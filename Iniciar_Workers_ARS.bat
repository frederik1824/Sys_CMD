@echo off
TITLE ARS CMD - Motor Logistico y Procesamiento en Segundo Plano (Colas Asincronas)
COLOR 0B

echo =======================================================
echo          ARS CMD: SISTEMA DE COLAS ASINCRONAS          
echo =======================================================
echo.
echo Iniciando el motor de procesamiento en segundo plano...
echo Este daemon es equivalente a Laravel Horizon para entornos Windows.
echo Mantén esta ventana abierta. Todo reporte procesado por excel aparecera aqui.
echo.

:: Navegar al directorio del proyecto automáticamente basado en donde se ejecuta el .bat
cd /d "%~dp0"

:: Ejecutar el Queue Worker
php artisan queue:work --queue=default --tries=3 --timeout=120

pause
