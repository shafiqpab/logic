<?php
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action']; 

if ($action=="load_drop_down_buyer") {
	list($company,$type)=explode("_",$data);
	if($type==1) {
		echo create_drop_down( "cbo_customer_name", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $company, "$load_function");
	} else {
		echo create_drop_down( "cbo_customer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", "", "" );
	}	
	exit();	 
}

if($action=="generate_report") {
	// reference OG-TB-20-00044, Akram-02012020

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$cbo_company_id=str_replace("'","", $cbo_company_id);
	$cbo_order_source=str_replace("'","", $cbo_order_source);
	$cbo_customer_name=str_replace("'","", $cbo_customer_name);
	$internal_no=str_replace("'","", $txt_internal_no);
	$cbo_section_id=str_replace("'","", $cbo_section_id);
	$cbo_delivery_status=str_replace("'","", $cbo_delivery_status);
	$txt_date_from=str_replace("'","", $txt_date_from);
	$txt_date_to=str_replace("'","", $txt_date_to);
	$txt_order_no=str_replace("'","", $txt_order_no);

	if($cbo_delivery_status== 0){$delivery_status_con="";}
	else{$delivery_status_con=" and c.delivery_status=$cbo_delivery_status";}
		
	if($db_type==0) 
	{
		$txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
		$txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
	}
	else if($db_type==2) 
	{
		$txt_date_from=change_date_format($txt_date_from,'','',1);
		$txt_date_to=change_date_format($txt_date_to,'','',1);
	}
	
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 ","id","buyer_name");
	$trimsGroupArr = return_library_array("select id, item_name from lib_item_group where item_category=4 and status_active=1","id","item_name");
	$colorNameArr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$sizeArr = return_library_array("select id, size_name from lib_size where status_active=1 and is_deleted=0", 'id','size_name');
	$leaderArr = return_library_array("select id, team_leader_name from lib_marketing_team where status_active=1 and is_deleted=0 and project_type=3","id","team_leader_name");
	$memberArr = return_library_array("select id, team_member_name from lib_mkt_team_member_info","id","team_member_name");

	$currency_rate=return_field_value( "conversion_rate", "currency_conversion_rate", "currency=2 and status_active=1  and id in(select max(id) from currency_conversion_rate where currency=2 and is_deleted=0 and status_active=1)" , "conversion_rate" );
	
	if($txt_date_from!="" and $txt_date_to!=""){
		// $order_where_con.=" and a.receive_date between '$txt_date_from' and '$txt_date_to'";
		$prod_where_con.=" and a.production_date between '$txt_date_from' and '$txt_date_to'";
		// $qc_where_con.=" and a.receive_date between '$txt_date_from' and '$txt_date_to'";
		$delivery_where_con.=" and a.delivery_date between '$txt_date_from' and '$txt_date_to'";
		$bill_where_con.=" and d.bill_date between '$txt_date_from' and '$txt_date_to'";
		// else if($cbo_date_category==2){$where_con.=" and a.delivery_date between '$txt_date_from' and '$txt_date_to'";}
	}
	if($cbo_section_id){$where_con.=" and b.section='$cbo_section_id'";} 
	if($cbo_order_source){$where_con.=" and a.within_group='$cbo_order_source'";} 
	if($cbo_customer_name){$where_con.=" and a.party_id='$cbo_customer_name'";} 
	
 	$buyer_po_id_cond = '';
    if($cbo_order_source==1 || $cbo_order_source==0)
    {
		if($internal_no !="") $internal_no_cond = " and grouping like('%$internal_no%')";
		
		$buyer_po_arr=array();
		$buyer_po_id_arr=array();
		$po_sql ="select id, grouping from wo_po_break_down  where is_deleted=0 and status_active=1 $internal_no_cond "; 
		$po_sql_res=sql_select($po_sql);
		foreach ($po_sql_res as $row)
		{
			$buyer_po_arr[$row[csf("id")]]['grouping']=$row[csf("grouping")];
			$buyer_po_id_arr[]=$row[csf("id")];
		}
		unset($po_sql_res);
        //$buyer_po_lib = return_library_array("select id, id as id2 from wo_po_break_down where grouping like '%$internal_no%'",'id','id2');
		if($internal_no !="")
		{
			$buyer_po_id = implode(",", $buyer_po_id_arr);
			if ($buyer_po_id=="")
			{
				echo "Not Found."; die;
			}
       	 if($buyer_po_id !="") $buyer_po_id_cond = " and b.buyer_po_id in($buyer_po_id)";
		 
		 
		/*$trims_buyer_po_id=implode(",", $buyer_po_id_arr);
		if($trims_buyer_po_id!='')
		{
			
			$trimsbuyerpoid=chop($trims_buyer_po_id,','); 
			$buyer_po_id_cond="";
			$trimsbuyerpoids=count(array_unique(explode(",",$trimsbuyerpoid)));
			if($db_type==2 && $trimsbuyerpoids>1000)
			{
				$buyer_po_id_cond=" and (";
				$trimsbuyerpoidArr=array_chunk(explode(",",$trimsbuyerpoid),999); 
				foreach($trimsbuyerpoidArr as $bpoids)
				{
					$bpoids=implode(",",$bpoids);
					$buyer_po_id_cond.=" b.buyer_po_id in($bpoids) or"; 
				}
				$buyer_po_id_cond=chop($buyer_po_id_cond,'or ');
				$buyer_po_id_cond.=")";
			}
			else
			{
				if($trimsbuyerpoid!="")
				{
					$bpoids=implode(",",array_unique(explode(",",$trimsbuyerpoid)));
					$buyer_po_id_cond=" and b.buyer_po_id in($bpoids)";
				}
				else { $buyer_po_id_cond ="";}
			}
		
		}*/
		}
    }
	//echo $buyer_po_id_cond; die;
	
	if($txt_order_no !="") $order_no_cond = " and b.order_no like('%$txt_order_no%')";
	
	//print_r($buyer_po_arr); die;
	
	$trims_order_sql="select a.id,a.subcon_job,a.receive_date,a.delivery_date,a.order_no as cust_order_no,a.party_id,a.within_group,a.currency_id,a.team_leader,a.team_member, b.item_group,b.buyer_buyer,b.section,b.sub_section,b.job_no_mst,b.order_no,b.booked_uom,b.order_uom,b.buyer_po_no,b.buyer_po_id,c.id as break_id,b.cust_style_ref, c.description,c.order_id,c.item_id,c.color_id,c.size_id,c.qnty,c.rate ,c.amount,c.booked_qty,c.delivery_status
	from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c
	where a.subcon_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id and a.entry_form=255 and a.company_id =$cbo_company_id  $buyer_po_id_cond $order_no_cond $where_con $delivery_status_con
	and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 ";
	// echo $trims_order_sql; //die;
	$trims_receive_id_arr=array();
	$trims_data_arr=array();	
	$result_trims_order_sql = sql_select($trims_order_sql);

	foreach($result_trims_order_sql as $row)
	{
		$key=$row[csf("id")].'*'.$row[csf("order_id")].'*'.$row[csf("section")].'*'.$row[csf("sub_section")].'*'.$row[csf("item_group")].'*'.$row[csf("item_id")].'*'.$row[csf("description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("booked_uom")].'*'.$row[csf("rate")];
		//Customer Name  	Cust Buyer	Cust. Work Order	Trims Job No	Style	Internal Ref	Section	Item Group	Order UOM	Item Description	Item Color

		
		$trims_data_arr[$key][qnty]+=$row[csf("qnty")];
		$trims_data_arr[$key][amount]+=$row[csf("amount")];
		$trims_data_arr[$key][booked_qty]+=$row[csf("booked_qty")];
		$trims_data_arr[$key][break_ids].=$row[csf("break_id")].',';
		
		$date_array[$key]=array(
		subcon_job=>$row[csf("subcon_job")],
		item_group=>$row[csf("item_group")],
		receive_date=>$row[csf("receive_date")],
		delivery_date=>$row[csf("delivery_date")],
		order_qty=>$trims_data_arr[$key][qnty],
		order_rate=>$row[csf("rate")],
		order_amount=>$trims_data_arr[$key][amount],
		booked_qty=>$trims_data_arr[$key][booked_qty],
		cust_order_no=>$row[csf("cust_order_no")],
		party_id=>$row[csf("party_id")],
		within_group=>$row[csf("within_group")],
		buyer_buyer=>$row[csf("buyer_buyer")],
		section=>$row[csf("section")],
		sub_section=>$row[csf("sub_section")],
		booked_uom=>$row[csf("booked_uom")],
		order_uom=>$row[csf("order_uom")],
		description=>$row[csf("description")],
		delivery_status=>$row[csf("delivery_status")],
		order_no=>$row[csf("order_no")],
		buyer_po_no=>$row[csf("buyer_po_no")],
		currency_id=>$row[csf("currency_id")],
		team_leader=>$row[csf("team_leader")],
		team_member=>$row[csf("team_member")],
		break_ids=>$trims_data_arr[$key][break_ids],
		buyer_po_id=>$row[csf("buyer_po_id")],
		style=>$row[csf('cust_style_ref')],
		size_id=>$row[csf('size_id')]
		);
		$trims_receive_id_arr[$row[csf("id")]]=$row[csf("id")];
	}

	$trims_receive_id=implode(',',$trims_receive_id_arr);
		if($trims_receive_id!='')
		{
			
		$trimsreceiveid=chop($trims_receive_id,','); 
		$trimsreceiveid_cond="";
		$trimsreceive_ids=count(array_unique(explode(",",$trimsreceiveid)));
			if($db_type==2 && $trimsreceive_ids>1000)
			{
				$trimsreceiveid_cond=" and (";
				$trimsreceiveidArr=array_chunk(explode(",",$trimsreceiveid),999);
				foreach($trimsreceiveidArr as $ids)
				{
					$ids=implode(",",$ids);
					$trimsreceiveid_cond.=" a.received_id in($ids) or"; 
				}
				$trimsreceiveid_cond=chop($trimsreceiveid_cond,'or ');
				$trimsreceiveid_cond.=")";
			}
			else
			{
				if($trimsreceiveid!="")
				{
					$issue_ids=implode(",",array_unique(explode(",",$trimsreceiveid)));
					$trimsreceiveid_cond=" and a.received_id in($issue_ids)";
				}
				else { $trimsreceiveid_cond ="";}
			}
		
		}
		
	//Job-------------------------------	
	$trims_job_sql="select a.id,a.trims_job,a.received_no from trims_job_card_mst a where a.status_active=1 and a.is_deleted=0 $trimsreceiveid_cond "; 
	// echo $trims_job_sql;
	$trims_job_no_arr=array();	
	$result_trims_job_sql = sql_select($trims_job_sql);
	foreach($result_trims_job_sql as $row)
	{
		$trims_job_no_arr[$row[csf("received_no")]]=$row[csf("trims_job")];
		$trims_job_id_arr[$row[csf("id")]]=$row[csf("id")];
	}
		
		
		
		$trims_job_id=implode(',',$trims_job_id_arr);
		if($trims_job_id!='')
		{
			
			$trimsjobid=chop($trims_job_id,','); 
			$trimsjobid_cond="";
			$trimsjobids=count(array_unique(explode(",",$trimsjobid)));
			
			
			if($db_type==2 && $trimsjobids>1000)
			{
				$trimsjobid_cond=" and (";
				$trimsreceiveidArr=array_chunk(explode(",",$trimsjobid),999);
				foreach($trimsreceiveidArr as $jobids)
				{
					$jobids=implode(",",$jobids);
					$trimsjobid_cond.=" a.job_id in($jobids) or"; 
				}
				$trimsjobid_cond=chop($trimsjobid_cond,'or ');
				$trimsjobid_cond.=")";
			}
			else
			{
				if($trimsjobid!="")
				{
					$issue_ids=implode(",",array_unique(explode(",",$trimsjobid)));
					$trimsjobid_cond=" and a.job_id in($issue_ids)";
				}
				else { $trimsjobid_cond ="";}
			}
		
		}
		
	//production.................................
	$trims_production_sql="select a.section_id,c.sub_section,a.received_id,a.job_id,b.job_dtls_id,c.id as order_receive_dtls_id,c.item_description,c.color_id,c.size_id,c.uom,c.job_quantity,b.production_qty,b.qc_qty
	from trims_production_mst a,trims_production_dtls b,trims_job_card_dtls c
	where a.id=b.mst_id and c.id=b.job_dtls_id $trimsjobid_cond $trimsreceiveid_cond and a.entry_form=269 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $prod_where_con";

	// echo $trims_production_sql;

	$trims_production_data_arr=array();	
	$result_trims_production_sql = sql_select($trims_production_sql);
	foreach($result_trims_production_sql as $row)
	{
		$key=$row[csf("received_id")].'*'.$row[csf("section_id")].'*'.$row[csf("sub_section")].'*'.$row[csf("item_description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("uom")];
		
		$trims_production_data_arr[$key][qc_qty]+=$row[csf("qc_qty")];
		$trims_production_data_arr[$key][job_quantity]+=$row[csf("job_quantity")];
		$trims_production_data_arr[$key][production_qty]+=$row[csf("production_qty")];
	}
	
	//Delivery.................................
	$trims_delivery_sql="select a.delivery_date,a.received_id,b.delevery_qty,b.order_receive_rate,c.uom,b.section,c.sub_section,b.item_group,b.color_id,b.size_id,b.description,b.delevery_status,b.break_down_details_id
	from trims_delivery_mst a, trims_delivery_dtls b, trims_job_card_dtls c
	where a.id=b.mst_id and c.id=b.job_dtls_id $trimsreceiveid_cond and a.entry_form=208 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $delivery_where_con order by a.delivery_date"; 

	$trims_delivery_data_arr=array();	
	$result_trims_delivery_sql = sql_select($trims_delivery_sql);
	foreach($result_trims_delivery_sql as $row)
	{
		$key=$row[csf("item_group")].'*'.$row[csf("received_id")].'*'.$row[csf("section")].'*'.$row[csf("sub_section")].'*'.$row[csf("description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("uom")];
		
		$trims_delivery_data_arr[$key][delevery_qty]+=$row[csf("delevery_qty")];
		$trims_delivery_data_arr[$key][delevery_val]+=$row[csf("delevery_qty")]*$row[csf("order_receive_rate")];
		$trims_delivery_data_arr[$key][delevery_last_date]=$row[csf("delivery_date")];
		$trims_delivery_data_arr[$key][delevery_status]=$row[csf("delevery_status")];
		$trims_delivery_data_arr[$key][break_ids].=$row[csf("break_down_details_id")].',';
	}
	// echo "<pre>";
	// print_r($trims_delivery_data_arr);
	// echo "</pre>";
	//Bill.................................
	//select SECTION,ITEM_DESCRIPTION,COLOR_ID,SIZE_ID,ORDER_UOM,TOTAL_DELV_QTY,b.QUANTITY     from TRIMS_BILL_MST a, TRIMS_BILL_dtls b where a.id=b.mst_id and a.ENTRY_FORM=276	
	//bill.................................
	/*$trims_bill_sql="select d.received_id,b.section,c.sub_section,b.item_description,b.color_id,b.size_id,c.uom,total_delv_qty,b.quantity,b.bill_amount,b.id from trims_bill_mst a, trims_bill_dtls b,trims_job_card_dtls c, trims_job_card_mst d  where a.id=b.mst_id and c.id=b.job_dtls_id and c.job_no_mst=d.trims_job and a.entry_form=276 and d.received_id in(".implode(',',$trims_receive_id_arr).") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";*/
	$trims_bill_sql="select a.received_id,b.section,c.sub_section,b.item_description,b.color_id,b.size_id,c.uom,total_delv_qty,b.quantity,b.bill_amount,b.id
	from trims_bill_mst d, trims_bill_dtls b,trims_job_card_dtls c, trims_job_card_mst a
	where d.id=b.mst_id and c.id=b.job_dtls_id and c.job_no_mst=a.trims_job and d.entry_form=276 $trimsreceiveid_cond and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $bill_where_con";

	$trims_bill_data_arr=array();
	$result_trims_bill_sql = sql_select($trims_bill_sql);
	foreach($result_trims_bill_sql as $row)
	{
		$key=$row[csf("received_id")].'*'.$row[csf("section")].'*'.$row[csf("sub_section")].'*'.$row[csf("item_description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("uom")];
		
		$trims_bill_data_arr[$key][bill_qty]+=$row[csf("quantity")];
		$trims_bill_data_arr[$key][bill_val]+=$row[csf("bill_amount")];
		$trims_bill_data_arr[$key][dtls_ids].=$row[csf("id")].',';
	}
	//echo "<pre>";
	//print_r($trims_bill_data_arr);
	$width=3600;
	ob_start();
	?>
	<div align="center" style="height:auto; width:<?php echo $width;?>px; margin:0 auto; padding:0;">
		<table width="<?php echo $width;?>" cellpadding="0" cellspacing="0" id="caption" align="center">
			<thead class="form_caption" >
				<tr>
					<td colspan="36" align="center" style="font-size:20px;"><?php echo $companyArr[$cbo_company_id]; ?></td>
				</tr>
				<tr>
					<td colspan="36" align="center" style="font-size:14px; font-weight:bold" ><?php echo $report_title; ?></td>
				</tr>
				<tr>
					<td colspan="36" align="center" style="font-size:14px; font-weight:bold">
						<?php echo " From : ".$txt_date_from." To : ". $txt_date_to ;?>
					</td>
				</tr>
			</thead>
		</table>
        <table border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<?php echo $width;?>" rules="all" id="rpt_table_header" align="left">
			<thead>
				<th width="35">SL</th>
                <th width="100">Customer Name</th>
                <th width="100">Cust. Buyer</th>
                <th width="100">Cust. Work Order</th>
                <th width="100">Trims Job No</th>
                <th width="100">Style</th>
                <th width="100">Internal Ref</th>
                <th width="100">Section</th>
                <th width="100">Item Group</th>
                <th width="100">Order UOM</th>
                <th width="130">Item Description</th>
                <th width="100">Item Color</th>
                <th width="100">Item Size</th>
                <th width="100">Req. Qty/Ord Rcv</th>
                <th width="100">Rate</th>
                <th width="100">Ord Rcv Currency</th>
                <th width="100">Req. Value [Tk]</th>
                <th width="100">Prod. Qty</th>
                <th width="100">Prod. Value [Tk]</th>
                <th width="100">Prod. Bal. Qty</th>
                <th width="100">Prod. Bal. Value [Tk]</th>
                <th width="100">QC. Qty</th>
                <th width="100">QC. Bal. Qty</th>
                <th width="100">Deli. Qty.</th>
                <th width="100">Deli. Value [Tk]</th>
                <th width="100">Deli. Balance Qty</th>
                <th width="100">Deli. Balance Value [Tk]</th>
                <th width="100">Bill Qty</th>
                <th width="100">Bill Amount [Tk]</th>
                <th width="100">Bill Banalce Qty</th>
                <th width="100">Bill Balance Amount [Tk]</th>
			</thead>
			<tbody>
		<!-- <div style="width:<?php // echo $width;?>px; float:left; overflow-y:scroll;" id="scroll_body">
		<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="<?php echo $width;?>" rules="all" align="left"> -->
            <?php 
			$i=1;
			$total_order_qty=0;$total_order_val=0;$total_booked_qty=0;$total_production_qty=0;

			$con_rowspan_arr=array(); $pi_rowspan_arr=array(); $pi_dtls_rowspan_arr=array();
			foreach($date_array as $cont_id =>$conArray)
			{
				$con_rowspan=0;		
				foreach( $conArray as $con_dtls_id =>$piArray )
			    {   	
			    	$con_dtls_rowspan=0;
					foreach( $piArray as $pi_id =>$piRowArray )
			    	{   
			    		$pi_rowspan=0;		
			    		foreach( $piRowArray as $pi_dtls_id =>$piRow )
			        	{
			        		$con_rowspan++;
			        		$con_dtls_rowspan++;
			            	$pi_rowspan++;
			            }
			            $pi_dtls_rowspan_arr[$cont_id][$con_dtls_id][$pi_id]=$pi_rowspan;
				    }
				    $pi_rowspan_arr[$cont_id][$con_dtls_id]=$con_dtls_rowspan;
				}
				$con_rowspan_arr[$cont_id]=$con_rowspan;
			}

			foreach($date_array as $keysss=>$row)
			{
				list($id,$order_id,$section,$sub_section,$item_group,$item_id,$description,$color_id,$size_id,$booked_uom,$rate)=explode('*',$keysss);
				$key=$id.'*'.$section.'*'.$sub_section.'*'.$description.'*'.$color_id.'*'.$size_id.'*'.$booked_uom;
				//$production_qty_on_order_parcent=($trims_production_data_arr[$key][qc_qty]/$trims_production_data_arr[$key][job_quantity])*$row[order_qty];
				$production_qty_on_order_parcent=$trims_production_data_arr[$key][qc_qty];
				
				//WORK ORDER NO : 161
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
				$party=($row[within_group]==1)?$companyArr[$row[party_id]]:$buyerArr[$row[party_id]];
				$buyer_buyer=($row[within_group]==1)?$buyerArr[$row[buyer_buyer]]:$row[buyer_buyer];
			
				//$row[delivery_status]=$trims_delivery_data_arr[$item_group.'*'.$key][delevery_status];
				
				// if($row[delivery_status]==2){$bgcolor="#FFCC66";}
				// elseif($row[delivery_status]==3){$bgcolor="#8CD59C";}
				// else{$row[delivery_status]=1;}
			
			//---------------------------------------
			$total_order_qty+=$row[order_qty];
			$total_order_val+=$row[order_amount];
			$total_booked_qty+=$row[booked_qty];
			$total_production_qty+=$production_qty_on_order_parcent;
			
			if($row["currency_id"]==1){
				$row[order_rate]=$row[order_rate]/$currency_rate;
				$row[order_amount]=$row[order_amount]/$currency_rate;
			}
			$delivery_amt=number_format($row[order_rate]*$trims_delivery_data_arr[$item_group.'*'.$key][delevery_qty],2);
			$bill_amt=number_format($row[order_rate]*$trims_bill_data_arr[$key][bill_qty],2);
			$receive_ids=implode(',',$trims_receive_id_arr);
			if($row["within_group"]==1) $orderSource='Internal'; else $orderSource='External';
			?>
            <tr bgcolor="<?php echo $bgcolor; ?>" onclick="change_color('tr_<?php echo $i; ?>','<?php echo $bgcolor; ?>')" id="tr_<?php echo $i; ?>">
                <td width="35" align="center"><?php echo $i;?></td>
                <td width="100"><p><?php echo $party;?></p></td>
                <td width="100"><p><?php echo $buyer_buyer;?></p></td>
                <td width="100" align="center"><p><?php echo $row[cust_order_no];?></p></td>
                <td width="100" align="center"><p><?php echo $row[subcon_job]; ?></p></td>
                <td width="100" align="center"><p><?php echo $row[style]; ?></p></td>
                <td width="100"><p><?php echo $buyer_po_arr[$row[buyer_po_id]]['grouping']; ?></p></td>
                <td width="100"><p><?php echo $trims_section[$row[section]];?></p></td>
                <td width="100"><p><?php echo $trimsGroupArr[$row[item_group]];?></p></td>
                <td width="100" align="center"><?php echo $unit_of_measurement[$row[order_uom]];?></td>
                <td width="130"><p><?php echo $row[description];?></p></td>
                <td width="100"><p><?php echo $colorNameArr[$color_id];?></p></td>
                <td width="100" align="center"><p><?php echo $sizeArr[$row['size_id']]; ?></p></td>
                <td width="100" align="right"><?php echo number_format($row[order_qty],0); ?></td>
                <td width="100" align="right"><?php echo number_format($row[order_rate],4);?></td>
                <td width="100" align="center"><p><?php echo $currency[$row['currency_id']]; ?></p></td>
                <td width="100" align="center"><p></p></td>
                <td width="100" align="center"><p><?php echo $trims_production_data_arr[$key][production_qty]; ?></p></td>
                <td width="100" align="center"><p></p></td>
                <td width="100" align="center"><p><?php echo number_format($row[booked_qty]-$production_qty_on_order_parcent,0); ?></p></td>
                <td width="100" align="center"><p></p></td>
                <td width="100" align="center"><p><?php echo $trims_production_data_arr[$key][qc_qty]; ?></p></td>
                <td width="100" align="center"><p></p></td>
                <td width="100" align="right"><p><?php echo number_format($trims_delivery_data_arr[$item_group.'*'.$key][delevery_qty],0);?></p></td>
                <td width="100" align="right"><p>
	                	<?php
						//$DelvBalanceQty=number_format($row[order_qty]-$trims_delivery_data_arr[$item_group.'*'.$key][delevery_qty],0);
						$DelvBalanceQty=$row[order_qty]-$trims_delivery_data_arr[$item_group.'*'.$key][delevery_qty];
						if($DelvBalanceQty<=1){
						 	echo $DelvBalanceQty=0;
						}else{
							$DelvBalanceQty=number_format($row[order_qty]-$trims_delivery_data_arr[$item_group.'*'.$key][delevery_qty],0);
							echo $DelvBalanceQty;
						}
						// echo number_format($row[order_qty],0)-number_format($trims_delivery_data_arr[$item_group.'*'.$key][delevery_qty],0);
						//echo number_format($row[booked_qty]-$trims_delivery_data_arr[$item_group.'*'.$key][delevery_qty],0);
						?>
					</p>
				</td>
				<td width="100" align="right">
				<?php
                if($delivery_amt>0){
                	?><a href="##" onclick="fnc_amount_details('<?php echo $trims_delivery_data_arr[$item_group.'*'.$key][break_ids];?>','<?php echo $row[order_rate];?>','delivery_popup')"><p><?php echo $delivery_amt;?></p></a><?php
                }else{
                	?><?php echo '0'; ?><?php
                }?>
                </td>
                <td width="100" align="center"><p></p></td>
                <td width="100" align="right"><p><?php 
                	if($trims_bill_data_arr[$key][bill_qty] > 0) {
                		echo $trims_bill_data_arr[$key][bill_qty];
                	} else {
                		echo '0';
                	}
                ?></p></td>
                <td width="100" align="right">
                <?php
                if($bill_amt>0){
                	?><a href="##" onclick="fnc_amount_details('<?php echo chop($trims_bill_data_arr[$key][dtls_ids],',');?>','<?php echo $row[order_rate];?>','bill_popup')"><p><?php echo $bill_amt;?></p></a><?
                }else{
                	?><?php echo '0'; ?><?php
                }?>
                </td>
                <td width="100" align="right"><?php echo number_format($trims_delivery_data_arr[$item_group.'*'.$key][delevery_qty]-$trims_bill_data_arr[$key][bill_qty]);?></td>
                <td width="100"><?php echo number_format(($trims_delivery_data_arr[$item_group.'*'.$key][delevery_qty]-$trims_bill_data_arr[$key][bill_qty])*$row[order_rate],2); ?></td>
            </tr>
            <?php 
			$i++;
			} ?>
			</tbody>
		</table>
		</div>
		<!-- <table width="<?php // echo $width;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer" align="left">
			<tfoot>
		                <th width="35"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="130"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
			</tfoot>
		</table> -->
    </div>
    <?
    $html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$report_type";
    exit();
}

?>