<?php

use Dompdf\Dompdf;
use PHPMailer\PHPMailer\PHPMailer;

function get_view($view_name)
{
    $view = VIEWS . $view_name . '.php';
    if (!is_file($view)) {
        die('La vista no existe');
        return;
    }

    // Exste la vista
    require_once $view;
}

// Entidad Cotización
// new_quote[]
/**
 * number
 * name
 * company
 * email
 * items[]
 * subtotal
 * taxes
 * shipping
 * total
 */

/**
 * item
 * id
 * concept
 * type
 * quantity
 * price
 * taxes
 * total
 */

// Functions
/***
 * get_quote()
 * get_items()
 * get_item($id)
 * add_item($item)
 * delete_item($id)
 * delete_items()
 * restart_quote()
 */

function get_quote()
{
    if (!isset($_SESSION['new_quote'])) {
        return $_SESSION['new_quote'] = [
            'number' => rand(1111, 9999),
            'name' => '',
            'company' => '',
            'email' => '',
            'items' => [],
            'subtotal' => 0,
            'taxes' => 0,
            'shipping' => 0,
            'total' => 0
        ];
    }

    recalculate_quote();

    return $_SESSION['new_quote'];
}

function set_client($cliente)
{
    $_SESSION['new_quote']['name'] = trim($cliente['nombre']);
    $_SESSION['new_quote']['company'] = trim($cliente['empresa']);
    $_SESSION['new_quote']['email'] = trim($cliente['email']);
    return true;
}

function recalculate_quote()
{
    $items = [];
    $subtotal = 0;
    $taxes = 0;
    $shipping = SHIPPING;
    $total = 0;

    if (!isset($_SESSION['new_quote'])) {
        return false;
    }

    $items = $_SESSION['new_quote']['items'];

    if (!empty($items)) {
        foreach ($items as $item) {
            $subtotal += $item['total'];
            $taxes += $item['taxes'];
        }
    }

    $total = $subtotal + $taxes + $shipping;

    // Se almacena los resultados en las variables de sesión
    $_SESSION['new_quote']['subtotal'] = $subtotal;
    $_SESSION['new_quote']['taxes'] = $taxes;
    $_SESSION['new_quote']['shipping'] = $shipping;
    $_SESSION['new_quote']['total'] = $total;
    return true;
}

function restart_quote()
{

    $_SESSION['new_quote'] = [
        'number' => rand(1111, 9999),
        'name' => '',
        'company' => '',
        'email' => '',
        'items' => [],
        'subtotal' => 0,
        'taxes' => 0,
        'shipping' => 0,
        'total' => 0
    ];
    return true;
}

function get_items()
{
    $items = [];
    // Si no existe la cotización y obviamente está vacio el array  
    if (!isset($_SESSION['new_quote']['items'])) {
        return $items;
    }
    // La cotización existe, se le asigna el valor de items
    $items = $_SESSION['new_quote']['items'];
    return $items;
}

function get_item($id)
{
    $items = get_items();

    // Si no hay items
    if (empty($items)) {
        return false;
    }

    // Si hay registros iteramos
    foreach ($items as $item) {
        // Valida si existe un item con el id de consulta
        if ($item['id'] === $id) {
            return $item;
        }
    }

    // No hubo un match o resultados
    return false;
}

function delete_items()
{
    $_SESSION['new_quote']['items'] = [];
    recalculate_quote();
    return true;
}

function write_to_console($data)
{

    $console = 'console.log(' . json_encode($data) . ');';
    $console = sprintf('<script>%s</script> ', $console);
    echo $console;
}

function delete_item($id)
{
    $items = get_items();

    // Si no hay items
    if (empty($items)) {
        return false;
    }

    // Si hay registros iteramos
    foreach ($items as $i => $item) {
        // Valida si existe un item con el id de consulta
        if ($item['id'] === $id) {
            unset($_SESSION['new_quote']['items'][$i]);
            return true;
        }
    }

    // No hubo un match o resultados
    return false;
}

function add_item($item)
{
    $items = get_items();
    // Valida si existe el registro en el array
    if (get_item($item['id']) !== false) {
        foreach ($items as $i => $v_item) {
            if ($item['id'] === $v_item['id']) {
                $_SESSION['new_quote']['items'][$i] = $item;
                return true;
            }
        }
    }

    // Si no existe se añade al array
    $_SESSION['new_quote']['items'][] = $item;
    return true;
}

