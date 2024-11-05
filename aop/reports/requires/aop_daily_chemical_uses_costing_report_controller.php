<? 
include('../../../includes/common.php');
session_start();
extract($_REQUEST);
$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');


$company_array= return_library_array( "SELECT id, company_name from lib_company where status_active =1 and is_deleted=0",'id','company_name');
$buyer_short_name_arr=return_library_array( "SELECT id, short_name from lib_buyer where status_active =1 and is_deleted=0",'id','short_name');
$company_short_name_arr=return_library_array( "SELECT id,company_short_name from lib_company where status_active =1 and is_deleted=0",'id','company_short_name');
$imge_arr=return_library_array( "SELECT id,master_tble_id,image_location from common_photo_library where status_active =1 and is_deleted=0",'id','image_location');
$party_arr=return_library_array( "SELECT id, buyer_name from  lib_buyer where status_active =1 and is_deleted=0",'id','buyer_name');
$buyer_arr=return_library_array( "SELECT id, buyer_name from lib_buyer where status_active =1 and is_deleted=0",'id','buyer_name');

if ($action=="load_drop_down_buyer")
{
	//echo $data; die;
	$data=explode('_',$data);
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_buyer_id", 125, "SELECT comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Party --", $data[2], "");
		exit();
	}
	else
	{
		echo create_drop_down( "cbo_buyer_id", 125, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",$data[2], "" );
		exit();
	}
}



