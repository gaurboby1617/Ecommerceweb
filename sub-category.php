<?php include 'auth_check.php'; ?>
<?php

if (isset($_REQUEST['action'])) {
    header('Content-Type: application/json');
    $action = $_REQUEST['action'];

    switch ($action) {
        case 'categories':
            getCategoriesForDropdown($conn);
            break;
        case 'list':
            listSubCategories($conn);
            break;
        case 'get':
            getSubCategory($conn);
            break;
        case 'insert':
            insertSubCategory($conn);
            break;
        case 'update':
            updateSubCategory($conn);
            break;
        case 'delete':
            deleteSubCategory($conn);
            break;
        default:
            echo json_encode(["status" => "error", "message" => "Invalid action."]);
    }
    exit;
}

// ========================================================
// Functions (PHP me ye upar call ho sakte hain, koi issue nahi)
// ========================================================

function getCategoriesForDropdown($conn) {
    $result = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");

    if ($result === false) {
        echo json_encode(["status" => "error", "message" => "SQL Error: " . $conn->error]);
        return;
    }

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
}

function listSubCategories($conn) {
    $sql = "SELECT sc.id, sc.name, sc.image, sc.category_id, c.name AS category_name
            FROM sub_categories sc
            INNER JOIN categories c ON sc.category_id = c.id
            ORDER BY sc.id DESC";

    $result = $conn->query($sql);

    if ($result === false) {
        // Real DB error yahan aayega — console/network tab me dikhega
        echo json_encode([
            "status"  => "error",
            "message" => "SQL Error: " . $conn->error
        ]);
        return;
    }

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
}

function getSubCategory($conn) {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($id <= 0) {
        echo json_encode(["status" => "error", "message" => "Invalid sub category id."]);
        return;
    }

    $stmt = $conn->prepare("SELECT id, category_id, name, image FROM sub_categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(["status" => "success", "data" => $result->fetch_assoc()]);
    } else {
        echo json_encode(["status" => "error", "message" => "Sub category not found."]);
    }
    $stmt->close();
}

function validateInput($categoryId, $name) {
    $errors = [];
    if (empty($categoryId) || intval($categoryId) <= 0) {
        $errors['parent_category_id'] = "Please select a category.";
    }
    if (empty(trim($name))) {
        $errors['name'] = "Sub category name is required.";
    }
    return $errors;
}

function uploadImage($file) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $maxSize = 2 * 1024 * 1024; // 2MB

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        return ["error" => "Only jpg, jpeg, png, gif, webp images are allowed."];
    }
    if ($file['size'] > $maxSize) {
        return ["error" => "Image size must be less than 2MB."];
    }

    $newName   = uniqid('subcat_', true) . '.' . $ext;
    $uploadDir = 'uploads/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    if (move_uploaded_file($file['tmp_name'], $uploadDir . $newName)) {
        return ["success" => $newName];
    }
    return ["error" => "Image upload failed."];
}

function insertSubCategory($conn) {
    $categoryId = $_POST['category_id'] ?? '';
    $name       = $_POST['name'] ?? '';
    $errors     = validateInput($categoryId, $name);

    if (!empty($errors)) {
        echo json_encode(["status" => "validation_error", "errors" => $errors]);
        return;
    }

    $imageName = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload = uploadImage($_FILES['image']);
        if (isset($upload['error'])) {
            echo json_encode(["status" => "validation_error", "errors" => ["image" => $upload['error']]]);
            return;
        }
        $imageName = $upload['success'];
    }

    $categoryId = intval($categoryId);
    $stmt = $conn->prepare("INSERT INTO sub_categories (category_id, name, image) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $categoryId, $name, $imageName);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Sub category added successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Insert failed: " . $conn->error]);
    }
    $stmt->close();
}