function json_build($status = 200, $data = null, $msg = '')
{
    if (empty($msg) || $msg == '') {
        switch ($status) {
            case 200:
                $msg = 'OK';
                break;
            case 201:
                $msg = 'Created';
                break;
            case 400:
                $msg = 'Invalid Request';
                break;
            case 403:
                $msg = 'Access denied';
                break;
            case 404:
                $msg = 'Not Found';
                break;
            case 500:
                $msg = 'Internal Server Error';
                break;
            case 550:
                $msg = 'Permission denied';
                break;
            default:
                break;
        }
    }

    $json = [
        'status' => $status,
        'data' => $data,
        'msg' => $msg
    ];

    return json_encode($json);
}

function json_output($json)
{
    header('Access-Control-Allow-Origin: *');
    header('Content-type: application/json;charset=utf-8');

    if (is_array($json)) {
        $json = json_encode($json);
    }

    echo $json;

    exit();
}

function get_module($view, $data = [])
{
    $view = $view . '.php';
    if (!is_file($view)) {
        return false;
    }

    $d = $data = json_decode(json_encode($data));

    ob_start();
    require_once $view;
    $output = ob_get_clean();

    return $output;
}

function hook_saludo_php()
{
    echo 'Saludos desde PHP';
}

function hook_get_quote_res()
{
    $quote = get_quote();
    $html = get_module(MODULES . 'quote_table', $quote);

    json_output(json_build(200, ['quote' => $quote, 'html' => $html]));
}
// Agregar concepto
function hook_add_to_quote()
{
    // Validar parámetros
    if (!isset($_POST['concepto'], $_POST['tipo'], $_POST['precio_unitario'], $_POST['cantidad'])) {
        json_output(json_build(403, null, "Parámetros incompletos"));
    }

    $concepto = trim($_POST['concepto']);
    $type = trim($_POST['tipo']);
    $price = (float) str_replace([',', '$'], '', $_POST['precio_unitario']);
    $quantity = (int) trim($_POST['cantidad']);
    $subtotal = (float) $price * $quantity;
    $taxes = (float) $subtotal * (TAXES_RATE / 100);

    $item = [
        'id' => rand(1111, 9999),
        'concept' => $concepto,
        'type' => $type,
        'quantity' => $quantity,
        'price' => $price,
        'taxes' => $taxes,
        'total' => $subtotal,
    ];

    if (!add_item($item)) {
        json_output(json_build(400, null, 'Error al guardar concepto en la contización.'));
    }

    json_output(json_build(201, get_item($item['id']), 'Concepto agregado con éxito.'));
}
// Reiniciar cotización
function hook_restart_quote()
{
    $items = get_items();

    if (empty($items)) {
        json_output(json_build(400, null, 'No hay conceptos para reiniciar la cotización.'));
    }

    if (!restart_quote()) {
        json_output(json_build(400, null, 'Hubo un error al reiniciar la cotización.'));
    } else {
        json_output(json_build(200, get_quote(), 'La cotización se ha reiniciado con éxito.'));
    }
}
// Borrar un concepto de la cotización
function hook_delete_concept()
{
    if (!isset($_POST['id'])) {
        json_output(json_build(403, null, 'Parámetros incompletos'));
    }

    if (!delete_item((int) $_POST['id'])) {
        json_output(json_build(400, null, 'Hubo un problema al borrar el concepto.'));
    }

    json_output(json_build(200, get_quote(), 'Concept borrado con éxito.'));
}

// Cargar un concepto para editar
function hook_edit_concept()
{
    if (!isset($_POST['id'])) {
        json_output(json_build(403, null, 'Parámetros incompletos.'));
    }
    if (!$item = get_item((int)$_POST['id'])) {
        json_output(json_build(400, null, 'Hubo un problema al cargar concepto.'));
    }

    json_output(json_build(200, $item, 'Concepto cargado con éxito.'));
}

// Guardar los cambios de un concepto
function hook_save_concept()
{
    // Validar
    if (!isset($_POST['id_concepto'], $_POST['concepto'], $_POST['tipo'], $_POST['precio_unitario'], $_POST['cantidad'])) {
        json_output(json_build(403, null, 'Parámetros incompletos'));
    }

    $id = (int) $_POST['id_concepto'];
    $concept = trim($_POST['concepto']);
    $type = trim($_POST['tipo']);
    $price = (float) str_replace([',', '$'], '', $_POST['precio_unitario']);
    $quantity = (int) trim($_POST['cantidad']);
    $subtotal = (float) $price * $quantity;
    $taxes = (float) $subtotal * (TAXES_RATE / 100);

    $item = [
        'id' => $id,
        'concept' => $concept,
        'type' => $type,
        'quantity' => $quantity,
        'price' => $price,
        'taxes' => $taxes,
        'total' => $subtotal
    ];

    if (!add_item($item)) {
        json_output(json_build(400, null, 'Hubo un problema al guardar los cambios del concepto.'));
    }

    json_output(json_build(200, get_item($id), 'Cambios guardados con éxito.'));
}

