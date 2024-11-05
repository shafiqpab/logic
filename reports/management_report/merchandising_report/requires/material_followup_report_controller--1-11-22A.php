<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.trims.php');
require_once('../../../../includes/class4/class.fabrics.php');
require_once('../../../../includes/class4/class.yarns.php');

$user_id = $_SESSION['logic_erp']["user_id"];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$fabric_nature = $_SESSION['fabric_nature'];
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');

//--------------------------------------------------------------------------------------------------------------------
if($action=="print_button_variable_setting")
    {
        $print_report_format_arr = return_library_array("select format_id,format_id from lib_report_template where template_name =$data and module_id=2 and report_id=75 and is_deleted=0 and status_active=1","format_id","format_id");
       // echo "print_report_button_setting('".implode(',',$print_report_format_arr)."');\n";
        exit();
    }

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "load_drop_down( 'requires/material_followup_report_controller', this.value, 'load_drop_down_season', 'season_td');" );
	exit();
}

if ($action=="load_drop_down_season")
{
	echo create_drop_down( "cbo_season_id", 130, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 group by id, season_name order by season_name ASC","id,season_name", 1, "-Select Season-", "", "" );
	exit();
}

if ($action=="style_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
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
				$("#tr_"+i).click();
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
	if ($data[0]==0) $company_name=""; else $company_name="company_name='$data[0]'";
	if ($data[1]==0) $buyer_name=""; else $buyer_name=" and buyer_name='$data[1]'";
	if($db_type==0) $year_field="YEAR(insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year";
	else $year_field="";
	//$type_id=str_replace("'","",$type_id);
	//echo $data[2].'d,';
	if($data[2]==1) $type_con="id,job_prefix";else $type_con="id,style_ref_no";
	$sql ="select id,style_ref_no,job_no_prefix_num as job_prefix,$year_field from wo_po_details_master where $company_name $buyer_name"; 
	echo create_list_view("list_view", "Style Ref. No.,Job No,Year","200,100,100","450","310",0, $sql , "js_set_value", "$type_con", "", 1, "0", $arr, "style_ref_no,job_prefix,year", "","setFilterGrid('list_view',-1)","0","",1) ;	
	exit();	 
}
if ($action=="order_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	$company=$data[0];
	$buyer=$data[1];
	$style=$data[2];
	
	//print ($data[1]);
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
				$("#tr_"+i).click();
			}
			
		}
		
	function js_set_value(id)
	{ //alert(id);
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
	if ($data[0]==0) $company_id=""; else $company_id=" and company_name=$data[0]";
	if ($data[1]==0) $buyer_id=""; else $buyer_id=" and buyer_name=$data[1]";
	if ($data[2]==0) $style=""; else $style=" and b.id in($data[2])";
	
	/*if($db_type==0) $year_cond="and year(a.insert_date)='$data[3]'"; 
	else if($db_type==2) $year_cond="and to_char(a.insert_date,'YYYY')='$data[3]'";*/
	
	if($db_type==0) $year_field="YEAR(b.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(b.insert_date,'YYYY') as year";
	else $year_field="";
	
	//$sql ="select distinct a.id,a.po_number,b.job_no_prefix_num as job_prefix,$year_field from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no $company_id  $buyer_id $style $year_cond";
	
	$sql ="select a.id,a.po_number,b.job_no_prefix_num as job_prefix,$year_field from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.is_deleted=0 $company_id  $buyer_id $style";
	
	//echo $sql;
	 
	echo create_list_view("list_view", "Order Number,Job No, Year","150,100,50","440","310",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "po_number,job_prefix,year", "","setFilterGrid('list_view',-1)","0","",1) ;	
	exit();
}

$tmplte=explode("**",$data);
if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;


if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_library=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
	$country_name_library=return_library_array( "select id, country_name from lib_country", "id", "country_name"  );
	$lib_supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
	$lib_color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	$lib_season_arr=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name"  );

	$company_name=str_replace("'","",$cbo_company_name);
	$serch_by=str_replace("'","",$cbo_search_by);
	$txt_style_id=str_replace("'","",$txt_style_id);
	$order_no_id=str_replace("'","",$txt_order_no_id);
	$cbo_season_id=str_replace("'","",$cbo_season_id);

	$buyer_id_cond="";
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}

	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_job_no=trim($txt_job_no);
	$txt_style_id=trim($txt_style_id);
	if($txt_style_id!="")
	{
		if($txt_style_id!="" || $txt_style_id!=0) $jobcond="and a.id in($txt_style_id) "; else $jobcond="";
	}
	else
	{
	if($txt_job_no!="" || $txt_job_no!=0) $jobcond="and a.job_no_prefix_num in('".$txt_job_no."')"; else $jobcond="";
	//if(str_replace("'","",$txt_style_ref)!="") $style_ref_cond=" and a.style_ref_no ='".str_replace("'","",trim($txt_style_ref))."'"; else $style_ref_cond="";
	if(str_replace("'","",$txt_style_ref)!="") $style_ref_cond=" and a.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'"; else $style_ref_cond="";
	}
	if($order_no_id!="")
	{
	if(str_replace("'","",$order_no_id)!="") $ordercond=" and b.id in($order_no_id)"; else $ordercond="";
	}
	else
	{
		if(str_replace("'","",$txt_order_no)!="") $ordercond=" and b.po_number like '%".str_replace("'","",$txt_order_no)."%'"; else $ordercond="";
	}

	$season_con='';

	if(!empty($cbo_season_id))
	{
		$season_con=" and a.season_buyer_wise='$cbo_season_id' ";
	}


	if(str_replace("'","",$cbo_item_group)=="")
	{
		$item_group_cond="";
		$item_group_cond2="";
		$item_group_cond3="";
	}
	else
	{
		$item_group_cond="and e.trim_group in(".str_replace("'","",$cbo_item_group).")";
		$item_group_cond2="and b.trim_group in(".str_replace("'","",$cbo_item_group).")";
		$item_group_cond3="and a.item_group_id in(".str_replace("'","",$cbo_item_group).")";
	}
	$date_type=str_replace("'","",$cbo_date_type);

	$date_cond='';
	if($date_type==2)
	{
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			$start_date=(str_replace("'","",$txt_date_from));
			$end_date=(str_replace("'","",$txt_date_to));
			$date_cond="and c.country_ship_date between '$start_date' and '$end_date'";
		}
	}
	else
	{
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			$start_date=(str_replace("'","",$txt_date_from));
			$end_date=(str_replace("'","",$txt_date_to));
			$date_cond="and b.pub_shipment_date between '$start_date' and '$end_date'";
		}
	}
	//if (str_replace("'","",$txt_job_no)=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in (".str_replace("'","",$txt_job_no).") ";

	

	$file_no=str_replace("'","",$txt_file_no);
	$internal_ref=str_replace("'","",$txt_internal_ref);
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' ";
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' ";
	$cbo_year=str_replace("'","",$cbo_year);
	if($db_type==0)
	{
		if(trim($cbo_year)!=0) $year_cond=" and YEAR(a.insert_date)=$cbo_year"; else $year_cond="";
	}
	else if($db_type==2)
	{
		if(trim($cbo_year)!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year"; else $year_cond="";
	}
	if(str_replace("'","",$cbo_ship_status)>0){ $ship_status_cond=" and b.shiping_status=$cbo_ship_status";}else{
		$ship_status_cond="";
	};	
	

	//Precost v2 print button.........................................................
	$pre_cost2_print_button_arr=return_library_array( "select template_name,format_id from lib_report_template where module_id = 2 and report_id = 43 and is_deleted = 0 and status_active=1", "template_name", "format_id"  );
	list($first_print_button)=explode(',',$pre_cost2_print_button_arr[$company_name]);
	
	$print_button_action_arr=array(50=>'preCostRpt',51=>'preCostRpt2',52=>'bomRpt',63=>'bomRpt2',156=>'accessories_details',157=>'accessories_details2',158=>'preCostRptWoven',159=>'bomRptWoven',170=>'preCostRpt3',171=>'preCostRpt4',142=>'preCostRptBpkW',192=>'checkListRpt');
	$print_button_action = $print_button_action_arr[$first_print_button];
	?>
	<div style="width:3060px">
	<?

	$local_foreign_arr = array (
		array(1=>"302",2=>"311"),
		array(1=>"301",2=>"310"),
		array(1=>"300",2=>"309"),
		array(1=>"279",2=>"308"),
		array(1=>"278",2=>"307"),
		array(1=>"277",2=>"306"),
		array(1=>"276",2=>"305")
	
	  );






	

	// echo "<pre>";
	// print_r($local_foreign_arr);

	if($db_type==0) $select_job_year="year(a.insert_date) as job_year"; else $select_job_year="to_char(a.insert_date,'YYYY') as job_year";
	//wo_pre_cost_fabric_cost_dtls c,wo_po_color_size_breakdown d, wo_pre_cos_fab_co_color_dtls e

	// Main query =========
	$sql_po="SELECT a.id as job_id,a.buyer_name, a.season_buyer_wise,a.job_no,a.job_quantity, a.job_no_prefix_num,a.order_uom, $select_job_year, a.style_ref_no, a.total_set_qnty as ratio, b.id as po_id, b.file_no, b.po_number, b.po_quantity as po_qnty, b.plan_cut,b.grouping, b.pub_shipment_date,c.color_number_id as color_id,c.order_quantity,d.id as fab_dtls_id,d.body_part_id as bpart_id,d.color_size_sensitive,d.lib_yarn_count_deter_id as deter_id,d.color_type_id,d.construction,d.composition,d.gsm_weight,d.nominated_supp,d.width_dia_type,d.uom,d.source_id,d.avg_cons
	from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_fabric_cost_dtls d
	where a.id=b.job_id and c.po_break_down_id=b.id and  a.id=d.job_id and  d.job_id=b.job_id and  d.job_id=c.job_id and  a.company_name=$company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1   $date_cond $buyer_id_cond $jobcond $ordercond $ship_status_cond $style_ref_cond 
	order by a.job_no, c.color_number_id";

	// echo  $sql_po; die;
	$sql_po_result=sql_select($sql_po);
	$result_data_arr=$result_job_wise=array();$all_po_id=""; $chk_po_arr=array(); $temp_dtls_id = array();
	foreach($sql_po_result as $row)
	{
        if ($all_po_id == "") $all_po_id = $row[csf("po_id")]; else $all_po_id .= "," . $row[csf("po_id")];
        if ($all_job_id == "") $all_job_id = $row[csf("job_id")]; else $all_job_id .= "," . $row[csf("job_id")];
	}
	
	$all_job_ids=implode(",",array_unique(explode(",",$all_job_id)));
	$all_po_ids=implode(",",array_unique(explode(",",$all_po_id)));
	$txt_style_ref=str_replace("'","",$txt_style_ref);


	$jobIds=chop($all_job_ids,','); $job_cond_for_in="";  
	$job_ids=count(array_unique(explode(",",$all_job_ids)));
	if($db_type==2 && $job_ids>1000)
	{
		$job_cond_for_in=" and (";
		$jobIdsArr=array_chunk(explode(",",$jobIds),999);
		foreach($jobIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$job_cond_for_in.=" d.job_id in($ids) or"; 
		}
		$job_cond_for_in=chop($job_cond_for_in,'or ');
		$job_cond_for_in.=")";
	}
	else
	{
		$jobIds=implode(",",(array_unique(explode(",",$jobIds))));
		$job_cond_for_in=" and d.job_id in($jobIds)";
	}
	
	$sql_fab="SELECT d.id as fab_dtls_id,d.job_id,d.job_no,d.body_part_id as bpart_id,d.color_size_sensitive,d.lib_yarn_count_deter_id as deter_id,d.color_type_id,d.construction,d.composition,e.gmts_color_id,e.contrast_color_id 
	from wo_pre_cost_fabric_cost_dtls d, wo_pre_cos_fab_co_color_dtls e 
	where d.id=e.pre_cost_fabric_cost_dtls_id and d.status_active=1 and e.status_active=1 $job_cond_for_in";
	$sql_fab_result=sql_select($sql_fab);
	foreach($sql_fab_result as $row)
	{
		$bpart_id=$row[csf("bpart_id")];
		$deter_id=$row[csf("deter_id")];
		$color_id=$row[csf("gmts_color_id")];
		$contrast_fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$color_id]["contrast_color_id"]=$row[csf("contrast_color_id")];
	}
	unset($sql_fab_result);
	
	$condition1= new condition();
	$condition1->company_name("=$company_name");
	if(!empty($all_po_id)){
		$condition1->po_id_in("$all_po_ids");
	}
	// echo "=".$txt_style_ref;die;
	
	if(trim($txt_style_id)!="" && trim($txt_style_id)!="") 
	{
		$condition1->jobid_in("$txt_style_id");
	}
	else if(trim($txt_style_id)=="" && trim($txt_style_ref)!="") 
	{
	   $condition1->style_ref_no("='$txt_style_ref'");
	}
	//jobid_in
	
	if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
	{
		$condition1->pub_shipment_date(" between '$start_date' and '$end_date'");
    }
	if(str_replace("'","",$txt_job_no) !='')
	{
	   $condition1->job_no_prefix_num("in($txt_job_no)");
	}
	
					 
	$condition1->init();
	$fabric= new fabric($condition1);
	//echo $fabric->getQuery(); die;
	$fabric_req_arr=$fabric->getQtyArray_by_OrderBodypartDeterminIdAndGmtscolor_knitAndwoven_greyAndfinish();
	$fabric_req_cost_arr=$fabric->getAmountArray_by_OrderBodypartDeterminIdAndGmtscolor_knitAndwoven_greyAndfinish();
	

	 // === Main array ================	
	foreach($sql_po_result as $row) // main array
	{
        $bpart_id = $row[csf("bpart_id")];
        $deter_id = $row[csf("deter_id")];
        $color_id = $row[csf("color_id")];
		$po_id = $row[csf("po_id")];

        if ($row[csf("color_size_sensitive")]==3) 
        {
        	$fabric_color=$contrast_fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$color_id]["contrast_color_id"];
        }
        else
        {
        	$fabric_color=$color_id;
        }
		
		$fabric_req_knit=$fabric_req_arr['knit']['grey'][$po_id][$bpart_id][$deter_id][$fabric_color][$row[csf("uom")]];
		$fabric_req_wov=$fabric_req_arr['woven']['grey'][$po_id][$bpart_id][$deter_id][$fabric_color][$row[csf("uom")]];

		$tot_fabric_req=$fabric_req_knit+$fabric_req_wov;
		$fabric_req_knit_cost=$fabric_req_cost_arr['knit']['grey'][$po_id][$bpart_id][$deter_id][$fabric_color][$row[csf("uom")]];
		$fabric_req_wov_cost=$fabric_req_cost_arr['woven']['grey'][$po_id][$bpart_id][$deter_id][$fabric_color][$row[csf("uom")]];

		$tot_fabric_req_cost=$fabric_req_wov_cost+$fabric_req_knit_cost;
		

        $fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$fabric_color][$row[csf("source_id")]]["pre_fab_req_qty"] = $tot_fabric_req;
		 $fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$fabric_color][$row[csf("source_id")]]["pre_fab_req_amt"] = $tot_fabric_req_cost;
		
		 $fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$fabric_color][$row[csf("source_id")]]["po_id"] .= $row[csf("po_id")] . ',';
		 
        $fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$fabric_color][$row[csf("source_id")]]["buyer_name"] = $row[csf("buyer_name")];
        $fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$fabric_color][$row[csf("source_id")]]["job_no_prefix_num"] = $row[csf("job_no_prefix_num")];
        $fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$fabric_color][$row[csf("source_id")]]["job_no"] = $row[csf("job_no")];
        $fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$fabric_color][$row[csf("source_id")]]["construction"] = $row[csf("construction")];
        $fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$fabric_color][$row[csf("source_id")]]["composition"] = $row[csf("composition")];
        $fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$fabric_color][$row[csf("source_id")]]["gsm_weight"] = $row[csf("gsm_weight")];
        $fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$fabric_color][$row[csf("source_id")]]["nominated_supp"] = $row[csf("nominated_supp")];
        $fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$fabric_color][$row[csf("source_id")]]["color_type_id"] = $color_type[$row[csf("color_type_id")]];
        $fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$fabric_color][$row[csf("source_id")]]["width_dia_type"] = $fabric_typee[$row[csf("width_dia_type")]];
        $fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$fabric_color][$row[csf("source_id")]]["season_buyer_wise"] = $row[csf("season_buyer_wise")];
        $fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$fabric_color][$row[csf("source_id")]]["fab_uom"] = $row[csf("uom")];
        $fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$fabric_color][$row[csf("source_id")]]["source_id"] = $row[csf("source_id")];
        $fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$fabric_color][$row[csf("source_id")]]["avg_cons"] = $row[csf("avg_cons")];
        $fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$fabric_color][$row[csf("source_id")]]["order_uom"] = $row[csf("order_uom")];
        $fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$fabric_color][$row[csf("source_id")]]["sensitive"] = $row[csf("color_size_sensitive")];

        $fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$fabric_color][$row[csf("source_id")]]["style_ref_no"] = $row[csf("style_ref_no")];
        if ($row[csf("grouping")]) {
            $fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$fabric_color][$row[csf("source_id")]]["ref_no"] .= $row[csf("grouping")] . ',';
        }
        if ($row[csf("file_no")]) {
            $fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$fabric_color][$row[csf("source_id")]]["file_no"] .= $row[csf("file_no")] . ',';
        }

        if ($fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$fabric_color][$row[csf("source_id")]]["po_number"] == "")
            $fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$fabric_color][$row[csf("source_id")]]["po_number"] = $row[csf("po_number")];
        else $fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$fabric_color][$row[csf("source_id")]]["po_number"] .= '**' . $row[csf("po_number")];

        if ($chk_po_arr[$row[csf("po_id")]] == "") {
            $fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$fabric_color][$row[csf("source_id")]]["po_qnty_pcs"] += $row[csf("po_qnty")] * $row[csf("ratio")];
            $fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$fabric_color][$row[csf("source_id")]]["po_qnty"] += $row[csf("po_qnty")];
            $chk_po_arr[$row[csf("po_id")]] = 1000;
        }

        $fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$fabric_color][$row[csf("source_id")]]["plan_cut"] += $row[csf("plan_cut")];
        $fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$fabric_color][$row[csf("source_id")]]["wo_qty"] += $row[csf("grey_fab_qnty")];

        $fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$fabric_color][$row[csf("source_id")]]["gmt_color"].=$color_id.',';

        $fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$fabric_color][$row[csf("source_id")]]["pub_shipment_date"] .= $row[csf("pub_shipment_date")] . ',';

        $job_qty_wise_arr[$row[csf("job_no")]]["po_qnty"] = $row[csf("job_quantity")];
        $job_qty_wise_arr[$row[csf("job_no")]]["po_qnty_pcs"] = $row[csf("job_quantity")] * $row[csf("ratio")];

        $po_no_arr[] = $row[csf('po_id')];
        $job_no_arr[] = $row[csf('job_no')];
	}
	ksort($fabric_wise_arr);
	unset($sql_po_result);
	// echo '<pre>';print_r($fabric_wise_arr);die;

	
	//print_r($fabric_req_arr);die; 
	$poIds=chop($all_po_id,','); $po_cond_for_in=""; $po_cond_for_in2=""; $po_cond_for_in3=""; $po_cond_for_in4="";
	$po_ids=count(array_unique(explode(",",$all_po_id)));
	if($db_type==2 && $po_ids>1000)
	{
		$po_cond_for_in=" and (";
		$po_cond_for_in2=" and (";
		$po_cond_for_in3=" and (";
		
		$poIdsArr=array_chunk(explode(",",$poIds),999);
		foreach($poIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$po_cond_for_in.=" b.po_break_down_id in($ids) or"; 
			$po_cond_for_in2.=" a.po_breakdown_id in($ids) or"; 
			$po_cond_for_in3.=" b.order_id in($ids) or"; 
		
		}
		$po_cond_for_in=chop($po_cond_for_in,'or ');
		$po_cond_for_in.=")";
		$po_cond_for_in2=chop($po_cond_for_in2,'or ');
		$po_cond_for_in2.=")";
		$po_cond_for_in3=chop($po_cond_for_in3,'or ');
		$po_cond_for_in3.=")";
	}
	else
	{
		$poIds=implode(",",(array_unique(explode(",",$poIds))));
		$po_cond_for_in=" and b.po_break_down_id in($poIds)";
		$po_cond_for_in2=" and a.po_breakdown_id  in($poIds)";
		$po_cond_for_in3=" and b.order_id in($poIds)";
	}
	
	

	$booking_req_arr=array();
	$sql_wo="SELECT a.id, a.booking_no,a.booking_date,a.pay_mode,a.supplier_id,a.currency_id,a.exchange_rate,b.gmts_color_id,a.booking_type,b.po_break_down_id as po_id,
	(b.grey_fab_qnty) as grey_fab_qnty,b.job_no,b.amount,c.body_part_id as bpart_id,c.color_size_sensitive,c.lib_yarn_count_deter_id as deter_id
	from wo_booking_mst a, wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c  where a.booking_no=b.booking_no and c.id=b.pre_cost_fabric_cost_dtls_id and a.booking_type=1 and b.fin_fab_qnty>0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1  $po_cond_for_in";
	//echo $sql_wo; die;
	$sql_wo_res=sql_select($sql_wo);
	$usd_id=2;
	foreach ($sql_wo_res as $row)
	{		
		$booking_date=$row[csf("booking_date")];
		$currency_id=$row[csf("currency_id")];
		if($db_type==0)
		{
			$conversion_date=change_date_format($booking_date, "Y-m-d", "-",1);
		}
		else
		{
			$conversion_date=change_date_format($booking_date, "d-M-y", "-",1);
		}
		$currency_rate=set_conversion_rate($usd_id,$conversion_date );
		//echo $currency_id.'='.$row[csf("amount")].'='.$currency_rate.', ';
		if($currency_id==1) //Taka
		{
			$amount=$row[csf("amount")]/$currency_rate;
		}
		else
		{
			$amount=$row[csf("amount")];	
		}
		
		if($all_booking_id=="") $all_booking_id=$row[csf("id")]; else $all_booking_id.=",".$row[csf("id")];
		
		if ($row[csf("color_size_sensitive")]==3) 
        {
        	$fb_fabric_color=$contrast_fabric_wise_arr[$row[csf("job_no")]][$row[csf("bpart_id")]][$row[csf("deter_id")]][$row[csf("gmts_color_id")]]["contrast_color_id"];
        }
        else
        {
        	$fb_fabric_color=$row[csf("gmts_color_id")];
        }

		$booking_req_arr[$row[csf("po_id")]][$row[csf("bpart_id")]][$row[csf("deter_id")]][$fb_fabric_color]['grey']+=$row[csf("grey_fab_qnty")];
		$booking_req_arr[$row[csf("po_id")]][$row[csf("bpart_id")]][$row[csf("deter_id")]][$fb_fabric_color]['amount']+=$amount;

		/*$booking_req_arr[$row[csf("po_id")]][$row[csf("bpart_id")]][$row[csf("deter_id")]][$row[csf("gmts_color_id")]]['grey']+=$row[csf("grey_fab_qnty")];
		$booking_req_arr[$row[csf("po_id")]][$row[csf("bpart_id")]][$row[csf("deter_id")]][$row[csf("gmts_color_id")]]['amount']+=$amount;*/

		if($row[csf("pay_mode")]==3 || $row[csf("pay_mode")]==5)
		{
			$com_supplier=$company_library[$row[csf("supplier_id")]];
		}
		else
		{
			$com_supplier=$supplier_library[$row[csf("supplier_id")]];
		}

		$booking_supp_arr[$row[csf("po_id")]][$row[csf("bpart_id")]][$row[csf("deter_id")]][$fb_fabric_color]['supp_com'].=$com_supplier.',';
		$booking_supp_arr[$row[csf("po_id")]][$row[csf("bpart_id")]][$row[csf("deter_id")]][$fb_fabric_color]['booking_no'].=$row[csf("booking_no")].',';

		/*$booking_supp_arr[$row[csf("po_id")]][$row[csf("bpart_id")]][$row[csf("deter_id")]][$row[csf("gmts_color_id")]]['supp_com'].=$com_supplier.',';
		$booking_supp_arr[$row[csf("po_id")]][$row[csf("bpart_id")]][$row[csf("deter_id")]][$row[csf("gmts_color_id")]]['booking_no'].=$row[csf("booking_no")].',';*/
	}
	unset($sql_wo_res);
	$bkIds=chop($all_booking_id,','); $book_cond_for_in="";  
	$bk_ids=count(array_unique(explode(",",$all_booking_id)));
	if($db_type==2 && $bk_ids>1000)
	{
	$book_cond_for_in=" and (";
	$bkIdsArr=array_chunk(explode(",",$bkIds),999);
	foreach($bkIdsArr as $ids)
	{
	$ids=implode(",",$ids);
	$book_cond_for_in.=" b.work_order_id in($ids) or"; 
	}
	$book_cond_for_in=chop($book_cond_for_in,'or ');
	$book_cond_for_in.=")";
	}
	else
	{
	$bkIds=implode(",",(array_unique(explode(",",$bkIds))));
	$book_cond_for_in=" and b.work_order_id in($bkIds)";
	}
	
	$sql="select b.pi_id,a.id,a.item_category_id,a.pi_number,a.pi_date,a.importer_id, b.work_order_no,b.work_order_id,b.item_group,b.item_prod_id,
	b.determination_id
	from com_pi_master_details  a,  com_pi_item_details b
	where a.id=b.pi_id and a.importer_id=$cbo_company_name   and a.status_active=1 and a.is_deleted=0 and b.status_active=1
	and b.is_deleted=0 $book_cond_for_in";
	//echo $sql ; // die;
	$sql_result = sql_select($sql);
	foreach($sql_result as $row)
	{
		$piDataArr[$row[csf("work_order_no")]].=$row[csf("pi_number")].',';
	}
	unset($sql_result);
	
	$prodKnitDataArr=sql_select("select a.po_breakdown_id as po_id,b.fabric_description_id as deter_id,b.rate,b.body_part_id,a.color_id,
	(CASE WHEN a.entry_form = 17 or a.entry_form  =37 or a.entry_form =225 THEN a.quantity ELSE 0 END) AS knit_qnty_rec
	from order_wise_pro_details a, pro_finish_fabric_rcv_dtls b, inv_receive_master c where a.dtls_id=b.id and b.mst_id=c.id  and c.item_category in (2,3) and (a.entry_form = 17 or a.entry_form = 37 or a.entry_form = 225) and (c.entry_form = 17 or c.entry_form = 37 or c.entry_form = 225) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   $po_cond_for_in2 ");// and c.receive_basis<>9
	$kniting_prod_arr=array();
	foreach($prodKnitDataArr as $row)
	{
	$kniting_prod_arr[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("color_id")]]["knit_qnty_rec"]+=$row[csf("knit_qnty_rec")];
	$kniting_prod_arr[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("color_id")]]["knit_qnty_rec_amt"]+=$row[csf("knit_qnty_rec")]*$row[csf("rate")];
	}
	unset($prodKnitDataArr);
	$issueprodKnitDataArr=sql_select("select a.po_breakdown_id as po_id,d.detarmination_id as deter_id,b.cons_rate,b.body_part_id,a.color_id,
	(CASE WHEN a.entry_form =18 or a.entry_form =19 THEN a.quantity ELSE 0 END) AS knit_qnty_issue
	from order_wise_pro_details a, inv_transaction b, inv_issue_master c,product_details_master d where a.trans_id=b.id and b.mst_id=c.id and d.id=b.prod_id and a.prod_id=d.id and c.item_category in (2,3) and (a.entry_form =18 or a.entry_form =19) and (c.entry_form =18 or c.entry_form =19) and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1 $po_cond_for_in2 ");// and c.receive_basis<>9
	$issue_kniting_prod_arr=array();
	foreach($issueprodKnitDataArr as $row)
	{
	$issue_kniting_prod_arr[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("color_id")]]["knit_qnty_issue"]+=$row[csf("knit_qnty_issue")];
	$issue_kniting_prod_arr[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("color_id")]]["knit_qnty_issue_amt"]+=$row[csf("knit_qnty_issue")]*$row[csf("cons_rate")];
	}
	unset($issueprodKnitDataArr);
	
	if(empty($all_po_id))
	{
	echo '<div align="left" style="width:1000px;"><h1 align="center" style="color:#f00;">Order not found</h></div>'; die;
	}

	$btb_lc_data=sql_select("select b.id,b.btb_prefix,b.btb_prefix_number,b.lc_date,b.btb_system_id,b.bank_code,b.lc_year,b.lc_category,b.lc_serial,a.pi_number,b.id as pi_id from com_pi_master_details a ,com_btb_lc_master_details b where b.pi_id=a.id");

	foreach($btb_lc_data as $row){

		$pi_wise_btb_lc_arr[$row[csf("pi_id")]]['pi_number']=$row[csf("pi_number")];
		$pi_wise_btb_lc_arr[$row[csf("pi_id")]]['btb_lc_date']=$row[csf("lc_date")];
	}

	$modSql="select a.id,a.task_catagory,a.task_name,a.task_short_name,a.task_type,a.completion_percent,a.task_sequence_no,a.task_group,b.task_template_id,b.lead_time 
	from lib_tna_task a,tna_task_template_details b where a.task_name=b.tna_task_id and b.task_type in (1,6) $task_group_con and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 order by a.task_sequence_no asc";

	$mod_sql= sql_select($modSql);
	$tna_task_array=array();
	$tna_task_id=array();
	$tna_task_cat=array();
	$tna_task_name_arr=array();
	foreach ($mod_sql as $row)
	{	
		$tna_task_group_by_id[$row[csf("task_name")]]=$row[csf("task_group")];
		$tna_task_id[$row[csf("task_name")]]=$row[csf("task_name")];
		$tna_task_array[$row[csf("task_name")]] =$row[csf("task_short_name")];
		$tna_task_name_array[$row[csf("id")]] =$tna_task_name[$row[csf("task_name")]];
		$tna_task_cat[$row[csf("id")]]=$row[csf("task_catagory")];
		$tna_task_name_arr[$row[csf("id")]]=$row[csf("task_name")];
		$lead_time_array[$row[csf("task_template_id")]]=$row[csf("lead_time")];
		$tast_tmp_id_arr[$row[csf("task_template_id")]][$row[csf("tna_task_id")]]=$row[csf("tna_task_id")];
	}




	$po_no_arr_all=implode(",",$po_no_arr); if($po_no_arr_all!="") $po_no_arr_all .=",0"; else $po_no_arr_all .="0"; 
	$job_no_all="'".implode("','",$job_no_arr)."'";
	$c=count($tna_task_id);
	
	if($db_type==0)
	{
		$sql ="select a.APPROVED,a.READY_TO_APPROVED,a.po_number_id, a.job_no, a.shipment_date,max(b.pub_shipment_date) as pub_shipment_date,max(b.pub_shipment_date_prev) as pub_shipment_date_prev, a.template_id, a.po_receive_date,b.insert_date,";
		$i=1;
	
		foreach( $tna_task_id as $dval=>$id)    	
		{
			if ($i!=$c) $sql .="max(CASE WHEN CONCAT(a.task_number) = '".$id."' THEN concat(a.actual_start_date,'_',a.actual_finish_date,'_',a.task_start_date,'_',a.task_finish_date,'_',a.notice_date_start,'_',a.notice_date_end,'_',a.remarks,'_',a.id,'_',a.task_number,'_',a.plan_start_flag,'_',a.plan_finish_flag)  END ) as status$id, ";
			else $sql .="max(CASE WHEN CONCAT(a.task_number) = '".$id."' THEN concat(a.actual_start_date,'_',a.actual_finish_date,'_',a.task_start_date,'_',a.task_finish_date,'_',a.notice_date_start,'_',a.notice_date_end,'_',a.remarks,'_',a.id,'_',a.task_number,'_',a.plan_start_flag,'_',a.plan_finish_flag)  END ) as status$id ";
			$i++;
		}
		
		$sql .=" from tna_process_mst a, wo_po_break_down b where a.po_number_id=b.id and a.po_number_id in( $po_no_arr_all ) and a.job_no in ($job_no_all) $shipment_status_con and b.status_active=1  and b.po_quantity>0 $order_status_cond and a.task_type=1 group by a.APPROVED,a.READY_TO_APPROVED,a.po_number_id,a.job_no,b.insert_date order by a.shipment_date,a.po_number_id,a.job_no"; 
	}
	else
	{
		$sql ="select a.APPROVED,a.READY_TO_APPROVED,a.po_number_id, a.job_no, max(a.shipment_date) as shipment_date,max(b.pub_shipment_date) as pub_shipment_date,max(b.pub_shipment_date_prev) as pub_shipment_date_prev,a.template_id, max(a.po_receive_date) as po_receive_date,b.insert_date,";
		$i=1;
		
		foreach( $tna_task_id as $dval=>$id)    	
		{
			if ($i!=$c) $sql .="max(CASE WHEN a.task_number = '".$id."' THEN a.actual_start_date || '_' || a.actual_finish_date || '_' || a.task_start_date || '_' || a.task_finish_date ||'_' || a.notice_date_start || '_' || a.notice_date_end || '_' || a.remarks || '_' || a.id || '_' || a.task_number || '_' || a.plan_start_flag || '_' || a.plan_finish_flag  END ) as status$id, ";
			
			else $sql .="max(CASE WHEN a.task_number = '".$id."' THEN a.actual_start_date || '_' || a.actual_finish_date || '_' || a.task_start_date || '_' || a.task_finish_date || '_' || a.notice_date_start || '_' || a.notice_date_end || '_' || a.remarks || '_' || a.id || '_' || a.task_number || '_' || a.plan_start_flag || '_' || a.plan_finish_flag  END ) as status$id ";
			
			$i++;
		}
		//------------------
			$sql_order_con='';
			$po_no_arr_all=explode(',',$po_no_arr_all);
			$chunk_po_no_arr_all=array_chunk(array_unique($po_no_arr_all),999);
			$p=1;
			foreach($chunk_po_no_arr_all as $rlz_sub_id)
			{
				if($p==1) $sql_order_con .=" and (a.po_number_id in(".implode(',',$rlz_sub_id).")"; else $sql_sub_lc .=" or a.po_number_id in(".implode(',',$rlz_sub_id).")";
				$p++;
			}
			$sql_order_con .=" )";
			
			$sql_job_con='';
			$job_no_all=explode(',',$job_no_all);
			$chunk_job_no_all=array_chunk(array_unique($job_no_all),999);
			$q=1;
			foreach($chunk_job_no_all as $rlz_sub_id)
			{
				if($q==1) $sql_job_con .=" and (a.job_no in(".implode(',',$rlz_sub_id).")"; else $sql_sub_lc .=" or a.job_no in(".implode(',',$rlz_sub_id).")";
				$p++;
			}
			$sql_job_con .=" )";
			
			
		//-------------------------------
		$sql .=" from  tna_process_mst a, wo_po_break_down b where a.po_number_id=b.id $sql_order_con $sql_job_con $shipment_status_con and b.status_active=1 and b.po_quantity>0 $order_status_cond  and a.task_type in (1,6)  group by a.APPROVED,a.READY_TO_APPROVED,a.po_number_id,a.job_no,a.template_id,a.shipment_date,b.insert_date order by a.shipment_date,a.po_number_id,a.job_no"; 
		
		$sql2="select a.APPROVED,a.READY_TO_APPROVED,a.po_number_id, a.job_no,a.task_number, max(a.shipment_date) as shipment_date,max(b.pub_shipment_date) as pub_shipment_date,max(b.pub_shipment_date_prev) as pub_shipment_date_prev,a.template_id, max(a.po_receive_date) as po_receive_date,b.insert_date,max(a.actual_start_date) as actual_start_date,max(a.actual_finish_date) as actual_finish_date,max(a.task_start_date) as task_start_date,max(a.task_finish_date) as task_finish_date from  tna_process_mst a, wo_po_break_down b where a.po_number_id=b.id $sql_order_con $sql_job_con $shipment_status_con and b.status_active=1 and b.po_quantity>0 $order_status_cond  and a.task_type in (1,6)  group by a.APPROVED,a.READY_TO_APPROVED,a.task_number,a.po_number_id,a.job_no,a.template_id,a.shipment_date,b.insert_date order by a.shipment_date,a.po_number_id,a.job_no";
	}
	
	//  echo $sql;
	$data_sql= sql_select($sql2);
	
	
	$poArr=array();$templateArr=array();
	foreach ($data_sql as $row)
	{
		$poArr[$row[csf('po_number_id')]]=$row[csf('po_number_id')];
		$templateArr[$row[csf('template_id')]]=$row[csf('template_id')];
		$job_wise_tna_data_arr[$row[csf('job_no')]][$row[csf('task_number')]]['actual_start_date']=$row[csf('actual_start_date')];
		$job_wise_tna_data_arr[$row[csf('job_no')]][$row[csf('task_number')]]['actual_finish_date']=$row[csf('actual_finish_date')];
		$job_wise_tna_data_arr[$row[csf('job_no')]][$row[csf('task_number')]]['plan_start_date']=$row[csf('task_start_date')];
		$job_wise_tna_data_arr[$row[csf('job_no')]][$row[csf('task_number')]]['plan_end_date']=$row[csf('task_finish_date')];


	}
	 
	// echo "<pre>";
	// print_r($job_wise_tna_data_arr);
	
	//selected task id start--------------------------------------------
	
	
	$tna_process_task_sql = "SELECT a.TNA_TASK_ID as TASK_NUMBER FROM TNA_TASK_TEMPLATE_DETAILS a WHERE a.IS_DELETED=0 and a.STATUS_ACTIVE=1 and a.task_type in (1,6) ".where_con_using_array($templateArr,0,'a.TASK_TEMPLATE_ID')." GROUP BY a.TNA_TASK_ID";
	//echo $tna_process_task_sql;//die;
	$tna_process_task_sql_result = sql_select($tna_process_task_sql);
	$tna_process_task_arr=array();
	foreach( $tna_process_task_sql_result as  $row ) 
	{	
		$tna_process_task_arr[$row[TASK_NUMBER]]=$tna_task_array[$row[TASK_NUMBER]];
		$tna_process_task_id_arr[$row[TASK_NUMBER]]=$row[TASK_NUMBER];
		

	}	
	







	//for sequence................
	$tempTaskNameArr=array();$tempTaskIdArr=array();
	$trimstempTaskNameArr=array();
	$fabtempTaskNameArr=array();
	foreach($tna_task_array as $tid=>$tn){
		if($tna_process_task_arr[$tid]){
			$tempTaskNameArr[$tid]=$tna_process_task_arr[$tid];
			$tempTaskIdArr[$tid]=$tid;

			if( $tid==32 || $tid==71  || $tid==300  || $tid==8 || $tid==13 || $tid==29 || $tid==279  || $tid==308 || $tid==11 || $tid==307 || $tid==278  || $tid==310 || $tid==301 || $tid==309 || $tid==12 || $tid==24){
				$trimstempTaskNameArr[$tid]=$tna_process_task_arr[$tid];
				 $trims_tna[$tid]=$tid;
			}
			if($tid==31 || $tid==73 || $tid==277 || $tid==29    || $tid==8 || $tid==13 || $tid==305 || $tid==306 || $tid==10 || $tid==12 || $tid==24 || $tid==276 || $tid==302 ){

				$fabtempTaskNameArr[$tid]=$tna_process_task_arr[$tid];
				 $fabric_tna[$tid]=$tid;
			}
			
			
		}
	}

	
	
	$tna_task_array=array();
	$tna_trims_task_array=array();
	$tna_fab_task_array=array();
	$tna_task_id=array();
	$tna_task_array=$tempTaskNameArr;
	$tna_trims_task_array=$trimstempTaskNameArr;
	$tna_fab_task_array=$fabtempTaskNameArr;
	$tna_task_id=$tempTaskIdArr;
	//............................end




	$tbl_width2=3300+count($fabric_tna)*100*3;


	$tbl_width=3370+count($trims_tna)*100*3;
	$tbl_width3=3350+count($trims_tna)*100*3;

	// echo count($tna_task_array)*100*3;
	$tna_all_task=implode(",",$tna_task_id);




	//=======================================================================================================================
	$btb_data_array=sql_select("SELECT a.id,a.btb_prefix_number, a.btb_system_id, a.lc_number, a.supplier_id, a.application_date, a.last_shipment_date, a.lc_date, a.lc_value, 
	c.item_category_id, a.importer_id ,a.btb_prefix,a.btb_prefix_number,a.bank_code,a.lc_year,a.lc_category,a.lc_serial,a.issuing_bank_id,b.pi_id,c.pi_number
	FROM com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_master_details c 	WHERE a.id=b.com_btb_lc_master_details_id and b.pi_id=c.id  and a.is_deleted = 0 and a.item_basis_id=1 
	group by a.id, a.insert_date, a.btb_prefix_number, a.btb_system_id, a.lc_number, a.supplier_id, a.application_date,  a.last_shipment_date, a.lc_date, a.lc_value, c.item_category_id, a.importer_id, a.ref_closing_status,a.btb_prefix ,a.btb_prefix_number,a.bank_code,a.lc_year,a.lc_category,a.lc_serial,a.issuing_bank_id,b.pi_id,c.pi_number");

	foreach ($btb_data_array as $row)
	{
		$pi_wise_btb_arr[$row[csf('pi_number')]]['btb_lc_no']=$row[csf('bank_code')].$row[csf('lc_year')].$row[csf('lc_category')].$row[csf('lc_serial')];
		$pi_wise_btb_arr[$row[csf('pi_number')]]['btb_lc_date']=$row[csf('lc_date')];
		$pi_wise_btb_arr[$row[csf('pi_number')]]['supplier_id']=$lib_supplier_arr[$row[csf('supplier_id')]];
	}

	ob_start();
	?>
     
    <div style="width:100%">
        <table width="<? echo $tbl_width2;?>">
            <tr>
                <td align="center" width="100%" colspan="20" class="form_caption"><? echo $report_title.'<br>'.$company_library[str_replace("'","",$company_name)].'<br/>';
				if(str_replace("'","",$txt_date_from)!="") echo  str_replace("'","",$txt_date_from).' To '.str_replace("'","",$txt_date_to);
				?></td>
            </tr>
			<tr>
				<td align="center" width="100%" colspan="20" class="form_caption">
					<table style="margin-left:2400px; margin-top:5px" id="table_notes">
					<tr>
						<td bgcolor="yellow" height="15" width="30"></td>
						<td> WO Qty Fully or Partial Pending with Req Qty</td>
							<td bgcolor="green" height="15" width="30">&nbsp;</td>
							<td>WO Qty equal with Req Qty </td>
							<td bgcolor="red" height="15" width="30"></td>
							<td>WO Qty greater than Req Qty</td>
						</tr>
						<tr>
							<td colspan="6" align="center">
								(All WO Qty will calculate with Conversion Factor)
							</td>
						</tr>
					</table>
				</td>
			</tr>
        </table>
	
        <table width="<? echo $tbl_width2+20;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1">
        	<caption><b style="float:left"> Fabric</b></caption>
            <thead>
				<tr style="font-size:13px">
                    <th width="100" colspan="38">&nbsp;</th>		
					
					<?
					$i=0;

					foreach($tna_fab_task_array as $task_name=>$key)
					{
						$i++;

						if($task_name==31 || $task_name==73 || $task_name==277 || $task_name==29 || $task_name==8 || $task_name==13  || $task_name==305 || $task_name==306 || $task_name==10 || $task_name==12 || $task_name==24 || $task_name==276 || $task_name==302){


						if(count($tna_task_array)==$i){ echo '<th width="100" colspan="3" title="'.$task_name.'='.$tna_task_name[$task_name].'">'. $key." <br><small>".$tna_task_group_by_id[$task_name]."</small> </th>"; }else{ echo '<th width="100" colspan="3" title="'.$task_name.'='.$tna_task_name[$task_name].'">'.$key." <br><small>".$tna_task_group_by_id[$task_name]."</small> </th>";

						// if(count($tna_fab_task_array)==$i){ echo '<th width="100" colspan="3" title="'.$task_name.'='.$tna_task_name[$task_name].'">'. $key." <br><small>".$tna_task_group_by_id[$task_name]."</small> </th>"; }else{ echo '<th width="100" colspan="3" title="'.$task_name.'='.$tna_task_name[$task_name].'">'.$key." <br><small>".$tna_task_group_by_id[$task_name]."</small> </th>";


						}
					   }
					}
					?>
                </tr>                    
                <tr style="font-size:13px">
                   <th width="30">SL</th>
                   <th width="110">Buyer</th>
				   <th width="100">Season</th>
                   <th width="100">Job No</th>
                   <th width="100">Style Ref</th>
                   <th width="70">Internal Ref.</th>
                   <th width="70">File No</th>
                   <th width="100">Order No</th>
                   <th width="70">Style Qty</th>
                   <th width="40">UOM</th>                        
                   <th width="70">Qty (Pcs)</th>                        
                   <th width="70">Shipment Date</th>
                   <th width="120">Body Part</th>
                   <th width="120">Fabric Construction</th>
                   <th width="150">Fabric Compostion</th>
                   <th width="70">Color Type</th>                        
                   <th width="70">Fabric Weight</th>
                   <th width="70">Width</th>
                   <th width="100">Gmt. Color</th>
                   <th width="100">Fabric Color</th>
				   <th width="100">Material Source</th>
				   <th width="100">Avg. Cons</th>
                   
                   <th width="80">Req Qty</th>  
				   <th width="50" title="">Fabric UOM</th>
				   <th width="80">Pre Costing Value</th>                        
				   <th width="80">WO Qty</th>                        
				   <th width="80">WO Value (USD)</th>
                    
                   <th width="120">Supplier</th> 
                   <th width="100">PI No.</th>
				   <th width="100">BTB LC No</th>
				   <th width="100">BTB LC Date</th>
                   <th width="80">In-House Qty</th>
                   <th width="80">In-House Amount</th>  
                   <th width="80">Receive Balance</th>
                   <th width="80">Issue to Cutting</th>
                   <th width="80">Issue Amount</th>
                   <th width="80">Left Over / Balance</th>
                   <th width="100">Left Over / Balance Amount</th>

				   <?php
				   	foreach($fabric_tna as $vid=>$key)
					{
						if($key==31 || $key==73 || $key==277 || $key==29  || $key==8 || $key==13 || $key==305 || $key==306 || $key==10 || $key==12 || $key==24 || $key==276 || $key==302)
						{
							$i++;?>

							<th width="100">Plan End Date</th>
							<th width="100">Actual End Date</th>
							<th width="100">Delay/ Early</th>
				   			<?
						}
					}
					?>
                </tr>
            </thead>
       	</table>

        <div style="width:<? echo $tbl_width2+40;?>px; overflow-y:scroll; max-height:300px;font-size:12px; overflow-x:hidden;" id="scroll_body">
	        <table width="<? echo $tbl_width2+20;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body2">
	            <?  				
				$job_wise_arr=array();$body_wise_arr=array(); $jobPoArr=array();
				foreach($fabric_wise_arr as $job_no=>$job_data)
				{
					$job_wise_row=0;
					foreach($job_data as $bprat_id=>$body_data)
					{
						$body_wise_row=0;
						foreach($body_data as $deter_id=>$deter_data)
						{
							$deter_wise_row=0;
							foreach($deter_data as $fabric_color=>$source_data)
							{
								foreach($source_data as $source_id=>$val)
								{
									$job_wise_row++;$body_wise_row++;$deter_wise_row++;
									//$jobPoArr[$job_no].=$val["po_number"].'**';
									$exPoNos=array_filter(array_unique(explode("**",$val["po_number"])));
									foreach($exPoNos as $pono)
									{
										$jobPoArr[$job_no][$pono]=$pono;
									}
								}
							}
							$job_wise_arr[$job_no]=$job_wise_row;
							
							$body_wise_arr[$job_no][$bprat_id]=$body_wise_row;
							$fab_wise_arr[$job_no][$bprat_id][$deter_id]=$deter_wise_row;
						}
					}
				}
				//print_r($job_wise_arr);

				$i=1;$total_po_qnty=$total_po_qnty_pcs=$total_fabric_req=$total_fabric_req_cost=$total_fabric_req=$total_fabric_amount=$total_kniting_prod_recv_amt=$total_kniting_prod_recv=$total_fab_recv_balance=$total_kniting_prod_issue=$total_kniting_prod_issue_amt=$total_left_over_bal=$total_left_over_bal_amount=0;
				
				foreach($fabric_wise_arr as $job_no=>$job_data)
				{
					$j=1;
					foreach($job_data as $bprat_id=>$body_data)
					{
						$b=1;
					  	foreach($body_data as $deter_id=>$deter_data)
					  	{
							$f=1;
							foreach($deter_data as $fabric_color=>$source_data)
							{
								foreach($source_data as $source_id=>$val)
								{
									$ratio=$val["ratio"];
									$po_id=rtrim($val["po_id"],',');
									$po_ids=array_unique(explode(",",$po_id));
									$gmt_color_arr=array_unique(explode(",",chop($val["gmt_color"],',')));
									// echo '<pre>';print_r($gmt_color_arr);
									$sensitive=$val["sensitive"];
									
									$fab_color=$lib_color_arr[$fabric_color];
									
									$booking_req=$booking_amount=$kniting_prod_recv=$kniting_prod_recv_amt=$kniting_prod_issue=$kniting_prod_issue_amt=0; $bookingNo=""; $supplier_comp="";
									
									//foreach ($gmt_color_arr as $key => $gmt_color) 
									//{
										// echo $gmt_color.'<br>';
										foreach($po_ids as $pId)
										{
											// echo $gmt_color.'<br>';
											// echo $pId.'<br>';
											/*$fabric_req_knit=array_sum($fabric_req_arr['knit']['grey'][$pId][$bprat_id][$deter_id][$fabric_color]);
											$fabric_req_wov=array_sum($fabric_req_arr['woven']['grey'][$pId][$bprat_id][$deter_id][$fabric_color]);

											$tot_fabric_req+=$fabric_req_knit+$fabric_req_wov;
											$fabric_req_knit_cost=array_sum($fabric_req_cost_arr['knit']['grey'][$pId][$bprat_id][$deter_id][$fabric_color]);
											$fabric_req_wov_cost=array_sum($fabric_req_cost_arr['woven']['grey'][$pId][$bprat_id][$deter_id][$fabric_color]);

											$tot_fabric_req_cost+=$fabric_req_wov_cost+$fabric_req_knit_cost;*/
											
											/*$booking_req+=$booking_req_arr[$pId][$bprat_id][$deter_id][$gmt_color]['grey'];
											$booking_amount+=$booking_req_arr[$pId][$bprat_id][$deter_id][$gmt_color]['amount'];*/

											$booking_req+=$booking_req_arr[$pId][$bprat_id][$deter_id][$fabric_color]['grey'];
											$booking_amount+=$booking_req_arr[$pId][$bprat_id][$deter_id][$fabric_color]['amount'];

											$booking_no=rtrim($booking_supp_arr[$pId][$bprat_id][$deter_id][$fabric_color]['booking_no'],',');
											$bookingNo.=$booking_no.",";
											$supp_comp= rtrim($booking_supp_arr[$pId][$bprat_id][$deter_id][$fabric_color]['supp_com'],',');
											$supplier_comp.=$supp_comp.",";
										 
										//$all_gmt_color .= $lib_color_arr[$gmt_color] . ", ";
										
										
										$kniting_prod_recv+=$kniting_prod_arr[$pId][$bprat_id][$deter_id][$fabric_color]["knit_qnty_rec"];
										$kniting_prod_recv_amt+=$kniting_prod_arr[$pId][$bprat_id][$deter_id][$fabric_color]["knit_qnty_rec_amt"];
										$kniting_prod_issue+=$issue_kniting_prod_arr[$pId][$bprat_id][$deter_id][$fabric_color]["knit_qnty_issue"];
										$kniting_prod_issue_amt+=$issue_kniting_prod_arr[$pId][$bprat_id][$deter_id][$fabric_color]["knit_qnty_issue_amt"];
									}
									$tot_fabric_req=$val["pre_fab_req_qty"];
									$tot_fabric_req_cost=$val["pre_fab_req_amt"];

									$all_gmt_color="";
									foreach ($gmt_color_arr as $key => $gmt_color) 
									{
										$all_gmt_color .= $lib_color_arr[$gmt_color] . ", ";
									}
									$all_gmt_color = rtrim($all_gmt_color, ", ");

									$booking_no=rtrim($bookingNo,',');
									$supp_com=rtrim($supplier_comp,',');
									$supp_coms=implode(",",array_unique(explode(",",$supp_com)));
									
									$pub_shipment_date=rtrim($val["pub_shipment_date"],',');
									$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
									$min_pub_shipment_date=min($pub_shipment_date);
									
									$total_sew_input=$sew_input_qnty_in+$sew_input_qnty_out;
									
									
									$booking_nos=array_unique(explode(",",$booking_no));
									$pi_nos="";
									foreach($booking_nos as $bNo)
									{
										$pi_no=rtrim($piDataArr[$bNo],',');
										$pi_nos.=$pi_no.',';
									}
									$pi_nos=rtrim($pi_nos,',');
									$all_pi_no=implode(", ",array_unique(explode(",",$pi_nos)));
									//$job_wise_arr[$job_no]
									?>
							 		<tr bgcolor = "<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="font-size:13px">
					                    <?
					                    if($j==1)
										{
											//$tot_po_plan_qnty=$val["plan_cut"];//$val["po_qnty"];
											//$tot_po_qnty=$val["po_qnty"];
											//$po_qnty_pcs=$val["po_qnty_pcs"];
											$tot_po_qnty=$job_qty_wise_arr[$job_no]["po_qnty"];;
											$po_qnty_pcs=$job_qty_wise_arr[$job_no]["po_qnty_pcs"];
											$sensitive=$val["sensitive"];
											//$plan_cut_qnty=$val["plan_cut_qnty"];	 
											$total_po_qnty+=$tot_po_qnty;
											$total_po_qnty_pcs+=$po_qnty_pcs;
											?>
						                    <td width="30" valign="middle" rowspan="<? echo $job_wise_arr[$job_no]; ?>"><? echo $i; ?></td>
						                    <td width="110" valign="middle" rowspan="<? echo $job_wise_arr[$job_no]; ?>"><? echo $buyer_short_name_library[$val["buyer_name"]]; ?></td>
											<td width="100" valign="middle" rowspan="<? echo $job_wise_arr[$job_no]; ?>"><? echo $lib_season_arr[$val["season_buyer_wise"]]; ?></td>
						                    <td width="100" valign="middle" align="center" rowspan="<? echo $job_wise_arr[$job_no]; ?>"><? echo $val["job_no_prefix_num"]; ?></td>
						                    <td width="100" valign="middle" rowspan="<? echo $job_wise_arr[$job_no]; ?>" align="center"><div style="word-wrap:break-word:100px; word-break:break-all;"><? echo $val["style_ref_no"]; ?></div></td>
						                    <td width="70" valign="middle" rowspan="<? echo $job_wise_arr[$job_no]; ?>" align="center"> <div style="word-wrap:break-word:70px; word-break:break-all;"><?  $ref_no=rtrim($val["ref_no"],",");echo implode(', ',array_unique(explode(",",$ref_no)));?></div></td>
						                    <td width="70" valign="middle" rowspan="<? echo $job_wise_arr[$job_no]; ?>" align="center" bgcolor="#FFFFCC"><div style="word-wrap:break-word:70px;"><?  $file_no=rtrim($val["file_no"],",");$file_nos=implode(', ',array_unique(explode(",",$file_no))); echo $file_nos;//number_format($tot_po_qnty); ?></div></td>
						                    <td align="center" valign="middle"  rowspan="<? echo $job_wise_arr[$job_no]; ?>" width="100"><div style="word-wrap:break-word:100px; word-break:break-all;"><? $po_no=implode(",",array_unique($jobPoArr[$job_no])); $po_nos=implode(",",array_unique($jobPoArr[$job_no])); echo $po_nos; ?></div></td>
						                    <td align="right" valign="middle" rowspan="<? echo $job_wise_arr[$job_no]; ?>" width="70"><? echo number_format($tot_po_qnty,0); ?></td>
						                    <td align="center" valign="middle"  rowspan="<? echo $job_wise_arr[$job_no]; ?>" width="40" title=""><? echo $unit_of_measurement[$val["order_uom"]]; ?></td>
						                    <td align="right" valign="middle" rowspan="<? echo $job_wise_arr[$job_no]; ?>" width="70"> <? echo number_format($po_qnty_pcs,0); ?></td>
						                    <td align="center" valign="middle" rowspan="<? echo $job_wise_arr[$job_no]; ?>" width="70"><? echo change_date_format($min_pub_shipment_date); ?></td>
						                    <?
											//$body_wise_arr[$job_no][$bprat_id]
										}
										if($b==1)
										{
											//$fab_wise_arr[$job_no][$bprat_id][$deter_id]
											?>
						                    <td align="center" rowspan="<? echo $body_wise_arr[$job_no][$bprat_id]; ?>" width="120"><div style="word-wrap:break-word:118px;"><? echo $body_part[$bprat_id]; ?></div></td>
						                   	<?
					                    }
										if($f==1)
										{
											?>
						                    <td align="center" width="120" title="DeterId=<? echo $deter_id;?>" rowspan="<? echo $fab_wise_arr[$job_no][$bprat_id][$deter_id]; ?>"><div style="word-wrap:break-word:120px;"><? echo $val["construction"]; ?></div></td>
						                    <td align="center" width="150" rowspan="<? echo $fab_wise_arr[$job_no][$bprat_id][$deter_id]; ?>" style=""><div style="word-wrap:break-word:150px;"><? echo $val["composition"]; ?></div></td>
						                    <td align="center" width="70" rowspan="<? echo $fab_wise_arr[$job_no][$bprat_id][$deter_id]; ?>" style="word-break:break-all" title=""><div style="word-wrap:break-word:70px;"><? echo $val["color_type_id"]; ?></div></td>
						                    <td align="center" width="70" rowspan="<? echo $fab_wise_arr[$job_no][$bprat_id][$deter_id]; ?>"><? echo $val["gsm_weight"]; ?></td>
						                    <td align="center" width="70" rowspan="<? echo $fab_wise_arr[$job_no][$bprat_id][$deter_id]; ?>" style=""><div style="word-wrap:break-word:70px;"><? echo $val["width_dia_type"]; ?></div></td>
						                    <?
					                    }?>

					                    <td align="center" width="100" style="word-break:break-all" title="Gmts Color=<? echo $all_gmt_color;?>"><div style="word-wrap:break-word:100px;"><? echo $all_gmt_color; ?></div></td>
					                    <td align="center" width="100" style="word-break:break-all" title="sensitive=<? echo $sensitive; ?>"><div style="word-wrap:break-word:100px;"><? echo $fab_color; ?></div></td>

										<td align="center" width="100" style="word-break:break-all" title="sensitive=<? echo $sensitive; ?>"><div style="word-wrap:break-word:100px;"><? echo $commission_particulars[$val["source_id"]]; ?></div></td>
										<td align="center" width="100" style="word-break:break-all" title="sensitive=<? echo $sensitive; ?>"><div style="word-wrap:break-word:100px;"><? echo $val["avg_cons"]; ?></div></td>
					                    <td align="right" width="80" title="PreCost GreyQty"><? echo number_format($tot_fabric_req,2); ?></td>
					                    <td align="right" width="50" title=""><? echo $unit_of_measurement[$val["fab_uom"]]; ?></td>
					                    <td align="right" width="80" bgcolor="#FFFFCC"><? echo number_format($tot_fabric_req_cost,2); ?></td>

					                    <td align="right" width="80" title="Grey Qty"><a href="javascript:open_wo_popup('<? echo implode(',',array_unique(explode(",",$po_id)));?>','<? echo $bprat_id;?>','<? echo $deter_id;?>','<? echo chop($val["gmt_color"],',');?>','<? echo $job_no;?>','WO Info','wo_popup')"><? echo number_format($booking_req,2); ?></a>
					                    </td>

					                    <td align="right" width="80" title=""><?  echo number_format($booking_amount,2); ?></td>
										<?php
										$btb_lc_no="";$btb_lc_date="";$supplier_id="";
										foreach(array_unique(explode(",",$pi_nos)) as $rows)
										{
											$btb_lc_no=$pi_wise_btb_arr[$rows]['btb_lc_no'];
											$btb_lc_date=$pi_wise_btb_arr[$rows]['btb_lc_date']; 
											$supplier_id=$pi_wise_btb_arr[$rows]['supplier_id']; 
										}?>
					                    <td align="center" width="120" style=""  title="BookingNo=<? echo implode(",",array_unique(explode(",",$booking_no)));;?>"><div style="word-wrap:break-word:120px;"><? 
										echo $supplier_id;//echo $supp_coms; 
										//$supp_coms=implode(",",array_unique(explode(",",$val['nominated_supp'])));echo $supp_coms; 
										?></div></td>
					                    <td align="center" width="100" title="PI No"><div style="word-wrap:break-word:100px;"><? echo $all_pi_no; ?></div></td>
										<td align="center" width="100" ><div style="word-wrap:break-word:100px;"><? 
										// pi_wise_btb_arr
										echo $btb_lc_no; 
										?></div></td>
										<td align="center" width="100"><div style="word-wrap:break-word:100px;"><?	 echo $btb_lc_date; ?></div></td>
					                    <td align="right" width="80" title="Woven Fin Recv"><a href="javascript:open_wo_popup('<? echo implode(',',array_unique(explode(",",$po_id)));?>','<? echo $bprat_id;?>','<? echo $deter_id;?>','<? echo $fabric_color;?>','<? echo $job_no;?>','Fin Recv Info','fin_recv_popup')"><? echo number_format($kniting_prod_recv,2); ?></a></td>
					                    <td align="right" width="80" title="Gmt Print Recv"><? echo number_format($kniting_prod_recv_amt,2); ?></td>
					                    <td align="right" width="80" title="Wo Qty-Fin Recv Qty"><?  $recv_balance=$booking_req-$kniting_prod_recv;echo number_format($recv_balance,2);?></td>
					                    <td align="right" width="80" title="Fin Issue"><a href="javascript:open_wo_popup('<? echo implode(',',array_unique(explode(",",$po_id)));?>','<? echo $bprat_id;?>','<? echo $deter_id;?>','<? echo $fabric_color;?>','<? echo $job_no;?>','Fin Issue Info','fin_issue_popup')"><?   echo number_format($kniting_prod_issue,2); ?></a></td>
					                    <td align="right" width="80"><? echo number_format($kniting_prod_issue_amt,2); ?></td>
					                    <td align="right" width="80" title="Recv-Issue"><? $left_over_bal=$kniting_prod_recv-$kniting_prod_issue;echo number_format($left_over_bal,2);//sew_reject_qnty ?></td>
					                    <td align="right" width="100" title="Amount Recv-Issue"><? $left_over_bal_amount=$kniting_prod_recv_amt-$kniting_prod_issue_amt;
					                    echo number_format($left_over_bal_amount,2); ?></td>

										<?php
										$tast_id_arr=array_unique(explode(',',$tast_tmp_id_arr[$row[csf('template_id')]]));
										//  echo "<pre>";
										//  print_r($tna_task_id);
									  	foreach($fabric_tna as $vid=>$key)
										{
											if($key==31 || $key==73 || $key==277 || $key==29 || $key==8 || $key==13 || $key==305 || $key==306 || $key==10 || $key==12 || $key==24 || $key==276 || $key==302 ){
											// $job_wise_tna_data_arr[$job_no][$key]['plan_end_date']
											$plan_date=$job_wise_tna_data_arr[$job_no][$key]['plan_end_date'];
											$actual_date=$job_wise_tna_data_arr[$job_no][$key]['actual_finish_date'];
											
											if(strtotime($actual_date)>0){
												$diff = abs(strtotime($plan_date) - strtotime($actual_date));
												$delay = floor($diff / (60*60*24));	
												}else{
													$delay="";
												}		
												
											 	if($key==306 || $key==277 || $key==302 || $key==305 || $key==276){
													if($val["source_id"]==1 && ($key==276 || $key==277 || $key==302)){
														$actual_finish_date=$job_wise_tna_data_arr[$job_no][$key]['actual_finish_date'];
														$plan_end_date=$job_wise_tna_data_arr[$job_no][$key]['plan_end_date'];
													}
													else if($val["source_id"]==2 && ($key==305 || $key==306)){
														$actual_finish_date=$job_wise_tna_data_arr[$job_no][$key]['actual_finish_date'];
														$plan_end_date=$job_wise_tna_data_arr[$job_no][$key]['plan_end_date'];
													}
													else{
														$actual_finish_date='';
														$plan_end_date='';
														$delay="";
													}
												}
												else{
													$actual_finish_date=$job_wise_tna_data_arr[$job_no][$key]['actual_finish_date'];
													$plan_end_date=$job_wise_tna_data_arr[$job_no][$key]['plan_end_date'];
												}
											
											
											 ?>
												<td width="100" align="right" id=""><?= $plan_end_date;?></td>
												<td width="100" align="right" id=""><?= $actual_finish_date;?></td>
												<td width="100" align="right" id=""><? 	echo $delay;?></td>
											<? }
										} 
										?>
									</tr>
									<?
									
									$total_fabric_req+=$tot_fabric_req;
									$total_fabric_req_cost+=$tot_fabric_req_cost;
									$total_booking_req+=$booking_req;
									$total_fabric_amount+=$booking_amount;	
									$total_kniting_prod_recv+=$kniting_prod_recv;
									$total_kniting_prod_recv_amt+=$kniting_prod_recv_amt;
									$total_fab_recv_balance+=$recv_balance;
									
									$total_kniting_prod_issue+=$kniting_prod_issue;
									$total_kniting_prod_issue_amt+=$kniting_prod_issue_amt;
									$total_left_over_bal+=$left_over_bal;
									$total_left_over_bal_amount+=$left_over_bal_amount;
									
									$i++;$j++;$b++;$f++;									
								}
							}
						}
					}
				}
				?>
	        </table>
        </div>

        <table width="<? echo $tbl_width2+20;?>" class="tbl_bottom" cellpadding="0" cellspacing="0" border="1" rules="all">            
            <tr style="font-size:13px">
                <td width="30">&nbsp;</td>
                <td width="110">&nbsp;</td>
				<td width="100">&nbsp;</td>
                <td width="100">&nbsp;</td>   
                <td width="100">&nbsp;</td>
                <td width="70">&nbsp;</td> 
                <td width="70" align="right" id="" bgcolor="#FFFFCC">&nbsp;<? //echo number_format($tot_order_qty); ?></td>
                <td width="100" align="right" id="">&nbsp;<? //echo number_format($tot_yarn_req_qty,2); ?></td>
                <td width="70" align="right" id="td_order_qty">&nbsp;<? //echo number_format($total_po_qnty,0); ?></td>
                <td width="40" align="right" id="">&nbsp;<? //echo number_format($tot_po_qnty_pcs,2); ?></td>
                
                <td width="70" align="right" id="td_order_qty_pcs">&nbsp;<? //echo number_format($total_po_qnty_pcs,0); ?></td>
                <td width="70" align="right" id="">&nbsp;<? //echo number_format($tot_knit_prod_qty,2); ?></td>
                
                <td width="120" align="right" id="">&nbsp;<? //echo number_format($total_grey_rec_qty,2); ?></td>
                <td width="120" align="right" id="">&nbsp;<? //echo number_format($total_under_over_prod,2); ?></td>
                <td width="150" align="right" id="">&nbsp;<? //echo number_format($total_knit_issuedToDyeQnty,2); ?></td>
                <td width="70" align="right" id="">&nbsp;<? //echo number_format($total_knit_left_over,2); ?></td>
                <td width="70" align="right" id="">&nbsp;<? //echo number_format($tot_fin_req_qty,2); ?></td>
                <td width="70" align="right" id="">&nbsp;<? //echo number_format($tot_total_finishing_prod,2); ?></td>
                
                <td width="100" align="right" id="">&nbsp;<? //echo number_format($total_fin_fab_recv,2); ?></td>
				<td width="100" align="right" id="">&nbsp;<? //echo number_format($total_fin_fab_recv,2); ?></td>
				<td width="100" align="right" id="">&nbsp;<? //echo number_format($total_fin_fab_recv,2); ?></td>
                <td width="100" align="right" id="">Total :</td>
                
                <td width="80" align="right" id=""><? echo number_format($total_fabric_req,2); ?></td>
                <td width="50" align="right" id="">&nbsp;<? //echo number_format($tot_finish_left_over,2); ?></td> 
                <td width="80" align="right" id="" bgcolor="#FFFFCC"><? echo number_format($total_fabric_req_cost,2); ?></td>
                <td width="80" align="right" id=""><? echo number_format($total_booking_req,2); ?></td>
                <td width="80" align="right" id=""><? echo number_format($total_fabric_amount,2); ?></td>
                
                <td width="120" align="right" id="">&nbsp;<? // echo number_format($tot_cutting_excess_lessQty); ?></td>
                <td width="100" align="right" id="">&nbsp;<? //echo number_format($tot_total_print_issued); ?></td>
				<td width="100" align="right" id="">&nbsp;<? // echo number_format($tot_cutting_excess_lessQty); ?></td>
                <td width="100" align="right" id="">&nbsp;<? //echo number_format($tot_total_print_issued); ?></td>
                <td width="80" align="right" id=""><? echo number_format($total_kniting_prod_recv,2); ?></td>
                <td width="80" align="right" id=""><? echo number_format($total_kniting_prod_recv_amt,2); ?></td>
                <td width="80" align="right" id=""><? echo number_format($total_fab_recv_balance,2); ?></td>
                
                <td width="80" align="right" id=""><? echo number_format($total_kniting_prod_issue,2); ?></td>
                <td width="80" align="right" id=""><? echo number_format($total_kniting_prod_issue_amt,2); ?></td>
                <td width="80" align="right" id=""><? echo number_format($total_left_over_bal,2); ?></td>
                <td align="right" width="100" id=""><? echo number_format($total_left_over_bal_amount,2); ?></td>

				<?
				foreach($fabric_tna as $vid=>$key)	
				{

					if($key==31 || $key==73 || $key==277 || $key==29 || $key==8 || $key==13 || $key==305 || $key==306 || $key==10 || $key==12 || $key==24 || $key==276  || $key==302 )
				 	{?>

					<td width="100" align="right" id="">&nbsp;</td>
					<td width="100" align="right" id="">&nbsp;</td>
					<td width="100" align="right" id="">&nbsp;</td>
					<? 
					}
				}?>
            </tr>
       	</table>
       	<?
		//Both part End
	   	?>
    </div>
    <br>
    <?
		
    if(str_replace("'","",$cbo_search_by)==1) //Order Wise
	{
		if($template==1)
		{
			
			?>
			<div  width="<?=$tbl_width;?>" >
			<table width="<?=$tbl_width;?>" >
                <tr class="form_caption"><td colspan="33" align="center"><? //echo $report_title; ?></td></tr>
                <tr class="form_caption"><td colspan="33" align="center"><? //echo $company_library[$company_name]; ?></td></tr>
			</table>
			

			<table class="rpt_table" width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
             <caption> <b style="float:left">Accessories</b></caption>
			 <thead>
			 <tr>
					<th width="100" colspan="38">&nbsp;</th>     
					
					<?
					foreach($tna_trims_task_array as $task_name=>$key)
					{
						$i++;

						if( $task_name==32 || $task_name==71   || $task_name==300  || $task_name==8 || $task_name==13 || $task_name==29 || $task_name==279  || $task_name==308 || $task_name==11 || $task_name==307 || $task_name==278  || $task_name==310 || $task_name==301 || $task_name==309 || $task_name==12 || $task_name==24){

						if(count($tna_task_array)==$i){ echo '<th width="100" colspan="3" title="'.$task_name.'='.$tna_task_name[$task_name].'">'. $key." <br><small>".$tna_task_group_by_id[$task_name]."</small> </th>"; }else{ echo '<th width="100" colspan="3" title="'.$task_name.'='.$tna_task_name[$task_name].'">'.$key." <br><small>".$tna_task_group_by_id[$task_name]."</small> </th>";

						}}
						
					}?>


				</tr>
                <tr>
                    <th width="30">SL</th>
                    <th width="50">Buyer</th>
                    <th width="50">Season</th>
                    <th width="100">Job No</th>
                    <th width="100">Style Ref</th>
                    <th width="100">Internal Ref</th>
                    <th width="100">File No</th>
                    <th width="90">Order No</th>
                    <th width="80">Order Qnty</th>
                    <th width="50">UOM</th>
                    <th width="80">Qty (Pcs)</th>
                    <th width="80">Shipment Date</th>
                    <th width="100">Trims Name</th>
                    <th width="140">Item Description</th>
                    <th width="100">Remark</th>
                    <th width="100">Brand/Sup Ref</th>
					<th width="100">Material Source</th>
                    <th width="60">Appr Req.</th>
                    <th width="80">Approve Status</th>
                    <th width="100">Item Entry Date</th>
                    <th width="80">Avg. Cons</th>
                    <th width="100">Req. Qty</th>
                    <th width="100">Pre Costing Value</th>
                    <th width="90">WO Qty</th>
                    <th width="60">Trims UOM</th>
                    <th width="100">WO Value (USD)</th>
                    <th width="150">Supplier</th>
                    <th width="70">WO Delay Days</th>
                    <th width="90">PI No</th>
					<th width="100">BTB LC No</th>
					<th width="100">BTB LC Date</th>

                    <th width="90">In-House Qty</th>
                    <th width="90">In-House Value</th>
                    <th width="90">Receive Balance</th>
                    <th width="90">Issue to Prod.</th>
                    <th width="90">Issue Value</th>
                    <th width="90">Left Over/ Balance (Qty)</th>
                    <th width="100">Left Over/ Balance (value)</th>
					<?php
					 foreach($trims_tna as $vid=>$key)
					 {
					  if( $key==32 || $key==71   || $key==300  || $key==8 || $key==13 || $key==29 || $key==279  || $key==308 || $key==11 || $key==307 || $key==278  || $key==310 || $key==301 || $key==309 || $key==12 || $key==24){?>


					<th width="100">Plan End Date</th>
					<th width="100">Actual End Date</th>
					<th width="100">Delay/ Early</th>
					<?}}?>

				


				</tr>
		</thead>
			</table>
			<div style="width:<?=$tbl_width+20;?>px; max-height:400px; overflow-y:scroll" id="scroll_body">
			<table class="rpt_table" width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
			<? 
			$conversion_factor_array=array();$item_arr=array();
			$conversion_factor=sql_select("select id,trim_uom,order_uom,conversion_factor from lib_item_group where status_active=1  ");
			foreach($conversion_factor as $row_f)
			{
				$conversion_factor_array[$row_f[csf('id')]]['con_factor']=$row_f[csf('conversion_factor')];
				$conversion_factor_array[$row_f[csf('id')]]['cons_uom']=$row_f[csf('trim_uom')];
				$item_arr[$row_f[csf('id')]]['order_uom']=$row_f[csf('order_uom')];
			}
			unset($conversion_factor);
			$app_sql=sql_select("select job_no_mst,accessories_type_id,approval_status from wo_po_trims_approval_info");
			$app_status_arr=array();
			foreach($app_sql as $row)
			{
				$app_status_arr[$row[csf("job_no_mst")]][$row[csf("accessories_type_id")]]=$row[csf("approval_status")];
			}
			unset($app_sql);

			$sql_po_qty_country_wise_arr=array();
			$po_job_arr=array();
			$sql_po_country_data=sql_select("SELECT  b.id,b.job_no_mst,c.country_id, sum(c.order_quantity/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst  and b.id=c.po_break_down_id   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond group by   b.id,b.job_no_mst,c.country_id order by b.id,b.job_no_mst,c.country_id");
			foreach( $sql_po_country_data as $sql_po_country_row)
			{
				$sql_po_qty_country_wise_arr[$sql_po_country_row[csf('id')]][$sql_po_country_row[csf('country_id')]]=$sql_po_country_row[csf('order_quantity_set')];
				$po_job_arr[$sql_po_country_row[csf('id')]]=$sql_po_country_row[csf('job_no_mst')];
			}
			unset($sql_po_country_data);

			$po_data_arr=array();
			$po_id_string="";
			$today=date("Y-m-d");

			$sql_pos=("SELECT a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.order_uom, b.file_no, b.grouping, b.id, b.po_number, sum(c.order_quantity) as order_quantity, sum(c.order_quantity/a.total_set_qnty) as order_quantity_set, b.pub_shipment_date,a.season_buyer_wise
			from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
			where
			a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.company_name=$company_name $buyer_id_cond ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $date_cond $style_ref_cond $jobcond  $ordercond $file_no_cond $internal_ref_cond $year_cond $season_con
			group by a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.order_uom, a.total_set_qnty, b.file_no, b.grouping, b.id, b.po_number, b.pub_shipment_date ,a.season_buyer_wise order by b.id");
			//echo $sql_pos; die;
			$sql_po=sql_select($sql_pos);
			$po_arr=array(); $tot_rows=0;
			foreach($sql_po as $row)
			{
				$tot_rows++;
				$po_arr[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
				$po_arr[$row[csf('id')]]['job_no']=$row[csf('job_no')];
				$po_arr[$row[csf('id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
				$po_arr[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')];
				$po_arr[$row[csf('id')]]['order_uom']=$row[csf('order_uom')];
				$po_arr[$row[csf('id')]]['file_no']=$row[csf('file_no')];
				$po_arr[$row[csf('id')]]['grouping']=$row[csf('grouping')];
				$po_arr[$row[csf('id')]]['po_number']=$row[csf('po_number')];
				$po_arr[$row[csf('id')]]['order_quantity']=$row[csf('order_quantity')];
				$po_arr[$row[csf('id')]]['order_quantity_set']=$row[csf('order_quantity_set')];
				$po_arr[$row[csf('id')]]['pub_shipment_date']=$row[csf('pub_shipment_date')];
				$po_arr[$row[csf('id')]]['season_buyer_wise']=$row[csf('season_buyer_wise')];
				$po_id_string.=$row[csf('id')].",";
			}
			unset($sql_po);
			$po_id_string=rtrim($po_id_string,",");
			if($po_id_string=="")
			{
				echo "<strong style='color:red;font-size:30px'>No Data found</strong>";
				die;
			}

			$poIds=chop($po_id_string,','); $order_cond=""; $order_cond1=""; $order_cond2=""; $precost_po_cond="";
			if($db_type==2 && $tot_rows>1000)
			{
				$order_cond=" and (";
				$order_cond1=" and (";
				$order_cond2=" and (";
				$precost_po_cond=" and (";
				$poIdsArr=array_chunk(explode(",",$poIds),999);
				foreach($poIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					//$poIds_cond.=" po_break_down_id in($ids) or ";
					$order_cond.=" b.po_break_down_id in($ids) or";
					$order_cond1.=" b.po_breakdown_id in($ids) or";
					$order_cond2.=" d.po_breakdown_id in($ids) or";
					$precost_po_cond.=" c.po_break_down_id in($ids) or";
				}
				$order_cond=chop($order_cond,'or ');
				$order_cond.=")";
				$order_cond1=chop($order_cond1,'or ');
				$order_cond1.=")";
				$order_cond2=chop($order_cond2,'or ');
				$order_cond2.=")";
				$precost_po_cond=chop($precost_po_cond,'or ');
				$precost_po_cond.=")";
			}
			else
			{
				$order_cond=" and b.po_break_down_id in($poIds)";
				$order_cond1=" and b.po_breakdown_id in($poIds)";
				$order_cond2=" and d.po_breakdown_id in($poIds)";
				$precost_po_cond=" and c.po_break_down_id in($poIds)";
			}
			//echo $cbo_item_group;
			if(str_replace("'","",$cbo_item_group)=="")
			{
				$trm_group_pre_cost_cond="";
				$trm_group_without_precost_cond="";
				$trm_group_rec_cond="";
				$trm_group_recrtn_cond="";
				$trm_group_iss_cond="";
			}
			else
			{
				$trm_group_pre_cost_cond="and b.trim_group in(".str_replace("'","",$cbo_item_group).")";
				$trm_group_without_precost_cond="and b.trim_group in(".str_replace("'","",$cbo_item_group).")";
				$trm_group_rec_cond="and a.item_group_id in(".str_replace("'","",$cbo_item_group).")";
				$trm_group_recrtn_cond="and c.item_group_id in(".str_replace("'","",$cbo_item_group).")";
				$trm_group_iss_cond="and a.item_group_id in(".str_replace("'","",$cbo_item_group).")";
			}

			$condition= new condition();
			if($txt_style_id!="")
			{
				$condition->jobid_in("$txt_style_id"); 
			}
			else 
			{
				if(str_replace("'","",$txt_job_no) !='')
				{
					$condition->job_no_prefix_num("in($txt_job_no)"); 
				}
			  	if(str_replace("'","",trim($txt_style_ref))!='')
				{
					$style_ref=str_replace("'","",trim($txt_style_ref));
					$condition->style_ref_no("like '%$style_ref%'");
				}
		    }

			if($file_no!="")
			{
				$condition->file_no("=$file_no"); 
			}
		    
			if(str_replace("'","",$cbo_buyer_name)!=0)
			{
				$condition->buyer_name("=$cbo_buyer_name"); 
			}
		    
			if(str_replace("'","",$cbo_season_id)!=0)
			{
				$condition->season("=$cbo_season_id"); 
			}
			
			if($order_no_id!="")
			{
					$condition->po_id("in($order_no_id)");
			}
			elseif(str_replace("'","",$txt_order_no)!='')
			{
				$order_nos=str_replace("'","",$txt_order_no);
				$condition->po_number(" like '%$order_nos%'");
			}
			
			if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
			{
				$start_date=(str_replace("'","",$txt_date_from));
				$end_date=(str_replace("'","",$txt_date_to));
			}
			if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
			{
				  $condition->country_ship_date(" between '$start_date' and '$end_date'");
			}
			/*if(str_replace("'",'',$txt_po_breack_down_id) !="")
			{
				$condition->po_id("in($txt_po_breack_down_id)");
			}*/
			$condition->init();
			$trim= new trims($condition);
			// echo $trim->getQuery(); die;
			$trim_qty=$trim->getQtyArray_by_orderCountryAndPrecostdtlsid();
			//print_r($trim_qty);
			// $trim= new trims($condition);
			$trim_amount=$trim->getAmountArray_by_orderCountryAndPrecostdtlsid();
			$budget_arr=array();
			$order_by = ($cbo_season_id !=0) ? " c.po_break_down_id" : " b.trim_group";

			$sql_pre_cost=sql_select("SELECT a.costing_per, max(a.costing_date) as costing_date, b.id as trim_dtla_id, b.trim_group, b.remark, b.description, b.brand_sup_ref, b.cons_uom, b.cons_dzn_gmts, b.rate, b.amount, b.apvl_req, b.nominated_supp, b.insert_date, c.cons, c.country_id, c.cons as cons_cal, c.po_break_down_id
			from wo_pre_cost_mst a, wo_pre_cost_trim_cost_dtls b, wo_pre_cost_trim_co_cons_dtls c
			where c.job_no=b.job_no and b.id=c.wo_pre_cost_trim_cost_dtls_id and a.job_no=b.job_no and a.status_active=1 and c.status_active=1 and  b.status_active=1 and c.cons>0 $trm_group_pre_cost_cond $precost_po_cond
			group by a.costing_per, b.id, b.trim_group, b.remark, b.description, b.brand_sup_ref, b.cons_uom, b.cons_dzn_gmts, b.rate, b.amount, b.apvl_req, b.nominated_supp, b.insert_date, c.cons, c.pcs, c.country_id, c.po_break_down_id order by $order_by");

			//echo $sql_pre_cost; die;
			foreach($sql_pre_cost as $rowp)
			{
				$dzn_qnty=0;

				if($rowp[csf('costing_per')]==1) $dzn_qnty=12;
				else if($rowp[csf('costing_per')]==3) $dzn_qnty=12*2;
				else if($rowp[csf('costing_per')]==4) $dzn_qnty=12*3;
				else if($rowp[csf('costing_per')]==5) $dzn_qnty=12*4;
				else $dzn_qnty=1;

				$po_qty=0;$req_qnty=0; $req_value=0;
				if($rowp[csf('country_id')]==0)
				{
					$po_qty=$po_arr[$rowp[csf('po_break_down_id')]]['order_quantity'];
					$req_qnty+=$trim_qty[$rowp[csf('po_break_down_id')]][$rowp[csf('country_id')]][$rowp[csf('trim_dtla_id')]];
					//$req_value+=$trim_amount[$rowp[csf('po_break_down_id')]][$rowp[csf('country_id')]][$rowp[csf('trim_dtla_id')]];
				}
				else
				{
					$country_id= explode(",",$rowp[csf('country_id')]);
					for($cou=0;$cou<=count($country_id); $cou++)
					{
						$po_qty+=$sql_po_qty_country_wise_arr[$rowp[csf('po_break_down_id')]][$country_id[$cou]];
						$req_qnty+=$trim_qty[$rowp[csf('po_break_down_id')]][$country_id[$cou]][$rowp[csf('trim_dtla_id')]];
						//$req_value+=$trim_amount[$rowp[csf('po_break_down_id')]][$rowp[csf('country_id')]][$rowp[csf('trim_dtla_id')]];
					}
				}
				$req_value=$rowp[csf('rate')]*$req_qnty;

				$po_data_arr[$rowp[csf('po_break_down_id')]]['trim_dtla_id'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('trim_dtla_id')];// for rowspan
				$po_data_arr[$rowp[csf('po_break_down_id')]]['trim_group'][$rowp[csf('trim_group')]]=$rowp[csf('trim_group')];
				$po_data_arr[$rowp[csf('po_break_down_id')]][$rowp[csf('trim_group')]][$rowp[csf('trim_dtla_id')]]=$rowp[csf('trim_dtla_id')]; // for rowspannn
				$po_data_arr[$rowp[csf('po_break_down_id')]]['trim_group_dtls'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('trim_group')];
				$po_data_arr[$rowp[csf('po_break_down_id')]]['remark'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('remark')];
				$po_data_arr[$rowp[csf('po_break_down_id')]]['brand_sup_ref'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('brand_sup_ref')];
				$po_data_arr[$rowp[csf('po_break_down_id')]]['apvl_req'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('apvl_req')];
				$po_data_arr[$rowp[csf('po_break_down_id')]]['insert_date'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('insert_date')];
				$po_data_arr[$rowp[csf('po_break_down_id')]]['req_qnty'][$rowp[csf('trim_dtla_id')]]+=$req_qnty;
				$po_data_arr[$rowp[csf('po_break_down_id')]]['req_value'][$rowp[csf('trim_dtla_id')]]+=$req_value;
				$po_data_arr[$rowp[csf('po_break_down_id')]]['cons_uom'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('cons_uom')];
				$po_data_arr[$rowp[csf('po_break_down_id')]]['trim_group_from'][$rowp[csf('trim_dtla_id')]]="Pre_cost";
				$po_data_arr[$rowp[csf('po_break_down_id')]]['rate'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('rate')];
				$po_data_arr[$rowp[csf('po_break_down_id')]]['description'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('description')];
				$po_data_arr[$rowp[csf('po_break_down_id')]]['country_id'][$rowp[csf('trim_dtla_id')]].=$rowp[csf('country_id')].',';
				$po_data_arr[$rowp[csf('po_break_down_id')]]['avg_cons'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('cons_dzn_gmts')];
				//$po_data_arr[$rowp[csf('po_break_down_id')]]['costing_date'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('costing_date')];
				$budget_arr[$rowp[csf('po_break_down_id')]]['costing_per']=$rowp[csf('costing_per')];
				$budget_arr[$rowp[csf('po_break_down_id')]]['costing_date']=$rowp[csf('costing_date')];
			}
			unset($sql_pre_cost);

			//echo $sql_po; die;
			//LISTAGG(CAST( a.supplier_id || '**' || a.pay_mode AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.booking_no) as no_of_roll
			if($db_type==2)
			{
				$wo_sql_without_precost=sql_select("select min(a.booking_date) as booking_date, b.job_no, LISTAGG(CAST(a.booking_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as booking_no, LISTAGG(CAST(a.supplier_id || '**' || a.pay_mode AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as supplier_id, b.po_break_down_id, b.trim_group, b.pre_cost_fabric_cost_dtls_id, sum(b.wo_qnty) as wo_qnty, sum(b.amount/b.exchange_rate) as amount, sum(b.rate) as rate
				from wo_booking_mst a, wo_booking_dtls b
				where a.item_category=4 and a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name $order_cond $trm_group_without_precost_cond
				group by b.po_break_down_id, b.trim_group, b.job_no, b.pre_cost_fabric_cost_dtls_id");//and item_from_precost=2
			}
			else if($db_type==0)
			{
				$wo_sql_without_precost=sql_select("select min(a.booking_date) as booking_date, b.job_no, group_concat(a.booking_no) as booking_no, group_concat(concat_ws('**',a.supplier_id,a.pay_mode)) as supplier_id, b.po_break_down_id, b.trim_group, b.pre_cost_fabric_cost_dtls_id, sum(b.wo_qnty) as wo_qnty, sum(b.amount/b.exchange_rate) as amount, sum(b.rate) as rate
				from wo_booking_mst a, wo_booking_dtls b
				where a.item_category=4 and a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name $order_cond group by b.po_break_down_id, b.trim_group, b.job_no, b.pre_cost_fabric_cost_dtls_id");//and item_from_precost=2
			}

			/*if($db_type==2)
			{
				$wo_sql_without_precost=sql_select("select min(a.booking_date) as booking_date, b.job_no, LISTAGG(CAST(a.booking_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as booking_no, a.supplier_id, a.pay_mode, b.po_break_down_id, b.trim_group, b.pre_cost_fabric_cost_dtls_id, sum(b.wo_qnty) as wo_qnty, sum(b.amount/b.exchange_rate) as amount, sum(b.rate) as rate, LISTAGG(CAST(b.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as booking_dtls_id
				from wo_booking_mst a, wo_booking_dtls b
				where a.item_category=4 and a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name $order_cond $trm_group_without_precost_cond
				group by b.po_break_down_id, b.trim_group, b.job_no, b.pre_cost_fabric_cost_dtls_id, a.supplier_id, a.pay_mode");//and item_from_precost=2
			}
			else if($db_type==0)
			{
				$wo_sql_without_precost=sql_select("select min(a.booking_date) as booking_date, b.job_no, group_concat(a.booking_no) as booking_no, a.supplier_id, a.pay_mode, b.po_break_down_id, b.trim_group, b.pre_cost_fabric_cost_dtls_id, sum(b.wo_qnty) as wo_qnty, sum(b.amount/b.exchange_rate) as amount, sum(b.rate) as rate, group_concat(b.id) as booking_dtls_id
				from wo_booking_mst a, wo_booking_dtls b
				where a.item_category=4 and a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name $order_cond group by b.po_break_down_id, b.trim_group, b.job_no, b.pre_cost_fabric_cost_dtls_id, a.supplier_id, a.pay_mode");//and item_from_precost=2
			}*/


			//echo $wo_sql_without_precost; die;
			$style_data_arr1=array();$trims_dtls_id_arr=array();$booking_precost_id=array();
			foreach($wo_sql_without_precost as $wo_row_without_precost)
			{
				$conversion_factor_rate=$conversion_factor_array[$wo_row_without_precost[csf('trim_group')]]['con_factor'];
				//$cons_uom=$conversion_factor_array[$wo_row_without_precost[csf('trim_group')]]['cons_uom'];
				$cons_uom=$item_arr[$wo_row_without_precost[csf('trim_group')]]['order_uom'];
				$booking_no=implode(",",array_unique(explode(",",$wo_row_without_precost[csf('booking_no')])));
				$supplier_id=implode(",",array_unique(explode(",",$wo_row_without_precost[csf('supplier_id')])));
				$wo_qnty=$wo_row_without_precost[csf('wo_qnty')];
				$amount=$wo_row_without_precost[csf('amount')];
				$wo_date=$wo_row_without_precost[csf('booking_date')];

				$booking_id_arr=array_unique(explode(",",$wo_row_without_precost[csf('booking_dtls_id')]));
				foreach($booking_id_arr as $book_id)
				{
					$booking_precost_id[$book_id]=$wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')];
				}

				//$trims_dtls_id_arr[]
				if($wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')] =="" || $wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')] ==0)
				{
					$trim_dtla_id=max($po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]]['trim_dtla_id'])+1;
					$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]]['trim_dtla_id'][$trim_dtla_id]=$trim_dtla_id;// for rowspan
					$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]]['trim_group'][$wo_row_without_precost[csf('trim_group')]]=$wo_row_without_precost[csf('trim_group')];
					$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][$wo_row_without_precost[csf('trim_group')]][$trim_dtla_id]=$trim_dtla_id;// for rowspannn
					$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]]['trim_group_dtls'][$trim_dtla_id]=$wo_row_without_precost[csf('trim_group')];
					$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]]['cons_uom'][$trim_dtla_id]=$cons_uom;

					$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]]['trim_group_from'][$trim_dtla_id]="Booking Without Pre_cost";
				}
				else
				{
					$trim_dtla_id=$wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')];
				}

				$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]]['wo_qnty'][$trim_dtla_id]+=$wo_qnty;
				$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]]['amount'][$trim_dtla_id]+=$amount;
				$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]]['wo_date'][$trim_dtla_id]=$wo_date;
				$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]]['wo_qnty_trim_group'][$wo_row_without_precost[csf('trim_group')]]+=$wo_qnty;
				$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]]['booking_no'][$trim_dtla_id]=$booking_no;
				$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]]['supplier_id'][$trim_dtla_id]=$wo_row_without_precost[csf('supplier_id')];
				//$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]]['pay_mode'][$trim_dtla_id]=$wo_row_without_precost[csf('pay_mode')];
				$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]]['conversion_factor_rate'][$trim_dtla_id]=$conversion_factor_rate;
			}
			unset($wo_sql_without_precost);

			//echo "<pre>";print_r($booking_precost_id);die;

			//$receive_qty_data=sql_select("select b.po_breakdown_id, a.item_group_id,sum(b.quantity) as quantity from inv_receive_master c,product_details_master d, inv_trims_entry_dtls a, order_wise_pro_details b where a.mst_id=c.id and a.trans_id=b.trans_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $order_cond1  group by b.po_breakdown_id, a.item_group_id order by a.item_group_id ");
			$sql_rec_data=sql_select("select b.po_breakdown_id, a.item_group_id, c.receive_basis, a.booking_id, b.quantity as quantity, b.order_rate as rate, c.exchange_rate, (b.quantity*b.order_rate) as amount
			from inv_receive_master c, product_details_master d, inv_trims_entry_dtls a, order_wise_pro_details b
			where a.mst_id=c.id and a.trans_id=b.trans_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_cond1 $trm_group_rec_cond order by a.item_group_id ");

			//echo $receive_qty_data; die;
			foreach($sql_rec_data as $k => $row)
			{
				if($po_data_arr[$row[csf('po_breakdown_id')]]['trim_group'][$row[csf('item_group_id')]]=="" || $po_data_arr[$row[csf('po_breakdown_id')]]['trim_group'][$row[csf('item_group_id')]]==0)
				{
					$cons_uom=$conversion_factor_array[$row[csf('item_group_id')]]['cons_uom'];
					$trim_dtla_id=max($po_data_arr[$row[csf('po_breakdown_id')]]['trim_dtla_id'])+1;
					$po_data_arr[$row[csf('po_breakdown_id')]]['trim_dtla_id'][$trim_dtla_id]=$trim_dtla_id;// for rowspan
					$po_data_arr[$row[csf('po_breakdown_id')]]['trim_group'][$row[csf('item_group_id')]]=$row[csf('item_group_id')];
					$po_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$trim_dtla_id]=$trim_dtla_id;// for rowspannn
					$po_data_arr[$row[csf('po_breakdown_id')]]['trim_group_dtls'][$trim_dtla_id]=$row[csf('item_group_id')];
					$po_data_arr[$row[csf('po_breakdown_id')]]['cons_uom'][$trim_dtla_id]=$cons_uom;
					$po_data_arr[$row[csf('po_breakdown_id')]]['trim_group_from'][$trim_dtla_id]="Trim Receive";
					//echo $trim_dtla_id.'==';
				}
				$po_data_arr[$row[csf('po_breakdown_id')]]['inhouse_qnty'][$row[csf('item_group_id')]]+=$row[csf('quantity')];
                if($row[csf('rate')] > 0) {
                    $po_data_arr[$row[csf('po_breakdown_id')]]['inhouse_rate'][$row[csf('item_group_id')]][$k] = $row[csf('rate')];
                }
				//$po_data_arr[$row[csf('po_breakdown_id')]]['inhouse_value'][$row[csf('item_group_id')]]+=$row[csf('amount')];
				$amount=0;  $amount=($row[csf('quantity')]*$row[csf('rate')]);
				$po_data_arr[$row[csf('po_breakdown_id')]]['inhouse_value'][$row[csf('item_group_id')]]+=$amount;
				$po_data_arr[$row[csf('po_breakdown_id')]]['basis_piwono'][$row[csf('item_group_id')]].=$row[csf('receive_basis')].'_'.$row[csf('booking_id')].',';
			}
			unset($sql_rec_data);
			//print_r($po_data_arr['20552']['inhouse_qnty']);

			$sql_wo_pi=sql_select("select a.pi_number, b.work_order_no, b.work_order_dtls_id from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=4 and a.status_active=1 and a.importer_id=$company_name and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.work_order_dtls_id>0");
			$pi_arr=array();
			foreach($sql_wo_pi as $rowPi)
			{
				if($tem_pi[$rowPi[csf('work_order_no')]]=="")
				{
					$tem_pi[$rowPi[csf('work_order_no')]]=$rowPi[csf('pi_number')];
					$pi_arr[$rowPi[csf('work_order_no')]].=$rowPi[csf('pi_number')].'**';
				}

				/*if($tem_pi[$rowPi[csf('pi_number')]][$booking_precost_id[$rowPi[csf('work_order_dtls_id')]]]=="")
				{
					$tem_pi[$rowPi[csf('pi_number')]][$booking_precost_id[$rowPi[csf('work_order_dtls_id')]]]=$rowPi[csf('pi_number')];
					$pi_arr[$booking_precost_id[$rowPi[csf('work_order_dtls_id')]]].=$rowPi[csf('pi_number')].'**';
				}*/

			}
			unset($sql_wo_pi);

			//echo ( $pi_arr[34757]); echo jahid;die;

			/*$receive_rtn_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id, sum(d.quantity) as quantity   from  inv_issue_master a,inv_transaction b, product_details_master c, order_wise_pro_details d,inv_receive_master e  where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and e.id=a.received_id and b.transaction_type=3 and a.entry_form=49 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $order_cond2 $trm_group_recrtn_cond  group by d.po_breakdown_id, c.item_group_id order by c.item_group_id");
			//echo $receive_rtn_qty_data; die;
			foreach($receive_rtn_qty_data as $row)
			{
				$ord_uom_qty=0;
				$ord_uom_qty=$row[csf('quantity')]/$item_arr[$row[csf('item_group_id')]]['order_uom'];
				$po_data_arr[$row[csf('po_breakdown_id')]]['receive_rtn_qty'][$row[csf('item_group_id')]]+=$ord_uom_qty;
			}
			unset($receive_rtn_qty_data);*/


			$receive_rtn_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id, d.quantity as quantity, c.order_rate as rate
			from product_details_master c,order_wise_pro_details d
			where d.prod_id=c.id and d.trans_type=3 and d.entry_form=49 and d.status_active=1 and d.is_deleted=0 $order_cond2");
			foreach($receive_rtn_qty_data as $row)
			{
				$ord_uom_qty=0; $receive_rtn_amount=0;
				//$ord_uom_qty=$row[csf('quantity')]/$conversion_factor_array[$row[csf('item_group_id')]]['con_factor'];
				$ord_uom_qty=$row[csf('quantity')];
				$receive_rtn_amount=$ord_uom_qty*$row[csf('rate')];
				$po_data_arr[$row[csf('po_breakdown_id')]]['receive_rtn_qty'][$row[csf('item_group_id')]]+=$ord_uom_qty;
				$po_data_arr[$row[csf('po_breakdown_id')]]['receive_rtn_amount'][$row[csf('item_group_id')]]+=$receive_rtn_amount;
			}
			//echo "<pre>";print_r($style_data_arr);
			unset($receive_rtn_qty_data);
			//echo "select b.po_breakdown_id, a.item_group_id, c.receive_basis, a.booking_id, b.quantity as quantity, a.cons_rate, (b.quantity*d.avg_rate_per_unit) as amount from inv_receive_master c, product_details_master d, inv_trims_entry_dtls a, order_wise_pro_details b where a.mst_id=c.id and a.trans_id=b.trans_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_cond1 $trm_group_rec_cond order by a.item_group_id ";
			$transfer_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id, sum((case when d.trans_type=5 then d.quantity else 0 end)-(case when d.trans_type=6 then d.quantity else 0 end)) as quantity ,
			sum(case when d.trans_type=5 then d.quantity else 0 end) as in_qty,
			sum(case when d.trans_type=6 then d.quantity else 0 end) as out_qty,
			sum(case when d.trans_type=5 then (d.quantity*d.order_rate) else 0 end) as in_amount,
			sum(case when d.trans_type=6 then (d.quantity*d.order_rate) else 0 end) as out_amount
			from product_details_master c,order_wise_pro_details d
			where d.prod_id=c.id and d.trans_type in(5,6) and d.entry_form=78 and d.status_active=1 and d.is_deleted=0 $order_cond2 group by d.po_breakdown_id, c.item_group_id");
			foreach($transfer_qty_data as $row)
			{
				$transfe_amount=0;
				$transfe_amount=$row[csf('in_amount')]-$row[csf('out_amount')];

				$po_data_arr[$row[csf('po_breakdown_id')]]['transfe_qty'][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				$po_data_arr[$row[csf('po_breakdown_id')]]['transfe_in'][$row[csf('item_group_id')]]+=$row[csf('in_qty')];
				$po_data_arr[$row[csf('po_breakdown_id')]]['transfe_out'][$row[csf('item_group_id')]]+=$row[csf('out_qty')];
				$po_data_arr[$row[csf('po_breakdown_id')]]['transfe_in_amount'][$row[csf('item_group_id')]]+=$row[csf('in_amount')];
				$po_data_arr[$row[csf('po_breakdown_id')]]['transfe_out_amount'][$row[csf('item_group_id')]]+=$row[csf('out_amount')];
				$po_data_arr[$row[csf('po_breakdown_id')]]['transfe_amount'][$row[csf('item_group_id')]]+=$transfe_amount;
			}
			unset($transfer_qty_data);

			$issue_qty_data=sql_select("select b.po_breakdown_id, p.item_group_id,sum(b.quantity) as quantity, sum(b.quantity*b.order_rate) as issue_amount
			from inv_issue_master d, product_details_master p, inv_trims_issue_dtls a, order_wise_pro_details b
			where a.mst_id=d.id and a.prod_id=p.id and a.trans_id=b.trans_id and b.trans_type=2 and d.entry_form=25 and b.entry_form=25 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_cond1 $trm_group_iss_cond group by b.po_breakdown_id, p.item_group_id");
			//echo $issue_qty_data; die;
			foreach($issue_qty_data as $row)
			{
				$po_data_arr[$row[csf('po_breakdown_id')]]['issue_qty'][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				$po_data_arr[$row[csf('po_breakdown_id')]]['issue_amount'][$row[csf('item_group_id')]]+=$row[csf('issue_amount')];
			}
			unset($issue_qty_data);


			$issue_rtn_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id, d.quantity as quantity, (d.quantity*d.order_rate) as amount
			from product_details_master c, order_wise_pro_details d
			where d.prod_id=c.id and d.trans_type=4 and d.entry_form=73 and d.status_active=1 and d.is_deleted=0 $order_cond2");
			foreach($issue_rtn_qty_data as $row)
			{
				$po_data_arr[$row[csf('po_breakdown_id')]]['issue_rtn_qty'][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				$po_data_arr[$row[csf('po_breakdown_id')]]['issue_rtn_amount'][$row[csf('item_group_id')]]+=$row[csf('amount')];
			}

			unset($issue_rtn_qty_data);

			$total_pre_costing_value=0;
			$total_wo_value=0;
			$total_left_over_balanc=0;$total_issue_amount=0;$total_rec_bal_qnty=0;

			$summary_array=array();
			$i=1;
			$bookingNoArr=return_library_array( "select id, booking_no from  wo_booking_mst", "id", "booking_no");
			$piArr=return_library_array("select id, pi_number from com_pi_master_details", "id", "pi_number");
			//die;
			foreach($po_arr as $key=>$value)
			{
				$rowspan=0;
				//print_r($po_data_arr[$key]['trim_dtla_id']);
				$rowspan=count($po_data_arr[$key]['trim_dtla_id']);
				if($rowspan!=0)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                        <td width="30" rowspan="<? echo $rowspan; ?>"><? echo $i; ?></td>
                        <td width="50" rowspan="<? echo $rowspan; ?>"><p style="word-break:break-all"><? echo $buyer_short_name_library[$value['buyer']]; ?></p></td>
                        <td width="50" rowspan="<? echo $rowspan; ?>"><p style="word-break:break-all"><? echo $lib_season_arr[$value['season_buyer_wise']]; ?></p></td>
                        <td width="100" align="center" rowspan="<? echo $rowspan; ?>"><p><? echo $value['job_no_prefix_num']; ?></p></td>
                        <td width="100" rowspan="<? echo $rowspan; ?>" style="word-break: break-all;"><p><? echo $value['style_ref']; ?></p></td>
                        <td width="100" rowspan="<? echo $rowspan; ?>" style="word-break: break-all;"><p><? echo $value['grouping']; ?></p></td>
                        <td width="100" rowspan="<? echo $rowspan; ?>" style="word-break: break-all;"><p><? echo $value['file_no']; ?></p></td>

                        <td width="90" rowspan="<? echo $rowspan; ?>" style="word-break: break-all;"><p><a href='#report_details' onclick="generate_report('<? echo $company_name; ?>','<? echo $value['job_no']; ?>','<? echo $value['buyer']; ?>','<? echo $value['style_ref']; ?>','<? echo change_date_format($budget_arr[$key]['costing_date']); ?>','<? echo $key; ?>','<? echo $budget_arr[$key]['costing_per']; ?>','preCostRpt');"> <? $po_number=$value['po_number']; echo $po_number; ?></a></p></td>

                        <td width="80" align="right" rowspan="<? echo $rowspan; ?>"><p><a href='#report_details' onclick="order_qty_popup('<? echo $company_name; ?>','<? echo $value['job_no']; ?>','<? echo $key; ?>','<? echo $value['buyer']; ?>', <? echo $txt_date_from; ?>, <? echo $txt_date_to; ?> ,'order_qty_data');"><? echo number_format($value['order_quantity_set'],0,'.',''); ?></a></p></td>

                        <td width="50" align="center" rowspan="<? echo $rowspan; ?>"><p><? echo $unit_of_measurement[$value['order_uom']]; ?></p></td>
                        <td width="80" align="right" rowspan="<? echo $rowspan; ?>"><? echo number_format($value['order_quantity'],0,'.',''); ?></td>
                        <td width="80" align="center" rowspan="<? echo $rowspan; ?>" style="word-break: break-all;"><p><? $pub_shipment_date= $value['pub_shipment_date']; echo change_date_format($pub_shipment_date); ?></p></td>
						<?
						$po_id=$po_data_arr[$key];

						//print_r ( $po_id );
						foreach($po_id['trim_group'] as $key_trim=>$value_trim)
						{
							$gg=1;
							$summary_array['trim_group'][$key_trim]=$key_trim;
							//print_r($key_trim); $po_data_arr[$row[csf('po_breakdown_id')]]['trim_group'][$row[csf('item_group_id')]]
							foreach($po_id[$key_trim] as $key_trim1=>$value_trim1)
							{
								//echo $key_trim1; $po_data_arr[$row[csf('po_breakdown_id')]]['trim_group_dtls'][$trim_dtla_id]
								$rowspannn=count($po_id[$key_trim]);
								if($gg==1)
								{
									?>
									<td width="100" style="word-break: break-all;"><p><? echo $item_library[$po_id['trim_group_dtls'][$key_trim1]]; ?></p></td>
                                    <td width="140"><p style="word-break:break-all"><? echo $po_id['description'][$key_trim1]; ?></p></td>
									<td width="100"><p style="word-break:break-all"><? echo $po_id['remark'][$key_trim1]; ?></p></td>
									<td width="100"><p style="word-break:break-all"><? echo $po_id['brand_sup_ref'][$key_trim1]; ?></p></td>
									<td width="100"><p style="word-break:break-all"><? echo $po_id['remark'][$key_trim1]; ?></p></td>
									<td width="60" align="center"><p><? if($po_id['apvl_req'][$key_trim1]==1) echo "Yes"; else echo ""; ?></p></td>
									<td width="80" align="center">
									<?
									if($po_id['apvl_req'][$key_trim1]==1)
									{
										$app_status=$app_status_arr[$value['job_no']][$po_id['trim_group_dtls'][$key_trim1]];
										$approved_status=$approval_status[$app_status];
										$summary_array['item_app'][$key_trim][all]+=1;
										if($app_status==3)
										{
											$summary_array['item_app'][$key_trim][app]+=1;
										}
									}
									else $approved_status="";

									echo $approved_status;
									$country_idAll=implode(",",array_filter(array_unique(explode(",",$value[country_id][$key_trim1]))));
									?>
									</td>
                                    <td width="100"><p><? $insert_date=explode(" ",$po_id['insert_date'][$key_trim1]); echo change_date_format($insert_date[0],'','',''); ?></p></td>
                                    <td width="80" align="right"><?=number_format($po_id['avg_cons'][$key_trim1],4); ?></td>
                                    <td width="100" align="right"><p><a href='#report_details' onclick="order_req_qty_popup('<? echo $company_name; ?>','<? echo $value['job_no']; ?>','<? echo $key; ?>', '<? echo $value['buyer']; ?>','<? echo $po_id['rate'][$key_trim1]; ?>','<? echo $po_id['trim_group_dtls'][$key_trim1];?>' ,'<? echo $po_id['booking_no'][$key_trim1] ;?>','<? echo $po_id['description'][$key_trim1];?>','<?=$country_idAll;?>','<? echo $po_id['trim_dtla_id'][$key_trim1]; ?>','<? echo $start_date ?>','<? echo $end_date ?>','order_req_qty_data');">
                                    <? $req_qty=0; $req_qty=number_format($po_id['req_qnty'][$key_trim1],2,'.',''); echo $req_qty; $summary_array['req_qnty'][$key_trim]+=$req_qty; ?></a></p></td>
                                    <td width="100" align="right"><p><? echo number_format($po_id['req_value'][$key_trim1],2); $total_pre_costing_value+=$po_id['req_value'][$key_trim1]; ?></p></td>
									<?
                                    // $conversion_factor_rate=$conversion_factor_array[$row[csf('trim_group')]]['con_factor'];
                                    $wo_qnty=0;
                                    $wo_qnty=number_format($po_id['wo_qnty'][$key_trim1],2,'.','');

                                    /*if($wo_qnty > $req_qty) $color_wo="red";
                                    else if($wo_qnty < $req_qty ) $color_wo="yellow";
                                    else  $color_wo="";*/

									if(($po_id['conversion_factor_rate'][$key_trim1]*$wo_qnty)==$req_qty) $color_wo="green";
									else if(($po_id['conversion_factor_rate'][$key_trim1]*$wo_qnty) > $req_qty) $color_wo="red";
									else if(($po_id['conversion_factor_rate'][$key_trim1]*$wo_qnty) < $req_qty ) $color_wo="yellow";
									else $color_wo="";

                                    $supplier_name_string="";
                                    $supplier_id_arr=array_unique(explode(',',$po_id['supplier_id'][$key_trim1]));
                                    foreach($supplier_id_arr as $supplier_id_arr_key=>$supplier_id_arr_value)
                                    {
										$ex_sup_data=explode("**",$supplier_id_arr_value);

										if($ex_sup_data[1]==3 || $ex_sup_data[1]==5) $suplier_name_arr=$company_library; else $suplier_name_arr=$lib_supplier_arr;
                                   		$supplier_name_string.=$suplier_name_arr[$ex_sup_data[0]].",";
                                    }

									/*if($po_id['pay_mode'][$key_trim1]==3 || $po_id['pay_mode'][$key_trim1]==5)
									{
										$supplier_name_string=$company_library[$po_id['supplier_id'][$key_trim1]];
									}
									else
									{
										$supplier_name_string=$lib_supplier_arr[$po_id['supplier_id'][$key_trim1]];
									}*/


									$booking_no_arr=array_unique(explode(',',$po_id['booking_no'][$key_trim1]));
									$main_booking_no_large_data=""; $piWoNo='';
									foreach($booking_no_arr as $booking_no1)
									{
										$piWoNo.=chop($pi_arr[$booking_no1],"**").",";
										if($main_booking_no_large_data=="") $main_booking_no_large_data=$booking_no1; else $main_booking_no_large_data.=",".$booking_no1;
										/*
										if($booking_no1!="")
										{
											if($piWoNo=="") $piWoNo=implode(',',array_filter(array_unique(explode("**",$pi_arr[$booking_no1])))); else $piWoNo.=",".implode(',',array_filter(array_unique(explode("**",$pi_arr[$booking_no1]))));//
										}*/
									}
									?>
                                    <td width="90" align="right" title="<? echo 'conversion_factor='.$po_id['conversion_factor_rate'][$key_trim1];?>" bgcolor="<? echo $color_wo;?>"><p><a href='#report_details' onclick="openmypage('<? echo $key; ?>','<? echo $po_id['trim_group_dtls'][$key_trim1]; ?>','<? echo $value['job_no']; ?>','<? echo $main_booking_no_large_data;?>','<? echo $po_id['trim_dtla_id'][$key_trim1];?>','booking_info');">
                                    <? echo number_format($wo_qnty,2,'.',''); $summary_array['wo_qnty'][$key_trim]+=$wo_qnty; ?></a></p></td>
                                    <td width="60"><p><? echo $unit_of_measurement[$item_arr[$po_id['trim_group_dtls'][$key_trim1]]['order_uom']];
                                    //echo $unit_of_measurement[$po_id['cons_uom'][$key_trim1]];
                                    $summary_array['cons_uom'][$key_trim]=$item_arr[$po_id['trim_group_dtls'][$key_trim1]]['order_uom'];//$po_id['cons_uom'][$key_trim1];
                                    ?></p></td>
                                    <td width="100" align="right" title="<? echo number_format($po_id['rate'][$key_trim1],2,'.',''); ?>"><p><? echo number_format($po_id['amount'][$key_trim1],2,'.',''); $total_wo_value+=$po_id['amount'][$key_trim1];?></p></td>
                                    <td width="150" align="left"><p><? echo chop($supplier_name_string,","); ?></p></td>
                                    <td width="70" title="<? echo change_date_format($po_id['wo_date'][$key_trim1])."==".$key_trim1;?>" align="center"><p>
                                    <? $tot=change_date_format($insert_date[0]);
                                    if($po_id['wo_qnty'][$key_trim1]<=0 )
                                    {
                                    	$daysOnHand = datediff('d',$tot,$today);
                                    }
                                    else
                                    {
										$wo_date=$po_id['wo_date'][$key_trim1];
										$wo_date=change_date_format($wo_date);
										$daysOnHand = datediff('d',$tot,$wo_date);;
                                    }
                                    echo $daysOnHand; ?></p></td>
                                    <?
									$inhouse_value=0; $inhouse_qnty=0;
									$transfe_out=number_format($po_id['transfe_out'][$key_trim],2,'.','');
									$transfe_in=number_format($po_id['transfe_in'][$key_trim],2,'.','');

									$transfe_in_out=$transfe_in.' & '.$transfe_out;
									$transfe_out_amt=number_format($po_id['transfe_out_amount'][$key_trim],2,'.','');
									$transfe_in_amt=number_format($po_id['transfe_in_amount'][$key_trim],2,'.','');

									$transfe_in_out_amt=$transfe_in_amt.' & '.$transfe_out_amt;
                                    $inhouse_qnty=($po_id['inhouse_qnty'][$key_trim]+$po_id['transfe_qty'][$key_trim])-$po_id['receive_rtn_qty'][$key_trim];

									$inhouse_value=($po_id['inhouse_value'][$key_trim]+$po_id['transfe_amount'][$key_trim])-$po_id['receive_rtn_amount'][$key_trim];

									$total_inhouse_value+=$inhouse_value;
                                    $balance=$po_id['wo_qnty_trim_group'][$key_trim]-$inhouse_qnty;
                                    $conv_rate=$conversion_factor_array[$po_id['trim_group_dtls'][$key_trim1]]['con_factor'];
                                    $issue_qnty=$po_id['issue_qty'][$key_trim]-$po_id['issue_rtn_qty'][$key_trim];

									$issue_amount=($po_id['issue_qty'][$key_trim]-$po_id['issue_rtn_qty'][$key_trim]) * (count($po_id['inhouse_rate'][$key_trim]) > 0 ? array_sum($po_id['inhouse_rate'][$key_trim]) / count($po_id['inhouse_rate'][$key_trim]) : 0);

                                    //$tot_issue=$issue_qnty/$conv_rate;
									$tot_issue=$issue_qnty;
                                    $left_overqty=$inhouse_qnty-$tot_issue;

									$left_overamount=$inhouse_value-$issue_amount;

                                    $summary_array['inhouse_qnty'][$key_trim]+=$inhouse_qnty;
                                    $summary_array['inhouse_qnty_bl'][$key_trim]+=$balance;
                                    $summary_array['issue_qty'][$key_trim]+=$tot_issue;
                                    $summary_array['left_overqty'][$key_trim]+=$left_overqty;
                                    ?>
                                    <td width="90"><p><? echo chop($piWoNo,","); ?></p></td>
									<td width="100" align="right"><?= $pi_wise_btb_arr[chop($piWoNo,",")]['btb_lc_no'];?></td>
									<td width="100" align="right"><?=$pi_wise_btb_arr[chop($piWoNo,",")]['btb_lc_date'];;?></td>
                                    <td width="90" align="right" title="<? echo "Inhouse-Qty: ".number_format($po_id['inhouse_qnty'][$key_trim]-$po_id['receive_rtn_qty'][$key_trim],2,'.','')."\n Transfer in & Out Qty: ".$transfe_in_out."\n Return Qty: ".number_format($po_id['receive_rtn_qty'][$key_trim],2,'.',''); ?>" rowspan="<? echo $rowspannn; ?>"><p><a href='#report_details' onclick="openmypage_inhouse('<? echo $key; ?>','<? echo $po_id['trim_group_dtls'][$key_trim1]; ?>','booking_inhouse_info');"><? echo number_format($inhouse_qnty,2,'.',''); ?></a></p></td>
                                    <td width="90" align="right" title="<? echo "Inhouse-Amt: ".number_format($po_id['inhouse_value'][$key_trim]-$po_id['receive_rtn_amount'][$key_trim],2,'.','')."\n Transfer in & Out Amt: ".$transfe_in_out_amt."\n Return Amt: ".number_format($po_id['receive_rtn_amount'][$key_trim],2,'.',''); ?>" rowspan="<? echo $rowspannn; ?>"><p><? echo number_format($inhouse_value,2,'.',''); ?></p></td>
                                    <td width="90" align="right" rowspan="<? echo $rowspannn; ?>"><p><? echo number_format($balance,2,'.',''); $total_rec_bal_qnty+=$balance; ?></p></td>
                                    <td width="90" align="right" title="<? echo "Issue-Qty: ".number_format($po_id['issue_qty'][$key_trim],2,'.','')."\n Issue Return Qty: ".number_format($po_id['issue_rtn_qty'][$key_trim],2,'.',''); ?>" rowspan="<? echo $rowspannn; ?>">
                                    <p><a href='#report_details' title="Conv. Factor: <? echo $conv_rate;?>" onclick="openmypage_issue('<? echo $key; ?>','<? echo $po_id['trim_group_dtls'][$key_trim1]; ?>','booking_issue_info');"><? echo number_format($tot_issue,2,'.',''); ?></a></p></td>
                                    <td width="90" align="right" title="<? echo "Issue-Amt: ".number_format($po_id['issue_amount'][$key_trim],2,'.','')."\n Issue Return Amt: ".number_format($po_id['issue_rtn_amount'][$key_trim],2,'.',''); ?>" rowspan="<? echo $rowspannn; ?>"><p><? echo number_format($issue_amount,2,'.',''); $total_issue_amount+=$issue_amount; ?></p></td>
                                    <td width="90" align="right" rowspan="<? echo $rowspannn; ?>"><p><? echo number_format($left_overqty,2,'.',''); ?></p></td>
                                    <td  width="100" align="right" rowspan="<? echo $rowspannn; ?>"><p><? echo number_format($left_overamount,2,'.',''); $total_left_over_balanc+=$left_overamount; ?></p></td>
                                    <?
								}
								else
								{
									?>
									<td width="100" title="<? echo $po_id['trim_group_from'][$key_trim1]; ?>" style="word-break: break-all;"><p><? echo $item_library[$po_id['trim_group_dtls'][$key_trim1]]; ?></p></td>
                                    <td width="140"><p style="word-break:break-all"><? echo $po_id['description'][$key_trim1]; ?></p></td>
									<td width="100"><p style="word-break:break-all"><? echo $po_id['remark'][$key_trim1]; ?></p></td>
									<td width="100"><p style="word-break:break-all"><? echo $po_id['brand_sup_ref'][$key_trim1]; ?></p></td>
									<td width="100"><p style="word-break:break-all"><? echo $po_id['source_id'][$key_trim1]; ?></p></td>
									<td width="60" align="center"><p><? if($po_id['apvl_req'][$key_trim1]==1) echo "Yes"; else echo ""; ?></p></td>
									<td width="80" align="center"><?
									if($po_id['apvl_req'][$key_trim1]==1)
									{
										$app_status=$app_status_arr[$value['job_no']][$po_id['trim_group_dtls'][$key_trim1]];
										$approved_status=$approval_status[$app_status];
										$summary_array['item_app'][$key_trim][all]+=1;
										if($app_status==3)
										{
											$summary_array['item_app'][$key_trim][app]+=1;
										}
									}
									else
									{
										$approved_status="";
									}
									echo $approved_status; ?>
									</td>
                                    <td width="100"><p><? $insert_date=explode(" ",$po_id['insert_date'][$key_trim1]); echo change_date_format($insert_date[0],'','',''); ?></p></td>
                                    <td width="80" align="right"><?=number_format($po_id['avg_cons'][$key_trim1],4); ?></td>
                                    <td width="100" align="right">
                                    <p><a href='#report_details' onclick="order_req_qty_popup('<? echo $company_name; ?>','<? echo $value['job_no']; ?>','<? echo $key; ?>', '<? echo $value['buyer']; ?>','<? echo $po_id['rate'][$key_trim1]; ?>','<? echo $po_id['trim_group_dtls'][$key_trim1];?>' ,'<? echo $po_id['booking_no'][$key_trim1] ;?>','<? echo $po_id['description'][$key_trim1];?>','<? echo $po_id['country_id'][$key_trim1]; ?>','<? echo $po_id['trim_dtla_id'][$key_trim1]; ?>','<? echo $start_date ?>','<? echo $end_date ?>','order_req_qty_data');">
                                    <? $req_qty=number_format($po_id['req_qnty'][$key_trim1],2,'.',''); echo $req_qty; $summary_array['req_qnty'][$key_trim]+=$po_id['req_qnty'][$key_trim1]; ?></a></p></td>
                                    <td width="100" align="right">
                                    <p><? echo number_format($po_id['req_value'][$key_trim1],2);
                                    $total_pre_costing_value+=$po_id['req_value'][$key_trim1];?></p>
                                    </td>
									<?
                                    $wo_qnty=number_format($po_id['wo_qnty'][$key_trim1],2);

                                    /*if($wo_qnty > $req_qty) $color_wo="red";
                                    else if($wo_qnty < $req_qty ) $color_wo="yellow";
                                    else $color_wo="";*/

									if(($po_id['conversion_factor_rate'][$key_trim1]*$wo_qnty)==$req_qty) $color_wo="green";
									else if(($po_id['conversion_factor_rate'][$key_trim1]*$wo_qnty) > $req_qty) $color_wo="red";
									else if(($po_id['conversion_factor_rate'][$key_trim1]*$wo_qnty) < $req_qty ) $color_wo="yellow";
									else $color_wo="";

									$supplier_name_string="";
                                    $supplier_id_arr=array_unique(explode(',',$po_id['supplier_id'][$key_trim1]));
                                    foreach($supplier_id_arr as $supplier_id_arr_key=>$supplier_id_arr_value)
                                    {
										$ex_sup_data=explode("**",$supplier_id_arr_value);
										if($ex_sup_data[1]==3 || $ex_sup_data[1]==5) $suplier_name_arr=$company_library; else $suplier_name_arr=$lib_supplier_arr;
                                   		$supplier_name_string.=$suplier_name_arr[$ex_sup_data[0]].",";
                                    }


									/*if($po_id['pay_mode'][$key_trim1]==3 || $po_id['pay_mode'][$key_trim1]==5)
									{
										$supplier_name_string=$company_library[$po_id['supplier_id'][$key_trim1]];
									}
									else
									{
										$supplier_name_string=$lib_supplier_arr[$po_id['supplier_id'][$key_trim1]];
									}*/

									$booking_no_arr=array_unique(explode(',',$po_id['booking_no'][$key_trim1]));
									$main_booking_no_large_data=""; $piWoNo='';
									foreach($booking_no_arr as $booking_no1)
									{
										$piWoNo.=chop($pi_arr[$booking_no1],"**").",";
										if($main_booking_no_large_data=="") $main_booking_no_large_data=$booking_no1; else $main_booking_no_large_data.=",".$booking_no1;
										/*
										if($booking_no1!="")
										{
											if($piWoNo=="") $piWoNo=implode(',',array_filter(array_unique(explode("**",$pi_arr[$booking_no1])))); else $piWoNo.=",".implode(',',array_filter(array_unique(explode("**",$pi_arr[$booking_no1]))));//
										}*/
									}

                                    ?>
                                    <td width="90" align="right" title="<? echo 'conversion_factor='.$po_id['conversion_factor_rate'][$key_trim1];?>" bgcolor="<? echo $color_wo;?>"><p><a href='#report_details' onclick="openmypage('<? echo $key; ?>','<? echo $po_id['trim_group_dtls'][$key_trim1]; ?>','<? echo $value['job_no']; ?>','<? echo $main_booking_no_large_data;?>','<? echo $po_id['trim_dtla_id'][$key_trim1];?>','booking_info');"><? echo number_format($po_id['wo_qnty'][$key_trim1],2,'.',''); $summary_array['wo_qnty'][$key_trim]+=$po_id['wo_qnty'][$key_trim1];?></a></p></td>
                                    <td width="60" align="center"><p><? $unit_of_measurement[$item_arr[$po_id['trim_group_dtls'][$key_trim1]]['order_uom']];
                                    //echo $unit_of_measurement[$po_id['cons_uom'][$key_trim1]];
                                    $summary_array['cons_uom'][$key_trim]= $item_arr[$po_id['trim_group_dtls'][$key_trim1]]['order_uom'];//$po_id['cons_uom'][$key_trim1]; ?></p>
                                    </td>
								
                                    <td width="100" align="right" title="<? echo number_format($po_id['rate'][$key_trim1],2,'.',''); ?>"><p><? echo number_format($value['amount'][$key_trim1],2,'.',''); $total_wo_value+=$po_id['amount'][$key_trim1]; ?></p></td>
                                    <td width="150"><p><? echo chop($supplier_name_string,","); ?></p></td>

                                    <td width="70" align="center" title="<? echo change_date_format($po_id['wo_date'][$key_trim1])."==".$key_trim1;?>"><p>
									<?
                                    $tot=change_date_format($insert_date[0]);
                                    if($po_id['wo_qnty'][$key_trim1]<=0 )
                                    {
                                    	$daysOnHand = datediff('d',$tot,$today);
                                    }
                                    else
                                    {
										$wo_date=$po_id['wo_date'][$key_trim1];
										$wo_date=change_date_format($wo_date);
										$daysOnHand = datediff('d',$tot,$wo_date);;
                                    }
                                    echo $daysOnHand; ?></p></td>
                                    <td width="90"><p><? echo chop($piWoNo,","); ?></p></td>
									<td width="100" align="right"><?=$pi_wise_btb_lc_arr[$row[csf("pi_id")]]['pi_number'];?></td>
									<td width="100" align="right"><?=$pi_wise_btb_lc_arr[$row[csf("pi_id")]]['btb_lc_date'];?></td>
                                    <?
									}
									foreach($trims_tna as $vid=>$key)
									{
									 if( $key==32 || $key==71   || $key==300  || $key==8 || $key==13 || $key==29 || $key==279  || $key==308 || $key==11 || $key==307 || $key==278  || $key==310 || $key==301 || $key==309 || $key==12 || $key==24){
									
										$plan_date=$job_wise_tna_data_arr[$job_no][$key]['plan_end_date'];
										$actual_date=$job_wise_tna_data_arr[$job_no][$key]['actual_finish_date'];
										if(strtotime($actual_date)>0){
										$diff = abs(strtotime($plan_date) - strtotime($actual_date));
										$delay = floor($diff / (60*60*24));	
										}else{
											$delay="";
										}							
								
								 ?>
									<td width="100" align="right" id=""><?=$job_wise_tna_data_arr[$job_no][$key]['plan_end_date'];;?></td>
									<td width="100" align="right" id=""><?=$job_wise_tna_data_arr[$job_no][$key]['actual_finish_date'];;?></td>
									<td width="100" align="right" id=""><? 	echo $delay;?></td>
										<? }}?>
									
									 
                                    </tr>
                                    <?
                                    $gg++;
								}
							}// end  foreach($value[$key_trim] as $key_trim1=>$value_trim1)
							$i++;
						}
					}
					unset($po_data_arr);
					unset($po_arr);
				?>
				</table>
                </div>
                <table class="rpt_table" width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <tfoot>
                        <th width="30">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="90">&nbsp;</th>
						
                        <th width="80" align="right" id="total_order_qnty"><? //echo number_format($total_order_qnty,0); ?></th>
                        <th width="50">&nbsp;</th>
                        <th width="80" align="right" id="total_order_qnty_in_pcs"><? //echo number_format($total_order_qnty_in_pcs,0); ?></th>
                        <th width="80">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="140">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>

                        <th width="60">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="100">&nbsp;</th>
						<th width="80">&nbsp;</th>
                        <th width="100" align="right" id="value_req_qnty"><? //echo number_format($total_req_qnty,2); ?></th>
                        <th width="100" align="right" id="value_pre_costing"><? echo number_format($total_pre_costing_value,2); ?></th>
                        <th width="90" align="right" id="value_wo_qty"><? //echo number_format($total_wo_qnty,2); ?></th>
						
                        <th width="60" align="right" >&nbsp;</th>
                        <th width="100" align="right" id=""><? echo number_format($total_wo_value,2); ?></th>
                        <th width="150" align="right" id="">&nbsp;</th>
                        <th width="70" align="right">&nbsp;<p><? //echo number_format($req_value,2,'.',''); ?></p></th>
                        <th width="90" align="right">&nbsp;<? //echo number_format($total_in_qnty,2); ?></th>
						
						<th width="100" align="right" >&nbsp;</th>
						<th width="100" align="right" >&nbsp;</th>

                        <th width="90" align="right" id="value_in_qty">&nbsp;<? //echo number_format($total_in_qnty,2); ?></th>



                        <th width="90" align="right" id="value_in_amount">&nbsp;<? echo number_format($total_inhouse_value,2); ?></th>
		

                        <th width="90" align="right" id="value_rec_qty"><? echo number_format($total_rec_bal_qnty,2); ?></th>
                        <th width="90" align="right" id="value_issue_qty">&nbsp;<? //echo number_format($total_issue_qnty,2); ?></th>
                        <th width="90" align="right" id="value_issue_amount"><? echo number_format($total_issue_amount,2); ?></th>

                        <th width="90" align="right" id="value_leftover_qty">&nbsp;<? //echo number_format($total_leftover_qnty,2); ?></th>
                        <th  width="100" align="right" id="value_leftover_amount"><? echo number_format($total_left_over_balanc,2); ?></th>

						<?php
								foreach($trims_tna as $vid=>$key)
									{
									 if( $key==32 || $key==71   || $key==300  || $key==8 || $key==13 || $key==29 || $key==279  || $key==308 || $key==11 || $key==307 || $key==278  || $key==310 || $key==301 || $key==309 || $key==12 || $key==24){?>

								<th width="100">&nbsp;</th>
								<th width="100">&nbsp;</th>
								<th width="100">&nbsp;</th>

						<?}}?>
                    </tfoot>
                </table>

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
                $tot_req_qnty_summary=$tot_wo_qnty_summary=$tot_in_qnty_summary=$in_house_bal=$tot_issue_qnty_summary=$tot_leftover_qnty_summary=0;
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
                        <td width="60" align="center"><? echo $unit_of_measurement[$summary_array[cons_uom][$key_trim]]; ?></td>
                        <td width="80" align="right"><? $app_perc=($summary_array[item_app][$key_trim][app]*100)/$summary_array[item_app][$key_trim][all]; if ($app_perc>=0) echo number_format($app_perc,2); ?></td>
                        <td width="110" align="right"><? echo number_format($summary_array[req_qnty][$key_trim],2); ?></td>
                        <td width="110" align="right"><? echo number_format( $summary_array[wo_qnty][$key_trim],2); ?></td>
                        <td width="80" align="right"><? $wo_per= $summary_array[wo_qnty][$key_trim]/$summary_array[req_qnty][$key_trim]*100; echo number_format($wo_per,2).'%'; ?></td>
                        <td width="110" align="right"><? echo number_format($summary_array[inhouse_qnty][$key_trim],2); ?></td>
                        <td width="80" align="right"><? $in_per=$summary_array[inhouse_qnty][$key_trim]/$summary_array[wo_qnty][$key_trim]*100; echo number_format($in_per,2).'%'; ?></td>
                        <td width="110" align="right"><? echo number_format($summary_array[inhouse_qnty_bl][$key_trim],2); $in_house_bal+=($value['wo']-$value['in']); ?></td>
                        <td width="110" align="right"><? echo number_format($summary_array[issue_qty][$key_trim],2); ?></td>
                        <td width="80" align="right"><? $wo_per=$summary_array[issue_qty][$key_trim]/$summary_array[wo_qnty][$key_trim]*100; echo number_format($wo_per,2).'%'; ?></td>
                        <td align="right"><? echo number_format($summary_array[left_overqty][$key_trim],2); ?></td>
                    </tr>
					<?
                    $z++;
                    if(isset($summary_array[req_qnty][$key_trim]))
					{
						 $tot_req_qnty_summary+=$summary_array[req_qnty][$key_trim];
					}
					if(isset($summary_array[wo_qnty][$key_trim]))
					{
						 $tot_wo_qnty_summary+=$summary_array[wo_qnty][$key_trim];
					}

					if(isset($summary_array[inhouse_qnty][$key_trim]))
					{
						$tot_in_qnty_summary+=$summary_array[inhouse_qnty][$key_trim];
					}
					if(isset($summary_array[inhouse_qnty_bl][$key_trim]))
					{
						$in_house_bal+=$summary_array[inhouse_qnty_bl][$key_trim];
					}

					if(isset($summary_array[issue_qty][$key_trim]))
					{
						$tot_issue_qnty_summary+=$summary_array[issue_qty][$key_trim];
					}

					if(isset($summary_array[left_overqty][$key_trim]))
					{
						 $tot_leftover_qnty_summary+=$summary_array[left_overqty][$key_trim];
					}
				}
				$summary_array=array();
				?>
                <tfoot>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_req_qnty_summary,2); ?>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_wo_qnty_summary,2); ?>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_in_qnty_summary,2); ?>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th align="right"><? echo number_format($in_house_bal,2); ?>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_issue_qnty_summary,2); ?>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_leftover_qnty_summary,2); ?>&nbsp;</th>
                </tfoot>
			</table>
			</div>
		<?
        }
	}
	else if(str_replace("'","",$cbo_search_by)==2) //Style Wise
	{

		if($template==1)
		{
			//ob_start();
			?>
		
			<fieldset style="width:100%;">
                <table width="<?=$tbl_width3;?>">
                <caption> <b style="float:left">Accessories</b></caption>
                    <tr class="form_caption"><td colspan="32" align="center"><? //echo $report_title; ?></td></tr>
                    <tr class="form_caption"><td colspan="32" align="center"><? //echo $company_library[$company_name]; ?></td></tr>
                </table>
                <table class="rpt_table" width="<?=$tbl_width3;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
						
				<tr>
					<th width="100" colspan="38">&nbsp;</th>     
					
					<?
					foreach($tna_trims_task_array as $task_name=>$key)
					{
						$i++;

						if( $task_name==32 || $task_name==71   || $task_name==300  || $task_name==8 || $task_name==13  || $task_name==29 || $task_name==279  || $task_name==308 || $task_name==11 || $task_name==307 || $task_name==278  || $task_name==310 || $task_name==301 || $task_name==309 || $task_name==12 || $task_name==24){

						if(count($tna_task_array)==$i){ echo '<th width="100" colspan="3" title="'.$task_name.'='.$tna_task_name[$task_name].'">'. $key." <br><small>".$tna_task_group_by_id[$task_name]."</small> </th>"; }else{ echo '<th width="100" colspan="3" title="'.$task_name.'='.$tna_task_name[$task_name].'">'.$key." <br><small>".$tna_task_group_by_id[$task_name]."</small> </th>";

						}}
						
					}?>


				</tr>
                   
						<tr>
                        <th width="30">SL</th>
                        <th width="50">Buyer</th>
                        <th width="50">Season</th>
                        <th width="100">Job No</th>
                        <th width="100">Style Ref</th>
                        <th width="100">Internal Ref</th>
                        <th width="100">File No</th>
                        <th width="90">Order No</th>
                        <th width="80">Order Qty</th>
                        <th width="50">UOM</th>
                        <th width="80">Qty (Pcs)</th>
                        <th width="80">Shipment Date</th>
                        <th width="100">Trims Name</th>
                        <th width="140">Item Description</th>
						<th width="100">Remarks</th>
                        <th width="100">Brand/Sup Ref</th>
						<th width="100">Material Source</th>
                        <th width="60">Appr Req.</th>
                        <th width="80">Approve Status</th>
                        <th width="100">Item Entry Date</th>
                        <th width="80">Avg. Cons</th>
                        <th width="100">Req Qty</th>
                        <th width="100">Pre Costing Value</th>
                        <th width="90">WO Qty</th>
                        <th width="60">Trims UOM</th>
                        <th width="100">WO Value (USD)</th>
                        <th width="150">Supplier</th>
                        <th width="70">WO Delay Days</th>
                        <th width="70">PI No.</th>
						<th width="100">BTB LC No</th>
						<th width="100">BTB LC Date</th>
                        <th width="90">In-House Qty</th>
                        <th width="90">In-House Amount</th>
                        <th width="90">Receive Balance</th>
                        <th width="90">Issue to Prod.</th>
                        <th width="90">Issue Amount</th>
                        <th width="90">Left Over/Balance</th>
                        <th  width="100">Left Over/Balance Amount</th>
						<?php
					 foreach($trims_tna as $vid=>$key)
					 {
					  if( $key==32 || $key==71  || $key==300  || $key==8 || $key==13 || $key==29 || $key==279  || $key==308 || $key==11 || $key==307 || $key==278  || $key==310 || $key==301 || $key==309 || $key==12 || $key==24){?>


					<th width="100">Plan End Date</th>
					<th width="100">Actual End Date</th>
					<th width="100">Delay/ Early</th>
					<?}}?>
				</tr>
                    </thead>
                </table>
                <div style="width:<?=$tbl_width3+20;?>px; max-height:400px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="<?=$tbl_width3;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
			<?

			$conversion_factor_array=array(); $item_arr=array();
			$conversion_factor=sql_select("select id ,trim_uom,order_uom,conversion_factor from  lib_item_group  where status_active=1 and item_category=4");
			foreach($conversion_factor as $row_f)
			{
				$conversion_factor_array[$row_f[csf('id')]]['con_factor']=$row_f[csf('conversion_factor')];
				$conversion_factor_array[$row_f[csf('id')]]['cons_uom']=$row_f[csf('trim_uom')];
				$item_arr[$row_f[csf('id')]]['order_uom']=$row_f[csf('order_uom')];
			}
			unset($conversion_factor);
			$app_sql=sql_select("select job_no_mst,accessories_type_id,approval_status from wo_po_trims_approval_info");
			$app_status_arr=array();
			foreach($app_sql as $row)
			{
				$app_status_arr[$row[csf("job_no_mst")]][$row[csf("accessories_type_id")]]=$row[csf("approval_status")];
			}
			unset($app_sql);

			$sql_po_qty_country_wise_arr=array();
			$po_job_arr=array(); $style_po_qty_arr=array();
 			$sql_po_qty_country_wise=sql_select("select  b.id, b.job_no_mst, c.country_id, sum(c.order_quantity/a.total_set_qnty) as order_quantity_set, sum(c.order_quantity) as order_quantity  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst  and b.id=c.po_break_down_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $year_cond group by b.id, b.job_no_mst, c.country_id order by b.id, b.job_no_mst, c.country_id");
			//echo "select  b.id, b.job_no_mst, c.country_id, sum(c.order_quantity/a.total_set_qnty) as order_quantity_set, sum(c.order_quantity) as order_quantity  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst  and b.id=c.po_break_down_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $year_cond group by b.id, b.job_no_mst, c.country_id order by b.id, b.job_no_mst, c.country_id";
			foreach( $sql_po_qty_country_wise as $row)
			{
				$sql_po_qty_country_wise_arr[$row[csf('id')]][$row[csf('country_id')]]=$row[csf('order_quantity_set')];
				$po_job_arr[$row[csf('id')]]=$row[csf('job_no_mst')];
				$style_po_qty_arr[$row[csf('job_no_mst')]]['order_qty_set']+=$row[csf('order_quantity_set')];
				$style_po_qty_arr[$row[csf('job_no_mst')]]['po_qty']+=$row[csf('order_quantity')];
			}
			//print_r($style_po_qty_arr);
			unset($sql_po_qty_country_wise);


			

			$style_data_arr=array();
			$po_id_string="";
			$today=date("Y-m-d");
			 $sql_pos=("select a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.order_uom, b.file_no, b.grouping, b.id, b.po_number, sum(c.order_quantity) as order_quantity, sum(c.order_quantity/a.total_set_qnty) as order_quantity_set, b.pub_shipment_date , a.season_buyer_wise
			from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
			where
			a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.company_name=$company_name $buyer_id_cond ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $date_cond $style_ref_cond $jobcond  $ordercond $file_no_cond $internal_ref_cond  $year_cond $season_con 
			group by a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.order_uom, a.total_set_qnty, b.file_no, b.grouping, b.id, b.po_number, b.pub_shipment_date  , a.season_buyer_wise order by b.id");
			//echo $sql_pos; //and a.job_no='FAL-16-00179'
			$sql_po=sql_select($sql_pos);
			$tot_rows=0;  $style_data_all=array();
			foreach($sql_po as $row)
			{
				$tot_rows++;

				$style_data[$row[csf('job_no')]]['job_data']=$row[csf("buyer_name")]."##".$row[csf("job_no_prefix_num")]."##".$row[csf("style_ref_no")]."##".$row[csf("order_uom")]."##".$row[csf('season_buyer_wise')];

				$style_data_all[$row[csf('job_no')]].=$row[csf("file_no")]."__".$row[csf("grouping")]."__".$row[csf("po_number")]."__".$row[csf("pub_shipment_date")]."__".$row[csf("shiping_status")]."__".$row[csf("id")]."***";

				$po_arr[$row[csf('job_no')]]['order_quantity']+=$row[csf('order_quantity')];
				$po_arr[$row[csf('job_no')]]['order_quantity_set']+=$row[csf('order_quantity_set')];
				$po_id_string.=$row[csf('id')].",";
			}
		
			unset($sql_po);
			$po_id_string=rtrim($po_id_string,",");
			//	print_r($po_id_string); die;
			if($po_id_string=="")
			{
				echo "<strong style='color:red;font-size:30px'>No Data found</strong>";
				die;
			}


			$poIds=chop($po_id_string,','); $order_cond=""; $order_cond1=""; $order_cond2=""; $precost_po_cond="";
			if($db_type==2 && $tot_rows>1000)
			{
				$order_cond=" and (";
				$order_cond1=" and (";
				$order_cond2=" and (";
				$precost_po_cond=" and (";
				$poIdsArr=array_chunk(explode(",",$poIds),999);
				foreach($poIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					//$poIds_cond.=" po_break_down_id in($ids) or ";
					$order_cond.=" b.po_break_down_id in($ids) or";
					$order_cond1.=" b.po_breakdown_id in($ids) or";
					$order_cond2.=" d.po_breakdown_id in($ids) or";
					$precost_po_cond.=" c.po_break_down_id in($ids) or";
				}
				$order_cond=chop($order_cond,'or ');
				$order_cond.=")";
				$order_cond1=chop($order_cond1,'or ');
				$order_cond1.=")";
				$order_cond2=chop($order_cond2,'or ');
				$order_cond2.=")";
				$precost_po_cond=chop($precost_po_cond,'or ');
				$precost_po_cond.=")";
			}
			else
			{
				$order_cond=" and b.po_break_down_id in($poIds)";
				$order_cond1=" and b.po_breakdown_id in($poIds)";
				$order_cond2=" and d.po_breakdown_id in($poIds)";
				$precost_po_cond=" and c.po_break_down_id in($poIds)";
			}
			if(str_replace("'","",$cbo_item_group)=="")
			{
				$trm_group_pre_cost_cond="";
				$trm_group_without_precost_cond="";
				$trm_group_rec_cond="";
				$trm_group_recrtn_cond="";
				$trm_group_iss_cond="";
			}
			else
			{
				$trm_group_pre_cost_cond="and b.trim_group in(".str_replace("'","",$cbo_item_group).")";
				$trm_group_without_precost_cond="and b.trim_group in(".str_replace("'","",$cbo_item_group).")";
				$trm_group_rec_cond="and a.item_group_id in(".str_replace("'","",$cbo_item_group).")";
				$trm_group_recrtn_cond="and c.item_group_id in(".str_replace("'","",$cbo_item_group).")";
				$trm_group_iss_cond="and a.item_group_id in(".str_replace("'","",$cbo_item_group).")";
			}

			$condition= new condition();
			
			if($txt_style_id!="")
			{
				$condition->jobid_in("$txt_style_id"); 
			}
			else {
					if(str_replace("'","",$txt_job_no) !='')
					{
					$condition->job_no_prefix_num("in($txt_job_no)"); 
				  }
				  if(str_replace("'","",trim($txt_style_ref))!='')
					{
						$style_ref=str_replace("'","",trim($txt_style_ref));
						$condition->style_ref_no("like '%$style_ref%'");
					}
		    }
			
			
			if($order_no_id!="")
			{
					$condition->po_id("in($order_no_id)");
			}
			elseif(str_replace("'","",$txt_order_no)!='')
			{
				$order_nos=str_replace("'","",$txt_order_no);
				$condition->po_number(" like '%$order_nos%'");
			}
			//$txt_style_refs="'".trim($txt_style_ref)."'";
			

			if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
			{
				$start_date=(str_replace("'","",$txt_date_from));
				$end_date=(str_replace("'","",$txt_date_to));
			}

			if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
			{
				  $condition->country_ship_date(" between '$start_date' and '$end_date'");
			}			

			if($file_no!="")
			{
				$condition->file_no("=$file_no"); 
			}
		    
			if(str_replace("'","",$cbo_buyer_name)!=0)
			{
				$condition->buyer_name("=$cbo_buyer_name"); 
			}
		    
			if(str_replace("'","",$cbo_season_id)!=0)
			{
				$condition->season("=$cbo_season_id"); 
			}
			$condition->init();
			$trim= new trims($condition);
			//echo $trim->getQuery();die;
			//$trim_qty=$trim->getQtyArray_by_orderAndPrecostdtlsid();
			//$trim_qty=$trim->getQtyArray_by_orderCountryAndPrecostdtlsid();
			$trim_qty=$trim->getQtyArray_by_jobAndPrecostdtlsid();
			//print_r($trim_qty);
			$trim= new trims($condition);
			//$trim_amount=$trim->getAmountArray_by_orderAndPrecostdtlsid();
			$trim_amount=$trim->getAmountArray_by_jobAndPrecostdtlsid();
			$order_by = ($cbo_season_id !=0) ? " c.po_break_down_id" : " b.trim_group";
			$costing_arr=array();
		 $sql_pre="select a.costing_per, a.costing_date, b.id as trim_dtla_id, b.trim_group, b.remark, b.description, b.brand_sup_ref, b.cons_uom, b.cons_dzn_gmts, b.rate, b.amount, b.apvl_req, b.nominated_supp, b.insert_date, c.cons, c.country_id, c.cons as cons_cal, c.po_break_down_id, b.job_no,b.source_id			from wo_pre_cost_mst a, wo_pre_cost_trim_cost_dtls b, wo_pre_cost_trim_co_cons_dtls c
			where c.job_no=b.job_no and b.id=c.wo_pre_cost_trim_cost_dtls_id and a.job_no=b.job_no and c.cons>0 and b.status_active=1 and a.status_active=1 and c.status_active=1 $trm_group_pre_cost_cond $precost_po_cond
			group by a.costing_per, a.costing_date, b.id, b.trim_group, b.remark, b.description, b.brand_sup_ref, b.cons_uom, b.cons_dzn_gmts, b.rate, b.amount, b.apvl_req, b.nominated_supp, b.insert_date, c.cons, c.pcs, c.country_id, c.po_break_down_id, b.job_no,b.source_id order by $order_by";
			
			$sql_pre_cost=sql_select($sql_pre);

			//echo $sql_pre_cost; die;
			if(count($sql_pre_cost)>0)
			{
				foreach($sql_pre_cost as $rowp)
				{
					$dzn_qnty=0;

					if($rowp[csf('costing_per')]==1) $dzn_qnty=12;
					else if($rowp[csf('costing_per')]==3) $dzn_qnty=12*2;
					else if($rowp[csf('costing_per')]==4) $dzn_qnty=12*3;
					else if($rowp[csf('costing_per')]==5) $dzn_qnty=12*4;
					else $dzn_qnty=1;

					$po_qty=0;  $req_value=0;//$req_qnty=0;
					if($rowp[csf('country_id')]==0)
					{
						$po_qty=$po_arr[$rowp[csf('job_no')]]['order_quantity'];
						//$req_qnty+=$trim_qty[$rowp[csf('po_break_down_id')]][$rowp[csf('country_id')]][$rowp[csf('trim_dtla_id')]];
					}
					else
					{
						$country_id= explode(",",$rowp[csf('country_id')]);
						for($cou=0;$cou<=count($country_id); $cou++)
						{
							$po_qty+=$sql_po_qty_country_wise_arr[$rowp[csf('po_break_down_id')]][$country_id[$cou]];
							//$req_qnty+=$trim_qty[$rowp[csf('po_break_down_id')]][$country_id[$cou]][$rowp[csf('trim_dtla_id')]];
						}

					}
					$req_qnty=$trim_qty[$rowp[csf('job_no')]][$rowp[csf('trim_dtla_id')]];
					$req_value=$trim_amount[$rowp[csf('job_no')]][$rowp[csf('trim_dtla_id')]];

					$style_data_arr[$rowp[csf('job_no')]]['trim_dtla_id'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('trim_dtla_id')];// for rowspan
					$style_data_arr[$rowp[csf('job_no')]]['trim_group'][$rowp[csf('trim_group')]]=$rowp[csf('trim_group')];
					$style_data_arr[$rowp[csf('job_no')]][$rowp[csf('trim_group')]][$rowp[csf('trim_dtla_id')]]=$rowp[csf('trim_dtla_id')]; // for rowspannn
					$style_data_arr[$rowp[csf('job_no')]]['trim_group_dtls'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('trim_group')];
					$style_data_arr[$rowp[csf('job_no')]]['remark'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('remark')];
					$style_data_arr[$rowp[csf('job_no')]]['brand_sup_ref'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('brand_sup_ref')];
					$style_data_arr[$rowp[csf('job_no')]]['apvl_req'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('apvl_req')];
					$style_data_arr[$rowp[csf('job_no')]]['insert_date'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('insert_date')];
					$style_data_arr[$rowp[csf('job_no')]]['req_qnty'][$rowp[csf('trim_dtla_id')]]+=$req_qnty;
					$style_data_arr[$rowp[csf('job_no')]]['req_value'][$rowp[csf('trim_dtla_id')]]+=$req_value;
					$style_data_arr[$rowp[csf('job_no')]]['cons_uom'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('cons_uom')];
					$style_data_arr[$rowp[csf('job_no')]]['trim_group_from'][$rowp[csf('trim_dtla_id')]]="Pre_cost";
					$style_data_arr[$rowp[csf('job_no')]]['rate'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('rate')];
					$style_data_arr[$rowp[csf('job_no')]]['description'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('description')];
					$style_data_arr[$rowp[csf('job_no')]]['country_id'][$rowp[csf('trim_dtla_id')]].=$rowp[csf('country_id')].',';
					$style_data_arr[$rowp[csf('job_no')]]['avg_cons'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('cons_dzn_gmts')];
					$style_data_arr[$rowp[csf('job_no')]]['source_id'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('source_id')];
	
					$costing_arr[$rowp[csf('job_no')]]['costing_per']=$rowp[csf('costing_per')];
					$costing_arr[$rowp[csf('job_no')]]['costing_date']=$rowp[csf('costing_date')];
				}
			}
			else
			{
				echo "<strong style='color:red;font-size:30px'>No Data found</strong>";
				die;
			}
			unset($sql_pre_cost);
		//	print_r($style_data_arr);

			if($db_type==2)
			{

				$sql_without_precost=sql_select("select min(a.booking_date) as booking_date, b.job_no, LISTAGG(CAST(a.booking_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as booking_no, LISTAGG(CAST(a.supplier_id || '**' || a.pay_mode AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as supplier_id, b.po_break_down_id, b.trim_group, b.pre_cost_fabric_cost_dtls_id, sum(b.wo_qnty) as wo_qnty, sum(b.amount/b.exchange_rate) as amount, sum(b.rate) as rate, LISTAGG(CAST(b.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as booking_dtls_id
				from wo_booking_mst a, wo_booking_dtls b
				where a.item_category=4 and a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name $order_cond $item_group_cond2
				group by b.po_break_down_id,b.trim_group,b.job_no,b.pre_cost_fabric_cost_dtls_id");//and item_from_precost=2
			}
			else if($db_type==0)
			{
				$sql_without_precost=sql_select("select min(a.booking_date) as booking_date ,b.job_no, group_concat(a.booking_no) as booking_no, group_concat(concat_ws('**',a.supplier_id,a.pay_mode)) as supplier_id, b.po_break_down_id, b.trim_group,b.pre_cost_fabric_cost_dtls_id,sum(b.wo_qnty) as wo_qnty,sum(b.amount/b.exchange_rate) as amount,sum(b.rate) as rate, group_concat(b.id) as booking_dtls_id
				from wo_booking_mst a, wo_booking_dtls b
				where a.item_category=4 and a.booking_no=b.booking_no  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name $order_cond  $item_group_cond2
				group by b.po_break_down_id,b.trim_group,b.job_no,b.pre_cost_fabric_cost_dtls_id");//and item_from_precost=2
			}
			//print_r($sql_without_precost);
			$style_data_arr1=array();

			foreach($sql_without_precost as $wo_row_without_precost)
			{
				$conversion_factor_rate=$conversion_factor_array[$wo_row_without_precost[csf('trim_group')]]['con_factor'];
				//$cons_uom=$item_arr[$wo_row_without_precost[csf('trim_group')]]['order_uom'];
				$cons_uom=$conversion_factor_array[$wo_row_without_precost[csf('trim_group')]]['cons_uom'];
				$booking_no=implode(",",array_unique(explode(",",$wo_row_without_precost[csf('booking_no')])));
				$supplier_id=implode(",",array_unique(explode(",",$wo_row_without_precost[csf('supplier_id')])));
				$wo_qnty=$wo_row_without_precost[csf('wo_qnty')];//*$conversion_factor_rate;
				$amount=$wo_row_without_precost[csf('amount')];
				$wo_date=$wo_row_without_precost[csf('booking_date')];

				$booking_id_arr=array_unique(explode(",",$wo_row_without_precost[csf('booking_dtls_id')]));
				foreach($booking_id_arr as $book_id)
				{
					$booking_precost_id[$book_id]=$wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')];
				}

				if($wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')] =="" || $wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')] ==0)
				{
					//echo $wo_row_without_precost[csf('trim_group')];
					$trim_dtla_id=max($style_data_arr[$wo_row_without_precost[csf('job_no')]]['trim_dtla_id'])+1;
					$style_data_arr[$wo_row_without_precost[csf('job_no')]]['trim_dtla_id'][$trim_dtla_id]=$trim_dtla_id;// for rowspan
					$style_data_arr[$wo_row_without_precost[csf('job_no')]]['trim_group'][$wo_row_without_precost[csf('trim_group')]]=$wo_row_without_precost[csf('trim_group')];
					$style_data_arr[$wo_row_without_precost[csf('job_no')]][$wo_row_without_precost[csf('trim_group')]][$trim_dtla_id]=$trim_dtla_id;// for rowspannn
					$style_data_arr[$wo_row_without_precost[csf('job_no')]]['trim_group_dtls'][$trim_dtla_id]=$wo_row_without_precost[csf('trim_group')];
					$style_data_arr[$wo_row_without_precost[csf('job_no')]]['cons_uom'][$trim_dtla_id]=$cons_uom;

					$style_data_arr[$wo_row_without_precost[csf('job_no')]]['trim_group_from'][$trim_dtla_id]="Booking Without Pre_cost";
				}
				else
				{
					$trim_dtla_id=$wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')];
				}
				$style_data_arr[$wo_row_without_precost[csf('job_no')]]['wo_qnty'][$trim_dtla_id]+=$wo_qnty;
				$style_data_arr[$wo_row_without_precost[csf('job_no')]]['amount'][$trim_dtla_id]+=$amount;
				$style_data_arr[$wo_row_without_precost[csf('job_no')]]['wo_date'][$trim_dtla_id]=$wo_date;
				$style_data_arr[$wo_row_without_precost[csf('job_no')]]['wo_qnty_trim_group'][$wo_row_without_precost[csf('trim_group')]]+=$wo_qnty;


				$style_data_arr2[$wo_row_without_precost[csf('job_no')]]['booking_no'][$trim_dtla_id].=$booking_no.",";
				$style_data_arr[$wo_row_without_precost[csf('job_no')]]['booking_no'][$trim_dtla_id].=$booking_no.",";
				$style_data_arr[$wo_row_without_precost[csf('job_no')]]['supplier_id'][$trim_dtla_id].=$wo_row_without_precost[csf('supplier_id')].",";
				$style_data_arr[$wo_row_without_precost[csf('job_no')]]['conversion_factor_rate'][$trim_dtla_id]=$conversion_factor_rate;
			}
			unset($sql_without_precost);
			//die;
			$receive_qty_data=sql_select("select b.po_breakdown_id, a.item_group_id, b.quantity as quantity, b.order_rate as rate, c.exchange_rate
			from  inv_receive_master c,product_details_master d, inv_trims_entry_dtls a, order_wise_pro_details b
			where a.mst_id=c.id and a.trans_id=b.trans_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $order_cond1  $item_group_cond3  ");


			foreach($receive_qty_data as $k => $row)
			{
				if($style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['trim_group'][$row[csf('item_group_id')]]=="" || $style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['trim_group'][$row[csf('item_group_id')]]==0)
				{
					//echo $row[csf('item_group_id')];
					$cons_uom=$conversion_factor_array[$row[csf('item_group_id')]]['cons_uom'];

					$trim_dtla_id=max($style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['trim_dtla_id'])+1;
					$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['trim_dtla_id'][$trim_dtla_id]=$trim_dtla_id;// for rowspan
					$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['trim_group'][$row[csf('item_group_id')]]=$row[csf('item_group_id')];
					$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][$row[csf('item_group_id')]][$trim_dtla_id]=$trim_dtla_id;// for rowspannn
					$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['trim_group_dtls'][$trim_dtla_id]=$row[csf('item_group_id')];
					$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['cons_uom'][$trim_dtla_id]=$cons_uom;

					$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['trim_group_from'][$trim_dtla_id]="Trim Receive";
				}
				$amount=0;  $amount=($row[csf('quantity')]*$row[csf('rate')]);
                if($row[csf('rate')]  > 0){
                    $style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['inhouse_rate'][$row[csf('item_group_id')]][$k]=$row[csf('rate')];
                }
				$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['inhouse_qnty'][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['inhouse_amount'][$row[csf('item_group_id')]]+=$amount;//$row[csf('amount')];
			}

			unset($receive_qty_data);
			//die;
			/*$receive_rtn_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id,sum(d.quantity) as quantity
			from inv_issue_master a,inv_transaction b,product_details_master c,order_wise_pro_details d,inv_receive_master e
			where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and e.id=a.received_id and b.transaction_type=3 and a.entry_form=49 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_cond2  group by d.po_breakdown_id, c.item_group_id order by c.item_group_id");*/
			$receive_rtn_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id, d.quantity as quantity, d.order_rate as rate
			from product_details_master c, order_wise_pro_details d
			where d.prod_id=c.id and d.trans_type=3 and d.entry_form=49 and d.status_active=1 and d.is_deleted=0 $order_cond2");
			foreach($receive_rtn_qty_data as $row)
			{
				$receive_rtn_amount=0;
				//$conv_quantity=$row[csf('quantity')]/$conversion_factor_array[$row[csf('item_group_id')]]['con_factor'];
				$conv_quantity=$row[csf('quantity')];
				$receive_rtn_amount=$conv_quantity*$row[csf('rate')];
				$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['receive_rtn_qty'][$row[csf('item_group_id')]]+=$conv_quantity;
				$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['receive_rtn_amount'][$row[csf('item_group_id')]]+=$receive_rtn_amount;
			}
			//echo "<pre>";print_r($style_data_arr);
			unset($receive_rtn_qty_data);

			$transfer_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id, sum((case when d.trans_type=5 then d.quantity else 0 end)-(case when d.trans_type=6 then d.quantity else 0 end)) as quantity ,
			sum(case when d.trans_type=5 then d.quantity else 0 end) as in_qty,
			sum(case when d.trans_type=6 then d.quantity else 0 end) as out_qty,
			sum(case when d.trans_type=5 then (d.quantity*d.order_rate) else 0 end) as in_amount,
			sum(case when d.trans_type=6 then (d.quantity*d.order_rate) else 0 end) as out_amount
			from product_details_master c,order_wise_pro_details d
			where d.prod_id=c.id and d.trans_type in(5,6) and d.entry_form=78 and d.status_active=1 and d.is_deleted=0 $order_cond2 group by d.po_breakdown_id, c.item_group_id");
			foreach($transfer_qty_data as $row)
			{
				$transfe_amount=0;
				$transfe_amount=$row[csf('in_amount')]-$row[csf('out_amount')];
				$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['transfe_qty'][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['transfe_in'][$row[csf('item_group_id')]]+=$row[csf('in_qty')];
				$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['transfe_out'][$row[csf('item_group_id')]]+=$row[csf('out_qty')];

				$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['transfe_amount'][$row[csf('item_group_id')]]+=$transfe_amount;
				$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['transfe_in_amount'][$row[csf('item_group_id')]]+=$row[csf('in_amount')];
				$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['transfe_out_amount'][$row[csf('item_group_id')]]+=$row[csf('out_amount')];
			}
			unset($transfer_qty_data);

			$issue_qty_data=sql_select("select b.po_breakdown_id, p.item_group_id, sum(b.quantity) as quantity, sum(b.quantity*b.order_rate) as amount
			from  inv_issue_master d,product_details_master p,inv_trims_issue_dtls a , order_wise_pro_details b
			where a.mst_id=d.id and a.prod_id=p.id and a.trans_id=b.trans_id and b.trans_type=2 and d.entry_form=25 and b.entry_form=25 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $order_cond1  group by b.po_breakdown_id, p.item_group_id");

			foreach($issue_qty_data as $row)
			{
				$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['issue_qty'][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['issue_amount'][$row[csf('item_group_id')]]+=$row[csf('amount')];
			}

			unset($issue_qty_data);

			$issue_rtn_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id, d.quantity as quantity, (d.quantity*d.order_rate) as amount
			from product_details_master c,order_wise_pro_details d
			where d.prod_id=c.id and d.trans_type=4 and d.entry_form=73 and d.status_active=1 and d.is_deleted=0 $order_cond2");
			foreach($issue_rtn_qty_data as $row)
			{
				$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['issue_rtn_qty'][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['issue_rtn_amount'][$row[csf('item_group_id')]]+=$row[csf('amount')];
			}
			unset($issue_rtn_qty_data);

			$sql_wo_pi=sql_select("select a.pi_number, b.work_order_no, b.work_order_dtls_id from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=4 and a.status_active=1 and a.importer_id=$company_name and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.work_order_dtls_id>0");
			$pi_arr=array();
			foreach($sql_wo_pi as $rowPi)
			{
				if($tem_pi[$rowPi[csf('work_order_no')]]=="")
				{
					$tem_pi[$rowPi[csf('work_order_no')]]=$rowPi[csf('pi_number')];
					$pi_arr[$rowPi[csf('work_order_no')]].=$rowPi[csf('pi_number')].'**';
				}


				/*if($tem_pi[$rowPi[csf('pi_number')]][$booking_precost_id[$rowPi[csf('work_order_dtls_id')]]]=="")
				{
					$tem_pi[$rowPi[csf('pi_number')]][$booking_precost_id[$rowPi[csf('work_order_dtls_id')]]]=$rowPi[csf('pi_number')];
					$pi_arr[$booking_precost_id[$rowPi[csf('work_order_dtls_id')]]].=$rowPi[csf('pi_number')].'**';
				}*/

			}
			unset($sql_wo_pi);

			/*$sql_wo_pi=sql_select("select a.pi_number, b.work_order_no from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=4 and a.status_active=1 and a.importer_id=$company_name and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pi_number, b.work_order_no");
			$pi_arr=array();
			foreach($sql_wo_pi as $rowPi)
			{
				$pi_arr[$rowPi[csf('work_order_no')]].=$rowPi[csf('pi_number')].'**';
			}*/


			$total_pre_costing_value=0;
			$total_wo_value=0;
			$total_left_over_balanc=0;
			$total_issue_amount=0;
			$total_rec_bal_qnty=0;
			$summary_array=array();
			$i=1;
		//			 echo "<pre>";
		//			 print_r($style_data_arr);
			foreach($style_data_arr as $key=>$value)
			{
				$rowspan=0;
				$rowspan=count($value['trim_dtla_id']);
				//echo array_sum($value[order_quantity_set]).',';

				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				//echo $value[booking_no][$key_trim1];
				$job=$key;
				$job_data=explode('##',$style_data[$job]['job_data']);

				$style_po_data=explode('***',$style_data_all[$job]);

				$file_no_all=""; $grouping_all=""; $po_no_all=""; $ship_date_all=""; $ship_status_all=""; $po_id_all='';
				foreach($style_po_data as $po_data)
				{
					$ex_po_data=explode('__',$po_data);

					if($file_no_all=="") $file_no_all=$ex_po_data[0]; else $file_no_all.=','.$ex_po_data[0];
					if($grouping_all=="") $grouping_all=$ex_po_data[1]; else $grouping_all.=','.$ex_po_data[1];
					if($po_no_all=="") $po_no_all=$ex_po_data[2]; else $po_no_all.=','.$ex_po_data[2];
					if($ship_date_all=="") $ship_date_all=change_date_format($ex_po_data[3]); else $ship_date_all.=','.change_date_format($ex_po_data[3]);
					if($ship_status_all=="") $ship_status_all=$ex_po_data[4]; else $ship_status_all.=','.$ex_po_data[4];
					if($po_id_all=="") $po_id_all=$ex_po_data[5]; else $po_id_all.=','.$ex_po_data[5];
				}

				$file_no=implode(', ',array_filter(array_unique(explode(',',$file_no_all))));
				$grouping=implode(', ',array_filter(array_unique(explode(',',$grouping_all))));
				$po_no=implode(', ',array_filter(array_unique(explode(',',$po_no_all))));
				$ship_date=implode(', ',array_filter(array_unique(explode(',',$ship_date_all))));
				$ship_status=implode(', ',array_filter(array_unique(explode(',',$ship_status_all))));
				$poId_all=implode(', ',array_filter(array_unique(explode(',',$po_id_all))));
				//print_r($style_po_arr );

				if($rowspan!=0)
				{
				?>
				<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                    <td width="30" title="<? echo $po_qty; ?>" rowspan="<? echo $rowspan; ?>"><p><? echo $i; ?></p></td>
                    <td width="50" rowspan="<? echo $rowspan; ?>"><p style="word-break:break-all"><? echo $buyer_short_name_library[$job_data[0]]; ?></p></td>
                    <td width="50" rowspan="<? echo $rowspan; ?>"><p style="word-break:break-all"><? echo $lib_season_arr[$job_data[4]]; ?></p></td>
                    <td width="100" align="center" rowspan="<? echo $rowspan; ?>"><p style="word-break:break-all"><? echo $job_data[1]; ?></p></td>
                    <td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo $job_data[2]; ?></p></td>
                    <td width="100" rowspan="<? echo $rowspan; ?>"><p style="word-break:break-all"><? echo $grouping; ?></p></td>
                    <td width="100" rowspan="<? echo $rowspan; ?>"><p style="word-break:break-all"><? echo $file_no; ?></p></td>
                    <td width="90" rowspan="<? echo $rowspan; ?>"><p>
                    <a href='#report_details' onclick="generate_report('<? echo $company_name; ?>','<? echo $job; ?>', '<? echo $job_data[0]; ?>','<? echo $job_data[2]; ?>','<? echo change_date_format($costing_arr[$job]['costing_date']); ?>','<? echo rtrim($po_id_all,','); ?>','<? echo $costing_arr[$job]['costing_per']; ?>','<? echo $print_button_action;?>');"> <? $po_number=$po_no; $po_id=$poId_all; echo $po_number;
						$order_quantity_set=$style_po_qty_arr[$job]['order_qty_set'];
						$po_qty_pcs=$style_po_qty_arr[$job]['po_qty'];
						?></a></p>

                        </td>
                    <td width="80" align="right" rowspan="<? echo $rowspan; ?>"><p><a href='#report_details' onclick="order_qty_popup('<? echo $company_name; ?>','<? echo $job; ?>','<? echo $po_id; ?>', '<? echo $job_data[0]; ?>', <? echo $txt_date_from; ?>, <? echo $txt_date_to; ?> ,'order_qty_data');"><? echo number_format($order_quantity_set,0,'.',''); ?></a></p></td>

                    <td width="50" align="center" rowspan="<? echo $rowspan; ?>"><p><? echo $unit_of_measurement[$job_data[3]]; ?></p></td>
                    <td width="80" align="right" rowspan="<? echo $rowspan; ?>"><p><? echo number_format($po_qty_pcs,0,'.',''); ?></p></td>
                    <td width="80" align="center" rowspan="<? echo $rowspan; ?>"><p style="word-break:break-all"><? $pub_shipment_date=$ship_date; echo $pub_shipment_date; ?></p></td>
					<?
                    //print_r( $value[trim_group]);
                    foreach($value['trim_group'] as $key_trim=>$value_trim)
                    {
						$summary_array['trim_group'][$key_trim]=$key_trim;
						$gg=1;
						foreach($value[$key_trim] as $key_trim1=>$value_trim1)
						{
							$rowspannn=count($value[$key_trim]);
						//	print($key_trim1).',';
							if($gg==1)
							{
								$booking_no_arr=array_unique(explode(',',$style_data_arr2[$job]['booking_no'][$key_trim1]));
								//$booking_no_arr=$style_data_arr2[$job][booking_no][$key_trim1];
								/*$piWoNo='';
								foreach($booking_no_arr as $booking_no1)
								{
									//if($main_booking_no_large_data=="") $main_booking_no_large_data=$booking_no1; else $main_booking_no_large_data.=",".$booking_no1;
									if($booking_no1!="")
									{
										if($piWoNo=="") $piWoNo=implode(',',array_filter(array_unique(explode("**",$pi_arr[$booking_no1])))); else $piWoNo.=",".implode(',',array_filter(array_unique(explode("**",$pi_arr[$booking_no1]))));//
									}
								}*/
								?>
                                <td width="100" title="<? echo $value['trim_group_from'][$key_trim1]; ?>"><p><? echo $item_library[$value['trim_group_dtls'][$key_trim1]]; ?></p></td>
                                <td width="140"><p style="word-break:break-all"><? echo $value['description'][$key_trim1]; ?></p></td>
								<td width="100"><p style="word-break:break-all"><? echo $value['remark'][$key_trim1]; ?></p></td>
								<td width="100"><p style="word-break:break-all"><? echo $value['brand_sup_ref'][$key_trim1]; ?></p></td>
                                <td width="100"><p style="word-break:break-all"><? echo $commission_particulars[$value['source_id'][$key_trim1]]; ?></p></td>
                                <td width="60" align="center"><p><? if($value['apvl_req'][$key_trim1]==1) echo "Yes"; else echo ""; ?></p></td>
                                <td width="80" align="center"><p><?
                                if($value['apvl_req'][$key_trim1]==1)
                                {
									$app_status=$app_status_arr[$job][$value['trim_group_dtls'][$key_trim1]];
									$approved_status=$approval_status[$app_status];
									$summary_array['item_app'][$key_trim][all]+=1;
									if($app_status==3)
									{
										$summary_array['item_app'][$key_trim][app]+=1;
									}
                                }
                                else
                                {
                                	$approved_status="";
                                } echo $approved_status; ?></p></td>

                                <td width="100" align="center"><p><? $insert_date=explode(" ",$value['insert_date'][$key_trim1]); echo change_date_format($insert_date[0],'','',''); ?></p></td>
                                <td width="80" align="right"><?=number_format($value['avg_cons'][$key_trim1],4); ?></td>
                                <?
								$booking_no_arr=array_unique(explode(',',rtrim($value['booking_no'][$key_trim1],",")));

                                $main_booking_no_large_data="";
                                foreach($booking_no_arr as $booking_no1)
                                {
                                	if($main_booking_no_large_data=="") $main_booking_no_large_data=$booking_no1; else $main_booking_no_large_data.=",".$booking_no1;
                                }
								$country_id=implode(',',array_filter(array_unique(explode(',',$value['country_id'][$key_trim1]))));
								?>
                                <td width="100" title="<? echo $job.'='.$key_trim1;?>" align="right"><p><a href='#report_details' onclick="order_req_qty_popup('<? echo $company_name; ?>','<? echo $job; ?>','<? echo $po_id; ?>', '<? echo $job_data[0]; ?>','<? echo $value['rate'][$key_trim1]; ?>','<? echo $value['trim_group_dtls'][$key_trim1];?>' ,'<? echo $main_booking_no_large_data;?>','<? echo $value['description'][$key_trim1] ;?>','<? echo rtrim($country_id,",");?>','<? echo $value['trim_dtla_id'][$key_trim1];?>','<? echo $start_date ?>','<? echo $end_date ?>','order_req_qty_data');">
                                <?
								//$req_qnty=number_format($value['req_qnty'][$key_trim1],2,'.','');
								$trim_req_value=$trim_amount[$job][$key_trim1];
								$trim_req_qnty=$trim_qty[$job][$key_trim1];
								$req_qty=number_format($trim_req_qnty,2,'.',''); echo $req_qty; $summary_array['req_qnty'][$key_trim]+=$req_qty;//$value['req_qnty'][$key_trim1]; ?></a></p></td>

								<td width="100" align="right"><p><?
								//echo number_format($value['req_value'][$key_trim1],2);
								 echo number_format($trim_req_value,2); $total_pre_costing_value+=$trim_req_value; ?></p></td>
								<?
								$wo_qnty=number_format($value['wo_qnty'][$key_trim1],2,'.','');
								if(($value['conversion_factor_rate'][$key_trim1]*$wo_qnty)==$req_qty) $color_wo="green";
								else if(($value['conversion_factor_rate'][$key_trim1]*$wo_qnty) > $req_qty) $color_wo="red";
								else if(($value['conversion_factor_rate'][$key_trim1]*$wo_qnty) < $req_qty ) $color_wo="yellow";
								else $color_wo="";

								$supplier_name_string="";
								$supplier_id_arr=array_unique(explode(',',rtrim($value['supplier_id'][$key_trim1],",")));
								foreach($supplier_id_arr as $supplier_id_arr_key=>$supplier_id_arr_value)
								{
									$ex_sup_data=explode("**",$supplier_id_arr_value);
									if($ex_sup_data[1]==3 || $ex_sup_data[1]==5) $suplier_name_arr=$company_library; else $suplier_name_arr=$lib_supplier_arr;
									$supplier_name_string.=$suplier_name_arr[$ex_sup_data[0]].",";
								}

								/*if($value['pay_mode'][$key_trim1]==3 || $value['pay_mode'][$key_trim1]==5)
								{
									$supplier_name_string=$company_library[$value['supplier_id'][$key_trim1]];
								}
								else
								{
									$supplier_name_string=$lib_supplier_arr[$value['supplier_id'][$key_trim1]];
								}*/

                                ?>
                                <td width="100" align="right" title="<? echo 'conversion_factor='.$value['conversion_factor_rate'][$key_trim1];?>" bgcolor="<? echo $color_wo;?>"><p><a href='#report_details' onclick="openmypage('<? echo $po_id; ?>','<? echo $value['trim_group_dtls'][$key_trim1]; ?>','<? echo $job; ?>','<? echo $main_booking_no_large_data;?>','<? echo $value['trim_dtla_id'][$key_trim1];?>','booking_info');">
                                	<? echo number_format($value['wo_qnty'][$key_trim1],2,'.',''); $summary_array['wo_qnty'][$key_trim]+=$value['wo_qnty'][$key_trim1]; //$total_pre_costing_value+=$value['wo_qnty'][$key_trim1]; ?></a></p></td>
                                <td width="60" align="center"><p>
									<? echo $unit_of_measurement[$item_arr[$value['trim_group_dtls'][$key_trim1]]['order_uom']];
                                    $summary_array['cons_uom'][$key_trim]= $item_arr[$value['trim_group_dtls'][$key_trim1]]['order_uom']; ?></p></td>
                                <td width="100" align="right" title="<? echo number_format($value['rate'][$key_trim1],2,'.',''); ?>"><p>
                                	<? echo number_format($value['amount'][$key_trim1],2,'.',''); $total_wo_value+=$value['amount'][$key_trim1]; ?></p></td>

                                <td width="150" align="left"><p><? echo rtrim($supplier_name_string,','); ?></p></td>
                                <td width="70" align="center" title="<? echo change_date_format($value['wo_date'][$key_trim1])."===". rtrim($value['booking_no'][$key_trim1]);?>"><p>
                                <? $tot=change_date_format($insert_date[0]);
                                if($value['wo_qnty'][$key_trim1]<=0 ) $daysOnHand = datediff('d',$tot,$today);
                                else
                                {
									$wo_date=$value['wo_date'][$key_trim1];
									$wo_date=change_date_format($wo_date);
									$daysOnHand = datediff('d',$tot,$wo_date);;
                                }
                                echo $daysOnHand; ?></p></td>
								<?
								$transfe_out=number_format($value['transfe_out'][$key_trim],2,'.','');
								$transfe_in=number_format($value['transfe_in'][$key_trim],2,'.','');
								$transfe_in_out=$transfe_in.' & '.$transfe_out;

								$transfe_out_amt=number_format($value['transfe_out_amount'][$key_trim],2,'.','');
								$transfe_in_amt=number_format($value['transfe_in_amount'][$key_trim],2,'.','');
								$transfe_in_out_amt=$transfe_in_amt.' & '.$transfe_out_amt;

                                $inhouse_qnty=($value['inhouse_qnty'][$key_trim]+$value['transfe_qty'][$key_trim])-$value['receive_rtn_qty'][$key_trim];
								$inhouse_amt=($value['inhouse_amount'][$key_trim]+$value['transfe_amount'][$key_trim])-$value['receive_rtn_amount'][$key_trim];

								$total_inhouse_value+=$inhouse_amt;
                                $balance=$value['wo_qnty_trim_group'][$key_trim]-$inhouse_qnty;
                                $conv_rate=$conversion_factor_array[$value['trim_group_dtls'][$key_trim1]]['con_factor'];
                                $issue_qnty=$value['issue_qty'][$key_trim]-$value['issue_rtn_qty'][$key_trim];
		//								$issue_amt=$value['issue_amount'][$key_trim]-$value['issue_rtn_amount'][$key_trim];
								$issue_amt=($value['issue_qty'][$key_trim]-$value['issue_rtn_qty'][$key_trim]) * (count($value['inhouse_rate'][$key_trim]) > 0 ? array_sum($value['inhouse_rate'][$key_trim]) / count($value['inhouse_rate'][$key_trim]) : 0);
		//                                print_r($value['inhouse_rate'][$key_trim]);
                                //$tot_issue=$issue_qnty/$conv_rate;
								$tot_issue=$issue_qnty;
                                $left_overqty=$inhouse_qnty-$tot_issue;
								$left_overamt=$inhouse_amt-$issue_amt;
                                $summary_array['inhouse_qnty'][$key_trim]+=$inhouse_qnty;
                                $summary_array['inhouse_qnty_bl'][$key_trim]+=$balance;
                                $summary_array['issue_qty'][$key_trim]+=$tot_issue;
                                $summary_array['left_overqty'][$key_trim]+=$left_overqty;//transfe_amount

								$booking_no_arr=array_unique(explode(',',rtrim($value['booking_no'][$key_trim1],",")));
								$all_pi_no="";
								foreach($booking_no_arr as $book_no)
								{
									$all_pi_no.=chop($pi_arr[$book_no],"**").",";
								}
                                ?>
                                <td  width="70"><p><? echo chop($all_pi_no,","); ?> </p></td>
								<td width="100" align="right"><?= $pi_wise_btb_arr[chop($all_pi_no,",")]['btb_lc_no'];?></td>
								<td width="100" align="right"><?=$pi_wise_btb_arr[chop($all_pi_no,",")]['btb_lc_date'];;?></td>
                                <td width="90" align="right" title="<? echo "Inhouse-Qty: ".number_format($value['inhouse_qnty'][$key_trim]-$value['receive_rtn_qty'][$key_trim],2,'.','')."\n Transfer In & Out Qty: ".$transfe_in_out."\n Return Qty: ".number_format($value['receive_rtn_qty'][$key_trim],2,'.',''); ?>" rowspan="<? echo $rowspannn; ?>"><p><a href='#report_details' onclick="openmypage_inhouse('<? echo $po_id; ?>','<? echo $value['trim_group_dtls'][$key_trim1]; ?>','booking_inhouse_info');"><? echo number_format($inhouse_qnty,2,'.',''); ?></a></p></td>
                                <td width="90" align="right" title="<? echo "Inhouse-Amt: ".number_format($value['inhouse_amount'][$key_trim]-$value['receive_rtn_qty'][$key_trim],2,'.','')."\n Transfer In & Out Amt: ".$transfe_in_out_amt."\n Return Amt: ".number_format($value['receive_rtn_amount'][$key_trim],2,'.',''); ?>" rowspan="<? echo $rowspannn; ?>"><p><? echo number_format($inhouse_amt,2,'.',''); ?></p></td>
                                <td width="90" align="right" rowspan="<? echo $rowspannn; ?>"><p><a href='#report_details' onclick="openmypage_balance('<? echo $po_id; ?>','<? echo $value['trim_group_dtls'][$key_trim1]; ?>','balance_popup');"><? echo number_format($balance,2,'.',''); ?></a><? $total_rec_bal_qnty+=$balance; ?></p></td>
                                <td width="90" align="right" title="<? echo "issue qnty:".number_format($value['issue_qty'][$key_trim],2,'.','')."\n Issue Return Qty: ".number_format($value['issue_rtn_qty'][$key_trim],2,'.','');  ?>" rowspan="<? echo $rowspannn; ?>"><p><a href='#report_details' onclick="openmypage_issue('<? echo $po_id; ?>','<? echo $value['trim_group_dtls'][$key_trim1]; ?>','booking_issue_info');"><? echo number_format($tot_issue,2,'.',''); ?></a></p></td>
                                 <td width="90" align="right" title="<? echo "Issue Amt:".array_sum($value['inhouse_rate'][$key_trim]) / count($value['inhouse_rate'][$key_trim]).'---'.number_format($value['issue_amount'][$key_trim],2,'.','')."\n Issue Return Amt: ".number_format($value['issue_rtn_amount'][$key_trim],2,'.','');  ?>" rowspan="<? echo $rowspannn; ?>"><p><? echo number_format($issue_amt,2,'.',''); $total_issue_amount+=$issue_amt; ?></a></p></td>
                                <td width="90" align="right" rowspan="<? echo $rowspannn; ?>"><p><a href='#report_details' onclick="openmypage_leftover('<? echo $po_id; ?>','<? echo $value['trim_group_dtls'][$key_trim1]; ?>','leftover_popup');"><? echo number_format($left_overqty,2,'.',''); ?></a></p></td>
                                <td width="100" align="right" rowspan="<? echo $rowspannn; ?>"><p><? echo number_format($left_overamt,2,'.',''); $total_left_over_balanc+=$left_overamt; ?></p></td>
                                <?
							}
							else
							{
								?>
								<td width="100" title="<? echo $value['trim_group_from'][$key_trim1]; ?>"><p><? echo $item_library[$value['trim_group_dtls'][$key_trim1]]; ?></p></td>
                                <td width="140"><p style="word-break:break-all"><? echo $value['description'][$key_trim1]; ?></p></td>
								<td width="100"><p style="word-break:break-all"><? echo $value['remark'][$key_trim1]; ?></p></td>
                                <td width="100"><p style="word-break:break-all"><? echo $value['brand_sup_ref'][$key_trim1]; ?></p></td>
								<td width="100"><p style="word-break:break-all"><? echo $commission_particulars[$value['source_id'][$key_trim1]]; ?></p></td>
								
          
                                <td width="60" align="center"><p><? if($value['apvl_req'][$key_trim1]==1) echo "Yes"; else echo ""; ?></p></td>
                                <td width="80" align="center"><p><?
                                if($value['apvl_req'][$key_trim1]==1)
                                {
									$app_status=$app_status_arr[$job][$value['trim_group_dtls'][$key_trim1]];
									$approved_status=$approval_status[$app_status];
									$summary_array['item_app'][$key_trim][all]+=1;
									if($app_status==3)
									{
										$summary_array['item_app'][$key_trim][app]+=1;
									}
                                }
                                else
                                {
                                	$approved_status="";
                                }
                                echo $approved_status; ?></p></td>

                                <td width="100" align="center"><p><? $insert_date=explode(" ",$value['insert_date'][$key_trim1]); echo change_date_format($insert_date[0],'','',''); ?></p></td>
                                <td width="80" align="right"><?=number_format($value['avg_cons'][$key_trim1],4); ?></td>
                                <td width="100" align="right"><p><a href='#report_details' onclick="order_req_qty_popup('<? echo $company_name; ?>','<? echo $job; ?>','<? echo $po_id; ?>', '<? echo $job_data[0]; ?>','<? echo $value['rate'][$key_trim1]; ?>','<? echo $value['trim_group_dtls'][$key_trim1];?>' ,'<? echo $value['booking_no'][$key_trim1];?>','<? echo $value['description'][$key_trim1] ;?>','<? echo rtrim($value['country_id'][$key_trim1],",");?>','<? echo $value['trim_dtla_id'][$key_trim1];?>','<? echo $start_date ?>','<? echo $end_date ?>','order_req_qty_data');">
									<?
									$trim_req_value=$trim_amount[$job][$key_trim1];
									$trim_req_qnty=$trim_qty[$job][$key_trim1];
								//$req_qty=number_format($value['req_qnty'][$key_trim1],2,'.','');
									$req_qty=number_format($trim_req_qnty,2,'.',''); echo $req_qty;
                                    $summary_array['req_qnty'][$key_trim]+=$req_qty;
                                    ?></a></p></td>

								<td width="100" align="right"><p><? echo number_format($trim_req_value,2); $total_pre_costing_value+=$trim_req_value; ?></p></td>
								<?
                                $wo_qnty=number_format($value['wo_qnty'][$key_trim1],2);

                                /*if($wo_qnty > $req_qty) $color_wo="red";
                                else if($wo_qnty < $req_qty ) $color_wo="yellow";
                                else $color_wo="";*/

								if(($value['conversion_factor_rate'][$key_trim1]*$wo_qnty)==$req_qty) $color_wo="green";
								else if(($value['conversion_factor_rate'][$key_trim1]*$wo_qnty) > $req_qty) $color_wo="red";
								else if(($value['conversion_factor_rate'][$key_trim1]*$wo_qnty) < $req_qty ) $color_wo="yellow";
								else $color_wo="";

								$supplier_name_string="";
								$supplier_id_arr=array_unique(explode(',',rtrim($value['supplier_id'][$key_trim1],",")));
								foreach($supplier_id_arr as $supplier_id_arr_key=>$supplier_id_arr_value)
								{
									$ex_sup_data=explode("**",$supplier_id_arr_value);
									if($ex_sup_data[1]==3 || $ex_sup_data[1]==5) $suplier_name_arr=$company_library; else $suplier_name_arr=$lib_supplier_arr;
									$supplier_name_string.=$suplier_name_arr[$ex_sup_data[0]].",";
								}
								$booking_no_arr=array_unique(explode(',',rtrim($value['booking_no'][$key_trim1],",")));
								$main_booking_no_large_data="";
								foreach($booking_no_arr as $booking_no1)
								{
									if($main_booking_no_large_data=="") $main_booking_no_large_data=$booking_no1; else $main_booking_no_large_data.=",".$booking_no1;
								}
								?>
                                <td width="90" align="right" title="<? echo 'conversion_factor='.$value['conversion_factor_rate'][$key_trim1];?>" bgcolor="<? echo $color_wo;?>"><p><a href='#report_details' onclick="openmypage('<? echo $po_id; ?>','<? echo $value['trim_group_dtls'][$key_trim1]; ?>','<? echo $job; ?>','<? echo $main_booking_no_large_data;?>','<? echo $value['trim_dtla_id'][$key_trim1];?>','booking_info');">
                                	<? echo number_format($value['wo_qnty'][$key_trim1],2,'.',''); $summary_array['wo_qnty'][$key_trim]+=$value['wo_qnty'][$key_trim1]; ?></a></p></td>

                                <td width="60" align="center"><p><? echo $unit_of_measurement[$item_arr[$value['trim_group_dtls'][$key_trim1]]['order_uom']];
                               		$summary_array['cons_uom'][$key_trim]= $item_arr[$value['trim_group_dtls'][$key_trim1]]['order_uom']; ?></p></td>

                                <td width="100" align="right" title="<? echo number_format($value['rate'][$key_trim1],2,'.',''); ?>"><p><? echo number_format($value['amount'][$key_trim1],2,'.',''); $total_wo_value+=$value['amount'][$key_trim1]; ?></p></td>
                                <td width="150" align="left"><p><? echo rtrim($supplier_name_string,','); ?></p></td>
                                <td  width="70"align="center" title="<? echo change_date_format($value[wo_date][$key_trim1])."===". rtrim($value['booking_no'][$key_trim1]);?>"><p>
                                <? $tot=change_date_format($insert_date[0]);
                                if($value['wo_qnty'][$key_trim1]<=0 )
                                {
                                	$daysOnHand = datediff('d',$tot,$today);
                                }
                                else
                                {
									$wo_date=$value['wo_date'][$key_trim1];
									$wo_date=change_date_format($wo_date);
									$daysOnHand = datediff('d',$tot,$wo_date);;
                                }
                                echo $daysOnHand; ?></p></td>
                                <?
                                $booking_no_arr=array_unique(explode(',',rtrim($value['booking_no'][$key_trim1],",")));
								$all_pi_no="";
								foreach($booking_no_arr as $book_no)
								{
									$all_pi_no.=chop($pi_arr[$book_no],"**").",";
								}
                                ?>
                                <td  width="70"><p><? echo chop($all_pi_no,","); ?> </p></td>
								<td width="100" align="right"><?= $pi_wise_btb_arr[chop($all_pi_no,",")]['btb_lc_no'];?></td>
								<td width="100" align="right"><?=$pi_wise_btb_arr[chop($all_pi_no,",")]['btb_lc_date'];;?></td>
							<?
                            } 
							
							
									
									foreach($trims_tna as $vid=>$key)
									{
									 if( $key==32 || $key==71  || $key==300  || $key==8 || $key==13 || $key==29 || $key==279  || $key==308 || $key==11 || $key==307 || $key==278  || $key==310 || $key==301 || $key==309 || $key==12 || $key==24){
									
									$plan_date=$job_wise_tna_data_arr[$job_no][$key]['plan_end_date'];
								$actual_date=$job_wise_tna_data_arr[$job_no][$key]['actual_finish_date'];
								
								if(strtotime($actual_date)>0){
									$diff = abs(strtotime($plan_date) - strtotime($actual_date));
									$delay = floor($diff / (60*60*24));	
									}else{
										$delay="";
									}
									
									
								 	if($key==308 || $key==307 || $key==278 || $key==279 || $key==309 || $key==300 || $key==301 || $key==310){
										if($value['source_id'][$key_trim1]==1 && ( $key==278 || $key==279 || $key==300 || $key==301)){
											$actual_finish_date=$job_wise_tna_data_arr[$job_no][$key]['actual_finish_date'];
											$plan_end_date=$job_wise_tna_data_arr[$job_no][$key]['plan_end_date'];
										}
										else if($value['source_id'][$key_trim1]==2 && ($key==308 || $key==307 || $key==309 || $key==310)){
											$actual_finish_date=$job_wise_tna_data_arr[$job_no][$key]['actual_finish_date'];
											$plan_end_date=$job_wise_tna_data_arr[$job_no][$key]['plan_end_date'];
										}
										else{
											$actual_finish_date='';
											$plan_end_date='';
											$delay="";
										}
									}
									else{
										$actual_finish_date=$job_wise_tna_data_arr[$job_no][$key]['actual_finish_date'];
										$plan_end_date=$job_wise_tna_data_arr[$job_no][$key]['plan_end_date'];
									}
                                    
                                    
									
																		
							
								 ?>
									<td width="100" align="right" id="" title=<?=$key;?>><?=$plan_end_date;?></td>
									<td width="100" align="right" id=""><?=$actual_finish_date;?></td>
									<td width="100" align="right" id=""><? 	echo $delay;?></td>

										<?}}?>
									
						</tr>
						<? $gg++;
                        }// end  foreach($value[$key_trim] as $key_trim1=>$value_trim1)
					}
					$i++;
				}
			}//end
			?>
			</table>
            </div>
			<table class="rpt_table" width="<?=$tbl_width3;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
                <tfoot>
                    <th width="30">&nbsp;</th>
                    <th width="50">&nbsp;</th>
                    <th width="50">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>

                    <th width="90">&nbsp;</th>
                    <th width="80" id="total_order_qnty">&nbsp;<? //echo number_format($total_order_qnty,0); ?></th>
                    <th width="50">&nbsp;</th>
                    <th width="80" id="total_order_qnty_in_pcs">&nbsp;<? //echo number_format($total_order_qnty_in_pcs,0); ?></th>
                    <th width="80">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="140">&nbsp;</th>

                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>

                    <th width="60">&nbsp;</th>
					<th width="80">&nbsp;</th>
					<th width="100">&nbsp;</th>

                    <th width="80">&nbsp;</th>
                    <th width="100" id="value_req_qnty"><? //echo number_format($total_req_qnty,2); ?>&nbsp;</th>
                    <th width="100" id="value_pre_costing"><? echo number_format($total_pre_costing_value,2); ?></th>
                    <th width="90"><? //echo number_format($total_wo_qnty,2); ?>&nbsp;</th>
                    <th width="60">&nbsp;</th>

                    <th width="100"id="value_wo_qty"><? echo number_format($total_wo_value,2); ?></th>
                    <th width="150">&nbsp;</th>
                    <th width="70"><p><? //echo number_format($req_value,2,'.',''); ?>&nbsp;</p></th>
                    <th width="70"><p><? //echo number_format($req_value,2,'.',''); ?>&nbsp;</p></th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
                    <th width="90" id="value_in_qty"><? //echo number_format($total_in_qnty,2); ?></th>
                    <th width="90" id="value_in_amount"><? echo number_format($total_inhouse_value,2); ?></th>
                    <th width="90" id="value_rec_qty"><? echo number_format($total_rec_bal_qnty,2); ?></th>
                    <th width="90" id="value_issue_qty"><? //echo number_format($total_issue_qnty,2); ?></th>
                    <th width="90" id="value_issue_amount"><? echo number_format($total_issue_amount,2); ?></th>
                    <th width="90" id="value_leftover_qty"><? //echo number_format($total_leftover_qnty,2); ?>&nbsp;</th>
                    <th width="100" id="value_leftover_amount"> <? echo number_format($total_left_over_balanc,2); ?>&nbsp;</th>
					<?php
					foreach($trims_tna as $vid=>$key)
					{
					 if( $key==32 || $key==71   || $key==300  || $key==8 || $key==13 || $key==29 || $key==279  || $key==308 || $key==11 || $key==307 || $key==278  || $key==310 || $key==301 || $key==309 || $key==12 || $key==24){

						?>
						  <th width="100">&nbsp;</th>
						  <th width="100">&nbsp;</th>
						  <th width="100">&nbsp;</th>
						  <?}}?>
                </tfoot>
			</table>

			<table>
				<tr><td height="15"></td></tr>
			</table>
		
        </fieldset>
      
		<?
        }
	}
	?>
    
    </div>
    <?
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename****$tot_rows****1";
	exit();
}

if($action=="report_generate2")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_library=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
	$country_name_library=return_library_array( "select id, country_name from lib_country", "id", "country_name"  );
	$lib_supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );

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
			else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}

	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_job_no=trim($txt_job_no);
	if($txt_job_no !="" || $txt_job_no !=0) $jobcond="and a.job_no_prefix_num='".$txt_job_no."'"; else $jobcond="";

	if(str_replace("'","",$cbo_item_group)=="") $item_group_cond="";
	else $item_group_cond="and e.trim_group in(".str_replace("'","",$cbo_item_group).")";

	$date_type=str_replace("'","",$cbo_date_type);

	$date_cond='';
	if($date_type==2)
	{
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			$start_date=(str_replace("'","",$txt_date_from));
			$end_date=(str_replace("'","",$txt_date_to));
			$date_cond="and c.country_ship_date between '$start_date' and '$end_date'";
		}
	}
	else
	{
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			$start_date=(str_replace("'","",$txt_date_from));
			$end_date=(str_replace("'","",$txt_date_to));
			$date_cond="and b.pub_shipment_date between '$start_date' and '$end_date'";
		}
	}

	if (str_replace("'","",$txt_job_no)=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in (".str_replace("'","",$txt_job_no).") ";

	if(str_replace("'","",$txt_style_ref)!="") $style_ref_cond=" and a.style_ref_no = '".str_replace("'","",$txt_style_ref)."'"; else $style_ref_cond="";
	if(str_replace("'","",$txt_order_no)!="") $ordercond=" and b.po_number like '%".str_replace("'","",$txt_order_no)."%'"; else $ordercond="";
	$file_no=str_replace("'","",$txt_file_no);
	$internal_ref=str_replace("'","",$txt_internal_ref);
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' ";
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' ";
	$cbo_year=str_replace("'","",$cbo_year);
	if($db_type==0)
	{
		if(trim($cbo_year)!=0) $year_cond=" and YEAR(a.insert_date)=$cbo_year"; else $year_cond="";
	}
	else if($db_type==2)
	{
		if(trim($cbo_year)!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year"; else $year_cond="";
	}

	//echo $file_no_cond.'=='.$internal_ref_cond;die;
	//echo "select format_id from lib_report_template where template_name ='".$company_name."' and module_id=2 and report_id=22 and is_deleted=0 and status_active=1"; die;
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$company_name."' and module_id=2 and report_id=22 and is_deleted=0 and status_active=1","format_id");
	//echo "gg"; die;
	"print_report_button_setting('$print_report_format');\n";
	//$print_report_button_setting=print_report_button_setting('$print_report_format');
	//echo "gg"; die;
	if(str_replace("'","",$cbo_search_by)==1)
	{
		if($template==1)
		{
			ob_start();
			?>
            <div style="width:3050px">
            <fieldset style="width:100%;">
			<table width="3040">
				<tr class="form_caption">
					<td colspan="34" align="center"><? echo $report_title; ?></td>
				</tr>
				<tr class="form_caption">
					<td colspan="34" align="center"><? echo $company_library[$company_name]; ?></td>
				</tr>
			</table>
			<table class="rpt_table" width="3040" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<th width="30">SL</th>
					<th width="50">Buyer</th>
					<th width="100">Job No</th>
                    <th width="100">Style Ref</th>
					<th width="100">Internal Ref</th>
                    <th width="100">File No</th>

					<th width="90">Order No</th>
					<th width="80">Order Qty</th>
					<th width="50">UOM</th>
					<th width="80">Qty (Pcs)</th>
					<th width="80">Shipment Date</th>
					<th width="100">Trims Name</th>
                    <th width="140">Item Description</th>
                    <th width="100">Remark</th>
					<th width="100">Brand/Sup Ref</th>
					<th width="60">Appr Req.</th>
					<th width="80">Approve Status</th>
                    <th width="100">Item Entry Date</th>
                    <th width="80">Avg. Cons</th>
					<th width="100">Req Qty</th>
					<th width="100">Pre Costing Value</th>
					<th width="90">WO Qty</th>
                    <th width="60">Trims UOM</th>
                    <th width="100">WO Value (USD)</th>
                    <th width="150">Supplier</th>
                    <th width="70">WO Delay Days</th>
                    <th width="90">PI No</th>
					<th width="90">In-House Qty</th>
                    <th width="90">In-House Amount</th>
					<th width="90">Receive Balance</th>
					<th width="90">Issue to Prod.</th>
                    <th width="90">Issue Amount</th>
					<th width="90">Left Over/Balance</th>
                    <th>Left Over/Balance Amount</th>
				</thead>
			</table>
			<div style="width:3060px; max-height:400px; overflow-y:scroll" id="scroll_body">
				<table class="rpt_table" width="3040" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
				<?
                $conversion_factor_array=array();$item_arr=array();
				$conversion_factor=sql_select("select id, trim_uom, order_uom, conversion_factor from lib_item_group where status_active=1  ");
				foreach($conversion_factor as $row_f)
				{
					$conversion_factor_array[$row_f[csf('id')]]['con_factor']=$row_f[csf('conversion_factor')];
					$conversion_factor_array[$row_f[csf('id')]]['cons_uom']=$row_f[csf('trim_uom')];
					$item_arr[$row_f[csf('id')]]['order_uom']=$row_f[csf('order_uom')];
				}
				unset($conversion_factor);

				$conversion_factor=array();
				$app_sql=sql_select("select job_no_mst,accessories_type_id,approval_status from wo_po_trims_approval_info");
				$app_status_arr=array();
				foreach($app_sql as $row)
				{
					$app_status_arr[$row[csf("job_no_mst")]][$row[csf("accessories_type_id")]]=$row[csf("approval_status")];
				}
				unset($app_sql);

				$sql_po_qty_country_wise_arr=array();
				$po_job_arr=array();
				$sql_po_country_data=sql_select("select  b.id, b.job_no_mst, c.country_id, sum(c.order_quantity/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst  and b.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond group by   b.id,b.job_no_mst,c.country_id order by b.id,b.job_no_mst,c.country_id");
				foreach( $sql_po_country_data as $sql_po_country_row)
				{
					$sql_po_qty_country_wise_arr[$sql_po_country_row[csf('id')]][$sql_po_country_row[csf('country_id')]]=$sql_po_country_row[csf('order_quantity_set')];
					$po_job_arr[$sql_po_country_row[csf('id')]]=$sql_po_country_row[csf('job_no_mst')];
				}
				unset($sql_po_country_data);

				$po_data_arr=array();
				$po_id_string="";
				$today=date("Y-m-d");

				$sql_pos=("select a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.order_uom, b.file_no, b.grouping, b.id, b.po_number, sum(c.order_quantity) as order_quantity, sum(c.order_quantity/a.total_set_qnty) as order_quantity_set, b.pub_shipment_date
				from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
				where
				a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.company_name=$company_name $buyer_id_cond ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $date_cond $style_ref_cond $jobcond $ordercond $file_no_cond $internal_ref_cond $year_cond
				group by a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.order_uom, a.total_set_qnty, b.file_no, b.grouping, b.id, b.po_number, b.pub_shipment_date order by b.id ASC");
				//echo $sql_pos; die;
				$sql_po=sql_select($sql_pos);
				$po_arr=array(); $tot_rows=0;
				foreach($sql_po as $row)
				{
					$tot_rows++;
					$po_arr[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
					$po_arr[$row[csf('id')]]['job_no']=$row[csf('job_no')];
					$po_arr[$row[csf('id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
					$po_arr[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')];
					$po_arr[$row[csf('id')]]['order_uom']=$row[csf('order_uom')];
					$po_arr[$row[csf('id')]]['file_no']=$row[csf('file_no')];
					$po_arr[$row[csf('id')]]['grouping']=$row[csf('grouping')];
					$po_arr[$row[csf('id')]]['po_number']=$row[csf('po_number')];
					$po_arr[$row[csf('id')]]['order_quantity']=$row[csf('order_quantity')];
					$po_arr[$row[csf('id')]]['order_quantity_set']=$row[csf('order_quantity_set')];
					$po_arr[$row[csf('id')]]['pub_shipment_date']=$row[csf('pub_shipment_date')];
					$po_id_string.=$row[csf('id')].",";
				}
				unset($sql_po);
				$po_id_string=rtrim($po_id_string,",");
				if($po_id_string=="")
				{
					echo "<strong style='color:red;font-size:30px'>No Data found</strong>";
					die;
				}

				$poIds=chop($po_id_string,','); $order_cond=""; $order_cond1=""; $order_cond2=""; $precost_po_cond="";
				if($db_type==2 && $tot_rows>1000)
				{
					$order_cond=" and (";
					$order_cond1=" and (";
					$order_cond2=" and (";
					$precost_po_cond=" and (";
					$poIdsArr=array_chunk(explode(",",$poIds),999);
					foreach($poIdsArr as $ids)
					{
						$ids=implode(",",$ids);
						//$poIds_cond.=" po_break_down_id in($ids) or ";
						$order_cond.=" b.po_break_down_id in($ids) or";
						$order_cond1.=" b.po_breakdown_id in($ids) or";
						$order_cond2.=" d.po_breakdown_id in($ids) or";
						$precost_po_cond.=" c.po_break_down_id in($ids) or";
					}
					$order_cond=chop($order_cond,'or ');
					$order_cond.=")";
					$order_cond1=chop($order_cond1,'or ');
					$order_cond1.=")";
					$order_cond2=chop($order_cond2,'or ');
					$order_cond2.=")";
					$precost_po_cond=chop($precost_po_cond,'or ');
					$precost_po_cond.=")";
				}
				else
				{
					$order_cond=" and b.po_break_down_id in($poIds)";
					$order_cond1=" and b.po_breakdown_id in($poIds)";
					$order_cond2=" and d.po_breakdown_id in($poIds)";
					$precost_po_cond=" and c.po_break_down_id in($poIds)";
				}

				$condition= new condition();
				if(str_replace("'","",$txt_job_no) !=''){
					$condition->job_no_prefix_num("=$txt_job_no");
				}
				if(str_replace("'","",$txt_order_no)!='')
				{
					//$condition->po_number("=$txt_order_no");
					$order_nos=str_replace("'","",$txt_order_no);
					$condition->po_number(" like '%$order_nos%'");
				}

				if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
				{
					$start_date=(str_replace("'","",$txt_date_from));
					$end_date=(str_replace("'","",$txt_date_to));
				}
				if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
				{
					  $condition->country_ship_date(" between '$start_date' and '$end_date'");
				}
				/*if(str_replace("'",'',$txt_po_breack_down_id) !="")
				{
					$condition->po_id("in($txt_po_breack_down_id)");
				}*/

				$condition->init();
				$trim= new trims($condition);
				//echo $trim->getQuery(); die;
				$trim_qty=$trim->getQtyArray_by_orderCountryAndPrecostdtlsid();
				//print_r($trim_qty);
				$trim= new trims($condition);
				$trim_amount=$trim->getAmountArray_by_orderAndPrecostdtlsid();

				$sql_pre_cost=sql_select("select a.costing_per, a.costing_date, b.id as trim_dtla_id, b.trim_group, b.remark, b.description, b.brand_sup_ref, b.cons_uom, b.cons_dzn_gmts, b.rate, b.amount, b.apvl_req, b.nominated_supp, b.insert_date, c.cons, c.pcs, c.country_id, c.cons as cons_cal, c.po_break_down_id
			from wo_pre_cost_mst a, wo_pre_cost_trim_cost_dtls b, wo_pre_cost_trim_co_cons_dtls c
			where c.job_no=b.job_no and b.id=c.wo_pre_cost_trim_cost_dtls_id and a.job_no=b.job_no and c.cons>0 $trm_group_pre_cost_cond $precost_po_cond
			group by a.costing_per, a.costing_date, b.id, b.trim_group, b.remark, b.description, b.brand_sup_ref, b.cons_uom, b.cons_dzn_gmts, b.rate, b.amount, b.apvl_req, b.nominated_supp, b.insert_date, c.cons, c.pcs, c.country_id, c.po_break_down_id order by b.trim_group ASC");

				$tot_rows=count($sql_pre_cost);
				$i=1;
				if(count($sql_pre_cost)>0)
				{
					foreach($sql_pre_cost as $rowp)
					{
						$dzn_qnty=0;
						if($rowp[csf('costing_per')]==1) $dzn_qnty=12;
						else if($rowp[csf('costing_per')]==3) $dzn_qnty=12*2;
						else if($rowp[csf('costing_per')]==4) $dzn_qnty=12*3;
						else if($rowp[csf('costing_per')]==5) $dzn_qnty=12*4;
						else $dzn_qnty=1;

						$poId=0;
						$poId=$rowp[csf('po_break_down_id')];

						$po_qty=0; $req_qnty=0; $req_value=0;
						if($rowp[csf('country_id')]==0)
						{
							$po_qty=$po_arr[$poId]['order_quantity'];
							$req_qnty+=$trim_qty[$poId][$rowp[csf('country_id')]][$rowp[csf('trim_dtla_id')]];
						}
						else
						{
							$country_id= explode(",",$rowp[csf('country_id')]);
							for($cou=0; $cou<=count($country_id); $cou++)
							{
								$po_qty+=$sql_po_qty_country_wise_arr[$poId][$country_id[$cou]];
								$req_qnty+=$trim_qty[$poId][$country_id[$cou]][$rowp[csf('trim_dtla_id')]];
							}
						}

						//$req_qnty=($rowp[csf('cons_cal')]/$dzn_qnty)*$po_qty;
						//$req_value= $rowp[csf('rate')]*$req_qnty;

						$req_value=$trim_amount[$poId][$rowp[csf('trim_dtla_id')]];

						$po_data_arr[$poId]['trim_dtla_id'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('trim_dtla_id')];// for rowspan
						$po_data_arr[$poId]['trim_group'][$rowp[csf('trim_group')]]=$rowp[csf('trim_group')];
						$po_data_arr[$poId][$rowp[csf('trim_group')]][$rowp[csf('trim_dtla_id')]]=$rowp[csf('trim_dtla_id')]; // for rowspannn
						$po_data_arr[$poId]['trim_group_dtls'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('trim_group')];
						$po_data_arr[$poId]['remark'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('remark')];

						$po_data_arr[$poId]['brand_sup_ref'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('brand_sup_ref')];
						$po_data_arr[$poId]['apvl_req'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('apvl_req')];
						$po_data_arr[$poId]['insert_date'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('insert_date')];
						$po_data_arr[$poId]['req_qnty'][$rowp[csf('trim_dtla_id')]]+=$req_qnty;
						$po_data_arr[$poId]['req_value'][$rowp[csf('trim_dtla_id')]]+=$req_value;
						$po_data_arr[$poId]['cons_uom'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('cons_uom')];

						$po_data_arr[$poId]['trim_group_from'][$rowp[csf('trim_dtla_id')]]="Pre_cost";
						$po_data_arr[$poId]['rate'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('rate')];
						$po_data_arr[$poId]['description'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('description')];
						$po_data_arr[$poId]['country_id'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('country_id')];

						$po_data_arr[$poId]['costing_per'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('costing_per')];
						$po_data_arr[$poId]['costing_date'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('costing_date')];
						$po_data_arr[$poId]['avg_cons'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('cons_dzn_gmts')];
					}
				}
				else
				{
					echo "<strong style='color:red;font-size:30px'>No Data found</strong>";
					die;
				}
				unset($sql_pre_cost);

				if($db_type==2)
				{
					$sql_without_precost=sql_select("select min(a.booking_date) as booking_date, b.job_no, LISTAGG(CAST(a.booking_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as booking_no, LISTAGG(CAST( a.supplier_id || '**' || a.pay_mode AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.booking_no) as supplier_id, b.po_break_down_id, b.trim_group, b.pre_cost_fabric_cost_dtls_id, sum(b.wo_qnty) as wo_qnty, sum(b.amount/b.exchange_rate) as amount,sum(b.rate) as rate from wo_booking_mst a, wo_booking_dtls b where a.item_category=4 and a.booking_no=b.booking_no  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name $order_cond group by b.po_break_down_id, b.trim_group, b.job_no, b.pre_cost_fabric_cost_dtls_id");//and item_from_precost=2
				}
				else if($db_type==0)
				{
					$sql_without_precost=sql_select("select min(a.booking_date) as booking_date, b.job_no, group_concat(a.booking_no) as booking_no, group_concat( concat_ws('**',a.supplier_id, a.pay_mode)) as supplier_id, b.po_break_down_id, b.trim_group, b.pre_cost_fabric_cost_dtls_id, sum(b.wo_qnty) as wo_qnty, sum(b.amount/b.exchange_rate) as amount, sum(b.rate) as rate from wo_booking_mst a, wo_booking_dtls b where a.item_category=4 and a.booking_no=b.booking_no  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name $order_cond  group by b.po_break_down_id,b.trim_group,b.job_no,b.pre_cost_fabric_cost_dtls_id");//and item_from_precost=2
				}

				$style_data_arr1=array();
				foreach($sql_without_precost as $wo_row)
				{
					$conversion_factor_rate=$conversion_factor_array[$wo_row[csf('trim_group')]]['con_factor'];
					//$cons_uom=$conversion_factor_array[$wo_row_without_precost[csf('trim_group')]]['cons_uom'];
					$cons_uom=$item_arr[$wo_row[csf('trim_group')]]['order_uom'];
					$booking_no=$wo_row[csf('booking_no')];
					$supplier_id=$wo_row[csf('supplier_id')];
					$wo_qnty=$wo_row[csf('wo_qnty')];
					$amount=$wo_row[csf('amount')];
					$wo_date=$wo_row[csf('booking_date')];

					$poId=0;
					$poId=$wo_row[csf('po_break_down_id')];

					if($wo_row[csf('pre_cost_fabric_cost_dtls_id')] =="" || $wo_row[csf('pre_cost_fabric_cost_dtls_id')] ==0)
					{
						$trim_dtla_id=max($po_data_arr[$poId]['trim_dtla_id'][$trim_dtla_id])+1;
						$po_data_arr[$poId]['trim_dtla_id'][$trim_dtla_id]=$trim_dtla_id;// for rowspan
						$po_data_arr[$poId]['trim_group'][$wo_row[csf('trim_group')]]=$wo_row[csf('trim_group')];
						$po_data_arr[$poId][$wo_row[csf('trim_group')]][$trim_dtla_id]=$trim_dtla_id;// for rowspannn
						$po_data_arr[$poId]['trim_group_dtls'][$trim_dtla_id]=$wo_row[csf('trim_group')];
						$po_data_arr[$poId]['cons_uom'][$trim_dtla_id]=$cons_uom;

						$po_data_arr[$poId]['trim_group_from'][$trim_dtla_id]="Booking Without Pre_cost";
					}
					else
					{
						$trim_dtla_id=$wo_row[csf('pre_cost_fabric_cost_dtls_id')];
					}

					$po_data_arr[$poId]['wo_qnty'][$trim_dtla_id]+=$wo_qnty;
					$po_data_arr[$poId]['amount'][$trim_dtla_id]+=$amount;
					$po_data_arr[$poId]['wo_date'][$trim_dtla_id]=$wo_date;
					$po_data_arr[$poId]['wo_qnty_trim_group'][$wo_row[csf('trim_group')]]+=$wo_qnty;
					$po_data_arr[$poId]['booking_no'][$trim_dtla_id]=$booking_no;
					$po_data_arr[$poId]['supplier_id'][$trim_dtla_id]=$supplier_id;
					$po_data_arr[$poId]['conversion_factor_rate'][$trim_dtla_id]=$conversion_factor_rate;
				}
				unset($sql_without_precost);
				//echo "select b.po_breakdown_id, a.item_group_id, sum(b.quantity) as quantity from  inv_receive_master c, product_details_master d, inv_trims_entry_dtls a, order_wise_pro_details b where a.mst_id=c.id and a.trans_id=b.trans_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $order_cond1  group by b.po_breakdown_id, a.item_group_id order by a.item_group_id ";
			$sql_rec_data=sql_select("select b.po_breakdown_id, a.item_group_id, c.receive_basis, a.booking_id, b.quantity as quantity, a.rate, c.exchange_rate, (b.quantity*d.avg_rate_per_unit) as amount from inv_receive_master c,product_details_master d, inv_trims_entry_dtls a, order_wise_pro_details b where a.mst_id=c.id and a.trans_id=b.trans_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_cond1 $trm_group_rec_cond order by a.item_group_id ");

				foreach($sql_rec_data as $row)
				{
					$poId=0; $poId=$row[csf('po_breakdown_id')];
					if($po_data_arr[$row[csf('po_breakdown_id')]]['trim_group'][$row[csf('item_group_id')]]=="" || $po_data_arr[$row[csf('po_breakdown_id')]]['trim_group'][$row[csf('item_group_id')]]==0)
					{
						$cons_uom=$conversion_factor_array[$row[csf('item_group_id')]]['cons_uom'];
						$trim_dtla_id=max($po_data_arr[$poId]['trim_dtla_id'])+1;
						$po_data_arr[$poId]['trim_dtla_id'][$trim_dtla_id]=$trim_dtla_id;// for rowspan
						$po_data_arr[$poId]['trim_group'][$row[csf('item_group_id')]]=$row[csf('item_group_id')];
						$po_data_arr[$poId][$row[csf('item_group_id')]][$trim_dtla_id]=$trim_dtla_id;// for rowspannn
						$po_data_arr[$poId]['trim_group_dtls'][$trim_dtla_id]=$row[csf('item_group_id')];
						$po_data_arr[$poId]['cons_uom'][$trim_dtla_id]=$cons_uom;
						$po_data_arr[$poId]['trim_group_from'][$trim_dtla_id]="Trim Receive";
						//echo $trim_dtla_id.'==';
					}
					$po_data_arr[$poId]['inhouse_qnty'][$row[csf('item_group_id')]]+=$row[csf('quantity')];
					$amount=0;  $amount=($row[csf('quantity')]*$row[csf('rate')]);//*$row[csf('exchange_rate')];
					$po_data_arr[$poId]['inhouse_amount'][$row[csf('item_group_id')]]+=$amount;
					$po_data_arr[$poId]['basis_piwono'][$row[csf('item_group_id')]].=$row[csf('receive_basis')].'_'.$row[csf('booking_id')].',';
				}
				unset($sql_rec_data);

				$sql_wo_pi=sql_select("select a.pi_number, b.work_order_no from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=4 and a.status_active=1 and a.importer_id=$company_name and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pi_number, b.work_order_no");
				$pi_arr=array();
				foreach($sql_wo_pi as $rowPi)
				{
					$pi_arr[$rowPi[csf('work_order_no')]].=$rowPi[csf('pi_number')].'**';
				}
				unset($sql_wo_pi);

				/*$receive_rtn_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id, sum(d.quantity) as quantity from inv_issue_master a,inv_transaction b, product_details_master c, order_wise_pro_details d,inv_receive_master e  where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and e.id=a.received_id and b.transaction_type=3 and a.entry_form=49 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $order_cond2 $trm_group_recrtn_cond  group by d.po_breakdown_id, c.item_group_id order by c.item_group_id");
				//echo $receive_rtn_qty_data; die;
				foreach($receive_rtn_qty_data as $row)
				{
					$ord_uom_qty=0;
					$ord_uom_qty=$row[csf('quantity')]/$item_arr[$row[csf('item_group_id')]]['order_uom'];
					$po_data_arr[$row[csf('po_breakdown_id')]]['receive_rtn_qty'][$row[csf('item_group_id')]]+=$ord_uom_qty;
				}
				unset($receive_rtn_qty_data);	*/

				$receive_rtn_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id, d.quantity as quantity, c.avg_rate_per_unit as rate from product_details_master c,order_wise_pro_details d where d.prod_id=c.id and d.trans_type=3 and d.entry_form=49 and d.status_active=1 and d.is_deleted=0 $order_cond2 order by c.item_group_id ASC");
				foreach($receive_rtn_qty_data as $row)
				{
					$ord_uom_qty=0; $receive_rtn_amt=0;
					//$ord_uom_qty=$row[csf('quantity')]/$conversion_factor_array[$row[csf('item_group_id')]]['con_factor'];
					$ord_uom_qty=$row[csf('quantity')];
					$receive_rtn_amt=$ord_uom_qty*$row[csf('rate')];
					$po_data_arr[$row[csf('po_breakdown_id')]]['receive_rtn_qty'][$row[csf('item_group_id')]]+=$ord_uom_qty;
					$po_data_arr[$row[csf('po_breakdown_id')]]['receive_rtn_amt'][$row[csf('item_group_id')]]+=$receive_rtn_amt;
				}
				//echo "<pre>";print_r($style_data_arr);
				unset($receive_rtn_qty_data);

				$transfer_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id, sum((case when d.trans_type=5 then d.quantity else 0 end)-(case when d.trans_type=6 then d.quantity else 0 end)) as quantity,
				sum(case when d.trans_type=5 then d.quantity else 0 end) as in_qty,
				sum(case when d.trans_type=6 then d.quantity else 0 end) as out_qty,
				sum(case when d.trans_type=5 then (d.quantity*c.avg_rate_per_unit) else 0 end) as in_amount,
				sum(case when d.trans_type=6 then (d.quantity*c.avg_rate_per_unit) else 0 end) as out_amount
				from product_details_master c,order_wise_pro_details d
				where d.prod_id=c.id and d.trans_type in(5,6) and d.entry_form=78 and d.status_active=1 and d.is_deleted=0 $order_cond2 group by d.po_breakdown_id, c.item_group_id order by c.item_group_id");
				foreach($transfer_qty_data as $row)
				{
					$transfe_amount=0;
					$transfe_amount=$row[csf('in_amount')]-$row[csf('out_amount')];
					$po_data_arr[$row[csf('po_breakdown_id')]]['transfe_qty'][$row[csf('item_group_id')]]+=$row[csf('quantity')];
					$po_data_arr[$row[csf('po_breakdown_id')]]['transfe_in'][$row[csf('item_group_id')]]+=$row[csf('in_qty')];
					$po_data_arr[$row[csf('po_breakdown_id')]]['transfe_out'][$row[csf('item_group_id')]]+=$row[csf('out_qty')];

					$po_data_arr[$row[csf('po_breakdown_id')]]['transfe_amount'][$row[csf('item_group_id')]]+=$transfe_amount;
					$po_data_arr[$row[csf('po_breakdown_id')]]['transfe_in_amount'][$row[csf('item_group_id')]]+=$row[csf('in_amount')];
					$po_data_arr[$row[csf('po_breakdown_id')]]['transfe_out_amount'][$row[csf('item_group_id')]]+=$row[csf('out_amount')];
				}
				unset($transfer_qty_data);


				$issue_qty_data=sql_select("select b.po_breakdown_id, p.item_group_id,sum(b.quantity) as quantity, sum(b.quantity*b.order_rate) as issue_amount
			from inv_issue_master d, product_details_master p, inv_trims_issue_dtls a, order_wise_pro_details b
			where a.mst_id=d.id and a.prod_id=p.id and a.trans_id=b.trans_id and b.trans_type=2 and d.entry_form=25 and b.entry_form=25 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_cond1 $trm_group_iss_cond group by b.po_breakdown_id, p.item_group_id");
				//echo $issue_qty_data; die;
				foreach($issue_qty_data as $row)
				{
					$po_data_arr[$row[csf('po_breakdown_id')]]['issue_qty'][$row[csf('item_group_id')]]+=$row[csf('quantity')];
					$po_data_arr[$row[csf('po_breakdown_id')]]['issue_amount'][$row[csf('item_group_id')]]+=$row[csf('amount')];
				}
				unset($issue_qty_data);


				$issue_rtn_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id, d.quantity as quantity, (d.quantity*c.avg_rate_per_unit) as amount from product_details_master c,order_wise_pro_details d where d.prod_id=c.id and d.trans_type=4 and d.entry_form=73 and d.status_active=1 and d.is_deleted=0 $order_cond2 order by c.item_group_id");
				foreach($issue_rtn_qty_data as $row)
				{
					$po_data_arr[$row[csf('po_breakdown_id')]]['issue_rtn_qty'][$row[csf('item_group_id')]]+=$row[csf('quantity')];
					$po_data_arr[$row[csf('po_breakdown_id')]]['issue_rtn_amt'][$row[csf('item_group_id')]]+=$row[csf('amount')];
				}

				unset($issue_rtn_qty_data);


				/*$sql_rec_rtn_data=sql_select("select d.po_breakdown_id, c.item_group_id,sum(d.quantity) as quantity   from  inv_issue_master a,inv_transaction b, product_details_master c, order_wise_pro_details d,inv_receive_master e  where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and e.id=a.received_id   and b.transaction_type=3 and a.entry_form=49 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $order_cond2  group by d.po_breakdown_id, c.item_group_id order by c.item_group_id");

				foreach($sql_rec_rtn_data as $row)
				{
					$po_data_arr[$row[csf('po_breakdown_id')]]['receive_rtn_qty'][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				}
				unset($sql_rec_rtn_data);*/

				/*$sql_issue_data=sql_select("select b.po_breakdown_id, a.item_group_id,sum(b.quantity) as quantity  from  inv_issue_master d,product_details_master p,inv_trims_issue_dtls a , order_wise_pro_details b where a.mst_id=d.id and a.prod_id=p.id and a.trans_id=b.trans_id and b.trans_type=2 and b.entry_form=25 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $order_cond1  group by b.po_breakdown_id, a.item_group_id");
				foreach($sql_issue_data as $row)
				{
					$po_data_arr[$row[csf('po_breakdown_id')]]['issue_qty'][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				}
				unset($sql_issue_data);*/
				$bookingNoArr=return_library_array( "select id, booking_no from  wo_booking_mst", "id", "booking_no");
				$piArr=return_library_array("select id, pi_number from com_pi_master_details", "id", "pi_number");
				$total_pre_costing_value=0; $total_wo_value=0; $total_left_over_balanc=0;$total_issue_amount=0;$total_rec_bal_qnty=0;
				$summary_array=array();
				$i=1; $x=0;
				foreach($po_data_arr as $key=>$value)
				{
				    $z=1;
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					foreach($value['trim_group'] as $key_trim=>$value_trim)
					{   $y=1;
						$summary_array['trim_group'][$key_trim]=$key_trim;
						foreach($value[$key_trim] as $key_trim1=>$value_trim1)
						{
							if($z==1) $style_color=''; else $style_color=$bgcolor."; border: none";
							$z++;

							if($y==1) $style_colory=''; else $style_colory=$bgcolor."; border: none";
							$x++; $y++;
							$po_qty=0; $po_qty_set=0; $buyer_name=''; $job_no=''; $job_no_prefix_num=''; $style_ref_no=''; $grouping=''; $file_no=''; $order_uom=''; $po_number=''; $pub_shipment_date='';
							$po_qty=$po_arr[$key]['order_quantity'];
							$po_qty_set=$po_arr[$key]['order_quantity_set'];
							$buyer_name=$po_arr[$key]['buyer'];
							$job_no=$po_arr[$key]['job_no'];
							$job_no_prefix_num=$po_arr[$key]['job_no_prefix_num'];
							$style_ref_no=$po_arr[$key]['style_ref'];
							$grouping=$po_arr[$key]['grouping'];
							$file_no=$po_arr[$key]['file_no'];
							$order_uom=$po_arr[$key]['order_uom'];
							$po_number=$po_arr[$key]['po_number'];
							$pub_shipment_date=$po_arr[$key]['pub_shipment_date'];
						?>
						<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $x; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $x; ?>">
							<td width="30" style="color:<? echo $style_color; ?>" title="<? echo $po_qty; ?>"  ><? echo $i; ?></td>
							<td width="50" style="color:<? echo $style_color; ?>"><p><? echo $buyer_short_name_library[$buyer_name]; ?></p></td>
							<td width="100" style="color:<? echo $style_color; ?>" align="center" ><p><? echo $job_no_prefix_num; ?></p></td>
							<td width="100" style="word-break: break-all;color:<? echo $style_color ?>"><p><? echo $style_ref_no; ?></p></td>
							<td width="100" style="word-break: break-all;color:<? echo $style_color ?>"><p><? echo $grouping; ?></p></td>
							<td width="100" style="word-break: break-all;color:<? echo $style_color ?>"><p><? echo $file_no; ?></p></td>
							<td width="90" style="word-break: break-all;color:<? echo $style_color ?>"><p>
								<a style="word-break: break-all;color: <? echo $style_color ?>" href='#report_details' onclick="generate_report('<? echo $company_name; ?>','<? echo $job_no; ?>','<? echo $buyer_name; ?>','<? echo $style_ref_no; ?>','<? echo change_date_format($value['costing_date'][$key_trim1]); ?>','<? echo $key; ?>','<? echo $value['costing_per'][$key_trim1]; ?>','preCostRpt');"><? echo $po_number; ?></a></p></td>
                            <td width="80" style="word-break: break-all;color:<? echo $style_color ?>" align="right"><p><a style="word-break: break-all;color: <? echo $style_color ?>" href='#report_details' onclick="order_qty_popup('<? echo $company_name; ?>','<? echo $job_no; ?>','<? echo $key; ?>', '<? echo $buyer_name; ?>',<? echo $txt_date_from; ?>,<? echo $txt_date_to; ?>,'order_qty_data');"><? echo number_format($po_qty_set,0,'.',''); ?></a></p></td>

							<td width="50" align="center" style="word-break:break-all;color:<? echo $style_color; ?>"><p><? echo $unit_of_measurement[$order_uom]; ?></p></td>
							<td width="80" align="right" style="word-break: break-all;color:<? echo $style_color ?>"><p><? echo number_format($po_qty,0,'.',''); ?></p></td>
							<td width="80" align="center" style="word-break: break-all;color:<? echo $style_color ?>"><? echo change_date_format($pub_shipment_date); ?></td>
							<td width="100" title="<? echo $value['trim_group_from'][$key_trim1]; ?>" style="word-break: break-all;"><p><? echo $item_library[$value[trim_group_dtls][$key_trim1]]; ?></p></td>
                            <td width="140"><p><? echo $value['description'][$key_trim1]; ?></p></td>
							<td width="100"><p><? echo $value['remark'][$key_trim1]; ?></p></td>
							<td width="100"><p><? echo $value['brand_sup_ref'][$key_trim1]; ?></p></td>
							<td width="60" align="center"><p><? if($value['apvl_req'][$key_trim1]==1) echo "Yes"; else echo ""; ?></p></td>
							<td width="80" align="center"><?
								if($value['apvl_req'][$key_trim1]==1)
								{
									$app_status=$app_status_arr[$job_no][$value['trim_group_dtls'][$key_trim1]];
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
								echo $approved_status; ?></td>
                          	<td width="100"><p><? $insert_date=explode(" ",$value['insert_date'][$key_trim1]); echo change_date_format($insert_date[0],'','',''); ?></p></td>
                            <td width="80" align="right"><?=number_format($value['avg_cons'][$key_trim1],4); ?></td>
							<td width="100" align="right"><p>
                                <a href='#report_details' onclick="order_req_qty_popup('<? echo $company_name; ?>','<? echo $job_no; ?>','<? echo $key; ?>','<? echo $buyer_name; ?>','<? echo $value['rate'][$key_trim1]; ?>','<? echo $value['trim_group_dtls'][$key_trim1];?>' ,'<? echo $value['booking_no'][$key_trim1] ;?>','<? echo $value['description'][$key_trim1];?>','<? echo $value['country_id'][$key_trim1]; ?>','<? echo $value['trim_dtla_id'][$key_trim1]; ?>','<? echo $start_date ?>','<? echo $end_date ?>','order_req_qty_data');"><? $req_qty=number_format($value['req_qnty'][$key_trim1],2,'.',''); echo $req_qty;
								$summary_array[req_qnty][$key_trim]+=$value['req_qnty'][$key_trim1]; ?></a></p></td>
							<td width="100" align="right"><p><? echo number_format($value['req_value'][$key_trim1],2); $total_pre_costing_value+=$value['req_value'][$key_trim1]; ?></p></td>
                                <?
							    $wo_qnty=number_format($value['wo_qnty'][$key_trim1],2,'.','');

								/*if($wo_qnty > $req_qty) $color_wo="red";
								else if($wo_qnty < $req_qty ) $color_wo="yellow";
								else $color_wo="";*/

								if(($value['conversion_factor_rate'][$key_trim1]*$wo_qnty)==$req_qty) $color_wo="green";
								else if(($value['conversion_factor_rate'][$key_trim1]*$wo_qnty) > $req_qty) $color_wo="red";
								else if(($value['conversion_factor_rate'][$key_trim1]*$wo_qnty) < $req_qty ) $color_wo="yellow";
								else $color_wo="";

								$supplier_name_string="";
								$supplier_id_arr=array_unique(explode(',',$value['supplier_id'][$key_trim1]));
								//print_r($supplier_id_arr);
								foreach($supplier_id_arr as $supplier_id_arr_key=>$supplier_id_arr_value)
								{
									$ex_sup_data=explode("**",$supplier_id_arr_value);
									if($ex_sup_data[1]==3 || $ex_sup_data[1]==5) $suplier_name_arr=$company_library; else $suplier_name_arr=$lib_supplier_arr;
									$supplier_name_string.=$suplier_name_arr[$ex_sup_data[0]].",";
								}

								$booking_no_arr=array_unique(explode(',',$value['booking_no'][$key_trim1]));
								$main_booking_no_large_data=""; $piWoNo='';
								foreach($booking_no_arr as $booking_no1)
								{
									if($main_booking_no_large_data=="") $main_booking_no_large_data=$booking_no1; else $main_booking_no_large_data.=",".$booking_no1;
									if($booking_no1!="")
									{
										if($piWoNo=="") $piWoNo=implode(',',array_filter(array_unique(explode("**",$pi_arr[$booking_no1])))); else $piWoNo.=",".implode(',',array_filter(array_unique(explode("**",$pi_arr[$booking_no1]))));//
									}
								}
								?>
							<td width="90" align="right" title="<? echo 'conversion_factor='.$value['conversion_factor_rate'][$key_trim1];?>" bgcolor="<? echo $color_wo;?>"><p><a href='#report_details' onclick="openmypage('<? echo $key; ?>','<? echo $value['trim_group_dtls'][$key_trim1]; ?>','<? echo $job_no; ?>','<? echo $main_booking_no_large_data;?>','<? echo $value['trim_dtla_id'][$key_trim1];?>','booking_info');">
								<? echo number_format($value['wo_qnty'][$key_trim1],2,'.',''); $summary_array[wo_qnty][$key_trim]+=$value['wo_qnty'][$key_trim1]; ?></a></p></td>
                            <td width="60"><p><? echo $unit_of_measurement[$item_arr[$value['trim_group_dtls'][$key_trim1]]['order_uom']];//    $item_arr[$key['trim_group_dtls'][$key_trim1]]['order_uom'];
								$summary_array[cons_uom][$key_trim]=$item_arr[$value['trim_group_dtls'][$key_trim1]]['order_uom']; ?></p></td>
                            <td width="100" align="right" title="<? echo number_format($value['rate'][$key_trim1],2,'.',''); ?>">
                                <p><? echo number_format($value['amount'][$key_trim1],2,'.','');  $total_wo_value+=$value['amount'][$key_trim1]; ?></p></td>
                            <td width="150"><p><? echo rtrim($supplier_name_string,","); ?></p></td>
                            <td width="70" title="<? echo change_date_format($value['wo_date'][$key_trim1]);?>"><p><?
								$tot=change_date_format($insert_date[0]);
								if($value['wo_qnty'][$key_trim1]<=0 )
									$daysOnHand = datediff('d',$tot,$today);
								else
								{
									$wo_date=$value['wo_date'][$key_trim1];
									$wo_date=change_date_format($wo_date);
									$daysOnHand = datediff('d',$tot,$wo_date);;
								}
								echo $daysOnHand; ?></p></td>
                                <?
								$transfe_out=number_format($value['transfe_out'][$key_trim],2,'.','');
								$transfe_in=number_format($value['transfe_in'][$key_trim],2,'.','');
								$transfe_in_out=$transfe_in.' & '.$transfe_out;

								$transfe_out_amt=number_format($value['transfe_out_amount'][$key_trim],2,'.','');
								$transfe_in_amt=number_format($value['transfe_in_amount'][$key_trim],2,'.','');
								$transfe_in_out_amt=$transfe_in_amt.' & '.$transfe_out_amt;
								$inhouse_amount=0;
								$inhouse_qnty=($value['inhouse_qnty'][$key_trim]+$value['transfe_qty'][$key_trim])-$value['receive_rtn_qty'][$key_trim];
								$inhouse_amount=($value['inhouse_amount'][$key_trim]+$value['transfe_amount'][$key_trim])-$value['receive_rtn_amt'][$key_trim];
								$total_inhouse_value+=$inhouse_amount;
								$balance=$value['wo_qnty_trim_group'][$key_trim]-$inhouse_qnty;
								$issue_qnty=$value['issue_qty'][$key_trim]-$value['issue_rtn_qty'][$key_trim];
								$issue_amount=$value['issue_amount'][$key_trim]-$value['issue_rtn_amt'][$key_trim];
								$left_overqty=$inhouse_qnty-$issue_qnty;
								$left_overamt=$inhouse_amount-$issue_amount;

								$summary_array[inhouse_qnty][$key_trim]+=$inhouse_qnty;
								$summary_array[inhouse_qnty_bl][$key_trim]+=$balance;
								$summary_array[issue_qty][$key_trim]+=$issue_qnty;
								$summary_array[left_overqty][$key_trim]+=$left_overqty;
								?>
                             <td width="90" rowspan="<? echo $rowspannn; ?>"><p><? echo $piWoNo; ?></p></td>
                            <td width="90" align="right" style="color:<? echo $style_colory ?>" title="<? echo "Inhouse-Qty: ".number_format($value['inhouse_qnty'][$key_trim]-$value['receive_rtn_qty'][$key_trim],2,'.','')."\n Transfer In & Out Qty: ".$transfe_in_out."\n Return Qty: ".number_format($value['receive_rtn_qty'][$key_trim],2,'.',''); ?>"><a style="color:<? echo $style_colory ?>" href='#report_details' onclick="openmypage_inhouse('<? echo $key; ?>','<? echo $value['trim_group_dtls'][$key_trim1]; ?>','booking_inhouse_info');"><? echo number_format($inhouse_qnty,2,'.',''); ?></a></td>
                             <td width="90" align="right" style="color:<? echo $style_colory ?>" title="<? echo "Inhouse-Amt: ".number_format($value['inhouse_qnty'][$key_trim]-$value['receive_rtn_amt'][$key_trim],2,'.','')."\n Transfer In & Out Amt: ".$transfe_in_out_amt."\n Return Amt: ".number_format($value['receive_rtn_amt'][$key_trim],2,'.',''); ?>"><? echo number_format($inhouse_amount,2,'.',''); ?></td>
							<td width="90" align="right" style="color:<? echo $style_colory ?>"><? echo number_format($balance,2,'.',''); $total_rec_bal_qnty+=$balance; ?></td>
							<td width="90" align="right" title="<? echo "Issue-Qty: ".number_format($value['issue_qty'][$key_trim],2,'.','')."\n Issue Return Qty: ".number_format($value['issue_rtn_qty'][$key_trim],2,'.',''); ?>" style="color:<? echo $style_colory ?>"><a style="color:<? echo $style_colory ?>" href='#report_details' onclick="openmypage_issue('<? echo $key; ?>','<? echo $value['trim_group_dtls'][$key_trim1]; ?>','booking_issue_info');"><? echo number_format($issue_qnty,2,'.',''); ?></a></td>
                            <td width="90" align="right" title="<? echo "Issue-Amt: ".number_format($value['issue_amount'][$key_trim],2,'.','')."\n Issue Return Amt: ".number_format($value['issue_rtn_amt'][$key_trim],2,'.',''); ?>" style="color:<? echo $style_colory ?>"><? echo number_format($issue_amount,2,'.',''); $total_issue_amount+=$issue_amount; ?></td>
							<td width="90" align="right" style="color:<? echo $style_colory ?>"><? echo number_format($left_overqty,2,'.',''); ?></td>
                            <td align="right" style="color:<? echo $style_colory ?>"><? echo number_format($left_overamt,2,'.',''); $total_left_over_balanc+=$left_overamt; ?></td>
						</tr>
						<?
					}// end  foreach($value[$key_trim] as $key_trim1=>$value_trim1)
				}
				$i++;
			}
			unset($po_data_arr);
			?>
			</table>
            </div>
            <table class="rpt_table" width="3040" cellpadding="0" cellspacing="0" border="1" rules="all">
                <tfoot>
                    <th width="30">&nbsp;</th>
                    <th width="50">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="90">&nbsp;</th>
                    <th width="80" align="right" id="total_order_qnty"><? //echo number_format($total_order_qnty,0); ?></th>
                    <th width="50">&nbsp;</th>
                    <th width="80" align="right" id="total_order_qnty_in_pcs"><? //echo number_format($total_order_qnty_in_pcs,0); ?></th>
                    <th width="80">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="140">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="100" align="right" id="value_req_qnty"><? //echo number_format($total_req_qnty,2); ?></th>
                    <th width="100" align="right" id="value_pre_costing"><? echo number_format($total_pre_costing_value,2); ?></th>
                    <th width="90" align="right" id=""><? //echo number_format($total_wo_qnty,2); ?></th>
                    <th width="60" align="right" ></th>
                    <th width="100" align="right" id="value_wo_qty"><? //echo number_format($total_wo_value,2); ?></th>
                    <th width="150" align="right" id=""></th>
                    <th width="70" align="right"><p><? //echo number_format($req_value,2,'.',''); ?>&nbsp;</p></th>
                    <th width="90">&nbsp;</th>
                    <th width="90" align="right" id="value_in_qty"><? //echo number_format($total_in_qnty,2); ?></th>
                    <th width="90" align="right" id="value_in_amount"><? echo number_format($total_inhouse_value,2); ?></th>
                    <th width="90" align="right" id="value_rec_qty"><? echo number_format($total_rec_bal_qnty,2); ?></th>
                    <th width="90" align="right" id="value_issue_qty"><? //echo number_format($total_issue_qnty,2); ?></th>
                    <th width="90" align="right" id="value_issue_amount"><? echo number_format($total_issue_amount,2); ?></th>
                    <th width="90" align="right" id="value_leftover_qty"><? //echo number_format($total_leftover_qnty,2); ?></th>
                    <th align="right" id="value_leftover_amount"><? echo number_format($total_left_over_balanc,2); ?></th>
                </tfoot>
            </table>
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
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr2_<? echo $z; ?>','<? echo $bgcolor;?>')" id="tr2_<? echo $z; ?>">
							<td width="30"><? echo $z; ?></td>
							<td width="110"><p><? echo $item_library[$key_trim]; ?></p></td>
							<td width="60" align="center"><? echo $unit_of_measurement[$summary_array[cons_uom][$key_trim]]; ?></td>
							<td width="80" align="right"><?
							//$app_perc=($summary_array[item_app][$key_trim][app]*100)/$summary_array[item_app][$key_trim][all]; echo number_format($app_perc,2);
							$app_perc=($summary_array[item_app][$key_trim][app]*100)/$summary_array[item_app][$key_trim][all]; if ($app_perc>=0) echo $app_perc; ?></td>
							<td width="110" align="right"><? echo number_format($summary_array[req_qnty][$key_trim],2); ?></td>
							<td width="110" align="right"><? echo number_format( $summary_array[wo_qnty][$key_trim],2); ?></td>
							<td width="80" align="right"><? $wo_per= $summary_array[wo_qnty][$key_trim]/$summary_array[req_qnty][$key_trim]*100; echo number_format($wo_per,2).'%'; ?></td>
							<td width="110" align="right"><? echo number_format($summary_array[inhouse_qnty][$key_trim],2); ?></td>
							<td width="80" align="right"><? $in_per=$summary_array[inhouse_qnty][$key_trim]/$summary_array[wo_qnty][$key_trim]*100; echo number_format($in_per,2).'%'; ?></td>
							<td width="110" align="right"><? echo number_format($summary_array[inhouse_qnty_bl][$key_trim],2); $in_house_bal+=($value['wo']-$value['in']); ?></td>
							<td width="110" align="right"><? echo number_format($summary_array[issue_qty][$key_trim],2); ?></td>
							<td width="80" align="right"><? $wo_per=$summary_array[issue_qty][$key_trim]/$summary_array[wo_qnty][$key_trim]*100; echo number_format($wo_per,2).'%'; ?></td>
							<td align="right"><? echo number_format($summary_array[left_overqty][$key_trim],2); ?></td>
						</tr>
					<?
					$z++;
					}
					unset($summary_array);
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
 	else if(str_replace("'","",$cbo_search_by)==2)
	{
		if($template==1)
		{
			ob_start();
			?>
			<div style="width:2980px">
			<fieldset style="width:100%;">
			<table width="2980">
                <tr class="form_caption"><td colspan="32" align="center"><? echo $report_title; ?></td></tr>
                <tr class="form_caption"><td colspan="32" align="center"><? echo $company_library[$company_name]; ?></td></tr>
			</table>
			<table class="rpt_table" width="2940" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <th width="30">SL</th>
                    <th width="50">Buyer</th>
                    <th width="100">Job No</th>
                    <th width="100">Style Ref</th>
                    <th width="100">Internal Ref</th>
                    <th width="100">File No</th>
                    <th width="90">Order No</th>
                    <th width="80">Order Qty</th>
                    <th width="50">UOM</th>
                    <th width="80">Qty (Pcs)</th>
                    <th width="80">Shipment Date</th>
                    <th width="100">Trims Name</th>
                    <th width="140">Item Description</th>
                    <th width="100">Brand/Sup Ref</th>
                    <th width="60">Appr Req.</th>
                    <th width="80">Approve Status</th>
                    <th width="100">Item Entry Date</th>
                    <th width="80">Avg. Cons</th>
                    <th width="100">Req Qty</th>
                    <th width="100">Pre Costing Value</th>
                    <th width="90">WO Qty</th>
                    <th width="60">Trims UOM</th>
                    <th width="100">WO Value (USD)</th>
                    <th width="150">Supplier</th>
                    <th width="70">WO Delay Days</th>
                    <th width="90">PI No.</th>
                    <th width="90">In-House Qty</th>
                    <th width="90">In-House Amount</th>
                    <th width="90">Receive Balance</th>
                    <th width="90">Issue to Prod.</th>
                    <th width="90">Issue Amount</th>
                    <th width="90">Left Over/Balance Qty</th>
                    <th>Left Over/Balance Amount</th>
                </thead>
			</table>
            <div style="width:2960px; max-height:400px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="2940" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
                <?
				$conversion_factor_array=array(); $item_arr=array();
				$conversion_factor=sql_select("select id ,trim_uom,order_uom,conversion_factor from  lib_item_group  where status_active=1 and item_category=4");
				foreach($conversion_factor as $row_f)
				{
					$conversion_factor_array[$row_f[csf('id')]]['con_factor']=$row_f[csf('conversion_factor')];
					$conversion_factor_array[$row_f[csf('id')]]['cons_uom']=$row_f[csf('trim_uom')];
					$item_arr[$row_f[csf('id')]]['order_uom']=$row_f[csf('order_uom')];
				}
				unset($conversion_factor);

				$app_sql=sql_select("select job_no_mst,accessories_type_id,approval_status from wo_po_trims_approval_info");
				$app_status_arr=array();
				foreach($app_sql as $row)
				{
					$app_status_arr[$row[csf("job_no_mst")]][$row[csf("accessories_type_id")]]=$row[csf("approval_status")];
				}
				unset($app_sql);

				$sql_po_qty_country_wise_arr=array();
				$po_job_arr=array(); $style_po_qty_arr=array();
				$sql_po_qty_country_wise=sql_select("select  b.id, b.job_no_mst, c.country_id, sum(c.order_quantity/a.total_set_qnty) as order_quantity_set, sum(c.order_quantity) as order_quantity  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst  and b.id=c.po_break_down_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $year_cond group by b.id, b.job_no_mst, c.country_id order by b.id, b.job_no_mst, c.country_id");
				foreach( $sql_po_qty_country_wise as $sql_po_qty_country_wise_row)
				{
					$sql_po_qty_country_wise_arr[$sql_po_qty_country_wise_row[csf('id')]][$sql_po_qty_country_wise_row[csf('country_id')]]=$sql_po_qty_country_wise_row[csf('order_quantity_set')];
					$po_job_arr[$sql_po_qty_country_wise_row[csf('id')]]=$sql_po_qty_country_wise_row[csf('job_no_mst')];
					$style_po_qty_arr[$sql_po_qty_country_wise_row[csf('job_no_mst')]]['order_qty_set']+=$sql_po_qty_country_wise_row[csf('order_quantity_set')];
					$style_po_qty_arr[$sql_po_qty_country_wise_row[csf('job_no_mst')]]['po_qty']+=$sql_po_qty_country_wise_row[csf('order_quantity')];
				}
				//print_r($style_po_qty_arr);
				unset($sql_po_qty_country_wise);

				$style_data_arr=array();
				$po_id_string="";
				$today=date("Y-m-d");
				$sql_pos=("select a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.order_uom, b.file_no, b.grouping, b.id, b.po_number, sum(c.order_quantity) as order_quantity, sum(c.order_quantity/a.total_set_qnty) as order_quantity_set, b.pub_shipment_date
				from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
				where
				a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.company_name=$company_name $buyer_id_cond ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $date_cond $style_ref_cond $jobcond  $ordercond $file_no_cond $internal_ref_cond  $year_cond
				group by a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.order_uom, a.total_set_qnty, b.file_no, b.grouping, b.id, b.po_number, b.pub_shipment_date order by b.id");
				//echo $sql_pos; die;//and a.job_no='FAL-16-00179'
				$sql_po=sql_select($sql_pos);
				$tot_rows=0;  $style_data_all=array();
				foreach($sql_po as $row)
				{
					$tot_rows++;

					$style_data[$row[csf('job_no')]]['job_data']=$row[csf("buyer_name")]."##".$row[csf("job_no_prefix_num")]."##".$row[csf("style_ref_no")]."##".$row[csf("order_uom")];

					$style_data_all[$row[csf('job_no')]].=$row[csf("file_no")]."__".$row[csf("grouping")]."__".$row[csf("po_number")]."__".$row[csf("pub_shipment_date")]."__".$row[csf("shiping_status")]."__".$row[csf("id")]."***";

					$po_arr[$row[csf('job_no')]]['order_quantity']+=$row[csf('order_quantity')];
					$po_arr[$row[csf('job_no')]]['order_quantity_set']+=$row[csf('order_quantity_set')];
					$po_id_string.=$row[csf('id')].",";
				}
				//print_r($style_data_all); die;
				unset($sql_po);
				$po_id_string=rtrim($po_id_string,",");
				if($po_id_string=="")
				{
					echo "<strong style='color:red;font-size:30px'>No Data found</strong>";
					die;
				}

				$poIds=chop($po_id_string,','); $order_cond=""; $order_cond1=""; $order_cond2=""; $precost_po_cond="";
				if($db_type==2 && $tot_rows>1000)
				{
					$order_cond=" and (";
					$order_cond1=" and (";
					$order_cond2=" and (";
					$precost_po_cond=" and (";
					$poIdsArr=array_chunk(explode(",",$poIds),999);
					foreach($poIdsArr as $ids)
					{
						$ids=implode(",",$ids);
						//$poIds_cond.=" po_break_down_id in($ids) or ";
						$order_cond.=" b.po_break_down_id in($ids) or";
						$order_cond1.=" b.po_breakdown_id in($ids) or";
						$order_cond2.=" d.po_breakdown_id in($ids) or";
						$precost_po_cond.=" c.po_break_down_id in($ids) or";
					}
					$order_cond=chop($order_cond,'or ');

					$order_cond.=")";
					$order_cond1=chop($order_cond1,'or ');
					$order_cond1.=")";
					$order_cond2=chop($order_cond2,'or ');
					$order_cond2.=")";
					$precost_po_cond=chop($precost_po_cond,'or ');
					$precost_po_cond.=")";
				}
				else
				{
					$order_cond=" and b.po_break_down_id in($poIds)";
					$order_cond1=" and b.po_breakdown_id in($poIds)";
					$order_cond2=" and d.po_breakdown_id in($poIds)";
					$precost_po_cond=" and c.po_break_down_id in($poIds)";
				}

				$condition= new condition();
				if(str_replace("'","",$txt_job_no) !=''){
					$condition->job_no_prefix_num("=$txt_job_no");
				}
				if(str_replace("'","",$txt_order_no)!='')
				{
					//$condition->po_number("=$txt_order_no");
					$order_nos=str_replace("'","",$txt_order_no);
					$condition->po_number(" like '%$order_nos%'");
				}

				if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
				{
					$start_date=(str_replace("'","",$txt_date_from));
					$end_date=(str_replace("'","",$txt_date_to));
				}

				if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
				{
					  $condition->country_ship_date(" between '$start_date' and '$end_date'");
				}
				$condition->init();
				$trim= new trims($condition);
				//$trim_qty=$trim->getQtyArray_by_orderAndPrecostdtlsid();
				$trim_qty=$trim->getQtyArray_by_jobAndPrecostdtlsid();
				//print_r($trim_qty);
				$trim= new trims($condition);
				$trim_amount=$trim->getAmountArray_by_jobAndPrecostdtlsid();
				$costing_arr=array();
				$sql_pre_cost=sql_select("select a.costing_per, a.costing_date, b.id as trim_dtla_id, b.trim_group, b.remark, b.description, b.brand_sup_ref, b.cons_uom, b.cons_dzn_gmts, b.rate, b.amount, b.apvl_req, b.nominated_supp, b.insert_date, c.cons, c.country_id, c.cons as cons_cal, c.po_break_down_id, b.job_no
				from wo_pre_cost_mst a, wo_pre_cost_trim_cost_dtls b, wo_pre_cost_trim_co_cons_dtls c
				where c.job_no=b.job_no and b.id=c.wo_pre_cost_trim_cost_dtls_id and a.job_no=b.job_no and c.cons>0 $trm_group_pre_cost_cond $precost_po_cond
				group by a.costing_per, a.costing_date, b.id, b.trim_group, b.remark, b.description, b.brand_sup_ref, b.cons_uom, b.cons_dzn_gmts, b.rate, b.amount, b.apvl_req, b.nominated_supp, b.insert_date, c.cons, c.pcs, c.country_id, c.po_break_down_id, b.job_no order by b.trim_group");

				if(count($sql_pre_cost)>0)
				{
					foreach($sql_pre_cost as $rowp)
					{
						$dzn_qnty=0;

						if($rowp[csf('costing_per')]==1) $dzn_qnty=12;
						else if($rowp[csf('costing_per')]==3) $dzn_qnty=12*2;
						else if($rowp[csf('costing_per')]==4) $dzn_qnty=12*3;
						else if($rowp[csf('costing_per')]==5) $dzn_qnty=12*4;
						else $dzn_qnty=1;

						$po_qty=0; $req_qnty=0; $req_value=0;
						if($rowp[csf('country_id')]==0)
						{
							$po_qty=$po_arr[$rowp[csf('job_no')]]['order_quantity'];
							//$po_qty=$po_arr[$rowp[csf('job_no')]]['order_quantity'];
							//$req_qnty+=$trim_qty[$rowp[csf('po_break_down_id')]][$rowp[csf('country_id')]][$rowp[csf('trim_dtla_id')]];
						}
						else
						{
							$country_id= explode(",",$rowp[csf('country_id')]);
							for($cou=0;$cou<=count($country_id); $cou++)
							{
								$po_qty+=$sql_po_qty_country_wise_arr[$rowp[csf('po_break_down_id')]][$country_id[$cou]];
								//$po_qty+=$sql_po_qty_country_wise_arr[$rowp[csf('po_break_down_id')]][$country_id[$cou]];
								//$req_qnty+=$trim_qty[$rowp[csf('po_break_down_id')]][$country_id[$cou]][$rowp[csf('trim_dtla_id')]];
							}
						}
						$req_qnty=$trim_qty[$rowp[csf('job_no')]][$rowp[csf('trim_dtla_id')]];
						$req_value=$trim_amount[$rowp[csf('job_no')]][$rowp[csf('trim_dtla_id')]];

						//$req_value=$trim_amount[$rowp[csf('po_break_down_id')]][$rowp[csf('trim_dtla_id')]];

						$style_data_arr[$rowp[csf('job_no')]]['trim_dtla_id'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('trim_dtla_id')];// for rowspan
						$style_data_arr[$rowp[csf('job_no')]]['trim_group'][$rowp[csf('trim_group')]]=$rowp[csf('trim_group')];
						$style_data_arr[$rowp[csf('job_no')]][$rowp[csf('trim_group')]][$rowp[csf('trim_dtla_id')]]=$rowp[csf('trim_dtla_id')]; // for rowspannn
						$style_data_arr[$rowp[csf('job_no')]]['trim_group_dtls'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('trim_group')];
						$style_data_arr[$rowp[csf('job_no')]]['remark'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('remark')];
						$style_data_arr[$rowp[csf('job_no')]]['brand_sup_ref'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('brand_sup_ref')];
						$style_data_arr[$rowp[csf('job_no')]]['apvl_req'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('apvl_req')];
						$style_data_arr[$rowp[csf('job_no')]]['insert_date'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('insert_date')];
						$style_data_arr[$rowp[csf('job_no')]]['req_qnty'][$rowp[csf('trim_dtla_id')]]=$req_qnty;
						$style_data_arr[$rowp[csf('job_no')]]['req_value'][$rowp[csf('trim_dtla_id')]]=$req_value;
						$style_data_arr[$rowp[csf('job_no')]]['cons_uom'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('cons_uom')];
						$style_data_arr[$rowp[csf('job_no')]]['trim_group_from'][$rowp[csf('trim_dtla_id')]]="Pre_cost";
						$style_data_arr[$rowp[csf('job_no')]]['rate'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('rate')];
						$style_data_arr[$rowp[csf('job_no')]]['description'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('description')];
						$style_data_arr[$rowp[csf('job_no')]]['country_id'][$rowp[csf('trim_dtla_id')]].=$rowp[csf('country_id')].',';
						$style_data_arr[$rowp[csf('job_no')]]['avg_cons'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('cons_dzn_gmts')];

						$costing_arr[$rowp[csf('job_no')]]['costing_per']=$rowp[csf('costing_per')];
						$costing_arr[$rowp[csf('job_no')]]['costing_date']=$rowp[csf('costing_date')];
					}
				}
				else
				{
					echo "<strong style='color:red;font-size:30px'>No Data found</strong>";
					die;
				}
				unset($sql_pre_cost);

				if($db_type==2)
				{
					$sql_without_precost=sql_select("select min(a.booking_date) as booking_date ,b.job_no,LISTAGG(CAST(a.booking_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as booking_no, LISTAGG(CAST( a.supplier_id || '**' || a.pay_mode AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.booking_no) as supplier_id, b.po_break_down_id, b.trim_group,b.pre_cost_fabric_cost_dtls_id, sum(b.wo_qnty) as wo_qnty,sum(b.amount/b.exchange_rate) as amount,sum(b.rate) as rate from wo_booking_mst a, wo_booking_dtls b where a.item_category=4 and a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name $order_cond group by b.po_break_down_id,b.trim_group,b.job_no,b.pre_cost_fabric_cost_dtls_id");//and item_from_precost=2
				}
				else if($db_type==0)
				{
					$sql_without_precost=sql_select("select min(a.booking_date) as booking_date ,b.job_no,group_concat(a.booking_no) as booking_no, group_concat(concat_ws('**',a.supplier_id,a.pay_mode)) as supplier_id, b.po_break_down_id, b.trim_group,b.pre_cost_fabric_cost_dtls_id,sum(b.wo_qnty) as wo_qnty,sum(b.amount/b.exchange_rate) as amount,sum(b.rate) as rate from wo_booking_mst a, wo_booking_dtls b where a.item_category=4 and a.booking_no=b.booking_no  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name $order_cond  group by b.po_break_down_id,b.trim_group,b.job_no,b.pre_cost_fabric_cost_dtls_id");//and item_from_precost=2
				}
				$style_data_arr1=array();
				foreach($sql_without_precost as $row_precost)
				{
					$conversion_factor_rate=$conversion_factor_array[$row_precost[csf('trim_group')]]['con_factor'];
					//$cons_uom=$item_arr[$wo_row_without_precost[csf('trim_group')]]['order_uom'];
					$cons_uom=$conversion_factor_array[$row_precost[csf('trim_group')]]['cons_uom'];
					$booking_no=$row_precost[csf('booking_no')];
					$supplier_id=$row_precost[csf('supplier_id')];
					$wo_qnty=$row_precost[csf('wo_qnty')];//*$conversion_factor_rate;
					$amount=$row_precost[csf('amount')];
					$wo_date=$row_precost[csf('booking_date')];

					$job_no=''; $job_no=$row_precost[csf('job_no')];

					if($row_precost[csf('pre_cost_fabric_cost_dtls_id')] =="" || $row_precost[csf('pre_cost_fabric_cost_dtls_id')] ==0)
					{
						//echo $wo_row_without_precost[csf('trim_group')];
						$trim_dtla_id=max($style_data_arr[$row_precost[csf('job_no')]]['trim_dtla_id'])+1;
						$style_data_arr[$job_no]['trim_dtla_id'][$trim_dtla_id]=$trim_dtla_id;// for rowspan
						$style_data_arr[$job_no]['trim_group'][$row_precost[csf('trim_group')]]=$row_precost[csf('trim_group')];
						$style_data_arr[$job_no][$row_precost[csf('trim_group')]][$trim_dtla_id]=$trim_dtla_id;// for rowspannn
						$style_data_arr[$job_no]['trim_group_dtls'][$trim_dtla_id]=$row_precost[csf('trim_group')];
						$style_data_arr[$job_no]['cons_uom'][$trim_dtla_id]=$cons_uom;

						$style_data_arr[$job_no]['trim_group_from'][$trim_dtla_id]="Booking Without Pre_cost";
					}
					else
					{
						$trim_dtla_id=$row_precost[csf('pre_cost_fabric_cost_dtls_id')];
					}
					$style_data_arr[$job_no]['wo_qnty'][$trim_dtla_id]+=$wo_qnty;
					$style_data_arr[$job_no]['amount'][$trim_dtla_id]+=$amount;
					$style_data_arr[$job_no]['wo_date'][$trim_dtla_id]=$wo_date;
					$style_data_arr[$job_no]['wo_qnty_trim_group'][$row_precost[csf('trim_group')]]+=$wo_qnty;

					$style_data_arr2[$job_no]['booking_no'][$trim_dtla_id].=$booking_no.",";
					$style_data_arr[$job_no]['booking_no'][$trim_dtla_id].=$booking_no.",";
					$style_data_arr[$job_no]['supplier_id'][$trim_dtla_id].=$supplier_id.",";
					$style_data_arr[$job_no]['conversion_factor_rate'][$trim_dtla_id]=$conversion_factor_rate;
				}
				unset($sql_without_precost);

				$sql_rec_data=sql_select("select b.po_breakdown_id, a.item_group_id, c.receive_basis, a.booking_id, b.quantity as quantity, a.rate, c.exchange_rate, (b.quantity*d.avg_rate_per_unit) as amount from inv_receive_master c, product_details_master d, inv_trims_entry_dtls a, order_wise_pro_details b where a.mst_id=c.id and a.trans_id=b.trans_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_cond1 $trm_group_rec_cond order by a.item_group_id ");

				foreach($sql_rec_data as $row)
				{
					$poId=0; $poId=$row[csf('po_breakdown_id')];
					if($style_data_arr[$po_job_arr[$poId]]['trim_group'][$row[csf('item_group_id')]]=="" || $style_data_arr[$po_job_arr[$poId]]['trim_group'][$row[csf('item_group_id')]]==0)
					{
						$cons_uom=$conversion_factor_array[$row[csf('item_group_id')]]['cons_uom'];
						$trim_dtla_id=max($style_data_arr[$po_job_arr[$poId]]['trim_dtla_id'])+1;
						$style_data_arr[$po_job_arr[$poId]]['trim_dtla_id'][$trim_dtla_id]=$trim_dtla_id;// for rowspan
						$style_data_arr[$po_job_arr[$poId]]['trim_group'][$row[csf('item_group_id')]]=$row[csf('item_group_id')];
						$style_data_arr[$po_job_arr[$poId]][$row[csf('item_group_id')]][$trim_dtla_id]=$trim_dtla_id;// for rowspannn
						$style_data_arr[$po_job_arr[$poId]]['trim_group_dtls'][$trim_dtla_id]=$row[csf('item_group_id')];
						$style_data_arr[$po_job_arr[$poId]]['cons_uom'][$trim_dtla_id]=$cons_uom;

						$style_data_arr[$po_job_arr[$poId]]['trim_group_from'][$trim_dtla_id]="Trim Receive";
					}
					$style_data_arr[$po_job_arr[$poId]]['inhouse_qnty'][$row[csf('item_group_id')]]+=$row[csf('quantity')];
					$amount=0;  $amount=($row[csf('quantity')]*$row[csf('rate')]);//*$row[csf('exchange_rate')];
					$style_data_arr[$po_job_arr[$poId]]['inhouse_amount'][$row[csf('item_group_id')]]+=$amount;
				}
				unset($sql_rec_data);

				$receive_rtn_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id, d.quantity as quantity, c.avg_rate_per_unit as rate
				from product_details_master c,order_wise_pro_details d
				where d.prod_id=c.id and d.trans_type=3 and d.entry_form=49 and d.status_active=1 and d.is_deleted=0 $order_cond2");
				foreach($receive_rtn_qty_data as $row)
				{
					$receive_rtn_amt=0;
					//$conv_quantity=$row[csf('quantity')]/$conversion_factor_array[$row[csf('item_group_id')]]['con_factor'];
					$conv_quantity=$row[csf('quantity')];
					$receive_rtn_amt=$conv_quantity*$row[csf('rate')];
					$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['receive_rtn_qty'][$row[csf('item_group_id')]]+=$conv_quantity;
					$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['receive_rtn_amt'][$row[csf('item_group_id')]]+=$receive_rtn_amt;
				}
				//echo "<pre>";print_r($style_data_arr);
				unset($receive_rtn_qty_data);

				$transfer_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id, sum((case when d.trans_type=5 then d.quantity else 0 end)-(case when d.trans_type=6 then d.quantity else 0 end)) as quantity,
				sum(case when d.trans_type=5 then d.quantity else 0 end) as in_qty,
				sum(case when d.trans_type=6 then d.quantity else 0 end) as out_qty,
				sum(case when d.trans_type=5 then (d.quantity*c.avg_rate_per_unit) else 0 end) as in_amount,
				sum(case when d.trans_type=6 then (d.quantity*c.avg_rate_per_unit) else 0 end) as out_amount
				from product_details_master c,order_wise_pro_details d
				where d.prod_id=c.id and d.trans_type in(5,6) and d.entry_form=78 and d.status_active=1 and d.is_deleted=0 $order_cond2 group by d.po_breakdown_id, c.item_group_id");
				foreach($transfer_qty_data as $row)
				{
					$transfe_amount=0;
					$transfe_amount=$row[csf('in_amount')]-$row[csf('out_amount')];
					$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['transfe_qty'][$row[csf('item_group_id')]]+=$row[csf('quantity')];
					$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['transfe_in'][$row[csf('item_group_id')]]+=$row[csf('in_qty')];
					$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['transfe_out'][$row[csf('item_group_id')]]+=$row[csf('out_qty')];

					$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['transfe_amount'][$row[csf('item_group_id')]]+=$transfe_amount;
					$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['transfe_in_amt'][$row[csf('item_group_id')]]+=$row[csf('in_amount')];
					$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['transfe_out_amt'][$row[csf('item_group_id')]]+=$row[csf('out_amount')];
				}
				unset($transfer_qty_data);

				$issue_qty_data=sql_select("select b.po_breakdown_id, p.item_group_id,sum(b.quantity) as quantity, sum(b.quantity*b.order_rate) as issue_amount
			from inv_issue_master d, product_details_master p, inv_trims_issue_dtls a, order_wise_pro_details b
			where a.mst_id=d.id and a.prod_id=p.id and a.trans_id=b.trans_id and b.trans_type=2 and d.entry_form=25 and b.entry_form=25 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_cond1 $trm_group_iss_cond group by b.po_breakdown_id, p.item_group_id");
				foreach($issue_qty_data as $row)
				{
					$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['issue_qty'][$row[csf('item_group_id')]]+=$row[csf('quantity')];
					$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['issue_amount'][$row[csf('item_group_id')]]+=$row[csf('amount')];
				}

				unset($issue_qty_data);


				$issue_rtn_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id, d.quantity as quantity, (d.quantity*c.avg_rate_per_unit) as amount
				from product_details_master c, order_wise_pro_details d
				where d.prod_id=c.id and d.trans_type=4 and d.entry_form=73 and d.status_active=1 and d.is_deleted=0 $order_cond2");
				foreach($issue_rtn_qty_data as $row)
				{
					$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['issue_rtn_qty'][$row[csf('item_group_id')]]+=$row[csf('quantity')];
					$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['issue_rtn_amt'][$row[csf('item_group_id')]]+=$row[csf('amount')];
				}

				unset($issue_rtn_qty_data);
				$sql_wo_pi=sql_select("select a.pi_number, b.work_order_no from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=4 and a.status_active=1 and a.importer_id=$company_name and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pi_number, b.work_order_no");
				$pi_arr=array();
				foreach($sql_wo_pi as $rowPi)
				{
					$pi_arr[$rowPi[csf('work_order_no')]].=$rowPi[csf('pi_number')].'**';
				}
				unset($sql_wo_pi);

				/*$sql_rec_rtn_data=sql_select("select d.po_breakdown_id, c.item_group_id,sum(d.quantity) as quantity   from  inv_issue_master a,inv_transaction b, product_details_master c, order_wise_pro_details d,inv_receive_master e  where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and e.id=a.received_id   and b.transaction_type=3 and a.entry_form=49 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $order_cond2  group by d.po_breakdown_id, c.item_group_id order by c.item_group_id");
				foreach($sql_rec_rtn_data as $row)
				{
					$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['receive_rtn_qty'][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				}
				unset($sql_rec_rtn_data);

				$sql_issue_data=sql_select("select b.po_breakdown_id, a.item_group_id,sum(b.quantity) as quantity  from  inv_issue_master d,product_details_master p,inv_trims_issue_dtls a , order_wise_pro_details b where a.mst_id=d.id and a.prod_id=p.id and a.trans_id=b.trans_id and b.trans_type=2 and b.entry_form=25 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $order_cond1  group by b.po_breakdown_id, a.item_group_id");
				foreach($sql_issue_data as $row)
				{
					$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][issue_qty][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				}
				unset($sql_issue_data);*/

				$total_pre_costing_value=0;	$total_wo_value=0;$total_left_over_balanc=0;$total_issue_amount=0;$total_rec_bal_qnty=0;
				$summary_array=array();
				$i=1; $x=0;
				foreach($style_data_arr as $key=>$value)
				{
					$z=1;
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					foreach($value['trim_group'] as $key_trim=>$value_trim)
					{
						$y=1;
						$summary_array[trim_group][$key_trim]=$key_trim;
						foreach($value[$key_trim] as $key_trim1=>$value_trim1)
						{
							if($z==1) $style_color=''; else $style_color=$bgcolor."; border: none";
							$z++;

							if($y==1) $style_colory=''; else $style_colory=$bgcolor."; border: none";
							$x++; $y++;

							$job=$key; $buyer_name=''; $job_no_prefix_num=''; $style_ref_no=''; $order_uom='';
							$job_data=explode('##',$style_data[$job]['job_data']);
							$buyer_name=$job_data[0];
							$job_no_prefix_num=$job_data[1];
							$style_ref_no=$job_data[2];
							$order_uom=$job_data[3];

							$style_po_data=explode('***',$style_data_all[$job]);

							$file_no_all=""; $grouping_all=""; $po_no_all=""; $ship_date_all=""; $ship_status_all=""; $po_id_all='';
							foreach($style_po_data as $po_data)
							{
								$ex_po_data=explode('__',$po_data);

								if($file_no_all=="") $file_no_all=$ex_po_data[0]; else $file_no_all.=','.$ex_po_data[0];
								if($grouping_all=="") $grouping_all=$ex_po_data[1]; else $grouping_all.=','.$ex_po_data[1];
								if($po_no_all=="") $po_no_all=$ex_po_data[2]; else $po_no_all.=','.$ex_po_data[2];
								if($ship_date_all=="") $ship_date_all=change_date_format($ex_po_data[3]); else $ship_date_all.=','.change_date_format($ex_po_data[3]);
								if($ship_status_all=="") $ship_status_all=$ex_po_data[4]; else $ship_status_all.=','.$ex_po_data[4];
								if($po_id_all=="") $po_id_all=$ex_po_data[5]; else $po_id_all.=','.$ex_po_data[5];
							}

							$file_no=implode(',',array_filter(array_unique(explode(',',$file_no_all))));
							$grouping=implode(',',array_filter(array_unique(explode(',',$grouping_all))));
							$po_no=implode(',',array_filter(array_unique(explode(',',$po_no_all))));
							$ship_date=implode(',',array_filter(array_unique(explode(',',$ship_date_all))));
							$ship_status=implode(',',array_filter(array_unique(explode(',',$ship_status_all))));
							$poId_all=implode(',',array_filter(array_unique(explode(',',$po_id_all))));

							$po_qty=0; $po_qty_set=0;
							$po_qty=$po_arr[$job]['order_quantity'];
							$po_qty_set=$po_arr[$job]['order_quantity_set'];

							?>
							<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $x; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $x; ?>">
								<td width="30" style="word-break: break-all;color:<? echo $style_color; ?>"title="<? echo $po_qty; ?>" ><? echo $i; ?></td>
								<td width="50" style="word-break: break-all;color:<? echo $style_color; ?>"><? echo $buyer_short_name_library[$buyer_name]; ?></td>
								<td width="100" style="word-break: break-all;color:<? echo $style_color; ?>"align="center" ><? echo $job_no_prefix_num; ?></td>
								<td width="100" style="word-break: break-all;color:<? echo $style_color; ?>"><? echo $style_ref_no; ?></td>
								<td width="100" style="word-break: break-all;color:<? echo $style_color; ?>"><? echo $grouping; ?></td>
								<td width="100" style="word-break: break-all;color:<? echo $style_color; ?>"><? echo $file_no; ?></td>
								<td width="90" style="word-break: break-all;color:<? echo $style_color; ?>">
									<a style="word-break: break-all;color: <? echo $style_color; ?>" href='#report_details' onclick="generate_report('<? echo $company_name; ?>','<? echo $job; ?>','<? echo $buyer_name; ?>','<? echo $style_ref_no; ?>','<? echo change_date_format($costing_arr[$job]['costing_date']); ?>','<? echo $poId_all; ?>','<? echo $costing_arr[$job]['costing_per']; ?>','preCostRpt2');"><? $po_number=$po_no; $po_id=$poId_all; echo $po_number; ?></a></td>
								<td width="80" style="word-break: break-all;color: <? echo $style_color; ?>"align="right">
									<a style="word-break: break-all;color:<? echo $style_color; ?>" href='#report_details' onclick="order_qty_popup('<? echo $company_name; ?>','<? echo $job; ?>','<? echo $poId_all; ?>','<? echo $buyer_name; ?>',<? echo $txt_date_from; ?>,<? echo $txt_date_to; ?>,'order_qty_data');"><? echo number_format($po_qty_set,0,'.',''); ?></a></td>
								<td width="50" align="center" style="word-break: break-all;color:<? echo $style_color; ?>"><? echo $unit_of_measurement[$order_uom]; ?></td>
								<td width="80" align="right" style="word-break: break-all;color:<? echo $style_color; ?>"><? echo number_format($po_qty,0,'.',''); ?></td>
								<td width="80" style="word-break: break-all;color:<? echo $style_color; ?>"><? $pub_shipment_date=$ship_date; echo $pub_shipment_date; ?></td>
								<td width="100" title="<? echo $value['trim_group_from'][$key_trim1]; ?>"><p><? echo $item_library[$value['trim_group_dtls'][$key_trim1]]; ?></p></td>
                                <td width="140"><p><? echo $value['description'][$key_trim1]; ?></p></td>
								<td width="100"><p><? echo $value['brand_sup_ref'][$key_trim1]; ?></p></td>
								<td width="60" align="center"><p><? if($value['apvl_req'][$key_trim1]==1) echo "Yes"; else echo "&nbsp;"; ?></p></td>
								<td width="80" align="center"><p><?
									if($value['apvl_req'][$key_trim1]==1)
									{
										$app_status=$app_status_arr[$job][$value['trim_group_dtls'][$key_trim1]];
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
									$country_id=implode(',',array_filter(array_unique(explode(',',$value['country_id'][$key_trim1]))));
									?></p></td>
								<td width="100" align="center"><p><? $insert_date=explode(" ",$value['insert_date'][$key_trim1]); echo change_date_format($insert_date[0],'','',''); ?></p></td>
                                <td width="80" align="right"><?=number_format($insert_date[0],4); ?></td>
								<td width="100" align="right"><p>
									<a href='#report_details' onclick="order_req_qty_popup('<? echo $company_name; ?>','<? echo $job; ?>','<? echo $po_id; ?>', '<? echo $buyer_name; ?>','<? echo $value['rate'][$key_trim1]; ?>','<? echo $value['trim_group_dtls'][$key_trim1];?>','<? echo $value['booking_no'][$key_trim1];?>','<? echo $value['description'][$key_trim1] ;?>','<? echo rtrim($country_id,",");?>','<? echo $value['trim_dtla_id'][$key_trim1];?>','<? echo $start_date ?>','<? echo $end_date ?>','order_req_qty_data');">
									<? $req_qty=number_format($value['req_qnty'][$key_trim1],2,'.',''); echo $req_qty; $summary_array[req_qnty][$key_trim]+=$value['req_qnty'][$key_trim1]; ?></a></p></td>
								<td width="100" align="right"><p><? echo number_format($value['req_value'][$key_trim1],2); $total_pre_costing_value+=$value['req_value'][$key_trim1]; ?></p></td>
									<?
									$wo_qnty=number_format($value['wo_qnty'][$key_trim1],2,'.','');

									/*if($wo_qnty > $req_qty) $color_wo="red";
									else if($wo_qnty < $req_qty ) $color_wo="yellow";
									else $color_wo="";*/

									if(($value['conversion_factor_rate'][$key_trim1]*$wo_qnty)==$req_qty) $color_wo="green";
									else if(($value['conversion_factor_rate'][$key_trim1]*$wo_qnty) > $req_qty) $color_wo="red";
									else if(($value['conversion_factor_rate'][$key_trim1]*$wo_qnty) < $req_qty ) $color_wo="yellow";
									else $color_wo="";

									$supplier_name_string="";
									$supplier_id_arr=array_unique(explode(',',rtrim($value['supplier_id'][$key_trim1],",")));

									foreach($supplier_id_arr as $supplier_id_arr_key=>$supplier_id_arr_value)
									{
										$ex_sup_data=explode("**",$supplier_id_arr_value);
										if($ex_sup_data[1]==3 || $ex_sup_data[1]==5) $suplier_name_arr=$company_library; else $suplier_name_arr=$lib_supplier_arr;
										$supplier_name_string.=$suplier_name_arr[$ex_sup_data[0]].",";
									}
									$booking_no_arr=array_unique(explode(',',rtrim($value['booking_no'][$key_trim1],",")));

									$main_booking_no_large_data=""; $piWoNo='';
									foreach($booking_no_arr as $booking_no1)
									{
										if($main_booking_no_large_data=="") $main_booking_no_large_data=$booking_no1; else $main_booking_no_large_data.=",".$booking_no1;
										if($booking_no1!="")
										{
											if($piWoNo=="") $piWoNo=implode(',',array_filter(array_unique(explode("**",$pi_arr[$booking_no1])))); else $piWoNo.=",".implode(',',array_filter(array_unique(explode("**",$pi_arr[$booking_no1]))));//
										}
									}

									?>
								<td width="90" align="right" title="<? echo 'conversion_factor='.$value['conversion_factor_rate'][$key_trim1];?>" bgcolor="<? echo $color_wo;?>"><p><a href='#report_details' onclick="openmypage('<? echo $po_id; ?>','<? echo $value['trim_group_dtls'][$key_trim1]; ?>','<? echo $job; ?>','<? echo $main_booking_no_large_data;?>','<? echo $value['trim_dtla_id'][$key_trim1];?>','booking_info');">
									<? echo number_format($value['wo_qnty'][$key_trim1],2,'.',''); $summary_array[wo_qnty][$key_trim]+=$value['wo_qnty'][$key_trim1]; ?></a></p></td>
								<td width="60" align="center"><p><? echo $unit_of_measurement[$item_arr[$value['trim_group_dtls'][$key_trim1]]['order_uom']]; $summary_array[cons_uom][$key_trim]= $item_arr[$value['trim_group_dtls'][$key_trim1]]['order_uom']; ?></p></td>
								<td width="100" align="right" title="<? echo number_format($value['rate'][$key_trim1],2,'.',''); ?>"><p><? echo number_format($value['amount'][$key_trim1],2,'.',''); $total_wo_value+=$value['amount'][$key_trim1]; ?></p></td>

								<td width="150"><p><? echo rtrim($supplier_name_string,','); ?></p></td>
								<td width="70" align="right" title="<? echo change_date_format($value['wo_date'][$key_trim1]);?>"><p>
									<? $tot=change_date_format($insert_date[0]);
									if($value['wo_qnty'][$key_trim1]<=0 )
									{
										$daysOnHand = datediff('d',$tot,$today);
									}
									else
									{
										$wo_date=$value['wo_date'][$key_trim1];
										$wo_date=change_date_format($wo_date);
										$daysOnHand = datediff('d',$tot,$wo_date);;
									}
									echo $daysOnHand; ?></p></td>
									<?
									$transfe_out=number_format($value['transfe_out'][$key_trim],2,'.','');
									$transfe_in=number_format($value['transfe_in'][$key_trim],2,'.','');
									$transfe_in_out=$transfe_in.' & '.$transfe_out;

									$transfe_out_amt=number_format($value['transfe_out'][$key_trim],2,'.','');
									$transfe_in_amt=number_format($value['transfe_in'][$key_trim],2,'.','');
									$transfe_in_out_amt=$transfe_in_amt.' & '.$transfe_out_amt;

									$inhouse_qnty=($value['inhouse_qnty'][$key_trim]+$value['transfe_qty'][$key_trim])-$value['receive_rtn_qty'][$key_trim];
									$inhouse_amount=($value['inhouse_amount'][$key_trim]+$value['transfe_amount'][$key_trim])-$value['receive_rtn_amt'][$key_trim];
									$total_inhouse_value+=$inhouse_amount;
									$balance=$value['wo_qnty_trim_group'][$key_trim]-$inhouse_qnty;
									$issue_qnty=$value['issue_qty'][$key_trim]-$value['issue_rtn_qty'][$key_trim];
									$issue_amount=$value['issue_amount'][$key_trim]-$value['issue_rtn_amt'][$key_trim];
									$left_overqty=$inhouse_qnty-$issue_qnty;
									$left_overamt=$inhouse_amount-$issue_amount;
									$summary_array['inhouse_qnty'][$key_trim]+=$inhouse_qnty;
									$summary_array['inhouse_qnty_bl'][$key_trim]+=$balance;
									$summary_array['issue_qty'][$key_trim]+=$issue_qnty;
									$summary_array['left_overqty'][$key_trim]+=$left_overqty;
									?>
                                <td width="90" tyle="word-break: break-all;color: <? echo $style_colory ?>"><p><? echo $piWoNo; ?> </p></td>
								<td width="90" style="word-break: break-all;color: <? echo $style_colory ?>" align="right" title="<? echo "Inhouse-Qty: ".number_format($value['inhouse_qnty'][$key_trim]-$value['receive_rtn_qty'][$key_trim],2,'.','')."\n Transfer In & Out Qty: ".$$transfe_in_out."\n Return Qty: ".number_format($value['receive_rtn_qty'][$key_trim],2,'.',''); ?>"><a  style="word-break: break-all;color: <? echo $style_colory ?>" href='#report_details' onclick="openmypage_inhouse('<? echo $po_id; ?>','<? echo $value['trim_group_dtls'][$key_trim1]; ?>','booking_inhouse_info');"><? echo number_format($inhouse_qnty,2,'.',''); ?></a></td>
                                <td width="90" style="word-break: break-all;color: <? echo $style_colory ?>" align="right" title="<? echo "Inhouse-Amt: ".number_format($value['inhouse_amount'][$key_trim]-$value['receive_rtn_amt'][$key_trim],2,'.','')."\n Transfer In & Out Amt: ".$$transfe_in_out_amt."\n Return Amt: ".number_format($value['receive_rtn_amt'][$key_trim],2,'.',''); ?>"><? echo number_format($inhouse_amount,2,'.',''); ?></td>
								<td width="90" style="word-break: break-all;color: <? echo $style_colory ?>" align="right" ><? echo number_format($balance,2,'.',''); $total_rec_bal_qnty+=$balance; ?></td>
								<td width="90" title="<? echo "Issue-Qty: ".number_format($value['issue_qty'][$key_trim],2,'.','')."\n Issue Return Qty: ".number_format($value['issue_rtn_qty'][$key_trim],2,'.',''); ?>" style="word-break: break-all;color: <? echo $style_colory ?>" align="right" ><a  style="word-break: break-all;color: <? echo $style_colory ?>" href='#report_details' onclick="openmypage_issue('<? echo $po_id; ?>','<? echo $value['trim_group_dtls'][$key_trim1]; ?>','booking_issue_info');"><? echo number_format($issue_qnty,2,'.',''); ?></a></td>
                                <td width="90" title="<? echo "Issue-Amt: ".number_format($value['issue_amount'][$key_trim],2,'.','')."\n Issue Return Amt: ".number_format($value['issue_rtn_amt'][$key_trim],2,'.',''); ?>" style="word-break: break-all;color: <? echo $style_colory ?>" align="right" ><? echo number_format($issue_amount,2,'.',''); $total_issue_amount+=$issue_amount ?></a></td>
								<td width="90" align="right" style="word-break: break-all;color: <? echo $style_colory ?>"><? echo number_format($left_overqty,2,'.',''); ?></td>
                                <td align="right" style="word-break: break-all;color: <? echo $style_colory ?>"><? echo number_format($left_overamt,2,'.',''); $total_left_over_balanc+=$left_overamt; ?></td>
							</tr>
						<?
						}// end  foreach($value[$key_trim] as $key_trim1=>$value_trim1)
					}
					$i++;
				}
			?>
			</table>
            </div>
			<table class="rpt_table" width="2940" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tfoot>
					<th width="30">&nbsp;</th>
					<th width="50">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="90">&nbsp;</th>
					<th width="80" align="right" id="total_order_qnty"><? //echo number_format($total_order_qnty,0); ?></th>
					<th width="50">&nbsp;</th>
					<th width="80" align="right" id="total_order_qnty_in_pcs"><? //echo number_format($total_order_qnty_in_pcs,0); ?></th>
					<th width="80">&nbsp;</th>
					<th width="100">&nbsp;</th>
                    <th width="140">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="60">&nbsp;</th>
					<th width="80">&nbsp;</th>
					<th width="100">&nbsp;</th>
                    <th width="80">&nbsp;</th>
					<th width="100" align="right" id="value_req_qnty"><? //echo number_format($total_req_qnty,2); ?></th>
					<th width="100" align="right" id="value_pre_costing"><? echo number_format($total_pre_costing_value,2); ?></th>
					<th width="90" align="right" id=""><? //echo number_format($total_wo_qnty,2); ?></th>
					<th width="60" align="right" ></th>
					<th width="100" align="right" id="value_wo_qty"><? echo number_format($total_wo_value,2); ?></th>
					<th width="150" align="right" id=""></th>
					<th width="70" align="right"><p><? //echo number_format($req_value,2,'.',''); ?>&nbsp;</p></th>
                    <th width="90">&nbsp;</th>
					<th width="90" align="right" id="value_in_qty"><? //echo number_format($total_in_qnty,2); ?></th>
                    <th width="90" align="right" id="value_in_amount"><? echo number_format($total_inhouse_value,2); ?></th>
					<th width="90" align="right" id="value_rec_qty"><? echo number_format($total_rec_bal_qnty,2); ?></th>
					<th width="90" align="right" id="value_issue_qty"><? //echo number_format($total_issue_qnty,2); ?></th>
                    <th width="90" align="right" id="value_issue_amount"><? echo number_format($total_issue_amount,2); ?></th>
					<th width="90" align="right" id="value_leftover_qty"><? //echo number_format($total_leftover_qnty,2); ?></th>
                    <th align="right" id="value_leftover_amount"><? echo number_format($total_left_over_balanc,2); ?></th>
				</tfoot>
			</table>

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
					?>
					<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr2_<? echo $z; ?>','<? echo $bgcolor;?>')" id="tr2_<? echo $z; ?>">
						<td width="30"><? echo $z; ?></td>
						<td width="110"><p><? echo $item_library[$key_trim]; ?></p></td>
						<td width="60" align="center"><? echo $unit_of_measurement[$summary_array[cons_uom][$key_trim]]; ?></td>
						<td width="80" align="right"><? $app_perc=($summary_array[item_app][$key_trim][app]*100)/$summary_array[item_app][$key_trim][all];  if ($app_perc>=0)echo number_format($app_perc,2); ?></td>
						<td width="110" align="right"><? echo number_format($summary_array[req_qnty][$key_trim],2); ?></td>
						<td width="110" align="right"><? echo number_format( $summary_array[wo_qnty][$key_trim],2); ?></td>
						<td width="80" align="right"><? $wo_per= $summary_array[wo_qnty][$key_trim]/$summary_array[req_qnty][$key_trim]*100; echo number_format($wo_per,2).'%'; ?></td>
						<td width="110" align="right"><? echo number_format($summary_array[inhouse_qnty][$key_trim],2); ?></td>
						<td width="80" align="right"><? $in_per=$summary_array[inhouse_qnty][$key_trim]/$summary_array[wo_qnty][$key_trim]*100; echo number_format($in_per,2).'%'; ?></td>
						<td width="110" align="right"><? echo number_format($summary_array[inhouse_qnty_bl][$key_trim],2); $in_house_bal+=($value['wo']-$value['in']); ?></td>
						<td width="110" align="right"><? echo number_format($summary_array[issue_qty][$key_trim],2); ?></td>
						<td width="80" align="right"><? $wo_per=$summary_array[issue_qty][$key_trim]/$summary_array[wo_qnty][$key_trim]*100; echo number_format($wo_per,2).'%'; ?></td>
						<td align="right"><? echo number_format($summary_array[left_overqty][$key_trim],2); ?></td>
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
	echo "$total_data****$filename****$tot_rows****2";
	exit();
}

if($action=="report_generate3")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_library=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
	$country_name_library=return_library_array( "select id, country_name from lib_country", "id", "country_name"  );
	$lib_supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
	$lib_color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	$lib_season_arr=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name"  );
	$yarn_count_arr=return_library_array("select id,yarn_count from  lib_yarn_count","id","yarn_count");

	$company_name=str_replace("'","",$cbo_company_name);
	$serch_by=str_replace("'","",$cbo_search_by);
	$txt_style_id=str_replace("'","",$txt_style_id);
	$order_no_id=str_replace("'","",$txt_order_no_id);
	$cbo_season_id=str_replace("'","",$cbo_season_id);

	$buyer_id_cond="";
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}

	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_job_no=trim($txt_job_no);
	$txt_style_id=trim($txt_style_id);
	if($txt_style_id!="")
	{
		if($txt_style_id!="" || $txt_style_id!=0) $jobcond="and a.id in($txt_style_id) "; else $jobcond="";
	}
	else
	{
	if($txt_job_no!="" || $txt_job_no!=0) $jobcond="and a.job_no_prefix_num in('".$txt_job_no."')"; else $jobcond="";
	if(str_replace("'","",$txt_style_ref)!="") $style_ref_cond=" and a.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'"; else $style_ref_cond="";
	}
	if($order_no_id!="")
	{
	if(str_replace("'","",$order_no_id)!="") $ordercond=" and b.id in($order_no_id)"; else $ordercond="";
	}
	else
	{
		if(str_replace("'","",$txt_order_no)!="") $ordercond=" and b.po_number like '%".str_replace("'","",$txt_order_no)."%'"; else $ordercond="";
	}

	$season_con='';

	if(!empty($cbo_season_id))
	{
		$season_con=" and a.season_buyer_wise='$cbo_season_id' ";
	}


	if(str_replace("'","",$cbo_item_group)=="")
	{
		$item_group_cond="";
		$item_group_cond2="";
		$item_group_cond3="";
	}
	else
	{
		$item_group_cond="and e.trim_group in(".str_replace("'","",$cbo_item_group).")";
		$item_group_cond2="and b.trim_group in(".str_replace("'","",$cbo_item_group).")";
		$item_group_cond3="and a.item_group_id in(".str_replace("'","",$cbo_item_group).")";
	}
	$date_type=str_replace("'","",$cbo_date_type);

	$date_cond='';
	if($date_type==2)
	{
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			$start_date=(str_replace("'","",$txt_date_from));
			$end_date=(str_replace("'","",$txt_date_to));
			$date_cond="and c.country_ship_date between '$start_date' and '$end_date'";
		}
	}
	else
	{
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			$start_date=(str_replace("'","",$txt_date_from));
			$end_date=(str_replace("'","",$txt_date_to));
			$date_cond="and b.pub_shipment_date between '$start_date' and '$end_date'";
		}
	}

	$file_no=str_replace("'","",$txt_file_no);
	$internal_ref=str_replace("'","",$txt_internal_ref);
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' ";
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' ";
	$cbo_year=str_replace("'","",$cbo_year);
	if($db_type==0)
	{
		if(trim($cbo_year)!=0) $year_cond=" and YEAR(a.insert_date)=$cbo_year"; else $year_cond="";
	}
	else if($db_type==2)
	{
		if(trim($cbo_year)!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year"; else $year_cond="";
	}
	if(str_replace("'","",$cbo_ship_status)>0){ $ship_status_cond=" and b.shiping_status=$cbo_ship_status";}else{
		$ship_status_cond="";
	};	
	

	//Precost v2 print button.........................................................
	$pre_cost2_print_button_arr=return_library_array( "select template_name,format_id from lib_report_template where module_id = 2 and report_id = 43 and is_deleted = 0 and status_active=1", "template_name", "format_id"  );
	list($first_print_button)=explode(',',$pre_cost2_print_button_arr[$company_name]);
	
	$print_button_action_arr=array(50=>'preCostRpt',51=>'preCostRpt2',52=>'bomRpt',63=>'bomRpt2',156=>'accessories_details',157=>'accessories_details2',158=>'preCostRptWoven',159=>'bomRptWoven',170=>'preCostRpt3',171=>'preCostRpt4',142=>'preCostRptBpkW',192=>'checkListRpt');
	$print_button_action = $print_button_action_arr[$first_print_button];
	?>
	<div style="width:3060px">
	<?
	
	$local_foreign_arr = array (
		array(1=>"302",2=>"311"),
		array(1=>"301",2=>"310"),
		array(1=>"300",2=>"309"),
		array(1=>"279",2=>"308"),
		array(1=>"278",2=>"307"),
		array(1=>"277",2=>"306"),
		array(1=>"276",2=>"305")
	
	  );

	// echo "<pre>";
	// print_r($local_foreign_arr);

	$sql_po="SELECT a.id as JOB_ID,a.buyer_name, a.season_buyer_wise, a.job_no,a.job_quantity, a.job_no_prefix_num,a.order_uom, a.style_ref_no, a.total_set_qnty as ratio, a.gauge as GAUGE, b.id as po_id, b.po_number, b.po_quantity as po_qnty, b.pub_shipment_date,c.order_quantity, c.plan_cut_qnty as PLAN_CUT_QNTY, d.costing_per as COSTING_PER, e.id as FAB_DTLS_ID, e.body_part_id as BPART_ID, e.source_id,e.avg_cons, f.rate as RATE, f.count_id as COUNT_ID, f.copm_one_id as COPM_ONE_ID, f.percent_one as PERCENT_ONE,  g.measurement as MEASUREMENT, g.color_number_id as GMT_COLOR, g.stripe_color as YARN_COLOR
	from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, wo_pre_cost_mst d, wo_pre_cost_fabric_cost_dtls e,wo_pre_cost_fab_yarn_cost_dtls f,wo_pre_stripe_color g
	where a.id=b.job_id and c.po_break_down_id=b.id and a.id=d.job_id and a.id=e.job_id and e.job_id=b.job_id and e.job_id=c.job_id and f.fabric_cost_dtls_id=e.id and e.id=g.pre_cost_fabric_cost_dtls_id and f.color=g.stripe_color and g.color_number_id=c.color_number_id
	and a.company_name=$company_name and a.status_active=1 and b.status_active=1 and c.status_active=1 and e.status_active=1 and f.status_active=1 and g.status_active=1 $date_cond $buyer_id_cond $jobcond $ordercond $ship_status_cond $style_ref_cond order by a.job_no";
	 
 	//  echo  $sql_po;

	$sql_po_result=sql_select($sql_po);
	if(count($sql_po_result)<1)
	{
		echo '<div align="left" style="width:1000px;"><h1 align="center" style="color:#f00;">Data not found</h></div>'; die;
	}
	$result_data_arr=$result_job_wise=array();$all_po_id=""; $chk_po_arr=array();
	foreach($sql_po_result as $row)
	{
		$all_job_id[$row["JOB_ID"]]=$row["JOB_ID"];
		$all_po_arr[$row[csf("po_id")]]=$row[csf("po_id")];
		
		$job_id=$row["JOB_ID"];
		$bpart_id=$row["BPART_ID"];
		$count_id=$row["COUNT_ID"];
		$gmt_color=$row["GMT_COLOR"];
		$yarn_color=$row["YARN_COLOR"];
		$yarn_composition=$composition[$row["COPM_ONE_ID"]]." ".$row["PERCENT_ONE"]."%";;
		$yarn_wise_arr[$job_id][$bpart_id][$yarn_color][$count_id][$yarn_composition]["po_id"].=$row[csf("po_id")].',';
		$yarn_wise_arr[$job_id][$bpart_id][$yarn_color][$count_id][$yarn_composition]["buyer_name"]=$row[csf("buyer_name")];
		$yarn_wise_arr[$job_id][$bpart_id][$yarn_color][$count_id][$yarn_composition]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
		$yarn_wise_arr[$job_id][$bpart_id][$yarn_color][$count_id][$yarn_composition]["job_no"]=$row[csf("job_no")];
		$yarn_wise_arr[$job_id][$bpart_id][$yarn_color][$count_id][$yarn_composition]["construction"]=$row[csf("construction")];
		$yarn_wise_arr[$job_id][$bpart_id][$yarn_color][$count_id][$yarn_composition]["composition"]=$row[csf("composition")];
		$yarn_wise_arr[$job_id][$bpart_id][$yarn_color][$count_id][$yarn_composition]["gauge"]=$row["GAUGE"];
		$yarn_wise_arr[$job_id][$bpart_id][$yarn_color][$count_id][$yarn_composition]["season_buyer_wise"]=$row[csf("season_buyer_wise")];
		$yarn_wise_arr[$job_id][$bpart_id][$yarn_color][$count_id][$yarn_composition]["avg_cons"]=$row[csf("avg_cons")];
		$yarn_wise_arr[$job_id][$bpart_id][$yarn_color][$count_id][$yarn_composition]["order_uom"]=$row[csf("order_uom")];
		$yarn_wise_arr[$job_id][$bpart_id][$yarn_color][$count_id][$yarn_composition]["style_ref_no"]=$row[csf("style_ref_no")];
		$yarn_wise_arr[$job_id][$bpart_id][$yarn_color][$count_id][$yarn_composition]["measurement"]=$row["MEASUREMENT"];
		$yarn_wise_arr[$job_id][$bpart_id][$yarn_color][$count_id][$yarn_composition]["plan_cut_qnty"]+=$row["PLAN_CUT_QNTY"];
		$yarn_wise_arr[$job_id][$bpart_id][$yarn_color][$count_id][$yarn_composition]["costing_per"]=$row["COSTING_PER"];
		$yarn_wise_arr[$job_id][$bpart_id][$yarn_color][$count_id][$yarn_composition]["rate"]=$row["RATE"];
		$yarn_wise_arr[$job_id][$bpart_id][$yarn_color][$count_id][$yarn_composition]["gmt_color"].=$lib_color_arr[$row["GMT_COLOR"]].",";
		if($row["COSTING_PER"]==1){$order_price_per_dzn=12;}
		else if($row["COSTING_PER"]==2){$order_price_per_dzn=1;}
		else if($row["COSTING_PER"]==3){$order_price_per_dzn=24;}
		else if($row["COSTING_PER"]==4){$order_price_per_dzn=36;}
		else if($row["COSTING_PER"]==5){$order_price_per_dzn=48;}

		$yarn_wise_arr[$job_id][$bpart_id][$yarn_color][$count_id][$yarn_composition]["booking_req_qnty"]+=($row["MEASUREMENT"]/$order_price_per_dzn)*$row["PLAN_CUT_QNTY"];
		
		if($yarn_wise_arr[$job_id][$bpart_id][$yarn_color][$count_id][$yarn_composition]["po_number"]=="")
		{			
			$yarn_wise_arr[$job_id][$bpart_id][$yarn_color][$count_id][$yarn_composition]["po_number"]=$row[csf("po_number")];
		}
		else{ $yarn_wise_arr[$job_id][$bpart_id][$yarn_color][$count_id][$yarn_composition]["po_number"].='**'.$row[csf("po_number")];}
		
		if($chk_po_arr[$row[csf("po_id")]]=="")
		{
			$yarn_wise_arr[$job_id][$bpart_id][$yarn_color][$count_id][$yarn_composition]["po_qnty_pcs"]+=$row[csf("po_qnty")]*$row[csf("ratio")];
			$yarn_wise_arr[$job_id][$bpart_id][$yarn_color][$count_id][$yarn_composition]["po_qnty"]+=$row[csf("po_qnty")];
			$chk_po_arr[$row[csf("po_id")]]=1000;
		}
		
		$yarn_wise_arr[$job_id][$bpart_id][$yarn_color][$count_id][$yarn_composition]["pub_shipment_date"].=$row[csf("pub_shipment_date")].',';
		$job_qty_wise_arr[$job_id]["po_qnty"]=$row[csf("job_quantity")];
		$job_qty_wise_arr[$job_id]["po_qnty_pcs"]=$row[csf("job_quantity")]*$row[csf("ratio")];

		$po_no_arr[]=$row[csf('po_id')];
		$job_no_arr[$row["JOB_NO"]]=$row["JOB_NO"];
	}
	ksort($yarn_wise_arr);
	unset($sql_po_result);
	unset($sql_fab_result);
	$booking_req_arr=array();
	$all_job_id_in=where_con_using_array($all_job_id,0,'b.job_id');
	$sql_wo="SELECT a.id as ID, b.id as DTLS_ID, b.job_id as JOB_ID, b.yarn_count as YARN_COUNT, b.yarn_comp_type1st as YARN_COMP_TYPE1ST, b.yarn_comp_percent1st as PERCENT, b.color_name as COLOR_NAME, b.supplier_order_quantity as QUANTITY, b.amount as AMOUNT 
	from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.entry_form=234 and a.status_active=1 and b.status_active=1 $all_job_id_in";
	// echo $sql_wo; 
	$sql_wo_res=sql_select($sql_wo);
	$usd_id=2;
	foreach ($sql_wo_res as $row)
	{
		$all_booking_dtls_id[$row["DTLS_ID"]]=$row["DTLS_ID"];
		$yarn_composition=$composition[$row["YARN_COMP_TYPE1ST"]]." ".$row["PERCENT"]."%";
		$booking_req_arr[$row["JOB_ID"]][$row["COLOR_NAME"]][$row["YARN_COUNT"]][$yarn_composition]['qnty']+=$row["QUANTITY"];
		$booking_req_arr[$row["JOB_ID"]][$row["COLOR_NAME"]][$row["YARN_COUNT"]][$yarn_composition]['amount']+=$row["AMOUNT"];
		$booking_req_arr[$row["JOB_ID"]][$row["COLOR_NAME"]][$row["YARN_COUNT"]][$yarn_composition]['dtls_id'].=$row["DTLS_ID"].",";
	}
	unset($sql_wo_res);

	$book_cond_for_in=where_con_using_array($all_booking_dtls_id,0,'b.work_order_dtls_id');
	$sql="SELECT a.id as PI_ID,a.pi_number as PI_NUMBER,b.work_order_dtls_id as WORK_ORDER_DTLS_ID
	from com_pi_master_details  a,  com_pi_item_details b
	where a.id=b.pi_id and a.importer_id=$cbo_company_name and a.item_category_id=1 and a.status_active=1 and b.status_active=1
	$book_cond_for_in";
	// echo $sql;
	$sql_result = sql_select($sql);
	foreach($sql_result as $row)
	{
		$piDataArr[$row["WORK_ORDER_DTLS_ID"]]["PI_NUMBER"].=$row["PI_NUMBER"].',';
		$piDataArr[$row["WORK_ORDER_DTLS_ID"]]["PI_ID"].=$row["PI_ID"].',';
		$pi_id_all[$row["PI_ID"]]=$row["PI_ID"];
	}
	unset($sql_result);	
	//print_r($booking_req_arr);

	$all_job_no_in=where_con_using_array($job_no_arr,1,'a.job_no');
	$trns_sql="SELECT a.job_no as JOB_NO, 
	sum(case when a.transaction_type=1 then a.cons_quantity else 0 end) as RCV_QNTY,
	sum(case when a.transaction_type=1 then a.cons_amount else 0 end) as RCV_AMOUNT,
	sum(case when a.transaction_type=2 then a.cons_quantity else 0 end) as ISS_QNTY,
	sum(case when a.transaction_type=2 then a.cons_amount else 0 end) as ISS_AMOUNT,
	sum(case when a.transaction_type=3 then a.cons_quantity else 0 end) as RCV_RTN_QNTY,
	sum(case when a.transaction_type=3 then a.cons_amount else 0 end) as RCV_RTN_AMOUNT,
	sum(case when a.transaction_type=4 then a.cons_quantity else 0 end) as ISS_RTN_QNTY,
	sum(case when a.transaction_type=4 then a.cons_amount else 0 end) as ISS_RTN_AMOUNT,
	b.yarn_count_id as COUNT_ID, b.yarn_comp_type1st as YARN_COMP, b.yarn_comp_percent1st as PERCENT, b.color as COLOR
	from inv_transaction a, product_details_master b 
	where a.prod_id=b.id and a.item_category=1 and a.status_active=1 and b.status_active=1 $all_job_no_in 
	group by a.job_no, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.color";
	// echo $trns_sql;
	
	$trns_result = sql_select($trns_sql);
	foreach($trns_result as $row)
	{
		$yarn_composition=$composition[$row["YARN_COMP"]]." ".$row["PERCENT"]."%";
		$yarn_prod_arr[$row["JOB_NO"]][$row["COLOR"]][$row["COUNT_ID"]][$yarn_composition]["rcv_qnty"]+=$row["RCV_QNTY"];
		$yarn_prod_arr[$row["JOB_NO"]][$row["COLOR"]][$row["COUNT_ID"]][$yarn_composition]["rcv_amt"]+=$row["RCV_AMOUNT"];
		$yarn_prod_arr[$row["JOB_NO"]][$row["COLOR"]][$row["COUNT_ID"]][$yarn_composition]["iss_qnty"]+=$row["ISS_QNTY"];
		$yarn_prod_arr[$row["JOB_NO"]][$row["COLOR"]][$row["COUNT_ID"]][$yarn_composition]["iss_amt"]+=$row["ISS_AMOUNT"];
		$yarn_prod_arr[$row["JOB_NO"]][$row["COLOR"]][$row["COUNT_ID"]][$yarn_composition]["rcv_rtn_qnty"]+=$row["RCV_RTN_QNTY"];
		$yarn_prod_arr[$row["JOB_NO"]][$row["COLOR"]][$row["COUNT_ID"]][$yarn_composition]["rcv_rtn_amount"]+=$row["RCV_RTN_AMOUNT"];
		$yarn_prod_arr[$row["JOB_NO"]][$row["COLOR"]][$row["COUNT_ID"]][$yarn_composition]["iss_rtn_qnty"]+=$row["ISS_RTN_QNTY"];
		$yarn_prod_arr[$row["JOB_NO"]][$row["COLOR"]][$row["COUNT_ID"]][$yarn_composition]["iss_rtn_amount"]+=$row["ISS_RTN_AMOUNT"];
		$yarn_prod_arr[$row["JOB_NO"]][$row["COLOR"]][$row["COUNT_ID"]][$yarn_composition]["yarn_comp"]=$row["yarn_comp"];
		$yarn_prod_arr[$row["JOB_NO"]][$row["COLOR"]][$row["COUNT_ID"]][$yarn_composition]["percent"]=$row["PERCENT"];
	}
	unset($trns_result);
	
	$pi_id_in=where_con_using_array($pi_id_all,0,'b.pi_id');
	$btb_lc_data=sql_select("SELECT a.lc_number as LC_NUMBER, b.pi_id as PI_ID from com_btb_lc_master_details a, com_btb_lc_pi b where a.id=b.com_btb_lc_master_details_id and a.status_active=1 and b.status_active=1 $pi_id_in");

	foreach($btb_lc_data as $row){
		$pi_wise_btb_lc_arr[$row["PI_ID"]]=$row["LC_NUMBER"];
	}
	
	//............................end

	$tbl_width2=3300;
	$tbl_width=3370+count($trims_tna)*100*3;
	$tbl_width3=3350+count($trims_tna)*100*3;
	//=======================================================================================================================
	ob_start();
	?>
    <style>
		.wrd_brk{word-break: break-all;}
	</style>
    <div style="width:100%">
		<table width="<? echo $tbl_width2;?>">
			<tr>
				<td align="center" width="100%" colspan="20" class="form_caption"><? echo $report_title.'<br>'.$company_library[str_replace("'","",$company_name)].'<br/>';
				if(str_replace("'","",$txt_date_from)!="") echo  str_replace("'","",$txt_date_from).' To '.str_replace("'","",$txt_date_to);
					?></td>
			</tr>
			<tr>
				<td align="center" width="100%" colspan="20" class="form_caption">
					<table style="margin-left:2400px; margin-top:5px" id="table_notes">
					<tr>
						<td bgcolor="yellow" height="15" width="30"></td>
						<td> WO Qty Fully or Partial Pending with Req Qty</td>
							<td bgcolor="green" height="15" width="30">&nbsp;</td>
							<td>WO Qty equal with Req Qty </td>
							<td bgcolor="red" height="15" width="30"></td>
							<td>WO Qty greater than Req Qty</td>
						</tr>
						<!-- <tr>
							<td colspan="6" align="center">
								(All WO Qty will calculate with Conversion Factor)
							</td>
						</tr> -->
					</table>
				</td>
			</tr>
		</table>
		<?	####### For Yarn ########?>
		<fieldset style="width:100%;">
		<table width="<? echo $tbl_width2+20;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1">
		<caption><b style="float:left"> Yarn</b></caption>
			<thead>                   
				<tr style="font-size:13px">
					<th width="30">SL</th>
					<th width="110">Buyer</th>
					<th width="100">Season</th>
					<th width="100">Job No</th>
					<th width="100">Style Ref</th>
					<th width="70">Guage</th>
					<th width="100">Order No</th>
					<th width="70">Style Qty</th>
					<th width="40">UOM</th>                        
					<th width="70">Qty (Pcs)</th>                        
					<th width="70">Shipment Date</th>
					<th width="120">Body Part</th>
					
					<th width="100">Yarn Color</th>
					<th width="100">Gmt. Color</th>
					<th width="120">Yarn Count</th>
					<th width="150">Yarn Compostion</th>
					<th width="100">Avg. Cons</th>
					
					<th width="80">Req Qty</th>  
					<th width="50">Yarn UOM</th>
					<th width="80">Pre Costing Value</th>                        
					<th width="80">WO Qty</th>                        
					<th width="80">WO Value (USD)</th>
					
					<th width="100">PI No.</th>
					<th width="100">BTB LC No</th>
					<th width="80">In-House Qty</th>
					<th width="80">In-House Amount</th>  
					<th width="80">Receive Balance</th>
					<th width="80">Issue to Knitting</th>
					<th width="80">Issue Amount</th>
					<th width="80">Left Over / Balance</th>
					<th width="100">Left Over / Balance Amount</th>
				</tr>
			</thead>
		</table>
		<div style="width:<? echo $tbl_width2+40;?>px; overflow-y:scroll; max-height:300px;font-size:12px; overflow-x:hidden;" id="scroll_body">
			<table width="<? echo $tbl_width2+20;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body2">

				<?  				
				$job_wise_arr=array();$body_wise_arr=array(); $jobPoArr=array();
				foreach($yarn_wise_arr as $job_id=>$job_data)
				{
					$job_wise_row=0;
					foreach($job_data as $bprat_id=>$body_data)
					{
						$body_wise_row=0;
						foreach($body_data as $yarn_color=>$yarn_data)
						{
							foreach($yarn_data as $count_id=>$count_data)
							{
								foreach($count_data as $composition_data=>$val)
								{
									$job_wise_row++;$body_wise_row++;
									$exPoNos=array_filter(array_unique(explode("**",$val["po_number"])));
									foreach($exPoNos as $pono)
									{
										$jobPoArr[$job_id][$pono]=$pono;
									}
								}
							}
						}
						$body_wise_arr[$job_id][$bprat_id]=$body_wise_row;
					}
					$job_wise_arr[$job_id]=$job_wise_row;
				}
				//print_r($job_wise_arr);

				$i=1;
				foreach($yarn_wise_arr as $job_id=>$job_data)
				{
					$a=1;
					foreach($job_data as $bprat_id=>$body_data)
					{
						$b=1;
						foreach($body_data as $yarn_color=>$yarn_data)
						{
							foreach($yarn_data as $count_id=>$count_data)
							{
								foreach($count_data as $composition_data=>$val)
								{

									$pub_shipment_date=rtrim($val["pub_shipment_date"],',');
									$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
									$min_pub_shipment_date=min($pub_shipment_date);

									$booking_req_qnty=number_format($val["booking_req_qnty"]*2.20462,2,".","");
									$pre_cost_val=number_format(($val["booking_req_qnty"]*2.20462)*$val["rate"],2,".","");
									
									$booking_qnty=$booking_req_arr[$job_id][$yarn_color][$count_id][$composition_data]['qnty'];
									$booking_amount=$booking_req_arr[$job_id][$yarn_color][$count_id][$composition_data]['amount'];
									$booking_dtls_id=$booking_req_arr[$job_id][$yarn_color][$count_id][$composition_data]['dtls_id'];
									$booking_dtls_arr=array_unique(explode(",",rtrim($booking_dtls_id,",")));
									$all_pi_no="";
									foreach($booking_dtls_arr as $row)
									{
										$all_pi_no.=$piDataArr[$row]["PI_NUMBER"].",";
										$all_pi_id.=$piDataArr[$row]["PI_ID"].",";
									}
									$all_pi_id_arr=array_unique(explode(",",rtrim($all_pi_id,",")));
									$btb_lc_no="";
									foreach($all_pi_id_arr as $row)
									{
										$btb_lc_no.=$pi_wise_btb_lc_arr[$row].",";
									}
									$yarn_prod_rcv_qnty=$yarn_prod_arr[$val["job_no"]][$yarn_color][$count_id][$composition_data]["rcv_qnty"];
									$yarn_prod_rcv_amt=$yarn_prod_arr[$val["job_no"]][$yarn_color][$count_id][$composition_data]["rcv_amt"];
									$yarn_prod_rcv_rtn_qnty=$yarn_prod_arr[$val["job_no"]][$yarn_color][$count_id][$composition_data]["rcv_rtn_qnty"];
									$yarn_prod_rcv_rtn_amt=$yarn_prod_arr[$val["job_no"]][$yarn_color][$count_id][$composition_data]["rcv_rtn_amt"];
									$yarn_prod_iss_qnty=$yarn_prod_arr[$val["job_no"]][$yarn_color][$count_id][$composition_data]["iss_qnty"];
									$yarn_prod_iss_amt=$yarn_prod_arr[$val["job_no"]][$yarn_color][$count_id][$composition_data]["iss_amt"];
									$yarn_prod_iss_rtn_qnty=$yarn_prod_arr[$val["job_no"]][$yarn_color][$count_id][$composition_data]["iss_rtn_qnty"];
									$yarn_prod_iss_rtn_amt=$yarn_prod_arr[$val["job_no"]][$yarn_color][$count_id][$composition_data]["iss_rtn_amt"];
									$yarn_comp=$yarn_prod_arr[$val["job_no"]][$yarn_color][$count_id][$composition_data]["yarn_comp"];
									$yarn_percent=$yarn_prod_arr[$val["job_no"]][$yarn_color][$count_id][$composition_data]["percent"];

									if($booking_qnty==$booking_req_qnty){ $color_wo="green"; }
									else if($booking_qnty > $booking_req_qnty){ $color_wo="red"; }
									else if($booking_qnty < $booking_req_qnty ){ $color_wo="yellow"; }
									else $color_wo="";
										
									?>
									<tr bgcolor = "<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="font-size:13px">
						
										<?
										if($a==1)
										{
											$tot_po_qnty=$job_qty_wise_arr[$job_id]["po_qnty"];;
											$po_qnty_pcs=$job_qty_wise_arr[$job_id]["po_qnty_pcs"];
											$sensitive=$val["sensitive"]; 
											$total_po_qnty+=$tot_po_qnty;
											$total_po_qnty_pcs+=$po_qnty_pcs;
											$job_rowspan=$job_wise_arr[$job_id];
											?>
												<td width="30" valign="middle" rowspan="<?=$job_rowspan;?>"><? echo $i; ?></td>
												<td width="110" valign="middle" rowspan="<?=$job_rowspan;?>" class="wrd_brk"><? echo $buyer_short_name_library[$val["buyer_name"]]; ?></td>
												<td width="100" valign="middle" rowspan="<?=$job_rowspan;?>" class="wrd_brk"><? echo $lib_season_arr[$val["season_buyer_wise"]]; ?></td>
												<td width="100" valign="middle" align="center" rowspan="<?=$job_rowspan;?>"><? echo $val["job_no_prefix_num"]; ?></td>
												<td width="100" valign="middle" rowspan="<?=$job_rowspan;?>" align="center" class="wrd_brk"><? echo $val["style_ref_no"]; ?></td>
												<td width="70" valign="middle" rowspan="<?=$job_rowspan;?>" align="center" class="wrd_brk" ><?echo $gauge_arr[$val["gauge"]]; ?></td>
												<td align="center" valign="middle"  rowspan="<?=$job_rowspan;?>" width="100" class="wrd_brk"><? $po_nos=implode(", ",array_unique($jobPoArr[$job_id])); echo $po_nos; ?></td>
												<td align="right" valign="middle" rowspan="<?=$job_rowspan;?>" width="70"><? echo number_format($tot_po_qnty,0); ?></td>
												<td align="center" valign="middle"  rowspan="<?=$job_rowspan;?>" width="40" ><? echo $unit_of_measurement[$val["order_uom"]]; ?></td>
												<td align="right" valign="middle" rowspan="<?=$job_rowspan;?>" width="70"> <? echo number_format($po_qnty_pcs,0); ?></td>
												<td align="center" valign="middle" rowspan="<?=$job_rowspan;?>" width="70"><? echo change_date_format($min_pub_shipment_date); ?></td>
											<?
										}
										if($b==1)
										{
											?>
											<td align="center" valign="middle" rowspan="<?=$body_wise_arr[$job_id][$bprat_id]; ?>" width="120" class="wrd_brk"><? echo $body_part[$bprat_id]; ?></td>
											<?
										}
										?>
										<td align="center" width="100" class="wrd_brk"><? echo $lib_color_arr[$yarn_color]; ?></td>
										<td align="center" width="100" class="wrd_brk"><? echo implode(", ",array_unique(explode(",",chop($val["gmt_color"],',')))); ?></td>
										<td align="center" width="120" class="wrd_brk"><? echo $yarn_count_arr[$count_id]; ?></td>
										<td align="center" width="150" class="wrd_brk"><? echo $composition_data; ?></td>
										<td align="center" width="100" class="wrd_brk" ><? echo $val["measurement"]; ?></td>
										<td align="right" width="80" ><? echo number_format($booking_req_qnty,2); ?></td>
										<td align="right" width="50"  >Lbs</td>
										<td align="right" width="80" ><? echo number_format($pre_cost_val,2); ?></td>
										<td align="right" width="80" bgcolor="<?=$color_wo;?>" title="<?=$booking_qnty."__".$booking_req_qnty;?>"><a href="javascript:open_yarn_wo('<? echo implode(',',array_unique(explode(",",rtrim($booking_dtls_id,","))));?>','<? echo $job_id;?>','WO Info','yarn_wo_popup')"><? echo number_format($booking_qnty,2); ?></a>
										</td>
										<td align="right" width="80" ><?  echo number_format($booking_amount,2); ?></td>
										<td align="center" width="100" class="wrd_brk"><? echo implode(", ",array_unique(explode(",",rtrim($all_pi_no,',')))); ?></td>
										<td align="center" width="100" class="wrd_brk" ><? echo implode(", ",array_unique(explode(",",rtrim($btb_lc_no,',')))); ?></td>
										<td align="right" width="80"><a href="javascript:open_yarn_trns('<?=$val["job_no"];?>','<?=$yarn_color;?>','<?=$count_id;?>','<?=$yarn_comp;?>','<?=$yarn_percent;?>','Yarn Recv Info','yarn_rcv_popup')"><? echo number_format($yarn_prod_rcv_qnty-$yarn_prod_rcv_rtn_qnty,2); ?></a></td>
										<td align="right" width="80" ><? echo number_format($yarn_prod_rcv_amt-$yarn_prod_rcv_rtn_amt,2); ?></td>
										<td align="right" width="80"><?  $recv_balance=$booking_req_qnty-$yarn_prod_rcv_qnty+$yarn_prod_rcv_rtn_qnty;echo number_format($recv_balance,2);?></td>
										<td align="right" width="80"><a href="javascript:open_yarn_trns('<?=$val["job_no"];?>','<?=$yarn_color;?>','<?=$count_id;?>','<?=$yarn_comp;?>','<?=$yarn_percent;?>','Yarn Issue Info','yarn_iss_popup')"><?   echo number_format($yarn_prod_iss_qnty-$yarn_prod_iss_rtn_qnty,2); ?></a></td>
										<td align="right" width="80"><? echo number_format($yarn_prod_iss_amt-$yarn_prod_iss_rtn_amt,2); ?></td>
										<td align="right" width="80" ><? $left_over_bal=$yarn_prod_rcv_qnty-$yarn_prod_iss_qnty-$yarn_prod_rcv_rtn_qnty+$yarn_prod_iss_rtn_qnty;echo number_format($left_over_bal,2);?></td>
										<td align="right" width="100"><? $left_over_bal_amount=$yarn_prod_rcv_amt-$yarn_prod_iss_amt-$yarn_prod_rcv_rtn_amt+$yarn_prod_iss_rtn_amt;
										echo number_format($left_over_bal_amount,2); ?></td>
									</tr>
									<?
									$total_req_qnty+=$booking_req_qnty;
									$total_pre_cost_val+=$pre_cost_val;
									$total_booking_qnty+=$booking_qnty;
									$total_booking_amount+=$booking_amount;

									$total_yarn_prod_rcv+=$yarn_prod_rcv_qnty-$yarn_prod_rcv_rtn_qnty;
									$total_yarn_prod_amt+=$yarn_prod_rcv_amt-$yarn_prod_rcv_rtn_amt;
									$total_yarn_rcv_balance+=$recv_balance;
									
									$total_yarn_prod_iss_qnty+=$yarn_prod_iss_qnty-$yarn_prod_iss_rtn_qnty;
									$total_yarn_prod_iss_amt+=$yarn_prod_iss_amt-$yarn_prod_iss_rtn_amt;
									$total_left_over_bal+=$left_over_bal;
									$total_left_over_bal_amount+=$left_over_bal_amount;
									
									$i++;$a++;$b++;
								}
							}
						}
					}
				}
					
				?>
			</table>
		</div>
        	<table width="<? echo $tbl_width2+20;?>" class="tbl_bottom" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tfoot>
					<tr style="font-size:13px">
						<td width="30">&nbsp;</td>
						<td width="110">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="70">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="70">&nbsp;</td>
						<td width="40">&nbsp;</td>                        
						<td width="70">&nbsp;</td>                        
						<td width="70">&nbsp;</td>
						<td width="120">&nbsp;</td>
						<td width="100">&nbsp;</th>
						<td width="100">&nbsp;</th>
						<td width="120">&nbsp;</th>
						<td width="150">&nbsp;</th>
						<td width="100">Total :</th>
						<td width="80"><?echo number_format($total_req_qnty,2); ?></th>  
						<td width="50">&nbsp;</th>
						<td width="80"><?echo number_format($total_pre_cost_val,2); ?></th>                        
						<td width="80"><?echo number_format($total_booking_qnty,2); ?></th>                        
						<td width="80"><?echo number_format($total_booking_amount,2); ?></th>
						<td width="100">&nbsp;</th>
						<td width="100">&nbsp;</th>
						<td width="80"><?echo number_format($total_yarn_prod_rcv,2); ?></th>
						<td width="80"><?echo number_format($total_yarn_prod_amt,2); ?></th>  
						<td width="80"><?echo number_format($total_yarn_rcv_balance,2); ?></th>
						<td width="80"><?echo number_format($total_yarn_prod_iss_qnty,2); ?></th>
						<td width="80"><?echo number_format($total_yarn_prod_iss_amt,2); ?></th>
						<td width="80"><?echo number_format($total_left_over_bal,2); ?></th>
						<td width="100"><?echo number_format($total_left_over_bal_amount,2); ?></th>
					</tr>
				</tfoot>
           </table>
        </div>
		</fieldset>
        <br>
        <?
		
	####### For Accessories ########
	$modSql="SELECT a.id,a.task_catagory,a.task_name,a.task_short_name,a.task_type,a.completion_percent,a.task_sequence_no,a.task_group,b.task_template_id,b.lead_time 
	from lib_tna_task a,tna_task_template_details b where a.task_name=b.tna_task_id and b.task_type in (1,6) $task_group_con and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 order by a.task_sequence_no asc";
	
	$mod_sql= sql_select($modSql);
	$tna_task_array=array();
	$tna_task_id=array();
	$tna_task_cat=array();
	$tna_task_name_arr=array();
	foreach ($mod_sql as $row)
	{	
		$tna_task_group_by_id[$row[csf("task_name")]]=$row[csf("task_group")];
		$tna_task_id[$row[csf("task_name")]]=$row[csf("task_name")];
		$tna_task_array[$row[csf("task_name")]] =$row[csf("task_short_name")];
		$tna_task_name_array[$row[csf("id")]] =$tna_task_name[$row[csf("task_name")]];
		$tna_task_cat[$row[csf("id")]]=$row[csf("task_catagory")];
		$tna_task_name_arr[$row[csf("id")]]=$row[csf("task_name")];
		$lead_time_array[$row[csf("task_template_id")]]=$row[csf("lead_time")];
		$tast_tmp_id_arr[$row[csf("task_template_id")]][$row[csf("tna_task_id")]]=$row[csf("tna_task_id")];
	}

	$po_no_arr_all=implode(",",$po_no_arr); if($po_no_arr_all!="") $po_no_arr_all .=",0"; else $po_no_arr_all .="0"; 
	$job_no_all="'".implode("','",$job_no_arr)."'";
	$c=count($tna_task_id);
	
	if($db_type==0)
	{
		$sql ="SELECT a.APPROVED,a.READY_TO_APPROVED,a.po_number_id, a.job_no, a.shipment_date,max(b.pub_shipment_date) as pub_shipment_date,max(b.pub_shipment_date_prev) as pub_shipment_date_prev, a.template_id, a.po_receive_date,b.insert_date,";
		$i=1;
	
		foreach( $tna_task_id as $dval=>$id)    	
		{
			if ($i!=$c) $sql .="max(CASE WHEN CONCAT(a.task_number) = '".$id."' THEN concat(a.actual_start_date,'_',a.actual_finish_date,'_',a.task_start_date,'_',a.task_finish_date,'_',a.notice_date_start,'_',a.notice_date_end,'_',a.remarks,'_',a.id,'_',a.task_number,'_',a.plan_start_flag,'_',a.plan_finish_flag)  END ) as status$id, ";
			else $sql .="max(CASE WHEN CONCAT(a.task_number) = '".$id."' THEN concat(a.actual_start_date,'_',a.actual_finish_date,'_',a.task_start_date,'_',a.task_finish_date,'_',a.notice_date_start,'_',a.notice_date_end,'_',a.remarks,'_',a.id,'_',a.task_number,'_',a.plan_start_flag,'_',a.plan_finish_flag)  END ) as status$id ";
			$i++;
		}
		
		$sql .=" from tna_process_mst a, wo_po_break_down b where a.po_number_id=b.id and a.po_number_id in( $po_no_arr_all ) and a.job_no in ($job_no_all) $shipment_status_con and b.status_active=1  and b.po_quantity>0 $order_status_cond and a.task_type=1 group by a.APPROVED,a.READY_TO_APPROVED,a.po_number_id,a.job_no,b.insert_date order by a.shipment_date,a.po_number_id,a.job_no"; 
	}
	else
	{
		$sql ="SELECT a.APPROVED,a.READY_TO_APPROVED,a.po_number_id, a.job_no, max(a.shipment_date) as shipment_date,max(b.pub_shipment_date) as pub_shipment_date,max(b.pub_shipment_date_prev) as pub_shipment_date_prev,a.template_id, max(a.po_receive_date) as po_receive_date,b.insert_date,";
		$i=1;
		
		foreach( $tna_task_id as $dval=>$id)    	
		{
			if ($i!=$c) $sql .="max(CASE WHEN a.task_number = '".$id."' THEN a.actual_start_date || '_' || a.actual_finish_date || '_' || a.task_start_date || '_' || a.task_finish_date ||'_' || a.notice_date_start || '_' || a.notice_date_end || '_' || a.remarks || '_' || a.id || '_' || a.task_number || '_' || a.plan_start_flag || '_' || a.plan_finish_flag  END ) as status$id, ";
			
			else $sql .="max(CASE WHEN a.task_number = '".$id."' THEN a.actual_start_date || '_' || a.actual_finish_date || '_' || a.task_start_date || '_' || a.task_finish_date || '_' || a.notice_date_start || '_' || a.notice_date_end || '_' || a.remarks || '_' || a.id || '_' || a.task_number || '_' || a.plan_start_flag || '_' || a.plan_finish_flag  END ) as status$id ";
			
			$i++;
		}
		//------------------
			$sql_order_con='';
			$po_no_arr_all=explode(',',$po_no_arr_all);
			$chunk_po_no_arr_all=array_chunk(array_unique($po_no_arr_all),999);
			$p=1;
			foreach($chunk_po_no_arr_all as $rlz_sub_id)
			{
				if($p==1) $sql_order_con .=" and (a.po_number_id in(".implode(',',$rlz_sub_id).")"; else $sql_sub_lc .=" or a.po_number_id in(".implode(',',$rlz_sub_id).")";
				$p++;
			}
			$sql_order_con .=" )";
			
			$sql_job_con='';
			$job_no_all=explode(',',$job_no_all);
			$chunk_job_no_all=array_chunk(array_unique($job_no_all),999);
			$q=1;
			foreach($chunk_job_no_all as $rlz_sub_id)
			{
				if($q==1) $sql_job_con .=" and (a.job_no in(".implode(',',$rlz_sub_id).")"; else $sql_sub_lc .=" or a.job_no in(".implode(',',$rlz_sub_id).")";
				$p++;
			}
			$sql_job_con .=" )";
			
		//-------------------------------
		$sql .=" from  tna_process_mst a, wo_po_break_down b where a.po_number_id=b.id $sql_order_con $sql_job_con $shipment_status_con and b.status_active=1 and b.po_quantity>0 $order_status_cond  and a.task_type in (1,6)  group by a.APPROVED,a.READY_TO_APPROVED,a.po_number_id,a.job_no,a.template_id,a.shipment_date,b.insert_date order by a.shipment_date,a.po_number_id,a.job_no"; 
		
		$sql2="select a.APPROVED,a.READY_TO_APPROVED,a.po_number_id, a.job_no,a.task_number, max(a.shipment_date) as shipment_date,max(b.pub_shipment_date) as pub_shipment_date,max(b.pub_shipment_date_prev) as pub_shipment_date_prev,a.template_id, max(a.po_receive_date) as po_receive_date,b.insert_date,max(a.actual_start_date) as actual_start_date,max(a.actual_finish_date) as actual_finish_date,max(a.task_start_date) as task_start_date,max(a.task_finish_date) as task_finish_date from  tna_process_mst a, wo_po_break_down b where a.po_number_id=b.id $sql_order_con $sql_job_con $shipment_status_con and b.status_active=1 and b.po_quantity>0 $order_status_cond  and a.task_type in (1,6)  group by a.APPROVED,a.READY_TO_APPROVED,a.task_number,a.po_number_id,a.job_no,a.template_id,a.shipment_date,b.insert_date order by a.shipment_date,a.po_number_id,a.job_no";
	}
	//  echo $sql;
	$data_sql= sql_select($sql2);
		
	$poArr=array();$templateArr=array();
	foreach ($data_sql as $row)
	{
		$poArr[$row[csf('po_number_id')]]=$row[csf('po_number_id')];
		$templateArr[$row[csf('template_id')]]=$row[csf('template_id')];
		$job_wise_tna_data_arr[$row[csf('job_no')]][$row[csf('task_number')]]['actual_start_date']=$row[csf('actual_start_date')];
		$job_wise_tna_data_arr[$row[csf('job_no')]][$row[csf('task_number')]]['actual_finish_date']=$row[csf('actual_finish_date')];
		$job_wise_tna_data_arr[$row[csf('job_no')]][$row[csf('task_number')]]['plan_start_date']=$row[csf('task_start_date')];
		$job_wise_tna_data_arr[$row[csf('job_no')]][$row[csf('task_number')]]['plan_end_date']=$row[csf('task_finish_date')];
	}
	 
	// echo "<pre>";
	// print_r($job_wise_tna_data_arr);
	
	//selected task id start--------------------------------------------
	$tna_process_task_sql = "SELECT a.TNA_TASK_ID as TASK_NUMBER FROM TNA_TASK_TEMPLATE_DETAILS a WHERE a.IS_DELETED=0 and a.STATUS_ACTIVE=1 and a.task_type in (1,6) ".where_con_using_array($templateArr,0,'a.TASK_TEMPLATE_ID')." GROUP BY a.TNA_TASK_ID";
	//echo $tna_process_task_sql;//die;
	$tna_process_task_sql_result = sql_select($tna_process_task_sql);
	$tna_process_task_arr=array();
	foreach( $tna_process_task_sql_result as  $row ) 
	{	
		$tna_process_task_arr[$row["TASK_NUMBER"]]=$tna_task_array[$row["TASK_NUMBER"]];
		$tna_process_task_id_arr[$row["TASK_NUMBER"]]=$row["TASK_NUMBER"];
	}	
	
	//for sequence................
	$tempTaskNameArr=array();$tempTaskIdArr=array();
	$trimstempTaskNameArr=array();
	foreach($tna_task_array as $tid=>$tn){
		if($tna_process_task_arr[$tid]){
			$tempTaskNameArr[$tid]=$tna_process_task_arr[$tid];
			$tempTaskIdArr[$tid]=$tid;
			if( $tid==32 || $tid==71  || $tid==300  || $tid==8 || $tid==13 || $tid==29 || $tid==279  || $tid==308 || $tid==11 || $tid==307 || $tid==278  || $tid==310 || $tid==301 || $tid==309 || $tid==12 || $tid==24){
				$trimstempTaskNameArr[$tid]=$tna_process_task_arr[$tid];
				 $trims_tna[$tid]=$tid;
			}
		}
	}
	$btb_data_array=sql_select("SELECT a.id,a.btb_prefix_number, a.btb_system_id, a.lc_number, a.supplier_id, a.application_date, a.last_shipment_date, a.lc_date, a.lc_value, 
	c.item_category_id, a.importer_id ,a.btb_prefix,a.btb_prefix_number,a.bank_code,a.lc_year,a.lc_category,a.lc_serial,a.issuing_bank_id,b.pi_id,c.pi_number
	FROM com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_master_details c 	WHERE a.id=b.com_btb_lc_master_details_id and b.pi_id=c.id  and a.is_deleted = 0 and a.item_basis_id=1 
	group by a.id, a.insert_date, a.btb_prefix_number, a.btb_system_id, a.lc_number, a.supplier_id, a.application_date,  a.last_shipment_date, a.lc_date, a.lc_value, c.item_category_id, a.importer_id, a.ref_closing_status,a.btb_prefix ,a.btb_prefix_number,a.bank_code,a.lc_year,a.lc_category,a.lc_serial,a.issuing_bank_id,b.pi_id,c.pi_number");

	foreach ($btb_data_array as $row)
	{
		$pi_wise_btb_arr[$row[csf('pi_number')]]['btb_lc_no']=$row[csf('bank_code')].$row[csf('lc_year')].$row[csf('lc_category')].$row[csf('lc_serial')];
		$pi_wise_btb_arr[$row[csf('pi_number')]]['btb_lc_date']=$row[csf('lc_date')];
		$pi_wise_btb_arr[$row[csf('pi_number')]]['supplier_id']=$lib_supplier_arr[$row[csf('supplier_id')]];
	}
	
	$tna_task_array=array();
	$tna_trims_task_array=array();
	$tna_task_id=array();
	$tna_task_array=$tempTaskNameArr;
	$tna_trims_task_array=$trimstempTaskNameArr;
	$tna_task_id=$tempTaskIdArr;
	$tna_all_task=implode(",",$tna_task_id);
    if(str_replace("'","",$cbo_search_by)==1) //Order Wise
	{
		if($template==1)
		{
			
			?>
			<div  width="<?=$tbl_width;?>" >	
			<table class="rpt_table" width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
             <caption> <b style="float:left">Accessories</b></caption>
				<thead>
					<tr>
						<th width="100" colspan="38">&nbsp;</th>     
						
						<?
						foreach($tna_trims_task_array as $task_name=>$key)
						{
							$i++;

							if( $task_name==32 || $task_name==71   || $task_name==300  || $task_name==8 || $task_name==13 || $task_name==29 || $task_name==279  || $task_name==308 || $task_name==11 || $task_name==307 || $task_name==278  || $task_name==310 || $task_name==301 || $task_name==309 || $task_name==12 || $task_name==24){

							if(count($tna_task_array)==$i){ echo '<th width="100" colspan="3" title="'.$task_name.'='.$tna_task_name[$task_name].'">'. $key." <br><small>".$tna_task_group_by_id[$task_name]."</small> </th>"; }else{ echo '<th width="100" colspan="3" title="'.$task_name.'='.$tna_task_name[$task_name].'">'.$key." <br><small>".$tna_task_group_by_id[$task_name]."</small> </th>";
							}}
						}?>
					</tr>
					<tr>
						<th width="30">SL</th>
						<th width="50">Buyer</th>
						<th width="50">Season</th>
						<th width="100">Job No</th>
						<th width="100">Style Ref</th>
						<th width="100">Internal Ref</th>
						<th width="100">File No</th>
						<th width="90">Order No</th>
						<th width="80">Order Qnty</th>
						<th width="50">UOM</th>
						<th width="80">Qty (Pcs)</th>
						<th width="80">Shipment Date</th>
						<th width="100">Trims Name</th>
						<th width="140">Item Description</th>
						<th width="100">Remark</th>
						<th width="100">Brand/Sup Ref</th>
						<th width="100">Material Source</th>
						<th width="60">Appr Req.</th>
						<th width="80">Approve Status</th>
						<th width="100">Item Entry Date</th>
						<th width="80">Avg. Cons</th>
						<th width="100">Req. Qty</th>
						<th width="100">Pre Costing Value</th>
						<th width="90">WO Qty</th>
						<th width="60">Trims UOM</th>
						<th width="100">WO Value (USD)</th>
						<th width="150">Supplier</th>
						<th width="70">WO Delay Days</th>
						<th width="90">PI No</th>
						<th width="100">BTB LC No</th>
						<th width="100">BTB LC Date</th>

						<th width="90">In-House Qty</th>
						<th width="90">In-House Value</th>
						<th width="90">Receive Balance</th>
						<th width="90">Issue to Prod.</th>
						<th width="90">Issue Value</th>
						<th width="90">Left Over/ Balance (Qty)</th>
						<th width="100">Left Over/ Balance (value)</th>
						<?php
						foreach($trims_tna as $vid=>$key)
						{
							if( $key==32 || $key==71   || $key==300  || $key==8 || $key==13 || $key==29 || $key==279  || $key==308 || $key==11 || $key==307 || $key==278  || $key==310 || $key==301 || $key==309 || $key==12 || $key==24)
							{?>
								<th width="100">Plan End Date</th>
								<th width="100">Actual End Date</th>
								<th width="100">Delay/ Early</th>
								<?
							}
						}?>
					</tr>
				</thead>
			</table>
			<div style="width:<?=$tbl_width+20;?>px; max-height:400px; overflow-y:scroll" id="scroll_body">
			<table class="rpt_table" width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
			<? 
			$conversion_factor_array=array();$item_arr=array();
			$conversion_factor=sql_select("select id,trim_uom,order_uom,conversion_factor from lib_item_group where status_active=1  ");
			foreach($conversion_factor as $row_f)
			{
				$conversion_factor_array[$row_f[csf('id')]]['con_factor']=$row_f[csf('conversion_factor')];
				$conversion_factor_array[$row_f[csf('id')]]['cons_uom']=$row_f[csf('trim_uom')];
				$item_arr[$row_f[csf('id')]]['order_uom']=$row_f[csf('order_uom')];
			}
			unset($conversion_factor);
			$app_sql=sql_select("select job_no_mst,accessories_type_id,approval_status from wo_po_trims_approval_info");
			$app_status_arr=array();
			foreach($app_sql as $row)
			{
				$app_status_arr[$row[csf("job_no_mst")]][$row[csf("accessories_type_id")]]=$row[csf("approval_status")];
			}
			unset($app_sql);

			$sql_po_qty_country_wise_arr=array();
			$po_job_arr=array();
			$sql_po_country_data=sql_select("SELECT  b.id,b.job_no_mst,c.country_id, sum(c.order_quantity/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst  and b.id=c.po_break_down_id   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond group by   b.id,b.job_no_mst,c.country_id order by b.id,b.job_no_mst,c.country_id");
			foreach( $sql_po_country_data as $sql_po_country_row)
			{
				$sql_po_qty_country_wise_arr[$sql_po_country_row[csf('id')]][$sql_po_country_row[csf('country_id')]]=$sql_po_country_row[csf('order_quantity_set')];
				$po_job_arr[$sql_po_country_row[csf('id')]]=$sql_po_country_row[csf('job_no_mst')];
			}
			unset($sql_po_country_data);

			$po_data_arr=array();
			$po_id_string="";
			$today=date("Y-m-d");

			$sql_pos=("SELECT a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.order_uom, b.file_no, b.grouping, b.id, b.po_number, sum(c.order_quantity) as order_quantity, sum(c.order_quantity/a.total_set_qnty) as order_quantity_set, b.pub_shipment_date,a.season_buyer_wise
			from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
			where
			a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.company_name=$company_name $buyer_id_cond ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $date_cond $style_ref_cond $jobcond  $ordercond $file_no_cond $internal_ref_cond $year_cond $season_con
			group by a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.order_uom, a.total_set_qnty, b.file_no, b.grouping, b.id, b.po_number, b.pub_shipment_date ,a.season_buyer_wise order by b.id");
			//echo $sql_pos; die;
			$sql_po=sql_select($sql_pos);
			$po_arr=array(); $tot_rows=0;
			foreach($sql_po as $row)
			{
				$tot_rows++;
				$po_arr[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
				$po_arr[$row[csf('id')]]['job_no']=$row[csf('job_no')];
				$po_arr[$row[csf('id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
				$po_arr[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')];
				$po_arr[$row[csf('id')]]['order_uom']=$row[csf('order_uom')];
				$po_arr[$row[csf('id')]]['file_no']=$row[csf('file_no')];
				$po_arr[$row[csf('id')]]['grouping']=$row[csf('grouping')];
				$po_arr[$row[csf('id')]]['po_number']=$row[csf('po_number')];
				$po_arr[$row[csf('id')]]['order_quantity']=$row[csf('order_quantity')];
				$po_arr[$row[csf('id')]]['order_quantity_set']=$row[csf('order_quantity_set')];
				$po_arr[$row[csf('id')]]['pub_shipment_date']=$row[csf('pub_shipment_date')];
				$po_arr[$row[csf('id')]]['season_buyer_wise']=$row[csf('season_buyer_wise')];
				$po_id_string.=$row[csf('id')].",";
			}
			unset($sql_po);
			$po_id_string=rtrim($po_id_string,",");
			if($po_id_string=="")
			{
				echo "<strong style='color:red;font-size:30px'>No Data found</strong>";
				die;
			}

			$poIds=chop($po_id_string,','); $order_cond=""; $order_cond1=""; $order_cond2=""; $precost_po_cond="";
			if($db_type==2 && $tot_rows>1000)
			{
				$order_cond=" and (";
				$order_cond1=" and (";
				$order_cond2=" and (";
				$precost_po_cond=" and (";
				$poIdsArr=array_chunk(explode(",",$poIds),999);
				foreach($poIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					//$poIds_cond.=" po_break_down_id in($ids) or ";
					$order_cond.=" b.po_break_down_id in($ids) or";
					$order_cond1.=" b.po_breakdown_id in($ids) or";
					$order_cond2.=" d.po_breakdown_id in($ids) or";
					$precost_po_cond.=" c.po_break_down_id in($ids) or";
				}
				$order_cond=chop($order_cond,'or ');
				$order_cond.=")";
				$order_cond1=chop($order_cond1,'or ');
				$order_cond1.=")";
				$order_cond2=chop($order_cond2,'or ');
				$order_cond2.=")";
				$precost_po_cond=chop($precost_po_cond,'or ');
				$precost_po_cond.=")";
			}
			else
			{
				$order_cond=" and b.po_break_down_id in($poIds)";
				$order_cond1=" and b.po_breakdown_id in($poIds)";
				$order_cond2=" and d.po_breakdown_id in($poIds)";
				$precost_po_cond=" and c.po_break_down_id in($poIds)";
			}
			//echo $cbo_item_group;
			if(str_replace("'","",$cbo_item_group)=="")
			{
				$trm_group_pre_cost_cond="";
				$trm_group_without_precost_cond="";
				$trm_group_rec_cond="";
				$trm_group_recrtn_cond="";
				$trm_group_iss_cond="";
			}
			else
			{
				$trm_group_pre_cost_cond="and b.trim_group in(".str_replace("'","",$cbo_item_group).")";
				$trm_group_without_precost_cond="and b.trim_group in(".str_replace("'","",$cbo_item_group).")";
				$trm_group_rec_cond="and a.item_group_id in(".str_replace("'","",$cbo_item_group).")";
				$trm_group_recrtn_cond="and c.item_group_id in(".str_replace("'","",$cbo_item_group).")";
				$trm_group_iss_cond="and a.item_group_id in(".str_replace("'","",$cbo_item_group).")";
			}

			$condition= new condition();
			if($txt_style_id!="")
			{
				$condition->jobid_in("$txt_style_id"); 
			}
			else 
			{
				if(str_replace("'","",$txt_job_no) !='')
				{
					$condition->job_no_prefix_num("in($txt_job_no)"); 
				}
			  	if(str_replace("'","",trim($txt_style_ref))!='')
				{
					$style_ref=str_replace("'","",trim($txt_style_ref));
					$condition->style_ref_no("like '%$style_ref%'");
				}
		    }

			if($file_no!="")
			{
				$condition->file_no("=$file_no"); 
			}
		    
			if(str_replace("'","",$cbo_buyer_name)!=0)
			{
				$condition->buyer_name("=$cbo_buyer_name"); 
			}
		    
			if(str_replace("'","",$cbo_season_id)!=0)
			{
				$condition->season("=$cbo_season_id"); 
			}
			
			if($order_no_id!="")
			{
					$condition->po_id("in($order_no_id)");
			}
			elseif(str_replace("'","",$txt_order_no)!='')
			{
				$order_nos=str_replace("'","",$txt_order_no);
				$condition->po_number(" like '%$order_nos%'");
			}
			
			if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
			{
				$start_date=(str_replace("'","",$txt_date_from));
				$end_date=(str_replace("'","",$txt_date_to));
			}
			if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
			{
				  $condition->country_ship_date(" between '$start_date' and '$end_date'");
			}
			/*if(str_replace("'",'',$txt_po_breack_down_id) !="")
			{
				$condition->po_id("in($txt_po_breack_down_id)");
			}*/
			$condition->init();
			$trim= new trims($condition);
			// echo $trim->getQuery(); die;
			$trim_qty=$trim->getQtyArray_by_orderCountryAndPrecostdtlsid();
			//print_r($trim_qty);
			// $trim= new trims($condition);
			$trim_amount=$trim->getAmountArray_by_orderCountryAndPrecostdtlsid();
			$budget_arr=array();
			$order_by = ($cbo_season_id !=0) ? " c.po_break_down_id" : " b.trim_group";

			$sql_pre_cost=sql_select("SELECT a.costing_per, max(a.costing_date) as costing_date, b.id as trim_dtla_id, b.trim_group, b.remark, b.description, b.brand_sup_ref, b.cons_uom, b.cons_dzn_gmts, b.rate, b.amount, b.apvl_req, b.nominated_supp, b.insert_date, c.cons, c.country_id, c.cons as cons_cal, c.po_break_down_id
			from wo_pre_cost_mst a, wo_pre_cost_trim_cost_dtls b, wo_pre_cost_trim_co_cons_dtls c
			where c.job_no=b.job_no and b.id=c.wo_pre_cost_trim_cost_dtls_id and a.job_no=b.job_no and a.status_active=1 and c.status_active=1 and  b.status_active=1 and c.cons>0 $trm_group_pre_cost_cond $precost_po_cond
			group by a.costing_per, b.id, b.trim_group, b.remark, b.description, b.brand_sup_ref, b.cons_uom, b.cons_dzn_gmts, b.rate, b.amount, b.apvl_req, b.nominated_supp, b.insert_date, c.cons, c.pcs, c.country_id, c.po_break_down_id order by $order_by");

			//echo $sql_pre_cost; die;
			foreach($sql_pre_cost as $rowp)
			{
				$dzn_qnty=0;

				if($rowp[csf('costing_per')]==1) $dzn_qnty=12;
				else if($rowp[csf('costing_per')]==3) $dzn_qnty=12*2;
				else if($rowp[csf('costing_per')]==4) $dzn_qnty=12*3;
				else if($rowp[csf('costing_per')]==5) $dzn_qnty=12*4;
				else $dzn_qnty=1;

				$po_qty=0;$req_qnty=0; $req_value=0;
				if($rowp[csf('country_id')]==0)
				{
					$po_qty=$po_arr[$rowp[csf('po_break_down_id')]]['order_quantity'];
					$req_qnty+=$trim_qty[$rowp[csf('po_break_down_id')]][$rowp[csf('country_id')]][$rowp[csf('trim_dtla_id')]];
					//$req_value+=$trim_amount[$rowp[csf('po_break_down_id')]][$rowp[csf('country_id')]][$rowp[csf('trim_dtla_id')]];
				}
				else
				{
					$country_id= explode(",",$rowp[csf('country_id')]);
					for($cou=0;$cou<=count($country_id); $cou++)
					{
						$po_qty+=$sql_po_qty_country_wise_arr[$rowp[csf('po_break_down_id')]][$country_id[$cou]];
						$req_qnty+=$trim_qty[$rowp[csf('po_break_down_id')]][$country_id[$cou]][$rowp[csf('trim_dtla_id')]];
						//$req_value+=$trim_amount[$rowp[csf('po_break_down_id')]][$rowp[csf('country_id')]][$rowp[csf('trim_dtla_id')]];
					}
				}
				$req_value=$rowp[csf('rate')]*$req_qnty;

				$po_data_arr[$rowp[csf('po_break_down_id')]]['trim_dtla_id'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('trim_dtla_id')];// for rowspan
				$po_data_arr[$rowp[csf('po_break_down_id')]]['trim_group'][$rowp[csf('trim_group')]]=$rowp[csf('trim_group')];
				$po_data_arr[$rowp[csf('po_break_down_id')]][$rowp[csf('trim_group')]][$rowp[csf('trim_dtla_id')]]=$rowp[csf('trim_dtla_id')]; // for rowspannn
				$po_data_arr[$rowp[csf('po_break_down_id')]]['trim_group_dtls'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('trim_group')];
				$po_data_arr[$rowp[csf('po_break_down_id')]]['remark'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('remark')];
				$po_data_arr[$rowp[csf('po_break_down_id')]]['brand_sup_ref'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('brand_sup_ref')];
				$po_data_arr[$rowp[csf('po_break_down_id')]]['apvl_req'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('apvl_req')];
				$po_data_arr[$rowp[csf('po_break_down_id')]]['insert_date'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('insert_date')];
				$po_data_arr[$rowp[csf('po_break_down_id')]]['req_qnty'][$rowp[csf('trim_dtla_id')]]+=$req_qnty;
				$po_data_arr[$rowp[csf('po_break_down_id')]]['req_value'][$rowp[csf('trim_dtla_id')]]+=$req_value;
				$po_data_arr[$rowp[csf('po_break_down_id')]]['cons_uom'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('cons_uom')];
				$po_data_arr[$rowp[csf('po_break_down_id')]]['trim_group_from'][$rowp[csf('trim_dtla_id')]]="Pre_cost";
				$po_data_arr[$rowp[csf('po_break_down_id')]]['rate'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('rate')];
				$po_data_arr[$rowp[csf('po_break_down_id')]]['description'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('description')];
				$po_data_arr[$rowp[csf('po_break_down_id')]]['country_id'][$rowp[csf('trim_dtla_id')]].=$rowp[csf('country_id')].',';
				$po_data_arr[$rowp[csf('po_break_down_id')]]['avg_cons'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('cons_dzn_gmts')];
				//$po_data_arr[$rowp[csf('po_break_down_id')]]['costing_date'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('costing_date')];
				$budget_arr[$rowp[csf('po_break_down_id')]]['costing_per']=$rowp[csf('costing_per')];
				$budget_arr[$rowp[csf('po_break_down_id')]]['costing_date']=$rowp[csf('costing_date')];
			}
			unset($sql_pre_cost);

			//echo $sql_po; die;
			//LISTAGG(CAST( a.supplier_id || '**' || a.pay_mode AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.booking_no) as no_of_roll
			if($db_type==2)
			{
				$wo_sql_without_precost=sql_select("select min(a.booking_date) as booking_date, b.job_no, LISTAGG(CAST(a.booking_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as booking_no, LISTAGG(CAST(a.supplier_id || '**' || a.pay_mode AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as supplier_id, b.po_break_down_id, b.trim_group, b.pre_cost_fabric_cost_dtls_id, sum(b.wo_qnty) as wo_qnty, sum(b.amount/b.exchange_rate) as amount, sum(b.rate) as rate
				from wo_booking_mst a, wo_booking_dtls b
				where a.item_category=4 and a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name $order_cond $trm_group_without_precost_cond
				group by b.po_break_down_id, b.trim_group, b.job_no, b.pre_cost_fabric_cost_dtls_id");//and item_from_precost=2
			}
			else if($db_type==0)
			{
				$wo_sql_without_precost=sql_select("select min(a.booking_date) as booking_date, b.job_no, group_concat(a.booking_no) as booking_no, group_concat(concat_ws('**',a.supplier_id,a.pay_mode)) as supplier_id, b.po_break_down_id, b.trim_group, b.pre_cost_fabric_cost_dtls_id, sum(b.wo_qnty) as wo_qnty, sum(b.amount/b.exchange_rate) as amount, sum(b.rate) as rate
				from wo_booking_mst a, wo_booking_dtls b
				where a.item_category=4 and a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name $order_cond group by b.po_break_down_id, b.trim_group, b.job_no, b.pre_cost_fabric_cost_dtls_id");//and item_from_precost=2
			}
			//echo $wo_sql_without_precost; die;
			$style_data_arr1=array();$trims_dtls_id_arr=array();$booking_precost_id=array();
			foreach($wo_sql_without_precost as $wo_row_without_precost)
			{
				$conversion_factor_rate=$conversion_factor_array[$wo_row_without_precost[csf('trim_group')]]['con_factor'];
				//$cons_uom=$conversion_factor_array[$wo_row_without_precost[csf('trim_group')]]['cons_uom'];
				$cons_uom=$item_arr[$wo_row_without_precost[csf('trim_group')]]['order_uom'];
				$booking_no=implode(",",array_unique(explode(",",$wo_row_without_precost[csf('booking_no')])));
				$supplier_id=implode(",",array_unique(explode(",",$wo_row_without_precost[csf('supplier_id')])));
				$wo_qnty=$wo_row_without_precost[csf('wo_qnty')];
				$amount=$wo_row_without_precost[csf('amount')];
				$wo_date=$wo_row_without_precost[csf('booking_date')];

				$booking_id_arr=array_unique(explode(",",$wo_row_without_precost[csf('booking_dtls_id')]));
				foreach($booking_id_arr as $book_id)
				{
					$booking_precost_id[$book_id]=$wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')];
				}

				//$trims_dtls_id_arr[]
				if($wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')] =="" || $wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')] ==0)
				{
					$trim_dtla_id=max($po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]]['trim_dtla_id'])+1;
					$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]]['trim_dtla_id'][$trim_dtla_id]=$trim_dtla_id;// for rowspan
					$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]]['trim_group'][$wo_row_without_precost[csf('trim_group')]]=$wo_row_without_precost[csf('trim_group')];
					$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][$wo_row_without_precost[csf('trim_group')]][$trim_dtla_id]=$trim_dtla_id;// for rowspannn
					$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]]['trim_group_dtls'][$trim_dtla_id]=$wo_row_without_precost[csf('trim_group')];
					$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]]['cons_uom'][$trim_dtla_id]=$cons_uom;

					$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]]['trim_group_from'][$trim_dtla_id]="Booking Without Pre_cost";
				}
				else
				{
					$trim_dtla_id=$wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')];
				}

				$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]]['wo_qnty'][$trim_dtla_id]+=$wo_qnty;
				$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]]['amount'][$trim_dtla_id]+=$amount;
				$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]]['wo_date'][$trim_dtla_id]=$wo_date;
				$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]]['wo_qnty_trim_group'][$wo_row_without_precost[csf('trim_group')]]+=$wo_qnty;
				$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]]['booking_no'][$trim_dtla_id]=$booking_no;
				$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]]['supplier_id'][$trim_dtla_id]=$wo_row_without_precost[csf('supplier_id')];
				//$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]]['pay_mode'][$trim_dtla_id]=$wo_row_without_precost[csf('pay_mode')];
				$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]]['conversion_factor_rate'][$trim_dtla_id]=$conversion_factor_rate;
			}
			unset($wo_sql_without_precost);

			//echo "<pre>";print_r($booking_precost_id);die;

			$sql_rec_data=sql_select("select b.po_breakdown_id, a.item_group_id, c.receive_basis, a.booking_id, b.quantity as quantity, b.order_rate as rate, c.exchange_rate, (b.quantity*b.order_rate) as amount
			from inv_receive_master c, product_details_master d, inv_trims_entry_dtls a, order_wise_pro_details b
			where a.mst_id=c.id and a.trans_id=b.trans_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_cond1 $trm_group_rec_cond order by a.item_group_id ");

			//echo $receive_qty_data; die;
			foreach($sql_rec_data as $row)
			{
				if($po_data_arr[$row[csf('po_breakdown_id')]]['trim_group'][$row[csf('item_group_id')]]=="" || $po_data_arr[$row[csf('po_breakdown_id')]]['trim_group'][$row[csf('item_group_id')]]==0)
				{
					$cons_uom=$conversion_factor_array[$row[csf('item_group_id')]]['cons_uom'];
					$trim_dtla_id=max($po_data_arr[$row[csf('po_breakdown_id')]]['trim_dtla_id'])+1;
					$po_data_arr[$row[csf('po_breakdown_id')]]['trim_dtla_id'][$trim_dtla_id]=$trim_dtla_id;// for rowspan
					$po_data_arr[$row[csf('po_breakdown_id')]]['trim_group'][$row[csf('item_group_id')]]=$row[csf('item_group_id')];
					$po_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$trim_dtla_id]=$trim_dtla_id;// for rowspannn
					$po_data_arr[$row[csf('po_breakdown_id')]]['trim_group_dtls'][$trim_dtla_id]=$row[csf('item_group_id')];
					$po_data_arr[$row[csf('po_breakdown_id')]]['cons_uom'][$trim_dtla_id]=$cons_uom;
					$po_data_arr[$row[csf('po_breakdown_id')]]['trim_group_from'][$trim_dtla_id]="Trim Receive";
					//echo $trim_dtla_id.'==';
				}
				$po_data_arr[$row[csf('po_breakdown_id')]]['inhouse_qnty'][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				//$po_data_arr[$row[csf('po_breakdown_id')]]['inhouse_value'][$row[csf('item_group_id')]]+=$row[csf('amount')];
				$amount=0;  $amount=($row[csf('quantity')]*$row[csf('rate')]);
				$po_data_arr[$row[csf('po_breakdown_id')]]['inhouse_value'][$row[csf('item_group_id')]]+=$amount;
				$po_data_arr[$row[csf('po_breakdown_id')]]['basis_piwono'][$row[csf('item_group_id')]].=$row[csf('receive_basis')].'_'.$row[csf('booking_id')].',';
			}
			unset($sql_rec_data);
			//print_r($po_data_arr['20552']['inhouse_qnty']);

			$sql_wo_pi=sql_select("select a.pi_number, b.work_order_no, b.work_order_dtls_id from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=4 and a.status_active=1 and a.importer_id=$company_name and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.work_order_dtls_id>0");
			$pi_arr=array();
			foreach($sql_wo_pi as $rowPi)
			{
				if($tem_pi[$rowPi[csf('work_order_no')]][$rowPi[csf('pi_number')]]=="")
				{
					$tem_pi[$rowPi[csf('work_order_no')]][$rowPi[csf('pi_number')]]=$rowPi[csf('pi_number')];
					$pi_arr[$rowPi[csf('work_order_no')]].=$rowPi[csf('pi_number')].'**';
				}
			}
			unset($sql_wo_pi);

			$receive_rtn_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id, d.quantity as quantity, c.order_rate as rate
			from product_details_master c,order_wise_pro_details d
			where d.prod_id=c.id and d.trans_type=3 and d.entry_form=49 and d.status_active=1 and d.is_deleted=0 $order_cond2");
			foreach($receive_rtn_qty_data as $row)
			{
				$ord_uom_qty=0; $receive_rtn_amount=0;
				//$ord_uom_qty=$row[csf('quantity')]/$conversion_factor_array[$row[csf('item_group_id')]]['con_factor'];
				$ord_uom_qty=$row[csf('quantity')];
				$receive_rtn_amount=$ord_uom_qty*$row[csf('rate')];
				$po_data_arr[$row[csf('po_breakdown_id')]]['receive_rtn_qty'][$row[csf('item_group_id')]]+=$ord_uom_qty;
				$po_data_arr[$row[csf('po_breakdown_id')]]['receive_rtn_amount'][$row[csf('item_group_id')]]+=$receive_rtn_amount;
			}
			unset($receive_rtn_qty_data);

			$transfer_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id, sum((case when d.trans_type=5 then d.quantity else 0 end)-(case when d.trans_type=6 then d.quantity else 0 end)) as quantity ,
			sum(case when d.trans_type=5 then d.quantity else 0 end) as in_qty,
			sum(case when d.trans_type=6 then d.quantity else 0 end) as out_qty,
			sum(case when d.trans_type=5 then (d.quantity*d.order_rate) else 0 end) as in_amount,
			sum(case when d.trans_type=6 then (d.quantity*d.order_rate) else 0 end) as out_amount
			from product_details_master c,order_wise_pro_details d
			where d.prod_id=c.id and d.trans_type in(5,6) and d.entry_form=78 and d.status_active=1 and d.is_deleted=0 $order_cond2 group by d.po_breakdown_id, c.item_group_id");
			foreach($transfer_qty_data as $row)
			{
				$transfe_amount=0;
				$transfe_amount=$row[csf('in_amount')]-$row[csf('out_amount')];

				$po_data_arr[$row[csf('po_breakdown_id')]]['transfe_qty'][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				$po_data_arr[$row[csf('po_breakdown_id')]]['transfe_in'][$row[csf('item_group_id')]]+=$row[csf('in_qty')];
				$po_data_arr[$row[csf('po_breakdown_id')]]['transfe_out'][$row[csf('item_group_id')]]+=$row[csf('out_qty')];
				$po_data_arr[$row[csf('po_breakdown_id')]]['transfe_in_amount'][$row[csf('item_group_id')]]+=$row[csf('in_amount')];
				$po_data_arr[$row[csf('po_breakdown_id')]]['transfe_out_amount'][$row[csf('item_group_id')]]+=$row[csf('out_amount')];
				$po_data_arr[$row[csf('po_breakdown_id')]]['transfe_amount'][$row[csf('item_group_id')]]+=$transfe_amount;
			}
			unset($transfer_qty_data);

			$issue_qty_data=sql_select("select b.po_breakdown_id, p.item_group_id,sum(b.quantity) as quantity, sum(b.quantity*b.order_rate) as issue_amount
			from inv_issue_master d, product_details_master p, inv_trims_issue_dtls a, order_wise_pro_details b
			where a.mst_id=d.id and a.prod_id=p.id and a.trans_id=b.trans_id and b.trans_type=2 and d.entry_form=25 and b.entry_form=25 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_cond1 $trm_group_iss_cond group by b.po_breakdown_id, p.item_group_id");
			//echo $issue_qty_data; die;
			foreach($issue_qty_data as $row)
			{
				$po_data_arr[$row[csf('po_breakdown_id')]]['issue_qty'][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				$po_data_arr[$row[csf('po_breakdown_id')]]['issue_amount'][$row[csf('item_group_id')]]+=$row[csf('issue_amount')];
			}
			unset($issue_qty_data);


			$issue_rtn_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id, d.quantity as quantity, (d.quantity*d.order_rate) as amount
			from product_details_master c, order_wise_pro_details d
			where d.prod_id=c.id and d.trans_type=4 and d.entry_form=73 and d.status_active=1 and d.is_deleted=0 $order_cond2");
			foreach($issue_rtn_qty_data as $row)
			{
				$po_data_arr[$row[csf('po_breakdown_id')]]['issue_rtn_qty'][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				$po_data_arr[$row[csf('po_breakdown_id')]]['issue_rtn_amount'][$row[csf('item_group_id')]]+=$row[csf('amount')];
			}

			unset($issue_rtn_qty_data);

			$total_pre_costing_value=0;
			$total_wo_value=0;
			$total_left_over_balanc=0;$total_issue_amount=0;$total_rec_bal_qnty=0;

			$summary_array=array();
			$i=1;
			$bookingNoArr=return_library_array( "select id, booking_no from  wo_booking_mst", "id", "booking_no");
			$piArr=return_library_array("select id, pi_number from com_pi_master_details", "id", "pi_number");
			//die;
			foreach($po_arr as $key=>$value)
			{
				$rowspan=0;
				//print_r($po_data_arr[$key]['trim_dtla_id']);
				$rowspan=count($po_data_arr[$key]['trim_dtla_id']);
				if($rowspan!=0)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                        <td width="30" rowspan="<? echo $rowspan; ?>"><? echo $i; ?></td>
                        <td width="50" rowspan="<? echo $rowspan; ?>"><p style="word-break:break-all"><? echo $buyer_short_name_library[$value['buyer']]; ?></p></td>
                        <td width="50" rowspan="<? echo $rowspan; ?>"><p style="word-break:break-all"><? echo $lib_season_arr[$value['season_buyer_wise']]; ?></p></td>
                        <td width="100" align="center" rowspan="<? echo $rowspan; ?>"><p><? echo $value['job_no_prefix_num']; ?></p></td>
                        <td width="100" rowspan="<? echo $rowspan; ?>" style="word-break: break-all;"><p><? echo $value['style_ref']; ?></p></td>
                        <td width="100" rowspan="<? echo $rowspan; ?>" style="word-break: break-all;"><p><? echo $value['grouping']; ?></p></td>
                        <td width="100" rowspan="<? echo $rowspan; ?>" style="word-break: break-all;"><p><? echo $value['file_no']; ?></p></td>

                        <td width="90" rowspan="<? echo $rowspan; ?>" style="word-break: break-all;"><p><a href='#report_details' onclick="generate_report('<? echo $company_name; ?>','<? echo $value['job_no']; ?>','<? echo $value['buyer']; ?>','<? echo $value['style_ref']; ?>','<? echo change_date_format($budget_arr[$key]['costing_date']); ?>','<? echo $key; ?>','<? echo $budget_arr[$key]['costing_per']; ?>','preCostRpt');"> <? $po_number=$value['po_number']; echo $po_number; ?></a></p></td>

                        <td width="80" align="right" rowspan="<? echo $rowspan; ?>"><p><a href='#report_details' onclick="order_qty_popup('<? echo $company_name; ?>','<? echo $value['job_no']; ?>','<? echo $key; ?>','<? echo $value['buyer']; ?>', <? echo $txt_date_from; ?>, <? echo $txt_date_to; ?> ,'order_qty_data');"><? echo number_format($value['order_quantity_set'],0,'.',''); ?></a></p></td>

                        <td width="50" align="center" rowspan="<? echo $rowspan; ?>"><p><? echo $unit_of_measurement[$value['order_uom']]; ?></p></td>
                        <td width="80" align="right" rowspan="<? echo $rowspan; ?>"><? echo number_format($value['order_quantity'],0,'.',''); ?></td>
                        <td width="80" align="center" rowspan="<? echo $rowspan; ?>" style="word-break: break-all;"><p><? $pub_shipment_date= $value['pub_shipment_date']; echo change_date_format($pub_shipment_date); ?></p></td>
						<?
						$po_id=$po_data_arr[$key];

						//print_r ( $po_id );
						foreach($po_id['trim_group'] as $key_trim=>$value_trim)
						{
							$gg=1;
							$summary_array['trim_group'][$key_trim]=$key_trim;
							//print_r($key_trim); $po_data_arr[$row[csf('po_breakdown_id')]]['trim_group'][$row[csf('item_group_id')]]
							foreach($po_id[$key_trim] as $key_trim1=>$value_trim1)
							{
								//echo $key_trim1; $po_data_arr[$row[csf('po_breakdown_id')]]['trim_group_dtls'][$trim_dtla_id]
								$rowspannn=count($po_id[$key_trim]);
								if($gg==1)
								{
									?>
									<td width="100" style="word-break: break-all;"><p><? echo $item_library[$po_id['trim_group_dtls'][$key_trim1]]; ?></p></td>
                                    <td width="140"><p style="word-break:break-all"><? echo $po_id['description'][$key_trim1]; ?></p></td>
									<td width="100"><p style="word-break:break-all"><? echo $po_id['remark'][$key_trim1]; ?></p></td>
									<td width="100"><p style="word-break:break-all"><? echo $po_id['brand_sup_ref'][$key_trim1]; ?></p></td>
									<td width="100"><p style="word-break:break-all"><? echo $po_id['remark'][$key_trim1]; ?></p></td>
									<td width="60" align="center"><p><? if($po_id['apvl_req'][$key_trim1]==1) echo "Yes"; else echo ""; ?></p></td>
									<td width="80" align="center">
									<?
									if($po_id['apvl_req'][$key_trim1]==1)
									{
										$app_status=$app_status_arr[$value['job_no']][$po_id['trim_group_dtls'][$key_trim1]];
										$approved_status=$approval_status[$app_status];
										$summary_array['item_app'][$key_trim][all]+=1;
										if($app_status==3)
										{
											$summary_array['item_app'][$key_trim][app]+=1;
										}
									}
									else $approved_status="";

									echo $approved_status;
									$country_idAll=implode(",",array_filter(array_unique(explode(",",$value[country_id][$key_trim1]))));
									?>
									</td>
                                    <td width="100"><p><? $insert_date=explode(" ",$po_id['insert_date'][$key_trim1]); echo change_date_format($insert_date[0],'','',''); ?></p></td>
                                    <td width="80" align="right"><?=number_format($po_id['avg_cons'][$key_trim1],4); ?></td>
                                    <td width="100" align="right"><p><a href='#report_details' onclick="order_req_qty_popup('<? echo $company_name; ?>','<? echo $value['job_no']; ?>','<? echo $key; ?>', '<? echo $value['buyer']; ?>','<? echo $po_id['rate'][$key_trim1]; ?>','<? echo $po_id['trim_group_dtls'][$key_trim1];?>' ,'<? echo $po_id['booking_no'][$key_trim1] ;?>','<? echo $po_id['description'][$key_trim1];?>','<?=$country_idAll;?>','<? echo $po_id['trim_dtla_id'][$key_trim1]; ?>','<? echo $start_date ?>','<? echo $end_date ?>','order_req_qty_data');">
                                    <? $req_qty=0; $req_qty=number_format($po_id['req_qnty'][$key_trim1],2,'.',''); echo $req_qty; $summary_array['req_qnty'][$key_trim]+=$req_qty; ?></a></p></td>
                                    <td width="100" align="right"><p><? echo number_format($po_id['req_value'][$key_trim1],2); $total_pre_costing_value+=$po_id['req_value'][$key_trim1]; ?></p></td>
									<?
                                    // $conversion_factor_rate=$conversion_factor_array[$row[csf('trim_group')]]['con_factor'];
                                    $wo_qnty=0;
                                    $wo_qnty=number_format($po_id['wo_qnty'][$key_trim1],2,'.','');

                                    /*if($wo_qnty > $req_qty) $color_wo="red";
                                    else if($wo_qnty < $req_qty ) $color_wo="yellow";
                                    else  $color_wo="";*/

									if(($po_id['conversion_factor_rate'][$key_trim1]*$wo_qnty)==$req_qty) $color_wo="green";
									else if(($po_id['conversion_factor_rate'][$key_trim1]*$wo_qnty) > $req_qty) $color_wo="red";
									else if(($po_id['conversion_factor_rate'][$key_trim1]*$wo_qnty) < $req_qty ) $color_wo="yellow";
									else $color_wo="";

                                    $supplier_name_string="";
                                    $supplier_id_arr=array_unique(explode(',',$po_id['supplier_id'][$key_trim1]));
                                    foreach($supplier_id_arr as $supplier_id_arr_key=>$supplier_id_arr_value)
                                    {
										$ex_sup_data=explode("**",$supplier_id_arr_value);

										if($ex_sup_data[1]==3 || $ex_sup_data[1]==5) $suplier_name_arr=$company_library; else $suplier_name_arr=$lib_supplier_arr;
                                   		$supplier_name_string.=$suplier_name_arr[$ex_sup_data[0]].",";
                                    }

									$booking_no_arr=array_unique(explode(',',$po_id['booking_no'][$key_trim1]));
									$main_booking_no_large_data=""; $piWoNo='';
									foreach($booking_no_arr as $booking_no1)
									{
										$piWoNo.=chop($pi_arr[$booking_no1],"**").",";
										if($main_booking_no_large_data=="") $main_booking_no_large_data=$booking_no1; else $main_booking_no_large_data.=",".$booking_no1;
									}
									?>
                                    <td width="90" align="right" title="<? echo 'conversion_factor='.$po_id['conversion_factor_rate'][$key_trim1];?>" bgcolor="<? echo $color_wo;?>"><p><a href='#report_details' onclick="openmypage('<? echo $key; ?>','<? echo $po_id['trim_group_dtls'][$key_trim1]; ?>','<? echo $value['job_no']; ?>','<? echo $main_booking_no_large_data;?>','<? echo $po_id['trim_dtla_id'][$key_trim1];?>','booking_info');">
                                    <? echo number_format($wo_qnty,2,'.',''); $summary_array['wo_qnty'][$key_trim]+=$wo_qnty; ?></a></p></td>
                                    <td width="60"><p><? echo $unit_of_measurement[$item_arr[$po_id['trim_group_dtls'][$key_trim1]]['order_uom']];
                                    //echo $unit_of_measurement[$po_id['cons_uom'][$key_trim1]];
                                    $summary_array['cons_uom'][$key_trim]=$item_arr[$po_id['trim_group_dtls'][$key_trim1]]['order_uom'];//$po_id['cons_uom'][$key_trim1];
                                    ?></p></td>
                                    <td width="100" align="right" title="<? echo number_format($po_id['rate'][$key_trim1],2,'.',''); ?>"><p><? echo number_format($po_id['amount'][$key_trim1],2,'.',''); $total_wo_value+=$po_id['amount'][$key_trim1];?></p></td>
                                    <td width="150" align="left"><p><? echo chop($supplier_name_string,","); ?></p></td>
                                    <td width="70" title="<? echo change_date_format($po_id['wo_date'][$key_trim1])."==".$key_trim1;?>" align="center"><p>
                                    <? $tot=change_date_format($insert_date[0]);
                                    if($po_id['wo_qnty'][$key_trim1]<=0 )
                                    {
                                    	$daysOnHand = datediff('d',$tot,$today);
                                    }
                                    else
                                    {
										$wo_date=$po_id['wo_date'][$key_trim1];
										$wo_date=change_date_format($wo_date);
										$daysOnHand = datediff('d',$tot,$wo_date);;
                                    }
                                    echo $daysOnHand; ?></p></td>
                                    <?
									$inhouse_value=0; $inhouse_qnty=0;
									$transfe_out=number_format($po_id['transfe_out'][$key_trim],2,'.','');
									$transfe_in=number_format($po_id['transfe_in'][$key_trim],2,'.','');

									$transfe_in_out=$transfe_in.' & '.$transfe_out;
									$transfe_out_amt=number_format($po_id['transfe_out_amount'][$key_trim],2,'.','');
									$transfe_in_amt=number_format($po_id['transfe_in_amount'][$key_trim],2,'.','');

									$transfe_in_out_amt=$transfe_in_amt.' & '.$transfe_out_amt;
                                    $inhouse_qnty=($po_id['inhouse_qnty'][$key_trim]+$po_id['transfe_qty'][$key_trim])-$po_id['receive_rtn_qty'][$key_trim];

									$inhouse_value=($po_id['inhouse_value'][$key_trim]+$po_id['transfe_amount'][$key_trim])-$po_id['receive_rtn_amount'][$key_trim];

									$total_inhouse_value+=$inhouse_value;
                                    $balance=$po_id['wo_qnty_trim_group'][$key_trim]-$inhouse_qnty;
                                    $conv_rate=$conversion_factor_array[$po_id['trim_group_dtls'][$key_trim1]]['con_factor'];
                                    $issue_qnty=$po_id['issue_qty'][$key_trim]-$po_id['issue_rtn_qty'][$key_trim];
									$issue_amount=$po_id['issue_amount'][$key_trim]-$po_id['issue_rtn_amount'][$key_trim];

                                    //$tot_issue=$issue_qnty/$conv_rate;
									$tot_issue=$issue_qnty;
                                    $left_overqty=$inhouse_qnty-$tot_issue;

									$left_overamount=$inhouse_value-$issue_amount;

                                    $summary_array['inhouse_qnty'][$key_trim]+=$inhouse_qnty;
                                    $summary_array['inhouse_qnty_bl'][$key_trim]+=$balance;
                                    $summary_array['issue_qty'][$key_trim]+=$tot_issue;
                                    $summary_array['left_overqty'][$key_trim]+=$left_overqty;
									$btb_lc_no_arr=array_unique(explode(",",chop($piWoNo,',')));
									$btb_lc_no=$btb_lc_date="";
									foreach($btb_lc_no_arr as $val)
									{
										if($pi_wise_btb_arr[$val]['btb_lc_no'])
										{
											$btb_lc_no.=$pi_wise_btb_arr[$val]['btb_lc_no'].",";
											$btb_lc_date.=$pi_wise_btb_arr[$val]['btb_lc_date'].",";
										}
									}
                                    ?>
                                    <td width="90"><p><? echo chop($piWoNo,","); ?></p></td>
									<td width="100" align="right"><?=implode(", ",array_unique(explode(",",chop($btb_lc_no,','))));?></td>
									<td width="100" align="right"><?=implode(", ",array_unique(explode(",",chop($btb_lc_date,','))));?></td>
                                    <td width="90" align="right" title="<? echo "Inhouse-Qty: ".number_format($po_id['inhouse_qnty'][$key_trim]-$po_id['receive_rtn_qty'][$key_trim],2,'.','')."\n Transfer in & Out Qty: ".$transfe_in_out."\n Return Qty: ".number_format($po_id['receive_rtn_qty'][$key_trim],2,'.',''); ?>" rowspan="<? echo $rowspannn; ?>"><p><a href='#report_details' onclick="openmypage_inhouse('<? echo $key; ?>','<? echo $po_id['trim_group_dtls'][$key_trim1]; ?>','booking_inhouse_info');"><? echo number_format($inhouse_qnty,2,'.',''); ?></a></p></td>
                                    <td width="90" align="right" title="<? echo "Inhouse-Amt: ".number_format($po_id['inhouse_value'][$key_trim]-$po_id['receive_rtn_amount'][$key_trim],2,'.','')."\n Transfer in & Out Amt: ".$transfe_in_out_amt."\n Return Amt: ".number_format($po_id['receive_rtn_amount'][$key_trim],2,'.',''); ?>" rowspan="<? echo $rowspannn; ?>"><p><? echo number_format($inhouse_value,2,'.',''); ?></p></td>
                                    <td width="90" align="right" rowspan="<? echo $rowspannn; ?>"><p><? echo number_format($balance,2,'.',''); $total_rec_bal_qnty+=$balance; ?></p></td>
                                    <td width="90" align="right" title="<? echo "Issue-Qty: ".number_format($po_id['issue_qty'][$key_trim],2,'.','')."\n Issue Return Qty: ".number_format($po_id['issue_rtn_qty'][$key_trim],2,'.',''); ?>" rowspan="<? echo $rowspannn; ?>">
                                    <p><a href='#report_details' title="Conv. Factor: <? echo $conv_rate;?>" onclick="openmypage_issue('<? echo $key; ?>','<? echo $po_id['trim_group_dtls'][$key_trim1]; ?>','booking_issue_info');"><? echo number_format($tot_issue,2,'.',''); ?></a></p></td>
                                    <td width="90" align="right" title="<? echo "Issue-Amt: ".number_format($po_id['issue_amount'][$key_trim],2,'.','')."\n Issue Return Amt: ".number_format($po_id['issue_rtn_amount'][$key_trim],2,'.',''); ?>" rowspan="<? echo $rowspannn; ?>"><p><? echo number_format($issue_amount,2,'.',''); $total_issue_amount+=$issue_amount; ?></p></td>
                                    <td width="90" align="right" rowspan="<? echo $rowspannn; ?>"><p><? echo number_format($left_overqty,2,'.',''); ?></p></td>
                                    <td  width="100" align="right" rowspan="<? echo $rowspannn; ?>"><p><? echo number_format($left_overamount,2,'.',''); $total_left_over_balanc+=$left_overamount; ?></p></td>
                                    <?
								}
								else
								{
									?>
									<td width="100" title="<? echo $po_id['trim_group_from'][$key_trim1]; ?>" style="word-break: break-all;"><p><? echo $item_library[$po_id['trim_group_dtls'][$key_trim1]]; ?></p></td>
                                    <td width="140"><p style="word-break:break-all"><? echo $po_id['description'][$key_trim1]; ?></p></td>
									<td width="100"><p style="word-break:break-all"><? echo $po_id['remark'][$key_trim1]; ?></p></td>
									<td width="100"><p style="word-break:break-all"><? echo $po_id['brand_sup_ref'][$key_trim1]; ?></p></td>
									<td width="100"><p style="word-break:break-all"><? echo $po_id['source_id'][$key_trim1]; ?></p></td>
									<td width="60" align="center"><p><? if($po_id['apvl_req'][$key_trim1]==1) echo "Yes"; else echo ""; ?></p></td>
									<td width="80" align="center"><?
									if($po_id['apvl_req'][$key_trim1]==1)
									{
										$app_status=$app_status_arr[$value['job_no']][$po_id['trim_group_dtls'][$key_trim1]];
										$approved_status=$approval_status[$app_status];
										$summary_array['item_app'][$key_trim][all]+=1;
										if($app_status==3)
										{
											$summary_array['item_app'][$key_trim][app]+=1;
										}
									}
									else
									{
										$approved_status="";
									}
									echo $approved_status; ?>
									</td>
                                    <td width="100"><p><? $insert_date=explode(" ",$po_id['insert_date'][$key_trim1]); echo change_date_format($insert_date[0],'','',''); ?></p></td>
                                    <td width="80" align="right"><?=number_format($po_id['avg_cons'][$key_trim1],4); ?></td>
                                    <td width="100" align="right">
                                    <p><a href='#report_details' onclick="order_req_qty_popup('<? echo $company_name; ?>','<? echo $value['job_no']; ?>','<? echo $key; ?>', '<? echo $value['buyer']; ?>','<? echo $po_id['rate'][$key_trim1]; ?>','<? echo $po_id['trim_group_dtls'][$key_trim1];?>' ,'<? echo $po_id['booking_no'][$key_trim1] ;?>','<? echo $po_id['description'][$key_trim1];?>','<? echo $po_id['country_id'][$key_trim1]; ?>','<? echo $po_id['trim_dtla_id'][$key_trim1]; ?>','<? echo $start_date ?>','<? echo $end_date ?>','order_req_qty_data');">
                                    <? $req_qty=number_format($po_id['req_qnty'][$key_trim1],2,'.',''); echo $req_qty; $summary_array['req_qnty'][$key_trim]+=$po_id['req_qnty'][$key_trim1]; ?></a></p></td>
                                    <td width="100" align="right">
                                    <p><? echo number_format($po_id['req_value'][$key_trim1],2);
                                    $total_pre_costing_value+=$po_id['req_value'][$key_trim1];?></p>
                                    </td>
									<?
                                    $wo_qnty=number_format($po_id['wo_qnty'][$key_trim1],2);

                                    /*if($wo_qnty > $req_qty) $color_wo="red";
                                    else if($wo_qnty < $req_qty ) $color_wo="yellow";
                                    else $color_wo="";*/

									if(($po_id['conversion_factor_rate'][$key_trim1]*$wo_qnty)==$req_qty) $color_wo="green";
									else if(($po_id['conversion_factor_rate'][$key_trim1]*$wo_qnty) > $req_qty) $color_wo="red";
									else if(($po_id['conversion_factor_rate'][$key_trim1]*$wo_qnty) < $req_qty ) $color_wo="yellow";
									else $color_wo="";

									$supplier_name_string="";
                                    $supplier_id_arr=array_unique(explode(',',$po_id['supplier_id'][$key_trim1]));
                                    foreach($supplier_id_arr as $supplier_id_arr_key=>$supplier_id_arr_value)
                                    {
										$ex_sup_data=explode("**",$supplier_id_arr_value);
										if($ex_sup_data[1]==3 || $ex_sup_data[1]==5) $suplier_name_arr=$company_library; else $suplier_name_arr=$lib_supplier_arr;
                                   		$supplier_name_string.=$suplier_name_arr[$ex_sup_data[0]].",";
                                    }

									$booking_no_arr=array_unique(explode(',',$po_id['booking_no'][$key_trim1]));
									$main_booking_no_large_data=""; $piWoNo='';
									foreach($booking_no_arr as $booking_no1)
									{
										$piWoNo.=chop($pi_arr[$booking_no1],"**").",";
										if($main_booking_no_large_data=="") $main_booking_no_large_data=$booking_no1; else $main_booking_no_large_data.=",".$booking_no1;
									}

                                    ?>
                                    <td width="90" align="right" title="<? echo 'conversion_factor='.$po_id['conversion_factor_rate'][$key_trim1];?>" bgcolor="<? echo $color_wo;?>"><p><a href='#report_details' onclick="openmypage('<? echo $key; ?>','<? echo $po_id['trim_group_dtls'][$key_trim1]; ?>','<? echo $value['job_no']; ?>','<? echo $main_booking_no_large_data;?>','<? echo $po_id['trim_dtla_id'][$key_trim1];?>','booking_info');"><? echo number_format($po_id['wo_qnty'][$key_trim1],2,'.',''); $summary_array['wo_qnty'][$key_trim]+=$po_id['wo_qnty'][$key_trim1];?></a></p></td>
                                    <td width="60" align="center"><p><? $unit_of_measurement[$item_arr[$po_id['trim_group_dtls'][$key_trim1]]['order_uom']];
                                    //echo $unit_of_measurement[$po_id['cons_uom'][$key_trim1]];
                                    $summary_array['cons_uom'][$key_trim]= $item_arr[$po_id['trim_group_dtls'][$key_trim1]]['order_uom'];//$po_id['cons_uom'][$key_trim1]; ?></p>
                                    </td>
								
                                    <td width="100" align="right" title="<? echo number_format($po_id['rate'][$key_trim1],2,'.',''); ?>"><p><? echo number_format($value['amount'][$key_trim1],2,'.',''); $total_wo_value+=$po_id['amount'][$key_trim1]; ?></p></td>
                                    <td width="150"><p><? echo chop($supplier_name_string,","); ?></p></td>

                                    <td width="70" align="center" title="<? echo change_date_format($po_id['wo_date'][$key_trim1])."==".$key_trim1;?>"><p>
									<?
                                    $tot=change_date_format($insert_date[0]);
                                    if($po_id['wo_qnty'][$key_trim1]<=0 )
                                    {
                                    	$daysOnHand = datediff('d',$tot,$today);
                                    }
                                    else
                                    {
										$wo_date=$po_id['wo_date'][$key_trim1];
										$wo_date=change_date_format($wo_date);
										$daysOnHand = datediff('d',$tot,$wo_date);;
                                    }
                                    echo $daysOnHand; ?></p></td>
                                    <td width="90"><p><? echo chop($piWoNo,","); ?></p></td>
									<td width="100" align="right"><?=$pi_wise_btb_lc_arr[$row[csf("pi_id")]]['pi_number'];?></td>
									<td width="100" align="right"><?=$pi_wise_btb_lc_arr[$row[csf("pi_id")]]['btb_lc_date'];?></td>
                                    <?
									}
									foreach($trims_tna as $vid=>$key)
									{
									 if( $key==32 || $key==71   || $key==300  || $key==8 || $key==13 || $key==29 || $key==279  || $key==308 || $key==11 || $key==307 || $key==278  || $key==310 || $key==301 || $key==309 || $key==12 || $key==24){
									
										$plan_date=$job_wise_tna_data_arr[$job_no][$key]['plan_end_date'];
										$actual_date=$job_wise_tna_data_arr[$job_no][$key]['actual_finish_date'];
										if(strtotime($actual_date)>0){
										$diff = abs(strtotime($plan_date) - strtotime($actual_date));
										$delay = floor($diff / (60*60*24));	
										}else{
											$delay="";
										}							
								
								 ?>
									<td width="100" align="right" id=""><?=$job_wise_tna_data_arr[$job_no][$key]['plan_end_date'];;?></td>
									<td width="100" align="right" id=""><?=$job_wise_tna_data_arr[$job_no][$key]['actual_finish_date'];;?></td>
									<td width="100" align="right" id=""><? 	echo $delay;?></td>
										<? }}?>
									
									 
                                    </tr>
                                    <?
                                    $gg++;
								}
							}// end  foreach($value[$key_trim] as $key_trim1=>$value_trim1)
							$i++;
						}
					}
					unset($po_data_arr);
					unset($po_arr);
				?>
				</table>
                </div>
                <table class="rpt_table" width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <tfoot>
                        <th width="30">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="90">&nbsp;</th>
						
                        <th width="80" align="right" id="total_order_qnty"><? //echo number_format($total_order_qnty,0); ?></th>
                        <th width="50">&nbsp;</th>
                        <th width="80" align="right" id="total_order_qnty_in_pcs"><? //echo number_format($total_order_qnty_in_pcs,0); ?></th>
                        <th width="80">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="140">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>

                        <th width="60">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="100">&nbsp;</th>
						<th width="80">&nbsp;</th>
                        <th width="100" align="right" id="value_req_qnty"><? //echo number_format($total_req_qnty,2); ?></th>
                        <th width="100" align="right" id="value_pre_costing"><? echo number_format($total_pre_costing_value,2); ?></th>
                        <th width="90" align="right" id="value_wo_qty"><? //echo number_format($total_wo_qnty,2); ?></th>
						
                        <th width="60" align="right" >&nbsp;</th>
                        <th width="100" align="right" id=""><? echo number_format($total_wo_value,2); ?></th>
                        <th width="150" align="right" id="">&nbsp;</th>
                        <th width="70" align="right">&nbsp;<p><? //echo number_format($req_value,2,'.',''); ?></p></th>
                        <th width="90" align="right">&nbsp;<? //echo number_format($total_in_qnty,2); ?></th>
						
						<th width="100" align="right" >&nbsp;</th>
						<th width="100" align="right" >&nbsp;</th>

                        <th width="90" align="right" id="value_in_qty">&nbsp;<? //echo number_format($total_in_qnty,2); ?></th>



                        <th width="90" align="right" id="value_in_amount">&nbsp;<? echo number_format($total_inhouse_value,2); ?></th>
		

                        <th width="90" align="right" id="value_rec_qty"><? echo number_format($total_rec_bal_qnty,2); ?></th>
                        <th width="90" align="right" id="value_issue_qty">&nbsp;<? //echo number_format($total_issue_qnty,2); ?></th>
                        <th width="90" align="right" id="value_issue_amount"><? echo number_format($total_issue_amount,2); ?></th>

                        <th width="90" align="right" id="value_leftover_qty">&nbsp;<? //echo number_format($total_leftover_qnty,2); ?></th>
                        <th  width="100" align="right" id="value_leftover_amount"><? echo number_format($total_left_over_balanc,2); ?></th>

						<?php
								foreach($trims_tna as $vid=>$key)
									{
									 if( $key==32 || $key==71   || $key==300  || $key==8 || $key==13 || $key==29 || $key==279  || $key==308 || $key==11 || $key==307 || $key==278  || $key==310 || $key==301 || $key==309 || $key==12 || $key==24){?>

								<th width="100">&nbsp;</th>
								<th width="100">&nbsp;</th>
								<th width="100">&nbsp;</th>

						<?}}?>
                    </tfoot>
                </table>

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
                $tot_req_qnty_summary=$tot_wo_qnty_summary=$tot_in_qnty_summary=$in_house_bal=$tot_issue_qnty_summary=$tot_leftover_qnty_summary=0;
                foreach($summary_array[trim_group] as $key_trim=>$value)
                {
                    if($z%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr2_<? echo $z; ?>','<? echo $bgcolor;?>')" id="tr2_<? echo $z; ?>">
                        <td width="30"><? echo $z; ?></td>
                        <td width="110"><p><? echo $item_library[$key_trim]; ?></p></td>
                        <td width="60" align="center"><? echo $unit_of_measurement[$summary_array[cons_uom][$key_trim]]; ?></td>
                        <td width="80" align="right"><? $app_perc=($summary_array[item_app][$key_trim][app]*100)/$summary_array[item_app][$key_trim][all]; if ($app_perc>=0) echo number_format($app_perc,2); ?></td>
                        <td width="110" align="right"><? echo number_format($summary_array[req_qnty][$key_trim],2); ?></td>
                        <td width="110" align="right"><? echo number_format( $summary_array[wo_qnty][$key_trim],2); ?></td>
                        <td width="80" align="right"><? $wo_per= $summary_array[wo_qnty][$key_trim]/$summary_array[req_qnty][$key_trim]*100; echo number_format($wo_per,2).'%'; ?></td>
                        <td width="110" align="right"><? echo number_format($summary_array[inhouse_qnty][$key_trim],2); ?></td>
                        <td width="80" align="right"><? $in_per=$summary_array[inhouse_qnty][$key_trim]/$summary_array[wo_qnty][$key_trim]*100; echo number_format($in_per,2).'%'; ?></td>
                        <td width="110" align="right"><? echo number_format($summary_array[inhouse_qnty_bl][$key_trim],2); $in_house_bal+=($value['wo']-$value['in']); ?></td>
                        <td width="110" align="right"><? echo number_format($summary_array[issue_qty][$key_trim],2); ?></td>
                        <td width="80" align="right"><? $wo_per=$summary_array[issue_qty][$key_trim]/$summary_array[wo_qnty][$key_trim]*100; echo number_format($wo_per,2).'%'; ?></td>
                        <td align="right"><? echo number_format($summary_array[left_overqty][$key_trim],2); ?></td>
                    </tr>
					<?
                    $z++;
                    if(isset($summary_array[req_qnty][$key_trim]))
					{
						 $tot_req_qnty_summary+=$summary_array[req_qnty][$key_trim];
					}
					if(isset($summary_array[wo_qnty][$key_trim]))
					{
						 $tot_wo_qnty_summary+=$summary_array[wo_qnty][$key_trim];
					}

					if(isset($summary_array[inhouse_qnty][$key_trim]))
					{
						$tot_in_qnty_summary+=$summary_array[inhouse_qnty][$key_trim];
					}
					if(isset($summary_array[inhouse_qnty_bl][$key_trim]))
					{
						$in_house_bal+=$summary_array[inhouse_qnty_bl][$key_trim];
					}

					if(isset($summary_array[issue_qty][$key_trim]))
					{
						$tot_issue_qnty_summary+=$summary_array[issue_qty][$key_trim];
					}

					if(isset($summary_array[left_overqty][$key_trim]))
					{
						 $tot_leftover_qnty_summary+=$summary_array[left_overqty][$key_trim];
					}
				}
				$summary_array=array();
				?>
                <tfoot>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_req_qnty_summary,2); ?>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_wo_qnty_summary,2); ?>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_in_qnty_summary,2); ?>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th align="right"><? echo number_format($in_house_bal,2); ?>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_issue_qnty_summary,2); ?>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_leftover_qnty_summary,2); ?>&nbsp;</th>
                </tfoot>
			</table>
			</div>
		<?
        }
	}
	else if(str_replace("'","",$cbo_search_by)==2) //Style Wise
	{

		if($template==1)
		{
			//ob_start();
			?>
		
			<fieldset style="width:100%;">
                <table width="<?=$tbl_width3;?>">
                <caption> <b style="float:left">Accessories</b></caption>
                    <tr class="form_caption"><td colspan="32" align="center"><? //echo $report_title; ?></td></tr>
                    <tr class="form_caption"><td colspan="32" align="center"><? //echo $company_library[$company_name]; ?></td></tr>
                </table>
                <table class="rpt_table" width="<?=$tbl_width3;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
						
				<tr>
					<th width="100" colspan="38">&nbsp;</th>     
					
					<?
					foreach($tna_trims_task_array as $task_name=>$key)
					{
						$i++;

						if( $task_name==32 || $task_name==71   || $task_name==300  || $task_name==8 || $task_name==13  || $task_name==29 || $task_name==279  || $task_name==308 || $task_name==11 || $task_name==307 || $task_name==278  || $task_name==310 || $task_name==301 || $task_name==309 || $task_name==12 || $task_name==24){

						if(count($tna_task_array)==$i){ echo '<th width="100" colspan="3" title="'.$task_name.'='.$tna_task_name[$task_name].'">'. $key." <br><small>".$tna_task_group_by_id[$task_name]."</small> </th>"; }else{ echo '<th width="100" colspan="3" title="'.$task_name.'='.$tna_task_name[$task_name].'">'.$key." <br><small>".$tna_task_group_by_id[$task_name]."</small> </th>";

						}}
						
					}?>
				</tr>
				<tr>
					<th width="30">SL</th>
					<th width="50">Buyer</th>
					<th width="50">Season</th>
					<th width="100">Job No</th>
					<th width="100">Style Ref</th>
					<th width="100">Internal Ref</th>
					<th width="100">File No</th>
					<th width="90">Order No</th>
					<th width="80">Order Qty</th>
					<th width="50">UOM</th>
					<th width="80">Qty (Pcs)</th>
					<th width="80">Shipment Date</th>
					<th width="100">Trims Name</th>
					<th width="140">Item Description</th>
					<th width="100">Remarks</th>
					<th width="100">Brand/Sup Ref</th>
					<th width="100">Material Source</th>
					<th width="60">Appr Req.</th>
					<th width="80">Approve Status</th>
					<th width="100">Item Entry Date</th>
					<th width="80">Avg. Cons</th>
					<th width="100">Req Qty</th>
					<th width="100">Pre Costing Value</th>
					<th width="90">WO Qty</th>
					<th width="60">Trims UOM</th>
					<th width="100">WO Value (USD)</th>
					<th width="150">Supplier</th>
					<th width="70">WO Delay Days</th>
					<th width="70">PI No.</th>
					<th width="100">BTB LC No</th>
					<th width="100">BTB LC Date</th>
					<th width="90">In-House Qty</th>
					<th width="90">In-House Amount</th>
					<th width="90">Receive Balance</th>
					<th width="90">Issue to Prod.</th>
					<th width="90">Issue Amount</th>
					<th width="90">Left Over/Balance</th>
					<th  width="100">Left Over/Balance Amount</th>
					<?php
						foreach($trims_tna as $vid=>$key)
						{
							if( $key==32 || $key==71  || $key==300  || $key==8 || $key==13 || $key==29 || $key==279  || $key==308 || $key==11 || $key==307 || $key==278  || $key==310 || $key==301 || $key==309 || $key==12 || $key==24)
							{
								?>
									<th width="100">Plan End Date</th>
									<th width="100">Actual End Date</th>
									<th width="100">Delay/ Early</th>
								<?
							}
						}
					?>
				</tr>
				</thead>
                </table>
                <div style="width:<?=$tbl_width3+20;?>px; max-height:400px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="<?=$tbl_width3;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
			<?

			$conversion_factor_array=array(); $item_arr=array();
			$conversion_factor=sql_select("select id ,trim_uom,order_uom,conversion_factor from  lib_item_group  where status_active=1 and item_category=4");
			foreach($conversion_factor as $row_f)
			{
				$conversion_factor_array[$row_f[csf('id')]]['con_factor']=$row_f[csf('conversion_factor')];
				$conversion_factor_array[$row_f[csf('id')]]['cons_uom']=$row_f[csf('trim_uom')];
				$item_arr[$row_f[csf('id')]]['order_uom']=$row_f[csf('order_uom')];
			}
			unset($conversion_factor);
			$app_sql=sql_select("select job_no_mst,accessories_type_id,approval_status from wo_po_trims_approval_info");
			$app_status_arr=array();
			foreach($app_sql as $row)
			{
				$app_status_arr[$row[csf("job_no_mst")]][$row[csf("accessories_type_id")]]=$row[csf("approval_status")];
			}
			unset($app_sql);

			$sql_po_qty_country_wise_arr=array();
			$po_job_arr=array(); $style_po_qty_arr=array();
 			$sql_po_qty_country_wise=sql_select("select  b.id, b.job_no_mst, c.country_id, sum(c.order_quantity/a.total_set_qnty) as order_quantity_set, sum(c.order_quantity) as order_quantity  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst  and b.id=c.po_break_down_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $year_cond group by b.id, b.job_no_mst, c.country_id order by b.id, b.job_no_mst, c.country_id");
			foreach( $sql_po_qty_country_wise as $row)
			{
				$sql_po_qty_country_wise_arr[$row[csf('id')]][$row[csf('country_id')]]=$row[csf('order_quantity_set')];
				$po_job_arr[$row[csf('id')]]=$row[csf('job_no_mst')];
				$style_po_qty_arr[$row[csf('job_no_mst')]]['order_qty_set']+=$row[csf('order_quantity_set')];
				$style_po_qty_arr[$row[csf('job_no_mst')]]['po_qty']+=$row[csf('order_quantity')];
			}
			//print_r($style_po_qty_arr);
			unset($sql_po_qty_country_wise);			

			$style_data_arr=array();
			$po_id_string="";
			$today=date("Y-m-d");
			$sql_pos=("SELECT a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.order_uom, b.file_no, b.grouping, b.id, b.po_number, sum(c.order_quantity) as order_quantity, sum(c.order_quantity/a.total_set_qnty) as order_quantity_set, b.pub_shipment_date , a.season_buyer_wise
			from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
			where
			a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.company_name=$company_name $buyer_id_cond ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $date_cond $style_ref_cond $jobcond  $ordercond $file_no_cond $internal_ref_cond  $year_cond $season_con 
			group by a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.order_uom, a.total_set_qnty, b.file_no, b.grouping, b.id, b.po_number, b.pub_shipment_date  , a.season_buyer_wise order by b.id");
			//echo $sql_pos; //and a.job_no='FAL-16-00179'
			$sql_po=sql_select($sql_pos);
			$tot_rows=0;  $style_data_all=array();
			foreach($sql_po as $row)
			{
				$tot_rows++;

				$style_data[$row[csf('job_no')]]['job_data']=$row[csf("buyer_name")]."##".$row[csf("job_no_prefix_num")]."##".$row[csf("style_ref_no")]."##".$row[csf("order_uom")]."##".$row[csf('season_buyer_wise')];

				$style_data_all[$row[csf('job_no')]].=$row[csf("file_no")]."__".$row[csf("grouping")]."__".$row[csf("po_number")]."__".$row[csf("pub_shipment_date")]."__".$row[csf("shiping_status")]."__".$row[csf("id")]."***";

				$po_arr[$row[csf('job_no')]]['order_quantity']+=$row[csf('order_quantity')];
				$po_arr[$row[csf('job_no')]]['order_quantity_set']+=$row[csf('order_quantity_set')];
				$po_id_string.=$row[csf('id')].",";
			}
		
			unset($sql_po);
			$po_id_string=rtrim($po_id_string,",");
			//	print_r($po_id_string); die;
			if($po_id_string=="")
			{
				echo "<strong style='color:red;font-size:30px'>No Data found</strong>";
				die;
			}

			$poIds=chop($po_id_string,','); $order_cond=""; $order_cond1=""; $order_cond2=""; $precost_po_cond="";
			if($db_type==2 && $tot_rows>1000)
			{
				$order_cond=" and (";
				$order_cond1=" and (";
				$order_cond2=" and (";
				$precost_po_cond=" and (";
				$poIdsArr=array_chunk(explode(",",$poIds),999);
				foreach($poIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					//$poIds_cond.=" po_break_down_id in($ids) or ";
					$order_cond.=" b.po_break_down_id in($ids) or";
					$order_cond1.=" b.po_breakdown_id in($ids) or";
					$order_cond2.=" d.po_breakdown_id in($ids) or";
					$precost_po_cond.=" c.po_break_down_id in($ids) or";
				}
				$order_cond=chop($order_cond,'or ');
				$order_cond.=")";
				$order_cond1=chop($order_cond1,'or ');
				$order_cond1.=")";
				$order_cond2=chop($order_cond2,'or ');
				$order_cond2.=")";
				$precost_po_cond=chop($precost_po_cond,'or ');
				$precost_po_cond.=")";
			}
			else
			{
				$order_cond=" and b.po_break_down_id in($poIds)";
				$order_cond1=" and b.po_breakdown_id in($poIds)";
				$order_cond2=" and d.po_breakdown_id in($poIds)";
				$precost_po_cond=" and c.po_break_down_id in($poIds)";
			}
			if(str_replace("'","",$cbo_item_group)=="")
			{
				$trm_group_pre_cost_cond="";
				$trm_group_without_precost_cond="";
				$trm_group_rec_cond="";
				$trm_group_recrtn_cond="";
				$trm_group_iss_cond="";
			}
			else
			{
				$trm_group_pre_cost_cond="and b.trim_group in(".str_replace("'","",$cbo_item_group).")";
				$trm_group_without_precost_cond="and b.trim_group in(".str_replace("'","",$cbo_item_group).")";
				$trm_group_rec_cond="and a.item_group_id in(".str_replace("'","",$cbo_item_group).")";
				$trm_group_recrtn_cond="and c.item_group_id in(".str_replace("'","",$cbo_item_group).")";
				$trm_group_iss_cond="and a.item_group_id in(".str_replace("'","",$cbo_item_group).")";
			}

			$condition= new condition();
			
			if($txt_style_id!="")
			{
				$condition->jobid_in("$txt_style_id"); 
			}
			else {
					if(str_replace("'","",$txt_job_no) !='')
					{
					$condition->job_no_prefix_num("in($txt_job_no)"); 
				  }
				  if(str_replace("'","",trim($txt_style_ref))!='')
					{
						$style_ref=str_replace("'","",trim($txt_style_ref));
						$condition->style_ref_no("like '%$style_ref%'");
					}
		    }
			
			
			if($order_no_id!="")
			{
					$condition->po_id("in($order_no_id)");
			}
			elseif(str_replace("'","",$txt_order_no)!='')
			{
				$order_nos=str_replace("'","",$txt_order_no);
				$condition->po_number(" like '%$order_nos%'");
			}			

			if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
			{
				$start_date=(str_replace("'","",$txt_date_from));
				$end_date=(str_replace("'","",$txt_date_to));
			}

			if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
			{
				  $condition->country_ship_date(" between '$start_date' and '$end_date'");
			}			

			if($file_no!="")
			{
				$condition->file_no("=$file_no"); 
			}
		    
			if(str_replace("'","",$cbo_buyer_name)!=0)
			{
				$condition->buyer_name("=$cbo_buyer_name"); 
			}
		    
			if(str_replace("'","",$cbo_season_id)!=0)
			{
				$condition->season("=$cbo_season_id"); 
			}
			$condition->init();
			$trim= new trims($condition);
			//echo $trim->getQuery();die;
			//$trim_qty=$trim->getQtyArray_by_orderAndPrecostdtlsid();
			//$trim_qty=$trim->getQtyArray_by_orderCountryAndPrecostdtlsid();
			$trim_qty=$trim->getQtyArray_by_jobAndPrecostdtlsid();
			//print_r($trim_qty);
			$trim= new trims($condition);
			//$trim_amount=$trim->getAmountArray_by_orderAndPrecostdtlsid();
			$trim_amount=$trim->getAmountArray_by_jobAndPrecostdtlsid();
			$order_by = ($cbo_season_id !=0) ? " c.po_break_down_id" : " b.trim_group";
			$costing_arr=array();
		 	$sql_pre="SELECT a.costing_per, a.costing_date, b.id as trim_dtla_id, b.trim_group, b.remark, b.description, b.brand_sup_ref, b.cons_uom, b.cons_dzn_gmts, b.rate, b.amount, b.apvl_req, b.nominated_supp, b.insert_date, c.cons, c.country_id, c.cons as cons_cal, c.po_break_down_id, b.job_no,b.source_id			from wo_pre_cost_mst a, wo_pre_cost_trim_cost_dtls b, wo_pre_cost_trim_co_cons_dtls c
			where c.job_no=b.job_no and b.id=c.wo_pre_cost_trim_cost_dtls_id and a.job_no=b.job_no and c.cons>0 and b.status_active=1 and a.status_active=1 and c.status_active=1 $trm_group_pre_cost_cond $precost_po_cond
			group by a.costing_per, a.costing_date, b.id, b.trim_group, b.remark, b.description, b.brand_sup_ref, b.cons_uom, b.cons_dzn_gmts, b.rate, b.amount, b.apvl_req, b.nominated_supp, b.insert_date, c.cons, c.pcs, c.country_id, c.po_break_down_id, b.job_no,b.source_id order by $order_by";
			
			$sql_pre_cost=sql_select($sql_pre);

			//echo $sql_pre_cost; die;
			if(count($sql_pre_cost)>0)
			{
				foreach($sql_pre_cost as $rowp)
				{
					$dzn_qnty=0;

					if($rowp[csf('costing_per')]==1) $dzn_qnty=12;
					else if($rowp[csf('costing_per')]==3) $dzn_qnty=12*2;
					else if($rowp[csf('costing_per')]==4) $dzn_qnty=12*3;
					else if($rowp[csf('costing_per')]==5) $dzn_qnty=12*4;
					else $dzn_qnty=1;

					$po_qty=0;  $req_value=0;//$req_qnty=0;
					if($rowp[csf('country_id')]==0)
					{
						$po_qty=$po_arr[$rowp[csf('job_no')]]['order_quantity'];
						//$req_qnty+=$trim_qty[$rowp[csf('po_break_down_id')]][$rowp[csf('country_id')]][$rowp[csf('trim_dtla_id')]];
					}
					else
					{
						$country_id= explode(",",$rowp[csf('country_id')]);
						for($cou=0;$cou<=count($country_id); $cou++)
						{
							$po_qty+=$sql_po_qty_country_wise_arr[$rowp[csf('po_break_down_id')]][$country_id[$cou]];
							//$req_qnty+=$trim_qty[$rowp[csf('po_break_down_id')]][$country_id[$cou]][$rowp[csf('trim_dtla_id')]];
						}

					}
					$req_qnty=$trim_qty[$rowp[csf('job_no')]][$rowp[csf('trim_dtla_id')]];
					$req_value=$trim_amount[$rowp[csf('job_no')]][$rowp[csf('trim_dtla_id')]];

					$style_data_arr[$rowp[csf('job_no')]]['trim_dtla_id'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('trim_dtla_id')];// for rowspan
					$style_data_arr[$rowp[csf('job_no')]]['trim_group'][$rowp[csf('trim_group')]]=$rowp[csf('trim_group')];
					$style_data_arr[$rowp[csf('job_no')]][$rowp[csf('trim_group')]][$rowp[csf('trim_dtla_id')]]=$rowp[csf('trim_dtla_id')]; // for rowspannn
					$style_data_arr[$rowp[csf('job_no')]]['trim_group_dtls'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('trim_group')];
					$style_data_arr[$rowp[csf('job_no')]]['remark'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('remark')];
					$style_data_arr[$rowp[csf('job_no')]]['brand_sup_ref'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('brand_sup_ref')];
					$style_data_arr[$rowp[csf('job_no')]]['apvl_req'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('apvl_req')];
					$style_data_arr[$rowp[csf('job_no')]]['insert_date'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('insert_date')];
					$style_data_arr[$rowp[csf('job_no')]]['req_qnty'][$rowp[csf('trim_dtla_id')]]+=$req_qnty;
					$style_data_arr[$rowp[csf('job_no')]]['req_value'][$rowp[csf('trim_dtla_id')]]+=$req_value;
					$style_data_arr[$rowp[csf('job_no')]]['cons_uom'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('cons_uom')];
					$style_data_arr[$rowp[csf('job_no')]]['trim_group_from'][$rowp[csf('trim_dtla_id')]]="Pre_cost";
					$style_data_arr[$rowp[csf('job_no')]]['rate'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('rate')];
					$style_data_arr[$rowp[csf('job_no')]]['description'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('description')];
					$style_data_arr[$rowp[csf('job_no')]]['country_id'][$rowp[csf('trim_dtla_id')]].=$rowp[csf('country_id')].',';
					$style_data_arr[$rowp[csf('job_no')]]['avg_cons'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('cons_dzn_gmts')];
					$style_data_arr[$rowp[csf('job_no')]]['source_id'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('source_id')];
	
					$costing_arr[$rowp[csf('job_no')]]['costing_per']=$rowp[csf('costing_per')];
					$costing_arr[$rowp[csf('job_no')]]['costing_date']=$rowp[csf('costing_date')];
				}
			}
			else
			{
				echo "<strong style='color:red;font-size:30px'>No Data found</strong>";
				die;
			}
			unset($sql_pre_cost);
			//	print_r($style_data_arr);

			if($db_type==2)
			{

				$sql_without_precost=sql_select("select min(a.booking_date) as booking_date, b.job_no, LISTAGG(CAST(a.booking_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as booking_no, LISTAGG(CAST(a.supplier_id || '**' || a.pay_mode AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as supplier_id, b.po_break_down_id, b.trim_group, b.pre_cost_fabric_cost_dtls_id, sum(b.wo_qnty) as wo_qnty, sum(b.amount/b.exchange_rate) as amount, sum(b.rate) as rate, LISTAGG(CAST(b.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as booking_dtls_id
				from wo_booking_mst a, wo_booking_dtls b
				where a.item_category=4 and a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name $order_cond $item_group_cond2
				group by b.po_break_down_id,b.trim_group,b.job_no,b.pre_cost_fabric_cost_dtls_id");//and item_from_precost=2
			}
			else if($db_type==0)
			{
				$sql_without_precost=sql_select("select min(a.booking_date) as booking_date ,b.job_no, group_concat(a.booking_no) as booking_no, group_concat(concat_ws('**',a.supplier_id,a.pay_mode)) as supplier_id, b.po_break_down_id, b.trim_group,b.pre_cost_fabric_cost_dtls_id,sum(b.wo_qnty) as wo_qnty,sum(b.amount/b.exchange_rate) as amount,sum(b.rate) as rate, group_concat(b.id) as booking_dtls_id
				from wo_booking_mst a, wo_booking_dtls b
				where a.item_category=4 and a.booking_no=b.booking_no  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name $order_cond  $item_group_cond2
				group by b.po_break_down_id,b.trim_group,b.job_no,b.pre_cost_fabric_cost_dtls_id");//and item_from_precost=2
			}
			//print_r($sql_without_precost);
			$style_data_arr1=array();

			foreach($sql_without_precost as $wo_row_without_precost)
			{
				$conversion_factor_rate=$conversion_factor_array[$wo_row_without_precost[csf('trim_group')]]['con_factor'];
				//$cons_uom=$item_arr[$wo_row_without_precost[csf('trim_group')]]['order_uom'];
				$cons_uom=$conversion_factor_array[$wo_row_without_precost[csf('trim_group')]]['cons_uom'];
				$booking_no=implode(",",array_unique(explode(",",$wo_row_without_precost[csf('booking_no')])));
				$supplier_id=implode(",",array_unique(explode(",",$wo_row_without_precost[csf('supplier_id')])));
				$wo_qnty=$wo_row_without_precost[csf('wo_qnty')];//*$conversion_factor_rate;
				$amount=$wo_row_without_precost[csf('amount')];
				$wo_date=$wo_row_without_precost[csf('booking_date')];

				$booking_id_arr=array_unique(explode(",",$wo_row_without_precost[csf('booking_dtls_id')]));
				foreach($booking_id_arr as $book_id)
				{
					$booking_precost_id[$book_id]=$wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')];
				}

				if($wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')] =="" || $wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')] ==0)
				{
					//echo $wo_row_without_precost[csf('trim_group')];
					$trim_dtla_id=max($style_data_arr[$wo_row_without_precost[csf('job_no')]]['trim_dtla_id'])+1;
					$style_data_arr[$wo_row_without_precost[csf('job_no')]]['trim_dtla_id'][$trim_dtla_id]=$trim_dtla_id;// for rowspan
					$style_data_arr[$wo_row_without_precost[csf('job_no')]]['trim_group'][$wo_row_without_precost[csf('trim_group')]]=$wo_row_without_precost[csf('trim_group')];
					$style_data_arr[$wo_row_without_precost[csf('job_no')]][$wo_row_without_precost[csf('trim_group')]][$trim_dtla_id]=$trim_dtla_id;// for rowspannn
					$style_data_arr[$wo_row_without_precost[csf('job_no')]]['trim_group_dtls'][$trim_dtla_id]=$wo_row_without_precost[csf('trim_group')];
					$style_data_arr[$wo_row_without_precost[csf('job_no')]]['cons_uom'][$trim_dtla_id]=$cons_uom;

					$style_data_arr[$wo_row_without_precost[csf('job_no')]]['trim_group_from'][$trim_dtla_id]="Booking Without Pre_cost";
				}
				else
				{
					$trim_dtla_id=$wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')];
				}
				$style_data_arr[$wo_row_without_precost[csf('job_no')]]['wo_qnty'][$trim_dtla_id]+=$wo_qnty;
				$style_data_arr[$wo_row_without_precost[csf('job_no')]]['amount'][$trim_dtla_id]+=$amount;
				$style_data_arr[$wo_row_without_precost[csf('job_no')]]['wo_date'][$trim_dtla_id]=$wo_date;
				$style_data_arr[$wo_row_without_precost[csf('job_no')]]['wo_qnty_trim_group'][$wo_row_without_precost[csf('trim_group')]]+=$wo_qnty;


				$style_data_arr2[$wo_row_without_precost[csf('job_no')]]['booking_no'][$trim_dtla_id].=$booking_no.",";
				$style_data_arr[$wo_row_without_precost[csf('job_no')]]['booking_no'][$trim_dtla_id].=$booking_no.",";
				$style_data_arr[$wo_row_without_precost[csf('job_no')]]['supplier_id'][$trim_dtla_id].=$wo_row_without_precost[csf('supplier_id')].",";
				$style_data_arr[$wo_row_without_precost[csf('job_no')]]['conversion_factor_rate'][$trim_dtla_id]=$conversion_factor_rate;
			}
			unset($sql_without_precost);
			//die;
			$receive_qty_data=sql_select("select b.po_breakdown_id, a.item_group_id, b.quantity as quantity, b.order_rate as rate, c.exchange_rate
			from  inv_receive_master c,product_details_master d, inv_trims_entry_dtls a, order_wise_pro_details b
			where a.mst_id=c.id and a.trans_id=b.trans_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $order_cond1  $item_group_cond3  ");


			foreach($receive_qty_data as $row)
			{
				if($style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['trim_group'][$row[csf('item_group_id')]]=="" || $style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['trim_group'][$row[csf('item_group_id')]]==0)
				{
					//echo $row[csf('item_group_id')];
					$cons_uom=$conversion_factor_array[$row[csf('item_group_id')]]['cons_uom'];

					$trim_dtla_id=max($style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['trim_dtla_id'])+1;
					$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['trim_dtla_id'][$trim_dtla_id]=$trim_dtla_id;// for rowspan
					$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['trim_group'][$row[csf('item_group_id')]]=$row[csf('item_group_id')];
					$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][$row[csf('item_group_id')]][$trim_dtla_id]=$trim_dtla_id;// for rowspannn
					$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['trim_group_dtls'][$trim_dtla_id]=$row[csf('item_group_id')];
					$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['cons_uom'][$trim_dtla_id]=$cons_uom;

					$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['trim_group_from'][$trim_dtla_id]="Trim Receive";
				}
				$amount=0;  $amount=($row[csf('quantity')]*$row[csf('rate')]);
				$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['inhouse_qnty'][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['inhouse_amount'][$row[csf('item_group_id')]]+=$amount;//$row[csf('amount')];
			}

			unset($receive_qty_data);
			//die;
			/*$receive_rtn_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id,sum(d.quantity) as quantity
			from inv_issue_master a,inv_transaction b,product_details_master c,order_wise_pro_details d,inv_receive_master e
			where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and e.id=a.received_id and b.transaction_type=3 and a.entry_form=49 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_cond2  group by d.po_breakdown_id, c.item_group_id order by c.item_group_id");*/
			$receive_rtn_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id, d.quantity as quantity, d.order_rate as rate
			from product_details_master c, order_wise_pro_details d
			where d.prod_id=c.id and d.trans_type=3 and d.entry_form=49 and d.status_active=1 and d.is_deleted=0 $order_cond2");
			foreach($receive_rtn_qty_data as $row)
			{
				$receive_rtn_amount=0;
				//$conv_quantity=$row[csf('quantity')]/$conversion_factor_array[$row[csf('item_group_id')]]['con_factor'];
				$conv_quantity=$row[csf('quantity')];
				$receive_rtn_amount=$conv_quantity*$row[csf('rate')];
				$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['receive_rtn_qty'][$row[csf('item_group_id')]]+=$conv_quantity;
				$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['receive_rtn_amount'][$row[csf('item_group_id')]]+=$receive_rtn_amount;
			}
			//echo "<pre>";print_r($style_data_arr);
			unset($receive_rtn_qty_data);

			$transfer_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id, sum((case when d.trans_type=5 then d.quantity else 0 end)-(case when d.trans_type=6 then d.quantity else 0 end)) as quantity ,
			sum(case when d.trans_type=5 then d.quantity else 0 end) as in_qty,
			sum(case when d.trans_type=6 then d.quantity else 0 end) as out_qty,
			sum(case when d.trans_type=5 then (d.quantity*d.order_rate) else 0 end) as in_amount,
			sum(case when d.trans_type=6 then (d.quantity*d.order_rate) else 0 end) as out_amount
			from product_details_master c,order_wise_pro_details d
			where d.prod_id=c.id and d.trans_type in(5,6) and d.entry_form=78 and d.status_active=1 and d.is_deleted=0 $order_cond2 group by d.po_breakdown_id, c.item_group_id");
			foreach($transfer_qty_data as $row)
			{
				$transfe_amount=0;
				$transfe_amount=$row[csf('in_amount')]-$row[csf('out_amount')];
				$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['transfe_qty'][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['transfe_in'][$row[csf('item_group_id')]]+=$row[csf('in_qty')];
				$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['transfe_out'][$row[csf('item_group_id')]]+=$row[csf('out_qty')];

				$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['transfe_amount'][$row[csf('item_group_id')]]+=$transfe_amount;
				$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['transfe_in_amount'][$row[csf('item_group_id')]]+=$row[csf('in_amount')];
				$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['transfe_out_amount'][$row[csf('item_group_id')]]+=$row[csf('out_amount')];
			}
			unset($transfer_qty_data);

			$issue_qty_data=sql_select("select b.po_breakdown_id, p.item_group_id, sum(b.quantity) as quantity, sum(b.quantity*b.order_rate) as amount
			from  inv_issue_master d,product_details_master p,inv_trims_issue_dtls a , order_wise_pro_details b
			where a.mst_id=d.id and a.prod_id=p.id and a.trans_id=b.trans_id and b.trans_type=2 and d.entry_form=25 and b.entry_form=25 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $order_cond1  group by b.po_breakdown_id, p.item_group_id");

			foreach($issue_qty_data as $row)
			{
				$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['issue_qty'][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['issue_amount'][$row[csf('item_group_id')]]+=$row[csf('amount')];
			}

			unset($issue_qty_data);

			$issue_rtn_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id, d.quantity as quantity, (d.quantity*d.order_rate) as amount
			from product_details_master c,order_wise_pro_details d
			where d.prod_id=c.id and d.trans_type=4 and d.entry_form=73 and d.status_active=1 and d.is_deleted=0 $order_cond2");
			foreach($issue_rtn_qty_data as $row)
			{
				$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['issue_rtn_qty'][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['issue_rtn_amount'][$row[csf('item_group_id')]]+=$row[csf('amount')];
			}
			unset($issue_rtn_qty_data);

			$sql_wo_pi=sql_select("select a.pi_number, b.work_order_no, b.work_order_dtls_id from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=4 and a.status_active=1 and a.importer_id=$company_name and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.work_order_dtls_id>0");
			$pi_arr=array();
			foreach($sql_wo_pi as $rowPi)
			{
				if($tem_pi[$rowPi[csf('work_order_no')]][$rowPi[csf('pi_number')]]=="")
				{
					$tem_pi[$rowPi[csf('work_order_no')]][$rowPi[csf('pi_number')]]=$rowPi[csf('pi_number')];
					$pi_arr[$rowPi[csf('work_order_no')]].=$rowPi[csf('pi_number')].'**';
				}
				
			}
			unset($sql_wo_pi);
			$total_pre_costing_value=0;
			$total_wo_value=0;
			$total_left_over_balanc=0;
			$total_issue_amount=0;
			$total_rec_bal_qnty=0;
			$summary_array=array();
			$i=1;
			// echo "<pre>";
			// print_r($style_data_arr);
			foreach($style_data_arr as $key=>$value)
			{
				$rowspan=0;
				$rowspan=count($value['trim_dtla_id']);
				//echo array_sum($value[order_quantity_set]).',';

				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				//echo $value[booking_no][$key_trim1];
				$job=$key;
				$job_data=explode('##',$style_data[$job]['job_data']);

				$style_po_data=explode('***',$style_data_all[$job]);

				$file_no_all=""; $grouping_all=""; $po_no_all=""; $ship_date_all=""; $ship_status_all=""; $po_id_all='';
				foreach($style_po_data as $po_data)
				{
					$ex_po_data=explode('__',$po_data);

					if($file_no_all=="") $file_no_all=$ex_po_data[0]; else $file_no_all.=','.$ex_po_data[0];
					if($grouping_all=="") $grouping_all=$ex_po_data[1]; else $grouping_all.=','.$ex_po_data[1];
					if($po_no_all=="") $po_no_all=$ex_po_data[2]; else $po_no_all.=','.$ex_po_data[2];
					if($ship_date_all=="") $ship_date_all=change_date_format($ex_po_data[3]); else $ship_date_all.=','.change_date_format($ex_po_data[3]);
					if($ship_status_all=="") $ship_status_all=$ex_po_data[4]; else $ship_status_all.=','.$ex_po_data[4];
					if($po_id_all=="") $po_id_all=$ex_po_data[5]; else $po_id_all.=','.$ex_po_data[5];
				}

				$file_no=implode(', ',array_filter(array_unique(explode(',',$file_no_all))));
				$grouping=implode(', ',array_filter(array_unique(explode(',',$grouping_all))));
				$po_no=implode(', ',array_filter(array_unique(explode(',',$po_no_all))));
				$ship_date=implode(', ',array_filter(array_unique(explode(',',$ship_date_all))));
				$ship_status=implode(', ',array_filter(array_unique(explode(',',$ship_status_all))));
				$poId_all=implode(', ',array_filter(array_unique(explode(',',$po_id_all))));
				//print_r($style_po_arr );

				if($rowspan!=0)
				{
				?>
				<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                    <td width="30" title="<? echo $po_qty; ?>" rowspan="<? echo $rowspan; ?>"><p><? echo $i; ?></p></td>
                    <td width="50" rowspan="<? echo $rowspan; ?>"><p style="word-break:break-all"><? echo $buyer_short_name_library[$job_data[0]]; ?></p></td>
                    <td width="50" rowspan="<? echo $rowspan; ?>"><p style="word-break:break-all"><? echo $lib_season_arr[$job_data[4]]; ?></p></td>
                    <td width="100" align="center" rowspan="<? echo $rowspan; ?>"><p style="word-break:break-all"><? echo $job_data[1]; ?></p></td>
                    <td width="100" rowspan="<? echo $rowspan; ?>"><p><? echo $job_data[2]; ?></p></td>
                    <td width="100" rowspan="<? echo $rowspan; ?>"><p style="word-break:break-all"><? echo $grouping; ?></p></td>
                    <td width="100" rowspan="<? echo $rowspan; ?>"><p style="word-break:break-all"><? echo $file_no; ?></p></td>
                    <td width="90" rowspan="<? echo $rowspan; ?>" style="word-break: break-all;"><p>
                    <a href='#report_details' onclick="generate_report('<? echo $company_name; ?>','<? echo $job; ?>', '<? echo $job_data[0]; ?>','<? echo $job_data[2]; ?>','<? echo change_date_format($costing_arr[$job]['costing_date']); ?>','<? echo rtrim($po_id_all,','); ?>','<? echo $costing_arr[$job]['costing_per']; ?>','<? echo $print_button_action;?>');"> <? $po_number=$po_no; $po_id=$poId_all; echo $po_number;
						$order_quantity_set=$style_po_qty_arr[$job]['order_qty_set'];
						$po_qty_pcs=$style_po_qty_arr[$job]['po_qty'];
						?></a></p>

                        </td>
                    <td width="80" align="right" rowspan="<? echo $rowspan; ?>"><p><a href='#report_details' onclick="order_qty_popup('<? echo $company_name; ?>','<? echo $job; ?>','<? echo $po_id; ?>', '<? echo $job_data[0]; ?>', <? echo $txt_date_from; ?>, <? echo $txt_date_to; ?> ,'order_qty_data');"><? echo number_format($order_quantity_set,0,'.',''); ?></a></p></td>

                    <td width="50" align="center" rowspan="<? echo $rowspan; ?>"><p><? echo $unit_of_measurement[$job_data[3]]; ?></p></td>
                    <td width="80" align="right" rowspan="<? echo $rowspan; ?>"><p><? echo number_format($po_qty_pcs,0,'.',''); ?></p></td>
                    <td width="80" align="center" rowspan="<? echo $rowspan; ?>"><p style="word-break:break-all"><? $pub_shipment_date=$ship_date; echo $pub_shipment_date; ?></p></td>
					<?
                    //print_r( $value[trim_group]);
                    foreach($value['trim_group'] as $key_trim=>$value_trim)
                    {
						$summary_array['trim_group'][$key_trim]=$key_trim;
						$gg=1;
						foreach($value[$key_trim] as $key_trim1=>$value_trim1)
						{
							$rowspannn=count($value[$key_trim]);
							if($gg==1)
							{
								$booking_no_arr=array_unique(explode(',',$style_data_arr2[$job]['booking_no'][$key_trim1]));
								//$booking_no_arr=$style_data_arr2[$job][booking_no][$key_trim1];
								?>
                                <td width="100" title="<? echo $value['trim_group_from'][$key_trim1]; ?>"><p><? echo $item_library[$value['trim_group_dtls'][$key_trim1]]; ?></p></td>
                                <td width="140"><p style="word-break:break-all"><? echo $value['description'][$key_trim1]; ?></p></td>
								<td width="100"><p style="word-break:break-all"><? echo $value['remark'][$key_trim1]; ?></p></td>
								<td width="100"><p style="word-break:break-all"><? echo $value['brand_sup_ref'][$key_trim1]; ?></p></td>
                                <td width="100"><p style="word-break:break-all"><? echo $commission_particulars[$value['source_id'][$key_trim1]]; ?></p></td>
                                <td width="60" align="center"><p><? if($value['apvl_req'][$key_trim1]==1) echo "Yes"; else echo ""; ?></p></td>
                                <td width="80" align="center"><p><?
                                if($value['apvl_req'][$key_trim1]==1)
                                {
									$app_status=$app_status_arr[$job][$value['trim_group_dtls'][$key_trim1]];
									$approved_status=$approval_status[$app_status];
									$summary_array['item_app'][$key_trim][all]+=1;
									if($app_status==3)
									{
										$summary_array['item_app'][$key_trim][app]+=1;
									}
                                }
                                else
                                {
                                	$approved_status="";
                                } echo $approved_status; ?></p></td>

                                <td width="100" align="center"><p><? $insert_date=explode(" ",$value['insert_date'][$key_trim1]); echo change_date_format($insert_date[0],'','',''); ?></p></td>
                                <td width="80" align="right"><?=number_format($value['avg_cons'][$key_trim1],4); ?></td>
                                <?
								$booking_no_arr=array_unique(explode(',',rtrim($value['booking_no'][$key_trim1],",")));

                                $main_booking_no_large_data="";
                                foreach($booking_no_arr as $booking_no1)
                                {
                                	if($main_booking_no_large_data=="") $main_booking_no_large_data=$booking_no1; else $main_booking_no_large_data.=",".$booking_no1;
                                }
								$country_id=implode(',',array_filter(array_unique(explode(',',$value['country_id'][$key_trim1]))));
								?>
                                <td width="100" align="right"><p><a href='#report_details' onclick="order_req_qty_popup('<? echo $company_name; ?>','<? echo $job; ?>','<? echo $po_id; ?>', '<? echo $job_data[0]; ?>','<? echo $value['rate'][$key_trim1]; ?>','<? echo $value['trim_group_dtls'][$key_trim1];?>' ,'<? echo $main_booking_no_large_data;?>','<? echo $value['description'][$key_trim1] ;?>','<? echo rtrim($country_id,",");?>','<? echo $value['trim_dtla_id'][$key_trim1];?>','<? echo $start_date ?>','<? echo $end_date ?>','order_req_qty_data');">
                                <?
								//$req_qnty=number_format($value['req_qnty'][$key_trim1],2,'.','');
								$trim_req_value=$trim_amount[$key][$key_trim1];
								$trim_req_qnty=$trim_qty[$key][$key_trim1];
								$req_qty=number_format($trim_req_qnty,2,'.',''); echo $req_qty; $summary_array['req_qnty'][$key_trim]+=$req_qty;//$value['req_qnty'][$key_trim1]; ?></a></p></td>

								<td width="100" align="right"><p><?
								//echo number_format($value['req_value'][$key_trim1],2);
								 echo number_format($trim_req_value,2); $total_pre_costing_value+=$trim_req_value; ?></p></td>
								<?
								$wo_qnty=number_format($value['wo_qnty'][$key_trim1],2,'.','');
								if(($value['conversion_factor_rate'][$key_trim1]*$wo_qnty)==$req_qty) $color_wo="green";
								else if(($value['conversion_factor_rate'][$key_trim1]*$wo_qnty) > $req_qty) $color_wo="red";
								else if(($value['conversion_factor_rate'][$key_trim1]*$wo_qnty) < $req_qty ) $color_wo="yellow";
								else $color_wo="";

								$supplier_name_string="";
								$supplier_id_arr=array_unique(explode(',',rtrim($value['supplier_id'][$key_trim1],",")));
								foreach($supplier_id_arr as $supplier_id_arr_key=>$supplier_id_arr_value)
								{
									$ex_sup_data=explode("**",$supplier_id_arr_value);
									if($ex_sup_data[1]==3 || $ex_sup_data[1]==5) $suplier_name_arr=$company_library; else $suplier_name_arr=$lib_supplier_arr;
									$supplier_name_string.=$suplier_name_arr[$ex_sup_data[0]].",";
								}

								/*if($value['pay_mode'][$key_trim1]==3 || $value['pay_mode'][$key_trim1]==5)
								{
									$supplier_name_string=$company_library[$value['supplier_id'][$key_trim1]];
								}
								else
								{
									$supplier_name_string=$lib_supplier_arr[$value['supplier_id'][$key_trim1]];
								}*/

                                ?>
                                <td width="100" align="right" title="<? echo 'conversion_factor='.$value['conversion_factor_rate'][$key_trim1];?>" bgcolor="<? echo $color_wo;?>"><p><a href='#report_details' onclick="openmypage('<? echo $po_id; ?>','<? echo $value['trim_group_dtls'][$key_trim1]; ?>','<? echo $job; ?>','<? echo $main_booking_no_large_data;?>','<? echo $value['trim_dtla_id'][$key_trim1];?>','booking_info');">
                                	<? echo number_format($value['wo_qnty'][$key_trim1],2,'.',''); $summary_array['wo_qnty'][$key_trim]+=$value['wo_qnty'][$key_trim1]; //$total_pre_costing_value+=$value['wo_qnty'][$key_trim1]; ?></a></p></td>
                                <td width="60" align="center"><p>
									<? echo $unit_of_measurement[$item_arr[$value['trim_group_dtls'][$key_trim1]]['order_uom']];
                                    $summary_array['cons_uom'][$key_trim]= $item_arr[$value['trim_group_dtls'][$key_trim1]]['order_uom']; ?></p></td>
                                <td width="100" align="right" title="<? echo number_format($value['rate'][$key_trim1],2,'.',''); ?>"><p>
                                	<? echo number_format($value['amount'][$key_trim1],2,'.',''); $total_wo_value+=$value['amount'][$key_trim1]; ?></p></td>

                                <td width="150" align="left" style="word-break: break-all;"><p><? echo rtrim($supplier_name_string,','); ?></p></td>
                                <td width="70" align="center" title="<? echo change_date_format($value['wo_date'][$key_trim1])."===". rtrim($value['booking_no'][$key_trim1]);?>"><p>
                                <? $tot=change_date_format($insert_date[0]);
                                if($value['wo_qnty'][$key_trim1]<=0 ) $daysOnHand = datediff('d',$tot,$today);
                                else
                                {
									$wo_date=$value['wo_date'][$key_trim1];
									$wo_date=change_date_format($wo_date);
									$daysOnHand = datediff('d',$tot,$wo_date);;
                                }
                                echo $daysOnHand; ?></p></td>
								<?
								$transfe_out=number_format($value['transfe_out'][$key_trim],2,'.','');
								$transfe_in=number_format($value['transfe_in'][$key_trim],2,'.','');
								$transfe_in_out=$transfe_in.' & '.$transfe_out;

								$transfe_out_amt=number_format($value['transfe_out_amount'][$key_trim],2,'.','');
								$transfe_in_amt=number_format($value['transfe_in_amount'][$key_trim],2,'.','');
								$transfe_in_out_amt=$transfe_in_amt.' & '.$transfe_out_amt;

                                $inhouse_qnty=($value['inhouse_qnty'][$key_trim]+$value['transfe_qty'][$key_trim])-$value['receive_rtn_qty'][$key_trim];
								$inhouse_amt=($value['inhouse_amount'][$key_trim]+$value['transfe_amount'][$key_trim])-$value['receive_rtn_amount'][$key_trim];

								$total_inhouse_value+=$inhouse_amt;
                                $balance=$value['wo_qnty_trim_group'][$key_trim]-$inhouse_qnty;
                                $conv_rate=$conversion_factor_array[$value['trim_group_dtls'][$key_trim1]]['con_factor'];
                                $issue_qnty=$value['issue_qty'][$key_trim]-$value['issue_rtn_qty'][$key_trim];
								$issue_amt=$value['issue_amount'][$key_trim]-$value['issue_rtn_amount'][$key_trim];
                                //$tot_issue=$issue_qnty/$conv_rate;
								$tot_issue=$issue_qnty;
                                $left_overqty=$inhouse_qnty-$tot_issue;
								$left_overamt=$inhouse_amt-$issue_amt;
                                $summary_array['inhouse_qnty'][$key_trim]+=$inhouse_qnty;
                                $summary_array['inhouse_qnty_bl'][$key_trim]+=$balance;
                                $summary_array['issue_qty'][$key_trim]+=$tot_issue;
                                $summary_array['left_overqty'][$key_trim]+=$left_overqty;//transfe_amount

								$booking_no_arr=array_unique(explode(',',rtrim($value['booking_no'][$key_trim1],",")));
								$all_pi_no="";
								foreach($booking_no_arr as $book_no)
								{
									// $all_pi_no.=chop($pi_arr[$book_no],"**").",";
									$all_pi_no_arr=explode("**",chop($pi_arr[$book_no],'**'));
									foreach($all_pi_no_arr as $val)
									{
										$all_pi_no.=$val.",";
									}									
								}
								$btb_lc_no_arr=array_unique(explode(",",chop($all_pi_no,',')));
								$btb_lc_no=$btb_lc_date="";
								foreach($btb_lc_no_arr as $val)
								{
									if($pi_wise_btb_arr[$val]['btb_lc_no'])
									{
										$btb_lc_no.=$pi_wise_btb_arr[$val]['btb_lc_no'].",";
										$btb_lc_date.=$pi_wise_btb_arr[$val]['btb_lc_date'].",";
									}
								}
                                ?>
                                <td  width="70" style="word-break: break-all;"><p><? echo chop($all_pi_no,","); ?> </p></td>
								<td width="100" align="right" style="word-break: break-all;"><?=implode(", ",array_unique(explode(",",chop($btb_lc_no,','))));?></td>
								<td width="100" align="right"><?=implode(", ",array_unique(explode(",",chop($btb_lc_date,','))));;?></td>
                                <td width="90" align="right" title="<? echo "Inhouse-Qty: ".number_format($value['inhouse_qnty'][$key_trim]-$value['receive_rtn_qty'][$key_trim],2,'.','')."\n Transfer In & Out Qty: ".$transfe_in_out."\n Return Qty: ".number_format($value['receive_rtn_qty'][$key_trim],2,'.',''); ?>" rowspan="<? echo $rowspannn; ?>"><p><a href='#report_details' onclick="openmypage_inhouse('<? echo $po_id; ?>','<? echo $value['trim_group_dtls'][$key_trim1]; ?>','booking_inhouse_info');"><? echo number_format($inhouse_qnty,2,'.',''); ?></a></p></td>
                                <td width="90" align="right" title="<? echo "Inhouse-Amt: ".number_format($value['inhouse_amount'][$key_trim]-$value['receive_rtn_qty'][$key_trim],2,'.','')."\n Transfer In & Out Amt: ".$transfe_in_out_amt."\n Return Amt: ".number_format($value['receive_rtn_amount'][$key_trim],2,'.',''); ?>" rowspan="<? echo $rowspannn; ?>"><p><? echo number_format($inhouse_amt,2,'.',''); ?></p></td>
                                <td width="90" align="right" rowspan="<? echo $rowspannn; ?>"><p><? echo number_format($balance,2,'.',''); $total_rec_bal_qnty+=$balance;?></p></td>
                                <td width="90" align="right" title="<? echo "issue qnty:".number_format($value['issue_qty'][$key_trim],2,'.','')."\n Issue Return Qty: ".number_format($value['issue_rtn_qty'][$key_trim],2,'.','');  ?>" rowspan="<? echo $rowspannn; ?>"><p><a href='#report_details' onclick="openmypage_issue('<? echo $po_id; ?>','<? echo $value['trim_group_dtls'][$key_trim1]; ?>','booking_issue_info');"><? echo number_format($tot_issue,2,'.',''); ?></a></p></td>
                                 <td width="90" align="right" title="<? echo "Issue Amt:".number_format($value['issue_amount'][$key_trim],2,'.','')."\n Issue Return Amt: ".number_format($value['issue_rtn_amount'][$key_trim],2,'.','');  ?>" rowspan="<? echo $rowspannn; ?>"><p><? echo number_format($issue_amt,2,'.',''); $total_issue_amount+=$issue_amt; ?></a></p></td>
                                <td width="90" align="right" rowspan="<? echo $rowspannn; ?>"><p><? echo number_format($left_overqty,2,'.',''); ?></p></td>
                                <td width="100" align="right" rowspan="<? echo $rowspannn; ?>"><p><? echo number_format($left_overamt,2,'.',''); $total_left_over_balanc+=$left_overamt; ?></p></td>
                                <?
							}
							else
							{
								?>
								<td width="100" title="<? echo $value['trim_group_from'][$key_trim1]; ?>"><p><? echo $item_library[$value['trim_group_dtls'][$key_trim1]]; ?></p></td>
                                <td width="140"><p style="word-break:break-all"><? echo $value['description'][$key_trim1]; ?></p></td>
								<td width="100"><p style="word-break:break-all"><? echo $value['remark'][$key_trim1]; ?></p></td>
                                <td width="100"><p style="word-break:break-all"><? echo $value['brand_sup_ref'][$key_trim1]; ?></p></td>
								<td width="100"><p style="word-break:break-all"><? echo $commission_particulars[$value['source_id'][$key_trim1]]; ?></p></td>
								
          
                                <td width="60" align="center"><p><? if($value['apvl_req'][$key_trim1]==1) echo "Yes"; else echo ""; ?></p></td>
                                <td width="80" align="center"><p><?
                                if($value['apvl_req'][$key_trim1]==1)
                                {
									$app_status=$app_status_arr[$job][$value['trim_group_dtls'][$key_trim1]];
									$approved_status=$approval_status[$app_status];
									$summary_array['item_app'][$key_trim][all]+=1;
									if($app_status==3)
									{
										$summary_array['item_app'][$key_trim][app]+=1;
									}
                                }
                                else
                                {
                                	$approved_status="";
                                }
                                echo $approved_status; ?></p></td>

                                <td width="100" align="center"><p><? $insert_date=explode(" ",$value['insert_date'][$key_trim1]); echo change_date_format($insert_date[0],'','',''); ?></p></td>
                                <td width="80" align="right"><?=number_format($value['avg_cons'][$key_trim1],4); ?></td>
                                <td width="100" align="right"><p><a href='#report_details' onclick="order_req_qty_popup('<? echo $company_name; ?>','<? echo $job; ?>','<? echo $po_id; ?>', '<? echo $job_data[0]; ?>','<? echo $value['rate'][$key_trim1]; ?>','<? echo $value['trim_group_dtls'][$key_trim1];?>' ,'<? echo $value['booking_no'][$key_trim1];?>','<? echo $value['description'][$key_trim1] ;?>','<? echo rtrim($value['country_id'][$key_trim1],",");?>','<? echo $value['trim_dtla_id'][$key_trim1];?>','<? echo $start_date ?>','<? echo $end_date ?>','order_req_qty_data');">
									<?
									$trim_req_value=$trim_amount[$key][$key_trim1];
									$trim_req_qnty=$trim_qty[$key][$key_trim1];
								//$req_qty=number_format($value['req_qnty'][$key_trim1],2,'.','');
									$req_qty=number_format($trim_req_qnty,2,'.',''); echo $req_qty;
                                    $summary_array['req_qnty'][$key_trim]+=$req_qty;
                                    ?></a></p></td>

								<td width="100" align="right"><p><? echo number_format($trim_req_value,2); $total_pre_costing_value+=$trim_req_value; ?></p></td>
								<?
                                $wo_qnty=number_format($value['wo_qnty'][$key_trim1],2);

                                /*if($wo_qnty > $req_qty) $color_wo="red";
                                else if($wo_qnty < $req_qty ) $color_wo="yellow";
                                else $color_wo="";*/

								if(($value['conversion_factor_rate'][$key_trim1]*$wo_qnty)==$req_qty) $color_wo="green";
								else if(($value['conversion_factor_rate'][$key_trim1]*$wo_qnty) > $req_qty) $color_wo="red";
								else if(($value['conversion_factor_rate'][$key_trim1]*$wo_qnty) < $req_qty ) $color_wo="yellow";
								else $color_wo="";

								$supplier_name_string="";
								$supplier_id_arr=array_unique(explode(',',rtrim($value['supplier_id'][$key_trim1],",")));
								foreach($supplier_id_arr as $supplier_id_arr_key=>$supplier_id_arr_value)
								{
									$ex_sup_data=explode("**",$supplier_id_arr_value);
									if($ex_sup_data[1]==3 || $ex_sup_data[1]==5) $suplier_name_arr=$company_library; else $suplier_name_arr=$lib_supplier_arr;
									$supplier_name_string.=$suplier_name_arr[$ex_sup_data[0]].",";
								}

								/*if($value['pay_mode'][$key_trim1]==3 || $value['pay_mode'][$key_trim1]==5)
								{
									$supplier_name_string=$company_library[$value['supplier_id'][$key_trim1]];
								}
								else
								{
									$supplier_name_string=$lib_supplier_arr[$value['supplier_id'][$key_trim1]];
								}*/

								$booking_no_arr=array_unique(explode(',',rtrim($value['booking_no'][$key_trim1],",")));
								$main_booking_no_large_data="";
								foreach($booking_no_arr as $booking_no1)
								{
									if($main_booking_no_large_data=="") $main_booking_no_large_data=$booking_no1; else $main_booking_no_large_data.=",".$booking_no1;
								}
								?>
                                <td width="90" align="right" title="<? echo 'conversion_factor='.$value['conversion_factor_rate'][$key_trim1];?>" bgcolor="<? echo $color_wo;?>"><p><a href='#report_details' onclick="openmypage('<? echo $po_id; ?>','<? echo $value['trim_group_dtls'][$key_trim1]; ?>','<? echo $job; ?>','<? echo $main_booking_no_large_data;?>','<? echo $value['trim_dtla_id'][$key_trim1];?>','booking_info');">
                                	<? echo number_format($value['wo_qnty'][$key_trim1],2,'.',''); $summary_array['wo_qnty'][$key_trim]+=$value['wo_qnty'][$key_trim1]; ?></a></p></td>

                                <td width="60" align="center"><p><? echo $unit_of_measurement[$item_arr[$value['trim_group_dtls'][$key_trim1]]['order_uom']];
                               		$summary_array['cons_uom'][$key_trim]= $item_arr[$value['trim_group_dtls'][$key_trim1]]['order_uom']; ?></p></td>

                                <td width="100" align="right" title="<? echo number_format($value['rate'][$key_trim1],2,'.',''); ?>"><p><? echo number_format($value['amount'][$key_trim1],2,'.',''); $total_wo_value+=$value['amount'][$key_trim1]; ?></p></td>
                                <td width="150" align="left" style="word-break: break-all;"><p><? echo rtrim($supplier_name_string,','); ?></p></td>
                                <td  width="70"align="center" title="<? echo change_date_format($value[wo_date][$key_trim1])."===". rtrim($value['booking_no'][$key_trim1]);?>"><p>
                                <? $tot=change_date_format($insert_date[0]);
                                if($value['wo_qnty'][$key_trim1]<=0 )
                                {
                                	$daysOnHand = datediff('d',$tot,$today);
                                }
                                else
                                {
									$wo_date=$value['wo_date'][$key_trim1];
									$wo_date=change_date_format($wo_date);
									$daysOnHand = datediff('d',$tot,$wo_date);;
                                }
                                echo $daysOnHand; ?></p></td>
                                <?
                                $booking_no_arr=array_unique(explode(',',rtrim($value['booking_no'][$key_trim1],",")));
								$all_pi_no="";
								foreach($booking_no_arr as $book_no)
								{
									// $all_pi_no.=chop($pi_arr[$book_no],"**").",";
									$all_pi_no_arr=explode("**",chop($pi_arr[$book_no],'**'));
									foreach($all_pi_no_arr as $val)
									{
										$all_pi_no.=$val.",";
									}
								}
								$btb_lc_no_arr=array_unique(explode(",",chop($all_pi_no,',')));
								$btb_lc_no=$btb_lc_date="";
								foreach($btb_lc_no_arr as $val)
								{
									if($pi_wise_btb_arr[$val]['btb_lc_no'])
									{
										$btb_lc_no.=$pi_wise_btb_arr[$val]['btb_lc_no'].",";
										$btb_lc_date.=$pi_wise_btb_arr[$val]['btb_lc_date'].",";
									}
								}
                                ?>
                                <td  width="70" style="word-break: break-all;"><p><? echo chop($all_pi_no,","); ?> </p></td>
								<td width="100" align="right" style="word-break: break-all;"><?=implode(", ",array_unique(explode(",",chop($btb_lc_no,','))));?></td>
								<td width="100" align="right"><?=implode(", ",array_unique(explode(",",chop($btb_lc_date,','))));;?></td>
							<?
                            } 
							
							
									
									foreach($trims_tna as $vid=>$key)
									{
									 if( $key==32 || $key==71  || $key==300  || $key==8 || $key==13 || $key==29 || $key==279  || $key==308 || $key==11 || $key==307 || $key==278  || $key==310 || $key==301 || $key==309 || $key==12 || $key==24){
									
									$plan_date=$job_wise_tna_data_arr[$job_no][$key]['plan_end_date'];
								$actual_date=$job_wise_tna_data_arr[$job_no][$key]['actual_finish_date'];
								
								if(strtotime($actual_date)>0){
									$diff = abs(strtotime($plan_date) - strtotime($actual_date));
									$delay = floor($diff / (60*60*24));	
									}else{
										$delay="";
									}
									
									
								 	if($key==308 || $key==307 || $key==278 || $key==279 || $key==309 || $key==300 || $key==301 || $key==310){
										if($value['source_id'][$key_trim1]==1 && ( $key==278 || $key==279 || $key==300 || $key==301)){
											$actual_finish_date=$job_wise_tna_data_arr[$job_no][$key]['actual_finish_date'];
											$plan_end_date=$job_wise_tna_data_arr[$job_no][$key]['plan_end_date'];
										}
										else if($value['source_id'][$key_trim1]==2 && ($key==308 || $key==307 || $key==309 || $key==310)){
											$actual_finish_date=$job_wise_tna_data_arr[$job_no][$key]['actual_finish_date'];
											$plan_end_date=$job_wise_tna_data_arr[$job_no][$key]['plan_end_date'];
										}
										else{
											$actual_finish_date='';
											$plan_end_date='';
											$delay="";
										}
									}
									else{
										$actual_finish_date=$job_wise_tna_data_arr[$job_no][$key]['actual_finish_date'];
										$plan_end_date=$job_wise_tna_data_arr[$job_no][$key]['plan_end_date'];
									}									
							
								 ?>
									<td width="100" align="right" id="" title=<?=$key;?>><?=$plan_end_date;?></td>
									<td width="100" align="right" id=""><?=$actual_finish_date;?></td>
									<td width="100" align="right" id=""><? 	echo $delay;?></td>

								<?}}?>
									
						</tr>
						<? $gg++;
                        }// end  foreach($value[$key_trim] as $key_trim1=>$value_trim1)
					}
					$i++;
				}
			}//end
			?>
			</table>
            </div>
			<table class="rpt_table" width="<?=$tbl_width3;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
                <tfoot>
                    <th width="30">&nbsp;</th>
                    <th width="50">&nbsp;</th>
                    <th width="50">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>

                    <th width="90">&nbsp;</th>
                    <th width="80" id="total_order_qnty">&nbsp;<? //echo number_format($total_order_qnty,0); ?></th>
                    <th width="50">&nbsp;</th>
                    <th width="80" id="total_order_qnty_in_pcs">&nbsp;<? //echo number_format($total_order_qnty_in_pcs,0); ?></th>
                    <th width="80">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="140">&nbsp;</th>

                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>

                    <th width="60">&nbsp;</th>
					<th width="80">&nbsp;</th>
					<th width="100">&nbsp;</th>

                    <th width="80">&nbsp;</th>
                    <th width="100" id="value_req_qnty"><? //echo number_format($total_req_qnty,2); ?>&nbsp;</th>
                    <th width="100" id="value_pre_costing"><? echo number_format($total_pre_costing_value,2); ?></th>
                    <th width="90"><? //echo number_format($total_wo_qnty,2); ?>&nbsp;</th>
                    <th width="60">&nbsp;</th>

                    <th width="100"id="value_wo_qty"><? echo number_format($total_wo_value,2); ?></th>
                    <th width="150">&nbsp;</th>
                    <th width="70"><p><? //echo number_format($req_value,2,'.',''); ?>&nbsp;</p></th>
                    <th width="70"><p><? //echo number_format($req_value,2,'.',''); ?>&nbsp;</p></th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
                    <th width="90" id="value_in_qty"><? //echo number_format($total_in_qnty,2); ?></th>
                    <th width="90" id="value_in_amount"><? echo number_format($total_inhouse_value,2); ?></th>
                    <th width="90" id="value_rec_qty"><? echo number_format($total_rec_bal_qnty,2); ?></th>
                    <th width="90" id="value_issue_qty"><? //echo number_format($total_issue_qnty,2); ?></th>
                    <th width="90" id="value_issue_amount"><? echo number_format($total_issue_amount,2); ?></th>
                    <th width="90" id="value_leftover_qty"><? //echo number_format($total_leftover_qnty,2); ?>&nbsp;</th>
                    <th width="100" id="value_leftover_amount"> <? echo number_format($total_left_over_balanc,2); ?>&nbsp;</th>
					<?php
					foreach($trims_tna as $vid=>$key)
					{
					 if( $key==32 || $key==71   || $key==300  || $key==8 || $key==13 || $key==29 || $key==279  || $key==308 || $key==11 || $key==307 || $key==278  || $key==310 || $key==301 || $key==309 || $key==12 || $key==24){

						?>
						  <th width="100">&nbsp;</th>
						  <th width="100">&nbsp;</th>
						  <th width="100">&nbsp;</th>
						  <?
						}
					}?>
                </tfoot>
			</table>

			<table>
				<tr><td height="15"></td></tr>
			</table>
		
        </fieldset>
      
		<?
        }
	}
	?>
    
    </div>
    <?
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename****$tot_rows****1";
	exit();
}




if($action=='fin_issue_popup')
{
	echo load_html_head_contents("Issue info", "../../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_id."*".$body_id;die;

	//echo $ratio;die;
	$user_library=return_library_array( "select id,user_name from user_passwd", "id", "user_name"  );
	$style_ref_library=return_library_array( "select job_no,style_ref_no from wo_po_details_master", "job_no", "style_ref_no"  );

	?>
	<script>

		function window_close()
		{
			parent.emailwindow.hide();
		}

	</script>
	<script>
		function print_window()
		{
			$("#table_body_popup tr:first").hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
			$("#table_body_popup tr:first").show();
		}	
	</script>	
    
 <div  style="width:1040px;" align="center">
        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
  </div>
    <div  id="report_div" style="width:100%;" align="center">
   
       <table width="1040" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr>
                    <th colspan="13">Issue Details</th>
                </tr>
                <tr>
                    <th width="20">SL</th>
                    <th width="60">Prod. ID</th>
                    <th width="110">Issue No</th>
                    <th width="70">Issue. Date</th>
                    <th width="100">Job No</th>
                    <th width="100">Style No</th>
                    <th width="100">Body Part</th>
                    <th width="150">Fabric Desc.</th>
                    <th width="70">Issue. Qty</th>
                    <th width="50">Rate</th>
                    <th width="70">Amount</th>
                    <th width="70">Uom</th> 
                    <th width="">Insert By</th>
                    
                   
                </tr>
            </thead>
            <tbody  id="table_body_popup">
            <?
	$issueprodKnitDataArr="select c.issue_number,c.issue_date,c.inserted_by,a.po_breakdown_id as po_id,b.prod_id,d.detarmination_id as deter_id,b.cons_rate,b.body_part_id,d.unit_of_measure as order_uom,a.color_id,
	(CASE WHEN a.entry_form =18 or a.entry_form =19 THEN a.quantity ELSE 0 END) AS knit_qnty_issue
	from order_wise_pro_details a, inv_transaction b, inv_issue_master c,product_details_master d where a.trans_id=b.id and b.mst_id=c.id and d.id=b.prod_id and a.prod_id=d.id and c.item_category in (2,3) and (a.entry_form =18 or a.entry_form =19) and (c.entry_form =18 or c.entry_form =19) and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.po_breakdown_id in ($po_id) and a.color_id in ($color_id) and b.body_part_id in($body_id) and d.detarmination_id in($deterId)";// and c.receive_basis<>9
	$issue_prodKnitDataArr=sql_select($issueprodKnitDataArr);
	$issue_kniting_prod_arr=array();
	foreach($issue_prodKnitDataArr as $row)
	{
	$issue_kniting_prod_arr[$row[csf("issue_number")]][$row[csf("prod_id")]][$row[csf("body_part_id")]]["knit_qnty_issue"]+=$row[csf("knit_qnty_issue")];
	$issue_kniting_prod_arr[$row[csf("issue_number")]][$row[csf("prod_id")]][$row[csf("body_part_id")]]["knit_qnty_issue_amt"]+=$row[csf("knit_qnty_issue")]*$row[csf("cons_rate")];
	$issue_kniting_prod_arr[$row[csf("issue_number")]][$row[csf("prod_id")]][$row[csf("body_part_id")]]["issue_date"]=$row[csf("issue_date")];
	$issue_kniting_prod_arr[$row[csf("issue_number")]][$row[csf("prod_id")]][$row[csf("body_part_id")]]["order_uom"]=$row[csf("order_uom")];
	$issue_kniting_prod_arr[$row[csf("issue_number")]][$row[csf("prod_id")]][$row[csf("body_part_id")]]["inserted_by"]=$row[csf("inserted_by")];
	$prod_id_arr[$row[csf("prod_id")]]=$row[csf("prod_id")];
	}
	$desc_arr=return_library_array( "select id, product_name_details from product_details_master where id in(".implode(",",$prod_id_arr).")",'id','product_name_details');
		  $i=1;$tot_knit_qnty_issue_amt=$tot_knit_qnty_issue=0;
			foreach($issue_kniting_prod_arr as $issueNo=>$issue_Data)
			{
			 foreach($issue_Data as $prod_id=>$prod_Data)
			 {
			  foreach($prod_Data as $bodyId=>$row)
			  {
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
			?>
            	<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trpo_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trpo_<? echo $i; ?>">
                    <td><p><? echo $i; ?>&nbsp;</p></td>
                    <td><p><? echo $prod_id; ?>&nbsp;</p></td>
                    <td><p><? echo $issueNo; ?>&nbsp;</p></td>
                    <td><p><? echo change_date_format($row[('issue_date')]); ?>&nbsp;</p></td>
                    <td><p><? echo $jobNo; ?>&nbsp;</p></td>
                    <td><p><? echo $style_ref_library[$jobNo]; ?>&nbsp;</p></td>
                    <td><p><? echo $body_part[$bodyId]; ?>&nbsp;</p></td>
                    <td><p><? echo $desc_arr[$prod_id]; ?>&nbsp;</p></td>
                    <td align="right"><? echo number_format($row[('knit_qnty_issue')],2); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[('knit_qnty_issue_amt')]/$row[('knit_qnty_issue')],2); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[('knit_qnty_issue_amt')],2);///unit_of_measurement ?>&nbsp;</td>
                     <td align="center"><? echo $unit_of_measurement[$row[('order_uom')]]; ?>&nbsp;</td>
                    <td align="center"><? echo $user_library[$row['inserted_by']]; ?>&nbsp;</td>
                  
                    
                </tr>
            <?
				$tot_knit_qnty_issue+=$row[('knit_qnty_issue')];
				$tot_knit_qnty_issue_amt+=$row[('knit_qnty_issue_amt')];
				$i++;
			  }
			 }
			}
			?>
            </tbody>
            <tfoot>
                <tr>
                    <th align="right">&nbsp;</th>
                     <th align="right">&nbsp;</th>
                    <th align="right">&nbsp;</th>
                     <th align="right">&nbsp;</th>
                     <th align="right">&nbsp;</th>
                     <th align="right">&nbsp;</th>
                     <th align="right"></th>
                    <th align="right">Total</th>
                    <th align="right"><? echo number_format($tot_knit_qnty_issue,2,'.',''); ?></th>
                    <th align="right"></th>
                    <th align="right"><? echo number_format($tot_knit_qnty_issue_amt,2,'.',''); ?>&nbsp;</th>
                    <th align="right"><? //echo number_format($tot_recv_bl_qnty,0,'.',''); ?>&nbsp;</th>
                    <th align="right"><? //echo number_format($tot_recv_bl_qnty,0,'.',''); ?>&nbsp;</th>
                </tr>
             </tfoot>
        </table>
          <script>   setFilterGrid("table_body_popup",-1);</script>
        </div>
	<?

	exit();
}
if($action=='fin_recv_popup')
{
	echo load_html_head_contents("Recv Details info", "../../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_id."*".$body_id;die;

	//echo $ratio;die;

	?>
	<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}

	</script>
	<script>
		function print_window()
		{
			$("#table_body_popup tr:first").hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
			$("#table_body_popup tr:first").show();
		}	
	</script>	
    
 <div  style="width:1150px;" align="center">
      <input  type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
  </div>
    <div  id="report_div" style="100%;" align="center">
   
       <table width="1150" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr>
                    <th colspan="15">Recevied Details</th>
                </tr>
                <tr>
                    <th width="20">SL</th>
                    <th width="60">Prod. ID</th>
                    <th width="110">MRR No</th>
                    <th width="70">Challan No</th>
                    <th width="70">Recv. Date</th>
                    <th width="100">WO No</th>
                    <th width="100">PI No</th>
                    <th width="100">Body Part</th>
                    <th width="150">Fabric Desc.</th>
                    <th width="70">Recv. Qty</th>
                    <th width="70">Rate</th>
                     <th width="70">Amount</th>
                    <th width="40">Uom</th> 
                    <th width="100">Supplier</th> 
                    <th width="">Insert By</th>
                    
                   
                </tr>
            </thead>
            <tbody  id="table_body_popup">
            <?
	


	$prodKnitDataArr=sql_select("select c.recv_number,c.challan_no,c.supplier_id,c.booking_no,c.booking_id,c.receive_date,c.inserted_by,a.po_breakdown_id as po_id,b.uom,b.prod_id,b.fabric_description_id as deter_id,b.rate,b.body_part_id,a.color_id,
	(CASE WHEN a.entry_form = 17 or a.entry_form = 37 or a.entry_form = 225  THEN a.quantity ELSE 0 END) AS knit_qnty_rec
	from order_wise_pro_details a, pro_finish_fabric_rcv_dtls b, inv_receive_master c where a.dtls_id=b.id and b.mst_id=c.id  and c.item_category in (2,3) and (a.entry_form = 17 or a.entry_form = 37 or a.entry_form = 225) and (c.entry_form = 17 or c.entry_form = 37 or c.entry_form = 225) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.po_breakdown_id in ($po_id)
	and a.color_id in ($color_id) and b.body_part_id in($body_id) and b.fabric_description_id in($deterId) ");// and c.receive_basis<>9
	 
	$kniting_prod_arr=array();
	foreach($prodKnitDataArr as $row)
	{
	$kniting_prod_arr[$row[csf("recv_number")]][$row[csf("prod_id")]][$row[csf("body_part_id")]]["knit_qnty_rec"]+=$row[csf("knit_qnty_rec")];
	$kniting_prod_arr[$row[csf("recv_number")]][$row[csf("prod_id")]][$row[csf("body_part_id")]]["knit_qnty_rec_amt"]+=$row[csf("knit_qnty_rec")]*$row[csf("rate")];
	$kniting_prod_arr[$row[csf("recv_number")]][$row[csf("prod_id")]][$row[csf("body_part_id")]]["uom"]=$row[csf("uom")];
	$kniting_prod_arr[$row[csf("recv_number")]][$row[csf("prod_id")]][$row[csf("body_part_id")]]["inserted_by"]=$row[csf("inserted_by")];
	$kniting_prod_arr[$row[csf("recv_number")]][$row[csf("prod_id")]][$row[csf("body_part_id")]]["receive_date"]=$row[csf("receive_date")];
	$kniting_prod_arr[$row[csf("recv_number")]][$row[csf("prod_id")]][$row[csf("body_part_id")]]["challan_no"]=$row[csf("challan_no")];
	$kniting_prod_arr[$row[csf("recv_number")]][$row[csf("prod_id")]][$row[csf("body_part_id")]]["booking_no"]=$row[csf("booking_no")];
	$kniting_prod_arr[$row[csf("recv_number")]][$row[csf("prod_id")]][$row[csf("body_part_id")]]["supplier_id"]=$row[csf("supplier_id")];
	$booking_id_arr[$row[csf("booking_id")]]=$row[csf("booking_id")];
	$prod_id_arr[$row[csf("prod_id")]]=$row[csf("prod_id")];
	}
	$desc_arr=return_library_array( "select id, product_name_details from product_details_master where id in(".implode(",",$prod_id_arr).")",'id','product_name_details');
	//echo "select id, item_description from product_details_master where id in(".implode(",",$prod_id_arr).")";

	/*$issueprodKnitDataArr=sql_select("select a.po_breakdown_id as po_id,d.detarmination_id as deter_id,b.cons_rate,b.body_part_id,a.color_id,
	(CASE WHEN a.entry_form in(19) THEN a.quantity ELSE 0 END) AS knit_qnty_issue
	from order_wise_pro_details a, inv_transaction b, inv_issue_master c,product_details_master d where a.trans_id=b.id and b.mst_id=c.id and d.id=b.prod_id and a.prod_id=d.id and c.item_category=3 and a.entry_form in(19) and c.entry_form in(19) and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 $po_cond_for_in2 ");// and c.receive_basis<>9
	$issue_kniting_prod_arr=array();
	foreach($issueprodKnitDataArr as $row)
	{
	$issue_kniting_prod_arr[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("color_id")]]["knit_qnty_issue"]+=$row[csf("knit_qnty_issue")];
	$issue_kniting_prod_arr[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("deter_id")]][$row[csf("color_id")]]["knit_qnty_issue_amt"]+=$row[csf("knit_qnty_issue")]*$row[csf("cons_rate")];
	}
	*/
	$sql="select b.pi_id,a.id,a.item_category_id,a.pi_number,a.pi_date,a.importer_id, b.work_order_no,b.work_order_id,b.item_group,b.item_prod_id,
	b.determination_id
	from com_pi_master_details  a,  com_pi_item_details b
	where a.id=b.pi_id   and a.status_active=1 and a.is_deleted=0 and b.status_active=1
	and b.is_deleted=0 and b.work_order_id in(".implode(",",$booking_id_arr).")";
	  //echo $sql ; // die;
	$sql_result = sql_select($sql);
	foreach($sql_result as $row)
	{
		$piDataArr[$row[csf("work_order_no")]].=$row[csf("pi_number")].',';
	}
	
	 
	 $user_library=return_library_array( "select id,user_name from user_passwd", "id", "user_name"  );

		  
		  $i=1;$tot_recv_qnty=$tot_recv_amt=0;
			foreach($kniting_prod_arr as $recvNo=>$recv_Data)
			{
			 foreach($recv_Data as $prod_id=>$prod_Data)
			 {
			  foreach($prod_Data as $bodyId=>$row)
			 {
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";//change_date_format
			?>
            	<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trpo_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trpo_<? echo $i; ?>">
                    <td><p><? echo $i; ?>&nbsp;</p></td>
                    <td><p><? echo $prod_id; ?>&nbsp;</p></td>
                    <td><p><? echo $recvNo; ?>&nbsp;</p></td>
                    <td><p><? echo $row[('challan_no')]; ?>&nbsp;</p></td>
                    <td><p><? echo change_date_format($row[('receive_date')]); ?>&nbsp;</p></td>
                    <td><p><? echo $row[('booking_no')]; ?>&nbsp;</p></td>
                    <td><p><? echo $piDataArr[$row[('booking_no')]]; ?>&nbsp;</p></td>
                    <td><p><? echo  $body_part[$bodyId]; ?>&nbsp;</p></td>
                     <td><p><? echo  $desc_arr[$prod_id]; ?>&nbsp;</p></td>
                    <td align="right"><? echo number_format($row[('knit_qnty_rec')],2);; ?>&nbsp;</td>
                    <td align="right"><? echo  number_format($row[('knit_qnty_rec_amt')]/$row[('knit_qnty_rec')],2); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[('knit_qnty_rec_amt')],2); ?>&nbsp;</td>
                    <td align="right"><? echo $unit_of_measurement[$row[('uom')]];///supplier_id ?>&nbsp;</td>
                    <td align="center"><? echo $supplier_library[$row[('supplier_id')]]; ?>&nbsp;</td>
                    <td align="right"><? echo $user_library[$row[('inserted_by')]];///unit_of_measurement ?>&nbsp;</td>
                    
                </tr>
            <?
				$tot_recv_qnty+=$row[('knit_qnty_rec')];
				$tot_recv_amt+=$row[('knit_qnty_rec_amt')];
				$i++;
			  }
			 }
			}
			?>
            </tbody>
            <tfoot>
                <tr>
                    <th align="right">&nbsp;</th>
                     <th align="right">&nbsp;</th>
                    <th align="right">&nbsp;</th>
                     <th align="right">&nbsp;</th>
                     <th align="right">&nbsp;</th>
                     <th align="right">&nbsp;</th>
                    <th align="right">&nbsp;</th>
                      <th align="right">&nbsp;</th>
                     <th align="right">Total</th>
                     <th align="right"><? echo number_format($tot_recv_qnty,2,'.',''); ?></th>
                     <th align="right">&nbsp;</th>
                     <th align="right"><? echo number_format($tot_recv_amt,2,'.',''); ?>&nbsp;</th>
                     <th align="right">&nbsp;</th>
                  
                    <th align="right"></th>
                    <th align="right"><? //echo number_format($tot_recv_bl_qnty,0,'.',''); ?>&nbsp;</th>
                </tr>
             </tfoot>
        </table>
          <script>   setFilterGrid("table_body_popup",-1);</script>
        </div>
	<?

	exit();
}
if($action=='wo_popup')
{
	echo load_html_head_contents("WO Details info", "../../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_id."*".$body_id;die;

	//echo $ratio;die;

	?>
	<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}

	 
	
	function generate_fabric_report(type,report_type,booking,company,fabric_nature,fabric_source){
	
				var show_yarn_rate='';
			var report_title="";
			var image_cond=1;
			
			if(type!='print_booking_5' && type != 'fabric_booking_report')
			{

			var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate/Amount\nPress  \"OK\"  to Show Yarn Rate/Amount");
			if (r==true){
				show_yarn_rate="1";
			}
			else{
				show_yarn_rate="0";
			}
			}
			// $report_title=$( "div.form_caption" ).html();+'&path=../../'	
			// 'txt_booking_no*cbo_company_name*txt_order_no_id*cbo_fabric_natu*cbo_fabric_source*id_approved_id'		
			var data="action="+type+
			'&show_yarn_rate='+show_yarn_rate+
			'&cbo_company_name='+company+
			'&txt_booking_no='+"'"+booking+"'"+
			'&report_type='+report_type+
			'&cbo_fabric_natu='+fabric_nature+
			'&cbo_fabric_source='+fabric_source+
			'&image_cond='+image_cond;
			http.open("POST","../../../../order/woven_order/requires/partial_fabric_booking_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_fabric_report_reponse;

			}
			function generate_fabric_report_reponse(){
			if(http.readyState == 4)
			{      
				var w = window.open();
			var d = w.document.open();
			// d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				//  '<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><body><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="all" /><title></title></head>'+http.responseText+'</body</html>');
			d.close();
			}
		}
	</script>
	<script>
		function print_window()
		{
			$("#table_body_popup tr:first").hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
			$("#table_body_popup tr:first").show();
		}	
	</script>	
    
 <div  style="width:750px;" align="center">
        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
  </div>
    <div  id="report_div" style="width:100%;" align="center">
   
       <table width="750" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr>
                    <th colspan="8">WO Summary</th>
                </tr>
                <tr>
                    <th width="20">SL</th>
                    <th width="100">WO No</th>
                    <th width="70">Wo Date</th>
                    <th width="100">Body Part</th>
                    <th width="200">Fabric Desc.</th>
                    <th width="70">Wo Qty</th>
                    <th width="50">UOM</th>
                    <th width="">Supplier</th>
                   
                </tr>
            </thead>
            <tbody  id="table_body_popup">
            <?
			/* $sql_wo="select a.id, a.booking_no,a.booking_date,a.pay_mode,a.supplier_id,a.currency_id,a.exchange_rate,b.gmts_color_id,a.booking_type,b.po_break_down_id as po_id,
	(b.grey_fab_qnty) as grey_fab_qnty,b.job_no,b.amount,c.body_part_id as bpart_id,c.color_size_sensitive,c.lib_yarn_count_deter_id as deter_id
	from wo_booking_mst a, wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c  where a.booking_no=b.booking_no and c.id=b.pre_cost_fabric_cost_dtls_id and a.booking_type=1 and b.fin_fab_qnty>0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1  $po_cond_for_in";*/
		$sql_wo="SELECT a.id, a.booking_no,a.booking_date,a.pay_mode,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,b.gmts_color_id,a.booking_type,b.po_break_down_id as po_id,
	(b.grey_fab_qnty) as grey_fab_qnty,b.job_no,b.amount,c.body_part_id as bpart_id,c.color_size_sensitive,c.lib_yarn_count_deter_id as deter_id,c.construction,c.composition,c.gsm_weight,c.uom,a.company_id,a.item_category,a.fabric_source
	from wo_booking_mst a, wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c  where a.booking_no=b.booking_no and c.id=b.pre_cost_fabric_cost_dtls_id and a.booking_type=1 and b.fin_fab_qnty>0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1 and b.po_break_down_id in ($po_id)
	and b.gmts_color_id in ($color_id) and c.body_part_id in ($body_id)  and c.lib_yarn_count_deter_id in ($deterId)";
	//echo $sql_wo; die;
	$sql_wo_res=sql_select($sql_wo);
	foreach ($sql_wo_res as $row)
	{
	
		
		$booking_date=$row[csf("booking_date")];
		$currency_id=$row[csf("currency_id")];
		if($db_type==0)
		{
			$conversion_date=change_date_format($booking_date, "Y-m-d", "-",1);
		}
		else
		{
			$conversion_date=change_date_format($booking_date, "d-M-y", "-",1);
		}
		$currency_rate=set_conversion_rate($usd_id,$conversion_date );
	
		if($currency_id==1) //Taka
		{
			$amount=$row[csf("amount")]/$currency_rate;
		}
		else
		{
			$amount=$row[csf("amount")];	
		}
		
		if($row[csf("pay_mode")]==3 || $row[csf("pay_mode")]==5)
		{
			$com_supplier=$company_library[$row[csf("supplier_id")]];
		}
		else
		{
			$com_supplier=$supplier_library[$row[csf("supplier_id")]];
		}
		$booking_req_arr[$row[csf("booking_no")]][$row[csf("bpart_id")]][$row[csf("deter_id")]]['grey']+=$row[csf("grey_fab_qnty")];
		$booking_req_arr[$row[csf("booking_no")]][$row[csf("bpart_id")]][$row[csf("deter_id")]]['amount']+=$amount;
		$booking_req_arr[$row[csf("booking_no")]][$row[csf("bpart_id")]][$row[csf("deter_id")]]['desc']=$body_part[$row[csf("bpart_id")]].','.$row[csf("construction")].','.$row[csf("composition")].','.$row[csf("gsm_weight")];
		$booking_req_arr[$row[csf("booking_no")]][$row[csf("bpart_id")]][$row[csf("deter_id")]]['supp']=$com_supplier;
		$booking_req_arr[$row[csf("booking_no")]][$row[csf("bpart_id")]][$row[csf("deter_id")]]['uom']=$row[csf("uom")];
		$booking_req_arr[$row[csf("booking_no")]][$row[csf("bpart_id")]][$row[csf("deter_id")]]['booking_date']=$row[csf("booking_date")];
		$booking_req_arr[$row[csf("booking_no")]][$row[csf("bpart_id")]][$row[csf("deter_id")]]['company_id']=$row[csf("company_id")];
		$booking_req_arr[$row[csf("booking_no")]][$row[csf("bpart_id")]][$row[csf("deter_id")]]['item_category']=$row[csf("item_category")];
		$booking_req_arr[$row[csf("booking_no")]][$row[csf("bpart_id")]][$row[csf("deter_id")]]['fabric_source']=$row[csf("fabric_source")];
	 }
	 
		  
		  $i=1;$tot_wo_qnty=0;
			foreach($booking_req_arr as $booking=>$book_Data)
			{
			 foreach($book_Data as $body_id=>$body_Data)
			 {
			  foreach($body_Data as $deterId=>$row)
			 {
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";			
				$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$row[('company_id')]."'  and module_id=2 and report_id=35 and is_deleted=0 and status_active=1");
				$print_report_format_arr=explode(",",$print_report_format);
				// print_r($print_report_format_arr);
			
					if($print_report_format_arr[0]==143){ $type='show_fabric_booking_report_urmi';}
					if($print_report_format_arr[0]==84){ $type='show_fabric_booking_report_urmi_per_job';}
					if($print_report_format_arr[0]==85){ $type='print_booking_3';}
					if($print_report_format_arr[0]==151){ $type='show_fabric_booking_report_advance_attire_ltd';}
					if($print_report_format_arr[0]==160){ $type='print_booking_5';}
					if($print_report_format_arr[0]==175){ $type='print_booking_6';}
					if($print_report_format_arr[0]==155){ $type='fabric_booking_report';}
					if($print_report_format_arr[0]==235){ $type='print_9';}
					if($print_report_format_arr[0]==191){ $type='print_booking_7';}
				
			?>
            	<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trpo_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trpo_<? echo $i; ?>">
                    <td><p><? echo $i; ?>&nbsp;</p></td>
                    <td><a href='#' onClick="generate_fabric_report('<?=$type;?>',1,'<?=$booking;?>','<?=$row[('company_id')];?>','<?=$row[('item_category')];?>','<?=$row[('fabric_source')];?>')"><?=$booking?></a></td>
                    <td align="center"><? echo change_date_format($row[('booking_date')]);; ?>&nbsp;</td>
                    <td align="center"><? echo   $body_part[$body_id]; ?>&nbsp;</td>
                    <td align="center"><? echo $row[('desc')]; ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[('grey')],2); ?>&nbsp;</td>
                    <td align="center"><? echo $unit_of_measurement[$row[('uom')]]; ?>&nbsp;</td>
                    <td align="center"><? echo $row[('supp')]; ?>&nbsp;</td>
                    
                </tr>
            <?
				$tot_wo_qnty+=$row[('grey')];
				$i++;
			  }
			 }
			}
			?>
            </tbody>
            <tfoot>
                <tr>
                    <th align="right">&nbsp;</th>
                     <th align="right">&nbsp;</th>
                    <th align="right">&nbsp;</th>
                     <th align="right">&nbsp;</th>
                    <th align="right">Total</th>
                    <th align="right"><? echo number_format($tot_wo_qnty,2,'.',''); ?>&nbsp;</th>
                    <th align="right"></th>
                    <th align="right"><? //echo number_format($tot_recv_bl_qnty,0,'.',''); ?>&nbsp;</th>
                </tr>
             </tfoot>
        </table>
          <script>   setFilterGrid("table_body_popup",-1);</script>
        </div>
<?

	exit();
}

if($action=="booking_info")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

 <script>
	function generate_trim_report(action,txt_booking_no,cbo_company_name,id_approved_id,cbo_isshort)
	{
			var show_comment='';
			var r=confirm("Press  \"Cancel\"  to hide  comments\nPress  \"OK\"  to Show comments");
			if (r==true) show_comment="1"; else show_comment="0";
			var report_title="";
			var fabric_nature = <? echo $fabric_nature ?>;
			if(cbo_isshort==1)
			{
				report_title="Short Trims Booking [Multiple Order]";
			}
			else
			{
				report_title="Multi Job Wise Trim Booking";
			}
			//var report_title='';
			var data="action="+action+'&report_title='+"'"+report_title+'&txt_booking_no='+"'"+txt_booking_no+"'"+'&cbo_company_name='+cbo_company_name+'&id_approved_id='+id_approved_id+'&report_type=1&link=1';
			//freeze_window(5);
			if(fabric_nature == 3)
			{
				if(cbo_isshort==1)
				{
					http.open("POST","../../../../order/woven_gmts/requires/short_trims_booking_multi_job_controllerurmi.php",true);
				}
				else
				{
					http.open("POST","../../../../order/woven_gmts/requires/trims_booking_multi_job_controllerurmi.php",true);
				}
			}
			else
			{
				if(cbo_isshort==1)
				{
					http.open("POST","../../../../order/woven_order/requires/short_trims_booking_multi_job_controllerurmi.php",true);
				}
				else
				{
					http.open("POST","../../../../order/woven_order/requires/trims_booking_multi_job_controllerurmi.php",true);
				}
			}
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
        <td align="center" colspan="9"><strong>WO Summary</strong> </td>
         </tr>
        </table>
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="20">Sl</th>
                    <th width="100">Wo No</th>
                    <th width="60">Wo Type</th>
                    <th width="60">Wo Date</th>
                    <th width="100">Country</th>
                    <th width="200">Item Description</th>
                    <th width="80">Wo Qty</th>
                    <th width="60">UOM</th>
                    <th>Supplier</th>
				</thead>
                <tbody>
                <?
				$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
				$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );

				$conversion_factor_array=array();

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
				$wo_sql="select a.is_short, a.is_approved as is_approved, a.booking_no, a.booking_date, a.pay_mode, a.supplier_id, b.job_no, b.country_id_string, b.po_break_down_id, sum(b.wo_qnty) as wo_qnty, b.uom from wo_booking_mst a, wo_booking_dtls b
				where a.item_category=4 and a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1
				and b.status_active=1 and b.is_deleted=0 and  b.job_no='$job_no' and b.trim_group=$item_name and b.po_break_down_id in($po_id) and b.pre_cost_fabric_cost_dtls_id=$trim_dtla_id $boking_cond group by a.is_short, a.is_approved, b.po_break_down_id, b.job_no, a.booking_no, a.booking_date, a.pay_mode, a.supplier_id, b.uom, b.country_id_string";
				$dtlsArray=sql_select($wo_sql);

				$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$cbo_company_name."' and module_id=2 and report_id in(5,6) and is_deleted=0 and status_active=1");

				$report= max(explode(',',$print_report_format));

				if($report==433){$reporAction="show_trim_booking_report19";}
				elseif($report==13){$reporAction="show_trim_booking_report";}
				elseif($report==14){$reporAction="show_trim_booking_report1";}
				elseif($report==15){$reporAction="show_trim_booking_report2";}
				elseif($report==16){$reporAction="show_trim_booking_report3";}
			

				foreach($dtlsArray as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$description=$item_description_arr[$row[csf('po_break_down_id')]][$row[csf('job_no')]][$trim_dtla_id]['description'];
					$conversion_factor_rate=$conversion_factor_array[$item_name]['con_factor'];
					$country_arr_data=explode(',',$row[csf('country_id_string')]);
					$country_name_data="";
					foreach($country_arr_data as $country_row)
					{
						if($country_name_data=="") $country_name_data=$country_name_library[$country_row]; else $country_name_data.=",".$country_name_library[$country_row];
					}
					$wo_type=''; $action_name="";
					if($fabric_nature == 3)
					{
						if($row[csf('is_short')]==1)
						{
							$wo_type="Short";
							$action_name="show_trim_booking_report";
						}
						else
						{
							$wo_type="Main";
							$action_name="show_trim_booking_report";
						}
					}
					else
					{
						if($row[csf('is_short')]==1)
						{
							$wo_type="Short";
							$action_name="show_trim_booking_report19";
						}
						else
						{
							$wo_type="Main";
							$action_name="show_trim_booking_report19";
						}
					}
					$supplier_name_str="";
					if($row[csf('pay_mode')]==3 || $row[csf('pay_mode')]==5) $supplier_name_str=$company_arr[$row[csf('supplier_id')]]; else $supplier_name_str=$supplier_arr[$row[csf('supplier_id')]];
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="20"><p><? echo $i; ?></p></td>
						<td width="100"><p><a href="#" onClick="generate_trim_report('<? echo $action_name; ?>','<? echo $row[csf('booking_no')]; ?>',<? echo $cbo_company_name; ?>,<? echo $row[csf('is_approved')]; ?>,<? echo $row[csf('is_short')]; ?>)"><? echo $row[csf('booking_no')]; ?></a></p></td>
						<td width="60"><p><? echo $wo_type; ?></p></td>
						<td width="60"><p><? echo change_date_format($row[csf('booking_date')]); ?></p></td>
						<td width="100"><p><? echo $country_name_data; ?></p></td>
						<td width="200"><p><?  echo $description; ?></p></td>
						<td width="80" align="right" title="<? echo 'conversion_factor='.$conversion_factor_rate; ?>"><p><? echo number_format($row[csf('wo_qnty')],2); ?></p></td>
						<td width="60" align="center" ><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
						<td><p><? echo $supplier_name_str; ?></p></td>
					</tr>
					<?
					$tot_qty+=$row[csf('wo_qnty')];
					$i++;
				}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                   		 <td colspan="6" align="right">Total</td>
                    	<td  align="right"><? echo number_format($tot_qty,2); ?></td>
                        <td align="right">&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div style="display:none" id="data_panel"></div>
    </fieldset>
    <script type="text/javascript" src="../../../../js/jquery.js"></script>
    <script type="text/javascript" src="../../../../js/jquerybarcode.js"></script>
    <?
	exit();
}
//disconnect($con);
?>
<?
if($action=="booking_inhouse_info")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<!--<div style="width:880px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
	<fieldset style="width:870px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="30">Sl</th>
                    <th width="60">Prod. ID</th>
                    <th width="100">Recv. ID</th>
                    <th width="100">Wo/Pi No</th>
                    <th width="100">Chalan No</th>
                    <th width="70">Recv. Date</th>
                    <th width="150">Item Description.</th>
                    <th width="80">Recv. Qty.</th>
                    <th>Reject Qty.</th>
				</thead>
                <tbody>
                <?
					$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
					$bookingNoArr=return_library_array( "select id, booking_no from  wo_booking_mst", "id", "booking_no");
					$piArr=return_library_array("select id, pi_number from com_pi_master_details", "id", "pi_number");
					$i=1;

					$item_arr=array();
					$conversion_factor=sql_select("select id,conversion_factor,order_uom from lib_item_group where status_active=1  ");
					foreach($conversion_factor as $row_f)
					{
						$item_arr[$row_f[csf('id')]]['order_uom']=$row_f[csf('conversion_factor')];
					}
					unset($conversion_factor);

					$receive_rtn_data=array();
					//echo "select a.issue_number, a.issue_date, e.id, d.po_breakdown_id, c.item_group_id, sum(d.quantity) as quantity from  inv_issue_master a,inv_transaction b, product_details_master c, order_wise_pro_details d,inv_receive_master e  where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and e.id=a.received_id   and b.transaction_type=3 and a.entry_form=49 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and d.po_breakdown_id in($po_id) and c.item_group_id='$item_name' group by a.issue_number,a.issue_date,e.id,d.po_breakdown_id, c.item_group_id order by c.item_group_id";die;



					$receive_qty_data="select a.id, c.po_breakdown_id, a.receive_basis, b.booking_id, b.item_group_id, b.prod_id as prod_id, a.challan_no, b.item_description, a.recv_number, a.receive_date, SUM(c.quantity) as quantity, sum(reject_receive_qnty) as reject_receive_qnty
					from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c , product_details_master d
					where a.id=b.mst_id  and a.entry_form=24 and  a.item_category=4  and b.id=c.dtls_id and b.prod_id=d.id and b.trans_id=c.trans_id and c.trans_type=1 and  c.po_breakdown_id in($po_id)  and b.item_group_id='$item_name' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by  c.po_breakdown_id, b.item_group_id, a.receive_basis, b.booking_id, b.prod_id, a.id, b.item_description, a.recv_number, a.challan_no, a.receive_date";

					$dtlsArray=sql_select($receive_qty_data);

					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$qty=0;
						$qty=$row[csf('quantity')];

						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="60"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="100" align="center"><p><? $piwo_no='';
							if($row[csf('receive_basis')]==1)
							{
								$piwo_no=$piArr[$row[csf('booking_id')]];
							}
							else if($row[csf('receive_basis')]==2)
							{
								$piwo_no=$bookingNoArr[$row[csf('booking_id')]];
							}
							echo $piwo_no; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="70" align="center"><p><? echo  change_date_format($row[csf('receive_date')]); ?></p></td>
                            <td width="150" align="center"><p><? echo $row[csf('item_description')]; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($qty,2); ?></p></td>
                            <td align="right"><p><? echo number_format($row[csf('reject_receive_qnty')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty+=$qty;
						$tot_rej_qty+=$row[csf('reject_receive_qnty')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="6" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_qty,2); ?></td>
                         <td><? echo number_format($tot_rej_qty,2); ?></td>
                    </tr>
                </tfoot>
            </table>
            <?
			$transfer_qty_data=sql_select("select a.transfer_system_id, a.transfer_date, d.po_breakdown_id, c.item_group_id, d.trans_type, d.quantity as quantity, b.prod_id, c.item_description
					from  inv_item_transfer_mst a, inv_transaction b, product_details_master c, order_wise_pro_details d
					where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and a.transfer_criteria=4 and a.item_category=4 and b.item_category=4 and b.transaction_type in(5,6) and d.trans_type in(5,6) and d.entry_form=78 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.po_breakdown_id in($po_id) and c.item_group_id='$item_name'");
			?>
            <table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="30">Sl</th>
                    <th width="80">Prod. ID</th>
                    <th width="100">Transfer. ID</th>
                    <th width="100">Transfer Type</th>
                    <th width="100">Transfer Date</th>
                    <th width="80">Item Description.</th>
                    <th width="160">Return Qty.</th>
				</thead>
                <tbody>
                <?
					/*echo "select a.transfer_system_id, a.transfer_date, d.po_breakdown_id, c.item_group_id, sum((case when b.transaction_type=5 and d.trans_type=5 then d.quantity else 0 end)-(case when b.transaction_type=6 and d.trans_type=6 then d.quantity else 0 end)) as quantity, b.prod_id, c.item_description
					from  inv_item_transfer_mst a, inv_transaction b, product_details_master c, order_wise_pro_details d
					where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and a.transfer_criteria=4 and a.item_category=4 and b.item_category=4 and b.transaction_type in(5,6) and d.trans_type in(5,6) and d.entry_form=78 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.po_breakdown_id in($po_id) and c.item_group_id='$item_name'
					group by a.transfer_system_id, a.transfer_date, d.po_breakdown_id, c.item_group_id, b.prod_id, c.item_description";die;*/


					foreach($transfer_qty_data as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";

						$qty=0;
						$qty=$row[csf('quantity')];

						if($row[csf('trans_type')]==5)
						{
							$trans_type="Transfer In";
							$trans_in_qnty+=$qty;

						}
						else
						{
							$trans_type="Transfer Out";
							$trans_out_qnty+=$qty;
						}

						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="80"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $trans_type; ?></p></td>
                            <td width="100" align="center"><p><? echo  change_date_format($row[csf('transfer_date')]); ?></p></td>
                            <td width="80" align="center"><p><? echo $row[csf('item_description')]; ?></p></td>
                            <td width="160" align="right"><p><? echo number_format($qty,2); ?></p></td>
                        </tr>
						<?
						$tot_trans_qty+=$qty;
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_trans_qty,2); ?></td>
                    </tr>
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
					$receive_rtn_qty_data=sql_select("select a.issue_number, a.issue_date , d.po_breakdown_id, c.item_group_id, d.quantity as quantity, b.prod_id, c.item_description
					from inv_issue_master a, inv_transaction b, product_details_master c, order_wise_pro_details d
					where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and b.transaction_type=3 and d.trans_type=3 and a.entry_form=49 and d.entry_form=49 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and d.po_breakdown_id in($po_id) and c.item_group_id='$item_name'");


					foreach($receive_rtn_qty_data as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";

						$qty=0;
						$qty=$row[csf('quantity')];
						//$qty=$row[csf('quantity')]/$item_arr[$row[csf('item_group_id')]]['order_uom'];
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="80"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="100" align="center"><p><? echo  change_date_format($row[csf('issue_date')]); ?></p></td>
                            <td width="80" align="center"><p><? echo $row[csf('item_description')]; ?></p></td>
                            <td width="160" align="right"><p><? echo number_format($qty,2); ?></p></td>
                        </tr>
						<?
						$tot_rtn_qty+=$qty;
						$i++;
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
                        <td><? $balance_qnty=($tot_qty+$trans_in_qnty)-($tot_rtn_qty+$trans_out_qnty); echo number_format($balance_qnty,2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}

if($action=="balance_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<!--<div style="width:880px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
	<fieldset style="width:830px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="810" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="30">Sl</th>
                    <th width="60">PO No</th>
                    <th width="70">Item Group</th>
					<th width="150">Item Description.</th>
                    <th width="70">Garments Color</th>
                    <th width="70">Item Color</th>
                    <th width="70">Item Size</th>
					<th width="50">UOM</th>
                    <th width="80">Work Order Qty</th>
					<th width="80">Receive Qty</th>
					<th width="80">Receive<br>Balance Qty</th>
				</thead>
                <tbody>
                <?
					$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
					$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
					$size_arr=return_library_array( "select id, size_name from lib_size", "id", "size_name"  );
					$item_group_arr=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
					$bookingNoArr=return_library_array( "select id, booking_no from  wo_booking_mst", "id", "booking_no");
					$piArr=return_library_array("select id, pi_number from com_pi_master_details", "id", "pi_number");
					$i=1;

					$item_arr=array();
					$conversion_factor=sql_select("select id,conversion_factor,order_uom from lib_item_group where status_active=1  ");
					foreach($conversion_factor as $row_f)
					{
						$item_arr[$row_f[csf('id')]]['order_uom']=$row_f[csf('conversion_factor')];
					}
					unset($conversion_factor);

					$receive_rtn_data=array();

					$receive_qty_data="SELECT c.po_breakdown_id,e.po_break_down_id,  b.booking_id, b.item_group_id, b.prod_id as prod_id,  b.item_description,b.gmts_color_id,b.item_color,b.gmts_size_id,b.cons_uom,  c.quantity as quantity,e.wo_qnty as wo_qnty
					from  inv_trims_entry_dtls b, order_wise_pro_details c , product_details_master d,wo_booking_dtls e
					where c.entry_form=24 and  b.id=c.dtls_id and b.prod_id=d.id and b.trans_id=c.trans_id and c.trans_type=1 and c.po_breakdown_id=e.po_break_down_id and e.trim_group=b.item_group_id and e.po_break_down_id in($po_id)  and b.item_group_id='$item_name' and b.status_active=1 and b.is_deleted=0 group by  c.po_breakdown_id, b.item_group_id,  b.booking_id, b.prod_id, b.item_description,b.gmts_color_id,b.item_color,b.gmts_size_id, b.cons_uom,e.po_break_down_id, c.quantity,e.wo_qnty ";
					echo $receive_qty_data; die;

					$dtlsArray=sql_select($receive_qty_data);

					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$wo_qnty=0;
						$qnty=0;
						$qnty=$row[csf('quantity')];
						$wo_qnty=$row[csf('wo_qnty')];
						$rec_qnty=$wo_qnty-$qnty;

						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30" align="center"><p><? echo $i; ?></p></td>
                            <td width="60" align="center"><p><? echo $row[csf('po_breakdown_id')]; ?></p></td>
							<td width="70" align="center"><p><? echo $item_group_arr[$row[csf('item_group_id')]]; ?></p></td>
							<td width="150" align="center"><p><? echo $row[csf('item_description')]; ?></p></td>
							<td width="70" align="center"><p><? echo $color_arr[$row[csf('gmts_color_id')]]; ?></p></td>
							<td width="70" align="center"><p><? echo $color_arr[$row[csf('item_color')]]; ?></p></td>
							<td width="70" align="center"><p><? echo $size_arr[$row[csf('gmts_size_id')]]; ?></p></td>
							<td width="50" align="center"><p><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($wo_qnty,2); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($qnty,2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($rec_qnty,2); ?></p></td>
                        </tr>
						<?
						$tot_wo_qnty+=$wo_qnty;
						$tot_qty+=$qnty;
						$tot_bal_qty+=$rec_qnty;
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="7" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_wo_qnty,2); ?></td>
                        <td><? echo number_format($tot_qty,2); ?></td>
						<td><? echo number_format($tot_bal_qty,2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}

if($action=="leftover_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<!--<div style="width:880px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
	<fieldset style="width:830px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="810" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="30">Sl</th>
                    <th width="60">PO No</th>
                    <th width="70">Item Group</th>
					<th width="150">Item Description.</th>
                    <th width="70">Garments Color</th>
                    <th width="70">Item Color</th>
                    <th width="70">Item Size</th>
					<th width="50">UOM</th>
                    <th width="80">Receive Qty</th>
					<th width="80">Issue Qty</th>
					<th width="80">Issue<br>Balance Qty</th>
				</thead>
                <tbody>
                <?
					$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
					$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
					$size_arr=return_library_array( "select id, size_name from lib_size", "id", "size_name"  );
					$item_group_arr=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
					$bookingNoArr=return_library_array( "select id, booking_no from  wo_booking_mst", "id", "booking_no");
					$piArr=return_library_array("select id, pi_number from com_pi_master_details", "id", "pi_number");
					$i=1;

					$qty_data_arr=array();
					$qty_data=sql_select("select a.id, c.po_breakdown_id,   b.item_group_id, b.prod_id as prod_id,  b.item_description, c.quantity as quantity from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c , product_details_master d where  a.id=b.mst_id  and a.entry_form=24 and  a.item_category=4  and  b.id=c.dtls_id and b.prod_id=d.id and b.trans_id=c.trans_id and c.trans_type=1  and  c.po_breakdown_id in($po_id)  and b.item_group_id='$item_name' and b.status_active=1 and b.is_deleted=0 group by a.id, c.po_breakdown_id, b.item_group_id,  b.prod_id,b.item_description,c.quantity");
					foreach($qty_data as $row)
					{
						$qty_data_arr[$row[csf('po_breakdown_id')]]['quantity']+=$row[csf('quantity')];
					}
	

					$item_arr=array();
					$conversion_factor=sql_select("select id,conversion_factor,order_uom from lib_item_group where status_active=1  ");
					foreach($conversion_factor as $row_f)
					{
						$item_arr[$row_f[csf('id')]]['order_uom']=$row_f[csf('conversion_factor')];
					}
					unset($conversion_factor);

					$receive_qty_data=array();

					$receive_qty_data="select  a.id, a.issue_number,  b.booking_id, b.item_group_id, b.prod_id as prod_id,  b.item_description,b.gmts_color_id,b.item_color_id,b.gmts_size_id,b.uom, c.po_breakdown_id, c.quantity
					from   inv_issue_master a,inv_trims_issue_dtls b, order_wise_pro_details c , product_details_master d
					where a.id=b.mst_id  and a.entry_form=25 and  b.id=c.dtls_id and b.prod_id=d.id and b.trans_id=c.trans_id and c.trans_type=2 and c.po_breakdown_id in($po_id)  and b.item_group_id='$item_name' and b.status_active=1 and b.is_deleted=0 group by  a.id,a.issue_number, b.item_group_id,  b.booking_id, b.prod_id, b.item_description,b.gmts_color_id,b.item_color_id,b.gmts_size_id, b.uom, c.po_breakdown_id, c.quantity";
					//echo $receive_qty_data;

					$dtlsArray=sql_select($receive_qty_data);

					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$qnty=0;
						$qnty=$row[csf('quantity')];

						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30" align="center"><p><? echo $i; ?></p></td>
                            <td width="60" align="center"><p><? echo $row[csf('po_breakdown_id')]; ?></p></td>
							<td width="70" align="center"><p><? echo $item_group_arr[$row[csf('item_group_id')]]; ?></p></td>
							<td width="150" align="center"><p><? echo $row[csf('item_description')]; ?></p></td>
							<td width="70" align="center"><p><? echo $color_arr[$row[csf('gmts_color_id')]]; ?></p></td>
							<td width="70"><p><? echo $color_arr[$row[csf('item_color')]]; ?></p></td>
							<td width="70"><p><? echo $size_arr[$row[csf('gmts_size_id')]]; ?></p></td>
							<td width="50" align="center"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>

                            <td width="80" align="right"><p><? echo number_format($qty_data_arr[$row[csf('po_breakdown_id')]]['quantity'],2); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($qnty,2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($qty_data_arr[$row[csf('po_breakdown_id')]]['quantity']-$qnty,2); ?></p></td>
                        </tr>
						<?
						$tot_qty+=$qty_data_arr[$row[csf('po_breakdown_id')]]['quantity'];
						$total_qty+=$qnty;
						$tot_bal_qty+=$qty_data_arr[$row[csf('po_breakdown_id')]]['quantity']-$qnty;
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="7" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_qty,2); ?></td>
                        <td><? echo number_format($total_qty,2); ?></td>
						<td><? echo number_format($tot_bal_qty,2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}


if($action=="booking_issue_info")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<!--	<div style="width:880px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	-->	<fieldset style="width:870px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
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
					$conversion_factor_array=array();	$item_arr=array();
					$conversion_factor=sql_select("select id ,trim_uom,order_uom,conversion_factor from  lib_item_group  ");
					foreach($conversion_factor as $row_f)
					{
					 $conversion_factor_array[$row_f[csf('id')]]['con_factor']=$row_f[csf('conversion_factor')];
					 $conversion_factor_array[$row_f[csf('id')]]['cons_uom']=$row_f[csf('trim_uom')];
					 $item_arr[$row_f[csf('id')]]['order_uom']=$row_f[csf('order_uom')];
					}
					$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
					$i=1;
					//$wo_sql="select a.item_group_id,a.prod_id,b.recv_number,b.receive_date,a.item_description,sum(a.cons_qnty) as cons_qnty  from inv_receive_master b, inv_trims_entry_dtls a where b.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category=4 group by a.item_group_id,a.prod_id,b.recv_number,b.receive_date,a.item_description";

				 $mrr_sql=("select a.id, a.issue_number,a.challan_no,p.item_group_id,b.prod_id, a.issue_date,b.item_description,SUM(c.quantity) as quantity
					from  inv_issue_master a,inv_trims_issue_dtls b, order_wise_pro_details c,product_details_master p
					where a.id=b.mst_id  and a.entry_form=25 and p.id=b.prod_id and b.id=c.dtls_id and b.trans_id=c.trans_id and c.trans_type=2 and a.is_deleted=0 and a.status_active=1 and
					b.status_active=1 and b.is_deleted=0 and  c.po_breakdown_id in($po_id) and p.item_group_id='$item_name' group by c.po_breakdown_id,p.item_group_id,b.item_description,a.issue_number,a.id,a.issue_date,b.prod_id,a.challan_no ");

					$dtlsArray=sql_select($mrr_sql);

					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$conv_fact=$conversion_factor_array[$row[csf('item_group_id')]]['con_factor'];
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="80" align="center"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td width="100"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="100" align="center"><p><? echo  change_date_format($row[csf('issue_date')]); ?></p></td>
                            <td width="80" align="center"><p><? echo $row[csf('item_description')]; ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($row[csf('quantity')],2); //echo number_format($row[csf('quantity')]/$conv_fact,2); ?></p></td>
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
					$issue_rtn_qty_data=sql_select("select a.recv_number, a.receive_date , d.po_breakdown_id, c.item_group_id, d.quantity as quantity, b.prod_id, c.item_description
					from inv_receive_master a, inv_transaction b, product_details_master c, order_wise_pro_details d
					where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and b.transaction_type=4 and d.trans_type=4 and a.entry_form=73 and d.entry_form=73 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and d.po_breakdown_id in($po_id) and c.item_group_id='$item_name'");

					foreach($issue_rtn_qty_data as $row)
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
                            <td width="160" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                        </tr>
						<?
						$tot_rtn_qty+=$row[csf('quantity')];
						$i++;
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
                        <td><? $balance_qnty=($tot_qty-$tot_rtn_qty); echo number_format($balance_qnty,2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}

if($action=="order_qty_data")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$country_name_library=return_library_array( "select id, country_name from lib_country", "id", "country_name"  );
	//echo $po_id; die;
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
                    <th width="80">Order Qty. (PCS)</th>

				</thead>
                <tbody>
                <?
					$date_cond='';
					if(str_replace("'","",$from_date)!="" && str_replace("'","",$to_date)!="")
					{
						$start_date=(str_replace("'","",$from_date));
						$end_date=(str_replace("'","",$to_date));
						$date_cond="and c.country_ship_date between '$start_date' and '$end_date'";
					}
					$i=1;
					$order_arr=return_library_array( "select id, po_number from wo_po_break_down where id in($po_id)", "id", "po_number"  );

				 	$gmt_item_id=return_field_value("item_number_id", "wo_po_color_size_breakdown", "po_break_down_id='$po_id'");
					$country_id=return_field_value("country_id", "wo_po_color_size_breakdown", "po_break_down_id='$po_id'");
					 //echo $gmt_item_id;
					$sql_po_qty=sql_select("select sum(c.order_quantity) as order_quantity,c.country_id  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id in($po_id) and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,c.country_id ");
					list($sql_po_qty_row)=$sql_po_qty;
					$po_qty=$sql_po_qty_row[csf('order_quantity')];
					//echo "select sum(c.order_quantity) as order_quantity,c.country_id  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id in($po_id) and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,c.country_id ";

					$sql=" select sum( c.order_quantity) as po_quantity, c.country_id, c.po_break_down_id from wo_po_color_size_breakdown c where c.po_break_down_id in($po_id) and c.status_active=1 and c.is_deleted=0 $date_cond group by c.country_id,c.po_break_down_id";
					//echo $sql;
					$dtlsArray=sql_select($sql);

					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
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
//disconnect($con);

if($action=="order_req_qty_data")
{
	/*echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
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

				/*$fabriccostArray=sql_select("select costing_per_id, trims_cost, embel_cost, cm_cost, commission, common_oh, lab_test, inspection, freight, comm_cost from wo_pre_cost_dtls where job_no='".$job_no."' and status_active=1 and is_deleted=0");

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
	exit();*/
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$buyer_short_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name");
	$po_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");

	?>
	<!--<div style="width:680px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px" class="formbutton"/></div>-->
	<fieldset style="width:670px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="30">Sl</th>
                    <th width="80">Buyer Name</th>
                    <th width="100">Order No</th>
                    <th width="100">Item Description</th>
                    <th width="100">Country</th>
                    <th width="80">Req. Qty.</th>
					<th width="60">Uom</th>
                    <th>Req. Rate</th>
				</thead>
                <tbody>
                <?
				//echo $po_id;
				$condition= new condition();
				$condition->job_no("='$job_no'");

				$condition->po_id("in($po_id)");

				if(str_replace("'","",$start_date)!="" && str_replace("'","",$end_date)!="")
				{
					$condition->country_ship_date(" between '$start_date' and '$end_date'");
				}

				$condition->init();
				$trim= new trims($condition);
				$trim_qty=$trim->getQtyArray_by_orderCountryAndPrecostdtlsid();


				//print_r($trim_qty);
				//$trim= new trims($condition);
				//$trim_amount=$trim->getAmountArray_by_orderAndPrecostdtlsid();

				//$trim_qty=$trim->getQtyArray_by_jobAndPrecostdtlsid();
			//print_r($trim_qty);
				//$trim= new trims($condition);
			//$trim_amount=$trim->getAmountArray_by_orderAndPrecostdtlsid();
				//$trim_amount=$trim->getAmountArray_by_jobAndPrecostdtlsid();

				$country_id_str="";
				if($start_date=="" && $end_date=="") $date_cond=""; else $date_cond="and country_ship_date between '$start_date' and '$end_date'";
				$sql_color_size="select id, country_id from wo_po_color_size_breakdown where po_break_down_id in ($po_id) and job_no_mst='$job_no' and status_active=1 and is_deleted=0 $date_cond";
				$sql_color_size_res=sql_select($sql_color_size);
				foreach($sql_color_size_res as $row)
				{
					if($country_id_str=="") $country_id_str=$row[csf('id')]; else $country_id_str.=','.$row[csf('id')];
				}
				$excountry_id=array_filter(array_unique(explode(",",$country_id_str)));
				if($excountry_id!="") $country_idcond= "and c.color_size_table_id in ($excountry_id)"; else $country_idcond= "";

				$sql="select  b.id as trim_dtla_id, b.description,b.cons_uom, b.rate, b.amount,  c.cons, c.country_id, c.po_break_down_id, b.job_no
					from wo_pre_cost_mst a, wo_pre_cost_trim_cost_dtls b, wo_pre_cost_trim_co_cons_dtls c
					where c.job_no=b.job_no and b.id=c.wo_pre_cost_trim_cost_dtls_id and a.job_no=b.job_no and a.job_no='$job_no' and c.po_break_down_id in ($po_id) and b.id=$trim_dtla_id and c.cons>0
					group by  b.id, b.description, b.rate, b.amount,b.cons_uom,  c.cons, c.country_id, c.po_break_down_id, b.job_no order by b.trim_group";

				$dtlsArray=sql_select($sql);
				$pre_cost_data_arr=array();
				foreach($dtlsArray as $row)
				{
					$excountry_id=array_unique(explode(",",$row[csf('country_id')])); $req_qty=0;
					foreach($excountry_id as $country_id)
					{

						//$req_qty=$trim_qty[$row[csf('po_break_down_id')]][$country_id][$row[csf('trim_dtla_id')]];
						$pre_cost_data_arr[$row[csf('po_break_down_id')]][$country_id][$row[csf('trim_dtla_id')]]=$req_qty;
						$pre_cost_uom_arr[$row[csf('po_break_down_id')]][$country_id][$row[csf('trim_dtla_id')]]=$row[csf('cons_uom')];
					}
				}
				unset($dtlsArray);
				$i=1;
				foreach($pre_cost_data_arr as $po_id=>$po_data)
				{
					foreach($po_data as $country_id=>$country_data)
					{
						foreach($country_data as $description=>$req_qty)
						{
							//if(in_array($country_id,$excountry_id))
							//{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								//echo $po_id.'='.$country_id.'='.$description.', ';
								$trim_req_qty=$trim_qty[$po_id][$country_id][$description];
								$uom_id=$pre_cost_uom_arr[$po_id][$country_id][$description];

								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
									<td width="30"><p><? echo $i; ?></p></td>
									<td width="80" align="center"><p><? echo $buyer_short_arr[$buyer]; ?></p></td>
									<td width="100"><p><? echo $po_arr[$po_id]; ?></p></td>
									<td width="100"><p><? //echo $description;//$description; ?></p></td>
									<td width="100" align="center"><p><? echo $country_arr[$country_id]; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($trim_req_qty,2); ?></p></td>
									<td width="60" align="right"><p><? echo $unit_of_measurement[$uom_id]; ?></p></td>
									<td align="right"><p><? echo number_format($rate,4); ?></p></td>
								</tr>
								<?
								$tot_qty+=$trim_req_qty;
								$i++;
							//}
						}
					}
				}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td align="right">&nbsp;</td>
                    	<td align="right" colspan="4">Total</td>
                        <td align="right"><? echo number_format($tot_qty,2); ?></td>
                        <td>&nbsp;</td>
						<td>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}

if($action=='yarn_wo_popup')
{
	echo load_html_head_contents("WO Details info", "../../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
	$sql_wo="SELECT a.WO_NUMBER_PREFIX_NUM, a.WO_DATE, a.SUPPLIER_ID, b.YARN_COMP_TYPE1ST, b.YARN_COMP_PERCENT1ST, sum(b.supplier_order_quantity) as QUANTITY, b.UOM 
	from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.entry_form=234 and a.status_active=1 and b.status_active=1 and b.job_id=$job_id and b.id in($booking_dtls_id) group by a.WO_NUMBER_PREFIX_NUM, a.WO_DATE, a.SUPPLIER_ID, b.YARN_COMP_TYPE1ST, b.YARN_COMP_PERCENT1ST,b.UOM ";
	// echo $sql_wo; 
	$sql_wo_res=sql_select($sql_wo);  

	?>

    <div  id="report_div" style="width:100%;" align="center">
   
       <table width="750" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr>
                    <th width="20">SL</th>
                    <th width="100">WO No</th>
                    <th width="80">Wo Date</th>
                    <th width="200">Yarn Composition</th>
                    <th width="80">Wo Qty</th>
                    <th width="50">UOM</th>
                    <th >Supplier</th>
                </tr>
            </thead>
            <tbody  id="table_body_popup">
            	<?
				$i=1;$tot_wo_qnty=0;
				foreach($sql_wo_res as $row)
				{

					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";			
					$yarn_composition=$composition[$row["YARN_COMP_TYPE1ST"]]." ".$row["YARN_COMP_PERCENT1ST"]."%";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trpo_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trpo_<? echo $i; ?>">
						<td><p><? echo $i; ?>&nbsp;</p></td>
						<td align="center"><?=$row['WO_NUMBER_PREFIX_NUM']?></td>
						<td align="center"><? echo change_date_format($row['WO_DATE']); ?>&nbsp;</td>
						<td align="center"><? echo $yarn_composition; ?>&nbsp;</td>
						<td align="right"><? echo number_format($row['QUANTITY'],2); ?>&nbsp;</td>
						<td align="center"><? echo $unit_of_measurement[$row['UOM']]; ?>&nbsp;</td>
						<td align="center"><? echo $supplier_arr[$row["SUPPLIER_ID"]]; ?>&nbsp;</td>            
					</tr>
					<?
					$tot_wo_qnty+=$row['QUANTITY'];
					$i++;
				}
				?>
            </tbody>
			<tfoot>
				<tr>
					<th></th>
					<th></th>
					<th></th>
					<th>Total:</th>
					<th><? echo number_format($tot_wo_qnty,2); ?></th>
					<th></th>
					<th></th>
				</tr>
			</tfoot>
        </table>
      </div>
	<?
	exit();
}

if($action=='yarn_rcv_popup')
{
	echo load_html_head_contents("WO Details info", "../../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	$lib_color_arr=return_library_array( "SELECT id, color_name from lib_color", "id", "color_name"  );
	$piArr=return_library_array("SELECT id, pi_number from com_pi_master_details", "id", "pi_number");
	$supplierArr=return_library_array("SELECT id, supplier_name from lib_supplier", "id", "supplier_name");
	$sql_cond="";
	if($yarn_color){$sql_cond.=" and c.color=$yarn_color ";}
	if($count_id){$sql_cond.=" and c.yarn_count_id=$count_id ";}
	if($yarn_comp){$sql_cond.=" and c.yarn_comp_type1st=$yarn_comp ";}
	if($yarn_percent){$sql_cond.=" and c.yarn_comp_percent1st=$yarn_percent ";}
	$rcv_sql="SELECT a.RECEIVE_BASIS, a.RECV_NUMBER_PREFIX_NUM, a.RECEIVE_DATE, a.BOOKING_ID, a.REMARKS, sum(b.cons_quantity) as RCV_QNTY, sum(b.cons_amount) as RCV_AMOUNT, c.YARN_COMP_TYPE1ST, c.YARN_COMP_PERCENT1ST , c.COLOR, c.LOT
	from inv_receive_master a, inv_transaction b, product_details_master c 
	where a.id=b.mst_id and b.prod_id=c.id and b.receive_basis=1 and b.transaction_type = 1 and b.item_category=1 and a.entry_form=248 and b.entry_form=248 and a.status_active=1 and b.status_active=1 and b.job_no='$job_no' $sql_cond
	group by a.RECEIVE_BASIS,a.RECV_NUMBER_PREFIX_NUM,a.RECEIVE_DATE,a.BOOKING_ID,a.REMARKS,c.YARN_COMP_TYPE1ST,c.YARN_COMP_PERCENT1ST,c.COLOR,c.LOT";
	// echo $rcv_sql; 
	$rcv_sql_res=sql_select($rcv_sql);  


	$rcv_rtn_sql="SELECT a.ISSUE_NUMBER_PREFIX_NUM, a.ISSUE_DATE, a.SUPPLIER_ID, a.REMARKS, sum(b.cons_quantity) as RCV_RTN_QNTY, sum(b.cons_amount) as RCV_RTN_AMOUNT, c.YARN_COMP_TYPE1ST, c.YARN_COMP_PERCENT1ST , c.COLOR, c.LOT, c.YARN_TYPE
	from inv_issue_master a, inv_transaction b, product_details_master c 
	where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type = 3 and b.item_category=1 and a.entry_form=381 and b.entry_form=381 and a.status_active=1 and b.status_active=1 and b.job_no='$job_no' $sql_cond
	group by a.ISSUE_NUMBER_PREFIX_NUM,a.ISSUE_DATE,a.SUPPLIER_ID,a.REMARKS,c.YARN_COMP_TYPE1ST,c.YARN_COMP_PERCENT1ST,c.COLOR,c.LOT,c.YARN_TYPE";
	// echo $rcv_rtn_sql; 
	$rcv_rtn_sql_res=sql_select($rcv_rtn_sql);  

	?>

    <div id="report_div" style="width:100%;" align="center">
    	<table width="1000" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
				<tr>
					<th colspan="12">In-House Qty Pop Up</th>
				</tr>
				<tr>
					<th colspan="12">Receive Details</th>
				</tr>
                <tr>
                    <th width="20">SL</th>
                    <th width="80">Rcvd. Basis</th>
                    <th width="80">PI/WO No</th>
                    <th width="80">MRR No</th>
                    <th width="80">MRR Date</th>
                    <th width="80">Yarn Color</th>
                    <th width="150">Yarn Composition</th>
                    <th width="80">Y. Lot</th>
                    <th width="80">MRR Qty.</th>
                    <th width="80">Unit Price</th>
                    <th width="80">MRR Value</th>
                    <th >Remarks</th>
                </tr>
            </thead>
            <tbody  id="table_body_popup">
            	<?
				$i=1;$tot_rcv_qnty=0;$tot_rcv_amt=0;
				foreach($rcv_sql_res as $row)
				{

					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";			
					$yarn_composition=$composition[$row["YARN_COMP_TYPE1ST"]]." ".$row["YARN_COMP_PERCENT1ST"]."%";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trpo_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trpo_<? echo $i; ?>">
						<td><p><? echo $i++; ?>&nbsp;</p></td>
						<td align="center"><?=$receive_basis_arr[$row['RECEIVE_BASIS']]?></td>
						<td align="center"><?=($row['RECEIVE_BASIS']==1)?$piArr[$row['BOOKING_ID']]:"";?></td>
						<td align="center"><?=$row['RECV_NUMBER_PREFIX_NUM']?></td>
						<td align="center"><? echo change_date_format($row['RECEIVE_DATE']); ?>&nbsp;</td>
						<td ><? echo $lib_color_arr[$row['COLOR']]; ?>&nbsp;</td>
						<td ><? echo $yarn_composition; ?>&nbsp;</td>
						<td ><?=$row['LOT']?></td>
						<td align="right"><? echo number_format($row['RCV_QNTY'],2); ?>&nbsp;</td>
						<td align="right"><? echo number_format($row['RCV_AMOUNT']/$row['RCV_QNTY'],2); ?>&nbsp;</td>
						<td align="right"><? echo number_format($row['RCV_AMOUNT'],2); ?>&nbsp;</td>
						<td ><? echo $row['REMARKS']; ?>&nbsp;</td>         
					</tr>
					<?
					$tot_rcv_qnty+=$row['RCV_QNTY'];
					$tot_rcv_amt+=$row['RCV_AMOUNT'];
				}
				?>
            </tbody>
			<tfoot>
				<tr>
					<th colspan="7"></th>
					<th>Total:</th>
					<th><? echo number_format($tot_rcv_qnty,2); ?></th>
					<th></th>
					<th><? echo number_format($tot_rcv_amt,2); ?></th>
					<th></th>
				</tr>
			</tfoot>
    	</table>
		<br>
    	<table width="1000" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
				<tr>
					<th colspan="12">Receive Return Details</th>
				</tr>
	            <tr>
                    <th width="20">SL</th>
                    <th width="80">Return Date</th>
                    <th width="80">Return Number</th>
                    <th width="80">Returned To</th>
                    <th width="80">Lot No</th>
                    <th width="80">Color</th>
                    <th width="150">Yarn Composition</th>
                    <th width="80">Yarn Type</th>
                    <th width="80">Return Qnty</th>
                    <th width="80">Rate</th>
                    <th width="80">Value</th>
                    <th >Remark</th>
                </tr>
            </thead>
            <tbody  id="table_body_popup">
            	<?
				$i=1;$tot_rcv_rtn_qnty=0;$tot_rcv_rtn_amt=0;
				foreach($rcv_rtn_sql_res as $row)
				{

					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";			
					$yarn_composition=$composition[$row["YARN_COMP_TYPE1ST"]]." ".$row["YARN_COMP_PERCENT1ST"]."%";
					?>
					<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('trpo_<?=$i; ?>','<?=$bgcolor; ?>')" id="trpo_<?=$i; ?>">
						<td><p><?=$i++; ?>&nbsp;</p></td>
						<td align="center"><?=change_date_format($row['ISSUE_DATE']); ?>&nbsp;</td>
						<td align="center"><?=$row['ISSUE_NUMBER_PREFIX_NUM']?></td>
						<td align="center"><?=$supplierArr[$row['SUPPLIER_ID']]?></td>
						<td ><?=$row['LOT']?></td>
						<td ><?=$lib_color_arr[$row['COLOR']]; ?>&nbsp;</td>
						<td ><?=$yarn_composition; ?>&nbsp;</td>
						<td ><?=$yarn_type[$row["YARN_TYPE"]];?>&nbsp;</td>
						<td align="right"><?=number_format($row['RCV_RTN_QNTY'],2); ?>&nbsp;</td>
						<td align="right"><?=number_format($row['RCV_RTN_AMOUNT']/$row['RCV_RTN_QNTY'],2); ?>&nbsp;</td>
						<td align="right"><?=number_format($row['RCV_RTN_AMOUNT'],2); ?>&nbsp;</td>
						<td ><?=$row['REMARKS']; ?>&nbsp;</td>         
					</tr>
					<?
					$tot_rcv_rtn_qnty+=$row['RCV_RTN_QNTY'];
					$tot_rcv_rtn_amt+=$row['RCV_RTN_AMOUNT'];
				}
				?>
            </tbody>
			<tfoot>
				<tr>
					<th colspan="7"></th>
					<th>Total:</th>
					<th><? echo number_format($tot_rcv_rtn_qnty,2); ?></th>
					<th></th>
					<th><? echo number_format($tot_rcv_rtn_amt,2); ?></th>
					<th></th>
				</tr>
				<tr>
					<th colspan="7"></th>
					<th>Net Receive:</th>
					<th><? echo number_format($tot_rcv_qnty-$tot_rcv_rtn_qnty,2); ?></th>
					<th></th>
					<th><? echo number_format($tot_rcv_amt-$tot_rcv_rtn_amt,2); ?></th>
					<th></th>
				</tr>
			</tfoot>
    	</table>
    </div>
	<?
	exit();
}

if($action=='yarn_iss_popup')
{
	echo load_html_head_contents("WO Details info", "../../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	$lib_color_arr=return_library_array( "SELECT id, color_name from lib_color", "id", "color_name"  );
	$sql_cond="";
	if($yarn_color){$sql_cond.=" and c.color=$yarn_color ";}
	if($count_id){$sql_cond.=" and c.yarn_count_id=$count_id ";}
	if($yarn_comp){$sql_cond.=" and c.yarn_comp_type1st=$yarn_comp ";}
	if($yarn_percent){$sql_cond.=" and c.yarn_comp_percent1st=$yarn_percent ";}
	$iss_sql="SELECT a.ISSUE_BASIS, a.ISSUE_PURPOSE, a.ISSUE_NUMBER_PREFIX_NUM, a.ISSUE_DATE, a.REMARKS, b.REQUISITION_NO, sum(b.cons_quantity) as ISS_QNTY, c.YARN_COMP_TYPE1ST, c.YARN_COMP_PERCENT1ST, c.COLOR, c.LOT
	from inv_issue_master a, inv_transaction b, product_details_master c 
	where a.id=b.mst_id and b.prod_id=c.id and b.item_category=1 and a.entry_form=277 and b.entry_form=277 and b.transaction_type=2 and a.status_active=1 and b.status_active=1 and b.job_no='$job_no' $sql_cond
	group by a.ISSUE_BASIS, a.ISSUE_PURPOSE, a.ISSUE_NUMBER_PREFIX_NUM, a.ISSUE_DATE, a.REMARKS, b.REQUISITION_NO, c.YARN_COMP_TYPE1ST, c.YARN_COMP_PERCENT1ST, c.COLOR, c.LOT";
	// echo $iss_sql; 
	$iss_sql_res=sql_select($iss_sql);  

	$iss_rtn_sql="SELECT a.RECEIVE_BASIS, a.CHALLAN_NO, a.BOOKING_NO, a.RECV_NUMBER_PREFIX_NUM, a.RECEIVE_DATE, a.REMARKS, sum(b.cons_quantity) as ISS_RTN_QNTY, c.YARN_COMP_TYPE1ST, c.YARN_COMP_PERCENT1ST, c.COLOR, c.LOT
	from inv_receive_master a, inv_transaction b, product_details_master c 
	where a.id=b.mst_id and b.prod_id=c.id and b.item_category=1 and a.entry_form=382 and b.entry_form=382 and b.transaction_type=4 and a.status_active=1 and b.status_active=1 and b.job_no='$job_no' $sql_cond
	group by a.RECEIVE_BASIS, a.CHALLAN_NO, a.BOOKING_NO, a.RECV_NUMBER_PREFIX_NUM, a.RECEIVE_DATE, a.REMARKS, c.YARN_COMP_TYPE1ST, c.YARN_COMP_PERCENT1ST, c.COLOR, c.LOT";
	// echo $iss_rtn_sql; 
	$iss_rtn_sql_res=sql_select($iss_rtn_sql); 
	?>
    <div id="report_div" style="width:100%;" align="center">
       <table width="1000" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
				<tr>
					<th colspan="11">Issue to Knittining Pop up</th>
				</tr>
				<tr>
					<th colspan="11">Yarn Issue Pop Up</th>
				</tr>
                <tr>
                    <th width="20">SL</th>
                    <th width="100">Issue Basis</th>
                    <th width="100">Lot Ration/Job /WO No</th>
                    <th width="100">Issue Purpose</th>
                    <th width="80">Issue ID No</th>
                    <th width="80">Issue ID Date</th>
                    <th width="80">Yarn Color</th>
                    <th width="150">Yarn Composition</th>
                    <th width="80">Y. Lot</th>
                    <th width="80">Issue Qty.</th>
                    <th >Remarks</th>
                </tr>
            </thead>
            <tbody  id="table_body_popup">
            	<?
				$i=1;$tot_iss_qnty=0;
				foreach($iss_sql_res as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";			
					$yarn_composition=$composition[$row["YARN_COMP_TYPE1ST"]]." ".$row["YARN_COMP_PERCENT1ST"]."%";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trpo_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trpo_<? echo $i; ?>">
						<td><p><? echo $i; ?>&nbsp;</p></td>
						<td align="center"><?=$issue_basis[$row['ISSUE_BASIS']]?></td>
						<td align="center"><?=$row['REQUISITION_NO'];?></td>
						<td align="center"><?=$yarn_issue_purpose[$row['ISSUE_PURPOSE']]?></td>
						<td align="center"><?=$row['ISSUE_NUMBER_PREFIX_NUM']?></td>
						<td align="center"><? echo change_date_format($row['ISSUE_DATE']); ?>&nbsp;</td>
						<td ><? echo $lib_color_arr[$row['COLOR']]; ?>&nbsp;</td>
						<td ><? echo $yarn_composition; ?>&nbsp;</td>
						<td ><?=$row['LOT']?></td>
						<td align="right"><? echo number_format($row['ISS_QNTY'],2); ?>&nbsp;</td>
						<td ><? echo $row['REMARKS']; ?>&nbsp;</td>         
					</tr>
					<?
					$tot_iss_qnty+=$row['ISS_QNTY'];
					$i++;
				}
				?>
            </tbody>
			<tfoot>
				<tr>
					<th colspan="8"></th>
					<th>Total:</th>
					<th><? echo number_format($tot_iss_qnty,2); ?></th>
					<th></th>
				</tr>
			</tfoot>
    	</table>
		<br>
		<table width="1000" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
				<tr>
					<th colspan="11">Yarn Issue Return Pop Up</th>
				</tr>
                <tr>
                    <th width="20">SL</th>
                    <th width="100">Return Basis</th>
                    <th width="100">Job/Booking/lot</th>
                    <th width="100">Return Challan</th>
                    <th width="80">Return Id No</th>
                    <th width="80">Return Date</th>
                    <th width="80">Yarn Color</th>
                    <th width="150">Yarn Composition</th>
                    <th width="80">Y. Lot</th>
                    <th width="80">Return Qty.</th>
                    <th >Remarks</th>
                </tr>
            </thead>
            <tbody  id="table_body_popup">
            	<?
				$i=1;$tot_iss_rtn_qnty=0;
				foreach($iss_rtn_sql_res as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";			
					$yarn_composition=$composition[$row["YARN_COMP_TYPE1ST"]]." ".$row["YARN_COMP_PERCENT1ST"]."%";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trpo_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trpo_<? echo $i; ?>">
						<td><p><? echo $i; ?>&nbsp;</p></td>
						<td align="center"><?=$issue_basis[$row['RECEIVE_BASIS']]?></td>
						<td align="center"><?=$row['BOOKING_NO'];?></td>
						<td align="center"><?=$row['CHALLAN_NO'];?></td>
						<td align="center"><?=$row['RECV_NUMBER_PREFIX_NUM']?></td>
						<td align="center"><? echo change_date_format($row['RECEIVE_DATE']); ?>&nbsp;</td>
						<td ><? echo $lib_color_arr[$row['COLOR']]; ?>&nbsp;</td>
						<td ><? echo $yarn_composition; ?>&nbsp;</td>
						<td ><?=$row['LOT']?></td>
						<td align="right"><? echo number_format($row['ISS_RTN_QNTY'],2); ?>&nbsp;</td>
						<td ><? echo $row['REMARKS']; ?>&nbsp;</td>         
					</tr>
					<?
					$tot_iss_rtn_qnty+=$row['ISS_RTN_QNTY'];
					$i++;
				}
				?>
            </tbody>
			<tfoot>
				<tr>
					<th colspan="8"></th>
					<th>Total: </th>
					<th><? echo number_format($tot_iss_rtn_qnty,2); ?></th>
					<th></th>
				</tr>
				<tr>
					<th colspan="8"></th>
					<th>Net Issue: </th>
					<th><? echo number_format($tot_iss_qnty-$tot_iss_rtn_qnty,2); ?></th>
					<th></th>
				</tr>
			</tfoot>
    	</table>
    </div>
	<?
	exit();
}
disconnect($con);
?>