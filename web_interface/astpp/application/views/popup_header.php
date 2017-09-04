<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>

<link href="<?= base_url() ?>assets/css/bootstrap.min.css" rel="stylesheet"/>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/status/dist/css/bootstrap-select.css" />
<link href="<?= base_url() ?>assets/css/global-style.css" rel="stylesheet" type="text/css"/>
<script src="<?= base_url() ?>assets/js/maxcdn_bootstrap.min.js"></script>
<script src="<?php echo  base_url(); ?>assets/status/dist/js/bootstrap-select.js"></script>

<script type="text/javascript">
    
    $(document).ready(function() {
        $('.selectpicker').selectpicker('refresh');
        $(".selectpicker").removeClass("col-md-5");  
        $(".selectpicker").addClass("col-md-3"); 
  });        
</script>
</head>
</html>
