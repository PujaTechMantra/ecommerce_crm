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
    public $product_code;
    public $meta_title;
    public $meta_description;
    public $meta_keyword;
    public $image;
    public $single_image = [];     
    public $existing_single_images = []; 
    public $product_type = '';

    public $rows = [];

    public $collections = [];
    public $categories = [];
    public $subcategories = [];
    public $colors = [];
    public $sizes = [];
    public $removed_single_images = [];
    public $removed_variation_images = [];
    public $removed_variation_ids = [];

    public function mount($productId)
    {
        $product = Product::with(['items.images'])->findOrFail($productId);
        $this->product = $product;

        // Load collections, colors, sizes
        $this->collections   = Collection::where('status', 1)->get();
        $this->colors        = Color::where('status', 1)->get();
        $this->sizes         = Size::where('status', 1)->get();
        $this->categories    = Category::where('status', 1)->get();
        $this->subcategories = SubCategory::where('status', 1)->get();

        // Main product data
        $this->collection_id = $product->collection_id;
        $this->cat_id        = $product->category_id;
        $this->subcat_id     = $product->sub_category_id;
        $this->title         = $product->title;
        $this->short_desc    = $product->short_desc;
        $this->desc          = $product->long_desc;
        $this->product_type  = $product->product_type;
        $this->product_code = $product->product_sku;
        $this->meta_title       = $product->meta_title;
        $this->meta_description = $product->meta_description;
        $this->meta_keyword     = $product->meta_keyword;

        // Filter categories & subcategories
        $this->categories = Category::where('collection_id', $this->collection_id)
                                ->where('status', 1)
                                ->get();
        $this->subcategories = SubCategory::where('category_id', $this->cat_id)
                                      ->where('status', 1)
                                      ->get();

        // Load items
        if ($this->product_type === 'single') {
            $item = $product->items()->first();
            if ($item) {
                $this->base_price = $item->base_price;
                $this->display_price = $item->display_price;
                $this->specification = $item->specification;
                $this->single_image = null;
                $this->existing_single_images = $item->images
                    ->pluck('image')
                    ->toArray();
            }
        } else {
            $this->rows = $product->items->map(function ($item) {
                return [
                    'id'            => $item->id,
                    'color_id'      => $item->color_id,
                    'size_id'       => $item->size_id,
                    'item_code'     => $item->item_code,
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
        $this->categories = Category::where('collection_id', $this->collection_id)
                ->where('status', 1)
                ->get();
        $this->cat_id = null;
        $this->subcat_id = null;
        $this->subcategories = [];
    }

    // Filter subcategories based on selected category
    public function updatedCatId()
    {
        $this->subcategories = $this->cat_id 
                            ? SubCategory::where('category_id', $this
                            ->where('status', 1)
                            ->cat_id)->get() : [];
        $this->subcat_id = null;
    }

    // Add new variation row
    public function addRow()
    {
        $this->rows[] = [
            'color_id' => '',
            'size_id' => '',
            'item_code' => '',
            'base_price' => '',
            'display_price' => '',
            'specification' => '',
            'images' => [],
            'existing_images' => [],
        ];
        $this->dispatch('init-editors');
    }

    public function removeSingleImage($index)
    {
        $this->removed_single_images[] = $this->existing_single_images[$index];
        unset($this->existing_single_images[$index]);
        $this->existing_single_images = array_values($this->existing_single_images);
    }

    public function removeVariationImage($rowIndex, $imgIndex)
    {
        $this->removed_variation_images[$rowIndex][] =
            $this->rows[$rowIndex]['existing_images'][$imgIndex];

        unset($this->rows[$rowIndex]['existing_images'][$imgIndex]);
        $this->rows[$rowIndex]['existing_images'] =
            array_values($this->rows[$rowIndex]['existing_images']);
    }


    // Remove variation row
    public function removeRow($index)
    {
        if ($this->product_type === 'variation' && count($this->rows) <= 1) {
            $this->addError('rows', 'At least one variation item is required.');
            return;
        }
        if (!empty($this->rows[$index]['existing_images'])) {
            foreach ($this->rows[$index]['existing_images'] as $img) {
                $this->removed_variation_images[$index][] = $img;
            }
        }
        if (!empty($this->rows[$index]['id'])) {
            $this->removed_variation_ids[] = $this->rows[$index]['id'];
        }

        unset($this->rows[$index]);
        $this->rows = array_values($this->rows);
    }

    public function updatedProductType($value)
    {
        if ($value === 'single') {
            $this->rows = [];
            $item = $this->product->items()->first();
            $this->base_price = $item?->base_price;
            $this->display_price = $item?->display_price;
            $this->specification = $item?->specification;
            $this->single_image = null; 

        } else {
            // Reset single fields
            $this->base_price = '';
            $this->display_price = '';
            $this->specification = '';
            $this->rows = $this->product->items->map(function ($item) {
                return [
                    'color_id' => $item->color_id,
                    'size_id' => $item->size_id,
                    'item_code' => $item->item_code,
                    'base_price' => $item->base_price,
                    'display_price' => $item->display_price,
                    'specification' => $item->specification,
                    'images' => [],
                    'existing_images' => $item->images->pluck('image')->toArray(),
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
            'product_sku' => $this->product_code,
            'collection_id' => $this->collection_id,
            'category_id' => $this->cat_id ?: null,
            'sub_category_id' => $this->subcat_id ?: null,
            'short_desc' => $this->short_desc,
            'long_desc' => $this->desc,
            'image' => $imagePath,
            'product_type' => $this->product_type,
            'meta_title'       => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keyword'     => $this->meta_keyword,
        ]);

        $images = ProductImage::whereIn(
            'product_item_id',
            $this->removed_variation_ids
        )->get();

        foreach ($images as $img) {
            Storage::disk('public')->delete($img->image);
            $img->delete();
        }
        ProductItem::whereIn('id', $this->removed_variation_ids)->delete();

        if ($this->product_type === 'single') {

           $singleItem = ProductItem::firstOrCreate([
                'product_id'   => $this->product->id,
                'product_type' => $this->product_type,
            ], [
                'image' => $this->product->image // fallback
            ]);

            $singleItem->update([
                'item_code'    => $this->product_code,
                'base_price'    => $this->base_price,
                'display_price' => $this->display_price,
                'specification' => $this->specification,
            ]);

            // SAVE MULTIPLE IMAGES
                foreach ($this->removed_single_images as $img) {
                    $image = ProductImage::where('image', $img)->first();
                    if ($image) {
                        Storage::disk('public')->delete($image->image);
                        $image->delete();
                    }
                }

                //  Upload new images (only if present)
                if (!empty($this->single_image)) {
                    foreach ($this->single_image as $file) {
                        $path = $file->store('product-images', 'public');

                        ProductImage::create([
                            'product_item_id' => $singleItem->id,
                            'image'           => $path,
                        ]);
                    }
                }

        } else {

            // Remove single item if switching to variation
            $singleItem = ProductItem::where('product_id', $this->product->id)
                ->where('product_type', 'single')
                ->first();

            if ($singleItem) {
                foreach ($singleItem->images as $img) {
                    Storage::disk('public')->delete($img->image);
                    $img->delete();
                }

                $singleItem->delete();
            }

                foreach ($this->removed_variation_images as $rowImages) {
                    foreach ($rowImages as $img) {
                        $image = ProductImage::where('image', $img)->first();
                        if ($image) {
                            Storage::disk('public')->delete($image->image);
                            $image->delete();
                        }
                    }
                }

                    // Variation products: handle each row
                foreach ($this->rows as $row) {

                    if (!empty($row['id'])) {
                        $item = ProductItem::find($row['id']);
                        $item->update([
                            'color_id'      => $row['color_id'],
                            'size_id'       => $row['size_id'],
                            'item_code'     => $row['item_code'],
                            'base_price'    => $row['base_price'],
                            'display_price' => $row['display_price'],
                            'specification' => $row['specification'],
                        ]);
                    } else {
                        $item = ProductItem::create([
                            'product_id'    => $this->product->id,
                            'product_type'  => $this->product_type,
                            'color_id'      => $row['color_id'],
                            'size_id'       => $row['size_id'],
                            'item_code'     => $row['item_code'],
                            'base_price'    => $row['base_price'],
                            'display_price' => $row['display_price'],
                            'specification' => $row['specification'],
                        ]);
                    }


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

    protected function messages()
    {
        return [
            'rows.required' => 'At least one variation item is required.',
            'rows.min'      => 'At least one variation item is required.',
            'rows.*.images.required' => 'At least one image is required for this variation.',
            'rows.*.images.min' => 'At least one image is required for this variation.',
        ];
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
            $attributes["rows.$index.image"]         = "Variation {$no} Image";
        }

        return $attributes;
    }

    protected function rules()
    {
        $rules = [
            'collection_id'     => 'required',
            'title'             => 'required|string|max:255',
            'product_code'      => 'required|string|max:100|unique:products,product_sku,' . $this->product->id,
            'product_type'      => 'required',
            'image'             => 'nullable|image',
            'meta_title'        => 'nullable|string|max:255',
            'meta_description'  => 'nullable|string|max:500',
            'meta_keyword'      => 'nullable|string|max:500',
        ];

        if ($this->product_type === 'single') {
            $rules['base_price'] = 'required|numeric|min:0';
            $rules['display_price'] = 'required|numeric|min:0';
            $rules['single_image'] = 'nullable|array';
            $rules['single_image.*'] = 'image|max:2048';
        }

        if ($this->product_type === 'variation') {

            $rules['rows'] = 'required|array|min:1';

            foreach ($this->rows as $index => $row) {
                $rules["rows.$index.color_id"] = 'required';
                $rules["rows.$index.size_id"] = 'required';
                $rules["rows.$index.item_code"] = 'required|string|max:100|unique:product_items,item_code,' . ($row['id'] ?? 'NULL') . ',id';
                $rules["rows.$index.base_price"] = 'required|numeric|min:0';
                $rules["rows.$index.display_price"] = 'required|numeric|min:0';

                if (empty($row['existing_images'])) {
                    $rules["rows.$index.images"] = 'required|array|min:1';
                } else {
                    $rules["rows.$index.images"] = 'nullable|array';
                }
                
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
