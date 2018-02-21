<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery-1.7.1.js"></script>
<script>
    $(document).ready(function() {
        $("#import_termination1").click(function(){
            var dataString = $("#termination_rates").serialize();
            $.ajax({
                url: "<?= base_url(); ?>/rates/terminationrates_rates_import/",
                type: "POST",
                data: dataString,
                async: true,
                enctype: 'multipart/form-data',
                success: function(data){ 
                    if(data)
                    {
                        alert(data);
                        return false;
                    } else{
        		  
                    }
                },
                cache: false
            });
              
            
        });        
        $("#ok").click(function(){
            window.location='/rates/terminationrates_list/';
        });
        
        
    });
</script>

<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" >                        
    <div class="portlet-header ui-widget-header">LCR Import Termination Rates<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div class="portlet-content">	
        <!--            -->
        <form method="post" action="/rates/terminationrates_rates_import/" name="termination_rates" id="termination_rates" target="submitter" enctype="multipart/form-data">
            <div class="column" style="padding: 5px 7px 0px 0px;width: 100%;">
                <div class="content-box content-box-header ui-corner-all float-left full">
                    <div class="portlet-header ui-widget-header">Instructions:<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
                    <div class="portlet-content">
                        <div class="sub-form">
                            <span style="font-size:12px; line-height: 20px; font-family: arial;">
                                <p align="justify"><b>File must be in the following format:</b><br />
                                    Code,Prepend,Destination,Connect Cost,Included Seconds,Per Minute Cost,Increment,Precedence.<br/>
                                </p>
                            </span>
                        </div>                            
                    </div>
                </div>
            </div>


        <div class="two-column" style="float:left;width: 100%;">
            <div class="column" style="padding: 5px 7px 0px 0px;width: 57%;">
                <div class="content-box content-box-header ui-corner-all float-left full">
                    <div class="portlet-header ui-widget-header">Import Termination Rates:<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
                    <div class="portlet-content">
                        <div class="sub-form">
                            <label style="width:10%;">Trunk list:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label> 
                            <? $data['didlist'] = form_dropdown('trunk_id', $this->db_model->build_dropdown("id,name", "trunks", "", ""), '');
                            echo $data['didlist']; ?>
                            <br/><br/>
                            <input type="hidden" name="mode" value="Import Outbound Routes"  />
                            <input type="hidden" name="logintype" value="<?= $this->session->userdata('logintype') ?>" />
                            <input type="hidden" name="username" value="<?= $this->session->userdata('username') ?>" />
                            <div><label style="width:10%;">Select the file:</label>
                                <input class="text field large" name="rateimport" id="rateimport" size="15" type="file"/>
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
                            For Download Sample File: <br/><a href="<?= base_url();?>rates/customer_rates_download_sample_file/terminationrates_sample"><u>Click Here.</u></a>            
                        </div>                            
                    </div>
                </div>
            </div>
        </div>
        <div class="column" style="padding: 5px 7px 0px 0px">
            <div class="portlet-content">
                <iframe name="submitter" id="submitter" frameborder="0" src="" width="100%" style="background-color:transparent; float:left; display:block;height:135px;">
                </iframe>
                <input class="ui-state-default float-right ui-corner-all ui-button" id="ok" type="button" name="action" value="Cancel" />    
                <input type="submit" id="import_termination" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Import..." />
            </div>
        </div>
        </form>        
    </div>
</div>
