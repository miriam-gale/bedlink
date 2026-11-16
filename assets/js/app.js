// assets/js/app.js (Mimi) — the toolbox every page loads
function toast(message, type = 'ok') {
  const el = document.createElement('div');
  el.className = 'toast toast--' + type;
  el.textContent = message;
  document.body.append(el);
  setTimeout(() => el.remove(), 4000);
}