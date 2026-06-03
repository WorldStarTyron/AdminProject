# Testplan — Rolgebaseerde Toegangscontrole (RBAC)
**Datum**: Juni 2026  
**Feature**: Rolgebaseerde Toegangscontrole (RBAC) voor alle pagina's en functionaliteiten  
**Status**: Klaar voor testen

> [!NOTE]
> Dit testplan verifieert dat elke gebruikersrol (Lid, Administratie Medewerker, Voorzitter, Applicatie Beheerder) uitsluitend toegang heeft tot de functionaliteiten die zijn vastgelegd in het RBAC-ontwerp. De tests dekken: paginatoegang, sidebar-zichtbaarheid, actieknoppen en beveiligingsrandgevallen.

---

## Rollen overzicht

| Rol ID | Rolnaam                    | Toegang samenvatting |
|--------|----------------------------|----------------------|
| 1      | Administratie Medewerker   | Dashboard, leden (lezen + schrijven), betalingen (lezen + schrijven), rapport, activiteitlog |
| 2      | Lid                        | Alleen eigen profiel (`/Lidpagina`) |
| 3      | Voorzitter                 | Dashboard, leden (alleen lezen), betalingen (alleen lezen), rapport, activiteitlog |
| 4      | Applicatie Beheerder       | Alles: dashboard, leden (CRUD + verwijderen), betalingen (CRUD), rapport, log, rollenbeheer, gebruikersbeheer |

---

## Voorbereiding

### Testgegevens aanmaken
Maak **4 testgebruikers** aan, elk met één van de rollen:

| Testgebruiker | E-mail                    | Wachtwoord   | Rol                        |
|---------------|---------------------------|--------------|----------------------------|
| Test Lid      | `lid@test.nl`             | `Test1234!`  | Lid (rol_id = 2)           |
| Test Admin    | `admin@test.nl`           | `Test1234!`  | Administratie Medewerker (rol_id = 1) |
| Test Voorzitter | `voorzitter@test.nl`    | `Test1234!`  | Voorzitter (rol_id = 3)    |
| Test Beheerder | `beheerder@test.nl`      | `Test1234!`  | Applicatie Beheerder (rol_id = 4) |

### SQL voor testgegevens
```sql
-- Controleer bestaande rollen
SELECT * FROM rollen;

-- Controleer of testgebruikers bestaan
SELECT g.id, g.naam, g.email, r.rol_naam 
FROM gebruikers g 
LEFT JOIN gebruikers_rollen gr ON g.id = gr.gebruiker_id 
LEFT JOIN rollen r ON gr.rol_id = r.id 
WHERE g.email IN ('lid@test.nl', 'admin@test.nl', 'voorzitter@test.nl', 'beheerder@test.nl');
```

---

## 1. PAGINATOEGANG PER ROL

### TC-RBAC-001 — Lid: Toegang tot eigen profiel

| Veld | Waarde |
|---|---|
| **Testcase ID** | TC-RBAC-001 |
| **Prioriteit** | Hoog |
| **Beschrijving** | Controleer of een gebruiker met de rol "Lid" na het inloggen doorgestuurd wordt naar `/Lidpagina` en daar zijn eigen profielgegevens ziet. |
| **Teststappen** | 1. Navigeer naar `/login` <br> 2. Log in als `lid@test.nl` / `Test1234!` <br> 3. Observeer de redirect-locatie |
| **Verwacht Resultaat** | De gebruiker wordt automatisch doorgestuurd naar `/Lidpagina` (route `GegevensPagina`). De pagina toont de profielgegevens van het ingelogde lid. |
| **Risico** | Redirect-logica in `AuthController@redirectBasedOnRole` klopt niet |
| **Risico Oplossing** | Controleer de `redirectBasedOnRole()` methode in `AuthController.php` |
| **Status** | ⬜ Niet getest |
| **Werkelijk Resultaat** | — |
| **Opmerkingen** | — |

---

### TC-RBAC-002 — Lid: Geen toegang tot dashboard

