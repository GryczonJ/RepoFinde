<?php
namespace App\Tests\Service;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use App\Service\FavoritesService;

class FavoritesServiceTest extends TestCase
{
    private FavoritesService $service;

    protected function setUp(): void
    {
        $this->service = new FavoritesService();
    }

    public function testAddFavoriteAddsRepositoryForUser(): void
    {
        $userId = Uuid::uuid4()->toString(); // Uuid jako string
        $repoId = 42;

        $this->service->addFavorite($userId, $repoId);

        $this->assertEquals([$repoId], $this->service->getFavorites($userId));
    }

    public function testAddFavoriteDoesNotAddDuplicate(): void
    {
        $userId = Uuid::uuid4()->toString(); // Uuid jako string;
        $repoId = 101;

        $this->service->addFavorite($userId, $repoId);
        $this->service->addFavorite($userId, $repoId); // próbujemy dodać ten sam repozytorium drugi raz

        $favorites = $this->service->getFavorites($userId);

        $this->assertCount(1, $favorites);
        $this->assertEquals([$repoId], $favorites);
    }

    public function testGetFavoritesReturnsEmptyArrayForUnknownUser(): void
    {
        $unknownUser = Uuid::uuid4()->toString(); // Uuid jako string;

        $this->assertEquals([], $this->service->getFavorites($unknownUser));
    }

    public function testAddFavoritesForMultipleUsers(): void
    {
        $user1 = Uuid::uuid4()->toString(); // Uuid jako string;
        $user2 = Uuid::uuid4()->toString(); // Uuid jako string;

        $this->service->addFavorite($user1, 1);
        $this->service->addFavorite($user2, 2);
        $this->service->addFavorite($user2, 3);

        $this->assertEquals([1], $this->service->getFavorites($user1));
        $this->assertEquals([2, 3], $this->service->getFavorites($user2));
    }
}