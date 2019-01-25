<?php
$error = filter_input(INPUT_GET, 'err', $filter = FILTER_SANITIZE_STRING);
 
if (! $error) {
    $error = 'Il y a une érreur.';
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Secure Login: Erreur</title>
        <link rel="stylesheet" href="styles/main.css" />
    </head>
    <body>
        <h1>Il y a eu un problème</h1>
        <p class="error"><?php echo $error; ?></p>  
    </body>
</html>