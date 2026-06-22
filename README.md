# Khilafat Anjuman Administratiesysteem

Een webapplicatie voor de ledenadministratie en betalingsregistratie van Khilafat Anjuman (Regeffio Baarn). Met deze applicatie beheert de organisatie haar leden, houdt zij de maandelijkse contributies bij en regelt zij wie wat mag zien en doen op basis van zijn rol.

De applicatie is gebouwd tijdens een BPV-stage bij Bit Dynamics N.V. in Baarn.

## Over de applicatie

Khilafat Anjuman hield de ledenadministratie en betalingen eerst handmatig bij. Dat kostte veel tijd en er ontstonden snel fouten. Deze applicatie brengt alles op één plek samen:

- Het beheren van leden (toevoegen, bekijken, bewerken, activeren en deactiveren).
- Het registreren van de maandelijkse contributie van **SRD 150** per lid.
- Het bijhouden welke betalingen open staan en welke betaald zijn.
- Het automatisch deactiveren van leden die 3 maanden achter elkaar niet betaald hebben.
- Het maken van bonnen (kwitanties) voor betalingen.
- Een dashboard met grafieken zodat het bestuur in één oogopslag de cijfers ziet.

## Functionaliteiten

- **Inloggen met rollen** – Elke gebruiker logt in en komt op de juiste startpagina, afhankelijk van zijn rol.
- **Ledenbeheer** – Leden toevoegen, zoeken, filteren (op maand en jaar) en de gegevens bewerken.
- **Betalingen registreren** – Betalingen vastleggen en koppelen aan een lid, met de mogelijkheid om een betalingsbewijs (PDF) te uploaden.
- **Verwijderen en herstellen** – Verwijderde betalingen worden niet meteen weggegooid (soft delete) en kunnen worden hersteld.
- **Bonnen genereren** – Per betaling een bon met een uniek nummer in het formaat `BON-KA[jaar]-[volgnummer]`.
- **Notificaties** – Een belletje in de navigatiebalk laat zien hoeveel ongelezen meldingen er zijn.
- **Wachtwoord vergeten** – Opnieuw instellen met een code van 6 cijfers die na 10 minuten verloopt.
- **Dashboard** – Overzicht van leden en betalingen met grafieken.
- **Automatische controles** – Dagelijkse taken die openstaande betalingen controleren en leden deactiveren na 3 gemiste maanden.

## Rollen

De applicatie kent vier rollen. Elke rol heeft zijn eigen rechten, geregeld met Laravel Gates.

| Rol | Wat de rol mag |
|-----|----------------|
| **Lid** | Eigen gegevens en betalingen bekijken. |
| **Administratie Medewerker** | Leden en betalingen beheren. |
| **Voorzitter** | Overzicht en rapportages bekijken. |
| **Applicatie Beheerder** | Volledig beheer, inclusief gebruikers en rollen. |

## Gebruikte technieken

- **Laravel 10** – het PHP-framework voor de back-end.
- **MySQL** – de database.
- **Blade** – voor de pagina's (templates).
- **Tailwind CSS** – voor de opmaak.
- **Alpine.js** – voor kleine stukjes interactie (dropdowns, het notificatie-belletje).
- **Chart.js** – voor de grafieken op het dashboard.
- **Font Awesome** – voor de iconen.

## Databasestructuur

De database bestaat uit negen tabellen:

| Tabel | Inhoud |
|-------|--------|
| `gebruikers` | De inloggegevens van gebruikers. |
| `leden` | De gegevens van de leden. |
| `rollen` | De beschikbare rollen. |
| `gebruikers_rollen` | Koppeltabel tussen gebruikers en rollen (veel-op-veel). |
| `betalingen` | De geregistreerde betalingen. |
| `bonnen` | De gegenereerde bonnen. |
| `activiteit` | Het bijhouden van acties in het systeem. |
| `notificatie` | De meldingen voor gebruikers. |
| `wachtwoord_reset` | De codes voor het opnieuw instellen van een wachtwoord. |

De primaire sleutels zijn van het type `INT AUTO_INCREMENT`.

## Lokaal opzetten

Hieronder de stappen om het project op je eigen computer te draaien.

**1. Project ophalen en pakketten installeren**

```bash
git clone <repository-url>
cd AdminProject
composer install
npm install
```

**2. Omgevingsbestand klaarzetten**

```bash
cp .env.example .env
php artisan key:generate
```

Vul daarna in `.env` je databasegegevens in (naam, gebruiker, wachtwoord).

**3. Database aanmaken**

```bash
php artisan migrate
php artisan db:seed
```

**4. Front-end en server starten**

```bash
npm run dev
php artisan serve
```

De applicatie draait nu op `http://localhost:8000`.

## Geautomatiseerde taken

Het systeem heeft twee Artisan commands die dagelijks via de scheduler draaien:

- `CheckOpenstaandeBetalingen` – controleert welke betalingen open staan.
- `CheckDeactiveerLeden` – deactiveert leden die 3 maanden niet betaald hebben.

Om de scheduler lokaal te laten draaien:

```bash
php artisan schedule:work
```

Op een server zet je hiervoor een cronjob klaar.

## Over dit project

Deze applicatie is gemaakt als onderdeel van een BPV-stage van de opleiding HBO ICT (Webdevelopment). Het project is uitgevoerd bij Bit Dynamics N.V. door Regeffio Baarn, onder begeleiding van praktijkopleider Simon Noerdjan.
