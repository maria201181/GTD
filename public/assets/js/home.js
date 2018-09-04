window.onload = function() {
    $("#collapseOne").addClass('show');
    $("#home").addClass('active');
    var data = $('.section-filter').find('form').serialize(); 
    $("#date_error").html();

    $.ajax({
        type: 'GET',
        url : 'summary', 
        data : data,           
        success : function (response) {
            if(response.success) {
                $('#total_users').html(response.summary.total_users);
                $('#total_users_active').html(response.summary.total_users_active);
                //$('#total_users_active_unused').html(response.summary.total_users_active_unused);                
                $('#total_users_unused').html(response.summary.total_users_unused);
                $('#total_users_unsuscribe').html(response.summary.total_users_unsuscribe);
                if (response.summary.total_users != 0) {
                    porc_total_users_active = ((response.summary.total_users_active*100)/response.summary.total_users).toFixed(2);
                    //porc_total_users_active_unused = (response.summary.total_users_active_unused*100)/response.summary.total_users;                    
                    porc_total_users_unused = ((response.summary.total_users_unused*100)/response.summary.total_users).toFixed(2);
                    porc_total_users_unsuscribe = ((response.summary.total_users_unsuscribe*100)/response.summary.total_users).toFixed(2);
                }
                else {
                    porc_total_users_active = /*porc_total_users_active_unused =*/ porc_total_users_unsuscribe = porc_total_users_unused = 0;
                }

                var chart = new CanvasJS.Chart("chartContainer", {
                    theme: "light2", // "light1", "light2", "dark1", "dark2"
                    exportEnabled: true,
                    animationEnabled: true,
                    title: {
                         text: "Usabilidad del Sistema"
                    },

                    data: [{
                        type: "pie",
                        startAngle: 25,
                        toolTipContent: "<b>{label}</b>: {y}%",
                        showInLegend: "true",
                        legendText: "{label}",
                        indexLabelFontSize: 16,
                        indexLabel: "{label} - {y}%",
                        dataPoints: [
                            /*{ y: porc_total_users_active, label: "Activos y usan productos" },
                            { y: porc_total_users_active_unused, label: "Activos y no usan productos" },
                            { y: porc_total_users_unsuscribe, label: "Bajas" },
                            { y: porc_total_users_unused, label: "No usan el servicio" }*/
                            { y: porc_total_users_unused, label: "Usuarios no usan el producto" },
                            { y: porc_total_users_active, label: "Usuarios Activos" },
                            { y: porc_total_users_unsuscribe, label: "Bajas" }
                        ]
                    }]
                });
                $("#loading").css('display', 'none');
                chart.render();

            }
            else {                
                //bootbox.alert('Ocurrio un error al calcular el reporte de Usabilidad, porfavor intente nuevamente.');                
                $("#loading").css('display', 'none');
                for (var error in response.errors) {
                    //$("#" + error + '_error').html(data.errors[error][0]);
                    $("#date_error").html(response.errors[error][0]);
                    break;
                }
            }
        },  
        error: function() {
            bootbox.alert('Ocurrio un error al calcular el reporte de Usabilidad, porfavor intente nuevamente.');            
        }
    });

    if ($('#date_from').val() == '' && $('#date_to').val() == '') {
        var now = new Date();
        var day = ("0" + now.getDate()).slice(-2);
        var month = ("0" + (now.getMonth() + 1)).slice(-2);
        var today = now.getFullYear()+"-"+(month)+"-"+(day) ;
        $('#date_from, #date_to').val(today);
    }
}