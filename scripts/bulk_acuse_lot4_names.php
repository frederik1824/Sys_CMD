<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Afiliado;
use App\Models\Estado;
use App\Models\HistorialEstado;
use Illuminate\Support\Facades\DB;

$nombres = [
'VENUS MARIEL FELIZ MORENO', 'YELAINI BRAZOBAN DE LA CRUZ', 'ASLI MICHELL RODRIGUEZ', 
'YENILETZI RAQUEL DE LA CRUZ RONDON', 'TEIRI MARIA ESTEL LEONARDO OZUNA', 'OMEGA YNES ALMANZAR', 
'CARINA ESMERALDA TAISON TERRERO', 'EDGAR ENRIQUE VASQUEZ ARACHE', 'ELISANDRA ANTIGUA MEDINA', 
'HAZAEL EDUARDO RAMIREZ MATOS', 'HENDELSON FRANCISCO DUARTE REYNOSO', 'JAYRO ROLANDO PEREZ SALAS', 
'JOSTIN DAVID PACHECO PUJOLS', 'JUAN CARLOS FRIAS SANCHEZ', 'YISAHIRA VASQUEZ DE LA CRUZ', 
'DENISHA ESTHER CASTILLO JAVIEL', 'SOL DANIA MARTINEZ MARTINEZ', 'JADE MARINA PICHARDO COSTE', 
'GENOVA VENTURA VELOZ', 'KIARA MANUELSY LEONARDO BAEZ', 'GABRIEL ISAI SANTOS ESPINAL', 
'LUIS ALBERTO VENTURA ROJAS', 'CRISTINA CARRASCO ROSARIO', 'CHARLENY JIMENEZ REYES', 
'ROSANNA VASQUEZ ORTIZ', 'VIRGINIA ESTEFANI DE JESUS PEGUERO', 'ALAMS WILMERT MEDINA VARGAS', 
'FRANLLEYS JAVIER MENDEZ', 'GABRIEL LAUREANO ESPINAL', 'CARLOS YUNIOR VALENZUELA CHERISABLE', 
'WILFRI MANUEL AMEZQUITA', 'JARDIER MOREL CHALAS', 'PRISLEIDY PLACENCIA MARTINEZ', 
'FELIX JAVIER DEL ROSARIO BEJARAN', 'ANDILEINY CUEVAS GUICHARDO', 'DARYELI GARCIA MONTAÑO', 
'PAMELA LEDESMA', 'SACHERI TEJEDA DIAZ', 'MILAGROS DE LOS ANGELES ARIAS PEREZ', 'YULEIDI CRISTAL LORA', 
'KARLA DOMINGUEZ REYES', 'DANYI GLENVIR MORETA DE LA CRUZ', 'JEAN CARLOS SEGURA CUEVAS', 
'DIOHANDRIS DEBORAH TEJADA', 'INELSON MENDEZ MENDEZ', 'GRANFIS GORIS GONZALEZ', 
'RAYMELY ESTHER BRITO', 'SAMUEL EMILIO FEBLES JAQUE', 'EDWIN MIGUEL MARTE CABRERA', 
'INMANOL GRULLON AQUINO', 'JOSE RAFAEL ABREU ROBLES', 'GERLIN RAMIREZ DELGADO', 
'ELLIOT JESUS CABRERA', 'LISBETH LINAREZ ANDRE', 'DANIELA DE JESUS ALCANTARA', 'MELANI PEÑA ABREU', 
'ESCARLET MARIEL MATEO FELIZ', 'CHRISTOPHER OVALLE FELIZ', 'ENGER RODRIGUEZ', 
'WILIAN GARCIA JN GUILLAUME', 'NICAURIS VENTURA VALDEZ', 'CARINA MARTINEZ GONZALEZ', 
'KIMBERLY JIMENEZ REYES', 'ELIZANDRA PERDOMO RUBECINDO', 'NOEL RAMIREZ DIPRE', 
'ANDERSON ALEXANDER DIAZ CASTILLO', 'TOMAS ALBERTO RODRIGUEZ MURSITON', 'ENGEL PANIAGUA TEJEDA', 
'DARIELVIN SANTOS ORTEGA', 'GINADEL BENSUA', 'ALVIT BALDAYAC', 'GENESIS YACKELLY SANTOS PAULINO', 
'ADERLIN ESTARLIN RODRIGUEZ GOMEZ', 'JUSTIN AMANCIO CAMPUSANO VELASQUEZ', 
'SHEISY MARIA MISSURY BATISTA MARTE', 'VICTOR DAVID POCHE SUERO', 'YARINE RODRIGUEZ DE LOS SANTOS', 
'FERNANDO JOSE CONCEPCION ARIAS', 'NELSON JOEL RAMOS ROSSO', 'OSMAILY HUMBERTO GOMEZ LEBRON', 
'VICTOR STARLYN HILARIO MERCEDES', 'ABIGAIL DIVISON MARTINEZ', 'ARIEL OSVALDO ROSARIO VENTURA', 
'ROBIN ISMAEL MOLINA SIME', 'MARIA DE LA LUZ PEREZ PEREZ', 'ELVIS ALEXANDER CEDEÑO MATEO', 
'LUCERO FRANCOIS PINALES', 'MIGUEL ANGEL GERMAN', 'SINDY MIGUELINA EUSEBIO MARTINEZ', 
'URIEL ELIAS PEGUERO', 'JENNIFER ALEJANDRA GERONIMO ANGULO', 'BRYAN MANUEL MOQUETE DEL CARMEN', 
'EDIONASIS JIMENEZ MATOS', 'GENESI JARIDA BENZO ADAMES', 'ERIKA MASSIEL SIRENA', 
'BENEDICTO ABAD SIERRA MEDINA', 'ERNESTO LUIS BATISTA GUZMAN', 'ERASMO ROSARIO', 'MALLELIN MARCELINO', 
'ARNOLD MANUEL CASTILLO REYES', 'LUIS ANGEL FELIZ FIGUEREO', 'YAMILEE ALEXANDRA GALVAN RIVERA', 
'EDILITH MARIA SANTOS GOMEZ', 'WARLIN ALBERTO GARCIA MATOS', 'JAYLO SANCHEZ CESAR', 
'ROSANNA MARGARITA OVALLES ALMANZAR', 'BRIANNEY JURLEINY REYES JAVIER', 'YARITZA ESMEYLIN NIEVES MARTE', 
'BRAILYN JAVIER PUELLO VARGAS', 'VALERY ARLYN RODRIGUEZ NUÑEZ', 'ERVIN VIRGILIO GARCIA REYES', 
'JOSE MANUEL TORRES ORTIZ', 'KEMERLY ACOSTA SANCHEZ', 'VICTOR JUNIOR LARA', 
'NAOMI EURIDICIS DISLA DEL ORBE', 'BRENDA ALTAGRACIA SEVERINO MOYA', 'YENIBEL CAROLAY GOMEZ', 
'CHAREILI UREÑA DE LOS SANTOS', 'LESHLIE NICOLE DIAZ VALLEJO', 'JESUS ERNESTO DERVILLE POLEON', 
'MAYKIN KING DEL BOIS', 'MIGUEL FRANCO DE LOS SANTOS'
];

