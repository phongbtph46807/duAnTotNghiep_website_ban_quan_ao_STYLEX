<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserLoyalty;
use App\Models\LoyaltyTier;
use Illuminate\Support\Facades\DB;

class LoyaltyService
{
    /**
     * Khởi tạo hoặc cập nhật hạng thành viên cho user
     */
    public function initializeUserLoyalty(User $user): UserLoyalty
    {
        $userLoyalty = UserLoyalty::firstOrCreate(
            ['user_id' => $user->id],
            [
                'loyalty_tier_id' => $this->getDefaultTierId(),
                'total_spent' => 0
            ]
        );

        return $userLoyalty;
    }

    /**
     * Lấy ID của hạng mặc định (Đồng)
     */
    protected function getDefaultTierId(): int
    {
        // Tìm tier có min_spend_required = 0 (Đồng)
        $defaultTier = LoyaltyTier::where('min_spend_required', 0)->first();
        
        if ($defaultTier) {
            return $defaultTier->id;
        }

        // Nếu không tìm thấy, tìm tier đầu tiên
        $firstTier = LoyaltyTier::orderBy('min_spend_required')->first();
        
        if ($firstTier) {
            return $firstTier->id;
        }

        // Nếu vẫn không có, tạo tier Đồng mặc định
        $defaultTier = LoyaltyTier::create([
            'name' => 'Đồng',
            'min_spend_required' => 0,
            'discount_rate' => 0.0,
        ]);

        return $defaultTier->id;
    }

    /**
     * Cập nhật tổng chi tiêu và nâng cấp hạng thành viên
     */
    public function updateUserSpending(User $user, float $amount): void
    {
        DB::transaction(function () use ($user, $amount) {
            // Khởi tạo nếu chưa có
            $userLoyalty = $this->initializeUserLoyalty($user);

            // Cập nhật tổng chi tiêu
            $userLoyalty->total_spent += $amount;
            $userLoyalty->save();

            // Kiểm tra và nâng cấp hạng nếu đủ điều kiện
            $newTier = $this->calculateTierForSpending($userLoyalty->total_spent);
            
            if ($newTier && $newTier->id !== $userLoyalty->loyalty_tier_id) {
                $userLoyalty->loyalty_tier_id = $newTier->id;
                $userLoyalty->save();
            }
        });
    }

    /**
     * Tính toán hạng thành viên dựa trên tổng chi tiêu
     */
    public function calculateTierForSpending(float $totalSpent): ?LoyaltyTier
    {
        // Lấy hạng cao nhất mà user đủ điều kiện
        return LoyaltyTier::where('min_spend_required', '<=', $totalSpent)
            ->orderByDesc('min_spend_required')
            ->first();
    }

    /**
     * Lấy hạng thành viên hiện tại của user
     */
    public function getCurrentTier(User $user): ?LoyaltyTier
    {
        $userLoyalty = $this->initializeUserLoyalty($user);
        return $userLoyalty->loyaltyTier;
    }

    /**
     * Tính toán số tiền cần chi tiêu thêm để lên hạng tiếp theo
     */
    public function getNextTierProgress(User $user): ?array
    {
        $userLoyalty = $this->initializeUserLoyalty($user);
        $currentTier = $userLoyalty->loyaltyTier;
        $totalSpent = $userLoyalty->total_spent;

        // Nếu không có current tier, lấy tier mặc định
        if (!$currentTier) {
            $currentTier = $this->calculateTierForSpending($totalSpent);
            if (!$currentTier) {
                // Nếu vẫn không có, lấy tier đầu tiên (Đồng)
                $currentTier = LoyaltyTier::orderBy('min_spend_required')->first();
            }
            // Cập nhật lại userLoyalty với tier đúng
            if ($currentTier) {
                $userLoyalty->loyalty_tier_id = $currentTier->id;
                $userLoyalty->save();
            }
        }

        // Nếu vẫn không có current tier, return null
        if (!$currentTier) {
            return null;
        }

        // Tìm hạng tiếp theo
        $nextTier = LoyaltyTier::where('min_spend_required', '>', $totalSpent)
            ->orderBy('min_spend_required')
            ->first();

        if (!$nextTier) {
            return null; // Đã ở hạng cao nhất
        }

        $remaining = $nextTier->min_spend_required - $totalSpent;
        $progress = $currentTier->min_spend_required > 0 
            ? (($totalSpent - $currentTier->min_spend_required) / ($nextTier->min_spend_required - $currentTier->min_spend_required)) * 100
            : ($totalSpent / $nextTier->min_spend_required) * 100;

        return [
            'next_tier' => $nextTier,
            'remaining' => max(0, $remaining),
            'progress' => min(100, max(0, $progress))
        ];
    }

    /**
     * Tính toán giảm giá dựa trên hạng thành viên
     */
    public function calculateDiscount(User $user, float $subtotal): float
    {
        $tier = $this->getCurrentTier($user);
        if (!$tier || $tier->discount_rate <= 0) {
            return 0;
        }

        return round($subtotal * ($tier->discount_rate / 100), 0);
    }
}

