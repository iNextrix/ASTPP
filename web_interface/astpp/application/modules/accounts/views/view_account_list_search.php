<div class="portlet-content"  id="search_bar" style="cursor:pointer; display:none">
    <?php
    error_reporting(~E_NOTICE);
    $account_search = $this->session->userdata('account_search');
    ?>
    <form action="<?= base_url() ?>accounts/search" id="search_form1" name="search_form" method="POST" enctype="multipart/form-data" style="display:block" class="form">
        <input type="hidden" name="ajax_search" value="1">
        <input type="hidden" name="advance_search" value="1">
        <ul>
            <fieldset  >
                <legend><span style="font-size:14px; font-weight:bold; color:#000;">Search Account</span></legend>
                <li>  
                    <div class="float-left" style="width:30%">
                        <span>
                            <label>Account Number:</label>
                            <input size="20" class="text field" name="account_number" value="<?= @$account_search['account_number'] ?>" > &nbsp;                  
                            <select name="account_number_operator" class="field select">
                                <option value="1">contains</option>
                                <option value="2">doesn't contain</option>
                                <option value="3">is equal to</option>
                                <option value="4">is not equal to</option>
                            </select>
                        </span>
                    </div> 
                    <div class="float-left" style="width:30%">
                        <span>
                            <label>Pricelist :</label>
                        </span>
                    </div>  
                    <div class="float-left" style="width:30%">
                        <span>
                            <label>Firstname:</label>
                            <input size="20" class="text field" name="first_name" value="<?= @$account_search['first_name'] ?>" > &nbsp;
                            <select name="first_name_operator" class="field select">
                                <option value="1">contains</option>
                                <option value="2">doesn't contain</option>
                                <option value="3">is equal to</option>
                                <option value="4">is not equal to</option>
                            </select>
                        </span>
                    </div>
                </li>
                <li>
                    <div class="float-left" style="width:30%">
                        <span>
                            <label>Lastname:</label>
                            <input size="20" class="text field" name="last_name" value="<?= @$account_search['last_name'] ?>"  > &nbsp;
                            <select name="last_name_operator"  class="field select">
                                <option value="1">contains</option>
                                <option value="2">doesn't contain</option>
                                <option value="3">is equal to</option>
                                <option value="4">is not equal to</option>
                            </select>
                        </span>
                    </div>
                    <div class="float-left" style="width:30%">
                        <span>
                            <label>Company:</label>
                            <input size="20" class="text field" name="company" value="<?= @$account_search['company'] ?>" > &nbsp;
                            <select name="company_operator"  class="field select ">
                                <option value="1">contains</option>
                                <option value="2">doesn't contain</option>
                                <option value="3">is equal to</option>
                                <option value="4">is not equal to</option>
                            </select>
                        </span>
                    </div>

                    <div class="float-left" style="width:30%">
                        <span>
                            <label>Balance:</label>
                            <input size="20" class="text field" name="balance" value="<?= @$account_search['balance'] ?>" > &nbsp;
                            <select name="balance_operator" class="field select" style="width:132px;" >
                                <option value="1">is equal to</option>
                                <option value="2">is not equal to</option>
                                <option value="3">greater than</option>
                                <option value="4">less than</option>
                                <option value="5">greather or equal than</option>
                                <option value="6">less or equal than</option>
                            </select>
                        </span>
                    </div>
                </li>

                <li>
                    <div class="float-left" style="width:30%">
                        <span>
                            <label>CreditLimit:</label>
                            <input size="20" class="text field" name="creditlimit" value="<?= @$account_search['creditlimit'] ?>" > &nbsp;
                            <select name="creditlimit_operator" class="field select" style="width:132px;"  >
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
                        <span>
                            <label>Billing Cycle :</label>
                        </span>
                    </div>  
                    <div class="float-left" style="width:30%">
                        <span>
                            <label>PTE:</label>
                            <select class="select field" name="posttoexternal" style="width:307px;" >
                                <option value="" selected="selected" >--Select PTE--</option>
                                <option value="1">YES</option>
                                <option value="0">NO</option>
                            </select>
                        </span>
                    </div>
                </li>
                <li>
                    <div class="float-left" style="width:30%">
                        <span>
                            <label>Account Type:</label>
                        </span>
                    </div>  
                    <div class="float-left" style="width:30%">
                        <span>
                            <label>Country:</label>
                        </span>
                    </div> 

                    <div class="float-left" style="width:30%">
                        <span>
                            <label>Currency:</label>
                        </span>
                    </div>
                </li>


            </fieldset>

        </ul>
        <br />

        <input type="reset" id="id_reset" class="ui-state-default float-right ui-corner-all ui-button" name="reset" value="Clear Search Filter">&nbsp; 
        <input type="button" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Search" id="account_search" style="margin-right:22px;" />
        <br><br>
    </form>
</div>
