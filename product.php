<?php

include 'db_connection.php';

// Get all products
function getAllProducts() {
    global $conn;
    $sql = "SELECT * FROM product";
    $result = $conn->query($sql);
    $products = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
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

// Add a new product
function addProduct($description, $image, $price, $shippingCost) {
    global $conn;
    $sql = "INSERT INTO product (description, image, price, shippingCost) VALUES ('$description', '$image', '$price', '$shippingCost')"; // Changed table name and column names
    if ($conn->query($sql) === TRUE) {
        return "New product added successfully";
    } else {
        return "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Endpoint to add a new product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'addProduct') {
    $description = $_POST['description'];
    $image = $_POST['image'];
    $price = $_POST['price'];
    $shippingCost = $_POST['shippingCost']; 
    echo addProduct($description, $image, $price, $shippingCost);
}

?>