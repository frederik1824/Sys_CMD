# 📡 Implementación de Webhooks Real-Time (Firebase -> Laravel)

Esta configuración permitirá que cada vez que un carnet o empresa cambie en Firestore, el servidor Laravel lo sincronice en milisegundos.

## 1. Código para Firebase Cloud Function (Node.js)

Debes crear una función en el archivo `index.js` de tus Firebase Functions:

```javascript
const functions = require('firebase-functions');
const axios = require('axios');

// CONFIGURACIÓN (Cambiar por tu URL real)
const LARAVEL_WEBHOOK_URL = 'https://TU-DOMINIO.com/firebase/webhook';
const WEBHOOK_SECRET = 'EL_VALOR_QUE_ESTA_EN_TU_ENV'; // FIREBASE_WEBHOOK_SECRET

exports.onAfiliadoUpdate = functions.firestore
    .document('afiliados/{id}')
    .onWrite(async (change, context) => {
        const data = change.after.exists ? change.after.data() : null;
        
        if (!data) return null; // Documento borrado

        try {
            await axios.post(LARAVEL_WEBHOOK_URL, {
                type: 'afiliado',
                id: context.params.id,
                uuid: data.uuid || null
            }, {
                headers: { 'X-Firebase-Secret': WEBHOOK_SECRET }
            });
            console.log(`Notificación enviada a Laravel para Afiliado: ${context.params.id}`);
        } catch (error) {
            console.error('Error enviando Webhook:', error.message);
        }
    });

exports.onEmpresaUpdate = functions.firestore
    .document('empresas/{id}')
    .onWrite(async (change, context) => {
        const data = change.after.exists ? change.after.data() : null;
        if (!data) return null;

        try {
            await axios.post(LARAVEL_WEBHOOK_URL, {
                type: 'empresa',
                id: context.params.id,
                uuid: data.uuid || null
            }, {
                headers: { 'X-Firebase-Secret': WEBHOOK_SECRET }
            });
            console.log(`Notificación enviada a Laravel para Empresa: ${context.params.id}`);
        } catch (error) {
            console.error('Error enviando Webhook:', error.message);
        }
    });
```

## 2. Notas Importantes para el Desarrollo Local
Como estás trabajando en `127.0.0.1:8000`, Firebase (que está en la nube) no puede ver tu computadora directamente. Para probar esto localmente, necesitas usar una herramienta como **ngrok**:

1.  Instala ngrok y corre: `ngrok http 8000`
2.  Copia la URL que te de ngrok (ej: `https://a1b2-c3d4.ngrok.io`)
3.  Úsala en la constante `LARAVEL_WEBHOOK_URL` del script de arriba.

## 3. Seguridad
El sistema ya está configurado para rechazar cualquier petición que no incluya el encabezado `X-Firebase-Secret` correcto que generamos hoy.
