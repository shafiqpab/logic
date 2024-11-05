<?

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
//--------------------------------------------------------------------------------------------------------------------
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" );     	 
	exit();
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor_id", 100, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and company_id='$data' and production_process=2  order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );
}

$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$floor_name = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');

if($action=="report_generate")
{
	$process = array( &$_POST );
	// echo"<pre>";
	// print_r($process);
	extract(check_magic_quote_gpc( $process )); 
	$company_name= str_replace("'","",$cbo_company_name);
	$cbo_search_by= str_replace("'","",$cbo_search_by);
	$cbo_floor_id= str_replace("'","",$cbo_floor_id);
	$cbo_year= str_replace("'","",$cbo_year);
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and d.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and d.buyer_name in (".str_replace("'","",$cbo_buyer_name).")";
	}
	
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		$date_cond=" and c.shipment_date between $txt_date_from and $txt_date_to ";
	}
	
	$search_cond="";
	
	if($cbo_search_by==1 && str_replace("'","",$txt_search_string)!="") $search_cond=" and d.job_no_prefix_num=$txt_search_string";
	else if($cbo_search_by==2 && str_replace("'","",$txt_search_string)!="") $search_cond=" and d.style_ref_no=$txt_search_string";
	else if($cbo_search_by==3 && str_replace("'","",$txt_search_string)!="") $search_cond=" and c.po_number=$txt_search_string";
	else if($cbo_search_by==4 && str_replace("'","",$txt_search_string)!="") $search_cond=" and c.file_no=$txt_search_string";
	else if($cbo_search_by==5 && str_replace("'","",$txt_search_string)!="") $search_cond=" and c.grouping=$txt_search_string";
	
	if($cbo_floor_id !=0) $floor_cond=" and e.floor_id=$cbo_floor_id";
	if($cbo_floor_id !=0) $floor_knitting_cond=" and b.floor_id=$cbo_floor_id";
	// echo $floor_cond;die;
	$year_cond="";
	if($cbo_year>0)
	{
		if($db_type==0)
		{
			$year_cond=" and year(d.insert_date)='$cbo_year'";
		}
		else
		{
			$year_cond=" and to_char(d.insert_date, 'YYYY')='$cbo_year'";
		}
	}
	
	//  $sql="select a.body_part_id, a.construction, a.composition, b.fabric_color_id, sum(b.grey_fab_qnty) as grey_fab_qnty, c.id as po_id, c.po_number, d.id as job_id, d.job_no_prefix_num, d.job_no, d.company_name, d.buyer_name, d.style_ref_no,c.grouping
	// 	 from wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls b, wo_po_break_down c, wo_po_details_master d
	// 	 where a.id=b.pre_cost_fabric_cost_dtls_id and b.po_break_down_id=c.id and c.job_no_mst=d.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.company_name=$company_name $buyer_id_cond $date_cond $search_cond $year_cond
	// 	 group by a.body_part_id, a.construction, a.composition, b.fabric_color_id, c.id, c.po_number, d.id, d.job_no_prefix_num, d.job_no, d.company_name, d.buyer_name, d.style_ref_no, c.grouping";
	if($cbo_floor_id !=0)
	{
		$sql="select a.body_part_id, a.construction, a.composition, b.fabric_color_id, sum(b.grey_fab_qnty) as grey_fab_qnty, c.id as po_id, c.po_number, d.id as job_id, d.job_no_prefix_num, d.job_no, d.company_name, d.buyer_name, d.style_ref_no,c.grouping,e.floor_id
		from wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls b, wo_po_break_down c, wo_po_details_master d, pro_grey_prod_entry_dtls     e,
		pro_roll_details f
		where a.id=b.pre_cost_fabric_cost_dtls_id and b.po_break_down_id=c.id and c.job_no_mst=d.job_no and f.po_breakdown_id  = c.id
		and e.id = f.dtls_id and f.entry_form = 2  
		and e.mst_id = f.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.company_name=$company_name $buyer_id_cond $date_cond $search_cond $year_cond $floor_cond
		group by a.body_part_id, a.construction, a.composition, b.fabric_color_id, c.id, c.po_number, d.id, d.job_no_prefix_num, d.job_no, d.company_name, d.buyer_name, d.style_ref_no, c.grouping, e.floor_id";
	}
	else
	{
		$sql="select a.body_part_id, a.construction, a.composition, b.fabric_color_id, sum(b.grey_fab_qnty) as grey_fab_qnty, c.id as po_id, c.po_number, d.id as job_id, d.job_no_prefix_num, d.job_no, d.company_name, d.buyer_name, d.style_ref_no,c.grouping
		from wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls b, wo_po_break_down c, wo_po_details_master d
		where a.id=b.pre_cost_fabric_cost_dtls_id and b.po_break_down_id=c.id and c.job_no_mst=d.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.company_name=$company_name $buyer_id_cond $date_cond $search_cond $year_cond
		group by a.body_part_id, a.construction, a.composition, b.fabric_color_id, c.id, c.po_number, d.id, d.job_no_prefix_num, d.job_no, d.company_name, d.buyer_name, d.style_ref_no, c.grouping";
	}
		 
	
	// echo $sql;
		 
	$sql_result=sql_select($sql);
	$details_data=array();
	foreach($sql_result as $row)
	{
		$all_po_id.=$row[csf("po_id")].",";
		$details_data[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("fabric_color_id")]]["body_part_id"]=$row[csf("body_part_id")];
		$details_data[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("fabric_color_id")]]["construction"]=$row[csf("construction")];
		$details_data[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("fabric_color_id")]]["composition"]=$row[csf("composition")];
		$details_data[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("fabric_color_id")]]["fabric_color_id"]=$row[csf("fabric_color_id")];
		$details_data[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("fabric_color_id")]]["grey_fab_qnty"]+=$row[csf("grey_fab_qnty")];
		$details_data[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("fabric_color_id")]]["po_id"]=$row[csf("po_id")];
		$details_data[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("fabric_color_id")]]["po_number"]=$row[csf("po_number")];
		$details_data[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("fabric_color_id")]]["job_id"]=$row[csf("job_id")];
		$details_data[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("fabric_color_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
		$details_data[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("fabric_color_id")]]["job_no"]=$row[csf("job_no")];
		$details_data[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("fabric_color_id")]]["grouping"]=$row[csf("grouping")];
		$details_data[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("fabric_color_id")]]["company_name"]=$row[csf("company_name")];
		$details_data[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("fabric_color_id")]]["buyer_name"]=$row[csf("buyer_name")];
		$details_data[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("fabric_color_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
	}
	
	
	/*$sql_knitting="select a.po_breakdown_id, b.body_part_id, b.color_id, c.mst_id, c.dtls_id, c.barcode_no, sum(a.quantity) as quantity  
		from order_wise_pro_details a, pro_grey_prod_entry_dtls b, pro_roll_details c 
		where a.dtls_id=b.id and b.id=c.dtls_id and b.mst_id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.po_breakdown_id in(".implode(",",$all_po_arr).")";*/
	
	
	$all_po_arr=array_unique(explode(",",chop($all_po_id,",")));
	
	if($db_type==0)
	{
		$sql_knitting="select b.body_part_id, b.color_id, b.floor_id, c.mst_id, c.dtls_id, c.po_breakdown_id, c.barcode_no, c.qnty as quantity  
		from pro_grey_prod_entry_dtls b, pro_roll_details c 
		where b.id=c.dtls_id and b.mst_id=c.mst_id and c.entry_form=2 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 $floor_knitting_cond and c.is_deleted=0 and c.po_breakdown_id in(".implode(",",$all_po_arr).")";
	}
	else
	{
		$all_po_arr=array_chunk($all_po_arr,999);
		$sql_knitting="select b.mst_id, b.body_part_id, b.color_id, b.floor_id, c.mst_id, c.dtls_id, c.po_breakdown_id, c.barcode_no, c.qnty as quantity  
		from pro_grey_prod_entry_dtls b, pro_roll_details c 
		where b.id=c.dtls_id and b.mst_id=c.mst_id and c.entry_form=2 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 $floor_knitting_cond and c.is_deleted=0 ";
		$p=1;
		if(!empty($all_po_arr))
		{
			foreach($all_po_arr as $po_id)
			{
				if($p==1) $sql_knitting .=" and (c.po_breakdown_id in(".implode(',',$po_id).")"; else $sql_knitting .=" or c.po_breakdown_id in(".implode(',',$po_id).")";
				$p++;
			}
			$sql_knitting .=" )";
		}
	}
	// echo $sql_knitting;die;
	$sql_knitting_result=sql_select($sql_knitting);
	$kinitting_data=array();
	$all_barcode="";
	foreach($sql_knitting_result as $row)
	{
		$all_barcode.=$row[csf("barcode_no")].",";
		$kinitting_data[$row[csf("po_breakdown_id")]][$row[csf("body_part_id")]][$row[csf("color_id")]]["quantity"]+=$row[csf("quantity")];
		$kinitting_data[$row[csf("po_breakdown_id")]][$row[csf("body_part_id")]][$row[csf("color_id")]]["floor_id"]=$row[csf("floor_id")];
		$kinitting_data[$row[csf("po_breakdown_id")]][$row[csf("body_part_id")]][$row[csf("color_id")]]["mst_id"].=$row[csf("mst_id")].",";
		$kinitting_data[$row[csf("po_breakdown_id")]][$row[csf("body_part_id")]][$row[csf("color_id")]]["barcode_no"].=$row[csf("barcode_no")].",";
	}
	
	$all_barcode=chop($all_barcode,",");
	
	// delivery info
	if($all_barcode!="")
	{
		$all_barcode_arr=array_unique(explode(",",$all_barcode));
		if($db_type==0)
		{
			$sql_delivery="select barcode_no, qnty  from pro_roll_details where status_active=1 and is_deleted=0 and entry_form=56 and barcode_no in (".implode(",",$all_barcode_arr).")";
		}
		else
		{
			$all_barcode_arr=array_chunk($all_barcode_arr,999);
			$sql_delivery="select barcode_no, qnty from pro_roll_details where status_active=1 and is_deleted=0 and entry_form=56 ";
			$p=1;
			foreach($all_barcode_arr as $bar_code)
			{
				if($p==1) $sql_delivery .=" and (barcode_no in(".implode(',',$bar_code).")"; else $sql_delivery .=" or barcode_no in(".implode(',',$bar_code).")";
				$p++;
			}
			$sql_delivery .=" )";
		}
		
	}
	
	$sql_delivery_result=sql_select($sql_delivery);
	$delivery_data=array();
	foreach($sql_delivery_result as $row)
	{
		$delivery_data[$row[csf("barcode_no")]]=$row[csf("qnty")];
	}
	
	
	// receive info
	if($all_barcode!="")
	{
		$all_barcode_arr=array_unique(explode(",",$all_barcode));
		if($db_type==0)
		{
			$sql_receive="select a.barcode_no, a.qnty  from pro_roll_details a, pro_grey_prod_entry_dtls b where a.dtls_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in(2,58) and b.trans_id<>0 and a.barcode_no in (".implode(",",$all_barcode_arr).")";
		}
		else
		{
			$all_barcode_arr=array_chunk($all_barcode_arr,999);
			$sql_receive="select a.barcode_no, a.qnty  from pro_roll_details a, pro_grey_prod_entry_dtls b where a.dtls_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in(2,58) and b.trans_id<>0 ";
			$p=1;
			foreach($all_barcode_arr as $bar_code)
			{
				if($p==1) $sql_receive .=" and (a.barcode_no in(".implode(',',$bar_code).")"; else $sql_receive .=" or a.barcode_no in(".implode(',',$bar_code).")";
				$p++;
			}
			$sql_receive .=" )";
		}
		
	}
	
	$sql_receive_result=sql_select($sql_receive);
	$receive_data=array();
	foreach($sql_receive_result as $row)
	{
		$receive_data[$row[csf("barcode_no")]]=$row[csf("qnty")];
	}
	
	// Issue info
	if($all_barcode!="")
	{
		$all_barcode_arr=array_unique(explode(",",$all_barcode));
		if($db_type==0)
		{
			$sql_issue="select barcode_no, qnty  from pro_roll_details where status_active=1 and is_deleted=0 and entry_form=61 and barcode_no in (".implode(",",$all_barcode_arr).")";
		}
		else
		{
			$all_barcode_arr=array_chunk($all_barcode_arr,999);
			$sql_issue="select barcode_no, qnty from pro_roll_details where status_active=1 and is_deleted=0 and entry_form=61 ";
			$p=1;
			foreach($all_barcode_arr as $bar_code)
			{
				if($p==1) $sql_issue .=" and (barcode_no in(".implode(',',$bar_code).")"; else $sql_issue .=" or barcode_no in(".implode(',',$bar_code).")";
				$p++;
			}
			$sql_issue .=" )";
		}
		
	}
	
	$sql_issue_result=sql_select($sql_issue);
	$issue_data=array();
	foreach($sql_issue_result as $row)
	{
		$issue_data[$row[csf("barcode_no")]]=$row[csf("qnty")];
	}
	
	
	// Transfer info
	if($all_barcode!="")
	{
		$all_barcode_arr=array_unique(explode(",",$all_barcode));
		if($db_type==0)
		{
			$sql_transfer="select a.barcode_no, (case when b.trans_type=5 then a.qnty else 0 end) as trans_in, (case when b.trans_type=6 then a.qnty else 0 end) as trans_out  from pro_roll_details a, order_wise_pro_details b where a.dtls_id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=83 and b.entry_form=83 and a.barcode_no in (".implode(",",$all_barcode_arr).")";
		}
		else
		{
			$all_barcode_arr=array_chunk($all_barcode_arr,999);
			$sql_transfer="select a.barcode_no, b.trans_type, a.qnty from pro_roll_details a, order_wise_pro_details b where a.dtls_id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=83 and b.entry_form=83 ";
			$p=1;
			foreach($all_barcode_arr as $bar_code)
			{
				if($p==1) $sql_transfer .=" and (a.barcode_no in(".implode(',',$bar_code).")"; else $sql_transfer .=" or a.barcode_no in(".implode(',',$bar_code).")";
				$p++;
			}
			$sql_transfer .=" )";
		}
		
	}
	
	//echo $sql_transfer;
	
	$sql_transfer_result=sql_select($sql_transfer);
	$transfer_data=array();
	foreach($sql_transfer_result as $row)
	{
		if($row[csf("trans_type")]==5)
			$transfer_data[$row[csf("barcode_no")]]["trans_in"]=$row[csf("qnty")];
		else
			$transfer_data[$row[csf("barcode_no")]]["trans_out"]=$row[csf("qnty")];
	}
	//var_dump($transfer_data);
	ob_start();
?>
<fieldset style="width:2170px;">
 	<table width="2150" cellspacing="0" cellpadding="0" border="0" rules="all" >
        <tr class="form_caption">
            <td colspan="20" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
        </tr>
        <tr class="form_caption">
            <td colspan="20" align="center"><? echo $company_library[$company_name]; ?></td>
        </tr>
    </table>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2150" class="rpt_table" >
        <thead>
            <th width="30">SL No</th>
            <th width="100">Company Name</th>
			<th width="140">Knitting Production Floor</th>
            <th width="100">Job No</th>
            <th width="110">Style No</th>
            <th width="110">Internal Ref. Number</th>
            <th width="110">Order No</th>
            <th width="120">Body Part</th>
            <th width="110">Composition</th>
            <th width="110">Constraction</th>
            <th width="120">Color Name</th>
            <th width="100">Grey Qty.</th>
            <th width="100">Kniting Production Qty.</th>
            <th width="100">Knitting Balance Qty.</th>
            <th width="100">Knitting Delivery TO Store</th>
            <th width="100">Delivery Balance</th>
            <th width="100">Grey Fabric Rccvd</th>
            <th width="100">Rcvd Balance</th>
            <th width="100">Issue Qty.</th>
            <th width="100">In Hand Qty.</th>
            <th width="">Remarks</th>
        </thead>
    </table>
    <div style="width:2170px; overflow-y:scroll; max-height:450px;" id="scroll_body">
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2150" class="rpt_table" id="tbl_list_search">
		<?
		$i=1;
		foreach($details_data as $po_id=>$po_result)
		{
			foreach($po_result as $body_part_id=>$body_result)
			{
				foreach($body_result as $color_id=>$row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$knit_qnty=$grey_delivery=$grey_receive=$grey_issue=$knit_balance=$delivery_balance=$receive_balance=$in_hand=$transfer_in=$transfer_out=0;
					$all_barcode="";
					$knit_qnty=$kinitting_data[$po_id][$body_part_id][$color_id]["quantity"];
					$knit_floor=$kinitting_data[$po_id][$body_part_id][$color_id]["floor_id"];
					$all_barcode=array_unique(explode(",",chop($kinitting_data[$po_id][$body_part_id][$color_id]["barcode_no"],",")));
					foreach($all_barcode as $b_code)
					{
						$grey_delivery+=$delivery_data[$b_code];
						$grey_receive+=$receive_data[$b_code];
						$grey_issue+=$issue_data[$b_code];
						$transfer_in+=$transfer_data[$b_code]["trans_in"];
						$transfer_out+=$transfer_data[$b_code]["trans_out"];
					}
					$grey_receive=$grey_receive+$transfer_in;
					$grey_issue=$grey_issue+$transfer_out;
					
					$knit_balance=$row[('grey_fab_qnty')]-$knit_qnty;
					$delivery_balance=$knit_qnty-$grey_delivery;
					$receive_balance=$row[('grey_fab_qnty')]-$grey_receive;
					$in_hand=$grey_receive-$grey_issue;
					
					$all_mst_id=chop($kinitting_data[$po_id][$body_part_id][$color_id]["mst_id"],",");
					
					$data_p= $row[('job_no')]."_".$row[('body_part_id')]."_".$row[('construction')]."_".$row[('fabric_color_id')]."_".chop($kinitting_data[$po_id][$body_part_id][$color_id]["barcode_no"],",");
					
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
						<td width="30" align="center"><? echo $i; ?></td>
						<td width="100"><p><? echo $company_library[$row[('company_name')]]; ?></p></td>
						<td width="140"><p><? echo $floor_name[$knit_floor]; ?></p></td>
						<td width="100"><p><? echo $row[('job_no')]; ?></p></td>
						<td width="110"><p><? echo $row[('style_ref_no')]; ?></p></td>
						<td width="110"><p><? echo $row[('grouping')]; ?></p></td>
						<td width="110"><p><? echo $row[('po_number')]; ?></p></td>
						<td width="120"><p><? echo $body_part[$row[('body_part_id')]]; ?></p></td>
						<td width="110"><p><? echo $row[('composition')]; ?></p></td>
						<td width="110"><p><? echo $row[('construction')]; ?></p></td>
						<td width="120"><p><? echo $color_library[$row[('fabric_color_id')]]; ?></p></td>
						<td width="100" align="right"><? echo number_format($row[('grey_fab_qnty')],2); ?></td>
						<td align="right" width="100"><a href='#report_details' onClick="openmypage_knitting('<? echo $row[('job_no')]; ?>','<? echo $row[('body_part_id')]; ?>','<? echo $row[('construction')];?>','<? echo $row[('fabric_color_id')];?>','<? echo $all_mst_id;?>','knitting_popup');"><? echo number_format($knit_qnty,2); ?></a></td>
						<td width="100" align="right"><? echo number_format($knit_balance,2);  ?></td>
						<td width="100" align="right"><? echo number_format($grey_delivery,2);  ?></td>
						<td width="100" align="right"><? echo number_format($delivery_balance,2); ?></td>
						<td width="100" align="right"><a href="##" onClick="openpage('recv_popup','<? echo $data_p; ?>')"><? echo number_format($grey_receive,2);  ?></a></td>
						<td width="100" align="right"><? echo number_format($receive_balance,2);  ?></td>
						<td width="100" align="right"><a href="##" onClick="openpage('iss_popup','<? echo $data_p; ?>')"><? echo number_format($grey_issue,2); ?></a></td>
						<td align="right" width="100"><? echo number_format($in_hand,2,'.',''); ?>  </td>
						<td><p>&nbsp;</p></td>
					</tr>
					<?
					$total_grey_fab_qnty+=$row[('grey_fab_qnty')];
					$total_knit_qnty+=$knit_qnty;
					$total_knit_balance+=$knit_balance;
					$total_grey_delivery+=$grey_delivery;
					$total_delivery_balance+=$delivery_balance;
					
					$total_grey_receive+=$grey_receive;
					$total_receive_balance+=$receive_balance;
					$total_grey_issue+=$grey_issue;
					$total_in_hand+=$in_hand;
					
					$i++; 
				}
			}
			 
		}
?>
     </table>
     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2150" class="rpt_table">
         <tfoot>
            <th width="30"></th>
            <th width="100"></th>
            <th width="140"></th>
            <th width="100"></th>
            <th width="110"></th>
            <th width="110"></th>
            <th width="110"></th>
            <th width="120"></th>
            <th width="110"></th>
            <th width="110"></th>
            <th width="120" align="right">Total: </th>
            <th width="100" align="right" id="value_total_grey_fab_qnty"><? echo number_format($total_grey_fab_qnty,2,'.',''); ?></th>
            <th width="100" align="right" id="value_total_knit_qnty"><? echo number_format($total_knit_qnty,2,'.',''); ?></th>
            <th width="100" align="right" id="value_total_knit_balance"><? echo number_format($total_knit_balance,2,'.',''); ?></th>
            <th width="100" align="right" id="value_total_grey_delivery"><? echo number_format($total_grey_delivery,2,'.',''); ?></th>
            <th width="100" align="right" id="value_total_delivery_balance"><? echo number_format($total_delivery_balance,2,'.',''); ?></th>
            <th width="100" align="right" id="value_total_grey_receive"><? echo number_format($total_grey_receive,2,'.',''); ?></th>
            <th width="100" align="right" id="value_total_receive_balance"><? echo number_format($total_receive_balance,2,'.',''); ?></th>
            <th width="100" align="right" id="value_total_grey_issue"><? echo number_format($total_grey_issue,2,'.',''); ?></th>
            <th width="100" align="right" id="value_total_in_hand"><? echo number_format($total_in_hand,2,'.',''); ?></th>
            <th width=""></th>
        </tfoot>
     </table>
    </div>
</fieldset>
<?
	exit();
}

