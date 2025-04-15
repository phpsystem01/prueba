<?php
require_once 'database.php';

function registrarUsuario($username, $email, $password, $nombre_completo) {
    $db = new Database();
    $conn = $db->getConnection();
    
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    
    $stmt = $conn->prepare("INSERT INTO usuarios (username, email, password, nombre_completo) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $hashed_password, $nombre_completo);
    
    $result = $stmt->execute();
    
    $stmt->close();
    $db->closeConnection();
    
    return $result;
}

function verificarUsuario($username, $password) {
    $db = new Database();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("SELECT id, username, password FROM usuarios WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            return true;
        }
    }
    
    $stmt->close();
    $db->closeConnection();
    
    return false;
}

function usuarioExiste($username, $email) {
    $db = new Database();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();
    
    $count = $stmt->num_rows;
    
    $stmt->close();
    $db->closeConnection();
    
    return $count > 0;
}
?>