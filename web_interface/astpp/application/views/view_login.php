<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML+RDFa 1.1//EN" "-//W3C//DTD XHTML 1.0 Transitional//EN">
<html xml:lang="en" xmlns:fb="http://ogp.me/ns/fb#" xmlns="http://www.w3.org/1999/xhtml" lang="en"><head>
    <link href="<?=base_url()?>css/common.css" media="screen" rel="stylesheet" type="text/css">
  <link href="<?=base_url()?>css/dlgbox.css" media="screen" rel="stylesheet" type="text/css">
  <!--<style>
  html,body{height:100%;}
body{background-color:#fff 320px;background-image:url('data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4gPHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PGxpbmVhckdyYWRpZW50IGlkPSJncmFkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjUwJSIgeTE9IjAlIiB4Mj0iNTAlIiB5Mj0iMTAwJSI+PHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iIzllY2ZmNSIvPjxzdG9wIG9mZnNldD0iMTAwJSIgc3RvcC1jb2xvcj0iI2ZmZmZmZiIvPjwvbGluZWFyR3JhZGllbnQ+PC9kZWZzPjxyZWN0IHg9IjAiIHk9IjAiIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JhZCkiIC8+PC9zdmc+IA==');background-size:100%;background-image:-webkit-gradient(linear,50% 0,50% 320,color-stop(0%,#9ecff5),color-stop(100%,#fff));background-image:-webkit-linear-gradient(top,#9ecff5,#fff 320px);background-image:-moz-linear-gradient(top,#9ecff5,#fff 320px);background-image:-o-linear-gradient(top,#9ecff5,#fff 320px);background-image:-ms-linear-gradient(top,#9ecff5,#fff 320px);background-image:linear-gradient(top,#9ecff5,#fff 320px);background-repeat:no-repeat;background-image:-webkit-gradient(linear,0 0,0 320,from(#9ecff5),to(white));}

  .p40{padding:40px!important;}
   .dialog {
    -moz-border-radius: 5px 5px 5px 5px;
    -moz-box-shadow: 0 5px 15px 0 rgba(0, 0, 0, 0.2);
    background-color: white;
    border: 1px solid #C0C2C4;
    color: #5B636B;
    padding: 20px;
    position: absolute;
    z-index: 1000;
}
	.dialog.center {   left: 50%;    margin-left: -265px;    margin-top: -160px;    position: absolute;    top: 50%;    width: 500px;}
	
	
	
	.dialog_content{position:relative;padding-bottom:60px;}
	.dialog_content .content{margin-top:-16px;padding:40px 20px 20px 20px;clear:none;display:block;-moz-border-radius:5px;-webkit-border-radius:5px;-o-border-radius:5px;-ms-border-radius:5px;-khtml-border-radius:5px;border-radius:5px;border:1px solid #ced0d3;background-color:#eff1f2;}
	.dialog_content .content,.dialog_content .tabcontent.notabs{padding-top:15px;margin-top:0;}
	.dialog_content .tabcontent.lighter{background-color:#f4f5f5;}
	.dialog label{font-size:13px!important;margin-right:15px;width:105px;text-align:right;text-shadow:white 0 0 1px;}
	
	.dialog label.large {  width: 140px;}

	.dialog label {    font-size: 13px !important;    margin-right: 15px;    text-align: right;    text-shadow: 0 0 1px white;    width: 105px; }
	label {    clear: both;    display: block;    float: left;    font-weight: bold; font-family:Verdana, Geneva, sans-serif; }
	
	.toggle.large{background-position:0 -676px;padding-left:24px;}
	.toggle.large.open{background-position:0 -708px;}
	
	.dialog label.large{width:140px;}
	.dialog input[type=text],.dialog input[type=password],.dialog textarea,.dialog .area,.dialog .input{font-size:13px;width:350px;padding:4px 6px;margin:0;}
	.dialog span.input{width:150px;height:15px;}
	.dialog textarea{min-height:40px;margin-bottom:10px;}
	.indented{margin-left:120px!important;}.indented.large{margin-left:160px!important;}
	.dialog_buttons{position:absolute;right:0;bottom:0;float:right;text-align:left;}.dialog_buttons .button{margin-left:10px;}
	
	.button.default{color:white!important;border-color:#3995da;text-shadow:rgba(0,0,0,0.2) 1px 1px 0;background-color:#55b4f4 8%;background-image:url('data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4gPHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PGxpbmVhckdyYWRpZW50IGlkPSJncmFkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjUwJSIgeTE9IjAlIiB4Mj0iNTAlIiB5Mj0iMTAwJSI+PHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iIzc0YzVmNiIvPjxzdG9wIG9mZnNldD0iOCUiIHN0b3AtY29sb3I9IiM1NWI0ZjQiLz48c3RvcCBvZmZzZXQ9IjEwMCUiIHN0b3AtY29sb3I9IiMyMjdmZGYiLz48L2xpbmVhckdyYWRpZW50PjwvZGVmcz48cmVjdCB4PSIwIiB5PSIwIiB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI2dyYWQpIiAvPjwvc3ZnPiA=');background-size:100%;background-image:-webkit-gradient(linear,50% 0,50% 100%,color-stop(0%,#74c5f6),color-stop(8%,#55b4f4),color-stop(100%,#227fdf));background-image:-webkit-linear-gradient(top,#74c5f6,#55b4f4 8%,#227fdf);background-image:-moz-linear-gradient(top,#74c5f6,#55b4f4 8%,#227fdf);background-image:-o-linear-gradient(top,#74c5f6,#55b4f4 8%,#227fdf);background-image:-ms-linear-gradient(top,#74c5f6,#55b4f4 8%,#227fdf);background-image:linear-gradient(top,#74c5f6,#55b4f4 8%,#227fdf);}
	.button.default:hover{background-color:#55b4f4 8%;background-image:url('data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4gPHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PGxpbmVhckdyYWRpZW50IGlkPSJncmFkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjUwJSIgeTE9IjAlIiB4Mj0iNTAlIiB5Mj0iMTAwJSI+PHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iIzc0YzVmNiIvPjxzdG9wIG9mZnNldD0iOCUiIHN0b3AtY29sb3I9IiM1NWI0ZjQiLz48c3RvcCBvZmZzZXQ9IjEwMCUiIHN0b3AtY29sb3I9IiMxZDcyY2EiLz48L2xpbmVhckdyYWRpZW50PjwvZGVmcz48cmVjdCB4PSIwIiB5PSIwIiB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI2dyYWQpIiAvPjwvc3ZnPiA=');background-size:100%;background-image:-webkit-gradient(linear,50% 0,50% 100%,color-stop(0%,#74c5f6),color-stop(8%,#55b4f4),color-stop(100%,#1d72ca));background-image:-webkit-linear-gradient(top,#74c5f6,#55b4f4 8%,#1d72ca);background-image:-moz-linear-gradient(top,#74c5f6,#55b4f4 8%,#1d72ca);background-image:-o-linear-gradient(top,#74c5f6,#55b4f4 8%,#1d72ca);background-image:-ms-linear-gradient(top,#74c5f6,#55b4f4 8%,#1d72ca);background-image:linear-gradient(top,#74c5f6,#55b4f4 8%,#1d72ca);}
	.button.default:active{background-color:#55b4f4;background-image:url('data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4gPHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PGxpbmVhckdyYWRpZW50IGlkPSJncmFkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjUwJSIgeTE9IjAlIiB4Mj0iNTAlIiB5Mj0iMTAwJSI+PHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iIzFkNzJjYSIvPjxzdG9wIG9mZnNldD0iMTAwJSIgc3RvcC1jb2xvcj0iIzU1YjRmNCIvPjwvbGluZWFyR3JhZGllbnQ+PC9kZWZzPjxyZWN0IHg9IjAiIHk9IjAiIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JhZCkiIC8+PC9zdmc+IA==');background-size:100%;background-image:-webkit-gradient(linear,50% 0,50% 100%,color-stop(0%,#1d72ca),color-stop(100%,#55b4f4));background-image:-webkit-linear-gradient(top,#1d72ca,#55b4f4);background-image:-moz-linear-gradient(top,#1d72ca,#55b4f4);background-image:-o-linear-gradient(top,#1d72ca,#55b4f4);background-image:-ms-linear-gradient(top,#1d72ca,#55b4f4);background-image:linear-gradient(top,#1d72ca,#55b4f4);}
	
	.button, .button:visited {
    -moz-border-radius: 3px 3px 3px 3px;
    background-color: #EFF1F2;
    background-image: -moz-linear-gradient(center top , #FFFFFF, #EFF1F2);
    border: 1px solid #CED0D3;
    color: #5B636B;
    cursor: pointer;
    display: inline-block;
    font: bold 15px/30px Helvetica Neue,Helvetica,Arial,sans-serif;
    margin: 0;
    min-width: 50px;
    overflow: visible;
    padding: 0 15px;
    text-align: center;
    text-shadow: 1px 1px 0 rgba(255, 255, 255, 0.2);
    white-space: nowrap;
	}
	
	#dlg_flash_msg {
    -moz-border-radius: 5px 5px 0 0;
    background: url("< ?php echo base_url();?>css/themes/apple_pie/images/sign_warning.png") no-repeat scroll 18px 8px #EFF1F2;
    border-bottom: 1px solid #CED0D3;
    font-size: 12px;
    font-weight: bold;
    line-height: 16px;
    margin: -20px -20px 10px;
    padding: 8px 20px 8px 42px;
}

  </style>-->
</head>
<body>

	<div class="p40">
		 <div class="dialog center"> 
         <?php if(isset($astpp_errormsg)):?>
         <div class="error" id="dlg_flash_msg">Login unsuccessful. Please make sure you entered the correct username and password, and that your account is active.</div>
        <?php endif;?>		
          <form action="<?php echo base_url();?>astpp/login" method="POST">
         	<div class="dialog_content login">
            		<div style="margin: 25px 0pt; text-align: center; clear: both;">
    				<a href="#"><img src="<?php echo base_url();?>css/themes/apple_pie/images/logo.png" width="187" height="71" border="0"></a>
  					</div>                    
                    <div id="logins">               
                        
                        <div class="content" style="height: 110px;">
                          <p>
                            <label for="login" class="large" style="color: rgb(16, 51, 77); line-height: 24px; font-size: 15px;"> Username</label>
                            <input id="username" name="username" style="width: 200px;" tabindex="1" type="text">
                          </p>
                    
                          <p>
                            <label for="password" class="large" style="color: rgb(16, 51, 77); line-height: 24px; font-size: 15px;">Password</label>
                            <input id="password" name="password" style="width: 200px;" tabindex="2" value="" type="password">
                          </p>
                    
                          <p style="margin-bottom: 5px;">&nbsp;
                            
                            
                    
                            
                          </p>
                    
                          <p class="indented large">&nbsp;
                          
                          </p>
                          <div style="width: 0pt; height: 0pt; overflow: hidden;">
                            <button type="submit">&nbsp;</button>
                          </div>
                        </div>
                     
					</div>

                    <div class="dialog_buttons">
                      <input class="button default" id="btn_signin" name="commit" style="font-size: 17px; float: left;" tabindex="3" value="Sign In" type="submit">
                    </div>                  

            </div>
            </form>
         	
         </div>
	</div>

</body>
</html>
