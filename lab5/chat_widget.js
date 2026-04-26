/**
 * ============================================================
 *  chat_widget.js — Widget Chat Live via AJAX
 *  Face Off Bar
 * ============================================================
 */

(function chatWidget() {
  'use strict';

  const CHAT_URL = 'chat.php';

  /* ============================================================
     Stiluri inline — se injectează o singură dată
     ============================================================ */
  const CSS = `
    /* ---- Buton de toggle ---- */
    #fw-toggle {
      position: fixed;
      bottom: 28px; left: 28px;
      z-index: 998;
      width: 52px; height: 52px;
      border-radius: 50%;
      background: #0d0d0d;
      border: 1px solid rgba(212,160,23,.45);
      color: #d4a017;
      font-size: 1.35rem;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 24px rgba(0,0,0,.55), 0 0 0 0 rgba(212,160,23,.3);
      transition: background .2s, border-color .2s, transform .2s, box-shadow .2s;
      backdrop-filter: blur(10px);
      outline: none;
    }
    #fw-toggle:hover {
      background: #d4a017;
      color: #000;
      border-color: #d4a017;
      transform: scale(1.08);
    }
    #fw-toggle.open {
      background: #d4a017;
      color: #000;
      border-color: #d4a017;
    }
    #fw-toggle .fw-badge {
      position: absolute;
      top: -4px; right: -4px;
      width: 18px; height: 18px;
      background: #e74c3c;
      border-radius: 50%;
      border: 2px solid #0d0d0d;
      font-size: .6rem;
      font-weight: 700;
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      animation: badgePulse 2s ease-in-out infinite;
      font-family: 'Jost', sans-serif;
    }
    @keyframes badgePulse {
      0%, 100% { transform: scale(1); }
      50%       { transform: scale(1.2); }
    }

    /* ---- Fereastra de chat ---- */
    #fw-box {
      position: fixed;
      bottom: 94px; left: 28px;
      z-index: 997;
      width: 340px;
      max-height: 500px;
      border-radius: 18px;
      background: #111;
      border: 1px solid rgba(212,160,23,.28);
      box-shadow: 0 20px 60px rgba(0,0,0,.75), 0 0 0 1px rgba(212,160,23,.06);
      display: flex;
      flex-direction: column;
      overflow: hidden;
      transform: translateY(18px) scale(.97);
      opacity: 0;
      pointer-events: none;
      transition: transform .32s cubic-bezier(.34,1.4,.64,1), opacity .28s ease;
      font-family: 'Jost', sans-serif;
    }
    #fw-box.open {
      transform: translateY(0) scale(1);
      opacity: 1;
      pointer-events: all;
    }

    /* ---- Header chat ---- */
    #fw-header {
      background: linear-gradient(135deg, #161616 0%, #1a1500 100%);
      padding: 16px 18px 14px;
      border-bottom: 1px solid rgba(212,160,23,.18);
      display: flex;
      align-items: center;
      gap: 12px;
      flex-shrink: 0;
    }
    #fw-avatar {
      width: 38px; height: 38px;
      border-radius: 50%;
      background: linear-gradient(135deg, #d4a017, #f0c040);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.1rem;
      flex-shrink: 0;
      box-shadow: 0 0 12px rgba(212,160,23,.3);
    }
    #fw-header-text h4 {
      font-family: 'Cormorant Garamond', serif;
      color: #d4a017;
      font-size: 1rem;
      font-weight: 600;
      letter-spacing: .5px;
      line-height: 1.2;
    }
    #fw-header-text p {
      color: #666;
      font-size: .7rem;
      letter-spacing: 1px;
      text-transform: uppercase;
      font-weight: 400;
      margin-top: 2px;
    }
    #fw-status-dot {
      width: 7px; height: 7px;
      background: #27ae60;
      border-radius: 50%;
      display: inline-block;
      margin-right: 5px;
      animation: statusBlink 3s ease-in-out infinite;
    }
    @keyframes statusBlink {
      0%, 90%, 100% { opacity: 1; }
      95%           { opacity: .3; }
    }

    /* ---- Mesaje ---- */
    #fw-messages {
      flex: 1;
      overflow-y: auto;
      padding: 16px 14px;
      display: flex;
      flex-direction: column;
      gap: 10px;
      scrollbar-width: thin;
      scrollbar-color: rgba(212,160,23,.2) transparent;
    }
    #fw-messages::-webkit-scrollbar { width: 4px; }
    #fw-messages::-webkit-scrollbar-thumb { background: rgba(212,160,23,.2); border-radius: 2px; }

    .fw-msg {
      max-width: 82%;
      padding: 10px 13px;
      border-radius: 14px;
      font-size: .84rem;
      line-height: 1.55;
      animation: msgIn .28s cubic-bezier(.34,1.4,.64,1);
      white-space: pre-wrap;
    }
    @keyframes msgIn {
      from { opacity: 0; transform: translateY(8px) scale(.96); }
      to   { opacity: 1; transform: translateY(0) scale(1); }
    }
    .fw-msg.bot {
      background: #1a1a1a;
      border: 1px solid rgba(212,160,23,.15);
      color: #ccc;
      border-radius: 14px 14px 14px 4px;
      align-self: flex-start;
    }
    .fw-msg.bot strong { color: #d4a017; font-weight: 600; }
    .fw-msg.user {
      background: linear-gradient(135deg, #d4a017, #c49010);
      color: #000;
      border-radius: 14px 14px 4px 14px;
      align-self: flex-end;
      font-weight: 500;
    }

    /* ---- Typing indicator ---- */
    #fw-typing {
      display: none;
      align-self: flex-start;
      padding: 10px 14px;
      background: #1a1a1a;
      border: 1px solid rgba(212,160,23,.12);
      border-radius: 14px 14px 14px 4px;
      gap: 5px;
      align-items: center;
    }
    #fw-typing.visible { display: flex; }
    .fw-dot {
      width: 6px; height: 6px;
      background: #d4a017;
      border-radius: 50%;
      opacity: .4;
      animation: dotBounce 1.2s ease-in-out infinite;
    }
    .fw-dot:nth-child(2) { animation-delay: .2s; }
    .fw-dot:nth-child(3) { animation-delay: .4s; }
    @keyframes dotBounce {
      0%, 80%, 100% { transform: translateY(0); opacity: .4; }
      40%           { transform: translateY(-5px); opacity: 1; }
    }

    /* ---- Input area ---- */
    #fw-footer {
      border-top: 1px solid rgba(212,160,23,.12);
      padding: 12px 14px;
      display: flex;
      gap: 8px;
      align-items: flex-end;
      flex-shrink: 0;
      background: #0e0e0e;
    }
    #fw-input {
      flex: 1;
      background: #1a1a1a;
      border: 1px solid rgba(212,160,23,.15);
      border-radius: 20px;
      color: #e8e0d0;
      font-family: 'Jost', sans-serif;
      font-size: .84rem;
      padding: 9px 14px;
      outline: none;
      resize: none;
      max-height: 100px;
      line-height: 1.45;
      transition: border-color .2s;
      overflow-y: auto;
    }
    #fw-input:focus { border-color: rgba(212,160,23,.45); }
    #fw-input::placeholder { color: #444; }
    #fw-send {
      width: 36px; height: 36px;
      border-radius: 50%;
      background: #d4a017;
      border: none;
      color: #000;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1rem;
      flex-shrink: 0;
      transition: background .2s, transform .15s;
      outline: none;
    }
    #fw-send:hover { background: #f0c040; transform: scale(1.08); }
    #fw-send:active { transform: scale(.95); }

    /* ---- Sugestii rapide ---- */
    #fw-suggestions {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
      padding: 8px 14px 4px;
      border-top: 1px solid rgba(212,160,23,.08);
      background: #0e0e0e;
    }
    .fw-chip {
      padding: 5px 12px;
      border-radius: 20px;
      background: transparent;
      border: 1px solid rgba(212,160,23,.25);
      color: #888;
      font-size: .72rem;
      font-family: 'Jost', sans-serif;
      cursor: pointer;
      transition: all .2s;
      letter-spacing: .5px;
    }
    .fw-chip:hover {
      background: rgba(212,160,23,.12);
      color: #d4a017;
      border-color: rgba(212,160,23,.5);
    }

    @media (max-width: 400px) {
      #fw-box { width: calc(100vw - 24px); left: 12px; bottom: 80px; }
      #fw-toggle { bottom: 16px; left: 16px; }
    }
  `;

  /* ---- Injectează CSS ---- */
  if (!document.getElementById('fw-styles')) {
    const style = document.createElement('style');
    style.id = 'fw-styles';
    style.textContent = CSS;
    document.head.appendChild(style);
  }

  /* ============================================================
     HTML Widget
     ============================================================ */
  const SUGGESTIONS = ['Program', 'Rezervare', 'Adresă', 'Meniu', 'Hookah'];

  const wrapper = document.createElement('div');
  wrapper.innerHTML = `
    <!-- Buton toggle -->
    <button id="fw-toggle" aria-label="Deschide chat" title="Chat live">
      <span id="fw-icon">💬</span>
      <span class="fw-badge" id="fw-badge">1</span>
    </button>

    <!-- Fereastra chat -->
    <div id="fw-box" role="dialog" aria-label="Chat Face Off Bar">
      <div id="fw-header">
        <div id="fw-avatar">🍹</div>
        <div id="fw-header-text">
          <h4>Face Off Bar</h4>
          <p><span id="fw-status-dot"></span>Asistent live</p>
        </div>
      </div>
      <div id="fw-messages">
        <!-- Mesaj inițial -->
        <div class="fw-msg bot">Bun venit la <strong>Face Off Bar</strong>! 🍹

Pot să te ajut cu informații despre <strong>program</strong>, <strong>rezervări</strong>, <strong>meniu</strong> sau <strong>adresă</strong>.</div>
      </div>
      <div id="fw-typing">
        <div class="fw-dot"></div>
        <div class="fw-dot"></div>
        <div class="fw-dot"></div>
      </div>
      <div id="fw-suggestions">
        ${SUGGESTIONS.map(s => `<button class="fw-chip" data-text="${s}">${s}</button>`).join('')}
      </div>
      <div id="fw-footer">
        <textarea id="fw-input" placeholder="Scrie un mesaj…" rows="1"></textarea>
        <button id="fw-send" aria-label="Trimite">➤</button>
      </div>
    </div>
  `;
  document.body.appendChild(wrapper);

  /* ---- Referințe DOM ---- */
  const toggle      = document.getElementById('fw-toggle');
  const box         = document.getElementById('fw-box');
  const messages    = document.getElementById('fw-messages');
  const input       = document.getElementById('fw-input');
  const sendBtn     = document.getElementById('fw-send');
  const typing      = document.getElementById('fw-typing');
  const badge       = document.getElementById('fw-badge');
  const fwIcon      = document.getElementById('fw-icon');

  let isOpen    = false;
  let isBusy    = false;

  /* ---- Toggle chat ---- */
  toggle.addEventListener('click', () => {
    isOpen = !isOpen;
    box.classList.toggle('open', isOpen);
    toggle.classList.toggle('open', isOpen);
    fwIcon.textContent = isOpen ? '✕' : '💬';

    // Ascunde badge după prima deschidere
    badge.style.display = 'none';

    if (isOpen) {
      setTimeout(() => input.focus(), 350);
      scrollBottom();
    }
  });

  /* ---- Trimite mesaj ---- */
  function sendMessage() {
    const text = input.value.trim();
    if (!text || isBusy) return;

    addMessage(text, 'user');
    input.value = '';
    autoResize();
    isBusy = true;

    // Ascunde sugestii după primul mesaj
    const sugg = document.getElementById('fw-suggestions');
    if (sugg) sugg.style.display = 'none';

    // Arată indicator de scriere
    showTyping(true);

    // Delay natural (500–1000ms)
    const delay = 500 + Math.random() * 500;

    fetch(CHAT_URL, {
      method:  'POST',
      headers: { 'Content-Type': 'application/json' },
      body:    JSON.stringify({ message: text }),
    })
      .then(r => r.json())
      .then(data => {
        setTimeout(() => {
          showTyping(false);
          addMessage(data.reply || 'Ne pare rău, încearcă din nou.', 'bot');
          isBusy = false;
        }, delay);
      })
      .catch(() => {
        setTimeout(() => {
          showTyping(false);
          addMessage('Ne pare rău, a apărut o eroare. Sună-ne la ☎ 0789 57 760.', 'bot');
          isBusy = false;
        }, delay);
      });
  }

  /* ---- Adaugă mesaj în UI ---- */
  function addMessage(text, type) {
    const div = document.createElement('div');
    div.className = `fw-msg ${type}`;

    // Bold **text**
    div.innerHTML = text.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');

    messages.appendChild(div);
    scrollBottom();
  }

  /* ---- Typing indicator ---- */
  function showTyping(show) {
    typing.classList.toggle('visible', show);
    if (show) scrollBottom();
  }

  /* ---- Scroll jos ---- */
  function scrollBottom() {
    setTimeout(() => {
      messages.scrollTop = messages.scrollHeight;
    }, 50);
  }

  /* ---- Auto-resize textarea ---- */
  function autoResize() {
    input.style.height = 'auto';
    input.style.height = Math.min(input.scrollHeight, 100) + 'px';
  }
  input.addEventListener('input', autoResize);

  /* ---- Enter pentru trimitere (Shift+Enter = linie nouă) ---- */
  input.addEventListener('keydown', e => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      sendMessage();
    }
  });

  sendBtn.addEventListener('click', sendMessage);

  /* ---- Sugestii rapide (chips) ---- */
  document.querySelectorAll('.fw-chip').forEach(chip => {
    chip.addEventListener('click', () => {
      input.value = chip.dataset.text;
      sendMessage();
    });
  });

})();
