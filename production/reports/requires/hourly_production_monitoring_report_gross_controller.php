<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "","");  
	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 150, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/hourly_production_monitoring_report_gross_controller', this.value, 'load_drop_down_floor', 'floor_td' );",0 ); 
	exit();    	 
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor_id", 130, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );     	 	
	exit();    	 
}

if($action=="company_wise_report_button_setting")
{
	extract($_REQUEST);
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."'  and module_id=7 and report_id=239 and is_deleted=0 and status_active=1");
	//echo $print_report_format.jahid;die;
	//$field_name, $table_name, $query_cond, $return_fld_name, $new_conn
	$print_report_format_arr=explode(",",$print_report_format);

	echo "$('#Show').hide();\n";
	echo "$('#Show2').hide();\n";
	
	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==147){echo "$('#Show').show();\n";}
			if($id==259){echo "$('#Show2').show();\n";}
			if($id==242){echo "$('#Show3').show();\n";}
		}
	}
	else
	{
		echo "$('#Show').show();\n";
		echo "$('#Show2').show();\n";
		echo "$('#Show3').show();\n";
	}
	exit();
}

/*
if ($action=="load_drop_down_line")
{
	$explode_data = explode("_",$data);
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$explode_data[2] and variable_list=23 and is_deleted=0 and status_active=1");
	$txt_date = $explode_data[3];
	
	$cond="";
	if($prod_reso_allo==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_array=array();
		
		if($txt_date=="")
		{
			if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_id= $explode_data[1]";
			if( $explode_data[0]!=0 ) $cond = " and floor_id= $explode_data[0]";
			$line_data=sql_select("select id, line_number from prod_resource_mst where is_deleted=0 $cond");
		}
		else
		{
			if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and a.location_id= $explode_data[1]";
			if( $explode_data[0]!=0 ) $cond = " and a.floor_id= $explode_data[0]";
			
			$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date='".change_date_format($txt_date,'yyyy-mm-dd')."' and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id");
		}
		
		foreach($line_data as $row)
		{
			$line='';
			$line_number=explode(",",$row[csf('line_number')]);
			foreach($line_number as $val)
			{
				if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
			}
			$line_array[$row[csf('id')]]=$line;
		}

		echo create_drop_down( "cbo_line", 130,$line_array,"", 1, "-- Select Line --", $selected, "",0,0 );
	}
	else
	{
		if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_name= $explode_data[1]";
		if( $explode_data[0]!=0 ) $cond = " and floor_name= $explode_data[0]";

		echo create_drop_down( "cbo_line", 130, "select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name","id,line_name", 1, "-- Select Line --", $selected, "",0,0 );
	}
	exit();
}

*/

if($action=="job_wise_search")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
	<script>
			
			var selected_id = new Array;
			var selected_name = new Array;
			
			function check_all_data() {
				var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
				tbl_row_count = tbl_row_count - 0;
				for( var i = 1; i <= tbl_row_count; i++ ) {
					var onclickString = $('#tr_' + i).attr('onclick');
					var paramArr = onclickString.split("'");
					var functionParam = paramArr[1];
					js_set_value( functionParam );
					
				}
			}
			
			function toggle( x, origColor ) {
				var newColor = 'yellow';
				if ( x.style ) { 
					x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
				}
			}
			
			function js_set_value( strCon ) 
			{
				
					var splitSTR = strCon.split("_");
					var str = splitSTR[0];
					var selectID = splitSTR[1];
					var selectDESC = splitSTR[2];
					//$('#txt_individual_id' + str).val(splitSTR[1]);
					//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
					
					toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
					
					if( jQuery.inArray( selectID, selected_id ) == -1 ) {
						selected_id.push( selectID );
						selected_name.push( selectDESC );					
					}
					else {
						for( var i = 0; i < selected_id.length; i++ ) {
							if( selected_id[i] == selectID ) break;
						}
						selected_id.splice( i, 1 );
						selected_name.splice( i, 1 ); 
					}
					var id = ''; var name = ''; var job = '';
					for( var i = 0; i < selected_id.length; i++ ) {
						id += selected_id[i] + ',';
						name += selected_name[i] + ','; 
					}
					id 		= id.substr( 0, id.length - 1 );
					name 	= name.substr( 0, name.length - 1 ); 
					
					$('#txt_selected_id').val( id );
					$('#txt_selected').val( name ); 
					
			}
		</script>
	<?
	extract($_REQUEST);
	//echo $company;die;
	if($company==0) $company_name=""; else $company_name=" and b.company_name=$company";//job_no
	
	if($buyer==0) $buyer_name=""; else $buyer_name="and b.buyer_name=$buyer";
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$company and variable_list=23 and is_deleted=0 and status_active=1");
   if($prod_reso_allo==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_array=array();
		
		if($txt_date=="")
		{
			if(  $location!=0 ) $cond = " and a.location_id= $location";
			if( $floor_id!=0 ) $cond.= " and a.floor_id= $floor_id";
			$line_data="select a.id, b.line_name from prod_resource_mst a,lib_sewing_line b where a.is_deleted=0 and a.line_number=b.id $cond";
		}
		else
		{
			if(  $location!=0 ) $cond = " and a.location_id= $location";
			if( $floor_id!=0 ) $cond.= " and a.floor_id= $floor_id";
			
			$line_data="select a.id, c.line_name from prod_resource_mst a, prod_resource_dtls b,lib_sewing_line c where a.id=b.mst_id and a.line_number=c.id and b.pr_date='".change_date_format($txt_date,'yyyy-mm-dd')."' and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id";
		}
	


      echo create_list_view("list_view", "Line ","250","300","310",0, $line_data , "js_set_value", "id,line_name", "", 1, "0", $arr, "line_name", "","setFilterGrid('list_view',-1)","0","",1);	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
		
	}
	else
	{
		if( $location!=0  ) $cond = " and location_name= $location";
		if( $floor_id!=0 ) $cond.= " and floor_name= $floor_id";
		$line_data="select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name";
			
	echo create_list_view("list_view", "Line No","250","300","310",0, $line_data , "js_set_value", "id,line_name", "", 1, "0", $arr, "line_name", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	}
	exit();
}

