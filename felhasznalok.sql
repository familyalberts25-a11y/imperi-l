-- ============================================
--  Imperiál Söröző — Felhasználók adatbázis
--  Futtasd le phpMyAdmin-ban (vagy az italok
--  tábla mellé, ugyanabba az imperial_sorozo
--  adatbázisba)!
-- ============================================

CREATE DATABASE IF NOT EXISTS imperial_sorozo
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_hungarian_ci;

USE imperial_sorozo;

CREATE TABLE IF NOT EXISTS felhasznalok (
  id                    INT AUTO_INCREMENT PRIMARY KEY,
  felhasznalonev        VARCHAR(50)   NOT NULL,
  email                 VARCHAR(150)  NOT NULL,
  jelszo_hash           VARCHAR(255)  NOT NULL,
  elfogadta_feltetelek  TINYINT(1)    NOT NULL DEFAULT 0,
  letrehozva            TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
  utolso_belepes        TIMESTAMP     NULL,
  UNIQUE KEY uq_felhasznalonev (felhasznalonev),
  UNIQUE KEY uq_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

-- Megjegyzés: nincs minta felhasználó feltöltve, mert a jelszót
-- mindig a regisztrációs form hash-eli (password_hash). Regisztrálj
-- egy tesztfiókot a bejelentkező oldalról, úgy biztos jó lesz a hash.
