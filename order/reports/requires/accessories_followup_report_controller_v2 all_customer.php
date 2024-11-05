<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');

//--------------------------------------------------------------------------------------------------------------------


if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );   	 
	exit();
}
$tmplte=explode("**",$data);
if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;


if($action=="report_generate")
{ 
	$company_library=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	//$costing_per_id_library=return_library_array( "select job_no, costing_per from wo_pre_cost_mst", "job_no", "costing_per");
	$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
	//$color_name_library=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	$country_name_library=return_library_array( "select id, country_name from lib_country", "id", "country_name"  );
	//$order_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number"  );
	$lib_supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_name=str_replace("'","",$cbo_company_name);
	$serch_by=str_replace("'","",$cbo_search_by);
	$buyer_id_cond="";
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	
	//echo '=='.$cbo_company_name.'___'.$txt_job_no.'___'.$txt_order_no.'___';
	
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_job_no=trim($txt_job_no);
	if($txt_job_no !="" || $txt_job_no !=0)
	{
		//$year = substr(str_replace("'","",$cbo_year_selection), -2); 
		//$job_no=$company_library[$company_name]."-".$year."-".str_pad($txt_job_no, 5, 0, STR_PAD_LEFT);
		//$jobcond="and a.job_no='".$job_no."'";
		$jobcond="and a.job_no_prefix_num='".$txt_job_no."'";

	}
	else
	{
		$jobcond="";	
	}
	
	
	if(str_replace("'","",$cbo_item_group)=="")
	{
		$item_group_cond="";
	}
	else
	{
		$item_group_cond="and e.trim_group in(".str_replace("'","",$cbo_item_group).")";
	}
	
	
	$date_cond='';
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		$start_date=(str_replace("'","",$txt_date_from));
		$end_date=(str_replace("'","",$txt_date_to));
		$date_cond="and c.country_ship_date between '$start_date' and '$end_date'";
		
	}
	if (str_replace("'","",$txt_job_no)=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in (".str_replace("'","",$txt_job_no).") ";
	
	if(str_replace("'","",$txt_style_ref)!="") $style_ref_cond=" and a.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'"; else $style_ref_cond="";
	if(str_replace("'","",$txt_order_no)!="")
	{
		$ordercond=" and b.po_number like '%".str_replace("'","",$txt_order_no)."%'"; 
	}
	else
	{
		$ordercond="";
	}
	$file_no=str_replace("'","",$txt_file_no);
	$internal_ref=str_replace("'","",$txt_internal_ref);
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' "; 
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' "; 
	//echo $file_no_cond.'=='.$internal_ref_cond;die;

  if(str_replace("'","",$cbo_search_by)==1)
  {
	if($template==1)
	{
		ob_start();
	?>
		<div style="width:2600px">
		<fieldset style="width:100%;">	
			<table width="2600">
				<tr class="form_caption">
					<td colspan="27" align="center">Accessories Followup Report</td>
				</tr>
				<tr class="form_caption">
					<td colspan="27" align="center"><? echo $company_library[$company_name]; ?></td>
				</tr>
			</table>
			<table class="rpt_table" width="2560" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<th width="30">SL</th>
					<th width="50">Buyer</th>
					<th width="100">Job No</th>
                     <th width="100">Style Ref</th>
					<th width="100">Internal Ref</th>
                    <th width="100">File No</th>
                   
					<th width="90">Order No</th>
					<th width="80">Order Qnty</th>
					<th width="50">UOM</th>
					<th width="80">Qnty (Pcs)</th>
					<th width="80">Shipment Date</th>
					<th width="100">Trims Name</th>
                    <th width="100">Remark</th>
					<th width="100">Brand/Sup Ref</th>
					<th width="60">Appr Req.</th>
					<th width="80">Approve Status</th>
                    <th width="100">Item Entry Date</th>
					<th width="100">Req Qnty</th>
					<th width="100">Pre Costing Value</th>
					<th width="90">WO Qnty</th>
                    <th width="60">Trims UOM</th>
                    <th width="100">WO Value</th>
                    <th width="150">Supplier</th>
                    <th width="70">WO Delay Days</th>
					<th width="90">In-House Qnty</th>
                    <th width="90">In-House Value</th>
					<th width="90">Receive Balance</th>
					<th width="90">Issue to Prod.</th>
					<th>Left Over/Balance</th>
				</thead>
			</table>
			<div style="width:2580px; max-height:400px; overflow-y:scroll" id="scroll_body">
				<table class="rpt_table" width="2560" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
	<?
	$conversion_factor_array=array();
	$conversion_factor=sql_select("select id,trim_uom,conversion_factor from lib_item_group  ");
	foreach($conversion_factor as $row_f)
	{
	 $conversion_factor_array[$row_f[csf('id')]]['con_factor']=$row_f[csf('conversion_factor')];
	 $conversion_factor_array[$row_f[csf('id')]]['cons_uom']=$row_f[csf('trim_uom')];
	}
	$conversion_factor=array();
	$app_sql=sql_select("select job_no_mst,accessories_type_id,approval_status from wo_po_trims_approval_info");
	$app_status_arr=array();
	foreach($app_sql as $row)
	{
		$app_status_arr[$row[csf("job_no_mst")]][$row[csf("accessories_type_id")]]=$row[csf("approval_status")];
	}
	$app_sql=array();
	
	$sql_po_qty_country_wise_arr=array();
	$po_job_arr=array();
	$sql_po_qty_country_wise=sql_select("select  b.id,b.job_no_mst,c.country_id, sum(c.order_quantity) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst  and b.id=c.po_break_down_id   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond group by   b.id,b.job_no_mst,c.country_id order by b.id,b.job_no_mst,c.country_id");
	foreach( $sql_po_qty_country_wise as $sql_po_qty_country_wise_row)
	{
	$sql_po_qty_country_wise_arr[$sql_po_qty_country_wise_row[csf('id')]][$sql_po_qty_country_wise_row[csf('country_id')]]=$sql_po_qty_country_wise_row[csf('order_quantity_set')];
	$po_job_arr[$sql_po_qty_country_wise_row[csf('id')]]=$sql_po_qty_country_wise_row[csf('job_no_mst')];
	}
	$sql_po_qty_country_wise=array();
 
    
	$po_data_arr=array();
	$po_id_string="";
	$today=date("Y-m-d");
	/*$sql_query=sql_select("select a.buyer_name,a.job_no,a.job_no_prefix_num,style_ref_no,b.file_no,b.grouping, b.id,b.po_number,a.order_uom,sum(distinct b.po_quantity) as po_quantity,a.total_set_qnty  ,sum(distinct b.po_quantity*a.total_set_qnty) as po_quantity_psc ,sum(c.order_quantity) as order_quantity ,sum(c.order_quantity/a.total_set_qnty) as order_quantity_set,  b.pub_shipment_date,
	d.costing_per,
	e.id as trim_dtla_id,
	e.trim_group,
	e.remark,
	e.description,
	e.brand_sup_ref,
	e.cons_uom,
	e.cons_dzn_gmts,
	e.rate,
	e.amount,
	e.apvl_req,
	e.nominated_supp,
	e.insert_date,
	f.cons,
	f.country_id,
	f.cons as cons_cal
	from 
	wo_po_details_master a, 
	wo_po_break_down b,
	wo_po_color_size_breakdown c
	left join 
	wo_pre_cost_trim_co_cons_dtls f 
	on
	c.job_no_mst=f.job_no and
	c.po_break_down_id=f.po_break_down_id and
	f.cons > 0
	join
	wo_pre_cost_trim_cost_dtls e
	on 
	f.job_no=e.job_no  and
	e.id=f.wo_pre_cost_trim_cost_dtls_id 
	$item_group_cond
	join 
	wo_pre_cost_mst d
	on
	e.job_no =d.job_no
	where 
	a.job_no=b.job_no_mst   and 
	a.job_no=c.job_no_mst   and 
	b.id=c.po_break_down_id and 
	a.is_deleted=0          and 
	a.status_active=1       and 
	b.is_deleted=0          and 
	b.status_active=1       and 
	c.is_deleted=0          and 
	c.status_active=1       and 
	a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond  $ordercond $file_no_cond $internal_ref_cond
	group by a.buyer_name,a.job_no,a.job_no_prefix_num,style_ref_no,b.file_no,b.grouping, b.id,b.po_number,a.order_uom,a.total_set_qnty,b.pub_shipment_date,d.costing_per,
	e.id,
	e.trim_group,
	e.remark,
	e.description,
	e.brand_sup_ref,
	e.cons_uom,
	e.cons_dzn_gmts,
	e.rate,
	e.amount,
	e.apvl_req,
	e.nominated_supp,
	e.insert_date,
	f.cons,
	f.pcs,
	f.country_id
	order by b.id, e.trim_group
	");*/
	$sql_query=sql_select("select a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, b.file_no, b.grouping, b.id, b.po_number, a.order_uom, sum(c.order_quantity) as order_quantity, sum(c.order_quantity/a.total_set_qnty) as order_quantity_set, b.pub_shipment_date,
	d.costing_per,
	e.id as trim_dtla_id,
	e.trim_group,
	e.remark,
	e.description,
	e.brand_sup_ref,
	e.cons_uom,
	e.cons_dzn_gmts,
	e.rate,
	e.amount,
	e.apvl_req,
	e.nominated_supp,
	e.insert_date,
	f.cons,
	f.country_id,
	f.cons as cons_cal
	from 
	wo_po_details_master a, 
	wo_po_break_down b,
	wo_po_color_size_breakdown c
	left join 
	wo_pre_cost_trim_co_cons_dtls f 
	on
	c.job_no_mst=f.job_no and
	c.po_break_down_id=f.po_break_down_id and
	f.cons > 0
	join
	wo_pre_cost_trim_cost_dtls e
	on 
	f.job_no=e.job_no  and
	e.id=f.wo_pre_cost_trim_cost_dtls_id 
	$item_group_cond
	join 
	wo_pre_cost_mst d
	on
	e.job_no =d.job_no
	where 
	a.job_no=b.job_no_mst   and 
	a.job_no=c.job_no_mst   and 
	b.id=c.po_break_down_id and 
	a.is_deleted=0          and 
	a.status_active=1       and 
	b.is_deleted=0          and 
	b.status_active=1       and 
	c.is_deleted=0          and 
	c.status_active=1       and 
	a.company_name=$company_name $buyer_id_cond ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $date_cond $style_ref_cond $jobcond  $ordercond $file_no_cond $internal_ref_cond
	group by a.buyer_name,a.job_no,a.job_no_prefix_num,style_ref_no,b.file_no,b.grouping, b.id,b.po_number,a.order_uom,a.total_set_qnty,b.pub_shipment_date,d.costing_per,
	e.id,
	e.trim_group,
	e.remark,
	e.description,
	e.brand_sup_ref,
	e.cons_uom,
	e.cons_dzn_gmts,
	e.rate,
	e.amount,
	e.apvl_req,
	e.nominated_supp,
	e.insert_date,
	f.cons,
	f.pcs,
	f.country_id
	order by b.id, e.trim_group
	");//sum(distinct b.po_quantity) as po_quantity,a.total_set_qnty  ,sum(distinct b.po_quantity*a.total_set_qnty) as po_quantity_psc ,
	
				$tot_rows=count($sql_query);
				$i=1;
				foreach($sql_query as $row)
				{
					      
						   
						    $dzn_qnty=0;
							if($row[csf('costing_per')]==1)
							{
								$dzn_qnty=12;
							}
							else if($row[csf('costing_per')]==3)
							{
								$dzn_qnty=12*2;
							}
							else if($row[csf('costing_per')]==4)
							{
								$dzn_qnty=12*3;
							}
							else if($row[csf('costing_per')]==5)
							{
								$dzn_qnty=12*4;
							}
							else
							{
								$dzn_qnty=1;
							}
							
							
							 $po_qty=0;
							 if($row[csf('country_id')]==0)
							 {
								$po_qty=$row[csf('order_quantity')];
							 }
							 else
							 {
								$country_id= explode(",",$row[csf('country_id')]);
								for($cou=0;$cou<=count($country_id); $cou++)
								{
								$po_qty+=$sql_po_qty_country_wise_arr[$row[csf('id')]][$country_id[$cou]];
								}
							 }
							 
							 $req_qnty=($row[csf('cons_cal')]/$dzn_qnty)*$po_qty;
							 $req_value= $row[csf('rate')]*$req_qnty;
							 
							 $po_data_arr[$row[csf('id')]][job_no]=$row[csf('job_no')];
							 $po_data_arr[$row[csf('id')]][buyer_name]=$row[csf('buyer_name')];
							 $po_data_arr[$row[csf('id')]][job_no_prefix_num]=$row[csf('job_no_prefix_num')];
							 $po_data_arr[$row[csf('id')]][style_ref_no]=$row[csf('style_ref_no')];
							 
							 $po_data_arr[$row[csf('id')]][grouping]=$row[csf('grouping')];
							 $po_data_arr[$row[csf('id')]][file_no]=$row[csf('file_no')];
							 $po_data_arr[$row[csf('id')]][order_uom]=$row[csf('order_uom')];
							 $po_data_arr[$row[csf('id')]][po_id]=$row[csf('id')];
							 $po_data_arr[$row[csf('id')]][po_number]=$row[csf('po_number')];
							 $po_data_arr[$row[csf('id')]][order_quantity_set]=$row[csf('order_quantity_set')];
							 $po_data_arr[$row[csf('id')]][order_quantity]=$row[csf('order_quantity')];
							 $po_data_arr[$row[csf('id')]][pub_shipment_date]=change_date_format($row[csf('pub_shipment_date')]);
							 $po_id_string.=$row[csf('id')].",";
							 
							 $po_data_arr[$row[csf('id')]][trim_dtla_id][$row[csf('trim_dtla_id')]]=$row[csf('trim_dtla_id')];// for rowspan
							 $po_data_arr[$row[csf('id')]][trim_group][$row[csf('trim_group')]]=$row[csf('trim_group')];
							
							 
							 $po_data_arr[$row[csf('id')]][$row[csf('trim_group')]][$row[csf('trim_dtla_id')]]=$row[csf('trim_dtla_id')]; // for rowspannn
							 $po_data_arr[$row[csf('id')]][trim_group_dtls][$row[csf('trim_dtla_id')]]=$row[csf('trim_group')];
							 
							
							  $po_data_arr[$row[csf('id')]][remark][$row[csf('trim_dtla_id')]]=$row[csf('remark')];
							  
							 $po_data_arr[$row[csf('id')]][brand_sup_ref][$row[csf('trim_dtla_id')]]=$row[csf('brand_sup_ref')];
							 $po_data_arr[$row[csf('id')]][apvl_req][$row[csf('trim_dtla_id')]]=$row[csf('apvl_req')];
							 $po_data_arr[$row[csf('id')]][insert_date][$row[csf('trim_dtla_id')]]=$row[csf('insert_date')];
							 $po_data_arr[$row[csf('id')]][req_qnty][$row[csf('trim_dtla_id')]]+=$req_qnty;
							 $po_data_arr[$row[csf('id')]][req_value][$row[csf('trim_dtla_id')]]+=$req_value;
							 $po_data_arr[$row[csf('id')]][cons_uom][$row[csf('trim_dtla_id')]]=$row[csf('cons_uom')];
							 
							 $po_data_arr[$row[csf('id')]][trim_group_from][$row[csf('trim_dtla_id')]]="Pre_cost";
							 
							 $po_data_arr[$row[csf('id')]][rate][$row[csf('trim_dtla_id')]]=$row[csf('rate')];
							 $po_data_arr[$row[csf('id')]][description][$row[csf('trim_dtla_id')]]=$row[csf('description')];
							 $po_data_arr[$row[csf('id')]][country_id][$row[csf('trim_dtla_id')]]=$row[csf('country_id')];
							 
							// $style_data_arr[$row[csf('job_no')]][wo_qnty][$row[csf('trim_dtla_id')]]+=$wo_qnty;
							// $style_data_arr[$row[csf('job_no')]][amount][$row[csf('trim_dtla_id')]]+=$amount;
							// $style_data_arr[$row[csf('job_no')]][wo_date][$row[csf('trim_dtla_id')]]=$wo_date;
							// $style_data_arr[$row[csf('job_no')]][wo_qnty_trim_group][$row[csf('trim_group')]]+=$wo_qnty;
				}
				$sql_query=array();
				$po_id_string=rtrim($po_id_string,",");
				if($po_id_string=="")
				{
				echo "<strong style='color:red;font-size:30px'>No Data found</strong>";
				die;
				}
				$po_id=array_chunk(array_unique(explode(",",$po_id_string)),1000, true);
				 $order_cond="";
				   $ji=0;
				   foreach($po_id as $key=> $value)
				   {
					   if($ji==0)
					   {
						$order_cond=" and b.po_break_down_id  in(".implode(",",$value).")"; 
						$order_cond1=" and b.po_breakdown_id  in(".implode(",",$value).")"; 
						$order_cond2=" and d.po_breakdown_id  in(".implode(",",$value).")"; 
					   }
					   else
					   {
						$order_cond.=" or b.po_break_down_id  in(".implode(",",$value).")";
						$order_cond1.=" or b.po_breakdown_id  in(".implode(",",$value).")";
						$order_cond2.=" or d.po_breakdown_id  in(".implode(",",$value).")";
					   }
					   $ji++;
				   }
				 $po_id=array();
				
				if($db_type==2)
				{
				  $wo_sql_without_precost=sql_select("select min(a.booking_date) as booking_date ,b.job_no,LISTAGG(CAST(a.booking_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as booking_no,LISTAGG(CAST(a.supplier_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as supplier_id, b.po_break_down_id, b.trim_group,b.pre_cost_fabric_cost_dtls_id, sum(b.wo_qnty) as wo_qnty,sum(b.amount/b.exchange_rate) as amount,sum(b.rate) as rate from wo_booking_mst a, wo_booking_dtls b where a.item_category=4 and a.booking_no=b.booking_no  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name $order_cond group by b.po_break_down_id,b.trim_group,b.job_no,b.pre_cost_fabric_cost_dtls_id");//and item_from_precost=2
				}
				else if($db_type==0)
				{
				  $wo_sql_without_precost=sql_select("select min(a.booking_date) as booking_date ,b.job_no,group_concat(a.booking_no) as booking_no,group_concat(a.supplier_id) as supplier_id, b.po_break_down_id, b.trim_group,b.pre_cost_fabric_cost_dtls_id,sum(b.wo_qnty) as wo_qnty,sum(b.amount/b.exchange_rate) as amount,sum(b.rate) as rate from wo_booking_mst a, wo_booking_dtls b where a.item_category=4 and a.booking_no=b.booking_no  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name $order_cond  group by b.po_break_down_id,b.trim_group,b.job_no,b.pre_cost_fabric_cost_dtls_id");//and item_from_precost=2
				}
				$style_data_arr1=array();
				foreach($wo_sql_without_precost as $wo_row_without_precost)
				{
					
					$conversion_factor_rate=$conversion_factor_array[$wo_row_without_precost[csf('trim_group')]]['con_factor'];
					$cons_uom=$conversion_factor_array[$wo_row_without_precost[csf('trim_group')]]['cons_uom'];
					$booking_no=$wo_row_without_precost[csf('booking_no')];
					$supplier_id=$wo_row_without_precost[csf('supplier_id')];
					$wo_qnty=$wo_row_without_precost[csf('wo_qnty')]*$conversion_factor_rate;
					$amount=$wo_row_without_precost[csf('amount')];
					$wo_date=$wo_row_without_precost[csf('booking_date')];
					
					if($wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')] =="" || $wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')] ==0)
					{
					    $trim_dtla_id=max($po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][trim_dtla_id])+1;
						$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][trim_dtla_id][$trim_dtla_id]=$trim_dtla_id;// for rowspan
					    $po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][trim_group][$wo_row_without_precost[csf('trim_group')]]=$wo_row_without_precost[csf('trim_group')];
				        $po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][$wo_row_without_precost[csf('trim_group')]][$trim_dtla_id]=$trim_dtla_id;// for rowspannn
						$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][trim_group_dtls][$trim_dtla_id]=$wo_row_without_precost[csf('trim_group')];
						$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][cons_uom][$trim_dtla_id]=$cons_uom;
						
						$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][trim_group_from][$trim_dtla_id]="Booking Without Pre_cost";
					}
					else
					{
						$trim_dtla_id=$wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')];
						
					}
					//$style_data_arr[$wo_row_without_precost[csf('job_no')]][trim_dtla_id][$trim_dtla_id]=$trim_dtla_id;// for rowspan
					//$style_data_arr[$wo_row_without_precost[csf('job_no')]][trim_group][$wo_row_without_precost[csf('trim_group')]]=$wo_row_without_precost[csf('trim_group')];
				   // $style_data_arr[$wo_row_without_precost[csf('job_no')]][$wo_row_without_precost[csf('trim_group')]][$trim_dtla_id]=$trim_dtla_id;// for rowspannn
				    //$style_data_arr[$wo_row_without_precost[csf('job_no')]][trim_group_dtls][$trim_dtla_id]=$wo_row_without_precost[csf('trim_group')];
					
					$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][wo_qnty][$trim_dtla_id]+=$wo_qnty;
					$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][amount][$trim_dtla_id]+=$amount;
					$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][wo_date][$trim_dtla_id]=$wo_date;
					$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][wo_qnty_trim_group][$wo_row_without_precost[csf('trim_group')]]+=$wo_qnty;
					
					$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][booking_no][$trim_dtla_id]=$booking_no;
					$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][supplier_id][$trim_dtla_id]=$supplier_id;
					$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][conversion_factor_rate][$trim_dtla_id]=$conversion_factor_rate;
					
				
					 
				}
				$wo_sql_without_precost=array();
				
				$receive_qty_data=sql_select("select b.po_breakdown_id, a.item_group_id,sum(b.quantity) as quantity,a.rate   from  inv_receive_master c,product_details_master d,inv_trims_entry_dtls a , order_wise_pro_details b where a.mst_id=c.id and a.trans_id=b.trans_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $order_cond1  group by b.po_breakdown_id, a.item_group_id,a.rate order by a.item_group_id ");
				 
				
				
				foreach($receive_qty_data as $row)
				{
					if($po_data_arr[$row[csf('po_breakdown_id')]][trim_group][$row[csf('item_group_id')]]=="" || $po_data_arr[$row[csf('po_breakdown_id')]][trim_group][$row[csf('item_group_id')]]==0)
					{
						$cons_uom=$conversion_factor_array[$row[csf('item_group_id')]]['cons_uom'];
						$trim_dtla_id=max($po_data_arr[$row[csf('po_breakdown_id')]][trim_dtla_id])+1;
						$po_data_arr[$row[csf('po_breakdown_id')]][trim_dtla_id][$trim_dtla_id]=$trim_dtla_id;// for rowspan
						$po_data_arr[$row[csf('po_breakdown_id')]][trim_group][$row[csf('item_group_id')]]=$row[csf('item_group_id')];
				        $po_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$trim_dtla_id]=$trim_dtla_id;// for rowspannn
						$po_data_arr[$row[csf('po_breakdown_id')]][trim_group_dtls][$trim_dtla_id]=$row[csf('item_group_id')];
						$po_data_arr[$row[csf('po_breakdown_id')]][cons_uom][$trim_dtla_id]=$cons_uom;
						$po_data_arr[$row[csf('po_breakdown_id')]][trim_group_from][$trim_dtla_id]="Trim Receive";
					}
				    $po_data_arr[$row[csf('po_breakdown_id')]][inhouse_qnty][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				    $po_data_arr[$row[csf('po_breakdown_id')]][inhouse_rate][$row[csf('item_group_id')]]=$row[csf('rate')];
				}
				
				$receive_qty_data=array();
				$receive_rtn_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id,sum(d.quantity) as quantity   from  inv_issue_master a,inv_transaction b, product_details_master c, order_wise_pro_details d,inv_receive_master e  where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and e.id=a.received_id   and b.transaction_type=3 and a.entry_form=49 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $order_cond2  group by d.po_breakdown_id, c.item_group_id order by c.item_group_id");
				
				foreach($receive_rtn_qty_data as $row)
				{
				$po_data_arr[$row[csf('po_breakdown_id')]][receive_rtn_qty][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				}
				$receive_rtn_qty_data=array();$issue_rtn_qty_data=array();
				
				
				$issue_qty_data=sql_select("select b.po_breakdown_id, a.item_group_id, b.quantity as quantity  from  inv_issue_master d,product_details_master p,inv_trims_issue_dtls a , order_wise_pro_details b where a.mst_id=d.id and a.prod_id=p.id and a.trans_id=b.trans_id and b.trans_type=2 and b.entry_form=25 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $order_cond1");
				foreach($issue_qty_data as $row)
				{
				$po_data_arr[$row[csf('po_breakdown_id')]][issue_qty][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				}
				
				$sql_issue_ret=("select c.po_breakdown_id as po_id, p.item_group_id,SUM(c.quantity) as quantity
					from   inv_transaction b, order_wise_pro_details c,product_details_master p 
					where  c.entry_form=73 and p.id=b.prod_id  and b.id=c.trans_id and b.transaction_type=4 and c.is_deleted=0 and c.status_active=1 and b.status_active=1 and b.is_deleted=0  group by c.po_breakdown_id,p.item_group_id");					
				$issue_result=sql_select($sql_issue_ret);
				$issue_qty_data_arr=array();
				foreach($issue_result as $row)
				{
				$issue_qty_data_arr[$row[csf('po_id')]][issue_ret_qty][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				}
					
				$issue_qty_data=array();
				$total_pre_costing_value=0;
				$total_wo_value=0;
				$summary_array=array();
				$i=1;
				foreach($po_data_arr as $key=>$value)
				{
					  $rowspan=count($value[trim_dtla_id]);
					  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";     
					?>
					<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
						<td width="30" title="<? echo $po_qty; ?>" rowspan="<? echo $rowspan; ?>"><? echo $i; ?></td>
						<td width="50" rowspan="<? echo $rowspan; ?>"><p><? echo $buyer_short_name_library[$value[buyer_name]]; ?>&nbsp;</p></td>
						<td width="100" align="center" rowspan="<? echo $rowspan; ?>"><p><? echo $value[job_no_prefix_num]; ?>&nbsp;</p></td>
						<td width="100" rowspan="<? echo $rowspan; ?>" style="word-break: break-all;"><p><? echo $value[style_ref_no]; ?>&nbsp;</p></td>
                        <td width="100" rowspan="<? echo $rowspan; ?>" style="word-break: break-all;"><p><? echo $value[grouping]; ?></p></td>
                    	<td width="100" rowspan="<? echo $rowspan; ?>" style="word-break: break-all;"><p><? echo $value[file_no]; ?></p></td>
						<td width="90" rowspan="<? echo $rowspan; ?>" style="word-break: break-all;">
                        <p>
                        <a href='#report_details' onclick="generate_report('<? echo $company_name; ?>','<? echo $value[job_no]; ?>','preCostRpt');">
						<? 
						$po_number=$value[po_number];
						//$po_number=implode(",", $value[po_id]);
						echo $po_number; 
						?>
                        </a>&nbsp;
                        </p>
                        </td>
						<td width="80" align="right" rowspan="<? echo $rowspan; ?>">
                        <p>
                        <a href='#report_details' onclick="order_qty_popup('<? echo $company_name; ?>','<? echo $value[job_no]; ?>','<? echo $value[po_id]; ?>', '<? echo $value[buyer_name]; ?>' ,'order_qty_data');"><? echo number_format($value[order_quantity_set],0,'.',''); ?>
                        </a>
                        &nbsp;
                        </p>
                        </td>
                        
						<td width="50" align="center" rowspan="<? echo $rowspan; ?>"><p><? echo $unit_of_measurement[$value[order_uom]]; ?>&nbsp;</p></td>
						<td width="80" align="right" rowspan="<? echo $rowspan; ?>"><p><? echo number_format($value[order_quantity],0,'.',''); ?>&nbsp;</p></td>
						<td width="80" align="center" rowspan="<? echo $rowspan; ?>" style="word-break: break-all;">
                        <p>
						<? 
						$pub_shipment_date= $value[pub_shipment_date];
						echo $pub_shipment_date; 
						?>
                        &nbsp;
                        </p>
                        </td>
					<? //$total_pre_costing_value=0;
					foreach($value[trim_group] as $key_trim=>$value_trim)
				     {
					 $gg=1;	
					 $summary_array[trim_group][$key_trim]=$key_trim;
					 foreach($value[$key_trim] as $key_trim1=>$value_trim1)
				     { 
						 $rowspannn=count($value[$key_trim]);
						 if($gg==1)
						 {
					      
						  
					?>
								<td width="100" title="<? echo $value[trim_group_from][$key_trim1];  ?>" style="word-break: break-all;">
									<p>
										<? 
										echo $item_library[$value[trim_group_dtls][$key_trim1]]; 
										//echo $value[trim_group_dtls][$key_trim1];
										?>
									&nbsp;</p>
								</td>
                                <td width="100" title="<? //echo $value[trim_group_from][$key_trim1];  ?>">
									<p>
										<? 
										//echo $item_library[$value[trim_group_dtls][$key_trim1]]; 
										echo $value[remark][$key_trim1];
										?>
									&nbsp;</p>
								</td>
                                
								<td width="100">
									<p>
										<?
										echo $value[brand_sup_ref][$key_trim1];
										//echo $row[csf('brand_sup_ref')]; 
										?>
									&nbsp;</p>
								</td>
								<td width="60" align="center">
                                <p>
								<? 
								 
								if($value[apvl_req][$key_trim1]==1) echo "Yes"; else echo "&nbsp;"; 
								?>
                                &nbsp;
                                </p>
                                </td>
								<td width="80" align="center">
                                
								<? 
								if($value[apvl_req][$key_trim1]==1)
								{
									$app_status=$app_status_arr[$value[job_no]][$value[trim_group_dtls][$key_trim1]];
									$approved_status=$approval_status[$app_status];
								    $summary_array[item_app][$key_trim][all]+=1;
									if($app_status==3)
									{
										$summary_array[item_app][$key_trim][app]+=1;
									}
								}
								else
								{
								$approved_status="";	
								}
								echo $approved_status; 
								?>
                                
                               
                                </td>
							
                                <td width="100" align="right"><p>
								<? 
								$insert_date=explode(" ",$value[insert_date][$key_trim1]);
								echo change_date_format($insert_date[0],'','','');//echo change_date_format($row[csf('pre_date')],'','',1); 
								?></p></td>
								<td width="100" align="right">
                                <p>
                                <a href='#report_details' onclick="order_req_qty_popup('<? echo $company_name; ?>','<? echo $value[job_no]; ?>','<? echo $value[po_id]; ?>', '<? echo $value[buyer_name]; ?>','<? echo $value[rate][$key_trim1]; ?>','<? echo $value[trim_group_dtls][$key_trim1];?>' ,'<? echo $value[booking_no][$key_trim1] ;?>','<? echo $value[description][$key_trim1];?>','<? echo $value[country_id][$key_trim1]; ?>','<? echo $value[trim_dtla_id][$key_trim1]; ?>','<? echo $start_date ?>','<? echo $end_date ?>','order_req_qty_data');">
								<? 
								$req_qty=number_format($value[req_qnty][$key_trim1],2,'.','');
								echo $req_qty; 
								$summary_array[req_qnty][$key_trim]+=$value[req_qnty][$key_trim1];
								?>
                                </a>
                                
                                </p>
                                </td>
                                
								<td width="100" align="right">
                                <p>
								<? 
								echo number_format($value[req_value][$key_trim1],2); 
								$total_pre_costing_value+=$value[req_value][$key_trim1];
								?>
                                
                                </p>
                                </td>
                                <?
							   // $conversion_factor_rate=$conversion_factor_array[$row[csf('trim_group')]]['con_factor'];
							    $wo_qnty=number_format($value[wo_qnty][$key_trim1],2,'.','');
								if($wo_qnty > $req_qty)
								{
									$color_wo="red";	
								}
								
								else if($wo_qnty < $req_qty )
								{
									$color_wo="yellow";		
								}
								
								else 
								{
								$color_wo="";	
								}
								$supplier_name_string="";
								$supplier_id_arr=array_unique(explode(',',$value[supplier_id][$key_trim1]));
								//print_r($supplier_id_arr);
								foreach($supplier_id_arr as $supplier_id_arr_key=>$supplier_id_arr_value)
								{
									$supplier_name_string.=$lib_supplier_arr[$supplier_id_arr_value].",";
								}
								
								$booking_no_arr=array_unique(explode(',',$value[booking_no][$key_trim1]));
								//$booking_no_arr_d=implode(',',$booking_no_arr);
								//print $order_id.'='.	$trim_id;// $wo_qty_array[$order_id][$trim_id]['booking_no'];
								$main_booking_no_large_data="";
								foreach($booking_no_arr as $booking_no1)
								{	
									//if($booking_no1>0)
									//{
									if($main_booking_no_large_data=="") $main_booking_no_large_data=$booking_no1; else $main_booking_no_large_data.=",".$booking_no1;
									//}
									//print($main_booking_no_large_data);
								}
								?>
								<td width="90" align="right" title="<? echo 'conversion_factor='.$value[conversion_factor_rate][$key_trim1];?>" bgcolor="<? echo $color_wo;?>"><p><a href='#report_details' onclick="openmypage('<? echo $value[po_id]; ?>','<? echo $value[trim_group_dtls][$key_trim1]; ?>','<? echo $value[job_no]; ?>','<? echo $main_booking_no_large_data;?>','<? echo $value[trim_dtla_id][$key_trim1];?>','booking_info');">
								<? 
								//$wo_qty_array[$row[csf('po_break_down_id')]][$row[csf('trim_group')]][$row[csf('trim_dtla_id')]]['wo_qnty']
								 echo number_format($value[wo_qnty][$key_trim1],2,'.','');
								 $summary_array[wo_qnty][$key_trim]+=$value[wo_qnty][$key_trim1];
								?>
                                </a></p></td>
                                <td width="60" align="center">
                                <p>
								<? 
								echo $unit_of_measurement[$value[cons_uom][$key_trim1]];
								$summary_array[cons_uom][$key_trim]=$value[cons_uom][$key_trim1];
								?></p></td>
                                <td width="100" align="right" title="<? echo number_format($value[rate][$key_trim1],2,'.',''); ?>">
                                <p>
								<?  
								echo number_format($value[amount][$key_trim1],2,'.',''); 
								$total_wo_value+=$value[amount][$key_trim1];
								?>
                                
                                </p>
                                </td>
                                
                                <td width="150" align="left">
                                <p>
								<? echo rtrim($supplier_name_string,","); ?>
                                </p>
                                </td>
                                
                                <td width="70" align="right" title="<? echo change_date_format($value[wo_date][$key_trim1]);?>"><p>
                                 <?
								 
								$tot=change_date_format($insert_date[0]);
								if($value[wo_qnty][$key_trim1]<=0 )
								{
								 $daysOnHand = datediff('d',$tot,$today);
								}
								else
								{
									$wo_date=$value[wo_date][$key_trim1];
									$wo_date=change_date_format($wo_date);
									$daysOnHand = datediff('d',$tot,$wo_date);;
								}
								 echo $daysOnHand; 
								?>&nbsp;</p>
                                </td>
                                <?
								
								$inhouse_qnty=$value[inhouse_qnty][$key_trim]-$value[receive_rtn_qty][$key_trim];
								$inhouse_rate=$value[inhouse_rate][$key_trim];
								$inhouse_value=$inhouse_qnty*$inhouse_rate;								
								
								$balance=$value[wo_qnty_trim_group][$key_trim]-$inhouse_qnty;
								$issue_qnty=$value[issue_qty][$key_trim];
								$issue_ret_qty=$issue_qty_data_arr[$key][issue_ret_qty][$key_trim];
								$left_overqty=$inhouse_qnty-($issue_qnty-$issue_ret_qty);
								
								$summary_array[inhouse_qnty][$key_trim]+=$inhouse_qnty;
								$summary_array[inhouse_value][$key_trim]+=$inhouse_value;
								$summary_array[inhouse_qnty_bl][$key_trim]+=$balance;
								$summary_array[issue_qty][$key_trim]+=$issue_qnty-$issue_ret_qty;
								$summary_array[left_overqty][$key_trim]+=$left_overqty;
								?>
                                
                                <td width="90" align="right" title="<? echo "Inhouse-Qty: ".$value[inhouse_qnty][$key_trim]."\nReturn Qty: ".$value[receive_rtn_qty][$key_trim]; ?>" rowspan="<? echo $rowspannn; ?>"><p><a href='#report_details' onclick="openmypage_inhouse('<? echo $value[po_id]; ?>','<? echo $value[trim_group_dtls][$key_trim1]; ?>','booking_inhouse_info');"><? echo number_format($inhouse_qnty,2,'.',''); ?></a>&nbsp;</p></td>
								
                                <td width="90" align="right">
									<a href='#report_details' onclick="openmypage_inhouse('<? echo $value[po_id]; ?>','<? echo $value[trim_group_dtls][$key_trim1]; ?>','booking_inhouse_value_info');">
									<? echo number_format($inhouse_value,2,'.',''); ?>
                                    </a>
                                </td>
                                
                                <td width="90" align="right" rowspan="<? echo $rowspannn; ?>"><p><? echo number_format($balance,2,'.',''); ?>&nbsp;</p></td>
								<td width="90" align="right" title="<? echo "Issue-Qty: ".$issue_qnty."\nReturn Qty: ".$issue_ret_qty; ?>" rowspan="<? echo $rowspannn; ?>"><p><a href='#report_details' onclick="openmypage_issue('<? echo $value[po_id]; ?>','<? echo $value[trim_group_dtls][$key_trim1]; ?>','booking_issue_info');"><? echo number_format($issue_qnty-$issue_ret_qty,2,'.',''); ?></a>&nbsp;</p></td>
								<td align="right" rowspan="<? echo $rowspannn; ?>"><p><? echo number_format($left_overqty,2,'.',''); ?>&nbsp;</p></td>
                                
                                <?
						 }
						 else
						 {
						 ?>
                                
								<td width="100" title="<? echo $value[trim_group_from][$key_trim1];  ?>" style="word-break: break-all;">
									<p>
										<? echo $item_library[$value[trim_group_dtls][$key_trim1]]; ?>
									&nbsp;</p>
								</td>
                                <td width="100">
									<p>
										<?
										echo $value[remark][$key_trim1];
										//echo $row[csf('brand_sup_ref')]; 
										?>
									&nbsp;</p>
								</td>
								<td width="100">
									<p>
										<?
										echo $value[brand_sup_ref][$key_trim1];
										//echo $row[csf('brand_sup_ref')]; 
										?>
									&nbsp;</p>
								</td>
								<td width="60" align="center">
                                <p>
								<? 
								 
								if($value[apvl_req][$key_trim1]==1) echo "Yes"; else echo "&nbsp;"; 
								?>
                                &nbsp;
                                </p>
                                </td>
								<td width="80" align="center">
                                
								<? 
								if($value[apvl_req][$key_trim1]==1)
								{
									$app_status=$app_status_arr[$value[job_no]][$value[trim_group_dtls][$key_trim1]];
									$approved_status=$approval_status[$app_status];
									$summary_array[item_app][$key_trim][all]+=1;
									if($app_status==3)
									{
										$summary_array[item_app][$key_trim][app]+=1;
									}
								}
								else
								{
								$approved_status="";	
								}
								echo $approved_status; 
								?>
                               
                                </td>
							
                                <td width="100" align="right"><p>
								<? 
								$insert_date=explode(" ",$value[insert_date][$key_trim1]);
								echo change_date_format($insert_date[0],'','','');//echo change_date_format($row[csf('pre_date')],'','',1); 
								?>&nbsp;</p></td>
								<td width="100" align="right">
                                <p>
                                <a href='#report_details' onclick="order_req_qty_popup('<? echo $company_name; ?>','<? echo $value[job_no]; ?>','<? echo $value[po_id]; ?>', '<? echo $value[buyer_name]; ?>','<? echo $value[rate][$key_trim1]; ?>','<? echo $value[trim_group_dtls][$key_trim1];?>' ,'<? echo $value[booking_no][$key_trim1] ;?>','<? echo $value[description][$key_trim1];?>','<? echo $value[country_id][$key_trim1]; ?>','<? echo $value[trim_dtla_id][$key_trim1]; ?>','<? echo $start_date ?>','<? echo $end_date ?>','order_req_qty_data');">
								<? 
								$req_qty=number_format($value[req_qnty][$key_trim1],2,'.','');
								echo $req_qty;
								$summary_array[req_qnty][$key_trim]+=$value[req_qnty][$key_trim1];
								?>
                                </a>
                               
                                </p>
                                </td>
                                
								<td width="100" align="right">
                                <p>
								<? 
								echo number_format($value[req_value][$key_trim1],2); 
								$total_pre_costing_value+=$value[req_value][$key_trim1];
								?>
                                
                                </p>
                                </td>
                                <?
								$wo_qnty=number_format($value[wo_qnty][$key_trim1],2);
								if($wo_qnty > $req_qty)
								{
									$color_wo="red";	
								}
								
								else if($wo_qnty < $req_qty )
								{
									$color_wo="yellow";		
								}
								
								else 
								{
								$color_wo="";	
								}
								
								$supplier_name_string="";
								$supplier_id_arr=array_unique(explode(',',$value[supplier_id][$key_trim1]));
								//print_r($supplier_id_arr);
								foreach($supplier_id_arr as $supplier_id_arr_key=>$supplier_id_arr_value)
								{
									$supplier_name_string.=$lib_supplier_arr[$supplier_id_arr_value].",";
								}
								
								$booking_no_arr=array_unique(explode(',',$value[booking_no][$key_trim1]));
								//$booking_no_arr_d=implode(',',$booking_no_arr);
								//print $order_id.'='.	$trim_id;// $wo_qty_array[$order_id][$trim_id]['booking_no'];
								$main_booking_no_large_data="";
								foreach($booking_no_arr as $booking_no1)
								{	
									//if($booking_no1>0)
									//{
									if($main_booking_no_large_data=="") $main_booking_no_large_data=$booking_no1; else $main_booking_no_large_data.=",".$booking_no1;
									//}
									//print($main_booking_no_large_data);
								}
								?>
								<td width="90" align="right" title="<? echo 'conversion_factor='.$value[conversion_factor_rate][$key_trim1];?>" bgcolor="<? echo $color_wo;?>"><p><a href='#report_details' onclick="openmypage('<? echo $value[po_id]; ?>','<? echo $value[trim_group_dtls][$key_trim1]; ?>','<? echo $value[job_no]; ?>','<? echo $main_booking_no_large_data;?>','<? echo $value[trim_dtla_id][$key_trim1];?>','booking_info');">
								<? 
								//$wo_qty_array[$row[csf('po_break_down_id')]][$row[csf('trim_group')]][$row[csf('trim_dtla_id')]]['wo_qnty']
								 echo number_format($value[wo_qnty][$key_trim1],2,'.','');
								 $summary_array[wo_qnty][$key_trim]+=$value[wo_qnty][$key_trim1];

								?>
                                </a></p></td>
                                
                                <td width="60" align="center">
                                <p>
								<? 
								echo $unit_of_measurement[$value[cons_uom][$key_trim1]]; 
								$summary_array[cons_uom][$key_trim]= $value[cons_uom][$key_trim1];
								?>
                                </p></td>
                                
                                <td width="100" align="right" title="<? echo number_format($value[rate][$key_trim1],2,'.',''); ?>">
                                <p>
								<?  
								echo number_format($value[amount][$key_trim1],2,'.',''); 
								$total_wo_value+=$value[amount][$key_trim1];
								?>
                               
                                </p>
                                </td>
                                <td width="150" align="left">
                                <p>
								<? echo rtrim($supplier_name_string,","); ?>
                                </p>
                                </td>
                                <td width="70" align="right" title="<? echo change_date_format($value[wo_date][$key_trim1]);?>"><p>
                                 <?
								$tot=change_date_format($insert_date[0]);
								if($value[wo_qnty][$key_trim1]<=0 )
								{
								 $daysOnHand = datediff('d',$tot,$today);
								}
								else
								{
									$wo_date=$value[wo_date][$key_trim1];
									$wo_date=change_date_format($wo_date);
									$daysOnHand = datediff('d',$tot,$wo_date);;
								}
								 echo $daysOnHand; 
								?>&nbsp;</p>
                                </td>
                                <?
						 }
								?>
							 </tr>
							
					<?
						
						$gg++;
			    }// end  foreach($value[$key_trim] as $key_trim1=>$value_trim1)
				}
				?>
                <?
				$i++;
				}
				$po_data_arr=array();
				?>
                 
				</table>
				<table class="rpt_table" width="2560" cellpadding="0" cellspacing="0" border="1" rules="all">
					<tfoot>
						<th width="30"></th>
						<th width="50"></th>
						<th width="100"></th>
						<th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
						<th width="90"></th>
						<th width="80" align="right" id="total_order_qnty"><? //echo number_format($total_order_qnty,0); ?></th>
						<th width="50"></th>
						<th width="80" align="right" id="total_order_qnty_in_pcs"><? //echo number_format($total_order_qnty_in_pcs,0); ?></th>
						<th width="80"></th>
						<th width="100"></th>
                        <th width="100"></th>
						<th width="100"></th>
						<th width="60"></th>
						<th width="80"></th>
						<th width="100"></th>
						<th width="100" align="right" id="value_req_qnty"><? //echo number_format($total_req_qnty,2); ?></th>
						<th width="100" align="right" id="value_pre_costing"><? echo number_format($total_pre_costing_value,2); ?></th>
						<th width="90" align="right" id="value_wo_qty"><? //echo number_format($total_wo_qnty,2); ?></th>
                        <th width="60" align="right" ></th>
                        <th width="100" align="right" id=""><? echo number_format($total_wo_value,2); ?></th>
                        <th width="150" align="right" id=""></th>
                        <th width="70" align="right"><p><? //echo number_format($req_value,2,'.',''); ?>&nbsp;</p></th>
                        <th width="90" align="right" id="value_in_qty"><? //echo number_format($total_in_qnty,2); ?></th>
                        <th width="90" align="right" id="value_in_value"><? //echo number_format($total_in_qnty,2); ?></th>
                         
						<th width="90" align="right" id="value_rec_qty"><? //echo number_format($total_rec_bal_qnty,2); ?></th>
						<th width="90" align="right" id="value_issue_qty"><? //echo number_format($total_issue_qnty,2); ?></th>
						<th align="right" id="value_leftover_qty"><? //echo number_format($total_leftover_qnty,2); ?></th>
					</tfoot>
				</table>
				</div>
				<table>
					<tr><td height="17"></td></tr>
				</table>
				<u><b>Summary</b></u>
				<table class="rpt_table" width="1200" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<th width="30">SL</th>
						<th width="110">Item</th>
						<th width="60">UOM</th>
						<th width="80">Approved %</th>
						<th width="110">Req Qty</th>
						<th width="110">WO Qty</th>
						<th width="80">WO %</th>
						<th width="110">In-House Qty</th>
						<th width="80">In-House %</th>
						<th width="110">In-House Balance Qty</th>
						<th width="110">Issue Qty</th>
						<th width="80">Issue %</th>
						<th>Left Over</th>
					</thead>
					<?
					$z=1; $tot_req_qnty_summary=0;
					foreach($summary_array[trim_group] as $key_trim=>$value)
					{
						if($z%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						//$tot_req_qnty_summary+=$value['req'];
						//$tot_wo_qnty_summary+=$value['wo'];
						//$tot_in_qnty_summary+=$value['in'];
						//$tot_issue_qnty_summary+=$value['issue'];
						//$tot_leftover_qnty_summary+=$value['leftover'];
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr2_<? echo $z; ?>','<? echo $bgcolor;?>')" id="tr2_<? echo $z; ?>">
							<td width="30"><? echo $z; ?></td>
							<td width="110"><p><? echo $item_library[$key_trim]; ?></p></td>
							<td width="60" align="center">
							<? 
							echo $unit_of_measurement[$summary_array[cons_uom][$key_trim]]; 
							?></td>  
							<td width="80" align="right"><? $app_perc=($summary_array[item_app][$key_trim][app]*100)/$summary_array[item_app][$key_trim][all]; echo number_format($app_perc,2); ?>&nbsp;</td>
							<td width="110" align="right"><? echo number_format($summary_array[req_qnty][$key_trim],2); ?>&nbsp;</td>
							<td width="110" align="right"><? echo number_format( $summary_array[wo_qnty][$key_trim],2); ?>&nbsp;</td>
							<td width="80" align="right"><? $wo_per= $summary_array[wo_qnty][$key_trim]/$summary_array[req_qnty][$key_trim]*100; echo number_format($wo_per,2).'%'; ?>&nbsp;</td>
							<td width="110" align="right"><? echo number_format($summary_array[inhouse_qnty][$key_trim],2); ?>&nbsp;</td>
							<td width="80" align="right"><? $in_per=$summary_array[inhouse_qnty][$key_trim]/$summary_array[wo_qnty][$key_trim]*100; echo number_format($in_per,2).'%'; ?>&nbsp;</td>
							<td width="110" align="right"><? echo number_format($summary_array[inhouse_qnty_bl][$key_trim],2); $in_house_bal+=($value['wo']-$value['in']); ?>&nbsp;</td>
							<td width="110" align="right"><? echo number_format($summary_array[issue_qty][$key_trim],2); ?>&nbsp;</td>
							<td width="80" align="right"><? $wo_per=$summary_array[issue_qty][$key_trim]/$summary_array[wo_qnty][$key_trim]*100; echo number_format($wo_per,2).'%'; ?>&nbsp;</td>
							<td align="right"><? echo number_format($summary_array[left_overqty][$key_trim],2); ?>&nbsp;</td>
						</tr>
					<?	
					$z++;
					}
					$summary_array=array();
					?>
					<tfoot>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th align="right"><? //echo number_format($tot_req_qnty_summary,2); ?>&nbsp;</th>
						<th align="right"><? //echo number_format($tot_wo_qnty_summary,2); ?>&nbsp;</th>
						<th>&nbsp;</th>
						<th align="right"><? //echo number_format($tot_in_qnty_summary,2); ?>&nbsp;</th>
						<th>&nbsp;</th>
						<th align="right"><? //echo number_format($in_house_bal,2); ?>&nbsp;</th>
						<th align="right"><? //echo number_format($tot_issue_qnty_summary,2); ?>&nbsp;</th>
						<th>&nbsp;</th>
						<th align="right"><? //echo number_format($tot_leftover_qnty_summary,2); ?>&nbsp;</th>
					</tfoot>   	
				</table>
			</fieldset>
		</div>
	<?
	}
	}
	
	
	
	
	
	
	
//===========================================================================================================================================================

  if(str_replace("'","",$cbo_search_by)==2)
  {
	if($template==1)
	{
		
		ob_start();
	?>
		<div style="width:2400px">
		<fieldset style="width:100%;">	
			<table width="2400">
				<tr class="form_caption">
					<td colspan="26" align="center">Accessories Followup Report</td>
				</tr>
				<tr class="form_caption">
					<td colspan="26" align="center"><? echo $company_library[$company_name]; ?></td>
				</tr>
			</table>
			<table class="rpt_table" width="2360" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<th width="30">SL</th>
					<th width="50">Buyer</th>
					<th width="100">Job No</th>
					<th width="100">Style Ref</th>
                    <th width="100">Internal Ref</th>
                    <th width="100">File No</th>
					<th width="90">Order No</th>
					<th width="80">Order Qnty</th>
					<th width="50">UOM</th>
					<th width="80">Qnty (Pcs)</th>
					<th width="80">Shipment Date</th>
					<th width="100">Trims Name</th>
					<th width="100">Brand/Sup Ref</th>
					<th width="60">Appr Req.</th>
					<th width="80">Approve Status</th>
                    <th width="100">Item Entry Date</th>
					<th width="100">Req Qnty</th>
					<th width="100">Pre Costing Value</th>
					<th width="90">WO Qnty</th>
                    <th width="60">Trims UOM</th>
                    <th width="100">WO Value</th>
                    <th width="150">Supplier</th>
                    <th width="70">WO Delay Days</th>
					<th width="90">In-House Qnty</th>
					<th width="90">Receive Balance</th>
					<th width="90">Issue to Prod.</th>
					<th>Left Over/Balance</th>
				</thead>
			</table>
			<div style="width:2380px; max-height:400px; overflow-y:scroll" id="scroll_body">
				<table class="rpt_table" width="2360" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
	<?
	
	/*$wo_qty_array=array();
	$wo_qty_summary_array=array();
	if($db_type==2)
	{
	 $wo_sql="select min(a.booking_date) as booking_date ,b.job_no,LISTAGG(CAST(a.booking_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as booking_no, b.po_break_down_id, b.trim_group,b.pre_cost_fabric_cost_dtls_id, sum(b.wo_qnty) as wo_qnty,sum(b.amount) as amount,sum(b.rate) as rate from wo_booking_mst a, wo_booking_dtls b 
	where a.item_category=4 and a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name and b.job_no='FAL-14-00628' group by b.po_break_down_id,b.trim_group,b.job_no,b.pre_cost_fabric_cost_dtls_id";
	}
	else if($db_type==0)
	{
	$wo_sql="select min(a.booking_date) as booking_date ,b.job_no,group_concat(a.booking_no) as booking_no, b.po_break_down_id, b.trim_group,b.pre_cost_fabric_cost_dtls_id,sum(b.wo_qnty) as wo_qnty,sum(b.amount) as amount,sum(b.rate) as rate from wo_booking_mst a, wo_booking_dtls b 
	where a.item_category=4 and a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name group by b.po_break_down_id,b.trim_group,b.job_no,b.pre_cost_fabric_cost_dtls_id";
	}
	$dataArray=sql_select($wo_sql);
	foreach($dataArray as $row )
	{
		
		$wo_qty_array[$row[csf('po_break_down_id')]][$row[csf('trim_group')]][$row[csf('pre_cost_fabric_cost_dtls_id')]]['booking_no']=$row[csf('booking_no')];
		$wo_qty_array[$row[csf('po_break_down_id')]][$row[csf('trim_group')]][$row[csf('pre_cost_fabric_cost_dtls_id')]]['wo_qnty']=$row[csf('wo_qnty')];
		$wo_qty_array[$row[csf('po_break_down_id')]][$row[csf('trim_group')]][$row[csf('pre_cost_fabric_cost_dtls_id')]]['rate']=$row[csf('rate')];
		$wo_qty_array[$row[csf('po_break_down_id')]][$row[csf('trim_group')]][$row[csf('pre_cost_fabric_cost_dtls_id')]]['amount']=$row[csf('amount')];
		$wo_qty_array[$row[csf('po_break_down_id')]][$row[csf('trim_group')]][$row[csf('pre_cost_fabric_cost_dtls_id')]]['wo_date']=$row[csf('booking_date')];
		
		$wo_qty_summary_array[$row[csf('trim_group')]]['wo_qnty']=$row[csf('wo_qnty')];
	}*/
	
	/*$receive_qty_array=array();
	$receive_qty_data=sql_select("select b.po_breakdown_id, a.item_group_id,sum(b.quantity) as quantity   from  inv_receive_master c,product_details_master d,inv_trims_entry_dtls a , order_wise_pro_details b where a.mst_id=c.id and a.trans_id=b.trans_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, a.item_group_id");
	foreach($receive_qty_data as $row)
	{
		$receive_qty_array[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]['receive_qty']=$row[csf('quantity')];
	}
		
	$issue_qty_array=array();
	$issue_qty_data=sql_select("select b.po_breakdown_id, a.item_group_id,sum(b.quantity) as quantity  from  inv_issue_master d,product_details_master p,inv_trims_issue_dtls a , order_wise_pro_details b where a.mst_id=d.id and a.prod_id=p.id and a.trans_id=b.trans_id and b.trans_type=2 and b.entry_form=25 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, a.item_group_id");
	foreach($issue_qty_data as $row)
	{
		$issue_qty_array[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]['issue_qty']=$row[csf('quantity')];
	}*/
	
	
	$conversion_factor_array=array();
	$conversion_factor=sql_select("select id ,trim_uom,conversion_factor from  lib_item_group  ");
	foreach($conversion_factor as $row_f)
	{
	 $conversion_factor_array[$row_f[csf('id')]]['con_factor']=$row_f[csf('conversion_factor')];
	 $conversion_factor_array[$row_f[csf('id')]]['cons_uom']=$row_f[csf('trim_uom')];
	}
	$conversion_factor=array();
	$app_sql=sql_select("select job_no_mst,accessories_type_id,approval_status from wo_po_trims_approval_info");
	$app_status_arr=array();
	foreach($app_sql as $row)
	{
		$app_status_arr[$row[csf("job_no_mst")]][$row[csf("accessories_type_id")]]=$row[csf("approval_status")];
	}
	$app_sql=array();
	
	$sql_po_qty_country_wise_arr=array();
	$po_job_arr=array();
	$sql_po_qty_country_wise=sql_select("select  b.id,b.job_no_mst,c.country_id, sum(c.order_quantity/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst  and b.id=c.po_break_down_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond group by   b.id,b.job_no_mst,c.country_id order by b.id,b.job_no_mst,c.country_id");
	foreach( $sql_po_qty_country_wise as $sql_po_qty_country_wise_row)
	{
	$sql_po_qty_country_wise_arr[$sql_po_qty_country_wise_row[csf('id')]][$sql_po_qty_country_wise_row[csf('country_id')]]=$sql_po_qty_country_wise_row[csf('order_quantity_set')];
	$po_job_arr[$sql_po_qty_country_wise_row[csf('id')]]=$sql_po_qty_country_wise_row[csf('job_no_mst')];
	}
	$sql_po_qty_country_wise=array();
 
    
	$style_data_arr=array();
	$po_id_string="";
	$today=date("Y-m-d");
	/*$sql_query=sql_select("select a.buyer_name,a.job_no,a.job_no_prefix_num,style_ref_no, b.file_no,b.grouping,b.id,b.po_number,a.order_uom,sum(distinct b.po_quantity) as po_quantity,a.total_set_qnty  ,sum(distinct b.po_quantity*a.total_set_qnty) as po_quantity_psc ,sum(c.order_quantity) as order_quantity ,sum(c.order_quantity/a.total_set_qnty) as order_quantity_set,  b.pub_shipment_date,
	d.costing_per,
	e.id as trim_dtla_id,
	e.trim_group,
	e.description,
	e.brand_sup_ref,
	e.cons_uom,
	e.cons_dzn_gmts,
	e.rate,
	e.amount,
	e.apvl_req,
	e.nominated_supp,
	e.insert_date,
	f.cons,
	f.country_id,
	f.cons as cons_cal
	from 
	wo_po_details_master a, 
	wo_po_break_down b,
	wo_po_color_size_breakdown c
	left join 
	wo_pre_cost_trim_co_cons_dtls f 
	on
	c.job_no_mst=f.job_no and
	c.po_break_down_id=f.po_break_down_id and
	f.cons>0
	join
	wo_pre_cost_trim_cost_dtls e
	on 
	f.job_no=e.job_no  and
	e.id=f.wo_pre_cost_trim_cost_dtls_id 
	$item_group_cond
	join 
	wo_pre_cost_mst d
	on
	e.job_no =d.job_no
	where 
	a.job_no=b.job_no_mst   and 
	a.job_no=c.job_no_mst   and 
	b.id=c.po_break_down_id and 
	a.company_name=1        and 
	a.is_deleted=0          and 
	a.status_active=1       and 
	b.is_deleted=0          and 
	b.status_active=1       and 
	c.is_deleted=0          and 
	c.status_active=1       and 
	a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond  $ordercond  $file_no_cond $internal_ref_cond
	group by a.buyer_name,a.job_no,a.job_no_prefix_num,style_ref_no,b.file_no,b.grouping, b.id,b.po_number,a.order_uom,a.total_set_qnty,b.pub_shipment_date,d.costing_per,
	e.id,
	e.trim_group,
	e.description,
	e.brand_sup_ref,
	e.cons_uom,
	e.cons_dzn_gmts,
	e.rate,
	e.amount,
	e.apvl_req,
	e.nominated_supp,
	e.insert_date,
	f.cons,
	f.pcs,
	f.country_id
	order by b.id, e.trim_group
	");*/
	$sql_query=sql_select("select a.buyer_name,a.job_no,a.job_no_prefix_num,style_ref_no, b.file_no,b.grouping,b.id,b.po_number,a.order_uom,sum(c.order_quantity) as order_quantity ,sum(c.order_quantity/a.total_set_qnty) as order_quantity_set,  b.pub_shipment_date,
	d.costing_per,
	e.id as trim_dtla_id,
	e.trim_group,
	e.description,
	e.brand_sup_ref,
	e.cons_uom,
	e.cons_dzn_gmts,
	e.rate,
	e.amount,
	e.apvl_req,
	e.nominated_supp,
	e.insert_date,
	f.cons,
	f.country_id,
	f.cons as cons_cal
	from 
	wo_po_details_master a, 
	wo_po_break_down b,
	wo_po_color_size_breakdown c
	left join 
	wo_pre_cost_trim_co_cons_dtls f 
	on
	c.job_no_mst=f.job_no and
	c.po_break_down_id=f.po_break_down_id and
	f.cons>0
	join
	wo_pre_cost_trim_cost_dtls e
	on 
	f.job_no=e.job_no  and
	e.id=f.wo_pre_cost_trim_cost_dtls_id 
	$item_group_cond
	join 
	wo_pre_cost_mst d
	on
	e.job_no =d.job_no
	where 
	a.job_no=b.job_no_mst   and 
	a.job_no=c.job_no_mst   and 
	b.id=c.po_break_down_id and 
	a.company_name=1        and 
	a.is_deleted=0          and 
	a.status_active=1       and 
	b.is_deleted=0          and 
	b.status_active=1       and 
	c.is_deleted=0          and 
	c.status_active=1       and 
	a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond  $ordercond  $file_no_cond $internal_ref_cond
	group by a.buyer_name,a.job_no,a.job_no_prefix_num,style_ref_no,b.file_no,b.grouping, b.id,b.po_number,a.order_uom,a.total_set_qnty,b.pub_shipment_date,d.costing_per,
	e.id,
	e.trim_group,
	e.description,
	e.brand_sup_ref,
	e.cons_uom,
	e.cons_dzn_gmts,
	e.rate,
	e.amount,
	e.apvl_req,
	e.nominated_supp,
	e.insert_date,
	f.cons,
	f.pcs,
	f.country_id
	order by b.id, e.trim_group
	");//sum(distinct b.po_quantity) as po_quantity,a.total_set_qnty  ,sum(distinct b.po_quantity*a.total_set_qnty) as po_quantity_psc ,
				$tot_rows=count($sql_query);
				$i=1;
				foreach($sql_query as $row)
				{
					      
						   
						    $dzn_qnty=0;
							if($row[csf('costing_per')]==1)
							{
								$dzn_qnty=12;
							}
							else if($row[csf('costing_per')]==3)
							{
								$dzn_qnty=12*2;
							}
							else if($row[csf('costing_per')]==4)
							{
								$dzn_qnty=12*3;
							}
							else if($row[csf('costing_per')]==5)
							{
								$dzn_qnty=12*4;
							}
							else
							{
								$dzn_qnty=1;
							}
							
							
							 $po_qty=0;
							 if($row[csf('country_id')]==0)
							 {
								$po_qty=$row[csf('order_quantity')];
							 }
							 else
							 {
								$country_id= explode(",",$row[csf('country_id')]);
								for($cou=0;$cou<=count($country_id); $cou++)
								{
								$po_qty+=$sql_po_qty_country_wise_arr[$row[csf('id')]][$country_id[$cou]];
								}
							 }
							 
							 $req_qnty=($row[csf('cons_cal')]/$dzn_qnty)*$po_qty;
							 $req_value= $row[csf('rate')]*$req_qnty;
							 
							
							 
							 $style_data_arr[$row[csf('job_no')]][job_no]=$row[csf('job_no')];
							 $style_data_arr[$row[csf('job_no')]][buyer_name]=$row[csf('buyer_name')];
							 $style_data_arr[$row[csf('job_no')]][job_no_prefix_num]=$row[csf('job_no_prefix_num')];
							 $style_data_arr[$row[csf('job_no')]][style_ref_no]=$row[csf('style_ref_no')];
							 $style_data_arr[$row[csf('job_no')]][grouping]=$row[csf('grouping')];
							 $style_data_arr[$row[csf('job_no')]][file_no]=$row[csf('file_no')];
							 
							 $style_data_arr[$row[csf('job_no')]][order_uom]=$row[csf('order_uom')];
							 $style_data_arr[$row[csf('job_no')]][po_id][$row[csf('id')]]=$row[csf('id')];
							 $style_data_arr[$row[csf('job_no')]][po_number][$row[csf('id')]]=$row[csf('po_number')];
							 $style_data_arr[$row[csf('job_no')]][order_quantity_set][$row[csf('id')]]=$row[csf('order_quantity_set')];
							 $style_data_arr[$row[csf('job_no')]][order_quantity][$row[csf('id')]]=$row[csf('order_quantity')];
							 $style_data_arr[$row[csf('job_no')]][pub_shipment_date][$row[csf('id')]]=change_date_format($row[csf('pub_shipment_date')]);
							 $po_id_string.=$row[csf('id')].",";
							 
							 $style_data_arr[$row[csf('job_no')]][trim_dtla_id][$row[csf('trim_dtla_id')]]=$row[csf('trim_dtla_id')];// for rowspan
							 $style_data_arr[$row[csf('job_no')]][trim_group][$row[csf('trim_group')]]=$row[csf('trim_group')];
							 $style_data_arr[$row[csf('job_no')]][$row[csf('trim_group')]][$row[csf('trim_dtla_id')]]=$row[csf('trim_dtla_id')]; // for rowspannn
							 $style_data_arr[$row[csf('job_no')]][trim_group_dtls][$row[csf('trim_dtla_id')]]=$row[csf('trim_group')];
							 
							
							 
							 $style_data_arr[$row[csf('job_no')]][brand_sup_ref][$row[csf('trim_dtla_id')]]=$row[csf('brand_sup_ref')];
							 $style_data_arr[$row[csf('job_no')]][apvl_req][$row[csf('trim_dtla_id')]]=$row[csf('apvl_req')];
							 $style_data_arr[$row[csf('job_no')]][insert_date][$row[csf('trim_dtla_id')]]=$row[csf('insert_date')];
							 $style_data_arr[$row[csf('job_no')]][req_qnty][$row[csf('trim_dtla_id')]]+=$req_qnty;
							 $style_data_arr[$row[csf('job_no')]][req_value][$row[csf('trim_dtla_id')]]+=$req_value;
							 $style_data_arr[$row[csf('job_no')]][cons_uom][$row[csf('trim_dtla_id')]]=$row[csf('cons_uom')];
							 
							 $style_data_arr[$row[csf('job_no')]][trim_group_from][$row[csf('trim_dtla_id')]]="Pre_cost";
							 
							 
							 $style_data_arr[$row[csf('job_no')]][rate][$row[csf('trim_dtla_id')]]=$row[csf('rate')];
                             $style_data_arr[$row[csf('job_no')]][description][$row[csf('trim_dtla_id')]]=$row[csf('description')];
                             $style_data_arr[$row[csf('job_no')]][country_id][$row[csf('trim_dtla_id')]].=$row[csf('country_id')].",";

							 
							// $style_data_arr[$row[csf('job_no')]][wo_qnty][$row[csf('trim_dtla_id')]]+=$wo_qnty;
							// $style_data_arr[$row[csf('job_no')]][amount][$row[csf('trim_dtla_id')]]+=$amount;
							// $style_data_arr[$row[csf('job_no')]][wo_date][$row[csf('trim_dtla_id')]]=$wo_date;
							// $style_data_arr[$row[csf('job_no')]][wo_qnty_trim_group][$row[csf('trim_group')]]+=$wo_qnty;
				}
				
				$sql_query=array();
				$po_id_string=rtrim($po_id_string,",");
				if($po_id_string=="")
				{
				echo "<strong style='color:red;font-size:30px'>No Data found</strong>";
				die;
				}
				
				$po_id=array_chunk(array_unique(explode(",",$po_id_string)),1000, true);
				 $order_cond="";
				   $ji=0;
				   foreach($po_id as $key=> $value)
				   {
					   if($ji==0)
					   {
						$order_cond=" and b.po_break_down_id  in(".implode(",",$value).")"; 
						$order_cond1=" and b.po_breakdown_id  in(".implode(",",$value).")"; 
						$order_cond2=" and d.po_breakdown_id  in(".implode(",",$value).")"; 
					   }
					   else
					   {
						$order_cond.=" or b.po_break_down_id  in(".implode(",",$value).")";
						$order_cond1.=" or b.po_breakdown_id  in(".implode(",",$value).")";
						$order_cond2.=" or d.po_breakdown_id  in(".implode(",",$value).")";
					   }
					   $ji++;
				   }

				if($db_type==2)
				{
					//echo "select min(a.booking_date) as booking_date ,b.job_no,LISTAGG(CAST(a.booking_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as booking_no, b.po_break_down_id, b.trim_group,b.pre_cost_fabric_cost_dtls_id, sum(b.wo_qnty) as wo_qnty,sum(b.amount) as amount,sum(b.rate) as rate from wo_booking_mst a, wo_booking_dtls b where a.item_category=4 and a.booking_no=b.booking_no  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name and b.po_break_down_id in($po_id_string) group by b.po_break_down_id,b.trim_group,b.job_no,b.pre_cost_fabric_cost_dtls_id";
				  $wo_sql_without_precost=sql_select("select min(a.booking_date) as booking_date ,b.job_no,LISTAGG(CAST(a.booking_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as booking_no,LISTAGG(CAST(a.supplier_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as supplier_id, b.po_break_down_id, b.trim_group,b.pre_cost_fabric_cost_dtls_id, sum(b.wo_qnty) as wo_qnty,sum(b.amount) as amount,sum(b.rate) as rate from wo_booking_mst a, wo_booking_dtls b where a.item_category=4 and a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name $order_cond group by b.po_break_down_id,b.trim_group,b.job_no,b.pre_cost_fabric_cost_dtls_id");//and item_from_precost=2
				}
				else if($db_type==0)
				{
					//echo "select min(a.booking_date) as booking_date ,b.job_no,group_concat(a.booking_no) as booking_no, group_concat(a.supplier_id) as supplier_id, b.po_break_down_id, b.trim_group,b.pre_cost_fabric_cost_dtls_id,sum(b.wo_qnty) as wo_qnty,sum(b.amount) as amount,sum(b.rate) as rate from wo_booking_mst a, wo_booking_dtls b where a.item_category=4 and a.booking_no=b.booking_no  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name $order_cond  group by b.po_break_down_id,b.trim_group,b.job_no,b.pre_cost_fabric_cost_dtls_id";
				  $wo_sql_without_precost=sql_select("select min(a.booking_date) as booking_date ,b.job_no,group_concat(a.booking_no) as booking_no, group_concat(a.supplier_id) as supplier_id, b.po_break_down_id, b.trim_group,b.pre_cost_fabric_cost_dtls_id,sum(b.wo_qnty) as wo_qnty,sum(b.amount) as amount,sum(b.rate) as rate from wo_booking_mst a, wo_booking_dtls b where a.item_category=4 and a.booking_no=b.booking_no  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name $order_cond  group by b.po_break_down_id,b.trim_group,b.job_no,b.pre_cost_fabric_cost_dtls_id");//and item_from_precost=2
				}
				$style_data_arr1=array();
				foreach($wo_sql_without_precost as $wo_row_without_precost)
				{
					
					$conversion_factor_rate=$conversion_factor_array[$wo_row_without_precost[csf('trim_group')]]['con_factor'];
					$cons_uom=$conversion_factor_array[$wo_row_without_precost[csf('trim_group')]]['cons_uom'];
					$booking_no=$wo_row_without_precost[csf('booking_no')];
					$supplier_id=$wo_row_without_precost[csf('supplier_id')];
					$wo_qnty=$wo_row_without_precost[csf('wo_qnty')]*$conversion_factor_rate;
					$amount=$wo_row_without_precost[csf('amount')];
					$wo_date=$wo_row_without_precost[csf('booking_date')];
					
					if($wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')] =="" || $wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')] ==0)
					{
					    $trim_dtla_id=max($style_data_arr[$wo_row_without_precost[csf('job_no')]][trim_dtla_id])+1;
						$style_data_arr[$wo_row_without_precost[csf('job_no')]][trim_dtla_id][$trim_dtla_id]=$trim_dtla_id;// for rowspan
					    $style_data_arr[$wo_row_without_precost[csf('job_no')]][trim_group][$wo_row_without_precost[csf('trim_group')]]=$wo_row_without_precost[csf('trim_group')];
				        $style_data_arr[$wo_row_without_precost[csf('job_no')]][$wo_row_without_precost[csf('trim_group')]][$trim_dtla_id]=$trim_dtla_id;// for rowspannn
						$style_data_arr[$wo_row_without_precost[csf('job_no')]][trim_group_dtls][$trim_dtla_id]=$wo_row_without_precost[csf('trim_group')];
						$style_data_arr[$wo_row_without_precost[csf('job_no')]][cons_uom][$trim_dtla_id]=$cons_uom;
						
						$style_data_arr[$wo_row_without_precost[csf('job_no')]][trim_group_from][$trim_dtla_id]="Booking Without Pre_cost";
					}
					else
					{
						$trim_dtla_id=$wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')];
						
					}
					//$style_data_arr[$wo_row_without_precost[csf('job_no')]][trim_dtla_id][$trim_dtla_id]=$trim_dtla_id;// for rowspan
					//$style_data_arr[$wo_row_without_precost[csf('job_no')]][trim_group][$wo_row_without_precost[csf('trim_group')]]=$wo_row_without_precost[csf('trim_group')];
				   // $style_data_arr[$wo_row_without_precost[csf('job_no')]][$wo_row_without_precost[csf('trim_group')]][$trim_dtla_id]=$trim_dtla_id;// for rowspannn
				    //$style_data_arr[$wo_row_without_precost[csf('job_no')]][trim_group_dtls][$trim_dtla_id]=$wo_row_without_precost[csf('trim_group')];
					
					$style_data_arr[$wo_row_without_precost[csf('job_no')]][wo_qnty][$trim_dtla_id]+=$wo_qnty;
					$style_data_arr[$wo_row_without_precost[csf('job_no')]][amount][$trim_dtla_id]+=$amount;
					$style_data_arr[$wo_row_without_precost[csf('job_no')]][wo_date][$trim_dtla_id]=$wo_date;
					$style_data_arr[$wo_row_without_precost[csf('job_no')]][wo_qnty_trim_group][$wo_row_without_precost[csf('trim_group')]]+=$wo_qnty;
					
					$style_data_arr[$wo_row_without_precost[csf('job_no')]][booking_no][$trim_dtla_id].=$booking_no.",";
					$style_data_arr[$wo_row_without_precost[csf('job_no')]][supplier_id][$trim_dtla_id].=$supplier_id.",";
					$style_data_arr[$wo_row_without_precost[csf('job_no')]][conversion_factor_rate][$trim_dtla_id]=$conversion_factor_rate;

					
				
					 
				}
				$wo_sql_without_precost=array();
				
				$receive_qty_data=sql_select("select b.po_breakdown_id, a.item_group_id,sum(b.quantity) as quantity   from  inv_receive_master c,product_details_master d,inv_trims_entry_dtls a , order_wise_pro_details b where a.mst_id=c.id and a.trans_id=b.trans_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $order_cond1  group by b.po_breakdown_id, a.item_group_id order by a.item_group_id ");
				 
				foreach($receive_qty_data as $row)
				{
					if($style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][trim_group][$row[csf('item_group_id')]]=="" || $style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][trim_group][$row[csf('item_group_id')]]==0)
					{
						$cons_uom=$conversion_factor_array[$row[csf('item_group_id')]]['cons_uom'];
						$trim_dtla_id=max($style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][trim_dtla_id])+1;
						$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][trim_dtla_id][$trim_dtla_id]=$trim_dtla_id;// for rowspan
						$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][trim_group][$row[csf('item_group_id')]]=$row[csf('item_group_id')];
				        $style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][$row[csf('item_group_id')]][$trim_dtla_id]=$trim_dtla_id;// for rowspannn
						$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][trim_group_dtls][$trim_dtla_id]=$row[csf('item_group_id')];
						$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][cons_uom][$trim_dtla_id]=$cons_uom;
						
						$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][trim_group_from][$trim_dtla_id]="Trim Receive";

					}
				    $style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][inhouse_qnty][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				}
				$receive_qty_data=array();
				
				$receive_rtn_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id,sum(d.quantity) as quantity   from  inv_issue_master a,inv_transaction b, product_details_master c, order_wise_pro_details d,inv_receive_master e  where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and e.id=a.received_id   and b.transaction_type=3 and a.entry_form=49 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $order_cond2  group by d.po_breakdown_id, c.item_group_id order by c.item_group_id");
				foreach($receive_rtn_qty_data as $row)
				{
				//$style_data_arr[$row[csf('po_breakdown_id')]][receive_rtn_qty][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][receive_rtn_qty][$row[csf('item_group_id')]]+=$row[csf('quantity')];

				}
				$receive_rtn_qty_data=array();
				
				
				$issue_qty_data=sql_select("select b.po_breakdown_id, a.item_group_id, b.quantity as quantity  from  inv_issue_master d,product_details_master p,inv_trims_issue_dtls a , order_wise_pro_details b where a.mst_id=d.id and a.prod_id=p.id and a.trans_id=b.trans_id and b.trans_type=2 and b.entry_form=25 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $order_cond1");
				foreach($issue_qty_data as $row)
				{
				$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][issue_qty][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				}
				$issue_qty_data_arr=array();
				$sql_issue_ret=("select c.po_breakdown_id as po_id, p.item_group_id,SUM(c.quantity) as quantity
					from   inv_transaction b, order_wise_pro_details c,product_details_master p 
					where  c.entry_form=73 and p.id=b.prod_id  and b.id=c.trans_id and b.transaction_type=4 and c.is_deleted=0 and c.status_active=1 and b.status_active=1 and b.is_deleted=0  group by c.po_breakdown_id,p.item_group_id");					
				$issue_result=sql_select($sql_issue_ret);
				foreach($issue_result as $row)
				{
				$issue_qty_data_arr[$row[csf('po_id')]][issue_ret_qty][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				}
				
				$issue_qty_data=array();
				
				
				$total_pre_costing_value=0;	
				$total_wo_value=0;
				$summary_array=array();			
				$i=1;
				foreach($style_data_arr as $key=>$value)
				{
					
					 $rowspan=count($value[trim_dtla_id]);
					
							 
					  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";     
					?>
					<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
						<td width="30" title="<? echo $po_qty; ?>" rowspan="<? echo $rowspan; ?>"><p><? echo $i; ?>&nbsp;</p></td>
						<td width="50" rowspan="<? echo $rowspan; ?>"><p><? echo $buyer_short_name_library[$value[buyer_name]]; ?>&nbsp;</p></td>
						<td width="100" align="center" rowspan="<? echo $rowspan; ?>"><p><? echo $value[job_no_prefix_num]; ?>&nbsp;</p></td>
						<td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo $value[style_ref_no]; ?>&nbsp;</p></td>
                        <td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo $value[grouping]; ?></p></td>
                    	<td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo $value[file_no]; ?></p></td>
						<td width="90" rowspan="<? echo $rowspan; ?>">
                        <p>
                        <a href='#report_details' onclick="generate_report('<? echo $company_name; ?>','<? echo $value[job_no]; ?>','preCostRpt');">
						<? 
						$po_number=implode(",", $value[po_number]);
						$po_id=implode(",", $value[po_id]);
						echo $po_number; 
						?>
                        </a>&nbsp;
                        </p>
                        </td>
						<td width="80" align="right" rowspan="<? echo $rowspan; ?>">
                        <p>
                        <a href='#report_details' onclick="order_qty_popup('<? echo $company_name; ?>','<? echo $value[job_no]; ?>','<? echo $po_id; ?>', '<? echo $value[buyer_name]; ?>' ,'order_qty_data');"><? echo number_format(array_sum($value[order_quantity_set]),0,'.',''); ?>
                        </a>
                        &nbsp;
                        </p>
                        </td>
                        
						<td width="50" align="center" rowspan="<? echo $rowspan; ?>"><p><? echo $unit_of_measurement[$value[order_uom]]; ?>&nbsp;</p></td>
						<td width="80" align="right" rowspan="<? echo $rowspan; ?>"><p><? echo number_format(array_sum($value[order_quantity]),0,'.',''); ?>&nbsp;</p></td>
						<td width="80" align="center" rowspan="<? echo $rowspan; ?>">
                        <p>
						<? 
						$pub_shipment_date=implode(",", $value[pub_shipment_date]);
						echo $pub_shipment_date; 
						?>
                        &nbsp;
                        </p>
                        </td>
					<?
					foreach($value[trim_group] as $key_trim=>$value_trim)
				     {
					  $summary_array[trim_group][$key_trim]=$key_trim;
					 $gg=1;	
					 foreach($value[$key_trim] as $key_trim1=>$value_trim1)
				     { 
						 $rowspannn=count($value[$key_trim]);
						 if($gg==1)
						 {
					      
						  
					?>
								<td width="100" title="<? echo $value[trim_group_from][$key_trim1];  ?>">
									<p>
										<? 
										echo $item_library[$value[trim_group_dtls][$key_trim1]]; 
										//echo $value[trim_group_dtls][$key_trim1];
										?>
									&nbsp;</p>
								</td>
								<td width="100">
									<p>
										<?
										echo $value[brand_sup_ref][$key_trim1];
										//echo $row[csf('brand_sup_ref')]; 
										?>
									&nbsp;</p>
								</td>
								<td width="60" align="center">
                                <p>
								<? 
								 
								if($value[apvl_req][$key_trim1]==1) echo "Yes"; else echo "&nbsp;"; 
								?>
                                &nbsp;
                                </p>
                                </td>
								<td width="80" align="center">
                                <p>
								<? 
								if($value[apvl_req][$key_trim1]==1)
								{
									$app_status=$app_status_arr[$value[job_no]][$value[trim_group_dtls][$key_trim1]];
									$approved_status=$approval_status[$app_status];
									$summary_array[item_app][$key_trim][all]+=1;
									if($app_status==3)
									{
										$summary_array[item_app][$key_trim][app]+=1;
									}
								}
								else
								{
								$approved_status="";	
								}
								echo $approved_status; 
								?>
                                &nbsp;
                                </p>
                                </td>
							
                                <td width="100" align="right"><p>
								<? 
								$insert_date=explode(" ",$value[insert_date][$key_trim1]);
								echo change_date_format($insert_date[0],'','','');//echo change_date_format($row[csf('pre_date')],'','',1); 
								?>&nbsp;</p></td>
								<td width="100" align="right">
                                <p>
                                <a href='#report_details' onclick="order_req_qty_popup('<? echo $company_name; ?>','<? echo $value[job_no]; ?>','<? echo $po_id; ?>', '<? echo $value[buyer_name]; ?>','<? echo $value[rate][$key_trim1]; ?>','<? echo $value[trim_group_dtls][$key_trim1];?>' ,'<? echo $value[booking_no][$key_trim1];?>','<? echo $value[description][$key_trim1] ;?>','<? echo rtrim($value[country_id][$key_trim1],",");?>','<? echo $value[trim_dtla_id][$key_trim1];?>','<? echo $start_date ?>','<? echo $end_date ?>','order_req_qty_data');">
								<? 
								$req_qty=number_format($value[req_qnty][$key_trim1],2,'.','');
								echo $req_qty; 
								$summary_array[req_qnty][$key_trim]+=$value[req_qnty][$key_trim1];
								?>
                                </a>
                                &nbsp;
                                </p>
                                </td>
                                
								<td width="100" align="right"><p><? echo number_format($value[req_value][$key_trim1],2); $total_pre_costing_value+=$value[req_value][$key_trim1]; ?>&nbsp;</p></td>
                                <?
							   // $conversion_factor_rate=$conversion_factor_array[$row[csf('trim_group')]]['con_factor'];
							    $wo_qnty=number_format($value[wo_qnty][$key_trim1],2,'.','');
								if($wo_qnty > $req_qty)
								{
									$color_wo="red";	
								}
								
								else if($wo_qnty < $req_qty )
								{
									$color_wo="yellow";		
								}
								
								else 
								{
								$color_wo="";	
								}
								
								
								$supplier_name_string="";
								$supplier_id_arr=array_unique(explode(',',rtrim($value[supplier_id][$key_trim1],",")));
								//print_r($supplier_id_arr);
								foreach($supplier_id_arr as $supplier_id_arr_key=>$supplier_id_arr_value)
								{
									$supplier_name_string.=$lib_supplier_arr[$supplier_id_arr_value].",";
								}
								$booking_no_arr=array_unique(explode(',',rtrim($value[booking_no][$key_trim1],",")));
								//$booking_no_arr_d=implode(',',$booking_no_arr);
								//print $order_id.'='.	$trim_id;// $wo_qty_array[$order_id][$trim_id]['booking_no'];
								$main_booking_no_large_data="";
								foreach($booking_no_arr as $booking_no1)
								{	
									//if($booking_no1>0)
									//{
									if($main_booking_no_large_data=="") $main_booking_no_large_data=$booking_no1; else $main_booking_no_large_data.=",".$booking_no1;
									//}
									//print($main_booking_no_large_data);
								}
								?>
								<td width="90" align="right" title="<? echo 'conversion_factor='.$value[conversion_factor_rate][$key_trim1];?>" bgcolor="<? echo $color_wo;?>"><p><a href='#report_details' onclick="openmypage('<? echo $po_id; ?>','<? echo $value[trim_group_dtls][$key_trim1]; ?>','<? echo $value[job_no]; ?>','<? echo $main_booking_no_large_data;?>','<? echo $value[trim_dtla_id][$key_trim1];?>','booking_info');">
								<? 
								//$wo_qty_array[$row[csf('po_break_down_id')]][$row[csf('trim_group')]][$row[csf('trim_dtla_id')]]['wo_qnty']
								 echo number_format($value[wo_qnty][$key_trim1],2,'.','');
								 $summary_array[wo_qnty][$key_trim]+=$value[wo_qnty][$key_trim1];
								?>
                                </a>&nbsp;</p></td>
                                <td width="60" align="center">
                                <p>
								<? 
								echo $unit_of_measurement[$value[cons_uom][$key_trim1]]; 
								$summary_array[cons_uom][$key_trim]= $value[cons_uom][$key_trim1];
								?>
                                &nbsp;
                                </p>
                                </td>
                                <td width="100" align="right" title="<? echo number_format($value[rate][$key_trim1],2,'.',''); ?>">
                                <p>
								<?  
								echo number_format($value[amount][$key_trim1],2,'.',''); 
								$total_wo_value+=$value[amount][$key_trim1];
								?>
                                &nbsp;
                                </p>
                                </td>
                                
                                <td width="150" align="left">
                                <p>
								<?  
								echo rtrim($supplier_name_string,',');
								?>
                                
                                </p>
                                </td>
                                <td width="70" align="right" title="<? echo change_date_format($value[wo_date][$key_trim1]);?>"><p>
                                 <?
								 
								$tot=change_date_format($insert_date[0]);
								if($value[wo_qnty][$key_trim1]<=0 )
								{
								 $daysOnHand = datediff('d',$tot,$today);
								}
								else
								{
									$wo_date=$value[wo_date][$key_trim1];
									$wo_date=change_date_format($wo_date);
									$daysOnHand = datediff('d',$tot,$wo_date);;
								}
								 echo $daysOnHand; 
								?>&nbsp;</p>
                                </td>
                                <?
								$inhouse_qnty=$value[inhouse_qnty][$key_trim]-$value[receive_rtn_qty][$key_trim];
								$balance=$value[wo_qnty_trim_group][$key_trim]-$inhouse_qnty;
								$issue_qnty=$value[issue_qty][$key_trim];
								$issue_ret_qnty=$issue_qty_data_arr[$key][issue_ret_qty][$key_trim];
								$left_overqty=$inhouse_qnty-($issue_qnty-$issue_ret_qnty);
								$summary_array[inhouse_qnty][$key_trim]+=$inhouse_qnty;
								$summary_array[inhouse_qnty_bl][$key_trim]+=$balance;
								$summary_array[issue_qty][$key_trim]+=$issue_qnty-$issue_ret_qnty;
								$summary_array[left_overqty][$key_trim]+=$left_overqty;
								?>
                                
                                <td width="90" align="right" title="<? echo "Inhouse-Qty: ".$value[inhouse_qnty][$key_trim]."\nReturn Qty: ".$value[receive_rtn_qty][$key_trim]; ?>" rowspan="<? echo $rowspannn; ?>"><p><a href='#report_details' onclick="openmypage_inhouse('<? echo $po_id; ?>','<? echo $value[trim_group_dtls][$key_trim1]; ?>','booking_inhouse_info');"><? echo number_format($inhouse_qnty,2,'.',''); ?></a>&nbsp;</p></td>
								<td width="90" align="right" rowspan="<? echo $rowspannn; ?>"><p><? echo number_format($balance,2,'.',''); ?>&nbsp;</p></td>
								<td width="90" align="right"  title="<? echo "issue-Qty: ".$issue_qnty."\nReturn Qty: ".$issue_ret_qnty; ?>" rowspan="<? echo $rowspannn; ?>"><p><a href='#report_details' onclick="openmypage_issue('<? echo $po_id; ?>','<? echo $value[trim_group_dtls][$key_trim1]; ?>','booking_issue_info');"><? echo number_format($issue_qnty-$issue_ret_qnty,2,'.',''); ?></a>&nbsp;</p></td>
								<td align="right" rowspan="<? echo $rowspannn; ?>"><p><? echo number_format($left_overqty,2,'.',''); ?>&nbsp;</p></td>
                                <?
						 }
						 else
						 {
						 ?>
                                
								<td width="100" title="<? echo $value[trim_group_from][$key_trim1];  ?>">
									<p>
										<? echo $item_library[$value[trim_group_dtls][$key_trim1]]; ?>
									&nbsp;</p>
								</td>
								<td width="100">
									<p>
										<?
										echo $value[brand_sup_ref][$key_trim1];
										//echo $row[csf('brand_sup_ref')]; 
										?>
									&nbsp;</p>
								</td>
								<td width="60" align="center">
                                <p>
								<? 
								 
								if($value[apvl_req][$key_trim1]==1) echo "Yes"; else echo "&nbsp;"; 
								?>
                                &nbsp;
                                </p>
                                </td>
								<td width="80" align="center">
                                <p>
								<? 
								if($value[apvl_req][$key_trim1]==1)
								{
									
									$app_status=$app_status_arr[$value[job_no]][$value[trim_group_dtls][$key_trim1]];
									$approved_status=$approval_status[$app_status];
								    $summary_array[item_app][$key_trim][all]+=1;
									if($app_status==3)
									{
										$summary_array[item_app][$key_trim][app]+=1;
									}
								}
								else
								{
								$approved_status="";	
								}
								echo $approved_status; 
								?>
                                &nbsp;
                                </p>
                                </td>
							
                                <td width="100" align="right"><p>
								<? 
								$insert_date=explode(" ",$value[insert_date][$key_trim1]);
								echo change_date_format($insert_date[0],'','','');//echo change_date_format($row[csf('pre_date')],'','',1); 
								?>&nbsp;</p></td>
								<td width="100" align="right">
                                <p>
                                <a href='#report_details' onclick="order_req_qty_popup('<? echo $company_name; ?>','<? echo $value[job_no]; ?>','<? echo $po_id; ?>', '<? echo $value[buyer_name]; ?>','<? echo $value[rate][$key_trim1]; ?>','<? echo $value[trim_group_dtls][$key_trim1];?>' ,'<? echo $value[booking_no][$key_trim1];?>','<? echo $value[description][$key_trim1] ;?>','<? echo rtrim($value[country_id][$key_trim1],",");?>','<? echo $value[trim_dtla_id][$key_trim1];?>','<? echo $start_date ?>','<? echo $end_date ?>','order_req_qty_data');">
								<? 
								$req_qty=number_format($value[req_qnty][$key_trim1],2,'.','');
								echo $req_qty;  
								$summary_array[req_qnty][$key_trim]+=$value[req_qnty][$key_trim1];
								?>
                                </a>
                                &nbsp;
                                </p>
                                </td>
                                
								<td width="100" align="right"><p><? echo number_format($value[req_value][$key_trim1],2); $total_pre_costing_value+=$value[req_value][$key_trim1]; ?>&nbsp;</p></td>
                                <?
								$wo_qnty=number_format($value[wo_qnty][$key_trim1],2);
								if($wo_qnty > $req_qty)
								{
									$color_wo="red";	
								}
								
								else if($wo_qnty < $req_qty )
								{
									$color_wo="yellow";		
								}
								
								else 
								{
								$color_wo="";	
								}
								
								$supplier_name_string="";
								$supplier_id_arr=array_unique(explode(',',rtrim($value[supplier_id][$key_trim1],",")));
								//print_r($supplier_id_arr);
								foreach($supplier_id_arr as $supplier_id_arr_key=>$supplier_id_arr_value)
								{
									$supplier_name_string.=$lib_supplier_arr[$supplier_id_arr_value].",";
								}
								$booking_no_arr=array_unique(explode(',',rtrim($value[booking_no][$key_trim1],",")));
								//$booking_no_arr_d=implode(',',$booking_no_arr);
								//print $order_id.'='.	$trim_id;// $wo_qty_array[$order_id][$trim_id]['booking_no'];
								$main_booking_no_large_data="";
								foreach($booking_no_arr as $booking_no1)
								{	
									//if($booking_no1>0)
									//{
									if($main_booking_no_large_data=="") $main_booking_no_large_data=$booking_no1; else $main_booking_no_large_data.=",".$booking_no1;
									//}
									//print($main_booking_no_large_data);
								}
								?>
								<td width="90" align="right" title="<? echo 'conversion_factor='.$value[conversion_factor_rate][$key_trim1];?>" bgcolor="<? echo $color_wo;?>"><p><a href='#report_details' onclick="openmypage('<? echo $po_id; ?>','<? echo $value[trim_group_dtls][$key_trim1]; ?>','<? echo $value[job_no]; ?>','<? echo $main_booking_no_large_data;?>','<? echo $value[trim_dtla_id][$key_trim1];?>','booking_info');">
								<? 
								//$wo_qty_array[$row[csf('po_break_down_id')]][$row[csf('trim_group')]][$row[csf('trim_dtla_id')]]['wo_qnty']
								 echo number_format($value[wo_qnty][$key_trim1],2,'.','');
								 $summary_array[wo_qnty][$key_trim]+=$value[wo_qnty][$key_trim1];
								?>
                                </a>&nbsp;</p></td>
                                
                                <td width="60" align="center">
                                <p>
								<? 
								echo $unit_of_measurement[$value[cons_uom][$key_trim1]]; 
								$summary_array[cons_uom][$key_trim]= $value[cons_uom][$key_trim1];
								?>
                                &nbsp;</p></td>
                                
                                <td width="100" align="right" title="<? echo number_format($value[rate][$key_trim1],2,'.',''); ?>">
                                <p>
								<?  
								echo number_format($value[amount][$key_trim1],2,'.',''); 
								$total_wo_value+=$value[amount][$key_trim1];
								?>
                                &nbsp;
                                </p>
                                </td>
                                <td width="150" align="left">
                                <p>
								<?  
								echo rtrim($supplier_name_string,',');
								?>
                                
                                </p>
                                </td>
                                <td width="70" align="right" title="<? echo change_date_format($value[wo_date][$key_trim1]);?>"><p>
                                 <?
								$tot=change_date_format($insert_date[0]);
								if($value[wo_qnty][$key_trim1]<=0 )
								{
								 $daysOnHand = datediff('d',$tot,$today);
								}
								else
								{
									$wo_date=$value[wo_date][$key_trim1];
									$wo_date=change_date_format($wo_date);
									$daysOnHand = datediff('d',$tot,$wo_date);;
								}
								 echo $daysOnHand; 
								?>&nbsp;</p>
                                </td>
                                <?
						 }
								?>
							 </tr>
							
					<?
					
						//$total_wo_qnty+=$wo_qty_array[$row[csf('id')]][$row[csf('trim_group')]][$row[csf('trim_dtla_id')]]['wo_qnty']*$conversion_factor_rate;
						//$total_wo_value+=$wo_qty_array[$order_id][$trim_id]['amount'];
						//$total_in_qnty+=$inhouse_qnty;
						//$rec_bal=$wo_qty_array[$row[csf('id')]][$selectResult[('trim_group')]]['wo_qnty']-$inhouse_qnty;
						//$total_rec_bal_qnty+=$balance;
						
						//$total_issue_qnty+=$issue_qnty;
						//$total_leftover_qnty+=$left_overqty;
							
							
					   // $item_array[$row[csf('trim_group')]]['req']+=$req_qnty;
						//$item_array[$row[csf('trim_group')]]['wo']+=$wo_qty_array[$row[csf('id')]][$row[csf('trim_group')]][$row[csf('trim_dtla_id')]]['wo_qnty']*$conversion_factor_rate;
						//$item_array[$row[csf('trim_group')]]['in']+=$inhouse_qnty;
						//$item_array[$row[csf('trim_group')]]['issue']+=$issue_qnty;
						//$item_array[$row[csf('trim_group')]]['leftover']+=$left_overqty;
						
						$gg++;
			    }// end  foreach($value[$key_trim] as $key_trim1=>$value_trim1)
				}
				?>
                <?
				$i++;
				}
				
				?>
                 
				</table>
				<table class="rpt_table" width="2360" cellpadding="0" cellspacing="0" border="1" rules="all">
					<tfoot>
						<th width="30"></th>
						<th width="50"></th>
						<th width="100"></th>
						<th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
						<th width="90"></th>
						<th width="80" align="right" id="total_order_qnty"><? //echo number_format($total_order_qnty,0); ?></th>
						<th width="50"></th>
						<th width="80" align="right" id="total_order_qnty_in_pcs"><? //echo number_format($total_order_qnty_in_pcs,0); ?></th>
						<th width="80"></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="60"></th>
						<th width="80"></th>
						<th width="100"></th>
						<th width="100" align="right" id="value_req_qnty"><? //echo number_format($total_req_qnty,2); ?></th>
						<th width="100" align="right" id="value_pre_costing"><? echo number_format($total_pre_costing_value,2); ?></th>
						<th width="90" align="right" id="value_wo_qty"><? //echo number_format($total_wo_qnty,2); ?></th>
                        <th width="60" align="right" ></th>
                        <th width="100" align="right" id=""><? echo number_format($total_wo_value,2); ?></th>
                         <th width="150" align="right" id=""></th>
                        <th width="70" align="right"><p><? //echo number_format($req_value,2,'.',''); ?>&nbsp;</p></th>
                        <th width="90" align="right" id="value_in_qty"><? //echo number_format($total_in_qnty,2); ?></th>
						<th width="90" align="right" id="value_rec_qty"><? //echo number_format($total_rec_bal_qnty,2); ?></th>
						<th width="90" align="right" id="value_issue_qty"><? //echo number_format($total_issue_qnty,2); ?></th>
						<th align="right" id="value_leftover_qty"><? //echo number_format($total_leftover_qnty,2); ?></th>
					</tfoot>
				</table>
				</div>
				<table>
					<tr><td height="15"></td></tr>
				</table>
				<u><b>Summary</b></u>
				<table class="rpt_table" width="1200" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<th width="30">SL</th>
						<th width="110">Item</th>
						<th width="60">UOM</th>
						<th width="80">Approved %</th>
						<th width="110">Req Qty</th>
						<th width="110">WO Qty</th>
						<th width="80">WO %</th>
						<th width="110">In-House Qty</th>
						<th width="80">In-House %</th>
						<th width="110">In-House Balance Qty</th>
						<th width="110">Issue Qty</th>
						<th width="80">Issue %</th>
						<th>Left Over</th>
					</thead>
					<?
					$z=1; $tot_req_qnty_summary=0;
					foreach($summary_array[trim_group] as $key_trim=>$value)
					{
						if($z%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						//$tot_req_qnty_summary+=$value['req'];
						//$tot_wo_qnty_summary+=$value['wo'];
						//$tot_in_qnty_summary+=$value['in'];
						//$tot_issue_qnty_summary+=$value['issue'];
						//$tot_leftover_qnty_summary+=$value['leftover'];
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr2_<? echo $z; ?>','<? echo $bgcolor;?>')" id="tr2_<? echo $z; ?>">
							<td width="30"><? echo $z; ?></td>
							<td width="110"><p><? echo $item_library[$key_trim]; ?></p></td>
							<td width="60" align="center">
							<? 
							echo $unit_of_measurement[$summary_array[cons_uom][$key_trim]]; 
							?></td>  
							<td width="80" align="right"><? $app_perc=($summary_array[item_app][$key_trim][app]*100)/$summary_array[item_app][$key_trim][all]; echo number_format($app_perc,2); ?>&nbsp;</td>
							<td width="110" align="right"><? echo number_format($summary_array[req_qnty][$key_trim],2); ?>&nbsp;</td>
							<td width="110" align="right"><? echo number_format( $summary_array[wo_qnty][$key_trim],2); ?>&nbsp;</td>
							<td width="80" align="right"><? $wo_per= $summary_array[wo_qnty][$key_trim]/$summary_array[req_qnty][$key_trim]*100; echo number_format($wo_per,2).'%'; ?>&nbsp;</td>
							<td width="110" align="right"><? echo number_format($summary_array[inhouse_qnty][$key_trim],2); ?>&nbsp;</td>
							<td width="80" align="right"><? $in_per=$summary_array[inhouse_qnty][$key_trim]/$summary_array[wo_qnty][$key_trim]*100; echo number_format($in_per,2).'%'; ?>&nbsp;</td>
							<td width="110" align="right"><? echo number_format($summary_array[inhouse_qnty_bl][$key_trim],2); $in_house_bal+=($value['wo']-$value['in']); ?>&nbsp;</td>
							<td width="110" align="right"><? echo number_format($summary_array[issue_qty][$key_trim],2); ?>&nbsp;</td>
							<td width="80" align="right"><? $wo_per=$summary_array[issue_qty][$key_trim]/$summary_array[wo_qnty][$key_trim]*100; echo number_format($wo_per,2).'%'; ?>&nbsp;</td>
							<td align="right"><? echo number_format($summary_array[left_overqty][$key_trim],2); ?>&nbsp;</td>
						</tr>
					<?	
					$z++;
					}
					?>
					<tfoot>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th align="right"><? //echo number_format($tot_req_qnty_summary,2); ?>&nbsp;</th>
						<th align="right"><? //echo number_format($tot_wo_qnty_summary,2); ?>&nbsp;</th>
						<th>&nbsp;</th>
						<th align="right"><? //echo number_format($tot_in_qnty_summary,2); ?>&nbsp;</th>
						<th>&nbsp;</th>
						<th align="right"><? //echo number_format($in_house_bal,2); ?>&nbsp;</th>
						<th align="right"><? //echo number_format($tot_issue_qnty_summary,2); ?>&nbsp;</th>
						<th>&nbsp;</th>
						<th align="right"><? //echo number_format($tot_leftover_qnty_summary,2); ?>&nbsp;</th>
					</tfoot>   	
				</table>
			</fieldset>
		</div>
	<?
	}
}


	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename****$tot_rows****$action";
	exit();	
}