if($action=="batch_no_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script type="text/javascript">
		function js_set_value(id)
		{ 
			document.getElementById('selected_id').value=id;
			parent.emailwindow.hide();
		}
	</script>
	<input type="hidden" id="selected_id" name="selected_id" /> 
	<?
	$buyer = str_replace("'","",$buyer_name);
	//$year = str_replace("'","",$year);
	$buyer = str_replace("'","",$buyer_name);
	$job_no = str_replace("'","",$job_no);
	$aop_ref = str_replace("'","",$aop_ref);

    /*if($db_type==0) 
	{
		$year_field="year(a.insert_date) as year"; 
		 if(trim($year)!=0) $year_field_cond="and YEAR(a.insert_date)=$year";  else $year_field_cond="";
	}
    else if($db_type==2) 
	{
		$year_field="to_char(a.insert_date,'YYYY') as year";
		 if(trim($year)!=0) $year_field_cond=" and to_char(a.insert_date,'YYYY')=$year"; else $year_field_cond="";
	}
    else 
	{
		$year_field="";
		$year_field_cond="";
	}*/
	
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num=$job_no";
	//echo $buyer;die;
	
	if(trim($buyer)==0) $sub_buyer_name_cond=""; else $sub_buyer_name_cond=" and a.party_id=$buyer";
	if($aop_ref!='') $aop_cond=" and a.aop_reference like '%$aop_ref%'"; else $aop_cond="";

	$ord_sql = "SELECT b.id,a.subcon_job,a.aop_reference,b.order_no from subcon_ord_mst a ,subcon_ord_dtls b where company_id =$company_id $aop_cond $job_no_cond  and a.entry_form=278 and  a.subcon_job=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0"; //$sub_buyer_name_cond
		$ordArray=sql_select( $ord_sql ); 
		$po_arr=array(); 
		$ref_arr=array();
		foreach ($ordArray as $row)
		{
			$po_arr[$row[csf('id')]] = $row[csf('order_no')];
			$ref_arr[$row[csf('id')]] = $row[csf('aop_reference')];
			$po_id[] .= $row[csf("id")];
		}
		$po_id_cond=" and b.po_id in (".implode(",",$po_id).") ";


		$sql = "SELECT a.id, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.dur_req_hr, a.dur_req_min, listagg(b.po_id,',') within group (order by b.po_id) as po_id from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=281 $po_id_cond group by a.id, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.dur_req_hr, a.dur_req_min order by a.id DESC"; 

		//echo $sql; die;
	
	//$sql="SELECT distinct b.id, b.job_no_mst as job_no ,a.party_id as buyer_name, b.order_no as po_number, a.job_no_prefix_num as job_prefix, $year_field from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where b.job_no_mst=a.subcon_job and a.id=c.mst_id and b.id=c.order_id and a.company_id=$company_id $sub_buyer_name_cond $job_no_cond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 group by b.id, a.party_id, b.job_no_mst, b.order_no, a.job_no_prefix_num, a.company_id, a.insert_date";	
	
	$buyer=return_library_array( "SELECT id,buyer_name from lib_buyer where status_active =1 and is_deleted=0", "id", "buyer_name"  );
	$color_arr=return_library_array( "SELECT id, color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
	?>
	<table width="500" border="1" rules="all" class="rpt_table">
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="50">Batch Id</th>
                <th width="50">Batch no</th>
                <th width="80">Color</th>
                <th width="80">Batch Weight</th>
                <th width="70">Batch Date</th>
                <th>PO No.</th>
                
            </tr>
        </thead>
    </table>
    <div style="max-height:340px; overflow:auto;">
        <table id="table_body2" width="500" border="1" rules="all" class="rpt_table">
			<? 
			$rows=sql_select($sql);
            $i=1;
            foreach($rows as $data)
            {


				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					$order_no=''; $order_ids=''; $order_ids=''; $all_ref_arr=array(); $ref_no='';
					$order_id=array_unique(explode(",",$data[csf("po_id")]));
					foreach($order_id as $val)
					{
						if($order_no=="") $order_no=$po_arr[$val]; else $order_no.=",".$po_arr[$val];
						if($order_ids=="") $order_ids=$val; else $order_ids.=", ".$val;
						$all_ref_arr[] .= $ref_arr[$val];
						//$ref_arr[$row[csf("id")]];
					}
					//echo "<pre>";
					//print_r($all_ref_arr);
					$ref_no = implode(",", array_unique($all_ref_arr));
					$ref_no = chop($ref_no,',');
					$order_no=implode(",",array_unique(explode(",",chop($order_no,','))));


				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value('<? echo $data[csf('id')].'_'.$data[csf('batch_no')]; ?>')" style="cursor:pointer;">
                    <td width="30"><? echo $i; ?></td>
                    <td width="50"><p><? echo $data[csf('id')]; ?></p></td>
                    <td width="50"><p><? echo $data[csf('batch_no')]; ?></p></td>
                    <td width="80"><p><? echo $color_arr[$data[csf('color_id')]]; ?></p></td>
                    <td width="80"><p><? echo $data[csf('batch_weight')]; ?></p></td>
                    <td width="70"><p><? echo $data[csf('batch_date')]; ?></p></td>
                    <td width=""><p><? echo $order_no; ?></p></td>
				</tr>
				<? $i++; 
			} ?>
		</table>
	</div>
	<script> setFilterGrid("table_body2",-1); </script>
	<?
	exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	//var_dump($process);
	extract(check_magic_quote_gpc( $process ));
	
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_within_group=str_replace("'","",$cbo_within_group);
	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
	$cbo_work_order_type=str_replace("'","",$cbo_work_order_type);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$txt_aop_ref=str_replace("'","",$txt_aop_ref);
	$txt_batch_no=str_replace("'","",$txt_batch_no);
	$txt_batch_id=str_replace("'","",$txt_batch_id);
	$txt_buyer_po=str_replace("'","",$txt_buyer_po);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	
	//cbo_company_id*cbo_within_group*cbo_buyer_id*txt_job_no*txt_order_no*cbo_work_order_type*txt_aop_ref*txt_batch_no*txt_batch_id*txt_buyer_po*txt_style_ref*txt_date_from*txt_date_to
	
	$query_cond='';
	if($cbo_company_id!=0) $query_cond.=" and e.company_id ='$cbo_company_id'"; 
	if($cbo_buyer_id!=0) $query_cond.=" and e.party_id ='$cbo_buyer_id'";
	if($cbo_within_group!=0) $query_cond.=" and a.within_group ='$cbo_within_group'"; 
	if($cbo_work_order_type!=0) $query_cond.=" and a.aop_work_order_type ='$cbo_work_order_type'"; 
	if($txt_job_no!="") $query_cond.=" and a.subcon_job like '%$txt_job_no%'";
	if($txt_order_no!="") $query_cond.=" and a.order_no like '%$txt_order_no%'";
	if($txt_aop_ref!="") $query_cond.=" and a.aop_reference like '%$txt_aop_ref%'";
	if($txt_batch_no!="") $query_cond.=" and c.batch_no like '%$txt_batch_no%'";
	if($txt_batch_id!=0) $query_cond.=" and c.id ='$txt_batch_id'"; 
	if($txt_buyer_po!="") $query_cond.=" and b.buyer_po_no like '%$txt_buyer_po%'";
	if($txt_style_ref!="") $query_cond.=" and b.buyer_style_ref like '%$txt_style_ref%'";

	
	if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") $date_cond=""; else $date_cond=" and e.product_date between $txt_date_from and $txt_date_to";


	if($cbo_within_group==1){
		$party_arr=return_library_array( "SELECT id, company_name from lib_company where status_active =1 and is_deleted=0",'id','company_name');
	}else{
		$party_arr=return_library_array( "SELECT id, buyer_name from lib_buyer where status_active =1 and is_deleted=0",'id','buyer_name');
	}
	
	$company_arr=return_library_array( "SELECT id, company_name from lib_company where status_active =1 and is_deleted=0",'id','company_name');
	$group_arr=return_library_array( "SELECT id,item_name from lib_item_group where  status_active=1 and is_deleted=0",'id','item_name');
	$color_arr=return_library_array("SELECT id,color_name from lib_color where status_active=1 and is_deleted=0" ,"id","color_name");
	$recipe_arr=return_library_array( "SELECT id,recipe_no from pro_recipe_entry_mst where  company_id=$cbo_company_id and entry_form=285 and  status_active=1 and is_deleted=0",'id','recipe_no');
	$buyer_arr=return_library_array( "SELECT id, buyer_name from lib_buyer where status_active =1 and is_deleted=0",'id','buyer_name');
	
	?>
    <div>
	<?
	/*$query = "SELECT a.location_id, a.issue_date, a.issue_basis, a.req_no, a.req_id, a.issue_purpose, a.company_id, a.loan_party, a.lap_dip_no, a.batch_no, a.order_id, a.sub_order_id, a.style_ref, a.store_id, a.buyer_job_no, a.is_posted_account, a.lc_company, a.floor_id, a.machine_id, a.remarks, b.id,a.issue_number,b.store_id,b.cons_uom, b.cons_quantity, b.machine_category, b.machine_id, b.prod_id, b.location_id, b.department_id, b.section_id,b.cons_rate,b.cons_amount,c.item_description, c.item_group_id, c.sub_group_name, c.item_size, d.sub_process,d.id as issue_dtls_id, d.item_category, d.dose_base, d.ratio, d.recipe_qnty, d.adjust_percent, d.adjust_type, d.required_qnty, d.req_qny_edit, d.recipe_id,e.embellishment_job,e.party_id ,e.order_no, f.buyer_po_no,f.buyer_style_ref,f.body_part,f.embl_type,f.order_quantity, e.within_group,f.buyer_buyer,f.order_uom, g.grouping 
	from inv_issue_master a, inv_transaction b, product_details_master c, dyes_chem_issue_dtls d, subcon_ord_mst e , subcon_ord_dtls f

	left join wo_po_break_down g on f.buyer_po_id=g.id and g.is_deleted=0 and g.status_active=1
	left join wo_po_details_master h on g.job_no_mst=h.job_no and h.is_deleted=0 and h.status_active=1 

	-- left join wo_po_break_down d on b.buyer_po_id=d.id and d.is_deleted=0 and d.status_active=1
	-- left join wo_po_details_master e on d.job_no_mst=e.job_no and e.is_deleted=0 and e.status_active=1 

    where a.id=d.mst_id and b.id =d.trans_id and d.product_id=c.id and a.sub_order_id=f.id and f.mst_id=e.id and a.buyer_job_no=e.order_no and e.entry_form=204  and b.transaction_type=2 and a.entry_form=250 and b.item_category in (5,6,7,23,22) and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and c.status_active =1 and c.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and e.status_active =1 and e.is_deleted =0 and f.status_active =1 and f.is_deleted =0 $date_cond $query_cond $recipe_cond $sub_process_cond order by a.id ";
	*/



	$query = "SELECT a.subcon_job, a.job_no_prefix_num, a.aop_work_order_type, a.within_group, a.currency_id, a.order_id, a.order_no,a.aop_reference,a.exchange_rate, b.id as job_dtls_id, b.buyer_po_id, b.item_color_id, b.aop_color_id, b.order_quantity, b.buyer_po_no, b.buyer_style_ref, b.buyer_buyer, c.batch_no,c.color_id, c.batch_weight, d.po_id,d.buyer_po_id, d.batch_qnty,  e.product_no,e.product_type,e.company_id,e.location_id,e.party_id,e.product_date,  f.batch_id, f.product_type, f.process, f.color_id, f.product_qnty, f.reject_qnty, f.buyer_po_id,f.remarks
	from subcon_ord_mst a, subcon_ord_dtls b, pro_batch_create_mst c, pro_batch_create_dtls d, subcon_production_mst e, subcon_production_dtls f
    where  a.id=b.mst_id and b.id=d.po_id and c.id=d.mst_id and c.id=f.batch_id and e.id=f.mst_id $date_cond $query_cond and a.aop_work_order_type in(1,2,3)  and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and c.status_active =1 and c.is_deleted =0 and e.status_active =1 and e.is_deleted =0 and f.status_active =1 and f.is_deleted =0 order by e.product_date,  f.batch_id";

	//echo $query; die;
	$sql_data_query = sql_select($query);
	$countRecords = count($query); 
	//echo $sql_data_query;
	ob_start();
	$details_data=array();
	$po_id_arr=array();
	
	foreach( $sql_data_query as $row)
	{
		$po_id_arr[] = $row[csf('job_dtls_id')];
		
		//detail data in Array  
		$details_data[$row[csf("aop_work_order_type")]][$row[csf("po_id")]][$row[csf("batch_id")]]["product_date"] =$row[csf('product_date')];
		$details_data[$row[csf("aop_work_order_type")]][$row[csf("po_id")]][$row[csf("batch_id")]]["party_id"] =$row[csf('party_id')];
		$details_data[$row[csf("aop_work_order_type")]][$row[csf("po_id")]][$row[csf("batch_id")]]["buyer_buyer"] =$row[csf('buyer_buyer')];
		$details_data[$row[csf("aop_work_order_type")]][$row[csf("po_id")]][$row[csf("batch_id")]]["buyer_style_ref"] =$row[csf('buyer_style_ref')];
		$details_data[$row[csf("aop_work_order_type")]][$row[csf("po_id")]][$row[csf("batch_id")]]["subcon_job"] =$row[csf('subcon_job')];
		$details_data[$row[csf("aop_work_order_type")]][$row[csf("po_id")]][$row[csf("batch_id")]]["order_no"] =$row[csf('order_no')];
		$details_data[$row[csf("aop_work_order_type")]][$row[csf("po_id")]][$row[csf("batch_id")]]["order_quantity"] +=$row[csf('order_quantity')];
		$details_data[$row[csf("aop_work_order_type")]][$row[csf("po_id")]][$row[csf("batch_id")]]["aop_reference"] =$row[csf('aop_reference')];
		$details_data[$row[csf("aop_work_order_type")]][$row[csf("po_id")]][$row[csf("batch_id")]]["buyer_po_no"] =$row[csf('buyer_po_no')];
		$details_data[$row[csf("aop_work_order_type")]][$row[csf("po_id")]][$row[csf("batch_id")]]["aop_color_id"] =$color_arr[$row[csf('aop_color_id')]];
		$details_data[$row[csf("aop_work_order_type")]][$row[csf("po_id")]][$row[csf("batch_id")]]["batch_no"] =$row[csf('batch_no')];
		$details_data[$row[csf("aop_work_order_type")]][$row[csf("po_id")]][$row[csf("batch_id")]]["batch_weight"] +=$row[csf('batch_weight')];
		$details_data[$row[csf("aop_work_order_type")]][$row[csf("po_id")]][$row[csf("batch_id")]]["process"] =$conversion_cost_head_array[$row[csf('process')]];
		$details_data[$row[csf("aop_work_order_type")]][$row[csf("po_id")]][$row[csf("batch_id")]]["remarks"] =$row[csf('remarks')];
		$details_data[$row[csf("aop_work_order_type")]][$row[csf("po_id")]][$row[csf("batch_id")]]["within_group"] =$row[csf('within_group')];
		$details_data[$row[csf("aop_work_order_type")]][$row[csf("po_id")]][$row[csf("batch_id")]]["aop_work_order_type"] =$row[csf('aop_work_order_type')];
		
	}
	/*echo "<pre>";
	print_r($details_data); die;*/

	$po_id_arr = array_unique($po_id_arr);

	$order_dtls_con=where_con_using_array($po_id_arr,0,"f.id");


	$sql = "SELECT a.req_no, a.batch_id, a.sub_order_id, a.req_id, b.cons_quantity,b.cons_rate,b.cons_amount,c.batch_no, d.recipe_qnty, d.required_qnty, d.req_qny_edit, d.id as issue_dtls_id, d.recipe_id, e.aop_work_order_type, e.party_id, e.within_group, f.buyer_buyer
	from inv_issue_master a, inv_transaction b, pro_batch_create_mst c, dyes_chem_issue_dtls d, subcon_ord_mst e, subcon_ord_dtls f
    where a.id=d.mst_id and b.id =d.trans_id and a.sub_order_id=f.id and f.mst_id=e.id and a.buyer_job_no=e.subcon_job and a.batch_id=c.id and e.entry_form=278  and b.transaction_type=2 and a.entry_form=308 and b.item_category in (5,6,7,23,22) and e.aop_work_order_type in(1,2,3) and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and e.status_active =1 and e.is_deleted =0 and f.status_active =1 and f.is_deleted =0 $order_dtls_con order by a.req_no ";


    //echo $sql;die;
    $sql_result = sql_select($sql);

    $issue_data=array();

    $party_wise_arr=array();
	$buyer_wise_arr=array();
	$wo_type_wise_arr=array();
	foreach( $sql_result as $row)
	{
		$party_wise_arr[$row[csf('party_id')]]["party_id"]=$party_arr[$row[csf('party_id')]];
		$party_wise_arr[$row[csf('party_id')]]["req_qny_edit"]+=$row[csf('req_qny_edit')];
		$party_wise_arr[$row[csf('party_id')]]["cons_amount"]+=$row[csf('cons_amount')];
		$party_wise_arr[$row[csf('party_id')]]["within_group"]=$row[csf('within_group')];

		$buyer_wise_arr[$row[csf('buyer_buyer')]]["buyer_buyer"]=$row[csf('buyer_buyer')];
		$buyer_wise_arr[$row[csf('buyer_buyer')]]["req_qny_edit"]+=$row[csf('req_qny_edit')];
		$buyer_wise_arr[$row[csf('buyer_buyer')]]["cons_amount"]+=$row[csf('cons_amount')];
		$buyer_wise_arr[$row[csf('buyer_buyer')]]["within_group"]=$row[csf('within_group')];

		$wo_type_wise_arr[$row[csf('aop_work_order_type')]]["aop_work_order_type"]=$row[csf('aop_work_order_type')];
		$wo_type_wise_arr[$row[csf('aop_work_order_type')]]["req_qny_edit"]+=$row[csf('req_qny_edit')];
		$wo_type_wise_arr[$row[csf('aop_work_order_type')]]["cons_amount"]+=$row[csf('cons_amount')];

		$issue_data[$row[csf("aop_work_order_type")]][$row[csf("sub_order_id")]][$row[csf("batch_id")]]["required_qnty"] +=$row[csf('required_qnty')];
		//$issue_data[$row[csf("aop_work_order_type")]][$row[csf("sub_order_id")]][$row[csf("batch_id")]]["cons_rate"] =$row[csf('cons_rate')];
		//$issue_data[$row[csf("aop_work_order_type")]][$row[csf("sub_order_id")]][$row[csf("batch_id")]]["cons_quantity"] +=$row[csf('cons_quantity')];
		$issue_data[$row[csf("aop_work_order_type")]][$row[csf("sub_order_id")]][$row[csf("batch_id")]]["recipe_id"] =$row[csf('recipe_id')];
		$issue_data[$row[csf("aop_work_order_type")]][$row[csf("sub_order_id")]][$row[csf("batch_id")]]["issue_dtls_id"] .=$row[csf('issue_dtls_id')].',';
		$issue_data[$row[csf("aop_work_order_type")]][$row[csf("sub_order_id")]][$row[csf("batch_id")]]["req_qny_edit"] +=$row[csf('req_qny_edit')];
		$issue_data[$row[csf("aop_work_order_type")]][$row[csf("sub_order_id")]][$row[csf("batch_id")]]["cons_amount"] +=$row[csf('cons_amount')];
		$issue_data[$row[csf("aop_work_order_type")]][$row[csf("sub_order_id")]][$row[csf("batch_id")]]["cons_rate"] =$row[csf('cons_amount')]/$row[csf('req_qny_edit')];
		
		
	}
	/*echo "<pre>";
	print_r($issue_data); die;*/

	?>
	<style type="text/css">
		.brk_word {
		  word-wrap: break-word;
		  word-break: break-all;
		}
	</style>
	<div style="width:2070px; margin:0 auto;">
		<fieldset style="width:100%;">	
		    <table width="98%" cellpadding="0" cellspacing="0" id="caption">
	            <tr>  
	                <td align="center" width="100%" colspan="20" class="form_caption" >
	                	<strong style="font-size:18px">AOP Batch Wise Dyes and Chemical Uses and Costing Report</strong>
	                </td>
	            </tr>
	        </table>

	        <table width="1130">
	            <tr>
	                <td valign="top" width="360">
	                    <table border="1" class="rpt_table" rules="all" width="360" cellpadding="0" cellspacing="0">
	                        <thead>
	                        	<tr>
	                            	<th colspan="3">Party Wise Total</th>
	                            </tr>
	                            <tr>
	                                <th width="120">Party Name</th>
	                                <th width="120">Issue Qty </th>
	                                <th width="">Value</th>
	                                
	                            </tr>
	                        </thead>
	                    </table>
	                    <div style="width:360px; max-height:180px; overflow-y:scroll" id="scroll_body1">
	                 		<table border="1" class="rpt_table" rules="all" width="340" cellpadding="0" cellspacing="0" id="tblpartysumm">
								<?
	                            $p=1; $total_issue=$total_value=0;
	                            foreach($party_wise_arr as $party_id=>$row)
	                            {
	                                if ($p%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	                                /*$rowOrdQty=0; $deliQty=0;
	                                $rowOrdQty=$mdata['yes']+$mdata['no'];
									$deliQty=$deliveryInhouseSumm_arr[$yearMonth];*/
	                                ?>
	                                <tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('trm_<?=$p; ?>','<?=$bgcolor;?>')" id="trm_<?=$p;?>">
	                                    <td width="120" style="word-break:break-all"><?=$party_arr[$party_id]; ?></td>
	                                    <td width="120" align="right"><?=number_format($row['req_qny_edit'],2); ?></td>
	                                    <td width="" align="right"><?=number_format($row['cons_amount'],2); ?></td>
	                                    
	                                </tr>
	                                <?
	                                $p++;
									
									$total_issue+=$row['req_qny_edit'];
									$total_value+=$row['cons_amount'];
									
	                            }
	                            ?>
	                            <tfoot>
	                                <th align="right">Total:</th>
	                                <th align="right"><?=number_format($total_issue,2); ?></th>
	                                <th align="right"><?=number_format($total_value,2); ?></th>
	                            </tfoot>
	                        </table>
	                    </div>
	                </td>
	                <td width="30">&nbsp;</td>
	                <td valign="top" width="360">
	                	<table border="1" class="rpt_table" rules="all" width="360" cellpadding="0" cellspacing="0">
	                        <thead>
	                        	<tr>
	                                <th colspan="3">Buyer Wise Total</th>
	                            </tr>
	                            <tr>
	                                <tr>
	                                <th width="120">Buyer Name</th>
	                                <th width="120">Issue Qty </th>
	                                <th width="">Value</th>
	                                
	                            </tr>
	                            </tr>
	                        </thead>
	                    </table>
	                    <div style="width:360px; max-height:180px; overflow-y:scroll" id="scroll_body2">
	                 		<table border="1" class="rpt_table" rules="all" width="340" cellpadding="0" cellspacing="0" id="tbl_buyersumm">
								<?
	                            $b=1; $total_issue=$total_value=0;
	                            foreach($buyer_wise_arr as $buyerid=>$row)
	                            {
	                                if ($b%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	                                /*$rowbOrdQty=0; $bdeliqty=0;
	                                $rowbOrdQty=$bqty;
									$bdeliqty=$deliverySubconSumm_arr[$buyerid];*/
									if ($row['within_group']==1) {
										$buyer_name=$buyer_arr[$buyerid];
									}else{
										$buyer_name=$row['buyer_buyer'];
									}
	                                ?>
	                                <tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('trb_<?=$b; ?>','<?=$bgcolor;?>')" id="trb_<?=$b;?>">
	                                    <td width="120" style="word-break:break-all"><?=$buyer_name; ?></td>
	                                    <td width="120" align="right"><?=number_format($row['req_qny_edit'],2); ?></td>
	                                    <td width="" align="right"><?=number_format($row['cons_amount'],2); ?></td>
	                                    
	                                </tr>
	                                <?
	                                $b++;
									$total_issue+=$row['req_qny_edit'];
									$total_value+=$row['cons_amount'];
	                            }
	                            ?>
	                            <tfoot>
	                                <th align="right">Total:</th>
	                                <th align="right"><?=number_format($total_issue,2); ?></th>
	                                <th align="right"><?=number_format($total_value,2); ?></th>
	                                
	                            </tfoot>
	                        </table>
	                    </div>
	                </td>
	                <td width="30">&nbsp;</td>
	                <td valign="top" width="360">
	                	<table border="1" class="rpt_table" rules="all" width="360" cellpadding="0" cellspacing="0">
	                        <thead>
	                        	<tr>
	                                <th colspan="3">Order Type Wise Total</th>
	                            </tr>
	                            <tr>
	                                <tr>
	                                <th width="120">Order Type</th>
	                                <th width="120">Issue Qty </th>
	                                <th width="">Value</th>
	                                
	                            </tr>
	                            </tr>
	                        </thead>
	                    </table>
	                    <div style="width:360px; max-height:180px; overflow-y:scroll" id="scroll_body2">
	                 		<table border="1" class="rpt_table" rules="all" width="340" cellpadding="0" cellspacing="0" id="tbl_buyersumm">
								<?
	                            $t=1; $total_issue=$total_value=0;
	                            foreach($wo_type_wise_arr as $wo_type=>$row)
	                            {
	                                if ($t%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	                                /*$rowbOrdQty=0; $bdeliqty=0;
	                                $rowbOrdQty=$bqty;
									$bdeliqty=$deliverySubconSumm_arr[$buyerid];*/
	                                ?>
	                                <tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('trb_<?=$t; ?>','<?=$bgcolor;?>')" id="trb_<?=$t;?>">
	                                    <td width="120" style="word-break:break-all"><?=$aop_work_order_type[$wo_type]; ?></td>
	                                    <td width="120" align="right"><?=number_format($row['req_qny_edit'],2); ?></td>
	                                    <td width="" align="right"><?=number_format($row['cons_amount'],2); ?></td>
	                                    
	                                </tr>
	                                <?
	                                $t++;
									$total_issue+=$row['req_qny_edit'];
									$total_value+=$row['cons_amount'];
	                            }
	                            ?>
	                            <tfoot>
	                                <th align="right">Total:</th>
	                                <th align="right"><?=number_format($total_issue,2); ?></th>
	                                <th align="right"><?=number_format($total_value,2); ?></th>
	                                
	                            </tfoot>
	                        </table>
	                    </div>
	                </td>
	            </tr>
	        </table>
	        <br />
	        <br />
	        <br />
	        <!-- <div style="width:2070px;" align="center" style="max-height:500px; overflow-y:scroll;" id="scroll_body"> -->
	        <?
			 $total_rate=$total_qty=$total_amount=0; $table_id=1;
			foreach($details_data as $wo_type=>$wo_type_data)
			{
			?>	
			<div style="width:2070px;" align="center" style="max-height:500px; overflow-y:scroll;" id="scroll_body">
				<table width="2050"  class="rpt_table" cellpadding="0" cellspacing="0" rules="all">
		            <tr>  
		                <td align="left" colspan="20" class="text" >
		                	<strong style="font-size:14px"><? echo $aop_work_order_type[$wo_type]; ?></strong>
		                </td>
		                <!-- <td align="right" colspan="18" >&nbsp;</td> -->
		            </tr>
	        	</table>

				<table width="2050"  class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1" rules="all">
					<thead>

						<th class="brk_word" width="30" align="center">Sl </th>
						<th class="brk_word" width="100" align="center">Production Date </th>
						<th class="brk_word" width="100" align="center">Party Name </th>
						<th class="brk_word" width="100" align="center">Buyer </th>
						<th class="brk_word" width="100" align="center">Buyer Style</th>
						<th class="brk_word" width="100" align="center">AOP Job Number </th>
						<th class="brk_word" width="100" align="center">AOP Order No</th>
						<th class="brk_word" width="100" align="center">Order Qty.</th>
						<th class="brk_word" width="100" align="center">AOP Ref.</th>
						<th class="brk_word" width="100" align="center">Buyer PO Number</th>
						<th class="brk_word" width="100" align="center">AOP Color</th>
						<th class="brk_word" width="100" align="center">Batch NO</th>
						<th class="brk_word" width="100" align="center">"Batch Weight</th>
						<th class="brk_word" width="120" align="center">Print Type</th>
						<th class="brk_word" width="100" align="center">Requisition Qty/Kg </th>
						<th class="brk_word" width="100" align="center"> Issue Qty/Kg </th>
						<th class="brk_word" width="100" align="center">Avg. Issue Rate/Taka</th>
						<th class="brk_word" width="100" align="center"> Issue Amount / Tk</th>
						<th class="brk_word" width="100" align="center">Recipie No </th>
						<th class="brk_word"  align="center">Remarks </th>
					</thead>
				</table>
			</div>
			<div style="width:2070px;" align="center" style="max-height:500px; overflow-y:scroll;" id="scroll_body">	
				<table width="2050"  class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body<? echo $table_id; ?>">
					<tbody>
						<?	
					$i=1;	//$total_rate=$total_qty=$total_amount=0;
					foreach($wo_type_data as $order_dlts_id=>$order_dlts_id_data)
					{
						$order_wise_rate=$order_wise_qty=$order_wise_amount=0;
						foreach($order_dlts_id_data as $batch_id=>$row)
						{								
							if ($i%2==0)  
							$bgcolor="#E9F3FF";
							else
							$bgcolor="#FFFFFF";
							
							$required_qnty=$issue_data[$wo_type][$order_dlts_id][$batch_id]["required_qnty"];
							$recipe_id=$issue_data[$wo_type][$order_dlts_id][$batch_id]["recipe_id"];
							$req_qny_edit=$issue_data[$wo_type][$order_dlts_id][$batch_id]["req_qny_edit"];
							$cons_amount=$issue_data[$wo_type][$order_dlts_id][$batch_id]["cons_amount"];
							//$cons_rate=$issue_data[$wo_type][$order_dlts_id][$batch_id]["cons_rate"];
							$issue_dtls_id=$issue_data[$wo_type][$order_dlts_id][$batch_id]["issue_dtls_id"];
							$cons_rate=$cons_amount/$req_qny_edit;

							$issue_dtls_id=chop($issue_dtls_id,',');

							if($row['within_group']==1) 
							{
								$partyarr = $company_arr[$row['party_id']];
								$buyerArr = $buyer_arr[$row['buyer_buyer']];
							}
							else
							{
								$partyarr = $buyer_arr[$row['party_id']];
								$buyerArr = $row['buyer_buyer'];
							}

							
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i;?>">
								
								<td class="brk_word" width="30" align="center"><? echo $i; ?> </td>
								<td class="brk_word" width="100" align="center"><? echo change_date_format($row['product_date']); ?></td>
								<td class="brk_word" width="100" align="center"><? echo $partyarr;  ?></td>
								<td class="brk_word" width="100" align="center"><? echo $buyerArr; ?></td>
								<td class="brk_word" width="100" align="center"><? echo $row['buyer_style_ref']; ?></td>
								<td class="brk_word" width="100" align="center"><? echo $row['subcon_job']; ?></td>
								<td class="brk_word" width="100" align="center"><? echo $row['order_no']; ?></td>
								<td class="brk_word" width="100" align="right"><? echo number_format($row['order_quantity'],2); ?></td>
								<td class="brk_word" width="100" align="center"><? echo $row['aop_reference']; ?></td>
								<td class="brk_word" width="100" align="center"><? echo $row['buyer_po_no']; ?></td>
								<td class="brk_word" width="100" align="center"><? echo $row['aop_color_id']; ?></td>
								<td class="brk_word" width="100" align="center"><? echo $row['batch_no']; ?></td>
								<td class="brk_word" width="100" align="right"><? echo number_format($row['batch_weight'],2); ?></td>
								<td class="brk_word" width="120" align="center"><? echo $row['process']; ?></td>
								<td class="brk_word" width="100" align="right"><? echo number_format($required_qnty,2); ?></td>

								<td class="brk_word" width="100" align="right"><a href="##" onClick="openmypage_qty('<? echo $issue_dtls_id; ?>','issue_qty_popup')"><? echo number_format($req_qny_edit,2); ?></a>
									</td>
								<td class="brk_word" width="100" align="right"><? echo fn_number_format($cons_rate,2); ?></td>
								<td class="brk_word" width="100" align="right"><? echo number_format($cons_amount,2); ?></td>
								<td class="brk_word" width="100" align="center"><? echo $recipe_arr[$recipe_id]; ?></td>
								<td class="brk_word" width="" align="center"><? echo $row['remarks']; ?></td>


							</tr>
							<?
							$i++;
							
							$total_qty+=$req_qny_edit;
							$total_amount+=$cons_amount;
							//$total_rate+=$cons_rate;
							$total_rate=$total_amount/$total_qty;

							$order_wise_qty+=$req_qny_edit;
							$order_wise_amount+=$cons_amount;
							//$order_wise_rate+=$cons_rate;
							$order_wise_rate=$order_wise_amount/$order_wise_qty;
							
						}
					?>
					<tr bgcolor="#ddf9ff">
						<td colspan="15" align="right"><strong>Order Total :</strong></td>
						<td align="right"><strong><? echo number_format($order_wise_qty,2);  ?></strong> </td>
						<td align="right"><strong><? echo fn_number_format($order_wise_rate,2);  ?></strong> </td>
						<td align="right"><strong><? echo number_format($order_wise_amount,2);  ?></strong> </td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
					</tr>
					<?
							
					
					}


				?>
					<!-- <tr bgcolor="#ddffdf">
						<td colspan="15" align="right"><strong>Grand Total :</strong></td>
						<td align="right"><strong><? //echo number_format($total_qty,2);  ?></strong> </td>
						<td align="right"><strong><? //echo number_format($total_rate,2);  ?></strong> </td>
						<td align="right"><strong><? //echo number_format($total_amount,2);  ?></strong> </td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
					</tr>
									
				</tbody>
				</table> -->
				<?
			$table_id++;
			}
					?>
					<tr bgcolor="#ddffdf">
						<td colspan="15" align="right"><strong>Grand Total :</strong></td>
						<td align="right"><strong><? echo number_format($total_qty,2);  ?></strong> </td>
						<td align="right"><strong><? echo fn_number_format($total_rate,2);  ?></strong> </td>
						<td align="right"><strong><? echo number_format($total_amount,2);  ?></strong> </td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
					</tr>
									
				</tbody>
				</table> 
			</div>
			
		</fieldset>
	</div>	
		<?
				//--------------------------------------------------------End----------------------------------------
		?>
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
    echo "$html**$filename**$table_id"; 
    exit();
}

if($action=="issue_qty_popup")
{
	echo load_html_head_contents("Issue Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	//$expData=explode('_',$order_id);
	//$order_id=$expData[0];
	//$process_id=$expData[1];
	?>
        <fieldset style="width:1150px">
            <div style="width:100%;" align="center">
                <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                    <thead>
                    	<tr>							  	   	  	  	  
                            <th width="30">SL</th>
                            <th width="60">Issue Date</th>
                            <th width="100">Issue No</th>
                            <th width="100">Batch No</th>
                            <th width="80">Batch Color</th>
                            <th width="100">Item Group</th>
                            <th width="150">Item Name</th>
                            <th width="70">Recipie  Qty</th>
                            <th width="100">Req No</th>
                            <th width="70">Req Qty</th>
                            <th width="70">Issue rate</th>
                            <th width="100">Issue Qty</th>
                            <th width="">Issue Value</th>
                    	</tr>
                    </thead>
                </table>
            </div>  
            <div style="width:100%; max-height:330px; overflow-y:scroll" align="left">
                <table id="table_body2" cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                    <?
                    $color_arr=return_library_array("SELECT id,color_name from lib_color where status_active=1 and is_deleted=0" ,"id","color_name");
                    $item_name_arr=return_library_array("SELECT id,item_name from lib_item_group where status_active=1 and is_deleted=0" ,"id","item_name");

					$sql_dtls = "SELECT a.location_id, a.issue_date, a.issue_basis, a.req_no, a.req_id, a.issue_purpose, a.company_id, a.loan_party, a.lap_dip_no, a.batch_no, a.order_id, a.sub_order_id, a.style_ref, a.store_id, a.buyer_job_no, a.is_posted_account, a.lc_company, a.floor_id, a.machine_id, a.remarks, b.id,a.issue_number,b.store_id,b.cons_uom, b.cons_quantity, b.machine_category, b.machine_id, b.prod_id, b.location_id, b.department_id, b.section_id,b.cons_rate,b.cons_amount,c.item_description, c.item_group_id, c.sub_group_name, c.item_size, d.sub_process,d.id as issue_dtls_id, d.item_category, d.dose_base, d.ratio, d.recipe_qnty, d.adjust_percent, d.adjust_type, d.required_qnty, d.req_qny_edit, d.recipe_id, e.batch_no, e.color_id from inv_issue_master a, inv_transaction b, product_details_master c, dyes_chem_issue_dtls d, pro_batch_create_mst e
					where a.id=d.mst_id and b.id =d.trans_id and d.product_id=c.id and a.batch_id=e.id and b.transaction_type=2 and a.entry_form=308 and b.item_category in (5,6,7,23,22) and d.id in ($issue_dtls_ids) and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and c.status_active =1 and c.is_deleted =0 and d.status_active =1 and d.is_deleted =0 order by d.sub_process ";

                   	//echo $sql;
					$sql_dtls_res= sql_select($sql_dtls); $color_array=array(); $k=1;
					$tot_recipe_qty=$tot_required_qnty=$tot_cons_amount_edit=$tot_req_qny_edit=0;
					foreach( $sql_dtls_res as $row )
                    {
                        $k++; 
                        if ($k%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";

                        ?>
                        <!-- Issue Date	Recipe No	Recipie Color	Garments Color	Item Group	Item Name	Ratio	 Recipie  Qty 	 Req No  	 Req Qty 	 Issue No 	 Issue Qty  -->

						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
							<td width="30"><? echo $i; ?></td>
                            <td width="60"><? echo change_date_format($row[csf("issue_date")]);?> </td>
                            <td width="100"><? echo $row[csf("issue_number")];?></td>
                            <td width="100"><? echo $row[csf("batch_no")];?></td>
                            <td width="80"><? echo $color_arr[$row[csf("color_id")]];?></td>
                            <td width="100"><? echo $item_name_arr[$row[csf("item_group_id")]];?></td>
                            <td width="150"><? echo $row[csf("item_description")];?></td>
                            <td width="70" align="right"><? echo number_format($row[csf("recipe_qnty")],2);?></td>
                            <td width="100"><? echo $row[csf("req_no")];?></td>
                            <td width="70" align="right"><? echo number_format($row[csf("required_qnty")],2);?></td>
                            <td width="70" align="right"><? echo number_format($row[csf("cons_rate")],2);?></td>
                            
                            <td width="100" align="right"><? echo number_format($row[csf("req_qny_edit")],2);?></td>
                            <td width="" align="right"><? echo number_format($row[csf("cons_amount")],2);?></td>
						</tr>
						<?
						$tot_recipe_qty+=$row[csf("recipe_qnty")];
						$tot_required_qnty+=$row[csf("required_qnty")];
						$tot_req_qny_edit+=$row[csf("req_qny_edit")];
						$tot_cons_amount_edit+=$row[csf("cons_amount")];
					}
					?>
                    <tr class="tbl_bottom">
                    	<td colspan="7" align="right">Total: </td>
                        <td align="right"><p><? echo number_format($tot_recipe_qty,2); ?></p></td>
                        <td>&nbsp;</td>
                        <td align="right"><p><? echo number_format($tot_required_qnty,2); ?></p></td>
                        <td>&nbsp;</td>
                        <td align="right"><p><? echo number_format($tot_req_qny_edit,2); ?></p></td>
                        <td align="right"><p><? echo number_format($tot_cons_amount_edit,2); ?></p></td>
                    </tr>
                </table>
            </div> 
            <script> setFilterGrid("table_body2",-1); </script>
	</fieldset>
 </div> 
<?
exit();
}

?>