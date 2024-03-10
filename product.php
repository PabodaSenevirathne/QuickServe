<?php

include 'db_connection.php';

// Get all products
function getAllProducts()
{
    global $conn;
    $sql = "SELECT * FROM `product`";
    $result = $conn->query($sql);
    $products = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    return $products;
}

// Endpoint to get all products
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getAllProducts') {
    $products = getAllProducts();
    echo json_encode($products);
}

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
    
    // Check if all required fields are provided
    if ($description !== null && $image !== null && $price !== null && $shippingCost !== null) {
        $response = addProduct($description, $image, $price, $shippingCost);
        echo json_encode(array("message" => $response));
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Incomplete data"));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "add Method not allowed"));
}


// Function to update Product

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

    // Dynamically bind parameters
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
} else {
    http_response_code(405);
    echo json_encode(array("message" => "update Method not allowed"));
    exit;
}


// Function to delete a product
function deleteProduct($productId)
{
    global $conn;
    $sql = "DELETE FROM product WHERE productId = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return "Error preparing statement: " . $conn->error;
    }

    $stmt->bind_param("i", $productId);
    if ($stmt->execute()) {
        return "Product deleted successfully";
    } else {
        return "Error deleting product: " . $stmt->error;
    }
}

// Endpoint to delete a product
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Retrieve JSON data from the request body
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);

    // Check if JSON data is valid and contains the required parameters
    if (isset($data['action']) && $data['action'] === 'deleteProduct' && isset($data['productId'])) {
        // Extract productId from JSON data
        $productId = $data['productId'];

        // Delete the product
        echo deleteProduct($productId);
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Invalid JSON data or missing parameters"));
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(array("message" => "DELETE method not allowed"));
}

?>
