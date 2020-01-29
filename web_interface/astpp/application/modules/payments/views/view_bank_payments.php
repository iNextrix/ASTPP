<? extend('master.php') ?>
<? startblock('extra_head') ?>
    <script type="text/javascript" language="javascript">

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

        function clear_pay_attr() {
            $('#pdoc_paydate').empty();
            $('#pdoc_paysumm').empty();
            $('#pdoc_paydesc').empty();

            var items = ['username', 'inn', 'brs', 'bname', 'kpp', 'bik'];

            $(items).each(function(t, i){
                $('#pdoc_'+i).empty();
                $('#b_'+i).empty();
            });
        }

        function validate_pay_attr() {
            var items = ['username', 'inn', 'brs', 'bname', 'kpp', 'bik'];
            var res   = true

            $(items).each(function(t, i){
                $('#pdoc_'+i).removeClass('not-equal');
                $('#b_'+i).removeClass('not-equal');
                if ( $('#pdoc_'+i).val() != $('#b_'+i).val() ) {
                    $('#pdoc_'+i).addClass('not-equal')
                    $('#b_'+i).addClass('not-equal');
                    res = false;
                }
            }).promise().done(function(){
                if (res){
                    show_toast('ok', '<?=gettext('User found')?>');
                } else {
                    show_toast('error', '<?=gettext('Some data not equals')?>');
                }
            });
        }

        function make_transaction() {
            var uid = parseInt($('#b_username').data('uid'));
            if (uid){
                $.post(
                    '/payments/bpay_transaction',
                    {u: uid, d: $('#pdoc_paydate').val(), s: $('#pdoc_paysumm').val(), ds:$('#pdoc_paydesc').val()},
                    function (data){
                        if (parseInt(data) > 0){
                            show_toast('ok', '<?=gettext('Payment applied')?>');
                        };
                    }
                );
            }else{
                show_toast('error', '<?=gettext('User not found')?>');
            }
        }

        var openFile = function(event) {
           var input = event.target;
           var reader = new FileReader();

           clear_pay_attr();

           reader.onload = function(){
                var aStr = reader.result.split("\n");
                var isBlockOpen = false;
// Parse 1C doc
                $(aStr).each(function(i, o){
                    if (/СекцияДокумент=Платежное поручение/.test(o)){
                        isBlockOpen = true;
                    }
                    if (/КонецДокумента/.test(o)){
                        isBlockOpen = false;
                    }

                    if (isBlockOpen){
                        var [k, v] = o.split('=');
// Pay date
                        if (/^Дата$/.test(k)){
                            $('#pdoc_paydate').val(v);
                        }
// Pay summ
                        if (/^Сумма$/.test(k)){
                            $('#pdoc_paysumm').val(v);
                        }
// Pay description
                        if (/^НазначениеПлатежа$/.test(k)){
                            $('#pdoc_paydesc').val(v);
                        }
// Username
                        if (/^Плательщик$/.test(k)){
                            $('#pdoc_username').val(v);
                        }
// INN
                        if (/^ПлательщикИНН$/.test(k)){
                            $('#pdoc_inn').val(v);
                        }
// Bank RS
                        if (/^ПлательщикРасчСчет$/.test(k)){
                            $('#pdoc_brs').val(v);
                        }
// Bank Name
                        if (/^ПлательщикБанк1$/.test(k)){
                            $('#pdoc_bname').val(v);
                        }
// KPP
                        if (/^ПлательщикКПП$/.test(k)){
                            $('#pdoc_kpp').val(v);
                        }
// BIK
                        if (/^ПлательщикБИК$/.test(k)){
                            $('#pdoc_bik').val(v);
                        }
                    }
                }).promise().done(function(){
// Search and load user attr by INN
                    if ($('#pdoc_paydate').val() && $('#pdoc_paysumm').val() && $('#pdoc_inn').val()) {
                        $.post(
                            '/payments/pattr',
                            { i: $('#pdoc_inn').val() },
                            function (data){
                                if (! data.hasOwnProperty('error')){
                                    $('#b_username').val(data.company_name);
                                    $('#b_inn').val(data.inn);
                                    $('#b_brs').val(data.bank_rs);
                                    $('#b_bname').val(data.bank_name);
                                    $('#b_kpp').val(data.bank_kpp);
                                    $('#b_bik').val(data.bank_bik);
                                    $('#b_username').data('uid', data.id);
                                    validate_pay_attr();
                                } else {
                                    show_toast('error', data.error_str);
                                }
                            }
                        );
                    }
                });
           };

           reader.readAsText(input.files[0],'windows-1251');
        };

        $(document).ready(function(){
            clear_pay_attr();
        });
    </script>
<? endblock() ?>

<? startblock('page-title') ?>
<?= $page_title ?>
<? endblock() ?>

<? startblock('content') ?>        
<style>

.fbutton > div {
    display:flex;
}

.flexigrid div.tDiv2 {
    float: right;
}

.flexigrid input {
    text-align: right;
}

.upload-btn-wrapper {
  position: relative;
  overflow: hidden;
  display: inline-block;
}

.upload-btn-wrapper input[type=file] {
  font-size: 100px;
  position: absolute;
  left: 0;
  top: 0;
  opacity: 0;
}

.not-equal {
    border-bottom: 1px solid #D23C19 !important;
}

