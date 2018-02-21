<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
        <div class="portlet-header ui-widget-header">LCR Import Termination Rates<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
        <div class="portlet-content">	
        <form method="post" action="<?=base_url()?>/cgi-bin/astpp-admin/astpp-wraper.cgi" target="submitter" enctype="multipart/form-data">
        <span style="font-size:12px; width:600px;">
        File must be in the following format:<br />LD PREPEND CODE ie. 00 or 011(We add this one),Outgoing LD PREPEND (Only used for dialing out),CountryCode,Area Code,Destination,Connect Cost,Included Seconds,Per Minute Cost,Increment,Trunk,Precedence Level
        <br />The file shall have the text fields escaped with quotation marks and the fields seperated by commas.<br />You do not need to have the patterns setup as regex patterns.  Your pattern will have "^" prepended and ".*" appended.<br />
        <br />
        </span>
        <label class="desc">Select the file:</label>
        <input type="hidden" name="mode" value="Import Outbound Routes"  />
        <input type="hidden" name="logintype" value="<?=$this->session->userdata('logintype')?>" />
        <input type="hidden" name="username" value="<?=$this->session->userdata('username')?>" />
        <input type="file" class="ui-state-default ui-corner-all ui-button" name="rateimport"  size="40" />
        <iframe name="submitter" id="submitter" frameborder="0" src="" height="100px" width="100%" style="background-color:transparent; float:left; display:block">
        </iframe>
        <div style="width:100%; float:left; height:50px; margin-top:20px;">
        <input type="submit" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Import..." />
        </div>
        </form>        
        </div>
</div>