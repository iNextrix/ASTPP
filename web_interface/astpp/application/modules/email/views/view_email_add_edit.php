<script type="text/javascript">
    $("#submit").click(function(){
      submit_form("commission_form");
    })
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

<section class="slice m-0">
	<div class="w-section inverse p-4">
            
                <?php

if (isset($validation_errors)) {
                    echo $validation_errors;
                }
                ?> 
            
            <?php echo $form; ?>
        </div>    
        <?php

if (isset($maildata) && $maildata != '') {
            echo "<div class='col-md-12 no-padding'>".gettext("Attachments")." :</div>";
            $imgArr = explode(",", $maildata);
            foreach ($imgArr as $key => $imgname) {
                $imgpath = base_url() . "email/email_history_list_attachment/" . $imgname;
                echo "<div class='col-md-4 no-padding'>
                  <a href='" . $imgpath . "'>" . $imgname . "</a>
                </div>";
            }
        }
        ?>          
</section>
<script type="text/javascript" language="javascript">
$(document).ready(function() {
    $("input[type='hidden']").parents('li.form-group').addClass("d-none");
    $("textarea").parents('li.form-group').addClass("h-auto");
});
</script>
