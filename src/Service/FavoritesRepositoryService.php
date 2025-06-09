<?php

namespace App\Service;

class FavoritesService
{
    private array $favorites = [];

    public function addFavorite(string $userId, int $repositoryId): void
    {
        if (!isset($this->favorites[$userId])) {
            $this->favorites[$userId] = [];
        }

        if (!in_array($repositoryId, $this->favorites[$userId], true)) {
            $this->favorites[$userId][] = $repositoryId;
        }
    }

    public function getFavorites(string $userId): array
    {
        return $this->favorites[$userId] ?? [];
    }
}