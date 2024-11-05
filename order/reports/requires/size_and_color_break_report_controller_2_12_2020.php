<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');

if($action=="load_drop_down_buyer")
{
	if($data != 0)
	{
		echo create_drop_down( "cbo_buyer_id", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "load_drop_down( 'requires/size_and_color_break_report_controller', this.value, 'load_drop_down_brand', 'brand_td');load_drop_down( 'requires/size_and_color_break_report_controller', this.value, 'load_drop_down_season', 'season_td');" );
		exit();
	}
	else{
		echo create_drop_down( "cbo_buyer_id", 120, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id    and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "load_drop_down( 'requires/size_and_color_break_report_controller', this.value, 'load_drop_down_brand', 'brand_td');load_drop_down( 'requires/size_and_color_break_report_controller', this.value, 'load_drop_down_season', 'season_td');" );
		exit();
	}
}
if ($action=="load_drop_down_brand")
{
	 //echo "select id, brand_name from lib_buyer_brand brand where buyer_id='$data' and status_active =1 and is_deleted=0 $brand_id_cond order by brand_name ASC";
	echo create_drop_down( "cbo_brand_id", 100, "select id, brand_name from lib_buyer_brand brand where buyer_id='$data' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "--Brand--", $selected, "" );
	exit();
}
if ($action=="load_drop_down_season")
{
	echo create_drop_down( "cbo_season_id", 100, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "" );
	exit();
}
if ($action=="week_date")
{
	$data=explode("_",$data);
	$sql_week_start_end_date=sql_select("select min(week_date) as week_start_day, Max(week_date) as week_end_day from week_of_year where week between '$data[0]' and '$data[1]' and year='$data[2]'");
	//echo "select min(week_date) as week_start_day, Max(week_date) as week_end_day from week_of_year where week between '$data[0]' and '$data[1]' and year= '$data[2]'";
	$week_start_day=0;
	$week_end_day=0;
	foreach ($sql_week_start_end_date as $row_week_week_start_end_date)
	{
		$week_start_day=$row_week_week_start_end_date[csf("week_start_day")];
		$week_end_day=$row_week_week_start_end_date[csf("week_end_day")];
	}
	echo change_date_format($week_start_day,"dd-mm-yyyy",'-')."_".change_date_format($week_end_day,"dd-mm-yyyy",'-');
}

if ($action=="job_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data);
	?>
    <script>
	function js_set_value( job_id )
	{
		//alert(po_id)
		document.getElementById('txt_job_id').value=job_id;
		parent.emailwindow.hide();
	}

	</script>
     <input type="hidden" id="txt_job_id" />
 <?
	if ($data[0]==0) $company_id=""; else $company_id=" and a.company_name=$data[0]";
	if ($data[1]==0) $buyer_id=""; else $buyer_id=" and a.buyer_name=$data[1]";
	//if ($data[2]==0) $year_id=""; else $year_id=" and buyer_name=$data[2]";
	if($db_type==0)
	{
		if(str_replace("'","",$data[2])!=0) $year_cond=" and year(a.insert_date)=".str_replace("'","",$data[2]).""; else $year_cond="";
	}
	else
	{
		if(str_replace("'","",$data[2])!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=".str_replace("'","",$data[2]).""; else $year_cond="";
	}

	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$teamMemberArr = return_library_array("select id,team_leader_name from lib_marketing_team ","id","team_leader_name");
	 $order_type=str_replace("'","",$data[3]);
	if($order_type==1)
	{
		 $sql= "select a.id, a.job_no,a.job_no_prefix_num, a.style_ref_no, a.product_dept, a.dealing_marchant, a.team_leader from wo_po_details_master a where a.status_active=1 and a.is_deleted=0 $company_id $buyer_id $year_cond group by a.id, a.job_no,a.job_no_prefix_num, a.style_ref_no, a.product_dept, a.dealing_marchant, a.team_leader order by a.job_no ";
	}
	else
	{
		 $sql= "select a.id, a.job_no,a.job_no_prefix_num, a.style_ref_no, a.product_dept, a.dealing_marchant, a.team_leader from wo_po_details_master a, wo_po_break_down  b where  b.job_no_mst=a.job_no and a.status_active=1 and a.is_deleted=0 $company_id $buyer_id $year_cond group by a.id, a.job_no,a.job_no_prefix_num, a.style_ref_no, a.product_dept, a.dealing_marchant, a.team_leader order by a.job_no";
	}
	//echo $sql;die;

	$arr=array(2=>$product_dept,3=>$marchentrArr,4=>$teamMemberArr);
	echo  create_list_view("list_view", "Job No,Style Ref.,Prod. Dept.,Marchant,Team Name", "100,110,110,150,150","680","360",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "0,0,product_dept,dealing_marchant,team_leader", $arr , "job_no_prefix_num,style_ref_no,product_dept,dealing_marchant,team_leader", "",'setFilterGrid("list_view",-1);','0,0,0,0,0','') ;
	exit();
}

if ($action=="po_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data);

?>
    <script>
	var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();

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
		var str=id.split("_");
		toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
		var strdt=str[2];
		str=str[1];

		if( jQuery.inArray(  str , selected_id ) == -1 ) {
			selected_id.push( str );
			selected_name.push( strdt );
		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == str  ) break;
			}
			selected_id.splice( i, 1 );
			selected_name.splice( i,1 );
		}
		var id = '';
		var ddd='';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
			ddd += selected_name[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );
		ddd = ddd.substr( 0, ddd.length - 1 );
		$('#txt_po_id').val( id );
		$('#txt_po_val').val( ddd );
	}

	</script>
     <input type="hidden" id="txt_po_id" />
     <input type="hidden" id="txt_po_val" />
     <?
	// echo $data[0];
	 if ($data[0]==0) $company_name=""; else $company_name=" and a.company_name='$data[0]'";
	 if ($data[1]==0) $buyer_name=""; else $buyer_name=" and a.buyer_name='$data[1]'";
	 if ($data[2]=="") $job_num=""; else $job_num=" and a.job_no_prefix_num='$data[2]'";
	if($db_type==0)
	{
		if(str_replace("'","",$data[3])!=0) $year_cond=" and year(a.insert_date)=".str_replace("'","",$data[3]).""; else $year_cond="";
	}
	else
	{
		if(str_replace("'","",$data[3])!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=".str_replace("'","",$data[3]).""; else $year_cond="";
	}

	$order_type=str_replace("'","",$data[4]);
	if($order_type==1)
	{
		$sql= "select b.id, b.po_number, b.job_no_mst, b.pub_shipment_date,c.gmts_item_id from  wo_po_details_mas_set_details c,wo_po_details_master a
		LEFT JOIN wo_po_break_down b ON a.job_no = b.job_no_mst
		AND b.is_deleted =0 AND b.status_active =1
		where  a.job_no=c.job_no and a.status_active=1 and a.is_deleted=0 $job_num $company_name $buyer_name $year_cond group by b.id, b.po_number, b.job_no_mst, b.pub_shipment_date,c.gmts_item_id order by po_number";
	}
	else
	{
		$sql= "select b.id, b.po_number, b.job_no_mst, b.pub_shipment_date,c.gmts_item_id from wo_po_details_master a, wo_po_break_down  b, wo_po_details_mas_set_details c where a.job_no=b.job_no_mst and a.job_no=c.job_no and a.status_active=1 and a.is_deleted=0 $job_num $company_name $buyer_name $year_cond group by b.id, b.po_number, b.job_no_mst, b.pub_shipment_date,c.gmts_item_id order by po_number";
	}
	//echo  $sql;die;
	$arr=array(3=>$garments_item);
	echo  create_list_view("list_view", "PO No.,Job No.,Pub Shipment Date,Item Name", "100,100,80,150","450","360",0, $sql , "js_set_value", "id,po_number", "", 1, "0,0,0,gmts_item_id", $arr , "po_number,job_no_mst,pub_shipment_date,gmts_item_id", "",'setFilterGrid("list_view",-1);','0,0,3,0','',1) ;
	exit();
}

