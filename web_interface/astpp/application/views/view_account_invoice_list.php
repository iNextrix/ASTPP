<? extend('master.php') ?>

<? startblock('extra_head') ?>

<!--flexigrid css & js-->
<link rel="stylesheet" href="<?= base_url() ?>css/flexigrid.css" type="text/css" />
<script type="text/javascript" src="<?= base_url() ?>js/flexigrid.js"></script>

<script type="text/javascript" language="javascript">
    function get_alert_msg(id)
    {
        confirm_string = 'are you sure to delete?';
        var answer = confirm(confirm_string);
        return answer // answer is a boolean
    }
</script>
<?php //echo "<pre>";  print_r(Common_model::$global_config);?>
<script type="text/javascript">
    $(document).ready(function() {
		
			var showOrHide=false;
			$("#search_bar").toggle(showOrHide);
		
        $("#flex1").flexigrid({
            url: "<?php echo base_url(); ?>accounting/account_invoice_grid/",
            method: 'GET',
            dataType: 'json',
            colModel : [
                {display: 'Invoice Numebr', name: 'invoiceid', width: 100, sortable: false, align: 'center'},
                {display: 'Account Number', name: 'accountid', width: 120, sortable: false, align: 'center'},
                {display: 'Account Name', name: 'name', width: 120, sortable: false, align: 'center'},
                {display: 'Account Type', name: 'entity', width: 120, sortable: false, align: 'center'},
                {display: 'Invoice Date', name: 'date', width: 90, sortable: false, align: 'center'},
                {display: 'Invoice Total', name: 'value', width: 100, sortable: false, align: 'center'},
                {display: 'Action', name: '', width : 70, align: 'center', formatter:'showlink', formatoptions:{baseLinkUrl:'', }, },

            ],
            buttons : [
                //		{name: 'Add', bclass: 'add', onpress : add_button},
                //		{separator: true},
                {name: 'Refresh', bclass: 'reload', onpress : reload_button},
                {separator: true},
                {name: 'Remove Search Filter', bclass: 'reload', onpress : clear_filter},
            ],
            nowrap: false,
			
            showToggleBtn: false,
            sortname: "id",
            sortorder: "asc",
            usepager: true,
            resizable: false,
            useRp: true,
            rp: 20,
            showTableToggleBtn: false,
            width: "auto",
            height: 300,
            pagetext: 'Page',
            outof: 'of',
            nomsg: 'No items',
            procmsg: 'Processing, please wait ...',
            pagestat: 'Displaying {from} to {to} of {total} items',
            //preProcess: formatContactResults,
            onSuccess: function(data){
                $('a[rel*=facebox]').facebox({
                    loadingImage : '<?php echo base_url(); ?>/images/loading.gif',
                    closeImage   : '<?php echo base_url(); ?>/images/closelabel.png'
                });
            },
            onError: function(){
                alert("Request failed");
            }
        });
        
        $("#from_date").datetimepicker({ dateFormat: 'yy-mm-dd', rangeSelect: false });
    

        $("#invoice_search").click(function(){

            $.ajax({type:'POST', url: '<? echo base_url() ?>accounting/search',
                data:$('#search_form1').serialize(),
                success: function(response) {
                    $('#flex1').flexReload();
                }
            });
        });
  
        $("#id_reset").click(function(){
 
            // window.location = '<?php echo base_url(); ?>accounting/clearsearchfilter/';
            $.ajax({url:'<?= base_url() ?>accounting/clearsearchfilter', success:function(){
                    $('#flex1').flexReload();	
                }
            });
            //$("#id_account").val('');
            //$("#id_company").val('');
            //$("#id_fname").val('');
            //$("#id_lname").val('');
        });  
		
		$("#show_search").click(function(){
		$("#search_bar").toggle();
		});
  
    });
        
      


    function add_button()
    {
        jQuery.facebox({ ajax: '<?php //echo base_url();  ?>accounting/account_taxes/add'});
    }
    function clear_filter()
    {
        window.location = '<?php echo base_url(); ?>accounting/clearsearchfilter/';
    }
    function delete_button()
    {
        if( confirm("are you sure to delete?") == true)
            return true;
        else
            return false;
    }
    function reload_button()
    {
        $('#flex1').flexReload();
    }

</script>			

<? endblock() ?>

<? startblock('page-title') ?>
<?= $page_title ?><br/>
<? endblock() ?>

<? startblock('content') ?>
<br/>


