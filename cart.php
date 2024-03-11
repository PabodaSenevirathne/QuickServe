<?php

include 'db_connection.php';


// Update the quantity of an item in the cart
function updateQuantity($userId, $quantity) {
    global $conn;
    $sql = "UPDATE cart SET quantity = $quantity WHERE userId = $userId";

    if ($conn->query($sql) === TRUE) {
        echo "Record updated successfully";
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

// Endpoint to update the quantity
if ($_SERVER['REQUEST_METHOD'] === 'PATCH' && isset($_POST['action']) && $_POST['action'] === 'updateQuantity') {
    $userId = $_GET['userId'];

    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);

    $quantity = $data['quantity'];

    updateQuantity($userId, $quantity);

    // if (isset($data['id'])) {
    //     $id = $data['id'];
    //     unset($data['id']);

    //     if (!empty($data)) {
    //         $response = updateProduct($productId, $data);
    //         echo json_encode(array("message" => $response));
    //     } else {
    //         http_response_code(400);
    //         echo json_encode(array("message" => "No fields to update"));
    //     }
    // } else {
    //     http_response_code(400);
    //     echo json_encode(array("message" => "productId not provided"));
    // }
    // exit;
} else {
    http_response_code(405);
    echo json_encode(array("message" => "patch Method not allowed"));
}


?>
