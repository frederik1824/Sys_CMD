# Protocolo de Entrega y Cierre Operativo (CMD & Safesure)

Este documento describe la nueva logística de estados para garantizar la trazabilidad total desde la entrega en campo hasta la recepción del soporte físico en las oficinas de CMD.

## 🔄 Flujo de Estados del Carnet

### Paso 1: Entrega en Campo (Responsabilidad Safesure)
*   **Acción**: El mensajero entrega de forma efectiva el carnet al afiliado.
*   **Estado en Firebase**: Se debe marcar el campo `estado_id` con el valor **6** (`Entregado`).
*   **Requisito de Evidencia**: Es obligatorio adjuntar la foto del acuse de recibo o formulario firmado en Firebase.
*   **Visibilidad**: En el sistema CMD, el carnet aparecerá en color azul/verde indicando que ya fue entregado en calle, pero el proceso **aún no está cerrado**.

### Paso 2: Recepción de Soportes Físicos (Logística CMD)
*   **Acción**: Los mensajeros o transportistas entregan los formularios físicos firmados en la oficina central de CMD.
*   **Validación**: El personal de CMD verifica que el carnet físico/documentación coincida con lo reportado en el sistema.

### Paso 3: Confirmación y Cierre (Responsabilidad CMD)
*   **Acción**: Al validar el documento físico, el personal de CMD hará clic en el botón **"Confirmar Recepción"**.
*   **Estado en Firebase**: El sistema CMD actualizará automáticamente el `estado_id` a **9** (`Completado`).
*   **Efecto Inmutable**: Una vez el carnet pasa a estado 9, el expediente queda **bloqueado**. No se permiten cambios de responsable, empresa o estados adicionales sin una "Solicitud de Reapertura".

---

## 📋 Requerimientos Técnicos para Safesure

Para que esta logística funcione, solicitamos al equipo de Safesure asegurar lo siguiente:

1.  **Soporte de Estado 6**: Asegurar que sus triggers o scripts de actualización usen el ID **6** para las entregas exitosas.
2.  **Consulta de Cierre**: Safesure puede monitorear el estado **9** en Firebase para saber cuándo un carnet ha sido formalmente aceptado y cerrado por CMD.
3.  **Trazabilidad de Errores**: Si un carnet es marcado como 6 (Entregado) pero CMD detecta un error en el físico, CMD usará la **Nota de Seguimiento** para notificar a Safesure antes de pasar al estado 9.

---
*Documento preparado para coordinación con el equipo técnico de Safesure.*