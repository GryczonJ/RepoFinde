<?php
namespace App\Tests\Controller;
use App\Controller\FindRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\FavoritesService;

class FindRepositoryTest extends TestCase
{
     public function testGetListRepositoryReturnsJsonResponse()
    {
        $expectedData = [
            'items' => [
                ['full_name' => 'symfony/symfony'],
                ['full_name' => 'laravel/laravel'],
            ]
        ];

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('toArray')->willReturn($expectedData);

        $clientMock = $this->createMock(HttpClientInterface::class);
        $clientMock->method('request')->willReturn($responseMock);

        $controller = new FindRepository($clientMock);

        $response = $controller->getListRepository(
            page: 1,
            per_page: 2,
            DateCreate: null,
            ProgramingLangage: "PHP",
            order: "desc"
        );

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->getStatusCode());

        $decodedContent = json_decode($response->getContent(), true);
        $this->assertEquals($expectedData, $decodedContent);
    }

    public function testGetListRepositoryWithPagination()
    {
        $page = 3;
        $perPage = 10;

        $expectedData = [
            'total_count' => 1000,
            'items' => [
                ['full_name' => 'repo/name1'],
                ['full_name' => 'repo/name2'],
            ]
        ];

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->expects($this->once())
            ->method('toArray')
            ->willReturn($expectedData);

        $clientMock = $this->createMock(HttpClientInterface::class);

        $clientMock->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                'https://api.github.com/search/repositories',
              $this->callback(function ($options) use ($page, $perPage) {
                    return isset($options['query']) &&
                        $options['query']['page'] === $page &&
                        $options['query']['per_page'] === $perPage &&
                        str_contains($options['query']['q'], 'language:PHP') &&
                        str_contains($options['query']['q'], 'stars:>=0') &&
                        $options['query']['sort'] === 'stars' &&
                        $options['query']['order'] === 'desc' &&
                        isset($options['headers']['User-Agent']) &&
                        $options['headers']['User-Agent'] === 'SymfonyApp';
                })
            )
            ->willReturn($responseMock);

        $controller = new FindRepository($clientMock);

        $response = $controller->getListRepository(
            $page,
            $perPage,
            null,
            'PHP',
            'desc'
        );

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->getStatusCode());

        $decodedContent = json_decode($response->getContent(), true);
        $this->assertEquals($expectedData, $decodedContent);
    }
}
