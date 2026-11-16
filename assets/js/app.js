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