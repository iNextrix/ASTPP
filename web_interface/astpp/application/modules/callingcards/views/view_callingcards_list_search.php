<div class="portlet-content"  id="search_bar" style="cursor:pointer; display:none">
    <?php
    error_reporting(~E_NOTICE);
    $callingcard_search = $this->session->userdata('callingcard_search');
    ?>
    <script>
        $(document).ready(function() {
            $("#first_from_date").datetimepicker({ dateFormat: 'yy-mm-dd' });		
            $("#first_to_date").datetimepicker({ dateFormat: 'yy-mm-dd' });	
            $("#creation_from_date").datetimepicker({ dateFormat: 'yy-mm-dd' });		
            $("#creation_to_date").datetimepicker({ dateFormat: 'yy-mm-dd' });			
        });
    </script>
    <form action="<?= base_url() ?>callingcards/cards_search" id="search_form1" name="search_form" method="POST" enctype="multipart/form-data" style="display:block" class="form">
        <input type="hidden" name="ajax_search" value="1">
        <input type="hidden" name="advance_search" value="1">
        <ul style=" list-style:none;">
            <fieldset>
                <legend><span style="font-size:14px; font-weight:bold; color:#000;">Search Calling Card</span></legend>
                <li>
                    <div class="float-left" style="width:30%">
                        <span>
                            <label >Account Number:</label>
                            <input size="20" class="text field" name="account_nummber" id="account_number">
                            <a onclick="window.open('<?= base_url() ?>accounts/search_callingcard_account_list/' , 'AccountList','scrollbars=1,width=650,height=330,top=20,left=100,scrollbars=1');" href="#"><img src="<?= base_url() ?>images/icon_arrow_orange.gif" border="0"></a>
                        </span>
                    </div>
                    <div class="float-left" style="width:30%">
                        <span>
                            <label>Card Number:</label>
                            <input size="20" class="text field" name="card_nummber" id="card_number" />&nbsp;
                            <select name="card_number_operator" class="field select">
                                <option value="1">contains</option>
                                <option value="2">doesn't contain</option>
                                <option value="3">is equal to</option>
                                <option value="4">is not equal to</option>
                            </select>              
                        </span>
                    </div>
                    <div class="float-left" style="width:30%">
                        <span><label>Brand:</label></span>
                    </div>
                </li>
                <li>
                    <div class="float-left" style="width:30%">
                        <label>Balance :</label>
                        <input size="20" class="text field" name="balance"> &nbsp;
                        <select name="balance_operator"  class="field select " style="width:132px;">
                            <option value="1">is equal to</option>
                            <option value="2">is not equal to</option>
                            <option value="3">greater than</option>
                            <option value="4">less than</option>
                            <option value="5">greather or equal than</option>
                            <option value="6">less or equal than</option>
                        </select>
                    </div>  
                    <div class="float-left" style="width:30%">
                        <span><label >Balance Used:</label>
                            <input size="20" class="text field" name="balance_used"> &nbsp;
                            <select name="balance_used_operator"  class="field select" style="width:132px;">
                                <option value="1">is equal to</option>
                                <option value="2">is not equal to</option>
                                <option value="3">greater than</option>
                                <option value="4">less than</option>
                                <option value="5">greather or equal than</option>
                                <option value="6">less or equal than</option>
                            </select>
                        </span>
                    </div>   
                    <div class="float-left" style="width:30%">
                        <label>In Use :</label>
                        <select name="inuse" class="select field" style="width:307px;" >
                            <option value="1" >Yes</option>
                            <option value="0" >No</option>          
                        </select>
                        </span>
                    </div>
                </li>

                <li>
                    <div class="float-left" style="width:30%">
                        <span>
                            <label >Creation Start Date:</label>
                            <input size="20" class="text field" name="creation_start_date" id="creation_from_date">&nbsp;<img src="<?= base_url() ?>images/calendar.png" border="0"> 
                        </span>
                    </div>
                    <div class="float-left" style="width:30%">
                        <span>
                            <label>Creation End Date:</label>
                            <input size="20" class="text field" name="creation_end_date" id="creation_to_date"> &nbsp;<img src="<?= base_url() ?>images/calendar.png" border="0">      
                        </span>
                    </div>
                </li>
                <li>
                    <div class="float-left" style="width:30%">
                        <span>
                            <label >Used Start Date :</label>
                            <input size="20" class="text field" name="first_used_start_date" id="first_from_date">&nbsp;<img src="<?= base_url() ?>images/calendar.png" border="0">  
                        </span>
                    </div>  
                    <div class="float-left" style="width:30%">
                        <span>
                            <label>Used End Date:</label>
                            <input size="20" class="text field" name="first_used_end_date" id="first_to_date">  &nbsp;<img src="<?= base_url() ?>images/calendar.png" border="0">   
                        </span>
                    </div>	
                </li>
            </fieldset>
        </ul>
        <br />
        <input type="reset" id="id_reset" class="ui-state-default float-right ui-corner-all ui-button" name="reset" value="Clear Search Filter">&nbsp; 
        <input type="button" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Search" id="callingcard_search" style="margin-right:22px;" />
        <br><br>
    </form>
</div>
