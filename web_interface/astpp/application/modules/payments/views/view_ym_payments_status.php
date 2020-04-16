<? extend('master.php') ?>
<? startblock('extra_head') ?>
<? endblock() ?>

<? startblock('page-title') ?>
<?=$page_title ?>
<? endblock() ?>

<? startblock('content') ?>        
<style>

.form-center {
    margin-left: auto;
    margin-right: auto;
    width: 60%;
    text-align:center;
}

.msg-success, .msg-fail {
    font-size: 16px !important;
    font-weight: 600;
}

.msg-success i, .msg-fail i {
    padding: 10px;
    display: block;
}

.msg-fail {
    color: red;
}

.msg-success {
    color: green;
}

</style>
    <section class="slice color-three">
        <div class="w-section inverse p-0">
            <div class="card col-md-12 pb-4">
                <div class="flexigrid">
                    <div class="p-5 form-center" id="floating-label">
                        <?php
                            if ($status == 'success'){
                                print('<label class="col-md-12 p-5 msg-success"><i class="fa fa-2x fa-check-circle-o" aria-hidden="true"></i>'.gettext('Payment successfully applied.').'</label>');
                                print('<label class="col-md-12 msg-success">'.gettext('Your balance will soon update. Wait couple minutes please.').'</label>');
                            } else {
                                print('<label class="col-md-12 p-5 msg-fail"><i class="fa fa-2x fa-times-circle-o" aria-hidden="true"></i>'.gettext('Payment refused, try once more.').'</label>');
                                print('<a class="btn btn-secondary" href="/payments/ympay" role="button">'.gettext('Try again').'</a>');
                            }
                        ?>
                    </div>
                </div>
             </div>
        </div>
    </section>
<? endblock() ?>	
<? end_extend() ?>  
