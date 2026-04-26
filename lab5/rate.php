<?php
/* ============================================================
   rate.php — Backend AJAX pentru sistemul de rating
   Face Off Bar
   ============================================================ */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');

$ratingsFile = __DIR__ . '/ratings.json';

// ---- Încarcă ratingurile existente ----
$ratings = [];
if (file_exists($ratingsFile)) {
    $raw = file_get_contents($ratingsFile);
    $ratings = json_decode($raw, true) ?: [];
}

// ---- GET: returnează toate ratingurile ----
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode($ratings);
    exit;
}

// ---- POST: adaugă un vot nou ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $itemId = isset($input['item']) ? preg_replace('/[^a-z0-9_-]/i', '', $input['item']) : '';
    $stars  = isset($input['stars']) ? (int)$input['stars'] : 0;

    // Validare
    if (!$itemId || $stars < 1 || $stars > 5) {
        http_response_code(400);
        echo json_encode(['error' => 'Date invalide.']);
        exit;
    }

    // Actualizează sau inițializează
    if (!isset($ratings[$itemId])) {
        $ratings[$itemId] = ['total' => 0, 'count' => 0];
    }

    $ratings[$itemId]['total'] += $stars;
    $ratings[$itemId]['count']++;

    // Salvează în fișier
    if (file_put_contents($ratingsFile, json_encode($ratings, JSON_PRETTY_PRINT)) === false) {
        http_response_code(500);
        echo json_encode(['error' => 'Nu s-a putut salva ratingul.']);
        exit;
    }

    $avg   = $ratings[$itemId]['total'] / $ratings[$itemId]['count'];
    $count = $ratings[$itemId]['count'];

    echo json_encode([
        'success' => true,
        'item'    => $itemId,
        'avg'     => round($avg, 1),
        'count'   => $count,
    ]);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Metodă nepermisă.']);
