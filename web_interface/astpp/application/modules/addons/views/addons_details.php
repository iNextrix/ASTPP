<? extend('left_panel_master.php') ?>
<? startblock('extra_head') ?>
<?php endblock() ?>
<? startblock('page-title') ?>
<?= $page_title ?>
<? endblock() ?>
<?php startblock('content') ?>
<script>
function install_addon(){
	$(".overlay").show();
}
</script>

<div id="main-wrapper">
	<section class="slice color-three py-4">
		<div class="w-section inverse no-padding">
			<div class="container-fluid">
				<div class="addons">
					<div class="card col-md-10 mx-auto p-0">
						<div class="card-header">
							<div class="col-12">
								<a class="btn btn-secondary float-right"
									href="<?php echo base_url();?>addons/addons_list/<?php echo $type;?>"><?php echo gettext("Back"); ?></a>
							</div>
						</div>

						<div class="col-md-12 addon_details">
							<div class="col-md-6 col-sm-12 mt-4 float-left p-0">
								<div class="card flex-row flex-wrap">
									<div class="col-3 px-3 py-3">
										<img
											src="<? echo base_url()?>addons/<?=$type?>/<?=$package_name?>/static/images/<?= $package_name?>_icon.png"
											class="img-fluid img-rounded" alt="icon">
									</div>
									<div class="col py-3 pr-3 pl-0">
										<div class="card-body px-3 py-0">
											<h3 class="text-dark card-title fw4"><?php echo $addon_name;?></h3>
											<p class="card-text m-0"><?php echo $description;?></p>
											<p class="py-2 m-0"><?php echo gettext("by"); ?> <strong
													class="text-secondary addon_strong"><?php echo $author;?></strong>
											</p>

											<div class="float-right col-6 pr-0">                                      
								<?
        if ($version_error == 'false') {
            if ($update_flag == 'true') {
                ?>
										<a
													href="<?= base_url() ?>addons/addons_install/<?= $type."/".$package_name ?>/update/<?= $new_version."/".$old_version ?>"
													class="btn btn-info btn-block" onclick='return install_addon()'><?php echo gettext("Update"); ?></a>
												<a
													href="<?= base_url() ?>addons/addons_install/<?php echo $type;?>/<?= $package_name ?>/uninstall/"
													class="btn btn-light border btn-block" onclick='return install_addon()'><?php echo gettext("Uninstall"); ?></a>
									<?

} else {
                if ($new_version == $old_version) {
                    ?>
												<a
													href="<?= base_url() ?>addons/addons_install/<?php echo $type;?>/<?= $package_name ?>/uninstall/"
													class="btn btn-light border btn-block" onclick='return install_addon()'><?php echo gettext("Uninstall"); ?></a>
										<?  }else{?>
												<?php 
												$request_uri=explode("/",$_SERVER['REQUEST_URI']);
												if (isset($request_uri) && $request_uri[3]=='Enterprise') {?>
														<a href="<?= base_url() ?>addons/addons_enterprise_license/<?= $type."/".$package_name ?>/install/<?= $new_version."/".$old_version ?>" class="btn btn-info btn-block" rel="facebox" >Install</a> 
													<?php }else{?>
															<a href="<?= base_url() ?>addons/addons_install/<?= $type."/".$package_name ?>/install/<?= $new_version."/".$old_version ?>" class="btn btn-info btn-block" onclick='return install_addon()'>Install</a> 
												 <?php }
									

}
            }
        } else {
            $error_msg = gettext("Addon source code is older than your installed version.");
            ?>
								<?}?>
                </div>
										</div>
									</div>
								</div>
							</div>

							<div class="col-md-3 col-sm-12 mt-4 float-right p-0">
								<dl class="row m-0">
									<dt class="col-6 text-md-right p-0"><?php echo gettext("Version"); ?></dt>
									<dd class="col text-right p-0"><?php echo $new_version;?></dd>
									<dt class="col-6 text-md-right p-0"><?php echo gettext("License"); ?></dt>
									<dd class="col text-right p-0"><?php echo $license;?></dd>
								</dl>
								<!--
                       <dl class="row m-0">
                        <dt class="col-9">Dependencies</dt>
                        <dd class="">1</dd>
                      </dl> 
-->
							</div>
						</div> 
                  <?php if(isset($error_msg) && $error_msg != ""){?>
					<div class="col-md-12 mt-4">
							<div class="alert alert-danger">
						<?php echo gettext("	Addon source code is older than your installed version."); ?>
						</div>
						</div>  
				  <?}?>
                  
					<?php
    $contents = file_get_contents(FCPATH . "/addons/" . $type . "/" . $package_name . "/static/index.html");
    $contents = str_replace("#BASEURL#", base_url(), $contents);
    echo $contents;
    ?>

               

            </div>
				</div>
			</div>
	
	</section>
</div>

<? endblock() ?>  

<? end_extend() ?>
<script type="text/javascript">
  $(document).ready(function(){
      $('.page-wrap').addClass('addon_wrap');
      
      $(function(){
			var current = location.pathname;
			$('.sidemenu li a').each(function(){
				var $this = $(this);
				// if the current path is like this link, make it active
				if($this.attr('href').indexOf(current) !== -1){
					$this.parents('.sidemenu li ').addClass('active');
				}
			})
		})
  });
</script>
