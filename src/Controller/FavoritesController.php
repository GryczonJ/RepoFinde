<?php

namespace App\Controller;

use App\Service\FavoritesService;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/users/{userId}/favorites', name: 'addFavorite', methods: ['POST'])]
    /**
     * @OA\Post(
     *     path="/users/{userId}/favorites",
     *     summary="Dodaje repozytorium do ulubionych użytkownika",
     *     description="Dodaje repozytorium do ulubionych użytkownika na podstawie identyfikatora użytkownika i identyfikatora repozytorium.",
     *     tags={"Favorites"},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         description="Unikalny identyfikator użytkownika",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     required={"repositoryId"},
     *                     @OA\Property(property="repositoryId", type="string", description="Identyfikator repozytorium z GitHub")
     *                 )
     *             )
     *         }
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
     *             @OA\Property(property="error", type="string", example="repositoryId jest wymagane")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Nieoczekiwany błąd serwera",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Nieoczekiwany błąd.")
     *         )
     *     )
     * )
     */
    public function addFavorite(string $userId, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        //$userId = $data['userId'] ?? null;
        $repositoryId = $data['repositoryId'] ?? null;

        if (!$userId || !$repositoryId) {
            return new JsonResponse(['error' => 'userId i repositoryId są wymagane'], 400);
        }

        $status = $this->favoritesService->addFavorite($userId, $repositoryId);

        if ($status === 'added') {
            return new JsonResponse(['message' => 'Repozytorium zostało dodane do ulubionych.'], 200);
        }

        if ($status === 'already_exists') {
            return new JsonResponse(['message' => 'Repozytorium już znajduje się na liście ulubionych.'], 200);
        }
        return new JsonResponse(['error' => 'Nieoczekiwany błąd.'], 500);
    }

    #[Route('/users/{userId}/favorites', name: 'getFavorites', methods: ['GET'])]
    /**
     * @OA\Get(
     *     path="/users/{userId}/favorites",
     *     summary="Zwraca listę ulubionych repozytoriów dla użytkownika",
     *     description="Zwraca listę ulubionych repozytoriów dla użytkownika.",
     *     tags={"Favorites"},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
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
     *     )
     * )
     */
    public function getFavorites(string $userId): JsonResponse
    {
        //$userId = $request->get('userId');

        if (!$userId) {
            return new JsonResponse(['error' => 'userId jest wymagany'], 400);
        }

        $favorites = $this->favoritesService->getFavorites($userId);

        return new JsonResponse(['favorites' => $favorites]);
    }
}
