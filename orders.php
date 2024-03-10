<?php

include 'db_connection.php';

// Get all orders of user
function getOrdersByUserID($userId) {
    global $conn;
    $sql = "SELECT * FROM `orders` WHERE userId = $userId";
    $result = $conn->query($sql);
    $orders = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
    }
    return $orders;
}

// Endpoint to get orders of user
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getOrdersByUserId') {
    $userId = $_GET['userId'];
    $orders = getOrdersByUserId($userId);
    echo json_encode($orders);
}

// Add a new order
function addOrder($userId, $productId, $quantity) {
    global $conn;
    $sql = "INSERT INTO `orders` (userId, productId, quantity) VALUES ($userId, $productId, $quantity)";
    if ($conn->query($sql) === TRUE) {
        return "New order added successfully";
    } else {
        return "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Endpoint to add a new order
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'addOrder') {
    $userId = $_POST['userId'];
    $productId = $_POST['productId'];
    $quantity = $_POST['quantity'];
    echo addOrder($userId, $productId, $quantity);
}

?>
