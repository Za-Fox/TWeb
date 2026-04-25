<?php
/* ============================================================
   Lucrarea de laborator Nr.3 & Nr.4 — contact.php
   Face Off Bar — Contact + Formular CGI
   ============================================================ */

$status  = isset($_GET['status'])  ? htmlspecialchars($_GET['status'])  : '';
$errcode = isset($_GET['errcode']) ? htmlspecialchars($_GET['errcode']) : '';

$hour = (int)date('G');
$isOpen = ($hour >= 5 || $hour < 3);
$barStatus      = $isOpen ? '● Deschis acum' : '● Închis momentan';
$barStatusClass = $isOpen ? 'open' : 'closed';

$errorMessages = [
    'empty_name'    => '⚠️ Numele este obligatoriu.',
    'empty_email'   => '⚠️ Adresa de email este obligatorie.',
    'invalid_email' => '⚠️ Adresa de email nu este validă.',
    'empty_message' => '⚠️ Mesajul nu poate fi gol.',
    'save_error'    => '⚠️ Eroare la salvarea mesajului. Încearcă din nou.',
];
?>
<!DOCTYPE html>
<html lang="ro">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Contact — Face Off Bar</title>
  <link rel="stylesheet" href="shared.css" />
  <link rel="stylesheet" href="contact.css" />
  <style>
    .php-notice { padding:14px 18px; border-radius:8px; font-size:.9rem; font-weight:500; margin-bottom:22px; display:flex; align-items:center; gap:10px; }
    .php-notice.success { background:rgba(39,174,96,.12); color:#27ae60; border:1px solid rgba(39,174,96,.3); }
    .php-notice.error   { background:rgba(231,76,60,.12); color:#e74c3c; border:1px solid rgba(231,76,60,.3); }
    .status-badge { display:inline-flex; align-items:center; gap:6px; font-size:.75rem; font-weight:600; letter-spacing:1px; padding:4px 12px; border-radius:20px; margin-top:6px; }
    .status-badge.open   { background:rgba(39,174,96,.15); color:#27ae60; border:1px solid rgba(39,174,96,.3); }
    .status-badge.closed { background:rgba(231,76,60,.15); color:#e74c3c; border:1px solid rgba(231,76,60,.3); }
  </style>
</head>
<body>

  <header>
    <h1>Contact</h1>
    <p>Suntem aici pentru tine</p>
  </header>

  <nav>
    <a href="acasa.php">Acasă</a>
    <a href="meniu.php">Meniu</a>
    <a href="galerie.php">Galerie</a>
    <a href="contact.php" class="nav-activ">Contact</a>
  </nav>

  <div class="container">

    <div class="contact-grid reveal">
      <div class="map">
        <iframe src="https://www.google.com/maps?q=Face+Off,+Chisinau&output=embed" allowfullscreen="" loading="lazy"></iframe>
      </div>

      <div class="contact-info">
        <h2>Vizitează-ne</h2>
        <div class="info-row">
          <div class="info-icon">📍</div>
          <div>
            <div class="info-label">Adresă</div>
            <div class="info-value">Bulevardul Moscova 21<br>MD-2046, Chișinău, Republica Moldova</div>
          </div>
        </div>
        <div class="info-row">
          <div class="info-icon">📞</div>
          <div>
            <div class="info-label">Telefon</div>
            <div class="info-value"><a href="tel:+37378957760">0789 57 760</a></div>
          </div>
        </div>
        <div class="info-row">
          <div class="info-icon">🕐</div>
          <div>
            <div class="info-label">Program</div>
            <div class="info-value">
              Luni – Duminică: 05:00 – 03:00<br>
              <span class="status-badge <?= $barStatusClass ?>"><?= $barStatus ?></span>
            </div>
          </div>
        </div>
        <div class="social">
          <a href="https://www.instagram.com/faceoff_cocktails/" target="_blank" title="Instagram">IG</a>
          <a href="https://www.facebook.com/p/Face-Off-61550629761569/" target="_blank" title="Facebook">FB</a>
          <a href="https://www.tiktok.com/@faceoffcocktails" target="_blank" title="TikTok">TT</a>
        </div>
      </div>
    </div>

    <div id="contact-form-section" class="reveal">
      <div class="section-title" style="margin-bottom:20px;">Trimite-ne un mesaj</div>

      <?php if ($status === 'success'): ?>
        <div class="php-notice success">✅ Mesajul a fost trimis cu succes! Te vom contacta în curând.</div>
      <?php elseif ($status === 'error'): ?>
        <div class="php-notice error"><?= $errorMessages[$errcode] ?? '⚠️ A apărut o eroare. Încearcă din nou.' ?></div>
      <?php endif; ?>

      <div class="form-box">
        <form action="cgi-bin/process_contact.cgi" method="POST" id="contact-form" novalidate>
          <div class="form-group">
            <input id="cf-name"    name="name"    type="text"  placeholder="Numele tău *"  autocomplete="name" />
            <input id="cf-email"   name="email"   type="email" placeholder="Email *"        autocomplete="email" />
            <input id="cf-subject" name="subject" type="text"  placeholder="Subiect" />
            <textarea id="cf-msg"  name="message" rows="5"     placeholder="Mesajul tău *"></textarea>
            <input type="hidden" name="return_page" value="contact.php" />
            <button id="cf-submit" type="submit" class="btn btn-solid">Trimite mesajul</button>
          </div>
          <div id="cf-feedback" style="display:none;margin-top:16px;padding:12px 16px;border-radius:8px;font-size:.88rem;font-weight:500;"></div>
        </form>
      </div>
    </div>

  </div>

  <script src="efecte.js"></script>
  <script src="contact_validation.js"></script>

</body>
</html>
