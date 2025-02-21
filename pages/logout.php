<?php
session_start();

// Supprime toutes les variables de session
$_SESSION = [];

// Détruit la session
session_destroy();

// Redirige l'utilisateur vers la page de connexion
header('Location: ./login.php');
exit;
?>