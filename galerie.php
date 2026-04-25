<?php
/* ============================================================
   Lucrarea de laborator Nr.3 — galerie.php
   Face Off Bar — Galerie foto
   ============================================================ */

// ---- Array-ul imaginilor definit în PHP ----
$imagini = [
    [
        'src' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSpYlj3xAFqbd1OhoXLxxeuSZls-KjlrJcevw&s',
        'alt' => 'Face Off Bar 1',
    ],
    [
        'src' => 'https://i.simpalsmedia.com/afisha.md/places/original/fdb33e3db6f4f62ae16836732c929a16.png',
        'alt' => 'Face Off Bar 2',
    ],
    [
        'src' => 'https://avatars.mds.yandex.net/get-altay/10142335/2a0000018b2994d97b4009cd4691d8a3bd17/L_height',
        'alt' => 'Face Off Bar 3',
    ],
    [
        'src' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQH-xlgYvDmqTMqhD-gPzTjXO9p9Nsq3fP1Cg&s',
        'alt' => 'Face Off Bar 4',
    ],
    [
        'src' => 'https://avatars.mds.yandex.net/get-altay/9704097/2a0000018b2992a302250dae699ebc35db60/L_height',
        'alt' => 'Face Off Bar 5',
    ],
    [
        'src' => 'https://avatars.mds.yandex.net/get-altay/6506385/2a0000018b29949ac71d37ceb3662033bf82/L_height',
        'alt' => 'Face Off Bar 6',
    ],
    [
        'src' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSNXMVRTFMarOOH-t4Yd1byiaazxxbhXMlRVg&s',
        'alt' => 'Face Off Bar 7',
    ],
    [
        'src' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRxNIAQ7onD6_2cizWxG-dOIWuQPuMSyIu42Q&s',
        'alt' => 'Face Off Bar 8',
    ],
    [
        'src' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ9a7MSbuvOj_WP7YxzPzikMerLAM2Iaf_iIQ&s',
        'alt' => 'Face Off Bar 9',
    ],
];

// ---- Grupare în slide-uri a câte 3 imagini ----
$slidesPerPage = 3;
$slides = array_chunk($imagini, $slidesPerPage);
$totalSlides = count($slides);
$totalImagini = count($imagini);
?>
<!DOCTYPE html>
<html lang="ro">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Galerie — Face Off Bar</title>
  <link rel="stylesheet" href="shared.css" />
  <link rel="stylesheet" href="galerie.css" />
