/**
 * STOCK MANAGER — script.js corrigé
 * Corrections : validation sans blocage, mobile-safe, toast amélioré
 */

function svgIcon(name) {
    const icons = {
        check: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m5 12 5 5 9-9"></path></svg>',
        x: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"></path></svg>',
        warning:
            '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3 2.5 20h19L12 3Z"></path><path d="M12 9v5"></path><circle cx="12" cy="17" r="1"></circle></svg>',
        search: '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.5-3.5"></path></svg>',
        ordinateur:
            '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="12" rx="2"></rect><path d="M8 19h8"></path></svg>',
        telephone:
            '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="7" y="3" width="10" height="18" rx="2"></rect><circle cx="12" cy="17" r="1"></circle></svg>',
        television:
            '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="6" width="18" height="12" rx="2"></rect><path d="M8 3 12 6 16 3"></path></svg>',
    };
    const svg = icons[name] || '';
    return `<span class="icon-svg icon-${name}">${svg}</span>`;
}

/* ══════════════════════════════════════════
    1. TOAST
   ══════════════════════════════════════════ */
function showToast(message, type = 'success', duration = 3500) {
    let toast = document.getElementById('toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'toast';
        document.body.appendChild(toast);
    }
    toast.innerHTML =
        (type === 'success' ? svgIcon('check') : svgIcon('x')) + message;
    toast.className = `show ${type}`;
    clearTimeout(toast._timer);
    toast._timer = setTimeout(() => {
        toast.classList.remove('show');
    }, duration);
}

/* Intercept window.alert() */
window.alert = function (msg) {
    const isError =
        msg.toLowerCase().includes('déjà') ||
        msg.toLowerCase().includes('erreur') ||
        msg.toLowerCase().includes('incorrect');
    showToast(msg, isError ? 'error' : 'success');
};

/* ══════════════════════════════════════════
    2. MODALE DE CONFIRMATION
   ══════════════════════════════════════════ */
function createModal() {
    if (document.getElementById('confirm-modal')) return;
    const modal = document.createElement('div');
    modal.id = 'confirm-modal';
    modal.innerHTML = `
    <div class="modal-overlay" id="modal-overlay">
      <div class="modal-box">
        <div class="modal-icon">${svgIcon('warning')}</div>
        <p class="modal-message" id="modal-message">Êtes-vous sûr ?</p>
        <div class="modal-actions">
          <button class="modal-cancel" id="modal-cancel">Annuler</button>
          <button class="modal-confirm" id="modal-confirm">Supprimer</button>
        </div>
      </div>
    </div>`;
    modal.style.cssText =
        'position:fixed;inset:0;z-index:9998;display:none;align-items:center;justify-content:center;';
    document.body.appendChild(modal);

    const style = document.createElement('style');
    style.textContent = `
    .modal-overlay {
      position:fixed;inset:0;background:rgba(0,0,0,0.65);
      backdrop-filter:blur(6px);display:flex;
      align-items:center;justify-content:center;
      animation:fadeIn .2s ease;padding:1rem;
    }
    @keyframes fadeIn{from{opacity:0}to{opacity:1}}
    .modal-box {
      background:#fff;border:1px solid rgba(60,70,120,0.1);
      border-radius:16px;padding:2rem 2rem;max-width:340px;
      width:100%;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,.2);
      animation:scaleIn .25s cubic-bezier(.34,1.56,.64,1);
    }
    @keyframes scaleIn{from{transform:scale(.85);opacity:0}to{transform:scale(1);opacity:1}}
    .modal-icon{font-size:2.2rem;margin-bottom:.6rem}
    .modal-message{color:#141926;font-family:'DM Mono',monospace;font-size:.88rem;margin-bottom:1.4rem;line-height:1.6}
    .modal-actions{display:flex;gap:.75rem;justify-content:center}
    .modal-cancel{
      padding:.6rem 1.2rem;border-radius:8px;cursor:pointer;
      background:transparent;border:1px solid rgba(60,70,120,0.15);
      color:#6b728f;font-family:'DM Mono',monospace;font-size:.85rem;transition:.2s;
      min-height:44px;
    }
    .modal-cancel:hover{border-color:#e0294a;color:#e0294a;}
    .modal-confirm{
        padding:.6rem 1.4rem;border-radius:8px;cursor:pointer;
        background:#e0294a;border:none;color:#fff;
        font-family:'DM Mono',monospace;font-size:.85rem;
        box-shadow:0 2px 12px rgba(224,41,74,.3);transition:.2s;
        min-height:44px;
    }
    .modal-confirm:hover{background:#c0183a;transform:translateY(-1px)}`;
    document.head.appendChild(style);
}