| Veld | Waarde |
|---|---|
| **Testcase ID** | TC-RBAC-002 |
| **Prioriteit** | Hoog |
| **Beschrijving** | Controleer of een gebruiker met de rol "Lid" GEEN toegang heeft tot het dashboard. |
| **Teststappen** | 1. Log in als `lid@test.nl` <br> 2. Navigeer handmatig naar `/MainDashboardPagina` |
| **Verwacht Resultaat** | De gebruiker ontvangt een **403 Forbidden** foutmelding of wordt teruggestuurd. Het dashboard wordt NIET getoond. |
| **Risico** | Gate `dashboard` mist de Lid-restrictie |
| **Risico Oplossing** | Controleer `AuthServiceProvider.php` → gate `dashboard` bevat NIET de rol "Lid" |
| **Status** | ⬜ Niet getest |
| **Werkelijk Resultaat** | — |
| **Opmerkingen** | — |

---

### TC-RBAC-003 — Lid: Geen toegang tot ledenpagina

| Veld | Waarde |
|---|---|
| **Testcase ID** | TC-RBAC-003 |
| **Prioriteit** | Hoog |
| **Beschrijving** | Controleer of een gebruiker met de rol "Lid" GEEN toegang heeft tot de ledenlijst. |
| **Teststappen** | 1. Log in als `lid@test.nl` <br> 2. Navigeer handmatig naar `/ledenpagina` |
| **Verwacht Resultaat** | De gebruiker ontvangt een **403 Forbidden** foutmelding. De ledenlijst wordt NIET getoond. |
| **Risico** | Gate `leden-bekijken` laat Lid-rol door |
| **Risico Oplossing** | Controleer dat gate `leden-bekijken` NIET "Lid" bevat |
| **Status** | ⬜ Niet getest |
| **Werkelijk Resultaat** | — |
| **Opmerkingen** | — |

---

### TC-RBAC-004 — Lid: Geen toegang tot betalingen

| Veld | Waarde |
|---|---|
| **Testcase ID** | TC-RBAC-004 |
| **Prioriteit** | Hoog |
| **Beschrijving** | Controleer of een gebruiker met de rol "Lid" GEEN toegang heeft tot de betalingspagina. |
| **Teststappen** | 1. Log in als `lid@test.nl` <br> 2. Navigeer handmatig naar `/betalingPagina` |
| **Verwacht Resultaat** | De gebruiker ontvangt een **403 Forbidden** foutmelding. |
| **Risico** | — |
| **Risico Oplossing** | — |
| **Status** | ⬜ Niet getest |
| **Werkelijk Resultaat** | — |
| **Opmerkingen** | Test ook `/RapportPagina`, `/ActiviteitLogPagina`, `/rollen-beheer`, `/GebruikersBeheerPagina` – alle moeten 403 geven voor een Lid. |

---

### TC-RBAC-005 — Administratie Medewerker: Dashboard toegang

| Veld | Waarde |
|---|---|
| **Testcase ID** | TC-RBAC-005 |
| **Prioriteit** | Hoog |
| **Beschrijving** | Controleer of een Administratie Medewerker toegang heeft tot het dashboard. |
| **Teststappen** | 1. Log in als `admin@test.nl` <br> 2. Observeer de redirect na login |
| **Verwacht Resultaat** | De gebruiker wordt doorgestuurd naar `/MainDashboardPagina`. Het dashboard wordt correct weergegeven. |
| **Risico** | — |
| **Risico Oplossing** | — |
| **Status** | ⬜ Niet getest |
| **Werkelijk Resultaat** | — |
| **Opmerkingen** | — |

---

### TC-RBAC-006 — Administratie Medewerker: Leden lezen + schrijven

| Veld | Waarde |
|---|---|
| **Testcase ID** | TC-RBAC-006 |
| **Prioriteit** | Hoog |
| **Beschrijving** | Controleer of een Administratie Medewerker de ledenlijst kan bekijken én leden kan toevoegen/bewerken. |
| **Teststappen** | 1. Log in als `admin@test.nl` <br> 2. Navigeer naar `/ledenpagina` <br> 3. Verifieer dat de ledenlijst zichtbaar is <br> 4. Controleer of de knop "Lid toevoegen" zichtbaar is <br> 5. Klik op een lid om de detailpagina te openen <br> 6. Controleer of de "Bewerken" knop zichtbaar is |
| **Verwacht Resultaat** | Ledenlijst wordt getoond. De knoppen "Lid toevoegen" en "Bewerken" zijn zichtbaar en functioneel. |
| **Risico** | `@can('leden-beheren')` directive ontbreekt op knoppen |
| **Risico Oplossing** | Controleer blade-views op correcte `@can` directives |
| **Status** | ⬜ Niet getest |
| **Werkelijk Resultaat** | — |
| **Opmerkingen** | — |

