<?php

// Function to update Product
include '../db_connection.php';


function updateProduct($productId, $newData) {
    global $conn;

    $sql = "UPDATE product SET ";
    $params = array();
    foreach ($newData as $key => $value) {
        $sql .= "$key = ?, ";
        $params[] = $value;
    }
    
    $sql = rtrim($sql, ", ");
    $sql .= " WHERE productId = ?";
    $params[] = $productId;

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return "Error preparing statement: " . $conn->error;
    }

    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        return "Product updated successfully";
    } else {
        return "Error updating product: " . $stmt->error;
    }
}

// Endpoint to update a product
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['action']) && $_GET['action'] === 'updateProduct') {
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);

    if (isset($data['productId'])) {
        $productId = $data['productId'];
        unset($data['productId']);

        if (!empty($data)) {
            $response = updateProduct($productId, $data);
            echo json_encode(array("message" => $response));
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "No fields to update"));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "productId not provided"));
    }
    exit;
}

?>