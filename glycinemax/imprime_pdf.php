<?php

include_once('lib/funciones.php');
$texto = $_POST['texto'];
$f_inicio = date_transform_lat($_POST['fecha_inicio']);
$f_fin = date_transform_lat($_POST['fecha_fin']);
$html = '<table>';
$html.= base64_decode($_POST['html']);
$apertura = $_POST['apertura'];
require_once("tcpdf/tcpdf.php");
// create new PDF document
$pdf = new TCPDF();

// set document information
//$pdf->SetCreator(PDF_CREATOR);
// set default header data
//$pdf->SetHeaderData();
// set header and footer fonts
/* $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
  $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
 */
// set default monospaced font

$pdf->SetHeaderData('', '', $texto. " " . $f_inicio . "  " . $f_fin, 'Fundación impulso de una vida');

$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins

$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
// set image scale factor
//$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
    require_once(dirname(__FILE__) . '/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------
// set font
$pdf->SetFont('dejavusans', '', 10);

// add a page
$pdf->AddPage();

// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)
// create some HTML content
/* $html = '<h1>HTML Example</h1>
  Some special characters: &lt; € &euro; &#8364; &amp; è &egrave; &copy; &gt; \\slash \\\\double-slash \\\\\\triple-slash
  <h2>List</h2>
  List example:
  <ol>
  <li><img src="" alt="test alt attribute" width="30" height="30" border="0" /> test image</li>
  <li><b>bold text</b></li>
  <li><i>italic text</i></li>
  <li><u>underlined text</u></li>
  <li><b>b<i>bi<u>biu</u>bi</i>b</b></li>
  <li><a href="http://www.tecnick.com" dir="ltr">link to http://www.tecnick.com</a></li>
  <li>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.<br />Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</li>
  <li>SUBLIST
  <ol>
  <li>row one
  <ul>
  <li>sublist</li>
  </ul>
  </li>
  <li>row two</li>
  </ol>
  </li>
  <li><b>T</b>E<i>S</i><u>T</u> <del>line through</del></li>
  <li><font size="+3">font + 3</font></li>
  <li><small>small text</small> normal <small>small text</small> normal <sub>subscript</sub> normal <sup>superscript</sup> normal</li>
  </ol>
  <dl>
  <dt>Coffee</dt>
  <dd>Black hot drink</dd>
  <dt>Milk</dt>
  <dd>White cold drink</dd>
  </dl>'; */

// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');


// ---------------------------------------------------------
//Close and output PDF document
if ($apertura === 'ver') {
    $pdf->Output('example_006.pdf', 'I');
} else {
    foreach ($_POST['id'] as $value) {
        $to.=$value . ",";
    }
    $to = substr($to, 0, -1);
    $subject = $texto.' Impulso de una nueva Vida';
    $cuerpo = ' 
Hola estimado le adjunto el resumen saludos.
';
    $fileName = 'flujo.pdf';
    $fileatt = $pdf->Output($fileName, 'E');
    include 'send_mail.php';
}

//============================================================+
// END OF FILE
//============================================================+
?>