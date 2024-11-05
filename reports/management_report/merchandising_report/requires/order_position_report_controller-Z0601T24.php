<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Order Position Report.
Functionality	:	
JS Functions	:
Created by		:	Zakaria joy 
Creation date 	: 	13-05-2023
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
include('../../../../includes/class4/class.conditions.php');
include('../../../../includes/class4/class.reports.php');
include('../../../../includes/class4/class.fabrics.php');
include('../../../../includes/class4/class.yarns.php');
include('../../../../includes/class4/class.trims.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
//--------------------------------------------------------------------------------------------------------------------
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
if($db_type==2) $select_date=" to_char(a.insert_date,'YYYY')";
else if ($db_type==0) $select_date=" year(a.insert_date)";

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name   order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
	exit();
}

if ($action=="load_drop_down_team_member")
{
	echo create_drop_down( "cbo_team_member", 110, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active=1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "- Team Member-", $selected, "" ); 
	exit();
}

if($action=="job_no_popup_bk")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data);
	?>	
    <script>
	var selected_id = new Array, selected_name = new Array(); selected_style_name = new Array();
	 
	function toggle( x, origColor ) {
		var newColor = 'yellow';
		if ( x.style ) {
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}
	
	function js_set_value( strcon )
	{
		$('#txt_job_no').val( strcon );
		parent.emailwindow.hide();
	}
		  
	</script>
     <input type="hidden" id="txt_job_no" />
 	<?
	if ($data[0]==0) $company_id=""; else $company_id=" and company_name=$data[0]";
	if ($data[1]==0) $buyer_id=""; else $buyer_id=" and buyer_name=$data[1]";
	//if ($data[2]==0) $year_id=""; else $year_id=" and buyer_name=$data[2]";
	if($db_type==0)
	{
		if(str_replace("'","",$data[2])!=0) $year_cond=" and year(insert_date)=".str_replace("'","",$data[2]).""; else $year_cond="";	
	}
	else
	{
		if(str_replace("'","",$data[2])!=0) $year_cond=" and to_char(insert_date,'YYYY')=".str_replace("'","",$data[2]).""; else $year_cond="";
	}
	
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$teamMemberArr = return_library_array("select id,team_leader_name from lib_marketing_team ","id","team_leader_name");
	
	$sql= "select id, job_no, job_no_prefix_num, style_ref_no, product_dept, dealing_marchant, team_leader from wo_po_details_master where status_active=1 and is_deleted=0 $company_id $buyer_id $year_cond group by id, job_no, job_no_prefix_num, style_ref_no, product_dept, dealing_marchant, team_leader order by id DESC ";
	
	//echo $sql;die;
	
	$arr=array(2=>$product_dept,3=>$marchentrArr,4=>$teamMemberArr);
	echo  create_list_view("list_view", "Job No,Style Ref.,Prod. Dept.,Marchant,Team Name", "100,110,110,150,150","680","360",0, $sql , "js_set_value", "job_no_prefix_num,style_ref_no,id", "", 1, "0,0,product_dept,dealing_marchant,team_leader", $arr , "job_no_prefix_num,style_ref_no,product_dept,dealing_marchant,team_leader", "",'setFilterGrid("list_view",-1);','0,0,0,0,0','',"") ;
	exit();
}
if ($action=="job_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data);

  	?>
    <script>
	var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array(); selected_style = new Array();

	function toggle( x, origColor ) {
		var newColor = 'yellow';
		if ( x.style ) {
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}

	function check_all_data()
	{
		var row_num=$('#list_view tr').length-1;
		for(var i=1;  i<=row_num;  i++)
		{
			if($("#tr_"+i).css("display") != "none")
			{
				$("#tr_"+i).click();
			}
		}
	}

	function js_set_value(id)
	{
		var str_arr=id.split("_");
		toggle( document.getElementById( 'tr_' + str_arr[0] ), '#FFFFFF' );
		var strdt=str_arr[2];
		var str=str_arr[1];
		var strstyle=str_arr[3];
		console.log(strstyle);

		if( jQuery.inArray(  str , selected_id ) == -1 ) {
			selected_id.push( str );
			selected_name.push( strdt );
			selected_style.push( strstyle );
		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == str  ) break;
			}
			selected_id.splice( i, 1 );
			selected_name.splice( i,1 );
			selected_style.splice( i,1 );
		}
		var id = '';
		var ddd='';
		var style='';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
			ddd += selected_name[i] + ',';
			style += selected_style[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );
		ddd = ddd.substr( 0, ddd.length - 1 );
		style = style.substr( 0, style.length - 1 );
		$('#txt_job_id').val( id );
		$('#txt_job_val').val( ddd );
		$('#txt_style').val( style );
	}

	</script>
     <input type="hidden" id="txt_job_id" />
     <input type="hidden" id="txt_job_val" />
     <input type="hidden" id="txt_style" />
     <?
	// echo $data[0];
	 if ($data[0]==0) $company_name=""; else $company_name=" and a.company_name='$data[0]'";
	 if ($data[1]==0) $buyer_name=""; else $buyer_name=" and a.buyer_name in ($data[1])";
	 if ($data[3]==0) $product_dept_cond=""; else $product_dept_cond=" and a.product_dept in ($data[3])";
	 if ($data[2]=="") $job_num=""; else $job_num=" and a.job_no_prefix_num='$data[2]'";
	if($db_type==0)
	{
		if(str_replace("'","",$data[4])!=0) $year_cond=" and year(a.insert_date)=".str_replace("'","",$data[4]).""; else $year_cond="";
	}
	else
	{
		if(str_replace("'","",$data[4])!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=".str_replace("'","",$data[4]).""; else $year_cond="";
	}

	$order_type=str_replace("'","",$data[4]);

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

	$arr=array (0=>$buyer_arr,5=>$product_dept);
	
	$sql = "SELECT a.id,a.product_dept, a.style_ref_no,a.buyer_name,a.job_no,a.job_no_prefix_num,a.style_description,$select_date as job_year from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst $company_name $buyer_name $product_dept_cond $year_cond and a.is_deleted=0 and b.is_deleted=0 group by a.id, a.style_ref_no, a.job_no, a.job_no_prefix_num, a.buyer_name, a.insert_date, a.product_dept, a.style_description order by a.id DESC";

	//echo $sql;

	echo  create_list_view("list_view", "Buyer,Style,Job Year, Job No, Item Description, Prod. Dept.", "100,100,80,80,120,120","650","580",0, $sql , "js_set_value", "id,job_no_prefix_num,style_ref_no", "", 1, "buyer_name,0,0,0,0,product_dept", $arr ,"buyer_name,style_ref_no,job_year,job_no_prefix_num,style_description,product_dept", "",'setFilterGrid("list_view",-1);','0,0,0,0,0,0','',1) ;
	
	exit();
}
if( $action=="report_generate" )
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	$company_name=str_replace("'","",$cbo_company_name);
	$file_no=str_replace("'","",$txt_file_no);
	$style_no=str_replace("'","",$txt_style_no);
	$job_id=str_replace("'","",$hidden_job_id);
	$product_department=str_replace("'","",$cbo_product_department);
	if($style_no=="") $style_no_cond=""; else $style_no_cond=" and b.style_ref_no like '%'.$style_no' ";
	if($job_id!="") $job_id_cond=" and a.id in ($job_id)"; else $job_id_cond="";
	if($product_department!="") $product_department_cond=" and a.product_dept in ($product_department)"; else $product_department_cond="";

	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name =$company_name and variable_list=23 and is_deleted=0 and status_active=1");

	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	
	$date_cond='';
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		$start_date=str_replace("'","",$txt_date_from);
		$end_date=str_replace("'","",$txt_date_to); 
		$date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
		$date_cond2=" and pub_shipment_date between '$start_date' and '$end_date'";
	}
	
	if($db_type==0)
	{
		if(str_replace("'","",$cbo_year)!=0) $yearCond=" and year(a.insert_date)=".str_replace("'","",$cbo_year).""; else $yearCond="";
	}
	else
	{
		if(str_replace("'","",$cbo_year)!=0) $yearCond=" and to_char(a.insert_date,'YYYY')=".str_replace("'","",$cbo_year).""; else $yearCond="";
	}
	if(str_replace("'","",$txt_job_no)!="" || str_replace("'","",$txt_job_no)!=0) $jobcond="and a.job_no_prefix_num=".$txt_job_no.""; else $jobcond="";
    //echo $hidden_job_id.'=='.$txt_job_no; die;
    if($job_id=='' && str_replace("'","",$txt_job_no)==""){
        $job_id_sql=sql_select("SELECT a.id as job_id from wo_po_details_master a join wo_po_break_down b on a.id=b.job_id where a.status_active=1 and a.is_deleted=0 and  a.company_name=$company_name $buyer_id_cond $date_cond $yearCond $product_department_cond group by a.id");
		$job_id_arr=array();
		foreach($job_id_sql as $row){
			$job_id_arr[$row[csf('job_id')]]=$row[csf('job_id')];
		}
    }

	// All Library
	$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
	$company_arr=return_library_array("select id,company_name from  lib_company","id","company_name");
	$color_arr=return_library_array("select id,color_name from  lib_color","id","color_name");
	$supplier_arr=return_library_array("select id,supplier_name from  lib_supplier","id","supplier_name");
	$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
	$yarn_count_library=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count"  );
	$dealing_merchant_array = return_library_array("select id, team_member_name from lib_mkt_team_member_info","id","team_member_name");
	$team_library=return_library_array( "select id, team_name from lib_marketing_team", "id", "team_name"  );
	$brand_library=return_library_array( "select id, brand_name from lib_buyer_brand", "id", "brand_name"  );
	$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );

	if(count($job_id_arr)>0){
		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2,3) and ENTRY_FORM=2004");
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 2004, 2, $job_id_arr, $empty_arr);//job ID Ref from=1	

		$main_sql="SELECT a.job_no, a.id as job_id, a.buyer_name, a.style_ref_no, a.brand_id, a.job_quantity, a.total_set_qnty, rtrim(xmlagg(xmlelement(po_data, b.po_number,', ').extract('//text()') order by b.id).getclobval(),', ') as po_data , min(b.pub_shipment_date) as shipment_data, min(b.shipment_date) as original_ship_date, rtrim(xmlagg(xmlelement(gmts_id, c.gmts_item_id,',').extract('//text()') order by c.id).getclobval(),',') as gmts_id, e.cm_cost, f.exchange_rate from wo_po_details_master a join wo_po_break_down b on a.id=b.job_id join wo_po_details_mas_set_details c on a.id=c.job_id join gbl_temp_engine d on a.id=d.ref_val join wo_pre_cost_dtls e on e.job_id=a.id join wo_pre_cost_mst f on a.id=f.job_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.entry_form=2004 and ref_from=2 group by a.id, a.job_no, a.buyer_name, a.style_ref_no, a.brand_id, a.job_quantity, a.total_set_qnty, e.cm_cost, f.exchange_rate order by min(b.shipment_date)";
	}
	else{
		if($job_id_cond!=''){
			$main_sql="SELECT a.job_no, a.id as job_id, a.buyer_name, a.style_ref_no, a.brand_id, a.job_quantity, a.total_set_qnty, rtrim(xmlagg(xmlelement(po_data, b.po_number,', ').extract('//text()') order by b.id).getclobval(),', ') as po_data , min(b.pub_shipment_date) as shipment_data, min(b.shipment_date) as original_ship_date, rtrim(xmlagg(xmlelement(gmts_id, c.gmts_item_id,',').extract('//text()') order by c.id).getclobval(),',') as gmts_id, e.cm_cost, f.exchange_rate from wo_po_details_master a join wo_po_break_down b on a.id=b.job_id join wo_po_details_mas_set_details c on a.id=c.job_id left join wo_pre_cost_dtls e on e.job_id=a.id left join wo_pre_cost_mst f on a.id=f.job_id  where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  a.company_name=$company_name $buyer_id_cond $date_cond $yearCond $product_department_cond $job_id_cond  group by a.id, a.job_no, a.buyer_name, a.style_ref_no, a.brand_id, a.job_quantity, a.total_set_qnty, e.cm_cost, f.exchange_rate order by min(b.shipment_date)";
		}
		else{
			$main_sql="SELECT a.job_no, a.id as job_id, a.buyer_name, a.style_ref_no, a.brand_id, a.job_quantity, a.total_set_qnty, rtrim(xmlagg(xmlelement(po_data, b.po_number,', ').extract('//text()') order by b.id).getclobval(),', ') as po_data , min(b.pub_shipment_date) as shipment_data, min(b.shipment_date) as original_ship_date, rtrim(xmlagg(xmlelement(gmts_id, c.gmts_item_id,',').extract('//text()') order by c.id).getclobval(),',') as gmts_id, e.cm_cost, f.exchange_rate from wo_po_details_master a join wo_po_break_down b on a.id=b.job_id join wo_po_details_mas_set_details c on a.id=c.job_id left join wo_pre_cost_dtls e on e.job_id=a.id left join wo_pre_cost_mst f on a.id=f.job_id  where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  a.company_name=$company_name $buyer_id_cond $date_cond $yearCond $product_department_cond $jobcond group by a.id, a.job_no, a.buyer_name, a.style_ref_no, a.brand_id, a.job_quantity, a.total_set_qnty, e.cm_cost, f.exchange_rate order by min(b.shipment_date)";
		}
		
	}
	

	//echo $main_sql; die;
	$main_data=sql_select($main_sql);
	$job_id_arr=array();
	foreach($main_data as $row){
		$job_id_arr[$row[csf('job_id')]]=$row[csf('job_id')];
	}
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2,3) and ENTRY_FORM=2004");
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 2004, 1, $job_id_arr, $empty_arr);//job ID Ref from=1

	$costing_library=return_library_array( "select a.job_no, a.costing_date from wo_pre_cost_mst a join gbl_temp_engine b on b.ref_val=c.job_id where b.entry_form=2004 and b.ref_from=1 and b.user_id=$user_id", "job_no", "costing_date"  );
	//echo "1088"; die;
	$jobIds=implode(",",$job_id_arr);
	$condition= new condition();
	$condition->jobid_in("$jobIds");
	$condition->init();
	$trim= new trims($condition);
	//echo $trim->getQuery(); die;
	$get_trims_data=$trim->getQtyArray_by_jobAndItemid();
	/* echo '<pre>';
	print_r($get_trims_data); die; */
	//wo_yarn_dyeing_dtls

	

	$sarvice_booking=sql_select("SELECT a.entry_form,a.booking_no, a.pay_mode, a.supplier_id, a.company_id, c.job_id from wo_booking_mst a join wo_booking_dtls b on a.id=b.booking_mst_id join wo_po_break_down c on c.id=b.po_break_down_id join gbl_temp_engine d on d.ref_val=c.job_id where a.booking_type=3 and d.entry_form=2004 and d.ref_from=1 and d.user_id=$user_id and a.entry_form in (228,229,162)");
	$service_booking_arr=array();
	$dyeing_booking_arr=array();
	$aop_booking_arr=array();
	foreach($sarvice_booking as $row){
		if($row[csf('entry_form')]==228){
			$service_booking_arr[$row[csf('job_id')]]['booking_no'][$row[csf('booking_no')]]=$row[csf('booking_no')];
			$service_booking_arr[$row[csf('job_id')]]['company'][$row[csf('company_id')]]=$company_arr[$row[csf('company_id')]];
			if($row[csf('pay_mode')]==3 || $row[csf('pay_mode')]==5){
				$service_booking_arr[$row[csf('job_id')]]['supplier'][$company_arr[$row[csf('supplier_id')]]]=$company_arr[$row[csf('supplier_id')]];
			}
			else{
				$service_booking_arr[$row[csf('job_id')]]['supplier'][$supplier_arr[$row[csf('supplier_id')]]]=$supplier_arr[$row[csf('supplier_id')]];
			}
		}
		if($row[csf('entry_form')]==229){
			$dyeing_booking_arr[$row[csf('job_id')]]['booking_no'][$row[csf('booking_no')]]=$row[csf('booking_no')];
			if($row[csf('pay_mode')]==3 || $row[csf('pay_mode')]==5){
				$dyeing_booking_arr[$row[csf('job_id')]]['supplier'][$company_arr[$row[csf('supplier_id')]]]=$company_arr[$row[csf('supplier_id')]];
			}
			else{
				$dyeing_booking_arr[$row[csf('job_id')]]['supplier'][$supplier_arr[$row[csf('supplier_id')]]]=$supplier_arr[$row[csf('supplier_id')]];
			}
		}
		if($row[csf('entry_form')]==162){
			$aop_booking_arr[$row[csf('job_id')]]['booking_no'][$row[csf('booking_no')]]=$row[csf('booking_no')];
			if($row[csf('pay_mode')]==3 || $row[csf('pay_mode')]==5){
				$aop_booking_arr[$row[csf('job_id')]]['supplier'][$company_arr[$row[csf('supplier_id')]]]=$company_arr[$row[csf('supplier_id')]];
			}
			else{
				$aop_booking_arr[$row[csf('job_id')]]['supplier'][$supplier_arr[$row[csf('supplier_id')]]]=$supplier_arr[$row[csf('supplier_id')]];
			}
		}
		
	}
	$fabric_avg_cons=sql_select("SELECT avg(a.cons) as fin_cons, a.job_id from wo_pre_cos_fab_co_avg_con_dtls a join gbl_temp_engine b on b.ref_val=a.job_id where b.entry_form=2004 and b.ref_from=1 and status_active=1 and is_deleted=0 and b.user_id=$user_id group by a.job_id");
	$fabric_cons_arr=array();
	foreach($fabric_avg_cons as $row){
		$fabric_cons_arr[$row[csf('job_id')]]=$row[csf('fin_cons')];
	}

	$fabric_booking=sql_select("SELECT b.fin_fab_qnty , c.job_id, a.booking_no, b.fabric_color_id from wo_booking_mst a join wo_booking_dtls b on a.id=b.booking_mst_id join wo_po_break_down c on c.id=b.po_break_down_id join gbl_temp_engine d on d.ref_val=c.job_id where a.booking_type=1 and d.entry_form=2004 and d.ref_from=1 and d.user_id=$user_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	$fabric_booking_arr=array();
	foreach($fabric_booking as $row){
		$fabric_booking_arr[$row[csf('job_id')]]['fin_fab_qty'][$row[csf('fabric_color_id')]]+=$row[csf('fin_fab_qnty')];
		$fabric_booking_arr[$row[csf('job_id')]]['booking_no'][$row[csf('booking_no')]]=$row[csf('booking_no')];
	}

	$fabric_rcv=sql_select("SELECT b.receive_qnty, f.job_id from inv_receive_master a join pro_finish_fabric_rcv_dtls b on a.id=b.mst_id join order_wise_pro_details c on b.trans_id=c.trans_id and b.prod_id=c.prod_id and b.id=c.dtls_id join wo_po_break_down d on c.po_breakdown_id=d.id join gbl_temp_engine e on e.ref_val=d.id join wo_po_break_down f on c.po_breakdown_id=f.id where e.entry_form=2004 and e.ref_from=1 and e.user_id=$user_id and c.entry_form =37 and c.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0");
	$fabric_rcv_arr=array();
	foreach($fabric_rcv as $row){
		$fabric_rcv_arr[$row[csf('job_id')]]+=$row[csf('receive_qnty')];
	}

	$trims_rcv=sql_select("SELECT c.quantity, d.job_no_mst, b.item_group_id as trim_group from inv_receive_master a join inv_trims_entry_dtls b on a.id=b.mst_id join order_wise_pro_details c on b.trans_id=c.trans_id and b.prod_id=c.prod_id and b.id=c.dtls_id join wo_po_break_down d on c.po_breakdown_id=d.id join gbl_temp_engine e on e.ref_val=d.job_id where e.entry_form=2004 and e.ref_from=1 and e.user_id=$user_id and c.entry_form=24 and c.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0");
	$trims_rcv_arr=array();
	foreach($trims_rcv as $row){
		$trims_rcv_arr[$row[csf('job_no_mst')]][$row[csf('trim_group')]]+=$row[csf('quantity')];
	}
	
	$acc_status=array();
	foreach($get_trims_data as $jobno=>$job_data_arr){
		foreach($job_data_arr as $trimid=>$value){
			if((int)$value!=(int)$trims_rcv_arr[$jobno][$trimid]){
				$acc_status[$jobno][$trimid]=$item_library[$trimid];
			}
		}
	}

	/* Cutting from Bundle
	$cutting_qc_qty=sql_select("SELECT c.id as job_id, sum(b.qc_pass_qty) as qc_pass_qty, a.serving_company from pro_gmts_cutting_qc_mst a join pro_gmts_cutting_qc_dtls b on b.mst_id=a.id join wo_po_details_master c on c.job_no=a.job_no join gbl_temp_engine d on d.ref_val=c.id where d.entry_form=2004 and d.ref_from=1 and d.user_id=$user_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by c.id, a.serving_company");
	$cutting_qty_arr=array();
	$production_unit_arr=array();
	foreach($cutting_qc_qty as $row){
		$cutting_qty_arr[$row[csf('job_id')]]=$row[csf('qc_pass_qty')];
		$production_unit_arr[$row[csf('job_id')]][$row[csf('serving_company')]]=$company_arr[$row[csf('serving_company')]];
	} 
	*/
	$sewing_output_qty=sql_select("SELECT sum(b.production_qnty) as production_quantity, c.job_id , a.production_type, rtrim(xmlagg(xmlelement(sewing_line, a.sewing_line,',').extract('//text()') order by a.id).getclobval(),',') as sewing_line, a.serving_company, a.embel_name from pro_garments_production_mst a join pro_garments_production_dtls b on a.id=b.mst_id join wo_po_break_down c on c.id=a.po_break_down_id join gbl_temp_engine d on d.ref_val=c.job_id where d.entry_form=2004 and d.ref_from=1 and d.user_id=$user_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type in (1,3,8,5) and b.production_type in (1,3,8,5) group by c.job_id, a.production_type, a.serving_company, a.embel_name");
	$sewing_qty_arr=array(); $packing_qty_arr=array(); $printing_qty_arr=array(); $embroidery_qty_arr=array();
	foreach($sewing_output_qty as $row){
		if($row[csf('production_type')]==5){
			$sewing_qty_arr[$row[csf('job_id')]]+=$row[csf('production_quantity')];
			$line_id=explode(",", rtrim($row[csf('sewing_line')]->load()));
			if($prod_reso_allo==1){
				foreach($line_id as $lineid){
					$line_array[$lineid]=$lineid;
					$line_array_dtls[$row[csf('job_id')]][$lineid]=$lineid;
				}				
			}
			else{
				foreach($line_id as $lineid){
					$line_id_arr[$row[csf('job_id')]][$lineid]=$line_library[$lineid];
				}
			}
			
			$production_unit_arr[$row[csf('job_id')]][$row[csf('serving_company')]]=$company_arr[$row[csf('serving_company')]];
		}
		if($row[csf('production_type')]==8){
			$packing_qty_arr[$row[csf('job_id')]]+=$row[csf('production_quantity')];
			$production_unit_arr[$row[csf('job_id')]][$row[csf('serving_company')]]=$company_arr[$row[csf('serving_company')]];
		}
		if($row[csf('production_type')]==1){
			$cutting_qty_arr[$row[csf('job_id')]]+=$row[csf('production_quantity')];
			$production_unit_arr[$row[csf('job_id')]][$row[csf('serving_company')]]=$company_arr[$row[csf('serving_company')]];
		}
		if($row[csf('production_type')]==3){
			if($row[csf('embel_name')]==1){
				$printing_qty_arr[$row[csf('job_id')]]+=$row[csf('production_quantity')];
			}
			if($row[csf('embel_name')]==2){
				$embroidery_qty_arr[$row[csf('job_id')]]+=$row[csf('production_quantity')];
			}
			$production_unit_arr[$row[csf('job_id')]][$row[csf('serving_company')]]=$company_arr[$row[csf('serving_company')]];
		}
	}

	if(count($line_array)>0){
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 2004, 3, $line_array, $empty_arr);//line Ref from=3

		$line_data=sql_select("SELECT a.id, a.line_number,a.prod_resource_num from prod_resource_mst a join prod_resource_dtls b on a.id=b.mst_id join gbl_temp_engine c on a.id=c.ref_val where c.entry_form=2004 and c.ref_from=3 and c.user_id=$user_id and a.is_deleted=0 and b.is_deleted=0 group by a.id,a.prod_resource_num, a.line_number");
		$line_merge=9999;
		foreach($line_data as $row)
		{
			$line='';
			$line_number=explode(",",$row[csf('line_number')]);
			foreach($line_number as $val)
			{
				if(count($line_number)>1)
				{
					$line_merge++;
					$new_arr[$line_merge]=$row[csf('id')];
				}
				else
					$new_arr[$line_library[$val]]=$row[csf('id')];
				if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
			}
			$line_array[$row[csf('id')]]=$line;
		}
		ksort($new_arr);

		foreach($new_arr as $key=>$v)
		{
			$line_array_new[$v]=$line_array[$v];
		}
		foreach($line_array_dtls as $jobid=>$linearr){
			foreach($linearr as $lineid=>$lineno){
				$line_id_arr[$jobid][$lineid]=$line_array_new[$lineid];
			}
		}
	}

	$Sales_contract_data=sql_select("SELECT b.contract_no, c.job_id from com_sales_contract_order_info a join com_sales_contract b on a.com_sales_contract_id=b.id join wo_po_break_down c on a.wo_po_break_down_id=c.id join gbl_temp_engine d on d.ref_val=c.job_id where d.entry_form=2004 and d.ref_from=1 and d.user_id=$user_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.contract_no, c.job_id");
	$sc_contract_arr=array();
	foreach($Sales_contract_data as $row){
		$sc_contract_arr[$row[csf('job_id')]][$row[csf('contract_no')]]=$row[csf('contract_no')];
	}

	$lc_number_data=sql_select("SELECT c.job_id, rtrim(xmlagg(xmlelement(export_lc_no, b.export_lc_no,', ').extract('//text()') order by b.id).getclobval(),', ') as export_lc_no from com_export_lc_order_info a join com_export_lc b on a.com_export_lc_id=b.id join wo_po_break_down c on a.wo_po_break_down_id=c.id join gbl_temp_engine d on d.ref_val=c.job_id where d.entry_form=2004 and d.ref_from=1 and d.user_id=$user_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by c.job_id");
	$lc_number_arr=array();
	foreach($lc_number_data as $row){
		$lc_number_arr[$row[csf('job_id')]]=rtrim($row[csf('export_lc_no')]->load(),',');
	}

	$export_number_data=sql_select("SELECT c.job_id, rtrim(xmlagg(xmlelement(invoice_no, a.invoice_no,', ').extract('//text()') order by a.id).getclobval(),', ') as invoice_no, sum(b.current_invoice_value) as current_invoice_value, a.commission_percent from com_export_invoice_ship_mst a join com_export_invoice_ship_dtls b on a.id=b.mst_id join wo_po_break_down c on b.po_breakdown_id=c.id join gbl_temp_engine d on d.ref_val=c.job_id where d.entry_form=2004 and d.ref_from=1 and d.user_id=$user_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by c.job_id, a.commission_percent");
	$export_number_arr=array();
	foreach($export_number_data as $row){
		$export_number_arr[$row[csf('job_id')]]['invoice_no']=rtrim($row[csf('invoice_no')]->load(),',');
		$export_number_arr[$row[csf('job_id')]]['value']=$row[csf('current_invoice_value')];
		$export_number_arr[$row[csf('job_id')]]['commission_percent']=$row[csf('commission_percent')];
	}

	$exfactory_qty=sql_select("SELECT b.job_id, sum(a.ex_factory_qnty) as ex_factory_qnty, min(a.ex_factory_date) as ex_factory_date from pro_ex_factory_mst a join wo_po_break_down b on a.po_break_down_id=b.id join gbl_temp_engine c on c.ref_val=b.job_id where c.entry_form=2004 and c.user_id=$user_id and c.ref_from=1 and a.status_active=1 and a.is_deleted=0 group by b.job_id");
	$exfactory_qty_arr=array();
	foreach($exfactory_qty as $row){
		$exfactory_qty_arr[$row[csf('job_id')]]['qty']=$row[csf('ex_factory_qnty')];
		$exfactory_qty_arr[$row[csf('job_id')]]['exdate']=$row[csf('ex_factory_date')];
	}

	$yarn_dyeing_data=sql_select("SELECT a.ydw_no, a.supplier_id, b.job_no_id as job_id from wo_yarn_dyeing_mst a join wo_yarn_dyeing_dtls b on a.id=b.mst_id join gbl_temp_engine c on c.ref_val=b.job_no_id where c.entry_form=2004 and c.user_id=$user_id and c.ref_from=1 and a.status_active=1 and a.is_deleted=0 group by a.ydw_no, a.supplier_id, b.job_no_id");
	foreach($yarn_dyeing_data as $row){
		$Yarn_dyeing_work_arr[$row[csf('job_id')]]['supplier'][$row[csf('supplier_id')]]=$supplier_arr[$row[csf('supplier_id')]];
		$Yarn_dyeing_work_arr[$row[csf('job_id')]]['wo_no'][$row[csf('ydw_no')]]=$row[csf('ydw_no')];
	}
	ob_start();
	?>
	<div style="width:100%">
		<table width="4800" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="100">Buyer</th>
					<th width="110">Brand</th>
					<th width="100">Job Number</th>
					<th width="100">Article</th>				
					<th width="200">Order No.</th>
					<th width="100">Item Description</th>
					<th width="100">Order Qty</th>					
					<th width="100">Delivery date</th>
					<th width="80">Revised Delivery date</th>
					<th width="100">Production Unit</th>
					<th width="70">PP STATUS</th>
					<th width="120">Fabrics Status</th>
					<th width="70">Knit</th>
					<th width="150">Knitting Factory</th>
					<th width="150">Knitting Service Booking Ref </th>
					<th width="120">Dyeing Factory</th>
					<th width="150">Dyeing Service Booking Ref</th>
					<th width="120">Aop Factory</th>
					<th width="150">AOP Service Booking Ref</th>
					<th width="150">Yarn Dyed Factory</th>
					<th width="150">Yarn dyed Service Booking Ref</th>
					<th width="150">Fabrics ref</th>
					<th width="100">Accessories Status</th>
					<th width="60">Fabrics Status of DKL</th>
					<th width="60">Challan no</th>
					<th width="60">Date</th>
					<th width="60">Accessories Status of DKL</th>
					<th width="60">Challan no</th>
					<th width="60">Date</th>
					<th width="150">Production status</th>
					<th width="100">Line</th>
					<th width="80">Cutting</th>
					<th width="80">Printing</th>
					<th width="80">Embroidery</th>
					<th width="80">Sewing</th>
					<th width="80">Finishing</th>
					<th width="80">CM per Gmts (TK)</th>
					<th width="80">Avg. Booking Consumption</th>
					<th width="120">LC/SC NO</th>
					<th width="120">Inv. NO</th>
					<th width="80">Shipped Qty</th>
					<th width="100">Shipment Status</th>
					<th width="100">Less/More Qty</th>
					<th width="80">G.Inv Amt</th>
					<th width="80">Buyer Comm.</th>
					<th width="80">Net Inv Amt</th>
					<th width="80">Ex-Fac Dt</th>
				</tr>				
			</thead>
			<tbody id="table_body">
				<?
				$i=1;
				foreach($main_data as $row){
					$gmts_id_arr=array();
					$gmts_item_arr=explode(",", rtrim($row[csf('gmts_id')]->load(),','));
					foreach($gmts_item_arr as $gmid){
						$gmts_id_arr[$gmid]=$garments_item[$gmid];
					}
					$fabric_status=0;
					if($fabric_rcv_arr[$row[csf('job_id')]]==array_sum($fabric_booking_arr[$row[csf('job_id')]]['fin_fab_qty'])){
						$fabric_status='INHOUSE';
					}
					else{
						$color_wise_fabric=array();
						foreach($fabric_booking_arr[$row[csf('job_id')]]['fin_fab_qty'] as $colorid=>$fabqty){
							$color_wise_fabric[]=$color_arr[$colorid].':'.number_format($fabqty);
						}
						$fabric_status=implode(", ",$color_wise_fabric);
					}
					
					if(count($get_trims_data[$row[csf('job_no')]])==0){
						$trims_status="Budget Pending";
					}
					else if(count($acc_status[$row[csf('job_no')]])>0){
						$trims_status=implode(", ",$acc_status[$row[csf('job_no')]]);
					}
					else{
						$trims_status="INHOUSE";
					}
					$production_status='Ready For Ship';
					if($cutting_qty_arr[$row[csf('job_id')]] >= $row[csf('job_quantity')]*$row[csf('total_set_qnty')]){
						if($sewing_qty_arr[$row[csf('job_id')]] >= $row[csf('job_quantity')]*$row[csf('total_set_qnty')]){
							if($packing_qty_arr[$row[csf('job_id')]] >= $row[csf('job_quantity')]*$row[csf('total_set_qnty')]){
								$production_status='Ready For Ship';
							}
							else{
								$production_status='Packing Finishing Running';
							}
						}
						else{
							$production_status='Sewing Running';
						}
					}
					else{
						if($cutting_qty_arr[$row[csf('job_id')]]>=1){
							$production_status='Cutting Running';
						}
						else{
							$production_status='Cutting Pending';
						}
						
					}

					$sc_lc_no="";
					if($lc_number_arr[$row[csf('job_id')]] !="")
					{
						$sc_lc_no= "LC: ". $lc_number_arr[$row[csf('job_id')]];
					}
					if(count($sc_contract_arr[$row[csf('job_id')]])>0)
					{
						$sc_lc_no= " SC: ".implode(", ",$sc_contract_arr[$row[csf('job_id')]]);
					}
					$job_qty=$row[csf('job_quantity')]*$row[csf('total_set_qnty')];
					$shipment_status="";
					if(count($exfactory_qty_arr[$row[csf('job_id')]]['qty'])>0){
						if($exfactory_qty_arr[$row[csf('job_id')]]['qty']==$job_qty){
							$shipment_status="Full Shipment";
						}
						elseif($exfactory_qty_arr[$row[csf('job_id')]]['qty']<$job_qty){
							$shipment_status="Partial Shipment";
						}
						else{
							$shipment_status="Over Shipment";
						}
					}
					else{
						$shipment_status="Full pending";
					}
					$less_more_qty=0;
					if($exfactory_qty_arr[$row[csf('job_id')]]['qty']>0){
						$less_more_qty=$job_qty-$exfactory_qty_arr[$row[csf('job_id')]]['qty'];
					}

					$export_invoice_commission=$export_number_arr[$row[csf('job_id')]]['value']*$export_number_arr[$row[csf('job_id')]]['commission_percent']/100;

					// Total calculation
					$total_job_qty+=$job_qty;
					$total_cutting_qty+=$cutting_qty_arr[$row[csf('job_id')]];
					$total_sewing_qty+=$sewing_qty_arr[$row[csf('job_id')]];
					$total_packing_qty+=$packing_qty_arr[$row[csf('job_id')]];
					$total_ship_qty+=$exfactory_qty_arr[$row[csf('job_id')]]['qty'];
					$total_export_value+=$export_number_arr[$row[csf('job_id')]]['value'];
					$total_less_more_qty+=$less_more_qty;
					$total_invoice_commission+=$export_invoice_commission;
					$total_net_inv_amt+=$export_number_arr[$row[csf('job_id')]]['value']-$export_invoice_commission;

				?>
				<tr>
					<td width="30"><?= $i ?></td>
					<td align="center" width="100"><?= $buyer_arr[$row[csf('buyer_name')]] ?></td>
					<td align="center" width="110"><?= $brand_library[$row[csf('brand_id')]] ?></td>
					<td align="center" width="100"><?= $row[csf('job_no')] ?></td>
					<td align="center" width="100"><?= $row[csf('style_ref_no')] ?></td>
					<td align="center" width="200"><?= $row[csf('po_data')]->load() ?></td>
					<td align="center" width="100"><?= implode(", ",$gmts_id_arr) ?></td>
					<td align="right" width="100"><?= $row[csf('job_quantity')]*$row[csf('total_set_qnty')] ?></td>
					<td align="center" width="100"><?= change_date_format($row[csf('shipment_data')]); ?></td>
					<td align="center" width="80"><?= change_date_format($row[csf('original_ship_date')]); ?></td>
					<td width="100"><?= implode(", ",$production_unit_arr[$row[csf('job_id')]]) ?></td>
					<td width="70"></td>
					<td width="120" title="Booking Finish Qnty compare with Finish fabric Rcv Qty"><?= $fabric_status ?></td>
					<td width="70"><? if(count($service_booking_arr[$row[csf('job_id')]]['company'])>0) echo "OK"; ?></td>
					<td width="150"><?= implode(", ",$service_booking_arr[$row[csf('job_id')]]['supplier']) ?></td>
					<td width="150"><?= implode(", ",$service_booking_arr[$row[csf('job_id')]]['booking_no']) ?></td>
					<td width="120"><?= implode(", ",$dyeing_booking_arr[$row[csf('job_id')]]['supplier']) ?></td>
					<td width="150"><?= implode(", ",$dyeing_booking_arr[$row[csf('job_id')]]['booking_no']) ?></td>
					<td width="120"><?= implode(", ",$aop_booking_arr[$row[csf('job_id')]]['supplier']) ?></td>
					<td width="150"><?= implode(", ",$aop_booking_arr[$row[csf('job_id')]]['booking_no']) ?></td>
					<td width="150"><?= implode(", ",$Yarn_dyeing_work_arr[$row[csf('job_id')]]['supplier']) ?></td>
					<td width="150"><?= implode(", ",$Yarn_dyeing_work_arr[$row[csf('job_id')]]['wo_no']) ?></td>
					<td width="150"><?= implode(", ",$fabric_booking_arr[$row[csf('job_id')]]['booking_no']) ?></td>
					<td width="100"><?= $trims_status ?></td>
					<td width="60"></td>
					<td width="60"></td>
					<td width="60"></td>
					<td width="60"></td>
					<td width="60"></td>
					<td width="60"></td>
					<td width="150"><?= $production_status ?></td>
					<td width="100"><?= implode(", ",$line_id_arr[$row[csf('job_id')]]) ?></td>
					<td align="right" width=80"><?= $cutting_qty_arr[$row[csf('job_id')]] ?></td>
					<td align="right" width="80"><?= $printing_qty_arr[$row[csf('job_id')]] ?></td>
					<td align="right" width="80"><?= $embroidery_qty_arr[$row[csf('job_id')]] ?></td>
					<td align="right" width="80"><?= $sewing_qty_arr[$row[csf('job_id')]] ?></td>
					<td align="right" width="80"><?= $packing_qty_arr[$row[csf('job_id')]] ?></td>
					<td align="right" width="80" title="{(pre-costing CM  X 60%)/12} X exchange rate"><?= fn_number_format((($row[csf('cm_cost')]*.6)/12)*$row[csf('exchange_rate')],2,".",""); ?></td>
					<td align="right" width="80"><?= fn_number_format($fabric_cons_arr[$row[csf('job_id')]],2,".",""); ?></td>
					<td align="center" width="120"><?= $sc_lc_no; ?></td>
					<td align="center" width="120"><?= $export_number_arr[$row[csf('job_id')]]['invoice_no']; ?></td>
					<td align="right" width="80"><?= $exfactory_qty_arr[$row[csf('job_id')]]['qty']; ?></td>
					<td align="center" width="100"><?= $shipment_status ?></td>
					<td align="right" width="100"><?= $less_more_qty ?></td>
					<td align="right" width="80"><?= $export_number_arr[$row[csf('job_id')]]['value'] ?></td>
					<td align="right" width="80"><?= $export_invoice_commission ?></td>
					<td align="right" width="80"><?= $export_number_arr[$row[csf('job_id')]]['value']-$export_invoice_commission ?></td>
					<td align="right" width="80"><?= change_date_format($exfactory_qty_arr[$row[csf('job_id')]]['exdate']); ?></td>
				</tr>
				<? 
				$i++;
				} ?>
			</tbody>
			<tfoot>				
				<tr>
					<th width="30"></th>
					<th width="100"></th>
					<th width="110"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="200"></th>
					<th width="100">Total</th>
					<th width="100"><?= $total_job_qty ?></th>					
					<th width="100"></th>
					<th width="80"></th>
					<th width="100"></th>
					<th width="70"></th>
					<th width="120"></th>
					<th width="70"></th>
					<th width="150"></th>
					<th width="150"></th>
					<th width="120"></th>
					<th width="150"></th>
					<th width="120"></th>
					<th width="150"></th>
					<th width="150"></th>
					<th width="150"></th>
					<th width="150"></th>
					<th width="100"></th>
					<th width="60"></th>
					<th width="60"></th>
					<th width="60"></th>
					<th width="60"></th>
					<th width="60"></th>
					<th width="60"></th>
					<th width="150"></th>
					<th width="100"></th>
					<th width="80"><?= $total_cutting_qty ?></th>
					<th width="80"></th>
					<th width="80"></th>
					<th width="80"><?= $total_sewing_qty ?></th>
					<th width="80"><?= $total_packing_qty ?></th>
					<th width="80"></th>
					<th width="80"></th>
					<th width="120"></th>
					<th width="120"></th>
					<th width="80"><?= $total_ship_qty ?></th>
					<th width="100"></th>
					<th width="100"><?= $total_less_more_qty ?></th>
					<th width="80"><?= $total_export_value ?></th>
					<th width="80"><?= $total_invoice_commission ?></th>
					<th width="80"><?= $total_net_inv_amt ?></th>
					<th width="80"></th>
				</tr>
			</tfoot>
		</table>
	</div>
	<?
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2,3) and ENTRY_FORM=2004");
	oci_commit($con);
	disconnect($con);
	
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html****$filename****1****$type";
	exit();	
}

