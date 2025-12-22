    <div class="flex-grow-1">
            <div class="col-lg-12 justify-content-left">
                    <div class="row">
                        @if(session()->has('message'))
                        <div class="alert alert-success" id="flashMessage">
                            {{ session('message') }}
                        </div>
                        @endif

                    </div>
                </div>
               
            <div class="col-lg-12 d-flex justify-content-end gap-2 mb-3">
                <button type="button"
                        class="btn btn-info"
                        wire:click="resetStockForm"
                        data-bs-toggle="modal"
                        data-bs-target="#stockUpdateModal">
                    <i class="ri-database-2-line ri-16px me-1"></i> Stock Update
                </button>
                <button type="button"
                        class="btn btn-warning"
                        data-bs-toggle="modal"
                        data-bs-target="#importCsvModal">
                    <i class="ri-file-text-line ri-16px me-1"></i> Import
                </button>

                <a href="{{ route('admin.product.add') }}" class="btn btn-primary">
                    <i class="ri-add-line ri-16px me-1"></i> Add Product
                </a>
            </div>
            <!-- Import CSV Modal -->
            <div class="modal fade" id="importCsvModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
                <div class="modal-dialog modal-md modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Import Products</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <form wire:submit.prevent="import" enctype="multipart/form-data">
                            <div class="modal-body">

                                <!-- CSV Upload -->
                                <div class="mb-3">
                                    <label class="form-label">Upload CSV File</label>
                                    <input type="file"
                                        wire:model="csv_file"
                                        class="form-control"
                                        accept=".csv">
                                    @error('csv_file')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Sample CSV -->
                                <div class="mb-3">
                                    <a href="{{ asset('assets/sample/sample_product_import.csv') }}"
                                    target="_blank"
                                    class="btn btn-outline-success btn-sm">
                                        <i class="ri-download-line me-1"></i> Download Sample CSV
                                    </a>
                                </div>

                                @if(session()->has('csv_error'))
                                    <div class="alert alert-danger">
                                        {{ session('csv_error') }}
                                    </div>
                                @endif

                            </div>

                            <div class="modal-footer">
                                <button type="button"
                                        class="btn btn-secondary"
                                        data-bs-dismiss="modal">
                                    Cancel
                                </button>

                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-upload-line me-1"></i> Import
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Stock Update CSV Modal -->
            <div class="modal fade" id="stockUpdateModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
                <div class="modal-dialog modal-md modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Update Product Stock</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <form wire:submit.prevent="updateStock" enctype="multipart/form-data">
                            <div class="modal-body">

                                <!-- CSV Upload -->
                                <div class="mb-3">
                                    <label class="form-label">Upload Stock CSV File</label>
                                    <input type="file"
                                        wire:model="stock_csv"
                                        class="form-control"
                                        accept=".csv">
                                    @error('stock_csv')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Sample CSV -->
                                <div class="mb-3">
                                    <a href="javascript:void(0)"
                                        wire:click.prevent="downloadStockSample"
                                    class="btn btn-outline-success btn-sm">
                                        <i class="ri-download-line me-1"></i> Download Sample CSV
                                    </a>
                                </div>

                                @if(session()->has('stock_error'))
                                    <div class="alert alert-danger">
                                        {{ session('stock_error') }}
                                    </div>
                                @endif

                            </div>

                            <div class="modal-footer">
                                <button type="button"
                                        class="btn btn-secondary"
                                        data-bs-dismiss="modal">
                                    Cancel
                                </button>

                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-refresh-line me-1"></i> Update Stock
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


        <div class="card">
            <div class="card-header">
                <h5>Product List</h5>
                <div class="row g-2">
                

                    <div class="col-md-3">
                        <label>Collection</label>
                        <select wire:model.live="collection_id" class="form-select">
                            <option value="">All</option>
                            @foreach($collections as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label>Category</label>
                        <select wire:model.live="cat_id" class="form-select">
                            <option value="">All</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Sub Category</label>
                        <select wire:model.live="sub_cat_id" class="form-select">
                            <option value="">All</option>
                            @foreach($subCategories as $subCat)
                                <option value="{{ $subCat->id }}">{{ $subCat->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label></label>
                        <input type="text" wire:model.live.debounce.500ms="keyword" class="form-control" placeholder="Search">
                    </div>
                </div>
            </div>

            <div class="card-body table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Product Type</th>
                            <th>Product Code</th>
                            <th>Collection</th>
                            <th>Category</th>
                            <th>Sub-Category</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $index => $product)
                        <tr>
                            <td>{{ $products->firstItem() + $index }}</td>
                            <td>{{ $product->title }}</td>
                            <td>{{ ucfirst($product->product_type) }}</td>
                            <td>{{ $product->product_sku }}</td>
                            <td>{{ $product->collection->name ?? 'NA' }}</td>
                            <td>{{ $product->category->title ?? 'NA' }}</td>
                            <td>{{ $product->subCategory->title ?? 'NA' }}</td>
                            <td>
                                <div class="form-check form-switch">
                                    <input
                                        class="form-check-input ms-auto"
                                        type="checkbox"
                                        id="flexSwitchCheckDefault{{ $product->id }}"
                                        wire:click="toggleStatus({{ $product->id }})"
                                        @if($product->status) checked @endif>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('admin.product.edit', $product->id) }}"
                                    class="btn btn-sm">
                                        <i class="ri-edit-box-line text-info"></i>
                                    </a>
                                <button class="btn btn-sm" wire:click="$dispatch('confirmDelete', { itemId: {{ $product->id }} })"><i class="ri-delete-bin-line text-danger"></i></button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No products found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
@section('page-script')
<script>
    window.addEventListener('confirmDelete', function (event) {
        let itemId = event.detail.itemId;
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('destroy', itemId); 
            }
        });
    });

</script>
<script>
    window.addEventListener('closeImportModal', () => {
        const modal = bootstrap.Modal.getInstance(
            document.getElementById('importCsvModal')
        );
        modal?.hide();
    });
</script>
<script>
    window.addEventListener('closeStockModal', () => {
        const modal = bootstrap.Modal.getInstance(
            document.getElementById('stockUpdateModal')
        );
        modal?.hide();
    });
</script>

@endsection