---

### TC-RBAC-007 — Administratie Medewerker: Leden NIET verwijderen

| Veld | Waarde |
|---|---|
| **Testcase ID** | TC-RBAC-007 |
| **Prioriteit** | Hoog |
| **Beschrijving** | Controleer of een Administratie Medewerker GEEN leden kan verwijderen. |
| **Teststappen** | 1. Log in als `admin@test.nl` <br> 2. Navigeer naar `/ledenpagina` <br> 3. Controleer of de "Verwijderen" knop NIET zichtbaar is <br> 4. Probeer handmatig via DevTools een DELETE request naar `/ledenpagina/delete/{lidId}` te sturen |
| **Verwacht Resultaat** | De "Verwijderen" knop is niet zichtbaar in de UI. Het handmatige DELETE-verzoek retourneert **403 Forbidden**. |
| **Risico** | Gate `leden-verwijderen` is te breed gedefinieerd |
| **Risico Oplossing** | Controleer dat gate `leden-verwijderen` alleen `isApplicatieBeheerder()` toestaat |
| **Status** | ⬜ Niet getest |
| **Werkelijk Resultaat** | — |
| **Opmerkingen** | — |

---

### TC-RBAC-008 — Administratie Medewerker: Geen rollenbeheer

| Veld | Waarde |
|---|---|
| **Testcase ID** | TC-RBAC-008 |
| **Prioriteit** | Hoog |
| **Beschrijving** | Controleer of een Administratie Medewerker GEEN toegang heeft tot rollenbeheer en gebruikersbeheer. |
| **Teststappen** | 1. Log in als `admin@test.nl` <br> 2. Navigeer handmatig naar `/rollen-beheer` <br> 3. Navigeer handmatig naar `/GebruikersBeheerPagina` |
| **Verwacht Resultaat** | Beide pagina's retourneren **403 Forbidden**. De sidebar toont GEEN links naar rollenbeheer en gebruikersbeheer. |
| **Risico** | Sidebar toont links zonder `@can` controle |
| **Risico Oplossing** | Controleer `sidebar.blade.php` op `@can('rollenbeheer')` en `@can('gebruikersbeheer')` directives |
| **Status** | ⬜ Niet getest |
| **Werkelijk Resultaat** | — |
| **Opmerkingen** | — |

---

### TC-RBAC-009 — Voorzitter: Alleen lezen, niet schrijven

| Veld | Waarde |
|---|---|
| **Testcase ID** | TC-RBAC-009 |
| **Prioriteit** | Hoog |
| **Beschrijving** | Controleer of de Voorzitter leden en betalingen kan BEKIJKEN maar NIET kan toevoegen, bewerken of verwijderen. |
| **Teststappen** | 1. Log in als `voorzitter@test.nl` <br> 2. Navigeer naar `/ledenpagina` — verifieer dat de lijst zichtbaar is <br> 3. Controleer of de knop "Lid toevoegen" NIET zichtbaar is <br> 4. Navigeer naar `/betalingPagina` — verifieer dat de lijst zichtbaar is <br> 5. Controleer of de knop "Betaling toevoegen" NIET zichtbaar is <br> 6. Probeer handmatig een POST request naar `/ledenpagina/addlid` te sturen via DevTools |
| **Verwacht Resultaat** | De ledenlijst en betalingslijst worden getoond (alleen lezen). De toevoeg-/bewerk-/verwijderknoppen zijn NIET zichtbaar. Het handmatige POST-verzoek retourneert **403 Forbidden**. |
| **Risico** | `@can('leden-beheren')` directive ontbreekt op schrijfknoppen |
| **Risico Oplossing** | Controleer alle blade-views op `@can` directives rond schrijf-acties |
| **Status** | ⬜ Niet getest |
| **Werkelijk Resultaat** | — |
| **Opmerkingen** | Test ook rapport en activiteitlog — Voorzitter heeft wel leestoegang. |

---

### TC-RBAC-010 — Voorzitter: Geen rollenbeheer of gebruikersbeheer

