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
use Illuminate\Support\Str;
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
    public $product_code;
    public $meta_title;
    public $meta_description;
    public $meta_keyword;

    public $size_chart;
    public $pack;
    public $pack_count;
    public $master_pack;
    public $master_pack_count;
    public $only_for;

    public $image;
    public $single_image = [];

    public $product_type = '';

    public $single = [
        'base_price' => '',
        'display_price' => '',
        'image' => null,
        'specification' => '',
    ];

    public $rows = [
        [
            'color_id' => '',
            'size_id' => '',
            'item_code' => '',
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

        $this->collections   = Collection::where('status', 1)->get();
        $this->categories    = Category::where('status', 1)->get();
        $this->subcategories = SubCategory::where('status', 1)->get();
        $this->colors        = Color::where('status', 1)->get();
        $this->sizes         = Size::where('status', 1)->get();

        if ($product) {
            $this->collection_id = $product->collection_id;
            $this->cat_id = $product->category_id;
            $this->subcat_id = $product->sub_category_id ?? null;
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

            $this->rows = $product->items->map(function($item) {
                return [
                    'color_id' => $item->color_id,
                    'size_id' => $item->size_id,
                    'item_code' => $item->item_code,
                    'base_price' => $item->price,
                    'display_price' => $item->offer_price,
                ];
            })->toArray();

            $this->product_type = $this->rows ? 'variation' : 'single';
        }

        $this->updatedCollectionId();
        $this->updatedCatId();
    }


    // Filter categories by selected collection
    public function updatedCollectionId()
    {
        $this->categories = Category::where('collection_id', $this->collection_id)
                    ->where('status', 1)
                    ->get();
        $this->cat_id = null;
        $this->subcat_id = null;
        $this->subcategories = [];
    }

    // Filter subcategories by selected category
    public function updatedCatId()
    {
        $this->subcategories = $this->cat_id ? SubCategory::where('category_id', $this->cat_id)
                ->where('status', 1)
                ->get() : [];
        $this->subcat_id = null;
    }

    public function addRow()
    {
        $this->rows[] = [
            'color_id' => '',
            'size_id' => '',
            'item_code' => '',
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
                'product_sku' => $this->product_code,
                'slug' => \Str::slug($this->title),
                'collection_id' => $this->collection_id,
                'category_id' => $this->cat_id,
                'sub_category_id' => $this->subcat_id,
                'short_desc' => $this->short_desc,
                'long_desc' => $this->desc,
                'image' => $mainImagePath,
                'product_type' => $this->product_type,
                'meta_title' => $this->meta_title,  
                'meta_description' => $this->meta_description,  
                'meta_keyword' => $this->meta_keyword,  
            ]
        );

        /** single PRODUCT **/
        if ($this->product_type === 'single') {

            $item = ProductItem::create([
                'product_id' => $product->id,
                'product_type' => $this->product_type,
                'base_price' => $this->base_price,
                'item_code' => $this->product_code,
                'display_price' => $this->display_price,
                'specification' => $this->specification,
            ]);

            // SAVE MULTIPLE single IMAGES
            if (!empty($this->single_image)) {
                foreach ($this->single_image as $file) {
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
                    'product_type'   => $this->product_type,
                    'color_id'       => $row['color_id'],
                    'size_id'        => $row['size_id'],
                    'item_code'      => $row['item_code'],
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
            $attributes["rows.$index.item_code"]       = "Variation {$no} Item Code";
            $attributes["rows.$index.base_price"]    = "Variation {$no} Base Price";
            $attributes["rows.$index.display_price"] = "Variation {$no} Display Price";
            $attributes["rows.$index.images"]         = "Variation {$no} Image";
        }

        return $attributes;
    }
    protected function rules()
    {
        $rules = [
            'collection_id' => 'required',
            'title'          => 'required|string|max:255',
            'product_code' => 'required|string|max:100|unique:products,product_sku',
            'product_type' => 'required',
            'image'         => $this->product
                ? 'nullable|image'
                : 'required|image',

            'product_type'  => 'required',
        ];

        // single PRODUCT
        if ($this->product_type === 'single') {
            $rules['base_price']    = 'required|numeric|min:0';
            $rules['display_price'] = 'required|numeric|min:0';
            $rules['single_image'] = 'nullable|array';
            $rules['single_image.*'] = 'image';
        }

        // VARIATION PRODUCT
        if ($this->product_type === 'variation') {
            foreach ($this->rows as $index => $row) {
                $rules["rows.$index.color_id"]      = 'required';
                $rules["rows.$index.size_id"]       = 'required';
                $rules["rows.$index.item_code"]     = 'required|string|max:100|unique:product_items,item_code';
                $rules["rows.$index.base_price"]    = 'required|numeric|min:0';
                $rules["rows.$index.display_price"] = 'required|numeric|min:0';
                $rules["rows.$index.images"] = 'required|array|min:1';
                $rules["rows.$index.images.*"] = 'image';
            }
        }

        return $rules;
    }

    public function render()
    {
        return view('livewire.product.product-add');
    }
}