if($action=="knitting_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $po_id;
	?>
    <script>
	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		$('#table_body tbody tr:first').hide();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+ '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		$('#table_body tbody tr:first').show();
		d.close();
	}
	var tableFilters = 
	{
		col_10: "none",
		col_operation: {
		id: ["value_total_balance"],
		col: [5],
		operation: ["sum"],
		write_method: ["innerHTML"]
		}
	}	
</script>	
	<fieldset style="width:640px; margin-left:3px">
        <p><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></p>
        <br>
        <div id="report_container" style="width:100%">
        	<table border="0" rules="all" width="620" cellpadding="0" cellspacing="0">
            	<tr>
                	<td>Job No : <? echo $job_no; ?></td>
                    <td>Body part : <? echo $body_part[$body_part_id]; ?></td>
                    <td>Constraction : <? echo $construction; ?></td>
                    <td>Color : <? echo $color_library[$fabric_color_id]; ?></td>
                </tr>
            </table>
            <br>
			<table border="1" class="rpt_table" rules="all" width="620" cellpadding="0" cellspacing="0" id="table_header">
				<thead>
                	<tr>
                        <th width="50">Sl</th>
                        <th width="130">Knit Pro. No</th>
                        <th width="130">Production No</th>
                        <th width="100">Prod Date</th>
                        <th width="100">Total roll</th>
                        <th>Production Qty</th>
                    </tr>
				</thead>
            </table>
           <!--  <div style="width:640px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body"> -->
           	<div style="width:640px;  font-size:12px; " id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="620" cellpadding="0" cellspacing="0" id="table_body">
                <tbody>
                <?
					$i=1;
					$sql_data=("select a.id, a.recv_number, a.receive_date, a.booking_no, count(c.barcode_no) as tot_roll, sum(c.qnty) as qnty from inv_receive_master a, pro_grey_prod_entry_dtls b,  pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and a.id in($all_mst_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, a.recv_number, a.receive_date, a.booking_no");
					//echo $sql_data;
					$data_array=sql_select($sql_data);
					$i=1;
					foreach($data_array as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
						?>
                            <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="50"><p><? echo $i; ?>&nbsp;</p></td>
                            <td width="130"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
                            <td width="130"><p><? echo $row[csf('recv_number')]; ?>&nbsp;</p></td>
                            <td width="100"><p><? echo change_date_format($row[csf('receive_date')]); ?>&nbsp;</p></td>
                            <td width="100" align="right"><p><? echo $row[csf('tot_roll')]; ?>&nbsp;</p></td>
                            <td align="right"><p><? echo number_format($row[csf('qnty')],2); ?></p></td>
                        </tr> 
						<?
						$tot_qty+=$row[csf('qnty')];
						$i++;
					}
				?>
                </tbody>
            </table>
            </div>
            <table border="1" class="rpt_table" rules="all" width="620" cellpadding="0" cellspacing="0" id="report_table_footer">
				<tfoot>
                    <th width="50">&nbsp;</th>
                    <th width="130">&nbsp;</th>
                    <th width="130">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100" align="right">Total:</th>
                    <th id="value_tot_qty" align="right"><? echo number_format($tot_qty,2); ?></th>
				</tfoot>
            </table>
        </div>
    </fieldset>
    <script>setFilterGrid('table_body',-1,tableFilters);</script>
    <?
	exit();
}

