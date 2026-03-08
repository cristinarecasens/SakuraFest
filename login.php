<?php

require_once 'config_db.php';

// Inicialitzem la variable d’avís
$missatge = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {

        $email = $_POST['email'];
        $password = $_POST['password'];

       //Busquem l'usuari
        $sql = "SELECT id, nom, contrasenya,email FROM usuaris WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_POST['email']]);

        $usuari = $stmt->fetch();

        if (!$usuari) {
    $missatge = "Aquest mail no existeix";
    } elseif (!password_verify($_POST['password'], $usuari['contrasenya'])) {
        $missatge = "Email o contrasenya incorrectes";
    } else {
        // Login correcte
        session_start();
        $_SESSION['user_id'] = $usuari['id'];
        $_SESSION['nom'] = $usuari['nom'];
        $_SESSION['logged'] = 1;

        $_SESSION['session'] = $usuari['nom'];



        // Aquí podrías actualizar la base de datos para poner logged = 1
        $sql_update = "UPDATE usuaris SET logged = 1 WHERE id = ?";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute([$usuari['id']]);

        header("Location: index.php");
        exit;
    }

    } catch (PDOException $e) {
        $missatge = "Error: " . $e->getMessage();
        echo("error");
    }
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Registrar usuari</title>
    <link rel="stylesheet" href="assets/styles/login.css">
    
</head>
<body>
    <div id="div-formulari">
        <h2>Registre</h2>

        <form action="login.php" method="post">
            <?php if ($missatge): ?>
                <div class="avis">
                    <?php echo $missatge; ?>
                </div>
            <?php endif; ?>
            <div class="apartat-formulari">
                <!-- Correu electrònic -->
                <label>Correu electrònic:</label><br/>
                <input type="email" name="email" required>
            </div>

            <div class="apartat-formulari">
                <!-- Contrasenya -->
                <label>Contrasenya:</label><br/>
                <input type="password" name="password" required>
            </div>
            
            <!-- Botó -->
            <div class="apartat-formulari">
                <button type="submit">Entrar</button>
            </div>
        </form>

        <div id="div-boto">
            <button class="boto-no-tinc" onclick="window.location.href='crear_compte.php'">No tinc un compte</button>
        </div>
        
    </div>

</body>
</html>