<?php

require_once 'app/config.php';
// Prueba PHPMailer


// $data = [
//     'name' => 'Pancho Villa',
//     'email' => 'edgaryunga01@gmail.com',
//     'subject' => 'Un nuevo correo',
//     'body' => '<h1>Mi plantilla de correo</h1>',
//     'alt_text' => 'Este es el texto alternativo.'
// ];

// $val = send_mail($data);
// if(!$val){
//     print_r('No se envió el correo');
// }

// die;

// Renderizador de la vista principal
get_view('inicio');
