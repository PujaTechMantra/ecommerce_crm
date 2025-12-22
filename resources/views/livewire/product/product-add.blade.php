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
            <h4>Add Product</h4>
            <div>
                <a href="{{route('admin.product.index')}}" class="btn btn-dark btn-sm me-2">Back</a>
                <button wire:click="save" class="btn btn-primary btn-sm">Publish Product</button>
            </div>
        </div>

        <div class="row">
            <!-- Left Column -->
            <div class="col-lg-8">

                <!-- Basic Product Info -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="form-floating mb-3">
                            <input type="text" wire:model.defer="title" class="form-control" placeholder="title">
                            <label class="required">Title</label>
                            @error('title') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" wire:model.defer="product_code" class="form-control" placeholder="Product Code">
                            <label class="required">Product Code</label>
                            @error('product_code') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <!-- Short Description -->
                        <div class="mb-3">
                            <label>Short Description</label>
                            <!-- <textarea wire:model.defer="short_desc" class="form-control" rows="3" id="short_desc"></textarea> -->
                            <div wire:ignore>
                                <textarea id="short_desc" class="form-control"></textarea>
                            </div>
                            @error('short_desc') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <!-- Full Description -->
                        <div class="mb-3">
                            <label>Description</label>
                            <!-- <textarea wire:model.defer="desc" class="form-control" rows="5" id="desc"></textarea> -->
                            <div wire:ignore>
                                <textarea id="desc" class="form-control"></textarea>
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
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="required">Main Image</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="w-100 product__thumb">
                            <label for="main_image">
                                <img id="main_image_preview" 
                                    src="{{ $image ? $image->temporaryUrl() : asset('assets/img/placeholder-product.jpg') }}" 
                                    class="img-fluid mb-3 border rounded" 
                                    style="width:100%; max-height:100px; object-fit:cover;"/>
                            </label>
                            @error('image') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                        <input type="file" id="main_image" accept="image/*" wire:model="image" class="d-none" onchange="previewImage(event, 'main_image_preview')">
                    </div>

                </div>

                <!-- Product Image -->
                <div class="card mb-4">
                    
                    <div class="card-body">

                        <div class="mb-3">
                            <label class="required">Collection</label>
                            <div wire:ignore>
                                <select id="collection_id" class="form-select">
                                    <option value="">Select</option>
                                    @foreach($collections as $collection)
                                        <option value="{{ $collection->id }}">{{ $collection->name }}</option>
                                    @endforeach
                                </select>
                            </div>
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
                    <label class="form-label required">Product Type</label>

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
                                id="single" value="single">
                            <label class="form-check-label" for="single">Single Product</label>
                        </div>
                         @error('product_type') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>


                <!-- Base Price for Single Product -->
                 @if($product_type === 'single')

                <div wire:key="single-block" class="mb-3">
                    <div class="border rounded p-3 mb-3 ">

                        <div class="row g-2">
                            <div class="col-md-5">
                                <label class="form-label required">Base Price</label>
                                <input type="number"
                                    wire:model.defer="base_price"
                                    class="form-control form-control-sm">
                                @error('base_price') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-5">
                                <label class="form-label required">Display Price</label>
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
                                    wire:model="single_image"
                                    multiple
                                    accept="image/*"
                                    class="form-control form-control-sm">
                                    @error('single_image.*') 
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                            </div>
                        </div>

                        <div class="row g-2">

                            <div class="col-md-12">
                                <label class="form-label">Specification</label>
                                <!-- <textarea wire:model="specification"
                                        rows="2"
                                        class="form-control form-control-sm" id="spec"></textarea> -->

                                <div wire:ignore>
                                    <textarea id="spec" class="form-control"></textarea>
                                </div>
                                    @error('specification') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>


                    </div>
                </div>
                @endif

                @if($product_type === 'variation')
                <div wire:key="variation-block" class="mb-3">

                    @foreach($rows as $index => $row)
                    <div class="border rounded p-3 mb-2 position-relative">

                        <!-- Row 1 -->
                        <div class="row g-2 mb-2">
                            <div class="col-md-2">
                                <label class="form-label required">Color</label>
                                <select wire:model="rows.{{ $index }}.color_id"
                                        class="form-select form-select-sm">
                                    <option value="">Select</option>
                                    @foreach($colors as $color)
                                        <option value="{{ $color->id }}">{{ $color->name }}</option>
                                    @endforeach
                                </select>
                                @error("rows.$index.color_id") <span class="text-danger">{{ $message }}</span> @enderror

                            </div>

                            <div class="col-md-2">
                                <label class="form-label required">Size</label>
                                <select wire:model="rows.{{ $index }}.size_id"
                                        class="form-select form-select-sm">
                                    <option value="">Select</option>
                                    @foreach($sizes as $size)
                                        <option value="{{ $size->id }}">{{ $size->name }}</option>
                                    @endforeach
                                </select>
                                @error("rows.$index.size_id") <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-2">
                                <label class="form-label required">Item Code</label>
                                <input type="text"
                                    wire:model="rows.{{ $index }}.item_code"
                                    class="form-control form-control-sm">
                                @error("rows.$index.item_code") <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label required">Base Price</label>
                                <input type="number"
                                    wire:model="rows.{{ $index }}.base_price"
                                    class="form-control form-control-sm">
                                @error("rows.$index.base_price") <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            

                            <div class="col-md-3">
                                <label class="form-label required">Display Price</label>
                                <input type="number"
                                    wire:model="rows.{{ $index }}.display_price"
                                    class="form-control form-control-sm">
                                @error("rows.$index.display_price") <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Row 2 -->
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label required">Image</label>
                                <input type="file"
                                    wire:model="rows.{{ $index }}.images"
                                    multiple
                                    accept="image/*"
                                    class="form-control form-control-sm">
                                @error("rows.$index.images*") 
                                    <span class="text-danger">{{ $message }}</span> 
                                @enderror
                            </div>
                        </div>

                        <div class="row g-2">

                            <div class="col-md-12">
                                <label class="form-label">Specification</label>
                                <!-- <textarea wire:model="rows.{{ $index }}.specification"
                                        rows="2"
                                        class="form-control form-control-sm row_spec"></textarea> -->

                                <div wire:ignore>
                                    <textarea id="row_spec_{{ $index }}" class="form-control"></textarea>
                                </div>
                                @error("rows.$index.specification") <span class="text-danger">{{ $message }}</span> @enderror
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
                            class="btn btn-success btn-sm mt-2">
                        + Add Variation
                    </button>
                </div>
                @endif
               
            </div>
        </div>
            <div class="d-flex justify-content-end">
                <button type="button"
                    wire:click="save"
                    class="btn btn-primary btn-sm mt-2">
                    Publish Product
                </button>
            </div>
    </div>
</div>
@section('page-script')
<script src="https://cdn.ckeditor.com/ckeditor5/41.2.0/classic/ckeditor.js"></script>
<script>
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
<script src="{{ asset('assets/js/chosen.jquery.js') }}"></script>
  <script>
    var jq = $.noConflict();

    jq("#collection_id").chosen({
        width: "100%"
    });

    jq("#collection_id").off('change').on('change', function () {
        const selected = jq(this).val();

        @this.set('collection_id', selected);
    });

    Livewire.hook('message.processed', () => {
        jq("#collection_id").trigger("chosen:updated");
    });
</script>
@endsection