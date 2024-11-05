<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
include('../../../includes/class4/class.conditions.php');
include('../../../includes/class4/class.reports.php');
include('../../../includes/class4/class.fabrics.php');
include('../../../includes/class4/class.yarns.php');
include('../../../includes/class4/class.trims.php');
include('../../../includes/class4/class.emblishments.php');
include('../../../includes/class4/class.washes.php');
include('../../../includes/class4/class.conversions.php');
//include('../../../includes/class4/class.fabrics2.php');
// include('../../../includes/class4/class.commercials.php');
// include('../../../includes/class4/class.commisions.php');
// include('../../../includes/class4/class.others.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if ($action == "load_drop_down_buyer") 
{
    echo create_drop_down("cbo_buyer_id", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1)) and b.tag_company='$data' $buyer_cond  order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "", 0);
	exit();
}

if ($action=="job_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
    ?>	
    <script>
        function js_set_value(str)
        {
            $("#hdn_job_info").val(str); 
            parent.emailwindow.hide();
        }  
	</script>
    <input type="hidden" id="hdn_job_info" />
    <?
	if ($data[1]==0) $buyer_name=""; else $buyer_name=" and buyer_name=$data[1]";
	// if ($data[2]=="") $order_no=""; else $order_no=" and a.po_number=$data[2]";
	$job_no=str_replace("'","",$txt_job_id);
	if ($data[2]=="") $job_no_cond=""; else $job_no_cond="  and job_no_prefix_num in('$data[2]')";
	
	$sql="select id,job_no_prefix_num, job_no, buyer_name, style_ref_no from wo_po_details_master  where company_name=$data[0] and is_deleted=0 $buyer_name $job_no_cond ORDER BY id desc";
	// echo $sql;
	$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$arr=array(2=>$buyer);
	
	echo  create_list_view("list_view", "Job No,Job no,Buyer,Style Ref.", "110,110,150,180","610","350",0, $sql, "js_set_value", "id,job_no,style_ref_no", "", 1, "0,0,buyer_name,0,0", $arr , "job_no_prefix_num,job_no,buyer_name,style_ref_no", "pi_variance_report_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0','') ;
	disconnect($con);
	exit();
}

if ($action=="po_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
    ?>	
    <script>
        function js_set_value(str)
        {
            $("#hdn_po_info").val(str); 
            parent.emailwindow.hide();
        }
	</script>
    <input type="hidden" id="hdn_po_info" />
    <?
	if ($data[1]==0) $buyer_name=""; else $buyer_name=" and b.buyer_name=$data[1]";
	if ($data[2]=="") $order_no=""; else $order_no=" and a.po_number=$data[2]";
	$job_no=str_replace("'","",$txt_job_id);
	if ($data[2]=="") $job_no_cond=""; else $job_no_cond="  and b.job_no_prefix_num in('$data[2]')";
	
	$sql="select a.id as po_id, a.po_number,b.id, b.job_no_prefix_num, b.job_no, b.buyer_name, b.style_ref_no from wo_po_details_master b, wo_po_break_down a  where b.job_no=a.job_no_mst and b.company_name=$data[0] and b.is_deleted=0 $buyer_name $job_no_cond ORDER BY b.job_no";
	//echo $sql;
	$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$arr=array(1=>$buyer);
	
	echo  create_list_view("list_view", "Job No,Buyer,Style Ref.,Order No", "110,110,150,180","610","350",0, $sql, "js_set_value", "id,job_no,style_ref_no,po_number,po_id,buyer_name", "", 1, "0,buyer_name,0,0,0", $arr , "job_no_prefix_num,buyer_name,style_ref_no,po_number", "pi_variance_report_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0','') ;
	disconnect($con);
	exit();
}

if ($action=="wo_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
    ?>	
    <script>
        function js_set_value(str)
        {
            $("#hdn_wo_info").val(str); 
            parent.emailwindow.hide();
        }
	</script>
    <input type="hidden" id="hdn_wo_info" />
    <?

    $buyer_cond=$buyer_cond2="";
	if($data[1]!=0) {	
		$buyer_cond =" and b.buyer_id=$data[1]";	
		$buyer_cond2 =" and a.buyer_id=$data[1]";	
	}

	$item_category_cond="";
	if($data[2]!="") {	
		$item_category_cond=" and a.item_category in($data[2])";	
	}

	$sql_wo="SELECT a.id, a.ydw_no as wo_number, a.yarn_dyeing_prefix_num as wo_number_prefix_num, a.booking_date as wo_date, 0 as buyer_id
	from wo_yarn_dyeing_mst a
	where a.status_active=1 and a.is_deleted=0 and a.item_category_id=24 and a.company_id=$data[0] $item_category_cond
	union all
	SELECT a.id, a.wo_number as wo_number, a.wo_number_prefix_num as wo_number_prefix_num, a.wo_date as wo_date, b.buyer_id as buyer_id
	from wo_non_order_info_mst a, wo_non_order_info_dtls b 
	where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=1 and entry_form=144 and a.company_name=$data[0] $item_category_cond $buyer_cond
	group by a.id, a.wo_number, a.wo_number_prefix_num, a.wo_date, b.buyer_id 
	union all
	SELECT a.id, a.booking_no as wo_number, a.booking_no_prefix_num as wo_number_prefix_num, a.booking_date as wo_date, a.buyer_id
	from wo_booking_mst a
	where a.status_active=1 and a.is_deleted=0 and a.booking_type in(1,2,6) and a.item_category in(2,3,4,25) and a.company_id=$data[0] $item_category_cond $buyer_cond2 order by id desc";
	// echo $sql_wo;
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$arr=array(2=>$buyer_arr);
	echo create_list_view("list_view", "WO No,WO Date,Buyer","100,90,160","400","380",0, $sql_wo , "js_set_value", "id,wo_number", "", 1, "0,0,buyer_id", $arr, "wo_number,wo_date,buyer_id", "","setFilterGrid('list_view',-1)","0,0,0","") ;
	disconnect($con);
	exit();
}

if ($action=="pi_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);	
	$data=explode('_',$data);
    ?>	
    <script>
        function js_set_value(str)
        {
            $("#hdn_pi_info").val(str); 
            parent.emailwindow.hide();
        }
	</script>
    <input type="hidden" id="hdn_pi_info" />
    <?

	$company_cond =" and a.importer_id=$data[0]";
	$item_category_cond="";
	if ($data[1] != "")	$item_category_cond =" and a.item_category_id in ($data[1])";
	

	$sql_pi="SELECT a.id, a.pi_number from com_pi_master_details a where a.status_active=1 and a.is_deleted=0 $company_cond $item_category_cond order by id desc";

	echo create_list_view("list_view", "PI No, System ID","190,160","400","380",0, $sql_pi , "js_set_value", "id,pi_number", "", 1, "0,0", $arr, "pi_number,id", "","setFilterGrid('list_view',-1)","0","") ;
	disconnect($con);
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_job_id=str_replace("'","",$txt_job_id);
	$txt_style_no=str_replace("'","",$txt_style_no);
	$txt_po_no=str_replace("'","",$txt_po_no);
	$txt_po_id=str_replace("'","",$txt_po_id);
	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
	$txt_wo_no=trim(str_replace("'","",$txt_wo_no));
	$txt_wo_id=trim(str_replace("'","",$txt_wo_id));
	$txt_pi_no=trim(str_replace("'","",$txt_pi_no));
	$txt_pi_id=trim(str_replace("'","",$txt_pi_id));
	$cbo_date_type=str_replace("'","",$cbo_date_type);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_approval_status=str_replace("'","",$cbo_approval_status);

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
	$body_part_arr = return_library_array("select id,body_part_full_name from lib_body_part where status_active=1 and is_deleted=0", "id", "body_part_full_name");
	$type_array=array(0=>$blank_array,1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type,6=>$blank_array,99=>$blank_array);
	$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id","yarn_count");
	$buyer_arr = return_library_array("select id,buyer_name from  lib_buyer ","id","buyer_name");
	$po_job_id_arr = return_library_array("select id,job_id from wo_po_break_down ","id","job_id");

	if($db_type==0)
	{
		$txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
		$txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
	}
	else if($db_type==2)
	{
		$txt_date_from=change_date_format($txt_date_from,'','',-1);
		$txt_date_to=change_date_format($txt_date_to,'','',-1);
	}

	$sql_cond="";
	if($cbo_company_name) $sql_cond.=" and e.importer_id='$cbo_company_name' ";
	if($cbo_item_category_id !='') 
	{
		$sql_cond.=" and e.item_category_id in ($cbo_item_category_id) ";
		$sql_cond_1=" and e.item_category_id in ($cbo_item_category_id) ";
		$sql_cond_2=" and e.item_category_id in ($cbo_item_category_id) ";
	}

	if($txt_job_id !="") 
	{
		$sql_cond.=" and a.id=$txt_job_id ";
		$job_no="'".$txt_job_no."'";
	}
	else if($txt_job_no !="")
	{
		$sql_cond.=" and a.job_no= '$txt_job_no' ";
		$job_no="'".$txt_job_no."'";
	}

	if($txt_style_no !="")
	{
		$sql_cond.=" and a.style_ref_no = '$txt_style_no' ";
	}

	if($txt_po_id !="") 
	{
		$po_cond=" and b.id=$txt_po_id ";
	}
	else if($txt_po_no !="")
	{
		$po_cond=" and b.po_number = '$txt_po_no' ";
	}

	if($txt_wo_id !="") 
	{
		$sql_cond.=" and c.id=$txt_wo_id ";
	}
	else if($txt_wo_no !="")
	{
		$wo_no_cond_1=" and c.yarn_dyeing_prefix_num = '$txt_wo_no' ";
		$wo_no_cond_2=" and c.wo_number_prefix_num = '$txt_wo_no' ";
		$wo_no_cond_3=" and c.booking_no_prefix_num = '$txt_wo_no' ";
	}

	if($txt_pi_id !="") 
	{
		$sql_cond.=" and e.id=$txt_pi_id ";
	}
	else if($txt_pi_no !="")
	{
		$sql_cond.=" and e.pi_number = '$txt_pi_no' ";
	}

	if($cbo_date_type==2 &&  ($txt_date_from && $txt_date_to))
	{
		if($txt_date_from !="" && $txt_date_to !="") 
		{
			$wo_date_cond=" and c.booking_date between '$txt_date_from' and '$txt_date_to'";
			$wo_date_cond_2=" and c.wo_date between '$txt_date_from' and '$txt_date_to'";
		}
	}
	if($cbo_date_type==1 &&  ($txt_date_from && $txt_date_to) )
	{
		if($txt_date_from !="" && $txt_date_to !="") $sql_cond.=" and e.pi_date between '$txt_date_from' and '$txt_date_to'";
	}
	if($cbo_approval_status!=0)
	{
		$sql_cond.=" and e.approved=$cbo_approval_status";
	}
	
	$sql="SELECT a.id as JOB_ID ,0 as PO_ID ,a.job_no as JOB_NO, a.buyer_name as BUYER_ID ,a.style_ref_no as STYLE_REF_NO, null as PO_NUMBER, c.id as MST_ID, c.ydw_no as WO_NUMBER, e.item_category_id as ITEM_CATEGORY, d.id as DTLS_ID, d.YARN_WO_QTY as  WO_QNTY, d.dyeing_charge as WO_RATE, d.amount as WO_AMOUNT, 0 as NUMBER_OF_LOT, 0 as ITEM_GROUP_ID, 0 as PRE_COST_FABRIC_COST_DTLS_ID, e.id as PI_ID, e.pi_number as PI_NUMBER, e.goods_rcv_status as GOODS_RCV_STATUS, f.id as PI_DTLS_ID, f.uom as UOM, f.quantity as PI_QUANTITY, f.rate as PI_RATE, f.amount as PI_AMOUNT, d.yarn_type as YARN_TYPE, d.count as YARN_COUNT, d.yarn_color as COLOR_NAME, d.yarn_comp_type1st as YARN_COMP_TYPE1ST, f.fabric_construction as FABRIC_CONSTRUCTION,f.fabric_composition as FABRIC_COMPOSITION, f.gsm as GSM ,f.dia_width as DIA_WIDTH, f.yarn_composition_percentage1 as PERCENTAGE, e.importer_id
	from wo_po_details_master a, wo_yarn_dyeing_mst c, wo_yarn_dyeing_dtls d, com_pi_master_details e, com_pi_item_details f
	where a.job_no=d.job_no and c.id=d.mst_id and f.work_order_dtls_id=d.id and e.id=f.pi_id and e.pi_basis_id=1 and c.item_category_id=24 and a.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 and f.status_active=1 $sql_cond $sql_cond_1 $wo_no_cond_1 $wo_date_cond
	union all 
	SELECT a.id as JOB_ID ,0 as PO_ID,a.job_no as JOB_NO, a.buyer_name as BUYER_ID ,a.style_ref_no as STYLE_REF_NO, null as PO_NUMBER, c.id as MST_ID, c.wo_number as WO_NUMBER, e.item_category_id as ITEM_CATEGORY, d.id as DTLS_ID, d.supplier_order_quantity as WO_QNTY, d.rate as WO_RATE, d.amount as WO_AMOUNT, d.number_of_lot as NUMBER_OF_LOT, 0 as ITEM_GROUP_ID, 0 as PRE_COST_FABRIC_COST_DTLS_ID, e.id as PI_ID, e.pi_number as PI_NUMBER, e.goods_rcv_status as GOODS_RCV_STATUS, f.id as PI_DTLS_ID, f.uom as UOM, f.quantity as PI_QUANTITY, f.rate as PI_RATE, f.amount as PI_AMOUNT, f.yarn_type as YARN_TYPE, f.count_name as YARN_COUNT, f.color_id as COLOR_NAME, f.yarn_composition_item1 as YARN_COMP_TYPE1ST, f.fabric_construction as FABRIC_CONSTRUCTION,f.fabric_composition as FABRIC_COMPOSITION, f.gsm as GSM ,f.dia_width as DIA_WIDTH, f.yarn_composition_percentage1 as PERCENTAGE, e.importer_id
	from wo_po_details_master a, wo_non_order_info_mst c, wo_non_order_info_dtls d, com_pi_master_details e, com_pi_item_details f 
	where a.job_no=d.job_no and c.id=d.mst_id and f.work_order_dtls_id=d.id and e.id=f.pi_id and e.pi_basis_id=1 and e.item_category_id=1 and c.entry_form=144 and a.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 and f.status_active=1 $sql_cond $sql_cond_2 $wo_no_cond_2 $wo_date_cond_2
	union all
	SELECT a.id as JOB_ID ,b.id as PO_ID,a.job_no as JOB_NO, a.buyer_name as BUYER_ID ,a.style_ref_no as STYLE_REF_NO, b.po_number as PO_NUMBER, c.id as MST_ID, c.booking_no as WO_NUMBER, e.item_category_id as ITEM_CATEGORY, d.id as DTLS_ID, d.fin_fab_qnty as WO_QNTY, d.rate as WO_RATE,d.amount as WO_AMOUNT, 0 as NUMBER_OF_LOT, d.trim_group as ITEM_GROUP_ID, d.pre_cost_fabric_cost_dtls_id as PRE_COST_FABRIC_COST_DTLS_ID, e.id as PI_ID, e.pi_number as PI_NUMBER, e.goods_rcv_status as GOODS_RCV_STATUS, f.id as PI_DTLS_ID, f.uom as UOM, f.quantity as PI_QUANTITY, f.rate as PI_RATE, f.amount as PI_AMOUNT, f.yarn_type as YARN_TYPE, f.count_name as YARN_COUNT, f.color_id as COLOR_NAME, f.yarn_composition_item1 as YARN_COMP_TYPE1ST, f.fabric_construction as FABRIC_CONSTRUCTION,f.fabric_composition as FABRIC_COMPOSITION, f.gsm as GSM ,f.dia_width as DIA_WIDTH, f.yarn_composition_percentage1 as PERCENTAGE, e.importer_id
	from wo_po_details_master a, wo_po_break_down b, wo_booking_mst c, wo_booking_dtls d, com_pi_master_details e, com_pi_item_details f
	where a.job_no=d.job_no and b.id=d.po_break_down_id and c.booking_no=d.booking_no and c.booking_type in(1,2,6) and f.work_order_id=c.id  and e.id=f.pi_id and e.pi_basis_id=1 and e.item_category_id in (2,3) and d.fabric_color_id=f.color_id and d.construction=f.fabric_construction and d.copmposition=f.fabric_composition and d.dia_width=f.dia_width and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 and f.status_active=1 $sql_cond $po_cond $sql_cond_2 $wo_no_cond_3 $wo_date_cond 
	union all
	SELECT a.id as JOB_ID,b.id as PO_ID,a.job_no as JOB_NO, a.buyer_name as BUYER_ID ,a.style_ref_no as STYLE_REF_NO, b.po_number as PO_NUMBER, c.id as MST_ID, c.booking_no as WO_NUMBER, e.item_category_id as ITEM_CATEGORY, d.id as DTLS_ID, d.fin_fab_qnty as WO_QNTY, d.rate as WO_RATE, d.amount as WO_AMOUNT, 0 as NUMBER_OF_LOT, d.trim_group as ITEM_GROUP_ID, d.pre_cost_fabric_cost_dtls_id as PRE_COST_FABRIC_COST_DTLS_ID, e.id as PI_ID, e.pi_number as PI_NUMBER, e.goods_rcv_status as GOODS_RCV_STATUS, f.id as PI_DTLS_ID, f.uom as UOM, f.quantity as PI_QUANTITY, f.rate as PI_RATE, f.amount as PI_AMOUNT, f.yarn_type as YARN_TYPE, f.count_name as YARN_COUNT, f.color_id as COLOR_NAME, f.yarn_composition_item1 as YARN_COMP_TYPE1ST, f.fabric_construction as FABRIC_CONSTRUCTION,f.fabric_composition as FABRIC_COMPOSITION, f.gsm as GSM ,f.dia_width as DIA_WIDTH, f.yarn_composition_percentage1 as PERCENTAGE, e.importer_id
	from wo_po_details_master a, wo_po_break_down b, wo_booking_mst c, wo_booking_dtls d, com_pi_master_details e, com_pi_item_details f
	where a.job_no=d.job_no and b.id=d.po_break_down_id and c.booking_no=d.booking_no and c.booking_type in(1,2,6) and f.work_order_dtls_id=d.id and e.id=f.pi_id and e.pi_basis_id=1 and e.item_category_id=25 and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 and f.status_active=1 $sql_cond $po_cond $sql_cond_2 $wo_no_cond_3 $wo_date_cond 
	union all
	SELECT a.id as JOB_ID,b.id as PO_ID,a.job_no as JOB_NO, a.buyer_name as BUYER_ID ,a.style_ref_no as STYLE_REF_NO, b.po_number as PO_NUMBER, c.id as MST_ID, c.booking_no as WO_NUMBER, e.item_category_id as ITEM_CATEGORY, d.id as DTLS_ID, g.cons as WO_QNTY, d.rate as WO_RATE, g.amount as WO_AMOUNT, 0 as NUMBER_OF_LOT, d.trim_group as ITEM_GROUP_ID, d.pre_cost_fabric_cost_dtls_id as PRE_COST_FABRIC_COST_DTLS_ID, e.id as PI_ID, e.pi_number as PI_NUMBER, e.goods_rcv_status as GOODS_RCV_STATUS, f.id as PI_DTLS_ID, f.uom as UOM, f.quantity as PI_QUANTITY, f.rate as PI_RATE, f.amount as PI_AMOUNT, f.yarn_type as YARN_TYPE, f.count_name as YARN_COUNT, f.color_id as COLOR_NAME, f.yarn_composition_item1 as YARN_COMP_TYPE1ST, f.fabric_construction as FABRIC_CONSTRUCTION,f.fabric_composition as FABRIC_COMPOSITION, f.gsm as GSM ,f.dia_width as DIA_WIDTH, f.yarn_composition_percentage1 as PERCENTAGE, e.importer_id
	from wo_po_details_master a, wo_po_break_down b, wo_booking_mst c, wo_booking_dtls d, com_pi_master_details e, com_pi_item_details f, wo_trim_book_con_dtls g 
	where a.job_no=d.job_no and b.id=d.po_break_down_id and c.booking_no=d.booking_no and c.booking_type in(1,2,6) and f.work_order_dtls_id=d.id and e.id=f.pi_id and e.pi_basis_id=1 and e.item_category_id=4 and d.id=g.wo_trim_booking_dtls_id and f.color_id=g.color_number_id and f.item_color=g.item_color and f.size_id=g.gmts_sizes and f.item_size=g.item_size and f.item_description=g.description and f.brand_supplier=g.brand_supplier and g.cons>0 and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 and f.status_active=1 and g.status_active=1 $sql_cond $po_cond $sql_cond_2 $wo_no_cond_3 $wo_date_cond 
	order by ITEM_CATEGORY,JOB_ID,MST_ID,PI_ID";
	// echo $sql;die;

	$sql_result=sql_select($sql);
	if(count($sql_result)<1){echo "<h1>No Data Found</h1>";die;}
	// var_dump($sql_result);
	$wo_mst_id_check=array();$pi_mst_id_check=array();$pi_knit_check=array();$pi_acc_check=array();
	$all_data_arr=array();
	$all_pi_id=$all_job_id='';
	foreach($sql_result as $row)
	{
		if($row["ITEM_CATEGORY"]==1 || $row["ITEM_CATEGORY"]==24)
		{
			$key=$row["JOB_ID"].'__'.$row["MST_ID"].'__'.$row["PI_ID"].'__'.$row["YARN_TYPE"].'__'.$row["YARN_COUNT"].'__'.$row["YARN_COMP_TYPE1ST"].'__'.$row["COLOR_NAME"];
		}
		else if($row["ITEM_CATEGORY"]==2)
		{
			$key=$row["JOB_ID"].'__'.$row["MST_ID"].'__'.$row["PI_ID"].'__'.$row["COLOR_NAME"].'__'.$row["FABRIC_CONSTRUCTION"].'__'.$row["FABRIC_COMPOSITION"].'__'.$row["GSM"].'__'.$row["DIA_WIDTH"];
		}
		else if($row["ITEM_CATEGORY"]==3)
		{
			$key=$row["JOB_ID"].'__'.$row["MST_ID"].'__'.$row["PI_ID"].'__'.$row["FABRIC_CONSTRUCTION"].'__'.$row["FABRIC_COMPOSITION"].'__'.$row["GSM"].'__'.$row["DIA_WIDTH"];
		}
		else if($row["ITEM_CATEGORY"]==4)
		{
			$key=$row["JOB_ID"].'__'.$row["MST_ID"].'__'.$row["PI_ID"].'__'.$row["ITEM_GROUP_ID"];
		}
		else
		{
			$key=$row["JOB_ID"].'__'.$row["MST_ID"].'__'.$row["PI_ID"];
		}

		$all_data_arr[$row["ITEM_CATEGORY"]][$key]["job_id"]=$row["JOB_ID"];
		$all_data_arr[$row["ITEM_CATEGORY"]][$key]["importer_id"]=$row["IMPORTER_ID"];
		$all_data_arr[$row["ITEM_CATEGORY"]][$key]["item_category"]=$row["ITEM_CATEGORY"];
		$all_data_arr[$row["ITEM_CATEGORY"]][$key]["job_no"]=$row["JOB_NO"];
		$all_data_arr[$row["ITEM_CATEGORY"]][$key]["buyer_id"]=$row["BUYER_ID"];
		$all_data_arr[$row["ITEM_CATEGORY"]][$key]["style_ref_no"]=$row["STYLE_REF_NO"];
		$all_data_arr[$row["ITEM_CATEGORY"]][$key]["po_number"].=$row["PO_NUMBER"].', ';

		$all_data_arr[$row["ITEM_CATEGORY"]][$key]["wo_id"]=$row["MST_ID"];
		$all_data_arr[$row["ITEM_CATEGORY"]][$key]["wo_dtls_id"]=$row["DTLS_ID"];
		$all_data_arr[$row["ITEM_CATEGORY"]][$key]["wo_number"]=$row["WO_NUMBER"];
		$all_data_arr[$row["ITEM_CATEGORY"]][$key]["number_of_lot"]=$row["NUMBER_OF_LOT"];
		$all_data_arr[$row["ITEM_CATEGORY"]][$key]["item_group_id"]=$row["ITEM_GROUP_ID"];
		$all_data_arr[$row["ITEM_CATEGORY"]][$key]["pre_cost_cost_dtls_id"]=$row["PRE_COST_FABRIC_COST_DTLS_ID"];
		$all_data_arr[$row["ITEM_CATEGORY"]][$key]["wo_qnty"]+=$row["WO_QNTY"];
		$all_data_arr[$row["ITEM_CATEGORY"]][$key]["wo_rate"]=$row["WO_RATE"];
		$all_data_arr[$row["ITEM_CATEGORY"]][$key]["wo_amount"]+=$row["WO_AMOUNT"];

		$all_data_arr[$row["ITEM_CATEGORY"]][$key]["yarn_type"]=$row["YARN_TYPE"];
		$all_data_arr[$row["ITEM_CATEGORY"]][$key]["yarn_count"]=$row["YARN_COUNT"];
		$all_data_arr[$row["ITEM_CATEGORY"]][$key]["color_name"]=$row["COLOR_NAME"];
		$all_data_arr[$row["ITEM_CATEGORY"]][$key]["yarn_comp_type1st"]=$row["YARN_COMP_TYPE1ST"];
		$all_data_arr[$row["ITEM_CATEGORY"]][$key]["fabric_construction"]=$row["FABRIC_CONSTRUCTION"];
		$all_data_arr[$row["ITEM_CATEGORY"]][$key]["fabric_composition"]=$row["FABRIC_COMPOSITION"];
		$all_data_arr[$row["ITEM_CATEGORY"]][$key]["gsm"]=$row["GSM"];
		$all_data_arr[$row["ITEM_CATEGORY"]][$key]["dia_width"]=$row["DIA_WIDTH"];
		$all_data_arr[$row["ITEM_CATEGORY"]][$key]["percentage"]=$row["PERCENTAGE"];
		$all_data_arr[$row["ITEM_CATEGORY"]][$key]["pi_id"]=$row["PI_ID"];
		$all_data_arr[$row["ITEM_CATEGORY"]][$key]["pi_dtls_id"]=$row["PI_DTLS_ID"];
		$all_data_arr[$row["ITEM_CATEGORY"]][$key]["pi_number"]=$row["PI_NUMBER"];
		$all_data_arr[$row["ITEM_CATEGORY"]][$key]["po_id"]=$row["PO_ID"];
		$all_data_arr[$row["ITEM_CATEGORY"]][$key]["goods_rcv_status"]=$row["GOODS_RCV_STATUS"];
		$all_data_arr[$row["ITEM_CATEGORY"]][$key]["pi_uom"]=$row["UOM"];
		$all_data_arr[$row["ITEM_CATEGORY"]][$key]["pi_rate"]=$row["PI_RATE"];
		$all_data_arr[$row["ITEM_CATEGORY"]][$key]["pi_quantity"]+=$row["PI_QUANTITY"];

		if($row["ITEM_CATEGORY"]==2 || ($row["ITEM_CATEGORY"]==4 && $row["GOODS_RCV_STATUS"]==2))
		{
			if($pi_check[$row["ITEM_CATEGORY"]][$key]=="")
			{
				$pi_check[$row["ITEM_CATEGORY"]][$key]=$key;
				// $all_data_arr[$row["ITEM_CATEGORY"]][$key]["pi_quantity"]+=$row["PI_QUANTITY"];
				$all_data_arr[$row["ITEM_CATEGORY"]][$key]["pi_amount"]=$row["PI_AMOUNT"];
			}
		}
		else
		{
			// $all_data_arr[$row["ITEM_CATEGORY"]][$key]["pi_quantity"]+=$row["PI_QUANTITY"];
			$all_data_arr[$row["ITEM_CATEGORY"]][$key]["pi_amount"]+=$row["PI_AMOUNT"];
		}
		
		if($wo_mst_id_check[$row["ITEM_CATEGORY"]][$key]=="")
		{
			$wo_mst_id_check[$row["ITEM_CATEGORY"]][$key]=$row["MST_ID"];
			$wo_count[$row["JOB_ID"]][$row["MST_ID"]]++;
		}
		if($pi_mst_id_check[$row["ITEM_CATEGORY"]][$key]=="")
		{
			$pi_mst_id_check[$row["ITEM_CATEGORY"]][$key]=$row["PI_ID"];
			$pi_count[$row["JOB_ID"]][$row["PI_ID"]]++;
		}
		if($row["GOODS_RCV_STATUS"]==1)
		{
			$all_wo_id.=$row["MST_ID"].',';
			$all_wo_dtls_id.=$row["DTLS_ID"].',';
		}
		else
		{
			$all_pi_id.=$row["PI_ID"].',';
			$all_pi_dtls_id.=$row["PI_DTLS_ID"].',';
		}
		$all_job_id.=$row["JOB_ID"].',';
	}
	unset($sql_result);
	$all_job_id=implode(",",array_unique(explode(",",chop($all_job_id,','))));
	$all_pi_id=implode(",",array_unique(explode(",",chop($all_pi_id,','))));
	$all_pi_dtls_id=implode(",",array_unique(explode(",",chop($all_pi_dtls_id,','))));
	$all_wo_id=implode(",",array_unique(explode(",",chop($all_wo_id,','))));
	$all_wo_dtls_id=implode(",",array_unique(explode(",",chop($all_wo_dtls_id,','))));
	// var_dump($all_data_arr);die;
	// echo $all_wo_dtls_id.'**';die;
	$job_ids=count(explode(",",$all_job_id));
	if($job_ids>1000)
	{
		$jobcond_in=" and (";
		$jobIdsArr=array_chunk(explode(",",$all_job_id),999);
		foreach($jobIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$jobcond_in.=" a.job_id in($ids) or"; 
		}
		$jobcond_in=chop($jobcond_in,'or ');
		$jobcond_in.=")";
	}
	else
	{ 
		$jobcond_in=" and a.job_id in($all_job_id)";
	}
	$trim_cost_dtls_sql="SELECT a.job_id as JOB_ID,a.trim_group as TRIM_GROUP,a.cons_uom as CONS_UOM, a.description as DESCRIPTION, a.rate as RATE, b.color_number_id as COLOR_NUMBER_ID from wo_pre_cost_trim_cost_dtls a ,wo_pre_cost_trim_co_cons_dtls b where a.id=b.wo_pre_cost_trim_cost_dtls_id $jobcond_in and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.job_id,a.trim_group,a.cons_uom, a.description, a.rate, b.color_number_id ";
	// echo $trim_cost_dtls_sql;die;
	$trim_cost_dtls_result=sql_select($trim_cost_dtls_sql);
	$trim_cost_dtls_arr=array();
	foreach($trim_cost_dtls_result as $row)
	{
		$key=$row['JOB_ID'].'_'.$row['TRIM_GROUP'];
		$trim_cost_dtls_arr[$key]['cons_uom']=$row['CONS_UOM'];
		$trim_cost_dtls_arr[$key]['description']=$row['DESCRIPTION'];
		$trim_cost_dtls_arr[$key]['rate']=$row['RATE'];
		$trim_cost_dtls_arr[$key]['garments_color'].=$color_library[$row['COLOR_NUMBER_ID']].', ';
	}
	unset($trim_cost_dtls_result);

	$emb_cost_dtls_sql="SELECT a.id as ID, a.job_id as JOB_ID, a.emb_name as EMB_NAME,a.emb_type as EMB_TYPE,a.body_part_id as BODY_PART_ID, a.rate as RATE from wo_pre_cost_embe_cost_dtls a  where a.status_active=1 and a.is_deleted=0 $jobcond_in";
	// echo $emb_cost_dtls_sql;die;
	$emb_cost_dtls_result=sql_select($emb_cost_dtls_sql);
	$emb_cost_dtls_arr=array();
	foreach($emb_cost_dtls_result as $row)
	{
		$key=$row['JOB_ID'].'_'.$row['ID'];
		$emb_cost_dtls_arr[$key]['emb_name']=$row['EMB_NAME'];
		$emb_cost_dtls_arr[$key]['emb_type']=$row['EMB_TYPE'];
		$emb_cost_dtls_arr[$key]['body_part_id']=$row['BODY_PART_ID'];
		$emb_cost_dtls_arr[$key]['rate']=$row['RATE'];
	}
	unset($emb_cost_dtls_result);

	$yarn_cost_dtls_sql="SELECT a.job_id as JOB_ID, a.id as ID, a.count_id as COUNT_ID,a.type_id as TYPE_ID,a.copm_one_id as COPM_ONE_ID, a.cons_qnty as CONS_QNTY, a.rate as RATE,a.amount as AMOUNT from wo_pre_cost_fab_yarn_cost_dtls a  where a.status_active=1 and a.is_deleted=0 $jobcond_in";
	// echo $yarn_cost_dtls_sql;die;
	$yarn_cost_dtls_result=sql_select($yarn_cost_dtls_sql);
	$yarn_cost_dtls_arr=array();
	foreach($yarn_cost_dtls_result as $row)
	{
		$key=$row['JOB_ID'].'_'.$row['TYPE_ID'].'_'.$row['COUNT_ID'].'_'.$row['COPM_ONE_ID'];
		$yarn_cost_dtls_arr[$key]['cons_qnty']=$row['CONS_QNTY'];
		$yarn_cost_dtls_arr[$key]['rate']=$row['RATE'];
		$yarn_cost_dtls_arr[$key]['amount']=$row['AMOUNT'];
	}
	unset($yarn_cost_dtls_result);
	
	$fabric_cost_dtls_sql="SELECT a.id as ID, a.job_id as JOB_ID, a.color_type_id as COLOR_TYPE_ID, a.rate as RATE,a.construction as CONSTRUCTION, a.composition as COMPOSITION,a.gsm_weight as GSM_WEIGHT, b.color_number_id as COLOR_NUMBER_ID, b.dia_width as DIA_WIDTH from wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b where a.id=b.pre_cost_fabric_cost_dtls_id $jobcond_in and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id,a.job_id, a.color_type_id, a.rate ,a.construction , a.composition,a.gsm_weight , b.color_number_id , b.dia_width ";
	// echo $fabric_cost_dtls_sql;die;
	$fabric_cost_dtls_result=sql_select($fabric_cost_dtls_sql);
	$fabric_cost_dtls_arr=array();
	foreach($fabric_cost_dtls_result as $row)
	{
		// $fabric_cost_dtls_arr[$row['CONSTRUCTION']][$row['COMPOSITION']][$row['GSM_WEIGHT']][$row['DIA_WIDTH']]['rate']=$row['RATE'];
		// $fabric_cost_dtls_arr[$row['CONSTRUCTION']][$row['COMPOSITION']][$row['GSM_WEIGHT']][$row['DIA_WIDTH']]['color_type_id']=$color_type[$row['COLOR_TYPE_ID']];
		// $fabric_cost_dtls_arr[$row['CONSTRUCTION']][$row['COMPOSITION']][$row['GSM_WEIGHT']][$row['DIA_WIDTH']]['COLOR_NUMBER_ID'].=$row['COLOR_NUMBER_ID'].',';
		$key=$row['JOB_ID'].'_'.$row['CONSTRUCTION'].'_'.$row['COMPOSITION'].'_'.$row['DIA_WIDTH'];
		$fabric_cost_dtls_arr[$key]['rate']=$row['RATE'];
		$fabric_cost_dtls_arr[$key]['color_type_id']=$color_type[$row['COLOR_TYPE_ID']];
		$fabric_cost_dtls_arr[$key]['COLOR_NUMBER_ID'].=$row['COLOR_NUMBER_ID'].',';
	}
	unset($fabric_cost_dtls_result);

	$conversion_cost_dtls_sql="SELECT a.id as ID,a.job_no as JOB_NO, a.charge_unit as RATE from wo_pre_cost_fab_conv_cost_dtls a where  cons_process=30 $jobcond_in and a.status_active=1 and a.is_deleted=0";
	// echo $conversion_cost_dtls_sql;die;
	$conversion_cost_dtls_result=sql_select($conversion_cost_dtls_sql);
	$conversion_cost_dtls_arr=array();
	foreach($conversion_cost_dtls_result as $row)
	{
		$conversion_cost_dtls_arr[$row['JOB_NO']]['rate']=$row['RATE'];
	}
	unset($conversion_cost_dtls_result);
	// var_dump($all_data_arr);die;

	$condition= new condition();     

	if($all_job_id!='')
	{
		$condition->jobid_in("$all_job_id");
	}
	$condition->company_name("=$cbo_company_name");

	$condition->init();
	// var_dump($all_data_arr);die;
	$trims= new trims($condition);
	// echo $trims->getQuery();die;
	$trim_qty= $trims->getQtyArray_by_jobAndItemid(); 
	$trim_amount=$trims->getAmountArray_by_jobAndItemid();
	// var_dump($trim_qty['FAL-21-01362']);die;
	$emblishment= new emblishment($condition);
	// echo $emblishment->getQuery();die;
	$emb_qty = $emblishment->getQtyArray_by_jobAndEmbname();
	$emb_amount = $emblishment->getAmountArray_by_jobAndEmbname();
	// var_dump($emb_amount['FAL-21-01361']);die;
	$wash= new wash($condition);
	$wash_qty=$wash->getQtyArray_by_jobAndEmbname();
	$wash_amount=$wash->getAmountArray_by_jobAndEmbname();
	// var_dump($wash_qty['FAL-21-01361']);die;
	$yarn= new yarn($condition);
	// $yarn_qtyAndAmount=$yarn->getJobCountCompositionColorAndTypeWiseYarnQtyAndAmountArray();
	$yarn_qtyAndAmount=$yarn->getJobCountCompositionPercentAndTypeWiseYarnQtyAndAmountArray();
	// var_dump($yarn_qtyAndAmount['FAL-21-01361'][10][1][100][2]);die;
	$fabric= new fabric($condition);
	$fabric_qty= $fabric->getQtyArray_by_job_knitAndwoven_greyAndfinish_purchase();
	$fabric_amount= $fabric->getAmountArray_by_job_knitAndwoven_greyAndfinish();
	// $fabric_qty= $fabric->getQtyArray_by_orderAndGmtscolor_knitAndwoven_greyAndfinish_purchase();
	// var_dump($fabricQtyByFabricSource['knit']['grey']['FAL-21-01361']);die;
	// var_dump($fabricQtyByFabricSource['knit']['finish']['FAL-21-01361']);die;
	$conversion= new conversion($condition);
	$conv_qty=$conversion->getQtyArray_by_JobAndProcess();
	$conv_amount=$conversion->getAmountArray_by_JobAndProcess();
	// var_dump($conv_qty['FAL-21-01361'][30]);die;

	$receive_qty_sql="";
	if($all_pi_id!="")
	{
		$receive_qty_sql = "SELECT a.item_category as ITEM_CATEGORY, a.receive_purpose as RECEIVE_PURPOSE, a.booking_id as BOOKING_ID, b.pi_wo_req_dtls_id as PI_WO_DTLS_ID, sum(b.order_qnty) as RECEIVE_QNTY, c.item_group_id as ITEM_GROUP_ID, c.yarn_count_id as YARN_COUNT_ID, c.yarn_comp_type1st as YARN_COMP_TYPE1ST,c.yarn_type as YARN_TYPE,c.color as COLOR,c.gsm as GSM,c.dia_width as DIA_WIDTH, c.item_description as ITEM_DESCRIPTION, null as SAVE_STRING, 2 as TYPE
		from inv_receive_master a, inv_transaction b, product_details_master c
		where a.id=b.mst_id and b.prod_id=c.id and a.receive_basis=1 and a.booking_id in ($all_pi_id) and b.pi_wo_req_dtls_id in($all_pi_dtls_id) and b.transaction_type=1 and a.item_category=1 and a.receive_purpose=16 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.item_category, a.receive_purpose,c.item_group_id, a.booking_id, b.pi_wo_req_dtls_id, c.yarn_count_id, c.yarn_comp_type1st,c.yarn_type,c.color,c.gsm,c.dia_width, c.item_description
		union all
		SELECT a.item_category as ITEM_CATEGORY, a.receive_purpose as RECEIVE_PURPOSE, a.booking_id as BOOKING_ID, b.pi_wo_req_dtls_id as PI_WO_DTLS_ID, sum(b.order_qnty) as RECEIVE_QNTY, c.item_group_id as ITEM_GROUP_ID, c.yarn_count_id as YARN_COUNT_ID, c.yarn_comp_type1st as YARN_COMP_TYPE1ST,c.yarn_type as YARN_TYPE,c.color as COLOR,c.gsm as GSM,c.dia_width as DIA_WIDTH, c.item_description as ITEM_DESCRIPTION, null as SAVE_STRING, 2 as TYPE
		from inv_receive_master a, inv_transaction b, product_details_master c
		where a.id=b.mst_id and b.prod_id=c.id and a.receive_basis=1 and a.booking_id in ($all_pi_id) and b.transaction_type=1 and a.item_category=1 and a.receive_purpose=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.item_category, a.receive_purpose,c.item_group_id, a.booking_id, b.pi_wo_req_dtls_id, c.yarn_count_id, c.yarn_comp_type1st,c.yarn_type,c.color,c.gsm,c.dia_width, c.item_description
		union all
		SELECT a.item_category as ITEM_CATEGORY, a.receive_purpose as RECEIVE_PURPOSE, a.booking_id as BOOKING_ID, b.pi_wo_dtls_id as PI_WO_DTLS_ID, sum(b.order_qnty) as RECEIVE_QNTY, c.item_group_id as ITEM_GROUP_ID, c.yarn_count_id as YARN_COUNT_ID, c.yarn_comp_type1st as YARN_COMP_TYPE1ST,c.yarn_type as YARN_TYPE,c.color as COLOR,c.gsm as GSM,c.dia_width as DIA_WIDTH, c.item_description as ITEM_DESCRIPTION, null as SAVE_STRING, 2 as TYPE
		from inv_receive_master a, inv_transaction b, product_details_master c
		where a.id=b.mst_id and b.prod_id=c.id and a.receive_basis=1 and a.booking_id in ($all_pi_id) and b.pi_wo_dtls_id in($all_pi_dtls_id) and b.transaction_type=1 and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.item_category, a.receive_purpose,c.item_group_id, a.booking_id, b.pi_wo_dtls_id, c.yarn_count_id, c.yarn_comp_type1st,c.yarn_type,c.color,c.gsm,c.dia_width, c.item_description
		union all
		SELECT a.item_category as ITEM_CATEGORY, a.receive_purpose as RECEIVE_PURPOSE, a.booking_id as BOOKING_ID, b.pi_wo_dtls_id as PI_WO_DTLS_ID, sum(d.receive_qnty) as RECEIVE_QNTY, c.item_group_id as ITEM_GROUP_ID, c.yarn_count_id as YARN_COUNT_ID, c.yarn_comp_type1st as YARN_COMP_TYPE1ST,c.yarn_type as YARN_TYPE,c.color as COLOR,c.gsm as GSM,c.dia_width as DIA_WIDTH, c.item_description as ITEM_DESCRIPTION, d.save_string as SAVE_STRING, 2 as TYPE
		from inv_receive_master a, inv_transaction b, product_details_master c, inv_trims_entry_dtls d
		where a.id=b.mst_id and b.prod_id=c.id and a.id=d.mst_id and b.id=d.trans_id and a.receive_basis=1 and a.booking_id in ($all_pi_id) and b.transaction_type=1 and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by a.item_category, a.receive_purpose,c.item_group_id, a.booking_id, b.pi_wo_dtls_id, c.yarn_count_id, c.yarn_comp_type1st,c.yarn_type,c.color,c.gsm,c.dia_width, c.item_description, d.save_string";
	}
	if($all_wo_id!="")
	{
		if($receive_qty_sql!=""){$receive_qty_sql.=" union all ";}
		$receive_qty_sql .= "SELECT a.item_category as ITEM_CATEGORY, a.receive_purpose as RECEIVE_PURPOSE, a.booking_id as BOOKING_ID, b.pi_wo_req_dtls_id as PI_WO_DTLS_ID, sum(b.order_qnty) as RECEIVE_QNTY, c.item_group_id as ITEM_GROUP_ID, c.yarn_count_id as YARN_COUNT_ID, c.yarn_comp_type1st as YARN_COMP_TYPE1ST,c.yarn_type as YARN_TYPE,c.color as COLOR,c.gsm as GSM,c.dia_width as DIA_WIDTH, c.item_description as ITEM_DESCRIPTION, null as SAVE_STRING, 1 as TYPE
		from inv_receive_master a, inv_transaction b, product_details_master c
		where a.id=b.mst_id and b.prod_id=c.id and a.receive_basis=2 and a.booking_id in ($all_wo_id) and b.pi_wo_req_dtls_id in($all_wo_dtls_id) and b.transaction_type=1 and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.item_category, a.receive_purpose ,c.item_group_id, a.booking_id, b.pi_wo_req_dtls_id, c.yarn_count_id,c.yarn_comp_type1st,c.yarn_type,c.color,c.gsm,c.dia_width, c.item_description
		union all
		SELECT a.item_category as ITEM_CATEGORY, a.receive_purpose as RECEIVE_PURPOSE, a.booking_id as BOOKING_ID, b.pi_wo_req_dtls_id as PI_WO_DTLS_ID, sum(b.order_qnty) as RECEIVE_QNTY, c.item_group_id as ITEM_GROUP_ID, c.yarn_count_id as YARN_COUNT_ID, c.yarn_comp_type1st as YARN_COMP_TYPE1ST,c.yarn_type as YARN_TYPE,c.color as COLOR,c.gsm as GSM,c.dia_width as DIA_WIDTH, c.item_description as ITEM_DESCRIPTION, null as SAVE_STRING, 1 as TYPE
		from inv_receive_master a, inv_transaction b, product_details_master c
		where a.id=b.mst_id and b.prod_id=c.id and a.receive_basis=2 and a.booking_id in ($all_wo_id) and b.transaction_type=1 and a.item_category=1 and a.receive_purpose=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.item_category, a.receive_purpose ,c.item_group_id, a.booking_id, b.pi_wo_req_dtls_id, c.yarn_count_id,c.yarn_comp_type1st,c.yarn_type,c.color,c.gsm,c.dia_width, c.item_description
		union all
		SELECT a.item_category as ITEM_CATEGORY, a.receive_purpose as RECEIVE_PURPOSE, a.booking_id as BOOKING_ID, b.pi_wo_dtls_id as PI_WO_DTLS_ID, sum(b.order_qnty) as RECEIVE_QNTY, c.item_group_id as ITEM_GROUP_ID, c.yarn_count_id as YARN_COUNT_ID, c.yarn_comp_type1st as YARN_COMP_TYPE1ST,c.yarn_type as YARN_TYPE,c.color as COLOR,c.gsm as GSM,c.dia_width as DIA_WIDTH, c.item_description as ITEM_DESCRIPTION, null as SAVE_STRING, 1 as TYPE
		from inv_receive_master a, inv_transaction b, product_details_master c
		where a.id=b.mst_id and b.prod_id=c.id and a.receive_basis=2 and a.booking_id in ($all_wo_id) and b.pi_wo_dtls_id in($all_wo_dtls_id) and b.transaction_type=1 and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.item_category, a.receive_purpose ,c.item_group_id, a.booking_id, b.pi_wo_dtls_id, c.yarn_count_id,c.yarn_comp_type1st,c.yarn_type,c.color,c.gsm,c.dia_width, c.item_description
		union all
		SELECT a.item_category as ITEM_CATEGORY, a.receive_purpose as RECEIVE_PURPOSE, a.booking_id as BOOKING_ID, b.pi_wo_dtls_id as PI_WO_DTLS_ID, sum(d.receive_qnty) as RECEIVE_QNTY, c.item_group_id as ITEM_GROUP_ID, c.yarn_count_id as YARN_COUNT_ID, c.yarn_comp_type1st as YARN_COMP_TYPE1ST,c.yarn_type as YARN_TYPE,c.color as COLOR,c.gsm as GSM,c.dia_width as DIA_WIDTH, c.item_description as ITEM_DESCRIPTION, d.save_string as SAVE_STRING, 1 as TYPE
		from inv_receive_master a, inv_transaction b, product_details_master c, inv_trims_entry_dtls d
		where a.id=b.mst_id and b.prod_id=c.id and a.id=d.mst_id and b.id=d.trans_id and a.receive_basis=2 and a.booking_id in ($all_wo_id) and b.transaction_type=1 and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by a.item_category, a.receive_purpose ,c.item_group_id, a.booking_id, b.pi_wo_dtls_id, c.yarn_count_id,c.yarn_comp_type1st,c.yarn_type,c.color,c.gsm,c.dia_width, c.item_description, d.save_string";
	}

	// echo $receive_qty_sql;die;
	$receive_qty_result=sql_select($receive_qty_sql);
	$receive_qty_data=array();
	foreach($receive_qty_result as $row)
	{
		if($row['ITEM_CATEGORY']==4)
		{
			$order_id_arr=explode(',',$row['SAVE_STRING']);
			foreach($order_id_arr as $orderInfo)
			{
				$orderData=explode('_',$orderInfo);
				$receive_qty_data[$row['TYPE']][$po_job_id_arr[$orderData[0]]][$row['ITEM_CATEGORY']][$row['BOOKING_ID']][$row['ITEM_GROUP_ID']]+= $orderData[1];
			}
		}
		else if($row['ITEM_CATEGORY']==1 && $row['RECEIVE_PURPOSE']==16)
		{
			$receive_qty_data[$row['TYPE']][$row['PI_WO_DTLS_ID']][$row['ITEM_CATEGORY']][$row['BOOKING_ID']][$row['YARN_TYPE']][$row['YARN_COUNT_ID']][$row['YARN_COMP_TYPE1ST']][$row['COLOR']]+= $row['RECEIVE_QNTY'];
		}
		else if($row['ITEM_CATEGORY']==1 && $row['RECEIVE_PURPOSE']==2)
		{
			$receive_qty_data[$row['TYPE']][$row['BOOKING_ID']][24][$row['BOOKING_ID']][$row['YARN_TYPE']][$row['YARN_COUNT_ID']][$row['YARN_COMP_TYPE1ST']][$row['COLOR']]+= $row['RECEIVE_QNTY'];
		}
		else if($row['ITEM_CATEGORY']==2)
		{
			$item_description_arr=explode(', ',$row['ITEM_DESCRIPTION']);
			$receive_qty_data[$row['TYPE']][$row['PI_WO_DTLS_ID']][$row['ITEM_CATEGORY']][$row['BOOKING_ID']][$row['COLOR']][$item_description_arr[0]][$item_description_arr[1]][$row['GSM']][$row['DIA_WIDTH']]+= $row['RECEIVE_QNTY'];
		}
		else if($row["ITEM_CATEGORY"]==3)
		{
			$item_description_arr=explode(', ',$row['ITEM_DESCRIPTION']);
			$receive_qty_data[$row['TYPE']][$row['BOOKING_ID']][$row['ITEM_CATEGORY']][$row['BOOKING_ID']][$item_description_arr[0]][$item_description_arr[1]][$row['GSM']][$row['DIA_WIDTH']]+= $row['RECEIVE_QNTY'];
		}
	}
	unset($receive_qty_result);
	ksort($all_data_arr);
	// var_dump($receive_qty_data);die;
	ob_start();
	?>
	<div style="width:1920px; margin-left:10px">
		<fieldset style="width:100%;">	 
			<table width="1900" cellpadding="0" cellspacing="0" id="caption">
				<tr>
					<td align="center" width="100%" colspan="20" class="form_caption" style="font-size:16px"><strong><? echo $company_arr[$cbo_company_name]; ?></strong></td>
				</tr> 
				<tr>  
					<td align="center" width="100%" colspan="20" class="form_caption" style="font-size:16px"><strong>PI Variance Report</strong></td>
				</tr>  
			</table>
			<br />
			
				<?
				// var_dump($all_data_arr);die;
				$arr_chk=array();$arr_chk1=array();
				
				foreach($all_data_arr as $category_id=>$category_val)
				{
					$i=1;
					$total_budget_amt=$total_wo_amt=$total_pi_amt=0;
					?>
						<table width="1900" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
						<thead>
							<tr>
								<th width="30">SL</th>
								<th width="100">WO</th>
								<th width="100">Buyer</th>
								<th width="100">Style Ref.</th>
								<?
									if($category_id!=1 && $category_id!=24)
									{
										?>
											<th width="120">PO NO</th>
										<?
									}
									if($category_id==4)
									{
										?>
											<th width="100">Item Group</th>
											<th width="100">Item Description</th>
											<!-- <th width="70">Item Color</th> -->
											<th width="100">Garments Color</th>
										<?
									}
									else if($category_id==1)
									{
										?>
											<th width="100">Yarn Type</th>
											<th width="100">Count</th>
											<th width="100">Lot/Brand</th>
											<th width="100">Composition</th>
										<?
									}
									else if($category_id==2 || $category_id==3)
									{
										?>
											<th width="100">Construction</th>
											<th width="100">Composition</th>
											<th width="100">Color Type</th>
											<th width="100">Garments Color</th>
										<?
									}
									else if($category_id==24)
									{
										?>
											<th width="100">Yarn Type</th>
											<th width="100">Count</th>
											<!-- <th width="100">Lot/Brand</th> -->
											<th width="100">Composition</th>
										<?
									}
									else if($category_id==25)
									{
										?>
											<th width="100">Emb Type</th>
											<th width="100">EMB Name</th>
											<th width="100">Body Part</th>
											<!-- <th width="100">Emb Description</th> -->
										<?
									}

									if($category_id==4)
									{
										?>
											<th width="50"> Budget UOM</th>
										<?
									}
									else
									{
										?>
											<th width="50">UOM</th>
										<?
									}
								?>
								
								<th width="80">Budget Qnty</th>
								<th width="80">Budget Rate</th>
								<th width="80">Budget Amount</th>
								<?
									if($category_id==4)
									{
										?>
											<th width="50">WO UOM</th>
										<?
									}
								?>
								<th width="80">WO Qnty</th>
								<th width="80">WO Rate </th>
								<th width="80">WO Amount</th>
								<th width="80">PI NO</th>
								<th width="80">PI Quantity</th>
								<th width="80">PI Rate</th>
								<th width="80">PI Amount</th>
								<th width="80">Budget Varience</th>
								<th >PI Varience</th>
								<?
									if($category_id!=25)
									{
										?>
											<th width="80">Receive</th>	
										<?
									}
								?>
							</tr>
						</thead>
						<tbody>
					<tr>
						<th colspan="52" style="text-align: left !important; color: black" bgcolor="#FFFFCC"><? echo  $item_category[$category_id]; ?> :</th>
					</tr>
					<?
					foreach($category_val as $key=>$val)
					{
						if ($i%2==0)
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";
						$wo_amt=$pi_amt=0;
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>"> 
							<td align="center"><? echo $i; ?></td>
							<?
								if(!in_array($val["job_id"].'*'.$val['wo_id'],$arr_chk))
								{
									$arr_chk[]=$val["job_id"].'*'.$val['wo_id'];
									$rowspan_wo_count= $wo_count[$val["job_id"]][$val['wo_id']];
									?>
									<td align="center" rowspan="<?= $rowspan_wo_count;?>" valign="middle"><?php echo $val['wo_number']; ?></td>
									<?
								}
							?>
							<td align="center"><?php echo $buyer_arr[$val['buyer_id']]; ?></td>
							<td align="center"><?php echo $val['style_ref_no']; ?></td>
							<?
								if($category_id!=1 && $category_id!=24)
								{
									?>
										<td align="center"><?php echo implode(", ",array_unique(explode(", ",chop($val['po_number'],', ')))); ?></td>
									<?
								}
								if($category_id==4)
								{
									$key=$val['job_id'].'_'.$val['item_group_id'];
									?>
										<td align="center"><?php echo $item_library[$val['item_group_id']]; ?></td>
										<td align="center"><?php echo $trim_cost_dtls_arr[$key]['description']; ?></td>
										<!-- <td align="center"><?php echo $val['item_color']; ?></td> -->
										<td align="center"><?php echo rtrim($trim_cost_dtls_arr[$key]['garments_color'],', ');?>
										</td>
									<?
								}
								else if($category_id==1)
								{	
									?>
										<td align="center"><?php echo $yarn_type[$val["yarn_type"]]; ?></td>
										<td align="center"><?php echo $yarn_count_arr[$val["yarn_count"]]; ?></td>
										<td align="center"><?php echo $val["number_of_lot"]; ?></td>
										<td align="center"><?php echo $composition[$val["yarn_comp_type1st"]]; ?></td>
									<?
								}
								else if($category_id==2)
								{
									$key=$val['job_id'].'_'.$val['fabric_construction'].'_'.$val['fabric_composition'].'_'.$val['dia_width'];
									?>
										<td align="center"><?php echo $val['fabric_construction'];?></td>
										<td align="center"><?php echo $val['fabric_composition'];?></td>
										<td align="center"><?php echo $fabric_cost_dtls_arr[$key]['color_type_id'];?></td>
										<td align="center"><?php 
											$color_id_arr=array_unique(explode(",",chop($fabric_cost_dtls_arr[$key]['COLOR_NUMBER_ID'],',')));
											$color_nam='';
											foreach($color_id_arr as $color_id)
											{
												$color_nam.=$color_library[$color_id].', ';
											}
											echo rtrim($color_nam,', ');
										?>
										</td>
									<?
								}
								else if($category_id==3)
								{
									$key=$val['job_id'].'_'.$val['fabric_construction'].'_'.$val['fabric_composition'].'_'.$val['dia_width'];
									?>
										<td align="center"><?php echo $val['fabric_construction'];?></td>
										<td align="center"><?php echo $val['fabric_composition'];?></td>
										<td align="center"><?php echo $fabric_cost_dtls_arr[$key]['color_type_id'];?></td>
										<td align="center"><?php 
											$color_id_arr=array_unique(explode(",",chop($fabric_cost_dtls_arr[$key]['COLOR_NUMBER_ID'],',')));
											$color_nam='';
											foreach($color_id_arr as $color_id)
											{
												$color_nam.=$color_library[$color_id].', ';
											}
											echo rtrim($color_nam,', ');
										?>
										</td>
									<?
								}
								else if($category_id==24)
								{
									?>
										<td align="center"><?php echo $yarn_type[$val['yarn_type']];?></td>
										<td align="center"><?php echo $yarn_count_arr[$val["yarn_count"]]; ?></td>
										<!-- <td align="center"><?php echo $val["number_of_lot"];?></td> -->
										<td align="center"><?php echo $composition[$val['yarn_comp_type1st']]; ?></td>
									<?
								}
								else if($category_id==25)
								{
									$key=$val['job_id'].'_'.$val['pre_cost_cost_dtls_id'];
									?>
										<td align="center"><?php echo $emblishment_name_array[$emb_cost_dtls_arr[$key]['emb_name']]; ?></td>
										<td align="center"><?php echo $type_array[$emb_cost_dtls_arr[$key]['emb_name']][$emb_cost_dtls_arr[$val['pre_cost_cost_dtls_id']]['emb_type']]; ?></td>
										<td align="center"><?php echo $body_part_arr[$emb_cost_dtls_arr[$key]['body_part_id']]; ?></td>
										<!-- <td align="center"><?php ?></td> -->
									<?
								}

								if($category_id==4)
								{
									$key=$val['job_id'].'_'.$val['item_group_id'];
									?>
										<td align="center"><? echo $unit_of_measurement[$trim_cost_dtls_arr[$key]['cons_uom']]; ?></td>
									<?
								}
								else
								{
									?>
										<td align="center"><? echo $unit_of_measurement[$val['pi_uom']]; ?></td>
									<?
								}
							?>
							<td align="right">
								<? 
									if($category_id==4)
									{
										echo number_format($trim_qty[$val['job_no']][$val['item_group_id']],2);
									}
									else if($category_id==1)
									{
										echo number_format($yarn_qtyAndAmount[$val['job_no']][$val["yarn_count"]][$val["yarn_comp_type1st"]][$val["percentage"]][$val["yarn_type"]]['qty'],2);
									}
									else if($category_id==2)
									{
										echo number_format($fabric_qty['knit']['grey'][$val['job_no']][$val['pi_uom']],2);
									}
									else if($category_id==3)
									{
										echo number_format($fabric_qty['woven']['grey'][$val['job_no']][$val['pi_uom']],2);
									}
									else if($category_id==24)
									{
										echo number_format($conv_qty[$val['job_no']][30][$val['pi_uom']],2);
									}
									else if($category_id==25)
									{
										if($emb_cost_dtls_arr[$val['pre_cost_cost_dtls_id']]['emb_name']==3)
										{
											$key=$val['job_id'].'_'.$val['pre_cost_cost_dtls_id'];
											echo number_format($wash_qty[$val['job_no']][$emb_cost_dtls_arr[$key]['emb_name']],2);
										}
										else
										{
											$key=$val['job_id'].'_'.$val['pre_cost_cost_dtls_id'];
											echo number_format($emb_qty[$val['job_no']][$emb_cost_dtls_arr[$key]['emb_name']],2);
										}
									}
								?>
							</td>
							<td align="right">
								<? 
									if($category_id==4)
									{
										$key=$val['job_id'].'_'.$val['item_group_id'];
										echo $trim_cost_dtls_arr[$key]['rate']; 
									}
									else if($category_id==1)
									{
										$key=$val['job_id'].'_'.$val['yarn_type'].'_'.$val['yarn_count'].'_'.$val['yarn_comp_type1st'];
										echo  $yarn_cost_dtls_arr[$key]['rate'];
									}
									else if($category_id==2 || $category_id==3)
									{
										$key=$val['job_id'].'_'.$val['fabric_construction'].'_'.$val['fabric_composition'].'_'.$val['dia_width'];
										echo $fabric_cost_dtls_arr[$key]['rate'];
									}
									else if($category_id==24)
									{
										echo $conversion_cost_dtls_arr[$val['job_no']]['rate'];	
									}
									else if($category_id==25)
									{
										$key=$val['job_id'].'_'.$val['pre_cost_cost_dtls_id'];
										echo $emb_cost_dtls_arr[$key]['rate']; 
									}
									
								?>
							</td>
							<td align="right">
								<? 							
									if($category_id==4)
									{
										echo number_format($trim_amount[$val['job_no']][$val['item_group_id']],2);
										$total_budget_amt+=$trim_amount[$val['job_no']][$val['item_group_id']]; 
									}
									else if($category_id==1)
									{
										echo number_format($yarn_qtyAndAmount[$val['job_no']][$val["yarn_count"]][$val["yarn_comp_type1st"]][$val["percentage"]][$val["yarn_type"]]['amount'],2);
										$total_budget_amt+=$yarn_qtyAndAmount[$val['job_no']][$val["yarn_count"]][$val["yarn_comp_type1st"]][$val["percentage"]][$val["yarn_type"]]['amount'];
									}
									else if($category_id==2)
									{
										echo number_format($fabric_amount['knit']['grey'][$val['job_no']][$val['pi_uom']],2);
										$total_budget_amt+=$fabric_amount['knit']['grey'][$val['job_no']][$val['pi_uom']]; 
									}
									else if($category_id==3)
									{
										echo number_format($fabric_amount['woven']['grey'][$val['job_no']][$val['pi_uom']],2);
										$total_budget_amt+=$fabric_amount['woven']['grey'][$val['job_no']][$val['pi_uom']]; 
									}
									else if($category_id==24)
									{
										echo number_format($conv_amount[$val['job_no']][30][$val['pi_uom']],2);
										$total_budget_amt+=$conv_amount[$val['job_no']][30][$val['pi_uom']];
									}
									else if($category_id==25)
									{
										if($emb_cost_dtls_arr[$val['pre_cost_cost_dtls_id']]['emb_name']==3)
										{
											$key=$val['job_id'].'_'.$val['pre_cost_cost_dtls_id'];
											echo number_format($wash_amount[$val['job_no']][$emb_cost_dtls_arr[$key]['emb_name']],2);$total_budget_amt+=$wash_amount[$val['job_no']][$emb_cost_dtls_arr[$val['pre_cost_cost_dtls_id']]['emb_name']]; 
										}
										else
										{
											$key=$val['job_id'].'_'.$val['pre_cost_cost_dtls_id'];
											echo number_format($emb_amount[$val['job_no']][$emb_cost_dtls_arr[$key]['emb_name']],2);$total_budget_amt+=$emb_amount[$val['job_no']][$emb_cost_dtls_arr[$key]['emb_name']]; 
										} 
									}
									
								?>
							</td>
							<?
								if($category_id==4)
								{
									?>
										<td align="center"><? echo $unit_of_measurement[$val['pi_uom']]; ?></td>
									<?
								}
							?>
							<td align="right"><? echo number_format($val["wo_qnty"],2);?></td>
							<td align="right"><?  echo $val["wo_rate"];?></td>
							<td align="right"><? echo number_format($val["wo_amount"],2);$total_wo_amt+=$val["wo_amount"];
								$wo_amt=number_format($val["wo_amount"],2); ?></td>
							<?
								if(!in_array($val["job_id"].'*'.$val['pi_id'],$arr_chk1))
								{
									$arr_chk1[]=$val["job_id"].'*'.$val['pi_id'];
									$rowspan_pi_count= $pi_count[$val["job_id"]][$val['pi_id']];
									?>
									<td align="center" rowspan="<?= $rowspan_pi_count;?>" valign="middle"><? echo $val['pi_number']; ?></td>
									<?
								}
							?>
							<td align="center"> <a href="##" onClick="openmypage_pi_qty_hyfer_link('<? echo $val['wo_number']; ?>','<? echo $val['po_id']; ?>','<? echo $val['item_group_id']; ?>','<? echo $val['style_ref_no']; ?>',<? echo $val['item_category']; ?>,'pi_qty_popup');"  > <p><? echo number_format($val["pi_quantity"],2); ?>&nbsp;</p></a></td>
							<td align="right"><p><? echo $val["pi_rate"]; ?>&nbsp;</p></td>
							<td align="right"><p><? echo number_format($val["pi_amount"],2);$total_pi_amt+=$val["pi_amount"];
								$pi_amt=number_format($val["pi_amount"],2); ?>&nbsp;</p></td>
							<td align="right">
								<? 
									if($category_id==4)
									{
										echo number_format($trim_amount[$val['job_no']][$val['item_group_id']]-$val["pi_amount"],2); 
									}
									else if($category_id==1)
									{
										echo number_format($yarn_qtyAndAmount[$val['job_no']][$val["yarn_count"]][$val["yarn_comp_type1st"]][$val["percentage"]][$val["yarn_type"]]['amount']-$val["pi_amount"],2);
									}
									else if($category_id==2)
									{
										echo number_format($fabric_amount['knit']['grey'][$val['job_no']][$val['pi_uom']]-$val["pi_amount"],2); 
									}
									else if($category_id==3)
									{
										echo number_format($fabric_amount['woven']['grey'][$val['job_no']][$val['pi_uom']]-$val["pi_amount"],2); 
									}
									else if($category_id==24)
									{
										echo number_format($conv_amount[$val['job_no']][30][$val['pi_uom']]-$val["pi_amount"],2);
									}
									else if($category_id==25)
									{
										if($emb_cost_dtls_arr[$val['pre_cost_cost_dtls_id']]['emb_name']==3)
										{
											$key=$val['job_id'].'_'.$val['pre_cost_cost_dtls_id'];
											echo number_format($wash_amount[$val['job_no']][$emb_cost_dtls_arr[$key]['emb_name']]-$val["pi_amount"],2);
										}
										else
										{
											$key=$val['job_id'].'_'.$val['pre_cost_cost_dtls_id'];
											echo number_format($emb_amount[$val['job_no']][$emb_cost_dtls_arr[$key]['emb_name']]-$val["pi_amount"],2);
										} 
									}
									
								?>
							</td>
							<td align="right"><? echo number_format($wo_amt-$pi_amt,2); ?></td>
							<?
								if($category_id!=25)
								{
									?>
										<td align="right">
											<? 
												if($category_id==4)
												{
													if($val["goods_rcv_status"]==1)
													{
														echo $receive_qty_data[$val['goods_rcv_status']][$val['job_id']][$category_id][$val["wo_id"]][$val['item_group_id']];
													}
													else
													{
														echo $receive_qty_data[$val['goods_rcv_status']][$val['job_id']][$category_id][$val["pi_id"]][$val['item_group_id']];
													}
												}
												else if($category_id==1)
												{
													if($val["goods_rcv_status"]==1)
													{
														echo $receive_qty_data[$val['goods_rcv_status']][$val['wo_dtls_id']][$category_id][$val["wo_id"]][$val['yarn_type']][$val['yarn_count']][$val['yarn_comp_type1st']][$val['color_name']];
													}
													else
													{
														echo $receive_qty_data[$val['goods_rcv_status']][$val['pi_dtls_id']][$category_id][$val["pi_id"]][$val['yarn_type']][$val['yarn_count']][$val['yarn_comp_type1st']][$val['color_name']];
													}
												}
												else if($category_id==2)
												{
													if($val["goods_rcv_status"]==1)
													{
														echo $receive_qty_data[$val['goods_rcv_status']][$val['wo_dtls_id']][$category_id][$val["wo_id"]][$val['color_name']][$val['fabric_construction']][$val['fabric_composition']][$val['gsm']][$val['dia_width']];
													}
													else
													{
														echo $receive_qty_data[$val['goods_rcv_status']][$val['pi_dtls_id']][$category_id][$val["pi_id"]][$val['color_name']][$val['fabric_construction']][$val['fabric_composition']][$val['gsm']][$val['dia_width']];
													}
												}
												else if($category_id==24)
												{	
													if($val["goods_rcv_status"]==1)
													{
														
														echo $receive_qty_data[$val['goods_rcv_status']][$val['wo_id']][$category_id][$val["wo_id"]][$val['yarn_type']][$val['yarn_count']][$val['yarn_comp_type1st']][$val['color_name']];
													}
													else
													{
														echo $receive_qty_data[$val['goods_rcv_status']][$val['pi_id']][$category_id][$val["pi_id"]][$val['yarn_type']][$val['yarn_count']][$val['yarn_comp_type1st']][$val['color_name']];
													}
												}
												
											?>
										</td>
									<?
								}
							?>


						</tr>
						<?
						$i++;
					}					
					?>
					<tr bgcolor="#CCCCCC">
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<?
							if($category_id!=1 && $category_id!=24)
							{
								?>
									<td></td>
								<?
							}
							if($category_id==4)
							{
								?>
									<td ></td>
									<td ></td>
									<!-- <td ></td> -->
									<td ></td>
								<?
							}
							else if($category_id==1)
							{
								?>
									<td ></td>
									<td ></td>
									<td ></td>
									<td ></td>
								<?
							}
							else if($category_id==2 || $category_id==3)
							{
								?>
									<td ></td>
									<td ></td>
									<td ></td>
									<td ></td>
								<?
							}
							else if($category_id==24)
							{
								?>
									<td ></td>
									<td ></td>
									<!-- <td ></td> -->
									<td ></td>
								<?
							}
							else if($category_id==25)
							{
								?>
									<td ></td>
									<td ></td>
									<td ></td>
									<!-- <td ></td> -->
								<?
							}
						?>
						<td></td>
						<td></td>
						<td align="right"><strong>Total: &nbsp;</strong></td>
						<td align="right"><strong><? echo number_format($total_budget_amt,2);?></strong></td>
						<?
							if($category_id==4)
							{
								?>
									<td ></td>
								<?
							}
						?>
						<td></td>
						<td></td>
						<td align="right"><strong><? echo number_format($total_wo_amt,2);?></strong></td>
						<td></td>
						<td></td>
						<td></td>
						<td align="right"><strong><? echo number_format($total_pi_amt,2);?></strong></td>
						<td></td>
						<td></td>
						<?
							if($category_id!=25)
							{
								?>
									<td></td>	
								<?
							}
						?>
					</tr>
					<br>
					<?
				}
				?>
				</tbody>	
			</table>
		</fieldset>
	</div>
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
	echo "$total_data****$filename";
	exit();
}

if($action=="pi_qty_popup")
{
	echo load_html_head_contents("", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	// echo $item_catagory."WASH";die;
	?>   
	<fieldset style="width:1610px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="1610
			" cellpadding="0" cellspacing="0" >
				<!-- <caption><strong></strong> </caption> -->
                <thead>
                    <th width="30">Sl</th>
                    <th width="130">WO</th>
                    <th width="80">WO Type</th>
                    <th width="80">Buyer</th>
                    <th width="80">PO Number</th>
                    <th width="80">Style Ref.</th>
                    <th width="80">HS Code</th>
                    <th width="80">Item Group.</th>
                    <th width="80">Item Description</th>
                    <th width="80">Gmts Color</th>
                    <th  width="80">Gmts Size</th>
                    <th  width="80">Item Color</th>
                    <th  width="80">Item Size</th>
                    <th  width="80">UOM</th>
                    <th  width="80">WO Qnty</th>
                    <th  width="80">WO Rate</th>
                    <th  width="80">WO Amount</th>
                    <th  width="80">Pi Quantity</th>
                    <th  width="80">Rate</th>
                    <th>Amount</th>
				</thead>
			</table>
                <?
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
					$line_arr=return_library_array( "select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1", "id", "line_name");
					$floor_arr=return_library_array( "select id,floor_name from lib_prod_floor where is_deleted=0 and status_active=1", "id", "floor_name");
					$po_arr=return_library_array( "select id,po_number from wo_po_break_down where is_deleted=0 and status_active=1", "id", "po_number");
					$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
					$color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
					$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
					$body_part_arr = return_library_array("select id,body_part_full_name from lib_body_part where status_active=1 and is_deleted=0", "id", "body_part_full_name");
					$type_array=array(0=>$blank_array,1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type,6=>$blank_array,99=>$blank_array);
					$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id","yarn_count");
					$buyer_arr = return_library_array("select id,buyer_name from  lib_buyer ","id","buyer_name");
					$po_job_id_arr = return_library_array("select id,job_id from wo_po_break_down ","id","job_id");

			    	$trim_cost_dtls_sql="SELECT a.job_id as JOB_ID,a.trim_group as TRIM_GROUP,a.cons_uom as CONS_UOM, a.description as DESCRIPTION, a.rate as RATE, b.color_number_id as COLOR_NUMBER_ID,b.ITEM_SIZE from wo_pre_cost_trim_cost_dtls a ,wo_pre_cost_trim_co_cons_dtls b where a.id=b.wo_pre_cost_trim_cost_dtls_id and a.trim_group=$item_group and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.job_id,a.trim_group,a.cons_uom, a.description, a.rate, b.color_number_id,b.item_size";
					$trims_cost_dtls_array=array();
	                $trim_cost_dtls_result=sql_select($trim_cost_dtls_sql);
					foreach( $trim_cost_dtls_result as $row){
						$trims_cost_dtls_array[$row['JOB_ID']]['DESCRIPTION']=$row['DESCRIPTION'];
						$trims_cost_dtls_array[$row['JOB_ID']]['COLOR_NUMBER_ID']=$row['COLOR_NUMBER_ID'];
						$trims_cost_dtls_array[$row['JOB_ID']]['ITEM_SIZE']=$row['ITEM_SIZE'];
						$trims_cost_dtls_array[$row['JOB_ID']]['CONS_UOM']=$row['CONS_UOM'];
					}
					// unset($trim_cost_dtls_result);


			if($item_catagory==24){
				$sql="SELECT a.id as JOB_ID,a.job_no as JOB_NO, a.buyer_name as BUYER_ID ,a.style_ref_no as STYLE_REF_NO, null as PO_NUMBER, c.id as MST_ID, c.ydw_no as WO_NUMBER, null as BOOKING_TYPE, e.item_category_id as ITEM_CATEGORY, d.id as DTLS_ID, d.YARN_WO_QTY as  WO_QNTY, d.dyeing_charge as WO_RATE, d.amount as WO_AMOUNT, 0 as NUMBER_OF_LOT, 0 as ITEM_GROUP_ID ,0 as GMTS_SIZE, 0 as PRE_COST_FABRIC_COST_DTLS_ID, e.id as PI_ID, e.pi_number as PI_NUMBER, e.goods_rcv_status as GOODS_RCV_STATUS, f.id as PI_DTLS_ID, f.uom as UOM, f.quantity as PI_QUANTITY, f.rate as PI_RATE, f.amount as PI_AMOUNT, d.yarn_type as YARN_TYPE, d.count as YARN_COUNT, d.yarn_color as COLOR_NAME, d.yarn_comp_type1st as YARN_COMP_TYPE1ST, f.fabric_construction as FABRIC_CONSTRUCTION,f.fabric_composition as FABRIC_COMPOSITION, f.gsm as GSM ,f.dia_width as DIA_WIDTH, f.yarn_composition_percentage1 as PERCENTAGE, e.HS_CODE
				from wo_po_details_master a, wo_yarn_dyeing_mst c, wo_yarn_dyeing_dtls d, com_pi_master_details e, com_pi_item_details f
				where a.job_no=d.job_no and c.id=d.mst_id and f.work_order_dtls_id=d.id and e.id=f.pi_id and e.pi_basis_id=1 and a.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 and f.status_active=1 and c.ydw_no='$txt_job_no' and a.style_ref_no='$txt_style_no' and e.importer_id=$companyID" ;
			}
			else if($item_catagory==1){
				$sql="SELECT a.id as JOB_ID,a.job_no as JOB_NO, a.buyer_name as BUYER_ID ,a.style_ref_no as STYLE_REF_NO, null as PO_NUMBER, c.id as MST_ID, c.wo_number as WO_NUMBER, null as BOOKING_TYPE, e.item_category_id as ITEM_CATEGORY, d.id as DTLS_ID, d.supplier_order_quantity as WO_QNTY, d.rate as WO_RATE, d.amount as WO_AMOUNT, d.number_of_lot as NUMBER_OF_LOT, 0 as ITEM_GROUP_ID ,0 as GMTS_SIZE, 0 as PRE_COST_FABRIC_COST_DTLS_ID, e.id as PI_ID, e.pi_number as PI_NUMBER, e.goods_rcv_status as GOODS_RCV_STATUS, f.id as PI_DTLS_ID, f.uom as UOM, f.quantity as PI_QUANTITY, f.rate as PI_RATE, f.amount as PI_AMOUNT, f.yarn_type as YARN_TYPE, f.count_name as YARN_COUNT, f.color_id as COLOR_NAME, f.yarn_composition_item1 as YARN_COMP_TYPE1ST, f.fabric_construction as FABRIC_CONSTRUCTION,f.fabric_composition as FABRIC_COMPOSITION, f.gsm as GSM ,f.dia_width as DIA_WIDTH, f.yarn_composition_percentage1 as PERCENTAGE, e.HS_CODE
				from wo_po_details_master a, wo_non_order_info_mst c, wo_non_order_info_dtls d, com_pi_master_details e, com_pi_item_details f 
				where a.job_no=d.job_no and c.id=d.mst_id and f.work_order_dtls_id=d.id and e.id=f.pi_id and e.pi_basis_id=1 and c.entry_form=144 and a.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 and f.status_active=1 and c.wo_number='$txt_job_no' and a.style_ref_no='$txt_style_no' and e.importer_id=$companyID";
			}else if($item_catagory==2 || $item_catagory==3){
				$sql="SELECT a.id as JOB_ID,a.job_no as JOB_NO, a.buyer_name as BUYER_ID ,a.style_ref_no as STYLE_REF_NO, b.po_number as PO_NUMBER, c.id as MST_ID, c.booking_no as WO_NUMBER ,c.booking_type as BOOKING_TYPE, e.item_category_id as ITEM_CATEGORY, d.id as DTLS_ID, d.fin_fab_qnty as WO_QNTY, d.rate as WO_RATE,d.amount as WO_AMOUNT, 0 as NUMBER_OF_LOT, d.trim_group as ITEM_GROUP_ID ,d.gmts_size as GMTS_SIZE, d.pre_cost_fabric_cost_dtls_id as PRE_COST_FABRIC_COST_DTLS_ID, e.id as PI_ID, e.pi_number as PI_NUMBER, e.goods_rcv_status as GOODS_RCV_STATUS, f.id as PI_DTLS_ID, f.uom as UOM, f.quantity as PI_QUANTITY, f.rate as PI_RATE, f.amount as PI_AMOUNT, f.yarn_type as YARN_TYPE, f.count_name as YARN_COUNT, f.color_id as COLOR_NAME, f.yarn_composition_item1 as YARN_COMP_TYPE1ST, f.fabric_construction as FABRIC_CONSTRUCTION,f.fabric_composition as FABRIC_COMPOSITION, f.gsm as GSM ,f.dia_width as DIA_WIDTH, f.yarn_composition_percentage1 as PERCENTAGE, e.HS_CODE
				from wo_po_details_master a, wo_po_break_down b, wo_booking_mst c, wo_booking_dtls d, com_pi_master_details e, com_pi_item_details f
				where a.job_no=d.job_no and b.id=d.po_break_down_id and c.booking_no=d.booking_no and c.booking_type in(1,2,6) and f.work_order_id=c.id  and e.id=f.pi_id and e.pi_basis_id=1 and d.fabric_color_id=f.color_id and d.construction=f.fabric_construction and d.copmposition=f.fabric_composition and d.dia_width=f.dia_width and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 and f.status_active=1 and c.booking_no='$txt_job_no' and a.style_ref_no='$txt_style_no' and e.importer_id=$companyID";
			}else if($item_catagory==25){
				$sql="SELECT a.id as JOB_ID,a.job_no as JOB_NO, a.buyer_name as BUYER_ID ,a.style_ref_no as STYLE_REF_NO, b.po_number as PO_NUMBER, c.id as MST_ID, c.booking_no as WO_NUMBER,c.booking_type as BOOKING_TYPE, e.item_category_id as ITEM_CATEGORY, d.id as DTLS_ID, d.fin_fab_qnty as WO_QNTY, d.rate as WO_RATE, d.amount as WO_AMOUNT, 0 as NUMBER_OF_LOT, d.trim_group as ITEM_GROUP_ID,d.gmts_size as GMTS_SIZE, d.pre_cost_fabric_cost_dtls_id as PRE_COST_FABRIC_COST_DTLS_ID, e.id as PI_ID, e.pi_number as PI_NUMBER, e.goods_rcv_status as GOODS_RCV_STATUS, f.id as PI_DTLS_ID, f.uom as UOM, f.quantity as PI_QUANTITY, f.rate as PI_RATE, f.amount as PI_AMOUNT, f.yarn_type as YARN_TYPE, f.count_name as YARN_COUNT, f.color_id as COLOR_NAME, f.yarn_composition_item1 as YARN_COMP_TYPE1ST, f.fabric_construction as FABRIC_CONSTRUCTION,f.fabric_composition as FABRIC_COMPOSITION, f.gsm as GSM ,f.dia_width as DIA_WIDTH, f.yarn_composition_percentage1 as PERCENTAGE, e.HS_CODE
				from wo_po_details_master a, wo_po_break_down b, wo_booking_mst c, wo_booking_dtls d, com_pi_master_details e, com_pi_item_details f
				where a.job_no=d.job_no and b.id=d.po_break_down_id and c.booking_no=d.booking_no and c.booking_type in(1,2,6) and f.work_order_dtls_id=d.id and e.id=f.pi_id and e.pi_basis_id=1 and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 and f.status_active=1 and c.booking_no='$txt_job_no' and a.style_ref_no='$txt_style_no' and a.style_ref_no='$txt_style_no' and e.importer_id=$companyID";
			}else if($item_catagory==4){
				$sql="SELECT a.id as JOB_ID,a.job_no as JOB_NO, a.buyer_name as BUYER_ID ,a.style_ref_no as STYLE_REF_NO, b.po_number as PO_NUMBER, c.id as MST_ID, c.booking_no as WO_NUMBER,c.booking_type as BOOKING_TYPE , e.item_category_id as ITEM_CATEGORY, d.id as DTLS_ID, g.cons as WO_QNTY, d.rate as WO_RATE, g.amount as WO_AMOUNT, 0 as NUMBER_OF_LOT, d.trim_group as ITEM_GROUP_ID,d.gmts_size as GMTS_SIZE, d.pre_cost_fabric_cost_dtls_id as PRE_COST_FABRIC_COST_DTLS_ID, e.id as PI_ID, e.pi_number as PI_NUMBER, e.goods_rcv_status as GOODS_RCV_STATUS, f.id as PI_DTLS_ID, f.uom as UOM, f.quantity as PI_QUANTITY, f.rate as PI_RATE, f.amount as PI_AMOUNT, f.yarn_type as YARN_TYPE, f.count_name as YARN_COUNT, f.color_id as COLOR_NAME, f.yarn_composition_item1 as YARN_COMP_TYPE1ST, f.fabric_construction as FABRIC_CONSTRUCTION,f.fabric_composition as FABRIC_COMPOSITION, f.gsm as GSM ,f.dia_width as DIA_WIDTH, f.yarn_composition_percentage1 as PERCENTAGE, e.HS_CODE
				from wo_po_details_master a, wo_po_break_down b, wo_booking_mst c, wo_booking_dtls d, com_pi_master_details e, com_pi_item_details f, wo_trim_book_con_dtls g 
				where a.job_no=d.job_no and b.id=d.po_break_down_id and c.booking_no=d.booking_no and c.booking_type in(1,2,6) and f.work_order_dtls_id=d.id and e.id=f.pi_id and e.pi_basis_id=1 and d.id=g.wo_trim_booking_dtls_id and f.color_id=g.color_number_id and f.item_color=g.item_color and f.size_id=g.gmts_sizes and f.item_size=g.item_size and f.item_description=g.description and f.brand_supplier=g.brand_supplier and g.cons>0 and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 and f.status_active=1 and g.status_active=1 and c.booking_no='$txt_job_no' and a.style_ref_no='$txt_style_no' and d.trim_group=$item_group and e.importer_id=$companyID
				order by ITEM_CATEGORY,JOB_ID,MST_ID,PI_ID";
			}
			// echo $sql;die;
					//  and b.id in($txt_po_no) 
					$dtlsArray=sql_select($sql);
					?>
					<table border="1" class="rpt_table" rules="all" width="1610" cellpadding="0" cellspacing="0" >
					<tbody>
					<?
					     $i=1;
					    foreach($dtlsArray as $row)
					    {
								if ($i%2==0)
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td style="word-break: break-all;" width="30" align="center"><?=$i?></td>
								<td style="word-break: break-all;" width="130"><?=$row['WO_NUMBER']?></td>
								<td style="word-break: break-all;" width="80"><?=$booking_type[$row['BOOKING_TYPE']]?></td>
								<td style="word-break: break-all;" width="80"><?=$buyer_arr[$row['BUYER_ID']]?></td>
								<td style="word-break: break-all;" width="80" align="center"><?=$row['PO_NUMBER']?></td>
								<td style="word-break: break-all;" width="80" align="center"><?=$row['STYLE_REF_NO']?></td>
								<td style="word-break: break-all;" width="80"><?=$row['HS_CODE']?></td>
								<td style="word-break: break-all;" width="80" align="center"><?=$item_library[$row['ITEM_GROUP_ID']]?></td>
								<td style="word-break: break-all;" width="80"><?=$trims_cost_dtls_array[$row['JOB_ID']]['DESCRIPTION'];?></td>
								<td style="word-break: break-all;" width="80" align="center"><?=$color_library[$trims_cost_dtls_array[$row['JOB_ID']]['COLOR_NUMBER_ID']];?></td>
								<td style="word-break: break-all;" width="80" align="center"><?=$row['GMTS_SIZE'];?></td>
								<td style="word-break: break-all;" width="80" align="center"><?=$row['COLOR_NAME']?></td>
								<td style="word-break: break-all;" width="80"><?=$trims_cost_dtls_array[$row['JOB_ID']]['ITEM_SIZE']?></td>
								<td style="word-break: break-all;" width="80" align="center"><?=$unit_of_measurement[$row['UOM']]?></td>
								<td style="word-break: break-all;" width="80" align="right"><?= number_format($row['WO_QNTY'],4)?></td>
								<td style="word-break: break-all;" width="80" align="right"><?=$row['WO_RATE']?></td>
								<td style="word-break: break-all;" width="80" align="right"><?= number_format($row['WO_AMOUNT'],4)?></td>
								<td style="word-break: break-all;" width="80" align="right"><?= number_format($row['PI_QUANTITY'],4)?></td>
								<td style="word-break: break-all;" width="80" align="right"><?=$row['PI_RATE']?></td>
								<td style="word-break: break-all;" align="right"><?=$row['PI_AMOUNT']?></td>
							</tr>
							<?	
							$i++;
							$wo_qty+=$row['PI_QUANTITY'];
					    }
				?>
                </tbody>
				<tfoot>
					<th colspan="17"></th>
					<th><?= number_format($wo_qty,4)?></th>
					<th></th>
					<th></th>
				</tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}
?>