function confirmModal(message) {
    return new Promise((resolve) => {
        createModal();
        const modal = document.getElementById('confirm-modal');
        document.getElementById('modal-message').textContent = message;
        modal.style.display = 'flex';

        const cancelBtn = document.getElementById('modal-cancel');
        const confirmBtn = document.getElementById('modal-confirm');

        function cleanup() {
            modal.style.display = 'none';
            cancelBtn.replaceWith(cancelBtn.cloneNode(true));
            confirmBtn.replaceWith(confirmBtn.cloneNode(true));
        }

        document.getElementById('modal-cancel').onclick = () => {
            cleanup();
            resolve(false);
        };
        document.getElementById('modal-confirm').onclick = () => {
            cleanup();
            resolve(true);
        };
    });
}

/* Intercept liens suppression */
document.addEventListener('click', async (e) => {
    const link = e.target.closest('a[href*="supprimer"]');
    if (!link) return;
    e.preventDefault();
    const confirmed = await confirmModal(
        'Supprimer ce produit définitivement ?',
    );
    if (confirmed) window.location.href = link.href;
});

/* ══════════════════════════════════════════
    3. VALIDATION FORMULAIRE
    CORRECTION : ne bloque jamais le bouton.
   ══════════════════════════════════════════ */
function setupFormValidation() {
    // Exclure les formulaires de tri/recherche (méthode GET)
    const form = document.querySelector(
        'form[method="post"], form[method="POST"]',
    );
    if (!form) return;

    const inputs = form.querySelectorAll(
        'input[type="text"], input[type="number"], input[type="password"]',
    );

    inputs.forEach((input) => {
        // Validation au blur (perte de focus)
        input.addEventListener('blur', () => validateField(input));
        // Nettoyage de l'erreur à la frappe
        input.addEventListener('input', () => {
            input.style.borderColor = '';
            const err = input
                .closest('.field-row, .form-group, td')
                ?.querySelector('.field-error');
            if (err) err.remove();
        });
    });

    form.addEventListener('submit', (e) => {
        let valid = true;
        inputs.forEach((input) => {
            if (!validateField(input)) valid = false;
        });
        if (!valid) {
            e.preventDefault();
            showToast('Veuillez corriger les erreurs.', 'error');
        }
        // CORRECTION : ne jamais désactiver le bouton submit
    });
}

function validateField(input) {
    const val = input.value.trim();
    let error = '';

    if (input.name === 'ref' && val.length < 2)
        error = 'Référence trop courte (min. 2 car.).';
    if (input.name === 'nom' && val.length < 2)
        error = 'Nom trop court (min. 2 car.).';
    if (input.name === 'prix' && (val === '' || isNaN(val) || +val <= 0))
        error = 'Prix invalide (doit être > 0).';
    if (input.type === 'password' && val.length < 3)
        error = 'Mot de passe trop court.';

    // Chercher le parent proche pour injecter l'erreur
    const parent =
        input.closest('.field-row, .form-group, td') || input.parentElement;

    const existing = parent.querySelector('.field-error');
    if (existing) existing.remove();

    if (error) {
        input.style.borderColor = '#e0294a';
        const span = document.createElement('span');
        span.className = 'field-error';
        span.textContent = error;
        span.style.cssText =
            'color:#e0294a;font-size:.72rem;display:block;margin-top:.3rem;';
        parent.appendChild(span);
        return false;
    }
    input.style.borderColor = '#00b37a';
    return true;
}

/* ══════════════════════════════════════════
    4. RECHERCHE LIVE (filtre tableau)
   ══════════════════════════════════════════ */
