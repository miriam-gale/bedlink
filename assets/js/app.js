// assets/js/app.js (Mimi) — the toolbox every page loads
function toast(message, type = 'ok') {
  const el = document.createElement('div');
  el.className = 'toast toast--' + type;
  el.textContent = message;
  document.body.append(el);
  setTimeout(() => el.remove(), 4000);
}

// confirmDialog('Cancel this trip?') → waits for the user, then gives back true (Yes) or false (Cancel)
function confirmDialog(message) {
  return new Promise(resolve => {                          // "I'll answer later, when they click"
    const d = document.createElement('dialog');            // 1. a pop-up box
    const p = document.createElement('p');
    p.textContent = message;                               // 2. the question (safe text)
    const yes = Object.assign(document.createElement('button'), { className: 'btn btn--primary', textContent: 'Yes' });
    const no  = Object.assign(document.createElement('button'), { className: 'btn', textContent: 'Cancel' });
    yes.onclick = () => { d.close(); d.remove(); resolve(true);  };   // 3. Yes → true
    no.onclick  = () => { d.close(); d.remove(); resolve(false); };   //    Cancel → false
    d.append(p, no, yes);
    document.body.append(d);
    d.showModal();                                         // 4. show it and dim the page behind
  });
}


// ==========================================
// THEME MANAGEMENT
// ==========================================

function initTheme() {
    const isAmbulancePage =
        document.body.classList.contains('ambulance-page');

    // Ambulance pages must always use dark mode
    if (isAmbulancePage) {
        document.body.classList.add('dark');
        return;
    }

    // Restore the user's saved preference
    const savedTheme = localStorage.getItem('bedlink-theme');

    if (savedTheme === 'dark') {
        document.body.classList.add('dark');
    } else {
        document.body.classList.remove('dark');
    }
}

function toggleTheme() {
    // Prevent ambulance pages from changing themes
    if (document.body.classList.contains('ambulance-page')) {
        return;
    }

    const isDark = document.body.classList.toggle('dark');

    // Save the user's preference
    localStorage.setItem('bedlink-theme', isDark ? 'dark' : 'light');
}

// Initialize theme when the page loads
document.addEventListener('DOMContentLoaded', () => {
    initTheme();

    const themeToggle = document.getElementById('theme-toggle');

    if (themeToggle) {
        themeToggle.addEventListener('click', toggleTheme);
    }

    // Language selector
const languageSelect = document.getElementById('language-select');

if (languageSelect) {
    languageSelect.addEventListener('change', function () {
        const url = new URL(window.location.href);
        url.searchParams.set('lang', this.value);
        window.location.href = url.toString();
    });
}
});
