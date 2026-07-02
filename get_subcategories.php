<?php
// get_subcategories.php
// Rakho: EcomWebProject/get_subcategories.php (indexx.php/prdct.php ke sath)
// Usage: get_subcategories.php?category_id=3
// Diye gaye category_id ki sub-categories JSON me return karta hai.

include 'conn.php';

header('Content-Type: application/json');

$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

$data = [];

if ($category_id > 0) {
    $sql = "SELECT id, category_id, name, image FROM sub_categories WHERE category_id = ? ORDER BY id ASC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $category_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = [
            'id'          => $row['id'],
            'category_id' => $row['category_id'],
            'name'        => $row['name'],
            // sub-category images bhi admin/uploads me save hoti hain
            'image'       => $row['image'] ? '../admin/uploads/' . $row['image'] : null
        ];
    }
    mysqli_stmt_close($stmt);
}

echo json_encode($data);
?>