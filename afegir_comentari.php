<?php
require_once 'config_db.php';

// Inicialitzem la variable d’avís
$missatge = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {

        $text = $_POST['comentari'];
        $id_activitat = $_POST['id_activitat'];
        
        session_start();
        $user_name = $_SESSION['session'] ?? null;

        // Obtenir l'ID de l'usuari a partir del nom d'usuari
        $sql_user = "SELECT id FROM usuaris WHERE nom = ?";
        $stmt_user = $pdo->prepare($sql_user);
        $stmt_user->execute([$user_name]);
        $user = $stmt_user->fetch();

        if (!$user) {
            die("Error: No s'ha pogut trobar l'usuari.");
        }

        $id_user = $user['id'];

        $sql = "INSERT INTO comentaris (`text`, activitat_id, user_id, positiu, negatiu, data_hora) VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$text, $id_activitat, $id_user, 0, 0]);

        header("Location: fitxa.php?id=" . $id_activitat);
        exit;

    } catch (PDOException $e) {
        $missatge = "S'ha produït un error inesperat.";
    }
}

?>