if($action=="report_generate")
{ 
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
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$companyArr = return_library_array("select id,company_name from lib_company","id","company_name"); 
	$buyerArr = return_library_array("select id,short_name from lib_buyer","id","short_name"); 
	$locationArr = return_library_array("select id,location_name from lib_location","id","location_name"); 
	$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name"); 
	$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name"); 
	//$lineArr = return_library_array("select id,sewing_line_serial from lib_sewing_line","id","sewing_line_serial");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$comapny_id=str_replace("'","",$cbo_company_id);
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$cbo_company_id and variable_list=23 and is_deleted=0 and status_active=1");
	//echo $prod_reso_allo."eee";die;
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
	}
	if(str_replace("'","",$cbo_company_id)==0) $company_name=""; else $company_name="and a.company_id=".str_replace("'","",$cbo_company_id)."";
	if(str_replace("'","",$cbo_location_id)==0) $location=""; else $location="and a.location=".str_replace("'","",$cbo_location_id)."";
	if(str_replace("'","",$cbo_floor_id)==0) $floor=""; else $floor="and a.floor_id=".str_replace("'","",$cbo_floor_id)."";
    if(str_replace("'","",$hidden_line_id)==0) $line=""; else $line="and a.sewing_line in(".str_replace("'","",$hidden_line_id).")";
	if(str_replace("'","",trim($txt_date))=="") $txt_date_from=""; else $txt_date_from=" and a.production_date=$txt_date";
 if($prod_reso_allo==1)
 {
	
	$prod_resource_array=array();
	$dataArray=sql_select("select a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper, b.line_chief, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and a.id=b.mst_id and a.company_id=$comapny_id");
	foreach($dataArray as $val)
	{
		$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['man_power']=$val[csf('man_power')];
		$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['operator']=$val[csf('operator')];
		$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['helper']=$val[csf('helper')];
		$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['terget_hour']=$val[csf('target_per_hour')];
		$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['working_hour']=$val[csf('working_hour')];
		$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['tpd']=$val[csf('target_per_hour')]*$val[csf('working_hour')];
		$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['day_start']=$val[csf('from_date')];
		$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['day_end']=$val[csf('to_date')];
		$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['capacity']=$val[csf('capacity')];
	}
 }
	// print_r($prod_resource_array);die;
 //********************************************************************************************************************************************************
  if($db_type==0)
        {
           // $country_ship_date_fld="a.country_ship_date";
            $manufacturing_company=return_field_value("group_concat(comp.id) as company_id","lib_company as comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company_id");
        }
    else
        {
           // $country_ship_date_fld="to_char(a.country_ship_date,'YYYY-MM-DD')";
            $manufacturing_company=return_field_value("listagg(comp.id,',') within group (order by comp.id) as company_id","lib_company comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company_id");
        }
	
   

	//print_r($prod_resource_array);
	if($db_type==2)
		{
		$pr_date=str_replace("'","",$txt_date);
		$pr_date_old=explode("-",str_replace("'","",$txt_date));
		$month=strtoupper($pr_date_old[1]);
		$year=substr($pr_date_old[2],2);
		$pr_date=$pr_date_old[0]."-".$month."-".$year;
		}
	  if($db_type==0)
		{
		$pr_date=str_replace("'","",$txt_date);
		}
		//echo $pr_date;die;
	
	$i=1; $grand_total_good=0; $grand_alter_good=0; $grand_total_reject=0;
	$html="";
	$floor_html="";
    $check_arr=array();
	if($db_type==0)
		{
			
		$sql=sql_select( "select  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line,
		                 b.buyer_name  as buyer_name,
						 a.po_break_down_id, a.item_number_id,
					     c.po_number as po_number,
						sum(a.production_quantity) as good_qnty, 
						sum(CASE WHEN production_hour ='1' and a.production_type=5 THEN production_quantity else 0 END) AS good_1am,
						sum(CASE WHEN production_hour ='2' and a.production_type=5 THEN production_quantity else 0 END) AS good_2am,
						sum(CASE WHEN production_hour ='3' and a.production_type=5 THEN production_quantity else 0 END) AS good_3am,
						sum(CASE WHEN production_hour ='4' and a.production_type=5 THEN production_quantity else 0 END) AS good_4am,
						sum(CASE WHEN production_hour ='5' and a.production_type=5 THEN production_quantity else 0 END) AS good_5am,
						sum(CASE WHEN production_hour ='6' and a.production_type=5 THEN production_quantity else 0 END) AS good_6am,
						sum(CASE WHEN production_hour ='7' and a.production_type=5 THEN production_quantity else 0 END) AS good_7am,
						sum(CASE WHEN production_hour ='8' and a.production_type=5 THEN production_quantity else 0 END) AS good_8am,
						sum(CASE WHEN production_hour ='9' and a.production_type=5 THEN production_quantity else 0 END) AS good_9am,
						sum(CASE WHEN production_hour ='10' and a.production_type=5 THEN production_quantity else 0 END) AS good_10am,
						sum(CASE WHEN production_hour ='11' and a.production_type=5 THEN production_quantity else 0 END) AS good_11am,
						sum(CASE WHEN production_hour ='12' and a.production_type=5 THEN production_quantity else 0 END) AS good_12am,
						sum(CASE WHEN production_hour ='13' and a.production_type=5 THEN production_quantity else 0 END) AS good_1pm,
						sum(CASE WHEN production_hour ='14' and a.production_type=5 THEN production_quantity else 0 END) AS good_2pm,
						sum(CASE WHEN production_hour ='15' and a.production_type=5 THEN production_quantity else 0 END) AS good_3pm,
						sum(CASE WHEN production_hour ='16' and a.production_type=5 THEN production_quantity else 0 END) AS good_4pm,
						sum(CASE WHEN production_hour ='17' and a.production_type=5 THEN production_quantity else 0 END) AS good_5pm,
						sum(CASE WHEN production_hour ='18' and a.production_type=5 THEN production_quantity else 0 END) AS good_6pm,
						sum(CASE WHEN production_hour ='19' and a.production_type=5 THEN production_quantity else 0 END) AS good_7pm,
						sum(CASE WHEN production_hour ='20' and a.production_type=5 THEN production_quantity else 0 END) AS good_8pm,
						sum(CASE WHEN production_hour ='21' and a.production_type=5 THEN production_quantity else 0 END) AS good_9pm,
						sum(CASE WHEN production_hour ='22' and a.production_type=5 THEN production_quantity else 0 END) AS good_10pm,
						sum(CASE WHEN production_hour ='23' and a.production_type=5 THEN production_quantity else 0 END) AS good_11pm,
						sum(CASE WHEN production_hour ='24' and a.production_type=5 THEN production_quantity else 0 END) AS good_12pm
						from pro_gar_prod_gross_mst a, wo_po_details_master b, wo_po_break_down c
						where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0                          $company_name $location $floor $line   $txt_date_from 
						 group by a.company_id, a.location, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name,a.item_number_id,c.po_number order by a.location, a.floor_id,a.po_break_down_id");

	     	}
			
		if($db_type==2)
		{
			
		$sqlTxt="SELECT  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line,
		                 b.buyer_name  as buyer_name,
						 a.po_break_down_id, a.item_number_id,
					     c.po_number as po_number,b.style_ref_no,
						sum(a.production_quantity) as good_qnty, 
						sum(CASE WHEN production_hour ='1' and a.production_type=5 THEN production_quantity else 0 END) AS good_1am,
						sum(CASE WHEN production_hour ='2' and a.production_type=5 THEN production_quantity else 0 END) AS good_2am,
						sum(CASE WHEN production_hour ='3' and a.production_type=5 THEN production_quantity else 0 END) AS good_3am,
						sum(CASE WHEN production_hour ='4' and a.production_type=5 THEN production_quantity else 0 END) AS good_4am,
						sum(CASE WHEN production_hour ='5' and a.production_type=5 THEN production_quantity else 0 END) AS good_5am,
						sum(CASE WHEN production_hour ='6' and a.production_type=5 THEN production_quantity else 0 END) AS good_6am,
						sum(CASE WHEN production_hour ='7' and a.production_type=5 THEN production_quantity else 0 END) AS good_7am,
						sum(CASE WHEN production_hour ='8' and a.production_type=5 THEN production_quantity else 0 END) AS good_8am,
						sum(CASE WHEN production_hour ='9' and a.production_type=5 THEN production_quantity else 0 END) AS good_9am,
						sum(CASE WHEN production_hour ='10' and a.production_type=5 THEN production_quantity else 0 END) AS good_10am,
						sum(CASE WHEN production_hour ='11' and a.production_type=5 THEN production_quantity else 0 END) AS good_11am,
						sum(CASE WHEN production_hour ='12' and a.production_type=5 THEN production_quantity else 0 END) AS good_12am,
						sum(CASE WHEN production_hour ='13' and a.production_type=5 THEN production_quantity else 0 END) AS good_1pm,
						sum(CASE WHEN production_hour ='14' and a.production_type=5 THEN production_quantity else 0 END) AS good_2pm,
						sum(CASE WHEN production_hour ='15' and a.production_type=5 THEN production_quantity else 0 END) AS good_3pm,
						sum(CASE WHEN production_hour ='16' and a.production_type=5 THEN production_quantity else 0 END) AS good_4pm,
						sum(CASE WHEN production_hour ='17' and a.production_type=5 THEN production_quantity else 0 END) AS good_5pm,
						sum(CASE WHEN production_hour ='18' and a.production_type=5 THEN production_quantity else 0 END) AS good_6pm,
						sum(CASE WHEN production_hour ='19' and a.production_type=5 THEN production_quantity else 0 END) AS good_7pm,
						sum(CASE WHEN production_hour ='20' and a.production_type=5 THEN production_quantity else 0 END) AS good_8pm,
						sum(CASE WHEN production_hour ='21' and a.production_type=5 THEN production_quantity else 0 END) AS good_9pm,
						sum(CASE WHEN production_hour ='22' and a.production_type=5 THEN production_quantity else 0 END) AS good_10pm,
						sum(CASE WHEN production_hour ='23' and a.production_type=5 THEN production_quantity else 0 END) AS good_11pm,
						sum(CASE WHEN production_hour ='24' and a.production_type=5 THEN production_quantity else 0 END) AS good_12pm
						from pro_gar_prod_gross_mst a, wo_po_details_master b, wo_po_break_down c
						where a.po_break_down_id=c.id and c.job_id=b.id and a.production_type=5 and  a.status_active=1 and a.is_deleted=0                          $company_name $location $floor $line   $txt_date_from 
						 group by a.company_id, a.location, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name,a.item_number_id,c.po_number,b.style_ref_no order by a.location, a.floor_id,a.po_break_down_id";	
						echo $sqlTxt;
		$sql=sql_select($sqlTxt);

	     	}
			
	$production_data_arr=array();
	$production_po_data_arr=array();
	foreach($sql as $val)
	{
		$production_po_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf('good_qnty')];
		$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['prod_reso_allo']=$val[csf('prod_reso_allo')]; 
		if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name']!="")
			{
				  $production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name'].=",".$val[csf('buyer_name')]; 
			}
		else
			{
			   $production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name']=$val[csf('buyer_name')]; 
			}
		
		if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number']!="")
			{
				  $production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number'].=",".$val[csf('po_number')]; 
			}
		else
			{
			   $production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number']=$val[csf('po_number')]; 
			}
		if($production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']!="")
			{
				  $production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id'].="****".$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]; 
			}
		else
			{
			   $production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']=$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]; 
			}
		
		 
		$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['quantity']+=$val[csf('good_qnty')];
		$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['1am']+=$val[csf('good_1am')];  
		$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['2am']+=$val[csf('good_2am')];
		$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['3am']+=$val[csf('good_3am')]; 
		$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['4am']+=$val[csf('good_4am')];
		$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['5am']+=$val[csf('good_5am')]; 
		$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['6am']+=$val[csf('good_6am')];
		$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['7am']+=$val[csf('good_7am')]; 
		$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['8am']+=$val[csf('good_8am')];
		$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['9am']+=$val[csf('good_9am')]; 
		$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['10am']+=$val[csf('good_10am')];
		$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['11am']+=$val[csf('good_11am')]; 
		$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['12am']+=$val[csf('good_12am')];
		 
	   $production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['1pm']+=$val[csf('good_1pm')]; 
	   $production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['2pm']+=$val[csf('good_2pm')]; 
	   $production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['3pm']+=$val[csf('good_3pm')];
	   $production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['4pm']+=$val[csf('good_4pm')];
	   $production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['5pm']+=$val[csf('good_5pm')]; 
	   $production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['6pm']+=$val[csf('good_6pm')];
	   $production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['7pm']+=$val[csf('good_7pm')]; 
	   $production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['8pm']+=$val[csf('good_8pm')]; 
	   $production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['9pm']+=$val[csf('good_9pm')]; 
	   $production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['10pm']+=$val[csf('good_10pm')]; 
	   $production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['11pm']+=$val[csf('good_11pm')]; 
	   $production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['12pm']+=$val[csf('good_12pm')]; 
	   $style_wise_po_arr[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
	   $all_style_arr[$val[csf('style_ref_no')]]=$val[csf('style_ref_no')];
		
	}

	$smv_source=return_field_value("smv_source","variable_settings_production","company_name in( $manufacturing_company) and variable_list=25 and   status_active=1 and is_deleted=0");
	if($smv_source=="" || $smv_source==0) {$smv_source = 1;}
	
	// echo "SELECT smv_source from variable_settings_production where company_name =$comapny_id and variable_list=25 and   status_active=1 and is_deleted=0";	
	
	// echo $smv_source;die;
	if($smv_source==3) // from gsd enrty
	{
		$style_nos="'".implode("','",$all_style_arr)."'";
		$sql_item="SELECT a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID  from PPL_GSD_ENTRY_MST a where a.APPLICABLE_PERIOD <= $txt_date and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 and A.STYLE_REF in($style_nos)  
			group by a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID
				ORDER BY a.GMTS_ITEM_ID ,a.APPLICABLE_PERIOD  DESC , a.id DESC";
		$gsdSqlResult=sql_select($sql_item);
	
		/*foreach($resultItem as $itemData)
		{
			// $item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('sam_style')];
			$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('total_smv')];
		}*/

		foreach($gsdSqlResult as $rows)
		{
			foreach($style_wise_po_arr[$rows['STYLE_REF']] as $po_id)
			{
				if($item_smv_array[$po_id][$rows['GMTS_ITEM_ID']]=='')
				{
					$item_smv_array[$po_id][$rows['GMTS_ITEM_ID']]=$rows[TOTAL_SMV];
				}
			}
		}
	}
	else
	{
		$sql_item="SELECT b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, c.smv_pcs, c.smv_pcs_precost,c.smv_set from wo_po_details_master a,wo_po_break_down b, wo_po_details_mas_set_details c where b.job_id=a.id and b.job_id=c.job_id and a.company_name in($manufacturing_company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$resultItem=sql_select($sql_item);
		
		foreach($resultItem as $itemData)
		{
			if($smv_source==1)
			{
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs')];
			}
			if($smv_source==2)
			{
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs_precost')];
			}
		}
	}
			
	        $before_8_am=$production_hour1=$production_hour2=$production_hour3=$production_hour4=$production_hour5=$production_hour6=$production_hour7=$production_hour8=0;   
           $production_hour9=$production_hour10=$production_hour11=$production_hour12=$production_hour13=$production_hour14=$production_hour15=$production_hour16=$avable_min=0;
	$production_hour17=$production_hour18=$production_hour19=$production_hour20=$production_hour21=$production_hour22=$production_hour23=$production_hour24=$today_product=0;
	$floor_hour1=$floor_hour2=$floor_hour3=$floor_hour4=$floor_hour5=$floor_hour6==$floor_hour7=$floor_hour8=$floor_before_9am=0;  $floor_name="";   
    $floor_hour9=$floor_hour10=$floor_hour11=$floor_hour12=$floor_hour13=$floor_hour14=$floor_hour15=$floor_hour16=$floor_man_power=0;
	$floor_hour17=$floor_hour18=$floor_hour19=$floor_hour20=$floor_hour21=$floor_hour22=$floor_hour23=$floor_hour24=$floor_operator=$floor_produc_min=0;
	$floor_smv=$floor_row=$floor_helper=$floor_tgt_h=$floor_days_run=$floor_before_8_am=$floor_working_hour=$floor_ttl_tgt=$floor_today_product=$floor_avale_minute=0;
	$total_hour1=$total_hour2=$total_hour3=$total_hour4=$total_hour5=$total_hour6==$total_hour7=$total_hour8=$total_before_8am=$total_operator=$total_helper=$gnd_hit_rate=0;   
    $total_hour9=$total_hour10=$total_hour11=$total_hour12=$total_hour13=$total_hour14=$total_hour15=$total_hour16=$total_smv=$total_terget=$grand_total_product=$gnd_line_effi=0;
	$total_hour17=$total_hour18=$total_hour19=$total_hour20=$total_hour21=$total_hour22=$total_hour23=$total_hour24=$total_man_power=$gnd_avable_min=$gnd_product_min=0;
	$item_smv=$item_smv_total=$line_efficiency=$days_run=$total_working_hour=$gnd_total_tgt_h=$total_capacity=0;
	$j=1;
	ob_start();
	$line_number_check_arr=array();
	$smv_for_item="";
			
		  
	foreach($production_data_arr as $f_id=>$fname)
		{
		foreach($fname as $l_id=>$ldata)
			{
					
				  if($i!=1)
				 	 {
						  if(!in_array($f_id, $check_arr))
							{
								if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								 $html.='<tr  bgcolor="#B6B6B6">
									<td width="40"></td>
									<td width="80"> </td>
									<td width="80"> </td>
									<td width="80"></td>
									<td width="140"></td>
									<td width="120"></td>
									<td align="right" width="60">&nbsp;</td>
									<td align="right" width="70">'.$floor_operator.'&nbsp;</td>
									<td align="right" width="50">'. $floor_helper.'&nbsp;</td>
									<td align="right" width="60">'. $floor_man_power.'&nbsp;</td>
									<td align="right" width="70">'. $floor_tgt_h.'&nbsp;</td>
									<td align="right" width="60">'. $floor_days_run.'&nbsp;</td>
									<td align="right" width="70">'.$floor_capacity.'&nbsp;</td>
									<td align="right" width="60">'. $floor_working_hour.'&nbsp;</td>
									<td align="right" width="80">'. $floor_ttl_tgt.'&nbsp;</td>
									<td align="right" width="80">'.$floor_today_product.'&nbsp;</td>
									<td align="right" width="80">'. ($floor_today_product-$floor_ttl_tgt).'&nbsp;</td>
									<td align="right" width="100">'. $floor_avale_minute.'&nbsp;</td>
									<td align="right" width="100">'. $floor_produc_min.'&nbsp;</td>
									<td align="right" width="60">'. number_format(($floor_today_product/$floor_ttl_tgt)*100,2).'%&nbsp;</td>
									<td align="right" width="90">'.number_format($floor_efficency,2).' %&nbsp;</td>
									<td align="right" width="50">'. $floor_hour9.'&nbsp;</td>
									<td align="right" width="50">'. $floor_hour10.'&nbsp;</td>
									<td align="right" width="50">'. $floor_hour11.'&nbsp;</td>
									<td align="right" width="50">'.$floor_hour12.'&nbsp;</td>
									<td align="right" width="50">'.$floor_hour13.'&nbsp;</td>
									<td align="right" width="50">'. $floor_hour14.'&nbsp;</td>
									<td align="right" width="50">'. $floor_hour15.'&nbsp;</td>
									<td align="right" width="50">'. $floor_hour16.'&nbsp;</td>
									<td align="right" width="50">'. $floor_hour17.'&nbsp;</td>
									<td align="right" width="50">'. $floor_hour18.'&nbsp;</td>
									<td align="right" width="50">'. $floor_hour19.'&nbsp;</td>
									<td align="right" width="50">'.$floor_hour20.'&nbsp;</td>
									<td align="right" width="50">'. $floor_hour21.'&nbsp;</td>
									<td align="right" width="50">'.$floor_hour22.'&nbsp;</td>
									<td align="right" width="50">'. $floor_hour23.'&nbsp;</td>
									<td align="right" >'. $floor_hour24.'&nbsp;</td>
									
								</tr>';
						$floor_html.='<tbody>';
					    $floor_html.="<tr bgcolor='$bgcolor' onclick=change_color('floor_$j','$bgcolor') id=floor_$j>";
						$floor_html.='<td width="40">'.$j.'&nbsp;</td>
									<td width="80" align="center">'.$floor_name.'&nbsp; </td>
									<td width="70" align="right">'. $floor_tgt_h.'&nbsp;</td>
									<td width="70" align="right">'.$floor_capacity.'&nbsp;</td>
									<td align="right" width="60">'. $floor_man_power.'&nbsp;</td>
									<td width="70" align="right">'.$floor_operator.'&nbsp;</td>
									<td width="50" align="right">'. $floor_helper.'&nbsp;</td>
								    <td align="right" width="60">'. $floor_working_hour.'&nbsp;</td>
									<td align="right" width="80">'. $floor_ttl_tgt.'&nbsp;</td>
									<td align="right" width="80">'.$floor_today_product.'&nbsp;</td>
									<td align="right" width="80">'. ($floor_today_product-$floor_ttl_tgt).'&nbsp;</td>
									<td align="right" width="100">'. $floor_avale_minute.'&nbsp;</td>
									<td align="right" width="100">'. $floor_produc_min.'&nbsp;</td>
									<td align="right" width="90">'. number_format(($floor_today_product/$floor_ttl_tgt)*100,2).'%&nbsp;</td>
									<td align="right" width="90">'.number_format($floor_efficency,2).' %&nbsp;</td>
									
									
									<td align="right" width="50">'. $floor_hour9.'&nbsp;</td>
									<td align="right" width="50">'. $floor_hour10.'&nbsp;</td>
									<td align="right" width="50">'. $floor_hour11.'&nbsp;</td>
									<td align="right" width="50">'.$floor_hour12.'&nbsp;</td>
									<td align="right" width="50">'. $floor_hour13.'&nbsp;</td>
									<td align="right" width="50">'. $floor_hour14.'&nbsp;</td>
									<td align="right" width="50">'. $floor_hour15.'&nbsp;</td>
									<td align="right" width="50">'. $floor_hour16.'&nbsp;</td>
									<td align="right" width="50">'. $floor_hour17.'&nbsp;</td>
									<td align="right" width="50">'. $floor_hour18.'&nbsp;</td>
									<td align="right" width="50">'. $floor_hour19.'&nbsp;</td>
									<td align="right" width="50">'.$floor_hour20.'&nbsp;</td>
									<td align="right" width="50">'. $floor_hour21.'&nbsp;</td>
									<td align="right" width="50">'.$floor_hour22.'&nbsp;</td>
									<td align="right" width="50" >'. $floor_hour23.'&nbsp;</td>
									<td align="right" >'. $floor_hour24.'&nbsp;</td>
								</tr>';
								  $floor_name="";
								  $floor_smv=0;
								  $floor_row=0;
								  $floor_operator=0;
								  $floor_helper=0;
								  $floor_tgt_h=0;
								  $floor_man_power=0;
								  $floor_days_run=0;
								  $floor_before_9_am=0;
								  $floor_hour9=0;
								  $floor_hour10=0;
								  $floor_hour11=0;
								  $floor_hour12=0; 
								  $floor_hour13=0; 
								  $floor_hour14=0; 
								  $floor_hour15=0;
								  $floor_hour16=0;
								  $floor_hour17=0;
								  $floor_hour18=0;
								  $floor_hour19=0;
								  $floor_hour20=0; 
								  $floor_hour21=0; 
								  $floor_hour22=0; 
								  $floor_hour23=0;
								  $floor_hour24=0;
								  $floor_working_hour=0;
								  $floor_ttl_tgt=0;
								  $floor_today_product=0;
								  $floor_avale_minute=0;
								  $floor_produc_min=0;
								  $floor_efficency=0;
								  $floor_man_power=0;
								  $floor_capacity=0;
								  $j++;
							}
				    	 }
				$floor_row++;	
				
		
				
			    $po_number=array_unique(explode(',',$row[csf('po_number')]));
			    $germents_item=array_unique(explode('****',$ldata['item_number_id']));
				$buyer_neme_all=array_unique(explode(',',$ldata['buyer_name']));
				$buyer_name="";
			   foreach($buyer_neme_all as $buy)
				{
					if($buyer_name!='') $buyer_name.=',';
					$buyer_name.=$buyerArr[$buy];
					
				}
				$garment_itemname='';
			    $item_smv="";
				$smv_for_item="";
				$produce_minit="";
				$order_no_total="";
			   foreach($germents_item as $g_val)
				{
					$po_garment_item=explode('**',$g_val);
					if($garment_itemname!='') $garment_itemname.=',';
					$garment_itemname.=$garments_item[$po_garment_item[1]];
					if($item_smv!='') $item_smv.='/';
					$item_smv.=$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
					if($order_no_total!="") $order_no_total.=",";
					$order_no_total.=$po_garment_item[0];
					
				 if($smv_for_item!="") $smv_for_item.="****".$po_garment_item[0]."**".$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
				 else
					$smv_for_item=$po_garment_item[0]."**".$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];	
					
					$produce_minit+=$production_po_data_arr[$f_id][$l_id][$po_garment_item[0]]*$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
					
				}
			
			   $day_run_sql=sql_select("select min(production_date) as min_date from pro_garments_production_mst where po_break_down_id in(".$order_no_total.")  and production_type=4");
			    foreach($day_run_sql as $row_run)
				{
					$sewing_day=$row_run[csf('min_date')];
					
				}
				
					if($sewing_day!="")
					{
						$days_run= $diff=datediff("d",$sewing_day,$pr_date);
					}
				else  $days_run=0;
				
			
		
		    $sewing_line='';
		
			if($ldata['prod_reso_allo']==1)
			{
			
				$line_number=explode(",",$prod_reso_arr[$l_id]);
				foreach($line_number as $val)
				{
					if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
				}
			}
			
		   else $sewing_line=$lineArr[$l_id];
		        $total_eff_hour=0;
				$production_hour1=$ldata['1am'];   if($production_hour1!=0) $total_eff_hour+=1;
				$production_hour2=$ldata['2am'];   if($production_hour2!=0) $total_eff_hour+=1;
				$production_hour3=$ldata['3am'];   if($production_hour3!=0) $total_eff_hour+=1;
				$production_hour4=$ldata['4am'];   if($production_hour4!=0) $total_eff_hour+=1;
				$production_hour5=$ldata['5am'];   if($production_hour5!=0) $total_eff_hour+=1;
				$production_hour6=$ldata['6am'];   if($production_hour6!=0) $total_eff_hour+=1;
				$production_hour7=$ldata['7am'];   if($production_hour7!=0) $total_eff_hour+=1;
				$production_hour8=$ldata['8am'];   if($production_hour8!=0) $total_eff_hour+=1;
				$production_hour9=$ldata['9am'];   if($production_hour9!=0) $total_eff_hour+=1;
				$production_hour10=$ldata['10am']; if($production_hour10!=0) $total_eff_hour+=1;
				$production_hour11=$ldata['11am']; if($production_hour11!=0) $total_eff_hour+=1;
				$production_hour12=$ldata['12pm']; if($production_hour12!=0) $total_eff_hour+=1;
				$production_hour13=$ldata['1pm'];  if($production_hour13!=0) $total_eff_hour+=1;
				$production_hour14=$ldata['2pm'];  if($production_hour14!=0) $total_eff_hour+=1;
				$production_hour15=$ldata['3pm'];  if($production_hour15!=0) $total_eff_hour+=1;
				$production_hour16=$ldata['4pm'];  if($production_hour16!=0) $total_eff_hour+=1;
				$production_hour17=$ldata['5pm'];  if($production_hour17!=0) $total_eff_hour+=1;
				$production_hour18=$ldata['6pm'];  if($production_hour18!=0) $total_eff_hour+=1; 
				$production_hour19=$ldata['7pm'];  if($production_hour19!=0) $total_eff_hour+=1;
				$production_hour20=$ldata['8pm'];  if($production_hour20!=0) $total_eff_hour+=1;
				$production_hour21=$ldata['9pm'];  if($production_hour21!=0) $total_eff_hour+=1;
				$production_hour22=$ldata['10pm']; if($production_hour22!=0) $total_eff_hour+=1;
				$production_hour23=$ldata['11pm']; if($production_hour23!=0) $total_eff_hour+=1;
				$production_hour24=$ldata['12am']; if($production_hour24!=0) $total_eff_hour+=1;
				if($total_eff_hour>$prod_resource_array[$l_id][$pr_date]['working_hour'])
				 {
					 $total_eff_hour=$prod_resource_array[$l_id][$pr_date]['working_hour'];
				 }
				$before_8_am=$production_hour1+$production_hour2+$production_hour3+$production_hour4+$production_hour5+$production_hour6+$production_hour7+$production_hour8;
				$today_product=$before_8_am+$production_hour9+$production_hour10+$production_hour11+$production_hour12+$production_hour13+$production_hour14+$production_hour15+$production_hour16+$production_hour17+$production_hour18+$production_hour19+$production_hour20+$production_hour21+$production_hour22+$production_hour23+$production_hour24;
				if($sewing_day!="")
					{
						$days_run= $diff=datediff("d",$sewing_day,$pr_date);
					}
				else  $days_run=0;
				//$avable_min=($prod_resource_array[$l_id][$pr_date]['man_power'])*$prod_resource_array[$l_id][$pr_date]['working_hour']*60;
		       //******************************* line effiecency****************************************************************************['']
			   $eff_target=($prod_resource_array[$l_id][$pr_date]['terget_hour']*$total_eff_hour);
			   $efficiency_min=($prod_resource_array[$l_id][$pr_date]['man_power'])*$total_eff_hour*60;
				
			   $line_efficiency=(($produce_minit)*100)/$efficiency_min;
             //********************************* calclution floor total    ****************************************************$pr_date],$sewing_day
			    $floor_name=$floorArr[$f_id];	
			    $floor_hour24+=$production_hour24;
			    $floor_hour9+=$production_hour9;
				$floor_hour10+=$production_hour10;
			    $floor_hour11+=$production_hour11;
				$floor_hour12+=$production_hour12; 
				$floor_hour13+=$production_hour13; 
				$floor_hour14+=$production_hour14;
			    $floor_hour15+=$production_hour15;
				$floor_hour16+=$production_hour16;
				$floor_hour17+=$production_hour17;
			    $floor_hour18+=$production_hour18;
				$floor_hour19+=$production_hour19; 
				$floor_hour20+=$production_hour20;
				$floor_hour21+=$production_hour21;
			    $floor_hour22+=$production_hour22;
				$floor_hour23+=$production_hour23; 
				$floor_before_8_am+=$before_8_am;
				$floor_smv+=$item_smv;
				$floor_capacity+=$prod_resource_array[$l_id][$pr_date]['capacity'];
				$floor_operator+=$prod_resource_array[$l_id][$pr_date]['operator'];
				$floor_helper+=$prod_resource_array[$l_id][$pr_date]['helper'];
				$floor_tgt_h+=$prod_resource_array[$l_id][$pr_date]['terget_hour'];	
	            $floor_days_run+=$days_run;
				$floor_working_hour+=$prod_resource_array[$l_id][$pr_date]['working_hour']; 
				$floor_ttl_tgt+=$eff_target;
				$floor_today_product+=$today_product;
				$floor_avale_minute+=$efficiency_min;
				$floor_produc_min+=$produce_minit; 
				$floor_efficency=($floor_produc_min/$floor_avale_minute)*100;
				$floor_man_power+=$prod_resource_array[$l_id][$pr_date]['man_power'];
					//**************************** calclution total **************************************************************************************************************
			    $total_hour24+=$production_hour24;
			    $total_hour9+=$production_hour9;
				$total_hour10+=$production_hour10;
			    $total_hour11+=$production_hour11;
				$total_hour12+=$production_hour12; 
				$total_hour13+=$production_hour13;
				$total_hour14+=$production_hour14;
			    $total_hour15+=$production_hour15;
				$total_hour16+=$production_hour16;
				$total_hour17+=$production_hour17;
			    $total_hour18+=$production_hour18;
				$total_hour19+=$production_hour19; 
				$total_hour20+=$production_hour20;
				$total_hour21+=$production_hour21;
			    $total_hour22+=$production_hour22;
				$total_hour23+=$production_hour923; 
				$total_before_8am+=$before_8_am;
				$total_capacity+=$prod_resource_array[l_id][$pr_date]['capacity'];
				$gnd_total_tgt_h+=$prod_resource_array[$l_id][$pr_date]['terget_hour'];	
				$total_working_hour+=$prod_resource_array[$l_id][$pr_date]['working_hour']; 
				$total_operator+=$prod_resource_array[$l_id][$pr_date]['operator'];
				$total_helper+=$prod_resource_array[$l_id][$pr_date]['helper'];
				$total_man_power+=$prod_resource_array[$l_id][$pr_date]['man_power'];
				//$total_smv+=$item_smv;
				$total_terget+=$eff_target;
				$grand_total_product+=$today_product;
				$gnd_avable_min+=$efficiency_min;
				$gnd_product_min+=$produce_minit; 
				//$gnd_hit_rate=($grand_total_product/$total_terget)*100;
				//$gnd_line_effi=($gnd_product_min/$gnd_avable_min)*100;
				
			
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					     $html.='<tbody>';
					     $html.="<tr bgcolor='$bgcolor' onclick=change_color('tr_$i','$bgcolor') id=tr_$i>";
					     $html.='<td width="40">'.$i.'&nbsp;</td>
								 <td width="80">'.$floor_name.'&nbsp; </td>
                                 <td align="center" width="80" >'. $sewing_line.'&nbsp; </td>
                                 <td width="80"><p>'.$buyer_name.'&nbsp;</p></td>
                                 <td width="140"><p>'.$ldata['po_number'].'&nbsp;</p></td>
                                 <td width="120"><p>'.$garment_itemname.'&nbsp;<p/> </td>
								 <td align="right" width="60"><p>'.$item_smv.'&nbsp;</p></td>
                                 <td align="right" width="70">'.$prod_resource_array[$l_id][$pr_date]['operator'].'&nbsp;</td>
                                 <td align="right" width="50">'.$prod_resource_array[$l_id][$pr_date]['helper'].'&nbsp;</td>
                                 <td align="right" width="60">'.$prod_resource_array[$l_id][$pr_date]['man_power'].'&nbsp;</td>
                                 <td align="right" width="70">'.$prod_resource_array[$l_id][$pr_date]['terget_hour'].'&nbsp;</td>
								 <td align="right" width="60">'.$days_run.'&nbsp;</td>
                                 <td align="right" width="70">'.$prod_resource_array[$l_id][$pr_date]['capacity'].'&nbsp;</td>
								 <td align="right" width="60">'.$prod_resource_array[$l_id][$pr_date]['working_hour'].'&nbsp;</td>
                                 <td align="right" width="80">'. $eff_target.'&nbsp;</td>
								 
								 
								 <td width="75" align="right"><a href="##" onclick="openmypage('.$cbo_company_id.",'".$order_no_total."',".$f_id.",".$l_id.",'tot_prod','".$smv_for_item."',".$txt_date.')">'.$today_product.'</a>&nbsp;</td>
								 
								 
								
								 <td align="right" width="80">'. ($today_product-$eff_target).'&nbsp;</td>
							     <td align="right" width="100">'.$efficiency_min.'&nbsp;</td>
                                 <td width="100" align="right"><a href="##" onclick="openmypage('.$cbo_company_id.",".$f_id.",".$l_id.",'tot_prod','".$smv_for_item."',".$txt_date.')">'.$produce_minit.'</a>&nbsp;</td>
								
								
								 
                                 <td align="right" width="60">'. number_format(($today_product/$eff_target)*100,2).' %&nbsp;</td>
								 <td align="right" width="90">'.number_format($line_efficiency,2). '%&nbsp;</td>
                              
								 <td align="right" width="50">'. $production_hour9.'&nbsp;</td>
                                 <td align="right" width="50">'.$production_hour10.'&nbsp;</td>
                                 <td align="right" width="50">'. $production_hour11.'&nbsp;</td>
                                 <td align="right" width="50">'. $production_hour12.'&nbsp;</td>
								  <td align="right" width="50">'. $production_hour13.'&nbsp;</td>
                                 <td align="right" width="50">'. $production_hour14.'&nbsp;</td>
                                 <td align="right" width="50">'. $production_hour15.'&nbsp;</td>
                                 <td align="right" width="50">'. $production_hour16.'&nbsp;</td>
                                 <td align="right" width="50">'. $production_hour17.'&nbsp;</td>
                                 <td align="right" width="50">'. $production_hour18.'&nbsp;</td>
                                 <td align="right" width="50">'. $production_hour19.'&nbsp;</td>
                                 <td align="right" width="50">'. $production_hour20.'&nbsp;</td>
                                 <td align="right" width="50">'. $production_hour21.'&nbsp;</td>
                                 <td align="right" width="50">'. $production_hour22.'&nbsp;</td>
                                 <td align="right" width="50">'.$production_hour23.'&nbsp;</td>
								 <td align="right" >'. $production_hour24.'&nbsp;</td>
								 
							</tr>
						 </tbody>';
				$i++;
				$check_arr[]=$f_id;
				  }
             }
				 $html.='<tr  bgcolor="#B6B6B6">
									<td width="40"></td>
									<td width="80"> </td>
									<td width="80"> </td>
									<td width="80"></td>
									<td width="140"></td>
									<td width="120"></td>
									<td align="right" width="60">&nbsp;</td>
									<td align="right" width="70">'.$floor_operator.'&nbsp;</td>
									<td align="right" width="50">'. $floor_helper.'&nbsp;</td>
									<td align="right" width="60">'. $floor_man_power.'&nbsp;</td>
									<td align="right" width="70">'. $floor_tgt_h.'&nbsp;</td>
									<td align="right" width="60">'. $floor_days_run.'&nbsp;</td>
									<td align="right" width="70">&nbsp;</td>
									<td align="right" width="60">'. $floor_working_hour.'&nbsp;</td>
									<td align="right" width="80">'. $floor_ttl_tgt.'&nbsp;</td>
									<td align="right" width="80">'.$floor_today_product.'&nbsp;</td>
									<td align="right" width="80">'. ($floor_today_product-$floor_ttl_tgt).'&nbsp;</td>
									<td align="right" width="100">'. $floor_avale_minute.'&nbsp;</td>
									<td align="right" width="100">'. $floor_produc_min.'&nbsp;</td>
									<td align="right" width="60">'. number_format(($floor_today_product/$floor_ttl_tgt)*100,2).'%&nbsp;</td>
									<td align="right" width="90">'.number_format($floor_efficency,2).' %&nbsp;</td>
									
									
									<td align="right" width="50">'. $floor_hour9.'&nbsp;</td>
									<td align="right" width="50">'. $floor_hour10.'&nbsp;</td>
									<td align="right" width="50">'. $floor_hour11.'&nbsp;</td>
									<td align="right" width="50">'.$floor_hour12.'&nbsp;</td>
									<td align="right" width="50">'.$floor_hour13.'&nbsp;</td>
									<td align="right" width="50">'. $floor_hour14.'&nbsp;</td>
									<td align="right" width="50">'. $floor_hour15.'&nbsp;</td>
									<td align="right" width="50">'. $floor_hour16.'&nbsp;</td>
									<td align="right" width="50">'. $floor_hour17.'&nbsp;</td>
									<td align="right" width="50">'. $floor_hour18.'&nbsp;</td>
									<td align="right" width="50">'. $floor_hour19.'&nbsp;</td>
									<td align="right" width="50">'.$floor_hour20.'&nbsp;</td>
									<td align="right" width="50">'. $floor_hour21.'&nbsp;</td>
									<td align="right" width="50">'.$floor_hour22.'&nbsp;</td>
									<td align="right" width="50" >'. $floor_hour23.'&nbsp;</td>
									<td align="right" >'. $floor_hour24.'&nbsp;</td>
									
								</tr>';
					   $floor_html.="<tr bgcolor='$bgcolor' onclick=change_color('floor_$j','$bgcolor') id=floor_$j>";
					   $floor_html.='<td width="40">'.$j.'&nbsp;</td>
									<td width="80" align="center">'.$floor_name.'&nbsp; </td>
									<td width="70" align="right">'. $floor_tgt_h.'&nbsp;</td>
									<td width="70" align="right">'.$floor_capacity.'&nbsp;</td>
									<td align="right" width="60">'. $floor_man_power.'&nbsp;</td>
									<td width="70" align="right">'.$floor_operator.'&nbsp;</td>
									<td width="50" align="right">'. $floor_helper.'&nbsp;</td>
									<td align="right" width="60">'. $floor_working_hour.'&nbsp;</td>
									<td align="right" width="80">'. $floor_ttl_tgt.'&nbsp;</td>
									<td align="right" width="80">'.$floor_today_product.'&nbsp;</td>
									<td align="right" width="80">'. ($floor_today_product-$floor_ttl_tgt).'&nbsp;</td>
									<td align="right" width="100">'. $floor_avale_minute.'&nbsp;</td>
									<td align="right" width="100">'. $floor_produc_min.'&nbsp;</td>
									<td align="right" width="90">'. number_format(($floor_today_product/$floor_ttl_tgt)*100,2).'%&nbsp;</td>
									<td align="right" width="90">'.number_format($floor_efficency,2).' %&nbsp;</td>
									
									
									<td align="right" width="50">'. $floor_hour9.'&nbsp;</td>
									<td align="right" width="50">'. $floor_hour10.'&nbsp;</td>
									<td align="right" width="50">'. $floor_hour11.'&nbsp;</td>
									<td align="right" width="50">'.$floor_hour12.'&nbsp;</td>
									<td align="right" width="50">'.$floor_hour13.'&nbsp;</td>
									<td align="right" width="50">'. $floor_hour14.'&nbsp;</td>
									<td align="right" width="50">'. $floor_hour15.'&nbsp;</td>
									<td align="right" width="50">'. $floor_hour16.'&nbsp;</td>
									<td align="right" width="50">'. $floor_hour17.'&nbsp;</td>
									<td align="right" width="50">'. $floor_hour18.'&nbsp;</td>
									<td align="right" width="50">'. $floor_hour19.'&nbsp;</td>
									<td align="right" width="50">'.$floor_hour20.'&nbsp;</td>
									<td align="right" width="50">'. $floor_hour21.'&nbsp;</td>
									<td align="right" width="50">'.$floor_hour22.'&nbsp;</td>
									<td align="right" >'. $floor_hour23.'&nbsp;</td>
									<td align="right" width="50">'. $floor_hour24.'&nbsp;</td>
								</tr></tbody>';
                      $smv_for_item="";
	
				?>
               
	<fieldset style="width:2450px">
       <table width="2200" cellpadding="0" cellspacing="0"> 
            <tr class="form_caption">
                <td colspan="25" align="center"><strong><? echo $report_title; ?></strong></td> 
            </tr>
            <tr class="form_caption">
                <td colspan="25" align="center"><strong><? echo $companyArr[$comapny_id]; ?></strong></td> 
            </tr>
            <tr class="form_caption">
                <td colspan="25" align="center"><strong><? echo "Date:  ".change_date_format( str_replace("'","",trim($txt_date)) ); ?></strong></td> 
            </tr>
        </table>
        <br />
        <label> <strong>Report Sumarry:-</strong></label> 
          <table id="table_header_2" class="rpt_table" width="1940" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr height="50">
                    <th width="40">SL</th>
                    <th width="80">Floor Name</th>
                    <th width="70">Hourly Terget</th>
                    <th width="70">Capacity</th>
                    <th width="60">Total Man  Power</th>
                    <th width="70">Operator</th>
                    <th width="50">Helper</th>
                    <th width="60">Line Hour</th>
                    <th width="80">Day Target</th>
                    <th width="80">Total Prod.</th>
                    <th width="80">Variance </th>
                    <th width="100">SMV Available</th>
                    <th width="100">SMV Achieved</th>
                    <th width="90">Achievement %</th>
                    <th width="90">Floor Eff. %</th>
                   
                    
                    <th width="50" style="vertical-align:middle"><div class="block_div">9 AM</div></th>
                    <th width="50" style="vertical-align:middle"><div class="block_div">10 AM</div></th>
                    <th width="50" style="vertical-align:middle"><div class="block_div">11 AM</div></th>
                    <th width="50" style="vertical-align:middle"><div class="block_div">12 PM</div></th>
                    <th width="50" style="vertical-align:middle"><div class="block_div">1 PM</div></th>
                    <th width="50" style="vertical-align:middle"><div class="block_div">2 PM</div></th>
                    <th width="50" style="vertical-align:middle"><div class="block_div">3 PM</div></th>
                    <th width="50" style="vertical-align:middle"><div class="block_div">4 PM</div></th>
                    <th width="50" style="vertical-align:middle"><div class="block_div">5 PM</div></th>
                    <th width="50" style="vertical-align:middle"><div class="block_div">6 PM</div></th>
                    <th width="50" style="vertical-align:middle"><div class="block_div">7 PM</div></th>
                    <th width="50" style="vertical-align:middle"><div class="block_div">8 PM</div></th>
                    <th width="50" style="vertical-align:middle"><div class="block_div">9 PM</div></th>
                    <th width="50" style="vertical-align:middle"><div class="block_div">10 PM</div></th>
                    <th  style="vertical-align:middle"><div class="block_div">11 PM</div></th>
                    <th width="50" style="vertical-align:middle"><div class="block_div">12 AM</div></th>
                </tr>
            </thead>
        </table>
        <div style="width:1960px; max-height:400px; overflow-y:scroll" id="scroll_body">
           <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
           <?  echo $floor_html; ?> 
            <tfoot>
                   <tr>
                        <th width="40"></th>
                        <th width="80">Total </th>
                        <th width="70"><? echo $gnd_total_tgt_h;   ?> </th>
                        <th width="70" align="right"><? echo $total_capacity; ?> </th>
                        <th width="60"><? echo $total_man_power; ?>&nbsp;</th>
                        <th width="70"><? echo $total_operator; ?></th>
                        <th width="50"><? echo $total_helper; ?></th>
                        <th align="right" width="60"><? echo $total_working_hour; ?>&nbsp;</th>
                        <th align="right" width="80"><? echo $total_terget; ?>&nbsp;</th>
                        <th align="right" width="80"><? echo $grand_total_product; ?>&nbsp;</th>
                        <th align="right" width="80"><? echo $grand_total_product-$total_terget; ?>&nbsp;</th>
                        <th align="right" width="100"><? echo $gnd_avable_min; ?>&nbsp;</th>
                        <th align="right" width="100"><? echo $gnd_product_min; ?>&nbsp;</th>
                        <th align="right" width="90"><?    echo number_format(($grand_total_product/$total_terget)*100,2)."%"; ?>&nbsp;</th>
                        <th align="center" width="90"><?    echo number_format(($gnd_product_min/$gnd_avable_min)*100,2)."%"; ?>&nbsp;</th>
                        <th align="right" width="50"><? echo $total_hour9; ?>&nbsp;</th>
                        <th align="right" width="50"><? echo $total_hour10; ?>&nbsp;</th>
                        <th align="right" width="50"><? echo $total_hour11; ?>&nbsp;</th>
                        <th align="right" width="50"><? echo $total_hour12; ?>&nbsp;</th>
                        <th align="right" width="50"><? echo $total_hour13; ?>&nbsp;</th>
                        <th align="right" width="50"><? echo $total_hour14; ?>&nbsp;</th>
                        <th align="right" width="50"><? echo $total_hour15; ?>&nbsp;</th>
                        <th align="right" width="50"><? echo $total_hour16; ?>&nbsp;</th>
					    <th align="right" width="50"><? echo $total_hour17; ?>&nbsp;</th>
                        <th align="right" width="50"><? echo $total_hour18; ?>&nbsp;</th>
                        <th align="right" width="50"><? echo $total_hour19; ?>&nbsp;</th>
                        <th align="right" width="50"><? echo $total_hour20; ?>&nbsp;</th>
                        <th align="right" width="50"><? echo $total_hour21; ?>&nbsp;</th>
                        <th align="right" width="50"><? echo $total_hour22; ?>&nbsp;</th>
                        <th align="right" width="50"><? echo $total_hour23; ?>&nbsp;</th>
					     <th align="right" ><? echo $total_hour24; ?>&nbsp;</th>
                    </tr>
                </tfoot>
            
           </table>
        
        </div>
    </br><br/>
        <table id="table_header_1" class="rpt_table" width="2470" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr height="50">
                    <th width="40">SL</th>
                    <th width="80">Floor Name</th>
                    <th width="80">Line No</th>
                    <th width="80">Buyer</th>
                    <th width="140">Order No</th>
                    <th width="120">Garments Item</th>
                    <th width="60">SMV</th>
                    <th width="70">Operator</th>
                    <th width="50">Helper</th>
                    <th width="60"> Man Power</th>
                    <th width="70">Hourly Terget</th>
                    <th width="60">Days Run</th>
                    <th width="70">Capacity</th>
                    <th width="60">Working Hour</th>
                    <th width="80">Total Target</th>
                    <th width="80">Total Prod.</th>
                    <th width="80">Variance pcs </th>
                    <th width="100">Available Minutes</th>
                    <th width="100">Produce Minutes</th>
                    <th width="60">Target Hit rate</th>
                    <th  width="90">Line Effi %</th>
                    <th width="50" style="vertical-align:middle"><div class="block_div">9 AM</div></th>
                    <th width="50" style="vertical-align:middle"><div class="block_div">10 AM</div></th>
                    <th width="50" style="vertical-align:middle"><div class="block_div">11 AM</div></th>
                    <th width="50" style="vertical-align:middle"><div class="block_div">12 PM</div></th>
                    <th width="50" style="vertical-align:middle"><div class="block_div">1 PM</div></th>
                    <th width="50" style="vertical-align:middle"><div class="block_div">2 PM</div></th>
                    <th width="50" style="vertical-align:middle"><div class="block_div">3 PM</div></th>
                    <th width="50" style="vertical-align:middle"><div class="block_div">4 PM</div></th>
                    <th width="50" style="vertical-align:middle"><div class="block_div">5 PM</div></th>
                    <th width="50" style="vertical-align:middle"><div class="block_div">6 PM</div></th>
                    <th width="50" style="vertical-align:middle"><div class="block_div">7 PM</div></th>
                    <th width="50" style="vertical-align:middle"><div class="block_div">8 PM</div></th>
                    <th width="50" style="vertical-align:middle"><div class="block_div">9 PM</div></th>
                    <th width="50" style="vertical-align:middle"><div class="block_div">10 PM</div></th>
                    <th  style="vertical-align:middle"><div class="block_div">11 PM</div></th>
                    <th width="50" style="vertical-align:middle"><div class="block_div">12 AM</div></th>
                </tr>
            </thead>
        </table>
        <div style="width:2490px; max-height:400px; overflow-y:scroll" id="scroll_body">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
              <? echo $html;  ?>
                <tfoot>
                   <tr>
                        <th width="40"></th>
                        <th width="80"> </th>
                        <th width="80"> </th>
                        <th width="80"></th>
                        <th width="140"></th>
                        <th width="120">Total</th>
                        <th align="right" width="60"><? // echo number_format($total_smv/($i-1),2); ?>&nbsp;</th>
                        <th align="right" width="70"><? echo $total_operator; ?>&nbsp;</th>
                        <th align="right" width="50"><? echo $total_helper; ?>&nbsp;</th>
                        <th align="right" width="60"><? echo $total_man_power; ?>&nbsp;</th>
                        <th align="right" width="70"><?  echo $gnd_total_tgt_h; ?>&nbsp;</th>
                        <th align="right" width="60"><? //echo $prod_resource_array[$row[csf('sewing_line')]][$pr_date]['man_power']; ?>&nbsp;</th>
                        <th align="right" width="70"><? echo $total_capacity; ?></th>
                        <th align="right" width="60"><? echo $total_working_hour; ?></th>
                        <th align="right" width="80"><? echo $total_terget; ?>&nbsp;</th>
                        <th align="right" width="80"><? echo $grand_total_product; ?>&nbsp;</th>
                        <th align="right" width="80"><? echo $grand_total_product-$total_terget; ?>&nbsp;</th>
                        <th align="right" width="100"><? echo $gnd_avable_min; ?>&nbsp;</th>
                        <th align="right" width="100"><? echo $gnd_product_min; ?>&nbsp;</th>
                        <th align="right" width="60"><?    echo number_format(($grand_total_product/$total_terget)*100,2)."%"; ?>&nbsp;</th>
                        <th align="right" width="90" ><?     echo number_format(($gnd_product_min/$gnd_avable_min)*100,2)."%";?>&nbsp;</th>
                        <th align="right" width="50"><? echo $total_hour9; ?>&nbsp;</th>
                        <th align="right" width="50"><? echo $total_hour10; ?>&nbsp;</th>
                        <th align="right" width="50"><? echo $total_hour11; ?>&nbsp;</th>
                        <th align="right" width="50"><? echo $total_hour12; ?>&nbsp;</th>
                        <th align="right" width="50"><? echo $total_hour13; ?>&nbsp;</th>
                        <th align="right" width="50"><? echo $total_hour14; ?>&nbsp;</th>
                        <th align="right" width="50"><? echo $total_hour15; ?>&nbsp;</th>
                        <th align="right" width="50"><? echo $total_hour16; ?>&nbsp;</th>
					    <th align="right" width="50"><? echo $total_hour17; ?>&nbsp;</th>
                        <th align="right" width="50"><? echo $total_hour18; ?>&nbsp;</th>
                        <th align="right" width="50"><? echo $total_hour19; ?>&nbsp;</th>
                        <th align="right" width="50"><? echo $total_hour20; ?>&nbsp;</th>
                        <th align="right" width="50"><? echo $total_hour21; ?>&nbsp;</th>
                        <th align="right" width="50"><? echo $total_hour22; ?>&nbsp;</th>
                        <th align="right" width="50"><? echo $total_hour23; ?>&nbsp;</th>
					    <th align="right" ><? echo $total_hour24; ?>&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
		</div>
	</fieldset>
	<?    
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename,'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data####$filename";
	exit();      
}

