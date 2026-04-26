<?php

$hour = (int)date('G');
if ($hour >= 5 && $hour < 12) {
    $greeting = 'Bună dimineața';
} elseif ($hour >= 12 && $hour < 18) {
    $greeting = 'Bună ziua';
} elseif ($hour >= 18 && $hour < 22) {
    $greeting = 'Bună seara';
} else {
    $greeting = 'Bun venit';
}

$zile = ['Duminică','Luni','Marți','Miercuri','Joi','Vineri','Sâmbătă'];
$luni = [
    1=>'ianuarie',2=>'februarie',3=>'martie',4=>'aprilie',
    5=>'mai',6=>'iunie',7=>'iulie',8=>'august',
    9=>'septembrie',10=>'octombrie',11=>'noiembrie',12=>'decembrie'
];
$dataCurenta = $zile[(int)date('w')] . ', ' . (int)date('j') . ' ' . $luni[(int)date('n')] . ' ' . date('Y');

$isOpen   = ($hour >= 5 || $hour < 3);
$barStatus     = $isOpen ? '● Deschis acum' : '● Închis momentan';
$barStatusClass = $isOpen ? 'open' : 'closed';

$features = [
    ['icon' => '✨', 'title' => 'Ambient Modern',       'desc' => 'Lumini ambientale și spațiu confortabil pentru fiecare ocazie.'],
    ['icon' => '🍹', 'title' => 'Cocktailuri Premium',  'desc' => 'Băuturi preparate din ingrediente de calitate de barmani cu experiență.'],
    ['icon' => '🎶', 'title' => 'Atmosferă Relaxantă',  'desc' => 'Perfect pentru prieteni, aniversări și evenimente speciale.'],
];
?>
<!DOCTYPE html>
<html lang="ro">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Face Off Bar — Chișinău</title>
  <link rel="stylesheet" href="shared.css" />
  <link rel="stylesheet" href="acasa.css" />
</head>
<body>

  <header>
    <h1>Face Off Bar</h1>
    <p>Cocktailuri premium · Atmosferă unică · Chișinău</p>
  </header>

  <nav>
    <a href="acasa.php" class="nav-activ">Acasă</a>
    <a href="meniu.php">Meniu</a>
    <a href="galerie.php">Galerie</a>
    <a href="contact.php">Contact</a>
  </nav>

  <div class="container">

    <!-- Salut dinamic generat de PHP -->
    <div class="php-info-bar reveal">
      <span class="php-greeting"><?= htmlspecialchars($greeting) ?>!</span>
      <span class="php-date">📅 <?= htmlspecialchars($dataCurenta) ?></span>
      <span class="status-badge <?= $barStatusClass ?>"><?= $barStatus ?></span>
    </div>

    <section class="hero reveal">
      <h2 id="hero-title">Bine ai venit la Face Off</h2>
      <p>Muzică bună, cocktailuri premium și o atmosferă relaxantă pentru seri memorabile alături de prieteni.</p>
      <a href="meniu.php" class="btn btn-solid">Vezi Meniul</a>
    </section>

    <section class="about reveal">
      <img
        src="https://lh3.googleusercontent.com/gps-cs-s/APNQkAG654ss6qMkaGrUf94-A3ZYW8ZbdXyFwzeafhPHBglt1vCXch31LduAOe11TfObC9sGnXMCm7PWWKPiz11YA6fcpNFuU3H7B6nf00caCxg-VCYocreMh_fb0amjatFY4RMluXc5=s1360-w1360-h1020-rw"
        alt="Interiorul Face Off Bar"
      />
      <div class="about-text">
        <h3>Povestea noastră</h3>
        <p>
          Face Off Bar este un bar modern destinat persoanelor care doresc să petreacă timp de calitate
          într-o atmosferă prietenoasă. Interiorul este amenajat într-un stil contemporan, fiind locul
          ideal pentru întâlniri sau evenimente speciale.
        </p>
        <br>
        <a href="contact.php" class="btn" style="margin-top:10px;">Rezervă o masă</a>
      </div>
    </section>

    <div class="section-title reveal" style="margin-top:60px;">De ce Face Off</div>
    <section class="features">
      <?php foreach ($features as $card): ?>
        <div class="card reveal">
          <div class="card-icon"><?= $card['icon'] ?></div>
          <h3><?= htmlspecialchars($card['title']) ?></h3>
          <p><?= htmlspecialchars($card['desc']) ?></p>
        </div>
      <?php endforeach; ?>
    </section>

  </div>

  <style>
    .php-info-bar {
      display: flex;
      align-items: center;
      gap: 18px;
      flex-wrap: wrap;
      background: var(--bg2);
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      padding: 14px 22px;
      margin-bottom: 36px;
      font-size: .85rem;
    }
    .php-greeting {
      color: var(--gold);
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.05rem;
      font-weight: 600;
    }
    .php-date { color: var(--text-muted); letter-spacing: .5px; }
    .status-badge {
      display: inline-flex; align-items: center; gap: 6px;
      font-size: .75rem; font-weight: 600; letter-spacing: 1px;
      padding: 4px 12px; border-radius: 20px;
    }
    .status-badge.open   { background:rgba(39,174,96,.15); color:#27ae60; border:1px solid rgba(39,174,96,.3); }
    .status-badge.closed { background:rgba(231,76,60,.15); color:#e74c3c; border:1px solid rgba(231,76,60,.3); }
  </style>

  <script src="efecte.js"></script>
  <script src="chat_widget.js"></script>
</body>
</html>
