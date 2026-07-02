
<?php include 'auth_check.php'; ?>
<?php 
include 'include/header.php';
include 'include/conn.php';
?>
<style>
    body { background-color: #f4f6f9; }
    .card { border: none; border-radius: 10px; }
    #imagePreview, #viewImage { object-fit: cover; }
    .table td, .table th { vertical-align: middle; }
    .status-toggle { cursor: pointer; }
</style>

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Product Management</h2>
        <button type="button" class="btn btn-primary" id="btnAddProduct">+ Add Product</button>
    </div>

    <div id="alertBox"></div>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width:50px;">#</th>
                        <th style="width:90px;">Image</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Sub Category</th>
                        <th>Brand</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Rating</th>
                        <th style="width:90px;">Status</th>
                        <th style="width:220px;">Action</th>
                    </tr>
                </thead>
                <tbody id="productList">
                    <!-- rows loaded via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add / Edit Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="productForm" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title" id="productModalTitle">Add Product</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="id" id="product_id">
            <input type="hidden" name="old_image" id="old_image">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Product Name</label>
                    <input type="text" class="form-control" name="name" id="product_name" required>
                    <div class="invalid-feedback" id="nameError"></div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Category</label>
                    <select class="form-select" name="category_id" id="product_category_id" required>
                        <option value="">-- Select Category --</option>
                    </select>
                    <div class="invalid-feedback" id="category_idError"></div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Sub Category</label>
                    <select class="form-select" name="sub_category_id" id="product_sub_category_id" required>
                        <option value="">-- Select Category First --</option>
                    </select>
                    <div class="invalid-feedback" id="sub_category_idError"></div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Brand</label>
                    <select class="form-select" name="brand_id" id="product_brand_id" required>
                        <option value="">-- Select Sub Category First --</option>
                    </select>
                    <div class="invalid-feedback" id="brand_idError"></div>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Price</label>
                    <input type="number" step="0.01" min="0" class="form-control" name="price" id="product_price" required>
                    <div class="invalid-feedback" id="priceError"></div>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" step="1" min="0" class="form-control" name="quantity" id="product_quantity" required>
                    <div class="invalid-feedback" id="quantityError"></div>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Rating (0-5)</label>
                    <input type="number" step="0.1" min="0" max="5" class="form-control" name="rating" id="product_rating" value="0">
                    <div class="invalid-feedback" id="ratingError"></div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status" id="product_status">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Product Image</label>
                    <input type="file" class="form-control" name="image" id="product_image" accept="image/*">
                    <div class="invalid-feedback" id="imageError"></div>
                    <div class="mt-2">
                        <img src="" id="imagePreview" alt="preview" style="max-height:100px; display:none;" class="border rounded p-1">
                    </div>
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label">Features <small class="text-muted">(one per line)</small></label>
                    <textarea class="form-control" name="features" id="product_features" rows="4" placeholder="e.g.&#10;6GB RAM&#10;128GB Storage&#10;5000mAh Battery"></textarea>
                    <div class="invalid-feedback" id="featuresError"></div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="btnSave">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Product Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="text-center mb-3">
            <img src="" id="viewImage" style="max-height:150px; display:none;" class="border rounded p-1">
        </div>
        <table class="table table-borderless mb-2">
            <tr><th style="width:140px;">Product Name</th><td id="viewName"></td></tr>
            <tr><th>Category</th><td id="viewCategory"></td></tr>
            <tr><th>Sub Category</th><td id="viewSubCategory"></td></tr>
            <tr><th>Brand</th><td id="viewBrand"></td></tr>
            <tr><th>Price</th><td id="viewPrice"></td></tr>
            <tr><th>Quantity</th><td id="viewQuantity"></td></tr>
            <tr><th>Rating</th><td id="viewRating"></td></tr>
            <tr><th>Status</th><td id="viewStatus"></td></tr>
        </table>
        <strong>Features:</strong>
        <div id="viewFeatures" class="mt-1"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Delete Confirm Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        Kya aap is product ko delete karna chahte hain? Ye action wapas nahi ho sakta.
        <input type="hidden" id="delete_id">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="btnConfirmDelete">Delete</button>
      </div>
    </div>
  </div>
</div>


<script>
$(document).ready(function () {

    loadCategoriesDropdown();
    loadProducts();

    // ---------- Add button ----------
    $('#btnAddProduct').on('click', function () {
        resetForm();
        $('#productModalTitle').text('Add Product');
        $('#productModal').modal('show');
    });

    // ---------- List load ----------
    function loadProducts() {
        $.ajax({
            url: 'ajax_product.php',
            type: 'GET',
            data: { action: 'list' },
            dataType: 'json',
            success: function (response) {
                let rows = '';
                if (response.length > 0) {
                    $.each(response, function (i, p) {
                        let img = p.image
                            ? '<img src="uploads/' + p.image + '" width="50" height="50" style="object-fit:cover;" class="rounded">'
                            : '<span class="text-muted">No image</span>';

                        let statusBadge = p.status == 1
                            ? '<span class="badge bg-success status-toggle" data-id="' + p.id + '" data-status="1">Active</span>'
                            : '<span class="badge bg-secondary status-toggle" data-id="' + p.id + '" data-status="0">Inactive</span>';

                        rows += '<tr>' +
                                '<td>' + (i + 1) + '</td>' +
                                '<td>' + img + '</td>' +
                                '<td>' + escapeHtml(p.name) + '</td>' +
                                '<td>' + escapeHtml(p.category_name || '-') + '</td>' +
                                '<td>' + escapeHtml(p.sub_category_name || '-') + '</td>' +
                                '<td>' + escapeHtml(p.brand_name || '-') + '</td>' +
                                '<td>' + parseFloat(p.price).toFixed(2) + '</td>' +
                                '<td>' + p.quantity + '</td>' +
                                '<td>' + parseFloat(p.rating).toFixed(1) + ' ⭐</td>' +
                                '<td>' + statusBadge + '</td>' +
                                '<td>' +
                                '<button class="btn btn-sm btn-info btnView" data-id="' + p.id + '">View</button> ' +
                                '<button class="btn btn-sm btn-warning btnEdit" data-id="' + p.id + '">Edit</button> ' +
                                '<button class="btn btn-sm btn-danger btnDelete" data-id="' + p.id + '">Delete</button>' +
                                '</td>' +
                                '</tr>';
                    });
                } else {
                    rows = '<tr><td colspan="11" class="text-center text-muted">No products found</td></tr>';
                }
                $('#productList').html(rows);
            },
            error: function () {
                showAlert('danger', 'Products load karne me error aaya.');
            }
        });
    }

    // ---------- Load Category dropdown ----------
    function loadCategoriesDropdown(selectedId) {
        $.ajax({
            url: 'ajax_product.php',
            type: 'GET',
            data: { action: 'get_categories' },
            dataType: 'json',
            success: function (response) {
                let options = '<option value="">-- Select Category --</option>';
                $.each(response, function (i, cat) {
                    options += '<option value="' + cat.id + '">' + escapeHtml(cat.name) + '</option>';
                });
                $('#product_category_id').html(options);
                if (selectedId) {
                    $('#product_category_id').val(selectedId);
                }
            }
        });
    }

    // ---------- Load Sub Category dropdown ----------
    function loadSubCategoriesDropdown(categoryId, selectedSubId) {
        if (!categoryId) {
            $('#product_sub_category_id').html('<option value="">-- Select Category First --</option>');
            return;
        }
        $.ajax({
            url: 'ajax_product.php',
            type: 'GET',
            data: { action: 'get_subcategories', category_id: categoryId },
            dataType: 'json',
            success: function (response) {
                let options = '<option value="">-- Select Sub Category --</option>';
                $.each(response, function (i, sub) {
                    options += '<option value="' + sub.id + '">' + escapeHtml(sub.name) + '</option>';
                });
                $('#product_sub_category_id').html(options);
                if (selectedSubId) {
                    $('#product_sub_category_id').val(selectedSubId);
                }
            }
        });
    }

    // ---------- Load Brand dropdown (filtered by category + subcategory) ----------
    function loadBrandsDropdown(categoryId, subCategoryId, selectedBrandId) {
        if (!categoryId || !subCategoryId) {
            $('#product_brand_id').html('<option value="">-- Select Sub Category First --</option>');
            return;
        }
        $.ajax({
            url: 'ajax_product.php',
            type: 'GET',
            data: { action: 'get_brands', category_id: categoryId, sub_category_id: subCategoryId },
            dataType: 'json',
            success: function (response) {
                let options = '<option value="">-- Select Brand --</option>';
                $.each(response, function (i, b) {
                    options += '<option value="' + b.id + '">' + escapeHtml(b.name) + '</option>';
                });
                $('#product_brand_id').html(options);
                if (selectedBrandId) {
                    $('#product_brand_id').val(selectedBrandId);
                }
            }
        });
    }

    // ---------- Cascading changes ----------
    $('#product_category_id').on('change', function () {
        $('#product_brand_id').html('<option value="">-- Select Sub Category First --</option>');
        loadSubCategoriesDropdown($(this).val());
    });

    $('#product_sub_category_id').on('change', function () {
        let categoryId = $('#product_category_id').val();
        loadBrandsDropdown(categoryId, $(this).val());
    });

    // ---------- Insert / Update ----------
    $('#productForm').on('submit', function (e) {
        e.preventDefault();
        clearErrors();

        let formData = new FormData(this);
        let id = $('#product_id').val();
        formData.append('action', id ? 'update' : 'insert');

        $('#btnSave').prop('disabled', true).text('Saving...');

        $.ajax({
            url: 'ajax_product.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    $('#productModal').modal('hide');
                    showAlert('success', response.message);
                    loadProducts();
                } else if (response.status === 'validation_error') {
                    showValidationErrors(response.errors);
                } else {
                    showAlert('danger', response.message);
                }
            },
            error: function () {
                showAlert('danger', 'Server error. Please try again.');
            },
            complete: function () {
                $('#btnSave').prop('disabled', false).text('Save');
            }
        });
    });

    // ---------- Edit ----------
    $(document).on('click', '.btnEdit', function () {
        let id = $(this).data('id');
        resetForm();

        $.ajax({
            url: 'ajax_product.php',
            type: 'GET',
            data: { action: 'get', id: id },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    let p = response.data;
                    $('#product_id').val(p.id);
                    $('#old_image').val(p.image);
                    $('#product_name').val(p.name);
                    $('#product_price').val(p.price);
                    $('#product_quantity').val(p.quantity);
                    $('#product_rating').val(p.rating);
                    $('#product_features').val(p.features);
                    $('#product_status').val(p.status);

                    loadCategoriesDropdown(p.category_id);
                    loadSubCategoriesDropdown(p.category_id, p.sub_category_id);
                    loadBrandsDropdown(p.category_id, p.sub_category_id, p.brand_id);

                    if (p.image) {
                        $('#imagePreview').attr('src', 'uploads/' + p.image).show();
                    }
                    $('#productModalTitle').text('Edit Product');
                    $('#productModal').modal('show');
                } else {
                    showAlert('danger', response.message);
                }
            },
            error: function () {
                showAlert('danger', 'Record load karne me error aaya.');
            }
        });
    });

    // ---------- View ----------
    $(document).on('click', '.btnView', function () {
        let id = $(this).data('id');

        $.ajax({
            url: 'ajax_product.php',
            type: 'GET',
            data: { action: 'get', id: id },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    let p = response.data;
                    $('#viewName').text(p.name);
                    $('#viewCategory').text(p.category_name || '-');
                    $('#viewSubCategory').text(p.sub_category_name || '-');
                    $('#viewBrand').text(p.brand_name || '-');
                    $('#viewPrice').text(parseFloat(p.price).toFixed(2));
                    $('#viewQuantity').text(p.quantity);
                    $('#viewRating').text(parseFloat(p.rating).toFixed(1) + ' ⭐');
                    $('#viewStatus').html(p.status == 1
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-secondary">Inactive</span>');

                    let featuresHtml = '<span class="text-muted">No features added</span>';
                    if (p.features && p.features.trim() !== '') {
                        let lines = p.features.split('\n').filter(f => f.trim() !== '');
                        featuresHtml = '<ul class="mb-0">' +
                            lines.map(f => '<li>' + escapeHtml(f) + '</li>').join('') +
                            '</ul>';
                    }
                    $('#viewFeatures').html(featuresHtml);

                    if (p.image) {
                        $('#viewImage').attr('src', 'uploads/' + p.image).show();
                    } else {
                        $('#viewImage').hide();
                    }
                    $('#viewModal').modal('show');
                } else {
                    showAlert('danger', response.message);
                }
            },
            error: function () {
                showAlert('danger', 'Record load karne me error aaya.');
            }
        });
    });

    // ---------- Image preview ----------
    $('#product_image').on('change', function () {
        let file = this.files[0];
        if (file) {
            let reader = new FileReader();
            reader.onload = function (e) {
                $('#imagePreview').attr('src', e.target.result).show();
            };
            reader.readAsDataURL(file);
        }
    });

    // ---------- Status toggle ----------
    $(document).on('click', '.status-toggle', function () {
        let id = $(this).data('id');
        let status = $(this).data('status');

        $.ajax({
            url: 'ajax_product.php',
            type: 'POST',
            data: { action: 'toggle_status', id: id, status: status },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    loadProducts();
                } else {
                    showAlert('danger', response.message);
                }
            },
            error: function () {
                showAlert('danger', 'Status update karne me error aaya.');
            }
        });
    });

    // ---------- Delete ----------
    $(document).on('click', '.btnDelete', function () {
        $('#delete_id').val($(this).data('id'));
        $('#deleteModal').modal('show');
    });

    $('#btnConfirmDelete').on('click', function () {
        let id = $('#delete_id').val();
        $.ajax({
            url: 'ajax_product.php',
            type: 'POST',
            data: { action: 'delete', id: id },
            dataType: 'json',
            success: function (response) {
                $('#deleteModal').modal('hide');
                if (response.status === 'success') {
                    showAlert('success', response.message);
                    loadProducts();
                } else {
                    showAlert('danger', response.message);
                }
            },
            error: function () {
                $('#deleteModal').modal('hide');
                showAlert('danger', 'Server error. Please try again.');
            }
        });
    });

    // ---------- Helpers ----------
    function resetForm() {
        $('#productForm')[0].reset();
        $('#product_id').val('');
        $('#old_image').val('');
        $('#imagePreview').hide().attr('src', '');
        $('#product_sub_category_id').html('<option value="">-- Select Category First --</option>');
        $('#product_brand_id').html('<option value="">-- Select Sub Category First --</option>');
        clearErrors();
    }

    function clearErrors() {
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
    }

    function showValidationErrors(errors) {
        $.each(errors, function (field, message) {
            $('#product_' + field).addClass('is-invalid');
            $('#' + field + 'Error').text(message);
        });
    }

    function showAlert(type, message) {
        let alertHtml = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
            message +
            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
            '</div>';
        $('#alertBox').html(alertHtml);
        setTimeout(function () { $('.alert').alert('close'); }, 3000);
    }

    function escapeHtml(text) {
        let div = document.createElement('div');
        div.innerText = text;
        return div.innerHTML;
    }
});
</script>
<?php 
include 'include/footer.php';
?>