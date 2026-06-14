import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/sidebar.css',
                'resources/css/Toevoegen.css',
                'resources/css/login.css',
                'resources/css/ledenpagina-new.css',
                'resources/css/BetalingPagina.css',
                'resources/js/UI/Sidebar.js',
                'resources/js/Pages/Rolbeheer.js',
                'resources/js/Charts/Rapportage-Chart.js',
                'resources/js/Charts/MainDashboard-chart.js',
                'resources/js/Auth/Login.js',
                'resources/js/Pages/EditPagina.js',
                'resources/js/Utils/FormValidator.js',
                'resources/js/AddModals/AddLidModal.js',
                'resources/js/UI/Button&More.js',
                'resources/js/Charts/TotalLeden-Chart.js',
                'resources/js/Auth/verifycode.js',
                'resources/js/AddModals/AddBetalingModal.js',
                'resources/js/EditModals/EditBetalingModal.js',
                'resources/js/EditModals/EditGebruikerModal.js',
                'resources/js/Charts/TotalBetaling-chart.js',
                'resources/js/Utils/OptieDisable.js'
            ],
            refresh: true,
        }),
    ],
});
