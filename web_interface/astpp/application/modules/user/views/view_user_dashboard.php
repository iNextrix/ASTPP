<? extend('master.php') ?>
<? startblock('page-title') ?>
  <?php echo $page_title; ?>
<? endblock() ?>
<?php
	$accountinfo=$this->session->userdata('accountinfo');
	$currency=$this->common->get_field_name('currency','currency',array("id"=>$accountinfo['currency_id']));

?>
<? startblock('extra_head') ?>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/chart/highcharts.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/chart/exporting.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/chart/highcharts-3d.js"></script>
<style>
.second{
 color: black;
 opacity:0.4;
 border: 0px;
 width: 100%; 
 padding:100px 0px 100px 0px;
 margin:5px;  
 display:relative;
 text-align:center;
 font-size:250%;
}
.active_product table td{
	word-break:break-all;
	}
.new_products table td{
	word-break:break-all;
	}	
</style>
<script type="text/javascript">
	function get_current_data(){
		 $.ajax({
				type:'POST',
				url: "<?php echo base_url(); ?>"+'user/user_get_current_info/',
				dataType: 'JSON',
				cache    : false,
				async    : false,
        success: function(response_data) {
        		
        		$("#product_count").html(response_data.product_count);
        		$("#call_count").html(response_data.call_count);
        		$("#balance").html(response_data.balance);
        		$("#invoice_amount").html(response_data.invoice_amount);
        }
    	});
	}
  $(document).ready(function() {
			get_current_data();
	});
</script> 
<? endblock() ?>
<? startblock('content') ?>

