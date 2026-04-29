<?php
$zip = new ZipArchive;
if ($zip->open('DOCUMENTO DE REGLAS DE NEGOCIO Y LÓGICA FUNCIONAL.docx') === TRUE) {
    $xml = $zip->getFromName('word/document.xml');
    $text = strip_tags(str_replace('<w:p', "\n<w:p", $xml));
    file_put_contents('rules_extracted.txt', trim($text));
    $zip->close();
    echo "Saved to rules_extracted.txt";
} else {
    echo "Could not open file.";
}
