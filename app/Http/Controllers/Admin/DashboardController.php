<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'from' => 'nullable|date',
            'to'   => 'nullable|date|after_or_equal:from',
        ]);

        $from = Carbon::parse($request->input('from', today()))->startOfDay();
        $to   = Carbon::parse($request->input('to', today()))->endOfDay();

        // Transaksi batal tidak dihitung di laporan
        $orders = Order::selesai()->whereBetween('created_at', [$from, $to]);

        $omzet = (clone $orders)->sum('total');
        $trx   = (clone $orders)->count();
        $tunai = (clone $orders)->where('payment_method', 'tunai')->sum('total');
        $qris  = (clone $orders)->where('payment_method', 'qris')->sum('total');

        $porsi = OrderItem::join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'selesai')
            ->whereBetween('orders.created_at', [$from, $to])
            ->sum('order_items.qty');

        // Omzet per hari
        $harian = Order::selesai()
            ->selectRaw("DATE(created_at) as tgl, COUNT(*) as trx, SUM(total) as omzet,
                SUM(CASE WHEN payment_method = 'tunai' THEN total ELSE 0 END) as tunai,
                SUM(CASE WHEN payment_method = 'qris' THEN total ELSE 0 END) as qris")
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('tgl')
            ->orderBy('tgl')
            ->get();

        // Menu terlaris
        $terlaris = OrderItem::join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'selesai')
            ->whereBetween('orders.created_at', [$from, $to])
            ->selectRaw('order_items.product_name, SUM(order_items.qty) as porsi, SUM(order_items.price * order_items.qty) as omzet')
            ->groupBy('order_items.product_name')
            ->orderByDesc('porsi')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'from',
            'to',
            'omzet',
            'trx',
            'porsi',
            'tunai',
            'qris',
            'harian',
            'terlaris'
        ));
    }
}
