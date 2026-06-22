/**
 * NaijaSchoolMS — Student Portal JavaScript
 * Covers: sidebar, tabs, dropdowns, toasts, CBT engine,
 *         lobby countdown, score bars, photo upload, password strength
 */

/* ── Utility helpers ─────────────────────────────────────── */
const $ = (sel, ctx = document) => ctx.querySelector(sel);
const $$ = (sel, ctx = document) => [...ctx.querySelectorAll(sel)];
const on = (el, ev, fn) => el && el.addEventListener(ev, fn);

/* ── Sidebar toggle (mobile) ─────────────────────────────── */
function initSidebar() {
  const sidebar  = $('.sidebar');
  const overlay  = $('.sidebar-overlay');
  const hamburger= $('.header-hamburger');
  if (!sidebar) return;

  function open()  { sidebar.classList.add('open'); overlay && overlay.classList.add('visible'); document.body.style.overflow = 'hidden'; }
  function close() { sidebar.classList.remove('open'); overlay && overlay.classList.remove('visible'); document.body.style.overflow = ''; }

  on(hamburger, 'click', open);
  on(overlay,   'click', close);

  // Close on nav link click (mobile)
  $$('.sidebar-link').forEach(l => on(l, 'click', () => { if (window.innerWidth < 768) close(); }));
}

/* ── Dropdown menus ──────────────────────────────────────── */
function initDropdowns() {
  $$('[data-dropdown]').forEach(trigger => {
    const menu = document.getElementById(trigger.dataset.dropdown);
    if (!menu) return;
    on(trigger, 'click', e => {
      e.stopPropagation();
      const open = menu.classList.contains('open');
      $$('.dropdown-menu.open').forEach(m => m.classList.remove('open'));
      if (!open) menu.classList.add('open');
    });
  });
  on(document, 'click', () => $$('.dropdown-menu.open').forEach(m => m.classList.remove('open')));
}

/* ── Tab switcher ────────────────────────────────────────── */
function initTabs() {
  $$('[data-tab]').forEach(btn => {
    on(btn, 'click', () => {
      const group = btn.closest('[data-tab-group]') || btn.closest('.tabs')?.parentElement;
      if (!group) return;
      $$('[data-tab]', group).forEach(b => b.classList.remove('active'));
      $$('[data-tab-panel]', group).forEach(p => p.classList.remove('active'));
      btn.classList.add('active');
      const panel = group.querySelector(`[data-tab-panel="${btn.dataset.tab}"]`);
      if (panel) panel.classList.add('active');
    });
  });
}

/* ── Toast notifications ─────────────────────────────────── */
function showToast(title, message = '', type = 'info') {
  let container = $('.toast-container');
  if (!container) {
    container = document.createElement('div');
    container.className = 'toast-container';
    document.body.appendChild(container);
  }
  const t = document.createElement('div');
  t.className = `toast ${type}`;
  t.innerHTML = `
    <div style="flex:1">
      <div class="toast-title">${title}</div>
      ${message ? `<div class="toast-message">${message}</div>` : ''}
    </div>
    <button class="toast-close" aria-label="Close">&times;</button>`;
  container.appendChild(t);
  on($('.toast-close', t), 'click', () => t.remove());
  setTimeout(() => t.remove(), 4500);
}
window.showToast = showToast;

/* ── Animate score bars on page load ────────────────────────── */
function initScoreBars() {
  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const fill = entry.target;
        fill.style.width = fill.dataset.width || '0%';
        observer.unobserve(fill);
      }
    });
  }, { threshold: 0.1 });

  $$('.score-bar-fill').forEach(bar => {
    const pct = parseFloat(bar.dataset.width) || 0;
    bar.style.width = '0%';
    bar.classList.remove('high', 'medium', 'low', 'fail');
    if (pct >= 70)      bar.classList.add('high');
    else if (pct >= 50) bar.classList.add('medium');
    else if (pct >= 40) bar.classList.add('low');
    else                bar.classList.add('fail');
    observer.observe(bar);
  });
}

