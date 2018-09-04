function cleanFields() {
	$('input[name!="_token"]').val("");
	$("select").val("");
	$("input:radio:first").attr("checked", true);
	$(".help-block .error").html("");
}

$(document).ready(function() {

	$('.pagination li').addClass('page-item');
	$('.pagination li a').addClass('page-link');
	$('.pagination span').addClass('page-link');

	$(document).on('click','.btn-clear',function(e) {
		e.preventDefault();
		cleanFields(); 
	}); 
    
    $(".number").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 || (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
            (e.keyCode >= 35 && e.keyCode <= 40)) {                
                return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
});
