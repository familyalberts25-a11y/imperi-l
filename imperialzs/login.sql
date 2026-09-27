-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1
-- Létrehozás ideje: 2025. Sze 08. 15:00
-- Kiszolgáló verziója: 10.4.32-MariaDB
-- PHP verzió: 8.2.12
--
-- Frissítve: jelszavak bcrypt hash-eléssel (password_hash / password_verify
-- kompatibilis), hogy a service.php biztonságosan tudja kezelni őket.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `imperial_sorozo_db`
--
CREATE DATABASE IF NOT EXISTS `imperial_sorozo_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci;
USE `imperial_sorozo_db`;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `felhasznalok`
--

CREATE TABLE `felhasznalok` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `felhasznalok`
-- (az eredeti jelszavak zárójelben, csak tájékoztatásul; az adatbázisban a hash van tárolva)
--

INSERT INTO `felhasznalok` (`id`, `name`, `password`, `username`) VALUES
(1, 'Kovacs Janos', '$2b$12$j3vy3If8l5egvjJJlNjZv.p4oGsWGcbv/Ym4pMD6r7Qb0LfTJ8cNm', 'Kovi'),        -- jelszo123
(2, 'Nagy Erika',   '$2b$12$2dhApaSxxO17WDK4Fq34Ne0swdXaE6.v9TFesWtb7gQPlAjzDa/8m', 'lopeaonm'),    -- titok456
(3, 'Szabo Peter',  '$2b$12$Bfkz.FPglow5OFHqutPcyOnytgSalGzcPHm8eLCGLae0HxLRhMIRm', 'petya'),       -- pass789
(4, 'Toth Anna',    '$2b$12$ulX9mOjWnQArbe1JZhsfC.goaHcPZ2iYaLeeQEDGU.TWQi7hWHi5O', 'anna'),        -- alma2025
(5, 'Kiss Bela',    '$2b$12$OAY2ogqdX94S.WsRI.IThefX6/93W2ovYe2KD1QRVqzpHRxb/9QQi', 'bela');        -- secure!pass

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `felhasznalok`
--
ALTER TABLE `felhasznalok`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `felhasznalok`
--
ALTER TABLE `felhasznalok`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
