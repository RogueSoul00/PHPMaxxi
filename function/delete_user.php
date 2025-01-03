<?php
// Recebe o ID do usuário via POST
$data = json_decode(file_get_contents('php://input'), true);
var_dump($data);
$id = filter_var($data['id'], FILTER_VALIDATE_INT);

if (!$id) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'ID invalido']);
    exit;
}

try {
    // Deleta o usuário da tabela
    $sql = "DELETE FROM tabela1 WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode(['sucesso' => true]);
} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao apagar o usuário: ' . $e->getMessage()]);
}
?>
