<?php 
// Receives data via POST
$data = json_decode(file_get_contents('php://input'), true);

// Data validation
$name = filter_var($data['name'], FILTER_SANITIZE_STRING);
$email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
$telephone = filter_var($data['telephone'], FILTER_SANITIZE_STRING);

if (!$name || !$email || !$telephone) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

try {
    // Insert new user
    $sql = "INSERT INTO table1 (name, email, telephone) VALUES (:name, :email, :telephone)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':telephone', $telephone);
    $stmt->execute();

    // Get the ID of the newly inserted user
    $id = $conn->lastInsertId();

    echo json_encode(['success' => true, 'id' => $id]); // Return the generated ID to update the table row
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error saving user: ' . $e->getMessage()]);
}
?>
