/**
 * ============================================================
 *  rating.js — Sistem de rating cu stele via AJAX
 *  Face Off Bar
 * ============================================================
 */

(function starRating() {
  'use strict';

  const RATE_URL = 'rate.php';

  /* ---- Încarcă ratingurile existente la init ---- */
  fetch(RATE_URL)
    .then(r => r.json())
    .then(data => {
      document.querySelectorAll('.star-widget').forEach(widget => {
        const id = widget.dataset.id;
        if (data[id]) {
          updateDisplay(widget, data[id].total / data[id].count, data[id].count, false);
        }
      });
    })
    .catch(() => {}); // silențios dacă fișierul nu există încă

  /* ---- Delegare evenimente pe document ---- */
  document.addEventListener('click', e => {
    const star = e.target.closest('.star-btn');
    if (!star) return;

    const widget = star.closest('.star-widget');
    if (!widget || widget.dataset.voted === 'true') return;

    const itemId = widget.dataset.id;
    const val    = parseInt(star.dataset.val, 10);

    sendRating(widget, itemId, val);
  });

  /* ---- Hover preview ---- */
  document.addEventListener('mouseover', e => {
    const star = e.target.closest('.star-btn');
    if (!star) return;
    const widget = star.closest('.star-widget');
    if (!widget || widget.dataset.voted === 'true') return;

    const val = parseInt(star.dataset.val, 10);
    highlightStars(widget, val);
  });

  document.addEventListener('mouseout', e => {
    const star = e.target.closest('.star-btn');
    if (!star) return;
    const widget = star.closest('.star-widget');
    if (!widget || widget.dataset.voted === 'true') return;

    // Resetează la ratingul curent (dacă există)
    const current = parseFloat(widget.dataset.current || '0');
    highlightStars(widget, current);
  });

  /* ---- Trimite ratingul via AJAX ---- */
  function sendRating(widget, itemId, stars) {
    // Blochează widget-ul imediat (previne dublu-vot)
    widget.dataset.voted = 'true';
    widget.classList.add('rated');

    // Animație de confirmare
    highlightStars(widget, stars);

    fetch(RATE_URL, {
      method:  'POST',
      headers: { 'Content-Type': 'application/json' },
      body:    JSON.stringify({ item: itemId, stars }),
    })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          updateDisplay(widget, data.avg, data.count, true);
          widget.dataset.current = data.avg;
          pulseWidget(widget);
          if (typeof window.onRatingSuccess === 'function') {
            window.onRatingSuccess(data.avg, data.count);
          }
        } else {
          widget.dataset.voted = 'false';
          widget.classList.remove('rated');
        }
      })
      .catch(() => {
        widget.dataset.voted = 'false';
        widget.classList.remove('rated');
      });
  }

  /* ---- Actualizează display-ul ratingului ---- */
  function updateDisplay(widget, avg, count, animate) {
    widget.dataset.current = avg;
    highlightStars(widget, avg);

    const info = widget.querySelector('.rating-info');
    if (!info) return;

    const label = count === 1 ? 'vot' : 'voturi';
    info.textContent = `${avg.toFixed(1)} (${count} ${label})`;

    if (animate) {
      info.style.animation = 'none';
      void info.offsetWidth; // reflow
      info.style.animation = 'ratingPop .4s cubic-bezier(.34,1.56,.64,1)';
    }
  }

  /* ---- Colorează stelele (suportă valori decimale) ---- */
  function highlightStars(widget, value) {
    const stars = widget.querySelectorAll('.star-btn');
    stars.forEach(s => {
      const v = parseInt(s.dataset.val, 10);
      s.classList.toggle('filled',  v <= Math.round(value));
      s.classList.toggle('partial', false); // simplificat
    });
  }

  /* ---- Animație pulse după vot ---- */
  function pulseWidget(widget) {
    widget.classList.add('pulse');
    setTimeout(() => widget.classList.remove('pulse'), 600);
  }

})();
