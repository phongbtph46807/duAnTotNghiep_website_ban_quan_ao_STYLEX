<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeSalary;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalaryController extends Controller
{
    public function index()
    {
        $month = request('month', now()->month);
        $year = request('year', now()->year);

        $salaries = EmployeeSalary::with('user')
            ->where('month', $month)
            ->where('year', $year)
            ->get();

        return view('admin.salaries.index', compact('salaries', 'month', 'year'));
    }

    public function create()
    {
        $employees = User::where('role_id', '!=', 1)->get();
        $month = now()->month;
        $year = now()->year;

        return view('admin.salaries.create', compact('employees', 'month', 'year'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'base_salary' => 'required|integer|min:0',
            'bonus' => 'required|integer|min:0',
            'deduction' => 'required|integer|min:0',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020',
            'notes' => 'nullable|string',
        ]);

        EmployeeSalary::create($request->all());

        return redirect()->route('admin.salaries.index')->with('success', 'Thêm lương thành công');
    }

    public function edit(EmployeeSalary $salary)
    {
        $employees = User::where('role_id', '!=', 1)->get();

        return view('admin.salaries.edit', compact('salary', 'employees'));
    }

    public function update(Request $request, EmployeeSalary $salary)
    {
        $request->validate([
            'base_salary' => 'required|integer|min:0',
            'bonus' => 'required|integer|min:0',
            'deduction' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $salary->update($request->only('base_salary', 'bonus', 'deduction', 'notes'));

        return redirect()->route('admin.salaries.index')->with('success', 'Cập nhật lương thành công');
    }

    public function destroy(EmployeeSalary $salary)
    {
        $salary->delete();

        return redirect()->route('admin.salaries.index')->with('success', 'Xóa lương thành công');
    }
}
