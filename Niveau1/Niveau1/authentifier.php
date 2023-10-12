<?php
session_start();

// Vérification des informations d'identification (ex. : par rapport à une base de données)
if ($_POST['email'] === 'E-Mail' && $_POST['motpasse'] === 'Mot de passe') {
    // Authentification réussie
    $_SESSION['connected_id'] = true;
    header('Location: admin.php');
    exit();
} else {
    // Authentification échouée
    header('Location: admin.php');
    exit();
    echo "raté";
}
?>
