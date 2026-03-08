<?php
require_once 'config_db.php';

// Inicialitzem la variable d’avís
$missatge = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {

        $email = $_POST['email'];
        $nom = $_POST['nom'];
        $password = $_POST['password'];

        // Comprovar si el mail existeix
        $sql = "SELECT id FROM usuaris WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $missatge= "Aquest mail ja existeix";
            
        }

        // Inserir usuari
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuaris (nom, email, contrasenya, telefon, poblacio, logged) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nom, $email, $hash, $_POST['telefon'], $_POST['poblacio'],0]);

        // CONSTRASENYA: Abc1234!

        $missatge= "Usuari creat correctament";

        header("Location: login.php");
        exit;

    } catch (PDOException $e) {

        if ($e->getCode() == 23000) {
            $missatge = "Aquest correu ja està registrat.";
        } else {
            $missatge = "S'ha produït un error inesperat.";
        }

    }
}

?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Registre</title>
    <link rel="stylesheet" href="assets/styles/login.css">
</head>
<body>

    <div id="div-formulari">
        <h2>Registre</h2>

        <form action="crear_compte.php" method="post">

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
                <input type="password" name="password" required
                    pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$!]).{8,}"
                    title="Mínim 8 caràcters, una majúscula, una minúscula, un número i un símbol (@ # $ !)">
            </div>

            <!-- Repetir contrasenya -->
            <div class="apartat-formulari">
                <label>Repetir contrasenya:</label><br/>
                <input type="password" name="password2" required>
            </div>
        

            <div class="apartat-formulari">
                <label>Telèfon:</label>
                <div class="telefon-linia">
                    <select name="pais" required>
                        <option value="+34">🇪🇸 +34 España</option>
                        <option value="+1">🇺🇸 +1 Estados Unidos</option>
                        <option value="+44">🇬🇧 +44 Reino Unido</option>
                        <option value="+33">🇫🇷 +33 Francia</option>
                        <option value="+49">🇩🇪 +49 Alemania</option>
                        <option value="+39">🇮🇹 +39 Italia</option>
                        <option value="+52">🇲🇽 +52 México</option>
                        <option value="+54">🇦🇷 +54 Argentina</option>
                        <option value="+55">🇧🇷 +55 Brasil</option>
                        <option value="+61">🇦🇺 +61 Australia</option>
                        <option value="+91">🇮🇳 +91 India</option>
                        <option value="+81">🇯🇵 +81 Japón</option>
                        <option value="+86">🇨🇳 +86 China</option>
                        <option value="+7">🇷🇺 +7 Rusia</option>
                        <option value="+27">🇿🇦 +27 Sudáfrica</option>
                        <option value="+65">🇸🇬 +65 Singapur</option>
                        <option value="+351">🇵🇹 +351 Portugal</option>
                        <option value="+31">🇳🇱 +31 Países Bajos</option>
                        <option value="+41">🇨🇭 +41 Suiza</option>
                        <option value="+43">🇦🇹 +43 Austria</option>
                    </select>
                    <input type="tel" name="telefon" pattern="[0-9]{9}" required
                        title="Introdueix 9 dígits">
                </div>
            </div>

            <!-- Nom i cognom -->
            <div class="apartat-formulari">
                <label>Nom i cognom:</label><br/>
                <input type="text" name="nom" required>
            </div>

            <!-- Població -->
            <div class="apartat-formulari">
                <label>Població:</label><br/>
                <input type="text" name="poblacio" required>
            </div>

            <!-- Botó -->
            <div class="apartat-formulari">
                <button type="submit">Crear compte</button>
            </div>

        </form>

        <div id="div-boto">
            <button class="boto-no-tinc" onclick="window.location.href='login.php'">Ja tinc un compte</button>
        </div>
        
    </div>



</body>
</html>