/* ── Lobby countdown clock ───────────────────────────────── */
function initLobbyCountdown() {
  const el = $('#lobby-countdown');
  if (!el) return;

  const targetTime = el.dataset.target; // ISO string
  if (!targetTime) return;

  const target = new Date(targetTime).getTime();
  const enterBtn = $('#enter-exam-btn');
  const statusText = $('#lobby-status-text');

  function update() {
    const now  = Date.now();
    const diff = target - now;

    if (diff <= 0) {
      // Exam has started
      el.querySelector('.countdown-num[data-unit=h]').textContent = '00';
      el.querySelector('.countdown-num[data-unit=m]').textContent = '00';
      el.querySelector('.countdown-num[data-unit=s]').textContent = '00';
      if (enterBtn) { enterBtn.removeAttribute('disabled'); enterBtn.classList.remove('btn-primary'); enterBtn.classList.add('btn-accent'); }
      if (statusText) statusText.textContent = 'Exam is now LIVE — you may enter.';
      return;
    }

    const h = Math.floor(diff / 3600000);
    const m = Math.floor((diff % 3600000) / 60000);
    const s = Math.floor((diff % 60000) / 1000);

    el.querySelector('.countdown-num[data-unit=h]').textContent = String(h).padStart(2, '0');
    el.querySelector('.countdown-num[data-unit=m]').textContent = String(m).padStart(2, '0');
    el.querySelector('.countdown-num[data-unit=s]').textContent = String(s).padStart(2, '0');

    setTimeout(update, 1000);
  }
  update();
}

/* ════════════════════════════════════════════════════════════
   CBT EXAM ENGINE
   ════════════════════════════════════════════════════════════ */
