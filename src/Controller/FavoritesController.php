<?php

namespace App\Controller;

use OpenApi\Annotations as OA;
use App\Service\FavoritesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response; 

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
     * @OA\Post(
     *     path="/favorites",
     *     summary="Dodaje repozytorium do ulubionych użytkownika",
     *     description="Dodaje repozytorium do ulubionych użytkownika.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"userId","repositoryId"},
     *             @OA\Property(property="userId", type="string", description="Unikalny identyfikator użytkownika"),
     *             @OA\Property(property="repositoryId", type="integer", description="Identyfikator repozytorium")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Repozytorium dodane do ulubionych",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Repozytorium zostało dodane do ulubionych.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Błąd walidacji danych",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Niepoprawne dane wejściowe.")
     *         )
     *     )
     * )
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
     * @OA\Get(
     *     path="/favorites",
     *     summary="Zwraca listę ulubionych repozytoriów dla użytkownika",
     *     description="Zwraca listę ulubionych repozytoriów dla użytkownika.",
     *     @OA\Parameter(
     *         name="userId",
     *         in="query",
     *         required=true,
     *         description="Unikalny identyfikator użytkownika",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista ulubionych repozytoriów",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=123),
     *                 @OA\Property(property="name", type="string", example="repository-name"),
     *                 @OA\Property(property="url", type="string", example="https://github.com/user/repository")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Błąd walidacji danych",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Niepoprawny parametr userId.")
     *         )
     *     )
     * )
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
