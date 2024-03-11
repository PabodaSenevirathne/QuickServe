<?php

include '../db_connection.php';


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
}

?>