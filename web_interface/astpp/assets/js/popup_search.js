var old_submenu_val = '';		
$(document).ready(function(){
	
	$("#navigationjj li").removeClass("active");
	
	$("#navigationjj a").each(function(submenuLink){
		if($(this).attr("href") == window.location.href)
		{
			$(this).parent().addClass("active");
		}
	})
	
	$('a[rel*=facebox]').facebox({
	loadingImage : '/assets/images/loading.gif',
	closeImage   : '/assets/images/closelabel.png'
})
	
	$('.hasDatepicker').datepicker({
		changeMonth: true,
		changeYear: true,
		showButtonPanel: true
	});
	
	old_submenu_val = $('#navigationjj').html();
	
	$("#top").mouseover(function(){
		if(old_submenu_val != '')
		$('#navigationjj').html(old_submenu_val);
	});	
	
	$("#body_content").mouseover(function(){
		if(old_submenu_val != '')
		$('#navigationjj').html(old_submenu_val);	
	});
			
});


function fix_sub()
{
	//old_submenu_val = '';
}
function hide_sub()
{
	if(old_submenu_val != '')
		$('#navigationjj').html(old_submenu_val);
}	 
