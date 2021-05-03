<? extend('master.php') ?>
<? startblock('extra_head') ?>

<style>
.info-box-text {
    text-transform: uppercase;
}
.info-box-number {
    display: block;
    /*font-weight: bold;*/
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
              <h3 class="bg-secondary text-light p-3 rounded-top"><?php echo gettext("Products"); ?></h3>
                  <div class="row px-4">
                      <form class="col-md-12 p-4">
                          <?php foreach($product_info as $key =>$value){ ?>
                          <a class="col-md-3 float-left p-2" href="<?php echo base_url(); ?>pages/checkout/<?php echo $value['id'] ?>">
                              <div class="card">
                                  <div class="topup-info-box">
                                      <div class="h-100 bg-info p-4 text-light float-left"><i class="fa fa-shopping-cart fa-2x"></i></div>
                                      <div class="p-4 float-left">
                                          <span class="info-box-text text-info"><?php echo  $value ['name']?></span>
                                          <span class="info-box-number text-dark"><?php  echo $value ['price'];?></span>
                                      </div>
                                  </div>
                              </div>
                          </a>
                          <?php } ?>
                      </form>
                  </div>
              </div>

          </div>
      </div>
  </div>
</div>
</div>


<? endblock() ?>    

<? end_extend() ?>  
