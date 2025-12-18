<?php

namespace App\Livewire\Product;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\ProductItem;
use App\Models\Collection;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Support\Facades\Auth;
use DB;

class ProductList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $collection_id;
    public $cat_id;
    public $sub_cat_id;
    public $keyword;

    public $collections = [];
    public $categories = [];
    public $subCategories = [];


    protected $updatesQueryString = [ 'sub_cat_id', 'collection_id', 'cat_id', 'keyword'];

    public function mount()
    {

        $this->collections = Collection::where('status', 1)
            ->where('is_deleted', 0)
            ->orderBy('id', 'DESC')
            ->get();

        $this->categories = Category::where('status', 1)
            ->where('deleted_at', null)
            ->orderBy('id', 'DESC')
            ->get();
        $this->subCategories = SubCategory::where('status', 1)
            ->where('deleted_at', null)
            ->orderBy('id', 'DESC')
            ->get();
            
    }

    public function updating($field)
    {
        $this->resetPage();
    }

     public function toggleStatus($id)
    {
        $product = Product::findOrFail($id);
        $product->status = !$product->status;
        $product->save();

        session()->flash('message', 'Product status updated successfully!');
    }

    public function render()
    {
        $query = Product::with(['items', 'category'])
            ->whereNull('deleted_at');

        if ($this->collection_id) {
            $query->where('collection_id', $this->collection_id);
        }

        if ($this->cat_id) {
            $query->where('category_id', $this->cat_id);
        }
        if ($this->sub_cat_id) {
            $query->where('sub_category_id', $this->sub_cat_id);
        }

        if ($this->keyword) {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->keyword}%");
            });
        }

        $products = $query->orderBy('id', 'desc')->paginate(25);

        return view('livewire.product.product-list', compact('products'));
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        ProductItem::where('product_id', $product->id)->delete();
        
        $product->deleted_at = now();
        $product->save();

        // ProductItem::where('product_id', $product->id)
        //     ->update(['deleted_at' => now()]);

        session()->flash('success', 'Product deleted successfully!');

    }

}
