<div class="row mb-4">

    <div class="col-lg-12">
        <div class="card">

            <!-- Header -->
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h6 class="mb-0">Product Reviews</h6>
                    </div>

                    <div class="col-md-4 d-flex justify-content-end align-items-center gap-2">
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            class="form-control border border-2 p-2 custom-input-sm"
                            placeholder="Search by user or review...">

                        <button
                            type="button"
                            onclick="location.reload()"
                            class="btn btn-danger text-white custom-input-sm">
                            <i class="ri-restart-line"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Body -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Product</th>
                                <th>Rating</th>
                                <th>Review</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reviews as $review)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td>
                                        {{ $review->user->name ?? 'N/A' }}
                                    </td>

                                    <td>
                                        <div class="fw-semibold">
                                            {{ $review->product->title ?? 'N/A' }}
                                        </div>

                                        <div class="text-muted small">
                                            {{ $review->product->product_sku ?? '' }}
                                        </div>
                                    </td>

                                    <td>
                                        <span class="badge bg-warning text-dark">
                                            {{ $review->rating }} ★
                                        </span>
                                    </td>

                                    <td>
                                        {{ Str::limit($review->review, 60) }}
                                    </td>

                                    <td>
                                        {{ $review->created_at->format('d M Y') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        No reviews found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

</div>
