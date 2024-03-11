<?php

include '../db_connection.php';

// Add a new product
function addProduct($description, $image, $price, $shippingCost)
{
    global $conn;
    $sql = "INSERT INTO product (description, image, price, shippingCost) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return "Error preparing statement: " . $conn->error;
    }

    $stmt->bind_param("ssii", $description, $image, $price, $shippingCost);
    if ($stmt->execute()) {
        return "New product added successfully";
    } else {
        return "Error adding product: " . $stmt->error;
    }
}

// Endpoint to add a new product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'addProduct') {
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);

    $description = $data['description'];
    $image = $data['image'];
    $price = $data['price'];
    $shippingCost = $data['shippingCost'];
    
    if ($description !== null && $image !== null && $price !== null && $shippingCost !== null) {
        $response = addProduct($description, $image, $price, $shippingCost);
        echo json_encode(array("message" => $response));
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Incomplete data"));
    }
}

?>