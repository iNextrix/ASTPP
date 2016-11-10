<script type="text/javascript">
    $("#submit").click(function(){
      submit_form("commission_form");
    })
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
        <?php if(isset($maildata) && $maildata != ''){
		  echo "<div class='col-md-12 no-padding'>Attachments :</div>";
		  $imgArr = explode(",",$maildata);
		  foreach($imgArr as $key => $imgname){
			$imgpath = base_url()."email/email_history_list_attachment/".$imgname;
			echo "<div class='col-md-4 no-padding'>
                  <a href='".$imgpath."'>".$imgname."</a>
                </div>";
		  }
		} ?>          
    </section>
  </div>
</div>

