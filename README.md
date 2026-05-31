# 🌟 Zegowska Szama — Internetowy System Obsługi Sklepiku Szkolnego

![Wersja](https://img.shields.io/badge/wersja-1.0.0--stable-brightgreen?style=for-the-badge)
![PHP](https://img.shields.io/badge/PHP-7.4%20%7C%208.x-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6+-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)

**Zegowska Szama** to nowoczesna, wysoce responsywna i bezpieczna platforma e-commerce, stworzona z myślą o pełnej automatyzacji szkolnego sklepiku. System całkowicie eliminuje problem długich kolejek na przerwach, pozwalając uczniom na błyskawiczne, asynchroniczne zamawianie produktów online z opcją odbioru osobistego. Z drugiej strony, administratorzy zyskują potężne narzędzie do monitorowania zamówień i zarządzania biznesem w czasie rzeczywistym.

---

## 🚀 Kluczowe Funkcjonalności (Co urywa głowę?)

### 👨‍🎓 Panel Ucznia (Klienta)
* **Dynamiczny Sklep (Live Storefront):** Przeglądanie asortymentu w intuicyjnych kategoriach (Jedzenie, Napoje, Przekąski) z asynchronicznym filtrowaniem.
* **Wyszukiwarka Instant Search:** Moduł oparty na Fetch API, aktualizujący widok produktów natychmiast po wpisaniu znaku — bez przeładowywania strony!
* **Inteligentny Koszyk:** Błyskawiczne podsumowanie wybranych produktów, dynamiczne obliczanie kwoty do zapłaty i płynna finalizacja.
* **Centrum Dowodzenia:** Edycja danych profilowych, transparentna historia zamówień ze statusami (*W drodze* / *Dostarczone*) oraz opcja całkowitego, bezpiecznego usunięcia konta.

### 👑 Panel Administratora (System ERP)
* **Kontrola Bazy Użytkowników:** Przejrzysty podgląd zarejestrowanych uczniów wraz z narzędziami do natychmiastowej moderacji i kasowania profili.
* **Zarządzanie Asortymentem (CMS):** Pełna władza nad ofertą — wprowadzanie nowości i błyskawiczne ściąganie wyprzedanych hitów z wirtualnych półek.
* **Pipeline Zamówień:** Monitorowanie zgłoszeń z podziałem na statusy. Przyspiesza to kompletowanie szamy i drastycznie redukuje chaos na przerwach.

---

## 🛠️ Stack Technologiczny

* **Backend:** PHP (zoptymalizowany proceduralny rdzeń zapewniający maksymalną prędkość i niezawodność).
* **Frontend:** HTML5, CSS3, JavaScript (ES6+ z wykorzystaniem nowoczesnego, asynchronicznego API).
* **UI/UX:** Bootstrap 5.3.3 (Zaprojektowany w duchu Mobile-First — sklep prezentuje się i działa perfekcyjnie na ekranie każdego smartfona oraz na desktopie).
* **Baza Danych:** MySQL / MariaDB.

---

## 🔒 Standardy Bezpieczeństwa (Security First)

Aplikacja nie idzie na kompromisy, jeśli chodzi o bezpieczeństwo:
1. **Ochrona przed SQL Injection:** Wszystkie operacje wprowadzania danych przepuszczane są przez sterylizator `mysqli_real_escape_string()`.
2. **Kryptografia haseł:** Zapomnij o trzymaniu haseł plain-textem. System wykorzystuje solidne algorytmy szyfrowania (`password_hash`), aby chronić wrażliwe dane klientów.
3. **Hermetyzacja Sesji:** Ścisła weryfikacja tokenów sesji (`$_SESSION`). Próba wtargnięcia na podstronę bez odpowiednich uprawnień skutkuje natychmiastowym odbiciem na stronę logowania. 

---

## 💾 Model Bazy Danych (ERD Overview)

Solidny szkielet platformy działa w oparciu o wysoce relacyjną strukturę obsługującą potężne zapytania typu JOIN:
* **`użytkownicy`**: Rejestr profili uczniów (ID, imię, nazwisko, email, zaszyfrowane hasło).
* **`admin`**: Izolowana tabela dedykowana wyłącznie poświadczeniom dostępu na szczeblu root.
* **`produkty`**: Wirtualny magazyn zawierający parametry takie jak nazwa, cena, kategoria i korelacja obrazu.
* **`koszyk`**: Tabela pośrednicząca (Most) — w locie paruje użytkownika z wybranymi przez niego smakołykami.
* **`zamówienia`**: Globalny rejestr transakcji przypisujący koszyki do numerów indeksów i spinający to znacznikiem czasu oraz statusem dostawy.

---

## ⚙️ Szybki Start (Instalacja)

Zegowska Szama jest gotowa do uruchomienia w każdym standardowym środowisku lokalnym (XAMPP/WAMP/Laragon) oraz na produkcyjnych serwerach VPS/Shared.

1. Skonfiguruj serwer obsługujący PHP z podpiętą bazą MySQL.
2. Utwórz czystą bazę danych o nazwie `zegowskaszama` (zalecane kodowanie: `utf8mb4_general_ci`).
3. Zaimportuj architekturę tabel przy pomocy dołączonego do projektu pliku SQL.
4. Przekopiuj całą zawartość repozytorium do wybranego folderu publicznego na swoim serwerze.
5. Jeśli wdrażasz system na zewnętrznym hostingu — nie zapomnij zaktualizować poświadczeń (użytkownik, hasło, host) w funkcjach `mysqli_connect()`.
6. Odpal w przeglądarce plik wejściowy (logowanie/sklep) i gotowe!

---

## 📈 Roadmapa (Rozwój platformy)

* **Inventory Control 2.0:** System samodzielnie zablokuje możliwość zakupu produktu, gdy wirtualny stan magazynowy spadnie do zera.
* **Gospodarka Bezgotówkowa:** Integracja z rynkowymi bramkami płatniczymi (BLIK / PayU) do opłacania zamówień z wyprzedzeniem.
* **Kody Promocyjne:** Algorytm przeliczania zniżek procentowych w koszyku dla stałych bywalców sklepiku.
