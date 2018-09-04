
$("#collapseTwo").addClass('show');
$("#user").addClass('active');

$(document).on('click','.open_modal_edit',function() {
    $('.error').html("");
    $(".showPassword").prop('checked', false); 
    $( ".noEdit" ).prop( "disabled", true );
    $(".editNoRequired").css('display', 'none');
     cleanFields();
    $.get({
        url : $(this).attr('url'), 
        success : function (result) {
            $('input[name="id"]').val(result.data.id);
            $('input[name="rut"]').val(result.data.rut);
            $('input[name="name"]').val(result.data.name);
            $('input[name="dv"]').val(result.data.dv);
            $('input[name="surname"]').val(result.data.surname);
            $('input[name="second_surname"]').val(result.data.second_surname);
            $('input[name="email"]').val(result.data.email);
            $('select[name="company_id"]').val(result.data.company_id);
            $('select[name="profile_id"]').val(result.data.profile_id);            
            $('input[name="status"]').filter("[value='" + result.data.status + "']").attr('checked', true);
            $('.btn-save').val('update');
            $('#modalUser').modal('show');
        },
        error: function() {
            alert('Error');
        }
    })
});

$(document).on('click','.open_modal_new', function() {        
    $('.error').html("");
    $('.btn-save').val('create');
    $(".showPassword").prop('checked', false); 
    $( ".noEdit" ).prop( "disabled", false );
    $(".editNoRequired").css('display', 'block');
    cleanFields();
    $('#modalUser').modal('show');
});


$(document).on('click','.disabledUser', function() {   
    $('.error').html("");
    button =  $(this);

    message =  button.html() == "Habilitar"?'Deshabilitar':'Habilitar';   

    bootbox.confirm({
        message: "Desea " + message + " este Usuario?",
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
                   bootbox.alert('Error al intentar cambiar el estado el Usuario, intente nuevamente.');            
                }
            })
        }
    });
    
});


$(document).on('click','.btn-save',function() {    
    var action= $(this).val();
    var data = $(this).closest('.modal-content').find('form').serialize(); 
            
    if (action == 'create' ) {
        var url = 'user';
        var method =  'POST';
    }        
    else {
        var url = 'user/' +  $('input[name="id"]').val();  
        var method =  'PUT';
    }

    $(".error").html("");
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


$(".showPassword").change(function() {
    if ($('.showPassword:checked').length > 0) {
        $("input[name=password], input[name=password_confirm]").attr('type', 'text');
    }
    else {
        $("input[name=password], input[name=password_confirm]").attr('type', 'password');
    }
});


