<?php

include 'db_connection.php';

// Get all orders of user
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

// Endpoint to get orders of user
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getOrdersByUserId') {
    $userId = $_GET['userId'];
    $orders = getOrdersByUserId($userId);
    echo json_encode($orders);
}

// Add a new order
// function addOrder($userId, $productId, $quantity) {
//     global $conn;
//     $sql = "INSERT INTO `orders` (userId, productId, quantity) VALUES ($userId, $productId, $quantity)";
//     if ($conn->query($sql) === TRUE) {
//         echo "inside code";
//         return "New order added successfully";
//     } else {
//         return "Error: " . $sql . "<br>" . $conn->error;
//     }
// }

function addOrder($userId, $productId, $quantity)
{
    global $conn;
    $sql = "INSERT INTO orders (userId, productId, quantity) VALUES (?, ?, ?)";

    // Prepare the statement
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return "Error preparing statement: " . $conn->error;
    }

    // Bind parameters and execute the statement
    $stmt->bind_param("iii", $userId, $productId, $quantity);
    if ($stmt->execute()) {
        return "New order added successfully";
    } else {
        return "Error adding order: " . $stmt->error;
    }
}

// Endpoint to add a new order
// if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'addOrder') {
//     $userId = $_POST['userId'];
//     $productId = $_POST['productId'];
//     $quantity = $_POST['quantity'];
//     echo addOrder($userId, $productId, $quantity);
// }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $json_data = file_get_contents('php://input');

    // Decode JSON data
    $data = json_decode($json_data, true);
    // Get user inputs from the request body
    $userId = $data['userId'];
    $productId = $data['productId'];
    $quantity = $data['quantity'];

    // Check if all required fields are provided
    if ($userId !== null && $productId !== null && $quantity !== null) {
        $response = addOrder($userId, $productId, $quantity);
        echo json_encode(array("message" => $response));
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Incomplete data"));
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(array("message" => "Method not allowed"));
}
