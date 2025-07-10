# The Backend Demo

Projekt demonstracyjny backendu służący do wyszukiwania popularnych repozytoriów na GitHubie.

## 📋 Opis funkcjonalności

Aplikacja umożliwia:

- 🔍 Pobieranie listy najpopularniejszych repozytoriów z GitHuba, posortowanych według liczby gwiazdek.
- 🔟 Możliwość przeglądania Top 10, 50 lub 100 repozytoriów.
- 📆 Filtrowanie repozytoriów na podstawie daty utworzenia.
- 🧑‍💻 Filtrowanie wyników według języka programowania.
- ⭐ (Bonus) Oznaczanie repozytoriów jako ulubione (przechowywane w pamięci aplikacji).

## ⚙️ Technologie

- PHP 8.x
- [Docker](https://www.docker.com/)
- Composer
- GitHub REST API
- Laravel 
- Swagger
- PHPUnit – testy jednostkowe

## 🚀 Uruchomienie projektu (Docker)

1. **Klonowanie repozytorium**

   ```bash
   git clone https://github.com/GryczonJ/RepoFinde.git
   cd RepoFinde

2. **Utworzenie plików konfiguracyjnych**
na podstwie pliku .env-example utowżyć plik .env i uzupełnić puste luki. (podać klucz do API GitHub)

3. **Uruchomienie kontenerów**
docker-compose up -d --build

4. **Dostęp do aplikacji**

http://localhost:8080 <= tu już działa endpoint

5. **Uzupełnij plik .env**
Pole: GITHUB_TOKEN=''

6. **Dokumentacjia**
http://localhost:8080/api/doc