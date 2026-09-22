function togglePassword(){
  const input = document.getElementById('password');
  const eye = document.getElementById('eyeIcon');
  const isHidden = input.type === 'password';
  input.type = isHidden ? 'text' : 'password';
  eye.innerHTML = isHidden
    ? '<path d="M3 3l18 18M10.6 10.7a3.2 3.2 0 0 0 4.5 4.5M6.1 6.4C3.6 8 1.5 12 1.5 12s3.5 7 10.5 7c1.9 0 3.5-.4 4.9-1.1M17.4 17.6C20.1 15.9 22.5 12 22.5 12s-1-2-2.8-3.7"/>'
    : '<path d="M1.5 12S5 5 12 5s10.5 7 10.5 7-3.5 7-10.5 7S1.5 12 1.5 12Z"/><circle cx="12" cy="12" r="3.2"/>';
}

// --- Feltételek checkbox: csak akkor jelenik meg, ha a felhasználó
// ténylegesen elkezd gépelni, és a gomb addig letiltva marad, amíg
// be nincs pipálva. ---
document.addEventListener('DOMContentLoaded', () => {
  const usernameInput = document.getElementById('username');
  const passwordInput = document.getElementById('password');
  const termsField    = document.getElementById('termsField');
  const termsCheckbox = document.getElementById('terms');
  const submitBtn      = document.getElementById('submitBtn');
  const statusMsg      = document.getElementById('statusMsg');
  const form            = document.getElementById('loginForm');

  function revealTerms(){
    if (!termsField.classList.contains('visible')){
      termsField.classList.add('visible');
    }
  }

  function updateSubmitState(){
    submitBtn.disabled = !termsCheckbox.checked;
  }

  usernameInput.addEventListener('input', revealTerms);
  passwordInput.addEventListener('input', revealTerms);
  termsCheckbox.addEventListener('change', updateSubmitState);

  function showStatus(msg, type){
    statusMsg.textContent = msg;
    statusMsg.className = 'status-msg visible ' + type;
  }
  function hideStatus(){
    statusMsg.className = 'status-msg';
  }

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    hideStatus();

    if (!termsCheckbox.checked){
      showStatus('A Feltételek elfogadása kötelező.', 'error');
      return;
    }

    submitBtn.disabled = true;
    submitBtn.textContent = 'BEJELENTKEZÉS…';

    try {
      const res = await fetch('auth-api.php?action=login', {
        method: 'POST',
        credentials: 'include',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          felhasznalonev: usernameInput.value.trim(),
          jelszo: passwordInput.value,
          emlekezz: document.querySelector('input[name="remember"]').checked
        })
      });
      const data = await res.json();

      if (!data.ok){
        showStatus(data.error || 'Sikertelen bejelentkezés.', 'error');
        submitBtn.disabled = false;
      } else {
        showStatus('Sikeres bejelentkezés! Átirányítás…', 'success');
        // ide jöhet pl.: window.location.href = 'menu.html';
      }
    } catch (err){
      showStatus('Nem sikerült elérni a szervert.', 'error');
      submitBtn.disabled = false;
    } finally {
      submitBtn.textContent = 'BEJELENTKEZÉS';
    }
  });
});

