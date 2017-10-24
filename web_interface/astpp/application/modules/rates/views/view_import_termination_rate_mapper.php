<?php
   extend('master.php') ?>
<?php
   startblock('extra_head') ?>
   <? //echo "<pre>";print_r($csv_tmp_data);exit;
   if (isset($csv_tmp_data) && !empty($csv_tmp_data)) {
   echo '<script>';
   echo 'var csv_tmp_data = ' . json_encode($csv_tmp_data) . ';';
  //  echo 'alert(csv_tmp_data);';
   echo '</script>';
   }
    ?>
<script type="text/javascript" language="javascript">
   $(document).ready(function() {
   });
</script> <script type="text/javascript" language="javascript"><?
   if (isset($mapto_fields) && !empty($mapto_fields)) {
    foreach($mapto_fields as $csv_key => $csv_value) {
    echo '$("#'.$csv_value .'-prefix").live("change", function () {';
    echo 'var select = document.getElementById("'.$csv_value .'-select");';
    echo 'var answer = select.options[select.selectedIndex].value;';
    echo 'document.getElementById("'.$csv_value .'-display").value =  (!answer) ? document.getElementById("'.$csv_value .'-prefix").value : document.getElementById("'.$csv_value .'-prefix").value + csv_tmp_data[2][answer];';
    echo '});';
    echo '$("#'.$csv_value .'-select").live("change", function () {';
    echo 'var select = document.getElementById("'.$csv_value .'-select");';
    echo 'var answer = select.options[select.selectedIndex].value;';
   
    echo 'document.getElementById("'.$csv_value .'-display").value = (!answer) ? document.getElementById("'.$csv_value .'-prefix").value : document.getElementById("'.$csv_value .'-prefix").value + csv_tmp_data[2][answer];';
    echo '});';
    }
    }
   ?></script>

<?php
   endblock() ?>
<?php
   startblock('page-title') ?>
<?php echo $page_title
   ?>
<br/>
<?php
   endblock() ?>
<?php
   startblock('content') ?>
<?php
   if (!isset($csv_tmp_data)) { ?>
<section class="slice color-three padding-t-20">
   <div class="w-section inverse no-padding">
      <div class="container">
         <form method="post" action="<?php echo base_url() ?>rates/termination_rate_mapper_preview_file/" enctype="multipart/form-data" id="termination_rate">
            <div class="row">
               <div class="col-md-12">
                  <div class="w-box">
                     <span  style="margin-left:10px; text-align: center;background-color: none;color:#DD191D;">
                     <?php
                        if (isset($error) && !empty($error)) {
                        	echo $error;
                        } ?>
                     </span>
                     <h3 class="padding-l-16">You must either select a field from your file OR provide a default value for the following fields:</h3>
                     <p>Code,Destination,Connect Cost,Included Seconds,Per Minute Cost,Increment,Precedence,Strip,Prepend.</p>
                  </div>
               </div>
               <div class="col-md-12  no-padding">
                  <div class="col-md-6">
                     <div class="w-box trunklist">
                        <h3 class="padding-t-10 padding-l-16 padding-b-10">Import Termination Rates:</h3>
                        <div class="col-md-12 no-padding">
                           <label class="col-md-3">Trunk List:</label>
                           <div class="">
                              <?php
                                 $trunklist = form_dropdown('trunk_id', $this->db_model->build_dropdown("id,name", "trunks", "where_arr", array(
                                 	"status " => "0"
                                 )) , '');
                                 echo $trunklist; ?>
                           </div>
                        </div>

                        <div class="col-md-12 no-padding">
                           <input type="hidden" name="mode" value="import_termination_rate_mapper" />
                           <input type="hidden" name="logintype" value="<?= $this->session->userdata('logintype') ?>" />
                           <input type="hidden" name="username" value="<?= $this->session->userdata('username') ?>" />
                           <label class="col-md-3">Select the file:</label>
                           <div class="col-md-5 no-padding">
                              <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                 <div class="form-control" data-trigger="fileinput">
                                    <span class="fileinput-filename"></span>
                                 </div>
                                 <span class="input-group-addon btn btn-primary btn-file" style="display: table-cell;">
                                 <span class="fileinput-new">Select file</span>
                                 <input style="height:33px;" type="file" name="termination_rate_import_mapper"  size="15" id="termination_rate_import_mapper"></span>
                              </div>
                           </div>
                        </div>
                      <!--  <label class="col-md-3">File has Header Record.:</label>
                        <div class="col-md-1">
                           <input type='checkbox' name='has_header'/>
                        </div>-->
                     </div>
                  </div>
               </div>
            </div>
            <div class="col-md-12 padding-b-10">
               <div class="pull-right">
                  <input class="btn btn-line-parrot" id="import_terminationrate" type="submit" name="action" value="Import" />
                  <a href="<?php echo base_url() . 'rates/termination_rates_list/' ?>" >
                  <input class="btn btn-line-sky margin-x-10" id="ok" type="button" name="action" value="Cancel"/>
                  </a>
               </div>
            </div>
         </form>
      </div>
   </div>
   </div>
</section>
<?php
   } ?>
