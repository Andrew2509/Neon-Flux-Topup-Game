<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Rating;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    /**
     * Simpan ulasan setelah top-up sukses (satu kali per order_id).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => ['required', 'string', 'max:64', 'regex:/^[A-Za-z0-9\-]+$/'],
            'stars' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:8', 'max:500'],
            'author_nickname' => ['nullable', 'string', 'max:40'],
        ]);

        if (session('testimonial_eligible_order_id') !== $validated['order_id']) {
            abort(403, 'Sesi ulasan tidak valid. Buka lagi dari halaman sukses top-up.');
        }

        $order = Order::where('order_id', $validated['order_id'])->where('status', 'success')->first();
        if (! $order) {
            abort(403);
        }

        if (Rating::where('order_id', $validated['order_id'])->exists()) {
            return back()->with('error', 'Ulasan untuk pesanan ini sudah pernah dikirim.');
        }

        Rating::create([
            'user_id' => auth()->id(),
            'order_id' => $order->order_id,
            'product_name' => mb_substr((string) $order->product_name, 0, 255),
            'stars' => $validated['stars'],
            'comment' => $validated['comment'],
            'author_nickname' => $validated['author_nickname'] ?? null,
            'is_visible' => true,
        ]);

        session()->forget('testimonial_eligible_order_id');

        return redirect()
            ->to(route('home').'#testimoni')
            ->with('success', 'Terima kasih! Ulasan Anda sudah tercatat.');
    }
}
