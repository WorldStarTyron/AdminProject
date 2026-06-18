// Dit bestand zorgt voor het wegstoppen van de success melding
// Na 5 seconden verdwijnt de melding

setTimeout(function() {
    var toast = document.getElementById('successToast');
    if (toast) {
        toast.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-10px)';
        setTimeout(function() { toast.remove(); }, 500);
    }
}, 5000);
