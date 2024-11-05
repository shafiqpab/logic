<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');
include('../../../includes/class4/class.conditions.php');
include('../../../includes/class4/class.reports.php');
include('../../../includes/class4/class.fabrics.php');

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
		echo create_drop_down( "cbo_buyer_id", 120, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "load_drop_down( 'requires/size_and_color_break_report_controller', this.value, 'load_drop_down_brand', 'brand_td');load_drop_down( 'requires/size_and_color_break_report_controller', this.value, 'load_drop_down_season', 'season_td');");
		exit();
	}
}

if ($action=="load_drop_down_brand")
{
	 //echo "select id, brand_name from lib_buyer_brand brand where buyer_id='$data' and status_active =1 and is_deleted=0 $brand_id_cond order by brand_name ASC";
	echo create_drop_down( "cbo_brand_id", 70, "select id, brand_name from lib_buyer_brand brand where buyer_id='$data' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "--Brand--", $selected, "" );
	exit();
}

if ($action=="load_drop_down_season")
{
	echo create_drop_down( "cbo_season_id", 70, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "" );
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


if($action=="company_wise_report_button_setting")
{
	extract($_REQUEST);

	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=2 and report_id=198 and is_deleted=0 and status_active=1");
	
	//echo $print_report_format; disconnect($con); die;
	
	$print_report_format_arr=explode(",",$print_report_format);
	//print_r($print_report_format_arr);
	echo "$('#show_button1').hide();\n";
	echo "$('#show_button2').hide();\n";
	echo "$('#show_button3').hide();\n";
	echo "$('#show_button4').hide();\n";
	echo "$('#show_button5').hide();\n";
	echo "$('#show_button6').hide();\n";
	echo "$('#show_button7').hide();\n";
	echo "$('#show_button8').hide();\n";
	echo "$('#show_button9').hide();\n";
	echo "$('#show_button10').hide();\n";
	echo "$('#show_button11').hide();\n";

	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			
			if($id==108){echo "$('#show_button1').show();\n";}
			if($id==766){echo "$('#show_button2').show();\n";}
			if($id==195){echo "$('#show_button3').show();\n";}
			if($id==242){echo "$('#show_button4').show();\n";}
			if($id==359){echo "$('#show_button5').show();\n";}
			if($id==712){echo "$('#show_button6').show();\n";}
			if($id==408){echo "$('#show_button7').show();\n";}
			if($id==446){echo "$('#show_button9').show();\n";}
			if($id==23){echo "$('#show_button8').show();\n";}
			if($id==389){echo "$('#show_button10').show();\n";}
			if($id==191){echo "$('#show_button11').show();\n";}
			
			
		}
	}
	exit();	
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
     <div align="center"><? echo create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --" ); ?></div>
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
		 $sql= "select a.id, a.job_no,a.job_no_prefix_num, a.style_ref_no, a.product_dept, a.dealing_marchant, a.team_leader from wo_po_details_master a where a.status_active=1 and a.is_deleted=0 $company_id $buyer_id $year_cond group by a.id, a.job_no,a.job_no_prefix_num, a.style_ref_no, a.product_dept, a.dealing_marchant, a.team_leader order by a.id DESC ";
	}
	else
	{
		 $sql= "select a.id, a.job_no,a.job_no_prefix_num, a.style_ref_no, a.product_dept, a.dealing_marchant, a.team_leader from wo_po_details_master a, wo_po_break_down  b where  b.job_no_mst=a.job_no and a.status_active=1 and a.is_deleted=0 $company_id $buyer_id $year_cond group by a.id, a.job_no,a.job_no_prefix_num, a.style_ref_no, a.product_dept, a.dealing_marchant, a.team_leader order by a.id DESC";
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
     <div align="center"><? echo create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --" ); ?></div>
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
	extract($_REQUEST);
	//$process = array( &$_POST );
	//extract(check_magic_quote_gpc( $process ));
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
	$cbo_season_year=str_replace("'","",$cbo_season_year);
	$cbo_order_status=str_replace("'","",$cbo_order_status);
	$txt_style_description=str_replace("'","",$txt_style_description);
	
	//echo $type.'=kausar'; die;

	if ($cbo_company==0) $company_id=""; else $company_id=" and a.company_name=$cbo_company";
	if ($cbo_buyer==0) $buyer_id=""; else $buyer_id=" and a.buyer_name=$cbo_buyer";
	if ($cbo_brand_id==0) $brand_id_cond=""; else $brand_id_cond=" and a.brand_id=$cbo_brand_id";
	if ($cbo_season_id==0) $season_id_cond=""; else $season_id_cond=" and a.season_buyer_wise=$cbo_season_id";
	if ($cbo_season_year==0) $seasonYearCond=""; else $seasonYearCond=" and a.season_year=$cbo_season_year";
	if ($cbo_order_status==0) $orderStatusCond=""; else $orderStatusCond=" and c.is_confirmed=$cbo_order_status";
	if ($job_no=="") $job_num=""; else $job_num=" and a.job_no='$job_no'";
	if ($job_no=="") $job_num_mst=""; else $job_num_mst=" and a.job_no_prefix_num=$job_no";
	if ($hidd_job==0) $job_id=""; else $job_id=" and a.id in ($hidd_job)";
	if ($txt_file_no=="") $file_cond=""; else $file_cond=" and c.file_no='$txt_file_no'";
	if ($txt_ref_no=="") $ref_cond=""; else $ref_cond=" and c.grouping='$txt_ref_no'";
	if ($job_no=="") $job_num=""; else $job_num=" and a.job_no='$job_no'";
	if ($txt_style_ref=="") $style_ref_cond=""; else $style_ref_cond=" and a.style_ref_no='$txt_style_ref'";
	if ($txt_style_description=="") $style_description_cond=""; else $style_description_cond=" and a.style_description='$txt_style_description'";

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
	$country_code_arr=return_library_array( "select id, ultimate_country_code from lib_country_loc_mapping", "id", "ultimate_country_code");
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
	  $sql_set= "select a.job_no,a.currency_id, b.smv_pcs,c.id as po_id,b.gmts_item_id from  wo_po_details_mas_set_details b,wo_po_details_master a LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst AND c.is_deleted =0 AND c.status_active =1 where  a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 $company_id $buyer_id".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_id $job_num_mst $po_id $pub_shipment_date2 $brand_id_cond $season_id_cond $year_cond $job_num_mst $style_ref_cond $ref_cond $file_cond $seasonYearCond $orderStatusCond group by a.job_no, c.id, b.smv_pcs, b.gmts_item_id, a.currency_id ";
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
	$job_no_array=array(); $po_details_array=array(); $job_size_array=array(); $po_item_array=array(); $po_country_array=array(); $po_country_ship_date_array=array(); $po_color_array=array(); $job_qnty_color_size_table_array=array(); $job_size_tot_qnty_array=array();

	$po_color_size_qnty_array=array(); $po_color_qnty_array=array(); $po_qnty_array=array(); $po_qnty_color_size_table_array=array(); $po_size_tot_qnty_array=array(); $po_item_qnty_array=array(); $po_item_size_tot_qnty_array=array(); $po_country_qnty_array=array(); $po_country_size_tot_qnty_array=array(); $po_ship_date_array=array(); $po_file_no_array=array(); $po_ref_no_array=array();
	$po_wise_remark=array();

	ob_start();
	if($txt_order_type==1)
	{
		$sql="SELECT a.id as job_id, a.currency_id, a.job_no, a.company_name, a.buyer_name,a.brand_id,a.season_buyer_wise as season, a.style_ref_no,a.style_description, a.product_dept, a.location_name, a.dealing_marchant, (a.job_quantity*a.total_set_qnty) as job_quantity, a.order_uom, a.total_set_qnty, a.order_repeat_no, d.item_number_id as item_number_id, c.id as po_id, c.file_no, c.grouping, c.po_number,c.pack_handover_date, c.pub_shipment_date, c.shipment_date, c.po_received_date, (c.po_quantity*a.total_set_qnty) as po_quantity, c.excess_cut, c.plan_cut, c.unit_price, c.po_total_price, d.id, d.country_id, d.country_ship_date, d.cutup_date, d.size_number_id, d.color_number_id, d.order_quantity, d.order_rate, d.order_total, d.excess_cut_perc, d.plan_cut_qnty, d.size_order,d.article_number,c.details_remarks,a.style_owner,d.code_id FROM wo_po_details_master a LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst AND c.is_deleted =0 AND c.status_active =1 LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst AND c.id = d.po_break_down_id AND d.is_deleted =0 AND d.status_active =1 WHERE a.is_deleted =0 AND a.status_active =1 $company_id $buyer_id".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_id $po_id $brand_id_cond $season_id_cond $pub_shipment_date $year_cond $job_num_mst $style_ref_cond $ref_cond $file_cond $seasonYearCond $orderStatusCond $style_description_cond order by d.size_order, a.job_no, c.id, d.country_ship_date, d.color_order";
	}
	else
	{
		$sql="SELECT a.id as job_id, a.currency_id, a.job_no, a.company_name, a.buyer_name,a.brand_id,a.season_buyer_wise as season, a.style_ref_no,a.style_description, a.product_dept, a.location_name, a.dealing_marchant, (a.job_quantity*a.total_set_qnty) as job_quantity, a.order_uom, a.total_set_qnty, a.order_repeat_no, d.item_number_id as item_number_id, c.id as po_id,c.file_no, c.grouping, c.po_number, c.pack_handover_date, c.pub_shipment_date, c.shipment_date, c.po_received_date, (c.po_quantity*a.total_set_qnty) as po_quantity, c.excess_cut, c.plan_cut, c.unit_price, c.po_total_price, d.id,d.country_id, d.country_ship_date, d.cutup_date, d.size_number_id, d.color_number_id, d.order_quantity, d.order_rate, d.order_total, d.excess_cut_perc, d.plan_cut_qnty, d.size_order,d.article_number,c.details_remarks,a.style_owner,d.code_id FROM wo_po_details_master a,wo_po_break_down c LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst AND c.id = d.po_break_down_id AND d.is_deleted =0 AND d.status_active =1 AND c.is_deleted =0 AND c.status_active =1 WHERE a.job_no = c.job_no_mst 	AND c.is_deleted =0 AND c.status_active =1 and a.is_deleted =0 AND a.status_active =1 $company_id $buyer_id".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_id $po_id $pub_shipment_date $brand_id_cond $season_id_cond $year_cond $job_num_mst $style_ref_cond $ref_cond $file_cond $seasonYearCond $orderStatusCond $style_description_cond order by d.size_order, a.job_no, d.color_order, c.id, d.country_ship_date";
	}

	//echo $sql; die;
	$sql_data = sql_select($sql);
	foreach( $sql_data as $row)
	{
		$job_no_array[$row[csf('job_no')]]=array("job_no"=>$row[csf('job_no')],"job_quantity"=>$row[csf('job_quantity')],"company_name"=>$row[csf('company_name')],"buyer_name"=>$row[csf('buyer_name')],"style_ref_no"=>$row[csf('style_ref_no')],"style_description"=>$row[csf('style_description')],"product_dept"=>$row[csf('product_dept')],"dealing_marchant"=>$row[csf('dealing_marchant')],"job_id"=>$row[csf('job_id')],"order_uom"=>$row[csf('order_uom')],"brand_id"=>$row[csf('brand_id')],"season"=>$row[csf('season')],"style_owner"=>$row[csf('style_owner')]);
		$po_details_array[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('po_number')];
		$po_wise_remark[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('details_remarks')];
		$po_file_no_array[$row[csf('job_no')]][$row[csf('po_id')]]['file']=$row[csf('file_no')];
		$po_ref_no_array[$row[csf('job_no')]][$row[csf('po_id')]]['ref']=$row[csf('grouping')];
		$job_size_array[$row[csf('job_no')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$po_item_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]]=$row[csf('item_number_id')];
		$po_country_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]]['country_id']=$row[csf('country_id')];
		$po_country_array_code[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]]['code_id']=$row[csf('code_id')];
		$po_ship_date_array[$row[csf('job_no')]][$row[csf('po_id')]]['shipdate']=$row[csf('shipment_date')];
		$po_ship_date_array[$row[csf('job_no')]][$row[csf('po_id')]]['pubshipdate']=$row[csf('pub_shipment_date')];
		$po_phd_date_array[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('pack_handover_date')];
		$po_country_ship_date_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]=$row[csf('country_ship_date')];
		$po_color_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]] [$row[csf('color_number_id')]]=$row[csf('color_number_id')];

		$style_color_size_arr[$row[csf('style_ref_no')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['color_size_qnty']+=$row[csf('order_quantity')];;

		$job_qnty_color_size_table_array[$row[csf('job_no')]]+=$row[csf('order_quantity')];
		$job_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];

		$po_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('po_quantity')];
		$po_qnty_price_array[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('unit_price')];

		$po_qnty_color_size_table_array[$row[csf('job_no')]][$row[csf('po_id')]]+=$row[csf('order_quantity')];
		$po_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];

		$po_item_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]]+=$row[csf('order_quantity')];
		$po_item_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];

		$po_country_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]+=$row[csf('order_quantity')];
		$po_article_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('color_number_id')]]['article_number'].=$row[csf('article_number')]."***";
		$po_country_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];

		$po_color_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]] [$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
		$po_color_size_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]] [$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
		//$tmp_job[$row[csf('job_no')]]=$row[csf('job_no')];
		//$size_array[$row[csf("job_no")]][$row[csf("size_order")]]=$row[csf("size_number_id")];

		$size_array[$row[csf("job_no")]][$row[csf("size_order")]]=$row[csf("size_number_id")];
	}
	/* echo "<pre>";
	 print_r($po_country_array);die;*/

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
    <table id="scroll_body" align="center" style="height:auto; width:1340px; margin:0 auto; padding:0;">
        <tr>
            <td width="1360">
            <?
            foreach($job_no_array as $rdata=>$det)
            {
				$company_id=$det['company_name'];
				$buyer_id=$det['buyer_name'];
                $slab_data=sql_select("SELECT ship_plan from lib_excess_cut_slab where status_active=1 and is_deleted=0 and comapny_id=$company_id and buyer_id=$buyer_id");
				$ship_plan_data=0;
				$is_ship_plan=0;
				if(count($slab_data)>0){
					foreach($slab_data as $row){
						if($row[csf('ship_plan')]>0){
							$ship_plan_data=$row[csf('ship_plan')];
							$is_ship_plan=1;
						}						
					}
				}
                ?><br/>
                <table width="1400px" align="center" border="1" rules="all" id="table_header_1">
                    <tr style="background-color:#FFF">
                        <td width="60" align="right">Job No: </td><td width="90" onClick="openmypage_job_color_size('requires/size_and_color_break_report_controller.php?action=job_color_size&job_no=<?=$det["job_no"]; ?>','Job Color Size');"><a href="##"><?=$det['job_no']; ?></a></td>
                        <td width="60" align="right">Job Qty: </td><td width="90"><?=$det['job_quantity']."(Pcs)"; ?></td>
                        <td width="60" align="right">Company: </td><td width="90"><?=$companyArr[$det['company_name']]; ?></td>
						<td width="100" align="right">Style Owner: &nbsp</td><td width="100">&nbsp <?=$companyArr[$det['style_owner']]; ?></td>
                        <td width="60" align="right">Buyer: </td><td width="85"><?=$buyerArr[$det['buyer_name']]; ?></td>
                        <td width="60" align="right">Brand: </td><td width="85"><?=$brand_nameArr[$det['brand_id']]; ?></td>
                        <td width="60" align="right">Season: </td><td width="85"><?=$seasonArr[$det['season']]; ?></td>
                        <td width="65" align="right">Style Ref.: </td><td width="85">
							<? if($is_ship_plan==1){ ?>
								<a href="##" onClick="openmypage_fabric_dtls('requires/size_and_color_break_report_controller.php?action=job_fabric_dtls&job_no=<?=$det["job_no"]; ?>','Fabric Cost');"><?=$det['style_ref_no']; ?></a>
							<? } else { echo $det['style_ref_no']; } ?>
						</td>
						<td width="65" align="left">Style Description: </td><td width="85"><?=$det['style_description']; ?></td>
                        <td width="70" align="right">Prod. Dept.: </td><td width="80"><?=$product_dept[$det['product_dept']]; ?></td>
                        <td width="60" align="right">Merchant: </td><td width="90"><?=$marchentrArr[$det['dealing_marchant']]; ?></td>
                        <td width="60" align="right">Ord. Re. No: </td><td width="90"><?=$det['order_repeat_no']; ?></td>
                    </tr>
                </table>
                <br/>
                <table width="1340px" align="center" border="1" rules="all" class="rpt_table" id="color_size">
                    <thead>
                        <tr>
                            <!--<th width="60">Sl</th>-->
                            <th width="60">PO Number</th>
                            <? if($presantation_type==1){ ?>
                            <th width="60">File No</th>
                            <th width="70">IR/IB</th>
                            <? } ?>
                            <th width="100">Remarks</th>
                            <th width="60">PO Qty and Price</th>
                            <th width="70">Item and SMV </th>
                            <th width="60">Country</th>
							<th width="60">Ucast Code</th>
                            <th width="60">Color</th>
                            <th width="60">Article No</th>
                            <th width="60">Color Total</th>
                            <?
                            foreach($size_array[$det['job_no']] as $key=>$value)
                            {
                                if($value!="")
                                {
                                    ?><th width="60"><?=$itemSizeArr[$value]; ?></th><?
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
                                $countrysl=1;
                                foreach($po_color_array [$det['job_no']][$key][$item_value][$country_value] as $color_key=>$color_value)
                                {
                                    if($countrysl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                ?>
                                    <tr bgcolor="<?=$bgcolor;?>">
                                        <?
										$pack_handover_date=$po_phd_date_array[$det['job_no']][$key];
                                        if($posl==1)
                                        {
											if($type==0)
											{
                                            ?><td align="center" valign="middle" rowspan="<?=$po_rowspan_arr[$key]; ?>" ><?=$value; echo "&nbsp;<br/> Ship Date:".change_date_format($po_ship_date_array[$det['job_no']][$key]['pubshipdate'],"dd-mm-yyyy","-"); echo "<br/>".date('l', strtotime($po_ship_date_array[$det['job_no']][$key]['pubshipdate'])).'<br> PHD Date: '.change_date_format($pack_handover_date); ?></td><?
											}
											else if($type==4)
											{
												 ?><td align="center" valign="middle" rowspan="<?=$po_rowspan_arr[$key]; ?>" ><?=$value; echo "&nbsp;<br/>Pub. Ship Date:".change_date_format($po_ship_date_array[$det['job_no']][$key]['pubshipdate'],"dd-mm-yyyy","-"); echo "<br/>".date('l', strtotime($po_ship_date_array[$det['job_no']][$key]['pubshipdate'])); ?></td><?
											}
                                        }
                                        if($presantation_type==1)
                                        {
                                            if($posl==1)
                                            {
                                            ?><td align="center" valign="middle" rowspan="<?=$po_rowspan_arr[$key]; ?>" ><?=$po_file_no_array[$det['job_no']][$key]['file']; ?></td><?
                                            }
                                            if($posl==1)
                                            {
                                                ?><td align="center" valign="middle" rowspan="<?=$po_rowspan_arr[$key]; ?>" ><?=$po_ref_no_array[$det['job_no']][$key]['ref']; ?></td><?
                                            }
                                        }
                                        if($posl==1)
                                        {
                                            $po_price = $po_qnty_price_array[$det['job_no']][$key];
                                            $qnty = $po_qnty_array[$det['job_no']][$key] ? $po_qnty_array[$det['job_no']][$key] : 0;
                                            ?>
                                            <td align="center" valign="middle" rowspan="<?=$po_rowspan_arr[$key]; ?>"><?=$po_wise_remark[$det['job_no']][$key];?></td>
                                            <td align="center" valign="middle" rowspan="<?=$po_rowspan_arr[$key]; ?>"><?=$qnty.' (Pcs) <br>'.number_format($po_price,2).' <b>'.$currency[$row[csf("currency_id")]].'</b>'; ?></td><?
                                        }
                                        if($itemsl==1)
                                        {
                                            $item_smv_pcs=$smv_no_array[$det[('job_no')]][$key][$item_value]['smv_pcs'];
                                            ?><td align="center" title="<?=$det[('job_no')].'='.$key.'='.$item_value.',smv='.$item_smv_pcs;?>" valign="middle" rowspan="<?=$item_rowspan_arr[$key][$item_key]; ?>" ><?=$garments_item[$item_value].'<br>'.$item_smv_pcs;?></td><?
                                        }
                                        if($countrysl==1)
                                        {
                                         
										  
										    ?><td align="center" valign="middle" rowspan="<?=$country_rowspan_arr[$key][$item_key][$country_value]; ?>"><?=$countryArr[$country_value]."<br/>".change_date_format($po_country_ship_date_array [$det['job_no']][$key][$item_value][$country_value],"dd-mm-yyyy","-")."<br/>".date('l', strtotime($po_country_ship_date_array [$det['job_no']][$key][$item_value][$country_value])); ?></td><?
                                        }
                                        ?>
										<td align="center" valign="middle"><?=$country_code_arr[$po_country_array_code [$det['job_no']][$key][$item_value]['code_id']] ;?></td>
                                        <td align="center" valign="middle"><?=$colorArr[$color_value] ;?></td>
                                        <td align="center" valign="middle"><?=implode(", ", array_filter(array_unique(explode("***", $po_article_array[$det['job_no']][$key][$item_key][$country_key][$color_value]['article_number'])))); ?></td>
                                        <td align="center" valign="middle"><?=$po_color_qnty_array [$det['job_no']][$key][$item_value][$country_value][$color_value]; ?></td>
                                        <?
                                        foreach($size_array[$det['job_no']] as $key_s=>$value_s)
                                        {
                                            if($value_s !="")
                                            {
                                                ?><td align="center" valign="middle"><?=$po_color_size_qnty_array [$det['job_no']][$key][$item_value][$country_value][$color_value][$value_s]; ?></td><?
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
                                    <td colspan="4">Country Total:</td>
                                    <td align="center" valign="middle"><?=$po_country_qnty_array[$det['job_no']][$key][$item_key][$country_key]; ?></td>
                                    <?
                                    foreach($job_size_array[$det['job_no']] as $key_s=>$value_s)
                                    {
                                        if($value_s !="")
                                        {
                                            ?><td align="center" valign="middle"><?=$po_country_size_tot_qnty_array [$det['job_no']][$key][$item_value][$country_value][$value_s]; ?></td><?
                                        }
                                    }
                                    ?>
                                </tr>
                            <?
                            }
                            ?>
                            <tr style="font-weight:bold; font-size:12px">
                                <td colspan="5">Item Total:</td>
                                <td align="center" valign="middle"><?=$po_item_qnty_array[$det['job_no']][$key][$item_key]; ?></td>
                                <?
                                foreach($size_array[$det['job_no']] as $key_s=>$value_s)
                                {
                                    if($value_s !="")
                                    {
                                        ?><td align="center" valign="middle"><?=$po_item_size_tot_qnty_array [$det['job_no']][$key][$item_value][$value_s]; ?></td><?
                                    }
                                }
                                ?>
                            </tr>
                            <?
                        }
                        if($presantation_type==1) $colspn=10; else $colspn=8;
                        ?>
                        <tr style="font-weight:bold; font-size:12px">
                            <td colspan="<?=$colspn; ?>">Po Total:</td>
                            <td align="center" valign="middle"><?=$po_qnty_color_size_table_array [$det['job_no']][$key]; ?></td>
                            <?
                            foreach($size_array[$det['job_no']] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
                                    ?><td align="center" valign="middle"><?=$po_size_tot_qnty_array [$det['job_no']][$key][$value_s]; ?></td><?
                                }
                            }
                            ?>
                        </tr>
                        <?
                    }
                    ?>
                    <tr style="font-weight:bold; font-size:12px">
                        <td colspan="<?=$colspn; ?>">Grand Total:</td>
                        <td align="center" valign="middle"><?=$job_qnty_color_size_table_array [$det['job_no']]; ?></td>
                        <?
                        foreach($size_array[$det['job_no']] as $key_s=>$value_s)
                        {
                            if($value_s !="")
                            {
                                ?><td align="center" valign="middle"><?=$job_size_tot_qnty_array [$det['job_no']][$value_s]; ?></td><?
                            }
                        }
                        ?>
                    </tr>
					
					<?php
					// $style_color_size_arr[$row[csf('style_ref_no')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['color_size_qnty']
					$no_color=array();
					foreach($style_color_size_arr[$det['style_ref_no']] as $color_id=>$size_data){
						$no_color[$det['style_ref_no']]+=1;
						foreach($size_data as $size_id=>$val){
						$color_sum[$det['style_ref_no']][$color_id]+=$val['color_size_qnty'];
						}
						
					}

					// print_r($no_color);
					//================================issue==19934>26-09-2021=================
					$style="";
					foreach($style_color_size_arr[$det['style_ref_no']] as $color_id=>$size_data)
					{
						?>
							<tr style="font-weight:bold; font-size:12px">
								<?php
								if($style!=$det['style_ref_no']){?>
								<td align="center" rowspan="<?=$no_color[$det['style_ref_no']];?>"><?=$det['style_ref_no'];?></td>
								<td align="center" colspan="<?=$colspn-2; ?>" rowspan="<?=$no_color[$det['style_ref_no']];?>">Color Size Wise Summery</td>
								<?}?>
								<td align="center"><?=$colorArr[$color_id]; ?></td>
								<td align="center"><?=$color_sum[$det['style_ref_no']][$color_id]; ?></td>
								<?
								foreach($size_array[$det['job_no']] as $size_id=>$val){?>
								<td align="center"><?=$size_data[$val]['color_size_qnty']; ?></td><?
									}?>
						</tr>
							<? $style=$det['style_ref_no'];
			   		 }?>
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
if ($action=="report_generate11")
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
	$cbo_season_year=str_replace("'","",$cbo_season_year);
	$cbo_order_status=str_replace("'","",$cbo_order_status);
	$txt_style_description=str_replace("'","",$txt_style_description);
	
	//echo $type.'=kausar'; die;

	if ($cbo_company==0) $company_id=""; else $company_id=" and a.company_name=$cbo_company";
	if ($cbo_buyer==0) $buyer_id=""; else $buyer_id=" and a.buyer_name=$cbo_buyer";
	if ($cbo_brand_id==0) $brand_id_cond=""; else $brand_id_cond=" and a.brand_id=$cbo_brand_id";
	if ($cbo_season_id==0) $season_id_cond=""; else $season_id_cond=" and a.season_buyer_wise=$cbo_season_id";
	if ($cbo_season_year==0) $seasonYearCond=""; else $seasonYearCond=" and a.season_year=$cbo_season_year";
	if ($cbo_order_status==0) $orderStatusCond=""; else $orderStatusCond=" and c.is_confirmed=$cbo_order_status";
	if ($job_no=="") $job_num=""; else $job_num=" and a.job_no='$job_no'";
	if ($job_no=="") $job_num_mst=""; else $job_num_mst=" and a.job_no_prefix_num=$job_no";
	if ($hidd_job==0) $job_id=""; else $job_id=" and a.id in ($hidd_job)";
	if ($txt_file_no=="") $file_cond=""; else $file_cond=" and c.file_no='$txt_file_no'";
	if ($txt_ref_no=="") $ref_cond=""; else $ref_cond=" and c.grouping='$txt_ref_no'";
	if ($job_no=="") $job_num=""; else $job_num=" and a.job_no='$job_no'";
	if ($txt_style_ref=="") $style_ref_cond=""; else $style_ref_cond=" and a.style_ref_no='$txt_style_ref'";
	if ($txt_style_description=="") $style_description_cond=""; else $style_description_cond=" and a.style_description='$txt_style_description'";

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
	  $sql_set= "select a.job_no,a.currency_id, b.smv_pcs,c.id as po_id,b.gmts_item_id from  wo_po_details_mas_set_details b,wo_po_details_master a LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst AND c.is_deleted =0 AND c.status_active =1 where  a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 $company_id $buyer_id".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_id $job_num_mst $po_id $pub_shipment_date2 $brand_id_cond $season_id_cond $year_cond $job_num_mst $style_ref_cond $ref_cond $file_cond $seasonYearCond $orderStatusCond group by a.job_no, c.id, b.smv_pcs, b.gmts_item_id, a.currency_id ";
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
	$job_no_array=array(); $po_details_array=array(); $job_size_array=array(); $po_item_array=array(); $po_country_array=array(); $po_country_ship_date_array=array(); $po_color_array=array(); $job_qnty_color_size_table_array=array(); $job_size_tot_qnty_array=array();

	$po_color_size_qnty_array=array(); $po_color_qnty_array=array(); $po_qnty_array=array(); $po_qnty_color_size_table_array=array(); $po_size_tot_qnty_array=array(); $po_item_qnty_array=array(); $po_item_size_tot_qnty_array=array(); $po_country_qnty_array=array(); $po_country_size_tot_qnty_array=array(); $po_ship_date_array=array(); $po_file_no_array=array(); $po_ref_no_array=array();
	$po_wise_remark=array();

	ob_start();
	if($txt_order_type==1)
	{
		$sql="SELECT a.id as job_id, a.currency_id, a.job_no, a.company_name, a.buyer_name,a.brand_id,a.season_buyer_wise as season, a.style_ref_no,a.style_description, a.product_dept, a.location_name, a.dealing_marchant, (a.job_quantity*a.total_set_qnty) as job_quantity, a.order_uom, a.total_set_qnty, a.order_repeat_no, d.item_number_id as item_number_id, c.id as po_id, c.file_no, c.grouping, c.po_number,c.pack_handover_date, c.pub_shipment_date, c.shipment_date, c.po_received_date, (c.po_quantity*a.total_set_qnty) as po_quantity, c.excess_cut, c.plan_cut, c.unit_price, c.po_total_price, d.id, d.country_id, d.country_ship_date, d.cutup_date, d.size_number_id, d.color_number_id, d.order_quantity, d.order_rate, d.order_total, d.excess_cut_perc, d.plan_cut_qnty, d.size_order,d.article_number,c.details_remarks FROM wo_po_details_master a LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst AND c.is_deleted =0 AND c.status_active =1 LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst AND c.id = d.po_break_down_id AND d.is_deleted =0 AND d.status_active =1 WHERE a.is_deleted =0 AND a.status_active =1 $company_id $buyer_id".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_id $po_id $brand_id_cond $season_id_cond $pub_shipment_date $year_cond $job_num_mst $style_ref_cond $ref_cond $file_cond $seasonYearCond $orderStatusCond $style_description_cond order by d.size_order, a.job_no, c.id, d.country_ship_date, d.color_order";
	}
	else
	{
		$sql="SELECT a.id as job_id, a.currency_id, a.job_no, a.company_name, a.buyer_name,a.brand_id,a.season_buyer_wise as season, a.style_ref_no,a.style_description, a.product_dept, a.location_name, a.dealing_marchant, (a.job_quantity*a.total_set_qnty) as job_quantity, a.order_uom, a.total_set_qnty, a.order_repeat_no, d.item_number_id as item_number_id, c.id as po_id,c.file_no, c.grouping, c.po_number, c.pack_handover_date, c.pub_shipment_date, c.shipment_date, c.po_received_date, (c.po_quantity*a.total_set_qnty) as po_quantity, c.excess_cut, c.plan_cut, c.unit_price, c.po_total_price, d.id,d.country_id, d.country_ship_date, d.cutup_date, d.size_number_id, d.color_number_id, d.order_quantity, d.order_rate, d.order_total, d.excess_cut_perc, d.plan_cut_qnty, d.size_order,d.article_number,c.details_remarks FROM wo_po_details_master a,wo_po_break_down c LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst AND c.id = d.po_break_down_id AND d.is_deleted =0 AND d.status_active =1 AND c.is_deleted =0 AND c.status_active =1 WHERE a.job_no = c.job_no_mst 	AND c.is_deleted =0 AND c.status_active =1 and a.is_deleted =0 AND a.status_active =1 $company_id $buyer_id".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_id $po_id $pub_shipment_date $brand_id_cond $season_id_cond $year_cond $job_num_mst $style_ref_cond $ref_cond $file_cond $seasonYearCond $orderStatusCond $style_description_cond order by d.size_order, a.job_no, d.color_order, c.id, d.country_ship_date";
	}

	//echo $sql;
	$sql_data = sql_select($sql);
	foreach( $sql_data as $row)
	{
		$job_no_array[$row[csf('job_no')]]=array("job_no"=>$row[csf('job_no')],"job_quantity"=>$row[csf('job_quantity')],"company_name"=>$row[csf('company_name')],"buyer_name"=>$row[csf('buyer_name')],"style_ref_no"=>$row[csf('style_ref_no')],"style_description"=>$row[csf('style_description')],"product_dept"=>$row[csf('product_dept')],"dealing_marchant"=>$row[csf('dealing_marchant')],"job_id"=>$row[csf('job_id')],"order_uom"=>$row[csf('order_uom')],"brand_id"=>$row[csf('brand_id')],"season"=>$row[csf('season')]);
		$po_details_array[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('po_number')];
		$po_wise_remark[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('details_remarks')];
		$po_file_no_array[$row[csf('job_no')]][$row[csf('po_id')]]['file']=$row[csf('file_no')];
		$po_ref_no_array[$row[csf('job_no')]][$row[csf('po_id')]]['ref']=$row[csf('grouping')];
		$job_size_array[$row[csf('job_no')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$po_item_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]]=$row[csf('item_number_id')];
		$po_country_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$po_ship_date_array[$row[csf('job_no')]][$row[csf('po_id')]]['shipdate']=$row[csf('shipment_date')];
		$po_ship_date_array[$row[csf('job_no')]][$row[csf('po_id')]]['pubshipdate']=$row[csf('pub_shipment_date')];
		$po_phd_date_array[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('pack_handover_date')];
		$po_country_ship_date_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]=$row[csf('country_ship_date')];
		$po_color_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]] [$row[csf('color_number_id')]]=$row[csf('color_number_id')];

		$style_color_size_arr[$row[csf('style_ref_no')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['color_size_qnty']+=$row[csf('order_quantity')];;

		$job_qnty_color_size_table_array[$row[csf('job_no')]]+=$row[csf('order_quantity')];
		$job_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];

		$po_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('po_quantity')];
		$po_qnty_price_array[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('unit_price')];

		$po_qnty_color_size_table_array[$row[csf('job_no')]][$row[csf('po_id')]]+=$row[csf('order_quantity')];
		$po_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];

		$po_item_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]]+=$row[csf('order_quantity')];
		$po_item_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];

		$po_country_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]+=$row[csf('order_quantity')];
		$po_article_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('color_number_id')]]['article_number'].=$row[csf('article_number')]."***";
		$po_country_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];

		$po_color_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]] [$row[csf('color_number_id')]]['order_qty']+=$row[csf('order_quantity')];
		$po_color_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]] [$row[csf('color_number_id')]]['plan_cut_qnty']+=$row[csf('plan_cut_qnty')];

		$po_color_size_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]] [$row[csf('color_number_id')]][$row[csf('size_number_id')]]['order_qty']+=$row[csf('order_quantity')];
		$po_color_size_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]] [$row[csf('color_number_id')]][$row[csf('size_number_id')]]['excess_cut']+=$row[csf('excess_cut_perc')];
		$po_color_size_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]] [$row[csf('color_number_id')]][$row[csf('size_number_id')]]['plan_cut_qnty']+=$row[csf('plan_cut_qnty')];

		//$tmp_job[$row[csf('job_no')]]=$row[csf('job_no')];
		//$size_array[$row[csf("job_no")]][$row[csf("size_order")]]=$row[csf("size_number_id")];

		$size_array[$row[csf("job_no")]][$row[csf("size_order")]]=$row[csf("size_number_id")];
	}
	// echo "<pre>";
	// print_r($style_color_size_arr);

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
					$po_rowspan=$po_rowspan*3+1;
					$po_rowspan_arr[$po_id]=$po_rowspan;
					$item_rowspan=$item_rowspan*3+1;

					$item_rowspan_arr[$po_id][$item_id]=$item_rowspan;
				}
				//$po_rowspan++;
				$po_rowspan=$po_rowspan+1;
				$po_rowspan_arr[$po_id]=$po_rowspan;
			}
			//$po_rowspan++;
		}
		//$po_rowspan++;
	}
	?>
    <table id="scroll_body" align="center" style="height:auto; width:1340px; margin:0 auto; padding:0;">
        <tr>
            <td width="1360">
            <?
            foreach($job_no_array as $rdata=>$det)
            {
				$company_id=$det['company_name'];
				$buyer_id=$det['buyer_name'];
                $slab_data=sql_select("SELECT ship_plan from lib_excess_cut_slab where status_active=1 and is_deleted=0 and comapny_id=$company_id and buyer_id=$buyer_id");
				$ship_plan_data=0;
				$is_ship_plan=0;
				if(count($slab_data)>0){
					foreach($slab_data as $row){
						if($row[csf('ship_plan')]>0){
							$ship_plan_data=$row[csf('ship_plan')];
							$is_ship_plan=1;
						}						
					}
				}
                ?><br/>
                <table width="1360px" align="center" border="1" rules="all" id="table_header_1">
                    <tr style="background-color:#FFF">
                        <td width="60" align="right">Job No: </td><td width="90" onClick="openmypage_job_color_size('requires/size_and_color_break_report_controller.php?action=job_color_size&job_no=<?=$det["job_no"]; ?>','Job Color Size');"><a href="##"><?=$det['job_no']; ?></a></td>
                        <td width="60" align="right">Job Qty: </td><td width="90"><?=$det['job_quantity']."(Pcs)"; ?></td>
                        <td width="60" align="right">Company: </td><td width="90"><?=$companyArr[$det['company_name']]; ?></td>
                        <td width="60" align="right">Buyer: </td><td width="85"><?=$buyerArr[$det['buyer_name']]; ?></td>
                        <td width="60" align="right">Brand: </td><td width="85"><?=$brand_nameArr[$det['brand_id']]; ?></td>
                        <td width="60" align="right">Season: </td><td width="85"><?=$seasonArr[$det['season']]; ?></td>
                        <td width="65" align="right">Style Ref.: </td><td width="85">
							<? if($is_ship_plan==1){ ?>
								<a href="##" onClick="openmypage_fabric_dtls('requires/size_and_color_break_report_controller.php?action=job_fabric_dtls&job_no=<?=$det["job_no"]; ?>','Fabric Cost');"><?=$det['style_ref_no']; ?></a>
							<? } else { echo $det['style_ref_no']; } ?>
						</td>
						<td width="65" align="left">Style Description: </td><td width="85"><?=$det['style_description']; ?></td>
                        <td width="70" align="right">Prod. Dept.: </td><td width="80"><?=$product_dept[$det['product_dept']]; ?></td>
                        <td width="60" align="right">Merchant: </td><td width="90"><?=$marchentrArr[$det['dealing_marchant']]; ?></td>
                        <td width="60" align="right">Ord. Re. No: </td><td width="90"><?=$det['order_repeat_no']; ?></td>
                    </tr>
                </table>
                <br/>
                <table width="1340px" align="center" border="1" rules="all" class="rpt_table" id="color_size">
                    <thead>
                        <tr>
                            <!--<th width="60">Sl</th>-->
                            <th width="60">PO Number</th>
                            <? if($presantation_type==1){ ?>
                            <th width="60">File No</th>
                            <th width="70">Ref. No/Master Style</th>
                            <? } ?>
                            <th width="100">Remarks</th>
                            <th width="60">PO Qty and Price</th>
                            <th width="70">Item and SMV </th>
                            <th width="60">Country</th>
                            <th width="60">Color</th>
                            <th width="60">Article No</th>
                            <th width="60">Color Total</th>
                            <?
                            foreach($size_array[$det['job_no']] as $key=>$value)
                            {
                                if($value!="")
                                {
                                    ?><th width="60"><?=$itemSizeArr[$value]; ?></th><?
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
                                $countrysl=1;
                                foreach($po_color_array [$det['job_no']][$key][$item_value][$country_value] as $color_key=>$color_value)
                                {
                                    if($countrysl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                	?>
                                    <tr bgcolor="<?=$bgcolor;?>">
                                        <?
										$pack_handover_date=$po_phd_date_array[$det['job_no']][$key];
                                        if($posl==1)
                                        {											
												 ?><td align="center" valign="middle" rowspan="<?=$po_rowspan_arr[$key]; ?>" ><?=$value; echo "&nbsp;<br/>Pub. Ship Date:".change_date_format($po_ship_date_array[$det['job_no']][$key]['pubshipdate'],"dd-mm-yyyy","-"); echo "<br/>".date('l', strtotime($po_ship_date_array[$det['job_no']][$key]['pubshipdate'])); ?></td><?
                                        }
                                        if($presantation_type==1)
                                        {
                                            if($posl==1)
                                            {
                                            ?><td align="center" valign="middle" rowspan="<?=$po_rowspan_arr[$key]; ?>" ><?=$po_file_no_array[$det['job_no']][$key]['file']; ?></td><?
                                            }
                                            if($posl==1)
                                            {
                                                ?><td align="center" valign="middle" rowspan="<?=$po_rowspan_arr[$key]; ?>" ><?=$po_ref_no_array[$det['job_no']][$key]['ref']; ?></td><?
                                            }
                                        }
                                        if($posl==1)
                                        {
                                            $po_price = $po_qnty_price_array[$det['job_no']][$key];
                                            $qnty = $po_qnty_array[$det['job_no']][$key] ? $po_qnty_array[$det['job_no']][$key] : 0;
                                            ?>
                                            <td align="center" valign="middle" rowspan="<?=$po_rowspan_arr[$key]; ?>"><?=$po_wise_remark[$det['job_no']][$key];?></td>
                                            <td align="center" valign="middle" rowspan="<?=$po_rowspan_arr[$key]; ?>"><?=$qnty.' (Pcs) <br>'.number_format($po_price,2).' <b>'.$currency[$row[csf("currency_id")]].'</b>'; ?></td><?
                                        }
                                        if($itemsl==1)
                                        {
                                            $item_smv_pcs=$smv_no_array[$det[('job_no')]][$key][$item_value]['smv_pcs'];
                                            ?><td align="center" title="<?=$det[('job_no')].'='.$key.'='.$item_value.',smv='.$item_smv_pcs;?>" valign="middle" rowspan="<?=$item_rowspan_arr[$key][$item_key]; ?>" ><?=$garments_item[$item_value].'<br>'.$item_smv_pcs;?></td><?
                                        }
                                        if($countrysl==1)
                                        {
                                         
										  
										    ?><td align="center" valign="middle" rowspan="<?=$country_rowspan_arr[$key][$item_key][$country_key]*3; ?>"><?=$countryArr[$country_value]."<br/>".change_date_format($po_country_ship_date_array [$det['job_no']][$key][$item_value][$country_value],"dd-mm-yyyy","-")."<br/>".date('l', strtotime($po_country_ship_date_array [$det['job_no']][$key][$item_value][$country_value])); ?></td><?
                                        }
                                        ?>
                                        <td align="center" valign="middle"><?=$colorArr[$color_value] ;?></td>
                                        <td align="center" valign="middle"><?=implode(", ", array_filter(array_unique(explode("***", $po_article_array[$det['job_no']][$key][$item_key][$country_key][$color_value]['article_number'])))); ?></td>
                                        <td align="center" valign="middle"><?=$po_color_qnty_array [$det['job_no']][$key][$item_value][$country_value][$color_value]['order_qty']; ?></td>
                                        <?
                                        foreach($size_array[$det['job_no']] as $key_s=>$value_s)
                                        {
                                            if($value_s !="")
                                            {
                                                ?><td align="center" valign="middle"><?=$po_color_size_qnty_array [$det['job_no']][$key][$item_value][$country_value][$color_value][$value_s]['order_qty']; ?></td><?
                                            }
                                        }
                                        ?>
                                    </tr>
									<tr>
										<td align="center" valign="middle" colspan="2"><strong>Ex. Cut %</strong></td>
										<td align="center" valign="middle"></td>
                                        <?
                                        foreach($size_array[$det['job_no']] as $key_s=>$value_s)
                                        {
                                            if($value_s !="")
                                            {
                                                ?><td align="center" valign="middle"><?=$po_color_size_qnty_array [$det['job_no']][$key][$item_value][$country_value][$color_value][$value_s]['excess_cut']; ?></td><?
                                            }
                                        }
                                        ?>
                                    </tr>
									<tr>
										<td align="center" valign="middle" colspan="2"><strong>Plan Cut Qty</strong></td>
                                        <td align="center" valign="middle"><?=$po_color_qnty_array [$det['job_no']][$key][$item_value][$country_value][$color_value]['plan_cut_qnty']; ?></td>
                                        <?
                                        foreach($size_array[$det['job_no']] as $key_s=>$value_s)
                                        {
                                            if($value_s !="")
                                            {
                                                ?><td align="center" valign="middle"><?=$po_color_size_qnty_array [$det['job_no']][$key][$item_value][$country_value][$color_value][$value_s]['plan_cut_qnty']; ?></td><?
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
                                    <td colspan="3">Country Total:</td>
                                    <td align="center" valign="middle"><?=$po_country_qnty_array[$det['job_no']][$key][$item_key][$country_key]; ?></td>
                                    <?
                                    foreach($job_size_array[$det['job_no']] as $key_s=>$value_s)
                                    {
                                        if($value_s !="")
                                        {
                                            ?><td align="center" valign="middle"><?=$po_country_size_tot_qnty_array [$det['job_no']][$key][$item_value][$country_value][$value_s]; ?></td><?
                                        }
                                    }
                                    ?>
                                </tr>
                            <?
                            }
                            ?>
                            <tr style="font-weight:bold; font-size:12px">
                                <td colspan="4">Item Total:</td>
                                <td align="center" valign="middle"><?=$po_item_qnty_array[$det['job_no']][$key][$item_key]; ?></td>
                                <?
                                foreach($size_array[$det['job_no']] as $key_s=>$value_s)
                                {
                                    if($value_s !="")
                                    {
                                        ?><td align="center" valign="middle"><?=$po_item_size_tot_qnty_array [$det['job_no']][$key][$item_value][$value_s]; ?></td><?
                                    }
                                }
                                ?>
                            </tr>
                            <?
                        }
                        if($presantation_type==1) $colspn=9; else $colspn=7;
                        ?>
                        <tr style="font-weight:bold; font-size:12px">
                            <td colspan="<?=$colspn; ?>">Po Total:</td>
                            <td align="center" valign="middle"><?=$po_qnty_color_size_table_array [$det['job_no']][$key]; ?></td>
                            <?
                            foreach($size_array[$det['job_no']] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
                                    ?><td align="center" valign="middle"><?=$po_size_tot_qnty_array [$det['job_no']][$key][$value_s]; ?></td><?
                                }
                            }
                            ?>
                        </tr>
                        <?
                    }
                    ?>
                    <tr style="font-weight:bold; font-size:12px">
                        <td colspan="<?=$colspn; ?>">Grand Total:</td>
                        <td align="center" valign="middle"><?=$job_qnty_color_size_table_array [$det['job_no']]; ?></td>
                        <?
                        foreach($size_array[$det['job_no']] as $key_s=>$value_s)
                        {
                            if($value_s !="")
                            {
                                ?><td align="center" valign="middle"><?=$job_size_tot_qnty_array [$det['job_no']][$value_s]; ?></td><?
                            }
                        }
                        ?>
                    </tr>
					
					<?php
					// $style_color_size_arr[$row[csf('style_ref_no')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['color_size_qnty']
					$no_color=array();
					foreach($style_color_size_arr[$det['style_ref_no']] as $color_id=>$size_data){
						$no_color[$det['style_ref_no']]+=1;
						foreach($size_data as $size_id=>$val){
						$color_sum[$det['style_ref_no']][$color_id]+=$val['color_size_qnty'];
						}
						
					}

					// print_r($no_color);
					//================================issue==19934>26-09-2021=================
					$style="";
					foreach($style_color_size_arr[$det['style_ref_no']] as $color_id=>$size_data)
					{
						?>
							<tr style="font-weight:bold; font-size:12px">
								<?php
								if($style!=$det['style_ref_no']){?>
								<td align="center" rowspan="<?=$no_color[$det['style_ref_no']];?>"><?=$det['style_ref_no'];?></td>
								<td align="center" colspan="<?=$colspn-2; ?>" rowspan="<?=$no_color[$det['style_ref_no']];?>">Color Size Wise Summery</td>
								<?}?>
								<td align="center"><?=$colorArr[$color_id]; ?></td>
								<td align="center"><?=$color_sum[$det['style_ref_no']][$color_id]; ?></td>
								<?
								foreach($size_array[$det['job_no']] as $size_id=>$val){?>
								<td align="center"><?=$size_data[$val]['color_size_qnty']; ?></td><?
									}?>
						</tr>
							<? $style=$det['style_ref_no'];
			   		 }?>
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


if ($action=="report_generate10")
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
	$cbo_season_year=str_replace("'","",$cbo_season_year);
	$cbo_order_status=str_replace("'","",$cbo_order_status);
	$txt_style_description=str_replace("'","",$txt_style_description);
	
	//echo $type.'=kausar'; die;

	if ($cbo_company==0) $company_id=""; else $company_id=" and a.company_name=$cbo_company";
	if ($cbo_buyer==0) $buyer_id=""; else $buyer_id=" and a.buyer_name=$cbo_buyer";
	if ($cbo_brand_id==0) $brand_id_cond=""; else $brand_id_cond=" and a.brand_id=$cbo_brand_id";
	if ($cbo_season_id==0) $season_id_cond=""; else $season_id_cond=" and a.season_buyer_wise=$cbo_season_id";
	if ($cbo_season_year==0) $seasonYearCond=""; else $seasonYearCond=" and a.season_year=$cbo_season_year";
	if ($cbo_order_status==0) $orderStatusCond=""; else $orderStatusCond=" and c.is_confirmed=$cbo_order_status";
	if ($job_no=="") $job_num=""; else $job_num=" and a.job_no='$job_no'";
	if ($job_no=="") $job_num_mst=""; else $job_num_mst=" and a.job_no_prefix_num=$job_no";
	if ($hidd_job==0) $job_id=""; else $job_id=" and a.id in ($hidd_job)";
	if ($txt_file_no=="") $file_cond=""; else $file_cond=" and c.file_no='$txt_file_no'";
	if ($txt_ref_no=="") $ref_cond=""; else $ref_cond=" and c.grouping='$txt_ref_no'";
	if ($job_no=="") $job_num=""; else $job_num=" and a.job_no='$job_no'";
	if ($txt_style_ref=="") $style_ref_cond=""; else $style_ref_cond=" and a.style_ref_no='$txt_style_ref'";
	if ($txt_style_description=="") $style_description_cond=""; else $style_description_cond=" and a.style_description='$txt_style_description'";

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
	  $sql_set= "select a.job_no,a.currency_id, b.smv_pcs,c.id as po_id,b.gmts_item_id from  wo_po_details_mas_set_details b,wo_po_details_master a LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst AND c.is_deleted =0 AND c.status_active =1 where  a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 $company_id $buyer_id".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_id $job_num_mst $po_id $pub_shipment_date2 $brand_id_cond $season_id_cond $year_cond $job_num_mst $style_ref_cond $ref_cond $file_cond $seasonYearCond $orderStatusCond group by a.job_no, c.id, b.smv_pcs, b.gmts_item_id, a.currency_id ";
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
	$job_no_array=array(); $po_details_array=array(); $job_size_array=array(); $po_item_array=array(); $po_country_array=array(); $po_country_ship_date_array=array(); $po_color_array=array(); $job_qnty_color_size_table_array=array(); $job_size_tot_qnty_array=array();

	$po_color_size_qnty_array=array(); $po_color_qnty_array=array(); $po_qnty_array=array(); $plan_cut_array=array(); $po_qnty_color_size_table_array=array(); $po_size_tot_qnty_array=array(); $po_item_qnty_array=array(); $po_item_size_tot_qnty_array=array(); $po_country_qnty_array=array(); $po_country_size_tot_qnty_array=array(); $po_ship_date_array=array(); $po_file_no_array=array(); $po_ref_no_array=array();
	$po_wise_remark=array();

	ob_start();
	if($txt_order_type==1)
	{
		$sql="SELECT a.id as job_id, a.currency_id, a.job_no, a.company_name, a.buyer_name,a.brand_id,a.season_buyer_wise as season, a.style_ref_no,a.style_description, a.product_dept, a.location_name, a.dealing_marchant, (a.job_quantity*a.total_set_qnty) as job_quantity, a.order_uom, a.total_set_qnty, a.order_repeat_no, d.item_number_id as item_number_id, c.id as po_id, c.file_no, c.grouping, c.po_number,c.pack_handover_date, c.pub_shipment_date, c.shipment_date, c.po_received_date, (c.po_quantity*a.total_set_qnty) as po_quantity, c.excess_cut, c.plan_cut, c.unit_price, c.po_total_price, d.id, d.country_id, d.country_ship_date, d.cutup_date, d.size_number_id, d.color_number_id, d.order_quantity, d.order_rate, d.order_total, d.excess_cut_perc, d.plan_cut_qnty, d.size_order,d.article_number,c.details_remarks FROM wo_po_details_master a LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst AND c.is_deleted =0 AND c.status_active =1 LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst AND c.id = d.po_break_down_id AND d.is_deleted =0 AND d.status_active =1 WHERE a.is_deleted =0 AND a.status_active =1 $company_id $buyer_id".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_id $po_id $brand_id_cond $season_id_cond $pub_shipment_date $year_cond $job_num_mst $style_ref_cond $ref_cond $file_cond $seasonYearCond $orderStatusCond $style_description_cond order by d.size_order, a.job_no, c.id, d.country_ship_date, d.color_order";
	}
	else
	{
		$sql="SELECT a.id as job_id, a.currency_id, a.job_no, a.company_name, a.buyer_name,a.brand_id,a.season_buyer_wise as season, a.style_ref_no,a.style_description, a.product_dept, a.location_name, a.dealing_marchant, (a.job_quantity*a.total_set_qnty) as job_quantity, a.order_uom, a.total_set_qnty, a.order_repeat_no, d.item_number_id as item_number_id, c.id as po_id,c.file_no, c.grouping, c.po_number, c.pack_handover_date, c.pub_shipment_date, c.shipment_date, c.po_received_date, (c.po_quantity*a.total_set_qnty) as po_quantity, c.excess_cut, c.plan_cut, c.unit_price, c.po_total_price, d.id,d.country_id, d.country_ship_date, d.cutup_date, d.size_number_id, d.color_number_id, d.order_quantity, d.order_rate, d.order_total, d.excess_cut_perc, d.plan_cut_qnty, d.size_order,d.article_number,c.details_remarks FROM wo_po_details_master a,wo_po_break_down c LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst AND c.id = d.po_break_down_id AND d.is_deleted =0 AND d.status_active =1 AND c.is_deleted =0 AND c.status_active =1 WHERE a.job_no = c.job_no_mst 	AND c.is_deleted =0 AND c.status_active =1 and a.is_deleted =0 AND a.status_active =1 $company_id $buyer_id".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_id $po_id $pub_shipment_date $brand_id_cond $season_id_cond $year_cond $job_num_mst $style_ref_cond $ref_cond $file_cond $seasonYearCond $orderStatusCond $style_description_cond order by d.size_order, a.job_no, d.color_order, c.id, d.country_ship_date";
	}

	//echo $sql;
	$sql_data = sql_select($sql);
	foreach( $sql_data as $row)
	{
		$job_no_array[$row[csf('job_no')]]=array("job_no"=>$row[csf('job_no')],"job_quantity"=>$row[csf('job_quantity')],"company_name"=>$row[csf('company_name')],"buyer_name"=>$row[csf('buyer_name')],"style_ref_no"=>$row[csf('style_ref_no')],"style_description"=>$row[csf('style_description')],"product_dept"=>$row[csf('product_dept')],"dealing_marchant"=>$row[csf('dealing_marchant')],"job_id"=>$row[csf('job_id')],"order_uom"=>$row[csf('order_uom')],"brand_id"=>$row[csf('brand_id')],"season"=>$row[csf('season')]);
		$po_details_array[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('po_number')];
		$po_wise_remark[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('details_remarks')];
		$po_file_no_array[$row[csf('job_no')]][$row[csf('po_id')]]['file']=$row[csf('file_no')];
		$po_ref_no_array[$row[csf('job_no')]][$row[csf('po_id')]]['ref']=$row[csf('grouping')];
		$job_size_array[$row[csf('job_no')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$po_item_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]]=$row[csf('item_number_id')];
		$po_country_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$po_ship_date_array[$row[csf('job_no')]][$row[csf('po_id')]]['shipdate']=$row[csf('shipment_date')];
		$po_ship_date_array[$row[csf('job_no')]][$row[csf('po_id')]]['pubshipdate']=$row[csf('pub_shipment_date')];
		$po_phd_date_array[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('pack_handover_date')];
		$po_country_ship_date_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]=$row[csf('country_ship_date')];
		$po_color_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]] [$row[csf('color_number_id')]]=$row[csf('color_number_id')];

		$style_color_size_arr[$row[csf('style_ref_no')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['color_size_qnty']+=$row[csf('order_quantity')];;

		$job_qnty_color_size_table_array[$row[csf('job_no')]]+=$row[csf('order_quantity')];
		$job_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];

		$po_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('po_quantity')];
		$plan_cut_array[$row[csf('job_no')]][$row[csf('po_id')]]+=$row[csf('plan_cut_qnty')];
		$po_qnty_price_array[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('unit_price')];

		$po_qnty_color_size_table_array[$row[csf('job_no')]][$row[csf('po_id')]]+=$row[csf('order_quantity')];
		$po_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];

		$po_item_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]]+=$row[csf('order_quantity')];
		$po_item_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];

		$po_country_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]+=$row[csf('order_quantity')];
		$po_article_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('color_number_id')]]['article_number'].=$row[csf('article_number')]."***";
		$po_country_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];

		$po_color_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]] [$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
		$po_color_size_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]] [$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
		//$tmp_job[$row[csf('job_no')]]=$row[csf('job_no')];
		//$size_array[$row[csf("job_no")]][$row[csf("size_order")]]=$row[csf("size_number_id")];

		$size_array[$row[csf("job_no")]][$row[csf("size_order")]]=$row[csf("size_number_id")];
	}
	// echo "<pre>";
	// print_r($style_color_size_arr);

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
    <table id="scroll_body" align="center" style="height:auto; width:1340px; margin:0 auto; padding:0;">
        <tr>
            <td width="1480">
            <?
            foreach($job_no_array as $rdata=>$det)
            {
				$company_id=$det['company_name'];
				$buyer_id=$det['buyer_name'];
                $slab_data=sql_select("SELECT ship_plan from lib_excess_cut_slab where status_active=1 and is_deleted=0 and comapny_id=$company_id and buyer_id=$buyer_id");
				$ship_plan_data=0;
				$is_ship_plan=0;
				if(count($slab_data)>0){
					foreach($slab_data as $row){
						if($row[csf('ship_plan')]>0){
							$ship_plan_data=$row[csf('ship_plan')];
							$is_ship_plan=1;
						}						
					}
				}
                ?><br/>
                <table width="1480px" align="center" border="1" rules="all" id="table_header_1">
                    <tr style="background-color:#FFF">
                        <td width="60" align="right">Job No: </td><td width="90" onClick="openmypage_job_color_size('requires/size_and_color_break_report_controller.php?action=job_color_size&job_no=<?=$det["job_no"]; ?>','Job Color Size');"><a href="##"><?=$det['job_no']; ?></a></td>
                        <td width="60" align="right">Job Qty: </td><td width="90"><?=$det['job_quantity']."(Pcs)"; ?></td>
                        <td width="60" align="right">Company: </td><td width="90"><?=$companyArr[$det['company_name']]; ?></td>
                        <td width="60" align="right">Buyer: </td><td width="85"><?=$buyerArr[$det['buyer_name']]; ?></td>
                        <td width="60" align="right">Brand: </td><td width="85"><?=$brand_nameArr[$det['brand_id']]; ?></td>
                        <td width="60" align="right">Season: </td><td width="85"><?=$seasonArr[$det['season']]; ?></td>
                        <td width="65" align="right">Style Ref.: </td><td width="85">
							<? if($is_ship_plan==1){ ?>
								<a href="##" onClick="openmypage_fabric_dtls('requires/size_and_color_break_report_controller.php?action=job_fabric_dtls&job_no=<?=$det["job_no"]; ?>','Fabric Cost');"><?=$det['style_ref_no']; ?></a>
							<? } else { echo $det['style_ref_no']; } ?>
						</td>
						<td width="65" align="left">Style Description: </td><td width="85"><?=$det['style_description']; ?></td>
                        <td width="70" align="right">Prod. Dept.: </td><td width="80"><?=$product_dept[$det['product_dept']]; ?></td>
                        <td width="60" align="right">Merchant: </td><td width="90"><?=$marchentrArr[$det['dealing_marchant']]; ?></td>
                        <td width="60" align="right">Ord. Re. No: </td><td width="90"><?=$det['order_repeat_no']; ?></td>
                    </tr>
                </table>
                <br/>
                <table width="1460px" align="center" border="1" rules="all" class="rpt_table" id="color_size">
                    <thead>
                        <tr>
                            <!--<th width="60">Sl</th>-->
                            <th width="60">PO Number</th>
                            <? if($presantation_type==1){ ?>
                            <th width="60">File No</th>
                            <th width="70">IR/IB</th>
                            <? } ?>
                            <th width="100">Remarks</th>
                            <th width="60">PO Qty and Price</th>
							<th width="60">Plan Cut Qty</th>
							<th width="60">Plan Cut %</th>
                            <th width="70">Item and SMV </th>
                            <th width="60">Country</th>
                            <th width="60">Color</th>
                            <th width="60">Article No</th>
                            <th width="60">Color Total</th>
                            <?
                            foreach($size_array[$det['job_no']] as $key=>$value)
                            {
                                if($value!="")
                                {
                                    ?><th width="60"><?=$itemSizeArr[$value]; ?></th><?
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
                                $countrysl=1;
                                foreach($po_color_array [$det['job_no']][$key][$item_value][$country_value] as $color_key=>$color_value)
                                {
                                    if($countrysl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                ?>
                                    <tr bgcolor="<?=$bgcolor;?>">
                                        <?
										$pack_handover_date=$po_phd_date_array[$det['job_no']][$key];
					
										if($posl==1)
										{
										?>
										<td  align="center" valign="middle" rowspan="<? echo $po_rowspan_arr[$key]; ?>" ><?  echo $value; echo "<br/> Ship Date:".change_date_format($po_ship_date_array[$det['job_no']][$key]['shipdate'],"dd-mm-yyyy","-"); echo "<br/>".date('l', strtotime($po_ship_date_array[$det['job_no']][$key]['shipdate'])); ?></td>
										<?
										}
                                        if($presantation_type==1)
                                        {
                                            if($posl==1)
                                            {
                                            ?><td align="center" valign="middle" rowspan="<?=$po_rowspan_arr[$key]; ?>" ><?=$po_file_no_array[$det['job_no']][$key]['file']; ?></td><?
                                            }
                                            if($posl==1)
                                            {
                                                ?><td align="center" valign="middle" rowspan="<?=$po_rowspan_arr[$key]; ?>" ><?=$po_ref_no_array[$det['job_no']][$key]['ref']; ?></td><?
                                            }
                                        }
                                        if($posl==1)
                                        {
                                            $po_price = $po_qnty_price_array[$det['job_no']][$key];
                                            $qnty = $po_qnty_array[$det['job_no']][$key] ? $po_qnty_array[$det['job_no']][$key] : 0;
											$plan_cut = $plan_cut_array[$det['job_no']][$key] ? $plan_cut_array[$det['job_no']][$key] : 0;
											
                                            ?>
                                            <td align="center" valign="middle" rowspan="<?=$po_rowspan_arr[$key]; ?>"><?=$po_wise_remark[$det['job_no']][$key];?></td>
                                            <td align="center" valign="middle" rowspan="<?=$po_rowspan_arr[$key]; ?>"><?=$qnty.' (Pcs) <br>'.number_format($po_price,2).' <b>'.$currency[$row[csf("currency_id")]].'</b>'; ?></td>
											<?
											$plan_cut_per=(($plan_cut-$qnty)/$qnty)*100;
											?>
											<td align="center" title="(PlanCut-PoQty/POQty)*100=<? echo $plan_cut.'-'.$qnty.'/'.$qnty.'*100';?>" valign="middle" rowspan="<?=$po_rowspan_arr[$key]; ?>"><?=number_format($plan_cut_per,2); ?>%</td>
											<td align="center" valign="middle" rowspan="<?=$po_rowspan_arr[$key]; ?>"><?=$plan_cut; ?></td>
											<?
                                        }
                                        if($itemsl==1)
                                        {
                                            $item_smv_pcs=$smv_no_array[$det[('job_no')]][$key][$item_value]['smv_pcs'];
                                            ?><td align="center" title="<?=$det[('job_no')].'='.$key.'='.$item_value.',smv='.$item_smv_pcs;?>" valign="middle" rowspan="<?=$item_rowspan_arr[$key][$item_key]; ?>" ><?=$garments_item[$item_value].'<br>'.$item_smv_pcs;?></td><?
                                        }
                                        if($countrysl==1)
                                        {
                                         
										  
										    ?><td align="center" valign="middle" rowspan="<?=$country_rowspan_arr[$key][$item_key][$country_key]; ?>"><?=$countryArr[$country_value]."<br/>".change_date_format($po_country_ship_date_array [$det['job_no']][$key][$item_value][$country_value],"dd-mm-yyyy","-")."<br/>".date('l', strtotime($po_country_ship_date_array [$det['job_no']][$key][$item_value][$country_value])); ?></td><?
                                        }
                                        ?>
                                        <td align="center" valign="middle"><?=$colorArr[$color_value] ;?></td>
                                        <td align="center" valign="middle"><?=implode(", ", array_filter(array_unique(explode("***", $po_article_array[$det['job_no']][$key][$item_key][$country_key][$color_value]['article_number'])))); ?></td>
                                        <td align="center" valign="middle"><?=$po_color_qnty_array [$det['job_no']][$key][$item_value][$country_value][$color_value]; ?></td>
                                        <?
                                        foreach($size_array[$det['job_no']] as $key_s=>$value_s)
                                        {
                                            if($value_s !="")
                                            {
                                                ?><td align="center" valign="middle"><?=$po_color_size_qnty_array [$det['job_no']][$key][$item_value][$country_value][$color_value][$value_s]; ?></td><?
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
                                    <td colspan="3">Country Total:</td>
                                    <td align="center" valign="middle"><?=$po_country_qnty_array[$det['job_no']][$key][$item_key][$country_key]; ?></td>
                                    <?
                                    foreach($job_size_array[$det['job_no']] as $key_s=>$value_s)
                                    {
                                        if($value_s !="")
                                        {
                                            ?><td align="center" valign="middle"><?=$po_country_size_tot_qnty_array [$det['job_no']][$key][$item_value][$country_value][$value_s]; ?></td><?
                                        }
                                    }
                                    ?>
                                </tr>
                            <?
                            }
                            ?>
                            <tr style="font-weight:bold; font-size:12px">
                                <td colspan="4">Item Total:</td>
                                <td align="center" valign="middle"><?=$po_item_qnty_array[$det['job_no']][$key][$item_key]; ?></td>
                                <?
                                foreach($size_array[$det['job_no']] as $key_s=>$value_s)
                                {
                                    if($value_s !="")
                                    {
                                        ?><td align="center" valign="middle"><?=$po_item_size_tot_qnty_array [$det['job_no']][$key][$item_value][$value_s]; ?></td><?
                                    }
                                }
                                ?>
                            </tr>
                            <?
                        }
                        if($presantation_type==1) $colspn=11; else $colspn=9;
                        ?>
                        <tr style="font-weight:bold; font-size:12px">
                            <td colspan="<?=$colspn; ?>">Po Total:</td>
                            <td align="center" valign="middle"><?=$po_qnty_color_size_table_array [$det['job_no']][$key]; ?></td>
                            <?
                            foreach($size_array[$det['job_no']] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
                                    ?><td align="center" valign="middle"><?=$po_size_tot_qnty_array [$det['job_no']][$key][$value_s]; ?></td><?
                                }
                            }
                            ?>
                        </tr>
                        <?
                    }
                    ?>
                    <tr style="font-weight:bold; font-size:12px">
                        <td colspan="<?=$colspn; ?>">Grand Total:</td>
                        <td align="center" valign="middle"><?=$job_qnty_color_size_table_array [$det['job_no']]; ?></td>
                        <?
                        foreach($size_array[$det['job_no']] as $key_s=>$value_s)
                        {
                            if($value_s !="")
                            {
                                ?><td align="center" valign="middle"><?=$job_size_tot_qnty_array [$det['job_no']][$value_s]; ?></td><?
                            }
                        }
                        ?>
                    </tr>
					
					<?php
					// $style_color_size_arr[$row[csf('style_ref_no')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['color_size_qnty']
					$no_color=array();
					foreach($style_color_size_arr[$det['style_ref_no']] as $color_id=>$size_data){
						$no_color[$det['style_ref_no']]+=1;
						foreach($size_data as $size_id=>$val){
						$color_sum[$det['style_ref_no']][$color_id]+=$val['color_size_qnty'];
						}
						
					}

					// print_r($no_color);
					//================================issue==19934>26-09-2021=================
					$style="";
					foreach($style_color_size_arr[$det['style_ref_no']] as $color_id=>$size_data)
					{
						?>
							<tr style="font-weight:bold; font-size:12px">
								<?php
								if($style!=$det['style_ref_no']){?>
								<td align="center" rowspan="<?=$no_color[$det['style_ref_no']];?>"><?=$det['style_ref_no'];?></td>
								<td align="center" colspan="<?=$colspn-2; ?>" rowspan="<?=$no_color[$det['style_ref_no']];?>">Color Size Wise Summery</td>
								<?}?>
								<td align="center"><?=$colorArr[$color_id]; ?></td>
								<td align="center"><?=$color_sum[$det['style_ref_no']][$color_id]; ?></td>
								<?
								foreach($size_array[$det['job_no']] as $size_id=>$val){?>
								<td align="center"><?=$size_data[$val]['color_size_qnty']; ?></td><?
									}?>
						</tr>
							<? $style=$det['style_ref_no'];
			   		 }?>
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

if ($action=="report_generate8") //ISD=9873 MD MAMUN AHMED SAGOR 12-05-2022
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
	$cbo_season_year=str_replace("'","",$cbo_season_year);
	$cbo_order_status=str_replace("'","",$cbo_order_status);
	
	

	if ($cbo_company==0) $company_id=""; else $company_id=" and a.company_name=$cbo_company";
	if ($cbo_buyer==0) $buyer_id=""; else $buyer_id=" and a.buyer_name=$cbo_buyer";
	if ($cbo_brand_id==0) $brand_id_cond=""; else $brand_id_cond=" and a.brand_id=$cbo_brand_id";
	if ($cbo_season_id==0) $season_id_cond=""; else $season_id_cond=" and a.season_buyer_wise=$cbo_season_id";
	if ($cbo_season_year==0) $seasonYearCond=""; else $seasonYearCond=" and a.season_year=$cbo_season_year";
	if ($cbo_order_status==0) $orderStatusCond=""; else $orderStatusCond=" and c.is_confirmed=$cbo_order_status";
	
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
			where  a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 $company_id $buyer_id".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_id $job_num_mst $po_id $pub_shipment_date2 $brand_id_cond $season_id_cond $year_cond $job_num_mst $style_ref_cond $ref_cond $file_cond $seasonYearCond $orderStatusCond group by a.job_no, c.id, b.smv_pcs, b.gmts_item_id, a.currency_id ";
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
	$job_no_array=array(); $po_details_array=array(); $job_size_array=array(); $po_item_array=array(); $po_country_array=array(); $po_country_ship_date_array=array(); $po_color_array=array(); $job_qnty_color_size_table_array=array(); $job_size_tot_qnty_array=array();

	$po_color_size_qnty_array=array(); $po_color_qnty_array=array(); $po_qnty_array=array(); $po_qnty_color_size_table_array=array(); $po_size_tot_qnty_array=array(); $po_item_qnty_array=array(); $po_item_size_tot_qnty_array=array(); $po_country_qnty_array=array(); $po_country_size_tot_qnty_array=array(); $po_ship_date_array=array(); $po_file_no_array=array(); $po_ref_no_array=array();
	$po_wise_remark=array();

	ob_start();
	if($txt_order_type==1)
	{
		$sql="SELECT a.id as job_id, a.currency_id, a.job_no, a.company_name, a.buyer_name,a.brand_id,a.season_buyer_wise as season, a.style_ref_no, a.product_dept, a.location_name, a.dealing_marchant, (a.job_quantity*a.total_set_qnty) as job_quantity, a.order_uom, a.total_set_qnty, a.order_repeat_no, d.item_number_id as item_number_id, c.id as po_id, c.file_no, c.grouping, c.po_number,c.pack_handover_date, c.pub_shipment_date, c.shipment_date, c.po_received_date, (c.po_quantity*a.total_set_qnty) as po_quantity, c.excess_cut, c.plan_cut, c.unit_price, c.po_total_price, d.id, d.country_id, d.country_ship_date, d.cutup_date, d.size_number_id, d.color_number_id, d.order_quantity, d.order_rate, d.order_total, d.excess_cut_perc, d.plan_cut_qnty, d.size_order,d.article_number,c.details_remarks

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
		$company_id $buyer_id".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_id $po_id $brand_id_cond $season_id_cond $pub_shipment_date $year_cond $job_num_mst $style_ref_cond $ref_cond $file_cond $seasonYearCond $orderStatusCond order by d.size_order, a.job_no, c.id, d.country_ship_date, d.color_order";
	}
	else
	{
		$sql="SELECT a.id as job_id, a.currency_id, a.job_no, a.company_name, a.buyer_name,a.brand_id,a.season_buyer_wise as season, a.style_ref_no, a.product_dept, a.location_name, a.dealing_marchant, (a.job_quantity*a.total_set_qnty) as job_quantity, a.order_uom, a.total_set_qnty, a.order_repeat_no, d.item_number_id as item_number_id, c.id as po_id,c.file_no, c.grouping, c.po_number, c.pack_handover_date, c.pub_shipment_date, c.shipment_date, c.po_received_date, (c.po_quantity*a.total_set_qnty) as po_quantity, c.excess_cut, c.plan_cut, c.unit_price, c.po_total_price, d.id,d.country_id, d.country_ship_date, d.cutup_date, d.size_number_id, d.color_number_id, d.order_quantity, d.order_rate, d.order_total, d.excess_cut_perc, d.plan_cut_qnty, d.size_order,d.article_number,c.details_remarks

		FROM wo_po_details_master a,wo_po_break_down c
		LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst 
		 
		AND c.id = d.po_break_down_id
		AND d.is_deleted =0
		AND d.status_active =1
		AND c.is_deleted =0
		AND c.status_active =1
		WHERE a.job_no = c.job_no_mst 	AND c.is_deleted =0 AND c.status_active =1 and
		a.is_deleted =0 AND a.status_active =1
		$company_id $buyer_id".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_id $po_id $pub_shipment_date $brand_id_cond $season_id_cond $year_cond $job_num_mst $style_ref_cond $ref_cond $file_cond $seasonYearCond $orderStatusCond order by d.size_order, a.job_no, d.color_order, c.id, d.country_ship_date";
	}

	//echo $sql;
	$sql_data = sql_select($sql);
	foreach( $sql_data as $row)
	{
		$job_no_array[$row[csf('job_no')]]=array("job_no"=>$row[csf('job_no')],"job_quantity"=>$row[csf('job_quantity')],"company_name"=>$row[csf('company_name')],"buyer_name"=>$row[csf('buyer_name')],"style_ref_no"=>$row[csf('style_ref_no')],"product_dept"=>$row[csf('product_dept')],"dealing_marchant"=>$row[csf('dealing_marchant')],"job_id"=>$row[csf('job_id')],"order_uom"=>$row[csf('order_uom')],"brand_id"=>$row[csf('brand_id')],"season"=>$row[csf('season')]);
		$po_details_array[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('po_number')];
		$po_wise_remark[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('details_remarks')];
		$po_file_no_array[$row[csf('job_no')]][$row[csf('po_id')]]['file']=$row[csf('file_no')];
		$po_ref_no_array[$row[csf('job_no')]][$row[csf('po_id')]]['ref']=$row[csf('grouping')];
		$job_size_array[$row[csf('job_no')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$po_item_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]]=$row[csf('item_number_id')];
		$po_country_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$po_ship_date_array[$row[csf('job_no')]][$row[csf('po_id')]]['shipdate']=$row[csf('shipment_date')];
		$po_ship_date_array[$row[csf('job_no')]][$row[csf('po_id')]]['pubshipdate']=$row[csf('pub_shipment_date')];
		$po_phd_date_array[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('pack_handover_date')];
		$po_country_ship_date_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]=$row[csf('country_ship_date')];
		$po_color_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]] [$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$po_article_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('color_number_id')]][$row[csf('article_number')]]=$row[csf('article_number')];
		

		$style_color_size_arr[$row[csf('style_ref_no')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['color_size_qnty']+=$row[csf('order_quantity')];;

		$job_qnty_color_size_table_array[$row[csf('job_no')]]+=$row[csf('order_quantity')];
		$job_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];

		$po_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('po_quantity')];
		$po_qnty_price_array[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('unit_price')];

		$po_qnty_color_size_table_array[$row[csf('job_no')]][$row[csf('po_id')]]+=$row[csf('order_quantity')];
		$po_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];

		$po_item_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]]+=$row[csf('order_quantity')];
		$po_item_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];

		$po_country_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]+=$row[csf('order_quantity')];
		// $po_article_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('color_number_id')]]['article_number'].=$row[csf('article_number')]."***";
		$po_country_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];

		$po_color_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]] [$row[csf('color_number_id')]][$row[csf('article_number')]]+=$row[csf('order_quantity')];
		$po_color_size_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]] [$row[csf('color_number_id')]][$row[csf('article_number')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
		//$tmp_job[$row[csf('job_no')]]=$row[csf('job_no')];
		//$size_array[$row[csf("job_no")]][$row[csf("size_order")]]=$row[csf("size_number_id")];

		$size_array[$row[csf("job_no")]][$row[csf("size_order")]]=$row[csf("size_number_id")];

		$company_wise_summry[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('color_number_id')]][$row[csf('article_number')]]['qnty']+=$row[csf('order_quantity')];
	}
	// echo "<pre>";
	// print_r($style_color_size_arr);

	$po_rowspan_arr=array();
	$item_rowspan_arr=array();
	$country_rowspan_arr=array();
	$color_row_array=array();
	foreach($po_article_array as $job)
	{ 
		foreach($job as $po_id=>$po_value)
		{
			$po_rowspan=0;
			foreach($po_value as $item_id =>$item_value)
			{
				$item_rowspan=0;
				foreach($item_value as $country_id =>$country_value)
				{
					$country_rowspan=0;
					foreach($country_value as $color_id =>$color_value)
					{
						$color_rowspan=0;
						foreach($color_value as $article_key =>$article_value)
						{
						$po_rowspan++;

						$item_rowspan++;
						
						$country_rowspan++;
						$color_rowspan++;
						
						}
						
						$color_row_array[$po_id][$item_id][$country_id][$color_id]=$color_rowspan;
					}

					$country_rowspan_arr[$po_id][$item_id][$country_id]=$country_rowspan;
					
				}
				
				$item_rowspan_arr[$po_id][$item_id]=$item_rowspan;
			
			}
			//$po_rowspan++;
			$po_rowspan_arr[$po_id]=$po_rowspan;
		}
		//$po_rowspan++;
		
		
	}
	?>
	

	<table id="color_size" align="center" border="1" rules="all" class="rpt_table" style="height:auto; width:520px; margin:0 auto; padding:0;">
                    <thead>
						 <tr>                            
                            <th width="100" colspan="6">Summary</th>                        
						</tr>
                        <tr>
                            
                            <th width="100">Company</th>                         
                            <th width="100">Buyer Name</th>
                            <th width="100">Style Ref </th>
                            <th width="100">Color Name</th>                  
                            <th width="60">Article No</th>
                            <th width="60">Inseam Total</th>
						</tr>
                    </thead>
					<body>
						<?php
						$comapny_row_arr=array();$color_row_arr=array();
						$buyer_row_arr=array();	$style_row_arr=array();
					foreach($company_wise_summry as $company_id=>$buyer_data){
						$company_rowspan=0;
						foreach($buyer_data as $buyer_id=>$style_data){
							$buyer_rowspan=0;
							foreach($style_data as $style_id=>$color_data){
								$style_rowspan=0;
								foreach($color_data as $color_id=>$article_data){
									$color_rowspan=0;
									foreach($article_data as $article_id=>$val){


										$company_rowspan++;
										$buyer_rowspan++;
										$color_rowspan++;
										$style_rowspan++;


									}
									
									
									$color_row_arr[$company_id][$buyer_id][$style_id][$color_id]=$color_rowspan;
								}
								$style_row_arr[$company_id][$buyer_id][$style_id]=$style_rowspan;
							}
							$buyer_row_arr[$company_id][$buyer_id]=$buyer_rowspan;
						}
						$comapny_row_arr[$company_id]=$company_rowspan;
					}

					// echo "<pre>";
					// print_r($color_row_array);



					
						foreach($company_wise_summry as $company_id=>$buyer_data){
							$c=1;
							foreach($buyer_data as $buyer_id=>$style_data){
								$b=1;
								foreach($style_data as $style_id=>$color_data){		
									$s=1;							
									foreach($color_data as $color_id=>$article_data){
										$cl=1;
										foreach($article_data as $article_id=>$val){
						                   ?>
												<tr>	
													<?php
													if($c==1){
													?>												
													<td width="60" align="left" rowspan="<?=$comapny_row_arr[$company_id];?>"><?=$companyArr[$company_id];?></td>     
													<?}
													if($b==1){
													?>                    
													<td width="60" align="left" rowspan="<?=$buyer_row_arr[$company_id][$buyer_id];?>"><?=$buyerArr[$buyer_id];?></td>
													<?}
													if($s==1){
													?>
													<td width="70" align="left" rowspan="<?=$style_row_arr[$company_id][$buyer_id][$style_id];?>"><?=$style_id;?> </td>
													<?php
													}
													
													if($cl==1){
													?>
													<td width="60" align="left" rowspan="<?=$color_row_arr[$company_id][$buyer_id][$style_id][$color_id];?>"><?=$colorArr[$color_id];?></td>	
													<?}?>											
													<td width="60" align="left"><?=$article_id;?></td>
													<td width="60" align="right"><?=$val['qnty'];?></td>
												</tr>

					                   	<?			$cl++;$c++;$b++;$s++;}
										}
									}
								}
							}
						?>
					</body>

			    </table>
				
   		 <table id="scroll_body" align="center" style="height:auto; width:1340px; margin:0 auto; padding:0;">
     	   <tr>
            <td width="1360">
            <?
            foreach($job_no_array as $rdata=>$det)
            {
                //ksort($size_array[$det["job_no"]]);
                ?><br/>
                <table width="1280px" align="center" border="1"  class="rpt_table" rules="all" id="table_header_1">
                    <tr style="background-color:#FFF">
                        <td width="60" align="right"><b>Job No: </b></td><td width="90" onClick="openmypage_job_color_size('requires/size_and_color_break_report_controller.php?action=job_color_size&job_no=<?=$det['job_no']; ?>','Job Color Size');"><a href="##"><?=$det['job_no']; ?></a></td>
                        <td width="60" align="right"><b>Job Qty: </b></td><td width="90"><?=$det['job_quantity']."(Pcs)"; ?></td>
                        <td width="60" align="right"><b>Company: </b></td><td width="90"><?=$companyArr[$det['company_name']]; ?></td>
                        <td width="60" align="right"><b>Buyer:</b> </td><td width="85"><?=$buyerArr[$det['buyer_name']]; ?></td>
                        <td width="60" align="right"><b>Brand: </b></td><td width="85"><?=$brand_nameArr[$det['brand_id']]; ?></td>
                        <td width="60" align="right"><b>Season: </b></td><td width="85"><?=$seasonArr[$det['season']]; ?></td>
                        <td width="65" align="right"><b>Style Ref.: </b></td><td width="85"><?=$det['style_ref_no']; ?></td>
                        <td width="70" align="right"><b>Prod. Dept.:</b> </td><td width="80"><?=$product_dept[$det['product_dept']]; ?></td>
                        <td width="60" align="right"><b>Merchant:</b> </td><td width="90"><?=$marchentrArr[$det['dealing_marchant']]; ?></td>
                        <td width="60" align="right"><b>Ord. Re. No:</b></td><td width="90"><?=$det['order_repeat_no']; ?></td>
                    </tr>
                </table>
                <br/>
				

				<br>

                <table width="1340px" align="center" border="1" rules="all" class="rpt_table" id="color_size">
                    <thead>
                        <tr>
                            <!--<th width="60">Sl</th>-->
                            <th width="60">PO Number</th>                         
                            <th width="60">PO Qty and Price</th>
                            <th width="70">Item and SMV </th>
                            <th width="60">Country</th>
                            <th width="60">Color</th>
                            <th width="60">Article No</th>
                            <th width="60">Color Total</th>
                            <?
                            foreach($size_array[$det['job_no']] as $key=>$value)
                            {
                                if($value!="")
                                {
                                    ?><th width="60"><?=$itemSizeArr[$value]; ?></th><?
                                }
                            }
                            ?>
                        </tr>
                    </thead>
                    <?
					// echo "<pre>";
					// print_r($po_rowspan_arr);
                    foreach($po_details_array[$det['job_no']] as $key=>$value)
                    {
                        $posl=1;
                        foreach($po_item_array [$det['job_no']][$key] as $item_key=>$item_value)
                        {
                            $itemsl=1;$article=1;
                            foreach($po_country_array [$det['job_no']][$key][$item_value] as $country_key=>$country_value)
                            {
                                $countrysl=1;
                                foreach($po_color_array [$det['job_no']][$key][$item_value][$country_value] as $color_key=>$color_value)
                                {
									$clr=1;
								 foreach($po_article_array [$det['job_no']][$key][$item_value][$country_value][$color_value] as $article_key=>$article_value)
                                 {
									// $po_article_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('color_number_id')]][$row[csf('article_number')]]=$row[csf('article_number')];
                                    if($countrysl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                ?>
                                    <tr bgcolor="<?=$bgcolor;?>">
                                        <?
										$pack_handover_date=$po_phd_date_array[$det['job_no']][$key];

										$po_price = $po_qnty_price_array[$det['job_no']][$key];
										$qnty = $po_qnty_array[$det['job_no']][$key] ? $po_qnty_array[$det['job_no']][$key] : 0;
                                        
										if($article==1){
											
                                         ?><td align="center" valign="middle" rowspan="<?=$po_rowspan_arr[$key]+2; ?>" ><?=$value; echo "<br/> Ship Date:".change_date_format($po_ship_date_array[$det['job_no']][$key]['shipdate'],"dd-mm-yyyy","-"); echo "<br/>".date('l', strtotime($po_ship_date_array[$det['job_no']][$key]['shipdate'])).'<br> PHD Date: '.change_date_format($pack_handover_date); ?></td>
										  <td align="center" valign="middle" rowspan="<?=$po_rowspan_arr[$key]+2; ?>"><?=$qnty.' (Pcs) <br>'.number_format($po_price,2).' <b>'.$currency[$row[csf("currency_id")]].'</b>'; ?></td>
										 <?
											
										}
                                       
                                      
                                        if($itemsl==1)
                                        {
                                            $item_smv_pcs=$smv_no_array[$det[('job_no')]][$key][$item_value]['smv_pcs'];
                                            ?><td align="center" title="<?=$det[('job_no')].'='.$key.'='.$item_value.',smv='.$item_smv_pcs;?>" valign="middle" rowspan="<?=$item_rowspan_arr[$key][$item_key]+1; ?>" ><?=$garments_item[$item_value].'<br>'.$item_smv_pcs;?></td><?
                                        }
                                        if($countrysl==1)
                                        {
                                         
										  
										    ?><td align="center" valign="middle" rowspan="<?=$country_rowspan_arr[$key][$item_key][$country_key]; ?>"><?=$countryArr[$country_value]."<br/>".change_date_format($po_country_ship_date_array [$det['job_no']][$key][$item_value][$country_value],"dd-mm-yyyy","-")."<br/>".date('l', strtotime($po_country_ship_date_array [$det['job_no']][$key][$item_value][$country_value])); ?></td><?
                                        }
										if($clr==1){

										
										?>
                                        <td  rowspan="<?=$color_row_array[$key][$item_key][$country_key][$color_key];?>"><?=$colorArr[$color_value] ;?></td>
										<?}?>
                                        <td><?=$article_key; ?></td>
                                        <td align="right"><?=$po_color_qnty_array [$det['job_no']][$key][$item_value][$country_value][$color_value][$article_value]; ?></td>
                                        <?
                                        foreach($size_array[$det['job_no']] as $key_s=>$value_s)
                                        {
                                            if($value_s !="")
                                            {
                                                ?><td align="right"><?=$po_color_size_qnty_array [$det['job_no']][$key][$item_value][$country_value][$color_value][$article_value][$value_s]; ?></td><?
                                            }
                                        }
                                        ?>
                                    </tr>
                                    <?
                                    $posl++;
                                    $itemsl++;
                                    $countrysl++;
									$article++;
									$clr++;
                                }
							   }
                                ?>
                                <tr style="font-weight:bold; font-size:12px">
                                    <td colspan="3">Country Total:</td>
                                    <td align="right"><?=$po_country_qnty_array[$det['job_no']][$key][$item_key][$country_key]; ?></td>
                                    <?
                                    foreach($job_size_array[$det['job_no']] as $key_s=>$value_s)
                                    {
                                        if($value_s !="")
                                        {
                                            ?><td align="right"><?=$po_country_size_tot_qnty_array [$det['job_no']][$key][$item_value][$country_value][$value_s]; ?></td><?
                                        }
                                    }
                                    ?>
                                </tr>
                            <?
                            }
                            ?>
                            <tr style="font-weight:bold; font-size:12px">
                                <td colspan="4">Item Total:</td>
                                <td align="right"><?=$po_item_qnty_array[$det['job_no']][$key][$item_key]; ?></td>
                                <?
                                foreach($size_array[$det['job_no']] as $key_s=>$value_s)
                                {
                                    if($value_s !="")
                                    {
                                        ?><td align="right"><?=$po_item_size_tot_qnty_array [$det['job_no']][$key][$item_value][$value_s]; ?></td><?
                                    }
                                }
                                ?>
                            </tr>
                            <?
                        }
                      
                        ?>
                        <tr style="font-weight:bold; font-size:12px">
                            <td colspan="6">Po Total:</td>
                            <td align="right"><?=$po_qnty_color_size_table_array [$det['job_no']][$key]; ?></td>
                            <?
                            foreach($size_array[$det['job_no']] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
                                    ?><td align="right"><?=$po_size_tot_qnty_array [$det['job_no']][$key][$value_s]; ?></td><?
                                }
                            }
                            ?>
                        </tr>
                        <?
                    }
                    ?>
                    <tr style="font-weight:bold; font-size:12px">
                        <td colspan="6">Grand Total:</td>
                        <td align="right"><?=$job_qnty_color_size_table_array [$det['job_no']]; ?></td>
                        <?
                        foreach($size_array[$det['job_no']] as $key_s=>$value_s)
                        {
                            if($value_s !="")
                            {
                                ?><td align="right"><?=$job_size_tot_qnty_array [$det['job_no']][$value_s]; ?></td><?
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
if ($action=="report_generate6")
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
	$cbo_season_year=str_replace("'","",$cbo_season_year);
	$cbo_order_status=str_replace("'","",$cbo_order_status);
	
	//echo $type.'=kausar'; die;

	if ($cbo_company==0) $company_id=""; else $company_id=" and a.company_name=$cbo_company";
	if ($cbo_buyer==0) $buyer_id=""; else $buyer_id=" and a.buyer_name=$cbo_buyer";
	if ($cbo_brand_id==0) $brand_id_cond=""; else $brand_id_cond=" and a.brand_id=$cbo_brand_id";
	if ($cbo_season_id==0) $season_id_cond=""; else $season_id_cond=" and a.season_buyer_wise=$cbo_season_id";
	if ($cbo_season_year==0) $seasonYearCond=""; else $seasonYearCond=" and a.season_year=$cbo_season_year";
	if ($cbo_order_status==0) $orderStatusCond=""; else $orderStatusCond=" and c.is_confirmed=$cbo_order_status";
	
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
	$user_nameArr = return_library_array("select id,user_name from user_passwd ","id","user_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	
	$seasonArr = return_library_array("select id,season_name from lib_buyer_season ","id","season_name");
	$brand_nameArr = return_library_array("select id,brand_name from lib_buyer_brand ","id","brand_name");
	
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
	$destinationArr = return_library_array("select id,ultimate_country_code from  lib_country_loc_mapping ","id","ultimate_country_code");
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
			where  a.job_no=b.job_no and a.status_active=1 and c.matrix_type=3 and a.is_deleted=0 $company_id $buyer_id".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_id $job_num_mst $po_id $pub_shipment_date2 $brand_id_cond $season_id_cond $year_cond $job_num_mst $style_ref_cond $ref_cond $file_cond $seasonYearCond $orderStatusCond group by a.job_no, c.id, b.smv_pcs, b.gmts_item_id, a.currency_id ";
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
	$job_no_array=array(); $po_details_array=array(); $job_size_array=array(); $po_item_array=array(); $po_country_array=array(); $po_country_ship_date_array=array(); $po_color_array=array(); $job_qnty_color_size_table_array=array(); $job_size_tot_qnty_array=array();

	$po_color_size_qnty_array=array(); $po_color_qnty_array=array(); $po_qnty_array=array(); $po_qnty_color_size_table_array=array(); $po_size_tot_qnty_array=array(); $po_item_qnty_array=array(); $po_item_size_tot_qnty_array=array(); $po_country_qnty_array=array(); $po_country_size_tot_qnty_array=array(); $po_ship_date_array=array(); $po_file_no_array=array(); $po_ref_no_array=array();

	ob_start();
	if($txt_order_type==1)
	{
		$sql="SELECT a.id as job_id, a.currency_id, a.job_no, a.company_name, a.buyer_name,a.brand_id,a.season_buyer_wise as season, a.style_ref_no, a.product_dept, a.location_name, a.dealing_marchant, (a.job_quantity*a.total_set_qnty) as job_quantity, a.order_uom, a.total_set_qnty, a.order_repeat_no, d.item_number_id as item_number_id, c.id as po_id, c.file_no, c.grouping, c.po_number,c.pack_handover_date, c.pub_shipment_date, c.shipment_date, c.po_received_date, (c.po_quantity*a.total_set_qnty) as po_quantity, c.excess_cut, c.plan_cut, c.unit_price, c.po_total_price, d.id, d.country_id, d.country_ship_date, d.cutup_date, d.size_number_id, d.color_number_id, d.order_quantity, d.order_rate, d.order_total, d.excess_cut_perc, d.plan_cut_qnty, d.size_order,d.article_number,d.code_id,c.inserted_by,c.insert_date

		FROM wo_po_details_master a
		LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
		AND c.is_deleted =0
		AND c.status_active =1
		LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
		AND c.id = d.po_break_down_id
		AND d.is_deleted =0
		AND d.status_active =1
		and c.matrix_type=3
		WHERE
		a.is_deleted =0
		AND a.status_active =1
		$company_id $buyer_id".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_id $po_id $brand_id_cond $season_id_cond $pub_shipment_date $year_cond $job_num_mst $style_ref_cond $ref_cond $file_cond $seasonYearCond $orderStatusCond order by d.size_order, a.job_no, c.id, d.country_ship_date, d.color_order";
	}
	else
	{
		$sql="SELECT a.id as job_id, a.currency_id, a.job_no, a.company_name, a.buyer_name,a.brand_id,a.season_buyer_wise as season, a.style_ref_no, a.product_dept, a.location_name, a.dealing_marchant, (a.job_quantity*a.total_set_qnty) as job_quantity, a.order_uom, a.total_set_qnty, a.order_repeat_no, d.item_number_id as item_number_id, c.id as po_id,c.file_no, c.grouping, c.po_number, c.pack_handover_date, c.pub_shipment_date, c.shipment_date, c.po_received_date, (c.po_quantity*a.total_set_qnty) as po_quantity, c.excess_cut, c.plan_cut, c.unit_price, c.po_total_price, d.id,d.country_id, d.country_ship_date, d.cutup_date, d.size_number_id, d.color_number_id, d.order_quantity, d.order_rate, d.order_total, d.excess_cut_perc, d.plan_cut_qnty, d.size_order,d.article_number,d.code_id,c.inserted_by,c.insert_date

		FROM wo_po_details_master a,wo_po_break_down c
		LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst 
		 
		AND c.id = d.po_break_down_id
		AND d.is_deleted =0
		AND d.status_active =1
		AND c.is_deleted =0
		AND c.status_active =1
		and c.matrix_type=3
		WHERE a.job_no = c.job_no_mst 	AND c.is_deleted =0 AND c.status_active =1 and
		a.is_deleted =0 AND a.status_active =1
		$company_id $buyer_id".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_id $po_id $pub_shipment_date $brand_id_cond $season_id_cond $year_cond $job_num_mst $style_ref_cond $ref_cond $file_cond $seasonYearCond $orderStatusCond order by d.size_order, a.job_no, d.color_order, c.id, d.country_ship_date";
	}

	//echo $sql;
	$sql_data = sql_select($sql);
	foreach( $sql_data as $row)
	{
		$job_no_array[$row[csf('job_no')]]=array("job_no"=>$row[csf('job_no')],"job_quantity"=>$row[csf('job_quantity')],"company_name"=>$row[csf('company_name')],"buyer_name"=>$row[csf('buyer_name')],"style_ref_no"=>$row[csf('style_ref_no')],"product_dept"=>$row[csf('product_dept')],"dealing_marchant"=>$row[csf('dealing_marchant')],"job_id"=>$row[csf('job_id')],"order_uom"=>$row[csf('order_uom')],"brand_id"=>$row[csf('brand_id')],"season"=>$row[csf('season')]);
		$po_details_array[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('po_number')];
		$po_file_no_array[$row[csf('job_no')]][$row[csf('po_id')]]['file']=$row[csf('file_no')];
		$po_file_no_array[$row[csf('job_no')]][$row[csf('po_id')]]['user_id']=$user_nameArr[$row[csf('inserted_by')]];
		$po_file_no_array[$row[csf('job_no')]][$row[csf('po_id')]]['po_received_date']=$row[csf('po_received_date')];
		$po_file_no_array[$row[csf('job_no')]][$row[csf('po_id')]]['po_insert_date']=$row[csf('insert_date')];

		$po_ref_no_array[$row[csf('job_no')]][$row[csf('po_id')]]['ref']=$row[csf('grouping')];
		$job_size_array[$row[csf('job_no')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$po_item_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]]=$row[csf('item_number_id')];
		$po_country_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$po_ship_date_array[$row[csf('job_no')]][$row[csf('po_id')]]['shipdate']=$row[csf('shipment_date')];
		$po_ship_date_array[$row[csf('job_no')]][$row[csf('po_id')]]['pubshipdate']=$row[csf('pub_shipment_date')];
		$po_phd_date_array[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('pack_handover_date')];
		$po_country_ship_date_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]=$row[csf('country_ship_date')];
		$po_country_destination_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]=$destinationArr[$row[csf('code_id')]];
		$po_color_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]] [$row[csf('color_number_id')]]=$row[csf('color_number_id')];

		$style_color_size_arr[$row[csf('style_ref_no')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['color_size_qnty']+=$row[csf('order_quantity')];;

		$job_qnty_color_size_table_array[$row[csf('job_no')]]+=$row[csf('order_quantity')];
		$job_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];

		$po_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('po_quantity')];
		$po_qnty_price_array[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('unit_price')];

		$po_qnty_color_size_table_array[$row[csf('job_no')]][$row[csf('po_id')]]+=$row[csf('order_quantity')];
		$po_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];

		$po_item_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]]+=$row[csf('order_quantity')];
		$po_item_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];

		$po_country_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]+=$row[csf('order_quantity')];
		$po_article_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('color_number_id')]]['article_number']=$row[csf('article_number')];
		$po_country_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];

		$po_color_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]] [$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
		$po_color_size_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]] [$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
		//$tmp_job[$row[csf('job_no')]]=$row[csf('job_no')];
		//$size_array[$row[csf("job_no")]][$row[csf("size_order")]]=$row[csf("size_number_id")];

		$size_array[$row[csf("job_no")]][$row[csf("size_order")]]=$row[csf("size_number_id")];
		$job_id_arr[$row[csf("job_id")]]=$row[csf("job_id")];
	}
	// echo "<pre>";
	// print_r($style_color_size_arr);
	$sql_ratio=sql_select("select id, job_id, po_id, country_id, gmts_item_id, country_ship_date, color_id, size_id, ratio_qty, ratio_rate, ultimate_country_id, code_id, ul_country_code from  wo_po_ratio_breakdown where  status_active=1  ".where_con_using_array($job_id_arr,1,'job_id')." and is_deleted=0 order by id ASC");

	foreach($sql_ratio as $row){
		$po_color_size_ratio_arr[$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("gmts_item_id")]][$row[csf("color_id")]][$row[csf("size_id")]]['ratio_qty']+=$row[csf("ratio_qty")];
		$po_color_ratio_arr[$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("gmts_item_id")]][$row[csf("color_id")]]['ratio_qty']+=$row[csf("ratio_qty")];

	}
	// 	echo "<pre>";
	//  print_r($po_color_size_ratio_arr);

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
                ?><br/>
                <table width="1910px" align="center" border="1" rules="all" id="table_header_1">
                    <tr style="background-color:#FFF">
                        <td width="60" align="right">Job No: </td><td width="90" onClick="openmypage_job_color_size('requires/size_and_color_break_report_controller.php?action=job_color_size&job_no=<?=$det["job_no"]; ?>','Job Color Size');"><a href="##"><?=$det['job_no']; ?></a></td>
                        <td width="60" align="right">Job Qty: </td><td width="90"><?=$det['job_quantity']."(Pcs)"; ?></td>
                        <td width="60" align="right">Company: </td><td width="90"colspan="2"><?=$companyArr[$det['company_name']]; ?></td>
                        <td width="60" align="right" >Buyer/Brand: </td>
						<td width="85" colspan="2"><?=$buyerArr[$det['buyer_name']]."/".$brand_nameArr[$det['brand_id']]; ?></td>
               
                        <td width="60" align="right">Season: </td><td width="85"><?=$seasonArr[$det['season']]; ?></td>
                        <td width="65" align="right">Style Ref.: </td><td width="85"><?=$det['style_ref_no']; ?></td>
                        <td width="70" align="right">Prod. Dept.: </td><td width="80"><?=$product_dept[$det['product_dept']]; ?></td>
                        <td width="60" align="right">Merchant: </td><td width="90"><?=$marchentrArr[$det['dealing_marchant']]; ?></td>
                      
                    </tr>
                </table>
                <br/>
                <table width="1910px" align="center" border="1" rules="all" class="rpt_table" id="color_size">
                    <thead>
                        <tr>
                            <!--<th width="60">Sl</th>-->
                          
							<th width="30">SL/N</th>
							<th width="100">User Name</th>
							<th width="100">Po insert Date </th>
							<th width="100">PO Received Date</th>
							<th width="100">Ship Date </th>
							<th width="100">Country Ship Date</th>
							<th width="100">PHD Date </th>


                            <th width="100">File No</th>
							
                            <th width="70">Price(USD)</th>
							<th width="60">Country</th>
							<th width="70">Destination Code </th>
                
							<th width="60">PO Number</th>
                            <th width="60">PO Qty (Pcs)</th>
							<th width="60">Country Total</th>
                            <th width="60">Color</th>
                            <th width="60">Color/Ratio Total</th>
                            
                            <?
                            foreach($size_array[$det['job_no']] as $key=>$value)
                            {
                                if($value!="")
                                {
                                    ?><th width="60"><?=$itemSizeArr[$value]; ?></th><?
                                }
                            }
                            ?>
                        </tr>
                    </thead>
                    <?
					$i=1;
					$tot_color_ratio_qty=0;
                    foreach($po_details_array[$det['job_no']] as $key=>$value)
                    {
                        $posl=1;
                        foreach($po_item_array [$det['job_no']][$key] as $item_key=>$item_value)
                        {
                            $itemsl=1;
                            foreach($po_country_array [$det['job_no']][$key][$item_value] as $country_key=>$country_value)
                            {
                                $countrysl=1;
                                foreach($po_color_array [$det['job_no']][$key][$item_value][$country_value] as $color_key=>$color_value)
                                {
                                    if($countrysl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                ?>
                                    <tr bgcolor="<?=$bgcolor;?>">
                                        <?
										$pack_handover_date=$po_phd_date_array[$det['job_no']][$key];
										
                                        if($posl==1){

                                            ?>
											<td align="center" valign="middle" >&nbsp;<?=$i; ?></td>
									
											<td align="center" valign="middle" >&nbsp;<?=$po_file_no_array[$det['job_no']][$key]['user_id']; ?></td>
											<td align="center" valign="middle"  >&nbsp;<?=$po_file_no_array[$det['job_no']][$key]['po_insert_date']; ?></td>
											<td align="center" valign="middle"  >&nbsp;<?=$po_file_no_array[$det['job_no']][$key]['po_received_date']; ?></td>
										
											<td align="center" valign="middle"  >&nbsp;<?=$po_ship_date_array[$det['job_no']][$key]['shipdate']; ?></td>
											<td align="center" valign="middle"  >&nbsp;<?=$po_country_ship_date_array[$det['job_no']][$key][$item_key][$country_key]; ?></td>
											<td align="center" valign="middle"  >&nbsp;<?=$po_phd_date_array[$det['job_no']][$key]; ?></td>

											<td align="center" valign="middle"  >&nbsp;<?=$po_file_no_array[$det['job_no']][$key]['file']; ?></td>
											<?
                                            
                                          
											$po_price = $po_qnty_price_array[$det['job_no']][$key];
											?><td align="center" valign="middle" rowspan="<?=$po_rowspan_arr[$key]-2; ?>" >&nbsp;<?=number_format($po_price,2).' <b>'.$currency[$row[csf("currency_id")]]; ?></td>
											<td align="center" valign="middle" rowspan="<?=$country_rowspan_arr[$key][$item_key][$country_key]; ?>">&nbsp;<?=$countryArr[$country_value]."<br/>"; ?></td>
											<?
                                            $destination=$po_country_destination_array[$det['job_no']][$key][$item_key][$country_key];
                                            ?><td align="center"  valign="middle" rowspan="<?=$item_rowspan_arr[$key][$item_key]-1; ?>" >&nbsp;<?=$destination;?></td>
                                     
											<td align="right" valign="middle" rowspan="<?=$po_rowspan_arr[$key]-2; ?>" >&nbsp;<?=$value;  ?></td>
											<?
                                      		 $color_ratio_qty=$po_color_ratio_arr[$key][$country_key][$item_value][$color_value]['ratio_qty'];
											   $tot_color_ratio_qty+=$po_color_qnty_array[$det['job_no']][$key][$item_value][$country_value][$color_value];
                                           
                                            $qnty = $po_qnty_array[$det['job_no']][$key] ? $po_qnty_array[$det['job_no']][$key] : 0;
                                            ?><td align="right" valign="middle" rowspan="<?=$po_rowspan_arr[$key]-2; ?>">&nbsp;<?=$qnty; ?></td><?
                                        }?>
                                        <td align="right">&nbsp;<?=$po_country_qnty_array[$det['job_no']][$key][$item_value][$country_value]; ?></td>
                                        <td>&nbsp;<?=$colorArr[$color_value] ;?></td>
                                        <td align="right">&nbsp;<?=$po_color_qnty_array[$det['job_no']][$key][$item_value][$country_value][$color_value]."<br><b style='color:red;'>[". $color_ratio_qty."]<b>"; ?></td>
                                 
                                        <?
                                        foreach($size_array[$det['job_no']] as $key_s=>$value_s)
                                        {
                                            if($value_s !="")
                                            {
												$ratio_qty=$po_color_size_ratio_arr[$key][$country_key][$item_value][$color_value][$value_s]['ratio_qty'];
                                                ?><td align="right">&nbsp;<?=$po_color_size_qnty_array[$det['job_no']][$key][$item_value][$country_value][$color_value][$value_s]."<br><b style='color:red;'>[".$ratio_qty."]<b>"; ?></td><?
                                            }
                                        }
                                        ?>
                                    </tr>
                                    <?
                                    $posl++;
                                    $itemsl++;
                                    $countrysl++;
									$i++;
                                }
                                ?>
                               
                            <?
                            }
                            ?>
                          
                            <?
                        }
                    
                    }
                    ?>
                   
					
					<?php
					// $style_color_size_arr[$row[csf('style_ref_no')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['color_size_qnty']
					$no_color=array();
					foreach($style_color_size_arr[$det['style_ref_no']] as $color_id=>$size_data){
						$no_color[$det['style_ref_no']]+=1;
						foreach($size_data as $size_id=>$val){
						$color_sum[$det['style_ref_no']][$color_id]+=$val['color_size_qnty'];
						$size_wise_grand_tot[$size_id]+=$val['color_size_qnty'];
						}
						
					}

					
					//================================issue==19934>26-09-2021=================
					$style="";
				
						?>
							<tr style="font-weight:bold; font-size:12px">
								
								
								<td align="right" colspan="15">The Grand Total:</td>		
								<td align="right" ><?=$tot_color_ratio_qty;?></td>								
								
								<?
								foreach($size_array as $job_id=>$job_data){
									foreach($job_data as $size_id=>$val){

									
									?>
								<td align="right"><?=$size_wise_grand_tot[$val];//$size_data[$val]['color_size_qnty']; ?></td><?
									}}?>
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
if ($action=="report_generate_show3")
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
	$cbo_season_year=str_replace("'","",$cbo_season_year);
	$cbo_order_status=str_replace("'","",$cbo_order_status);

	if ($cbo_company==0) $company_id=""; else $company_id=" and a.company_name=$cbo_company";
	if ($cbo_buyer==0) $buyer_id=""; else $buyer_id=" and a.buyer_name=$cbo_buyer";
	if ($cbo_brand_id==0) $brand_id_cond=""; else $brand_id_cond=" and a.brand_id=$cbo_brand_id";
	if ($cbo_season_id==0) $season_id_cond=""; else $season_id_cond=" and a.season_buyer_wise=$cbo_season_id";
	if ($cbo_season_year==0) $seasonYearCond=""; else $seasonYearCond=" and a.season_year=$cbo_season_year";
	if ($cbo_order_status==0) $orderStatusCond=""; else $orderStatusCond=" and c.is_confirmed=$cbo_order_status";
	
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
	  $sql_set= "select a.job_no,a.currency_id, b.smv_pcs,c.id as po_id,b.gmts_item_id from wo_po_details_mas_set_details b,wo_po_details_master a
			LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
			AND c.is_deleted =0 AND c.status_active =1
			where  a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0   $company_id $buyer_id".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_id $job_num_mst $po_id $pub_shipment_date2 $brand_id_cond $season_id_cond $year_cond $job_num_mst $style_ref_cond $ref_cond $file_cond $seasonYearCond $orderStatusCond group by a.job_no,c.id, b.smv_pcs,b.gmts_item_id,a.currency_id ";
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
	$job_no_array=array(); $po_details_array=array(); $job_size_array=array(); $po_item_array=array(); $po_country_array=array(); $po_country_ship_date_array=array(); $po_color_array=array(); $job_qnty_color_size_table_array=array(); $job_size_tot_qnty_array=array();

	$po_color_size_qnty_array=array(); $po_color_qnty_array=array(); $po_qnty_array=array(); $po_qnty_color_size_table_array=array(); $po_size_tot_qnty_array=array(); $po_item_qnty_array=array(); $po_item_size_tot_qnty_array=array(); $po_country_qnty_array=array(); $po_country_size_tot_qnty_array=array(); $po_ship_date_array=array(); $po_file_no_array=array(); $po_ref_no_array=array();

	ob_start();
	if($txt_order_type==1)
	{
		$sql="SELECT a.id as job_id, a.currency_id, a.job_no, a.company_name, a.buyer_name,a.brand_id,a.season_buyer_wise as season, a.style_ref_no, a.product_dept, a.location_name, a.dealing_marchant, (a.job_quantity*a.total_set_qnty) as job_quantity, a.order_uom, a.total_set_qnty, a.order_repeat_no, d.item_number_id as item_number_id, c.id as po_id, c.file_no, c.grouping, c.po_number,c.pack_handover_date, c.pub_shipment_date, c.shipment_date, c.po_received_date, (c.po_quantity*a.total_set_qnty) as po_quantity, c.excess_cut, c.plan_cut, c.unit_price, c.po_total_price, d.id, d.country_id, d.country_ship_date, d.cutup_date, d.size_number_id, d.color_number_id, d.order_quantity, d.order_rate, d.order_total, d.excess_cut_perc, d.plan_cut_qnty, d.size_order,d.article_number

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
		$company_id $buyer_id".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_id $po_id $brand_id_cond $season_id_cond $pub_shipment_date $year_cond $job_num_mst $style_ref_cond $ref_cond $file_cond $seasonYearCond $orderStatusCond order by d.size_order, a.job_no, c.id, d.country_ship_date, d.color_order";
	}
	else
	{
		$sql="SELECT a.id as job_id, a.currency_id, a.job_no, a.company_name, a.buyer_name,a.brand_id,a.season_buyer_wise as season, a.style_ref_no, a.product_dept, a.location_name, a.dealing_marchant, (a.job_quantity*a.total_set_qnty) as job_quantity, a.order_uom, a.total_set_qnty, a.order_repeat_no, d.item_number_id as item_number_id, c.id as po_id,c.file_no, c.grouping, c.po_number,c.pack_handover_date, c.pub_shipment_date, c.shipment_date, c.po_received_date, (c.po_quantity*a.total_set_qnty) as po_quantity, c.excess_cut, c.plan_cut, c.unit_price, c.po_total_price, d.id,d.country_id, d.country_ship_date, d.cutup_date, d.size_number_id, d.color_number_id, d.order_quantity, d.order_rate, d.order_total, d.excess_cut_perc, d.plan_cut_qnty, d.size_order,d.article_number

		FROM wo_po_details_master a,wo_po_break_down c
		LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst 
		 
		AND c.id = d.po_break_down_id
		AND d.is_deleted =0
		AND d.status_active =1
		AND c.is_deleted =0
		AND c.status_active =1
		WHERE a.job_no = c.job_no_mst 	AND c.is_deleted =0 AND c.status_active =1 and
		a.is_deleted =0 AND a.status_active =1
		$company_id $buyer_id".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_id $po_id $pub_shipment_date $brand_id_cond $season_id_cond $year_cond $job_num_mst $style_ref_cond $ref_cond $file_cond $seasonYearCond $orderStatusCond order by d.size_order, a.job_no, d.color_order, c.id, d.country_ship_date";
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
		$po_phd_date_array[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('pack_handover_date')];
		$po_country_ship_date_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]=$row[csf('country_ship_date')];
		$po_color_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]] [$row[csf('color_number_id')]]=$row[csf('color_number_id')];

		$style_color_size_arr[$row[csf('style_ref_no')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['color_size_qnty']+=$row[csf('order_quantity')];;

		$job_qnty_color_size_table_array[$row[csf('job_no')]]+=$row[csf('order_quantity')];
		$job_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];

		$po_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('po_quantity')];
		$po_qnty_price_array[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('unit_price')];

		$po_qnty_color_size_table_array[$row[csf('job_no')]][$row[csf('po_id')]]+=$row[csf('order_quantity')];
		$po_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];

		$po_item_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]]+=$row[csf('order_quantity')];
		$po_item_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];

		$po_country_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]+=$row[csf('order_quantity')];
		$po_article_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('color_number_id')]]['article_number']=$row[csf('article_number')];
		$po_country_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];

		$po_color_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]] [$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
		$po_color_size_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]] [$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
		//$tmp_job[$row[csf('job_no')]]=$row[csf('job_no')];
		//$size_array[$row[csf("job_no")]][$row[csf("size_order")]]=$row[csf("size_number_id")];

		$size_array[$row[csf("job_no")]][$row[csf("size_order")]]=$row[csf("size_number_id")];
		$job_arr[$row[csf("job_no")]]=$row[csf("job_no")];
	}
	// echo "<pre>";
	// print_r($style_color_size_arr);

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
		//===============================fabrication query===================================
		$fab_sql="select  a.id,a.company_id,a.fabrication,a.style_ref_id,a.set_break_down,a.style_refernce,b.job_no 
		from wo_quotation_inquery a ,wo_po_details_master b	where a.style_refernce=b.style_ref_no ".where_con_using_array($job_arr,1,'b.job_no')." order by a.id";
		$fab_data=sql_select($fab_sql);
		foreach($fab_data as $val){
	
			$job_fab_arr[$val[csf("job_no")]]['fab_desc']=$val[csf("fabrication")];
		}
	?>
    <table id="scroll_body" align="center" style="height:auto; width:1240px; margin:0 auto; padding:0;">
        <tr>
            <td width="1260">
            <?
            foreach($job_no_array as $rdata=>$det)
            {
                //ksort($size_array[$det["job_no"]]);
                ?><br/>
                <table width="1180px" align="center" border="1" rules="all" id="table_header_1">
                    <tr style="background-color:#FFF">
                        <td width="60" align="right">Job No: </td><td width="90" onClick="openmypage_job_color_size('requires/size_and_color_break_report_controller.php?action=job_color_size&job_no=<?=$det["job_no"]; ?>','Job Color Size');"><a href="##"><?=$det['job_no']; ?></a></td>
                        <td width="60" align="right">Job Qty: </td><td width="90"><?=$det['job_quantity']."(Pcs)"; ?></td>
                        <td width="60" align="right">Company: </td><td width="90"><?=$companyArr[$det['company_name']]; ?></td>
                        <td width="60" align="right">Buyer: </td><td width="85"><?=$buyerArr[$det['buyer_name']]; ?></td>
                        <td width="60" align="right">Brand: </td><td width="85"><?=$brand_nameArr[$det['brand_id']]; ?></td>
                        <td width="60" align="right">Season: </td><td width="85"><?=$seasonArr[$det['season']]; ?></td>
                        <td width="65" align="right">Style Ref.: </td><td width="85"><?=$det['style_ref_no']; ?></td>
                        <td width="70" align="right">Prod. Dept.: </td><td width="80"><?=$product_dept[$det['product_dept']]; ?></td>
                        <td width="60" align="right">Merchant: </td><td width="90"><?=$marchentrArr[$det['dealing_marchant']]; ?></td>
                        <td width="60" align="right">Ord. Re. No: </td><td width="90"><?=$det['order_repeat_no']; ?></td>
                    </tr>
                </table>
                <br/>
                <table width="1240px" align="center" border="1" rules="all" class="rpt_table" id="color_size">
                    <thead>
                        <tr>
                            <!--<th width="60">Sl</th>-->
                            <th width="60">PO Number</th>
                            <? if($presantation_type==1){ ?>
                            <th width="60">File No</th>
                            <th width="70">Ref. No/Master Style</th>
                            <? } ?>
                            <th width="60">PO Qty and Price</th>
                            <th width="70">Fabrication </th>
                            <th width="60">Country</th>
                            <th width="60">Color</th>
                            <th width="60">Article No</th>
                            <th width="60">Color Total</th>
                            <?
                            foreach($size_array[$det['job_no']] as $key=>$value)
                            {
                                if($value!="")
                                {
                                    ?><th width="60"><?=$itemSizeArr[$value]; ?></th><?
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
                                $countrysl=1;
                                foreach($po_color_array [$det['job_no']][$key][$item_value][$country_value] as $color_key=>$color_value)
                                {
                                    if($countrysl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                ?>
                                    <tr bgcolor="<?=$bgcolor;?>">
                                        <?
										$pack_handover_date=$po_phd_date_array[$det['job_no']][$key];
                                        if($posl==1)
                                        {
                                            ?><td align="center" valign="middle" rowspan="<?=$po_rowspan_arr[$key]; ?>" ><?=$value; echo "<br/> Ship Date:".change_date_format($po_ship_date_array[$det['job_no']][$key],"dd-mm-yyyy","-"); echo "<br/>".date('l', strtotime($po_ship_date_array[$det['job_no']][$key])).'<br> PHD Date: '.change_date_format($pack_handover_date); ?></td><?
                                        }
                                        if($presantation_type==1)
                                        {
                                            if($posl==1)
                                            {
                                            ?><td align="center" valign="middle" rowspan="<?=$po_rowspan_arr[$key]; ?>" ><?=$po_file_no_array[$det['job_no']][$key]['file']; ?></td><?
                                            }
                                            if($posl==1)
                                            {
                                                ?><td align="center" valign="middle" rowspan="<?=$po_rowspan_arr[$key]; ?>" ><?=$po_ref_no_array[$det['job_no']][$key]['ref']; ?></td><?
                                            }
                                        }
                                        if($posl==1)
                                        {
                                            $po_price = $po_qnty_price_array[$det['job_no']][$key];
                                            $qnty = $po_qnty_array[$det['job_no']][$key] ? $po_qnty_array[$det['job_no']][$key] : 0;
                                            ?><td align="center" valign="middle" rowspan="<?=$po_rowspan_arr[$key]; ?>"><?=$qnty.' (Pcs) <br>'.number_format($po_price,2).' <b>'.$currency[$row[csf("currency_id")]].'</b>'; ?></td><?
                                        }
                                        if($itemsl==1)
                                        {
                                           
                                            ?><td align="center" valign="middle"  title="Fabrication from Inquery Page" rowspan="<?=$item_rowspan_arr[$key][$item_key]; ?>" ><?=$job_fab_arr[$rdata]['fab_desc'];?></td><?
                                        }
                                        if($countrysl==1)
                                        {
                                         
										  
										    ?><td align="center" valign="middle" rowspan="<?=$country_rowspan_arr[$key][$item_key][$country_key]; ?>"><?=$countryArr[$country_value]."<br/>".change_date_format($po_country_ship_date_array [$det['job_no']][$key][$item_value][$country_value],"dd-mm-yyyy","-")."<br/>".date('l', strtotime($po_country_ship_date_array [$det['job_no']][$key][$item_value][$country_value])); ?></td><?
                                        }
                                        ?>
                                        <td><?=$colorArr[$color_value] ;?></td>
                                        <td><?=$po_article_array[$det['job_no']][$key][$item_key][$country_key][$color_value]['article_number']; ?></td>
                                        <td align="right"><?=$po_color_qnty_array [$det['job_no']][$key][$item_value][$country_value][$color_value]; ?></td>
                                        <?
                                        foreach($size_array[$det['job_no']] as $key_s=>$value_s)
                                        {
                                            if($value_s !="")
                                            {
                                                ?><td align="right"><?=$po_color_size_qnty_array [$det['job_no']][$key][$item_value][$country_value][$color_value][$value_s]; ?></td><?
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
                                    <td colspan="3">Country Total:</td>
                                    <td align="right"><?=$po_country_qnty_array[$det['job_no']][$key][$item_key][$country_key]; ?></td>
                                    <?
                                    foreach($job_size_array[$det['job_no']] as $key_s=>$value_s)
                                    {
                                        if($value_s !="")
                                        {
                                            ?><td align="right"><?=$po_country_size_tot_qnty_array [$det['job_no']][$key][$item_value][$country_value][$value_s]; ?></td><?
                                        }
                                    }
                                    ?>
                                </tr>
                            <?
                            }
                            ?>
                            <tr style="font-weight:bold; font-size:12px">
                                <td colspan="4">Item Total:</td>
                                <td align="right"><?=$po_item_qnty_array[$det['job_no']][$key][$item_key]; ?></td>
                                <?
                                foreach($size_array[$det['job_no']] as $key_s=>$value_s)
                                {
                                    if($value_s !="")
                                    {
                                        ?><td align="right"><?=$po_item_size_tot_qnty_array [$det['job_no']][$key][$item_value][$value_s]; ?></td><?
                                    }
                                }
                                ?>
                            </tr>
                            <?
                        }
                        if($presantation_type==1) $colspn=8; else $colspn=6;
                        ?>
                        <tr style="font-weight:bold; font-size:12px">
                            <td colspan="<?=$colspn; ?>">Po Total:</td>
                            <td align="right"><?=$po_qnty_color_size_table_array [$det['job_no']][$key]; ?></td>
                            <?
                            foreach($size_array[$det['job_no']] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
                                    ?><td align="right"><?=$po_size_tot_qnty_array [$det['job_no']][$key][$value_s]; ?></td><?
                                }
                            }
                            ?>
                        </tr>
                        <?
                    }
                    ?>
                    <tr style="font-weight:bold; font-size:12px">
                        <td colspan="<?=$colspn; ?>">Grand Total:</td>
                        <td align="right"><?=$job_qnty_color_size_table_array [$det['job_no']]; ?></td>
                        <?
                        foreach($size_array[$det['job_no']] as $key_s=>$value_s)
                        {
                            if($value_s !="")
                            {
                                ?><td align="right"><?=$job_size_tot_qnty_array [$det['job_no']][$value_s]; ?></td><?
                            }
                        }
                        ?>
                    </tr>
					
					<?php
					// $style_color_size_arr[$row[csf('style_ref_no')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['color_size_qnty']
					$no_color=array();
					foreach($style_color_size_arr[$det['style_ref_no']] as $color_id=>$size_data){
						$no_color[$det['style_ref_no']]+=1;
						foreach($size_data as $size_id=>$val){
						$color_sum[$det['style_ref_no']][$color_id]+=$val['color_size_qnty'];
						}
						
					}

					// print_r($no_color);
					//================================issue==19934>26-09-2021=================
					$style="";
					foreach($style_color_size_arr[$det['style_ref_no']] as $color_id=>$size_data)
					{
						?>
							<tr style="font-weight:bold; font-size:12px">
								<?php
								if($style!=$det['style_ref_no']){?>
								<td align="center" rowspan="<?=$no_color[$det['style_ref_no']];?>"><?=$det['style_ref_no'];?></td>
								<td align="center" colspan="<?=$colspn-2; ?>" rowspan="<?=$no_color[$det['style_ref_no']];?>">Color Size Wise Summery</td>
								<?}?>
								<td align="right"><?=$colorArr[$color_id]; ?></td>
								<td align="right"><?=$color_sum[$det['style_ref_no']][$color_id]; ?></td>
								<?
								foreach($size_array[$det['job_no']] as $size_id=>$val){?>
								<td align="right"><?=$size_data[$val]['color_size_qnty']; ?></td><?
									}?>
						</tr>
							<? $style=$det['style_ref_no'];
			   		 }?>
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

if ($action=="report_generate_show4")
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
	$cbo_season_year=str_replace("'","",$cbo_season_year);
	$cbo_order_status=str_replace("'","",$cbo_order_status);

	if ($cbo_company==0) $company_id=""; else $company_id=" and a.company_name=$cbo_company";
	if ($cbo_buyer==0) $buyer_id=""; else $buyer_id=" and a.buyer_name=$cbo_buyer";
	if ($cbo_brand_id==0) $brand_id_cond=""; else $brand_id_cond=" and a.brand_id=$cbo_brand_id";
	if ($cbo_season_id==0) $season_id_cond=""; else $season_id_cond=" and a.season_buyer_wise=$cbo_season_id";
	if ($cbo_season_year==0) $seasonYearCond=""; else $seasonYearCond=" and a.season_year=$cbo_season_year";
	if ($cbo_order_status==0) $orderStatusCond=""; else $orderStatusCond=" and c.is_confirmed=$cbo_order_status";
	
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
			where  a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 $company_id $buyer_id".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_id $job_num_mst $po_id $pub_shipment_date2 $brand_id_cond $season_id_cond $year_cond $job_num_mst $style_ref_cond $ref_cond $file_cond $seasonYearCond $orderStatusCond group by a.job_no,c.id, b.smv_pcs,b.gmts_item_id,a.currency_id ";
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
	$job_no_array=array(); $po_details_array=array(); $job_size_array=array(); $po_item_array=array(); $po_country_array=array(); $po_country_ship_date_array=array(); $po_color_array=array(); $job_qnty_color_size_table_array=array(); $job_size_tot_qnty_array=array();

	$po_color_size_qnty_array=array(); $po_color_qnty_array=array(); $po_qnty_array=array(); $po_qnty_color_size_table_array=array(); $po_size_tot_qnty_array=array(); $po_item_qnty_array=array(); $po_item_size_tot_qnty_array=array(); $po_country_qnty_array=array(); $po_country_size_tot_qnty_array=array(); $po_ship_date_array=array(); $po_file_no_array=array(); $po_ref_no_array=array();

	ob_start();
	if($txt_order_type==1)
	{
		$sql="SELECT a.id as job_id, a.currency_id, a.job_no, a.company_name, a.buyer_name,a.brand_id,a.season_buyer_wise as season, a.style_ref_no, a.product_dept, a.location_name, a.dealing_marchant, (a.job_quantity*a.total_set_qnty) as job_quantity, a.order_uom, a.total_set_qnty, a.order_repeat_no, d.item_number_id as item_number_id, c.id as po_id, c.file_no, c.grouping, c.po_number,c.pack_handover_date, c.pub_shipment_date, c.shipment_date, c.po_received_date, (c.po_quantity*a.total_set_qnty) as po_quantity, c.excess_cut, c.plan_cut, c.unit_price, c.po_total_price, d.id, d.country_id, d.country_ship_date, d.cutup_date, d.size_number_id, d.color_number_id, d.order_quantity, d.order_rate, d.order_total, d.excess_cut_perc, d.plan_cut_qnty, d.size_order,d.article_number

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
		$company_id $buyer_id".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_id $po_id $brand_id_cond $season_id_cond $pub_shipment_date $year_cond $job_num_mst $style_ref_cond $ref_cond $file_cond $seasonYearCond $orderStatusCond order by d.size_order, a.job_no, c.id, d.country_ship_date, d.color_order";
	}
	else
	{
		$sql="SELECT a.id as job_id, a.currency_id, a.job_no, a.company_name, a.buyer_name,a.brand_id,a.season_buyer_wise as season, a.style_ref_no, a.product_dept, a.location_name, a.dealing_marchant, (a.job_quantity*a.total_set_qnty) as job_quantity, a.order_uom, a.total_set_qnty, a.order_repeat_no, d.item_number_id as item_number_id, c.id as po_id,c.file_no, c.grouping, c.po_number,c.pack_handover_date, c.pub_shipment_date, c.shipment_date, c.po_received_date, (c.po_quantity*a.total_set_qnty) as po_quantity, c.excess_cut, c.plan_cut, c.unit_price, c.po_total_price, d.id,d.country_id, d.country_ship_date, d.cutup_date, d.size_number_id, d.color_number_id, d.order_quantity, d.order_rate, d.order_total, d.excess_cut_perc, d.plan_cut_qnty, d.size_order,d.article_number

		FROM wo_po_details_master a,wo_po_break_down c
		LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst 
		 
		AND c.id = d.po_break_down_id
		AND d.is_deleted =0
		AND d.status_active =1
		AND c.is_deleted =0
		AND c.status_active =1
		WHERE a.job_no = c.job_no_mst 	AND c.is_deleted =0 AND c.status_active =1 and
		a.is_deleted =0 AND a.status_active =1
		$company_id $buyer_id".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_id $po_id $pub_shipment_date $brand_id_cond $season_id_cond $year_cond $job_num_mst $style_ref_cond $ref_cond $file_cond $seasonYearCond $orderStatusCond order by d.size_order, a.job_no, d.color_order, c.id, d.country_ship_date";
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
		$po_phd_date_array[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('pack_handover_date')];
		$po_country_ship_date_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]=$row[csf('country_ship_date')];
		$po_color_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]] [$row[csf('color_number_id')]]=$row[csf('color_number_id')];

		$style_color_size_arr[$row[csf('style_ref_no')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['color_size_qnty']+=$row[csf('order_quantity')];;

		$job_qnty_color_size_table_array[$row[csf('job_no')]]+=$row[csf('order_quantity')];
		$job_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];

		$po_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('po_quantity')];
		$po_qnty_price_array[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('unit_price')];

		$po_qnty_color_size_table_array[$row[csf('job_no')]][$row[csf('po_id')]]+=$row[csf('order_quantity')];
		$po_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];

		$po_item_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]]+=$row[csf('order_quantity')];
		$po_item_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];

		$po_country_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]+=$row[csf('order_quantity')];
		$po_article_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('color_number_id')]]['article_number']=$row[csf('article_number')];
		$po_country_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];

		$po_color_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]] [$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
		$po_color_size_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]] [$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
		//$tmp_job[$row[csf('job_no')]]=$row[csf('job_no')];
		//$size_array[$row[csf("job_no")]][$row[csf("size_order")]]=$row[csf("size_number_id")];

		//=============================part-2===================================
		// $size_array[$row[csf("job_no")]][$row[csf("size_order")]]=$row[csf("size_number_id")];
		$size_array[$row[csf("size_order")]]=$row[csf("size_number_id")];

		 $po_color_wise_arr[$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color_number_id")]]=$row[csf("color_number_id")];
		 $job_wise_data_arr[$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color_number_id")]]['style_ref_no']=$row[csf("style_ref_no")];
		 $job_wise_data_arr[$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color_number_id")]]['country_id']=$row[csf("country_id")];

		 $job_arr[$row[csf("job_no")]]=$row[csf("job_no")];
		 $po_color_size_qty[$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("color_number_id")]][$row[csf('size_number_id')]]['qty']+=$row[csf('order_quantity')];
	}
	// echo "<pre>";
	// print_r($style_color_size_arr);

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


	//===============================fabrication query===================================
	$fab_sql="select  a.id,a.company_id,a.fabrication,a.style_ref_id,a.set_break_down,a.style_refernce,b.job_no 
	from wo_quotation_inquery a ,wo_po_details_master b	where a.style_refernce=b.style_ref_no ".where_con_using_array($job_arr,1,'b.job_no')." order by a.id";
	$fab_data=sql_select($fab_sql);
	foreach($fab_data as $val){

		$job_fab_arr[$val[csf("job_no")]]['fab_desc']=$val[csf("fabrication")];
	}


		foreach($size_array as $val){

			$count_row+=1;
		}?>
  <div style="overflow-y:scroll; max-height:330px;"  width="<?=$count_row*70+760;?>px" id="buyer_list_view" align="center">			
	<fieldset style="width:650px; margin-top:10px" align="center">
			<h4>COLOUR & SIZE WISE QUANTITY  BRCAKDOWN (CODE: MMD/M&M/DMF-09)</h4>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table" >
                <thead>
                	<td width="200"><b>AUKO-TEXGROUP</b></td>
                    <td width="100"><b>Company:</b></td>
                    <td width="150"><?=$companyArr[$cbo_company];?></td>
                    <td width="100"><b>Buyer:</b></td>  
					<td width="100"><?=$buyerArr[$cbo_buyer];?></td>                  
                </thead>
            </table>
	</fieldset>
  <table id="scroll_body" align="center" width="<?=$count_row*70+760;?>px" style="height:auto; margin:0 auto; padding:0;">

	    <table width="<?=$count_row*70+750;?>px" align="center" border="1" rules="all" class="rpt_table" id="color_size">


				<thead>
					<tr>
						<th rowspan="2" width="100">Job #</th>
						<th rowspan="2" width="100">PO #</th>
						<th rowspan="2" width="100">Style Name </th>
						<th rowspan="2" width="100">Color</th>
						<th rowspan="2" width="150">Fabric Description</th>
						<th rowspan="2" width="100">Destonation</th>
						<th colspan="<?=$count_row;?>" width="70" align="center">Size</th>
						<th rowspan="2" width="100">Total</th>
					</tr>
					<tr><? foreach($size_array as $val){?><th width="70"><?=$itemSizeArr[$val];?></th><?}?></tr>
		   		</thead>
	   	</table>
		<table width="<?=$count_row*70+750;?>px" align="center" border="1" rules="all" class="rpt_table">
		<tbody>
			<? 
			foreach($job_wise_data_arr as $jobid=>$po_data){
				foreach($po_data as $poid=>$color_data){
					
					foreach($color_data as $colorid=>$row){
						$job_wise_row[$jobid]+=1;
						$po_wise_row[$poid]+=1;

					}}}
					
					$job_id="";
			foreach($job_wise_data_arr as $jobid=>$po_data){
				foreach($po_data as $poid=>$color_data){
					foreach($color_data as $colorid=>$row){
				?>

					<tr>
						<?
						if($job_id!==$jobid){?>
						<td width="100" rowspan="<?=$job_wise_row[$jobid];?>"><?=$jobid;?></td>	
						<?$job_id=$jobid;}?>
						<?
						if($po_id!==$poid){?>
						<td width="100" rowspan="<?=$po_wise_row[$poid];?>"><?=$poid;?></td>	
						<?$po_id=$poid;}?>
					
						<td width="100"><?=$row['style_ref_no'];?></td>
						<td width="100"><?=$colorArr[$colorid];?></td>
						<td width="150"><?=$job_fab_arr[$jobid]['fab_desc'];?></td>
						<td width="100"><?=$countryArr[$row['country_id']];?></td>
						<?
							foreach($size_array as $size){
								$job_size_sum_qty[$jobid][$size]+=$po_color_size_qty[$jobid][$poid][$colorid][$size]['qty'];
								$po_color_sum_qty[$colorid]+=$po_color_size_qty[$jobid][$poid][$colorid][$size]['qty'];
								
							?>	
						<td width="70"><?=$po_color_size_qty[$jobid][$poid][$colorid][$size]['qty'];?></td>
						<?}?>
						<td width="100"><?=$po_color_sum_qty[$colorid];?></td>
					</tr>
				<?
				}				
			    }
				?>
				<tr>
					
					<td width="100" colspan="6" align="right"><b>Total</b></td>						
					<?	foreach($size_array as $tsize){
								$job_sum_qty[$jobid]+=$job_size_sum_qty[$jobid][$tsize];?>
					<td width="70"><?=$job_size_sum_qty[$jobid][$tsize];?></td>
					<?}?>
					<td width="100"><?=$job_sum_qty[$jobid];?></td>
					</tr>
				<?
			}?>
				</tbody>
		</table>

	</table>
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
	echo "$total_data####$filename";
	exit();
}

if ($action=="report_generate_show2")
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
	$cbo_season_year=str_replace("'","",$cbo_season_year);
	$cbo_order_status=str_replace("'","",$cbo_order_status);

	if ($cbo_company==0) $company_id=""; else $company_id=" and a.company_name=$cbo_company";
	if ($cbo_buyer==0) $buyer_id=""; else $buyer_id=" and a.buyer_name=$cbo_buyer";
	if ($cbo_brand_id==0) $brand_id_cond=""; else $brand_id_cond=" and a.brand_id=$cbo_brand_id";
	if ($cbo_season_id==0) $season_id_cond=""; else $season_id_cond=" and a.season_buyer_wise=$cbo_season_id";
	if ($cbo_season_year==0) $seasonYearCond=""; else $seasonYearCond=" and a.season_year=$cbo_season_year";
	if ($cbo_order_status==0) $orderStatusCond=""; else $orderStatusCond=" and c.is_confirmed=$cbo_order_status";
	
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
			where  a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0   $company_id $buyer_id".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_id $job_num_mst $po_id $pub_shipment_date2 $brand_id_cond $season_id_cond $year_cond $job_num_mst $style_ref_cond $ref_cond $file_cond $seasonYearCond $orderStatusCond group by a.job_no, c.id, b.smv_pcs, b.gmts_item_id, a.currency_id ";
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
	$company_id $buyer_id".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_id $po_id $brand_id_cond $season_id_cond $pub_shipment_date $year_cond $job_num_mst $style_ref_cond $ref_cond $file_cond $seasonYearCond $orderStatusCond order by d.size_order, a.job_no, c.id, d.country_ship_date, d.color_order";
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
	$company_id $buyer_id".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_id $po_id $pub_shipment_date $brand_id_cond $season_id_cond $year_cond $job_num_mst $style_ref_cond $ref_cond $file_cond $seasonYearCond $orderStatusCond order by d.size_order, a.job_no, d.color_order, c.id, d.country_ship_date";
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
	$po_id_arr=array();
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
					array_push($po_id_arr, $po_id);
				}
				$po_rowspan++;
				$po_rowspan_arr[$po_id]=$po_rowspan;
			}
			//$po_rowspan++;
		}
		//$po_rowspan++;
	}

	$po_id_arr=array_unique($po_id_arr);

	$po_cond=where_con_using_array($po_id_arr,0,"a.id");
	$color_size_sql="select a.file_no,a.job_no_mst,sum(b.order_quantity) order_quantity ,b.size_number_id, b.item_number_id,
       b.color_number_id, a.grouping from wo_po_break_down a,wo_po_color_size_breakdown b 
       where  a.id=b.po_break_down_id   
       	 and a.is_deleted = 0
         and a.status_active=1
         and b.status_active = 1
         and b.is_deleted=0  
         and  a.file_no='$txt_file_no' $po_cond 
         group by a.file_no,a.job_no_mst,b.size_number_id, b.item_number_id,
       b.color_number_id, a.grouping order by b.size_number_id";
	//echo $color_size_sql;
	$result_color=sql_select($color_size_sql);
	$sizes_arr=array();
	$color_size_data=array();
	$job_no_arr=array();
	$job_wise_data=array();
	foreach ($result_color as $row) 
	{
		array_push($sizes_arr, $row[csf('size_number_id')]);
		$color_size_data[$row[csf('job_no_mst')]][$row[csf('grouping')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['qnty']+=$row[csf('order_quantity')];
		
		array_push($job_no_arr, $row[csf('job_no_mst')]);
	}
	$sizes_arr=array_unique($sizes_arr);
	$job_no_arr=array_unique($job_no_arr);
	$job_cond=where_con_using_array($job_no_arr,1,"job_no");
	//echo "select id,style_ref_no from  wo_po_details_master where is_deleted=0 $job_cond";
	$style_data = return_library_array("select job_no,style_ref_no from  wo_po_details_master where is_deleted=0 $job_cond","job_no","style_ref_no");
	$size_wise_total=array();
	$color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	?>
	<br>
	<center>
		<table  align="center" border="1" rules="all" class="rpt_table" style="max-width: 1440px;align-content: center">
			<thead>
				<tr>
					<th width="35">Sl</th>
					<th width="80">File No</th>
					<th width="115">Job NO</th>
					<th width="115">Int ref</th>
					<th width="150">Style Ref</th>
					<th width="110">Gmt Item</th>
					<th width="110">Color</th>
					<? foreach ($sizes_arr as $key => $size_id) { ?>
						
						<th width="50"> <? echo $itemSizeArr[$size_id];?></th>
					<? } ?>
					<th>Total</th>
				</tr>
			</thead>
			<tbody>
				<? 
				$i=1;
				$grand_total=0;
				$row_span=0;
				 foreach ($color_size_data as $job_no => $job_data) 
				 {
				 	foreach ($job_data as $int_re => $ref_data) 
				 	{
				 		foreach ($ref_data as $item_number_id => $item_data) 
				 		{
				 			foreach ($item_data as $color_number_id => $color_data) 
				 			{
				 				$row_span++;
				 			}
				 		}
				 	}
				 }
				 foreach ($color_size_data as $job_no => $job_data) 
				 {
				 	foreach ($job_data as $int_re => $ref_data) 
				 	{
				 		foreach ($ref_data as $item_number_id => $item_data) 
				 		{
				 			foreach ($item_data as $color_number_id => $color_data) 
				 			{
				 				# code...
				 			
							 	$row_total=0;
							 		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							 	?>
							 		<tr bgcolor="<? echo $bgcolor;?>">
							 			<td><?php echo $i; ?></td>
							 			<?php 

							 				if($i==1)
							 				{
							 					?>
							 					<td rowspan="<?=$row_span; ?>" style="vertical-align: middle;text-align: center;"><?=$txt_file_no; ?></td>
							 					<?
							 				}
							 				$color=$color_library[$color_number_id];
							 				$item=$garments_item[$item_number_id];
							 				
							 			 ?>
							 			 <td><p><?php echo $job_no; ?></p></td>
							 			 <td><p><?php echo $int_re; ?></p></td>

							 			 <td><p><?php echo $style_data[$job_no]; ?></p></td>
							 			 <td><p><?php echo $item; ?></p></td>
							 			 <td><p><?php echo $color; ?></p></td>
							 			 <? foreach ($sizes_arr as $key => $size_id) { ?>
											
											<td align="right"> <? echo $color_data[$size_id]['qnty'];$size_wise_total[$size_id]+=$color_data[$size_id]['qnty'];$row_total+=$color_data[$size_id]['qnty'];?></td>
										<? } ?>
							 			 <td><?php echo number_format($row_total); ?></td>
							 		</tr>
							 	<?
							 	$grand_total+=$row_total;
							 	$i++;
							}
						}
				 	}
				 }	
				?>
				<tr>
					<td colspan="7" align="right">Total</td>
					<? foreach ($sizes_arr as $key => $size_id) { ?>
								
						<td align="right"> <strong><? echo $size_wise_total[$size_id];?></strong></td>
					<? } ?>
					<td><?php echo number_format($grand_total); ?></td>
				</tr>
			</tbody>
			<tfoot>
				
			</tfoot>
		</table>
	</center>

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
                <td width="60" align="right">Job No: </td><td width="90" onClick="openmypage_job_color_size('requires/size_and_color_break_report_controller.php?action=job_color_size&job_no=<? echo $det["job_no"] ?>','Job Color Size')"><a href="##"><? echo $det['job_no']; ?></a></td>
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
	$cbo_season_year=str_replace("'","",$cbo_season_year);
	$cbo_order_status=str_replace("'","",$cbo_order_status);
	
	if ($cbo_brand_id==0) $brand_id_cond=""; else $brand_id_cond=" and a.brand_id=$cbo_brand_id";
	if ($cbo_season_id==0) $season_id_cond=""; else $season_id_cond=" and a.season_buyer_wise=$cbo_season_id";
	if ($cbo_season_year==0) $seasonYearCond=""; else $seasonYearCond=" and a.season_year=$cbo_season_year";
	if ($cbo_order_status==0) $orderStatusCond=""; else $orderStatusCond=" and c.is_confirmed=$cbo_order_status";


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
			where  a.job_no=e.job_no and a.job_no = d.job_no_mst and e.job_no = d.job_no_mst  and a.status_active=1 and a.is_deleted=0 $company_id $buyer_id".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $po_id $pub_shipment_date $brand_id_cond $season_id_cond $job_num_mst $year_cond $file_cond $style_ref_cond $ref_cond $seasonYearCond $orderStatusCond group by a.job_no,c.id, e.smv_pcs,e.gmts_item_id,d.cutup_date  ";

	$sql_data_set = sql_select($sql_set);
	foreach( $sql_data_set as $row)
	{
		$smv_no_array[$row[csf('job_no')]][$row[csf('cutup_date')]][$row[csf('po_id')]][$row[csf('gmts_item_id')]]['smv_pcs']=$row[csf('smv_pcs')];
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
	$sql="SELECT a.id as job_id, a.currency_id, a.job_no, a.company_name, a.buyer_name, a.brand_id,a.season_buyer_wise as season, a.style_ref_no, a.product_dept, a.location_name, a.dealing_marchant, (a.job_quantity*a.total_set_qnty) as job_quantity, a.order_uom, a.total_set_qnty, a.order_repeat_no, d.item_number_id as item_number_id, c.id as po_id, c.file_no, c.grouping, c.po_number, c.pub_shipment_date,c.pack_handover_date, c.shipment_date, c.po_received_date, (c.po_quantity*a.total_set_qnty) as po_quantity, c.excess_cut, c.plan_cut, c.unit_price, c.po_total_price, d.id, d.cutup, d.country_id, d.country_ship_date, d.cutup_date, d.size_number_id, d.color_number_id, d.order_quantity, d.order_rate, d.order_total, d.excess_cut_perc, d.plan_cut_qnty, d.size_order

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
	$company_id $buyer_id".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_id $po_id $pub_shipment_date $brand_id_cond $season_id_cond $year_cond $job_num_mst $style_ref_cond $ref_cond $file_cond $seasonYearCond $orderStatusCond order by a.job_no, d.cutup_date, d.size_order, c.id, d.country_ship_date, d.color_order";
	}
	else
	{
		 $sql="SELECT distinct a.id as job_id, a.job_no, a.company_name, a.buyer_name, a.brand_id,a.season_buyer_wise as season, a.style_ref_no, a.product_dept, a.location_name, a.dealing_marchant, (a.job_quantity*a.total_set_qnty) as job_quantity , a.order_uom, a.total_set_qnty,d.item_number_id as item_number_id, c.id as po_id,c.file_no,c.grouping, c.po_number, c.pub_shipment_date,c.pack_handover_date, c.shipment_date, c.po_received_date, (c.po_quantity*a.total_set_qnty) as po_quantity, c.excess_cut, c.plan_cut, c.unit_price, c.po_total_price,d.id, d.cutup, d.country_id,d.country_ship_date,d.cutup_date, d.size_number_id,d.size_order, d.color_number_id, d.order_quantity, d.order_rate, d.order_total, d.excess_cut_perc, d.plan_cut_qnty
	FROM wo_po_details_master a,wo_po_break_down c
	LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
	AND c.id = d.po_break_down_id
	AND d.is_deleted =0
	AND d.status_active =1
	WHERE  a.job_no=c.job_no_mst  and c.is_deleted=0 and c.status_active=1 and
	d.id!=0 and a.is_deleted=0 AND a.status_active =1
	$company_id $buyer_id $job_id $po_id $pub_shipment_date $job_num_mst $year_cond $file_cond $brand_id_cond $season_id_cond $style_ref_cond $ref_cond $seasonYearCond $orderStatusCond order by a.job_no, d.cutup_date, d.size_order, c.id, d.cutup, d.color_order, d.id ";
	}
	//echo $sql;die;

	$sql_data = sql_select($sql);
	foreach( $sql_data as $row)
	{
		$job_no_array[$row[csf('job_no')]]=array("job_no"=>$row[csf('job_no')],"job_quantity"=>$row[csf('job_quantity')],"company_name"=>$row[csf('company_name')],"buyer_name"=>$row[csf('buyer_name')],"style_ref_no"=>$row[csf('style_ref_no')],"product_dept"=>$row[csf('product_dept')],"dealing_marchant"=>$row[csf('dealing_marchant')],"job_id"=>$row[csf('job_id')],"order_uom"=>$row[csf('order_uom')],"brand_id"=>$row[csf('brand_id')],"season"=>$row[csf('season')]);
		$po_details_array[$row[csf('job_no')]][$row[csf('cutup_date')]][$row[csf('po_id')]]=$row[csf('po_number')];
		$po_file_no_array[$row[csf('job_no')]][$row[csf('po_id')]]['file']=$row[csf('file_no')];
		$po_ref_no_array[$row[csf('job_no')]][$row[csf('po_id')]]['ref']=$row[csf('grouping')];
		$po_ref_no_array[$row[csf('job_no')]][$row[csf('po_id')]]['phd']=$row[csf('pack_handover_date')];
		$po_cutup_date_array[$row[csf('job_no')]][$row[csf('cutup_date')]][$row[csf('po_id')]]=$row[csf('cutup_date')];
		$job_size_array[$row[csf('job_no')]][$row[csf('size_order')]]=$row[csf('size_number_id')];
		$po_item_array[$row[csf('job_no')]][$row[csf('cutup_date')]][$row[csf('po_id')]][$row[csf('item_number_id')]]=$row[csf('item_number_id')];
		$po_country_array[$row[csf('job_no')]][$row[csf('cutup_date')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('cutup')]][$row[csf('country_id')]]=$row[csf('country_id')];
		//$po_ship_date_array[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('cutup_date')];
		$po_country_ship_date_array[$row[csf('job_no')]][$row[csf('cutup_date')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('cutup')]][$row[csf('country_id')]]=$row[csf('country_ship_date')];
		$po_color_array[$row[csf('job_no')]][$row[csf('cutup_date')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('cutup')]][$row[csf('country_id')]] [$row[csf('color_number_id')]]=$row[csf('color_number_id')];

		$job_qnty_color_size_table_array[$row[csf('job_no')]]+=$row[csf('order_quantity')];
		$job_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
		$po_qnty_array[$row[csf('job_no')]][$row[csf('cutup_date')]][$row[csf('po_id')]]+=$row[csf('order_quantity')];
		$po_price_array[$row[csf('job_no')]][$row[csf('cutup_date')]][$row[csf('po_id')]]=$row[csf('unit_price')];
		$po_cut_off_array[$row[csf('job_no')]][$row[csf('cutup_date')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('cutup')]]=$row[csf('cutup')];
		$po_qnty_color_size_table_array[$row[csf('job_no')]][$row[csf('cutup_date')]][$row[csf('po_id')]]+=$row[csf('order_quantity')];
		$po_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('cutup_date')]][$row[csf('po_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];

		$po_item_qnty_array[$row[csf('job_no')]][$row[csf('cutup_date')]][$row[csf('po_id')]][$row[csf('item_number_id')]]+=$row[csf('order_quantity')];
		$po_cut_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('cutup_date')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('cutup')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];

		$po_item_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('cutup_date')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
		$po_cutoff_tot_qty_array[$row[csf('job_no')]][$row[csf('cutup_date')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('cutup')]]+=$row[csf('order_quantity')];
		$po_country_id_array[$row[csf('job_no')]][$row[csf('cutup_date')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('cutup')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$po_country_qnty_array[$row[csf('job_no')]][$row[csf('cutup_date')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('cutup')]][$row[csf('country_id')]]+=$row[csf('order_quantity')];
		$po_country_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('cutup_date')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('cutup')]][$row[csf('country_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];

		$po_color_qnty_array[$row[csf('job_no')]][$row[csf('cutup_date')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('cutup')]][$row[csf('country_id')]] [$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
		$po_color_size_qnty_array[$row[csf('job_no')]][$row[csf('cutup_date')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('cutup')]][$row[csf('country_id')]] [$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('order_quantity')];
	}

	//print_r($po_cutup_date_array);

	$po_rowspan_arr=array();
	$item_rowspan_arr=array();
	$country_rowspan_arr=array();
	$cutoff_rowspan_arr=array();
	foreach($po_color_array as $job)
	{
		foreach($job as $cut_up_id=>$cut_up_value)
	    {
		   foreach($cut_up_value as $po_id=>$po_value)
		   {
			$po_rowspan=0;
			foreach($po_value as $item_id =>$item_value)
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
							$country_rowspan_arr[$cut_up_id][$po_id][$item_id][$cutoff_id][$country_id]=$country_rowspan;
							$country_rowspan++;
						}
						$po_rowspan++;
						$po_rowspan_arr[$cut_up_id][$po_id]=$po_rowspan;
						$item_rowspan++;
						$cutoff_rowspan++;
						$cutoff_rowspan_arr[$cut_up_id][$po_id][$item_id][$cutoff_id]=$cutoff_rowspan;

					}
					$po_rowspan++;
					$po_rowspan_arr[$cut_up_id][$po_id]=$po_rowspan;
					$item_rowspan++;

					$item_rowspan_arr[$cut_up_id][$po_id][$item_id]=$item_rowspan;
				}
				$po_rowspan++;
				$po_rowspan_arr[$cut_up_id][$po_id]=$po_rowspan;
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
                <td width="60" align="right">Job No: </td><td width="90" onClick="openmypage_job_color_size('requires/size_and_color_break_report_controller.php?action=job_color_size_cut&job_no=<? echo $det["job_no"] ?>','Job Color Size')"><a href="##"><? echo $det['job_no']; ?></a></td>
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
           foreach($po_cutup_date_array[$det['job_no']] as $cut_up_key=>$cut_up_value)
           {
	          foreach($po_details_array[$det['job_no']][$cut_up_key] as $key=>$value)
	          {
			   
                $posl=1;
                foreach($po_item_array [$det['job_no']][$cut_up_key][$key] as $item_key=>$item_value)
                {

                    $itemsl=1;
					   foreach($po_cut_off_array [$det['job_no']][$cut_up_key][$key][$item_value] as $cutoff_key=>$cutoff_value)
						{

							$cutoffsl=1;
							foreach($po_country_array [$det['job_no']][$cut_up_key][$key][$item_value][$cutoff_value] as $country_key=>$country_value)
							{
								//echo count($po_country_array [$det[csf('job_no')]][$key][$item_key]);
								$countrysl=1;
								foreach($po_color_array [$det['job_no']][$cut_up_key][$key][$item_value][$cutoff_value][$country_value] as $color_key=>$color_value)
								{
									if($countrysl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
									<tr bgcolor="<? echo $bgcolor;?>" >
										<!--<td valign="middle" rowspan="" ><?  //echo $itemsl;?></td>-->
										<?
										$phd_date=$po_ref_no_array[$det['job_no']][$key]['phd'];
										if($posl==1)
										{
										?>
										<td  align="center" valign="middle" rowspan="<? echo $po_rowspan_arr[$cut_up_key][$key]; ?>" ><?  echo $value; echo "<br/> Cut-off Date:<br/>".change_date_format($cut_up_key,"dd-mm-yyyy","-"); if($cut_up_key!='0000-00-00') echo "<br/>".date('l', strtotime($cut_up_key)).'<br> PHD: '.change_date_format($phd_date); ?></td>
										<?
										}
										if($presantation_type==1)
										{
											if($posl==1)
											{
											?>
											<td  align="center" valign="middle" rowspan="<? echo $po_rowspan_arr[$cut_up_key][$key]; ?>" ><?  echo $po_file_no_array[$det['job_no']][$key]['file']; ?></td>
											<?
											}
											if($posl==1)
											{
											?>
											<td  align="center" valign="middle" rowspan="<? echo $po_rowspan_arr[$cut_up_key][$key]; ?>" ><?  echo  $po_ref_no_array[$det['job_no']][$key]['ref']; ?></td>
											<?
											}
										}
										if($posl==1)
										{
											//po_price_array
											$po_price=$po_price_array[$det['job_no']][$cut_up_key][$key];
										?>
										<td align="center" valign="middle" rowspan="<? echo $po_rowspan_arr[$cut_up_key][$key]; ?>"><? echo  $po_qnty_array[$det['job_no']][$cut_up_key][$key].' (Pcs)'.'<br>'.number_format($po_price,2).'<b> USD</b>'; ?></td>
										<?
										}
										if($itemsl==1)
										{
											$item_smv_pcs=$smv_no_array[$det[('job_no')]][$cut_up_key][$key][$item_value]['smv_pcs'];

										?>
										<td align="center" valign="middle" rowspan="<? echo $item_rowspan_arr[$cut_up_key][$key][$item_key]; ?>" title="<?="SMV=".$item_smv_pcs; ?>" ><?  echo $garments_item[$item_value].'<br/>'.number_format($item_smv_pcs,3);?></td>
										<?
										}
										if($cutoffsl==1)
										{
										?>
										<td align="center" valign="middle" rowspan="<?  echo $cutoff_rowspan_arr[$cut_up_key][$key][$item_key][$cutoff_key]; ?>" ><?  echo $cut_up_array[$po_cut_off_array[$det['job_no']][$cut_up_key][$key][$item_value][$cutoff_value]] ;?></td>
										<?
										}
										if($countrysl==1)
										{
										?>
										<td align="center" valign="middle" rowspan="<?  echo $country_rowspan_arr[$cut_up_key][$key][$item_key][$cutoff_key][$country_key]; ?>"><?  echo $countryArr[$po_country_id_array[$det['job_no']][$cut_up_key][$key][$item_value][$cutoff_value][$country_value]]."<br/>".change_date_format($po_country_ship_date_array [$det['job_no']][$cut_up_key][$key][$item_value][$cutoff_value][$country_value],"dd-mm-yyyy","-")."<br/>".date('l', strtotime($po_country_ship_date_array[$det['job_no']][$cut_up_key][$key][$item_value][$cutoff_value][$country_value]));  ?></td>
										<?
										}
										?>
										<td><?  echo $colorArr[$color_value] ;?></td>
										<td align="right"><? echo $po_color_qnty_array [$det['job_no']][$cut_up_key][$key][$item_value][$cutoff_value][$country_value][$color_value]; ?></td>
										<?
										foreach($job_size_array[$det['job_no']] as $key_s=>$value_s)
										{
											if($value_s !="")
											{
										?>
										<td align="right"><? echo $po_color_size_qnty_array [$det['job_no']][$cut_up_key][$key][$item_value][$cutoff_value][$country_value][$color_value][$value_s]?></td>
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
						 <td colspan="" align="right"><? echo $po_country_qnty_array[$det['job_no']][$cut_up_key][$key][$item_key][$cutoff_value][$country_key] ?></td>
						 <?
						foreach($job_size_array[$det['job_no']] as $key_s=>$value_s)
						{
						if($value_s !="")
						{
							//$po_country_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]]
						?>
						<td align="right"><? echo $po_country_size_tot_qnty_array [$det['job_no']][$cut_up_key][$key][$item_value][$cutoff_value][$country_value][$value_s]; ?></td>
						<?
						}
						}
						?>
						</tr>
							<?
							}
							?>
						<tr style="font-weight:bold; font-size:12px">
						<td colspan="3"><? echo $cut_up_array[$po_cut_off_array[$det['job_no']][$cut_up_key][$key][$item_value][$cutoff_value]]; ?> Total:</td>
						<td colspan="" align="right"><? echo $po_cutoff_tot_qty_array[$det['job_no']][$cut_up_key][$key][$item_value][$cutoff_value] ?></td>
						<?
						foreach($job_size_array[$det['job_no']] as $key_s=>$value_s)
						{
						if($value_s !="")
						{
							//$po_item_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('size_number_id')]]
						?>
						<td align="right"><? echo $po_cut_size_tot_qnty_array[$det['job_no']][$cut_up_key][$key][$item_value][$cutoff_value][$value_s]?></td>
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
						<td colspan="" align="right"><? echo $po_item_qnty_array[$det['job_no']][$cut_up_key][$key][$item_key] ?></td>
						<?
						foreach($job_size_array[$det['job_no']] as $key_s=>$value_s)
						{
						if($value_s !="")
						{
							//$po_item_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('size_number_id')]]
						?>
						<td align="right"><? echo $po_item_size_tot_qnty_array [$det['job_no']][$cut_up_key][$key][$item_value][$value_s]?></td>
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

                   <td  align="right"><? echo $po_qnty_color_size_table_array [$det['job_no']][$cut_up_key][$key]?></td>

                 <?
				foreach($job_size_array[$det['job_no']] as $key_s=>$value_s)
				{
				if($value_s !="")
				{
					//$po_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('size_number_id')]]
				?>
				<td align="right"><? echo $po_size_tot_qnty_array [$det['job_no']][$cut_up_key][$key][$value_s]?></td>
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

if ($action=="report_generate_summary")//ISD-21-02385
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
	$cbo_season_year=str_replace("'","",$cbo_season_year);
	$cbo_order_status=str_replace("'","",$cbo_order_status);

	if ($cbo_company==0) $company_id=""; else $company_id=" and a.company_name=$cbo_company";
	if ($cbo_buyer==0) $buyer_id=""; else $buyer_id=" and a.buyer_name=$cbo_buyer";
	if ($cbo_brand_id==0) $brand_id_cond=""; else $brand_id_cond=" and a.brand_id=$cbo_brand_id";
	if ($cbo_season_id==0) $season_id_cond=""; else $season_id_cond=" and a.season_buyer_wise=$cbo_season_id";
	if ($cbo_season_year==0) $seasonYearCond=""; else $seasonYearCond=" and a.season_year=$cbo_season_year";
	if ($cbo_order_status==0) $orderStatusCond=""; else $orderStatusCond=" and c.is_confirmed=$cbo_order_status";
	
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
	$countryCodeArr = return_library_array("select id,ultimate_country_code from lib_country_loc_mapping ","id","ultimate_country_code");

	$sql_coun_data = sql_select("select id,country_name,short_name from lib_country");
	foreach($sql_coun_data as $row){
		if($row[csf('short_name')]) $shortName='-'.$row[csf('short_name')];else $shortName='';
		$countryArr[$row[csf('id')]]=$row[csf('country_name')].$shortName;
	}
	unset($sql_coun_data);
	//$countryArr = return_library_array("select id,country_name from lib_country ","id","country_name");
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
	  $sql_set= "select a.job_no,a.currency_id, b.smv_pcs,c.id as po_id,b.gmts_item_id from  wo_po_details_mas_set_details b,wo_po_details_master a
			LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
			AND c.is_deleted =0 AND c.status_active =1
			where  a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0   $company_id $buyer_id".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_id $job_num_mst $po_id $pub_shipment_date2 $brand_id_cond $season_id_cond $year_cond $job_num_mst $style_ref_cond $ref_cond $file_cond $seasonYearCond $orderStatusCond group by a.job_no,c.id, b.smv_pcs,b.gmts_item_id,a.currency_id ";
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

	ob_start();
	if($txt_order_type==1)
	{
		$sql="SELECT a.id as job_id, a.currency_id, a.job_no, a.company_name, a.buyer_name,a.brand_id,a.season_buyer_wise as season, a.style_ref_no, a.product_dept, a.location_name, a.dealing_marchant, (a.job_quantity*a.total_set_qnty) as job_quantity, a.order_uom, a.total_set_qnty, a.order_repeat_no, a.ship_mode, d.item_number_id as item_number_id, c.id as po_id, c.file_no, c.grouping, c.po_number, c.pub_shipment_date, c.shipment_date, c.po_received_date, (c.po_quantity*a.total_set_qnty) as po_quantity, c.excess_cut, c.plan_cut, c.unit_price, c.po_total_price, d.id, d.country_id, d.ul_country_code, d.country_ship_date, d.cutup_date, d.size_number_id, d.color_number_id, d.order_quantity, d.order_rate, d.order_total, d.excess_cut_perc, d.plan_cut_qnty, d.size_order

	FROM wo_po_details_master a
	LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
	AND c.is_deleted =0 AND c.status_active =1
	LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
	AND c.id = d.po_break_down_id AND d.is_deleted =0 AND d.status_active =1
	WHERE
	a.is_deleted =0 AND a.status_active =1
	$company_id $buyer_id".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_id $po_id $brand_id_cond $season_id_cond $pub_shipment_date $year_cond $job_num_mst $style_ref_cond $ref_cond $file_cond $seasonYearCond $orderStatusCond order by d.size_order, a.job_no, c.id, d.country_ship_date, d.color_order";
	}
	else
	{
		$sql="SELECT a.id as job_id, a.currency_id, a.job_no, a.company_name, a.buyer_name,a.brand_id,a.season_buyer_wise as season, a.style_ref_no, a.product_dept, a.location_name, a.dealing_marchant, (a.job_quantity*a.total_set_qnty) as job_quantity, a.order_uom, a.total_set_qnty, a.order_repeat_no, a.ship_mode, d.item_number_id as item_number_id, c.id as po_id, c.file_no, c.grouping, c.po_number, c.pub_shipment_date, c.shipment_date, c.po_received_date, (c.po_quantity*a.total_set_qnty) as po_quantity, c.excess_cut, c.plan_cut, c.unit_price, c.po_total_price, d.id, d.country_id, d.ul_country_code, d.country_ship_date, d.cutup_date, d.size_number_id, d.color_number_id, d.order_quantity, d.order_rate, d.order_total, d.excess_cut_perc, d.plan_cut_qnty, d.size_order

	FROM wo_po_details_master a, wo_po_break_down c
	LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst 
	AND c.id = d.po_break_down_id AND d.is_deleted =0 AND d.status_active =1 AND c.is_deleted =0 AND c.status_active =1
	WHERE a.job_no = c.job_no_mst 	AND c.is_deleted =0 AND c.status_active =1 and a.is_deleted =0 AND a.status_active =1
	$company_id $buyer_id".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_id $po_id $pub_shipment_date $brand_id_cond $season_id_cond $year_cond $job_num_mst $style_ref_cond $ref_cond $file_cond $seasonYearCond $orderStatusCond order by d.size_order, a.job_no, d.color_order, c.id, d.country_ship_date";
	}

	//echo $sql;
	$po_details_array=array(); $poNoArr=array(); $sizeArr=array(); $countryCodeColorArr=array(); $countryCodeColorArr=array(); $countryCodeColorSizeArr=array();
	
	$sql_data = sql_select($sql);
	foreach( $sql_data as $row)
	{
		if($row[csf('ul_country_code')]=="") $row[csf('ul_country_code')]=0;
		$job_no_array[$row[csf('job_no')]]=array("job_no"=>$row[csf('job_no')],"job_quantity"=>$row[csf('job_quantity')],"company_name"=>$row[csf('company_name')],"buyer_name"=>$row[csf('buyer_name')],"style_ref_no"=>$row[csf('style_ref_no')],"product_dept"=>$row[csf('product_dept')],"dealing_marchant"=>$row[csf('dealing_marchant')],"job_id"=>$row[csf('job_id')],"order_uom"=>$row[csf('order_uom')],"brand_id"=>$row[csf('brand_id')],"season"=>$row[csf('season')],"ship_mode"=>$row[csf('ship_mode')]);
		
		$poNoArr[$row[csf('job_no')]].=$row[csf('po_number')].'__';
		$sizeArr[$row[csf("job_no")]][$row[csf('size_order')]]=$row[csf('size_number_id')];
		$countryCodeColorArr[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('country_ship_date')]][$row[csf('country_id')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
		$countryCodeColorSizeArr[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('country_ship_date')]][$row[csf('country_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
		
		
		/*$po_file_no_array[$row[csf('job_no')]][$row[csf('po_id')]]['file']=$row[csf('file_no')];
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

		$size_array[$row[csf("job_no")]][$row[csf("size_order")]]=$row[csf("size_number_id")];*/
	}
	//print_r($size_array);

	$country_rowspan_arr=array();
	$countryDate_rowspan_arr=array();
	foreach($countryCodeColorArr as $job=>$jobdata)
	{
		foreach($jobdata as $po_id=>$podata)
		{
			foreach($podata as $countrydate =>$countrydatedata)
			{
				$countryDate_rowspan=0; $cdateQty=0;
				foreach($countrydatedata as $country_id =>$countryData)
				{
					$country_rowspan=0;
					foreach($countryData as $color_id =>$colorData)
					{
						//echo " $color_id==";
						$country_rowspan++;
						$countryDate_rowspan++;
						$cdateQty+=$colorData;
					}
					$country_rowspan_arr[$po_id][$countrydate][$country_id]=$country_rowspan;
				}
				$countryDate_rowspan_arr[$po_id][$countrydate]['span']=$countryDate_rowspan;
				$countryDate_rowspan_arr[$po_id][$countrydate]['qty']=$cdateQty;
			}
		}
	}
	//print_r($countryCode_rowspan_arr);
	?>
    <table id="scroll_body" align="center" style="height:auto; width:100%; margin:0 auto; padding:0;">
        <tr>
            <td width="100%">
            <? $q=1;
            foreach($job_no_array as $rdata=>$det)
            {
                //ksort($size_array[$det["job_no"]]);
				$sizeCountWidth=count($sizeArr[$det['job_no']])*60;
                ?>
                <table width="1080px" align="center" border="1" rules="all" class="rpt_table" id="table_header_1">
                	<? if($q==1) { ?>
                    <tr class="form_caption" style="border:none;">
                        <td colspan="6" align="center" style="border:none;font-size:18px; font-weight:bold"><?=$report_title.' [Summary]'; ?></td>
                    </tr>
                    <tr class="form_caption" style="border:none;">
                        <td colspan="6" align="center" style="border:none;font-size:16px; font-weight:bold"><?=$companyArr[$cbo_company]; ?></td>
                    </tr>
                    <? } $q++; ?>
                    <tr class="form_caption" bgcolor="#dddddd">
                        <td width="200" style="word-break:break-all"><b>Buyer: <?=$buyerArr[$det['buyer_name']]; ?></b></td>
                        <td width="190" style="word-break:break-all"><b>Style Ref.: <?=$det['style_ref_no']; ?></b></td>
                        <td width="130"><b>Job No: <a href="openmypage_job_color_size('requires/size_and_color_break_report_controller.php?action=job_color_size&job_no=<?=$det["job_no"]; ?>','Job Color Size');"><?=$det['job_no']; ?></a></b></td>
                        <td width="280" style="word-break:break-all"><b>Order No: <?=implode(",",array_filter(array_unique(explode("__",$poNoArr[$det['job_no']])))); ?></b></td>
                        <td width="140"><b>Season: <?=$seasonArr[$det['season']]; ?></b></td>
                        <td><b>Ship Mode: <?=$shipment_mode[$det['ship_mode']]; ?></b></td>
                    </tr>
                </table>
                <br/>
                <table width="<?=530+$sizeCountWidth; ?>px" align="center" border="1" rules="all" class="rpt_table" id="color_size">
                    <thead>
                        <tr>
                            <th width="80">Country Name</th>
                            <th width="60">Country Code</th>
                            <th width="80">Color</th>
                            <th width="70">Color PO Qty.</th>
                            <?
                            foreach($sizeArr[$det['job_no']] as $key=>$value)
                            {
                                if($value!="")
                                {
                                    ?><th width="60"><?=$itemSizeArr[$value]; ?></th><?
                                }
                            }
                            ?>
                            <th width="70">Total Qty.</th>
                            <th width="70">Ship Qty.</th>
                            <th>Ship TOD</th>
                        </tr>
                    </thead>
                    <?
                    foreach($countryCodeColorArr[$det['job_no']] as $pid=>$podata)
                    {
                        foreach($podata as $cdate=>$countrydateData)
                        {
                            $cdatesl=1;
                            foreach($countrydateData as $cid=>$countryData)
                            {
								$countrysl=1;
                                foreach($countryData as $color_id=>$colorQty)
                                {
									if($countrysl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
									<tr bgcolor="<?=$bgcolor;?>">
										<?
										if($countrysl==1)
										{
											$excountry=explode("-",$countryArr[$cid]);
											?><td align="center" valign="middle" rowspan="<?=$country_rowspan_arr[$pid][$cdate][$cid]; ?>" ><?=$excountry[0]; ?></td>
											<td align="center" valign="middle" rowspan="<?=$country_rowspan_arr[$pid][$cdate][$cid]; ?>" ><?=$excountry[1]; ?></td><?
										}
										?>
										<td><?=$colorArr[$color_id]; ?></td>
										<td align="right"><?=$colorQty; ?></td>
										<?
										foreach($sizeArr[$det['job_no']] as $key=>$sizeid)
										{
											if($sizeid!="")
											{
												$sizeQty=0;
												$sizeQty=$countryCodeColorSizeArr[$det['job_no']][$pid][$cdate][$cid][$color_id][$sizeid];
												$sizeQtyArr[$sizeid]+=$sizeQty;
												?><td align="right"><?=$sizeQty; ?></td><?
											}
										}
										?>
										<td align="right"><?=$colorQty; ?></td>
                                        <?
										if($cdatesl==1)
										{
											?>
											<td align="center" valign="middle" rowspan="<?=$countryDate_rowspan_arr[$pid][$cdate]['span']; ?>" title="<?=$cdate; ?>"><?=$countryDate_rowspan_arr[$pid][$cdate]['qty']; ?></td>
                                            <td align="center" valign="middle" rowspan="<?=$countryDate_rowspan_arr[$pid][$cdate]['span']; ?>"><?=change_date_format($cdate).'<br>'.date("l", strtotime($cdate)); ?></td>
                                            <?
										}
										?>
									</tr>
									<?
									$poColorTotal+=$colorQty;
									$totalQty+=$colorQty;
									$countrysl++;
									$cdatesl++;
								}
                            }
                        }
                    }
                    ?>
                    <tr style="font-weight:bold; font-size:12px">
                        <td colspan="3" align="right">Grand Total:</td>
                        <td align="right"><?=$poColorTotal; ?></td>
                        <?
                        foreach($sizeArr[$det['job_no']] as $key=>$sid)
						{
							if($sizeid!="")
							{
								$sizeQty=0;
								$sizeQty=$sizeQtyArr[$sid];
								?><td align="right"><?=$sizeQty; ?></td><?
							}
						}
                        ?>
                        <td align="right"><?=$totalQty; ?></td>
                        <td align="right">&nbsp;</td>
                        <td align="right">&nbsp;</td>
                    </tr>
                </table><br/>
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
	 $sql="SELECT a.id as job_id, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.product_dept, a.location_name, a.dealing_marchant, (a.job_quantity*a.total_set_qnty) as job_quantity , a.order_uom, a.total_set_qnty,d.item_number_id as item_number_id, c.id as po_id, c.po_number, c.pub_shipment_date, c.shipment_date, c.po_received_date, (c.po_quantity*a.total_set_qnty) as po_quantity, c.excess_cut, c.plan_cut, c.unit_price, c.po_total_price,  d.country_id,d.country_ship_date, d.size_number_id, d.color_number_id, d.order_quantity, d.order_rate, d.order_total, d.excess_cut_perc,d.size_order, d.plan_cut_qnty
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
	a.status_active =1 order by d.size_order asc
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
    <p style="text-align:center; margin-bottom:10px;"><input type="button" id="" name="" class="formbutton" style="width:100px" value="Print" onClick="print_window()" /></p>
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
if($action=="job_fabric_dtls")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
	?>
	<script>
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		var selected_id = new Array(); var selected_uom = new Array();
		function js_set_value( str )
		{
			toggle( document.getElementById( 'fabricdata_' + str ), '#FFFFCC' );
			if( jQuery.inArray( $('#fabricid' + str).val(), selected_id ) == -1 ) {
				for( var i = 0; i < selected_uom.length; i++ ) {
					console.log(selected_uom[i]+'=='+$('#fabricuom' + str).val());
					if( selected_uom[i] != $('#fabricuom' + str).val() ){
						alert("Multiple Uom Not Allowed");
						toggle( document.getElementById( 'fabricdata_' + str ), '#FFFFFF' );
						return;
					}
				}
				selected_id.push( $('#fabricid' + str).val() );
				selected_uom.push($('#fabricuom' + str).val());
				
			}
			else{
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#fabricid' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_uom.splice( i,1 );
			}
			var id = ''; var job = ''; var txt_trim_group_id=''; var txt_po_id='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			$('#txt_selected_id').val( id );
		}
	</script>
	<?
    extract($_REQUEST);
	$fabric_array=sql_select("SELECT id, body_part_id, fab_nature_id, color_type_id, lib_yarn_count_deter_id as lib_yarn_count_deter_id, construction, composition, fabric_description, source_id, gsm_weight, color_size_sensitive, color, consumption_basis, avg_cons, fabric_source, rate, amount, avg_finish_cons, avg_process_loss, status_active, cons_breack_down, msmnt_break_down, color_break_down, yarn_breack_down, process_loss_method, marker_break_down, width_dia_type, avg_cons_yarn, gsm_weight_yarn, plan_cut_qty, job_plan_cut_qty, is_apply_last_update, uom, body_part_type from wo_pre_cost_fabric_cost_dtls where job_no='$job_no' and status_active=1 and is_deleted=0 order by seq asc");

	$condition= new condition();
	$condition->job_no("='$job_no'");	
	$condition->init();
	$fabric= new fabric($condition);
	$fabric_qty=$fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();

	?>
	<div id="report_container">
		<input type="hidden" name="txt_selected_id" id="txt_selected_id" value="" />
		<input type="hidden" name="txt_job_no" id="txt_job_no" value="<?= $job_no ?>" />
		<table width="1030px" align="center" border="1" rules="all" class="rpt_table" >
			<thead>
				<th>Body Part</th>
				<th>Color Type</th>
				<th>Fabric Description</th>
				<th>Fabric Source</th>
				<th>Fabric Weight</th>
				<th>F. Weight Type</th>
				<th>Uom</th>
				<th>Avg. Fabric Cons</th>
				<th>Rate</th>
				<th>Total Qty</th>
			</thead>
			<?	
			$i=1;			
			foreach($fabric_array as $row){
				$fabric_description=$body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("fabric_description")].", ".$row[csf("gsm_weight")];
				$fabricqty=0;
				if($row[csf('fab_nature_id')]==3){
					$fabricqty=$fabric_qty['woven']['grey'][$row[csf("id")]][$row[csf("uom")]];
				}else{
					$fabricqty=$fabric_qty['knit']['grey'][$row[csf("id")]][$row[csf("uom")]];
				}
				?>
			<tr id="fabricdata_<?= $i?>" bgcolor="#FFFFFF" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $i;?>)">
				<td><?= $body_part[$row[csf("body_part_id")]] ?>
					<input type="hidden" id="fabricid<?= $i?>" value="<?= $row[csf("id")] ?>" >
					<input type="hidden" id="fabricuom<?= $i?>" value="<?= $row[csf("uom")] ?>" >
				</td>
 				<td><?= $color_type[$row[csf("color_type_id")]] ?></td>
				<td title="<?= $fabric_description ?>"><?= $fabric_description ?></td>
				<td><?= $fabric_source[$row[csf("fabric_source")]] ?></td>
				<td><?= $row[csf("gsm_weight")] ?></td>
				<td><?= $fabric_typee[$row[csf("width_dia_type")]] ?></td>
				<td><?= $unit_of_measurement[$row[csf("uom")]] ?></td>
				<td><?= $row[csf("avg_cons")] ?></td>
				<td><?= $row[csf("rate")] ?></td>
				<td><?= $fabricqty ?></td>
			</tr>
			<? $i++;
			 } ?>
			 <tfoot>
				<tr><td colspan="10">&nbsp;</td></tr>
				<tr>				
					<td colspan="10" align="center"><input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" /></td>
				</tr>
			 </tfoot>
		</table>
	</div>
    <?
	exit();
}
if ($action=="report_generate_show5")
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
	$cbo_season_year=str_replace("'","",$cbo_season_year);
	$cbo_order_status=str_replace("'","",$cbo_order_status);

	if ($cbo_company==0) $company_id=""; else $company_id=" and a.company_name=$cbo_company";
	if ($cbo_buyer==0) $buyer_id=""; else $buyer_id=" and a.buyer_name=$cbo_buyer";
	if ($cbo_brand_id==0) $brand_id_cond=""; else $brand_id_cond=" and a.brand_id=$cbo_brand_id";
	if ($cbo_season_id==0) $season_id_cond=""; else $season_id_cond=" and a.season_buyer_wise=$cbo_season_id";
	if ($cbo_season_year==0) $seasonYearCond=""; else $seasonYearCond=" and a.season_year=$cbo_season_year";
	if ($cbo_order_status==0) $orderStatusCond=""; else $orderStatusCond=" and c.is_confirmed=$cbo_order_status";
	
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
	  $sql_set= "select a.job_no,a.currency_id, b.smv_pcs,c.id as po_id,b.gmts_item_id from wo_po_details_mas_set_details b,wo_po_details_master a
			LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
			AND c.is_deleted =0 AND c.status_active =1
			where  a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0   $company_id $buyer_id".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_id $job_num_mst $po_id $pub_shipment_date2 $brand_id_cond $season_id_cond $year_cond $job_num_mst $style_ref_cond $ref_cond $file_cond $seasonYearCond $orderStatusCond group by a.job_no,c.id, b.smv_pcs,b.gmts_item_id,a.currency_id ";
	//echo  $sql_set; die;

	$sql_data_set = sql_select($sql_set);
	foreach( $sql_data_set as $row)
	{
		if($row[csf('smv_pcs')])
		{
			$smv_no_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('gmts_item_id')]]['smv_pcs']=$row[csf('smv_pcs')];
		}
	}
	//print_r($smv_no_array);
	$job_no_array=array(); $po_details_array=array(); $job_size_array=array(); $po_item_array=array(); $po_country_array=array(); $po_country_ship_date_array=array(); $po_color_array=array(); $job_qnty_color_size_table_array=array(); $job_size_tot_qnty_array=array();

	$po_color_size_qnty_array=array(); $po_color_qnty_array=array(); $po_qnty_array=array(); $po_qnty_color_size_table_array=array(); $po_size_tot_qnty_array=array(); $po_item_qnty_array=array(); $po_item_size_tot_qnty_array=array(); $po_country_qnty_array=array(); $po_country_size_tot_qnty_array=array(); $po_ship_date_array=array(); $po_file_no_array=array(); $po_ref_no_array=array();

	ob_start();
	if($txt_order_type==1)
	{
		$sql="SELECT a.id as job_id, a.currency_id, a.job_no, a.company_name, a.buyer_name,a.brand_id,a.season_buyer_wise as season, a.style_ref_no, a.product_dept, a.location_name, a.dealing_marchant, (a.job_quantity*a.total_set_qnty) as job_quantity, a.order_uom, a.total_set_qnty, a.order_repeat_no, d.item_number_id as item_number_id, a.set_smv, a.gmts_item_id, c.id as po_id, c.file_no, c.grouping, c.po_number,c.pack_handover_date, c.pub_shipment_date, c.shipment_date, c.po_received_date, (c.po_quantity*a.total_set_qnty) as po_quantity, c.excess_cut, c.plan_cut, c.unit_price, c.po_total_price, d.id, d.country_id, d.country_ship_date, d.cutup_date, d.size_number_id, d.color_number_id, d.order_quantity, d.order_rate, d.order_total, d.excess_cut_perc, d.plan_cut_qnty, d.size_order,d.article_number,a.style_owner

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
		$company_id $buyer_id".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_id $po_id $brand_id_cond $season_id_cond $pub_shipment_date $year_cond $job_num_mst $style_ref_cond $ref_cond $file_cond $seasonYearCond $orderStatusCond order by d.size_order, a.job_no, c.id, d.country_ship_date, d.color_order";
	}
	else
	{
		$sql="SELECT a.id as job_id, a.currency_id, a.job_no, a.company_name, a.buyer_name,a.brand_id,a.season_buyer_wise as season, a.style_ref_no, a.product_dept, a.location_name, a.dealing_marchant, (a.job_quantity*a.total_set_qnty) as job_quantity, a.order_uom, a.total_set_qnty, a.order_repeat_no, d.item_number_id as item_number_id, a.set_smv, a.gmts_item_id, c.id as po_id,c.file_no, c.grouping, c.po_number,c.pack_handover_date, c.pub_shipment_date, c.shipment_date, c.po_received_date, (c.po_quantity*a.total_set_qnty) as po_quantity, c.excess_cut, c.plan_cut, c.unit_price, c.po_total_price, d.id,d.country_id, d.country_ship_date, d.cutup_date, d.size_number_id, d.color_number_id, d.order_quantity, d.order_rate, d.order_total, d.excess_cut_perc, d.plan_cut_qnty, d.size_order,d.article_number,a.style_owner

		FROM wo_po_details_master a,wo_po_break_down c
		LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst 
		 
		AND c.id = d.po_break_down_id
		AND d.is_deleted =0
		AND d.status_active =1
		AND c.is_deleted =0
		AND c.status_active =1
		WHERE a.job_no = c.job_no_mst 	AND c.is_deleted =0 AND c.status_active =1 and
		a.is_deleted =0 AND a.status_active =1
		$company_id $buyer_id".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_id $po_id $pub_shipment_date $brand_id_cond $season_id_cond $year_cond $job_num_mst $style_ref_cond $ref_cond $file_cond $seasonYearCond $orderStatusCond order by d.size_order, a.job_no, d.color_order, c.id, d.country_ship_date";
	}

	//echo $sql; die;
	$sql_data = sql_select($sql);
	foreach( $sql_data as $row)
	{
		$job_no_array[$row[csf('job_no')]]=array("job_no"=>$row[csf('job_no')],"job_quantity"=>$row[csf('job_quantity')],"company_name"=>$row[csf('company_name')],"buyer_name"=>$row[csf('buyer_name')],"style_ref_no"=>$row[csf('style_ref_no')],"product_dept"=>$row[csf('product_dept')],"dealing_marchant"=>$row[csf('dealing_marchant')],"job_id"=>$row[csf('job_id')],"order_uom"=>$row[csf('order_uom')],"brand_id"=>$row[csf('brand_id')],"season"=>$row[csf('season')], "set_smv"=>$row[csf('set_smv')], "gmts_item_id"=>$row[csf('gmts_item_id')], "total_set_qnty"=>$row[csf('total_set_qnty')], "style_owner"=>$row[csf('style_owner')]);
		$po_details_array[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('po_number')];
		$po_file_no_array[$row[csf('job_no')]][$row[csf('po_id')]]['file']=$row[csf('file_no')];
		$po_ref_no_array[$row[csf('job_no')]][$row[csf('po_id')]]['ref']=$row[csf('grouping')];
		$job_size_array[$row[csf('job_no')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$po_item_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]]=$row[csf('item_number_id')];
		$po_country_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$po_ship_date_array[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('shipment_date')];
		$po_phd_date_array[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('pack_handover_date')];
		$po_country_ship_date_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]=$row[csf('country_ship_date')];
		$po_color_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]] [$row[csf('color_number_id')]]=$row[csf('color_number_id')];

		$style_color_size_arr[$row[csf('style_ref_no')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['color_size_qnty']+=$row[csf('order_quantity')];
		$style_country_color_size_arr[$row[csf('style_ref_no')]][$row[csf('country_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['country_color_size_qnty']+=$row[csf('order_quantity')];

		$job_qnty_color_size_table_array[$row[csf('job_no')]]+=$row[csf('order_quantity')];
		$job_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];

		$po_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('po_quantity')];
		$po_qnty_price_array[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('unit_price')];

		$po_qnty_color_size_table_array[$row[csf('job_no')]][$row[csf('po_id')]]+=$row[csf('order_quantity')];
		$po_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];

		$po_item_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]]+=$row[csf('order_quantity')];
		$po_item_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];

		$po_country_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]+=$row[csf('order_quantity')];
		$po_article_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('color_number_id')]]['article_number']=$row[csf('article_number')];
		$po_country_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];

		$po_color_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]] [$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
		$po_color_size_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]] [$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
		//$tmp_job[$row[csf('job_no')]]=$row[csf('job_no')];
		//$size_array[$row[csf("job_no")]][$row[csf("size_order")]]=$row[csf("size_number_id")];

		$size_array[$row[csf("job_no")]][$row[csf("size_order")]]=$row[csf("size_number_id")];
		$job_arr[$row[csf("job_no")]]=$row[csf("job_no")];
	}
	// echo "<pre>";
	// print_r($style_color_size_arr);

	/*echo '<pre>';
	print_r($style_country_color_size_arr); die;*/

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
		//===============================fabrication query===================================
		$fab_sql="select  a.id,a.company_id,a.fabrication,a.style_ref_id,a.set_break_down,a.style_refernce,b.job_no 
		from wo_quotation_inquery a ,wo_po_details_master b	where a.style_refernce=b.style_ref_no ".where_con_using_array($job_arr,1,'b.job_no')." order by a.id";
		$fab_data=sql_select($fab_sql);
		foreach($fab_data as $val){
	
			$job_fab_arr[$val[csf("job_no")]]['fab_desc']=$val[csf("fabrication")];
		}
	?>
    <table id="scroll_body" align="center" style="height:auto; width:1300px; margin:0 auto; padding:0;">
        <tr>
            <td width="1380">
            <?
            foreach($job_no_array as $rdata=>$det)
            {
                //ksort($size_array[$det["job_no"]]);
                ?><br/>
                <table width="1240px" align="center" border="1" rules="all" id="table_header_1">
                    <tr style="background-color:#FFF">
                        <td width="60" align="right">Job No: </td><td width="90" onClick="openmypage_job_color_size('requires/size_and_color_break_report_controller.php?action=job_color_size&job_no=<?=$det["job_no"]; ?>','Job Color Size');"><a href="##"><?=$det['job_no']; ?></a></td>
                        <td width="60" align="right">Job Qty: </td><td width="90"><?=$det['job_quantity']."(Pcs)"; ?></td>
                        <td width="60" align="right">Company: </td><td width="90"><?=$companyArr[$det['company_name']]; ?></td>
						<td width="60" align="right">Owner: </td><td width="90"><?=$companyArr[$det['style_owner']]; ?></td>
                        <td width="60" align="right">Buyer: </td><td width="85"><?=$buyerArr[$det['buyer_name']]; ?></td>
                        <td width="60" align="right">Brand: </td><td width="85"><?=$brand_nameArr[$det['brand_id']]; ?></td>
                        <td width="60" align="right">Season: </td><td width="85"><?=$seasonArr[$det['season']]; ?></td>
                        <td width="65" align="right">Style Ref.: </td><td width="85"><?=$det['style_ref_no']; ?></td>
                        <td width="70" align="right">Prod. Dept.: </td><td width="80"><?=$product_dept[$det['product_dept']]; ?></td>
                        <td width="60" align="right">Merchant: </td><td width="90"><?=$marchentrArr[$det['dealing_marchant']]; ?></td>
                        <td width="60" align="right">Ord. Re. No: </td><td width="90"><?=$det['order_repeat_no']; ?></td>
                    </tr>
                </table>
                <br/>
                <table width="1240px" align="center" border="1" rules="all" class="rpt_table" id="color_size">
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
                            foreach($size_array[$det['job_no']] as $key=>$value)
                            {
                                if($value!="")
                                {
                                    ?><th width="60"><?=$itemSizeArr[$value]; ?></th><?
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
                                $countrysl=1;
                                foreach($po_color_array [$det['job_no']][$key][$item_value][$country_value] as $color_key=>$color_value)
                                {
                                    if($countrysl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                ?>
                                    <tr bgcolor="<?=$bgcolor;?>">
                                        <?
										$pack_handover_date=$po_phd_date_array[$det['job_no']][$key];
                                        if($posl==1)
                                        {
                                            ?><td align="center" valign="middle" rowspan="<?=$po_rowspan_arr[$key]; ?>" ><?=$value; echo "<br/> Ship Date:".change_date_format($po_ship_date_array[$det['job_no']][$key],"dd-mm-yyyy","-"); echo "<br/>".date('l', strtotime($po_ship_date_array[$det['job_no']][$key])).'<br> PHD Date: '.change_date_format($pack_handover_date); ?></td><?
                                        }
                                        if($presantation_type==1)
                                        {
                                            if($posl==1)
                                            {
                                            ?><td align="center" valign="middle" rowspan="<?=$po_rowspan_arr[$key]; ?>" ><?=$po_file_no_array[$det['job_no']][$key]['file']; ?></td><?
                                            }
                                            if($posl==1)
                                            {
                                                ?><td align="center" valign="middle" rowspan="<?=$po_rowspan_arr[$key]; ?>" ><?=$po_ref_no_array[$det['job_no']][$key]['ref']; ?></td><?
                                            }
                                        }
                                        if($posl==1)
                                        {
                                            $po_price = $po_qnty_price_array[$det['job_no']][$key];
                                            $qnty = $po_qnty_array[$det['job_no']][$key] ? $po_qnty_array[$det['job_no']][$key] : 0;
                                            ?><td align="center" valign="middle" rowspan="<?=$po_rowspan_arr[$key]; ?>"><?=$qnty.' (Pcs) <br>'.number_format($po_price,2).' <b>'.$currency[$row[csf("currency_id")]].'</b>'; ?></td><?
                                        }
                                        if($itemsl==1)
                                        {
                                        	$item_id_arr=explode(",", $job_no_array[$det['job_no']]['gmts_item_id']);
                                        	foreach ($item_id_arr as $itemid) {
                                        		$item_str_arr[$itemid]=$garments_item[$itemid];
                                        	}                                           
                                            ?><td align="center" valign="middle"  title="Fabrication from Inquery Page" rowspan="<?=$item_rowspan_arr[$key][$item_key]; ?>" ><?= implode(",", $item_str_arr).'<br>'.$job_no_array[$det['job_no']]['set_smv'] ;?></td><?
                                        }
                                        if($countrysl==1)
                                        {
                                         
										  
										    ?><td align="center" valign="middle" rowspan="<?=$country_rowspan_arr[$key][$item_key][$country_key]; ?>"><?=$countryArr[$country_value]."<br/>".change_date_format($po_country_ship_date_array [$det['job_no']][$key][$item_value][$country_value],"dd-mm-yyyy","-")."<br/>".date('l', strtotime($po_country_ship_date_array [$det['job_no']][$key][$item_value][$country_value])); ?></td><?
                                        }
                                        ?>
                                        <td><?=$colorArr[$color_value] ;?></td>
                                        <td align="right"><?=$po_color_qnty_array [$det['job_no']][$key][$item_value][$country_value][$color_value]; ?></td>
                                        <?
                                        foreach($size_array[$det['job_no']] as $key_s=>$value_s)
                                        {
                                            if($value_s !="")
                                            {
                                                ?><td align="right"><?=$po_color_size_qnty_array [$det['job_no']][$key][$item_value][$country_value][$color_value][$value_s]; ?></td><?
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
                                    <td align="right"><?=$po_country_qnty_array[$det['job_no']][$key][$item_key][$country_key]; ?></td>
                                    <?
                                    foreach($job_size_array[$det['job_no']] as $key_s=>$value_s)
                                    {
                                        if($value_s !="")
                                        {
                                            ?><td align="right"><?=$po_country_size_tot_qnty_array [$det['job_no']][$key][$item_value][$country_value][$value_s]; ?></td><?
                                        }
                                    }
                                    ?>
                                </tr>
                            <?
                            }
                            ?>
                            <tr style="font-weight:bold; font-size:12px">
                                <td colspan="3">Item Total:</td>
                                <td align="right"><?=$po_item_qnty_array[$det['job_no']][$key][$item_key]; ?></td>
                                <?
                                foreach($size_array[$det['job_no']] as $key_s=>$value_s)
                                {
                                    if($value_s !="")
                                    {
                                        ?><td align="right"><?=$po_item_size_tot_qnty_array [$det['job_no']][$key][$item_value][$value_s]; ?></td><?
                                    }
                                }
                                ?>
                            </tr>
                            <?
                        }
                        if($presantation_type==1) $colspn=7; else $colspn=7;
                        ?>
                        <tr style="font-weight:bold; font-size:12px">
                            <td colspan="<?=$colspn; ?>">Po Total:</td>
                            <td align="right"><?=$po_qnty_color_size_table_array [$det['job_no']][$key]; ?></td>
                            <?
                            foreach($size_array[$det['job_no']] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
                                    ?><td align="right"><?=$po_size_tot_qnty_array [$det['job_no']][$key][$value_s]; ?></td><?
                                }
                            }
                            ?>
                        </tr>
                        <tr style="font-weight:bold; font-size:12px">
                            <td colspan="<?=$colspn; ?>">Dzn Total:</td>
                            <td align="right"><?= $po_qnty_color_size_table_array [$det['job_no']][$key]/12; ?></td>
                            <?
                            foreach($size_array[$det['job_no']] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
                                    ?><td align="right"><?=$po_size_tot_qnty_array [$det['job_no']][$key][$value_s]/12; ?></td><?
                                }
                            }
                            ?>
                        </tr>
                        <tr style="font-weight:bold; font-size:12px">
                            <td colspan="<?=$colspn; ?>">Pack Total:</td>
                            <td align="right"><?= $po_qnty_color_size_table_array [$det['job_no']][$key]/$job_no_array[$det['job_no']]['total_set_qnty']; ?></td>
                            <?
                            foreach($size_array[$det['job_no']] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
                                    ?><td align="right"><?=$po_size_tot_qnty_array [$det['job_no']][$key][$value_s]/$job_no_array[$det['job_no']]['total_set_qnty']; ?></td><?
                                }
                            }
                            ?>
                        </tr>
                        <?
                        $total_set_qnty=$job_no_array[$det['job_no']]['total_set_qnty'];
                    }
                    ?>
                    <tr style="font-weight:bold; font-size:12px">
                        <td colspan="<?=$colspn; ?>">Grand Total:</td>
                        <td align="right"><?=$job_qnty_color_size_table_array [$det['job_no']]; ?></td>
                        <?
                        foreach($size_array[$det['job_no']] as $key_s=>$value_s)
                        {
                            if($value_s !="")
                            {
                                ?><td align="right"><?=$job_size_tot_qnty_array [$det['job_no']][$value_s]; ?></td><?
                            }
                        }
                        ?>
                    </tr>
                    <tr style="font-weight:bold; font-size:12px">
                        <td colspan="<?=$colspn; ?>">Grand Dzn Total:</td>
                        <td align="right"><?=$job_qnty_color_size_table_array [$det['job_no']]/12; ?></td>
                        <?
                        foreach($size_array[$det['job_no']] as $key_s=>$value_s)
                        {
                            if($value_s !="")
                            {
                                ?><td align="right"><?=$job_size_tot_qnty_array [$det['job_no']][$value_s]/12; ?></td><?
                            }
                        }
                        ?>
                    </tr>
                    <tr style="font-weight:bold; font-size:12px">
                        <td colspan="<?=$colspn; ?>">Grand Pack Total:</td>
                        <td align="right"><?=$job_qnty_color_size_table_array [$det['job_no']]/$total_set_qnty; ?></td>
                        <?
                        foreach($size_array[$det['job_no']] as $key_s=>$value_s)
                        {
                            if($value_s !="")
                            {
                                ?><td align="right"><?=$job_size_tot_qnty_array [$det['job_no']][$value_s]/$total_set_qnty; ?></td><?
                            }
                        }
                        ?>
                    </tr>
					
					<?php
					// $style_color_size_arr[$row[csf('style_ref_no')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['color_size_qnty']
					$no_color=array();
					foreach($style_color_size_arr[$det['style_ref_no']] as $color_id=>$size_data){
						$no_color[$det['style_ref_no']]+=1;
						foreach($size_data as $size_id=>$val){
						$color_sum[$det['style_ref_no']][$color_id]+=$val['color_size_qnty'];
						}
						
					}

					// print_r($no_color);
					//================================issue==19934>26-09-2021=================					
					$style="";
					foreach($style_color_size_arr[$det['style_ref_no']] as $color_id=>$size_data)
					{
						?>
							<tr style="font-weight:bold; font-size:12px">
								<?php
								if($style!=$det['style_ref_no']){?>
								<td align="center" rowspan="<?=2*$no_color[$det['style_ref_no']]+1;?>"><?=$det['style_ref_no'];?></td>
								<td align="center" colspan="<?=$colspn-2; ?>" rowspan="<?= 2*$no_color[$det['style_ref_no']]+1;?>">Color Size Wise Summery</td>
								<?}?>
								<td align="right"><?=$colorArr[$color_id]; ?></td>
								<td align="right"><?=$color_sum[$det['style_ref_no']][$color_id]; ?></td>
								<?
								foreach($size_array[$det['job_no']] as $size_id=>$val){ ?>
									<td align="right"><?echo 
									$size_data[$val]['color_size_qnty'];
									$grand_size_data[$size_id]+=$size_data[$val]['color_size_qnty'];
									?></td><?
								}
								?>
							</tr>
							<tr style="font-weight:bold; font-size:12px">
								<td align="right">Color Dzn Total</td>
								<td align="right"><?= number_format($color_sum[$det['style_ref_no']][$color_id]/12,2); ?></td>
								<?
								foreach($size_array[$det['job_no']] as $size_id=>$val){ ?>
									<td align="right"><? echo number_format($size_data[$val]['color_size_qnty']/12,2);
									 ?></td><?
								}
								?>
							</tr>
							<? $style=$det['style_ref_no'];
							$color_grand_total+=$color_sum[$det['style_ref_no']][$color_id];
			   		} ?>
			   		<tr style="font-weight:bold; font-size:12px">
						<td align="right">Grand Pack Total</td>
						<td align="right"><?= number_format($color_grand_total/$total_set_qnty,2); ?></td>
						<?
						foreach($size_array[$det['job_no']] as $size_id=>$val){ ?>
							<td align="right"><?= number_format($grand_size_data[$size_id]/$total_set_qnty,2); ?></td><?
						}
						?>
					</tr>
					<?
					$no_country=array();$numbers_row=0;$ctryArr=array();
					foreach($style_country_color_size_arr[$det['style_ref_no']] as $country_id=>$color_data){
						
						foreach ($color_data as $color_id => $sizedata) {
							$no_country[$det['style_ref_no']]+=1;
							$ctryArr[$det['style_ref_no']][$country_id]=$country_id;
							$numbers_row+=1;
							$no_country_color[$det['style_ref_no']][$country_id]+=1;
							foreach($sizedata as $size_id=>$val){
								$country_color_sum[$det['style_ref_no']][$country_id][$color_id]+=$val['country_color_size_qnty'];
							}
						}					
					}
					$style="";
					$countryArr = return_library_array("select id,country_name from lib_country ","id","country_name");
					/*echo '<pre>';
					print_r($style_country_color_size_arr); die;*/
					$country_span=0;
					foreach($style_country_color_size_arr[$det['style_ref_no']] as $country_id=>$color_data)
					{
						$k=0;
						$style_span=0;
						foreach ($color_data as $color_id => $size_data) {
						?>
							<tr style="font-weight:bold; font-size:12px">
								<?php
								if($country_span==0){?>
								<td align="center" rowspan="<?=count($ctryArr[$det['style_ref_no']])*3+$no_country[$det['style_ref_no']];?>" title="<?=count($ctryArr[$det['style_ref_no']]).'==>'.$no_country[$det['style_ref_no']].'==>'.$numbers_row;;?>">Country Wise Summary</td>
								<?} 
								if($style_span==0){?>
								<td align="center" colspan="<?=$colspn-2; ?>" rowspan="<?=3+$no_country_color[$det['style_ref_no']][$country_id];?>"><?= $countryArr[$country_id]  ?></td>
								<?} 
								
									
								?>
								<td align="right"><?=$colorArr[$color_id]; ?></td>
								<td align="right"><?=$country_color_sum[$det['style_ref_no']][$country_id][$color_id]; ?></td>
								<?
								foreach($size_array[$det['job_no']] as $size_id=>$val){ ?>
									<td align="right"><?echo 
									$size_data[$val]['country_color_size_qnty'];
									$grand_size_data[$country_id][$val]+=$size_data[$val]['country_color_size_qnty'];
									?></td><?
								}
								?>
							</tr>
							<?  
							$style=$det['style_ref_no'];
							$country_color_grand_total[$country_id]+=$country_color_sum[$det['style_ref_no']][$country_id][$color_id];
							$style_span++;
							$country_span++;
			   			}
			   			
							$k++;
							?>
							<tr style="font-weight:bold; font-size:12px">
								<td align="right">Country Pcs Total</td>
								<td align="right"><?= number_format($country_color_grand_total[$country_id],2); ?></td>
								<?
								foreach($size_array[$det['job_no']] as $size_id=>$val){ ?>
									<td align="right"><? echo number_format($grand_size_data[$country_id][$val],2);
									 ?></td><?
								}
								?>
							</tr>
							<tr style="font-weight:bold; font-size:12px">
								<td align="right">Country Dzn Total</td>
								<td align="right"><?= number_format($country_color_grand_total[$country_id]/12,2); ?></td>
								<?
								foreach($size_array[$det['job_no']] as $size_id=>$val){ ?>
									<td align="right"><? echo number_format($grand_size_data[$country_id][$val]/12,2);
									 ?></td><?
								}
								?>
							</tr>
							<tr style="font-weight:bold; font-size:12px">
								<td align="right">Country Pack Total</td>
								<td align="right"><?= number_format($country_color_grand_total[$country_id]/$total_set_qnty,2); ?></td>
								<?
								foreach($size_array[$det['job_no']] as $size_id=>$val){ ?>
									<td align="right"><? echo number_format($grand_size_data[$country_id][$val]/$total_set_qnty,2);
									 ?></td><?
								}
								?>
							</tr>
							<?
			   		} ?>
			   		
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
if($action=='packing_list_for_cutting'){
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$size_library=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	$fabric_id=str_replace("'","",$fabric_id);

	$fabric_data=sql_select("SELECT a.id, a.job_no, a.uom, a.fab_nature_id, b.company_name, b.buyer_name, b.style_ref_no from wo_pre_cost_fabric_cost_dtls a join wo_po_details_master b on a.job_id=b.id where a.status_active=1 and a.is_deleted=0 and a.id in ($fabric_id) and b.status_active=1 and b.is_deleted=0 group by a.id, a.job_no, a.uom, a.fab_nature_id, b.company_name, b.buyer_name, b.style_ref_no");
	$fabricqty=0;
	$condition= new condition();
	$condition->job_no("='$job_no'");	
	$condition->init();
	$fabric= new fabric($condition);
	$fabric_qty=$fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();

	foreach($fabric_data as $row){
		if($row[csf('fab_nature_id')]==3){
			$fabricqty+=$fabric_qty['woven']['grey'][$row[csf("id")]][$row[csf("uom")]];
		}else{
			$fabricqty+=$fabric_qty['knit']['grey'][$row[csf("id")]][$row[csf("uom")]];
		}
		$company_id=$row[csf("company_name")];
		$buyer_id=$row[csf("buyer_name")];
		$style_ref_no=$row[csf("style_ref_no")];
		$uom=$row[csf("uom")];
	}
	$slab_data=sql_select("SELECT ship_plan from lib_excess_cut_slab where status_active=1 and is_deleted=0 and comapny_id=$company_id and buyer_id=$buyer_id");
	$ship_plan_data=0;
	if(count($slab_data)>0){
		foreach($slab_data as $row){
			if($row[csf('ship_plan')]>0){
				$ship_plan_data=$row[csf('ship_plan')];
			}						
		}
	}

	$color_size_data=sql_select("SELECT a.id, a.size_number_id, a.color_number_id, a.order_quantity, a.plan_cut_qnty, b.id as po_id, b.po_number from wo_po_color_size_breakdown a join wo_po_break_down b on a.po_break_down_id=b.id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.job_no_mst='$job_no'");
	$color_size_arr=array();
	foreach($color_size_data as $row){
		$order_quantity+=$row[csf('order_quantity')];
		$plan_cut_qnty+=$row[csf('plan_cut_qnty')];
		/* $po_array[$row[csf("po_id")]]['po_no']=$row[csf("po_number")];
		$po_color_arr[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$po_size_arr[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$color_size_arr[$row[csf('po_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('order_quantity')]; */
		$color_size_array[$row[csf("po_id")]][$row[csf("color_number_id")]]=$row[csf("order_quantity")];
		$po_size_qty_array[$row[csf("po_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['po_qty']+=$row[csf("plan_cut_qnty")];
		$po_array[$row[csf("po_id")]]['po_no']=$row[csf("po_number")];
		$posize_array[$row[csf("size_number_id")]]=$row[csf("size_number_id")];
	}
	$po_rowspan_arr=array();
	foreach($color_size_array as $pokey=>$po_data)
	{
		$po_row_span=0;
		foreach($po_data as $colorkey=>$val)
		{
			$po_row_span++;
		}
		$po_rowspan_arr[$pokey]=$po_row_span;
	}
	/* echo '<pre>';
	print_r($po_array); die; */
	ob_start(); ?>
	<div style="width:1000px; float:left; margin:0 auto">
	<table style="width:850px" >
		<tr>
			<td colspan="6" align="center">
				<b style="font-size:25px;">Packing List For Cutting</b><br>
				<b style="font-size:20px;"><? echo $company_arr[$company_id]; ?></b>
			</td>
		</tr>
	</table>
	<br>
	<table  border="0" cellpadding="0" cellspacing="0" style="text-align:center;" >
		<tr>
			<td align="Left" width="210"><strong>Job NO</strong></td>
			<td align="left">:&nbsp;<?= $job_no ?></td>
		</tr>
		<tr>
			<td align="Left" width="210"><strong>Buyer Name</strong></td>
			<td align="left">:&nbsp;<?= $buyer_arr[$buyer_id] ?></td>
		</tr>
		<tr>
			<td align="Left" width="210"><strong>Style Name</strong></td>
			<td align="left">:&nbsp;<?= $style_ref_no ?></td>
		</tr>
		<tr>
			<td align="Left" width="210"><strong>Order QTY</strong></td>
			<td align="left">:&nbsp;<? echo fn_number_format($order_quantity,0) ?></td>
		</tr>
		<tr>
			<td align="Left" width="210"><strong>Ship Plan QTY</strong></td>
			<td align="left">:&nbsp;<?= fn_number_format($order_quantity+($order_quantity*$ship_plan_data/100),0) ?></td>
		</tr>
		<tr>
			<td align="Left" width="210"><strong>Fabric Req QTY</strong></td>
			<td align="left">:&nbsp;<?= fn_number_format($fabricqty,0).' '. $unit_of_measurement[$uom] ?></td>
		</tr>
		<tr>
			<td align="Left" width="210"><strong>Excess Cutting QTY</strong></td>
			<td align="left">:&nbsp;<?= fn_number_format($plan_cut_qnty,0); ?></td>
		</tr>		
		<tr>
			<td align="Left" width="210"><strong>Consumption Per(Pcs)</strong></td>
			<td align="left">:&nbsp;<?= fn_number_format($fabricqty/$order_quantity,2).' '.$unit_of_measurement[$uom] ?></td>
		</tr>
		<tr>
			<td align="Left" width="210"><strong>Actul Consumption Per(Pcs)</strong></td>
			<td align="left" title="Fabric Req QTY/Excess Cutting QTY">:&nbsp;<?= fn_number_format($fabricqty/$plan_cut_qnty,2).' '.$unit_of_measurement[$uom] ?></td>
		</tr>
	</table>
	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:<?= 320+(count($posize_array)*50) ?>px; text-align:center; margin-top:20px" rules="all">
		<thead>
			<tr>
				<th width="120">Order Number</th>
				<th width="120">Color</th>
				<?
				foreach ($posize_array as $sizid)
				{
				?>
					<th width="50"><? echo  $size_library[$sizid];  ?></th>
				<?
				}
				?>
				<th width="70">Order Total</th>
			</tr>
		</thead>
		<tbody>
			<?
				$k=1; $total_qnty=0;
				foreach ($color_size_array as $pokey=>$po_data)
				{
					$m=1;
					foreach ($po_data as $colorkey=>$color_data)
					{
						$po_rowspan=$po_rowspan_arr[$pokey];
						?>
						<tr>
							<?
							if($m==1)
							{
								?>
								<td rowspan="<? echo $po_rowspan;?>" align="center"><? echo $po_array[$pokey]['po_no']; ?></td>
								<?
							}
							?>
							<td align="left"><? echo $color_library[$colorkey]; ?></td>
							<?
							$po_size_qty=0;$tot_qnty=array();
							foreach ($posize_array as $sizval)
							{
								$size_count=count($sizval);
								$po_size_qty=$po_size_qty_array[$pokey][$colorkey][$sizval]['po_qty']
								?>
								<td align="right"><? echo fn_number_format($po_size_qty,0); ?></td>
								<?
								$tot_qnty[$pokey][$cid]+=$po_size_qty;
								$tot_qnty_size[$sizval]+=$po_size_qty;
							}
							?>
							<td align="right"><? echo fn_number_format($tot_qnty[$pokey][$cid],0); ?></td>
						</tr>
						<?
						$total_qnty+=$tot_qnty[$pokey][$cid];
						$m++;
					}
					$k++;
				}
				?>
				<tr>
				<td colspan="2" align="right"><strong>Size Total</strong></td>
				<?
				foreach ($posize_array as $sizval)
				{
					?>
					<td align="right"><?php echo fn_number_format($tot_qnty_size[$sizval],0); ?></td>
					<?
				}
				?>
				<td align="right"><?php echo fn_number_format($total_qnty,0); ?></td>
				</tr>
		</tbody>
	</table>
	</div>
	<?
	$emailBody=ob_get_contents();
	ob_end_clean();
	echo $emailBody;
}
?>