| Veld | Waarde |
|---|---|
| **Testcase ID** | TC-RBAC-010 |
| **Prioriteit** | Hoog |
| **Beschrijving** | Controleer of de Voorzitter GEEN toegang heeft tot rollenbeheer en gebruikersbeheer. |
| **Teststappen** | 1. Log in als `voorzitter@test.nl` <br> 2. Navigeer handmatig naar `/rollen-beheer` <br> 3. Navigeer handmatig naar `/GebruikersBeheerPagina` |
| **Verwacht Resultaat** | Beide pagina's retourneren **403 Forbidden**. |
| **Risico** | — |
| **Risico Oplossing** | — |
| **Status** | ⬜ Niet getest |
| **Werkelijk Resultaat** | — |
| **Opmerkingen** | — |

---

### TC-RBAC-011 — Applicatie Beheerder: Volledige toegang

| Veld | Waarde |
|---|---|
| **Testcase ID** | TC-RBAC-011 |
| **Prioriteit** | Hoog |
| **Beschrijving** | Controleer of de Applicatie Beheerder toegang heeft tot ALLE pagina's en functies, inclusief rollenbeheer en gebruikersbeheer. |
| **Teststappen** | 1. Log in als `beheerder@test.nl` <br> 2. Navigeer naar elk van de volgende pagina's en verifieer toegang: <br> - `/MainDashboardPagina` <br> - `/ledenpagina` <br> - `/betalingPagina` <br> - `/RapportPagina` <br> - `/ActiviteitLogPagina` <br> - `/rollen-beheer` <br> - `/GebruikersBeheerPagina` <br> 3. Controleer of alle CRUD-knoppen zichtbaar zijn (toevoegen, bewerken, verwijderen) |
| **Verwacht Resultaat** | Alle pagina's worden succesvol geladen. Alle knoppen (toevoegen, bewerken, verwijderen) zijn zichtbaar en functioneel. Rollenbeheer en gebruikersbeheer zijn toegankelijk. |
| **Risico** | Rol "Applicatie beheerder" in de DB heeft een kleine 'b', terwijl de code "Applicatie Beheerder" verwacht |
| **Risico Oplossing** | Controleer dat `Gebruiker.php` → `isApplicatieBeheerder()` en `hasAnyRole()` de rolnamen case-insensitive vergelijken via `strtolower()`. |
| **Status** | ⬜ Niet getest |
| **Werkelijk Resultaat** | — |
| **Opmerkingen** | Let op: de DB heeft "Applicatie beheerder" (kleine 'b'). Dit is gefixt in het Gebruiker-model met normalisatie. |

---

## 2. SIDEBAR ZICHTBAARHEID PER ROL

### TC-RBAC-012 — Sidebar: Lid ziet alleen profiel

| Veld | Waarde |
|---|---|
| **Testcase ID** | TC-RBAC-012 |
| **Prioriteit** | Middel |
| **Beschrijving** | Controleer of de sidebar voor een Lid alleen de link naar het eigen profiel toont. |
| **Teststappen** | 1. Log in als `lid@test.nl` <br> 2. Inspecteer de sidebar in de navigatie |
| **Verwacht Resultaat** | De sidebar toont ALLEEN: <br> - Profiel / Mijn gegevens <br> - Uitloggen <br> NIET zichtbaar: Dashboard, Leden, Betalingen, Rapport, Activiteitlog, Rollenbeheer, Gebruikersbeheer |
| **Risico** | — |
| **Risico Oplossing** | — |
| **Status** | ⬜ Niet getest |
| **Werkelijk Resultaat** | — |
| **Opmerkingen** | — |

---

### TC-RBAC-013 — Sidebar: Voorzitter ziet lees-menu's

| Veld | Waarde |
|---|---|
| **Testcase ID** | TC-RBAC-013 |
| **Prioriteit** | Middel |
| **Beschrijving** | Controleer of de sidebar voor de Voorzitter de juiste menu-items toont. |
| **Teststappen** | 1. Log in als `voorzitter@test.nl` <br> 2. Inspecteer de sidebar in de navigatie |
| **Verwacht Resultaat** | De sidebar toont: Dashboard, Leden, Betalingen, Rapport, Activiteitlog, Uitloggen. <br> NIET zichtbaar: Rollenbeheer, Gebruikersbeheer |
| **Risico** | — |
| **Risico Oplossing** | — |
| **Status** | ⬜ Niet getest |
| **Werkelijk Resultaat** | — |
| **Opmerkingen** | — |

