/* ============================================================
   NaijaSchoolMS — Parent Portal  |  parent.js
   Sections: Sidebar · Dropdowns · Child Switcher · Toasts
             Score Bars · Fee Checkout · Paystack · Filters
             Announcements mark-as-read
   ============================================================ */

document.addEventListener('DOMContentLoaded', () => {

  /* ── 1. SIDEBAR MOBILE ────────────────────────────────── */
  const sidebar   = document.getElementById('sidebar');
  const overlay   = document.getElementById('sidebar-overlay');
  const hamburger = document.getElementById('hamburger-btn');

  function openSidebar()  { sidebar?.classList.add('open');    overlay?.classList.add('open');    document.body.style.overflow = 'hidden'; }
  function closeSidebar() { sidebar?.classList.remove('open'); overlay?.classList.remove('open'); document.body.style.overflow = ''; }

  hamburger?.addEventListener('click', openSidebar);
  overlay?.addEventListener('click', closeSidebar);

  /* ── 2. DROPDOWNS ─────────────────────────────────────── */
  document.querySelectorAll('[data-dropdown]').forEach(trigger => {
    trigger.addEventListener('click', e => {
      e.stopPropagation();
      const menu   = document.getElementById(trigger.dataset.dropdown);
      const isOpen = menu?.classList.contains('open');
      document.querySelectorAll('.dropdown-menu.open').forEach(m => m.classList.remove('open'));
      if (!isOpen) menu?.classList.add('open');
    });
  });
  document.addEventListener('click', () => {
    document.querySelectorAll('.dropdown-menu.open').forEach(m => m.classList.remove('open'));
  });

  /* ── 3. CHILD SWITCHER ────────────────────────────────── */
  const childTabs = document.querySelectorAll('[data-child-tab]');
  childTabs.forEach(tab => {
    tab.addEventListener('click', () => {
      const childId = tab.dataset.childTab;
      // Update tab pills
      childTabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      // Show/hide child panels
      document.querySelectorAll('[data-child-panel]').forEach(panel => {
        panel.style.display = panel.dataset.childPanel === childId ? '' : 'none';
      });
      // Update sidebar child indicator
      const nameEl = document.getElementById('current-child-name');
      if (nameEl) nameEl.textContent = tab.querySelector('.child-tab-name')?.textContent || '';
    });
  });

  /* ── 4. GREETING ──────────────────────────────────────── */
  const greetEl = document.getElementById('greeting-time');
  if (greetEl) {
    const h = new Date().getHours();
    greetEl.textContent = h < 12 ? 'Good Morning' : h < 17 ? 'Good Afternoon' : 'Good Evening';
  }

  /* ── 5. TOASTS ────────────────────────────────────────── */
  window.showToast = (msg, type = 'info', duration = 3500) => {
    const wrap = document.getElementById('toast-wrap');
    if (!wrap) return;
    const icons = {
      success: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>',
      error:   '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg>',
      info:    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>',
    };
    const t = document.createElement('div');
    t.className = `toast t-${type}`;
    t.innerHTML = (icons[type] || icons.info) + `<span>${msg}</span>`;
    wrap.appendChild(t);
    setTimeout(() => t.remove(), duration);
  };

  /* ── 6. SCORE BAR ANIMATIONS ──────────────────────────── */
  const animateBars = () => {
    document.querySelectorAll('.score-bar-fill[data-score]').forEach(bar => {
      const score = parseInt(bar.dataset.score);
      bar.style.width = score + '%';
      bar.style.background = score >= 70 ? '#16a34a' : score >= 50 ? '#d97706' : '#dc2626';
    });
  };
  const io = new IntersectionObserver(entries => {
    if (entries.some(e => e.isIntersecting)) { animateBars(); io.disconnect(); }
  }, { threshold: .1 });
  document.querySelector('.score-bar-fill') && io.observe(document.querySelector('.score-bar-fill'));

  /* ── 7. FILTER PILLS ──────────────────────────────────── */
  document.querySelectorAll('[data-filter-group]').forEach(group => {
    const target = group.dataset.filterGroup;
    group.querySelectorAll('[data-filter]').forEach(btn => {
      btn.addEventListener('click', () => {
        group.querySelectorAll('[data-filter]').forEach(b => {
          b.classList.remove('bg-forest-700', 'text-white', 'border-forest-700');
          b.classList.add('bg-white', 'text-gray-500', 'border-gray-200');
        });
        btn.classList.add('bg-forest-700', 'text-white', 'border-forest-700');
        btn.classList.remove('bg-white', 'text-gray-500', 'border-gray-200');
        const filter = btn.dataset.filter;
        document.querySelectorAll(`[data-filter-target="${target}"]`).forEach(item => {
          item.style.display = (filter === 'all' || item.dataset.category === filter) ? '' : 'none';
        });
      });
    });
  });

  /* ── 8. FEE CHECKOUT ──────────────────────────────────── */
  const feeItems  = document.querySelectorAll('.fee-item[data-amount]');
  const totalEl   = document.getElementById('checkout-total');
  const countEl   = document.getElementById('checkout-count');
  const payBtn    = document.getElementById('pay-btn');

  function recalcFees() {
    let total = 0, count = 0;
    feeItems.forEach(item => {
      if (item.classList.contains('selected')) {
        total += parseFloat(item.dataset.amount || 0);
        count++;
      }
    });
    if (totalEl) totalEl.textContent = '₦' + total.toLocaleString('en-NG', { minimumFractionDigits: 2 });
    if (countEl) countEl.textContent = count + ' item' + (count !== 1 ? 's' : '');
    if (payBtn)  { payBtn.disabled = (total === 0); payBtn.dataset.amount = total; }
  }

  feeItems.forEach(item => {
    item.addEventListener('click', () => {
      item.classList.toggle('selected');
      recalcFees();
    });
  });
  recalcFees();

  /* ── 9. PAYSTACK INITIATION ───────────────────────────── */
  window.initiatePaystack = (btn) => {
    const amount  = parseFloat(btn.dataset.amount || 0);
    const email   = btn.dataset.email || 'parent@example.com';
    const ref     = 'NSM_' + Date.now() + '_' + Math.random().toString(36).substr(2, 6).toUpperCase();
    const meta    = btn.dataset.meta || '{}';

    if (amount <= 0) { showToast('Please select at least one fee item.', 'error'); return; }

    // Collect selected fee IDs
    const selectedIds = [];
    document.querySelectorAll('.fee-item.selected[data-fee-id]').forEach(el => selectedIds.push(el.dataset.feeId));

    if (typeof PaystackPop === 'undefined') {
      // Paystack.js not loaded — simulate for UI preview
      showToast('Paystack loaded — redirecting…', 'info');
      setTimeout(() => { window.location.href = '/parent/fees/success?reference=' + ref; }, 1200);
      return;
    }

    const handler = PaystackPop.setup({
      key:    btn.dataset.key || 'pk_test_xxxxxxxxx',
      email,
      amount: Math.round(amount * 100), // in kobo
      ref,
      currency: 'NGN',
      metadata: { fee_ids: selectedIds, custom_fields: [] },
      callback: (response) => {
        window.location.href = '/parent/fees/callback?reference=' + response.reference;
      },
      onClose: () => {
        showToast('Payment window closed. Your fees are still outstanding.', 'info');
      },
    });
    handler.openIframe();
  };

  /* ── 10. SELECT ALL / DESELECT ALL FEES ──────────────── */
  window.selectAllFees = () => {
    document.querySelectorAll('.fee-item[data-amount]').forEach(i => i.classList.add('selected'));
    recalcFees();
  };
  window.deselectAllFees = () => {
    document.querySelectorAll('.fee-item[data-amount]').forEach(i => i.classList.remove('selected'));
    recalcFees();
  };

  /* ── 11. ANNOUNCEMENTS MARK AS READ ───────────────────── */
  window.markRead = (btn, id) => {
    const card = btn.closest('[data-ann-card]');
    card?.classList.add('opacity-60');
    btn.style.display = 'none';
    const dot = card?.querySelector('[data-unread-dot]');
    if (dot) dot.style.display = 'none';
    fetch(`/parent/announcements/${id}/read`, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '' }
    }).catch(() => {});
  };
  window.markAllRead = () => {
    document.querySelectorAll('[data-ann-card]').forEach(card => {
      card.classList.add('opacity-60');
      card.querySelector('[data-unread-dot]')?.remove();
      const btn = card.querySelector('[data-mark-read-btn]'); if (btn) btn.style.display = 'none';
    });
  };

  /* ── 12. ANNOUNCEMENT EXPAND/COLLAPSE ─────────────────── */
  document.querySelectorAll('[data-ann-toggle]').forEach(btn => {
    btn.addEventListener('click', () => {
      const card = btn.closest('[data-ann-card]');
      const body = card?.querySelector('[data-ann-body]');
      const isOpen = body?.classList.contains('hidden');
      body?.classList.toggle('hidden', !isOpen);
      btn.textContent = isOpen ? 'Show less ↑' : 'Read more →';
      // Auto-mark as read
      const id = card?.dataset.annId;
      const markBtn = card?.querySelector('[data-mark-read-btn]');
      if (markBtn && id) markRead(markBtn, id);
    });
  });

  /* ── 13. TABS (results per term) ──────────────────────── */
  document.querySelectorAll('[data-tab-nav]').forEach(nav => {
    nav.querySelectorAll('[data-tab]').forEach(btn => {
      btn.addEventListener('click', () => {
        const target = btn.dataset.tab;
        const scope  = btn.closest('[data-tab-scope]') || document;
        nav.querySelectorAll('[data-tab]').forEach(b => {
          b.classList.remove('text-forest-700', 'border-forest-700', 'border-b-2');
          b.classList.add('text-gray-400', 'border-transparent');
        });
        btn.classList.add('text-forest-700', 'border-forest-700', 'border-b-2');
        btn.classList.remove('text-gray-400', 'border-transparent');
        scope.querySelectorAll('[data-tab-pane]').forEach(p => p.classList.add('hidden'));
        scope.querySelector(`[data-tab-pane="${target}"]`)?.classList.remove('hidden');
      });
    });
  });

});
