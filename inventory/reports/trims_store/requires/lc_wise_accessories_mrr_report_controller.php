<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$store_arr=return_library_array( "select id, store_name from lib_store_location", "id", "store_name"  );
$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );

if($action=="pi_dtls_popup")
{
	echo load_html_head_contents("PI Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	if($pi_ids==""){echo "No Pi Found";die;}
	//echo $pi_ids;die;

	$sql= "select id, pi_number, supplier_id, importer_id, pi_date, total_amount, upcharge, discount, net_total_amount from com_pi_master_details where id in($pi_ids) and is_deleted=0 and status_active=1";
    //echo $sql;die;
	$sql_result=sql_select($sql);
	?>
    <table width="780" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
    <thead>
        <tr>
            <th width="40">SL</th>
            <th width="150">PI Number</th>
            <th width="150">Supplier</th>
            <th width="100">PI Amount</th>
            <th width="100">Upcharge</th>
            <th width="100">Discount</th>
            <th>Net PI Amount</th>
        </tr>
    </thead>
    <tbody>
    	<?
		$i=1;
		foreach($sql_result as $row)
		{
			if($i%2==0){
				$bgcolor = '#FFFFFF';
			}else{
				$bgcolor = '#E9F3FF';
			}
			?>
            <tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $i;?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" >
            	<td align="center"><? echo $i; ?></td>
                <td><? echo $row[csf("pi_number")]; ?></td>
                <td><? echo $supplier_arr[$row[csf("supplier_id")]]; ?></td>
                <td align="right"><? echo number_format($row[csf("total_amount")],2); ?></td>
                <td align="right"><? echo number_format($row[csf("upcharge")],2); ?></td>
                <td align="right"><? echo number_format($row[csf("discount")],2); ?></td>
                <td align="right"><? echo number_format($row[csf("net_total_amount")],2); ?></td>
            </tr>
            <?
			$i++;
		}
		?>
    </tbody>
    <tfoot>
    	<tr>
        	<th colspan="7" align="center"><input type="button" name="search" id="search" value="Close" onClick="parent.emailwindow.hide();" style="width:100px" class="formbutton" /></th>
        </tr>
    </tfoot>
</table>
    <?
	exit();
}

if($action=="btbLc_popup")
{
  	echo load_html_head_contents("BTB LC Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
    ?>

        <script>
        	function js_set_value(str)
        	{
        		var splitData = str.split("_");
        		$("#btbLc_id").val(splitData[0]);
        		$("#btbLc_no").val(splitData[1]);
        		parent.emailwindow.hide();
        	}
        </script>
    </head>
    <body>
        <div align="center" style="width:100%; margin-top:5px" >
            <form name="searchlcfrm_1" id="searchlcfrm_1" autocomplete="off">
        	<table width="600" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                        <tr>
                            <th>Supplier</th>
                            <th id="search_by_td_up">Enter BTB LC Number</th>
                            <th>
                            	<input type="reset" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('searchlcfrm_1','search_div','','','','');" />
                                <input type="hidden" id="btbLc_id" value="" />
                                <input type="hidden" id="btbLc_no" value="" />
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr align="center">
                            <td>
                                <?
                                    $sql_supplier = "select DISTINCT(c.id),c.supplier_name from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$companyID' and b.party_type in(1,4,5,23)";
        							echo create_drop_down( "cbo_supplier_id", 160,"$sql_supplier",'id,supplier_name', 1, '-- All Supplier --',0,'',0);
                                ?>
                            </td>
                            <td align="center" id="search_by_td">
                                <input type="text" style="width:230px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                            </td>
                             <td align="center">
                                <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier_id').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $companyID; ?>, 'create_lc_search_list_view', 'search_div', 'lc_wise_accessories_mrr_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                            </td>
                   	 	</tr>
                    </tbody>
                </table>
                <div align="center" style="margin-top:10px" id="search_div"> </div>
                </form>
           </div>
        </body>
        <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
<?

}


if($action=="create_lc_search_list_view")
{
	$ex_data = explode("_",$data);

	if($ex_data[0]==0) $cbo_supplier = "%%"; else $cbo_supplier = $ex_data[0];
	$txt_search_common = trim($ex_data[1]);
	$company = $ex_data[2];

	$sql= "select id, lc_number, supplier_id, importer_id, lc_date, last_shipment_date, lc_value from com_btb_lc_master_details where importer_id=$company and pi_entry_form=167 and supplier_id like '$cbo_supplier' and lc_number like '%".$txt_search_common."%' and is_deleted=0 and status_active=1 order by id desc";
    //echo $sql;
	$arr=array(1=>$company_arr,2=>$supplier_arr);
	echo create_list_view("list_view", "LC No, Importer, Supplier Name, LC Date, Last Shipment Date, LC Value","130,110,130,90,130","780","260",0, $sql , "js_set_value", "id,lc_number", "", 1, "0,importer_id,supplier_id,0,0,0,0", $arr, "lc_number,importer_id,supplier_id,lc_date,last_shipment_date,lc_value", "",'','0,0,0,3,3,2') ;
	exit();

}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	//if(str_replace("'","",$cbo_store_name)!="") $store_cond=" and b.store_id in(".str_replace("'","",$cbo_store_name).")";
	//$pi_no_cond=str_replace("'","",$txt_pi_no);
	$btbLc_id_str=str_replace("'","",$btbLc_id);
    //echo $btbLc_id_str; //die;
	//if(str_replace("'","",$cbo_store_name)==0) $store="%%"; else $store=str_replace("'","",$cbo_store_name);
	
	if(str_replace("'","",$btbLc_id)=='') $btbLc_id_cond=""; else $btbLc_id_cond=" and a.com_btb_lc_master_details_id='$btbLc_id_str'";
	if(str_replace("'","",$btbLc_id)=='') $btbLc_id_cond_mst=""; else $btbLc_id_cond_mst=" and a.id='$btbLc_id_str'";
	//if(str_replace("'","",$txt_pi_no)=='') $pi_cond_btb=""; else $pi_cond_btb="and c.pi_number='$pi_no_cond'";

	if($db_type==0)
	{
		$conversion_date=date("Y-m-d");
	}
	else
	{
		$conversion_date=date("d-M-y");
	}
	$exchange_rate=set_conversion_rate( 2, $conversion_date );
	ob_start();
	?>
    <!--<fieldset style="width:1120px">-->
    	<div style="width:100%; margin-left:10px;" align="left">
            <table width="800" cellpadding="0" cellspacing="0" style="visibility:hidden; border:none" id="caption">
                <tr>
                   <td align="center" width="100%" colspan="9" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
                </tr>
            </table>
            <table width="800" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <?
                    $sql_btb_lc = "select a.id,a.lc_number, a.supplier_id, a.importer_id, a.lc_date, a.last_shipment_date, a.lc_value,a.payterm_id,a.item_category_id
					from com_btb_lc_master_details a
					where  a.pi_entry_form=167 and a.is_deleted=0 and a.status_active=1  and a.importer_id=$cbo_company_name  $btbLc_id_cond_mst ";
                    //echo $sql_btb_lc; //and a.item_category_id = 4
                    $btbLcData=sql_select($sql_btb_lc);

                    foreach ($btbLcData as  $row) {
                        $lc_id[$row[csf('id')]] = $row[csf('id')];
                        
                    }
                    $lc_ids= implode(",",$lc_id);
                    

                    $sql_lc_pi = "select a.id,a.lc_number,a.importer_id,a.lc_date,a.lc_value,a.pi_entry_form,c.id as pi_id,c.item_category_id,d.amount
                    from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_master_details c, com_pi_item_details d
                    where a.id = b.com_btb_lc_master_details_id  and b.pi_id = c.id  and c.id = d.pi_id and  a.pi_entry_form = 167 and a.is_deleted = 0 and a.status_active = 1 and c.item_category_id = 4  and a.importer_id = $cbo_company_name and a.id in($lc_ids)";
                    //echo $sql_lc_pi;
                    $piLcDataResult=sql_select($sql_lc_pi); $lc_value_data_array=array();$all_pi_id_array=array();
                    foreach ($piLcDataResult as $row) {
                       $lc_value_data_array[$row[csf("id")]]+=$row[csf("amount")];
                       $all_pi_id_array[$row[csf('pi_id')]]=$row[csf('pi_id')];
                    }
                    $allpi_ids= implode(",",array_unique($all_pi_id_array));
                    //print_r($lc_value_data_array);
                    //echo $allpi_ids;
                ?>
                <thead>
                	 <tr>
                        <th colspan="9">BTB LC Details</th>
                    </tr>
                	<tr>
                        <th width="40">SL</th>
                        <th width="120">Importer</th>
                        <th width="100">Item Category</th>
                        <th width="110">Supplier</th>
                        <th width="120">BTB LC No</th>
                        <th width="100">Pay Terms</th>
                        <th width="100">LC Date</th>
                        <th>LC Value</th>
                    </tr>
                </thead>
            </table>
            <div style="width:820px; overflow-y:scroll; max-height:430px;" align="left" id="btb_dtls_part">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" id="tbl_lc_wise_accessories_mrr_dtls" >
                <?
                $i=1;$pi_id_all_array=array(); $pi_name_array=array(); $tot_pi_amnt=0;$lc_id=array();
				if(count($btbLcData)>0)
				{
                    foreach ($btbLcData as  $value) {
                        if($i%2==0){
                            $bgcolor = '#FFFFFF';
                        }else{
                            $bgcolor = '#E9F3FF';
                        }
                        $lc_id[$value[csf('id')]] = $value[csf('id')];
				?>
                    <tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $value[csf('importer_id')];?>" onClick="change_color('tr_<? echo $value[csf('importer_id')];?>','<? echo $bgcolor;?>')" >
                        <td width="40" align="center"><p><? echo $i; ?></p></td>
                        <td width="120" align="center"><p><? echo $company_arr[$value[csf('importer_id')]]; ?></p></td>
                        <td width="100" align="center"><p><? echo $item_category[$value[csf('item_category_id')]]; ?></p></td>
                        <td width="110" align="center"><p><? echo $supplier_arr[$value[csf('supplier_id')]]; ?></p></td>
                        <td width="120" align="center"><p><? echo $value[csf('lc_number')]; ?></p></td>
                        <td width="100" align="center"><p><? echo $pay_term[$value[csf('payterm_id')]]; ?></p></td>
                        <td width="100" align="center"><p><? echo change_date_format($value[csf('lc_date')]); ?></p></td>
                        <td align="right"><p><? echo number_format($value[csf('lc_value')],2);//number_format($lc_value_data_array[$value[csf('id')]],2); ?></p></td>
                    </tr>
                <?
                        $i++;
                        $tot_lc_value +=$value[csf('lc_value')];
                    }

				}
                unset($btbLcData);
				?>
                <tfoot>
                    <tr>
                        <th colspan="7" align="right">Total</th>
                        <th align="right"> <?php echo number_format($tot_lc_value,2,'.','');?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
            <br>
            <? //print_r($pi_id_all_array);?>
            <table width="820" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <tr>
                        <th colspan="7">PI Details</th>
                    </tr>
                    <tr>
                        <th width="40">SL</th>
                        <th width="150">PI Number</th>
                        <th width="100">PI Date</th>
                        <th width="120">Work Order Number</th>
                        <th width="120">WO Value</th>
                        <th width="120">PI Value</th>
                        <th>Net PI Value</th>
                    </tr>
                </thead>
            </table>
                <div style="width:820px; overflow-y:scroll; max-height:430px;" align="left" id="pi_dtls_part">
                    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" id="tbl_woven_finish_feb_pi_dtls" >
                    <?
                    $lc_ids = implode(",", $lc_id);
                    $sql_get_pi_ids = "select b.pi_id, c.pi_number, c.net_total_amount from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_master_details c
                    where  a.id = b.com_btb_lc_master_details_id and b.pi_id=c.id and a.pi_entry_form=167 and a.is_deleted=0 and a.status_active=1 and b.status_active =1 and a.importer_id=$cbo_company_name  $btbLc_id_cond_mst and c.item_category_id =4";
					//echo $sql_get_pi_ids;die;
                    $result_pi_id=sql_select($sql_get_pi_ids);
					$net_pi_amt_arr=array();
                    foreach ($result_pi_id as $value) {
                        $pi_id_all_array[$value[csf('pi_id')]]=$value[csf('pi_id')];
                        $pi_name_array[$value[csf('pi_id')]]=$value[csf('pi_number')];
						$net_pi_amt_arr[$value[csf('pi_id')]]=$value[csf('net_total_amount')];
                    }
                    $all_pi_id = implode(",",$pi_id_all_array);
                    //echo $all_pi_id;
					//print_r($net_pi_amt_arr);
                    (count($pi_id_all_array)>0) ? $pi_cond="and b.id in($all_pi_id)" : $pi_cond="" ;
                    $i=1;

                    $sql_pi_details="select c.id as pi_dtls_id, b.id as pi_id,b.pi_number, b.pi_date, b.currency_id,b.remarks,b.item_category_id, b.goods_rcv_status, c.work_order_no as booking_no, c.work_order_id as booking_id,c.work_order_dtls_id, c.quantity, c.rate, c.amount
                    from com_pi_master_details b, com_pi_item_details c,  com_btb_lc_pi a
                    where  b.id = c.pi_id and c.pi_id = a.pi_id and  b.entry_form = 167 and b.importer_id = $cbo_company_name and b.item_category_id = 4 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 $pi_cond $btbLc_id_cond ";

                    //echo $sql_pi_details;//die;
                    $result=sql_select($sql_pi_details);
                    $pi_dtls_data = array();$pi_data_check=array();$all_booking_id_arr=array();
                    foreach ($result as  $value) {
                        $pi_dtls_data[$value[csf('pi_id')]][$value[csf('booking_no')]]['pi_id']=$value[csf('pi_id')];
                        $pi_dtls_data[$value[csf('pi_id')]][$value[csf('booking_no')]]['booking_no']=$value[csf('booking_no')];
                        $pi_dtls_data[$value[csf('pi_id')]][$value[csf('booking_no')]]['booking_id']=$value[csf('booking_id')];
                        $pi_dtls_data[$value[csf('pi_id')]][$value[csf('booking_no')]]['pi_number']=$value[csf('pi_number')];
                        $pi_dtls_data[$value[csf('pi_id')]][$value[csf('booking_no')]]['amount']+=$value[csf('amount')];
                        $pi_dtls_data[$value[csf('pi_id')]][$value[csf('booking_no')]]['pi_date']=$value[csf('pi_date')];
                        $pi_dtls_data[$value[csf('pi_id')]][$value[csf('booking_no')]]['goods_rcv_status']=$value[csf('goods_rcv_status')];
                        //$pi_dtls_data[$value[csf('pi_id')]][$value[csf('booking_no')]]['rowspan']+=1;                        
                    }
                    // echo "<pre>";
                    // print_r($pi_dtls_data);
                    // echo "<pre>";
                    $k=1;
                    foreach ($pi_dtls_data as $booking_no_array) {
                        foreach ($booking_no_array as $row) {
                            $tot_pi_value +=$row['amount'];
                            $tot_pi_wo_value[$k] +=$row['amount'];                            
                        }
                        $k++;
                    }
                    // echo '<pre>';
                    // print_r($tot_pi_wo_value);die;
                   
                    $supplier_pi_arr=array();
                    $p=1;
                    $x=1;
                    foreach($pi_dtls_data as $pi_id=>$booking_no_array)
                    {
                        $y=1;$z=1;
                        $rowspan= count($booking_no_array);//die;
                        foreach ($booking_no_array as  $row)
                        {                            
                            $rowspanPI= count($row);//die;
                            if ($p%2==0)
                                $bgcolor="#E9F3FF";
                            else
                                $bgcolor="#FFFFFF";
                            
                            ?>
                            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trr_<? echo $p;?>','<? echo $bgcolor;?>')" id="trr_<? echo $p;?>">
                            <? if($y==1) {   
                                //$tot_pi_amnt+=$row['amount'];                              
                                ?>
                                <td width="40" rowspan="<? echo $rowspan; ?>" style="vertical-align: middle; text-align:center;"><? echo $p; ?></td>
                                <td width="150" align="center" rowspan="<? echo $rowspan; ?>" style="vertical-align: middle;"><p><? echo $row['pi_number']; ?></p></td>
                                <td width="100" align="center" rowspan="<? echo $rowspan; ?>" style="vertical-align: middle;"><? echo change_date_format($row['pi_date']); ?></td>
                            <? }?>
                                <td width="120" align="center"><p><? echo $row['booking_no']; ?></p></td>
                                <td align="right" width="120"><p><? echo number_format($row['amount'],2,'.',''); 
                                $tot_pi_amnt+=$row['amount'];
                                //$tot_pi_wo_value[$row['booking_id']]
                                ?>&nbsp;</p></td>
                            <?  if($z==1) 
								{ 
									?>
									<td align="right" rowspan="<? echo $rowspan; ?>" valign="middle" width="120"><? echo number_format($tot_pi_wo_value[$x],2,'.',''); ?></td>
                                    <td align="right" rowspan="<? echo $rowspan; ?>" valign="middle"><? echo number_format($net_pi_amt_arr[$pi_id],2,'.',''); $tot_net_pi_amt+=$net_pi_amt_arr[$pi_id]; ?></td>
									<? 
								}?>
                            </tr>
                            <?

                            
                            $p++;$y++;$z++;

                            if($row['goods_rcv_status']==1)
                            {
                                $all_booking_id_array[$row['booking_id']] = $row['booking_id'];
                            }
                            else
                            {
                                $all_booking_id_array[$row['pi_id']] = $row['pi_id'];
                            }
							$all_pi_ids[$row['pi_id']]=$row['pi_id'];
                           
                        }
                        $x++;

                    }
                ?>
                
            </table>
        </div>
        <table width="820" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
        	<tfoot>
            	<tr>
                    <th colspan="5" align="right">Total</th>
                    <th align="right" width="120"> <?php echo number_format($tot_pi_amnt,2,'.','');?></th>
                    <th align="right" width="143" style="padding-right:18px;"> <?php echo number_format($tot_net_pi_amt,2,'.','');?></th>
                </tr>
                <tr>
                	<th colspan="6" align="right">Upcharge/Discount</th>
                    <th><input type="button" name="Details" id="Details" value="Details" onClick="openmypage_pinumber('<?= implode(",",$all_pi_ids);?>')" style="width:100px" class="formbutton" /></th>
                </tr>
            </tfoot>
        </table>
        <br>
        <table width="600" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
            <thead>
                <tr>
                    <th colspan="7">Accessories Receive</th>
                </tr>
                <tr>
                    <th width="40">SL</th>
                    <th width="110">Recv. Date</th>
                    <th width="110">MRR No</th>
                    <th width="90">Challan No</th>
                    <!-- <th width="90">MRR Qnty</th>
                    <th width="70">Rate</th> -->
                    <th>MRR Value</th>
                </tr>
            </thead>
        </table>
        <div style="width:620px; overflow-y:scroll; max-height:300px;" align="left" id="rcv_dtls_part">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" id="tbl_woven_finish_feb_rcv" >
                <tbody>
                 <?
                    
                    $all_booking_id=implode(",",$all_booking_id_array);
                    $compos=''; $tot_recv_qnty=0; $tot_recv_amnt=0; $recv_id_array=array(); $recv_pi_array=array(); $pi_data_array=array();
                    
					if ($all_booking_id!='' || $all_booking_id!=0)
					{
						$sql_recv="select a.id as rec_id, a.recv_number, a.receive_date, a.challan_no, a.booking_id, a.booking_no, b.order_rate, b.order_uom, b.order_qnty, b.remarks, b.prod_id, b.pi_wo_batch_no as pi_wo_id, b.order_rate, c.quantity,c.order_amount
                        from inv_receive_master a, inv_transaction b, order_wise_pro_details c
                        where a.id = b.mst_id and b.id=c.trans_id and  a.item_category = 4 and a.entry_form=24 and a.company_id = $cbo_company_name and a.receive_basis in(1,2) and b.item_category = 4 and b.transaction_type = 1 and a.booking_id in ($all_booking_id) and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0";
					}
                    //echo $sql_recv;
                    $pi_rec_data_arr= array();
                    $dataArray=sql_select($sql_recv);


                    foreach ($dataArray as $value) {

                        $pi_rec_data_arr[$value[csf('booking_id')]]['rec_id']=$value[csf('rec_id')];
                        $pi_rec_data_arr[$value[csf('booking_id')]]['recv_number']=$value[csf('recv_number')];
                        $pi_rec_data_arr[$value[csf('booking_id')]]['receive_date']=$value[csf('receive_date')];
                        $pi_rec_data_arr[$value[csf('booking_id')]]['challan_no']=$value[csf('challan_no')];
                        $pi_rec_data_arr[$value[csf('booking_id')]]['booking_id']=$value[csf('booking_id')];
                        $pi_rec_data_arr[$value[csf('booking_id')]]['booking_no']=$value[csf('booking_no')];
                        $pi_rec_data_arr[$value[csf('booking_id')]]['pi_wo_id']=$value[csf('pi_wo_id')];
                       

                        $pi_rec_data_arr[$value[csf('booking_id')]]['amount']+=$value[csf('order_amount')];
                        $pi_rec_data_arr[$value[csf('booking_id')]]['quantity']+=$value[csf('quantity')];
                        $pi_rec_data_arr[$value[csf('booking_id')]]['order_rate']=$value[csf('order_rate')];
                        $pi_rec_data_arr[$value[csf('booking_id')]]['remarks']=$value[csf('remarks')];

                    }
                    //echo "<pre>";
                    //print_r($pi_rec_data_arr);
                    $i=1;
                    foreach($pi_rec_data_arr as $value)
                    {
                        if ($i%2==0)
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";

                        $item_description=$value['construction']." ".$value['copmposition'];

                        ?>
                            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trp_<? echo $i;?>','<? echo $bgcolor;?>')" id="trp_<? echo $i;?>">
                                <td width="40" align="center"><? echo $i; ?></td>
                                <td width="110" align="center"><p><? echo change_date_format($value['receive_date']); ?></p></td>
                                <td width="110"><p><? echo $value['recv_number']; ?></p></td>
                                <td width="90"><p>&nbsp;<? echo $value['challan_no']; ?></p></td>
                                <!-- <td width="90" align="right"><p>&nbsp;<? //echo $value['quantity']; ?></p></td>
                                <td width="70" align="right"><? //echo number_format($rate,2,'.',''); ?>&nbsp;</td> -->
                                <td align="right"><p><? echo number_format($value['amount'],2,'.',''); ?>&nbsp;</p></td>
                            </tr>
                        <?

                        $pi_wo_ids = $value['pi_wo_id'];
                        $tot_recv_amnt+=$value['amount'];
                        $i++;
                    }

                 ?>
                </tbody>
                <tfoot>
                    <th colspan="4" align="right">Total</th>
                    <!-- <th align="right"><?php //echo number_format($tot_recv_qnty,2,'.','');?></th>
                    <th>&nbsp;</th> -->
                    <th align="right"><?php echo number_format($tot_recv_amnt,2,'.','');?></th>
                </tfoot>
            </table>
        </div>
        <br>
        <table width="600" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
            <thead>
                <tr>
                    <th colspan="7">Receive Return</th>
                </tr>
                <tr>
                    <th width="40">SL</th>
                    <th width="110">Returned Date</th>
                    <th width="110">Return No</th>
                    <th width="90">Returned Value</th>
                    <th>Remarks</th>
                </tr>
            </thead>
        </table>
        <div style="width:620px; overflow-y:scroll; max-height:300px;" align="left" id="rtn_dtls_part">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" id="tbl_woven_finish_feb_rcv" >
                <tbody>
                 <?
                    //$pi_id_all=implode(",",array_flip($pi_id_all_array));
					//$pi_id_all=implode(",",$pi_id_all_array);
                    $all_booking_id=implode(",",$all_booking_id_array);
                    $compos=''; $tot_recv_qnty=0; $tot_recv_amnt=0; 
                    //echo $pi_id_all.Jahhid;die;
					if ($all_booking_id!='' || $all_booking_id!=0)
					{
						$sql_recv_return="select a.id as returned_id, a.issue_number as return_number, a.issue_date as returned_date, b.remarks, b.cons_amount,b.rcv_rate, b.rcv_amount
                        from inv_issue_master a, inv_transaction b
                        where a.id = b.mst_id and  a.item_category = 4 and a.entry_form=49 and a.company_id = $cbo_company_name and b.transaction_type in(3) and b.item_category = 4 and b.transaction_type = 3 and b.pi_wo_batch_no in ($pi_wo_ids) and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0";
					}
                    //echo $sql_recv_return;
                    //$pi_rec_data_arr= array();
                    $dataArray=sql_select($sql_recv_return);
                    $i=1;
                    foreach ($dataArray as $value) {

                        if ($i%2==0)
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";
                        if($value[csf('rcv_amount')] !="")
                        {
                            $returned_amount+=$value[csf('rcv_amount')];
                        }else{
                            $returned_amount+=$value[csf('cons_amount')];
                        }
                        ?>
                            <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trp_<? echo $i;?>','<? echo $bgcolor;?>')" id="trp_<? echo $i;?>">
                                <td width="40" align="center"><? echo $i; ?></td>
                                <td width="110" align="center"><p><? echo change_date_format($value[csf('returned_date')]); ?></p></td>
                                <td width="110"><p><? echo $value[csf('return_number')]; ?></p></td>
                                <td align="right" width="90"><p><? echo number_format($returned_amount,2,'.',''); ?>&nbsp;</p></td>
                                <td><p><? echo $value[csf('remarks')]; ?></p></td>
                            </tr>
                        <?

                        //$tot_recv_qnty+=$value['quantity'];
                        $tot_recv_amnt+=$returned_amount;
                        $i++;
                    }

                 ?>
                </tbody>
                <tfoot>
                    <th colspan="3" align="right">Total</th>
                    <th align="right"><?php echo number_format($tot_recv_amnt,2,'.','');?></th>
                    <th align="right">&nbsp;</th>
                </tfoot>
            </table>
        </div>
        <br>
        <table width="520" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="woven_acceptance_tble">
            <thead>
                <tr>
                    <th colspan="4">Acceptance Details </th>
                </tr>
                <tr>
                    <th width="120">Company Accpt. Date</th>
                    <th width="120">Bank Accpt. Date</th>
                    <th width="100">Invoice No</th>
                    <th>Value</th>
                </tr>
            </thead>
        </table>
        <div style="width:540px; overflow-y:scroll; max-height:430px;" align="left" id="accep_dtls_part">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="520" class="rpt_table" id="tbl_woven_finish_feb_acceptance" >
                <tbody>
                <?
                    //var_dump($pi_id_all);
					if ($allpi_ids!='' || $allpi_ids!=0)
					{
                        $acceptance_sql = "select m.id as pi_id, a.company_acc_date, a.bank_acc_date, a.invoice_no, b.current_acceptance_value as acceptance_value
						from com_pi_master_details m
						left  join com_import_invoice_dtls b on m.id=b.pi_id   and b.is_lc=1 and b.status_active=1 and b.is_deleted=0  and b.current_acceptance_value > 0
						left  join com_import_invoice_mst a on a.id=b.import_invoice_id
						where  m.id in($allpi_ids) and a.status_active =1 and a.is_deleted =0
						order by m.id";
                        //echo $acceptance_sql;

						$accep_result=sql_select($acceptance_sql);

						foreach($accep_result as $row)
						{
							$accep_breakdown_data[$row[csf("pi_id")]]++;
							$acceptance_arr[$row[csf("pi_id")]]+=$row[csf("acceptance_value")];


                            if ($i%2==0)
                                $bgcolor="#E9F3FF";
                            else
                                $bgcolor="#FFFFFF";
                            ?>
						    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
							<?
                                //$balance=$tot_lc_value-$row[csf("acceptance_value")];
                                
								?>
							    <td width="120" align="center"><p><? if($row[csf("company_acc_date")]!="" && $row[csf("company_acc_date")]!="0000-00-00") echo change_date_format($row[csf("company_acc_date")]);?></p></td>
								<td width="120" align="center"><p><? if($row[csf("bank_acc_date")]!="" && $row[csf("bank_acc_date")]!="0000-00-00") echo change_date_format($row[csf("bank_acc_date")]);?></p></td>
								<td width="100"  align="right"><p><? echo $row[csf("invoice_no")]; ?></p></td>
							    <td align="right"><p><? echo number_format($row[csf("acceptance_value")],2,'.',''); $toatl_acceptance_value += $row[csf("acceptance_value")]; ?></p></td>
								
                            </tr>

                            <?
                            $toatal_balance = ($tot_lc_value-$toatl_acceptance_value);
                            $i++;
                        }
                        
                    }
                    unset($accep_result);
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th align="right" colspan="3">Total</th>
                        <th align="right"><p><?php echo number_format($toatl_acceptance_value,2,'.',''); ?></p></th>
                    </tr>
                    <tr>
                        <th align="right" colspan="3">Total Balance</th>
                        <th align="right"><p><? echo number_format($toatal_balance,2,'.','');?>&nbsp;</p></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <br>
        <table width="520" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="woven_acceptance_tble">
            <thead>
                <tr>
                    <th colspan="7">Payment Details </th>
                </tr>
                <tr>
                    <th width="100"> Date</th>
                    <th width="100">System Number</th>
                    <th width="100">Invoice No</th>
                    <th width="100">Bank Reference</th>
                    <th>Payment Value</th>
                </tr>
            </thead>
        </table>
        <div style="width:520px; overflow-y:scroll; max-height:430px;"  align="left">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="500" class="rpt_table" id="tbl_woven_finish_feb_acceptance" >
                <tbody>
                <?
                    //var_dump($pi_id_all);$lc_id
					if ($lc_id!='' || $lc_id!=0)
					{
                        $payment_sql = "select m.id as payment_id, m.system_number, m.payment_date, a.invoice_no, a.bank_ref,b.accepted_ammount as payment_value 
                        from com_import_payment_mst m, com_import_invoice_mst a, com_import_payment b
                        where m.id=b.mst_id and m.invoice_id = a.id and b.invoice_id = a.id and a.is_lc = 1 and m.status_active=1 and M.IS_DELETED=0 and a.status_active=1 and a.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0  and m.lc_id in ($lc_ids) order by m.id";
                        //echo $payment_sql;

						$payment_result=sql_select($payment_sql);

						foreach($payment_result as $row)
						{
                            if ($i%2==0)
                                $bgcolor="#E9F3FF";
                            else
                                $bgcolor="#FFFFFF";
                            ?>
						    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
							<?
                                //$balance=$tot_lc_value-$row[csf("acceptance_value")];
                                
								?>
							    <td width="100" align="center"><p><? if($row[csf("payment_date")]!="" && $row[csf("payment_date")]!="0000-00-00") echo change_date_format($row[csf("payment_date")]);?>&nbsp;</p></td>
								<td width="100" align="center"><p><? echo $row[csf("system_number")];?>&nbsp;</p></td>
								<td width="100" align="right"><p><? echo $row[csf("invoice_no")]; ?></p></td>
								<td width="100" align="right"><p><? echo $row[csf("bank_ref")]; ?>&nbsp;</p></td>
							    <td width="100" align="right"><p><? echo $row[csf("payment_value")]; $toatl += $row[csf("payment_value")];?>&nbsp;</p></td>
								
                            </tr>
                            <?
                            $i++;
                        }
                    }
                    unset($payment_result);
                    ?>
                </tbody>
                <tfoot>
                    <th align="right" colspan="4">Total</th>
                    <th align="right"><?php echo number_format($toatl,2,'.',''); ?></th>
                </tfoot>
            </table>
        </div>
        	 <?
				echo signature_table(3, str_replace("'","",$cbo_company_name), "1100px");
			 ?>
        </div>
    <!--</fieldset>-->
	<?
    foreach (glob("$user_id*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}
?>
