<?
extend('master.php');
startblock('extra_head');
endblock();

startblock('page-title');
print(gettext($page_title));
endblock();

startblock('content');
?>

<section class="slice color-three pb-4">
    <div id="floating-label" class="w-section inverse p-0">
        <div class="col-md-12">
            <div class="card float-left p-4 col-md-8">
                <div class="card pb-4 px-0">
                    <h3 class="bg-secondary text-light p-2 rounded-top"><?php echo gettext('Requested reports list'); ?></h3>
                    <div class="row px-4">
                        <div class="col-12">
                            <table id="reports_list" class="table">
                                <thead>
                                    <tr>
                                      <th scope="col"><?=gettext('Date create')?></th>
                                      <th scope="col"><?=gettext('Begin date')?></th>
                                      <th scope="col"><?=gettext('End date')?></th>
                                      <th scope="col"><?=gettext('Status')?></th>
                                      <th scope="col"><?=gettext('Download')?></th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                  </tbody>
                            </table>
                        </div>
                    </div>
                </div>
           </div>
           <div class="card float-right p-4 col-md-4">
                <div class="card pb-4 px-0">
                    <h3 class="bg-secondary text-light p-2 rounded-top"><?php echo gettext('Request new report'); ?></h3>
                    <div class="row px-4">
                        <div class='col-md-12 form-group'>
                            <label class="col-md-12 p-0 control-label"><?php echo gettext('Begin date')?></label>
                            <input type="text" class="form-control" id="r_begin_data"/>
                        </div>
                        <div class='col-md-12 form-group'>
                            <label class="col-md-12 p-0 control-label"><?php echo gettext('End date')?></label>
                            <input type="text" class="form-control" id="r_end_data"/>
                        </div>
                        <div class='col-md-12'>
                            <div class="col-md-4 float-right">
                                <button class="btn btn-success btn-block" id="report_new" type="button"><?php echo gettext('New report');?></button>
                            </div>
                            <div class="col-md-4 float-right">
                                <button class="btn btn-info btn-block" id="report_refresh" type="button"><?php echo gettext('Refresh');?></button>
                            </div>
                        </div>
                    </div>
                </div>
           </div>
        </div>
    </div>
</section>
<script>
function show_toast(toast_type, toast_message) {
    if (toast_type == 'error'){
        $("#toast-container_error").hide();
        $("#toast-container_error").css("display","block").delay(5000).fadeOut();
    }
    if (toast_type == 'ok'){
        $("#toast-container").hide();
        $("#toast-container").css("display","block").delay(5000).fadeOut();
    }

    $(".toast-message").html(toast_message);
}

$("#report_new").click(function(){
    $.post(
        location.pathname+'/addreport',
        {bd:$("#r_begin_data").val(), ed:$("#r_end_data").val()},
        function(a){
            if (a.hasOwnProperty('error')){
                if (parseInt(a.error) === 0){
                    loadReportsList();
                    show_toast('ok', '<?=gettext('New report requested. Wait for ready.');?>');
                } else {
                    show_toast('error', a.status);
                }
            }
            console.log(a);
        }
    );
});

$("#report_refresh").click(function(){
    loadReportsList();
    show_toast('ok', '<?=gettext('Refresh requested reports status');?>');
});

function loadReportsList(){
    var tbody = $("#reports_list").find('tbody:first');

    $.post(
        location.pathname+'/getlist',
        {},
        function(list){
            $(tbody).empty();

            if (list.length > 0){
                $(list).each(function(i,o){
                    $(tbody).append("<tr><td>"+o.cdate+"</td><td>"+o.bdate+"</td><td>"+o.edate+"</td><td>"+o.pstatus+"</td><td>"+o.hsum+"</td></tr>");
                });
            } else {
                $(tbody).append("<tr><td colspan=5 class='text-center p-4'><?=gettext('Empty')?></td></tr>");
            }
        }
    );

    return true;
}

$(document).ready(function(){
    var date = new Date();
    var currentdate = new Date(date.getFullYear(), date.getMonth(), 1);
    var datetime    = currentdate.getFullYear() + "-" + ('0' + (currentdate.getMonth()+1)).slice(-2) + "-" + ("0" + currentdate.getDate()).slice(-2);
    currentdate     = new Date(date.getFullYear(), date.getMonth() + 1, 0);
    var datetime1   = currentdate.getFullYear() + "-" + ('0' + (currentdate.getMonth()+1)).slice(-2) + "-" + ("0" + currentdate.getDate()).slice(-2);

    $("#r_begin_data").datepicker({
        value:datetime,
        uiLibrary: 'bootstrap4',
        iconsLibrary: 'fontawesome',
        modal:true,
        format: 'yyyy-mm-dd',
        footer:true
    });

    $("#r_end_data").datepicker({
        value:datetime1,
        uiLibrary: 'bootstrap4',
        iconsLibrary: 'fontawesome',
        modal:true,
        format: 'yyyy-mm-dd',
        footer:true
    });

    loadReportsList();
});
</script>
<?
endblock();
end_extend();
?>
