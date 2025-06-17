<?php
namespace App\Tests\Controller;
use App\Controller\FindRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\FavoritesService;
use Symfony\Component\HttpFoundation\Request;



class FindRepositoryTest extends TestCase
{
    private function mockClientReturning(array $data): HttpClientInterface
    {
        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('toArray')->willReturn($data);

        $clientMock = $this->createMock(HttpClientInterface::class);
        $clientMock->method('request')->willReturn($responseMock);

        return $clientMock;
    }

    public function testResponseIsJsonResponse(): void
    {
        $controller = new FindRepository($this->mockClientReturning([]));

        $request = new Request(query: [
            'page' => 1,
            'per_page' => 5,
            'DateCreate' => null,
            'ProgramingLangage' => "PHP",
            'order' => "desc"
        ]);

        $response = $controller->getListRepository($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    public function testResponseHasStatusCode200(): void
    {
        $controller = new FindRepository($this->mockClientReturning([]));

        $request = new Request(query: [
            'page' => 1,
            'per_page' => 5,
            'DateCreate' => null,
            'ProgramingLangage' => 'PHP',
            'order' => 'desc'
        ]);

        $response = $controller->getListRepository($request);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testResponseReturnsExpectedData(): void
    {
        $expectedData = [
            'items' => [
                ['full_name' => 'symfony/symfony'],
                ['full_name' => 'laravel/laravel'],
            ]
        ];

        $controller = new FindRepository($this->mockClientReturning($expectedData));

        $request = new Request(query: [
            'page' => 1,
            'per_page' => 2,
            'DateCreate' => null,
            'ProgramingLangage' => 'PHP',
            'order' => 'desc'
        ]);

        $response = $controller->getListRepository($request);

        $decodedContent = json_decode($response->getContent(), true);

        $this->assertEquals($expectedData, $decodedContent);
    }

   public function testGitHubApiQueryIsBuiltCorrectly(): void
    {
        $page = 3;
        $perPage = 10;

        $clientMock = $this->createMock(HttpClientInterface::class);

        $clientMock->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                'https://api.github.com/search/repositories',
                $this->anything() // nie sprawdzamy od razu, robimy to w callbacku
            )
            ->willReturnCallback(function ($method, $url, $options) use ($page, $perPage) {
                // Debug - pokaż przekazane opcje
                fwrite(STDERR, print_r($options, true));

                // Sprawdzenie poszczególnych elementów query
                TestCase::assertSame($page, $options['query']['page']);
                TestCase::assertSame($perPage, $options['query']['per_page']);
                TestCase::assertStringContainsString('language:PHP', $options['query']['q']);
                TestCase::assertStringContainsString('stars:>=0', $options['query']['q']);
                TestCase::assertSame('stars', $options['query']['sort']);
                TestCase::assertSame('desc', $options['query']['order']);
                TestCase::assertSame('SymfonyApp', $options['headers']['User-Agent']);

                // Mock odpowiedzi
                $responseMock = TestCase::createMock(ResponseInterface::class);
                $responseMock->method('toArray')->willReturn(['items' => []]);
                return $responseMock;
            });

        $controller = new FindRepository($clientMock);

        $request = new Request(query: [
            'page' => $page,
            'per_page' => $perPage,
            'DateCreate' => null,
            'ProgramingLangage' => 'PHP',
            'order' => 'desc'
        ]);

        // Wywołanie kontrolera
        $controller->getListRepository($request);
    }

}
