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
                'resources/js/app.js',
                'resources/js/Rolbeheer.js',
                'resources/js/Charts/Rapportage-Chart.js',
                'resources/js/Charts/MainDashboard-chart.js',
                'resources/js/Login.js',
                'resources/js/EditPagina.js',
                'resources/js/FormValidator.js',
                'resources/js/Modal/AddLidModal.js',
                'resources/js/Button&More.js',
                'resources/js/Charts/TotalLeden-Chart.js',
                'resources/js/verifycode.js',
                'resources/js/Modal/AddBetalingModal.js',
                'resources/js/Modal/EditBetalingModal.js',
                'resources/js/Charts/TotalBetaling-chart.js',
                'resources/js/OptieDisable.js',
                'resources/js/CheckSubscriptie.js'
            ],
            refresh: true,
        }),
    ],
});
