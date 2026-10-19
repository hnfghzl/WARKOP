<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => 'nullable|string|max:255',
        ]);

        $transactions = Transaction::with('items')
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('invoice_number', 'like', '%'.$search.'%')
                        ->orWhere('customer_name', 'like', '%'.$search.'%');
                });
            })
            ->latest()
            ->get();

        return view('transactions', compact('transactions', 'filters'));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load('items');

        return view('transaction-detail', compact('transaction'));
    }

    public function success(Transaction $transaction)
    {
        return view('transaction-success', compact('transaction'));
    }

    public function receipt(Transaction $transaction)
    {
        $transaction->load('items');

        return view('receipt', compact('transaction'));
    }

    public function reports(Request $request)
    {
        $period = $request->query('period');
        $filters = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if (empty($filters['start_date']) && empty($filters['end_date']) && ! empty($period)) {
            if ($period === 'today') {
                $filters['start_date'] = now()->toDateString();
                $filters['end_date'] = now()->toDateString();
            } elseif ($period === 'week') {
                $filters['start_date'] = now()->subDays(6)->toDateString();
                $filters['end_date'] = now()->toDateString();
            } elseif ($period === 'month') {
                $filters['start_date'] = now()->startOfMonth()->toDateString();
                $filters['end_date'] = now()->toDateString();
            } elseif ($period === 'last_month') {
                $filters['start_date'] = now()->subMonth()->startOfMonth()->toDateString();
                $filters['end_date'] = now()->subMonth()->endOfMonth()->toDateString();
            } elseif ($period === 'year') {
                $filters['start_date'] = now()->startOfYear()->toDateString();
                $filters['end_date'] = now()->toDateString();
            }
        }

        $reportQuery = Transaction::query();

        if (! empty($filters['start_date'])) {
            $reportQuery->whereDate('created_at', '>=', $filters['start_date']);
        }

        if (! empty($filters['end_date'])) {
            $reportQuery->whereDate('created_at', '<=', $filters['end_date']);
        }

        $reportTotal = (clone $reportQuery)->sum('total');
        $transactionCount = (clone $reportQuery)->count();
        $totalDiscount = (clone $reportQuery)->sum('discount');
        $totalTax = (clone $reportQuery)->sum('tax');
        $recentTransactions = (clone $reportQuery)->with('items')->latest()->take(10)->get();

        $dailyBreakdown = (clone $reportQuery)->select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(total) as total')
        )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'asc')
            ->get();

        $maxDailyTotal = $dailyBreakdown->max('total') ?: 1;

        $paymentBreakdown = (clone $reportQuery)->select(
            'payment_method',
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(total) as total')
        )
            ->groupBy('payment_method')
            ->orderByDesc('total')
            ->get();

        $topProducts = TransactionItem::whereIn('transaction_id', (clone $reportQuery)->select('id'))
            ->select(
                'product_name',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(subtotal) as total_amount')
            )
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        $totalItemsSold = (int) TransactionItem::whereIn('transaction_id', (clone $reportQuery)->select('id'))->sum('quantity');

        return view('reports', compact(
            'filters',
            'period',
            'reportTotal',
            'transactionCount',
            'totalDiscount',
            'totalTax',
            'totalItemsSold',
            'recentTransactions',
            'dailyBreakdown',
            'maxDailyTotal',
            'paymentBreakdown',
            'topProducts'
        ));
    }
}
