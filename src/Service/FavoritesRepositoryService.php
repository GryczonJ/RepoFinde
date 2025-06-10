<?php

namespace App\Service;
use Symfony\Component\Uid\Guid;

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
     * @param Guid $userId       Identyfikator użytkownika (np. UUID lub login)
     * @param int    $repositoryId Identyfikator repozytorium (z GitHuba lub innego źródła)
     *
     * @return void
     */
    public function addFavorite(Guid $userId, int $repositoryId): void
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
     * @param Guid $userId Identyfikator użytkownika
     *
     * @return int[] Tablica ID ulubionych repozytoriów
     */
    public function getFavorites(Guid $userId): array
    {
        return $this->favorites[$userId] ?? [];
    }
}