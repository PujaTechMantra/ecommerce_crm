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
        <div class="d-flex justify-content-between mb-4 align-items-center">
            <h4>Edit Product</h4>
            <div>
                <a href="{{route('admin.product.index')}}" class="btn btn-dark btn-sm me-2">Back</a>
                <button wire:click="save" class="btn btn-primary btn-sm">Update Product</button>
            </div>
        </div>

        <div class="row">
            <!-- Left Column -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="form-floating mb-3">
                            <input type="text" wire:model.defer="title" class="form-control" placeholder="Title">
                            <label class="required">Title</label>
                            @error('title') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" wire:model.defer="product_code" class="form-control" placeholder="Product Code">
                            <label class="required">Product Code</label>
                            @error('product_code') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label>Short Description</label>
                            <!-- <textarea wire:ignore="short_desc" class="form-control" rows="3" id="short_des" ></textarea> -->

                            <div wire:ignore>
                                <textarea id="short_desc" class="form-control">{{ $short_desc }}</textarea>
                            </div>
                            @error('short_desc') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label>Description</label>
                            <!-- <textarea wire:ignore="desc" class="form-control" rows="5" id="des"></textarea> -->
                            <div wire:ignore>
                                <textarea id="desc" class="form-control">{{ $desc }}</textarea>
                            </div>
                            @error('desc') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-body">
                        <h6>Meta</h6>
                        <div class="form-floating mb-3">
                            <input type="text" wire:model.defer="meta_title" class="form-control" placeholder="Title">
                            <label>Title</label>
                            @error('meta_title') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" wire:model.defer="meta_description" class="form-control" placeholder="Description">
                            <label>Description</label>
                            @error('meta_description') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" wire:model.defer="meta_keyword" class="form-control" placeholder="Keyword">
                            <label>Keyword</label>
                            @error('meta_keyword') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-lg-4">
                <!-- Main Image -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="required">Main Image</h5>
                    </div>

                    <div class="card-body text-center">
                        <div class="w-100 product__thumb">
                            <label for="main_image">
                                <img
                                    src="
                                        @if($image)
                                            {{ $image->temporaryUrl() }}
                                        @elseif($product->image)
                                            {{ asset('storage/'.$product->image) }}
                                        @else
                                            {{ asset('assets/img/placeholder-product.jpg') }}
                                        @endif
                                    "
                                    class="img-fluid mb-3 border rounded"
                                    style="width:100%; max-height:120px; object-fit:cover; cursor:pointer;"
                                />
                            </label>

                            @error('image')
                                <p class="small text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <input
                            type="file"
                            id="main_image"
                            accept="image/*"
                            wire:model="image"
                            class="d-none"
                        >

                        <small>Image Size: 870px × 1160px</small>
                    </div>
                </div>


                <!-- Collection / Category / Subcategory -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="mb-3" wire:ignore>
                            <label class="card-header required">Collection</label>
                            <select
                                id="collection_id"
                                class="form-select"
                                data-selected="{{ $collection_id }}"
                            >
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

        <!-- Product Type -->
        <div class="card mb-4">
            <div class="card-body">
                <label class="form-label required">Product Type</label>
                <div>
                    @php
                        $disableProductType = $product->items->count() > 0;
                    @endphp
                    <div class="form-check form-check-inline">
                       <input type="radio"
                            wire:model="product_type"
                            id="variation"
                            value="variation"
                            class="form-check-input"
                            @disabled($disableProductType)>
                        <label for="variation" class="form-check-label">Variation Product</label>
                    </div>
                    <div class="form-check form-check-inline">
                       <input type="radio"
                            wire:model="product_type"
                            id="single"
                            value="single"
                            class="form-check-input"
                            @disabled($disableProductType)>
                           
                        <label for="single" class="form-check-label">Single Product</label>
                    </div>
                   
                </div>
                 @error('product_type') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
               
        </div>

        <!-- single Product Form -->
        @if($product_type === 'single')
            <div wire:key="product-type-single" class="card mb-4">
                <div class="card-body">
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label class="form-label required">Base Price</label>
                            <input type="number" wire:model.defer="base_price" class="form-control">
                            @error('base_price') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Display Price</label>
                            <input type="number" wire:model.defer="display_price" class="form-control">
                            @error('display_price') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-md-4">
                            <label>Image</label>
                            <input type="file" wire:model="single_image" multiple accept="image/*" class="form-control">
                                {{-- New uploads preview --}}
                                @if($single_image)
                                    <div class="d-flex flex-wrap gap-2 mt-2">
                                        @foreach($single_image as $img)
                                            <img src="{{ $img->temporaryUrl() }}"
                                                class="border rounded"
                                                style="width:80px;height:80px;object-fit:cover;">
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Existing images --}}
                                @if(!$single_image && !empty($existing_single_images))
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    @foreach($existing_single_images as $index => $img)
                                        <div class="position-relative">
                                            <img src="{{ asset('storage/'.$img) }}"
                                                class="border rounded"
                                                style="width:80px;height:80px;object-fit:cover;">

                                            <button type="button"
                                                    wire:click="removeSingleImage({{ $index }})"
                                                    class="btn btn-danger btn-sm position-absolute top-0 end-0"
                                                    style="padding:2px 6px;">
                                                ✕
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif


                            @error('single_image.*') 
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-2 mb-2">

                        <div class="col-md-12">
                            <label>Specification</label>
                               <!-- <textarea wire:ignore="specification" class="form-control" id="spec"></textarea> -->
                               <div wire:ignore>
                                    <textarea id="spec" class="form-control">{{ $specification }}</textarea>
                                </div>
                            @error('specification') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Variation Product Form -->
        @if($product_type === 'variation')
            <div wire:key="product-type-variation" class="card mb-4">
                <div class="card-body">
                    @error('rows')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    @foreach($rows as $index => $row)
                        <div class="border p-3 mb-3 position-relative">
                            <div class="row g-2 mb-2">
                                <div class="col-md-2">
                                    <label class="form-label required">Color</label>
                                    <select wire:model="rows.{{ $index }}.color_id" class="form-select">
                                        <option value="">Select</option>
                                        @foreach($colors as $color)
                                            <option value="{{ $color->id }}">{{ $color->name }}</option>
                                        @endforeach
                                    </select>
                                    @error("rows.$index.color_id") <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label required">Size</label>
                                    <select wire:model="rows.{{ $index }}.size_id" class="form-select">
                                        <option value="">Select</option>
                                        @foreach($sizes as $size)
                                            <option value="{{ $size->id }}">{{ $size->name }}</option>
                                        @endforeach
                                    </select>
                                    @error("rows.$index.size_id") <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label required">Item Code</label>
                                    <input type="text" wire:model="rows.{{ $index }}.item_code" class="form-control">
                                    @error("rows.$index.item_code") <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label required">Base Price</label>
                                    <input type="number" wire:model="rows.{{ $index }}.base_price" class="form-control">
                                    @error("rows.$index.base_price") <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label required">Display Price</label>
                                    <input type="number" wire:model="rows.{{ $index }}.display_price" class="form-control">
                                    @error("rows.$index.display_price") <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="row g-2 mb-2">
                                <div class="col-md-4">
                                    <label class="form-label required">Image</label>
                                    <!-- <input type="file" wire:model="rows.{{ $index }}.image" class="form-control"> -->
                                    <input type="file" wire:model="rows.{{ $index }}.images" class="form-control" multiple>
                                    @if(!empty($row['existing_images']))
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($row['existing_images'] as $imgIndex => $img)
                                            <div class="position-relative">
                                                <img src="{{ asset('storage/'.$img) }}"
                                                    class="rounded border"
                                                    style="width:70px;height:70px;object-fit:cover;">

                                                <button type="button"
                                                        wire:click="removeVariationImage({{ $index }}, {{ $imgIndex }})"
                                                        class="btn btn-danger btn-sm position-absolute top-0 end-0"
                                                        style="padding:2px 6px;">
                                                    ✕
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                    @error("rows.$index.images") <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="row g-2 mb-2">

                                <div class="col-md-12">
                                    <label>Specification</label>
                                    <!-- <textarea wire:ignore="rows.{{ $index }}.specification" class="form-control row_spec"></textarea> -->

                                    <div wire:ignore>
                                        <textarea id="row_spec_{{ $index }}" class="form-control">
                                            {{ $row['specification'] ?? '' }}
                                        </textarea>
                                    </div>
                                    @error("rows.$index.specification") <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <button type="button" wire:click="removeRow({{ $index }})" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2">✕</button>
                        </div>
                    @endforeach

                    <button type="button" wire:click="addRow" class="btn btn-success btn-sm">+ Add Variation</button>

                </div>
            </div>
        @endif
        <div class="d-flex justify-content-end">
            <button wire:click="save" class="btn btn-primary align-right btn-sm">Update Product</button>
        </div>
    </div>
