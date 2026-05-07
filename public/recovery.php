<?php
/**
 * SysCarnet Emergency Recovery Tool
 * Esta herramienta funciona fuera del framework para casos de desastre total.
 */

// SEGURIDAD: Solo permitir acceso desde IP local o mediante un token (OPCIONAL)
// define('ALLOWED_IP', '127.0.0.1');
// if ($_SERVER['REMOTE_ADDR'] !== ALLOWED_IP) { die('Acceso Denegado'); }

$storagePath = __DIR__ . '/../storage/app/backups';
$envFile = __DIR__ . '/../.env';

function getEnvValue($key, $default = '') {
    global $envFile;
    if (!file_exists($envFile)) return $default;
    $content = file_get_contents($envFile);
    if (preg_match('/^' . $key . '=(.*)$/m', $content, $matches)) {
        return trim($matches[1]);
    }
    return $default;
}

$backups = [];
if (is_dir($storagePath)) {
    $files = scandir($storagePath, SCANDIR_SORT_DESCENDING);
    foreach ($files as $file) {
        if (str_ends_with($file, '.sql')) {
            $backups[] = $file;
        }
    }
}

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['backup_file'])) {
    $targetFile = $_POST['backup_file'];
    if (in_array($targetFile, $backups)) {
        $dbHost = getEnvValue('DB_HOST', '127.0.0.1');
        $dbName = getEnvValue('DB_DATABASE');
        $dbUser = getEnvValue('DB_USERNAME');
        $dbPass = getEnvValue('DB_PASSWORD');
        
        $command = sprintf(
            'mysql --user=%s --password=%s --host=%s %s < %s',
            escapeshellarg($dbUser),
            escapeshellarg($dbPass),
            escapeshellarg($dbHost),
            escapeshellarg($dbName),
            escapeshellarg($storagePath . '/' . $targetFile)
        );

        exec($command, $output, $returnVar);
        if ($returnVar === 0) {
            $message = "<div class='success'>✅ Sistema Restaurado Exitosamente. Intenta entrar al ERP ahora.</div>";
        } else {
            $message = "<div class='error'>❌ Fallo en la restauración. Código de error: $returnVar</div>";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SysCarnet | MODO RECUPERACIÓN</title>
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; background: #0f172a; color: white; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .card { background: #1e293b; padding: 40px; border-radius: 32px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); max-width: 500px; width: 90%; border: 1px solid #334155; }
        h1 { font-size: 24px; font-weight: 900; margin-bottom: 8px; color: #f43f5e; }
        p { color: #94a3b8; font-size: 14px; line-height: 1.6; margin-bottom: 24px; }
        select { width: 100%; padding: 16px; border-radius: 16px; background: #0f172a; border: 1px solid #334155; color: white; margin-bottom: 20px; font-weight: bold; }
        button { width: 100%; padding: 16px; border-radius: 16px; background: #f43f5e; color: white; border: none; font-weight: 900; text-transform: uppercase; letter-spacing: 2px; cursor: pointer; transition: all 0.2s; }
        button:hover { background: #e11d48; transform: translateY(-2px); }
        .success { background: #064e3b; color: #6ee7b7; padding: 16px; border-radius: 16px; margin-bottom: 20px; font-size: 13px; font-weight: bold; }
        .error { background: #4c0519; color: #fda4af; padding: 16px; border-radius: 16px; margin-bottom: 20px; font-size: 13px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="card">
        <h1>🚨 MODO RECUPERACIÓN</h1>
        <p>Esta herramienta funciona de forma independiente al ERP. Úsala solo si el sistema principal no carga.</p>
        
        <?php echo $message; ?>

        <form method="POST">
            <label style="display:block; font-size: 10px; font-weight: 900; color: #64748b; margin-bottom: 8px; text-transform: uppercase;">Seleccionar Punto de Restauración</label>
            <select name="backup_file" required>
                <?php foreach($backups as $file): ?>
                    <option value="<?php echo $file; ?>"><?php echo $file; ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Ejecutar Restauración Crítica</button>
        </form>
        
        <div style="margin-top: 24px; text-align: center;">
            <a href="/" style="color: #64748b; font-size: 12px; text-decoration: none; font-weight: bold;">← Volver al Sistema Principal</a>
        </div>
    </div>
</body>
</html>
