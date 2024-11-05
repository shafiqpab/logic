<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );

if ($action == "load_drop_down_knitting_com") 
{
	$data = explode("_", $data);
	$company_id = $data[1];
	if ($data[0] == 1) {
		echo create_drop_down("cbo_knitting_company", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--Select Knit Company--", "", "", "");
	} else if ($data[0] == 3) {
		echo create_drop_down("cbo_knitting_company", 120, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select Knit Company--", 0, "");
	} else {
		echo create_drop_down("cbo_knitting_company", 120, $blank_array, "", 1, "--Select Knit Company--", 0, "");
	}
	exit();
}

if ($action=="load_drop_down_buyer")
{
	if($data!=0)
	{
		echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0); 
	}
	else
	{
		echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0  $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",0,"" );
	}
	exit();
}

if ($action=="load_drop_down_supplier")
{
	echo create_drop_down( "cbo_supplier_name", 172, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company=$data and b.party_type=25 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name","id,supplier_name", 1, "--Select Supplier--",$selected,"",0 );
	
	exit();
}

if($action=="report_generate") 
{	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	 
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_within_group=str_replace("'","",$cbo_within_group);
	$cbo_po_company=str_replace("'","",$cbo_po_company);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_year_selection=str_replace("'","",$cboYearSelection);
	$txt_sales_order_no=str_replace("'","",$txt_sales_order_no);
	$cbo_based_on=str_replace("'","",$cbo_based_on);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	
	//$po_company_name_cond $buyer_name_cond $company_name_cond $sales_order_cond $sys_date_cond
	if($cbo_po_company>0){$po_company_name_cond="and c.po_company_id=$cbo_po_company";}else{$po_company_name_cond="";}
	if($cbo_buyer_name>0){$buyer_name_cond="and c.po_buyer =$cbo_buyer_name";}else{$buyer_name_cond="";}
	if($cbo_company_name>0){$company_name_cond="and j.company_id =$cbo_company_name";}else{$company_name_cond="";}
	if($cbo_within_group>0){$within_group_cond="and j.within_group =$cbo_within_group";}else{$within_group_cond="";}
	if($txt_sales_order_no!=''){$sales_order_cond="and c.job_no like '%$txt_sales_order_no%'";}else{$sales_order_cond="";}
	//if($db_type==0) { $year_cond=" and YEAR(c.insert_date)=$cbo_year_selection";   }
	//if($db_type==2) {$year_cond=" and to_char(c.insert_date,'YYYY')=$cbo_year_selection";}

	$sys_date='';
	if($db_type==0)
	{ 
		if($cbo_year_selection !=0){
			$year_cond=" and YEAR(j.insert_date)=$cbo_year_selection";
		}
		if ($txt_date_from!="" &&  $txt_date_to!="") $sys_date_cond = "and j.sys_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'"; else $sys_date_cond ="";
	}
	else
	{
		if($cbo_year_selection !=0){
			$year_cond=" and to_char(j.insert_date,'YYYY')=$cbo_year_selection";
		}
		
		if ($txt_date_from!="" &&  $txt_date_to!=""){
			$sys_date_cond = "and j.sys_date between '".change_date_format($txt_date_from, "", "",1)."' and '".change_date_format($txt_date_to, "", "",1)."'";
			//$sys_date = "'".change_date_format($txt_date_from, "", "",1)."' To '".change_date_format($txt_date_to, "", "",1)."'"; 
			$sys_date = change_date_format($txt_date_from, "", "",1) .'To'. change_date_format($txt_date_to, "", "",1); 
		} 
	}
	$com_dtls = fnc_company_location_address($cbo_company_name, "", 1);


	/*if($db_type==0)
	{ 
		$year_cond=" and YEAR(a.insert_date)=$cbo_year_selection";
		if ($txt_date_from!="" &&  $txt_date_to!="")
		{
			if($cbo_based_on==1)
			{
				//$sys_date_cond = "and h.sys_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'"; else $sys_date_cond ="";
				$sys_date_cond = "and h.sys_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'"; else $sys_date_cond ="";
			}
		} 
		
	}
	else
	{
		$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year_selection";
		if ($txt_date_from!="" &&  $txt_date_to!="")
		{
			if($cbo_based_on==1)
			{
				$sys_date_cond = "and h.sys_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $sys_date_cond ="";
			}
		} 
	}*/

	/*if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and a.receive_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $order_rcv_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and a.receive_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $order_rcv_date ="";
	}*/
	
	ob_start();
	// php start
	?>
    	<style type="text/css">
            .block_div { 
				width:auto;
				height:auto;
				text-wrap:normal;
				vertical-align:bottom;
				display: block;
				position: !important; 
				-webkit-transform: rotate(-90deg);
				-moz-transform: rotate(-90deg);	
            }
    	</style>
	<?
	// php end
	
	if($report_format==1) // Booking Wise Button
	{
		?>
		<fieldset style="width:3210px;">
			<table width="3100">
				<tr>
					<td align="center" width="100%" colspan="29" class="form_caption"><? echo $company_library[str_replace("'","",$cbo_company_name)]; ?></td>
				</tr>
				<tr>
				   <td align="center" width="100%" colspan="29" class="form_caption" style="font-size:12px;"><? echo   $com_dtls[1]; ?></td>
				</tr>
				<tr>
					<td align="center" width="100%" colspan="29" class="form_caption" style="font-size:18px;">FSO Wise AOP Send and Receive Report</td>
				</tr>
				<tr>
					<td align="center" width="100%" colspan="29" class="form_caption" style="font-size:14px;"><? echo $sys_date; ?></td>
				</tr>
			</table>
			<?
				$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
				$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
				$style_library = return_library_array("select job_no, style_ref_no from wo_po_details_master", "job_no", "style_ref_no");
				$construction_arr = return_library_array("select id, construction from lib_yarn_count_determina_mst", "id", "construction");
				
				/*if($cbo_within_group==1){*/
					//and b.company_id in($cbo_company_name) 
					$booking_type_arr = array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");
					$batch_sql="select a.id, a.batch_no, a.batch_date, a.booking_no, b.is_short, b.short_booking_type,0 as booking_without_order,b.booking_type,b.entry_form  from pro_batch_create_mst a, wo_booking_mst b where a.booking_no=b.booking_no and b.booking_type=1 and a.status_active=1 and a.is_deleted=0 
					union all 
					select a.id, a.batch_no, a.batch_date, a.booking_no, b.is_short,null as short_booking_type,1 as booking_without_order,b.booking_type,b.entry_form_id as entry_form  
					from pro_batch_create_mst a, wo_non_ord_samp_booking_mst b 
					where a.booking_no=b.booking_no and b.booking_type=4 and a.status_active=1 and a.is_deleted=0";

					$batch_result=sql_select($batch_sql);
					$batch_data=array();
					$bookingNos="";$booking_typeArr=array();
					foreach($batch_result as $row)
					{
						if($row[csf("booking_without_order")]==1)
						{
							$bookingNos.="'".$row[csf("booking_no")]."',";
						}
						$batch_data[$row[csf("id")]]["batch_no"]=$row[csf("batch_no")];
						$batch_data[$row[csf("id")]]["batch_date"]=$row[csf("batch_date")];
						$batch_data[$row[csf("id")]]["booking_no"]=$row[csf("booking_no")];
						$batch_data[$row[csf("id")]]["is_short"]=$row[csf("is_short")];
						$batch_data[$row[csf("id")]]["short_booking_type"]=$row[csf("short_booking_type")];
						$batch_data[$row[csf("id")]]["booking_without_order"]=$row[csf("booking_without_order")];
						$batch_data[$row[csf("id")]]["booking_type"]=$row[csf("booking_type")];

						$bookingType="";
						if($row[csf('booking_type')] == 4 && $row[csf("booking_without_order")]==0)
						{
							$booking_typeArr[$row[csf("booking_no")]]="Sample Without Order";
						} else if ($row[csf("booking_without_order")]==1) {
							$booking_typeArr[$row[csf("booking_no")]]="Sample Without Order";
						} else {
							$booking_typeArr[$row[csf("booking_no")]]=$booking_type_arr[$row[csf("entry_form")]];
						}
					}
				/*}*/

				/*$booking_sql="select entry_form,booking_no from wo_booking_mst where is_deleted=0 and status_active=1 ";
				$booking_sql_res=sql_select($booking_sql);
				foreach ($booking_sql_res as $row) 
				{
					$entry_form_arr[$row[csf("booking_no")]]["entry_form"] =$row[csf("entry_form")];
				}

				$sample_booking_sql="select entry_form,booking_no from wo_non_ord_samp_booking_mst where is_deleted=0 and status_active=1 ";
				$sample_booking_sql_res=sql_select($sample_booking_sql);
				foreach ($sample_booking_sql_res as $row) 
				{
					$entry_form_arr[$row[csf("booking_no")]]["entry_form"] =$row[csf("entry_form")];
				}*/
				//echo "<pre>";
				//print_r($entry_form_arr);

				/*$sql= "select  f.company_id as po_company,f.id,h.company_id, f.buyer_id,a.job_no,c.style_ref_no,c.job_no as fso_number, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no,b.determination_id, b.color_id, b.body_part_id , b.fabric_desc, b.gsm_weight, b.dia,d.width_dia_type,b.cons_uom, e.batch_weight,b.id as so_dtls_id,e.id as batch_id ,c.season,f.entry_form,h.supplier_id, listagg(cast(g.mst_id as varchar(4000)),',') within group(order by g.mst_id) as issue_ids from 
				wo_pre_cost_fabric_cost_dtls a, fabric_sales_order_dtls b, fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e, wo_booking_mst f, wo_fabric_aop_dtls g, wo_fabric_aop_mst h where h.id=g.mst_id and f.buyer_id=h.buyer_id and a.id=b.pre_cost_fabric_cost_dtls_id and b.mst_id=c.id and c.id=d.po_id and d.mst_id=e.id and f.tagged_booking_no = c.sales_booking_no and g.sales_order_dtls_id=b.id and f.id=g.order_id and g.batch_id=e.id and e.color_id=b.color_id and f.booking_type=3 and f.process=35 and h.entry_form=462 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and g.status_active=1 and g.is_deleted=0 $po_company_name_cond $buyer_name_cond $company_name_cond $sales_order_cond $year_cond $sys_date_cond group by f.company_id,f.id,h.company_id, f.buyer_id,a.job_no,c.style_ref_no,c.job_no, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no,b.determination_id, b.color_id, b.body_part_id , b.fabric_desc, b.gsm_weight, b.dia ,d.width_dia_type, b.cons_uom, e.batch_weight,b.id,e.id ,c.season,f.entry_form,h.supplier_id order by f.id desc";
*/
				/*$sql= "select  c.company_id as po_company,c.id,h.company_id, h.buyer_id,c.style_ref_no,c.job_no as fso_number, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no,b.determination_id, b.color_id, b.body_part_id , b.fabric_desc, b.gsm_weight, b.dia,d.width_dia_type,b.cons_uom, e.batch_weight,b.id as so_dtls_id,e.id as batch_id ,c.season,h.supplier_id from 
				 fabric_sales_order_dtls b, fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e, wo_fabric_aop_dtls g, wo_fabric_aop_mst h where h.id=g.mst_id  and b.mst_id=c.id and c.id=d.po_id and d.mst_id=e.id and g.sales_order_dtls_id=b.id and g.batch_id=e.id and e.color_id=b.color_id and h.entry_form=462 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and g.status_active=1 and g.is_deleted=0 $po_company_name_cond $buyer_name_cond $company_name_cond $within_group_cond $sales_order_cond $year_cond $sys_date_cond group by c.company_id,c.id,h.company_id, h.buyer_id,c.style_ref_no,c.job_no, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no,b.determination_id, b.color_id, b.body_part_id , b.fabric_desc, b.gsm_weight, b.dia ,d.width_dia_type, b.cons_uom, e.batch_weight,b.id,e.id ,c.season,h.supplier_id order by c.id desc";*/



				/*$sql= "select c.id,c.company_id  as po_company, c.buyer_id,c.style_ref_no,c.job_no as fso_number, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, b.color_id, b.body_part_id , b.fabric_desc, b.gsm_weight, b.dia ,b.id as so_dtls_id,e.id as batch_id,g.id as dtls_id, g.process_type_id, g.quantity, g.rate, g.amount, g.number_of_roll, d.batch_qnty as batch_weight, g.remark  from 
				fabric_sales_order_dtls b, fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e, wo_fabric_aop_dtls g ,product_details_master  h where b.mst_id=c.id and c.id=d.po_id and d.mst_id=e.id and g.sales_order_dtls_id=b.id and g.batch_id=e.id and e.color_id=b.color_id and d.prod_id=h.id and g.mst_id=$mst_id and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and g.status_active=1 and g.is_deleted=0 group by  c.id,c.company_id, c.buyer_id,c.style_ref_no,c.job_no, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, b.color_id, b.body_part_id , b.fabric_desc, b.gsm_weight, b.dia ,b.id,e.id,g.id, g.process_type_id, g.quantity, g.rate, g.amount, g.number_of_roll, g.remark, d.batch_qnty order by b.id desc";*/

				

				/*$batch_sql="select a.id, a.batch_no, a.batch_date, a.booking_no, b.is_short, b.short_booking_type,0 as booking_without_order,b.booking_type,b.entry_form  from pro_batch_create_mst a, wo_booking_mst b where a.booking_no=b.booking_no and b.booking_type=1 and a.status_active=1 and a.is_deleted=0 and b.company_id in($cbo_company_id) 
				union all 
				select a.id, a.batch_no, a.batch_date, a.booking_no, b.is_short,null as short_booking_type,1 as booking_without_order,b.booking_type,b.entry_form_id as entry_form  
				from pro_batch_create_mst a, wo_non_ord_samp_booking_mst b 
				where a.booking_no=b.booking_no and b.booking_type=4 and a.status_active=1 and a.is_deleted=0 and b.company_id in($cbo_company_id) ";

				$batch_result=sql_select($batch_sql);
				$batch_data=array();
				$bookingNos="";$booking_typeArr=array();
				foreach($batch_result as $row)
				{
					$bookingType="";
					if($row[csf('booking_type')] == 4 && $row[csf("booking_without_order")]==0)
					{
						$booking_typeArr[$row[csf("booking_no")]]="Sample Without Order";
					}
					else if ($row[csf("booking_without_order")]==1) {
						$booking_typeArr[$row[csf("booking_no")]]="Sample Without Order";
					}
					else {
						$booking_typeArr[$row[csf("booking_no")]]=$booking_type_arr[$row[csf("entry_form")]];
					}
				}*/

				//$booking_type_arr = array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");

			$sql= "SELECT c.id, c.po_company_id  as po_company, c.buyer_id, c.po_buyer, c.style_ref_no, c.job_no AS fso_number, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, e.color_id, h.item_description AS fabric_desc, h.gsm AS gsm_weight, h.dia_width AS dia, e.id AS batch_id, g.id as issue_dtls_id, g.batch_dtls_id, g.process_type_id, g.quantity, g.rate, g.amount, g.number_of_roll, g.remark,h.id as prod_id, j.sys_no, j.sys_date ,j.order_no,j.id as issue_id,g.mst_id,j.supplier_id ,j.company_id,c.season,h.detarmination_id ,d.width_dia_type, h.unit_of_measure as cons_uom,j.entry_form
				FROM  fabric_sales_order_mst c, pro_batch_create_dtls d, pro_batch_create_mst e, wo_fabric_aop_dtls g, product_details_master h, wo_fabric_aop_mst j
				WHERE   c.id = d.po_id AND d.mst_id = e.id AND g.batch_id = e.id AND d.prod_id = h.id and d.mst_id=e.id and g.batch_id=e.id and d.prod_id=h.id and d.prod_id = g.prod_id and j.id=g.mst_id and j.entry_form=462 and  c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and g.status_active=1 and g.is_deleted=0 $po_company_name_cond $buyer_name_cond $company_name_cond $within_group_cond $sales_order_cond $year_cond $sys_date_cond  group by  c.id, c.po_company_id, c.buyer_id, c.po_buyer, c.style_ref_no, c.job_no, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, e.color_id, h.item_description, h.gsm, h.dia_width, e.id, g.id, g.batch_dtls_id, g.process_type_id, g.quantity, g.rate, g.amount, g.number_of_roll, g.remark,h.id, j.sys_no, j.sys_date ,j.order_no,j.id,g.mst_id,j.supplier_id,j.company_id,c.season, h.detarmination_id ,d.width_dia_type, h.unit_of_measure,j.entry_form order by g.id desc";
				$sql_qry_prod_entry=sql_select($sql);

				$composition_arr = array(); $constructtion_arr = array();
				$sql_deter = "select a.id as ID, a.construction as CONSTRUCTION, b.copmposition_id as COPMPOSITION_ID, b.percent as PERCENT from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
				$data_array = sql_select($sql_deter);
				foreach ($data_array as $row) {
					$constructtion_arr[$row['ID']] = $row['CONSTRUCTION'];
					$composition_arr[$row['ID']] .= $composition[$row['COPMPOSITION_ID']] . " " . $row['PERCENT'] . "% ";
				}
				//echo "<pre>";
				//print_r($constructtion_arr);
				
				$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
				$color_library=return_library_array("select id,color_name from lib_color", "id", "color_name");

				/*$tran_sql= "select c.id,c.company_id, c.buyer_id,c.style_ref_no,c.job_no as fso_number, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, b.color_id, b.body_part_id , b.fabric_desc, b.gsm_weight, b.dia, e.batch_weight,b.id as so_dtls_id,e.id as batch_id,g.id as dtls_id, g.process_type_id, g.quantity, g.rate, g.amount, g.number_of_roll, g.remark, h.sys_no,h.order_no,h.id as recv_id,h.supplier_id,h.entry_form from fabric_sales_order_dtls b, fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e, wo_fabric_aop_dtls g, wo_fabric_aop_mst h where h.id=g.mst_id and b.mst_id=c.id and c.id=d.po_id and d.mst_id=e.id and g.sales_order_dtls_id=b.id and g.batch_id=e.id and e.color_id=b.color_id and h.entry_form in (462,467) and  b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and g.status_active=1 and g.is_deleted=0 $po_company_name_cond $buyer_name_cond $company_name_cond  $within_group_cond  $sales_order_cond $sys_date_cond  group by  c.id,c.company_id, c.buyer_id,c.style_ref_no,c.job_no, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, b.color_id, b.body_part_id , b.fabric_desc, b.gsm_weight, b.dia, e.batch_weight,b.id,e.id,g.id, g.process_type_id, g.quantity, g.rate, g.amount, g.number_of_roll, g.remark, h.sys_no,h.order_no,h.id,h.supplier_id,h.entry_form order by b.id desc";*/

				$tran_sql= "SELECT c.id, c.company_id, c.po_buyer AS buyer_id, c.style_ref_no, c.job_no AS fso_number, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, e.color_id, h.item_description AS fabric_desc, h.gsm AS gsm_weight, h.dia_width AS dia, e.id AS batch_id, g.id as issue_dtls_id, g.batch_dtls_id, g.process_type_id, g.quantity, g.rate, g.amount, g.number_of_roll, g.remark,h.id as prod_id, j.sys_no, j.sys_date ,j.order_no,j.issue_id,g.mst_id,j.entry_form FROM  fabric_sales_order_mst c, pro_batch_create_dtls d, pro_batch_create_mst e, wo_fabric_aop_dtls g, product_details_master h, wo_fabric_aop_mst j
				WHERE   c.id = d.po_id AND d.mst_id = e.id AND g.batch_id = e.id AND d.prod_id = h.id and d.mst_id=e.id and g.batch_id=e.id and d.prod_id=h.id and d.prod_id = g.prod_id and j.id=g.mst_id and j.entry_form in (462,467) and  c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and g.status_active=1 and g.is_deleted=0 $po_company_name_cond $buyer_name_cond $company_name_cond $within_group_cond $sales_order_cond $year_cond $sys_date_cond  group by  c.id, c.company_id, c.po_buyer, c.style_ref_no, c.job_no, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, e.color_id, h.item_description, h.gsm, h.dia_width, e.id, g.id, g.batch_dtls_id, g.process_type_id, g.quantity, g.rate, g.amount, g.number_of_roll, g.remark,h.id, j.sys_no, j.sys_date ,j.order_no,j.issue_id,g.mst_id,j.entry_form order by g.id desc";

				$tran_sql_array = sql_select($tran_sql);
				/*foreach( $result as $row){
					$issue_arr[$row[csf("company_id")]][$row[csf("issue_id")]][$row[csf("sys_no")]][$row[csf("supplier_id")]]['quantity']+=$row[csf("quantity")];
					$issue_arr[$row[csf("company_id")]][$row[csf("issue_id")]][$row[csf("sys_no")]][$row[csf("supplier_id")]]['order_no']=$row[csf("order_no")];
				}*/

				$batch_dtls_sql= "select  d.id,d.body_part_id,d.batch_qnty from pro_batch_create_dtls d where d.is_deleted=0 and d.status_active=1 $batch_dtls_id_cond";
				$batch_dtlsArray=sql_select($batch_dtls_sql);
				foreach ($batch_dtlsArray as $row) 
				{
					$batch_dtls_arr[$row[csf("id")]]["body_part_id"] =$row[csf("body_part_id")];
					//$batch_dtls_arr[$row[csf("id")]]["batch_qnty"] =$row[csf("batch_qnty")];
				}
				foreach ($tran_sql_array as $row) {
					if($row[csf("entry_form")]==462)
					{
						$issue_arr[$row[csf("entry_form")]][$row[csf("fso_number")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("color_id")]]["iss_qty"] += $row[csf("quantity")];
						$issue_arr[$row[csf("entry_form")]][$row[csf("fso_number")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("color_id")]]["amount"] += $row[csf("amount")];
					}
					else{
						$recv_arr[$row[csf("entry_form")]][$row[csf("fso_number")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("color_id")]]["qty"] += $row[csf("quantity")];
						$recv_arr[$row[csf("entry_form")]][$row[csf("fso_number")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("color_id")]]["amount"] += $row[csf("amount")];
						$recv_arr[$row[csf("entry_form")]][$row[csf("fso_number")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("color_id")]]["issue_id"] .= $row[csf("issue_id")].',';
					}
				}
				// and e.color_id=b.color_id
				//echo "<pre>";
				//print_r($issue_arr);
			?>
			<style>
				.breakAll{
					word-break:break-all;
					word-wrap: break-word;
				}
				.inline { 
				    display: inline-block; 
				}
			</style>
			<div> 
				<!-- Program  Info Start -->
				<div class='inline' style="width: 3100px; float:left;">
				<table width="3200" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" style="float:left; margin-top: 10px;">
					<thead>
						<tr>
							<th rowspan="2" width="30">SL</th>
							<th rowspan="2" width="170">Company</th>
							<th rowspan="2" width="170">PO Company</th>
							<th rowspan="2" width="150">PO Buyer</th>
							<th rowspan="2" width="130">Style Ref.</th>
							<th rowspan="2" width="100">Season</th>
							<th rowspan="2" width="120">Booking No</th>
							<th rowspan="2" width="100">Booking Type</th>
							<th rowspan="2" width="120">FSO</th>
							<th rowspan="2" width="150">Party Name</th>
							<th colspan="10">Fabric Details</th>
							<th colspan="3">Issue Details</th>
							<th colspan="3">Receive Details</th>
							<th colspan="3">Stock Details</th>
						</tr>
						<tr>
							<th width="100">Body Part</th>
							<th width="150">Construction</th>
							<th width="150">Composition</th>
							<th width="70">GSM</th>
							<th width="70">F/Dia</th>
							<th width="80">Dia Type</th>
							<th width="100">Batch No</th>
							<th width="100">Ext. No</th>
							<th width="100">Fab. Color</th>
							<th width="100">UOM</th>
							<th width="100">Total Issue Qty.</th>
							<th width="100">Issue Rate</th>
							<th width="100">Issue Amount</th>
							<th width="100">Total Receive Qty.</th>
							<th width="100">Receive Rate</th>
							<th width="100">Receive Amount</th>
							<th width="100">Balance</th>
							<th width="100">Balance Rate</th>
							<th>Balance Amount</th>
						</tr>
					</thead>
				</table>
				
				<div style="width:3200px; float:left; max-height:300px; overflow-y:scroll" id="scroll_body6">
				<table width="3180" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body_show6" style="float:left;">
					<tbody>
					<?
					$i=1;$tot_issue_qty=$tot_issue_amount=$tot_recv_qty=$tot_recv_amount=$tot_bal_qty=$tot_bal_amount=0;
					foreach($sql_qry_prod_entry as $row)
					{
						//echo $bgcolor;die("sumon");	break;
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$issue_qty=$issue_amount=$issue_rate=$recv_qty=$recv_amount=$recv_rate=$bal_qty=$bal_amount=$bal_rate=0;


						$issue_qty=$issue_arr[462][$row[csf("fso_number")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("color_id")]]["iss_qty"];						
						$issue_amount=$issue_arr[462][$row[csf("fso_number")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("color_id")]]["amount"];
						if($issue_amount>0){
							$issue_rate=$issue_amount/$issue_qty;
						}
						

						$recv_qty=$recv_arr[467][$row[csf("fso_number")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("color_id")]]["qty"];
						$recv_amount=$recv_arr[467][$row[csf("fso_number")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("color_id")]]["amount"];
						$issue_id=$recv_arr[467][$row[csf("fso_number")]][$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("color_id")]]["issue_id"];
						if($recv_amount>0){
							$recv_rate=$recv_amount/$recv_qty;
						} 
						$bal_qty=$issue_qty-$recv_qty;
						$bal_amount=$issue_amount-$recv_amount;
						if($bal_amount>0){
							$bal_rate=$bal_amount/$bal_qty;
						} 

						$batch_dtls_id=array_unique(explode(",",chop($row[csf("batch_dtls_id")],',')));
						foreach ($batch_dtls_id as $value) {
							$bodyPartID=$batch_dtls_arr[$value]["body_part_id"];
							//$batchQty +=$batch_dtls_arr[$value]["batch_qnty"];
						}
						if($row[csf("within_group")]==1) {
							$buyer_id=$row[csf("po_buyer")];
						}else{
							$buyer_id=$row[csf("buyer_id")];
						}

						if($row[csf("po_company")]=='') {
							$po_company=$row[csf("company_id")];
						}else{
							$po_company=$row[csf("po_company")];
						}

						?>						
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>');" id="tr_2nd<? echo $i; ?>">
							<td width="30"  style="word-wrap: break-word;word-break: break-all;" ><? echo $i; ?></td>
							<td width="170"  style="word-wrap: break-word;word-break: break-all;" ><? echo $company_library[$row[csf("company_id")]]; ?></td>
							<td width="170"  style="word-wrap: break-word;word-break: break-all;" ><? echo $company_library[$po_company]; ?></td>
							<td width="150"  style="word-wrap: break-word;word-break: break-all;" ><? echo $buyer_library[$buyer_id]; ?></td>
							<td width="130"  style="word-wrap: break-word;word-break: break-all;" ><? echo $row[csf("style_ref_no")]; ?></td>
							<td width="100"  style="word-wrap: break-word;word-break: break-all;" ><? echo $row[csf("season")]; ?></td>
							<td width="120"  style="word-wrap: break-word;word-break: break-all;" ><? echo $row[csf("sales_booking_no")]; ?></td>
							<td width="100"  style="word-wrap: break-word;word-break: break-all;" ><? echo $booking_typeArr[$row[csf("sales_booking_no")]];
							/*if($row[csf("within_group")]==1) 
							{
								echo $booking_typeArr[$row[csf("sales_booking_no")]];
								//if($entry_form_arr[$row[csf("sales_booking_no")]]["entry_form"]!='') echo $booking_type_arr[$entry_form_arr[$row[csf("sales_booking_no")]]["entry_form"]]; else echo "Main" ;
							}else{
								echo "" ;
							}*/ ?></td>
							<td width="120"  style="word-wrap: break-word;word-break: break-all;" ><? echo $row[csf("fso_number")]; ?></td>
							<td width="150"  style="word-wrap: break-word;word-break: break-all;" ><? echo $supplierArr[$row[csf("supplier_id")]]; ?></td>
							<td width="100"  style="word-wrap: break-word;word-break: break-all;"  ><? echo $body_part[$bodyPartID]; ?></td>
							<td width="150"  style="word-wrap: break-word;word-break: break-all;" ><? echo $constructtion_arr[$row[csf('detarmination_id')]]; ?></td>
							<td width="150"  style="word-wrap: break-word;word-break: break-all;" ><? echo $composition_arr[$row[csf('detarmination_id')]]; ?></td>
							<td width="70"  style="word-wrap: break-word;word-break: break-all;" ><? echo $row[csf("gsm_weight")]; ?></td>
							<td width="70"  style="word-wrap: break-word;word-break: break-all;" ><? echo $row[csf("dia")]; ?></td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;" ><? echo $fabric_typee[$row[csf("width_dia_type")]]; ?></td>
							<td width="100"  style="word-wrap: break-word;word-break: break-all;" ><? echo $row[csf("batch_no")]; ?></td>
							<td width="100"  style="word-wrap: break-word;word-break: break-all;" ><? echo $row[csf("extention_no")]; ?></td>
							<td width="100"  style="word-wrap: break-word;word-break: break-all;" ><? echo $color_library[$row[csf("color_id")]]; ?></td>
							<td width="100" align="center"  style="word-wrap: break-word;word-break: break-all;" ><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
							<td width="100" align="right"  style="word-wrap: break-word;word-break: break-all;" ><a href="##" onclick="fnc_purchase_details('<? echo $row[csf("fso_number")]."_".$row[csf("batch_id")]."_".$row[csf("color_id")]."_".$row[csf("prod_id")]."_".$txt_date_from."_".$txt_date_to."_".$row[csf("issue_id")];?>','issue_popup')"><p><? echo number_format($issue_qty,2);  ?></p></a></td>
							<td width="100" align="right"  style="word-wrap: break-word;word-break: break-all;" ><?  if($issue_rate!=0 && $issue_rate!='') echo number_format($issue_rate,2); else echo 0.00; ?></td>
							<td width="100" align="right"  style="word-wrap: break-word;word-break: break-all;" ><?  if($issue_amount!=0 && $issue_amount!='') echo number_format($issue_amount,4); else echo 0.00;  ?></td>
							<td width="100" align="right"><a href="##" onclick="fnc_purchase_details('<? echo $row[csf("fso_number")]."_".$row[csf("batch_id")]."_".$row[csf("color_id")]."_".$row[csf("prod_id")]."_".$txt_date_from."_".$txt_date_to."_".$issue_id;?>','receive_popup')"><p><? echo number_format($recv_qty,2);  ?></p></a></td>
							<td width="100" align="right"  style="word-wrap: break-word;word-break: break-all;" ><? if($recv_rate!=0 && $recv_rate!='') echo number_format($recv_rate,2); else echo 0.00; ?></td>
							<td width="100" align="right"  style="word-wrap: break-word;word-break: break-all;" ><?  if($recv_amount!=0 && $recv_amount!='') echo number_format($recv_amount,4); else echo 0.00; ?></td>
							<td width="100" align="right"  style="word-wrap: break-word;word-break: break-all;" ><? echo number_format($bal_qty,2); ?></td>
							<td width="100" align="right"  style="word-wrap: break-word;word-break: break-all;" ><? if($bal_rate!=0 && $bal_rate!='') echo number_format($bal_rate,2); else echo 0.00; ?></td>
							<td align="right"  style="word-wrap: break-word;word-break: break-all;" ><? if($bal_qty!=0 && $bal_qty!='') echo number_format($bal_amount,4); else echo 0.00; ?></td>
							
						</tr>
						<?
						$tot_issue_qty+=$issue_qty;
						$tot_issue_amount+=$issue_amount;
						$tot_recv_qty+=$recv_qty;
						$tot_recv_amount+=$recv_amount;
						$tot_bal_qty+=$bal_qty;
						$tot_bal_amount+=$bal_amount;
						$i++;
					}
					?>
					</tbody>
				</table>
				<table width="3180" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left">
					<tfoot>
						<tr style="background-color:#CCCCCC;">
							<td width="30">&nbsp;</td>
							<td width="170">&nbsp;</td>
							<td width="170">&nbsp;</td>
							<td width="150">&nbsp;</td>
							<td width="130">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td width="120">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td width="120">&nbsp;</td>
							<td width="150">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td width="150">&nbsp;</td>
							<td width="150">&nbsp;</td>
							<td width="70">&nbsp;</td>
							<td width="70">&nbsp;</td>
							<td width="80">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td width="100"><strong>Total</strong></td>
							<td width="100" align="right"><strong><? echo number_format($tot_issue_qty,2); ?></strong></td>
							<td width="100">&nbsp;</td>
							<td width="100" align="right"><strong><? echo number_format($tot_issue_amount,4); ?></strong></td>
							<td width="100" align="right"><strong><? echo number_format($tot_recv_qty,2); ?></strong></td>
							<td width="100">&nbsp;</td>
							<td width="100" align="right"><strong><? echo number_format($tot_recv_amount,4); ?></strong></td>
							<td width="100" align="right"><strong><? echo number_format($tot_bal_qty,2); ?></strong></td>
							<td width="100" align="right">&nbsp;</td>
							<td align="right"><strong><? echo number_format($tot_bal_amount,4); ?></strong></td>												
						</tr>
					</tfoot>
				</table>	
			</div>

		<?
	}
	
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
	echo "$total_data####$filename####$reportType";
	exit();
}

if ($action == "jobNo_popup") {
	echo load_html_head_contents("Job Info", "../../../", 1, 1, '', '1', '');
	extract($_REQUEST);
	?>
	<script>

		function js_set_value(booking_data) {
			document.getElementById('hidden_booking_data').value = booking_data;
			parent.emailwindow.hide();
		}

	</script>
</head>
<body>
	<div align="center">
		<fieldset style="width:830px;margin-left:4px;">
			<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
				<table cellpadding="0" cellspacing="0" width="600" border="1" rules="all" class="rpt_table">
					<thead>
						<th>Within Group</th>
						<th style="display: none;">Sales Order Type</th>
						<th>Search By</th>
						<th>Search</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px"
							class="formbutton"/>
							<input type="hidden" name="hidden_booking_data" id="hidden_booking_data" value="">
						</th>
					</thead>
					<tr class="general">
						<td align="center">
							<?
							echo create_drop_down("cbo_within_group", 150, $yes_no, "", 1, "--All--", $cbo_within_group, $dd, 0);
							?>
						</td>
						<td align="center" style="display: none;">
							<?
							echo create_drop_down("cbo_sales_order_type", 150, $sales_order_type_arr, "", 1, "--Select--", "", $dd, 0);
							?>
						</td>
						<td align="center">
							<?
							$search_by_arr = array(1 => "Sales Order No", 2 => "Sales / Booking No", 3 => "Style Ref.");
							echo create_drop_down("cbo_search_by", 150, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
							?>
						</td>
						<td align="center">
							<input type="text" style="width:140px" class="text_boxes" name="txt_search_common"
							id="txt_search_common"/>
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show"
							onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+'<? echo $cbo_company_id; ?>'+'_'+document.getElementById('cbo_within_group').value + '_' + document.getElementById('cbo_sales_order_type').value, 'create_job_search_list_view', 'search_div', 'fso_wise_aop_send_receive_report_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
							style="width:100px;"/>
						</td>
					</tr>
				</table>
				<div id="search_div" style="margin-top:10px"></div>
			</form>
		</fieldset>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action == "create_job_search_list_view") {
	$data = explode('_', $data);

	$company_arr = return_library_array("select id,company_name from lib_company", 'id', 'company_name');
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');

	$search_string = trim($data[0]);
	$search_by = $data[1];
	$company_id = $data[2];
	$within_group = $data[3];
	$sales_order_type = $data[4];
	$company_cond=" and c.company_id='$company_id'";

	/*$search_field_cond = '';
	if ($search_string != "") {
		if ($search_by == 1) {
			$search_field_cond = " and c.job_no like '%" . $search_string . "'";
		} else if ($search_by == 2) {
			$search_field_cond = " and c.sales_booking_no like '%" . $search_string . "'";
		} else {
			$search_field_cond = " and c.style_ref_no like '" . $search_string . "%'";
		}
	}*/
	if ($search_string!=''){
		//if($search_category==1) $search_cond=" and e.batch_no like '%$search_string%'";
		if($search_by==1) $search_cond=" and c.job_no like '%$search_string%'";
		if($search_by==2) $search_cond=" and c.sales_booking_no like '%$search_string%'";
		if($search_by==3) $search_cond=" and c.style_ref_no like '%$search_string%'";
	}

	if ($within_group == 0) $within_group_cond = ""; else $within_group_cond = " and c.within_group=$within_group";
	if ($sales_order_type == 0) $sales_type_cond = ""; else $sales_type_cond = " and sales_order_type=$sales_order_type";

	if ($db_type == 0) $year_field = "YEAR(c.insert_date) as year";
	else if ($db_type == 2) $year_field = "to_char(c.insert_date,'YYYY') as year";
	else $year_field = "";//defined Later

	$batch_str=",listagg(e.batch_no,',') within group (order by e.id)  as batch_no";
	
	/*$sql = "select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_order_type, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id, b.batch_no from fabric_sales_order_mst a LEFT JOIN pro_batch_create_mst b ON b.SALES_ORDER_NO = a.job_no and b.status_active=0 where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $within_group_cond $sales_type_cond $search_field_cond order by a.id desc";*/

	/*$sql= "select $year_field,c.id,c.job_no_prefix_num, c.job_no, c.within_group, c.sales_order_type, c.sales_booking_no, c.booking_date, c.buyer_id, c.style_ref_no ,e.batch_no 
	from wo_pre_cost_fabric_cost_dtls a, fabric_sales_order_dtls b, fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e, wo_booking_mst f  
	where a.id=b.pre_cost_fabric_cost_dtls_id and b.mst_id=c.id and c.id=d.po_id and d.mst_id=e.id and f.tagged_booking_no = c.sales_booking_no and e.color_id=b.color_id and f.booking_type=3 and f.process=35 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and f.company_id=$company_id $within_group_cond $sales_type_cond $search_field_cond 
	group by c.insert_date,c.id,c.job_no_prefix_num, c.job_no, c.within_group, c.sales_order_type, c.sales_booking_no, c.booking_date, c.buyer_id, c.style_ref_no ,e.batch_no order by c.id";*/

	$sql= "select  $year_field, c.id,c.company_id, c.buyer_id,c.style_ref_no,c.job_no as fso_number, c.within_group, c.sales_booking_no $batch_str  from 
	fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e , product_details_master  g , wo_fabric_aop_dtls f where g.id=f.prod_id and  f.batch_id=e.id and c.id=d.po_id and d.mst_id=e.id and d.prod_id=g.id  and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1  $company_cond $buyer_cond $within_group_cond $batch_date $search_cond group by  c.id,c.company_id, c.buyer_id,c.style_ref_no,c.job_no, c.within_group, c.sales_booking_no, c.insert_date order by c.id desc"; 

	/*$sql= "SELECT f.id,f.company_id,f.buyer_id,  0 as job_no, c.style_ref_no,c.job_no as fso_number,c.within_group,c.sales_booking_no,e.batch_no,e.extention_no,e.color_id,d.body_part_id,g.item_description as fabric_desc,g.gsm as gsm_weight,g.dia_width as dia ,sum (d.batch_qnty) as batch_weight,e.id as batch_id $batch_dtls_id_str from fabric_sales_order_mst c, pro_batch_create_dtls d, pro_batch_create_mst e, wo_booking_mst f, product_details_master g where c.id = d.po_id and d.mst_id = e.id and f.tagged_booking_no = c.sales_booking_no and d.prod_id = g.id and f.booking_type = 3 and f.process = 35 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and c.sales_booking_no ='$hid_fab_booking' $company_cond $buyer_cond $within_group_cond $batch_date $supplier_cond $search_cond group by   f.id,f.company_id,f.buyer_id, c.style_ref_no,c.job_no,c.within_group,c.sales_booking_no,e.batch_no,e.extention_no,e.color_id,d.body_part_id,g.item_description,g.gsm,g.dia_width,e.id order by f.id desc";*/
	//echo $sql; die;
	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="40">Year</th>
			<th width="50">Within Group</th>
			<th width="140">Buyer</th>
			<th width="130">Sales Order No</th>
			<th width="130">Sales/ Booking No</th>
			<th width="130">Style Ref.</th>
			<th>Batch No</th>
		</thead>
	</table>
	<div style="width:800px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table"
		id="tbl_list_search">
		<?
		$i = 1;
		if(!empty($result)){
			foreach ($result as $row) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

				if ($row[csf('within_group')] == 1)
					$buyer = $company_arr[$row[csf('buyer_id')]];
				else
					$buyer = $buyer_arr[$row[csf('buyer_id')]];
				
				$batch_nos=chop($row[csf('batch_no')],',');
				$batch_nos=implode(",",array_unique(explode(",",$batch_nos)));

				//$booking_data = $booking_arr[$row[csf('sales_booking_no')]]['job_no'] . "**" . $booking_arr[$row[csf('sales_booking_no')]]['id'] ;
				$booking_data = $row[csf('fso_number')] . "**" . $row[csf('id')] ;
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
					onClick="js_set_value('<? echo $booking_data; ?>');">
					<td width="30"><? echo $i; ?></td>
					<td width="40" align="center"><p><? echo $row[csf('year')]; ?></p></td>
					<td width="50"><p><? echo $yes_no[$row[csf('within_group')]]; ?>&nbsp;</p></td>
					<td width="140"><p><? echo $buyer; ?>&nbsp;</p></td>
					<td width="130"><p>&nbsp;<? echo $row[csf('fso_number')]; ?></p></td>
					<td width="130"><p><? echo $row[csf('sales_booking_no')]; ?></p></td>
					<td width="130"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
					<td ><p><? echo $batch_nos; ?></p></td>
				</tr>
				<?
				$i++;
			}
		}else{

		}
		?>
	</table>
</div>
<?
exit();
}
if ($action == "jobNo_popuppp") {
	echo load_html_head_contents("Job Info", "../../../", 1, 1, '', '1', '');
	extract($_REQUEST);
	?>
	<script>

		function js_set_value(booking_data) {
			document.getElementById('hidden_booking_data').value = booking_data;
			parent.emailwindow.hide();
		}

	</script>
</head>
<body>
	<div align="center">
		<fieldset style="width:830px;margin-left:4px;">
			<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
				<table cellpadding="0" cellspacing="0" width="600" border="1" rules="all" class="rpt_table">
					<thead>
						<th>Within Group</th>
						<th style="display: none;">Sales Order Type</th>
						<th>Search By</th>
						<th>Search</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px"
							class="formbutton"/>
							<input type="hidden" name="hidden_booking_data" id="hidden_booking_data" value="">
						</th>
					</thead>
					<tr class="general">
						<td align="center">
							<?
							echo create_drop_down("cbo_within_group", 150, $yes_no, "", 0, "--Select--", $cbo_within_group, $dd, 0);
							?>
						</td>
						<td align="center" style="display: none;">
							<?
							echo create_drop_down("cbo_sales_order_type", 150, $sales_order_type_arr, "", 1, "--Select--", "", $dd, 0);
							?>
						</td>
						<td align="center">
							<?
							$search_by_arr = array(1 => "Sales Order No", 2 => "Sales / Booking No", 3 => "Style Ref.");
							echo create_drop_down("cbo_search_by", 150, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
							?>
						</td>
						<td align="center">
							<input type="text" style="width:140px" class="text_boxes" name="txt_search_common"
							id="txt_search_common"/>
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show"
							onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+'<? echo $cbo_company_id; ?>'+'_'+document.getElementById('cbo_within_group').value + '_' + document.getElementById('cbo_sales_order_type').value, 'create_job_search_list_view', 'search_div', 'fso_wise_aop_send_receive_report_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
							style="width:100px;"/>
						</td>
					</tr>
				</table>
				<div id="search_div" style="margin-top:10px"></div>
			</form>
		</fieldset>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action == "create_job_search_list_viewwww") {
	$data = explode('_', $data);

	$company_arr = return_library_array("select id,company_name from lib_company", 'id', 'company_name');
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');

	$search_string = trim($data[0]);
	$search_by = $data[1];
	$company_id = $data[2];
	$within_group = $data[3];
	$sales_order_type = $data[4];

	$search_field_cond = '';
	if ($search_string != "") {
		if ($search_by == 1) {
			$search_field_cond = " and c.job_no like '%" . $search_string . "'";
		} else if ($search_by == 2) {
			$search_field_cond = " and c.sales_booking_no like '%" . $search_string . "'";
		} else {
			$search_field_cond = " and c.style_ref_no like '" . $search_string . "%'";
		}
	}

	if ($within_group == 0) $within_group_cond = ""; else $within_group_cond = " and c.within_group=$within_group";
	if ($sales_order_type == 0) $sales_type_cond = ""; else $sales_type_cond = " and sales_order_type=$sales_order_type";

	if ($db_type == 0) $year_field = "YEAR(c.insert_date) as year";
	else if ($db_type == 2) $year_field = "to_char(c.insert_date,'YYYY') as year";
	else $year_field = "";//defined Later
	
	/*$sql = "select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_order_type, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id, b.batch_no from fabric_sales_order_mst a LEFT JOIN pro_batch_create_mst b ON b.SALES_ORDER_NO = a.job_no and b.status_active=0 where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $within_group_cond $sales_type_cond $search_field_cond order by a.id desc";*/

	$sql= "select $year_field,c.id,c.job_no_prefix_num, c.job_no, c.within_group, c.sales_order_type, c.sales_booking_no, c.booking_date, c.buyer_id, c.style_ref_no ,e.batch_no 
	from wo_pre_cost_fabric_cost_dtls a, fabric_sales_order_dtls b, fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e, wo_booking_mst f  
	where a.id=b.pre_cost_fabric_cost_dtls_id and b.mst_id=c.id and c.id=d.po_id and d.mst_id=e.id and f.tagged_booking_no = c.sales_booking_no and e.color_id=b.color_id and f.booking_type=3 and f.process=35 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and f.company_id=$company_id $within_group_cond $sales_type_cond $search_field_cond 
	group by c.insert_date,c.id,c.job_no_prefix_num, c.job_no, c.within_group, c.sales_order_type, c.sales_booking_no, c.booking_date, c.buyer_id, c.style_ref_no ,e.batch_no order by c.id";
	//echo $sql;//die;
	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="40">Year</th>
			<th width="50">Within Group</th>
			<th width="140">Buyer</th>
			<th width="130">Sales Order No</th>
			<th width="130">Sales/ Booking No</th>
			<th width="130">Style Ref.</th>
			<th>Batch No</th>
		</thead>
	</table>
	<div style="width:800px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table"
		id="tbl_list_search">
		<?
		$i = 1;
		if(!empty($result)){
			foreach ($result as $row) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

				if ($row[csf('within_group')] == 1)
					$buyer = $company_arr[$row[csf('buyer_id')]];
				else
					$buyer = $buyer_arr[$row[csf('buyer_id')]];

				//$booking_data = $booking_arr[$row[csf('sales_booking_no')]]['job_no'] . "**" . $booking_arr[$row[csf('sales_booking_no')]]['id'] ;
				$booking_data = $row[csf('job_no')] . "**" . $row[csf('id')] ;
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
					onClick="js_set_value('<? echo $booking_data; ?>');">
					<td width="30"><? echo $i; ?></td>
					<td width="40" align="center"><p><? echo $row[csf('year')]; ?></p></td>
					<td width="50"><p><? echo $yes_no[$row[csf('within_group')]]; ?>&nbsp;</p></td>
					<td width="140"><p><? echo $buyer; ?>&nbsp;</p></td>
					<td width="130"><p>&nbsp;<? echo $row[csf('job_no')]; ?></p></td>
					<td width="130"><p><? echo $row[csf('sales_booking_no')]; ?></p></td>
					<td width="130"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
					<td ><p><? echo $row[csf('batch_no')]; ?></p></td>
				</tr>
				<?
				$i++;
			}
		}else{

		}
		?>
	</table>
</div>
<?
exit();
}

if($action=="issue_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");

	//$item_group_name = return_field_value("item_name","lib_item_group","id=".$product_sql[0][csf("item_group_id")],"item_name");	?>
   
    <table width="1080" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
    	<thead>
        	<tr>
            	<th width="30">SL</th>
                <th width="70">Issue Date</th>
                <th width="120">Issue ID</th>
                <th width="120">Service Company</th>
                <th width="100">Batch No</th>
                <th width="70">Ext. No</th>
                <th width="120">Sales Order No</th>
                <th width="120">Booking No</th>
                <th width="100">Color</th>
                <th width="70">Batch Quantity</th>
                <th width="70">Issue Quantity</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
		<?

		$prod_id_ref=explode("_",$prod_id);
		$fso_number=$prod_id_ref[0];
		$batch_id=$prod_id_ref[1];
		$color_id=$prod_id_ref[2];
		$prodId=$prod_id_ref[3];
		$txt_date_from=$prod_id_ref[4];
		$txt_date_to=$prod_id_ref[5];

		if($db_type==0)
		{ 
			//$year_cond=" and YEAR(a.insert_date)=$cbo_year_selection";
			if ($txt_date_from!="" &&  $txt_date_to!="") $sys_date_cond = "and j.sys_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'"; else $sys_date_cond ="";
		}
		else
		{
			//$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year_selection";
			if ($txt_date_from!="" &&  $txt_date_to!="") $sys_date_cond = "and j.sys_date between '".change_date_format($txt_date_from, "", "",1)."' and '".change_date_format($txt_date_to, "", "",1)."'"; else $sys_date_cond ="";
		}
		$color_library=return_library_array("select id,color_name from lib_color", "id", "color_name");
		$batch_dtls_sql= "select d.id,d.body_part_id,d.batch_qnty from pro_batch_create_dtls d where d.mst_id=$batch_id and d.is_deleted=0 and d.status_active=1";
		$batch_dtlsArray=sql_select($batch_dtls_sql);
		foreach ($batch_dtlsArray as $row) 
		{
			$batch_dtls_arr[$row[csf("id")]]["body_part_id"] =$row[csf("body_part_id")];
			$batch_dtls_arr[$row[csf("id")]]["batch_qnty"] =$row[csf("batch_qnty")];
		}

		/*$details_sql= "select c.id,c.company_id, c.buyer_id,c.style_ref_no,c.job_no as fso_number, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, b.color_id, b.body_part_id , b.fabric_desc, b.gsm_weight, b.dia, e.batch_weight,b.id as so_dtls_id,e.id as batch_id,g.id as dtls_id, g.process_type_id, g.quantity, g.rate, g.amount, g.number_of_roll, g.remark, h.sys_no,h.order_no,h.id as recv_id,h.supplier_id,h.entry_form,h.sys_no,h.sys_date from 
		 fabric_sales_order_dtls b, fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e, wo_fabric_aop_dtls g, wo_fabric_aop_mst h where h.id=g.mst_id and b.mst_id=c.id and c.id=d.po_id and d.mst_id=e.id  and g.sales_order_dtls_id=b.id and g.batch_id=e.id and e.color_id=b.color_id and h.entry_form in (462) and c.job_no='$fso_number' and e.id='$batch_id' and b.color_id='$color_id' and b.id=$so_dtls_id $sys_date_cond and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and g.status_active=1 and g.is_deleted=0  group by  c.id,c.company_id, c.buyer_id,c.style_ref_no,c.job_no, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, b.color_id, b.body_part_id , b.fabric_desc, b.gsm_weight, b.dia, e.batch_weight,b.id,e.id,g.id, g.process_type_id, g.quantity, g.rate, g.amount, g.number_of_roll, g.remark, h.sys_no,h.order_no,h.id,h.supplier_id,h.entry_form,h.sys_no,h.sys_date order by b.id desc";*/

		$details_sql= "SELECT c.id, c.company_id  as po_company, c.po_buyer AS buyer_id, c.style_ref_no, c.job_no AS fso_number, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, e.color_id, h.item_description AS fabric_desc, h.gsm AS gsm_weight, h.dia_width AS dia, e.id AS batch_id, g.id as issue_dtls_id, g.batch_dtls_id, g.process_type_id, g.quantity, g.rate, g.amount, g.number_of_roll, g.remark,h.id as prod_id, j.sys_no, j.sys_date ,j.order_no,j.id as issue_id,g.mst_id,j.supplier_id ,j.company_id,c.season,h.detarmination_id ,d.width_dia_type, h.unit_of_measure as cons_uom,j.entry_form
		FROM  fabric_sales_order_mst c, pro_batch_create_dtls d, pro_batch_create_mst e, wo_fabric_aop_dtls g, product_details_master h, wo_fabric_aop_mst j
		WHERE   c.id = d.po_id AND d.mst_id = e.id AND g.batch_id = e.id AND d.prod_id = h.id and d.mst_id=e.id and g.batch_id=e.id and d.prod_id=h.id and d.prod_id = g.prod_id and j.id=g.mst_id and j.entry_form=462 and  c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and g.status_active=1 and g.is_deleted=0 and c.job_no='$fso_number' and e.id='$batch_id' and e.color_id='$color_id'  and g.prod_id='$prodId' $sys_date_cond  group by  c.id, c.company_id, c.po_buyer, c.style_ref_no, c.job_no, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, e.color_id, h.item_description, h.gsm, h.dia_width, e.id, g.id, g.batch_dtls_id, g.process_type_id, g.quantity, g.rate, g.amount, g.number_of_roll, g.remark,h.id, j.sys_no, j.sys_date ,j.order_no,j.id,g.mst_id,j.supplier_id,j.company_id,c.season, h.detarmination_id ,d.width_dia_type, h.unit_of_measure,j.entry_form order by g.id desc";
		
		//echo $details_sql;
		$sql_result=sql_select($details_sql);
		$t=1;
		foreach($sql_result as $row)
		{
			if ($t%2==0)
			$bgcolor="#E9F3FF";
			else
			$bgcolor="#FFFFFF";

			$batch_dtls_id=array_unique(explode(",",$row[csf("batch_dtls_id")]));
			$batchQty=$bodyPartID='';
			foreach ($batch_dtls_id as $value) {
				$bodyPartID=$batch_dtls_arr[$value]["body_part_id"];
				$batchQty +=$batch_dtls_arr[$value]["batch_qnty"];
			}
        	?>
        	<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $t; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $t; ?>">
        		<td width="30"><? echo $t; ?></td>
                <td width="70"><? echo change_date_format($row[csf("sys_date")]); ?></td>
                <td width="120"><? echo $row[csf("sys_no")];?></td>
                <td width="120"><? echo $supplierArr[$row[csf('supplier_id')]]; ?></td>
                <td width="100"><? echo $row[csf("batch_no")];?></td>
                <td width="70"><? echo $row[csf("extention_no")];?></td>
                <td width="120"><? echo $row[csf("fso_number")];?></td>
                <td width="120"><? echo $row[csf("sales_booking_no")];?></td>
                <td width="100"><? echo $color_library[$row[csf("color_id")]];?></td>
                <td width="70" align="right"><? echo  number_format($batchQty,2); $total_batch_qnty+=$batchQty; ?></td>
                <td width="70" align="right"><? echo  number_format($row[csf("quantity")],2); $total_issue_qnty+=$row[csf("quantity")];?></td>
                <td><? echo $row[csf("remark")];?></td>
            </tr>
            <?
			$t++;
		}
		?>
        </tbody>
        <tfoot>
        	<tr>
        		<th colspan="10" align="right">Total</th>
                <th width="70" align="right"><? echo number_format($total_issue_qnty,2); ?></th>
               	<th >&nbsp;</th>
            </tr>
        </tfoot>
    </table>
    <?

}
if($action=="receive_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");

	//$item_group_name = return_field_value("item_name","lib_item_group","id=".$product_sql[0][csf("item_group_id")],"item_name");	?>
   
    <table width="1080" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
    	<thead>
        	<tr>
            	<th width="30">SL</th>
                <th width="70">Receive Date</th>
                <th width="120">Receive ID</th>
                <th width="120">Service Company</th>
                <th width="100">Batch No</th>
                <th width="70">Ext. No</th>
                <th width="120">Sales Order No</th>
                <th width="120">Booking No</th>
                <th width="100">Color</th>
                <th width="70">Issue Quantity</th>
                <th width="70">Receive Quantity</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
		<?

		$prod_id_ref=explode("_",$prod_id);
		$fso_number=$prod_id_ref[0];
		$batch_id=$prod_id_ref[1];
		$color_id=$prod_id_ref[2];
		$prodId=$prod_id_ref[3];
		$issueId=$prod_id_ref[6];
		$issueId=chop($issueId,',');
		$color_library=return_library_array("select id,color_name from lib_color", "id", "color_name");

		$issue_dtls_sql= "select d.id,d.quantity from wo_fabric_aop_dtls d where d.mst_id in ($issueId) and d.is_deleted=0 and d.status_active=1";
		$dtlsArray=sql_select($issue_dtls_sql);
		foreach ($dtlsArray as $row) 
		{
			$issue_qty_arr[$row[csf("id")]]["quantity"] =$row[csf("quantity")];
		}

		/*$details_sql= "select f.id,f.company_id, f.buyer_id,a.job_no,c.style_ref_no,c.job_no as fso_number, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, b.color_id, b.body_part_id , b.fabric_desc, b.gsm_weight, b.dia, e.batch_weight,b.id as so_dtls_id,e.id as batch_id,g.id as dtls_id, g.process_type_id, g.quantity, g.rate, g.amount, g.number_of_roll, g.remark, h.sys_no,h.order_no,h.id as recv_id,h.supplier_id,h.entry_form,h.sys_no,h.sys_date from 
		wo_pre_cost_fabric_cost_dtls a, fabric_sales_order_dtls b, fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e, wo_booking_mst f, wo_fabric_aop_dtls g, wo_fabric_aop_mst h where h.id=g.mst_id and f.buyer_id=h.buyer_id and a.id=b.pre_cost_fabric_cost_dtls_id and b.mst_id=c.id and c.id=d.po_id and d.mst_id=e.id and f.tagged_booking_no = c.sales_booking_no and g.sales_order_dtls_id=b.id and f.id=g.order_id and g.batch_id=e.id and e.color_id=b.color_id and f.booking_type=3 and f.process=35 and h.entry_form in (467) and c.job_no='$fso_number' and e.id='$batch_id' and b.color_id='$color_id' and b.id=$so_dtls_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and g.status_active=1 and g.is_deleted=0  group by  f.id,f.company_id, f.buyer_id,a.job_no,c.style_ref_no,c.job_no, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, b.color_id, b.body_part_id , b.fabric_desc, b.gsm_weight, b.dia, e.batch_weight,b.id,e.id,g.id, g.process_type_id, g.quantity, g.rate, g.amount, g.number_of_roll, g.remark, h.sys_no,h.order_no,h.id,h.supplier_id,h.entry_form,h.sys_no,h.sys_date order by b.id desc";*/

		/*$details_sql= "select c.id,c.company_id, c.buyer_id,c.style_ref_no,c.job_no as fso_number, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, b.color_id, b.body_part_id , b.fabric_desc, b.gsm_weight, b.dia, e.batch_weight,b.id as so_dtls_id,e.id as batch_id,g.id as dtls_id, g.process_type_id, g.quantity, g.rate, g.amount, g.number_of_roll, g.remark, h.sys_no,h.order_no,h.id as recv_id,h.supplier_id,h.entry_form,h.sys_no,h.sys_date from 
		 fabric_sales_order_dtls b, fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e, wo_fabric_aop_dtls g, wo_fabric_aop_mst h where h.id=g.mst_id and b.mst_id=c.id and c.id=d.po_id and d.mst_id=e.id  and g.sales_order_dtls_id=b.id and g.batch_id=e.id and e.color_id=b.color_id and h.entry_form in (467) and c.job_no='$fso_number' and e.id='$batch_id' and b.color_id='$color_id' and b.id=$so_dtls_id $sys_date_cond and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and g.status_active=1 and g.is_deleted=0  group by  c.id,c.company_id, c.buyer_id,c.style_ref_no,c.job_no, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, b.color_id, b.body_part_id , b.fabric_desc, b.gsm_weight, b.dia, e.batch_weight,b.id,e.id,g.id, g.process_type_id, g.quantity, g.rate, g.amount, g.number_of_roll, g.remark, h.sys_no,h.order_no,h.id,h.supplier_id,h.entry_form,h.sys_no,h.sys_date order by b.id desc";*/

		$details_sql= "SELECT c.id, c.company_id  as po_company, c.po_buyer AS buyer_id, c.style_ref_no, c.job_no AS fso_number, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, e.color_id, h.item_description AS fabric_desc, h.gsm AS gsm_weight, h.dia_width AS dia, e.id AS batch_id, g.issue_dtls_id as issue_dtls_id, g.batch_dtls_id, g.process_type_id, g.quantity, g.rate, g.amount, g.number_of_roll, g.remark,h.id as prod_id, j.sys_no, j.sys_date ,j.order_no,j.id as issue_id,g.mst_id,j.supplier_id ,j.company_id,c.season,h.detarmination_id ,d.width_dia_type, h.unit_of_measure as cons_uom,j.entry_form,g.id as rcv_dtls_id
		FROM  fabric_sales_order_mst c, pro_batch_create_dtls d, pro_batch_create_mst e, wo_fabric_aop_dtls g, product_details_master h, wo_fabric_aop_mst j
		WHERE   c.id = d.po_id AND d.mst_id = e.id AND g.batch_id = e.id AND d.prod_id = h.id and d.mst_id=e.id and g.batch_id=e.id and d.prod_id=h.id and d.prod_id = g.prod_id and j.id=g.mst_id and j.entry_form=467 and  c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and g.status_active=1 and g.is_deleted=0 and c.job_no='$fso_number' and e.id='$batch_id' and e.color_id='$color_id'  and g.prod_id='$prodId' $sys_date_cond  group by  c.id, c.company_id, c.po_buyer, c.style_ref_no, c.job_no, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, e.color_id, h.item_description, h.gsm, h.dia_width, e.id, g.issue_dtls_id, g.batch_dtls_id, g.process_type_id, g.quantity, g.rate, g.amount, g.number_of_roll, g.remark,h.id, j.sys_no, j.sys_date ,j.order_no,j.id,g.mst_id,j.supplier_id,j.company_id,c.season, h.detarmination_id ,d.width_dia_type, h.unit_of_measure,j.entry_form,g.id order by g.id desc";
		
		//echo $details_sql;
		$sql_result=sql_select($details_sql);
		$t=1; $issue_qty='';
		foreach($sql_result as $row)
		{
			if ($t%2==0)
			$bgcolor="#E9F3FF";
			else
			$bgcolor="#FFFFFF";
			$issue_qty=$issue_qty_arr[$row[csf("issue_dtls_id")]]["quantity"] ;
        	?>
        	<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $t; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $t; ?>">
        		<td width="30"><? echo $t; ?></td>
                <td width="70"><? echo change_date_format($row[csf("sys_date")]); ?></td>
                <td width="120"><? echo $row[csf("sys_no")];?></td>
                <td width="120"><? echo $supplierArr[$row[csf('supplier_id')]]; ?></td>
                <td width="100"><? echo $row[csf("batch_no")];?></td>
                <td width="70"><? echo $row[csf("extention_no")];?></td>
                <td width="120"><? echo $row[csf("fso_number")];?></td>
                <td width="120"><? echo $row[csf("sales_booking_no")];?></td>
                <td width="100"><? echo $color_library[$row[csf("color_id")]]?></td>
                <td width="70" align="right"><? echo  number_format($issue_qty,2); $total_issue_qnty+=$issue_qty; ?></td>
                <td width="70" align="right"><? echo  number_format($row[csf("quantity")],2); $total_receive_qnty+=$row[csf("quantity")];?></td>
                <td><? echo $row[csf("remark")];?></td>
            </tr>
            <?
			$t++;
		}
		?>
        </tbody>
        <tfoot>
        	<tr>
        		<th colspan="10" align="right">Total</th>
                <th width="70" align="right"><? echo number_format($total_receive_qnty,2); ?></th>
               	<th >&nbsp;</th>
            </tr>
        </tfoot>
    </table>
    <?

}

