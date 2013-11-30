(function($){
$(document).ready(function() {
	
	var branchsShowClick = true;
	$('#profileBranchBtn').click(function(){
		var companyId = $(this).attr('data');
		var ajaxurl = "http://localhost/rhpr/www/userAjax/ajax.php";
		var table = $(this).attr('data');
		var editUrl = makeUrl({module: "company", manager: "branch", action: "edit"});

		//console.log(editUrl); return false;
		if (branchsShowClick) 
			$.ajax({
				url: ajaxurl,
				cache: false,
				type: 'POST',
				data: {
					'action': 'getBranch',
					'frmCompanyID': companyId,
					'frmEdit': editUrl
				},
				success: function(data){
					console.log(data);
					$("#profileBranchBox").html(data);
					$("#profileBranchBox").show().animate({
						height: "377px",
						top: "-377px",
						opacity: 1
					}, {queue:true, duration: 500});
				},
				error: function(){
					console.log('Error in region List');
				}
			});
		else {
			$("#profileBranchBox").animate({
				height: "0",
				top: "0",
				opacity: 0
			}, {queue:true, duration: 500});
			//$("#profileBranchBox").fadeOut(1);
		}
		branchsShowClick = !branchsShowClick;
	});
	
	$(".citySelect").change(function(){
		var cityId = $(this).val();
		//var ajaxurl = makeUrl({module: "company", action: "getAddr"});
		var ajaxurl = "http://localhost/rhpr/www/userAjax/ajax.php";
		var table = $(this).attr('data');

		//console.log(ajaxurl); return false;
		$.ajax({
			url: ajaxurl,
			cache: false,
            type: 'POST',
			data: {'action':'getAddr', 'frmOpt':cityId,'frmTitle':'region','frmTable':table},
			success: function(data){
				//console.log(data);
				$("#regionList").html(data);
			},
			error: function(){
				console.log('Error in region List');
			}
		});
		
	});
	
	$(document).on("change", ".regionSelect", function(){
		var regionId = $(this).val();
		var ajaxurl = "http://localhost/rhpr/www/userAjax/ajax.php";
		var table = $(this).attr('data');
		
		//console.log(ajaxurl); return false;
		$.ajax({
			url: ajaxurl,
			cache: false,
            type: 'POST',
			data: {'action':'getAddr', 'frmOpt':regionId,'frmTitle':'village','frmTable':table},
			success: function(data){
				//console.log(data);
				$("#villageList").html(data);
			},
			error: function(){
				console.log('Error in region List');
			}
		});
		
	});
	
	$("#form-group").validationEngine();
	$(".chosen").chosen();
	
	$('#company_mobile').mask('999 999 9999',{placeholder:"9"});
	$('#company_telephone-1').mask('999 999 9999',{placeholder:"9"});
	$('#company_telephone-2').mask('999 999 9999',{placeholder:" "});
	$('#company_fax').mask('999 999 9999',{placeholder:" "});
	
	$.mask.definitions['~']='[+-a]';
	$('#company_blood').mask('a~',{placeholder:" "});
	
	$('.mobile').mask('999 999 9999',{placeholder:"9"});
	$('.tel').mask('999 999 9999',{placeholder:"9"});
	$('.tel2').mask('999 999 9999',{placeholder:" "});
	$('.fax').mask('999 999 9999',{placeholder:" "});
	
	// initialise plugin
	$('#categoryMenu').superfish({
		//add options here if required
	});
	
	$(function(){
		
		var tabTitle = $( "#tab_title" ),
			tabContent = $( "#tab_content" ),
			tabTemplate = "<li><a href='#{href}'>#{label}</a> <span class='ui-icon ui-icon-close' role='presentation'>Remove Tab</span></li>",
			tabCounter = 2;
			
		var tabs = $( "#tabs" ).tabs();
			
		$("#company_brancha").blur(function(){
			var branchCount = parseInt($(this).val());
			
			if (branchCount <= 5)
			{
				
				for(i = 1; i <= branchCount; i++)
				{
					addTab();
					//$("#tabs").fadeIn(500);
				}
				console.log('aaaaa');
				$(this).prop('disabled', true);
				$("html, body").animate({
					scrollTop: 0
				}, 600);
			}
		});
		
		// actual addTab function: adds new tab using the input from the form above
	    function addTab() {
			var label = "Branch " + String(parseInt(tabCounter) - 1) ,
				id = "tabs-" + tabCounter,
				li = $( tabTemplate.replace( /#\{href\}/g, "#" + id ).replace( /#\{label\}/g, label ) ),
				tabContentHtml = $("#branchData").html();
			
			tabs.find( ".ui-tabs-nav" ).append( li );
			tabs.append( "<div id='" + id + "'>" + tabContentHtml + "</div>" );
			tabs.tabs( "refresh" );
			tabCounter++;
	    }
		
		// close icon: removing the tab on click
		tabs.delegate( "span.ui-icon-close", "click", function() {
			var panelId = $( this ).closest( "li" ).remove().attr( "aria-controls" );
			tabCounter--;
			if (tabCounter == 2){
				$("#company_branch").prop('disabled', false);
			}
			$( "#" + panelId ).remove();
			tabs.tabs( "refresh" );
		});
		
	});
	
	$(function(){
		var breyselClick = true;
		$('#breyselLoginBtn').click(function(){
			if (breyselClick)
				$('#breyselLogin').hide().fadeIn(500);
			else
				$('#breyselLogin').fadeOut(500);
			breyselClick = !breyselClick;
		});
		
		var kurumsalClick = true;
		$('#kurumsalLoginBtn').click(function(){
			if (kurumsalClick)
				$('#kurumsalLogin').hide().fadeIn(500);
			else
				$('#kurumsalLogin').fadeOut(500);
			kurumsalClick = !kurumsalClick;
		});
		
		var loginClick = true;
		$('#loginBtn').click(function(){
			if (loginClick)
				$("#login").animate({
				    width: "180px",
					height: "150px",
					opacity: 1
				  }, 500 );
			else
				$("#login").animate({
				    width: "0",
					height: "0",
					opacity: 0
				  }, 500 );
			loginClick = !loginClick;
		});
		
		var logoutClick = true;
		$('#logoutBtn').click(function(){
			if (logoutClick)
				$("#logout").animate({
				    width: "77px",
					height: "76px",
					opacity: 1
				  }, 500 );
			else
				$("#logout").animate({
				    width: "0",
					height: "0",
					opacity: 0
				  }, 500 );
			logoutClick = !logoutClick;
		});
	});

});
})(jQuery);