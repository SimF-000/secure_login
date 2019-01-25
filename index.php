<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
 
sec_session_start();
 
if (login_check($mysqli) == true) {
    $logged = 'in';
} else {
    $logged = 'out';
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Secure Login: Connexion</title>
        <link rel="stylesheet" href="styles/main.css" />
        <script type="text/JavaScript" src="js/sha512.js"></script> 
        <script type="text/JavaScript" src="js/forms.js"></script> 
    </head>
    <body>
        <?php
        if (isset($_GET['error'])) {
            echo '<p class="error">Erreur de connexion</p>';
        }
        ?> 
        <form action="includes/process_login.php" method="post" name="login_form">                      
            Email: <input type="text" name="email" />
            Mot de passe: <input type="password" name="password" id="password"/>
            <input type="button" value="Login" onclick="formhash(this.form, this.form.password);" /> 
        </form>
 
        <?php if (login_check($mysqli) == true) { ?>
            <p>Vous êtes conneté <?php echo $logged; ?> en tant que <?php echo htmlentities($_SESSION['email']); ?></p>
            <p>Vous souhaitez changer d'utilisateur ? <a href="includes/logout.php">Déconnexion</a>.</p>
            <p>Vous souhaitez voire votre page de profile ? <a href="protected_page.php">Votre compte</a></p>
        <?php } else { ?>
            <p>Si vous n'avez pas de compte : <a href='register.php'>register</a></p>
        <?php } ?>      
    </body>
</html>