if($action=="report_generate_date") //2nd Button Start...
{
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
            .break_all
            {
            	word-wrap: break-word;
            	word-break: break-all;
            }
          
        </style> 
	<?
	extract($_REQUEST);
	$process = array( &$_POST );
	
	//change_date_format($txt_date);die;
	
	if($db_type==0)	$txt_date=change_date_format($txt_date,'yyyy-mm-dd');
	if($db_type==2)	$txt_date=change_date_format($txt_date,'','',1);
	$txt_date="'".$txt_date."'";
	
	extract(check_magic_quote_gpc( $process ));
	$companyArr = return_library_array("select id,company_name from lib_company","id","company_name"); 
	$buyerArr = return_library_array("select id,short_name from lib_buyer","id","short_name"); 
	$locationArr = return_library_array("select id,location_name from lib_location","id","location_name"); 
	$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");

	$floor_sl_Arr = return_library_array("select id,floor_serial_no from lib_prod_floor where PRODUCTION_PROCESS=5","id","floor_serial_no");

	//print_r($floor_sl_Arr);

	$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name"); 
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$comapny_id=str_replace("'","",$cbo_company_id);
    $today_date=date("Y-m-d");
	$txt_producting_day="".str_replace("'","",$txt_date)."";
	
	//***************************************************************************************************************************
	$lineDataArr = sql_select("select id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1
	order by sewing_line_serial"); 
	foreach($lineDataArr as $lRow)
	{
		$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
		$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
		$lastSlNo=$lRow[csf('sewing_line_serial')];
	}
	if($db_type==0)
	{
		$min_shif_start=return_field_value("min(TIME_FORMAT(d.prod_start_time, '%H:%i' ))  as line_start_time","prod_resource_mst a,prod_resource_dtls  b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id=$comapny_id and shift_id=1 and pr_date=$txt_date and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");	
	}
	else
	{
		$min_shif_start=return_field_value("min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time","prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id=$comapny_id and shift_id=1 and pr_date=$txt_date and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");
	}
	
	if($min_shif_start=="")
	{
		echo "<p style='font-size:20px', align='center'>No Line Engage for the selected Date.Please Check Actual Production Resource Entry.<p/>";die;
		
	}
	
	
	//==============================shift time===================================================================================================
	$start_time_arr=array();
	if($db_type==0)
	{
		$start_time_data_arr=sql_select("select company_name, shift_id, TIME_FORMAT( prod_start_time, '%H:%i' ) as prod_start_time,TIME_FORMAT( lunch_start_time, '%H:%i' ) as lunch_start_time from variable_settings_production where company_name in($comapny_id) and shift_id=1  and variable_list=26 and status_active=1 and is_deleted=0");
	}
	else
	{
		$start_time_data_arr=sql_select("select company_name, shift_id, TO_CHAR(prod_start_time,'HH24:MI') as prod_start_time,TO_CHAR(lunch_start_time,'HH24:MI') as lunch_start_time from variable_settings_production where  company_name in($comapny_id) and  shift_id=1 and variable_list=26 and status_active=1 and is_deleted=0");	
	}
	
	foreach($start_time_data_arr as $row)
	{
		$start_time_arr[$row[csf('shift_id')]]['pst']=$row[csf('prod_start_time')];
		$start_time_arr[$row[csf('shift_id')]]['lst']=$row[csf('lunch_start_time')];
	}
	
	$prod_start_hour=$start_time_arr[1]['pst'];
	$global_start_lanch=$start_time_arr[1]['lst'];
	if($prod_start_hour=="") $prod_start_hour="08:00";
	$start_time=explode(":",$prod_start_hour);
	// $hour=substr($start_time[0],1,1); 
	$hour=$start_time[0]*1; 
	$minutes=$start_time[1]; $last_hour=23;
	$lineWiseProd_arr=array(); $prod_arr=array(); $start_hour_arr=array();
	$start_hour=$prod_start_hour;
	$start_hour_arr[$hour]=$start_hour;
	for($j=$hour;$j<$last_hour;$j++)
	{
		$start_hour=add_time($start_hour,60);
		$start_hour_arr[$j+1]=substr($start_hour,0,5);
	}
	//echo $pc_date_time;die;
	$start_hour_arr[$j+1]='23:59';
	if($prod_start_hour>$min_shif_start)  $prod_start_hour=$min_shif_start;
	$actual_date=date("Y-m-d");
	$actual_production_date=date("Y-m-d",strtotime(str_replace("'","",$txt_date)));
	$actual_time=substr(date("Y-m-d H:i:s",strtotime($pc_date_time)),11,2);	
	$acturl_hour_minute=date("H:i",strtotime($pc_date_time));	
	$generated_hourarr=array();
	$first_hour_time=explode(":",$min_shif_start);
	$hour_line=substr($first_hour_time[0],1,1); $minutes_one=$start_time[1];
	$line_start_hour_arr[$hour_line]=$min_shif_start;
	
	for($l=$hour_line;$l<$last_hour;$l++)
	{
		$min_shif_start=add_time($min_shif_start,60);
		$line_start_hour_arr[$l+1]=substr($min_shif_start,0,5);
	}
	
	$line_start_hour_arr[$j+1]='23:59';
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$cbo_company_id and variable_list=23 and is_deleted=0 
	and status_active=1");
	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and b.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else  $buyer_id_cond="";
		}
		else
		{
		$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and b.buyer_name=$cbo_buyer_name";
	}
	
	//if(str_replace("'","",$cbo_company_id)==0) $company_name=""; else $company_name="and a.company_id=".str_replace("'","",$cbo_company_id)."";
	if(str_replace("'","",$cbo_company_id)==0) $company_name=""; else $company_name="and a.serving_company=".str_replace("'","",$cbo_company_id)."";
	
	if(str_replace("'","",$cbo_location_id)==0) 
	{
		$subcon_location="";
		$location="";
	}
	else 
	{
		$location=" and a.location=".str_replace("'","",$cbo_location_id)."";
		$subcon_location=" and a.location_id=".str_replace("'","",$cbo_location_id)."";
	}
	$cbo_floor_id=str_replace("'","",$cbo_floor_id);
	if($cbo_floor_id==0) $floor=""; else $floor="and a.floor_id in(".$cbo_floor_id.")";
    if(str_replace("'","",$hidden_line_id)==0)
	{ 
		$line=""; 
		$subcon_line="";
	}
	else 
	{
		$subcon_line="and a.line_id in(".str_replace("'","",$hidden_line_id).")";
		$line="and a.sewing_line in(".str_replace("'","",$hidden_line_id).")";
	}
	$cbo_no_prod_type=str_replace("'","",$cbo_no_prod_type);
	$file_no=str_replace("'","",$txt_file_no);
	$ref_no=str_replace("'","",$txt_ref_no);
	if($file_no!="") $file_cond="and c.file_no=$file_no";else $file_cond="";
	if($ref_no!="") $ref_cond="and c.grouping='$ref_no'";else $ref_cond="";
	//echo $file_cond;
	
	if(str_replace("'","",trim($txt_date))=="") $txt_date_from=""; else $txt_date_from=" and a.production_date=$txt_date";
	// echo $txt_date_from; die;
	
	if($prod_reso_allo[0]==1)
	{
		$prod_resource_array=array();

		$dataArray_sql=sql_select("SELECT a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper,b.smv_adjust,b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and c.id=b.mast_dtl_id and a.company_id=$comapny_id and b.pr_date=$txt_date and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0");
		foreach($dataArray_sql as $val)
		{
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['man_power']=$val[csf('man_power')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['operator']=$val[csf('operator')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['helper']=$val[csf('helper')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['terget_hour']=$val[csf('target_per_hour')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['working_hour']=$val[csf('working_hour')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['tpd']=$val[csf('target_per_hour')]*$val[csf('working_hour')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['day_start']=$val[csf('from_date')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['day_end']=$val[csf('to_date')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['capacity']=$val[csf('capacity')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['smv_adjust']=$val[csf('smv_adjust')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['smv_adjust_type']=$val[csf('smv_adjust_type')];
		}
		//var_dump($prod_resource_array);
		if(str_replace("'","",trim($txt_date))==""){$pr_date_con="";}else{$pr_date_con=" and b.pr_date=$txt_date";}

		if($db_type==0)
		{
			$dataArray=sql_select("SELECT a.id,b.pr_date,d.shift_id,TIME_FORMAT( d.prod_start_time, '%H:%i' ) as prod_start_time,TIME_FORMAT( d.lunch_start_time, '%H:%i' ) as lunch_start_time from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 $pr_date_con"); 
		}
		else
		{
			$dataArray=sql_select("SELECT a.id,b.pr_date,d.shift_id,TO_CHAR(d.prod_start_time,'HH24:MI') as prod_start_time, TO_CHAR( d.lunch_start_time,'HH24:MI') as lunch_start_time from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 $pr_date_con");
		}
		
		$line_number_arr=array();
		foreach($dataArray as $val)
		{
			$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['shift_id']=$val[csf('shift_id')];
			$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['prod_start_time']=$val[csf('prod_start_time')];
			$line_number_arr[$val[csf('id')]][$val[csf('pr_date')]]['lunch_start_time']=$val[csf('lunch_start_time')];
		}
	}
 	//******************************************************************************************************************
  	if($db_type==0)
	{
		$manufacturing_company=return_field_value("group_concat(comp.id) as company_id","lib_company as comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company_id");
	}
	else
	{
		$manufacturing_company=return_field_value("listagg(comp.id,',') within group (order by comp.id) as company_id","lib_company comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company_id");
	}
	
	
	if($db_type==0) $prod_start_cond="prod_start_time";
	else if($db_type==2) $prod_start_cond="TO_CHAR(prod_start_time,'DD-MON-YYYY HH24:MI')";
	
	$variable_start_time_arr='';
	$prod_start_time=sql_select("select $prod_start_cond as prod_start_time from variable_settings_production where company_name=$cbo_company_id and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1");
	//echo "select company_name, prod_start_time from variable_settings_production where company_name=$cbo_company_id and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1";
	foreach($prod_start_time as $row)
	{
		$ex_time=explode(" ",$row[csf('prod_start_time')]);
		if($db_type==0) $variable_start_time_arr=$row[csf('prod_start_time')];
		else if($db_type==2) $variable_start_time_arr=$ex_time[1];
	}//die;
	//echo $variable_start_time_arr;
	unset($prod_start_time);
	$current_date_time=date('d-m-Y H:i');
	$variable_date=change_date_format(str_replace("'","",$txt_date)).' '.$variable_start_time_arr;
	//echo $variable_date.'='.$current_date_time;
	$datediff=datediff("n",$variable_date,$current_date_time);
	
	$ex_date_time=explode(" ",$current_date_time);
	$current_date=$ex_date_time[0];
	$current_time=$ex_date_time[1];
	$ex_time=explode(":",$current_time);
	
	$search_prod_date=change_date_format(str_replace("'","",$txt_date));
	$current_eff_min=($ex_time[0]*60)+$ex_time[1];
	//echo $current_date.'='.$search_prod_date;
	$variable_time= explode(":",$variable_start_time_arr);
	$vari_min=($variable_time[0]*60)+$variable_time[1];
	$difa_time=explode(".",number_format(($current_eff_min-$vari_min)/60,2));//datediff("",$ctime,$variable_start_time_arr);
	$dif_time=number_format($datediff/60,2);
	$dif_hour_min=date("H", strtotime($dif_time));
	
   	$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($manufacturing_company) and variable_list=25 and   status_active=1 and is_deleted=0");
	// echo $smv_source;
    if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
	
    if($smv_source==3)
	{
		$sql_item="select b.id, a.sam_style, a.gmts_item_id from ppl_gsd_entry_mst a, wo_po_break_down b where b.job_no_mst=a.po_job_no and a.is_deleted=0 
	and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$resultItem=sql_select($sql_item);
	
		foreach($resultItem as $itemData)
		{
			$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('sam_style')];
		}
	}
	else
	{
		 $sql_item="SELECT b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, c.smv_pcs, c.smv_pcs_precost,c.smv_set from wo_po_details_master a,wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no and a.company_name in($manufacturing_company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$resultItem=sql_select($sql_item);
		
		foreach($resultItem as $itemData)
		{
			if($smv_source==1)
			{
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs')];
			}
			if($smv_source==2)
			{
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs_precost')];
			}
		}
	}
 
	if($db_type==2)
	{
		$pr_date=str_replace("'","",$txt_date);
		$pr_date_old=explode("-",str_replace("'","",$txt_date));
		$month=strtoupper($pr_date_old[1]);
		$year=substr($pr_date_old[2],2);
		$pr_date=$pr_date_old[0]."-".$month."-".$year;
	}
	if($db_type==0)
	{
		$pr_date=str_replace("'","",$txt_date);
	}
	
	$i=1; $grand_total_good=0; $grand_alter_good=0; $grand_total_reject=0;
	$html="";
	$floor_html="";
    $check_arr=array();
	
	if($db_type==0) //a.production_date
	{
		$sql="select  a.company_id, a.location, a.production_date,a.floor_id,d.floor_serial_no,e.sewing_line_serial, a.prod_reso_allo, a.production_date, a.sewing_line,
		b.buyer_name  as buyer_name,b.style_ref_no,b.job_no,a.po_break_down_id, a.item_number_id,c.po_number as po_number,c.unit_price,c.file_no,c.grouping as ref,sum(a.production_quantity) as good_qnty,"; 
		$first=1;
		for($h=$hour;$h<$last_hour;$h++)
		{
			$bg=$start_hour_arr[$h];
			$end=substr(add_time($start_hour_arr[$h],60),0,5);
			$prod_hour="prod_hour".substr($bg,0,2);
			if($first==1)
			{
				$sql.="sum(CASE WHEN   a.production_hour<='$end' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,";
			}
			else
			{
				$sql.="sum(CASE WHEN a.production_hour>'$bg' and  a.production_hour<='$end' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,";
			}
			$first=$first+1;
		}
		$sql.="sum(CASE WHEN  a.production_hour>'$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]' and a.production_type=5 THEN production_quantity else 0 END) AS prod_hour23 from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a left join lib_prod_floor d on a.floor_id=d.id and d.is_deleted=0 left join lib_sewing_line e on a.sewing_line=e.id and e.is_deleted=0  where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $company_name $location $floor $line $buyer_id_cond  $txt_date_from $file_cond $ref_cond group by b.job_no,a.company_id, a.location, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date, a.sewing_line,e.sewing_line_serial,b.buyer_name,a.production_date,a.item_number_id,c.po_number,c.file_no,c.unit_price,c.grouping,b.style_ref_no order by a.location, a.floor_id,e.sewing_line_serial,a.prod_reso_allo";
	}
	else if($db_type==2)
	{
		$sql="SELECT  a.company_id, a.location, a.floor_id,a.production_date, a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name  as buyer_name,b.style_ref_no,b.job_no, a.po_break_down_id, a.item_number_id,c.po_number as po_number,c.file_no,c.unit_price,c.grouping as ref,sum(d.production_qnty) as good_qnty,"; 
		$first=1;
		for($h=$hour;$h<$last_hour;$h++)
		{
			$bg=$start_hour_arr[$h];
			$end=substr(add_time($start_hour_arr[$h],60),0,5);
			$prod_hour="prod_hour".substr($bg,0,2);
			if($first==1)
			{
				$sql.="sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN production_qnty else 0 END) AS $prod_hour,";
			}
			else
			{
				$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 
				THEN production_qnty else 0 END) AS $prod_hour,";
			}
			$first++;
		}
		$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN production_qnty else 0 END) AS prod_hour23 
		FROM  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a ,pro_garments_production_dtls d
		WHERE a.production_type=5 and a.po_break_down_id=c.id and c.job_id=b.id and a.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_name $location $floor $line $buyer_id_cond  $txt_date_from $file_cond $ref_cond 
		GROUP BY b.job_no, a.company_id, a.location,a.production_date, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name,b.style_ref_no,a.item_number_id,c.po_number,c.unit_price,c.file_no,c.grouping 
		ORDER BY a.location,a.floor_id,a.sewing_line";
	}
	// echo $sql;die;
	$sql_resqlt=sql_select($sql);
	$production_data_arr=array();
	$production_po_data_arr=array();
	$production_serial_arr=array(); $reso_line_ids=''; $all_po_id="";
	$active_days_arr=array();
	$duplicate_date_arr=array();
	foreach($sql_resqlt as $val)
	{
		 $prod_date=$val[csf('production_date')];
		if($val[csf('prod_reso_allo')]==1)
		{
			$sewing_line_id=$prod_reso_arr[$val[csf('sewing_line')]];
			$reso_line_ids.=$val[csf('sewing_line')].',';
		}
		else
		{
			$sewing_line_id=$val[csf('sewing_line')];
		}
		
		if($lineSerialArr[$sewing_line_id]=="")
		{
			$lastSlNo++;
			$slNo=$lastSlNo;
			$lineSerialArr[$sewing_line_id]=$slNo;
		}
		else $slNo=$lineSerialArr[$sewing_line_id];
		$production_serial_arr[$prod_date][$floor_sl_Arr[$val[csf('floor_id')]]][$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]]=$val[csf('sewing_line')];
		
		$line_start=$line_number_arr[$val[csf('sewing_line')]][$val[csf('production_date')]]['prod_start_time'];
		if($line_start!="") 
		{ 
			$line_start_hour=substr($line_start,0,2); 
			if(substr($line_start_hour,0,1)==0)  $line_start_hour=substr($line_start_hour,1,1);	
		}
		else
		{
			$line_start_hour=$hour; 
		}
		
	 	for($h=$hour;$h<$last_hour;$h++)
		{
			$prod_hour="prod_hour".substr($start_hour_arr[$h],0,2)."";
			$production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]][$prod_hour]+=$val[csf($prod_hour)]; 
			
			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date)) 
			{
				if( $h>=$line_start_hour && $h<=$actual_time)
				{
					$production_po_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf($prod_hour)]; 
				} 	
			}
			
			if(str_replace("'","",$actual_production_date)<str_replace("'","",$actual_date)) 
			{	
				$production_po_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf($prod_hour)];
			}
		}
		
		if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date)) 
		{	
			if( $h>=$line_start_hour && $h<=$actual_time)
			{
				$production_po_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf('prod_hour23')];     
			} 	
		}
		else
		{
			$production_po_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf('prod_hour23')];     
		}
		
	 	$production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['prod_hour23']+=$val[csf('prod_hour23')];  
		$production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['prod_reso_allo']=$val[csf('prod_reso_allo')]; 
		
	 	if($production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name']!="")
		{
			$production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name'].=",".$val[csf('buyer_name')]; 
		}
	 	else
		{
			$production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['buyer_name']=$val[csf('buyer_name')]; 
		}
	
	 	if($production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number']!="")
		{
			$production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number'].=",".$val[csf('po_number')];
			$production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['job_no'].=",".$val[csf('job_no')];
			$production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_id'].=",".$val[csf('po_break_down_id')];
			$production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['style'].=",".$val[csf('style_ref_no')];
			$production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['file'].=",".$val[csf('file_no')];
			$production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['ref'].=",".$val[csf('ref')]; 
		}
	 	else
		{
			$production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_number']=$val[csf('po_number')];
			$production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['job_no']=$val[csf('job_no')];
			$production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['po_id']=$val[csf('po_break_down_id')]; 
			$production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['style']=$val[csf('style_ref_no')]; 
			$production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['file']=$val[csf('file_no')]; 
			$production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['ref']=$val[csf('ref')]; 
		}
		$fob_rate_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['rate']=$val[csf('unit_price')]; 
		
		if($production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']!="")
		{
			$production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id'].="****".$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]; 
		}
		else
		{
			 $production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']=$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]; 
		}
		$production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]]['quantity']+=$val[csf('good_qnty')];
		$production_data_arr_qty[$prod_date][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]]['quantity']+=$val[csf('good_qnty')];
		
		if($all_po_id=="") $all_po_id=$val[csf('po_break_down_id')]; else $all_po_id.=",".$val[csf('po_break_down_id')];
	}
	//echo "<pre>"; print_r($production_serial_arr);die();

	$po_ids=count(array_unique(explode(",",$all_po_id)));
	$po_numIds=chop($all_po_id,',');
	$poIds_cond="";
	$poIds_cond2="";
	if($all_po_id!='' || $all_po_id!=0)
	{
		if($db_type==2 && $po_ids>1000)
		{
			$poIds_cond=" and (";
			$poIds_cond2=" and (";
			$poIdsArr=array_chunk(explode(",",$po_numIds),990);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$poIds_cond.=" b.id  in($ids) or ";
				$poIds_cond2.=" c.id  in($ids) or ";
			}
			$poIds_cond=chop($poIds_cond,'or ');
			$poIds_cond2=chop($poIds_cond,'or ');
			$poIds_cond.=")";
			$poIds_cond2.=")";
		}
		else
		{
			$poIds_cond=" and  b.id  in($all_po_id)";
			$poIds_cond2=" and  c.id  in($all_po_id)";
		}
	}


    $po_active_sql="SELECT a.floor_id,a.sewing_line,a.production_date,a.po_break_down_id,a.item_number_id from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and  a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $company_name $location $floor $line $buyer_id_cond   $file_cond $ref_cond $poIds_cond2 group by  a.floor_id,a.sewing_line,a.production_date ,a.po_break_down_id,a.item_number_id";
    //echo $po_active_sql;die;
	foreach(sql_select($po_active_sql) as $vals)
	{
		$prod_dates=$vals[csf('production_date')];
		if($duplicate_date_arr[$vals[csf('po_break_down_id')]][$vals[csf('item_number_id')]][$prod_dates]=="")
		{
			$active_days_arr[$vals[csf('floor_id')]][$vals[csf('sewing_line')]]+=1;
			$active_days_arr_powise[$vals[csf('po_break_down_id')]][$vals[csf('item_number_id')]]+=1;
			$duplicate_date_arr[$vals[csf('po_break_down_id')]][$vals[csf('item_number_id')]][$prod_dates]=$prod_dates;
		}

	}
	//print_r($duplicate_date_arr);
		
	$sql_item_rate="select b.id, c.item_number_id, c.order_quantity, c.order_total from wo_po_details_master a,wo_po_break_down b, wo_po_color_size_breakdown c where b.job_no_mst=a.job_no and b.id=c.po_break_down_id and b.job_no_mst=c.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1  $file_cond $ref_cond  $poIds_cond";
	$resultRate=sql_select($sql_item_rate);
	$item_po_array=array();
	foreach($resultRate as $row)
	{
		$item_po_array[$row[csf('id')]][$row[csf('item_number_id')]]['qty']+=$row[csf('order_quantity')];
		$item_po_array[$row[csf('id')]][$row[csf('item_number_id')]]['amt']+=$row[csf('order_total')];
	}
	
	// subcoutact data **********************************************************************************************************************
	
    if($db_type==0)
    {
		$sql_sub_contuct= "select  a.company_id, a.location_id,d.floor_serial_no,e.sewing_line_serial,a.prod_reso_allo, a.floor_id, a.production_date, a.line_id,b.party_id  as buyer_name,a.order_id,c.order_no as po_number,c.cust_style_ref,b.subcon_job as job_no, max(c.smv) as smv,sum(a.production_qnty) as good_qnty,"; 
		
		$first=1;
		for($h=$hour;$h<$last_hour;$h++)
		{
			$bg=$start_hour_arr[$h];
			$end=substr(add_time($start_hour_arr[$h],60),0,5);
			$prod_hour="prod_hour".substr($bg,0,2);
			if($first==1)
			{
				$sql_sub_contuct.="sum(CASE WHEN  a.hour<='$end' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,";
			}
			else
			{
				$sql_sub_contuct.="sum(CASE WHEN a.hour>'$bg' and a.hour<='$end' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,";	
			}
			$first=$first+1;
   		}
   		$sql_sub_contuct.="sum(CASE WHEN  a.hour>'$start_hour_arr[$last_hour]' and a.hour<='$start_hour_arr[24]' and a.production_type=2 THEN a.production_qnty else 0 END) AS prod_hour23 from subcon_ord_mst b, subcon_ord_dtls c,subcon_gmts_prod_dtls a left join lib_prod_floor d on a.floor_id=d.id and d.is_deleted=0 left join lib_sewing_line e on a.line_id=e.id and e.is_deleted=0 where a.production_type=2 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.company_id=$comapny_id $subcon_location $floor $subcon_line   $txt_date_from group by a.company_id, a.location_id, a.floor_id,d.floor_serial_no,a.order_id, a.production_date,a.prod_reso_allo,a.line_id,b.party_id,c.order_no,c.cust_style_ref,b.subcon_job ,e.sewing_line_serial order by a.location_id,d.floor_serial_no,e.sewing_line_serial,a.prod_reso_allo";
	}
	else
	{
		$sql_sub_contuct= "select  a.company_id, a.location_id, a.production_date,a.floor_id,d.floor_serial_no,e.sewing_line_serial,  a.line_id,b.party_id  as buyer_name,a.prod_reso_allo,a.order_id,c.order_no as po_number,c.cust_style_ref, b.subcon_job as job_no,  max(c.smv) as smv,a.prod_reso_allo,sum(a.production_qnty) as good_qnty,"; 
		//cho $sql_sub_contuct;die;
		$first=1;
		for($h=$hour;$h<$last_hour;$h++)
		{
			$bg=$start_hour_arr[$h];
			$end=substr(add_time($start_hour_arr[$h],60),0,5);
			$prod_hour="prod_hour".substr($bg,0,2);
			if($first==1)
			{
				$sql_sub_contuct.="sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')<='$end' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,";
			}
			else
			{
				$sql_sub_contuct.="sum(CASE WHEN TO_CHAR(a.hour,'HH24:MI')>'$bg' and TO_CHAR(a.hour,'HH24:MI')<='$end' and a.production_type=2 THEN a.production_qnty else 0 END) AS $prod_hour,";	
			}
			$first++;
		}
		
	   	$sql_sub_contuct.="sum(CASE WHEN  TO_CHAR(a.hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.hour,'HH24:MI')<='$start_hour_arr[24]'	and a.production_type=2 THEN a.production_qnty else 0 END) AS prod_hour23 from subcon_ord_mst b, subcon_ord_dtls c,subcon_gmts_prod_dtls a left join lib_prod_floor d on a.floor_id=d.id  and d.is_deleted=0 left join lib_sewing_line e on a.line_id=e.id and e.is_deleted=0 where a.production_type=2 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.company_id=$comapny_id $subcon_location $floor $subcon_line   $txt_date_from group by a.company_id, a.location_id, a.floor_id,d.floor_serial_no,a.order_id, a.production_date,  b.subcon_job,a.line_id,b.party_id,c.order_no,c.cust_style_ref,e.sewing_line_serial,a.prod_reso_allo order by a.location_id, a.floor_id,e.sewing_line_serial,a.prod_reso_allo";
		
	}
	//echo $sql_sub_contuct;die;
	$sub_result=sql_select($sql_sub_contuct);
	$subcon_order_smv=array();		
	foreach($sub_result as $subcon_val)
	{
		$prod_date=$val[csf('production_date')];
		if($val[csf('prod_reso_allo')]==1)
		{
			$sewing_line_id=$prod_reso_arr[$subcon_val[csf('sewing_line')]];
		}
		else
		{
			$sewing_line_id=$subcon_val[csf('sewing_line')];
		}
		
		if($lineSerialArr[$sewing_line_id]=="")
		{
			$lastSlNo++;
			$slNo=$lastSlNo;
			$lineSerialArr[$sewing_line_id]=$slNo;
		}
		else $slNo=$lineSerialArr[$sewing_line_id];
		//$production_serial_arr[$subcon_val[csf('floor_id')]][$slNo][$subcon_val[csf('sewing_line')]]=$subcon_val[csf('sewing_line')];
		
		$production_po_data_arr[$prod_date][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$subcon_val[csf('good_qnty')];
		if($production_data_arr[$prod_date][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['buyer_name']!="")
		{
			$production_data_arr[$prod_date][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['buyer_name'].=",".$subcon_val[csf('buyer_name')]; 
		}
		else
		{
			$production_data_arr[$prod_date][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['buyer_name']=$subcon_val[csf('buyer_name')]; 
		}
	
		if($production_data_arr[$prod_date][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['po_number']!="")
		{
			$production_data_arr[$prod_date][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['po_number'].=",".$subcon_val[csf('po_number')];
			$production_data_arr[$prod_date][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['job_no'].=",".$subcon_val[csf('job_no')];
			$production_data_arr[$prod_date][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['style'].=",".$subcon_val[csf('cust_style_ref')];  
		}
		else
		{
			$production_data_arr[$prod_date][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['po_number']=$subcon_val[csf('po_number')]; 
			$production_data_arr[$prod_date][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['job_no']=$subcon_val[csf('job_no')]; 
			$production_data_arr[$prod_date][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['style']=$subcon_val[csf('cust_style_ref')]; 
		}
	
		if($production_data_arr[$prod_date][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['order_id']!="")
		{
			$production_data_arr[$prod_date][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['order_id'].=",".$subcon_val[csf('order_id')]; 
		}
		else
		{
			$production_data_arr[$prod_date][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['order_id'].=$subcon_val[csf('order_id')]; 
		}
		$subcon_order_smv[$subcon_val[csf('order_id')]]=$subcon_val[csf('smv')];
		$production_data_arr[$prod_date][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['quantity']+=$subcon_val[csf('good_qnty')];
		
	 	$line_start=$line_number_arr[$val[csf('line_id')]][$val[csf('production_date')]]['prod_start_time']	;
	 	if($line_start!="") 
	 	{ 
			$line_start_hour=substr($line_start,0,2); 
			if(substr($line_start_hour,0,1)==0)  $line_start_hour=substr($line_start_hour,1,1);	
	 	}
		else
	 	{
			$line_start_hour=$hour; 
	 	}
		for($h=$hour;$h<=$last_hour;$h++)
		{
			$prod_hour="prod_hour".substr($start_hour_arr[$h],0,2).""; 
			$production_data_arr[$prod_date][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$prod_hour]+=$subcon_val[csf($prod_hour)]; 
			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date)) 
			{
				 if( $h>=$line_start_hour && $h<=$actual_time)
				 {
				 $production_po_data_arr[$prod_date][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$val[csf($prod_hour)];	                 } 
			}
			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date)) 
			{
				$production_po_data_arr[$prod_date][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$val[csf($prod_hour)];	            }
		 }
		if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date)) 
		{	
			if( $h>=$line_start_hour && $h<=$actual_time)
			{
				$production_po_data_arr[$prod_date][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$val[csf('prod_hour23')];
			} 	
		}
		else
		{
			$production_po_data_arr[$prod_date][$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$val[csf('prod_hour23')];
		}
		$production_data_arr[$prod_date][$val[csf('floor_id')]][$val[csf('line_id')]]['prod_hour23']+=$val[csf('prod_hour23')];
	}
	//For Summary Report New Add No Prodcut
	if($cbo_no_prod_type==1)
	{
		//No Production line Start ....
		$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
		$sql_active_line=sql_select("select sewing_line,sum(production_quantity) as total_production from pro_garments_production_mst  where production_date=".$txt_date." and production_type=5 and status_active=1 and is_deleted=0 and prod_reso_allo=1 group by  sewing_line");
				//$actual_line_arr=array();
				foreach($sql_active_line as $inf)
				{	
				   if(str_replace("","",$inf[csf('sewing_line')])!="")
				   {
						//if(str_replace("","",$actual_line_arr)!="") $actual_line_arr.=",";
					    //$actual_line_arr.="'".$inf[csf('sewing_line')]."'";
				   }
				}
						//echo $actual_line_arr;die;
			$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$comapny_id and variable_list=23 and is_deleted=0 and status_active=1");
			$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
			if(str_replace("'","",$cbo_location_id)==0) 
			{
			$location_cond="";
			}
			else 
			{
			$location=" and a.location_id=".str_replace("'","",$cbo_location_id)."";
			}
			
			if(str_replace("'","",$cbo_floor_id)==0) $floor=""; else $floor="and a.floor_id=".str_replace("'","",$cbo_floor_id)."";
			$lin_ids=str_replace("'","",$hidden_line_id);
			$res_line_cond=rtrim($reso_line_ids,",");
			
			 $dataArray_sum=sql_select("SELECT a.id, a.floor_id,1 as prod_reso_allo,2 as type_line, a.line_number as line_no, b.man_power, b.operator, b.helper, b.working_hour,b.target_per_hour,b.smv_adjust,b.smv_adjust_type,d.prod_start_time,b.pr_date from  prod_resource_dtls b,prod_resource_dtls_time d,prod_resource_mst a  where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and b.pr_date=".$txt_date." and d.shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 and a.id not in($res_line_cond) and a.line_number like '%$lin_ids%'  $location  $floor  group by a.id, a.floor_id, a.line_number,b.pr_date, d.prod_start_time,b.man_power, b.operator, b.helper, b.working_hour,b.target_per_hour,b.smv_adjust,b.smv_adjust_type order by a.floor_id");
			 //echo $dataArray_sum;die;
			 //echo "select a.id, a.floor_id,1 as prod_reso_allo,2 as type_line, a.line_number as line_no, b.man_power, b.operator, b.helper, b.working_hour,b.target_per_hour,b.smv_adjust,b.smv_adjust_type,d.prod_start_time from  prod_resource_dtls b,prod_resource_dtls_time d,prod_resource_mst a  where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$comapny_id and b.pr_date=".$txt_date." and d.shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 and a.id not in($res_line_cond) and a.line_number like '%$lin_ids%'  $location  $floor  group by a.id, a.floor_id, a.line_number, d.prod_start_time,b.man_power, b.operator, b.helper, b.working_hour,b.target_per_hour,b.smv_adjust,b.smv_adjust_type order by a.floor_id";die;
			 $no_prod_line_arr=array();
			 foreach( $dataArray_sum as $row)
			 { 
			 
				$sewing_line_id=$row[csf('line_no')];
				$prod_date=$row[csf('pr_date')];
				if($lineSerialArr[$sewing_line_id]=="")
				{
					$lastSlNo++;
					$slNo=$lastSlNo;
					$lineSerialArr[$sewing_line_id]=$slNo;
				}
				else $slNo=$lineSerialArr[$sewing_line_id];

				 $production_serial_arr[$prod_date][$floor_sl_Arr[$row[csf('floor_id')]]][$row[csf('floor_id')]][$slNo][$row[csf('id')]]=$row[csf('id')]; 

				 $production_data_arr[$prod_date][$row[csf('floor_id')]][$row[csf('id')]]['type_line']=$row[csf('type_line')];

				 $production_data_arr[$prod_date][$row[csf('floor_id')]][$row[csf('id')]]['prod_reso_allo']=$row[csf('prod_reso_allo')]; 
				 $production_data_arr[$prod_date][$row[csf('floor_id')]][$row[csf('id')]]['man_power']=$row[csf('man_power')]; 
				 $production_data_arr[$prod_date][$row[csf('floor_id')]][$row[csf('id')]]['operator']=$row[csf('operator')]; 
				 $production_data_arr[$prod_date][$row[csf('floor_id')]][$row[csf('id')]]['helper']=$row[csf('helper')]; 
				 $production_data_arr[$prod_date][$row[csf('floor_id')]][$row[csf('id')]]['working_hour']=$row[csf('working_hour')];						
				 $production_data_arr[$prod_date][$row[csf('floor_id')]][$row[csf('id')]]['terget_hour']=$row[csf('target_per_hour')];
				 $production_data_arr[$prod_date][$row[csf('floor_id')]][$row[csf('id')]]['total_line_hour']=$row[csf('man_power')]*$row[csf('working_hour')]; 
				 $production_data_arr[$prod_date][$row[csf('floor_id')]][$row[csf('id')]]['smv_adjust']=$row[csf('smv_adjust')]; 
				 $production_data_arr[$prod_date][$row[csf('floor_id')]][$row[csf('id')]]['smv_adjust_type']=$row[csf('smv_adjust_type')]; 
				 $production_data_arr[$prod_date][$row[csf('floor_id')]][$row[csf('id')]]['prod_start_time']=$row[csf('prod_start_time')];
			 }
			 $dataArray_sql_cap=sql_select("SELECT  a.floor_id, a.line_number as line_no,b.pr_date,c.capacity from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and c.id=b.mast_dtl_id  and a.company_id=$comapny_id and b.pr_date=".$txt_date."  and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  group by a.id,a.line_number, a.floor_id, a.line_number, b.man_power,b.pr_date, b.operator,c.capacity, b.helper, b.working_hour,b.target_per_hour order by a.floor_id");
			 //$prod_resource_array_summary=array();
			 foreach( $dataArray_sql_cap as $row)
			 {
				$prod_date=$row[csf('pr_date')];
				 $production_data_arr[$prod_date][$row[csf('floor_id')]][$row[csf('line_no')]]['capacity']=$row[csf('capacity')]; 
			 }
	
	} //End
	
	//echo "<pre>";
	//print_r($production_serial_arr);//die;
    $avable_min=0;
	$today_product=0;
    $floor_name="";   
    $floor_man_power=0;
	$floor_operator=$floor_produc_min=0;
	$floor_smv=$floor_row=$floor_helper=$floor_tgt_h=$floor_days_run=$floor_working_hour=$line_floor_production=$floor_today_product=$floor_avale_minute=0;
	$total_operator=$total_helper=$gnd_hit_rate=0;   
    $total_smv=$total_terget=$grand_total_product=$gnd_line_effi=0;
    $total_man_power=$gnd_avable_min=$gnd_product_min=0;
	$item_smv=$item_smv_total=$line_efficiency=$days_run=$days_active=$total_working_hour=$gnd_total_tgt_h=$total_capacity=0;
	$j=1;
	ob_start();
	$line_number_check_arr=array();
	$smv_for_item="";
	$total_production=array();
	$floor_production=array();
    $line_floor_production=0;
    $line_total_production=0; $gnd_total_fob_val=0; $gnd_final_total_fob_val=0;

	foreach($production_serial_arr as $date_key=>$date_fname)
	{
		ksort($date_fname);
		foreach ($date_fname as $fsl => $fsl_data) 
		{
			foreach($fsl_data as $f_id=>$fname)
			{
				ksort($fname);
				//  echo "<pre>";
				//  print_r($fname);die; //$floor_sl_Arr
				
				foreach($fname as $sl=>$s_data)
				{
					foreach($s_data as $l_id=>$ldata)
					{
					$po_value=$production_data_arr[$date_key][$f_id][$ldata]['po_number'];
					 


					if($po_value)
					{

						//}
						
						if($i!=1)
						{
							if(!in_array($f_id, $check_arr))
							{
								if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$html.='<tr  bgcolor="#B6B6B6">
									<td class="break_all" width="40">&nbsp;</td>
									<td class="break_all" width="80">&nbsp;</td>
									<td class="break_all" width="80">&nbsp;</td>
									<td class="break_all" width="80">&nbsp;</td>
									<td class="break_all" width="80">&nbsp;</td>
									<td class="break_all" width="100">&nbsp;</td>
									<td class="break_all" width="140">&nbsp;</td>
									<td class="break_all" width="100">&nbsp;</td>
									<td class="break_all" width="80">&nbsp;</td>
									<td class="break_all" width="80">&nbsp;</td>
									<td class="break_all" width="80">&nbsp;</td>
									<td class="break_all" align="right" width="60">&nbsp;</td>
									<td class="break_all" align="right" width="70">'.$floor_operator.'</td>
									<td class="break_all" align="right" width="50">'. $floor_helper.'</td>
									<td class="break_all" align="right" width="60">'. $floor_man_power.'</td>
									<td class="break_all" align="right" width="70">'. $floor_tgt_h.'</td>
									<td class="break_all" align="right" width="60">'. $floor_days_run.'</td>
									<td class="break_all" align="right" width="60">'. $floor_days_active.'</td>
									<td class="break_all" align="right" width="70">'.$floor_capacity.'</td>
									<td class="break_all" align="right" width="60">'. $floor_working_hour.'</td>
									<td class="break_all" align="right" width="60">&nbsp;</td>
									<td class="break_all" align="right" width="60">&nbsp;</td>
									<td class="break_all" align="right" width="80">'.$eff_target_floor.'</td>
									<td class="break_all" align="right" width="80">'.$line_floor_production.'</td>
									<td class="break_all" align="right" width="80">&nbsp;</td>
									<td class="break_all" align="right" width="80">'. ($line_floor_production-$eff_target_floor).';</td>
									<td class="break_all" align="right" width="100">'. number_format($floor_avale_minute,0).'</td>
									<td class="break_all" align="right" width="100">'. number_format($floor_produc_min,0).'</td>
									<td class="break_all" align="right" width="60">'.number_format(($line_floor_production/$eff_target_floor)*100,2).'%</td>
									<td class="break_all" align="right" width="90">'.number_format($floor_efficency,2).' %</td>
									<td class="break_all" align="right" width="70">'.number_format($gnd_total_fob_val,2).'</td>';
									
									$gnd_total_fob_val=0;
									for($k=$hour; $k<=$last_hour; $k++)
									{
										$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";


										if($start_hour_arr[$k]==$global_start_lanch)
										{
											$bg_color='background:yellow';
										}
										if($floor_tgt_h>$floor_production[$prod_hour])
										{
											$bg_color='background:red';
											if($floor_production[$prod_hour]==0)
											{
												$bg_color='';
											}
										}
										else
										{
											$bg_color='';
										}


										$html.='<td class="break_all" align="right" width="50" style='.$bg_color.' >'. $floor_production[$prod_hour].'</td>';
									}
									$html.='</tr>';
									
								$floor_name="";
								$floor_smv=0;
								$floor_row=0;
								$floor_operator=0;
								$floor_helper=0;
								$floor_tgt_h=0;
								$floor_man_power=0;
								$floor_days_run=0;
								$eff_target_floor=0;
								unset($floor_production);
								$floor_working_hour=0;
								$line_floor_production=0;
								$floor_today_product=0;
								$floor_avale_minute=0;
								$floor_produc_min=0;
								$floor_efficency=0;
								$floor_man_power=0;
								$floor_capacity=0;
								$j++;
							}
						}
						$floor_row++;
						//$item_ids=$production_data_arr[$f_id][$ldata]['item_number_id'];
						$germents_item=array_unique(explode('****',$production_data_arr[$date_key][$f_id][$ldata]['item_number_id']));
					
						$buyer_neme_all=array_unique(explode(',',$production_data_arr[$date_key][$f_id][$ldata]['buyer_name']));
						$buyer_name="";
						foreach($buyer_neme_all as $buy)
						{
							if($buyer_name!='') $buyer_name.=',';
							$buyer_name.=$buyerArr[$buy];
						}
						$garment_itemname='';
						$active_days='';
						$item_smv="";$item_ids='';
						$smv_for_item="";
						$produce_minit="";
						$order_no_total="";
						$efficiency_min=0;
						$tot_po_qty=0;$fob_val=0;
						foreach($germents_item as $g_val)
						{
							
							$po_garment_item=explode('**',$g_val);
							if($garment_itemname!='') $garment_itemname.=',';
							$garment_itemname.=$garments_item[$po_garment_item[1]];
							if($item_ids=='') $item_ids=$po_garment_item[1];else $item_ids.=",".$po_garment_item[1];
							if($active_days=="")$active_days=$active_days_arr_powise[$po_garment_item[0]][$po_garment_item[1]];
							else $active_days.=','.$active_days_arr_powise[$po_garment_item[0]][$po_garment_item[1]];
							
							
							//echo $item_po_array[$po_garment_item[0]][$po_garment_item[1]]['amt'].'<br>';
							$tot_po_qty+=$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['qty'];
							$tot_po_amt+=$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['amt'];
							if($item_smv!='') $item_smv.='/';
							//echo $po_garment_item[0].'='.$po_garment_item[1];
							$item_smv.=$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
							if($order_no_total!="") $order_no_total.=",";
							$order_no_total.=$po_garment_item[0];
							if($smv_for_item!="") $smv_for_item.="****".$po_garment_item[0]."**".$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
							else
							$smv_for_item=$po_garment_item[0]."**".$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];	
							$produce_minit+=$production_po_data_arr[$date_key][$f_id][$l_id][$po_garment_item[0]]*$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
							$fob_rate=$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['amt']/$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['qty'];
							$prod_qty=$production_data_arr_qty[$date_key][$f_id][$l_id][$po_garment_item[0]][$po_garment_item[1]]['quantity'];
							//echo $prod_qty.'<br>';
							if(is_nan($fob_rate)){ $fob_rate=0; }
							$fob_val+=$prod_qty*$fob_rate;
						}
						//$fob_rate=$tot_po_amt/$tot_po_qty;
						
						$subcon_po_id=array_unique(explode(',',$production_data_arr[$date_key][$f_id][$ldata]['order_id']));
						$subcon_order_id="";
						foreach($subcon_po_id as $sub_val)
						{
							$subcon_po_smv=explode(',',$sub_val); 
							if($sub_val!=0)
							{
							if($item_smv!='') $item_smv.='/';
							if($item_smv!='') $item_smv.='/';
							$item_smv.=$subcon_order_smv[$sub_val];
							}
							$produce_minit+=$production_po_data_arr[$date_key][$f_id][$l_id][$sub_val]*$subcon_order_smv[$sub_val];
							if($subcon_order_id!="") $subcon_order_id.=",";
							$subcon_order_id.=$sub_val;
						}
						if($order_no_total!="")
						{
							$day_run_sql=sql_select("select min(production_date) as min_date from pro_garments_production_mst
							where po_break_down_id in(".$order_no_total.")  and production_type=4");
							foreach($day_run_sql as $row_run)
							{
							$sewing_day=$row_run[csf('min_date')];
							}
							if($sewing_day!="")
							{
							$days_run=datediff("d",$sewing_day,$pr_date);
							}
							else  $days_run=0;
						}
						$type_line=$production_data_arr[$date_key][$f_id][$ldata]['type_line'];
						$prod_reso_allo=$production_data_arr[$date_key][$f_id][$ldata]['prod_reso_allo'];
						/*if($type_line==2)
						{
							$sewing_line='';
							if($production_data_arr[$f_id][$ldata]['prod_reso_allo']==1)
							{
								$line_number='';
								$line_number=explode(",",$ldata);
								foreach($line_data as $lin_id)
								{
									//echo $lin_id.'dd';
									$line_number=explode(",",$prod_reso_arr[$lin_id]);
								}
								foreach($line_number as $val)
								{
									if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
								}
							}
							else $sewing_line=$lineArr[$lin_id];
						}
						else
						{*/
							$sewing_line='';
							if($production_data_arr[$date_key][$f_id][$ldata]['prod_reso_allo']==1)
							{
								$line_number=explode(",",$prod_reso_arr[$ldata]);
								foreach($line_number as $val)
								{
									if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
								}
							}
							else $sewing_line=$lineArr[$ldata];
						//}
							// echo $f_id."=".$ldata."=".$production_data_arr[$date_key][$f_id][$ldata]['prod_reso_allo']."==<br>";
						
						/*$sewing_line='';
						if($production_data_arr[$f_id][$ldata]['prod_reso_allo']==1)
						{
						$line_number=explode(",",$prod_reso_arr[$ldata]);
						foreach($line_number as $val)
						{
						if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
						}
						}
						else $sewing_line=$lineArr[$ldata];*/							
				
						$lunch_start="";
						$lunch_start=$line_number_arr[$ldata][$pr_date]['lunch_start_time'];  
						$lunch_hour=$start_time_arr[$row[1]]['lst']; 
						if($lunch_start!="") 
						{ 
						$lunch_start_hour=$lunch_start; 
						}
						else
						{
						$lunch_start_hour=$lunch_hour; 
						}
						
						$production_hour=array();
						for($h=$hour;$h<=$last_hour;$h++)
						{
							$prod_hour="prod_hour".substr($line_start_hour_arr[$h],0,2).""; 
							$production_hour[$prod_hour]=$production_data_arr[$date_key][$f_id][$ldata][$prod_hour];
							$floor_production[$prod_hour]+=$production_data_arr[$date_key][$f_id][$ldata][$prod_hour];
							$total_production[$prod_hour]+=$production_data_arr[$date_key][$f_id][$ldata][$prod_hour];
						}				
						
						$floor_production['prod_hour24']+=$production_data_arr[$date_key][$f_id][$ldata]['prod_hour23'];
						$total_production['prod_hour24']+=$production_data_arr[$date_key][$f_id][$ldata]['prod_hour23'];
						$production_hour['prod_hour24']=$production_data_arr[$date_key][$f_id][$ldata]['prod_hour23']; 
						$line_production_hour=0;
						if(str_replace("'","",$actual_production_date)>str_replace("'","",$actual_date)) 
						{
							if($type_line==2) //No Profuction Line
							{
								$line_start=$production_data_arr[$date_key][$f_id][$l_id]['prod_start_time'];
							}
							else
							{
								$line_start=$line_number_arr[$date_key][$ldata][$pr_date]['prod_start_time'];
							}
							if($line_start!="") 
							{ 
								$line_start_hour=substr($line_start,0,2); 
								if(substr($line_start_hour,0,1)==0)  $line_start_hour=substr($line_start_hour,1,1);	
							}
							else
							{
								$line_start_hour=$hour; 
							}
							$actual_time_hour=0;
							$total_eff_hour=0;
							for($lh=$line_start_hour;$lh<=$last_hour;$lh++)
							{
								$bg=$start_hour_arr[$lh];
								if($lh<$actual_time)
								{
								$total_eff_hour=$total_eff_hour+1;;	
								$line_hour="prod_hour".substr($bg,0,2)."";
								echo $line_production_hour+=$production_data_arr[$date_key][$f_id][$ldata][$line_hour];
								$line_floor_production+=$production_data_arr[$date_key][$f_id][$ldata][$line_hour];
								$line_total_production+=$production_data_arr[$date_key][$f_id][$ldata][$line_hour];
								$actual_time_hour=$start_hour_arr[$lh+1];
								}
							}
							if($start_hour_arr[$actual_time]>$lunch_start_hour) $total_eff_hour=$total_eff_hour-1;
							
							if($type_line==2)
							{
								if($total_eff_hour>$production_data_arr[$date_key][$f_id][$l_id]['working_hour'])
								{
									$total_eff_hour=$production_data_arr[$date_key][$f_id][$l_id]['working_hour'];
								}
							}
							else
							{
								if($total_eff_hour>$prod_resource_array[$ldata][$pr_date]['working_hour'])
								{
									$total_eff_hour=$prod_resource_array[$ldata][$pr_date]['working_hour'];
								}
							}
							
						}
						if(str_replace("'","",$actual_production_date)<=str_replace("'","",$actual_date)) 
						{
							for($ah=$hour;$ah<=$last_hour;$ah++)
							{
								$prod_hour="prod_hour".substr($start_hour_arr[$ah],0,2).""; 
								$line_production_hour+=$production_data_arr[$date_key][$f_id][$ldata][$prod_hour];
								$line_floor_production+=$production_data_arr[$date_key][$f_id][$ldata][$prod_hour];
								$line_total_production+=$production_data_arr[$date_key][$f_id][$ldata][$prod_hour];
							}
							if($type_line==2)
							{
								$total_eff_hour=$production_data_arr[$date_key][$f_id][$l_id]['working_hour'];
							}
							else
							{
								$total_eff_hour=$prod_resource_array[$ldata][$pr_date]['working_hour'];	
							}
						}
						
						if($sewing_day!="")
						{
							$days_run= $diff=datediff("d",$sewing_day,$pr_date);
							$days_active= $active_days_arr[$f_id][$l_id];
						}
						else 
						{
							$days_run=0;
							$days_active=0;
						} 
						
						$current_wo_time=0;
						if($current_date==$search_prod_date)
						{
							$prod_wo_hour=$total_eff_hour;
							
							if ($dif_time<$prod_wo_hour)//
							{
								$current_wo_time=$dif_hour_min;
								$cla_cur_time=$dif_time;
							}
							else
							{
								$current_wo_time=$prod_wo_hour;
								$cla_cur_time=$prod_wo_hour;
							}
						}
						else
						{
							$current_wo_time=$total_eff_hour;
							$cla_cur_time=$total_eff_hour;
						}
							$total_adjustment=0;
							if($type_line==2) //No Production Line
							{
								$smv_adjustmet_type=$production_data_arr[$date_key][$f_id][$l_id]['smv_adjust_type'];
								$eff_target=($production_data_arr[$date_key][$f_id][$l_id]['terget_hour']*$total_eff_hour);

								if($total_eff_hour>=$production_data_arr[$date_key][$f_id][$l_id]['working_hour'])
								{
									if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$production_data_arr[$date_key][$f_id][$l_id]['smv_adjust'];
									if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($production_data_arr[$date_key][$f_id][$l_id]['smv_adjust'])*(-1);
								}
								$efficiency_min+=$total_adjustment+($production_data_arr[$date_key][$f_id][$l_id]['man_power'])*$cla_cur_time*60;
								$line_efficiency=(($produce_minit)*100)/$efficiency_min;
							}
							else
							{
								$smv_adjustmet_type=$prod_resource_array[$ldata][$pr_date]['smv_adjust_type'];
								$eff_target=($prod_resource_array[$ldata][$pr_date]['terget_hour']*$total_eff_hour);
								
								if($total_eff_hour>=$prod_resource_array[$ldata][$pr_date]['working_hour'])
								{
								if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$prod_resource_array[$ldata][$pr_date]['smv_adjust'];
								if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($prod_resource_array[$ldata][$pr_date]['smv_adjust'])*(-1);
								}
								
								/*$actual_hours=date("H",time())-$line_start_hour; for metro
								$cur_time=date("H",time());
								if($cur_time>$line_start_hour){
									$actual_hours=$actual_hours-1;
								}
								else
								{
									$actual_hours=$actual_hours	;
								}
								$total_eff_hour_custom=($actual_hours>$total_eff_hour)?$total_eff_hour:$actual_hours;

								$producting_day=strtotime("Y-m-d",$txt_producting_day);
								
								if($producting_day>$today_date)
								{
									
									$efficiency_min+=$total_adjustment+($prod_resource_array[$ldata][$pr_date]['man_power'])*$total_eff_hour*60;
									//echo $total_eff_hour."_";
								}
								else
								{
									$efficiency_min+=$total_adjustment+($prod_resource_array[$ldata][$pr_date]['man_power'])*$total_eff_hour_custom*60;
									//echo $total_eff_hour_custom."_";
								}*/
								
								
								
								
								$efficiency_min+=$total_adjustment+($prod_resource_array[$ldata][$pr_date]['man_power'])*$cla_cur_time*60;
								$line_efficiency=(($produce_minit)*100)/$efficiency_min;
							}
						
						
						
						
						if($type_line==2) //No Production Line
						{
							$man_power=$production_data_arr[$date_key][$f_id][$l_id]['man_power'];
							$operator=$production_data_arr[$date_key][$f_id][$l_id]['operator'];
							$helper=$production_data_arr[$date_key][$f_id][$l_id]['helper'];
							$terget_hour=$production_data_arr[$date_key][$f_id][$l_id]['target_hour'];	
							$capacity=$production_data_arr[$date_key][$f_id][$l_id]['capacity'];
							$working_hour=$production_data_arr[$date_key][$f_id][$l_id]['working_hour']; 
							
							$floor_working_hour+=$production_data_arr[$date_key][$f_id][$l_id]['working_hour']; 
							$eff_target_floor+=$eff_target;
							$floor_today_product+=$today_product;
							$floor_avale_minute+=$efficiency_min;
							$floor_produc_min+=$produce_minit; 
							$floor_efficency=($floor_produc_min/$floor_avale_minute)*100;
							$floor_capacity+=$production_data_arr[$date_key][$f_id][$l_id]['capacity'];
							$floor_helper+=$production_data_arr[$date_key][$ldata][$l_id]['helper'];
							$floor_man_power+=$production_data_arr[$date_key][$f_id][$l_id]['man_power'];
							$floor_operator+=$production_data_arr[$date_key][$f_id][$l_id]['operator'];
							$total_operator+=$production_data_arr[$date_key][$f_id][$l_id]['operator'];
							$total_man_power+=$production_data_arr[$date_key][$f_id][$l_id]['man_power'];	
							$total_helper+=$production_data_arr[$date_key][$f_id][$l_id]['helper'];
							$total_capacity+=$production_data_arr[$date_key][$f_id][$l_id]['capacity'];
							$floor_tgt_h+=$production_data_arr[$date_key][$f_id][$l_id]['target_hour'];
							$total_working_hour+=$production_data_arr[$date_key][$f_id][$l_id]['working_hour']; 
							$gnd_total_tgt_h+=$production_data_arr[$date_key][$f_id][$l_id]['target_hour'];
							$total_terget+=$eff_target;	
							$grand_total_product+=$today_product;
							$gnd_avable_min+=$efficiency_min;
							$gnd_product_min+=$produce_minit;
							
							$gnd_total_fob_val+=$fob_val; 
							$gnd_final_total_fob_val+=$fob_val;
						}
						else
						{
							$man_power=$prod_resource_array[$ldata][$pr_date]['man_power'];	
							$operator=$prod_resource_array[$ldata][$pr_date]['operator'];
							$helper=$prod_resource_array[$ldata][$pr_date]['helper'];
							$terget_hour=$prod_resource_array[$ldata][$pr_date]['terget_hour'];	
							$capacity=$prod_resource_array[$ldata][$pr_date]['capacity'];
							$working_hour=$prod_resource_array[$ldata][$pr_date]['working_hour'];
							
							$floor_capacity+=$prod_resource_array[$ldata][$pr_date]['capacity'];
							$floor_man_power+=$prod_resource_array[$ldata][$pr_date]['man_power'];
							$floor_operator+=$prod_resource_array[$ldata][$pr_date]['operator'];
							$floor_helper+=$prod_resource_array[$ldata][$pr_date]['helper'];
							$floor_tgt_h+=$prod_resource_array[$ldata][$pr_date]['terget_hour'];	
							$floor_working_hour+=$prod_resource_array[$ldata][$pr_date]['working_hour']; 
							$eff_target_floor+=$eff_target;
							$floor_today_product+=$today_product;
							$floor_avale_minute+=$efficiency_min;
							$floor_produc_min+=$produce_minit; 
							$floor_efficency=($floor_produc_min/$floor_avale_minute)*100;
							
							$total_operator+=$prod_resource_array[$ldata][$pr_date]['operator'];
							$total_man_power+=$prod_resource_array[$ldata][$pr_date]['man_power'];
							$total_helper+=$prod_resource_array[$ldata][$pr_date]['helper'];
							$total_capacity+=$prod_resource_array[$ldata][$pr_date]['capacity'];
							$total_working_hour+=$prod_resource_array[$ldata][$pr_date]['working_hour']; 
							$gnd_total_tgt_h+=$prod_resource_array[$ldata][$pr_date]['terget_hour'];
							$total_terget+=$eff_target;
							$grand_total_product+=$today_product;
							$gnd_avable_min+=$efficiency_min;
							$gnd_product_min+=$produce_minit;
							$gnd_total_fob_val+=$fob_val;
							$gnd_final_total_fob_val+=$fob_val; 
							
						} 
						$po_id=rtrim($production_data_arr[$date_key][$f_id][$ldata]['po_id'],',');
						$po_id=array_unique(explode(",",$po_id));
						$style=rtrim($production_data_arr[$date_key][$f_id][$ldata]['style']);
						$style=implode(",",array_unique(explode(",",$style)));
						
						$cbo_get_upto=str_replace("'","",$cbo_get_upto);
						$txt_parcentage=str_replace("'","",$txt_parcentage);
					
						$floor_name=$floorArr[$f_id];
						
						$floor_smv+=$item_smv;
						
						
						$floor_days_run+=$days_run; 
						$floor_days_active+=$days_active;	
						
						$po_id=$production_data_arr[$date_key][$f_id][$ldata]['po_id'];//$item_ids//$subcon_order_id
						$styles=explode(",",$style);
						$style_button='';//
						foreach($styles as $sid)
						{
							if( $style_button=='') 
							{ 
								$style_button="<a href='#' onClick=\"generate_style_popup('".$sid."','".$po_id."','".$subcon_order_id."','".$ldata."','".$f_id."','".$item_ids."','".$prod_reso_allo."',".$txt_date.",'show_style_line_generate_report','".$i."')\" '> ".$sid."<a/>";
							}
							else
							{
								$style_button.=", "."<a href='#' onClick=\"generate_style_popup('".$sid."','".$po_id."','".$subcon_order_id."','".$ldata."','".$f_id."','".$item_ids."','".$prod_reso_allo."',".$txt_date.",'show_style_line_generate_report','".$i."')\" '> ".$sid."<a/>";
							}
						}
						$as_on_current_hour_target=0; $as_on_current_hour_variance=0;
						$as_on_current_hour_target=$terget_hour*$cla_cur_time;
						$as_on_current_hour_variance=$line_production_hour-$as_on_current_hour_target;
						
						if($line_efficiency<=$txt_parcentage) $efficiency_color="#FF0000"; else $efficiency_color="#FFFFFF";
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$html.='<tbody>';
						$html.="<tr bgcolor='$bgcolor' onclick=change_color('tr_$i','$bgcolor') id=tr_$i>";
						$html.='<td class="break_all" width="40">'.$i.'&nbsp;</td>
								<td class="break_all" width="80" >'.$date_key.'&nbsp; </td>
								<td class="break_all" width="80" title='.$fsl.'>'.$floor_name.'&nbsp; </td>
								<td class="break_all" align="center" width="80" title="'.$l_id.'" >'. $sewing_line.'&nbsp; </td>
								<td class="break_all" width="80"><p>'.$buyer_name.'&nbsp;</p></td>
								<td class="break_all" width="100"><p>'.$production_data_arr[$date_key][$f_id][$ldata]['job_no'].'&nbsp;</p></td>
								<td class="break_all" width="140"><p>'.$production_data_arr[$date_key][$f_id][$ldata]['po_number'].'&nbsp;</p></td>
								<td class="break_all" width="100"><p>'.$style_button.'&nbsp;</p></td>
								<td class="break_all" width="80"><p>'.$production_data_arr[$date_key][$f_id][$ldata]['file'].'&nbsp;</p></td>
								<td class="break_all" width="80"><p>'.$production_data_arr[$date_key][$f_id][$ldata]['ref'].'&nbsp;</p></td>
								<td class="break_all" width="120" style="word-wrap:break-word; word-break: break-all;">'.$garment_itemname.'</td>
								<td class="break_all" align="right" width="60"><p>'.$item_smv.'</p></td>
								<td class="break_all" align="right" width="70">'.$operator.'</td>
								<td class="break_all" align="right" width="50">'.$helper.'</td>
								<td class="break_all" align="right" width="60">'.$man_power.'</td>
								<td class="break_all" align="right" width="70">'.$terget_hour.'</td>
								<td class="break_all" align="right" width="60">'.$days_run.'</td> 
								<td class="break_all" align="right" width="60">'.$active_days.'</td>
								<td class="break_all" align="right" width="70">'.$capacity.'</td>
								<td class="break_all" align="right" width="60">'.$working_hour.'</td>
								<td class="break_all" align="right" width="60">'.$cla_cur_time.'</td>
								<td class="break_all" align="right" width="60">'.number_format($as_on_current_hour_target,0).'</td>
								<td class="break_all" align="right" width="80">'.number_format($eff_target,0).'</td>
								<td class="break_all" width="75" align="right"><a href="##" onclick="openmypage('.$cbo_company_id.",'".$order_no_total."','".$subcon_order_id."',".$f_id.",".$ldata.",'tot_prod','".$smv_for_item."','".$acturl_hour_minute."','".$line_start."',".$txt_date.')">'.$line_production_hour.'</a></td>
								<td class="break_all" align="right" width="80">'.number_format($as_on_current_hour_variance,0).'</td>
								<td class="break_all" align="right" width="80">'.($line_production_hour-$eff_target).'</td>
								<td class="break_all" align="right" width="100">'.number_format($efficiency_min,0).'</td>
								<td class="break_all" width="100" align="right"><a href="##" onclick="openmypage('.$cbo_company_id.",'".$order_no_total."','".$subcon_order_id."',".$f_id.",".$ldata.",'tot_prod','".$smv_for_item."','".$acturl_hour_minute."','".$line_start."',".$txt_date.')">'.number_format($produce_minit,0).'</a></td>
								<td class="break_all" align="right" width="60" >'. number_format(($line_production_hour/$eff_target)*100,2).'%</td>';
								
								if($line_efficiency<=$txt_parcentage)
								{
									$html.='<td class="break_all" align="right" width="60" bgcolor="red">'.number_format($line_efficiency,2).'%</td>';
								}
								else
								{
									$html.='<td class="break_all" align="right" width="60">'.number_format($line_efficiency,2).'%</td>'; 
								}
								$html.='<td class="break_all" width="70" title='.$fob_rate.' align="right"><a href="##" onclick="openmypage('.$cbo_company_id.",'".$order_no_total."','".$subcon_order_id."',".$f_id.",".$ldata.",'tot_fob_value_popup','".$smv_for_item."','".$actual_time_hour."','".$line_start."',".$txt_date.')">'.number_format($fob_val,2).'</a></td>'; 
								for($k=$hour; $k<=$last_hour; $k++)
								{
									$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
									
									
									if($start_hour_arr[$k]==$lunch_start_hour)
									{
										$bg_color='background:yellow';
									}
									else if($terget_hour>$production_hour[$prod_hour])
									{
										$bg_color='background:red';
										if($production_hour[$prod_hour]==0)
										{
											$bg_color='';
										}
									}
									else if($terget_hour<$production_hour[$prod_hour])
									{
										$bg_color='background:green';
										if($production_hour[$prod_hour]==0)
										{
											$bg_color='';
										}
									}
									else
									{
										$bg_color="";
									}
									
									$html.='<td class="break_all" align="right" width="50"  style='.$bg_color.'>'.$production_hour[$prod_hour].'</td>';
									//$html.='<td class="break_all" align="right" width="50"  style=" background-color:#FFFF66" >'.$production_hour[$prod_hour].'&nbsp;kk</td>';
								}
						$html.='</tr>';
						$i++;
						$check_arr[]=$f_id;
					}
					}
				
				}
			
			}
		}//floor_sl_arr data end

	}//end
			$html.='<tr  bgcolor="#B6B6B6">
					<td class="break_all" width="40">&nbsp;</td>
					<td class="break_all" width="80">&nbsp;</td>
					<td class="break_all" width="80">&nbsp;</td>
					<td class="break_all" width="80">&nbsp;</td>
					<td class="break_all" width="80">&nbsp;</td>
					<td class="break_all" width="100">&nbsp;</td>
					<td class="break_all" width="140">&nbsp;</td>
					<td class="break_all" width="100">&nbsp;</td>
					<td class="break_all" width="80">&nbsp;</td>
					<td class="break_all" width="80">&nbsp;</td>
					<td class="break_all" width="120">&nbsp;</td>
					<td class="break_all" align="right" width="60">&nbsp;</td>
					<td class="break_all" align="right" width="70">'.$floor_operator.'</td>
					<td class="break_all" align="right" width="50">'. $floor_helper.'</td>
					<td class="break_all" align="right" width="60">'. $floor_man_power.'</td>
					<td class="break_all" align="right" width="70">'. $floor_tgt_h.'</td>
					<td class="break_all" align="right" width="60">'. $floor_days_run.'</td>
					<td class="break_all" align="right" width="60">'. $floor_days_active.'</td>
					<td class="break_all" align="right" width="70">&nbsp;</td>
					<td class="break_all" align="right" width="60">'. $floor_working_hour.'</td>
					<td class="break_all" align="right" width="60">&nbsp;</td>
					<td class="break_all" align="right" width="60">&nbsp;</td>
					<td class="break_all" align="right" width="80">'. $eff_target_floor.'</td>
					<td class="break_all" align="right" width="80">'.$line_floor_production.'</td>
					<td class="break_all" align="right" width="80">'.$line_floor_production.'</td>
					<td class="break_all" align="right" width="80">'. ($line_floor_production-$eff_target_floor).'</td>
					<td class="break_all" align="right" width="100">'. number_format($floor_avale_minute,0).'</td>
					<td class="break_all" align="right" width="100">'. number_format($floor_produc_min,0).'</td>
					<td class="break_all" align="right" width="60">'. number_format(($line_floor_production/$eff_target_floor)*100,2).'%</td>
					<td class="break_all" align="right" width="90">'.number_format($floor_efficency,2).'%</td>
					<td class="break_all" width="70" align="right">'.number_format($gnd_total_fob_val,2).'</td>';
					
					for($k=$hour; $k<=$last_hour; $k++)
					{
						$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
						
						if($floor_tgt_h>$floor_production[$prod_hour])
						{
							$bg_color='background:red';
							if($floor_production[$prod_hour]==0)
							{
								$bg_color='';
							}
						}
						else if($floor_tgt_h<$floor_production[$prod_hour])
						{
							$bg_color='background:green';
							if($floor_production[$prod_hour]==0)
							{
								$bg_color='';
							}
						}
						else if($start_hour_arr[$k]==$global_start_lanch)
						{
							 $bg_color='background:yellow';
						//$html.='<td class="break_all" align="right" width="50" style=" background-color:#FFFF66" >'. $floor_production[$prod_hour].'&nbsp;</td>';
						}
						else
						{
							 $bg_color='';
						//$html.='<td class="break_all" align="right" width="50">'. $floor_production[$prod_hour].'&nbsp;</td>';
						}
						$html.='<td class="break_all" align="right" width="50" style='.$bg_color.' >'. $floor_production[$prod_hour].'</td>';
					}
									
				   $html.='</tr> </tbody>';
				  
				?>
               
	<fieldset style="width:2530px">
       <table width="2200" cellpadding="0" cellspacing="0"> 
            <tr class="form_caption">
                <td colspan="26" align="center"><strong><? echo $report_title; ?> &nbsp;V2</strong></td> 
            </tr>
            <tr class="form_caption">
                <td colspan="26" align="center"><strong><? echo $companyArr[$comapny_id]; ?></strong></td> 
            </tr>
            <tr class="form_caption">
                <td colspan="26" align="center"><strong><? echo "Date:  ".change_date_format( str_replace("'","",trim($txt_date)) ); ?></strong></td> 
            </tr>
        </table>
        <br />
        <table  width="600" cellpadding="0"  cellspacing="0" align="center" style="padding-left:200px">
            <tr>
                
               
                <td bgcolor="#FFFF66" height="18" width="30" ></td>
                <td> &nbsp;Lunch Hour</td>
                <td bgcolor="red" height="18" width="30"></td>
                <td> &nbsp;Efficiency % less than Standard And Production less than Target</td>
                
            
            </tr>
        </table>
       <br/>
        <table id="table_header_1" class="rpt_table" width="3240" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr height="50">
                    <th class="break_all" width="40">SL</th>
                    <th class="break_all" width="80">Prod Date</th>
                    <th class="break_all" width="80">Floor Name</th>
                    <th class="break_all" width="80">Line No</th>
                    <th class="break_all" width="80">Buyer</th>
                    <th class="break_all" width="100">Job</th>
                    <th class="break_all" width="140">Order No</th>
                    <th class="break_all" width="100">Style Ref.</th>
                    <th class="break_all" width="80">File No</th>
                    <th class="break_all" width="80">Ref. No</th>
                    <th class="break_all" width="120">Garments Item</th>
                    <th class="break_all" width="60">SMV</th>
                    <th class="break_all" width="70">Operator</th>
                    <th class="break_all" width="50">Helper</th>
                    <th class="break_all" width="60">ManPower</th>
                    <th class="break_all" width="70">Hourly <br>Target (Pcs)</th>
                    <th class="break_all" width="60">Days Run</th> 
                    <th class="break_all" width="60">Active <br>Prod.Days</th>
                    <th class="break_all" width="70">Capacity</th>
                    <th class="break_all" width="60">Working Hour</th>
                    <th class="break_all" width="60">Current Hour</th>
                    <th class="break_all" width="60">As On Current <br>Hour Target (Pcs)</th>
                    <th class="break_all" width="80">Total Target</th>
                    <th class="break_all" width="80">Total Prod.</th>
                    <th class="break_all" width="80">As On Current <br>Hour Prod.Variance</th>
                    <th class="break_all" width="80">Total <br>Variance(Pcs)</th>
                    <th class="break_all" width="100">Available<br> Minutes</th>
                    <th class="break_all" width="100">Produce <br>Minutes</th>
                    <th class="break_all" width="60">Target <br>Hit rate</th>
                    <th class="break_all" width="90">Line Effi %</th>
                    <th class="break_all" width="70">FOB Val.</th>
                   <?
				
                	for($k=$hour+1; $k<=$last_hour+1; $k++)
					{
					?>
                      <th class="break_all" width="50" style="vertical-align:middle"><div class="block_div"><?  echo substr($start_hour_arr[$k],0,5);   ?></div></th>
					<?	
					}
                ?>
                </tr>
            </thead>
        </table>
        <div style="width:3260px; max-height:400px; overflow-y:scroll" id="scroll_body">
            <table class="rpt_table"    width="3240" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
              <? echo $html;  ?>
                <tfoot>
                   <tr>
                        <th class="break_all" width="40">&nbsp;</th>
                        <th class="break_all" width="80">&nbsp;</th>
                         <th class="break_all" width="80">&nbsp;</th>
                        <th class="break_all" width="80">&nbsp;</th>
                        <th class="break_all" width="80">&nbsp;</th>
                        <th class="break_all" width="100">&nbsp;</th>
                        <th class="break_all" width="140">&nbsp;</th>
                        <th class="break_all" width="100">&nbsp;</th>
                        <th class="break_all" width="80">&nbsp;</th>
                        <th class="break_all" width="80">&nbsp;</th>
                        <th class="break_all" width="120">Total</th>
                        <th class="break_all" align="right" width="60"><? // echo number_format($total_smv/($i-1),2); ?>&nbsp;</th>
                        <th class="break_all" align="right" width="70"><? echo $total_operator; ?>&nbsp;</th>
                        <th class="break_all" align="right" width="50"><? echo $total_helper; ?>&nbsp;</th>
                        <th class="break_all" align="right" width="60"><? echo $total_man_power; ?>&nbsp;</th>
                        <th class="break_all" align="right" width="70"><?  echo $gnd_total_tgt_h; ?>&nbsp;</th>
                        <th class="break_all" align="right" width="60"><? //echo $prod_resource_array[$row[csf('sewing_line')]][$pr_date]['man_power']; ?>&nbsp;</th>
                        <th class="break_all" align="right" width="60">&nbsp;</th>
                        <th class="break_all" align="right" width="70"><? echo $total_capacity; ?></th>
                        <th class="break_all" align="right" width="60"><? echo $total_working_hour; ?></th>
                        <th class="break_all" align="right" width="60">&nbsp;</th>
                        <th class="break_all" align="right" width="60">&nbsp;</th>
                        <th class="break_all" align="right" width="80"><? echo $total_terget; ?>&nbsp;</th>
                        <th class="break_all" align="right" width="80"><? echo $line_total_production; ?>&nbsp;</th>
                        <th class="break_all" align="right" width="80">&nbsp;</th>
                        <th class="break_all" align="right" width="80"><? echo $line_total_production-$total_terget; ?>&nbsp;</th>
                        <th class="break_all" align="right" width="100"><? echo number_format($gnd_avable_min,0); ?>&nbsp;</th>
                        <th class="break_all" align="right" width="100"><? echo number_format($gnd_product_min,0); ?>&nbsp;</th>
                        <th class="break_all" align="right" width="60"><? echo number_format(($line_total_production/$total_terget)*100,2)."%"; ?>&nbsp;</th>
                        <th class="break_all" align="right" width="90" ><? echo number_format(($gnd_product_min/$gnd_avable_min)*100,2)."%";?>&nbsp;</th>
                        <th class="break_all" align="right" width="70"><? echo number_format($gnd_final_total_fob_val,2);?>&nbsp;</th>
					    <?
						for($k=$hour; $k<=$last_hour; $k++)
						{
						$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
						?>
						<th class="break_all" align="right" width="50"><? echo $total_production[$prod_hour]; ?></th>
						<?	
						}
                        ?>
                    </tr>
                </tfoot>
            </table>
		</div>
	</fieldset>
         
	<?    
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename,'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data####$filename";
	exit();      

} // 2nd Button End

if($action=="report_generate3") // show3 button
{ 
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
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$companyArr = return_library_array("select id,company_name from lib_company","id","company_name"); 
	$buyerArr = return_library_array("select id,short_name from lib_buyer","id","short_name"); 
	$locationArr = return_library_array("select id,location_name from lib_location","id","location_name"); 
	$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name"); 
	$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name");  
	$prod_reso_arr=return_library_array( "SELECT id, line_number from prod_resource_mst",'id','line_number');

	$comapny_id=str_replace("'","",$cbo_company_id);
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$cbo_company_id and variable_list=23 and is_deleted=0 and status_active=1");
	//echo $prod_reso_allo."eee";die;
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
	}
	if(str_replace("'","",$cbo_company_id)==0) $company_name=""; else $company_name="and a.company_id=".str_replace("'","",$cbo_company_id)."";
	if(str_replace("'","",$cbo_location_id)==0) $location=""; else $location="and a.location=".str_replace("'","",$cbo_location_id)."";
	if(str_replace("'","",$cbo_location_id)==0) $location2=""; else $location2="and a.location_id=".str_replace("'","",$cbo_location_id)."";
	if(str_replace("'","",$cbo_floor_id)==0) $floor=""; else $floor="and a.floor_id=".str_replace("'","",$cbo_floor_id)."";
    if(str_replace("'","",$hidden_line_id)==0) $line=""; else $line="and a.sewing_line in(".str_replace("'","",$hidden_line_id).")";

	$min_shif_start=return_field_value("min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time","prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and   a.company_id in($comapny_id) and shift_id=1 and pr_date=$txt_date and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");
	if($min_shif_start=="")
	{
		echo "<div style='width:80%;margin:5px auto;' class='alert alert-danger'>No Line Engage for the selected Date.Please Check Actual Production Resource Entry.<div/>";die;
		
	}

	// ======================== shift wise line =========================
	
	$sql = "SELECT a.id,min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time,min(TO_CHAR(d.lunch_start_time,'HH24:MI')) as LUNCH_START_TIME from prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d where a.id=d.mst_id and a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and   a.company_id in($comapny_id) and shift_id=1 and pr_date=$txt_date and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 group by a.id";
	// echo $sql;
	$res = sql_select($sql);
	$line_wise_shift_arr = array();
	$line_wise_shift_lunch_arr = array();

	foreach ($res as $val) 
	{
		$line_wise_shift_arr[$val['ID']] = $val['LINE_START_TIME'];
		$line_wise_shift_lunch_arr[$val['ID']] = $val['LUNCH_START_TIME'];
	}
	unset($res);
	//==============================shift time===============================
	$start_time_data_arr=sql_select("SELECT company_name, shift_id, TO_CHAR(prod_start_time,'HH24:MI') as prod_start_time,TO_CHAR(lunch_start_time,'HH24:MI') as lunch_start_time from variable_settings_production where  company_name in($comapny_id) and  shift_id=1 and variable_list=26 and status_active=1 and is_deleted=0");
		
		
	$group_prod_start_time=sql_select("SELECT min(TO_CHAR(prod_start_time,'HH24:MI')) as prod_start_time  from variable_settings_production where company_name in($comapny_id) and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1");
		
	$lunch_start_time = "";	
	foreach($start_time_data_arr as $row)
	{
		$start_time_arr[$row[csf('company_name')]][$row[csf('shift_id')]]['pst']=$row[csf('prod_start_time')];
		$start_time_arr[$row[csf('company_name')]][$row[csf('shift_id')]]['lst']=$row[csf('lunch_start_time')];
		$exp = explode(":",$row[csf('lunch_start_time')]);
		$lunch_start_time = $exp[0]*1;
	}
	// echo $lunch_start_time."ddddddd";
	unset($start_time_data_arr);

	$prod_start_hour=$group_prod_start_time[0][csf('prod_start_time')];
	if($prod_start_hour=="") $prod_start_hour="08:00";
	$start_time=explode(":",$prod_start_hour);
	$hour=$start_time[0]*1; 
	$minutes=$start_time[1]; 
	$last_hour=23;
	$lineWiseProd_arr=array(); $prod_arr=array(); $start_hour_arr=array();
	$start_hour=$prod_start_hour;
	$start_hour_arr[$hour]=$start_hour;
	for($j=$hour;$j<$last_hour;$j++)
	{
		$start_hour=add_time($start_hour,60);
		$start_hour_arr[$j+1]=substr($start_hour,0,5);
		// echo $j."<br>";
	}
	//echo $pc_date_time;die;
	$start_hour_arr[$j+1]='23:59';

	if($prod_start_hour>$min_shif_start)  $prod_start_hour=$min_shif_start;
	$actual_date=date("Y-m-d");
	$actual_production_date=date("Y-m-d",strtotime(str_replace("'","",$txt_date)));
	$actual_time=substr(date("Y-m-d H:i:s",strtotime($pc_date_time)),11,2);	
	$generated_hourarr=array();
	$first_hour_time=explode(":",$min_shif_start);
	$hour_line=$first_hour_time[0]*1; $minutes_one=$start_time[1];
	$line_start_hour_arr[$hour_line]=$min_shif_start;
	
	for($l=$hour_line;$l<$last_hour;$l++)
	{
		$min_shif_start=add_time($min_shif_start,60);
		$line_start_hour_arr[$l+1]=substr($min_shif_start,0,5);
	}
	
	$line_start_hour_arr[$j+1]='23:59';

	/* =====================================================================================================/
	/												Prod Resource data										/
	/===================================================================================================== */
 	if($prod_reso_allo==1)
 	{	
		$prod_resource_array=array();
		$dataArray=sql_select("SELECT a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper, b.line_chief, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and a.id=b.mst_id and c.id=b.mast_dtl_id and a.company_id=$comapny_id $location2 $floor and b.pr_date=$txt_date");
		foreach($dataArray as $val)
		{
			$prod_resource_array[$val[csf('id')]]['man_power']=$val[csf('man_power')];
			$prod_resource_array[$val[csf('id')]]['operator']=$val[csf('operator')];
			$prod_resource_array[$val[csf('id')]]['helper']=$val[csf('helper')];
			$prod_resource_array[$val[csf('id')]]['terget_hour']=$val[csf('target_per_hour')];
			$prod_resource_array[$val[csf('id')]]['working_hour']=$val[csf('working_hour')];
			$prod_resource_array[$val[csf('id')]]['tpd']=$val[csf('target_per_hour')]*$val[csf('working_hour')];
			$prod_resource_array[$val[csf('id')]]['day_start']=$val[csf('from_date')];
			$prod_resource_array[$val[csf('id')]]['day_end']=$val[csf('to_date')];
			$prod_resource_array[$val[csf('id')]]['capacity']=$val[csf('capacity')];
		}

		

		/*===============================================================================/
		/							Actual resource SMV data							 /
		/============================================================================== */
		$prod_resource_smv_adj_array = array();
		$sql_query="SELECT b.mst_id, b.pr_date,b.number_of_emp ,b.adjust_hour,b.total_smv,b.adjustment_source from prod_resource_mst a,prod_resource_smv_adj b  where a.id=b.mst_id  and a.company_id=$comapny_id and b.pr_date=$txt_date and a.is_deleted=0 and b.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and b.adjustment_source in(3,4,6) $location2";
		// echo $sql_query;
		$sql_query_res=sql_select($sql_query);
		foreach($sql_query_res as $val)
		{
			$val[csf('pr_date')]=date("d-M-Y",strtotime($val[csf('pr_date')]));
			
			$prod_resource_smv_adj_array[$val[csf('mst_id')]]['number_of_emp']+=$val[csf('number_of_emp')];
			$prod_resource_smv_adj_array[$val[csf('mst_id')]]['adjust_hour']+=$val[csf('adjust_hour')];
			$prod_resource_smv_adj_array[$val[csf('mst_id')]]['total_smv']+=$val[csf('total_smv')];
			
		}
 	}
	// echo "<pre>";print_r($prod_resource_smv_adj_array);die;
	/* =====================================================================================================/
	/												Gmts Prod data											/
	/===================================================================================================== */
	
	$sql="SELECT  a.company_id, a.location, a.floor_id,a.shift_name, a.production_date, a.sewing_line,b.id as job_id,b.job_no,b.buyer_name  as buyer_name,b.style_ref_no, b.total_set_qnty as ratio, a.po_break_down_id, a.item_number_id,c.job_no_mst as job_no , c.po_number as po_number,c.unit_price,c.grouping,a.prod_reso_allo,c.id as po_id,
	sum(CASE WHEN a.production_type=5 THEN production_quantity else 0 END) as good_qnty,
	sum(CASE WHEN a.production_type=4 THEN production_quantity else 0 END) as input_qnty,"; 
		 
	$first=1;
	for($h=$hour;$h<$last_hour;$h++)
	{
			$bg=$start_hour_arr[$h];
			$end=substr(add_time($start_hour_arr[$h],60),0,5);
			$prod_hour="prod_hour".substr($bg,0,2);
			if($first==1)
			{
				$sql.="sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,";
			}
			else
			{
				$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,";
			}
		$first++;
	}
	$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN production_quantity else 0 END) AS prod_hour23 
		from wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a 
		where a.production_type in(4,5) and a.po_break_down_id=c.id and c.job_id=b.id and  a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0  and a.prod_reso_allo=1 and a.production_date=$txt_date $company_name $location $floor $line $buyer_id_cond 
		group by a.company_id, a.location, a.floor_id,a.shift_name,a.po_break_down_id, a.production_date, a.prod_reso_allo,c.id, a.sewing_line,b.id, b.job_no,b.total_set_qnty, b.buyer_name, b.style_ref_no, a.item_number_id, c.po_number,c.grouping,c.job_no_mst,  c.unit_price";
	// echo $sql;
	
	$res = sql_select($sql);
	$data_array = array();
	$lc_com_array = array();
	$poIdArr=array();
	$jobIdArr=array();
	$all_style_arr=array();
	$po_unit_price_array = array();
	foreach ($res as $v)
	{
		$lc_com_array[$v[csf('company_id')]] = $v[csf('company_id')];
		$poIdArr[$v[csf('po_break_down_id')]] = $v[csf('po_break_down_id')];	
		$jobIdArr[$v[csf('job_id')]] = $v[csf('job_id')];	
		$all_style_arr[$v[csf('style_ref_no')]] = $v[csf('style_ref_no')];
		$style_wise_po_arr[$v[csf('style_ref_no')]][$v[csf('po_break_down_id')]] = $v[csf('po_break_down_id')];

		$sewing_line='';
		if($v['PROD_RESO_ALLO']==1)
		{
			$sewing_line_ids=$prod_reso_arr[$v['SEWING_LINE']];
			$sl_ids_arr = explode(",", $sewing_line_ids);
			$sewing_line_id = $sl_ids_arr[0]; // always 1st line id will take
			foreach($sl_ids_arr as $val)
			{
				if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
			}
		}
		else
		{
			$sewing_line_id=$v['SEWING_LINE'];
			$sewing_line=$lineArr[$v['SEWING_LINE']];
		}
		
		if($lineSerialArr[$sewing_line_id]=="")
		{
			$lastSlNo++;
			$slNo=$lastSlNo;
			$lineSerialArr[$sewing_line_id]=$slNo;
		}
		else $slNo=$lineSerialArr[$sewing_line_id];

		$data_array[$v['FLOOR_ID']][$slNo][$sewing_line][$v['JOB_NO']][$v['PO_ID']][$v['ITEM_NUMBER_ID']]['buyer_name'] = $v['BUYER_NAME'];
		$data_array[$v['FLOOR_ID']][$slNo][$sewing_line][$v['JOB_NO']][$v['PO_ID']][$v['ITEM_NUMBER_ID']]['style'] = $v['STYLE_REF_NO'];
		$data_array[$v['FLOOR_ID']][$slNo][$sewing_line][$v['JOB_NO']][$v['PO_ID']][$v['ITEM_NUMBER_ID']]['po_number'] .= $v['PO_NUMBER']."**";
		$data_array[$v['FLOOR_ID']][$slNo][$sewing_line][$v['JOB_NO']][$v['PO_ID']][$v['ITEM_NUMBER_ID']]['line_id'] = $v['SEWING_LINE'];
		$data_array[$v['FLOOR_ID']][$slNo][$sewing_line][$v['JOB_NO']][$v['PO_ID']][$v['ITEM_NUMBER_ID']]['po_id'] = $v['PO_ID'];
		$data_array[$v['FLOOR_ID']][$slNo][$sewing_line][$v['JOB_NO']][$v['PO_ID']][$v['ITEM_NUMBER_ID']]['good_qnty'] += $v['GOOD_QNTY'];
		$data_array[$v['FLOOR_ID']][$slNo][$sewing_line][$v['JOB_NO']][$v['PO_ID']][$v['ITEM_NUMBER_ID']]['totay_input_qnty'] += $v['INPUT_QNTY'];
		
	 	for($h=$hour;$h<$last_hour;$h++)
		{
			$prod_hour="prod_hour".substr($start_hour_arr[$h],0,2)."";
			$data_array[$v['FLOOR_ID']][$slNo][$sewing_line][$v['JOB_NO']][$v['PO_ID']][$v['ITEM_NUMBER_ID']][$prod_hour]+=$v[csf($prod_hour)];
			
		}
		$po_unit_price_array[$v[csf('po_break_down_id')]]=$v[csf('UNIT_PRICE')];
	}

	/*===================================================================================== /
	/										Total Data 										/
	/===================================================================================== */
	$jobIds_cond = where_con_using_array($jobIdArr,0,"c.job_id");
	$sql = "SELECT c.id,a.floor_id,a.sewing_line, c.job_no_mst, c.item_number_id,c.order_rate,c.po_break_down_id as po_id,(CASE
	WHEN a.production_type = 4 THEN  a.production_date END) AS first_input_date, (case when b.production_type=4 then b.production_qnty else 0 end) as input_qty, (case when b.production_type=5 then b.production_qnty else 0 end) as output_qty,c.order_quantity FROM wo_po_color_size_breakdown  c LEFT JOIN pro_garments_production_dtls b ON c.id = b.color_size_break_down_id AND b.status_active = 1 AND b.is_deleted = 0 LEFT JOIN pro_garments_production_mst a ON a.id = b.mst_id and a.status_active = 1 AND a.is_deleted = 0 
	WHERE c.status_active = 1 AND c.is_deleted = 0 $jobIds_cond order by a.production_date desc";
	// echo $sql;
	$res = sql_select($sql);
	$tot_data_array = array();
	$order_qty_array = array();
	$order_rate_array = array();
	$check_array = array();
	foreach ($res as $val) 
	{
		if($check_array[$val['ID']]=="")
		{
			// echo $val['ID']."==".$val['ORDER_QUANTITY']."<br>";
			$order_qty_array[$val['PO_ID']][$val['ITEM_NUMBER_ID']]+=$val['ORDER_QUANTITY'];
			// $tot_data_array[$val['FLOOR_ID']][$val['SEWING_LINE']][$val['JOB_NO_MST']][$val['PO_ID']][$val['ITEM_NUMBER_ID']]['order_quantity']+=$val['ORDER_QUANTITY'];
			$check_array[$val['ID']] = $val['ID'];
			$order_rate_array[$val['PO_ID']][$val['ITEM_NUMBER_ID']]['order_rate'] += $val['ORDER_RATE'];
			$order_rate_array[$val['PO_ID']][$val['ITEM_NUMBER_ID']]['tot_row']++;
		}
		$tot_data_array[$val['FLOOR_ID']][$val['SEWING_LINE']][$val['JOB_NO_MST']][$val['PO_ID']][$val['ITEM_NUMBER_ID']]['input_qty']+=$val['INPUT_QTY'];
		$tot_data_array[$val['FLOOR_ID']][$val['SEWING_LINE']][$val['JOB_NO_MST']][$val['PO_ID']][$val['ITEM_NUMBER_ID']]['output_qty']+=$val['OUTPUT_QTY'];
		if($val['FIRST_INPUT_DATE']!="")
		{
			$tot_data_array[$val['FLOOR_ID']][$val['SEWING_LINE']][$val['JOB_NO_MST']][$val['PO_ID']][$val['ITEM_NUMBER_ID']]['first_input_date']=$val['FIRST_INPUT_DATE'];
		}
	}
		 
	// echo "<pre>";print_r($order_qty_array);die;
	/*===================================================================================== /
	/									Operation Bulletin 									/
	/===================================================================================== */
	$style_cond = where_con_using_array($all_style_arr,1,"a.style_ref");
	$sqlgsd="SELECT a.PROCESS_ID,a.style_ref,a.gmts_item_id,b.id, b.mst_id, b.row_sequence_no, b.body_part_id, b.lib_sewing_id, b.resource_gsd, b.attachment_id, b.efficiency, b.total_smv, b.target_on_full_perc from PPL_GSD_ENTRY_MST a,ppl_gsd_entry_dtls b where a.id=b.mst_id $style_cond and b.is_deleted=0 order by b.row_sequence_no asc";
	// echo $sqlgsd;die;
	$gsd_res=sql_select($sqlgsd);
	$mst_id_arr = array();
	foreach($gsd_res as $row)
	{
		$mst_id_arr[$row['MST_ID']] = $row['MST_ID'];
	}
	$mst_id_cond = where_con_using_array($mst_id_arr,0,"a.gsd_mst_id");
	// ======================================================================
	$balanceDataArray=array();
	$blData=sql_select("SELECT a.id, gsd_dtls_id, smv, layout_mp from ppl_balancing_mst_entry a, ppl_balancing_dtls_entry b where a.id=b.mst_id and a.balancing_page=1 $mst_id_cond and a.is_deleted=0 and b.is_deleted=0");
	foreach($blData as $row)
	{
		$balanceDataArray[$row[csf('gsd_dtls_id')]]['smv']=$row[csf('smv')];
		$balanceDataArray[$row[csf('gsd_dtls_id')]]['layout_mp']=$row[csf('layout_mp')];
	}

	$gsd_data_array = array();

	foreach($gsd_res as $slectResult)
	{
		if($balanceDataArray[$slectResult[csf('id')]]['smv']>0)	
		{
			$smv=$balanceDataArray[$slectResult[csf('id')]]['smv'];
		}
		else
		{
			$smv=$slectResult[csf('total_smv')];
		}
		
		$rescId=$slectResult[csf('resource_gsd')];
		$layOut=$balanceDataArray[$slectResult[csf('id')]]['layout_mp'];
		 
		if($rescId==40 || $rescId==41 || $rescId==43 || $rescId==44 || $rescId==48 || $rescId==68 || $rescId==69 || $rescId==70 || $rescId==147)
		{
			$helperSmv=$helperSmv+$smv;
			$helperMp=$helperMp+$layOut;
		}
		else if($rescId==53)
		{
			$fIMSmv=$fIMSmv+$smv;
			$fImMp=$fImMp+$layOut;
		}
		else if($rescId==54)
		{
			$fQISmv=$fQISmv+$smv;
			$fQiMp=$fQiMp+$layOut;
		}
		else if($rescId==55)
		{
			$polyHelperSmv=$polyHelperSmv+$smv;
			$polyHelperMp=$polyHelperMp+$layOut;
		}
		else if($rescId==56)
		{
			$pkSmv=$pkSmv+$smv;
			$pkMp=$pkMp+$layOut;
		}
		else if($rescId==90)
		{
			$htSmv=$htSmv+$smv;
			$htMp=$htMp+$layOut;
		}
		else if($rescId==176)
		{
			$imSmv=$imSmv+$smv;
			$imMp=$imMp+$layOut;
		}
		else
		{
			$machineSmv=$machineSmv+$smv;
			$machineMp=$machineMp+$layOut;
			
			$mpSumm[$rescId]+= $layOut;
		}
		$i++;
		
		$gsd_data_array[$slectResult['STYLE_REF']][$slectResult['GMTS_ITEM_ID']]['operator'] = $machineMp;
		$gsd_data_array[$slectResult['STYLE_REF']][$slectResult['GMTS_ITEM_ID']]['sew_helper'] = $helperMp;
	}

	/* foreach($gsd_res as $row)
	{
		if($balanceDataArray[$row[csf('id')]]['smv']>0)	
		{
			$smv=$balanceDataArray[$row[csf('id')]]['smv'];
		}
		else
		{
			$smv=$row[csf('total_smv')];
		}
		
		$rescId=$row[csf('resource_gsd')];
		$layOut=$balanceDataArray[$row[csf('id')]]['layout_mp'];
		 
		if($rescId==40 || $rescId==41 || $rescId==43 || $rescId==44 || $rescId==48 || $rescId==68 || $rescId==69 || $rescId==70 || $rescId==147)
		{
			$helperSmv=$helperSmv+$smv;
			$helperMp=$helperMp+$layOut;
		}
		else if($rescId==53)
		{
			$fIMSmv=$fIMSmv+$smv;
			$fImMp=$fImMp+$layOut;
		}
		else if($rescId==54)
		{
			$fQISmv=$fQISmv+$smv;
			$fQiMp=$fQiMp+$layOut;
		}
		else if($rescId==55)
		{
			$polyHelperSmv=$polyHelperSmv+$smv;
			$polyHelperMp=$polyHelperMp+$layOut;
		}
		else if($rescId==56)
		{
			$pkSmv=$pkSmv+$smv;
			$pkMp=$pkMp+$layOut;
		}
		else if($rescId==90)
		{
			$htSmv=$htSmv+$smv;
			$htMp=$htMp+$layOut;
		}
		else if($rescId==176)
		{
			$imSmv=$imSmv+$smv;
			$imMp=$imMp+$layOut;
		}
		else
		{
			$machineSmv=$machineSmv+$smv;
			$machineMp=$machineMp+$layOut;
			
			$mpSumm[$rescId]+= $layOut;
		}
		$gsd_data_array[$row['STYLE_REF']][$row['GMTS_ITEM_ID']]['operator'] += $machineSmv;
		$gsd_data_array[$row['STYLE_REF']][$row['GMTS_ITEM_ID']]['sew_helper'] += $helperMp;
		$i++;
	} */
	// echo "<pre>";print_r($gsd_data_array);echo "</pre>";
	/*===================================================================================== /
	/										smv sorce 										/
	/===================================================================================== */
	$lc_com_ids = implode(",",$lc_com_array);
	$poIds_cond = where_con_using_array($poIdArr,0,"b.id");
   	$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($lc_com_ids) and variable_list=25 and   status_active=1 and is_deleted=0");
	// echo $smv_source;
	
	if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
    if($smv_source==3) // from gsd enrty
	{
		$style_cond = where_con_using_array($all_style_arr,1,"a.style_ref");
		$sql_item="SELECT a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID  from PPL_GSD_ENTRY_MST a where a.APPLICABLE_PERIOD <= $txt_date and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 $style_cond and a.APPROVED=1 group by a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID ORDER BY a.GMTS_ITEM_ID ,a.APPLICABLE_PERIOD  DESC , a.id DESC";
		$gsdSqlResult=sql_select($sql_item);
		// echo $sql_item;die;
		
		foreach($gsdSqlResult as $rows)
		{
			foreach($style_wise_po_arr[$rows['STYLE_REF']] as $po_id)
			{
				if($item_smv_array[$po_id][$rows['GMTS_ITEM_ID']]=='')
				{
					$item_smv_array[$po_id][$rows['GMTS_ITEM_ID']]=$rows['TOTAL_SMV'];
				}
			}
		}
	}
	else
	{
		$sql_item="SELECT b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, c.smv_pcs, c.smv_pcs_precost,c.smv_set from wo_po_details_master a,wo_po_break_down b, wo_po_details_mas_set_details c where b.job_id=a.id and b.job_id=c.job_id and a.company_name in($lc_com_ids) $poIds_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		// echo $sql_item;
		$resultItem=sql_select($sql_item);
		
		foreach($resultItem as $itemData)
		{
			if($smv_source==1)
			{
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs')];
			}
			if($smv_source==2)
			{
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs_precost')];
			}
		}
	}
	// echo "<pre>";print_r($item_smv_array);echo "</pre>";

	// ========================== costing per and cm ===================== 
	$job_id_cond = where_con_using_array($jobIdArr,0,"job_id");
	$costing_per_arr = return_library_array("SELECT job_no, costing_per from wo_pre_cost_mst where status_active=1 $job_id_cond","job_no","costing_per");
	$cm_arr = return_library_array("SELECT job_no, cm_cost from wo_pre_cost_dtls where status_active=1 $job_id_cond","job_no","cm_cost");

	
	$tot_td = 0;
	for($k=$hour; $k<=$last_hour; $k++)
	{	
		$tot_td++;	
	}
	$tbl_width = 3310+($tot_td*50);

	$rowspan_arr = array();
	foreach ($data_array as $flr_id => $flr_data) 
	{
		foreach ($flr_data as $sl => $sl_data) 
		{
			foreach ($sl_data as $l_name => $l_data) 
			{
				foreach ($l_data as $job => $job_data) 
				{
					foreach ($job_data as $po_id => $po_data) 
					{
						foreach ($po_data as $itm_id => $r) 
						{
							$rowspan_arr[$flr_id]++;
						}
					}
				}
			}
		}
	}
	ob_start();
	?>               
	<fieldset style="width:<?=$tbl_width+20;?>px">
       <table width="3030" cellpadding="0" cellspacing="0"> 
            <tr class="form_caption">
                <td colspan="25" align="center"><strong><? echo $report_title; ?></strong></td> 
            </tr>
            <tr class="form_caption">
                <td colspan="25" align="center"><strong><? echo $companyArr[$comapny_id]; ?></strong></td> 
            </tr>
            <tr class="form_caption">
                <td colspan="25" align="center"><strong><? echo "Date:  ".change_date_format( str_replace("'","",trim($txt_date)) ); ?></strong></td> 
            </tr>
        </table>
         
		<!-- =================================== header start ===================================== -->
        <table id="table_header_1" class="rpt_table" width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr>
                    <th width="40"></p>SL</p></th>
                    <th width="80"></p>Floor Name</p></th>
                    <th width="80"></p>Line No</p></th>
                    <th width="80"></p>Buyer</p></th>
                    <th width="140"></p>Style</p></th>
                    <th width="120"></p>Item</p></th>
                    <th width="140"></p>Order No</p></th>
                    <th width="80"></p>FOB Value($)/PCS</p></th>
                    <th width="80"></p>CM Value Consider on 20%  of FOB Value/DZN</p></th>
                    <th width="80"></p>CM Value ($)/DZN</p></th>
                    <th width="80"></p>CM  Value ($)/PCS</p></th>
                    <th width="80"></p>Today Total CM Value ( $)</p></th>
                    <th width="80"></p>Order Qty.</p></th>
                    <th width="80"></p>Today Input</p></th>
                    <th width="80"></p>Total Input</p></th>
                    <th width="80"></p>Total Output Till Now</p></th>
                    <th width="80"></p>WIP</p></th>
                    <th width="60"></p>SMV</p></th>
                    <th width="60"></p>Input Min</p></th>
                    <th width="60"></p>Output Min</p></th>
                    <th width="80"></p>OP/As per Layout</p></th>
                    <th width="80"></p>Present OP/(Use M/C)</p></th>
                    <th width="80"></p>H/P Asper Layout</p></th>
                    <th width="80"></p>Present Helpar/(use H.P)</p></th>
                    <th width="80"></p>Total Man Power</p></th>
                    <th width="80"></p>Input Date</p></th>
                    <th width="80"></p>Total working Hour</p></th>
                    <th width="80"></p>After 5 PM Reduce Person</p></th>
                    <th width="80"></p>After 5 PM Total Working Minute</p></th>
                    <th width="80"></p>Target Per Hour</p></th>
                    <th width="80"></p>Total Target</p></th>
                    <th width="80"></p>Hourly Target CM</p></th>
                    <th width="80"></p>Day Earning CM Target </p></th>
					<?
					for($k=$hour; $k<=$last_hour; $k++)
					{
						?>
						<th width="50" style="vertical-align:middle"></p><div class="block_div"><?=substr($start_hour_arr[$k],0,5)."-<br>".substr($start_hour_arr[$k+1],0,5);?></div></p></th>
						<?
					}
					?>	
                    <th width="80"></p>Total Production</p></th>
                    <th width="80"></p>Today Line Wise Cost</p></th>
                    <th width="80"></p>Line Wise CM </p></th>
                    <th width="80"></p>Shortage/Gain</p></th>
                    <th width="80"></p>Line Wise  Tgt.Acv  Till Now</p></th>
                    <th width="80"></p>Line wise Eff. % Till Now</p></th>
                    <th width="80"></p>Remarks </p></th>
                </tr>
            </thead>
        </table>
		
		<!-- ====================================== body part ================================== -->
        <div style="width:<?=$tbl_width+20;?>px; max-height:400px; overflow-y:scroll" id="scroll_body">
            <table class="rpt_table" width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
				<tbody>
					<?
					$i = 1;
					$gr_fob_val_pcs = 0;
					$gr_cm_val_cons = 0;
					$gr_cm_val_dzn = 0;
					$gr_cm_val_pcs = 0;
					$gr_today_cm = 0;
					$gr_order_qty = 0;
					$gr_today_input = 0;
					$gr_total_input = 0;
					$gr_today_output = 0;
					$gr_wip = 0;
					$gr_input_min = 0;
					$gr_output_min = 0;
					$gr_layout_operator = 0;
					$gr_actual_operator = 0;
					$gr_layout_helper = 0;
					$gr_actual_helper = 0;
					$gr_manpower = 0;
					$gr_working_hour = 0;
					$gr_5pm_person = 0;
					$gr_5pm_wo_min = 0;
					$gr_target_per_hour = 0;
					$gr_target= 0;
					$gr_target_cm= 0;
					$gr_earnings_cm= 0;
					$gr_prod_qty= 0;
					$gr_prod_qty= 0;
					$gr_line_cost= 0;
					$gr_line_cm= 0;
					$gr_short_gain= 0;
					$gr_achive= 0;
					$gr_tot_array = array();
					$hourly_cm_array = array();
					foreach ($data_array as $flr_id => $flr_data) 
					{
						$l = 0;
						$f=0;
						$flr_fob_val_pcs = 0;
						$flr_cm_val_cons = 0;
						$flr_cm_val_dzn = 0;
						$flr_cm_val_pcs = 0;
						$flr_today_cm = 0;
						$flr_order_qty = 0;
						$flr_today_input = 0;
						$flr_total_input = 0;
						$flr_today_output = 0;
						$flr_wip = 0;
						$flr_input_min = 0;
						$flr_output_min = 0;
						$flr_layout_operator = 0;
						$flr_actual_operator = 0;
						$flr_layout_helper = 0;
						$flr_actual_helper = 0;
						$flr_manpower = 0;
						$flr_working_hour = 0;
						$flr_5pm_person = 0;
						$flr_5pm_wo_min = 0;
						$flr_target_per_hour = 0;
						$flr_target= 0;
						$flr_target_cm= 0;
						$flr_earnings_cm= 0;
						$flr_prod_qty= 0;
						$flr_line_cost= 0;
						$flr_line_cm= 0;
						$flr_short_gain= 0;
						$flr_achive= 0;
						ksort($flr_data);
						$floor_tot_array = array();
						foreach ($flr_data as $sl => $sl_data) 
						{
							foreach ($sl_data as $l_name => $l_data) 
							{
								foreach ($l_data as $job => $job_data) 
								{
									foreach ($job_data as $po_id => $po_data) 
									{
										foreach ($po_data as $itm_id => $r) 
										{
											$search_string = $flr_id."**".$r['line_id']."**".$job."**".$po_id."**".$itm_id."**".str_replace("'","", $txt_date);
											$po_number = implode(",",array_unique(array_filter(explode("**",$r['po_number']))));

											$po_unit_price = $po_unit_price_array[$po_id];
											$order_rate = $order_rate_array[$po_id][$itm_id]['order_rate'];
											$tot_row = $order_rate_array[$po_id][$itm_id]['tot_row'];
											$item_rate = ($tot_row) ? $order_rate / $tot_row : 0;
											$cm_fob_rate = (($item_rate*20)/100)*12;
											$cm_val_dzn = ($cm_arr[$job]/$po_unit_price)*$item_rate;
											// echo $r['po_number']."==(".$cm_arr[$job]."/".$po_unit_price.")*".$item_rate."<br>";
											$cm_val_pcs = $cm_val_dzn/12;
											$today_tot_cm = $r['good_qnty']*$cm_val_pcs;
											$target_cm = $today_tot_cm*$prod_resource_array[$r['line_id']]['terget_hour'];
											$earning_cm = $today_tot_cm*$r['good_qnty'];
											$item_smv = $item_smv_array[$po_id][$itm_id];
											$order_quantity = $order_qty_array[$po_id][$itm_id];
											// $order_quantity = $tot_data_array[$flr_id][$r['line_id']][$job][$po_id][$itm_id]['order_quantity'];
											$input_qty = $tot_data_array[$flr_id][$r['line_id']][$job][$po_id][$itm_id]['input_qty'];
											$output_qty = $tot_data_array[$flr_id][$r['line_id']][$job][$po_id][$itm_id]['output_qty'];
											$first_input_date = $tot_data_array[$flr_id][$r['line_id']][$job][$po_id][$itm_id]['first_input_date'];
											$wip = $input_qty - $output_qty;

											$man_power = $prod_resource_array[$r['line_id']]['man_power'];
											$act_operator = $prod_resource_array[$r['line_id']]['operator'];
											$helper = $prod_resource_array[$r['line_id']]['helper'];
											$terget_hour = $prod_resource_array[$r['line_id']]['terget_hour'];
											$working_hour = $prod_resource_array[$r['line_id']]['working_hour'];
											$tpd = $prod_resource_array[$r['line_id']]['tpd'];

											$gsd_operator = $gsd_data_array[$r['style']][$itm_id]['operator'];
											$sew_helper = $gsd_data_array[$r['style']][$itm_id]['sew_helper'];
											$line_wise_cost = $act_operator*32;
											$shortage_gain = $line_wise_cost - $today_tot_cm;
											$style = "";
											if($shortage_gain>0)
											{
												$style = "color:green";
											}
											else if ($shortage_gain<0) 
											{
												$style = "color:red";
											}
											else
											{
												$style = "color:black";
											}

											$achive = ($r['good_qnty']/$tpd)*100;

											$after_5pm_emp = 0;
											$after_5pm_wo_hour = 0;
											if(strtotime(date('d-M-Y')) == strtotime(str_replace("'","",$txt_date)))
											{
												if(date('H')>18)
												{
													$after_5pm_emp = $prod_resource_smv_adj_array[$r['line_id']]['number_of_emp'];
													$after_5pm_wo_hour = $prod_resource_smv_adj_array[$r['line_id']]['adjust_hour'];
												}
											}
											else
											{
												$ot_hour = $working_hour - $prod_resource_smv_adj_array[$r['line_id']]['adjust_hour'];
												// echo $working_hour ."-". $prod_resource_smv_adj_array[$r['line_id']]['adjust_hour']."<br>";
												if($ot_hour>10) // base time 8am to 6pm
												{
													$after_5pm_emp = $prod_resource_smv_adj_array[$r['line_id']]['number_of_emp'];
													$after_5pm_wo_hour = $prod_resource_smv_adj_array[$r['line_id']]['adjust_hour'];
												}												
											}

											$after_5pm_wo_min = $after_5pm_emp*$after_5pm_wo_hour*60;
											$input_min = ($man_power*60*$working_hour) - $after_5pm_wo_min;
											$output_min = $r['good_qnty']*$item_smv;
											$line_effi = ($output_min*$input_min)/100;
											

											if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
											?>
											<tr bgcolor="<? echo $bgcolor;?>" id="tr_<?= $i;?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')">
												<td valign="middle" width="40"><p><?=$i;?></p></td>
												<?
												if($f==0)
												{
													?>
													<td rowspan="<?=$rowspan_arr[$flr_id];?>" valign="middle" width="80"><div class="block_div" style="color: blue;font-weight:bold;"><?=$floorArr[$flr_id];?></div></td>
													<?
													$f++;
												}
												?>
												<td valign="middle" width="80"><p><?=$l_name;?></p></td>
												<td valign="middle" width="80"><p><?=$buyerArr[$r['buyer_name']];?></p></td>
												<td valign="middle" width="140"><p><?=$r['style'];?></p></td>
												<td valign="middle" width="120" title="<?=$itm_id;?>"><p><?=$garments_item[$itm_id];?></p></td>
												<td valign="middle" width="140"><p><?=$po_number;?></p></td>
												<td valign="middle" align="right" width="80"><p>$ <?=number_format($item_rate,2);?></p></td>
												<td valign="middle" align="right" width="80"><p>$ <?=number_format($cm_fob_rate,2);?></p></td>
												<td valign="middle" align="right" width="80"><p>$ <?=number_format($cm_val_dzn,2);?></p></td>
												<td valign="middle" align="right" width="80"><p>$ <?=number_format($cm_val_pcs,2);?></p></td>
												<td valign="middle" align="right" width="80"><p>$ <?=number_format($today_tot_cm,2);?></p></td>
												<td valign="middle" align="right" width="80"><p><?=number_format($order_quantity,0);?></p></td>
												<td valign="middle" align="right" width="80"><p><?=number_format($r['totay_input_qnty'],0);?></p></td>
												<td valign="middle" align="right" width="80"><p><?=number_format($input_qty,0);?></p></td>
												<td valign="middle" align="right" width="80"><p><?=number_format($output_qty,0);?></p></td>
												<td valign="middle" align="right" width="80"><p><?=number_format($wip,0);?></p></td>
												<td valign="middle" align="right" width="60"><p><?=number_format($item_smv,2);?></p></td>
												<td valign="middle" align="right" width="60"><p><?=number_format($input_min,2);?></p></td>
												<td valign="middle" align="right" width="60"><p><?=number_format($output_min,2);?></p></td>
												<td valign="middle" align="right" width="80"><p><?=number_format($gsd_operator,0);?></p></td>
												<td valign="middle" align="right" width="80"><p><?=number_format($act_operator,0);?></p></td>
												<td valign="middle" align="right" width="80"><p><?=number_format($sew_helper,0);?></p></td>
												<td valign="middle" align="right" width="80"><p><?=number_format($helper,0);?></p></td>
												<td valign="middle" align="right" width="80"><p><?=number_format($man_power,0);?></p></td>
												<td valign="middle" align="center" width="80"><p><?=change_date_format($first_input_date);?></p></td>
												<td valign="middle" align="right" width="80"><p><?=number_format($working_hour,0);?></p></td>
												<td valign="middle" align="right" width="80"><p><?=number_format($after_5pm_emp,0);?></p></td>
												<td valign="middle" align="right" width="80"><p><?=number_format($after_5pm_wo_min,2);?></p></td>
												<td valign="middle" align="right" width="80"><p><?=number_format($terget_hour,0);?></p></td>
												<td valign="middle" align="right" width="80"><p><?=number_format($tpd,0);?></p></td>
												<td valign="middle" align="right" width="80"><p>$ <?=number_format($target_cm,2);?></p></td>
												<td valign="middle" align="right" width="80"><p>$ <?=number_format($earning_cm,2);?></p></td>
												<?
												$line_tot = 0;
												for($k=$hour; $k<=$last_hour; $k++)
												{
													$rowspan = 0;
													if($k==$lunch_start_time) // lunch hour 
													{
														if($l==0)
														{
															?>
															<td rowspan="<?=$rowspan_arr[$flr_id];?>" valign="middle" width="50"><div class="block_div" style="color: blue;font-weight:bold;">Lunch Time</div></td>
															<?
															$l++;
														}
													}
													else
													{
														$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
														?>
														<td valign="middle" align="right" width="50"><?=number_format($r[$prod_hour],0);?></td>
														<?
														$line_tot += $r[$prod_hour];
														$floor_tot_array[$flr_id][$prod_hour] += $r[$prod_hour];
														$gr_tot_array[$prod_hour] += $r[$prod_hour];
														$hourly_cm_array[$flr_id][$prod_hour] += $r[$prod_hour]*$cm_val_pcs;
													}
												}
												?>
												<td valign="middle" align="right" width="80"><p><?=number_format($line_tot,0);?></p></td>
												<td valign="middle" align="right" width="80"><p>$ <?=number_format($line_wise_cost,2);?></p></td>
												<td valign="middle" align="right" width="80"><p>$ <?=number_format($today_tot_cm,2);?></p></td>
												<td valign="middle" align="right" width="80"><p style="<?=$style;?>">$ <?=number_format($shortage_gain,2);?></p></td>
												<td valign="middle" align="right" width="80"><p><?=number_format($achive,2);?></p></td>
												<td valign="middle" align="right" width="80"><p><?=number_format($line_effi,2);?></p></td>
												<td valign="middle" align="center" width="80">
													<a href="javascript:void(0)" onclick="show_remarks_popup('<?=$search_string;?>')">View</a>
												</td>
											</tr>
											<?
											$i++;
											
											$gr_fob_val_pcs += $item_rate;
											$gr_cm_val_cons += $cm_fob_rate;
											$gr_cm_val_dzn += $cm_val_dzn;
											$gr_cm_val_pcs += $cm_val_pcs;
											$gr_today_cm += $today_tot_cm;
											$gr_order_qty += $order_quantity;
											$gr_today_input += $r['totay_input_qnty'];
											$gr_total_input += $input_qty;
											$gr_today_output += $output_qty;
											$gr_wip += $wip;
											$gr_input_min += $input_min;
											$gr_output_min += $output_min;
											$gr_layout_operator += $gsd_operator;
											$gr_actual_operator += $act_operator;
											$gr_layout_helper += $sew_helper;
											$gr_actual_helper += $helper;
											$gr_manpower += $man_power;
											$gr_working_hour += $working_hour;
											$gr_5pm_person += $after_5pm_emp;
											$gr_5pm_wo_min += $after_5pm_wo_min;
											$gr_target_per_hour += $terget_hour;
											$gr_target+= $tpd;
											$gr_target_cm+= $target_cm;
											$gr_earnings_cm+= $earning_cm;
											$gr_prod_qty+= $line_tot;
											$gr_line_cost+= $line_wise_cost;
											$gr_line_cm+= $today_tot_cm;
											$gr_short_gain+= $shortage_gain;
											$gr_short_gain+= $shortage_gain;
											$gr_achive += $achive;
											
											$flr_fob_val_pcs += $item_rate;
											$flr_cm_val_cons += $cm_fob_rate;
											$flr_cm_val_dzn += $cm_val_dzn;
											$flr_cm_val_pcs += $cm_val_pcs;
											$flr_today_cm += $today_tot_cm;
											$flr_order_qty += $order_quantity;
											$flr_today_input += $r['totay_input_qnty'];
											$flr_total_input += $input_qty;
											$flr_today_output += $output_qty;
											$flr_wip += $wip;
											$flr_input_min += $input_min;
											$flr_output_min += $output_min;
											$flr_layout_operator += $gsd_operator;
											$flr_actual_operator += $act_operator;
											$flr_layout_helper += $sew_helper;
											$flr_actual_helper += $helper;
											$flr_manpower += $man_power;
											$flr_working_hour += $working_hour;
											$flr_5pm_person += $after_5pm_emp;
											$flr_5pm_wo_min += $after_5pm_wo_min;
											$flr_target_per_hour += $terget_hour;
											$flr_target+= $tpd;
											$flr_target_cm+= $target_cm;
											$flr_earnings_cm+= $earning_cm;

											$flr_prod_qty+= $line_tot;
											$flr_line_cost+= $line_wise_cost;
											$flr_line_cm+= $today_tot_cm;
											$flr_short_gain+= $shortage_gain;
											$flr_achive += $achive;
										}
									}
								}
							}
						}
						?>
						<tr style="text-align: right;font-weight:bold;background:#cddcdc">
							<td colspan="7">Sub Total</td>
							<td>$ <?=number_format($flr_fob_val_pcs,2);?></td>
							<td>$ <?=number_format($flr_cm_val_cons,2);?></td>
							<td>$ <?=number_format($flr_cm_val_dzn,2);?></td>
							<td>$ <?=number_format($flr_cm_val_pcs,2);?></td>
							<td>$ <?=number_format($flr_today_cm,2);?></td>
							<td><?=number_format($flr_order_qty,0);?></td>
							<td><?=number_format($flr_today_input,2);?></td>
							<td><?=number_format($flr_total_input,2);?></td>
							<td><?=number_format($flr_today_output,2);?></td>
							<td><?=number_format($flr_wip,2);?></td>
							<td></td>
							<td><?=number_format($flr_input_min,2);?></td>
							<td><?=number_format($flr_output_min,2);?></td>
							<td><?=number_format($flr_layout_operator,0);?></td>
							<td><?=number_format($flr_actual_operator,2);?></td>
							<td><?=number_format($flr_layout_helper,2);?></td>
							<td><?=number_format($flr_actual_helper,2);?></td>
							<td><?=number_format($flr_manpower,2);?></td>
							<td></td>
							<td><?=number_format($flr_working_hour,2);?></td>
							<td><?=number_format($flr_5pm_person,0);?></td>
							<td><?=number_format($flr_5pm_wo_min,2);?></td>
							<td><?=number_format($flr_target_per_hour,2);?></td>
							<td><?=number_format($flr_target,2);?></td>
							<td>$ <?=number_format($flr_target_cm,2);?></td>
							<td>$ <?=number_format($flr_earnings_cm,2);?></td>							
							<?
							$flr_tot = 0;
							for($k=$hour; $k<=$last_hour; $k++)
							{
								$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
								?>
								<td width="50"><?=number_format($floor_tot_array[$flr_id][$prod_hour],0);?></td>
								<?
								$flr_tot += $floor_tot_array[$flr_id][$prod_hour];
							}
							?>
							<td><?=number_format($flr_tot,0);?></td>
							<td>$ <?=number_format($flr_line_cost,2);?></td>
							<td>$ <?=number_format($flr_line_cm,2);?></td>
							<td>$ <?=number_format($flr_short_gain,2);?></td>
							<td><?=number_format($flr_achive,2);?></td>
							<td></td>
							<td></td>							
						</tr>
						<!-- ========================== cm ================================ -->
						<tr style="text-align: right;font-weight:bold;background:#dccdcd">
							<td colspan="33"> Hourly CM Earn</td>						
							<?
							$tot_cm_earn = 0;
							for($k=$hour; $k<=$last_hour; $k++)
							{
								$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
								?>
								<td width="50">$ <?= number_format($hourly_cm_array[$flr_id][$prod_hour],2);?></td>
								<?
								$tot_cm_earn += $hourly_cm_array[$flr_id][$prod_hour];
							}
							?>
							<td align="center" rowspan="2" colspan="7" valign="middle">$ <?=number_format($tot_cm_earn,2);?></td>
							
						</tr>
						<!-- ============================ target vs achive ========================== -->
						<tr style="text-align: right;font-weight:bold;background:#cccccd">
							<td colspan="33">Hourly Target Achivment</td>						
							<?
							for($k=$hour; $k<=$last_hour; $k++)
							{
								$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
								?>
								<td width="50">$ <?=number_format(($floor_tot_array[$flr_id][$prod_hour]/$flr_target_per_hour),2);?></td>
								<?
							}
							?>							
						</tr>
						<?
					}
					?>
				</tbody>
            </table>
		</div>
		<!-- ==================================== footer part ================================ -->
		<table id="table_footer_1" class="rpt_table" width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
            <tfoot>
                <tr>
                    <th width="40"></th>
                    <th width="80"></th>
                    <th width="80"></th>
                    <th width="80"></th>
                    <th width="140"></th>
                    <th width="120"></th>
                    <th width="140">Grand Total:</th>

					<th width="80">$ <?=number_format($gr_fob_val_pcs,2);?></th>
					<th width="80">$ <?=number_format($gr_cm_val_cons,2);?></th>
					<th width="80">$ <?=number_format($gr_cm_val_dzn,2);?></th>
					<th width="80">$ <?=number_format($gr_cm_val_pcs,2);?></th>
					<th width="80">$ <?=number_format($gr_today_cm,2);?></th>
					<th width="80"><?=number_format($gr_order_qty,0);?></th>
					<th width="80"><?=number_format($gr_today_input,2);?></th>
					<th width="80"><?=number_format($gr_total_input,2);?></th>
					<th width="80"><?=number_format($gr_today_output,2);?></th>
					<th width="80"><?=number_format($gr_wip,2);?></th>
					<th width="60"></th>
					<th width="60"><?=number_format($gr_input_min,2);?></th>
					<th width="60"><?=number_format($gr_output_min,2);?></th>
					<th width="80"><?=number_format($gr_layout_operator,0);?></th>
					<th width="80"><?=number_format($gr_actual_operator,2);?></th>
					<th width="80"><?=number_format($gr_layout_helper,2);?></th>
					<th width="80"><?=number_format($gr_actual_helper,2);?></th>
					<th width="80"><?=number_format($gr_manpower,2);?></th>
					<th width="80"></th>
					<th width="80"><?=number_format($gr_working_hour,2);?></th>
					<th width="80"><?=number_format($gr_5pm_person,2);?></th>
					<th width="80"><?=number_format($gr_5pm_wo_min,2);?></th>
					<th width="80"><?=number_format($gr_target_per_hour,2);?></th>
					<th width="80"><?=number_format($gr_target,2);?></th>
					<th width="80">$ <?=number_format($gr_target_cm,2);?></th>
					<th width="80">$ <?=number_format($gr_earnings_cm,2);?></th>							
					<?
					$gr_tot = 0;			
					for($k=$hour; $k<=$last_hour; $k++)
					{
						$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
						?>
						<th width="50"><?=number_format($gr_tot_array[$prod_hour],0);?></th>
						<?
						$gr_tot += $gr_tot_array[$prod_hour];
					}
					?>
                    <th width="80"><p><?=number_format($gr_tot,0);?></p></th>
					<th width="80">$ <?=number_format($flr_line_cost,2);?></th>
					<th width="80">$ <?=number_format($flr_line_cm,2);?></th>
					<th width="80">$ <?=number_format($flr_short_gain,2);?></th>
					<th width="80"><?=number_format($flr_achive,2);?></th>
					<th width="80"></th>
					<th width="80"></th>
                </tr>
            </tfoot>
        </table>
	</fieldset>
	<?    
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename,'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data####$filename";
	exit();      
}

if($action=="tot_prod")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	?>



		<fieldset style="width:520px; ">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" align="center">
					<thead>
						<th width="30">SL</th>
						<th width="120">Order No</th>
						<th width="70">Item Smv</th>
						<th width="100">Production Qnty</th>
						<th width="100">Produced Min.</th>
					</thead>
			
						<?
					
						$sql_pop=sql_select("select  c.po_number,a.po_break_down_id,
							sum(a.production_quantity) as good_qnty, 
							sum(CASE WHEN production_hour ='1' and a.production_type=5 THEN production_quantity else 0 END) AS good_1am,
							sum(CASE WHEN production_hour ='2' and a.production_type=5 THEN production_quantity else 0 END) AS good_2am,
							sum(CASE WHEN production_hour ='3' and a.production_type=5 THEN production_quantity else 0 END) AS good_3am,
							sum(CASE WHEN production_hour ='4' and a.production_type=5 THEN production_quantity else 0 END) AS good_4am,
							sum(CASE WHEN production_hour ='5' and a.production_type=5 THEN production_quantity else 0 END) AS good_5am,
							sum(CASE WHEN production_hour ='6' and a.production_type=5 THEN production_quantity else 0 END) AS good_6am,
							sum(CASE WHEN production_hour ='7' and a.production_type=5 THEN production_quantity else 0 END) AS good_7am,
							sum(CASE WHEN production_hour ='8' and a.production_type=5 THEN production_quantity else 0 END) AS good_8am,
							sum(CASE WHEN production_hour ='9' and a.production_type=5 THEN production_quantity else 0 END) AS good_9am,
							sum(CASE WHEN production_hour ='10' and a.production_type=5 THEN production_quantity else 0 END) AS good_10am,
							sum(CASE WHEN production_hour ='11' and a.production_type=5 THEN production_quantity else 0 END) AS good_11am,
							sum(CASE WHEN production_hour ='12' and a.production_type=5 THEN production_quantity else 0 END) AS good_12am,
							sum(CASE WHEN production_hour ='13' and a.production_type=5 THEN production_quantity else 0 END) AS good_1pm,
							sum(CASE WHEN production_hour ='14' and a.production_type=5 THEN production_quantity else 0 END) AS good_2pm,
							sum(CASE WHEN production_hour ='15' and a.production_type=5 THEN production_quantity else 0 END) AS good_3pm,
							sum(CASE WHEN production_hour ='16' and a.production_type=5 THEN production_quantity else 0 END) AS good_4pm,
							sum(CASE WHEN production_hour ='17' and a.production_type=5 THEN production_quantity else 0 END) AS good_5pm,
							sum(CASE WHEN production_hour ='18' and a.production_type=5 THEN production_quantity else 0 END) AS good_6pm,
							sum(CASE WHEN production_hour ='19' and a.production_type=5 THEN production_quantity else 0 END) AS good_7pm,
							sum(CASE WHEN production_hour ='20' and a.production_type=5 THEN production_quantity else 0 END) AS good_8pm,
							sum(CASE WHEN production_hour ='21' and a.production_type=5 THEN production_quantity else 0 END) AS good_9pm,
							sum(CASE WHEN production_hour ='22' and a.production_type=5 THEN production_quantity else 0 END) AS good_10pm,
							sum(CASE WHEN production_hour ='23' and a.production_type=5 THEN production_quantity else 0 END) AS good_11pm,
							sum(CASE WHEN production_hour ='24' and a.production_type=5 THEN production_quantity else 0 END) AS good_12pm
							from pro_gar_prod_gross_mst a, wo_po_details_master b, wo_po_break_down c
							where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and a.company_id=".$company_id."  and a.floor_id=".$floor_id." and a.sewing_line=".$sewing_line."  and a.po_break_down_id in(".$po_id.") and a.production_date='".$prod_date."'  group by c.po_number,a.po_break_down_id order by  c.po_number ");
			
					$new_smv=array();
					$item_smv_pop=explode("****",$item_smv);
						$order_id="";
					foreach($item_smv_pop as $po_id_smv) 
						{
							$po_id_smv_pop=explode("**",$po_id_smv);
							$new_smv[$po_id_smv_pop[0]]=$po_id_smv_pop[1];
							/*  if($order_id!="") $order_id.=",".$po_id_smv_pop[0]; 
							else $order_id=$po_id_smv_pop[0]; */

							
						}
						
						
					
						
						$total_producd_min=0;
						$i=1; $total_qnty=0;
						foreach($sql_pop as $pop_val)
						{
						$po_qty=0;	
						$po_number=$pop_val[csf('po_number')];
						$po_qty+=$pop_val[csf('good_1am')];
						$po_qty+=$pop_val[csf('good_3am')];
						$po_qty+=$pop_val[csf('good_4am')];
						$po_qty+=$pop_val[csf('good_5am')];
						$po_qty+=$pop_val[csf('good_6am')];
						$po_qty+=$pop_val[csf('good_7am')];
						$po_qty+=$pop_val[csf('good_8am')];
						$po_qty+=$pop_val[csf('good_9am')];
						$po_qty+=$pop_val[csf('good_10am')];
						$po_qty+=$pop_val[csf('good_11am')];
						$po_qty+=$pop_val[csf('good_12am')];
						$po_qty+=$pop_val[csf('good_1pm')];
						$po_qty+=$pop_val[csf('good_2pm')];
						$po_qty+=$pop_val[csf('good_3pm')];
						$po_qty+=$pop_val[csf('good_4pm')];
						$po_qty+=$pop_val[csf('good_5pm')];
						$po_qty+=$pop_val[csf('good_6pm')];
						$po_qty+=$pop_val[csf('good_7pm')];
						$po_qty+=$pop_val[csf('good_8pm')];
						$po_qty+=$pop_val[csf('good_9pm')];
						$po_qty+=$pop_val[csf('good_10pm')];
						$po_qty+=$pop_val[csf('good_11pm')];
						$po_qty+=$pop_val[csf('good_12pm')];
						$item_smv_pop=$new_smv[$pop_val[csf('po_break_down_id')]];
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";	
						
							$total_qnty+=$row[csf('qnty')];
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><? echo $i; ?></td>
								<td width="120" align="center"><? echo $po_number; ?></td>
								<td align="right"><? echo $item_smv_pop; ?>&nbsp;</td>
								<td align="right"><? $total_po_qty+=$po_qty; echo $po_qty; ?>&nbsp;</td>
								<td align="right">
									<?
									$producd_min=$po_qty*$item_smv_pop;  $total_producd_min+=$producd_min;
									echo $po_qty*$item_smv_pop;
									?>&nbsp;</td>
							</tr>
						<?
						$i++;
						}
						?>
						<tfoot>
							<th colspan="3" align="right">Total</th>
						
							<th align="right"><? echo $total_po_qty; ?>&nbsp;</th>
							<th align="right"><? echo $total_producd_min; ?>&nbsp;</th>
						</tfoot>
					</table>
			
			</div>
		</fieldset>   
	<?
	exit();
}

if($action=="remarks_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	list($floor,$line,$job,$po_id,$item,$date) = explode("**",$search_string);

	$sql = "SELECT a.sys_number,b.remarks,to_char(b.production_hour,'HH24 : MI') as prod_hour from PRO_GMTS_DELIVERY_MST a, pro_garments_production_mst b where a.id=b.delivery_mst_id and b.po_break_down_id=$po_id and b.item_number_id=$item and b.sewing_line=$line and b.production_date='$date' and b.floor_id=$floor and b.status_active=1 and b.is_deleted=0 and b.production_type=5 group by a.sys_number,b.remarks,b.production_hour order by a.sys_number";
	// echo $sql;
	$res = sql_select($sql);

	?>



		<fieldset style="width:520px; ">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="100%" cellpadding="0" cellspacing="0" align="center">
					<thead>
						<th width="30">SL</th>
						<th width="110">Entry No</th>
						<th width="40">Hour</th>
						<th width="">Remarks</th>
					</thead>
						<?
						foreach ($res as $v) 
						{
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
									<td><? echo $i; ?></td>
									<td align="left"><?=$v['SYS_NUMBER']; ?></td>
									<td align="center"><?=$v['PROD_HOUR']; ?>&nbsp;</td>
									<td align="left"><?=$v['REMARKS']; ?>&nbsp;</td>
								</tr>
							<?
							$i++;
						}
						?>
					</table>			
			</div>
		</fieldset>   
	<?
	exit();
}