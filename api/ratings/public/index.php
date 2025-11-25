
require_once __DIR__ . '/../vendor/autoload.php';

use Trekly\Ratings\Infrastructure\Persistence\PdoRatingRepository;
use Trekly\Ratings\Application\GetCreatorAverageRatingUseCase;
use Trekly\Ratings\Interface\Http\RatingController;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

if (preg_match('#^/api/ratings/creator/([^/]+)/average$#', $uri, $matches)) {
    $creatorId = $matches[1];
    if ($method === 'GET') {
        try {
            // Ajusta los datos de conexión según tu entorno
            $pdo = new PDO('mysql:host=localhost;dbname=gotribe;charset=utf8mb4', 'usuario', 'contraseña');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $repository = new PdoRatingRepository($pdo);
            $useCase = new GetCreatorAverageRatingUseCase($repository);
            $controller = new RatingController($useCase);
            $controller->getCreatorAverage($creatorId);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
    }
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Not Found', 'uri' => $uri]);
}
