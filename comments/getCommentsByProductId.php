<?php
include '../db_connection.php';

// Get all comments
function getCommentsByProductId($productId) {
    global $conn;
    $sql = "SELECT * FROM comments WHERE productId = $productId";
    $result = $conn->query($sql);
    $comments = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $comments[] = $row;
        }
    }
    return $comments;
}

// Endpoint to get comments
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getCommentsByProductId') {
    $productId = $_GET['productId'];
    $comments = getCommentsByProductId($productId);
    echo json_encode($comments);
}

?>