$acuse_id = Estado::where('nombre', 'Acuse recibido')->value('id') ?? 7;

$stats = [
    'nombres_procesados' => count($nombres),
    'afiliados_encontrados' => 0,
    'actualizados' => 0,
    'ya_en_estado' => 0,
    'no_encontrados' => 0
];

try {
    DB::beginTransaction();

    foreach ($nombres as $nombre) {
        $nombre_clean = trim($nombre);
        $afiliados = Afiliado::where('nombre_completo', 'LIKE', $nombre_clean)->get();

        if ($afiliados->isEmpty()) {
            $stats['no_encontrados']++;
            continue;
        }

        foreach ($afiliados as $afiliado) {
            $stats['afiliados_encontrados']++;

            if ($afiliado->estado_id == $acuse_id) {
                $stats['ya_en_estado']++;
                continue;
            }

            $oldEstado = $afiliado->estado_id;
            $afiliado->estado_id = $acuse_id;
            $afiliado->save();

            HistorialEstado::create([
                'afiliado_id' => $afiliado->id,
                'estado_anterior_id' => $oldEstado,
                'estado_nuevo_id' => $acuse_id,
                'user_id' => 1,
                'observacion' => 'Actualización masiva a Acuse (Lote 4): Marcado por solicitud de usuario.'
            ]);

            $stats['actualizados']++;
        }
    }

    DB::commit();

    echo "Reporte de Operación Lote 4 (Acuse por Nombre):\n";
    echo "--------------------------\n";
    echo "Nombres en el listado: {$stats['nombres_procesados']}\n";
    echo "Afiliados encontrados: {$stats['afiliados_encontrados']}\n";
    echo "Ya estaban en 'Acuse': {$stats['ya_en_estado']}\n";
    echo "Actualizados ahora: {$stats['actualizados']}\n";
    echo "Nombres no localizados: {$stats['no_encontrados']}\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}