if($action=="report_generate2"){
	 
	$company_library=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	//$costing_per_id_library=return_library_array( "select job_no, costing_per from wo_pre_cost_mst", "job_no", "costing_per");
	$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
	//$color_name_library=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	$country_name_library=return_library_array( "select id, country_name from lib_country", "id", "country_name"  );
	//$order_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number"  );
	$lib_supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_name=str_replace("'","",$cbo_company_name);
	$serch_by=str_replace("'","",$cbo_search_by);
	$buyer_id_cond="";
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	
	//echo '=='.$cbo_company_name.'___'.$txt_job_no.'___'.$txt_order_no.'___';
	
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_job_no=trim($txt_job_no);
	if($txt_job_no !="" || $txt_job_no !=0)
	{
		//$year = substr(str_replace("'","",$cbo_year_selection), -2); 
		//$job_no=$company_library[$company_name]."-".$year."-".str_pad($txt_job_no, 5, 0, STR_PAD_LEFT);
		//$jobcond="and a.job_no='".$job_no."'";
		$jobcond="and a.job_no_prefix_num='".$txt_job_no."'";

	}
	else
	{
		$jobcond="";	
	}
	
	
	if(str_replace("'","",$cbo_item_group)=="")
	{
		$item_group_cond="";
	}
	else
	{
		$item_group_cond="and e.trim_group in(".str_replace("'","",$cbo_item_group).")";
	}
	
	
	$date_cond='';
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		$start_date=(str_replace("'","",$txt_date_from));
		$end_date=(str_replace("'","",$txt_date_to));
		$date_cond="and c.country_ship_date between '$start_date' and '$end_date'";
		
	}
	if (str_replace("'","",$txt_job_no)=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in (".str_replace("'","",$txt_job_no).") ";
	
	if(str_replace("'","",$txt_style_ref)!="") $style_ref_cond=" and a.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'"; else $style_ref_cond="";
	if(str_replace("'","",$txt_order_no)!="")
	{
		$ordercond=" and b.po_number like '%".str_replace("'","",$txt_order_no)."%'"; 
	}
	else
	{
		$ordercond="";
	}
	$file_no=str_replace("'","",$txt_file_no);
	$internal_ref=str_replace("'","",$txt_internal_ref);
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' "; 
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' "; 
	//echo $file_no_cond.'=='.$internal_ref_cond;die;

  if(str_replace("'","",$cbo_search_by)==1)
  {
	if($template==1)
	{
		ob_start();
	?>
		<div style="width:2600px">
		<fieldset style="width:100%;">	
			<table width="2600">
				<tr class="form_caption">
					<td colspan="27" align="center">Accessories Followup Report</td>
				</tr>
				<tr class="form_caption">
					<td colspan="27" align="center"><? echo $company_library[$company_name]; ?></td>
				</tr>
			</table>
			<table class="rpt_table" width="2560" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<th width="30">SL</th>
					<th width="50">Buyer</th>
					<th width="100">Job No</th>
                     <th width="100">Style Ref</th>
					<th width="100">Internal Ref</th>
                    <th width="100">File No</th>
                   
					<th width="90">Order No</th>
					<th width="80">Order Qnty</th>
					<th width="50">UOM</th>
					<th width="80">Qnty (Pcs)</th>
					<th width="80">Shipment Date</th>
					<th width="100">Trims Name</th>
                    <th width="100">Remark</th>
					<th width="100">Brand/Sup Ref</th>
					<th width="60">Appr Req.</th>
					<th width="80">Approve Status</th>
                    <th width="100">Item Entry Date</th>
					<th width="100">Req Qnty</th>
					<th width="100">Pre Costing Value</th>
					<th width="90">WO Qnty</th>
                    <th width="60">Trims UOM</th>
                    <th width="100">WO Value</th>
                    <th width="150">Supplier</th>
                    <th width="70">WO Delay Days</th>
					<th width="90">In-House Qnty</th>
					<th width="90">In-House Value</th>
					<th width="90">Receive Balance</th>
					<th width="90">Issue to Prod.</th>
					<th>Left Over/Balance</th>
				</thead>
			</table>
			<div style="width:2580px; max-height:400px; overflow-y:scroll" id="scroll_body">
				<table class="rpt_table" width="2560" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
	<?
	$conversion_factor_array=array();
	$conversion_factor=sql_select("select id ,trim_uom,conversion_factor from  lib_item_group  ");
	foreach($conversion_factor as $row_f)
	{
	 $conversion_factor_array[$row_f[csf('id')]]['con_factor']=$row_f[csf('conversion_factor')];
	 $conversion_factor_array[$row_f[csf('id')]]['cons_uom']=$row_f[csf('trim_uom')];
	}
	$conversion_factor=array();
	$app_sql=sql_select("select job_no_mst,accessories_type_id,approval_status from wo_po_trims_approval_info");
	$app_status_arr=array();
	foreach($app_sql as $row)
	{
		$app_status_arr[$row[csf("job_no_mst")]][$row[csf("accessories_type_id")]]=$row[csf("approval_status")];
	}
	$app_sql=array();
	
	$sql_po_qty_country_wise_arr=array();
	$po_job_arr=array();
	$sql_po_qty_country_wise=sql_select("select  b.id,b.job_no_mst,c.country_id, sum(c.order_quantity/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst  and b.id=c.po_break_down_id   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond group by   b.id,b.job_no_mst,c.country_id order by b.id,b.job_no_mst,c.country_id");
	foreach( $sql_po_qty_country_wise as $sql_po_qty_country_wise_row)
	{
	$sql_po_qty_country_wise_arr[$sql_po_qty_country_wise_row[csf('id')]][$sql_po_qty_country_wise_row[csf('country_id')]]=$sql_po_qty_country_wise_row[csf('order_quantity_set')];
	$po_job_arr[$sql_po_qty_country_wise_row[csf('id')]]=$sql_po_qty_country_wise_row[csf('job_no_mst')];
	}
	$sql_po_qty_country_wise=array();
 
    
	$po_data_arr=array();
	$po_id_string="";
	$today=date("Y-m-d");
	/*$sql_query=sql_select("select a.buyer_name,a.job_no,a.job_no_prefix_num,style_ref_no,b.file_no,b.grouping, b.id,b.po_number,a.order_uom,sum(distinct b.po_quantity) as po_quantity,a.total_set_qnty  ,sum(distinct b.po_quantity*a.total_set_qnty) as po_quantity_psc ,sum(c.order_quantity) as order_quantity ,sum(c.order_quantity/a.total_set_qnty) as order_quantity_set,  b.pub_shipment_date,
	d.costing_per,
	e.id as trim_dtla_id,
	e.trim_group,
	e.remark,
	e.description,
	e.brand_sup_ref,
	e.cons_uom,
	e.cons_dzn_gmts,
	e.rate,
	e.amount,
	e.apvl_req,
	e.nominated_supp,
	e.insert_date,
	f.cons,
	f.country_id,
	f.cons as cons_cal
	from 
	wo_po_details_master a, 
	wo_po_break_down b,
	wo_po_color_size_breakdown c
	left join 
	wo_pre_cost_trim_co_cons_dtls f 
	on
	c.job_no_mst=f.job_no and
	c.po_break_down_id=f.po_break_down_id and
	f.cons > 0
	join
	wo_pre_cost_trim_cost_dtls e
	on 
	f.job_no=e.job_no  and
	e.id=f.wo_pre_cost_trim_cost_dtls_id 
	$item_group_cond
	join 
	wo_pre_cost_mst d
	on
	e.job_no =d.job_no
	where 
	a.job_no=b.job_no_mst   and 
	a.job_no=c.job_no_mst   and 
	b.id=c.po_break_down_id and 
	a.is_deleted=0          and 
	a.status_active=1       and 
	b.is_deleted=0          and 
	b.status_active=1       and 
	c.is_deleted=0          and 
	c.status_active=1       and 
	a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond  $ordercond $file_no_cond $internal_ref_cond
	group by a.buyer_name,a.job_no,a.job_no_prefix_num,style_ref_no,b.file_no,b.grouping, b.id,b.po_number,a.order_uom,a.total_set_qnty,b.pub_shipment_date,d.costing_per,
	e.id,
	e.trim_group,
	e.remark,
	e.description,
	e.brand_sup_ref,
	e.cons_uom,
	e.cons_dzn_gmts,
	e.rate,
	e.amount,
	e.apvl_req,
	e.nominated_supp,
	e.insert_date,
	f.cons,
	f.pcs,
	f.country_id
	order by b.id, e.trim_group
	");*/
	$sql_query=sql_select("select a.buyer_name,a.job_no,a.job_no_prefix_num,style_ref_no,b.file_no,b.grouping, b.id,b.po_number,a.order_uom,sum(c.order_quantity) as order_quantity ,sum(c.order_quantity/a.total_set_qnty) as order_quantity_set,  b.pub_shipment_date,
	d.costing_per,
	e.id as trim_dtla_id,
	e.trim_group,
	e.remark,
	e.description,
	e.brand_sup_ref,
	e.cons_uom,
	e.cons_dzn_gmts,
	e.rate,
	e.amount,
	e.apvl_req,
	e.nominated_supp,
	e.insert_date,
	f.cons,
	f.country_id,
	f.cons as cons_cal
	from 
	wo_po_details_master a, 
	wo_po_break_down b,
	wo_po_color_size_breakdown c
	left join 
	wo_pre_cost_trim_co_cons_dtls f 
	on
	c.job_no_mst=f.job_no and
	c.po_break_down_id=f.po_break_down_id and
	f.cons > 0
	join
	wo_pre_cost_trim_cost_dtls e
	on 
	f.job_no=e.job_no  and
	e.id=f.wo_pre_cost_trim_cost_dtls_id 
	$item_group_cond
	join 
	wo_pre_cost_mst d
	on
	e.job_no =d.job_no
	where 
	a.job_no=b.job_no_mst   and 
	a.job_no=c.job_no_mst   and 
	b.id=c.po_break_down_id and 
	a.is_deleted=0          and 
	a.status_active=1       and 
	b.is_deleted=0          and 
	b.status_active=1       and 
	c.is_deleted=0          and 
	c.status_active=1       and 
	a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond  $ordercond $file_no_cond $internal_ref_cond
	group by a.buyer_name,a.job_no,a.job_no_prefix_num,style_ref_no,b.file_no,b.grouping, b.id,b.po_number,a.order_uom,a.total_set_qnty,b.pub_shipment_date,d.costing_per,
	e.id,
	e.trim_group,
	e.remark,
	e.description,
	e.brand_sup_ref,
	e.cons_uom,
	e.cons_dzn_gmts,
	e.rate,
	e.amount,
	e.apvl_req,
	e.nominated_supp,
	e.insert_date,
	f.cons,
	f.pcs,
	f.country_id
	order by b.id, e.trim_group
	");//sum(distinct b.po_quantity) as po_quantity,a.total_set_qnty  ,sum(distinct b.po_quantity*a.total_set_qnty) as po_quantity_psc ,
	
				$tot_rows=count($sql_query);
				$i=1;
				foreach($sql_query as $row)
				{
					      
						   
						    $dzn_qnty=0;
							if($row[csf('costing_per')]==1)
							{
								$dzn_qnty=12;
							}
							else if($row[csf('costing_per')]==3)
							{
								$dzn_qnty=12*2;
							}
							else if($row[csf('costing_per')]==4)
							{
								$dzn_qnty=12*3;
							}
							else if($row[csf('costing_per')]==5)
							{
								$dzn_qnty=12*4;
							}
							else
							{
								$dzn_qnty=1;
							}
							
							
							 $po_qty=0;
							 if($row[csf('country_id')]==0)
							 {
								$po_qty=$row[csf('order_quantity')];
							 }
							 else
							 {
								$country_id= explode(",",$row[csf('country_id')]);
								for($cou=0;$cou<=count($country_id); $cou++)
								{
								$po_qty+=$sql_po_qty_country_wise_arr[$row[csf('id')]][$country_id[$cou]];
								}
							 }
							 
							 $req_qnty=($row[csf('cons_cal')]/$dzn_qnty)*$po_qty;
							 $req_value= $row[csf('rate')]*$req_qnty;
							 
							 $po_data_arr[$row[csf('id')]][job_no]=$row[csf('job_no')];
							 $po_data_arr[$row[csf('id')]][buyer_name]=$row[csf('buyer_name')];
							 $po_data_arr[$row[csf('id')]][job_no_prefix_num]=$row[csf('job_no_prefix_num')];
							 $po_data_arr[$row[csf('id')]][style_ref_no]=$row[csf('style_ref_no')];
							 
							 $po_data_arr[$row[csf('id')]][grouping]=$row[csf('grouping')];
							 $po_data_arr[$row[csf('id')]][file_no]=$row[csf('file_no')];
							 $po_data_arr[$row[csf('id')]][order_uom]=$row[csf('order_uom')];
							 $po_data_arr[$row[csf('id')]][po_id]=$row[csf('id')];
							 $po_data_arr[$row[csf('id')]][po_number]=$row[csf('po_number')];
							 $po_data_arr[$row[csf('id')]][order_quantity_set]=$row[csf('order_quantity_set')];
							 $po_data_arr[$row[csf('id')]][order_quantity]=$row[csf('order_quantity')];
							 $po_data_arr[$row[csf('id')]][pub_shipment_date]=change_date_format($row[csf('pub_shipment_date')]);
							 $po_id_string.=$row[csf('id')].",";
							 
							 $po_data_arr[$row[csf('id')]][trim_dtla_id][$row[csf('trim_dtla_id')]]=$row[csf('trim_dtla_id')];// for rowspan
							 $po_data_arr[$row[csf('id')]][trim_group][$row[csf('trim_group')]]=$row[csf('trim_group')];
							
							 
							 $po_data_arr[$row[csf('id')]][$row[csf('trim_group')]][$row[csf('trim_dtla_id')]]=$row[csf('trim_dtla_id')]; // for rowspannn
							 $po_data_arr[$row[csf('id')]][trim_group_dtls][$row[csf('trim_dtla_id')]]=$row[csf('trim_group')];
							 
							
							  $po_data_arr[$row[csf('id')]][remark][$row[csf('trim_dtla_id')]]=$row[csf('remark')];
							  
							 $po_data_arr[$row[csf('id')]][brand_sup_ref][$row[csf('trim_dtla_id')]]=$row[csf('brand_sup_ref')];
							 $po_data_arr[$row[csf('id')]][apvl_req][$row[csf('trim_dtla_id')]]=$row[csf('apvl_req')];
							 $po_data_arr[$row[csf('id')]][insert_date][$row[csf('trim_dtla_id')]]=$row[csf('insert_date')];
							 $po_data_arr[$row[csf('id')]][req_qnty][$row[csf('trim_dtla_id')]]+=$req_qnty;
							 $po_data_arr[$row[csf('id')]][req_value][$row[csf('trim_dtla_id')]]+=$req_value;
							 $po_data_arr[$row[csf('id')]][cons_uom][$row[csf('trim_dtla_id')]]=$row[csf('cons_uom')];
							 
							 $po_data_arr[$row[csf('id')]][trim_group_from][$row[csf('trim_dtla_id')]]="Pre_cost";
							 
							 $po_data_arr[$row[csf('id')]][rate][$row[csf('trim_dtla_id')]]=$row[csf('rate')];
							 $po_data_arr[$row[csf('id')]][description][$row[csf('trim_dtla_id')]]=$row[csf('description')];
							 $po_data_arr[$row[csf('id')]][country_id][$row[csf('trim_dtla_id')]]=$row[csf('country_id')];
							 
							// $style_data_arr[$row[csf('job_no')]][wo_qnty][$row[csf('trim_dtla_id')]]+=$wo_qnty;
							// $style_data_arr[$row[csf('job_no')]][amount][$row[csf('trim_dtla_id')]]+=$amount;
							// $style_data_arr[$row[csf('job_no')]][wo_date][$row[csf('trim_dtla_id')]]=$wo_date;
							// $style_data_arr[$row[csf('job_no')]][wo_qnty_trim_group][$row[csf('trim_group')]]+=$wo_qnty;
				}
				$sql_query=array();
				$po_id_string=rtrim($po_id_string,",");
				if($po_id_string=="")
				{
				echo "<strong style='color:red;font-size:30px'>No Data found</strong>";
				die;
				}
				$po_id=array_chunk(array_unique(explode(",",$po_id_string)),1000, true);
				 $order_cond="";
				   $ji=0;
				   foreach($po_id as $key=> $value)
				   {
					   if($ji==0)
					   {
						$order_cond=" and b.po_break_down_id  in(".implode(",",$value).")"; 
						$order_cond1=" and b.po_breakdown_id  in(".implode(",",$value).")"; 
						$order_cond2=" and d.po_breakdown_id  in(".implode(",",$value).")"; 
					   }
					   else
					   {
						$order_cond.=" or b.po_break_down_id  in(".implode(",",$value).")";
						$order_cond1.=" or b.po_breakdown_id  in(".implode(",",$value).")";
						$order_cond2.=" or d.po_breakdown_id  in(".implode(",",$value).")";
					   }
					   $ji++;
				   }
				 $po_id=array();
				
				if($db_type==2)
				{
				  $wo_sql_without_precost=sql_select("select min(a.booking_date) as booking_date ,b.job_no,LISTAGG(CAST(a.booking_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as booking_no,LISTAGG(CAST(a.supplier_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as supplier_id, b.po_break_down_id, b.trim_group,b.pre_cost_fabric_cost_dtls_id, sum(b.wo_qnty) as wo_qnty,sum(b.amount/b.exchange_rate) as amount,sum(b.rate) as rate from wo_booking_mst a, wo_booking_dtls b where a.item_category=4 and a.booking_no=b.booking_no  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name $order_cond group by b.po_break_down_id,b.trim_group,b.job_no,b.pre_cost_fabric_cost_dtls_id");//and item_from_precost=2
				}
				else if($db_type==0)
				{
				  $wo_sql_without_precost=sql_select("select min(a.booking_date) as booking_date ,b.job_no,group_concat(a.booking_no) as booking_no,group_concat(a.supplier_id) as supplier_id, b.po_break_down_id, b.trim_group,b.pre_cost_fabric_cost_dtls_id,sum(b.wo_qnty) as wo_qnty,sum(b.amount/b.exchange_rate) as amount,sum(b.rate) as rate from wo_booking_mst a, wo_booking_dtls b where a.item_category=4 and a.booking_no=b.booking_no  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name $order_cond  group by b.po_break_down_id,b.trim_group,b.job_no,b.pre_cost_fabric_cost_dtls_id");//and item_from_precost=2
				}
				$style_data_arr1=array();
				foreach($wo_sql_without_precost as $wo_row_without_precost)
				{
					
					$conversion_factor_rate=$conversion_factor_array[$wo_row_without_precost[csf('trim_group')]]['con_factor'];
					$cons_uom=$conversion_factor_array[$wo_row_without_precost[csf('trim_group')]]['cons_uom'];
					$booking_no=$wo_row_without_precost[csf('booking_no')];
					$supplier_id=$wo_row_without_precost[csf('supplier_id')];
					$wo_qnty=$wo_row_without_precost[csf('wo_qnty')]*$conversion_factor_rate;
					$amount=$wo_row_without_precost[csf('amount')];
					$wo_date=$wo_row_without_precost[csf('booking_date')];
					
					if($wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')] =="" || $wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')] ==0)
					{
					    $trim_dtla_id=max($po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][trim_dtla_id])+1;
						$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][trim_dtla_id][$trim_dtla_id]=$trim_dtla_id;// for rowspan
					    $po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][trim_group][$wo_row_without_precost[csf('trim_group')]]=$wo_row_without_precost[csf('trim_group')];
				        $po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][$wo_row_without_precost[csf('trim_group')]][$trim_dtla_id]=$trim_dtla_id;// for rowspannn
						$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][trim_group_dtls][$trim_dtla_id]=$wo_row_without_precost[csf('trim_group')];
						$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][cons_uom][$trim_dtla_id]=$cons_uom;
						
						$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][trim_group_from][$trim_dtla_id]="Booking Without Pre_cost";
					}
					else
					{
						$trim_dtla_id=$wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')];
						
					}
					//$style_data_arr[$wo_row_without_precost[csf('job_no')]][trim_dtla_id][$trim_dtla_id]=$trim_dtla_id;// for rowspan
					//$style_data_arr[$wo_row_without_precost[csf('job_no')]][trim_group][$wo_row_without_precost[csf('trim_group')]]=$wo_row_without_precost[csf('trim_group')];
				   // $style_data_arr[$wo_row_without_precost[csf('job_no')]][$wo_row_without_precost[csf('trim_group')]][$trim_dtla_id]=$trim_dtla_id;// for rowspannn
				    //$style_data_arr[$wo_row_without_precost[csf('job_no')]][trim_group_dtls][$trim_dtla_id]=$wo_row_without_precost[csf('trim_group')];
					
					$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][wo_qnty][$trim_dtla_id]+=$wo_qnty;
					$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][amount][$trim_dtla_id]+=$amount;
					$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][wo_date][$trim_dtla_id]=$wo_date;
					$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][wo_qnty_trim_group][$wo_row_without_precost[csf('trim_group')]]+=$wo_qnty;
					
					$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][booking_no][$trim_dtla_id]=$booking_no;
					$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][supplier_id][$trim_dtla_id]=$supplier_id;
					$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][conversion_factor_rate][$trim_dtla_id]=$conversion_factor_rate;
					
				
					 
				}
				$wo_sql_without_precost=array();
				
				$receive_qty_data=sql_select("select b.po_breakdown_id, a.item_group_id,sum(b.quantity) as quantity,a.rate  from  inv_receive_master c,product_details_master d,inv_trims_entry_dtls a , order_wise_pro_details b where a.mst_id=c.id and a.trans_id=b.trans_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $order_cond1  group by b.po_breakdown_id, a.item_group_id,a.rate order by a.item_group_id ");
				 
				foreach($receive_qty_data as $row)
				{
					if($po_data_arr[$row[csf('po_breakdown_id')]][trim_group][$row[csf('item_group_id')]]=="" || $po_data_arr[$row[csf('po_breakdown_id')]][trim_group][$row[csf('item_group_id')]]==0)
					{
						$cons_uom=$conversion_factor_array[$row[csf('item_group_id')]]['cons_uom'];
						$trim_dtla_id=max($po_data_arr[$row[csf('po_breakdown_id')]][trim_dtla_id])+1;
						$po_data_arr[$row[csf('po_breakdown_id')]][trim_dtla_id][$trim_dtla_id]=$trim_dtla_id;// for rowspan
						$po_data_arr[$row[csf('po_breakdown_id')]][trim_group][$row[csf('item_group_id')]]=$row[csf('item_group_id')];
				        $po_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$trim_dtla_id]=$trim_dtla_id;// for rowspannn
						$po_data_arr[$row[csf('po_breakdown_id')]][trim_group_dtls][$trim_dtla_id]=$row[csf('item_group_id')];
						$po_data_arr[$row[csf('po_breakdown_id')]][cons_uom][$trim_dtla_id]=$cons_uom;
						$po_data_arr[$row[csf('po_breakdown_id')]][trim_group_from][$trim_dtla_id]="Trim Receive";
					}
				    $po_data_arr[$row[csf('po_breakdown_id')]][inhouse_qnty][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				    $po_data_arr[$row[csf('po_breakdown_id')]][inhouse_rate][$row[csf('item_group_id')]]=$row[csf('rate')];
				}
				
				$receive_qty_data=array();
				$receive_rtn_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id,sum(d.quantity) as quantity   from  inv_issue_master a,inv_transaction b, product_details_master c, order_wise_pro_details d,inv_receive_master e  where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and e.id=a.received_id   and b.transaction_type=3 and a.entry_form=49 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $order_cond2  group by d.po_breakdown_id, c.item_group_id order by c.item_group_id");
				
				foreach($receive_rtn_qty_data as $row)
				{
				$po_data_arr[$row[csf('po_breakdown_id')]][receive_rtn_qty][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				}
				$receive_rtn_qty_data=array();
				
				$issue_qty_data=sql_select("select b.po_breakdown_id, a.item_group_id, b.quantity as quantity  from  inv_issue_master d,product_details_master p,inv_trims_issue_dtls a , order_wise_pro_details b where a.mst_id=d.id and a.prod_id=p.id and a.trans_id=b.trans_id and b.trans_type=2 and b.entry_form=25 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $order_cond1");
				foreach($issue_qty_data as $row)
				{
				$po_data_arr[$row[csf('po_breakdown_id')]][issue_qty][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				}
				$issue_qty_data_arr=array();
				$sql_issue_ret=("select c.po_breakdown_id as po_id, p.item_group_id,SUM(c.quantity) as quantity
					from   inv_transaction b, order_wise_pro_details c,product_details_master p 
					where  c.entry_form=73 and p.id=b.prod_id  and b.id=c.trans_id and b.transaction_type=4 and c.is_deleted=0 and c.status_active=1 and b.status_active=1 and b.is_deleted=0  group by c.po_breakdown_id,p.item_group_id");					
				$issue_result=sql_select($sql_issue_ret);
				foreach($issue_result as $row)
				{
				$issue_qty_data_arr[$row[csf('po_id')]][issue_ret_qty][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				}
				
				$issue_qty_data=array();
				$total_pre_costing_value=0;
				$total_wo_value=0;
				$summary_array=array();
				$i=1;
				$x=0;
				foreach($po_data_arr as $key=>$value)
				{ 
				    $z=1;
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";    
					foreach($value[trim_group] as $key_trim=>$value_trim)
					{   $y=1;
						$summary_array[trim_group][$key_trim]=$key_trim;
						foreach($value[$key_trim] as $key_trim1=>$value_trim1)
						{ 
							if($z==1){
								
								$style_color='';
							}
							else{
								$style_color=$bgcolor."; border: none";
							}
							$z++;
							
							if($y==1){
								
								$style_colory='';
							}
							else{
								$style_colory=$bgcolor."; border: none";
							}
							$x++;
							$y++;
					?>
                    <tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $x; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $x; ?>">
						<td width="30" style=" color: <? echo $style_color ?>" title="<? echo $po_qty; ?>"  ><? echo $i; ?></td>
						<td width="50" style=" color: <? echo $style_color ?>"><p><? echo $buyer_short_name_library[$value[buyer_name]]; ?>&nbsp;</p></td>
						<td width="100" style=" color: <? echo $style_color ?>" align="center" ><p><? echo $value[job_no_prefix_num]; ?>&nbsp;</p></td>
						<td width="100"  style="word-break: break-all;color: <? echo $style_color ?>"><p><? echo $value[style_ref_no]; ?>&nbsp;</p></td>
                        <td width="100"  style="word-break: break-all;color: <? echo $style_color ?>"><p><? echo $value[grouping]; ?></p></td>
                    	<td width="100" style="word-break: break-all;color: <? echo $style_color ?>"><p><? echo $value[file_no]; ?></p></td>
						<td width="90"  style="word-break: break-all;color: <? echo $style_color ?>">
                        <p>
                        <a style="word-break: break-all;color: <? echo $style_color ?>" href='#report_details' onclick="generate_report('<? echo $company_name; ?>','<? echo $value[job_no]; ?>','preCostRpt');">
						<? 
						$po_number=$value[po_number];
						//$po_number=implode(",", $value[po_id]);
						echo $po_number; 
						?>
                        </a>&nbsp;
                        </p>
                        </td>
						<td width="80"  style="word-break: break-all;color: <? echo $style_color ?>" align="right" >
                        <p>
                        <a style="word-break: break-all;color: <? echo $style_color ?>" href='#report_details' onclick="order_qty_popup('<? echo $company_name; ?>','<? echo $value[job_no]; ?>','<? echo $value[po_id]; ?>', '<? echo $value[buyer_name]; ?>' ,'order_qty_data');"><? echo number_format($value[order_quantity_set],0,'.',''); ?>
                        </a>
                        &nbsp;
                        </p>
                        </td>
                        
						<td width="50" align="center"  style="word-break: break-all;color: <? echo $style_color ?>"><p><? echo $unit_of_measurement[$value[order_uom]]; ?>&nbsp;</p></td>
						<td width="80" align="right"  style="word-break: break-all;color: <? echo $style_color ?>"><p><? echo number_format($value[order_quantity],0,'.',''); ?>&nbsp;</p></td>
						<td width="80" align="center"   style="word-break: break-all;color: <? echo $style_color ?>">
                        <p>
						<? 
						$pub_shipment_date= $value[pub_shipment_date];
						echo $pub_shipment_date; 
						?>
                        &nbsp;
                        </p>
                        </td>
								<td width="100" title="<? echo $value[trim_group_from][$key_trim1];  ?>" style="word-break: break-all;">
									<p>
										<? 
										echo $item_library[$value[trim_group_dtls][$key_trim1]]; 
										//echo $value[trim_group_dtls][$key_trim1];
										?>
									&nbsp;</p>
								</td>
                                <td width="100" title="<? //echo $value[trim_group_from][$key_trim1];  ?>">
									<p>
										<? 
										//echo $item_library[$value[trim_group_dtls][$key_trim1]]; 
										echo $value[remark][$key_trim1];
										?>
									&nbsp;</p>
								</td>
                                
								<td width="100">
									<p>
										<?
										echo $value[brand_sup_ref][$key_trim1];
										//echo $row[csf('brand_sup_ref')]; 
										?>
									&nbsp;</p>
								</td>
								<td width="60" align="center">
                                <p>
								<? 
								 
								if($value[apvl_req][$key_trim1]==1) echo "Yes"; else echo "&nbsp;"; 
								?>
                                &nbsp;
                                </p>
                                </td>
								<td width="80" align="center">
                                
								<? 
								if($value[apvl_req][$key_trim1]==1)
								{
									$app_status=$app_status_arr[$value[job_no]][$value[trim_group_dtls][$key_trim1]];
									$approved_status=$approval_status[$app_status];
								    $summary_array[item_app][$key_trim][all]+=1;
									if($app_status==3)
									{
										$summary_array[item_app][$key_trim][app]+=1;
									}
								}
								else
								{
								$approved_status="";	
								}
								echo $approved_status; 
								?>
                                
                               
                                </td>
							
                                <td width="100" align="right"><p>
								<? 
								$insert_date=explode(" ",$value[insert_date][$key_trim1]);
								echo change_date_format($insert_date[0],'','','');//echo change_date_format($row[csf('pre_date')],'','',1); 
								?></p></td>
								<td width="100" align="right">
                                <p>
                                <a href='#report_details' onclick="order_req_qty_popup('<? echo $company_name; ?>','<? echo $value[job_no]; ?>','<? echo $value[po_id]; ?>', '<? echo $value[buyer_name]; ?>','<? echo $value[rate][$key_trim1]; ?>','<? echo $value[trim_group_dtls][$key_trim1];?>' ,'<? echo $value[booking_no][$key_trim1] ;?>','<? echo $value[description][$key_trim1];?>','<? echo $value[country_id][$key_trim1]; ?>','<? echo $value[trim_dtla_id][$key_trim1]; ?>','<? echo $start_date ?>','<? echo $end_date ?>','order_req_qty_data');">
								<? 
								$req_qty=number_format($value[req_qnty][$key_trim1],2,'.','');
								echo $req_qty; 
								$summary_array[req_qnty][$key_trim]+=$value[req_qnty][$key_trim1];
								?>
                                </a>
                                
                                </p>
                                </td>
                                
								<td width="100" align="right">
                                <p>
                                
								<? 
								echo number_format($value[req_value][$key_trim1],2); 
								$total_pre_costing_value+=$value[req_value][$key_trim1];
								?>
                                
                                </p>
                                </td>
                                <?
							   // $conversion_factor_rate=$conversion_factor_array[$row[csf('trim_group')]]['con_factor'];
							    $wo_qnty=number_format($value[wo_qnty][$key_trim1],2,'.','');
								if($wo_qnty > $req_qty)
								{
									$color_wo="red";	
								}
								
								else if($wo_qnty < $req_qty )
								{
									$color_wo="yellow";		
								}
								
								else 
								{
								$color_wo="";	
								}
								$supplier_name_string="";
								$supplier_id_arr=array_unique(explode(',',$value[supplier_id][$key_trim1]));
								//print_r($supplier_id_arr);
								foreach($supplier_id_arr as $supplier_id_arr_key=>$supplier_id_arr_value)
								{
									$supplier_name_string.=$lib_supplier_arr[$supplier_id_arr_value].",";
								}
								
								$booking_no_arr=array_unique(explode(',',$value[booking_no][$key_trim1]));
								//$booking_no_arr_d=implode(',',$booking_no_arr);
								//print $order_id.'='.	$trim_id;// $wo_qty_array[$order_id][$trim_id]['booking_no'];
								$main_booking_no_large_data="";
								foreach($booking_no_arr as $booking_no1)
								{	
									//if($booking_no1>0)
									//{
									if($main_booking_no_large_data=="") $main_booking_no_large_data=$booking_no1; else $main_booking_no_large_data.=",".$booking_no1;
									//}
									//print($main_booking_no_large_data);
								}
								?>
								<td width="90" align="right" title="<? echo 'conversion_factor='.$value[conversion_factor_rate][$key_trim1];?>" bgcolor="<? echo $color_wo;?>"><p><a href='#report_details' onclick="openmypage('<? echo $value[po_id]; ?>','<? echo $value[trim_group_dtls][$key_trim1]; ?>','<? echo $value[job_no]; ?>','<? echo $main_booking_no_large_data;?>','<? echo $value[trim_dtla_id][$key_trim1];?>','booking_info');">
								<? 
								//$wo_qty_array[$row[csf('po_break_down_id')]][$row[csf('trim_group')]][$row[csf('trim_dtla_id')]]['wo_qnty']
								 echo number_format($value[wo_qnty][$key_trim1],2,'.','');
								 $summary_array[wo_qnty][$key_trim]+=$value[wo_qnty][$key_trim1];
								?>
                                </a></p></td>
                                <td width="60" align="center">
                                <p>
								<? 
								echo $unit_of_measurement[$value[cons_uom][$key_trim1]];
								$summary_array[cons_uom][$key_trim]=$value[cons_uom][$key_trim1];
								?></p></td>
                                <td width="100" align="right" title="<? echo number_format($value[rate][$key_trim1],2,'.',''); ?>">
                                <p>
								<?  
								echo number_format($value[amount][$key_trim1],2,'.',''); 
								$total_wo_value+=$value[amount][$key_trim1];
								?>
                                
                                </p>
                                </td>
                                
                                <td width="150" align="left">
                                <p>
								<? echo rtrim($supplier_name_string,","); ?>
                                </p>
                                </td>
                                
                                <td width="70" align="right" title="<? echo change_date_format($value[wo_date][$key_trim1]);?>"><p>
                                 <?
								 
								$tot=change_date_format($insert_date[0]);
								if($value[wo_qnty][$key_trim1]<=0 )
								{
								 $daysOnHand = datediff('d',$tot,$today);
								}
								else
								{
									$wo_date=$value[wo_date][$key_trim1];
									$wo_date=change_date_format($wo_date);
									$daysOnHand = datediff('d',$tot,$wo_date);;
								}
								 echo $daysOnHand; 
								?>&nbsp;</p>
                                </td>
                                <?
								
								$inhouse_qnty=$value[inhouse_qnty][$key_trim]-$value[receive_rtn_qty][$key_trim];
								$inhouse_rate=$value[inhouse_rate][$key_trim];
								$inhouse_value=$inhouse_qnty*$inhouse_rate;
								$balance=$value[wo_qnty_trim_group][$key_trim]-$inhouse_qnty;
								$issue_qnty=$value[issue_qty][$key_trim];
								$issue_ret_qnty=$issue_qty_data_arr[$key][issue_ret_qty][$key_trim];
								$left_overqty=$inhouse_qnty-($issue_qnty-$issue_ret_qnty);
								
								$summary_array[inhouse_qnty][$key_trim]+=$inhouse_qnty;
								$summary_array[inhouse_value][$key_trim]+=$inhouse_value;
								$summary_array[inhouse_qnty_bl][$key_trim]+=$balance;
								$summary_array[issue_qty][$key_trim]+=$issue_qnty-$issue_ret_qnty;
								$summary_array[left_overqty][$key_trim]+=$left_overqty;
								?>
                                
                                <td width="90" align="right" style=" color: <? echo $style_colory ?>" title="<? echo "Inhouse-Qty: ".$value[inhouse_qnty][$key_trim]."\nReturn Qty: ".$value[receive_rtn_qty][$key_trim]; ?>" ><a  style=" color: <? echo $style_colory ?>" href='#report_details' onclick="openmypage_inhouse('<? echo $value[po_id]; ?>','<? echo $value[trim_group_dtls][$key_trim1]; ?>','booking_inhouse_info');"><? echo number_format($inhouse_qnty,2,'.',''); ?></a></td>
                                <td width="90" align="right">
									<a href='#report_details' onclick="openmypage_inhouse('<? echo $value[po_id]; ?>','<? echo $value[trim_group_dtls][$key_trim1]; ?>','booking_inhouse_value_info');">
									<? echo number_format($inhouse_value,2,'.',''); ?>
                                    </a>
                                </td>
								<td width="90" align="right" style=" color: <? echo $style_colory ?>"><? echo number_format($balance,2,'.',''); ?></td>
								<td width="90" align="right" title="<? echo "Issue-Qty: ".$issue_qnty."\nReturn Qty: ".$issue_ret_qnty; ?>" style=" color: <? echo $style_colory ?>"><a style=" color: <? echo $style_colory ?>" href='#report_details' onclick="openmypage_issue('<? echo $value[po_id]; ?>','<? echo $value[trim_group_dtls][$key_trim1]; ?>','booking_issue_info');"><? echo number_format($issue_qnty-$issue_ret_qnty,2,'.',''); ?></a></td>
								<td align="right" style=" color: <? echo $style_colory ?>"><? echo number_format($left_overqty,2,'.',''); ?></td>
							 </tr>
							
					<?
						}// end  foreach($value[$key_trim] as $key_trim1=>$value_trim1)
					}
				$i++;
				}
				$po_data_arr=array();
				?>
                 
				</table>
				<table class="rpt_table" width="2560" cellpadding="0" cellspacing="0" border="1" rules="all">
					<tfoot>
						<th width="30"></th>
						<th width="50"></th>
						<th width="100"></th>
						<th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
						<th width="90"></th>
						<th width="80" align="right" id="total_order_qnty"><? //echo number_format($total_order_qnty,0); ?></th>
						<th width="50"></th>
						<th width="80" align="right" id="total_order_qnty_in_pcs"><? //echo number_format($total_order_qnty_in_pcs,0); ?></th>
						<th width="80"></th>
						<th width="100"></th>
                        <th width="100"></th>
						<th width="100"></th>
						<th width="60"></th>
						<th width="80"></th>
						<th width="100"></th>
						<th width="100" align="right" id="value_req_qnty"><? //echo number_format($total_req_qnty,2); ?></th>
						<th width="100" align="right" id="value_pre_costing"><? //echo number_format($total_pre_costing_value,2); ?></th>
						<th width="90" align="right" id=""><? //echo number_format($total_wo_qnty,2); ?></th>
                        <th width="60" align="right" ></th>
                        <th width="100" align="right" id="value_wo_qty"><? //echo number_format($total_wo_value,2); ?></th>
                        <th width="150" align="right" id=""></th>
                        <th width="70" align="right"><p><? //echo number_format($req_value,2,'.',''); ?>&nbsp;</p></th>
                        <th width="90" align="right" id="value_in_qty"><? //echo number_format($total_in_qnty,2); ?></th>
                        <th width="90" align="right" id="value_in_val"><? //echo number_format($total_in_qnty,2); ?></th>
                         
						<th width="90" align="right" id="value_rec_qty"><? //echo number_format($total_rec_bal_qnty,2); ?></th>
						<th width="90" align="right" id="value_issue_qty"><? //echo number_format($total_issue_qnty,2); ?></th>
						<th align="right" id="value_leftover_qty"><? //echo number_format($total_leftover_qnty,2); ?></th>
					</tfoot>
				</table>
				</div>
				<table>
					<tr><td height="17"></td></tr>
				</table>
				<u><b>Summary</b></u>
				<table class="rpt_table" width="1200" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<th width="30">SL</th>
						<th width="110">Item</th>
						<th width="60">UOM</th>
						<th width="80">Approved %</th>
						<th width="110">Req Qty</th>
						<th width="110">WO Qty</th>
						<th width="80">WO %</th>
						<th width="110">In-House Qty</th>
						<th width="80">In-House %</th>
						<th width="110">In-House Balance Qty</th>
						<th width="110">Issue Qty</th>
						<th width="80">Issue %</th>
						<th>Left Over</th>
					</thead>
					<?
					$z=1; $tot_req_qnty_summary=0;
					foreach($summary_array[trim_group] as $key_trim=>$value)
					{
						if($z%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						//$tot_req_qnty_summary+=$value['req'];
						//$tot_wo_qnty_summary+=$value['wo'];
						//$tot_in_qnty_summary+=$value['in'];
						//$tot_issue_qnty_summary+=$value['issue'];
						//$tot_leftover_qnty_summary+=$value['leftover'];
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr2_<? echo $z; ?>','<? echo $bgcolor;?>')" id="tr2_<? echo $z; ?>">
							<td width="30"><? echo $z; ?></td>
							<td width="110"><p><? echo $item_library[$key_trim]; ?></p></td>
							<td width="60" align="center">
							<? 
							echo $unit_of_measurement[$summary_array[cons_uom][$key_trim]]; 
							?></td>  
							<td width="80" align="right"><? $app_perc=($summary_array[item_app][$key_trim][app]*100)/$summary_array[item_app][$key_trim][all]; echo number_format($app_perc,2); ?>&nbsp;</td>
							<td width="110" align="right"><? echo number_format($summary_array[req_qnty][$key_trim],2); ?>&nbsp;</td>
							<td width="110" align="right"><? echo number_format( $summary_array[wo_qnty][$key_trim],2); ?>&nbsp;</td>
							<td width="80" align="right"><? $wo_per= $summary_array[wo_qnty][$key_trim]/$summary_array[req_qnty][$key_trim]*100; echo number_format($wo_per,2).'%'; ?>&nbsp;</td>
							<td width="110" align="right"><? echo number_format($summary_array[inhouse_qnty][$key_trim],2); ?>&nbsp;</td>
							<td width="80" align="right"><? $in_per=$summary_array[inhouse_qnty][$key_trim]/$summary_array[wo_qnty][$key_trim]*100; echo number_format($in_per,2).'%'; ?>&nbsp;</td>
							<td width="110" align="right"><? echo number_format($summary_array[inhouse_qnty_bl][$key_trim],2); $in_house_bal+=($value['wo']-$value['in']); ?>&nbsp;</td>
							<td width="110" align="right"><? echo number_format($summary_array[issue_qty][$key_trim],2); ?>&nbsp;</td>
							<td width="80" align="right"><? $wo_per=$summary_array[issue_qty][$key_trim]/$summary_array[wo_qnty][$key_trim]*100; echo number_format($wo_per,2).'%'; ?>&nbsp;</td>
							<td align="right"><? echo number_format($summary_array[left_overqty][$key_trim],2); ?>&nbsp;</td>
						</tr>
					<?	
					$z++;
					}
					$summary_array=array();
					?>
					<tfoot>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th align="right"><? //echo number_format($tot_req_qnty_summary,2); ?>&nbsp;</th>
						<th align="right"><? //echo number_format($tot_wo_qnty_summary,2); ?>&nbsp;</th>
						<th>&nbsp;</th>
						<th align="right"><? //echo number_format($tot_in_qnty_summary,2); ?>&nbsp;</th>
						<th>&nbsp;</th>
						<th align="right"><? //echo number_format($in_house_bal,2); ?>&nbsp;</th>
						<th align="right"><? //echo number_format($tot_issue_qnty_summary,2); ?>&nbsp;</th>
						<th>&nbsp;</th>
						<th align="right"><? //echo number_format($tot_leftover_qnty_summary,2); ?>&nbsp;</th>
					</tfoot>   	
				</table>
			</fieldset>
		</div>
	<?
	}
	}
	
	
	
	
	
	
	
//===========================================================================================================================================================

  if(str_replace("'","",$cbo_search_by)==2)
  {
	if($template==1)
	{
		
		ob_start();
	?>
		<div style="width:2400px">
		<fieldset style="width:100%;">	
			<table width="2400">
				<tr class="form_caption">
					<td colspan="26" align="center">Accessories Followup Report</td>
				</tr>
				<tr class="form_caption">
					<td colspan="26" align="center"><? echo $company_library[$company_name]; ?></td>
				</tr>
			</table>
			<table class="rpt_table" width="2360" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<th width="30">SL</th>
					<th width="50">Buyer</th>
					<th width="100">Job No</th>
					<th width="100">Style Ref</th>
                    <th width="100">Internal Ref</th>
                    <th width="100">File No</th>
					<th width="90">Order No</th>
					<th width="80">Order Qnty</th>
					<th width="50">UOM</th>
					<th width="80">Qnty (Pcs)</th>
					<th width="80">Shipment Date</th>
					<th width="100">Trims Name</th>
					<th width="100">Brand/Sup Ref</th>
					<th width="60">Appr Req.</th>
					<th width="80">Approve Status</th>
                    <th width="100">Item Entry Date</th>
					<th width="100">Req Qnty</th>
					<th width="100">Pre Costing Value</th>
					<th width="90">WO Qnty</th>
                    <th width="60">Trims UOM</th>
                    <th width="100">WO Value</th>
                    <th width="150">Supplier</th>
                    <th width="70">WO Delay Days</th>
					<th width="90">In-House Qnty</th>
					<th width="90">Receive Balance</th>
					<th width="90">Issue to Prod.</th>
					<th>Left Over/Balance</th>
				</thead>
			</table>
			<div style="width:2380px; max-height:400px; overflow-y:scroll" id="scroll_body">
				<table class="rpt_table" width="2360" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
	<?
	
	/*$wo_qty_array=array();
	$wo_qty_summary_array=array();
	if($db_type==2)
	{
	 $wo_sql="select min(a.booking_date) as booking_date ,b.job_no,LISTAGG(CAST(a.booking_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as booking_no, b.po_break_down_id, b.trim_group,b.pre_cost_fabric_cost_dtls_id, sum(b.wo_qnty) as wo_qnty,sum(b.amount) as amount,sum(b.rate) as rate from wo_booking_mst a, wo_booking_dtls b 
	where a.item_category=4 and a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name and b.job_no='FAL-14-00628' group by b.po_break_down_id,b.trim_group,b.job_no,b.pre_cost_fabric_cost_dtls_id";
	}
	else if($db_type==0)
	{
	$wo_sql="select min(a.booking_date) as booking_date ,b.job_no,group_concat(a.booking_no) as booking_no, b.po_break_down_id, b.trim_group,b.pre_cost_fabric_cost_dtls_id,sum(b.wo_qnty) as wo_qnty,sum(b.amount) as amount,sum(b.rate) as rate from wo_booking_mst a, wo_booking_dtls b 
	where a.item_category=4 and a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name group by b.po_break_down_id,b.trim_group,b.job_no,b.pre_cost_fabric_cost_dtls_id";
	}
	$dataArray=sql_select($wo_sql);
	foreach($dataArray as $row )
	{
		
		$wo_qty_array[$row[csf('po_break_down_id')]][$row[csf('trim_group')]][$row[csf('pre_cost_fabric_cost_dtls_id')]]['booking_no']=$row[csf('booking_no')];
		$wo_qty_array[$row[csf('po_break_down_id')]][$row[csf('trim_group')]][$row[csf('pre_cost_fabric_cost_dtls_id')]]['wo_qnty']=$row[csf('wo_qnty')];
		$wo_qty_array[$row[csf('po_break_down_id')]][$row[csf('trim_group')]][$row[csf('pre_cost_fabric_cost_dtls_id')]]['rate']=$row[csf('rate')];
		$wo_qty_array[$row[csf('po_break_down_id')]][$row[csf('trim_group')]][$row[csf('pre_cost_fabric_cost_dtls_id')]]['amount']=$row[csf('amount')];
		$wo_qty_array[$row[csf('po_break_down_id')]][$row[csf('trim_group')]][$row[csf('pre_cost_fabric_cost_dtls_id')]]['wo_date']=$row[csf('booking_date')];
		
		$wo_qty_summary_array[$row[csf('trim_group')]]['wo_qnty']=$row[csf('wo_qnty')];
	}*/
	
	/*$receive_qty_array=array();
	$receive_qty_data=sql_select("select b.po_breakdown_id, a.item_group_id,sum(b.quantity) as quantity   from  inv_receive_master c,product_details_master d,inv_trims_entry_dtls a , order_wise_pro_details b where a.mst_id=c.id and a.trans_id=b.trans_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, a.item_group_id");
	foreach($receive_qty_data as $row)
	{
		$receive_qty_array[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]['receive_qty']=$row[csf('quantity')];
	}
		
	$issue_qty_array=array();
	$issue_qty_data=sql_select("select b.po_breakdown_id, a.item_group_id,sum(b.quantity) as quantity  from  inv_issue_master d,product_details_master p,inv_trims_issue_dtls a , order_wise_pro_details b where a.mst_id=d.id and a.prod_id=p.id and a.trans_id=b.trans_id and b.trans_type=2 and b.entry_form=25 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, a.item_group_id");
	foreach($issue_qty_data as $row)
	{
		$issue_qty_array[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]['issue_qty']=$row[csf('quantity')];
	}*/
	
	
	$conversion_factor_array=array();
	$conversion_factor=sql_select("select id ,trim_uom,conversion_factor from  lib_item_group  ");
	foreach($conversion_factor as $row_f)
	{
	 $conversion_factor_array[$row_f[csf('id')]]['con_factor']=$row_f[csf('conversion_factor')];
	 $conversion_factor_array[$row_f[csf('id')]]['cons_uom']=$row_f[csf('trim_uom')];
	}
	$conversion_factor=array();
	$app_sql=sql_select("select job_no_mst,accessories_type_id,approval_status from wo_po_trims_approval_info");
	$app_status_arr=array();
	foreach($app_sql as $row)
	{
		$app_status_arr[$row[csf("job_no_mst")]][$row[csf("accessories_type_id")]]=$row[csf("approval_status")];
	}
	$app_sql=array();
	
	$sql_po_qty_country_wise_arr=array();
	$po_job_arr=array();
	$sql_po_qty_country_wise=sql_select("select  b.id,b.job_no_mst,c.country_id, sum(c.order_quantity/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst  and b.id=c.po_break_down_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond group by   b.id,b.job_no_mst,c.country_id order by b.id,b.job_no_mst,c.country_id");
	foreach( $sql_po_qty_country_wise as $sql_po_qty_country_wise_row)
	{
	$sql_po_qty_country_wise_arr[$sql_po_qty_country_wise_row[csf('id')]][$sql_po_qty_country_wise_row[csf('country_id')]]=$sql_po_qty_country_wise_row[csf('order_quantity_set')];
	$po_job_arr[$sql_po_qty_country_wise_row[csf('id')]]=$sql_po_qty_country_wise_row[csf('job_no_mst')];
	}
	$sql_po_qty_country_wise=array();
 
    
	$style_data_arr=array();
	$po_id_string="";
	$today=date("Y-m-d");
	/*$sql_query=sql_select("select a.buyer_name,a.job_no,a.job_no_prefix_num,style_ref_no, b.file_no,b.grouping,b.id,b.po_number,a.order_uom,sum(distinct b.po_quantity) as po_quantity,a.total_set_qnty  ,sum(distinct b.po_quantity*a.total_set_qnty) as po_quantity_psc ,sum(c.order_quantity) as order_quantity ,sum(c.order_quantity/a.total_set_qnty) as order_quantity_set,  b.pub_shipment_date,
	d.costing_per,
	e.id as trim_dtla_id,
	e.trim_group,
	e.description,
	e.brand_sup_ref,
	e.cons_uom,
	e.cons_dzn_gmts,

	e.rate,
	e.amount,
	e.apvl_req,
	e.nominated_supp,
	e.insert_date,
	f.cons,
	f.country_id,
	f.cons as cons_cal
	from 
	wo_po_details_master a, 
	wo_po_break_down b,
	wo_po_color_size_breakdown c
	left join 
	wo_pre_cost_trim_co_cons_dtls f 
	on
	c.job_no_mst=f.job_no and
	c.po_break_down_id=f.po_break_down_id and
	f.cons>0
	join
	wo_pre_cost_trim_cost_dtls e
	on 
	f.job_no=e.job_no  and
	e.id=f.wo_pre_cost_trim_cost_dtls_id 
	$item_group_cond
	join 
	wo_pre_cost_mst d
	on
	e.job_no =d.job_no
	where 
	a.job_no=b.job_no_mst   and 
	a.job_no=c.job_no_mst   and 
	b.id=c.po_break_down_id and 
	a.company_name=1        and 
	a.is_deleted=0          and 
	a.status_active=1       and 
	b.is_deleted=0          and 
	b.status_active=1       and 
	c.is_deleted=0          and 
	c.status_active=1       and 
	a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond  $ordercond  $file_no_cond $internal_ref_cond
	group by a.buyer_name,a.job_no,a.job_no_prefix_num,style_ref_no,b.file_no,b.grouping, b.id,b.po_number,a.order_uom,a.total_set_qnty,b.pub_shipment_date,d.costing_per,
	e.id,
	e.trim_group,
	e.description,
	e.brand_sup_ref,
	e.cons_uom,
	e.cons_dzn_gmts,
	e.rate,
	e.amount,
	e.apvl_req,
	e.nominated_supp,
	e.insert_date,
	f.cons,
	f.pcs,
	f.country_id
	order by b.id, e.trim_group
	");*/
	$sql_query=sql_select("select a.buyer_name,a.job_no,a.job_no_prefix_num,style_ref_no, b.file_no,b.grouping,b.id,b.po_number,a.order_uom,sum(c.order_quantity) as order_quantity ,sum(c.order_quantity/a.total_set_qnty) as order_quantity_set,  b.pub_shipment_date,
	d.costing_per,
	e.id as trim_dtla_id,
	e.trim_group,
	e.description,
	e.brand_sup_ref,
	e.cons_uom,
	e.cons_dzn_gmts,
	e.rate,
	e.amount,
	e.apvl_req,
	e.nominated_supp,
	e.insert_date,
	f.cons,
	f.country_id,
	f.cons as cons_cal
	from 
	wo_po_details_master a, 
	wo_po_break_down b,
	wo_po_color_size_breakdown c
	left join 
	wo_pre_cost_trim_co_cons_dtls f 
	on
	c.job_no_mst=f.job_no and
	c.po_break_down_id=f.po_break_down_id and
	f.cons>0
	join
	wo_pre_cost_trim_cost_dtls e
	on 
	f.job_no=e.job_no  and
	e.id=f.wo_pre_cost_trim_cost_dtls_id 
	$item_group_cond
	join 
	wo_pre_cost_mst d
	on
	e.job_no =d.job_no
	where 
	a.job_no=b.job_no_mst   and 
	a.job_no=c.job_no_mst   and 
	b.id=c.po_break_down_id and 
	a.company_name=1        and 
	a.is_deleted=0          and 
	a.status_active=1       and 
	b.is_deleted=0          and 
	b.status_active=1       and 
	c.is_deleted=0          and 
	c.status_active=1       and 
	a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond  $ordercond  $file_no_cond $internal_ref_cond
	group by a.buyer_name,a.job_no,a.job_no_prefix_num,style_ref_no,b.file_no,b.grouping, b.id,b.po_number,a.order_uom,a.total_set_qnty,b.pub_shipment_date,d.costing_per,
	e.id,
	e.trim_group,
	e.description,
	e.brand_sup_ref,
	e.cons_uom,
	e.cons_dzn_gmts,
	e.rate,
	e.amount,
	e.apvl_req,
	e.nominated_supp,
	e.insert_date,
	f.cons,
	f.pcs,
	f.country_id
	order by b.id, e.trim_group
	");//sum(distinct b.po_quantity) as po_quantity,a.total_set_qnty  ,sum(distinct b.po_quantity*a.total_set_qnty) as po_quantity_psc ,
				$tot_rows=count($sql_query);
				$i=1;
				foreach($sql_query as $row)
				{
					      
						   
						    $dzn_qnty=0;
							if($row[csf('costing_per')]==1)
							{
								$dzn_qnty=12;
							}
							else if($row[csf('costing_per')]==3)
							{
								$dzn_qnty=12*2;
							}
							else if($row[csf('costing_per')]==4)
							{
								$dzn_qnty=12*3;
							}
							else if($row[csf('costing_per')]==5)
							{
								$dzn_qnty=12*4;
							}
							else
							{
								$dzn_qnty=1;
							}
							
							
							 $po_qty=0;
							 if($row[csf('country_id')]==0)
							 {
								$po_qty=$row[csf('order_quantity')];
							 }
							 else
							 {
								$country_id= explode(",",$row[csf('country_id')]);
								for($cou=0;$cou<=count($country_id); $cou++)
								{
								$po_qty+=$sql_po_qty_country_wise_arr[$row[csf('id')]][$country_id[$cou]];
								}
							 }
							 
							 $req_qnty=($row[csf('cons_cal')]/$dzn_qnty)*$po_qty;
							 $req_value= $row[csf('rate')]*$req_qnty;
							 
							
							 
							 $style_data_arr[$row[csf('job_no')]][job_no]=$row[csf('job_no')];
							 $style_data_arr[$row[csf('job_no')]][buyer_name]=$row[csf('buyer_name')];
							 $style_data_arr[$row[csf('job_no')]][job_no_prefix_num]=$row[csf('job_no_prefix_num')];
							 $style_data_arr[$row[csf('job_no')]][style_ref_no]=$row[csf('style_ref_no')];
							 $style_data_arr[$row[csf('job_no')]][grouping]=$row[csf('grouping')];
							 $style_data_arr[$row[csf('job_no')]][file_no]=$row[csf('file_no')];
							 
							 $style_data_arr[$row[csf('job_no')]][order_uom]=$row[csf('order_uom')];
							 $style_data_arr[$row[csf('job_no')]][po_id][$row[csf('id')]]=$row[csf('id')];
							 $style_data_arr[$row[csf('job_no')]][po_number][$row[csf('id')]]=$row[csf('po_number')];
							 $style_data_arr[$row[csf('job_no')]][order_quantity_set][$row[csf('id')]]=$row[csf('order_quantity_set')];
							 $style_data_arr[$row[csf('job_no')]][order_quantity][$row[csf('id')]]=$row[csf('order_quantity')];
							 $style_data_arr[$row[csf('job_no')]][pub_shipment_date][$row[csf('id')]]=change_date_format($row[csf('pub_shipment_date')]);
							 $po_id_string.=$row[csf('id')].",";
							 
							 $style_data_arr[$row[csf('job_no')]][trim_dtla_id][$row[csf('trim_dtla_id')]]=$row[csf('trim_dtla_id')];// for rowspan
							 $style_data_arr[$row[csf('job_no')]][trim_group][$row[csf('trim_group')]]=$row[csf('trim_group')];
							 $style_data_arr[$row[csf('job_no')]][$row[csf('trim_group')]][$row[csf('trim_dtla_id')]]=$row[csf('trim_dtla_id')]; // for rowspannn
							 $style_data_arr[$row[csf('job_no')]][trim_group_dtls][$row[csf('trim_dtla_id')]]=$row[csf('trim_group')];
							 
							
							 
							 $style_data_arr[$row[csf('job_no')]][brand_sup_ref][$row[csf('trim_dtla_id')]]=$row[csf('brand_sup_ref')];
							 $style_data_arr[$row[csf('job_no')]][apvl_req][$row[csf('trim_dtla_id')]]=$row[csf('apvl_req')];
							 $style_data_arr[$row[csf('job_no')]][insert_date][$row[csf('trim_dtla_id')]]=$row[csf('insert_date')];
							 $style_data_arr[$row[csf('job_no')]][req_qnty][$row[csf('trim_dtla_id')]]+=$req_qnty;
							 $style_data_arr[$row[csf('job_no')]][req_value][$row[csf('trim_dtla_id')]]+=$req_value;
							 $style_data_arr[$row[csf('job_no')]][cons_uom][$row[csf('trim_dtla_id')]]=$row[csf('cons_uom')];
							 
							 $style_data_arr[$row[csf('job_no')]][trim_group_from][$row[csf('trim_dtla_id')]]="Pre_cost";
							 
							 
							 $style_data_arr[$row[csf('job_no')]][rate][$row[csf('trim_dtla_id')]]=$row[csf('rate')];
                             $style_data_arr[$row[csf('job_no')]][description][$row[csf('trim_dtla_id')]]=$row[csf('description')];
                             $style_data_arr[$row[csf('job_no')]][country_id][$row[csf('trim_dtla_id')]].=$row[csf('country_id')].",";

							 
							// $style_data_arr[$row[csf('job_no')]][wo_qnty][$row[csf('trim_dtla_id')]]+=$wo_qnty;
							// $style_data_arr[$row[csf('job_no')]][amount][$row[csf('trim_dtla_id')]]+=$amount;
							// $style_data_arr[$row[csf('job_no')]][wo_date][$row[csf('trim_dtla_id')]]=$wo_date;
							// $style_data_arr[$row[csf('job_no')]][wo_qnty_trim_group][$row[csf('trim_group')]]+=$wo_qnty;
				}
				
				$sql_query=array();
				$po_id_string=rtrim($po_id_string,",");
				if($po_id_string=="")
				{
				echo "<strong style='color:red;font-size:30px'>No Data found</strong>";
				die;
				}
				
				$po_id=array_chunk(array_unique(explode(",",$po_id_string)),1000, true);
				 $order_cond="";
				   $ji=0;
				   foreach($po_id as $key=> $value)
				   {
					   if($ji==0)
					   {
						$order_cond=" and b.po_break_down_id  in(".implode(",",$value).")"; 
						$order_cond1=" and b.po_breakdown_id  in(".implode(",",$value).")"; 
						$order_cond2=" and d.po_breakdown_id  in(".implode(",",$value).")"; 
					   }
					   else
					   {
						$order_cond.=" or b.po_break_down_id  in(".implode(",",$value).")";
						$order_cond1.=" or b.po_breakdown_id  in(".implode(",",$value).")";
						$order_cond2.=" or d.po_breakdown_id  in(".implode(",",$value).")";
					   }
					   $ji++;
				   }

				if($db_type==2)
				{
					//echo "select min(a.booking_date) as booking_date ,b.job_no,LISTAGG(CAST(a.booking_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as booking_no, b.po_break_down_id, b.trim_group,b.pre_cost_fabric_cost_dtls_id, sum(b.wo_qnty) as wo_qnty,sum(b.amount) as amount,sum(b.rate) as rate from wo_booking_mst a, wo_booking_dtls b where a.item_category=4 and a.booking_no=b.booking_no  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name and b.po_break_down_id in($po_id_string) group by b.po_break_down_id,b.trim_group,b.job_no,b.pre_cost_fabric_cost_dtls_id";
				  $wo_sql_without_precost=sql_select("select min(a.booking_date) as booking_date ,b.job_no,LISTAGG(CAST(a.booking_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as booking_no,LISTAGG(CAST(a.supplier_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as supplier_id, b.po_break_down_id, b.trim_group,b.pre_cost_fabric_cost_dtls_id, sum(b.wo_qnty) as wo_qnty,sum(b.amount) as amount,sum(b.rate) as rate from wo_booking_mst a, wo_booking_dtls b where a.item_category=4 and a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name $order_cond group by b.po_break_down_id,b.trim_group,b.job_no,b.pre_cost_fabric_cost_dtls_id");//and item_from_precost=2
				}
				else if($db_type==0)
				{
					//echo "select min(a.booking_date) as booking_date ,b.job_no,group_concat(a.booking_no) as booking_no, group_concat(a.supplier_id) as supplier_id, b.po_break_down_id, b.trim_group,b.pre_cost_fabric_cost_dtls_id,sum(b.wo_qnty) as wo_qnty,sum(b.amount) as amount,sum(b.rate) as rate from wo_booking_mst a, wo_booking_dtls b where a.item_category=4 and a.booking_no=b.booking_no  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name $order_cond  group by b.po_break_down_id,b.trim_group,b.job_no,b.pre_cost_fabric_cost_dtls_id";
				  $wo_sql_without_precost=sql_select("select min(a.booking_date) as booking_date ,b.job_no,group_concat(a.booking_no) as booking_no, group_concat(a.supplier_id) as supplier_id, b.po_break_down_id, b.trim_group,b.pre_cost_fabric_cost_dtls_id,sum(b.wo_qnty) as wo_qnty,sum(b.amount) as amount,sum(b.rate) as rate from wo_booking_mst a, wo_booking_dtls b where a.item_category=4 and a.booking_no=b.booking_no  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name $order_cond  group by b.po_break_down_id,b.trim_group,b.job_no,b.pre_cost_fabric_cost_dtls_id");//and item_from_precost=2
				}
				$style_data_arr1=array();
				foreach($wo_sql_without_precost as $wo_row_without_precost)
				{
					
					$conversion_factor_rate=$conversion_factor_array[$wo_row_without_precost[csf('trim_group')]]['con_factor'];
					$cons_uom=$conversion_factor_array[$wo_row_without_precost[csf('trim_group')]]['cons_uom'];
					$booking_no=$wo_row_without_precost[csf('booking_no')];
					$supplier_id=$wo_row_without_precost[csf('supplier_id')];
					$wo_qnty=$wo_row_without_precost[csf('wo_qnty')]*$conversion_factor_rate;
					$amount=$wo_row_without_precost[csf('amount')];
					$wo_date=$wo_row_without_precost[csf('booking_date')];
					
					if($wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')] =="" || $wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')] ==0)
					{
					    $trim_dtla_id=max($style_data_arr[$wo_row_without_precost[csf('job_no')]][trim_dtla_id])+1;
						$style_data_arr[$wo_row_without_precost[csf('job_no')]][trim_dtla_id][$trim_dtla_id]=$trim_dtla_id;// for rowspan
					    $style_data_arr[$wo_row_without_precost[csf('job_no')]][trim_group][$wo_row_without_precost[csf('trim_group')]]=$wo_row_without_precost[csf('trim_group')];
				        $style_data_arr[$wo_row_without_precost[csf('job_no')]][$wo_row_without_precost[csf('trim_group')]][$trim_dtla_id]=$trim_dtla_id;// for rowspannn
						$style_data_arr[$wo_row_without_precost[csf('job_no')]][trim_group_dtls][$trim_dtla_id]=$wo_row_without_precost[csf('trim_group')];
						$style_data_arr[$wo_row_without_precost[csf('job_no')]][cons_uom][$trim_dtla_id]=$cons_uom;
						
						$style_data_arr[$wo_row_without_precost[csf('job_no')]][trim_group_from][$trim_dtla_id]="Booking Without Pre_cost";
					}
					else
					{
						$trim_dtla_id=$wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')];
						
					}
					//$style_data_arr[$wo_row_without_precost[csf('job_no')]][trim_dtla_id][$trim_dtla_id]=$trim_dtla_id;// for rowspan
					//$style_data_arr[$wo_row_without_precost[csf('job_no')]][trim_group][$wo_row_without_precost[csf('trim_group')]]=$wo_row_without_precost[csf('trim_group')];
				   // $style_data_arr[$wo_row_without_precost[csf('job_no')]][$wo_row_without_precost[csf('trim_group')]][$trim_dtla_id]=$trim_dtla_id;// for rowspannn
				    //$style_data_arr[$wo_row_without_precost[csf('job_no')]][trim_group_dtls][$trim_dtla_id]=$wo_row_without_precost[csf('trim_group')];
					
					$style_data_arr[$wo_row_without_precost[csf('job_no')]][wo_qnty][$trim_dtla_id]+=$wo_qnty;
					$style_data_arr[$wo_row_without_precost[csf('job_no')]][amount][$trim_dtla_id]+=$amount;
					$style_data_arr[$wo_row_without_precost[csf('job_no')]][wo_date][$trim_dtla_id]=$wo_date;
					$style_data_arr[$wo_row_without_precost[csf('job_no')]][wo_qnty_trim_group][$wo_row_without_precost[csf('trim_group')]]+=$wo_qnty;
					
					$style_data_arr[$wo_row_without_precost[csf('job_no')]][booking_no][$trim_dtla_id].=$booking_no.",";
					$style_data_arr[$wo_row_without_precost[csf('job_no')]][supplier_id][$trim_dtla_id].=$supplier_id.",";
					$style_data_arr[$wo_row_without_precost[csf('job_no')]][conversion_factor_rate][$trim_dtla_id]=$conversion_factor_rate;

					
				
					 
				}
				$wo_sql_without_precost=array();
				
				$receive_qty_data=sql_select("select b.po_breakdown_id, a.item_group_id,sum(b.quantity) as quantity   from  inv_receive_master c,product_details_master d,inv_trims_entry_dtls a , order_wise_pro_details b where a.mst_id=c.id and a.trans_id=b.trans_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $order_cond1  group by b.po_breakdown_id, a.item_group_id order by a.item_group_id ");
				 
				foreach($receive_qty_data as $row)
				{
					if($style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][trim_group][$row[csf('item_group_id')]]=="" || $style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][trim_group][$row[csf('item_group_id')]]==0)
					{
						$cons_uom=$conversion_factor_array[$row[csf('item_group_id')]]['cons_uom'];
						$trim_dtla_id=max($style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][trim_dtla_id])+1;
						$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][trim_dtla_id][$trim_dtla_id]=$trim_dtla_id;// for rowspan
						$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][trim_group][$row[csf('item_group_id')]]=$row[csf('item_group_id')];
				        $style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][$row[csf('item_group_id')]][$trim_dtla_id]=$trim_dtla_id;// for rowspannn
						$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][trim_group_dtls][$trim_dtla_id]=$row[csf('item_group_id')];
						$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][cons_uom][$trim_dtla_id]=$cons_uom;
						
						$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][trim_group_from][$trim_dtla_id]="Trim Receive";

					}
				    $style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][inhouse_qnty][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				}
				$receive_qty_data=array();
				
				$receive_rtn_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id,sum(d.quantity) as quantity   from  inv_issue_master a,inv_transaction b, product_details_master c, order_wise_pro_details d,inv_receive_master e  where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and e.id=a.received_id   and b.transaction_type=3 and a.entry_form=49 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $order_cond2  group by d.po_breakdown_id, c.item_group_id order by c.item_group_id");
				foreach($receive_rtn_qty_data as $row)
				{
				//$style_data_arr[$row[csf('po_breakdown_id')]][receive_rtn_qty][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][receive_rtn_qty][$row[csf('item_group_id')]]+=$row[csf('quantity')];

				}
				$receive_rtn_qty_data=array();
				
				
				$issue_qty_data=sql_select("select b.po_breakdown_id, a.item_group_id, b.quantity as quantity  from  inv_issue_master d,product_details_master p,inv_trims_issue_dtls a , order_wise_pro_details b where a.mst_id=d.id and a.prod_id=p.id and a.trans_id=b.trans_id and b.trans_type=2 and b.entry_form=25 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $order_cond1");
				foreach($issue_qty_data as $row)
				{
				$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][issue_qty][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				}
				$issue_qty_data_arr=array();
				$sql_issue_ret=("select c.po_breakdown_id as po_id, p.item_group_id, c.quantity as quantity
					from   inv_transaction b, order_wise_pro_details c,product_details_master p 
					where  c.entry_form=73 and p.id=b.prod_id  and b.id=c.trans_id and b.transaction_type=4 and c.is_deleted=0 and c.status_active=1 and b.status_active=1 and b.is_deleted=0 ");					
				$issue_result=sql_select($sql_issue_ret);
				foreach($issue_result as $row)
				{
				$issue_qty_data_arr[$row[csf('po_id')]][issue_ret_qty][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				}

				$issue_qty_data=array();
				
				
				$total_pre_costing_value=0;	
				$total_wo_value=0;
				$summary_array=array();			
				$i=1;
				$x=0;
				foreach($style_data_arr as $key=>$value)
				{   
				    $z=1;
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";     
					foreach($value[trim_group] as $key_trim=>$value_trim)
					{
						$y=1;
						$summary_array[trim_group][$key_trim]=$key_trim;
						foreach($value[$key_trim] as $key_trim1=>$value_trim1)
						{ 
							if($z==1){
							
							$style_color='';
							}
							else{
							$style_color=$bgcolor."; border: none";
							}
							$z++;
							
							if($y==1){
							
							$style_colory='';
							}
							else{
							$style_colory=$bgcolor."; border: none";
							}
							$x++;
							$y++;
					?>
                    <tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $x; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $x; ?>">
						<td width="30" style="word-break: break-all;color: <? echo $style_color ?>"title="<? echo $po_qty; ?>" ><? echo $i; ?></td>
						<td width="50" style="word-break: break-all;color: <? echo $style_color ?>"><? echo $buyer_short_name_library[$value[buyer_name]]; ?></td>
						<td width="100" style="word-break: break-all;color: <? echo $style_color ?>"align="center" ><? echo $value[job_no_prefix_num]; ?></td>
						<td width="100" style="word-break: break-all;color: <? echo $style_color ?>"><? echo $value[style_ref_no]; ?></td>
                        <td width="100" style="word-break: break-all;color: <? echo $style_color ?>"><? echo $value[grouping]; ?></td>
                    	<td width="100" style="word-break: break-all;color: <? echo $style_color ?>"><? echo $value[file_no]; ?></td>
						<td width="90" style="word-break: break-all;color: <? echo $style_color ?>">
                        
                        <a style="word-break: break-all;color: <? echo $style_color ?>" href='#report_details' onclick="generate_report('<? echo $company_name; ?>','<? echo $value[job_no]; ?>','preCostRpt');">
						<? 
						$po_number=implode(",", $value[po_number]);
						$po_id=implode(",", $value[po_id]);
						echo $po_number; 
						?>
                        </a>
                        </td>
						<td width="80" style="word-break: break-all;color: <? echo $style_color ?>"align="right">
                       
                        <a style="word-break: break-all;color: <? echo $style_color ?>" href='#report_details' onclick="order_qty_popup('<? echo $company_name; ?>','<? echo $value[job_no]; ?>','<? echo $po_id; ?>', '<? echo $value[buyer_name]; ?>' ,'order_qty_data');"><? echo number_format(array_sum($value[order_quantity_set]),0,'.',''); ?>
                        </a>
                       
                        </td>
                        
						<td width="50" align="center" style="word-break: break-all;color: <? echo $style_color ?>"><? echo $unit_of_measurement[$value[order_uom]]; ?></td>
						<td width="80" align="right" style="word-break: break-all;color: <? echo $style_color ?>"><? echo number_format(array_sum($value[order_quantity]),0,'.',''); ?></td>
						<td width="80" align="center" style="word-break: break-all;color: <? echo $style_color ?>">
                        
						<? 
						$pub_shipment_date=implode(",", $value[pub_shipment_date]);
						echo $pub_shipment_date; 
						?>
                        </td>
								<td width="100" title="<? echo $value[trim_group_from][$key_trim1];  ?>">
									<p>
										<? 
										echo $item_library[$value[trim_group_dtls][$key_trim1]]; 
										//echo $value[trim_group_dtls][$key_trim1];
										?>
									&nbsp;</p>
								</td>
								<td width="100">
									<p>
										<?
										echo $value[brand_sup_ref][$key_trim1];
										//echo $row[csf('brand_sup_ref')]; 
										?>
									&nbsp;</p>
								</td>
								<td width="60" align="center">
                                <p>
								<? 
								 
								if($value[apvl_req][$key_trim1]==1) echo "Yes"; else echo "&nbsp;"; 
								?>
                                &nbsp;
                                </p>
                                </td>
								<td width="80" align="center">
                                <p>
								<? 
								if($value[apvl_req][$key_trim1]==1)
								{
									$app_status=$app_status_arr[$value[job_no]][$value[trim_group_dtls][$key_trim1]];
									$approved_status=$approval_status[$app_status];
									$summary_array[item_app][$key_trim][all]+=1;
									if($app_status==3)
									{
										$summary_array[item_app][$key_trim][app]+=1;
									}
								}
								else
								{
								$approved_status="";	
								}
								echo $approved_status; 
								?>
                                &nbsp;
                                </p>
                                </td>
							
                                <td width="100" align="right"><p>
								<? 
								$insert_date=explode(" ",$value[insert_date][$key_trim1]);
								echo change_date_format($insert_date[0],'','','');//echo change_date_format($row[csf('pre_date')],'','',1); 
								?>&nbsp;</p></td>
								<td width="100" align="right">
                                <p>
                                <a href='#report_details' onclick="order_req_qty_popup('<? echo $company_name; ?>','<? echo $value[job_no]; ?>','<? echo $po_id; ?>', '<? echo $value[buyer_name]; ?>','<? echo $value[rate][$key_trim1]; ?>','<? echo $value[trim_group_dtls][$key_trim1];?>' ,'<? echo $value[booking_no][$key_trim1];?>','<? echo $value[description][$key_trim1] ;?>','<? echo rtrim($value[country_id][$key_trim1],",");?>','<? echo $value[trim_dtla_id][$key_trim1];?>','<? echo $start_date ?>','<? echo $end_date ?>','order_req_qty_data');">
								<? 
								$req_qty=number_format($value[req_qnty][$key_trim1],2,'.','');
								echo $req_qty; 
								$summary_array[req_qnty][$key_trim]+=$value[req_qnty][$key_trim1];
								?>
                                </a>
                                &nbsp;
                                </p>
                                </td>
                                
								<td width="100" align="right">DD<p><? echo number_format($value[req_value][$key_trim1],2); $total_pre_costing_value+=$value[req_value][$key_trim1]; ?>&nbsp;</p></td>
                                <?
							   // $conversion_factor_rate=$conversion_factor_array[$row[csf('trim_group')]]['con_factor'];
							    $wo_qnty=number_format($value[wo_qnty][$key_trim1],2,'.','');
								if($wo_qnty > $req_qty)
								{
									$color_wo="red";	
								}
								
								else if($wo_qnty < $req_qty )
								{
									$color_wo="yellow";		
								}
								
								else 
								{
								$color_wo="";	
								}
								
								
								$supplier_name_string="";
								$supplier_id_arr=array_unique(explode(',',rtrim($value[supplier_id][$key_trim1],",")));
								//print_r($supplier_id_arr);
								foreach($supplier_id_arr as $supplier_id_arr_key=>$supplier_id_arr_value)
								{
									$supplier_name_string.=$lib_supplier_arr[$supplier_id_arr_value].",";
								}
								$booking_no_arr=array_unique(explode(',',rtrim($value[booking_no][$key_trim1],",")));
								//$booking_no_arr_d=implode(',',$booking_no_arr);
								//print $order_id.'='.	$trim_id;// $wo_qty_array[$order_id][$trim_id]['booking_no'];
								$main_booking_no_large_data="";
								foreach($booking_no_arr as $booking_no1)
								{	
									//if($booking_no1>0)
									//{
									if($main_booking_no_large_data=="") $main_booking_no_large_data=$booking_no1; else $main_booking_no_large_data.=",".$booking_no1;
									//}
									//print($main_booking_no_large_data);
								}
								?>
								<td width="90" align="right" title="<? echo 'conversion_factor='.$value[conversion_factor_rate][$key_trim1];?>" bgcolor="<? echo $color_wo;?>"><p><a href='#report_details' onclick="openmypage('<? echo $po_id; ?>','<? echo $value[trim_group_dtls][$key_trim1]; ?>','<? echo $value[job_no]; ?>','<? echo $main_booking_no_large_data;?>','<? echo $value[trim_dtla_id][$key_trim1];?>','booking_info');">
								<? 
								//$wo_qty_array[$row[csf('po_break_down_id')]][$row[csf('trim_group')]][$row[csf('trim_dtla_id')]]['wo_qnty']
								 echo number_format($value[wo_qnty][$key_trim1],2,'.','');
								 $summary_array[wo_qnty][$key_trim]+=$value[wo_qnty][$key_trim1];
								?>
                                </a>&nbsp;</p></td>
                                <td width="60" align="center">
                                <p>
								<? 
								echo $unit_of_measurement[$value[cons_uom][$key_trim1]]; 
								$summary_array[cons_uom][$key_trim]= $value[cons_uom][$key_trim1];
								?>
                                &nbsp;
                                </p>
                                </td>
                                <td width="100" align="right" title="<? echo number_format($value[rate][$key_trim1],2,'.',''); ?>">
                                <p>
								<?  
								echo number_format($value[amount][$key_trim1],2,'.',''); 
								$total_wo_value+=$value[amount][$key_trim1];
								?>
                                &nbsp;
                                </p>
                                </td>
                                
                                <td width="150" align="left">
                                <p>
								<?  
								echo rtrim($supplier_name_string,',');
								?>
                                
                                </p>
                                </td>
                                <td width="70" align="right" title="<? echo change_date_format($value[wo_date][$key_trim1]);?>"><p>
                                 <?
								 
								$tot=change_date_format($insert_date[0]);
								if($value[wo_qnty][$key_trim1]<=0 )
								{
								 $daysOnHand = datediff('d',$tot,$today);
								}
								else
								{
									$wo_date=$value[wo_date][$key_trim1];
									$wo_date=change_date_format($wo_date);
									$daysOnHand = datediff('d',$tot,$wo_date);;
								}
								 echo $daysOnHand; 
								?>&nbsp;</p>
                                </td>
                                <?
								$inhouse_qnty=$value[inhouse_qnty][$key_trim]-$value[receive_rtn_qty][$key_trim];
								$balance=$value[wo_qnty_trim_group][$key_trim]-$inhouse_qnty;
								$issue_qnty=$value[issue_qty][$key_trim];
								$issue_ret_qnty=$issue_qty_data_arr[issue_ret_qty][$key_trim];
								$left_overqty=$inhouse_qnty-($issue_qnty-$issue_ret_qnty);
								$summary_array[inhouse_qnty][$key_trim]+=$inhouse_qnty;
								$summary_array[inhouse_qnty_bl][$key_trim]+=$balance;
								$summary_array[issue_qty][$key_trim]+=$issue_qnty-$issue_ret_qnty;
								$summary_array[left_overqty][$key_trim]+=$left_overqty;
								?>
                                
                                <td width="90" style="word-break: break-all;color: <? echo $style_colory ?>" align="right" title="<? echo "Inhouse-Qty: ".$value[inhouse_qnty][$key_trim]."\nReturn Qty: ".$value[receive_rtn_qty][$key_trim]; ?>"><a  style="word-break: break-all;color: <? echo $style_colory ?>" href='#report_details' onclick="openmypage_inhouse('<? echo $po_id; ?>','<? echo $value[trim_group_dtls][$key_trim1]; ?>','booking_inhouse_info');"><? echo number_format($inhouse_qnty,2,'.',''); ?></a></td>
								<td width="90" style="word-break: break-all;color: <? echo $style_colory ?>" align="right" ><? echo number_format($balance,2,'.',''); ?></td>
								<td width="90" title="<? echo "Issue-Qty: ".$issue_qnty."\nReturn Qty: ".$issue_ret_qnty; ?>" style="word-break: break-all;color: <? echo $style_colory ?>" align="right" ><a  style="word-break: break-all;color: <? echo $style_colory ?>" href='#report_details' onclick="openmypage_issue('<? echo $po_id; ?>','<? echo $value[trim_group_dtls][$key_trim1]; ?>','booking_issue_info');"><? echo number_format($issue_qnty-$issue_ret_qnty,2,'.',''); ?></a></td>
								<td align="right" style="word-break: break-all;color: <? echo $style_colory ?>"><? echo number_format($left_overqty,2,'.',''); ?></td>
							 </tr>
							
					<?
						}// end  foreach($value[$key_trim] as $key_trim1=>$value_trim1)
					}
				$i++;
				}
				?>
                 
				</table>
				<table class="rpt_table" width="2360" cellpadding="0" cellspacing="0" border="1" rules="all">
					<tfoot>
						<th width="30"></th>
						<th width="50"></th>
						<th width="100"></th>
						<th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
						<th width="90"></th>
						<th width="80" align="right" id="total_order_qnty"><? //echo number_format($total_order_qnty,0); ?></th>
						<th width="50"></th>
						<th width="80" align="right" id="total_order_qnty_in_pcs"><? //echo number_format($total_order_qnty_in_pcs,0); ?></th>
						<th width="80"></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="60"></th>
						<th width="80"></th>
						<th width="100"></th>
						<th width="100" align="right" id="value_req_qnty"><? //echo number_format($total_req_qnty,2); ?></th>
						<th width="100" align="right" id="value_pre_costing"><? echo number_format($total_pre_costing_value,2); ?></th>
						<th width="90" align="right" id=""><? //echo number_format($total_wo_qnty,2); ?></th>
                        <th width="60" align="right" ></th>
                        <th width="100" align="right" id="value_wo_qty"><? echo number_format($total_wo_value,2); ?></th>
                         <th width="150" align="right" id=""></th>
                        <th width="70" align="right"><p><? //echo number_format($req_value,2,'.',''); ?>&nbsp;</p></th>
                        <th width="90" align="right" id="value_in_qty"><? //echo number_format($total_in_qnty,2); ?></th>
						<th width="90" align="right" id="value_rec_qty"><? //echo number_format($total_rec_bal_qnty,2); ?></th>
						<th width="90" align="right" id="value_issue_qty"><? //echo number_format($total_issue_qnty,2); ?></th>
						<th align="right" id="value_leftover_qty"><? //echo number_format($total_leftover_qnty,2); ?></th>
					</tfoot>
				</table>
				</div>
				<table>
					<tr><td height="15"></td></tr>
				</table>
				<u><b>Summary</b></u>
				<table class="rpt_table" width="1200" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<th width="30">SL</th>
						<th width="110">Item</th>
						<th width="60">UOM</th>
						<th width="80">Approved %</th>
						<th width="110">Req Qty</th>
						<th width="110">WO Qty</th>
						<th width="80">WO %</th>
						<th width="110">In-House Qty</th>
						<th width="80">In-House %</th>
						<th width="110">In-House Balance Qty</th>
						<th width="110">Issue Qty</th>
						<th width="80">Issue %</th>
						<th>Left Over</th>
					</thead>
					<?
					$z=1; $tot_req_qnty_summary=0;
					foreach($summary_array[trim_group] as $key_trim=>$value)
					{
						if($z%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						//$tot_req_qnty_summary+=$value['req'];
						//$tot_wo_qnty_summary+=$value['wo'];
						//$tot_in_qnty_summary+=$value['in'];
						//$tot_issue_qnty_summary+=$value['issue'];
						//$tot_leftover_qnty_summary+=$value['leftover'];
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr2_<? echo $z; ?>','<? echo $bgcolor;?>')" id="tr2_<? echo $z; ?>">
							<td width="30"><? echo $z; ?></td>
							<td width="110"><p><? echo $item_library[$key_trim]; ?></p></td>
							<td width="60" align="center">
							<? 
							echo $unit_of_measurement[$summary_array[cons_uom][$key_trim]]; 
							?></td>  
							<td width="80" align="right"><? $app_perc=($summary_array[item_app][$key_trim][app]*100)/$summary_array[item_app][$key_trim][all]; echo number_format($app_perc,2); ?>&nbsp;</td>
							<td width="110" align="right"><? echo number_format($summary_array[req_qnty][$key_trim],2); ?>&nbsp;</td>
							<td width="110" align="right"><? echo number_format( $summary_array[wo_qnty][$key_trim],2); ?>&nbsp;</td>
							<td width="80" align="right"><? $wo_per= $summary_array[wo_qnty][$key_trim]/$summary_array[req_qnty][$key_trim]*100; echo number_format($wo_per,2).'%'; ?>&nbsp;</td>
							<td width="110" align="right"><? echo number_format($summary_array[inhouse_qnty][$key_trim],2); ?>&nbsp;</td>
							<td width="80" align="right"><? $in_per=$summary_array[inhouse_qnty][$key_trim]/$summary_array[wo_qnty][$key_trim]*100; echo number_format($in_per,2).'%'; ?>&nbsp;</td>
							<td width="110" align="right"><? echo number_format($summary_array[inhouse_qnty_bl][$key_trim],2); $in_house_bal+=($value['wo']-$value['in']); ?>&nbsp;</td>
							<td width="110" align="right"><? echo number_format($summary_array[issue_qty][$key_trim],2); ?>&nbsp;</td>
							<td width="80" align="right"><? $wo_per=$summary_array[issue_qty][$key_trim]/$summary_array[wo_qnty][$key_trim]*100; echo number_format($wo_per,2).'%'; ?>&nbsp;</td>
							<td align="right"><? echo number_format($summary_array[left_overqty][$key_trim],2); ?>&nbsp;</td>
						</tr>
					<?	
					$z++;
					}
					?>
					<tfoot>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th align="right"><? //echo number_format($tot_req_qnty_summary,2); ?>&nbsp;</th>
						<th align="right"><? //echo number_format($tot_wo_qnty_summary,2); ?>&nbsp;</th>
						<th>&nbsp;</th>
						<th align="right"><? //echo number_format($tot_in_qnty_summary,2); ?>&nbsp;</th>
						<th>&nbsp;</th>
						<th align="right"><? //echo number_format($in_house_bal,2); ?>&nbsp;</th>
						<th align="right"><? //echo number_format($tot_issue_qnty_summary,2); ?>&nbsp;</th>
						<th>&nbsp;</th>
						<th align="right"><? //echo number_format($tot_leftover_qnty_summary,2); ?>&nbsp;</th>
					</tfoot>   	
				</table>
			</fieldset>
		</div>
	<?
	}
}


	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename****$tot_rows****$action";
	exit();	

}