function generar_pdf($filename = null, $html, $save_to_file = true)
{
    $filename = $filename === null ? time() . '.pdf' : $filename . '.pdf';

    $pdf = new Dompdf();
    $pdf->setPaper('A4');
    $pdf->loadHtml($html);
    $pdf->render();
    // Se valida si se guardar el pdf automático
    if ($save_to_file) {
        $output = $pdf->output();
        file_put_contents($filename, $output);
        return true;
    }

    $pdf->stream($filename);
    return true;
}
// Crea el pdf de la cotización
function hook_generate_quote()
{
    // Validar
    if (!isset($_POST['nombre'], $_POST['empresa'], $_POST['email'])) {
        json_output(json_build(403, null, 'Parámetros imcompletos.'));
    }

    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        json_output(json_build(400, null, 'Dirección de correo inválida.'));
    }

    // Guardar información del cliente
    $cliente = [
        'nombre' => $_POST['nombre'],
        'empresa' => $_POST['empresa'],
        'email' => $_POST['email'],
    ];

    set_client($cliente);

    // Cargar Cotización
    $quote = get_quote();

    if (empty($quote['items'])) {
        json_output(json_build(400, null, 'No hay conceptos en la cotización.'));
    }

    $module = MODULES . 'pdf_template';
    $html = get_module($module, $quote);
    $filename = 'coty_' . $quote['number'];
    $download =  sprintf(URL . 'pdf.php?number=%s', $quote['number']); // URL.UPLOADS.$filename.'.pdf';
    $quote['url'] = $download;

    // Generar PDF y guardarlo en el servidor
    if (!generar_pdf(UPLOADS . $filename, $html)) {
        json_output(json_build(400, null, 'Hubo un problema al generar la cotización.'));
    }

    json_output(json_build(200, $quote, 'Cotización generada con éxito.'));
}
// Cargar todas las cotizaciones
function get_all_quote_pdf()
{
    return $quotes = glob(UPLOADS . 'coty_*.pdf');
}
// Redirección
function redirect($route)
{
    header(sprintf('Location: %s', $route));
    exit;
}
// Enviar nuevo correo electronico
function send_mail($data)
{
    $mail = new PHPMailer();
    $mail->setFrom('edgarsyunga01@gmail.com', 'Sistema de Cotización'); // Remitente
    $mail->addAddress($data['email'], empty($data['name'] ? null : $data['name'])); // destinatario
    $mail->Subject = $data['subject']; // Asunto
    $mail->msgHTML(get_module(MODULES . 'email_template', $data)); // plantilla
    $mail->AltBody = $data['alt_text']; // Alternativo
    $mail->CharSet = 'UTF-8'; // Charset

    //Adjuntos
    if (!empty($data['attachments'])) {
        foreach ($data['attachments'] as $file) {
            $mail->addAttachment($file);
        }
    }

    if (!$mail->send()) {
        return false;
    }
    return true;
}

// Validar envio de correo
function hook_send_quote()
{
    if (!isset($_POST['number'])) {
        json_output(json_build(403, null, 'Parámetros incompletos'));
    }

    // Validar correo
    $number = $_POST['number'];
    $quote = get_quote();
    if (!filter_var($quote['email'], FILTER_VALIDATE_EMAIL)) {
        json_output(json_build(400, null, 'Dirección de correo no es válida.'));
    }
    //Valida la existencia de la cotización
    $file = sprintf(UPLOADS . 'coty_%s.pdf', $number);
    if (!is_file($file)) {
        json_output(json_build(400, null, 'La cotización no existe.'));
    }

    // Guarda información para el correo.
    $body = '<h1>Nueva Cotización</h1> <br> <p>Hola <b>%s</b>, has recibido una cotización con folio <b>%s</b> por parte de <b>%s</b>, se encuentra adjuntada a este correo.</p> ';
    $body = sprintf($body, $quote['name'], $number, APP_NAME);
    $email_data = [
        'subject' => sprintf('Cotización número %s recibida', $number),
        'alt_text' => sprintf('Nueva cotización de  %s recibida', APP_NAME),
        'body' => $body,
        'name' => $quote['name'],
        'email' => $quote['email'],
        'attachments' => [$file]
    ];

    if (!send_mail($email_data)) {
        json_output(json_build(400, null, 'Hubo un problema al enviar el correo.'));
    }
    json_output(json_build(200, null, 'Cotización enviada con éxito.'));
}
