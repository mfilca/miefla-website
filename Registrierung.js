document.getElementById("registrierungsFormular").addEventListener("submit", function(event) {
  event.preventDefault(); // Verhindert das Neuladen der Seite

  const name = document.getElementById("name").value;
  const email = document.getElementById("email").value;
  const passwort = document.getElementById("passwort").value;

  const ausgabe = `
    <h2>Registrierung erfolgreich</h2>
    <p><strong>Name:</strong> ${name}</p>
    <p><strong>E-Mail:</strong> ${email}</p>
    <p><strong>Passwort:</strong> ${"*".repeat(passwort.length)}</p>
  `;

  document.getElementById("ausgabe").innerHTML = ausgabe;

  console.log("Registrierung abgeschlossen:", { name, email, passwort });
});
