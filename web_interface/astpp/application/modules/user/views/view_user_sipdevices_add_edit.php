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
    $(".change_number").click(function(){
        $.ajax({type:'POST',
            url: "<?= base_url()?>user/user_generate_number/"+10,
            success: function(response) {
               $("input[name=fs_username]").val(data.trim());
            }
        });
    });
</script>
<section class="slice gray no-margin">
 <div class="w-section inverse no-padding">
   <div>
     <div>
        <div class="col-md-12 no-padding margin-t-15 margin-b-10">
	        <div class="col-md-10"><b><? echo $page_title; ?></b></div>
	  </div>
     </div>
    </div>
  </div>    
</section>

<div>
  <div>
    <section class="slice color-three no-margin">
	<div class="w-section inverse no-padding">
            <div style="color:red;margin-left: 60px;">
                <?php if (isset($validation_errors)) {
	echo $validation_errors;
}
?> 
            </div>
            <?php echo $form; ?>
        </div>      
    </section>
  </div>
</div>

