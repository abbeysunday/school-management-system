/* ============================================================
   NaijaSchoolMS — Student Portal  |  student.js
   Sections: Sidebar · Dropdowns · Tabs · Toasts · Score Bars
             Countdown · CBT Exam Engine · Filters · Profile
   ============================================================ */

document.addEventListener('DOMContentLoaded', () => {

  /* ── 1. SIDEBAR MOBILE TOGGLE ─────────────────────────── */
  const sidebar  = document.getElementById('sidebar');
  const overlay  = document.getElementById('sidebar-overlay');
  const hamburger = document.getElementById('hamburger-btn');

  function openSidebar()  { sidebar?.classList.add('open');    overlay?.classList.add('open');    document.body.style.overflow = 'hidden'; }
  function closeSidebar() { sidebar?.classList.remove('open'); overlay?.classList.remove('open'); document.body.style.overflow = ''; }

  hamburger?.addEventListener('click', openSidebar);
  overlay?.addEventListener('click', closeSidebar);

  /* ── 2. DROPDOWN MENUS ────────────────────────────────── */
  document.querySelectorAll('[data-dropdown]').forEach(trigger => {
    trigger.addEventListener('click', e => {
      e.stopPropagation();
      const menuId = trigger.dataset.dropdown;
      const menu   = document.getElementById(menuId);
      const isOpen = menu?.classList.contains('open');
      // close all
      document.querySelectorAll('.dropdown-menu.open').forEach(m => m.classList.remove('open'));
      if (!isOpen) menu?.classList.add('open');
    });
  });
  document.addEventListener('click', () => {
    document.querySelectorAll('.dropdown-menu.open').forEach(m => m.classList.remove('open'));
  });

  /* ── 3. TABS (profile) ────────────────────────────────── */
  document.querySelectorAll('[data-tab-nav]').forEach(nav => {
    nav.querySelectorAll('[data-tab]').forEach(btn => {
      btn.addEventListener('click', () => {
        const target = btn.dataset.tab;
        const scope  = btn.closest('[data-tab-scope]') || document;
        // deactivate all buttons
        nav.querySelectorAll('[data-tab]').forEach(b => {
          b.classList.remove('text-forest-700', 'border-forest-700', 'border-b-2');
          b.classList.add('text-gray-400', 'border-transparent');
        });
        // activate clicked
        btn.classList.add('text-forest-700', 'border-forest-700', 'border-b-2');
        btn.classList.remove('text-gray-400', 'border-transparent');
        // switch panes
        scope.querySelectorAll('[data-tab-pane]').forEach(p => p.classList.add('hidden'));
        scope.querySelector(`[data-tab-pane="${target}"]`)?.classList.remove('hidden');
      });
    });
  });

  /* ── 4. TOASTS ────────────────────────────────────────── */
  window.showToast = (msg, type = 'info', duration = 3500) => {
    const wrap = document.getElementById('toast-wrap');
    if (!wrap) return;
    const icons = {
      success: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>',
      error:   '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>',
      info:    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>',
    };
    const t = document.createElement('div');
    t.className = `toast t-${type}`;
    t.innerHTML = (icons[type] || icons.info) + `<span>${msg}</span>`;
    wrap.appendChild(t);
    setTimeout(() => t.remove(), duration);
  };

  /* ── 5. SCORE BAR ANIMATIONS ──────────────────────────── */
  const animateBars = () => {
    document.querySelectorAll('.score-bar-fill[data-score]').forEach(bar => {
      bar.style.width = bar.dataset.score + '%';
      const score = parseInt(bar.dataset.score);
      bar.style.background = score >= 70 ? '#16a34a' : score >= 50 ? '#d97706' : '#dc2626';
    });
  };
  const io = new IntersectionObserver(entries => {
    entries.forEach(e => { if (e.isIntersecting) { animateBars(); io.disconnect(); } });
  }, { threshold: .2 });
  const barWrap = document.querySelector('.score-bar-fill');
  if (barWrap) io.observe(barWrap);

  /* ── 6. PILL FILTER TABS (CBT index, Announcements) ──── */
  document.querySelectorAll('[data-filter-group]').forEach(group => {
    const target = group.dataset.filterGroup;
    group.querySelectorAll('[data-filter]').forEach(btn => {
      btn.addEventListener('click', () => {
        group.querySelectorAll('[data-filter]').forEach(b => {
          b.classList.remove('bg-forest-700', 'text-white', 'border-forest-700');
          b.classList.add('border-gray-200', 'text-gray-500');
        });
        btn.classList.add('bg-forest-700', 'text-white', 'border-forest-700');
        btn.classList.remove('border-gray-200', 'text-gray-500');
        const filter = btn.dataset.filter;
        document.querySelectorAll(`[data-filter-target="${target}"]`).forEach(item => {
          const show = filter === 'all' || item.dataset.category === filter;
          item.style.display = show ? '' : 'none';
        });
      });
    });
  });

  /* ── 7. GREETING TIME-OF-DAY ──────────────────────────── */
  const greetEl = document.getElementById('greeting-time');
  if (greetEl) {
    const h = new Date().getHours();
    greetEl.textContent = h < 12 ? 'Good Morning' : h < 17 ? 'Good Afternoon' : 'Good Evening';
  }

  /* ── 8. TIMETABLE DAY SELECTOR ────────────────────────── */
  window.selectTimetableDay = (dayNum) => {
    document.querySelectorAll('[data-week-card]').forEach(c => {
      const isThis = +c.dataset.weekCard === dayNum;
      c.classList.toggle('bg-forest-700', isThis);
      c.classList.toggle('text-white', isThis);
      c.classList.toggle('border-forest-700', isThis);
    });
    document.querySelectorAll('[data-day-row]').forEach(r => {
      r.style.display = +r.dataset.dayRow === dayNum ? '' : 'none';
    });
    const label = document.getElementById('day-label');
    if (label) label.textContent = ['Monday','Tuesday','Wednesday','Thursday','Friday'][dayNum-1] || '';
  };

  /* ── 9. LOBBY COUNTDOWN ───────────────────────────────── */
  const lobbyTarget = document.getElementById('lobby-target');
  if (lobbyTarget) {
    const target     = new Date(lobbyTarget.dataset.target).getTime();
    const enterBtn   = document.getElementById('lobby-enter-btn');
    const confirmChk = document.getElementById('lobby-confirm');

    const tick = () => {
      const diff = target - Date.now();
      if (diff <= 0) {
        ['cd-h','cd-m','cd-s'].forEach(id => { const el = document.getElementById(id); if (el) el.textContent = '00'; });
        if (confirmChk?.checked) enterBtn?.removeAttribute('disabled');
        return;
      }
      const h = String(Math.floor(diff / 3600000)).padStart(2,'0');
      const m = String(Math.floor((diff % 3600000) / 60000)).padStart(2,'0');
      const s = String(Math.floor((diff % 60000) / 1000)).padStart(2,'0');
      const cdH = document.getElementById('cd-h'); if (cdH) cdH.textContent = h;
      const cdM = document.getElementById('cd-m'); if (cdM) cdM.textContent = m;
      const cdS = document.getElementById('cd-s'); if (cdS) cdS.textContent = s;
      setTimeout(tick, 1000);
    };
    tick();

    confirmChk?.addEventListener('change', () => {
      const past = Date.now() >= target;
      if (confirmChk.checked && past) {
        enterBtn?.removeAttribute('disabled');
      } else {
        enterBtn?.setAttribute('disabled', '');
      }
    });
  }

  /* ── 10. CBT EXAM ENGINE ──────────────────────────────── */
  const examEl = document.getElementById('exam-root');
  if (examEl) {
    let currentQ  = 0;
    const answers = {};
    const flags   = {};
    const total   = parseInt(examEl.dataset.total || '1');
    let   seconds = parseInt(examEl.dataset.duration || '2700'); // 45 min default
    let   autoSaveTimer;

    const timerEl   = document.getElementById('exam-timer');
    const progressEl = document.getElementById('exam-progress-fill');
    const qCountEl  = document.getElementById('q-count');

    // ── Timer
    const tick = () => {
      seconds--;
      const m = Math.floor(seconds / 60), s = seconds % 60;
      if (timerEl) {
        timerEl.textContent = `${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
        timerEl.className = 'exam-timer' + (seconds < 300 ? ' danger' : seconds < 600 ? ' warn' : '');
      }
      if (seconds <= 0) { clearInterval(examInterval); submitExam(true); }
    };
    const examInterval = setInterval(tick, 1000);

    // ── Navigate
    window.gotoQuestion = (n) => {
      document.querySelectorAll('.question-slide').forEach(s => s.classList.remove('active'));
      document.querySelector(`.question-slide[data-q="${n}"]`)?.classList.add('active');
      document.querySelectorAll('.nav-q-btn').forEach(b => b.classList.remove('current'));
      document.querySelector(`.nav-q-btn[data-qn="${n}"]`)?.classList.add('current');
      currentQ = n;
      if (qCountEl) qCountEl.textContent = `${n+1} / ${total}`;
      if (progressEl) progressEl.style.width = `${((n+1)/total)*100}%`;
    };
    window.prevQ = () => { if (currentQ > 0)       gotoQuestion(currentQ - 1); };
    window.nextQ = () => { if (currentQ < total-1) gotoQuestion(currentQ + 1); };

    // ── Answer
    document.querySelectorAll('.option-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const q = parseInt(btn.dataset.q);
        answers[q] = btn.dataset.opt;
        document.querySelectorAll(`.option-btn[data-q="${q}"]`).forEach(b => b.classList.remove('chosen'));
        btn.classList.add('chosen');
        const nav = document.querySelector(`.nav-q-btn[data-qn="${q}"]`);
        if (nav && !flags[q]) { nav.classList.add('answered'); nav.classList.remove('flagged'); }
      });
    });

    // ── Flag
    window.toggleFlag = () => {
      flags[currentQ] = !flags[currentQ];
      const nav = document.querySelector(`.nav-q-btn[data-qn="${currentQ}"]`);
      if (nav) {
        nav.classList.toggle('flagged', flags[currentQ]);
        nav.classList.toggle('answered', !flags[currentQ] && !!answers[currentQ]);
      }
      const btn = document.getElementById('flag-btn');
      if (btn) btn.classList.toggle('text-yellow-500', flags[currentQ]);
    };

    // ── Auto-save every 30s
    autoSaveTimer = setInterval(() => {
      const attemptId = examEl.dataset.attempt;
      if (!attemptId) return;
      fetch(`/student/exams/save/${attemptId}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '' },
        body: JSON.stringify({ answers })
      }).catch(() => {});
    }, 30000);

    // ── Submit modal
    window.openSubmitModal = () => {
      const answered = Object.keys(answers).length;
      const flagged  = Object.keys(flags).filter(k => flags[k]).length;
      const unanswered = total - answered;
      const el = document.getElementById('modal-answered');   if (el) el.textContent = answered;
      const el2 = document.getElementById('modal-unanswered'); if (el2) el2.textContent = unanswered;
      const el3 = document.getElementById('modal-flagged');    if (el3) el3.textContent = flagged;
      document.getElementById('submit-modal')?.classList.add('open');
    };
    window.closeSubmitModal = () => {
      document.getElementById('submit-modal')?.classList.remove('open');
    };

    const submitExam = (auto = false) => {
      clearInterval(examInterval);
      clearInterval(autoSaveTimer);
      const form = document.getElementById('exam-submit-form');
      if (auto) {
        const input = document.createElement('input');
        input.type = 'hidden'; input.name = 'auto_submit'; input.value = '1';
        form?.appendChild(input);
      }
      form?.submit();
    };
    window.confirmSubmit = () => submitExam(false);

    // ── Keyboard shortcuts
    document.addEventListener('keydown', e => {
      if (['INPUT','TEXTAREA'].includes(e.target.tagName)) return;
      if (e.key === 'ArrowRight' || e.key === 'ArrowDown') { e.preventDefault(); nextQ(); }
      if (e.key === 'ArrowLeft'  || e.key === 'ArrowUp')   { e.preventDefault(); prevQ(); }
      if (e.key === 'f' || e.key === 'F') toggleFlag();
      if (['a','A','1'].includes(e.key)) document.querySelector(`.option-btn[data-q="${currentQ}"][data-opt="A"]`)?.click();
      if (['b','B','2'].includes(e.key)) document.querySelector(`.option-btn[data-q="${currentQ}"][data-opt="B"]`)?.click();
      if (['c','C','3'].includes(e.key)) document.querySelector(`.option-btn[data-q="${currentQ}"][data-opt="C"]`)?.click();
      if (['d','D','4'].includes(e.key)) document.querySelector(`.option-btn[data-q="${currentQ}"][data-opt="D"]`)?.click();
    });

    // ── Back button lock
    history.pushState(null, '', location.href);
    window.addEventListener('popstate', () => { history.pushState(null, '', location.href); });
    window.addEventListener('beforeunload', e => { e.preventDefault(); e.returnValue = ''; });

    // init
    gotoQuestion(0);
  }

  /* ── 11. ANNOUNCEMENTS MARK AS READ ───────────────────── */
  window.markRead = (btn, id) => {
    const card = btn.closest('[data-ann-card]');
    card?.classList.remove('border-l-forest-700');
    card?.classList.add('opacity-60');
    btn.style.display = 'none';
    const dot = card?.querySelector('[data-unread-dot]');
    if (dot) dot.style.display = 'none';
    fetch(`/student/announcements/${id}/read`, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '' }
    }).catch(() => {});
  };
  window.markAllRead = () => {
    document.querySelectorAll('[data-ann-card]').forEach(card => {
      card.classList.add('opacity-60');
      const dot = card.querySelector('[data-unread-dot]'); if (dot) dot.style.display = 'none';
      const btn = card.querySelector('[data-mark-read-btn]'); if (btn) btn.style.display = 'none';
    });
  };

  /* ── 12. PROFILE PHOTO UPLOAD ─────────────────────────── */
  const photoZone  = document.getElementById('photo-zone');
  const photoInput = document.getElementById('photo-input');
  const photoPreview = document.getElementById('photo-preview');

  photoZone?.addEventListener('click', () => photoInput?.click());
  photoZone?.addEventListener('dragover', e => { e.preventDefault(); photoZone.classList.add('drag-over'); });
  photoZone?.addEventListener('dragleave', () => photoZone.classList.remove('drag-over'));
  photoZone?.addEventListener('drop', e => {
    e.preventDefault(); photoZone.classList.remove('drag-over');
    const file = e.dataTransfer.files[0];
    if (file) previewPhoto(file);
  });
  photoInput?.addEventListener('change', () => {
    if (photoInput.files[0]) previewPhoto(photoInput.files[0]);
  });
  function previewPhoto(file) {
    const reader = new FileReader();
    reader.onload = e => {
      if (photoPreview) { photoPreview.src = e.target.result; photoPreview.style.display = 'block'; }
    };
    reader.readAsDataURL(file);
  }

  /* ── 13. PASSWORD STRENGTH ────────────────────────────── */
  const newPassEl = document.getElementById('new_password');
  const strBars   = document.querySelectorAll('.str-bar');
  const strLabel  = document.getElementById('str-label');

  newPassEl?.addEventListener('input', () => {
    const val = newPassEl.value;
    let score = 0;
    if (val.length >= 8) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    const level = score <= 1 ? 'weak' : score <= 2 ? 'medium' : 'strong';
    const labels = { weak: 'Weak', medium: 'Fair', strong: 'Strong' };
    strBars.forEach((bar, i) => {
      bar.className = 'str-bar';
      if (i < score) bar.classList.add(level);
    });
    if (strLabel) { strLabel.textContent = val ? labels[level] : ''; strLabel.className = `text-xs mt-1 font-semibold ${level === 'weak' ? 'text-red-500' : level === 'medium' ? 'text-yellow-600' : 'text-green-600'}`; }
  });

  const confirmPassEl = document.getElementById('password_confirmation');
  confirmPassEl?.addEventListener('input', () => {
    const match = newPassEl?.value === confirmPassEl.value;
    confirmPassEl.style.borderColor = confirmPassEl.value ? (match ? '#16a34a' : '#dc2626') : '';
  });

});
