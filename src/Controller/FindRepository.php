<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface; 
use OpenApi\Attributes as OA; 
use Nelmio\ApiDocBundle\Annotation\Model; 
use Nelmio\ApiDocBundle\Annotation\Security;

use DateTime;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response; 

class FindRepository extends AbstractController
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }


    //, page, date, programing langage, language
    /**
     * @param int $per_page
     * @param DateTime $DateCreate
     * @param string $ProgramingLangage
     * @param string $order
     * @return Response
     */
    //paginacjia, try catch 
    #[Route('/popular', name: 'getListRepositoryName', methods: ['GET'])]
    public function getListRepositoryName(int $per_page = 50, DateTime $DateCreate, string $ProgramingLangage, string $order = "desc"): Response
    {
        // Składanie URL-a do wyszukiwania repozytoriów
        $url = 'https://api.github.com/search/repositories';
        
        // Sformatuj datę
        $formattedDate = $DateCreate->format('Y-m-d');

        // Zbuduj zapytanie do GitHub API w czytelny sposób
        $query = 'language:' . $ProgramingLangage . '+created:>' . $formattedDate;

        // Wykonaj zapytanie HTTP
        $response = $this->client->request('GET', $url, [
                'query' => [
                    'q' => $query,
                    'sort' => 'stars',
                    'order' => $order,
                    'per_page' => $per_page,
                ],
                'headers' => [
                    'Accept' => 'application/vnd.github+json',
                    'User-Agent' => 'SymfonyApp',
                ],
            ]);

        $data = $response->toArray();
        return new JsonResponse($data); 
    }


    // Zorbić własną typ by nie dało się wprowadzić innego typu
   public function getAdvancedListRepositoryName(string $type, string $sort ): Response
    {
        // Składanie URL-a do wyszukiwania repozytoriów
        $url = 'https://api.github.com/search/repositories';
        
        // Wykonanie zapytania
        $response = $this->client->request('GET', $url, [
            'query' => [
                'q' => $query,
                'sort' => 'name',
                'order' => $sort,
                'per_page' => $howMany
            ],
            'headers' => [
                'Accept' => 'application/vnd.github+json',
                'User-Agent' => 'SymfonyApp', // GitHub wymaga nagłówka User-Agent
            ],
        ]);

        $data = $response->toArray();

         // Wyciąganie nazw repozytoriów
        $repoNames = array_map(fn($item) => $item['full_name'], $data['items']);
        // Zwracanie odpowiedzi w formacie JSON
        return $this->json($repoNames);
    }
    
    //osobna klasa
    #[Route('/favorites', name: 'addFavorite', methods: ['Get'])]
    public function addFavorite():JsonResponse
    {
        return new JsonResponse(['message' => 'Dodano do ulubionych']);
    }
    
    #[Route('/favorites', name: 'get_favorites', methods: ['GET'])]
    public function getFavorites(): JsonResponse
    {
        // ... logika pobierania ulubionych ...
        return new JsonResponse(/* ... lista ulubionych ... */);
    }
}