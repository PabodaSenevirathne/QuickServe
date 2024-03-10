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

// // Endpoint to delete a user
// if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
//     $json_data = file_get_contents('php://input');
//     $data = json_decode($json_data, true);

//     if (isset($data['action']) && $data['action'] === 'deleteUser') {
//         $userId = $data['userId'];
//         echo deleteUser($userId);
//     }
// } else {
//     http_response_code(405); // Method Not Allowed
//     echo json_encode(array("message" => "DELETE method not allowed"));
// }


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
    
    // Check if all required fields are provided
    if ($email !== null && $password !== null && $username !== null && $shippingAddress !== null) {
        $response = addUser($email, $password, $username, $shippingAddress);
        echo json_encode(array("message" => $response));
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Incomplete data"));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "add Method not allowed"));
}


// Function to update user data
function updateUser($userId, $newData) {
    global $conn;

    $sql = "UPDATE user SET ";
    $params = array();
    foreach ($newData as $key => $value) {
        $sql .= "$key = ?, ";
        $params[] = $value;
    }

    $sql = rtrim($sql, ", ");
    $sql .= " WHERE userId = ?";
    $params[] = $userId;

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return "Error preparing statement: " . $conn->error;
    }

    // Dynamically bind parameters
    $types = str_repeat('s', count($params) - 1) . 'i';
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        return "User updated successfully";
    } else {
        return "Error updating user: " . $stmt->error;
    }
}



// Endpoint to update a user
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['action']) && $_GET['action'] === 'updateUser') {
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);

    if (isset($data['userId'])) {
        $userId = $data['userId'];
        unset($data['userId']);

        if (!empty($data)) {
            $response = updateUser($userId, $data);
            echo json_encode(array("message" => $response));
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "No fields to update"));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "userId not provided"));
    }
    exit;
} else {
    http_response_code(405);
    echo json_encode(array("message" => "update Method not allowed"));
    exit;
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
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Retrieve JSON data from the request body
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);

    // Check if JSON data is valid and contains the required parameters
    if (isset($data['action']) && $data['action'] === 'deleteUser' && isset($data['userId'])) {
        // Extract userId from JSON data
        $userId = $data['userId'];

        // Delete the user
        echo deleteUser($userId);
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Invalid JSON data or missing parameters"));
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(array("message" => "DELETE method not allowed"));
}



?>