function updateSubCategory($conn) {
    $id         = intval($_POST['id'] ?? 0);
    $categoryId = $_POST['category_id'] ?? '';
    $name       = $_POST['name'] ?? '';
    $oldImage   = $_POST['old_image'] ?? null;
    $errors     = validateInput($categoryId, $name);

    if ($id <= 0) {
        echo json_encode(["status" => "error", "message" => "Invalid sub category id."]);
        return;
    }

    if (!empty($errors)) {
        echo json_encode(["status" => "validation_error", "errors" => $errors]);
        return;
    }

    $imageName = $oldImage;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload = uploadImage($_FILES['image']);
        if (isset($upload['error'])) {
            echo json_encode(["status" => "validation_error", "errors" => ["image" => $upload['error']]]);
            return;
        }
        // purani image delete kar do
        if ($oldImage && file_exists('uploads/' . $oldImage)) {
            unlink('uploads/' . $oldImage);
        }
        $imageName = $upload['success'];
    }

    $categoryId = intval($categoryId);
    $stmt = $conn->prepare("UPDATE sub_categories SET category_id = ?, name = ?, image = ? WHERE id = ?");
    $stmt->bind_param("issi", $categoryId, $name, $imageName, $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Sub category updated successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Update failed: " . $conn->error]);
    }
    $stmt->close();
}

function deleteSubCategory($conn) {
    $id = intval($_POST['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(["status" => "error", "message" => "Invalid sub category id."]);
        return;
    }

    $stmt = $conn->prepare("SELECT image FROM sub_categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if ($row['image'] && file_exists('uploads/' . $row['image'])) {
            unlink('uploads/' . $row['image']);
        }
    }
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM sub_categories WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Sub category deleted successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Delete failed: " . $conn->error]);
    }
    $stmt->close();
}

