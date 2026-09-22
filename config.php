<?php
// ============================================
//  Imperiál Söröző — Adatbázis kapcsolat
//  Ugyanaz, mint az italok-menühöz: ha már
//  van config.php a szerveren, nem kell újra
//  feltölteni, ez a fájl azzal megegyezik.
//  Módosítsd ha más jelszót használsz!
// ============================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // XAMPP alapértelmezett
define('DB_PASS', '');           // XAMPP alapértelmezett (üres)
define('DB_NAME', 'imperial_sorozo');
define('DB_CHARSET', 'utf8mb4');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    }
    return $pdo;
}
