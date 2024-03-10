<?php

include 'db_connection.php';

// Function to get all users
function getAllUsers()
{
    global $conn;
    $sql = "SELECT * FROM user";
    $result = $conn->query($sql);
    $users = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
    return $users;
}

// Endpoint to get all users
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getAllUsers') {
    $users = getAllUsers();
    echo json_encode($users);
}

// Function to add a new user
function addUser($email, $password, $username, $shippingAddress)
{
    global $conn;
    $sql = "INSERT INTO user (email, password, username, shippingAddress) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $email, $password, $username, $shippingAddress);

    if ($stmt->execute()) {
        return "New user added successfully";
    } else {
        return "Error adding user: " . $stmt->error;
    }
}

// Endpoint to add a new user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'addUser') {
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);

    $email = $data['email'];
    $password = $data['password'];
    $username = $data['username'];
    $shippingAddress = $data['shippingAddress'];

    // Check if all required fields are provided
    if ($email !== null && $password !== null && $username !== null && $shippingAddress !== null) {
        $response = addUser($email, $password, $username, $shippingAddress);
        echo json_encode(array("message" => $response));
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Incomplete data"));
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(array("message" => "Method not allowed"));
}

// Function to delete a user
function deleteUser($userId)
{
    global $conn;
    $sql = "DELETE FROM user WHERE userId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);

    if ($stmt->execute()) {
        return "User deleted successfully";
    } else {
        return "Error deleting user: " . $stmt->error;
    }
}

// Endpoint to delete a user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);

    if (isset($data['action']) && $data['action'] === 'deleteUser') {
        $userId = $data['userId'];
        echo deleteUser($userId);
    }
}

// Function to update a user
function updateUser($userId, $email, $password, $username, $shippingAddress)
{
    global $conn;
    $sql = "UPDATE user SET email = ?, password = ?, username = ?, shippingAddress = ? WHERE userId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $email, $password, $username, $shippingAddress, $userId);

    if ($stmt->execute()) {
        return "User updated successfully";
    } else {
        return "Error updating user: " . $stmt->error;
    }
}

// Endpoint to update a user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);

    if (isset($data['action']) && $data['action'] === 'updateUser') {
        $userId = $data['userId'];
        $email = $data['email'];
        $password = $data['password'];
        $username = $data['username'];
        $shippingAddress = $data['shippingAddress'];

        echo updateUser($userId, $email, $password, $username, $shippingAddress);
    }
}
