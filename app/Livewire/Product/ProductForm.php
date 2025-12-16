<?php

namespace App\Livewire\Product;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\Collection;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Color;
use App\Models\Size;

class ProductForm extends Component
{
    use WithFileUploads;

    public $product;

    /* Basic */
    public $collection_id;
    public $cat_id;
    public $subcat_id;
    public $name;
    public $style_no;

    /* Description */
    public $short_desc;
    public $desc;

    /* Pricing */
    public $price;
    public $offer_price;

    /* Others */
    public $size_chart;
    public $pack;
    public $pack_count;
    public $master_pack;
    public $master_pack_count;
    public $only_for;

    /* Image */
    public $image;

    public $product_type = '';

    /* Options */
    public $rows = [
        ['color_id' => '', 'size_id' => '', 'price' => '', 'offer_price' => '']
    ];

    /* Dropdown data */
    public $collections = [];
    public $categories = [];
    public $subcategories = [];
    public $colors = [];
    public $sizes = [];

      public function mount(Product $product = null)
    {
        $this->product = $product;

        $this->collections = Collection::all();
        $this->categories  = Category::all();
        $this->subcategories = SubCategory::all();
        $this->colors      = Color::all();
        $this->sizes       = Size::all();

        if ($product) {
            $this->collection_id = $product->collection_id;
            $this->cat_id = $product->cat_id;
            $this->subcat_id = $product->subcat_id ?? null;
            $this->name = $product->name;
            $this->style_no = $product->style_no;
            $this->short_desc = $product->short_desc;
            $this->desc = $product->desc;
            $this->price = $product->price;
            $this->offer_price = $product->offer_price;
            $this->size_chart = $product->size_chart;
            $this->pack = $product->pack;
            $this->pack_count = $product->pack_count;
            $this->master_pack = $product->master_pack;
            $this->master_pack_count = $product->master_pack_count;
            $this->only_for = $product->only_for;
            $this->status = $product->status;

            $this->rows = $product->options->map(function($option) {
                return [
                    'color_id' => $option->color_id,
                    'size_id' => $option->size_id,
                    'price' => $option->price,
                    'offer_price' => $option->offer_price,
                ];
            })->toArray();

            $this->product_type = $this->rows ? 'variation' : 'direct';
        }

        $this->updatedCollectionId();
        $this->updatedCatId();
    }


    // Filter categories by selected collection
    public function updatedCollectionId()
    {
        $this->categories = Category::where('collection_id', $this->collection_id)->get();
        $this->cat_id = null;
        $this->subcat_id = null;
        $this->subcategories = [];
    }

    // Filter subcategories by selected category
    public function updatedCatId()
    {
        $this->subcategories = $this->cat_id ? SubCategory::where('category_id', $this->cat_id)->get() : [];
        $this->subcat_id = null;
    }

    // Add/Remove option rows
    public function addRow()
    {
        $this->rows[] = ['color_id' => '', 'size_id' => '', 'price' => '', 'offer_price' => ''];
    }

    public function removeRow($index)
    {
        unset($this->rows[$index]);
        $this->rows = array_values($this->rows);
    }

    // Save product
     public function save()
    {
        $this->validate();

        $imagePath = $this->image ? $this->image->store('products', 'public') : ($this->product->image ?? null);

        $product = $this->product 
            ? tap($this->product)->update([
                'collection_id' => $this->collection_id,
                'cat_id' => $this->cat_id,
                'subcat_id' => $this->subcat_id,
                'name' => $this->name,
                'style_no' => $this->style_no,
                'short_desc' => $this->short_desc,
                'desc' => $this->desc,
                'price' => $this->product_type === 'direct' ? $this->price : null,
                'offer_price' => $this->product_type === 'direct' ? $this->offer_price : null,
                'size_chart' => $this->size_chart,
                'pack' => $this->pack,
                'pack_count' => $this->pack_count,
                'master_pack' => $this->master_pack,
                'master_pack_count' => $this->master_pack_count,
                'only_for' => $this->only_for,
                'image' => $imagePath,
            ])
            : Product::create([
                'collection_id' => $this->collection_id,
                'cat_id' => $this->cat_id,
                'subcat_id' => $this->subcat_id,
                'name' => $this->name,
                'style_no' => $this->style_no,
                'short_desc' => $this->short_desc,
                'desc' => $this->desc,
                'price' => $this->product_type === 'direct' ? $this->price : null,
                'offer_price' => $this->product_type === 'direct' ? $this->offer_price : null,
                'size_chart' => $this->size_chart,
                'pack' => $this->pack,
                'pack_count' => $this->pack_count,
                'master_pack' => $this->master_pack,
                'master_pack_count' => $this->master_pack_count,
                'only_for' => $this->only_for,
                'image' => $imagePath,
            ]);

        // Only save options if variation product
        if ($this->product_type === 'variation') {
            if ($this->product) {
                $product->options()->delete();
            }
            foreach ($this->rows as $row) {
                $product->options()->create([
                    'color_id' => $row['color_id'],
                    'size_id' => $row['size_id'],
                    'price' => $row['price'],
                    'offer_price' => $row['offer_price'],
                ]);
            }
        }

        session()->flash('message', $this->product ? 'Product updated successfully' : 'Product created successfully');
        return redirect()->route('admin.product.index');
    }

     protected function rules()
    {
        $rules = [
            'collection_id' => 'required',
            'cat_id' => 'required',
            'name' => 'required|string|max:255',
            'style_no' => 'required',
            'image' => $this->product ? 'nullable|image|max:2048' : 'required|image|max:2048',
        ];

        if ($this->product_type === 'direct') {
            $rules['price'] = 'required|numeric';
            $rules['offer_price'] = 'nullable|numeric';
        } else {
            // Variation rules for rows
            foreach ($this->rows as $index => $row) {
                $rules["rows.$index.color_id"] = 'required';
                $rules["rows.$index.size_id"] = 'required';
                $rules["rows.$index.price"] = 'required|numeric';
                $rules["rows.$index.offer_price"] = 'nullable|numeric';
            }
        }

        return $rules;
    }

    public function render()
    {
        return view('livewire.product.product-form');
    }
}
