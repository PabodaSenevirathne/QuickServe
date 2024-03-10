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
function addComment($productId, $userId, $rating, $image, $text){
    global $conn;
    $sql = "INSERT INTO comments (productId, userId, rating, image, text) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return "Error preparing statement: " . $conn->error;
    }

    $stmt->bind_param("iisss", $productId, $userId, $rating, $image, $text);
    if ($stmt->execute()) {
        return "Comment added successfully";
    } else {
        return "Error adding comment: " . $stmt->error;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'addComment') {
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);

    $productId = $data['productId'];
    $userId = $data['userId'];
    $rating = $data['rating'];
    $image = $data['image'];
    $text = $data['text'];
    
    if ($productId !== null && $userId !== null && $rating !== null && $image !== null && $text !== null ) {
        $response = addComment($productId, $userId, $rating, $image, $text);
        echo json_encode(array("message" => $response));
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Incomplete data"));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "add Method not allowed"));
}




// Delete a comment (Use by the Admin user only)
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
