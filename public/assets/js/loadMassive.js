
$("#collapseTwo").addClass('show');
$("#loadMassive").addClass('active');

$('input[type="file"]'). change(function(e){
	var fileName = e.target.files[0].name;
	if (fileName == '')
		$('.fileName').html('Ningun archivo seleccionado');
	else
		$('.fileName').html(fileName);
});


$('input, select').change(function(event) {
	if($(this).val() != '') {
		$("#" + $(this).attr('id') + "_error").html("");
	}
});



$('form').submit(function(event) {
	event.preventDefault();
    $(".error").html("");

    $("#loading").removeClass('loadingInactive');
    $("#loading").addClass('loading');

    datos = new FormData($(this)[0]);    
    $.ajax({            
    	type: "POST",    	
        url : "loadMassive", 
        data : datos,
        success : function (response) {
            if(response.success) {
                $("#loading").removeClass('loading');
                $("#loading").addClass('loadingInactive');
                bootbox.alert('Datos cargados correctamente.', function() {
                    $('#modalUser').modal('hide');
                    location.reload();    
                });
            }
            else {   
                $("#loading").removeClass('loading');
                $("#loading").addClass('loadingInactive');
                bootbox.alert('Información sumistrada no esta completa, porfavor verifique.');
                for (var error in response.errors) {
                    $("#" + error + '_error').html(response.errors[error][0]);
                }
            }
        },  
        error: function(response) {
            $("#loading").removeClass('loading');
            $("#loading").addClass('loadingInactive');
            bootbox.alert('Error al intentar cargar los datos verifique la información sumistrada en el archivo e intente nuevamente.');            
        },
        cache: false,
    	processData: false,
    	contentType: false
    })

   });




