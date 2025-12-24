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
        <i class="ri-add-line ri-16px me-1"></i>Create Coupon
    </button>
</div>
@else
<div class="col-lg-12 d-flex justify-content-end mb-3">
    <button class="btn btn-dark btn-sm" wire:click="ActiveCreateTab(1)">
         <i class="ri-arrow-go-back-line"></i>Back
    </button>
</div>
@endif

@if($active_tab==2 || $active_tab==3)
<div class="col-lg-12">
    <div class="card">
        <div class="card-body">
            <h5>{{ $active_tab == 2 ? 'Add Coupon' : 'Edit Coupon' }}</h5>

            <form wire:submit.prevent="{{ $active_tab==2 ? 'store' : 'update' }}">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="required">Coupon Name</label>
                        <input type="text" class="form-control" wire:model="name">
                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="required">Coupon Code</label>
                        <input type="text" class="form-control" wire:model="coupon_code">
                        @error('coupon_code') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="required">Type</label>
                        <select class="form-control" wire:model="coupon_type">
                            <option value="1">Percentage</option>
                            <option value="2">Flat</option>
                        </select>
                        @error('coupon_type') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="required">Amount</label>
                        <input type="number" min="0" class="form-control" wire:model="amount">
                        @error('amount') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="required">Max Use</label>
                        <input type="number" min="0" class="form-control" wire:model="max_time_of_use">
                        @error('max_time_of_use') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                     <div class="col-md-4 mb-3">
                        <label class="required">Max time One Can Use</label>
                        <input type="number" min="0" class="form-control" wire:model="max_time_one_can_use">
                        @error('max_time_one_can_use') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="required">Start Date</label>
                        <input type="date" class="form-control" wire:model="start_date">
                        @error('start_date') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="required">End Date</label>
                        <input type="date" class="form-control" wire:model="end_date">
                        @error('end_date') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="text-end">
                    <button class="btn btn-primary btn-sm">
                        {{ $active_tab==2 ? 'Save' : 'Update' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@else
<div class="col-lg-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h6>Coupon List</h6>
            <input type="text" class="form-control w-50"
                   wire:model.live.debounce.300ms="search"
                   placeholder="Search code...">
        </div>

        <div class="card-body p-0">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Coupon Type</th>
                        <th>Amount</th>
                        <th>Validity</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($coupons as $coupon)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $coupon->name }}</td>
                        <td>{{ $coupon->coupon_code }}</td>
                        <td>{{ $coupon->coupon_type == 1 ? 'Percentage' : 'Flat' }}</td>
                        <td>{{ $coupon->amount }}</td>
                        <td> 
                           {{ \Carbon\Carbon::parse($coupon->start_date)->format('d M Y') }}
                            -
                            {{ $coupon->end_date
                                ? \Carbon\Carbon::parse($coupon->end_date)->format('d M Y')
                                : 'N/A'
                            }}
                        </td>
                       <td class="align-middle text-sm text-center">
                            <div class="form-check form-switch">
                                <input
                                    class="form-check-input ms-auto"
                                    type="checkbox"
                                    id="flexSwitchCheckDefault{{ $coupon->id }}"
                                    wire:click="toggleStatus({{ $coupon->id }})"
                                    @if($coupon->status) checked @endif>
                            </div>
                        </td>
                        <td>
                            <button class="btn btn-sm" wire:click="edit({{ $coupon->id }})"><i class="ri-edit-box-line text-info"></i></button>
                            <button class="btn btn-sm" wire:click="$dispatch('confirmDelete', { itemId: {{ $coupon->id }} })"><i class="ri-delete-bin-line text-danger"></i></button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
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