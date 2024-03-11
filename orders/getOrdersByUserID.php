<?php

include '../db_connection.php';

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

?>