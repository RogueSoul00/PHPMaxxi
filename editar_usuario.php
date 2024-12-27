<?php
require 'conexao.php';

// Valida o parâmetro ID via GET
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    echo "<p>ID inválido ou não fornecido.</p>";
    exit;
}

try {
    // Consulta o registro com o ID fornecido
    $sql = "SELECT * FROM tabela1 WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica se o registro foi encontrado
    if (!$usuario) {
        echo "<p>Usuário não encontrado.</p>";
        exit;
    }
} catch (PDOException $e) {
    die("Erro ao buscar dados: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        h1 {
            margin-bottom: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .button {
            display: inline-block;
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .button:hover {
            background-color: #45a049;
        }
        .edit-button {
            background-color: #007BFF;
            margin-left: 10px;
        }
        .edit-button:hover {
            background-color: #0056b3;
        }
    </style>
    <script>
    
    function adicionarNovaLinha() {
        const tabela = document.getElementById('corpo-tabela'); // Seleciona o corpo da tabela
        const novaLinha = document.createElement('tr'); // Cria uma nova linha

        // Adiciona as células com inputs vazios
        novaLinha.innerHTML = `
            <td></td> <!-- ID, não será editável -->
            <td><input type="text" name="nome" placeholder="Nome"></td>
            <td><input type="email" name="email" placeholder="Email"></td>
            <td><input type="text" name="telefone" placeholder="Telefone"></td>
            <td>
                <button class="button" onclick="salvarNovaLinha(this)">Salvar</button>
            </td>
        `;
        tabela.appendChild(novaLinha); // Adiciona a nova linha à tabela
    }

    function salvarNovaLinha(botaoSalvar) {
        const linha = botaoSalvar.parentElement.parentElement; // Encontra a linha em que o botão foi clicado
        const inputs = linha.querySelectorAll('input'); // Obtém todos os inputs dessa linha
        const data = {}; // Objeto para armazenar os dados do formulário

        // Coleta os dados dos inputs
        inputs.forEach(input => {
            data[input.name] = input.value;
        });

        // Realiza uma requisição POST para salvar os dados
        fetch('funcoes/salvar_novo_usuario.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.sucesso) {
                alert('Usuário adicionado com sucesso!');
                window.location.reload(); // Recarrega a página para atualizar a lista de usuários
            } else {
                alert('Erro ao salvar o usuário: ' + result.mensagem);
            }
        })
        .catch(error => console.error('Erro:', error));
    }



        function habilitarEdicao(id) {
            const linha = document.getElementById(`linha-${id}`);
            const campos = linha.querySelectorAll('.campo-editavel');
            const salvarBtn = linha.querySelector('.botao-salvar');
            const editarBtn = linha.querySelector('.botao-editar');

            campos.forEach(campo => {
                const valorAtual = campo.textContent;
                campo.innerHTML = `<input type="text" value="${valorAtual}" name="${campo.dataset.campo}">`;
            });

            salvarBtn.style.display = 'inline-block';
            editarBtn.style.display = 'none';
        }

        function salvarEdicao(id) {
            const linha = document.getElementById(`linha-${id}`);
            const inputs = linha.querySelectorAll('input');
            const data = {};

            inputs.forEach(input => {
                data[input.name] = input.value;
            });
            data['id'] = id;

            fetch('funcoes/salvar_edicao.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.sucesso) {
                    inputs.forEach(input => {
                        const td = input.parentElement;
                        td.textContent = input.value;
                    });

                    linha.querySelector('.botao-salvar').style.display = 'none';
                    linha.querySelector('.botao-editar').style.display = 'inline-block';
                } else {
                    alert('Erro ao salvar alterações: ' + result.mensagem);
                }
            })
            .catch(error => console.error('Erro:', error));
        }

        function apagarUsuario(id) {
        if (confirm("Tem certeza que deseja apagar este usuário?")) {
            fetch('funcoes/apagar_usuario.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            })
            .then(response => response.json())
            .then(result => {
                if (result.sucesso) {
                    alert("Usuário apagado com sucesso!");
                    document.getElementById(`linha-${id}`).remove(); // Remove a linha da tabela
                } else {
                    alert("Erro ao apagar o usuário: " + result.mensagem);
                }
            })
            .catch(error => console.error('Erro:', error));
        }
    }
    </script>
</head>
<body>
    <div class="container">
        <h1>Lista de Usuários</h1>
        <button class="button" onclick="adicionarNovaLinha()">Novo Usuário</button>
    </div>

    <?php if ($linhas): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Telefone</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody  id="corpo-tabela">
                <?php foreach ($linhas as $linha): ?>
                    <tr id="linha-<?= htmlspecialchars($linha['id']) ?>">
                        <td><?= htmlspecialchars($linha['id']) ?></td>
                        <td class="campo-editavel" data-campo="nome"><?= htmlspecialchars($linha['nome']) ?></td>
                        <td class="campo-editavel" data-campo="email"><?= htmlspecialchars($linha['email']) ?></td>
                        <td class="campo-editavel" data-campo="telefone"><?= htmlspecialchars($linha['telefone']) ?></td>
                        <td>
                            <button class="button edit-button botao-editar" onclick="habilitarEdicao(<?= htmlspecialchars($linha['id']) ?>)">Editar</button>
                            <button class="button botao-salvar" style="display: none;" onclick="salvarEdicao(<?= htmlspecialchars($linha['id']) ?>)">Salvar</button>
                            <button class="button botao-apagar" onclick="apagarUsuario(<?= htmlspecialchars($linha['id']) ?>)">Apagar</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nenhum registro encontrado na tabela.</p>
    <?php endif; ?>
</body>
</html>