if($action=="booking_info")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	?>
    
 <script>
	function generate_trim_report(action,txt_booking_no,cbo_company_name,id_approved_id,cbo_isshort)
	{
			var show_comment='';
			var r=confirm("Press  \"Cancel\"  to hide  comments\nPress  \"OK\"  to Show comments");
			if (r==true)
			{
				show_comment="1";
			}
			else
			{
				show_comment="0";
			}
			var data="action="+action+'&report_title=Country and Order Wise Trims Booking&show_comment='+show_comment+'&txt_booking_no='+"'"+txt_booking_no+"'"+'&cbo_company_name='+cbo_company_name+'&id_approved_id='+id_approved_id+'&cbo_isshort='+cbo_isshort+'&link=1';
			//freeze_window(5);
			http.open("POST","../../woven_order/requires/trims_booking_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_trim_report_reponse;
			
	}
	
	function generate_trim_report_reponse()
	{
		if(http.readyState == 4) 
		{
			$('#data_panel').html( http.responseText );
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
			d.close();
		}
	}
		
	 
 </script>   
    
    
    
    
	<!--<div style="width:880px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
	<fieldset style="width:870px; margin-left:3px">
		<div id="scroll_body" align="center">
        <table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
        <tr>
        <td align="center" colspan="8"><strong> WO  Summary</strong> </td>
         </tr>
        </table>
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="30">Sl</th>
                    <th width="100">Wo No</th>
                    <th width="75">Wo Date</th>
                     <th width="100">Country</th>
                     <th width="200">Item Description</th>
                    <th width="80">Wo Qty</th>
                    <th width="60">UOM</th>
                    <th width="100">Supplier</th>
				</thead>
                <tbody>
                <?
				
					
					$conversion_factor_array=array();
					$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
					$conversion_factor=sql_select("select id ,conversion_factor from  lib_item_group ");
					foreach($conversion_factor as $row_f)
					{
					$conversion_factor_array[$row_f[csf('id')]]['con_factor']=$row_f[csf('conversion_factor')];
					}
					
					$i=1;
					$country_arr_data=array();
					$sql_data=sql_select("select c.country_id,c.po_break_down_id,c.job_no_mst from wo_po_color_size_breakdown c  where c.po_break_down_id in($po_id) and c.status_active=1 and c.is_deleted=0 group by c.country_id,c.po_break_down_id,c.job_no_mst  ");
					foreach($sql_data as $row_c)
					{
					$country_arr_data[$row_c[csf('po_break_down_id')]][$row_c[csf('job_no_mst')]]['country']=$row_c[csf('country_id')];
					}
					
					
						
					$item_description_arr=array();
					$wo_sql_trim=sql_select("select b.id,b.item_color,b.job_no, b.po_break_down_id, b.description,b.brand_supplier,b.item_size from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id=b.wo_trim_booking_dtls_id and a.pre_cost_fabric_cost_dtls_id=$trim_dtla_id and a.is_deleted=0 and a.status_active=1 and a.job_no=b.job_no  group by b.id,b.po_break_down_id,b.job_no,b.description,b.brand_supplier,b.item_size,b.item_color");
					foreach($wo_sql_trim as $row_trim)
					{
					$item_description_arr[$row_trim[csf('po_break_down_id')]][$row_trim[csf('job_no')]][$trim_dtla_id]['description']=$row_trim[csf('description')];
	
					} 
					
					$boking_cond="";
					$booking_no= explode(',',$book_num);
					foreach($booking_no as $book_row)
					{
						if($boking_cond=="") $boking_cond="and a.booking_no in('$book_row'"; else  $boking_cond .=",'$book_row'";
						
					} 
					if($boking_cond!="")$boking_cond.=")";
					  $wo_sql="select max(a.is_short)as is_short,max(a.is_approved) as is_approved,a.booking_no, a.booking_date, a.supplier_id,b.job_no,b.country_id_string, b.po_break_down_id,sum(b.wo_qnty) as wo_qnty,b.uom from wo_booking_mst a, wo_booking_dtls b 
					where  a.item_category=4 and a.booking_no=b.booking_no  and a.is_deleted=0 and a.status_active=1 
					and b.status_active=1 and b.is_deleted=0 and  b.job_no='$job_no' and b.trim_group=$item_name and b.po_break_down_id in($po_id) and b.pre_cost_fabric_cost_dtls_id=$trim_dtla_id $boking_cond group by  b.po_break_down_id,b.job_no,
					a.booking_no, a.booking_date, a.supplier_id,b.uom,b.country_id_string";
					$dtlsArray=sql_select($wo_sql);
					
					
					
$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$cbo_company_name."' and module_id=2 and report_id in(5,6) and is_deleted=0 and status_active=1");
			
