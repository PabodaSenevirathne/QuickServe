<?php

include '../db_connection.php';


// Update the quantity of an item in the cart
function updateQuantityForProduct($productId, $id, $newQuantity) {
    global $conn;
    $sql = "UPDATE cart SET quantity = ? WHERE productId = ? AND id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return "Error preparing statement: " . $conn->error;
    }

    $stmt->bind_param("iii", $newQuantity, $productId, $id);
    if ($stmt->execute()) {
        return "Quantity updated successfully";
    } else {
        return "Error updating quantity: " . $stmt->error;
    }
}

// Handle PATCH request to update quantity for a given user ID
// if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
//     $json_data = file_get_contents('php://input');
//     $data = json_decode($json_data, true);

//     if (isset($data['productId']) && isset($data['id']) && isset($data['newQuantity'])) {
//         $productId = $data['productId'];
//         $id = $data['id'];
//         $newQuantity = $data['newQuantity'];

//         $response = updateQuantityForProduct($productId, $id, $newQuantity);
//         echo json_encode(array("message" => $response));
//     } else {
//         http_response_code(400);
//         echo json_encode(array("message" => "productId, id, or newQuantity not provided"));
//     }
// } else {
//     http_response_code(405);
//     echo json_encode(array("message" => "Method not allowed"));
// }

if ($_SERVER['REQUEST_METHOD'] === 'PATCH' && isset($_GET['productId']) && isset($_GET['id'])) {
    $productId = $_GET['productId'];
    $id = $_GET['id'];

    // Note: newQuantity is expected to be provided in the request body
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);

    if (isset($data['newQuantity'])) {
        $newQuantity = $data['newQuantity'];
        $response = updateQuantityForProduct($productId, $id, $newQuantity);
        echo json_encode(array("message" => $response));
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "newQuantity not provided in the request body"));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "productId or id not provided as query parameters"));
}

?>
