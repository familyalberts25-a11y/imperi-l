// A service.php ugyanabban a mappában van, mint ez a fájl.
var SERVICE_URL = "service.php";

var form       = document.getElementById("loginForm");
var terms      = document.getElementById("terms");
var termsField = document.getElementById("termsField");
var submitBtn  = document.getElementById("submitBtn");
var statusMsg  = document.getElementById("statusMsg");

// A feltételek mező megjelenítése (a CSS alapból összecsukva tartja).
window.addEventListener("DOMContentLoaded", function () {
  termsField.classList.add("visible");
});

// A gomb csak akkor engedélyezett, ha a feltételeket elfogadták.
terms.addEventListener("change", function () {
  submitBtn.disabled = !terms.checked;
});

// Jelszó láthatóságának váltása.
function togglePassword() {
  var input = document.getElementById("password");
  var eyeIcon = document.getElementById("eyeIcon");

  if (input.type === "password") {
    input.type = "text";
    eyeIcon.style.opacity = "0.6";
  } else {
    input.type = "password";
    eyeIcon.style.opacity = "1";
  }
}

function mutatUzenet(szoveg, tipus) {
  statusMsg.textContent = szoveg;
  statusMsg.className = "status-msg visible " + tipus;
}

function elrejtUzenet() {
  statusMsg.className = "status-msg";
  statusMsg.textContent = "";
}

form.addEventListener("submit", function (e) {
  e.preventDefault();
  elrejtUzenet();

  var username = document.getElementById("username").value.trim();
  var password = document.getElementById("password").value;

  if (username === "" || password === "") {
    mutatUzenet("Kérjük, töltse ki az összes mezőt!", "error");
    return;
  }

  if (!terms.checked) {
    mutatUzenet("Kérjük, fogadja el a Feltételeket!", "error");
    return;
  }

  submitBtn.disabled = true;
  submitBtn.textContent = "BEJELENTKEZÉS…";

  fetch(SERVICE_URL, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      action: "login",
      username: username,
      password: password
    })
  })
    .then(function (valasz) { return valasz.json(); })
    .then(function (adat) {
      submitBtn.disabled = false;
      submitBtn.textContent = "BEJELENTKEZÉS";

      if (adat.ok) {
        mutatUzenet("Sikeres bejelentkezés! Üdv, " + adat.name + "!", "success");
        // Ide jöhet pl. átirányítás:
        // window.location.href = "fooldal.html?name=" + encodeURIComponent(adat.name);
      } else {
        mutatUzenet(adat.message || "Hibás felhasználónév vagy jelszó.", "error");
      }
    })
    .catch(function (hiba) {
      submitBtn.disabled = false;
      submitBtn.textContent = "BEJELENTKEZÉS";
      mutatUzenet("Nem sikerült kapcsolódni a szerverhez.", "error");
      console.error(hiba);
    });
});
