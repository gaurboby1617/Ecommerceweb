<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');
include 'include/conn.php';

$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'list':
        getProductList();
        break;
    case 'get':
        getProduct();
        break;
    case 'get_categories':
        getCategories();
        break;
    case 'get_subcategories':
        getSubCategories();
        break;
    case 'get_brands':
        getBrands();
        break;
    case 'insert':
        insertProduct();
        break;
    case 'update':
        updateProduct();
        break;
    case 'delete':
        deleteProduct();
        break;
    case 'toggle_status':
        toggleStatus();
        break;
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}

// ---------------------------------------------------------
function getProductList() {
    global $conn;
    $sql = "SELECT p.id, p.name, p.image, p.price, p.quantity, p.rating, p.status,
                   c.name AS category_name,
                   sc.name AS sub_category_name,
                   br.name AS brand_name
            FROM products p
            LEFT JOIN categories c ON c.id = p.category_id
            LEFT JOIN sub_categories sc ON sc.id = p.sub_category_id
            LEFT JOIN brand br ON br.id = p.brand_id
            ORDER BY p.id DESC";
    $result = mysqli_query($conn, $sql);
    $data = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    } else {
        $data = ['debug_error' => mysqli_error($conn), 'debug_sql' => $sql];
    }
    echo json_encode($data);
}

// ---------------------------------------------------------
function getProduct() {
    global $conn;
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $sql = "SELECT * FROM products WHERE id = $id LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        $row['category_name'] = getNameById('categories', $row['category_id']);
        $row['sub_category_name'] = getNameById('sub_categories', $row['sub_category_id']);
        $row['brand_name'] = getNameById('brand', $row['brand_id']);
        echo json_encode(['status' => 'success', 'data' => $row]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Product not found']);
    }
}

function getNameById($table, $id) {
    global $conn;
    $id = (int)$id;
    // table name comes from a fixed whitelist below, never from user input
    $allowed = ['categories', 'sub_categories', 'brand'];
    if (!in_array($table, $allowed)) return '';
    $result = mysqli_query($conn, "SELECT name FROM $table WHERE id = $id LIMIT 1");
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
        $data = ['debug_error' => mysqli_error($conn), 'debug_sql' => $sql];
    }
    echo json_encode($data);
}

// ---------------------------------------------------------
function getBrands() {
    global $conn;
    $category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
    $sub_category_id = isset($_GET['sub_category_id']) ? (int)$_GET['sub_category_id'] : 0;
    $sql = "SELECT id, name FROM brand
            WHERE category_id = $category_id AND sub_category_id = $sub_category_id
            ORDER BY name ASC";
    $result = mysqli_query($conn, $sql);
    $data = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    } else {
        $data = ['debug_error' => mysqli_error($conn), 'debug_sql' => $sql];
    }
    echo json_encode($data);
}

