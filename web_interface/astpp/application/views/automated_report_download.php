<?php if(!empty($this->session->userdata('accountinfo'))) { 
 include('header.php'); 
 }else{
 include('header_webpage.php');
} ?>

<section class="slice color-one">
 <div class="w-section inverse border_box p-0 border-top">
   <div class="">
     <div class="row">

     </div>
    </div>
  </div>    
</section>

<? start_block_marker('content') ?>
<? end_block_marker() ?>
<?php 
if(!empty($this->session->userdata('accountinfo'))) { 
 include('footer.php'); 
} else {
include('footer_webpage.php'); 
} 
?>

