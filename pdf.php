<?php
require_once 'app/config.php';

// generar_pdf('cotizacion_'.time(),get_module(MODULES.'pdf_template'));
// echo $_GET['number'];
// Validar que existan cotizaciones y el parámetro $_GET['number']
if (!isset($_GET['number'])) {
    redirect('index.php?error=invalid_number');
}

// Si no hay cotizaciones
$quotes = get_all_quote_pdf();
if (empty($quotes)) {
    redirect('index.php?error=no_quotes');
}

//Buscar el match del folio que buscamos
$number = trim($_GET['number']);
$coty = sprintf(UPLOADS . 'coty_%s.pdf', $number);

if (!is_file($coty)) {
    // Descargar
    redirect('index.php?error=not_found');
}

header('Content-Type: application/pdf');
header(sprintf('Content-Disposition: attachment;filename=%s', pathinfo($coty, PATHINFO_BASENAME)));
readfile($coty);
