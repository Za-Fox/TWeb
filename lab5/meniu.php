<?php
/* ============================================================
   Lucrarea de laborator Nr.3 + AJAX Rating — meniu.php
   Face Off Bar — Meniu cu sistem de rating
   ============================================================ */

function slugify(string $str): string {
    $map = ['ă'=>'a','â'=>'a','î'=>'i','ș'=>'s','ț'=>'t',"'"=>'-',' '=>'-'];
    $str = mb_strtolower($str, 'UTF-8');
    $str = strtr($str, $map);
    $str = preg_replace('/[^a-z0-9-]/', '', $str);
    $str = preg_replace('/-+/', '-', $str);
    return trim($str, '-');
}

$meniu = [
    'cocktails' => [
        'label' => 'Cocktails',
        'cards' => [
            ['title' => 'Signature', 'items' => [
                ['name' => 'Verde Mortal',   'price' => '139 MDL'],
                ['name' => 'El Contrabando', 'price' => '139 MDL'],
                ['name' => 'Oro y Trufa',    'price' => '159 MDL'],
            ]],
            ['title' => 'Clasice', 'items' => [
                ['name' => 'Mojito',        'price' => '85 MDL'],
                ['name' => 'Martini',       'price' => '90 MDL'],
                ['name' => 'Old Fashioned', 'price' => '95 MDL'],
            ]],
        ],
    ],
    'alcoolice' => [
        'label' => 'Băuturi Alcoolice',
        'cards' => [
            ['title' => 'Whisky', 'items' => [
                ['name' => 'Jameson',       'price' => '70 MDL'],
                ['name' => "Jack Daniel's", 'price' => '75 MDL'],
            ]],
            ['title' => 'Vodka', 'items' => [
                ['name' => 'Absolut',    'price' => '65 MDL'],
                ['name' => 'Grey Goose', 'price' => '85 MDL'],
            ]],
        ],
    ],
    'non-alcoolice' => [
        'label' => 'Băuturi Non-Alcoolice',
        'cards' => [
            ['title' => 'Fresh & Sucuri', 'items' => [
                ['name' => 'Fresh Portocale', 'price' => '45 MDL'],
                ['name' => 'Limonadă',        'price' => '40 MDL'],
            ]],
            ['title' => 'Răcoritoare', 'items' => [
                ['name' => 'Coca-Cola', 'price' => '30 MDL'],
                ['name' => 'Red Bull',  'price' => '45 MDL'],
            ]],
        ],
    ],
    'cafea' => [
        'label' => 'Cafea',
        'cards' => [
            ['title' => 'Preparate', 'items' => [
                ['name' => 'Espresso',   'price' => '25 MDL'],
                ['name' => 'Cappuccino', 'price' => '35 MDL'],
                ['name' => 'Latte',      'price' => '40 MDL'],
            ]],
        ],
    ],
    'hookah' => [
        'label' => 'Hookah',
        'cards' => [
            ['title' => 'Narghilea', 'items' => [
                ['name' => 'Classic', 'price' => '250 MDL'],
                ['name' => 'Premium', 'price' => '300 MDL'],
            ]],
        ],
    ],
];

$activCat   = isset($_GET['cat']) ? htmlspecialchars($_GET['cat']) : 'toate';
$totalItems = array_sum(array_map(fn($c) => array_sum(array_map(fn($k) => count($k['items']), $c['cards'])), $meniu));
?>
<!DOCTYPE html>
<html lang="ro">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Meniu — Face Off Bar</title>
  <link rel="stylesheet" href="shared.css" />
  <link rel="stylesheet" href="meniu.css" />
  <style>
    /* ================================================================
       Star Rating
       ================================================================ */
    .menu-item {
      flex-direction: column;
      align-items: flex-start;
      gap: 0;
      padding: 11px 8px 10px;
    }
    .menu-item-top {
      display: flex;
      justify-content: space-between;
      align-items: center;
      width: 100%;
    }
    .star-widget {
      display: flex;
      align-items: center;
      gap: 5px;
      margin-top: 8px;
      user-select: none;
    }
    .star-btn {
      background: none;
      border: none;
      padding: 1px;
      cursor: pointer;
      font-size: .95rem;
      line-height: 1;
      color: rgba(255,255,255,.1);
      transition: color .12s, transform .12s;
      outline: none;
    }
    .star-widget:not([data-voted="true"]) .star-btn:hover { transform: scale(1.25); }
    .star-btn.filled {
      color: #d4a017;
      text-shadow: 0 0 7px rgba(212,160,23,.5);
    }
    .star-widget[data-voted="true"] .star-btn { cursor: default; }
    .rating-info {
      font-size: .7rem;
      color: #555;
      letter-spacing: .3px;
      margin-left: 3px;
      min-width: 78px;
    }
    .star-widget.rated .rating-info { color: #d4a017; }
    .rating-cta {
      font-size: .65rem;
      color: #3a3a3a;
      letter-spacing: 1px;
      text-transform: uppercase;
    }
    .star-widget.rated .rating-cta { display: none; }

    @keyframes ratingPop {
      0%  { transform: scale(.8); opacity: 0; }
      60% { transform: scale(1.12); }
      100%{ transform: scale(1);   opacity: 1; }
    }
    @keyframes widgetPulse {
      0%,100%{ transform: scale(1); }
      50%    { transform: scale(1.04); }
    }
    .star-widget.pulse { animation: widgetPulse .5s ease; }

    #rating-toast {
      position: fixed;
      bottom: 90px; left: 50%;
      transform: translateX(-50%) translateY(14px);
      background: rgba(212,160,23,.95);
      color: #000;
      padding: 10px 24px;
      border-radius: 30px;
      font-size: .82rem;
      font-weight: 600;
      letter-spacing: .5px;
      pointer-events: none;
      opacity: 0;
      transition: opacity .28s, transform .28s;
      z-index: 999;
      white-space: nowrap;
      box-shadow: 0 4px 22px rgba(0,0,0,.5);
    }
    #rating-toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }

    .php-stats {
      color: var(--text-muted);
      font-size: .88rem;
      margin-bottom: 24px;
      padding: 12px 18px;
      background: var(--bg2);
      border: 1px solid var(--border);
      border-radius: var(--radius);
    }
  </style>
