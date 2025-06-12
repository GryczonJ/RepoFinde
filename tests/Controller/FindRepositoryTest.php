use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\GithubRepositoryService; // Zmień na swoją przestrzeń nazw
use DateTime;

class RepositoryServiceTest extends TestCase
{
    public function testGetListRepositoryNameReturnsJsonResponse()
    {
        // Arrange
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponseData = [
            'total_count' => 1,
            'items' => [
                ['name' => 'test-repo', 'stargazers_count' => 100]
            ]
        ];

        $mockResponse->method('toArray')->willReturn($mockResponseData);
        $mockHttpClient->method('request')->willReturn($mockResponse);

        $service = new GithubRepositoryService($mockHttpClient);

        $date = new DateTime('2024-01-01');
        $lang = 'PHP';

        // Act
        $result = $service->getListRepositoryName(1, 50, $date, $lang);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(200, $result->getStatusCode());

        $data = json_decode($result->getContent(), true);
        $this->assertEquals('test-repo', $data['items'][0]['name']);
        $this->assertEquals(100, $data['items'][0]['stargazers_count']);
    }

    public function testGetListRepositoryNameHandlesException()
    {
        // Arrange
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->method('request')->willThrowException(new \Exception('API error'));

        $service = new GithubRepositoryService($mockHttpClient);

        // Act
        $response = $service->getListRepositoryName(1, 50, null, 'PHP');

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $data);
        $this->assertEquals('API error', $data['error']);
    }
}