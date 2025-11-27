<?php
/**
 * Script de prueba para verificar el sistema de calificaciones
 * Ejecutar desde: http://localhost/gotribe/api/user/test_rating.php
 */

require_once __DIR__ . '/bootstrap.php';

use Trekly\User\Infrastructure\Persistence\DoctrineCreatorRatingRepository;

echo "<h1>Test del Sistema de Calificaciones</h1>";

// Obtener el EntityManager
$entityManager = $GLOBALS['entityManager'] ?? null;

if (!$entityManager) {
    die("Error: EntityManager no disponible");
}

$ratingRepo = new DoctrineCreatorRatingRepository($entityManager);

// Parámetros de prueba
$testMemberId = "test_member_123";
$testCreatorId = "test_creator_456";
$testProjectId = "test_project_789";

echo "<h2>1. Verificando calificación existente</h2>";
echo "Buscando: memberId={$testMemberId}, projectId={$testProjectId}<br>";

$existingRating = $ratingRepo->findByMemberAndProject($testMemberId, $testProjectId);

if ($existingRating) {
    echo "<strong style='color: red;'>✗ ENCONTRADA calificación existente:</strong><br>";
    echo "ID: " . $existingRating->getId() . "<br>";
    echo "Creator ID: " . $existingRating->getCreatorId() . "<br>";
    echo "Member ID: " . $existingRating->getMemberId() . "<br>";
    echo "Project ID: " . $existingRating->getProjectId() . "<br>";
    echo "Rating: " . $existingRating->getRating() . "<br>";
} else {
    echo "<strong style='color: green;'>✓ No se encontró calificación existente (correcto)</strong><br>";
}

echo "<h2>2. Listando TODAS las calificaciones en la BD</h2>";
try {
    $allRatings = $entityManager->getRepository(\Trekly\User\Domain\Rating\CreatorRating::class)->findAll();
    echo "Total de calificaciones: " . count($allRatings) . "<br><br>";
    
    if (count($allRatings) > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Creator ID</th><th>Member ID</th><th>Project ID</th><th>Rating</th><th>Comment</th></tr>";
        foreach ($allRatings as $rating) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($rating->getId()) . "</td>";
            echo "<td>" . htmlspecialchars($rating->getCreatorId()) . "</td>";
            echo "<td>" . htmlspecialchars($rating->getMemberId()) . "</td>";
            echo "<td>" . htmlspecialchars($rating->getProjectId()) . "</td>";
            echo "<td>" . htmlspecialchars($rating->getRating()) . "</td>";
            echo "<td>" . htmlspecialchars($rating->getComment() ?? 'N/A') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<em>No hay calificaciones en la base de datos</em>";
    }
} catch (Exception $e) {
    echo "<strong style='color: red;'>Error: " . $e->getMessage() . "</strong>";
}

echo "<h2>3. Información de la BD</h2>";
echo "Base de datos: " . $_ENV['USER_DB_DATABASE'] . "<br>";
echo "Host: " . $_ENV['DB_HOST'] . "<br>";

echo "<hr>";
echo "<p><a href='test_rating.php'>Recargar</a></p>";
