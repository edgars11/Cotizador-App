<?php
require_once INCLUDES . 'head.php';
require_once INCLUDES . 'navbar.php';

?>


<!-- Content -->
<div class="container-fluid py-2">
    <div class="row">
        <div class="col-12 wrapper_notificartions">

        </div>
    </div>
    <div class="row">
        <div class="col-lg-7 col-12">
            <div class="card mb-3">
                <div class="card-header">Información Cliente</div>
                <div class="card-body">
                    <form>
                        <div class="form-group row">
                            <div class="col-4">
                                <label for="nombre">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingrese Nombre Cliente" required>
                            </div>
                            <div class="col-4">
                                <label for="empresa">Empresa</label>
                                <input type="text" class="form-control" id="empresa" name="empresa" placeholder="Ingrese Nombre de la empresa" required>
                            </div>
                            <div class="col-4">
                                <label for="email">Email</label>
                                <input type="text" class="form-control" id="email" name="email" placeholder="Ingrese Email Cliente" required>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Agregar nuevo concepto</div>
                <div class="card-body">
                    <form id="add_to_quote" method="POST">
                        <div class="form-group row">
                            <div class="col-3">
                                <label for="concepto">Concepto</label>
                                <input type="text" class="form-control" id="concepto" name="concepto" placeholder="Nombre Concepto" required>
                            </div>
                            <div class="col-3">
                                <label for="tipo">Tipo de Producto</label>
                                <select name="tipo" id="tipo" class="form-control">
                                    <option value="producto">Producto</option>
                                    <option value="servicio">Servicio</option>
                                </select>
                            </div>
                            <div class="col-3">
                                <label for="cantidad">Cantidad</label>
                                <input type="number" class="form-control" id="cantidad" name="cantidad" min="1" max="9999" value="1" required>
                            </div>
                            <div class="col-3">
                                <label for="precio_unitario">Precio Unitario</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                    </div>
                                    <input type="text" class="form-control" id="precio_unitario" name="precio_unitario" placeholder="0.00" required>
                                </div>
                            </div>
                        </div>
                        <br>
                        <button class="btn btn-success" type="submit">Agregar concepto</button>
                        <button class="btn btn-danger" type="reset">Cancelar</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-5 cal-12">

            <div class="wrapper_update_concept" style="display: none;">
                <!-- Formulario para Editar concepto -->
                <div class="card mb-3">
                    <div class="card-header">Editar concepto:</div>
                    <div class="card-body">
                        <form id="save_concept" method="POST">
                            <input type="hidden" class="form-control" id="id_concepto" name="id_concepto" required>
                            <div class="form-group row">
                                <div class="col-3">
                                    <label for="concepto">Concepto</label>
                                    <input type="text" class="form-control" id="concepto" name="concepto" placeholder="Nombre Concepto" required>
                                </div>
                                <div class="col-3">
                                    <label for="tipo">Tipo de Producto</label>
                                    <select name="tipo" id="tipo" class="form-control">
                                        <option value="producto">Producto</option>
                                        <option value="servicio">Servicio</option>
                                    </select>
                                </div>
                                <div class="col-3">
                                    <label for="cantidad">Cantidad</label>
                                    <input type="number" class="form-control" id="cantidad" name="cantidad" min="1" max="9999" value="1" required>
                                </div>
                                <div class="col-3">
                                    <label for="precio_unitario">Precio Unitario</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="text" class="form-control" id="precio_unitario" name="precio_unitario" placeholder="0.00" required>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <button class="btn btn-success" type="submit">Guardar concepto</button>
                            <button class="btn btn-danger" type="reset" id="cancel_edit">Cancelar</button>
                        </form>
                    </div>
                </div>
                <!-- Fin formulario edición -->
            </div>
            <!-- Inicio lista de conceptos -->
            <div class="card">
                <div class="card-header">
                    Resumen de Cotización
                    <button class="btn btn-danger float-right restart_quote">Reiniciar</button>
                </div>
                <div class="card-body wrapper_quote">
                </div>
                <div class="card-footer">
                    <button class="btn btn-success" id="generate_quote">Generar PDF</button>
                    <a class="btn btn-primary" id="download_quote" href="" style="display: none;">Descargar PDF</a>
                    <button class="btn btn-warning" id="send_quote" style="display: none;">Enviar por correo</button>
                </div>
            </div>
            <!-- Fin lista de conceptos -->
        </div>
    </div>
</div>
<!-- End Content -->

<?php require_once INCLUDES . 'footer.php'; ?>