<?php

include 'db_connection.php';

// Get all items in the cart of a user
function getCartItemsByUserId($userId) {
    global $conn;
    $sql = "SELECT * FROM cart WHERE userId = $userId";
    $result = $conn->query($sql);
    $cartItems = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $cartItems[] = $row;
        }
    }
    return $cartItems;
}

// Endpoint to get items in the cart for a user
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getCartItemsByUserId') {
    $userId = $_GET['userId'];
    $cartItems = getCartItemsByUserId($userId);
    echo json_encode($cartItems);
}

// Add a new item to the cart
function addToCart($userId, $productId, $quantity) {
    global $conn;
    $sql = "INSERT INTO cart (userId, productId, quantity) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $userId, $productId, $quantity);
    
    if ($stmt->execute()) {
        return "Item added to cart successfully";
    } else {
        return "Error adding item to cart: " . $stmt->error;
    }
}

// Endpoint to add a new item to the cart
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);

    $userId = $data['userId'];
    $productId = $data['productId'];
    $quantity = $data['quantity'];

    // Check if all required fields are provided
    if ($userId !== null && $productId !== null && $quantity !== null) {
        echo addToCart($userId, $productId, $quantity);
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Incomplete data"));
    } exit;
} else {
    http_response_code(405);
    echo json_encode(array("message" => " add Method not allowed"));
    exit;
}

// Update the quantity of an item in the cart
function updateQuantity($cartItemId, $quantity) {
    global $conn;
    $sql = "UPDATE cart SET quantity = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $quantity, $cartItemId);
    
    if ($stmt->execute()) {
        return "Cart item quantity updated successfully";
    } else {
        return "Error updating cart item quantity: " . $stmt->error;
    }
}

// Endpoint to update the quantity of an item in the cart
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_POST['action']) && $_POST['action'] === 'updateCartItemQuantity') {
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);

    $cartItemId = $data['cartItemId'];
    $quantity = $data['quantity'];

    // Check if all required fields are provided
    if ($cartItemId !== null && $quantity !== null) {
        echo updateQuantity($cartItemId, $quantity);
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Incomplete data"));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Method not allowed"));
}

// Function to delete a cart item
function deleteCartItem($cartItemId) {
    global $conn;
    $sql = "DELETE FROM cart WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cartItemId);

    if ($stmt->execute()) {
        return "Cart item deleted successfully";
    } else {
        return "Error deleting cart item: " . $stmt->error;
    }
}






?>
