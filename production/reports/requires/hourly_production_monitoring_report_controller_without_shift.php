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
	echo create_drop_down( "cbo_location_id", 150, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/hourly_production_monitoring_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );",0 ); 
	exit();    	 
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor_id", 130, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );     	 	
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
	if(str_replace("'","",$cbo_location_id)==0) 
	{
		$subcon_location="";
		$location="";
	}
	else 
	{
		$location="and a.location=".str_replace("'","",$cbo_location_id)."";
		$subcon_location="and a.location_id=".str_replace("'","",$cbo_location_id)."";
	}
	if(str_replace("'","",$cbo_floor_id)==0) $floor=""; else $floor="and a.floor_id=".str_replace("'","",$cbo_floor_id)."";
    if(str_replace("'","",$hidden_line_id)==0)
	{ $line=""; 
	$subcon_line="";
	}
	else 
	{
		$subcon_line="and a.line_id in(".str_replace("'","",$hidden_line_id).")";
		$line="and a.sewing_line in(".str_replace("'","",$hidden_line_id).")";
	}
	if(str_replace("'","",trim($txt_date))=="") $txt_date_from=""; else $txt_date_from=" and a.production_date=$txt_date";
 if($prod_reso_allo[0]==1)
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
	
   $smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($manufacturing_company) and variable_list=25 and status_active=1 and is_deleted=0");
   if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
   if($smv_source==3)
		{
			$sql_item="select b.id, a.sam_style, a.gmts_item_id from ppl_gsd_entry_mst a, wo_po_break_down b where b.job_no_mst=a.po_job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			$resultItem=sql_select($sql_item);
			foreach($resultItem as $itemData)
			{
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('sam_style')];
			}
		}
		else
		{
			$sql_item="select b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, smv_pcs, smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no and a.company_name in($manufacturing_company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
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
					sum(CASE WHEN a.production_hour>0 and a.production_hour<=1 and a.production_type=5 THEN production_quantity else 0 END) AS good_1am,
					sum(CASE WHEN a.production_hour>1 and a.production_hour<=2 and a.production_type=5 THEN production_quantity else 0 END) AS good_2am,
					sum(CASE WHEN a.production_hour>2 and a.production_hour<=3 and a.production_type=5 THEN production_quantity else 0 END) AS good_3am,
					sum(CASE WHEN a.production_hour>3 and a.production_hour<=4 and a.production_type=5 THEN production_quantity else 0 END) AS good_4am,
					sum(CASE WHEN a.production_hour>4 and a.production_hour<=5 and a.production_type=5 THEN production_quantity else 0 END) AS good_5am,
					sum(CASE WHEN a.production_hour>5 and a.production_hour<=6 and a.production_type=5 THEN production_quantity else 0 END) AS good_6am,
					sum(CASE WHEN a.production_hour>6 and a.production_hour<=7 and a.production_type=5 THEN production_quantity else 0 END) AS good_7am,
					sum(CASE WHEN a.production_hour>7 and a.production_hour<=8 and a.production_type=5 THEN production_quantity else 0 END) AS good_8am,
					sum(CASE WHEN a.production_hour>8 and a.production_hour<=9 and a.production_type=5 THEN production_quantity else 0 END) AS good_9am,
					sum(CASE WHEN a.production_hour>9 and a.production_hour<=10 and a.production_type=5 THEN production_quantity else 0 END) AS good_10am,
					sum(CASE WHEN a.production_hour>10 and a.production_hour<=11 and a.production_type=5 THEN production_quantity else 0 END) AS good_11am,
					sum(CASE WHEN a.production_hour>11 and a.production_hour<=12 and a.production_type=5 THEN production_quantity else 0 END) AS good_12pm,
					sum(CASE WHEN a.production_hour>12 and a.production_hour<=13 and a.production_type=5 THEN production_quantity else 0 END) AS good_1pm,
					sum(CASE WHEN a.production_hour>13 and a.production_hour<=14 and a.production_type=5 THEN production_quantity else 0 END) AS good_2pm,
					sum(CASE WHEN a.production_hour>14 and a.production_hour<=15 and a.production_type=5 THEN production_quantity else 0 END) AS good_3pm,
					sum(CASE WHEN a.production_hour>15 and a.production_hour<=16 and a.production_type=5 THEN production_quantity else 0 END) AS good_4pm,
					sum(CASE WHEN a.production_hour>16 and a.production_hour<=17 and a.production_type=5 THEN production_quantity else 0 END) AS good_5pm,
					sum(CASE WHEN a.production_hour>17 and a.production_hour<=18 and a.production_type=5 THEN production_quantity else 0 END) AS good_6pm,
					sum(CASE WHEN a.production_hour>18 and a.production_hour<=19 and a.production_type=5 THEN production_quantity else 0 END) AS good_7pm,
					sum(CASE WHEN a.production_hour>19 and a.production_hour<=20 and a.production_type=5 THEN production_quantity else 0 END) AS good_8pm,
					sum(CASE WHEN a.production_hour>20 and a.production_hour<=21 and a.production_type=5 THEN production_quantity else 0 END) AS good_9pm,
					sum(CASE WHEN a.production_hour>21 and a.production_hour<=22 and a.production_type=5 THEN production_quantity else 0 END) AS good_10pm,
					sum(CASE WHEN a.production_hour>22 and a.production_hour<=23 and a.production_type=5 THEN production_quantity else 0 END) AS good_11pm,
					sum(CASE WHEN a.production_hour>23 and a.production_hour<=24 and a.production_type=5 THEN production_quantity else 0 END) AS good_12am
						from pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c
						where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0                          $company_name $location $floor $line   $txt_date_from 
						 group by a.company_id, a.location, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name,a.item_number_id,c.po_number order by a.location, a.floor_id,a.sewing_line,a.po_break_down_id");

	     	}
			
		if($db_type==2)
		{
			
			
		$sql=sql_select("select  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line,
					 b.buyer_name  as buyer_name,
					 a.po_break_down_id, a.item_number_id,
					 c.po_number as po_number,
					sum(a.production_quantity) as good_qnty, 
					sum(CASE WHEN a.production_hour>0 and a.production_hour<=1 and a.production_type=5 THEN production_quantity else 0 END) AS good_1am,
					sum(CASE WHEN a.production_hour>1 and a.production_hour<=2 and a.production_type=5 THEN production_quantity else 0 END) AS good_2am,
					sum(CASE WHEN a.production_hour>2 and a.production_hour<=3 and a.production_type=5 THEN production_quantity else 0 END) AS good_3am,
					sum(CASE WHEN a.production_hour>3 and a.production_hour<=4 and a.production_type=5 THEN production_quantity else 0 END) AS good_4am,
					sum(CASE WHEN a.production_hour>4 and a.production_hour<=5 and a.production_type=5 THEN production_quantity else 0 END) AS good_5am,
					sum(CASE WHEN a.production_hour>5 and a.production_hour<=6 and a.production_type=5 THEN production_quantity else 0 END) AS good_6am,
					sum(CASE WHEN a.production_hour>6 and a.production_hour<=7 and a.production_type=5 THEN production_quantity else 0 END) AS good_7am,
					sum(CASE WHEN a.production_hour>7 and a.production_hour<=8 and a.production_type=5 THEN production_quantity else 0 END) AS good_8am,
					sum(CASE WHEN a.production_hour>8 and a.production_hour<=9 and a.production_type=5 THEN production_quantity else 0 END) AS good_9am,
					sum(CASE WHEN a.production_hour>9 and a.production_hour<=10 and a.production_type=5 THEN production_quantity else 0 END) AS good_10am,
					sum(CASE WHEN a.production_hour>10 and a.production_hour<=11 and a.production_type=5 THEN production_quantity else 0 END) AS good_11am,
					sum(CASE WHEN a.production_hour>11 and a.production_hour<=12 and a.production_type=5 THEN production_quantity else 0 END) AS good_12pm,
					sum(CASE WHEN a.production_hour>12 and a.production_hour<=13 and a.production_type=5 THEN production_quantity else 0 END) AS good_1pm,
					sum(CASE WHEN a.production_hour>13 and a.production_hour<=14 and a.production_type=5 THEN production_quantity else 0 END) AS good_2pm,
					sum(CASE WHEN a.production_hour>14 and a.production_hour<=15 and a.production_type=5 THEN production_quantity else 0 END) AS good_3pm,
					sum(CASE WHEN a.production_hour>15 and a.production_hour<=16 and a.production_type=5 THEN production_quantity else 0 END) AS good_4pm,
					sum(CASE WHEN a.production_hour>16 and a.production_hour<=17 and a.production_type=5 THEN production_quantity else 0 END) AS good_5pm,
					sum(CASE WHEN a.production_hour>17 and a.production_hour<=18 and a.production_type=5 THEN production_quantity else 0 END) AS good_6pm,
					sum(CASE WHEN a.production_hour>18 and a.production_hour<=19 and a.production_type=5 THEN production_quantity else 0 END) AS good_7pm,
					sum(CASE WHEN a.production_hour>19 and a.production_hour<=20 and a.production_type=5 THEN production_quantity else 0 END) AS good_8pm,
					sum(CASE WHEN a.production_hour>20 and a.production_hour<=21 and a.production_type=5 THEN production_quantity else 0 END) AS good_9pm,
					sum(CASE WHEN a.production_hour>21 and a.production_hour<=22 and a.production_type=5 THEN production_quantity else 0 END) AS good_10pm,
					sum(CASE WHEN a.production_hour>22 and a.production_hour<=23 and a.production_type=5 THEN production_quantity else 0 END) AS good_11pm,
					sum(CASE WHEN a.production_hour>23 and a.production_hour<=24 and a.production_type=5 THEN production_quantity else 0 END) AS good_12am
					from pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c
					where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0                          $company_name $location $floor $line   $txt_date_from 
					 group by a.company_id, a.location, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name,a.item_number_id,c.po_number order by a.location, a.floor_id,a.sewing_line,a.po_break_down_id");

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
		$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['12pm']+=$val[csf('good_12pm')];
		 
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
	   $production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['12am']+=$val[csf('good_12am')]; 
		
	}
	
	$sql_sub_contuct=sql_select( "select  a.company_id, a.location_id, a.floor_id, a.production_date, a.line_id,
		                 b.party_id  as buyer_name,
						 a.order_id,
					     c.order_no as po_number,max(c.smv) as smv,
						sum(a.production_qnty) as good_qnty, 
						sum(CASE WHEN a.hour>0 and a.hour<=1 and a.production_type=2 THEN production_qnty else 0 END) AS good_1am,
						sum(CASE WHEN hour>1 and hour<=2 and a.production_type=2 THEN production_qnty else 0 END) AS good_2am,
						sum(CASE WHEN hour>2 and hour<=3 and a.production_type=2 THEN production_qnty else 0 END) AS good_3am,
						sum(CASE WHEN hour>3 and hour<=4 and a.production_type=2 THEN production_qnty else 0 END) AS good_4am,
						sum(CASE WHEN hour>4 and hour<=5 and a.production_type=2 THEN production_qnty else 0 END) AS good_2am,
						sum(CASE WHEN hour>5 and hour<=6 and a.production_type=2 THEN production_qnty else 0 END) AS good_6am,
						sum(CASE WHEN hour>6 and hour<=7 and a.production_type=2 THEN production_qnty else 0 END) AS good_7am,
						sum(CASE WHEN hour>7 and hour<=8 and a.production_type=2 THEN production_qnty else 0 END) AS good_8am,
						sum(CASE WHEN hour>8 and hour<=9 and a.production_type=2 THEN production_qnty else 0 END) AS good_9am,
						sum(CASE WHEN hour>9 and hour<=10 and a.production_type=2 THEN production_qnty else 0 END) AS good_10am,
						sum(CASE WHEN hour>10 and hour<=11 and a.production_type=2 THEN production_qnty else 0 END) AS good_11am,
						sum(CASE WHEN hour>11 and hour<=12 and a.production_type=2 THEN production_qnty else 0 END) AS good_12pm,
						sum(CASE WHEN hour>12 and hour<=13 and a.production_type=2 THEN production_qnty else 0 END) AS good_1pm,
						sum(CASE WHEN hour>13 and hour<=14 and a.production_type=2 THEN production_qnty else 0 END) AS good_2pm,
						sum(CASE WHEN hour>14 and hour<=15 and a.production_type=2 THEN production_qnty else 0 END) AS good_3pm,
						sum(CASE WHEN hour>15 and hour<=16 and a.production_type=2 THEN production_qnty else 0 END) AS good_4pm,
						sum(CASE WHEN hour>16 and hour<=17 and a.production_type=2 THEN production_qnty else 0 END) AS good_2pm,
						sum(CASE WHEN hour>17 and hour<=18 and a.production_type=2 THEN production_qnty else 0 END) AS good_6pm,
						sum(CASE WHEN hour>18 and hour<=19 and a.production_type=2 THEN production_qnty else 0 END) AS good_7pm,
						sum(CASE WHEN hour>19 and hour<=20 and a.production_type=2 THEN production_qnty else 0 END) AS good_8pm,
						sum(CASE WHEN hour>20 and hour<=21 and a.production_type=2 THEN production_qnty else 0 END) AS good_9pm,
						sum(CASE WHEN hour>21 and hour<=2 and a.production_type=2 THEN production_qnty else 0 END) AS good_10pm,
						sum(CASE WHEN hour>22 and hour<=23 and a.production_type=2 THEN production_qnty else 0 END) AS good_11pm,
						sum(CASE WHEN hour>23 and hour<=24 and a.production_type=2 THEN production_qnty else 0 END) AS good_12am
						from subcon_gmts_prod_dtls a,  subcon_ord_mst b, subcon_ord_dtls c
						where a.production_type=2 and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0                          $company_name $subcon_location $floor $subcon_line   $txt_date_from 
						 group by a.company_id, a.location_id, a.floor_id,a.order_id, a.production_date, a.line_id,b.party_id,c.order_no order by a.location_id, a.floor_id,a.line_id,a.order_id");		
	
	    $subcon_order_smv=array();		
		foreach($sql_sub_contuct as $subcon_val)
			{
				$production_po_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]][$subcon_val[csf('order_id')]]+=$subcon_val[csf('good_qnty')];
				//$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['prod_reso_allo']=$subcon_val[csf('prod_reso_allo')]; 
				if($production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['buyer_name']!="")
					{
						  $production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['buyer_name'].=",".$subcon_val[csf('buyer_name')]; 
					}
				else
					{
					   $production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['buyer_name']=$subcon_val[csf('buyer_name')]; 
					}
				
				if($production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['po_number']!="")
					{
						  $production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['po_number'].=",".$subcon_val[csf('po_number')]; 
					}
				else
					{
					   $production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['po_number']=$subcon_val[csf('po_number')]; 
					}
			
			if($production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['order_id']!="")
					{
						  $production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['order_id'].=",".$subcon_val[csf('order_id')]; 
					}
			else
					{
					   $production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['order_id']=$subcon_val[csf('order_id')]; 
					}
			
				
				$subcon_order_smv[$subcon_val[csf('order_id')]]=$subcon_val[csf('smv')];
				$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['quantity']+=$subcon_val[csf('good_qnty')];
				$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['1am']+=$subcon_val[csf('good_1am')];  
				$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['2am']+=$subcon_val[csf('good_2am')];
				$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['3am']+=$subcon_val[csf('good_3am')]; 
				$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['4am']+=$subcon_val[csf('good_4am')];
				$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['5am']+=$subcon_val[csf('good_5am')]; 
				$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['6am']+=$subcon_val[csf('good_6am')];
				$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['7am']+=$subcon_val[csf('good_7am')]; 
				$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['8am']+=$subcon_val[csf('good_8am')];
				$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['9am']+=$subcon_val[csf('good_9am')]; 
				$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['10am']+=$subcon_val[csf('good_10am')];
				$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['11am']+=$subcon_val[csf('good_11am')]; 
				$production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['12pm']+=$subcon_val[csf('good_12pm')];
				 
			   $production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['1pm']+=$subcon_val[csf('good_1pm')]; 
			   $production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['2pm']+=$subcon_val[csf('good_2pm')]; 
			   $production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['3pm']+=$subcon_val[csf('good_3pm')];
			   $production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['4pm']+=$subcon_val[csf('good_4pm')];
			   $production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['5pm']+=$subcon_val[csf('good_5pm')]; 
			   $production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['6pm']+=$subcon_val[csf('good_6pm')];
			   $production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['7pm']+=$subcon_val[csf('good_7pm')]; 
			   $production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['8pm']+=$subcon_val[csf('good_8pm')]; 
			   $production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['9pm']+=$subcon_val[csf('good_9pm')]; 
			   $production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['10pm']+=$subcon_val[csf('good_10pm')]; 
			   $production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['11pm']+=$subcon_val[csf('good_11pm')]; 
			   $production_data_arr[$subcon_val[csf('floor_id')]][$subcon_val[csf('line_id')]]['12am']+=$subcon_val[csf('good_12am')]; 
				
			}
			
		//print_r($production_data_arr);die;	
			
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
				
		
				
			    //$po_number=array_unique(explode(',',$row[csf('po_number')]));
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
			  $subcon_po_id=array_unique(explode(',',$ldata['order_id']));
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
		    	$produce_minit+=$production_po_data_arr[$f_id][$l_id][$sub_val]*$subcon_order_smv[$sub_val];

				 if($subcon_po_id!="") $subcon_po_id.=",";
				 $subcon_order_id.=$sub_val;
				 
			  }
	   	if($order_no_total!="")
			{
				
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
			}
		
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
				//echo ceil($prod_resource_array[$l_id][$pr_date]['working_hour']);die;
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
								 
								 
								 <td width="75" align="right"><a href="##" onclick="openmypage('.$cbo_company_id.",'".$order_no_total."','".$subcon_order_id."',".$f_id.",".$l_id.",'tot_prod','".$smv_for_item."',".$txt_date.')">'.$today_product.'</a>&nbsp;</td>
								 
								 
								
								 <td align="right" width="80">'. ($today_product-$eff_target).'&nbsp;</td>
							     <td align="right" width="100">'.$efficiency_min.'&nbsp;</td>
                                
								 <td width="100" align="right"><a href="##" onclick="openmypage('.$cbo_company_id.",'".$order_no_total."','".$subcon_order_id."',".$f_id.",".$l_id.",'tot_prod','".$smv_for_item."',".$txt_date.')">'.$produce_minit.'</a>&nbsp;</td>
								
								 
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
			       $new_smv=array();
                   $item_smv_pop=explode("****",$item_smv);
				    $order_id="";
				   foreach($item_smv_pop as $po_id_smv) 
				     {
						   $po_id_smv_pop=explode("**",$po_id_smv);
						   $new_smv[$po_id_smv_pop[0]]=$po_id_smv_pop[1];
					 }
					
			  
						
		   $sql_pop=sql_select("select  c.po_number,a.po_break_down_id,
						sum(a.production_quantity) as good_qnty 
						from pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c
						where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and a.company_id=".$company_id."  and a.floor_id=".$floor_id." and a.sewing_line=".$sewing_line."  and a.po_break_down_id in(".$po_id.") and a.production_date='".$prod_date."'  group by c.po_number,a.po_break_down_id order by  c.po_number ");
						
         $subcon_production_data_arr=array();
		 foreach($sql_pop as $pro_val)
				{
				  $subcon_production_data_arr[$pro_val[csf('po_break_down_id')]][$pro_val[csf('po_number')]]['po_number']=$pro_val[csf('po_number')];	
				  $subcon_production_data_arr[$pro_val[csf('po_break_down_id')]][$pro_val[csf('po_number')]]['po_qty']=$pro_val[csf('good_qnty')];	
                  $subcon_production_data_arr[$pro_val[csf('po_break_down_id')]][$pro_val[csf('po_number')]]['item_smv']=$new_smv[$pro_val[csf('po_break_down_id')]];	
					
				}
		if($subcon_order!="")
		{		
	   $sql_subcon=sql_select("select  
						       a.order_id,c.smv,
					           c.order_no as po_number,
						       sum(a.production_qnty) as good_qnty
						       from subcon_gmts_prod_dtls a, subcon_ord_dtls c
						       where a.production_type=2 and a.order_id=c.id  and a.status_active=1 and a.is_deleted=0  and a.company_id=".$company_id."  and a.floor_id=".$floor_id." and a.line_id=".$sewing_line."  and a.order_id in(".$subcon_order.") and a.production_date='".$prod_date."'                         	   group by a.order_id, c.order_no,c.smv");
		}
							   
		 foreach($sql_subcon as $sub_val)
				{
				  $subcon_production_data_arr[$sub_val[csf('order_id')]][$sub_val[csf('po_number')]]['po_number']=$sub_val[csf('po_number')];	
				  $subcon_production_data_arr[$sub_val[csf('order_id')]][$sub_val[csf('po_number')]]['po_qty']=$sub_val[csf('good_qnty')];	
                  $subcon_production_data_arr[$sub_val[csf('order_id')]][$sub_val[csf('po_number')]]['item_smv']=$sub_val[csf('smv')];	
					
				}		   
							   
					//print_r($subcon_production_data_arr);
                 
					
			$total_producd_min=0;
			$i=1; $total_qnty=0;
			foreach($subcon_production_data_arr as $sub_id=>$pop_val)
			{
			foreach($pop_val as $po_id=>$pop_val)
                    {
					
                       if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                       else
                            $bgcolor="#FFFFFF";	
                    
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="120" align="center"><? echo $pop_val['po_number']; ?></td>
                            <td align="right"><? echo $pop_val['item_smv']; ?>&nbsp;</td>
                            <td align="right"><? $total_po_qty+=$pop_val['po_qty']; echo $pop_val['po_qty']; ?>&nbsp;</td>
                            <td align="right">
							     <?
								   $producd_min=$pop_val['po_qty']*$pop_val['item_smv'];  $total_producd_min+=$producd_min;
								  echo $producd_min;
								  ?>&nbsp;</td>
                        </tr>
                    <?
                    $i++;
                    }
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