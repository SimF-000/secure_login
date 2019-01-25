<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
 
sec_session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Secure Login: Page protégée</title>
        <link rel="stylesheet" href="styles/main.css" />
    </head>
    <body>
        <?php if (login_check($mysqli) == true) : ?>
            <p>Bonjour <?php echo htmlentities($_SESSION['email']); ?>!</p>
            <p>Voici votre page <?php echo $_SESSION['role']['label']; ?></p>
            <?php $collegues = get_collegue($_SESSION['role']['id'], $mysqli); ?>
            <table>
            <?php foreach ($collegues as $collegue) { ?>
                <tr><td><?php echo $collegue['email'] ?></td></tr>
            <?php } ?>
            </table>
            <p>Retour à la page de login <a href="index.php">Page de login</a></p>
        <?php else : ?>
            <p>
                <span class="error">Vous n'avez pas l'autorisation nécéssaire. Allez sur la page de </span><a href="index.php">connexion</a>.
            </p>
        <?php endif; ?>
    </body>
</html>