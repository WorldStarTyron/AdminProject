# Plan: Fix Betaling Status Issue

## Probleem
Wanneer een lid een betaling bewijs upload via het formulier:
- De status zou "in_wachting" moeten worden
- Maar het wordt nu "afgewezen" (Openstaand) in plaats daarvan

## Oorzaak
1. De status dropdown in het formulier heeft geen "in_wachting" optie
2. Als een bewijs wordt geüpload, wordt de status niet automatisch op "in_wachting" gezet
3. Als de betaling wordt afgekeurd, wordt de status op "Openstaand" gezet i.p.v. een duidelijke "afgewezen" status

## Oplossing

### Stap 1: Status automatisch zetten bij bewijs upload
In `AddBetalingModal.js`: Als er een bestand wordt geüpload, de status automatisch op "in_wachting" zetten

### Stap 2: Formulier validatie aanpassen
In `add-Betaling-modal.blade.php`: De status dropdown aanpassen zodat admins de status handmatig kunnen wijzigen

### Stap 3: Reject functie fixen  
In `BetalingController.php`: De RejectBewijs functie moet een duidelijke "afgewezen" status gebruiken

## Bijgewerkte files

- [x] `resources/js/Utils/OptieDisable.js` - Auto-set status naar "in_wachting" als bewijs wordt geüpload
- [x] `resources/views/layouts/AddModals/add-Betaling-modal.blade.php` - Status opties: in_wachting en afgewezen toegevoegd
- [x] `resources/views/layouts/EditModals/edit-Betaling-modal.blade.php` - Status opties: in_wachting en afgewezen toegevoegd
- [x] `app/Http/Controllers/BetalingController.php` - RejectBewijs zet nu status op "afgewezen"

## Status

✓ Fix voltooid
