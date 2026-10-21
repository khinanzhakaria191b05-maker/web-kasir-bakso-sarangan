<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'order_type'           => 'required|in:dine_in,takeaway',
            'table_number'         => 'required_if:order_type,dine_in|nullable|string|max:20',
            'note'                 => 'nullable|string|max:500',
            'payment_method'       => 'required|in:tunai,qris',
            'cash_received'        => 'nullable|integer|min:0',
            'items'                => 'required|array|min:1',
            'items.*.product_id'   => 'required|exists:products,id',
            'items.*.qty'          => 'required|integer|min:1|max:99',
            'items.*.option_ids'   => 'nullable|array',
            'items.*.option_ids.*' => 'integer',
        ]);

        $order = DB::transaction(function () use ($data, $request) {
            $total = 0;
            $rows = [];

            foreach ($data['items'] as $row) {
                $product = Product::with('options')->findOrFail($row['product_id']);

                if (!$product->is_available) {
                    abort(422, "Menu {$product->name} sedang habis.");
                }

                // Hanya opsi yang memang tertaut ke menu ini yang diterima
                $opts = $product->options->whereIn('id', $row['option_ids'] ?? [])->values();
                $unit = $product->price + $opts->sum('price');
                $total += $unit * $row['qty'];

                $rows[] = [
                    'product_id'   => $product->id,
                    'product_name' => $product->name,
                    'price'        => $unit,
                    'qty'          => $row['qty'],
                    'options'      => $opts->map(fn($o) => ['name' => $o->name, 'price' => $o->price])->all(),
                ];
            }

            $cash = null;
            $change = null;
            if ($data['payment_method'] === 'tunai') {
                $cash = $data['cash_received'] ?? 0;
                if ($cash < $total) {
                    abort(422, 'Uang diterima kurang dari total.');
                }
                $change = $cash - $total;
            }

            // Nomor invoice: INV + tanggal + urutan harian
            $seq = Order::whereDate('created_at', today())->lockForUpdate()->count() + 1;
            $invoice = 'INV' . now()->format('ymd') . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);

            $order = Order::create([
                'invoice_no'     => $invoice,
                'user_id'        => $request->user()->id,
                'order_type'     => $data['order_type'],
                'table_number'   => $data['order_type'] === 'dine_in' ? $data['table_number'] : null,
                'note'           => $data['note'] ?? null,
                'total'          => $total,
                'payment_method' => $data['payment_method'],
                'cash_received'  => $cash,
                'change_amount'  => $change,
            ]);

            $order->items()->createMany($rows);

            return $order;
        });

        return response()->json([
            'id'         => $order->id,
            'invoice_no' => $order->invoice_no,
            'total'      => $order->total,
            'change'     => $order->change_amount,
            'struk_url'  => route('kasir.struk', $order),
        ]);
    }

    public function struk(Request $request, Order $order)
    {
        // Kasir hanya boleh melihat struk miliknya sendiri
        abort_unless(
            $request->user()->role === 'admin' || $order->user_id === $request->user()->id,
            403
        );

        $order->load('items', 'user');

        return view('kasir.struk', compact('order'));
    }
}
