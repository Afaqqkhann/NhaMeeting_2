<?php 

$my_file = $_GET['file'];
//echo $my_file;die;

// instantiate and use the dompdf class

$path = substr($my_file, 7);

//echo $path;die;

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename='.$path);
header('Content-Transfer-Encoding: binary');
header('Accept-Ranges: bytes');

//echo $path;die;

readfile($path);

/*$options = new Options();
$options->setIsRemoteEnabled(true);


$dompdf = new Dompdf($options);
$dompdf->loadHtml('<img src="http://125.209.65.69:81/hrmis/'.$my_file.'" alt="notification"/>');

// (Optional) Setup the paper size and orientation
//$dompdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$dompdf->render();
// Output the generated PDF to Browser
//$dompdf->stream();
$dompdf->stream('download.pdf',array('Attachment'=>false));
*/

?>