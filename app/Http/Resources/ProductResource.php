<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        // $this là Product model
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'name' => $this->name,
            'slug' => $this->slug,
            'short_description' => $this->short_description,
            'base_price' => (float)$this->base_price,
            'total_stock' => (int)$this->total_stock,
            'default_image' => $this->default_image,
            'is_active' => (bool)$this->is_active,
            'visibility' => $this->visibility,
            'brand' => $this->whenLoaded('brand', function () {
                return [
                    'id' => $this->brand->id ?? null,
                    'name' => $this->brand->name ?? null,
                ];
            }),
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id ?? null,
                    'name' => $this->category->name ?? null,
                ];
            }),
            'variants' => ProductVariantResource::collection($this->whenLoaded('variants')),
            'images' => $this->whenLoaded('images', function () {
                return $this->images->map(fn($img) => [
                    'id' => $img->id,
                    'image_url' => $img->image_url,
                    'is_main' => $img->is_main,
                    'sort_order' => $img->sort_order
                ]);
            }),
            'reviews_summary' => $this->getReviewSummary(),
        ];
    }
    private function getReviewSummary()
    {
        $reviews = $this->whenLoaded('reviews', fn() => $this->reviews, collect());

        if ($reviews->isEmpty()) {
            return [
                'average_rating' => 0,
                'total_reviews' => 0,
                'ratings_count' => [
                    5 => 0,
                    4 => 0,
                    3 => 0,
                    2 => 0,
                    1 => 0
                ],
                'experience_avg' => [
                    'fabric' => 0,
                    'fit' => 0,
                    'color' => 0
                ],
                'reviews' => []
            ];
        }

        return [
            'average_rating' => round($reviews->avg('rating'), 1),
            'total_reviews' => $reviews->count(),
            'ratings_count' => [
                5 => $reviews->where('rating', 5)->count(),
                4 => $reviews->where('rating', 4)->count(),
                3 => $reviews->where('rating', 3)->count(),
                2 => $reviews->where('rating', 2)->count(),
                1 => $reviews->where('rating', 1)->count(),
            ],
            'experience_avg' => (function () use ($reviews) {
                $allExperiences = \App\Models\ReviewExperience::whereIn('review_id', $reviews->pluck('id'))->get();

                return [
                    'fabric' => round($allExperiences->where('criterion', 'Chất liệu vải')->avg('rating') ?? 0, 1),
                    'fit'    => round($allExperiences->where('criterion', 'Độ vừa vặn')->avg('rating') ?? 0, 1),
                    'color'  => round($allExperiences->where('criterion', 'Màu sắc')->avg('rating') ?? 0, 1),
                ];
            })(),
            // Lấy một vài đánh giá gần nhất
            'reviews' => $reviews->take(5)->map(function ($review) {
                return [
                    'id' => $review->id,
                    'user' => [
                        'name' => $review->user->name ?? 'Ẩn danh',
                        'avatar' => $review->user->avatar ?? null,
                    ],
                    'rating' => (int)$review->rating,
                    'comment' => $review->content,
                    'variant' => $review->productVariant?->attribute_summary,
                    'media' => $review->media->map(fn($m) => $m->url),
                    'created_at' => $review->created_at->diffForHumans(),
                ];
            })
        ];
    }
}
