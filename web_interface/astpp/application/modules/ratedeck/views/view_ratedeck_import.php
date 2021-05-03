<script type="text/javascript"
	src="<?php echo base_url(); ?>assets/js/jquery-1.7.1.js"></script>
<script>
    $(document).ready(function() {
        $("#ok").click(function(){
            window.location='/wrongnumber/wrongnumber_list/';
        });
    });
</script>


<section class="slice color-three padding-t-20">
	<div class="w-section inverse no-padding">
		<div class="container">
			<div class="row">
				<form method="post"
					action="<?= base_url() ?>wrongnumber/wrongnumber_bulk_import"
					target="submitter" enctype="multipart/form-data">
					<div class="col-md-12">
						<div class="w-box">
							<span
								style="margin-left: 10px; text-align: center; background-color: none; color: #1c8400;">
                    <?

if (isset($error) && ! empty($error)) {
                        echo $error;
                    }
                    ?>
                 </span>
							<h3 class="padding-t-10 padding-l-16"><?php echo gettext('File must be in the following format:'); ?><br />
		<?php echo gettext('number,description,creation_date,last_modified_date'); ?>
                
                <br />
								<br /><?php echo gettext('The file shall have the text fields escaped with quotation marks and the fields seperated by commas.'); ?></p>
						
						</div>

					</div>
					<div class="col-md-12  no-padding">
						<div class="col-md-6">
							<div class="w-box">
								<h3 class="padding-t-10 padding-l-16 padding-b-10"><?php echo gettext('Import Wrongnumber'); ?></h3>
								<div class="col-md-12 no-padding">
									<label class="col-md-3"><?php echo gettext('Trunk List:'); ?></label>
									<div>
                               <?

$trunklist = form_dropdown('trunk_id', $this->db_model->build_dropdown("id,name", "trunks", "", ""), '');
                            echo $trunklist;
                            ?></div>
								</div>
								<div class="col-md-12 no-padding" s>
									<input type="hidden" name="mode" value="Import Wrongnumber" />
									<input type="hidden" name="logintype"
										value="<?= $this->session->userdata('logintype') ?>" /> <input
										type="hidden" name="username"
										value="<?= $this->session->userdata('username') ?>" /> <label
										class="col-md-3"><?php echo gettext('Select the file:'); ?></label>

									<div class="col-md-5">
										<span class="no-padding form-control"><input
											class="text field large" type="file" name="rateimport"
											size="15" id="rateimport" style="height: 34px;" />
									
									</div>
									</span>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="w-box padding-b-10">
								<h3 class="padding-t-10 padding-l-16 padding-b-10"><?php echo gettext('Download Sample File:'); ?></h3>
								<div class="col-md-12 no-padding">
									<label class="col-md-4"><?php echo gettext('For Download Sample File'); ?></label>
									<div>
										<a
											href="href='<?= base_url(); ?>/wrongnumber/wrongnumber_list/'"
											class="btn btn-success"><i class="fa fa-file-excel-o fa-lg"></i>&nbsp;<?php echo gettext('Click Here'); ?></a>
									</div>
								</div>

							</div>
						</div>
					</div>
					<div class="col-md-12 padding-b-10">
						<div class="pull-right">
							<a href="<?= base_url().'wrongnumber/wrongnumber_list/'?>"><input
								class="btn btn-three" id="ok" type="button" name="action"
								value="Cancel" /></a> <input class="btn btn-primary"
								id="impoddrt_termination1" type="submit" name="action"
								value="Import" />
						</div>
					</div>
			
			</div>
		</div>
	</div>
</section>







<div
	class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
	<div class="portlet-header ui-widget-header">
		<span class="ui-icon ui-icon-circle-arrow-s"></span>
	</div>
	<div class="portlet-content">
		<form method="post"
			action="<?= base_url() ?>wrongnumber/wrongnumber_bulk_import"
			target="submitter" enctype="multipart/form-data">
			<span style="font-size: 12px;">
				</td>
			</tr>
			</span> <br />
			<br /> <input type="hidden" name="mode" value="Import Wrongnumber" />
			<input type="hidden" name="logintype"
				value="<?= $this->session->userdata('logintype') ?>" /> <input
				type="hidden" name="username"
				value="<?= $this->session->userdata('username') ?>" /> <label
				class="desc"><?php echo gettext('Select the file:'); ?></label> <input
				type="file" class="ui-state-default ui-corner-all ui-button"
				name="wrongnumberimport" size="40" />
			<iframe name="submitter" id="submitter" frameborder="0" src=""
				height="100px" width="100%"
				style="background-color: transparent; float: left; display: block">
			</iframe>
			<div
				style="width: 100%; float: left; height: 50px; margin-top: 20px;">
				<input class="ui-state-default float-right ui-corner-all ui-button"
					id="ok" type="button" name="action" value="Cancel" /> <input
					type="submit"
					class="ui-state-default float-right ui-corner-all ui-button"
					name="action" value="Import..." />
			</div>

		</form>

	</div>
</div>

