<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use OpenApi\Annotations as OA;

use DateTime;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response; 



class FindRepository extends AbstractController
{
    /**
     * Klient HTTP używany do wykonywania zapytań do zewnętrznego API (GitHub).
     *
     * @var HttpClientInterface
     */
    private HttpClientInterface $client;
 
    /**
     * Konstruktor kontrolera. Wstrzykuje klienta HTTP.
     *
     * @param HttpClientInterface $client Klient HTTP
     */
    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }
 
    #[Route('/', name: 'getListRepository', methods: ['GET'])]
    /**
     * Pobiera listę popularnych repozytoriów z GitHub według liczby gwiazdek.
     *
     * @param int $page Numer strony wyników (paginacja), domyślnie 1.
     * @param int $per_page Liczba wyników na stronę (max 100 wg GitHub API), domyślnie 50.
     * @param DateTime|null $DateCreate (Opcjonalnie) Repozytoria utworzone po tej dacie.
     * @param string $ProgramingLangage Język programowania (np. PHP, Python, JavaScript).
     * @param string $order Kolejność sortowania wyników: 'desc' (malejąco) lub 'asc' (rosnąco), domyślnie 'desc'.
     *
     * @return Response JSON z wynikami wyszukiwania repozytoriów lub błąd.
     */
    public function getListRepository(int $page = 1, int $per_page = 50, ?DateTime $DateCreate = null, ?string $ProgramingLangage = null, string $order = "desc"): Response
    {
        $url = 'https://api.github.com/search/repositories';

        $filters = ['stars:>=0']; 

        if ($ProgramingLangage !== null) {
            $filters[] = 'language:' . $ProgramingLangage;
        }
        if ($DateCreate !== null) {
            $filters[] = 'created:>' . $DateCreate->format('Y-m-d');
        }

        $query = implode('+', $filters);

         try {
            $response = $this->client->request('GET', $url, [
                    'query' => [
                        'q' => $query,
                        'sort' => 'stars',
                        'order' => $order,
                        'per_page' => $per_page,
                        'page' => $page,
                    ],
                    'headers' => [
                        'Accept' => 'application/vnd.github+json',
                        'User-Agent' => 'SymfonyApp',
                    ],
                ]);

            $data = $response->toArray();
            } 
            catch (Exception $e) {
                 return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
            }
        return new JsonResponse($data); 
    }
}