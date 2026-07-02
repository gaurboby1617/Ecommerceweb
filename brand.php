

<?php include 'auth_check.php'; ?>
<?php 
include 'include/header.php';
include 'include/conn.php';
?>
<style>
    body { background-color: #f4f6f9; }
    .card { border: none; border-radius: 10px; }
    #imagePreview, #viewLogo { object-fit: cover; }
    .table td, .table th { vertical-align: middle; }
    .status-toggle { cursor: pointer; }
</style>

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Brand Management</h2>
        <button type="button" class="btn btn-primary" id="btnAddBrand">+ Add Brand</button>
    </div>

    <div id="alertBox"></div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width:60px;">#</th>
                        <th style="width:100px;">Logo</th>
                        <th>Brand Name</th>
                        <th>Category</th>
                        <th>Sub Category</th>
                        <th style="width:100px;">Status</th>
                        <th style="width:220px;">Action</th>
                    </tr>
                </thead>
                <tbody id="brandList">
                    <!-- rows loaded via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add / Edit Modal -->
<div class="modal fade" id="brandModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="brandForm" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title" id="brandModalTitle">Add Brand</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="id" id="brand_id">
            <input type="hidden" name="old_image" id="old_image">

            <div class="mb-3">
                <label class="form-label">Brand Name</label>
                <input type="text" class="form-control" name="name" id="brand_name" required>
                <div class="invalid-feedback" id="nameError"></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Category</label>
                <select class="form-select" name="category_id" id="brand_category_id" required>
                    <option value="">-- Select Category --</option>
                </select>
                <div class="invalid-feedback" id="category_idError"></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Sub Category</label>
                <select class="form-select" name="sub_category_id" id="brand_sub_category_id" required>
                    <option value="">-- Select Category First --</option>
                </select>
                <div class="invalid-feedback" id="sub_category_idError"></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Status</label>
                <select class="form-select" name="status" id="brand_status">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Brand Logo</label>
                <input type="file" class="form-control" name="image" id="brand_image" accept="image/*">
                <div class="invalid-feedback" id="imageError"></div>
                <div class="mt-2">
                    <img src="" id="imagePreview" alt="preview" style="max-height:120px; display:none;" class="border rounded p-1">
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
        <h5 class="modal-title">Brand Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="text-center mb-3">
            <img src="" id="viewLogo" style="max-height:150px; display:none;" class="border rounded p-1">
        </div>
        <table class="table table-borderless mb-0">
            <tr><th style="width:140px;">Brand Name</th><td id="viewName"></td></tr>
            <tr><th>Category</th><td id="viewCategory"></td></tr>
            <tr><th>Sub Category</th><td id="viewSubCategory"></td></tr>
            <tr><th>Status</th><td id="viewStatus"></td></tr>
        </table>
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
        Kya aap is brand ko delete karna chahte hain? Ye action wapas nahi ho sakta.
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
    loadBrands();

    // ---------- Add button ----------
    $('#btnAddBrand').on('click', function () {
        resetForm();
        $('#brandModalTitle').text('Add Brand');
        $('#brandModal').modal('show');
    });

    // ---------- List load ----------
    function loadBrands() {
        $.ajax({
            url: 'ajax_brand.php',
            type: 'GET',
            data: { action: 'list' },
            dataType: 'json',
            success: function (response) {
                let rows = '';
                if (response.length > 0) {
                    $.each(response, function (i, b) {
                        let img = b.image
                            ? '<img src="uploads/' + b.image + '" width="50" height="50" style="object-fit:cover;" class="rounded">'
                            : '<span class="text-muted">No image</span>';

                        let statusBadge = b.status == 1
                            ? '<span class="badge bg-success status-toggle" data-id="' + b.id + '" data-status="1">Active</span>'
                            : '<span class="badge bg-secondary status-toggle" data-id="' + b.id + '" data-status="0">Inactive</span>';

                        rows += '<tr>' +
                                '<td>' + (i + 1) + '</td>' +
                                '<td>' + img + '</td>' +
                                '<td>' + escapeHtml(b.name) + '</td>' +
                                '<td>' + escapeHtml(b.category_name || '-') + '</td>' +
                                '<td>' + escapeHtml(b.sub_category_name || '-') + '</td>' +
                                '<td>' + statusBadge + '</td>' +
                                '<td>' +
                                '<button class="btn btn-sm btn-info btnView" data-id="' + b.id + '">View</button> ' +
                                '<button class="btn btn-sm btn-warning btnEdit" data-id="' + b.id + '">Edit</button> ' +
                                '<button class="btn btn-sm btn-danger btnDelete" data-id="' + b.id + '">Delete</button>' +
                                '</td>' +
                                '</tr>';
                    });
                } else {
                    rows = '<tr><td colspan="7" class="text-center text-muted">No brands found</td></tr>';
                }
                $('#brandList').html(rows);
            },
            error: function () {
                showAlert('danger', 'Brands load karne me error aaya.');
            }
        });
    }

    // ---------- Load Category dropdown ----------
    function loadCategoriesDropdown(selectedId) {
        $.ajax({
            url: 'ajax_brand.php',
            type: 'GET',
            data: { action: 'get_categories' },
            dataType: 'json',
            success: function (response) {
                let options = '<option value="">-- Select Category --</option>';
                $.each(response, function (i, cat) {
                    options += '<option value="' + cat.id + '">' + escapeHtml(cat.name) + '</option>';
                });
                $('#brand_category_id').html(options);
                if (selectedId) {
                    $('#brand_category_id').val(selectedId);
                }
            }
        });
    }

    // ---------- Load Sub Category dropdown (based on category) ----------
    function loadSubCategoriesDropdown(categoryId, selectedSubId) {
        if (!categoryId) {
            $('#brand_sub_category_id').html('<option value="">-- Select Category First --</option>');
            return;
        }
        $.ajax({
            url: 'ajax_brand.php',
            type: 'GET',
            data: { action: 'get_subcategories', category_id: categoryId },
            dataType: 'json',
            success: function (response) {
                let options = '<option value="">-- Select Sub Category --</option>';
                $.each(response, function (i, sub) {
                    options += '<option value="' + sub.id + '">' + escapeHtml(sub.name) + '</option>';
                });
                $('#brand_sub_category_id').html(options);
                if (selectedSubId) {
                    $('#brand_sub_category_id').val(selectedSubId);
                }
            }
        });
    }

    // ---------- Category change -> reload subcategory dropdown ----------
    $('#brand_category_id').on('change', function () {
        loadSubCategoriesDropdown($(this).val());
    });

    // ---------- Insert / Update ----------
    $('#brandForm').on('submit', function (e) {
        e.preventDefault();
        clearErrors();

        let formData = new FormData(this);
        let id = $('#brand_id').val();
        formData.append('action', id ? 'update' : 'insert');

        $('#btnSave').prop('disabled', true).text('Saving...');

        $.ajax({
            url: 'ajax_brand.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    $('#brandModal').modal('hide');
                    showAlert('success', response.message);
                    loadBrands();
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
            url: 'ajax_brand.php',
            type: 'GET',
            data: { action: 'get', id: id },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    let b = response.data;
                    $('#brand_id').val(b.id);
                    $('#old_image').val(b.image);
                    $('#brand_name').val(b.name);
                    $('#brand_status').val(b.status);

                    loadCategoriesDropdown(b.category_id);
                    loadSubCategoriesDropdown(b.category_id, b.sub_category_id);

                    if (b.image) {
                        $('#imagePreview').attr('src', 'uploads/' + b.image).show();
                    }
                    $('#brandModalTitle').text('Edit Brand');
                    $('#brandModal').modal('show');
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
            url: 'ajax_brand.php',
            type: 'GET',
            data: { action: 'get', id: id },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    let b = response.data;
                    $('#viewName').text(b.name);
                    $('#viewCategory').text(b.category_name || '-');
                    $('#viewSubCategory').text(b.sub_category_name || '-');
                    $('#viewStatus').html(b.status == 1
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-secondary">Inactive</span>');

                    if (b.image) {
                        $('#viewLogo').attr('src', 'uploads/' + b.image).show();
                    } else {
                        $('#viewLogo').hide();
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
    $('#brand_image').on('change', function () {
        let file = this.files[0];
        if (file) {
            let reader = new FileReader();
            reader.onload = function (e) {
                $('#imagePreview').attr('src', e.target.result).show();
            };
            reader.readAsDataURL(file);
        }
    });

    // ---------- Status toggle (click badge in list) ----------
    $(document).on('click', '.status-toggle', function () {
        let id = $(this).data('id');
        let status = $(this).data('status');

        $.ajax({
            url: 'ajax_brand.php',
            type: 'POST',
            data: { action: 'toggle_status', id: id, status: status },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    loadBrands();
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
            url: 'ajax_brand.php',
            type: 'POST',
            data: { action: 'delete', id: id },
            dataType: 'json',
            success: function (response) {
                $('#deleteModal').modal('hide');
                if (response.status === 'success') {
                    showAlert('success', response.message);
                    loadBrands();
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
        $('#brandForm')[0].reset();
        $('#brand_id').val('');
        $('#old_image').val('');
        $('#imagePreview').hide().attr('src', '');
        $('#brand_sub_category_id').html('<option value="">-- Select Category First --</option>');
        clearErrors();
    }

    function clearErrors() {
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
    }

    function showValidationErrors(errors) {
        $.each(errors, function (field, message) {
            $('#brand_' + field).addClass('is-invalid');
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