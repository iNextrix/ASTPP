<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        build_grid("ipmap_grid","<?=  base_url()?>user/user_ipmap_json",<? echo $ipmap_grid_field; ?>,"");
              	jQuery.validator.addMethod("lettersonly", function(value, element) {
	    return this.optional(element) || /^[a-z]+$/i.test(value);
	  }, "Letters only please");
          
          $.validator.addMethod('IP4Checker', function(value) {
            var pattern = /^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/;
            return pattern.test(value);
        }, 'Invalid IP address');

        $('#ip_map').validate({
            rules: {
		name: {
		    lettersonly: true
		},
                ip: {
                    required: true,
                    IP4Checker: true
                },
                prefix:{
                    number:true
                }
            }
        });
    });
</script>
<style>
    .error{
        width:auto;
        margin-left:40px;
        float:left;
    }
    .text, .field{
        float:left;
    }
    </style>

<? endblock() ?>

<? startblock('page-title') ?>
<?= $page_title ?><br/>
<? endblock() ?>

<? startblock('content') ?>  
<!--    <div id="customer_details">
        <div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
            <div class="portlet-header ui-widget-header">< ?php echo isset($account)?"Edit":"Create New";?> Account
                <?= @$page_title ?>
                <span class="ui-icon ui-icon-circle-arrow-s"></span></div>
            <div style="color:red;margin-left: 60px;">
                <?php if (isset($validation_errors)) {
                    echo $validation_errors;
                } ?> 
            </div>

        </div>
    </div>-->
<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
    <div class="portlet-header ui-widget-header">IP Mapping
        <span id="error_msg" class=" success"></span>
        <span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div class="sub-form">         
<form method="post" name="ip_map" id="ip_map" action="<?= base_url() ?>user/user_ipmap_action/add" enctype="multipart/form-data">
                                        <div style="width:20%">
					  <label class="label_custom">Name:&nbsp;</label>
					  <input class="text field" name="name" style="float:left;" type="text">
					</div>
                                        <div style="width:20%">
					  <label class="label_custom">IP:&nbsp;</label>
					  <input class="text field" name="ip" size="16" style="float:left;"  type="text">
					</div>
                                        <div style="width:20%">
					  <label class="label_custom">Prefix:&nbsp;</label>
					  <input class="text field" name="prefix" size="16" style="float:left;" type="text">
					</div>
                                        <div style="width:60px;">
					  <input class="ui-state-default ui-corner-all ui-button" name="action" value="Map Ip" type="submit">
					</div>
                                    </form>
    </div>
</div>  
<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
    <div class="portlet-header ui-widget-header">IP Map List
        <span id="error_msg" class=" success"></span>
        <span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div class="portlet-content">         
        <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">
            <table id="ipmap_grid" align="left" style="display:none;"></table>
        </form>
    </div>
</div>  

<? endblock() ?>	

<? end_extend() ?>  
