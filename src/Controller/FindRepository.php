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

    #[Route('/repositories', name: 'getListRepository', methods: ['GET'])]
    /**
     * @OA\Get(
     *     path="/repositories",
     *     summary="Pobiera listę popularnych repozytoriów z GitHub według liczby gwiazdek",
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Numer strony wyników (paginacja)",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Liczba wyników na stronę (max 100)",
     *         @OA\Schema(type="integer", default=50)
     *     ),
     *     @OA\Parameter(
     *         name="DateCreate",
     *         in="query",
     *         description="Repozytoria utworzone po tej dacie (YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="ProgramingLangage",
     *         in="query",
     *         description="Język programowania (np. PHP, Python)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="order",
     *         in="query",
     *         description="Kolejność sortowania wyników: 'desc' lub 'asc'",
     *         @OA\Schema(type="string", default="desc")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista repozytoriów",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Błędne dane wejściowe"
     *     )
     * )
     */
    public function getListRepository(Request $request): JsonResponse
    {
        $page = (int) $request->query->get('page', 1);
        $per_page = (int) $request->query->get('per_page', 50);
        $order = $request->query->get('order', 'desc');
        $ProgramingLangage = $request->query->get('ProgramingLangage');
        $DateCreateString = $request->query->get('DateCreate');
        
        $url = 'https://api.github.com/search/repositories';
       $DateCreate = DateTime::createFromFormat('Y-m-d', $DateCreateString);
       
       if ($ProgramingLangage === null&& $DateCreate === null) {
             $filters = ['stars:>=0']; 
        }
       else {
            if ($ProgramingLangage !== null) {
                $filters[] = 'language:' . $ProgramingLangage;
            }
            if ($DateCreate !== null) {
                    $filters[] = 'created:>' . $DateCreate->format('Y-m-d');
                }
        }

        $query = implode(' ', $filters);
        // Debug
        //     dd([
        // 'final_query_string' => $query,
        // 'request_url' => $url,
        // 'query_params' => [
        //     'q' => $query,
        //     'sort' => 'stars',
        //     'order' => $order,
        //     'per_page' => $per_page,
        //     'page' => $page,
        //     ]
        // ]);

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
                        'Authorization' => 'Bearer ' . $_ENV['GITHUB_TOKEN'], 
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