</style>

    <section class="slice color-three">
        <div class="w-section inverse p-0">
            <div class="card col-md-12 pb-4">
                <div class="flexigrid">
                <div class="tDiv">
                <div class="tDiv2">
                    <div class="fbutton">
                        <div>
                            <span> <h3 class="px-4 p-1"><?=gettext('Upload paymets document')?> :</h3> </span>
                            <span class="upload-btn-wrapper">
                                <button class="btn btn-secondary ml-2" type="button" ><i class="fa fa-upload fa-lg"></i><?=gettext('Upload')?></button>
                                <input type="file" name="myfile" onchange='openFile(event)'/>
                            </span>
                        </div>
                    </div>
                </div>
                </div>
                <div class="pop_md col-12 pb-4">
                    <div id="floating-label" class="pb-4">
                    <div class="row px-4">

                    <div class="col-md-6 form-group">
                        <label class="col-md-12 p-0 control-label"><?=gettext('Pay Date')?></label>
                        <input class="col-md-12 form-control form-control-lg" value="" id="pdoc_paydate" size="20" type="text">
                        <div class="tooltips error_div float-left p-0" style="display: block;"></div>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="col-md-12 p-0 control-label"><?=gettext('Pay summ')?></label>
                        <input class="col-md-12 form-control form-control-lg" value="" id="pdoc_paysumm" size="20" type="text">
                        <div class="tooltips error_div float-left p-0" style="display: block;"></div>
                    </div>

                    <div class="col-md-12 form-group">
                        <label class="col-md-12 p-0 control-label"><?=gettext('Description')?></label>
                        <input class="col-md-12 form-control form-control-lg" value="" id="pdoc_paydesc" size="20" type="text">
                        <div class="tooltips error_div float-left p-0" style="display: block;"></div>
                    </div>


                    <div class="col-md-6 form-group p-0">
                        <h3 class="px-4 p-0"><?=gettext('From paymet document')?> :</h3>
                    </div>
                    <div class="col-md-6 form-group p-0">
                        <h3 class="px-4 p-0"><?=gettext('From billing')?> :</h3>
                    </div>

                    <div class="col-md-6 form-group">
                        <label class="col-md-12 p-0 control-label"><?=gettext('Username')?></label>
                        <input class="col-md-12 form-control form-control-lg" value="" id="pdoc_username" size="20" type="text">
                        <div class="tooltips error_div float-left p-0" style="display: block;"></div>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="col-md-12 p-0 control-label"><?=gettext('Username')?></label>
                        <input class="col-md-12 form-control form-control-lg" value="" id="b_username" size="20" type="text">
                        <div class="tooltips error_div float-left p-0" style="display: block;"></div>
                    </div>

                    <div class="col-md-6 form-group">
                        <label class="col-md-12 p-0 control-label"><?=gettext('INN')?></label>
                        <input class="col-md-12 form-control form-control-lg" value="" id="pdoc_inn" size="20" type="text">
                        <div class="tooltips error_div float-left p-0" style="display: block;"></div>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="col-md-12 p-0 control-label"><?=gettext('INN')?></label>
                        <input class="col-md-12 form-control form-control-lg" value="" id="b_inn" size="20" type="text">
                        <div class="tooltips error_div float-left p-0" style="display: block;"></div>
                    </div>

                    <div class="col-md-6 form-group">
                        <label class="col-md-12 p-0 control-label"><?=gettext('Bank RS')?></label>
                        <input class="col-md-12 form-control form-control-lg" value="" id="pdoc_brs" size="20" type="text">
                        <div class="tooltips error_div float-left p-0" style="display: block;"></div>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="col-md-12 p-0 control-label"><?=gettext('Bank RS')?></label>
                        <input class="col-md-12 form-control form-control-lg" value="" id="b_brs" size="20" type="text">
                        <div class="tooltips error_div float-left p-0" style="display: block;"></div>
                    </div>

                    <div class="col-md-6 form-group">
                        <label class="col-md-12 p-0 control-label"><?=gettext('Bank name')?></label>
                        <input class="col-md-12 form-control form-control-lg" value="" id="pdoc_bname" size="20" type="text">
                        <div class="tooltips error_div float-left p-0" style="display: block;"></div>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="col-md-12 p-0 control-label"><?=gettext('Bank name')?></label>
                        <input class="col-md-12 form-control form-control-lg" value="" id="b_bname" size="20" type="text">
                        <div class="tooltips error_div float-left p-0" style="display: block;"></div>
                    </div>

                    <div class="col-md-6 form-group">
                        <label class="col-md-12 p-0 control-label"><?=gettext('KPP')?></label>
                        <input class="col-md-12 form-control form-control-lg" value="" id="pdoc_kpp" size="20" type="text">
                        <div class="tooltips error_div float-left p-0" style="display: block;"></div>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="col-md-12 p-0 control-label"><?=gettext('KPP')?></label>
                        <input class="col-md-12 form-control form-control-lg" value="" id="b_kpp" size="20" type="text">
                        <div class="tooltips error_div float-left p-0" style="display: block;"></div>
                    </div>

                    <div class="col-md-6 form-group">
                        <label class="col-md-12 p-0 control-label"><?=gettext('Bank BIK')?></label>
                        <input class="col-md-12 form-control form-control-lg" value="" id="pdoc_bik" size="20" type="text">
                        <div class="tooltips error_div float-left p-0" style="display: block;"></div>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="col-md-12 p-0 control-label"><?=gettext('Bank BIK')?></label>
                        <input class="col-md-12 form-control form-control-lg" value="" id="b_bik" size="20" type="text">
                        <div class="tooltips error_div float-left p-0" style="display: block;"></div>
                    </div>

                    <div class="col-12 mt-4 text-right p-2">
                        <button class="btn btn-secondary ml-2" type="button" onclick="make_transaction()"><?=gettext('Pay')?></button>
                    </div>
                    </div>
                    </div>
                    </div>
                </div>
                <form method="post" action="" enctype="multipart/form-data" id="bpay_transaction">
                    <table id="" align="left" style=""></table>
                </form>
             </div>
        </div>
    </section>
<? endblock() ?>	
<? end_extend() ?>  