if($action=="trims_cost")
{
	echo load_html_head_contents("Report Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	?>
    <div>
        <fieldset style="width:600px;">
        <div style="width:600px" align="center">	
            <table class="rpt_table" width="470" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <th width="120">Job No</th>
                    <th width="200">Order No</th>
                    <th>Order Qnty</th>
                </thead>
                <tr bgcolor="#FFFFFF">
                	<td align="center"><? echo $job_no; ?></td>
                    <td><? echo $po_no; ?></td>
                    <td align="right"><? echo fn_number_format($po_qnty,0); ?></td>
                </tr>
            </table>
            <table style="margin-top:10px" class="rpt_table" width="600" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                	<th width="40">SL</th>
                    <th width="130">Item Name</th>
                    <th width="90">Cons/Dzn</th>
                    <th width="80">Rate</th>
                    <th width="110">Trims Cost/Dzn</th>
                    <th>Total Trims Cost</th>
                </thead>
            </table>
            </div>
            <div style="width:620px; max-height:250px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="600" cellpadding="0" cellspacing="0" border="1" rules="all">
                	<?
					$costing_per=return_field_value("costing_per","wo_pre_cost_mst","job_no='$job_no' and status_active=1 and is_deleted=0");
                        
					$dzn_qnty=0;
					if($costing_per==1)
					{
						$dzn_qnty=12;
					}
					else if($costing_per==3)
					{
						$dzn_qnty=12*2;
					}
					else if($costing_per==4)
					{
						$dzn_qnty=12*3;
					}
					else if($costing_per==5)
					{
						$dzn_qnty=12*4;
					}
					else
					{
						$dzn_qnty=1;
					}
					//and b.po_break_down_id='$po_id' 
					$sql="select a.trim_group, a.amount,a.rate, a.cons_dzn_gmts as cons from wo_pre_cost_trim_cost_dtls a where   a.job_no='$job_no' and a.status_active=1 and a.is_deleted=0";
					$trimsArray=sql_select($sql);
					$i=1;
					foreach($trimsArray as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="40"><? echo $i; ?></td>
							<td width="130"><div style="width:130px; word-wrap:break-word"><? echo $item_library[$row[csf('trim_group')]]; ?></div></td>
							<td width="90" align="right"><? echo fn_number_format($row[csf('cons')],2); ?></td>
							<td width="80" align="right"><? echo fn_number_format($row[csf('rate')],2); ?></td>
							<td width="110" align="right">
								<?
                                    $trims_cost_per_dzn=$row[csf('cons')]*$row[csf('rate')]; 
                                    echo fn_number_format($trims_cost_per_dzn,2);
									$tot_trims_cost_per_dzn+=$trims_cost_per_dzn; 
                                ?>
                            </td>
							<td align="right">
								<?
                                	$trims_cost=($po_qnty/$dzn_qnty)*$trims_cost_per_dzn;
									echo fn_number_format($trims_cost,2);
									$tot_trims_cost+=$trims_cost;
                                ?>
                            </td>
						</tr>
					<?
					$i++;
					}
					?>
                	<tfoot>
                        <th colspan="4">Total</th>
                        <th><? echo fn_number_format($tot_trims_cost_per_dzn,2); ?></th>
                        <th><? echo fn_number_format($tot_trims_cost,2); ?></th>
                    </tfoot>    
                </table>
            </div>
        </fieldset>
    </div>
	<?
}

if($action=="other_cost")
{
	echo load_html_head_contents("Report Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	?>
    <div align="center">
        <fieldset style="width:600px;">
            <table class="rpt_table" width="470" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <th width="120">Job No</th>
                    <th width="200">Order No</th>
                    <th>Order Qnty</th>
                </thead>
                <tr bgcolor="#FFFFFF">
                	<td align="center"><? echo $job_no; ?></td>
                    <td><? echo $po_no; ?></td>
                    <td align="right"><? echo fn_number_format($po_qnty,0); ?></td>
                </tr>
            </table>
            <table style="margin-top:10px" class="rpt_table" width="470" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                    <th width="200">Particulars</th>
                    <th width="90">Cost/Dzn</th>
                    <th>Total Cost</th>
                </thead>
				<?
                $costing_per=return_field_value("costing_per","wo_pre_cost_mst","job_no='$job_no' and status_active=1 and is_deleted=0");
                    
                $dzn_qnty=0;
                if($costing_per==1)
                {
                    $dzn_qnty=12;
                }
                else if($costing_per==3)
                {
                    $dzn_qnty=12*2;
                }
                else if($costing_per==4)
                {
                    $dzn_qnty=12*3;
                }
                else if($costing_per==5)
                {
                    $dzn_qnty=12*4;
                }
                else
                {
                    $dzn_qnty=1;
                }
                    
                $sql="select common_oh, lab_test, inspection, freight, comm_cost from wo_pre_cost_dtls where job_no='$job_no' and status_active=1 and is_deleted=0";
                $fabriccostArray=sql_select($sql);
                ?>
                <tr bgcolor="#E9F3FF">
                    <td>Commercial Cost</td>
                    <td align="right"><? echo fn_number_format($fabriccostArray[0][csf('comm_cost')],2); ?></td>
                    <td align="right">
                        <?
                            $comm_cost=($po_qnty/$dzn_qnty)*$fabriccostArray[0][csf('comm_cost')]; 
                            echo fn_number_format($comm_cost,2);
                        ?>
                    </td>
                </tr>
                <tr bgcolor="#FFFFFF">
                    <td>Lab Test Cost</td>
                    <td align="right"><? echo fn_number_format($fabriccostArray[0][csf('lab_test')],2); ?></td>
                    <td align="right">
                        <?
                            $lab_cost=($po_qnty/$dzn_qnty)*$fabriccostArray[0][csf('lab_test')]; 
                            echo fn_number_format($lab_cost,2);
                        ?>
                    </td>
                </tr>
                 <tr bgcolor="#E9F3FF">
                    <td>Inspection Cost</td>
                    <td align="right"><? echo fn_number_format($fabriccostArray[0][csf('inspection')],2); ?></td>
                    <td align="right">
                        <?
                            $inspection_cost=($po_qnty/$dzn_qnty)*$fabriccostArray[0][csf('inspection')]; 
                            echo fn_number_format($inspection_cost,2);
                        ?>
                    </td>
                </tr>
                <tr bgcolor="#FFFFFF">
                    <td>Freight Cost</td>
                    <td align="right"><? echo fn_number_format($fabriccostArray[0][csf('freight')],2); ?></td>
                    <td align="right">
                        <?
                            $freight_cost=($po_qnty/$dzn_qnty)*$fabriccostArray[0][csf('freight')]; 
                            echo fn_number_format($freight_cost,2);
                        ?>
                    </td>
                </tr>
                <tr bgcolor="#E9F3FF">
                    <td>Common OH Cost</td>
                    <td align="right"><? echo fn_number_format($fabriccostArray[0][csf('common_oh')],2); ?></td>
                    <td align="right">
                        <?
                            $common_oh_cost=($po_qnty/$dzn_qnty)*$fabriccostArray[0][csf('common_oh')]; 
                            echo fn_number_format($common_oh_cost,2);
							
							$tot_cost_per_dzn=$fabriccostArray[0][csf('comm_cost')]+$fabriccostArray[0][csf('lab_test')]+$fabriccostArray[0][csf('inspection')]+$fabriccostArray[0][csf('freight')]+$fabriccostArray[0][csf('common_oh')];
							$tot_cost=$comm_cost+$lab_cost+$inspection_cost+$freight_cost+$common_oh_cost;
                        ?>
                    </td>
                </tr>
                <tfoot>
                    <th>Total</th>
                    <th><? echo fn_number_format($tot_cost_per_dzn,2); ?></th>
                    <th><? echo fn_number_format($tot_cost,2); ?></th>
                </tfoot>    
            </table>
        </fieldset>
    </div>
	<?
}
if($action=="job_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	
	<script>
	function js_set_value(str)
	{
		$("#hide_job_no").val(str);
		parent.emailwindow.hide(); 
	}
	</script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:580px;">
            <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Job Year</th>
                    <th>Search By</th>
                    <th>Style No</th>
                    <th id="search_by_td_up" width="170">Please Enter Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_id $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>
						<td><? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", $selected, "",0,"" ); ?></td>                
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref",3=>"Order No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td>
						
						<td align="center">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_style_no" id="txt_style_no" />	
                        </td>  
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company_id; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>', 'job_popup_search_list_view', 'search_div', 'order_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}

if ($action=="job_popup_search_list_view")
{
  	//echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	list($company_id,$buyer_id,$search_type,$search_value,$cbo_year)=explode('**',$data);

	if($search_type==1 && $search_value!=''){
		$search_con=" and a.job_no like('%$search_value')";	
	}
	else if($search_type==2 && $search_value!=''){
		$search_con=" and a.style_ref_no like('%$search_value%')";	
	}
	else if($search_type==3 && $search_value!=''){
		$search_con=" and b.po_number like('%$search_value%')";	
	}

	if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_cond="";
		}
		else
		{
			$buyer_cond="";
		}
	}
	else
	{
		$buyer_cond=" and a.buyer_name=$buyer_id";
	}
	
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0)
		{
			$year_cond=" and YEAR(a.insert_date)=$cbo_year";
			$year_field="YEAR(a.insert_date)";
		}
		else
		{
			$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";	
			$year_field="to_char(a.insert_date,'YYYY')";
		}
	}
	else $year_cond="";

	$arr=array (2=>$company_library,3=>$buyer_arr);
	$sql= "select a.job_no_prefix_num, a.job_no, a.company_name,a.buyer_name,a.style_ref_no,$year_field as year from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and  a.company_name=$company_id  $buyer_cond $year_cond $search_con order by a.id DESC";
	//echo $sql;
	echo  create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No", "70,70,120,100,100","570","230",0, $sql , "js_set_value", "year,job_no", "", 1, "0,0,company_name,buyer_name,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no", "","setFilterGrid('list_view',-1)",'0,0,0,0,0');
	echo "<input type='hidden' id='hide_job_no' />";
	
	exit();
}
disconnect($con);
?>