// ---------------------------------------------------------
function validateProductInput($isUpdate = false) {
    $errors = [];

    $name = trim($_POST['name'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $sub_category_id = (int)($_POST['sub_category_id'] ?? 0);
    $brand_id = (int)($_POST['brand_id'] ?? 0);
    $price = $_POST['price'] ?? '';
    $quantity = $_POST['quantity'] ?? '';
    $rating = $_POST['rating'] ?? '0';

    if ($name === '') {
        $errors['name'] = 'Product name is required.';
    } elseif (strlen($name) > 200) {
        $errors['name'] = 'Product name must be under 200 characters.';
    }

    if ($category_id <= 0) {
        $errors['category_id'] = 'Please select a category.';
    }

    if ($sub_category_id <= 0) {
        $errors['sub_category_id'] = 'Please select a sub category.';
    }

    if ($brand_id <= 0) {
        $errors['brand_id'] = 'Please select a brand.';
    }

    if ($price === '' || !is_numeric($price) || (float)$price < 0) {
        $errors['price'] = 'Enter a valid price.';
    }

    if ($quantity === '' || !is_numeric($quantity) || (int)$quantity < 0) {
        $errors['quantity'] = 'Enter a valid quantity.';
    }

    if ($rating !== '' && (!is_numeric($rating) || (float)$rating < 0 || (float)$rating > 5)) {
        $errors['rating'] = 'Rating must be between 0 and 5.';
    }

    return $errors;
}

// ---------------------------------------------------------
function insertProduct() {
    global $conn;
    $errors = validateProductInput();

    $imageName = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $imageName = handleImageUpload($_FILES['image'], $errors);
    } else {
        $errors['image'] = 'Product image is required.';
    }

    if (!empty($errors)) {
        echo json_encode(['status' => 'validation_error', 'errors' => $errors]);
        return;
    }

    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $category_id = (int)$_POST['category_id'];
    $sub_category_id = (int)$_POST['sub_category_id'];
    $brand_id = (int)$_POST['brand_id'];
    $price = (float)$_POST['price'];
    $quantity = (int)$_POST['quantity'];
    $rating = (float)($_POST['rating'] ?? 0);
    $features = mysqli_real_escape_string($conn, trim($_POST['features'] ?? ''));
    $status = isset($_POST['status']) ? (int)$_POST['status'] : 1;
    $image_esc = mysqli_real_escape_string($conn, $imageName);

    $sql = "INSERT INTO products
            (category_id, sub_category_id, brand_id, name, image, price, quantity, rating, features, status, created_at)
            VALUES
            ($category_id, $sub_category_id, $brand_id, '$name', '$image_esc', $price, $quantity, $rating, '$features', $status, NOW())";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(['status' => 'success', 'message' => 'Product added successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . mysqli_error($conn)]);
    }
}

// ---------------------------------------------------------
function updateProduct() {
    global $conn;
    $id = (int)($_POST['id'] ?? 0);
    $old_image = $_POST['old_image'] ?? '';

    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid product id.']);
        return;
    }

    $errors = validateProductInput(true);

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

    if ($imageName !== $old_image && $old_image !== '' && file_exists(__DIR__ . '/uploads/' . $old_image)) {
        @unlink(__DIR__ . '/uploads/' . $old_image);
    }

    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $category_id = (int)$_POST['category_id'];
    $sub_category_id = (int)$_POST['sub_category_id'];
    $brand_id = (int)$_POST['brand_id'];
    $price = (float)$_POST['price'];
    $quantity = (int)$_POST['quantity'];
    $rating = (float)($_POST['rating'] ?? 0);
    $features = mysqli_real_escape_string($conn, trim($_POST['features'] ?? ''));
    $status = isset($_POST['status']) ? (int)$_POST['status'] : 1;
    $image_esc = mysqli_real_escape_string($conn, $imageName);

    $sql = "UPDATE products SET
                category_id = $category_id,
                sub_category_id = $sub_category_id,
                brand_id = $brand_id,
                name = '$name',
                image = '$image_esc',
                price = $price,
                quantity = $quantity,
                rating = $rating,
                features = '$features',
                status = $status
            WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(['status' => 'success', 'message' => 'Product updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . mysqli_error($conn)]);
    }
}

// ---------------------------------------------------------
function deleteProduct() {
    global $conn;
    $id = (int)($_POST['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid product id.']);
        return;
    }

    $result = mysqli_query($conn, "SELECT image FROM products WHERE id = $id");
    $row = $result ? mysqli_fetch_assoc($result) : null;

    if ($row && !empty($row['image']) && file_exists(__DIR__ . '/uploads/' . $row['image'])) {
        @unlink(__DIR__ . '/uploads/' . $row['image']);
    }

    if (mysqli_query($conn, "DELETE FROM products WHERE id = $id")) {
        echo json_encode(['status' => 'success', 'message' => 'Product deleted successfully.']);
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

    if (mysqli_query($conn, "UPDATE products SET status = $newStatus WHERE id = $id")) {
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
    $newName = 'product_' . time() . '_' . uniqid() . '.' . $ext;
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