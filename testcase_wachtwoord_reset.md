# Testcases — Wachtwoord Herstellen (Password Recovery)

> [!NOTE]
> Deze testcases dekken de volledige wachtwoord-reset flow: e-mail invoeren → verificatiecode ontvangen → code invoeren → nieuw wachtwoord instellen.

---

## TC-WR-001 — Succesvol herstelcode versturen

| Veld | Waarde |
|---|---|
| **Testcase ID** | TC-WR-001 |
| **Prioriteit** | Hoog |
| **Beschrijving** | Controleer of een geregistreerde gebruiker succesvol een verificatiecode ontvangt via e-mail. |
| **Teststappen** | 1. Navigeer naar `/Recover-password` <br> 2. Voer een geldig, geregistreerd e-mailadres in <br> 3. Klik op "Send Code" |
| **Verwacht Resultaat** | De gebruiker wordt doorgestuurd naar `/verify-code` met de melding "De code is verstuurd naar je e-mail." Er wordt een rij aangemaakt in de `wachtwoord_reset` tabel met `gebruikt = 0` en een `verlopen_op` van 10 minuten in de toekomst. De gebruiker ontvangt een e-mail met een 6-cijferige code. |
| **Risico** | E-mail wordt niet afgeleverd (SMTP-configuratie fout) |
| **Risico Oplossing** | Controleer `.env` mailconfiguratie (MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD). Test met Mailtrap of Mailhog in ontwikkelomgeving. |
| **Status** | ⬜ Niet getest |
| **Werkelijk Resultaat** | — |
| **Opmerkingen** | Eerdere ongebruikte codes voor dezelfde gebruiker worden automatisch verwijderd. |

---

## TC-WR-002 — Ongeldig e-mailadres invoeren

| Veld | Waarde |
|---|---|
| **Testcase ID** | TC-WR-002 |
| **Prioriteit** | Hoog |
| **Beschrijving** | Controleer of het systeem een foutmelding toont bij een niet-geregistreerd e-mailadres. |
| **Teststappen** | 1. Navigeer naar `/Recover-password` <br> 2. Voer een e-mailadres in dat NIET in de `gebruikers` tabel staat (bijv. `nietbestaand@test.nl`) <br> 3. Klik op "Send Code" |
| **Verwacht Resultaat** | De gebruiker blijft op de pagina en ziet een validatiefout bij het e-mailveld. Er wordt geen rij aangemaakt in `wachtwoord_reset` en geen e-mail verstuurd. |
| **Risico** | Foutmelding is te technisch of in het Engels i.p.v. Nederlands |
| **Risico Oplossing** | Gebruik custom validatieberichten via een Nederlands taalbestand (`resources/lang/nl/validation.php`). |
| **Status** | ⬜ Niet getest |
| **Werkelijk Resultaat** | — |
| **Opmerkingen** | — |

---

## TC-WR-003 — Leeg e-mailveld indienen

| Veld | Waarde |
|---|---|
| **Testcase ID** | TC-WR-003 |
| **Prioriteit** | Middel |
| **Beschrijving** | Controleer of het formulier validatie voorkomt dat een leeg formulier wordt ingediend. |
| **Teststappen** | 1. Navigeer naar `/Recover-password` <br> 2. Laat het e-mailveld leeg <br> 3. Klik op "Send Code" |
| **Verwacht Resultaat** | Het HTML5 `required` attribuut voorkomt indiening. Als het toch naar de server gaat, toont Laravel een "The email field is required." validatiemelding. |
| **Risico** | Laag — standaard browser- en Laravel-validatie |
| **Risico Oplossing** | N.v.t. |
| **Status** | ⬜ Niet getest |
| **Werkelijk Resultaat** | — |
| **Opmerkingen** | — |

---

## TC-WR-004 — Verificatiecode correct invoeren

