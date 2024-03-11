<?php

include '../db_connection.php';

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

?>