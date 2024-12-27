<?php
require '../conexao.php';

// Recebe os dados via POST
$data = json_decode(file_get_contents('php://input'), true);

// Validação dos dados
$nome = filter_var($data['nome'], FILTER_SANITIZE_STRING);
$email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
$telefone = filter_var($data['telefone'], FILTER_SANITIZE_STRING);

if (!$nome || !$email || !$telefone) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Dados inválidos']);
    exit;
}

try {
    // Inserção do novo usuário
    $sql = "INSERT INTO tabela1 (nome, email, telefone) VALUES (:nome, :email, :telefone)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->execute();

    // Obtém o ID do novo usuário inserido
    $id = $conn->lastInsertId();

    echo json_encode(['sucesso' => true, 'id' => $id]); // Retorna o ID gerado para atualizar a linha da tabela
} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao salvar usuário: ' . $e->getMessage()]);
}
?>