if($action=="recv_popup")
{
 	echo load_html_head_contents("Receive Details", "../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	$data=explode("_",$data);
	$job_no=$data[0];
	$body_part_id=$data[1];
	$construction=$data[2];
	$fabric_color_id=$data[3];
	$barcode_nos=$data[4];
	?>
	<script>
	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		$('#table_body tbody tr:first').hide();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+ '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		$('#table_body tbody tr:first').show();
		d.close();
	}
	var tableFilters = 
	{
		col_10: "none",
		col_operation: {
		id: ["value_total_balance"],
		col: [5],
		operation: ["sum"],
		write_method: ["innerHTML"]
		}
	}	
</script>	
	<fieldset style="width:640px; margin-left:3px">
        <p><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></p>
        <br>
        <div id="report_container" style="width:100%">
        	<table border="0" rules="all" width="620" cellpadding="0" cellspacing="0">
            	<tr>
                	<td>Job No : <? echo $job_no; ?></td>
                    <td>Body part : <? echo $body_part[$body_part_id]; ?></td>
                    <td>Constraction : <? echo $construction; ?></td>
                    <td>Color : <? echo $color_library[$fabric_color_id]; ?></td>
                </tr>
            </table>
            <br>
        
        <table cellpadding="0" width="620" class="rpt_table" rules="all" border="1">
            <thead>
                <th width="50">SL</th>
                <th width="120">Purpose</th>
                <th width="130">Transaction No</th>
                <th width="100">Bacode No</th>
                <th width="100">Roll No</th>
                <th>Roll Weight</th>
            </thead>
        </table>
        <div style="width:640px; max-height:250px; overflow-y:scroll">
            <table cellpadding="0" width="620" class="rpt_table" rules="all" border="1" id="tbl_list_search">
			<?
			
				$i=0; $tot_grey_qnty=0;
                $sql="select a.recv_number, c.barcode_no, c.roll_no, c.qnty
				from inv_receive_master a,  pro_grey_prod_entry_dtls b, pro_roll_details c 
				WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,58) and c.entry_form in(2,58) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos)";
               //echo $sql."<br>";//die;
			   	$tot_qnty=0;
                $result= sql_select($sql);
                foreach($result as $row)
                {
					$i++;
                    if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
               	?>
                   <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="50"><? echo $i; ?></td>
                        <td width="120"><p><? echo "Receive"; ?>&nbsp;</p></td>
                        <td width="130"><p><? echo $row[csf('recv_number')]; ?>&nbsp;</p></td>
                        <td width="100"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
                        <td width="100" align="center"><p><? echo $row[csf('roll_no')]; ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
                    </tr>
                <? 
					$tot_qnty+=$row[csf('qnty')];
                } 
				$trans_sql="select a.transfer_system_id, c.barcode_no, c.roll_no, c.qnty 
				from order_wise_pro_details p, inv_item_transfer_mst a,  inv_item_transfer_dtls b,  pro_roll_details c where p.trans_id=b.to_trans_id and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form=83 and p.entry_form=83 and p.trans_type=5 and c.status_active=1 and c.is_deleted=0 