| Veld | Waarde |
|---|---|
| **Testcase ID** | TC-WR-004 |
| **Prioriteit** | Hoog |
| **Beschrijving** | Controleer of een geldige 6-cijferige verificatiecode correct wordt geaccepteerd. |
| **Teststappen** | 1. Voer TC-WR-001 uit om een code te ontvangen <br> 2. Op de `/verify-code` pagina, voer de correcte 6-cijferige code in (per cijfer, per invoerveld) <br> 3. Klik op "Verify" |
| **Verwacht Resultaat** | De gebruiker wordt doorgestuurd naar `/Recover-password/new-password` met de melding "Code geverifieerd. Stel nu je nieuwe wachtwoord in." De `gebruikt` kolom in `wachtwoord_reset` wordt bijgewerkt naar `1`. |
| **Risico** | Sessie verliest het e-mailadres bij langzaam invullen |
| **Risico Oplossing** | Het e-mailadres wordt opgeslagen in de sessie bij het versturen van de code. Sessie-timeout is standaard 120 minuten in Laravel. |
| **Status** | ⬜ Niet getest |
| **Werkelijk Resultaat** | — |
| **Opmerkingen** | — |

---

## TC-WR-005 — Onjuiste verificatiecode invoeren

| Veld | Waarde |
|---|---|
| **Testcase ID** | TC-WR-005 |
| **Prioriteit** | Hoog |
| **Beschrijving** | Controleer of het systeem een foutmelding toont bij een verkeerde verificatiecode. |
| **Teststappen** | 1. Voer TC-WR-001 uit om een code te ontvangen <br> 2. Op de `/verify-code` pagina, voer een ONJUISTE 6-cijferige code in (bijv. `000000`) <br> 3. Klik op "Verify" |
| **Verwacht Resultaat** | De gebruiker blijft op `/verify-code` en ziet de foutmelding "De code is onjuist of verlopen. Probeer opnieuw." |
| **Risico** | Geen brute-force bescherming op code-invoer |
| **Risico Oplossing** | Voeg rate limiting toe via Laravel `ThrottleRequests` middleware op de POST-route (bijv. `throttle:5,1` voor max 5 pogingen per minuut). |
| **Status** | ⬜ Niet getest |
| **Werkelijk Resultaat** | — |
| **Opmerkingen** | — |

---

## TC-WR-006 — Verlopen verificatiecode gebruiken

| Veld | Waarde |
|---|---|
| **Testcase ID** | TC-WR-006 |
| **Prioriteit** | Hoog |
| **Beschrijving** | Controleer of een code die langer dan 10 minuten geleden is aangemaakt, wordt geweigerd. |
| **Teststappen** | 1. Voer TC-WR-001 uit om een code te ontvangen <br> 2. Wacht langer dan 10 minuten (of pas `verlopen_op` handmatig aan in de database naar een tijdstip in het verleden) <br> 3. Op de `/verify-code` pagina, voer de correcte code in <br> 4. Klik op "Verify" |
| **Verwacht Resultaat** | De gebruiker ziet de foutmelding "De code is onjuist of verlopen. Probeer opnieuw." De code wordt niet geaccepteerd. |
| **Risico** | Tijdzone-verschil tussen server en database |
| **Risico Oplossing** | Zorg dat de Laravel `APP_TIMEZONE` in `.env` overeenkomt met de MySQL server-tijdzone. |
| **Status** | ⬜ Niet getest |
| **Werkelijk Resultaat** | — |
| **Opmerkingen** | — |

---

## TC-WR-007 — Nieuw wachtwoord succesvol instellen

