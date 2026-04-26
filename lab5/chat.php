<?php
/* ============================================================
   chat.php — Backend AJAX pentru chat live
   Face Off Bar
   ============================================================ */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Metodă nepermisă.']);
    exit;
}

$input   = json_decode(file_get_contents('php://input'), true);
$message = mb_strtolower(trim($input['message'] ?? ''), 'UTF-8');

if (empty($message)) {
    echo json_encode(['reply' => 'Scrie-ne ceva și te ajutăm cu drag! 😊']);
    exit;
}

/* ============================================================
   Baza de răspunsuri — keyword matching
   ============================================================ */
$responses = [
    [
        'keywords' => ['salut', 'buna', 'bună', 'hello', 'hi', 'hey', 'servus', 'ciao'],
        'replies'  => [
            'Bună! 👋 Bine ai venit la Face Off Bar. Cu ce te putem ajuta?',
            'Salut! 🍹 Suntem bucuroși să te ajutăm. Ce dorești să știi?',
        ],
    ],
    [
        'keywords' => ['program', 'orar', 'ore', 'ora', 'deschis', 'inchis', 'închis', 'deschid'],
        'replies'  => [
            "Suntem deschiși **zilnic între 05:00 – 03:00**. 🕐\nNe vedem în curând!",
        ],
    ],
    [
        'keywords' => ['rezerv', 'masa', 'masă', 'book', 'tabel', 'loc'],
        'replies'  => [
            "Pentru rezervări ne poți contacta la **☎ 0789 57 760** sau completează formularul de pe pagina **Contact**. 📋",
        ],
    ],
    [
        'keywords' => ['adres', 'unde', 'locatie', 'locație', 'gasesc', 'găsesc', 'harta', 'hartă', 'drum', 'ajung'],
        'replies'  => [
            "Ne găsești la 📍 **Bulevardul Moscova 21, MD-2046, Chișinău**.\nFolosește Google Maps pentru navigație!",
        ],
    ],
    [
        'keywords' => ['cocktail', 'bautur', 'băutur', 'drink', 'mojito', 'martini', 'whisky', 'vodka', 'rom', 'gin'],
        'replies'  => [
            "Avem o selecție rafinată de cocktailuri signature și clasice! 🍹\nVizitează pagina noastră de **Meniu** pentru a vedea toate preparatele și prețurile.",
        ],
    ],
    [
        'keywords' => ['pret', 'preț', 'costa', 'costă', 'cat', 'cât', 'mdl', 'lei'],
        'replies'  => [
            "Cocktailurile noastre pornesc de la **85 MDL**, iar cele signature de la **139 MDL**. ✨\nVezi meniul complet pe pagina **Meniu**.",
        ],
    ],
    [
        'keywords' => ['hookah', 'narghi', 'narghilea', 'shisha'],
        'replies'  => [
            "Oferim narghilele în două variante:\n🌿 **Classic** — 250 MDL\n💎 **Premium** — 300 MDL",
        ],
    ],
    [
        'keywords' => ['cafea', 'espresso', 'cappuccino', 'latte', 'coffee'],
        'replies'  => [
            "Avem preparate speciale de cafea ☕:\n• Espresso — 25 MDL\n• Cappuccino — 35 MDL\n• Latte — 40 MDL",
        ],
    ],
    [
        'keywords' => ['parcare', 'parc'],
        'replies'  => [
            "Există parcare disponibilă în apropierea localului pe Bulevardul Moscova. 🚗",
        ],
    ],
    [
        'keywords' => ['instagram', 'facebook', 'tiktok', 'social', 'media', 'retele', 'rețele'],
        'replies'  => [
            "Ne găsești pe:\n📸 Instagram: **@faceoff_cocktails**\n👍 Facebook: **Face Off**\n🎵 TikTok: **@faceoffcocktails**",
        ],
    ],
    [
        'keywords' => ['eveniment', 'petrecere', 'aniversar', 'zi de nastere', 'nastere', 'naștere', 'party', 'grup'],
        'replies'  => [
            "Organizăm evenimente private și petreceri! 🎉\nContactează-ne la **0789 57 760** sau prin formularul de pe pagina **Contact** pentru detalii și disponibilitate.",
        ],
    ],
    [
        'keywords' => ['wifi', 'internet', 'parola', 'parolă'],
        'replies'  => [
            "Avem Wi-Fi gratuit disponibil pentru clienți. 📶\nParola o primești la sosire!",
        ],
    ],
    [
        'keywords' => ['multumesc', 'mulțumesc', 'mersi', 'merci', 'thanks', 'ok', 'super', 'perfect'],
        'replies'  => [
            'Cu plăcere! 🥂 Te așteptăm la Face Off Bar!',
            'Cu drag! Ne vedem în curând! ✨',
        ],
    ],
    [
        'keywords' => ['pa', 'la revedere', 'bye', 'ciao', 'noapte buna', 'noapte bună'],
        'replies'  => [
            'La revedere! 👋 Te așteptăm cu drag la Face Off Bar!',
        ],
    ],
];

/* ---- Caută primul match de keyword ---- */
$reply = null;
foreach ($responses as $entry) {
    foreach ($entry['keywords'] as $kw) {
        if (mb_strpos($message, $kw, 0, 'UTF-8') !== false) {
            // Alege aleatoriu dintr-un array de răspunsuri
            $reply = $entry['replies'][array_rand($entry['replies'])];
            break 2;
        }
    }
}

/* ---- Răspuns implicit ---- */
if (!$reply) {
    $defaults = [
        "Nu am înțeles exact întrebarea, dar suntem aici să ajutăm! 😊\nSună-ne la **☎ 0789 57 760** sau vizitează pagina **Contact**.",
        "Hmm, nu am găsit un răspuns exact. Încearcă să mă întrebi despre **program**, **rezervări**, **meniu** sau **adresă**! 🍹",
    ];
    $reply = $defaults[array_rand($defaults)];
}

/* ---- Simulare delay natural (opțional) ---- */
// usleep(400000); // 0.4s

echo json_encode([
    'success' => true,
    'reply'   => $reply,
]);
