<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'from'    => 'nullable|date',
            'to'      => 'nullable|date|after_or_equal:from',
            'user_id' => 'nullable|exists:users,id',
            'method'  => 'nullable|in:tunai,qris',
        ]);

        $from = Carbon::parse($request->input('from', today()))->startOfDay();
        $to   = Carbon::parse($request->input('to', today()))->endOfDay();

        $query = Order::with('user')->withSum('items as porsi', 'qty')
            ->whereBetween('created_at', [$from, $to])
            ->when($request->user_id, fn($q, $v) => $q->where('user_id', $v))
            ->when($request->method, fn($q, $v) => $q->where('payment_method', $v));

        // Transaksi batal tidak ikut dijumlah
        $totalFiltered = (clone $query)->selesai()->sum('total');

        $orders = $query->latest()->paginate(20)->withQueryString();
        $kasirs = User::orderBy('name')->get(['id', 'name']);

        return view('admin.transactions.index', compact('orders', 'kasirs', 'from', 'to', 'totalFiltered'));
    }

    public function show(Order $order)
    {
        $order->load('items', 'user', 'voidedBy');

        return view('admin.transactions.show', compact('order'));
    }

    public function void(Request $request, Order $order)
    {
        $data = $request->validate([
            'void_reason' => 'required|string|max:200',
        ]);

        if ($order->status === 'batal') {
            return back()->with('error', 'Transaksi ini sudah dibatalkan.');
        }

        $order->update([
            'status'      => 'batal',
            'void_reason' => $data['void_reason'],
            'voided_by'   => $request->user()->id,
            'voided_at'   => now(),
        ]);

        return back()->with('success', 'Transaksi dibatalkan.');
    }
}
