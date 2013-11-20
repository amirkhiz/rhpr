(function($){
$(document).ready(function() {
	
	$("#form-group").validationEngine();
	$(".chosen").chosen();
	
	$('#user_mobile').mask('999 999 9999',{placeholder:"9"});
	$.mask.definitions['~']='[+-a]';
	$('#user_blood').mask('a~',{placeholder:" "});
	
	// initialise plugin
	$('#categoryMenu').superfish({
		//add options here if required
	});

});
})(jQuery);