| Veld | Waarde |
|---|---|
| **Testcase ID** | TC-WR-007 |
| **Prioriteit** | Hoog |
| **Beschrijving** | Controleer of de gebruiker succesvol een nieuw wachtwoord kan instellen na verificatie. |
| **Teststappen** | 1. Voer TC-WR-001 en TC-WR-004 uit (succesvolle verificatie) <br> 2. Op de `/Recover-password/new-password` pagina, voer een nieuw wachtwoord in (min. 8 tekens) <br> 3. Voer hetzelfde wachtwoord in bij "Confirm Password" <br> 4. Klik op "Reset Password" |
| **Verwacht Resultaat** | Het wachtwoord wordt gehasht (bcrypt) en opgeslagen in de `gebruikers` tabel (kolom `wachtwoord_hash`). De gebruiker wordt doorgestuurd naar de loginpagina met de melding "Wachtwoord succesvol gewijzigd. Je kunt nu inloggen." Sessiedata wordt opgeruimd. |
| **Risico** | Gebruiker kan de new-password pagina direct benaderen zonder verificatie |
| **Risico Oplossing** | De controller controleert of `session('reset_verified')` is ingesteld. Zonder verificatie wordt de gebruiker teruggestuurd naar `/Recover-password`. |
| **Status** | ⬜ Niet getest |
| **Werkelijk Resultaat** | — |
| **Opmerkingen** | — |

---

## TC-WR-008 — Wachtwoord bevestiging komt niet overeen

| Veld | Waarde |
|---|---|
| **Testcase ID** | TC-WR-008 |
| **Prioriteit** | Middel |
| **Beschrijving** | Controleer of het systeem een foutmelding toont wanneer wachtwoord en bevestiging niet overeenkomen. |
| **Teststappen** | 1. Voer TC-WR-001 en TC-WR-004 uit (succesvolle verificatie) <br> 2. Op de `/Recover-password/new-password` pagina, voer "NieuwWachtwoord1" in bij "New Password" <br> 3. Voer "AndersWachtwoord2" in bij "Confirm Password" <br> 4. Klik op "Reset Password" |
| **Verwacht Resultaat** | De gebruiker blijft op de pagina en ziet een validatiemelding: "The password confirmation does not match." Het wachtwoord wordt niet gewijzigd. |
| **Risico** | Laag — standaard Laravel `confirmed` validatieregel |
| **Risico Oplossing** | N.v.t. |
| **Status** | ⬜ Niet getest |
| **Werkelijk Resultaat** | — |
| **Opmerkingen** | — |

---

## TC-WR-009 — Directe toegang tot new-password zonder verificatie

| Veld | Waarde |
|---|---|
| **Testcase ID** | TC-WR-009 |
| **Prioriteit** | Middel |
| **Beschrijving** | Controleer of een gebruiker niet direct een nieuw wachtwoord kan instellen zonder de verificatiecode in te voeren. |
| **Teststappen** | 1. Open een nieuwe browser (of incognito venster) <br> 2. Navigeer direct naar `/Recover-password/new-password` <br> 3. Voer een wachtwoord in en klik op "Reset Password" |
| **Verwacht Resultaat** | De gebruiker wordt doorgestuurd naar `/Recover-password` met de foutmelding "Verificatie niet voltooid. Begin opnieuw." Het wachtwoord wordt niet gewijzigd. |
| **Risico** | Beveiligingsrisico als deze check ontbreekt |
| **Risico Oplossing** | De controller controleert `session('reset_verified')` en `session('reset_email')` voordat het wachtwoord wordt gewijzigd. |
| **Status** | ⬜ Niet getest |
| **Werkelijk Resultaat** | — |
| **Opmerkingen** | — |

---

## Overzicht aangebrachte fixes

> [!IMPORTANT]
> De volgende fixes zijn toegepast om de volledige password-reset flow werkend te maken:

| # | Fix | Bestand |
|---|---|---|
| 1 | E-mailtemplate verplaatst naar `resources/views/ForgetPassword/` | `Email-template.blade.php` |
| 2 | `postVerifyCode()` volledig geïmplementeerd | `PasswordResetController.php` |
| 3 | `postNewPassword()` volledig geïmplementeerd | `PasswordResetController.php` |
| 4 | POST-route `new-password.post` geactiveerd (was uitgecommentarieerd) | `web.php` |
| 5 | View-verwijzing gecorrigeerd: `new-password` → `NewPassword` | `PasswordResetController.php` |
| 6 | Oude ongebruikte resetcodes worden verwijderd bij nieuwe aanvraag | `PasswordResetController.php` |
| 7 | Sessie-gebaseerde flow toegevoegd voor beveiliging | `PasswordResetController.php` |