const CBT = (() => {
  let state = {
    current:  1,
    total:    0,
    answers:  {},  // { qNum: 'A'|'B'|'C'|'D' }
    flags:    {},  // { qNum: true }
    timeLeft: 0,
    timerInterval: null,
    autoSaveInterval: null,
    attemptId: null,
  };

  /* ── Initialise ─────────────────────────────────────────── */
  function init() {
    const shell = $('.exam-shell');
    if (!shell) return;

    state.total     = parseInt(shell.dataset.total || 0);
    state.timeLeft  = parseInt(shell.dataset.duration || 0) * 60; // minutes → seconds
    state.attemptId = shell.dataset.attempt;

    if (!state.total || !state.timeLeft) return;

    // Restore any saved answers (from hidden inputs)
    $$('[data-saved-answer]').forEach(el => {
      const q = el.dataset.question;
      const a = el.dataset.savedAnswer;
      if (a) state.answers[q] = a;
    });

    renderQuestion(state.current);
    startTimer();
    startAutoSave();
    buildNavigator();
    updateProgressBar();

    // Back-button lock (CBT security)
    history.pushState(null, '', location.href);
    on(window, 'popstate', () => { history.pushState(null, '', location.href); });

    // Warn on tab close / refresh
    on(window, 'beforeunload', e => {
      e.preventDefault();
      e.returnValue = 'Your exam is in progress. Leaving will NOT auto-submit. Are you sure?';
    });

    // Keyboard shortcuts A/B/C/D + arrows
    on(document, 'keydown', handleKeydown);

    // Navigator toggle (mobile)
    const navToggle = $('.nav-toggle-btn');
    const navPanel  = $('.navigator-panel');
    const navClose  = $('.nav-close-btn');
    on(navToggle, 'click', () => navPanel && navPanel.classList.toggle('open'));
    on(navClose,  'click', () => navPanel && navPanel.classList.remove('open'));
  }

  /* ── Render current question ─────────────────────────────── */
  function renderQuestion(qNum) {
    $$('[data-question-slide]').forEach(slide => {
      slide.style.display = slide.dataset.questionSlide == qNum ? 'block' : 'none';
    });

    const flag = state.flags[qNum];
    const flagBtn = $('.flag-btn');
    if (flagBtn) {
      flagBtn.classList.toggle('flagged', !!flag);
      flagBtn.innerHTML = `<svg viewBox="0 0 24 24" fill="${flag ? 'currentColor' : 'none'}" stroke="currentColor" stroke-width="2"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg>
      ${flag ? 'Flagged' : 'Flag'}`;
    }

    // Mark options
    $$('.option-btn').forEach(btn => {
      btn.classList.remove('selected');
      if (btn.dataset.option === state.answers[qNum]) btn.classList.add('selected');
    });

    // Counter
    const counter = $('.question-counter');
    if (counter) counter.innerHTML = `Question <strong>${qNum}</strong> of <strong>${state.total}</strong>`;

    // Prev/Next buttons
    const prevBtn = $('#prev-btn');
    const nextBtn = $('#next-btn');
    if (prevBtn) prevBtn.disabled = qNum <= 1;
    if (nextBtn) nextBtn.textContent = qNum >= state.total ? 'Review & Submit' : 'Next →';

    // Update navigator highlight
    $$('.nav-q-btn').forEach(b => {
      b.classList.remove('current');
      if (parseInt(b.dataset.q) === qNum) b.classList.add('current');
    });

    state.current = qNum;
  }

  /* ── Timer ───────────────────────────────────────────────── */
  function startTimer() {
    const display = $('#exam-timer');
    if (!display) return;

    function tick() {
      if (state.timeLeft <= 0) {
        display.textContent = '00:00';
        autoSubmit();
        return;
      }
      const m = Math.floor(state.timeLeft / 60);
      const s = state.timeLeft % 60;
      display.textContent = `${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;

      display.classList.remove('warning', 'danger');
      if      (state.timeLeft <= 300) display.classList.add('danger');  // last 5 min
      else if (state.timeLeft <= 600) display.classList.add('warning'); // last 10 min

      state.timeLeft--;
    }
    tick();
    state.timerInterval = setInterval(tick, 1000);
  }

  /* ── Auto-save every 30 s ────────────────────────────────── */
  function startAutoSave() {
    state.autoSaveInterval = setInterval(saveProgress, 30000);
  }

  async function saveProgress() {
    if (!state.attemptId) return;
    try {
      const token = document.querySelector('meta[name=csrf-token]')?.content;
      await fetch(`/api/cbt/save-progress/${state.attemptId}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token || '' },
        body: JSON.stringify({ answers: state.answers }),
      });
    } catch (_) { /* silent — try again next tick */ }
  }

  /* ── Answer selection ────────────────────────────────────── */
  function selectAnswer(qNum, option) {
    state.answers[qNum] = option;
    $$('.option-btn').forEach(b => b.classList.toggle('selected', b.dataset.option === option));
    // Update navigator dot
    const navBtn = $(`.nav-q-btn[data-q="${qNum}"]`);
    if (navBtn) { navBtn.classList.remove('flagged-q'); navBtn.classList.add('answered'); }
    updateProgressBar();
  }

  /* ── Flag question ───────────────────────────────────────── */
  function toggleFlag() {
    state.flags[state.current] = !state.flags[state.current];
    renderQuestion(state.current);
    const navBtn = $(`.nav-q-btn[data-q="${state.current}"]`);
    if (navBtn) {
      if (state.flags[state.current]) {
        navBtn.classList.add('flagged-q');
        navBtn.classList.remove('answered');
      } else {
        navBtn.classList.remove('flagged-q');
        if (state.answers[state.current]) navBtn.classList.add('answered');
      }
    }
  }

  /* ── Navigator ───────────────────────────────────────────── */
  function buildNavigator() {
    const grid = $('.navigator-grid');
    if (!grid) return;
    grid.innerHTML = '';
    for (let i = 1; i <= state.total; i++) {
      const btn = document.createElement('button');
      btn.className = 'nav-q-btn' + (i === state.current ? ' current' : '');
      btn.dataset.q = i;
      btn.textContent = i;
      on(btn, 'click', () => renderQuestion(i));
      grid.appendChild(btn);
    }
  }

  /* ── Progress bar ────────────────────────────────────────── */
  function updateProgressBar() {
    const answered = Object.keys(state.answers).length;
    const pct = state.total ? (answered / state.total) * 100 : 0;
    const bar = $('.exam-progress-fill');
    if (bar) bar.style.width = pct + '%';
  }

  /* ── Submit ──────────────────────────────────────────────── */
  function openSubmitModal() {
    const answered = Object.keys(state.answers).length;
    const flagged  = Object.keys(state.flags).filter(k => state.flags[k]).length;
    const unanswered = state.total - answered;

    $('#modal-answered-count')  && ($('#modal-answered-count').textContent  = answered);
    $('#modal-unanswered-count') && ($('#modal-unanswered-count').textContent = unanswered);
    $('#modal-flagged-count')   && ($('#modal-flagged-count').textContent   = flagged);

    const m = $('.modal-backdrop');
    if (m) m.classList.add('open');
  }

  function closeModal() {
    const m = $('.modal-backdrop');
    if (m) m.classList.remove('open');
  }

  async function doSubmit() {
    clearInterval(state.timerInterval);
    clearInterval(state.autoSaveInterval);
    window.onbeforeunload = null;

    const form = $('#submit-form');
    if (form) {
      // Populate hidden inputs
      Object.entries(state.answers).forEach(([q, a]) => {
        const input = form.querySelector(`[name="answers[${q}]"]`) || (() => {
          const inp = document.createElement('input');
          inp.type = 'hidden'; inp.name = `answers[${q}]`; inp.value = a;
          form.appendChild(inp); return inp;
        })();
        input.value = a;
      });
      form.submit();
    }
  }

  function autoSubmit() {
    clearInterval(state.timerInterval);
    clearInterval(state.autoSaveInterval);
    showToast('Time Up!', 'Your exam has been submitted automatically.', 'warning');
    setTimeout(doSubmit, 2000);
  }

  /* ── Keyboard shortcuts ──────────────────────────────────── */
  function handleKeydown(e) {
    if (['INPUT','TEXTAREA'].includes(e.target.tagName)) return;
    switch (e.key.toUpperCase()) {
      case 'A': selectAnswer(state.current, 'A'); break;
      case 'B': selectAnswer(state.current, 'B'); break;
      case 'C': selectAnswer(state.current, 'C'); break;
      case 'D': selectAnswer(state.current, 'D'); break;
      case 'ARROWLEFT': case 'ARROWUP':
        if (state.current > 1) renderQuestion(state.current - 1);
        break;
      case 'ARROWRIGHT': case 'ARROWDOWN':
        if (state.current < state.total) renderQuestion(state.current + 1);
        break;
      case 'F': toggleFlag(); break;
      case 'ESCAPE': closeModal(); break;
    }
  }

  /* ── Expose public API ───────────────────────────────────── */
  return { init, selectAnswer, toggleFlag, openSubmitModal, closeModal, doSubmit, renderQuestion, state };
})();

