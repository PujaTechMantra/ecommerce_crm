<?php

namespace App\Livewire\Product;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\ProductItem;
use App\Models\Collection;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Color;
use App\Models\Size;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;
use DB;

class ProductList extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public $collection_id;
    public $cat_id;
    public $sub_cat_id;
    public $keyword;
    public $csv_file;
    public $stock_csv;

    public $collections = [];
    public $categories = [];
    public $subCategories = [];

    protected $rules = [
        'csv_file' => 'required|mimes:csv,txt|max:2048',
        'stock_csv' => 'required|mimes:csv,txt|max:2048',
    ];

    protected $listeners = [
        'autoImportCSV' => 'import',
    ];
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

    public function resetStockForm()
    {
        $this->reset(['stock_csv']);
        $this->resetErrorBag();
    }

    public function openFile()
    {
        $this->dispatch('openFile');
    }

    public function import()
    {
        $this->validate([
            'csv_file' => 'required|mimes:csv,txt|max:2048',
        ]);

        DB::beginTransaction();

        try {
            $path = $this->csv_file->getRealPath();
            $rows = array_map('str_getcsv', file($path));

            $header = array_map('trim', array_shift($rows));

            foreach ($rows as $row) {

                if (count($row) !== count($header)) {
                    continue;
                }

                $data = array_combine($header, $row);

                $productType = strtolower(trim($data['product_type']));

                if (!in_array($productType, ['single', 'variation'])) {
                    throw new \Exception("Invalid product_type: {$data['product_type']}");
                }

                //products
                $product = Product::where('product_sku', $data['product_code'])->first();

                if (!$product) {

                    $collectionName = trim($data['collection']);
                    $collection = Collection::firstOrCreate(
                        ['name' => $collectionName],
                        [
                            'slug'   => Str::slug($collectionName),
                            'status' => 1,
                        ]
                    );

                    $category = null;
                    if (!empty($data['category'])) {
                        $categoryName = trim($data['category']);

                        $category = Category::firstOrCreate(
                            [
                                'title'         => $categoryName,
                                'collection_id'=> $collection->id,
                            ],
                            [
                                'slug'   => Str::slug($categoryName),
                                'status' => 1,
                            ]
                        );
                    }

                    $subCat = null;
                    if (!empty($data['sub_category']) && $category) {
                        $subCategoryName = trim($data['sub_category']);

                        $subCat = SubCategory::firstOrCreate(
                            [
                                'title'         => $subCategoryName,
                                'category_id'  => $category->id,
                            ],
                            [
                                'status' => 1,
                            ]
                        );
                    }

                    $product = Product::create([
                        'title'            => $data['title'],
                        'slug'             => Str::slug($data['title']),
                        'product_sku'      => $data['product_code'],
                        'short_desc'       => $data['short_desc'],
                        'long_desc'        => $data['long_desc'],
                        'product_type'     => $productType,
                        'collection_id'    => $collection->id,
                        'category_id'      => $category?->id,
                        'sub_category_id'  => $subCat?->id,
                        'meta_title'       => $data['meta_title'] ?? null,
                        'meta_description' => $data['meta_description'] ?? null,
                        'meta_keyword'     => $data['meta_keyword'] ?? null,
                        'status'           => $data['status'] ?? 1,
                    ]);
                }

                // product item
                if (ProductItem::where('item_code', $data['item_code'])->exists()) {
                    throw new \Exception("Item code already exists: {$data['item_code']}");
                }

                $itemData = [
                    'product_id'    => $product->id,
                    'product_type'  => $productType,
                    'base_price'    => $data['base_price'],
                    'display_price' => $data['display_price'] ?? null,
                    'specification' => $data['specification'] ?? null,
                    'status'        => $data['item_status'] ?? 1,
                ];

                // only for variation products
                if ($productType === 'variation') {

                    // get color
                    $colorId = null;
                    if (!empty($data['color'])) {
                        $color = Color::firstOrCreate(
                            ['name' => trim($data['color'])],
                            ['status' => 1]
                        );
                        $colorId = $color->id;
                    }

                    // get size
                    $sizeId = null;
                    if (!empty($data['size'])) {
                        $size = Size::firstOrCreate(
                            ['name' => trim($data['size'])],
                            ['status' => 1]
                        );
                        $sizeId = $size->id;
                    }

                    $itemData['color_id'] = $colorId;
                    $itemData['size_id']  = $sizeId;
                    $itemData['item_code'] = $data['item_code'];

                }else{

                    $itemData['item_code'] = $product->product_sku;

                }

                ProductItem::create($itemData);

            }

            DB::commit();

            session()->flash('message', 'Products imported successfully!');

            $this->reset('csv_file');

            $this->dispatch('closeImportModal');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('csv_error', $e->getMessage());
        }
    }

    public function downloadStockSample()
    {
        $fileName = 'stock_sample.csv';

        return response()->streamDownload(function () {

            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['product_code', 'item_code', 'quantity']);

            $items = ProductItem::query()
                ->join('products', 'products.id', '=', 'product_items.product_id')
                ->whereNull('products.deleted_at')
                ->whereNull('product_items.deleted_at')
                ->select(
                    'products.product_sku',
                    'product_items.item_code'
                )
                ->orderBy('products.id')
                ->get();

            foreach ($items as $item) {
                fputcsv($handle, [
                    $item->product_sku, 
                    $item->item_code, 
                    ''        
                ]);
            }

            fclose($handle);

        }, $fileName, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
        ]);
    }

    public function updateStock()
    {
        $this->validate([
            'stock_csv' => 'required|mimes:csv,txt|max:2048',
        ]);

        DB::beginTransaction();

        try {
            $path = $this->stock_csv->getRealPath();
            $rows = array_map('str_getcsv', file($path));
            $header = array_map('trim', array_shift($rows));

            foreach ($rows as $row) {

                if (count($row) !== count($header)) {
                    continue;
                }

                $data = array_combine($header, $row);

                $productCode = trim($data['product_code']);
                $itemCode    = trim($data['item_code']);
                $qty         = (int) $data['quantity'];

                if ($qty <= 0) {
                    throw new \Exception("Invalid quantity for item: {$itemCode}");
                }

                $product = Product::where('product_sku', $productCode)->first();
                if (!$product) {
                    throw new \Exception("Product not found: {$productCode}");
                }

                $item = ProductItem::where('product_id', $product->id)
                    ->where('item_code', $itemCode)
                    ->first();

                if (!$item) {
                    throw new \Exception("Product item not found: {$itemCode}");
                }

                $item->quantity = ($item->quantity ?? 0) + $qty;
                $item->save();

                DB::table('stock_logs')->insert([
                    'product_id'      => $product->id,
                    'product_item_id' => $item->id,
                    'quantity'        => $qty,
                    'type'            => 'Add',
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }

            DB::commit();

            session()->flash('message', 'Stock updated successfully!');
            $this->reset('stock_csv');
            $this->dispatch('closeStockModal');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('stock_error', $e->getMessage());
        }
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
