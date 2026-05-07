-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 23 Mar 2026, 15:47
-- Wersja serwera: 10.4.27-MariaDB
-- Wersja PHP: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `zegowskaszama`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `admin`
--

CREATE TABLE `admin` (
  `id` int(10) UNSIGNED NOT NULL,
  `Imie` varchar(35) NOT NULL,
  `Nazwisko` varchar(35) NOT NULL,
  `Login` varchar(35) NOT NULL,
  `Hasło` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `produkty`
--

CREATE TABLE `produkty` (
  `id` int(11) NOT NULL,
  `Nazwa` varchar(100) NOT NULL,
  `Cena` float NOT NULL,
  `Kategoria` varchar(35) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Zrzut danych tabeli `produkty`
--

INSERT INTO `produkty` (`id`, `Nazwa`, `Cena`, `Kategoria`) VALUES
(1, 'Duble dog', 8.5, 'jedzenie'),
(2, 'Buła gołosza', 6.7, 'jedzenie'),
(3, 'Hot-dog klasyczny', 6, 'jedzenie'),
(4, 'Hot-dog XXL', 9.5, 'jedzenie'),
(5, 'Zapiekanka mini', 7.5, 'jedzenie'),
(6, 'Zapiekanka duża', 10, 'jedzenie'),
(7, 'Tost z serem', 5, 'jedzenie'),
(8, 'Tost z szynką i serem', 6.5, 'jedzenie'),
(9, 'Kanapka z szynką', 6.5, 'jedzenie'),
(10, 'Kanapka z serem', 6, 'jedzenie'),
(11, 'Kanapka z kurczakiem', 7.5, 'jedzenie'),
(12, 'Sałatka makaronowa', 8, 'jedzenie'),
(13, 'Drożdżówka z serem', 4, 'przekąski'),
(14, 'Drożdżówka z czekoladą', 4.5, 'przekąski'),
(15, 'Drożdżówka z budyniem', 4.5, 'przekąski'),
(16, 'Pączek z marmoladą', 3.5, 'przekąski'),
(17, 'Pączek z czekoladą', 4, 'przekąski'),
(18, 'Rogal maślany', 3, 'przekąski'),
(19, 'Baton czekoladowy', 4, 'przekąski'),
(20, 'Wafel Prince Polo', 3.5, 'przekąski'),
(21, 'Kinder Bueno', 5.5, 'przekąski'),
(22, 'Chipsy małe', 5.5, 'przekąski'),
(23, 'Chipsy duże', 8, 'przekąski'),
(24, 'Paluszki solone', 3, 'przekąski'),
(25, 'Krakersy', 4.5, 'przekąski'),
(26, 'Orzeszki ziemne', 5, 'przekąski'),
(27, 'Żelki', 4.5, 'przekąski'),
(28, 'Guma do żucia', 2.5, 'przekąski'),
(29, 'Woda mineralna 0.5L', 3, 'napoje'),
(30, 'Woda smakowa 0.5L', 3.5, 'napoje'),
(31, 'Sok owocowy 0.33L', 3.5, 'napoje'),
(32, 'Sok owocowy 1L', 6.5, 'napoje'),
(33, 'Napój gazowany 0.5L', 5, 'napoje'),
(34, 'Napój gazowany 1L', 7.5, 'napoje'),
(35, 'Ice tea 0.5L', 4.5, 'napoje'),
(36, 'Kawa mrożona', 6.5, 'napoje'),
(37, 'Kawa czarna', 5, 'napoje'),
(38, 'Kawa z mlekiem', 5.5, 'napoje'),
(39, 'Herbata ciepła', 3, 'napoje'),
(40, 'Czekolada na gorąco', 6, 'napoje');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `użytkownicy`
--

CREATE TABLE `użytkownicy` (
  `id` int(10) UNSIGNED NOT NULL,
  `Imie` varchar(35) NOT NULL,
  `Nazwisko` varchar(35) NOT NULL,
  `Login` varchar(35) NOT NULL,
  `Hasło` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `zamówienia`
--

CREATE TABLE `zamówienia` (
  `id` int(11) NOT NULL,
  `id_użytkownika` int(11) UNSIGNED NOT NULL,
  `id_produktu` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `Hasło` (`Hasło`);

--
-- Indeksy dla tabeli `produkty`
--
ALTER TABLE `produkty`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Indeksy dla tabeli `użytkownicy`
--
ALTER TABLE `użytkownicy`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `Hasło` (`Hasło`),
  ADD KEY `id` (`id`);

--
-- Indeksy dla tabeli `zamówienia`
--
ALTER TABLE `zamówienia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_użytkownika` (`id_użytkownika`),
  ADD KEY `id_produktu` (`id_produktu`);

--
-- AUTO_INCREMENT dla zrzuconych tabel
--

--
-- AUTO_INCREMENT dla tabeli `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `produkty`
--
ALTER TABLE `produkty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT dla tabeli `użytkownicy`
--
ALTER TABLE `użytkownicy`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `zamówienia`
--
ALTER TABLE `zamówienia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `zamówienia`
--
ALTER TABLE `zamówienia`
  ADD CONSTRAINT `zamówienia_ibfk_1` FOREIGN KEY (`id_produktu`) REFERENCES `produkty` (`id`),
  ADD CONSTRAINT `zamówienia_ibfk_2` FOREIGN KEY (`id_użytkownika`) REFERENCES `użytkownicy` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
