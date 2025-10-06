<?php
require __DIR__ . '/../../vendor/autoload.php';
 = new \Mpdf\Mpdf(['tempDir' => __DIR__ . '/tmp']);

   = ['html'] ?? '<h1>Solicitud</h1><p>Sin contenido.</p>';
 = ['nombre_archivo'] ?? ('Solicitud_' . date('Ymd_His') . '.pdf');

 = __DIR__ . '/../../entregados/solicitudes_pdf';
if (!is_dir()) { mkdir(, 0777, true); }
 =  . '/' . ;

->WriteHTML();
->Output(, \Mpdf\Output\Destination::FILE);

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="'..'"');
readfile();
exit;
