<?php
// get_categories.php
// Yahan rakho: EcomWebProject/get_categories.php (indexx.php ke sath, same folder)
// Admin panel ("Category Management") me jo categories add ki gayi hain,
// wo yahan se JSON format me frontend ko milti hain.

// EcomWebProject ka apna conn.php (database: ecommerce)
include 'conn.php';

header('Content-Type: application/json');

$sql = "SELECT id, name, image, created_at FROM categories ORDER BY id ASC";
$result = mysqli_query($conn, $sql);

$data = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = [
            'id'         => $row['id'],
            'name'       => $row['name'],
            // admin folder EcomWebProject ka sibling hai (uske andar nahi),
            // isliye "../admin/uploads/" path use karna hoga
            'image'      => $row['image'] ? '../admin/uploads/' . $row['image'] : null,
            'created_at' => $row['created_at']
        ];
    }
} else {
    error_log('get_categories.php query error: ' . mysqli_error($conn));
}

echo json_encode($data);
?>