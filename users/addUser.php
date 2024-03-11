<?php

include '../db_connection.php';

// Function to add a new user
function addUser($email, $password, $username, $shippingAddress)
{
    global $conn;
    $sql = "INSERT INTO user (email, password, username, shippingAddress) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return "Error preparing statement: " . $conn->error;
    }

    $stmt->bind_param("ssii", $email, $password, $username, $shippingAddress);
    if ($stmt->execute()) {
        return "New user added successfully";
    } else {
        return "Error adding user: " . $stmt->error;
    }
}

// Endpoint to add a new user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'addUser') {
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);

    $email = $data['email'];
    $password = $data['password'];
    $username = $data['username'];
    $shippingAddress = $data['shippingAddress'];
    
    if ($email !== null && $password !== null && $username !== null && $shippingAddress !== null) {
        $response = addUser($email, $password, $username, $shippingAddress);
        echo json_encode(array("message" => $response));
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Incomplete data"));
    }
}

?>