</div>
@section('page-script')
<script src="https://cdn.ckeditor.com/ckeditor5/41.2.0/classic/ckeditor.js"></script>

<script>

    // ClassicEditor.create( document.querySelector( '#des' ) ).catch( error => {
    //     console.error( error );
    // });

    // ClassicEditor.create( document.querySelector( '#short_des' ) ).catch( error => {
    //     console.error( error );
    // });
    // ClassicEditor.create( document.querySelector( '#spe' ) ).catch( error => {
    //     console.error( error );
    // });
    // ClassicEditor.create( document.querySelector( '.row_spec' ) ).catch( error => {
    //     console.error( error );
    // });

   function initEditor(id, model) {
        const el = document.getElementById(id);
        if (!el || el.dataset.editorInitialized) return;

        el.dataset.editorInitialized = true;

        ClassicEditor.create(el)
            .then(editor => {
                editor.model.document.on('change:data', () => {
                    @this.set(model, editor.getData());
                });
            })
            .catch(console.error);
    }

    function initEditors() {
        initEditor('short_desc', 'short_desc');
        initEditor('desc', 'desc');
        initEditor('spec', 'specification');

        document.querySelectorAll('[id^="row_spec_"]').forEach(el => {
            const index = el.id.replace('row_spec_', '');
            initEditor(el.id, `rows.${index}.specification`);
        });
    }

    // initial load
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(initEditors, 300);
    });

    // Livewire v3 event
    document.addEventListener('init-editors', () => {
        setTimeout(initEditors, 300);
    });
</script>
<link rel="stylesheet" href="{{ asset('assets/custom_css/component-chosen.css') }}">
<script src="{{ asset('assets/js/chosen.jquery.js') }}"></script> -->
<script>
    var jq = $.noConflict();

    function initCollectionChosen() {
        const el = jq('#collection_id');
        if (!el.length) return;

        el.chosen({ width: "100%" });

        // SET SELECTED VALUE ON LOAD
        const selected = el.data('selected');
        if (selected) {
            el.val(selected).trigger('chosen:updated');
        }

        // Update Livewire when changed
        el.on('change', function () {
            @this.set('collection_id', jq(this).val());
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(initCollectionChosen, 300);
    });

    // Re-sync AFTER Livewire updates
    Livewire.hook('message.processed', () => {
        const val = @this.get('collection_id');
        if (val) {
            jq('#collection_id').val(val).trigger('chosen:updated');
        }
    });
</script>

@endsection

