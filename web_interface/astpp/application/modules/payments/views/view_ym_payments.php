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

        function validate_pay_attr() {
            var res = true;

            $('#paysumm').removeClass('not-equal');

            if (isNaN(parseFloat($('#paysumm').val())) || parseFloat($('#paysumm').val()) ===0){
                show_toast('error', '<?=gettext('Pay sum not defined')?>');
                $('#paysumm').addClass('not-equal');
                res = false;
            }

            return res;
        }

        function replace_order() {
            if (validate_pay_attr()){
                $.post(
                    '/payments/ym_replace_order',
                    {u: <?=$uid?>, s: $('#paysumm').val()},
                    function (data){
                        if (data.hasOwnProperty('rurl') && data.rurl !== null){
                            setTimeout(
                                ()=>window.location.replace(data.rurl),
                                2000
                            );
                            show_toast('ok', '<?=gettext('Redirecting to payment site')?>');
                        } else {
                            show_toast('error', '<?=gettext('Payment gateway error. Try once more.')?>');
                        };
                    }
                );
            }
        }
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

.flexigrid input {
    text-align: right;
}

.form-center {
    margin-left: auto;
    margin-right: auto;
    width: 30%;
}

.not-equal {
    border-bottom: 1px solid #D23C19 !important;
}

</style>

    <section class="slice color-three">
        <div class="w-section inverse p-0">
            <div class="card col-md-12 pb-4">
                <div class="flexigrid">
                <div class="pop_md col-12 pb-4">
                    <div class="pb-4 form-center" id="floating-label">
                        <div class="row px-4" id="floating-label">
                            <div class="col form-group">
                                <label class="col-md-12 p-0 control-label"><?=gettext('Pay summ')?></label>
                                <input class="col-md-12 form-control form-control-lg" value="" id="paysumm" size="20" type="text">
                                <div class="tooltips error_div float-left p-0" style="display: block;"></div>
                            </div>
                        </div>
                        <div class="row px-4">
                            <div class="col-11 mt-4 text-right p-2">
                                <button class="btn btn-secondary ml-2" type="button" onclick="replace_order()"><?=gettext('Make pay')?></button>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
             </div>
        </div>
    </section>
<? endblock() ?>	
<? end_extend() ?>