// ========================================================
// Normal page load (no action param) — HTML render karo
// ========================================================
include 'include/header.php';
?>
<style>
    body { background-color: #f4f6f9; }
    .card { border: none; border-radius: 10px; }
    #subImagePreview { object-fit: cover; }
    .table td, .table th { vertical-align: middle; }
</style>

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Sub Category Management</h2>
        <button type="button" class="btn btn-primary" id="btnAddSubCategory">+ Add Sub Category</button>
    </div>

    <div id="alertBox"></div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width:60px;">#</th>
                        <th style="width:100px;">Image</th>
                        <th>Sub Category</th>
                        <th>Parent Category</th>
                        <th style="width:160px;">Action</th>
                    </tr>
                </thead>
                <tbody id="subCategoryList">
                    <!-- rows loaded via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add / Edit Modal -->
<div class="modal fade" id="subCategoryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="subCategoryForm" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title" id="subCategoryModalTitle">Add Sub Category</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="id" id="subcat_id">
            <input type="hidden" name="old_image" id="old_image">

            <div class="mb-3">
                <label class="form-label">Select Category</label>
                <select class="form-select" name="category_id" id="parent_category_id" required>
                    <option value="">-- Select Category --</option>
                </select>
                <div class="invalid-feedback" id="parent_category_idError"></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Sub Category Name</label>
                <input type="text" class="form-control" name="name" id="subcat_name" required>
                <div class="invalid-feedback" id="nameError"></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Sub Category Image</label>
                <input type="file" class="form-control" name="image" id="subcat_image" accept="image/*">
                <div class="invalid-feedback" id="imageError"></div>
                <div class="mt-2">
                    <img src="" id="subImagePreview" alt="preview" style="max-height:120px; display:none;" class="border rounded p-1">
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="btnSaveSubCategory">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Confirm Modal -->
<div class="modal fade" id="deleteSubCategoryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        Kya aap is sub category ko delete karna chahte hain? Ye action wapas nahi ho sakta.
        <input type="hidden" id="delete_subcat_id">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="btnConfirmDeleteSubCategory">Delete</button>
      </div>
    </div>
  </div>
</div>

<?php
include 'include/footer.php';
?>

<script>
$(document).ready(function () {

    loadCategoriesDropdown();
    loadSubCategories();

    // ---------- Add button ----------
    $('#btnAddSubCategory').on('click', function () {
        resetForm();
        $('#subCategoryModalTitle').text('Add Sub Category');
        $('#subCategoryModal').modal('show');
    });

    // ---------- Load parent category dropdown ----------
    function loadCategoriesDropdown() {
        $.ajax({
            url: 'sub-category.php',
            type: 'GET',
            data: { action: 'categories' },
            dataType: 'json',
            success: function (response) {
                let options = '<option value="">-- Select Category --</option>';
                $.each(response, function (i, cat) {
                    options += '<option value="' + cat.id + '">' + escapeHtml(cat.name) + '</option>';
                });
                $('#parent_category_id').html(options);
            },
            error: function () {
                showAlert('danger', 'Categories load karne me error aaya.');
            }
        });
    }

    // ---------- Load sub category list ----------
    function loadSubCategories() {
        $.ajax({
            url: 'sub-category.php',
            type: 'GET',
            data: { action: 'list' },
            dataType: 'json',
            success: function (response) {
                let rows = '';
                if (response.length > 0) {
                    $.each(response, function (i, sc) {
                        let img = sc.image
                            ? '<img src="uploads/' + sc.image + '" width="50" height="50" style="object-fit:cover;" class="rounded">'
                            : '<span class="text-muted">No image</span>';

                        rows += '<tr>' +
                                '<td>' + (i + 1) + '</td>' +
                                '<td>' + img + '</td>' +
                                '<td>' + escapeHtml(sc.name) + '</td>' +
                                '<td>' + escapeHtml(sc.category_name) + '</td>' +
                                '<td>' +
                                '<button class="btn btn-sm btn-warning btnEditSubCategory" data-id="' + sc.id + '">Edit</button> ' +
                                '<button class="btn btn-sm btn-danger btnDeleteSubCategory" data-id="' + sc.id + '">Delete</button>' +
                                '</td>' +
                                '</tr>';
                    });
                } else {
                    rows = '<tr><td colspan="5" class="text-center text-muted">No sub categories found</td></tr>';
                }
                $('#subCategoryList').html(rows);
            },
            error: function () {
                showAlert('danger', 'Sub categories load karne me error aaya.');
            }
        });
    }

    // ---------- Insert / Update ----------
    $('#subCategoryForm').on('submit', function (e) {
        e.preventDefault();
        clearErrors();

        let formData = new FormData(this);
        let id = $('#subcat_id').val();
        formData.append('action', id ? 'update' : 'insert');

        $('#btnSaveSubCategory').prop('disabled', true).text('Saving...');

        $.ajax({
            url: 'sub-category.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    $('#subCategoryModal').modal('hide');
                    showAlert('success', response.message);
                    loadSubCategories();
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
                $('#btnSaveSubCategory').prop('disabled', false).text('Save');
            }
        });
    });

    // ---------- Edit ----------
    $(document).on('click', '.btnEditSubCategory', function () {
        let id = $(this).data('id');
        resetForm();

        $.ajax({
            url: 'sub-category.php',
            type: 'GET',
            data: { action: 'get', id: id },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    let sc = response.data;
                    $('#subcat_id').val(sc.id);
                    $('#old_image').val(sc.image);
                    $('#parent_category_id').val(sc.category_id);
                    $('#subcat_name').val(sc.name);
                    if (sc.image) {
                        $('#subImagePreview').attr('src', 'uploads/' + sc.image).show();
                    }
                    $('#subCategoryModalTitle').text('Edit Sub Category');
                    $('#subCategoryModal').modal('show');
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
    $('#subcat_image').on('change', function () {
        let file = this.files[0];
        if (file) {
            let reader = new FileReader();
            reader.onload = function (e) {
                $('#subImagePreview').attr('src', e.target.result).show();
            };
            reader.readAsDataURL(file);
        }
    });

    // ---------- Delete ----------
    $(document).on('click', '.btnDeleteSubCategory', function () {
        $('#delete_subcat_id').val($(this).data('id'));
        $('#deleteSubCategoryModal').modal('show');
    });

    $('#btnConfirmDeleteSubCategory').on('click', function () {
        let id = $('#delete_subcat_id').val();
        $.ajax({
            url: 'sub-category.php',
            type: 'POST',
            data: { action: 'delete', id: id },
            dataType: 'json',
            success: function (response) {
                $('#deleteSubCategoryModal').modal('hide');
                if (response.status === 'success') {
                    showAlert('success', response.message);
                    loadSubCategories();
                } else {
                    showAlert('danger', response.message);
                }
            },
            error: function () {
                $('#deleteSubCategoryModal').modal('hide');
                showAlert('danger', 'Server error. Please try again.');
            }
        });
    });

    // ---------- Helpers ----------
    function resetForm() {
        $('#subCategoryForm')[0].reset();
        $('#subcat_id').val('');
        $('#old_image').val('');
        $('#subImagePreview').hide().attr('src', '');
        clearErrors();
    }

    function clearErrors() {
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
    }

    function showValidationErrors(errors) {
        $.each(errors, function (field, message) {
            $('#' + field).addClass('is-invalid');
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