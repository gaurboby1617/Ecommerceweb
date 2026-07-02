<?php
// get_products.php
// Rakho: EcomWebProject/get_products.php (indexx.php/prdct.php ke sath)
// Optional query params: ?cat_id=2&subcat_id=4
// Sirf active products (status = 1) return karta hai, category/sub_category/brand
// naam bhi JOIN karke saath deta hai.

include 'conn.php';

header('Content-Type: application/json');

$cat_id    = isset($_GET['cat_id']) && $_GET['cat_id'] !== '' ? intval($_GET['cat_id']) : 0;
$subcat_id = isset($_GET['subcat_id']) && $_GET['subcat_id'] !== '' ? intval($_GET['subcat_id']) : 0;

$sql = "SELECT p.id, p.name, p.price, p.quantity, p.rating, p.image, p.features, p.status,
               p.category_id, p.sub_category_id, p.brand_id,
               c.name  AS category_name,
               sc.name AS sub_category_name,
               b.name  AS brand_name
        FROM products p
        LEFT JOIN categories     c  ON p.category_id = c.id
        LEFT JOIN sub_categories sc ON p.sub_category_id = sc.id
        LEFT JOIN brand          b  ON p.brand_id = b.id
        WHERE p.status = 1";

$params = [];
$types  = "";

if ($cat_id > 0) {
    $sql .= " AND p.category_id = ?";
    $params[] = $cat_id;
    $types   .= "i";
}
if ($subcat_id > 0) {
    $sql .= " AND p.sub_category_id = ?";
    $params[] = $subcat_id;
    $types   .= "i";
}

$sql .= " ORDER BY p.id DESC";

$stmt = mysqli_prepare($conn, $sql);
if ($params) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$data = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = [
            'id'              => (int)$row['id'],
            'name'            => $row['name'],
            'price'           => (float)$row['price'],
            'quantity'        => (int)$row['quantity'],
            'rating'          => round((float)$row['rating']), // stars ke liye whole number
            'image'           => $row['image'] ? '../admin/uploads/' . $row['image'] : '',
            'desc'            => $row['features'],
            'category_id'     => $row['category_id'] !== null ? (int)$row['category_id'] : null,
            'category'        => $row['category_name'],
            'sub_category_id' => $row['sub_category_id'] !== null ? (int)$row['sub_category_id'] : null,
            'sub_category'    => $row['sub_category_name'],
            'brand'           => $row['brand_name']
        ];
    }
} else {
    error_log('get_products.php query error: ' . mysqli_error($conn));
}

echo json_encode($data);
?>