<?php
include_once 'psl-config.php';
 
function sec_session_start() {
    $session_name = 'sec_session_id';   // Création d'un id de session 
    $secure = SECURE;
    // Empêche le JS d'avoir accès à cette session
    $httponly = true;
    // Force les sessions à n'utiliser que des cookies
    if (ini_set('session.use_only_cookies', 1) === FALSE) {
        header("Location: ../error.php?err=Impossible de créer une session sure");
        exit();
    }
    // Paramètres des cookies
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly);
    session_name($session_name);
    session_start();            
    session_regenerate_id();    // régénère la session et supprime l'ancienne 
}

function login($email, $password, $mysqli) {

    // Utilisation de champs préfait pour éviter les injections SQL
    if ($stmt = $mysqli->prepare(" SELECT m.id, m.password, m.role, r.label FROM members as m INNER JOIN roles as r ON r.rid = m.role WHERE email = ? LIMIT 1")) {
        $stmt->bind_param('s', $email);  // Prend email en parametre
        $stmt->execute();
        $stmt->store_result();
 
        // crée des variables en fonction du résultat
        $stmt->bind_result($user_id, $db_password, $role_id, $role_label);
        $stmt->fetch();

        if ($stmt->num_rows == 1) {
            // Si l'utilisateur éxiste on regarde le nombre de tentative de connexion
 
            if (checkbrute($user_id, $mysqli) == true) {
                // le compte est bloqué

                 $to      = $email;
                 $subject = 'compte bloqué';
                 $message = '<p>votre compte à été bloqué à cause d\'un nombre trop élévé de requête</p>';
                 $message .= '<p>Renvoyez nous un message si vous souhaitez le débloquer</p>';
                 $headers = 'From: simon.fouin@gmail.com' . "\r\n" .
                 'Reply-To: simon.fouin@gmail.com' . "\r\n" .
                 'X-Mailer: PHP/' . phpversion();

                 mail($to, $subject, $message, $headers);
                return false;

            } else {
                // Vérifie le mot de passe
                if (password_verify($password, $db_password)) {

                    $user_browser = $_SERVER['HTTP_USER_AGENT'];
                    $user_id = preg_replace("/[^0-9]+/", "", $user_id);

                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['role']['id'] = $role_id;
                    $_SESSION['role']['label'] = html_entity_decode($role_label);
                    $_SESSION['email'] = $email;
                    $_SESSION['login_string'] = hash('sha512', $db_password . $user_browser);

                    // le mot de passe est correcte, on enregistre la tentative dans la BDD
                    $now = time();
                    $mysqli->query("INSERT INTO login_attempts(user_id, time, login) VALUES ('$user_id', '$now', 1)");

                    // l'utilisateur est loggé.
                    return true;
                } else {
                    // le mot de passe est incorect, on enregistre la tentative dans la BDD
                    $now = time();
                    $mysqli->query("INSERT INTO login_attempts(user_id, time, login) VALUES ('$user_id', '$now', 0)");
                    return false;
                }
            }
        } else {
            // l'utilisateur n'éxiste pas
            return false;
        }
    }
}

function checkbrute($user_id, $mysqli) {
    $now = time();
 
    // On compte toutes les tentative de log des 2 dernières heures. 
    $valid_attempts = $now - (2 * 60 * 60);
 
    if ($stmt = $mysqli->prepare("SELECT time FROM login_attempts WHERE user_id = ? AND time > '$valid_attempts' AND login = 0")) {
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $stmt->store_result();
 
        // Si il y a plus de 5 tentatives 
        if ($stmt->num_rows > 5) {
            return true;
        } else {
            return false;
        }
    }
}

function login_check($mysqli) {
    if (isset($_SESSION['user_id'], $_SESSION['login_string'])) {
 
        $user_id = $_SESSION['user_id'];
        $login_string = $_SESSION['login_string'];
        $user_browser = $_SERVER['HTTP_USER_AGENT'];
 
        if ($stmt = $mysqli->prepare("SELECT password 
                                      FROM members 
                                      WHERE id = ? LIMIT 1")) {
            $stmt->bind_param('i', $user_id);
            $stmt->execute();   
            $stmt->store_result();
 
            if ($stmt->num_rows == 1) {
                $stmt->bind_result($password);
                $stmt->fetch();
                $login_check = hash('sha512', $password . $user_browser);
 
                if (hash_equals($login_check, $login_string) ){
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}


function esc_url($url) {
 
    if ('' == $url) {
        return $url;
    }
 
    $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);
 
    $strip = array('%0d', '%0a', '%0D', '%0A');
    $url = (string) $url;
 
    $count = 1;
    while ($count) {
        $url = str_replace($strip, '', $url, $count);
    }
 
    $url = str_replace(';//', '://', $url);
 
    $url = htmlentities($url);
 
    $url = str_replace('&amp;', '&#038;', $url);
    $url = str_replace("'", '&#039;', $url);
 
    if ($url[0] !== '/') {
        return '';
    } else {
        return $url;
    }
}

function get_collegue($rid, $mysqli) {
    $query = "SELECT email FROM members WHERE role = $rid"; 
    $result = mysqli_fetch_all($mysqli->query($query), MYSQLI_ASSOC);
    if (!empty($result)) {
        return $result;
    } else {
        return false;
    }    
}

function get_roles($mysqli) {
    $query = "SELECT * FROM roles"; 
    $result = mysqli_fetch_all($mysqli->query($query), MYSQLI_ASSOC);
    if (!empty($result)) {
        return $result;
    } else {
        return false;
    }    
}