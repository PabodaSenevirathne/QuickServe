<?php
include '../db_connection.php';

// Function to delete a product
function deleteCartItem($id)
{
    global $conn;
    $sql = "DELETE FROM cart WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        echo "Record deleted successfully";
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

// Endpoint to delete a product
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['action']) && $_GET['action'] === 'deleteCartItem') {
    $id = $_GET['id'];
    deleteCartItem($id);
} else {
    http_response_code(405);
}

?>