if ($action=="report_generate")
{
	//extract($_REQUEST);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company=str_replace("'","",$cbo_company_id);
	$cbo_buyer=str_replace("'","",$cbo_buyer_id);
	$job_no=str_replace("'","",$txt_job_no);
	$hidd_job=str_replace("'","",$hidd_job_id);
	$hidd_po=str_replace("'","",$hidd_po_id);
	$txt_po_no=str_replace("'","",$txt_po_no);
	$txt_file_no=str_replace("'","",$txt_file_no);
	$txt_ref_no=str_replace("'","",$txt_ref_no);
	$txt_order_type=str_replace("'","",$txt_order_type);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$presantation_type=str_replace("'","",$cbo_presantation_type);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$cbo_season_id=str_replace("'","",$cbo_season_id);
	$cbo_brand_id=str_replace("'","",$cbo_brand_id);

	if ($cbo_company==0) $company_id=""; else $company_id=" and a.company_name=$cbo_company";
	if ($cbo_buyer==0) $buyer_id=""; else $buyer_id=" and a.buyer_name=$cbo_buyer";
	if ($cbo_brand_id==0) $brand_id_cond=""; else $brand_id_cond=" and a.brand_id=$cbo_brand_id";
	if ($cbo_season_id==0) $season_id_cond=""; else $season_id_cond=" and a.season_buyer_wise=$cbo_season_id";
	
	if ($job_no=="") $job_num=""; else $job_num=" and a.job_no='$job_no'";
	if ($job_no=="") $job_num_mst=""; else $job_num_mst=" and a.job_no_prefix_num=$job_no";
	if ($hidd_job==0) $job_id=""; else $job_id=" and a.id in ($hidd_job)";
	if ($txt_file_no=="") $file_cond=""; else $file_cond=" and c.file_no='$txt_file_no'";
	if ($txt_ref_no=="") $ref_cond=""; else $ref_cond=" and c.grouping='$txt_ref_no'";
	if ($job_no=="") $job_num=""; else $job_num=" and a.job_no='$job_no'";
	if ($txt_style_ref=="") $style_ref_cond=""; else $style_ref_cond=" and a.style_ref_no='$txt_style_ref'";

	if($hidd_po!='')
	{
		if ($hidd_po=="") $po_id=""; else $po_id=" and c.id in ( $hidd_po )";
	}
	else
	{
		if ($txt_po_no=="") $po_id=""; else $po_id=" and c.po_number='$txt_po_no'";
	}

	if($db_type==0)
	{
		if( $date_from=="" && $date_to=="" ) $pub_shipment_date=""; else $pub_shipment_date= " and d.country_ship_date between '".$date_from."' and '".$date_to."'";
		if( $date_from=="" && $date_to=="" ) $pub_shipment_date2=""; else $pub_shipment_date2= " and c.pub_shipment_date between '".$date_from."' and '".$date_to."'";
		if(str_replace("'","",$cbo_year)!=0) $year_cond=" and year(a.insert_date)=".str_replace("'","",$cbo_year).""; else $year_cond="";
	}
	else if($db_type==2)
	{
		if( $date_from=="" && $date_to=="" ) $pub_shipment_date=""; else $pub_shipment_date= " and d.country_ship_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
		if( $date_from=="" && $date_to=="" ) $pub_shipment_date2=""; else $pub_shipment_date2= " and c.pub_shipment_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";

		if(str_replace("'","",$cbo_year)!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=".str_replace("'","",$cbo_year).""; else $year_cond="";
	}

	$companyArr = return_library_array("select id,company_name from lib_company ","id","company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	
	$seasonArr = return_library_array("select id,season_name from lib_buyer_season ","id","season_name");
	$brand_nameArr = return_library_array("select id,brand_name from lib_buyer_brand ","id","brand_name");
	
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");

	$sql_coun_data = sql_select("select id,country_name,short_name from lib_country");
	foreach($sql_coun_data as $row){
		if($row[csf('short_name')])$shortName=' [<b>'.$row[csf('short_name')].'</b>]';else $shortName='';
		$countryArr[$row[csf('id')]]=$row[csf('country_name')].$shortName;
	}
	unset($sql_coun_data);
	//$countryArr = return_library_array("select id,country_name from lib_country ","id","country_name");
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
	  $sql_set= "select a.job_no,a.currency_id, b.smv_pcs,c.id as po_id,b.gmts_item_id from  wo_po_details_mas_set_details b,wo_po_details_master a
			LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
			AND c.is_deleted =0 AND c.status_active =1
			where  a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0   $company_id $buyer_id".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_id $job_num_mst $po_id $pub_shipment_date2 $brand_id_cond $season_id_cond $year_cond $job_num_mst $style_ref_cond $ref_cond $file_cond group by  a.job_no,c.id, b.smv_pcs,b.gmts_item_id,a.currency_id ";
	//echo  $sql_set;

	$sql_data_set = sql_select($sql_set);
	foreach( $sql_data_set as $row)
	{
		if($row[csf('smv_pcs')])
		{
		$smv_no_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('gmts_item_id')]]['smv_pcs']=$row[csf('smv_pcs')];
		}
	}
	//print_r($smv_no_array);
	$job_no_array=array();
	$po_details_array=array();
	$job_size_array=array();
	$po_item_array=array();
	$po_country_array=array();
	$po_country_ship_date_array=array();
	$po_color_array=array();
	$job_qnty_color_size_table_array=array();
	$job_size_tot_qnty_array=array();

	$po_color_size_qnty_array=array();
	$po_color_qnty_array=array();
	$po_qnty_array=array();
	$po_qnty_color_size_table_array=array();
	$po_size_tot_qnty_array=array();
	$po_item_qnty_array=array();
	$po_item_size_tot_qnty_array=array();
	$po_country_qnty_array=array();
	$po_country_size_tot_qnty_array=array();
	$po_ship_date_array=array();
	$po_file_no_array=array();
	$po_ref_no_array=array();

	ob_start();
	if($txt_order_type==1)
	{
		$sql="SELECT a.id as job_id, a.currency_id, a.job_no, a.company_name, a.buyer_name,a.brand_id,a.season_buyer_wise as season, a.style_ref_no, a.product_dept, a.location_name, a.dealing_marchant, (a.job_quantity*a.total_set_qnty) as job_quantity, a.order_uom, a.total_set_qnty, a.order_repeat_no, d.item_number_id as item_number_id, c.id as po_id, c.file_no, c.grouping, c.po_number, c.pub_shipment_date, c.shipment_date, c.po_received_date, (c.po_quantity*a.total_set_qnty) as po_quantity, c.excess_cut, c.plan_cut, c.unit_price, c.po_total_price, d.id, d.country_id, d.country_ship_date, d.cutup_date, d.size_number_id, d.color_number_id, d.order_quantity, d.order_rate, d.order_total, d.excess_cut_perc, d.plan_cut_qnty, d.size_order

	FROM wo_po_details_master a
	LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
	AND c.is_deleted =0
	AND c.status_active =1
	LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
	AND c.id = d.po_break_down_id
	AND d.is_deleted =0
	AND d.status_active =1
	WHERE
	a.is_deleted =0
	AND a.status_active =1
	$company_id $buyer_id".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_id $po_id $brand_id_cond $season_id_cond $pub_shipment_date $year_cond $job_num_mst $style_ref_cond $ref_cond $file_cond order by d.size_order, a.job_no, c.id, d.country_ship_date, d.color_order";
	}
	else
	{
		$sql="SELECT a.id as job_id, a.currency_id, a.job_no, a.company_name, a.buyer_name,a.brand_id,a.season_buyer_wise as season, a.style_ref_no, a.product_dept, a.location_name, a.dealing_marchant, (a.job_quantity*a.total_set_qnty) as job_quantity, a.order_uom, a.total_set_qnty, a.order_repeat_no, d.item_number_id as item_number_id, c.id as po_id,c.file_no, c.grouping, c.po_number, c.pub_shipment_date, c.shipment_date, c.po_received_date, (c.po_quantity*a.total_set_qnty) as po_quantity, c.excess_cut, c.plan_cut, c.unit_price, c.po_total_price, d.id,d.country_id, d.country_ship_date, d.cutup_date, d.size_number_id, d.color_number_id, d.order_quantity, d.order_rate, d.order_total, d.excess_cut_perc, d.plan_cut_qnty, d.size_order

	FROM wo_po_details_master a,wo_po_break_down c
	LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
	AND c.id = d.po_break_down_id
	AND d.is_deleted =0
	AND d.status_active =1
	WHERE a.job_no = c.job_no_mst 	AND c.is_deleted =0 AND c.status_active =1 and
	a.is_deleted =0 AND a.status_active =1
	$company_id $buyer_id".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_id $po_id $pub_shipment_date $brand_id_cond $season_id_cond $year_cond $job_num_mst $style_ref_cond $ref_cond $file_cond order by d.size_order, a.job_no, d.color_order, c.id, d.country_ship_date";
	}

	//echo $sql;
	$sql_data = sql_select($sql);
	foreach( $sql_data as $row)
	{

		$job_no_array[$row[csf('job_no')]]=array("job_no"=>$row[csf('job_no')],"job_quantity"=>$row[csf('job_quantity')],"company_name"=>$row[csf('company_name')],"buyer_name"=>$row[csf('buyer_name')],"style_ref_no"=>$row[csf('style_ref_no')],"product_dept"=>$row[csf('product_dept')],"dealing_marchant"=>$row[csf('dealing_marchant')],"job_id"=>$row[csf('job_id')],"order_uom"=>$row[csf('order_uom')],"brand_id"=>$row[csf('brand_id')],"season"=>$row[csf('season')]);
		$po_details_array[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('po_number')];
		$po_file_no_array[$row[csf('job_no')]][$row[csf('po_id')]]['file']=$row[csf('file_no')];
		$po_ref_no_array[$row[csf('job_no')]][$row[csf('po_id')]]['ref']=$row[csf('grouping')];
		$job_size_array[$row[csf('job_no')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$po_item_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]]=$row[csf('item_number_id')];
		$po_country_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$po_ship_date_array[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('shipment_date')];
		$po_country_ship_date_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]=$row[csf('country_ship_date')];
		$po_color_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]] [$row[csf('color_number_id')]]=$row[csf('color_number_id')];

		$job_qnty_color_size_table_array[$row[csf('job_no')]]+=$row[csf('order_quantity')];
		$job_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
		$po_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('po_quantity')];
		$po_qnty_price_array[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('unit_price')];

		$po_qnty_color_size_table_array[$row[csf('job_no')]][$row[csf('po_id')]]+=$row[csf('order_quantity')];
		$po_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];

		$po_item_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]]+=$row[csf('order_quantity')];
		$po_item_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];

		$po_country_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]+=$row[csf('order_quantity')];
		$po_country_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];

		$po_color_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]] [$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
		$po_color_size_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]] [$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('order_quantity')];
		//$tmp_job[$row[csf('job_no')]]=$row[csf('job_no')];
		//$size_array[$row[csf("job_no")]][$row[csf("size_order")]]=$row[csf("size_number_id")];

		$size_array[$row[csf("job_no")]][$row[csf("size_order")]]=$row[csf("size_number_id")];
	}

	//print_r($size_array);

	$po_rowspan_arr=array();
	$item_rowspan_arr=array();
	$country_rowspan_arr=array();
	foreach($po_color_array as $job)
	{
		foreach($job as $po_id=>$po_value)
		{
			$po_rowspan=0;
			foreach($po_value as $item_id =>$item_value)
			{
				$item_rowspan=0;
				foreach($item_value as $country_id =>$country_value)
				{
					$country_rowspan=1;
					foreach($country_value as $color_id =>$color_value)
					{
						$po_rowspan++;

						$item_rowspan++;
						$country_rowspan_arr[$po_id][$item_id][$country_id]=$country_rowspan;
						$country_rowspan++;
					}
					$po_rowspan++;
					$po_rowspan_arr[$po_id]=$po_rowspan;
					$item_rowspan++;

					$item_rowspan_arr[$po_id][$item_id]=$item_rowspan;
				}
				$po_rowspan++;
				$po_rowspan_arr[$po_id]=$po_rowspan;
			}
			//$po_rowspan++;
		}
		//$po_rowspan++;
	}

	?>

    <table id="scroll_body" align="center" style="height:auto; width:1240px; margin:0 auto; padding:0;">
    <tr>
    <td width="1260">
	<?
	foreach($job_no_array as $rdata=>$det)
	{
		//ksort($size_array[$det["job_no"]]);
	?>
        <br/>
        <table width="1180px" align="center" border="1" rules="all" id="table_header_1">
            <tr style="background-color:#FFF">
                <td width="60" align="right">Job No: </td><td width="90" onclick="openmypage_job_color_size('requires/size_and_color_break_report_controller.php?action=job_color_size&job_no=<? echo $det["job_no"] ?>','Job Color Size')"><a href="##"><? echo $det['job_no']; ?></a></td>
                <td width="60" align="right">Job Qnty: </td><td width="90"><? echo $det['job_quantity']."(Pcs)"; ?></td>
                <td width="60" align="right">Company: </td><td width="90"><? echo $companyArr[$det['company_name']]; ?></td>
                <td width="60" align="right">Buyer: </td><td width="85"><? echo $buyerArr[$det['buyer_name']]; ?></td>
                <td width="60" align="right">Brand: </td><td width="85"><? echo $brand_nameArr[$det['brand_id']]; ?></td>
                 <td width="60" align="right">Season: </td><td width="85"><? echo $seasonArr[$det['season']]; ?></td>
                <td width="65" align="right">Style Ref.: </td><td width="85"><? echo $det['style_ref_no']; ?></td>
                <td width="70" align="right">Prod. Dept.: </td><td width="80"><? echo $product_dept[$det['product_dept']]; ?></td>
                <td width="60" align="right">Merchant: </td><td width="90"><? echo $marchentrArr[$det['dealing_marchant']]; ?></td>
                <td width="60" align="right">Ord. Re. No: </td><td width="90"><? echo $det['order_repeat_no']; ?></td>
            </tr>
        </table>
        <br/>
        <table width="1180px" align="center" border="1" rules="all" class="rpt_table" id="color_size">
            <thead>
                <tr>
                    <!--<th width="60">Sl</th>-->
                    <th width="60">PO Number</th>
                    <? if($presantation_type==1){ ?>
                    <th width="60">File No</th>
                    <th width="70">Ref. No/Master Style</th>
                    <? } ?>
                    <th width="60">PO Qty and Price</th>
                    <th width="70">Item and SMV </th>
                    <th width="60">Country</th>
                    <th width="60">Color</th>
                    <th width="60">Color Total</th>
                    <?
                    /*echo "<pre>";
                    print_r($size_array[$det['job_no']]);
                    die;*/
					foreach($size_array[$det['job_no']] as $key=>$value)
                    {
						if($value !="")
						{
					?>
                    <th width="60"><? echo $itemSizeArr[$value];?></th>
                    <?
						}
					}
					?>
                </tr>
            </thead>
            <?

            foreach($po_details_array[$det['job_no']] as $key=>$value)
            {
                $posl=1;
                foreach($po_item_array [$det['job_no']][$key] as $item_key=>$item_value)
                {
                    $itemsl=1;
                    foreach($po_country_array [$det['job_no']][$key][$item_value] as $country_key=>$country_value)
                    {
						//echo count($po_country_array [$det[csf('job_no')]][$key][$item_key]);
                        $countrysl=1;
                       //print_r($po_color_array [$det['job_no']][$key][$item_value][$country_value]);
                        foreach($po_color_array [$det['job_no']][$key][$item_value][$country_value] as $color_key=>$color_value)
                        {
							if($countrysl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        ?>
                            <tr bgcolor="<? echo $bgcolor;?>">
                                <!--<td valign="middle" rowspan="" ><?  //echo $itemsl;?></td>-->
                                <?
                                if($posl==1)
                                {
                                ?>
                                <td  align="center" valign="middle" rowspan="<? echo $po_rowspan_arr[$key]; ?>" ><?  echo $value; echo "<br/> Ship Date:".change_date_format($po_ship_date_array[$det['job_no']][$key],"dd-mm-yyyy","-"); echo "<br/>".date('l', strtotime($po_ship_date_array[$det['job_no']][$key])); ?></td>
                                <?
                                }
								if($presantation_type==1)
								{
									if($posl==1)
									{
									?>
									<td  align="center" valign="middle" rowspan="<? echo $po_rowspan_arr[$key]; ?>" ><?  echo $po_file_no_array[$det['job_no']][$key]['file']; ?></td>
									<?
									}
									 if($posl==1)
									{
									?>
									<td  align="center" valign="middle" rowspan="<? echo $po_rowspan_arr[$key]; ?>" ><?  echo $po_ref_no_array[$det['job_no']][$key]['ref']; ?></td>
									<?
									}
								}
                                if($posl==1)
                                {
									//$po_price=$po_qnty_price_array[$row[csf('job_no')]][$key];//old
									$po_price = $po_qnty_price_array[$det['job_no']][$key];
									$qnty = $po_qnty_array[$det['job_no']][$key] ? $po_qnty_array[$det['job_no']][$key] : 0;
                                ?>

                                <td align="center" valign="middle" rowspan="<? echo $po_rowspan_arr[$key]; ?>"><? echo  $qnty.' (Pcs) <br>'.number_format($po_price,2).'  <b>'.$currency[$row[csf("currency_id")]].'</b>'; ?></td>
                                <?
                                }
                                if($itemsl==1)
                                {
									$item_smv_pcs=$smv_no_array[$det[('job_no')]][$key][$item_value]['smv_pcs'];
									//$item_smv_pcs2=$smv_no_array2[$row[csf('job_no')]][$key][$item_value]['smv_pcs'];
									//echo $item_smv_pcs2.'d';

                                ?>
                                <td align="center" title="<? echo $det[('job_no')].'='.$key.'='.$item_value.',smv='.$item_smv_pcs;?>" valign="middle" rowspan="<?  echo $item_rowspan_arr[$key][$item_key]; ?>" ><?  echo $garments_item[$item_value].'<br>'.$item_smv_pcs;?></td>
                                <?
                                }
                                if($countrysl==1)
                                {
                                ?>
                                <td align="center" valign="middle" rowspan="<?  echo $country_rowspan_arr[$key][$item_key][$country_key]; ?>"><?  echo $countryArr[$country_value]."<br/>".change_date_format($po_country_ship_date_array [$det['job_no']][$key][$item_value][$country_value],"dd-mm-yyyy","-")."<br/>".date('l', strtotime($po_country_ship_date_array [$det['job_no']][$key][$item_value][$country_value]));  ?></td>
                                <?
                                }
                                ?>
                                <td><?  echo $colorArr[$color_value] ;?></td>
                                <td align="right"><? echo $po_color_qnty_array [$det['job_no']][$key][$item_value][$country_value][$color_value]; ?></td>
                                <?
								foreach($size_array[$det['job_no']] as $key_s=>$value_s)
								{
									if($value_s !="")
						            {
								?>
								<td align="right"><? echo $po_color_size_qnty_array [$det['job_no']][$key][$item_value][$country_value][$color_value][$value_s]?></td>
								<?
									}
								}
								?>
                            </tr>
                            <?
                            $posl++;
                            $itemsl++;
                            $countrysl++;
                        }
						?>
                 <tr style="font-weight:bold; font-size:12px">
                 <td colspan="2">Country Total:</td>
                 <td colspan="" align="right"><? echo $po_country_qnty_array[$det['job_no']][$key][$item_key][$country_key] ?></td>
                 <?
				foreach($job_size_array[$det['job_no']] as $key_s=>$value_s)
				{
				if($value_s !="")
				{
					//$po_country_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]]
				?>
				<td align="right"><? echo $po_country_size_tot_qnty_array [$det['job_no']][$key][$item_value][$country_value][$value_s]?></td>
				<?
				}
				}
				?>
                </tr>
                    <?
                    }
					?>
                <tr style="font-weight:bold; font-size:12px">
                <td colspan="3">Item Total:</td>
                <td colspan="" align="right"><? echo $po_item_qnty_array[$det['job_no']][$key][$item_key] ?></td>
                <?
				foreach($size_array[$det['job_no']] as $key_s=>$value_s)
				{
				if($value_s !="")
				{
					//$po_item_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('size_number_id')]]
				?>
				<td align="right"><? echo $po_item_size_tot_qnty_array [$det['job_no']][$key][$item_value][$value_s]?></td>
				<?
				}
				}
				?>
                </tr>
                <?

                }
				if($presantation_type==1) $colspn=7; else $colspn=5;
				?>
                <tr style="font-weight:bold; font-size:12px">
                <td colspan="<? echo $colspn; ?>">Po Total:</td>

                   <td  align="right"><? echo $po_qnty_color_size_table_array [$det['job_no']][$key]?></td>

                 <?
				foreach($size_array[$det['job_no']] as $key_s=>$value_s)
				{
				if($value_s !="")
				{
					//$po_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('size_number_id')]]
				?>
				<td align="right"><? echo $po_size_tot_qnty_array [$det['job_no']][$key][$value_s]?></td>
				<?
				}
				}
				?>
                </tr>

                <?
            }
            ?>
            <tr style="font-weight:bold; font-size:12px">
                <td colspan="<? echo $colspn; ?>">Grand Total:</td>

                   <td align="right"><? echo $job_qnty_color_size_table_array [$det['job_no']];?></td>

                 <?
				foreach($size_array[$det['job_no']] as $key_s=>$value_s)
				{ //$job_size_array[$row[csf('job_no')]][$row[csf('size_number_id')]]
				if($value_s !="")
			    {
				?>
				<td align="right"><? echo $job_size_tot_qnty_array [$det['job_no']][$value_s]?></td>
				<?
				}
				}
				?>
                </tr>
        </table>

	<?
	}
	?>

       </td>
       </tr>
       </table>


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
	echo "$total_data####$filename";
	exit();
}


