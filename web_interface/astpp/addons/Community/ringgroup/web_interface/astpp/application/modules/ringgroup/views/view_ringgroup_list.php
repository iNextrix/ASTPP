<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
       
        build_grid("ringgroup_grid","",<? echo $grid_fields; ?>,<? echo $grid_buttons; ?>);
        $('.checkall').click(function () {
         $('.chkRefNos').prop('checked', $(this).prop('checked'));
     });
        $("#ringgroup_search_btn").click(function(){
            post_request_for_search("ringgroup_grid","","ringgroup_search");
        });        
        $("#id_reset").click(function(){
            clear_search_request("ringgroup_grid","");
            $("#accountcode").html("<option value='' selected='selected'><?php echo gettext('--Select--')?> </option>");
        });
        

    });

    function account_change_add(val){
        $.ajax({
          type: "POST",
          url: "<?= base_url()?>/ringgroup/customer_account_change/"+val,
          data:'',
          success:function(alt) { 
              $("#accountcode").html(alt);    
              $('.selectpicker').selectpicker('refresh');
          }
      });
    }

    
</script>
<? endblock() ?>
<? startblock('page-title') ?>
<?= $page_title ?>
<? endblock() ?>
<? startblock('content') ?>        
<section class="slice color-three">
    <div class="w-section inverse p-0">
        <div class="col-12">
            <div class="portlet-content mb-4"  id="search_bar" style="display:none">
                <?php echo $form_search; ?>
            </div>
        </div>
    </div>
</section>
<section class="slice color-three pb-4">
    <div class="w-section inverse p-0">
        <div class="card col-md-12 pb-4">      
            <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">
                <table id="ringgroup_grid" align="left" style="display:none;"></table>
            </form>
        </div>  
    </div>
</section>
<? endblock() ?>
<? end_extend() ?>