---

### TC-RBAC-014 — Sidebar: Applicatie Beheerder ziet alles

| Veld | Waarde |
|---|---|
| **Testcase ID** | TC-RBAC-014 |
| **Prioriteit** | Middel |
| **Beschrijving** | Controleer of de sidebar voor de Applicatie Beheerder ALLE menu-items toont, inclusief rollenbeheer en gebruikersbeheer. |
| **Teststappen** | 1. Log in als `beheerder@test.nl` <br> 2. Inspecteer de sidebar in de navigatie |
| **Verwacht Resultaat** | De sidebar toont: Dashboard, Leden, Betalingen, Rapport, Activiteitlog, Rollenbeheer, Gebruikersbeheer, Uitloggen |
| **Risico** | — |
| **Risico Oplossing** | — |
| **Status** | ⬜ Niet getest |
| **Werkelijk Resultaat** | — |
| **Opmerkingen** | — |

---

## 3. BEVEILIGINGSTESTS (Directe URL / API manipulatie)

### TC-RBAC-015 — Directe URL-toegang zonder login

| Veld | Waarde |
|---|---|
| **Testcase ID** | TC-RBAC-015 |
| **Prioriteit** | Hoog |
| **Beschrijving** | Controleer of een niet-ingelogde bezoeker geen toegang heeft tot beschermde pagina's. |
| **Teststappen** | 1. Open een incognito/privé-venster (niet ingelogd) <br> 2. Navigeer direct naar elk van de volgende URL's: <br> - `/MainDashboardPagina` <br> - `/ledenpagina` <br> - `/betalingPagina` <br> - `/RapportPagina` <br> - `/ActiviteitLogPagina` <br> - `/rollen-beheer` <br> - `/GebruikersBeheerPagina` <br> - `/Lidpagina` |
| **Verwacht Resultaat** | De gebruiker wordt voor ELKE pagina doorgestuurd naar `/login`. |
| **Risico** | Route-middleware `auth` ontbreekt op sommige routes |
| **Risico Oplossing** | Controleer dat alle routes binnen een `middleware(['auth'])` groep staan in `web.php`. |
| **Status** | ⬜ Niet getest |
| **Werkelijk Resultaat** | — |
| **Opmerkingen** | — |

---

### TC-RBAC-016 — POST/PUT/DELETE zonder juiste rol (API-manipulatie)

| Veld | Waarde |
|---|---|
| **Testcase ID** | TC-RBAC-016 |
| **Prioriteit** | Hoog |
| **Beschrijving** | Controleer of backend-routes beschermd zijn tegen ongeautoriseerde verzoeken, zelfs als een gebruiker de UI-controles omzeilt. |
| **Teststappen** | 1. Log in als `voorzitter@test.nl` <br> 2. Open DevTools → Console <br> 3. Stuur de volgende fetch-verzoeken: <br> **3a.** POST naar `/ledenpagina/addlid` (Voorzitter mag niet schrijven) <br> **3b.** PUT naar `/ledenpagina/{lidId}` (Voorzitter mag niet bewerken) <br> **3c.** DELETE naar `/ledenpagina/delete/{lidId}` (Voorzitter mag niet verwijderen) <br> **3d.** POST naar `/betalingPagina/addBetaling` (Voorzitter mag niet schrijven) <br> 4. Gebruik de volgende JavaScript code: <br> ```javascript fetch('/ledenpagina/addlid', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json' }, body: JSON.stringify({name: 'Test', email: 'hack@test.nl'}) }) .then(r => console.log(r.status)) ``` |
| **Verwacht Resultaat** | Alle verzoeken retourneren **403 Forbidden**. Er worden geen records aangemaakt of gewijzigd in de database. |
| **Risico** | Controller mist `Gate::authorize()` aanroep |
| **Risico Oplossing** | Controleer elke controller-methode op een `Gate::authorize()` aanroep naast de route-middleware. |
| **Status** | ⬜ Niet getest |
| **Werkelijk Resultaat** | — |
| **Opmerkingen** | Dit is een belangrijk beveiligingstest. UI-bescherming via `@can` is niet voldoende; backend moet ook blokkeren. |

