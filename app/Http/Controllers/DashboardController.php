<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->query('period', 'hari-ini');

        // Statistik Operasional Hari Ini
        $salesToday = (float) Transaction::whereDate('created_at', today())->sum('total');
        $transactionsToday = Transaction::whereDate('created_at', today())->count();
        $itemsSoldToday = (int) TransactionItem::whereDate('created_at', today())->sum('quantity');

        // Total Katalog & Akumulasi
        $productsCount = Product::count();
        $totalTransactions = Transaction::count();
        $totalSales = (float) Transaction::sum('total');
        $averageTransaction = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;
        $salesThisMonth = (float) Transaction::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        // Dukungan query period untuk pengujian & fleksibilitas
        if ($period === 'semua') {
            $periodLabel = 'Semua Waktu';
            $periodRevenue = $totalSales;
            $periodOrdersCount = $totalTransactions;
            $periodItemsCount = (int) TransactionItem::sum('quantity');
        } else {
            $period = 'hari-ini';
            $periodLabel = 'Hari Ini';
            $periodRevenue = $salesToday;
            $periodOrdersCount = $transactionsToday;
            $periodItemsCount = $itemsSoldToday;
        }

        // Monitoring stok menipis (<= 5)
        $lowStockProducts = Product::where('stock', '<=', 5)->orderBy('stock', 'asc')->take(5)->get();
        $lowStockCount = Product::where('stock', '<=', 5)->count();

        // Kategori produk aktif di toko
        $categories = Product::select('category')
            ->selectRaw('count(*) as total')
            ->whereNotNull('category')
            ->groupBy('category')
            ->get();

        // Transaksi terkini (5 transaksi terakhir)
        $recentTransactions = Transaction::with('items')->latest()->take(5)->get();

        return view('dashboard', compact(
            'productsCount',
            'transactionsToday',
            'itemsSoldToday',
            'salesToday',
            'salesThisMonth',
            'totalTransactions',
            'totalSales',
            'averageTransaction',
            'period',
            'periodLabel',
            'periodRevenue',
            'periodOrdersCount',
            'periodItemsCount',
            'lowStockProducts',
            'lowStockCount',
            'categories',
            'recentTransactions'
        ));
    }
}
