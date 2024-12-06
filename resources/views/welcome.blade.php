<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product List</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-3">
<div class="container">
    <h1>Add Product</h1>
    <form id="productForm" class="row g-3 mb-4">
        <div class="col-md-4">
            <label for="product_name" class="form-label">Product name</label>
            <input type="text" class="form-control" id="product_name" name="product_name" required>
        </div>
        <div class="col-md-4">
            <label for="quantity_in_stock" class="form-label">Quantity in stock</label>
            <input type="number" class="form-control" id="quantity_in_stock" name="quantity_in_stock" required min="0">
        </div>
        <div class="col-md-4">
            <label for="price_per_item" class="form-label">Price per item</label>
            <input type="number" step="0.01" class="form-control" id="price_per_item" name="price_per_item" required min="0">
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>

    <div class="table-container mt-5">
        <table class="table table-bordered" id="productTable">
            <thead class="table-dark">
            <tr>
                <th>Product name</th>
                <th>Quantity in stock</th>
                <th>Price per item</th>
                <th>Datetime submitted</th>
                <th>Total value number</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @php
                $sumTotalValue = 0;
            @endphp
            @foreach($data as $item)
                @php
                    $totalValue = $item['quantity_in_stock'] * $item['price_per_item'];
                    $sumTotalValue += $totalValue;
                @endphp
                <tr data-id="{{ $item['id'] }}">
                    <td>{{ $item['product_name'] }}</td>
                    <td>{{ $item['quantity_in_stock'] }}</td>
                    <td>{{ $item['price_per_item'] }}</td>
                    <td>{{ $item['datetime_submitted'] }}</td>
                    <td>{{ $totalValue }}</td>
                    <td>
                        <button class="btn btn-sm btn-secondary edit-btn">Edit</button>
                    </td>
                </tr>
            @endforeach
            <tr class="table-info">
                <td colspan="4" class="text-end"><strong>Total:</strong></td>
                <td colspan="2" id="sumTotal">{{ $sumTotalValue }}</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editForm" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="edit_id" />
                <div class="mb-3">
                    <label for="edit_product_name" class="form-label">Product name</label>
                    <input type="text" class="form-control" id="edit_product_name" name="product_name" required>
                </div>
                <div class="mb-3">
                    <label for="edit_quantity_in_stock" class="form-label">Quantity in stock</label>
                    <input type="number" class="form-control" id="edit_quantity_in_stock" name="quantity_in_stock" required min="0">
                </div>
                <div class="mb-3">
                    <label for="edit_price_per_item" class="form-label">Price per item</label>
                    <input type="number" step="0.01" class="form-control" id="edit_price_per_item" name="price_per_item" required min="0">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(function(){
        var editModal = new bootstrap.Modal(document.getElementById('editModal'), {
            keyboard: false
        });

        function renderTable(data) {
            var tbody = $('#productTable tbody');
            tbody.empty();
            var sumTotal = 0;
            data.forEach(function(item) {
                var total = item.quantity_in_stock * item.price_per_item;
                sumTotal += total;
                var row = '<tr data-id="'+item.id+'">'+
                    '<td>'+item.product_name+'</td>'+
                    '<td>'+item.quantity_in_stock+'</td>'+
                    '<td>'+item.price_per_item+'</td>'+
                    '<td>'+item.datetime_submitted+'</td>'+
                    '<td>'+total+'</td>'+
                    '<td><button class="btn btn-sm btn-secondary edit-btn">Edit</button></td>'+
                    '</tr>';
                tbody.append(row);
            });
            var totalRow = '<tr class="table-info">'+
                '<td colspan="4" class="text-end"><strong>Total:</strong></td>'+
                '<td colspan="2" id="sumTotal">'+sumTotal+'</td>'+
                '</tr>';
            tbody.append(totalRow);
        }

        $('#productForm').on('submit', function(e){
            e.preventDefault();
            $.ajax({
                url: '/save',
                type: 'POST',
                data: $(this).serialize(),
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function(res) {
                    if(res.success) {
                        renderTable(res.data);
                        $('#productForm')[0].reset();
                    }
                }
            });
        });

        $(document).on('click', '.edit-btn', function(){
            var tr = $(this).closest('tr');
            var id = tr.data('id');
            var product_name = tr.find('td:nth-child(1)').text();
            var quantity_in_stock = tr.find('td:nth-child(2)').text();
            var price_per_item = tr.find('td:nth-child(3)').text();

            $('#edit_id').val(id);
            $('#edit_product_name').val(product_name);
            $('#edit_quantity_in_stock').val(quantity_in_stock);
            $('#edit_price_per_item').val(price_per_item);

            editModal.show();
        });

        $('#editForm').on('submit', function(e){
            e.preventDefault();
            $.ajax({
                url: '/update',
                type: 'POST',
                data: $(this).serialize(),
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function(res) {
                    if(res.success) {
                        renderTable(res.data);
                        editModal.hide();
                    }
                }
            });
        });
    });
</script>
</body>
</html>
