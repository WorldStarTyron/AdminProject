# Khilafat Anjuman - cPanel Deployment & Troubleshoot Documentatie

Dit document beschrijft de stappen, uitdagingen en oplossingen die zijn doorlopen tijdens het online implementeren van de Laravel-applicatie **Khilafat Anjuman** op een cPanel-omgeving. Het dient als naslagwerk voor toekomstige deployments.

---

## 1. Belangrijke Les: Eerst Laravel Installeren op cPanel
Voordat de eigen projectbestanden succesvol konden worden geïmplementeerd, liepen we tegen veel database-, routerings- en verbindingsfouten aan. 

### Het Probleem
Bij het direct uploaden van de lokale Laravel-bestanden naar cPanel weigerde de server de routes te herkennen en mislukten databaseverbindingen. Dit kwam doordat de onderliggende PHP-omgeving, Composer-afhankelijkheden en serverconfiguraties op cPanel nog niet correct waren geïnitialiseerd voor Laravel.

### De Oplossing
De cruciale stap die eerst gezet moest worden: **Laravel installeren via de cPanel Application Manager / Softaculous**. 
* Door eerst een schone Laravel-installatie te doen op de server, configureert cPanel automatisch de juiste serverpaden, directory-machtigingen (zoals de symlink naar `public_html`), PHP-extensies en de benodigde `.htaccess`-redirects.
* Pas **daarna** konden we onze eigen projectbestanden (views, controllers, models, databasescripts en assets) overschrijven, wat leidde tot een correct functionerende backend.

---

## 2. Gevonden Problemen & Oplossingen

Tijdens het finetunen van de livegang kwamen we drie specifieke problemen tegen die te maken hadden met het verschil tussen de lokale Windows-ontwikkelomgeving en de online Linux-omgeving.

### Probleem A: Submap `/public` in URLs (JavaScript/AJAX)
* **Symptoom:** Modals slaagden er niet in om betalingen op te slaan en grafieken toonden geen data.
* **Oorzaak:** Omdat de live website via een submap (bijv. `domeinnaam.nl/public/...`) draait, zochten de JavaScript fetch-verzoeken naar de root van de domeinnaam (`/dashboard/chart-data`), waardoor ze een 404-fout kregen.
* **Oplossing:** Alle fetch-verzoeken in de JavaScript-bestanden zijn aangepast met een dynamische pad-check:
  ```javascript
  var basePath = window.location.pathname.startsWith('/public') ? '/public' : '';
  var url = basePath + '/dashboard/chart-data';
  ```
  *Getroffen bestanden: `MainDashboard-chart.js`, `TotalBetaling-chart.js`, `EditBetalingModal.js`, en `Herstel.js`.*

### Probleem B: Geen opmaak/CSS op de live website (Vite `hot` reload bug)
* **Symptoom:** De website laadde plotseling als kale tekst/HTML zonder opmaak en stylesheets. In de console stonden `net::ERR_CONNECTION_REFUSED` foutmeldingen naar `localhost:5173`.
* **Oorzaak:** Tijdens het uploaden was het bestand `public/hot` mee-geüpload. Dit bestand vertelt Laravel om stylesheets in te laden vanaf een lokale ontwikkelserver (`npm run dev`), wat online natuurlijk niet werkt.
* **Oplossing:** Er is een zelfhelende controle ingebouwd in [AppServiceProvider.php](file:///C:/Users/Regeffio/Desktop/AdminChannel/AdminProject/app/Providers/AppServiceProvider.php):
  ```php
  // Auto-fix: verwijder Vite 'hot' bestand op de live server
  if (config('app.env') === 'production' || (!app()->runningInConsole() && !in_array(request()->getHost(), ['localhost', '127.0.0.1', '::1']))) {
      $hotPath = public_path('hot');
      if (file_exists($hotPath)) {
          @unlink($hotPath);
      }
  }
  ```
  Zodra de website online geladen wordt, verwijdert Laravel nu automatisch het storende `hot`-bestand, waardoor de styling direct herstelt.

### Probleem C: Hoofdlettergevoeligheid (Linux vs Windows)
* **Symptoom:** Bij het inloggen als *Lid* of bij specifieke schermen crashte de website met een `InvalidArgumentException: View [lidpagina] not found`.
* **Oorzaak:** Windows is niet hoofdlettergevoelig, dus `view('lidpagina')` vond het bestand `Lidpagina.blade.php` lokaal wel. cPanel draait op Linux, wat wél hoofdlettergevoelig is. De server zocht naar `lidpagina.blade.php` en kon dit niet vinden.
* **Oplossing:** Alle controller-aanroepen zijn gecorrigeerd naar de exacte bestandsnamen op de server:
  * In `LidController.php`: `lidpagina` gewijzigd naar `Lidpagina`.
  * In `PostController.php`: `EditLidPagina` gewijzigd naar `editLidPagina` en `Layouts.AddModals.add-lid-modal` naar `layouts.AddModals.add-lid-modal`.

---

## 3. Checklist voor Toekomstige Updates
Volg deze stappen als je wijzigingen in de code lokaal hebt gemaakt en deze naar cPanel wilt uploaden:

1. **Vite Assets compileren:**
   Draai lokaal in de terminal:
   ```bash
   npm run build
   ```
2. **Sluitsysteem controleren:**
   Sluit je lokale `npm run dev` proces af (in je terminal via `Ctrl + C`) voordat je bestanden uploadt, om te voorkomen dat er een lokaal `public/hot` bestand wordt aangemaakt.
3. **Uploaden naar cPanel:**
   Upload de gewijzigde controllers, views en de gehele map `public/build/` (hier staan je gecompileerde CSS- en JS-bestanden in).
4. **Cache Leegmaken (indien nodig):**
   Mocht de layout of configuratie niet direct updaten op de server, open dan kort je browser op `https://jouw-website.nl/clear-cache.php` (dit tijdelijke script voert `php artisan optimize:clear` uit). *Vergeet niet dit script naderhand weer te verwijderen uit je online public-map!*
