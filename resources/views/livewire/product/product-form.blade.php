<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="d-flex justify-content-between mb-4">
            <h4>{{ $product ? 'Edit Product' : 'Add Product' }}</h4>
            <button wire:click="save" class="btn btn-primary">
                {{ $product ? 'Update' : 'Publish Product' }}
            </button>
        </div>

        <div class="row">
            <!-- Left Column -->
            <div class="col-lg-8">

                <!-- Basic Product Info -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="form-floating mb-3">
                            <input type="text" wire:model.defer="name" class="form-control" placeholder="Name">
                            <label>Title</label>
                            @error('title') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <!-- Short Description -->
                        <div class="mb-3">
                            <label>Short Description</label>
                            <textarea wire:model.defer="short_desc" class="form-control" rows="3"></textarea>
                            @error('short_desc') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <!-- Full Description -->
                        <div class="mb-3">
                            <label>Description</label>
                            <textarea wire:model.defer="desc" class="form-control" rows="5"></textarea>
                            @error('desc') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                    </div>
                </div>
            </div>
           

            <!-- Right Column -->
            <div class="col-lg-4">
                <!-- Collection & Category -->
                <div class="card mb-4">
                   <div class="card-header">
                        <h5>Main Image</h5>
                    </div>
                    <div class="card-body text-center">
                        @if($image)
                            <img src="{{ $image->temporaryUrl() }}" class="img-fluid mb-3" />
                        @else
                            <img src="{{ asset('backend/images/placeholder-image.jpg') }}" class="img-fluid mb-3" />
                        @endif
                        <input type="file" wire:model="image" class="form-control">
                        @error('image') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Product Image -->
                <div class="card mb-4">
                    
                    <div class="card-body">

                        <div class="mb-3">
                            <label>Collection</label>
                            <select wire:model.live="collection_id" class="form-select">
                                <option value="">Select</option>
                                @foreach($collections as $collection)
                                    <option value="{{ $collection->id }}">{{ $collection->name }}</option>
                                @endforeach
                            </select>
                            @error('collection_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label>Category</label>
                            <select wire:model.live="cat_id" class="form-select" @disabled(!$collection_id)>
                                <option value="">Select</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->title }}</option>
                                @endforeach
                            </select>
                            @error('cat_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label>Subcategory</label>
                            <select wire:model="subcat_id" class="form-select" @disabled(!$cat_id)>
                                <option value="">Select</option>
                                @foreach($subcategories as $sub)
                                    <option value="{{ $sub->id }}">{{ $sub->title }}</option>
                                @endforeach
                            </select>
                            @error('subcat_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>


                    </div>

                    
                </div>


            </div>
        </div>
         <div class="card mb-4">
            <div class="card-body">

                <!-- Product Type Toggle -->
                <div class="mb-3">
                    <label class="form-label">Product Type</label>

                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio"
                                wire:model.live="product_type"
                                id="variation" value="variation">
                            <label class="form-check-label" for="variation">Variation Product</label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio"
                                wire:model.live="product_type"
                                id="direct" value="direct">
                            <label class="form-check-label" for="direct">Direct Product</label>
                        </div>
                    </div>
                </div>


                <!-- Base Price for Direct Product -->
                 @if($product_type === 'direct')

                <div class="mb-3">
                    <div class="border rounded p-3 mb-3 ">

                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label">Base Price</label>
                                <input type="number"
                                    wire:model.defer="base_price"
                                    class="form-control form-control-sm">
                                @error('base_price') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Display Price</label>
                                <input type="number"
                                    wire:model.defer="display_price"
                                    class="form-control form-control-sm">
                                @error('display_price') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <!-- Row 2 -->
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label">Image</label>
                                <input type="file"
                                    wire:model="image"
                                    class="form-control form-control-sm">
                            </div>

                            <div class="col-md-8">
                                <label class="form-label">Specification</label>
                                <textarea wire:model="specification"
                                        rows="2"
                                        class="form-control form-control-sm"></textarea>
                            </div>
                        </div>


                    </div>
                </div>
                @endif

                @if($product_type === 'variation')
                <div class="mb-3">

                    @foreach($rows as $index => $row)
                    <div class="border rounded p-3 mb-2 position-relative">

                        <!-- Row 1 -->
                        <div class="row g-2 mb-2">
                            <div class="col-md-3">
                                <label class="form-label">Color</label>
                                <select wire:model="rows.{{ $index }}.color_id"
                                        class="form-select form-select-sm">
                                    <option value="">Select</option>
                                    @foreach($colors as $color)
                                        <option value="{{ $color->id }}">{{ $color->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Size</label>
                                <select wire:model="rows.{{ $index }}.size_id"
                                        class="form-select form-select-sm">
                                    <option value="">Select</option>
                                    @foreach($sizes as $size)
                                        <option value="{{ $size->id }}">{{ $size->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Base Price</label>
                                <input type="number"
                                    wire:model="rows.{{ $index }}.base_price"
                                    class="form-control form-control-sm">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Display Price</label>
                                <input type="number"
                                    wire:model="rows.{{ $index }}.display_price"
                                    class="form-control form-control-sm">
                            </div>
                        </div>

                        <!-- Row 2 -->
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label">Image</label>
                                <input type="file"
                                    wire:model="rows.{{ $index }}.image"
                                    class="form-control form-control-sm">
                            </div>

                            <div class="col-md-8">
                                <label class="form-label">Specification</label>
                                <textarea wire:model="rows.{{ $index }}.specification"
                                        rows="2"
                                        class="form-control form-control-sm"></textarea>
                            </div>
                        </div>

                        <!-- Remove Button -->
                        <button type="button"
                                wire:click="removeRow({{ $index }})"
                                class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2">
                            ✕
                        </button>

                    </div>
                    @endforeach

                    <button type="button"
                            wire:click="addRow"
                            class="btn btn-primary btn-sm mt-2">
                        + Add Variation
                    </button>

                </div>
                @endif






            </div>
        </div>


    </div>
</div>
