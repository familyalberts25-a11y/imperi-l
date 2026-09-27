<?php
header('Content-Type: application/json; charset=utf-8');

$servername = "localhost";
$dbusername = "root";   // módosítsd, ha szükséges
$dbpassword = "";       // módosítsd, ha szükséges
$dbname     = "imperial_sorozo_db";

$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["ok" => false, "message" => "Adatbázis-kapcsolódási hiba."]);
    exit;
}
$conn->set_charset("utf8mb4");

// A kérés adatai jöhetnek JSON body-ból (amit a HTML oldalak fetch()-csel küldenek)
$input = json_decode(file_get_contents("php://input"), true);
if (!is_array($input)) {
    $input = [];
}
$action = $input['action'] ?? ($_POST['action'] ?? ($_GET['action'] ?? ''));

// -------------------- BEJELENTKEZÉS --------------------
if ($action === 'login') {
    $username = trim($input['username'] ?? '');
    $password = (string)($input['password'] ?? '');

    if ($username === '' || $password === '') {
        echo json_encode(["ok" => false, "message" => "Hiányzó adatok."]);
        exit;
    }

    $stmt = $conn->prepare("SELECT id, name, password FROM felhasznalok WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Támogatja a régi, hash nélküli teszt-adatokat is, de az új jelszavak hash-eltek.
        $stored = $row['password'];
        $isValid = password_verify($password, $stored) || $password === $stored;

        if ($isValid) {
            echo json_encode(["ok" => true, "name" => $row['name']]);
        } else {
            echo json_encode(["ok" => false, "message" => "Hibás felhasználónév vagy jelszó."]);
        }
    } else {
        echo json_encode(["ok" => false, "message" => "Hibás felhasználónév vagy jelszó."]);
    }

    $stmt->close();
    $conn->close();
    exit;
}

// -------------------- REGISZTRÁCIÓ --------------------
if ($action === 'register') {
    $username = trim($input['username'] ?? '');
    $password = (string)($input['password'] ?? '');
    $name     = trim($input['name'] ?? $username);

    if ($username === '' || $password === '') {
        echo json_encode(["ok" => false, "message" => "Hiányzó adatok."]);
        exit;
    }
    if (strlen($password) < 6) {
        echo json_encode(["ok" => false, "message" => "A jelszónak legalább 6 karakter hosszúnak kell lennie."]);
        exit;
    }

    // Ellenőrizzük, hogy létezik-e már ilyen felhasználónév
    $check = $conn->prepare("SELECT id FROM felhasznalok WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        echo json_encode(["ok" => false, "message" => "Ez a felhasználónév már foglalt."]);
        $check->close();
        $conn->close();
        exit;
    }
    $check->close();

    $hash = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO felhasznalok (name, password, username) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $hash, $username);

    if ($stmt->execute()) {
        echo json_encode(["ok" => true]);
    } else {
        echo json_encode(["ok" => false, "message" => "Hiba a regisztráció során."]);
    }

    $stmt->close();
    $conn->close();
    exit;
}

// -------------------- ISMERETLEN MŰVELET --------------------
http_response_code(400);
echo json_encode(["ok" => false, "message" => "Ismeretlen művelet."]);
$conn->close();
