<?php

include 'db_connection.php';

// Get all users
function getAllUsers() {
    global $conn;
    $sql = "SELECT * FROM user";
    $result = $conn->query($sql);
    $users = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
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

// Add a new user
function addUser($email, $password, $username, $shippingAddress) {
    global $conn;
    $sql = "INSERT INTO user (email, password, username, shippingAddress) VALUES ('$email', '$password', '$username', '$shippingAddress')";
    if ($conn->query($sql) === TRUE) {
        return "New user added successfully";
    } else {
        return "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Endpoint to add a new user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'addUser') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $username = $_POST['username'];
    $shippingAddress = $_POST['shippingAddress'];
    echo addUser($email, $password, $username, $shippingAddress);
}

?>
