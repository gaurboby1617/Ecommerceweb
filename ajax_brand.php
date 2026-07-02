<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');
include 'include/conn.php';

$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'list':
        getBrandList();
        break;
    case 'get':
        getBrand();
        break;
    case 'get_categories':
        getCategories();
        break;
    case 'get_subcategories':
        getSubCategories();
        break;
    case 'insert':
        insertBrand();
        break;
    case 'update':
        updateBrand();
        break;
    case 'delete':
        deleteBrand();
        break;
    case 'toggle_status':
        toggleStatus();
        break;
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}

// ---------------------------------------------------------
function getBrandList() {
    global $conn;
    $sql = "SELECT b.id, b.name, b.image, b.status,
                   c.name AS category_name,
                   sc.name AS sub_category_name
            FROM brand b
            LEFT JOIN categories c ON c.id = b.category_id
            LEFT JOIN sub_categories sc ON sc.id = b.sub_category_id
            ORDER BY b.id DESC";
    $result = mysqli_query($conn, $sql);
    $data = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    }
    echo json_encode($data);
}

// ---------------------------------------------------------
function getBrand() {
    global $conn;
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $sql = "SELECT * FROM brand WHERE id = $id LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        // also attach readable names for the View modal
        $row['category_name'] = getCategoryName($row['category_id']);
        $row['sub_category_name'] = getSubCategoryName($row['sub_category_id']);
        echo json_encode(['status' => 'success', 'data' => $row]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Brand not found']);
    }
}

function getCategoryName($id) {
    global $conn;
    $id = (int)$id;
    $result = mysqli_query($conn, "SELECT name FROM categories WHERE id = $id LIMIT 1");
    $row = $result ? mysqli_fetch_assoc($result) : null;
    return $row ? $row['name'] : '';
}

function getSubCategoryName($id) {
    global $conn;
    $id = (int)$id;
    $result = mysqli_query($conn, "SELECT name FROM sub_categories WHERE id = $id LIMIT 1");
    $row = $result ? mysqli_fetch_assoc($result) : null;
    return $row ? $row['name'] : '';
}

// ---------------------------------------------------------
function getCategories() {
    global $conn;
    $sql = "SELECT id, name FROM categories ORDER BY name ASC";
    $result = mysqli_query($conn, $sql);
    $data = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    } else {
        // DEBUG: remove once everything is confirmed working
        $data = ['debug_error' => mysqli_error($conn), 'debug_sql' => $sql];
    }
    echo json_encode($data);
}

// ---------------------------------------------------------
function getSubCategories() {
    global $conn;
    $category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
    $sql = "SELECT id, name FROM sub_categories WHERE category_id = $category_id ORDER BY name ASC";
    $result = mysqli_query($conn, $sql);
    $data = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    } else {
        // DEBUG: remove once everything is confirmed working
        $data = ['debug_error' => mysqli_error($conn), 'debug_sql' => $sql];
    }
    echo json_encode($data);
}

// ---------------------------------------------------------
function insertBrand() {
    global $conn;
    $errors = [];

    $name = trim($_POST['name'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $sub_category_id = (int)($_POST['sub_category_id'] ?? 0);
    $status = isset($_POST['status']) ? (int)$_POST['status'] : 1;

    if ($name === '') {
        $errors['name'] = 'Brand name is required.';
    } elseif (strlen($name) > 150) {
        $errors['name'] = 'Brand name must be under 150 characters.';
    }

    if ($category_id <= 0) {
        $errors['category_id'] = 'Please select a category.';
    }

    if ($sub_category_id <= 0) {
        $errors['sub_category_id'] = 'Please select a sub category.';
    }

    $imageName = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $imageName = handleImageUpload($_FILES['image'], $errors);
    } else {
        $errors['image'] = 'Brand logo is required.';
    }

    if (!empty($errors)) {
        echo json_encode(['status' => 'validation_error', 'errors' => $errors]);
        return;
    }

    $name_esc = mysqli_real_escape_string($conn, $name);
    $image_esc = mysqli_real_escape_string($conn, $imageName);

    $sql = "INSERT INTO brand (name, category_id, sub_category_id, image, status, created_at)
            VALUES ('$name_esc', $category_id, $sub_category_id, '$image_esc', $status, NOW())";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(['status' => 'success', 'message' => 'Brand added successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . mysqli_error($conn)]);
    }
}

