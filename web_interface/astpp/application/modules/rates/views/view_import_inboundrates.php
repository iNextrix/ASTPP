<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery-1.7.1.js"></script>
<script>
    $(document).ready(function() {
        $("#ok").click(function(){
            window.location='/rates/origination_list/';
        });
    });
</script>

<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
    <!--<div class="portlet-header ui-widget-header">Import Routes<span class="ui-icon ui-icon-circle-arrow-s"></span></div>-->
    <div class="portlet-content">
        <form method="post" action="/rates/origination_import_file/" enctype="multipart/form-data" target="submitter" id="termination_rates">
            <div class="column" style="padding: 5px 7px 0px 0px;width: 100%;">
                <div class="content-box content-box-header ui-corner-all float-left full">
                    <div class="portlet-header ui-widget-header">Instructions:<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
                    <div class="portlet-content">
                        <div class="sub-form">
                            <span style="font-size:12px; line-height: 20px; font-family: arial;">
                                <p align="justify"><b>File must be in the following format:</b><br />
                                    Code,Destination,Connect Cost,Included Seconds,Per Minute Cost,Increment,Precedence.<br/>
                                </p>
                            </span>
                        </div>                            
                    </div>
                </div>
            </div>
        <div class="two-column" style="float:left;width: 100%;">
            <div class="column" style="padding: 5px 7px 0px 0px;width: 57%;">
                <div class="content-box content-box-header ui-corner-all float-left full">
                    <div class="portlet-header ui-widget-header">Import Origination Rates:<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
                    <div class="portlet-content">
                        <div class="sub-form">
                            <label>&nbsp;&nbsp;&nbsp;Rate Group:</label>  
                            <? $pricelists = form_dropdown('pricelist_id', $this->db_model->build_dropdown("id,name", " pricelists", "where_arr", array("reseller_id" => "0", "status <>" => "2")), '');
                            echo $pricelists;
                            ?>                
                            <br/><br/>
                            <input type="hidden" name="mode" value="Import Routes" />
                            <input type="hidden" name="logintype" value="<?= $this->session->userdata('logintype') ?>" />
                            <input type="hidden" name="username" value="<?= $this->session->userdata('username') ?>" />
                            <div><label style="width:10%;">Select the file:</label>
                                <input class="text field large" type="file" name="rateimport"  size="15" id="rateimport" style="widht:15px;"/>
                            </div>
                        </div>                            
                    </div>
                </div>
            </div>
            <div class="column column-right" style="padding: 5px 7px 0px 0px;width: 37%;">
                <div class="content-box content-box-header ui-corner-all float-left full" style="height:125px;">
                    <div class="portlet-header ui-widget-header">Download Sample File:<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
                    <div class="portlet-content">
                        <div class="sub-form">
                            For Download Sample File <a href="<?= base_url(); ?>rates/customer_rates_download_sample_file/originationrates_sample"><img src="/images/excel.gif"/><u>Click Here.</u></a>            
                        </div>                            
                    </div>
                </div>
            </div>
        </div>
        <div class="column" style="padding: 5px 7px 0px 0px">
            <div class="portlet-content">
                <iframe name="submitter" id="submitter" frameborder="0" src=""  width="100%" style="background-color:transparent; float:left; display:block; height:235px;">
                </iframe>
                <input class="ui-state-default float-right ui-corner-all ui-button" id="ok" type="button" name="action" value="Close" />    
                <input class="ui-state-default float-right ui-corner-all ui-button" id="impoddrt_termination1" type="submit" name="action" value="Import..." />            
            </div>
        </div>
    </form>
</div>
</div>

