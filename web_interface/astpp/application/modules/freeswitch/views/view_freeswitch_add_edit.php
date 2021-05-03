<?php include(FCPATH.'application/views/popup_header.php'); ?>
<link rel="stylesheet"
	href="<?php echo base_url(); ?>assets/css/flexigrid.css"
	type="text/css">
<link href="<?php echo base_url(); ?>assets/css/facebox.css"
	rel="stylesheet" media="all" />
<script type="text/javascript">
    $("#submit").click(function(){
        submit_form("sipdevices_form");
    });
     $(".change_pass").click(function(){
            $.ajax({type:'POST',
                url: "<?= base_url()?>accounts/customer_sipdevice_random_password",
                success: function(response) {
					if(response.length > 50){
						$('#password').val("");
						 $("#toast-container_error").css("display","block");
						   	 $(".toast-message").html("Your session has been expired please re-login");
						   	 $('.toast-top-right').delay(900000).fadeOut(40000).fadeIn(2000);
				
						
							 setTimeout(function(){window.location.href = "<?= base_url() ?>"} , 5000); 
					}else{
						$('#password').val(response.trim());
						$('#password1').val(response.trim());
					}
                }
            });
        })

      $(".change_password").click(function(){
            $.ajax({type:'POST',
                url: "<?= base_url()?>accounts/customer_sipdevice_voicemail_random_password/"+5,
                success: function(response) {
		    if(response.length > 50){
						$('#password').val("");
						 $("#toast-container_error").css("display","block");
						   	 $(".toast-message").html("Your session has been expired please re-login");
						   	 $('.toast-top-right').delay(900000).fadeOut(40000).fadeIn(2000);
				
						
							 setTimeout(function(){window.location.href = "<?= base_url() ?>"} , 5000); 
		   } else {
                    $('#password').val(response.trim());
                    $('#random_password').val(response.trim());
		   }
                }
            });
        })


    $(".change_number").click(function(){
        $.ajax({type:'POST',
            url: "<?= base_url()?>accounts/customer_sipdevice_number/"+10,
            success: function(response) {
		if(response.length > 50){
						$('#username').val("");
						 $("#toast-container_error").css("display","block");
					   	 $(".toast-message").html("Your session has been expired please re-login");
					   	 $('.toast-top-right').delay(900000).fadeOut(40000).fadeIn(2000);
					
						 setTimeout(function(){window.location.href = "<?= base_url() ?>"} , 5000); 
		   }else{
						$('#username').val(response.trim());
						$('#username1').val(response.trim());
		   }

            }
        });
    })

	function account_change_add(val){  
	      	$.ajax({
		type: "POST",
		url: "<?= base_url()?>/accounts/customer_account_change/"+val,
		data:'',
		success:function(alt) { 
			  $("#accountcode").html(alt);    
       $('.selectpicker').selectpicker('refresh');
		}
	});
}
</script>
<script type="text/javascript">
function account_change(val){
		var reseller_id=val;
		$.ajax({
			type: "POST",
			url: "<?= base_url()?>/accounts/customer_account_change/"+reseller_id,
			data:'',
			success:function(alt) {
				$("#account_code").html(alt);    
			}
		});
}
</script>
<script type="text/javascript">
  $(document).ready(function(){
      $(".breadcrumb li a").removeAttr("data-ripple",""); 
  });
</script>

<section class="slice m-0">
	<div class="w-section inverse p-0">
		<div>
			<div>
				<div class="col-md-12 p-0 card-header">
					<h3 class="fw4 p-4 m-0"><? echo $page_title; ?></h3 class="text-light p-3 rounded-top">
				</div>
			</div>
		</div>
	</div>
</section>

<div>
	<div>
		<section class="slice m-0">
			<div class="w-section inverse p-4">
				<div style="">
                <?php

if (isset($validation_errors)) {
                    echo $validation_errors;
                }
                ?> 
            </div>
            <?php echo $form; ?>
        </div>
		</section>
	</div>
</div>

<script type="text/javascript" language="javascript">
$(document).ready(function() {
    $("input[type='hidden']").parents('li.form-group').addClass("d-none");
});
</script>
<script type="text/javascript">
  $(document).ready(function(){
      $('.page-wrap').addClass('addon_wrap');
  });
</script>
