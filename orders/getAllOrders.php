<?php

include '../db_connection.php';

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
