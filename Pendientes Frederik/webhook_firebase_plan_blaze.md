# 📌 Pendiente: Finalización de Webhook Real-Time

## Estado Actual
- **Laravel**: Implementado al 100% (Rutas, Middleware, Controlador y Secret en .env).
- **Firebase Functions**: Código base escrito en `/sys-carnet-functions/functions/index.js`.
- **Bloqueo**: Se requiere subir el proyecto de Firebase al **Plan Blaze** (Pay-as-you-go) para permitir el despliegue de funciones (es un requisito de Google Cloud Build).

## Pasos para retomar
1.  **Upgrade**: Ir a [Firebase Console](https://console.firebase.google.com/project/syscarnet/usage/details) y activar el Plan Blaze.
2.  **URL ngrok**: Iniciar ngrok (`ngrok http 8000`) y actualizar la URL en `index.js`.
3.  **Despliegue**: Ejecutar `firebase deploy --only functions` desde la carpeta `sys-carnet-functions`.
4.  **Prueba**: Cambiar un dato en Firestore y verificar actualización instantánea en Laravel.

---
*Nota: El plan Blaze es gratuito hasta los 2 millones de ejecuciones, pero requiere tarjeta de crédito para activarse.*
