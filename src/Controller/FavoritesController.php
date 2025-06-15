<?php

namespace App\Controller;

use App\Service\FavoritesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Ramsey\Uuid\Uuid;

class FavoritesController extends AbstractController
{
    /**
     * Kontroler obsługujący dodawanie i pobieranie ulubionych repozytoriów dla użytkownika.
     */
    private FavoritesService $favoritesService;

    /**
     * Konstruktor z wstrzykiwaniem serwisu ulubionych repozytoriów.
     *
     * @param FavoritesService $favoritesService Serwis przechowujący ulubione repozytoria w pamięci.
     */
    public function __construct(FavoritesService $favoritesService)
    {
        $this->favoritesService = $favoritesService;
    }

    #[Route('/favorites', name: 'addFavorite', methods: ['POST'])]
    /**
     * Dodaje repozytorium do ulubionych użytkownika.
     *
     * Oczekiwane parametry w zapytaniu POST:
     * - userId (string): unikalny identyfikator użytkownika
     * - repositoryId (int): identyfikator repozytorium
     *
     * @param Request $request Obiekt żądania HTTP
     * @return JsonResponse Komunikat o sukcesie lub błąd walidacji
     */
    public function addFavorite(Request $request): JsonResponse
    {
        $userId = $request->get('userId');
        $repositoryId = (int) $request->get('repositoryId');

        if (!$userId || !$repositoryId) {
            return new JsonResponse(['error' => 'userId i repositoryId są wymagane'], 400);
        }

        $this->favoritesService->addFavorite($userId, $repositoryId);

        return new JsonResponse(['message' => 'Dodano do ulubionych']);
    }

    #[Route('/favorites', name: 'get_favorites', methods: ['GET'])]
    /**
     * Zwraca listę ulubionych repozytoriów dla użytkownika.
     *
     * Oczekiwany parametr w zapytaniu GET:
     * - userId (string): unikalny identyfikator użytkownika
     *
     * @param Request $request Obiekt żądania HTTP
     * @return JsonResponse Lista ulubionych repozytoriów lub błąd walidacji
     */
    public function getFavorites(Request $request): JsonResponse
    {
        $userId = $request->get('userId');

        if (!$userId) {
            return new JsonResponse(['error' => 'userId jest wymagany'], 400);
        }

        $favorites = $this->favoritesService->getFavorites($userId);

        return new JsonResponse(['favorites' => $favorites]);
    }
}
