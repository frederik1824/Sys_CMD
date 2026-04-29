<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


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

    // MODULO: TRASPASOS
    Route::prefix('traspasos')->name('traspasos.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Modules\Traspasos\TraspasoController::class, 'index'])->name('index');
        Route::get('/exportar', [\App\Http\Controllers\Modules\Traspasos\TraspasoController::class, 'export'])->name('export');
        Route::get('/importar', [\App\Http\Controllers\Modules\Traspasos\TraspasoController::class, 'importView'])->name('import');
        Route::post('/importar', [\App\Http\Controllers\Modules\Traspasos\TraspasoController::class, 'import'])->name('import.store');
        Route::get('/efectividad-masiva', [\App\Http\Controllers\Modules\Traspasos\TraspasoController::class, 'bulkEffectiveView'])->name('bulk.effective');
        Route::post('/efectividad-masiva', [\App\Http\Controllers\Modules\Traspasos\TraspasoController::class, 'processBulkEffective'])->name('bulk.effective.store');
        Route::post('/{traspaso}/emitir-carnet', [\App\Http\Controllers\Modules\Traspasos\TraspasoController::class, 'emitirCarnet'])->name('emitir-carnet');
        Route::patch('/{traspaso}/enriquecer', [\App\Http\Controllers\Modules\Traspasos\TraspasoController::class, 'updateEnrichment'])->name('enrich');

        // Administración de Traspasos (Restringido)
        Route::middleware('spatie_role:Admin|Supervisor de Traspasos')->group(function() {
            // Personal de Traspasos
            Route::resource('usuarios', \App\Http\Controllers\Modules\Traspasos\UserController::class);
            Route::post('usuarios/{user}/impersonate', [\App\Http\Controllers\Modules\Traspasos\UserController::class, 'impersonate'])->name('usuarios.impersonate');
            
            // Estructura (Contextual)
            Route::resource('departamentos', App\Http\Controllers\DepartamentoController::class);

            // Configuración de Agentes
            Route::prefix('configuracion')->name('config.')->group(function () {
                Route::get('/agentes', [\App\Http\Controllers\Modules\Traspasos\AgenteController::class, 'index'])->name('agentes');
                Route::post('/supervisores', [\App\Http\Controllers\Modules\Traspasos\AgenteController::class, 'storeSupervisor'])->name('supervisores.store');
                Route::post('/agentes', [\App\Http\Controllers\Modules\Traspasos\AgenteController::class, 'storeAgente'])->name('agentes.store');
                Route::post('/metas', [\App\Http\Controllers\Modules\Traspasos\AgenteController::class, 'storeMeta'])->name('metas.store');
                Route::post('/supervisores/{supervisor}/toggle', [\App\Http\Controllers\Modules\Traspasos\AgenteController::class, 'toggleSupervisor'])->name('supervisores.toggle');
                Route::post('/agentes/{agente}/toggle', [\App\Http\Controllers\Modules\Traspasos\AgenteController::class, 'toggleAgente'])->name('agentes.toggle');
            });
        });
    });

    // MODULO: SOLICITUDES DE AFILIACIÓN
    Route::prefix('solicitudes-afiliacion')->name('solicitudes-afiliacion.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Modules\Afiliacion\SolicitudController::class, 'index'])->name('index');
        Route::get('/search-afiliado', [\App\Http\Controllers\Modules\Afiliacion\SolicitudController::class, 'searchAfiliado'])->name('search-afiliado');
        Route::get('/check-stats', [\App\Http\Controllers\Modules\Afiliacion\SolicitudController::class, 'checkStats'])->name('check-stats');
        Route::get('/crear', [\App\Http\Controllers\Modules\Afiliacion\SolicitudController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Modules\Afiliacion\SolicitudController::class, 'store'])->name('store');
        // Administración de Afiliación (Restringido)
        Route::middleware('spatie_role:Admin|Supervisor de Afiliación')->group(function() {
            // Configuración
            Route::get('/configuracion', [\App\Http\Controllers\Modules\Afiliacion\ConfiguracionController::class, 'index'])->name('config');
            Route::post('/configuracion/tipos', [\App\Http\Controllers\Modules\Afiliacion\ConfiguracionController::class, 'storeTipo'])->name('config.tipos.store');
            Route::patch('/configuracion/tipos/{tipo}', [\App\Http\Controllers\Modules\Afiliacion\ConfiguracionController::class, 'updateTipo'])->name('config.tipos.update');
            Route::post('/configuracion/documentos', [\App\Http\Controllers\Modules\Afiliacion\ConfiguracionController::class, 'storeDocumento'])->name('config.documentos.store');
            Route::delete('/configuracion/documentos/{documento}', [\App\Http\Controllers\Modules\Afiliacion\ConfiguracionController::class, 'deleteDocumento'])->name('config.documentos.delete');

            // Personal de Afiliación
            Route::resource('usuarios', \App\Http\Controllers\Modules\Afiliacion\UserController::class);
            Route::post('usuarios/{user}/impersonate', [\App\Http\Controllers\Modules\Afiliacion\UserController::class, 'impersonate'])->name('usuarios.impersonate');

            // Estructura (Contextual)
            Route::resource('departamentos', App\Http\Controllers\DepartamentoController::class);
        });

        Route::get('/reportes', [\App\Http\Controllers\Modules\Afiliacion\ReporteController::class, 'index'])->name('reports');
        Route::get('/carga-trabajo', [\App\Http\Controllers\Modules\Afiliacion\ReporteController::class, 'workload'])->name('workload');

        Route::post('/bulk-assign', [\App\Http\Controllers\Modules\Afiliacion\SolicitudController::class, 'bulkAssign'])->name('bulk-assign');
        Route::get('/{solicitud}', [\App\Http\Controllers\Modules\Afiliacion\SolicitudController::class, 'show'])->name('show');
        Route::get('/{solicitud}/editar', [\App\Http\Controllers\Modules\Afiliacion\SolicitudController::class, 'edit'])->name('edit');
        Route::patch('/{solicitud}', [\App\Http\Controllers\Modules\Afiliacion\SolicitudController::class, 'update'])->name('update');
        
        // Acciones de Flujo
        Route::post('/{solicitud}/asignar', [\App\Http\Controllers\Modules\Afiliacion\SolicitudController::class, 'assign'])->name('assign');
        Route::post('/{solicitud}/aprobar', [\App\Http\Controllers\Modules\Afiliacion\SolicitudController::class, 'approve'])->name('approve');
        Route::post('/{solicitud}/rechazar', [\App\Http\Controllers\Modules\Afiliacion\SolicitudController::class, 'reject'])->name('reject');
        Route::post('/{solicitud}/devolver', [\App\Http\Controllers\Modules\Afiliacion\SolicitudController::class, 'return'])->name('return');
        Route::middleware('can:solicitudes_afiliacion.escalar')->post('/{solicitud}/escalar', [\App\Http\Controllers\Modules\Afiliacion\SolicitudController::class, 'escalate'])->name('escalate');
        
        // Documentos
        Route::get('/documentos/{documento}', [\App\Http\Controllers\Modules\Afiliacion\SolicitudController::class, 'viewDocumento'])->name('documentos.view');
        Route::post('/{solicitud}/documentos/{documento}/validar', [\App\Http\Controllers\Modules\Afiliacion\SolicitudController::class, 'validateDocument'])->name('documentos.validate');
    });

    // Módulos de Configuración (Restringido - Spatie Permission)
    Route::middleware('can:access_admin_panel')->group(function() {
        // Estructura (Global / CMD)
        Route::resource('departamentos', App\Http\Controllers\DepartamentoController::class);
        
        // Usuarios de CMD (Aislados)
        Route::resource('usuarios', \App\Http\Controllers\Modules\CMD\UserController::class);
        Route::post('usuarios/{user}/impersonate', [\App\Http\Controllers\Modules\CMD\UserController::class, 'impersonate'])->name('usuarios.impersonate');

        Route::get('catalogo', function() {
            return view('catalogo.index');
        })->name('catalogo.index');

        // Firebase Sync Center
        Route::prefix('firebase')->name('firebase.')->group(function() {
            Route::get('/sync-center', [App\Http\Controllers\FirebaseSyncController::class, 'index'])->name('sync_center');
            Route::post('/pull', [App\Http\Controllers\FirebaseSyncController::class, 'pull'])->name('sync_pull');
            Route::post('/push', [App\Http\Controllers\FirebaseSyncController::class, 'push'])->name('sync_push');
            
            // Control routes
            Route::post('/pause', [App\Http\Controllers\FirebaseSyncController::class, 'pause'])->name('sync_pause');
            Route::post('/resume', [App\Http\Controllers\FirebaseSyncController::class, 'resume'])->name('sync_resume');
            Route::post('/cancel', [App\Http\Controllers\FirebaseSyncController::class, 'cancel'])->name('sync_cancel');
            Route::get('/health-check', [App\Http\Controllers\FirebaseSyncController::class, 'healthCheck'])->name('health_check');
            Route::post('/reconcile', [App\Http\Controllers\FirebaseSyncController::class, 'reconcile'])->name('reconcile');
            Route::post('/cleanup-snapshots', [App\Http\Controllers\FirebaseSyncController::class, 'cleanupSnapshots'])->name('cleanup_snapshots');
            Route::post('/purge-cache', [App\Http\Controllers\FirebaseSyncController::class, 'purgeCache'])->name('purge_cache');
            Route::post('/purge-queue', [App\Http\Controllers\FirebaseSyncController::class, 'purgeQueue'])->name('purge_queue');
            Route::get('/progress', [App\Http\Controllers\FirebaseSyncController::class, 'progress'])->name('progress');
            
            // New Disaster Recovery & Comparison
            Route::get('/compare', [App\Http\Controllers\FirebaseSyncController::class, 'compare'])->name('compare');
            Route::get('/snapshots', [App\Http\Controllers\FirebaseSyncController::class, 'listSnapshots'])->name('list_snapshots');
            Route::post('/restore-snapshot', [App\Http\Controllers\FirebaseSyncController::class, 'restoreSnapshot'])->name('restore_snapshot');
        });

        Route::post('catalogo/sync-firebase', function() {
            \App\Jobs\FirebaseSyncJob::dispatch(['--full' => true]);
            return back()->with('success', 'Sincronización global iniciada en segundo plano.');
        })->name('catalogo.sync_firebase');
        
        Route::resource('cortes', \App\Http\Controllers\CorteController::class);
        Route::resource('responsables', \App\Http\Controllers\ResponsableController::class);
        Route::resource('estados', \App\Http\Controllers\EstadoController::class);
        Route::resource('proveedores', \App\Http\Controllers\ProveedorController::class);
        Route::get('auditoria', [\App\Http\Controllers\AuditLogController::class, 'index'])->name('admin.audit.index');
    });

    // Módulo de Empresas (Spatie Permission)
    Route::middleware('can:manage_companies')->group(function() {
        Route::get('empresas/enrich', [\App\Http\Controllers\EmpresaController::class, 'enrich'])->name('empresas.enrich');
        Route::post('empresas/enrich', [\App\Http\Controllers\EmpresaController::class, 'processEnrich'])->name('empresas.processEnrich');
        Route::post('empresas/sync-firebase', [\App\Http\Controllers\EmpresaController::class, 'syncFirebase'])->name('empresas.sync_firebase');
        Route::resource('empresas', \App\Http\Controllers\EmpresaController::class)->whereUuid('empresa');
        Route::post('empresas/{empresa}/interaccion', [\App\Http\Controllers\EmpresaController::class, 'storeInteraction'])->name('empresas.interaction')->whereUuid('empresa');
    });
    
    Route::get('municipios/{provincia_id}', [\App\Http\Controllers\EmpresaController::class, 'getMunicipios'])->name('api.municipios');

    // Módulo de Afiliados
    Route::middleware('can:manage_affiliates')->group(function() {
        // Búsqueda e Exportación de Afiliados (DEBEN ir antes del resource)
        Route::post('afiliados/sync-firebase', [\App\Http\Controllers\AfiliadoController::class, 'syncFirebase'])->name('afiliados.sync_firebase');
        Route::get('afiliados/sync-progress', [\App\Http\Controllers\AfiliadoController::class, 'getProgress'])->name('afiliados.sync_progress');
        Route::post('afiliados/{afiliado}/sync_single', [\App\Http\Controllers\AfiliadoController::class, 'syncSingle'])->name('afiliados.sync_single')->whereUuid('afiliado');
        Route::get('afiliados/check-duplicate', [\App\Http\Controllers\AfiliadoController::class, 'checkDuplicate'])->name('afiliados.check_duplicate');
        Route::get('afiliados/search-ajax', [\App\Http\Controllers\AfiliadoController::class, 'searchAjax'])->middleware('throttle:60,1')->name('afiliados.search_ajax');
        Route::get('afiliados/export', [\App\Http\Controllers\AfiliadoController::class, 'export'])->name('afiliados.export');
        Route::post('afiliados/sanitize', [\App\Http\Controllers\AfiliadoController::class, 'sanitizeAddresses'])->name('afiliados.sanitize');
        
        // Afiliados Segmentados
        Route::get('afiliados/mios', [\App\Http\Controllers\AfiliadoController::class, 'indexMios'])->name('afiliados.mios');
        Route::get('afiliados/cmd', [\App\Http\Controllers\AfiliadoController::class, 'indexCmd'])->name('afiliados.cmd');
        Route::get('afiliados/otros', [\App\Http\Controllers\AfiliadoController::class, 'indexOtros'])->name('afiliados.otros');
        Route::get('afiliados/salida-inmediata', [\App\Http\Controllers\AfiliadoController::class, 'indexSalidaInmediata'])->name('afiliados.salida_inmediata');

        // Procesos Bulk y CRUD
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
    });

    // Módulo de Evidencias
    Route::middleware('can:manage_evidencias')->group(function() {
        Route::get('evidencias', [\App\Http\Controllers\EvidenciaController::class, 'index'])->name('evidencias.index');
        Route::post('evidencias/{evidencia}/status', [\App\Http\Controllers\EvidenciaController::class, 'updateStatus'])->name('evidencias.status');
        Route::post('evidencias/physical', [\App\Http\Controllers\EvidenciaController::class, 'validatePhysical'])->name('evidencias.physical');
    });

    // Módulo de Lotes (Batch)
    Route::middleware('can:manage_logistics')->group(function() {
        Route::get('lotes', [\App\Http\Controllers\LoteController::class, 'index'])->name('lotes.index');
        Route::post('lotes/proceso', [\App\Http\Controllers\LoteController::class, 'process'])->name('lotes.process');
    });

    // Módulo de Cierre (Carga de Acuses Físicos)
    Route::middleware('can:manage_closures')->group(function() {
        Route::get('cierre', [\App\Http\Controllers\CierreController::class, 'index'])->name('cierre.index');
        Route::post('cierre', [\App\Http\Controllers\CierreController::class, 'store'])->name('cierre.store');
    });

    // Gestión de Roles (Admin y Admin)
    Route::middleware('can:manage_users')->group(function() {
        Route::resource('roles', \App\Http\Controllers\RoleController::class)->only(['index', 'edit', 'update']);
        
        // Auditoría
        Route::get('auditoria', [\App\Http\Controllers\AuditLogController::class, 'index'])->name('admin.audit.index');
    });

    Route::get('stop-impersonating', [\App\Http\Controllers\UserController::class, 'stopImpersonating'])->name('usuarios.stop_impersonating');

    // Módulo de Liquidación (Spatie Permission)
    Route::middleware('can:manage_liquidations')->group(function() {
        Route::get('liquidacion', \App\Http\Controllers\LiquidacionController::class)->name('liquidacion.index');
        Route::post('liquidacion/proceso', [\App\Http\Controllers\LiquidacionController::class, 'process'])->name('liquidacion.process');
        Route::get('liquidacion/historial', [\App\Http\Controllers\LiquidacionController::class, 'history'])->name('liquidacion.history');
        Route::get('liquidacion/print/{recibo}', [\App\Http\Controllers\LiquidacionController::class, 'print'])->name('liquidacion.print');
    });

    // Módulo de Reportes
    Route::middleware('can:view_reports')->group(function() {
        Route::get('reportes', [\App\Http\Controllers\ReporteController::class, 'index'])->name('reportes.index');
        Route::get('executive-suite', [\App\Http\Controllers\ExecutiveSuiteController::class, 'index'])->name('executive.suite');
        Route::get('reportes/ejecutivo', [\App\Http\Controllers\ExecutiveDashboardController::class, 'index'])->name('reportes.executive');
        Route::get('reportes/export-center', [\App\Http\Controllers\ReporteController::class, 'exportCenter'])->name('reportes.export_center');
        Route::get('reportes/resumen', [\App\Http\Controllers\ReporteController::class, 'resumenTable'])->name('reportes.resumen');
        Route::get('reportes/supervision', [\App\Http\Controllers\ReporteController::class, 'supervision'])->name('reportes.supervision');
        Route::get('reportes/export', [\App\Http\Controllers\ReporteController::class, 'export'])->name('reportes.export');
        Route::get('reportes/heatmap', [\App\Http\Controllers\ReporteController::class, 'heatmap'])->name('reportes.heatmap');
        Route::get('reportes/alertas-sla', [\App\Http\Controllers\ReporteController::class, 'slaAlerts'])->name('reportes.sla_alerts');
        Route::get('reportes/comparativa', [\App\Http\Controllers\ReporteController::class, 'comparison'])->name('reportes.comparativa');
        Route::get('reportes/pendientes', [\App\Http\Controllers\ReporteController::class, 'pendientes'])->name('reportes.pendientes');
        Route::get('reportes/pendientes/export', [\App\Http\Controllers\ReporteController::class, 'exportPendientes'])->name('reportes.pendientes.export');
    });

    // Modulo de Importación Masiva (Spatie Permission) - Vinculado a manage_affiliates
    Route::middleware('can:manage_affiliates')->group(function() {
        // Módulo de Auditoría
        Route::get('/audit', [\App\Http\Controllers\AuditLogController::class, 'index'])->name('admin.audit.index');

        Route::get('importar', [\App\Http\Controllers\ImportController::class, 'index'])->name('import.index');
        Route::post('importar', [\App\Http\Controllers\ImportController::class, 'store'])->name('import.store');
        Route::get('importar/progreso/{lote_id}', [\App\Http\Controllers\ImportController::class, 'getProgress'])->name('import.progress');
        Route::get('importar/plantilla', [\App\Http\Controllers\ImportController::class, 'downloadTemplate'])->name('import.template');
    });

    // Módulo de Call Center
    Route::middleware('can:access_callcenter')->prefix('callcenter')->name('callcenter.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Admin\CallCenterController::class, 'dashboard'])->name('dashboard');
        Route::get('/worklist', [App\Http\Controllers\Admin\CallCenterController::class, 'worklist'])->name('worklist');
        Route::get('/history/{afiliado}', [App\Http\Controllers\Admin\CallCenterController::class, 'getHistory'])->name('history');
        Route::get('/assign', [App\Http\Controllers\Admin\CallCenterController::class, 'assignList'])->name('assign')->middleware('can:assign_calls');
        Route::post('/assign', [App\Http\Controllers\Admin\CallCenterController::class, 'assignStore'])->name('assign.store')->middleware('can:assign_calls');
        
        // Bandeja de Prospección
        Route::get('/reception', [App\Http\Controllers\Admin\CallCenterController::class, 'reception'])->name('reception');
        Route::get('/reception/template', [App\Http\Controllers\Admin\CallCenterController::class, 'downloadTemplate'])->name('reception.template');
        Route::post('/reception/check', [App\Http\Controllers\Admin\CallCenterController::class, 'checkProspects'])->name('reception.check');
        Route::post('/reception/store', [App\Http\Controllers\Admin\CallCenterController::class, 'storeProspects'])->name('reception.store');
        Route::get('/prospecting', [App\Http\Controllers\Admin\CallCenterController::class, 'prospectingWorklist'])->name('prospecting');
        Route::post('/prospecting/promote/{afiliado}', [App\Http\Controllers\Admin\CallCenterController::class, 'promoteProspect'])->name('prospecting.promote');
        
        Route::post('/calls/bulk', [App\Http\Controllers\Admin\CallCenterController::class, 'storeBulkCalls'])->name('calls.bulk');
        Route::post('/calls/{afiliado}', [App\Http\Controllers\Admin\CallCenterController::class, 'storeCall'])->name('calls.store');
        
        // Gestión de Documentos (Bandeja de Salida)
        Route::get('/management', [App\Http\Controllers\Admin\CallCenterController::class, 'managementTray'])->name('management');
        Route::post('/documents/{afiliado}/status', [App\Http\Controllers\Admin\CallCenterController::class, 'updateDocumentStatus'])->name('documents.status');
    });

    // Notas de Afiliados
    Route::middleware('can:manage_affiliates')->group(function() {
        Route::post('notas', [\App\Http\Controllers\NotaController::class, 'store'])->name('notas.store');
    });

    // Notificaciones y Estado del Sistema (Detallado)
    Route::get('api/queue-status', function() {
        $activeLotes = \App\Models\Lote::whereHas('afiliados', function($q) {
                // Consideramos activos los que tengan registros pero el worker siga trabajando
            })
            ->where('total_registros', '>', 0)
            ->withCount('afiliados')
            ->get()
            ->filter(function($lote) {
                return $lote->afiliados_count < $lote->total_registros;
            })
            ->map(function($lote) {
                return [
                    'id' => $lote->id,
                    'nombre' => $lote->nombre,
                    'progress' => round(($lote->afiliados_count / $lote->total_registros) * 100),
                    'total' => $lote->total_registros,
                    'current' => $lote->afiliados_count,
                    'logs' => Cache::get("import_logs_{$lote->id}", [])
                ];
            })->values();

        return response()->json([
            'count' => DB::table('jobs')->count(),
            'active_imports' => $activeLotes
        ]);
    })->name('api.queue_status');

    Route::post('notifications/mark-all-read', function(Illuminate\Http\Request $request) {
        auth()->user()->unreadNotifications->markAsRead();
        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'Todas las notificaciones marcadas como leídas.');
    })->name('notifications.markAllAsRead');

    Route::delete('notifications/delete-all', function() {
        auth()->user()->notifications()->delete();
        return back()->with('success', 'Todas las notificaciones han sido eliminadas.');
    })->name('notifications.deleteAll');

    Route::delete('notifications/{id}', function($id) {
        auth()->user()->notifications()->where('id', $id)->delete();
        return back()->with('success', 'Notificación eliminada.');
    })->name('notifications.destroy');

    // Módulo de Logística & Despacho
    Route::middleware('can:manage_logistics')->group(function() {
        Route::get('logistica/dashboard', [\App\Http\Controllers\LogisticaDashboardController::class, 'index'])->name('logistica.dashboard');
        Route::resource('mensajeros', \App\Http\Controllers\MensajeroController::class);
        Route::resource('rutas', \App\Http\Controllers\RutaController::class);
        
        // Rutas específicas de Despacho antes del resource
        Route::get('despachos/crear', [\App\Http\Controllers\DespachoController::class, 'createBatch'])->name('despachos.create_batch');
        Route::post('despachos/proceso', [\App\Http\Controllers\DespachoController::class, 'processBatch'])->name('despachos.process_batch');
        Route::get('despachos/{despacho}/print', [\App\Http\Controllers\DespachoController::class, 'print'])->name('despachos.print');
        Route::post('despachos/{despacho}/status', [\App\Http\Controllers\DespachoController::class, 'updateStatus'])->name('despachos.update_status');
        Route::post('despachos/item/{item}/status', [\App\Http\Controllers\DespachoController::class, 'updateItemStatus'])->name('despachos.item_status');
        Route::resource('despachos', \App\Http\Controllers\DespachoController::class);
    });
});

Route::get('logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout.get');
Route::post('firebase/webhook', [\App\Http\Controllers\Api\FirebaseWebhookController::class, 'handle'])->name('firebase.webhook');

require __DIR__.'/auth.php';
