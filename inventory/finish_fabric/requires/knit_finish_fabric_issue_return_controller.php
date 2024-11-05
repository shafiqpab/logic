<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-All Buyer-", $selected, "" );     	 
	exit();
}

//====================issue_popup========
if ($action=="issue_popup")
{
	echo load_html_head_contents("System ID Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(data)
		{
			datas=data.split("**");

			$('#hidden_issue_id').val(datas[0]);
			$("#hidden_issue_no").val(datas[1]);
			$("#hidden_fso_company").val(datas[2]);
			$("#hidden_po_company").val(datas[3]);
			$("#hidden_issue_date").val(datas[4]);
			$("#hidden_fso_company_id").val(datas[5]);
			
			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>
		<div align="center" style="width:950px;">
			<form name="searchsystemidfrm"  id="searchsystemidfrm">
				<fieldset style="width:950px;">
					<legend>Enter search words</legend>
					<table cellpadding="0" cellspacing="0" width="950" border="1" rules="all" class="rpt_table">
						<thead>
							<th class="">Company Name</th>
							<th>Delivery Date Range</th>
							<th>Search By</th>
							<th id="search_by_td_up">Please Enter System Id</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
								<input type="hidden" name="hidden_issue_id" id="hidden_issue_id" class="text_boxes" value="">
								<input type="hidden" name="hidden_issue_no" id="hidden_issue_no" class="text_boxes" value="">
								<input type="hidden" name="hidden_fso_company_id" id="hidden_fso_company_id" class="text_boxes" value="">
								<input type="hidden" name="hidden_fso_company" id="hidden_fso_company" class="text_boxes" value="">
								<input type="hidden" name="hidden_po_company" id="hidden_po_company" class="text_boxes" value="">
								<input type="hidden" name="hidden_issue_date" id="hidden_issue_date" class="text_boxes" value="">
							</th>
						</thead>
						<tr class="general">
							<td>
								<?
								echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.id","id,company_name", 0, "--Select Company--", 0,"");
								
								?>
							</td>
							<td>
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px;">To<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px;">
							</td>
							
							<td>
								<?
								$search_by_arr=array(1=>"MRR No/Issue No",2=>"FSO",3=>"Booking No",4=>"Batch No");
								$dd="change_search_event(this.value, '0*0*0*0', '0*0*0*0', '../../') ";
								echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
								?>
							</td>
							<td id="search_by_td">
								<input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
							</td>
							<td>
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_company_id').value, 'create_issue_search_list_view', 'search_div', 'knit_finish_fabric_issue_return_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
						</tr>
					</table>
					<div style="margin-top:10px;" id="search_div" align="center"></div>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="create_issue_search_list_view")
{
	$data = explode("_",$data);
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$cbo_year_selection =$data[4];
	$company_id = $data[5];

	if($company_id==0)
	{
		?>
		<span style="font-size:14px; font-weight:bold; color: red;">Please select company</span>
		<?
		exit();
	}

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	
	if($start_date!="" && $end_date!="")
	{		
		if($db_type==0)
		{
			$date_cond="and a.insert_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and a.insert_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}

		$year_condition = "";
	}
	else
	{
		$date_cond="";

		if($db_type==0)
		{
			if($cbo_year_selection>0)
			{
				$year_condition=" and YEAR(a.insert_date)=$cbo_year_selection";
			}
		}else 
		{
			if($cbo_year_selection>0)
			{
				$year_condition=" and to_char(a.insert_date,'YYYY')=$cbo_year_selection";
			}
		}	
	}
	
	if(trim($data[0])!="")
	{
		if($search_by==1)
			$search_field_cond="and a.issue_number_prefix_num like '$search_string'";
		else if($search_by==2)
			$search_field_cond="and d.job_no_prefix_num like '$search_string'";
		else if($search_by==3)
			$search_field_cond="and d.sales_booking_no like '$search_string'";
		else 
			$search_field_cond="and c.batch_no like '$search_string'";
	}
	else
	{
		$search_field_cond="";
	}

	
	if($db_type==0) 
	{
		$year_field="YEAR(a.insert_date)";
	}
	else if($db_type==2) 
	{
		$year_field="to_char(a.insert_date,'YYYY')";
	}
	else 
	{
		$year_field="null";
	}
	
   /* $result =sql_select("select a.id,a.issue_number mrr_no,a.issue_number_prefix_num,a.company_id as issue_company,$year_field as year,a.issue_date,b.order_id,b.batch_id,sum(b.issue_qnty) issue_qnty from inv_issue_master a,inv_finish_fabric_issue_dtls b,pro_batch_create_mst c,fabric_sales_order_mst d where a.id=b.mst_id and b.batch_id=c.id and b.order_id=d.id and a.entry_form=224 and a.company_id=$company_id and a.status_active='1' and a.is_deleted='0' and b.status_active=1 and  b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_cond $year_condition $search_field_cond group by a.id,a.issue_number,a.issue_number_prefix_num,a.company_id,a.insert_date,a.issue_date,b.order_id,b.batch_id");*/

	$issue_sql="SELECT a.id, issue_number_prefix_num, to_char(a.insert_date,'YYYY') as year, a.issue_number , a.challan_no, a.company_id, a.issue_date,a.issue_purpose,a.supplier_id party_name, a.buyer_id,a.location_id,a.store_id, b.sample_type, sum(b.issue_qnty) as issue_qnty, listagg(cast(c.batch_no as varchar2(4000)), ',') within group (order by c.id) as batch_no,d.id order_id,d.job_no,d.sales_booking_no,d.buyer_id,d.within_group,d.po_buyer,d.company_id as salse_company_id,d.po_company_id, d.po_job_no 
	from inv_issue_master a, inv_finish_fabric_issue_dtls b, pro_batch_create_mst c,fabric_sales_order_mst d,order_wise_pro_details e 
	where a.entry_form=224 and a.id=b.mst_id and b.batch_id=c.id and b.id=e.dtls_id and b.trans_id=e.trans_id and d.id=e.po_breakdown_id and e.entry_form=224 and e.trans_type=2 and a.item_category=2 and a.company_id=$company_id $search_field_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $date_cond 
	group by a.id, issue_number_prefix_num, a.issue_number, a.challan_no, a.company_id, a.issue_date, a.issue_purpose,a.supplier_id, a.buyer_id,a.location_id,a.store_id, b.sample_type, a.insert_date,d.id,d.job_no,d.sales_booking_no,d.buyer_id,d.within_group,d.po_buyer,d.company_id,d.po_company_id, d.po_job_no order by a.id";

	$issueData = sql_select($issue_sql);

	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="950" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="100">Company</th>
			<th width="70">Challan NO</th>
			<th width="80">Within Group</th>
			<th width="100">Buyer</th>
			<th width="140">FSO No</th>
			<th width="120">Booking No</th>
			<th width="100">Batch No</th>
			<th width="80">Delivery date</th>
			<th width="80">Delivery Qnty</th>
		</thead>
	</table>
	<div style="width:950px; max-height:240px; overflow-y:scroll" id="list_container_batch" align="left">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="930" class="rpt_table" id="tbl_list_search">  
			<?
			$i=1;
			foreach ($issueData as $row)
			{  
				if ($i%2==0)  
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";	 

				$buyer_id = ($row[csf('within_group')]==1)?$row[csf('po_buyer')]:$row[csf('buyer_id')];

				
				if($row[csf('within_group')]==1)
				{
					$po_company_name = $company_arr[$row[csf('po_company_id')]];
				}else {
					$po_company_name = $company_arr[$row[csf('salse_company_id')]];
				}

				$fso_company_name = $company_arr[$row[csf('salse_company_id')]];

				$data = $row[csf('id')]."**".$row[csf('issue_number')]."**".$fso_company_name."**".$po_company_name."**".change_date_format($row[csf('issue_date')])."**".$row[csf('salse_company_id')];

				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $data; ?>');"> 
					<td width="40" align="center"><? echo $i; ?></td>
					<td width="100" align="center"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
					<td width="70" align="center"><p><? echo $row[csf('issue_number_prefix_num')]; ?></p></td>
					<td width="80" align="center"><p><? echo $yes_no[$row[csf('within_group')]]; ?></p></td>
					<td width="100" align="center"><p><? echo $buyer_arr[$buyer_id]; ?></p></td>
					<td width="140" align="center"><p><? echo $row[csf('job_no')]; ?></p></td>
					<td width="120" align="center"><p><? echo $row[csf('sales_booking_no')]; ?></p></td>
					<td width="100" align="center"><p><? echo $row[csf('batch_no')]; ?></p></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
					<td width="80" align="right"><? echo number_format($row[csf('issue_qnty')],2); ?></td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
	</div>
	<?	
	exit();
}

if ($action=="upto_variable_settings")
{
	$data=explode("**",$data);
	$company_id=$data[0];
	echo $variable_inventory=return_field_value("store_method","variable_settings_inventory","company_name='$company_id' and item_category_id=2 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	exit();
}

if($action == "check_previous_return")
{
	$data=explode("**",$data);
	$issue_id=$data[0];

	if($issue_id > 0)
	{
		$sql="select recv_number, id, receive_date from inv_receive_master  where booking_id = $issue_id and entry_form = 233 and status_active=1 and is_deleted=0";
	}
	
	$data_array=sql_select($sql,1);
	if(count($data_array)>0)
	{
		echo "1"."_".$data_array[0][csf('recv_number')]."_".$data_array[0][csf('id')]."_".change_date_format($data_array[0][csf('receive_date')]);
	}
	else
	{
		echo "0";
	}
	exit();	
}


if($action == "load_drop_floor")
{
	$data = explode("_", $data);

	$store_id=$data[0];
	$company_id = $data[1];
	$sl = $data[2];

	echo create_drop_down( "to_floor_".$sl, "80", "select b.floor_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.floor_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name","floor_id,floor_room_rack_name", 1, "--Select Floor--", 0, "load_drop_down( 'requires/knit_finish_fabric_issue_return_controller',$store_id+'_'+$company_id+'_'+$sl+'_'+this.value, 'load_drop_room', 'room_td_$sl');load_drop_down( 'requires/knit_finish_fabric_issue_return_controller',$store_id+'_'+$company_id+'_'+$sl+'_'+this.value, 'load_drop_rack', 'rack_td_$sl');load_drop_down( 'requires/knit_finish_fabric_issue_return_controller',$store_id+'_'+$company_id+'_'+$sl+'_'+'', 'load_drop_shelf', 'shelf_td_$sl');" );
}

if($action == "load_drop_room")
{
	$data = explode("_", $data);
	$store_id=$data[0];
	$company_id = $data[1];
	$sl = $data[2];
	$floor_id = $data[3];
	$floor_cond = ($floor_id != "") ? " and b.floor_id='$data[3]'" : "";
	echo create_drop_down( "to_room_".$sl, "80", "select b.room_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.room_id and b.store_id='$store_id' and a.company_id='$company_id' $floor_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.room_id,a.floor_room_rack_name order by a.floor_room_rack_name","room_id,floor_room_rack_name", 1, "--Select Room--", 0, "load_drop_down( 'requires/knit_finish_fabric_issue_return_controller',$store_id+'_'+$company_id+'_'+$sl+'_'+$floor_id+'_'+this.value, 'load_drop_rack', 'rack_td_$sl');load_drop_down( 'requires/knit_finish_fabric_issue_return_controller',$store_id+'_'+$company_id+'_'+$sl+'_'+'', 'load_drop_shelf', 'shelf_td_$sl');" );
}

if($action == "load_drop_rack")
{
	$data = explode("_", $data);
	$store_id=$data[0];
	$company_id = $data[1];
	$sl = $data[2];
	$floor_id = $data[3];
	$room_id = $data[4];

	$floor_cond = ($floor_id != "") ? " and b.floor_id='$floor_id'" : "";
	$room_cond = ($room_id != "") ? " and b.room_id='$room_id'" : "";

	echo create_drop_down( "to_rack_".$sl, '80', "select b.rack_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.rack_id and b.store_id='$store_id' and a.company_id='$company_id' $floor_cond $room_cond and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,a.floor_room_rack_name order by a.floor_room_rack_name","rack_id,floor_room_rack_name", 1, "--Select Rack--", 0, "load_drop_down( 'requires/knit_finish_fabric_issue_return_controller',$store_id+'_'+$company_id+'_'+$sl+'_'+this.value, 'load_drop_shelf', 'shelf_td_$sl');" );

}

if($action == "load_drop_shelf")
{
	$data = explode("_", $data);
	$store_id=$data[0];
	$company_id = $data[1];
	$sl = $data[2];
	$rack_no = $data[3];

	echo create_drop_down( "to_shelf_".$sl, '80', "select b.shelf_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.shelf_id and b.store_id='$store_id' and a.company_id='$company_id' and b.rack_id='$rack_no' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name","shelf_id,floor_room_rack_name", 1, "--Select Shelf--", 0, "" );
}


if($action=='list_view_garments')
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$dataArr = explode("**", $data);

	$issueId = $dataArr[0];
	$recievedId = $dataArr[1];

	$buyer_arr=return_library_array( "select id, short_name from lib_buyer where status_active=1 and is_deleted=0",'id','short_name');

	$mainQuery = "select a.id issue_id,a.issue_number challan_no,a.issue_date delevery_date,a.company_id as comp_id,b.id as dtls_id,b.prod_id product_id,b.batch_id,b.order_id,b.body_part_id bodypart_id,a.location_id,b.uom,b.fabric_shade,sum(b.issue_qnty) issue_qnty,b.no_of_roll roll_no,b.width_type,b.order_id,b.trans_id,c.color_id,d.detarmination_id determination_id,d.gsm,d.dia_width dia,e.job_no_prefix_num as fso_no,e.sales_booking_no as booking_no,e.company_id,e.po_company_id,e.po_buyer,e.buyer_id,e.style_ref_no,e.within_group,f.cons_uom,f.cons_amount,f.store_id,f.cons_rate,f.order_rate, b.aop_rate,f.floor_id,f.room,f.self,f.rack from inv_issue_master a,inv_finish_fabric_issue_dtls b,pro_batch_create_mst c,product_details_master d,fabric_sales_order_mst e,inv_transaction f where a.entry_form=224 and a.id=b.mst_id and b.batch_id=c.id and b.prod_id=d.id and b.order_id=e.id and a.status_active='1' and a.is_deleted='0' and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 and a.id=$issueId and a.id=f.mst_id and b.trans_id=f.id and f.transaction_type=2 and f.item_category=2 and f.status_active=1 and f.is_deleted=0 and c.id = f.pi_wo_batch_no  and d.id = f.prod_id group by a.id,a.issue_number,a.issue_date,a.company_id,b.id,b.prod_id,b.batch_id,b.order_id,b.body_part_id,a.location_id,b.uom,b.fabric_shade,b.no_of_roll,b.width_type,b.order_id,b.trans_id,c.color_id,d.detarmination_id,d.gsm,d.dia_width,e.job_no_prefix_num, e.sales_booking_no,e.company_id,e.po_company_id,e.po_buyer,e.buyer_id,e.style_ref_no,e.within_group,f.cons_uom,f.cons_amount,f.store_id,f.cons_rate,f.order_rate,b.aop_rate,f.order_amount,f.floor_id,f.room,f.self,f.rack";


	if($recievedId!="")
	{
		$recRetQuery = "select b.issue_dtls_id,sum(b.receive_qnty) as receive_qnty, sum(c.cons_amount) as rcv_amount, b.trans_id,b.id as return_dtls_id, c.store_id,b.floor,b.room,b.rack_no,b.shelf_no, b.remarks from inv_receive_master a,pro_finish_fabric_rcv_dtls b, inv_transaction c where a.entry_form=233 and a.id=b.mst_id  and a.id=$recievedId and b.trans_id = c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 and c.status_active =1  and c.is_deleted=0 group by b.issue_dtls_id,a.remarks,b.trans_id,b.id,c.store_id,b.floor, b.room,b.rack_no,b.shelf_no,b.remarks";

		$recRetQueryResult = sql_select($recRetQuery);
		foreach ($recRetQueryResult as  $row) 
		{
			$rcvReturnRefData[$row[csf("issue_dtls_id")]]["issue_dtls_id"] = $row[csf("issue_dtls_id")];
			$rcvReturnRefData[$row[csf("issue_dtls_id")]]["receive_qnty"] = $row[csf("receive_qnty")];
			$rcvReturnRefData[$row[csf("issue_dtls_id")]]["rcv_amount"] = $row[csf("rcv_amount")];
			$rcvReturnRefData[$row[csf("issue_dtls_id")]]["store_id"] = $row[csf("store_id")];
			$rcvReturnRefData[$row[csf("issue_dtls_id")]]["floor"] = $row[csf("floor")];
			$rcvReturnRefData[$row[csf("issue_dtls_id")]]["room"] = $row[csf("room")];
			$rcvReturnRefData[$row[csf("issue_dtls_id")]]["rack_no"] = $row[csf("rack_no")];
			$rcvReturnRefData[$row[csf("issue_dtls_id")]]["shelf_no"] = $row[csf("shelf_no")];
			$rcvReturnRefData[$row[csf("issue_dtls_id")]]["remarks"] = $row[csf("remarks")];

			$rcvReturnRefData[$row[csf("issue_dtls_id")]]["trans_id"] = $row[csf("trans_id")];
			$rcvReturnRefData[$row[csf("issue_dtls_id")]]["return_dtls_id"] = $row[csf("return_dtls_id")];
		}
	}

	//echo $mainQuery; die();
	
	$mainQueryResult = sql_select($mainQuery);

	if(empty($mainQueryResult))
	{
		echo "<span style='color:red; font-weight:bold; font-size:14px;'><center>No Data Found</center></span>";
		exit();
	}

	$maniDataArr = array();
	foreach ($mainQueryResult as  $row) 
	{
		$batch_id_arr[] = $row[csf("batch_id")];
		$color_id_arr[] = $row[csf("color_id")];
		$salesOrderIds .= $row[csf('order_id')].",";

		$store_ids .= $row[csf('store_id')].",";
		$shelf_ids .= $row[csf('self')].",";
		$floor_ids .= $row[csf('floor_id')].",";
		$room_ids .= $row[csf('room')].",";
		$rack_ids .= $row[csf('rack')].",";

		if($recievedId!="")
		{
			$issue_ids .= $row[csf('booking_id')].",";
		}

		$company_id = $row[csf("comp_id")];
	}
	
	$salesOrderIds = implode(",", array_filter(array_unique(explode(",",chop($salesOrderIds,",")))));
	$store_ids = implode(",", array_filter(array_unique(explode(",",chop($store_ids,",")))));
	$shelf_ids = implode(",", array_filter(array_unique(explode(",",chop($shelf_ids,",")))));
	$floor_ids = implode(",", array_filter(array_unique(explode(",",chop($floor_ids,",")))));
	$room_ids = implode(",", array_filter(array_unique(explode(",",chop($room_ids,",")))));
	$rack_ids = implode(",", array_filter(array_unique(explode(",",chop($rack_ids,",")))));


	if($salesOrderIds!="")
	{
		$fso_sql = sql_select("select id,job_no_prefix_num,season,style_ref_no,po_company_id,company_id,po_buyer,buyer_id,within_group from fabric_sales_order_mst where status_active=1 and is_deleted=0 and id in($salesOrderIds)");
		$salesOrderData = array();
		foreach ($fso_sql as $row) {
			$salesOrderData[$row[csf('id')]]['po_buyer'] 				=  $row[csf('po_buyer')]; 
			$salesOrderData[$row[csf('id')]]['buyer_id'] 				=  $row[csf('buyer_id')]; 
			$salesOrderData[$row[csf('id')]]['within_group'] 			=  $row[csf('within_group')];
		}
	}

	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$data_array=sql_select($sql_deter);

	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
		}
	}

	if(!empty($batch_id_arr)){
		$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst where id in(".implode(",",$batch_id_arr).") and status_active=1 and is_deleted=0","id","batch_no");
	}

	$color_arr=array();
	if(!empty($color_id_arr)){
		$color_arr=return_library_array( "select id, color_name from lib_color where id in(".implode(",",$color_id_arr).") and status_active=1 and is_deleted=0",'id','color_name');
	}

	$floor_sql = sql_select("select a.company_id,b.store_id,b.floor_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.floor_id and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by a.company_id,b.store_id,b.floor_id,a.floor_room_rack_name order by a.floor_room_rack_name");

	foreach ($floor_sql as $val) 
	{
		$floor_ref_arr[$val[csf("company_id")]][$val[csf("store_id")]][$val[csf("floor_id")]] = $val[csf("floor_room_rack_name")];
	}

	$lib_room_arr=sql_select("select a.company_id,b.store_id,b.room_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.room_id and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by a.company_id,b.store_id,b.room_id,a.floor_room_rack_name order by a.floor_room_rack_name");
	foreach ($lib_room_arr as $val) 
	{
		$room_ref_arr[$val[csf("company_id")]][$val[csf("store_id")]][$val[csf("room_id")]] = $val[csf("floor_room_rack_name")];
	}

	$lib_rack_arr=sql_select("select a.company_id,b.store_id,b.rack_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.rack_id and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by a.company_id,b.store_id,b.rack_id,a.floor_room_rack_name order by a.floor_room_rack_name");
	foreach ($lib_rack_arr as $val) 
	{
		$rack_ref_arr[$val[csf("company_id")]][$val[csf("store_id")]][$val[csf("rack_id")]] = $val[csf("floor_room_rack_name")];
	}

	$lib_shelf_arr=sql_select("select a.company_id,b.store_id,b.shelf_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.shelf_id and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by a.company_id,b.store_id,b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name");
	foreach ($lib_shelf_arr as $val) 
	{
		$shelf_ref_arr[$val[csf("company_id")]][$val[csf("store_id")]][$val[csf("shelf_id")]] = $val[csf("floor_room_rack_name")];
	}

	?> 

        <table width="1740" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" >
            <thead>
                <th width="40">SL</th>
                <th width="80">Buyer</th>
                <th width="80">FSO NO</th>
                <th width="80">Booking No</th>
                <th width="80">Body Part</th>
                <th width="100">Fab. Description</th>
                <th width="80">Batch No</th>
                <th width="100">Color</th>
                <th width="80">Fabric Shade</th>
                <th width="80">UOM</th>
                <th width="80">Issued Qty</th>
                <th width="80">Rate</th>

                <th width="80">Rtn. Qty</th>
                <th width="80">Return Amount</th>
                <th width="80">Store Name</th>
                <th width="80">Floor</th>
                <th width="80">Room</th>
                <th width="80">Rack</th>
                <th width="80">Shelf</th>
                <th width="">Remarks</th>
            </thead>
        </table>

        <div style="width:1740px; overflow-y:scroll; max-height:350px;" id="scroll_body">
        <table width="1720" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" id="tbl_list_search">
        	<tbody>
        	<?php 
        	$i=1;
        	$buyerName = "";
        	foreach ($mainQueryResult as  $row) 
        	{	        		        			
    			if ($i%2==0)
				$bgcolor="#E9F3FF";
				else
				$bgcolor="#FFFFFF";

				if($salesOrderData[$row[csf('order_id')]]['within_group']==1)
				{
					$buyerId = $salesOrderData[$row[csf('order_id')]]['po_buyer'];
					$buyerName = $buyer_arr[$salesOrderData[$row[csf('order_id')]]['po_buyer']]; 
				}else{
					$buyerName = $buyer_arr[$salesOrderData[$row[csf('order_id')]]['buyer_id']];
					$buyerId = $salesOrderData[$row[csf('order_id')]]['buyer_id'];
				}


				$issue_qnty = $row[csf('issue_qnty')];
				
				$issueRate = $row[csf('cons_rate')];
				$issueRate = number_format($issueRate,2,".","");

				$issueOrderRate = $row[csf('order_rate')];
				
				$cons_amount =  number_format($row[csf('cons_amount')],2,".","");

				$company_id = $row[csf('comp_id')];

				$issueOrderAmount="";
				if($rcvReturnRefData[$row[csf("dtls_id")]]["issue_dtls_id"] =="")
                {
                	$issue_dtls_id = $row[csf('dtls_id')];
					$receive_qnty = "";
					$rcv_amount = "";
					$store_id = $row[csf('store_id')];
					$floor = $row[csf('floor_id')];
					$room = $row[csf('room')];
					$rack_no = $row[csf('rack')];
					$shelf_no =$row[csf('self')];
					$remarks = "";
					$trans_id="";
					$return_dtls_id="";
                }
                else
                {

                	$issue_dtls_id = $rcvReturnRefData[$row[csf("dtls_id")]]["issue_dtls_id"];
					$receive_qnty= $rcvReturnRefData[$row[csf("dtls_id")]]["receive_qnty"];
					$rcv_amount = $rcvReturnRefData[$row[csf("dtls_id")]]["rcv_amount"];

					$store_id = $rcvReturnRefData[$row[csf("dtls_id")]]["store_id"];
					$floor = $rcvReturnRefData[$row[csf("dtls_id")]]["floor"];
					$room =$rcvReturnRefData[$row[csf("dtls_id")]]["room"];
					$rack_no =$rcvReturnRefData[$row[csf("dtls_id")]]["rack_no"];
					$shelf_no =$rcvReturnRefData[$row[csf("dtls_id")]]["shelf_no"];
					$remarks = $rcvReturnRefData[$row[csf("dtls_id")]]["remarks"];

					$trans_id = $rcvReturnRefData[$row[csf("dtls_id")]]["trans_id"];
					$return_dtls_id = $rcvReturnRefData[$row[csf("dtls_id")]]["return_dtls_id"];

					$issueOrderAmount = $row[csf('order_rate')]*$receive_qnty;
					$issueOrderAmount = number_format($issueOrderAmount,2,".","");
                }

        		?>
		        <tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<?echo $i;?>">
		            <td width="40"><? echo $i; ?>
		            	<input type="hidden" name="hdden_data" id="hdden_data_<? echo $i;?>" 

		            	value="<? echo $row[csf('issue_id')]."**".$row[csf('order_id')]."**".$row[csf('booking_no')]."**".$row[csf('product_id')]."**".$row[csf('bodypart_id')]."**".$row[csf('determination_id')]."**".$row[csf('gsm')]."**".$row[csf('dia')]."**".$row[csf('batch_id')]."**".$row[csf('color_id')]."**".$row[csf('fabric_shade')]."**".$row[csf('cons_uom')]."**".$buyerId."**".$issue_qnty."**".$row[csf('store_id')]."**".$row[csf('floor_id')]."**".$row[csf('room')]."**".$row[csf('self')]."**".$row[csf('rack')]."**".$issueRate."**".$cons_amount."**".$row[csf('width_type')]."**".$row[csf('order_rate')]."**".$issue_dtls_id."**".$row[csf('aop_rate')];?>">
		            </td>
	                <td width="80"><? echo $buyerName;?></td>
	                <td width="80"><? echo $row[csf('fso_no')];?></td>
	                <td width="80"><? echo $row[csf('booking_no')];?></td>
	                <td width="80"><? echo $body_part[$row[csf('bodypart_id')]];?></td>
	                <td width="100"><? echo $composition_arr[$row[csf('determination_id')]];?></td>
	                <td width="80"><? echo $batch_arr[$row[csf('batch_id')]];?></td>
	                <td width="100"><? echo $color_arr[$row[csf('color_id')]];?></p></td>
	                <td width="80" align="center"><? echo $fabric_shade[$row[csf('fabric_shade')]];?></td>
	                <td width="80" align="center"><? echo $unit_of_measurement[$row[csf('uom')]];?></td>
	                <td width="80" align="right"><? echo number_format($issue_qnty,2);?></td>
	                <td width="80" align="right"><? echo number_format($row[csf('order_rate')],2,".","");//$row[csf('cons_rate')];?></td>

	                <td width="80">
	                	<input type="text" class="text_boxes_numeric" name="text_return_qnty[]" id="text_return_qnty_<? echo $i;?>" placeholder="Write" style="width: 68px;" value="<? echo $receive_qnty ?>" onKeyUp="calCulateAmount();" align="right">

	                	<input type="hidden" name="text_issue_qnty[]" id="text_issue_qnty<? echo $i;?>" value="<? echo $issue_qnty ; ?>">
	                	<input type="hidden" name="text_issue_rate[]" id="text_issue_rate<? echo $i;?>" value="<? echo $issueRate ; ?>">
	                	<input type="hidden" name="text_issue_order_rate[]" id="text_issue_order_rate<? echo $i;?>" value="<? echo $issueOrderRate ; ?>">

	                	<input type="hidden" name="hidden_transaction_id[]" id="hidden_transaction_id_<? echo $i;?>" value="<? echo $trans_id; ?>">
	                	<input type="hidden" name="hidden_dtls_id[]" id="hidden_dtls_id_<? echo $i;?>" value="<? echo $return_dtls_id; ?>">
	                	<input type="hidden" name="previous_rtn_qnty[]" id="previous_rtn_qnty_<? echo $i;?>" value="<? echo $receive_qnty; ?>">
	                	<input type="hidden" name="previous_rtn_amount[]" id="previous_rtn_amount_<? echo $i;?>" value="<? echo $rcv_amount; ?>">
	                	<input type="hidden" name="product_id[]" id="product_id_<? echo $i;?>" value="<? echo $row[csf('product_id')]; ?>">

	                	<input type="hidden" name="to_store[]" id="to_store_<? echo $i;?>" value="<? echo $store_id;?>">
	                	<input type="hidden" name="to_floor[]" id="to_floor_<? echo $i;?>" value="<? echo $floor;?>">
	                	<input type="hidden" name="to_room[]" id="to_room_<? echo $i;?>" value="<? echo $room;?>">
	                	<input type="hidden" name="to_rack[]" id="to_rack_<? echo $i;?>" value="<? echo $rack_no;?>">
	                	<input type="hidden" name="to_shelf[]" id="to_shelf_<? echo $i;?>" value="<? echo $shelf_no;?>">


	                </td>
	                <td width="80" align="right" id="text_issue_amooount<? echo $i;?>"><? echo $issueOrderAmount;?></td>


	                <td width="80" align="center"> 
					<? 
						$store_library=return_library_array( "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.category_type in(2) group by a.id, a.store_name order by a.store_name",'id','store_name');

						//echo create_drop_down( "to_store_$i", 80, $store_library,"",1, "--Select Store--", $store_id, "load_drop_down( 'requires/knit_finish_fabric_issue_return_controller',this.value+'_'+$company_id+'_'+$i, 'load_drop_floor', 'floor_td_$i');load_drop_down( 'requires/knit_finish_fabric_issue_return_controller',this.value+'_'+$company_id+'_'+$i+'_'+'', 'load_drop_room', 'room_td_$i');load_drop_down( 'requires/knit_finish_fabric_issue_return_controller',this.value+'_'+$company_id+'_'+$i+'_'+''+'', 'load_drop_rack', 'rack_td_$i');" );
					?>
					<p><? echo $store_library[$store_id];?></p>
					</td>

					<td width="80" id="floor_td_<? echo $i;?>" align="center">
						<p><? echo $floor_ref_arr[$company_id][$store_id][$floor];?></p>
						<? 
						//echo create_drop_down( "to_floor_$i", 80, $floor_ref_arr[$company_id][$store_id],"",1, "--Select Floor--", $floor, "load_drop_down( 'requires/knit_finish_fabric_issue_return_controller',".$store_id."+'_'+$company_id+'_'+$i+'_'+this.value, 'load_drop_room', 'room_td_$i');" );
						?>
					</td>


					<td width="80" id="room_td_<? echo $i;?>" align="center">
						
						<p><? echo $room_ref_arr[$company_id][$store_id][$room];?></p>
						<? //echo create_drop_down( "to_room_$i", 80, $room_ref_arr[$company_id][$store_id],"",1, "--Select Room--", $room, "load_drop_down( 'requires/knit_finish_fabric_issue_return_controller',".$store_id ."+'_'+$company_id+'_'+$i+'_'+".$floor. "+'_'+this.value, 'load_drop_rack', 'rack_td_$i');" );	?>
					</td>

					<td width="80" id="rack_td_<? echo $i;?>" align="center">
						<p><? echo $rack_ref_arr[$company_id][$store_id][$rack_no];?></p>
						<? //echo create_drop_down( "to_rack_$i", 80, $rack_ref_arr[$company_id][$store_id],"", 1, "--Select Rack--", $rack_no, "" ); ?>
					</td>

					<td width="80" id="shelf_td_<? echo $i;?>" align="center">
						<p><? echo $shelf_ref_arr[$company_id][$store_id][$shelf_no];?></p>
						<? //echo create_drop_down( "to_shelf_$i", 80, $shelf_ref_arr[$company_id][$store_id],"",1, "--Select Shelf--", $shelf_no, "" );?>
					</td>
					
	                <td width="">
	                	<p><input type="text" name="text_dtls_remarks[]" id="text_dtls_remarks_<? echo $i;?>" value="<? echo $remarks;?>" placeholder="write"></p>
	                </td>
			    </tr>
	        	
        		<?php 
		        		
	        	$i++;
    		}
        	?>
        	</tbody>
        </table>  
   	</div>
        
    <?
	exit;
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$fso_company_id = str_replace("'","",$fso_company_id); 
	$issue_rtn_date = date("d-M-Y", strtotime($issue_rtn_date));
	//echo "10**under development";die;

	$sql_garments_rcv=sql_select("select recv_number from inv_receive_master where entry_form=37 and receive_basis=10 and booking_id=$text_issue_id and status_active=1 and is_deleted=0");
	if(!empty($sql_garments_rcv))
	{
		echo "20**Receive by garments found.\nReceive No: ".$sql_garments_rcv[0][csf("recv_number")];
		die;
	}
	
	if($datas!="")
	{
		$detailsDataArr = explode(",", $datas);
		$sl=1;
		$product_Batch_FSO_Bodypart_Store_Widthtype_FabShade="";
		foreach ($detailsDataArr as $data_string) 
		{
			$dataArr = explode("**",$data_string);
			$batch_id_arr[$dataArr[8]]=$dataArr[8];

			$returnQnty = "return_qnty_".$sl;
			$to_store = "to_store_".$sl;
			$to_floor = "to_floor_".$sl;
			$to_room = "to_room_".$sl;
			$to_rack = "to_rack_".$sl;
			$to_shelf = "to_shelf_".$sl;

			$orderID = $dataArr[1];
			$productId = $dataArr[3];
			$bodyPartId =  $dataArr[4];
			$batchId = $dataArr[8];
			$fabricShade = $dataArr[10];
			$storeId = $dataArr[14];
			$width_type = $dataArr[21];


			if($$to_floor=="") $sql_floor_id = 0; else $sql_floor_id = $$to_floor;
			if($$to_room=="") $sql_room_id = 0; else $sql_room_id= $$to_room;
			if($$to_rack=="") $sql_rack_id = 0; else $sql_rack_id = $$to_rack;
			if($$to_shelf=="") $sql_shelf_id = 0; else $sql_shelf_id = $$to_shelf;


			$product_Batch_FSO_Bodypart_Store_Widthtype_FabShade = $productId."_".$batchId."_".$orderID."_".$bodyPartId."_".$storeId."_".$width_type."_".$fabricShade."_".$sql_floor_id."_".$sql_room_id."_".$sql_rack_id."_".$sql_shelf_id;

			$sys_total_issue_ret_data[$product_Batch_FSO_Bodypart_Store_Widthtype_FabShade]+=$$returnQnty;

			$sl++;
		}
	}

	$batch_id_arr = array_filter($batch_id_arr);

	//echo "10**<br><pre>";

	//print_r($sys_total_issue_ret_data);die;

	if(!empty($batch_id_arr))
	{
		if($db_type==0){
			$castingCond_order_id="cast(c.po_breakdown_id as CHAR(4000)) as order_id";
			$castingCond_to_order_id="cast(a.to_order_id as CHAR(4000)) as order_id";
			$booking_without_order_cond = " and (a.booking_without_order=0 or a.booking_without_order ='') ";
		}
		else{
			$castingCond_order_id="cast(c.po_breakdown_id as varchar2(4000)) as order_id";
			$castingCond_to_order_id="cast(a.to_order_id as varchar2(4000)) as order_id";
			$booking_without_order_cond = " and (a.booking_without_order=0 or a.booking_without_order is null) ";
		}

		$all_batch_ids = implode(",",$batch_id_arr);

		$sql = "SELECT x.store_id, x.prod_id, x.batch_id, x.order_id, x.body_part_id, x.dia_width_type, x.is_sales, x.uom, x.fabric_shade, x.floor, x.room, x.rack_no, x.shelf_no, sum(receive_qnty) as receive_qnty, sum(x.cons_quantity) cons_quantity 
		from 
		( 
			SELECT a.store_id, b.prod_id,b.batch_id, $castingCond_order_id,b.body_part_id, b.dia_width_type,b.is_sales,b.uom,b.fabric_shade, b.floor, b.room,b.rack_no,b.shelf_no, sum(c.quantity) as receive_qnty, sum(d.cons_quantity) as cons_quantity
			from inv_receive_master a,inv_transaction d,pro_finish_fabric_rcv_dtls b,order_wise_pro_details c 
			where a.id=b.mst_id and d.id=b.trans_id and b.id = c.dtls_id  $booking_without_order_cond   and b.batch_id in ($all_batch_ids) and a.company_id=$fso_company_id and a.item_category=2 and a.entry_form in (7,225) and c.entry_form in (7,225) and b.is_sales=1 and a.status_active=1 and b.status_active=1 and c.status_active=1 
			group by a.store_id, b.prod_id,b.batch_id,c.po_breakdown_id, b.body_part_id, b.dia_width_type,b.is_sales,b.uom,b.fabric_shade, b.floor, b.room,b.rack_no,b.shelf_no
		
			union all

			select b.to_store as store_id, b.from_prod_id as prod_id, b.to_batch_id as batch_id, $castingCond_to_order_id , b.body_part_id, b.dia_width_type, 1 as is_sales, b.uom, b.fabric_shade, b.to_floor_id as floor,b.to_room as room, b.to_rack as rack_no,b.to_shelf as shelf_no, sum(b.transfer_qnty) as receive_qnty, sum(d.cons_quantity) as cons_quantity
			from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction d 
			where a.id=b.mst_id and b.to_trans_id=d.id and a.entry_form in(230) and b.to_batch_id in ($all_batch_ids) and a.company_id = $fso_company_id and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0
			group by b.to_store, b.from_prod_id, b.to_batch_id, a.to_order_id, b.body_part_id, b.dia_width_type, b.uom, b.fabric_shade ,b.to_floor_id, b.to_room, b.to_rack,b.to_shelf 
		) x 
		group by x.store_id, x.prod_id, x.batch_id, x.order_id, x.body_part_id, x.dia_width_type, x.is_sales, x.uom, x.fabric_shade, x.floor, x.room, x.rack_no, x.shelf_no";


		$data_array=sql_select($sql);
		$sql_floor_id=$sql_room_id=$sql_rack_id=$sql_shelf_id=0;
		$product_Batch_FSO_Bodypart_Store_Widthtype_FabShade="";
		foreach ($data_array as $val) 
		{
			if($val[csf("floor")]=="") $sql_floor_id = 0; else $sql_floor_id = $val[csf("floor")];
			if($val[csf("room")]=="") $sql_room_id = 0; else $sql_room_id = $val[csf("room")];
			if($val[csf("rack_no")]=="") $sql_rack_id = 0; else $sql_rack_id = $val[csf("rack_no")];
			if($val[csf("shelf_no")]=="") $sql_shelf_id = 0; else $sql_shelf_id = $val[csf("shelf_no")];

			$product_Batch_FSO_Bodypart_Store_Widthtype_FabShade = $val[csf('prod_id')]."_".$val[csf('batch_id')]."_".$val[csf('order_id')]."_".$val[csf('body_part_id')]."_".$val[csf('store_id')]."_".$val[csf('dia_width_type')]."_".$val[csf('fabric_shade')]."_".$sql_floor_id."_".$sql_room_id."_".$sql_rack_id."_".$sql_shelf_id;

			$receive_qnty_arr[$product_Batch_FSO_Bodypart_Store_Widthtype_FabShade] += $val[csf('receive_qnty')];
		}


		$delivery_sql = sql_select("SELECT b.order_id, b.store_id, b.batch_id, b.body_part_id, b.prod_id, b.width_type, b.fabric_shade, b.uom, sum(b.issue_qnty) delivery_qnty, sum(e.cons_quantity) as cons_quantity, b.floor, b.room, b.rack_no, b.shelf_no, b.width_type as dia_width_type
		from inv_issue_master a, inv_finish_fabric_issue_dtls b, pro_batch_create_mst c,product_details_master d, inv_transaction e 
		where a.entry_form in (224,287) and a.id=b.mst_id and b.batch_id=c.id and b.prod_id=d.id and b.trans_id=e.id and a.status_active='1' and a.is_deleted='0' and b.batch_id in ($all_batch_ids) and b.status_active=1 and c.status_active=1 and d.status_active=1
		group by b.order_id, b.store_id, b.batch_id, b.body_part_id, b.prod_id, b.width_type, b.fabric_shade, b.uom, b.floor, b.room, b.rack_no, b.shelf_no");

		$sql_floor_id=$sql_room_id=$sql_rack_id=$sql_shelf_id=0;
		$product_Batch_FSO_Bodypart_Store_Widthtype_FabShade="";
		foreach ($delivery_sql as  $val)
		{
			if($val[csf("floor")]=="") $sql_floor_id = 0; else $sql_floor_id = $val[csf("floor")];
			if($val[csf("room")]=="") $sql_room_id = 0; else $sql_room_id = $val[csf("room")];
			if($val[csf("rack_no")]=="") $sql_rack_id = 0; else $sql_rack_id = $val[csf("rack_no")];
			if($val[csf("shelf_no")]=="") $sql_shelf_id = 0; else $sql_shelf_id = $val[csf("shelf_no")];

			$product_Batch_FSO_Bodypart_Store_Widthtype_FabShade = $val[csf('prod_id')]."_".$val[csf('batch_id')]."_".$val[csf('order_id')]."_".$val[csf('body_part_id')]."_".$val[csf('store_id')]."_".$val[csf('dia_width_type')]."_".$val[csf('fabric_shade')]."_".$sql_floor_id."_".$sql_room_id."_".$sql_rack_id."_".$sql_shelf_id;

			$delivery_arr[$product_Batch_FSO_Bodypart_Store_Widthtype_FabShade] += $val[csf("delivery_qnty")];
		}


		$trans_out_sql = "SELECT b.batch_id, a.from_order_id,  b.body_part_id, b.from_store, b.from_prod_id, b.dia_width_type, b.fabric_shade, b.uom, b.floor_id, b.room, b.rack, b.shelf, sum(b.transfer_qnty) as quantity, sum(c.cons_quantity) as cons_quantity
		FROM inv_item_transfer_mst a, inv_item_transfer_dtls b ,inv_transaction c 
		WHERE a.id=b.mst_id  and b.to_trans_id=c.id and a.entry_form =230 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and b.batch_id in ($all_batch_ids)
		group by a.from_order_id, b.from_store, b.body_part_id, b.from_prod_id, b.dia_width_type, b.fabric_shade, b.uom, b.floor_id, b.room, b.rack, b.shelf";

		$trans_out_Data = sql_select($trans_out_sql);
		$sql_floor_id=$sql_room_id=$sql_rack_id=$sql_shelf_id=0;
		$product_Batch_FSO_Bodypart_Store_Widthtype_FabShade="";
		foreach ($trans_out_Data as $val) 
		{
			if($val[csf("floor_id")]=="") $sql_floor_id = 0; else $sql_floor_id = $val[csf("floor_id")];
			if($val[csf("room")]=="") $sql_room_id = 0; else $sql_room_id = $val[csf("room")];
			if($val[csf("rack")]=="") $sql_rack_id = 0; else $sql_rack_id = $val[csf("rack")];
			if($val[csf("shelf")]=="") $sql_shelf_id = 0; else $sql_shelf_id = $val[csf("shelf")];

			$product_Batch_FSO_Bodypart_Store_Widthtype_FabShade = $val[csf('from_prod_id')]."_".$val[csf('batch_id')]."_".$val[csf('from_order_id')]."_".$val[csf('body_part_id')]."_".$val[csf('from_store')]."_".$val[csf('dia_width_type')]."_".$val[csf('fabric_shade')]."_".$sql_floor_id."_".$sql_room_id."_".$sql_rack_id."_".$sql_shelf_id;

			$trans_out_arr[$product_Batch_FSO_Bodypart_Store_Widthtype_FabShade] += $val[csf("quantity")];
			
		}

		/* if(str_replace("'","",$update_id) != "")
		{
			$up_stock_cond = " and a.id !=".$update_id;
		} */
		$issue_return_sql = sql_select("select a.id, b.prod_id, b.batch_id, e.po_breakdown_id, b.body_part_id, d.store_id, b.dia_width_type, b.fabric_shade, b.room, b.floor,b.rack_no, b.shelf_no, sum(e.quantity) as qnty, sum(d.cons_quantity) as cons_quantity
		from inv_receive_master a, pro_finish_fabric_rcv_dtls b, inv_transaction d , order_wise_pro_details e where a.id=b.mst_id  and b.mst_id=d.mst_id and b.trans_id = d.id and d.id = e.trans_id and b.id = e.dtls_id  and d.item_category=2 and d.transaction_type in (4) and a.entry_form in (233) and e.entry_form = 233 and b.batch_id in ($all_batch_ids) and e.is_sales=1 $up_stock_cond group by a.id, b.prod_id, b.batch_id, e.po_breakdown_id, b.body_part_id, d.store_id, b.dia_width_type, b.fabric_shade, b.room,b.floor,b.rack_no, b.shelf_no");

		$sql_floor_id=$sql_room_id=$sql_rack_id=$sql_shelf_id=0;
		foreach ($issue_return_sql as $val) 
		{
			if($val[csf("floor")]=="") $sql_floor_id = 0; else $sql_floor_id = $val[csf("floor")];
			if($val[csf("room")]=="") $sql_room_id = 0; else $sql_room_id = $val[csf("room")];
			if($val[csf("rack_no")]=="") $sql_rack_id = 0; else $sql_rack_id = $val[csf("rack_no")];
			if($val[csf("shelf_no")]=="") $sql_shelf_id = 0; else $sql_shelf_id = $val[csf("shelf_no")];

			$product_Batch_FSO_Bodypart_Store_Widthtype_FabShade = $val[csf('prod_id')]."_".$val[csf('batch_id')]."_".$val[csf('po_breakdown_id')]."_".$val[csf('body_part_id')]."_".$val[csf('store_id')]."_".$val[csf('dia_width_type')]."_".$val[csf('fabric_shade')]."_".$sql_floor_id."_".$sql_room_id."_".$sql_rack_id."_".$sql_shelf_id;

			$issue_return_qnty_arr[$product_Batch_FSO_Bodypart_Store_Widthtype_FabShade]+= $val[csf("qnty")];

			if(str_replace("'","",$update_id) == $val[csf("id")])
			{
				$this_challan_issue_return_qnty_arr[$product_Batch_FSO_Bodypart_Store_Widthtype_FabShade]+= $val[csf("qnty")];
			}
		}
	}
	else
	{
		echo "10**Data not found";
		die;
	}

	//echo "10**";
	foreach ($sys_total_issue_ret_data as $key=>$val) 
	{
		$receive_qnty = $receive_qnty_arr[$key];
		$delivery_qnty = $delivery_arr[$key];
		$trans_out_qnty = $trans_out_arr[$key];
		$issue_return_qnty = $issue_return_qnty_arr[$key];

		$stock_qnty  = ($receive_qnty + $issue_return_qnty ) - ($delivery_qnty + $trans_out_qnty);
		$stock_qnty = number_format($stock_qnty,2,'.','');

		//echo "($receive_qnty + $issue_return_qnty ) - ($delivery_qnty + $trans_out_qnty) = $stock_qnty >> $val , $this_challan_issue_return_qnty_arr[$key] <<<br><br>";

		if($stock_qnty >= 0)
		{
			//increase from previous entry
			if($val > $this_challan_issue_return_qnty_arr[$key])
			{
				if( $stock_qnty < ($val - $this_challan_issue_return_qnty_arr[$key]) )
				{
					echo "20**Can not increase, Stock Not Available";
					die;
				}
			}
			else if($this_challan_issue_return_qnty_arr[$key] > $val)
			{
				if($stock_qnty < ($this_challan_issue_return_qnty_arr[$key] - $val))
				{
					echo "20**Can not decrese, Stock Not Available";
					die;
				}
			}
		}
		else
		{
			echo "Stock Not Available";
			die;
		}
	}

	if ($operation==0)  // Insert Here 
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		if(str_replace("'","",$text_issue_id)!="")
		{
			$sql_issue=sql_select("select knit_dye_source,knit_dye_company from inv_issue_master where id=$text_issue_id and status_active=1 and is_deleted=0");
			$knit_dye_source=$sql_issue[0][csf("knit_dye_source")];
			$knit_dye_company=$sql_issue[0][csf("knit_dye_company")];
		}
		//echo "10**$knit_dye_company";die;
		$finish_recv_num=''; $finish_update_id='';
		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";

			$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);
			$new_finish_recv_system_id = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master",$con,1,$fso_company_id,'FFPE',233,date("Y",time()),2 ));

			$field_array="id, recv_number_prefix, recv_number_prefix_num, recv_number, entry_form, item_category, company_id, receive_date,booking_id,booking_no,knitting_source,knitting_company,inserted_by, insert_date";

			$data_array="(".$id.",'".$new_finish_recv_system_id[1]."',".$new_finish_recv_system_id[2].",'".$new_finish_recv_system_id[0]."',233,2,".$fso_company_id.",'".$issue_rtn_date."','".$text_issue_id."','".$txt_issue_no."',".$knit_dye_source.",".$knit_dye_company.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			$finish_issue_rtn_id =$id; 
			

			$field_array_trans="id, mst_id,company_id,item_category, transaction_type,store_id,floor_id,room,self,rack,pi_wo_batch_no,batch_id,prod_id,order_id,order_uom,cons_uom,cons_quantity,cons_rate,cons_amount,order_qnty,order_rate,order_amount,transaction_date, inserted_by, insert_date";

	
			$field_details_array = "id, mst_id, trans_id, prod_id, batch_id, body_part_id, fabric_description_id, gsm, width, color_id,order_id, buyer_id, uom, fabric_shade,receive_qnty,is_sales,floor,room,rack_no,shelf_no,dia_width_type,issue_dtls_id,remarks,inserted_by, insert_date,aop_rate,aop_amount";

			$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, is_sales,inserted_by, insert_date";

			$field_array_prod_update="current_stock*avg_rate_per_unit*stock_value*updated_by*update_date";

			if($datas!="")
			{
				$detailsDataArr = explode(",", $datas);

				

				$data_array_dtls = "";$data_array_prop = ""; $data_array_trans= "";
				$k=1;
				foreach ($detailsDataArr as $data_string) 
				{
					$dataArr = explode("**",$data_string);

					$detailsremarks = "details_remarks_".$k; 
					$returnQnty = "return_qnty_".$k;
					$to_store = "to_store_".$k;
					$to_floor = "to_floor_".$k;
					$to_room = "to_room_".$k;
					$to_rack = "to_rack_".$k;
					$to_shelf = "to_shelf_".$k;


					if(str_replace("'","",$$returnQnty)*1 > 0)
					{
						$dtls_id = return_next_id_by_sequence("PRO_FIN_FAB_RCV_DTLS_PK_SEQ", "pro_finish_fabric_rcv_dtls", $con);
						$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
						$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);

						$orderID = $dataArr[1];
						$bookingNo = $dataArr[2];
						$productId = $dataArr[3];
						$bodyPartId =  $dataArr[4];
						$detarminationId = $dataArr[5];
						$gsm = $dataArr[6];
						$dia = $dataArr[7];
						$batchId = $dataArr[8];
						$colorId = $dataArr[9];
						$fabricShade = $dataArr[10];
						$uom = $dataArr[11];
						$buyerId = $dataArr[12];
						$returnQty = $dataArr[13];
						$storeId = $dataArr[14];
						$floorId = $dataArr[15];

						$room = $dataArr[16];
						$self = $dataArr[17];
						$rack = $dataArr[18];
						$consRate = $dataArr[19];
						$consAmount = $dataArr[20];
						$width_type = $dataArr[21];
						$order_rate = $dataArr[22];
						$issue_dtls_id = $dataArr[23];
						$aop_rate = $dataArr[24];

						$currentAmount = ($$returnQnty*$consRate);
						$currentAmount = number_format($currentAmount,2,".","");

						$orderAmount = ($$returnQnty*$order_rate);
						$orderAmount = number_format($orderAmount,2,".","");


						$aop_amount = ($$returnQnty*$aop_rate);
						$aop_amount = number_format($aop_amount,4,".","");

						if($data_array_trans!="") $data_array_trans.= ",";
						$data_array_trans .="(".$id_trans.",".$finish_issue_rtn_id.",".$fso_company_id.",2,4,".$$to_store.",".$$to_floor.",".$$to_room.",".$$to_shelf.",".$$to_rack.",".$batchId.",".$batchId.",".$productId.",".$orderID.",".$uom.",".$uom.",".$$returnQnty.",'".$consRate."',".$currentAmount.",".$$returnQnty.",'".$order_rate."','".$orderAmount."','".$issue_rtn_date."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time. "')";

						if($data_array_dtls!="") $data_array_dtls.= ",";
						$data_array_dtls .= "(" . $dtls_id . ",". $finish_issue_rtn_id .",". $id_trans .",". $productId .",". $batchId .",'". $bodyPartId ."','". $detarminationId ."','". $gsm ."','". $dia ."','". $colorId ."','". $orderID ."','". $buyerId ."','". $uom ."','" . $fabricShade . "','" . $$returnQnty . "',1,'" .$$to_floor."','". $$to_room . "','" . $$to_rack . "','" . $$to_shelf . "','".$width_type."',".$issue_dtls_id.",'".$$detailsremarks."'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time ."','".$aop_rate."','".$aop_amount."')";
						
						if($data_array_prop!="") $data_array_prop.= ",";
						$data_array_prop.="(".$id_prop.",".$id_trans.",4,233,".$dtls_id.",'".$orderID."','".$productId."','".$colorId."','".$$returnQnty."',1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";


						$product_id_arr[$productId]=$productId;
						$return_qnty_arr[$productId] += $$returnQnty;
						$return_amount_arr[$productId] += $currentAmount;

						
					}
					$k++;

				}
			}

			$finish_recv_num=$new_finish_recv_system_id[0];
			$finish_update_id=$id;			
		}

		//echo "10**".$data_array_trans;die;
		$product_id_arr = array_filter($product_id_arr);
		if(count($product_id_arr) > 0)
		{
			$stockData=sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id in (".implode(',', $product_id_arr).")");
			foreach ($stockData as $row) 
			{
				$stock= $row[csf('current_stock')];
				$avg_rate=$row[csf('avg_rate_per_unit')];
				$stock_value=$row[csf('stock_value')];

				$cur_st_qnty=$stock+$return_qnty_arr[$row[csf('id')]];
				$cur_st_value=$stock_value+$return_amount_arr[$row[csf('id')]];

				if($cur_st_qnty >0){
					$cur_st_rate=$cur_st_value/$cur_st_qnty;
				}else{
					$cur_st_rate=0;
				}

				if ($cur_st_qnty<=0) 
				{
					$cur_st_value=0;
				}

				$cur_st_rate = number_format($cur_st_rate,2,".","");
				$cur_st_value = number_format($cur_st_value,2,".","");
				
				$data_array_prod_update[$row[csf('id')]]=explode("*",("'".$cur_st_qnty."'*'".$cur_st_rate."'*'".$cur_st_value."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

				$update_prodId_arr[] = $row[csf('id')];
			}
		}

		if(str_replace("'","",$update_id)=="")
		{
			//echo "5**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;

			$rID=sql_insert("inv_receive_master",$field_array,$data_array,0);
			$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
			$rID3=sql_insert("pro_finish_fabric_rcv_dtls",$field_details_array,$data_array_dtls,0);
			if($data_array_prop!="")
			{
				$rID4=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
			}

			if(count($data_array_prod_update)>0)
			{
				//echo "10**".bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $update_prodId_arr ); die();
				$productUpdate=execute_query(bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $update_prodId_arr ));
			}

		}

		//echo "10**".$rID."**".$rID2."**".$rID3."**".$rID4."**".$productUpdate; die;
		//echo "10**".$flag;die;
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID4 && $productUpdate)
			{
				mysql_query("COMMIT");  
				echo "0**".$finish_update_id."**".$finish_recv_num."**".$id_trans."**".$dtls_id."**".$id_prop."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**"."&nbsp;"."**0**$list_view_type";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3 && $rID4 && $productUpdate)
			{
				oci_commit($con); 
				echo "0**".$finish_update_id."**".$finish_recv_num."**".$id_trans."**".$dtls_id."**".$id_prop."**0";
			}
			else
			{
				oci_rollback($con);
				echo "5**0**"."&nbsp;"."**0**$list_view_type";
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		if(str_replace("'","",$text_issue_id)!="")
		{
			$sql_issue=sql_select("select knit_dye_source,knit_dye_company from inv_issue_master where id=$text_issue_id and status_active=1 and is_deleted=0");
			$knit_dye_source=$sql_issue[0][csf("knit_dye_source")];
			$knit_dye_company=$sql_issue[0][csf("knit_dye_company")];
		}

		$field_array_update="receive_date*knitting_source*knitting_company*updated_by*update_date";

		$data_array_update = "'".$issue_rtn_date."'*".$knit_dye_source."*".$knit_dye_company."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$updateid_finish=str_replace("'","",$update_id);
		$finish_recv_num=str_replace("'","",$txt_system_no);
		
		$up_received_dtls_id=str_replace("'","",$received_dtls_id);
		$up_trans_id=str_replace("'","",$trans_id);
		$up_proportion_id=str_replace("'","",$proportion_id);
		$product_ids=str_replace("'","",$product_ids);

		$field_array_trans="id, mst_id,company_id,item_category, transaction_type,store_id,floor_id,room,self,rack,pi_wo_batch_no,batch_id,prod_id,order_id,order_uom,cons_uom,cons_quantity,cons_rate,cons_amount,order_qnty,order_rate,order_amount,transaction_date, inserted_by, insert_date";
		$field_details_array = "id, mst_id, trans_id, prod_id, batch_id, body_part_id, fabric_description_id, gsm, width, color_id,order_id, buyer_id, uom, fabric_shade,receive_qnty,is_sales,floor,room,rack_no,shelf_no,dia_width_type, issue_dtls_id,remarks,inserted_by, insert_date, aop_rate, aop_amount";
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, is_sales,inserted_by, insert_date";

		$field_array_trans_update="cons_quantity*cons_rate*cons_amount*order_qnty*order_rate*order_amount*transaction_date*store_id*floor_id*room*self*rack*updated_by*update_date";
		$field_details_array_update = "receive_qnty*floor*room*rack_no*shelf_no*remarks*updated_by*update_date*aop_rate*aop_amount";
		$field_array_prod_update="current_stock*avg_rate_per_unit*stock_value*updated_by*update_date";

		if($updateid_finish == "")
		{
			echo "10**Update Id Not Found";
			disconnect($con);die;
		}

		if($datas!="")
		{
			$detailsDataArr = explode(",", $datas);

			$product_ids  = implode(",",array_filter(array_unique(explode(",", $product_ids))));

			if($product_ids == "")
			{
				echo "10**Product Not Found.";disconnect($con);die;
			}
			$finish_issue_rtn_id =$updateid_finish; 

			$data_array_dtls = "";$data_array_trans="";$data_array_prop="";
			$k=1;
			foreach ($detailsDataArr as $data_string) 
			{
				
				$dataArr = explode("**",$data_string);

				$orderID = $dataArr[1];
				$bookingNo = $dataArr[2];
				$productId = $dataArr[3];
				$bodyPartId =  $dataArr[4];
				$detarminationId = $dataArr[5];
				$gsm = $dataArr[6];
				$dia = $dataArr[7];
				$batchId = $dataArr[8];
				$colorId = $dataArr[9];
				$fabricShade = $dataArr[10];
				$uom = $dataArr[11];
				$buyerId = $dataArr[12];
				$returnQty = $dataArr[13];
				$storeId = $dataArr[14];
				$floorId = $dataArr[15];

				$room = $dataArr[16];
				$self = $dataArr[17];
				$rack = $dataArr[18];
				$consRate = $dataArr[19];
				$consAmount = $dataArr[20];
				$width_type = $dataArr[21];
				$orderRate = $dataArr[22];
				$issue_dtls_id = $dataArr[23];
				$aop_rate = $dataArr[24];

				$detailsremarks = "details_remarks_".$k; 
				$returnQnty = "return_qnty_".$k;
				$to_store = "to_store_".$k;
				$to_floor = "to_floor_".$k;
				$to_room = "to_room_".$k;
				$to_rack = "to_rack_".$k;
				$to_shelf = "to_shelf_".$k;
				$hidden_transaction_id = "hidden_transaction_id_".$k;
				$hidden_dtls_id = "hidden_dtls_id_".$k;
				$txt_previousQnty = "previous_rtn_qnty_".$k;
				$txt_previousAmount = "previous_rtn_amount_".$k;


				if(str_replace("'","",$$hidden_dtls_id) != "")
				{


					$update_dtls_ids .=str_replace("'","",$$hidden_dtls_id).",";

					$currentAmount = ($$returnQnty*$consRate);
					$currentAmount = number_format($currentAmount,2,".","");

					$orderAmount = ($$returnQnty*$orderRate);
					$orderAmount = number_format($orderAmount,2,".","");

					$previous_qnty = str_replace("'","",$$txt_previousQnty)*1;
					$previous_amount = str_replace("'","",$$txt_previousAmount)*1;

					$aop_amount = ($$returnQnty*$aop_rate);
					$aop_amount = number_format($aop_amount,4,".","");
					

					//$field_array_trans_update="cons_quantity*cons_rate*cons_amount*order_qnty*order_rate*order_amount*transaction_date*store_id*floor_id*room*self*rack*updated_by*update_date";
					$up_trans_id_arr[]=str_replace("'","",$$hidden_transaction_id);
					$data_array_trans_update[str_replace("'","",$$hidden_transaction_id)] = explode("*",($$returnQnty."*'".$consRate."'*'".$currentAmount."'*'".$$returnQnty."'*'".$orderRate."'*'".$orderAmount."'*'".$issue_rtn_date."'*".$$to_store."*".$$to_floor."*".$$to_room."*".$$to_shelf."*".$$to_rack."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));


					//$field_details_array_update = "receive_qnty*floor*room*rack_no*shelf_no*remarks*updated_by*update_date";
					$up_dtlsId_arr[]=str_replace("'","",$$hidden_dtls_id);
					$data_array_dtls_update[str_replace("'","",$$hidden_dtls_id)] = explode("*",($$returnQnty."*".$$to_floor."*".$$to_room."*".$$to_rack."*".$$to_shelf."*'".$$detailsremarks."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'".$aop_rate."'*'".$aop_amount."'"));

					$dtlsId_prop=$$hidden_dtls_id;
					$transId_prop=$$hidden_transaction_id;

					$product_id_arr[$productId]=$productId;
					$return_qnty_arr[$productId] += $$returnQnty;
					$return_amount_arr[$productId] += $currentAmount;

					$previous_qnty_arr[$productId] += $previous_qnty;
					$previous_amount_arr[$productId] += $previous_amount;

				}
				else
				{
					if(str_replace("'","",$$returnQnty)*1 > 0)
					{
						$currentAmount = ($$returnQnty*$consRate);
						$currentAmount = number_format($currentAmount,2,".","");

						$orderAmount = ($$returnQnty*$order_rate);
						$orderAmount = number_format($orderAmount,2,".","");

						$aop_amount = ($$returnQnty*$aop_rate);
						$aop_amount = number_format($aop_amount,4,".","");

						$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
						if($data_array_trans!="") $data_array_trans.= ",";
						$data_array_trans .="(".$id_trans.",".$finish_issue_rtn_id.",".$fso_company_id.",2,4,".$storeId.",".$$to_floor.",".$$to_room.",".$$to_shelf.",".$$to_rack.",".$batchId.",".$batchId.",".$productId.",".$orderID.",".$uom.",".$uom.",".$$returnQnty.",'".$consRate."',".$currentAmount.",".$$returnQnty.",'".$order_rate."','".$orderAmount."','".$issue_rtn_date."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

						$dtls_id = return_next_id_by_sequence("PRO_FIN_FAB_RCV_DTLS_PK_SEQ", "pro_finish_fabric_rcv_dtls", $con);
						if($data_array_dtls!="") $data_array_dtls.= ",";
						$data_array_dtls .= "(" . $dtls_id . ",". $finish_issue_rtn_id .",". $id_trans .",". $productId .",". $batchId .",'". $bodyPartId ."','". $detarminationId ."','". $gsm ."','". $dia ."','". $colorId ."','". $orderID ."','". $buyerId ."','". $uom ."','" . $fabricShade . "','" . $$returnQnty . "',1,'".$$to_floor."','". $$to_room . "','" . $$to_rack . "','" . $$to_shelf . "','".$width_type."',".$issue_dtls_id.",'".$$detailsremarks."'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time ."','" .$aop_rate ."','" .$aop_amount . "')";

						$dtlsId_prop= $dtls_id;
						$transId_prop= $id_trans;


						$product_id_arr[$productId]=$productId;
						$return_qnty_arr[$productId] += $$returnQnty;
						$return_amount_arr[$productId] += $currentAmount;
					}
					
				}

				if(str_replace("'","",$$returnQnty)*1 > 0)
				{
					$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
					if($data_array_prop!="") $data_array_prop.= ",";
					$data_array_prop.="(".$id_prop.",".$transId_prop.",4,233,".$dtlsId_prop.",'".$orderID."','".$productId."','".$colorId."','".$$returnQnty."',1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				}

				$k++;
			}
		}


		$rID=sql_update("inv_receive_master",$field_array_update,$data_array_update,"id",$updateid_finish,0);
		if($rID) $flag=1; else $flag=0;

		$rID2=$rID3=$rID4=$rID5=$rID6=$rID7=true;
		if(count($data_array_trans_update) > 0)
		{
			//echo "10**".bulk_update_sql_statement("inv_transaction","id",$field_array_trans_update,$data_array_trans_update,$up_trans_id_arr);die;
			$rID2=execute_query(bulk_update_sql_statement("inv_transaction","id",$field_array_trans_update,$data_array_trans_update,$up_trans_id_arr));
			if($flag==1) 
			{
				if($rID2) $flag=1; else $flag=0; 
			}
			$rID3=execute_query(bulk_update_sql_statement("pro_finish_fabric_rcv_dtls","id",$field_details_array_update,$data_array_dtls_update,$up_dtlsId_arr));

			if($flag==1) 
			{
				if($rID3) $flag=1; else $flag=0; 
			}
		}

		if($data_array_trans != "")
		{
			//echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;

			$rID4=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
			if($flag==1) 
			{
				if($rID4) $flag=1; else $flag=0; 
			}
		}

		if($data_array_dtls != "")
		{
			$rID5=sql_insert("pro_finish_fabric_rcv_dtls",$field_details_array,$data_array_dtls,0);
			if($flag==1) 
			{
				if($rID5) $flag=1; else $flag=0; 
			}
		}

		$update_dtls_ids=chop($update_dtls_ids,',');
		if($update_dtls_ids!="")
		{
			$rID6 = execute_query("DELETE FROM order_wise_pro_details WHERE dtls_id in(".$update_dtls_ids.") and entry_form=233");
			if($flag==1) 
			{
				if($rID6) $flag=1; else $flag=0; 
			}
		}

		if($data_array_prop!="")
		{
			$rID7=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
			if($flag==1) 
			{
				if($rID7) $flag=1; else $flag=0; 
			}
		}


		$product_id_arr = array_filter($product_id_arr);
		if(count($product_id_arr) > 0)
		{
			$stockData=sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id in (".implode(',', $product_id_arr).")");
			foreach ($stockData as $row) 
			{
				$stock= $row[csf('current_stock')];
				$avg_rate=$row[csf('avg_rate_per_unit')];
				$stock_value=$row[csf('stock_value')];

				$previousQnty = $previous_qnty_arr[$row[csf('id')]];
				$previousAmount = $previous_amount_arr[$row[csf('id')]];


				$cur_st_qnty = $stock - ($previousQnty  -  $return_qnty_arr[$row[csf('id')]]);
				$cur_st_value = $stock_value - ($previousAmount - $return_amount_arr[$row[csf('id')]]);

				$cur_st_qnty = number_format($cur_st_qnty,2,".","");
				
				if ($cur_st_qnty>0) {
					$cur_st_rate=$cur_st_value/$cur_st_qnty;
				}
				else{
					$cur_st_rate=0;
				}

				if ($cur_st_qnty<=0) 
				{
					$cur_st_value=0;
				}
				

				$cur_st_rate = number_format($cur_st_rate,2,".","");
				$cur_st_value = number_format($cur_st_value,2,".","");
				
				$data_array_prod_update[$row[csf('id')]]=explode("*",("'".$cur_st_qnty."'*'".$cur_st_rate."'*'".$cur_st_value."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

				$update_prodId_arr[] = $row[csf('id')];
			}
			
		}

		if(count($data_array_prod_update)>0)
		{
			//echo "10**".bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $update_prodId_arr ); die();
			$productUpdate=execute_query(bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $update_prodId_arr ));
		}

		//echo "10**$rID**$rID2**$rID3**$rID4**$rID5**$rID6**$rID7**$productUpdate";
		//oci_rollback($con);
		//die; 
		
		if($db_type==0)
		{
			if( $rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $rID7 && $productUpdate )
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $updateid_finish)."**".str_replace("'", '', $finish_recv_num)."**".$up_trans_id."**".$up_received_dtls_id."**".$up_proportion_id."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**0**0**1**$list_view_type";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if( $rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $rID7 && $productUpdate )
			{
				oci_commit($con);
				echo "1**".str_replace("'", '', $updateid_finish)."**".str_replace("'", '', $finish_recv_num)."**".$up_trans_id."**".$up_received_dtls_id."**".$up_proportion_id."**0";
			}
			else
			{
				oci_rollback($con);
				echo "6**0**0**1**$list_view_type";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2) // Delete Here----------------------------------------------------------
	{
		$con = connect(); 
		if($db_type==0)	{ mysql_query("BEGIN"); }

		$updateid_finish=str_replace("'","",$update_id);
		$finish_recv_num=str_replace("'","",$txt_system_no);
		$up_trans_id=str_replace("'","",$trans_id);
		$up_received_dtls_id=str_replace("'","",$received_dtls_id);
		$up_proportion_id=str_replace("'","",$proportion_id);
		$product_ids=str_replace("'","",$product_ids);

		if($updateid_finish == "")
		{
			echo "20**Update Id Not Found";
			disconnect($con);die;
		}
		$field_array_trans_update="updated_by*update_date*status_active*is_deleted";
		$field_details_array_update="updated_by*update_date*status_active*is_deleted";
		$field_array_prop_update="updated_by*update_date*status_active*is_deleted";
		$field_array_prod_update="current_stock*avg_rate_per_unit*stock_value*updated_by*update_date";

		if($datas!="")
		{
			$detailsDataArr = explode(",", $datas);
			$product_ids  = implode(",",array_filter(array_unique(explode(",", $product_ids))));

			$sql = sql_select( "SELECT a.id as product_id, b.id as trans_id, a.current_stock, b.cons_quantity, b.store_id, b.cons_amount from product_details_master a, inv_transaction b, inv_receive_master c where a.id=b.prod_id and b.mst_id=c.id and b.mst_id=$updateid_finish and c.id=$updateid_finish and c.entry_form=233 and a.item_category_id=2 and b.item_category=2 and b.transaction_type=4" );
			if (empty($sql)) 
			{
				echo "20**Delete Failed";
				disconnect($con);die;
			}
			foreach($sql as $result)
			{
				$product_cons_quantity_arr[$result[csf("product_id")]]+=$result[csf("cons_quantity")];
				$product_current_stock_arr[$result[csf("product_id")]]+=$result[csf("current_stock")];
				$product_cons_amount_arr[$result[csf("product_id")]]+=$result[csf("cons_amount")];
				$before_prod_id 	.= $result[csf("product_id")].',';
				$before_store_id	.= $result[csf("store_id")].',';
				$before_trans_id 	.= $result[csf("trans_id")].',';
			}
			// echo "10**<pre>"; print_r($product_qty_arr);die;
			$before_prod_id=chop($before_prod_id,',');
			$before_trans_id=chop($before_trans_id,',');

			$max_trans_query = sql_select("SELECT max(id) as max_id from inv_transaction where prod_id in($before_prod_id) and store_id in (".chop($before_store_id,',').") and item_category=2 and status_active=1");
			$max_trans_id = $max_trans_query[0][csf('max_id')];

			if($product_ids == "")
			{
				echo "20**Product Not Found.";disconnect($con);die;
			}
			$finish_issue_rtn_id =$updateid_finish; 

			$data_array_dtls = "";$data_array_trans="";$data_array_prop="";
			$k=1;
			foreach ($detailsDataArr as $data_string) 
			{				
				$dataArr = explode("**",$data_string);

				$productId = $dataArr[3];
				$returnQty = $dataArr[13];

				$consRate = $dataArr[19];
				$consAmount = $dataArr[20];
				$orderRate = $dataArr[22];

				$returnQnty = "return_qnty_".$k;
				$hidden_transaction_id = "hidden_transaction_id_".$k;
				$hidden_dtls_id = "hidden_dtls_id_".$k;
				$txt_previousQnty = "previous_rtn_qnty_".$k;
				$txt_previousAmount = "previous_rtn_amount_".$k;

				if($max_trans_id > $$hidden_transaction_id)
				{
					echo "20**Next transaction found of this store and product. delete not allowed.";
					disconnect($con);die;
				}

				if(str_replace("'","",$$hidden_dtls_id) != "")
				{
					$update_dtls_ids .=str_replace("'","",$$hidden_dtls_id).",";

					$currentAmount = ($$returnQnty*$consRate);
					$currentAmount = number_format($currentAmount,2,".","");

					$orderAmount = ($$returnQnty*$orderRate);
					$orderAmount = number_format($orderAmount,2,".","");

					$previous_qnty = str_replace("'","",$$txt_previousQnty)*1;
					$previous_amount = str_replace("'","",$$txt_previousAmount)*1;


					$up_trans_id_arr[]=str_replace("'","",$$hidden_transaction_id);
					$data_array_trans_update[str_replace("'","",$$hidden_transaction_id)] = explode("*",("'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*0*1"));

					$up_dtlsId_arr[]=str_replace("'","",$$hidden_dtls_id);
					$data_array_dtls_update[str_replace("'","",$$hidden_dtls_id)] = explode("*",("'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*0*1"));

					$data_array_prop_update[str_replace("'","",$$hidden_dtls_id)] = explode("*",("'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*0*1"));

					$dtlsId_prop=$$hidden_dtls_id;
					$transId_prop=$$hidden_transaction_id;

					$product_id_arr[$productId]=$productId;
					$return_qnty_arr[$productId] += $$returnQnty;
					$return_amount_arr[$productId] += $currentAmount;

					$previous_qnty_arr[$productId] += $previous_qnty;
					$previous_amount_arr[$productId] += $previous_amount;

				}
				$k++;
			}
		}
		// echo "10**<pre>";print_r($previous_qnty_arr);die;
		$product_id_arr = array_filter($product_id_arr);
		if(count($product_id_arr) > 0)
		{
			$stockData=sql_select("SELECT id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id in ($before_prod_id)");
			foreach ($stockData as $row) 
			{
				$stock= $row[csf('current_stock')];
				$avg_rate=$row[csf('avg_rate_per_unit')];
				$stock_value=$row[csf('stock_value')];


				$previousQnty = $product_cons_quantity_arr[$row[csf('id')]];
				$previousAmount = $product_cons_amount_arr[$row[csf('id')]];


				$cur_st_qnty = $stock - $previousQnty;
				$cur_st_value = $stock_value - $previousAmount;

				$cur_st_qnty = number_format($cur_st_qnty,2,".","");
				if($cur_st_qnty >0){
					$cur_st_rate=$cur_st_value/$cur_st_qnty;
				}else{
					$cur_st_rate=0;
				}

				if($cur_st_qnty <=0){
					$cur_st_value=0;
				}


				$cur_st_rate = number_format($cur_st_rate,2,".","");
				$cur_st_value = number_format($cur_st_value,2,".","");
				
				$data_array_prod_update[$row[csf('id')]]=explode("*",("'".$cur_st_qnty."'*'".$cur_st_rate."'*'".$cur_st_value."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

				$update_prodId_arr[] = $row[csf('id')];
			}
			
		}
		$field_array_update = "updated_by*update_date*status_active*is_deleted";
		$data_array_update = "'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'*0*1";
		$rID = sql_update("inv_receive_master", $field_array_update, $data_array_update, "id", $updateid_finish, 1);
		if($rID) $flag=1; else $flag=0;

		$rID2=$rID3=$rID4=$productUpdate=true;
		if(count($up_trans_id_arr) > 0)
		{
			$rID2=execute_query(bulk_update_sql_statement("inv_transaction","id",$field_array_trans_update,$data_array_trans_update,$up_trans_id_arr));
			// echo "10**".bulk_update_sql_statement("inv_transaction","id",$field_array_trans_update,$data_array_trans_update,$up_trans_id_arr);die;
			if($flag==1)
			{
				if($rID2) $flag=1; else $flag=0; 
			}
			
			$rID3=execute_query(bulk_update_sql_statement("pro_finish_fabric_rcv_dtls","id",$field_details_array_update,$data_array_dtls_update,$up_dtlsId_arr));
			// echo "10**".bulk_update_sql_statement("pro_finish_fabric_rcv_dtls","id",$field_details_array_update,$data_array_dtls_update,$up_dtlsId_arr);die;
			if($flag==1) 
			{
				if($rID3) $flag=1; else $flag=0; 
			}

			//$rID4=execute_query(bulk_update_sql_statement("order_wise_pro_details","dtls_id",$field_array_prop_update,$data_array_prop_update,$up_dtlsId_arr));

			$up_dtlsIds=implode(',', $up_dtlsId_arr);
			$rID4=execute_query("update order_wise_pro_details set status_active=0, is_deleted=1, updated_by=". $_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where trans_id in ($before_trans_id) and entry_form=233 and dtls_id in($up_dtlsIds)");

			// echo "10**".bulk_update_sql_statement("order_wise_pro_details","dtls_id",$field_array_prop_update,$data_array_prop_update,$up_dtlsId_arr);die;
			if($flag==1) 
			{
				if($rID4) $flag=1; else $flag=0; 
			}
		}

		if(count($data_array_prod_update)>0)
		{
			// echo "10**".bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $update_prodId_arr ); die();
			$productUpdate=execute_query(bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $update_prodId_arr ));
		}

		// echo "10**$rID**$rID2**$rID3**$rID4**$productUpdate";
		// oci_rollback($con);die;

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'", '', $updateid_finish)."**".str_replace("'", '', $finish_recv_num)."**".$up_trans_id."**".$up_received_dtls_id."**".$up_proportion_id."**0";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$updateid_finish);
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "2**".str_replace("'", '', $updateid_finish)."**".str_replace("'", '', $finish_recv_num)."**".$up_trans_id."**".$up_received_dtls_id."**".$up_proportion_id."**0";
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$updateid_finish);
			}
		}
		disconnect($con);
		die;
	}	
}

if ($action=="systemId_popup")
{
	echo load_html_head_contents("System ID Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(datas)
		{
			var data = datas.split("**");
			$('#hidden_sys_id').val(data[0]);
			$("#hidden_sys_no").val(data[1]);
			$("#hidden_issue_id").val(data[2]);
			$("#hidden_issue_no").val(data[3]);

			$("#hidden_fso_company_id").val(data[4]);
			$("#hidden_fso_company").val(data[5]);
			$("#hidden_po_company").val(data[6]);
			$("#hidden_issue_date").val(data[7]);
			$("#hidden_issue_rtn_date").val(data[8]);

			$("#hidden_recieved_dtls_id").val(data[9]);
			$("#hidden_transection_id").val(data[10]);
			$("#hidden_proportion_id").val(data[11]);

			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>
		<div align="center" style="width:1130px;">
			<form name="searchsystemidfrm"  id="searchsystemidfrm">
				<fieldset style="width:1120px;">
					<legend>Enter search words</legend>
					<table cellpadding="0" cellspacing="0" width="950" border="1" rules="all" class="rpt_table">
						<thead>
							<th>Company Name</th>
							<th>Receive Date Range</th>
							<th>Buyer</th>
							<th>Search By</th>
							<th id="search_by_td_up">Please Enter System Id</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
								
								<input type="hidden" name="hidden_sys_id" id="hidden_sys_id" class="text_boxes" value="">
								<input type="hidden" name="hidden_sys_no" id="hidden_sys_no" class="text_boxes" value="">
								
								<input type="hidden" name="hidden_issue_id" id="hidden_issue_id" class="text_boxes" value="">
								<input type="hidden" name="hidden_issue_no" id="hidden_issue_no" class="text_boxes" value="">

								<input type="hidden" name="hidden_recieved_dtls_id" id="hidden_recieved_dtls_id" class="text_boxes" value="">
								<input type="hidden" name="hidden_transection_id" id="hidden_transection_id" class="text_boxes" value="">
								<input type="hidden" name="hidden_proportion_id" id="hidden_proportion_id" class="text_boxes" value="">

								<input type="hidden" name="hidden_fso_company_id" id="hidden_fso_company_id" class="text_boxes" value="">
								<input type="hidden" name="hidden_fso_company" id="hidden_fso_company" class="text_boxes" value="">
								<input type="hidden" name="hidden_po_company" id="hidden_po_company" class="text_boxes" value="">
								<input type="hidden" name="hidden_issue_date" id="hidden_issue_date" class="text_boxes" value="">
								<input type="hidden" name="hidden_issue_rtn_date" id="hidden_issue_rtn_date" class="text_boxes" value="">

							</th>
						</thead>
						<tr class="general">
							<td>
                             <? 
                        	echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-Select Company-", $selected, "load_drop_down( 'knit_finish_fabric_issue_return_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );?>
                            
							</td>
							<td>
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px;">To<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px;">
							</td>
							<td id="buyer_td">
                                <?
                                echo create_drop_down("cbo_buyer_name", 100, $blank_array, "", 1, "-- Select Buyer --", $selected, "", 0, "");
                                ?>
                            </td>
							<td>
								<?
								$search_by_arr=array(1=>"System ID");
								$dd="change_search_event(this.value, '0', '0', '../../') ";
								echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
								?>
							</td>
							<td id="search_by_td">
								<input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
							</td>
							<td>
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_company_id').value, 'create_finish_search_list_view', 'search_div', 'knit_finish_fabric_issue_return_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
						</tr>
					</table>
					<div style="margin-top:10px;" id="search_div" align="center"></div>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="create_finish_search_list_view")
{
	$data = explode("_",$data);

	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$buyer_id =$data[4];
	$company_id = $data[5];

	$result = sql_select($sql);	

	if($company_id==0)
	{
		?>
		<span style="font-size:14px; font-weight:bold; color: red;">Please select company</span>
		<?
		exit();
	}
	
	if($buyer_id!=0) $buyer_cond="and b.buyer_id = $buyer_id";
	else $buyer_cond="";

	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.receive_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and a.receive_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}
	
	if(trim($data[0])!="")
	{
		if($search_by==1)
			$search_field_cond="and a.recv_number like '$search_string'";
	}
	else
	{
		$search_field_cond="";
	}	
	
	if($db_type==0) 
	{
		$year_field="YEAR(a.insert_date)";
		$batch_field=" group_concat(b.batch_id) as batch_id";
		$order_field=" group_concat(b.order_id) as order_id";

	}
	else if($db_type==2) 
	{
		$year_field="to_char(a.insert_date,'YYYY')";
		$batch_field=" listagg(b.batch_id,',') within group (order by b.batch_id) as batch_id";
		$order_field=" listagg(b.order_id,',') within group (order by b.order_id) as order_id";
	}
	else 
	{
		$year_field="null";
	}

	
	/*$sql = "select a.id,b.id as rcv_dtls_id, a.recv_number, a.recv_number_prefix_num,a.booking_no,a.booking_id, a.receive_date, $year_field as year, sum(b.receive_qnty) as recv_qty,b.batch_id,b.order_id,b.trans_id,c.id as proprotion_id from inv_receive_master a,pro_finish_fabric_rcv_dtls b,order_wise_pro_details c where a.id=b.mst_id and b.trans_id=c.trans_id and a.entry_form=233 and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond $date_cond $buyer_cond group by a.id, b.id, a.recv_number, a.recv_number_prefix_num,a.booking_no,a.booking_id, a.receive_date, a.insert_date,b.batch_id,b.order_id,b.trans_id,c.id order by a.id desc"; */

	$sql = "select a.id, a.recv_number, a.recv_number_prefix_num,a.booking_no,a.booking_id, a.receive_date, $year_field as year, sum(b.receive_qnty) as recv_qty,$batch_field, $order_field from inv_receive_master a,pro_finish_fabric_rcv_dtls b,order_wise_pro_details c where a.id=b.mst_id and b.trans_id=c.trans_id and a.entry_form=233 and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond $date_cond $buyer_cond group by a.id, a.recv_number, a.recv_number_prefix_num,a.booking_no,a.booking_id, a.receive_date, a.insert_date order by a.id desc";

	//echo $sql; 
	$result = sql_select($sql);	

	if(empty($result))
	{
		?>
		<span style="font-size:14px; font-weight:bold; color: red;">Data Not Found</span>
		<?
		exit();
	}

	foreach ($result as $row)
	{
		$batch_id_arr[$row[csf("batch_id")]] = $row[csf("batch_id")];
		$salesOrderIds .= $row[csf('order_id')].",";
		$issue_ids .= $row[csf('booking_id')].",";
	}

	$salesOrderIds = implode(",", array_filter(array_unique(explode(",",chop($salesOrderIds,",")))));
	$issue_ids = implode(",", array_filter(array_unique(explode(",",chop($issue_ids,",")))));

	$batch_id_arr = array_filter($batch_id_arr);
	if(!empty($batch_id_arr)){
		$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst where id in(".implode(",",$batch_id_arr).") and status_active=1 and is_deleted=0","id","batch_no");
	}


	if($salesOrderIds!="")
	{
		$fso_sql = sql_select("select id,company_id,po_company_id,within_group from fabric_sales_order_mst where status_active=1 and is_deleted=0 and id in($salesOrderIds)");


		$salesOrderData = array();
		foreach ($fso_sql as $row) 
		{
			if($row[csf('within_group')]==1)
			{
				$salesOrderData[$row[csf('id')]]['po_company'] 	=  $row[csf('po_company_id')]; 
			}else {
				$salesOrderData[$row[csf('id')]]['po_company'] 	=  $row[csf('company_id')]; 
			}

			$salesOrderData[$row[csf('id')]]['fso_company']	=  $row[csf('company_id')]; 
		}
	}

	if($issue_ids!="")
	{
		$sql_issue = sql_select("select id, issue_date from inv_issue_master where id in ($issue_ids) and status_active=1 and is_deleted=0");
		$issueData = array();
		foreach ($sql_issue as $row) {
			$issueData[$row[csf('id')]]['issue_date'] = $row[csf('issue_date')];
		}
	}
	
	$company_arr 	= return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0", 'id', 'company_name');

	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="526" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="70">Received ID</th>
			<th width="50">Year</th>
			<th width="80">Receive date</th>
			<th width="80">Receive Qnty</th>
			<th>Batch No</th>
		</thead>
	</table>
	<div style="width:530px; max-height:240px; overflow-y:scroll" id="list_container_batch" align="center">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="510" class="rpt_table" id="tbl_list_search" align="center">
			<?
			$i=1;
			foreach ($result as $row)
			{  
				if ($i%2==0)  
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";	 

				$recv_qnty=$row[csf('recv_qty')];

				$sales_id_arr = array_unique(explode(",", $row[csf('order_id')]));
				foreach ($sales_id_arr as  $val) 
				{
					$po_company_name = $company_arr[$salesOrderData[$val]['po_company']];
					$fso_company_name = $company_arr[$salesOrderData[$val]['fso_company']];
					$fso_company_id = $salesOrderData[$val]['fso_company'];
				}

				

				$datas = $row[csf('id')]."**".$row[csf('recv_number')]."**".$row[csf('booking_id')]."**".$row[csf('booking_no')]."**".$fso_company_id."**".$fso_company_name."**".$po_company_name."**".change_date_format($issueData[$row[csf('booking_id')]]['issue_date'])."**".change_date_format($row[csf('receive_date')]);

				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $datas; ?>');"> 
					<td width="40"><? echo $i; ?></td>
					<td width="70"><p>&nbsp;<? echo $row[csf('recv_number_prefix_num')]; ?></p></td>
					<td width="50" align="center"><p><? echo $row[csf('year')]; ?></p></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
					<td width="80" align="right"><? echo number_format($recv_qnty,2); ?>&nbsp;</td>
					<td align="center">
						<p>
							<? 
							$batch_id_arr = array_unique(explode(",", $row[csf('batch_id')]));
							$batch_nos = "";
							foreach ($batch_id_arr as  $val) 
							{
								if($batch_nos == "") $batch_nos = $batch_arr[$val]; else $batch_nos .= ",". $batch_arr[$val];
							}
							echo $batch_nos; 
							?>&nbsp;
						</p>
					</td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
	</div>
	<?	
	exit();
}

if($action=="show_finish_fabric_listview")
{
	
	$fabric_desc_arr=return_library_array("select id, item_description from product_details_master where item_category_id=2","id","item_description");
	//$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst","id","batch_no");
	//$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	
	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
		}
	}
	
	$sql="select a.booking_id,a.receive_date,b.id, b.prod_id, b.body_part_id, b.fabric_description_id, b.receive_qnty,c.store_id from  inv_receive_master a, pro_finish_fabric_rcv_dtls b, inv_transaction c where a.id=b.mst_id and b.mst_id=c.mst_id and b.mst_id='$data' and c.item_category=2 and c.transaction_type=4 and b.status_active = 1 and b.is_deleted = 0 and a.id=b.mst_id";

	$result=sql_select($sql); 

	foreach($result as $row)
	{ 
		$issue_ids .= $row[csf('booking_id')].",";
		$store_id .= $row[csf('store_id')].",";
	}

	$issue_ids = implode(",", array_filter(array_unique(explode(",",chop($issue_ids,",")))));
	$store_id = implode(",", array_filter(array_unique(explode(",",chop($store_id,",")))));

	if($issue_ids!="")
	{
		$sql_issue = sql_select("select a.id, a.issue_date,b.issue_qnty from inv_issue_master a,inv_finish_fabric_issue_dtls b where a.id=b.mst_id and a.id in ($issue_ids) and a.item_category=2 and a.entry_form=224 and a.status_active=1 and a.is_deleted=0");

		$issueData = array();
		foreach ($sql_issue as $row) {
			$issueData[$row[csf('id')]]['issue_date'] = $row[csf('issue_date')];
			$issueData[$row[csf('id')]]['issue_qnty'] = $row[csf('issue_qnty')];
		}
	}

	// Store
	if($store_id!="")
	{
		$store_arr = return_library_array("select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and b.category_type=2 and a.status_active=1 and a.is_deleted=0 and a.id in($store_id) group by a.id, a.store_name order by a.store_name",'id','store_name');
	}
	
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table">
		<thead>
			<th width="40">SL No</th>
			<th width="100">Store Name</th>
			<th width="80">Issue Qty</th>
			<th width="80">Issue Date</th>
			<th width="200">Item Description</th>
			<th width="80">Return Date</th>
			<th width="">Return Qnty</th>
		</thead>
	</table>
	<div style="width:720px; max-height:200px; overflow-y:scroll" id="list_container_batch" align="left">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" class="rpt_table" id="list_view">  
			<?
			$i=1;
			foreach($result as $row)
			{  
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	 

				if($row[csf('fabric_description_id')]==0 || $row[csf('fabric_description_id')]=="")
				{
					$fabric_desc=$fabric_desc_arr[$row[csf('prod_id')]]; 
				}
				else
				{
					$fabric_desc=$composition_arr[$row[csf('fabric_description_id')]];
				}

				$issueDate = change_date_format($issueData[$row[csf('booking_id')]]['issue_date']);
				$issue_qnty = $issueData[$row[csf('booking_id')]]['issue_qnty'];

				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"> 
					<td width="40"><p><? echo $i; ?></p></td>
					<td width="100"><p><? echo $store_arr[$row[csf('store_id')]];?>&nbsp;</p></td>
					<td width="80" align="right"><p><? echo number_format($issue_qnty,2); ?>&nbsp;</p></td>
					<td width="80" align="center"><p><? echo $issueDate; ?>&nbsp;</p></td>
					<td width="200"><p><? echo $fabric_desc; ?>&nbsp;</p></td>
					<td width="80" align="center"><p><? echo change_date_format($row[csf('receive_date')]); ?>&nbsp;</p></td>
					<td align="right"><p><? echo number_format($row[csf('receive_qnty')],2); ?>&nbsp;</p></td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
	</div>
	<?		
	exit();
}

?>
