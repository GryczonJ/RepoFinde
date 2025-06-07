<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FindRepository
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
    public function getListRepositoryName(int $per_page = 50, DateTime $DateCreate, string $ProgramingLangage, string $order = "desc"): Responses
    {
        // Składanie URL-a do wyszukiwania repozytoriów
        $url = 'https://api.github.com/search/repositories';
        
        // Wykonanie zapytania
        $response = $this->client->request('GET', $url, [
            'query' => [
                'q' => $query,
                'sort' => 'stars', // sort by star
                'order' => $sort, // sort order A - z
                'per_page' => $per_page, 
                'created:>' => $DateCreate,
                'language'=> $ProgramingLangage
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
        echo json_encode($repoNames);
        return $this->json($repoNames);
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
}