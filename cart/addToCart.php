<?php
include '../db_connection.php';

// Add a new item to the cart
function addToCart($userId, $productId, $quantity) {
    global $conn;
    $sql = "INSERT INTO cart (userId, productId, quantity) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $userId, $productId, $quantity);
    
    if ($stmt->execute()) {
        return "Item added to cart successfully";
    } else {
        return "Error adding item to cart: " . $stmt->error;
    }
}

// Endpoint to add a new item to the cart
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);

    $userId = $data['userId'];
    $productId = $data['productId'];
    $quantity = $data['quantity'];

    if ($userId !== null && $productId !== null && $quantity !== null) {
        echo addToCart($userId, $productId, $quantity);
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Incomplete data"));
    } exit;
}


?>