/* ── Photo upload & preview ──────────────────────────────── */
function initPhotoUpload() {
  const zone    = $('.photo-upload-zone');
  const input   = $('#photo-input');
  const preview = $('.photo-preview-img');
  if (!zone || !input) return;

  on(zone,  'click', () => input.click());
  on(input, 'change', handleFile);

  zone.addEventListener('dragover',  e => { e.preventDefault(); zone.classList.add('drag-over'); });
  zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
  zone.addEventListener('drop', e => {
    e.preventDefault();
    zone.classList.remove('drag-over');
    const file = e.dataTransfer.files[0];
    if (file) processPhoto(file);
  });

  function handleFile(e) { const f = e.target.files[0]; if (f) processPhoto(f); }

  function processPhoto(file) {
    if (!file.type.startsWith('image/')) { showToast('Invalid file', 'Please select an image file.', 'error'); return; }
    if (file.size > 2 * 1024 * 1024)    { showToast('File too large', 'Photo must be under 2MB.', 'error'); return; }
    const reader = new FileReader();
    reader.onload = e => {
      if (preview) { preview.src = e.target.result; preview.classList.add('visible'); }
      // Also update sidebar avatar and header avatar
      $$('.sidebar-avatar img, .header-avatar img').forEach(img => img.src = e.target.result);
    };
    reader.readAsDataURL(file);
  }
}

