<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SalaryService;
use Illuminate\Support\Facades\Log;

class GenerateMonthlySalary extends Command
{
    protected $signature = 'salary:generate-monthly 
                           {--role= : Specific role to generate (1,2,3)}
                           {--month= : Month to generate (default: current)}
                           {--year= : Year to generate (default: current)}
                           {--dry-run : Show what would be generated without actually creating}';

    protected $description = 'Generate monthly salary for employees by role';

    private $salaryService;

    public function __construct(SalaryService $salaryService)
    {
        parent::__construct();
        $this->salaryService = $salaryService;
    }

    public function handle()
    {
        $role = $this->option('role');
        $month = $this->option('month') ?: now()->month;
        $year = $this->option('year') ?: now()->year;
        $dryRun = $this->option('dry-run');

        $roles = $role ? [$role] : [1, 2, 3];
        $roleNames = [1 => 'Admin', 2 => 'Staff', 3 => 'Warehouse Manager'];

        $this->info("=== Tạo lương tháng {$month}/{$year} ===");
        
        if ($dryRun) {
            $this->warn('DRY RUN MODE - Không tạo dữ liệu thực tế');
        }

        foreach ($roles as $roleId) {
            $this->info("\n--- Role: {$roleNames[$roleId]} ---");
            
            try {
                $status = $this->salaryService->getGenerationStatus($roleId, $month, $year);
                
                if (!$status['can_generate']) {
                    if ($status['is_locked']) {
                        $this->warn("Role {$roleNames[$roleId]}: Đang được xử lý bởi tiến trình khác");
                    } else {
                        $this->warn("Role {$roleNames[$roleId]}: Đã tồn tại {$status['existing_count']} bản ghi lương");
                    }
                    continue;
                }
                
                if ($dryRun) {
                    $this->line("Sẽ tạo lương cho role {$roleNames[$roleId]}");
                    continue;
                }
                
                $result = $this->salaryService->generateSalaryByRole($roleId, $month, $year);
                
                $this->info("✓ Tạo thành công {$result['count']} bản ghi lương");
                $this->line("  Lương cơ bản: " . number_format($result['base_salary']) . " VND");
                
            } catch (\Exception $e) {
                $this->error("✗ Lỗi role {$roleNames[$roleId]}: " . $e->getMessage());
                Log::error("Salary generation failed", [
                    'role' => $roleId,
                    'month' => $month,
                    'year' => $year,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->info("\n=== Hoàn thành ===");
        return 0;
    }
}