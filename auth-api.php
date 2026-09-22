<?php
// ============================================
//  Imperiál Söröző — Belépés API
//  POST /auth-api.php?action=register  → regisztráció
//  POST /auth-api.php?action=login     → bejelentkezés
//  POST /auth-api.php?action=logout    → kijelentkezés
//  GET  /auth-api.php?action=me        → ki van bejelentkezve
// ============================================

require_once 'config.php';
session_start();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

function jsonOk($data)  { echo json_encode(['ok' => true,  'data' => $data], JSON_UNESCAPED_UNICODE); exit; }
function jsonErr($msg, $code = 400) { http_response_code($code); echo json_encode(['ok' => false, 'error' => $msg], JSON_UNESCAPED_UNICODE); exit; }

function readBody(): array {
    return json_decode(file_get_contents('php://input'), true) ?? [];
}

try {
    $db = getDB();

    // --- REGISZTRÁCIÓ ---
    if ($method === 'POST' && $action === 'register') {
        $b = readBody();

        $felhasznalonev = trim($b['felhasznalonev'] ?? '');
        $email          = trim($b['email'] ?? '');
        $jelszo         = (string)($b['jelszo'] ?? '');
        $feltetelek     = !empty($b['elfogadta_feltetelek']);

        if ($felhasznalonev === '' || mb_strlen($felhasznalonev) < 3)
            jsonErr('A felhasználónév legalább 3 karakter legyen.');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            jsonErr('Érvénytelen e-mail cím.');
        if (mb_strlen($jelszo) < 6)
            jsonErr('A jelszó legalább 6 karakter legyen.');
        if (!$feltetelek)
            jsonErr('A Feltételek elfogadása kötelező.');

        $stmt = $db->prepare("SELECT id FROM felhasznalok WHERE felhasznalonev = ? OR email = ?");
        $stmt->execute([$felhasznalonev, $email]);
        if ($stmt->fetch()) jsonErr('Ez a felhasználónév vagy e-mail cím már foglalt.');

        $hash = password_hash($jelszo, PASSWORD_BCRYPT);
        $stmt = $db->prepare("INSERT INTO felhasznalok (felhasznalonev, email, jelszo_hash, elfogadta_feltetelek) VALUES (?,?,?,1)");
        $stmt->execute([$felhasznalonev, $email, $hash]);

        jsonOk(['id' => (int)$db->lastInsertId(), 'felhasznalonev' => $felhasznalonev]);
    }

    // --- BEJELENTKEZÉS ---
    if ($method === 'POST' && $action === 'login') {
        $b = readBody();

        $azonosito = trim($b['felhasznalonev'] ?? '');   // felhasználónév VAGY e-mail
        $jelszo    = (string)($b['jelszo'] ?? '');
        $emlekezz  = !empty($b['emlekezz']);

        if ($azonosito === '' || $jelszo === '')
            jsonErr('Add meg a felhasználóneved/e-mail címed és a jelszavad.');

        $stmt = $db->prepare("SELECT * FROM felhasznalok WHERE felhasznalonev = ? OR email = ?");
        $stmt->execute([$azonosito, $azonosito]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($jelszo, $user['jelszo_hash']))
            jsonErr('Hibás felhasználónév vagy jelszó.', 401);

        $upd = $db->prepare("UPDATE felhasznalok SET utolso_belepes = NOW() WHERE id = ?");
        $upd->execute([$user['id']]);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['felhasznalonev'] = $user['felhasznalonev'];

        if ($emlekezz) {
            // 30 napos "emlékezz rám" cookie a session id-hoz
            setcookie(session_name(), session_id(), time() + 60 * 60 * 24 * 30, '/');
        }

        jsonOk([
            'id'             => (int)$user['id'],
            'felhasznalonev' => $user['felhasznalonev'],
            'email'          => $user['email'],
        ]);
    }

    // --- KIJELENTKEZÉS ---
    if ($method === 'POST' && $action === 'logout') {
        $_SESSION = [];
        session_destroy();
        jsonOk(['kijelentkezve' => true]);
    }

    // --- SESSION LEKÉRDEZÉS ---
    if ($method === 'GET' && $action === 'me') {
        if (empty($_SESSION['user_id'])) jsonErr('Nincs bejelentkezve.', 401);
        jsonOk([
            'id'             => (int)$_SESSION['user_id'],
            'felhasznalonev' => $_SESSION['felhasznalonev'],
        ]);
    }

    jsonErr('Ismeretlen művelet.', 404);

} catch (PDOException $e) {
    jsonErr('Adatbázis hiba: ' . $e->getMessage(), 500);
}
