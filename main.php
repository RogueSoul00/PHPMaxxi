<?php 
include("config/database.php");
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

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica se o registro foi encontrado
    if (!$user) {
        echo "<p>Usuário não encontrado.</p>";
        exit;
    }
} catch (PDOException $e) {
    die("Erro ao buscar dados: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
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
    // Função para adicionar uma nova linha na tabela
    function addNewRow() {
        const tableBody = document.getElementById('table-body'); // Seleciona o corpo da tabela
        const newRow = document.createElement('tr'); // Cria uma nova linha

        // Adiciona células com inputs vazios
        newRow.innerHTML = `
            <td></td> <!-- ID, não será editável -->
            <td><input type="text" name="name" placeholder="Name"></td>
            <td><input type="email" name="email" placeholder="Email"></td>
            <td><input type="text" name="telephone" placeholder="Phone"></td>
            <td>
                <button class="button" onclick="saveNewRow(this)">Save</button>
            </td>
        `;
        tableBody.appendChild(newRow); // Adiciona a nova linha à tabela
    }

    // Função para salvar a nova linha
    function saveNewRow(saveButton) {
        const row = saveButton.parentElement.parentElement; // Encontra a linha onde o botão foi clicado
        const inputs = row.querySelectorAll('input'); // Obtém todos os inputs dessa linha
        const data = {}; // Objeto para armazenar os dados do formulário

        // Coleta os dados dos inputs
        inputs.forEach(input => {
            data[input.name] = input.value;
        });

        // Realiza uma requisição POST para salvar os dados
        fetch('functions/save_new_user.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert('User added successfully!');
                window.location.reload(); // Recarrega a página para atualizar a lista de usuários
            } else {
                alert('Error saving the user: ' + result.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // Função para habilitar a edição dos dados
    function enableEdit(id) {
        const row = document.getElementById(`row-${id}`);
        const fields = row.querySelectorAll('.editable-field');
        const saveBtn = row.querySelector('.save-button');
        const editBtn = row.querySelector('.edit-button');

        fields.forEach(field => {
            const currentValue = field.textContent;
            field.innerHTML = `<input type="text" value="${currentValue}" name="${field.dataset.field}">`;
        });

        saveBtn.style.display = 'inline-block';
        editBtn.style.display = 'none';
    }

    // Função para salvar as alterações após a edição
    function saveEdit(id) {
        const row = document.getElementById(`row-${id}`);
        const inputs = row.querySelectorAll('input');
        const data = {};

        inputs.forEach(input => {
            data[input.name] = input.value;
        });
        data['id'] = id;

        fetch('functions/save_edit.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                inputs.forEach(input => {
                    const td = input.parentElement;
                    td.textContent = input.value;
                });

                row.querySelector('.save-button').style.display = 'none';
                row.querySelector('.edit-button').style.display = 'inline-block';
            } else {
                alert('Error saving changes: ' + result.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // Função para apagar o usuário
    function deleteUser(id) {
        if (confirm("Are you sure you want to delete this user?")) {
            fetch('functions/delete_user.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert("User deleted successfully!");
                    document.getElementById(`row-${id}`).remove(); // Remove a linha da tabela
                } else {
                    alert("Error deleting the user: " + result.message);
                }
            })
            .catch(error => console.error('Error:', error));
        }
    }
    </script>
</head>
<body>
    <div class="container">
        <h1>User List</h1>
        <button class="button" onclick="addNewRow()">New User</button>
    </div>

    <?php if ($rows): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="table-body">
                <?php foreach ($rows as $row): ?>
                    <tr id="row-<?= htmlspecialchars($row['id']) ?>">
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td class="editable-field" data-field="name"><?= htmlspecialchars($row['name']) ?></td>
                        <td class="editable-field" data-field="email"><?= htmlspecialchars($row['email']) ?></td>
                        <td class="editable-field" data-field="telephone"><?= htmlspecialchars($row['telephone']) ?></td>
                        <td>
                            <button class="button edit-button" onclick="enableEdit(<?= htmlspecialchars($row['id']) ?>)">Edit</button>
                            <button class="button save-button" style="display: none;" onclick="saveEdit(<?= htmlspecialchars($row['id']) ?>)">Save</button>
                            <button class="button delete-button" onclick="deleteUser(<?= htmlspecialchars($row['id']) ?>)">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No records found in the table.</p>
    <?php endif; ?>
</body>
</html>
