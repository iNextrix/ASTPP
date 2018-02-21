	/* Sidebar position - with cookie */

    $(function() {

		$('.sidebar-position a').click(function(){
			var side_id = $(this).attr("id");
			$('body').attr("id",side_id);
			$('.sidebar-position a').removeClass("active");
			$.cookie('side_pos', side_id );
			if($.browser.msie) {
			    location.reload();
			}
			$(this).addClass("active");
		})
			
	    var side_cookie = $.cookie('side_pos');

		$(".sidebar-position a[id="+ side_cookie +"]").addClass("active");

		if (side_cookie == 'sidebar-left') {
			$('body').attr("id","sidebar-left");

	    };

		if (side_cookie == 'sidebar-right') {
			$('body').attr("id","sidebar-right");

	    };

    });