//report for cut off date
if($action=="report_generate_cutoff")
{
	extract($_REQUEST);
	$cbo_company=str_replace("'","",$cbo_company_id);
	$cbo_buyer=str_replace("'","",$cbo_buyer_id);
	$job_no=str_replace("'","",$txt_job_no);
	$hidd_job=str_replace("'","",$hidd_job_id);
	$hidd_po=str_replace("'","",$hidd_po_id);
	$txt_po_no=str_replace("'","",$txt_po_no);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$txt_file_no=str_replace("'","",$txt_file_no);
	$txt_ref_no=str_replace("'","",$txt_ref_no);
	$txt_order_types=str_replace("'","",$txt_order_type);
	$presantation_type=str_replace("'","",$cbo_presantation_type);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	if ($cbo_brand_id==0) $brand_id_cond=""; else $brand_id_cond=" and a.brand_id=$cbo_brand_id";
	if ($cbo_season_id==0) $season_id_cond=""; else $season_id_cond=" and a.season_buyer_wise=$cbo_season_id";


	if ($cbo_company==0) $company_id=""; else $company_id=" and a.company_name=$cbo_company";
	if ($cbo_buyer==0) $buyer_id=""; else $buyer_id=" and a.buyer_name=$cbo_buyer";
	if ($job_no=="") $job_num=""; else $job_num=" and a.job_no='$job_no'";
	//if ($job_no=="") $job_num_mst=""; else $job_num_mst=" and b.job_no_mst=$job_no";
	if ($job_no=="") $job_num_mst=""; else $job_num_mst=" and a.job_no_prefix_num=$job_no";
	if ($txt_file_no=="") $file_cond=""; else $file_cond=" and c.file_no='$txt_file_no'";
	if ($txt_ref_no=="") $ref_cond=""; else $ref_cond=" and c.grouping='$txt_ref_no'";
	if ($hidd_job==0) $job_id=""; else $job_id=" and a.id in ($hidd_job)";
	//if ($hidd_po=="") $po_id=""; else $po_id=" and c.id in ( $hidd_po )";
	if ($txt_style_ref=="") $style_ref_cond=""; else $style_ref_cond=" and a.style_ref_no='$txt_style_ref'";

	if($hidd_po!='')
	{
	if ($hidd_po=="") $po_id=""; else $po_id=" and c.id in ( $hidd_po )";
	}
	else
	{
		if ($txt_po_no=="") $po_id=""; else $po_id=" and c.po_number='$txt_po_no'";
	}
	if($db_type==0)
	{
	if( $date_from=="" && $date_to=="" ) $pub_shipment_date=""; else $pub_shipment_date= " and d.cutup_date between '".$date_from."' and '".$date_to."'";
	if(str_replace("'","",$cbo_year)!=0) $year_cond=" and year(a.insert_date)=".str_replace("'","",$cbo_year).""; else $year_cond="";
	}
	if($db_type==2)
	{
	if( $date_from=="" && $date_to=="" ) $pub_shipment_date=""; else $pub_shipment_date= " and d.cutup_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
	if(str_replace("'","",$cbo_year)!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=".str_replace("'","",$cbo_year).""; else $year_cond="";
	}

	$companyArr = return_library_array("select id,company_name from lib_company ","id","company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
	//$countryArr = return_library_array("select id,country_name from lib_country ","id","country_name");
	$seasonArr = return_library_array("select id,season_name from lib_buyer_season ","id","season_name");
	$brand_nameArr = return_library_array("select id,brand_name from lib_buyer_brand ","id","brand_name");

	$sql_coun_data = sql_select("select id,country_name,short_name from lib_country");
	foreach($sql_coun_data as $row){
		if($row[csf('short_name')])$shortName=' [<b>'.$row[csf('short_name')].'</b>]';else $shortName='';
		$countryArr[$row[csf('id')]]=$row[csf('country_name')].$shortName;
	}
	unset($sql_coun_data);



	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
	  $sql_set= "select a.job_no, e.smv_pcs,c.id as po_id,e.gmts_item_id,d.cutup_date from  wo_po_details_mas_set_details e,wo_po_color_size_breakdown d ,wo_po_details_master a
			LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
			AND c.is_deleted =0 AND c.status_active =1
			where  a.job_no=e.job_no and a.job_no = d.job_no_mst and e.job_no = d.job_no_mst  and a.status_active=1 and a.is_deleted=0 $company_id $buyer_id".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $po_id $pub_shipment_date $brand_id_cond $season_id_cond $job_num_mst $year_cond $file_cond $style_ref_cond $ref_cond group by  a.job_no,c.id, e.smv_pcs,e.gmts_item_id,d.cutup_date  ";




	$sql_data_set = sql_select($sql_set);
	foreach( $sql_data_set as $row)
	{
		$smv_no_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('cutup_date')]][$row[csf('gmts_item_id')]]['smv_pcs']=$row[csf('smv_pcs')];
	}

	$job_no_array=array();
	$po_details_array=array();
	$job_size_array=array();
	$po_item_array=array();
	$po_country_array=array();
	$po_country_ship_date_array=array();
	$po_color_array=array();
	$job_qnty_color_size_table_array=array();
	$job_size_tot_qnty_array=array();

	$po_cut_off_array=array();
	$po_color_size_qnty_array=array();
	$po_color_qnty_array=array();
	$po_qnty_array=array();
	$po_qnty_color_size_table_array=array();
	$po_size_tot_qnty_array=array();
	$po_item_qnty_array=array();
	$po_item_size_tot_qnty_array=array();
	$po_country_qnty_array=array();
	$po_country_size_tot_qnty_array=array();
	$po_ship_date_array=array();
	$po_file_no_array=array();
	$po_ref_no_array=array();

	 /*$sql="SELECT a.id as job_id, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.product_dept, a.location_name, a.dealing_marchant, (a.job_quantity*a.total_set_qnty) as job_quantity , a.order_uom, a.total_set_qnty, b.id as po_id, b.po_number, b.pub_shipment_date, b.shipment_date, b.po_received_date, (b.po_quantity*a.total_set_qnty) as po_quantity, b.excess_cut, b.plan_cut, b.unit_price, b.po_total_price, c.item_number_id, c.country_id, c.size_number_id, c.color_number_id, c.order_quantity, c.order_rate, c.order_total, c.excess_cut_perc, c.plan_cut_qnty
	FROM wo_po_details_master a
	LEFT JOIN wo_po_break_down b ON a.job_no = b.job_no_mst
	AND b.is_deleted =0
	AND b.status_active =1
	LEFT JOIN wo_po_color_size_breakdown c ON b.job_no_mst = c.job_no_mst
	AND b.id = c.po_break_down_id
	AND c.is_deleted =0
	AND c.status_active =1
	WHERE
	a.is_deleted =0
	AND a.status_active =1
	$company_id $buyer_id $job_id $po_id $pub_shipment_date
	";*/
	ob_start();
	if($txt_order_types==1)
	{
	 /* $sql="SELECT distinct a.id as job_id, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.product_dept, a.location_name, a.dealing_marchant, (a.job_quantity*a.total_set_qnty) as job_quantity , a.order_uom, a.total_set_qnty,d.item_number_id as item_number_id, c.id as po_id,c.file_no,c.grouping, c.po_number, c.pub_shipment_date, c.shipment_date, c.po_received_date, (c.po_quantity*a.total_set_qnty) as po_quantity, c.excess_cut, c.plan_cut, c.unit_price, c.po_total_price,d.id, d.cutup, d.country_id,d.country_ship_date,d.cutup_date, d.size_number_id,d.size_order, d.color_number_id, d.order_quantity, d.order_rate, d.order_total, d.excess_cut_perc, d.plan_cut_qnty
	FROM wo_po_details_master a

	LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
	AND c.is_deleted =0
	AND c.status_active =1
	LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
	AND c.id = d.po_break_down_id
	AND d.is_deleted =0
	AND d.status_active =1
	WHERE
	d.id!=0 and
	a.is_deleted =0
	AND a.status_active =1
	$company_id $buyer_id $job_id $po_id $pub_shipment_date $job_num_mst $year_cond $file_cond $style_ref_cond $ref_cond order by a.job_no,c.id,d.cutup_date,d.cutup,d.color_order,d.size_order,d.id";*/
	$sql="SELECT a.id as job_id, a.currency_id, a.job_no, a.company_name, a.buyer_name, a.brand_id,a.season_buyer_wise as season, a.style_ref_no, a.product_dept, a.location_name, a.dealing_marchant, (a.job_quantity*a.total_set_qnty) as job_quantity, a.order_uom, a.total_set_qnty, a.order_repeat_no, d.item_number_id as item_number_id, c.id as po_id, c.file_no, c.grouping, c.po_number, c.pub_shipment_date, c.shipment_date, c.po_received_date, (c.po_quantity*a.total_set_qnty) as po_quantity, c.excess_cut, c.plan_cut, c.unit_price, c.po_total_price, d.id, d.cutup, d.country_id, d.country_ship_date, d.cutup_date, d.size_number_id, d.color_number_id, d.order_quantity, d.order_rate, d.order_total, d.excess_cut_perc, d.plan_cut_qnty, d.size_order

	FROM wo_po_details_master a
	LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
	AND c.is_deleted =0
	AND c.status_active =1
	LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
	AND c.id = d.po_break_down_id
	AND d.is_deleted =0
	AND d.status_active =1
	WHERE
	a.is_deleted =0
	AND a.status_active =1
	$company_id $buyer_id".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_id $po_id $pub_shipment_date $brand_id_cond $season_id_cond $year_cond $job_num_mst $style_ref_cond $ref_cond $file_cond order by d.size_order, a.job_no, c.id, d.country_ship_date, d.color_order";
	}
	else
	{
		 $sql="SELECT distinct a.id as job_id, a.job_no, a.company_name, a.buyer_name, a.brand_id,a.season_buyer_wise as season, a.style_ref_no, a.product_dept, a.location_name, a.dealing_marchant, (a.job_quantity*a.total_set_qnty) as job_quantity , a.order_uom, a.total_set_qnty,d.item_number_id as item_number_id, c.id as po_id,c.file_no,c.grouping, c.po_number, c.pub_shipment_date, c.shipment_date, c.po_received_date, (c.po_quantity*a.total_set_qnty) as po_quantity, c.excess_cut, c.plan_cut, c.unit_price, c.po_total_price,d.id, d.cutup, d.country_id,d.country_ship_date,d.cutup_date, d.size_number_id,d.size_order, d.color_number_id, d.order_quantity, d.order_rate, d.order_total, d.excess_cut_perc, d.plan_cut_qnty
	FROM wo_po_details_master a,wo_po_break_down c
	LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
	AND c.id = d.po_break_down_id
	AND d.is_deleted =0
	AND d.status_active =1
	WHERE  a.job_no=c.job_no_mst  and c.is_deleted=0 and c.status_active=1 and
	d.id!=0 and a.is_deleted=0 AND a.status_active =1
	$company_id $buyer_id $job_id $po_id $pub_shipment_date $job_num_mst $year_cond $file_cond $brand_id_cond $season_id_cond $style_ref_cond $ref_cond order by a.job_no,c.id,d.cutup_date,d.cutup,d.color_order,d.size_order,d.id ";
	}
	//echo $sql;die;

	$sql_data = sql_select($sql);
	foreach( $sql_data as $row)
	{
		$job_no_array[$row[csf('job_no')]]=array("job_no"=>$row[csf('job_no')],"job_quantity"=>$row[csf('job_quantity')],"company_name"=>$row[csf('company_name')],"buyer_name"=>$row[csf('buyer_name')],"style_ref_no"=>$row[csf('style_ref_no')],"product_dept"=>$row[csf('product_dept')],"dealing_marchant"=>$row[csf('dealing_marchant')],"job_id"=>$row[csf('job_id')],"order_uom"=>$row[csf('order_uom')],"brand_id"=>$row[csf('brand_id')],"season"=>$row[csf('season')]);
		$po_details_array[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('po_number')];
		$po_file_no_array[$row[csf('job_no')]][$row[csf('po_id')]]['file']=$row[csf('file_no')];
		$po_ref_no_array[$row[csf('job_no')]][$row[csf('po_id')]]['ref']=$row[csf('grouping')];
		$po_cutup_date_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('cutup_date')]]=$row[csf('cutup_date')];
		$job_size_array[$row[csf('job_no')]][$row[csf('size_order')]]=$row[csf('size_number_id')];
		$po_item_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('cutup_date')]][$row[csf('item_number_id')]]=$row[csf('item_number_id')];
		$po_country_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('cutup_date')]][$row[csf('item_number_id')]][$row[csf('cutup')]][$row[csf('country_id')]]=$row[csf('country_id')];
		//$po_ship_date_array[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('cutup_date')];
		$po_country_ship_date_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('cutup_date')]][$row[csf('item_number_id')]][$row[csf('cutup')]][$row[csf('country_id')]]=$row[csf('country_ship_date')];
		$po_color_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('cutup_date')]][$row[csf('item_number_id')]][$row[csf('cutup')]][$row[csf('country_id')]] [$row[csf('color_number_id')]]=$row[csf('color_number_id')];

		$job_qnty_color_size_table_array[$row[csf('job_no')]]+=$row[csf('order_quantity')];
		$job_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
		$po_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('cutup_date')]]+=$row[csf('order_quantity')];
		$po_price_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('cutup_date')]]=$row[csf('unit_price')];
		$po_cut_off_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('cutup_date')]][$row[csf('item_number_id')]][$row[csf('cutup')]]=$row[csf('cutup')];
		$po_qnty_color_size_table_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('cutup_date')]]+=$row[csf('order_quantity')];
		$po_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('cutup_date')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];

		$po_item_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('cutup_date')]][$row[csf('item_number_id')]]+=$row[csf('order_quantity')];
		$po_cut_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('cutup_date')]][$row[csf('item_number_id')]][$row[csf('cutup')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];

		$po_item_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('cutup_date')]][$row[csf('item_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
		$po_cutoff_tot_qty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('cutup_date')]][$row[csf('item_number_id')]][$row[csf('cutup')]]+=$row[csf('order_quantity')];
		$po_country_id_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('cutup_date')]][$row[csf('item_number_id')]][$row[csf('cutup')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$po_country_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('cutup_date')]][$row[csf('item_number_id')]][$row[csf('cutup')]][$row[csf('country_id')]]+=$row[csf('order_quantity')];
		$po_country_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('cutup_date')]][$row[csf('item_number_id')]][$row[csf('cutup')]][$row[csf('country_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];

		$po_color_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('cutup_date')]][$row[csf('item_number_id')]][$row[csf('cutup')]][$row[csf('country_id')]] [$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
		$po_color_size_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('cutup_date')]][$row[csf('item_number_id')]][$row[csf('cutup')]][$row[csf('country_id')]] [$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('order_quantity')];
	}

	//print_r($po_cutup_date_array);

	$po_rowspan_arr=array();
	$item_rowspan_arr=array();
	$country_rowspan_arr=array();
	$cutoff_rowspan_arr=array();
	foreach($po_color_array as $job)
	{
		foreach($job as $po_id=>$po_value)
		{
		 foreach($po_value as $cut_up_id=>$cut_up_value)
	     {
			$po_rowspan=0;
			foreach($cut_up_value as $item_id =>$item_value)
			{
				$item_rowspan=0;
				foreach($item_value as $cutoff_id =>$cutoff_value)
					{
					$cutoff_rowspan=0;
					foreach($cutoff_value as $country_id =>$country_value)
					{
						$country_rowspan=1;
						foreach($country_value as $color_id =>$color_value)
						{

							$po_rowspan++;

							$item_rowspan++;
							$cutoff_rowspan++;
							$country_rowspan_arr[$po_id][$cut_up_id][$item_id][$cutoff_id][$country_id]=$country_rowspan;
							$country_rowspan++;
						}
						$po_rowspan++;
						$po_rowspan_arr[$po_id][$cut_up_id]=$po_rowspan;
						$item_rowspan++;
						$cutoff_rowspan++;
						$cutoff_rowspan_arr[$po_id][$cut_up_id][$item_id][$cutoff_id]=$cutoff_rowspan;

					}
					$po_rowspan++;
					$po_rowspan_arr[$po_id][$cut_up_id]=$po_rowspan;
					$item_rowspan++;

					$item_rowspan_arr[$po_id][$cut_up_id][$item_id]=$item_rowspan;

				}
				$po_rowspan++;
				$po_rowspan_arr[$po_id][$cut_up_id]=$po_rowspan;
			}
		  }//$po_rowspan++;
		}
		//$po_rowspan++;
	}
	//print_r($item_rowspan_arr)
	?>


    <table id="scroll_body" align="center" style="height:auto; width:1230px; margin:0 auto; padding:0;">
    <tr>
    <td width="1250">

	<?
	foreach($job_no_array as $rdata=>$det)
	{
		//ksort($job_size_array[$det['job_no']]);
	?>
        <br/>
        <table width="1180px" align="center" border="1" rules="all">
            <tr style="background-color:#FFF">
                <td width="60" align="right">Job No: </td><td width="90" onclick="openmypage_job_color_size('requires/size_and_color_break_report_controller.php?action=job_color_size_cut&job_no=<? echo $det["job_no"] ?>','Job Color Size')"><a href="##"><? echo $det['job_no']; ?></a></td>
                <td width="60" align="right">Job Qnty: </td><td width="90"><? echo $det['job_quantity']."(Pcs)"; ?></td>
                <td width="60" align="right">Company: </td><td width="90"><? echo $companyArr[$det['company_name']]; ?></td>
                <td width="60" align="right">Buyer: </td><td width="85"><? echo $buyerArr[$det['buyer_name']]; ?></td>
                 <td width="60" align="right">Brand: </td><td width="85"><? echo $brand_nameArr[$det['brand_id']]; ?></td>
 				 <td width="60" align="right">Season: </td><td width="85"><? echo $seasonArr[$det['season']]; ?></td>
  
                <td width="65" align="right">Style Ref.: </td><td width="85"><? echo $det['style_ref_no']; ?></td>
                <td width="70" align="right">Prod. Dept.: </td><td width="80"><? echo $product_dept[$det['product_dept']]; ?></td>
                <td width="60" align="right">Merchant: </td><td width="90"><? echo $marchentrArr[$det['dealing_marchant']]; ?></td>
            </tr>
        </table>
        <br/>
        <table width="1180px" align="center" border="1" rules="all" class="rpt_table" >
            <thead>
                <tr>
                    <!--<th width="60">Sl</th>-->
                    <th width="60">PO Number</th>
                    <? if($presantation_type==1){ ?>
                    <th width="60">File No</th>
                    <th width="70">Ref. No/Master Style</th>
                    <? } ?>
                    <th width="60">PO Qnty and Price</th>
                    <th width="70">Item </th>
                    <th width="70">Cut-off </th>
                    <th width="60">Country</th>
                    <th width="60">Color</th>
                    <th width="60">Color Total</th>
                    <?

					foreach($job_size_array[$det['job_no']] as $key=>$value)
                    {
						if($value !="")
						{
					?>
                    <th width="60"><? echo $itemSizeArr[$value];?></th>
                    <?
						}
					}
					?>
                </tr>
            </thead>
            <?

            foreach($po_details_array[$det['job_no']] as $key=>$value)
            {
			   foreach($po_cutup_date_array[$det['job_no']][$key] as $cut_up_key=>$cut_up_value)
               {
                $posl=1;
                foreach($po_item_array [$det['job_no']][$key][$cut_up_value] as $item_key=>$item_value)
                {

                    $itemsl=1;
					   foreach($po_cut_off_array [$det['job_no']][$key][$cut_up_value][$item_value] as $cutoff_key=>$cutoff_value)
						{

							$cutoffsl=1;
							foreach($po_country_array [$det['job_no']][$key][$cut_up_value][$item_value][$cutoff_value] as $country_key=>$country_value)
							{
								//echo count($po_country_array [$det[csf('job_no')]][$key][$item_key]);
								$countrysl=1;
								foreach($po_color_array [$det['job_no']][$key][$cut_up_value][$item_value][$cutoff_value][$country_value] as $color_key=>$color_value)
								{
									if($countrysl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
									<tr bgcolor="<? echo $bgcolor;?>" >
										<!--<td valign="middle" rowspan="" ><?  //echo $itemsl;?></td>-->
										<?
										if($posl==1)
										{
										?>
										<td  align="center" valign="middle" rowspan="<? echo $po_rowspan_arr[$key][$cut_up_key]; ?>" ><?  echo $value; echo "<br/> Cut-off Date:<br/>".change_date_format($po_cutup_date_array[$det['job_no']][$key][$cut_up_value],"dd-mm-yyyy","-"); if($po_cutup_date_array[$det['job_no']][$key][$cut_up_value]!='0000-00-00') echo "<br/>".date('l', strtotime($po_cutup_date_array[$det['job_no']][$key][$cut_up_value])); ?></td>
										<?
										}
										if($presantation_type==1)
										{
											if($posl==1)
											{
											?>
											<td  align="center" valign="middle" rowspan="<? echo $po_rowspan_arr[$key][$cut_up_key]; ?>" ><?  echo $po_file_no_array[$det['job_no']][$key]['file']; ?></td>
											<?
											}
											if($posl==1)
											{
											?>
											<td  align="center" valign="middle" rowspan="<? echo $po_rowspan_arr[$key][$cut_up_key]; ?>" ><?  echo  $po_ref_no_array[$det['job_no']][$key]['ref']; ?></td>
											<?
											}
										}
										if($posl==1)
										{
											//po_price_array
											$po_price=$po_price_array[$det['job_no']][$key][$cut_up_value];
										?>
										<td align="center" valign="middle" rowspan="<? echo $po_rowspan_arr[$key][$cut_up_key]; ?>"><? echo  $po_qnty_array[$det['job_no']][$key][$cut_up_value].' (Pcs)'.'<br>'.number_format($po_price,2).'<b> USD</b>'; ?></td>
										<?
										}
										if($itemsl==1)
										{
											$item_smv_pcs=$smv_no_array[$det[('job_no')]][$key][$cut_up_key][$item_value]['smv_pcs'];

										?>
										<td align="center" valign="middle" rowspan="<? echo $item_rowspan_arr[$key][$cut_up_key][$item_key]; ?>" title="<?="SMV=".$item_smv_pcs; ?>" ><?  echo $garments_item[$item_value].'<br/>'.number_format($item_smv_pcs,3);?></td>
										<?
										}
										if($cutoffsl==1)
										{
										?>
										<td align="center" valign="middle" rowspan="<?  echo $cutoff_rowspan_arr[$key][$cut_up_key][$item_key][$cutoff_key]; ?>" ><?  echo $cut_up_array[$po_cut_off_array[$det['job_no']][$key][$cut_up_value][$item_value][$cutoff_value]] ;?></td>
										<?
										}
										if($countrysl==1)
										{
										?>
										<td align="center" valign="middle" rowspan="<?  echo $country_rowspan_arr[$key][$cut_up_key][$item_key][$cutoff_key][$country_key]; ?>"><?  echo $countryArr[$po_country_id_array[$det['job_no']][$key][$cut_up_value][$item_value][$cutoff_value][$country_value]]."<br/>".change_date_format($po_country_ship_date_array [$det['job_no']][$key][$cut_up_value][$item_value][$cutoff_value][$country_value],"dd-mm-yyyy","-")."<br/>".date('l', strtotime($po_country_ship_date_array [$det['job_no']][$key][$cut_up_value][$item_value][$cutoff_value][$country_value]));  ?></td>
										<?
										}
										?>
										<td><?  echo $colorArr[$color_value] ;?></td>
										<td align="right"><? echo $po_color_qnty_array [$det['job_no']][$key][$cut_up_value][$item_value][$cutoff_value][$country_value][$color_value]; ?></td>
										<?
										foreach($job_size_array[$det['job_no']] as $key_s=>$value_s)
										{
											if($value_s !="")
											{
										?>
										<td align="right"><? echo $po_color_size_qnty_array [$det['job_no']][$key][$cut_up_value][$item_value][$cutoff_value][$country_value][$color_value][$value_s]?></td>
										<?
											}
										}
										?>
									</tr>
									<?
									$posl++;
									$itemsl++;
									$cutoffsl++;
									$countrysl++;
								}
								?>
						 <tr style="font-weight:bold; font-size:12px; background-color:#DFDFDF">
						 <td colspan="2">Country Total:</td>
						 <td colspan="" align="right"><? echo $po_country_qnty_array[$det['job_no']][$key][$cut_up_value][$item_key][$cutoff_value][$country_key] ?></td>
						 <?
						foreach($job_size_array[$det['job_no']] as $key_s=>$value_s)
						{
						if($value_s !="")
						{
							//$po_country_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]]
						?>
						<td align="right"><? echo $po_country_size_tot_qnty_array [$det['job_no']][$key][$cut_up_value][$item_value][$cutoff_value][$country_value][$value_s]; ?></td>
						<?
						}
						}
						?>
						</tr>
							<?
							}
							?>
						<tr style="font-weight:bold; font-size:12px">
						<td colspan="3"><? echo $cut_up_array[$po_cut_off_array[$det['job_no']][$key][$cut_up_value][$item_value][$cutoff_value]]; ?> Total:</td>
						<td colspan="" align="right"><? echo $po_cutoff_tot_qty_array[$det['job_no']][$key][$cut_up_value][$item_value][$cutoff_value] ?></td>
						<?
						foreach($job_size_array[$det['job_no']] as $key_s=>$value_s)
						{
						if($value_s !="")
						{
							//$po_item_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('size_number_id')]]
						?>
						<td align="right"><? echo $po_cut_size_tot_qnty_array[$det['job_no']][$key][$cut_up_value][$item_value][$cutoff_value][$value_s]?></td>
						<?
						}
						}
						?>
						</tr>
						<?
						}
						?>

                        <tr style="font-weight:bold; font-size:12px;  background-color:#DFDFDF">
						<td colspan="4">Item Total:</td>
						<td colspan="" align="right"><? echo $po_item_qnty_array[$det['job_no']][$key][$cut_up_value][$item_key] ?></td>
						<?
						foreach($job_size_array[$det['job_no']] as $key_s=>$value_s)
						{
						if($value_s !="")
						{
							//$po_item_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('size_number_id')]]
						?>
						<td align="right"><? echo $po_item_size_tot_qnty_array [$det['job_no']][$key][$cut_up_value][$item_value][$value_s]?></td>
						<?
						}
						}
						?>
						</tr>
						<?
                }
				if($presantation_type==1) $colspn=8; else $colspn=6;
				?>
                <tr style="font-weight:bold; font-size:12px">
                <td colspan="<? echo $colspn; ?>">Date wise Po Total:</td>

                   <td  align="right"><? echo $po_qnty_color_size_table_array [$det['job_no']][$key][$cut_up_value]?></td>

                 <?
				foreach($job_size_array[$det['job_no']] as $key_s=>$value_s)
				{
				if($value_s !="")
				{
					//$po_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('size_number_id')]]
				?>
				<td align="right"><? echo $po_size_tot_qnty_array [$det['job_no']][$key][$cut_up_value][$value_s]?></td>
				<?
				}


				}
				?>
                </tr>

                <?
			  }
            }
            ?>
            <tfoot>
            <tr style="font-weight:bold; font-size:12px">
                <th colspan="<? echo $colspn; ?>" align="left">Grand Total:</th>

                   <th align="right"><? echo $job_qnty_color_size_table_array [$det['job_no']];?></th>

                 <?
				foreach($job_size_array[$det['job_no']] as $key_s=>$value_s)
				{ //$job_size_array[$row[csf('job_no')]][$row[csf('size_number_id')]]
				if($value_s !="")
			    {
				?>
				<th align="right"><? echo $job_size_tot_qnty_array [$det['job_no']][$value_s]?></th>
				<?
				}
				}
				?>
                </tr>
            </tfoot>
        </table>
         <br/>
	<?
	}
	?>

       </td>
       </tr>
       </table>


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
	echo "$total_data####$filename";
	exit();
}



