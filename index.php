<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD de Usuários</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="js/script.js"></script>
</head>

<body>
    <div class="container is-flex is-justify-content-center">
        <div class="box">
            <h1 class="title">Lista de Usuários</h1>
            <table class="table is-bordered is-striped is-hoverable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Cor</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="usuarios-tbody">
                </tbody>
            </table>
            <button class="button is-primary" onclick="criarUsuario()">
                <span class="icon">
                    <i class="fas fa-plus"></i>
                </span>
                <span>Criar Usuário</span>
            </button>
        </div>
    </div>
</body>

</html>