<!--======================================================================================-->
<? //startblock('content') ?>        
<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" id="searchbar">                        
    <div class="portlet-header ui-widget-header"><span id="show_search" style="cursor:pointer">Search</span><span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div class="portlet-content" id="search_bar">
        <?php
        error_reporting(~E_NOTICE);
        $invoice_search = $this->session->userdata('invoice_search');
        ?>
        <form action="<?= base_url() ?>accounting/search" id="search_form1" name="search_form" method="POST" enctype="multipart/form-data" style="display:block" class="form">
            <input type="hidden" name="ajax_search" value="1">
            <input type="hidden" name="advance_search" value="1">
            <ul>
                <fieldset  >
                    <legend><span style="font-size:14px; font-weight:bold; color:#000;">Search Account</span></legend>
                    <li>  
                        <div class="float-left" style="width:300px;" >
                            <span>
                                <label>Account Number:</label>
                                <input size="20" class="text field" name="account_number" value="<?= @$invoice_search['account_number'] ?>" > &nbsp;                  
                                <a onclick="window.open('<?=base_url()?>accounts/search_did_account_list/' , 'AccountList','scrollbars=1,width=650,height=330,top=20,left=100,scrollbars=1');" href="#"><img src="<?=base_url()?>images/icon_arrow_orange.gif" border="0"></a>
                            </span>
                        </div> 

                        <div class="float-left" style="width:300px;">
                            <span>
                                <label> Invoice Date:</label>
                                <input size="20" class="text field" name="invoice_date" id="from_date"/> 
<!--                                <input size="20" class="text field" name="invoice_date" value="<?= @$invoice_search['first_name'] ?>" > &nbsp;-->
                            </span>
                        </div>

                        <div class="float-left" style="width:400px;">
                            <span>
                                <label>Invoice Total:</label>
                                <input size="20" class="text field" name="creditlimit" value="<?= @$invoice_search['creditlimit'] ?>" > &nbsp;
                                <select name="creditlimit_operator" class="field select" style="width:132px;" >
                                    <option value="1" <?php if (@$invoice_search['creditlimit_operator'] == 1) {
            echo "selected";
        } ?> >is equal to</option>
                                    <option value="2"  <?php if (@$invoice_search['creditlimit_operator'] == 2) {
            echo "selected";
        } ?>>is not equal to</option>
                                    <option value="3"  <?php if (@$invoice_search['creditlimit_operator'] == 3) {
            echo "selected";
        } ?>>greater than</option>
                                    <option value="4"  <?php if (@$invoice_search['creditlimit_operator'] == 4) {
            echo "selected";
        } ?>>less than</option>
                                    <option value="5"  <?php if (@$invoice_search['creditlimit_operator'] == 5) {
            echo "selected";
        } ?>>greather or equal than</option>
                                    <option value="6"  <?php if (@$invoice_search['creditlimit_operator'] == 6) {
            echo "selected";
        } ?>>less or equal than</option>
                                </select>
                            </span>
                        </div>
<?php if ($this->session->userdata['logintype'] == 1 || $this->session->userdata['logintype'] == 5) { ?>
                            <div class="float-left" style="width:300px;"><span>
                                    <label>Invoice Type</label>
                                    <select name="invoice_type" class="field select" style="width:132px;" >
                                        <option value="1" <?php if (@$invoice_search['invoice_type'] == '1') {
        echo "selected";
    } ?> >My Invoices</option>
                                        <option value="2" <?php if (@$invoice_search['invoice_type'] == '2') {
        echo "selected";
    } ?>>My Reseller/Customer Invoices</option>
                                    </select>
                                </span></div>
<? } ?>


                    </li>

                </fieldset>

            </ul>
            <br />

            <input type="button" id="id_reset" class="ui-state-default float-right ui-corner-all ui-button" name="reset" value="Clear Search Filter">&nbsp; 
            <input type="button" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Search" id="invoice_search" style="margin-right:22px;" />
            <br><br>
        </form>
    </div>
</div>        



<? //endblock()  ?>	

<!--======================================================================================-->



<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
    <div class="portlet-header ui-widget-header">Account Invoice List<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div class="portlet-content">
        <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">
            <table id="flex1" align="left" style="display:none;"></table>
        </form>
    </div>
</div>



<?php
//echo $form;
?>
<? endblock() ?>

<? startblock('sidebar') ?>
Filter by
<? endblock() ?>

<? end_extend() ?>  