if($action=="job_color_size_cut")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	 $sql="SELECT a.id as job_id, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.product_dept, a.location_name, a.dealing_marchant, (a.job_quantity*a.total_set_qnty) as job_quantity , a.order_uom, a.total_set_qnty,d.item_number_id as item_number_id, c.id as po_id, c.po_number, c.pub_shipment_date, c.shipment_date, c.po_received_date, (c.po_quantity*a.total_set_qnty) as po_quantity, c.excess_cut, c.plan_cut, c.unit_price, c.po_total_price,  d.country_id,d.country_ship_date, d.size_number_id, d.color_number_id, d.order_quantity, d.order_rate, d.order_total, d.excess_cut_perc, d.plan_cut_qnty
	FROM wo_po_details_master a
	LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
	AND c.is_deleted =0
	AND c.status_active =1
	LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
	AND c.id = d.po_break_down_id
	AND d.is_deleted =0
	AND d.status_active =1
	WHERE
	a.job_no='$job_no' and
	a.is_deleted =0 and
	a.status_active =1
	";
	$job_color_tot=0;
	$companyArr = return_library_array("select id,company_name from lib_company ","id","company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
    $itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
	$job_no_array=array();
	$job_size_array=array();
	$job_size_qnty_array=array();
	$job_color_array=array();
	$job_color_qnty_array=array();
	$job_color_size_qnty_array=array();
	$sql_data = sql_select($sql);
	foreach( $sql_data as $row)
	{
	$job_no_array[$row[csf('job_no')]]=array("job_no"=>$row[csf('job_no')],"job_quantity"=>$row[csf('job_quantity')],"company_name"=>$row[csf('company_name')],"buyer_name"=>$row[csf('buyer_name')],"style_ref_no"=>$row[csf('style_ref_no')],"product_dept"=>$row[csf('product_dept')],"dealing_marchant"=>$row[csf('dealing_marchant')],"job_id"=>$row[csf('job_id')],"order_uom"=>$row[csf('order_uom')]);
	$job_size_array[$row[csf('job_no')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
	$job_size_qnty_array[$row[csf('job_no')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
	$job_color_array[$row[csf('job_no')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
	$job_color_qnty_array[$row[csf('job_no')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
	$job_color_size_qnty_array[$row[csf('job_no')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
	}
	?>
    <table width="1030px" align="center" border="1" rules="all">
            <tr style="background-color:#FFF">
                <td width="60" align="right">Job No: </td><td width="90" ><? echo $job_no; ?></td>
                <td width="60" align="right">Job Qty: </td><td width="90"><? echo $job_no_array[$job_no]['job_quantity']."(Pcs)"; ?></td>
                <td width="60" align="right">Company: </td><td width="90"><? echo $companyArr[$job_no_array[$job_no][csf('company_name')]]; ?></td>
                <td width="60" align="right">Buyer: </td><td width="85"><? echo $buyerArr[$job_no_array[$job_no][csf('buyer_name')]]; ?></td>
                <td width="65" align="right">Style Ref.: </td><td width="85"><? echo $job_no_array[$job_no][csf('style_ref_no')]; ?></td>
                <td width="70" align="right">Prod. Dept.: </td><td width="80"><? echo $product_dept[$job_no_array[$job_no][csf('product_dept')]]; ?></td>
                <td width="60" align="right">Merchant: </td><td width="90"><? echo $marchentrArr[$job_no_array[$job_no][csf('dealing_marchant')]]; ?></td>
            </tr>
        </table>
    <table width="1030px" align="center" border="1" rules="all" class="rpt_table" >
            <thead>
                <tr>
                    <th width="60">Color</th>
                    <th width="60">Color Total</th>
                    <?
					foreach($job_size_array[$job_no] as $key=>$value)
                    {
						if($value !="")
						{
					?>
                    <th width="60"><? echo $itemSizeArr[$value];?></th>
                    <?
						}
					}
					?>
                </tr>
            </thead>
            <?
			$i=1;
			foreach($job_color_array[$job_no] as $key_c=>$value_c)
            {
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			if($value_c != "")
            {
			?>

             <tr bgcolor="<? echo $bgcolor;?>">
             <td><? echo  $colorArr[$value_c]; ?></td>
             <td align="right"><? echo  $job_color_qnty_array[$job_no][$value_c]; $job_color_tot+=$job_color_qnty_array[$job_no][$value_c]; ?></td>
             <?
					foreach($job_size_array[$job_no] as $key_s=>$value_s)
                    {
						if($value_s !="")
						{
					?>
                    <td width="60" align="right"><? echo $job_color_size_qnty_array[$job_no][$value_c][$value_s];?></td>
                    <?
						}
					}
					?>
             </tr>
            <?
			$i++;
			}
			}
			?>
            <tfoot>
             <tr bgcolor="<? // echo $bgcolor;?>">
             <th>Total</th>
             <th align="right"><? echo  $job_color_tot; ?></th>
             <?
					foreach($job_size_array[$job_no] as $key_s=>$value_s)
                    {
						if($value_s !="")
						{
					?>
                    <th width="60" align="right"><? echo $job_size_qnty_array[$job_no][$value_s];?></th>
                    <?
						}
					}
					?>
             </tr>
             </tfoot>
            </table>
    <?
	exit();
}


if($action=="job_color_size")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	 $sql="SELECT a.id as job_id, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.product_dept, a.location_name, a.dealing_marchant, (a.job_quantity*a.total_set_qnty) as job_quantity , a.order_uom, a.total_set_qnty,d.item_number_id as item_number_id, c.id as po_id, c.po_number, c.pub_shipment_date, c.shipment_date, c.po_received_date, (c.po_quantity*a.total_set_qnty) as po_quantity, c.excess_cut, c.plan_cut, c.unit_price, c.po_total_price,  d.country_id,d.country_ship_date, d.size_number_id, d.color_number_id, d.order_quantity, d.order_rate, d.order_total, d.excess_cut_perc, d.plan_cut_qnty
	FROM wo_po_details_master a

	LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
	AND c.is_deleted =0
	AND c.status_active =1
	LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
	AND c.id = d.po_break_down_id
	AND d.is_deleted =0
	AND d.status_active =1
	WHERE
	a.job_no='$job_no' and
	a.is_deleted =0 and
	a.status_active =1
	";//LEFT JOIN wo_po_details_mas_set_details b ON a.job_no = b.job_no
	$job_color_tot=0;
	$companyArr = return_library_array("select id,company_name from lib_company ","id","company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
    $itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
	$job_no_array=array();
	$job_size_array=array();
	$job_size_qnty_array=array();
	$job_color_array=array();
	$job_color_qnty_array=array();
	$job_color_size_qnty_array=array();
	$sql_data = sql_select($sql);
	foreach( $sql_data as $row)
	{
		$job_no_array[$row[csf('job_no')]]=array("job_no"=>$row[csf('job_no')],"job_quantity"=>$row[csf('job_quantity')],"company_name"=>$row[csf('company_name')],"buyer_name"=>$row[csf('buyer_name')],"style_ref_no"=>$row[csf('style_ref_no')],"product_dept"=>$row[csf('product_dept')],"dealing_marchant"=>$row[csf('dealing_marchant')],"job_id"=>$row[csf('job_id')],"order_uom"=>$row[csf('order_uom')]);
		$job_size_array[$row[csf('job_no')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$row[csf('job_no')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
		$job_color_array[$row[csf('job_no')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array[$row[csf('job_no')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
		$job_color_size_qnty_array[$row[csf('job_no')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
	}
	?>
    <script>

	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		d.close();
	}
	</script>
    <p style="text-align:center; margin-bottom:10px;"><input type="button" id="" name="" class="formbutton" style="width:100px" value="Print" onclick="print_window()" /></p>
    <div id="report_container">
    <table width="1030px" align="center" border="1" rules="all" style="margin-top:20px;">
            <tr style="background-color:#FFF">
                <td width="50" align="left">Job No: </td><td width="80" ><? echo $job_no; ?></td>
                <td width="60" align="right">Job Qnty: </td><td width="80"><? echo $job_no_array[$job_no]['job_quantity']."(Pcs)"; ?></td>
                <td width="60" align="right">Company: </td><td width="120"><? echo $companyArr[$job_no_array[$job_no]['company_name']]; ?></td>
                <td width="60" align="right">Buyer: </td><td width="85"><? echo $buyerArr[$job_no_array[$job_no]['buyer_name']]; ?></td>
                <td width="65" align="right">Style Ref.: </td><td width="85"><? echo $job_no_array[$job_no]['style_ref_no']; ?></td>
                <td width="70" align="right">Prod. Dept.: </td><td width="80"><? echo $product_dept[$job_no_array[$job_no]['product_dept']]; ?></td>
                <td width="60" align="right">Merchant: </td><td width="90"><? echo $marchentrArr[$job_no_array[$job_no]['dealing_marchant']]; ?></td>
            </tr>
        </table>
    <table width="1030px" align="center" border="1" rules="all" class="rpt_table" >
            <thead>
                <tr>
                    <th width="60">Color</th>
                    <th width="60">Color Total</th>
                    <?
					foreach($job_size_array[$job_no] as $key=>$value)
                    {
						if($value !="")
						{
					?>
                    <th width="60"><? echo $itemSizeArr[$value];?></th>
                    <?
						}
					}
					?>
                </tr>
            </thead>
            <?
			$i=1;
			foreach($job_color_array[$job_no] as $key_c=>$value_c)
            {
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			if($value_c != "")
            {
			?>

             <tr bgcolor="<? echo $bgcolor;?>">
             <td><? echo  $colorArr[$value_c]; ?></td>
             <td align="right"><? echo  $job_color_qnty_array[$job_no][$value_c]; $job_color_tot+=$job_color_qnty_array[$job_no][$value_c]; ?></td>
             <?
					foreach($job_size_array[$job_no] as $key_s=>$value_s)
                    {
						if($value_s !="")
						{
					?>
                    <td width="60" align="right"><? echo $job_color_size_qnty_array[$job_no][$value_c][$value_s];?></td>
                    <?
						}
					}
					?>
             </tr>
            <?
			$i++;
			}
			}
			?>
            <tfoot>
             <tr bgcolor="<? // echo $bgcolor;?>">
             <th>Total</th>
             <th align="right"><? echo  $job_color_tot; ?></th>
             <?
					foreach($job_size_array[$job_no] as $key_s=>$value_s)
                    {
						if($value_s !="")
						{
					?>
                    <th width="60" align="right"><? echo $job_size_qnty_array[$job_no][$value_s];?></th>
                    <?
						}
					}
					?>
             </tr>
         </tfoot>
        </table>
    </div>
    <?
	exit();
}
?>
