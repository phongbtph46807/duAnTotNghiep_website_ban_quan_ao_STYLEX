<?php

namespace App\Jobs;

use App\Mail\InvoiceMail;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendOrderInvoiceMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $orderId;
    public string $toEmail;

    public function __construct(int $orderId, string $toEmail)
    {
        $this->orderId = $orderId;
        $this->toEmail = $toEmail;
    }

    public function handle(): void
    {
        $order = Order::with(['items.product', 'items.variant.size', 'items.variant.color', 'items.variant.texture'])->find($this->orderId);
        if (!$order) {
            return;
        }

        $items = [];
        foreach ($order->items as $it) {
            $variant = $it->variant;
            $variantLabelParts = [];
            if ($variant && $variant->size) { $variantLabelParts[] = $variant->size->name; }
            if ($variant && $variant->color) { $variantLabelParts[] = $variant->color->name; }
            if ($variant && $variant->texture) { $variantLabelParts[] = $variant->texture->name; }

            $items[] = [
                'product_name' => $it->product?->name ?? ('#' . (string) $it->product_id),
                'variant_label' => implode(' / ', array_filter($variantLabelParts)),
                'quantity' => (int) $it->quantity,
                'unit_price' => (int) $it->price,
                'line_total' => (int) $it->line_total,
            ];
        }

        $data = [
            'order_code' => $order->code,
            'full_name' => $order->full_name,
            'phone' => $order->phone,
            'email' => $order->email,
            'city' => $order->city,
            'address' => $order->address,
            'note' => $order->note,
            'subtotal' => (int) $order->subtotal,
            'shipping_fee' => (int) $order->shipping_fee,
            'discount' => (int) $order->discount,
            'total' => (int) $order->total,
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status,
            'status' => $order->status,
            'items' => $items,
            'placed_at' => optional($order->created_at)->format('d/m/Y H:i'),
        ];

        if (!empty($this->toEmail)) {
            Mail::to($this->toEmail)->send(new InvoiceMail($data));
        }
    }
}


