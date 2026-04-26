/**
 * ============================================================
 *  Lucrarea de laborator Nr.4 — Sarcina Suplimentară
 *  contact_validation.js
 *
 *  Verificare JavaScript a corectitudinii datelor înainte
 *  de trimiterea formularului către scriptul CGI.
 * ============================================================
 */

(function contactValidation() {
  'use strict';

  /* ---- Referințe DOM ---- */
  const form     = document.getElementById('contact-form');
  const feedback = document.getElementById('cf-feedback');
  if (!form) return;

  /* ---- Reguli de validare ---- */
  const EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  const rules = [
    {
      fieldId : 'cf-name',
      test    : v => v.length > 0,
      msg     : '⚠️ Numele tău este obligatoriu.',
    },
    {
      fieldId : 'cf-name',
      test    : v => v.length >= 2,
      msg     : '⚠️ Numele trebuie să conțină cel puțin 2 caractere.',
    },
    {
      fieldId : 'cf-email',
      test    : v => v.length > 0,
      msg     : '⚠️ Adresa de email este obligatorie.',
    },
    {
      fieldId : 'cf-email',
      test    : v => EMAIL_RE.test(v),
      msg     : '⚠️ Introdu o adresă de email validă (ex: nume@domeniu.md).',
    },
    {
      fieldId : 'cf-msg',
      test    : v => v.length > 0,
      msg     : '⚠️ Mesajul nu poate fi gol.',
    },
    {
      fieldId : 'cf-msg',
      test    : v => v.length >= 10,
      msg     : '⚠️ Mesajul trebuie să conțină cel puțin 10 caractere.',
    },
  ];

  /* ---- Afișare feedback ---- */
  function showFeedback(msg, ok) {
    feedback.textContent      = msg;
    feedback.style.background = ok ? 'rgba(39,174,96,.12)' : 'rgba(231,76,60,.12)';
    feedback.style.color      = ok ? '#27ae60'             : '#e74c3c';
    feedback.style.border     = `1px solid ${ok ? 'rgba(39,174,96,.3)' : 'rgba(231,76,60,.3)'}`;
    feedback.style.display    = 'block';
    feedback.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    if (ok) setTimeout(() => { feedback.style.display = 'none'; }, 5000);
  }

  /* ---- Marcare câmp cu eroare ---- */
  function markError(fieldId) {
    const el = document.getElementById(fieldId);
    if (!el) return;

    el.style.borderColor = '#e74c3c';
    el.style.boxShadow   = '0 0 0 2px rgba(231,76,60,.18)';
    el.focus();

    // Resetare la prima modificare
    el.addEventListener('input', function resetBorder() {
      el.style.borderColor = '';
      el.style.boxShadow   = '';
      el.removeEventListener('input', resetBorder);
    });
  }

  /* ---- Marcare câmp valid ---- */
  function markValid(fieldId) {
    const el = document.getElementById(fieldId);
    if (!el) return;
    el.style.borderColor = 'rgba(39,174,96,.5)';
    el.style.boxShadow   = '0 0 0 2px rgba(39,174,96,.08)';
    setTimeout(() => {
      el.style.borderColor = '';
      el.style.boxShadow   = '';
    }, 1800);
  }

  /* ---- Validare live (la ieșire din câmp) ---- */
  ['cf-name', 'cf-email', 'cf-msg'].forEach(id => {
    const el = document.getElementById(id);
    if (!el) return;

    el.addEventListener('blur', () => {
      const val = el.value.trim();
      // Găsim prima regulă neîndeplinită pentru acest câmp
      const fail = rules.find(r => r.fieldId === id && !r.test(val));
      if (fail) {
        markError(id);
      } else if (val.length > 0) {
        markValid(id);
      }
    });
  });

  /* ---- Contor caractere pentru textarea ---- */
  const textarea = document.getElementById('cf-msg');
  if (textarea) {
    const counter = document.createElement('small');
    counter.style.cssText = 'color:var(--text-muted);font-size:.75rem;text-align:right;display:block;margin-top:4px;';
    textarea.insertAdjacentElement('afterend', counter);

    textarea.addEventListener('input', () => {
      const len = textarea.value.length;
      counter.textContent = `${len} / 5000 caractere`;
      counter.style.color = len > 4800 ? '#e74c3c' : 'var(--text-muted)';
    });
  }

  /* ---- Interceptare submit → validare completă ---- */
  form.addEventListener('submit', function(e) {
    // Resetare feedback anterior
    feedback.style.display = 'none';

    let firstError = null;

    for (const rule of rules) {
      const el  = document.getElementById(rule.fieldId);
      const val = el ? el.value.trim() : '';

      if (!rule.test(val)) {
        if (!firstError) firstError = rule;   // prima eroare găsită
        markError(rule.fieldId);
      }
    }

    if (firstError) {
      e.preventDefault();                    // BLOCAT: nu se trimite formularul
      showFeedback(firstError.msg, false);
      return;
    }

    /* Date valide → indicăm că se trimite */
    const btn = document.getElementById('cf-submit');
    if (btn) {
      btn.disabled    = true;
      btn.textContent = 'Se trimite…';
      btn.style.opacity = '.7';
    }

    /* Formularul se trimite normal (POST → CGI) */
  });

})();
