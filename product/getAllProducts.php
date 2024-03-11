<?php

include '../db_connection.php';

// Get all products
function getAllProducts()
{
    global $conn;
    $sql = "SELECT * FROM `product`";
    $result = $conn->query($sql);
    $products = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    return $products;
}

// Endpoint to get all products
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getAllProducts') {
    $products = getAllProducts();
    echo json_encode($products);
}

?>