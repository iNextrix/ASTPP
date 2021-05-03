<?php include(FCPATH.'application/views/popup_header.php'); ?>
<script type="text/javascript">
    $("#submit").click(function(){
        submit_form("user_sipdevices_form");
    })
    $(".change_pass").click(function(){
	$.ajax({type:'POST',
	    url: "<?= base_url()?>user/user_generate_password",
	    success: function(response) {
		$("input[name=fs_password]").val(response.trim());
	    }
	});
    });
     $(".change_password").click(function(){
            $.ajax({type:'POST',
                url: "<?= base_url()?>user/user_sipdevice_voicemail_random_password",
                success: function(response) {
		    if(response.length > 50){
						$('#password').val("");
						 $("#toast-container_error").css("display","block");
						   	 $(".toast-message").html("Your session has been expired please re-login");
						   	 $('.toast-top-right').delay(900000).fadeOut(40000).fadeIn(2000);
				
						
							 setTimeout(function(){window.location.href = "<?= base_url() ?>"} , 5000); 
		   }else{
                    $('#password').val(response.trim());
                    $('#random_password').val(response.trim());
		   }
                }
            });
        });
    $(".change_number").click(function(){
        $.ajax({type:'POST',
            url: "<?= base_url()?>user/user_generate_number/"+10,
            success: function(response) {
               $("input[name=fs_username]").val(response.trim());
            }
        });
    });
</script>
<section class="slice m-0">
 <div class="w-section inverse p-0 card-header">
       <div class="col-md-12 p-0 card-header">
	        <h3 class="fw4 p-4 m-0"><? echo $page_title; ?></h3 class="bg-secondary text-light p-3 rounded-top">
	  </div>
  </div>    
</section>

<section class="slice m-0">
	<div class="w-section inverse p-4">
               <?php if (isset($validation_errors)) {
					echo $validation_errors;
				}
				?> 
				<?php echo $form; ?>
       </div>      
</section>
<script type="text/javascript" language="javascript">
$(document).ready(function() {

    $("input[type='hidden']").parents('li.form-group').addClass("d-none");
  
  
  
});

</script>
