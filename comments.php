<?php

include 'db_connection.php';

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

// Add a new comment
function addComment($productId, $userId, $rating, $image, $text) {
    global $conn;
    $sql = "INSERT INTO comments (productId, userId, rating, image, text) VALUES ($productId, $userId, $rating, '$image', '$text')";
    if ($conn->query($sql) === TRUE) {
        return "New comment added successfully";
    } else {
        return "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Endpoint to add a new comment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'addComment') {
    $productId = $_POST['productId'];
    $userId = $_POST['userId'];
    $rating = $_POST['rating'];
    $image = $_POST['image'];
    $text = $_POST['text'];
    echo addComment($productId, $userId, $rating, $image, $text);
}

// Delete a comment
function deleteComment($commentId) {
    global $conn;
    $sql = "DELETE FROM comments WHERE id = $commentId";
    if ($conn->query($sql) === TRUE) {
        return "Comment deleted successfully";
    } else {
        return "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Endpoint to delete a comment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'deleteComment') {
    $commentId = $_POST['commentId'];
    echo deleteComment($commentId);
}



?>
