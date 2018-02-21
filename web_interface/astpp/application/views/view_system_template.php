<? extend('master.php') ?>

	<? startblock('extra_head') ?>
<!--flexigrid css & js-->
<link rel="stylesheet" href="/css/flexigrid.css" type="text/css" />
<script type="text/javascript" src="/js/flexigrid.js"></script>    
<script type="text/javascript" language="javascript">
function get_alert_msg(id)
{
    confirm_string = 'are you sure to delete?';
    var answer = confirm(confirm_string);
    return answer // answer is a boolean
}
</script>

<script type="text/javascript">


$(document).ready(function() {
    var showOrHide=false;
	$("#search_bar").toggle(showOrHide);

$("#flex1").flexigrid({
    
    url: "<?php echo base_url();?>systems/template_grid/",
    method: 'GET',
    dataType: 'json',
	colModel : [
		{display: 'Template Name', name: 'tem_name', width: 120, sortable: true, align: 'center'},
		{display: 'Subject', name: 'subject', width: 130, sortable: false, align: 'center'},
                {display: 'Template', name: 'template', width: 550, sortable: false, align: 'center'},
//                {display: 'Account Number', name: 'accountid', width: 100, sortable: false, align: 'center'},
                {display: 'Account Number', name: 'accountid', width : 100, align: 'center', formatter:'showlink', formatoptions:{baseLinkUrl:'', }, },
                {display: 'Created Date', name: 'Create', width: 100, sortable: false, align: 'center'},
                {display: 'Modified Date', name: 'modified', width: 100, sortable: false, align: 'center'},

                {display: 'Action', name: '', width : 50, align: 'center', formatter:'showlink', formatoptions:{baseLinkUrl:'', }, },

		],
    buttons : [
// 		{name: 'Add', bclass: 'add', onpress : add_button},
		{separator: true},
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
    onSuccess: function(data){
        $('a[rel*=facebox]').facebox({
        	loadingImage : '<?php echo base_url();?>/images/loading.gif',
        	closeImage   : '<?php echo base_url();?>/images/closelabel.png'
      	});
    },
    onError: function(){
        alert("Request failed");
    }
});




$("#template_search").click(function(){
    
	$.ajax({type:'POST', url: '<?=base_url()?>systems/search', data:$('#template_form').serialize(), success: function(response) {

    $('#flex1').flexReload();
}});
	});

$("#id_reset").click(function(){
	$.ajax({url:'<?=base_url()?>systems/clearsearchfilter', success:function(){
	$('#flex1').flexReload();	
	}
	});
//window.location = '<?php echo base_url();?>systems/clearsearchfilter/';
	
});

$("#show_search").click(function(){
	$("#search_bar").toggle();
	});

});

function add_button()
{
	jQuery.facebox({ ajax: '<?php echo base_url();?>systems/template/add'});
}
function delete_button()
{
	confirm_string = '{% trans " you are hiding & stopping a campaign" %}';
    if( confirm("are you sure to delete?") == true)
	return true;
	else 
	return false;
}
function reload_button()
{
    $('#flex1').flexReload();
}
function clear_filter()
{
	window.location = '<?php echo base_url();?>systems/clearsearchfilter/';
}







</script>			
		
	<? endblock() ?>

    <? startblock('page-title') ?>
        <?=$page_title?><br/>
    <? endblock() ?>
    
	<? startblock('content') ?>
<br/>


<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" id="searchbar">                        
            <div class="portlet-header ui-widget-header"><span id="show_search" style="cursor:pointer">Search</span><span class="ui-icon ui-icon-circle-arrow-s"></span></div>
            <div class="portlet-content" id="search_bar">
           
	     <form action="<?=base_url()?>systems/search" id="template_form" name="template_form" method="POST" enctype="multipart/form-data" style="display:block">
         <input type="hidden" name="ajax_search" value="1" />
         <input type="hidden" name="advance_search" value="1">
         <ul style="list-style:none;" >
          <fieldset>
 			<legend><span style="font-size:14px; font-weight:bold; color:#000;">Search Account</span></legend>
             <li>  
             <div class="float-left" style="width:400px;" >
				<span>
                 <label>Template Name</label>
        <input size="20" class="text field" name="template_name" value="<?=@$template_search['template_name']?>" />   &nbsp;                
                  <select name="template_name_operator" class="field select">
                  <option value="1" <?php if(@$template_search['template_name_operator']==1) { echo "selected";}?> >contains</option>
                  <option value="2" <?php if(@$template_search['template_name_operator']==2) { echo "selected";}?>>doesn't contain</option>
                  <option value="3" <?php if(@$template_search['template_name_operator']==3) { echo "selected";}?>>is equal to</option>
                  <option value="4" <?php if(@$template_search['template_name_operator']==4) { echo "selected";}?>>is not equal to</option>
                  </select>
					</span>
		</div> 
              
                
                <div class="float-left" style="width:400px;">
				<span>
               <label>Subject:</label>
               <input size="20" class="text field" name="subject" value="<?=@$template_search['subject']?>" > &nbsp;
               <select name="subject_operator" style="padding:5px" class="field select">
               <option value="1" <?php if(@$template_search['subject_operator']==1) { echo "selected";}?>>contains</option>
               <option value="2" <?php if(@$template_search['subject_operator']==2) { echo "selected";}?>>doesn't contain</option>
               <option value="3" <?php if(@$template_search['subject_operator']==3) { echo "selected";}?>>is equal to</option>
               <option value="4" <?php if(@$template_search['subject_operator']==4) { echo "selected";}?>>is not equal to</option>
               </select>
                </span>
                </div>
                 <div class="float-left" style="width:400px;">
                 <span>
                   <label>Template:</label>
                   <input size="20" class="text field" name="template_desc" value="<?=@$template_search['template_desc']?>"  > &nbsp;
                   <select name="template_operator" style="padding:5px" class="field select">
                   <option value="1" <?php if(@$template_search['template_operator']==1) { echo "selected";}?> >contains</option>
                   <option value="2" <?php if(@$template_search['template_operator']==2) { echo "selected";}?>>doesn't contain</option>
                   <option value="3" <?php if(@$template_search['template_operator']==3) { echo "selected";}?>>is equal to</option>
                   <option value="4" <?php if(@$template_search['template_operator']==4) { echo "selected";}?>>is not equal to</option>
                   </select>
                 </span>
                 </div>

                <?
                if($this->session->userdata['logintype'] == '2')
                {?>

             	 <div class="float-left" style="width:400px;">
                 <span>
                    
                   <label>Account Number:</label>
                   <input size="20" class="text field" name="accountid" value="<?=@$template_search['accountid']?>" > &nbsp;
                  <a onclick="window.open('<?=base_url()?>accounts/search_callingcard_account_list/' , 'AccountList','scrollbars=1,width=650,height=330,top=20,left=100,scrollbars=1');" href="#"><img src="<?=base_url()?>images/icon_arrow_orange.gif" border="0"></a>
				</span>
                 </div>
                 <? }?>
             </li>
             <li>
        
                        
<!--                 <div class="float-left" style="width:400px;">
                <span>
               <label>Search Template Type:</label>
                 <select class="select field" name="template_choice" style="width:285px;" >
                 	<option value="" selected="selected" >--Select Tyep--</option>
                    <option value="1" <?php if(@$template_search['desc']=='1') { echo "selected";}?> >My Template</option>
                    <option value="2" <?php if(@$template_search['desc']=='0') { echo "selected";}?>>My Customer/Reseller Template</option>
                    <option value="3" <?php if(@$template_search['desc']=='0') { echo "selected";}?>>All Template</option>
                  </select>
               </span>
              </div>-->
             </li>
            </fieldset> 
         </ul>
           <br />
             <input type="button" id="id_reset" class="ui-state-default float-right ui-corner-all ui-button" name="reset" value="Clear Search Filter">&nbsp; 
            <input type="button" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Search" id="template_search" style="margin-right:22px;" />
            <br><br>  
        </form>             
          </div>
        </div>  


<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
            <div class="portlet-header ui-widget-header">Email Template<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
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
