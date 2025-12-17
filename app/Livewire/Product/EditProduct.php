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

class EditProduct extends Component
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
    public $image;
    public $dir_image = [];     
    public $existing_dir_images = []; 
    public $product_type = null;

    public $rows = [];

    public $collections = [];
    public $categories = [];
    public $subcategories = [];
    public $colors = [];
    public $sizes = [];

    public function mount($productId)
    {
        $product = Product::with('items')->findOrFail($productId);
        $this->product = $product;

        // Load collections, colors, sizes
        $this->collections = Collection::all();
        $this->colors = Color::all();
        $this->sizes = Size::all();

        // Main product data
        $this->collection_id = $product->collection_id;
        $this->cat_id        = $product->category_id;
        $this->subcat_id     = $product->sub_category_id;
        $this->title         = $product->title;
        $this->short_desc    = $product->short_desc;
        $this->desc          = $product->long_desc;
        $this->product_type  = $product->product_type == 1 ? 'direct' : 'variation';

        // Filter categories & subcategories
        $this->categories = Category::where('collection_id', $this->collection_id)->get();
        $this->subcategories = SubCategory::where('category_id', $this->cat_id)->get();

        // Load items
        if ($this->product_type === 'direct') {
            $item = $product->items()->first();
            if ($item) {
                $this->base_price = $item->base_price;
                $this->display_price = $item->display_price;
                $this->specification = $item->specification;
                $this->dir_image = null;
                $this->existing_dir_images = $item->images
                    ->pluck('image')
                    ->toArray();
            }
        } else {
            $this->rows = $product->items->map(function ($item) {
                return [
                    'color_id'      => $item->color_id,
                    'size_id'       => $item->size_id,
                    'base_price'    => $item->base_price,
                    'display_price' => $item->display_price,
                    'specification' => $item->specification,
                    'images' => [],
                    'existing_images' => $item->images->pluck('image')->toArray(),

                ];
            })->toArray();
        }
    }


    // Filter categories based on selected collection
    public function updatedCollectionId()
    {
        $this->categories = Category::where('collection_id', $this->collection_id)->get();
        $this->cat_id = null;
        $this->subcat_id = null;
        $this->subcategories = [];
    }

    // Filter subcategories based on selected category
    public function updatedCatId()
    {
        $this->subcategories = $this->cat_id ? SubCategory::where('category_id', $this->cat_id)->get() : [];
        $this->subcat_id = null;
    }

    // Add new variation row
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

    // Remove variation row
    public function removeRow($index)
    {
        unset($this->rows[$index]);
        $this->rows = array_values($this->rows);
    }

    public function updatedProductType($value)
    {
        if ($value === 'direct') {
            $this->rows = [];
            $item = $this->product->items()->first();
            $this->base_price = $item?->base_price;
            $this->display_price = $item?->display_price;
            $this->specification = $item?->specification;
            $this->dir_image = null; 

        } else {
            // Reset direct fields
            $this->base_price = '';
            $this->display_price = '';
            $this->specification = '';
            $this->rows = $this->product->items->map(function ($item) {
                return [
                    'color_id' => $item->color_id,
                    'size_id' => $item->size_id,
                    'base_price' => $item->base_price,
                    'display_price' => $item->display_price,
                    'specification' => $item->specification,
                    'image' => null,
                    'existing_image' => $item->image,
                ];
            })->toArray();
        }
         $this->dispatch('init-editors');
    }   

    public function save()
    {
        $this->validate();

        // Handle main product image
        $imagePath = $this->product->image;
        if ($this->image) {
            if ($this->product->image && Storage::disk('public')->exists($this->product->image)) {
                Storage::disk('public')->delete($this->product->image);
            }
            $imagePath = $this->image->store('products', 'public');
        }

        // Update product
        $this->product->update([
            'title' => $this->title,
            'slug' => Str::slug($this->title),
            'collection_id' => $this->collection_id,
            'category_id' => $this->cat_id ?: null,
            'sub_category_id' => $this->subcat_id ?: null,
            'short_desc' => $this->short_desc,
            'long_desc' => $this->desc,
            'image' => $imagePath,
            'product_type' => $this->product_type === 'direct' ? 1 : 2,
        ]);

        if ($this->product_type === 'direct') {

           $directItem = ProductItem::firstOrCreate([
                'product_id'   => $this->product->id,
                'product_type' => 1,
            ], [
                'image' => $this->product->image // fallback
            ]);

            $directItem->update([
                'base_price'    => $this->base_price,
                'display_price' => $this->display_price,
                'specification' => $this->specification,
            ]);

            // SAVE MULTIPLE IMAGES
            if (!empty($this->dir_image)) {

                // Optional: delete old images
                foreach ($directItem->images as $img) {
                    Storage::disk('public')->delete($img->image);
                    $img->delete();
                }

                foreach ($this->dir_image as $file) {
                    $path = $file->store('product-images', 'public');

                    ProductImage::create([
                        'product_item_id' => $directItem->id,
                        'image'           => $path,
                    ]);
                }
            }

        } else {

            // Remove direct item if switching to variation
            ProductItem::where('product_id', $this->product->id)
                ->where('product_type', 1)
                ->delete();

                    // Variation products: handle each row
                foreach ($this->rows as $row) {

                    $item = ProductItem::updateOrCreate(
                        [
                            'product_id'   => $this->product->id,
                            'color_id'     => $row['color_id'],
                            'size_id'      => $row['size_id'],
                            'product_type' => 2,
                        ],
                        [
                            'base_price'     => $row['base_price'],
                            'display_price'  => $row['display_price'],
                            'specification'  => $row['specification'],
                        ]
                    );

                    // SAVE MULTIPLE IMAGES (product_id)
                    if (!empty($row['images'])) {
                        foreach ($row['images'] as $file) {

                            $path = $file->store('product-images', 'public');

                            ProductImage::create([
                                'product_item_id' => $item->id,   // ONLY THIS
                                'image'           => $path,
                            ]);
                        }
                    }
                }

        }

        session()->flash('message', 'Product updated successfully!');
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
            'title'         => 'required|string|max:255',
            'image'         => 'nullable|image',
            'product_type'  => 'required',
        ];

        if ($this->product_type === 'direct') {
            $rules['base_price'] = 'required|numeric|min:0';
            $rules['display_price'] = 'required|numeric|min:0';
            $rules['dir_image'] = 'nullable|array';
            $rules['dir_image.*'] = 'image|max:2048';
        }

        if ($this->product_type === 'variation') {
            foreach ($this->rows as $index => $row) {
                $rules["rows.$index.color_id"] = 'required';
                $rules["rows.$index.size_id"] = 'required';
                $rules["rows.$index.base_price"] = 'required|numeric|min:0';
                $rules["rows.$index.display_price"] = 'required|numeric|min:0';
                $rules["rows.$index.images"] = $this->product
                                            ? 'nullable|array'
                                            : 'required|array|min:1';

                    $rules["rows.$index.images.*"] = 'image|max:2048';
            }
        }

        return $rules;
    }

    public function render()
    {
        return view('livewire.product.product-edit');
    }
}
