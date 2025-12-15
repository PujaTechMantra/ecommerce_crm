<div class="row mb-4">
    <div class="col-lg-8 col-md-6 mb-md-0 mb-4">
        <div class="card my-4">
            <div class="card-header pb-2">

                @if(session()->has('message'))
                    <div class="alert alert-success" id="flashMessage">
                        {{ session('message') }}
                    </div>
                @endif

                <div class="row mt-2">
                    <div class="col-lg-6 col-7">
                        <h6>Categories</h6>
                    </div>

                    <div class="col-lg-6 col-5 my-auto text-end">
                        <div class="ms-md-auto d-flex align-items-center">
                            <input type="text"
                                wire:model.live.debounce.500ms="search"
                                class="form-control border border-2 p-2 custom-input-sm"
                                placeholder="Search Categories..">
                            
                        </div>
                    </div>
                </div>

            </div>

            <div class="card-body px-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">SL</th>
                                <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">Image</th>
                                <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">Title</th>
                                <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">Collection</th>
                                <th class="text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                <th class="text-end text-secondary text-xxs font-weight-bolder opacity-7 px-4">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($categories as $k => $category)
                                <tr>
                                    <td class="align-middle text-center">{{ $k + 1 }}</td>

                                    <td class="align-middle text-center">
                                        @if($category->image)
                                            <img src="{{ asset('storage/'.$category->image) }}" width="40" height="40"
                                                class="rounded">
                                        @else
                                            <span class="text-muted">No Image</span>
                                        @endif
                                    </td>

                                    <td class="align-middle text-center">{{ ucwords($category->title) }}</td>
                                    <td class="align-middle text-center">
                                        {{ $category->collection->name ?? '-' }}
                                    </td>

                                    <td class="align-middle text-center">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                wire:click="toggleStatus({{ $category->id }})"
                                                {{ $category->status ? 'checked' : '' }}>
                                        </div>
                                    </td>

                                    <td class="align-middle text-end px-4">
                                        <button wire:click="edit({{ $category->id }})"
                                            class="btn btn-sm btn-icon text-info">
                                            <i class="ri-edit-box-line ri-20px"></i>
                                        </button>

                                        <button
                                            wire:click="$dispatch('confirmDelete', { itemId: {{ $category->id }} })"
                                            class="btn btn-sm btn-icon text-danger">
                                            <i class="ri-delete-bin-7-line ri-20px"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
            </div>

        </div>
    </div>

    <div class="col-lg-4 col-md-6 mb-md-0 mb-4">
        <div class="card my-4">
            <div class="card-body px-0 pb-2 mx-4">

                <h5 class="mb-3">{{ $categoryId ? "Update Category" : "Create Category" }}</h5>

                <form wire:submit.prevent="{{ $categoryId ? 'update' : 'save' }}">

                    <div class="form-floating form-floating-outline mb-4">
                        <input type="text" wire:model="title" class="form-control border border-2 p-2"
                            placeholder="Enter Category Title">
                        <label>Title</label>
                    </div>
                    @error('title') <p class="text-danger">{{ $message }}</p> @enderror

                    <div class="form-floating form-floating-outline mb-4">
                        <select wire:model="collection_id" class="form-control border border-2 p-2">
                            <option value="">-- Select Collection --</option>
                            @foreach($collections as $col)
                                <option value="{{ $col->id }}">{{ $col->name }}</option>
                            @endforeach
                        </select>
                        <label>Choose Collection</label>
                    </div>
                    @error('collection_id') <p class="text-danger">{{ $message }}</p> @enderror

                    <div class="mb-3">
                        <label class="form-label">Category Image</label>
                        <input type="file" wire:model="image" class="form-control border border-2 p-2">
                    </div>
                    @error('image') <p class="text-danger">{{ $message }}</p> @enderror

                    @if($image)
                        <img src="{{ $image->temporaryUrl() }}" width="80" class="rounded mb-3">
                    @elseif($categoryId && $existingImage)
                        <img src="{{ asset('storage/'.$existingImage) }}" width="80" class="rounded mb-3">
                    @endif

                    <div class="text-end mt-4">
                        <button type="button" wire:click="refresh" class="btn btn-danger text-white btn-sm">
                            <i class="ri-restart-line"></i>
                        </button>

                        <button type="submit" class="btn btn-primary btn-sm" wire:loading.attr="disabled">
                            {{ $categoryId ? "Update Category" : "Create Category" }}
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>
@section('page-script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

