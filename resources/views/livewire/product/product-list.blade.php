<div class="content-wrapper">
            
    <div class="container-xxl flex-grow-1 container-p-y">
            <div class="col-lg-12 justify-content-left">
                    <div class="row">
                        @if(session()->has('message'))
                        <div class="alert alert-success" id="flashMessage">
                            {{ session('message') }}
                        </div>
                        @endif

                    </div>
                </div>
            <div class="col-lg-12 d-flex justify-content-end mb-3">
                <a href="{{ route('admin.product.add') }}" class="btn btn-primary">
                    <i class="ri-add-line ri-16px me-1"></i> Add Product
                </a>
            </div>
        <div class="card">
            <div class="card-header">
                <h5>Filter</h5>
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
                            <td>{{ $product->collection->name ?? '' }}</td>
                            <td>{{ $product->category->title ?? '' }}</td>
                            <td>{{ $product->subCategory->title ?? '' }}</td>
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
@endsection