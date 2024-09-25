$('document').ready(() => {
    // Funcion para generar una notificación
    function notify(content, type = 'success') {
        let wrapper = $('.wrapper_notificartions'),
            id = Math.floor((Math.random() * 500) + 1),
            notificacion = '<div class="alert alert-' + type + '" id="noty_' + id + '">' + content + '</div>',
            time = 5000;
        // Insertar en el contenedor de la notificación
        wrapper.append(notificacion);

        setTimeout(function () {
            // wrapper.html();
            $('#noty_' + id).remove();
        }, time);

        return true;
    }

    //Carga contenido de la cotización
    function get_quote() {
        let wrapper = $('.wrapper_quote'),
            action = 'get_quote_res',
            name = $('#nombre'),
            company = $('#empresa'),
            email = $('#email');

        $.ajax({
            url: 'ajax.php',
            type: 'get',
            cache: false,
            dataType: 'json',
            data: { action },
            beforeSend: function () {
                wrapper.waitMe();
            }
        }).done(res => {
            if (res.status === 200) {
                name.val(res.data.quote.name);
                company.val(res.data.quote.company);
                email.val(res.data.quote.email);
                wrapper.html(res.data.html);
            } else {
                name.val('');
                company.val('');
                email.val('');
                wrapper.html(res.msg);
            }
        }).fail(err => {
            wrapper.html('Ocurrió un error, recarge la página.');
        }).always(() => {
            wrapper.waitMe('hide');
        });
    }
    get_quote();

    $('#add_to_quote').on('submit', add_to_quote);
    function add_to_quote(e) {
        e.preventDefault();
        console.log('add_quote');
        let form = $('#add_to_quote'),
            action = 'add_to_quote',
            data = new FormData(form.get(0)),
            errors = 0;

        // Agregar la acción al objeto Data
        data.append('action', action);

        // Validar concepto
        let concepto = $('#concepto').val(),
            precio = parseFloat($('#precio_unitario').val());
        if (concepto.lenght < 5) {
            notify('Ingrese un concepto válido por favor.', 'danger');
            errors++;
        }
        // Valida precio
        if (precio < 10) {
            notify('Por favor ingrese un precio mayor a $10.', 'danger');
            errors++;
        }

        if (errors > 0) {
            notify('Complete el formulario.', 'danger');
            return false;
        }

        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            cache: false,
            processData: false,
            contentType: false,
            data: data,
            beforeSend: () => {
                form.waitMe();
            }
        }).done(res => {
            if (res.status === 201) {
                notify(res.msg);
                form.trigger('reset');
                get_quote();
            } else {
                notify(res.msg, 'danger');
            }
        }).fail(err => {
            notify('Hubo un problema con la petición.', 'danger');
            form.trigger('reset');
        }).always(() => {
            form.waitMe('hide');
        });


    }

    $('.restart_quote').on('click', restart_quote);
    function restart_quote(e) {
        e.preventDefault();

        let button = $(this),
            action = 'restart_quote',
            download = $('#download_quote'),
            send = $('#send_quote'),
            generate = $('#generate_quote'),
            default_text = 'Generar cotización';

        if (!confirm('¿Estas seguro?')) return false;

        // petición
        $.ajax({
            url: 'ajax.php',
            type: 'post',
            dataType: 'json',
            data: { action }
        }).done(res => {
            if (res.status === 200) {
                download.fadeOut();
                download.attr('href', '');
                send.fadeOut();
                send.attr('data-number', '');
                generate.html(default_text);
                notify(res.msg);
                get_quote();
            } else {
                notify(res.msg, 'danger');
            }
        }).fail(err => {
            notify('Hubo un problema con la petición.', 'danger');
        }).always(() => {

        });
    }

    $('body').on('click', '.delete_concept', delete_concept);
    function delete_concept(e) {
        e.preventDefault();

        let button = $(this),
            id = button.data('id'),
            action = 'delete_concept';

        if (!confirm("¿Desea eliminar el item?")) return false;

        //
        $.ajax({
            url: 'ajax.php',
            type: 'post',
            dataType: 'json',
            data: { action, id },
            beforeSend: () => {
                $('body').waitMe();
            }
        }).done(res => {
            if (res.status === 200) {
                notify(res.msg);
                get_quote();
            } else {
                notify(res.msg, 'danger');
            }
        }).fail(err => {
            notify('Hubo un problema con la peticion', 'danger');
        }).always(() => {
            $('body').waitMe('hide');
        });
    }

    $('body').on('click', '.edit_concept', edit_concept);
    function edit_concept(e) {
        e.preventDefault();

        let button = $(this),
            id = button.data('id'),
            action = 'edit_concept',
            wrapper_update_concept = $('.wrapper_update_concept'),
            form_update_concept = $('#save_concept');

        //Petición
        $.ajax({
            url: 'ajax.php',
            type: 'post',
            dataType: 'json',
            data: { action, id },
            beforeSend: () => {
                $('body').waitMe();
            }
        }).done(res => {
            if (res.status === 200) {
                $('#id_concepto', form_update_concept).val(res.data.id);
                $('#concepto', form_update_concept).val(res.data.concept);
                $('#tipo option[value="' + res.data.type + '"]', form_update_concept).attr('selected', true);
                $('#cantidad', form_update_concept).val(res.data.quantity);
                $('#precio_unitario', form_update_concept).val(res.data.price);
                wrapper_update_concept.fadeIn();
                notify(res.msg);
            } else {
                notify(res.msg, 'danger');
            }
        }).fail(err => {
            notify('Hubo un problema con la petición.', 'danger');
        }).always(() => {
            $('body').waitMe('hide');
        });

    }

    // Función guardar cambios de concepto editado
    $('#save_concept').on('submit', (e) => {
        e.preventDefault();

        let form = $('#save_concept'),
            action = 'save_concept',
            data = new FormData(form.get(0)),
            wrapper_update_concept = $('.wrapper_update_concept'),
            errors = 0;
        // Agregar la acción del objeto data
        data.append('action', action);
        // Validar concepto
        let concept = $('#concepto', form).val(),
            precio = parseFloat($('#precio_unitario', form).val());

        if (concept.lenght < 5) {
            notify('Ingrese un concepto valido por favor', 'danger');
            errros++;
        }
        if (precio < 10) {
            notify('Por favor ingrese un precio mayor a $10.00', 'danger');
            errros++;
        }

        if (errors > 0) {
            notify('Complete el formulario.', 'danger');
            return false;
        }

        //Petición ajax
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            cache: false,
            processData: false,
            contentType: false,
            data: data,
            beforeSend: () => {
                form.waitMe();
            }
        }).done(res => {
            if (res.status === 200) {
                wrapper_update_concept.fadeOut();
                form.trigger('reset');
                notify(res.msg);
                get_quote();
            } else {
                notify(res.msg, 'danger');
            }
        }).fail(err => {
            notify('Hubo un error con la petición, intenta nuevamente.', 'danger');
            wrapper_update_concept.fadeOut();
            form.trigger('reset');
        }).always(() => {
            form.waitMe('hide');
        })
    });

    // Cancelar y cerrar el formulario de modificación
    $('#cancel_edit').on('click', (e) => {
        e.preventDefault();

        let button = $(this),
            wrapper = $('.wrapper_update_concept'),
            form = $('#save_concept');

        wrapper.fadeOut();
        form.trigger('reset');
    })

    // Generar PDF 
    $('#generate_quote').on('click', (e) => {
        e.preventDefault();

        let button = $('#generate_quote'),
            default_text = button.html(),
            new_text = 'Volver a generar',
            download = $('#download_quote'),
            send = $('#send_quote'),
            nombre = $('#nombre').val(),
            empresa = $('#empresa').val(),
            email = $('#email').val(),
            action = 'generate_quote',
            errors = 0;

        // Validando la acción
        if (!confirm('¿Estás seguro?')) return true;

        //Validando la información
        if (nombre < 5) {
            notify('Ingrese un nombre válido para el cliente.', 'danger');
            errors++;
        }
        if (empresa < 5) {
            notify('Ingrese un nombre válido para la empresa.', 'danger');
            errors++;
        }
        if (email < 5) {
            notify('Ingrese un correo válido.', 'danger');
            errors++;
        }

        if (errors > 0) {
            notify('Valide todos los campos.', 'danger');
            return false;
        }

        //Petición
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            cache: false,
            data: { action, nombre, empresa, email },
            beforeSend: () => {
                $('body').waitMe();
                button.html('Generando....');
            }
        }).done(res => {
            if (res.status === 200) {
                notify(res.msg);
                download.attr('href', res.data.url);
                download.fadeIn();
                send.attr('data-number', res.data.number);
                send.fadeIn();
                button.html(new_text);
            } else {
                notify(res.msg, 'danger');
                download.attr('href', '');
                download.fadeOut();
                send.attr('data-number', '');
                send.fadeOut();
                button.html('Reintentar');
            }
        }).fail(err => {
            notify('Hubo un error al generar el PDF.', 'danger');
            button.html(default_text);
        }).always(() => {
            $('body').waitMe('hide');
        });

    });

    $('#send_quote').on('click', send_quote);
    function send_quote(e) {
        e.preventDefault();

        let button = $('#send_quote'),
            default_text = button.html(),
            new_text = 'Volver a enviar',
            number = button.data('number'),
            acción = 'send_quote';

        if (!confirm('¿Está seguro?')) return false;

        if (number === '') {
            notify('El folio de la cotización no es válido.', 'danger');
            return false;
        }
        //Petición
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            cache: false,
            data: { acción, number },
            beforeSend: () => {
                $('body').waitMe();
                button.html('Enviando...');
            }
        }).done(res => {
            if (res.status === 200) {
                notify(res.msg);
                button.html(new_text);
            } else {
                notify(res.msg, 'danger');
                button.html('Reintentar');
            }
        }).fail(err => {
            notify('Hubo un problema con la petición, intenta de nuevo.', 'danger');
            button.html(default_text);
        }).always(() => {
            $('body').waitMe('hide');
        });
    }
});