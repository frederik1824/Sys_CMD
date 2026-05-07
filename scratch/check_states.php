<?php
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
foreach(DB::table('estado_traspasos')->get() as $e) {
    echo "$e->id: $e->nombre ($e->slug)\n";
}
