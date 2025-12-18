<?php

namespace App\Services;

use App\Models\EmployeeSalary;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalaryService
{
    /**
     * Tạo lương tự động theo role
     */
    public function generateSalariesByRole($role, $month, $year)
    {
        try {
            DB::beginTransaction();
            
            // Lấy thông tin lương role
            $roleSalary = DB::table('role_salaries')->where('role', $role)->first();
            
            if (!$roleSalary) {
                throw new \Exception('Chưa cấu hình lương cho role này');
            }
            
            // Lấy danh sách nhân viên theo role
            $employees = User::where('role', $role)->get();
            
            if ($employees->isEmpty()) {
                throw new \Exception('Không có nhân viên nào thuộc role này');
            }
            
            $created = 0;
            $updated = 0;
            $errors = [];
            
            foreach ($employees as $employee) {
                try {
                    $salary = EmployeeSalary::updateOrCreate(
                        [
                            'user_id' => $employee->id,
                            'month' => $month,
                            'year' => $year
                        ],
                        [
                            'base_salary' => $roleSalary->base_salary,
                            'bonus' => 0,
                            'deduction' => 0,
                            'status' => 'pending',
                            'created_by' => auth()->id(),
                            'notes' => "Tạo tự động từ role salary - " . now()->format('d/m/Y H:i')
                        ]
                    );
                    
                    if ($salary->wasRecentlyCreated) {
                        $created++;
                    } else {
                        $updated++;
                    }
                    
                } catch (\Exception $e) {
                    $errors[] = "Lỗi tạo lương cho {$employee->name}: " . $e->getMessage();
                }
            }
            
            DB::commit();
            
            // Log activity
            Log::info('Salary generation completed', [
                'role' => $role,
                'month' => $month,
                'year' => $year,
                'created' => $created,
                'updated' => $updated,
                'errors' => count($errors),
                'user_id' => auth()->id()
            ]);
            
            return [
                'success' => true,
                'created' => $created,
                'updated' => $updated,
                'errors' => $errors
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Salary generation failed', [
                'role' => $role,
                'month' => $month,
                'year' => $year,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Duyệt lương
     */
    public function approveSalary(EmployeeSalary $salary)
    {
        if (!$salary->canBeApproved()) {
            throw new \Exception('Chỉ có thể phê duyệt lương ở trạng thái chờ duyệt');
        }
        
        try {
            DB::beginTransaction();
            
            $salary->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
            
            // Log activity
            Log::info('Salary approved', [
                'salary_id' => $salary->id,
                'employee' => $salary->user->name,
                'amount' => $salary->getTotalSalary(),
                'approved_by' => auth()->id()
            ]);
            
            DB::commit();
            
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Salary approval failed', [
                'salary_id' => $salary->id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Từ chối lương
     */
    public function rejectSalary(EmployeeSalary $salary, string $reason)
    {
        if (!$salary->canBeRejected()) {
            throw new \Exception('Chỉ có thể từ chối lương ở trạng thái chờ duyệt');
        }
        
        if (strlen(trim($reason)) < 10) {
            throw new \Exception('Lý do từ chối phải có ít nhất 10 ký tự');
        }
        
        try {
            DB::beginTransaction();
            
            $salary->update([
                'status' => 'rejected',
                'rejection_reason' => trim($reason),
                'rejected_by' => auth()->id(),
                'rejected_at' => now(),
            ]);
            
            // Log activity
            Log::info('Salary rejected', [
                'salary_id' => $salary->id,
                'employee' => $salary->user->name,
                'amount' => $salary->getTotalSalary(),
                'reason' => $reason,
                'rejected_by' => auth()->id()
            ]);
            
            DB::commit();
            
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Salary rejection failed', [
                'salary_id' => $salary->id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Lấy thống kê lương theo tháng
     */
    public function getSalaryStatistics($month, $year)
    {
        return [
            'total_salaries' => EmployeeSalary::byPeriod($month, $year)->count(),
            'pending_count' => EmployeeSalary::byPeriod($month, $year)->where('status', 'pending')->count(),
            'approved_count' => EmployeeSalary::byPeriod($month, $year)->where('status', 'approved')->count(),
            'rejected_count' => EmployeeSalary::byPeriod($month, $year)->where('status', 'rejected')->count(),
            'total_amount' => EmployeeSalary::byPeriod($month, $year)
                ->where('status', 'approved')
                ->get()
                ->sum(function($salary) {
                    return $salary->getTotalSalary();
                }),
        ];
    }
}