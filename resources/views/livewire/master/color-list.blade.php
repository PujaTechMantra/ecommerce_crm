<div class="row mb-4">
<div class="row">
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
        <button type="button" class="btn btn-primary" wire:click="ActiveCreateTab(2)">
            <i class="ri-add-line ri-16px me-1"></i> Create New Color
        </button>
    </div>
@else
    <div class="col-lg-12 d-flex justify-content-end mb-3">
        <button type="button" class="btn btn-dark btn-sm" wire:click="ActiveCreateTab(1)">
            <i class="ri-arrow-go-back-line"></i> Back
        </button>
    </div>
@endif

<div class="col-lg-12">
    @if(session()->has('success'))
        <div class="alert alert-success" id="flashMessage">{{ session('success') }}</div>
    @endif
</div>

@if($active_tab==2)
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h5>Add New Color</h5>
                <form wire:submit.prevent="store">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Color Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" wire:model="title">
                            @error('title') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Color Code <span class="text-danger">*</span></label>
                            <input type="color" class="form-control form-control-color" wire:model="code" title="Choose color">
                            @error('code') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary btn-sm">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@elseif($active_tab==3)
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h5>Edit Color</h5>
                <form wire:submit.prevent="update">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Color Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" wire:model="title">
                            @error('title') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Color Code <span class="text-danger">*</span></label>
                            <input type="color" class="form-control form-control-color" wire:model="code" title="Choose color">
                            @error('code') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary btn-sm">Update</button>
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
                    <div class="col-md-6">
                        <h6>Color List</h6>
                    </div>
                    <div class="col-md-6 text-end">
                        <input type="text"
                        wire:model.live.debounce.300ms="search"
                        class="form-control w-50 d-inline"
                        placeholder="Search...">
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
                                <th>Code</th>
                                <th>Status</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($colors as $color)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $color->name }}</td>

                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span
                                                style="
                                                    width: 22px;
                                                    height: 22px;
                                                    background-color: {{ $color->code }};
                                                    border: 1px solid #ccc;
                                                    display: inline-block;
                                                    border-radius: 4px;
                                                ">
                                            </span>
                                            <span>{{ $color->code }}</span>
                                        </div>
                                    </td>
                                      <td class="align-middle text-sm text-center">
                                                <div class="form-check form-switch">
                                                    <input
                                                        class="form-check-input ms-auto"
                                                        type="checkbox"
                                                        id="flexSwitchCheckDefault{{ $color->id }}"
                                                        wire:click="toggleStatus({{ $color->id }})"
                                                        @if($color->status) checked @endif>
                                                </div>
                                        </td>
                                    <td>
                                        <button class="btn btn-sm" wire:click="edit({{ $color->id }})"><i class="ri-edit-box-line text-info"></i></button>
                                        <button class="btn btn-sm" wire:click="$dispatch('confirmDelete', { itemId: {{ $color->id }} })"><i class="ri-delete-bin-line text-danger"></i></button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No data found</td>
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

