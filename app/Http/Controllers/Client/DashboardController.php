<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Company;
use App\Models\Kilometer;
use App\Models\Salary;
use App\Enums\InvoiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the dashboard
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        // Base queries based on user role
        $invoiceQuery = $this->getInvoiceQueryForUser($user);
        $kilometerQuery = $this->getKilometerQueryForUser($user);
        $salaryQuery = $this->getSalaryQueryForUser($user);

        // Current year statistics
        $yearStats = [
            'total_invoices' => (clone $invoiceQuery)->inYear($currentYear)->count(),
            'total_expenses' => (clone $invoiceQuery)->inYear($currentYear)->ofType(InvoiceType::EXPENSE)->sum('total'),
            'total_sales' => (clone $invoiceQuery)->inYear($currentYear)->ofType(InvoiceType::SALE)->sum('total'),
            'total_kilometers' => (clone $kilometerQuery)->inYear($currentYear)->sum('kilometers'),
            'total_salaries' => (clone $salaryQuery)->inYear($currentYear)->count(),
        ];

        // Current month statistics
        $monthStats = [
            'invoices' => (clone $invoiceQuery)->inYear($currentYear)->inMonth($currentMonth)->count(),
            'expenses' => (clone $invoiceQuery)->inYear($currentYear)->inMonth($currentMonth)->ofType(InvoiceType::EXPENSE)->sum('total'),
            'sales' => (clone $invoiceQuery)->inYear($currentYear)->inMonth($currentMonth)->ofType(InvoiceType::SALE)->sum('total'),
            'kilometers' => (clone $kilometerQuery)->inYear($currentYear)->inMonth($currentMonth)->sum('kilometers'),
        ];

        // Monthly data for charts (last 12 months)
        $monthlyData = $this->getMonthlyChartData($invoiceQuery, $currentYear);

        // Recent invoices
        $recentInvoices = (clone $invoiceQuery)
            ->with(['company', 'categoryData', 'creator'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Expense categories summary
        $expensesByCategory = (clone $invoiceQuery)
            ->inYear($currentYear)
            ->ofType(InvoiceType::EXPENSE)
            ->with('categoryData')
            ->get()
            ->groupBy('category.name')
            ->map(function ($invoices) {
                return [
                    'count' => $invoices->count(),
                    'total' => $invoices->sum('total'),
                ];
            })
            ->sortByDesc('total')
            ->take(10);

        // Top companies by invoices
        $topCompanies = (clone $invoiceQuery)
            ->inYear($currentYear)
            ->with('company')
            ->get()
            ->groupBy('company.name')
            ->map(function ($invoices) {
                return [
                    'count' => $invoices->count(),
                    'total' => $invoices->sum('total'),
                ];
            })
            ->sortByDesc('count')
            ->take(5);

        return view('client.dashboard.index', compact(
            'yearStats',
            'monthStats',
            'monthlyData',
            'recentInvoices',
            'expensesByCategory',
            'topCompanies',
            'currentYear',
            'currentMonth'
        ));
    }

    /**
     * Get invoice query based on user role
     */
    private function getInvoiceQueryForUser($user)
    {
        $query = Invoice::query();

        // If user is not admin or accountant, only show their own invoices
        if (!$user->canSeeAllData()) {
            $query->byCreator($user->id);
        }

        return $query;
    }

    /**
     * Get kilometer query based on user role
     */
    private function getKilometerQueryForUser($user)
    {
        $query = Kilometer::query();

        // If user is not admin or accountant, only show their own records
        if (!$user->canSeeAllData()) {
            $query->byCreator($user->id);
        }

        return $query;
    }

    /**
     * Get salary query based on user role
     */
    private function getSalaryQueryForUser($user)
    {
        $query = Salary::query();

        // If user is not admin or accountant, only show their own records
        if (!$user->canSeeAllData()) {
            $query->byCreator($user->id);
        }

        return $query;
    }

    /**
     * Get monthly chart data for the last 12 months
     */
    private function getMonthlyChartData($invoiceQuery, $currentYear)
    {
        $monthlyData = [];

        for ($month = 1; $month <= 12; $month++) {
            $expenses = (clone $invoiceQuery)
                ->inYear($currentYear)
                ->inMonth($month)
                ->ofType(InvoiceType::EXPENSE)
                ->sum('total');

            $sales = (clone $invoiceQuery)
                ->inYear($currentYear)
                ->inMonth($month)
                ->ofType(InvoiceType::SALE)
                ->sum('total');

            $monthlyData[] = [
                'month' => Carbon::create($currentYear, $month, 1)->format('M'),
                'expenses' => (float) $expenses,
                'sales' => (float) $sales,
            ];
        }

        return $monthlyData;
    }
}
