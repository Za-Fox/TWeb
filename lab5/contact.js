const EMAILJS_PUBLIC_KEY  = 'tyX_8BKLkx2asrzje';
const EMAILJS_SERVICE_ID  = 'service_k7vyprr';
const EMAILJS_TEMPLATE_ID = 'template_tolqz8h';

(function contactForm() {
  const btn      = document.getElementById('cf-submit');
  const feedback = document.getElementById('cf-feedback');
  if (!btn) return;

  const script = document.createElement('script');
  script.src = 'https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js';
  script.onload = () => emailjs.init({ publicKey: EMAILJS_PUBLIC_KEY });
  document.head.appendChild(script);

  function showFeedback(msg, ok) {
    feedback.textContent      = msg;
    feedback.style.background = ok ? 'rgba(39,174,96,.12)' : 'rgba(231,76,60,.12)';
    feedback.style.color      = ok ? '#27ae60'             : '#e74c3c';
    feedback.style.border     = `1px solid ${ok ? 'rgba(39,174,96,.3)' : 'rgba(231,76,60,.3)'}`;
    feedback.style.display    = 'block';
    if (ok) setTimeout(() => { feedback.style.display = 'none'; }, 5000);
  }

  function markError(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.style.borderColor = '#e74c3c';
    el.focus();
    setTimeout(() => { el.style.borderColor = ''; }, 2500);
  }

  function setLoading(isLoading) {
    btn.disabled    = isLoading;
    btn.textContent = isLoading ? 'Se trimite…' : 'Trimite mesajul';
  }

  function resetForm() {
    ['cf-name', 'cf-email', 'cf-subject', 'cf-msg'].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.value = '';
    });
  }

  btn.addEventListener('click', async () => {
    const name    = document.getElementById('cf-name')?.value.trim()    || '';
    const email   = document.getElementById('cf-email')?.value.trim()   || '';
    const subject = document.getElementById('cf-subject')?.value.trim() || '';
    const message = document.getElementById('cf-msg')?.value.trim()     || '';

    if (!name)   { showFeedback('⚠️ Introdu numele tău.',            false); markError('cf-name');  return; }
    if (!email || !email.includes('@') || !email.includes('.')) {
                   showFeedback('⚠️ Adresa de email nu este validă.', false); markError('cf-email'); return; }
    if (!message) { showFeedback('⚠️ Mesajul nu poate fi gol.',       false); markError('cf-msg');   return; }

    setLoading(true);

    const templateParams = {
      from_name:  name,
      from_email: email,
      subject:    subject || '(fără subiect)',
      message:    message,
    };

    try {
      await emailjs.send(EMAILJS_SERVICE_ID, EMAILJS_TEMPLATE_ID, templateParams);

      showFeedback(' Mesajul a fost trimis! Te vom contacta în curând.', true);
      btn.textContent      = 'Trimis ✓';
      btn.style.background = '#27ae60';
      btn.style.color      = '#fff';
      resetForm();

      setTimeout(() => {
        btn.textContent      = 'Trimite mesajul';
        btn.style.background = '';
        btn.style.color      = '';
        btn.disabled         = false;
      }, 6000);

    } catch (error) {
      console.error('EmailJS error:', error);
      showFeedback(' Trimiterea a eșuat. Încearcă din nou sau contactează-ne telefonic.', false);
    } finally {
      if (btn.textContent === 'Se trimite…') setLoading(false);
    }
  });
})();
