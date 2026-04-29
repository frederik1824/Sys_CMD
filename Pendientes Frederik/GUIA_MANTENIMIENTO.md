# 🛠️ Guía de Mantenimiento y Operación - SysCarnet

Este documento detalla las acciones y comandos fundamentales para asegurar el correcto funcionamiento del sistema, especialmente los procesos de sincronización con Firebase Cloud.

---

## 🚀 1. El Motor de Segundo Plano (Queues)

La sincronización en SysCarnet (Pull y Push) se ejecuta de forma asíncrona para no bloquear la interfaz del usuario. Para que esto funcione, **debes tener un proceso de escucha de colas activo.**

### Comando Principal:
```bash
php artisan queue:work
```
- **¿Qué hace?**: Escucha y ejecuta los trabajos (Jobs) de sincronización que se envían cuando presionas botones en el "Sync Center".
- **Recomendación**: En un entorno de producción, este comando debería ser gestionado por una herramienta como **Supervisor** para que se reinicie automáticamente si falla.

---

## 🤖 2. Comandos de Sincronización Manual (Artisan)

Aunque existe el "Sync Center" en la web, puedes ejecutar estos comandos directamente desde la terminal para mantenimiento o depuración masiva.

### Descarga de Datos (Firebase -> Local):
```bash
# Modo Inteligente (Delta Sync): Solo descarga lo que ha cambiado desde la última vez.
php artisan firebase:sync-pull

# Modo Total: Descarga todo desde cero (Cuidado: consume mucha cuota).
php artisan firebase:sync-pull --full
```

### Subida de Datos (Local -> Firebase):
```bash
# Sincronización Diferencial: Sube solo los registros locales modificados.
php artisan firebase:push-all

# Forzar Subida Completa: Sobrescribe todo en la nube con los datos locales.
php artisan firebase:push-all --force
```

---

## ⚙️ 3. Configuración y Credenciales

El sistema se comunica con Firebase mediante una cuenta de servicio de Google Cloud.

- **Archivo de Credenciales**: `syscarnet-firebase-adminsdk-fbsvc-7eab5483a2.json` (Ubicado en la raíz).
- **Variable de Entorno**: Verifica en tu archivo `.env` que la ruta sea correcta:
  ```env
  FIREBASE_CREDENTIALS=syscarnet-firebase-adminsdk-fbsvc-7eab5483a2.json
  ```

> [!WARNING]
> **Seguridad**: Nunca compartas el archivo `.json` de credenciales fuera de tu entorno seguro, ya que otorga acceso administrativo total a tu base de datos de Firebase.

---

## 🧹 4. Mantenimiento del Sistema

Acciones periódicas para mantener el sistema limpio y actualizado:

| Acción | Comando | Motivo |
| :--- | :--- | :--- |
| **Limpiar Caché** | `php artisan cache:clear` | Si el indicador de progreso (barra de carga) se queda "pegado" erróneamente. |
| **Actualizar DB** | `php artisan migrate` | Si añado una nueva función que requiera cambios en las tablas de la base de datos. |
| **Verificar Colas** | `php artisan queue:failed` | Para ver si algún proceso de sincronización falló por problemas de internet. |
| **Reiniciar Colas** | `php artisan queue:flush` | Limpia todos los trabajos fallidos de la lista de espera. |

---

## 📈 5. Automatización (Opcional)

Si deseas que el sistema se sincronice solo todas las noches, puedes configurar una tarea programada (Cron Job) que ejecute:
```bash
php artisan schedule:run
```
*(Nota: Requiere configuración previa en `routes/console.php`)*.

---

> [!TIP]
> **¿Problemas con la barra de progreso?**
> Si iniciaste una sincronización y no ves movimiento, lo primero que debes revisar es que la terminal con `php artisan queue:work` esté abierta y sin errores de conexión.
