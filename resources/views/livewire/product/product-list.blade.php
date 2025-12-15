<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
          
        <!-- Product List Table -->
        <div class="card">
            <div class="card-datatable table-responsive">
                <div class="card-header border-bottom">
                    <h5 class="mb-0">Filter</h5>
                    <div class="d-flex justify-content-between align-items-center row pt-4 gap-4 gap-md-0">
                        <div class="col-md-4 product_status"></div>
                        <div class="col-md-4 product_category"></div>
                        <div class="col-md-4 product_stock"></div>
                    </div>
                    <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto mt-0">
                        <div class="dt-search mb-0 mb-md-5">
                            <input type="search" class="form-control form-control-sm ms-0" id="dt-search-0" placeholder="Search" aria-controls="DataTables_Table_0">
                            <label for="dt-search-0"></label>
                        </div>
                    </div>
                    <div class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto ms-auto gap-md-2 gap-0 mt-0">
                        <!-- <div class="dt-length">
                            <select name="DataTables_Table_0_length" aria-controls="DataTables_Table_0" class="form-select form-select-sm" id="dt-length-0">
                                <option value="7">7</option>
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select><label for="dt-length-0">

                            </label>
                        </div> -->
                        <div class="dt-buttons btn-group flex-wrap mb-md-0 mb-5"> 
                            <div class="btn-group">
                                <button class="btn buttons-collection btn-outline-secondary dropdown-toggle me-4 waves-effect" tabindex="0" aria-controls="DataTables_Table_0" type="button" aria-haspopup="dialog" aria-expanded="false">
                                    <span>
                                        <span class="d-flex align-items-center">
                                            <i class="icon-base ri ri-download-line icon-16px me-sm-2"></i> 
                                            <span class="d-none d-sm-inline-block">Export</span>
                                        </span>
                                    </span>
                                </button>
                            </div> 
                            <a href="{{ route('admin.product.add') }}" 
                                class="btn add-new btn-primary">
                                    <span>
                                        <i class="icon-base ri ri-add-line me-0 me-sm-1 icon-16px"></i>
                                        <span class="d-none d-sm-inline-block">Add Product</span>
                                    </span>
                                </a>

                        </div>
                    </div>
                </div>
            </div>

            <div class="card-datatable table-responsive">
                <table class="datatables-products table">
                    <thead>
                        <tr>
                        <th></th>
                        <th></th>
                        <th>product</th>
                        <th>category</th>
                        <th>stock</th>
                        <th>sku</th>
                        <th>price</th>
                        <th>qty</th>
                        <th>status</th>
                        <th>actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

</div>