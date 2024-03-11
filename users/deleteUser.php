<?php

include '../db_connection.php';


// Function to delete a product
function deleteUser($userId)
{
    global $conn;
    $sql = "DELETE FROM user WHERE userId = $userId";
    if ($conn->query($sql) === TRUE) {
        echo "Record deleted successfully";
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

// Endpoint to delete a product
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['action']) && $_GET['action'] === 'deleteUser') {
    $userId = $_GET['userId'];
    deleteUser($userId);
} else {
    http_response_code(405);
    echo json_encode(array("message" => "delete Method not allowed"));
}

?>