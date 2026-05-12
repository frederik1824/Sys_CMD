<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\Admin\DispersionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Reorganized into modular prefixes to avoid route collisions and improve context.
*/

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('portal');
    }
    return view('welcome');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/portal', [\App\Http\Controllers\PortalController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('portal');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::get('stop-impersonating', [\App\Http\Controllers\Admin\AccessControlController::class, 'stopImpersonating'])->name('usuarios.stop_impersonating');

    // --------------------------------------------------------------------------
    // ADMINISTRACIÓN CENTRAL DE ACCESOS
    // --------------------------------------------------------------------------
    Route::prefix('admin/control-accesos')->name('admin.access.')->middleware(['auth', 'role:Admin|Super-Admin'])->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AccessControlController::class, 'index'])->name('index');
        
        // Gestión de Usuarios Centralizada
        Route::get('/users', [\App\Http\Controllers\Admin\AccessControlController::class, 'users'])->name('users');
        Route::get('/users/create', [\App\Http\Controllers\Admin\AccessControlController::class, 'createUser'])->name('users.create');
        Route::post('/users/store', [\App\Http\Controllers\Admin\AccessControlController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{user}/edit', [\App\Http\Controllers\Admin\AccessControlController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{user}/update', [\App\Http\Controllers\Admin\AccessControlController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}/delete', [\App\Http\Controllers\Admin\AccessControlController::class, 'deleteUser'])->name('users.delete');
        Route::post('/users/{user}/reset-password', [\App\Http\Controllers\Admin\AccessControlController::class, 'resetPassword'])->name('users.reset-password');

        // Gestión de Aplicaciones (Módulos)
        Route::get('/apps', [\App\Http\Controllers\Admin\AccessControlController::class, 'applications'])->name('apps');
        Route::get('/apps/create', [\App\Http\Controllers\Admin\AccessControlController::class, 'createApplication'])->name('apps.create');
        Route::post('/apps/store', [\App\Http\Controllers\Admin\AccessControlController::class, 'storeApplication'])->name('apps.store');
        Route::get('/apps/{application}/edit', [\App\Http\Controllers\Admin\AccessControlController::class, 'editApplication'])->name('apps.edit');
        Route::put('/apps/{application}/update', [\App\Http\Controllers\Admin\AccessControlController::class, 'updateApplication'])->name('apps.update');
        Route::patch('/apps/{application}/toggle', [\App\Http\Controllers\Admin\AccessControlController::class, 'toggleApplication'])->name('apps.toggle');

        // Gestión de Roles y Permisos
        Route::get('/roles', [\App\Http\Controllers\Admin\AccessControlController::class, 'roles'])->name('roles');
        Route::get('/roles/create', [\App\Http\Controllers\Admin\AccessControlController::class, 'createRole'])->name('roles.create');
        Route::post('/roles/store', [\App\Http\Controllers\Admin\AccessControlController::class, 'storeRole'])->name('roles.store');
        Route::get('/roles/{role}/edit', [\App\Http\Controllers\Admin\AccessControlController::class, 'editRole'])->name('roles.edit');
        Route::put('/roles/{role}/update', [\App\Http\Controllers\Admin\AccessControlController::class, 'updateRole'])->name('roles.update');
        Route::post('/roles/{role}/duplicate', [\App\Http\Controllers\Admin\AccessControlController::class, 'duplicateRole'])->name('roles.duplicate');
        Route::get('/permissions', [\App\Http\Controllers\Admin\AccessControlController::class, 'permissions'])->name('permissions');

        Route::post('/store', [\App\Http\Controllers\Admin\AccessControlController::class, 'store'])->name('store');
        Route::patch('/{access}/toggle', [\App\Http\Controllers\Admin\AccessControlController::class, 'toggleAccess'])->name('toggle');
        Route::delete('/{access}/revoke', [\App\Http\Controllers\Admin\AccessControlController::class, 'revokeAccess'])->name('revoke');
        Route::post('/{user}/impersonate', [\App\Http\Controllers\Admin\AccessControlController::class, 'impersonate'])->name('impersonate');
        Route::get('/audit', [\App\Http\Controllers\Admin\AccessControlController::class, 'auditLogs'])->name('audit');
    });

    // Esta ruta debe ser accesible por el usuario impersonado para poder volver
    Route::post('/impersonate/stop', [\App\Http\Controllers\Admin\AccessControlController::class, 'stopImpersonating'])->name('admin.access.impersonate.stop');

    // Redirección de Rutas Legadas a la Nueva Consola de Seguridad
    Route::get('sistema/usuarios', function() { return redirect()->route('admin.access.users'); });
    Route::get('sistema/usuarios/create', function() { return redirect()->route('admin.access.users.create'); });
    Route::get('sistema/usuarios/{user}/edit', function($user) { return redirect()->route('admin.access.users.edit', $user); });

    // --------------------------------------------------------------------------
    // MODULO: CARNETIZACIÓN (ID SYSTEM / CMD)
    // Prefix: /carnetizacion | Name: carnetizacion.*
    // --------------------------------------------------------------------------
    Route::prefix('carnetizacion')->name('carnetizacion.')->middleware('app_access:cmd')->group(function () {
        
        // Main Dashboard
        Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
        
        // Sync Center (Requested URL: /carnetizacion/sync-center)
        Route::prefix('sync-center')->name('sync_center.')->group(function() {
            Route::get('/', [App\Http\Controllers\FirebaseSyncController::class, 'index'])->name('index');
            Route::post('/pull', [App\Http\Controllers\FirebaseSyncController::class, 'pull'])->name('sync_pull');
            Route::post('/push', [App\Http\Controllers\FirebaseSyncController::class, 'push'])->name('sync_push');
            Route::post('/pause', [App\Http\Controllers\FirebaseSyncController::class, 'pause'])->name('sync_pause');
            Route::post('/resume', [App\Http\Controllers\FirebaseSyncController::class, 'resume'])->name('sync_resume');
            Route::post('/cancel', [App\Http\Controllers\FirebaseSyncController::class, 'cancel'])->name('sync_cancel');
            Route::get('/health-check', [App\Http\Controllers\FirebaseSyncController::class, 'healthCheck'])->name('health_check');
            Route::post('/reconcile', [App\Http\Controllers\FirebaseSyncController::class, 'reconcile'])->name('reconcile');
            Route::post('/cleanup-snapshots', [App\Http\Controllers\FirebaseSyncController::class, 'cleanupSnapshots'])->name('cleanup_snapshots');
            Route::post('/purge-cache', [App\Http\Controllers\FirebaseSyncController::class, 'purgeCache'])->name('purge_cache');
            Route::post('/purge-queue', [App\Http\Controllers\FirebaseSyncController::class, 'purgeQueue'])->name('purge_queue');
            Route::get('/progress', [App\Http\Controllers\FirebaseSyncController::class, 'progress'])->name('progress');
            Route::get('/compare', [App\Http\Controllers\FirebaseSyncController::class, 'compare'])->name('compare');
            Route::get('/snapshots', [App\Http\Controllers\FirebaseSyncController::class, 'list_snapshots'])->name('list_snapshots');
            Route::post('/restore-snapshot', [App\Http\Controllers\FirebaseSyncController::class, 'restoreSnapshot'])->name('restore_snapshot');
            
            // New Advanced Views
            Route::get('/records', [App\Http\Controllers\FirebaseSyncController::class, 'records'])->name('records');
            Route::get('/conflicts', [App\Http\Controllers\FirebaseSyncController::class, 'conflicts'])->name('conflicts');
        });

        // Módulo de Afiliados
        Route::middleware('can:manage_affiliates')->group(function() {
            Route::post('afiliados/sync-firebase', [\App\Http\Controllers\AfiliadoController::class, 'syncFirebase'])->name('afiliados.sync_firebase');
            Route::get('afiliados/sync-progress', [\App\Http\Controllers\AfiliadoController::class, 'getProgress'])->name('afiliados.sync_progress');
            Route::post('afiliados/{afiliado}/sync_single', [\App\Http\Controllers\AfiliadoController::class, 'syncSingle'])->name('afiliados.sync_single')->whereUuid('afiliado');
            Route::get('afiliados/check-duplicate', [\App\Http\Controllers\AfiliadoController::class, 'checkDuplicate'])->name('afiliados.check_duplicate');
            Route::get('afiliados/search-ajax', [\App\Http\Controllers\AfiliadoController::class, 'searchAjax'])->middleware('throttle:60,1')->name('afiliados.search_ajax');
            Route::get('afiliados/export', [\App\Http\Controllers\AfiliadoController::class, 'export'])->name('afiliados.export');
            Route::post('afiliados/sanitize', [\App\Http\Controllers\AfiliadoController::class, 'sanitizeAddresses'])->name('afiliados.sanitize');
            Route::get('afiliados/mios', [\App\Http\Controllers\AfiliadoController::class, 'indexMios'])->name('afiliados.mios');
            Route::get('afiliados/cmd', [\App\Http\Controllers\AfiliadoController::class, 'indexCmd'])->name('afiliados.cmd');
            Route::get('afiliados/otros', [\App\Http\Controllers\AfiliadoController::class, 'indexOtros'])->name('afiliados.otros');
            Route::get('afiliados/call-center', [\App\Http\Controllers\AfiliadoController::class, 'indexCallCenter'])->name('afiliados.call_center');
            Route::get('afiliados/salida-inmediata', [\App\Http\Controllers\AfiliadoController::class, 'indexSalidaInmediata'])->name('afiliados.salida_inmediata');
            Route::post('afiliados/bulk-assign', [\App\Http\Controllers\AfiliadoController::class, 'bulkAssign'])->name('afiliados.bulk_assign');
            Route::post('afiliados/bulk-company', [\App\Http\Controllers\AfiliadoController::class, 'bulkCompany'])->name('afiliados.bulk_company');
            Route::post('afiliados/{afiliado}/reassign', [\App\Http\Controllers\AfiliadoController::class, 'reassign'])->name('afiliados.reassign')->whereUuid('afiliado');
            Route::post('afiliados/{afiliado}/confirm-receipt', [\App\Http\Controllers\AfiliadoController::class, 'confirmReceipt'])->name('afiliados.confirm_receipt')->whereUuid('afiliado');
            Route::post('afiliados/{afiliado}/confirm-acuse', [\App\Http\Controllers\AfiliadoController::class, 'confirmAcuse'])->name('afiliados.confirm_acuse')->whereUuid('afiliado');
            Route::post('afiliados/bulk-status', [\App\Http\Controllers\AfiliadoController::class, 'bulkStatus'])->name('afiliados.bulk_status');
            Route::post('afiliados/{afiliado}/estado_single', [\App\Http\Controllers\AfiliadoController::class, 'updateStatus'])->name('afiliados.update_status')->whereUuid('afiliado');
            Route::post('afiliados/{afiliado}/evidencia', [\App\Http\Controllers\AfiliadoController::class, 'uploadEvidencia'])->name('afiliados.upload_evidencia')->whereUuid('afiliado');
            Route::post('afiliados/{afiliado}/reopen', [\App\Http\Controllers\AfiliadoController::class, 'reopen'])->name('afiliados.reopen')->whereUuid('afiliado');
            Route::resource('afiliados', \App\Http\Controllers\AfiliadoController::class)->whereUuid('afiliado');
            Route::post('notas', [\App\Http\Controllers\NotaController::class, 'store'])->name('notas.store');

            // Importación
            Route::get('importar', [\App\Http\Controllers\ImportController::class, 'index'])->name('import.index');
            Route::post('importar', [\App\Http\Controllers\ImportController::class, 'store'])->name('import.store');
            Route::get('importar/progreso/{lote_id}', [\App\Http\Controllers\ImportController::class, 'getProgress'])->name('import.progress');
            Route::get('importar/plantilla', [\App\Http\Controllers\ImportController::class, 'downloadTemplate'])->name('import.template');
            
            // Auditoría contextual
            // Route::get('/audit', [\App\Http\Controllers\AuditLogController::class, 'index'])->name('audit.index');
        });
    }); // END MODULO: CARNETIZACIÓN

    // --------------------------------------------------------------------------
    // MODULO: CALL CENTER
    // Prefix: /callcenter | Name: callcenter.*
    // --------------------------------------------------------------------------
        Route::middleware(['can:access_callcenter', 'app_access:callcenter'])->prefix('callcenter')->name('callcenter.')->group(function () {
            Route::get('/dashboard', [App\Http\Controllers\Admin\CallCenterController::class, 'dashboard'])->name('dashboard');
            Route::get('/worklist', [App\Http\Controllers\Admin\CallCenterController::class, 'worklist'])->name('worklist');
            Route::get('/history/{afiliado}', [App\Http\Controllers\Admin\CallCenterController::class, 'getHistory'])->name('history');
            Route::get('/assign', [App\Http\Controllers\Admin\CallCenterController::class, 'assignList'])->name('assign')->middleware('can:assign_calls');
            Route::post('/assign', [App\Http\Controllers\Admin\CallCenterController::class, 'assignStore'])->name('assign.store')->middleware('can:assign_calls');
            Route::get('/reception', [App\Http\Controllers\Admin\CallCenterController::class, 'reception'])->name('reception');
            Route::get('/reception/template', [App\Http\Controllers\Admin\CallCenterController::class, 'downloadTemplate'])->name('reception.template');
            Route::post('/reception/check', [App\Http\Controllers\Admin\CallCenterController::class, 'checkProspects'])->name('reception.check');
            Route::post('/reception/store', [App\Http\Controllers\Admin\CallCenterController::class, 'storeProspects'])->name('reception.store');
            Route::get('/prospecting', [App\Http\Controllers\Admin\CallCenterController::class, 'prospectingWorklist'])->name('prospecting');
            Route::post('/prospecting/promote/{afiliado}', [App\Http\Controllers\Admin\CallCenterController::class, 'promoteProspect'])->name('prospecting.promote');
            Route::post('/calls/bulk', [App\Http\Controllers\Admin\CallCenterController::class, 'storeBulkCalls'])->name('calls.bulk');
            Route::post('/calls/{afiliado}', [App\Http\Controllers\Admin\CallCenterController::class, 'storeCall'])->name('calls.store');
            Route::get('/management', [App\Http\Controllers\Admin\CallCenterController::class, 'managementTray'])->name('management');
            Route::post('/documents/{afiliado}/status', [App\Http\Controllers\Admin\CallCenterController::class, 'updateDocumentStatus'])->name('documents.status');
        });

    // --------------------------------------------------------------------------
    // MODULO: LOGÍSTICA & DESPACHO
    // Prefix: /logistica | Name: logistica.* (for custom routes)
    // --------------------------------------------------------------------------
    Route::middleware('can:manage_logistics')->group(function() {
            Route::get('logistica/dashboard', [\App\Http\Controllers\LogisticaDashboardController::class, 'index'])->name('logistica.dashboard');
            Route::resource('mensajeros', \App\Http\Controllers\MensajeroController::class);
            Route::resource('rutas', \App\Http\Controllers\RutaController::class);
            Route::get('despachos/crear', [\App\Http\Controllers\DespachoController::class, 'createBatch'])->name('despachos.create_batch');
            Route::post('despachos/proceso', [\App\Http\Controllers\DespachoController::class, 'processBatch'])->name('despachos.process_batch');
            Route::get('despachos/{despacho}/print', [\App\Http\Controllers\DespachoController::class, 'print'])->name('despachos.print');
            Route::post('despachos/{despacho}/status', [\App\Http\Controllers\DespachoController::class, 'updateStatus'])->name('despachos.update_status');
            Route::post('despachos/item/{item}/status', [\App\Http\Controllers\DespachoController::class, 'updateItemStatus'])->name('despachos.item_status');
            Route::resource('despachos', \App\Http\Controllers\DespachoController::class);
            Route::get('lotes', [\App\Http\Controllers\LoteController::class, 'index'])->name('lotes.index');
            Route::post('lotes/proceso', [\App\Http\Controllers\LoteController::class, 'process'])->name('lotes.process');
        });

    // --------------------------------------------------------------------------
    // MODULO: EVIDENCIAS
    // Prefix: /evidencias | Name: evidencias.*
    // --------------------------------------------------------------------------
    Route::middleware('can:manage_evidencias')->group(function() {
            Route::get('evidencias', [\App\Http\Controllers\EvidenciaController::class, 'index'])->name('evidencias.index');
            Route::post('evidencias/{evidencia}/status', [\App\Http\Controllers\EvidenciaController::class, 'updateStatus'])->name('evidencias.status');
            Route::post('evidencias/physical', [\App\Http\Controllers\EvidenciaController::class, 'validatePhysical'])->name('evidencias.physical');
        });

    // --------------------------------------------------------------------------
    // MODULO: CIERRE
    // Prefix: /cierre | Name: cierre.*
    // --------------------------------------------------------------------------
    Route::middleware('can:manage_closures')->group(function() {
            Route::get('cierre', [\App\Http\Controllers\CierreController::class, 'index'])->name('cierre.index');
            Route::post('cierre', [\App\Http\Controllers\CierreController::class, 'store'])->name('cierre.store');
        });

    // --------------------------------------------------------------------------
    // MODULO: LIQUIDACIÓN
    // Prefix: /liquidacion | Name: liquidacion.*
    // --------------------------------------------------------------------------
    Route::middleware('can:manage_liquidations')->group(function() {
            Route::get('liquidacion', \App\Http\Controllers\LiquidacionController::class)->name('liquidacion.index');
            Route::post('liquidacion/proceso', [\App\Http\Controllers\LiquidacionController::class, 'process'])->name('liquidacion.process');
            Route::get('liquidacion/historial', [\App\Http\Controllers\LiquidacionController::class, 'history'])->name('liquidacion.history');
            Route::get('liquidacion/print/{recibo}', [\App\Http\Controllers\LiquidacionController::class, 'print'])->name('liquidacion.print');
        });

    // --------------------------------------------------------------------------
    // MODULO: TRASPASOS
    // Prefix: /traspasos | Name: traspasos.*
    // --------------------------------------------------------------------------
    Route::prefix('traspasos')->name('traspasos.')->middleware('app_access:traspasos')->group(function () {
        Route::get('/', [\App\Http\Controllers\Modules\Traspasos\TraspasoController::class, 'index'])->name('index');
        Route::get('/dashboard', [\App\Http\Controllers\Modules\Traspasos\TraspasoController::class, 'dashboard'])->name('dashboard');
        Route::get('/exportar', [\App\Http\Controllers\Modules\Traspasos\TraspasoController::class, 'export'])->name('export');
        Route::get('/historial/{traspaso}', [\App\Http\Controllers\Modules\Traspasos\TraspasoController::class, 'history'])->name('history');
        Route::get('/{traspaso}/editar', [\App\Http\Controllers\Modules\Traspasos\TraspasoController::class, 'edit'])->name('edit');
        Route::put('/{traspaso}', [\App\Http\Controllers\Modules\Traspasos\TraspasoController::class, 'update'])->name('update');
        Route::get('/importar', [\App\Http\Controllers\Modules\Traspasos\TraspasoController::class, 'importView'])->name('import');
        Route::post('/importar', [\App\Http\Controllers\Modules\Traspasos\TraspasoController::class, 'import'])->name('import.store');
        Route::get('/efectividad-masiva', [\App\Http\Controllers\Modules\Traspasos\TraspasoController::class, 'bulkEffectiveView'])->name('bulk.effective');
        Route::post('/efectividad-masiva', [\App\Http\Controllers\Modules\Traspasos\TraspasoController::class, 'processBulkEffective'])->name('bulk.effective.store');
        Route::post('/{traspaso}/emitir-carnet', [\App\Http\Controllers\Modules\Traspasos\TraspasoController::class, 'emitirCarnet'])->name('emitir-carnet');
        Route::post('/{traspaso}/rechazar', [\App\Http\Controllers\Modules\Traspasos\TraspasoController::class, 'rechazar'])->name('rechazar');
        Route::patch('/{traspaso}/enriquecer', [\App\Http\Controllers\Modules\Traspasos\TraspasoController::class, 'updateEnrichment'])->name('enrich');
        Route::patch('/{traspaso}/verificar', [\App\Http\Controllers\Modules\Traspasos\TraspasoController::class, 'verificar'])->name('verificar');
        Route::get('/{traspaso}/historial', [\App\Http\Controllers\Modules\Traspasos\TraspasoController::class, 'history'])->name('history');
        Route::get('/sincronizacion-unipago', [\App\Http\Controllers\Modules\Traspasos\TraspasoController::class, 'syncUnipagoView'])->name('sync.unipago');
        Route::post('/sincronizacion-unipago', [\App\Http\Controllers\Modules\Traspasos\TraspasoController::class, 'processSyncUnipago'])->name('sync.unipago.store');

        // Administración
        Route::middleware('can:configurar_traspasos')->group(function() {
            // Gestión de usuarios centralizada
            Route::get('usuarios', function() { return redirect()->route('admin.access.users'); })->name('usuarios.index');
            Route::post('usuarios/{user}/impersonate', function($user) { return redirect()->route('admin.access.impersonate', $user); })->name('usuarios.impersonate');
            Route::resource('departamentos', App\Http\Controllers\DepartamentoController::class);
            Route::prefix('configuracion')->name('config.')->group(function () {
                Route::get('/agentes', [\App\Http\Controllers\Modules\Traspasos\AgenteController::class, 'index'])->name('agentes');
                Route::post('/supervisores', [\App\Http\Controllers\Modules\Traspasos\AgenteController::class, 'storeSupervisor'])->name('supervisores.store');
                Route::post('/agentes', [\App\Http\Controllers\Modules\Traspasos\AgenteController::class, 'storeAgente'])->name('agentes.store');
                Route::post('/metas', [\App\Http\Controllers\Modules\Traspasos\AgenteController::class, 'storeMeta'])->name('metas.store');
                Route::post('/supervisores/{supervisor}/toggle', [\App\Http\Controllers\Modules\Traspasos\AgenteController::class, 'toggleSupervisor'])->name('supervisores.toggle');
                Route::post('/agentes/{agente}/toggle', [\App\Http\Controllers\Modules\Traspasos\AgenteController::class, 'toggleAgente'])->name('agentes.toggle');

                // Motivos de Rechazo
                Route::get('/motivos', [\App\Http\Controllers\Modules\Traspasos\MotivoRechazoController::class, 'index'])->name('motivos.index');
                Route::post('/motivos', [\App\Http\Controllers\Modules\Traspasos\MotivoRechazoController::class, 'store'])->name('motivos.store');
                Route::patch('/motivos/{motivo}', [\App\Http\Controllers\Modules\Traspasos\MotivoRechazoController::class, 'update'])->name('motivos.update');
                Route::patch('/motivos/{motivo}/toggle', [\App\Http\Controllers\Modules\Traspasos\MotivoRechazoController::class, 'toggle'])->name('motivos.toggle');
                Route::delete('/motivos/{motivo}', [\App\Http\Controllers\Modules\Traspasos\MotivoRechazoController::class, 'destroy'])->name('motivos.destroy');

                // Procesos de Afiliación (Requisitos)
                Route::get('/procesos-afiliacion', [\App\Http\Controllers\Modules\Afiliacion\ConfiguracionController::class, 'index'])->name('afiliacion-procesos.index');
                Route::post('/procesos-afiliacion/tipos', [\App\Http\Controllers\Modules\Afiliacion\ConfiguracionController::class, 'storeTipo'])->name('afiliacion-procesos.store-tipo');
                Route::post('/procesos-afiliacion/documentos', [\App\Http\Controllers\Modules\Afiliacion\ConfiguracionController::class, 'storeDocumento'])->name('afiliacion-procesos.store-doc');
                Route::delete('/procesos-afiliacion/documentos/{documento}', [\App\Http\Controllers\Modules\Afiliacion\ConfiguracionController::class, 'deleteDocumento'])->name('afiliacion-procesos.delete-doc');
            });
        });
        
        // Reportes
        Route::get('/reportes', [\App\Http\Controllers\ReporteController::class, 'index'])->name('reports');
    });

    // --------------------------------------------------------------------------
    // MODULO: AFILIACIÓN
    // Prefix: /solicitudes-afiliacion | Name: afiliacion.*
    // --------------------------------------------------------------------------
    Route::prefix('solicitudes-afiliacion')->name('afiliacion.')->middleware('app_access:afiliacion')->group(function () {
        Route::get('/', [\App\Http\Controllers\Modules\Afiliacion\SolicitudController::class, 'index'])->name('index');
        Route::get('/search-afiliado', [\App\Http\Controllers\Modules\Afiliacion\SolicitudController::class, 'searchAfiliado'])->name('search-afiliado');
        Route::get('/check-stats', [\App\Http\Controllers\Modules\Afiliacion\SolicitudController::class, 'checkStats'])->name('check-stats');
        Route::get('/crear', [\App\Http\Controllers\Modules\Afiliacion\SolicitudController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Modules\Afiliacion\SolicitudController::class, 'store'])->name('store');
        
        Route::middleware('can:solicitudes_afiliacion.configurar')->group(function() {
            Route::get('/configuracion', [\App\Http\Controllers\Modules\Afiliacion\ConfiguracionController::class, 'index'])->name('config');
            Route::post('/configuracion/tipos', [\App\Http\Controllers\Modules\Afiliacion\ConfiguracionController::class, 'storeTipo'])->name('config.tipos.store');
            Route::patch('/configuracion/tipos/{tipo}', [\App\Http\Controllers\Modules\Afiliacion\ConfiguracionController::class, 'updateTipo'])->name('config.tipos.update');
            Route::post('/configuracion/documentos', [\App\Http\Controllers\Modules\Afiliacion\ConfiguracionController::class, 'storeDocumento'])->name('config.documentos.store');
            Route::delete('/configuracion/documentos/{documento}', [\App\Http\Controllers\Modules\Afiliacion\ConfiguracionController::class, 'deleteDocumento'])->name('config.documentos.delete');
            // Gestión de usuarios centralizada
            Route::get('usuarios', function() { return redirect()->route('admin.access.users'); })->name('usuarios.index');
            Route::post('usuarios/{user}/impersonate', function($user) { return redirect()->route('admin.access.impersonate', $user); })->name('usuarios.impersonate');
            Route::resource('departamentos', App\Http\Controllers\DepartamentoController::class);
        });

        Route::get('/reportes', [\App\Http\Controllers\Modules\Afiliacion\ReporteController::class, 'index'])->name('reports');
        Route::get('/carga-trabajo', [\App\Http\Controllers\Modules\Afiliacion\ReporteController::class, 'workload'])->name('workload');
        Route::post('/bulk-assign', [\App\Http\Controllers\Modules\Afiliacion\SolicitudController::class, 'bulkAssign'])->name('bulk-assign');
        Route::get('/{solicitud}', [\App\Http\Controllers\Modules\Afiliacion\SolicitudController::class, 'show'])->name('show');
        Route::get('/{solicitud}/editar', [\App\Http\Controllers\Modules\Afiliacion\SolicitudController::class, 'edit'])->name('edit');
        Route::patch('/{solicitud}', [\App\Http\Controllers\Modules\Afiliacion\SolicitudController::class, 'update'])->name('update');
        Route::post('/{solicitud}/asignar', [\App\Http\Controllers\Modules\Afiliacion\SolicitudController::class, 'assign'])->name('assign');
        Route::post('/{solicitud}/aprobar', [\App\Http\Controllers\Modules\Afiliacion\SolicitudController::class, 'approve'])->name('approve');
        Route::post('/{solicitud}/completar', [\App\Http\Controllers\Modules\Afiliacion\SolicitudController::class, 'complete'])->name('complete');
        Route::post('/{solicitud}/rechazar', [\App\Http\Controllers\Modules\Afiliacion\SolicitudController::class, 'reject'])->name('reject');
        Route::post('/{solicitud}/devolver', [\App\Http\Controllers\Modules\Afiliacion\SolicitudController::class, 'return'])->name('return');
        Route::middleware('can:solicitudes_afiliacion.escalar')->post('/{solicitud}/escalar', [\App\Http\Controllers\Modules\Afiliacion\SolicitudController::class, 'escalate'])->name('escalate');
        Route::get('/documentos/{documento}', [\App\Http\Controllers\Modules\Afiliacion\SolicitudController::class, 'viewDocumento'])->name('documentos.view');
        Route::post('/{solicitud}/documentos/{documento}/validar', [\App\Http\Controllers\Modules\Afiliacion\SolicitudController::class, 'validateDocument'])->name('documentos.validate');
    });

    // --------------------------------------------------------------------------
    // MODULO: INTRANET
    // Prefix: /intranet | Name: intranet.*
    // --------------------------------------------------------------------------
    Route::prefix('intranet')->name('intranet.')->middleware('app_access:intranet')->group(function () {
        Route::get('/dashboard', function() { return view('intranet.index'); })->name('dashboard');
        Route::get('/catalogo', function() { 
            return view('catalogo.index', [
                'corteCount' => \App\Models\Corte::count(),
                'responsableCount' => \App\Models\Responsable::count(),
                'proveedorCount' => \App\Models\Proveedor::count(),
                'estadoCount' => \App\Models\Estado::count(),
                'empresaCount' => \App\Models\Empresa::count(),
                'userCount' => \App\Models\User::count(),
            ]); 
        })->name('catalogo.index');
        Route::post('/catalogo/sync-firebase', function() {
            \App\Jobs\FirebaseSyncJob::dispatch(['--full' => true]);
            return back()->with('success', 'Sincronización iniciada.');
        })->name('catalogo.sync_firebase');
    });

    // --------------------------------------------------------------------------
    // MODULO: SISTEMA
    // Prefix: /sistema | Name: sistema.*
    // --------------------------------------------------------------------------
    Route::middleware('can:access_admin_panel')->prefix('sistema')->name('sistema.')->group(function() {
        Route::resource('departamentos', App\Http\Controllers\DepartamentoController::class);
        // Gestión de usuarios centralizada
        Route::get('usuarios', function() { return redirect()->route('admin.access.users'); })->name('usuarios.index');
        Route::post('usuarios/{user}/impersonate', function($user) { return redirect()->route('admin.access.impersonate', $user); })->name('usuarios.impersonate');
        
        Route::resource('cortes', \App\Http\Controllers\CorteController::class);
        Route::resource('responsables', \App\Http\Controllers\ResponsableController::class);
        Route::resource('estados', \App\Http\Controllers\EstadoController::class);
        Route::resource('proveedores', \App\Http\Controllers\ProveedorController::class);
        
        // Backups
        Route::get('backups', [\App\Http\Controllers\BackupController::class, 'index'])->name('backups.index');
        Route::post('backups/settings', [\App\Http\Controllers\BackupController::class, 'saveSettings'])->name('backups.settings');
        Route::post('backups/create', [\App\Http\Controllers\BackupController::class, 'create'])->name('backups.create');
        Route::get('backups/{name}/download', [\App\Http\Controllers\BackupController::class, 'download'])->name('backups.download');
        Route::delete('backups/{name}', [\App\Http\Controllers\BackupController::class, 'destroy'])->name('backups.destroy');

        Route::middleware('can:manage_companies')->group(function() {
            Route::get('empresas/enrich', [\App\Http\Controllers\EmpresaController::class, 'enrich'])->name('empresas.enrich');
            Route::post('empresas/enrich', [\App\Http\Controllers\EmpresaController::class, 'processEnrich'])->name('empresas.processEnrich');
            Route::post('empresas/sync-firebase', [\App\Http\Controllers\EmpresaController::class, 'syncFirebase'])->name('empresas.sync_firebase');
            Route::resource('empresas', \App\Http\Controllers\EmpresaController::class)->whereUuid('empresa');
            Route::post('empresas/{empresa}/interaccion', [\App\Http\Controllers\EmpresaController::class, 'storeInteraction'])->name('empresas.interaction')->whereUuid('empresa');
        });

        Route::get('roles', function() { return redirect()->route('admin.access.roles'); })->name('roles.index');
    });

    // --------------------------------------------------------------------------
    // MODULO: REPORTES GLOBALES
    // Prefix: /reportes | Name: reportes.*
    // --------------------------------------------------------------------------
    Route::middleware(['can:view_reports', 'app_access:reportes'])->prefix('reportes')->name('reportes.')->group(function() {
        Route::get('/', [\App\Http\Controllers\ReporteController::class, 'index'])->name('index');
        Route::get('executive-suite', [\App\Http\Controllers\ExecutiveSuiteController::class, 'index'])->name('executive.suite');
        Route::get('ejecutivo', [\App\Http\Controllers\ExecutiveDashboardController::class, 'index'])->name('executive');
        Route::get('export-center', [\App\Http\Controllers\ReporteController::class, 'exportCenter'])->name('export_center');
        Route::get('resumen', [\App\Http\Controllers\ReporteController::class, 'resumenTable'])->name('resumen');
        Route::get('supervision', [\App\Http\Controllers\ReporteController::class, 'supervision'])->name('supervision');
        Route::get('export', [\App\Http\Controllers\ReporteController::class, 'export'])->name('export');
        Route::get('heatmap', [\App\Http\Controllers\ReporteController::class, 'heatmap'])->name('heatmap');
        Route::get('alertas-sla', [\App\Http\Controllers\ReporteController::class, 'slaAlerts'])->name('sla_alerts');
        Route::get('produccion-traspasos', [\App\Http\Controllers\ReporteController::class, 'produccionTraspasos'])->name('produccion_traspasos');
        Route::get('produccion-traspasos/export', [\App\Http\Controllers\ReporteController::class, 'exportProduccionTraspasos'])->name('produccion_traspasos.export');
        Route::get('produccion-traspasos/export-pdf', [\App\Http\Controllers\ReporteController::class, 'exportProduccionTraspasosPdf'])->name('produccion_traspasos.export_pdf');
        Route::get('comparativa', [\App\Http\Controllers\ReporteController::class, 'comparison'])->name('comparativa');
        Route::get('pendientes', [\App\Http\Controllers\ReporteController::class, 'pendientes'])->name('pendientes');
        Route::get('pendientes/export', [\App\Http\Controllers\ReporteController::class, 'exportPendientes'])->name('pendientes.export');
    });

    // API & UTILS
    Route::get('api/queue-status', function() {
        $jobsCount = \DB::table('jobs')->count();
        
        // Buscar importaciones activas en los últimos 30 minutos (basado en caché)
        $activeImports = [];
        $runningLotes = \App\Models\Lote::where('created_at', '>=', now()->subMinutes(60))
            ->orderBy('id', 'desc')
            ->limit(3)
            ->get();

        foreach($runningLotes as $lote) {
            $status = \Illuminate\Support\Facades\Cache::get("import_status_{$lote->id}");
            if ($status === 'running' || $status === 'pending') {
                $progress = \Illuminate\Support\Facades\Cache::get("import_progress_{$lote->id}", 0);
                $total = \Illuminate\Support\Facades\Cache::get("import_total_{$lote->id}", 0);
                $activeImports[] = [
                    'id' => $lote->id,
                    'nombre' => $lote->nombre,
                    'progress' => ($total > 0) ? round(($progress / $total) * 100) : 0,
                    'current' => $progress,
                    'total' => $total,
                    'logs' => array_reverse(\Illuminate\Support\Facades\Cache::get("import_logs_{$lote->id}", []))
                ];
            }
        }

        return response()->json([
            'count' => $jobsCount,
            'active_imports' => $activeImports
        ]);
    })->name('api.queue_status');

    Route::post('notifications/mark-all-read', function() {
        \Illuminate\Support\Facades\Auth::user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.markAllAsRead');

    // --- Módulo de Call Center V2 ---
    Route::prefix('call-center')->name('call-center.')->group(function () {
        Route::get('/', [App\Http\Controllers\Modules\CallCenter\CallCenterController::class, 'index'])->name('index');
        Route::get('/importar', [App\Http\Controllers\Modules\CallCenter\CallCenterController::class, 'create'])->name('import');
        Route::post('/importar/iniciar', [App\Http\Controllers\Modules\CallCenter\CallCenterController::class, 'startBatch'])->name('import.start');
        Route::post('/importar/procesar-chunk', [App\Http\Controllers\Modules\CallCenter\CallCenterController::class, 'processChunk'])->name('import.chunk');
        Route::post('/importar', [App\Http\Controllers\Modules\CallCenter\CallCenterController::class, 'importData'])->name('import.store');
        Route::get('/bandeja', [App\Http\Controllers\Modules\CallCenter\CallCenterController::class, 'worklist'])->name('worklist');
        Route::get('/gestionar/{registro:uuid}', [App\Http\Controllers\Modules\CallCenter\CallCenterController::class, 'manage'])->name('manage');
        Route::post('/gestionar/{registro:uuid}/llamada', [App\Http\Controllers\Modules\CallCenter\CallCenterController::class, 'storeGestion'])->name('gestion.store');
        Route::post('/gestionar/{registro:uuid}/documento', [App\Http\Controllers\Modules\CallCenter\CallCenterController::class, 'updateDocument'])->name('document.update');
        Route::post('/gestionar/{registro:uuid}/promover', [App\Http\Controllers\Modules\CallCenter\CallCenterController::class, 'promoteToCarnet'])->name('promote');
        Route::get('/estadisticas', [App\Http\Controllers\Modules\CallCenter\CallCenterController::class, 'stats'])->name('stats');
    });

    // --- Módulo de Asistencia ---
    Route::prefix('asistencia')->name('asistencia.')->group(function () {
        // Rutas del Empleado
        Route::middleware(['can:asistencia.marcar'])->group(function () {
            Route::get('/', [App\Http\Controllers\Modules\Asistencia\AsistenciaController::class, 'index'])->name('index');
            Route::post('/marcar', [App\Http\Controllers\Modules\Asistencia\AsistenciaController::class, 'marcar'])->name('marcar');
            Route::get('/historial', [App\Http\Controllers\Modules\Asistencia\AsistenciaController::class, 'historial'])->name('historial');
            Route::post('/justificar/{registro}', [App\Http\Controllers\Modules\Asistencia\AsistenciaController::class, 'justificar'])->name('justificar');
            
            Route::prefix('permisos')->name('permisos.')->group(function () {
                Route::get('/', [App\Http\Controllers\Modules\Asistencia\PermisoController::class, 'index'])->name('index');
                Route::post('/', [App\Http\Controllers\Modules\Asistencia\PermisoController::class, 'store'])->name('store');
            });
        });

        // Rutas del Supervisor / Admin
        Route::middleware(['can:asistencia.ver_dashboard'])->group(function () {
            Route::get('/dashboard', [App\Http\Controllers\Modules\Asistencia\AsistenciaController::class, 'dashboard'])->name('dashboard');
            
            Route::prefix('permisos')->name('permisos.')->group(function () {
                Route::get('/bandeja', [App\Http\Controllers\Modules\Asistencia\PermisoController::class, 'bandeja'])->name('bandeja');
                Route::post('/{permiso}/decidir', [App\Http\Controllers\Modules\Asistencia\PermisoController::class, 'decidir'])->name('decidir');
            });

            Route::prefix('reportes')->name('reportes.')->group(function () {
                Route::get('/', [App\Http\Controllers\Modules\Asistencia\ReporteAsistenciaController::class, 'index'])->name('index');
                Route::get('/export', [App\Http\Controllers\Modules\Asistencia\ReporteAsistenciaController::class, 'export'])->name('export');
            });

            Route::prefix('configuracion')->name('configuracion.')->group(function () {
                Route::get('/', [App\Http\Controllers\Modules\Asistencia\ConfiguracionController::class, 'index'])->name('index');
                Route::post('/turno', [App\Http\Controllers\Modules\Asistencia\ConfiguracionController::class, 'saveTurno'])->name('save_turno');
                Route::post('/asignar-turno', [App\Http\Controllers\Modules\Asistencia\ConfiguracionController::class, 'asignarTurno'])->name('asignar_turno');
                Route::post('/global', [App\Http\Controllers\Modules\Asistencia\ConfiguracionController::class, 'updateGlobal'])->name('global');
            });
        });
    });

    // --- Administración y Actualizaciones ---
    Route::middleware(['auth', 'can:access_update_manager'])->prefix('admin/updates')->name('admin.updates.')->group(function () {
        Route::get('/', [App\Http\Controllers\Modules\Admin\UpdateManagerController::class, 'index'])->name('index');
        Route::get('/health', [App\Http\Controllers\Modules\Admin\HealthMonitorController::class, 'getSystemStatus'])->name('health');
        Route::post('/backup', [App\Http\Controllers\Modules\Admin\UpdateManagerController::class, 'createBackup'])->name('backup');
        Route::post('/rollback/{backup}', [App\Http\Controllers\Modules\Admin\UpdateManagerController::class, 'rollback'])->name('rollback');
        Route::post('/upload', [App\Http\Controllers\Modules\Admin\UpdateManagerController::class, 'uploadUpdate'])->name('upload');
        Route::post('/apply', [App\Http\Controllers\Modules\Admin\UpdateManagerController::class, 'applyUpdate'])->name('apply');
        Route::post('/pack', [App\Http\Controllers\Modules\Admin\UpdateManagerController::class, 'pack'])->name('pack');
        Route::get('/download-release/{filename}', [App\Http\Controllers\Modules\Admin\UpdateManagerController::class, 'downloadRelease'])->name('download_release');
        Route::get('/logs', [App\Http\Controllers\Modules\Admin\UpdateManagerController::class, 'getLogs'])->name('logs');
        Route::post('/purge', [App\Http\Controllers\Modules\Admin\UpdateManagerController::class, 'purgeSystem'])->name('purge');
    });

    // --- Control de Dispersión y Bajas ---
    Route::middleware(['auth', 'can:manage_dispersion'])->prefix('admin/dispersion')->name('dispersion.')->group(function () {
        Route::get('/', [DispersionController::class, 'index'])->name('index');
        Route::get('/history', [DispersionController::class, 'history'])->name('history');
        Route::get('/reports', [DispersionController::class, 'reports'])->name('reports');
        Route::get('/reports/{period}', [DispersionController::class, 'showReport'])->name('reports.show');
        Route::get('/report/{period}/download', [DispersionController::class, 'downloadReport'])->name('report');
        Route::get('/config', [DispersionController::class, 'config'])->name('config');
        
        Route::post('/periodos', [DispersionController::class, 'storePeriod'])->name('periods.store');
        Route::get('/periodos/{period}', [DispersionController::class, 'show'])->name('show');
        Route::get('/periodos/{period}/report', [DispersionController::class, 'downloadReport'])->name('report');
    });

    // --------------------------------------------------------------------------
    // MODULO: PROGRAMA PYP (PROMOCIÓN Y PREVENCIÓN)
    // Prefix: /pyp | Name: pyp.*
    // --------------------------------------------------------------------------
    Route::prefix('pyp')->name('pyp.')->middleware(['auth', 'app_access:pyp'])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Modules\Pyp\DashboardController::class, 'index'])->name('dashboard');
        
        // Gestión de Afiliados (Buscador y Ficha)
        Route::get('/afiliados', [\App\Http\Controllers\Modules\Pyp\AfiliadoController::class, 'index'])->name('afiliados.index');
        Route::get('/afiliados/{afiliado:uuid}', [\App\Http\Controllers\Modules\Pyp\AfiliadoController::class, 'show'])->name('afiliados.show');
        
        // Evaluaciones Médicas
        Route::get('/afiliados/{afiliado:uuid}/evaluar', [\App\Http\Controllers\Modules\Pyp\EvaluacionController::class, 'create'])->name('evaluaciones.create');
        Route::post('/afiliados/{afiliado:uuid}/evaluar', [\App\Http\Controllers\Modules\Pyp\EvaluacionController::class, 'store'])->name('evaluaciones.store');
        
        // Seguimientos CRM
        Route::post('/afiliados/{afiliado:uuid}/seguimiento', [\App\Http\Controllers\Modules\Pyp\SeguimientoController::class, 'store'])->name('seguimientos.store');

        // Matriculación (Crear Afiliado desde PyP)
        Route::get('/afiliados/crear/nuevo', [\App\Http\Controllers\Modules\Pyp\AfiliadoController::class, 'create'])->name('afiliados.create');
        Route::post('/afiliados/crear/nuevo', [\App\Http\Controllers\Modules\Pyp\AfiliadoController::class, 'store'])->name('afiliados.store');
        
        Route::get('/programas', [\App\Http\Controllers\Modules\Pyp\DashboardController::class, 'index'])->name('programas.index'); // Placeholder
    });

    // --------------------------------------------------------------------------
    // MODULO: RED DE PRESTADORES (PSS)
    // Prefix: /pss | Name: pss.*
    // --------------------------------------------------------------------------
    Route::prefix('pss')->name('pss.')->middleware(['auth'])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\PssManagementController::class, 'index'])->name('dashboard');
        
        // Médicos
        Route::get('/medicos', [\App\Http\Controllers\PssManagementController::class, 'medicos'])->name('medicos.index');
        Route::get('/medicos/crear', [\App\Http\Controllers\PssManagementController::class, 'medicos'])->name('medicos.create');
        
        // Centros
        Route::get('/centros', [\App\Http\Controllers\PssManagementController::class, 'centros'])->name('centros.index');
        
        // Catálogos
        Route::get('/catalogos', [\App\Http\Controllers\PssManagementController::class, 'catalogos'])->name('catalogos.index');
        
        // Importación
        Route::get('/importar', [\App\Http\Controllers\PssManagementController::class, 'import'])->name('import.index');
    });

    // --- DOCUMENTACIÓN Y MANUALES ---
    Route::get('admin/docs/afiliacion/pdf', [App\Http\Controllers\Admin\DocumentationController::class, 'exportManualAfiliacion'])->name('admin.docs.afiliacion.pdf');
});


Route::post('firebase/webhook', [\App\Http\Controllers\Api\FirebaseWebhookController::class, 'handle'])->name('firebase.webhook');

require __DIR__.'/auth.php';
