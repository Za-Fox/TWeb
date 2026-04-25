<?php
/* ============================================================
   Lucrarea de laborator Nr.3 — meniu.php
   Face Off Bar — Meniu
   ============================================================ */

// ---- Datele meniului definite în PHP (array asociativ) ----
$meniu = [
    'cocktails' => [
        'label' => 'Cocktails',
        'cards' => [
            [
                'title' => 'Signature',
                'items' => [
                    ['name' => 'Verde Mortal',   'price' => '139 MDL'],
                    ['name' => 'El Contrabando', 'price' => '139 MDL'],
                    ['name' => 'Oro y Trufa',    'price' => '159 MDL'],
                ],
            ],
            [
                'title' => 'Clasice',
                'items' => [
                    ['name' => 'Mojito',        'price' => '85 MDL'],
                    ['name' => 'Martini',       'price' => '90 MDL'],
                    ['name' => 'Old Fashioned', 'price' => '95 MDL'],
                ],
            ],
        ],
    ],
    'alcoolice' => [
        'label' => 'Băuturi Alcoolice',
        'cards' => [
            [
                'title' => 'Whisky',
                'items' => [
                    ['name' => 'Jameson',      'price' => '70 MDL'],
                    ['name' => "Jack Daniel's", 'price' => '75 MDL'],
                ],
            ],
            [
                'title' => 'Vodka',
                'items' => [
                    ['name' => 'Absolut',    'price' => '65 MDL'],
                    ['name' => 'Grey Goose', 'price' => '85 MDL'],
                ],
            ],
        ],
    ],
    'non-alcoolice' => [
        'label' => 'Băuturi Non-Alcoolice',
        'cards' => [
            [
                'title' => 'Fresh & Sucuri',
                'items' => [
                    ['name' => 'Fresh Portocale', 'price' => '45 MDL'],
                    ['name' => 'Limonadă',        'price' => '40 MDL'],
                ],
            ],
            [
                'title' => 'Răcoritoare',
                'items' => [
                    ['name' => 'Coca-Cola', 'price' => '30 MDL'],
                    ['name' => 'Red Bull',  'price' => '45 MDL'],
                ],
            ],
        ],
    ],
    'cafea' => [
        'label' => 'Cafea',
        'cards' => [
            [
                'title' => 'Preparate',
                'items' => [
                    ['name' => 'Espresso',   'price' => '25 MDL'],
                    ['name' => 'Cappuccino', 'price' => '35 MDL'],
                    ['name' => 'Latte',      'price' => '40 MDL'],
                ],
            ],
        ],
    ],
    'hookah' => [
        'label' => 'Hookah',
        'cards' => [
            [
                'title' => 'Narghilea',
                'items' => [
                    ['name' => 'Classic', 'price' => '250 MDL'],
                    ['name' => 'Premium', 'price' => '300 MDL'],
                ],
            ],
        ],
    ],
];

// ---- Categoria activă (din GET, default = toate) ----
$activCat = isset($_GET['cat']) ? htmlspecialchars($_GET['cat']) : 'toate';

// ---- Număr total de preparate (calculat în PHP) ----
$totalItems = 0;
foreach ($meniu as $cat) {
    foreach ($cat['cards'] as $card) {
        $totalItems += count($card['items']);
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Meniu — Face Off Bar</title>
  <link rel="stylesheet" href="shared.css" />
  <link rel="stylesheet" href="meniu.css" />
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

    <!-- Statistică generată de PHP -->
    <p class="php-stats reveal">
      📋 Meniul nostru conține <strong style="color:var(--gold)"><?= $totalItems ?> produse</strong>
      în <strong style="color:var(--gold)"><?= count($meniu) ?> categorii</strong>.
    </p>

    <!-- Căutare -->
    <div class="search-wrap reveal">
      <input type="text" id="menu-search" placeholder="Caută în meniu..." />
    </div>

    <!-- Filtre (generate din array-ul PHP) -->
    <div class="filter-bar reveal">
      <button class="filter-btn <?= $activCat === 'toate' ? 'activ' : '' ?>" data-cat="toate">Noi Oferim:</button>
      <?php foreach ($meniu as $catKey => $catData): ?>
        <button
          class="filter-btn <?= $activCat === $catKey ? 'activ' : '' ?>"
          data-cat="<?= htmlspecialchars($catKey) ?>">
          <?= htmlspecialchars($catData['label']) ?>
        </button>
      <?php endforeach; ?>
    </div>

    <!-- Secțiuni meniu generate din PHP -->
    <?php foreach ($meniu as $catKey => $catData):
      $hidden = ($activCat !== 'toate' && $activCat !== $catKey) ? ' hidden' : '';
    ?>
      <div class="cat-section<?= $hidden ?>" data-cat="<?= htmlspecialchars($catKey) ?>">
        <div class="section-title"><?= htmlspecialchars($catData['label']) ?></div>
        <div class="menu-grid">
          <?php foreach ($catData['cards'] as $card): ?>
            <div class="menu-card">
              <h3><?= htmlspecialchars($card['title']) ?></h3>
              <?php foreach ($card['items'] as $item): ?>
                <div class="menu-item">
                  <span><?= htmlspecialchars($item['name']) ?></span>
                  <span class="price"><?= htmlspecialchars($item['price']) ?></span>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>

    <a href="acasa.php" class="btn" style="margin-top:10px;">← Înapoi la Acasă</a>

  </div>

  <style>
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

  <script src="efecte.js"></script>
  <script>
    /* Filtrare categorii */
    document.querySelectorAll('.filter-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('activ'));
        btn.classList.add('activ');
        const cat = btn.dataset.cat;
        document.querySelectorAll('.cat-section').forEach(sec => {
          const ascunde = cat !== 'toate' && sec.dataset.cat !== cat;
          sec.classList.toggle('hidden', ascunde);
        });
      });
    });

    /* Căutare live */
    document.getElementById('menu-search').addEventListener('input', function() {
      const q = this.value.toLowerCase().trim();
      document.querySelectorAll('.menu-item').forEach(item => {
        const name = item.querySelector('span:first-child')?.textContent.toLowerCase() || '';
        item.style.display = (!q || name.includes(q)) ? '' : 'none';
      });
      document.querySelectorAll('.menu-card').forEach(card => {
        const visible = [...card.querySelectorAll('.menu-item')].some(i => i.style.display !== 'none');
        card.style.display = visible ? '' : 'none';
      });
    });
  </script>

</body>
</html>
