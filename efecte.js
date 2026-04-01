(function navActiv() {
  const page = location.pathname.split('/').pop() || 'acasa.html';
  const target = page === '' ? 'acasa.html' : page;
  document.querySelectorAll('nav a').forEach(a => {
    if (a.getAttribute('href') === target) a.classList.add('nav-activ');
  });
})();

(function scrollToTop() {
  const btn = document.createElement('button');
  btn.innerHTML = '↑';
  btn.title = 'Înapoi sus';
  btn.style.cssText = `
    position:fixed;bottom:28px;right:28px;z-index:999;
    width:44px;height:44px;border-radius:50%;
    background:transparent;color:#d4a017;
    border:1px solid rgba(212,160,23,.4);
    font-size:18px;font-weight:600;cursor:pointer;
    display:none;align-items:center;justify-content:center;
    box-shadow:0 4px 20px rgba(0,0,0,.5);
    transition:background .2s,transform .2s,border-color .2s;
    backdrop-filter:blur(8px);
  `;
  btn.addEventListener('mouseenter', () => { btn.style.background='#d4a017'; btn.style.color='#000'; btn.style.transform='scale(1.1)'; });
  btn.addEventListener('mouseleave', () => { btn.style.background='transparent'; btn.style.color='#d4a017'; btn.style.transform='scale(1)'; });
  btn.addEventListener('click', () => window.scrollTo({ top:0, behavior:'smooth' }));
  document.body.appendChild(btn);
  window.addEventListener('scroll', () => { btn.style.display = window.scrollY > 300 ? 'flex' : 'none'; }, { passive:true });
})();

(function scrollReveal() {
  const elems = document.querySelectorAll('.reveal');
  if (!elems.length) return;
  const obs = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (e.isIntersecting) { e.target.classList.add('visible'); obs.unobserve(e.target); }
    });
  }, { threshold: 0.08 });
  elems.forEach(el => obs.observe(el));
})();

(function typingEffect() {
  const el = document.getElementById('hero-title');
  if (!el) return;
  const text = el.textContent.trim();
  el.textContent = '';
  const cursor = document.createElement('span');
  cursor.className = 'cursor';
  el.appendChild(cursor);
  let i = 0;
  function type() {
    if (i < text.length) {
      el.insertBefore(document.createTextNode(text[i++]), cursor);
      setTimeout(type, 60);
    } else {
      setTimeout(() => { cursor.style.display = 'none'; }, 3000);
    }
  }
  setTimeout(type, 300);
})();

(function headerSparkle() {
  const header = document.querySelector('header');
  if (!header) return;
  if (!document.getElementById('sparkle-style')) {
    const s = document.createElement('style');
    s.id = 'sparkle-style';
    s.textContent = `
      @keyframes floatUp {
        0%   { opacity:0; transform:translateY(0) scale(.4); }
        20%  { opacity:.6; }
        100% { opacity:0; transform:translateY(-80px) scale(1); }
      }
      .sparkle {
        position:absolute; width:4px; height:4px;
        border-radius:50%; background:#d4a017;
        pointer-events:none; opacity:0;
        animation:floatUp 2.6s ease-in-out infinite;
      }
    `;
    document.head.appendChild(s);
  }
  for (let i = 0; i < 8; i++) {
    const dot = document.createElement('span');
    dot.className = 'sparkle';
    dot.style.left = `${5 + Math.random() * 90}%`;
    dot.style.bottom = '0';
    dot.style.animationDelay = `${(Math.random() * 2.6).toFixed(2)}s`;
    header.appendChild(dot);
  }
})();

(function menuHover() {
  document.querySelectorAll('.menu-item').forEach(item => {
    item.addEventListener('mouseenter', () => item.style.paddingLeft = '12px');
    item.addEventListener('mouseleave', () => item.style.paddingLeft = '');
  });
})();
