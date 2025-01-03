<?php
// Define o cabeçalho para JSON
header('Content-Type: application/json');

try {
    // Recebe os dados enviados via POST em formato JSON
    $input = json_decode(file_get_contents('php://input'), true);

    // Valida os campos recebidos
    if (!isset($input['id'], $input['nome'], $input['email'], $input['telefone']
    )) {
        throw new Exception("Dados inválidos.");
    }

    $id = filter_var($input['id'], FILTER_VALIDATE_INT);
    $nome = filter_var($input['nome'], FILTER_SANITIZE_STRING);
    $email = filter_var($input['email'], FILTER_SANITIZE_EMAIL);
    $telephone = filter_var($input['telefone'], FILTER_SANITIZE_STRING);

    if (!$id || !$nome || !$email || !$telephone) {
        throw new Exception("Dados inválidos.");
    }

    // Atualiza os dados no banco
    $sql = "UPDATE tabela1 SET nome = :nome, email = :email, telefone = 
    :telefone WHERE id = :id";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':telefone', $telephone);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    // Retorna sucesso
    echo json_encode(['sucesso' => true]);
} catch (Exception $e) {
    // Retorna erro
    echo json_encode(['sucesso' => false, 'mensagem' => $e->getMessage()]);
}
