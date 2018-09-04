
$("#collapseTwo").addClass('show');
$("#company").addClass('active');
$(document).on('click','.open_modal_edit',function() {    
    $('.error').html("");
    $.get({
        url : $(this).attr('url'), 
        success : function (result) {
            $('input[name="id"]').val(result.data.id);
            $('input[name="rut"]').val(result.data.rut);
            $('input[name="dv"]').val(result.data.dv);
            $('input[name="name"]').val(result.data.name);
            $('input[name="contact_phone"]').val(result.data.contact_phone);
            $('input[name="contact_name"]').val(result.data.contact_name);
            $('input[name="status"]').filter("[value='" + result.data.status + "']").attr('checked', true);
            $('.btn-save').val('update');
            $('#modalCompany').modal('show');
        },
        error: function() {
            alert('Error');
        }
    })
});

$(document).on('click','.open_modal_new',function() {        
    $('.btn-save').val('create');
    $('.error').html("");
    cleanFields();
    $('#modalCompany').modal('show');
});

$(document).on('click','.btn-save',function() {    
    $('.error').html("");
    var action= $(this).val();
    var data = $(this).closest('.modal-content').find('form').serialize(); 
            
    if (action == 'create' ) {
        var url = 'company';
        var method =  'POST';
    }        
    else {
        var url = 'company/' +  $('input[name="id"]').val();  
        var method =  'PUT';
    }

   $.ajax({
        type: method,
        url : url, 
        data : data,           
        success : function (data) {
            if(data.success) {
                bootbox.alert(data.message, function() {
                    $('#modalUser').modal('hide');
                    location.reload();    
                });
            }
            else {                
                bootbox.alert('Faltan campos requeridos. Por favor verifique la información sumistrada.');
                for (var error in data.errors) {
                    $("#" + error + '_error').html(data.errors[error][0]);
                }
            }
        },  
        error: function() {
            bootbox.alert('Error al intentar grabar el Usuario, intente nuevamente.');            
        }
    })
    
});

$(document).on('click','.disabledCompany', function() {        

    button =  $(this);

    message =  button.html() == "Habilitar"?'Deshabilitar':'Habilitar';   

    bootbox.confirm({
        message: "Desea " + message + " esta Empresa?",
        buttons: {
            confirm: {
                label: 'Si',
                className: 'btn-success'
            },
            cancel: {
                label: 'No',
                className: 'btn-danger'
            }
        },
        callback: function (result) {
            var url = button.attr('url');

            data = {
                "_token": $("input[name=_token]").val()
            }

            $.ajax({
                type: "PUT",
                url : url, 
                data : data,           
                success : function (data) {
                    if(data.success) {
                        button.html(button.html() == "Habilitar"?'Deshabilitar':'Habilitar');
                        
                    }
                },  
                error: function() {
                   bootbox.alert('Error al intentar cambiar el estado de la Empresa, intente nuevamente.');            
                }
            })
        }
    });
    
});




