<?php
include_once 'includes/register.inc.php';
include_once 'includes/functions.php';

$roles = get_roles($mysqli);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Secure Login: Formulaire d'incription</title>
        <script type="text/JavaScript" src="js/sha512.js"></script> 
        <script type="text/JavaScript" src="js/forms.js"></script>
        <link rel="stylesheet" href="styles/main.css" />
    </head>
    <body>
        <h1>Incription</h1>
        <?php
        if (!empty($error_msg)) {
            echo $error_msg;
        }
        ?>
        <ul>
            <li>Le mot de passe doit avoir au moins 6 charactères</li>
            <li>le mot de passe doit contenir
                <ul>
                    <li>Au moins une majuscule (A..Z)</li>
                    <li>Au moins une minuscule (a..z)</li>
                    <li>Au moins un nombre (0..9)</li>
                </ul>
            </li>
        </ul>
        <form action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post" name="registration_form">
            <?php if ($roles) { ?>
            Rôle: 
            <select name="role" id="role">
                <?php foreach ($roles as $role) { ?>
                    <option value=<?php echo $role['rid']; ?> <?php if ($role['rid'] == 2) { ?> selected="selected" <?php } ?>>
                        <?php echo $role['label']; ?>
                        </option>    
                <?php } ?>
            </select>
            <?php } ?>
            Email: <input type="text" name="email" id="email" /><br>
            
            Password: <input type="password" name="password" id="password"/><br>
            
            Confirm password: <input type="password" name="confirmpwd" id="confirmpwd" /><br>
            
            <input type="button" value="Register" 
                onclick="return regformhash(this.form,
                   this.form.email,
                   this.form.password,
                   this.form.confirmpwd);" 
           />

        </form>
        <p>Retour à la page de <a href="index.php">login</a>.</p>
    </body>
</html>