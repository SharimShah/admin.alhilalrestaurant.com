@extends('layouts.app')
@section('title')
    All Products
@endsection
@section('content')
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>All Products</h3>
                <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                    <li>
                        <a href={{ route('home') }}>
                            <div class="text-tiny">Dashboard</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <div class="text-tiny">All Products</div>
                    </li>
                </ul>
            </div>

            <div class="wg-box">
                <div class="flex items-center justify-between gap10 flex-wrap">
                    <a class="tf-button style-1 w208" href={{ route('products.create') }}><i class="icon-plus"></i>Add
                        new</a>
                </div>
                <div class="flex items-center justify-between gap10 flex-wrap">
                    <select id="categoryFilter" class="form-control">
                        <option value="">All Categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="wg-table table-all-user">
                    <!-- Search Inputs Above Table -->
                    <div class="row mb-3 custom-search-input">
                        <div class="col-md-2">
                            <input type="text" id="search_id" class="form-control column-search" placeholder="Search ID">
                        </div>
                        <div class="col-md-2">
                            <input type="text" id="search_name" class="form-control column-search"
                                placeholder="Search Name">
                        </div>
                        <div class="col-md-2">
                            <input type="text" id="search_slug" class="form-control column-search"
                                placeholder="Search Slug">
                        </div>
                        <div class="col-md-2">
                            <select id="search_active" class="form-control column-search">
                                <option value="">All Status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select id="search_featured" class="form-control column-search">
                                <option value="">All Feature</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select id="search_hidden" class="form-control column-search">
                                <option value="">Out Of Stock</option>
                                <option value="1">Active</option>
                                <option value="0">No Active</option>
                            </select>
                        </div>
                    </div>

                    <!-- DataTable -->
                    <table class="custom-table-style my-5" id="products_datable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Active Products</th>
                                <th>Feature Products</th>
                                {{-- <th>Free Delivery</th> --}}
                                <th>Stock</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="divider"></div>
                <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">

                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        var table = $('#products_datable').DataTable({
            processing: true,
            serverSide: true,
            dom: 'lrtip',
            // searching: false,
            ajax: {
                url: "{{ route('products.getproducts') }}",
                data: function(d) {
                    d.category_id = $('#categoryFilter').val();
                }
            },
            columns: [{
                    data: 'id',
                    name: 'id',
                    width: '20px'
                },
                {
                    data: 'image',
                    name: 'image',
                    width: '40px'
                },
                {
                    data: 'name',
                    name: 'name',
                    width: '40px'
                },
                {
                    data: 'slug',
                    name: 'slug',
                    width: '40px',
                    render: function(data, type, row) {
                        return `<a target="_blank" href="https://alhilalrestaurant.com/product/${data}">${data}</a>`;
                    }
                },
                {
                    data: 'active_product',
                    name: 'active_product',
                    width: '40px',
                    render: function(data, type, row) {
                        let btnClass = data == 1 ? 'btn-success' : 'btn-danger';
                        let btnText = data == 1 ? 'Active' : 'Inactive';
                        return `<button class="btn btn-sm toggle-status ${btnClass}" 
                            data-id="${row.id}" data-column="active_product">${btnText}</button>`;
                    }
                },
                {
                    data: 'feature_product',
                    name: 'feature_product',
                    width: '40px',
                    render: function(data, type, row) {
                        let btnClass = data == 1 ? 'btn-success' : 'btn-danger';
                        let btnText = data == 1 ? 'Featured' : 'Not Featured';
                        return `<button class="btn btn-sm toggle-status ${btnClass}" 
                            data-id="${row.id}" data-column="feature_product">${btnText}</button>`;
                    }
                },
                {
                    data: 'stock',
                    name: 'stock',
                    width: '40px',
                    render: function(data, type, row) {
                        let btnClass = data == 0 ? 'btn-success' : 'btn-danger';
                        let btnText = data == 0 ? 'In Stock' : 'Out of Stock';
                        return `<button class="btn btn-sm toggle-status ${btnClass}" 
                            data-id="${row.id}" data-column="stock">${btnText}</button>`;
                    }
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    width: '40px'
                }
            ]
        });

        // Trigger table reload on dropdown change
        $('#categoryFilter').on('change', function() {
            table.draw();
        });

        // Apply column search when typing or selecting from dropdown
        $('.column-search').on('keyup change', function() {
            let columnId = $(this).attr('id');
            let columnIndex;

            // Match input field with DataTable column index
            switch (columnId) {
                case 'search_id':
                    columnIndex = 0;
                    break;
                case 'search_name':
                    columnIndex = 2;
                    break;
                case 'search_slug':
                    columnIndex = 3;
                    break;
                case 'search_active':
                    columnIndex = 4;
                    break;
                case 'search_featured':
                    columnIndex = 5;
                    break;
                case 'search_hidden':
                    columnIndex = 7;
                    break;
                default:
                    return;
            }

            // Apply search value to the column
            table.column(columnIndex).search(this.value).draw();
        });

        // Universal toggle for active_product, feature_product, and stock
        $('#products_datable').on('click', '.toggle-status', function() {
            var button = $(this);
            var productId = button.data('id');
            var column = button.data('column'); // which column we are toggling

            $.ajax({
                url: "{{ route('products.toggleStatus') }}", // universal route
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: productId,
                    column: column
                },
                success: function(response) {
                    if (response.success) {
                        table.ajax.reload(null, false); // reload row
                    }
                }
            });
        });
    </script>
@endsection