if($action=="booking_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	
	<script>
	function js_set_value(str)
	{
		$("#hide_booking_no").val(str);
		parent.emailwindow.hide(); 
	}
	</script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:980px;">
            <table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th width="100">Booking No</th>
                    <th width="80">Style Desc.</th>
                    <th width="200">Date Range</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 				
                    <input type="hidden" name="hide_booking_no" id="hide_booking_no" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_id $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>                 
                        <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:100px"></td>
                        <td><input name="txt_style_desc" id="txt_style_desc" class="text_boxes" style="width:80px"></td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
                          <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                         </td> 	
                         <td align="center">
                 			<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company_id; ?>'+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('txt_style_desc').value, 'create_booking_search_list_view', 'search_div', 'fso_wise_aop_send_receive_report_controller','setFilterGrid(\'table_body_booking\',1)')" style="width:100px;" />              
                        </td>
                    </tr>
                    <tr>
                        <td  align="center" height="40" valign="middle" colspan="6">
                        <? 
                        echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );		
                        ?>
                        <? echo load_month_buttons();  ?>
                        </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}

if ($action=="create_booking_search_list_view")
{
	$data=explode('_',$data);
	$style_desc=$data[7];
	if ($data[0]!=0) $company="  a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	
	if ($data[1]!=0){$buyer=" and a.buyer_id='$data[1]'";}
	else{$buyer="";}
	
	if($db_type==0)
	 {
		  // $booking_year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[4]";
		  $booking_year_cond=" and YEAR(a.insert_date)=$data[4]";
		  if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' 
		  and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date =""; 
     }
	if($db_type==2)
	 {
		  $booking_year_cond=" and to_char(a.insert_date,'YYYY')=$data[4]";
		  if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."'
		  and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	 }
	if($data[6]==4 || $data[6]==0)
	{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[5]%'  $booking_year_cond  "; else $booking_cond="";
		if (str_replace("'","",$data[7])!="") $style_des_cond=" and b.style_des like '%$data[7]%' "; else $style_des_cond="";
	}
 
	
	

	/*$po_array=array();
	$sql_po= sql_select("select a.booking_no_prefix_num, a.booking_no,a.po_break_down_id from wo_non_ord_samp_booking_mst a  where $company $buyer $booking_date and booking_type=4  and   status_active=1  and 	is_deleted=0 order by booking_no");
	foreach($sql_po as $row)
	{
		$po_id=explode(",",$row[csf("po_break_down_id")]);
		$po_number_string="";
		foreach($po_id as $key=> $value )
		{
			$po_number_string.=$po_number[$value].",";
		}
		$po_array[$row[csf("po_break_down_id")]]=rtrim($po_number_string,",");
	}*/
	$style_library=return_library_array( "select id,style_ref_no from sample_development_mst", "id", "style_ref_no"  );
    $approved=array(0=>"No",1=>"Yes");
    $is_ready=array(0=>"No",1=>"Yes",2=>"No"); 
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$arr=array (2=>$comp,3=>$buyer_arr,4=>$item_category,5=>$fabric_source,6=>$suplier,7=>$style_library,9=>$approved,10=>$is_ready);
	 $sql= "select a.booking_no_prefix_num, a.booking_no,a.booking_date,a.company_id,a.buyer_id,a.item_category,a.fabric_source,a.supplier_id,a.is_approved,a.ready_to_approved,a.pay_mode,b.style_id,b.style_des from wo_non_ord_samp_booking_mst  a left join wo_non_ord_samp_booking_dtls b on a.booking_no=b.booking_no  and b.status_active=1 and b.is_deleted=0  where   $company". set_user_lavel_filtering(' and a.buyer_id','buyer_id')." $buyer $booking_date $booking_cond $style_des_cond and a.booking_type=4 and  a.status_active=1 and a.is_deleted=0 group by a.booking_no_prefix_num, a.booking_no,a.booking_date,a.company_id,a.buyer_id,a.item_category,a.fabric_source,a.supplier_id,a.is_approved,a.ready_to_approved,a.pay_mode,b.style_id,b.style_des order by booking_no"; 
	//echo $sql;
	//echo create_list_view("list_view", "Booking No,Booking Date,Company,Buyer,Fabric Nature,Fabric Source,Supplier,Style,Style Desc.,Approved,Is-Ready", "100,80,100,100,80,80,80,50,80,50","950","320",0, $sql , "js_set_value", "booking_no", "", 1, "0,0,company_id,buyer_id,item_category,fabric_source,supplier_id,style_id,0,is_approved,ready_to_approved", $arr , "booking_no_prefix_num,booking_date,company_id,buyer_id,item_category,fabric_source,supplier_id,style_id,style_des,is_approved,ready_to_approved", '','','0,3,0,0,0,0,0,0,0,0,0,0','','');
	?>
   <table class="rpt_table scroll" width="970" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
       <thead>
            <th width="40">Sl</th> 
            <th width="80">Booking No</th>  
            <th width="80">Booking Date</th>           	 
            <th width="100">Buyer</th>
            <th width="120">Fabric Nature</th>
            <th width="80">Fabric Source</th>
            <th width="80">Pay Mode</th>
            <th width="100">Supplier</th>
            <th width="80">Style</th>
            <th width="200">Style Desc.</th>
        </thead>
     </table>
		<div style="width:970px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="952" class="rpt_table" id="table_body_booking">
                <tbody>
                    <? 
                    $i=1;
                    $sql_data=sql_select($sql);
                    foreach($sql_data as $row){
                        if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";    
                    ?>
                    <tr bgcolor="<? echo $bgcolor;?>" onClick="js_set_value('<? echo $row[csf('booking_no')]  ?>')" style="cursor:pointer">
                        <td width="40"><? echo $i;?></td> 
                        <td width="80"><? echo $row[csf('booking_no_prefix_num')];?></td>  
                        <td width="80"><? echo date("d-m-Y",strtotime($row[csf('booking_date')]));?></td>           	 
                        <td width="100"><? echo $buyer_arr[$row[csf('buyer_id')]];?></td>
                        <td width="120"><? echo $item_category[$row[csf('item_category')]];?></td>
                        <td width="80"><? echo $fabric_source[$row[csf('fabric_source')]];?></td>
                        <td width="80">
                        <? echo $pay_mode[$row[csf('pay_mode')]];?>
                        </td>
                        <td width="100">
                        <? 
                        if($row[csf('pay_mode')]==3 || $row[csf('pay_mode')]==5){
                            echo $comp[$row[csf('supplier_id')]];
                        }
                        else{
                            echo $suplier[$row[csf('supplier_id')]];
                        }
                        ?>
                        </td>
                        <td width="80" style="word-wrap: break-word;word-break: break-all;"><? echo $style_library[$row[csf('style_id')]];?></td>
                        <td width="" style="word-wrap: break-word;word-break: break-all;"><? echo $row[csf('style_des')];?></td>

                    </tr>
                    <?
                    $i++;
                     }
                    ?>
                </tbody>
            </table>
        </div>
    <?
}
disconnect($con);
?>