// ---------------------------------------------------------
function updateBrand() {
    global $conn;
    $errors = [];

    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $sub_category_id = (int)($_POST['sub_category_id'] ?? 0);
    $status = isset($_POST['status']) ? (int)$_POST['status'] : 1;
    $old_image = $_POST['old_image'] ?? '';

    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid brand id.']);
        return;
    }

    if ($name === '') {
        $errors['name'] = 'Brand name is required.';
    } elseif (strlen($name) > 150) {
        $errors['name'] = 'Brand name must be under 150 characters.';
    }

    if ($category_id <= 0) {
        $errors['category_id'] = 'Please select a category.';
    }

    if ($sub_category_id <= 0) {
        $errors['sub_category_id'] = 'Please select a sub category.';
    }

    $imageName = $old_image;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploaded = handleImageUpload($_FILES['image'], $errors);
        if ($uploaded) {
            $imageName = $uploaded;
        }
    }

    if (!empty($errors)) {
        echo json_encode(['status' => 'validation_error', 'errors' => $errors]);
        return;
    }

    // remove old image only if a new one was uploaded
    if ($imageName !== $old_image && $old_image !== '' && file_exists(__DIR__ . '/uploads/' . $old_image)) {
        @unlink(__DIR__ . '/uploads/' . $old_image);
    }

    $name_esc = mysqli_real_escape_string($conn, $name);
    $image_esc = mysqli_real_escape_string($conn, $imageName);

    $sql = "UPDATE brand SET
                name = '$name_esc',
                category_id = $category_id,
                sub_category_id = $sub_category_id,
                image = '$image_esc',
                status = $status
            WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(['status' => 'success', 'message' => 'Brand updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . mysqli_error($conn)]);
    }
}

// ---------------------------------------------------------
function deleteBrand() {
    global $conn;
    $id = (int)($_POST['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid brand id.']);
        return;
    }

    $result = mysqli_query($conn, "SELECT image FROM brand WHERE id = $id");
    $row = $result ? mysqli_fetch_assoc($result) : null;

    if ($row && !empty($row['image']) && file_exists(__DIR__ . '/uploads/' . $row['image'])) {
        @unlink(__DIR__ . '/uploads/' . $row['image']);
    }

    if (mysqli_query($conn, "DELETE FROM brand WHERE id = $id")) {
        echo json_encode(['status' => 'success', 'message' => 'Brand deleted successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . mysqli_error($conn)]);
    }
}

// ---------------------------------------------------------
function toggleStatus() {
    global $conn;
    $id = (int)($_POST['id'] ?? 0);
    $currentStatus = (int)($_POST['status'] ?? 0);
    $newStatus = $currentStatus == 1 ? 0 : 1;

    if (mysqli_query($conn, "UPDATE brand SET status = $newStatus WHERE id = $id")) {
        echo json_encode(['status' => 'success', 'message' => 'Status updated.', 'new_status' => $newStatus]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . mysqli_error($conn)]);
    }
}

// ---------------------------------------------------------
function handleImageUpload($file, &$errors) {
    $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $maxSize = 2 * 1024 * 1024; // 2MB

    if (!in_array($file['type'], $allowed)) {
        $errors['image'] = 'Only JPG, PNG, WEBP, GIF images are allowed.';
        return null;
    }

    if ($file['size'] > $maxSize) {
        $errors['image'] = 'Image size must be under 2MB.';
        return null;
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $newName = 'brand_' . time() . '_' . uniqid() . '.' . $ext;
    $uploadDir = __DIR__ . '/uploads/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    if (!move_uploaded_file($file['tmp_name'], $uploadDir . $newName)) {
        $errors['image'] = 'Failed to upload image.';
        return null;
    }

    return $newName;
}