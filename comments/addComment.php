<?php

include '../db_connection.php';

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
}


?>