<? extend('master.php') ?>
<? startblock('extra_head') ?>

<script type="text/javascript">
$(document).ready(function() {

var showOrHide=false;
$("#search_bar").toggle(showOrHide);
	
$("#trunk_stats_report").flexigrid({
    url: "<?php echo base_url();?>statistics/trunkstats_json/",
    method: 'GET',
    dataType: 'json',
	colModel : [
		{display: 'Trunk Name', name: 'Number', width:224,  sortable: false, align: 'center'},
// 		{display: 'Code', name: 'code',width:110, sortable: false, align: 'center'},	
// 		{display: 'Destination', name: 'province',width:170, sortable: false, align: 'center'},
		{display: 'Attempted Calls', name: 'province',width:210, sortable: false, align: 'center'},
		{display: 'Completed Calls', name: 'CompletedCalls',width:240, sortable: false, align: 'center'},			
		{display: '<acronym title="Answer Seizure Rate.">ASR</acronym>', name: 'province',width:200, sortable: false, align: 'center'},		
		{display: '<acronym title="Average Call Duration">ACD</acronym>',width:200, name: 'city',  sortable: false, align: 'center'},     		
		{display: '<acronym title="Maximum Call Duration">MCD</acronym>',width:200, name: 'city',  sortable: false, align: 'center'},
//		{display: 'Billable', width:100,name: 'status',  sortable: false, align: 'center'},
//		{display: 'Cost', width:100,name: 'province',  sortable: false, align: 'center'},
	],

	nowrap: false,
	showToggleBtn: false,
	sortname: "Provider",
	sortorder: "asc",
	usepager: true,
	resizable: true,
	title: '',
	useRp: true,
	rp: 10,
	showTableToggleBtn: false,
	height: "auto",	
	width: "auto",	
	pagetext: 'Page',
	outof: 'of',
	nomsg: 'No Records',
	procmsg: 'Processing, please wait ...',
	pagestat: 'Displaying {from} to {to} of {total} Records',
	onSuccess: function(data){
	},
/*	onError: function(){
	  alert("Request failed");
      }*/
});
$("#id_reset").click(function(){
	$("#start_date").val('');
	$("#end_date").val('');
});
/*
$("#id_reset").click(function(){
    window.location = "<?=base_url()?>statistics/trunkstats/";
});*/
	
    $("#show_search").click(function(){
   //     $("#search_bar").toggle();
	//$("#search_bar").slideToggle("slow");

    });
        

	$('.checkall').click(function () {
            $('.chkRefNos').attr('checked', this.checked); //if you want to select/deselect checkboxes use this
        });
        $("#search_trunkstats").click(function(){
            post_request_for_search("trunk_stats_report","","trunk_stat_search");
	    $("#search_bar").toggle();
        });        
        $("#id_reset").click(function(){ 
            clear_search_request("trunk_stats_report","");
        });

});


function reload_button()
{
    $('#trunk_stats_report').flexReload();
}

</script>	
<script>
    $(document).ready(function() {
        $("#provider_from_date").datetimepicker({ dateFormat: 'yy-mm-dd' });		
        $("#provider_to_date").datetimepicker({ dateFormat: 'yy-mm-dd' });			
    });
</script> 
<? endblock() ?>

<? startblock('page-title') ?>
    <?=$page_title?><br/>
<? endblock() ?>
    
<? startblock('content') ?>  
       

<section class="slice color-three">
	<div class="w-section inverse no-padding">
    	<div class="container">
   	    <div class="row">
            	<div class="portlet-content"  id="search_bar" style="cursor:pointer; display:none">
                    	<?php echo $form_search; ?>
    	        </div>
            </div>
        </div>
    </div>
</section>

<section class="slice color-three padding-b-20">
	<div class="w-section inverse no-padding">
    	<div class="container">
        	<div class="row">
                <div class="col-md-12">      
                        <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">
                            <table id="trunk_stats_report" align="left" style="display:none;"></table>
                        </form>
                </div>  
            </div>
        </div>
    </div>
</section>
<? endblock() ?>	
<? startblock('sidebar') ?>
    <ul id="navigation">
            <li><a href="<?php echo base_url();?>accounts/create/">Create Account</a></li>
            <li><a href="<?php echo base_url();?>accounts/account_list/">List Accounts</a></li>							
    </ul>
    <br/><br/><br/><br/><br/><br/>    	
<? endblock() ?>
<? end_extend() ?>  
