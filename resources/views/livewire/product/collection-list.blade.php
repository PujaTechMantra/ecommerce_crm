<div class="row mb-4">

  <div class="col-lg-8 col-md-6 mb-md-0 mb-4">
    <div class="row">
      <div class="col-12">
        <div class="card my-4">

          <div class="card-header pb-2">

            <div class="row">
              @if(session()->has('message'))
                <div class="alert alert-success" id="flashMessage">
                    {{ session('message') }}
                </div>
              @endif
            </div>

            <div class="row">
              <div class="col-lg-6 col-7">
                <h6>Collections</h6>
              </div>

              <div class="col-lg-6 col-5 my-auto text-end">
                <div class="ms-md-auto d-flex align-items-center">
                    <input type="text"
                      wire:model.live.debounce.500ms="search"
                      class="form-control border border-2 p-2 custom-input-sm"
                      placeholder="Search Collections..">

                </div>
              </div>

            </div>
          </div>

          <div class="card-body px-0 pb-2">
            <div class="table-responsive p-0">

              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">SL</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Image</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Title</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                    <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 px-4">
                      Actions
                    </th>
                  </tr>
                </thead>

                <tbody>
                  @foreach($collections as $k => $collection)
                  <tr>
                    <td class="align-middle text-center">{{ $k + 1 }}</td>

                    <td class="align-middle text-center">
                      @if($collection->image)
                        <img src="{{ asset('storage/'.$collection->image) }}" width="40" height="40" class="rounded">
                      @else
                        <span class="text-muted">No Image</span>
                      @endif
                    </td>

                    <td class="align-middle text-center">
                      {{ ucwords($collection->name) }}
                    </td>

                    <td class="align-middle text-sm text-center">
                      <div class="form-check form-switch">
                        <input class="form-check-input ms-auto" type="checkbox"
                              id="switch{{ $collection->id }}"
                              wire:click="toggleStatus({{ $collection->id }})"
                              @if($collection->status) checked @endif >
                      </div>
                    </td>

                    <td class="align-middle text-end px-4">

                      <button wire:click="edit({{ $collection->id }})"
                        class="btn btn-sm btn-icon btn-text-secondary rounded-pill waves-effect"
                        title="Edit">
                        <i class="ri-edit-box-line ri-20px text-info"></i>
                      </button>

                      <button wire:click="$dispatch('confirmDelete', { itemId: {{ $collection->id }} })"
                        class="btn btn-sm btn-icon btn-text-secondary rounded-pill waves-effect"
                        title="Delete">
                        <i class="ri-delete-bin-7-line ri-20px text-danger"></i>
                      </button>

                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>

              <div class="d-flex justify-content-end mt-2">
                {{ $collections->links() }}
              </div>

            </div>
          </div>

        </div>
      </div>
    </div>
  </div>


  <div class="col-lg-4 col-md-6 mb-md-0 mb-4">
    <div class="row">
      <div class="col-12">

        <div class="card my-4">
          <div class="card-body px-0 pb-2 mx-4">

            <div class="d-flex justify-content-between mb-3">
              <h5>{{ $collectionId ? 'Update Collection' : 'Create Collection' }}</h5>
            </div>

            <form wire:submit.prevent="{{ $collectionId ? 'update' : 'save' }}">

              <div class="row">

                <div class="form-floating form-floating-outline mb-5">
                  <input type="text" wire:model="name"
                  class="form-control border border-2 p-2"
                  placeholder="Enter Collection Name">

                  <label>Name</label>
                </div>
                @error('name') <p class="text-danger">{{ $message }}</p> @enderror

                <div class="mb-3">
                  <label class="form-label">Collection Image</label>
                  <input type="file" wire:model="image" class="form-control border border-2 p-2">
                </div>
                @error('image') <p class="text-danger">{{ $message }}</p> @enderror

                @if($collectionId && $collectionImage = \App\Models\Collection::find($collectionId)->image)
                  <div class="mb-3">
                    <img src="{{ asset('storage/'.$collectionImage) }}" width="80" class="rounded">
                  </div>
                @endif

                <div class="mb-2 text-end mt-4">
                  <button type="button" wire:click="refresh"
                    class="btn btn-danger text-white mb-0 ms-2 btn-sm">
                    <i class="ri-restart-line"></i>
                  </button>

                  <button type="submit"
                    class="btn btn-secondary btn-sm btn-primary waves-effect waves-light"
                    wire:loading.attr="disabled">
                    <span>{{ $collectionId ? "Update Collection" : "Create Collection" }}</span>
                  </button>
                </div>

              </div>

            </form>

          </div>
        </div>

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