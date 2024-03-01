<?php
require_once __DIR__ . '/Connection.php';

$conexao = new Connection();

/**
 * Obtém todos os usuários do banco de dados.
 *
 * @return array Array contendo todos os usuários do banco de dados.
 */
function getTodosUsuarios()
{
    global $conexao;

    $usuarios = [];

    try {
        $usuarios = $conexao->query(
            'SELECT *, u.id as user_id, u.name as user_name, c.name as color_name FROM users u
            LEFT JOIN user_colors uc ON u.id = uc.user_id
            LEFT JOIN colors c ON uc.color_id = c.id'
        );

        return $usuarios->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao consultar o banco de dados ' . $e]);
        exit;
    }
}

/**
 * Obtém todas as cores do banco de dados.
 *
 * @return array Array contendo todas as cores do banco de dados.
 */
function getTodasCores()
{
    global $conexao;

    $cores = [];

    try {
        $cores = $conexao->query('SELECT * FROM colors');

        return $cores->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao consultar o banco de dados ' . $e]);
        exit;
    }
}

/**
 * Obtém um usuário específico pelo ID.
 *
 * @param int $usuario_id O ID do usuário a ser obtido.
 * @return array|null Array contendo as informações do usuário ou NULL se o usuário não for encontrado.
 */
function getUsuarioPorId($usuario_id)
{
    global $conexao;

    try {
        $usuario = $conexao->query(
            'SELECT *, u.id as user_id, u.name as user_name, c.name as color_name FROM users u
            LEFT JOIN user_colors uc ON u.id = uc.user_id
            LEFT JOIN colors c ON uc.color_id = c.id WHERE u.id = ' . $usuario_id
        );

        return $usuario->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao consultar o banco de dados ' . $e]);
        exit;
    }
}

/**
 * Edita o nome de um usuário.
 *
 * @param int $usuario_id O ID do usuário a ser editado.
 * @param string $nome O novo nome para o usuário.
 * @param string $email O novo email para o usuário.
 * @param int $cor_id O novo id de cor para o usuário.
 */
function editarUsuario($usuario_id, $nome, $email, $cor_id)
{
    global $conexao;

    try {
        $conexao->query("UPDATE users SET name = '$nome', email = '$email' WHERE id = $usuario_id");

        $corUsuario = $conexao->query(
            'SELECT * FROM user_colors WHERE user_id = ' . $usuario_id
        );

        if ($corUsuario->fetch(PDO::FETCH_ASSOC)) {
            $conexao->query("UPDATE user_colors SET color_id = '$cor_id' WHERE user_id = $usuario_id");
        } else {
            $conexao->query("INSERT INTO user_colors (user_id, color_id) VALUES ($usuario_id, $cor_id)");
        }

        echo json_encode(['sucesso' => 'Usuário atualizado com sucesso']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao atualizar o usuário no banco de dados ' . $e]);
        exit;
    }
}

/**
 * Exclui um usuário do banco de dados.
 *
 * @param int $usuario_id O ID do usuário a ser excluído.
 */
function excluirUsuario($usuario_id)
{
    global $conexao;

    try {
        $conexao->query("DELETE FROM users WHERE id = $usuario_id");
        echo json_encode(['sucesso' => 'Usuário excluído com sucesso']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao excluir o usuário no banco de dados ' . $e]);
        exit;
    }
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['all_users'])) {
            $usuarios = getTodosUsuarios();
            header('Content-Type: application/json');
            echo json_encode($usuarios);
        } 
        else if (isset($_GET['all_colors'])) {
            $usuarios = getTodasCores();
            header('Content-Type: application/json');

            echo json_encode($usuarios);
        } else if (isset($_GET['user_id'])) {
            $usuario_id = $_GET['user_id'];
            $usuario = getUsuarioPorId($usuario_id);
            header('Content-Type: application/json');

            echo json_encode($usuario);
        } else {
            http_response_code(400);
            echo json_encode(['erro' => 'Parâmetros inválidos']);
        }
        break;

    case 'PUT':
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (isset($data['edit_user'])) {
            $usuario_id = $data['id'];
            $nome = $data['nome'];
            $email = $data['email'];
            $cor_id = $data['cor_id'];

            editarUsuario($usuario_id, $nome, $email, $cor_id);
        }
        break;

    case 'DELETE':
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (isset($data['delete_user'])) {
            $usuario_id = $data['id'];
            excluirUsuario($usuario_id);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['erro' => 'Método não permitido']);
        break;
}
