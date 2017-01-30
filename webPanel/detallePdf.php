
<?php
error_reporting(-1);
require_once("lib/tcpdf/tcpdf.php");
$id=$_GET['claseId'];
$servidor=$_SERVER['HTTP_HOST']."/webPanel";

$html = file_get_contents("http://$servidor/detalleClase.php?id=$id&pdf");
//echo $html;
$f_fin = $f_inicio = "";
// create new PDF document
$pdf = new TCPDF();

$pdf->SetHeaderData('', '', "Reporte de simulacion" . " " . $f_inicio . "  " . $f_fin, 'Simuladores Guarani');

$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins

$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
    require_once(dirname(__FILE__) . '/lang/eng.php');
    $pdf->setLanguageArray($l);
}


$pdf->SetFont('dejavusans', '', 10);

$pdf->AddPage();
$pdf->writeHTML('<img alt="Simuladores Guarani" src="img/logo_ancho.png">', true, false, true, false, '');
$pdf->writeHTML($html, true, false, true, false, '');
ob_end_clean();
$pdf->Output('example_006.pdf', 'I');
