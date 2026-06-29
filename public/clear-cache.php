<?php
// Tijdelijk cache-clear script - VERWIJDER DIT NA GEBRUIK!

// Prevent output buffering issues
if (ob_get_level()) ob_end_clean();

// Security: only allow from specific IPs or add a secret key check
// Uncomment and set your IP to add basic protection:
// if ($_SERVER['REMOTE_ADDR'] !== 'YOUR.IP.ADDRESS') { http_response_code(403); exit('Forbidden'); }

define('LARAVEL_START', microtime(true));

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

// Use the Console Kernel correctly
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$results = [];

$commands = [
    'route:clear',
    'cache:clear',
    'config:clear',
    'view:clear',
    'optimize:clear',
];

foreach ($commands as $command) {
    try {
        $exitCode = $kernel->call($command);
        $results[$command] = $exitCode === 0 ? '✅ Success' : '❌ Failed (exit ' . $exitCode . ')';
    } catch (\Throwable $e) {
        $results[$command] = '❌ Error: ' . htmlspecialchars($e->getMessage());
    }
}

echo "<html><head><style>
body { font-family: Arial, sans-serif; background: #1a1a2e; color: #eee; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
.card { background: #16213e; border-radius: 12px; padding: 40px; max-width: 540px; width: 100%; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
h2 { color: #e94560; margin-top: 0; }
.item { padding: 10px 15px; background: #0f3460; border-radius: 8px; margin: 8px 0; font-size: 15px; display: flex; justify-content: space-between; align-items: center; gap: 12px; }
.item code { font-size: 13px; color: #a0cfff; }
.warning { color: #ffcc00; font-size: 13px; margin-top: 20px; background: rgba(255,204,0,0.1); padding: 12px; border-radius: 8px; border-left: 3px solid #ffcc00; }
.btn { display: inline-block; margin-top: 16px; padding: 10px 22px; background: #e94560; color: #fff; border-radius: 8px; text-decoration: none; font-size: 14px; }
</style></head><body>
<div class='card'>
  <h2>🛠 Cache Cleared</h2>";

foreach ($results as $cmd => $status) {
    echo "<div class='item'><span><code>php artisan $cmd</code></span><span>$status</span></div>";
}

echo "<div class='warning'>⚠️ <strong>Verwijder dit bestand onmiddellijk!</strong><br>
Ga in cPanel File Manager naar <code>public/clear-cache.php</code> en verwijder het.</div>
<a class='btn' href='/login'>🔐 Ga naar Login</a>
</div></body></html>";
