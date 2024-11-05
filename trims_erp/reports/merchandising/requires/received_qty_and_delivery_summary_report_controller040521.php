<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{ 
	list($company,$type)=explode("_",$data);
	if($type==1)
	{
		echo create_drop_down( "cbo_customer_name", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $company, "$load_function");
	}
	else
	{
		echo create_drop_down( "cbo_customer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", "", "" );
	}	
	exit();	 
}

if ($action=="load_drop_down_location") {	
	echo create_drop_down( 'cbo_location_name', 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );
	exit();
}

if($action=="load_drop_down_subsection")
{
	$data=explode('_',$data);
	if($data[0]==1) $subID='1,2,3';
	else if($data[0]==3) $subID='4,5,18';
	else if($data[0]==5) $subID='6,7,8,9,10,11,12,13';
	else if($data[0]==10) $subID='14,15';
	else if($data[0]==7) $subID='19,20,21';
	else if($data[0]==9) $subID='22';
	else $subID='0';
	//echo $data[0]."**".$subID;
	//echo create_drop_down( "cboembtype_".$data[1], 80,$emb_type,"", 1, "-- Select --", "", "","","" ); 
	echo create_drop_down( "cbo_sub_section_id", 100, $trims_sub_section,"",1, "-- Select Sub-Section --","","",0,$subID,'','','','','',"");
	exit();
}

