<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery-1.7.1.js"></script>
<script>
    $(document).ready(function() {
        $("#ok").click(function(){
            window.location='/did/did_list/';
        });
    });
</script>


<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
    <div class="portlet-header ui-widget-header">Import DIDs<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div class="portlet-content">
        <form method="post" action="<?= base_url() ?>did/did_bulk_import" target="submitter" enctype="multipart/form-data"> 
            <span style="font-size:12px;">
                File must be in the following format:<br />
                number, account, connectcost, includedseconds, monthlycost, cost,extensions, status, provider, country, province, city, increment
                <br/><br/>The file shall have the text fields escaped with quotation marks and the fields seperated by commas.</td></tr>
            </span>
            <br /><br />
            <input type="hidden" name="mode" value="Import DIDs" />
            <input type="hidden" name="logintype" value="<?= $this->session->userdata('logintype') ?>" />
            <input type="hidden" name="username" value="<?= $this->session->userdata('username') ?>" />
            <label class="desc">Select the file:</label>
            <input type="file" class="ui-state-default ui-corner-all ui-button" name="didimport"  size="40" />
            <iframe name="submitter" id="submitter" frameborder="0" src="" height="100px" width="100%" style="background-color:transparent; float:left; display:block">
            </iframe>
            <div style="width:100%; float:left; height:50px; margin-top:20px;">
                <input class="ui-state-default float-right ui-corner-all ui-button" id="ok" type="button" name="action" value="Cancel" />    
                <input type="submit" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Import..." />
            </div>

        </form>

    </div>
</div>
