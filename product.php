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


// // Update a product
// function updateProduct($productId, $description, $image, $price, $shippingCost)
// {
//     global $conn;
//     $sql = "UPDATE product SET description = ?, image = ?, price = ?, shippingCost = ? WHERE productId = ?";
//     $stmt = $conn->prepare($sql);
//     if (!$stmt) {
//         return "Error preparing statement: " . $conn->error;
//     }

//     $stmt->bind_param("ssiii", $description, $image, $price, $shippingCost, $productId);
//     if ($stmt->execute()) {
//         return "Product updated successfully";
//     } else {
//         return "Error updating product: " . $stmt->error;
//     }
// }

// Function to update user data
function updateProduct($productId, $newData) {
    global $conn;

    // Assuming $newData is an associative array with keys as column names and values as updated data
    $sql = "UPDATE product SET ";
    $params = array();
    foreach ($newData as $key => $value) {
        $sql .= "$key = ?, ";
        $params[] = $value;
    }
    // Remove trailing comma and space
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
        return "User updated successfully";
    } else {
        return "Error updating user: " . $stmt->error;
    }
}


// // Endpoint to update a product
// if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_POST['action']) && $_POST['action'] === 'updateProduct') {

//     $json_data = file_get_contents('php://input');
//     $data = json_decode($json_data, true);

//     $productId = $data['productId'];
//     $description = $data['description'];
//     $image = $data['image'];
//     $price = $data['price'];
//     $shippingCost = $data['shippingCost'];
    
//     // Check if all required fields are provided
//     if ($productId !== null && $description !== null && $image !== null && $price !== null && $shippingCost !== null) {
//         $response = updateProduct($productId, $description, $image, $price, $shippingCost);
//         echo json_encode(array("message" => $response));
//     } else {
//         http_response_code(400);
//         echo json_encode(array("message" => "Incomplete data"));
//     }
// } else {
//     http_response_code(405); // Method Not Allowed
//     echo json_encode(array("message" => "Method not allowed"));

// }

// Check if the request method is PUT
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['action']) && $_GET['action'] === 'updateProduct') {
    // Retrieve raw data from the request body
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);

    // Assuming the productId is part of the request data
    if (isset($data['productId'])) {
        $productId = $data['productId'];
        unset($data['productId']);

        // Check if there are any fields to update
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
    http_response_code(405); // Method Not Allowed
    echo json_encode(array("message" => "update Method not allowed"));
    exit;
}

// Delete a product
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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'deleteProduct') {
    $productId = $_POST['productId'];
    
    // Check if productId is provided
    if ($productId !== null) {
        $response = deleteProduct($productId);
        echo json_encode(array("message" => $response));
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Product ID is required"));
    }
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
    http_response_code(405); // Method Not Allowed
    echo json_encode(array("message" => "add Method not allowed"));
}
?>