if ($action=="order_popup")
{	
	echo load_html_head_contents("Order Search","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
	function js_set_value(id)
	{
		//alert(booking_no); 
		document.getElementById('hidd_booking_data').value=id;
		parent.emailwindow.hide();
	}
	
</script>
</head>
<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                <thead>
                    <tr>                	 
                        <th width="120">W/O No</th>
                        <th colspan="2" width="160">W/O Date Range</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                            <input type="hidden" id="hidd_booking_data">
                        </th>
                    </tr>                                 
                </thead>
                <tr class="general">
                    <td><input name="txt_order_number" id="txt_order_number" class="text_boxes" style="width:90px"></td>
                    <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From"></td>
                    <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To"></td> 
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_order_number').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company;?>+'_'+<? echo $party_name;?>+'_'+<? echo $customer_source;?>, 'create_booking_search_list_view', 'search_div', 'received_qty_and_delivery_summary_report_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                    </td>
                </tr>
            </table>
            <div id="search_div"></div>   
        </form>
    </div>
	</body>           
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}
if($action=="create_booking_search_list_view")
{	
	$data=explode('_',$data);
	//print_r($data);
	
	if ($data[4]!=0) $company=" and a.company_id='$data[4]'";
	if ($data[4]!=0) $sample_company=" and c.company_id='$data[4]'";
	
	if($db_type==0)
	{
		if ($data[1]!="" &&  $data[2]!="") $booking_date = "and a.booking_date between '".change_date_format($data[1], "yyyy-mm-dd", "-")."' and '".change_date_format($data[2], "yyyy-mm-dd", "-")."'"; else $booking_date ="";

		if ($data[1]!="" &&  $data[2]!="") $sample_booking_date = "and c.booking_date between '".change_date_format($data[1], "yyyy-mm-dd", "-")."' and '".change_date_format($data[2], "yyyy-mm-dd", "-")."'"; else $sample_booking_date ="";
		
	}
	else if($db_type==2)
	{
		if ($data[1]!="" &&  $data[2]!="") $booking_date = "and a.booking_date between '".change_date_format($data[1], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
		

		if ($data[1]!="" &&  $data[2]!="") $sample_booking_date = "and c.booking_date between '".change_date_format($data[1], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."'"; else $sample_booking_date ="";
		
	} 
	
	if ($data[0]!="") $woorder_cond=" and a.booking_no like '%$data[0]%' "; 
	if ($data[0]!="") $sample_woorder_cond=" and c.booking_no like '%$data[0]%' ";
	
	 $sql= "SELECT a.id, a.booking_type, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id,1 as type from  wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type in(2,5) and a.status_active=1 and a.lock_another_process=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company $booking_date $woorder_cond group by a.id, a.booking_type, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id 
	UNION
	SELECT c.id, c.booking_type, c.booking_no, c.booking_no_prefix_num, c.company_id, c.buyer_id, c.job_no, c.booking_date, c.currency_id,2 as type from  wo_non_ord_samp_booking_mst c, wo_non_ord_samp_booking_dtls d where c.booking_no=d.booking_no and c.booking_type in(5) and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $sample_company $sample_booking_date $sample_woorder_cond group by c.id, c.booking_type, c.booking_no, c.booking_no_prefix_num, c.company_id, c.buyer_id, c.job_no, c.booking_date, c.currency_id";

	$data_array=sql_select($sql);
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="440" >
        <thead>
            <th width="30">SL</th>
            <th width="60">W/O No</th>
            <th width="70">W/O Date</th>
        </thead>
        </table>
        <div style="width:440px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="420" class="rpt_table" id="list_view">
        <tbody>
            <? 
            $i=1;
            foreach($data_array as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$row[csf('booking_no')].'_'.$row[csf('currency_id')].'_'.$row[csf('type')]; ?>")' style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="60" align="center"><? echo $row[csf('booking_no')]; ?></td>
                    <td width="70"><? echo change_date_format($row[csf('booking_date')]); ?></td>
                </tr>
				<? 
                $i++; 
            } 
            ?>
        </tbody>
    </table>
	<?
	exit();
}

if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	////////////////////////////////////////////////
	$cbo_company_id=str_replace("'","", $cbo_company_id);
	$cbo_location_name=str_replace("'","", $cbo_location_name);
	$cbo_section_id=str_replace("'","", $cbo_section_id);
	$cbo_sub_section_id=str_replace("'","", $cbo_sub_section_id);
	$txt_date_from=str_replace("'","", $txt_date_from);
	$txt_date_to=str_replace("'","", $txt_date_to);
	$sql_con = '';
	$sql_con_date = '';


	/*$cbo_customer_source=str_replace("'","", $cbo_customer_source);
	$cbo_customer_name=str_replace("'","", $cbo_customer_name);
	$txt_order_no=str_replace("'","", $txt_order_no);
	$hid_order_id=str_replace("'","", $hid_order_id);*/
	
	if($cbo_section_id){$where_con.=" and b.section='$cbo_section_id'";} 
	if($cbo_sub_section_id){$where_con.=" and b.sub_section='$cbo_sub_section_id'";} 
	if($cbo_location_name){$where_con.=" and a.location_id = $cbo_location_name";}
	/*if($cbo_customer_source){$where_con.=" and d.within_group='$cbo_customer_source'";} 
	if($cbo_customer_name){$where_con.=" and a.party_id='$cbo_customer_name'";} */
	
	//////////////////////////////////////////////////////////////////
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$item_group_arr = return_library_array("select id, item_name from lib_item_group where item_category=4 and status_active=1","id","item_name");
	$order_source_arr = array(1 => 'In-House', 2 => 'Sub-Contract');
	$currency_rate=return_field_value( "conversion_rate", "currency_conversion_rate", "currency=2 and status_active=1  and id in(select max(id) from currency_conversion_rate where currency=2 and is_deleted=0 and status_active=1)" , "conversion_rate" );
	//////////////////////////////////////////////////////////////////
	
	if($txt_date_from!="" and $txt_date_to!="")
	{	
		// $where_con.=" and a.production_date between '$txt_date_from' and '$txt_date_to'";
		$sql_con .=" and a.receive_date between '$txt_date_from' and '$txt_date_to'";
		$sql_con_date .= " and c.delivery_date between '$txt_date_from' and '$txt_date_to'";
	}
	//echo $sql_con_date; die;
	$delivery_sql = "select a.id,a.order_no,a.currency_id, d.delevery_qty as delivery_qty , d.break_down_details_id,b.section, a.receive_date, b.sub_section, b.item_group, b.source_for_order, b.order_quantity as order_qty, b.order_uom, e.rate, e.qnty, e.amount, b.booked_conv_fac as conv_factor,e.description from subcon_ord_mst a, trims_delivery_mst c, trims_delivery_dtls d ,subcon_ord_dtls b, subcon_ord_breakdown e where c.id = d.mst_id and a.order_no=d.order_no and a.id = b.mst_id and a.subcon_job=e.job_no_mst and b.id=e.mst_id and d.break_down_details_id=e.id  and a.is_deleted=0 and d.is_deleted=0 and c.entry_form=208 $sql_con_date $where_con and a.company_id = $cbo_company_id ";
	//group by b.section,a.id, a.order_no, a.receive_date ,a.currency_id, b.sub_section, b.item_group, b.source_for_order, b.order_uom,b.order_quantity
	$delivery_ord_result = sql_select($delivery_sql);
	foreach ($delivery_ord_result as $row) {
		$id .=$row[csf('id')].','; 
	}

	/*$ids= chop($id,',');
	if($ids!=''){
		$ids=implode(",",array_unique(explode(",",$ids)));
		$sql_ord_con_id= " and a.id in ($ids)";
	}*/
	$ord_sql = "select a.id,a.currency_id, b.section, a.receive_date, a.order_no, b.sub_section, b.item_group, b.source_for_order, sum(e.qnty) as order_qty, sum(e.amount) as amount , b.order_uom,e.description from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown e where a.id = b.mst_id and a.subcon_job=e.job_no_mst and b.id=e.mst_id  and a.is_deleted=0 and b.is_deleted=0 and a.entry_form=255 $where_con and a.company_id = $cbo_company_id $sql_ord_con_id   group by a.currency_id, b.section, a.order_no, a.receive_date, b.sub_section, b.item_group, b.source_for_order, b.order_uom,a.id,e.description";
	//and a.SUBCON_JOB in ('APPIL-TOR-21-01585','APPIL-TOR-21-01582','APPIL-TOR-21-01608','APPIL-TOR-21-01610','APPIL-TOR-21-01609','APPIL-TOR-21-01581','APPIL-TOR-21-01615','APPIL-TOR-21-01615')
	$ord_sql_result = sql_select($ord_sql);
	foreach ($ord_sql_result as $rows) {
		$key= $rows[csf('id')].'*'.$rows[csf('section')].'*'.$rows[csf('sub_section')].'*'.$rows[csf('item_group')].'*'.$rows[csf('source_for_order')].'*'.$rows[csf('order_uom')].'*'.$rows[csf('description')];
		$booked_array[$key]['order_quantity']+=$rows[csf('order_qty')];
		$booked_array[$key]['amount']+=$rows[csf('amount')];
		//$booked_array[$key]['delivery_qty']+=$rows[csf('order_qty')];
		$booked_array[$key]['currency_id']=$rows[csf('currency_id')];
	}

	$attr_arr = array('section','sub_section','item_group','source_for_order','order_uom');
	$dalivery_order_qty=array(); 

	foreach ($delivery_ord_result as $rows) {
		$key= $rows[csf('section')].'*'.$rows[csf('sub_section')].'*'.$rows[csf('item_group')].'*'.$rows[csf('source_for_order')].'*'.$rows[csf('order_uom')];
		$dalivery_order_qty[$key]['delivery_qty']+=$rows[csf('delivery_qty')];
		$dalivery_order_qty_usd[$rows[csf('id')].'*'.$key.'*'.$rows[csf('description')]]['delivery_qty']+=$rows[csf('delivery_qty')];
	}
	//echo "<pre>";
	//print_r($booked_array); die;
	foreach ($delivery_ord_result as $row) {
		//$id .=$row[csf('id')].','; 
		$key= $row[csf('section')].'*'.$row[csf('sub_section')].'*'.$row[csf('item_group')].'*'.$row[csf('source_for_order')].'*'.$row[csf('order_uom')];
		foreach ($attr_arr as $attr) {
			$trims_ord_arr[$key][$attr] = $row[csf($attr)];
		}

		$trims_ord_arr[$key]['delivery_qty'] = $dalivery_order_qty[$key]['delivery_qty'];
		
		
		$new_key=$row[csf('id')].'*'.$key.'*'.$row[csf('description')];
		if (!in_array($new_key, $array_chk))
		{
			$orderquantity=$booked_array[$new_key]['order_quantity'];
			$orderamount=$booked_array[$new_key]['amount'];
			$currency_id=$booked_array[$new_key]['currency_id'];
			 
			$rate=number_format($orderamount/$orderquantity,4);
			if($currency_id==1){
				$usdrate=number_format($rate/$currency_rate,4);
			}else{
				$usdrate=$rate;
			}

			//echo $orderquantity.'=='.$orderamount.'=='.$currency_id.'=='.$rate.'=='.$usdrate.'=='.$dalivery_order_qty_usd[$new_key]['delivery_qty']*$usdrate."<br>";
			//echo $usdrate.'=='.$dalivery_order_qty_usd[$new_key]['delivery_qty']."<br>";
			//$trims_ord_arr[$key]['delivery_amt_usd'] += $booked_array[$new_key]['delivery_qty']*$usdrate;
			$trims_ord_arr[$key]['delivery_amt_usd'] += $dalivery_order_qty_usd[$new_key]['delivery_qty']*$usdrate;
			$array_chk[]=$new_key;
		}
		

		$paymentDate=date('Y-m-d', strtotime($txt_date_from));
		$contractDateBegin = date('Y-m-d', strtotime($row[csf('receive_date')]));
		$contractDateEnd = date('Y-m-d', strtotime($row[csf('receive_date')]));
		    
		if (($paymentDate >= $contractDateBegin) && ($paymentDate <= $contractDateEnd)){
		   $trims_ord_arr[$key]['order_qty'] += $row[csf('order_qty')];
		   $trims_ord_arr[$key]['amount'] += $row[csf('amount')];
		}
	}

	//echo "<pre>";
	//print_r($trims_ord_arr); die;
	$ids= chop($id,',');
	if($ids!=''){
		$ids=implode(",",array_unique(explode(",",$ids)));
		$sql_con_id= " and a.id not in ($ids)";
	}
	
	$trims_ord_sql = "select a.currency_id, b.section, a.receive_date, a.order_no, b.sub_section, b.item_group, b.source_for_order, sum(b.order_quantity) as order_qty, sum(b.amount) as amount , b.order_uom from subcon_ord_mst a, subcon_ord_dtls b where a.id = b.mst_id and a.is_deleted=0 and b.is_deleted=0 and a.entry_form=255 $sql_con $where_con and a.company_id = $cbo_company_id $sql_con_id group by a.currency_id, b.section, a.order_no, a.receive_date, b.sub_section, b.item_group, b.source_for_order, b.order_uom";

	
	$trims_ord_result = sql_select($trims_ord_sql);
	foreach ($trims_ord_result as $row) {
		$key= $row[csf('section')].'*'.$row[csf('sub_section')].'*'.$row[csf('item_group')].'*'.$row[csf('source_for_order')].'*'.$row[csf('order_uom')];
		foreach ($attr_arr as $attr) {
			$trims_ord_arr[$key][$attr] = $row[csf($attr)];
		}
		$trims_ord_arr[$key]['order_qty'] += $row[csf('order_qty')];
		$trims_ord_arr[$key]['amount'] += $row[csf('amount')];
	}
	/*echo '<pre>';
	print_r($trims_ord_arr); die;*/
	$width=1000;
	ob_start();
	?>

	<style>
		.rpt_table tbody tr td {
			padding: 5px 10px;
		}
	</style>
	<div align="center" style="height:auto; width:<?php echo $width+20;?>px; margin:0 auto; padding:0;">
    	<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="caption" align="center">
			<thead class="form_caption" >
				<tr>
					<td colspan="8" align="center" style="font-size:20px;"><? echo $companyArr[$cbo_company_id]; ?></td>
				</tr>
				<tr>
					<td colspan="8" align="center" style="font-size:14px; font-weight:bold" ><? echo $report_title; ?></td>
				</tr>
				<tr>
					<td colspan="8" align="center" style="font-size:14px; font-weight:bold">
						<? echo " From : ".change_date_format($txt_date_from) ." To : ". change_date_format($txt_date_to) ;?>
					</td>
				</tr>
			</thead>
		</table>
        <table border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<?php echo $width;?>" rules="all" id="scroll_body" align="left">
        	<thead>
        		<tr>
	    			<th>SL</th>
	    			<th>Section</th>
	    			<th>Sub Section</th>
	    			<th>Trims Group</th>
	    			<th>Order UOM</th>
	    			<th>Receive Qty</th>
	    			<th>Delivery Qty</th>
	    			<th>Delivery Amount (USD)</th>
	    			<th>Source</th>
    			</tr>
        	</thead>
        	<tbody>
        		<?php
        			$sl = 1;
        			foreach ($trims_ord_arr as $key=> $row) {
        				if ($sl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
        				$rrate=$row['amount']/$row['order_qty'];
        				//$cur_rrate=$rrate;
        				$delivery_amt=$row['delivery_qty']*$rrate;
        				//echo $row['amount'].'=='.$row['order_qty'].'=='.number_format($rrate, 2).'=='.$delivery_amt.'#';
        				?>
        				<tr id='tr_2nd<? echo $sl; ?>' bgcolor="<?php echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $sl; ?>','<? echo $bgcolor; ?>')" style="cursor:pointer">
        					<td><?php echo $sl; ?></td>
        					<td><?php echo $trims_section[$row['section']]; ?></td>
        					<td><?php echo $trims_sub_section[$row['sub_section']]; ?></td>
        					<td><?php echo $item_group_arr[$row['item_group']]; ?></td>
        					<td><?php echo $unit_of_measurement[$row['order_uom']]; ?></td>
        					<td align="right"><?php echo number_format($row['order_qty'], 2); ?></td>
        					<td align="right"><?php echo number_format($row['delivery_qty'], 2);   ?></td>
        					<td align="right"><?php echo number_format($row['delivery_amt_usd'], 2); ?></td>
        					<td><?php echo $order_source_arr[$row['source_for_order']]; ?></td>
        				</tr>
        				<?php
        				//$row['delivery_qqqty'].'=='.
        				$sl++;
        				$total_delivery_qty +=$row['delivery_qty'];
        				$total_delivery_amt_usd +=$row['delivery_amt_usd'];
        			}
        		?>
        	</tbody>
        	<tfoot>
        		<th colspan="7"></th>
    			<th><?php echo number_format($total_delivery_amt_usd, 2); ?></th>
    			<th>&nbsp;</th>
        	</tfoot>
		</table>
    </div>

	<?php
	
	$html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    // foreach (glob("*.xls") as $filename) {
    // //if( @filemtime($filename) < (time()-$seconds_old) )
    // @unlink($filename);
    // }
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename";
    //echo "$html";
    exit();
}

?>