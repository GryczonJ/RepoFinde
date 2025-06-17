<?php

namespace App\Service;
use Ramsey\Uuid\Uuid;
use OpenApi\Annotations as OA;
class FavoritesService
{
     /**
     * Tablica ulubionych repozytoriów przypisana do identyfikatora użytkownika.
     * Kluczem jest identyfikator użytkownika (string), a wartością tablica ID repozytoriów (int[]).
     *
     * @var array<string, int[]>
     */
    private array $favorites = [];

    /** @var string Ścieżka do pliku JSON z ulubionymi */
    private string $filePath = __DIR__ . '/favorites.json';

    /**
     * Konstruktor inicjalizujący serwis ulubionych repozytoriów.
     * Wczytuje dane z pliku JSON, jeśli istnieje.
     *
     * @throws \Exception Jeśli nie można odczytać pliku JSON
     */
    public function __construct()
    {
        // Wczytujemy dane z pliku JSON przy tworzeniu obiektu
        if (file_exists($this->filePath)) {
            $content = file_get_contents($this->filePath);
            $this->favorites = json_decode($content, true) ?: [];
        }
    }

    /**
     * Destruktor zapisujący dane do pliku JSON przy usuwaniu obiektu.
     * Używany do trwałego przechowywania ulubionych repozytoriów.
     */
    public function __destruct()
    {
        // Zapisz tablicę ulubionych do pliku JSON
        $json = json_encode($this->favorites, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($this->filePath, $json);
    }
    /**
     * Dodaje repozytorium do listy ulubionych dla danego użytkownika.
     * Jeśli użytkownik lub repozytorium jeszcze nie istnieje w zbiorze, zostanie dodane.
     *
     * @param string $userId Identyfikator użytkownika (UUID jako string)
     * @param string $repositoryId Identyfikator repozytorium (z GitHuba lub innego źródła)
     *
     * @return string Status dodania: 'added', 'already_exists'
     */
    public function addFavorite(String $userId, string $repositoryId): string
    {
        if (!isset($this->favorites[$userId])) {
            $this->favorites[$userId] = [];
        }

        if (!in_array($repositoryId, $this->favorites[$userId], true)) {
            $this->favorites[$userId][] = $repositoryId;
            return 'added'; 
        }
        return 'already_exists';
    }

    /**
     * Zwraca listę ulubionych repozytoriów danego użytkownika.
     *
     * @param String $userId Identyfikator użytkownika
     *
     * @return int[] Tablica ID ulubionych repozytoriów
     */
    public function getFavorites(String $userId): array
    {
        return $this->favorites[$userId] ?? [];
    }
}