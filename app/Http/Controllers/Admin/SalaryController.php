<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeSalary;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalaryController extends Controller
{
    public function index()
    {
        $month = request('month', now()->month);
        $year = request('year', now()->year);

        $salaries = EmployeeSalary::with(['user', 'createdBy', 'approvedBy', 'rejectedBy'])
            ->where('month', $month)
            ->where('year', $year)
            ->orderBy('status', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.salaries.index', compact('salaries', 'month', 'year'));
    }

    public function create()
    {
        $employees = User::where('role', '!=', 0)->get();
        $month = now()->month;
        $year = now()->year;

        return view('admin.salaries.create', compact('employees', 'month', 'year'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'base_salary' => 'required|numeric|min:0',
            'bonus' => 'required|numeric|min:0',
            'deduction' => 'required|numeric|min:0',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $exists = EmployeeSalary::where('user_id', $validated['user_id'])
                ->where('month', $validated['month'])
                ->where('year', $validated['year'])
                ->exists();

            if ($exists) {
                return back()->withInput()->with('error', 'Lương cho nhân viên này trong kỳ này đã tồn tại');
            }

            $validated['status'] = 'pending';
            $validated['created_by'] = auth()->id();

            EmployeeSalary::create($validated);

            DB::commit();

            return redirect()->route('admin.salaries.index')
                ->with('success', 'Tạo lương thành công. Trạng thái: Chờ duyệt');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Lỗi tạo lương: ' . $e->getMessage());
        }
    }

    public function edit(EmployeeSalary $salary)
    {
        if (!$salary->canBeEdited()) {
            return back()->with('error', 'Chỉ có thể sửa lương ở trạng thái chờ duyệt');
        }

        $employees = User::where('role', '!=', 0)->get();
        return view('admin.salaries.edit', compact('salary', 'employees'));
    }

    public function update(Request $request, EmployeeSalary $salary)
    {
        if (!$salary->canBeEdited()) {
            return back()->with('error', 'Chỉ có thể sửa lương ở trạng thái chờ duyệt');
        }

        $validated = $request->validate([
            'base_salary' => 'required|numeric|min:0',
            'bonus' => 'required|numeric|min:0',
            'deduction' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();
            $salary->update($validated);
            DB::commit();

            return redirect()->route('admin.salaries.index')
                ->with('success', 'Cập nhật lương thành công');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Lỗi cập nhật: ' . $e->getMessage());
        }
    }

    public function destroy(EmployeeSalary $salary)
    {
        if (!$salary->canBeDeleted()) {
            return back()->with('error', 'Chỉ có thể xóa lương ở trạng thái chờ duyệt');
        }

        try {
            $employeeName = $salary->user->name;
            $salary->delete();

            return redirect()->route('admin.salaries.index')
                ->with('success', "Xóa lương của {$employeeName} thành công");

        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi xóa lương: ' . $e->getMessage());
        }
    }

    public function generateByRole()
    {
        $roleSalaries = DB::table('role_salaries')->get()->keyBy('role');
        
        $roles = [
            ['id' => 1, 'name' => 'Admin', 'salary' => $roleSalaries->get(1)?->base_salary ?? 0],
            ['id' => 2, 'name' => 'Staff', 'salary' => $roleSalaries->get(2)?->base_salary ?? 0],
            ['id' => 3, 'name' => 'Warehouse Manager', 'salary' => $roleSalaries->get(3)?->base_salary ?? 0]
        ];
        
        $month = now()->month;
        $year = now()->year;
        
        return view('admin.salaries.generate-by-role', compact('roles', 'month', 'year'));
    }

    public function storeGenerateByRole(Request $request)
    {
        $validated = $request->validate([
            'role' => 'required|integer|in:1,2,3',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020',
        ]);

        try {
            DB::beginTransaction();

            $roleSalary = DB::table('role_salaries')->where('role', $validated['role'])->first();
            
            if (!$roleSalary) {
                return back()->withErrors(['role' => 'Chưa cấu hình lương cho role này']);
            }
            
            $employees = User::where('role', $validated['role'])->get();
            
            if ($employees->isEmpty()) {
                return back()->with('warning', 'Không có nhân viên nào thuộc role này');
            }
            
            $created = 0;
            $updated = 0;
            
            foreach ($employees as $employee) {
                $salary = EmployeeSalary::updateOrCreate(
                    [
                        'user_id' => $employee->id,
                        'month' => $validated['month'],
                        'year' => $validated['year']
                    ],
                    [
                        'base_salary' => $roleSalary->base_salary,
                        'bonus' => 0,
                        'deduction' => 0,
                        'status' => 'pending',
                        'created_by' => auth()->id(),
                        'notes' => 'Tạo tự động từ role salary'
                    ]
                );
                
                if ($salary->wasRecentlyCreated) {
                    $created++;
                } else {
                    $updated++;
                }
            }

            DB::commit();

            return redirect()->route('admin.salaries.index')
                ->with('success', "Tạo lương tự động thành công: {$created} mới, {$updated} cập nhật");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function approve($id)
    {
        try {
            DB::beginTransaction();

            $salary = EmployeeSalary::findOrFail($id);
            
            if (!$salary->canBeApproved()) {
                return back()->with('error', 'Chỉ có thể phê duyệt lương ở trạng thái pending. Trạng thái hiện tại: ' . $salary->status);
            }
            
            $salary->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            Log::info('Salary approved', [
                'salary_id' => $salary->id,
                'employee' => $salary->user->name,
                'amount' => $salary->getTotalSalary(),
                'approved_by' => auth()->id()
            ]);

            DB::commit();
            
            return redirect()->route('admin.salaries.index')
                ->with('success', "Phê duyệt lương cho {$salary->user->name} thành công. Trạng thái mới: approved");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi approve: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|min:10|max:500',
        ], [
            'rejection_reason.required' => 'Vui lòng nhập lý do từ chối',
            'rejection_reason.min' => 'Lý do từ chối phải có ít nhất 10 ký tự',
            'rejection_reason.max' => 'Lý do từ chối không được quá 500 ký tự',
        ]);

        try {
            DB::beginTransaction();

            $salary = EmployeeSalary::findOrFail($id);
            
            if (!$salary->canBeRejected()) {
                return back()->with('error', 'Chỉ có thể từ chối lương ở trạng thái pending. Trạng thái hiện tại: ' . $salary->status);
            }

            $salary->update([
                'status' => 'rejected',
                'rejection_reason' => $validated['rejection_reason'],
                'rejected_by' => auth()->id(),
                'rejected_at' => now(),
            ]);

            Log::info('Salary rejected', [
                'salary_id' => $salary->id,
                'employee' => $salary->user->name,
                'reason' => $validated['rejection_reason'],
                'rejected_by' => auth()->id()
            ]);

            DB::commit();

            return redirect()->route('admin.salaries.index')
                ->with('success', "Từ chối lương cho {$salary->user->name} thành công. Trạng thái mới: rejected");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi reject: ' . $e->getMessage());
        }
    }

    public function history()
    {
        $salaries = EmployeeSalary::with(['user', 'createdBy', 'approvedBy', 'rejectedBy'])
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->paginate(20);

        return view('admin.salaries.history', compact('salaries'));
    }

    // Role Salaries Management
    public function roleSalariesIndex()
    {
        $roleSalaries = DB::table('role_salaries')->get();
        return view('admin.role-salaries.index', compact('roleSalaries'));
    }

    public function roleSalariesCreate()
    {
        $roles = [
            ['id' => 1, 'name' => 'Admin'],
            ['id' => 2, 'name' => 'Staff'],
            ['id' => 3, 'name' => 'Warehouse Manager']
        ];

        return view('admin.role-salaries.create', compact('roles'));
    }

    public function roleSalariesStore(Request $request)
    {
        $request->validate([
            'role' => 'required|integer|in:1,2,3|unique:role_salaries,role',
            'base_salary' => 'required|integer|min:0',
        ]);

        DB::table('role_salaries')->insert([
            'role' => $request->role,
            'base_salary' => $request->base_salary,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.role-salaries.index')
            ->with('success', 'Thêm lương role thành công');
    }

    public function roleSalariesEdit($id)
    {
        $roleSalary = DB::table('role_salaries')->where('id', $id)->first();
        $roles = [
            ['id' => 1, 'name' => 'Admin'],
            ['id' => 2, 'name' => 'Staff'],
            ['id' => 3, 'name' => 'Warehouse Manager']
        ];

        return view('admin.role-salaries.edit', compact('roleSalary', 'roles'));
    }

    public function roleSalariesUpdate(Request $request, $id)
    {
        $request->validate([
            'base_salary' => 'required|integer|min:0',
        ]);

        DB::table('role_salaries')->where('id', $id)->update([
            'base_salary' => $request->base_salary,
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.role-salaries.index')
            ->with('success', 'Cập nhật lương role thành công');
    }

    public function roleSalariesDestroy($id)
    {
        DB::table('role_salaries')->where('id', $id)->delete();

        return redirect()->route('admin.role-salaries.index')
            ->with('success', 'Xóa lương role thành công');
    }
}