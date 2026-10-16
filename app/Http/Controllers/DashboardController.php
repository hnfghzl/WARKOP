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
        // Cek apakah ada transaksi pada bulan berjalan
        $hasThisMonth = Transaction::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->exists();

        // Default ke 'semua' jika bulan berjalan belum ada transaksi agar data langsung sinkron
        $defaultPeriod = $hasThisMonth ? 'bulan-ini' : 'semua';
        $period = $request->query('period', $defaultPeriod);

        // Pertahankan variabel asli untuk kompatibilitas contract/test
        $productsCount = Product::count();
        $transactionsToday = Transaction::whereDate('created_at', today())->count();
        $itemsSoldToday = (int) TransactionItem::whereDate('created_at', today())->sum('quantity');
        $salesToday = (float) Transaction::whereDate('created_at', today())->sum('total');
        $salesThisMonth = (float) Transaction::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        $totalTransactions = Transaction::count();
        $totalSales = (float) Transaction::sum('total');
        $averageTransaction = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;

        // Siapkan query sesuai filter periode
        $query = Transaction::query();
        $prevQuery = Transaction::query();
        $comparisonLabel = 'vs periode lalu';
        $periodLabel = 'Semua Waktu';

        if ($period === 'hari-ini') {
            $periodLabel = 'Hari Ini';
            $query->whereDate('created_at', today());
            $prevQuery->whereDate('created_at', today()->subDay());
            $comparisonLabel = 'vs kemarin';
        } elseif ($period === 'minggu-ini') {
            $periodLabel = 'Minggu Ini';
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            $prevQuery->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
            $comparisonLabel = 'vs minggu lalu';
        } elseif ($period === 'bulan-ini') {
            $periodLabel = 'Bulan Ini ('.now()->translatedFormat('F Y').')';
            $query->whereYear('created_at', now()->year)->whereMonth('created_at', now()->month);
            $prevQuery->whereYear('created_at', now()->subMonth()->year)->whereMonth('created_at', now()->subMonth()->month);
            $comparisonLabel = 'vs bulan lalu';
        } elseif ($period === 'bulan-lalu') {
            $periodLabel = 'Bulan Lalu ('.now()->subMonth()->translatedFormat('F Y').')';
            $query->whereYear('created_at', now()->subMonth()->year)->whereMonth('created_at', now()->subMonth()->month);
            $prevQuery->whereYear('created_at', now()->subMonths(2)->year)->whereMonth('created_at', now()->subMonths(2)->month);
            $comparisonLabel = 'vs 2 bulan lalu';
        } else {
            $period = 'semua';
            $periodLabel = 'Semua Waktu';
            $comparisonLabel = 'total akumulasi';
        }

        // Ambil transaksi periode yang dipilih dengan relasi items & product
        $periodTransactions = $query->with('items.product')->latest()->get();
        $periodRevenue = (float) $periodTransactions->sum('total');
        $periodOrdersCount = $periodTransactions->count();

        // Data pembanding untuk pertumbuhan (growth %)
        $prevRevenue = $period !== 'semua' ? (float) $prevQuery->sum('total') : 0;
        $prevOrdersCount = $period !== 'semua' ? $prevQuery->count() : 0;

        // Perhitungan pertumbuhan penjualan
        if ($period === 'semua') {
            $revenueGrowthText = '100 %';
            $revenueGrowthPositive = true;
        } elseif ($prevRevenue > 0) {
            $diff = $periodRevenue - $prevRevenue;
            $pct = round(($diff / $prevRevenue) * 100, 1);
            $revenueGrowthText = ($pct >= 0 ? '+' : '').number_format($pct, 1, ',', '.').' %';
            $revenueGrowthPositive = $pct >= 0;
        } elseif ($periodRevenue > 0) {
            $revenueGrowthText = '+100 %';
            $revenueGrowthPositive = true;
        } else {
            $revenueGrowthText = '0 %';
            $revenueGrowthPositive = true;
        }

        // Perhitungan pertumbuhan pesanan
        if ($period === 'semua') {
            $orderGrowthText = '100 %';
            $orderGrowthPositive = true;
        } elseif ($prevOrdersCount > 0) {
            $diffOrders = $periodOrdersCount - $prevOrdersCount;
            $pctOrders = round(($diffOrders / $prevOrdersCount) * 100, 1);
            $orderGrowthText = ($pctOrders >= 0 ? '+' : '').number_format($pctOrders, 1, ',', '.').' %';
            $orderGrowthPositive = $pctOrders >= 0;
        } elseif ($periodOrdersCount > 0) {
            $orderGrowthText = '+100 %';
            $orderGrowthPositive = true;
        } else {
            $orderGrowthText = '0 %';
            $orderGrowthPositive = true;
        }

        // Pelanggan unik dalam periode
        $customerCount = $periodTransactions->pluck('customer_name')->filter()->unique()->count();
        if ($customerCount === 0 && $periodOrdersCount > 0) {
            $customerCount = $periodOrdersCount;
        }

        // Rata-rata transaksi dalam periode
        $averageOrder = $periodOrdersCount > 0 ? $periodRevenue / $periodOrdersCount : 0;
        $prevAverageOrder = $prevOrdersCount > 0 ? $prevRevenue / $prevOrdersCount : 0;
        if ($period === 'semua') {
            $avgGrowthText = 'Stabil';
            $avgGrowthPositive = true;
        } elseif ($prevAverageOrder > 0) {
            $diffAvg = $averageOrder - $prevAverageOrder;
            $pctAvg = round(($diffAvg / $prevAverageOrder) * 100, 1);
            $avgGrowthText = ($pctAvg >= 0 ? '+' : '').number_format($pctAvg, 1, ',', '.').' %';
            $avgGrowthPositive = $pctAvg >= 0;
        } elseif ($averageOrder > 0) {
            $avgGrowthText = '+100 %';
            $avgGrowthPositive = true;
        } else {
            $avgGrowthText = '0 %';
            $avgGrowthPositive = true;
        }

        // 4 Kartu KPI Metrik Atas (100% Sinkron dengan Data Penjualan)
        $kpiData = [
            'revenue' => [
                'formatted' => 'Rp '.number_format($periodRevenue, 0, ',', '.'),
                'raw' => $periodRevenue,
                'growth' => ($revenueGrowthPositive ? '▲ ' : '▼ ').$revenueGrowthText,
                'growth_positive' => $revenueGrowthPositive,
                'comparison' => $comparisonLabel,
            ],
            'orders' => [
                'value' => $periodOrdersCount,
                'growth' => ($orderGrowthPositive ? '▲ ' : '▼ ').$orderGrowthText,
                'growth_positive' => $orderGrowthPositive,
                'comparison' => $comparisonLabel,
            ],
            'customers' => [
                'value' => $customerCount,
                'growth' => '▲ '.$customerCount.' Pelanggan',
                'growth_positive' => true,
                'comparison' => $comparisonLabel,
            ],
            'average_order' => [
                'formatted' => 'Rp '.number_format($averageOrder, 0, ',', '.'),
                'raw' => $averageOrder,
                'growth' => ($avgGrowthPositive ? '▲ ' : '▼ ').$avgGrowthText,
                'growth_positive' => $avgGrowthPositive,
                'comparison' => $comparisonLabel,
            ],
        ];

        // 7 Hari Grafik Batang (Senin s/d Minggu)
        $dayLabels = [
            1 => ['code' => 'SEN', 'name' => 'Senin'],
            2 => ['code' => 'SEL', 'name' => 'Selasa'],
            3 => ['code' => 'RAB', 'name' => 'Rabu'],
            4 => ['code' => 'KAM', 'name' => 'Kamis'],
            5 => ['code' => 'JUM', 'name' => 'Jumat'],
            6 => ['code' => 'SAB', 'name' => 'Sabtu'],
            7 => ['code' => 'MIN', 'name' => 'Minggu'],
        ];

        // Kelompokkan data transaksi berdasarkan hari dalam seminggu
        // Jika periode 'semua' atau 'bulan-ini', kelompokkan dari $periodTransactions
        $daySalesMap = [];
        foreach ($periodTransactions as $tx) {
            if ($tx->created_at) {
                $iso = $tx->created_at->dayOfWeekIso; // 1 = Senin, 7 = Minggu
                if (! isset($daySalesMap[$iso])) {
                    $daySalesMap[$iso] = ['amount' => 0, 'count' => 0];
                }
                $daySalesMap[$iso]['amount'] += (float) $tx->total;
                $daySalesMap[$iso]['count'] += 1;
            }
        }

        // Cari penjualan harian tertinggi untuk skala tinggi bar (persentase)
        $maxDailySales = 0;
        foreach ($dayLabels as $idx => $meta) {
            $amt = $daySalesMap[$idx]['amount'] ?? 0;
            if ($amt > $maxDailySales) {
                $maxDailySales = $amt;
            }
        }
        $chartScale = $maxDailySales > 0 ? $maxDailySales : 100000;

        $weeklyChart = [];
        foreach ($dayLabels as $idx => $meta) {
            $amt = $daySalesMap[$idx]['amount'] ?? 0;
            $cnt = $daySalesMap[$idx]['count'] ?? 0;
            $height = $amt > 0 ? max(12, round(($amt / $chartScale) * 100)) : 4;

            $weeklyChart[] = [
                'day' => $meta['code'],
                'name' => $meta['name'],
                'amount_raw' => $amt,
                'amount' => 'Rp '.number_format($amt, 0, ',', '.'),
                'height' => $height,
                'orders' => $cnt,
            ];
        }

        // Skala label sumbu Y dinamis
        $yScaleLabels = [
            'p100' => 'Rp '.number_format($chartScale, 0, ',', '.'),
            'p75' => 'Rp '.number_format(round($chartScale * 0.75), 0, ',', '.'),
            'p50' => 'Rp '.number_format(round($chartScale * 0.5), 0, ',', '.'),
            'p25' => 'Rp '.number_format(round($chartScale * 0.25), 0, ',', '.'),
            'p0' => 'Rp 0',
        ];

        // Hitung Penjualan per Kategori Secara Real dari TransactionItem
        $categoryTotals = [];
        $totalCategoryRevenue = 0;

        foreach ($periodTransactions as $tx) {
            foreach ($tx->items as $item) {
                $catName = $item->product && $item->product->category ? trim($item->product->category) : 'Lainnya';
                $catName = ucfirst(strtolower($catName));

                if (! isset($categoryTotals[$catName])) {
                    $categoryTotals[$catName] = ['amount' => 0, 'qty' => 0];
                }
                $sub = (float) $item->subtotal;
                $categoryTotals[$catName]['amount'] += $sub;
                $categoryTotals[$catName]['qty'] += (int) $item->quantity;
                $totalCategoryRevenue += $sub;
            }
        }

        // Urutkan kategori berdasarkan total penjualan terbesar
        uasort($categoryTotals, function ($a, $b) {
            return $b['amount'] <=> $a['amount'];
        });

        // Warna tema modern untuk segmen donat
        $palette = ['#3B46F1', '#60A5FA', '#F97316', '#34D399', '#A855F7', '#EC4899'];
        $donutCategories = [];
        $currentOffset = 0;
        $circumference = 314.16; // 2 * PI * 50
        $colorIdx = 0;

        if ($totalCategoryRevenue > 0) {
            foreach ($categoryTotals as $name => $data) {
                $pct = round(($data['amount'] / $totalCategoryRevenue) * 100, 1);
                $dashLength = round(($pct / 100) * $circumference, 2);
                $color = $palette[$colorIdx % count($palette)];

                $donutCategories[] = [
                    'name' => $name,
                    'percentage' => $pct,
                    'amount' => 'Rp '.number_format($data['amount'], 0, ',', '.'),
                    'qty' => $data['qty'],
                    'color' => $color,
                    'dasharray' => $dashLength.' '.$circumference,
                    'dashoffset' => -$currentOffset,
                ];

                $currentOffset += $dashLength;
                $colorIdx++;
            }
        } else {
            // Jika belum ada penjualan pada periode ini, siapkan kategori dari katalog produk
            $allCategories = Product::select('category')->distinct()->pluck('category')->filter();
            foreach ($allCategories as $catName) {
                $color = $palette[$colorIdx % count($palette)];
                $donutCategories[] = [
                    'name' => ucfirst(strtolower($catName)),
                    'percentage' => 0,
                    'amount' => 'Rp 0',
                    'qty' => 0,
                    'color' => $color,
                    'dasharray' => '0 '.$circumference,
                    'dashoffset' => 0,
                ];
                $colorIdx++;
            }
        }

        // 3 Kartu Metrik Bawah (Real Data)
        $completedTransactionsCount = $periodTransactions->where('status', 'completed')->count();
        $totalItemsSoldInPeriod = (int) $periodTransactions->flatMap->items->sum('quantity');
        $cashTransactionsCount = $periodTransactions->where('payment_method', 'cash')->count();
        $cashRate = $periodOrdersCount > 0 ? round(($cashTransactionsCount / $periodOrdersCount) * 100) : 0;

        $bottomCards = [
            [
                'title' => 'Pesanan Selesai',
                'value' => $completedTransactionsCount,
                'trend' => $periodOrdersCount > 0 ? round(($completedTransactionsCount / $periodOrdersCount) * 100).'% Selesai' : '0%',
                'trend_label' => 'dari '.$periodOrdersCount.' pesanan',
                'color' => 'blue',
            ],
            [
                'title' => 'Produk Terjual',
                'value' => $totalItemsSoldInPeriod.' item',
                'trend' => $periodOrdersCount > 0 ? 'Rata-rata '.round($totalItemsSoldInPeriod / $periodOrdersCount, 1).' item' : '0 item',
                'trend_label' => 'per transaksi',
                'color' => 'emerald',
            ],
            [
                'title' => 'Metode Pembayaran',
                'value' => $cashRate.'% Tunai',
                'trend' => $cashTransactionsCount.' Tunai',
                'trend_label' => ($periodOrdersCount - $cashTransactionsCount).' Non-Tunai',
                'color' => 'amber',
            ],
        ];

        // Tabel transaksi terkini: jika ada transaksi pada periode, tampilkan transaksi periode tersebut
        // Jika 0, ambil transaksi terkini dari database
        $recentTransactions = $periodTransactions->isNotEmpty()
            ? $periodTransactions->take(5)
            : Transaction::with('items')->latest()->take(5)->get();

        return view('dashboard', compact(
            'productsCount',
            'transactionsToday',
            'itemsSoldToday',
            'salesToday',
            'salesThisMonth',
            'totalTransactions',
            'totalSales',
            'averageTransaction',
            'kpiData',
            'weeklyChart',
            'donutCategories',
            'bottomCards',
            'recentTransactions',
            'period',
            'periodLabel',
            'yScaleLabels',
            'totalCategoryRevenue'
        ));
    }
}
