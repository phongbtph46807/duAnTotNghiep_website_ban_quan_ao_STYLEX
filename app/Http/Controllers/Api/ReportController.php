<?php

// app/Http/Controllers/Api/ReportController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdminReport;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $reports = AdminReport::query()
            ->when($request->from && $request->to, function ($q) use ($request) {
                $q->whereBetween('report_date', [$request->from, $request->to]);
            })
            ->orderByDesc('report_date')
            ->get();

        return response()->json(['status' => true, 'data' => $reports]);
    }
}
