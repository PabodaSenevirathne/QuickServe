<?php

include 'db_connection.php';

// Get all orders by user id
function getOrdersByUserID($userId)
{
    global $conn;
    $sql = "SELECT * FROM `orders` WHERE userId = $userId";
    $result = $conn->query($sql);
    $orders = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
    }
    return $orders;
}

// Endpoint to get orders of user by user id
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getOrdersByUserId') {
    $userId = $_GET['userId'];
    $orders = getOrdersByUserId($userId);
    echo json_encode($orders);
}

// Add a new order
function addOrder($userId, $productId, $quantity)
{
    global $conn;
    $sql = "INSERT INTO orders (userId, productId, quantity) VALUES (?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return "Error preparing statement: " . $conn->error;
    }

    $stmt->bind_param("iii", $userId, $productId, $quantity);
    if ($stmt->execute()) {
        return "Order added successfully";
    } else {
        return "Error adding order: " . $stmt->error;
    }
}

// Endpoint to add a new order
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $json_data = file_get_contents('php://input');

    // Decode JSON data
    $data = json_decode($json_data, true);
    $userId = $data['userId'];
    $productId = $data['productId'];
    $quantity = $data['quantity'];

    if ($userId !== null && $productId !== null && $quantity !== null) {
        $response = addOrder($userId, $productId, $quantity);
        echo json_encode(array("message" => $response));
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Incomplete data"));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Method not allowed"));
}

// Function to get all orders
function getAllOrders() {
    global $conn;
    $sql = "SELECT * FROM `orders`";
    $result = $conn->query($sql);
    $orders = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
    }
    return $orders;
}

// Endpoint to get all orders
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getAllOrders') {
    $orders = getAllOrders();
    echo json_encode($orders);
}

?>


