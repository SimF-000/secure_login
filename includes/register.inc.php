<?php
include_once 'db_connect.php';
include_once 'psl-config.php';
 
$error_msg = "";
 
if (isset($_POST['email'], $_POST['p'])) {
    // Vérification des données
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_NUMBER_INT);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg .= '<p class="error">Votre addresse email est invalide</p>';
    }
 
    $password = filter_input(INPUT_POST, 'p', FILTER_SANITIZE_STRING);
    if (strlen($password) != 128) {
        $error_msg .= '<p class="error">Configuration de mot de passe invalide.</p>';
    }
  
    $prep_stmt = "SELECT id FROM members WHERE email = ? LIMIT 1";
    $stmt = $mysqli->prepare($prep_stmt);
 
   // vérifie si l'utilisateur éxiste déjà  
    if ($stmt) {
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
 
        if ($stmt->num_rows == 1) {
            $error_msg .= '<p class="error">Un utilisateur avec cette email éxiste déjà.</p>';
            $stmt->close();
        }
    } else {
        $error_msg .= '<p class="error">érreur dans la BDD à la ligne 39</p>';
        $stmt->close();
    }
 
    if (empty($error_msg)) {
        $password = password_hash($password, PASSWORD_BCRYPT);
        $now = time();

        // insert le nouvelle utilisateur en BDD 
        if ($insert_stmt = $mysqli->prepare("INSERT INTO members (email, password, role, register) VALUES (?, ?, ?, ?)")) {
            $insert_stmt->bind_param('ssss', $email, $password, $role, $now);
            if (! $insert_stmt->execute()) {
                header('Location: ../error.php?err=l\'inscription à eu une érreur');
            }
        }
        header('Location: ./register_success.php');
    }
}
?>