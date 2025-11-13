<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        // Lấy danh sách review + liên kết cần thiết
        $reviews = Review::with([
            'user:id,name,email',
            'product:id,name',
            'productVariant:id,attribute_summary',
            'media',
            'experiences'
        ])
        ->latest()
        ->paginate(10);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function show($id)
    {
        $review = Review::with(['user', 'product', 'productVariant', 'media', 'experiences'])
                        ->findOrFail($id);

        return view('admin.reviews.show', compact('review'));
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return redirect()->route('admin.reviews.index')->with('success', 'Đã xóa đánh giá thành công');
    }
}
