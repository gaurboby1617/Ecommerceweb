


<?php include 'auth_check.php'; ?>
<?php 
include 'include/header.php';
include 'include/conn.php';
?>
<style>
    body { background-color: #f4f6f9; }
    .card { border: none; border-radius: 10px; }
    #imagePreview { object-fit: cover; }
    .table td, .table th { vertical-align: middle; }
</style>

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Category Management</h2>
        <button type="button" class="btn btn-primary" id="btnAddCategory">+ Add Category</button>
    </div>

    <div id="alertBox"></div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width:60px;">#</th>
                        <th style="width:100px;">Image</th>
                        <th>Name</th>
                        <th style="width:160px;">Action</th>
                    </tr>
                </thead>
                <tbody id="categoryList">
                    <!-- rows loaded via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add / Edit Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="categoryForm" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title" id="categoryModalTitle">Add Category</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="id" id="category_id">
            <input type="hidden" name="old_image" id="old_image">

            <div class="mb-3">
                <label class="form-label">Category Name</label>
                <input type="text" class="form-control" name="name" id="category_name" required>
                <div class="invalid-feedback" id="nameError"></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Category Image</label>
                <input type="file" class="form-control" name="image" id="category_image" accept="image/*">
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

<!-- Delete Confirm Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        Kya aap is category ko delete karna chahte hain? Ye action wapas nahi ho sakta.
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

    loadCategories();

    // ---------- Add button ----------
    $('#btnAddCategory').on('click', function () {
        resetForm();
        $('#categoryModalTitle').text('Add Category');
        $('#categoryModal').modal('show');
    });

    // ---------- List load ----------
    function loadCategories() {
        $.ajax({
            url: 'ajax.php',
            type: 'GET',
            data: { action: 'list' },
            dataType: 'json',
            success: function (response) {
                let rows = '';
                if (response.length > 0) {
                    $.each(response, function (i, cat) {
                        let img = cat.image
                            ? '<img src="uploads/' + cat.image + '" width="50" height="50" style="object-fit:cover;" class="rounded">'
                            : '<span class="text-muted">No image</span>';

                        rows += '<tr>' +
                                '<td>' + (i + 1) + '</td>' +
                                '<td>' + img + '</td>' +
                                '<td>' + escapeHtml(cat.name) + '</td>' +
                                '<td>' +
                                '<button class="btn btn-sm btn-warning btnEdit" data-id="' + cat.id + '">Edit</button> ' +
                                '<button class="btn btn-sm btn-danger btnDelete" data-id="' + cat.id + '">Delete</button>' +
                                '</td>' +
                                '</tr>';
                    });
                } else {
                    rows = '<tr><td colspan="4" class="text-center text-muted">No categories found</td></tr>';
                }
                $('#categoryList').html(rows);
            },
            error: function () {
                showAlert('danger', 'Categories load karne me error aaya.');
            }
        });
    }

    // ---------- Insert / Update ----------
    $('#categoryForm').on('submit', function (e) {
        e.preventDefault();
        clearErrors();

        let formData = new FormData(this);
        let id = $('#category_id').val();
        formData.append('action', id ? 'update' : 'insert');

        $('#btnSave').prop('disabled', true).text('Saving...');

        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    $('#categoryModal').modal('hide');
                    showAlert('success', response.message);
                    loadCategories();
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
            url: 'ajax.php',
            type: 'GET',
            data: { action: 'get', id: id },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    let cat = response.data;
                    $('#category_id').val(cat.id);
                    $('#old_image').val(cat.image);
                    $('#category_name').val(cat.name);
                    if (cat.image) {
                        $('#imagePreview').attr('src', 'uploads/' + cat.image).show();
                    }
                    $('#categoryModalTitle').text('Edit Category');
                    $('#categoryModal').modal('show');
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
    $('#category_image').on('change', function () {
        let file = this.files[0];
        if (file) {
            let reader = new FileReader();
            reader.onload = function (e) {
                $('#imagePreview').attr('src', e.target.result).show();
            };
            reader.readAsDataURL(file);
        }
    });

    // ---------- Delete ----------
    $(document).on('click', '.btnDelete', function () {
        $('#delete_id').val($(this).data('id'));
        $('#deleteModal').modal('show');
    });

    $('#btnConfirmDelete').on('click', function () {
        let id = $('#delete_id').val();
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            data: { action: 'delete', id: id },
            dataType: 'json',
            success: function (response) {
                $('#deleteModal').modal('hide');
                if (response.status === 'success') {
                    showAlert('success', response.message);
                    loadCategories();
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
        $('#categoryForm')[0].reset();
        $('#category_id').val('');
        $('#old_image').val('');
        $('#imagePreview').hide().attr('src', '');
        clearErrors();
    }

    function clearErrors() {
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
    }

    function showValidationErrors(errors) {
        $.each(errors, function (field, message) {
            $('#category_' + field).addClass('is-invalid');
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