---

### TC-RBAC-017 — Lid probeert rollenbeheer via API

| Veld | Waarde |
|---|---|
| **Testcase ID** | TC-RBAC-017 |
| **Prioriteit** | Hoog |
| **Beschrijving** | Controleer of een Lid niet via directe API-aanroepen rollen kan toewijzen. |
| **Teststappen** | 1. Log in als `lid@test.nl` <br> 2. Open DevTools → Console <br> 3. Stuur een POST naar `/rollen-beheer/assign` met een body met een rol_id en gebruiker_id |
| **Verwacht Resultaat** | Het verzoek retourneert **403 Forbidden**. Geen rollen worden gewijzigd. |
| **Risico** | — |
| **Risico Oplossing** | — |
| **Status** | ⬜ Niet getest |
| **Werkelijk Resultaat** | — |
| **Opmerkingen** | — |

---

## 4. RANDGEVALLEN

### TC-RBAC-018 — Case-sensitivity rolnamen

| Veld | Waarde |
|---|---|
| **Testcase ID** | TC-RBAC-018 |
| **Prioriteit** | Middel |
| **Beschrijving** | Controleer dat de RBAC correct werkt ondanks verschil in hoofdletters/kleine letters in rolnamen (bijv. "Applicatie beheerder" vs "Applicatie Beheerder" in de database). |
| **Teststappen** | 1. Query de database: `SELECT * FROM rollen;` <br> 2. Noteer de exacte schrijfwijze van elke rol <br> 3. Log in met de Applicatie Beheerder <br> 4. Probeer alle pagina's te openen |
| **Verwacht Resultaat** | Ondanks eventuele case-verschillen in de DB worden alle rollen correct herkend. De `strtolower()` normalisatie in `Gebruiker.php` → `hasAnyRole()` vangt dit op. |
| **Risico** | Nieuwe rollen worden met afwijkende schrijfwijze toegevoegd |
| **Risico Oplossing** | Gebruik altijd de `hasAnyRole()` methode die case-insensitive vergelijkt |
| **Status** | ⬜ Niet getest |
| **Werkelijk Resultaat** | — |
| **Opmerkingen** | Bekende situatie: rol_id 4 staat als "Applicatie beheerder" (kleine 'b') in de DB. |

---

### TC-RBAC-019 — Gebruiker zonder rol

| Veld | Waarde |
|---|---|
| **Testcase ID** | TC-RBAC-019 |
| **Prioriteit** | Middel |
| **Beschrijving** | Controleer wat er gebeurt als een gebruiker inlogt die GEEN rol heeft toegewezen. |
| **Teststappen** | 1. Maak een testgebruiker aan zonder rij in `gebruikers_rollen` <br> 2. Log in met deze gebruiker |
| **Verwacht Resultaat** | De gebruiker wordt niet doorgestuurd naar een 500-foutpagina. De applicatie handelt dit gracieus af (bijv. redirect naar login met foutmelding, of naar een "geen toegang" pagina). |
| **Risico** | `NullPointerException` / `undefined` fout als `rollen()` leeg is |
| **Risico Oplossing** | Voeg een fallback toe in `redirectBasedOnRole()` voor gebruikers zonder rol |
| **Status** | ⬜ Niet getest |
| **Werkelijk Resultaat** | — |
| **Opmerkingen** | — |

---

### TC-RBAC-020 — Sessie-timeout en herauthenticatie

| Veld | Waarde |
|---|---|
| **Testcase ID** | TC-RBAC-020 |
| **Prioriteit** | Laag |
| **Beschrijving** | Controleer of een verlopen sessie de RBAC-controles niet omzeilt. |
| **Teststappen** | 1. Log in als `admin@test.nl` <br> 2. Wacht tot de sessie verloopt (of verwijder het sessie-cookie handmatig) <br> 3. Probeer een beschermde pagina te openen |
| **Verwacht Resultaat** | De gebruiker wordt doorgestuurd naar `/login`. |
| **Risico** | — |
| **Risico Oplossing** | — |
| **Status** | ⬜ Niet getest |
| **Werkelijk Resultaat** | — |
| **Opmerkingen** | Laravel `auth` middleware handelt dit standaard af. |

---

## 5. TOEGANGSMATRIX (Snelle referentie)

