<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Reports\SalesReportService;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(private readonly SalesReportService $reports) {}

    public function index(): View
    {
        $filters = request()->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
        ]);

        $from = $filters['from'] ?? null;
        $to = $filters['to'] ?? null;

        return view('admin.reports.index', [
            'summary' => $this->reports->summary($from, $to),
            'daily' => $this->reports->daily($from, $to),
            'topProducts' => $this->reports->topProducts($from, $to),
            'from' => $from,
            'to' => $to,
        ]);
    }
}
