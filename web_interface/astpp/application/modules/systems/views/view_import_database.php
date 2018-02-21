<body style="width:500px;">
<section class="slice gray no-margin">
 <div class="w-section inverse no-padding">
   <div>
     <div>
        <div class="col-md-12 no-padding margin-t-15 margin-b-10">
	        <div class="col-md-10"><b><? echo $page_title; ?></b></div>
	  </div>
     </div>
    </div>
  </div>    
</section>

<div>
  <div>
    <section class="slice color-three no-margin">
	<div class="w-section inverse no-padding">
	<form method="post" enctype="multipart/form-data" action="<?= base_url()?>systems/database_import_file/">
<ul class="padding-15">

                <li class="col-md-12">   
		    <label class="col-md-3 no-padding">Name:</label> 
		    <input type="text" name="fname"  class="col-md-5 form-control">    
                </li>
                <li class="col-md-12">   
                    <label class="col-md-3 no-padding">Select File:</label>     
		   <div class="col-md-5"><span class="no-padding form-control" style="margin-left:-15px;overflow-x:hidden;width:260px;"><input style="height:33px;" name="userfile"   id="userfile" type="file"></span></div>
                                  
                </li>
</ul>
            <input type="submit" class="btn btn-line-parrot pull-right" name="action" value="Upload" />
	    
</form>
        </div>      
    </section>
  </div>
</div>