| Pagina / Actie | Lid | Admin. Medew. | Voorzitter | App. Beheerder |
|---|:---:|:---:|:---:|:---:|
| **Login** | ✅ | ✅ | ✅ | ✅ |
| **Eigen profiel** (`/Lidpagina`) | ✅ | ❌ | ❌ | ❌ |
| **Dashboard** (`/MainDashboardPagina`) | ❌ | ✅ | ✅ | ✅ |
| **Leden bekijken** (`/ledenpagina`) | ❌ | ✅ | ✅ | ✅ |
| **Leden toevoegen/bewerken** | ❌ | ✅ | ❌ | ✅ |
| **Leden verwijderen** | ❌ | ❌ | ❌ | ✅ |
| **Betalingen bekijken** (`/betalingPagina`) | ❌ | ✅ | ✅ | ✅ |
| **Betalingen toevoegen/bewerken/verwijderen** | ❌ | ✅ | ❌ | ✅ |
| **Rapport** (`/RapportPagina`) | ❌ | ✅ | ✅ | ✅ |
| **Activiteitlog** (`/ActiviteitLogPagina`) | ❌ | ✅ | ✅ | ✅ |
| **Rollenbeheer** (`/rollen-beheer`) | ❌ | ❌ | ❌ | ✅ |
| **Gebruikersbeheer** (`/GebruikersBeheerPagina`) | ❌ | ❌ | ❌ | ✅ |

---

## 6. TESTUITVOERING SAMENVATTING

| Testcase | Status | Opmerkingen | Pass / Fail |
|---|---|---|---|
| TC-RBAC-001: Lid → eigen profiel | ⬜ | | ⬜ Pass ⬜ Fail |
| TC-RBAC-002: Lid → geen dashboard | ⬜ | | ⬜ Pass ⬜ Fail |
| TC-RBAC-003: Lid → geen leden | ⬜ | | ⬜ Pass ⬜ Fail |
| TC-RBAC-004: Lid → geen betalingen | ⬜ | | ⬜ Pass ⬜ Fail |
| TC-RBAC-005: Admin → dashboard | ⬜ | | ⬜ Pass ⬜ Fail |
| TC-RBAC-006: Admin → leden CRUD | ⬜ | | ⬜ Pass ⬜ Fail |
| TC-RBAC-007: Admin → niet verwijderen | ⬜ | | ⬜ Pass ⬜ Fail |
| TC-RBAC-008: Admin → geen rollenbeheer | ⬜ | | ⬜ Pass ⬜ Fail |
| TC-RBAC-009: Voorzitter → alleen lezen | ⬜ | | ⬜ Pass ⬜ Fail |
| TC-RBAC-010: Voorzitter → geen beheer | ⬜ | | ⬜ Pass ⬜ Fail |
| TC-RBAC-011: Beheerder → volledig | ⬜ | | ⬜ Pass ⬜ Fail |
| TC-RBAC-012: Sidebar Lid | ⬜ | | ⬜ Pass ⬜ Fail |
| TC-RBAC-013: Sidebar Voorzitter | ⬜ | | ⬜ Pass ⬜ Fail |
| TC-RBAC-014: Sidebar Beheerder | ⬜ | | ⬜ Pass ⬜ Fail |
| TC-RBAC-015: Directe URL zonder login | ⬜ | | ⬜ Pass ⬜ Fail |
| TC-RBAC-016: API manipulatie | ⬜ | | ⬜ Pass ⬜ Fail |
| TC-RBAC-017: Lid → API rollenbeheer | ⬜ | | ⬜ Pass ⬜ Fail |
| TC-RBAC-018: Case-sensitivity | ⬜ | | ⬜ Pass ⬜ Fail |
| TC-RBAC-019: Gebruiker zonder rol | ⬜ | | ⬜ Pass ⬜ Fail |
| TC-RBAC-020: Sessie-timeout | ⬜ | | ⬜ Pass ⬜ Fail |

---

## 7. ONDERTEKENING

**Getest door**: _________________  
**Datum**: _________________  
**Totaalresultaat**: ⬜ PASS ⬜ FAIL  
**Gevonden issues**: _________________  
**Aanbeveling**: ⬜ Klaar voor productie ⬜ Fixes nodig ⬜ Geblokkeerd
