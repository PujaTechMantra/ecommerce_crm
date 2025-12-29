<div class="row mb-4">
        <div class="col-lg-12 justify-content-left">
            <div class="row">
                @if(session()->has('message'))
                <div class="alert alert-success" id="flashMessage">
                    {{ session('message') }}
                </div>
                @endif

            </div>
        </div>
    @if($active_tab==1)
        <div class="col-lg-12 d-flex justify-content-end mb-3">
            <button class="btn btn-primary" wire:click="ActiveCreateTab(2)">
                <i class="ri-add-line ri-16px me-1"></i>Create New Size
            </button>
        </div>
    @else
        <div class="col-lg-12 d-flex justify-content-end mb-3">
            <button class="btn btn-dark btn-sm" wire:click="ActiveCreateTab(1)">
                <i class="ri-arrow-go-back-line ri-16px me-0 me-sm-2 align-baseline"></i>
                Back
            </button>
        </div>
    @endif

    <div class="col-lg-12">
        @if(session()->has('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
    </div>

    @if($active_tab==2)
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5>Add New Size</h5>
                    <form wire:submit.prevent="store">
                        <div class="mb-3">
                            <label>Size Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="title">
                            @error('title') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="text-end">
                            <button class="btn btn-primary btn-sm" type="submit">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    @elseif($active_tab==3)
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5>Edit Size</h5>
                    <form wire:submit.prevent="update">
                        <div class="mb-3">
                            <label>Size Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="title">
                            @error('title') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="text-end">
                            <button class="btn btn-primary btn-sm" type="submit">Update</button>
                            <button type="button" class="btn btn-secondary btn-sm" wire:click="ActiveCreateTab(1)">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    @else
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <!-- Left -->
                        <div class="col-md-8">
                            <h6 class="mb-0">Size List</h6>
                        </div>

                        <!-- Right -->
                        <div class="col-md-4 d-flex justify-content-end align-items-center gap-2">
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="search"
                                class="form-control border border-2 p-2 custom-input-sm"
                                placeholder="Search...">

                            <button
                                type="button"
                                onclick="location.reload()"
                                class="btn btn-danger text-white custom-input-sm">
                                <i class="ri-restart-line"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sizes as $size)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $size->name }}</td>
                                        <td class="align-middle text-sm text-center">
                                                <div class="form-check form-switch">
                                                    <input
                                                        class="form-check-input ms-auto"
                                                        type="checkbox"
                                                        id="flexSwitchCheckDefault{{ $size->id }}"
                                                        wire:click="toggleStatus({{ $size->id }})"
                                                        @if($size->status) checked @endif>
                                                </div>
                                        </td>
                                        <td>
                                         <button class="btn btn-sm" wire:click="edit({{ $size->id }})"><i class="ri-edit-box-line text-info"></i></button>
                                        <button class="btn btn-sm" wire:click="$dispatch('confirmDelete', { itemId: {{ $size->id }} })"><i class="ri-delete-bin-line text-danger"></i></button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No data found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>

@section('page-script')
<script>
    window.addEventListener('confirmDelete', event => {
        let itemId = event.detail.itemId;
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!",
        }).then(result => {
            if (result.isConfirmed) {
                @this.call('delete', itemId);
            }
        });
    });
</script>
@endsection