<?php // echo "<pre>";	print_R($csv_tmp_data);exit;
   if (isset($csv_tmp_data) && !empty($csv_tmp_data)) { ?>
<section class="slice color-three padding-b-20">
   <div class="w-section inverse no-padding">
      <div class="container">
         <div class="row">
         </div>
      </div>
      <div class="row">
         <div class="col-md-12">
            <form id="import_form" name="import_form" action="<?php echo base_url() ?>rates/termination_rate_rates_mapper_import" enctype="multipart/form-data" method="POST">
               <table class="table">
                  <thead>
                     <tr>
                        <th>ASTPP Field</th>
                        <th>PREFIX/DEFAULT VALUE</th>
                        <th>Map to Field</th>
                        <th>Data Example</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php  //echo "<pre>";	print_R($mapto_fields);exit;
                        foreach($mapto_fields as $csv_key => $csv_value) {

                        		echo "<tr>";
                        		echo "<td>" . $csv_key . '(' . $csv_value . ")</td>";
                        		echo "<td><input type='text' name='".$csv_value ."-prefix' id='".$csv_value ."-prefix'></td>";
                        		echo "<td>";
                        		echo "<select name='".$csv_value ."-select' id='".$csv_value ."-select'>";
                        		?>
                     <option value=""></option>
                     <?php
                        $keys = array_keys($file_data);
                        for ($i = 0; $i < count($file_data); $i++) { ?>
                     <option value="<?php
                        echo $file_data[$i]; ?>"><?php
                        echo $file_data[$i]; ?></option>
                     <?php
                        }
                        echo "</td>";
                        echo "<td><input type='text' name='".$csv_value ."-display' id='".$csv_value ."-display'></td>";
                        echo "</tr>";

                        } ?>
                  </tbody>
               </table>
               <input type="hidden" name="trunkid" value="<?php echo $trunkid ?>" />
               <input type="hidden" name="check_header" value="<?php echo $check_header ?>" />
               <input type="hidden" name="mode" value="import_termination_rate_mapper" />
               <input type="hidden" name="filefields" value="<?php echo htmlspecialchars($field_select); ?>"  />
               <input type="hidden" name="logintype" value="<?php echo $this->session->userdata('logintype') ?>" />
               <input type="hidden" name="username" value="<?php echo $this->session->userdata('username') ?>" />
               <H2> Import File Data..</H2>
               <table width="100%" border="1"  class="details_table">
                  <?php
                     $cnt = 1;  //echo "<pre>";print_r($csv_tmp_data);exit;
                     foreach($csv_tmp_data as $csv_key => $csv_value) {
                   
                     	if ($csv_key < 15) {
                     		echo "<tr>";
                     		
                     		foreach($csv_value as $field_name => $field_val) {
                     			if ($csv_key == 0) {

                     				 echo "<th>".ucfirst($field_val)."</th>";

                     			}
                     			else {
                     				echo "<td class='portlet-content'>" . $field_val . "</td>";
                     				$cnt++;
                     			}
                     		}

                     		echo "</tr>";
                     	}
                     }

                     echo "<tr><td colspan='" . $cnt . "'>
                                            <a href='" . base_url() . "rates/termination_rate_list/'><input type='button' class='btn btn-line-sky pull-right  margin-x-10' value='Back'/></a>
                                            <input type='submit' class='btn btn-line-parrot pull-right'' id='Process' value='Process Records'/></td></tr>";
                     ?>
               </table>
            </form>
         </div>
      </div>
   </div>
   </div>
</section>
<?php
   } ?>
<?php
   endblock() ?>
<?php
   end_extend() ?>
