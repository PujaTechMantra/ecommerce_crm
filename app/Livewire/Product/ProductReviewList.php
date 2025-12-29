<?php

namespace App\Livewire\Product;

use Livewire\Component;
use App\Models\ProductReview;

class ProductReviewList extends Component
{
    public $search = '';

    public function render()
    {
        $reviews = ProductReview::with('user')
            ->where(function ($q) {
                $q->where('review', 'like', "%{$this->search}%")
                  ->orWhereHas('user', function ($u) {
                      $u->where('name', 'like', "%{$this->search}%");
                  });
            })
            ->latest()
            ->get();

        return view('livewire.product.product-review', compact('reviews'));
    }
}