function setupLiveSearch() {
    const table = document.querySelector('.table-scroll table');
    if (!table) return;

    const tableScroll = table.closest('.table-scroll');
    if (!tableScroll) return;

    const wrapper = document.createElement('div');
    wrapper.style.cssText = 'position:relative;margin-bottom:1rem;';
    wrapper.innerHTML = `
    <span style="position:absolute;left:.9rem;top:50%;transform:translateY(-50%);color:#6b728f;">${svgIcon('search')}</span>
    <input type="text" id="live-filter" placeholder="Filtrer le tableau…" style="padding-left:2.6rem;font-size:16px;">`;
    tableScroll.parentElement.insertBefore(wrapper, tableScroll);

    document
        .getElementById('live-filter')
        .addEventListener('input', function () {
            const q = this.value.toLowerCase();
            const rows = table.querySelectorAll('tbody tr');
            let found = 0;
            rows.forEach((row) => {
                const match = row.textContent.toLowerCase().includes(q);
                row.style.display = match ? '' : 'none';
                if (match) found++;
            });

            let counter = document.getElementById('filter-counter');
            if (!counter) {
                counter = document.createElement('p');
                counter.id = 'filter-counter';
                counter.style.cssText =
                    'text-align:center;font-size:.78rem;color:#6b728f;margin-top:.5rem;';
                tableScroll.parentElement.insertBefore(
                    counter,
                    tableScroll.nextSibling,
                );
            }
            counter.textContent = q ? `${found} résultat(s) trouvé(s)` : '';
        });
}

/* ══════════════════════════════════════════
    5. BADGES CATÉGORIE
   ══════════════════════════════════════════ */
const CAT_ICONS = {
    ordinateur: svgIcon('ordinateur'),
    telephone: svgIcon('telephone'),
    television: svgIcon('television'),
};

function applyBadges() {
    const rows = document.querySelectorAll('table tbody tr');
    rows.forEach((row) => {
        // Seulement la colonne catégorie (2ème td)
        const catTd = row.querySelector('td:nth-child(2)');
        if (!catTd) return;
        const val = catTd.textContent.trim().toLowerCase();
        if (CAT_ICONS[val]) {
            catTd.innerHTML = `<span class="badge badge-${val}">${CAT_ICONS[val]}${catTd.textContent.trim()}</span>`;
        }
    });
}

/* ══════════════════════════════════════════
    6. ANIMATION LIGNES TABLEAU
   ══════════════════════════════════════════ */
function animateTableRows() {
    const rows = document.querySelectorAll('table tbody tr');
    rows.forEach((row, i) => {
        row.style.opacity = '0';
        row.style.transform = 'translateX(-6px)';
        row.style.transition = `opacity .3s ease ${i * 0.04}s, transform .3s ease ${i * 0.04}s`;
        requestAnimationFrame(() => {
            setTimeout(() => {
                row.style.opacity = '1';
                row.style.transform = 'translateX(0)';
            }, 10);
        });
    });
}

/* ══════════════════════════════════════════
    7. NAV ACTIVE
   ══════════════════════════════════════════ */
function highlightActiveNav() {
    const page = window.location.pathname.split('/').pop() || 'index.php';
    document.querySelectorAll('.nav-links a').forEach((a) => {
        const href = a.getAttribute('href') || '';
        if (
            href &&
            href !== 'deconnexion.php' &&
            page.includes(href.split('?')[0])
        ) {
            a.classList.add('active');
        }
    });
}

/* ══════════════════════════════════════════
    8. COMPTEUR CARACTÈRES
   ══════════════════════════════════════════ */
function setupCharCounters() {
    ['ref', 'nom', 'marque'].forEach((name) => {
        const input = document.querySelector(`[name="${name}"]`);
        if (!input) return;
        const counter = document.createElement('span');
        counter.style.cssText =
            'font-size:.7rem;color:#6b728f;float:right;margin-top:.2rem;';
        const max = input.maxLength > 0 ? input.maxLength : 50;
        counter.textContent = `${input.value.length} / ${max}`;
        input.parentElement.appendChild(counter);
        input.addEventListener('input', () => {
            counter.textContent = `${input.value.length} / ${max}`;
        });
    });
}

/* ══════════════════════════════════════════
   INIT
   ══════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
    highlightActiveNav();
    setupFormValidation();
    setupLiveSearch();
    applyBadges();
    animateTableRows();
    setupCharCounters();
});