</head>
<body>

  <header>
    <h1>Meniu Face Off</h1>
    <p>Selecția noastră de cocktailuri &amp; băuturi</p>
  </header>

  <nav>
    <a href="acasa.php">Acasă</a>
    <a href="meniu.php" class="nav-activ">Meniu</a>
    <a href="galerie.php">Galerie</a>
    <a href="contact.php">Contact</a>
  </nav>

  <div class="container">

    <p class="php-stats reveal">
      📋 Meniul nostru conține <strong style="color:var(--gold)"><?= $totalItems ?> produse</strong>
      în <strong style="color:var(--gold)"><?= count($meniu) ?> categorii</strong>.
    </p>

    <div class="search-wrap reveal">
      <input type="text" id="menu-search" placeholder="Caută în meniu..." />
    </div>

    <div class="filter-bar reveal">
      <button class="filter-btn <?= $activCat==='toate'?'activ':'' ?>" data-cat="toate">Noi Oferim:</button>
      <?php foreach ($meniu as $catKey => $catData): ?>
        <button class="filter-btn <?= $activCat===$catKey?'activ':'' ?>" data-cat="<?= htmlspecialchars($catKey) ?>">
          <?= htmlspecialchars($catData['label']) ?>
        </button>
      <?php endforeach; ?>
    </div>

    <?php foreach ($meniu as $catKey => $catData):
      $hidden = ($activCat !== 'toate' && $activCat !== $catKey) ? ' hidden' : '';
    ?>
      <div class="cat-section<?= $hidden ?>" data-cat="<?= htmlspecialchars($catKey) ?>">
        <div class="section-title"><?= htmlspecialchars($catData['label']) ?></div>
        <div class="menu-grid">
          <?php foreach ($catData['cards'] as $card): ?>
            <div class="menu-card">
              <h3><?= htmlspecialchars($card['title']) ?></h3>
              <?php foreach ($card['items'] as $item):
                $slug = slugify($item['name']);
              ?>
                <div class="menu-item">
                  <div class="menu-item-top">
                    <span><?= htmlspecialchars($item['name']) ?></span>
                    <span class="price"><?= htmlspecialchars($item['price']) ?></span>
                  </div>
                  <div class="star-widget" data-id="<?= $slug ?>" data-voted="false" data-current="0">
                    <?php for ($s = 1; $s <= 5; $s++): ?>
                      <button class="star-btn" data-val="<?= $s ?>" title="<?= $s ?> <?= $s===1?'stea':'stele' ?>">★</button>
                    <?php endfor; ?>
                    <span class="rating-info">– voturi</span>
                    <span class="rating-cta">Votează</span>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>

    <a href="acasa.php" class="btn" style="margin-top:10px;">← Înapoi la Acasă</a>
  </div>

  <div id="rating-toast">✓ Rating salvat!</div>

  <script src="efecte.js"></script>
  <script>
    /* Filtrare */
    document.querySelectorAll('.filter-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('activ'));
        btn.classList.add('activ');
        const cat = btn.dataset.cat;
        document.querySelectorAll('.cat-section').forEach(sec => {
          sec.classList.toggle('hidden', cat !== 'toate' && sec.dataset.cat !== cat);
        });
      });
    });

    /* Căutare live */
    document.getElementById('menu-search').addEventListener('input', function() {
      const q = this.value.toLowerCase().trim();
      document.querySelectorAll('.menu-item').forEach(item => {
        const name = item.querySelector('.menu-item-top span:first-child')?.textContent.toLowerCase() || '';
        item.style.display = (!q || name.includes(q)) ? '' : 'none';
      });
      document.querySelectorAll('.menu-card').forEach(card => {
        const visible = [...card.querySelectorAll('.menu-item')].some(i => i.style.display !== 'none');
        card.style.display = visible ? '' : 'none';
      });
    });

    /* Toast */
    const toast = document.getElementById('rating-toast');
    window.onRatingSuccess = function(avg, count) {
      const label = count === 1 ? 'vot' : 'voturi';
      toast.textContent = `✓ Mulțumim! ${avg.toFixed(1)} ★ din 5 (${count} ${label})`;
      toast.classList.add('show');
      setTimeout(() => toast.classList.remove('show'), 2800);
    };
  </script>

  <script src="rating.js"></script>
  <script src="chat_widget.js"></script>

</body>
</html>
