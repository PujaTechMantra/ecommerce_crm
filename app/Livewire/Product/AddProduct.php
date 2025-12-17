<?php

namespace App\Livewire\Product;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\ProductItem;
use App\Models\ProductImage;
use App\Models\Collection;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Color;
use App\Models\Size;
use Illuminate\Support\Facades\Storage;

class AddProduct extends Component
{
    use WithFileUploads;

    public $product;

    public $collection_id;
    public $cat_id;
    public $subcat_id;
    public $title;

    public $short_desc;
    public $desc;

    public $base_price;
    public $display_price;
    public $specification;

    public $size_chart;
    public $pack;
    public $pack_count;
    public $master_pack;
    public $master_pack_count;
    public $only_for;

    public $image;
    public $dir_image = [];

    public $product_type = '';

    public $direct = [
        'base_price' => '',
        'display_price' => '',
        'image' => null,
        'specification' => '',
    ];

    public $rows = [
        [
            'color_id' => '',
            'size_id' => '',
            'base_price' => '',
            'display_price' => '',
            'images' => [],
            'specification' => '',
        ]
    ];

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
            $this->title = $product->title;
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
                    'base_price' => $option->price,
                    'display_price' => $option->offer_price,
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

    public function addRow()
    {
        $this->rows[] = [
            'color_id' => '',
            'size_id' => '',
            'base_price' => '',
            'display_price' => '',
            'image' => null,
            'specification' => '',
        ];

          $this->dispatch('init-editors');
    }

    public function updatedProductType()
    {
        $this->dispatch('init-editors');
    }

    public function removeRow($index)
    {
        unset($this->rows[$index]);
        $this->rows = array_values($this->rows);
    }

    public function save()
    {
        $this->validate();

        if ($this->image) {
            if ($this->product?->image && Storage::disk('public')->exists($this->product->image)) {
                Storage::disk('public')->delete($this->product->image);
            }

            $mainImagePath = $this->image->store('products', 'public');
        } else {
            $mainImagePath = $this->product?->image;
        }

        $product = Product::Create(
            [
                'title' => $this->title,
                'slug' => \Str::slug($this->title),
                'collection_id' => $this->collection_id,
                'category_id' => $this->cat_id,
                'sub_category_id' => $this->subcat_id,
                'short_desc' => $this->short_desc,
                'long_desc' => $this->desc,
                'image' => $mainImagePath,
                'product_type' => $this->product_type === 'direct' ? 1 : 2,
            ]
        );

        /** DIRECT PRODUCT **/
        if ($this->product_type === 'direct') {

            $item = ProductItem::create([
                'product_id' => $product->id,
                'product_type' => 1,
                'base_price' => $this->base_price,
                'display_price' => $this->display_price,
                'specification' => $this->specification,
            ]);

            // SAVE MULTIPLE DIRECT IMAGES
            if (!empty($this->dir_image)) {
                foreach ($this->dir_image as $file) {
                    $path = $file->store('product-images', 'public');

                    ProductImage::create([
                        'product_item_id' => $item->id,
                        'image'           => $path,
                    ]);
                }
            }
        }

        /** VARIATION PRODUCT **/
        if ($this->product_type === 'variation') {
           foreach ($this->rows as $row) {

                $item = ProductItem::create([
                    'product_id'     => $product->id,
                    'product_type'   => 2,
                    'color_id'       => $row['color_id'],
                    'size_id'        => $row['size_id'],
                    'base_price'     => $row['base_price'],
                    'display_price'  => $row['display_price'],
                    'specification'  => $row['specification'],
                ]);

                // SAVE MULTIPLE IMAGES
                if (!empty($row['images'])) {
                    foreach ($row['images'] as $file) {

                        $path = $file->store('product-images', 'public');

                        ProductImage::create([
                            'product_item_id' => $item->id, //
                            'image'           => $path,
                        ]);
                    }
                }
            }
        }

        session()->flash('message', 'Product saved successfully!');
        $this->redirectRoute('admin.product.index', navigate: true);
    }

    protected function validationAttributes()
    {
        $attributes = [];

        foreach ($this->rows as $index => $row) {
            $no = $index + 1;

            $attributes["rows.$index.color_id"]      = "Variation {$no} Color";
            $attributes["rows.$index.size_id"]       = "Variation {$no} Size";
            $attributes["rows.$index.base_price"]    = "Variation {$no} Base Price";
            $attributes["rows.$index.display_price"] = "Variation {$no} Display Price";
            $attributes["rows.$index.image"]         = "Variation {$no} Image";
        }

        return $attributes;
    }
    protected function rules()
    {
        $rules = [
            'collection_id' => 'required',
            'title'          => 'required|string|max:255',
            'image'         => $this->product
                ? 'nullable|image'
                : 'required|image',

            'product_type'  => 'required',
        ];

        // DIRECT PRODUCT
        if ($this->product_type === 'direct') {
            $rules['base_price']    = 'required|numeric|min:0';
            $rules['display_price'] = 'required|numeric|min:0';
            $rules['dir_image'] = 'nullable|array|min:1';
            $rules['dir_image.*'] = 'image|max:2048';
        }

        // VARIATION PRODUCT
        if ($this->product_type === 'variation') {
            foreach ($this->rows as $index => $row) {
                $rules["rows.$index.color_id"]      = 'required';
                $rules["rows.$index.size_id"]       = 'required';
                $rules["rows.$index.base_price"]    = 'required|numeric|min:0';
                $rules["rows.$index.display_price"] = 'required|numeric|min:0';
                $rules["rows.$index.images"] = 'required|array|min:1';
                $rules["rows.$index.images.*"] = 'image|max:2048';
            }
        }

        return $rules;
    }

    public function render()
    {
        return view('livewire.product.product-add');
    }
}
