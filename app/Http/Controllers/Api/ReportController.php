<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReportService;

class ReportController extends Controller
{
    public function __construct(private ReportService $reportService) {}

    public function sales()
    {
        return response()->json(
            $this->reportService->todaySales()
        );
    }

    public function stocks()
    {
        return response()->json(
            $this->reportService->stockReport()
        );
    }
}