</head>
<body>

  <div class="top-line"></div>

  <header>
    <h1>Galerie</h1>
    <p>Imagini din barul nostru</p>
  </header>

  <nav>
    <a href="acasa.php">Acasă</a>
    <a href="meniu.php">Meniu</a>
    <a href="galerie.php" class="nav-activ">Galerie</a>
    <a href="contact.php">Contact</a>
  </nav>

  <div class="container">

    <!-- Info generată de PHP -->
    <p class="php-gallery-info reveal">
      🖼️ Galeria conține <strong style="color:var(--gold)"><?= $totalImagini ?> fotografii</strong>
      în <strong style="color:var(--gold)"><?= $totalSlides ?> slide-uri</strong>.
    </p>

    <div class="slider reveal">

      <!-- Radio buttons generate de PHP -->
      <?php for ($i = 1; $i <= $totalSlides; $i++): ?>
        <input type="radio" name="slides" id="s<?= $i ?>" <?= $i === 1 ? 'checked' : '' ?>>
      <?php endfor; ?>

      <!-- Track cu slide-uri generate de PHP -->
      <div class="track" style="width: <?= $totalSlides * 100 ?>%;">
        <?php foreach ($slides as $slideIndex => $slideImagini): ?>
          <div class="slide" style="width: <?= round(100 / $totalSlides, 4) ?>%;">
            <?php foreach ($slideImagini as $img): ?>
              <div class="pic">
                <img
                  src="<?= htmlspecialchars($img['src']) ?>"
                  alt="<?= htmlspecialchars($img['alt']) ?>"
                  loading="lazy"
                />
              </div>
            <?php endforeach; ?>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Controale generate de PHP -->
      <div class="controls">
        <?php for ($i = 1; $i <= $totalSlides; $i++):
          $prev = ($i === 1) ? $totalSlides : $i - 1;
          $next = ($i === $totalSlides) ? 1 : $i + 1;
        ?>
          <label class="arrow prev p<?= $i ?>" for="s<?= $prev ?>">&#x2039;</label>
        <?php endfor; ?>
        <?php for ($i = 1; $i <= $totalSlides; $i++):
          $next = ($i === $totalSlides) ? 1 : $i + 1;
        ?>
          <label class="arrow next n<?= $i ?>" for="s<?= $next ?>">&#x203A;</label>
        <?php endfor; ?>
      </div>

      <!-- Dots generate de PHP -->
      <div class="dots">
        <?php for ($i = 1; $i <= $totalSlides; $i++): ?>
          <div class="dot d<?= $i ?>"></div>
        <?php endfor; ?>
      </div>

    </div><!-- /.slider -->

  </div>

  <!-- CSS slider dinamic (calculat din PHP) -->
  <style>
    .track { width: <?= $totalSlides * 100 ?>%; }
    <?php for ($i = 1; $i <= $totalSlides; $i++):
      $offset = round(($i - 1) * (100 / $totalSlides), 4);
    ?>
    #s<?= $i ?>:checked ~ .track { transform: translateX(-<?= $offset ?>%); }
    #s<?= $i ?>:checked ~ .dots .d<?= $i ?> { background: var(--gold); }
    <?php for ($j = 1; $j <= $totalSlides; $j++): ?>
    #s<?= $i ?>:checked ~ .controls .p<?= $i ?>,
    #s<?= $i ?>:checked ~ .controls .n<?= $i ?> { display: flex; }
    <?php endfor; ?>
    <?php endfor; ?>
  </style>

  <!-- Lightbox -->
  <div id="lightbox">
    <button id="lb-close">&times;</button>
    <button id="lb-prev">&#x2039;</button>
    <img id="lb-img" src="" alt="">
    <button id="lb-next">&#x203A;</button>
    <div id="lb-counter"></div>
  </div>

  <style>
    .php-gallery-info {
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
    /* Lightbox */
    let lbImages = [], lbCurrent = 0;

    function lbShow(idx) {
      lbCurrent = (idx + lbImages.length) % lbImages.length;
      document.getElementById('lb-img').src = lbImages[lbCurrent].src;
      document.getElementById('lb-img').alt = lbImages[lbCurrent].alt;
      document.getElementById('lb-counter').textContent = `${lbCurrent + 1} / ${lbImages.length}`;
    }

    function lbOpen(idx) {
      lbImages = [...document.querySelectorAll('.pic img')];
      document.getElementById('lightbox').classList.add('open');
      document.body.style.overflow = 'hidden';
      lbShow(idx);
    }

    function lbClose() {
      document.getElementById('lightbox').classList.remove('open');
      document.body.style.overflow = '';
    }

    document.getElementById('lb-close').addEventListener('click', lbClose);
    document.getElementById('lb-prev').addEventListener('click', e => { e.stopPropagation(); lbShow(lbCurrent - 1); });
    document.getElementById('lb-next').addEventListener('click', e => { e.stopPropagation(); lbShow(lbCurrent + 1); });
    document.getElementById('lightbox').addEventListener('click', e => {
      if (e.target === e.currentTarget || e.target.id === 'lb-img') lbClose();
    });

    document.addEventListener('click', e => {
      const img = e.target.closest('.pic img');
      if (!img) return;
      lbOpen([...document.querySelectorAll('.pic img')].indexOf(img));
    });

    document.addEventListener('keydown', e => {
      if (!document.getElementById('lightbox').classList.contains('open')) return;
      if (e.key === 'Escape')     lbClose();
      if (e.key === 'ArrowLeft')  lbShow(lbCurrent - 1);
      if (e.key === 'ArrowRight') lbShow(lbCurrent + 1);
    });

    /* Swipe */
    let touchX = 0;
    const slider = document.querySelector('.slider');
    slider.addEventListener('touchstart', e => { touchX = e.touches[0].clientX; }, { passive: true });
    slider.addEventListener('touchend', e => {
      const diff = touchX - e.changedTouches[0].clientX;
      if (Math.abs(diff) < 40) return;
      const radios = [...document.querySelectorAll('input[name="slides"]')];
      const cur = radios.findIndex(r => r.checked);
      radios[(cur + (diff > 0 ? 1 : -1) + radios.length) % radios.length].checked = true;
    });

    /* Auto-play */
    let paused = false;
    slider.addEventListener('mouseenter', () => paused = true);
    slider.addEventListener('mouseleave', () => paused = false);
    setInterval(() => {
      if (paused || document.getElementById('lightbox').classList.contains('open')) return;
      const radios = [...document.querySelectorAll('input[name="slides"]')];
      const cur = radios.findIndex(r => r.checked);
      radios[(cur + 1) % radios.length].checked = true;
    }, 5000);
  </script>

</body>
</html>
