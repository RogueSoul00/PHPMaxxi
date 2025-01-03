<?php
// Define the header for JSON
header('Content-Type: application/json');

try {
    // Receives the data sent via POST in JSON format
    $input = json_decode(file_get_contents('php://input'), true);

    // Validates the received fields
    if (!isset($input['id'], $input['name'], $input['email'], $input['telephone'])) {
        throw new Exception("Invalid data.");
    }

    $id = filter_var($input['id'], FILTER_VALIDATE_INT);
    $name = filter_var($input['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($input['email'], FILTER_SANITIZE_EMAIL);
    $telephone = filter_var($input['telephone'], FILTER_SANITIZE_STRING);

    if (!$id || !$name || !$email || !$telephone) {
        throw new Exception("Invalid data.");
    }

    // Update the data in the database
    $sql = "UPDATE table1 SET name = :name, email = :email, telephone = :telephone WHERE id = :id";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':telephone', $telephone);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    // Returns success
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    // Returns error
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
