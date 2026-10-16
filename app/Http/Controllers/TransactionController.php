<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
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
        $filters = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $reportQuery = Transaction::query();

        if (! empty($filters['start_date'])) {
            $reportQuery->whereDate('created_at', '>=', $filters['start_date']);
        }

        if (! empty($filters['end_date'])) {
            $reportQuery->whereDate('created_at', '<=', $filters['end_date']);
        }

        $reportTotal = (clone $reportQuery)->sum('total');
        $transactionCount = (clone $reportQuery)->count();
        $recentTransactions = (clone $reportQuery)->with('items')->latest()->take(10)->get();

        $dailyBreakdown = (clone $reportQuery)->select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(total) as total')
        )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'asc')
            ->get();

        return view('reports', compact(
            'filters',
            'reportTotal',
            'transactionCount',
            'recentTransactions',
            'dailyBreakdown'
        ));
    }
}
