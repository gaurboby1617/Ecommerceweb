<?php

header('Content-Type: application/json');
include 'include/conn.php';   // <-- ye $conn variable deta hai (aapke project ke hisaab se)

$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'list':
        listCategories($conn);
        break;
    case 'get':
        getCategory($conn);
        break;
    case 'insert':
        insertCategory($conn);
        break;
    case 'update':
        updateCategory($conn);
        break;
    case 'delete':
        deleteCategory($conn);
        break;
    default:
        echo json_encode(["status" => "error", "message" => "Invalid action."]);
}

// ===================== Functions =====================

function listCategories($conn) {
    $result = $conn->query("SELECT id, name, image FROM categories ORDER BY id DESC");
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
}

function getCategory($conn) {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($id <= 0) {
        echo json_encode(["status" => "error", "message" => "Invalid category id."]);
        return;
    }

    $stmt = $conn->prepare("SELECT id, name, image FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(["status" => "success", "data" => $result->fetch_assoc()]);
    } else {
        echo json_encode(["status" => "error", "message" => "Category not found."]);
    }
    $stmt->close();
}

function validateInput($name) {
    $errors = [];
    if (empty(trim($name))) {
        $errors['name'] = "Category name is required.";
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

    $newName   = uniqid('cat_', true) . '.' . $ext;
    $uploadDir = 'uploads/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    if (move_uploaded_file($file['tmp_name'], $uploadDir . $newName)) {
        return ["success" => $newName];
    }
    return ["error" => "Image upload failed."];
}

function insertCategory($conn) {
    $name   = $_POST['name'] ?? '';
    $errors = validateInput($name);

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

    $stmt = $conn->prepare("INSERT INTO categories (name, image) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $imageName);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Category added successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Insert failed: " . $conn->error]);
    }
    $stmt->close();
}

function updateCategory($conn) {
    $id       = intval($_POST['id'] ?? 0);
    $name     = $_POST['name'] ?? '';
    $oldImage = $_POST['old_image'] ?? null;
    $errors   = validateInput($name);

    if ($id <= 0) {
        echo json_encode(["status" => "error", "message" => "Invalid category id."]);
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

    $stmt = $conn->prepare("UPDATE categories SET name = ?, image = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $imageName, $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Category updated successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Update failed: " . $conn->error]);
    }
    $stmt->close();
}

function deleteCategory($conn) {
    $id = intval($_POST['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(["status" => "error", "message" => "Invalid category id."]);
        return;
    }

    $stmt = $conn->prepare("SELECT image FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if ($row['image'] && file_exists('uploads/' . $row['image'])) {
            unlink('uploads/' . $row['image']);
        }
    }
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Category deleted successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Delete failed: " . $conn->error]);
    }
    $stmt->close();
}
?>