<?
header('Content-type:text/html; charset=utf-8');

session_start();

if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
require_once('../../includes/common.php');
$user_id=$_SESSION['logic_erp']['user_id'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
//--------------------------------------------------------------------------------------------------------------------
$company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
$buyer_short_name_library = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
$location_id_arr = return_library_array("select a.id,b.id as location_id from lib_prod_floor a, lib_location b where a.location_id=b.id", "id", "location_id");
$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
//print_r($location_id_arr);die;

if ($action == "load_drop_down_buyer") {
	echo create_drop_down("cbo_buyer_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  group by buy.id, buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer --", $selected, "");
	exit();
}

if ($action == "load_drop_down_location") {
	echo create_drop_down("cbo_location_id", 140, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id in ($data) order by location_name", "id,location_name", 1, "--Select Location--", $selected, "", 0);


}
if($action=="print_button_variable_setting")
{
	$print_report_format=0;
	$print_report_format=return_field_value("format_id","lib_report_template","template_name in(".$data.") and module_id=11 and report_id=158 and is_deleted=0 and status_active=1");
	echo "document.getElementById('report_ids').value = '".$print_report_format."';\n";
	echo "print_report_button_setting('".$print_report_format."');\n";
	exit();
}

if($action=="production_popup")
{
	echo load_html_head_contents("Production Report", "../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);

	if($db_type==0)
	{
		$date_from="'".change_date_format($from_date,'yyyy-mm-dd')."'";
		$date_to="'".change_date_format($to_date,'yyyy-mm-dd')."'";
	}
	else if($db_type==2)
	{
		$date_from="'".change_date_format($from_date,'','',1)."'";
		$date_to="'".change_date_format($to_date,'','',1)."'";
	}
	else
	{
		$date_from="";
		$date_to="";
	}
	if($is_booking==0)
	{
	    $order_wise_sql= "SELECT a.po_break_down_id, sum(b.production_qnty) as production_qnty
		from pro_garments_production_mst a,  pro_garments_production_dtls b,wo_po_color_size_breakdown c
		where a.production_type=$type and b.production_type=$type and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.serving_company=$company_id
		and  a.location=$location and a.production_date between $date_from and $date_to group by  a.po_break_down_id";
		$order_wise_data= sql_select($order_wise_sql);
		foreach($order_wise_data as $vals)
		{
			$all_order_id[$vals[csf("po_break_down_id")]]=$vals[csf("po_break_down_id")];
		}

	 	$order_count=count($all_order_id); $order_cond="";
		if($db_type==2 && $order_count>400)
		{
			$order_cond=" and (";
			$poArr=array_chunk($all_order_id,399);
			foreach($poArr as $poNos)
			{
				$poNos=implode(",",$poNos);
				$order_cond.=" id in($poNos) or ";
			}
			$order_cond=chop($order_cond,'or ');
		 $order_cond.=")";
		}
		else
		{
			  $order_cond=" and id in (".trim(implode(",",array_unique($all_order_id)),",").")";
		}
 			$order_library=return_library_array( "select id,po_number from  wo_po_break_down where status_active=1 and is_deleted=0 $order_cond", "id", "po_number"  );


	}
  else
  {
  	if($is_knitting=="knitting")
  	{
  		$knit_sqls = "SELECT a.booking_no ,a.knitting_company,a.location_id, a.buyer_id, sum(b.grey_receive_qnty) as production_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b
		where a.id=b.mst_id and a.status_active=1   and a.entry_form=2 and a.is_deleted=0 and a.location_id=$location and a.knitting_company=$company_id and a.receive_date between  $date_from and $date_to group by a.booking_no, a.knitting_company,a.location_id,a.buyer_id ";
		$order_wise_data= sql_select($knit_sqls);

  	}
  	else
  	{
  	    $dyeing_sqls = "SELECT  a.batch_no as booking_no, a.service_company ,a.floor_id, sum(b.production_qty) as production_qnty from pro_fab_subprocess a, pro_fab_subprocess_dtls b where a.id=b.mst_id and a.entry_form=35  and b.load_unload_id=2 and a.result=1  and a.service_company=$company_id and a.floor_id in(select id from lib_prod_floor where location_id=$location and status_active=1 and is_deleted=0 ) and a.process_end_date between $date_from and $date_to group by   a.batch_no,a.service_company,a.floor_id ";
  		$order_wise_data= sql_select($dyeing_sqls);
  	}


  }

	?>
<fieldset style="margin:0px auto;">
	<table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="500" border="1">
            <thead>
                <tr>
                	<th width="40" align="center">SI</th>
                    <th align="center"><? if($is_booking=="0") {echo "Order No";}
                    else
                     {
                     	if($is_knitting=="knitting") {echo "Booking No";}
                     	else {echo "Batch No";}
                     } ?></th>
                    <th  width="160" align="right"><? if($is_booking=="0") {echo "Order Qnty.";} else {
                    	if($is_knitting=="knitting") {echo "Booking Qnty.";}
                    	else {echo "Quantity";}

                     	} ?></th>
                </tr>

            </thead>
            <tbody>
            <?
			$i=1;
			$total=0;
			foreach($order_wise_data as $key=>$value)
			{

				?>
					<tr>
						<td align="center" ><? echo $i;?></td>
						<td align="center"><? if($is_booking==0) {echo $order_library[$value[csf("po_break_down_id")]]; } else {echo $value[csf("booking_no")];}?></td>
 						<td align="right"><? echo $value[csf("production_qnty")]; ?></td>
					</tr>
				<?
				$i++;
				$total+=$value[csf("production_qnty")];

			}
			?>
			<tr>
			<th colspan="2" align="right">Total</th>
 				<th align="right" ><? echo $total; ?></th>
			</tr>

            </tbody>

    </table>
</fieldset>


	<?

}

if($action=="knitting_popup")
{
	echo load_html_head_contents("Production Report", "../../", 1, 1,$unicode,1,'');
 	extract($_REQUEST);
 	$buyer_arr = return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
 	$company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
 	$company_cond = ($company_id == 0) ? "" : " and a.knitting_company=$company_id";
	//$location_cond = ($location == 0) ? "" : " and a.location_id=$location";
	$location_cond = ($location == 0) ? "" : " and a.knitting_location_id=$location";
	?>
	<script>
	var tableFilters =
		{
			col_operation: {
			id: ["grand_recv_qty_id"],
			col: [7],
			operation: ["sum"],
			write_method: ["innerHTML"]
			}
		}

	</script>
	<fieldset style="margin:0px auto;">
		<table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="820" border="1">
	            <thead>
	                <tr>
	                	<th width="30" align="center">SL</th>
	                	<th width="80" align="center">Date</th>
	                	<th width="110" align="center">Job</th>
	                	<th width="60" align="center">PO No</th>
	                	<th width="60" align="center">Buyer</th>
	                	<th width="100" align="center">Booking No.</th>
	                	<th width="100" align="center">Production ID</th>
	                	<th width="80" align="center">Prod Qty</th>
	                	<th width="80" align="center">Company</th>
	                	<th align="center">Location</th>

	                </tr>
	            </thead>
	           </table>
	       <div style="max-height:400px; overflow-y:scroll; width:840px;" id="scroll_body">
	       <table  border="1" class="rpt_table"  width="820" rules="all" id="table_body" >
	           <tbody>
	            <?
	            $job_array=array();
				$job_sql="select a.job_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
				$job_sql_result=sql_select($job_sql);
				foreach($job_sql_result as $row)
				{
					$job_array[$row[csf('id')]]['job']=$row[csf('job_no')];
					$job_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
				}

				$i=1;
				$sql_knitting="select a.receive_date,a.recv_number,a.knitting_company,a.location_id, a.buyer_id, b.grey_receive_qnty as recv_qty ,b.order_id,a.booking_no
					from inv_receive_master a, pro_grey_prod_entry_dtls b
					where a.id=b.mst_id and a.status_active=1 and a.entry_form=2 and a.is_deleted=0 and a.receive_date='$date' $company_cond $location_cond order by a.receive_date,b.order_id";
				$sql_result=sql_select($sql_knitting);
				foreach($sql_result as $row)
				{
					$po_number=$job_no='';
					$order_no=explode(",",$row[csf('order_id')]);
					foreach($order_no as $val)
					{
						if($val>0) $po_number.=$job_array[$val]['po_number'].",";
						if($val>0) $job_no.=$job_array[$val]['job'].",";
					}
					$po_number=chop(implode(",",array_unique(explode(",", $po_number))),',');
					$job_no=chop(implode(",",array_unique(explode(",", $job_no))),',');
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30" align="center" ><? echo $i;?></td>
							<td width="80" align="center" ><? echo $row[csf('receive_date')];?></td>
							<td width="110" align="center" ><? echo $job_no;?></td>
							<td width="60" align="center" style="word-break: break-all;" ><? echo $po_number;?></td>
							<td width="60" align="center" ><? echo $buyer_arr[$row[csf('buyer_id')]];?></td>
							<td width="100" align="center" ><? echo $row[csf('booking_no')];?></td>
							<td width="100" align="center" ><? echo $row[csf('recv_number')];?></td>
							<td width="80" align="center" ><? echo $row[csf('recv_qty')];?></td>
							<td width="80" align="center" ><? echo $company_arr[$row[csf('knitting_company')]];?></td>
							<td align="center" ><? echo $location_arr[$row[csf('location_id')]];?></td>
						</tr>
					<?
					$grand_recv_qty+=$row[csf('recv_qty')];
					$i++;
				}
				?>
	            </tbody>
	          </table>
	      </div>
	      <div style="max-height:400px; overflow-y:scroll; width:840px;">
		    <table  border="1" class="rpt_table"  width="820" rules="all" >
		     	<tfoot>
		            <tr>
		                <th width="30"></th>
		                <th width="80"></th>
		                <th width="110"></th>
		                <th width="60"></th>
		                <th width="60"></th>
		                <th width="100"></th>
		                <th width="100">Grand Total</th>
		                <th id="grand_recv_qty_id" width="80" align="center"><? echo $grand_recv_qty;?></th>
		                <th width="80"></th>
		                <th></th>
		             </tr>
		          </tfoot>
		     </table>
	     </div>
	    <script>
			setFilterGrid("table_body",-1,tableFilters);
		</script>


	</fieldset>


		<?

}
if($action=="show5_button_popup")
{
	echo load_html_head_contents("Production Report", "../../", 1, 1,$unicode,1,'');
 	extract($_REQUEST);
	//echo "<pre>";print_r ($_REQUEST);die;
 	$buyer_arr = return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
 	$company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
	$company_cond = ($company_id == 0) ? "" : " and b.company_name=$company_id";
	 $country_arr = return_library_array( "select id, country_name from  lib_country",'id','country_name');
 	$garment_item_arr = return_library_array( "select id, item_name from  LIB_GARMENT_ITEM",'id','item_name');
	 $date_from = str_replace("'", "", $txt_date_from);
	 $date_to = str_replace("'", "", $txt_date_to);
	 $start_date = change_date_format(str_replace("'", "", $txt_date_from), "", "", 1);
	$end_date = change_date_format(str_replace("'", "", $txt_date_to), "", "", 1);


	$location_cond = ($location == 0) ? "" : " and b.location_name=$location";


	//$cancel_date=" DATE(a.update_date) AS cancel_date";
	$date1=$date;
	$date=date('d-M-y',$date);
	$date=" and a.po_received_date ='$date'";

	//echo $date;
	if($prod_type==22) // order info
	{
		?>
		<script>
		var tableFilters =
			{
				col_operation: {
				id: ["grand_recv_qty_id"],
				col: [7],
				operation: ["sum"],
				write_method: ["innerHTML"]
				}
			}

		</script>
		<fieldset style="margin:0px auto;">
		<table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="820" border="1">
	            <thead>
	                <tr>
	                	<th width="30" align="center">SI</th>
	                	<th width="80" align="center">Date</th>
	                	<th width="110" align="center">Job</th>
	                	<th width="60" align="center">PO No</th>
	                	<th width="60" align="center">Buyer</th>
	                	<th width="100" align="center">Country</th>
	                	<th width="80" align="center">Garments Item</th>
	                	<th width="80" align="center">Prod Qty</th>
	                	<th width="90" align="center">Company</th>
	                	<th align="center">Location</th>

	                </tr>
	            </thead>
	       </table>
	       <div style="max-height:400px; overflow-y:scroll; width:840px;" id="scroll_body">
	    	<table  border="1" class="rpt_table"  width="820" rules="all" id="table_body" >
	            <tbody>
	            <?
	            $order_sql=("SELECT a.job_no_mst,a. po_quantity,a.po_number,b.buyer_name,

		    	(c.order_quantity*b.total_set_qnty ) as projec_qty_pcs,

				 b.company_name, b.location_name , b.total_set_qnty, a.po_received_date ,c.item_number_id,c.country_id
				from wo_po_break_down a, wo_po_details_master b ,wo_po_color_size_breakdown c
				where a.is_confirmed=2 and a.job_id=b.id and a.id=c.po_break_down_id  and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.is_deleted=0 and c.status_active=1 $company_cond $date $location_cond
				");
				$i=1;

		//echo $order_sql;
				$sql_result=sql_select($order_sql);

				 foreach($sql_result as $row)
				 {

				 	?>

				 		<tr >
				 		<td width="30" align="center"><?=$i?></td>
	                	<td width="80" align="center"><?=$row['PO_RECEIVED_DATE']?></td>
	                 	<td width="110" align="center"><?=$row['JOB_NO_MST']?></td>
	                	<td width="60" align="center"><?=$row['PO_NUMBER']?></td>
	             	    <td width="60" align="center"><?=$buyer_arr[$row['BUYER_NAME']]?></td>
	             	    <td width="100" align="center"><?=$country_arr[$row['COUNTRY_ID']]?></td>
	                	<td width="80" align="center"><?=$garment_item_arr[$row['ITEM_NUMBER_ID']]?></td>
	                	<td width="80" align="right"><?=$row['PROJEC_QTY_PCS']?></td>
	                	<td width="90" align="center"><?=$company_arr[$row['COMPANY_NAME']]?></td>
	                	<td align="center"><?=$location_arr[$row['LOCATION_NAME']]?></td>

				 		</tr>

				 <?
				     	$grand_recv_qty+=$row['PROJEC_QTY_PCS'];
				 	$i++;
				//echo "<pre>";print_r($job_arr);
				 }

				?>
	            </tbody>
	     </table>
	     </div>
	     <div style="max-height:400px; overflow-y:scroll; width:840px;">
	     <table  border="1" class="rpt_table"  width="820" rules="all" >
	     	<tfoot>
	            <tr>
				<th width="30" align="center"></th>
	                	<th width="80" align="center"></th>
	                	<th width="110" align="center"></th>
	                	<th width="60" align="center"></th>
	                	<th width="60" align="center"></th>
	                	<th width="100" align="center"></th>
	                	<th width="80" align="center">Grand Total</th>
	                	<th width="80" align="center"><?=$grand_recv_qty?></th>
	                	<th width="90" align="center"></th>
	                	<th align="center"></th>
	                <th></th>
	             </tr>
	          </tfoot>
	     </table>
	     </div>

	</fieldset>

			<?
		}

	else if($prod_type==23)
	{
		?>
		<script>
		var tableFilters =
			{
				col_operation: {
				id: ["grand_recv_qty_id"],
				col: [7],
				operation: ["sum"],
				write_method: ["innerHTML"]
				}
			}

		</script>
		<fieldset style="margin:0px auto;">
		<table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="820" border="1">
	            <thead>
	                <tr>
	                	<th width="30" align="center">SI</th>
	                	<th width="80" align="center">Date</th>
	                	<th width="110" align="center">Job</th>
	                	<th width="60" align="center">PO No</th>
	                	<th width="60" align="center">Buyer</th>
	                	<th width="100" align="center">Country</th>
	                	<th width="80" align="center">Garments Item</th>
	                	<th width="80" align="center">Prod Qty</th>
	                	<th width="90" align="center">Company</th>
	                	<th align="center">Location</th>

	                </tr>
	            </thead>
	       </table>
	       <div style="max-height:400px; overflow-y:scroll; width:840px;" id="scroll_body">
	    	<table  border="1" class="rpt_table"  width="820" rules="all" id="table_body" >
	            <tbody>
	            <?
	            $order_sql=("SELECT a.job_no_mst,a. po_quantity,a.po_number,b.buyer_name,

			     (c.order_quantity*b.total_set_qnty ) as confirm_qty_pcs,

				 b.company_name, b.location_name , b.total_set_qnty, a.po_received_date ,c.item_number_id,c.country_id
				from wo_po_break_down a, wo_po_details_master b ,wo_po_color_size_breakdown c
				where a.is_confirmed=1 and a.job_id=b.id and a.id=c.po_break_down_id  and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.is_deleted=0 and c.status_active=1 $company_cond $date $location_cond
				");
				$i=1;

			//echo $order_sql;
				$sql_result=sql_select($order_sql);

				 foreach($sql_result as $row)
				 {

				 	?>

				 		<tr >
				 		<td width="30" align="center"><?=$i?></td>
	                	<td width="80" align="center"><?=$row['PO_RECEIVED_DATE']?></td>
	                 	<td width="110" align="center"><?=$row['JOB_NO_MST']?></td>
	                	<td width="60" align="center"><?=$row['PO_NUMBER']?></td>
	             	    <td width="60" align="center"><?=$buyer_arr[$row['BUYER_NAME']]?></td>
	             	    <td width="100" align="center"><?=$country_arr[$row['COUNTRY_ID']]?></td>
	                	<td width="80" align="center"><?=$garment_item_arr[$row['ITEM_NUMBER_ID']]?></td>
	                	<td width="80" align="right"><?=$row['CONFIRM_QTY_PCS']?></td>
	                	<td width="90" align="center"><?=$company_arr[$row['COMPANY_NAME']]?></td>
	                	<td align="center"><?=$location_arr[$row['LOCATION_NAME']]?></td>

				 		</tr>

				 <?
				     	$grand_recv_qty+=$row['CONFIRM_QTY_PCS'];
				 	$i++;
				//echo "<pre>";print_r($job_arr);
				 }

				?>
	            </tbody>
	     </table>
	     </div>
	     <div style="max-height:400px; overflow-y:scroll; width:840px;">
	     <table  border="1" class="rpt_table"  width="820" rules="all" >
	     	<tfoot>
	            <tr>
				<th width="30" align="center"></th>
	                	<th width="80" align="center"></th>
	                	<th width="110" align="center"></th>
	                	<th width="60" align="center"></th>
	                	<th width="60" align="center"></th>
	                	<th width="100" align="center"></th>
	                	<th width="80" align="center">Grand Total</th>
	                	<th width="80" align="center"><?=$grand_recv_qty?></th>
	                	<th width="90" align="center"></th>
	                	<th align="center"></th>
	                <th></th>
	             </tr>
	          </tfoot>
	     </table>
	     </div>

	</fieldset>

			<?
	}
	else if($prod_type==24)

	{
		$cancel_date=" TO_CHAR(a.update_date, 'mm/DD/YYYY') AS cancel_date";
		$date1=date('d-M-Y',$date1);
		//$date1=" and a.update_date ='$date1'" ;
		$date_cond_order2=" and a.update_date between '$date1' and '$date1 11:59:59 PM' ";

		?>
		<script>
		var tableFilters =
			{
				col_operation: {
				id: ["grand_recv_qty_id"],
				col: [7],
				operation: ["sum"],
				write_method: ["innerHTML"]
				}
			}

		</script>
		<fieldset style="margin:0px auto;">
		<table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="820" border="1">
	            <thead>
	                <tr>
	                	<th width="30" align="center">SI</th>
	                	<th width="80" align="center">Date</th>
	                	<th width="110" align="center">Job</th>
	                	<th width="60" align="center">PO No</th>
	                	<th width="60" align="center">Buyer</th>
	                	<th width="100" align="center">Country</th>
	                	<th width="80" align="center">Garments Item</th>
	                	<th width="80" align="center">Prod Qty</th>
	                	<th width="90" align="center">Company</th>
	                	<th align="center">Location</th>

	                </tr>
	            </thead>
	       </table>
	       <div style="max-height:400px; overflow-y:scroll; width:840px;" id="scroll_body">
	    	<table  border="1" class="rpt_table"  width="820" rules="all" id="table_body" >
	            <tbody>
	            <?
	           $cancel_order_sql=("SELECT a.job_no_mst, b.company_name, a.po_number,b.buyer_name,a.po_received_date, $cancel_date, b.location_name,  b.total_set_qnty,c.item_number_id,c.country_id,a.update_date,
			   (  a.po_quantity*b.total_set_qnty ) as cancel_order_qty
			   from wo_po_break_down a, wo_po_details_master b,wo_po_color_size_breakdown c
			   where a.is_confirmed=1 and  a.job_id=b.id and a.id=c.po_break_down_id and a.status_active in(3) and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $company_cond $date_cond_order2 $location_con
			   group by a.job_no_mst, b.company_name, a.update_date, a.po_received_date, b.location_name, a.po_number,b.buyer_name, b.total_set_qnty, c.item_number_id,c.country_id, a.po_quantity order by a.update_date");
			$i=1;

		//	echo $cancel_order_sql;
				$sql_result=sql_select($cancel_order_sql);

				 foreach($sql_result as $row)
				 {

				 	?>

				 		<tr >
				 		<td width="30" align="center"><?=$i?></td>
	                	<td width="80" align="center"><?=$row['CANCEL_DATE']?></td>
	                 	<td width="110" align="center"><?=$row['JOB_NO_MST']?></td>
	                	<td width="60" align="center"><?=$row['PO_NUMBER']?></td>
	             	    <td width="60" align="center"><?=$buyer_arr[$row['BUYER_NAME']]?></td>
	             	    <td width="100" align="center"><?=$country_arr[$row['COUNTRY_ID']]?></td>
	                	<td width="80" align="center"><?=$garment_item_arr[$row['ITEM_NUMBER_ID']]?></td>
	                	<td width="80" align="right"><?=$row['CANCEL_ORDER_QTY']?></td>
	                	<td width="90" align="center"><?=$company_arr[$row['COMPANY_NAME']]?></td>
	                	<td align="center"><?=$location_arr[$row['LOCATION_NAME']]?></td>

				 		</tr>

				 <?
				     	$grand_recv_qty+=$row['CANCEL_ORDER_QTY'];
				 	$i++;
				//echo "<pre>";print_r($job_arr);
				 }

				?>
	            </tbody>
	     </table>
	     </div>
	     <div style="max-height:400px; overflow-y:scroll; width:840px;">
	     <table  border="1" class="rpt_table"  width="820" rules="all" >
	     	<tfoot>
	            <tr>
				<th width="30" align="center"></th>
	                	<th width="80" align="center"></th>
	                	<th width="110" align="center"></th>
	                	<th width="60" align="center"></th>
	                	<th width="60" align="center"></th>
	                	<th width="100" align="center"></th>
	                	<th width="80" align="center">Grand Total</th>
	                	<th width="80" align="center"><?=$grand_recv_qty?></th>
	                	<th width="90" align="center"></th>
	                	<th align="center"></th>
	                <th></th>
	             </tr>
	          </tfoot>
	     </table>
	     </div>

	</fieldset>

			<?
	}
	else if($prod_type==25)
	{
		?>
		<script>
		var tableFilters =
			{
				col_operation: {
				id: ["grand_recv_qty_id"],
				col: [7],
				operation: ["sum"],
				write_method: ["innerHTML"]
				}
			}

		</script>
		<fieldset style="margin:0px auto;">
		<table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="820" border="1">
	            <thead>
	                <tr>
	                	<th width="30" align="center">SI</th>
	                	<th width="80" align="center">Date</th>
	                	<th width="110" align="center">Job</th>
	                	<th width="60" align="center">PO No</th>
	                	<th width="60" align="center">Buyer</th>
	                	<th width="100" align="center">Country</th>
	                	<th width="80" align="center">Garments Item</th>
	                	<th width="80" align="center">Prod Qty</th>
	                	<th width="90" align="center">Company</th>
	                	<th align="center">Location</th>

	                </tr>
	            </thead>
	       </table>
	       <div style="max-height:400px; overflow-y:scroll; width:840px;" id="scroll_body">
	    	<table  border="1" class="rpt_table"  width="820" rules="all" id="table_body" >
	            <tbody>
	            <?
	            $order_sql=("SELECT a.job_no_mst,a. po_quantity,a.po_number,b.buyer_name,

			    sum(case when a.status_active=2 then c.order_quantity*b.total_set_qnty else 0 end) as inactive_qty_pcs,

				 b.company_name, b.location_name , b.total_set_qnty, a.po_received_date ,c.item_number_id,c.country_id
				from wo_po_break_down a, wo_po_details_master b ,wo_po_color_size_breakdown c
				where   a.job_id=b.id and a.id=c.po_break_down_id  and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $company_cond $date $location_cond group by a.job_no_mst,a. po_quantity,a.po_number,b.buyer_name, b.company_name, b.location_name , b.total_set_qnty, a.po_received_date ,c.item_number_id,c.country_id
				");
				$i=1;

		//echo $order_sql;
				$sql_result=sql_select($order_sql);

				 foreach($sql_result as $row)
				 {

				 	?>

				 		<tr >
				 		<td width="30" align="center"><?=$i?></td>
	                	<td width="80" align="center"><?=$row['PO_RECEIVED_DATE']?></td>
	                 	<td width="110" align="center"><?=$row['JOB_NO_MST']?></td>
	                	<td width="60" align="center"><?=$row['PO_NUMBER']?></td>
	             	    <td width="60" align="center"><?=$buyer_arr[$row['BUYER_NAME']]?></td>
	             	    <td width="100" align="center"><?=$country_arr[$row['COUNTRY_ID']]?></td>
	                	<td width="80" align="center"><?=$garment_item_arr[$row['ITEM_NUMBER_ID']]?></td>
	                	<td width="80" align="right"><?=$row['INACTIVE_QTY_PCS']?></td>
	                	<td width="90" align="center"><?=$company_arr[$row['COMPANY_NAME']]?></td>
	                	<td align="center"><?=$location_arr[$row['LOCATION_NAME']]?></td>

				 		</tr>

				 <?
				     	$grand_recv_qty+=$row['INACTIVE_QTY_PCS'];
				 	$i++;
				//echo "<pre>";print_r($job_arr);
				 }

				?>
	            </tbody>
	     </table>
	     </div>
	     <div style="max-height:400px; overflow-y:scroll; width:840px;">
	     <table  border="1" class="rpt_table"  width="820" rules="all" >
	     	<tfoot>
	            <tr>
				<th width="30" align="center"></th>
	                	<th width="80" align="center"></th>
	                	<th width="110" align="center"></th>
	                	<th width="60" align="center"></th>
	                	<th width="60" align="center"></th>
	                	<th width="100" align="center"></th>
	                	<th width="80" align="center">Grand Total</th>
	                	<th width="80" align="center"><?=$grand_recv_qty?></th>
	                	<th width="90" align="center"></th>
	                	<th align="center"></th>
	                <th></th>
	             </tr>
	          </tfoot>
	     </table>
	     </div>

	</fieldset>

			<?
	}
	else if($prod_type==26)
	{

		$company_cond_sort_book = ($company_name == 0) ? "" : " and a.company_id=$company_name";
		$date1=date('d-M-Y',$date1);

		$closing_date_cond = " and a.closing_date between '$date1' and '$date1'";
		?>
		<script>
		var tableFilters =
			{
				col_operation: {
				id: ["grand_recv_qty_id"],
				col: [7],
				operation: ["sum"],
				write_method: ["innerHTML"]
				}
			}

		</script>
		<fieldset style="margin:0px auto;">
		<table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="820" border="1">
	            <thead>
	                <tr>
	                	<th width="30" align="center">SI</th>
	                	<th width="80" align="center">Date</th>
	                	<th width="110" align="center">Job</th>
	                	<th width="60" align="center">PO No</th>
	                	<th width="60" align="center">Buyer</th>
	                	<th width="100" align="center">Country</th>
	                	<th width="80" align="center">Garments Item</th>
	                	<th width="80" align="center">Prod Qty</th>
	                	<th width="90" align="center">Company</th>
	                	<th align="center">Location</th>

	                </tr>
	            </thead>
	       </table>
	       <div style="max-height:400px; overflow-y:scroll; width:840px;" id="scroll_body">
	    	<table  border="1" class="rpt_table"  width="820" rules="all" id="table_body" >
	            <tbody>
	            <?
	            $ref_close_data=("SELECT a.company_id, a.closing_date, a.reference_type, a.closing_status, a.inv_pur_req_mst_id, a.mrr_system_no,b.po_number,b.job_no_mst,c.buyer_name,c.location_name,d.item_number_id,d.country_id, sum(d.order_quantity*c.total_set_qnty) as ref_closing_qty
				from inv_reference_closing a, wo_po_break_down b, wo_po_details_master c,wo_po_color_size_breakdown d where a.inv_pur_req_mst_id=b.id and b.job_id=c.id and b.id=d.po_break_down_id and a.closing_status=1 $company_cond_sort_book $closing_date_cond
				group by a.company_id, a.closing_date, a.reference_type, a.closing_status, a.inv_pur_req_mst_id, a.mrr_system_no,b.po_number,b.job_no_mst,c.buyer_name,c.location_name,d.item_number_id,d.country_id order by a.closing_date");
			//echo $ref_close_data; die;
				$i=1;



				$sql_result=sql_select($ref_close_data);

				 foreach($sql_result as $row)
				 {

				 	?>

				 		<tr >
				 		<td width="30" align="center"><?=$i?></td>
	                	<td width="80" align="center"><?=$row['CLOSING_DATE']?></td>
	                 	<td width="110" align="center"><?=$row['JOB_NO_MST']?></td>
	                	<td width="60" align="center"><?=$row['PO_NUMBER']?></td>
	             	    <td width="60" align="center"><?=$buyer_arr[$row['BUYER_NAME']]?></td>
	             	    <td width="100" align="center"><?=$country_arr[$row['COUNTRY_ID']]?></td>
	                	<td width="80" align="center"><?=$garment_item_arr[$row['ITEM_NUMBER_ID']]?></td>
	                	<td width="80" align="right"><?=$row['REF_CLOSING_QTY']?></td>
	                	<td width="90" align="center"><?=$company_arr[$row['COMPANY_ID']]?></td>
	                	<td align="center"><?=$location_arr[$row['LOCATION_NAME']]?></td>

				 		</tr>

				 <?
				     	$grand_recv_qty+=$row['REF_CLOSING_QTY'];
				 	$i++;
				//echo "<pre>";print_r($job_arr);
				 }

				?>
	            </tbody>
	     </table>
	     </div>
	     <div style="max-height:400px; overflow-y:scroll; width:840px;">
	     <table  border="1" class="rpt_table"  width="820" rules="all" >
	     	<tfoot>
	            <tr>
				<th width="30" align="center"></th>
	                	<th width="80" align="center"></th>
	                	<th width="110" align="center"></th>
	                	<th width="60" align="center"></th>
	                	<th width="60" align="center"></th>
	                	<th width="100" align="center"></th>
	                	<th width="80" align="center">Grand Total</th>
	                	<th width="80" align="center"><?=$grand_recv_qty?></th>
	                	<th width="90" align="center"></th>
	                	<th align="center"></th>
	                <th></th>
	             </tr>
	          </tfoot>
	     </table>
	     </div>

	</fieldset>

			<?
	}
	else if($prod_type==27)
	{

		$company_cond_sort_book = ($company_name == 0) ? "" : " and a.company_id=$company_name";
		$date1=date('d-M-Y',$date1);

		$closing_date_cond = " and a.closing_date between '$date1' and '$date1'";
		?>
		<script>
		var tableFilters =
			{
				col_operation: {
				id: ["grand_recv_qty_id"],
				col: [7],
				operation: ["sum"],
				write_method: ["innerHTML"]
				}
			}

		</script>
		<fieldset style="margin:0px auto;">
		<table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="820" border="1">
	            <thead>
	                <tr>
	                	<th width="30" align="center">SI</th>
	                	<th width="80" align="center">Date</th>
	                	<th width="110" align="center">Job</th>
	                	<th width="60" align="center">PO No</th>
	                	<th width="60" align="center">Buyer</th>
	                	<th width="100" align="center">Country</th>
	                	<th width="80" align="center">Garments Item</th>
	                	<th width="80" align="center">Prod Qty</th>
	                	<th width="90" align="center">Company</th>
	                	<th align="center">Location</th>

	                </tr>
	            </thead>
	       </table>
	       <div style="max-height:400px; overflow-y:scroll; width:840px;" id="scroll_body">
	    	<table  border="1" class="rpt_table"  width="820" rules="all" id="table_body" >
	            <tbody>
	            <?
	            $ref_close_data=("SELECT a.company_id, a.closing_date, a.reference_type, a.closing_status, a.inv_pur_req_mst_id, a.mrr_system_no,b.po_number,b.job_no_mst,c.buyer_name,c.location_name,d.item_number_id,d.country_id, sum(d.order_quantity*c.total_set_qnty) as ref_closing_qty
				from inv_reference_closing a, wo_po_break_down b, wo_po_details_master c,wo_po_color_size_breakdown d where a.inv_pur_req_mst_id=b.id and b.job_id=c.id and b.id=d.po_break_down_id and a.closing_status=1 $company_cond_sort_book $closing_date_cond
				group by a.company_id, a.closing_date, a.reference_type, a.closing_status, a.inv_pur_req_mst_id, a.mrr_system_no,b.po_number,b.job_no_mst,c.buyer_name,c.location_name,d.item_number_id,d.country_id order by a.closing_date");
			//echo $ref_close_data; die;
				$i=1;



				$sql_result=sql_select($ref_close_data);

				 foreach($sql_result as $row)
				 {

				 	?>

				 		<tr >
				 		<td width="30" align="center"><?=$i?></td>
	                	<td width="80" align="center"><?=$row['CLOSING_DATE']?></td>
	                 	<td width="110" align="center"><?=$row['JOB_NO_MST']?></td>
	                	<td width="60" align="center"><?=$row['PO_NUMBER']?></td>
	             	    <td width="60" align="center"><?=$buyer_arr[$row['BUYER_NAME']]?></td>
	             	    <td width="100" align="center"><?=$country_arr[$row['COUNTRY_ID']]?></td>
	                	<td width="80" align="center"><?=$garment_item_arr[$row['ITEM_NUMBER_ID']]?></td>
	                	<td width="80" align="right"><?=$row['REF_CLOSING_QTY']?></td>
	                	<td width="90" align="center"><?=$company_arr[$row['COMPANY_ID']]?></td>
	                	<td align="center"><?=$location_arr[$row['LOCATION_NAME']]?></td>

				 		</tr>

				 <?
				     	$grand_recv_qty+=$row['REF_CLOSING_QTY'];
				 	$i++;
				//echo "<pre>";print_r($job_arr);
				 }

				?>
	            </tbody>
	     </table>
	     </div>
	     <div style="max-height:400px; overflow-y:scroll; width:840px;">
	     <table  border="1" class="rpt_table"  width="820" rules="all" >
	     	<tfoot>
	            <tr>
				<th width="30" align="center"></th>
	                	<th width="80" align="center"></th>
	                	<th width="110" align="center"></th>
	                	<th width="60" align="center"></th>
	                	<th width="60" align="center"></th>
	                	<th width="100" align="center"></th>
	                	<th width="80" align="center">Grand Total</th>
	                	<th width="80" align="center"><?=$grand_recv_qty?></th>
	                	<th width="90" align="center"></th>
	                	<th align="center"></th>
	                <th></th>
	             </tr>
	          </tfoot>
	     </table>
	     </div>

	</fieldset>

			<?
	}
}





if($action=="dyeing_popup")
{
	echo load_html_head_contents("Production Report", "../../", 1, 1,$unicode,1,'');
 	extract($_REQUEST);
 	$buyer_arr = return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
 	$company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
 	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
 	$company_cond = ($company_id == 0) ? "" : " and a.service_company=$company_id";

	?>
	<script>
	var tableFilters =
		{
			col_operation: {
			id: ["grand_prod_qty_id"],
			col: [7],
			operation: ["sum"],
			write_method: ["innerHTML"]
			}
		}

	</script>
	<fieldset style="margin:0px auto;">
		<table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="820" border="1">
	            <thead>
	                <tr>
	                	<th width="30" align="center">SI</th>
	                	<th width="80" align="center">Date</th>
	                	<th width="110" align="center">Job</th>
	                	<th width="60" align="center">PO No</th>
	                	<th width="60" align="center">Buyer</th>
	                	<th width="100" align="center">Booking No.</th>
	                	<th width="80" align="center">Batch</th>
	                	<th width="80" align="center">Color</th>
	                	<th width="80" align="center">Prod Qty</th>
	                	<th align="center">Company</th>

	                </tr>
	            </thead>
	         </table>
	        <div style="max-height:400px; overflow-y:scroll; width:840px;" id="scroll_body">
	    	<table  border="1" class="rpt_table"  width="820" rules="all" id="table_body" >
	            <tbody>
	            <?
				$i=1;
				$sql_dyeing="select a.production_date, a.service_company, a.floor_id,b.production_qty as prod_qty,a.batch_no,a.batch_id,b.prod_id from pro_fab_subprocess a, pro_fab_subprocess_dtls b where a.id=b.mst_id and a.entry_form=35 and b.load_unload_id=2 and a.result=1 and a.production_date='$date' $company_cond order by a.production_date";
			//echo $sql_dyeing;
				$sql_result=sql_select($sql_dyeing);
				$batch_id="";
				foreach ($sql_result as $row) {
					$batch_id .= $row[csf('batch_id')].",";
				}
				$batch_id = implode(",",explode(",",chop($batch_id,",")));

				$batch_name_arr=array();
			 	$sql_batch=sql_select("select a.id, a.batch_no,a.color_id,a.booking_no,b.po_id from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.id in ($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
				$po_id="";$batch_arr=array();
				foreach ($sql_batch as $row) {
					$batch_arr[$row[csf('id')]]['batch_no'] = $row[csf('batch_no')];
					$batch_arr[$row[csf('id')]]['color_id'] = $row[csf('color_id')];
					$batch_arr[$row[csf('id')]]['booking_no'] = $row[csf('booking_no')];
					//$po_id .= $row[csf('po_id')].",";
					$booking_nos .= "'".$row[csf('booking_no')]."'".",";
				}
	 			//$po_id = implode(",",explode(",",chop($po_id,",")));
	 			$booking_no = implode(",",explode(",",chop($booking_nos,",")));

				$job_array=array();
				$job_sql="select a.buyer_id,b.job_no,c.po_number,b.booking_no from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c where a.booking_no=b.booking_no and b.po_break_down_id=c.id and b.booking_no in ($booking_no) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
				$job_sql_result=sql_select($job_sql);
				foreach($job_sql_result as $row)
				{
					$job_array[$row[csf('booking_no')]]['job_no'] = $row[csf('job_no')];
					$job_array[$row[csf('booking_no')]]['po_number'] = $row[csf('po_number')];
					$job_array[$row[csf('booking_no')]]['buyer_id'] = $row[csf('buyer_id')];
				}

				foreach($sql_result as $row)
				{
					$booking_no = $batch_arr[$row[csf('batch_id')]]['booking_no'];
					$job_no = $job_array[$booking_no]['job_no'];
					$po_number = $job_array[$booking_no]['po_number'];
					$buyer_name = $job_array[$booking_no]['buyer_id'];
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30" align="center" ><? echo $i;?></td>
							<td width="80" align="center" ><? echo $row[csf('production_date')];?></td>
							<td width="110" align="center" ><? echo $job_no;?></td>
							<td width="60" align="center" ><? echo $po_number;?></td>
							<td width="60" align="center" ><? echo $buyer_arr[$buyer_name];?></td>
							<td width="100" align="center" ><? echo $booking_no;?></td>
							<td width="80" align="center" ><? echo $batch_arr[$row[csf('batch_id')]]['batch_no'];?></td>
							<td width="80" align="center" ><? echo $color_arr[$batch_arr[$row[csf('batch_id')]]['color_id']];?></td>
							<td width="80" align="center" ><? echo $row[csf('prod_qty')];?></td>
							<td align="center" ><? echo $company_arr[$row[csf('service_company')]];?></td>
						</tr>
					<?
					$grand_prod_qty+=$row[csf('prod_qty')];
					$i++;
				}
				?>
	            </tbody>
	          </table>
	    	</div>
	    <div style="max-height:400px; overflow-y:scroll; width:840px;">
	    <table  border="1" class="rpt_table"  width="820" rules="all" >
	     	<tfoot>
	            <tr>
	                <th width="30"></th>
	                <th width="80"></th>
	                <th width="110"></th>
	                <th width="60"></th>
	                <th width="60"></th>
	                <th width="100"></th>
	                <th width="80">Grand Total</th>
	                <th id="grand_prod_qty_id" width="80" align="center"><? echo $grand_prod_qty;?></th>
	                <th width="80"></th>
	                <th></th>
	             </tr>
	          </tfoot>
	     </table>
	     </div>
	    <script>
			setFilterGrid("table_body",-1,tableFilters);
		</script>
	</fieldset>
	<?
}

if($action=="rmg_popup")
{
	echo load_html_head_contents("Production Report", "../../", 1, 1,$unicode,1,'');
 	extract($_REQUEST);
 	$country_arr = return_library_array( "select id, country_name from  lib_country",'id','country_name');
 	$buyer_arr = return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
 	$garment_item_arr = return_library_array( "select id, item_name from  LIB_GARMENT_ITEM",'id','item_name');
 	$company_cond = ($company_id == 0) ? "" : " and a.serving_company=$company_id";
 	$location_cond = ($location == 0) ? "" : " and a.location=$location";
 	$type_cond = ($type == 0) ? "" : " and a.production_type=$type";
 	$emble_type_cond = ($emble_type == 0) ? "" : " and a.embel_name=$emble_type";

	?>
	<script>
	var tableFilters =
		{
			col_operation: {
			id: ["grand_prod_quantity_id"],
			col: [7],
			operation: ["sum"],
			write_method: ["innerHTML"]
			}
		}

	</script>
	<fieldset style="margin:0px auto;">
		<table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="820" border="1">
	            <thead>
	                <tr>
	                	<th width="30" align="center">SI</th>
	                	<th width="80" align="center">Date</th>
	                	<th width="110" align="center">Job</th>
	                	<th width="60" align="center">PO No</th>
	                	<th width="60" align="center">Buyer</th>
	                	<th width="100" align="center">Country</th>
	                	<th width="80" align="center">Garments Item</th>
	                	<th width="80" align="center">Prod Qty</th>
	                	<th width="90" align="center">Company</th>
	                	<th align="center">Location</th>

	                </tr>
	            </thead>
	       </table>
	       <div style="max-height:400px; overflow-y:scroll; width:840px;" id="scroll_body">
	    	<table  border="1" class="rpt_table"  width="820" rules="all" id="table_body" >
	            <tbody>
	            <?
	            $job_array=array();
				$job_sql="select a.job_no, b.id, b.po_number,a.buyer_name from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
				$job_sql_result=sql_select($job_sql);
				foreach($job_sql_result as $row)
				{
					$job_array[$row[csf('id')]]['job']=$row[csf('job_no')];
					$job_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
					$job_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
				}

				$i=1;
				$sql_rmg="select a.production_date,a.serving_company,a.country_id,a.po_break_down_id, a.location,a.production_type, a.embel_name, b.production_qnty as prod_quantity,a.ITEM_NUMBER_ID from pro_garments_production_mst a ,pro_garments_production_dtls b
					where a.id=b.mst_id and a.production_type=b.production_type and a.production_date='$date' and a.status_active=1 $company_cond and a.is_deleted=0 $location_cond $type_cond $emble_type_cond and b.status_active=1 and b.is_deleted=0 order by a.production_date,a.po_break_down_id";
			//echo $sql_rmg;
				$sql_result=sql_select($sql_rmg);
				foreach($sql_result as $row)
				{

					?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30" align="center" ><? echo $i;?></td>
							<td width="80" align="center" ><? echo $row[csf('production_date')];?></td>
							<td width="110" align="center" ><? echo $job_array[$row[csf('po_break_down_id')]]['job'];?></td>
							<td width="60" align="center" ><? echo $job_array[$row[csf('po_break_down_id')]]['po_number'];?></td>
							<td width="80" align="center" ><? echo $buyer_arr[$job_array[$row[csf('po_break_down_id')]]['buyer_name']];?></td>
							<td width="100" align="center" ><? echo $country_arr[$row[csf('country_id')]];?></td>
							<td width="80" align="center" ><? echo $garment_item_arr[$row[csf('ITEM_NUMBER_ID')]];?></td>
							<td width="80" align="center" ><? echo $row[csf('prod_quantity')];?></td>
							<td width="90" align="center" ><? echo $company_arr[$row[csf('serving_company')]];?></td>
							<td align="center" ><? echo $location_arr[$row[csf('location')]];?></td>

						</tr>
					<?
					$grand_prod_quantity+=$row[csf('prod_quantity')];
					$i++;
				}
				?>
	            </tbody>
	    </table>
	    </div>
	     <div style="max-height:400px; overflow-y:scroll; width:840px;">
	    <table  border="1" class="rpt_table"  width="820" rules="all" >
	     	<tfoot>
	            <tr>
	                <th width="30"></th>
	                <th width="80"></th>
	                <th width="110"></th>
	                <th width="60"></th>
	                <th width="80"></th>
	                <th width="100"></th>
	                <th width="80">Grand Total</th>
	                <th id="grand_prod_quantity_id" width="80" align="center"><? echo $grand_prod_quantity;?></th>
	                <th width="90"></th>
	                <th></th>
	             </tr>
	          </tfoot>
	     </table>
	     </div>
	    <script>
			setFilterGrid("table_body",-1,tableFilters);
		</script>
	</fieldset>
	<?
}

if($action=="emb_popup")
{
	echo load_html_head_contents("Production Report", "../../", 1, 1,$unicode,1,'');
 	extract($_REQUEST);

 	$country_arr = return_library_array( "select id, country_name from  lib_country",'id','country_name');
 	$buyer_arr = return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
 	$garment_item_arr = return_library_array( "select id, item_name from  LIB_GARMENT_ITEM",'id','item_name');
 	$company_cond = ($company_id == 0) ? "" : " and serving_company=$company_id";
 	$location_cond = ($location == 0) ? "" : " and location=$location";
 	$type_cond = ($type == 0) ? "" : " and a.production_type=$type";
 	$emble_type_cond = ($emble_type == 0) ? "" : " and a.embel_name=$emble_type";
	?>
	<script>
		var tableFilters =
		{
			col_operation: {
			id: ["grand_prod_quantity_id2"],
			col: [7],
			operation: ["sum"],
			write_method: ["innerHTML"]
			}
		}
	</script>
	<fieldset style="margin:0px auto;">
		<table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="820" border="1">
	            <thead>
	                <tr>
	                	<th width="30" align="center">SI</th>
	                	<th width="80" align="center">Date</th>
	                	<th width="110" align="center">Job</th>
	                	<th width="60" align="center">PO No</th>
	                	<th width="60" align="center">Buyer</th>
	                	<th width="100" align="center">Country</th>
	                	<th width="80" align="center">Garments Item</th>
	                	<th width="80" align="center">Prod Qty</th>
	                	<th width="90" align="center">Company</th>
	                	<th align="center">Location</th>

	                </tr>
	            </thead>
	            </table>
	        <div style="max-height:400px; overflow-y:scroll; width:840px;" id="scroll_body">
	    	<table  border="1" class="rpt_table"  width="820" rules="all" id="table_body" >
	            <tbody>
	            <?
	            $job_array=array();
				$job_sql="select a.job_no, b.id, b.po_number,a.buyer_name from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
				$job_sql_result=sql_select($job_sql);
				foreach($job_sql_result as $row)
				{
					$job_array[$row[csf('id')]]['job']=$row[csf('job_no')];
					$job_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
					$job_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
				}

				$i=1;
				$sql_emb="select a.production_date,a.item_number_id,a.country_id,a.sending_company as serving_company ,a.serving_company as serv_company,a.sending_location as location,a.production_type, a.embel_name, b.production_qnty as prod_quantity,a.po_break_down_id,a.production_source,a.location as serv_location
				from pro_garments_production_mst a ,pro_garments_production_dtls b
				where a.id=b.mst_id and a.production_type=b.production_type and a.status_active=1 and a.production_date='$date' and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $location_cond $type_cond $emble_type_cond $company_cond order by a.production_date,a.po_break_down_id ";
				//echo $sql_emb;
				$sql_result=sql_select($sql_emb);
				foreach($sql_result as $row)
				{

					?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30" align="center" ><? echo $i;?></td>
							<td width="80" align="center" ><? echo $row[csf('production_date')];?></td>
							<td width="110" align="center" ><? echo $job_array[$row[csf('po_break_down_id')]]['job'];?></td>
							<td width="60" align="center" ><? echo $job_array[$row[csf('po_break_down_id')]]['po_number'];?></td>
							<td width="60" align="center" ><? echo $buyer_arr[$job_array[$row[csf('po_break_down_id')]]['buyer_name']];?></td>
							<td width="100" align="center" ><? echo $country_arr[$row[csf('country_id')]];?></td>
							<td width="80" align="center" ><? echo $garment_item_arr[$row[csf('item_number_id')]];?></td>
							<td width="80" align="center" ><? echo $row[csf('prod_quantity')];?></td>
							<td width="90" align="center" ><?
								if($row[csf('production_source')]==1)
								{
									echo $company_arr[$row[csf('serv_company')]];
								}
								else
								{
							 		echo $supplier_arr[$row[csf('serv_company')]];
								}
							 ?></td>
							<td align="center" ><? echo $location_arr[$row[csf('serv_location')]];?></td>

						</tr>
					<?
					$grand_prod_quantity+=$row[csf('prod_quantity')];
					$i++;
				}
				?>
	            </tbody>
	        </table>
	        </div>
	        <div style="max-height:400px; overflow-y:scroll; width:840px;">
	    	<table  border="1" class="rpt_table"  width="820" rules="all" >
	            <tfoot>
	            	<tr>
	                    <th width="30"></th>
	                    <th width="80"></th>
	                    <th width="110"></th>
	                    <th width="60"></th>
	                    <th width="60"></th>
	                    <th width="100"></th>
	                    <th width="80">Grand Total</th>
	                    <th width="80" align="center" ><? echo $grand_prod_quantity;?></th>
	                    <th id="grand_prod_quantity_id2" width="90"></th>
	                    <th></th>
	                </tr>
	            </tfoot>
	    	</table>
	        </div>

	  	<script>
			setFilterGrid("table_body",-1);
		</script>
	</fieldset>
	<?
}

if ($action == "report_generate")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
 	$company_name = str_replace("'", "", $cbo_company_name);
	$location_id = str_replace("'", "", $cbo_location_id);
	$buyer_name = str_replace("'", "", $cbo_buyer_name);
	$date_from = str_replace("'", "", $txt_date_from);
	$date_to = str_replace("'", "", $txt_date_to);
 	$company_knit_cond = ($company_name == 0) ? "" : " and a.knitting_company=$company_name";
	$company_dyeing_cond = ($company_name == 0) ? "" : " and a.service_company=$company_name";
	$company_cond = ($company_name == 0) ? "" : " and a.serving_company=$company_name";
	$company_cond2 = ($company_name == 0) ? "" : " and d.company_name=$company_name";
	$company_cond3 = ($company_name == 0) ? "" : " and a.working_company_id=$company_name";
	$company_cond4 = ($company_name == 0) ? "" : " and a.delivery_company_id=$company_name";
	$location_cond = ($location_id == 0) ? "" : " and a.location=$location_id";
	//$company_cond_emb = ($company_name == 0) ? "" : " and a.sending_company=$company_name";
	//$location_cond_emb = ($location_id == 0) ? "" : " and a.sending_location=$location_id";
 	$company_cond_emb = ($company_name == 0) ? "" : " and a.serving_company=$company_name";
	$location_cond_emb = ($location_id == 0) ? "" : " and a.location=$location_id";
	$location_id_cond = ($location_id == 0) ? "" : " and a.knitting_location_id=$location_id";
	if ($company_name==0) $companyCond=""; else $companyCond="  and a.company_id=$company_name";
	if ($company_name==0) $workingCompany_name_cond2=""; else $workingCompany_name_cond2="  and a.working_company_id=$company_name";
	if (str_replace("'", "", $txt_date_from) != "" || str_replace("'", "", $txt_date_to) != "")
	{
		if ($db_type == 0)
		{
			$start_date = change_date_format(str_replace("'", "", $txt_date_from), "yyyy-mm-dd", "");
			$end_date = change_date_format(str_replace("'", "", $txt_date_to), "yyyy-mm-dd", "");
			$date_cond_order2= " and a.update_date between '".$start_date."' and '".$end_date." 23:59:59' ";
			$cancel_date=" DATE(a.update_date) AS cancel_date";
		}
		else if ($db_type == 2)
		{
			$start_date = change_date_format(str_replace("'", "", $txt_date_from), "", "", 1);
			$end_date = change_date_format(str_replace("'", "", $txt_date_to), "", "", 1);
			$date_cond_order2=" and a.update_date between '".$start_date."' and '".$end_date." 11:59:59 PM' ";
			$cancel_date=" TO_CHAR(a.update_date, 'mm/DD/YYYY') AS cancel_date";
		}
		$date_cond = " and a.production_date between '$start_date' and '$end_date'";
		$date_cond2 = " and a.process_end_date between '$start_date' and '$end_date'";
		$date_cond_knit = " and a.receive_date between '$start_date' and '$end_date'";
		//========

		$date_cond_order = " and a.po_received_date between '$start_date' and '$end_date'";
		$date_cond_sort_book = " and a.booking_date between '$start_date' and '$end_date'";
		$date_cond_sample_prod = " and b.sewing_date between '$start_date' and '$end_date'";
		$date_cond_sample_delivery = " and a.ex_factory_date between '$start_date' and '$end_date'";
		$date_cond_tran = " and b.transaction_date between '$start_date' and '$end_date'";
		$date_cond_subc = " and a.product_date between '$start_date' and '$end_date'";
		$batch_date_cond = " and a.batch_date between '$start_date' and '$end_date'";
		$prod_sourc_date_cond = " and c.pr_date between '$start_date' and '$end_date'";
		$inspection_date_cond = " and a.inspection_date between '$start_date' and '$end_date'";
		$delivery_date_cond = " and a.delivery_date between '$start_date' and '$end_date'";
		$closing_date_cond = " and a.closing_date between '$start_date' and '$end_date'";
		$printing_date_cond = " and b.production_date between '$start_date' and '$end_date'";
		$printing_delivery_date_cond = " and a.delivery_date between '$start_date' and '$end_date'";
		$hang_tag_date_cond = " and a.production_date between '$start_date' and '$end_date'";
		$knit_fin_fab_issue_date_cond = " and c.issue_date between '$start_date' and '$end_date'";
		$cutting_date_cond = " and a.entry_date between '$start_date' and '$end_date'";
		$dates_com="and  f.process_end_date BETWEEN '$start_date' AND '$end_date'";
		$date_cond_finish_feb_dilv_store="and  a.delevery_date BETWEEN '$start_date' AND '$end_date'";


	}

	if($type==1) // Show
	{
		// knitting summary
		$knit_sqls = sql_select("select a.receive_date,a.knitting_company,a.knitting_location_id as location_id, a.buyer_id, sum(b.grey_receive_qnty) as recv_qty
					from inv_receive_master a, pro_grey_prod_entry_dtls b
					where a.id=b.mst_id and a.status_active=1 $date_cond_knit $company_knit_cond and a.entry_form=2 and a.is_deleted=0 $location_id_cond
					group by  a.receive_date,a.knitting_company,a.knitting_location_id,a.buyer_id order by a.receive_date");


		$rev_qty = array();
		foreach ($knit_sqls as $value) {
			$rev_qty[$value[csf('knitting_company')]][$value[csf('location_id')]][$value[csf('receive_date')]]['knitting'] += $value[csf('recv_qty')];
			$rev_qty2[$value[csf('knitting_company')]][$value[csf('location_id')]]['knitting'] += $value[csf('recv_qty')];
		}
	 	// dyeing summary
		$dyeing_sqls = sql_select("select a.production_date, a.service_company, a.floor_id, sum(b.production_qty) as prod_qty from pro_fab_subprocess a, pro_fab_subprocess_dtls b where a.id=b.mst_id and a.entry_form=35 and b.load_unload_id=2 and a.result=1 $date_cond $company_dyeing_cond group by a.production_date, a.service_company,a.floor_id order by a.production_date");
	 	foreach ($dyeing_sqls as $value) {
			$rev_qty[$value[csf('service_company')]][$location_id_arr[$value[csf('floor_id')]]][$value[csf('production_date')]]['dyeing'] += $value[csf('prod_qty')];
			$rev_qty2[$value[csf('service_company')]][$location_id_arr[$value[csf('floor_id')]]]['dyeing'] += $value[csf('prod_qty')];

		}
		//echo "<pre>";
		//print_r($rev_qtys);die;
	 	$sqls = sql_select("SELECT a.production_date,a.serving_company,a.location,a.production_type, a.embel_name, sum(b.production_qnty) as prod_quantity
						from pro_garments_production_mst a ,pro_garments_production_dtls b,wo_po_color_size_breakdown c
						where a.id=b.mst_id and a.production_type=b.production_type and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and  a.status_active=1 $date_cond $company_cond and a.is_deleted=0 $location_cond and b.status_active=1 and b.is_deleted=0
						group by a.production_date,a.serving_company,a.location,a.production_type, a.embel_name order by a.production_date");

		/*$sqls_emb = sql_select("SELECT a.production_date,a.sending_company as serving_company,a.sending_location as location,a.production_type, a.embel_name, sum(b.production_qnty) as prod_quantity
						from pro_garments_production_mst a ,pro_garments_production_dtls b,wo_po_color_size_breakdown c
						where a.id=b.mst_id and a.production_type=b.production_type and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and a.status_active=1 $date_cond $company_cond_emb and a.is_deleted=0 $location_cond_emb and b.status_active=1 and b.is_deleted=0
						group by a.production_date,a.sending_company,a.sending_location,a.production_type, a.embel_name order by a.production_date");*/

						$sqls_emb = sql_select("SELECT a.production_date,a.serving_company,a.location,a.production_type, a.embel_name, sum(b.production_qnty) as prod_quantity
						from pro_garments_production_mst a ,pro_garments_production_dtls b,wo_po_color_size_breakdown c
						where a.id=b.mst_id and a.production_type=b.production_type and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and a.status_active=1 $date_cond $company_cond_emb and a.is_deleted=0 $location_cond_emb and b.status_active=1 and b.is_deleted=0
						group by a.production_date,a.serving_company,a.location,a.production_type, a.embel_name order by a.production_date");

	 	foreach($sqls_emb as $value)
		{
			if ($value[csf('production_type')] == 2)
			 {
				if ($value[csf('embel_name')] == 1)
				 {
					$rev_qty[$value[csf('serving_company')]][$value[csf('location')]][$value[csf('production_date')]]['print_issue'] += $value[csf('prod_quantity')];
					$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['print_issue'] += $value[csf('prod_quantity')];
				 }

				 else if ($value[csf('embel_name')] == 2) {
					$rev_qty[$value[csf('serving_company')]][$value[csf('location')]][$value[csf('production_date')]]['emb_issue'] += $value[csf('prod_quantity')];
					$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['emb_issue'] += $value[csf('prod_quantity')];
				}

				else if ($value[csf('embel_name')] == 4) {
					$rev_qty[$value[csf('serving_company')]][$value[csf('location')]][$value[csf('production_date')]]['special_issue'] += $value[csf('prod_quantity')];
					$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['special_issue'] += $value[csf('prod_quantity')];
				}

				else if ($value[csf('embel_name')] == 5) {
					$rev_qty[$value[csf('serving_company')]][$value[csf('location')]][$value[csf('production_date')]]['dyeing_issue'] += $value[csf('prod_quantity')];
					$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['dyeing_issue'] += $value[csf('prod_quantity')];
				}
			}

			else if ($value[csf('production_type')] == 3) {
				if ($value[csf('embel_name')] == 1) {
					$rev_qty[$value[csf('serving_company')]][$value[csf('location')]][$value[csf('production_date')]]['print_rcv'] += $value[csf('prod_quantity')];
					$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['print_rcv'] += $value[csf('prod_quantity')];
				}

				else if ($value[csf('embel_name')] == 2) {
					$rev_qty[$value[csf('serving_company')]][$value[csf('location')]][$value[csf('production_date')]]['emb_rcv'] += $value[csf('prod_quantity')];
					$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['emb_rcv'] += $value[csf('prod_quantity')];
				}

				else if ($value[csf('embel_name')] == 4) {
					$rev_qty[$value[csf('serving_company')]][$value[csf('location')]][$value[csf('production_date')]]['special_rcv'] += $value[csf('prod_quantity')];
					$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['special_rcv'] += $value[csf('prod_quantity')];
				}

				else if ($value[csf('embel_name')] == 5) {
					$rev_qty[$value[csf('serving_company')]][$value[csf('location')]][$value[csf('production_date')]]['dyeing_rcv'] += $value[csf('prod_quantity')];
					$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['dyeing_rcv'] += $value[csf('prod_quantity')];
				}
			}



		}


		foreach ($sqls as $value) {
			if ($value[csf('production_type')] == 1) {
				$rev_qty[$value[csf('serving_company')]][$value[csf('location')]][$value[csf('production_date')]]['cutting'] += $value[csf('prod_quantity')];
				$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['cutting'] += $value[csf('prod_quantity')];
			} else if ($value[csf('production_type')] == 5) {
				$rev_qty[$value[csf('serving_company')]][$value[csf('location')]][$value[csf('production_date')]]['sewing'] += $value[csf('prod_quantity')];
				$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['sewing'] += $value[csf('prod_quantity')];
			} else if ($value[csf('production_type')] == 7) {
				$rev_qty[$value[csf('serving_company')]][$value[csf('location')]][$value[csf('production_date')]]['iron'] += $value[csf('prod_quantity')];
				$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['iron'] += $value[csf('prod_quantity')];
			} else if ($value[csf('production_type')] == 8) {
				$rev_qty[$value[csf('serving_company')]][$value[csf('location')]][$value[csf('production_date')]]['finish'] += $value[csf('prod_quantity')];
				$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['finish'] += $value[csf('prod_quantity')];
			} else if ($value[csf('production_type')] == 2) {
				   if ($value[csf('embel_name')] == 3) {
					$rev_qty[$value[csf('serving_company')]][$value[csf('location')]][$value[csf('production_date')]]['wash_issue'] += $value[csf('prod_quantity')];
					$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['wash_issue'] += $value[csf('prod_quantity')];
				}


			} else if ($value[csf('production_type')] == 3) {
				  if ($value[csf('embel_name')] == 3) {
					$rev_qty[$value[csf('serving_company')]][$value[csf('location')]][$value[csf('production_date')]]['wash_rcv'] += $value[csf('prod_quantity')];
					$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['wash_rcv'] += $value[csf('prod_quantity')];
				}


			}
		}
		ob_start();
		?>

	    <fieldset style="width: 1320px; margin:20px auto;">
	        <table cellpadding="0" cellspacing="0" style="text-align: center; width: 100%">
	            <tr class="form_caption" style="border:none;">
	                <td align="center" width="100%" colspan="19"><strong style="font-size:25px"><?
							$com_name = return_field_value("company_name", "lib_company", "id=" . $cbo_company_name, "company_name");
							echo $com_name; ?></strong></td>
	            </tr>

	            <tr class="form_caption" style="border:none;">
	                <td align="center" width="100%" colspan="19">
	                    <strong style="font-size:16px">Production Summary:
							<?
							if ($start_date != '') echo change_date_format($start_date) . ' To ' . change_date_format($end_date); else echo '';
							?>
	                    </strong>
	                </td>
	            </tr>

	            <tr>
	                <th colspan="19" style="text-align: right;">Date of Print:&nbsp&nbsp<? echo date('d-m-Y'); ?></th>
	            </tr>

	            <tr>
	                <th colspan="19" style="text-align: right;">Time of Print:&nbsp&nbsp<? echo date('h:i A'); ?></th>
	            </tr>
	        </table>
	        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
	            <thead>
		            <tr>
		                <th width="30">SL</th>
		                <th width="80">DATE</th>
		                <th width="80">KNITTING</th>
		                <th width="80">DYEING</th>
		                <th width="80">CUTTING</th>
		                <th width="80">PRINT SEND</th>
		                <th width="80">PRINT RCV</th>
		                <th width="80">Emb SEND</th>
		                <th width="80">Emb RCV</th>
		                <th width="80">Special Work Send</th>
		                <th width="80">Special Work RCV</th>
		                <th width="80">Gmt Dyeing SEND</th>
		                <th width="80">Gmt Dyeing RCV</th>
		                <th width="80">WASH SEND</th>
		                <th width="80">WASH RCV</th>
		                <th width="80">SEWING</th>
		                <th width="80">IRON</th>
		                <th width="80">FINISH</th>
		                <th width="">REMARKS</th>
		            </tr>
	            </thead>
				<?php
				$date_arr = array();
				$date_fri_arr = array();

				foreach ($rev_qty as $company => $result_row) {
					foreach ($result_row as $location => $locationValue) {
						ksort($locationValue);
						$i = 1;
						$sub_total_knitting_qty = 0;
						$sub_total_dyeing_qty = 0;
						$sub_total_cutting_qty = 0;
						$sub_total_print_issue_qty = 0;
						$sub_total_print_rcv_qty = 0;

						$sub_total_emb_issue_qty = 0;
						$sub_total_emb_rcv_qty = 0;

						$sub_total_special_issue_qty = 0;
						$sub_total_special_rcv_qty = 0;

						$sub_total_dyeing_issue_qty = 0;
						$sub_total_dyeing_rcv_qty = 0;

						$sub_total_wash_issue_qty = 0;
						$sub_total_wash_rcv_qty = 0;
						$sub_total_sewing_qty = 0;
						$sub_total_iron_qty = 0;
						$sub_total_finish_qty = 0;
						$date_count = count($locationValue);
						foreach ($locationValue as $date => $row) {
							if (!in_array($date, $date_arr)) {
								$date_arr[] = $date;
							}
							if ($i % 2 == 1) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
							if ($i == 1) {
								?>
	                            <tr>
	                                <th colspan="20" style="background: #C2DCFF; text-align: left; padding-left: 5px;">
										<?=$company_arr[$company] . ", " . $location_arr[$location]; ?>
	                                </th>
	                            </tr>
							<?php } ?>
	                        <tr bgcolor="<? $timestamp = strtotime($date);$day_name= date("l", $timestamp );
	                        if($day_name=="Friday"){echo "crimson";} else{echo $bgcolor; }?>" style="<?if($day_name=="Friday"){echo 'color:white;';}?>">
	                            <td align="center"><?  echo $i; ?></td>
	                            <td align="center"><? echo change_date_format($date); ?></td>
	                            <td align="right"><a href="##" onClick="openmypage_knit_popup(<? echo $company; ?>,'<? echo $location; ?>','knitting_popup','Knitting PopUp','<? echo $date; ?>');" ><? echo number_format($row['knitting'], 2); ?></a></td>
	                            <td align="right"><a href="##" onClick="openmypage_dyeing_popup(<? echo $company; ?>,'<? echo $location; ?>','dyeing_popup','Dyeing PopUp','<? echo $date; ?>');" ><? echo number_format($row['dyeing'], 2); ?></a></td>
	                            <td align="right"><a href="##" onClick="openmypage_emb_popup(<? echo $company; ?>,'<? echo $location; ?>','rmg_popup','CUTTING PopUp','<? echo $date;?>','1','0','1','cutting')" ><? echo number_format($row['cutting'], 0); ?></a></td>

	                            <td align="right"><a href="##" onClick="openmypage_emb_popup(<? echo $company; ?>,'<? echo $location; ?>','emb_popup','PRINT Issue PopUp','<? echo $date;?>','2','1','0','print_issue');" ><? echo number_format($qtyValue['print_issue'], 0); ?></a></td>

	                            <td align="right"><a href="##" onClick="openmypage_emb_popup(<? echo $company; ?>,'<? echo $location; ?>','emb_popup','PRINT Recv PopUp','<? echo $date;?>','3','1','0','print_rcv');" ><? echo number_format($qtyValue['print_rcv'], 0); ?></a></td>
	                            <td align="right"><a href="##" onClick="openmypage_emb_popup(<? echo $company; ?>,'<? echo $location; ?>','emb_popup','EMB Issue PopUp','<? echo $date;?>','2','2','0','emb_issue');" ><? echo number_format($qtyValue['emb_issue'], 0); ?></a></td>
	                            <td align="right"><a href="##" onClick="openmypage_emb_popup(<? echo $company; ?>,'<? echo $location; ?>','emb_popup','EMB Recv PopUp','<? echo $date;?>','3','2','0','emb_rcv');" ><? echo number_format($qtyValue['emb_rcv'], 0); ?></a></td>
	                            <td align="right"><a href="##" onClick="openmypage_emb_popup(<? echo $company; ?>,'<? echo $location; ?>','emb_popup','Special Issue PopUp','<? echo $date;?>','2','4','0','special_issue');" ><? echo number_format($qtyValue['special_issue'], 0); ?></a></td>
	                            <td align="right"><a href="##" onClick="openmypage_emb_popup(<? echo $company; ?>,'<? echo $location; ?>','emb_popup','Special Recv PopUp','<? echo $date;?>','3','4','0','special_rcv');" ><? echo number_format($qtyValue['special_rcv'], 0); ?></a></td>
	                            <td align="right"><a href="##" onClick="openmypage_emb_popup(<? echo $company; ?>,'<? echo $location; ?>','emb_popup','DYEING Issue PopUp','<? echo $date;?>','2','5','0','dyeing_issue');" ><? echo number_format($qtyValue['dyeing_issue'], 0); ?></a></td>
	                            <td align="right"><a href="##" onClick="openmypage_emb_popup(<? echo $company; ?>,'<? echo $location; ?>','emb_popup','DYEING Recv PopUp','<? echo $date;?>','3','5','0','dyeing_rcv');" ><? echo number_format($qtyValue['dyeing_rcv'], 0); ?></a></td>
	                            <td align="right"><a href="##" onClick="openmypage_emb_popup(<? echo $company; ?>,'<? echo $location; ?>','emb_popup','WASH Issue PopUp','<? echo $date;?>','2','3','0','wash_issue');" ><? echo number_format($qtyValue['wash_issue'], 0); ?></a></td>
	                            <td align="right"><a href="##" onClick="openmypage_emb_popup(<? echo $company; ?>,'<? echo $location; ?>','emb_popup','WASH Recv PopUp','<? echo $date;?>','3','3','0','wash_rcv');" ><? echo number_format($qtyValue['wash_rcv'], 0); ?></a></td>
	                            <td align="right"><a href="##" onClick="openmypage_rmg_popup(<? echo $company; ?>,'<? echo $location; ?>','rmg_popup','SEWING PopUp','<? echo $date;?>','5','0','0','sewing');" ><? echo number_format($qtyValue['sewing'], 0); ?></a></td>
	                            <td align="right"><a href="##" onClick="openmypage_rmg_popup(<? echo $company; ?>,'<? echo $location; ?>','rmg_popup','IRON PopUp','<? echo $date;?>','7','0','0','iron');" ><? echo number_format($qtyValue['iron'], 0); ?></a></td>
	                            <td align="right"><a href="##" onClick="openmypage_rmg_popup(<? echo $company; ?>,'<? echo $location; ?>','rmg_popup','FINISH PopUp','<? echo $date;?>','8','0','0','finish');" ><? echo number_format($qtyValue['finish'], 0); ?></a></td>
	                            <td></td>
	                        </tr>
							<?
							$i++;

							$sub_total_knitting_qty += $qtyValue['knitting'];
							$sub_total_dyeing_qty += $qtyValue['dyeing'];
							$sub_total_cutting_qty += $qtyValue['cutting'];
							$sub_total_print_issue_qty += $qtyValue['print_issue'];
							$sub_total_print_rcv_qty += $qtyValue['print_rcv'];

							$sub_total_emb_issue_qty += $qtyValue['emb_issue'];
							$sub_total_emb_rcv_qty += $qtyValue['emb_rcv'];

							$sub_total_special_issue_qty += $qtyValue['special_issue'];
							$sub_total_special_rcv_qty += $qtyValue['special_rcv'];

							$sub_total_dyeing_issue_qty += $qtyValue['dyeing_issue'];
							$sub_total_dyeing_rcv_qty += $qtyValue['dyeing_rcv'];


							$sub_total_wash_issue_qty += $qtyValue['wash_issue'];
							$sub_total_wash_rcv_qty += $qtyValue['wash_rcv'];
							$sub_total_sewing_qty += $qtyValue['sewing'];
							$sub_total_iron_qty += $qtyValue['iron'];
							$sub_total_finish_qty += $qtyValue['finish'];

							$total_knitting_qty += $qtyValue['knitting'];
							$total_dyeing_qty += $qtyValue['dyeing'];
							$total_cutting_qty += $qtyValue['cutting'];
							$total_print_issue_qty += $qtyValue['print_issue'];
							$total_print_rcv_qty += $qtyValue['print_rcv'];

							$total_emb_issue_qty += $qtyValue['emb_issue'];
							$total_emb_rcv_qty += $qtyValue['emb_rcv'];

							$total_special_issue_qty += $qtyValue['special_issue'];
							$total_special_rcv_qty += $qtyValue['special_rcv'];

							$total_dyeing_issue_qty += $qtyValue['dyeing_issue'];
							$total_dyeing_rcv_qty += $qtyValue['dyeing_rcv'];

							$total_wash_issue_qty += $qtyValue['wash_issue'];
							$total_wash_rcv_qty += $qtyValue['wash_rcv'];
							$total_sewing_qty += $qtyValue['sewing'];
							$total_iron_qty += $qtyValue['iron'];
							$total_finish_qty += $qtyValue['finish'];

							$getdate = date('D', $time = strtotime($date));
							if ($getdate != 'Fri') {
								if (!in_array($date, $date_fri_arr)) {
									$date_fri_arr[] = $date;
								}
								$j++;
								$totl_knit_qty += $qtyValue['knitting'];
								$totl_dyei_qty += $qtyValue['dyeing'];
								$totl_cutt_qty += $qtyValue['cutting'];
								$totl_pnt_issue_qty += $qtyValue['print_issue'];
								$totl_prnt_rcv_qty += $qtyValue['print_rcv'];

								$totl_emb_issue_qty += $qtyValue['emb_issue'];
								$totl_emb_rcv_qty += $qtyValue['emb_rcv'];

								$totl_special_issue_qty += $qtyValue['special_issue'];
								$totl_special_rcv_qty += $qtyValue['special_rcv'];

								$totl_dyeing_issue_qty += $qtyValue['dyeing_issue'];
								$totl_dyeing_rcv_qty += $qtyValue['dyeing_rcv'];

								$totl_wash_issue_qty += $qtyValue['wash_issue'];
								$totl_wash_rcv_qty += $qtyValue['wash_rcv'];
								$totl_sewi_qty += $qtyValue['sewing'];
								$totl_iro_qty += $qtyValue['iron'];
								$totl_fini_qty += $qtyValue['finish'];
							}
						}
						?>
	                    <tr>
	                        <td colspan="12"></td>
	                    </tr>
	                    <tr>
	                        <td align="left" colspan="2"><b
	                                    style="font-size: 11px !important; padding-left: 5px;">SUB TOTAL</b></td>
	                        <td align="right">
	                            <b><?php echo number_format($sub_total_knitting_qty, 2); ?></b></td>
	                        <td align="right"><b><?php echo number_format($sub_total_dyeing_qty, 2); ?></b>
	                        </td>
	                        <td align="right"><b><?php echo number_format($sub_total_cutting_qty, 0); ?></b>
	                        </td>
	                        <td align="right">
	                            <b><?php echo number_format($sub_total_print_issue_qty,0); ?></b></td>
	                        <td align="right">
	                            <b><?php echo number_format($sub_total_print_rcv_qty, 0); ?></b></td>

	                             <td align="right">
	                            <b><?php echo number_format($sub_total_emb_issue_qty,0); ?></b></td>
	                        <td align="right">
	                            <b><?php echo number_format($sub_total_emb_rcv_qty, 0); ?></b></td>

	                             <td align="right">
	                            <b><?php echo number_format($sub_total_special_issue_qty,0); ?></b></td>
	                        <td align="right">
	                            <b><?php echo number_format($sub_total_special_rcv_qty, 0); ?></b></td>

	                             <td align="right">
	                            <b><?php echo number_format($sub_total_dyeing_issue_qty,0); ?></b></td>
	                        <td align="right">
	                            <b><?php echo number_format($sub_total_dyeing_rcv_qty, 0); ?></b></td>


	                        <td align="right">
	                            <b><?php echo number_format($sub_total_wash_issue_qty, 0); ?></b></td>
	                        <td align="right">
	                            <b><?php echo number_format($sub_total_wash_rcv_qty, 0); ?></b></td>
	                        <td align="right"><b><?php echo number_format($sub_total_sewing_qty, 0); ?></b>
	                        </td>
	                        <td align="right"><b><?php echo number_format($sub_total_iron_qty, 0); ?></b>
	                        </td>
	                        <td align="right"><b><?php echo number_format($sub_total_finish_qty, 0); ?></b>
	                        </td>
	                        <td></td>
	                    </tr>
						<?php
					}
				}
				$date_count = count($date_arr);
				$date_count2 = count($date_fri_arr);
				?>
	            <tr>
	                <td colspan="12" style="padding: 1px;"></td>
	            </tr>
	            <tr>
	                <td align="left" colspan="2"><b>GRAND TOTAL</b></td>
	                <td align="right"><b><?php echo number_format($total_knitting_qty, 2); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_dyeing_qty, 2); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_cutting_qty, 0); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_print_issue_qty, 0); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_print_rcv_qty, 0); ?></b></td>

	                <td align="right"><b><?php echo number_format($total_emb_issue_qty, 0); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_emb_rcv_qty, 0); ?></b></td>

	                <td align="right"><b><?php echo number_format($total_special_issue_qty, 0); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_special_rcv_qty, 0); ?></b></td>

	                <td align="right"><b><?php echo number_format($total_dyeing_issue_qty, 0); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_dyeing_rcv_qty, 0); ?></b></td>

	                <td align="right"><b><?php echo number_format($total_wash_issue_qty, 0); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_wash_rcv_qty, 0); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_sewing_qty, 0); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_iron_qty, 0); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_finish_qty, 0); ?></b></td>
	                <td></td>
	            </tr>
	            <tr>
	                <td align="left" colspan="2"><b>AVARAGE</b></td>
	                <td width="100" align="right">
	                    <b><?php echo number_format($total_knitting_qty / $date_count, 2); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_dyeing_qty / $date_count, 2); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_cutting_qty / $date_count, 0); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_print_issue_qty / $date_count, 0); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_print_rcv_qty / $date_count, 0); ?></b>
	                </td>

	                <td align="right">
	                    <b><?php echo number_format($total_emb_issue_qty / $date_count, 0); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_emb_rcv_qty / $date_count, 0); ?></b>
	                </td>

	                <td align="right">
	                    <b><?php echo number_format($total_special_issue_qty / $date_count, 0); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_special_rcv_qty / $date_count, 0); ?></b>
	                </td>

	                <td align="right">
	                    <b><?php echo number_format($total_dyeing_issue_qty / $date_count, 0); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_dyeing_rcv_qty / $date_count, 0); ?></b>
	                </td>


	                <td align="right">
	                    <b><?php echo number_format($total_wash_issue_qty / $date_count, 0); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_wash_rcv_qty / $date_count, 0); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_sewing_qty / $date_count, 0); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_iron_qty / $date_count, 0); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_finish_qty / $date_count, 0); ?></b>
	                </td>
	                <td></td>
	            </tr>
	            <tr>
	                <td align="left" colspan="2"><b>TOTAL EXCLUDE FRIDAY</b></td>
	                <td align="right"><b><?php echo number_format($totl_knit_qty, 2); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_dyei_qty, 2); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_cutt_qty, 0); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_pnt_issue_qty, 0); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_prnt_rcv_qty, 0); ?></b></td>

	                <td align="right"><b><?php echo number_format($totl_emb_issue_qty, 0); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_emb_rcv_qty, 0); ?></b></td>

	                <td align="right"><b><?php echo number_format($totl_special_issue_qty, 0); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_special_rcv_qty, 0); ?></b></td>

	                <td align="right"><b><?php echo number_format($totl_dyeing_issue_qty, 0); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_dyeing_rcv_qty, 0); ?></b></td>


	                <td align="right"><b><?php echo number_format($totl_wash_issue_qty, 0); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_wash_rcv_qty, 0); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_sewi_qty, 0); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_iro_qty, 0); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_fini_qty, 0); ?></b></td>
	                <td>&nbsp;</td>
	            </tr>
	            <tr>
	                <td align="left" colspan="2"><b>AVG. EXCL. FRIDAY</b></td>
	                <td align="right"><b><?php echo number_format($totl_knit_qty / $date_count2, 2); ?></b>
	                </td>
	                <td align="right"><b><?php echo number_format($totl_dyei_qty / $date_count2, 2); ?></b>
	                </td>
	                <td align="right"><b><?php echo number_format($totl_cutt_qty / $date_count2, 0); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($totl_pnt_issue_qty / $date_count2, 0); ?></b></td>
	                <td align="right">
	                    <b><?php echo number_format($totl_prnt_rcv_qty / $date_count2, 0); ?></b></td>

	                    <td align="right">
	                    <b><?php echo number_format($totl_emb_issue_qty / $date_count2, 0); ?></b></td>
	                <td align="right">
	                    <b><?php echo number_format($totl_emb_rcv_qty / $date_count2, 0); ?></b></td>

	                    <td align="right">
	                    <b><?php echo number_format($totl_special_issue_qty / $date_count2, 0); ?></b></td>
	                <td align="right">
	                    <b><?php echo number_format($totl_special_rcv_qty / $date_count2, 0); ?></b></td>

	                    <td align="right">
	                    <b><?php echo number_format($totl_dyeing_issue_qty / $date_count2, 0); ?></b></td>
	                <td align="right">
	                    <b><?php echo number_format($totl_dyeing_rcv_qty / $date_count2, 0); ?></b></td>


	                <td align="right">
	                    <b><?php echo number_format($totl_wash_issue_qty / $date_count2, 0); ?></b></td>
	                <td align="right">
	                    <b><?php echo number_format($totl_wash_rcv_qty / $date_count2, 0); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_sewi_qty / $date_count2, 0); ?></b>
	                </td>
	                <td align="right"><b><?php echo number_format($totl_iro_qty / $date_count2, 0); ?></b>
	                </td>
	                <td align="right"><b><?php echo number_format($totl_fini_qty / $date_count2, 0); ?></b>
	                </td>
	                <td>&nbsp;</td>
	            </tr>
	          </table>

	    </fieldset><br/>


	    <!-- ======compuny,location wise summury========== -->
	    <fieldset style="width: 1320px; margin:0px auto;">
	        <table cellspacing="0" cellpadding="0" id="sammary_tbl" border="1" rules="all" class="rpt_table" style="width: 100%">
	            <thead>
	            <tr>
	                <th colspan="20" style="text-align: center;">FACTORY WISE PRODUCTION SUMMARY</th>
	            </tr>
	            <tr>
						<th>SL</th>
						<th>Company</th>
						<th>Location/Factory</th>
						<th>KNITTING</th>
						<th>DYEING</th>
						<th>CUTTING</th>
						<th>PRINT SEND</th>
						<th>PRINT RCV</th>
						<th>EMB SEND</th>
						<th>EMB RCV</th>
						<th>Special Work SEND</th>
						<th>Special Work RCV</th>
						<th>GMT Dyeing SEND</th>
						<th>GMT Dyeing RCV</th>
						<th>WASH SEND</th>
						<th>WASH RCV</th>
						<th>SEWING</th>
						<th>IRON</th>
						<th>FINISH</th>
						<th>REMARKS</th>
	            </tr>
	            </thead>

				<?

				$k = 1;
				foreach ($rev_qty2 as $company_id => $company_data) {
					foreach ($company_data as $location_id => $location_data) {
						if ($k % 2 == 1) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						?>
	                    <tr bgcolor="<? echo $bgcolor; ?>">
	                        <td><? echo $k; ?></td>
	                        <td><? echo $company_arr[$company_id]; ?></td>
	                        <td><? echo $location_arr[$location_id]; ?></td>
	                        <td align="right"><a href="##" onClick="openmypage_popup(<? echo $company_id; ?>,'<? echo $location_id; ?>','1','production_popup','1','knitting popup','knitting');" ><? echo number_format($location_data['knitting'], 2); ?></a></td>
	                        <td align="right"><a href="##" onClick="openmypage_popup(<? echo $company_id; ?>,'<? echo $location_id; ?>','1','production_popup','1','dyeing popup','dyeing');" ><? echo number_format($location_data['dyeing'], 2); ?></a></td>
	                        <td align="right"><a href="##" onClick="openmypage_popup(<? echo $company_id; ?>,'<? echo $location_id; ?>','1','production_popup','0','cutting popup','');" > <? echo number_format($location_data['cutting'], 0); ?> </a></td>
	                        <td align="right"><? echo number_format($location_data['print_issue'], 0); ?></td>
	                        <td align="right"><? echo number_format($location_data['print_rcv'], 0); ?></td>
	                        <td align="right"><? echo number_format($location_data['emb_issue'], 0); ?></td>
	                        <td align="right"><? echo number_format($location_data['emb_rcv'], 0); ?></td>
	                        <td align="right"><? echo number_format($location_data['special_issue'], 0); ?></td>
	                        <td align="right"><? echo number_format($location_data['special_rcv'], 0); ?></td>
	                        <td align="right"><? echo number_format($location_data['dyeing_issue'], 0); ?></td>
	                        <td align="right"><? echo number_format($location_data['dyeing_rcv'], 0); ?></td>
	                        <td align="right"><? echo number_format($location_data['wash_issue'], 0); ?></td>
	                        <td align="right"><? echo number_format($location_data['wash_rcv'], 0); ?></td>
	                        <td align="right"><a href="##" onClick="openmypage_popup(<? echo $company_id; ?>,'<? echo $location_id; ?>','5','production_popup','0','sewing popup','');" ><? echo number_format($location_data['sewing'], 0); ?></a></td>
	                        <td align="right"><a href="##" onClick="openmypage_popup(<? echo $company_id; ?>,'<? echo $location_id; ?>','7','production_popup','0','iron popup','');" ><? echo number_format($location_data['iron'], 0); ?></a></td>
	                        <td align="right"><a href="##" onClick="openmypage_popup(<? echo $company_id; ?>,'<? echo $location_id; ?>','8','production_popup','0','finish popup','');" ><? echo number_format($location_data['finish'],0); ?> </a></td>
	                        <td></td>
	                    </tr>
						<?
						$k++;
						$total_knitting += $location_data['knitting'];
						$total_dyeing += $location_data['dyeing'];
						$total_cutting += $location_data['cutting'];
						$total_print_issue += $location_data['print_issue'];
						$total_print_rcv += $location_data['print_rcv'];
						$total_emb_issue += $location_data['emb_issue'];
						$total_emb_rcv += $location_data['emb_rcv'];
						$total_special_issue += $location_data['special_issue'];
						$total_special_rcv += $location_data['special_rcv'];
						$total_dyeing_issue += $location_data['dyeing_issue'];
						$total_dyeing_rcv += $location_data['dyeing_rcv'];
						$total_wash_issue += $location_data['wash_issue'];
						$total_wash_rcv += $location_data['wash_rcv'];
						$total_sewing += $location_data['sewing'];
						$total_iron += $location_data['iron'];
						$total_finish += $location_data['finish'];
					}
				}
				?>
	            <tr bgcolor="<? echo $bgcolor; ?>">
	                <td align="left" colspan="3"><b>TOTAL</b></td>
	                <td align="right"><b><? echo number_format($total_knitting, 2); ?></b></td>
	                <td align="right"><b><? echo number_format($total_dyeing, 2); ?></b></td>
	                <td align="right"><b><? echo number_format($total_cutting, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_print_issue, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_print_rcv, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_emb_issue, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_emb_rcv, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_special_issue, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_special_rcv, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_dyeing_issue, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_dyeing_rcv, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_wash_issue, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_wash_rcv, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_sewing, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_iron, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_finish, 0); ?></b></td>
	                <td><? //echo
						?></td>
	            </tr>
	        </table>

	    </fieldset>
		<?
		foreach (glob("$user_id*.xls") as $filename) {
			if (@filemtime($filename) < (time() - $seconds_old))
				@unlink($filename);
		}
		//---------end------------//
		$name = time();
		$filename = $user_id . "_" . $name . ".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc, ob_get_contents());
		//$filename=$user_id."_".$name.".xls";
		echo "$total_data####$filename";
	}
	else if($type==2) // Show 2
	{
		$knit_sqls = sql_select("select a.receive_date, sum(b.grey_receive_qnty) as recv_qty
						from inv_receive_master a, pro_grey_prod_entry_dtls b
						where a.id=b.mst_id and a.status_active=1 $date_cond_knit $company_knit_cond and a.entry_form=2 and a.is_deleted=0 $location_id_cond
						group by  a.receive_date order by a.receive_date");
		$knit_sqls_factory_wise = sql_select("select a.receive_date,a.knitting_company,a.knitting_location_id as location_id, a.buyer_id, sum(b.grey_receive_qnty) as recv_qty
						from inv_receive_master a, pro_grey_prod_entry_dtls b
						where a.id=b.mst_id and a.status_active=1 $date_cond_knit $company_knit_cond and a.entry_form=2 and a.is_deleted=0 $location_id_cond
						group by  a.receive_date,a.knitting_company,a.knitting_location_id,a.buyer_id order by a.receive_date");

		$rev_qty2 = array();
		foreach ($knit_sqls_factory_wise as $value)
		{
			$rev_qty2[$value[csf('knitting_company')]][$value[csf('location_id')]]['knitting'] += $value[csf('recv_qty')];
		}

		$rev_qty = array();
		foreach ($knit_sqls as $value)
		{
			$rev_qty[$value[csf('receive_date')]]['knitting'] += $value[csf('recv_qty')];
	 	}
		// dyeing summary

		$dyeing_sqls_factory_wise = sql_select("select a.process_end_date as production_date, a.service_company, a.floor_id, sum(b.production_qty) as prod_qty from pro_fab_subprocess a, pro_fab_subprocess_dtls b where a.id=b.mst_id and a.entry_form=35 and b.load_unload_id=2 and a.result=1 $date_cond2 $company_dyeing_cond group by a.process_end_date, a.service_company,a.floor_id order by a.process_end_date");
	 	foreach ($dyeing_sqls_factory_wise as $value)
	 	{
	 		$rev_qty2[$value[csf('service_company')]][$location_id_arr[$value[csf('floor_id')]]]['dyeing'] += $value[csf('prod_qty')];
		}

		$dyeing_sqls = sql_select("select a.process_end_date as production_date, sum(b.production_qty) as prod_qty from pro_fab_subprocess a, pro_fab_subprocess_dtls b where a.id=b.mst_id and a.entry_form=35 and b.load_unload_id=2 and a.result=1 $date_cond2 $company_dyeing_cond group by a.process_end_date order by a.process_end_date");
		//echo "select a.production_date, sum(b.production_qty) as prod_qty from pro_fab_subprocess a, pro_fab_subprocess_dtls b where a.id=b.mst_id and a.entry_form=35 and b.load_unload_id=2 and a.result=1 $date_cond $company_dyeing_cond group by a.production_date order by a.production_date";
	 	foreach ($dyeing_sqls as $value)
	 	 {
			$rev_qty[$value[csf('production_date')]]['dyeing'] += $value[csf('prod_qty')];
	     }



	 	$sqls = sql_select("SELECT a.production_date,a.production_type, a.embel_name, sum(b.production_qnty) as prod_quantity
						from pro_garments_production_mst a ,pro_garments_production_dtls b
						where a.id=b.mst_id and a.production_type=b.production_type and b.status_active=1 and b.is_deleted=0 and  a.status_active=1 $date_cond $company_cond and a.is_deleted=0 $location_cond
						group by a.production_date,a.production_type, a.embel_name order by a.production_date");

	 	$sqls_factory_wise = sql_select("SELECT a.production_date,a.serving_company,a.location,a.production_type, a.embel_name, sum(b.production_qnty) as prod_quantity
						from pro_garments_production_mst a  ,pro_garments_production_dtls b
						where a.id=b.mst_id and a.production_type=b.production_type and b.status_active=1 and b.is_deleted=0 and a.status_active=1 $date_cond $company_cond and a.is_deleted=0 $location_cond
						group by a.production_date,a.serving_company,a.location,a.production_type, a.embel_name order by a.production_date");


		$sqls_emb = sql_select("select a.production_date,a.production_type, a.embel_name, sum(a.production_quantity) as prod_quantity
						from pro_garments_production_mst a
						where status_active=1 $date_cond $company_cond_emb and is_deleted=0 $location_cond_emb
						group by a.production_date,a.production_type, a.embel_name order by a.production_date");

		$sqls_emb_factory_wise = sql_select("select a.production_date,a.sending_company as serving_company,a.sending_location as location,a.production_type, a.embel_name, sum(a.production_quantity) as prod_quantity
						from pro_garments_production_mst a
						where status_active=1 $date_cond $company_cond_emb and is_deleted=0 $location_cond_emb
						group by a.production_date,a.sending_company,a.sending_location,a.production_type, a.embel_name order by a.production_date");
	 	foreach($sqls_emb_factory_wise as $value)
		{

			if ($value[csf('production_type')] == 2)
			 {
				if ($value[csf('embel_name')] == 1)
				 {
	 				$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['print_issue'] += $value[csf('prod_quantity')];
				 }

				 else if ($value[csf('embel_name')] == 2)
				  {
	 				$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['emb_issue'] += $value[csf('prod_quantity')];
				  }

				else if ($value[csf('embel_name')] == 4)
				 {
	 				$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['special_issue'] += $value[csf('prod_quantity')];
				 }

				else if ($value[csf('embel_name')] == 5)
				 {
	 				$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['dyeing_issue'] += $value[csf('prod_quantity')];
				 }

	 		}


			else if ($value[csf('production_type')] == 3)
			{
				if ($value[csf('embel_name')] == 1)
				{
	 				$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['print_rcv'] += $value[csf('prod_quantity')];
				}

				else if ($value[csf('embel_name')] == 2)
				{
	 				$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['emb_rcv'] += $value[csf('prod_quantity')];
				}

				else if ($value[csf('embel_name')] == 4)
				{
	 				$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['special_rcv'] += $value[csf('prod_quantity')];
				}

				else if ($value[csf('embel_name')] == 5)
				{
	 				$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['dyeing_rcv'] += $value[csf('prod_quantity')];
				}
			}


		}

	 	foreach($sqls_emb as $value)
		{

			if ($value[csf('production_type')] == 2)
			 {
				if ($value[csf('embel_name')] == 1)
				 {
					$rev_qty[$value[csf('production_date')]]['print_issue'] += $value[csf('prod_quantity')];
				 }

				 else if ($value[csf('embel_name')] == 2) {
					$rev_qty[$value[csf('production_date')]]['emb_issue'] += $value[csf('prod_quantity')];
				}

				else if ($value[csf('embel_name')] == 4) {
					$rev_qty[$value[csf('production_date')]]['special_issue'] += $value[csf('prod_quantity')];
				}

				else if ($value[csf('embel_name')] == 5) {
					$rev_qty[$value[csf('production_date')]]['dyeing_issue'] += $value[csf('prod_quantity')];
				}



			}



			else if ($value[csf('production_type')] == 3) {
				if ($value[csf('embel_name')] == 1) {
					$rev_qty[$value[csf('production_date')]]['print_rcv'] += $value[csf('prod_quantity')];
				}

				else if ($value[csf('embel_name')] == 2) {
					$rev_qty[$value[csf('production_date')]]['emb_rcv'] += $value[csf('prod_quantity')];
				}

				else if ($value[csf('embel_name')] == 4) {
					$rev_qty[$value[csf('production_date')]]['special_rcv'] += $value[csf('prod_quantity')];
				}

				else if ($value[csf('embel_name')] == 5) {
					$rev_qty[$value[csf('production_date')]]['dyeing_rcv'] += $value[csf('prod_quantity')];
				}
			}



		}


		foreach ($sqls as $value)
		 {
			if ($value[csf('production_type')] == 1) {
				$rev_qty[$value[csf('production_date')]]['cutting'] += $value[csf('prod_quantity')];
			} else if ($value[csf('production_type')] == 5) {
				$rev_qty[$value[csf('production_date')]]['sewing'] += $value[csf('prod_quantity')];
			} else if ($value[csf('production_type')] == 7) {
				$rev_qty[$value[csf('production_date')]]['iron'] += $value[csf('prod_quantity')];
			} else if ($value[csf('production_type')] == 8) {
				$rev_qty[$value[csf('production_date')]]['finish'] += $value[csf('prod_quantity')];
			} else if ($value[csf('production_type')] == 2) {
				 if ($value[csf('embel_name')] == 3) {
					$rev_qty[$value[csf('production_date')]]['wash_issue'] += $value[csf('prod_quantity')];
				}



			}
			else if ($value[csf('production_type')] == 3)
			 {
				 if ($value[csf('embel_name')] == 3) {
					$rev_qty[$value[csf('production_date')]]['wash_rcv'] += $value[csf('prod_quantity')];
					$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['wash_rcv'] += $value[csf('prod_quantity')];
				}

			}
		}

		foreach ($sqls_factory_wise as $value) {
			if ($value[csf('production_type')] == 1)
			{
	 			$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['cutting'] += $value[csf('prod_quantity')];
			}
			else if ($value[csf('production_type')] == 5)
			 {
	 			$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['sewing'] += $value[csf('prod_quantity')];
			 }
			else if ($value[csf('production_type')] == 7)
			 {
	 			$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['iron'] += $value[csf('prod_quantity')];
			 }
			else if ($value[csf('production_type')] == 8)
			{
	 			$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['finish'] += $value[csf('prod_quantity')];
			}
			else if ($value[csf('production_type')] == 2)
			{
				 if ($value[csf('embel_name')] == 3)
				  {
	 				$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['wash_issue'] += $value[csf('prod_quantity')];
				  }
	 		}
			else if ($value[csf('production_type')] == 3)
			 {
				 if ($value[csf('embel_name')] == 3)
				  {
	 				$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['wash_rcv'] += $value[csf('prod_quantity')];
				  }

			}
		}
		ob_start();
		?>
	    <style>
	        #sammary_tbl th, #sammary_tbl td {
	            padding: 0 7px;
	        }
	        fieldset{border:0;}
	    </style>
	    <fieldset style="width: 1320px; margin:20px auto;">
	        <table cellpadding="0" cellspacing="0" style="text-align: center; width: 100%">
	            <tr class="form_caption" style="border:none;">
	                <td align="center" width="100%" colspan="19"><strong style="font-size:25px"><?
							$com_name = return_field_value("company_name", "lib_company", "id=" . $cbo_company_name, "company_name");
							echo $com_name; ?></strong></td>
	            </tr>

	            <tr class="form_caption" style="border:none;">
	                <td align="center" width="100%" colspan="19">
	                    <strong style="font-size:16px">Production Summary:
							<?
							if ($start_date != '') echo change_date_format($start_date) . ' To ' . change_date_format($end_date); else echo '';
							?>
	                    </strong>
	                </td>
	            </tr>

	            <tr>
	                <th colspan="19" style="text-align: right;">Date of Print:&nbsp&nbsp<? echo date('d-m-Y'); ?></th>
	            </tr>

	            <tr>
	                <th colspan="19" style="text-align: right;">Time of Print:&nbsp&nbsp<? echo date('h:i A'); ?></th>
	            </tr>
	        </table>
	        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
	            <thead>
	            <tr>
	                <th width="30">SL</th>
	                <th width="80">DATE</th>
	                <th width="80">KNITTING</th>
	                <th width="80">DYEING</th>
	                <th width="80">CUTTING</th>
	                <th width="80">PRINT SEND</th>
	                <th width="80">PRINT RCV</th>
	                <th width="80">Emb SEND</th>
	                <th width="80">Emb RCV</th>
	                <th width="80">Special Work Send</th>
	                <th width="80">Special Work RCV</th>
	                <th width="80">Gmt Dyeing SEND</th>
	                <th width="80">Gmt Dyeing RCV</th>
	                <th width="80">WASH SEND</th>
	                <th width="80">WASH RCV</th>
	                <th width="80">SEWING</th>
	                <th width="80">IRON</th>
	                <th width="80">FINISH</th>
	                <th width="">REMARKS</th>
	            </tr>
	            </thead>
				<?php
				$date_arr = array();
				$date_fri_arr = array();
			 	$k=1;
	 			$total_knitting_qty = 0;
				$total_dyeing_qty = 0;
				$total_cutting_qty = 0;
				$total_print_issue_qty = 0;
				$total_print_rcv_qty = 0;
				$total_emb_issue_qty = 0;
				$total_emb_rcv_qty = 0;
				$total_special_issue_qty = 0;
				$total_special_rcv_qty = 0;
				$total_dyeing_issue_qty = 0;
				$total_dyeing_rcv_qty = 0;
				$total_wash_issue_qty = 0;
				$total_wash_rcv_qty = 0;
				$total_sewing_qty = 0;
				$total_iron_qty = 0;
				$total_finish_qty = 0;
				ksort($rev_qty);
	 			foreach ($rev_qty as $qtyValue => $result_row)
	 			{

				if (!in_array($qtyValue, $date_arr))
				{
					$date_arr[] = $qtyValue;
				}

				$i = 1;
				$total_knitting_qty  =$total_knitting_qty + $result_row['knitting'];
				$total_dyeing_qty += $result_row['dyeing'];
				$total_cutting_qty += $result_row['cutting'];
				$total_print_issue_qty += $result_row['print_issue'];
				$total_print_rcv_qty += $result_row['print_rcv'];
				$total_emb_issue_qty += $result_row['emb_issue'];
				$total_emb_rcv_qty += $result_row['emb_rcv'];
				$total_special_issue_qty += $result_row['special_issue'];
				$total_special_rcv_qty += $result_row['special_rcv'];
				$total_dyeing_issue_qty += $result_row['dyeing_issue'];
				$total_dyeing_rcv_qty += $result_row['dyeing_rcv'];
				$total_wash_issue_qty += $result_row['wash_issue'];
				$total_wash_rcv_qty += $result_row['wash_rcv'];
				$total_sewing_qty += $result_row['sewing'];
				$total_iron_qty += $result_row['iron'];
				$total_finish_qty += $result_row['finish'];
			    $getdate = date('D', $time = strtotime($qtyValue));
				if ($getdate != 'Fri')
				{
					if (!in_array($qtyValue, $date_fri_arr))
					{
						$date_fri_arr[] = $qtyValue;
					}

					//$j++;
					$totl_knit_qty += $result_row['knitting'];
					$totl_dyei_qty += $result_row['dyeing'];
					$totl_cutt_qty += $result_row['cutting'];
					$totl_pnt_issue_qty += $result_row['print_issue'];
					$totl_prnt_rcv_qty += $result_row['print_rcv'];

					$totl_emb_issue_qty += $result_row['emb_issue'];
					$totl_emb_rcv_qty += $result_row['emb_rcv'];

					$totl_special_issue_qty += $result_row['special_issue'];
					$totl_special_rcv_qty += $result_row['special_rcv'];

					$totl_dyeing_issue_qty += $result_row['dyeing_issue'];
					$totl_dyeing_rcv_qty += $result_row['dyeing_rcv'];

					$totl_wash_issue_qty += $result_row['wash_issue'];
					$totl_wash_rcv_qty += $result_row['wash_rcv'];
					$totl_sewi_qty += $result_row['sewing'];
					$totl_iro_qty += $result_row['iron'];
					$totl_fini_qty += $result_row['finish'];
				}



				if ($i % 2 == 1) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

				  ?>
	            <tr bgcolor="<? $timestamp = strtotime($qtyValue);$day_name= date("l", $timestamp );
	            if($day_name=="Friday"){echo "crimson";} else{echo $bgcolor; }?>" style="<?if($day_name=="Friday"){echo 'color:white;';}?>">
	                <td align="center"><?  echo $k; ?></td>

	                <td align="center"><? echo change_date_format($qtyValue); ?></td>
	                <td align="right"><? echo number_format($result_row['knitting'], 2); ?></td>
	                <td align="right"><? echo number_format($result_row['dyeing'], 2); ?></td>
	                <td align="right"><? echo number_format($result_row['cutting'], 0); ?></td>
	                <td align="right"><? echo number_format($result_row['print_issue'], 0); ?></td>
	                <td align="right"><? echo number_format($result_row['print_rcv'], 0); ?></td>
	                <td align="right"><? echo number_format($result_row['emb_issue'], 0); ?></td>
	                <td align="right"><? echo number_format($result_row['emb_rcv'], 0); ?></td>
	                <td align="right"><? echo number_format($result_row['special_issue'],0); ?></td>
	                <td align="right"><? echo number_format($result_row['special_rcv'], 0); ?></td>
	                <td align="right"><? echo number_format($result_row['dyeing_issue'], 0); ?></td>
	                <td align="right"><? echo number_format($result_row['dyeing_rcv'], 0); ?></td>
	                <td align="right"><? echo number_format($result_row['wash_issue'], 0); ?></td>
	                <td align="right"><? echo number_format($result_row['wash_rcv'], 0); ?></td>
	                <td align="right"><? echo number_format($result_row['sewing'], 0); ?></td>
	                <td align="right"><? echo number_format($result_row['iron'], 0); ?></td>
	                <td align="right"><? echo number_format($result_row['finish'], 0); ?></td>
	                <td></td>
	            </tr>
				<?
					$k++;
					$i++;
					$date_count = count($date_arr);
					$date_count2 = count($date_fri_arr);
				}
			?>
	            <tr>
	                <td colspan="12" style="padding: 1px;"></td>
	            </tr>
	            <tr>
	                <td align="left" colspan="2"><b>Total</b></td>
	                <td align="right"><b><?php echo number_format($total_knitting_qty, 2); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_dyeing_qty, 2); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_cutting_qty, 0); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_print_issue_qty, 0); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_print_rcv_qty, 0); ?></b></td>

	                <td align="right"><b><?php echo number_format($total_emb_issue_qty, 0); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_emb_rcv_qty, 0); ?></b></td>

	                <td align="right"><b><?php echo number_format($total_special_issue_qty, 0); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_special_rcv_qty, 0); ?></b></td>

	                <td align="right"><b><?php echo number_format($total_dyeing_issue_qty, 0); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_dyeing_rcv_qty, 0); ?></b></td>

	                <td align="right"><b><?php echo number_format($total_wash_issue_qty, 0); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_wash_rcv_qty, 0); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_sewing_qty, 0); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_iron_qty, 0); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_finish_qty, 0); ?></b></td>
	                <td></td>
	            </tr>

	            <tr>
	                <td align="left" colspan="2"><b>AVARAGE</b></td>
	                <td width="100" align="right">
	                    <b><?php echo number_format($total_knitting_qty / $date_count, 2); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_dyeing_qty / $date_count, 2); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_cutting_qty / $date_count, 0); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_print_issue_qty / $date_count, 0); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_print_rcv_qty / $date_count, 0); ?></b>
	                </td>

	                <td align="right">
	                    <b><?php echo number_format($total_emb_issue_qty / $date_count, 0); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_emb_rcv_qty / $date_count, 0); ?></b>
	                </td>

	                <td align="right">
	                    <b><?php echo number_format($total_special_issue_qty / $date_count, 0); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_special_rcv_qty / $date_count, 0); ?></b>
	                </td>

	                <td align="right">
	                    <b><?php echo number_format($total_dyeing_issue_qty / $date_count, 0); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_dyeing_rcv_qty / $date_count, 0); ?></b>
	                </td>


	                <td align="right">
	                    <b><?php echo number_format($total_wash_issue_qty / $date_count, 0); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_wash_rcv_qty / $date_count, 0); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_sewing_qty / $date_count, 0); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_iron_qty / $date_count, 0); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_finish_qty / $date_count, 0); ?></b>
	                </td>
	                <td></td>
	            </tr>
	            <tr>
	                <td align="left" colspan="2"><b>TOTAL EXCLUDE FRIDAY</b></td>
	                <td align="right"><b><?php echo number_format($totl_knit_qty, 2); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_dyei_qty, 2); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_cutt_qty, 0); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_pnt_issue_qty, 0); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_prnt_rcv_qty, 0); ?></b></td>

	                <td align="right"><b><?php echo number_format($totl_emb_issue_qty, 0); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_emb_rcv_qty, 0); ?></b></td>

	                <td align="right"><b><?php echo number_format($totl_special_issue_qty, 0); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_special_rcv_qty, 0); ?></b></td>

	                <td align="right"><b><?php echo number_format($totl_dyeing_issue_qty, 0); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_dyeing_rcv_qty, 0); ?></b></td>


	                <td align="right"><b><?php echo number_format($totl_wash_issue_qty, 0); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_wash_rcv_qty, 0); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_sewi_qty, 0); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_iro_qty, 0); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_fini_qty, 0); ?></b></td>
	                <td>&nbsp;</td>
	            </tr>
	            <tr>
	                <td align="left" colspan="2"><b>AVG. EXCL. FRIDAY</b></td>
	                <td align="right"><b><?php echo number_format($totl_knit_qty / $date_count2, 2); ?></b>
	                </td>
	                <td align="right"><b><?php echo number_format($totl_dyei_qty / $date_count2, 2); ?></b>
	                </td>
	                <td align="right"><b><?php echo number_format($totl_cutt_qty / $date_count2, 0); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($totl_pnt_issue_qty / $date_count2, 0); ?></b></td>
	                <td align="right">
	                    <b><?php echo number_format($totl_prnt_rcv_qty / $date_count2, 0); ?></b></td>

	                    <td align="right">
	                    <b><?php echo number_format($totl_emb_issue_qty / $date_count2, 0); ?></b></td>
	                <td align="right">
	                    <b><?php echo number_format($totl_emb_rcv_qty / $date_count2, 0); ?></b></td>

	                    <td align="right">
	                    <b><?php echo number_format($totl_special_issue_qty / $date_count2, 0); ?></b></td>
	                <td align="right">
	                    <b><?php echo number_format($totl_special_rcv_qty / $date_count2, 0); ?></b></td>

	                    <td align="right">
	                    <b><?php echo number_format($totl_dyeing_issue_qty / $date_count2, 0); ?></b></td>
	                <td align="right">
	                    <b><?php echo number_format($totl_dyeing_rcv_qty / $date_count2, 0); ?></b></td>


	                <td align="right">
	                    <b><?php echo number_format($totl_wash_issue_qty / $date_count2, 0); ?></b></td>
	                <td align="right">
	                    <b><?php echo number_format($totl_wash_rcv_qty / $date_count2, 0); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_sewi_qty / $date_count2, 0); ?></b>
	                </td>
	                <td align="right"><b><?php echo number_format($totl_iro_qty / $date_count2, 0); ?></b>
	                </td>
	                <td align="right"><b><?php echo number_format($totl_fini_qty / $date_count2, 0); ?></b>
	                </td>
	                <td>&nbsp;</td>
	            </tr>


	        </table>

	    </fieldset><br/>


	    <!-- ======compuny,location wise summury========== -->
	    <fieldset style="width: 1320px; margin:0px auto;">
	        <table cellspacing="0" cellpadding="0" id="sammary_tbl" border="1" rules="all" class="rpt_table" style="width: 100%">
	            <thead>
	            <tr>
	                <th colspan="20" style="text-align: center;">FACTORY WISE PRODUCTION SUMMARY</th>
	            </tr>
	            <tr>
						<th>SL</th>
						<th>Company</th>
						<th>Location/Factory</th>
						<th>KNITTING</th>
						<th>DYEING</th>
						<th>CUTTING</th>
						<th>PRINT SEND</th>
						<th>PRINT RCV</th>
						<th>EMB SEND</th>
						<th>EMB RCV</th>
						<th>Special Work SEND</th>
						<th>Special Work RCV</th>
						<th>GMT Dyeing SEND</th>
						<th>GMT Dyeing RCV</th>
						<th>WASH SEND</th>
						<th>WASH RCV</th>
						<th>SEWING</th>
						<th>IRON</th>
						<th>FINISH</th>
						<th>REMARKS</th>
	            </tr>
	            </thead>

				<?

				$k = 1;
				/*echo "<pre>";
				print_r($rev_qty2);die;*/
				foreach ($rev_qty2 as $company_id => $company_data) {
					foreach ($company_data as $location_id => $location_data) {
						if ($k % 2 == 1) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

						?>
	                    <tr bgcolor="<? echo $bgcolor; ?>">
	                        <td><? echo $k; ?></td>
	                        <td><? echo $company_arr[$company_id]; ?></td>
	                        <td><? echo $location_arr[$location_id]; ?></td>
	                        <td align="right"><a href="##" onClick="openmypage_popup(<? echo $company_id; ?>,'<? echo $location_id; ?>','1','production_popup','1','knitting popup','knitting');" ><? echo number_format($location_data['knitting'], 2); ?></a></td>
	                        <td align="right"><a href="##" onClick="openmypage_popup(<? echo $company_id; ?>,'<? echo $location_id; ?>','1','production_popup','1','dyeing popup','dyeing');" ><? echo number_format($location_data['dyeing'], 2); ?></a></td>
	                        <td align="right"><a href="##" onClick="openmypage_popup(<? echo $company_id; ?>,'<? echo $location_id; ?>','1','production_popup','0','cutting popup','');" > <? echo number_format($location_data['cutting'], 0); ?></a></td>
	                        <td align="right"><? echo number_format($location_data['print_issue'], 0); ?></td>
	                        <td align="right"><? echo number_format($location_data['print_rcv'], 0); ?></td>
	                        <td align="right"><? echo number_format($location_data['emb_issue'], 0); ?></td>
	                        <td align="right"><? echo number_format($location_data['emb_rcv'], 0); ?></td>
	                        <td align="right"><? echo number_format($location_data['special_issue'], 0); ?></td>
	                        <td align="right"><? echo number_format($location_data['special_rcv'], 0); ?></td>
	                        <td align="right"><? echo number_format($location_data['dyeing_issue'], 0); ?></td>
	                        <td align="right"><? echo number_format($location_data['dyeing_rcv'], 0); ?></td>
	                        <td align="right"><? echo number_format($location_data['wash_issue'], 0); ?></td>
	                        <td align="right"><? echo number_format($location_data['wash_rcv'], 0); ?></td>
	                        <td align="right"><a href="##" onClick="openmypage_popup(<? echo $company_id; ?>,'<? echo $location_id; ?>','5','production_popup','0','sewing popup','');" ><? echo number_format($location_data['sewing'], 0); ?></a></td>
	                        <td align="right"><a href="##" onClick="openmypage_popup(<? echo $company_id; ?>,'<? echo $location_id; ?>','7','production_popup','0','iron popup','');" ><? echo number_format($location_data['iron'], 0); ?></a></td>
	                        <td align="right"><a href="##" onClick="openmypage_popup(<? echo $company_id; ?>,'<? echo $location_id; ?>','8','production_popup','0','finish popup','');" ><? echo number_format($location_data['finish'],0); ?></a></td>
	                        <td></td>
	                    </tr>
						<?
						$k++;
						$total_knitting += $location_data['knitting'];
						$total_dyeing += $location_data['dyeing'];
						$total_cutting += $location_data['cutting'];
						$total_print_issue += $location_data['print_issue'];
						$total_print_rcv += $location_data['print_rcv'];
						$total_emb_issue += $location_data['emb_issue'];
						$total_emb_rcv += $location_data['emb_rcv'];
						$total_special_issue += $location_data['special_issue'];
						$total_special_rcv += $location_data['special_rcv'];
						$total_dyeing_issue += $location_data['dyeing_issue'];
						$total_dyeing_rcv += $location_data['dyeing_rcv'];
						$total_wash_issue += $location_data['wash_issue'];
						$total_wash_rcv += $location_data['wash_rcv'];
						$total_sewing += $location_data['sewing'];
						$total_iron += $location_data['iron'];
						$total_finish += $location_data['finish'];

					}
				}
				?>
	            <tr bgcolor="<? echo $bgcolor; ?>">
	                <td align="left" colspan="3"><b>TOTAL</b></td>
	                <td align="right"><b><? echo number_format($total_knitting, 2); ?></b></td>
	                <td align="right"><b><? echo number_format($total_dyeing, 2); ?></b></td>
	                <td align="right"><b><? echo number_format($total_cutting, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_print_issue, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_print_rcv, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_emb_issue, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_emb_rcv, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_special_issue, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_special_rcv, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_dyeing_issue, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_dyeing_rcv, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_wash_issue, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_wash_rcv, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_sewing, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_iron, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_finish, 0); ?></b></td>
	                <td> </td>
	            </tr>
	        </table>

	    </fieldset>
		<?
		foreach (glob("$user_id*.xls") as $filename) {
			if (@filemtime($filename) < (time() - $seconds_old))
				@unlink($filename);
		}
		//---------end------------//
		$name = time();
		$filename = $user_id . "_" . $name . ".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc, ob_get_contents());
		//$filename=$user_id."_".$name.".xls";
		echo "$total_data####$filename";
	}

	else if($type==4) // Show 4
	{
		$knit_sqls = sql_select("select a.receive_date, sum(b.grey_receive_qnty) as recv_qty
		from inv_receive_master a, pro_grey_prod_entry_dtls b
		where a.id=b.mst_id and a.status_active=1 $date_cond_knit $company_knit_cond and a.entry_form=2 and a.is_deleted=0 $location_id_cond
		group by  a.receive_date order by a.receive_date");

		$knit_sqls_factory_wise = sql_select("select a.receive_date,a.knitting_company,a.knitting_location_id as location_id, a.buyer_id, sum(b.grey_receive_qnty) as recv_qty
		from inv_receive_master a, pro_grey_prod_entry_dtls b
		where a.id=b.mst_id and a.status_active=1 $date_cond_knit $company_knit_cond and a.entry_form=2 and a.is_deleted=0 $location_id_cond
		group by  a.receive_date,a.knitting_company,a.knitting_location_id,a.buyer_id order by a.receive_date ");

		$rev_qty2 = array();
		foreach ($knit_sqls_factory_wise as $value)
		{
			$rev_qty2[$value[csf('knitting_company')]][$value[csf('location_id')]]['knitting'] += $value[csf('recv_qty')];
		}

		$rev_qty = array();
		foreach ($knit_sqls as $value)
		{
			$dateitme=strtotime($value[csf('receive_date')]);
			$rev_qty[$dateitme]['knitting'] += $value[csf('recv_qty')];
	 	}
		// dyeing summary

		$dyeing_sqls_factory_wise = sql_select("select a.process_end_date as production_date, a.service_company, a.floor_id, sum(b.production_qty) as prod_qty from pro_fab_subprocess a, pro_fab_subprocess_dtls b where a.id=b.mst_id and a.entry_form=35 and b.load_unload_id=2 and a.result=1 $date_cond2 $company_dyeing_cond group by a.process_end_date, a.service_company,a.floor_id order by a.production_date ");
	 	foreach ($dyeing_sqls_factory_wise as $value)
	 	{
	 		$rev_qty2[$value[csf('service_company')]][$location_id_arr[$value[csf('floor_id')]]]['dyeing'] += $value[csf('prod_qty')];
		}

		// $dyeing_sqls = sql_select("select a.process_end_date as production_date, sum(b.production_qty) as prod_qty from pro_fab_subprocess a, pro_fab_subprocess_dtls b where a.id=b.mst_id and a.entry_form=35 and b.load_unload_id=2 and a.result=1 $date_cond2 $company_dyeing_cond group by a.process_end_date order by a.production_date");
		// //echo "select a.production_date, sum(b.production_qty) as prod_qty from pro_fab_subprocess a, pro_fab_subprocess_dtls b where a.id=b.mst_id and a.entry_form=35 and b.load_unload_id=2 and a.result=1 $date_cond $company_dyeing_cond group by a.production_date order by a.production_date";
	 	// foreach ($dyeing_sqls as $value)
	 	//  {
		// 	$rev_qty[$value[csf('production_date')]]['dyeing'] += $value[csf('prod_qty')];
	    //  }






		$sql_qty = "(SELECT f.process_end_date as process_end_date,sum(case when f.service_source=1 then a.batch_weight end) as batch_weight,
		SUM(case when f.service_source=1 and a.batch_against!=3 then b.batch_qnty end) AS production_qty_inhouse,
		SUM(case when f.service_source=3 then b.batch_qnty end) AS production_qty_outbound,	SUM(case WHEN a.batch_against=3 and a.booking_without_order=1 then b.batch_qnty end) AS prod_qty_sample_without_order,SUM(case WHEN a.batch_against=3 and a.booking_without_order=0 then b.batch_qnty end) AS prod_qty_sample_with_order,SUM(case WHEN b.is_sales=1 then b.batch_qnty end) AS fabric_sales_order_qty from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d, pro_fab_subprocess f, lib_machine_name g, pro_batch_create_mst a	where f.batch_id=a.id $workingCompany_name_cond2 $dates_com and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1	and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and f.result=1 group by f.process_end_date )
		union ( select f.process_end_date as process_end_date,sum(case when f.service_source=1 then a.batch_weight end) as batch_weight, SUM(case when f.service_source=1 and a.batch_against!=3 then b.batch_qnty end) AS production_qty_inhouse,SUM(case when f.service_source=3 then b.batch_qnty end) AS production_qty_outbound,SUM(case WHEN a.batch_against=3 and a.booking_without_order=1 then b.batch_qnty end) AS prod_qty_sample_without_order,	SUM(case WHEN a.batch_against=3 and a.booking_without_order=0 then b.batch_qnty end) AS prod_qty_sample_with_order,SUM(case WHEN b.is_sales=1 then b.batch_qnty end) AS fabric_sales_order_qty
		from pro_batch_create_dtls b, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g,wo_non_ord_samp_booking_mst h
		where h.booking_no=a.booking_no $workingCompany_name_cond2 and f.batch_id=a.id and f.batch_id=b.mst_id and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0	and f.status_active=1 and f.is_deleted=0  $dates_com and f.result=1 group by  f.process_end_date ) order by process_end_date ";


		//  echo  $sql_qty;

		$sql_result=sql_select($sql_qty);

		$production_qty_inhouse=0;
		$production_qty_outbound=0;
		$prod_qty_sample_without_order=0;
		$prod_qty_sample_with_order=0;
		$fabric_sales_order_qty=0;
		$batchIDs="";
		foreach($sql_result as $row)
		{
			$dateitme=strtotime($row[csf('process_end_date')]);
			$rev_qty[$dateitme]['dyeing'] +=$row[csf('production_qty_inhouse')]+
			$row[csf('production_qty_outbound')]+$row[csf('prod_qty_sample_without_order')]+$row[csf('prod_qty_sample_with_order')]+$row[csf('fabric_sales_order_qty')];
		}

		$sql_subcontact_qty=sql_select("SELECT  SUM(b.batch_qnty) AS production_qty_subcontact,f.process_end_date as production_date from pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g
		where a.batch_against in(1,2) and f.batch_id=a.id and f.batch_id=b.mst_id and a.entry_form=36 and g.id=f.machine_id and a.id=b.mst_id and f.entry_form=38 and f.load_unload_id=2
		and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0
		$dates_com GROUP BY f.process_end_date order by f.process_end_date ");//$companyCond



		$production_qty_subcontact=0;
		foreach($sql_subcontact_qty as $row)
		{
			// $production_qty_subcontact+=$row[csf('production_qty_subcontact')];
			$dateitme=strtotime($row[csf('production_date')]);
			$rev_qty[$dateitme]['dyeing']+=$row[csf('production_qty_subcontact')];

		}

		$sqls = sql_select("SELECT a.production_date,a.production_type, a.embel_name, sum(b.production_qnty) as prod_quantity
		from pro_garments_production_mst a ,pro_garments_production_dtls b
		where a.id=b.mst_id and a.production_type=b.production_type and b.status_active=1 and b.is_deleted=0 and  a.status_active=1 $date_cond $company_cond and a.is_deleted=0 $location_cond
		group by a.production_date,a.production_type, a.embel_name order by a.production_date");

		$sqls_factory_wise = sql_select("SELECT a.production_date,a.serving_company,a.location,a.production_type, a.embel_name, sum(b.production_qnty) as prod_quantity	from pro_garments_production_mst a  ,pro_garments_production_dtls b
		where a.id=b.mst_id and a.production_type=b.production_type and b.status_active=1 and b.is_deleted=0 and a.status_active=1 $date_cond $company_cond and a.is_deleted=0 $location_cond
		group by a.production_date,a.serving_company,a.location,a.production_type, a.embel_name order by a.production_date");


		$sqls_emb = sql_select("SELECT a.production_date,a.production_type, a.embel_name, sum(a.production_quantity) as prod_quantity
		from pro_garments_production_mst a
		where status_active=1 $date_cond $company_cond_emb and is_deleted=0 $location_cond_emb
		group by a.production_date,a.production_type, a.embel_name order by a.production_date");

		$sqls_emb_factory_wise = sql_select("SELECT a.production_date,a.sending_company as serving_company,a.sending_location as location,a.production_type, a.embel_name, sum(a.production_quantity) as prod_quantity
		from pro_garments_production_mst a
		where status_active=1 $date_cond $company_cond_emb and is_deleted=0 $location_cond_emb
		group by a.production_date,a.sending_company,a.sending_location,a.production_type, a.embel_name order by
		a.production_date");
	 	foreach($sqls_emb_factory_wise as $value)
		{

			if ($value[csf('production_type')] == 2)
			 {
				if ($value[csf('embel_name')] == 1)
				 {
	 				$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['print_issue'] += $value[csf('prod_quantity')];
				 }

				 else if ($value[csf('embel_name')] == 2)
				  {
	 				$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['emb_issue'] += $value[csf('prod_quantity')];
				  }

				else if ($value[csf('embel_name')] == 4)
				 {
	 				$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['special_issue'] += $value[csf('prod_quantity')];
				 }

				else if ($value[csf('embel_name')] == 5)
				 {
	 				$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['dyeing_issue'] += $value[csf('prod_quantity')];
				 }

	 		}


			else if ($value[csf('production_type')] == 3)
			{
				if ($value[csf('embel_name')] == 1)
				{
	 				$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['print_rcv'] += $value[csf('prod_quantity')];
				}

				else if ($value[csf('embel_name')] == 2)
				{
	 				$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['emb_rcv'] += $value[csf('prod_quantity')];
				}

				else if ($value[csf('embel_name')] == 4)
				{
	 				$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['special_rcv'] += $value[csf('prod_quantity')];
				}

				else if ($value[csf('embel_name')] == 5)
				{
	 				$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['dyeing_rcv'] += $value[csf('prod_quantity')];
				}
			}
		}

	 	foreach($sqls_emb as $value)
		{

			$dateitme=strtotime($value[csf('production_date')]);
			if ($value[csf('production_type')] == 2)
			 {
				if ($value[csf('embel_name')] == 1)
				 {
					$rev_qty[$dateitme]['print_issue'] += $value[csf('prod_quantity')];
				 }

				 else if ($value[csf('embel_name')] == 2) {
					$rev_qty[$dateitme]['emb_issue'] += $value[csf('prod_quantity')];
				}

				else if ($value[csf('embel_name')] == 4) {
					$rev_qty[$dateitme]['special_issue'] += $value[csf('prod_quantity')];
				}

				else if ($value[csf('embel_name')] == 5) {
					$rev_qty[$dateitme]['dyeing_issue'] += $value[csf('prod_quantity')];
				}



			}



			else if ($value[csf('production_type')] == 3) {
				$dateitme=strtotime($value[csf('production_date')]);
				if ($value[csf('embel_name')] == 1) {
					$rev_qty[$dateitme]['print_rcv'] += $value[csf('prod_quantity')];
				}

				else if ($value[csf('embel_name')] == 2) {
					$rev_qty[$dateitme]['emb_rcv'] += $value[csf('prod_quantity')];
				}

				else if ($value[csf('embel_name')] == 4) {
					$rev_qty[$dateitme]['special_rcv'] += $value[csf('prod_quantity')];
				}

				else if ($value[csf('embel_name')] == 5) {
					$rev_qty[$dateitme]['dyeing_rcv'] += $value[csf('prod_quantity')];
				}
			}
		}


		foreach ($sqls as $value)
		{
			$dateitme=strtotime($value[csf('production_date')]);
			if ($value[csf('production_type')] == 1) {
				$rev_qty[$dateitme]['cutting'] += $value[csf('prod_quantity')];
			} else if ($value[csf('production_type')] == 5) {
				$rev_qty[$dateitme]['sewing'] += $value[csf('prod_quantity')];
			} else if ($value[csf('production_type')] == 7) {
				$rev_qty[$dateitme]['iron'] += $value[csf('prod_quantity')];
			} else if ($value[csf('production_type')] == 8) {
				$rev_qty[$dateitme]['finish'] += $value[csf('prod_quantity')];
			} else if ($value[csf('production_type')] == 2) {
				 if ($value[csf('embel_name')] == 3) {
					$rev_qty[$dateitme]['wash_issue'] += $value[csf('prod_quantity')];
			}



			}
			else if ($value[csf('production_type')] == 3)
			 {
				$dateitme=strtotime($value[csf('production_date')]);
				 if ($value[csf('embel_name')] == 3) {
					$rev_qty[$dateitme]['wash_rcv'] += $value[csf('prod_quantity')];
					$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['wash_rcv'] += $value[csf('prod_quantity')];
				}

			}
		}

		foreach ($sqls_factory_wise as $value)
		{
			if ($value[csf('production_type')] == 1)
			{
	 			$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['cutting'] += $value[csf('prod_quantity')];
			}
			else if ($value[csf('production_type')] == 5)
			 {
	 			$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['sewing'] += $value[csf('prod_quantity')];
			 }
			else if ($value[csf('production_type')] == 7)
			 {
	 			$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['iron'] += $value[csf('prod_quantity')];
			 }
			else if ($value[csf('production_type')] == 8)
			{
	 			$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['finish'] += $value[csf('prod_quantity')];
			}
			else if ($value[csf('production_type')] == 2)
			{
				 if ($value[csf('embel_name')] == 3)
				  {
	 				$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['wash_issue'] += $value[csf('prod_quantity')];
				  }
	 		}
			else if ($value[csf('production_type')] == 3)
			 {
				 if ($value[csf('embel_name')] == 3)
				  {
	 				$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['wash_rcv'] += $value[csf('prod_quantity')];
				  }

			}
		}
		//======================Garments Delivery Entry========================

		$gmt_delivery_data=sql_select("SELECT b.id,b.garments_nature,b.po_break_down_id,b.item_number_id,b.ex_factory_date,b.ex_factory_qnty,b.delivery_mst_id ,a.delivery_date	from pro_ex_factory_delivery_mst a,pro_ex_factory_mst b where  a.id=b.delivery_mst_id  and b.status_active=1 and b.entry_form<>85 and b.is_deleted=0 $delivery_date_cond order by a.delivery_date");


		foreach($gmt_delivery_data as $row){
			$dateitme=strtotime($row[csf('delivery_date')]);
			$rev_qty[$dateitme]['gmts_delivery_qty'] += $row[csf('ex_factory_qnty')];
		}


		//=============================Embroidery Production===================

		$emb_prod_data=sql_select("SELECT b.mst_id, b.id, b.buyer_po_id, b.qcpass_qty, b.color_size_id, b.remarks,b.production_date,a.entry_form  FROM subcon_embel_production_mst a, subcon_embel_production_dtls b
		where  a.id=b.mst_id  and a.entry_form in (301,315,222) and a.status_active=1 and a.is_deleted=0 $printing_date_cond and b.status_active=1 and b.is_deleted=0 order by b.production_date");


		foreach($emb_prod_data as $row){
			$dateitme=strtotime($row[csf('production_date')]);
			if($row[csf('entry_form')]==315){
				$rev_qty[$dateitme]['emb_prod_qty'] += $row[csf('qcpass_qty')];
			}elseif($row[csf('entry_form')]==222){
				$rev_qty[$dateitme]['print_prod_qty'] += $row[csf('qcpass_qty')];
			}elseif($row[csf('entry_form')]==301){
				$rev_qty[$dateitme]['wash_prod_qty'] += $row[csf('qcpass_qty')];
			}
		}

		//==========================Knit Finish Fabric Issue======================================
		$knit_finish_fab_issue_data=sql_select("SELECT a.id, a.mst_id, a.trans_id, a.batch_id, a.prod_id,  a.issue_qnty,a.order_id,  a.body_part_id, a.gmt_item_id, b.cons_rate ,c.issue_date from inv_finish_fabric_issue_dtls a, inv_transaction b ,inv_issue_master c
		where a.trans_id= b.id  and a.mst_id=c.id and a.status_active=1 and c.company_id in($company_name) $knit_fin_fab_issue_date_cond and b.status_active=1 order by c.issue_date");

		foreach($knit_finish_fab_issue_data as $row){
			$dateitme=strtotime($row[csf('issue_date')]);
			$rev_qty[$dateitme]['knit_finish_fab_issue_qty'] += $row[csf('issue_qnty')];
		}

		//========================AOP Production===================================

		$knit_finish_fab_issue_data=sql_select("select a.id, a.product_no, a.basis, a.company_id, a.party_id, a.product_date,b.product_qnty	from subcon_production_mst a,subcon_production_dtls b where a.entry_form=291 $date_cond_subc  and a.id=b.mst_id order by a.product_date");



		foreach($knit_finish_fab_issue_data as $row){
			$dateitme=strtotime($row[csf('product_date')]);
			$rev_qty[$dateitme]['aop_prod_qty'] += $row[csf('product_qnty')];
		}


		//=========================Cut and Lay Entry Ratio Wise 3========================

		// $cutting_data=sql_select("select a.id,a.order_ids,a.ship_date,a.plies,b.entry_date,a.marker_qty,a.order_qty,a.total_lay_qty,a.lay_balance_qty,b.job_no,b.job_year,b.company_id, a.order_cut_no, a.roll_data 	from ppl_cut_lay_dtls a, ppl_cut_lay_mst b where b.id=a.mst_id $cutting_date_cond order by a.id");

		// $cutting_data=sql_select("select a.cutting_no, sum(c.size_qty) as qty ,a.entry_date
		// from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle c ,wo_po_details_master d
		// where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and  a.job_no=d.job_no  and  a.status_active=1 and a.is_deleted=0 and b.status_active=1
		//  and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $cutting_date_cond $company_cond3 group by  a.cutting_no ,a.entry_date order by a.entry_date ");

		// foreach($cutting_data as $row){
		// 	$dateitme=strtotime($row[csf('entry_date')]);
		// 	$rev_qty[$dateitme]['cutting_qty'] += $row[csf('qty')];
		// }
		$cutting_data_sql=sql_select("SELECT a.production_date,CASE WHEN a.production_type=1 THEN b.production_qnty ELSE 0 END
		AS cutting_qty from pro_garments_production_mst a,pro_garments_production_dtls b WHERE a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond $company_cond");
		foreach($cutting_data_sql as $row){
			$rev_qty[$row[csf('production_date')]]['cutting_qty'] += $row[csf('cutting_qty')];
		}

		ob_start();
		?>
	    <style>
	        #sammary_tbl th, #sammary_tbl td {
	            padding: 0 7px;
	        }
	        fieldset{border:0;}
	    </style>
	    <fieldset style="width: 1520px; margin:20px auto;">
	        <table cellpadding="0" cellspacing="0" style="text-align: center; width: 100%">
	            <tr class="form_caption" style="border:none;">
	                <td align="center" width="100%" colspan="09"><strong style="font-size:25px"><?
							$com_name = return_field_value("company_name", "lib_company", "id=" . $cbo_company_name, "company_name");
							echo $com_name; ?></strong></td>
	            </tr>

	            <tr class="form_caption" style="border:none;">
	                <td align="center" width="100%" colspan="15">
	                    <strong style="font-size:16px">Production Summary:
							<?
							if ($start_date != '') echo change_date_format($start_date) . ' To ' . change_date_format($end_date); else echo '';
							?>
	                    </strong>
	                </td>
	            </tr>

	            <tr>
	                <th colspan="09" style="text-align: right;">Date of Print:&nbsp&nbsp<? echo date('d-m-Y'); ?></th>
	            </tr>

	            <tr>
	                <th colspan="09" style="text-align: right;">Time of Print:&nbsp&nbsp<? echo date('h:i A'); ?></th>
	            </tr>
	        </table>
	        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
	            <thead>
		            <tr>
		                <th width="30">SL</th>
		                <th width="80">DATE</th>
		                <th width="80">KNITTING</th>
		                <th width="80">DYEING</th>

						<th width="80">AOP</th>
						<th width="80">Finish Fabric Issue</th>

		                <th width="80">CUTTING</th>

						<th width="80">PRINT</th>
						<th width="80">Embr.</th>


		                <th width="80">SEWING</th>
						<th width="80">GMTs WASH</th>
		                <th width="80">IRON</th>
		                <th width="80">FINISH</th>
						<th width="80">Ex-Factory</th>
		                <th width="">REMARKS</th>
		            </tr>
	            </thead>
				<?php
				$date_arr = array();
				$date_fri_arr = array();
			 	$k=1;
	 			$total_knitting_qty = 0;
				$total_dyeing_qty = 0;
				$total_cutting_qty = 0;
				$total_print_issue_qty = 0;
				$total_print_rcv_qty = 0;
				$total_emb_issue_qty = 0;
				$total_emb_rcv_qty = 0;
				$total_special_issue_qty = 0;
				$total_special_rcv_qty = 0;
				$total_dyeing_issue_qty = 0;
				$total_dyeing_rcv_qty = 0;
				$total_wash_issue_qty = 0;
				$total_wash_rcv_qty = 0;
				$total_sewing_qty = 0;
				$total_iron_qty = 0;
				$total_finish_qty = 0;
				$total_aop_qty=0;
				$total_knit_finish_fab_qty=0;
				$total_print_qty =0;
				$total_emb_qty=0;
				$total_wash_qty=0;
				$total_gmts_del_qty=0;
				ksort($rev_qty);
	 			foreach ($rev_qty as $qtyValue => $result_row)
	 			{
					if (!in_array($qtyValue, $date_arr))
					{
						$date_arr[] = $qtyValue;
					}

					$i = 1;
					$total_knitting_qty  =$total_knitting_qty + $result_row['knitting'];
					$total_dyeing_qty += $result_row['dyeing'];
					$total_cutting_qty += $result_row['cutting_qty'];
					$total_aop_qty += $result_row['aop_prod_qty'];
					$total_knit_finish_fab_qty += $result_row['knit_finish_fab_issue_qty'];
					$total_print_qty += $result_row['print_prod_qty'];
					$total_emb_qty += $result_row['emb_prod_qty'];

					$total_wash_qty += $result_row['wash_prod_qty'];
					$total_gmts_del_qty += $result_row['gmts_delivery_qty'];

					$total_print_issue_qty += $result_row['print_issue'];
					$total_print_rcv_qty += $result_row['print_rcv'];
					$total_emb_issue_qty += $result_row['emb_issue'];
					$total_emb_rcv_qty += $result_row['emb_rcv'];
					$total_special_issue_qty += $result_row['special_issue'];
					$total_special_rcv_qty += $result_row['special_rcv'];
					$total_dyeing_issue_qty += $result_row['dyeing_issue'];
					$total_dyeing_rcv_qty += $result_row['dyeing_rcv'];
					$total_wash_issue_qty += $result_row['wash_issue'];
					$total_wash_rcv_qty += $result_row['wash_rcv'];
					$total_sewing_qty += $result_row['sewing'];
					$total_iron_qty += $result_row['iron'];
					$total_finish_qty += $result_row['finish'];
				    $getdate = date('D', $time =$qtyValue);
					if ($getdate != 'Fri')
					{
						if (!in_array($qtyValue, $date_fri_arr))
						{
							$date_fri_arr[] = $qtyValue;
						}

						//$j++;
						$totl_knit_qty += $result_row['knitting'];
						$totl_dyei_qty += $result_row['dyeing'];

						$totl_aop_qty += $result_row['aop_prod_qty'];
						$totl_knit_fin_fab_qty += $result_row['knit_finish_fab_issue_qty'];

						$totl_cutt_qty += $result_row['cutting_qty'];

						$totl_print_qty += $result_row['print_prod_qty'];
						$totl_emb_qty += $result_row['emb_prod_qty'];

						$totl_pnt_issue_qty += $result_row['print_issue'];
						$totl_prnt_rcv_qty += $result_row['print_rcv'];

						$totl_emb_issue_qty += $result_row['emb_issue'];
						$totl_emb_rcv_qty += $result_row['emb_rcv'];

						$totl_special_issue_qty += $result_row['special_issue'];
						$totl_special_rcv_qty += $result_row['special_rcv'];

						$totl_dyeing_issue_qty += $result_row['dyeing_issue'];
						$totl_dyeing_rcv_qty += $result_row['dyeing_rcv'];

						$totl_wash_issue_qty += $result_row['wash_issue'];
						$totl_wash_rcv_qty += $result_row['wash_rcv'];
						$totl_sewi_qty += $result_row['sewing'];
						$totl_wash_qty += $result_row['wash_prod_qty'];
						$totl_iro_qty += $result_row['iron'];
						$totl_fini_qty += $result_row['finish'];
						$totl_gmts_del_qty += $result_row['gmts_delivery_qty'];
					}

					if ($i % 2 == 1) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

					  ?>
		            <tr bgcolor="<? $timestamp =$qtyValue;$day_name= date("l", $timestamp );
		            if($day_name=="Friday"){echo "crimson";} else{echo $bgcolor; }?>" style="<?if($day_name=="Friday"){echo 'color:white;';}?>">
		                <td align="center"><?  echo $k; ?></td>
		                <td align="center"><? echo date('d-M-Y',$qtyValue); ?></td>
		                <td align="right"><? echo number_format($result_row['knitting'], 2); ?></td>
		                <td align="right"><? echo number_format($result_row['dyeing'], 2); ?></td>
						<td align="right"><? echo number_format($result_row['aop_prod_qty'], 2); ?></td>
						<td align="right"><? echo number_format($result_row['knit_finish_fab_issue_qty'], 2); ?></td>
		                <td align="right"><? echo number_format($result_row['cutting_qty'], 0); ?></td>
						<td align="right"><? echo number_format($result_row['print_prod_qty'], 0); ?></td>
						<td align="right"><? echo number_format($result_row['emb_prod_qty'], 0); ?></td>

		                <td align="right"><? echo number_format($result_row['sewing'], 0); ?></td>
						<td align="right"><? echo number_format($result_row['wash_prod_qty'], 0); ?></td>
		                <td align="right"><? echo number_format($result_row['iron'], 0); ?></td>
		                <td align="right"><? echo number_format($result_row['finish'], 0); ?></td>
						<td align="right"><? echo number_format($result_row['gmts_delivery_qty'], 0); ?></td>
		                <td></td>
		            </tr>
					<?
					$k++;
					$i++;
					$date_count = count($date_arr);
					$date_count2 = count($date_fri_arr);
				}
				?>
	            <tr>
	                <td colspan="18" style="padding: 1px;"></td>
	            </tr>
	            <tr>

	                <td align="left" colspan="2"><b>Total</b></td>
	                <td align="right"><b><?php echo number_format($total_knitting_qty, 2); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_dyeing_qty, 2); ?></b></td>
					<td align="right"><b><?php echo number_format($total_aop_qty, 2); ?></b></td>
					<td align="right"><b><?php echo number_format($total_knit_finish_fab_qty, 2); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_cutting_qty, 0); ?></b></td>
					<td align="right"><b><?php echo number_format($total_print_qty, 0); ?></b></td>
					<td align="right"><b><?php echo number_format($total_emb_qty, 0); ?></b></td>

	                <td align="right"><b><?php echo number_format($total_sewing_qty, 0); ?></b></td>
					<td align="right"><b><?php echo number_format($total_wash_qty, 0); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_iron_qty, 0); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_finish_qty, 0); ?></b></td>
					<td align="right"><b><?php echo number_format($total_gmts_del_qty, 0); ?></b></td>
	                <td></td>
	            </tr>

	            <tr>
	                <td align="left" colspan="2"><b>AVARAGE</b></td>
	                <td width="100" align="right">
	                    <b><?php echo number_format($total_knitting_qty / $date_count, 2); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_dyeing_qty / $date_count, 2); ?></b>
	                </td>
					<td align="right">
	                    <b><?php echo number_format($total_aop_qty / $date_count, 2); ?></b>
	                </td>
					<td align="right">
	                    <b><?php echo number_format($total_knit_finish_fab_qty / $date_count, 2); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_cutting_qty / $date_count, 0); ?></b>
	                </td>
					<td align="right">
	                    <b><?php echo number_format($total_print_qty / $date_count, 0); ?></b>
	                </td>
					<td align="right">
	                    <b><?php echo number_format($total_emb_qty / $date_count, 0); ?></b>
	                </td>

	                <td align="right">
	                    <b><?php echo number_format($total_sewing_qty / $date_count, 0); ?></b>
	                </td>
					<td align="right">
	                    <b><?php echo number_format($total_wash_qty / $date_count, 0); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_iron_qty / $date_count, 0); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_finish_qty / $date_count, 0); ?></b>
	                </td>
					<td align="right">
	                    <b><?php echo number_format($total_gmts_del_qty / $date_count, 0); ?></b>
	                </td>
	                <td></td>
	            </tr>
	            <tr>
	                <td align="left" colspan="2"><b>TOTAL EXCLUDE FRIDAY</b></td>
	                <td align="right"><b><?php echo number_format($totl_knit_qty, 2); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_dyei_qty, 2); ?></b></td>
					<td align="right"><b><?php echo number_format($totl_aop_qty, 2); ?></b></td>
					<td align="right"><b><?php echo number_format($totl_knit_fin_fab_qty, 2); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_cutt_qty, 0); ?></b></td>
					<td align="right"><b><?php echo number_format($totl_print_qty, 0); ?></b></td>
					<td align="right"><b><?php echo number_format($totl_emb_qty, 0); ?></b></td>

	                <td align="right"><b><?php echo number_format($totl_sewi_qty, 0); ?></b></td>
					<td align="right"><b><?php echo number_format($totl_wash_qty, 0); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_iro_qty, 0); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_fini_qty, 0); ?></b></td>
					<td align="right"><b><?php echo number_format($totl_gmts_del_qty, 0); ?></b></td>
	                <td>&nbsp;</td>
	            </tr>
	            <tr>
	                <td align="left" colspan="2"><b>AVG. EXCL. FRIDAY</b></td>
	                <td align="right"><b><?php echo number_format($totl_knit_qty / $date_count2, 2); ?></b>
	                </td>
	                <td align="right"><b><?php echo number_format($totl_dyei_qty / $date_count2, 2); ?></b>
	                </td>
					<td align="right"><b><?php echo number_format($totl_aop_qty / $date_count2, 2); ?></b>
	                </td>
					<td align="right"><b><?php echo number_format($totl_knit_fin_fab_qty / $date_count2, 2); ?></b>
	                </td>
	                <td align="right"><b><?php echo number_format($totl_cutt_qty / $date_count2, 0); ?></b>
	                </td>
					<td align="right"><b><?php echo number_format($totl_print_qty / $date_count2, 0); ?></b>
	                </td>
					<td align="right"><b><?php echo number_format($totl_emb_qty / $date_count2, 0); ?></b>
	                </td>

	                <td align="right"><b><?php echo number_format($totl_sewi_qty / $date_count2, 0); ?></b>
	                </td>
					<td align="right"><b><?php echo number_format($totl_wash_qty / $date_count2, 0); ?></b>
	                </td>
	                <td align="right"><b><?php echo number_format($totl_iro_qty / $date_count2, 0); ?></b>
	                </td>
	                <td align="right"><b><?php echo number_format($totl_fini_qty / $date_count2, 0); ?></b>
	                </td>
					<td align="right"><b><?php echo number_format($totl_gmts_del_qty / $date_count2, 0); ?></b>
	                </td>
	                <td>&nbsp;</td>
	            </tr>
	        </table>
	    </fieldset><br/>


	    <!-- ======compuny,location wise summury========== -->
	    <fieldset style="width: 1320px; margin:0px auto;">
	        <table cellspacing="0" cellpadding="0" id="sammary_tbl" border="1" rules="all" class="rpt_table" style="width: 100%">
	            <thead>
	            <tr>
	                <th colspan="20" style="text-align: center;">FACTORY WISE PRODUCTION SUMMARY</th>
	            </tr>
	            <tr>
						<th>SL</th>
						<th>Company</th>
						<th>Location/Factory</th>
						<th>KNITTING</th>
						<th>DYEING</th>
						<th>CUTTING</th>
						<th>PRINT SEND</th>
						<th>PRINT RCV</th>
						<th>EMB SEND</th>
						<th>EMB RCV</th>
						<th>Special Work SEND</th>
						<th>Special Work RCV</th>
						<th>GMT Dyeing SEND</th>
						<th>GMT Dyeing RCV</th>
						<th>WASH SEND</th>
						<th>WASH RCV</th>
						<th>SEWING</th>
						<th>IRON</th>
						<th>FINISH</th>
						<th>REMARKS</th>
	            </tr>
	            </thead>

				<?

				$k = 1;
				/*echo "<pre>";
				print_r($rev_qty2);die;*/
				foreach ($rev_qty2 as $company_id => $company_data) {
					foreach ($company_data as $location_id => $location_data) {
						if ($k % 2 == 1) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

						?>
	                    <tr bgcolor="<? echo $bgcolor; ?>">
	                        <td><? echo $k; ?></td>
	                        <td><? echo $company_arr[$company_id]; ?></td>
	                        <td><? echo $location_arr[$location_id]; ?></td>
	                        <td align="right"><a href="##" onClick="openmypage_popup(<? echo $company_id; ?>,'<? echo $location_id; ?>','1','production_popup','1','knitting popup','knitting');" ><? echo number_format($location_data['knitting'], 2); ?></a></td>
	                        <td align="right"><a href="##" onClick="openmypage_popup(<? echo $company_id; ?>,'<? echo $location_id; ?>','1','production_popup','1','dyeing popup','dyeing');" ><? echo number_format($location_data['dyeing'], 2); ?></a></td>
	                        <td align="right"><a href="##" onClick="openmypage_popup(<? echo $company_id; ?>,'<? echo $location_id; ?>','1','production_popup','0','cutting popup','');" > <? echo number_format($location_data['cutting'], 0); ?></a></td>
	                        <td align="right"><? echo number_format($location_data['print_issue'], 0); ?></td>
	                        <td align="right"><? echo number_format($location_data['print_rcv'], 0); ?></td>
	                        <td align="right"><? echo number_format($location_data['emb_issue'], 0); ?></td>
	                        <td align="right"><? echo number_format($location_data['emb_rcv'], 0); ?></td>
	                        <td align="right"><? echo number_format($location_data['special_issue'], 0); ?></td>
	                        <td align="right"><? echo number_format($location_data['special_rcv'], 0); ?></td>
	                        <td align="right"><? echo number_format($location_data['dyeing_issue'], 0); ?></td>
	                        <td align="right"><? echo number_format($location_data['dyeing_rcv'], 0); ?></td>
	                        <td align="right"><? echo number_format($location_data['wash_issue'], 0); ?></td>
	                        <td align="right"><? echo number_format($location_data['wash_rcv'], 0); ?></td>
	                        <td align="right"><a href="##" onClick="openmypage_popup(<? echo $company_id; ?>,'<? echo $location_id; ?>','5','production_popup','0','sewing popup','');" ><? echo number_format($location_data['sewing'], 0); ?></a></td>
	                        <td align="right"><a href="##" onClick="openmypage_popup(<? echo $company_id; ?>,'<? echo $location_id; ?>','7','production_popup','0','iron popup','');" ><? echo number_format($location_data['iron'], 0); ?></a></td>
	                        <td align="right"><a href="##" onClick="openmypage_popup(<? echo $company_id; ?>,'<? echo $location_id; ?>','8','production_popup','0','finish popup','');" ><? echo number_format($location_data['finish'],0); ?></a></td>
	                        <td></td>
	                    </tr>
						<?
						$k++;
						$total_knitting += $location_data['knitting'];
						$total_dyeing += $location_data['dyeing'];
						$total_cutting += $location_data['cutting'];
						$total_print_issue += $location_data['print_issue'];
						$total_print_rcv += $location_data['print_rcv'];
						$total_emb_issue += $location_data['emb_issue'];
						$total_emb_rcv += $location_data['emb_rcv'];
						$total_special_issue += $location_data['special_issue'];
						$total_special_rcv += $location_data['special_rcv'];
						$total_dyeing_issue += $location_data['dyeing_issue'];
						$total_dyeing_rcv += $location_data['dyeing_rcv'];
						$total_wash_issue += $location_data['wash_issue'];
						$total_wash_rcv += $location_data['wash_rcv'];
						$total_sewing += $location_data['sewing'];
						$total_iron += $location_data['iron'];
						$total_finish += $location_data['finish'];

					}
				}
				?>
	            <tr bgcolor="<? echo $bgcolor; ?>">
	                <td align="left" colspan="3"><b>TOTAL</b></td>
	                <td align="right"><b><? echo number_format($total_knitting, 2); ?></b></td>
	                <td align="right"><b><? echo number_format($total_dyeing, 2); ?></b></td>
	                <td align="right"><b><? echo number_format($total_cutting, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_print_issue, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_print_rcv, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_emb_issue, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_emb_rcv, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_special_issue, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_special_rcv, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_dyeing_issue, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_dyeing_rcv, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_wash_issue, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_wash_rcv, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_sewing, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_iron, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_finish, 0); ?></b></td>
	                <td> </td>
	            </tr>
	        </table>
	    </fieldset>
		<?
		foreach (glob("$user_id*.xls") as $filename) {
			if (@filemtime($filename) < (time() - $seconds_old))
				@unlink($filename);
		}
		//---------end------------//
		$name = time();
		$filename = $user_id . "_" . $name . ".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc, ob_get_contents());
		//$filename=$user_id."_".$name.".xls";
		echo "$total_data####$filename";
	}
	else if($type==3) // Show 3
	{
		$location_id_cond_subc = ($location_id == 0) ? "" : " and a.knit_location_id=$location_id";
		$location_id_cond_y = ($location_id == 0) ? "" : " and d.location_id=$location_id";
		$knit_lc_location_id_cond = ($location_id == 0) ? "" : " and a.location_id=$location_id";
		$location_id_issue_cond = ($location_id == 0) ? "" : " and b.location_id=$location_id";
		$working_location_cond = ($location_id == 0) ? "" : " and a.working_location=$location_id";

		$company_cond_order = ($company_name == 0) ? "" : " and b.company_name=$company_name";
		$location_id_cond_order = ($location_id == 0) ? "" : " and b.location_name=$location_id";
		$company_cond_sort_book = ($company_name == 0) ? "" : " and a.company_id=$company_name";
		$company_cond_transf = ($company_name == 0) ? "" : " and b.company_id=$company_name";
		$working_company_id_cond = ($company_name == 0) ? "" : " and a.working_company_id=$company_name";
		$working_company_cond = ($company_name == 0) ? "" : " and a.working_company=$company_name";

		// Order info
		$order_sql=sql_select("SELECT a.job_no_mst, sum(a.po_quantity) as po_quantity, sum(a.po_quantity*b.total_set_qnty) as po_quantity_psc,
        sum(case when a.is_confirmed=1 then a.po_quantity*b.total_set_qnty else 0 end) as confirm_qty_pcs,
        sum(case when a.is_confirmed=2 then a.po_quantity*b.total_set_qnty else 0 end) as projec_qty_pcs,
        a.packing, b.company_name, b.location_name, b.order_uom , b.total_set_qnty, a.po_received_date
        from wo_po_break_down a, wo_po_details_master b
        where a.job_id=b.id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_cond_order $date_cond_order $location_id_cond_order
        group by a.job_no_mst, a.packing, b.company_name, b.location_name, b.order_uom, b.total_set_qnty, a.po_received_date order by a.po_received_date"); //and a.job_no_mst='rpc-21-00117'
		//echo $order_sql;die;
		$rev_qty = array();
		foreach ($order_sql as $value)
		{
			$rev_qty[$value[csf('company_name')]][strtotime($value[csf('po_received_date')])]['confirm_qty_pcs'] += $value[csf('confirm_qty_pcs')];
			$rev_qty[$value[csf('company_name')]][strtotime($value[csf('po_received_date')])]['projec_qty_pcs'] += $value[csf('projec_qty_pcs')];

			$rev_qty2[$value[csf('company_name')]]['confirm_qty_pcs'] += $value[csf('confirm_qty_pcs')];
			$rev_qty2[$value[csf('company_name')]]['projec_qty_pcs'] += $value[csf('projec_qty_pcs')];
		}
		//echo "<pre>";print_r($rev_qty);

		// ================================= cancel order =============================
		$cancel_order_sql=sql_select("SELECT a.job_no_mst, b.company_name, a.po_received_date, $cancel_date, b.location_name, a.packing, b.total_set_qnty,
        sum(case when a.is_confirmed=1 then a.po_quantity*b.total_set_qnty else 0 end) as cancel_order_qty
        from wo_po_break_down a, wo_po_details_master b
        where a.job_no_mst=b.job_no and a.status_active in(3) and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_cond_order $date_cond_order2 $location_id_cond_order
        group by a.job_no_mst, b.company_name, a.update_date, a.po_received_date, b.location_name, a.packing, b.total_set_qnty order by a.update_date");
		foreach ($cancel_order_sql as $value)
		{
			$date=$value[csf('cancel_date')];
			$cancel_date2=strtoupper(date("d-M-y", strtotime("$date")));
			$rev_qty[$value[csf('company_name')]][strtotime($cancel_date2)]['cancel_order_qty'] += $value[csf('cancel_order_qty')];
			$rev_qty2[$value[csf('company_name')]]['cancel_order_qty'] += $value[csf('cancel_order_qty')];
		}
		// echo "<pre>";print_r($rev_qty);

		// =================================== Short Fabric Booking ===================================
		$sort_fab_book_sql=sql_select("SELECT a.company_id, a.booking_date, sum(b.grey_fab_qnty) as sort_grey_qty  from wo_booking_mst a,  wo_booking_dtls b
        where a.booking_no=b.booking_no and a.job_no=b.job_no and a.entry_form=88 and a.item_category=2 and a.status_active=1 and b.is_deleted=0 and a.status_active=1 and b.is_deleted=0  $company_cond_sort_book $date_cond_sort_book
        group by a.company_id, a.booking_date order by a.booking_date");
		foreach ($sort_fab_book_sql as $value) // Location not found
		{
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('booking_date')])]['sort_grey_qty'] += $value[csf('sort_grey_qty')];
			$rev_qty2[$value[csf('company_id')]]['sort_grey_qty'] += $value[csf('sort_grey_qty')];
		}

		//================================== Sample Sewing Output (Sample Production [Pcs]) =====================================
		$sample_prod_sql=sql_select("SELECT a.company_id, b.sewing_date, a.location, sum(b.qc_pass_qty) as sample_prod_qty  from sample_sewing_output_mst a,  sample_sewing_output_dtls b
		where a.id=b.sample_sewing_output_mst_id and a.entry_form_id=130 and a.status_active=1 and b.is_deleted=0 and a.status_active=1 and b.is_deleted=0
		$company_cond_sort_book $location_cond $date_cond_sample_prod
		group by a.company_id, b.sewing_date, a.location order by b.sewing_date");
		foreach ($sample_prod_sql as $value)
		{
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('sewing_date')])]['sample_prod_qty'] += $value[csf('sample_prod_qty')];
			//$rev_qty[$value[csf('company_id')]][$value[csf('sewing_date')]]['location_id'] = $value[csf('location')];
			$rev_qty2[$value[csf('company_id')]]['sample_prod_qty'] += $value[csf('sample_prod_qty')];
			//$rev_qty2[$value[csf('company_id')]]['location_id'] = $value[csf('location')];
		}
		// echo "<pre>";print_r($rev_qty);

		//============================= Sample Delivery Entry (Sample Delivery [Pcs]) ==================================
		$sample_delivery_sql=sql_select("SELECT a.company_id, a.ex_factory_date, a.location, sum(b.ex_factory_qty) as sample_delivery_qty from sample_ex_factory_mst a,  sample_ex_factory_dtls b
		where a.id=b.sample_ex_factory_mst_id and a.entry_form_id=132 and b.entry_form_id=132 and a.status_active=1 and b.is_deleted=0 and a.status_active=1 and b.is_deleted=0
		$company_cond_sort_book $location_cond $date_cond_sample_delivery
		group by a.company_id, a.ex_factory_date, a.location order by ex_factory_date");
		foreach ($sample_delivery_sql as $value)
		{
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('ex_factory_date')])]['sample_delivery_qty'] += $value[csf('sample_delivery_qty')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('ex_factory_date')])]['location_id'] = $value[csf('location')];
			$rev_qty2[$value[csf('company_id')]]['sample_delivery_qty'] += $value[csf('sample_delivery_qty')];
			$rev_qty2[$value[csf('company_id')]]['location_id'] = $value[csf('location')];
		}
		// echo "<pre>";print_r($rev_qty);

		//=================================  Yarn Info =====================================
		$yarn_sql="SELECT b.mst_id, b.prod_id, b.company_id, b.transaction_date,
		sum(case when b.transaction_type in (1,4,5) and c.dyed_type in(0,2) then b.cons_quantity else 0 end) as grey_recv_total,
		sum(case when b.transaction_type in (1,4,5) and c.dyed_type in(1) then b.cons_quantity else 0 end) as dyed_recv_total,
		sum(case when b.transaction_type in (2,3,6) and c.dyed_type in(0,2) then b.cons_quantity else 0 end) as grey_iss_total,
		sum(case when b.transaction_type in (2,3,6) and c.dyed_type in(1) then b.cons_quantity else 0 end) as dyed_iss_total
		from inv_transaction b, product_details_master c, lib_store_location d
		where b.prod_id=c.id and b.store_id=d.id and b.item_category=1 and b.transaction_type in(1,2,3,4,5,6) and b.item_category=1 $company_cond_transf $location_id_cond_y $date_cond_tran
		and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.mst_id, b.prod_id, b.company_id, b.transaction_date order by transaction_date";
		$yarn_sql_result=sql_select($yarn_sql);
		foreach ($yarn_sql_result as $value)
		{
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('transaction_date')])]['grey_recv_total'] += $value[csf('grey_recv_total')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('transaction_date')])]['dyed_recv_total'] += $value[csf('dyed_recv_total')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('transaction_date')])]['grey_iss_total'] += $value[csf('grey_iss_total')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('transaction_date')])]['dyed_iss_total'] += $value[csf('dyed_iss_total')];

			$rev_qty2[$value[csf('company_id')]]['grey_recv_total'] += $value[csf('grey_recv_total')];
			$rev_qty2[$value[csf('company_id')]]['dyed_recv_total'] += $value[csf('dyed_recv_total')];
			$rev_qty2[$value[csf('company_id')]]['grey_iss_total'] += $value[csf('grey_iss_total')];
			$rev_qty2[$value[csf('company_id')]]['dyed_iss_total'] += $value[csf('dyed_iss_total')];
		}

		//==============================  knitting and Roll Recv summary ==============================
		$knit_and_rollRecv_sqls="SELECT x.receive_date, x.knitting_company,x.location_id, sum(x.recv_qty_in) as recv_qty_in, sum(x.recv_qty_out) as recv_qty_out, sum(x.knitting_qty_in) as knitting_qty_in, sum(x.knitting_qty_out) as knitting_qty_out
		FROM (
		SELECT a.receive_date, a.knitting_company, a.knitting_location_id as location_id,
		sum(case when a.entry_form =58 then b.grey_receive_qnty else 0 end) as recv_qty_in,
		0 as recv_qty_out,
		sum(case when a.entry_form =2 then b.grey_receive_qnty else 0 end) as knitting_qty_in,
		0 as knitting_qty_out
		from inv_receive_master a, pro_grey_prod_entry_dtls b
		where a.id=b.mst_id and a.status_active=1 $date_cond_knit $company_knit_cond $location_id_cond and a.entry_form in(2,58) and a.item_category=13 and a.is_deleted=0
		and a.knitting_source=1 group by a.receive_date,a.knitting_company,a.knitting_location_id
		UNION ALL
		SELECT a.receive_date, a.company_id as knitting_company, a.location_id,
		0 as recv_qty_in,
		sum(case when a.entry_form =58 then b.grey_receive_qnty else 0 end) as recv_qty_out,
		0 as knitting_qty_in,
		sum(case when a.entry_form =2 then b.grey_receive_qnty else 0 end) as knitting_qty_out
		from inv_receive_master a, pro_grey_prod_entry_dtls b
		where a.id=b.mst_id and a.status_active=1 $date_cond_knit $company_cond_sort_book $knit_lc_location_id_cond and a.entry_form in(2,58) and a.item_category=13 and a.is_deleted=0
		and a.knitting_source=3 group by a.receive_date,a.company_id,a.location_id )  x
		group by x.receive_date, x.knitting_company,x.location_id order by x.receive_date";
		//echo $knit_and_rollRecv_sqls;die;
		$knit_and_rollRecv_sqls_result=sql_select($knit_and_rollRecv_sqls);
		foreach ($knit_and_rollRecv_sqls_result as $value)
		{
			$rev_qty[$value[csf('knitting_company')]][strtotime($value[csf('receive_date')])]['knitting_qty_in'] += $value[csf('knitting_qty_in')];
			$rev_qty[$value[csf('knitting_company')]][strtotime($value[csf('receive_date')])]['knitting_qty_out'] += $value[csf('knitting_qty_out')];
			$rev_qty2[$value[csf('knitting_company')]]['knitting_qty_in'] += $value[csf('knitting_qty_in')];
			$rev_qty2[$value[csf('knitting_company')]]['knitting_qty_out'] += $value[csf('knitting_qty_out')];
			// ========
			$rev_qty[$value[csf('knitting_company')]][strtotime($value[csf('receive_date')])]['recv_qty_in'] += $value[csf('recv_qty_in')];
			$rev_qty[$value[csf('knitting_company')]][strtotime($value[csf('receive_date')])]['recv_qty_out'] += $value[csf('recv_qty_out')];
			$rev_qty2[$value[csf('knitting_company')]]['recv_qty_in'] += $value[csf('recv_qty_in')];
			$rev_qty2[$value[csf('knitting_company')]]['recv_qty_out'] += $value[csf('recv_qty_out')];
		}

		//=============================== Sub-Contact Knitting Production =================================
		$knit_subc_sqls="SELECT x.product_date, x.knitting_company,x.location_id, sum(x.subc_knitting_qty_in) as subc_knitting_qty_in, sum(x.subc_knitting_qty_out) as subc_knitting_qty_out
		FROM (
		SELECT a.product_date, a.knitting_company, a.knit_location_id as location_id, sum(b.product_qnty) as subc_knitting_qty_in, 0 as subc_knitting_qty_out
		from subcon_production_mst a, subcon_production_dtls b
		where a.id=b.mst_id $date_cond_subc $company_knit_cond $location_id_cond_subc
		and a.entry_form=159 and a.knitting_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.product_date,a.knitting_company, a.knit_location_id
		union all
		select a.product_date, a.company_id as knitting_company, a.location_id, 0 as subc_knitting_qty_in, sum(b.product_qnty) as subc_knitting_qty_out
		from subcon_production_mst a, subcon_production_dtls b
		where a.id=b.mst_id $date_cond_subc $company_cond_sort_book $knit_lc_location_id_cond and a.entry_form=159 and a.knitting_source=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.product_date,a.company_id,a.location_id )  x
		group by x.product_date, x.knitting_company,x.location_id order by x.product_date";

		$knit_subc_sqls_result=sql_select($knit_subc_sqls);
		foreach ($knit_subc_sqls_result as $value)
		{
			$rev_qty[$value[csf('knitting_company')]][strtotime($value[csf('product_date')])]['subc_knitting_qty_in'] += $value[csf('subc_knitting_qty_in')];
			$rev_qty[$value[csf('knitting_company')]][strtotime($value[csf('product_date')])]['subc_knitting_qty_out'] += $value[csf('subc_knitting_qty_out')];
			$rev_qty2[$value[csf('knitting_company')]]['subc_knitting_qty_in'] += $value[csf('subc_knitting_qty_in')];
			$rev_qty2[$value[csf('knitting_company')]]['subc_knitting_qty_out'] += $value[csf('subc_knitting_qty_out')];
		}

		//================================= Roll Issue ============================
		$roll_issue_sql="SELECT a.issue_date, a.company_id, b.location_id, sum(d.qnty) as issue_qty, d.barcode_no
		from inv_issue_master a, inv_transaction b, inv_grey_fabric_issue_dtls c, pro_roll_details d
		where a.id=b.mst_id and a.id=c.mst_id and b.id=c.trans_id and a.id=d.mst_id and c.id=d.dtls_id and a.entry_form=61 and d.entry_form=61 and b.item_category=13 and b.transaction_type=2
		and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $company_cond_sort_book $date_cond_tran $location_id_issue_cond
		group by a.issue_date, a.company_id, b.location_id, d.barcode_no order by a.issue_date";
		$roll_issue_sql_result=sql_select($roll_issue_sql);// and a.issue_number='RpC-KGIR-21-00033'
		$issue_barcode_no = array();
		foreach ($roll_issue_sql_result as $value)
	 	{
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('issue_date')])]['issue_qty'] += $value[csf('issue_qty')];
			$rev_qty2[$value[csf('company_id')]]['issue_qty'] += $value[csf('issue_qty')];
			$issue_barcode_no[$value[csf('barcode_no')]]=$value[csf('barcode_no')];
		}

		// Grey Roll Receive For Batch summary
		//===============user for roll receive for batch, location not in roll receive for batch page start
		$all_issue_barcode_arr = array_filter(array_unique($issue_barcode_no));
		if(count($all_issue_barcode_arr)>0)
		{
			$all_issue_barcode = implode(",", $all_issue_barcode_arr);
			$barcodeCond = $all_issue_barcode_cond = "";

			if($db_type==2 && count($all_issue_barcode_arr)>999)
			{
				$all_barcode_no_chunk=array_chunk($all_issue_barcode_arr,999) ;
				foreach($all_barcode_no_chunk as $chunk_arr)
				{
					$barcodeCond.=" c.barcode_no in(".implode(",",$chunk_arr).") or ";
				}

				$all_issue_barcode_cond.=" and (".chop($barcodeCond,'or ').")";
			}
			else
			{
				$all_issue_barcode_cond=" and c.barcode_no in($all_issue_barcode)";
			}

			$recv_roll_batch_sql="SELECT c.barcode_no from inv_receive_mas_batchroll a, pro_grey_batch_dtls b, pro_roll_details c
			where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and a.entry_form=62 and c.entry_form=62
			and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $all_issue_barcode_cond";
			$recv_roll_batch_sql_result=sql_select($recv_roll_batch_sql);
			$recv_batch_barcode_no=array();
			foreach ($recv_roll_batch_sql_result as $value)
		 	{
				$recv_batch_barcode_no[$value[csf('barcode_no')]]=$value[csf('barcode_no')];
			}
		}

		$all_recv_batch_barcode_arr = array_filter(array_unique($recv_batch_barcode_no));
		if(count($all_recv_batch_barcode_arr)>0)
		{
			$all_recv_batch_barcode = implode(",", $all_recv_batch_barcode_arr);
			$recv_batch_barcodeCond = $all_rcv_batch_barcode_cond = "";
			if($db_type==2 && count($all_recv_batch_barcode_arr)>999)
			{
				$all_barcode_no_chunk=array_chunk($all_recv_batch_barcode_arr,999) ;
				foreach($all_barcode_no_chunk as $chunk_arr)
				{
					$recv_batch_barcodeCond.=" d.barcode_no in(".implode(",",$chunk_arr).") or ";
				}

				$all_rcv_batch_barcode_cond.=" and (".chop($recv_batch_barcodeCond,'or ').")";
			}
			else
			{
				$all_rcv_batch_barcode_cond=" and d.barcode_no in($all_recv_batch_barcode)";
			}

			$issue_to_recv_batch_sql="SELECT a.issue_date, a.company_id, b.location_id, sum(d.qnty) as recv_batch_qty, d.barcode_no
			from inv_issue_master a, inv_transaction b, inv_grey_fabric_issue_dtls c, pro_roll_details d
			where a.id=b.mst_id and a.id=c.mst_id and b.id=c.trans_id and a.id=d.mst_id and c.id=d.dtls_id and a.entry_form=61 and d.entry_form=61 and b.item_category=13 and b.transaction_type=2
			and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_cond_sort_book $date_cond_tran $location_id_issue_cond $all_rcv_batch_barcode_cond
			group by a.issue_date, a.company_id, b.location_id, d.barcode_no order by a.issue_date";
			$issue_to_recv_batch_result=sql_select($issue_to_recv_batch_sql);
			foreach ($issue_to_recv_batch_result as $value)
		 	{
				$rev_qty[$value[csf('company_id')]][strtotime($value[csf('issue_date')])]['recv_batch_qty'] += $value[csf('recv_batch_qty')];
				$rev_qty2[$value[csf('company_id')]]['recv_batch_qty'] += $value[csf('recv_batch_qty')];
			}
		}
		//===============user for roll receive for batch, location not in roll receive for batch page end

		// Roll Issue Retn and Transfer In & Out
		$roll_issue_retn_transfer_sql="SELECT b.company_id, b.transaction_date, d.location_id,
		sum(case when  b.transaction_type=4 then b.cons_quantity else 0 end) as issue_rtn_qty,
		sum(case when  b.transaction_type=5 then b.cons_quantity else 0 end) as trans_in_qty,
		sum(case when  b.transaction_type=6 then b.cons_quantity else 0 end) as trans_out_qty
		from inv_item_transfer_mst a, inv_transaction b, lib_store_location d
		where  a.id=b.mst_id and b.store_id=d.id and b.item_category=13 and b.transaction_type in(4,5,6) and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_cond_transf $location_id_cond_y $date_cond_tran and a.entry_form=82
		group by b.company_id, b.transaction_date, d.location_id order by b.transaction_date";
		$roll_issue_retn_transfer_sql_result=sql_select($roll_issue_retn_transfer_sql);
		foreach ($roll_issue_retn_transfer_sql_result as $value)
	 	{
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('transaction_date')])]['issue_rtn_qty'] += $value[csf('issue_rtn_qty')];
			$rev_qty2[$value[csf('company_id')]]['issue_rtn_qty'] += $value[csf('issue_rtn_qty')];
			// ===========
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('transaction_date')])]['trans_in_qty'] += $value[csf('trans_in_qty')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('transaction_date')])]['trans_out_qty'] += $value[csf('trans_out_qty')];
			$rev_qty2[$value[csf('company_id')]]['trans_in_qty'] += $value[csf('trans_in_qty')];
			$rev_qty2[$value[csf('company_id')]]['trans_out_qty'] += $value[csf('trans_out_qty')];
		}
		// echo "<pre>";print_r($rev_qty3);

		// Batch Creation
		/*$batch_sqls = sql_select("SELECT a.working_company_id, a.batch_date, d.location_id,
		sum(case when a.batch_against in(1,3,5) and a.entry_form=0 then b.batch_qnty else 0 end) as batch_qty,
		sum(case when a.batch_against in(2) and a.entry_form=0 then b.batch_qnty else 0 end) as re_process_batch_qty
		from pro_batch_create_mst a, pro_batch_create_dtls b, lib_prod_floor d
		where a.id=b.mst_id and a.floor_id=d.id and a.batch_against in(1,2,3,5) and a.entry_form=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $working_company_id_cond $location_id_cond_y $batch_date_cond
		group by  a.working_company_id, a.batch_date, d.location_id order by a.batch_date");*/

		// ================================= batch =============================
		$batch_sqls = sql_select("SELECT a.working_company_id, a.batch_date, d.location_id,
		sum(case when a.batch_against in(1,3,5) and a.entry_form=0 then b.batch_qnty else 0 end) as batch_qty,
		sum(case when a.batch_against in(2) and a.entry_form=0 then b.batch_qnty else 0 end) as re_process_batch_qty
		from pro_batch_create_dtls b, pro_batch_create_mst a left join lib_prod_floor d on a.floor_id=d.id
		where a.id=b.mst_id and a.batch_against in(1,2,3,5) and a.entry_form=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $working_company_id_cond $location_id_cond_y $batch_date_cond
		group by a.working_company_id, a.batch_date, d.location_id order by a.batch_date");
		//and a.batch_no='RpC-BC-21-00014'
	 	foreach ($batch_sqls as $value)
	 	{
			$rev_qty[$value[csf('working_company_id')]][strtotime($value[csf('batch_date')])]['batch_qty'] += $value[csf('batch_qty')];
			$rev_qty[$value[csf('working_company_id')]][strtotime($value[csf('batch_date')])]['re_process_batch_qty'] += $value[csf('re_process_batch_qty')];
			$rev_qty2[$value[csf('working_company_id')]]['batch_qty'] += $value[csf('batch_qty')];
			$rev_qty2[$value[csf('working_company_id')]]['re_process_batch_qty'] += $value[csf('re_process_batch_qty')];
		}
		//========================= Subcon Batch Creation =================================
		$subc_batch_sqls = sql_select("SELECT a.company_id, a.batch_date, a.location_id,
		sum(case when a.batch_against in(1) and a.entry_form=36 then b.batch_qnty else 0 end) as subc_batch_qty,
		sum(case when a.batch_against in(2) and a.entry_form=36 then b.batch_qnty else 0 end) as subc_re_process_batch_qty
		from pro_batch_create_mst a, pro_batch_create_dtls b
		where a.id=b.mst_id and a.batch_against in(1,2) and a.entry_form=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_cond_sort_book $knit_lc_location_id_cond $batch_date_cond
		group by  a.company_id, a.batch_date, a.location_id order by a.batch_date");
	 	foreach ($subc_batch_sqls as $value)
	 	{
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('batch_date')])]['subc_batch_qty'] += $value[csf('subc_batch_qty')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('batch_date')])]['subc_re_process_batch_qty'] += $value[csf('subc_re_process_batch_qty')];
			$rev_qty2[$value[csf('company_id')]]['subc_batch_qty'] += $value[csf('subc_batch_qty')];
			$rev_qty2[$value[csf('company_id')]]['subc_re_process_batch_qty'] += $value[csf('subc_re_process_batch_qty')];
		}

	 	//===================================== dyeing summary =================================
		$dyeing_sqls = sql_select("SELECT a.service_company, a.process_end_date as production_date, d.location_id,
		sum(case when a.entry_form=35 then c.batch_qnty else 0 end) as prod_qty,
 		sum(case when a.entry_form=36 then c.batch_qnty else 0 end) as subc_prod_qty
		from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c, lib_prod_floor d
		where a.batch_id=b.id and a.floor_id=d.id and a.service_source=1 and a.entry_form in(35,36) and a.load_unload_id=2 and b.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.batch_against in(1,2,3) and a.result=1 and b.is_sales!=1 $date_cond2 $company_dyeing_cond $location_id_cond_y
		group by a.service_company, a.process_end_date, d.location_id order by a.process_end_date");
	 	foreach ($dyeing_sqls as $value)
	 	{
			$rev_qty[$value[csf('service_company')]][strtotime($value[csf('production_date')])]['dyeing'] += $value[csf('prod_qty')];
			$rev_qty2[$value[csf('service_company')]]['dyeing'] += $value[csf('prod_qty')];
			//[$location_id_arr[$value[csf('floor_id')]]]
			$rev_qty[$value[csf('service_company')]][strtotime($value[csf('production_date')])]['subc_dyeing'] += $value[csf('subc_prod_qty')];
			$rev_qty2[$value[csf('service_company')]]['subc_dyeing'] += $value[csf('subc_prod_qty')];
		}
		//echo "<pre>";//print_r($rev_qtys);die;
		/*$subc_dyeing_sqls = sql_select("SELECT a.production_date, a.service_company,a.floor_id, d.location_id, sum(b.batch_qnty) as subc_prod_qty
		from pro_fab_subprocess a, pro_batch_create_dtls b, pro_batch_create_mst c, lib_prod_floor d
		where a.batch_id=c.id and b.mst_id=a.batch_id and a.floor_id=d.id and c.id=b.mst_id and  c.entry_form=36 and a.status_active=1 and a.is_deleted=0 and a.service_source=1 and a.load_unload_id=2 and a.result=1 $date_cond $company_dyeing_cond $location_id_cond_y and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		group by a.production_date,a.service_company,a.floor_id, d.location_id order by a.production_date");
		foreach ($subc_dyeing_sqls as $value)
	 	{
			$rev_qty[$value[csf('service_company')]][$value[csf('production_date')]]['subc_dyeing'] += $value[csf('subc_prod_qty')];
			$rev_qty2[$value[csf('service_company')]]['subc_dyeing'] += $value[csf('subc_prod_qty')];
		}*/

		//============================ Trims dyeing summary ===========================
		$trims_dyeing_sqls = sql_select("SELECT a.service_company, a.process_end_date as production_date, d.location_id, sum(case when a.entry_form=35 then c.trims_wgt_qnty else 0 end) as trims_dyeing_qty
		from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_trims_dtls c, lib_prod_floor d
		where a.batch_id=b.id and b.id=c.mst_id and a.floor_id=d.id and a.entry_form in(35) and a.load_unload_id=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.batch_against in(1,2,3) and a.result=1 and b.is_sales!=1 $date_cond2 $company_dyeing_cond $location_id_cond_y
		group by a.service_company, a.process_end_date, d.location_id order by a.process_end_date");
	 	foreach ($trims_dyeing_sqls as $value)
	 	{
			$rev_qty[$value[csf('service_company')]][strtotime($value[csf('production_date')])]['trims_dyeing_qty'] += $value[csf('trims_dyeing_qty')];
			$rev_qty2[$value[csf('service_company')]]['trims_dyeing_qty'] += $value[csf('trims_dyeing_qty')];
		}

		// Dyeing Finishing [Inbound]
		/*$dyeing_finishing_sql="SELECT x.receive_date, x.knitting_company,x.location_id, sum(x.dye_fin_qty_in) as dye_fin_qty_in, sum(x.dye_fin_qty_out) as dye_fin_qty_out
		FROM ( SELECT a.receive_date, a.knitting_company, a.knitting_location_id as location_id,
		sum(case when a.entry_form=66 then b.receive_qnty else 0 end) as dye_fin_qty_in, 0 as dye_fin_qty_out
		from inv_receive_master a, pro_finish_fabric_rcv_dtls b
		where a.id=b.mst_id and a.status_active=1 $date_cond_knit $company_knit_cond $location_id_cond and a.entry_form in(66) and a.item_category=2 and a.is_deleted=0 and a.knitting_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.receive_date,a.knitting_company,a.knitting_location_id
		UNION ALL
		select a.receive_date, a.company_id as knitting_company, a.location_id, 0 as dye_fin_qty_in,
		sum(case when a.entry_form=66 then b.receive_qnty else 0 end) as dye_fin_qty_out
		from inv_receive_master a, pro_finish_fabric_rcv_dtls b
		where a.id=b.mst_id and a.status_active=1 $date_cond_knit $company_cond_sort_book $knit_lc_location_id_cond and a.entry_form in(66) and a.item_category=2
		and a.is_deleted=0 and a.knitting_source=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.receive_date,a.company_id,a.location_id ) x
		group by x.receive_date, x.knitting_company,x.location_id order by x.receive_date";*/

		$dyeing_finishing_sql="SELECT x.receive_date, x.knitting_company,x.location_id, sum(x.dye_fin_qty_in) as dye_fin_qty_in, sum(x.dye_fin_qty_out) as dye_fin_qty_out
		FROM ( SELECT a.knitting_source, a.receive_date, a.knitting_company, b.floor, a.recv_number, a.knitting_location_id as location_id,b.order_id,b.batch_id,b.gsm, b.receive_qnty as dye_fin_qty_in, 0 as dye_fin_qty_out,
		b.reject_qty,b.color_id,b.fabric_description_id,b.buyer_id, c.batch_no, a.recv_number, c.booking_no, c.booking_without_order,c.extention_no,b.remarks
		from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c
		where a.id=b.mst_id and b.batch_id = c.id and a.entry_form in(7,66) and a.item_category=2 and a.knitting_source=1 and a.status_active=1 and a.is_deleted=0 and c.status_active =1 $date_cond_knit $company_knit_cond $location_id_cond
		group by a.knitting_source, a.receive_date, a.knitting_company, b.floor, a.recv_number, a.knitting_location_id,b.order_id,b.batch_id,b.gsm, b.receive_qnty,
		b.reject_qty,b.color_id,b.fabric_description_id,b.buyer_id, c.batch_no, a.recv_number, c.booking_no, c.booking_without_order,c.extention_no,b.remarks
		UNION ALL
		SELECT a.knitting_source, a.receive_date, a.knitting_company, b.floor, a.recv_number, a.knitting_location_id as location_id, b.order_id,b.batch_id,b.gsm, 0 as dye_fin_qty_in, b.receive_qnty as dye_fin_qty_out,
		b.reject_qty,b.color_id,b.fabric_description_id,b.buyer_id, c.batch_no, a.recv_number, c.booking_no, c.booking_without_order,c.extention_no,b.remarks
		from inv_receive_master a, pro_finish_fabric_rcv_dtls b , pro_batch_create_mst c
		where a.id=b.mst_id and b.batch_id = c.id and a.entry_form in(7,66) and a.item_category=2 and a.knitting_source=3 and a.status_active=1 and a.is_deleted=0 and c.status_active =1 $date_cond_knit $company_cond_sort_book $knit_lc_location_id_cond
		group by a.knitting_source, a.receive_date, a.knitting_company, b.floor, a.recv_number, a.knitting_location_id,b.order_id,b.batch_id,b.gsm, b.receive_qnty,
		b.reject_qty,b.color_id,b.fabric_description_id,b.buyer_id, c.batch_no, a.recv_number, c.booking_no, c.booking_without_order,c.extention_no,b.remarks ) x
		group by x.receive_date, x.knitting_company,x.location_id order by x.receive_date";

		$dyeing_finishing_result=sql_select($dyeing_finishing_sql);
		foreach ($dyeing_finishing_result as $value)
		{
			$rev_qty[$value[csf('knitting_company')]][strtotime($value[csf('receive_date')])]['dye_fin_qty_in'] += $value[csf('dye_fin_qty_in')];
			$rev_qty[$value[csf('knitting_company')]][strtotime($value[csf('receive_date')])]['dye_fin_qty_out'] += $value[csf('dye_fin_qty_out')];
			$rev_qty2[$value[csf('knitting_company')]]['dye_fin_qty_in'] += $value[csf('dye_fin_qty_in')];
			$rev_qty2[$value[csf('knitting_company')]]['dye_fin_qty_out'] += $value[csf('dye_fin_qty_out')];
		}
		$subc_dyeing_fini_sql="SELECT a.company_id, a.product_date, a.location_id, sum(b.product_qnty) as fin_product_qnty from subcon_production_mst a, subcon_production_dtls b
		where a.id=b.mst_id and a.entry_form=292 $company_cond_sort_book $knit_lc_location_id_cond $date_cond_subc and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.company_id, a.product_date, a.location_id order by a.product_date";
		$subc_dyeing_fini_result=sql_select($subc_dyeing_fini_sql);
		foreach ($subc_dyeing_fini_result as $value)
		{
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('product_date')])]['fin_product_qnty'] += $value[csf('fin_product_qnty')];
			$rev_qty2[$value[csf('company_id')]]['fin_product_qnty'] += $value[csf('fin_product_qnty')];
		}

		//================================= Finish Fabric Roll Receive ===============================
		$finish_fab_roll_recv_sql="SELECT  a.company_id, a.receive_date,  a.location_id,
		sum(case when a.entry_form=68 and b.transaction_type=1 then b.cons_quantity else 0 end) as fin_roll_rcv_qty,
		sum(case when a.entry_form=126 and b.transaction_type=4 then b.cons_quantity else 0 end) as fin_roll_iss_rtn_qty
		from inv_receive_master a, inv_transaction b
		where a.id=b.mst_id and b.item_category=2 and a.entry_form in(68,126) and b.transaction_type in(1,4) $date_cond_knit $company_cond_sort_book $knit_lc_location_id_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.company_id, a.receive_date,  a.location_id order by a.receive_date";
		$finish_fab_roll_recv_result=sql_select($finish_fab_roll_recv_sql);
		foreach ($finish_fab_roll_recv_result as $value)
		{
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('receive_date')])]['fin_roll_rcv_qty'] += $value[csf('fin_roll_rcv_qty')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('receive_date')])]['fin_roll_iss_rtn_qty'] += $value[csf('fin_roll_iss_rtn_qty')];
			$rev_qty2[$value[csf('company_id')]]['fin_roll_rcv_qty'] += $value[csf('fin_roll_rcv_qty')];
			$rev_qty2[$value[csf('company_id')]]['fin_roll_iss_rtn_qty'] += $value[csf('fin_roll_iss_rtn_qty')];
		}
		//========================= Finish Fabric Roll Issue =====================================
		$finish_fab_roll_issue_sql="SELECT b.company_id, b.transaction_date, d.location_id,sum(case when b.transaction_type=2 and b.item_category=2 then b.cons_quantity else 0 end) as fin_roll_issue_qty
		from inv_issue_master a, inv_transaction b, lib_store_location d
		where   a.id=b.mst_id and a.store_id=d.id and b.item_category in(2) and b.transaction_type in(2) and a.entry_form=71 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_cond_sort_book $location_id_cond_y $date_cond_tran
		group by b.company_id, b.transaction_date, d.location_id order by b.transaction_date";
		$finish_fab_roll_issue_result=sql_select($finish_fab_roll_issue_sql);
		foreach ($finish_fab_roll_issue_result as $value)
		{
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('transaction_date')])]['fin_roll_issue_qty'] += $value[csf('fin_roll_issue_qty')];
			$rev_qty2[$value[csf('company_id')]]['fin_roll_issue_qty'] += $value[csf('fin_roll_issue_qty')];
		}
		//Finish Fabric Roll Trasfer Recv

		//========================= Finish Fabric Roll Issue =====================================
		$rcv_date_cond = str_replace("b.transaction_date", "a.receive_date", $date_cond_tran);
		$sql = "SELECT a.company_id,a.receive_date, b.receive_qnty from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form=126 and a.item_category=2 and a.status_active=1 and b.status_active=1 $company_cond_sort_book $location_id_cond_y $rcv_date_cond";
		// echo $sql;die();
		$res = sql_select($sql);
		foreach ($res as $value)
		{
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('receive_date')])]['fin_roll_issue_rtn_qty'] += $value[csf('receive_qnty')];
			$rev_qty2[$value[csf('company_id')]]['fin_roll_issue_rtn_qty'] += $value[csf('receive_qnty')];
		}
		//============================== AOP Issue, Receive, Cutting Fabric Receive =========================
		$aop_issue_sql="SELECT a.company_id, a.receive_date,
		sum(case when a.entry_form=63 and a.process_id=35 then b.roll_wgt else 0 end) as aop_issue_qty,
		sum(case when a.entry_form=65 and a.process_id=0 then b.roll_wgt else 0 end) as aop_recv_qty,
		sum(case when a.entry_form=72 and a.process_id=0 then b.roll_wgt else 0 end) as cutting_fab_recv
		from inv_receive_mas_batchroll a, pro_grey_batch_dtls b
		where a.id=b.mst_id and a.entry_form in(63,65,72) and a.process_id in(35,0)  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_cond_sort_book $date_cond_knit group by a.company_id, a.receive_date order by a.receive_date";
		$aop_issue_result=sql_select($aop_issue_sql);
		foreach ($aop_issue_result as $value)
		{
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('receive_date')])]['aop_issue_qty'] += $value[csf('aop_issue_qty')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('receive_date')])]['aop_recv_qty'] += $value[csf('aop_recv_qty')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('receive_date')])]['cutting_fab_recv'] += $value[csf('cutting_fab_recv')];
			$rev_qty2[$value[csf('company_id')]]['aop_issue_qty'] += $value[csf('aop_issue_qty')];
			$rev_qty2[$value[csf('company_id')]]['aop_recv_qty'] += $value[csf('aop_recv_qty')];
			$rev_qty2[$value[csf('company_id')]]['cutting_fab_recv'] += $value[csf('cutting_fab_recv')];
		}

		//==================== Total Man Power, No of Operator, No of Helper ===================================
		$prod_source_sql="SELECT a.company_id, c.pr_date, a.location_id, sum(c.man_power) as tot_man_power, sum(c.operator) as tot_operator, sum(c.helper) as tot_helper,sum(c.working_hour) as working_hour,sum(c.total_smv) as smv from prod_resource_mst a, prod_resource_dtls c
		where a.id=c.mst_id $company_cond_sort_book $knit_lc_location_id_cond $prod_sourc_date_cond
		and a.is_deleted=0 and c.is_deleted=0 group by a.company_id, c.pr_date, a.location_id order by c.pr_date";
		$prod_source_result=sql_select($prod_source_sql);
		$resource_data_arr = array();
		foreach ($prod_source_result as $value)
		{
			$resource_data_arr[$value[csf('company_id')]][strtotime($value[csf('pr_date')])]['man_power'] += $value[csf('tot_man_power')];
			$resource_data_arr[$value[csf('company_id')]][strtotime($value[csf('pr_date')])]['working_hour'] += $value[csf('working_hour')];

			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('pr_date')])]['man_power'] += $value[csf('tot_man_power')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('pr_date')])]['operator'] += $value[csf('tot_operator')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('pr_date')])]['helper'] += $value[csf('tot_helper')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('pr_date')])]['working_hour'] += $value[csf('working_hour')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('pr_date')])]['smv'] += $value[csf('smv')];
			$rev_qty2[$value[csf('company_id')]]['man_power'] += $value[csf('tot_man_power')];
			$rev_qty2[$value[csf('company_id')]]['operator'] += $value[csf('tot_operator')];
			$rev_qty2[$value[csf('company_id')]]['helper'] += $value[csf('tot_helper')];
		}
		// echo "<pre>";print_r($rev_qty3);

		//============================== garments_production ======================================
		$sqls = sql_select("SELECT d.style_ref_no, a.company_id,a.production_date,a.serving_company,a.location,a.production_type, a.embel_name, sum(b.production_qnty) as prod_quantity, a.production_source,a.po_break_down_id,a.item_number_id
		from pro_garments_production_mst a ,pro_garments_production_dtls b,wo_po_color_size_breakdown c,wo_po_details_master d
		where a.id=b.mst_id and a.production_type=b.production_type and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and  a.status_active=1 and d.id=c.job_id $date_cond $company_cond and a.is_deleted=0 $location_cond and b.status_active=1 and b.is_deleted=0
		group by d.style_ref_no, a.company_id,a.production_date,a.serving_company,a.location,a.production_type, a.embel_name, a.production_source,a.po_break_down_id,a.item_number_id order by a.production_date");
		//and a.production_type=5 and a.delivery_mst_id = 6546
		$sewing_data_array = array();
		foreach ($sqls as $value)
		{
			$lc_com_arr[$value[csf('company_id')]] = $value[csf('company_id')];
			$all_style_arr[$value[csf('style_ref_no')]] = $value[csf('style_ref_no')];

			if ($value[csf('production_type')] == 1 && $value[csf('production_source')]==1)
			{// cutting inhouse
				$rev_qty[$value[csf('serving_company')]][strtotime($value[csf('production_date')])]['cutting_inhouse'] += $value[csf('prod_quantity')];
				$rev_qty2[$value[csf('serving_company')]]['cutting_inhouse'] += $value[csf('prod_quantity')];
			}
			else if ($value[csf('production_type')] == 5 && $value[csf('production_source')]==1)
			{
				$rev_qty[$value[csf('serving_company')]][strtotime($value[csf('production_date')])]['sewing_inhouse_qty'] += $value[csf('prod_quantity')];
				$rev_qty2[$value[csf('serving_company')]]['sewing_inhouse_qty'] += $value[csf('prod_quantity')];
			}
			else if ($value[csf('production_type')] == 11 && $value[csf('production_source')]==1)
			{
				$rev_qty[$value[csf('serving_company')]][strtotime($value[csf('production_date')])]['inhouse_poly_qty'] += $value[csf('prod_quantity')];
				$rev_qty2[$value[csf('serving_company')]]['inhouse_poly_qty'] += $value[csf('prod_quantity')];
			}
			else if ($value[csf('production_type')] == 7)
			{
				$rev_qty[$value[csf('serving_company')]][strtotime($value[csf('production_date')])]['iron'] += $value[csf('prod_quantity')];
				$rev_qty2[$value[csf('serving_company')]]['iron'] += $value[csf('prod_quantity')];
			}
			else if ($value[csf('production_type')] == 8)
			{
				$rev_qty[$value[csf('serving_company')]][strtotime($value[csf('production_date')])]['finish'] += $value[csf('prod_quantity')];
				$rev_qty2[$value[csf('serving_company')]]['finish'] += $value[csf('prod_quantity')];
			}
			else if ($value[csf('production_type')] == 2)
			{
				if ($value[csf('embel_name')] == 3)
				{
					$rev_qty[$value[csf('serving_company')]][strtotime($value[csf('production_date')])]['wash_issue'] += $value[csf('prod_quantity')];
					$rev_qty2[$value[csf('serving_company')]]['wash_issue'] += $value[csf('prod_quantity')];
				}
			}
			else if ($value[csf('production_type')] == 3)
			{
				if ($value[csf('embel_name')] == 3)
				{
					$rev_qty[$value[csf('serving_company')]][strtotime($value[csf('production_date')])]['wash_rcv'] += $value[csf('prod_quantity')];
					$rev_qty2[$value[csf('serving_company')]]['wash_rcv'] += $value[csf('prod_quantity')];
				}
			}
			if ($value[csf('production_type')] == 5)
			{
				$sewing_data_array[$value[csf('serving_company')]][strtotime($value[csf('production_date')])][$value[csf('po_break_down_id')]][$value[csf('item_number_id')]] += $value[csf('prod_quantity')];
			}
		}

		/* =================================================================================/
		/									SMV Source										/
		/================================================================================= */
		$lc_com_ids = implode(",",$lc_com_arr);
		$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($lc_com_ids) and variable_list=25 and status_active=1 and is_deleted=0");
		// echo $smv_source."sdsdsdds";die;
		if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
		if($smv_source==3)
		{
			$style_nos=implode("','",$all_style_arr);
			$color_type_ids="'".implode("','",$color_type_array)."'";
			$gsdSql="SELECT a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID,a.COLOR_TYPE  from PPL_GSD_ENTRY_MST a where a.APPLICABLE_PERIOD <= $txt_date and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 and a.BULLETIN_TYPE=4  and A.STYLE_REF in('".$style_nos."') and a.APPROVED=1
			group by a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID,a.COLOR_TYPE
			 ORDER BY  a.GMTS_ITEM_ID ,a.APPLICABLE_PERIOD  DESC , a.id DESC"; //and a.BULLETIN_TYPE in(3,4)
			//echo $gsdSql; die;
			$gsdSqlResult = sql_select($gsdSql);
			//$gsdDataArr=array();
			foreach($gsdSqlResult as $rows)
			{
				// echo $rows[TOTAL_SMV]."<br>";
				foreach($style_wise_po_arr[$rows['STYLE_REF']] as $po_id)
				{
					if($item_smv_array[$po_id][$rows['COLOR_TYPE']][$rows['GMTS_ITEM_ID']]=='')
					{
						$item_smv_array[$po_id][$rows['COLOR_TYPE']][$rows['GMTS_ITEM_ID']]=$rows['TOTAL_SMV'];
					}
					if($item_smv_array2[$po_id][$rows['GMTS_ITEM_ID']]=='')
					{
						$item_smv_array2[$po_id][$rows['GMTS_ITEM_ID']]=$rows['TOTAL_SMV'];
					}
				}
			}

		}
		else
		{
			$style_nos=implode("','",$all_style_arr);
			$sql_item="SELECT b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, smv_pcs, smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,2,3) and A.STYLE_REF_NO in('".$style_nos."')"; //echo $sql_item;die;
			$resultItem=sql_select($sql_item);
			foreach($resultItem as $itemData)
			{
				if($smv_source==1)
				{
					$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs')];
				}
				else if($smv_source==2)
				{
					$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs_precost')];
				}
			}
		}

		// print_r($sewing_data_array);
		$sewing_effi_array = array();
		$com_sewing_effi_array = array();
		foreach ($sewing_data_array as $comkey => $com_data)
		{
			foreach ($com_data as $dtkey => $dt_data)
			{
				foreach ($dt_data as $pokey => $po_data)
				{
					foreach ($po_data as $itmkey => $itm_data)
					{
						$sewing_effi_array[$comkey][$dtkey] += (($itm_data*$item_smv_array[$pokey][$itmkey])/($resource_data_arr[$comkey][$dtkey]['man_power']*$resource_data_arr[$comkey][$dtkey]['working_hour']*60))*100;

						$com_sewing_effi_array[$comkey] += (($itm_data*$item_smv_array[$pokey][$itmkey])/($resource_data_arr[$comkey][$dtkey]['man_power']*$resource_data_arr[$comkey][$dtkey]['working_hour']*60))*100;
					}

				}
			}
		}
		// echo "<pre>";print_r($sewing_effi_array);

		// ==================================== emb data ======================================
		$company_cond_emb2 = str_replace("a.serving_company", "a.company_id", $company_cond_emb);
		$sqls_emb = "SELECT a.production_date,a.company_id,a.location,a.production_type, a.embel_name, sum(b.production_qnty) as prod_quantity,a.production_source
		from pro_garments_production_mst a ,pro_garments_production_dtls b,wo_po_color_size_breakdown c
		where a.id=b.mst_id and a.production_type=b.production_type and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and a.status_active=1 $date_cond $company_cond_emb2 and a.is_deleted=0 $location_cond_emb and b.status_active=1 and b.is_deleted=0 and a.production_type in(2,3)
		group by a.production_date,a.company_id,a.location,a.production_type, a.embel_name,a.production_source order by a.production_date";	//$company_cond_emb
		// echo $sqls_emb;die();
		$res = sql_select($sqls_emb);
	 	foreach($res as $value)
		{
			if ($value[csf('production_type')] == 2)
			{
				if ($value[csf('embel_name')] == 1)
				{
					$rev_qty[$value[csf('company_id')]][strtotime($value[csf('production_date')])][$value[csf('production_source')]]['print_issue'] += $value[csf('prod_quantity')];
					$rev_qty2[$value[csf('company_id')]]['print_issue'] += $value[csf('prod_quantity')];
				}

				else if ($value[csf('embel_name')] == 2) {
					$rev_qty[$value[csf('company_id')]][strtotime($value[csf('production_date')])][$value[csf('production_source')]]['emb_issue'] += $value[csf('prod_quantity')];
					$rev_qty2[$value[csf('company_id')]]['emb_issue'] += $value[csf('prod_quantity')];
				}

				else if ($value[csf('embel_name')] == 4) {
					$rev_qty[$value[csf('company_id')]][strtotime($value[csf('production_date')])][$value[csf('production_source')]]['special_issue'] += $value[csf('prod_quantity')];
					$rev_qty2[$value[csf('company_id')]]['special_issue'] += $value[csf('prod_quantity')];
				}

				else if ($value[csf('embel_name')] == 5) {
					$rev_qty[$value[csf('company_id')]][strtotime($value[csf('production_date')])]['dyeing_issue'] += $value[csf('prod_quantity')];
					$rev_qty2[$value[csf('company_id')]]['dyeing_issue'] += $value[csf('prod_quantity')];
				}
			}
			else if ($value[csf('production_type')] == 3)
			{
				if ($value[csf('embel_name')] == 1) {
					$rev_qty[$value[csf('company_id')]][strtotime($value[csf('production_date')])][$value[csf('production_source')]]['print_rcv'] += $value[csf('prod_quantity')];
					$rev_qty2[$value[csf('company_id')]]['print_rcv'] += $value[csf('prod_quantity')];
				}

				else if ($value[csf('embel_name')] == 2) {
					$rev_qty[$value[csf('company_id')]][strtotime($value[csf('production_date')])][$value[csf('production_source')]]['emb_rcv'] += $value[csf('prod_quantity')];
					$rev_qty2[$value[csf('company_id')]]['emb_rcv'] += $value[csf('prod_quantity')];
				}

				else if ($value[csf('embel_name')] == 4) {
					$rev_qty[$value[csf('company_id')]][strtotime($value[csf('production_date')])][$value[csf('production_source')]]['special_rcv'] += $value[csf('prod_quantity')];
					$rev_qty2[$value[csf('company_id')]]['special_rcv'] += $value[csf('prod_quantity')];
				}

				else if ($value[csf('embel_name')] == 5) {
					$rev_qty[$value[csf('company_id')]][strtotime($value[csf('production_date')])]['dyeing_rcv'] += $value[csf('prod_quantity')];
					$rev_qty2[$value[csf('company_id')]]['dyeing_rcv'] += $value[csf('prod_quantity')];
				}
			}
		}

		// echo "<pre>";print_r($rev_qty);die();


		//============================ Buyer Inspection ====================================
		$buyer_inspec_sql="SELECT b.company_name, a.working_company, a.inspection_date, a.working_location, sum(c.ins_qty) as ins_qty
		from pro_buyer_inspection a, pro_buyer_inspection_breakdown c, wo_po_details_master b , wo_po_break_down d
		where a.id=c.mst_id and a.po_break_down_id=d.id and d.job_no_mst=b.job_no $company_cond_order $working_location_cond $inspection_date_cond and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0
		group by b.company_name, a.working_company, a.inspection_date, a.working_location order by a.inspection_date ";
		$buyer_inspec_result=sql_select($buyer_inspec_sql);
		foreach ($buyer_inspec_result as $value)
		{
			$rev_qty[$value[csf('company_name')]][strtotime($value[csf('inspection_date')])]['ins_qty'] += $value[csf('ins_qty')];
			$rev_qty2[$value[csf('company_name')]]['ins_qty'] += $value[csf('ins_qty')];
		}

		// ============================== ex-factory data ========================================
		$gmt_shipment_sql="SELECT a.sys_number, a.company_id, a.location_id,b.invoice_no, a.delivery_date, b.po_break_down_id, a.entry_form,
		sum(case when a.entry_form<>85 then b.ex_factory_qnty else 0 end) as ship_qty,
		sum(case when a.entry_form<>85 then b.ex_factory_qnty*(c.unit_price/d.total_set_qnty) else 0 end) as ship_value,
		sum(case when a.entry_form=85 then b.ex_factory_qnty else 0 end) as ship_rtn_qty, (c.unit_price/d.total_set_qnty) as unit_price
		from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b, wo_po_break_down c,  wo_po_details_master d
		where a.id=b.delivery_mst_id and  b.po_break_down_id=c.id and c.job_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_cond_sort_book $knit_lc_location_id_cond $delivery_date_cond
		group by a.sys_number,a.company_id, a.location_id,b.invoice_no, a.delivery_date, b.po_break_down_id,c.unit_price, a.entry_form, d.total_set_qnty order by a.delivery_date";
		// echo $gmt_shipment_sql;die();
		$gmt_shipment_result=sql_select($gmt_shipment_sql);
		$invoice_id_arr = array();
		foreach ($gmt_shipment_result as $value)
		{
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('delivery_date')])]['ship_qty'] += $value[csf('ship_qty')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('delivery_date')])]['ship_value'] += $value[csf('ship_value')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('delivery_date')])]['ship_rtn_qty'] += $value[csf('ship_rtn_qty')];
			$rev_qty2[$value[csf('company_id')]]['ship_qty'] += $value[csf('ship_qty')];
			$rev_qty2[$value[csf('company_id')]]['ship_value'] += $value[csf('ship_value')];
			$rev_qty2[$value[csf('company_id')]]['ship_rtn_qty'] += $value[csf('ship_rtn_qty')];
			$invoice_id_arr[$value[csf('invoice_no')]] = $value[csf('invoice_no')];
		}

		// ==============================================
		$inv_id_cond = where_con_using_array($invoice_id_arr,0,"a.id");
		$sqlEx = sql_select("SELECT a.benificiary_id,b.ex_factory_date,a.invoice_value,a.net_invo_value from com_export_invoice_ship_mst a,pro_ex_factory_mst b where a.id=b.invoice_no and a.status_active=1 and b.status_active=1 $inv_id_cond");
		$shipment_net_val_arr = array();
		foreach ($sqlEx as $val)
		{
			$shipment_net_val_arr[$val[csf('benificiary_id')]][strtotime($val[csf('ex_factory_date')])] += $val[csf('net_invo_value')];
		}
		// echo "<pre>";print_r($shipment_net_val_arr);die();

		// Poly outbound, sewing output outbond
		$outbound="SELECT a.production_date,a.company_id,a.location,a.production_type, a.embel_name, sum(b.production_qnty) as prod_quantity, a.production_source
		from pro_garments_production_mst a ,pro_garments_production_dtls b,wo_po_color_size_breakdown c
		where a.id=b.mst_id and a.production_type=b.production_type and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and a.production_source=3 and a.production_type in(1,5,11)
		$date_cond $company_cond_sort_book $location_cond
		and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0
		group by a.production_date,a.company_id,a.location,a.production_type, a.embel_name, a.production_source order by a.production_date ";
		$sewing_output_outbond_result=sql_select($outbound);
		foreach ($sewing_output_outbond_result as $value)
		{
			if ($value[csf('production_type')] == 11) // Outbound poly entry
			{
				$rev_qty[$value[csf('company_id')]][strtotime($value[csf('production_date')])]['outbond_poly_qty'] += $value[csf('prod_quantity')];
				$rev_qty2[$value[csf('company_id')]]['outbond_poly_qty'] += $value[csf('prod_quantity')];
			}
			else if ($value[csf('production_type')] == 5) // Outbound Bundle Wise Sewing Output
			{
				$rev_qty[$value[csf('company_id')]][strtotime($value[csf('production_date')])]['sewing_outbound_qty'] += $value[csf('prod_quantity')];
				$rev_qty2[$value[csf('company_id')]]['sewing_outbound_qty'] += $value[csf('prod_quantity')];
			}
			else if ($value[csf('production_type')] == 1) // Outbound cutting qty (Cutting QC V2 page)
			{
				$rev_qty[$value[csf('company_id')]][strtotime($value[csf('production_date')])]['cutting_outbound_qty'] += $value[csf('prod_quantity')];
				$rev_qty2[$value[csf('company_id')]]['cutting_outbound_qty'] += $value[csf('prod_quantity')];
			}
		}

		// Subcon Poly Entry, subc sewing output entry, subc Cutting Entry
		$subc_data_sql="SELECT a.production_date,a.company_id,a.location_id,a.production_type, sum(a.production_qnty) as subc_production_qnty
		from subcon_gmts_prod_dtls a where a.status_active=1 and a.is_deleted=0 $date_cond $company_cond_sort_book $knit_lc_location_id_cond
		group by a.production_date,a.company_id,a.location_id,a.production_type order by a.production_date "; // $location_cond
		$subc_data_result=sql_select($subc_data_sql);
		foreach ($subc_data_result as $value)
		{
			if ($value[csf('production_type')] == 5) // subcon poly entry
			{
				$rev_qty[$value[csf('company_id')]][strtotime($value[csf('production_date')])]['subc_poly_qty'] += $value[csf('subc_production_qnty')];
				$rev_qty2[$value[csf('company_id')]]['subc_poly_qty'] += $value[csf('subc_production_qnty')];
			}
			else if ($value[csf('production_type')] == 2) // subc sewing output entry
			{
				$rev_qty[$value[csf('company_id')]][strtotime($value[csf('production_date')])]['subc_sewing_output_qty'] += $value[csf('subc_production_qnty')];
				$rev_qty2[$value[csf('company_id')]]['subc_sewing_output_qty'] += $value[csf('subc_production_qnty')];
			}
			else if ($value[csf('production_type')] == 1) // subc Cutting Entry
			{
				$rev_qty[$value[csf('company_id')]][strtotime($value[csf('production_date')])]['subc_cutting_qty'] += $value[csf('subc_production_qnty')];
				$rev_qty2[$value[csf('company_id')]]['subc_cutting_qty'] += $value[csf('subc_production_qnty')];
			}
		}
		// echo "<pre>";print_r($rev_qty);

		// ============================= Commercial Reference Closing =================================
		$ref_close_data=sql_select("SELECT a.company_id, a.closing_date, a.reference_type, a.closing_status, a.inv_pur_req_mst_id, a.mrr_system_no, sum(b.po_quantity*c.total_set_qnty) as ref_closing_qty
		from inv_reference_closing a, wo_po_break_down b, wo_po_details_master c where a.inv_pur_req_mst_id=b.id and b.job_no_mst=c.job_no and a.closing_status=1 $company_cond_sort_book $closing_date_cond
		group by a.company_id, a.closing_date, a.reference_type, a.closing_status, a.inv_pur_req_mst_id, a.mrr_system_no order by a.closing_date");
		foreach ($ref_close_data as $value)
		{
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('closing_date')])]['ref_closing_qty'] += $value[csf('ref_closing_qty')];

			$rev_qty2[$value[csf('company_id')]]['ref_closing_qty'] += $value[csf('ref_closing_qty')];
		}

		// =============== Printing Production in-house ===============
		$printing_production_sql = "SELECT a.company_id,a.sys_no, b.production_date, a.location_id, b.qcpass_qty
		FROM subcon_embel_production_mst a, subcon_embel_production_dtls b,
	    WHERE
	    a.id =b.mst_id

		and a.entry_form='222'
	    $printing_date_cond $company_cond_sort_book ";
		// echo $printing_production_sql;
		$printing_production_sql_result= sql_select($printing_production_sql);
		foreach($printing_production_sql_result as $row)
		{
          $rev_qty[$row[csf('company_id')]][strtotime($row[csf('production_date')])]['printing_production_qty'] += $row[csf('qcpass_qty')];
          $rev_qty2[$row[csf('company_id')]]['printing_production_qty'] += $row[csf('qcpass_qty')];
		}
		// echo"<pre>";
		// print_r($rev_qty);
		// =============== Printing Delivery ===============
		$printing_delivery_sql = "SELECT a.company_id,
		a.delivery_no,
		a.delivery_date,
		a.location_id,
		b.delivery_qty
        FROM subcon_delivery_mst a, subcon_delivery_dtls b
        WHERE     a.id = b.mst_id
		and a.entry_form='254'
		$printing_delivery_date_cond $company_cond_sort_book";
		$printing_delivery_sql_result= sql_select($printing_delivery_sql);
		foreach($printing_delivery_sql_result as $row)
		{
          $rev_qty[$row[csf('company_id')]][strtotime($row[csf('delivery_date')])]['printing_delivery_qty'] += $row[csf('delivery_qty')];
          $rev_qty2[$row[csf('company_id')]]['printing_delivery_qty'] += $row[csf('delivery_qty')];
		}
		// =============== Embroidery Production inhouse ===============
		$embroidery_production_sql = "SELECT a.company_id,a.sys_no, b.production_date, a.location_id, b.qcpass_qty
		FROM subcon_embel_production_mst a, subcon_embel_production_dtls b
	    WHERE
	    a.id =b.mst_id
		and a.entry_form='315'
	    $printing_date_cond $company_cond_sort_book ";
		$embroidery_production_sql_result= sql_select($embroidery_production_sql);
		foreach($embroidery_production_sql_result as $row)
		{
          $rev_qty[$row[csf('company_id')]][strtotime($row[csf('production_date')])]['embroidery_production_qty'] += $row[csf('qcpass_qty')];
          $rev_qty2[$row[csf('company_id')]]['embroidery_production_qty'] += $row[csf('qcpass_qty')];
		}
		// =============== Embroidery Delivery ===============
		$embroidery_delivery_sql = "SELECT a.company_id,
		a.delivery_no,
		a.delivery_date,
		a.location_id,
		b.delivery_qty
        FROM subcon_delivery_mst a, subcon_delivery_dtls b
        WHERE     a.id = b.mst_id
		AND a.entry_form = '325'
		$printing_delivery_date_cond $company_cond_sort_book";
		// echo $embroidery_delivery_sql;
		$embroidery_delivery_sql_result= sql_select($embroidery_delivery_sql);
		foreach($embroidery_delivery_sql_result as $row)
		{
          $rev_qty[$row[csf('company_id')]][strtotime($row[csf('delivery_date')])]['embroidery_delivery_qty'] += $row[csf('delivery_qty')];
          $rev_qty2[$row[csf('company_id')]]['embroidery_delivery_qty'] += $row[csf('delivery_qty')];
		}
		// =============== Hang Tag ===============
		$hang_tag_sql = "SELECT a.company_id,
		a.production_date,
		a.location,
		a.production_quantity,
		a.production_type
        FROM pro_garments_production_mst a
        WHERE
		a.production_type='15'
		and a.status_active=1
		and a.is_deleted=0
	    $hang_tag_date_cond $company_cond_sort_book";
		// echo $hang_tag_sql;
		$hang_tag_sql_result= sql_select($hang_tag_sql);
		foreach($hang_tag_sql_result as $row)
		{
          $rev_qty[$row[csf('company_id')]][strtotime($row[csf('production_date')])]['hang_tag_qty'] += $row[csf('production_quantity')];
          $rev_qty2[$row[csf('company_id')]]['hang_tag_qty'] += $row[csf('production_quantity')];
		}
		// echo"<pre>";
		// print_r($rev_qty);


		ob_start();
		?>
	    <style>
	        #sammary_tbl th, #sammary_tbl td {
	            padding: 0 7px;
	        }
	        fieldset{border:0;}
	    </style>

	    <fieldset style="width: 5730px; margin:20px auto;">
	        <table cellpadding="0" cellspacing="0" style="text-align: center; width: 100%">
	            <tr class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="19"><strong style="font-size:25px"><?
					$com_name = return_field_value("company_name", "lib_company", "id=" . $cbo_company_name, "company_name");
					echo $com_name; ?></strong></td>
	            </tr>

	            <tr class="form_caption" style="border:none;">
	                <td align="center" width="100%" colspan="19">
	                    <strong style="font-size:16px">Production Summary:
							<?
							if ($start_date != '') echo change_date_format($start_date) . ' To ' . change_date_format($end_date); else echo '';
							?>
	                    </strong>
	                </td>
	            </tr>

	            <tr>
	                <th colspan="19" style="text-align: right;">Date of Print:&nbsp&nbsp<? echo date('d-m-Y'); ?></th>
	            </tr>

	            <tr>
	                <th colspan="19" style="text-align: right;">Time of Print:&nbsp&nbsp<? echo date('h:i A'); ?></th>
	            </tr>
	        </table>
	        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
	            <thead>
		            <tr>
		                <th width="30">SL</th>
		                <th width="80">Date</th>
		                <th width="80">Projection Order [PCS]</th>
		                <th width="80">Confirm Order [PCS]</th>
		                <th width="80">Cancel Order [PCS]</th>
		                <th width="80">Reference Close [Pcs]</th>
		                <th width="80">Short/EFR Booking</th>
		                <th width="80">Sample Production [PCS]</th>
		                <th width="80">Sample Delivery [PCS]</th>
		                <th width="80">Grey Yarn Receive</th>
		                <th width="80">Grey Yarn Issue</th>
		                <th width="80">Dyed Yarn Receive</th>
		                <th width="80">Dyed Yarn Issue</th>
		                <th width="80">Knitting [Inbound]</th>
		                <th width="80">Knitting [Out-bound]</th>
		                <th width="80">Greige Fabric Receive</th>
		                <th width="80">Greige Fabric issue</th>
		                <th width="80">Greige Fabric Transfer Rcv</th>
		                <th width="80">Greige Fabric Transfer Issue</th>
		                <th width="80">Batch Greige Fabric Receive</th>
		                <th width="80">Batch</th>
		                <th width="80">Re-process Batch</th>
		                <th width="80">Trims Dyeing Inbound and Outbound</th>
		                <th width="80">Dyeing [Inbound]</th>
		                <th width="80">Dyeing [Outbound]</th>
		                <th width="80">Dyeing Finishing [Inbound]</th>
		                <th width="80">Dyeing Finishing [Out-bound]</th>
		                <th width="80">Finish Fabric Receive</th>
		                <th width="80">Finish Fabric Issue</th>
		                <th width="80">Finish Fabric Issue Return</th>
		                <th width="80">Finish Fabric Trasfer Recv</th>
		                <th width="80">Finish Fabric Trasfer Issue</th>
		                <th width="80">AOP Issue</th>
		                <th width="80">AOP Receive</th>
		                <th width="80">Cutting Fabric Receive</th>
		                <th width="80">Cutting [Inbound]</th>
		                <th width="80">Cutting [Out-bound]</th>
		                <th width="80">Print [Inhouse Send]</th>
		                <th width="80">Print [Out-bound Send]</th>
		                <th width="80">Total Print Send</th>
		                <th width="80">Print [Inhouse] Production</th>
		                <th width="80">Print [Inhouse Rcv]</th>
		                <th width="80">Print [Out-bound Rcv]</th>
		                <th width="80">Print Total Rcv</th>
		                <th width="80">EMB [Inhouse Send]</th>
		                <th width="80">EMB [Out-bound Send]</th>
		                <th width="80">EMB Total Send</th>
		                <th width="80">EMB [Inhouse] Production</th>
		                <th width="80">EMB [Inhouse Rcv]</th>
		                <th width="80">EMB [Out-bound Rcv]</th>
		                <th width="80">EMB Total Rcv</th>
		                <th width="80">Special Work Send</th>
		                <th width="80">Special Work RCV</th>
		                <th width="80">GMT DYeing Send</th>
		                <th width="80">GMT Dyeing RCV</th>
		                <th width="80">Wash Send</th>
		                <th width="80">Wash RCV</th>
		                <th width="80">Total Man Power</th>
		                <th width="80">No of Operator</th>
		                <th width="80">No of Helper</th>
		                <th width="80">Sewing Output [Inbound]</th>
		                <th width="80">Sewing Output [Out-bound]</th>
		                <th width="80">Sewing Efficiency %</th>
		                <th width="80">Iron</th>
		                <th width="80">Hangtag</th>
		                <th width="80">Poly</th>
		                <th width="80">Finish</th>
		                <th width="80">Inspection</th>
		                <th width="80">Shipment Qty</th>
		                <th width="80">Shipment Gross Value $</th>
		                <th width="80">Shipment Net Value $</th>
		                <th width="80">Shipment Return Qty</th>
		            </tr>
	            </thead>
				<?php
				$date_arr = array();
				$date_fri_arr = array();
				$month_year_check_arr = array();
				// echo"<pre>";
				// print_r($rev_qty);
				foreach ($rev_qty as $company => $result_row)
				{
					ksort($result_row);
					$i = 1;
					$sub_total_projec_qty=0;$sub_total_confirm_qty=0;$sub_total_cancel_order_qty=0;$sub_total_ref_closing_qty=0;$sub_total_sort_grey_qty=0;$sub_total_sample_prod_qty=0;$sub_total_sample_delivery_qty=0;$sub_total_grey_recv=0;$sub_total_grey_iss=0;$sub_total_dyed_recv=0;$sub_total_dyed_iss=0;$sub_total_knitting_qty_in=0;$sub_total_knitting_qty_out=0;$sub_total_recv_qty=0;$sub_total_issue_qty=0;$sub_total_trans_in_qty=0;$sub_total_trans_out_qty=0;$sub_total_recv_batch_qty=0;$sub_total_batch_qty=0;$sub_total_re_process_batch_qty=0;$sub_total_trims_dyeing_qty=0;$sub_total_dyeing_qty_in=0;//22
					$sub_total_dye_fini_in_qty=0;$sub_total_dye_fin_qty_out=0;$sub_total_fin_roll_rcv_qty=0;$sub_total_fin_roll_issue_qty=0;//27,28
					$sub_total_aop_issue_qty=0;$sub_total_aop_recv_qty=0;$sub_total_cutting_fab_recv=0;

					$sub_total_cutting_inhouse_qty=0;$sub_total_print_issue_qty=0;$sub_total_print_rcv_qty=0;$sub_total_emb_issue_qty=0;$sub_total_emb_rcv_qty=0;$sub_total_special_issue_qty=0;$sub_total_special_rcv_qty=0;$sub_total_dyeing_issue_qty=0;$sub_total_dyeing_rcv_qty=0;$sub_total_wash_issue_qty=0;$sub_total_wash_rcv_qty=0;$sub_total_man_power=0;$sub_total_operator=0;$sub_total_helper=0;$sub_total_sewing_inbound_qty=0;$sub_total_sewing_outbound_qty=0;$sub_total_iron_qty=0;$sub_total_finish_qty=0;$sub_total_ins_qty=0;$sub_total_ship_qty=0;$sub_total_ship_value=0;$sub_total_ship_rtn_qty=0;
					$date_count = count($locationValue);

					$month_total_projec_qty=0;$month_total_confirm_qty=0;$month_total_cancel_order_qty=0;$month_total_ref_closing_qty=0;$month_total_sort_grey_qty=0;$month_total_sample_prod_qty=0;$month_total_sample_delivery_qty=0;$month_total_grey_recv=0;$month_total_grey_iss=0;$month_total_dyed_recv=0;$month_total_dyed_iss=0;$month_total_knitting_qty_in=0;$month_total_knitting_qty_out=0;$month_total_recv_qty=0;$month_total_issue_qty=0;$month_total_trans_in_qty=0;$month_total_trans_out_qty=0;$month_total_recv_batch_qty=0;$month_total_batch_qty=0;$month_total_re_process_batch_qty=0;$month_total_trims_dyeing_qty=0;$month_total_dyeing_qty_in=0;//22
					$month_total_dye_fini_in_qty=0;$month_total_dye_fin_qty_out=0;$month_total_fin_roll_rcv_qty=0;$month_total_fin_roll_issue_qty=0;//27,28
					$month_total_aop_issue_qty=0;$month_total_aop_recv_qty=0;$month_total_cutting_fab_recv=0;$month_total_cutting_inhouse_qty=0;$month_total_print_issue_qty=0;$month_total_print_rcv_qty=0;$month_total_emb_issue_qty=0;$month_total_emb_rcv_qty=0;$month_total_special_issue_qty=0;$month_total_special_rcv_qty=0;$month_total_dyeing_issue_qty=0;$month_total_dyeing_rcv_qty=0;$month_total_wash_issue_qty=0;$month_total_wash_rcv_qty=0;$month_total_man_power=0;$month_total_operator=0;$month_total_helper=0;$month_total_sewing_inbound_qty=0;$month_total_sewing_outbound_qty=0;$month_total_iron_qty=0;$month_total_finish_qty=0;$month_total_ins_qty=0;$month_total_ship_qty=0;$month_total_ship_value=0;$month_total_ship_rtn_qty=0;

					$month_total_finish_fabric_issue_return_qty = 0;
					$month_total_print_inhound_qty =0;
					$month_total_print_outhound_qty = 0;
					$month_total_print_delivery_to_Cutting_qty = 0;
					$month_total_embroidery_inhound_qty = 0;
					$month_total_embroidery_outhound_qty = 0;
					$month_total_embroidery_delivery_to_cutting_qty = 0;
					$month_total_hangtag_qty =0;
					$month_total_shipment_net_value_qty=0;

					foreach ($result_row as $date => $qtyValue)
					{
						if (!in_array($date, $date_arr))
						{
							$date_arr[] = $date;
						}
						if ($i % 2 == 1) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

						// ----------------------- Month Total start a ----------------------
						if (!in_array(date('F, Y',$date), $month_year_check_arr2))
						{
							if ($i != 1)
							{
								?>

								<?php
								$month_total_projec_qty=0;$month_total_confirm_qty=0;$month_total_cancel_order_qty=0;$month_total_ref_closing_qty=0;$month_total_sort_grey_qty=0;$month_total_sample_prod_qty=0;$month_total_sample_delivery_qty=0;$month_total_grey_recv=0;$month_total_grey_iss=0;$month_total_dyed_recv=0;$month_total_dyed_iss=0;$month_total_knitting_qty_in=0;$month_total_knitting_qty_out=0;$month_total_recv_qty=0;$month_total_issue_qty=0;$month_total_trans_in_qty=0;$month_total_trans_out_qty=0;$month_total_recv_batch_qty=0;$month_total_batch_qty=0;$month_total_re_process_batch_qty=0;$month_total_trims_dyeing_qty=0;$month_total_dyeing_qty_in=0;//22
								$month_total_dye_fini_in_qty=0;$month_total_dye_fin_qty_out=0;$month_total_fin_roll_rcv_qty=0;$month_total_fin_roll_issue_qty=0;//27,28
								$month_total_aop_issue_qty=0;$month_total_aop_recv_qty=0;$month_total_cutting_fab_recv=0;$month_total_cutting_inhouse_qty=0;$month_total_print_issue_qty=0;$month_total_print_rcv_qty=0;$month_total_emb_issue_qty=0;$month_total_emb_rcv_qty=0;$month_total_special_issue_qty=0;$month_total_special_rcv_qty=0;$month_total_dyeing_issue_qty=0;$month_total_dyeing_rcv_qty=0;$month_total_wash_issue_qty=0;$month_total_wash_rcv_qty=0;$month_total_man_power=0;$month_total_operator=0;$month_total_helper=0;$month_total_sewing_inbound_qty=0;$month_total_sewing_outbound_qty=0;$month_total_iron_qty=0;$month_total_finish_qty=0;$month_total_ins_qty=0;$month_total_ship_qty=0;$month_total_ship_value=0;$month_total_ship_rtn_qty=0;

								$month_total_finish_fabric_issue_return_qty = 0;
								$month_total_print_inhound_qty =0;
								$month_total_print_outhound_qty = 0;
								$month_total_print_delivery_to_Cutting_qty = 0;
								$month_total_embroidery_inhound_qty = 0;
								$month_total_embroidery_outhound_qty = 0;
								$month_total_embroidery_delivery_to_cutting_qty = 0;
								$month_total_hangtag_qty =0;
								$month_total_shipment_net_value_qty=0;

							}
							$month_year_check_arr2[] = date('F, Y',$date);
						}
						// ----------------------- Month Total End a ------------------------

						// ===========Month, Year
						if (!in_array(date('F, Y',$date), $month_year_check_arr))
						{
							?>
                            <tr>
                            	<th style="background: #C2DCFF;"></th>
                                <th colspan="71" style="background: #C2DCFF; text-align: left; padding-left: 15px;">
									<?php echo date('F, Y',$date);?>
                                </th>
                            </tr>
							<?php
							$month_year_check_arr[] = date('F, Y',$date);
						}

						$knitting_qty_in=$qtyValue['knitting_qty_in']+$qtyValue['subc_knitting_qty_in'];
						$knitting_qty_out=$qtyValue['knitting_qty_out']+$qtyValue['subc_knitting_qty_out'];

						$recv_qty=$qtyValue['recv_qty_in']+$qtyValue['recv_qty_out']+$qtyValue['issue_rtn_qty'];

						$batch_qty=$qtyValue['batch_qty']+$qtyValue['subc_batch_qty'];
						$re_process_batch_qty=$qtyValue['re_process_batch_qty']+$qtyValue['subc_re_process_batch_qty'];

						$dyeing_in=$qtyValue['dyeing']+$qtyValue['subc_dyeing'];
						$dye_fini_in_qty=$qtyValue['dye_fin_qty_in']+$qtyValue['fin_product_qnty'];
						$poly_qty=$qtyValue['inhouse_poly_qty']+$qtyValue['outbond_poly_qty']+$qtyValue['subc_poly_qty'];

						$sewing_inbound_qty=$qtyValue['sewing_inhouse_qty']+$qtyValue['subc_sewing_output_qty'];
						$cutting_inhouse_qty=$qtyValue['cutting_inhouse']+$qtyValue['subc_cutting_qty'];

						$fin_roll_rcv_qty=$qtyValue['fin_roll_rcv_qty']+$qtyValue['fin_roll_iss_rtn_qty'];

						//  for efficiency
						$prod_min = ($sewing_inbound_qty+$qtyValue['sewing_outbound_qty'])*$qtyValue['smv'];
						$effi_min = $qtyValue['man_power']*$qtyValue['working_hour']*60;
						$sewing_effi = ($effi_min>0) ? ($prod_min / $effi_min)*100 : 0;

						$cutting_fab_recv = $qtyValue['fin_roll_issue_qty']-$qtyValue['fin_roll_issue_rtn_qty'];
						$shipment_net_value = $shipment_net_val_arr[$company][$date];
						// echo $company."=".$date."<br>1643479200";
						?>
                        <tr bgcolor="<? $timestamp = $date;$day_name= date("l", $timestamp );
                        	if($day_name=="Friday"){echo "crimson";} else{echo $bgcolor; }?>" style="<?if($day_name=="Friday"){echo 'color:white;';}?>">
                            <td align="center"><?  echo $i; ?></td>
                            <td align="center"><? echo date('d-m-Y',$date);//change_date_format($date); ?></td>
                            <td align="right"><? echo number_format($qtyValue['projec_qty_pcs'],2,'.',''); ?></td>
                            <td align="right"><? echo number_format($qtyValue['confirm_qty_pcs'],2,'.',''); ?></td>
                            <td align="right"><? echo number_format($qtyValue['cancel_order_qty'],2,'.',''); ?></td>
                            <td align="right"><? echo number_format($qtyValue['ref_closing_qty'],2,'.',''); ?></td>
                            <td align="right"><? echo number_format($qtyValue['sort_grey_qty'],2,'.',''); ?></td>
                            <td align="right"><? echo number_format($qtyValue['sample_prod_qty'],2,'.',''); ?></td>
                            <td align="right"><? echo number_format($qtyValue['sample_delivery_qty'],2,'.',''); ?></td>
                            <td align="right"><? echo number_format($qtyValue['grey_recv_total'],2,'.',''); ?></td>
                            <td align="right"><? echo number_format($qtyValue['grey_iss_total'],2,'.',''); ?></td>
                            <td align="right"><? echo number_format($qtyValue['dyed_recv_total'],2,'.',''); ?></td>
                            <td align="right"><? echo number_format($qtyValue['dyed_iss_total'],2,'.',''); ?></td>
                            <td align="right"><? echo number_format($knitting_qty_in, 2,'.',''); ?></td>
                            <td align="right"><? echo number_format($knitting_qty_out, 2,'.',''); ?></td>
                            <td align="right"><? echo number_format($recv_qty,2,'.',''); ?></td>
                            <td align="right"><? echo number_format($qtyValue['issue_qty'],2,'.',''); ?></td>
                            <td align="right"><? echo number_format($qtyValue['trans_in_qty'],2,'.',''); ?></td>
                            <td align="right"><? echo number_format($qtyValue['trans_out_qty'],2,'.',''); ?></td>
                            <td align="right"><? echo number_format($qtyValue['recv_batch_qty'],2,'.',''); ?></td>
                            <td align="right"><? echo number_format($batch_qty,2,'.',''); ?></td>
                            <td align="right"><? echo number_format($re_process_batch_qty,2,'.',''); ?></td>
                            <td align="right"><? echo number_format($qtyValue['trims_dyeing_qty'],2,'.',''); ?></td>
                            <td align="right"><? echo number_format($dyeing_in, 2,'.',''); ?></td>
                            <td align="right"><? //echo '22 Dyeing [Outbound]'; ?></td>
                            <td align="right"><? echo number_format($dye_fini_in_qty,2,'.',''); ?></td>
                            <td align="right"><? echo number_format($qtyValue['dye_fin_qty_out'],2,'.',''); ?></td>
                            <td align="right"><? echo number_format($fin_roll_rcv_qty,2,'.',''); ?></td>
                            <td align="right"><? echo number_format($qtyValue['fin_roll_issue_qty'],2,'.',''); ?></td>
                            <td align="right"> <? echo number_format($qtyValue['fin_roll_issue_rtn_qty'],2,'.',''); ?></td>
                            <td align="right"><? //echo '27 Finish Fabric Trasfer Recv'; ?></td>
                            <td align="right"><? //echo '28 Finish Fabric Trasfer Issue'; ?></td>
                            <td align="right"><? echo number_format($qtyValue['aop_issue_qty'],2,'.',''); ?></td>
                            <td align="right"><? echo number_format($qtyValue['aop_recv_qty'],2,'.',''); ?></td>
                            <td align="right"><? echo number_format($cutting_fab_recv,2); ?></td>
                            <td align="right" title="<? echo $qtyValue['cutting_inhouse'].'+'.$qtyValue['subc_cutting_qty']; ?>"><? echo number_format($cutting_inhouse_qty, 2,'.',''); ?></td>
                            <td align="right"><? //echo '33 CUTTING [Out-bound]'; ?></td>


                            <td align="right"><? echo number_format($qtyValue[1]['print_issue'], 2,'.',''); ?></td>
                            <td align="right"><? echo number_format($qtyValue[3]['print_issue'], 2,'.',''); ?></td>
                            <td align="right"><? echo number_format(($qtyValue[1]['print_issue']+$qtyValue[3]['print_issue']), 2,'.',''); ?></td>

                            <td align="right"><? echo number_format($qtyValue['printing_production_qty'], 2,'.',''); ?></td>

                            <td align="right"><? echo number_format($qtyValue[1]['print_rcv'], 2,'.',''); ?></td>
                            <td align="right"><? echo number_format($qtyValue[3]['print_rcv'], 2,'.',''); ?></td>
                            <td align="right"><? echo number_format(($qtyValue[1]['print_rcv']+$qtyValue[3]['print_rcv']), 2,'.',''); ?></td>


                            <td align="right"><? echo number_format($qtyValue[1]['emb_issue'], 2,'.',''); ?></td>
                            <td align="right"><? echo number_format($qtyValue[3]['emb_issue'], 2,'.',''); ?></td>
                            <td align="right"><? echo number_format(($qtyValue[1]['emb_issue']+$qtyValue[3]['emb_issue']), 2,'.',''); ?></td>

                            <td align="right"><? echo number_format($qtyValue['embroidery_production_qty'], 2,'.',''); ?></td>

                            <td align="right"><? echo number_format($qtyValue[1]['emb_rcv'], 2,'.',''); ?></td>
                            <td align="right"><? echo number_format($qtyValue[3]['emb_rcv'], 2,'.',''); ?></td>
                            <td align="right"><? echo number_format(($qtyValue[1]['emb_rcv']+$qtyValue[3]['emb_rcv']), 2,'.',''); ?></td>


                            <td align="right"><? echo number_format($qtyValue['special_issue'], 2,'.',''); ?></td>
                            <td align="right"><? echo number_format($qtyValue['special_rcv'], 2,'.',''); ?></td>
                            <td align="right"><? echo number_format($qtyValue['dyeing_issue'], 2,'.',''); ?></td>
                            <td align="right"><? echo number_format($qtyValue['dyeing_rcv'], 2,'.',''); ?></td>
                            <td align="right"><? echo number_format($qtyValue['wash_issue'], 2,'.',''); ?></td>
                            <td align="right"><? echo number_format($qtyValue['wash_rcv'], 2,'.',''); ?></td>

                            <td align="right"><? echo number_format($qtyValue['man_power'], 2,'.',''); ?></td>
                            <td align="right"><? echo number_format($qtyValue['operator'], 2,'.',''); ?></td>
                            <td align="right"><? echo number_format($qtyValue['helper'], 2,'.',''); ?></td>
                            <td align="right"><? echo number_format($sewing_inbound_qty, 2,'.',''); ?></td>
                            <td align="right"><? echo number_format($qtyValue['sewing_outbound_qty'], 2,'.',''); ?></td>
                            <td align="right" title="(sewing out*smv)/(manpower*working hour*60)*100"><? echo number_format($sewing_effi_array[$company][$date],2); ?></td>
                            <td align="right"><? echo number_format($qtyValue['iron'], 2,'.',''); ?></td>
                            <td align="right"> <? echo number_format($hangtag=$qtyValue['hang_tag_qty'], 2,'.',''); ?></td>
                            <td align="right"><? echo number_format($poly_qty, 2,'.',''); ?></td>
                            <td align="right"><? echo number_format($qtyValue['finish'], 2,'.',''); ?></td>
                            <td align="right"><? echo number_format($qtyValue['ins_qty'], 2,'.',''); ?></td>
                            <td align="right"><? echo number_format($qtyValue['ship_qty'], 2,'.',''); ?></td>
                            <td align="right"><? echo number_format($qtyValue['ship_value'], 2,'.',''); ?></td>
                            <td align="right"><? echo number_format($shipment_net_value, 2); ?></td>
                            <td align="right"><? echo number_format($qtyValue['ship_rtn_qty'], 2,'.',''); ?></td>
                        </tr>
						<?
						$i++;
						$month_total_projec_qty += $qtyValue['projec_qty_pcs'];
						$month_total_confirm_qty += $qtyValue['confirm_qty_pcs'];
						$month_total_cancel_order_qty += $qtyValue['cancel_order_qty'];
						$month_total_ref_closing_qty += $qtyValue['ref_closing_qty'];
						$month_total_sort_grey_qty += $qtyValue['sort_grey_qty'];
						$month_total_sample_prod_qty += $qtyValue['sample_prod_qty'];
						$month_total_sample_delivery_qty += $qtyValue['sample_delivery_qty'];
						$month_total_grey_recv += $qtyValue['grey_recv_total'];
						$month_total_grey_iss += $qtyValue['grey_iss_total'];
						$month_total_dyed_recv += $qtyValue['dyed_recv_total'];
						$month_total_dyed_iss += $qtyValue['dyed_iss_total'];
						$month_total_knitting_qty_in += $knitting_qty_in;
						$month_total_knitting_qty_out += $knitting_qty_out;
						$month_total_recv_qty += $recv_qty;
						$month_total_issue_qty += $qtyValue['issue_qty'];
						$month_total_trans_in_qty += $qtyValue['trans_in_qty'];
						$month_total_trans_out_qty += $qtyValue['trans_out_qty'];
						$month_total_recv_batch_qty += $qtyValue['recv_batch_qty'];
						$month_total_batch_qty += $batch_qty;
						$month_total_re_process_batch_qty += $re_process_batch_qty;
						$month_total_trims_dyeing_qty += $qtyValue['trims_dyeing_qty'];
						$month_total_dyeing_qty_in += $dyeing_in;
						//22 Dyeing [Outbound]
						$month_total_dye_fini_in_qty += $dye_fini_in_qty;
						$month_total_dye_fin_qty_out += $qtyValue['dye_fin_qty_out'];
						$month_total_fin_roll_rcv_qty += $fin_roll_rcv_qty;
						$month_total_fin_roll_issue_qty += $qtyValue['fin_roll_issue_qty'];
						$month_total_finish_fabric_issue_return_qty += $finish_fabric_issue_return;
						//27
						//28
						$month_total_aop_issue_qty += $qtyValue['aop_issue_qty'];
						$month_total_aop_recv_qty += $qtyValue['aop_recv_qty'];
						$month_total_cutting_fab_recv += $qtyValue['cutting_fab_recv'];
						$month_total_cutting_inhouse_qty += $cutting_inhouse_qty;
						$month_total_print_issue_in_qty += $qtyValue[1]['print_issue'];
						$month_total_print_issue_out_qty += $qtyValue[3]['print_issue'];
						$month_total_print_issue_qty += $qtyValue[1]['print_issue']+$qtyValue[3]['print_issue'];

						$month_total_print_rcv_qty += $qtyValue['printing_production_qty'];
						$month_total_print_rcv_qty += $qtyValue['print_rcv'];
						$month_total_print_rcv_qty += $qtyValue['print_rcv'];
						$month_total_print_rcv_qty += $qtyValue['print_rcv'];
						$month_total_emb_issue_qty += $qtyValue['emb_issue'];
						$month_total_emb_rcv_qty += $qtyValue['emb_rcv'];

						$month_total_print_inhound_qty += $print_inhound;
						$month_total_print_outhound_qty += $print_outhound;
						$month_total_print_delivery_to_Cutting_qty += $print_delivery_to_Cutting;
						$month_total_embroidery_inhound_qty += $embroidery_inhound;
						$month_total_embroidery_outhound_qty += $embroidery_outhound;
						$month_total_embroidery_delivery_to_cutting_qty += $embroidery_delivery_to_cutting;

						$month_total_special_issue_qty += $qtyValue['special_issue'];
						$month_total_special_rcv_qty += $qtyValue['special_rcv'];
						$month_total_dyeing_issue_qty += $qtyValue['dyeing_issue'];
						$month_total_dyeing_rcv_qty += $qtyValue['dyeing_rcv'];
						$month_total_wash_issue_qty += $qtyValue['wash_issue'];
						$month_total_wash_rcv_qty += $qtyValue['wash_rcv'];
						$month_total_man_power += $qtyValue['man_power'];
						$month_total_operator += $qtyValue['operator'];
						$month_total_helper += $qtyValue['helper'];
						$month_total_sewing_inbound_qty += $sewing_inbound_qty;
						$month_total_sewing_outbound_qty += $qtyValue['sewing_outbound_qty'];
						$month_total_iron_qty += $qtyValue['iron'];
						$month_total_hangtag_qty += $hangtag;
						$month_total_poly_qty += $poly_qty;
						$month_total_finish_qty += $qtyValue['finish'];
						$month_total_ins_qty += $qtyValue['ins_qty'];
						$month_total_ship_qty += $qtyValue['ship_qty'];
						$month_total_ship_value += $qtyValue['ship_value'];
						$month_total_shipment_net_value_qty += $shipment_net_value;
						$month_total_ship_rtn_qty += $qtyValue['ship_rtn_qty'];

						// ==============

						$sub_total_projec_qty += $qtyValue['projec_qty_pcs'];
						$sub_total_confirm_qty += $qtyValue['confirm_qty_pcs'];
						$sub_total_cancel_order_qty += $qtyValue['cancel_order_qty'];
						$sub_total_ref_closing_qty += $qtyValue['ref_closing_qty'];
						$sub_total_sort_grey_qty += $qtyValue['sort_grey_qty'];
						$sub_total_sample_prod_qty += $qtyValue['sample_prod_qty'];
						$sub_total_sample_delivery_qty += $qtyValue['sample_delivery_qty'];
						$sub_total_grey_recv += $qtyValue['grey_recv_total'];
						$sub_total_grey_iss += $qtyValue['grey_iss_total'];
						$sub_total_dyed_recv += $qtyValue['dyed_recv_total'];
						$sub_total_dyed_iss += $qtyValue['dyed_iss_total'];
						$sub_total_knitting_qty_in += $knitting_qty_in;
						$sub_total_knitting_qty_out += $knitting_qty_out;
						$sub_total_recv_qty += $recv_qty;
						$sub_total_issue_qty += $qtyValue['issue_qty'];
						$sub_total_trans_in_qty += $qtyValue['trans_in_qty'];
						$sub_total_trans_out_qty += $qtyValue['trans_out_qty'];
						$sub_total_recv_batch_qty += $qtyValue['recv_batch_qty'];
						$sub_total_batch_qty += $batch_qty;
						$sub_total_re_process_batch_qty += $re_process_batch_qty;
						$sub_total_trims_dyeing_qty += $qtyValue['trims_dyeing_qty'];
						$sub_total_dyeing_qty_in += $dyeing_in;
						//22 Dyeing [Outbound]
						$sub_total_dye_fini_in_qty += $dye_fini_in_qty;
						$sub_total_dye_fin_qty_out += $qtyValue['dye_fin_qty_out'];
						$sub_total_fin_roll_rcv_qty += $fin_roll_rcv_qty;
						$sub_total_fin_roll_issue_qty += $qtyValue['fin_roll_issue_qty'];
						$sub_total_finish_fabric_issue_return_qty += $finish_fabric_issue_return;
						//27
						//28
						$sub_total_aop_issue_qty += $qtyValue['aop_issue_qty'];
						$sub_total_aop_recv_qty += $qtyValue['aop_recv_qty'];
						$sub_total_cutting_fab_recv += $qtyValue['cutting_fab_recv'];
						$sub_total_cutting_inhouse_qty += $cutting_inhouse_qty;
						$sub_total_print_issue_qty += $qtyValue['print_issue'];
						$sub_total_print_rcv_qty += $qtyValue['print_rcv'];
						$sub_total_emb_issue_qty += $qtyValue['emb_issue'];
						$sub_total_emb_rcv_qty += $qtyValue['emb_rcv'];

						$sub_total_print_inhound_qty += $print_inhound;
						$sub_total_print_outhound_qty += $print_outhound;
						$sub_total_print_delivery_to_Cutting_qty += $print_delivery_to_Cutting;
						$sub_total_embroidery_inhound_qty += $embroidery_inhound;
						$sub_total_embroidery_outhound_qty += $embroidery_outhound;
						$sub_total_embroidery_delivery_to_cutting_qty += $embroidery_delivery_to_cutting;

						$sub_total_special_issue_qty += $qtyValue['special_issue'];
						$sub_total_special_rcv_qty += $qtyValue['special_rcv'];
						$sub_total_dyeing_issue_qty += $qtyValue['dyeing_issue'];
						$sub_total_dyeing_rcv_qty += $qtyValue['dyeing_rcv'];
						$sub_total_wash_issue_qty += $qtyValue['wash_issue'];
						$sub_total_wash_rcv_qty += $qtyValue['wash_rcv'];
						$sub_total_man_power += $qtyValue['man_power'];
						$sub_total_operator += $qtyValue['operator'];
						$sub_total_helper += $qtyValue['helper'];
						$sub_total_sewing_inbound_qty += $sewing_inbound_qty;
						$sub_total_sewing_outbound_qty += $qtyValue['sewing_outbound_qty'];
						$sub_total_iron_qty += $qtyValue['iron'];
						$sub_total_hangtag_qty += $hangtag;
						$sub_total_poly_qty += $poly_qty;
						$sub_total_finish_qty += $qtyValue['finish'];
						$sub_total_ins_qty += $qtyValue['ins_qty'];
						$sub_total_ship_qty += $qtyValue['ship_qty'];
						$sub_total_ship_value += $qtyValue['ship_value'];
						$sub_total_shipment_net_value += $shipment_net_value;
						$sub_total_ship_rtn_qty += $qtyValue['ship_rtn_qty'];

						$total_projec_qty += $qtyValue['projec_qty_pcs'];
						$total_confirm_qty += $qtyValue['confirm_qty_pcs'];
						$total_cancel_order_qty += $qtyValue['cancel_order_qty'];
						$total_ref_closing_qty += $qtyValue['ref_closing_qty'];
						$total_sort_grey_qty += $qtyValue['sort_grey_qty'];
						$total_sample_prod_qty += $qtyValue['sample_prod_qty'];
						$total_sample_delivery_qty += $qtyValue['sample_delivery_qty'];
						$total_grey_recv += $qtyValue['grey_recv_total'];
						$total_grey_iss += $qtyValue['grey_iss_total'];
						$total_dyed_recv += $qtyValue['dyed_recv_total'];
						$total_dyed_iss += $qtyValue['dyed_iss_total'];
						$total_knitting_qty_in += $knitting_qty_in;
						$total_knitting_qty_out += $knitting_qty_out;
						$total_recv_qty += $recv_qty;
						$total_issue_qty += $qtyValue['issue_qty'];
						$total_trans_in_qty += $qtyValue['trans_in_qty'];
						$total_trans_out_qty += $qtyValue['trans_out_qty'];
						$total_recv_batch_qty += $qtyValue['recv_batch_qty'];
						$total_batch_qty += $batch_qty;
						$total_re_process_batch_qty += $re_process_batch_qty;
						$total_trims_dyeing_qty += $qtyValue['trims_dyeing_qty'];
						$total_dyeing_qty_in += $dyeing_in;
						//22 Dyeing [Outbound]
						$total_dye_fini_in_qty += $dye_fini_in_qty;
						$total_dye_fin_qty_out += $qtyValue['dye_fin_qty_out'];
						$total_fin_roll_rcv_qty += $fin_roll_rcv_qty;
						$total_fin_roll_issue_qty += $qtyValue['fin_roll_issue_qty'];
						$total_finish_fabric_issue_return_qty += $finish_fabric_issue_return;
						//27
						//28
						$total_aop_issue_qty += $qtyValue['aop_issue_qty'];
						$total_aop_recv_qty += $qtyValue['aop_recv_qty'];
						$total_cutting_fab_recv += $qtyValue['cutting_fab_recv'];
						$total_cutting_inhouse_qty += $cutting_inhouse_qty;
						$total_print_issue_qty += $qtyValue['print_issue'];
						$total_print_rcv_qty += $qtyValue['print_rcv'];
						$total_emb_issue_qty += $qtyValue['emb_issue'];
						$total_emb_rcv_qty += $qtyValue['emb_rcv'];

						$total_print_inhound_qty += $print_inhound;
						$total_print_outhound_qty += $print_outhound;
						$total_print_delivery_to_Cutting_qty += $print_delivery_to_Cutting;
						$total_embroidery_inhound_qty += $embroidery_inhound;
						$total_embroidery_outhound_qty += $embroidery_outhound;
						$total_embroidery_delivery_to_cutting_qty += $embroidery_delivery_to_cutting;

						$total_special_issue_qty += $qtyValue['special_issue'];
						$total_special_rcv_qty += $qtyValue['special_rcv'];
						$total_dyeing_issue_qty += $qtyValue['dyeing_issue'];
						$total_dyeing_rcv_qty += $qtyValue['dyeing_rcv'];
						$total_wash_issue_qty += $qtyValue['wash_issue'];
						$total_wash_rcv_qty += $qtyValue['wash_rcv'];
						$total_man_power += $qtyValue['man_power'];
						$total_operator += $qtyValue['operator'];
						$total_helper += $qtyValue['helper'];
						$total_sewing_inhouse_qty += $sewing_inbound_qty;
						$total_sewing_outbound_qty += $qtyValue['sewing_outbound_qty'];;
						$total_iron_qty += $qtyValue['iron'];
						$total_hangtag_qty += $hangtag;
						$total_poly_qty += $poly_qty;
						$total_finish_qty += $qtyValue['finish'];
						$total_ins_qty += $qtyValue['ins_qty']; // 54
						$total_ship_qty += $qtyValue['ship_qty'];
						$total_ship_value += $qtyValue['ship_value'];
						$total_shipment_net_value += $shipment_net_value;
						$total_ship_rtn_qty += $qtyValue['ship_rtn_qty'];

						$getdate = date('D', $time = $date);
						if ($getdate != 'Fri')
						{
							if (!in_array($date, $date_fri_arr)) {
								$date_fri_arr[] = $date;
							}
							$j++;
							$totl_projec_qty += $qtyValue['projec_qty_pcs'];
							$totl_confirm_qty += $qtyValue['confirm_qty_pcs'];
							$totl_cancel_order_qty += $qtyValue['cancel_order_qty'];
							$totl_ref_closing_qty += $qtyValue['ref_closing_qty'];
							$totl_sort_grey_qty += $qtyValue['sort_grey_qty'];
							$totl_sample_prod_qty += $qtyValue['sample_prod_qty'];
							$totl_sample_delivery_qty += $qtyValue['sample_delivery_qty'];
							$totl_grey_recv += $qtyValue['grey_recv_total'];
							$totl_grey_iss += $qtyValue['grey_iss_total'];
							$totl_dyed_recv += $qtyValue['dyed_recv_total'];
							$totl_dyed_iss += $qtyValue['dyed_iss_total'];
							$totl_knit_qty_in += $knitting_qty_in;
							$totl_knit_qty_out += $knitting_qty_out;
							$totl_recv_qty += $recv_qty;
							$totl_issue_qty += $qtyValue['issue_qty'];
							$totl_trans_in_qty += $qtyValue['trans_in_qty'];
							$totl_trans_out_qty += $qtyValue['trans_out_qty'];
							$totl_recv_batch_qty += $qtyValue['recv_batch_qty'];
							$totl_batch_qty += $batch_qty;
							$totl_re_process_batch_qty += $re_process_batch_qty;
							$totl_trims_dyei_qty += $qtyValue['trims_dyeing_qty'];
							$totl_dyei_qty_in += $dyeing_in;
							// 22 Dyeing [Outbound]
							$totl_dye_fini_in_qty += $dye_fini_in_qty;
							$totl_dye_fin_qty_out += $qtyValue['dye_fin_qty_out'];
							$totl_fin_roll_rcv_qty += $fin_roll_rcv_qty;
							$totl_fin_roll_issue_qty += $qtyValue['fin_roll_issue_qty'];
							$totl_finish_fabric_issue_return_qty += $finish_fabric_issue_return;
							//27
							//28
							$totl_aop_issue_qty += $qtyValue['aop_issue_qty'];
							$totl_aop_recv_qty += $qtyValue['aop_recv_qty'];
							$totl_cutting_fab_recv += $qtyValue['cutting_fab_recv'];
							$totl_cutt_inhouse_qty += $cutting_inhouse_qty;
							$totl_pnt_issue_qty += $qtyValue['print_issue'];
							$totl_prnt_rcv_qty += $qtyValue['print_rcv'];
							$totl_emb_issue_qty += $qtyValue['emb_issue'];
							$totl_emb_rcv_qty += $qtyValue['emb_rcv'];

							$totl_print_inhound_qty += $print_inhound;
							$totl_print_outhound_qty += $print_outhound;
							$totl_print_delivery_to_Cutting_qty += $print_delivery_to_Cutting;
							$totl_embroidery_inhound_qty += $embroidery_inhound;
							$totl_embroidery_outhound_qty += $embroidery_outhound;
							$totl_embroidery_delivery_to_cutting_qty += $embroidery_delivery_to_cutting;

							$totl_special_issue_qty += $qtyValue['special_issue'];
							$totl_special_rcv_qty += $qtyValue['special_rcv'];
							$totl_dyeing_issue_qty += $qtyValue['dyeing_issue'];
							$totl_dyeing_rcv_qty += $qtyValue['dyeing_rcv'];
							$totl_wash_issue_qty += $qtyValue['wash_issue'];
							$totl_wash_rcv_qty += $qtyValue['wash_rcv'];
							$totl_man_power += $qtyValue['man_power'];
							$totl_operator += $qtyValue['operator'];
							$totl_helper += $qtyValue['helper'];
							$totl_sewi_inbound_qty += $sewing_inbound_qty;
							$totl_sewi_outbound_qty += $qtyValue['sewing_outbound_qty'];;
							$totl_iro_qty += $qtyValue['iron'];
							$totl_hangtag_qty += $hangtag;
							$totl_poly_qty += $poly_qty;
							$totl_fini_qty += $qtyValue['finish'];
							$totl_ins_qty += $qtyValue['ins_qty'];
							$totl_ship_qty += $qtyValue['ship_qty'];
							$totl_ship_value += $qtyValue['ship_value'];
							$totl_shipment_net_value += $shipment_net_value;
							$totl_ship_rtn_qty += $qtyValue['ship_rtn_qty'];
						}
					}
					?>
					<!-- Month Total start-->
                    <tr>
                        <td align="right" colspan="2"><b>Monthly Total</b></td>
                        <td align="right"><b><?php echo number_format($month_total_projec_qty,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_confirm_qty,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_cancel_order_qty,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_ref_closing_qty,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_sort_grey_qty,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_sample_prod_qty,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_sample_delivery_qty,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_grey_recv,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_grey_iss,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_dyed_recv,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_dyed_iss,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_knitting_qty_in, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_knitting_qty_out, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_recv_qty,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_issue_qty,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_trans_in_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_trans_out_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_recv_batch_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_batch_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_re_process_batch_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_trims_dyeing_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_dyeing_qty_in, 2,'.',''); ?></b>
                        </td>
                        <td align="right"><b><?php //echo '22 Dyeing [Outbound]'; ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_dye_fini_in_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_dye_fin_qty_out, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_fin_roll_rcv_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_fin_roll_issue_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_finish_fabric_issue_return_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php //echo '27 Finish Fabric Trasfer Recv'; ?></b></td>
                        <td align="right"><b><?php //echo '28 Finish Fabric Trasfer Issue'; ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_aop_issue_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_aop_recv_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_cutting_fab_recv, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_cutting_inhouse_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php //echo '33 CUTTING [Out-bound]'; ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_print_issue_qty,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_print_issue_qty,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_print_issue_qty,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_print_issue_qty,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_print_rcv_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_print_rcv_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_print_rcv_qty, 2,'.',''); ?></b></td>

                        <td align="right"><b><?php echo number_format($month_total_emb_issue_qty,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_emb_issue_qty,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_emb_issue_qty,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_emb_issue_qty,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_emb_rcv_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_emb_rcv_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_emb_rcv_qty, 2,'.',''); ?></b></td>

                        <td align="right"><b><?php echo number_format($month_total_special_issue_qty,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_special_rcv_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_dyeing_issue_qty,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_dyeing_rcv_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_wash_issue_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_wash_rcv_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_man_power, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_operator, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_helper, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_sewing_inbound_qty, 2,'.',''); ?></b>
                        </td>
                        <td align="right"><b><?php echo number_format($month_total_sewing_outbound_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php //echo '49 Effeciency'; ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_iron_qty, 2,'.',''); ?></b>
                        </td>
                        <td align="right"><b><?php echo number_format($month_total_hangtag_qty, 2,'.',''); ?></b>
                        </td>
                        <td align="right"><b><?php echo number_format($month_total_poly_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_finish_qty, 2,'.',''); ?></b>
                        </td>
                        <td align="right"><b><? echo number_format($month_total_ins_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><? echo number_format($month_total_ship_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><? echo number_format($month_total_ship_value, 2,'.',''); ?></b></td>
                        <td align="right"><b><? echo number_format($month_total_shipment_net_value_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><? echo number_format($month_total_ship_rtn_qty, 2,'.',''); ?></b></td>
                    </tr>
                    <!-- Month total end -->
                    <tr>
                        <td colspan="72"></td>
                    </tr>
					<tr>
                        <td align="right" colspan="2"><b>Sub Total</b></td>
                        <td align="right"><b><?php echo number_format($sub_total_projec_qty,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_confirm_qty,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_cancel_order_qty,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_ref_closing_qty,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_sort_grey_qty,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_sample_prod_qty,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_sample_delivery_qty,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_grey_recv,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_grey_iss,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_dyed_recv,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_dyed_iss,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_knitting_qty_in, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_knitting_qty_out, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_recv_qty,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_issue_qty,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_trans_in_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_trans_out_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_recv_batch_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_batch_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_re_process_batch_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_trims_dyeing_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_dyeing_qty_in, 2,'.',''); ?></b>
                        </td>
                        <td align="right"><b><?php //echo '22 Dyeing [Outbound]'; ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_dye_fini_in_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_dye_fin_qty_out, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_fin_roll_rcv_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_fin_roll_issue_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_finish_fabric_issue_return_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php //echo '27 Finish Fabric Trasfer Recv'; ?></b></td>
                        <td align="right"><b><?php //echo '28 Finish Fabric Trasfer Issue'; ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_aop_issue_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_aop_recv_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_cutting_fab_recv, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_cutting_inhouse_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php //echo '33 CUTTING [Out-bound]'; ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_print_issue_qty,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_print_issue_qty,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_print_issue_qty,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_print_issue_qty,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_print_rcv_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_print_rcv_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_print_rcv_qty, 2,'.',''); ?></b></td>

                        <td align="right"><b><?php echo number_format($sub_total_emb_issue_qty,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_emb_issue_qty,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_emb_issue_qty,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_emb_issue_qty,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_emb_rcv_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_emb_rcv_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_emb_rcv_qty, 2,'.',''); ?></b></td>

                        <td align="right"><b><?php echo number_format($sub_total_special_issue_qty,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_special_rcv_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_dyeing_issue_qty,2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_dyeing_rcv_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_wash_issue_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_wash_rcv_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_man_power, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_operator, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_helper, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_sewing_inbound_qty, 2,'.',''); ?></b>
                        </td>
                        <td align="right"><b><?php echo number_format($sub_total_sewing_outbound_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php //echo '49 Effeciency'; ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_iron_qty, 2,'.',''); ?></b>
                        </td>
                        <td align="right"><b><?php echo number_format($sub_total_hangtag_qty, 2,'.',''); ?></b>
                        </td>
                        <td align="right"><b><?php echo number_format($sub_total_poly_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><?php echo number_format($sub_total_finish_qty, 2,'.',''); ?></b>
                        </td>
                        <td align="right"><b><? echo number_format($sub_total_ins_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><? echo number_format($sub_total_ship_qty, 2,'.',''); ?></b></td>
                        <td align="right"><b><? echo number_format($sub_total_ship_value, 2,'.',''); ?></b></td>
                        <td align="right"><b><? echo number_format($sub_total_shipment_net_value, 2,'.',''); ?></b></td>
                        <td align="right"><b><? echo number_format($sub_total_ship_rtn_qty, 2,'.',''); ?></b></td>
                    </tr>
					<?php
				}
				$date_count = count($date_arr);
				$date_count2 = count($date_fri_arr);
				?>
	            <tr>
	                <td colspan="72" style="padding: 1px;"></td>
	            </tr>
	            <tr>
	                <td align="left" colspan="2"><b>Grand Total</b></td>
                    <td align="right"><b><?php echo number_format($total_projec_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_confirm_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_cancel_order_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_ref_closing_qty, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_sort_grey_qty, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_sample_prod_qty, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_sample_delivery_qty, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_grey_recv, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_grey_iss, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_dyed_recv, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_dyed_iss, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_knitting_qty_in, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_knitting_qty_out, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_recv_qty,2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_issue_qty,2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_trans_in_qty, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_trans_out_qty, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_recv_batch_qty, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_batch_qty, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_re_process_batch_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_trims_dyeing_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_dyeing_qty_in, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php //echo '22 Dyeing [Outbound]'; ?></b></td>
                    <td align="right"><b><?php echo number_format($total_dye_fini_in_qty, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_dye_fin_qty_out, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_fin_roll_rcv_qty, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_fin_roll_issue_qty, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_finish_fabric_issue_return_qty, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php //echo '27 Finish Fabric Trasfer Recv'; ?></b></td>
                    <td align="right"><b><?php //echo '28 Finish Fabric Trasfer Issue'; ?></b></td>
                    <td align="right"><b><?php echo number_format($total_aop_issue_qty, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_aop_recv_qty, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_cutting_fab_recv, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_cutting_inhouse_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php //echo '33 CUTTING [Out-bound]'; ?></b></td>
	                <td align="right"><b><?php echo number_format($total_print_issue_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_print_issue_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_print_issue_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_print_issue_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_print_rcv_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_print_rcv_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_print_rcv_qty, 2,'.',''); ?></b></td>

	                <td align="right"><b><?php echo number_format($total_emb_issue_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_emb_issue_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_emb_issue_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_emb_issue_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_emb_rcv_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_emb_rcv_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_emb_rcv_qty, 2,'.',''); ?></b></td>

	                <td align="right"><b><?php echo number_format($total_special_issue_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_special_rcv_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_dyeing_issue_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_dyeing_rcv_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_wash_issue_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_wash_rcv_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_man_power, 2,'.','');; ?></b></td>
                    <td align="right"><b><?php echo number_format($total_operator, 2,'.','');; ?></b></td>
                    <td align="right"><b><?php echo number_format($total_helper, 2,'.','');; ?></b></td>
	                <td align="right"><b><?php echo number_format($total_sewing_inhouse_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_sewing_outbound_qty, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php //echo '49 Effeciency'; ?></b></td>
	                <td align="right"><b><?php echo number_format($total_iron_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_hangtag_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_poly_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_finish_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><? echo number_format($total_ins_qty, 2,'.',''); ?></b></td>
                    <td align="right"><b><? echo number_format($total_ship_qty, 2,'.',''); ?></b></td>
                    <td align="right"><b><? echo number_format($total_ship_value, 2,'.',''); ?></b></td>
                    <td align="right"><b><? echo number_format($total_shipment_net_value, 2,'.',''); ?></b></td>
                    <td align="right"><b><? echo number_format($total_ship_rtn_qty, 2,'.',''); ?></b></td>
	            </tr>
	            <tr>
	                <td align="left" colspan="2"><b>Avarage</b></td>
                    <td align="right"><b><?php echo number_format($total_projec_qty / $date_count, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_confirm_qty / $date_count, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_cancel_order_qty / $date_count, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_ref_closing_qty / $date_count, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_sort_grey_qty / $date_count, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_sample_prod_qty / $date_count, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_sample_delivery_qty / $date_count, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_grey_recv / $date_count, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_grey_iss / $date_count,2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_dyed_recv / $date_count, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_dyed_iss / $date_count, 2,'.',''); ?></b></td>
	                <td width="100" align="right">
	                    <b><?php echo number_format($total_knitting_qty_in / $date_count, 2,'.',''); ?></b>
	                </td>
	                <td align="right"><b><?php echo number_format($total_knitting_qty_out / $date_count, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_recv_qty / $date_count,2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_issue_qty / $date_count,2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_trans_in_qty / $date_count, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_trans_out_qty / $date_count, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_recv_batch_qty / $date_count, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_batch_qty / $date_count, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_re_process_batch_qty / $date_count, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_trims_dyeing_qty / $date_count, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_dyeing_qty_in / $date_count, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php //echo '22 Dyeing [Outbound]'; ?></b></td>
                    <td align="right"><b><?php echo number_format($total_dye_fini_in_qty / $date_count, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_dye_fin_qty_out / $date_count, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_fin_roll_rcv_qty / $date_count, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_fin_roll_issue_qty / $date_count, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_finish_fabric_issue_return_qty / $date_count, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php //echo '27 Finish Fabric Trasfer Recv'; ?></b></td>
                    <td align="right"><b><?php //echo '28 Finish Fabric Trasfer Issue'; ?></b></td>
                    <td align="right"><b><?php echo number_format($total_aop_issue_qty / $date_count, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_aop_recv_qty / $date_count, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_cutting_fab_recv / $date_count, 2,'.','');; ?></b></td>
	                <td align="right"><b><?php echo number_format($total_cutting_inhouse_qty / $date_count, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php //echo '33 CUTTING [Out-bound]'; ?></b></td>
	                <td align="right">
	                    <b><?php echo number_format($total_print_issue_qty / $date_count, 2,'.',''); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_print_issue_qty / $date_count, 2,'.',''); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_print_issue_qty / $date_count, 2,'.',''); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_print_issue_qty / $date_count, 2,'.',''); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_print_rcv_qty / $date_count, 2,'.',''); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_print_rcv_qty / $date_count, 2,'.',''); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_print_rcv_qty / $date_count, 2,'.',''); ?></b>
	                </td>

	                <td align="right">
	                    <b><?php echo number_format($total_emb_issue_qty / $date_count, 2,'.',''); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_emb_issue_qty / $date_count, 2,'.',''); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_emb_issue_qty / $date_count, 2,'.',''); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_emb_issue_qty / $date_count, 2,'.',''); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_emb_rcv_qty / $date_count, 2,'.',''); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_emb_rcv_qty / $date_count, 2,'.',''); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_emb_rcv_qty / $date_count, 2,'.',''); ?></b>
	                </td>

	                <td align="right">
	                    <b><?php echo number_format($total_special_issue_qty / $date_count, 2,'.',''); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_special_rcv_qty / $date_count, 2,'.',''); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_dyeing_issue_qty / $date_count, 2,'.',''); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_dyeing_rcv_qty / $date_count, 2,'.',''); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_wash_issue_qty / $date_count, 2,'.',''); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_wash_rcv_qty / $date_count, 2,'.',''); ?></b>
	                </td>
	                <td align="right"><b><?php echo number_format($total_man_power / $date_count, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_operator / $date_count, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_helper / $date_count, 2,'.',''); ?></b></td>
	                <td align="right">
	                    <b><?php echo number_format($total_sewing_inhouse_qty / $date_count, 2,'.',''); ?></b>
	                </td>
	                <td align="right"><b><?php echo number_format($total_sewing_outbound_qty / $date_count, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php //echo '49 Effeciency'; ?></b></td>
	                <td align="right">
	                    <b><?php echo number_format($total_iron_qty / $date_count, 2,'.',''); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($total_hangtag_qty / $date_count, 2,'.',''); ?></b>
	                </td>
	                <td align="right"><b><?php echo number_format($total_poly_qty / $date_count, 2,'.',''); ?></b></td>
	                <td align="right">
	                    <b><?php echo number_format($total_finish_qty / $date_count, 2,'.',''); ?></b>
	                </td>
	                <td align="right"><b><? echo number_format($total_ins_qty / $date_count, 2,'.',''); ?></b></td>
                    <td align="right"><b><? echo number_format($total_ship_qty / $date_count, 2,'.',''); ?></b></td>
                    <td align="right"><b><? echo number_format($total_ship_value / $date_count, 2,'.',''); ?></b></td>
                    <td align="right"><b><? echo number_format($total_shipment_net_value / $date_count, 2,'.',''); ?></b></td>
                    <td align="right"><b><? echo number_format($total_ship_rtn_qty / $date_count, 2,'.',''); ?></b></td>
	            </tr>
	            <tr>
	                <td align="left" colspan="2"><b>Total Exclude Friday</b></td>
                    <td align="right"><b><?php echo number_format($totl_projec_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_confirm_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_cancel_order_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_ref_closing_qty, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_sort_grey_qty, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_sample_prod_qty, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_sample_delivery_qty, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_grey_recv, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_grey_iss, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_dyed_recv, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_dyed_iss, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_knit_qty_in, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_knit_qty_out, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_recv_qty,2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_issue_qty,2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_trans_in_qty, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_trans_out_qty, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_recv_batch_qty, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_batch_qty, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_re_process_batch_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_trims_dyei_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_dyei_qty_in, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php //echo '22 Dyeing [Outbound]'; ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_dye_fini_in_qty, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_dye_fin_qty_out, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_fin_roll_rcv_qty, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_fin_roll_issue_qty, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_finish_fabric_issue_return_qty, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php //echo '27 Finish Fabric Trasfer Recv'; ?></b></td>
                    <td align="right"><b><?php //echo '28 Finish Fabric Trasfer Issue'; ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_aop_issue_qty, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_aop_recv_qty, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_cutting_fab_recv, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_cutt_inhouse_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php //echo '33 CUTTING [Out-bound]'; ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_pnt_issue_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_pnt_issue_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_pnt_issue_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_pnt_issue_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_prnt_rcv_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_prnt_rcv_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_prnt_rcv_qty, 2,'.',''); ?></b></td>

	                <td align="right"><b><?php echo number_format($totl_emb_issue_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_emb_issue_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_emb_issue_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_emb_issue_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_emb_rcv_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_emb_rcv_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_emb_rcv_qty, 2,'.',''); ?></b></td>

	                <td align="right"><b><?php echo number_format($totl_special_issue_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_special_rcv_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_dyeing_issue_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_dyeing_rcv_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_wash_issue_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_wash_rcv_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_man_power, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_operator, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_helper, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_sewi_inbound_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_sewi_outbound_qty, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php //echo '49 Effeciency'; ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_iro_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_hangtag_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_poly_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_fini_qty, 2,'.',''); ?></b></td>
	                <td align="right"><b><? echo number_format($totl_ins_qty, 2,'.',''); ?></b></td>
                    <td align="right"><b><? echo number_format($totl_ship_qty, 2,'.',''); ?></b></td>
                    <td align="right"><b><? echo number_format($totl_ship_value, 2,'.',''); ?></b></td>
                    <td align="right"><b><? echo number_format($totl_shipment_net_value, 2,'.',''); ?></b></td>
                    <td align="right"><b><? echo number_format($totl_ship_rtn_qty, 2,'.',''); ?></b></td>
	            </tr>
	            <tr>
	                <td align="left" colspan="2"><b>AVG. Excl. Friday</b></td>
                    <td align="right"><b><?php echo number_format($totl_projec_qty / $date_count2, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_confirm_qty / $date_count2, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_cancel_order_qty / $date_count2, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_ref_closing_qty / $date_count2, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_sort_grey_qty / $date_count2, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_sample_prod_qty / $date_count2, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_sample_delivery_qty / $date_count2, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_grey_recv / $date_count2, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_grey_iss / $date_count2, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_dyed_recv / $date_count2, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_dyed_iss / $date_count2, 2,'.',''); ?></b></td>
	                <td align="right">
	                	<b><?php echo number_format($totl_knit_qty_in / $date_count2, 2,'.',''); ?></b>
	                </td>
	                <td align="right"><b><?php echo number_format($totl_knit_qty_out / $date_count2, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_recv_qty / $date_count2,2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_issue_qty / $date_count2,2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_trans_in_qty / $date_count2,2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_trans_out_qty / $date_count2, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_recv_batch_qty / $date_count2, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_batch_qty / $date_count2, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_re_process_batch_qty / $date_count2, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_trims_dyei_qty / $date_count2, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_dyei_qty_in / $date_count2, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php //echo '22 Dyeing [Outbound]'; ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_dye_fini_in_qty / $date_count2, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_dye_fin_qty_out / $date_count2, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_fin_roll_rcv_qty / $date_count2, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_fin_roll_issue_qty / $date_count2, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_finish_fabric_issue_return_qty  / $date_count2, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php //echo '27 Finish Fabric Trasfer Recv'; ?></b></td>
                    <td align="right"><b><?php //echo '28 Finish Fabric Trasfer Issue'; ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_aop_issue_qty / $date_count2, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_aop_recv_qty / $date_count2, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_cutting_fab_recv / $date_count2, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($totl_cutt_inhouse_qty / $date_count2, 2,'.',''); ?></b>
	                </td>
	                <td align="right"><b><?php //echo '33 CUTTING [Out-bound]'; ?></b></td>
	                <td align="right">
	                    <b><?php echo number_format($totl_pnt_issue_qty / $date_count2, 2,'.',''); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($totl_pnt_issue_qty / $date_count2, 2,'.',''); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($totl_pnt_issue_qty / $date_count2, 2,'.',''); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($totl_pnt_issue_qty / $date_count2, 2,'.',''); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($totl_prnt_rcv_qty / $date_count2, 2,'.',''); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($totl_prnt_rcv_qty / $date_count2, 2,'.',''); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($totl_prnt_rcv_qty / $date_count2, 2,'.',''); ?></b>
	                </td>

	                <td align="right">
	                    <b><?php echo number_format($totl_emb_issue_qty / $date_count2, 2,'.',''); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($totl_emb_issue_qty / $date_count2, 2,'.',''); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($totl_emb_issue_qty / $date_count2, 2,'.',''); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($totl_emb_issue_qty / $date_count2, 2,'.',''); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($totl_emb_rcv_qty / $date_count2, 2,'.',''); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($totl_emb_rcv_qty / $date_count2, 2,'.',''); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($totl_emb_rcv_qty / $date_count2, 2,'.',''); ?></b>
	                </td>


	                <td align="right">
	                    <b><?php echo number_format($totl_special_issue_qty / $date_count2, 2,'.',''); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($totl_special_rcv_qty / $date_count2, 2,'.',''); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($totl_dyeing_issue_qty / $date_count2, 2,'.',''); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($totl_dyeing_rcv_qty / $date_count2, 2,'.',''); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($totl_wash_issue_qty / $date_count2, 2,'.',''); ?></b>
	                </td>
	                <td align="right">
	                    <b><?php echo number_format($totl_wash_rcv_qty / $date_count2, 2,'.',''); ?></b>
	                </td>
	                <td align="right"><b><?php echo number_format($totl_man_power / $date_count2, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_operator / $date_count2, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($totl_helper / $date_count2, 2,'.',''); ?></b></td>
	                <td align="right">
	                	<b><?php echo number_format($totl_sewi_inbound_qty / $date_count2, 2,'.',''); ?></b>
	                </td>
	                <td align="right"><b><?php echo number_format($totl_sewi_outbound_qty / $date_count2, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php //echo '49 Effeciency'; ?></b></td>
	                <td align="right">
	                	<b><?php echo number_format($totl_iro_qty / $date_count2, 2,'.',''); ?></b>
	                </td>
	                <td align="right">
	                	<b><?php echo number_format($totl_hangtag_qty / $date_count2, 2,'.',''); ?></b>
	                </td>
	                <td align="right"><b><?php echo number_format($totl_poly_qty / $date_count2, 2,'.',''); ?></b></td>
	                <td align="right">
	                	<b><?php echo number_format($totl_fini_qty / $date_count2, 2,'.',''); ?></b>
	                </td>
	                <td align="right"><b><? echo number_format($totl_ins_qty / $date_count2, 2,'.',''); ?></b></td>
                    <td align="right"><b><? echo number_format($totl_ship_qty / $date_count2, 2,'.',''); ?></b></td>
                    <td align="right"><b><? echo number_format($totl_ship_value / $date_count2, 2,'.',''); ?></b></td>
                    <td align="right"><b><? echo number_format($totl_shipment_net_value / $date_count2, 2,'.',''); ?></b></td>
                    <td align="right"><b><? echo number_format($totl_ship_rtn_qty / $date_count2, 2,'.',''); ?></b></td>
	            </tr>
	        </table>

	    </fieldset><br/>


	    <!-- ======FACTORY WISE PRODUCTION SUMMARY START========== -->
	    <!-- ======Compuny,location wise summury================== -->
	    <fieldset style="width: 5320px; margin:0px auto;">
	        <table cellspacing="0" cellpadding="0" id="sammary_tbl" border="1" rules="all" class="rpt_table" style="width: 100%">
	            <thead>
		            <tr>
		                <th colspan="68" style="text-align: center;">Factory Wise Production Summary</th>
		            </tr>
		            <tr>
						<th>SL</th>
						<th>Company</th>
						<th>Projection Order [PCS]</th>
		                <th>Confirm Order [PCS]</th>
		                <th>Cancel Order [PCS]</th>
		                <th>Reference Close [Pcs]</th>
		                <th>Short/EFR Booking</th>
		                <th>Sample Production [PCS]</th>
		                <th>Sample Delivery [PCS]</th>
		                <th>Grey Yarn Receive</th>
		                <th>Grey Yarn Issue</th>
		                <th>Dyed Yarn Receive</th>
		                <th>Dyed Yarn Issue</th>
						<th>Knitting [Inbound]</th>
						<th>Knitting [Out-bound]</th>
		                <th>Greige Fabric Receive</th>
		                <th>Greige Fabric issue</th>
		                <th>Finish Fabric Issue Return</th>
		                <th>Greige Fabric Transfer Rcv</th>
		                <th>Greige Fabric Transfer Issue</th>
		                <th>Batch Greige Fabric Receive</th>
		                <th>Batch</th>
		                <th>Re-process Batch</th>
		                <th>Trims Dyeing [Inbound & Outbound]</th>
						<th>Dyeing [Inbound]</th>
						<th>Dyeing [Outbound]</th>
		                <th>Dyeing Finishing [Inbound]</th>
		                <th>Dyeing Finishing [Out-bound]</th>
		                <th>Finish Fabric Receive</th>
		                <th>Finish Fabric Issue</th>
		                <th>Finish Fabric Trasfer Recv</th>
		                <th>Finish Fabric Trasfer Issue</th>
		                <th>AOP Issue</th>
		                <th>AOP Receive</th>
		                <th>Cutting Fabric Receive</th>
						<th>Cutting [Inbound]</th>
						<th>Cutting [Out-bound]</th>
						<th>Print Send</th>
						<th>Print RCV</th>
						<th>EMB Send</th>
						<th>EMB RCV</th>
						<th>Print [Inhound]</th>
						<th>Print [Outhound]</th>
						<th>Print Delivery to Cutting</th>
						<th>Embroidery [Inhound]</th>
						<th>Embroidery [Outhound]</th>
						<th>Embroidery Delivery to Cutting</th>
						<th>Special Work Send</th>
						<th>Special Work RCV</th>
						<th>GMT Dyeing Send</th>
						<th>GMT Dyeing RCV</th>
						<th>Wash Send</th>
						<th>Wash RCV</th>
						<th>Total Man Power</th>
		                <th>No of Operator</th>
		                <th>No of Helper</th>
						<th>Sewing Output [Inbound]</th>
						<th>Sewing Output [Out-bound]</th>
		                <th>Sewing Efficiency %</th>
						<th>Iron</th>
						<th>Hangtag</th>
		                <th>Poly</th>
						<th>Finish</th>
						<th>Inspection</th>
		                <th>Shipment Qty</th>
		                <th>Shipment Gross Value $</th>
		                <th>Shipment Net Value $</th>
		                <th>Shipment Return Qty</th>
		            </tr>
	            </thead>
				<?
				$k = 1;
				foreach ($rev_qty2 as $company_id => $row)
				{
					if ($k % 2 == 1) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
					$knitting_qty_in2=$row['knitting_qty_in']+$row['subc_knitting_qty_in'];
					$knitting_qty_out2=$row['knitting_qty_out']+$row['subc_knitting_qty_out'];
					$recv_qty2=$row['recv_qty_in']+$row['recv_qty_out']+$row['issue_rtn_qty'];
					$batch_qty2=$row['batch_qty']+$row['subc_batch_qty'];
					$re_process_batch_qty2=$row['re_process_batch_qty']+$row['subc_re_process_batch_qty'];
					$dyeing_in2=$row['dyeing']+$row['subc_dyeing'];
					$dye_fini_in_qty2=$row['dye_fin_qty_in']+$row['fin_product_qnty'];
					$poly_qty2=$row['inhouse_poly_qty']+$row['outbond_poly_qty']+$row['subc_poly_qty'];
					$sewing_inbound_qty2=$row['sewing_inhouse_qty']+$row['subc_sewing_output_qty'];
					$cutting_inhouse_qty2=$row['cutting_inhouse']+$row['subc_cutting_qty'];
					$fin_roll_rcv_qty2=$row['fin_roll_rcv_qty']+$row['fin_roll_iss_rtn_qty'];
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td><? echo $k; ?></td>
                        <td><? echo $company_arr[$company_id]; ?></td>
                        <td align="right"><? echo number_format($row['projec_qty_pcs'], 2,'.',''); ?></td>
                        <td align="right"><? echo number_format($row['confirm_qty_pcs'], 2,'.',''); ?></td>
                        <td align="right"><? echo number_format($row['cancel_order_qty'], 2,'.',''); ?></td>
                        <td align="right"><? echo number_format($row['ref_closing_qty'], 2,'.',''); ?></td>
                        <td align="right"><? echo number_format($row['sort_grey_qty'], 2,'.',''); ?></td>
                        <td align="right"><? echo number_format($row['sample_prod_qty'], 2,'.',''); ?></td>
                        <td align="right"><? echo number_format($row['sample_delivery_qty'], 2,'.',''); ?></td>
                        <td align="right"><? echo number_format($row['grey_recv_total'], 2,'.',''); ?></td>
                        <td align="right"><? echo number_format($row['grey_iss_total'], 2,'.',''); ?></td>
                        <td align="right"><? echo number_format($row['dyed_recv_total'], 2,'.',''); ?></td>
                        <td align="right"><? echo number_format($row['dyed_iss_total'], 2,'.',''); ?></td>
                        <td align="right"><? echo number_format($knitting_qty_in2, 2,'.',''); ?></td>
                        <!-- <a href="##" onClick="openmypage_popup(<? //echo $company_id; ?>,'<? //echo $location_id; ?>','1','production_popup','1','knitting popup','knitting');" ><? //echo number_format($knitting_qty_in2, 0); ?></a> -->
                        <td align="right"><? echo number_format($knitting_qty_out2, 2,'.',''); ?></td>
                        <td align="right"><? echo number_format($recv_qty2,2,'.',''); ?></td>
                        <td align="right"><? echo number_format($row['issue_qty'],2,'.',''); ?></td>
                        <td align="right"><? echo number_format($row['trans_in_qty'], 2,'.',''); ?></td>
                        <td align="right"><? echo number_format($row['trans_out_qty'], 2,'.',''); ?></td>
                        <td align="right"><? echo number_format($row['recv_batch_qty'], 2,'.',''); ?></td>
                        <td align="right"><? echo number_format($batch_qty2, 2,'.',''); ?></td>
                        <td align="right"><? echo number_format($re_process_batch_qty2, 2,'.',''); ?></td>
                        <td align="right"><? echo number_format($row['trims_dyeing_qty'], 2,'.',''); ?></td>
                        <td align="right"><? echo number_format($dyeing_in2, 2,'.',''); ?></td>
                        <!-- <a href="##" onClick="openmypage_popup(<? //echo $company_id; ?>,'<? //echo $location_id; ?>','1','production_popup','1','dyeing popup','dyeing');" ><? //echo number_format($dyeing_in2, 0); ?></a> -->
                        <td align="right"><? //echo '22 Dyeing [Outbound]'; ?></td>
                        <td align="right"><? echo $dye_fini_in_qty2; ?></td>
                        <td align="right"><? echo number_format($row['dye_fin_qty_out'],2,'.',''); ?></td>
                        <td align="right"><? echo number_format($fin_roll_rcv_qty2,2,'.',''); ?></td>
                        <td align="right"><? echo number_format($row['fin_roll_issue_qty'],2,'.',''); ?></td>
                        <td align="right"><? echo number_format($finish_fabric_issue_return2 = 10,2,'.',''); ?></td>
                        <td align="right"><? //echo '27 Finish Fabric Trasfer Recv'; ?></td>
                        <td align="right"><? //echo '28 Finish Fabric Trasfer Issue'; ?></td>
                        <td align="right"><? echo number_format($row['aop_issue_qty'],2,'.',''); ?></td>
                        <td align="right"><? echo number_format($row['aop_recv_qty'],2,'.',''); ?></td>
                        <td align="right"><? echo number_format($row['cutting_fab_recv'],2,'.',''); ?></td>
                        <td align="right"><? echo number_format($cutting_inhouse_qty2, 2,'.',''); ?></td>
                        <!-- <a href="##" onClick="openmypage_popup(<? //echo $company_id; ?>,'<? //echo $location_id; ?>','1','production_popup','0','cutting popup','');" > <? //echo number_format($cutting_inhouse_qty2, 0); ?> </a> -->
                        <td align="right"><? //echo '33 CUTTING [Out-bound]'; ?></td>
                        <td align="right"><? echo number_format($row['print_issue'], 2,'.',''); ?></td>
                        <td align="right"><? echo number_format($row['print_rcv'], 2,'.',''); ?></td>
                        <td align="right"><? echo number_format($row['emb_issue'], 2,'.',''); ?></td>
                        <td align="right"><? echo number_format($row['emb_rcv'], 2,'.',''); ?></td>

                        <td align="right"><? echo number_format($print_inhound2 =$row['printing_production_qty'], 2,'.',''); ?></td>
                        <td align="right"><? echo number_format($print_outhound2=12, 2,'.',''); ?></td>
                        <td align="right"><? echo number_format($print_delivery_to_Cutting2=$row['printing_delivery_qty'], 2,'.',''); ?></td>
                        <td align="right"><? echo number_format($embroidery_inhound2=$row['embroidery_production_qty'], 2,'.',''); ?></td>
                        <td align="right"><? echo number_format($embroidery_outhound2=15, 2,'.',''); ?></td>
                        <td align="right"><? echo number_format($embroidery_delivery_to_cutting2=$row['embroidery_delivery_qty'], 2,'.',''); ?></td>

                        <td align="right"><? echo number_format($row['special_issue'], 2,'.',''); ?></td>
                        <td align="right"><? echo number_format($row['special_rcv'], 2,'.',''); ?></td>
                        <td align="right"><? echo number_format($row['dyeing_issue'], 2,'.',''); ?></td>
                        <td align="right"><? echo number_format($row['dyeing_rcv'], 2,'.',''); ?></td>
                        <td align="right"><? echo number_format($row['wash_issue'], 2,'.',''); ?></td>
                        <td align="right"><? echo number_format($row['wash_rcv'], 2,'.',''); ?></td>
                        <td align="right"><? echo number_format($row['man_power'], 2,'.',''); ?></td>
                        <td align="right"><? echo number_format($row['operator'], 2,'.',''); ?></td>
                        <td align="right"><? echo number_format($row['helper'], 2,'.',''); ?></td>
                        <td align="right"><? echo number_format($sewing_inbound_qty2, 2,'.',''); ?></td>
                        <!-- <a href="##" onClick="openmypage_popup(<? //echo $company_id; ?>,'<? //echo $location_id; ?>','5','production_popup','0','sewing popup','');" ><? //echo number_format($sewing_inbound_qty2, 0); ?></a> -->
                        <td align="right"><? echo number_format($row['sewing_outbound_qty'], 2,'.',''); ?></td>
                        <td align="right"><? echo number_format($com_sewing_effi_array[ $company_id], 2,'.',''); ?></td>
                        <td align="right"><? echo number_format($row['iron'], 2,'.',''); ?></td>
                        <!-- <a href="##" onClick="openmypage_popup(<? //echo $company_id; ?>,'<? //echo $location_id; ?>','7','production_popup','0','iron popup','');" ><? //echo number_format($row['iron'], 0); ?></a> -->
                        <td align="right"><? echo number_format($hangtag2=$row['hang_tag_qty'], 2,'.',''); ?></td>
                        <td align="right"><? echo number_format($poly_qty2, 2,'.',''); ?></td>
                        <td align="right"><? echo number_format($row['finish'],2,'.',''); ?></td>
                        <!-- <a href="##" onClick="openmypage_popup(<? //echo $company_id; ?>,'<? //echo $location_id; ?>','8','production_popup','0','finish popup','');" ><? //echo number_format($row['finish'],0); ?> </a> -->
                        <td align="right"><? echo number_format($row['ins_qty'],2,'.',''); ?></td>
                        <td align="right"><? echo number_format($row['ship_qty'],2,'.',''); ?></td>
                        <td align="right"><? echo number_format($row['ship_value'],2,'.',''); ?></td>
                        <td align="right"><? echo number_format($shipment_net_value2=25,2,'.',''); ?></td>
                        <td align="right"><? echo number_format($row['ship_rtn_qty'],2,'.',''); ?></td>
                    </tr>
					<?
					$k++;
					$total_projec_qty_pcs2 += $row['projec_qty_pcs'];
					$total_confirm_qty_pcs2 += $row['confirm_qty_pcs'];
					$total_cancel_order_qty2 += $row['cancel_order_qty'];
					$total_ref_closing_qty2 += $row['ref_closing_qty'];
					$total_sort_grey_qty2 += $row['sort_grey_qty'];
					$total_sample_prod_qty2 += $row['sample_prod_qty'];
					$total_sample_delivery_qty2 += $row['sample_delivery_qty'];
					$total_grey_recv_total2 += $row['grey_recv_total'];
					$total_grey_iss_total2 += $row['grey_iss_total'];
					$total_dyed_recv_total2 += $row['dyed_recv_total'];
					$total_dyed_iss_total2 += $row['dyed_iss_total'];
					$total_knitting_in2 += $knitting_qty_in2;
					$total_knitting_out2 += $knitting_qty_out2;
					$total_recv_qty2 += $recv_qty2;
					$total_issue_qty2 += $row['issue_qty'];
					$total_trans_in_qty2 += $row['trans_in_qty'];
					$total_trans_out_qty2 += $row['trans_out_qty'];
					$total_recv_batch_qty2 += $row['recv_batch_qty'];
					$total_batch_qty2 += $batch_qty2;
					$total_re_process_batch_qty2 += $re_process_batch_qty2;
					$trims_dyeing_qty2 += $qtyValue['trims_dyeing_qty'];
					$total_dyeing_in += $dyeing_in2;
					//$total_dyeing_out += $dyeing_out2; // 22
					$total_dye_fini_in_qty2 += $dye_fini_in_qty2;
					$total_dye_fin_qty_out2 += $row['dye_fin_qty_out']; //24
					$total_fin_roll_rcv_qty2 += $fin_roll_rcv_qty2; //25
					$total_fin_roll_issue_qty2 += $row['fin_roll_issue_qty']; //26
					$total_finish_fabric_issue_return2+= $finish_fabric_issue_return2; //26
					//27
					//28
					$total_aop_issue_qty2 += $row['aop_issue_qty'];
					$total_aop_recv_qty2 += $row['aop_recv_qty'];
					$total_cutting_fab_recv2 += $row['cutting_fab_recv'];
					$total_cutting_in2 += $cutting_inhouse_qty2;
					// $total_cutting_out2 += $row['cutting_out']; 33
					$total_print_issue += $row['print_issue'];
					$total_print_rcv += $row['print_rcv'];
					$total_emb_issue += $row['emb_issue'];
					$total_emb_rcv += $row['emb_rcv'];

					$total_print_inhound2 += $print_inhound2;
					$total_print_outhound2 += $print_outhound2;
					$total_print_delivery_to_Cutting2 += $print_delivery_to_Cutting2;
					$total_embroidery_inhound2 += $embroidery_inhound2;
					$total_embroidery_outhound2 += $embroidery_outhound2;
					$total_embroidery_delivery_to_cutting2 += $embroidery_delivery_to_cutting2;

					$total_special_issue += $row['special_issue'];
					$total_special_rcv += $row['special_rcv'];
					$total_dyeing_issue += $row['dyeing_issue'];
					$total_dyeing_rcv += $row['dyeing_rcv'];
					$total_wash_issue += $row['wash_issue'];
					$total_wash_rcv += $row['wash_rcv'];
					$total_man_power2 += $row['man_power'];
					$total_operator2 += $row['operator'];
					$total_helper2 += $row['helper'];
					$total_sewing_inbound2 += $sewing_inbound_qty2;
					$total_sewing_outbound2 += $row['sewing_outbound_qty'];
					$total_iron += $row['iron'];
					$total_hangtag2 += $hangtag2;
					$total_poly_qty2 += $poly_qty2;
					$total_finish += $row['finish'];
					$total_ins_qty2 += $row['ins_qty'];
					$total_ship_qty2 += $row['ship_qty'];
					$total_ship_value2 += $row['ship_value'];
					$total_shipment_net_value2 += $shipment_net_value2;
					$total_ship_rtn_qty2 += $row['ship_rtn_qty'];
				}
				?>
	            <tr bgcolor="<? echo $bgcolor; ?>">
	                <td align="left" colspan="2"><b>Total</b></td>
                    <td align="right"><b><?php echo number_format($total_projec_qty_pcs2, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_confirm_qty_pcs2, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_cancel_order_qty2, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_ref_closing_qty2, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_sort_grey_qty2, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_sample_prod_qty2, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_sample_delivery_qty2, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_grey_recv_total2, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_grey_iss_total2, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_dyed_recv_total2, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_dyed_iss_total2, 2,'.',''); ?></b></td>
	                <td align="right"><b><? echo number_format($total_knitting_in2, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_knitting_out2, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_recv_qty2,2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_issue_qty2,2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_trans_in_qty2, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_trans_out_qty2, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_recv_batch_qty2, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_batch_qty2, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_re_process_batch_qty2, 2,'.',''); ?></b></td>
	                <td align="right"><b><? echo number_format($trims_dyeing_qty2, 2,'.',''); ?></b></td>
	                <td align="right"><b><? echo number_format($total_dyeing_in, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php //echo '22 Dyeing [Outbound]'; ?></b></td>
                    <td align="right"><b><?php echo number_format($total_dye_fini_in_qty2, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_dye_fin_qty_out2, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_fin_roll_rcv_qty2, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_fin_roll_issue_qty2, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_finish_fabric_issue_return2, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php //echo '27 Finish Fabric Trasfer Recv'; ?></b></td>
                    <td align="right"><b><?php //echo '28 Finish Fabric Trasfer Issue'; ?></b></td>
                    <td align="right"><b><?php echo number_format($total_aop_issue_qty2, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_aop_recv_qty2, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_cutting_fab_recv2, 2,'.',''); ?></b></td>
	                <td align="right"><b><? echo number_format($total_cutting_in2, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php //echo '33'; ?></b></td>
	                <td align="right"><b><? echo number_format($total_print_issue, 2,'.',''); ?></b></td>
	                <td align="right"><b><? echo number_format($total_print_rcv, 2,'.',''); ?></b></td>
	                <td align="right"><b><? echo number_format($total_emb_issue, 2,'.',''); ?></b></td>
	                <td align="right"><b><? echo number_format($total_emb_rcv, 2,'.',''); ?></b></td>

	                <td align="right"><b><? echo number_format($total_print_inhound2, 2,'.',''); ?></b></td>
	                <td align="right"><b><? echo number_format($total_print_outhound2, 2,'.',''); ?></b></td>
	                <td align="right"><b><? echo number_format($total_print_delivery_to_Cutting2, 2,'.',''); ?></b></td>
	                <td align="right"><b><? echo number_format($total_embroidery_inhound2, 2,'.',''); ?></b></td>
	                <td align="right"><b><? echo number_format($total_embroidery_outhound2, 2,'.',''); ?></b></td>
	                <td align="right"><b><? echo number_format($total_embroidery_delivery_to_cutting2, 2,'.',''); ?></b></td>

	                <td align="right"><b><? echo number_format($total_special_issue, 2,'.',''); ?></b></td>
	                <td align="right"><b><? echo number_format($total_special_rcv, 2,'.',''); ?></b></td>
	                <td align="right"><b><? echo number_format($total_dyeing_issue, 2,'.',''); ?></b></td>
	                <td align="right"><b><? echo number_format($total_dyeing_rcv, 2,'.',''); ?></b></td>
	                <td align="right"><b><? echo number_format($total_wash_issue, 2,'.',''); ?></b></td>
	                <td align="right"><b><? echo number_format($total_wash_rcv, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_man_power2, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_operator2, 2,'.',''); ?></b></td>
                    <td align="right"><b><?php echo number_format($total_helper2, 2,'.',''); ?></b></td>
	                <td align="right"><b><? echo number_format($total_sewing_inbound2, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_sewing_outbound2, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php //echo '49 Effeciency '; ?></b></td>
	                <td align="right"><b><? echo number_format($total_iron, 2,'.',''); ?></b></td>
	                <td align="right"><b><? echo number_format($total_hangtag2, 2,'.',''); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_poly_qty2, 2,'.',''); ?></b></td>
	                <td align="right"><b><? echo number_format($total_finish, 2,'.',''); ?></b></td>
	                <td align="right"><b><? echo number_format($total_ins_qty2, 2,'.',''); ?></b></td>
                    <td align="right"><b><? echo number_format($total_ship_qty2, 2,'.',''); ?></b></td>
                    <td align="right"><b><? echo number_format($total_ship_value2, 2,'.',''); ?></b></td>
                    <td align="right"><b><? echo number_format($total_shipment_net_value2, 2,'.',''); ?></b></td>
                    <td align="right"><b><? echo number_format($total_ship_rtn_qty2, 2,'.',''); ?></b></td>
	            </tr>
	        </table>

	    </fieldset>
	    <!-- ======FACTORY WISE PRODUCTION SUMMARY END============ -->
		<?
		foreach (glob("$user_id*.xls") as $filename) {
			if (@filemtime($filename) < (time() - $seconds_old))
				@unlink($filename);
		}
		//---------end------------//
		$name = time();
		$filename = $user_id . "_" . $name . ".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc, ob_get_contents());
		//$filename=$user_id."_".$name.".xls";
		echo "$total_data####$filename";
	}
	else if($type==5) // Show 5
	{
		$location_id_cond_subc = ($location_id == 0) ? "" : " and a.knit_location_id=$location_id";
		$location_id_cond_y = ($location_id == 0) ? "" : " and d.location_id=$location_id";
		$location_id_cond_trns = ($location_id == 0) ? "" : " and b.location_id=$location_id";
		$knit_lc_location_id_cond = ($location_id == 0) ? "" : " and a.location_id=$location_id";
		$location_id_issue_cond = ($location_id == 0) ? "" : " and b.location_id=$location_id";
		$working_location_cond = ($location_id == 0) ? "" : " and a.working_location=$location_id";

		$company_cond_order = ($company_name == 0) ? "" : " and b.company_name in($company_name)";
		$location_id_cond_order = ($location_id == 0) ? "" : " and b.location_name in($location_id)";
		$company_cond_sort_book = ($company_name == 0) ? "" : " and a.company_id in($company_name)";
		$company_cond_transf = ($company_name == 0) ? "" : " and b.company_id in($company_name)";
		$working_company_id_cond = ($company_name == 0) ? "" : " and a.working_company_id in($company_name)";
		$working_company_cond = ($company_name == 0) ? "" : " and a.working_company in ($company_name)";
		$location_cond4 = ($location_id == 0) ? "" : " and a.location_id=$location_id";

		// Order info
		$order_sql=sql_select("SELECT a.job_no_mst, sum(a.po_quantity) as po_quantity, sum(a.po_quantity*b.total_set_qnty) as po_quantity_psc,
        sum(case when a.is_confirmed=1 then a.po_quantity*b.total_set_qnty else 0 end) as confirm_qty_pcs,
        sum(case when a.is_confirmed=2 then a.po_quantity*b.total_set_qnty else 0 end) as projec_qty_pcs,
        a.packing, b.company_name, b.location_name, b.order_uom , b.total_set_qnty, a.po_received_date
        from wo_po_break_down a, wo_po_details_master b
        where a.job_id=b.id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_cond_order $date_cond_order $location_id_cond_order
        group by a.job_no_mst, a.packing, b.company_name, b.location_name, b.order_uom, b.total_set_qnty, a.po_received_date order by a.po_received_date"); //and a.job_no_mst='rpc-21-00117'
		//echo $order_sql;die;
		$rev_qty = array();
		foreach ($order_sql as $value)
		{
			$rev_qty[$value[csf('company_name')]][strtotime($value[csf('po_received_date')])]['confirm_qty_pcs'] += $value[csf('confirm_qty_pcs')];
			$rev_qty[$value[csf('company_name')]][strtotime($value[csf('po_received_date')])]['projec_qty_pcs'] += $value[csf('projec_qty_pcs')];
			$rev_qty[$value[csf('company_name')]][strtotime($value[csf('po_received_date')])]['inactive_qty_pcs'] += $value[csf('inactive_qty_pcs')];

			$rev_qty2[$value[csf('company_name')]]['confirm_qty_pcs'] += $value[csf('confirm_qty_pcs')];
			$rev_qty2[$value[csf('company_name')]]['projec_qty_pcs'] += $value[csf('projec_qty_pcs')];
			$rev_qty2[$value[csf('company_name')]]['inactive_qty_pcs'] += $value[csf('inactive_qty_pcs')];
		}
	  //	echo "<pre>";print_r($rev_qty2); die;

		// ================================= cancel order =============================
		$cancel_order_sql=sql_select("SELECT a.job_no_mst, b.company_name, a.po_received_date, $cancel_date, b.location_name, a.packing, b.total_set_qnty,
        sum(case when a.is_confirmed=1 then a.po_quantity*b.total_set_qnty else 0 end) as cancel_order_qty
        from wo_po_break_down a, wo_po_details_master b
        where a.job_id=b.id and a.status_active in(3) and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_cond_order $date_cond_order2 $location_id_cond_order
        group by a.job_no_mst, b.company_name, a.update_date, a.po_received_date, b.location_name, a.packing, b.total_set_qnty order by a.update_date");
		//echo 	$cancel_order_sql;die;
		foreach ($cancel_order_sql as $value)
		{
			$date=$value[csf('cancel_date')];
			$cancel_date2=strtoupper(date("d-M-y", strtotime("$date")));
			$rev_qty[$value[csf('company_name')]][strtotime($cancel_date2)]['cancel_order_qty'] += $value[csf('cancel_order_qty')];
			$rev_qty2[$value[csf('company_name')]]['cancel_order_qty'] += $value[csf('cancel_order_qty')];
		}
		// echo "<pre>";print_r($rev_qty);

		// =================================== Short Fabric Booking ===================================
		$sort_fab_book_sql=sql_select("SELECT a.company_id, a.booking_date, sum(b.grey_fab_qnty) as sort_grey_qty  from wo_booking_mst a,  wo_booking_dtls b
        where a.booking_no=b.booking_no and a.job_no=b.job_no and a.entry_form=88  and a.item_category=2 and a.status_active=1 and b.is_deleted=0 and a.status_active=1 and b.is_deleted=0  $company_cond_sort_book $date_cond_sort_book
        group by a.company_id, a.booking_date order by a.booking_date");
		//echo $sort_fab_book_sql; die;
		foreach ($sort_fab_book_sql as $value) // Location not found
		{
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('booking_date')])]['sort_grey_qty'] += $value[csf('sort_grey_qty')];
			$rev_qty2[$value[csf('company_id')]]['sort_grey_qty'] += $value[csf('sort_grey_qty')];
		}

		$main_fab_book_sql=sql_select("SELECT a.company_id, a.booking_date, sum(b.grey_fab_qnty) as main_grey_qty  from wo_booking_mst a,  wo_booking_dtls b
        where a.booking_no=b.booking_no and a.job_no=b.job_no and a.entry_form=118  and a.item_category=2 and a.status_active=1 and b.is_deleted=0 and a.status_active=1 and b.is_deleted=0  $company_cond_sort_book $date_cond_sort_book
        group by a.company_id, a.booking_date order by a.booking_date");
		//echo $main_fab_book_sql; die;
		foreach ($main_fab_book_sql as $value) // Location not found
		{
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('booking_date')])]['main_grey_qty'] += $value[csf('main_grey_qty')];
			$rev_qty2[$value[csf('company_id')]]['main_grey_qty'] += $value[csf('main_grey_qty')];
		}

		//================================== Sample Sewing Output (Sample Production [Pcs]) =====================================
		$sample_prod_sql=sql_select("SELECT a.company_id, b.sewing_date, a.location, sum(b.qc_pass_qty) as sample_prod_qty  from sample_sewing_output_mst a,  sample_sewing_output_dtls b
		where a.id=b.sample_sewing_output_mst_id and a.status_active=1 and b.is_deleted=0 and a.status_active=1 and b.is_deleted=0
		$company_cond_sort_book $location_cond $date_cond_sample_prod
		group by a.company_id, b.sewing_date, a.location order by b.sewing_date");
		//echo $sample_prod_sql;die;
		foreach ($sample_prod_sql as $value)
		{
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('sewing_date')])]['sample_prod_qty'] += $value[csf('sample_prod_qty')];
			//$rev_qty[$value[csf('company_id')]][$value[csf('sewing_date')]]['location_id'] = $value[csf('location')];
			$rev_qty2[$value[csf('company_id')]]['sample_prod_qty'] += $value[csf('sample_prod_qty')];
			//$rev_qty2[$value[csf('company_id')]]['location_id'] = $value[csf('location')];
		}
		// echo "<pre>";print_r($rev_qty);

		//============================= Sample Delivery Entry (Sample Delivery [Pcs]) ==================================
		$sample_delivery_sql=sql_select("SELECT a.company_id, a.ex_factory_date, a.location, sum(b.ex_factory_qty) as sample_delivery_qty from sample_ex_factory_mst a,  sample_ex_factory_dtls b
		where a.id=b.sample_ex_factory_mst_id and a.entry_form_id=132 and b.entry_form_id=132 and a.status_active=1 and b.is_deleted=0 and a.status_active=1 and b.is_deleted=0
		$company_cond_sort_book $location_cond $date_cond_sample_delivery
		group by a.company_id, a.ex_factory_date, a.location order by ex_factory_date");
	 //	echo $sample_delivery_sql;die;
		foreach ($sample_delivery_sql as $value)
		{
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('ex_factory_date')])]['sample_delivery_qty'] += $value[csf('sample_delivery_qty')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('ex_factory_date')])]['location_id'] = $value[csf('location')];
			$rev_qty2[$value[csf('company_id')]]['sample_delivery_qty'] += $value[csf('sample_delivery_qty')];
			$rev_qty2[$value[csf('company_id')]]['location_id'] = $value[csf('location')];
		}
		// echo "<pre>";print_r($rev_qty);

		//=================================  Yarn Info =====================================
		$yarn_sql="SELECT b.mst_id, b.prod_id, b.company_id, b.transaction_date,
		sum(case when b.transaction_type in (1,4,5) and c.dyed_type in(0,2) then b.cons_quantity else 0 end) as grey_recv_total,
		sum(case when b.transaction_type in (1,4,5) and c.dyed_type in(1) then b.cons_quantity else 0 end) as dyed_recv_total,
		sum(case when b.transaction_type in (2,3,6) and c.dyed_type in(0,2) then b.cons_quantity else 0 end) as grey_iss_total,
		sum(case when b.transaction_type in (2,3,6) and c.dyed_type in(1) then b.cons_quantity else 0 end) as dyed_iss_total
		from inv_transaction b, product_details_master c, lib_store_location d
		where b.prod_id=c.id and b.store_id=d.id and b.item_category=1 and b.transaction_type in(1,2,3,4,5,6) and b.item_category=1 $company_cond_transf $location_id_cond_y $date_cond_tran
		and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.mst_id, b.prod_id, b.company_id, b.transaction_date order by transaction_date";
		//echo $yarn_sql;die;
		$yarn_sql_result=sql_select($yarn_sql);
		foreach ($yarn_sql_result as $value)
		{
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('transaction_date')])]['grey_recv_total'] += $value[csf('grey_recv_total')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('transaction_date')])]['dyed_recv_total'] += $value[csf('dyed_recv_total')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('transaction_date')])]['grey_iss_total'] += $value[csf('grey_iss_total')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('transaction_date')])]['dyed_iss_total'] += $value[csf('dyed_iss_total')];

			$rev_qty2[$value[csf('company_id')]]['grey_recv_total'] += $value[csf('grey_recv_total')];
			$rev_qty2[$value[csf('company_id')]]['dyed_recv_total'] += $value[csf('dyed_recv_total')];
			$rev_qty2[$value[csf('company_id')]]['grey_iss_total'] += $value[csf('grey_iss_total')];
			$rev_qty2[$value[csf('company_id')]]['dyed_iss_total'] += $value[csf('dyed_iss_total')];
		}
		//echo "<pre>";print_r($rev_qty2);die;

		//==============================  knitting and Roll Recv summary ==============================
		$knit_and_rollRecv_sqls="SELECT x.receive_date, x.knitting_company,x.location_id, sum(x.recv_qty_in) as recv_qty_in, sum(x.recv_qty_out) as recv_qty_out, sum(x.knitting_qty_in) as knitting_qty_in, sum(x.knitting_qty_out) as knitting_qty_out
		FROM (
		SELECT a.receive_date, a.knitting_company, a.knitting_location_id as location_id,
		sum(case when a.entry_form =58 then b.grey_receive_qnty else 0 end) as recv_qty_in,
		0 as recv_qty_out,
		sum(case when a.entry_form =2 then b.grey_receive_qnty else 0 end) as knitting_qty_in,
		0 as knitting_qty_out
		from inv_receive_master a, pro_grey_prod_entry_dtls b
		where a.id=b.mst_id and a.status_active=1 $date_cond_knit $company_knit_cond $location_id_cond and a.entry_form in(2,58) and a.item_category=13 and a.is_deleted=0
		and a.knitting_source=1 group by a.receive_date,a.knitting_company,a.knitting_location_id
		UNION ALL
		SELECT a.receive_date, a.company_id as knitting_company, a.location_id,
		0 as recv_qty_in,
		sum(case when a.entry_form =58 then b.grey_receive_qnty else 0 end) as recv_qty_out,
		0 as knitting_qty_in,
		sum(case when a.entry_form =2 then b.grey_receive_qnty else 0 end) as knitting_qty_out
		from inv_receive_master a, pro_grey_prod_entry_dtls b
		where a.id=b.mst_id and a.status_active=1 $date_cond_knit $company_cond_sort_book $knit_lc_location_id_cond and a.entry_form in(2,58) and a.item_category=13 and a.is_deleted=0
		and a.knitting_source=3 group by a.receive_date,a.company_id,a.location_id )  x
		group by x.receive_date, x.knitting_company,x.location_id order by x.receive_date";
		//echo $knit_and_rollRecv_sqls;die;
		$knit_and_rollRecv_sqls_result=sql_select($knit_and_rollRecv_sqls);
		foreach ($knit_and_rollRecv_sqls_result as $value)
		{
			$rev_qty[$value[csf('knitting_company')]][strtotime($value[csf('receive_date')])]['knitting_qty_in'] += $value[csf('knitting_qty_in')];
			$rev_qty[$value[csf('knitting_company')]][strtotime($value[csf('receive_date')])]['knitting_qty_out'] += $value[csf('knitting_qty_out')];
			$rev_qty2[$value[csf('knitting_company')]]['knitting_qty_in'] += $value[csf('knitting_qty_in')];
			$rev_qty2[$value[csf('knitting_company')]]['knitting_qty_out'] += $value[csf('knitting_qty_out')];
			// ========
			$rev_qty[$value[csf('knitting_company')]][strtotime($value[csf('receive_date')])]['recv_qty_in'] += $value[csf('recv_qty_in')];
			$rev_qty[$value[csf('knitting_company')]][strtotime($value[csf('receive_date')])]['recv_qty_out'] += $value[csf('recv_qty_out')];
			$rev_qty2[$value[csf('knitting_company')]]['recv_qty_in'] += $value[csf('recv_qty_in')];
			$rev_qty2[$value[csf('knitting_company')]]['recv_qty_out'] += $value[csf('recv_qty_out')];
		}
       //  echo"<pre>";print_r($rev_qty); die;
		//=============================== Sub-Contact Knitting Production =================================
		$knit_subc_sqls="SELECT x.product_date, x.knitting_company,x.location_id, sum(x.subc_knitting_qty_in) as subc_knitting_qty_in, sum(x.subc_knitting_qty_out) as subc_knitting_qty_out
		FROM (
		SELECT a.product_date, a.knitting_company, a.knit_location_id as location_id, sum(b.product_qnty) as subc_knitting_qty_in, 0 as subc_knitting_qty_out
		from subcon_production_mst a, subcon_production_dtls b
		where a.id=b.mst_id $date_cond_subc $company_knit_cond $location_id_cond_subc
		and a.entry_form=159 and a.knitting_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.product_date,a.knitting_company, a.knit_location_id
		union all
		select a.product_date, a.company_id as knitting_company, a.location_id, 0 as subc_knitting_qty_in, sum(b.product_qnty) as subc_knitting_qty_out
		from subcon_production_mst a, subcon_production_dtls b
		where a.id=b.mst_id $date_cond_subc $company_cond_sort_book $knit_lc_location_id_cond and a.entry_form=159 and a.knitting_source=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.product_date,a.company_id,a.location_id )  x
		group by x.product_date, x.knitting_company,x.location_id order by x.product_date";

		$knit_subc_sqls_result=sql_select($knit_subc_sqls);
		foreach ($knit_subc_sqls_result as $value)
		{
			$rev_qty[$value[csf('knitting_company')]][strtotime($value[csf('product_date')])]['subc_knitting_qty_in'] += $value[csf('subc_knitting_qty_in')];
			$rev_qty[$value[csf('knitting_company')]][strtotime($value[csf('product_date')])]['subc_knitting_qty_out'] += $value[csf('subc_knitting_qty_out')];
			$rev_qty2[$value[csf('knitting_company')]]['subc_knitting_qty_in'] += $value[csf('subc_knitting_qty_in')];
			$rev_qty2[$value[csf('knitting_company')]]['subc_knitting_qty_out'] += $value[csf('subc_knitting_qty_out')];
		}

		//================================= Roll Issue ============================
		$roll_issue_sql="SELECT a.issue_date, a.company_id, b.location_id, c.issue_qnty
		from inv_issue_master a, inv_transaction b, inv_grey_fabric_issue_dtls c
		where a.id=b.mst_id and a.id=c.mst_id and b.id=c.trans_id  and a.entry_form=16  and b.item_category=13 and b.transaction_type=2
		and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $company_cond_sort_book $date_cond_tran $location_id_issue_cond";

		//echo $roll_issue_sql;die;
		$roll_issue_sql_result=sql_select($roll_issue_sql);// and a.issue_number='RpC-KGIR-21-00033'
		$issue_barcode_no = array();
		foreach ($roll_issue_sql_result as $value)
	 	{
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('issue_date')])]['issue_qty'] += $value[csf('issue_qnty')];
			$rev_qty2[$value[csf('company_id')]]['issue_qty'] += $value[csf('issue_qnty')];

		}
        	//echo "<pre>";print_r($rev_qty);
		// Grey Roll Receive For Batch summary
		//===============user for roll receive for batch, location not in roll receive for batch page start
		$all_issue_barcode_arr = array_filter(array_unique($issue_barcode_no));
		if(count($all_issue_barcode_arr)>0)
		{
			$all_issue_barcode = implode(",", $all_issue_barcode_arr);
			$barcodeCond = $all_issue_barcode_cond = "";

			if($db_type==2 && count($all_issue_barcode_arr)>999)
			{
				$all_barcode_no_chunk=array_chunk($all_issue_barcode_arr,999) ;
				foreach($all_barcode_no_chunk as $chunk_arr)
				{
					$barcodeCond.=" c.barcode_no in(".implode(",",$chunk_arr).") or ";
				}

				$all_issue_barcode_cond.=" and (".chop($barcodeCond,'or ').")";
			}
			else
			{
				$all_issue_barcode_cond=" and c.barcode_no in($all_issue_barcode)";
			}

			$recv_roll_batch_sql="SELECT c.barcode_no from inv_receive_mas_batchroll a, pro_grey_batch_dtls b, pro_roll_details c
			where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and a.entry_form=62 and c.entry_form=62
			and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $all_issue_barcode_cond";
			$recv_roll_batch_sql_result=sql_select($recv_roll_batch_sql);
			$recv_batch_barcode_no=array();
			foreach ($recv_roll_batch_sql_result as $value)
		 	{
				$recv_batch_barcode_no[$value[csf('barcode_no')]]=$value[csf('barcode_no')];
			}
		}

		$all_recv_batch_barcode_arr = array_filter(array_unique($recv_batch_barcode_no));
		if(count($all_recv_batch_barcode_arr)>0)
		{
			$all_recv_batch_barcode = implode(",", $all_recv_batch_barcode_arr);
			$recv_batch_barcodeCond = $all_rcv_batch_barcode_cond = "";
			if($db_type==2 && count($all_recv_batch_barcode_arr)>999)
			{
				$all_barcode_no_chunk=array_chunk($all_recv_batch_barcode_arr,999) ;
				foreach($all_barcode_no_chunk as $chunk_arr)
				{
					$recv_batch_barcodeCond.=" d.barcode_no in(".implode(",",$chunk_arr).") or ";
				}

				$all_rcv_batch_barcode_cond.=" and (".chop($recv_batch_barcodeCond,'or ').")";
			}
			else
			{
				$all_rcv_batch_barcode_cond=" and d.barcode_no in($all_recv_batch_barcode)";
			}

			$issue_to_recv_batch_sql="SELECT a.issue_date, a.company_id, b.location_id, sum(d.qnty) as recv_batch_qty, d.barcode_no
			from inv_issue_master a, inv_transaction b, inv_grey_fabric_issue_dtls c, pro_roll_details d
			where a.id=b.mst_id and a.id=c.mst_id and b.id=c.trans_id and a.id=d.mst_id and c.id=d.dtls_id and a.entry_form=61 and d.entry_form=61 and b.item_category=13 and b.transaction_type=2
			and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_cond_sort_book $date_cond_tran $location_id_issue_cond $all_rcv_batch_barcode_cond
			group by a.issue_date, a.company_id, b.location_id, d.barcode_no order by a.issue_date";
			$issue_to_recv_batch_result=sql_select($issue_to_recv_batch_sql);
			foreach ($issue_to_recv_batch_result as $value)
		 	{
				$rev_qty[$value[csf('company_id')]][strtotime($value[csf('issue_date')])]['recv_batch_qty'] += $value[csf('recv_batch_qty')];
				$rev_qty2[$value[csf('company_id')]]['recv_batch_qty'] += $value[csf('recv_batch_qty')];
			}
		}
		//===============user for roll receive for batch, location not in roll receive for batch page end

		// Roll Issue Retn and Transfer In & Out
		$roll_issue_retn_transfer_sql="SELECT b.company_id, b.transaction_date, d.location_id,
		sum(case when  b.transaction_type=5 then b.cons_quantity else 0 end) as trans_in_qty,
		sum(case when  b.transaction_type=6 then b.cons_quantity else 0 end) as trans_out_qty
		from inv_item_transfer_mst a, inv_transaction b, lib_store_location d
		where  a.id=b.mst_id and b.store_id=d.id and b.item_category=13 and b.transaction_type in(4,5,6) and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_cond_transf $location_id_cond_y $date_cond_tran and a.entry_form=13
		group by b.company_id, b.transaction_date, d.location_id order by b.transaction_date";
		//echo $roll_issue_retn_transfer_sql;die;
		$roll_issue_retn_transfer_sql_result=sql_select($roll_issue_retn_transfer_sql);
		foreach ($roll_issue_retn_transfer_sql_result as $value)
	 	{

			// ===========
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('transaction_date')])]['trans_in_qty'] += $value[csf('trans_in_qty')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('transaction_date')])]['trans_out_qty'] += $value[csf('trans_out_qty')];
			$rev_qty2[$value[csf('company_id')]]['trans_in_qty'] += $value[csf('trans_in_qty')];
			$rev_qty2[$value[csf('company_id')]]['trans_out_qty'] += $value[csf('trans_out_qty')];
		}
		// echo "<pre>";print_r($rev_qty);
		// -----------------------finish Fabric  Transfer In & Out---------------
		$fabric_issue_retn_transfer_sql="SELECT b.company_id, b.transaction_date, d.location_id,
		sum(case when  b.transaction_type=4 then b.cons_quantity else 0 end) as issue_rtn_qty,
		sum(case when  b.transaction_type=5 then b.cons_quantity else 0 end) as finish_in_qty,
		sum(case when  b.transaction_type=6 then b.cons_quantity else 0 end) as finish_out_qty
		from inv_item_transfer_mst a, inv_transaction b, lib_store_location d
		where  a.id=b.mst_id and b.store_id=d.id and b.item_category=2 and b.transaction_type in(4,5,6) and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_cond_transf $location_id_cond_y $date_cond_tran and a.entry_form=14
		group by b.company_id, b.transaction_date, d.location_id order by b.transaction_date";
		//echo $fabric_issue_retn_transfer_sql;die;
		$fabric_issue_retn_transfer_sql_result=sql_select($fabric_issue_retn_transfer_sql);
		foreach ($fabric_issue_retn_transfer_sql_result as $value)
	 	{

			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('transaction_date')])]['finish_in_qty'] += $value[csf('finish_in_qty')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('transaction_date')])]['finish_out_qty'] += $value[csf('finish_out_qty')];
			$rev_qty2[$value[csf('company_id')]]['finish_in_qty'] += $value[csf('finish_in_qty')];
			$rev_qty2[$value[csf('company_id')]]['finish_out_qty'] += $value[csf('finish_out_qty')];
		}
		// echo "<pre>";print_r($rev_qty3);

		// ================================= batch =============================
		$batch_sqls = sql_select("SELECT a.working_company_id, a.batch_date, d.location_id,
		sum(case when a.batch_against in(1,3,5) and a.entry_form=0 then b.batch_qnty else 0 end) as batch_qty,
		sum(case when a.batch_against in(2) and a.entry_form=0 then b.batch_qnty else 0 end) as re_process_batch_qty
		from pro_batch_create_dtls b, pro_batch_create_mst a left join lib_prod_floor d on a.floor_id=d.id
		where a.id=b.mst_id and a.batch_against in(1,2,3,5) and a.entry_form=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $working_company_id_cond $location_id_cond_y $batch_date_cond
		group by a.working_company_id, a.batch_date, d.location_id order by a.batch_date");
		//and a.batch_no='RpC-BC-21-00014'
	 	foreach ($batch_sqls as $value)
	 	{
			$rev_qty[$value[csf('working_company_id')]][strtotime($value[csf('batch_date')])]['batch_qty'] += $value[csf('batch_qty')];
			$rev_qty[$value[csf('working_company_id')]][strtotime($value[csf('batch_date')])]['re_process_batch_qty'] += $value[csf('re_process_batch_qty')];
			$rev_qty2[$value[csf('working_company_id')]]['batch_qty'] += $value[csf('batch_qty')];
			$rev_qty2[$value[csf('working_company_id')]]['re_process_batch_qty'] += $value[csf('re_process_batch_qty')];
		}
		//========================= Subcon Batch Creation =================================
		$subc_batch_sqls = sql_select("SELECT a.company_id, a.batch_date, a.location_id,
		sum(case when a.batch_against in(1) and a.entry_form=36 then b.batch_qnty else 0 end) as subc_batch_qty,
		sum(case when a.batch_against in(2) and a.entry_form=36 then b.batch_qnty else 0 end) as subc_re_process_batch_qty
		from pro_batch_create_mst a, pro_batch_create_dtls b
		where a.id=b.mst_id and a.batch_against in(1,2) and a.entry_form=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_cond_sort_book $knit_lc_location_id_cond $batch_date_cond
		group by  a.company_id, a.batch_date, a.location_id order by a.batch_date");
	 	foreach ($subc_batch_sqls as $value)
	 	{
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('batch_date')])]['subc_batch_qty'] += $value[csf('subc_batch_qty')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('batch_date')])]['subc_re_process_batch_qty'] += $value[csf('subc_re_process_batch_qty')];
			$rev_qty2[$value[csf('company_id')]]['subc_batch_qty'] += $value[csf('subc_batch_qty')];
			$rev_qty2[$value[csf('company_id')]]['subc_re_process_batch_qty'] += $value[csf('subc_re_process_batch_qty')];
		}

	 	//===================================== dyeing summary =================================
		$dyeing_sqls = sql_select("SELECT a.service_company, a.process_end_date as production_date, d.location_id,
		sum(case when a.entry_form=35 then c.batch_qnty else 0 end) as prod_qty,
 		sum(case when a.entry_form=36 then c.batch_qnty else 0 end) as subc_prod_qty
		from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c, lib_prod_floor d
		where a.batch_id=b.id and a.floor_id=d.id and a.service_source=1 and a.entry_form in(35,36) and a.load_unload_id=2 and b.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.batch_against in(1,2,3) and a.result=1 and b.is_sales!=1 $date_cond2 $company_dyeing_cond $location_id_cond_y
		group by a.service_company, a.process_end_date, d.location_id order by a.process_end_date");
	 	foreach ($dyeing_sqls as $value)
	 	{
			$rev_qty[$value[csf('service_company')]][strtotime($value[csf('production_date')])]['dyeing'] += $value[csf('prod_qty')];
			$rev_qty2[$value[csf('service_company')]]['dyeing'] += $value[csf('prod_qty')];
			//[$location_id_arr[$value[csf('floor_id')]]]
			$rev_qty[$value[csf('service_company')]][strtotime($value[csf('production_date')])]['subc_dyeing'] += $value[csf('subc_prod_qty')];
			$rev_qty2[$value[csf('service_company')]]['subc_dyeing'] += $value[csf('subc_prod_qty')];
		}


		//============================ Trims dyeing summary ===========================
		$trims_dyeing_sqls = sql_select("SELECT a.service_company, a.process_end_date as production_date, d.location_id, sum(case when a.entry_form=35 then c.trims_wgt_qnty else 0 end) as trims_dyeing_qty
		from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_trims_dtls c, lib_prod_floor d
		where a.batch_id=b.id and b.id=c.mst_id and a.floor_id=d.id and a.entry_form in(35) and a.load_unload_id=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.batch_against in(1,2,3) and a.result=1 and b.is_sales!=1 $date_cond2 $company_dyeing_cond $location_id_cond_y
		group by a.service_company, a.process_end_date, d.location_id order by a.process_end_date");
	 	foreach ($trims_dyeing_sqls as $value)
	 	{
			$rev_qty[$value[csf('service_company')]][strtotime($value[csf('production_date')])]['trims_dyeing_qty'] += $value[csf('trims_dyeing_qty')];
			$rev_qty2[$value[csf('service_company')]]['trims_dyeing_qty'] += $value[csf('trims_dyeing_qty')];
		}



		$dyeing_finishing_sql="SELECT x.receive_date, x.knitting_company,x.location_id, sum(x.dye_fin_qty_in) as dye_fin_qty_in, sum(x.dye_fin_qty_out) as dye_fin_qty_out
		FROM ( SELECT a.knitting_source, a.receive_date, a.knitting_company, b.floor, a.recv_number, a.knitting_location_id as location_id,b.order_id,b.batch_id,b.gsm, b.receive_qnty as dye_fin_qty_in, 0 as dye_fin_qty_out,
		b.reject_qty,b.color_id,b.fabric_description_id,b.buyer_id, c.batch_no, a.recv_number, c.booking_no, c.booking_without_order,c.extention_no,b.remarks
		from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c
		where a.id=b.mst_id and b.batch_id = c.id and a.entry_form in(7,66) and a.item_category=2 and a.knitting_source=1 and a.status_active=1 and a.is_deleted=0 and c.status_active =1 $date_cond_knit $company_knit_cond $location_id_cond
		group by a.knitting_source, a.receive_date, a.knitting_company, b.floor, a.recv_number, a.knitting_location_id,b.order_id,b.batch_id,b.gsm, b.receive_qnty,
		b.reject_qty,b.color_id,b.fabric_description_id,b.buyer_id, c.batch_no, a.recv_number, c.booking_no, c.booking_without_order,c.extention_no,b.remarks
		UNION ALL
		SELECT a.knitting_source, a.receive_date, a.knitting_company, b.floor, a.recv_number, a.knitting_location_id as location_id, b.order_id,b.batch_id,b.gsm, 0 as dye_fin_qty_in, b.receive_qnty as dye_fin_qty_out,
		b.reject_qty,b.color_id,b.fabric_description_id,b.buyer_id, c.batch_no, a.recv_number, c.booking_no, c.booking_without_order,c.extention_no,b.remarks
		from inv_receive_master a, pro_finish_fabric_rcv_dtls b , pro_batch_create_mst c
		where a.id=b.mst_id and b.batch_id = c.id and a.entry_form in(7,66) and a.item_category=2 and a.knitting_source=3 and a.status_active=1 and a.is_deleted=0 and c.status_active =1 $date_cond_knit $company_cond_sort_book $knit_lc_location_id_cond
		group by a.knitting_source, a.receive_date, a.knitting_company, b.floor, a.recv_number, a.knitting_location_id,b.order_id,b.batch_id,b.gsm, b.receive_qnty,
		b.reject_qty,b.color_id,b.fabric_description_id,b.buyer_id, c.batch_no, a.recv_number, c.booking_no, c.booking_without_order,c.extention_no,b.remarks ) x
		group by x.receive_date, x.knitting_company,x.location_id order by x.receive_date";

		$dyeing_finishing_result=sql_select($dyeing_finishing_sql);
		foreach ($dyeing_finishing_result as $value)
		{
			$rev_qty[$value[csf('knitting_company')]][strtotime($value[csf('receive_date')])]['dye_fin_qty_in'] += $value[csf('dye_fin_qty_in')];
			$rev_qty[$value[csf('knitting_company')]][strtotime($value[csf('receive_date')])]['dye_fin_qty_out'] += $value[csf('dye_fin_qty_out')];
			$rev_qty2[$value[csf('knitting_company')]]['dye_fin_qty_in'] += $value[csf('dye_fin_qty_in')];
			$rev_qty2[$value[csf('knitting_company')]]['dye_fin_qty_out'] += $value[csf('dye_fin_qty_out')];
		}
		$subc_dyeing_fini_sql="SELECT a.company_id, a.product_date, a.location_id, sum(b.product_qnty) as fin_product_qnty from subcon_production_mst a, subcon_production_dtls b
		where a.id=b.mst_id and a.entry_form=292 $company_cond_sort_book $knit_lc_location_id_cond $date_cond_subc and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.company_id, a.product_date, a.location_id order by a.product_date";
		$subc_dyeing_fini_result=sql_select($subc_dyeing_fini_sql);
		foreach ($subc_dyeing_fini_result as $value)
		{
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('product_date')])]['fin_product_qnty'] += $value[csf('fin_product_qnty')];
			$rev_qty2[$value[csf('company_id')]]['fin_product_qnty'] += $value[csf('fin_product_qnty')];
		}

		//================================= Finish Fabric Roll Receive ===============================
		$finish_fab_roll_recv_sql="SELECT  a.company_id, a.receive_date,  a.location_id,
		sum(case when a.entry_form=37 and b.transaction_type=1 then b.cons_quantity else 0 end) as fin_roll_rcv_qty,
		sum(case when a.entry_form=126 and b.transaction_type=4 then b.cons_quantity else 0 end) as fin_roll_iss_rtn_qty
		from inv_receive_master a, inv_transaction b
		where a.id=b.mst_id and b.item_category=2 and a.entry_form in(37,126) and b.transaction_type in(1,4) $date_cond_knit $company_cond_sort_book $knit_lc_location_id_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.company_id, a.receive_date,  a.location_id order by a.receive_date";
		//echo $finish_fab_roll_recv_sql;
		$finish_fab_roll_recv_result=sql_select($finish_fab_roll_recv_sql);
		foreach ($finish_fab_roll_recv_result as $value)
		{
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('receive_date')])]['fin_roll_rcv_qty'] += $value[csf('fin_roll_rcv_qty')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('receive_date')])]['fin_roll_iss_rtn_qty'] += $value[csf('fin_roll_iss_rtn_qty')];
			$rev_qty2[$value[csf('company_id')]]['fin_roll_rcv_qty'] += $value[csf('fin_roll_rcv_qty')];
			$rev_qty2[$value[csf('company_id')]]['fin_roll_iss_rtn_qty'] += $value[csf('fin_roll_iss_rtn_qty')];
		}
		//========================= Finish Fabric Roll Issue =====================================
		$finish_fab_roll_issue_sql="SELECT b.company_id, b.transaction_date, d.location_id,sum(case when b.transaction_type=2 and b.item_category=2 then b.cons_quantity else 0 end) as fin_roll_issue_qty
		from inv_issue_master a, inv_transaction b, lib_store_location d
		where   a.id=b.mst_id and b.store_id=d.id and b.item_category in(2) and b.transaction_type in(2) and a.entry_form=18 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_cond_sort_book $location_id_cond_y $date_cond_tran
		group by b.company_id, b.transaction_date, d.location_id order by b.transaction_date";
	   //echo  $finish_fab_roll_issue_sql; die;

		$finish_fab_roll_issue_result=sql_select($finish_fab_roll_issue_sql);
		foreach ($finish_fab_roll_issue_result as $value)
		{
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('transaction_date')])]['fin_roll_issue_qty'] += $value[csf('fin_roll_issue_qty')];
			$rev_qty2[$value[csf('company_id')]]['fin_roll_issue_qty'] += $value[csf('fin_roll_issue_qty')];
		}
		//echo $finish_fab_roll_issue_sql;die;
		//Finish Fabric Roll Trasfer Recv

		//========================= Finish Fabric Roll Issue =====================================
		$rcv_date_cond = str_replace("b.transaction_date", "a.receive_date", $date_cond_tran);
		$sql = "SELECT a.company_id,a.receive_date, b.receive_qnty from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form=126 and a.item_category=2 and a.status_active=1 and b.status_active=1 $company_cond_sort_book $location_id_cond_y $rcv_date_cond";
		// echo $sql;die();
		$res = sql_select($sql);
		foreach ($res as $value)
		{
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('receive_date')])]['fin_roll_issue_rtn_qty'] += $value[csf('receive_qnty')];
			$rev_qty2[$value[csf('company_id')]]['fin_roll_issue_rtn_qty'] += $value[csf('receive_qnty')];
		}
		//============================== AOP Issue, Receive, Cutting Fabric Receive =========================
		$aop_issue_sql="SELECT a.company_id, a.receive_date,
		sum(case when a.entry_form=63 and a.process_id=35 then b.roll_wgt else 0 end) as aop_issue_qty,
		sum(case when a.entry_form=65 and a.process_id=0 then b.roll_wgt else 0 end) as aop_recv_qty,
		sum(case when a.entry_form=72 and a.process_id=0 then b.roll_wgt else 0 end) as cutting_fab_recv
		from inv_receive_mas_batchroll a, pro_grey_batch_dtls b
		where a.id=b.mst_id and a.entry_form in(63,65,72) and a.process_id in(35,0)  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_cond_sort_book $date_cond_knit group by a.company_id, a.receive_date order by a.receive_date";
		$aop_issue_result=sql_select($aop_issue_sql);
		foreach ($aop_issue_result as $value)
		{
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('receive_date')])]['aop_issue_qty'] += $value[csf('aop_issue_qty')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('receive_date')])]['aop_recv_qty'] += $value[csf('aop_recv_qty')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('receive_date')])]['cutting_fab_recv'] += $value[csf('cutting_fab_recv')];
			$rev_qty2[$value[csf('company_id')]]['aop_issue_qty'] += $value[csf('aop_issue_qty')];
			$rev_qty2[$value[csf('company_id')]]['aop_recv_qty'] += $value[csf('aop_recv_qty')];
			$rev_qty2[$value[csf('company_id')]]['cutting_fab_recv'] += $value[csf('cutting_fab_recv')];
		}
		//cutting qc
       $date_cond3 = " and a.cutting_qc_date between '$start_date' and '$end_date'";
	   $location_cond3 = ($location_id == 0) ? "" : " and a.location_id=$location_id";
		$cutt_qc_dtls=(" SELECT a.company_id,a.cutting_qc_date,a.production_source,a.location_id,sum(b.qc_pass_qty) as qc_pass_qty  from pro_gmts_cutting_qc_mst a,pro_gmts_cutting_qc_dtls b where b.mst_id=a.id $company_cond_sort_book $date_cond3 $location_cond3 and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 group by a.company_id, a.cutting_qc_date,production_source,a.location_id,b.qc_pass_qty order by a.cutting_qc_date ");
		//echo $cutt_qc_dtls;die;
		$cutt_qc=sql_select($cutt_qc_dtls);
		foreach ($cutt_qc as $value)
		{
				$rev_qty[$value[csf('company_id')]][strtotime($value[csf('cutting_qc_date')])]['cutting_qc'] += $value[csf('qc_pass_qty')];
				$rev_qty2[$value[csf('company_id')]]['cutting_qc'] += $value[csf('qc_pass_qty')];
		}

		$cutting_data=("SELECT a.company_id, c.size_qty as qty   ,a.entry_date ,a.source
			from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle c
			where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id   and  a.status_active=1 and a.is_deleted=0 and b.status_active=1
			 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $cutting_date_cond $company_cond3  order by a.entry_date ");
	      	//echo	 $cutting_data;die;


	   $cutting=sql_select($cutting_data);
			foreach($cutting as $row)
			{
				$rev_qty[$row[csf('company_id')]][strtotime($row[csf('entry_date')])]['cutting_lay'] += $row[csf('qty')];
				$rev_qty2[$row[csf('company_id')]]['cutting_lay'] += $row[csf('qty')];
			}
			//echo"<pre>";print_r($rev_qty2);die;

		//==================== Total Man Power, No of Operator, No of Helper ===================================
		$prod_source_sql="SELECT a.company_id, c.pr_date, a.location_id, sum(c.man_power) as tot_man_power, sum(c.operator) as tot_operator, sum(c.helper) as tot_helper,sum(c.working_hour) as working_hour,sum(c.total_smv) as smv ,sum (c.target_per_hour) as tot_target from prod_resource_mst a, prod_resource_dtls c
		where a.id=c.mst_id $company_cond_sort_book $knit_lc_location_id_cond $prod_sourc_date_cond
		and a.is_deleted=0 and c.is_deleted=0 group by a.company_id, c.pr_date, a.location_id order by c.pr_date";
		//echo $prod_source_sql;die;
		$prod_source_result=sql_select($prod_source_sql);
		$resource_data_arr = array();
		foreach ($prod_source_result as $value)
		{
			$resource_data_arr[$value[csf('company_id')]][strtotime($value[csf('pr_date')])]['man_power'] += $value[csf('tot_man_power')];
			$resource_data_arr[$value[csf('company_id')]][strtotime($value[csf('pr_date')])]['working_hour'] += $value[csf('working_hour')];
			$resource_data_arr[$value[csf('company_id')]][strtotime($value[csf('pr_date')])]['target'] += $value[csf('tot_target')];

			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('pr_date')])]['man_power'] += $value[csf('tot_man_power')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('pr_date')])]['operator'] += $value[csf('tot_operator')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('pr_date')])]['helper'] += $value[csf('tot_helper')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('pr_date')])]['working_hour'] += $value[csf('working_hour')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('pr_date')])]['smv'] += $value[csf('smv')];
			$rev_qty2[$value[csf('company_id')]]['man_power'] += $value[csf('tot_man_power')];
			$rev_qty2[$value[csf('company_id')]]['operator'] += $value[csf('tot_operator')];
			$rev_qty2[$value[csf('company_id')]]['helper'] += $value[csf('tot_helper')];
		}
		//echo "<pre>";print_r($resource_data_arr); die;
		//============================== garments_production ======================================
		$sqls = sql_select("SELECT d.style_ref_no, a.company_id,a.production_date,a.serving_company,a.location,a.production_type, a.embel_name, sum(b.production_qnty) as prod_quantity, a.production_source,a.po_break_down_id,a.item_number_id
		from pro_garments_production_mst a ,pro_garments_production_dtls b,wo_po_color_size_breakdown c,wo_po_details_master d
		where a.id=b.mst_id and a.production_type=b.production_type and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and  a.status_active=1 and d.id=c.job_id $date_cond $company_cond and a.is_deleted=0 $location_cond and b.status_active=1 and b.is_deleted=0
		group by d.style_ref_no, a.company_id,a.production_date,a.serving_company,a.location,a.production_type, a.embel_name, a.production_source,a.po_break_down_id,a.item_number_id order by a.production_date");
		//echo $sqls;die;
		//and a.production_type=5 and a.delivery_mst_id = 6546
		$sewing_data_array = array();
		foreach ($sqls as $value)
		{
			$lc_com_arr[$value[csf('company_id')]] = $value[csf('company_id')];
			$all_style_arr[$value[csf('style_ref_no')]] = $value[csf('style_ref_no')];

			if ($value[csf('production_type')] == 1)
			{// cutting inhouse
				$rev_qty[$value[csf('serving_company')]][strtotime($value[csf('production_date')])]['cutting_inhouse'] += $value[csf('prod_quantity')];
				$rev_qty2[$value[csf('serving_company')]]['cutting_inhouse'] += $value[csf('prod_quantity')];
			}
			else if ($value[csf('production_type')] == 5 )
			{
				//echo $value[csf('prod_quantity')]."<br>";
				$rev_qty[$value[csf('serving_company')]][strtotime($value[csf('production_date')])]['sewing_out'] += $value[csf('prod_quantity')];
				$rev_qty2[$value[csf('serving_company')]]['sewing_out'] += $value[csf('prod_quantity')];
			}
			else if ($value[csf('production_type')] == 4)
			{
				$rev_qty[$value[csf('serving_company')]][strtotime($value[csf('production_date')])]['sewing_in'] += $value[csf('prod_quantity')];
				$rev_qty2[$value[csf('serving_company')]]['sewing_in'] += $value[csf('prod_quantity')];
			}
			else if ($value[csf('production_type')] == 11)
			{
				$rev_qty[$value[csf('serving_company')]][strtotime($value[csf('production_date')])]['inhouse_poly_qty'] += $value[csf('prod_quantity')];
				$rev_qty2[$value[csf('serving_company')]]['inhouse_poly_qty'] += $value[csf('prod_quantity')];
			}
			else if ($value[csf('production_type')] == 7)
			{
				$rev_qty[$value[csf('serving_company')]][strtotime($value[csf('production_date')])]['iron'] += $value[csf('prod_quantity')];
				$rev_qty2[$value[csf('serving_company')]]['iron'] += $value[csf('prod_quantity')];
			}
			else if ($value[csf('production_type')] == 8)
			{
				$rev_qty[$value[csf('serving_company')]][strtotime($value[csf('production_date')])]['finish'] += $value[csf('prod_quantity')];
				$rev_qty2[$value[csf('serving_company')]]['finish'] += $value[csf('prod_quantity')];
			}
			else if ($value[csf('production_type')] == 2)
			{
				if ($value[csf('embel_name')] == 3)
				{
					$rev_qty[$value[csf('serving_company')]][strtotime($value[csf('production_date')])]['wash_issue'] += $value[csf('prod_quantity')];
					$rev_qty2[$value[csf('serving_company')]]['wash_issue'] += $value[csf('prod_quantity')];
				}
			}
			else if ($value[csf('production_type')] == 3)
			{
				if ($value[csf('embel_name')] == 3)
				{
					$rev_qty[$value[csf('serving_company')]][strtotime($value[csf('production_date')])]['wash_rcv'] += $value[csf('prod_quantity')];
					$rev_qty2[$value[csf('serving_company')]]['wash_rcv'] += $value[csf('prod_quantity')];
				}
			}
			if ($value[csf('production_type')] == 5)
			{
				$sewing_data_array[$value[csf('serving_company')]][strtotime($value[csf('production_date')])][$value[csf('po_break_down_id')]][$value[csf('item_number_id')]] += $value[csf('prod_quantity')];
			}
		}
		//echo "<pre>";print_r($rev_qty);die;

		$start_time_data_arr=sql_select("select company_name, shift_id, TO_CHAR(prod_start_time,'HH24:MI') as prod_start_time,TO_CHAR(lunch_start_time,'HH24:MI') as lunch_start_time from variable_settings_production where  company_name in($company_name) and  shift_id=1 and variable_list=26 and status_active=1 and is_deleted=0");
		//echo $start_time_data_arr;die;

      	foreach($start_time_data_arr as $row)
		{
			$start_time_arr[$row[csf('shift_id')]]['pst']=$row[csf('prod_start_time')];
			$start_time_arr[$row[csf('shift_id')]]['lst']=$row[csf('lunch_start_time')];
		}

        $prod_start_hour=$start_time_arr[1]['pst'];


		if($prod_start_hour=="") $prod_start_hour="08:00";
		$start_time=explode(":",$prod_start_hour);
		// $hour=substr($start_time[0],1,1);
		$hour = $start_time[0]*1;
		$time1 = strtotime($hour.":00");
		$time2 = strtotime(date('H:i:s'));
		$difference_hour = round(((abs($time2 - $time1) / 3600)-1),0);
		//echo $difference_hour;die;



		/* =================================================================================/
		/									SMV Source										/
		/================================================================================= */
		$lc_com_ids = implode(",",$lc_com_arr);
		$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($lc_com_ids) and variable_list=25 and status_active=1 and is_deleted=0");
		// echo $smv_source."sdsdsdds";die;
		if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
		if($smv_source==3)
		{
			$style_nos=implode("','",$all_style_arr);
			$color_type_ids="'".implode("','",$color_type_array)."'";
			$gsdSql="SELECT a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID,a.COLOR_TYPE  from PPL_GSD_ENTRY_MST a where a.APPLICABLE_PERIOD <= $txt_date and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 and a.BULLETIN_TYPE=4  and A.STYLE_REF in('".$style_nos."') and a.APPROVED=1
			group by a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID,a.COLOR_TYPE
			 ORDER BY  a.GMTS_ITEM_ID ,a.APPLICABLE_PERIOD  DESC , a.id DESC"; //and a.BULLETIN_TYPE in(3,4)
			// echo $gsdSql; die;
			$gsdSqlResult = sql_select($gsdSql);
			//$gsdDataArr=array();
			foreach($gsdSqlResult as $rows)
			{
				// echo $rows[TOTAL_SMV]."<br>";
				foreach($style_wise_po_arr[$rows['STYLE_REF']] as $po_id)
				{
					if($item_smv_array[$po_id][$rows['COLOR_TYPE']][$rows['GMTS_ITEM_ID']]=='')
					{
						$item_smv_array[$po_id][$rows['COLOR_TYPE']][$rows['GMTS_ITEM_ID']]=$rows['TOTAL_SMV'];
					}
					if($item_smv_array2[$po_id][$rows['GMTS_ITEM_ID']]=='')
					{
						$item_smv_array2[$po_id][$rows['GMTS_ITEM_ID']]=$rows['TOTAL_SMV'];
					}
				}
			}

		}
		else
		{
			$style_nos=implode("','",$all_style_arr);
			$sql_item="SELECT b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, smv_pcs, smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,2,3) and A.STYLE_REF_NO in('".$style_nos."')"; //echo $sql_item;die;
			$resultItem=sql_select($sql_item);
			foreach($resultItem as $itemData)
			{
				if($smv_source==1)
				{
					$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs')];
				}
				else if($smv_source==2)
				{
					$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs_precost')];
				}
			}
		}

		// print_r($sewing_data_array);
		$sewing_effi_array = array();
		$com_sewing_effi_arr = array();
		$com_acv_array = array();
		$sewing_acv_array = array();

		$com_sewing_effi_array = array();
		foreach ($sewing_data_array as $comkey => $com_data)
		{
			foreach ($com_data as $dtkey => $dt_data)
			{
				foreach ($dt_data as $pokey => $po_data)
				{
					foreach ($po_data as $itmkey => $itm_data)
					{
						//echo $itm_data;die;
						$sewing_effi_array[$comkey][$dtkey] += (($itm_data*$item_smv_array[$pokey][$itmkey])/($resource_data_arr[$comkey][$dtkey]['man_power']*$resource_data_arr[$comkey][$dtkey]['working_hour']*60))*100;

						 $sewing_acv_array[$comkey][$dtkey] += ($itm_data/($resource_data_arr[$comkey][$dtkey]['target']*$difference_hour))*100;
						// echo $difference_hour;die;

					//    $sewing_acv_array[$comkey][$dtkey] =$itm_data / (($resource_data_arr[$comkey][$dtkey]['target'])*($resource_data_arr[$comkey][$dtkey]['working_hour']));

						$com_sewing_effi_arr[$comkey] += (($itm_data*$item_smv_array[$pokey][$itmkey])/($resource_data_arr[$comkey][$dtkey]['man_power']*$resource_data_arr[$comkey][$dtkey]['working_hour']*60))*100;
						$com_acv_array[$comkey] += ($itm_data/($resource_data_arr[$comkey][$dtkey]['target']*$difference_hour))*100;


					}

				}
			}
		}
		//echo "<pre>";print_r($com_acv_array); die;


		// ==================================== emb data ======================================
		$company_cond_emb2 = str_replace("a.serving_company", "a.company_id", $company_cond_emb);
		$sqls_emb = "SELECT a.production_date,a.company_id,a.location,a.production_type, a.embel_name, sum(b.production_qnty) as prod_quantity,a.production_source
		from pro_garments_production_mst a ,pro_garments_production_dtls b,wo_po_color_size_breakdown c
		where a.id=b.mst_id and a.production_type=b.production_type and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and a.status_active=1 $date_cond $company_cond_emb2 and a.is_deleted=0 $location_cond_emb and b.status_active=1 and b.is_deleted=0 and a.production_type in(1,2,3)
		group by a.production_date,a.company_id,a.location,a.production_type, a.embel_name,a.production_source order by a.production_date";	//$company_cond_emb
		// echo $sqls_emb;die();
		$res = sql_select($sqls_emb);
	 	foreach($res as $value)
		{
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('production_date')])][$value[csf('production_type')]][$value[csf('embel_name')]]
			+=$value[csf('prod_quantity')];
			$rev_qty2[$value[csf('company_id')]][$value[csf('production_type')]][$value[csf('embel_name')]]
			+=$value[csf('prod_quantity')];


		}

		//echo "<pre>";print_r($rev_qty); die;


		//============================ Buyer Inspection ====================================
		$buyer_inspec_sql="SELECT b.company_name, a.working_company, a.inspection_date, a.working_location, sum(c.ins_qty) as ins_qty
		from pro_buyer_inspection a, pro_buyer_inspection_breakdown c, wo_po_details_master b , wo_po_break_down d
		where a.id=c.mst_id and a.po_break_down_id=d.id and d.job_no_mst=b.job_no $company_cond_order $working_location_cond $inspection_date_cond and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0
		group by b.company_name, a.working_company, a.inspection_date, a.working_location order by a.inspection_date ";
		$buyer_inspec_result=sql_select($buyer_inspec_sql);
		foreach ($buyer_inspec_result as $value)
		{
			$rev_qty[$value[csf('company_name')]][strtotime($value[csf('inspection_date')])]['ins_qty'] += $value[csf('ins_qty')];
			$rev_qty2[$value[csf('company_name')]]['ins_qty'] += $value[csf('ins_qty')];
		}

		// ============================== ex-factory data ========================================
		$gmt_shipment_sql="SELECT a.sys_number, a.company_id, a.location_id,b.invoice_no, a.delivery_date, b.po_break_down_id, a.entry_form,
		sum(case when a.entry_form<>85 then b.ex_factory_qnty else 0 end) as ship_qty,
		sum(case when a.entry_form<>85 then b.ex_factory_qnty*(c.unit_price/d.total_set_qnty) else 0 end) as ship_value,
		sum(case when a.entry_form=85 then b.ex_factory_qnty else 0 end) as ship_rtn_qty, (c.unit_price/d.total_set_qnty) as unit_price
		from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b, wo_po_break_down c,  wo_po_details_master d
		where a.id=b.delivery_mst_id and  b.po_break_down_id=c.id and c.job_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_cond_sort_book $knit_lc_location_id_cond $delivery_date_cond
		group by a.sys_number,a.company_id, a.location_id,b.invoice_no, a.delivery_date, b.po_break_down_id,c.unit_price, a.entry_form, d.total_set_qnty order by a.delivery_date";
		//echo $gmt_shipment_sql;die();
		$gmt_shipment_result=sql_select($gmt_shipment_sql);
		$invoice_id_arr = array();
		foreach ($gmt_shipment_result as $value)
		{
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('delivery_date')])]['ship_qty'] += $value[csf('ship_qty')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('delivery_date')])]['ship_value'] += $value[csf('ship_value')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('delivery_date')])]['ship_rtn_qty'] += $value[csf('ship_rtn_qty')];
			$rev_qty2[$value[csf('company_id')]]['ship_qty'] += $value[csf('ship_qty')];
			$rev_qty2[$value[csf('company_id')]]['ship_value'] += $value[csf('ship_value')];
			$rev_qty2[$value[csf('company_id')]]['ship_rtn_qty'] += $value[csf('ship_rtn_qty')];
			$invoice_id_arr[$value[csf('invoice_no')]] = $value[csf('invoice_no')];
		}

		// ==============================================
		$inv_id_cond = where_con_using_array($invoice_id_arr,0,"a.id");
		$sqlEx = sql_select("SELECT a.benificiary_id,b.ex_factory_date,a.invoice_value,a.net_invo_value from com_export_invoice_ship_mst a,pro_ex_factory_mst b where a.id=b.invoice_no and a.status_active=1 and b.status_active=1 $inv_id_cond");
		//echo $sqlEx;die;
		$shipment_net_val_arr = array();
		foreach ($sqlEx as $val)
		{
			$shipment_net_val_arr[$val[csf('benificiary_id')]][strtotime($val[csf('ex_factory_date')])] += $val[csf('net_invo_value')];
		}
		// echo "<pre>";print_r($shipment_net_val_arr);die();

		// Poly outbound, sewing output outbond
		$outbound="SELECT a.production_date,a.company_id,a.location,a.production_type, a.embel_name, sum(b.production_qnty) as prod_quantity, a.production_source
		from pro_garments_production_mst a ,pro_garments_production_dtls b,wo_po_color_size_breakdown c
		where a.id=b.mst_id and a.production_type=b.production_type and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and a.production_source=3 and a.production_type in(1,4,5,11)
		$date_cond $company_cond_sort_book $location_cond
		and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0
		group by a.production_date,a.company_id,a.location,a.production_type, a.embel_name, a.production_source order by a.production_date ";
		//echo $outbound;die;

		$sewing_output_outbond_result=sql_select($outbound);
		foreach ($sewing_output_outbond_result as $value)
		{
			if ($value[csf('production_type')] == 11) // Outbound poly entry
			{
				$rev_qty[$value[csf('company_id')]][strtotime($value[csf('production_date')])]['outbond_poly_qty'] += $value[csf('prod_quantity')];
				$rev_qty2[$value[csf('company_id')]]['outbond_poly_qty'] += $value[csf('prod_quantity')];
			}

			else if ($value[csf('production_type')] == 5) // Outbound Bundle Wise Sewing Output
			{
				$rev_qty[$value[csf('company_id')]][strtotime($value[csf('production_date')])]['sewing_out'] += $value[csf('prod_quantity')];
				$rev_qty2[$value[csf('company_id')]]['sewing_out'] += $value[csf('prod_quantity')];
			}
			else if ($value[csf('production_type')] == 1) // Outbound cutting qty (Cutting QC V2 page)
			{
				$rev_qty[$value[csf('company_id')]][strtotime($value[csf('production_date')])]['cutting_outbound_qty'] += $value[csf('prod_quantity')];
				$rev_qty2[$value[csf('company_id')]]['cutting_outbound_qty'] += $value[csf('prod_quantity')];
			}
		}
        //echo "<pre>";print_r($rev_qty); die;

		// Subcon Poly Entry, subc sewing output entry, subc Cutting Entry
		$subc_data_sql="SELECT a.production_date,a.company_id,a.location_id,a.production_type, sum(a.production_qnty) as subc_production_qnty
		from subcon_gmts_prod_dtls a where a.status_active=1 and a.is_deleted=0 $date_cond $company_cond_sort_book $knit_lc_location_id_cond
		group by a.production_date,a.company_id,a.location_id,a.production_type order by a.production_date "; // $location_cond
		$subc_data_result=sql_select($subc_data_sql);
		foreach ($subc_data_result as $value)
		{
			if ($value[csf('production_type')] == 11) // subcon poly entry
			{
				$rev_qty[$value[csf('company_id')]][strtotime($value[csf('production_date')])]['subc_poly_qty'] += $value[csf('subc_production_qnty')];
				$rev_qty2[$value[csf('company_id')]]['subc_poly_qty'] += $value[csf('subc_production_qnty')];
			}
			else if ($value[csf('production_type')] == 4) // subc sewing output entry
			{
				$rev_qty[$value[csf('company_id')]][strtotime($value[csf('production_date')])]['subc_sewing_in_output_qty'] += $value[csf('subc_production_qnty')];
				$rev_qty2[$value[csf('company_id')]]['subc_sewing_in_output_qty'] += $value[csf('subc_production_qnty')];
			}
			else if ($value[csf('production_type')] == 5) // subc sewing output entry
			{
				$rev_qty[$value[csf('company_id')]][strtotime($value[csf('production_date')])]['subc_sewing_output_qty'] += $value[csf('subc_production_qnty')];
				$rev_qty2[$value[csf('company_id')]]['subc_sewing_output_qty'] += $value[csf('subc_production_qnty')];
			}
			else if ($value[csf('production_type')] == 1) // subc Cutting Entry
			{
				$rev_qty[$value[csf('company_id')]][strtotime($value[csf('production_date')])]['subc_cutting_qty'] += $value[csf('subc_production_qnty')];
				$rev_qty2[$value[csf('company_id')]]['subc_cutting_qty'] += $value[csf('subc_production_qnty')];
			}
		}
		// echo "<pre>";print_r($rev_qty);

		// ============================= Commercial Reference Closing =================================
		$ref_close_data=sql_select("SELECT a.company_id, a.closing_date, a.reference_type, a.closing_status, a.inv_pur_req_mst_id, a.mrr_system_no, sum(d.order_quantity*c.total_set_qnty) as ref_closing_qty
		from inv_reference_closing a, wo_po_break_down b, wo_po_details_master c,wo_po_color_size_breakdown d where a.inv_pur_req_mst_id=b.id and b.job_id=c.id and  b.id=d.po_break_down_id and a.closing_status=1 $company_cond_sort_book $closing_date_cond
		group by a.company_id, a.closing_date, a.reference_type, a.closing_status, a.inv_pur_req_mst_id, a.mrr_system_no order by a.closing_date");
		foreach ($ref_close_data as $value)
		{
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('closing_date')])]['ref_closing_qty'] += $value[csf('ref_closing_qty')];

			$rev_qty2[$value[csf('company_id')]]['ref_closing_qty'] += $value[csf('ref_closing_qty')];
		}

		// =============== Printing Production in-house ===============
		$printing_production_sql = "SELECT a.company_id,a.sys_no, b.production_date, a.location_id, b.qcpass_qty,c.within_group
		FROM subcon_embel_production_mst a, subcon_embel_production_dtls b,subcon_ord_mst c
	    WHERE
	    a.id =b.mst_id and
		c.embellishment_job=a.job_no
		and a.entry_form='222'
		and c.within_group in (1,2)
	    $printing_date_cond $company_cond_sort_book ";
		// echo $printing_production_sql;
		$printing_production_sql_result= sql_select($printing_production_sql);
		foreach($printing_production_sql_result as $row)
		{
          $rev_qty[$row[csf('company_id')]][strtotime($row[csf('production_date')])][$row[csf('within_group')]]['printing_production_qty'] += $row[csf('qcpass_qty')];
          $rev_qty2[$row[csf('company_id')]][$row[csf('within_group')]]['printing_production_qty'] += $row[csf('qcpass_qty')];
		}
		// echo"<pre>";
		// print_r($rev_qty);
		// =============== Printing Delivery ===============
		$printing_delivery_sql = "SELECT a.company_id,
		a.delivery_no,
		a.delivery_date,
		a.location_id,
		a.within_group,
		b.delivery_qty
        FROM subcon_delivery_mst a, subcon_delivery_dtls b
        WHERE     a.id = b.mst_id
		and a.entry_form='254'
		and a.within_group in (1,2)
		$printing_delivery_date_cond $company_cond_sort_book";
		//echo $printing_delivery_sql;die;
		$printing_delivery_sql_result= sql_select($printing_delivery_sql);
		foreach($printing_delivery_sql_result as $row)
		{
          $rev_qty[$row[csf('company_id')]][strtotime($row[csf('delivery_date')])][$row[csf('within_group')]]['printing_delivery_qty'] += $row[csf('delivery_qty')];
          $rev_qty2[$row[csf('company_id')]][$row[csf('within_group')]]['printing_delivery_qty'] += $row[csf('delivery_qty')];
		}
		// echo"<pre>";
		// print_r($rev_qty2);
		// =============== Embroidery Production inhouse ===============
		$embroidery_production_sql = "SELECT a.company_id,a.sys_no, b.production_date, a.location_id, b.qcpass_qty
		FROM subcon_embel_production_mst a, subcon_embel_production_dtls b
	    WHERE
	    a.id =b.mst_id
		and a.entry_form='315'
	    $printing_date_cond $company_cond_sort_book ";
		//echo $embroidery_production_sql;die;

		$embroidery_production_sql_result= sql_select($embroidery_production_sql);
		foreach($embroidery_production_sql_result as $row)
		{
          $rev_qty[$row[csf('company_id')]][strtotime($row[csf('production_date')])]['embroidery_production_qty'] += $row[csf('qcpass_qty')];
          $rev_qty2[$row[csf('company_id')]]['embroidery_production_qty'] += $row[csf('qcpass_qty')];
		}
		// 	echo"<pre>";
		// print_r($rev_qty);
		// =============== Embroidery Delivery ===============
		$embroidery_delivery_sql = "SELECT a.company_id,
		a.delivery_no,
		a.delivery_date,
		a.location_id,
		b.delivery_qty,
		a.within_group
        FROM subcon_delivery_mst a, subcon_delivery_dtls b
        WHERE     a.id = b.mst_id
		AND a.entry_form = '325'
		and a.within_group in (1,2)
		$printing_delivery_date_cond $company_cond_sort_book";
		//echo $embroidery_delivery_sql; die;
		$embroidery_delivery_sql_result= sql_select($embroidery_delivery_sql);
		foreach($embroidery_delivery_sql_result as $row)
		{
          $rev_qty[$row[csf('company_id')]][strtotime($row[csf('delivery_date')])][$row[csf('within_group')]]['embroidery_delivery_qty'] += $row[csf('delivery_qty')];
          $rev_qty2[$row[csf('company_id')]][$row[csf('within_group')]]['embroidery_delivery_qty'] += $row[csf('delivery_qty')];
		}
		// 	echo"<pre>";
		// print_r($rev_qty); die;
		// =============== Hang Tag ===============
		$hang_tag_sql = "SELECT a.company_id,
		a.production_date,
		a.location,
		a.production_quantity,
		a.production_type
        FROM pro_garments_production_mst a
        WHERE
		a.production_type='15'
	    $hang_tag_date_cond $company_cond_sort_book";
		// echo $hang_tag_sql;
		$hang_tag_sql_result= sql_select($hang_tag_sql);
		foreach($hang_tag_sql_result as $row)
		{
          $rev_qty[$row[csf('company_id')]][strtotime($row[csf('production_date')])]['hang_tag_qty'] += $row[csf('production_quantity')];
          $rev_qty2[$row[csf('company_id')]]['hang_tag_qty'] += $row[csf('production_quantity')];
		}
		// echo"<pre>";
		// print_r($rev_qty);
		$date_cond4 = " and a.subcon_date between '$start_date' and '$end_date'";
		$location_cond5 = ($location_id == 0) ? "" : " and a.location_id=$location_id";
		$printing_sql="SELECT a.company_id,a.within_group ,a.subcon_date,b.quantity FROM sub_material_mst a, sub_material_dtls b  WHERE a.id=b.mst_id   and a.entry_form=205 and a.within_group in(1,2) and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 $company_cond_sort_book $location_cond5 $date_cond4 ";
		//echo $printing_sql;die;

		$sql_result_print_rev= sql_select($printing_sql);
		//unset($rev_qty);
		foreach($sql_result_print_rev as $row)
		{
          $rev_qty[$row[csf('company_id')]][strtotime($row[csf('subcon_date')])][$row[csf('within_group')]]['print_rcv_qty'] += $row[csf('quantity')];
          $rev_qty2[$row[csf('company_id')]][$row[csf('within_group')]]['print_rcv_qty'] += $row[csf('quantity')];
		}
		//echo"<pre>";
		//print_r($rev_qty2);die;
		$emb_sql="SELECT a.company_id,a.within_group,a.subcon_date,b.quantity FROM sub_material_mst a, sub_material_dtls b  WHERE a.id=b.mst_id   and a.entry_form=312 and a.within_group in(1,2) and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 $company_cond_sort_book $location_cond4 $date_cond4 ";
		//echo $emb_sql;die;

		$sql_result_print_rev= sql_select($emb_sql);
		//unset($rev_qty);
		foreach($sql_result_print_rev as $row)
		{
          $rev_qty[$row[csf('company_id')]][strtotime($row[csf('subcon_date')])][$row[csf('within_group')]]['embo_rcv_qty'] += $row[csf('quantity')];
          $rev_qty2[$row[csf('company_id')]]['embo_rcv_qty'] += $row[csf('quantity')];
		}
		// 	echo"<pre>";
		// print_r($rev_qty);die;


		ob_start();
		?>



	    <fieldset style="width: 4750px; margin:20px auto;">
	        <table cellpadding="0" cellspacing="0" style="text-align: center; width: 100%">
	            <tr class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="10"><strong style="font-size:25px"><?
					$com_name = return_field_value("company_name", "lib_company", "id=" . $cbo_company_name, "company_name");
					echo $com_name; ?></strong></td>
	            </tr>

	            <tr class="form_caption" style="border:none;">
	                <td align="center" width="100%" colspan="10">
	                    <strong style="font-size:16px">Production Summary:
							<?
							if ($start_date != '') echo change_date_format($start_date) . ' To ' . change_date_format($end_date); else echo '';
							?>
	                    </strong>
	                </td>
	            </tr>

	            <tr>
	                <td colspan="10" width="100%"style="text-align: center; font-size:16px">Date of Print:&nbsp&nbsp<? echo date('d-m-Y'); ?></td>
	            </tr>

	            <tr>
	                <td colspan="10" width="100%" style="text-align: center; font-size:16px;">Time of Print:&nbsp&nbsp<? echo date('h:i A'); ?></td>
	            </tr>
	        </table>
	        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
	            <thead>
					<tr>
						<th colspan="7">Order Information</th>

						<th colspan="2">Booking Information </th>
						<th colspan="4">Yarn Store </th>
						<th colspan="8">Greige Fabric Store </th>
						<th colspan="6">Cutting</th>
						<th colspan="3">Printing</th>
						<th colspan="3">Embroidery</th>
						<th colspan="7">Sewing</th>
						<th colspan="4">GMT Finishing</th>
						<th colspan="4">Export/Shipment</th>



					</tr>
		            <tr>
		                <th width="30"><p>SL</p></th>
		                <th width="100"><p>Production Date</p></th>
		                <th width="100"><p>Projection Order [PCS]</p></th>
		                <th width="100"><p>Confirm Order [PCS]</p></th>
		                <th width="100"><p>Cancel Order [PCS]</p></th>
						<th width="100"><p>InActive Order [PCS]</p></th>
		                <th width="100"><p>Reference Close [Pcs]</p></th>




						<th width="100"><p>Main Fabric Booking Qty</p></th>
						<th width="100"><p>Short/EFR Booking</p></th>

		                <th width="100"><p>Grey Yarn Received</p></th>
		                <th width="100"><p>Grey Yarn Issued</p></th>
		                <th width="100"><p>Dyed Yarn Received</p></th>
		                <th width="100"><p>Dyed Yarn Issued</p></th>


		                <th width="100"><p>Greige Fabric Received</p></th>
		                <th width="100"><p>Greige Fabric issued</p></th>
		                <th width="100"><p>Greige Fabric Transfer Rcv</p></th>
		                <th width="100"><p>Greige Fabric Transfer Issued</p></th>

		                <th width="100"><p>Finish Fabric Receive</p></th>
		                <th width="100"><p>Finish Fabric Issue</p></th>
		                <th width="100"><p>Finish Fabric Trasfer Recv</p></th>
		                <th width="100"><p>Finish Fabric Trasfer Issue</p></th>



		                <th width="100"><p>Cutting Lay</p></th>
						<th width="100"><p>Cutting QC</p></th>
		                <th width="100"><p>Cutting Send To Print</p></th>
						<th width="100"><p>Cutting  Rcv From Print </p></th>
						<th width="100"><p>Cutting Send to Embroidery </p></th>
						<th width="100"><p>Cutting Rcv from Embroidery </p></th>


						<th width="100"><p>Print  Rcv </p></th>
		                <th width="100"><p>Print Production</p></th>
		                <th width="100"><p>Print Delivery to Cutting</p></th>


						<th width="100"><p>EMB   Rcv </p></th>
		                <th width="100"><p>EMB  Production</p></th>
		                <th width="100"><p>EMB Delivery to Cutting </p></th>


						<th width="100"><p>Total Man Power</p></th>
						<th width="100"><p>No of Operator</p></th>
						<th width="100"><p>No of Helper</p></th>
						<th width="100"><p>Sewing Input</p></th>
						<th width="100"><p>Sewing Output</p></th>
						<th width="100"><p>Achivement %</p></th>
						<th width="100"><p>Sewing Efficiency %</p></th>

						<th width="100"><p>Iron</p></th>
						<th width="100"><p>Hangtag</p></th>
						<th width="100"><p>Poly</p></th>
						<th width="100"><p>Pack.And Finis.</p></th>


						<th width="100"><p>Shipment Qty</p></th>
						<th width="100"><p>Ship. Gross Value $</p></th>
						<th width="100"><p>Ship. Net Value $</p></th>
		                <th width="100"><p>Ship. Retn. Qty</p></th>
		            </tr>
	            </thead>
				<?php

				// echo"<pre>";
				// print_r($rev_qty);
				$date_arr = array();
				$date_fri_arr = array();
				$month_year_check_arr = array();
				// echo"<pre>";
				// print_r($rev_qty);
				foreach ($rev_qty as $company => $result_row)
				{
					?>
					<tr>
						<th style="background: #C2DCFF;"></th>
						<th colspan="71" style="background: #C2DCFF; text-align: left; padding-left: 15px;">
							<?=$company_arr[$company];?>
						</th>
					</tr>
					<?php
					ksort($result_row);
					$i = 1;
								$sub_total_projec_qty=0;$sub_total_confirm_qty=0;$sub_total_cancel_order_qty=0;$sub_total_inactive_qty =0;
								$sub_total_ref_closing_qty=0;$sub_total_main_grey_qty=0;$sub_total_sort_grey_qty=0;$sub_total_grey_recv=0;$sub_total_grey_iss=0;$sub_total_dyed_recv=0;$month_total_dyed_iss=0;$sub_total_recv_qty=0;$sub_total_issue_qty=0;$sub_total_trans_in_qty=0;$sub_total_trans_out_qty=0;$sub_total_fin_roll_rcv_qty=0;$sub_total_fin_roll_issue_qty=0;$sub_total_finish_in_qty=0;$sub_total_finsh_out_qty=0;$sub_total_cutting_lay=0;$sub_total_cutting_qc=0;$sub_total_cut_send_print=0;$sub_total_cut_rcv_print=0;$sub_total_emb_issue=0;$sub_total_emb_rcv=0;$sub_total_print_in_rcv=0;$sub_total_print_prod_in=0;$sub_total_print_delev_in=0;$sub_total_embo_in=0;$sub_total_emb_prod_in=0;$sub_total_emb_delev_in=0;$sub_total_man_power=0;$sub_total_operator=0;$sub_total_helper=0;$sub_total_sewing_in=0;$sub_total_sewing_out=0;
								$sub_total_achivement=0;$sub_total_sewing_efficiency=0;$sub_total_iron_qty=0;$sub_total_hangtag_qty=0;$sub_total_poly_qty =0;$sub_total_finish_qty=0;$sub_total_ship_qty=0;$sub_total_ship_value=0;
								$$sub_total_shipment_net_value_qty=0;$$sub_total_ship_rtn_qty=0;
					$date_count = count($locationValue);

								$month_total_projec_qty=0;$month_total_confirm_qty=0;$month_total_cancel_order_qty=0;$month_total_inactive_qty =0;
								$month_total_ref_closing_qty=0;$month_total_main_grey_qty=0;$month_total_sort_grey_qty=0;$month_total_grey_recv=0;$month_total_grey_iss=0;$month_total_dyed_recv=0;$month_total_dyed_iss=0;$month_total_recv_qty=0;$month_total_issue_qty=0;$month_total_trans_in_qty=0;$month_total_trans_out_qty=0;$month_total_fin_roll_rcv_qty=0;$month_total_fin_roll_issue_qty=0;$month_total_finish_in_qty=0;$month_total_finsh_out_qty=0;$month_total_cutting_lay=0;$month_total_cutting_qc=0;$month_total_cut_send_print=0;$month_total_cut_rcv_print=0;$month_total_emb_issue=0;$month_total_emb_rcv=0;$month_total_print_in_rcv=0;$month_total_print_prod_in=0;$month_total_print_delev_in=0;$month_total_embo_in=0;$month_total_emb_prod_in=0;$month_total_emb_delev_in=0;$month_total_man_power=0;$month_total_operator=0;$month_total_helper=0;$month_total_sewing_in=0;$month_total_sewing_out=0;
								$month_total_achivement=0;$month_total_sewing_efficiency=0;$month_total_iron_qty=0;$month_total_hangtag_qty=0;$month_total_poly_qty =0;$month_total_finish_qty=0;$month_total_ship_qty=0;$month_total_ship_value=0;
								$month_total_shipment_net_value_qty=0;$month_total_ship_rtn_qty=0;


					foreach ($result_row as $date => $qtyValue)
					{
						if (!in_array($date, $date_arr))
						{
							$date_arr[] = $date;
						}
						if ($i % 2 == 1) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

						// ----------------------- Month Total start a ----------------------
						if (!in_array(date('F, Y',$date), $month_year_check_arr2))
						{
							if ($i != 1)
							{
								?>

								<?php
								$month_total_projec_qty=0;$month_total_confirm_qty=0;$month_total_cancel_order_qty=0;$month_total_inactive_qty =0;
								$month_total_ref_closing_qty=0;$month_total_main_grey_qty=0;$month_total_sort_grey_qty=0;$month_total_grey_recv=0;$month_total_grey_iss=0;$month_total_dyed_recv=0;$month_total_dyed_iss=0;$month_total_recv_qty=0;$month_total_issue_qty=0;$month_total_trans_in_qty=0;$month_total_trans_out_qty=0;$month_total_fin_roll_rcv_qty=0;$month_total_fin_roll_issue_qty=0;$month_total_finish_in_qty=0;$month_total_finsh_out_qty=0;$month_total_cutting_lay=0;$month_total_cutting_qc=0;$month_total_cut_send_print=0;$month_total_cut_rcv_print=0;$month_total_emb_issue=0;$month_total_emb_rcv=0;$month_total_print_in_rcv=0;$month_total_print_prod_in=0;$month_total_print_delev_in=0;$month_total_embo_in=0;$month_total_emb_prod_in=0;$month_total_emb_delev_in=0;$month_total_man_power=0;$month_total_operator=0;$month_total_helper=0;$month_total_sewing_in=0;$month_total_sewing_out=0;
								$month_total_achivement=0;$month_total_sewing_efficiency=0;$month_total_iron_qty=0;$month_total_hangtag_qty=0;$month_total_poly_qty =0;$month_total_finish_qty=0;$month_total_ship_qty=0;$month_total_ship_value=0;
								$month_total_shipment_net_value_qty=0;$month_total_ship_rtn_qty=0;



							}
							$month_year_check_arr2[] = date('F, Y',$date);
						}
						// ----------------------- Month Total End a ------------------------

						// ===========Month, Year
						if (!in_array(date('F, Y',$date), $month_year_check_arr))
						{
							?>
                            <tr>
                            	<th style="background: #C2DCFF;"></th>
                                <th colspan="71" style="background: #C2DCFF; text-align: left; padding-left: 15px;">
									<?php echo date('F, Y',$date);?>
                                </th>
                            </tr>
							<?php
							$month_year_check_arr[] = date('F, Y',$date);
						}

						$knitting_qty=$qtyValue['knitting_qty_in'] + $qtyValue['knitting_qty_out'];
						$knitting_qty_out=$qtyValue['knitting_qty_out']+$qtyValue['subc_knitting_qty_out'];

						$recv_qty=$qtyValue['recv_qty_in']+$qtyValue['recv_qty_out']+$qtyValue['issue_rtn_qty'];

						$batch_qty=$qtyValue['batch_qty']+$qtyValue['subc_batch_qty'];
						$re_process_batch_qty=$qtyValue['re_process_batch_qty']+$qtyValue['subc_re_process_batch_qty'];

						$dyeing_in=$qtyValue['dyeing']+$qtyValue['subc_dyeing'];
						$dye_fini_in_qty=$qtyValue['dye_fin_qty_in']+$qtyValue['fin_product_qnty'];
						$poly_qty=$qtyValue['inhouse_poly_qty']+$qtyValue['outbond_poly_qty']+$qtyValue['subc_poly_qty'];


						$sewing_inbound_qty=$qtyValue['sewing_inhouse_qty']+$qtyValue['subc_sewing_output_qty'];
						$cutting_inhouse_qty=$qtyValue['cutting_inhouse']+$qtyValue['subc_cutting_qty'];

						$fin_roll_rcv_qty=$qtyValue['fin_roll_rcv_qty'];

						//  for efficiency
						$prod_min = ($qtyValue['sewing_in'] + $qtyValue['sewing_out'])*$qtyValue['smv'];
						$effi_min = $qtyValue['man_power']*$qtyValue['working_hour']*60;
						$sewing_effi = ($effi_min>0) ? ($prod_min / $effi_min)*100 : 0;

						$cutting_fab_recv = $qtyValue['fin_roll_issue_qty']-$qtyValue['fin_roll_issue_rtn_qty'];
						$shipment_net_value = $shipment_net_val_arr[$company][$date];

						$total_print_issue_qty=$qtyValue[2][1][1]+ $qtyValue[2][1][3];

						$total_emb_issue_qty= $qtyValue[2][2][1]+ $qtyValue[2][2][3];
						$total_emb_rcv_qty= $qtyValue[3][2][1]+ $qtyValue[3][2][3];
						$printing_rcv= $qtyValue[1]['print_rcv_qty']+ $qtyValue[2]['print_rcv_qty'];

						$printing_production= $qtyValue[1]['printing_production_qty']+ $qtyValue[2]['printing_production_qty'];
						$printing_delivery= $qtyValue[1]['printing_delivery_qty']+ $qtyValue[2]['printing_delivery_qty'];
						$emb_delivery= $qtyValue[1]['embroidery_delivery_qty']+ $qtyValue[2]['embroidery_delivery_qty'];
						//echo $emb_delivery."679g";
						$embo_rcv= $qtyValue[1]['embo_rcv_qty']+ $qtyValue[2]['embo_rcv_qty'];
						//$total_cutting_lay=$qtyValue['cutting_lay']+$qtyValue['cutting_outbound_qty'];

						// echo $company."=".$date."<br>1643479200";
						?>
                        <tr bgcolor="<? $timestamp = $date;$day_name= date("l", $timestamp );
                        	if($day_name=="Friday"){echo "crimson";} else{echo $bgcolor; }?>" style="<?if($day_name=="Friday"){echo 'color:white;';}?>">
							            <td align="center"><p> <?echo $i;?></p></td>
										<td align="center"><p><? echo date('d-m-Y',$date);?></p></td>
										<td align="right"><p><? echo number_format($qtyValue['projec_qty_pcs'],2,'.',''); ?></p></td>
										<td align="right"><p><? echo number_format($qtyValue['confirm_qty_pcs'],2,'.',''); ?></p></td>
										<td align="right"><p><? echo number_format($qtyValue['cancel_order_qty'],2,'.',''); ?></p></td>
										<td align="right"><p><?=number_format($qtyValue['inactive_qty_pcs'],2,'.','');?></p></td>
										<td align="right"><p><? echo number_format($qtyValue['ref_closing_qty'],2,'.',''); ?></p</td>



										<td align="right"><p><? echo number_format($qtyValue['main_grey_qty'],2,'.',''); ?></p></td>

										<td align="right"><p><? echo number_format($qtyValue['sort_grey_qty'],2,'.',''); ?></p></td>

										<td align="right"><p><? echo number_format($qtyValue['grey_recv_total'],2,'.',''); ?></p></td>
										<td align="right"><p><? echo number_format($qtyValue['grey_iss_total'],2,'.',''); ?></p></td>
										<td align="right"><p><? echo number_format($qtyValue['dyed_recv_total'],2,'.',''); ?></p></td>
										<td align="right"><p><? echo number_format($qtyValue['dyed_iss_total'],2,'.',''); ?></p></td>


										<td align="right"><p><? echo number_format($knitting_qty,2,'.',''); ?></p></td>
										<td align="right"><p><? echo number_format($qtyValue['issue_qty'],2,'.',''); ?></p></td>
										<td align="right"><p><? echo number_format($qtyValue['trans_in_qty'],2,'.',''); ?></p></td>
										<td align="right"><p><? echo number_format($qtyValue['trans_out_qty'],2,'.',''); ?></p></td>
										<td align="right"><p><? echo number_format($qtyValue['fin_roll_rcv_qty'],2,'.',''); ?></p></td>
										<td align="right"><p><? echo number_format($qtyValue['fin_roll_issue_qty'],2,'.',''); ?></p></td>

										<td align="right"><p><? echo number_format($qtyValue['finish_in_qty'],2,'.',''); ?></p></td>
										<td align="right"><p><? echo number_format($qtyValue['finish_out_qty'],2,'.',''); ?></p></td>


										<td align="right"><p><?=number_format($qtyValue['cutting_lay'],2,'.','');?></p></td>
										<td align="right"><p><?=number_format($qtyValue['cutting_qc'],2,'.','');?></p></td>

										<td align="right"><p><?=number_format($qtyValue[2][1],2,'.','');?></p></td>
										<td align="right"><p><?=number_format($qtyValue[3][1],2,'.','');?></p></td>

										<td align="right"><p><?=number_format($qtyValue[2][2],2,'.','');?></p></td>

										<td align="right"><p><?=number_format($qtyValue[3][2],2,'.','');?></p></td>


										<td align="right"><p><?=number_format($printing_rcv,2,'.','');?></p></td>



										<td align="right"><p><?=number_format($printing_production,2,'.','');?></p></td>

										<td align="right"><p><?=number_format($printing_delivery,2,'.','');?></p></td>


										<td align="right"><p><?=number_format($embo_rcv,2,'.','');?></p></td>


										<td align="right"><p><?=number_format($qtyValue['embroidery_production_qty'],2,'.','');?></p></td>

										<td align="right"><p><?=number_format($emb_delivery,2,'.','');?></p></td>



										<td align="right"><p><? echo number_format($qtyValue['man_power'], 2,'.',''); ?></p></td>
										<td align="right"><p><? echo number_format($qtyValue['operator'], 2,'.',''); ?></p></td>
										<td align="right"><p><? echo number_format($qtyValue['helper'], 2,'.',''); ?></p></td>
										<td align="right"><p><? echo number_format($qtyValue['sewing_in'], 2,'.',''); ?></p></td>

										<td align="right"><p><? echo number_format($qtyValue['sewing_out'], 2,'.',''); ?></p></td>

										<td align="right"  title="(sewing out/(target*working hour)*100"><? echo number_format($sewing_acv_array[$company][$date],2); ?>%</td>
										  <td align="right" title="(sewing out*smv)/(manpower*working hour*60)*100"><? echo number_format($sewing_effi_array[$company][$date],2); ?>%</td>
										<td align="right"><p><? echo number_format($qtyValue['iron'], 2,'.',''); ?></p></td>
										<td align="right"><p><? echo number_format($hangtag=$qtyValue['hang_tag_qty'], 2,'.',''); ?></p></td>
										<td align="right"><? echo number_format($poly_qty, 2,'.',''); ?></td>
										<td align="right"><p><? echo number_format($qtyValue['finish'], 2,'.',''); ?></p></td>


										<td align="right"><p><? echo number_format($qtyValue['ship_qty'], 2,'.',''); ?></p></td>
										<td align="right"><p><? echo number_format($qtyValue['ship_value'], 2,'.',''); ?></p></td>
										<td align="right"><p><? echo number_format($shipment_net_value, 2); ?></p></td>
										<td align="right"><p><? echo number_format($qtyValue['ship_rtn_qty'], 2,'.','');?></p></td>
                        </tr>
						<?
						$i++;
						$month_total_projec_qty += $qtyValue['projec_qty_pcs'];
						$month_total_confirm_qty += $qtyValue['confirm_qty_pcs'];
						$month_total_cancel_order_qty += $qtyValue['cancel_order_qty'];
						$month_total_inactive_qty += $qtyValue['inactive_qty_pcs'];

						$month_total_ref_closing_qty += $qtyValue['ref_closing_qty'];
						$month_total_main_grey_qty += $qtyValue['main_grey_qty'];
						$month_total_sort_grey_qty += $qtyValue['sort_grey_qty'];



						$month_total_grey_recv +=$qtyValue['grey_recv_total'];
						//echo
						$month_total_grey_iss += $qtyValue['grey_iss_total'];
						$month_total_dyed_recv += $qtyValue['dyed_recv_total'];
						$month_total_dyed_iss += $qtyValue['dyed_iss_total'];

						$month_total_recv_qty += $knitting_qty;

						$month_total_issue_qty += $qtyValue['issue_qty'];
						$month_total_trans_in_qty += $qtyValue['trans_in_qty'];
						$month_total_trans_out_qty += $qtyValue['trans_out_qty'];

						$month_total_finish_in_qty += $qtyValue['finish_in_qty'];
						$month_total_finsh_out_qty += $qtyValue['finish_out_qty'];


						$month_total_trims_dyeing_qty += $qtyValue['trims_dyeing_qty'];
						$month_total_dyeing_qty_in += $dyeing_in;

						$month_total_dye_fini_in_qty += $dye_fini_in_qty;
						$month_total_dye_fin_qty_out += $qtyValue['dye_fin_qty_out'];
						$month_total_fin_roll_rcv_qty += $fin_roll_rcv_qty;
						$month_total_fin_roll_issue_qty += $qtyValue['fin_roll_issue_qty'];
						$month_total_finish_fabric_issue_return_qty += $finish_fabric_issue_return;

						$month_total_cutting_inhouse_qty += $cutting_inhouse_qty;
						$month_total_print_issue +=$qtyValue[2][1][1];
						$month_total_print_issue_out += $qtyValue[2][1][3];
						$month_total_print_issue_total += $total_print_issue_qty;

						$month_total_print_rcv_qty +=$qtyValue[3][1][1];
						$month_total_print_rcv_out += $qtyValue[3][1][3];
						$month_total_print_rcv_total += $print_rcv_qty;

						$month_total_emb_issue +=$qtyValue[2][2];
						$month_total_emb_rcv +=$qtyValue[3][2];
						$month_total_achivement += $sewing_acv_array[$company][$date];
						$month_total_sewing_efficiency +=$sewing_effi_array[$company][$date];







						$month_total_print_inhound_qty += $print_inhound;
						$month_total_print_outhound_qty += $print_outhound;
						$month_total_print_delivery_to_Cutting_qty += $print_delivery_to_Cutting;
						$month_total_embroidery_inhound_qty += $embroidery_inhound;
						$month_total_embroidery_outhound_qty += $embroidery_outhound;
						$month_total_embroidery_delivery_to_cutting_qty += $embroidery_delivery_to_cutting;

						$month_total_special_issue_qty += $qtyValue['special_issue'];
						$month_total_special_rcv_qty += $qtyValue['special_rcv'];
						$month_total_dyeing_issue_qty += $qtyValue['dyeing_issue'];
						$month_total_dyeing_rcv_qty += $qtyValue['dyeing_rcv'];
						$month_total_wash_issue_qty += $qtyValue['wash_issue'];
						$month_total_wash_rcv_qty += $qtyValue['wash_rcv'];
						$month_total_man_power += $qtyValue['man_power'];
						$month_total_operator += $qtyValue['operator'];
						$month_total_helper += $qtyValue['helper'];
						$month_total_sewing_in += $qtyValue['sewing_in'];
						$month_total_sewing_in_outbound__qty += $qtyValue['sewing_in_outbound_qty'];
						$month_total_sewing_inbound_qty += $sewing_inbound_qty;
						$month_total_sewing_out += $qtyValue['sewing_out'];
						$month_total_iron_qty += $qtyValue['iron'];
						$month_total_hangtag_qty += $hangtag;
						$month_total_poly_qty += $poly_qty;
						$month_total_finish_qty += $qtyValue['finish'];
						$month_total_ins_qty += $qtyValue['ins_qty'];
						$month_total_ship_qty += $qtyValue['ship_qty'];
						$month_total_ship_value += $qtyValue['ship_value'];
						$month_total_shipment_net_value_qty += $shipment_net_value;
						$month_total_ship_rtn_qty += $qtyValue['ship_rtn_qty'];

						$month_total_cutting_qc+=$qtyValue['cutting_qc'];
						$month_total_cutting_lay+=$qtyValue['cutting_lay'];
						$month_total_cut_send_print+=$qtyValue[2][1];
						$month_total_cut_rcv_print+=$qtyValue[3][1];




						$month_total_print_in_rcv+=$qtyValue[1]['print_rcv_qty'];
						$month_total_print_out_rcv+=$qtyValue[2]['print_rcv_qty'];
						$month_total_print_total_rcv+=$printing_rcv;
						$month_total_print_prod_in+=$qtyValue[1]['printing_production_qty'];
						$month_total_print_prod_out+=$qtyValue[2]['printing_production_qty'];
						$month_total_print_delev_in+=$qtyValue[1]['printing_delivery_qty']; ;
						$month_total_print_delev_out+=$qtyValue[2]['printing_delivery_qty']; ;
						$month_total_print_delev_total+=$printing_delivery;
						$month_total_emb_prod_in+=$qtyValue['embroidery_production_qty'];


						$month_total_emb_delev_in+=$emb_delivery;



						$month_total_embo_in+=$qtyValue[1]['embo_rcv_qty'];;
						$month_total_embo_out+=$qtyValue[2]['embo_rcv_qty'];;
						$month_total_embo_total+=$embo_rcv;



						// ==============

						$sub_total_projec_qty += $qtyValue['projec_qty_pcs'];
						$sub_total_confirm_qty += $qtyValue['confirm_qty_pcs'];
						$sub_total_cancel_order_qty += $qtyValue['cancel_order_qty'];
						$sub_total_inactive_qty += $qtyValue['inactive_qty_pcs'];
						$sub_total_ref_closing_qty += $qtyValue['ref_closing_qty'];
						$sub_total_sort_grey_qty += $qtyValue['sort_grey_qty'];
						$sub_total_main_grey_qty += $qtyValue['main_grey_qty'];

						$sub_total_grey_recv += $qtyValue['grey_recv_total'];
						$sub_total_grey_iss += $qtyValue['grey_iss_total'];
						$sub_total_dyed_recv += $qtyValue['dyed_recv_total'];
						$sub_total_dyed_iss += $qtyValue['dyed_iss_total'];

						$sub_total_recv_qty += $knitting_qty;
						$sub_total_issue_qty += $qtyValue['issue_qty'];
						$sub_total_trans_in_qty += $qtyValue['trans_in_qty'];
						$sub_total_trans_out_qty += $qtyValue['trans_out_qty'];

						$sub_total_finish_in_qty += $qtyValue['finish_in_qty'];
						$sub_total_finsh_out_qty += $qtyValue['finish_out_qty'];



						$sub_total_dye_fini_in_qty += $dye_fini_in_qty;
						$sub_total_dye_fin_qty_out += $qtyValue['dye_fin_qty_out'];
						$sub_total_fin_roll_rcv_qty += $fin_roll_rcv_qty;
						$sub_total_fin_roll_issue_qty += $qtyValue['fin_roll_issue_qty'];


						$sub_total_cutting_fab_recv += $qtyValue['cutting_fab_recv'];
						$sub_total_cutting_qc+=$qtyValue['cutting_qc'];

						$sub_total_cut_send_print+=$qtyValue[2][1];
						$sub_total_cut_rcv_print+=$qtyValue[3][1];
						$sub_total_achivement += $sewing_acv_array[$company][$date];
						$sub_total_sewing_efficiency +=$sewing_effi_array[$company][$date];





						$sub_total_print_rcv_total += $print_rcv_qty;



						$sub_total_emb_issue +=$qtyValue[2][2];
						$sub_total_emb_rcv +=$qtyValue[3][2];



						$sub_total_print_inhound_qty += $print_inhound;
						$sub_total_print_outhound_qty += $print_outhound;
						$sub_total_print_delivery_to_Cutting_qty += $print_delivery_to_Cutting;
						$sub_total_embroidery_inhound_qty += $embroidery_inhound;
						$sub_total_embroidery_outhound_qty += $embroidery_outhound;
						$sub_total_embroidery_delivery_to_cutting_qty += $embroidery_delivery_to_cutting;

						$sub_total_special_issue_qty += $qtyValue['special_issue'];
						$sub_total_special_rcv_qty += $qtyValue['special_rcv'];
						$sub_total_dyeing_issue_qty += $qtyValue['dyeing_issue'];
						$sub_total_dyeing_rcv_qty += $qtyValue['dyeing_rcv'];
						$sub_total_wash_issue_qty += $qtyValue['wash_issue'];
						$sub_total_wash_rcv_qty += $qtyValue['wash_rcv'];
						$sub_total_man_power += $qtyValue['man_power'];
						$sub_total_operator += $qtyValue['operator'];
						$sub_total_helper += $qtyValue['helper'];
						$sub_total_sewing_in += $qtyValue['sewing_in'];
						$sub_total_sewing_in_outbound_qty += $qtyValue['sewing_in_outbound_qty'];
						$sub_total_sewing_inbound_qty += $sewing_inbound_qty;
						$sub_total_sewing_out += $qtyValue['sewing_out'];
						$sub_total_iron_qty += $qtyValue['iron'];
						$sub_total_hangtag_qty += $hangtag;
						$sub_total_poly_qty += $poly_qty;
						$sub_total_finish_qty += $qtyValue['finish'];
						$sub_total_ins_qty += $qtyValue['ins_qty'];
						$sub_total_ship_qty += $qtyValue['ship_qty'];
						$sub_total_ship_value += $qtyValue['ship_value'];
						$sub_total_shipment_net_value += $shipment_net_value;
						$sub_total_ship_rtn_qty += $qtyValue['ship_rtn_qty'];



						$sub_total_cutting_lay_out+=$qtyValue['cutting_outbound_qty'];


						$sub_total_print_in_rcv+=$qtyValue[1]['print_rcv_qty'];
						$sub_total_print_out_rcv+=$qtyValue[2]['print_rcv_qty'];
						$sub_total_print_total_rcv+=$printing_rcv;
						$sub_total_print_prod_in+=$qtyValue[1]['printing_production_qty'];
						$sub_total_print_prod_out+=$qtyValue[2]['printing_production_qty'];
						$sub_total_print_delev_in+=$qtyValue[1]['printing_delivery_qty']; ;
						$sub_total_print_delev_out+=$qtyValue[2]['printing_delivery_qty']; ;
						$sub_total_print_delev_total+=$printing_delivery;
						$sub_total_emb_prod_in+=$qtyValue['embroidery_production_qty'];

						$sub_total_emb_delev_in+=$emb_delivery;



						$sub_total_embo_in+=$qtyValue[1]['embo_rcv_qty'];;
						$sub_total_embo_out+=$qtyValue[2]['embo_rcv_qty'];;
						$sub_total_embo_total+=$embo_rcv;



						$total_projec_qty += $qtyValue['projec_qty_pcs'];
						$total_confirm_qty += $qtyValue['confirm_qty_pcs'];
						$total_cancel_order_qty += $qtyValue['cancel_order_qty'];
						$total_inactive_qty += $qtyValue['inactive_qty_pcs'];
						$total_ref_closing_qty += $qtyValue['ref_closing_qty'];
						$total_sort_grey_qty += $qtyValue['sort_grey_qty'];
						$total_main_grey_qty += $qtyValue['main_grey_qty'];
						$total_sample_prod_qty += $qtyValue['sample_prod_qty'];
						$total_sample_delivery_qty += $qtyValue['sample_delivery_qty'];
						$total_grey_recv += $qtyValue['grey_recv_total'];
						$total_grey_iss += $qtyValue['grey_iss_total'];
						$total_dyed_recv += $qtyValue['dyed_recv_total'];
						$total_dyed_iss += $qtyValue['dyed_iss_total'];
						$total_knitting_qty_in += $knitting_qty_in;
						$total_knitting_qty_out += $knitting_qty_out;
						$total_recv_qty += $knitting_qty;
						$total_issue_qty += $qtyValue['issue_qty'];
						$total_trans_in_qty += $qtyValue['trans_in_qty'];
						$total_trans_out_qty += $qtyValue['trans_out_qty'];

						$total_finish_in_qty += $qtyValue['finish_in_qty'];
						$total_finsh_out_qty += $qtyValue['finish_out_qty'];

						$sub_total_cutting_lay+=$qtyValue['cutting_lay'];

						$total_re_process_batch_qty += $re_process_batch_qty;
						$total_trims_dyeing_qty += $qtyValue['trims_dyeing_qty'];
						$total_dyeing_qty_in += $dyeing_in;
						//22 Dyeing [Outbound]
						$total_dye_fini_in_qty += $dye_fini_in_qty;
						$total_dye_fin_qty_out += $qtyValue['dye_fin_qty_out'];
						$total_fin_roll_rcv_qty += $fin_roll_rcv_qty;
						$total_fin_roll_issue_qty += $qtyValue['fin_roll_issue_qty'];
						$total_finish_fabric_issue_return_qty += $finish_fabric_issue_return;
						//27
						//28

						$total_cutting_inhouse_qty += $cutting_inhouse_qty;
						$total_print_issue_in += $qtyValue[2][1][1];
						$total_print_issue_out += $qtyValue[2][1][3];
						$total_print_issue_total += $total_print_issue_qty;

						$total_print_rcv_qty +=$qtyValue[3][1][1];
						$total_print_rcv_out += $qtyValue[3][1][3];
						$total_print_rcv_total += $print_rcv_qty;


						$total_emb_issue +=$qtyValue[2][2];
						$total_emb_rcv +=$qtyValue[3][2];



						$total_print_inhound_qty += $print_inhound;
						$total_print_outhound_qty += $print_outhound;
						$total_print_delivery_to_Cutting_qty += $print_delivery_to_Cutting;
						$total_embroidery_inhound_qty += $embroidery_inhound;
						$total_embroidery_outhound_qty += $embroidery_outhound;
						$total_embroidery_delivery_to_cutting_qty += $embroidery_delivery_to_cutting;

						$total_special_issue_qty += $qtyValue['special_issue'];
						$total_special_rcv_qty += $qtyValue['special_rcv'];
						$total_dyeing_issue_qty += $qtyValue['dyeing_issue'];
						$total_dyeing_rcv_qty += $qtyValue['dyeing_rcv'];
						$total_wash_issue_qty += $qtyValue['wash_issue'];
						$total_wash_rcv_qty += $qtyValue['wash_rcv'];
						$total_man_power += $qtyValue['man_power'];
						$total_operator += $qtyValue['operator'];
						$total_helper += $qtyValue['helper'];
						$total_sewing_in += $qtyValue['sewing_in'];
						$total_sewing_in_outbound_qty += $qtyValue['sewing_in_outbound_qty'];;
						$total_sewing_inhouse_qty += $sewing_inbound_qty;
						$total_sewing_out += $qtyValue['sewing_out'];
						$total_iron_qty += $qtyValue['iron'];
						$total_hangtag_qty += $hangtag;
						$total_poly_qty += $poly_qty;
						$total_finish_qty += $qtyValue['finish'];
						$total_ins_qty += $qtyValue['ins_qty']; // 54
						$total_ship_qty += $qtyValue['ship_qty'];
						$total_ship_value += $qtyValue['ship_value'];
						$total_shipment_net_value += $shipment_net_value;
						$total_ship_rtn_qty += $qtyValue['ship_rtn_qty'];
						$total_cutting_qc_inhouse+=$qtyValue['cutting_qc_inhouse'];
						$total_cutting_qc_outbound+=$qtyValue['cutting_qc_outbound'];

						$tota_cutting_lay+=$qtyValue['cutting_lay'];
						$total_cutting_qc+=$qtyValue['cutting_qc'];
						$total_cut_send_print+=$qtyValue[2][1];
						$total_cut_rcv_print+=$qtyValue[3][1];

						$total_achivement += $sewing_acv_array[$company][$date];
						$total_sewing_efficiency +=$sewing_effi_array[$company][$date];




						//$total_cutting_lay_total+=$total_cutting_lay;

						$total_print_in_rcv+=$qtyValue[1]['print_rcv_qty'];
						$total_print_out_rcv+=$qtyValue[2]['print_rcv_qty'];
						$total_printing_rcv+=$printing_rcv;

						$total_print_prod_in+=$qtyValue[1]['printing_production_qty'];
						$total_print_prod_out+=$qtyValue[2]['printing_production_qty'];

						$total_print_delev_in+=$qtyValue[1]['printing_delivery_qty']; ;
						$total_print_delev_out+=$qtyValue[2]['printing_delivery_qty']; ;
						$total_print_delev_total+=$printing_delivery;
						$total_emb_prod_in+=$qtyValue['embroidery_production_qty'];

						$total_emb_delev_in +=$emb_delivery;

						$total_emb_delev_total+=$emb_delivery;
						$total_embo_in+=$qtyValue[1]['embo_rcv_qty'];;
						$total_embo_out+=$qtyValue[2]['embo_rcv_qty'];;
						$total_embo_total+=$embo_rcv;


						$getdate = date('D', $time = $date);
						if ($getdate != 'Fri')
						{
							if (!in_array($date, $date_fri_arr)) {
								$date_fri_arr[] = $date;
							}
							$j++;
							$totl_projec_qty += $qtyValue['projec_qty_pcs'];
							$totl_confirm_qty += $qtyValue['confirm_qty_pcs'];
							$totl_cancel_order_qty += $qtyValue['cancel_order_qty'];
							$totl_inactive_qty += $qtyValue['inactive_qty_pcs'];
							$totl_ref_closing_qty += $qtyValue['ref_closing_qty'];
							$totl_sort_grey_qty += $qtyValue['sort_grey_qty'];
							$totl_main_grey_qty += $qtyValue['main_grey_qty'];
							$totl_sample_prod_qty += $qtyValue['sample_prod_qty'];
							$totl_sample_delivery_qty += $qtyValue['sample_delivery_qty'];
							$totl_grey_recv += $qtyValue['grey_recv_total'];
							$totl_grey_iss += $qtyValue['grey_iss_total'];
							$totl_dyed_recv += $qtyValue['dyed_recv_total'];
							$totl_dyed_iss += $qtyValue['dyed_iss_total'];
							$totl_knit_qty_in += $knitting_qty_in;
							$totl_knit_qty_out += $knitting_qty_out;
							$totl_recv_qty += $knitting_qty;
							$totl_issue_qty += $qtyValue['issue_qty'];
							$totl_trans_in_qty += $qtyValue['trans_in_qty'];
							$totl_trans_out_qty += $qtyValue['trans_out_qty'];

							 $totl_finish_in_qty += $qtyValue['finish_in_qty'];
							 $totl_finsh_out_qty += $qtyValue['finish_out_qty'];

							 $totl_cutting_lay+=$qtyValue['cutting_lay'];

							$totl_dyei_qty_in += $dyeing_in;
							// 22 Dyeing [Outbound]
							$totl_dye_fini_in_qty += $dye_fini_in_qty;
							$totl_dye_fin_qty_out += $qtyValue['dye_fin_qty_out'];
							$totl_fin_roll_rcv_qty += $fin_roll_rcv_qty;
							$totl_fin_roll_issue_qty += $qtyValue['fin_roll_issue_qty'];
							$totl_finish_fabric_issue_return_qty += $finish_fabric_issue_return;
							//27
							//28
							$totl_aop_issue_qty += $qtyValue['aop_issue_qty'];
							$totl_aop_recv_qty += $qtyValue['aop_recv_qty'];
							$totl_cutting_fab_recv += $qtyValue['cutting_fab_recv'];
							$totl_cutt_inhouse_qty += $cutting_inhouse_qty;
							$totl_prnt_issue += $qtyValue[2][1][1];
							$totl_print_issue_out += $qtyValue[2][1][3];
							$totl_print_issue_total += $total_print_issue_qty;

							$totl_print_rcv_qty +=$qtyValue[3][1][1];
							$totl_print_rcv_out += $qtyValue[3][1][3];
							$totl_print_rcv_total += $print_rcv_qty;

							$totl_emb_issue +=$qtyValue[2][2];
							$totl_emb_rcv +=$qtyValue[3][2];


							$totl_print_inhound_qty += $print_inhound;
							$totl_print_outhound_qty += $print_outhound;
							$totl_print_delivery_to_Cutting_qty += $print_delivery_to_Cutting;
							$totl_embroidery_inhound_qty += $embroidery_inhound;
							$totl_embroidery_outhound_qty += $embroidery_outhound;
							$totl_embroidery_delivery_to_cutting_qty += $embroidery_delivery_to_cutting;

							$totl_man_power += $qtyValue['man_power'];
							$totl_operator += $qtyValue['operator'];
							$totl_helper += $qtyValue['helper'];
							$totl_sewi_in += $qtyValue['sewing_in'];;
							$totl_sewi_in_outbound_qty += $qtyValue['sewing_in_outbound_qty'];;
							$totl_sewi_inbound_qty += $sewing_inbound_qty;
							$totl_sewi_out += $qtyValue['sewing_out'];;
							$totl_iro_qty += $qtyValue['iron'];
							$totl_hangtag_qty += $hangtag;
							$totl_poly_qty += $poly_qty;
							$totl_fini_qty += $qtyValue['finish'];
							$totl_ins_qty += $qtyValue['ins_qty'];
							$totl_ship_qty += $qtyValue['ship_qty'];
							$totl_ship_value += $qtyValue['ship_value'];
							$totl_shipment_net_value += $shipment_net_value;
							$totl_ship_rtn_qty += $qtyValue['ship_rtn_qty'];
							$totl_cutting_qc_inhouse+=$qtyValue['cutting_qc_inhouse'];
							$totl_cutting_qc_outbound+=$qtyValue['cutting_qc_outbound'];
							$totl_cutting_qc+=$qtyValue['cutting_qc'];
							$totl_cut_send_print+=$qtyValue[2][1];
							$totl_cut_rcv_print+=$qtyValue[3][1];

							$totl_achivement += $sewing_acv_array[$company][$date];
							$totl_sewing_efficiency +=$sewing_effi_array[$company][$date];


							$totl_cutting_lay_out+=$qtyValue['cutting_outbound_qty'];
							//$totl_cutting_lay_total+=$total_cutting_lay;

							$totl_print_in_rcv+=$qtyValue[1]['print_rcv_qty'];
							$totl_print_out_rcv+=$qtyValue[2]['print_rcv_qty'];
							$totl_print_total_rcv+=$printing_rcv;

							$totl_print_prod_in+=$qtyValue[1]['printing_production_qty'];
						    $totl_print_prod_out+=$qtyValue[2]['printing_production_qty'];
							$totl_print_delev_in+=$qtyValue[1]['printing_delivery_qty']; ;
							$totl_print_delev_out+=$qtyValue[2]['printing_delivery_qty']; ;
							$totl_print_delev_total+=$printing_delivery;
							$totl_emb_prod_in+=$qtyValue['embroidery_production_qty'];


							// $totl_emb_delev_in+=$emb_delivery;

							// $totl_emb_delev_total+=$emb_delivery;

							$totl_embo_in+=$qtyValue[1]['embo_rcv_qty'];;
							$totl_embo_out+=$qtyValue[2]['embo_rcv_qty'];;
							$totl_embo_total+=$embo_rcv;


						}
					}
					?>

                    <tr>
                        				<td align="right" colspan="2"><b>Monthly Total</b></td>
                        				<td align="right"><p><b><? echo number_format($month_total_projec_qty,2,'.',''); ?></b></p></td>
										<td align="right"><p><b><? echo number_format($month_total_confirm_qty,2,'.',''); ?></b></p></td>
										<td align="right"><p><b><? echo number_format($month_total_cancel_order_qty,2,'.',''); ?></b></p></td>
										<td align="right"><p><b><? echo number_format($month_total_inactive_qty,2,'.',''); ?></b></p></td>
										<td align="right"><p><b><? echo number_format($month_total_ref_closing_qty,2,'.',''); ?></b></p></td>

										<td align="right"><p><b><?=number_format($month_total_main_grey_qty,2,'.','')?></b></p></td>
										<td align="right"><p><b><?php echo number_format($month_total_sort_grey_qty,2,'.',''); ?></b></p></td>

										<td align="right"><p><b><?php echo number_format($month_total_grey_recv,2,'.',''); ?></b></p></td>
										<td align="right"><p><b><?php echo number_format($month_total_grey_iss,2,'.',''); ?></b></p></td>
										<td align="right"><p><b><?php echo number_format($month_total_dyed_recv,2,'.',''); ?></b></p></td>
										<td align="right"><p><b><?php echo number_format($month_total_dyed_iss,2,'.',''); ?></b></p></td>


										<td align="right"><p><b><?php echo number_format($month_total_recv_qty,2,'.',''); ?></b></p></td>
										<td align="right"><p><b><?php echo number_format($month_total_issue_qty,2,'.',''); ?></b></p></td>
										<td align="right"><p><b><?php echo number_format($month_total_trans_in_qty, 2,'.',''); ?></b></p></td>
										<td align="right"><p><b><?php echo number_format($month_total_trans_out_qty, 2,'.',''); ?></b></p></td>
										<td align="right"><p><b><?php echo number_format($month_total_fin_roll_rcv_qty, 2,'.',''); ?></b></p></td>
										<td align="right"><p><b><?php echo number_format($month_total_fin_roll_issue_qty, 2,'.',''); ?></b></p></td>

										<td align="right"><p><b><?php echo number_format($month_total_finish_in_qty, 2,'.',''); ?></b></p></td>
										<td align="right"><p><b><?php echo number_format($month_total_finsh_out_qty, 2,'.',''); ?></b></p></td>



										<td align="right"><p><b><?=number_format($month_total_cutting_lay, 2,'.','');?></b></p></td>
										<td align="right"><p><b><?=number_format($month_total_cutting_qc, 2,'.','');?></b></p></td>
										<td align="right"><p><b><?=number_format($month_total_cut_send_print, 2,'.','');?></b></p></td>
										<td align="right"><p><b><?=number_format($month_total_cut_rcv_print , 2,'.','');?></b></p></td>
										<td align="right"><p><b><?=number_format($month_total_emb_issue  , 2,'.','');?></b></p></td>
										<td align="right"><p><b><?=number_format($month_total_emb_rcv  , 2,'.','');?></b></p></td>

										<td align="right"><p><b><?=number_format($month_total_print_in_rcv , 2,'.','')?></b></p> </td>
										<td align="right"><p><b><?=number_format($month_total_print_prod_in , 2,'.','')?></b></p></td>
										<td align="right"><p><b><?=number_format($month_total_print_delev_in  , 2,'.','')?></b></p> </td>

										<td align="right"><p><b><?=number_format($month_total_embo_in , 2,'.','')?></b></p> </td>
										<td align="right"><p><b><?=number_format($month_total_emb_prod_in , 2,'.','')?></b></p></td>
										<td align="right"><p><b><?=number_format($month_total_emb_delev_in  , 2,'.','')?></b></p> </td>

										<td align="right"><p><b><?php echo number_format($month_total_man_power, 2,'.',''); ?></b></p></td>
										<td align="right"><p><b><?php echo number_format($month_total_operator, 2,'.',''); ?></b></p></td>
										<td align="right"><p><b><?php echo number_format($month_total_helper, 2,'.',''); ?></b></p></td>
										<td align="right"><p><b><?=number_format($month_total_sewing_in, 2,'.','') ?></b></p></td>
										<td align="right"><p><b><?php echo number_format($month_total_sewing_out, 2,'.',''); ?></b></p></td>
										<td align="right"><p><b><?=number_format($month_total_achivement , 2,'.','') ?>%</b></p></td>
										<td align="right"><p><b><?php echo number_format($month_total_sewing_efficiency, 2,'.',''); ?>%</b></p></td>

										<td align="right"><p><b><?php echo number_format($month_total_iron_qty, 2,'.',''); ?></b></p></td>
										<td align="right"><p><b><?php echo number_format($month_total_hangtag_qty, 2,'.',''); ?></b></p></td>
										<td align="right"><p><b><?php echo number_format($month_total_poly_qty, 2,'.',''); ?></b></p></td>
										<td align="right"><p><b><?php echo number_format($month_total_finish_qty, 2,'.',''); ?></b></p></td>


										<td align="right"><p><b><? echo number_format($month_total_ship_qty, 2,'.',''); ?></b></p></td>
										<td align="right"><p><b><? echo number_format($month_total_ship_value, 2,'.',''); ?></b></p></td>
										<td align="right"><p><b><? echo number_format($month_total_shipment_net_value_qty, 2,'.',''); ?></b></p></td>
										<td align="right"><b><? echo number_format($month_total_ship_rtn_qty, 2,'.',''); ?></b></td>
                    </tr>
                    <!-- Month total end -->

					<tr>
                        <td colspan="72"></td>
                    </tr>
                    <tr>

										<td align="left" colspan="2"><b style="font-size: 11px !important; padding-left: 5px;">Sub Total</b></td>
                        				<td align="right"><p><b><? echo number_format($sub_total_projec_qty,2,'.',''); ?></b></p></td>
										<td align="right"><p><b><? echo number_format($sub_total_confirm_qty,2,'.',''); ?></b></p></td>
										<td align="right"><p><b><? echo number_format($sub_total_cancel_order_qty,2,'.',''); ?></b></p></td>
										<td align="right"><p><b><? echo number_format($sub_total_inactive_qty,2,'.',''); ?></b></p></td>
										<td align="right"><p><b><? echo number_format($sub_total_ref_closing_qty,2,'.',''); ?></b></p></td>

										<td align="right"><p><b><?=number_format($sub_total_main_grey_qty,2,'.','')?></b></p></td>
										<td align="right"><p><b><?php echo number_format($sub_total_sort_grey_qty,2,'.',''); ?></b></p></td>

										<td align="right"><p><b><?php echo number_format($sub_total_grey_recv,2,'.',''); ?></b></p></td>
										<td align="right"><p><b><?php echo number_format($sub_total_grey_iss,2,'.',''); ?></b></p></td>
										<td align="right"><p><b><?php echo number_format($sub_total_dyed_recv,2,'.',''); ?></b></p></td>
										<td align="right"><p><b><?php echo number_format($sub_total_dyed_iss,2,'.',''); ?></b></p></td>


										<td align="right"><p><b><?php echo number_format($sub_total_recv_qty,2,'.',''); ?></b></p></td>
										<td align="right"><p><b><?php echo number_format($sub_total_issue_qty,2,'.',''); ?></b></p></td>
										<td align="right"><p><b><?php echo number_format($sub_total_trans_in_qty, 2,'.',''); ?></b></p></td>
										<td align="right"><p><b><?php echo number_format($sub_total_trans_out_qty, 2,'.',''); ?></b></p></td>
										<td align="right"><p><b><?php echo number_format($sub_total_fin_roll_rcv_qty, 2,'.',''); ?></b></p></td>
										<td align="right"><p><b><?php echo number_format($sub_total_fin_roll_issue_qty, 2,'.',''); ?></b></p></td>

										<td align="right"><p><b><?php echo number_format($sub_total_finish_in_qty, 2,'.',''); ?></b></p></td>
										<td align="right"><p><b><?php echo number_format($sub_total_finsh_out_qty, 2,'.',''); ?></b></p></td>


										<td align="right"><p><b><?=number_format($sub_total_cutting_lay, 2,'.','');?></b></p></td>
										<td align="right"><p><b><?=number_format($sub_total_cutting_qc, 2,'.','');?></b></p></td>
										<td align="right"><p><b><?=number_format($sub_total_cut_send_print, 2,'.','');?></b></p></td>
										<td align="right"><p><b><?=number_format($sub_total_cut_rcv_print , 2,'.','');?></b></p></td>
										<td align="right"><p><b><?=number_format($sub_total_emb_issue  , 2,'.','');?></b></p></td>
										<td align="right"><p><b><?=number_format($sub_total_emb_rcv  , 2,'.','');?></b></p></td>

										<td align="right"><p><b><?=number_format($sub_total_print_in_rcv , 2,'.','')?></b></p> </td>
										<td align="right"><p><b><?=number_format($sub_total_print_prod_in , 2,'.','')?></b></p></td>
										<td align="right"><p><b><?=number_format($sub_total_print_delev_in  , 2,'.','')?></b></p> </td>

										<td align="right"><p><b><?=number_format($sub_total_embo_in , 2,'.','')?></b></p> </td>
										<td align="right"><p><b><?=number_format($sub_total_emb_prod_in , 2,'.','')?></b></p></td>
										<td align="right"><p><b><?=number_format($sub_total_emb_delev_in  , 2,'.','')?></b></p> </td>

										<td align="right"><p><b><?php echo number_format($sub_total_man_power, 2,'.',''); ?></b></p></td>
										<td align="right"><p><b><?php echo number_format($sub_total_operator, 2,'.',''); ?></b></p></td>
										<td align="right"><p><b><?php echo number_format($sub_total_helper, 2,'.',''); ?></b></p></td>
										<td align="right"><p><b><?=number_format($sub_total_sewing_in, 2,'.','') ?></b></p></td>
										<td align="right"><p><b><?php echo number_format($sub_total_sewing_out, 2,'.',''); ?></b></p></td>
										<td align="right"><p><b><?=number_format($sub_total_achivement , 2,'.','') ?>%</b></p></td>
										<td align="right"><p><b><?php echo number_format($sub_total_sewing_efficiency, 2,'.',''); ?>%</b></p></td>

										<td align="right"><p><b><?php echo number_format($sub_total_iron_qty, 2,'.',''); ?></b></p></td>
										<td align="right"><p><b><?php echo number_format($sub_total_hangtag_qty, 2,'.',''); ?></b></p></td>
										<td align="right"><p><b><?php echo number_format($sub_total_poly_qty, 2,'.',''); ?></b></p></td>
										<td align="right"><p><b><?php echo number_format($sub_total_finish_qty, 2,'.',''); ?></b></p></td>


										<td align="right"><p><b><? echo number_format($sub_total_ship_qty, 2,'.',''); ?></b></p></td>
										<td align="right"><p><b><? echo number_format($sub_total_ship_value, 2,'.',''); ?></b></p></td>
										<td align="right"><p><b><? echo number_format($sub_total_shipment_net_value_qty, 2,'.',''); ?></b></p></td>
										<td align="right"><b><? echo number_format($sub_total_ship_rtn_qty, 2,'.',''); ?></b></td>
                    </tr>
					<?php
					$date_count = count($date_arr);
					$date_count2 = count($date_fri_arr);
					?>
							<tr>
											<td align="left" colspan="2"><b style="font-size: 11px !important; padding-left: 5px;">Grand Total</b></td>
											<td align="right"><p><b><? echo number_format($total_projec_qty,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><? echo number_format($total_confirm_qty,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><? echo number_format($total_cancel_order_qty,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><? echo number_format($total_inactive_qty,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><? echo number_format($total_ref_closing_qty,2,'.',''); ?></b></p></td>

											<td align="right"><p><b><?=number_format($total_main_grey_qty,2,'.','')?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_sort_grey_qty,2,'.',''); ?></b></p></td>

											<td align="right"><p><b><?php echo number_format($total_grey_recv,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_grey_iss,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_dyed_recv,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_dyed_iss,2,'.',''); ?></b></p></td>


											<td align="right"><p><b><?php echo number_format($total_recv_qty,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_issue_qty,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_trans_in_qty, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_trans_out_qty, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_fin_roll_rcv_qty, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_fin_roll_issue_qty, 2,'.',''); ?></b></p></td>

											<td align="right"><p><b><?php echo number_format($total_finish_in_qty, 2,'.',''); ?></b></p></td>
										   <td align="right"><p><b><?php echo number_format($total_finsh_out_qty, 2,'.',''); ?></b></p></td>



											<td align="right"><p><b><?=number_format($tota_cutting_lay, 2,'.','');?></b></p></td>
											<td align="right"><p><b><?=number_format($total_cutting_qc, 2,'.','');?></b></p></td>
											<td align="right"><p><b><?=number_format($total_cut_send_print, 2,'.','');?></b></p></td>
											<td align="right"><p><b><?=number_format($total_cut_rcv_print , 2,'.','');?></b></p></td>
											<td align="right"><p><b><?=number_format($total_emb_issue  , 2,'.','');?></b></p></td>
											<td align="right"><p><b><?=number_format($total_emb_rcv  , 2,'.','');?></b></p></td>

											<td align="right"><p><b><?=number_format($total_print_in_rcv , 2,'.','')?></b></p> </td>
											<td align="right"><p><b><?=number_format($total_print_prod_in , 2,'.','')?></b></p></td>
											<td align="right"><p><b><?=number_format($total_print_delev_in  , 2,'.','')?></b></p> </td>

											<td align="right"><p><b><?=number_format($total_embo_in , 2,'.','')?></b></p> </td>
											<td align="right"><p><b><?=number_format($total_emb_prod_in , 2,'.','')?></b></p></td>
											<td align="right"><p><b><?=number_format($total_emb_delev_in  , 2,'.','')?></b></p> </td>

											<td align="right"><p><b><?php echo number_format($total_man_power, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_operator, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_helper, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?=number_format($total_sewing_in, 2,'.','') ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_sewing_out, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?=number_format($total_achivement , 2,'.','') ?>%</b></p></td>
											<td align="right"><p><b><?php echo number_format($total_sewing_efficiency, 2,'.',''); ?>%</b></p></td>

											<td align="right"><p><b><?php echo number_format($total_iron_qty, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_hangtag_qty, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_poly_qty, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_finish_qty, 2,'.',''); ?></b></p></td>


											<td align="right"><p><b><? echo number_format($total_ship_qty, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><? echo number_format($total_ship_value, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><? echo number_format($total_shipment_net_value_qty, 2,'.',''); ?></b></p></td>
											<td align="right"><b><? echo number_format($total_ship_rtn_qty, 2,'.',''); ?></b></td>
					</tr>
					<tr>
											<td align="left" colspan="2"><b style="font-size: 11px !important; padding-left: 5px;">Avarage</b></td>
											<td align="right"><p><b><? echo number_format($total_projec_qty / $date_count,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><? echo number_format($total_confirm_qty / $date_count,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><? echo number_format($total_cancel_order_qty / $date_count,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><? echo number_format($total_inactive_qty / $date_count,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><? echo number_format($total_ref_closing_qty / $date_count,2,'.',''); ?></b></p></td>

											<td align="right"><p><b><?=number_format($total_main_grey_qty / $date_count,2,'.','')?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_sort_grey_qty / $date_count,2,'.',''); ?></b></p></td>

											<td align="right"><p><b><?php echo number_format($total_grey_recv / $date_count,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_grey_iss / $date_count,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_dyed_recv / $date_count,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_dyed_iss / $date_count,2,'.',''); ?></b></p></td>


											<td align="right"><p><b><?php echo number_format($total_recv_qty / $date_count,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_issue_qty / $date_count,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_trans_in_qty / $date_count, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_trans_out_qty / $date_count, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_fin_roll_rcv_qty  / $date_count, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_fin_roll_issue_qty / $date_count, 2,'.',''); ?></b></p></td>

											<td align="right"><p><b><?php echo number_format($total_finish_in_qty / $date_count, 2,'.',''); ?></b></p></td>
										   <td align="right"><p><b><?php echo number_format($total_finsh_out_qty / $date_count, 2,'.',''); ?></b></p></td>



											<td align="right"><p><b><?=number_format($tota_cutting_lay / $date_count, 2,'.','');?></b></p></td>
											<td align="right"><p><b><?=number_format($total_cutting_qc / $date_count, 2,'.','');?></b></p></td>
											<td align="right"><p><b><?=number_format($total_cut_send_print / $date_count, 2,'.','');?></b></p></td>
											<td align="right"><p><b><?=number_format($total_cut_rcv_print / $date_count, 2,'.','');?></b></p></td>
											<td align="right"><p><b><?=number_format($total_emb_issue / $date_count  , 2,'.','');?></b></p></td>
											<td align="right"><p><b><?=number_format($total_emb_rcv / $date_count , 2,'.','');?></b></p></td>

											<td align="right"><p><b><?=number_format($total_print_in_rcv / $date_count , 2,'.','')?></b></p> </td>
											<td align="right"><p><b><?=number_format($total_print_prod_in / $date_count, 2,'.','')?></b></p></td>
											<td align="right"><p><b><?=number_format($total_print_delev_in / $date_count , 2,'.','')?></b></p> </td>

											<td align="right"><p><b><?=number_format($total_embo_in / $date_count , 2,'.','')?></b></p> </td>
											<td align="right"><p><b><?=number_format($total_emb_prod_in / $date_count, 2,'.','')?></b></p></td>
											<td align="right"><p><b><?=number_format($total_emb_delev_in / $date_count  , 2,'.','')?></b></p> </td>

											<td align="right"><p><b><?php echo number_format($total_man_power / $date_count, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_operator / $date_count, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_helper / $date_count, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?=number_format($total_sewing_in / $date_count, 2,'.','') ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_sewing_out / $date_count, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?=number_format($total_achivement  / $date_count , 2,'.','') ?>%</b></p></td>
											<td align="right"><p><b><?php echo number_format($total_sewing_efficiency  / $date_count, 2,'.',''); ?>%</b></p></td>

											<td align="right"><p><b><?php echo number_format($total_iron_qty / $date_count, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_hangtag_qty / $date_count, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_poly_qty / $date_count, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_finish_qty / $date_count, 2,'.',''); ?></b></p></td>


											<td align="right"><p><b><? echo number_format($total_ship_qty / $date_count, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><? echo number_format($total_ship_value / $date_count, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><? echo number_format($total_shipment_net_value_qty / $date_count, 2,'.',''); ?></b></p></td>
											<td align="right"><b><? echo number_format($total_ship_rtn_qty / $date_count, 2,'.',''); ?></b></td>
					</tr>
					<tr>
											<td align="left" colspan="2"><b style="font-size: 11px !important; padding-left: 5px;">Total Exclude Friday</b></td>
											<td align="right"><p><b><? echo number_format($totl_projec_qty,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><? echo number_format($totl_confirm_qty,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><? echo number_format($totl_cancel_order_qty,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><? echo number_format($totl_inactive_qty,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><? echo number_format($totl_ref_closing_qty,2,'.',''); ?></b></p></td>

											<td align="right"><p><b><?=number_format($totl_main_grey_qty,2,'.','')?></b></p></td>
											<td align="right"><p><b><?php echo number_format($totl_sort_grey_qty,2,'.',''); ?></b></p></td>

											<td align="right"><p><b><?php echo number_format($totl_grey_recv,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($totl_grey_iss,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($totl_dyed_recv,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($totl_dyed_iss,2,'.',''); ?></b></p></td>


											<td align="right"><p><b><?php echo number_format($totl_recv_qty,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($totl_issue_qty,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($totl_trans_in_qty, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($totl_trans_out_qty, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($totl_fin_roll_rcv_qty, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($totl_fin_roll_issue_qty, 2,'.',''); ?></b></p></td>

											<td align="right"><p><b><?php echo number_format($totl_finish_in_qty, 2,'.',''); ?></b></p></td>
										   <td align="right"><p><b><?php echo number_format($totl_finsh_out_qty, 2,'.',''); ?></b></p></td>



											<td align="right"><p><b><?=number_format($totl_cutting_lay, 2,'.','');?></b></p></td>
											<td align="right"><p><b><?=number_format($totl_cutting_qc, 2,'.','');?></b></p></td>
											<td align="right"><p><b><?=number_format($totl_cut_send_print, 2,'.','');?></b></p></td>
											<td align="right"><p><b><?=number_format($totl_cut_rcv_print, 2,'.','');?></b></p></td>
											<td align="right"><p><b><?=number_format($totl_emb_issue  , 2,'.','');?></b></p></td>
											<td align="right"><p><b><?=number_format($totl_emb_rcv  , 2,'.','');?></b></p></td>

											<td align="right"><p><b><?=number_format($totl_print_in_rcv , 2,'.','')?></b></p> </td>
											<td align="right"><p><b><?=number_format($totl_print_prod_in , 2,'.','')?></b></p></td>
											<td align="right"><p><b><?=number_format($totl_print_delev_in  , 2,'.','')?></b></p> </td>

											<td align="right"><p><b><?=number_format($totl_embo_in , 2,'.','')?></b></p> </td>
											<td align="right"><p><b><?=number_format($totl_emb_prod_in , 2,'.','')?></b></p></td>
											<td align="right"><p><b><?=number_format($totl_emb_delev_in  , 2,'.','')?></b></p> </td>

											<td align="right"><p><b><?php echo number_format($totl_man_power, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($totl_operator, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($totl_helper, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?=number_format($totl_sewi_in, 2,'.','') ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($totl_sewi_out, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?=number_format($totl_achivement , 2,'.','') ?>%</b></p></td>
											<td align="right"><p><b><?php echo number_format($totl_sewing_efficiency, 2,'.',''); ?>%</b></p></td>
											<td align="right"><p><b><?php echo number_format($totl_iron_qty, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($totl_hangtag_qty, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($totl_poly_qty, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($totl_finish_qty, 2,'.',''); ?></b></p></td>


											<td align="right"><p><b><? echo number_format($totl_ship_qty, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><? echo number_format($totl_ship_value, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><? echo number_format($totl_shipment_net_value_qty, 2,'.',''); ?></b></p></td>
											<td align="right"><b><? echo number_format($totl_ship_rtn_qty, 2,'.',''); ?></b></td>
					</tr>
					<tr>
											<td align="left" colspan="2"><b style="font-size: 11px !important; padding-left: 5px;">AVG. Excl. Friday</b></td>
											<td align="right"><p><b><? echo number_format($totl_projec_qty /$date_count2,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><? echo number_format($totl_confirm_qty /$date_count2,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><? echo number_format($totl_cancel_order_qty /$date_count2,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><? echo number_format($totl_inactive_qty /$date_count2,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><? echo number_format($totl_ref_closing_qty /$date_count2,2,'.',''); ?></b></p></td>

											<td align="right"><p><b><?=number_format($totl_main_grey_qty /$date_count2,2,'.','')?></b></p></td>
											<td align="right"><p><b><?php echo number_format($totl_sort_grey_qty /$date_count2,2,'.',''); ?></b></p></td>

											<td align="right"><p><b><?php echo number_format($totl_grey_recv /$date_count2,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($totl_grey_iss /$date_count2,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($totl_dyed_recv /$date_count2,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($totl_dyed_iss /$date_count2,2,'.',''); ?></b></p></td>


											<td align="right"><p><b><?php echo number_format($totl_recv_qty /$date_count2,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($totl_issue_qty /$date_count2,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($totl_trans_in_qty /$date_count2, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($totl_trans_out_qty /$date_count2, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($totl_fin_roll_rcv_qty / $date_count2, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($totl_fin_roll_issue_qty /$date_count2, 2,'.',''); ?></b></p></td>

											<td align="right"><p><b><?php echo number_format($totl_finish_in_qty /$date_count2, 2,'.',''); ?></b></p></td>
										   <td align="right"><p><b><?php echo number_format($totl_finsh_out_qty /$date_count2, 2,'.',''); ?></b></p></td>



											<td align="right"><p><b><?=number_format($totl_cutting_lay /$date_count2, 2,'.','');?></b></p></td>
											<td align="right"><p><b><?=number_format($totl_cutting_qc /$date_count2, 2,'.','');?></b></p></td>
											<td align="right"><p><b><?=number_format($totl_cut_send_print /$date_count2, 2,'.','');?></b></p></td>
											<td align="right"><p><b><?=number_format($totl_cut_rcv_print /$date_count2, 2,'.','');?></b></p></td>
											<td align="right"><p><b><?=number_format($totl_emb_issue /$date_count2 , 2,'.','');?></b></p></td>
											<td align="right"><p><b><?=number_format($totl_emb_rcv /$date_count2  , 2,'.','');?></b></p></td>

											<td align="right"><p><b><?=number_format($totl_print_in_rcv /$date_count2 , 2,'.','')?></b></p> </td>
											<td align="right"><p><b><?=number_format($totl_print_prod_in /$date_count2, 2,'.','')?></b></p></td>
											<td align="right"><p><b><?=number_format($totl_print_delev_in /$date_count2 , 2,'.','')?></b></p> </td>

											<td align="right"><p><b><?=number_format($totl_embo_in /$date_count2 , 2,'.','')?></b></p> </td>
											<td align="right"><p><b><?=number_format($totl_emb_prod_in /$date_count2, 2,'.','')?></b></p></td>
											<td align="right"><p><b><?=number_format($totl_emb_delev_in  /$date_count2, 2,'.','')?></b></p> </td>

											<td align="right"><p><b><?php echo number_format($totl_man_power /$date_count2, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($totl_operator /$date_count2, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($totl_helper /$date_count2, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?=number_format($totl_sewi_in /$date_count2, 2,'.','') ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($totl_sewi_out /$date_count2, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($totl_achivement  /$date_count2, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?=number_format($totl_sewing_efficiency  /$date_count2 , 2,'.','') ?>%</b></p></td>

											<td align="right"><p><b><?php echo number_format($totl_iron_qty /$date_count2, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($totl_hangtag_qty /$date_count2, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($totl_poly_qty /$date_count2, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($totl_finish_qty /$date_count2, 2,'.',''); ?></b></p></td>


											<td align="right"><p><b><? echo number_format($totl_ship_qty /$date_count2, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><? echo number_format($totl_ship_value /$date_count2, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><? echo number_format($totl_shipment_net_value_qty /$date_count2, 2,'.',''); ?></b></p></td>
											<td align="right"><b><? echo number_format($totl_ship_rtn_qty /$date_count2, 2,'.',''); ?></b></td>
					</tr>


				<?
				}



				?>
	        </table>

	    </fieldset><br>
		<fieldset style="width: 4750px; margin:0px auto;">
	        <table cellspacing="0" cellpadding="0" id="sammary_tbl" border="1" rules="all" class="rpt_table" style="width: 100%">
	            <thead>
		            <tr>
		                <td align="center" colspan="35" style="font-size: 20px;"><strong>Factory Wise Production Summary</strong> </td>
		            </tr>

					<tr>
						<th colspan="7">Order Information</th>

						<th colspan="2">Booking Information </th>
						<th colspan="4">Yarn Store </th>
						<th colspan="8">Greige Fabric Store </th>
						<th colspan="6">Cutting</th>
						<th colspan="3">Printing</th>
						<th colspan="3">Embroidery</th>
						<th colspan="7">Sewing</th>
						<th colspan="4">GMT Finishing</th>
						<th colspan="4">Export/Shipment</th>
					</tr>

					<tr>
		                <th width="30"><p>SL</p></th>
		                <th width="100"><p>Production Date</p></th>
		                <th width="100"><p>Projection Order [PCS]</p></th>
		                <th width="100"><p>Confirm Order [PCS]</p></th>
		                <th width="100"><p>Cancel Order [PCS]</p></th>
						<th width="100"><p>InActive Order [PCS]</p></th>
		                <th width="100"><p>Reference Close [Pcs]</p></th>

						<th width="100"><p>Main Fabric Booking Qty</p></th>
						<th width="100"><p>Short/EFR Booking</p></th>

		                <th width="100"><p>Grey Yarn Received</p></th>
		                <th width="100"><p>Grey Yarn Issued</p></th>
		                <th width="100"><p>Dyed Yarn Received</p></th>
		                <th width="100"><p>Dyed Yarn Issued</p></th>


		                <th width="100"><p>Greige Fabric Received</p></th>
		                <th width="100"><p>Greige Fabric issued</p></th>
		                <th width="100"><p>Greige Fabric Transfer Rcv</p></th>
		                <th width="100"><p>Greige Fabric Transfer Issued</p></th>

		                <th width="100"><p>Finish Fabric Receive</p></th>
		                <th width="100"><p>Finish Fabric Issue</p></th>
		                <th width="100"><p>Finish Fabric Trasfer Recv</p></th>
		                <th width="100"><p>Finish Fabric Trasfer Issue</p></th>



		                <th width="100"><p>Cutting Lay</p></th>
						<th width="100"><p>Cutting QC</p></th>
		                <th width="100"><p>Cutting Send To Print</p></th>
						<th width="100"><p>Cutting  Rcv From Print </p></th>
						<th width="100"><p>Cutting Send to Embroidery </p></th>
						<th width="100"><p>Cutting Rcv from Embroidery </p></th>


						<th width="100"><p>Print  Rcv </p></th>
		                <th width="100"><p>Print Production</p></th>
		                <th width="100"><p>Print Delivery to Cutting</p></th>


						<th width="100"><p>EMB   Rcv </p></th>
		                <th width="100"><p>EMB  Production</p></th>
		                <th width="100"><p>EMB Delivery to Cutting </p></th>


						<th width="100"><p>Total Man Power</p></th>
						<th width="100"><p>No of Operator</p></th>
						<th width="100"><p>No of Helper</p></th>
						<th width="100"><p>Sewing Input</p></th>
						<th width="100"><p>Sewing Output</p></th>
						<th width="100"><p>Achivement %</p></th>
						<th width="100"><p>Sewing Efficiency %</p></th>

						<th width="100"><p>Iron</p></th>
						<th width="100"><p>Hangtag</p></th>
						<th width="100"><p>Poly</p></th>
						<th width="100"><p>Pack.And Finis.</p></th>


						<th width="100"><p>Shipment Qty</p></th>
						<th width="100"><p>Ship. Gross Value $</p></th>
						<th width="100"><p>Ship. Net Value $</p></th>
		                <th width="100"><p>Ship. Retn. Qty</p></th>
		            </tr>
	            </thead>

				<?
				$k = 1;
				foreach ($rev_qty2 as $company_id => $row)
				{
					if ($k % 2 == 1) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

					$recv_qty2=$row['recv_qty_in']+$row['recv_qty_out']+$row['issue_rtn_qty'];

					$dyeing_in2=$row['dyeing']+$row['subc_dyeing'];
					$dye_fini_in_qty2=$row['dye_fin_qty_in']+$row['fin_product_qnty'];
					$poly_qty2=$row['inhouse_poly_qty']+$row['outbond_poly_qty']+$row['subc_poly_qty'];

					$fin_roll_rcv_qty2=$row['fin_roll_rcv_qty']+$row['fin_roll_iss_rtn_qty'];
					$emb_delivery= $row[1]['embroidery_delivery_qty']+ $row[2]['embroidery_delivery_qty'];
					$printing_delivery= $row[1]['printing_delivery_qty']+ $row[2]['printing_delivery_qty'];
					$knitting_qty= $row['knitting_qty_in'] + $row['knitting_qty_out'];
					$tota_finish_in_qty +=$row['finish_in_qty'];
					$tota_finish_out_qty +=$row['finish_out_qty'];
					$cutting_qc += $row['cutting_qc'];
					$printing_rcv= $row[1]['print_rcv_qty']+ $row[2]['print_rcv_qty'];
					$printing_production= $row[1]['printing_production_qty']+ $row[2]['printing_production_qty'];
					$poly_qty=$row['inhouse_poly_qty']+$row['outbond_poly_qty']+$qtyValue['subc_poly_qty'];


					$achivement		+=$com_acv_array[$company_id];

					$sewing_efficiency	+=$com_sewing_effi_arr[$company_id];
					?>

					<?
					$k++;
					$total_projec_qty_pcs2 += $row['projec_qty_pcs'];
					$total_confirm_qty_pcs2 += $row['confirm_qty_pcs'];
					$total_cancel_order_qty2 += $row['cancel_order_qty'];
					$total_inactive_qty2 += $row['inactive_qty_pcs'];
					$total_ref_closing_qty2 += $row['ref_closing_qty'];
					$total_sort_grey_qty2 += $row['sort_grey_qty'];
					$total_cutting_lay2 +=$row['cutting_lay'];
				    $finish_fab_roll_issue2 +=$row['fin_roll_rcv_qty'];

					$total_grey_recv_total2 += $row['grey_recv_total'];
					$total_grey_iss_total2 += $row['grey_iss_total'];
					$total_dyed_recv_total2 += $row['dyed_recv_total'];
					$total_dyed_iss_total2 += $row['dyed_iss_total'];

					$total_recv_qty2 += $recv_qty2;
					$total_issue_qty2 += $row['issue_qty'];
					$total_trans_in_qty2 += $row['trans_in_qty'];
					$total_trans_out_qty2 += $row['trans_out_qty'];
					$total_recv_batch_qty2 += $row['recv_batch_qty'];
					$total_batch_qty2 += $batch_qty2;

					$total_dyeing_in += $dyeing_in2;
					//$total_dyeing_out += $dyeing_out2; // 22
					$total_dye_fini_in_qty2 += $dye_fini_in_qty2;
					$total_dye_fin_qty_out2 += $row['dye_fin_qty_out']; //24
					$total_fin_roll_rcv_qty2 += $fin_roll_rcv_qty2; //25
					$total_fin_roll_issue_qty2 += $row['fin_roll_issue_qty']; //26
					$total_finish_fabric_issue_return2+= $finish_fabric_issue_return2; //26
					//27
					//28
					$total_aop_issue_qty2 += $row['aop_issue_qty'];
					$total_aop_recv_qty2 += $row['aop_recv_qty'];
					$total_cutting_fab_recv2 += $row['cutting_fab_recv'];
					$total_cutting_in2 += $cutting_inhouse_qty2;
					// $total_cutting_out2 += $row['cutting_out']; 33
					$tota_print_issue +=$row[2][1];
					$tota_print_rcv += $row[3][1];
					$tota_emb_issue += $row[2][2];
					$tota_emb_rcv += $row[3][2];

					$total_print_inhound2 += $print_inhound2;
					$total_print_outhound2 += $print_outhound2;
					$total_print_delivery_to_Cutting2 += $print_delivery_to_Cutting2;
					$total_embroidery_inhound2 += $embroidery_inhound2;
					$total_embroidery_outhound2 += $embroidery_outhound2;
					$total_embroidery_delivery_to_cutting2 += $embroidery_delivery_to_cutting2;

					$total_special_issue += $row['special_issue'];
					$total_special_rcv += $row['special_rcv'];
					$total_dyeing_issue += $row['dyeing_issue'];
					$total_dyeing_rcv += $row['dyeing_rcv'];
					$total_wash_issue += $row['wash_issue'];
					$total_wash_rcv += $row['wash_rcv'];
					$total_man_power2 += $row['man_power'];
					$total_operator2 += $row['operator'];
					$total_helper2 += $row['helper'];
					$total_sewing_inbound2 += $sewing_inbound_qty2;
					$total_sewing_outbound2 += $row['sewing_outbound_qty'];
					$total_iron += $row['iron'];
					$total_hangtag2 += $hangtag2;
					$total_poly_qty2 += $poly_qty2;
					$total_finish += $row['finish'];
					$total_ins_qty2 += $row['ins_qty'];
					$total_ship_qty2 += $row['ship_qty'];
					$total_ship_value2 += $row['ship_value'];
					$total_shipment_net_value2 += $shipment_net_value2;
					$total_ship_rtn_qty2 += $row['ship_rtn_qty'];
					$total_sewing_in2 +=$row['sewing_in'];
					$total_sewing_out2 +=$row['sewing_out'];



					?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					    <td><? echo $k; ?></td>
					    <td><? echo $company_arr[$company_id]; ?></td>
						<td align="right"> <p><? echo number_format($row['projec_qty_pcs'],2,'.',''); ?></p></td>
						<td align="right"><p><? echo number_format($row['confirm_qty_pcs'],2,'.',''); ?></p></td>
						<td align="right"><p><? echo number_format($row['cancel_order_qty'],2,'.',''); ?></p></td>
						<td align="right"><p><?=number_format($row['inactive_qty_pcs'],2,'.','');?></p></td>
						<td align="right"><p><? echo number_format($row['ref_closing_qty'],2,'.',''); ?></p></td>




						<td align="right"><p><? echo number_format($row['main_grey_qty'],2,'.',''); ?></p></td>

						<td align="right"><p><? echo number_format($row['sort_grey_qty'],2,'.',''); ?></p></td>
						<td align="right"><p><? echo number_format($row['grey_recv_total'],2,'.',''); ?></p></td>
						<td align="right"><p><? echo number_format($row['grey_iss_total'],2,'.',''); ?></p></td>
						<td align="right"><p><? echo number_format($row['dyed_recv_total'],2,'.',''); ?></p></td>
						<td align="right"><p><? echo number_format($row['dyed_iss_total'],2,'.',''); ?></p></td>


						<td align="right"><p><? echo number_format($knitting_qty,2,'.',''); ?></p></td>
						<td align="right"><p><? echo number_format($row['issue_qty'],2,'.',''); ?></p></td>
						<td align="right"><p><? echo number_format($row['trans_in_qty'],2,'.',''); ?></p></td>
						<td align="right"><p><? echo number_format($row['trans_out_qty'],2,'.',''); ?></p></td>

						<td align="right"><p><? echo number_format($row['fin_roll_rcv_qty'],2,'.',''); ?></p></td>


						<td align="right"><p><? echo number_format($row['fin_roll_issue_qty'],2,'.',''); ?></p></td>

						<td align="right"><p><? echo number_format($row['finish_in_qty'],2,'.',''); ?></p></td>
						<td align="right"><p><? echo number_format($row['finish_out_qty'],2,'.',''); ?></p></td>




						<td align="right"><p><?=number_format($total_cutting_lay2,2,'.','');?></p></td>

						<td align="right"><p><?=number_format($row['cutting_qc'],2,'.','');?></p></td>



						<td align="right"><p><?=number_format($row[2][1],2,'.','');?></p></td>


						<td align="right"><p><?=number_format($row[3][1],2,'.','');?></p></td>


						<td align="right"><p><?=number_format($row[2][2],2,'.','');?></p></td>




						<td align="right"><p><?=number_format($row[3][2],2,'.','');?></p></td>


						<td align="right"><p><?=number_format($printing_rcv,2,'.','');?></p></td>


						<td align="right"><p><?=number_format($printing_production,2,'.','');?></p></td>

						<td align="right"><p><?=number_format($printing_delivery,2,'.','');?></p></td>


						<td align="right"><p><?=number_format($row['embo_rcv_qty'],2,'.','');?></p></td>


						<td align="right"><p><?=number_format($row['embroidery_production_qty'],2,'.','');?></p></td>

						<td align="right"><p><?=number_format($emb_delivery,2,'.','');?></p></td>



						<td align="right"><p><? echo number_format($row['man_power'], 2,'.',''); ?></p></td>
						<td align="right"><p><? echo number_format($row['operator'], 2,'.',''); ?></p></td>
						<td align="right"><p><? echo number_format($row['helper'], 2,'.',''); ?></p></td>
						<td align="right"><p><p><? echo number_format($row['sewing_in'], 2,'.',''); ?></p></p></td>
						<td align="right"><p><? echo number_format($row['sewing_out'], 2,'.',''); ?></p></td>
						<td align="right"  title="(sewing out/(target*working hour)*100"><? echo number_format($com_acv_array[$company_id],2); ?>%</td>
							<td align="right" title="(sewing out*smv)/(manpower*working hour*60)*100"><? echo number_format($com_sewing_effi_arr[$company_id],2); ?>%</td>
						<td align="right"><p><? echo number_format($row['iron'], 2,'.',''); ?></p></td>
						<td align="right"><p><? echo number_format($hangtag=$row['hang_tag_qty'], 2,'.',''); ?></p></td>
						<td align="right"><? echo number_format($poly_qty, 2,'.',''); ?></td>
						<td align="right"><p><? echo number_format($row['finish'], 2,'.',''); ?></p></td>

						<td align="right"><p><? echo number_format($row['ship_qty'], 2,'.',''); ?></p></td>
						<td align="right"><p><? echo number_format($row['ship_value'], 2,'.',''); ?></p></td>
						<td align="right"><p><? echo number_format($shipment_net_value, 2); ?></p></td>
						<td align="right"><p><? echo number_format($row['ship_rtn_qty'], 2,'.','');?></p></td>

				</tr>
				<?

				}
				?>
	            <tr bgcolor="<? echo $bgcolor; ?>">
	                <td align="left" colspan="2"><b>Total</b></td>
											<td align="right"><p><b><? echo number_format($total_projec_qty_pcs2,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><? echo number_format($total_confirm_qty_pcs2,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><? echo number_format($total_cancel_order_qty2,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><? echo number_format($total_inactive_qty2,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><? echo number_format($total_ref_closing_qty,2,'.',''); ?></b></p></td>

											<td align="right"><p><b><?=number_format($total_main_grey_qty,2,'.','')?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_sort_grey_qty,2,'.',''); ?></b></p></td>

											<td align="right"><p><b><?php echo number_format($total_grey_recv,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_grey_iss,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_dyed_recv,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_dyed_iss,2,'.',''); ?></b></p></td>


											<td align="right"><p><b><?php echo number_format($total_recv_qty,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_issue_qty,2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_trans_in_qty, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_trans_out_qty, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($finish_fab_roll_issue2, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_fin_roll_issue_qty, 2,'.',''); ?></b></p></td>

											<td align="right"><p><b><?php echo number_format($tota_finish_in_qty , 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($tota_finish_out_qty , 2,'.',''); ?></b></p></td>



											<td align="right"><p><b><?=number_format($total_cutting_lay2, 2,'.','');?></b></p></td>
											<td align="right"><p><b><?=number_format($cutting_qc, 2,'.','');?></b></p></td>
											<td align="right"><p><b><?=number_format($tota_print_issue, 2,'.','');?></b></p></td>
											<td align="right"><p><b><?=number_format($tota_print_rcv , 2,'.','');?></b></p></td>
											<td align="right"><p><b><?=number_format($tota_emb_issue  , 2,'.','');?></b></p></td>
											<td align="right"><p><b><?=number_format($tota_emb_rcv  , 2,'.','');?></b></p></td>

											<td align="right"><p><b><?=number_format($total_print_in_rcv , 2,'.','')?></b></p> </td>
											<td align="right"><p><b><?=number_format($total_print_prod_in , 2,'.','')?></b></p></td>
											<td align="right"><p><b><?=number_format($total_print_delev_in  , 2,'.','')?></b></p> </td>

											<td align="right"><p><b><?=number_format($total_embo_in , 2,'.','')?></b></p> </td>
											<td align="right"><p><b><?=number_format($total_emb_prod_in , 2,'.','')?></b></p></td>
											<td align="right"><p><b><?=number_format($total_emb_delev_in  , 2,'.','')?></b></p> </td>

											<td align="right"><p><b><?php echo number_format($total_man_power, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_operator, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_helper, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?=number_format($total_sewing_in2, 2,'.','') ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_sewing_out2, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?=number_format($achivement, 2,'.','') ?>%</b></p></td>
											<td align="right"><p><b><?php echo number_format($sewing_efficiency, 2,'.',''); ?>%</b></p></td>

											<td align="right"><p><b><?php echo number_format($total_iron_qty, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_hangtag_qty, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_poly_qty, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><?php echo number_format($total_finish_qty, 2,'.',''); ?></b></p></td>


											<td align="right"><p><b><? echo number_format($total_ship_qty, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><? echo number_format($total_ship_value, 2,'.',''); ?></b></p></td>
											<td align="right"><p><b><? echo number_format($total_shipment_net_value_qty, 2,'.',''); ?></b></p></td>
											<td align="right"><b><? echo number_format($total_ship_rtn_qty, 2,'.',''); ?></b></td>
	            </tr>
	        </table>

	    </fieldset>


	    <!-- ======FACTORY WISE PRODUCTION SUMMARY START========== -->
	    <!-- ======Compuny,location wise summury================== -->

	    <!-- ======FACTORY WISE PRODUCTION SUMMARY END============ -->
		<?
		foreach (glob("$user_id*.xls") as $filename) {
			if (@filemtime($filename) < (time() - $seconds_old))
				@unlink($filename);
		}
		//---------end------------//
		$name = time();
		$filename = $user_id . "_" . $name . ".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc, ob_get_contents());
		//$filename=$user_id."_".$name.".xls";
		echo "$total_data####$filename";
	}
	else if($type==6) // Show 6
	{
		$location_id_cond_subc = ($location_id == 0) ? "" : " and a.knit_location_id=$location_id";
		$location_id_cond_y = ($location_id == 0) ? "" : " and d.location_id=$location_id";
		$location_id_cond_trns = ($location_id == 0) ? "" : " and b.location_id=$location_id";
		$knit_lc_location_id_cond = ($location_id == 0) ? "" : " and a.location_id=$location_id";
		$location_id_issue_cond = ($location_id == 0) ? "" : " and b.location_id=$location_id";
		$working_location_cond = ($location_id == 0) ? "" : " and a.working_location=$location_id";

		$company_cond_order = ($company_name == 0) ? "" : " and b.company_name in($company_name)";
		$location_id_cond_order = ($location_id == 0) ? "" : " and b.location_name in($location_id)";
		$company_cond_sort_book = ($company_name == 0) ? "" : " and a.company_id in($company_name)";
		$company_cond_transf = ($company_name == 0) ? "" : " and b.company_id in($company_name)";
		$working_company_id_cond = ($company_name == 0) ? "" : " and a.working_company_id in($company_name)";
		$working_company_cond = ($company_name == 0) ? "" : " and a.working_company in ($company_name)";
		$location_cond4 = ($location_id == 0) ? "" : " and a.location_id=$location_id";


			//------------------------------------ Order info--------------------------------------------------//
		$order_sql=("SELECT a.job_no_mst, sum(a.po_quantity) as po_quantity, sum(a.po_quantity*b.total_set_qnty) as po_quantity_psc,
        sum(case when a.is_confirmed=1 then a.po_quantity*b.total_set_qnty else 0 end) as confirm_qty_pcs,
        sum(case when a.is_confirmed=2 then a.po_quantity*b.total_set_qnty else 0 end) as projec_qty_pcs,
        a.packing, b.company_name, b.location_name, b.order_uom , b.total_set_qnty, a.po_received_date
        from wo_po_break_down a, wo_po_details_master b
        where a.job_id=b.id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_cond_order $date_cond_order $location_id_cond_order
        group by a.job_no_mst, a.packing, b.company_name, b.location_name, b.order_uom, b.total_set_qnty, a.po_received_date order by a.po_received_date");
		// echo $order_sql;die;
		$rev_qty = array();
		foreach (sql_select($order_sql) as $value)
		{
			$rev_qty[$value[csf('company_name')]][strtotime($value[csf('po_received_date')])]['confirm_qty_pcs'] += $value[csf('confirm_qty_pcs')];
			$rev_qty[$value[csf('company_name')]][strtotime($value[csf('po_received_date')])]['projec_qty_pcs'] += $value[csf('projec_qty_pcs')];
			$rev_qty[$value[csf('company_name')]][strtotime($value[csf('po_received_date')])]['inactive_qty_pcs'] += $value[csf('inactive_qty_pcs')];

			$rev_qty2[$value[csf('company_name')]]['confirm_qty_pcs'] += $value[csf('confirm_qty_pcs')];
			$rev_qty2[$value[csf('company_name')]]['projec_qty_pcs'] += $value[csf('projec_qty_pcs')];
			$rev_qty2[$value[csf('company_name')]]['inactive_qty_pcs'] += $value[csf('inactive_qty_pcs')];
		}
	  	// echo "<pre>";print_r($rev_qty2); die;

		// -----------------------------------------------------Cancel Order------------------------------------------//

		$cancel_order_sql=("SELECT a.job_no_mst, b.company_name, a.po_received_date, $cancel_date, b.location_name, a.packing, b.total_set_qnty,
        sum(case when a.is_confirmed=1 then a.po_quantity*b.total_set_qnty else 0 end) as cancel_order_qty
        from wo_po_break_down a, wo_po_details_master b
        where a.job_no_mst=b.job_no and a.status_active in(3) and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_cond_order $date_cond_order2 $location_id_cond_order
        group by a.job_no_mst, b.company_name, a.update_date, a.po_received_date, b.location_name, a.packing, b.total_set_qnty order by a.update_date");
		// echo $cancel_order_sql;die;
		foreach (sql_select($cancel_order_sql) as $value)
		{
			$date=$value[csf('cancel_date')];
			$cancel_date2=strtoupper(date("d-M-y", strtotime("$date")));
			$rev_qty[$value[csf('company_name')]][strtotime($cancel_date2)]['cancel_order_qty'] += $value[csf('cancel_order_qty')];
			$rev_qty2[$value[csf('company_name')]]['cancel_order_qty'] += $value[csf('cancel_order_qty')];
		}
		// echo "<pre>";print_r($rev_qty2);

		//  echo "<pre>";print_r($rev_qty);





		//-------------------------------------------------------------------Main Fabric Booking ----------------------------------//

		$main_fab_book_sql=("SELECT a.company_id, a.booking_date, sum(b.grey_fab_qnty) as main_grey_qty  from wo_booking_mst a,  wo_booking_dtls b
        where a.booking_no=b.booking_no and a.job_no=b.job_no and a.entry_form=118  and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $company_cond_sort_book $date_cond_sort_book
        group by a.company_id, a.booking_date order by a.booking_date");
		// echo $main_fab_book_sql; die;
		foreach (sql_select($main_fab_book_sql) as $value) // Location not found
		{
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('booking_date')])]['main_grey_qty'] += $value[csf('main_grey_qty')];
			$rev_qty2[$value[csf('company_id')]]['main_grey_qty'] += $value[csf('main_grey_qty')];
		}


		// =================================== Short Fabric Booking =================================================================

		$sort_fab_book_sql=("SELECT a.company_id, a.booking_date, sum(b.grey_fab_qnty) as sort_grey_qty  from wo_booking_mst a,  wo_booking_dtls b
        where a.booking_no=b.booking_no and a.job_no=b.job_no and a.entry_form=88  and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0  $company_cond_sort_book $date_cond_sort_book
        group by a.company_id, a.booking_date order by a.booking_date");

		// echo $sort_fab_book_sql; die;
		foreach (sql_select($sort_fab_book_sql) as $value) // Location not found
		{
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('booking_date')])]['sort_grey_qty'] += $value[csf('sort_grey_qty')];
			$rev_qty2[$value[csf('company_id')]]['sort_grey_qty'] += $value[csf('sort_grey_qty')];
		}

		//================================== Sample Sewing Output (Sample Production [Pcs]) =====================================

		$sample_prod_sql=("SELECT a.company_id, b.sewing_date, a.location, sum(b.qc_pass_qty) as sample_prod_qty  from sample_sewing_output_mst a,  sample_sewing_output_dtls b
		where a.id=b.sample_sewing_output_mst_id and a.entry_form_id=130 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		$company_cond_sort_book $location_cond $date_cond_sample_prod
		group by a.company_id, b.sewing_date, a.location order by b.sewing_date");

		// echo $sample_prod_sql;die;
		foreach (sql_select($sample_prod_sql) as $value)
		{
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('sewing_date')])]['sample_prod_qty'] += $value[csf('sample_prod_qty')];

			$rev_qty2[$value[csf('company_id')]]['sample_prod_qty'] += $value[csf('sample_prod_qty')];

		}


		//============================= Sample Delivery Entry (Sample Delivery [Pcs]) ==================================

		$sample_delivery_sql=("SELECT a.company_id, a.ex_factory_date, a.location, sum(b.ex_factory_qty) as sample_delivery_qty from sample_ex_factory_mst a,  sample_ex_factory_dtls b
		where a.id=b.sample_ex_factory_mst_id and a.entry_form_id=132 and b.entry_form_id=132 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		$company_cond_sort_book $location_cond $date_cond_sample_delivery
		group by a.company_id, a.ex_factory_date, a.location order by ex_factory_date");

		//echo $sample_delivery_sql;die;
		foreach (sql_select($sample_delivery_sql) as $value)
		{
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('ex_factory_date')])]['sample_delivery_qty'] += $value[csf('sample_delivery_qty')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('ex_factory_date')])]['location_id'] = $value[csf('location')];
			$rev_qty2[$value[csf('company_id')]]['sample_delivery_qty'] += $value[csf('sample_delivery_qty')];
			$rev_qty2[$value[csf('company_id')]]['location_id'] = $value[csf('location')];
		}


		//===================================================  Yarn Info ================================================
		$yarn_sql="SELECT b.mst_id, b.prod_id, b.company_id, b.transaction_date,
		(case when b.transaction_type in (1,4,5) and c.dyed_type in(0,2) then b.cons_quantity else 0 end) as grey_recv_total,
		(case when b.transaction_type in (1,4,5) and c.dyed_type in(1) then b.cons_quantity else 0 end) as dyed_recv_total,
		(case when b.transaction_type in (2,3,6) and c.dyed_type in(0,2) then b.cons_quantity else 0 end) as grey_iss_total,
		(case when b.transaction_type in (2,3,6) and c.dyed_type in(1) then b.cons_quantity else 0 end) as dyed_iss_total
		from inv_transaction b, product_details_master c, lib_store_location d
		where b.prod_id=c.id and b.store_id=d.id and b.item_category=1 and b.transaction_type in(1,2,3,4,5,6) and b.item_category=1 $company_cond_transf $location_id_cond_y $date_cond_tran
		and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 order by transaction_date";
		//  echo $yarn_sql;die;
		$yarn_sql_result=sql_select($yarn_sql);
		foreach ($yarn_sql_result as $value)
		{
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('transaction_date')])]['grey_recv_total'] += $value[csf('grey_recv_total')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('transaction_date')])]['dyed_recv_total'] += $value[csf('dyed_recv_total')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('transaction_date')])]['grey_iss_total'] += $value[csf('grey_iss_total')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('transaction_date')])]['dyed_iss_total'] += $value[csf('dyed_iss_total')];

			$rev_qty2[$value[csf('company_id')]]['grey_recv_total'] += $value[csf('grey_recv_total')];
			$rev_qty2[$value[csf('company_id')]]['dyed_recv_total'] += $value[csf('dyed_recv_total')];
			$rev_qty2[$value[csf('company_id')]]['grey_iss_total'] += $value[csf('grey_iss_total')];
			$rev_qty2[$value[csf('company_id')]]['dyed_iss_total'] += $value[csf('dyed_iss_total')];
		}

			//==============================  knitting and Roll Recv summary ==============================
			$knit_and_rollRecv_sqls="SELECT x.receive_date, x.knitting_company,x.location_id, sum(x.recv_qty_in) as recv_qty_in, sum(x.recv_qty_out) as recv_qty_out, sum(x.knitting_qty_in) as knitting_qty_in, sum(x.knitting_qty_out) as knitting_qty_out
			FROM (
			SELECT a.receive_date, a.knitting_company, a.knitting_location_id as location_id,
			sum(case when a.entry_form =58 then b.grey_receive_qnty else 0 end) as recv_qty_in,
			0 as recv_qty_out,
			sum(case when a.entry_form =2 then b.grey_receive_qnty else 0 end) as knitting_qty_in,
			0 as knitting_qty_out
			from inv_receive_master a, pro_grey_prod_entry_dtls b
			where a.id=b.mst_id and a.status_active=1 $date_cond_knit $company_knit_cond $location_id_cond and a.entry_form in(2,58) and a.item_category=13 and a.is_deleted=0
			and a.knitting_source=1 group by a.receive_date,a.knitting_company,a.knitting_location_id
			UNION ALL
			SELECT a.receive_date, a.company_id as knitting_company, a.location_id,
			0 as recv_qty_in,
			sum(case when a.entry_form =58 then b.grey_receive_qnty else 0 end) as recv_qty_out,
			0 as knitting_qty_in,
			sum(case when a.entry_form =2 then b.grey_receive_qnty else 0 end) as knitting_qty_out
			from inv_receive_master a, pro_grey_prod_entry_dtls b
			where a.id=b.mst_id and a.status_active=1 $date_cond_knit $company_cond_sort_book $knit_lc_location_id_cond and a.entry_form in(2,58) and a.item_category=13 and a.is_deleted=0
			and a.knitting_source=3 group by a.receive_date,a.company_id,a.location_id )  x
			group by x.receive_date, x.knitting_company,x.location_id order by x.receive_date";
			// echo $knit_and_rollRecv_sqls;die;
			$knit_and_rollRecv_sqls_result=sql_select($knit_and_rollRecv_sqls);
			foreach ($knit_and_rollRecv_sqls_result as $value)
			{
				$rev_qty[$value[csf('knitting_company')]][strtotime($value[csf('receive_date')])]['knitting_qty_in'] += $value[csf('knitting_qty_in')];
				$rev_qty[$value[csf('knitting_company')]][strtotime($value[csf('receive_date')])]['knitting_qty_out'] += $value[csf('knitting_qty_out')];
				$rev_qty2[$value[csf('knitting_company')]]['knitting_qty_in'] += $value[csf('knitting_qty_in')];
				$rev_qty2[$value[csf('knitting_company')]]['knitting_qty_out'] += $value[csf('knitting_qty_out')];
				// ========
				$rev_qty[$value[csf('knitting_company')]][strtotime($value[csf('receive_date')])]['recv_qty_in'] += $value[csf('recv_qty_in')];
				$rev_qty[$value[csf('knitting_company')]][strtotime($value[csf('receive_date')])]['recv_qty_out'] += $value[csf('recv_qty_out')];
				$rev_qty2[$value[csf('knitting_company')]]['recv_qty_in'] += $value[csf('recv_qty_in')];
				$rev_qty2[$value[csf('knitting_company')]]['recv_qty_out'] += $value[csf('recv_qty_out')];
			}

			//=============================== Sub-Contact Knitting Production =================================
			$knit_subc_sqls="SELECT x.product_date, x.knitting_company,x.location_id, sum(x.subc_knitting_qty_in) as subc_knitting_qty_in, sum(x.subc_knitting_qty_out) as subc_knitting_qty_out
			FROM (
			SELECT a.product_date, a.knitting_company, a.knit_location_id as location_id, sum(b.product_qnty) as subc_knitting_qty_in, 0 as subc_knitting_qty_out
			from subcon_production_mst a, subcon_production_dtls b
			where a.id=b.mst_id $date_cond_subc $company_knit_cond $location_id_cond_subc
			and a.entry_form=159 and a.knitting_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
			group by a.product_date,a.knitting_company, a.knit_location_id
			union all
			select a.product_date, a.company_id as knitting_company, a.location_id, 0 as subc_knitting_qty_in, sum(b.product_qnty) as subc_knitting_qty_out
			from subcon_production_mst a, subcon_production_dtls b
			where a.id=b.mst_id $date_cond_subc $company_cond_sort_book $knit_lc_location_id_cond and a.entry_form=159 and a.knitting_source=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
			group by a.product_date,a.company_id,a.location_id )  x
			group by x.product_date, x.knitting_company,x.location_id order by x.product_date";
			// echo $knit_subc_sqls;die;
			$knit_subc_sqls_result=sql_select($knit_subc_sqls);
			foreach ($knit_subc_sqls_result as $value)
			{
				$rev_qty[$value[csf('knitting_company')]][strtotime($value[csf('product_date')])]['subc_knitting_qty_in'] += $value[csf('subc_knitting_qty_in')];
				$rev_qty[$value[csf('knitting_company')]][strtotime($value[csf('product_date')])]['subc_knitting_qty_out'] += $value[csf('subc_knitting_qty_out')];
				$rev_qty2[$value[csf('knitting_company')]]['subc_knitting_qty_in'] += $value[csf('subc_knitting_qty_in')];
				$rev_qty2[$value[csf('knitting_company')]]['subc_knitting_qty_out'] += $value[csf('subc_knitting_qty_out')];
			}


				//================================= Roll Issue ============================
		$roll_issue_sql="SELECT a.issue_date, a.company_id, b.location_id, sum(d.qnty) as issue_qty, d.barcode_no
		from inv_issue_master a, inv_transaction b, inv_grey_fabric_issue_dtls c, pro_roll_details d
		where a.id=b.mst_id and a.id=c.mst_id and b.id=c.trans_id and a.id=d.mst_id and c.id=d.dtls_id and a.entry_form=61 and d.entry_form=61 and b.item_category=13 and b.transaction_type=2
		and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $company_cond_sort_book $date_cond_tran $location_id_issue_cond
		group by a.issue_date, a.company_id, b.location_id, d.barcode_no order by a.issue_date";
		$roll_issue_sql_result=sql_select($roll_issue_sql);// and a.issue_number='RpC-KGIR-21-00033'
		$issue_barcode_no = array();
		foreach ($roll_issue_sql_result as $value)
	 	{
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('issue_date')])]['issue_qty'] += $value[csf('issue_qty')];
			$rev_qty2[$value[csf('company_id')]]['issue_qty'] += $value[csf('issue_qty')];
			$issue_barcode_no[$value[csf('barcode_no')]]=$value[csf('barcode_no')];
		}

		// Grey Roll Receive For Batch summary
		//===============user for roll receive for batch, location not in roll receive for batch page start=========================//
		$all_issue_barcode_arr = array_filter(array_unique($issue_barcode_no));
		if(count($all_issue_barcode_arr)>0)
		{
			$all_issue_barcode = implode(",", $all_issue_barcode_arr);
			$barcodeCond = $all_issue_barcode_cond = "";

			if($db_type==2 && count($all_issue_barcode_arr)>999)
			{
				$all_barcode_no_chunk=array_chunk($all_issue_barcode_arr,999) ;
				foreach($all_barcode_no_chunk as $chunk_arr)
				{
					$barcodeCond.=" c.barcode_no in(".implode(",",$chunk_arr).") or ";
				}

				$all_issue_barcode_cond.=" and (".chop($barcodeCond,'or ').")";
			}
			else
			{
				$all_issue_barcode_cond=" and c.barcode_no in($all_issue_barcode)";
			}

			$recv_roll_batch_sql="SELECT c.barcode_no from inv_receive_mas_batchroll a, pro_grey_batch_dtls b, pro_roll_details c
			where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and a.entry_form=62 and c.entry_form=62
			and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $all_issue_barcode_cond";
			$recv_roll_batch_sql_result=sql_select($recv_roll_batch_sql);
			$recv_batch_barcode_no=array();
			foreach ($recv_roll_batch_sql_result as $value)
		 	{
				$recv_batch_barcode_no[$value[csf('barcode_no')]]=$value[csf('barcode_no')];
			}
		}

		$all_recv_batch_barcode_arr = array_filter(array_unique($recv_batch_barcode_no));
		if(count($all_recv_batch_barcode_arr)>0)
		{
			$all_recv_batch_barcode = implode(",", $all_recv_batch_barcode_arr);
			$recv_batch_barcodeCond = $all_rcv_batch_barcode_cond = "";
			if($db_type==2 && count($all_recv_batch_barcode_arr)>999)
			{
				$all_barcode_no_chunk=array_chunk($all_recv_batch_barcode_arr,999) ;
				foreach($all_barcode_no_chunk as $chunk_arr)
				{
					$recv_batch_barcodeCond.=" d.barcode_no in(".implode(",",$chunk_arr).") or ";
				}

				$all_rcv_batch_barcode_cond.=" and (".chop($recv_batch_barcodeCond,'or ').")";
			}
			else
			{
				$all_rcv_batch_barcode_cond=" and d.barcode_no in($all_recv_batch_barcode)";
			}

			$issue_to_recv_batch_sql="SELECT a.issue_date, a.company_id, b.location_id, sum(d.qnty) as recv_batch_qty, d.barcode_no
			from inv_issue_master a, inv_transaction b, inv_grey_fabric_issue_dtls c, pro_roll_details d
			where a.id=b.mst_id and a.id=c.mst_id and b.id=c.trans_id and a.id=d.mst_id and c.id=d.dtls_id and a.entry_form=61 and d.entry_form=61 and b.item_category=13 and b.transaction_type=2
			and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_cond_sort_book $date_cond_tran $location_id_issue_cond $all_rcv_batch_barcode_cond
			group by a.issue_date, a.company_id, b.location_id, d.barcode_no order by a.issue_date";
			$issue_to_recv_batch_result=sql_select($issue_to_recv_batch_sql);
			foreach ($issue_to_recv_batch_result as $value)
		 	{
				$rev_qty[$value[csf('company_id')]][strtotime($value[csf('issue_date')])]['recv_batch_qty'] += $value[csf('recv_batch_qty')];
				$rev_qty2[$value[csf('company_id')]]['recv_batch_qty'] += $value[csf('recv_batch_qty')];
			}
		}
		//===============user for roll receive for batch, location not in roll receive for batch page end===============//

		// Roll Issue Retn and Transfer In & Out
		$roll_issue_retn_transfer_sql="SELECT b.company_id, b.transaction_date, d.location_id,
		sum(case when  b.transaction_type=4 then b.cons_quantity else 0 end) as issue_rtn_qty,
		sum(case when  b.transaction_type=5 then b.cons_quantity else 0 end) as trans_in_qty,
		sum(case when  b.transaction_type=6 then b.cons_quantity else 0 end) as trans_out_qty
		from inv_item_transfer_mst a, inv_transaction b, lib_store_location d
		where  a.id=b.mst_id and b.store_id=d.id and b.item_category=13 and b.transaction_type in(4,5,6)and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_cond_transf $location_id_cond_y $date_cond_tran and a.entry_form=82
		group by b.company_id, b.transaction_date, d.location_id order by b.transaction_date";


		$roll_issue_retn_transfer_sql_result=sql_select($roll_issue_retn_transfer_sql);
		foreach ($roll_issue_retn_transfer_sql_result as $value)
	 	{
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('transaction_date')])]['issue_rtn_qty'] += $value[csf('issue_rtn_qty')];
			$rev_qty2[$value[csf('company_id')]]['issue_rtn_qty'] += $value[csf('issue_rtn_qty')];
			// ===========
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('transaction_date')])]['trans_in_qty'] += $value[csf('trans_in_qty')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('transaction_date')])]['trans_out_qty'] += $value[csf('trans_out_qty')];
			$rev_qty2[$value[csf('company_id')]]['trans_in_qty'] += $value[csf('trans_in_qty')];
			$rev_qty2[$value[csf('company_id')]]['trans_out_qty'] += $value[csf('trans_out_qty')];
		}


			// ================================= BATCH ==================================//
			$batch_sqls = sql_select("SELECT a.working_company_id, a.batch_date, d.location_id,
			sum(case when a.batch_against in(1,3,5) and a.entry_form=0 then b.batch_qnty else 0 end) as batch_qty,
			sum(case when a.batch_against in(2) and a.entry_form=0 then b.batch_qnty else 0 end) as re_process_batch_qty,
			a.total_trims_weight
			from pro_batch_create_dtls b, pro_batch_create_mst a left join lib_prod_floor d on a.floor_id=d.id
			where a.id=b.mst_id and a.batch_against in(1,2,3,5) and a.entry_form=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $working_company_id_cond $location_id_cond_y $batch_date_cond
			group by a.working_company_id, a.batch_date, d.location_id,a.total_trims_weight order by a.batch_date");

			// echo $batch_sql="SELECT a.working_company_id, a.batch_date, d.location_id,
			// sum(case when a.batch_against in(1,3,5) and a.entry_form=0 then b.batch_qnty else 0 end) as batch_qty,
			// sum(case when a.batch_against in(2) and a.entry_form=0 then b.batch_qnty else 0 end) as re_process_batch_qty
			// from pro_batch_create_dtls b, pro_batch_create_mst a left join lib_prod_floor d on a.floor_id=d.id
			// where a.id=b.mst_id and a.batch_against in(1,2,3,5) and a.entry_form=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $working_company_id_cond $location_id_cond_y $batch_date_cond
			// group by a.working_company_id, a.batch_date, d.location_id order by a.batch_date";
			//and a.batch_no='RpC-BC-21-00014'
			 foreach ($batch_sqls as $value)
			 {
				$rev_qty[$value[csf('working_company_id')]][strtotime($value[csf('batch_date')])]['batch_qty'] += $value[csf('batch_qty')];
				$rev_qty[$value[csf('working_company_id')]][strtotime($value[csf('batch_date')])]['trims_dyeing_qty'] += $value[csf('total_trims_weight')];
				$rev_qty[$value[csf('working_company_id')]][strtotime($value[csf('batch_date')])]['re_process_batch_qty'] += $value[csf('re_process_batch_qty')];
				$rev_qty2[$value[csf('working_company_id')]]['batch_qty'] += $value[csf('batch_qty')];
				$rev_qty2[$value[csf('working_company_id')]]['re_process_batch_qty'] += $value[csf('re_process_batch_qty')];
			}
			//========================= Subcon Batch Creation ================================= //
			$subc_batch_sqls = sql_select("SELECT a.company_id, a.batch_date, a.location_id,
			sum(case when a.batch_against in(1) and a.entry_form=36 then b.batch_qnty else 0 end) as subc_batch_qty,
			sum(case when a.batch_against in(2) and a.entry_form=36 then b.batch_qnty else 0 end) as subc_re_process_batch_qty
			from pro_batch_create_mst a, pro_batch_create_dtls b
			where a.id=b.mst_id and a.batch_against in(1,2) and a.entry_form=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_cond_sort_book $knit_lc_location_id_cond $batch_date_cond
			group by  a.company_id, a.batch_date, a.location_id order by a.batch_date");
			 foreach ($subc_batch_sqls as $value)
			 {
				$rev_qty[$value[csf('company_id')]][strtotime($value[csf('batch_date')])]['subc_batch_qty'] += $value[csf('subc_batch_qty')];
				$rev_qty[$value[csf('company_id')]][strtotime($value[csf('batch_date')])]['subc_re_process_batch_qty'] += $value[csf('subc_re_process_batch_qty')];
				$rev_qty2[$value[csf('company_id')]]['subc_batch_qty'] += $value[csf('subc_batch_qty')];
				$rev_qty2[$value[csf('company_id')]]['subc_re_process_batch_qty'] += $value[csf('subc_re_process_batch_qty')];
			}

			 //===================================== DYEING SUMMARY ================================================//
			$dyeing_sqls = sql_select("SELECT a.service_company, a.process_end_date as production_date, d.location_id,
			sum(case when a.entry_form=35 then c.batch_qnty else 0 end) as prod_qty,
			 sum(case when a.entry_form=36 then c.batch_qnty else 0 end) as subc_prod_qty
			from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c, lib_prod_floor d
			where a.batch_id=b.id and a.floor_id=d.id and a.service_source=1 and a.entry_form in(35,36) and a.load_unload_id=2 and b.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.batch_against in(1,2,3) and a.result=1 and b.is_sales!=1 $date_cond2 $company_dyeing_cond $location_id_cond_y
			group by a.service_company, a.process_end_date, d.location_id order by a.process_end_date");
			 foreach ($dyeing_sqls as $value)
			 {
				$rev_qty[$value[csf('service_company')]][strtotime($value[csf('production_date')])]['dyeing'] += $value[csf('prod_qty')];
				$rev_qty2[$value[csf('service_company')]]['dyeing'] += $value[csf('prod_qty')];
				//[$location_id_arr[$value[csf('floor_id')]]]
				$rev_qty[$value[csf('service_company')]][strtotime($value[csf('production_date')])]['subc_dyeing'] += $value[csf('subc_prod_qty')];
				$rev_qty2[$value[csf('service_company')]]['subc_dyeing'] += $value[csf('subc_prod_qty')];
			}

		//=========================================================== DYEING_FINISHING=======================================//

		$dyeing_finishing_sql="SELECT x.receive_date, x.knitting_company,x.location_id, sum(x.dye_fin_qty_in) as dye_fin_qty_in, sum(x.dye_fin_qty_out) as dye_fin_qty_out
		FROM ( SELECT a.knitting_source, a.receive_date, a.knitting_company, b.floor, a.recv_number, a.knitting_location_id as location_id,b.order_id,b.batch_id,b.gsm, b.receive_qnty as dye_fin_qty_in, 0 as dye_fin_qty_out,
		b.reject_qty,b.color_id,b.fabric_description_id,b.buyer_id, c.batch_no, a.recv_number, c.booking_no, c.booking_without_order,c.extention_no,b.remarks
		from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c
		where a.id=b.mst_id and b.batch_id = c.id and a.entry_form in(7,66) and a.item_category=2 and a.knitting_source=1 and a.status_active=1 and a.is_deleted=0 and c.status_active =1 $date_cond_knit $company_knit_cond $location_id_cond
		group by a.knitting_source, a.receive_date, a.knitting_company, b.floor, a.recv_number, a.knitting_location_id,b.order_id,b.batch_id,b.gsm, b.receive_qnty,
		b.reject_qty,b.color_id,b.fabric_description_id,b.buyer_id, c.batch_no, a.recv_number, c.booking_no, c.booking_without_order,c.extention_no,b.remarks
		UNION ALL
		SELECT a.knitting_source, a.receive_date, a.knitting_company, b.floor, a.recv_number, a.knitting_location_id as location_id, b.order_id,b.batch_id,b.gsm, 0 as dye_fin_qty_in, b.receive_qnty as dye_fin_qty_out,
		b.reject_qty,b.color_id,b.fabric_description_id,b.buyer_id, c.batch_no, a.recv_number, c.booking_no, c.booking_without_order,c.extention_no,b.remarks
		from inv_receive_master a, pro_finish_fabric_rcv_dtls b , pro_batch_create_mst c
		where a.id=b.mst_id and b.batch_id = c.id and a.entry_form in(7,66) and a.item_category=2 and a.knitting_source=3 and a.status_active=1 and a.is_deleted=0 and c.status_active =1 $date_cond_knit $company_cond_sort_book $knit_lc_location_id_cond
		group by a.knitting_source, a.receive_date, a.knitting_company, b.floor, a.recv_number, a.knitting_location_id,b.order_id,b.batch_id,b.gsm, b.receive_qnty,
		b.reject_qty,b.color_id,b.fabric_description_id,b.buyer_id, c.batch_no, a.recv_number, c.booking_no, c.booking_without_order,c.extention_no,b.remarks ) x
		group by x.receive_date, x.knitting_company,x.location_id order by x.receive_date";

		$dyeing_finishing_result=sql_select($dyeing_finishing_sql);
		foreach ($dyeing_finishing_result as $value)
		{
			$rev_qty[$value[csf('knitting_company')]][strtotime($value[csf('receive_date')])]['dye_fin_qty_in'] += $value[csf('dye_fin_qty_in')];
			$rev_qty[$value[csf('knitting_company')]][strtotime($value[csf('receive_date')])]['dye_fin_qty_out'] += $value[csf('dye_fin_qty_out')];
			$rev_qty2[$value[csf('knitting_company')]]['dye_fin_qty_in'] += $value[csf('dye_fin_qty_in')];
			$rev_qty2[$value[csf('knitting_company')]]['dye_fin_qty_out'] += $value[csf('dye_fin_qty_out')];
		}
		$subc_dyeing_fini_sql="SELECT a.company_id, a.product_date, a.location_id, sum(b.product_qnty) as fin_product_qnty from subcon_production_mst a, subcon_production_dtls b
		where a.id=b.mst_id and a.entry_form=292 $company_cond_sort_book $knit_lc_location_id_cond $date_cond_subc and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.company_id, a.product_date, a.location_id order by a.product_date";
		$subc_dyeing_fini_result=sql_select($subc_dyeing_fini_sql);
		foreach ($subc_dyeing_fini_result as $value)
		{
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('product_date')])]['fin_product_qnty'] += $value[csf('fin_product_qnty')];
			$rev_qty2[$value[csf('company_id')]]['fin_product_qnty'] += $value[csf('fin_product_qnty')];
		}



		$finish_fab_roll_issue_sql="SELECT b.company_id, b.transaction_date, d.location_id,sum(case when b.transaction_type=2 and b.item_category=2 then b.cons_quantity else 0 end) as fin_roll_issue_qty
		from inv_issue_master a, inv_transaction b, lib_store_location d
		where   a.id=b.mst_id and a.store_id=d.id and b.item_category in(2) and b.transaction_type in(2) and a.entry_form=71 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_cond_sort_book $location_id_cond_y $date_cond_tran
		group by b.company_id, b.transaction_date, d.location_id order by b.transaction_date";
		$finish_fab_roll_issue_result=sql_select($finish_fab_roll_issue_sql);
		foreach ($finish_fab_roll_issue_result as $value)
		{
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('transaction_date')])]['fin_roll_issue_qty'] += $value[csf('fin_roll_issue_qty')];
			$rev_qty2[$value[csf('company_id')]]['fin_roll_issue_qty'] += $value[csf('fin_roll_issue_qty')];
		}

		//Finish Fabric Roll Trasfer Recv

		//========================= Finish Fabric Roll Issue =====================================
		$rcv_date_cond = str_replace("b.transaction_date", "a.receive_date", $date_cond_tran);
		$sql = "SELECT a.company_id,a.receive_date, b.receive_qnty from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form=126 and a.item_category=2 and a.status_active=1 and b.status_active=1 $company_cond_sort_book $location_id_cond_y $rcv_date_cond";
		// echo $sql;die();
		$res = sql_select($sql);
		foreach ($res as $value)
		{
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('receive_date')])]['fin_roll_issue_rtn_qty'] += $value[csf('receive_qnty')];
			$rev_qty2[$value[csf('company_id')]]['fin_roll_issue_rtn_qty'] += $value[csf('receive_qnty')];
		}

		//============================== AOP Issue, Receive, Cutting Fabric Receive =========================
		$aop_issue_sql="SELECT a.company_id, a.receive_date,
		sum(case when a.entry_form=63 and a.process_id=35 then b.roll_wgt else 0 end) as aop_issue_qty,
		sum(case when a.entry_form=65 and a.process_id=0 then b.roll_wgt else 0 end) as aop_recv_qty,
		sum(case when a.entry_form=72 and a.process_id=0 then b.roll_wgt else 0 end) as cutting_fab_recv
		from inv_receive_mas_batchroll a, pro_grey_batch_dtls b
		where a.id=b.mst_id and a.entry_form in(63,65,72) and a.process_id in(35,0)  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_cond_sort_book $date_cond_knit group by a.company_id, a.receive_date order by a.receive_date";
		$aop_issue_result=sql_select($aop_issue_sql);
		foreach ($aop_issue_result as $value)
		{
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('receive_date')])]['aop_issue_qty'] += $value[csf('aop_issue_qty')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('receive_date')])]['aop_recv_qty'] += $value[csf('aop_recv_qty')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('receive_date')])]['cutting_fab_recv'] += $value[csf('cutting_fab_recv')];
			$rev_qty2[$value[csf('company_id')]]['aop_issue_qty'] += $value[csf('aop_issue_qty')];
			$rev_qty2[$value[csf('company_id')]]['aop_recv_qty'] += $value[csf('aop_recv_qty')];
			$rev_qty2[$value[csf('company_id')]]['cutting_fab_recv'] += $value[csf('cutting_fab_recv')];
		}
			//==========================================================$cutting Lay===================================//

		$cutting_data=("SELECT a.company_id, c.size_qty as qty   ,a.entry_date ,a.source
		from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle c
		where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id   and  a.status_active=1 and a.is_deleted=0 and b.status_active=1
		and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form=509 $cutting_date_cond $company_cond3  order by a.entry_date ");//a.entry_form=509
	    // echo $cutting_data;die;


	   $cutting=sql_select($cutting_data);
		foreach($cutting as $row)
		{
			$rev_qty[$row[csf('company_id')]][strtotime($row[csf('entry_date')])]['cutting_lay'] += $row[csf('qty')];
			$rev_qty2[$row[csf('company_id')]]['cutting_lay'] += $row[csf('qty')];
		}
		//echo"<pre>";print_r($rev_qty2);die;


		//==================== Total Man Power, No of Operator, No of Helper ===================================
		$prod_source_sql="SELECT a.company_id, c.pr_date, a.location_id, sum(c.man_power) as tot_man_power, sum(c.operator) as tot_operator, sum(c.helper) as tot_helper,sum(c.working_hour) as working_hour,sum(c.total_smv) as smv from prod_resource_mst a, prod_resource_dtls c
		where a.id=c.mst_id $company_cond_sort_book $knit_lc_location_id_cond $prod_sourc_date_cond
		and a.is_deleted=0 and c.is_deleted=0 group by a.company_id, c.pr_date, a.location_id order by c.pr_date";
		$prod_source_result=sql_select($prod_source_sql);
		$resource_data_arr = array();
		foreach ($prod_source_result as $value)
		{
			$resource_data_arr[$value[csf('company_id')]][strtotime($value[csf('pr_date')])]['man_power'] += $value[csf('tot_man_power')];
			$resource_data_arr[$value[csf('company_id')]][strtotime($value[csf('pr_date')])]['working_hour'] += $value[csf('working_hour')];

			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('pr_date')])]['man_power'] += $value[csf('tot_man_power')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('pr_date')])]['operator'] += $value[csf('tot_operator')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('pr_date')])]['helper'] += $value[csf('tot_helper')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('pr_date')])]['working_hour'] += $value[csf('working_hour')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('pr_date')])]['smv'] += $value[csf('smv')];
			$rev_qty2[$value[csf('company_id')]]['man_power'] += $value[csf('tot_man_power')];
			$rev_qty2[$value[csf('company_id')]]['operator'] += $value[csf('tot_operator')];
			$rev_qty2[$value[csf('company_id')]]['helper'] += $value[csf('tot_helper')];
		}
		// echo "<pre>";print_r($rev_qty3);

		//============================== garments_production ======================================
		$sqls = sql_select("SELECT d.style_ref_no, a.company_id,a.production_date,a.serving_company,a.location,a.production_type, a.embel_name, sum(b.production_qnty) as prod_quantity, a.production_source,a.po_break_down_id,a.item_number_id
		from pro_garments_production_mst a ,pro_garments_production_dtls b,wo_po_color_size_breakdown c,wo_po_details_master d
		where a.id=b.mst_id and a.production_type=b.production_type and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and  a.status_active=1 and d.id=c.job_id $date_cond $company_cond and a.is_deleted=0 $location_cond and b.status_active=1 and b.is_deleted=0
		group by d.style_ref_no, a.company_id,a.production_date,a.serving_company,a.location,a.production_type, a.embel_name, a.production_source,a.po_break_down_id,a.item_number_id order by a.production_date");
		//and a.production_type=5 and a.delivery_mst_id = 6546
		$sewing_data_array = array();
		foreach ($sqls as $value)
		{
			$lc_com_arr[$value[csf('company_id')]] = $value[csf('company_id')];
			$all_style_arr[$value[csf('style_ref_no')]] = $value[csf('style_ref_no')];
			$style_wise_po_arr[$value[csf('style_ref_no')]][$value[csf('po_break_down_id')]] = $value[csf('po_break_down_id')];

			if ($value[csf('production_type')] == 1 && $value[csf('production_source')]==1)
			{// cutting inhouse
				$rev_qty[$value[csf('serving_company')]][strtotime($value[csf('production_date')])]['cutting_inhouse'] += $value[csf('prod_quantity')];
				$rev_qty2[$value[csf('serving_company')]]['cutting_inhouse'] += $value[csf('prod_quantity')];
			}
			else if ($value[csf('production_type')] == 4)
			{
				$rev_qty[$value[csf('serving_company')]][strtotime($value[csf('production_date')])]['sewing_in'] += $value[csf('prod_quantity')];
				$rev_qty2[$value[csf('serving_company')]]['sewing_in'] += $value[csf('prod_quantity')];
			}
			else if ($value[csf('production_type')] == 5 && $value[csf('production_source')]==1)
			{
				$rev_qty[$value[csf('serving_company')]][strtotime($value[csf('production_date')])]['sewing_inhouse_qty'] += $value[csf('prod_quantity')];
				$rev_qty2[$value[csf('serving_company')]]['sewing_inhouse_qty'] += $value[csf('prod_quantity')];
			}
			else if ($value[csf('production_type')] == 11 && $value[csf('production_source')]==1)
			{
				$rev_qty[$value[csf('serving_company')]][strtotime($value[csf('production_date')])]['inhouse_poly_qty'] += $value[csf('prod_quantity')];
				$rev_qty2[$value[csf('serving_company')]]['inhouse_poly_qty'] += $value[csf('prod_quantity')];
			}
			else if ($value[csf('production_type')] == 7)
			{
				$rev_qty[$value[csf('serving_company')]][strtotime($value[csf('production_date')])]['iron'] += $value[csf('prod_quantity')];
				$rev_qty2[$value[csf('serving_company')]]['iron'] += $value[csf('prod_quantity')];
			}
			else if ($value[csf('production_type')] == 8)
			{
				$rev_qty[$value[csf('serving_company')]][strtotime($value[csf('production_date')])]['finish'] += $value[csf('prod_quantity')];
				$rev_qty2[$value[csf('serving_company')]]['finish'] += $value[csf('prod_quantity')];
			}
			else if ($value[csf('production_type')] == 2)
			{
				if ($value[csf('embel_name')] == 3)
				{
					$rev_qty[$value[csf('serving_company')]][strtotime($value[csf('production_date')])]['wash_issue'] += $value[csf('prod_quantity')];
					$rev_qty2[$value[csf('serving_company')]]['wash_issue'] += $value[csf('prod_quantity')];
				}
			}
			else if ($value[csf('production_type')] == 3)
			{
				if ($value[csf('embel_name')] == 3)
				{
					$rev_qty[$value[csf('serving_company')]][strtotime($value[csf('production_date')])]['wash_rcv'] += $value[csf('prod_quantity')];
					$rev_qty2[$value[csf('serving_company')]]['wash_rcv'] += $value[csf('prod_quantity')];
				}
			}
			if ($value[csf('production_type')] == 5)
			{
				$sewing_data_array[$value[csf('serving_company')]][strtotime($value[csf('production_date')])][$value[csf('po_break_down_id')]][$value[csf('item_number_id')]] += $value[csf('prod_quantity')];
			}
		}

		// echo"<pre>";print_r($sewing_data_array);

		$start_time_data_arr=sql_select("select company_name, shift_id, TO_CHAR(prod_start_time,'HH24:MI') as prod_start_time,TO_CHAR(lunch_start_time,'HH24:MI') as lunch_start_time from variable_settings_production where  company_name in($company_name) and  shift_id=1 and variable_list=26 and status_active=1 and is_deleted=0");
		//echo $start_time_data_arr;die;

      	foreach($start_time_data_arr as $row)
		{
			$start_time_arr[$row[csf('shift_id')]]['pst']=$row[csf('prod_start_time')];
			$start_time_arr[$row[csf('shift_id')]]['lst']=$row[csf('lunch_start_time')];
		}

        $prod_start_hour=$start_time_arr[1]['pst'];


		if($prod_start_hour=="") $prod_start_hour="08:00";
		$start_time=explode(":",$prod_start_hour);
		// $hour=substr($start_time[0],1,1);
		$hour = $start_time[0]*1;
		$time1 = strtotime($hour.":00");
		$time2 = strtotime(date('H:i:s'));
		$difference_hour = round(((abs($time2 - $time1) / 3600)-1),0);
		//echo $difference_hour;die;



		/* =================================================================================/
		/									SMV Source										/
		/================================================================================= */
		$lc_com_ids = implode(",",$lc_com_arr);
		$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($lc_com_ids) and variable_list=25 and status_active=1 and is_deleted=0");
		// echo $smv_source."sdsdsdds";die;
		if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
		if($smv_source==3)
		{
			$style_nos=implode("','",$all_style_arr);
			$color_type_ids="'".implode("','",$color_type_array)."'";
			$gsdSql="SELECT a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID,a.COLOR_TYPE  from PPL_GSD_ENTRY_MST a where a.APPLICABLE_PERIOD <= $txt_date and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 and a.BULLETIN_TYPE=4  and A.STYLE_REF in('".$style_nos."') and a.APPROVED=1
			group by a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID,a.COLOR_TYPE
			 ORDER BY  a.GMTS_ITEM_ID ,a.APPLICABLE_PERIOD  DESC , a.id DESC"; //and a.BULLETIN_TYPE in(3,4)
			// echo $gsdSql; die;
			$gsdSqlResult = sql_select($gsdSql);
			//$gsdDataArr=array();
			foreach($gsdSqlResult as $rows)
			{
				// echo $rows[TOTAL_SMV]."<br>";
				foreach($style_wise_po_arr[$rows['STYLE_REF']] as $po_id)
				{
					// $po_id."=".$rows["GMTS_ITEM_ID"]."<br>";
					if($item_smv_array[$po_id][$rows["GMTS_ITEM_ID"]]=='')
					{
						$item_smv_array[$po_id][$rows["GMTS_ITEM_ID"]]=$rows["TOTAL_SMV"];
					}
					/* if($item_smv_array[$po_id][$rows['COLOR_TYPE']][$rows['GMTS_ITEM_ID']]=='')
					{
						$item_smv_array[$po_id][$rows['COLOR_TYPE']][$rows['GMTS_ITEM_ID']]=$rows['TOTAL_SMV'];
					}
					if($item_smv_array2[$po_id][$rows['GMTS_ITEM_ID']]=='')
					{
						$item_smv_array2[$po_id][$rows['GMTS_ITEM_ID']]=$rows['TOTAL_SMV'];
					} */
				}
			}

		}
		else
		{
			$style_nos=implode("','",$all_style_arr);
			$sql_item="SELECT b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, smv_pcs, smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,2,3) and A.STYLE_REF_NO in('".$style_nos."')"; //echo $sql_item;die;
			$resultItem=sql_select($sql_item);
			foreach($resultItem as $itemData)
			{
				if($smv_source==1)
				{
					$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs')];
				}
				else if($smv_source==2)
				{
					$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs_precost')];
				}
			}
		}

		// print_r($item_smv_array);
		$sewing_effi_array = array();
		$com_sewing_effi_arr = array();
		$com_acv_array = array();
		$sewing_acv_array = array();

		$com_sewing_effi_array = array();
		foreach ($sewing_data_array as $comkey => $com_data)
		{
			foreach ($com_data as $dtkey => $dt_data)
			{
				foreach ($dt_data as $pokey => $po_data)
				{
					foreach ($po_data as $itmkey => $itm_data)
					{
						// echo $itm_data;die;
						$sewing_effi_array[$comkey][$dtkey] += (($itm_data*$item_smv_array[$pokey][$itmkey])/($resource_data_arr[$comkey][$dtkey]['man_power']*$resource_data_arr[$comkey][$dtkey]['working_hour']*60))*100;

						 $sewing_acv_array[$comkey][$dtkey] += ($itm_data/($resource_data_arr[$comkey][$dtkey]['target']*$difference_hour))*100;
						// echo $difference_hour;die;

					//    $sewing_acv_array[$comkey][$dtkey] =$itm_data / (($resource_data_arr[$comkey][$dtkey]['target'])*($resource_data_arr[$comkey][$dtkey]['working_hour']));

						$com_sewing_effi_arr[$comkey] += (($itm_data*$item_smv_array[$pokey][$itmkey])/($resource_data_arr[$comkey][$dtkey]['man_power']*$resource_data_arr[$comkey][$dtkey]['working_hour']*60))*100;
						$com_acv_array[$comkey] += ($itm_data/($resource_data_arr[$comkey][$dtkey]['target']*$difference_hour))*100;


					}

				}
			}
		}
		// echo "<pre>";print_r($sewing_effi_array); die;

		// ==================================== emb data ======================================
		$company_cond_emb2 = str_replace("a.serving_company", "a.company_id", $company_cond_emb);
		$sqls_emb = "SELECT a.production_date,a.company_id,a.location,a.production_type, a.embel_name, sum(b.production_qnty) as prod_quantity,a.production_source
		from pro_garments_production_mst a ,pro_garments_production_dtls b,wo_po_color_size_breakdown c
		where a.id=b.mst_id and a.production_type=b.production_type and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and a.status_active=1 $date_cond $company_cond_emb2 and a.is_deleted=0 $location_cond_emb and b.status_active=1 and b.is_deleted=0 and a.production_type in(2,3)
		group by a.production_date,a.company_id,a.location,a.production_type, a.embel_name,a.production_source order by a.production_date";	//$company_cond_emb
		// echo $sqls_emb;die();
		$res = sql_select($sqls_emb);
	 	foreach($res as $value)
		{
			if ($value[csf('production_type')] == 2)
			{
				if ($value[csf('embel_name')] == 1)
				{
					$rev_qty[$value[csf('company_id')]][strtotime($value[csf('production_date')])][$value[csf('production_source')]]['print_issue'] += $value[csf('prod_quantity')];
					$rev_qty2[$value[csf('company_id')]][$value[csf('production_source')]]['print_issue'] += $value[csf('prod_quantity')];
				}

				else if ($value[csf('embel_name')] == 2) {
					$rev_qty[$value[csf('company_id')]][strtotime($value[csf('production_date')])][$value[csf('production_source')]]['emb_issue'] += $value[csf('prod_quantity')];
					$rev_qty2[$value[csf('company_id')]][$value[csf('production_source')]]['emb_issue'] += $value[csf('prod_quantity')];
				}

				else if ($value[csf('embel_name')] == 4) {
					$rev_qty[$value[csf('company_id')]][strtotime($value[csf('production_date')])][$value[csf('production_source')]]['special_issue'] += $value[csf('prod_quantity')];
					$rev_qty2[$value[csf('company_id')]]['special_issue'] += $value[csf('prod_quantity')];
				}

				else if ($value[csf('embel_name')] == 5) {
					$rev_qty[$value[csf('company_id')]][strtotime($value[csf('production_date')])]['dyeing_issue'] += $value[csf('prod_quantity')];
					$rev_qty2[$value[csf('company_id')]][$value[csf('production_source')]]['dyeing_issue'] += $value[csf('prod_quantity')];
				}
			}
			else if ($value[csf('production_type')] == 3)
			{
				if ($value[csf('embel_name')] == 1) {
					$rev_qty[$value[csf('company_id')]][strtotime($value[csf('production_date')])][$value[csf('production_source')]]['print_rcv'] += $value[csf('prod_quantity')];
					$rev_qty2[$value[csf('company_id')]][$value[csf('production_source')]]['print_rcv'] += $value[csf('prod_quantity')];
				}

				else if ($value[csf('embel_name')] == 2) {
					$rev_qty[$value[csf('company_id')]][strtotime($value[csf('production_date')])][$value[csf('production_source')]]['emb_rcv'] += $value[csf('prod_quantity')];
					$rev_qty2[$value[csf('company_id')]][$value[csf('production_source')]]['emb_rcv'] += $value[csf('prod_quantity')];
				}

				else if ($value[csf('embel_name')] == 4) {
					$rev_qty[$value[csf('company_id')]][strtotime($value[csf('production_date')])][$value[csf('production_source')]]['special_rcv'] += $value[csf('prod_quantity')];
					$rev_qty2[$value[csf('company_id')]]['special_rcv'] += $value[csf('prod_quantity')];
				}

				else if ($value[csf('embel_name')] == 5) {
					$rev_qty[$value[csf('company_id')]][strtotime($value[csf('production_date')])]['dyeing_rcv'] += $value[csf('prod_quantity')];
					$rev_qty2[$value[csf('company_id')]][$value[csf('production_source')]]['dyeing_rcv'] += $value[csf('prod_quantity')];
				}
			}
		}

		// echo "<pre>";print_r($rev_qty2);die();


		//============================ Buyer Inspection ====================================
		$buyer_inspec_sql="SELECT b.company_name, a.working_company, a.inspection_date, a.working_location, sum(c.ins_qty) as ins_qty
		from pro_buyer_inspection a, pro_buyer_inspection_breakdown c, wo_po_details_master b , wo_po_break_down d
		where a.id=c.mst_id and a.po_break_down_id=d.id and d.job_no_mst=b.job_no $company_cond_order $working_location_cond $inspection_date_cond and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0
		group by b.company_name, a.working_company, a.inspection_date, a.working_location order by a.inspection_date ";
		$buyer_inspec_result=sql_select($buyer_inspec_sql);
		foreach ($buyer_inspec_result as $value)
		{
			$rev_qty[$value[csf('company_name')]][strtotime($value[csf('inspection_date')])]['ins_qty'] += $value[csf('ins_qty')];
			$rev_qty2[$value[csf('company_name')]]['ins_qty'] += $value[csf('ins_qty')];
		}

		// ============================== ex-factory data ========================================
		$gmt_shipment_sql="SELECT a.sys_number, a.company_id, a.location_id,b.invoice_no, a.delivery_date, b.po_break_down_id, a.entry_form,
		sum(case when a.entry_form<>85 then b.ex_factory_qnty else 0 end) as ship_qty,
		sum(case when a.entry_form<>85 then b.ex_factory_qnty*(c.unit_price/d.total_set_qnty) else 0 end) as ship_value,
		sum(case when a.entry_form=85 then b.ex_factory_qnty else 0 end) as ship_rtn_qty, (c.unit_price/d.total_set_qnty) as unit_price
		from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b, wo_po_break_down c,  wo_po_details_master d
		where a.id=b.delivery_mst_id and  b.po_break_down_id=c.id and c.job_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_cond_sort_book $knit_lc_location_id_cond $delivery_date_cond
		group by a.sys_number,a.company_id, a.location_id,b.invoice_no, a.delivery_date, b.po_break_down_id,c.unit_price, a.entry_form, d.total_set_qnty order by a.delivery_date";
		// echo $gmt_shipment_sql;die();
		$gmt_shipment_result=sql_select($gmt_shipment_sql);
		$invoice_id_arr = array();
		foreach ($gmt_shipment_result as $value)
		{
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('delivery_date')])]['ship_qty'] += $value[csf('ship_qty')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('delivery_date')])]['ship_value'] += $value[csf('ship_value')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('delivery_date')])]['ship_rtn_qty'] += $value[csf('ship_rtn_qty')];
			$rev_qty2[$value[csf('company_id')]]['ship_qty'] += $value[csf('ship_qty')];
			$rev_qty2[$value[csf('company_id')]]['ship_value'] += $value[csf('ship_value')];
			$rev_qty2[$value[csf('company_id')]]['ship_rtn_qty'] += $value[csf('ship_rtn_qty')];
			$invoice_id_arr[$value[csf('invoice_no')]] = $value[csf('invoice_no')];
		}

		// ==============================================
		$inv_id_cond = where_con_using_array($invoice_id_arr,0,"a.id");
		$sqlEx = sql_select("SELECT a.benificiary_id,b.ex_factory_date,a.invoice_value,a.net_invo_value from com_export_invoice_ship_mst a,pro_ex_factory_mst b where a.id=b.invoice_no and a.status_active=1 and b.status_active=1 $inv_id_cond");
		$shipment_net_val_arr = array();
		foreach ($sqlEx as $val)
		{
			$shipment_net_val_arr[$val[csf('benificiary_id')]][strtotime($val[csf('ex_factory_date')])] += $val[csf('net_invo_value')];
		}
		// echo "<pre>";print_r($shipment_net_val_arr);die();

		// Poly outbound, sewing output outbond
		$outbound="SELECT a.production_date,a.company_id,a.location,a.production_type, a.embel_name, sum(b.production_qnty) as prod_quantity, a.production_source
		from pro_garments_production_mst a ,pro_garments_production_dtls b,wo_po_color_size_breakdown c
		where a.id=b.mst_id and a.production_type=b.production_type and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and a.production_source=3 and a.production_type in(1,5,11)
		$date_cond $company_cond_sort_book $location_cond
		and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0
		group by a.production_date,a.company_id,a.location,a.production_type, a.embel_name, a.production_source order by a.production_date ";
		$sewing_output_outbond_result=sql_select($outbound);
		foreach ($sewing_output_outbond_result as $value)
		{
			if ($value[csf('production_type')] == 11) // Outbound poly entry
			{
				$rev_qty[$value[csf('company_id')]][strtotime($value[csf('production_date')])]['outbond_poly_qty'] += $value[csf('prod_quantity')];
				$rev_qty2[$value[csf('company_id')]]['outbond_poly_qty'] += $value[csf('prod_quantity')];
			}
			else if ($value[csf('production_type')] == 5) // Outbound Bundle Wise Sewing Output
			{
				$rev_qty[$value[csf('company_id')]][strtotime($value[csf('production_date')])]['sewing_outbound_qty'] += $value[csf('prod_quantity')];
				$rev_qty2[$value[csf('company_id')]]['sewing_outbound_qty'] += $value[csf('prod_quantity')];
			}
			else if ($value[csf('production_type')] == 1) // Outbound cutting qty (Cutting QC V2 page)
			{
				$rev_qty[$value[csf('company_id')]][strtotime($value[csf('production_date')])]['cutting_outbound_qty'] += $value[csf('prod_quantity')];
				$rev_qty2[$value[csf('company_id')]]['cutting_outbound_qty'] += $value[csf('prod_quantity')];
			}
		}

		// Subcon Poly Entry, subc sewing output entry, subc Cutting Entry
		$subc_data_sql="SELECT a.production_date,a.company_id,a.location_id,a.production_type, sum(a.production_qnty) as subc_production_qnty
		from subcon_gmts_prod_dtls a where a.status_active=1 and a.is_deleted=0 $date_cond $company_cond_sort_book $knit_lc_location_id_cond
		group by a.production_date,a.company_id,a.location_id,a.production_type order by a.production_date "; // $location_cond
		$subc_data_result=sql_select($subc_data_sql);
		foreach ($subc_data_result as $value)
		{
			if ($value[csf('production_type')] == 5) // subcon poly entry
			{
				$rev_qty[$value[csf('company_id')]][strtotime($value[csf('production_date')])]['subc_poly_qty'] += $value[csf('subc_production_qnty')];
				$rev_qty2[$value[csf('company_id')]]['subc_poly_qty'] += $value[csf('subc_production_qnty')];
			}
			else if ($value[csf('production_type')] == 2) // subc sewing output entry
			{
				$rev_qty[$value[csf('company_id')]][strtotime($value[csf('production_date')])]['subc_sewing_output_qty'] += $value[csf('subc_production_qnty')];
				$rev_qty2[$value[csf('company_id')]]['subc_sewing_output_qty'] += $value[csf('subc_production_qnty')];
			}
			else if ($value[csf('production_type')] == 1) // subc Cutting Entry
			{
				$rev_qty[$value[csf('company_id')]][strtotime($value[csf('production_date')])]['subc_cutting_qty'] += $value[csf('subc_production_qnty')];
				$rev_qty2[$value[csf('company_id')]]['subc_cutting_qty'] += $value[csf('subc_production_qnty')];
			}
		}
		// echo "<pre>";print_r($rev_qty);

		// ============================= Commercial Reference Closing =================================
		$ref_close_data=sql_select("SELECT a.company_id, a.closing_date, a.reference_type, a.closing_status, a.inv_pur_req_mst_id, a.mrr_system_no, sum(b.po_quantity*c.total_set_qnty) as ref_closing_qty
		from inv_reference_closing a, wo_po_break_down b, wo_po_details_master c where a.inv_pur_req_mst_id=b.id and b.job_no_mst=c.job_no and a.closing_status=1 $company_cond_sort_book $closing_date_cond
		group by a.company_id, a.closing_date, a.reference_type, a.closing_status, a.inv_pur_req_mst_id, a.mrr_system_no order by a.closing_date");
		foreach ($ref_close_data as $value)
		{
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('closing_date')])]['ref_closing_qty'] += $value[csf('ref_closing_qty')];

			$rev_qty2[$value[csf('company_id')]]['ref_closing_qty'] += $value[csf('ref_closing_qty')];
		}

		// =============== Printing Production in-house ===============
		$printing_production_sql = "SELECT a.company_id,a.sys_no, b.production_date, a.location_id, b.qcpass_qty
		FROM subcon_embel_production_mst a, subcon_embel_production_dtls b
	    WHERE
	    a.id =b.mst_id

		and a.entry_form='222'
	    $printing_date_cond $company_cond_sort_book ";
		// echo $printing_production_sql;
		$printing_production_sql_result= sql_select($printing_production_sql);
		$prod_qty=array();
		foreach($printing_production_sql_result as $row)
		{
          $rev_qty[$row[csf('company_id')]][strtotime($row[csf('production_date')])]['printing_production_qty'] += $row[csf('qcpass_qty')];
          $rev_qty2[$row[csf('company_id')]]['printing_production_qty'] += $row[csf('qcpass_qty')];
		}
		// echo"<pre>";
		// print_r($rev_qty);
		// =============== Printing Delivery ===============
		$printing_delivery_sql = "SELECT a.company_id,
		a.delivery_no,
		a.delivery_date,
		a.location_id,
		b.delivery_qty
        FROM subcon_delivery_mst a, subcon_delivery_dtls b
        WHERE     a.id = b.mst_id
		and a.entry_form='254'
		$printing_delivery_date_cond $company_cond_sort_book";
		// echo $printing_delivery_sql;
		$printing_delivery_sql_result= sql_select($printing_delivery_sql);
		foreach($printing_delivery_sql_result as $row)
		{
          $rev_qty[$row[csf('company_id')]][strtotime($row[csf('delivery_date')])]['printing_delivery_qty'] += $row[csf('delivery_qty')];
          $rev_qty2[$row[csf('company_id')]]['printing_delivery_qty'] += $row[csf('delivery_qty')];
		}
		// 	echo"<pre>";
		// print_r($rev_qty);

		// =============== Embroidery Production inhouse ===============
		$embroidery_production_sql = "SELECT a.company_id,a.sys_no, b.production_date, a.location_id, b.qcpass_qty
		FROM subcon_embel_production_mst a, subcon_embel_production_dtls b
	    WHERE
	    a.id =b.mst_id
		and a.entry_form='315'
	    $printing_date_cond $company_cond_sort_book ";
		$embroidery_production_sql_result= sql_select($embroidery_production_sql);
		foreach($embroidery_production_sql_result as $row)
		{
          $rev_qty[$row[csf('company_id')]][strtotime($row[csf('production_date')])]['embroidery_production_qty'] += $row[csf('qcpass_qty')];
          $rev_qty2[$row[csf('company_id')]]['embroidery_production_qty'] += $row[csf('qcpass_qty')];
		}
		// =============== Embroidery Delivery ===============
		$embroidery_delivery_sql = "SELECT a.company_id,
		a.delivery_no,
		a.delivery_date,
		a.location_id,
		b.delivery_qty
        FROM subcon_delivery_mst a, subcon_delivery_dtls b
        WHERE     a.id = b.mst_id
		AND a.entry_form = '325'
		$printing_delivery_date_cond $company_cond_sort_book";
		//  echo $embroidery_delivery_sql;
		$embroidery_delivery_sql_result= sql_select($embroidery_delivery_sql);
		foreach($embroidery_delivery_sql_result as $row)
		{
          $rev_qty[$row[csf('company_id')]][strtotime($row[csf('delivery_date')])]['embroidery_delivery_qty'] += $row[csf('delivery_qty')];
          $rev_qty2[$row[csf('company_id')]]['embroidery_delivery_qty'] += $row[csf('delivery_qty')];
		}
		// =============== Hang Tag ===============
		$hang_tag_sql = "SELECT a.company_id,
		a.production_date,
		a.location,
		a.production_quantity,
		a.production_type
        FROM pro_garments_production_mst a
        WHERE
		a.production_type='15'
		and a.status_active=1
		and a.is_deleted=0
	    $hang_tag_date_cond $company_cond_sort_book";
		// echo $hang_tag_sql;
		$hang_tag_sql_result= sql_select($hang_tag_sql);
		foreach($hang_tag_sql_result as $row)
		{
          $rev_qty[$row[csf('company_id')]][strtotime($row[csf('production_date')])]['hang_tag_qty'] += $row[csf('production_quantity')];
          $rev_qty2[$row[csf('company_id')]]['hang_tag_qty'] += $row[csf('production_quantity')];
		}
		// echo"<pre>";
		// print_r($rev_qty);

		$date_cond4 = " and a.subcon_date between '$start_date' and '$end_date'";
		$location_cond5 = ($location_id == 0) ? "" : " and a.location_id=$location_id";
		$printing_sql="SELECT a.company_id,a.within_group ,a.subcon_date,b.quantity FROM sub_material_mst a, sub_material_dtls b  WHERE a.id=b.mst_id   and a.entry_form=205 and a.within_group in(1,2) and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 $company_cond_sort_book $location_cond5 $date_cond4 ";
		// echo $printing_sql;die;

		$sql_result_print_rev= sql_select($printing_sql);
		//unset($rev_qty);
		foreach($sql_result_print_rev as $row)
		{
          $rev_qty[$row[csf('company_id')]][strtotime($row[csf('subcon_date')])][$row[csf('within_group')]]['print_rcv_qty'] += $row[csf('quantity')];
          $rev_qty2[$row[csf('company_id')]][$row[csf('within_group')]]['print_rcv_qty'] += $row[csf('quantity')];
		}

		$emb_sql="SELECT a.company_id,a.within_group,a.subcon_date,b.quantity FROM sub_material_mst a, sub_material_dtls b  WHERE a.id=b.mst_id   and a.entry_form=312 and a.within_group in(1,2) and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 $company_cond_sort_book $location_cond4 $date_cond4 ";
		// echo $emb_sql;die;

		$sql_result_print_rev= sql_select($emb_sql);
		//unset($rev_qty);
		foreach($sql_result_print_rev as $row)
		{
          $rev_qty[$row[csf('company_id')]][strtotime($row[csf('subcon_date')])][$row[csf('within_group')]]['embo_rcv_qty'] += $row[csf('quantity')];
          $rev_qty2[$row[csf('company_id')]]['embo_rcv_qty'] += $row[csf('quantity')];
		}

		//================================= Finish Fabric Roll Receive ===============================
		$finish_fab_roll_recv_sql="SELECT  a.company_id, a.receive_date,  a.location_id,
		sum(case when a.entry_form=68 and b.transaction_type=1 then b.cons_quantity else 0 end) as fin_roll_rcv_qty,
		sum(case when a.entry_form=126 and b.transaction_type=4 then b.cons_quantity else 0 end) as fin_roll_iss_rtn_qty
		from inv_receive_master a, inv_transaction b
		where a.id=b.mst_id and b.item_category=2 and a.entry_form in(68,126) and b.transaction_type in(1,4) $date_cond_knit $company_cond_sort_book $knit_lc_location_id_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.company_id, a.receive_date,  a.location_id order by a.receive_date";
		$finish_fab_roll_recv_result=sql_select($finish_fab_roll_recv_sql);
		foreach ($finish_fab_roll_recv_result as $value)
		{
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('receive_date')])]['fin_roll_rcv_qty'] += $value[csf('fin_roll_rcv_qty')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('receive_date')])]['fin_roll_iss_rtn_qty'] += $value[csf('fin_roll_iss_rtn_qty')];
			$rev_qty2[$value[csf('company_id')]]['fin_roll_rcv_qty'] += $value[csf('fin_roll_rcv_qty')];
			$rev_qty2[$value[csf('company_id')]]['fin_roll_iss_rtn_qty'] += $value[csf('fin_roll_iss_rtn_qty')];
		}

		// -----------------------finish Fabric  Transfer In & Out---------------
		$fabric_issue_retn_transfer_sql="SELECT b.company_id, b.transaction_date, d.location_id,
		sum(case when  b.transaction_type=4 then b.cons_quantity else 0 end) as issue_rtn_qty,
		sum(case when  b.transaction_type=5 then b.cons_quantity else 0 end) as finish_in_qty,
		sum(case when  b.transaction_type=6 then b.cons_quantity else 0 end) as finish_out_qty
		from inv_item_transfer_mst a, inv_transaction b, lib_store_location d
		where  a.id=b.mst_id and b.store_id=d.id and b.item_category=2 and b.transaction_type in(4,5,6) and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_cond_transf $location_id_cond_y $date_cond_tran and a.entry_form=14
		group by b.company_id, b.transaction_date, d.location_id order by b.transaction_date";
		//echo $fabric_issue_retn_transfer_sql;die;
		$fabric_issue_retn_transfer_sql_result=sql_select($fabric_issue_retn_transfer_sql);
		foreach ($fabric_issue_retn_transfer_sql_result as $value)
	 	{

			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('transaction_date')])]['finish_in_qty'] += $value[csf('finish_in_qty')];
			$rev_qty[$value[csf('company_id')]][strtotime($value[csf('transaction_date')])]['finish_out_qty'] += $value[csf('finish_out_qty')];
			$rev_qty2[$value[csf('company_id')]]['finish_in_qty'] += $value[csf('finish_in_qty')];
			$rev_qty2[$value[csf('company_id')]]['finish_out_qty'] += $value[csf('finish_out_qty')];
		}





		ob_start();
		?>



	    <fieldset style="width: 8800px; margin:20px auto;">
	        <table cellpadding="0" cellspacing="0" style="text-align: center; width: 100%">
	            <tr class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="10"><strong style="font-size:25px"><?
					$com_name = return_field_value("company_name", "lib_company", "id=" . $cbo_company_name, "company_name");
					echo $com_name; ?></strong></td>
	            </tr>

	            <tr class="form_caption" style="border:none;">
	                <td align="center" width="100%" colspan="10">
	                    <strong style="font-size:16px">Production Summary:
							<?
							if ($start_date != '') echo change_date_format($start_date) . ' To ' . change_date_format($end_date); else echo '';
							?>
	                    </strong>
	                </td>
	            </tr>

	            <tr>
	                <td colspan="10" width="100%"style="text-align: center; font-size:16px">Date of Print:&nbsp&nbsp<? echo date('d-m-Y'); ?></td>
	            </tr>

	            <tr>
	                <td colspan="10" width="100%" style="text-align: center; font-size:16px;">Time of Print:&nbsp&nbsp<? echo date('h:i A'); ?></td>
	            </tr>
	        </table>
	        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
	            <thead>
					<tr>
						<th colspan="7">Order Information[Pcs]</th>
						<th colspan="2">Booking Information[KG]</th>
						<th colspan="2">Sample[PCS]</th>
						<th colspan="4">Yarn[KG]</th>
						<th colspan="2">Knitting[KG]</th>
						<th colspan="7">Greige Fabric Store[KG]</th>
						<th colspan="5">Dyeing & Dyeing Finishing[KG]</th>
						<th colspan="5">Finish Fabric Store[KG]</th>
						<th colspan="2">AOP [KG]</th>
						<th colspan="12">Cutting[PCS]</th>
						<th colspan="5">Print[PCS]</th>
						<th colspan="5">Embroidery[PCS]</th>
						<th colspan="7">Sewing[PCS]</th>
						<th colspan="5">GMT Finishing[PCS]</th>
						<th colspan="4">Shipment[PCS]</th>



					</tr>
		            <tr>
		                <th width="30"><p>SL</p></th>
		                <th width="120"><p>Production Date</p></th>
		                <th width="120"><p>Projection Order[PCS]</p></th>
		                <th width="120"><p>Confirm Order[PCS]</p></th>
		                <th width="120"><p>Cancel Order[PCS]</p></th>
						<th width="120"><p>InActive Order[PCS]</p></th>
		                <th width="120"><p>Reference Close[Pcs]</p></th>

						<th width="120"><p>Main Fabric Booking Qty[KG] </p></th>
						<th width="120"><p>Short/EFR Booking[KG]</p></th>

						<th width="120"><p>Sample Production[PCS]</p></th>
						<th width="120"><p>Sample Delivery[PCS]</p></th>

		                <th width="120"><p>Grey Yarn Received</p></th>
		                <th width="120"><p>Grey Yarn Issued</p></th>
		                <th width="120"><p>Dyed Yarn Received</p></th>
		                <th width="120"><p>Dyed Yarn Issued</p></th>

						<th width="120"><p>Knitting[Inbound]</p></th>
						<th width="120"><p>Knitting[Out-bound]</p></th>


		                <th width="120"><p>Greige Fabric Received</p></th>
		                <th width="120"><p>Greige Fabric issued</p></th>
		                <th width="120"><p>Greige Fabric Transfer Rcv</p></th>
		                <th width="120"><p>Greige Fabric Transfer Issued</p></th>
						<th width="120"><p>Batch Greige Fabric Receive</p></th>
		                <th width="120"><p>Batch</p></th>
		                <th width="120"><p>Re-process Batch</p></th>

						<th width="120"><p>Trims Dyeing Inbound and Outbound</p></th>
		                <th width="120"><p>Dyeing [Inbound]</p></th>
		                <th width="120"><p>Dyeing [Outbound]</p></th>
		                <th width="120"><p>Dyeing Finishing [Inbound]</p></th>
						<th width="120"><p>Dyeing Finishing [Out-bound]</p></th>

		                <th width="120"><p>Finish Fabric Receive</p></th>
		                <th width="120"><p>Finish Fabric Issue</p></th>
						<th width="120"><p>Finish Fabric Issue Return</p></th>
		                <th width="120"><p>Finish Fabric Trasfer Recv</p></th>
		                <th width="120"><p>Finish Fabric Trasfer Issue</p></th>

						<th width="120"><p>AOP Issue</p></th>
		                <th width="120"><p>AOP Receive</p></th>

		                <th width="120"><p>Cutting Fabric Receive[KG]</p></th>
						<th width="120"><p>Cutting Lay</p></th>
		                <th width="120"><p>Cutting[Inbound]</p></th>
						<th width="120"><p>Cutting[Out-bound]</p></th>
						<th width="120"><p>Cutting Send To Print[Inhouse]</p></th>
						<th width="120"><p>Cutting Send To Print [Out-bound]</p></th>
						<th width="120"><p>Total Print Send</p></th>
						<th width="120"><p>Cutting Send to Embroidery[Inhouse]</p></th>
		                <th width="120"><p>Cutting Send to Embroidery[Out-bound]</p></th>
						<th width="120"><p>EMB Total Send</p></th>
						<th width="120"><p>Cutting Rcv From Print</p></th>
						<th width="120"><p>Cutting Rcv from Embroidery</p></th>

						<th width="120"><p>Print[Inhouse Rcv]</p></th>
		                <th width="120"><p>Print[Out-bound Rcv]</p></th>
		                <th width="120"><p>Print Total Rcv</p></th>
						<th width="120"><p>Print Production</p></th>
		                <th width="120"><p>Print Delivery to Cutting</p></th>


						<th width="120"><p>EMB[Inhouse Rcv]</p></th>
		                <th width="120"><p>EMB[Out-bound Rcv]</p></th>
		                <th width="120"><p>EMB Total Rcv</p></th>
		                <th width="120"><p>EMB  Production</p></th>
		                <th width="120"><p>EMB Delivery to Cutting </p></th>


						<th width="120"><p>Total Man Power</p></th>
						<th width="120"><p>No of Operator</p></th>
						<th width="120"><p>No of Helper</p></th>
						<th width="120"><p>Sewing Input</p></th>
						<th width="120"><p>Sewing Output[Inbound]</p></th>
						<th width="120"><p>Sewing Output[Out-bound]</p></th>
						<th width="120"><p>Sewing Line Efficiency %</p></th>

						<th width="120"><p>Iron</p></th>
						<th width="120"><p>Hangtag</p></th>
						<th width="120"><p>Poly</p></th>
						<th width="120"><p>Packing And Finishing</p></th>
						<th width="120"><p>Inspection</p></th>


						<th width="120"><p>Shipment Qty</p></th>
						<th width="120"><p>Ship. Gross Value $</p></th>
						<th width="120"><p>Ship. Net Value $</p></th>
		                <th width="120"><p>Ship. Retn. Qty</p></th>
		            </tr>
	            </thead>
				 <tbody>
				 <?php
				$date_arr = array();
				$date_fri_arr = array();
				$month_year_check_arr = array();
				// echo"<pre>";
				// print_r($rev_qty);
				foreach ($rev_qty as $company => $result_row)
				{
					ksort($result_row);
					$i = 1;
					$sub_total_projec_qty=0;$sub_total_confirm_qty=0;$sub_total_cancel_order_qty=0;$sub_total_ref_closing_qty=0;$sub_total_sort_grey_qty=0;$sub_total_sample_prod_qty=0;$sub_total_sample_delivery_qty=0;$sub_total_grey_recv=0;$sub_total_grey_iss=0;$sub_total_dyed_recv=0;$sub_total_dyed_iss=0;$sub_total_knitting_qty_in=0;$sub_total_knitting_qty_out=0;$sub_total_recv_qty=0;$sub_total_issue_qty=0;$sub_total_trans_in_qty=0;$sub_total_trans_out_qty=0;$sub_total_recv_batch_qty=0;$sub_total_batch_qty=0;$sub_total_re_process_batch_qty=0;$sub_total_trims_dyeing_qty=0;$sub_total_dyeing_qty_in=0;//22
					$sub_total_dye_fini_in_qty=0;$sub_total_dye_fin_qty_out=0;$sub_total_fin_roll_rcv_qty=0;$sub_total_fin_roll_issue_qty=0;//27,28
					$sub_total_aop_issue_qty=0;$sub_total_aop_recv_qty=0;$sub_total_cutting_fab_recv=0;

					$sub_total_cutting_inhouse_qty=0;$sub_total_print_issue_qty=0;$sub_total_print_rcv_qty=0;$sub_total_emb_issue_qty=0;$sub_total_emb_rcv_qty=0;$sub_total_special_issue_qty=0;$sub_total_special_rcv_qty=0;$sub_total_dyeing_issue_qty=0;$sub_total_dyeing_rcv_qty=0;$sub_total_wash_issue_qty=0;$sub_total_wash_rcv_qty=0;$sub_total_man_power=0;$sub_total_operator=0;$sub_total_helper=0;$sub_total_sewing_inbound_qty=0;$sub_total_sewing_outbound_qty=0;$sub_total_iron_qty=0;$sub_total_finish_qty=0;$sub_total_ins_qty=0;$sub_total_ship_qty=0;$sub_total_ship_value=0;$sub_total_ship_rtn_qty=0;
					$date_count = count($locationValue);

					$month_total_projec_qty=0;$month_total_confirm_qty=0;$month_total_cancel_order_qty=0;$month_total_ref_closing_qty=0;$month_total_sort_grey_qty=0;$month_total_sample_prod_qty=0;$month_total_sample_delivery_qty=0;$month_total_grey_recv=0;$month_total_grey_iss=0;$month_total_dyed_recv=0;$month_total_dyed_iss=0;$month_total_knitting_qty_in=0;$month_total_knitting_qty_out=0;$month_total_recv_qty=0;$month_total_issue_qty=0;$month_total_trans_in_qty=0;$month_total_trans_out_qty=0;$month_total_recv_batch_qty=0;$month_total_batch_qty=0;$month_total_re_process_batch_qty=0;$month_total_trims_dyeing_qty=0;$month_total_dyeing_qty_in=0;//22
					$month_total_dye_fini_in_qty=0;$month_total_dye_fin_qty_out=0;$month_total_fin_roll_rcv_qty=0;$month_total_fin_roll_issue_qty=0;//27,28
					$month_total_aop_issue_qty=0;$month_total_aop_recv_qty=0;$month_total_cutting_fab_recv=0;$month_total_cutting_inhouse_qty=0;$month_total_print_issue_qty=0;$month_total_print_rcv_qty=0;$month_total_emb_issue_qty=0;$month_total_emb_rcv_qty=0;$month_total_special_issue_qty=0;$month_total_special_rcv_qty=0;$month_total_dyeing_issue_qty=0;$month_total_dyeing_rcv_qty=0;$month_total_wash_issue_qty=0;$month_total_wash_rcv_qty=0;$month_total_man_power=0;$month_total_operator=0;$month_total_helper=0;$month_total_sewing_inbound_qty=0;$month_total_sewing_outbound_qty=0;$month_total_iron_qty=0;$month_total_finish_qty=0;$month_total_ins_qty=0;$month_total_ship_qty=0;$month_total_ship_value=0;$month_total_ship_rtn_qty=0;

					$month_total_finish_fabric_issue_return_qty = 0;
					$month_total_print_inhound_qty =0;
					$month_total_print_outhound_qty = 0;
					$month_total_print_delivery_to_Cutting_qty = 0;
					$month_total_embroidery_inhound_qty = 0;
					$month_total_embroidery_outhound_qty = 0;
					$month_total_embroidery_delivery_to_cutting_qty = 0;
					$month_total_hangtag_qty =0;
					$month_total_shipment_net_value_qty=0;

					foreach ($result_row as $date => $qtyValue)
					{
						if (!in_array($date, $date_arr))
						{
							$date_arr[] = $date;
						}
						if ($i % 2 == 1) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

						// ----------------------- Month Total start a ----------------------
						if (!in_array(date('F, Y',$date), $month_year_check_arr2))
						{
							if ($i != 1)
							{
								?>

								<?php
								$month_total_projec_qty=0;$month_total_confirm_qty=0;$month_total_cancel_order_qty=0;$month_total_ref_closing_qty=0;$month_total_sort_grey_qty=0;$month_total_sample_prod_qty=0;$month_total_sample_delivery_qty=0;$month_total_grey_recv=0;$month_total_grey_iss=0;$month_total_dyed_recv=0;$month_total_dyed_iss=0;$month_total_knitting_qty_in=0;$month_total_knitting_qty_out=0;$month_total_recv_qty=0;$month_total_issue_qty=0;$month_total_trans_in_qty=0;$month_total_trans_out_qty=0;$month_total_recv_batch_qty=0;$month_total_batch_qty=0;$month_total_re_process_batch_qty=0;$month_total_trims_dyeing_qty=0;$month_total_dyeing_qty_in=0;//22
								$month_total_dye_fini_in_qty=0;$month_total_dye_fin_qty_out=0;$month_total_fin_roll_rcv_qty=0;$month_total_fin_roll_issue_qty=0;//27,28
								$month_total_aop_issue_qty=0;$month_total_aop_recv_qty=0;$month_total_cutting_fab_recv=0;$month_total_cutting_inhouse_qty=0;$month_total_print_issue_qty=0;$month_total_print_rcv_qty=0;$month_total_emb_issue_qty=0;$month_total_emb_rcv_qty=0;$month_total_special_issue_qty=0;$month_total_special_rcv_qty=0;$month_total_dyeing_issue_qty=0;$month_total_dyeing_rcv_qty=0;$month_total_wash_issue_qty=0;$month_total_wash_rcv_qty=0;$month_total_man_power=0;$month_total_operator=0;$month_total_helper=0;$month_total_sewing_inbound_qty=0;$month_total_sewing_outbound_qty=0;$month_total_iron_qty=0;$month_total_finish_qty=0;$month_total_ins_qty=0;$month_total_ship_qty=0;$month_total_ship_value=0;$month_total_ship_rtn_qty=0;

								$month_total_finish_fabric_issue_return_qty = 0;
								$month_total_print_inhound_qty =0;
								$month_total_print_outhound_qty = 0;
								$month_total_print_delivery_to_Cutting_qty = 0;
								$month_total_embroidery_inhound_qty = 0;
								$month_total_embroidery_outhound_qty = 0;
								$month_total_embroidery_delivery_to_cutting_qty = 0;
								$month_total_hangtag_qty =0;
								$month_total_shipment_net_value_qty=0;

							}
							$month_year_check_arr2[] = date('F, Y',$date);
						}
						// ----------------------- Month Total End a ------------------------

						// ===========Month, Year
						if (!in_array(date('F, Y',$date), $month_year_check_arr))
						{
							?>
                            <tr>
                            	<th style="background: #C2DCFF;"></th>
                                <th colspan="71" style="background: #C2DCFF; text-align: left; padding-left: 15px;">
									<?php echo date('F, Y',$date);?>
                                </th>
                            </tr>
							<?php
							$month_year_check_arr[] = date('F, Y',$date);
						}

						$knitting_qty_in=$qtyValue['knitting_qty_in']+$qtyValue['subc_knitting_qty_in'];
						$knitting_qty_out=$qtyValue['knitting_qty_out']+$qtyValue['subc_knitting_qty_out'];

						$recv_qty=$qtyValue['recv_qty_in']+$qtyValue['recv_qty_out']+$qtyValue['issue_rtn_qty'];

						$batch_qty=$qtyValue['batch_qty']+$qtyValue['subc_batch_qty'];
						$re_process_batch_qty=$qtyValue['re_process_batch_qty']+$qtyValue['subc_re_process_batch_qty'];

						$dyeing_in=$qtyValue['dyeing']+$qtyValue['subc_dyeing'];
						$dye_fini_in_qty=$qtyValue['dye_fin_qty_in']+$qtyValue['fin_product_qnty'];
						$poly_qty=$qtyValue['inhouse_poly_qty']+$qtyValue['outbond_poly_qty']+$qtyValue['subc_poly_qty'];

						$sewing_inbound_qty=$qtyValue['sewing_inhouse_qty']+$qtyValue['subc_sewing_output_qty'];
						$cutting_inhouse_qty=$qtyValue['cutting_inhouse']+$qtyValue['subc_cutting_qty'];

						$fin_roll_rcv_qty=$qtyValue['fin_roll_rcv_qty']+$qtyValue['fin_roll_iss_rtn_qty'];

						//  for efficiency
						$prod_min = ($sewing_inbound_qty+$qtyValue['sewing_outbound_qty'])*$qtyValue['smv'];
						$effi_min = $qtyValue['man_power']*$qtyValue['working_hour']*60;
						$sewing_effi = ($effi_min>0) ? ($prod_min / $effi_min)*100 : 0;

						$cutting_fab_recv = $qtyValue['fin_roll_issue_qty']-$qtyValue['fin_roll_issue_rtn_qty'];
						$shipment_net_value = $shipment_net_val_arr[$company][$date];

						$printing_rcv= $qtyValue[1]['print_rcv_qty']+ $qtyValue[2]['print_rcv_qty'];

						// $printing_production= $qtyValue[1]['printing_production_qty']+ $qtyValue[2]['printing_production_qty'];
						$printing_production= $qtyValue['printing_production_qty'];


						$printing_delivery= $qtyValue['printing_delivery_qty'];
						$embo_rcv= $qtyValue[1]['embo_rcv_qty']+ $qtyValue[2]['embo_rcv_qty'];
						// $emb_delivery= $qtyValue[1]['embroidery_delivery_qty']+ $qtyValue[2]['embroidery_delivery_qty'];
						$emb_delivery= $qtyValue['embroidery_delivery_qty'];


						?>
                        <tr bgcolor="<? $timestamp = $date;$day_name= date("l", $timestamp );if($day_name=="Friday"){echo "crimson";} else{echo $bgcolor; }?>" style="<?if($day_name=="Friday"){echo 'color:white;';}?>">
                            <td align="center"><?=$i; ?></td>
                            <td align="center"><?=date('d-m-Y',$date);?></td>
                            <td align="right"><?=number_format($qtyValue['projec_qty_pcs'],2); ?></td>
							<td align="right"><?=number_format($qtyValue['confirm_qty_pcs'],2); ?></td>
							<td align="right"><?=number_format($qtyValue['cancel_order_qty'],2); ?></td>
							<td align="right"><p><?=number_format($qtyValue['inactive_qty_pcs'],2);?></p></td>
							<td align="right"><p><?=number_format($qtyValue['ref_closing_qty'],2); ?></p></td>

							<td align="right"><p><?=number_format($qtyValue['main_grey_qty'],2); ?></p></td>
							<td align="right"><p><?=number_format($qtyValue['sort_grey_qty'],2); ?></p></td>

							<td align="right"><?=number_format($qtyValue['sample_prod_qty'],2); ?></td>
                            <td align="right"><?=number_format($qtyValue['sample_delivery_qty'],2); ?></td>

							<td align="right"><?=number_format($qtyValue['grey_recv_total'],2); ?></td>
                            <td align="right"><?=number_format($qtyValue['grey_iss_total'],2); ?></td>
                            <td align="right"><?=number_format($qtyValue['dyed_recv_total'],2); ?></td>
                            <td align="right"><?=number_format($qtyValue['dyed_iss_total'],2); ?></td>

							<td align="right"><?=number_format($knitting_qty_in,2); ?></td>
                            <td align="right"><?=number_format($knitting_qty_out,2); ?></td>

							<td align="right"><?=number_format($recv_qty,2); ?></td>
                            <td align="right"><?=number_format($qtyValue['issue_qty'],2); ?></td>
                            <td align="right"><?=number_format($qtyValue['trans_in_qty'],2); ?></td>
                            <td align="right"><?=number_format($qtyValue['trans_out_qty'],2); ?></td>

							<td align="right"><?=number_format($qtyValue['recv_batch_qty'],2); ?></td>
                            <td align="right"><?=number_format($batch_qty,2); ?></td>
                            <td align="right"><?=number_format($re_process_batch_qty,2); ?></td>

							<td align="right"><?=number_format($qtyValue['trims_dyeing_qty'],2); ?></td>
                            <td align="right"><?=number_format($dyeing_in,2); ?></td>
                            <td align="right"></td>
                            <td align="right"><?=number_format($dye_fini_in_qty,2); ?></td>
                            <td align="right"><?=number_format($qtyValue['dye_fin_qty_out'],2); ?></td>

							<td align="right"><?=number_format($fin_roll_rcv_qty,2); ?></td>
                            <td align="right"><?=number_format($qtyValue['fin_roll_issue_qty'],2); ?></td>
                            <td align="right"> <?=number_format($qtyValue['fin_roll_issue_rtn_qty'],2); ?></td>

							<td align="right"><p><?=number_format($qtyValue['finish_in_qty'],2); ?></p></td>
							<td align="right"><p><?=number_format($qtyValue['finish_out_qty'],2); ?></p></td>

                            <td align="right"><?=number_format($qtyValue['aop_issue_qty'],2); ?></td>
                            <td align="right"><?=number_format($qtyValue['aop_recv_qty'],2); ?></td>
                            <td align="right"><?=number_format($cutting_fab_recv,2); ?></td>

							<td align="right"><p><?=number_format($qtyValue['cutting_lay'],2);?></p></td>

							<td align="right"><?=number_format($cutting_inhouse_qty,2); ?></td>
                            <td align="right"><? //echo '33 CUTTING [Out-bound]'; ?></td>

							<td align="right"><?=number_format($qtyValue[1]['print_issue'],2);?></td>
                            <td align="right"><?=number_format($qtyValue[3]['print_issue'],2);?></td>
                            <td align="right"><?=number_format(($qtyValue[1]['print_issue']+$qtyValue[3]['print_issue']),2);?></td>



                            <td align="right"><?=number_format($qtyValue[1]['emb_issue'],2); ?></td>
                            <td align="right"><?=number_format($qtyValue[3]['emb_issue'],2); ?></td>
                            <td align="right"><?=number_format(($qtyValue[1]['emb_issue']+$qtyValue[3]['emb_issue']),2); ?></td>

							<td align="right"><?=number_format(($qtyValue[1]['print_rcv']+$qtyValue[3]['print_rcv']),2); ?></td>
							<td align="right"><?=number_format(($qtyValue[1]['emb_rcv']+$qtyValue[3]['emb_rcv']),2); ?></td>

							<td align="right"><?=number_format($qtyValue[1]['print_rcv_qty'],2); ?></td>
							<td align="right"><?=number_format($qtyValue[2]['print_rcv_qty'],2); ?></td>
							<td align="right"><?=number_format($printing_rcv,2); ?></td>
							<td align="right"><?=number_format($printing_production,2); ?></td>
							<td align="right"><?=number_format($printing_delivery,2); ?></td>
							<td align="right"><?=number_format($qtyValue[1]['embo_rcv_qty'],2); ?></td>
							<td align="right"><?=number_format($qtyValue[2]['embo_rcv_qty'],2); ?></td>
							<td align="right"><?=number_format($embo_rcv,2); ?></td>
							<td align="right"><p><?=number_format($qtyValue['embroidery_production_qty'],2);?></p></td>
							<td align="right"><p><?=number_format($emb_delivery,2);?></p></td>
							<td align="right"><p><?=number_format($qtyValue['man_power'],2);?></p></td>
							<td align="right"><p><?=number_format($qtyValue['operator'], 2); ?></p></td>
							<td align="right"><p><?=number_format($qtyValue['helper'], 2); ?></p></td>
							<td align="right"><p><?=number_format($qtyValue['sewing_in'], 2); ?></p></td>
							<td align="right"><?=number_format($sewing_inbound_qty,2); ?></td>
                            <td align="right"><?=number_format($qtyValue['sewing_outbound_qty'],2); ?></td>
                            <td align="right" title="(sewing out*smv)/(manpower*working hour*60)*100"><?=number_format($sewing_effi_array[$company][$date],2); ?></td>
                            <td align="right"><?=number_format($qtyValue['iron'],2); ?></td>
                            <td align="right"> <?=number_format($hangtag=$qtyValue['hang_tag_qty'],2); ?></td>
                            <td align="right"><?=number_format($poly_qty,2); ?></td>
                            <td align="right"><?=number_format($qtyValue['finish'],2); ?></td>
                            <td align="right"><?=number_format($qtyValue['ins_qty'],2); ?></td>
                            <td align="right"><?=number_format($qtyValue['ship_qty'],2); ?></td>
                            <td align="right"><?=number_format($qtyValue['ship_value'],2); ?></td>
                            <td align="right"><?=number_format($shipment_net_value,2); ?></td>
                            <td align="right"><?=number_format($qtyValue['ship_rtn_qty'],2); ?></td>

                        </tr>
						<?
						$i++;

						$month_total_projec_qty += $qtyValue['projec_qty_pcs'];
						$month_total_confirm_qty += $qtyValue['confirm_qty_pcs'];
						$month_total_cancel_order_qty += $qtyValue['cancel_order_qty'];
						$month_total_inactive_qty += $qtyValue['inactive_qty_pcs'];


						$month_total_ref_closing_qty += $qtyValue['ref_closing_qty'];
						$month_total_main_grey_qty += $qtyValue['main_grey_qty'];

						$month_total_sort_grey_qty += $qtyValue['sort_grey_qty'];
						$month_total_sample_prod_qty += $qtyValue['sample_prod_qty'];
						$month_total_sample_delivery_qty += $qtyValue['sample_delivery_qty'];
						$month_total_grey_recv += $qtyValue['grey_recv_total'];
						$month_total_grey_iss += $qtyValue['grey_iss_total'];
						$month_total_dyed_recv += $qtyValue['dyed_recv_total'];
						$month_total_dyed_iss += $qtyValue['dyed_iss_total'];
						$month_total_knitting_qty_in += $knitting_qty_in;
						$month_total_knitting_qty_out += $knitting_qty_out;
						$month_total_recv_qty += $recv_qty;
						$month_total_issue_qty += $qtyValue['issue_qty'];
						$month_total_trans_in_qty += $qtyValue['trans_in_qty'];
						$month_total_trans_out_qty += $qtyValue['trans_out_qty'];
						$month_total_recv_batch_qty += $qtyValue['recv_batch_qty'];
						$month_total_batch_qty += $batch_qty;
						$month_total_re_process_batch_qty += $re_process_batch_qty;
						$month_total_trims_dyeing_qty += $qtyValue['trims_dyeing_qty'];
						$month_total_dyeing_qty_in += $dyeing_in;
						//22 Dyeing [Outbound]
						$month_total_dye_fini_in_qty += $dye_fini_in_qty;
						$month_total_dye_fin_qty_out += $qtyValue['dye_fin_qty_out'];
						$month_total_fin_roll_rcv_qty += $fin_roll_rcv_qty;
						$month_total_fin_roll_issue_qty += $qtyValue['fin_roll_issue_qty'];
						$month_total_finish_fabric_issue_return_qty += $finish_fabric_issue_return;
						//27
						//28
						$month_total_aop_issue_qty += $qtyValue['aop_issue_qty'];
						$month_total_aop_recv_qty += $qtyValue['aop_recv_qty'];
						$month_total_cutting_fab_recv += $qtyValue['cutting_fab_recv'];
						$month_total_cutting_inhouse_qty += $cutting_inhouse_qty;
						$month_total_print_issue_in_qty += $qtyValue[1]['print_issue'];
						 $month_total_print_issue_out_qty += $qtyValue[3]['print_issue'];
						$month_total_print_issue_qty += $qtyValue[1]['print_issue']+$qtyValue[3]['print_issue'];

						$month_total_print_pro_qty += $printing_production;
						$month_total_print_delv_qty += $printing_delivery;
						$month_total_emb_qty	+=$embo_rcv;
						$month_embroidery_production_qty+=$qtyValue['embroidery_production_qty'];
						$month_total_emb_delivery	+=$emb_delivery;
						$month_total_sewing_efficiency +=$sewing_effi_array[$company][$date];

						$month_total_emb_issue_qty += $qtyValue['emb_issue'];
						$month_total_emb_rcv_qty += $qtyValue['emb_rcv'];

						$month_total_print_inhound_qty += $print_inhound;
						$month_total_print_outhound_qty += $print_outhound;
						$month_total_print_delivery_to_Cutting_qty += $print_delivery_to_Cutting;
						$month_total_embroidery_inhound_qty += $embroidery_inhound;
						$month_total_embroidery_outhound_qty += $embroidery_outhound;
						$month_total_embroidery_delivery_to_cutting_qty += $embroidery_delivery_to_cutting;

						$month_total_special_issue_qty += $qtyValue['special_issue'];
						$month_total_special_rcv_qty += $qtyValue['special_rcv'];
						$month_total_dyeing_issue_qty += $qtyValue['dyeing_issue'];
						$month_total_dyeing_rcv_qty += $qtyValue['dyeing_rcv'];
						$month_total_wash_issue_qty += $qtyValue['wash_issue'];
						$month_total_wash_rcv_qty += $qtyValue['wash_rcv'];
						$month_total_man_power += $qtyValue['man_power'];
						$month_total_operator += $qtyValue['operator'];
						$month_total_helper += $qtyValue['helper'];
						$month_total_sewing_inbound_qty += $sewing_inbound_qty;
						$month_total_sewing_outbound_qty += $qtyValue['sewing_outbound_qty'];
						$month_total_iron_qty += $qtyValue['iron'];
						$month_total_hangtag_qty += $hangtag;
						$month_total_poly_qty += $poly_qty;
						$month_total_finish_qty += $qtyValue['finish'];
						$month_total_ins_qty += $qtyValue['ins_qty'];
						$month_total_ship_qty += $qtyValue['ship_qty'];
						$month_total_ship_value += $qtyValue['ship_value'];
						$month_total_shipment_net_value_qty += $shipment_net_value;
						$month_total_ship_rtn_qty += $qtyValue['ship_rtn_qty'];

						$month_total_cutting_lay+=$qtyValue['cutting_lay'];
						$month_total_cutting_rcv_print+=($qtyValue[1]['print_rcv']+$qtyValue[3]['print_rcv']);
						$month_total_cutting_rcv_emb+=($qtyValue[1]['emb_rcv']+$qtyValue[3]['emb_rcv']);
						$month_total_print_rcv_inhound+=$qtyValue[1]['print_rcv_qty'];
						$month_total_print_rcv_outbound+=$qtyValue[1]['print_rcv_qty'];
						//$month_total_print_rcv_qty+=($month_total_print_rcv_inhound+$month_total_print_rcv_outbound);
						$month_total_sewing_in += $qtyValue['sewing_in'];
						$month_total_cutting_send_to_print_in+=$qtyValue[1]['print_issue'];
						$month_total_cutting_send_to_print_out+=$qtyValue[3]['print_issue'];
						$month_total_finish_in_qty += $qtyValue['finish_in_qty'];
						$month_total_finsh_out_qty += $qtyValue['finish_out_qty'];






						// ==============



						$total_projec_qty += $qtyValue['projec_qty_pcs'];
						$total_confirm_qty += $qtyValue['confirm_qty_pcs'];
						$total_cancel_order_qty += $qtyValue['cancel_order_qty'];
						$total_inactive_qty += $qtyValue['inactive_qty_pcs'];


						$total_ref_closing_qty += $qtyValue['ref_closing_qty'];
						$total_main_grey_qty += $qtyValue['main_grey_qty'];

						$total_sort_grey_qty += $qtyValue['sort_grey_qty'];
						$total_sample_prod_qty += $qtyValue['sample_prod_qty'];
						$total_sample_delivery_qty += $qtyValue['sample_delivery_qty'];
						$total_grey_recv += $qtyValue['grey_recv_total'];
						$total_grey_iss += $qtyValue['grey_iss_total'];
						$total_dyed_recv += $qtyValue['dyed_recv_total'];
						$total_dyed_iss += $qtyValue['dyed_iss_total'];
						$total_knitting_qty_in += $knitting_qty_in;
						$total_knitting_qty_out += $knitting_qty_out;
						$total_recv_qty += $recv_qty;
						$total_issue_qty += $qtyValue['issue_qty'];
						$total_trans_in_qty += $qtyValue['trans_in_qty'];
						$total_trans_out_qty += $qtyValue['trans_out_qty'];
						$total_recv_batch_qty += $qtyValue['recv_batch_qty'];
						$total_batch_qty += $batch_qty;
						$total_re_process_batch_qty += $re_process_batch_qty;
						$total_trims_dyeing_qty += $qtyValue['trims_dyeing_qty'];
						$total_dyeing_qty_in += $dyeing_in;
						//22 Dyeing [Outbound]
						$total_dye_fini_in_qty += $dye_fini_in_qty;
						$total_dye_fin_qty_out += $qtyValue['dye_fin_qty_out'];
						$total_fin_roll_rcv_qty += $fin_roll_rcv_qty;
						$total_fin_roll_issue_qty += $qtyValue['fin_roll_issue_qty'];
						$total_finish_fabric_issue_return_qty += $finish_fabric_issue_return;
						//27
						//28
						$total_aop_issue_qty += $qtyValue['aop_issue_qty'];
						$total_aop_recv_qty += $qtyValue['aop_recv_qty'];
						$total_cutting_fab_recv += $qtyValue['cutting_fab_recv'];
						$total_cutting_inhouse_qty += $cutting_inhouse_qty;
						$total_print_issue_in_qty += $qtyValue[1]['print_issue'];
						$total_print_issue_out_qty += $qtyValue[3]['print_issue'];
						$total_print_issue_qty += $qtyValue[1]['print_issue']+$qtyValue[3]['print_issue'];

						$total_print_pro_qty += $printing_production;
						$total_print_delv_qty += $printing_delivery;
						$total_emb_qty	+=$embo_rcv;
						$embroidery_production_qty+=$qtyValue['embroidery_production_qty'];
						$total_emb_delivery	+=$emb_delivery;
						$total_sewing_efficiency +=$sewing_effi_array[$company][$date];

						$total_emb_issue_qty += $qtyValue['emb_issue'];
						$total_emb_rcv_qty += $qtyValue['emb_rcv'];

						$total_print_inhound_qty += $print_inhound;
						$total_print_outhound_qty += $print_outhound;
						$total_print_delivery_to_Cutting_qty += $print_delivery_to_Cutting;
						$total_embroidery_inhound_qty += $embroidery_inhound;
						$total_embroidery_outhound_qty += $embroidery_outhound;
						$total_embroidery_delivery_to_cutting_qty += $embroidery_delivery_to_cutting;

						$total_special_issue_qty += $qtyValue['special_issue'];
						$total_special_rcv_qty += $qtyValue['special_rcv'];
						$total_dyeing_issue_qty += $qtyValue['dyeing_issue'];
						$total_dyeing_rcv_qty += $qtyValue['dyeing_rcv'];
						$total_wash_issue_qty += $qtyValue['wash_issue'];
						$total_wash_rcv_qty += $qtyValue['wash_rcv'];
						$total_man_power += $qtyValue['man_power'];
						$total_operator += $qtyValue['operator'];
						$total_helper += $qtyValue['helper'];
						$total_sewing_inbound_qty += $sewing_inbound_qty;
						$total_sewing_outbound_qty += $qtyValue['sewing_outbound_qty'];
						$total_iron_qty += $qtyValue['iron'];
						$total_hangtag_qty += $hangtag;
						$total_poly_qty += $poly_qty;
						$total_finish_qty += $qtyValue['finish'];
						$total_ins_qty += $qtyValue['ins_qty'];
						$total_ship_qty += $qtyValue['ship_qty'];
						$total_ship_value += $qtyValue['ship_value'];
						$total_shipment_net_value_qty += $shipment_net_value;
						$total_ship_rtn_qty += $qtyValue['ship_rtn_qty'];

						$total_cutting_lay+=$qtyValue['cutting_lay'];
						$total_cutting_rcv_print+=($qtyValue[1]['print_rcv']+$qtyValue[3]['print_rcv']);
						$total_cutting_rcv_emb+=($qtyValue[1]['emb_rcv']+$qtyValue[3]['emb_rcv']);
						$total_print_rcv_inhound+=$qtyValue[1]['print_rcv_qty'];
						$total_print_rcv_outbound+=$qtyValue[1]['print_rcv_qty'];
						$total_print_rcv_qty+=($total_print_rcv_inhound+$total_print_rcv_outbound);
						$total_sewing_in += $qtyValue['sewing_in'];
						$total_cutting_send_to_print_in+=$qtyValue[1]['print_issue'];
						$total_cutting_send_to_print_out+=$qtyValue[3]['print_issue'];
						$total_finish_in_qty += $qtyValue['finish_in_qty'];
						$total_finsh_out_qty += $qtyValue['finish_out_qty'];






					}
					?>
					<!-- Month Total start-->
                    <tr>
                        <td align="right" colspan="2"><b>Monthly Total</b></td>
                        <td align="right"><b><?=number_format($month_total_projec_qty,2); ?></b></td>
                        <td align="right"><b><?=number_format($month_total_confirm_qty,2); ?></b></td>
                        <td align="right"><b><?=number_format($month_total_cancel_order_qty,2); ?></b></td>
						<td align="right"><p><b><?=number_format($month_total_inactive_qty,2); ?></b></p></td>
						<td align="right"><b><?=number_format($month_total_ref_closing_qty,2); ?></b></td>
						<td align="right"><p><b><?=number_format($month_total_main_grey_qty,2)?></b></p></td>
						<td align="right"><p><b><?=number_format($month_total_sort_grey_qty,2); ?></b></p></td>
						<td align="right"><b><?=number_format($month_total_sample_prod_qty,2); ?></b></td>
                        <td align="right"><b><?=number_format($month_total_sample_delivery_qty,2); ?></b></td>
                        <td align="right"><b><?=number_format($month_total_grey_recv,2); ?></b></td>
                        <td align="right"><b><?=number_format($month_total_grey_iss,2); ?></b></td>
						<td align="right"><b><?=number_format($month_total_dyed_recv,2); ?></b></td>
                        <td align="right"><b><?=number_format($month_total_dyed_iss,2); ?></b></td>
                        <td align="right"><b><?=number_format($month_total_knitting_qty_in, 2); ?></b></td>
                        <td align="right"><b><?=number_format($month_total_knitting_qty_out, 2); ?></b></td>
						<td align="right"><b><?=number_format($month_total_recv_qty,2); ?></b></td>
                        <td align="right"><b><?=number_format($month_total_issue_qty,2); ?></b></td>
						<td align="right"><b><?=number_format($month_total_trans_in_qty,2); ?></b></td>
                        <td align="right"><b><?=number_format($month_total_trans_out_qty,2); ?></b></td>

                        <td align="right"><b><?=number_format($month_total_recv_batch_qty,2); ?></b></td>
                        <td align="right"><b><?=number_format($month_total_batch_qty,2); ?></b></td>
						<td align="right"><b><?=number_format($month_total_re_process_batch_qty, 2); ?></b></td>
						<td align="right"><b><?=number_format($month_total_trims_dyeing_qty,2); ?></b></td>
                        <td align="right"><b><?=number_format($month_total_dyeing_qty_in,2); ?></b></td>
						<td align="right"><b><?php //echo '22 Dyeing [Outbound]'; ?></b></td>
                        <td align="right"><b><?=number_format($month_total_dye_fini_in_qty,2); ?></b></td>
                        <td align="right"><b><?=number_format($month_total_dye_fin_qty_out,2); ?></b></td>
						<td align="right"><b><?=number_format($month_total_fin_roll_rcv_qty,2); ?></b></td>
                        <td align="right"><b><?=number_format($month_total_fin_roll_issue_qty,2); ?></b></td>
                        <td align="right"><b><?=number_format($month_total_finish_fabric_issue_return_qty,2); ?></b></td>
						<td align="right"><p><b><?php echo number_format($month_total_finish_in_qty, 2); ?></b></p></td>
						<td align="right"><p><b><?php echo number_format($month_total_finsh_out_qty, 2); ?></b></p></td>
                        <td align="right"><b><?=number_format($month_total_aop_issue_qty,2); ?></b></td>
                        <td align="right"><b><?=number_format($month_total_aop_recv_qty,2); ?></b></td>
                        <td align="right"><b><?=number_format($month_total_cutting_fab_recv,2); ?></b></td>
						<td align="right"><b><?=number_format($month_total_cutting_lay,2); ?></b></td>
						<td align="right" title="<? echo $qtyValue['cutting_inhouse'].'+'.$qtyValue['subc_cutting_qty']; ?>"><b><?=number_format($month_total_cutting_inhouse_qty,2); ?></b></td>
                        <td align="right"><? //echo '33 CUTTING [Out-bound]'; ?></td>


						<td align="right"><b><?=number_format($month_total_cutting_send_to_print_in, 2); ?></b></td>
                        <td align="right"><b><?=number_format($month_total_cutting_send_to_print_out, 2); ?></b></td>
                        <td align="right"><b><?=number_format(($month_total_cutting_send_to_print_in+$month_total_cutting_send_to_print_out), 2); ?></b></td>
						<td align="right"><b><?=number_format($qtyValue[1]['emb_issue'],2); ?></b></td>
                        <td align="right"><b><?=number_format($qtyValue[3]['emb_issue'],2); ?></b></td>
                        <td align="right"><b><?=number_format(($qtyValue[1]['emb_issue']+$qtyValue[3]['emb_issue']),2); ?></b></td>
						<td align="right"><b><?=number_format($month_total_cutting_rcv_print,2); ?></b></td>
						<td align="right"><b><?=number_format($month_total_cutting_rcv_emb,2); ?></b></td>

                        <td align="right"><b><?=number_format($print_rcv_inhound,2); ?></b></td>
                        <td align="right"><b><?=number_format($print_rcv_outbound,2); ?></b></td>
						<td align="right"><b><?=number_format($month_total_print_rcv_qty,2); ?></b></td>
						<td align="right"><b><?=number_format($month_total_print_pro_qty,2); ?></b></td>
						<td align="right"><b><?=number_format($month_total_print_delv_qty,2); ?></b></td>
						<td align="right"><b><?=number_format($month_total_emb_issue_qty,2); ?></b></td>
						<td align="right"><b><?=number_format($month_total_emb_rcv_qty,2); ?></b></td>
						<td align="right"><b><?=number_format($month_total_emb_qty,2); ?></b></td>
						<td align="right"><b><?=number_format($month_embroidery_production_qty,2); ?></b></td>
						<td align="right"><b><?=number_format($month_total_emb_delivery,2); ?></b></td>
						<td align="right"><b><?=number_format($month_total_man_power,2); ?></b></td>
						<td align="right"><b><?=number_format($month_total_operator,2); ?></b></td>
						<td align="right"><b><?=number_format($month_total_helper,2); ?></b></td>
						<td align="right"><b><?=number_format($month_total_sewing_in,2); ?></b></td>
						<td align="right"><b><?=number_format($month_total_sewing_inbound_qty,2); ?></b></td>
						<td align="right"><b><?=number_format($month_total_sewing_outbound_qty,2); ?></b></td>
						<td align="right"><p><b><?=number_format($month_total_sewing_efficiency,2); ?>%</b></p></td>
						<td align="right"><b><?php echo number_format($month_total_iron_qty, 2); ?></b>
                        </td>
                        <td align="right"><b><?php echo number_format($month_total_hangtag_qty,2); ?></b>
                        </td>
                        <td align="right"><b><?php echo number_format($month_total_poly_qty, 2); ?></b></td>
                        <td align="right"><b><?php echo number_format($month_total_finish_qty, 2); ?></b>
                        </td>
                        <td align="right"><b><? echo number_format($month_total_ins_qty, 2); ?></b></td>
                        <td align="right"><b><? echo number_format($month_total_ship_qty, 2); ?></b></td>
                        <td align="right"><b><? echo number_format($month_total_ship_value, 2); ?></b></td>
                        <td align="right"><b><? echo number_format($month_total_shipment_net_value_qty, 2); ?></b></td>
                        <td align="right"><b><? echo number_format($month_total_ship_rtn_qty, 2); ?></b></td>

                    </tr>
                    <!-- Month total end -->


					<?php
				}
				?>

				</tbody>
				<tfoot>
					<?
					$date_count = count($date_arr);
					$date_count2 = count($date_fri_arr);
					?>
				  <tr>
                        <td align="right" colspan="2"><b>Grand  Total</b></td>
                        <td align="right"><b><?=number_format($total_projec_qty,2); ?></b></td>
                        <td align="right"><b><?=number_format($total_confirm_qty,2); ?></b></td>
                        <td align="right"><b><?=number_format($total_cancel_order_qty,2); ?></b></td>
						<td align="right"><p><b><?=number_format($total_inactive_qty,2); ?></b></p></td>
						<td align="right"><b><?=number_format($total_ref_closing_qty,2); ?></b></td>
						<td align="right"><p><b><?=number_format($total_main_grey_qty,2)?></b></p></td>
						<td align="right"><p><b><?=number_format($total_sort_grey_qty,2); ?></b></p></td>
						<td align="right"><b><?=number_format($total_sample_prod_qty,2); ?></b></td>
                        <td align="right"><b><?=number_format($total_sample_delivery_qty,2); ?></b></td>
                        <td align="right"><b><?=number_format($total_grey_recv,2); ?></b></td>
                        <td align="right"><b><?=number_format($total_grey_iss,2); ?></b></td>
						<td align="right"><b><?=number_format($total_dyed_recv,2); ?></b></td>
                        <td align="right"><b><?=number_format($total_dyed_iss,2); ?></b></td>
                        <td align="right"><b><?=number_format($total_knitting_qty_in, 2); ?></b></td>
                        <td align="right"><b><?=number_format($total_knitting_qty_out, 2); ?></b></td>
						<td align="right"><b><?=number_format($total_recv_qty,2); ?></b></td>
                        <td align="right"><b><?=number_format($total_issue_qty,2); ?></b></td>
						<td align="right"><b><?=number_format($total_trans_in_qty,2); ?></b></td>
                        <td align="right"><b><?=number_format($total_trans_out_qty,2); ?></b></td>

                        <td align="right"><b><?=number_format($total_recv_batch_qty,2); ?></b></td>
                        <td align="right"><b><?=number_format($total_batch_qty,2); ?></b></td>
						<td align="right"><b><?=number_format($total_re_process_batch_qty, 2); ?></b></td>
						<td align="right"><b><?=number_format($total_trims_dyeing_qty,2); ?></b></td>
                        <td align="right"><b><?=number_format($total_dyeing_qty_in,2); ?></b></td>
						<td align="right"><b><?php //echo '22 Dyeing [Outbound]'; ?></b></td>
                        <td align="right"><b><?=number_format($total_dye_fini_in_qty,2); ?></b></td>
                        <td align="right"><b><?=number_format($total_dye_fin_qty_out,2); ?></b></td>
						<td align="right"><b><?=number_format($total_fin_roll_rcv_qty,2); ?></b></td>
                        <td align="right"><b><?=number_format($total_fin_roll_issue_qty,2); ?></b></td>
                        <td align="right"><b><?=number_format($total_finish_fabric_issue_return_qty,2); ?></b></td>
						<td align="right"><p><b><?php echo number_format($total_finish_in_qty, 2); ?></b></p></td>
						<td align="right"><p><b><?php echo number_format($total_finsh_out_qty, 2); ?></b></p></td>
                        <td align="right"><b><?=number_format($total_aop_issue_qty,2); ?></b></td>
                        <td align="right"><b><?=number_format($total_aop_recv_qty,2); ?></b></td>
                        <td align="right"><b><?=number_format($total_cutting_fab_recv,2); ?></b></td>
						<td align="right"><b><?=number_format($total_cutting_lay,2); ?></b></td>
						<td align="right" title="<? echo $qtyValue['cutting_inhouse'].'+'.$qtyValue['subc_cutting_qty']; ?>"><b><?=number_format($total_cutting_inhouse_qty, 2); ?></b></td>
                        <td align="right"><? //echo '33 CUTTING [Out-bound]'; ?></td>


						<td align="right"><b><?=number_format($total_cutting_send_to_print_in, 2); ?></b></td>
                        <td align="right"><b><?=number_format($total_cutting_send_to_print_out, 2); ?></b></td>
                        <td align="right"><b><?=number_format(($total_cutting_send_to_print_in+$total_cutting_send_to_print_out), 2); ?></b></td>
						<td align="right"><b><?=number_format($qtyValue[1]['emb_issue'],2); ?></b></td>
                        <td align="right"><b><?=number_format($qtyValue[3]['emb_issue'],2); ?></b></td>
                        <td align="right"><b><?=number_format(($qtyValue[1]['emb_issue']+$qtyValue[3]['emb_issue']),2); ?></b></td>
						<td align="right"><b><?=number_format($total_cutting_rcv_print,2); ?></b></td>
						<td align="right"><b><?=number_format($total_cutting_rcv_emb,2); ?></b></td>

                        <td align="right"><b><?=number_format($print_rcv_inhound,2); ?></b></td>
                        <td align="right"><b><?=number_format($print_rcv_outbound,2); ?></b></td>
						<td align="right"><b><?=number_format($total_print_rcv_qty,2); ?></b></td>
						<td align="right"><b><?=number_format($total_print_pro_qty,2); ?></b></td>
						<td align="right"><b><?=number_format($total_print_delv_qty,2); ?></b></td>
						<td align="right"><b><?=number_format($total_emb_issue_qty,2); ?></b></td>
						<td align="right"><b><?=number_format($total_emb_rcv_qty,2); ?></b></td>
						<td align="right"><b><?=number_format($total_emb_qty,2); ?></b></td>
						<td align="right"><b><?=number_format($embroidery_production_qty,2); ?></b></td>
						<td align="right"><b><?=number_format($total_emb_delivery,2); ?></b></td>
						<td align="right"><b><?=number_format($total_man_power,2); ?></b></td>
						<td align="right"><b><?=number_format($total_operator,2); ?></b></td>
						<td align="right"><b><?=number_format($total_helper,2); ?></b></td>
						<td align="right"><b><?=number_format($total_sewing_in,2); ?></b></td>
						<td align="right"><b><?=number_format($total_sewing_inbound_qty,2); ?></b></td>
						<td align="right"><b><?=number_format($total_sewing_outbound_qty,2); ?></b></td>
						<td align="right"><p><b><?=number_format($total_sewing_efficiency,2); ?>%</b></p></td>
						<td align="right"><b><?php echo number_format($total_iron_qty, 2); ?></b>
                        </td>
                        <td align="right"><b><?php echo number_format($total_hangtag_qty,2); ?></b>
                        </td>
                        <td align="right"><b><?php echo number_format($total_poly_qty, 2); ?></b></td>
                        <td align="right"><b><?php echo number_format($total_finish_qty, 2); ?></b>
                        </td>
                        <td align="right"><b><? echo number_format($total_ins_qty, 2); ?></b></td>
                        <td align="right"><b><? echo number_format($total_ship_qty, 2); ?></b></td>
                        <td align="right"><b><? echo number_format($total_ship_value, 2); ?></b></td>
                        <td align="right"><b><? echo number_format($total_shipment_net_value_qty, 2); ?></b></td>
                        <td align="right"><b><? echo number_format($total_ship_rtn_qty, 2); ?></b></td>

                 </tr>

				</tfoot>

	        </table>

	    </fieldset>
		<br>
		<fieldset style="width: 8800px; margin:20px auto;">

	        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
	            <thead>
					<tr>
						<th colspan="7">Order Information[Pcs]</th>
						<th colspan="2">Booking Information[KG]</th>
						<th colspan="2">Sample[PCS]</th>
						<th colspan="4">Yarn[KG]</th>
						<th colspan="2">Knitting[KG]</th>
						<th colspan="7">Greige Fabric Store[KG]</th>
						<th colspan="5">Dyeing & Dyeing Finishing[KG]</th>
						<th colspan="5">Finish Fabric Store[KG]</th>
						<th colspan="2">AOP [KG]</th>
						<th colspan="12">Cutting[PCS]</th>
						<th colspan="5">Print[PCS]</th>
						<th colspan="5">Embroidery[PCS]</th>
						<th colspan="7">Sewing[PCS]</th>
						<th colspan="5">GMT Finishing[PCS]</th>
						<th colspan="4">Shipment[PCS]</th>

					</tr>
		            <tr>
		                <th width="30"><p>SL</p></th>
		                <th width="120"><p>Production Date</p></th>
		                <th width="120"><p>Projection Order[PCS]</p></th>
		                <th width="120"><p>Confirm Order[PCS]</p></th>
		                <th width="120"><p>Cancel Order[PCS]</p></th>
						<th width="120"><p>InActive Order[PCS]</p></th>
		                <th width="120"><p>Reference Close[Pcs]</p></th>

						<th width="120"><p>Main Fabric Booking Qty[KG] </p></th>
						<th width="120"><p>Short/EFR Booking[KG]</p></th>

						<th width="120"><p>Sample Production[PCS]</p></th>
						<th width="120"><p>Sample Delivery[PCS]</p></th>

		                <th width="120"><p>Grey Yarn Received</p></th>
		                <th width="120"><p>Grey Yarn Issued</p></th>
		                <th width="120"><p>Dyed Yarn Received</p></th>
		                <th width="120"><p>Dyed Yarn Issued</p></th>

						<th width="120"><p>Knitting[Inbound]</p></th>
						<th width="120"><p>Knitting[Out-bound]</p></th>


		                <th width="120"><p>Greige Fabric Received</p></th>
		                <th width="120"><p>Greige Fabric issued</p></th>
		                <th width="120"><p>Greige Fabric Transfer Rcv</p></th>
		                <th width="120"><p>Greige Fabric Transfer Issued</p></th>
						<th width="120"><p>Batch Greige Fabric Receive</p></th>
		                <th width="120"><p>Batch</p></th>
		                <th width="120"><p>Re-process Batch</p></th>

						<th width="120"><p>Trims Dyeing Inbound and Outbound</p></th>
		                <th width="120"><p>Dyeing [Inbound]</p></th>
		                <th width="120"><p>Dyeing [Outbound]</p></th>
		                <th width="120"><p>Dyeing Finishing [Inbound]</p></th>
						<th width="120"><p>Dyeing Finishing [Out-bound]</p></th>

		                <th width="120"><p>Finish Fabric Receive</p></th>
		                <th width="120"><p>Finish Fabric Issue</p></th>
						<th width="120"><p>Finish Fabric Issue Return</p></th>
		                <th width="120"><p>Finish Fabric Trasfer Recv</p></th>
		                <th width="120"><p>Finish Fabric Trasfer Issue</p></th>

						<th width="120"><p>AOP Issue</p></th>
		                <th width="120"><p>AOP Receive</p></th>

		                <th width="120"><p>Cutting Fabric Receive[KG]</p></th>
						<th width="120"><p>Cutting Lay</p></th>
		                <th width="120"><p>Cutting[Inbound]</p></th>
						<th width="120"><p>Cutting[Out-bound]</p></th>
						<th width="120"><p>Cutting Send To Print[Inhouse]</p></th>
						<th width="120"><p>Cutting Send To Print [Out-bound]</p></th>
						<th width="120"><p>Total Print Send</p></th>
						<th width="120"><p>Cutting Send to Embroidery[Inhouse]</p></th>
		                <th width="120"><p>Cutting Send to Embroidery[Out-bound]</p></th>
						<th width="120"><p>EMB Total Send</p></th>
						<th width="120"><p>Cutting Rcv From Print</p></th>
						<th width="120"><p>Cutting Rcv from Embroidery</p></th>

						<th width="120"><p>Print[Inhouse Rcv]</p></th>
		                <th width="120"><p>Print[Out-bound Rcv]</p></th>
		                <th width="120"><p>Print Total Rcv</p></th>
						<th width="120"><p>Print Production</p></th>
		                <th width="120"><p>Print Delivery to Cutting</p></th>


						<th width="120"><p>EMB[Inhouse Rcv]</p></th>
		                <th width="120"><p>EMB[Out-bound Rcv]</p></th>
		                <th width="120"><p>EMB Total Rcv</p></th>
		                <th width="120"><p>EMB  Production</p></th>
		                <th width="120"><p>EMB Delivery to Cutting </p></th>


						<th width="120"><p>Total Man Power</p></th>
						<th width="120"><p>No of Operator</p></th>
						<th width="120"><p>No of Helper</p></th>
						<th width="120"><p>Sewing Input</p></th>
						<th width="120"><p>Sewing Output[Inbound]</p></th>
						<th width="120"><p>Sewing Output[Out-bound]</p></th>
						<th width="120"><p>Sewing Line Efficiency %</p></th>

						<th width="120"><p>Iron</p></th>
						<th width="120"><p>Hangtag</p></th>
						<th width="120"><p>Poly</p></th>
						<th width="120"><p>Pack.And Finis.</p></th>
						<th width="120"><p>Inspection</p></th>


						<th width="120"><p>Shipment Qty</p></th>
						<th width="120"><p>Ship. Gross Value $</p></th>
						<th width="120"><p>Ship. Net Value $</p></th>
		                <th width="120"><p>Ship. Retn. Qty</p></th>
		            </tr>
	            </thead>

				<tbody>
					<?
					$k = 1;
					foreach ($rev_qty2 as $company_id => $row)
					{
						if ($k % 2 == 1) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						$knitting_qty_in2=$row['knitting_qty_in']+$row['subc_knitting_qty_in'];
						$knitting_qty_out2=$row['knitting_qty_out']+$row['subc_knitting_qty_out'];
						$recv_qty2=$row['recv_qty_in']+$row['recv_qty_out']+$row['issue_rtn_qty'];
						$batch_qty2=$row['batch_qty']+$row['subc_batch_qty'];
						$re_process_batch_qty2=$row['re_process_batch_qty']+$row['subc_re_process_batch_qty'];
						$dyeing_in2=$row['dyeing']+$row['subc_dyeing'];
						$dye_fini_in_qty2=$row['dye_fin_qty_in']+$row['fin_product_qnty'];
						$poly_qty2=$row['inhouse_poly_qty']+$row['outbond_poly_qty']+$row['subc_poly_qty'];
						$sewing_inbound_qty2=$row['sewing_inhouse_qty']+$row['subc_sewing_output_qty'];
						$cutting_inhouse_qty2=$row['cutting_inhouse']+$row['subc_cutting_qty'];
						$fin_roll_rcv_qty2=$row['fin_roll_rcv_qty']+$row['fin_roll_iss_rtn_qty'];
						$cutting_rcv_emb =$row['emb_rcv'];
						$emb_issue_in=$row[1]['emb_issue'];
						$emb_issue_out=$row[3]['emb_issue'];
						$cutting_send_to_print_in +=$row[1]['print_issue'];
						$cutting_send_to_print_out +=$row[3]['print_issue'];

						// $sewing_inbound_qty2=$row['sewing_inhouse_qty']+$row['subc_sewing_output_qty'];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td><? echo $k; ?></td>
							<td><? echo $company_arr[$company_id]; ?></td>
							<td align="right"><?=number_format($row['projec_qty_pcs'],2); ?></td>
							<td align="right"><?=number_format($row['confirm_qty_pcs'],2); ?></td>
							<td align="right"><?=number_format($row['cancel_order_qty'],2); ?></td>
							<td align="right"><p><?=number_format($row['inactive_qty_pcs'],2);?></p></td>
							<td align="right"><p><?=number_format($row['ref_closing_qty'],2); ?></p></td>

							<td align="right"><p><?=number_format($row['main_grey_qty'],2); ?></p></td>
							<td align="right"><p><?=number_format($row['sort_grey_qty'],2); ?></p></td>

							<td align="right"><?=number_format($row['sample_prod_qty'],2); ?></td>
							<td align="right"><?=number_format($row['sample_delivery_qty'],2); ?></td>

							<td align="right"><?=number_format($row['grey_recv_total'],2); ?></td>
							<td align="right"><?=number_format($row['grey_iss_total'],2); ?></td>
							<td align="right"><?=number_format($row['dyed_recv_total'],2); ?></td>
							<td align="right"><?=number_format($row['dyed_iss_total'],2); ?></td>

							<td align="right"><?=number_format($knitting_qty_in2,2); ?></td>
							<td align="right"><?=number_format($knitting_qty_out2,2); ?></td>

							<td align="right"><?=number_format($recv_qty2,2); ?></td>
							<td align="right"><?=number_format($row['issue_qty'],2); ?></td>
							<td align="right"><?=number_format($row['trans_in_qty'],2); ?></td>
							<td align="right"><?=number_format($row['trans_out_qty'],2); ?></td>

							<td align="right"><?=number_format($row['recv_batch_qty'],2); ?></td>
							<td align="right"><?=number_format($batch_qty2,2); ?></td>
							<td align="right"><?=number_format($re_process_batch_qty,2); ?></td>

							<td align="right"><?=number_format($row['trims_dyeing_qty'],2); ?></td>
							<td align="right"><?=number_format($dyeing_in,2); ?></td>
							<td align="right"></td>
							<td align="right"><?=number_format($dye_fini_in_qty2,2); ?></td>
							<td align="right"><?=number_format($row['dye_fin_qty_out'],2); ?></td>

							<td align="right"><?=number_format($fin_roll_rcv_qty,2); ?></td>
							<td align="right"><?=number_format($row['fin_roll_issue_qty'],2); ?></td>
							<td align="right"> <?=number_format($row['fin_roll_issue_rtn_qty'],2); ?></td>
							<td align="right"></td>
							<td align="right"></td>

							<td align="right"><?=number_format($row['aop_issue_qty'],2); ?></td>
							<td align="right"><?=number_format($row['aop_recv_qty'],2); ?></td>
							<td align="right"><?=number_format($cutting_fab_recv,2); ?></td>

							<td align="right"><p><?=number_format($row['cutting_lay'],2);?></p></td>

							<td align="right"><?=number_format($cutting_inhouse_qty2,2); ?></td>
							<td align="right"><? //echo '33 CUTTING [Out-bound]'; ?></td>

							<td align="right"><?=number_format($cutting_send_to_print_in,2);?></td>
							<td align="right"><?=number_format($cutting_send_to_print_out,2);?></td>
							<td align="right"><?=number_format(($row[1]['print_issue']+$row[3]['print_issue']),2);?></td>



							<td align="right"><?=number_format($emb_issue_in,2); ?></td>
							<td align="right"><?=number_format($emb_issue_out,2); ?></td>
							<td align="right"><?=number_format(($emb_issue_in+$emb_issue_out),2); ?></td>

							<td align="right"><?=number_format(($row[1]['print_rcv']+$row[3]['print_rcv']),2); ?></td>
							<td align="right"><?=number_format(($row[1]['emb_rcv']+$row[3]['emb_rcv']),2); ?></td>

							<td align="right"><?=number_format($row[1]['print_rcv_qty'],2); ?></td>
							<td align="right"><?=number_format($row[2]['print_rcv_qty'],2); ?></td>
							<td align="right"><?=number_format($printing_rcv,2); ?></td>
							<td align="right"><?=number_format($printing_production,2); ?></td>
							<td align="right"><?=number_format($printing_delivery,2); ?></td>
							<td align="right"><?=number_format($row[1]['embo_rcv_qty'],2); ?></td>
							<td align="right"><?=number_format($row[2]['embo_rcv_qty'],2); ?></td>
							<td align="right"><?=number_format($embo_rcv,2); ?></td>
							<td align="right"><p><?=number_format($row['embroidery_production_qty'],2);?></p></td>
							<td align="right"><p><?=number_format($row['emb_delivery'],2);?></p></td>
							<td align="right"><p><?=number_format($row['man_power'],2);?></p></td>
							<td align="right"><p><?=number_format($row['operator'], 2); ?></p></td>
							<td align="right"><p><?=number_format($row['helper'], 2); ?></p></td>
							<td align="right"><p><?=number_format($row['sewing_in'], 2); ?></p></td>
							<td align="right"><?=number_format($sewing_inbound_qty2,2); ?></td>
							<td align="right"><?=number_format($row['sewing_outbound_qty'],2); ?></td>
							<td align="right" title="(sewing out*smv)/(manpower*working hour*60)*100"><?=number_format($sewing_effi_array[$company][$date],2); ?></td>
							<td align="right"><?=number_format($row['iron'],2); ?></td>
							<td align="right"> <?=number_format($hangtag=$row['hang_tag_qty'],2); ?></td>
							<td align="right"><?=number_format($poly_qty2,2); ?></td>
							<td align="right"><?=number_format($row['finish'],2); ?></td>
							<td align="right"><?=number_format($row['ins_qty'],2); ?></td>
							<td align="right"><?=number_format($row['ship_qty'],2); ?></td>
							<td align="right"><?=number_format($row['ship_value'],2); ?></td>
							<td align="right"><?=number_format($shipment_net_value, 2); ?></td>
							<td align="right"><?=number_format($row['ship_rtn_qty'],2); ?></td>
						</tr>
						<?
						$k++;
						$total_projec_qty_pcs2 += $row['projec_qty_pcs'];
						$total_confirm_qty_pcs2 += $row['confirm_qty_pcs'];
						$total_cancel_order_qty2 += $row['cancel_order_qty'];
						$total_ref_closing_qty2 += $row['ref_closing_qty'];
						$total_sort_grey_qty2 += $row['sort_grey_qty'];
						$total_sample_prod_qty2 += $row['sample_prod_qty'];
						$total_sample_delivery_qty2 += $row['sample_delivery_qty'];
						$total_grey_recv_total2 += $row['grey_recv_total'];
						$total_grey_iss_total2 += $row['grey_iss_total'];
						$total_dyed_recv_total2 += $row['dyed_recv_total'];
						$total_dyed_iss_total2 += $row['dyed_iss_total'];
						$total_knitting_in2 += $knitting_qty_in2;
						$total_knitting_out2 += $knitting_qty_out2;
						$total_recv_qty2 += $recv_qty2;
						$total_issue_qty2 += $row['issue_qty'];
						$total_trans_in_qty2 += $row['trans_in_qty'];
						$total_trans_out_qty2 += $row['trans_out_qty'];
						$total_recv_batch_qty2 += $row['recv_batch_qty'];
						$total_batch_qty2 += $batch_qty2;
						$total_re_process_batch_qty2 += $re_process_batch_qty2;
						$trims_dyeing_qty2 += $qtyValue['trims_dyeing_qty'];
						$total_dyeing_in += $dyeing_in2;
						//$total_dyeing_out += $dyeing_out2; // 22
						$total_dye_fini_in_qty2 += $dye_fini_in_qty2;
						$total_dye_fin_qty_out2 += $row['dye_fin_qty_out']; //24
						$total_fin_roll_rcv_qty2 += $fin_roll_rcv_qty2; //25
						$total_fin_roll_issue_qty2 += $row['fin_roll_issue_qty']; //26
						$total_finish_fabric_issue_return2+= $finish_fabric_issue_return2; //26
						//27
						//28
						$total_aop_issue_qty2 += $row['aop_issue_qty'];
						$total_aop_recv_qty2 += $row['aop_recv_qty'];
						$total_cutting_fab_recv2 += $row['cutting_fab_recv'];
						$total_cutting_in2 += $cutting_inhouse_qty2;
						// $total_cutting_out2 += $row['cutting_out']; 33
						$total_print_issue += $row['print_issue'];
						$total_print_rcv += $row['print_rcv'];
						$total_emb_issue += $row['emb_issue'];
						$total_emb_rcv += $row['emb_rcv'];

						$total_print_inhound2 += $print_inhound2;
						$total_print_outhound2 += $print_outhound2;
						$total_print_delivery_to_Cutting2 += $print_delivery_to_Cutting2;
						$total_embroidery_inhound2 += $embroidery_inhound2;
						$total_embroidery_outhound2 += $embroidery_outhound2;
						$total_embroidery_delivery_to_cutting2 += $embroidery_delivery_to_cutting2;

						$total_special_issue += $row['special_issue'];
						$total_special_rcv += $row['special_rcv'];
						$total_dyeing_issue += $row['dyeing_issue'];
						$total_dyeing_rcv += $row['dyeing_rcv'];
						$total_wash_issue += $row['wash_issue'];
						$total_wash_rcv += $row['wash_rcv'];
						$total_man_power2 += $row['man_power'];
						$total_operator2 += $row['operator'];
						$total_helper2 += $row['helper'];
						$total_sewing_inbound2 += $sewing_inbound_qty2;
						$total_sewing_outbound2 += $row['sewing_outbound_qty'];
						$total_iron += $row['iron'];
						$total_hangtag2 += $hangtag2;
						$total_poly_qty2 += $poly_qty2;
						$total_finish += $row['finish'];
						$total_ins_qty2 += $row['ins_qty'];
						$total_ship_qty2 += $row['ship_qty'];
						$total_ship_value2 += $row['ship_value'];
						$total_shipment_net_value2 += $shipment_net_value2;
						$total_ship_rtn_qty2 += $row['ship_rtn_qty'];
					}
					?>
				</tbody>

	            	<tr bgcolor="<? echo $bgcolor; ?>">

                        <td align="right" colspan="2"><b>Total</b></td>
                        <td align="right"><b><?=number_format($total_projec_qty,2); ?></b></td>
                        <td align="right"><b><?=number_format($total_confirm_qty,2); ?></b></td>
                        <td align="right"><b><?=number_format($total_cancel_order_qty,2); ?></b></td>
						<td align="right"><p><b><?=number_format($total_inactive_qty,2); ?></b></p></td>
						<td align="right"><b><?=number_format($total_ref_closing_qty,2); ?></b></td>
						<td align="right"><p><b><?=number_format($total_main_grey_qty,2)?></b></p></td>
						<td align="right"><p><b><?=number_format($total_sort_grey_qty,2); ?></b></p></td>
						<td align="right"><b><?=number_format($total_sample_prod_qty,2); ?></b></td>
                        <td align="right"><b><?=number_format($total_sample_delivery_qty,2); ?></b></td>
                        <td align="right"><b><?=number_format($total_grey_recv,2); ?></b></td>
                        <td align="right"><b><?=number_format($total_grey_iss,2); ?></b></td>
						<td align="right"><b><?=number_format($total_dyed_recv,2); ?></b></td>
                        <td align="right"><b><?=number_format($total_dyed_iss,2); ?></b></td>
                        <td align="right"><b><?=number_format($total_knitting_qty_in, 2); ?></b></td>
                        <td align="right"><b><?=number_format($total_knitting_qty_out, 2); ?></b></td>
						<td align="right"><b><?=number_format($total_recv_qty,2); ?></b></td>
                        <td align="right"><b><?=number_format($total_issue_qty,2); ?></b></td>
						<td align="right"><b><?=number_format($total_trans_in_qty,2); ?></b></td>
                        <td align="right"><b><?=number_format($total_trans_out_qty,2); ?></b></td>

                        <td align="right"><b><?=number_format($total_recv_batch_qty,2); ?></b></td>
                        <td align="right"><b><?=number_format($total_batch_qty,2); ?></b></td>
						<td align="right"><b><?=number_format($total_re_process_batch_qty, 2); ?></b></td>
						<td align="right"><b><?=number_format($total_trims_dyeing_qty,2); ?></b></td>
                        <td align="right"><b><?=number_format($total_dyeing_qty_in,2); ?></b></td>
						<td align="right"><b><?php //echo '22 Dyeing [Outbound]'; ?></b></td>
                        <td align="right"><b><?=number_format($total_dye_fini_in_qty,2); ?></b></td>
                        <td align="right"><b><?=number_format($total_dye_fin_qty_out,2); ?></b></td>
						<td align="right"><b><?=number_format($total_fin_roll_rcv_qty,2); ?></b></td>
                        <td align="right"><b><?=number_format($total_fin_roll_issue_qty,2); ?></b></td>
                        <td align="right"><b><?=number_format($total_finish_fabric_issue_return_qty,2); ?></b></td>
						<td align="right"><b><?php //echo '27 Finish Fabric Trasfer Recv'; ?></b></td>
                        <td align="right"><b><?php //echo '28 Finish Fabric Trasfer Issue'; ?></b></td>
                        <td align="right"><b><?=number_format($total_aop_issue_qty,2); ?></b></td>
                        <td align="right"><b><?=number_format($total_aop_recv_qty,2); ?></b></td>
                        <td align="right"><b><?=number_format($total_cutting_fab_recv,2); ?></b></td>
						<td align="right"><b><?=number_format($total_cutting_lay,2); ?></b></td>
						<td align="right" title="<? echo $row['cutting_inhouse'].'+'.$row['subc_cutting_qty']; ?>"><b><?=number_format($total_cutting_in2, 2); ?></b></td>
                        <td align="right"><? //echo '33 CUTTING [Out-bound]'; ?></td>


						<td align="right"><b><?=number_format($total_cutting_send_to_print_in, 2); ?></b></td>
                        <td align="right"><b><?=number_format($total_cutting_send_to_print_out, 2); ?></b></td>
                        <td align="right"><b><?=number_format(($total_cutting_send_to_print_in+$total_cutting_send_to_print_out), 2); ?></b></td>
						<td align="right"><b><?=number_format($row[1]['emb_issue'],2); ?></b></td>
                        <td align="right"><b><?=number_format($row[3]['emb_issue'],2); ?></b></td>
                        <td align="right"><b><?=number_format(($row[1]['emb_issue']+$row[3]['emb_issue']),2); ?></b></td>
						<td align="right"><b><?=number_format($total_cutting_rcv_print,2); ?></b></td>
						<td align="right"><b><?=number_format($total_cutting_rcv_emb,2); ?></b></td>

                        <td align="right"><b><?=number_format($print_rcv_inhound,2); ?></b></td>
                        <td align="right"><b><?=number_format($print_rcv_outbound,2); ?></b></td>
						<td align="right"><b><?=number_format($total_print_rcv_qty,2); ?></b></td>
						<td align="right"><b><?=number_format($total_print_pro_qty,2); ?></b></td>
						<td align="right"><b><?=number_format($total_print_delv_qty,2); ?></b></td>
						<td align="right"><b><?=number_format($total_emb_issue_qty,2); ?></b></td>
						<td align="right"><b><?=number_format($total_emb_rcv_qty,2); ?></b></td>
						<td align="right"><b><?=number_format($total_emb_qty,2); ?></b></td>
						<td align="right"><b><?=number_format($embroidery_production_qty,2); ?></b></td>
						<td align="right"><b><?=number_format($total_emb_delivery,2); ?></b></td>
						<td align="right"><b><?=number_format($total_man_power,2); ?></b></td>
						<td align="right"><b><?=number_format($total_operator,2); ?></b></td>
						<td align="right"><b><?=number_format($total_helper,2); ?></b></td>
						<td align="right"><b><?=number_format($total_sewing_in,2); ?></b></td>
						<td align="right"><b><?=number_format($total_sewing_inbound_qty,2); ?></b></td>
						<td align="right"><b><?=number_format($total_sewing_outbound_qty,2); ?></b></td>
						<td align="right"><p><b><?=number_format($total_sewing_efficiency,2); ?>%</b></p></td>
						<td align="right"><b><?php echo number_format($total_iron_qty, 2); ?></b>
                        </td>
                        <td align="right"><b><?php echo number_format($total_hangtag_qty,2); ?></b>
                        </td>
                        <td align="right"><b><?php echo number_format($total_poly_qty, 2); ?></b></td>
                        <td align="right"><b><?php echo number_format($total_finish_qty, 2); ?></b>
                        </td>
                        <td align="right"><b><? echo number_format($total_ins_qty, 2); ?></b></td>
                        <td align="right"><b><? echo number_format($total_ship_qty, 2); ?></b></td>
                        <td align="right"><b><? echo number_format($total_ship_value, 2); ?></b></td>
                        <td align="right"><b><? echo number_format($total_shipment_net_value_qty, 2); ?></b></td>
                        <td align="right"><b><? echo number_format($total_ship_rtn_qty, 2); ?></b></td>

                 </tr>




	        </table>

	    </fieldset>


	    <!-- ======FACTORY WISE PRODUCTION SUMMARY START========== -->
	    <!-- ======Compuny,location wise summury================== -->

	    <!-- ======FACTORY WISE PRODUCTION SUMMARY END============ -->
		<?
		foreach (glob("$user_id*.xls") as $filename) {
			if (@filemtime($filename) < (time() - $seconds_old))
				@unlink($filename);
		}
		//---------end------------//
		$name = time();
		$filename = $user_id . "_" . $name . ".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc, ob_get_contents());
		//$filename=$user_id."_".$name.".xls";
		echo "$total_data####$filename";
	}
	else if($type==7) // Show 7
	{
		
		$knit_sqls = sql_select("select a.receive_date, sum(b.grey_receive_qnty) as recv_qty
		from inv_receive_master a, pro_grey_prod_entry_dtls b
		where a.id=b.mst_id and a.status_active=1 $date_cond_knit $company_knit_cond and a.entry_form=2 and a.is_deleted=0 $location_id_cond
		group by  a.receive_date order by a.receive_date");

		// echo $sql="select a.receive_date, sum(b.grey_receive_qnty) as recv_qty
		// from inv_receive_master a, pro_grey_prod_entry_dtls b
		// where a.id=b.mst_id and a.status_active=1 $date_cond_knit $company_knit_cond and a.entry_form=2 and a.is_deleted=0 $location_id_cond
		// group by  a.receive_date order by a.receive_date";

		$knit_sqls_factory_wise = sql_select("select a.receive_date,a.knitting_company,a.knitting_location_id as location_id, a.buyer_id, sum(b.grey_receive_qnty) as recv_qty
		from inv_receive_master a, pro_grey_prod_entry_dtls b
		where a.id=b.mst_id and a.status_active=1 $date_cond_knit $company_knit_cond and a.entry_form=2 and a.is_deleted=0 $location_id_cond
		group by  a.receive_date,a.knitting_company,a.knitting_location_id,a.buyer_id order by a.receive_date ");



		$rev_qty2 = array();
		foreach ($knit_sqls_factory_wise as $value)
		{
			$rev_qty2[$value[csf('knitting_company')]][$value[csf('location_id')]]['knitting'] += $value[csf('recv_qty')];
		}

		$rev_qty = array();
		foreach ($knit_sqls as $value)
		{
			$dateitme=strtotime($value[csf('receive_date')]);
			$rev_qty[$dateitme]['knitting'] += $value[csf('recv_qty')];
	 	}
		// dyeing summary

		$dyeing_sqls_factory_wise = sql_select("select a.process_end_date as production_date, a.service_company, a.floor_id,b.production_qty as prod_qty from pro_fab_subprocess a, pro_fab_subprocess_dtls b where a.id=b.mst_id and a.entry_form=35 and b.load_unload_id=2 and a.result=1 $date_cond2 $company_dyeing_cond  ");

		// $sql="select a.process_end_date as production_date, a.service_company, a.floor_id,b.production_qty as prod_qty from pro_fab_subprocess a, pro_fab_subprocess_dtls b where a.id=b.mst_id and a.entry_form=35 and b.load_unload_id=2 and a.result=1 $date_cond2 $company_dyeing_cond  ";
		//  echo $dyeing_sqls_factory_wise;die;
	 	foreach ($dyeing_sqls_factory_wise as $value)
	 	{
			$dateitme=strtotime($value[csf('production_date')]);
			$rev_qty[$dateitme]['dyeing'] +=$value[csf('prod_qty')];
	 		$rev_qty2[$value[csf('service_company')]][$location_id_arr[$value[csf('floor_id')]]]['dyeing'] += $value[csf('prod_qty')];
		}




		$sql_qty = "(SELECT f.process_end_date as process_end_date,a.working_company_id,a.location_id,sum(case when f.service_source=1 then a.batch_weight end) as batch_weight,
		SUM(case when f.service_source=1 and a.batch_against!=3 then b.batch_qnty end) AS production_qty_inhouse,
		SUM(case when f.service_source=3 then b.batch_qnty end) AS production_qty_outbound,	SUM(case WHEN a.batch_against=3 and a.booking_without_order=1 then b.batch_qnty end) AS prod_qty_sample_without_order,SUM(case WHEN a.batch_against=3 and a.booking_without_order=0 then b.batch_qnty end) AS prod_qty_sample_with_order,SUM(case WHEN b.is_sales=1 then b.batch_qnty end) AS fabric_sales_order_qty from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d, pro_fab_subprocess f, lib_machine_name g, pro_batch_create_mst a	where f.batch_id=a.id $workingCompany_name_cond2 $dates_com and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1	and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and f.result=1 group by f.process_end_date,a.working_company_id,a.location_id )
		union ( SELECT f.process_end_date as process_end_date,a.working_company_id,a.location_id,sum(case when f.service_source=1 then a.batch_weight end) as batch_weight, SUM(case when f.service_source=1 and a.batch_against!=3 then b.batch_qnty end) AS production_qty_inhouse,SUM(case when f.service_source=3 then b.batch_qnty end) AS production_qty_outbound,SUM(case WHEN a.batch_against=3 and a.booking_without_order=1 then b.batch_qnty end) AS prod_qty_sample_without_order,	SUM(case WHEN a.batch_against=3 and a.booking_without_order=0 then b.batch_qnty end) AS prod_qty_sample_with_order,SUM(case WHEN b.is_sales=1 then b.batch_qnty end) AS fabric_sales_order_qty
		from pro_batch_create_dtls b, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g,wo_non_ord_samp_booking_mst h
		where h.booking_no=a.booking_no $workingCompany_name_cond2 and f.batch_id=a.id and f.batch_id=b.mst_id and a.entry_form=0 and g.id=f.machine_id and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0	and f.status_active=1 and f.is_deleted=0  $dates_com and f.result=1 group by  f.process_end_date,a.working_company_id,a.location_id ) order by process_end_date ";


		//  echo  $sql_qty;

		$sql_result=sql_select($sql_qty);

		$production_qty_inhouse=0;
		$production_qty_outbound=0;
		$prod_qty_sample_without_order=0;
		$prod_qty_sample_with_order=0;
		$fabric_sales_order_qty=0;
		$batchIDs="";
		foreach($sql_result as $row)
		{


			// $rev_qty2[$row[csf('working_company_id')]][$row[csf('location_id')]]['dyeing'] +=$row[csf('production_qty_inhouse')]+
			// $row[csf('production_qty_outbound')]+$row[csf('prod_qty_sample_without_order')]+$row[csf('prod_qty_sample_with_order')]+$row[csf('fabric_sales_order_qty')];
		}
		// print_r($rev_qty2);

		$sql_subcontact_qty=sql_select("SELECT  SUM(b.batch_qnty) AS production_qty_subcontact,f.process_end_date as production_date from pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g
		where a.batch_against in(1,2) and f.batch_id=a.id and f.batch_id=b.mst_id and a.entry_form=36 and g.id=f.machine_id and a.id=b.mst_id and f.entry_form=38 and f.load_unload_id=2
		and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0
		$dates_com GROUP BY f.process_end_date order by f.process_end_date ");//$companyCond

		// echo $sql="SELECT  SUM(b.batch_qnty) AS production_qty_subcontact,f.process_end_date as production_date from pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a, pro_fab_subprocess f, lib_machine_name g
		// where a.batch_against in(1,2) and f.batch_id=a.id and f.batch_id=b.mst_id and a.entry_form=36 and g.id=f.machine_id and a.id=b.mst_id and f.entry_form=38 and f.load_unload_id=2
		// and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0
		// $dates_com GROUP BY f.process_end_date order by f.process_end_date ";



		$production_qty_subcontact=0;
		foreach($sql_subcontact_qty as $row)
		{

			$dateitme=strtotime($row[csf('production_date')]);
			// $rev_qty[$dateitme]['dyeing']+=$row[csf('production_qty_subcontact')];

		}

		$finish_fab_delv_sql="SELECT a.KNITTING_COMPANY,a.LOCATION_ID,a.DELEVERY_DATE,b.CURRENT_DELIVERY,a.ENTRY_FORM from PRO_GREY_PROD_DELIVERY_MST a, PRO_GREY_PROD_DELIVERY_DTLS b where a.id=b.mst_id and a.ENTRY_FORM in(54,67) and  b.ENTRY_FORM in(54,67) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $company_knit_cond $date_cond_finish_feb_dilv_store ";
		//   echo $finish_fab_delv_sql;die;
		$sql_result_fin_store_delv=sql_select($finish_fab_delv_sql);

		foreach($sql_result_fin_store_delv as $row)
		{
			$dateitme=strtotime($row[csf('delevery_date')]);
			if ($row['ENTRY_FORM']==54)
			{
				$rev_qty[$dateitme]['current_delivery'] += $row[csf('current_delivery')];
			    $rev_qty2[$row[csf('knitting_company')]][$row[csf('location_id')]]['current_delivery'] += $row[csf('current_delivery')];
			}else
			{
				$rev_qty[$dateitme]['current_delivery'] += $row[csf('current_delivery')];
				$rev_qty2[$row[csf('knitting_company')]][$row[csf('location_id')]]['current_delivery'] += $row[csf('current_delivery')];
			}

		}
		//==========================Knit Finish Fabric Issue======================================
		$knit_finish_fab_issue_data=sql_select("SELECT a.id, a.mst_id, a.trans_id, a.batch_id, a.prod_id,  a.issue_qnty,a.order_id,  a.body_part_id, a.gmt_item_id, b.cons_rate ,c.issue_date from inv_finish_fabric_issue_dtls a, inv_transaction b ,inv_issue_master c
		where a.trans_id= b.id  and a.mst_id=c.id and a.status_active=1 and c.company_id in($company_name) $knit_fin_fab_issue_date_cond and b.status_active=1 order by c.issue_date");

		foreach($knit_finish_fab_issue_data as $row)
		{
			$dateitme=strtotime($row[csf('issue_date')]);
			$rev_qty[$dateitme]['knit_finish_fab_issue_qty'] += $row[csf('issue_qnty')];
		}

		$cutting_data_sql=sql_select("SELECT a.production_date,CASE WHEN a.production_type=1 THEN b.production_qnty ELSE 0 END
		AS cutting_qty from pro_garments_production_mst a,pro_garments_production_dtls b WHERE a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond $company_cond");
		foreach($cutting_data_sql as $row)
		{
			$rev_qty[strtotime($row[csf('production_date')])]['cutting_qty'] += $row[csf('cutting_qty')];

		}

		//=============================Embroidery Production===================

		$emb_prod_data=sql_select("SELECT b.mst_id, b.id, b.buyer_po_id, b.qcpass_qty, b.color_size_id, b.remarks,b.production_date,a.entry_form ,a.company_id,a.LOCATION_ID FROM subcon_embel_production_mst a, subcon_embel_production_dtls b
		where  a.id=b.mst_id  and a.entry_form in (301,315,222) and a.status_active=1 and a.is_deleted=0 $printing_date_cond and a.company_id in($company_name)  and b.status_active=1 and b.is_deleted=0 order by b.production_date");

    //  ===============================Data Come From Print & Module=======================================//


		foreach($emb_prod_data as $row)
		{
			$dateitme=strtotime($row[csf('production_date')]);
			if($row[csf('entry_form')]==315){
				$rev_qty[$dateitme]['emb_prod_qty'] += $row[csf('qcpass_qty')];
				$rev_qty2[$row['COMPANY_ID']][$row['LOCATION_ID']]['emb_prod_qty'] += $row[csf('qcpass_qty')];
			}elseif($row[csf('entry_form')]==222)
			{
				$rev_qty[$dateitme]['print_prod_qty'] += $row[csf('qcpass_qty')];
				$rev_qty2[$row['COMPANY_ID']][$row['LOCATION_ID']]['print_prod_qty'] += $row[csf('qcpass_qty')];

			}

		}
		// echo $cutting_data_sql;die;

		$sqls = sql_select("SELECT a.production_date,a.production_type, a.embel_name, sum(b.production_qnty) as prod_quantity
		from pro_garments_production_mst a ,pro_garments_production_dtls b
		where a.id=b.mst_id and a.production_type=b.production_type and b.status_active=1 and b.is_deleted=0 and  a.status_active=1 $date_cond $company_cond and a.is_deleted=0 $location_cond
		group by a.production_date,a.production_type, a.embel_name order by a.production_date");

		$sqls_factory_wise = sql_select("SELECT a.production_date,a.serving_company,a.location,a.production_type, a.embel_name, sum(b.production_qnty) as prod_quantity	from pro_garments_production_mst a  ,pro_garments_production_dtls b
		where a.id=b.mst_id and a.production_type=b.production_type and b.status_active=1 and b.is_deleted=0 and a.status_active=1 $date_cond $company_cond and a.is_deleted=0 $location_cond
		group by a.production_date,a.serving_company,a.location,a.production_type, a.embel_name order by a.production_date");



		$sqls_emb = sql_select("SELECT a.production_date,a.production_type, a.embel_name, sum(a.production_quantity) as prod_quantity
		from pro_garments_production_mst a
		where status_active=1 $date_cond $company_cond_emb and is_deleted=0 $location_cond_emb
		group by a.production_date,a.production_type, a.embel_name order by a.production_date");

		// echo $sql="SELECT a.production_date,a.production_type, a.embel_name, sum(a.production_quantity) as prod_quantity
		// from pro_garments_production_mst a
		// where status_active=1 $date_cond $company_cond_emb and is_deleted=0 $location_cond_emb
		// group by a.production_date,a.production_type, a.embel_name order by a.production_date";

		$sqls_emb_factory_wise = sql_select("SELECT a.production_date,a.sending_company as serving_company,a.sending_location as location,a.production_type, a.embel_name, sum(a.production_quantity) as prod_quantity
		from pro_garments_production_mst a
		where status_active=1 $date_cond $company_cond_emb and is_deleted=0 $location_cond_emb
		group by a.production_date,a.sending_company,a.sending_location,a.production_type, a.embel_name order by
		a.production_date");

		// echo $sql="SELECT a.production_date,a.sending_company as serving_company,a.sending_location as location,a.production_type, a.embel_name, sum(a.production_quantity) as prod_quantity
		// from pro_garments_production_mst a
		// where status_active=1 $date_cond $company_cond_emb and is_deleted=0 $location_cond_emb
		// group by a.production_date,a.sending_company,a.sending_location,a.production_type, a.embel_name order by
		// a.production_date";
	 	foreach($sqls_emb_factory_wise as $value)
		{

			if ($value[csf('production_type')] == 2)
			 {
				if ($value[csf('embel_name')] == 1)
				 {
	 				$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['print_issue'] += $value[csf('prod_quantity')];
				 }

				 else if ($value[csf('embel_name')] == 2)
				  {
	 				$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['emb_issue'] += $value[csf('prod_quantity')];
				  }

				else if ($value[csf('embel_name')] == 4)
				 {
	 				$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['special_issue'] += $value[csf('prod_quantity')];
				 }

				else if ($value[csf('embel_name')] == 5)
				 {
	 				$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['dyeing_issue'] += $value[csf('prod_quantity')];
				 }

	 		}


			else if ($value[csf('production_type')] == 3)
			{
				if ($value[csf('embel_name')] == 1)
				{
	 				$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['print_rcv'] += $value[csf('prod_quantity')];
				}

				else if ($value[csf('embel_name')] == 2)
				{
	 				$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['emb_rcv'] += $value[csf('prod_quantity')];
				}

				else if ($value[csf('embel_name')] == 4)
				{
	 				$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['special_rcv'] += $value[csf('prod_quantity')];
				}

				else if ($value[csf('embel_name')] == 5)
				{
	 				$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['dyeing_rcv'] += $value[csf('prod_quantity')];
				}
			}
		}

	 	foreach($sqls_emb as $value)
		{

			$dateitme=strtotime($value[csf('production_date')]);
			if ($value[csf('production_type')] == 2)
			 {
				if ($value[csf('embel_name')] == 1)
				 {
					$rev_qty[$dateitme]['print_issue'] += $value[csf('prod_quantity')];
				 }

				 else if ($value[csf('embel_name')] == 2) {
					$rev_qty[$dateitme]['emb_issue'] += $value[csf('prod_quantity')];
				}

				else if ($value[csf('embel_name')] == 4) {
					$rev_qty[$dateitme]['special_issue'] += $value[csf('prod_quantity')];
				}

				else if ($value[csf('embel_name')] == 5) {
					$rev_qty[$dateitme]['dyeing_issue'] += $value[csf('prod_quantity')];
				}



			}



			else if ($value[csf('production_type')] == 3) {
				$dateitme=strtotime($value[csf('production_date')]);
				if ($value[csf('embel_name')] == 1) {
					$rev_qty[$dateitme]['print_rcv'] += $value[csf('prod_quantity')];
				}

				else if ($value[csf('embel_name')] == 2) {
					$rev_qty[$dateitme]['emb_rcv'] += $value[csf('prod_quantity')];
				}

				else if ($value[csf('embel_name')] == 4) {
					$rev_qty[$dateitme]['special_rcv'] += $value[csf('prod_quantity')];
				}

				else if ($value[csf('embel_name')] == 5) {
					$rev_qty[$dateitme]['dyeing_rcv'] += $value[csf('prod_quantity')];
				}
			}
		}


		foreach ($sqls as $value)
		{
			$dateitme=strtotime($value[csf('production_date')]);
			if ($value[csf('production_type')] == 1) {
				$rev_qty[$dateitme]['cutting'] += $value[csf('prod_quantity')];
			} else if ($value[csf('production_type')] == 5) {
				$rev_qty[$dateitme]['sewing'] += $value[csf('prod_quantity')];
			} else if ($value[csf('production_type')] == 7) {
				$rev_qty[$dateitme]['iron'] += $value[csf('prod_quantity')];
			} else if ($value[csf('production_type')] == 11) {
				$rev_qty[$dateitme]['poly'] += $value[csf('prod_quantity')];
			} else if ($value[csf('production_type')] == 15) {
				$rev_qty[$dateitme]['hangtag'] += $value[csf('prod_quantity')];
			} else if ($value[csf('production_type')] == 8) {
				$rev_qty[$dateitme]['finish'] += $value[csf('prod_quantity')];
			} else if ($value[csf('production_type')] == 2) {
				 if ($value[csf('embel_name')] == 3) {
					$rev_qty[$dateitme]['wash_issue'] += $value[csf('prod_quantity')];
			}



			}
			else if ($value[csf('production_type')] == 3)
			 {
				$dateitme=strtotime($value[csf('production_date')]);
				 if ($value[csf('embel_name')] == 3) {
					$rev_qty[$dateitme]['wash_rcv'] += $value[csf('prod_quantity')];
					$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['wash_rcv'] += $value[csf('prod_quantity')];
				}

			}
		}

		foreach ($sqls_factory_wise as $value)
		{
			if ($value[csf('production_type')] == 1)
			{
	 			$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['cutting'] += $value[csf('prod_quantity')];
			}
			else if ($value[csf('production_type')] == 5)
			 {
	 			$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['sewing'] += $value[csf('prod_quantity')];
			 }
			 else if ($value[csf('production_type')] == 11)
			 {
	 			$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['poly'] += $value[csf('prod_quantity')];
			 }
			 else if ($value[csf('production_type')] == 15)
			 {
	 			$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['hangtag'] += $value[csf('prod_quantity')];
			 }
			else if ($value[csf('production_type')] == 7)
			 {
	 			$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['iron'] += $value[csf('prod_quantity')];
			 }
			else if ($value[csf('production_type')] == 8)
			{
	 			$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['finish'] += $value[csf('prod_quantity')];
			}
			else if ($value[csf('production_type')] == 2)
			{
				 if ($value[csf('embel_name')] == 3)
				  {
	 				$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['wash_issue'] += $value[csf('prod_quantity')];
				  }
	 		}
			else if ($value[csf('production_type')] == 3)
			 {
				 if ($value[csf('embel_name')] == 3)
				  {
	 				$rev_qty2[$value[csf('serving_company')]][$value[csf('location')]]['wash_rcv'] += $value[csf('prod_quantity')];
				  }

			}
		}

		//======================Garments Delivery Entry========================

		$gmt_delivery_data=sql_select("SELECT b.id,b.garments_nature,b.po_break_down_id,b.item_number_id,b.ex_factory_date,b.ex_factory_qnty,b.delivery_mst_id ,a.delivery_date,a.delivery_company_id,a.location_id	from pro_ex_factory_delivery_mst a,pro_ex_factory_mst b where  a.id=b.delivery_mst_id  and b.status_active=1 and b.entry_form<>85 and b.is_deleted=0 $delivery_date_cond $company_cond4 order by a.delivery_date");



		foreach($gmt_delivery_data as $row)
		{
			$dateitme=strtotime($row[csf('delivery_date')]);
			$rev_qty[$dateitme]['gmts_delivery_qty'] += $row[csf('ex_factory_qnty')];
			$rev_qty2[$row[csf('delivery_company_id')]][$row[csf('location_id')]]['gmts_delivery_qty'] += $row[csf('ex_factory_qnty')];
		}
		// echo"<pre>";
		// print_r($rev_qty2);


		ob_start();
		?>
	    <style>
	        #sammary_tbl th, #sammary_tbl td {
	            padding: 0 7px;
	        }
	        fieldset{border:0;}
	    </style>
	    <fieldset style="width: 1520px; margin:20px auto;">
	        <table cellpadding="0" cellspacing="0" style="text-align: center; width: 100%">
	            <tr class="form_caption" style="border:none;">
	                <td align="center" width="100%" colspan="09"><strong style="font-size:25px"><?
							$com_name = return_field_value("company_name", "lib_company", "id=" . $cbo_company_name, "company_name");
							echo $com_name; ?></strong></td>
	            </tr>

	            <tr class="form_caption" style="border:none;">
	                <td align="center" width="100%" colspan="15">
	                    <strong style="font-size:16px">Production Summary:
							<?
							if ($start_date != '') echo change_date_format($start_date) . ' To ' . change_date_format($end_date); else echo '';
							?>
	                    </strong>
	                </td>
	            </tr>

	            <tr>
	                <th colspan="09" style="text-align: center;">Date of Print:&nbsp&nbsp<? echo date('d-m-Y'); ?></th>
	            </tr>

	            <tr>
	                <th colspan="09" style="text-align: center;">Time of Print:&nbsp&nbsp<? echo date('h:i A'); ?></th>
	            </tr>
	        </table>
	        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
	            <thead>
		            <tr>
		                <th width="30">SL</th>
		                <th width="80">DATE</th>
		                <th width="80">KNITTING</th>
		                <th width="80">DYEING</th>

						<th width="80">FINISH FABRIC DELIVERY TO STORE</th>
						<th width="80">Finish Fabric Issue</th>

		                <th width="80">CUTTING</th>

						<th width="80">PRINT</th>
						<th width="80">Embr.</th>


		                <th width="80">SEWING</th>
		                <th width="80">IRON</th>
						<th width="80">HANGTAG</th>
						<th width="80">POLY</th>
		                <th width="80">PACKING AND FINISHING</th>
						<th width="80">Ex-Factory</th>
		                <!-- <th width="">REMARKS</th> -->
		            </tr>
	            </thead>
				<?php
				$date_arr = array();
				$date_fri_arr = array();
			 	$k=1;
	 			$total_knitting_qty = 0;
				$total_dyeing_qty = 0;
				$total_cutting_qty = 0;
				$total_print_issue_qty = 0;
				$total_print_rcv_qty = 0;
				$total_emb_issue_qty = 0;
				$total_emb_rcv_qty = 0;
				$total_special_issue_qty = 0;
				$total_special_rcv_qty = 0;
				$total_dyeing_issue_qty = 0;
				$total_dyeing_rcv_qty = 0;
				$total_wash_issue_qty = 0;
				$total_wash_rcv_qty = 0;
				$total_sewing_qty = 0;
				$total_iron_qty = 0;
				$total_finish_qty = 0;
				$total_aop_qty=0;
				$total_knit_finish_fab_qty=0;
				$total_print_qty =0;
				$total_emb_qty=0;
				$total_wash_qty=0;
				$total_gmts_del_qty=0;
				ksort($rev_qty);
	 			foreach ($rev_qty as $qtyValue => $result_row)
	 			{
					if (!in_array($qtyValue, $date_arr))
					{
						$date_arr[] = $qtyValue;
					}

					$i = 1;
					$total_knitting_qty  =$total_knitting_qty + $result_row['knitting'];
					$total_dyeing_qty += $result_row['dyeing'];
					$total_cutting_qty += $result_row['cutting_qty'];
					$total_aop_qty += $result_row['aop_prod_qty'];
					$total_knit_finish_fab_qty += $result_row['knit_finish_fab_issue_qty'];
					$total_print_qty += $result_row['print_prod_qty'];
					$total_emb_qty += $result_row['emb_prod_qty'];

					$total_wash_qty += $result_row['wash_prod_qty'];
					$total_gmts_del_qty += $result_row['gmts_delivery_qty'];

					$total_print_issue_qty += $result_row['print_issue'];
					$total_print_rcv_qty += $result_row['print_rcv'];
					$total_emb_issue_qty += $result_row['emb_issue'];
					$total_emb_rcv_qty += $result_row['emb_rcv'];
					$total_special_issue_qty += $result_row['special_issue'];
					$total_special_rcv_qty += $result_row['special_rcv'];
					// $total_dyeing_issue_qty += $result_row['dyeing_issue'];
					// $total_dyeing_rcv_qty += $result_row['dyeing_rcv'];
					$total_wash_issue_qty += $result_row['wash_issue'];
					$total_wash_rcv_qty += $result_row['wash_rcv'];
					$total_sewing_qty += $result_row['sewing'];
					$total_iron_qty += $result_row['iron'];
					$total_hangtag_qty += $result_row['hangtag'];
					$total_poly_qty += $result_row['poly'];
					$total_finish_qty += $result_row['finish'];
				    $getdate = date('D', $time =$qtyValue);
					if ($getdate != 'Fri')
					{
						if (!in_array($qtyValue, $date_fri_arr))
						{
							$date_fri_arr[] = $qtyValue;
						}

						//$j++;
						$totl_knit_qty += $result_row['knitting'];
						$totl_dyei_qty += $result_row['dyeing'];

						$totl_aop_qty += $result_row['aop_prod_qty'];
						$totl_knit_fin_fab_qty += $result_row['knit_finish_fab_issue_qty'];

						$totl_cutt_qty += $result_row['cutting_qty'];

						$totl_print_qty += $result_row['print_prod_qty'];
						$totl_emb_qty += $result_row['emb_prod_qty'];

						$totl_pnt_issue_qty += $result_row['print_issue'];
						$totl_prnt_rcv_qty += $result_row['print_rcv'];

						$totl_emb_issue_qty += $result_row['emb_issue'];
						$totl_emb_rcv_qty += $result_row['emb_rcv'];

						$totl_special_issue_qty += $result_row['special_issue'];
						$totl_special_rcv_qty += $result_row['special_rcv'];

						// $totl_dyeing_issue_qty += $result_row['dyeing_issue'];
						// $totl_dyeing_rcv_qty += $result_row['dyeing_rcv'];

						$totl_wash_issue_qty += $result_row['wash_issue'];
						$totl_wash_rcv_qty += $result_row['wash_rcv'];
						$totl_sewi_qty += $result_row['sewing'];
						$totl_wash_qty += $result_row['wash_prod_qty'];
						$totl_iro_qty += $result_row['iron'];
						$totl_fini_qty += $result_row['finish'];
						$totl_gmts_del_qty += $result_row['gmts_delivery_qty'];
						$totl_fin_del_qty += $result_row['current_delivery'];
					}

					if ($i % 2 == 1) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

					  ?>
		               <tr bgcolor="<? $timestamp =$qtyValue;$day_name= date("l", $timestamp );
		            	if($day_name=="Friday"){echo "crimson";} else{echo $bgcolor; }?>" style="<?if($day_name=="Friday"){echo 'color:white;';}?>">
		                <td align="center"><?  echo $k; ?></td>
		                <td align="center"><? echo date('d-M-Y',$qtyValue); ?></td>
		                <td align="right"><? echo number_format($result_row['knitting'], 2); ?></td>
		                <td align="right"><? echo number_format($result_row['dyeing'], 2); ?></td>
						<td align="right"><?=number_format($result_row['current_delivery'],2);  ?></td>
						<td align="right"><? echo number_format($result_row['knit_finish_fab_issue_qty'], 2); ?></td>
		                <td align="right"><? echo number_format($result_row['cutting_qty'], 0); ?></td>
						<td align="right"><? echo number_format($result_row['print_prod_qty'], 0); ?></td>
						<td align="right"><? echo number_format($result_row['emb_prod_qty'], 0); ?></td>

		                <td align="right"><? echo number_format($result_row['sewing'], 0); ?></td>
		                <td align="right"><? echo number_format($result_row['iron'], 0); ?></td>
						<td align="right"><? echo number_format($result_row['hangtag'], 0); ?></td>
						<td align="right"><? echo number_format($result_row['poly'], 0); ?></td>
		                <td align="right"><? echo number_format($result_row['finish'], 0); ?></td>
						<td align="right"><? echo number_format($result_row['gmts_delivery_qty'], 0); ?></td>

		             </tr>
					<?
					$k++;

					$date_count = count($date_arr);
					$date_count2 = count($date_fri_arr);
				}
				?>
	            <tr>
	                <td colspan="18" style="padding: 1px;"></td>
	            </tr>
	            <tr>

	                <td align="left" colspan="2"><b>Total</b></td>
	                <td align="right"><b><?php echo number_format($total_knitting_qty, 2); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_dyeing_qty, 2); ?></b></td>
					<td align="right"><b><?=number_format($totl_fin_del_qty,2)?></b></td>
					<td align="right"><b><?php echo number_format($total_knit_finish_fab_qty, 2); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_cutting_qty, 0); ?></b></td>
					<td align="right"><b><?php echo number_format($total_print_qty, 0); ?></b></td>
					<td align="right"><b><?php echo number_format($total_emb_qty, 0); ?></b></td>

	                <td align="right"><b><?php echo number_format($total_sewing_qty, 0); ?></b></td>

	                <td align="right"><b><?php echo number_format($total_iron_qty, 0); ?></b></td>
					<td align="right"><b><?php echo number_format($total_hangtag_qty, 0); ?></b></td>
					<td align="right"><b><?php echo number_format($total_poly_qty, 0); ?></b></td>
	                <td align="right"><b><?php echo number_format($total_finish_qty, 0); ?></b></td>
					<td align="right"><b><?php echo number_format($total_gmts_del_qty, 0); ?></b></td>
	                <td></td>
	            </tr>


	        </table>
	    </fieldset><br/>


	    <!-- ======compuny,location wise summury========== -->
	    <fieldset style="width: 1320px; margin:0px auto;">
	        <table cellspacing="0" cellpadding="0" id="sammary_tbl" border="1" rules="all" class="rpt_table" style="width: 100%">
	            <thead>
	            <tr>
	                <th colspan="20" style="text-align: center;">FACTORY WISE PRODUCTION SUMMARY</th>
	            </tr>
	            <tr>
						<th>SL</th>
						<th>Working Company</th>
						<th>Location/Factory</th>
						<th>KNITTING</th>
						<th>DYEING</th>
						<th>FINISH FABRIC DELIVERY TO STORE</th>
						<th>CUTTING</th>
						<th>PRINT SEND From Cutting</th>
						<th>PRINT RCV</th>
						<th>PRINT</th>
						<th>EMB SEND from cutting</th>
						<th>EMB RCV</th>
						<th>EMBROIDERY</th>
						<th>SEWING</th>
						<th>IRON</th>
						<th>HANGTAG</th>
						<th>POLY</th>
						<th>PACKING AND FINISHING</th>
						<th >Ex-Factory</th>
	            </tr>
	            </thead>

				<?

				$k = 1;
				/*echo "<pre>";
				print_r($rev_qty2);die;*/
				foreach ($rev_qty2 as $company_id => $company_data) {
					foreach ($company_data as $location_id => $location_data) {
						if ($k % 2 == 1) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

						?>
	                    <tr bgcolor="<? echo $bgcolor; ?>">
	                        <td><? echo $k; ?></td>
	                        <td><? echo $company_arr[$company_id]; ?></td>
	                        <td><? echo $location_arr[$location_id]; ?></td>
	                        <td align="right"><a href="##" onClick="openmypage_popup(<? echo $company_id; ?>,'<? echo $location_id; ?>','1','production_popup','1','knitting popup','knitting');" ><? echo number_format($location_data['knitting'], 2); ?></a></td>

	                        <td align="right"><a href="##" onClick="openmypage_popup(<? echo $company_id; ?>,'<? echo $location_id; ?>','1','production_popup','1','dyeing popup','dyeing');" ><? echo number_format($location_data['dyeing'], 2); ?></a></td>
							<td align="right"><? echo number_format($location_data['current_delivery'], 0); ?></td>

	                        <td align="right"><a href="##" onClick="openmypage_popup(<? echo $company_id; ?>,'<? echo $location_id; ?>','1','production_popup','0','cutting popup','');" > <? echo number_format($location_data['cutting'], 0); ?></a></td>

	                        <td align="right"><? echo number_format($location_data['print_issue'], 0); ?></td>
	                        <td align="right"><? echo number_format($location_data['print_rcv'], 0); ?></td>
							<td align="right"><? echo number_format($location_data['print_prod_qty'], 0); ?></td>
	                        <td align="right"><? echo number_format($location_data['emb_issue'], 0); ?></td>
	                        <td align="right"><? echo number_format($location_data['emb_rcv'], 0); ?></td>
							<td align="right"><? echo number_format($location_data['emb_prod_qty'], 0); ?></td>


	                        <td align="right"><a href="##" onClick="openmypage_popup(<? echo $company_id; ?>,'<? echo $location_id; ?>','5','production_popup','0','sewing popup','');" ><? echo number_format($location_data['sewing'], 0); ?></a></td>
	                        <td align="right"><a href="##" onClick="openmypage_popup(<? echo $company_id; ?>,'<? echo $location_id; ?>','7','production_popup','0','iron popup','');" ><? echo number_format($location_data['iron'], 0); ?></a></td>

							<td align="right"><a href="##" onClick="openmypage_popup(<? echo $company_id; ?>,'<? echo $location_id; ?>','15','production_popup','0','hangtag popup','');" ><? echo number_format($location_data['hangtag'], 0); ?></a></td>

							<td align="right"><a href="##" onClick="openmypage_popup(<? echo $company_id; ?>,'<? echo $location_id; ?>','11','production_popup','0','poly popup','');" ><? echo number_format($location_data['poly'], 0); ?></a></td>

	                        <td align="right"><a href="##" onClick="openmypage_popup(<? echo $company_id; ?>,'<? echo $location_id; ?>','8','production_popup','0','finish popup','');" ><? echo number_format($location_data['finish'],0); ?></a></td>

	                        <td align="right"><? echo number_format($location_data['gmts_delivery_qty'], 0); ?></td>
	                    </tr>
						<?
						$k++;
						$total_knitting += $location_data['knitting'];
						$total_dyeing += $location_data['dyeing'];
						$total_cutting += $location_data['cutting'];
						$total_print_issue += $location_data['print_issue'];
						$total_print_rcv += $location_data['print_rcv'];
						$total_print += $location_data['print_prod_qty'];
						$total_emb_issue += $location_data['emb_issue'];
						$total_emb_rcv += $location_data['emb_rcv'];
						$total_emb += $location_data['emb_prod_qty'];
						$total_hangtag += $location_data['hangtag'];
						$total_poly += $location_data['poly'];
						$total_ex_factory += $location_data['gmts_delivery_qty'];

						$total_sewing += $location_data['sewing'];
						$total_iron += $location_data['iron'];
						$total_finish += $location_data['finish'];
						$total_fin_del_qty += $location_data['current_delivery'];

					}
				}
				?>
	            <tr bgcolor="<? echo $bgcolor; ?>">
	                <td align="left" colspan="3"><b>TOTAL</b></td>
	                <td align="right"><b><? echo number_format($total_knitting, 2); ?></b></td>
	                <td align="right"><b><? echo number_format($total_dyeing, 2); ?></b></td>
					<td align="right"><b><?=number_format($total_fin_del_qty,2)?></b></td>
	                <td align="right"><b><? echo number_format($total_cutting, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_print_issue, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_print_rcv, 0); ?></b></td>
					  <td align="right"><b><? echo number_format($total_print, 0); ?></b></td>

	                <td align="right"><b><? echo number_format($total_emb_issue, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_emb_rcv, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_emb, 0); ?></b></td>

	                <td align="right"><b><? echo number_format($total_sewing, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_iron, 0); ?></b></td>
					<td align="right"><b><? echo number_format($total_hangtag, 0); ?></b></td>
	                <td align="right"><b><? echo number_format($total_poly, 0); ?></b></td>

	                <td align="right"><b><? echo number_format($total_finish, 0); ?></b></td>
					<td align="right"><b><? echo number_format($total_ex_factory, 0); ?></b></td>
	                <td> </td>
	            </tr>
	        </table>
	    </fieldset>
		<?
		foreach (glob("$user_id*.xls") as $filename) {
			if (@filemtime($filename) < (time() - $seconds_old))
				@unlink($filename);
		}
		//---------end------------//
		$name = time();
		$filename = $user_id . "_" . $name . ".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc, ob_get_contents());
		//$filename=$user_id."_".$name.".xls";
		echo "$total_data####$filename";
	}

	exit();
}
disconnect($con);

?>
