<? extend('master.php') ?>
<? startblock('extra_head') ?>
<style>
.info-box-text {
    text-transform: uppercase;
}
.info-box-number {
    display: block;
    font-size: 18px;
}
</style>

<? endblock() ?>

<? startblock('page-title') ?>
<?= $page_title ?>
<? endblock() ?>

<? startblock('content') ?>        

<div class="col-md-12"> 
    <div class="w-section inverse no-padding">
      <div class="container">
        <div class="card p-4" id="">
            <div class="" >
              <div class="card px-0">
              <h3 class="bg-secondary text-light p-3 rounded-top">Refill Here</h3>
                  <div class="row px-4">

		<?php $this->load->module("pages/pages");?>
		<?php  $this->pages->servies(); ?>
                  </div>
              </div>

          </div>
      </div>
  </div>
</div>
</div>


<? endblock() ?>    

<? end_extend() ?>  
