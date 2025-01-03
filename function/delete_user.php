<?php
// Receives the user ID via POST
$data = json_decode(file_get_contents('php://input'), true);
var_dump($data);
$userId = filter_var($data['id'], FILTER_VALIDATE_INT);

if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'Invalid ID']);
    exit;
}

try {
    // Deletes the user from the table
    $sql = "DELETE FROM table1 WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error deleting user: ' . $e->getMessage()]);
}
?>
