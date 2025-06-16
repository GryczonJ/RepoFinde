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

    /**
     * Dodaje repozytorium do listy ulubionych dla danego użytkownika.
     * Jeśli użytkownik lub repozytorium jeszcze nie istnieje w zbiorze, zostanie dodane.
     *
     * @param String $userId Identyfikator użytkownika normalnie Uuid (UUID) jako string
     *                       (np. '123e4567-e89b-12d3-a456-426614174000')
     * @param int $repositoryId Identyfikator repozytorium (z GitHuba lub innego źródła)
     *
     * @return void
     */
    public function addFavorite(String $userId, int $repositoryId): void
    {
        if (!isset($this->favorites[$userId])) {
            $this->favorites[$userId] = [];
        }

        if (!in_array($repositoryId, $this->favorites[$userId], true)) {
            $this->favorites[$userId][] = $repositoryId;
        }
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