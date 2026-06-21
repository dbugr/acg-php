<?php
$file = $GetPath('ApplicationPath').'/images/photounavailable.jpg';

if (file_exists($file)) {
   	Header('Content-Type: image/jpg');
    //header('Content-Length: ' . filesize($file));
    ob_clean();
    flush();
    readfile($file);
    exit;
}