<section class="slice">
    <div class="w-section inverse no-padding">
            <div class="row">
                <div class="col-md-12">        
                    
                     <div class="dashboard_values row mb-5">
                     
                     		<a href="<?php echo base_url(); ?>user/user_products_list/" class="col-lg-3 col-md-6 col-sm-12 pt-2">
                            <div class="bg-primary card col-12 text-light">
                                <div class="col-lg-8 col-7 float-left py-5 px-4 ">
                                    <div class="h1" id="product_count">0</div>
                                    <h3><?php echo gettext('Products') ?></h3>
                                </div>
                                <div class="col-lg-4 col-5 float-left py-5 px-4 ">
                                    <i class="fa fa-archive fa-4x float-left"></i>
                                </div>
                            </div>
                        </a>
                     
                     		<a href="<?php echo base_url(); ?>user/user_cdrs_report/" class="col-lg-3 col-md-6 col-sm-12 pt-2">
                            <div class="bg-danger card col-12 text-light">
                                <div class="col-lg-8 col-7 float-left py-5 px-4 ">
                                    <div class="h1" id="call_count">0</div>
                                    <h3><?php echo gettext("Today's Calls"); ?></h3>
                                </div>
                                <div class="col-lg-4 col-5 float-left py-5 px-4 ">
                                    <i class="fa fa-phone fa-4x float-left"></i>
                                </div>
                            </div>
                        </a>
                        
                        <a href="<?php echo base_url(); ?>user/user_invoices_list/" class="col-lg-3 col-md-6 col-sm-12 pt-2">
                            <div class="bg-success card col-12 text-light">
                                <div class="col-lg-8 col-7 float-left py-5 px-4 ">
                                   <div class="h1" id="invoice_amount">0</div>
                                    <h3><?php echo gettext('Outstanding'); ?> (<?php echo $currency;?>)</h3>
                                </div>
                                <div class="col-lg-4 col-5 float-left py-5 px-4 ">
                                    <i class="fa fa-wpforms fa-4x float-left"></i>
                                </div>
                            </div>
                        </a>
                        
                        <div class="col-lg-3 col-md-6 col-sm-12 pt-2"><div class="bg-dark card col-12 text-light">
                                <div class="col-lg-8 col-7 float-left py-5">
                                    <div class="h1" id="balance">0</div>
                                    <?php
										$variable =$accountinfo['posttoexternal']==1 ? 'Credit' : gettext('Balance');  
									?>
                                    <h3><?php echo $variable." (".$currency.")";?></h3>
                                </div>
                                <div class="col-lg-4 col-5 py-4 float-left text-center">
                                    <i class="fa fa-money fa-4x"></i>
                                    <a href="<?php echo base_url();?>products/products_topuplist/" class="btn btn-success btn-sm p-1 mt-2 w-100"><?php echo gettext('Top Up'); ?></a>
                                </div>
                            </div>
                        </div>      
                    </div>

                <div class="row mb-5">
                 <div class="col-lg-6 d-flex">
                    <div class="card w-100 active_product">
                        <h3 class="text-dark p-3"><i class="fa fa-archive text-primary fa-fw"></i> <?php echo gettext('Active Products'); ?>
                                <a href="<?php echo base_url();?>user/user_products_list/" class="float-right btn btn-secondary"><?php echo gettext('View All'); ?></a>
                        </h3>
                        <div class="card-body table-responsive">

                            <table class="table table-hover">
                              <thead class="thead-light">
                                <tr>
                                  <th scope="col"><?php echo gettext('Name') ?></th>
                                  <th scope="col"><?php echo gettext('Date'); ?></th>
                                  <th scope="col"><?php echo gettext('Order');?></th>
                                  <th scope="col"><?php echo gettext('Payment method'); ?></th>
				  <th scope="col"><?php echo gettext('Quantity'); ?></th>
                                  <th scope="col"><?php echo gettext('Setup Fee'); ?> [<?php echo $currency;?>]</th>
                                  <th scope="col"><?php echo gettext('Price');?> [<?php echo $currency;?>]</th>
                                  <th scope="col"><?php echo gettext('Status'); ?></th>
                                </tr>
                              </thead>
                              <tbody>
                              	<?php
                              		
																	$account_data = $this->session->userdata ( "accountinfo" );
																  $where_arr = array ("orders.accountid"=>$account_data['id'],"order_items.is_terminated"=>'0');
																	$this->db->order_by("orders.order_date", "desc");
																	
																	

																	
																	$query = $this->db_model->getJionQuery('orders', 'orders.id,orders.order_date,orders.order_id as id1,orders.payment_gateway,orders.payment_status,order_items.order_id as orderid,order_items.price,order_items.quantity,order_items.setup_fee,order_items.product_id',$where_arr, 'order_items','orders.id=order_items.order_id', 'inner',10, 0,'','');
																	

																	//
																	if($query->num_rows > 0){
																		$result_array =  $query->result_array();
																		foreach($result_array as $key=>$val){
																			echo "<tr>";
																			echo "<td>".$this->common->get_field_name('name',"products",array('id'=>$val['product_id']))."</td>";
											                echo "<td>".$val['order_date']."</td>";
											                echo "<td>".$val['id1']."</td>";
											                echo "<td>".$val['payment_gateway']."</td>";

     echo "<td>".$val['quantity']."</td>";
											                echo "<td>".$this->common_model->calculate_currency_customer($val ['setup_fee'])."</td>";
											                echo "<td>".$this->common_model->calculate_currency_customer($val ['price'])."</td>";
											                echo "<td>".$val['payment_status']."</td>";
											                echo "</tr>";
																		}
																	}
                                ?> 
                              </tbody>
                            </table>
                        
                        </div>
                    </div>
                 </div>
             
                 <div class="col-lg-6 d-flex">
                    <div class="card w-100 new_products">
                        <h3 class="text-dark p-3"><i class="fa fa-archive text-primary fa-fw"></i> <?php echo gettext('New Products');?>
                                
                                <a href="<?php echo base_url();?>user/user_available_products/" class="float-right btn btn-secondary"><?php echo gettext('View All')?></a>
                        </h3>
                        <div class="card-body table-responsive">

                            <table class="table table-hover">
                              <thead class="thead-light">
                                <tr>
                                  <th scope="col"><?php echo gettext('Name');?></th>
                                  <th scope="col"><?php echo gettext('Category');?></th>
                                  <th scope="col"><?php echo gettext('Price'); ?> [<?php echo $currency;?>]</th>
                                  <th scope="col"><?php echo gettext('Setup Fee');?> [<?php echo $currency;?>]</th>
                                  <th scope="col"><?php echo gettext('Bill Type');?></th>
                                  <th scope="col"><?php echo gettext('Bill Days');?></th>
                                </tr>
                              </thead>
                              <tbody>
                              	<?php
                              	
                              		if(isset($productdata) && !empty($productdata)){
                              			foreach($productdata as $key=>$val){
                              				echo "<tr>";
										                	echo "<td>".$val['name']."</td>";
										                	echo "<td>".$this->common->get_field_name("name","category",array("id"=>$val['product_category']))."</td>";
										                	echo "<td>".$this->common_model->calculate_currency_customer($val ['price'])."</td>";
										                	echo "<td>".$this->common_model->calculate_currency_customer($val ['setup_fee'])."</td>";
										                	if($val['billing_type'] == "0"){
						                          	echo "<td><span class='badge badge-success'>One time</span></td>";
						                          }else{
						                          	echo "<td><span class='badge badge-danger'>Recurring</span></td>";
						                          }
						                          echo "<td>".$val['billing_days']."</td>";
						                          echo "</tr>";
                              			}
                              		}
                              	?>
                              </tbody>
                            </table>
                        
                        </div>
                    </div>
                 </div>
             </div>

              <div class="row">
                 <div class="col-lg-12">
                    <div class="card">
                        <h3 class="text-dark p-3"><i class="fa fa-phone text-primary fa-fw"></i> <?php echo gettext('Recent Calls'); ?>
                                
                                <a href="<?php echo base_url();?>user/user_cdrs_report/" class="float-right btn btn-secondary"><?php echo gettext('View All'); ?></a>
                        </h3>
                        <div class="card-body table-responsive">

                            <table class="table table-hover">
                              <thead class="thead-light">
                                <tr>
                                  <th scope="col"><?php echo gettext('Date');?></th>
                                  <th scope="col"><?php echo gettext('Caller ID');?></th>
                                  <th scope="col"><?php echo gettext('Called Number');?></th>
                                  <th scope="col"><?php echo gettext('Destination');?></th>
                                  <th scope="col"><?php echo gettext('Duration');?></th>
                                  <th scope="col"><?php echo gettext('Debit');?> [<?php echo $currency;?>]</th>
                                  <th scope="col"><?php echo gettext('Disposition');?></th>
                                  <th scope="col"><?php echo gettext('Call Type');?></th>
                                  <th scope="col"><?php echo gettext("Direction");?></th>
                                </tr>
                              </thead>
                              <tbody>
                              	<?php
                              		
                              		$accountinfo = $this->session->userdata ( 'accountinfo' );
																	$where = array (
																			"accountid" => $accountinfo['id']
																	);
            											$query_cdrs = $this->db_model->select ( "*", "cdrs", $where,"callstart","desc","10","0","");
																	if($query_cdrs->num_rows > 0){
																		$result_array =  $query_cdrs->result_array();
																		foreach($result_array as $key=>$val){
											                echo "<tr>";
															echo "<td>".$this->common->convert_GMT_to ( '', '', $val ['callstart'] )."</td>";
										                	echo "<td>".$val['callerid']."</td>";
										                	echo "<td>".$val['callednum']."</td>";
										                	echo "<td>".$val['notes']."</td>";
										                	echo "<td>".$val['billseconds']."</td>";
						                          if($accountinfo['type'] =='3'){
						                          	echo "<td>".$this->common_model->calculate_currency_customer($val ['cost'])."</td>";
						                          }else{
						                          	echo "<td>".$this->common_model->calculate_currency_customer($val ['debit'])."</td>";
						                          }
						                          echo "<td>".$val['disposition']."</td>";
						                          echo "<td>".$val['calltype']."</td>";
						                          if($val['call_direction'] == "outbound"){
						                          	echo "<td><span class='badge badge-success'>".$val['call_direction']."</span></td>";
						                          }else{
						                          	echo "<td><span class='badge badge-danger'>".$val['call_direction']."</span></td>";
						                          }
						                          echo "</tr>";
																		}
																	}
                                ?>
                               
                              </tbody>
                            </table>
                        
                        </div>
                    </div>
                 </div>
             </div>    
	    </div>
	    </div>
    </div>
</section>

<? endblock() ?>
<? startblock('sidebar') ?>
<? endblock() ?>
<? end_extend() ?>  
