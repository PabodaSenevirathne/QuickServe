<?php

include 'db_connection.php';

// Get all items in the cart of a user
function getCartItemsByUserId($userId) {
    global $conn;
    $sql = "SELECT * FROM cart WHERE userId = $userId";
    $result = $conn->query($sql);
    $cartItems = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $cartItems[] = $row;
        }
    }
    return $cartItems;
}

// Endpoint to get items in the cart for a user
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getCartItemsByUserId') {
    $userId = $_GET['userId'];
    $cartItems = getCartItemsByUserId($userId);
    echo json_encode($cartItems);
}

// Add a new item to the cart
function addToCart($userId, $productId, $quantity) {
    global $conn;
    $sql = "INSERT INTO cart (userId, productId, quantity) VALUES ($userId, $productId, $quantity)";
    if ($conn->query($sql) === TRUE) {
        return "Item added to cart successfully";
    } else {
        return "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Endpoint to add a new item to the cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'addToCart') {
    $userId = $_POST['userId'];
    $productId = $_POST['productId'];
    $quantity = $_POST['quantity'];
    echo addToCart($userId, $productId, $quantity);
}

?>
