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

document.addEventListener('DOMContentLoaded', () => {

    // Theme toggle
    const themeToggle = document.getElementById('theme-toggle');

    if (themeToggle) {
        // Set initial accessibility state
        themeToggle.setAttribute(
            'aria-pressed',
            document.body.classList.contains('dark') ? 'true' : 'false'
        );

        themeToggle.addEventListener('click', (e) => {
            const dark = document.body.classList.toggle('dark');

            // Save theme preference in a cookie
            document.cookie =
                'theme=' + (dark ? 'dark' : 'light') +
                '; path=/bedlink; max-age=31536000; SameSite=Lax';

            // Update accessibility state
            e.currentTarget.setAttribute(
                'aria-pressed',
                dark ? 'true' : 'false'
            );
        });
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

/* =========================================
   BEDLINK API HELPER
========================================= */

async function api(endpoint, options = {}) {
    const {
        method = "GET",
        body = null,
        headers = {},
        ...fetchOptions
    } = options;

    const requestMethod = method.toUpperCase();

    const requestHeaders = {
        Accept: "application/json",
        ...headers
    };

    const requestOptions = {
        method: requestMethod,
        headers: requestHeaders,
        ...fetchOptions
    };

    // CSRF protection for state-changing requests
    if (!["GET", "HEAD", "OPTIONS"].includes(requestMethod)) {
        const CSRF =
            document.querySelector('meta[name="csrf"]')?.content || "";

        if (CSRF) {
            requestHeaders["X-CSRF-Token"] = CSRF;
        }
    }

    // Send request body
    if (body !== null) {
        if (
            typeof body === "object" &&
            !(body instanceof FormData)
        ) {
            requestHeaders["Content-Type"] = "application/json";
            requestOptions.body = JSON.stringify(body);
        } else {
            requestOptions.body = body;
        }
    }

    try {
        const response = await fetch(endpoint, requestOptions);

        let result;

        try {
            result = await response.json();
        } catch {
            result = {
                ok: false,
                error: "Invalid server response"
            };
        }

        // Preserve server response fields such as:
        // error, data, next_best, etc.
        return {
            ...result,
            ok: result?.ok === true && response.ok,
            status: response.status
        };

    } catch (error) {
        return {
            ok: false,
            status: 0,
            error: error.message || "Network error"
        };
    }
}


/* =========================================
   BEDLINK POLLING HELPER
========================================= */

function poll(endpoint, callback, interval = 15000) {
    let timer = null;
    let stopped = true;
    let running = false;

    async function execute() {
        if (stopped || running) return;

        running = true;

        try {
            const result = await api(endpoint);

            if (!stopped && result.ok) {
    await callback(result.data);
}
        } catch (error) {
            console.error("BedLink polling error:", error);
        } finally {
            running = false;

            if (!stopped) {
                timer = setTimeout(execute, interval);
            }
        }
    }

    function start() {
        if (!stopped) return;

        stopped = false;
        execute();
    }

    function stop() {
        stopped = true;
        clearTimeout(timer);
        timer = null;
    }

    start();

    return {
        start,
        stop
    };
}

/* =========================================
   BEDLINK TRANSLATION HELPERS
========================================= */

function t(key, vars = {}) {
    const text = window.L?.[key] ?? key;

    return String(text).replace(/\{(\w+)\}/g, (match, name) => {
        return vars[name] !== undefined
            ? String(vars[name])
            : match;
    });
}

function tn(key, n, vars = {}) {
    const suffix = n === 1 ? ".one" : ".other";

    return t(key + suffix, {
        ...vars,
        n
    });
}