$report= max(explode(',',$print_report_format));

if($report==13){$reporAction="show_trim_booking_report";}
elseif($report==14){$reporAction="show_trim_booking_report1";}
elseif($report==15){$reporAction="show_trim_booking_report2";}
elseif($report==16){$reporAction="show_trim_booking_report3";}

					
					
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
							$description=$item_description_arr[$row[csf('po_break_down_id')]][$row[csf('job_no')]][$trim_dtla_id]['description'];
							$conversion_factor_rate=$conversion_factor_array[$item_name]['con_factor'];
							$country_arr_data=explode(',',$row[csf('country_id_string')]);
							$country_name_data="";
							foreach($country_arr_data as $country_row)
								{
									if($country_name_data=="") $country_name_data=$country_name_library[$country_row]; else $country_name_data.=",".$country_name_library[$country_row];
								}
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="100"><p><a href="#" onClick="generate_trim_report('<? echo $reporAction;?>','<? echo $row[csf('booking_no')]; ?>',<? echo $cbo_company_name; ?>,<? echo $row[csf('is_approved')]; ?>,<? echo $row[csf('is_short')]; ?>)"><? echo $row[csf('booking_no')]; ?></a></p></td>
                            <td width="75"><p><? echo change_date_format($row[csf('booking_date')]); ?></p></td>
                             <td width="100"><p><? echo $country_name_data; ?></p></td>
                             <td width="200"><p><?  echo $description; ?></p></td>
                            <td width="80" align="right" title="<? echo 'conversion_factor='.$conversion_factor_rate; ?>"><p><? echo number_format($row[csf('wo_qnty')]*$conversion_factor_rate,2); ?></p></td>
                            <td width="60" align="center" ><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
                            <td width="100"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></p></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('wo_qnty')]*$conversion_factor_rate;
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                   		 <td colspan="5" align="right">Total</td>
                    	<td  align="right"><? echo number_format($tot_qty,2); ?></td>
                        <td align="right">&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div style="display:none" id="data_panel"></div>
    </fieldset>
    <?
	exit();
}
disconnect($con);
?>