and c.barcode_no in($barcode_nos) ";
				//echo $trans_sql."<br>";
				$trans_result=sql_select($trans_sql);
				foreach($trans_result as $row)
                {
					$i++;
                    if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					
               	?>
                   <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="50"><? echo $i; ?></td>
                        <td width="120"><p><? echo "Transfer"; ?>&nbsp;</p></td>
                        <td width="130"><p><? echo $row[csf("transfer_system_id")]; ?>&nbsp;</p></td>
                        <td width="100"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
                        <td width="100" align="center"><p><? echo $row[csf('roll_no')]; ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
                    </tr>
                <? 
					$tot_qnty+=$row[csf('qnty')];
                }
            ?>
            </table>
		</div> 
        <table cellpadding="0" width="620" class="rpt_table" rules="all" border="1">
            <tfoot>
                <th colspan="4">Roll Total :</th>
                <th width="100" style="text-align:center"><? echo $i; ?></th>
                <th width="113"><? echo number_format($tot_qnty,2); ?></th>
            </tfoot>
        </table>
        </div>
    </fieldset>
<?
exit();
}


if($action=="iss_popup")
{
 	echo load_html_head_contents("Issue Details", "../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	$data=explode("_",$data);
	$job_no=$data[0];
	$body_part_id=$data[1];
	$construction=$data[2];
	$fabric_color_id=$data[3];
	$barcode_nos=$data[4];
	?>
	<script>
	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		$('#table_body tbody tr:first').hide();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+ '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		$('#table_body tbody tr:first').show();
		d.close();
	}
	var tableFilters = 
	{
		col_10: "none",
		col_operation: {
		id: ["value_total_balance"],
		col: [5],
		operation: ["sum"],
		write_method: ["innerHTML"]
		}
	}	
</script>	
	<fieldset style="width:640px; margin-left:3px">
        <p><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></p>
        <br>
        <div id="report_container" style="width:100%">
        	<table border="0" rules="all" width="620" cellpadding="0" cellspacing="0">
            	<tr>
                	<td>Job No : <? echo $job_no; ?></td>
                    <td>Body part : <? echo $body_part[$body_part_id]; ?></td>
                    <td>Constraction : <? echo $construction; ?></td>
                    <td>Color : <? echo $color_library[$fabric_color_id]; ?></td>
                </tr>
            </table>
            <br>
        
        <table cellpadding="0" width="620" class="rpt_table" rules="all" border="1">
            <thead>
                <th width="50">SL</th>
                <th width="120">Purpose</th>
                <th width="130">Transaction No</th>
                <th width="100">Bacode No</th>
                <th width="100">Roll No</th>
                <th>Roll Weight</th>
            </thead>
        </table>
        <div style="width:640px; max-height:250px; overflow-y:scroll">
            <table cellpadding="0" width="620" class="rpt_table" rules="all" border="1" id="tbl_list_search">
			<?
			
				$i=0; $tot_qnty=0;
				
				$sql="select a.id, a.issue_number, a.issue_purpose, c.barcode_no, c.roll_no, c.qnty  
				from inv_issue_master a, inv_grey_fabric_issue_dtls b, pro_roll_details c 
				WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=61 and c.entry_form=61 and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos)";
               //echo $sql."<br>";//die;
			   	$tot_qnty=0;
                $result= sql_select($sql);
                foreach($result as $row)
                {
					$i++;
                    if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
               	?>
                   <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="50"><? echo $i; ?></td>
                        <td width="120"><p><? echo "Issue"; ?>&nbsp;</p></td>
                        <td width="130"><p><? echo $row[csf('issue_number')]; ?>&nbsp;</p></td>
                        <td width="100"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
                        <td width="100" align="center"><p><? echo $row[csf('roll_no')]; ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
                    </tr>
                <? 
					$tot_qnty+=$row[csf('qnty')];
                } 
				$trans_sql="select a.transfer_system_id, c.barcode_no, c.roll_no, c.qnty 
				from order_wise_pro_details p, inv_item_transfer_mst a,  inv_item_transfer_dtls b,  pro_roll_details c where p.trans_id=b.trans_id and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form=83 and p.entry_form=83 and p.trans_type=6 and c.status_active=1 and c.is_deleted=0 
and c.barcode_no in($barcode_nos) ";
				//echo $trans_sql."<br>";
				$trans_result=sql_select($trans_sql);
				foreach($trans_result as $row)
                {
					$i++;
                    if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					
               	?>
                   <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="50"><? echo $i; ?></td>
                        <td width="120"><p><? echo "Transfer"; ?>&nbsp;</p></td>
                        <td width="130"><p><? echo $row[csf("transfer_system_id")]; ?>&nbsp;</p></td>
                        <td width="100"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
                        <td width="100" align="center"><p><? echo $row[csf('roll_no')]; ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
                    </tr>
                <? 
					$tot_qnty+=$row[csf('qnty')];
                }
            ?>
            </table>
		</div> 
        <table cellpadding="0" width="620" class="rpt_table" rules="all" border="1">
            <tfoot>
                <th colspan="4">Roll Total :</th>
                <th width="100" style="text-align:center"><? echo $i; ?></th>
                <th width="113"><? echo number_format($tot_qnty,2); ?></th>
            </tfoot>
        </table>
        </div>
    </fieldset>
<?
exit();
}


?>