/* ── Password strength meter ─────────────────────────────── */
function initPasswordStrength() {
  const input = $('#new-password');
  const bars  = $$('.strength-bar');
  const label = $('.strength-label');
  if (!input || !bars.length) return;

  on(input, 'input', () => {
    const val = input.value;
    const score =
      (val.length >= 8 ? 1 : 0) +
      (/[A-Z]/.test(val) ? 1 : 0) +
      (/[0-9]/.test(val) ? 1 : 0) +
      (/[^A-Za-z0-9]/.test(val) ? 1 : 0);

    bars.forEach((bar, i) => {
      bar.classList.remove('weak', 'medium', 'strong');
      if (i < score) {
        if (score <= 1) bar.classList.add('weak');
        else if (score <= 3) bar.classList.add('medium');
        else bar.classList.add('strong');
      }
    });

    if (label) {
      label.textContent = score === 0 ? '' :
        score <= 1 ? 'Weak password' :
        score <= 3 ? 'Good password' : 'Strong password';
    }
  });
}

/* ── Confirm password match ──────────────────────────────── */
function initPasswordMatch() {
  const pass    = $('#new-password');
  const confirm = $('#confirm-password');
  const hint    = $('#password-match-hint');
  if (!pass || !confirm) return;

  function check() {
    if (!confirm.value) { if (hint) hint.textContent = ''; return; }
    const match = pass.value === confirm.value;
    if (hint) {
      hint.textContent = match ? '✓ Passwords match' : '✗ Passwords do not match';
      hint.style.color = match ? 'var(--clr-active)' : 'var(--clr-danger)';
    }
  }

  on(pass,    'input', check);
  on(confirm, 'input', check);
}

/* ── Term selector on results page ──────────────────────── */
function initTermSelector() {
  $$('.term-pill').forEach(pill => {
    on(pill, 'click', () => {
      $$('.term-pill').forEach(p => p.classList.remove('active'));
      pill.classList.add('active');
      const termId = pill.dataset.term;
      $$('[data-term-results]').forEach(block => {
        block.classList.toggle('hidden', block.dataset.termResults !== termId);
      });
    });
  });
}

/* ── CBT exam: delegate option clicks ─────────────────────── */
function initOptionClicks() {
  on(document, 'click', e => {
    const btn = e.target.closest('.option-btn');
    if (!btn) return;
    const q = btn.closest('[data-question-slide]')?.dataset.questionSlide;
    if (q) CBT.selectAnswer(q, btn.dataset.option);
  });

  on($('#flag-btn-main'), 'click', () => CBT.toggleFlag());
  on($('#prev-btn'),       'click', () => CBT.renderQuestion(CBT.state.current - 1));
  on($('#next-btn'),       'click', () => {
    if (CBT.state.current >= CBT.state.total) CBT.openSubmitModal();
    else CBT.renderQuestion(CBT.state.current + 1);
  });
  on($('#submit-btn'), 'click', () => CBT.openSubmitModal());
  on($('#modal-cancel-btn'), 'click', () => CBT.closeModal());
  on($('#modal-confirm-btn'), 'click', () => CBT.doSubmit());
  on($('.modal-backdrop'), 'click', e => { if (e.target === e.currentTarget) CBT.closeModal(); });
}

/* ── Boot everything ─────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
  initSidebar();
  initDropdowns();
  initTabs();
  initScoreBars();
  initLobbyCountdown();
  initPhotoUpload();
  initPasswordStrength();
  initPasswordMatch();
  initTermSelector();
  CBT.init();
  initOptionClicks();
});
