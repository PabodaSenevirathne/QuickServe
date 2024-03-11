<?php

include '../db_connection.php';
// Delete a comment (Use by the Admin user only)
function deleteComment($id)
{
    global $conn;
    $sql = "DELETE FROM comments WHERE userId = $id";
    if ($conn->query($sql) === TRUE) {
        echo "Record deleted successfully";
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

// Endpoint to delete a product
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['action']) && $_GET['action'] === 'deleteComment') {
    $id = $_GET['id'];
    deleteComment($id);
} else {
    http_response_code(405);
}

?>