<?
if($action=="booking_inhouse_info")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<!--<div style="width:880px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
	<fieldset style="width:870px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="30">Sl</th>
                    <th width="80">Prod. ID</th>
                    <th width="100">Recv. ID</th>
                    <th width="100">Chalan No</th>
                    <th width="100">Recv. Date</th>
                    <th width="80">Item Description.</th>
                    <th width="80">Recv. Qty.</th>
                    <th width="80">Reject Qty.</th>
				</thead>
                <tbody>
                <?
					$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
					$i=1;
					
					$receive_rtn_data=array();
					$receive_rtn_qty_data=sql_select("select a.issue_number,a.issue_date,e.id,d.po_breakdown_id, c.item_group_id,sum(d.quantity) as quantity   from  inv_issue_master a,inv_transaction b, product_details_master c, order_wise_pro_details d,inv_receive_master e  where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and e.id=a.received_id   and b.transaction_type=3 and a.entry_form=49 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and d.po_breakdown_id in($po_id) and c.item_group_id='$item_name'  group by a.issue_number,a.issue_date,e.id,d.po_breakdown_id, c.item_group_id order by c.item_group_id");
					
					foreach($receive_rtn_qty_data as $row)
					{
					$receive_rtn_data[$row[csf('id')]][issue_number]=$row[csf('issue_number')];	
					$receive_rtn_data[$row[csf('id')]][issue_date]=$row[csf('issue_date')];	
					$receive_rtn_data[$row[csf('id')]][quantity]=$row[csf('quantity')];
					}
					
					$receive_qty_data="select a.id, c.po_breakdown_id,b.item_group_id,b.prod_id as prod_id,a.challan_no,b.item_description, a.recv_number, a.receive_date, SUM(c.quantity) as quantity, sum(reject_receive_qnty) as reject_receive_qnty
					from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c , product_details_master d
					where a.id=b.mst_id  and a.entry_form=24 and  a.item_category=4  and b.id=c.dtls_id and b.prod_id=d.id and b.trans_id=c.trans_id and c.trans_type=1 and  c.po_breakdown_id in($po_id)  and b.item_group_id='$item_name' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by  c.po_breakdown_id,b.item_group_id,b.prod_id,a.id,b.item_description, a.recv_number,a.challan_no, a.receive_date";

					$dtlsArray=sql_select($receive_qty_data);
					
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
							
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="80"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="100" align="center"><p><? echo  change_date_format($row[csf('receive_date')]); ?></p></td>
                            <td width="80" align="center"><p><? echo $row[csf('item_description')]; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                             <td width="80" align="right"><p><? echo number_format($row[csf('reject_receive_qnty')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$tot_rej_qty+=$row[csf('reject_receive_qnty')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_qty,2); ?></td>
                         <td><? echo number_format($tot_rej_qty,2); ?></td>
                    </tr>
                </tfoot>
            </table>
            
            <table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="30">Sl</th>
                    <th width="80">Prod. ID</th>
                    <th width="100">Return. ID</th>
                    <th width="100">Chalan No</th>
                    <th width="100">Return Date</th>
                    <th width="80">Item Description.</th>
                    <th width="160">Return Qty.</th>
				</thead>
                <tbody>
                <?
					$dtlsArray=sql_select($receive_qty_data);
					
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
						if($receive_rtn_data[$row[csf('id')]][quantity]>0)
						{
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="80"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $receive_rtn_data[$row[csf('id')]][issue_number]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="100" align="center"><p><? echo  change_date_format($receive_rtn_data[$row[csf('id')]][issue_date]); ?></p></td>
                            <td width="80" align="center"><p><? echo $row[csf('item_description')]; ?></p></td>
                            <td width="160" align="right"><p><? echo number_format($receive_rtn_data[$row[csf('id')]][quantity],2); ?></p></td>
                        </tr>
						<?
						$tot_rtn_qty+=$receive_rtn_data[$row[csf('id')]][quantity];
						$i++;
						}
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_rtn_qty,2); ?></td>
                    </tr>
                    <tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Balance</td>
                        <td><? echo number_format($tot_qty-$tot_rtn_qty,2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}
disconnect($con);

if($action=="booking_inhouse_value_info")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<!--<div style="width:880px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
	<fieldset style="width:870px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="30">Sl</th>
                    <th width="50">Prod. ID</th>
                    <th width="90">Recv. ID</th>
                    <th width="100">WO/PI No</th>
                    <th width="80">Recv. Date</th>
                    <th width="150">Item Description.</th>
                    <th width="80">Recv. Qty.</th>
                    <th width="80">Rate</th>
                    <th width="80">Amount</th>
                    <th>Reject Qty.</th>
				</thead>
                <tbody>
                <?
					$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
					$i=1;
					
					$receive_rtn_data=array();
					$receive_rtn_qty_data=sql_select("select a.issue_number,a.issue_date,e.id,d.po_breakdown_id, c.item_group_id,sum(d.quantity) as quantity   from  inv_issue_master a,inv_transaction b, product_details_master c, order_wise_pro_details d,inv_receive_master e  where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and e.id=a.received_id   and b.transaction_type=3 and a.entry_form=49 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and d.po_breakdown_id in($po_id) and c.item_group_id='$item_name'  group by a.issue_number,a.issue_date,e.id,d.po_breakdown_id, c.item_group_id order by c.item_group_id");
					
					foreach($receive_rtn_qty_data as $row)
					{
					$receive_rtn_data[$row[csf('id')]][issue_number]=$row[csf('issue_number')];	
					$receive_rtn_data[$row[csf('id')]][issue_date]=$row[csf('issue_date')];	
					$receive_rtn_data[$row[csf('id')]][quantity]=$row[csf('quantity')];
					}
					
					$receive_qty_data="select a.id, c.po_breakdown_id,b.item_group_id,b.prod_id as prod_id,a.challan_no,b.item_description, a.recv_number, a.receive_date, SUM(c.quantity) as quantity, sum(reject_receive_qnty) as reject_receive_qnty,b.rate,e.po_number
					from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c , product_details_master d,wo_po_break_down e
					where e.id=c.po_breakdown_id and a.id=b.mst_id  and a.entry_form=24 and  a.item_category=4  and b.id=c.dtls_id and b.prod_id=d.id and b.trans_id=c.trans_id and c.trans_type=1 and  c.po_breakdown_id in($po_id)  and b.item_group_id='$item_name' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by  c.po_breakdown_id,b.item_group_id,b.prod_id,a.id,b.item_description, a.recv_number,a.challan_no, a.receive_date,b.rate,e.po_number";

					$dtlsArray=sql_select($receive_qty_data);
					
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
							
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="50"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td width="90" align="center"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                            <td width="80" align="center"><p><? echo  change_date_format($row[csf('receive_date')]); ?></p></td>
                            <td width="150" align="center"><p><? echo $row[csf('item_description')]; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                            
                            <td width="80" align="right"><p><? echo number_format($row[csf('rate')],2); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row[csf('quantity')]*$row[csf('rate')],2); ?></p></td>
                            
                            <td align="right"><p><? echo number_format($row[csf('reject_receive_qnty')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$tot_amount+=$row[csf('quantity')]*$row[csf('rate')];
						$tot_rej_qty+=$row[csf('reject_receive_qnty')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_qty,2); ?></td>
                        <td></td>
                        <td><? echo number_format($tot_amount,2); ?></td>
                        <td><? echo number_format($tot_rej_qty,2); ?></td>
                    </tr>
                </tfoot>
            </table>
            
            <table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="30">Sl</th>
                    <th width="80">Prod. ID</th>
                    <th width="100">Return. ID</th>
                    <th width="100">Chalan No</th>
                    <th width="100">Return Date</th>
                    <th width="80">Item Description.</th>
                    <th width="160">Return Qty.</th>
				</thead>
                <tbody>
                <?
					$dtlsArray=sql_select($receive_qty_data);
					
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
						if($receive_rtn_data[$row[csf('id')]][quantity]>0)
						{
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="80"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $receive_rtn_data[$row[csf('id')]][issue_number]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="100" align="center"><p><? echo  change_date_format($receive_rtn_data[$row[csf('id')]][issue_date]); ?></p></td>
                            <td width="80" align="center"><p><? echo $row[csf('item_description')]; ?></p></td>
                            <td width="160" align="right"><p><? echo number_format($receive_rtn_data[$row[csf('id')]][quantity],2); ?></p></td>
                        </tr>
						<?
						$tot_rtn_qty+=$receive_rtn_data[$row[csf('id')]][quantity];
						$i++;
						}
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_rtn_qty,2); ?></td>
                    </tr>
                    <tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Balance</td>
                        <td><? echo number_format($tot_qty-$tot_rtn_qty,2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}
disconnect($con);


if($action=="booking_issue_info")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
<!--	<div style="width:880px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
-->	<fieldset style="width:870px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				 <caption>Issue Details </caption>
                <thead>
                    <th width="30">Sl</th>
                    <th width="80">Prod. ID</th>
                    <th width="100">Issue. ID</th>
                     <th width="100">Chalan No</th>
                     <th width="100">Issue. Date</th>
                    <th width="80">Item Description.</th>
                    <th width="100">Issue. Qty.</th>
				</thead>
                <tbody>
                <?
					$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
					$i=1;
					//$wo_sql="select a.item_group_id,a.prod_id,b.recv_number,b.receive_date,a.item_description,sum(a.cons_qnty) as cons_qnty  from inv_receive_master b, inv_trims_entry_dtls a where b.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category=4 group by a.item_group_id,a.prod_id,b.recv_number,b.receive_date,a.item_description";
					
				 $mrr_sql=("select a.id, a.issue_number,a.challan_no,b.prod_id, a.issue_date,b.item_description,SUM(c.quantity) as quantity
					from  inv_issue_master a,inv_trims_issue_dtls b, order_wise_pro_details c,product_details_master p 
					where a.id=b.mst_id  and a.entry_form=25 and p.id=b.prod_id and b.id=c.dtls_id and b.trans_id=c.trans_id and c.trans_type=2 and a.is_deleted=0 and a.status_active=1 and
					b.status_active=1 and b.is_deleted=0 and  c.po_breakdown_id in($po_id) and p.item_group_id='$item_name' group by c.po_breakdown_id,p.item_group_id,b.item_description,a.issue_number,a.id,a.issue_date,b.prod_id,a.challan_no ");					
					
					$dtlsArray=sql_select($mrr_sql);
					
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
							
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="80" align="center"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td width="100"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="100" align="center"><p><? echo  change_date_format($row[csf('issue_date')]); ?></p></td>
                            <td width="80" align="center"><p><? echo $row[csf('item_description')]; ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_qty,2); ?></td>
                    </tr>
                </tfoot>
            </table>
            <br>
            <table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
            <caption> Return Details</caption>
				<thead>
                    <th width="30">Sl</th>
                    <th width="80">Prod. ID</th>
                    <th width="100">Return. ID</th>
                     <th width="100">Chalan No</th>
                     <th width="100">Return Date</th>
                    <th width="80">Item Description.</th>
                    <th width="100">Return. Qty.</th>
				</thead>
                <tbody>
                <?
					//$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
					$k=1;$ret_tot_qty=0;
					//$wo_sql="select a.item_group_id,a.prod_id,b.recv_number,b.receive_date,a.item_description,sum(a.cons_qnty) as cons_qnty  from inv_receive_master b, inv_trims_entry_dtls a where b.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category=4 group by a.item_group_id,a.prod_id,b.recv_number,b.receive_date,a.item_description";
					
				 $mrr_sql_ret=("select a.id, a.recv_number,a.challan_no,b.prod_id, a.receive_date,p.item_description,SUM(c.quantity) as quantity
					from   inv_receive_master a,inv_transaction b, order_wise_pro_details c,product_details_master p 
					where a.id=b.mst_id  and a.entry_form=73 and c.entry_form=73 and p.id=b.prod_id  and b.id=c.trans_id and b.transaction_type=4 and a.is_deleted=0 and a.status_active=1 and
					b.status_active=1 and b.is_deleted=0 and  c.po_breakdown_id in($po_id) and p.item_group_id='$item_name' group by c.po_breakdown_id,p.item_group_id,p.item_description,a.recv_number,a.challan_no,b.prod_id, a.receive_date,a.challan_no ");					
					
					$dtlsArray_data=sql_select($mrr_sql_ret);
					
					foreach($dtlsArray_data as $row)
					{
						if ($k%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
							
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k;?>">
							<td width="30"><p><? echo $k; ?></p></td>
                            <td width="80" align="center"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td width="100"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="100" align="center"><p><? echo  change_date_format($row[csf('receive_date')]); ?></p></td>
                            <td width="80" align="center"><p><? echo $row[csf('item_description')]; ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                        </tr>
						<?
						$ret_tot_qty+=$row[csf('quantity')];
						$k++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($ret_tot_qty,2); ?></td>
                    </tr>
                    
                    <tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Total Balance</td>
                        <td><? echo number_format($tot_qty-$ret_tot_qty,2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}
disconnect($con);
?>
<?
if($action=="order_qty_data")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$country_name_library=return_library_array( "select id, country_name from lib_country", "id", "country_name"  );
	?>
<!--	<div style="width:780px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
-->	<fieldset style="width:770px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="750" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="30">Sl</th>
                    <th width="80">Buyer Name</th>
                    <th width="100">Order No</th>
                     <th width="100">Country</th>
                    <th width="80">Order Qty.</th>
                   
				</thead>
                <tbody>
                <?
					$i=1;
					  $order_arr=return_library_array( "select id, po_number from wo_po_break_down where id in($po_id)", "id", "po_number"  );
					
				 $gmt_item_id=return_field_value("item_number_id", "wo_po_color_size_breakdown", "po_break_down_id='$po_id'");
				$country_id=return_field_value("country_id", "wo_po_color_size_breakdown", "po_break_down_id='$po_id'");
					 //echo $gmt_item_id;
					 $sql_po_qty=sql_select("select sum(c.order_quantity) as order_quantity,c.country_id  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id in($po_id) and c.item_number_id=' $gmt_item_id' and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,c.country_id ");
					list($sql_po_qty_row)=$sql_po_qty;
					$po_qty=$sql_po_qty_row[csf('order_quantity')];
					
					//$fabriccostArray=sql_select("select costing_per_id, trims_cost, embel_cost, cm_cost, commission, common_oh, lab_test, inspection, freight, comm_cost from wo_pre_cost_dtls where job_no='".$job_no."' and status_active=1 and is_deleted=0");
                        
                       
					
					$sql=" select sum( c.order_quantity) as po_quantity ,c.country_id,c.po_break_down_id from wo_po_color_size_breakdown c  where c.po_break_down_id in($po_id) and c.status_active=1 and c.is_deleted=0 group by c.country_id,c.po_break_down_id";					
					
					$dtlsArray=sql_select($sql);
					
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
							
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="80" align="center"><p><? echo $buyer_short_name_library[$buyer]; ?></p></td>
                            <td width="100"><p><? echo $order_arr[$row[csf('po_break_down_id')]]; ?></p></td>
                             <td width="100" align="center"><p><? echo $country_name_library[$row[csf('country_id')]]; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row[csf('po_quantity')],2); ?></p></td>
                           
                        </tr>
						<?
						$tot_qty+=$row[csf('po_quantity')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="3" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_qty,2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}
disconnect($con);
?>
<?
if($action=="order_req_qty_data")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$country_name_library=return_library_array( "select id, country_name from lib_country", "id", "country_name"  );
	?>
<!--	<div style="width:680px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
-->	<fieldset style="width:670px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="30">Sl</th>
                    <th width="80">Buyer Name</th>
                    <th width="100">Order No</th>
                     <th width="100">Item Description</th>
                     <th width="100">Country</th>
                    <th width="80">Req. Qty.</th>
                    <th width="">Req. Rate</th>
				</thead>
                <tbody>
                <? 
				
					// $gmt_item_id=return_field_value("item_number_id", "wo_po_color_size_breakdown", "po_break_down_id='$po_id'");
					 //$country_id=return_field_value("country_id", "wo_po_color_size_breakdown", "po_break_down_id='$po_id'");
					 //$sql_po_qty=sql_select("select sum(c.order_quantity) as order_quantity,c.country_id  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='".$po_id."' and c.item_number_id=' $gmt_item_id' and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,c.country_id ");
					//list($sql_po_qty_row)=$sql_po_qty;
					//$po_qty=$sql_po_qty_row[csf('order_quantity')];
					
					
					$order_arr=return_library_array( "select id, po_number from wo_po_break_down where id in($po_id)", "id", "po_number"  );
					$req_arr=array();
					$red_data=sql_select("select a.id,a.job_no,a.cons, a.po_break_down_id  from wo_pre_cost_trim_co_cons_dtls a , wo_pre_cost_trim_cost_dtls b where b.id=a.wo_pre_cost_trim_cost_dtls_id and b.trim_group=$item_group and a.job_no='$job_no' and a.po_break_down_id in($po_id) and b.id=$trim_dtla_id");
					foreach($red_data as $row_data)
					{
					$req_arr[$row_data[csf('po_break_down_id')]][$row_data[csf('job_no')]]['cons']=$row_data[csf('cons')];
					}
					//print_r($req_arr);
					
					$wo_sql_trim=sql_select("select b.id,b.job_no, b.po_break_down_id, b.description from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id=b.wo_trim_booking_dtls_id and a.is_deleted=0 and a.status_active=1 and a.job_no=b.job_no  group by b.id,b.po_break_down_id,b.job_no,b.description ");
					foreach($wo_sql_trim as $row_trim)
					{
					$item_description_arr[$row_trim[csf('po_break_down_id')]][$row_trim[csf('job_no')]]['job_no']=$row_trim[csf('job_no')];
					$item_description_arr[$row_trim[csf('po_break_down_id')]][$row_trim[csf('job_no')]]['description']=$row_trim[csf('description')];
					}
						
				/*$fabriccostArray=sql_select("select costing_per_id, trims_cost, embel_cost, cm_cost, commission, common_oh, lab_test, inspection, freight, comm_cost from wo_pre_cost_dtls where job_no='".$job_no."' and status_active=1 and is_deleted=0");*/
                        
                       	$costing_per_id=return_field_value( "costing_per", "wo_pre_cost_mst","job_no ='$job_no'");
						if($start_date !="" && $end_date!="")
						{
						$date_cond="and c.country_ship_date between '$start_date' and '$end_date'";
						}
						else
						{
						$date_cond="";
						}

					   $dzn_qnty=0;
                        if(	$costing_per_id==1)
                        {
                            $dzn_qnty=12;
                        }
                        else if($costing_per_id==3)
                        {
                            $dzn_qnty=12*2;
                        }
                        else if($costing_per_id==4)
                        {
                            $dzn_qnty=12*3;
                        }
                        else if($costing_per_id==5)
                        {
                            $dzn_qnty=12*4;
                        }
                        else
                        {
                            $dzn_qnty=1;
                        }
						
					
					$i=1;
					
					if($country_id_string==0)
					{
						$contry_cond="";
					}
					else
					{
						$contry_cond="and c.country_id in(".$country_id_string.")";
					}
					
				 // $sql=" select  sum(c.order_quantity) as po_quantity ,c.country_id as country_id from wo_po_color_size_breakdown c  where   c.job_no_mst='$job_no' and c.po_break_down_id=$po_id $contry_cond  and c.status_active=1 and c.is_deleted=0 group by c.country_id ";
			      $sql="select  b.id,b.job_no_mst,c.country_id, sum(c.order_quantity/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst  and b.id=c.po_break_down_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  c.job_no_mst='$job_no' and c.po_break_down_id in($po_id) $contry_cond  $date_cond  group by   b.id,b.job_no_mst,c.country_id order by b.id,b.job_no_mst,c.country_id";
			 			
					$dtlsArray=sql_select($sql);						
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
							$cons=$req_arr[$row[csf('id')]][$job_no]['cons'];
							$req_qty=($row[csf('order_quantity_set')]/$dzn_qnty)*$cons;
							//$descript=$item_description_arr[$po_id][$job_no]['description'];
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="80" align="center"><p><? echo $buyer_short_name_library[$buyer]; ?></p></td>
                            <td width="100"><p><? echo $order_arr[$row[csf('id')]]; ?></p></td>
                            <td width="100"><p><? echo $description; ?></p></td>
                            <td width="100" align="center"><p><? echo  $country_name_library[$row[csf('country_id')]]; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($req_qty,2); ?></p></td>
                            <td width="" align="right"><p><? echo number_format($rate,4); ?></p></td>
                           
                        </tr>
						<?
						$tot_qty+=$req_qty;
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td  align="right"></td>
                    	<td colspan="4" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty,2); ?> </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}
disconnect($con);
?>