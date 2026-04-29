# 🚀 Roadmap Técnico: Sincronización Inmediata & HUD Aeroespacial

Este documento detalla los pasos pendientes para finalizar la implementación de la infraestructura de alta fidelidad y sincronización en tiempo real.

## 1. ⚡ Configuración de Sincronización "Inmediata" (Firebase-First)
Para que el sistema local reciba actualizaciones de Firebase al instante sin depender del Worker de fondo.
- [ ] **Establecer Túnel de Seguridad**: Ejecutar `npx localtunnel --port 8000`.
- [ ] **Actualizar Firebase Functions**: Poner la URL del túnel en `index.js` y desplegar.

## 2. 💎 Refinamiento de Interfaz "Grado Aeroespacial"
Consolidar la estética HUD (Heads-Up Display) en todo el panel de control.
- [ ] **Sincronización de Heartbeat**: Verificar latencia real en el monitor HUD.
- [ ] **Efectos de Micro-Interacción**: Extender el estilo "Crystal Shell" a las tablas de afiliados.

## 3. ✨ Calidad de Datos (Activada ✅)
- [x] **Normalización de Nombres**: Conversión automática a Title Case al guardar.
- [x] **Limpieza de Direcciones**: Expansión automática de abreviaturas (C/, Esq., etc.).

## 4. 🔒 Resiliencia Operativa & Backups
- [ ] **Google Cloud Storage Backups**:
    - Crear script de respaldo automático de MySQL hacia el Storage de Firebase para evitar pérdida de datos local.

## 5. 🥷 Firebase Ninja (Optimización de Costos)
- [ ] **Implementar Cloud Function de Contadores**:
    ```javascript
    // Agregar a sys-carnet-functions/functions/index.js
    exports.countAfiliados = functions.firestore
        .document('afiliados/{id}')
        .onWrite(async (change) => {
            const db = admin.firestore();
            const counterRef = db.doc('metadata/stats');
            const increment = !change.before.exists ? 1 : (!change.after.exists ? -1 : 0);
            if (increment !== 0) {
                await counterRef.set({ total: admin.firestore.FieldValue.increment(increment) }, { merge: true });
            }
        });
    ```

---
*Documento evolucionado por Antigravity para Frederik.*
