$(document).ready(function() {
	
	$("#catSearchBox").keyUp(function(){
		var searchVal = $(this).val();
		var ajaxurl = makeUrl({module: "category", action: "searchTxtList"});
		//var ajaxurl = "http://localhost/rhpr/www/userAjax/ajax.php";
		var table = $(this).attr('data');

		console.log(ajaxurl); return false;
		$.ajax({
			url: ajaxurl,
			cache: false,
            type: 'POST',
			//data: {'action':'getAddr', 'frmOpt':cityId,'frmTitle':'region','frmTable':table},
			success: function(data){
				console.log(data);
				//$("#regionList").html(data);
			},
			error: function(){
				console.log('Error in region List');
			}
		});
		
	});
	
	$("#form-group").validationEngine();
	$(".chosen").chosen();
	
});
