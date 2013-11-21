(function($){
$(document).ready(function() {
	
	$("#form-group").validationEngine();
	$(".chosen").chosen();
	
	$('#user_mobile').mask('999 999 9999',{placeholder:"9"});
	$('#user_telephone-1').mask('999 999 9999',{placeholder:"9"});
	$('#user_telephone-2').mask('999 999 9999',{placeholder:"9"});
	$('#user_fax').mask('999 999 9999',{placeholder:"9"});
	
	$.mask.definitions['~']='[+-a]';
	$('#user_blood').mask('a~',{placeholder:" "});
	
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
			
		$("#user_brancha").blur(function(){
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
				$("#user_branch").prop('disabled', false);
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
		$('#LoginBtn').click(function(){
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
	});

});
})(jQuery);