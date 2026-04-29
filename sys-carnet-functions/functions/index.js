/**
 * Import function triggers from their respective submodules:
 *
 * const {onCall} = require("firebase-functions/v2/https");
 * const {onDocumentWritten} = require("firebase-functions/v2/firestore");
 *
 * See a full list of supported triggers at https://firebase.google.com/docs/functions
 */

const { setGlobalOptions } = require("firebase-functions");
const { onRequest } = require("firebase-functions/https");
const logger = require("firebase-functions/logger");

// For cost control, you can set the maximum number of containers that can be
// running at the same time. This helps mitigate the impact of unexpected
// traffic spikes by instead downgrading performance. This limit is a
// per-function limit. You can override the limit for each function using the
// `maxInstances` option in the function's options, e.g.
// `onRequest({ maxInstances: 5 }, (req, res) => { ... })`.
// NOTE: setGlobalOptions does not apply to functions using the v1 API. V1
// functions should each use functions.runWith({ maxInstances: 10 }) instead.
// In the v1 API, each function can only serve one request per container, so
// this will be the maximum concurrent request count.
setGlobalOptions({ maxInstances: 10 });

// Create and deploy your first functions
// https://firebase.google.com/docs/functions/get-started

// exports.helloWorld = onRequest((request, response) => {
//   logger.info("Hello logs!", {structuredData: true});
//   response.send("Hello from Firebase!");
// });
const functions = require('firebase-functions');
const axios = require('axios');

// ⚠️ CAMBIA ESTO por tu URL de ngrok cuando lo tengas corriendo
// Ejemplo: 'https://a1b2c3d4.ngrok-free.app/firebase/webhook'
const LARAVEL_WEBHOOK_URL = 'https://TU_URL_NGROK.ngrok-free.app/firebase/webhook';
const WEBHOOK_SECRET = 'b14510bfe2bf4ff9806aa2d70961094c';

// Función para Afiliados
exports.onAfiliadoUpdate = functions.firestore
    .document('afiliados/{id}')
    .onWrite(async (change, context) => {
        const data = change.after.exists ? change.after.data() : null;
        if (!data) return null; // Ignorar borrados

        try {
            await axios.post(LARAVEL_WEBHOOK_URL, {
                type: 'afiliado',
                id: context.params.id,
                uuid: data.uuid || null
            }, {
                headers: { 'X-Firebase-Secret': WEBHOOK_SECRET }
            });
            console.log(`Sync: Afiliado ${context.params.id} enviado a Laravel`);
        } catch (error) {
            console.error('Error Webhook Afiliado:', error.message);
        }
    });

// Función para Empresas
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
            console.log(`Sync: Empresa ${context.params.id} enviada a Laravel`);
        } catch (error) {
            console.error('Error Webhook Empresa:', error.message);
        }
    });
