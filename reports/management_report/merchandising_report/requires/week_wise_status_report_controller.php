<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create week wise Status Report
				
Functionality	:	
JS Functions	:
Created by		:	Monzu 
Creation date 	: 	02/01/2015
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start();
include('../../../../includes/common.php');

extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');
$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
$company_short_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
$company_team_name_arr=return_library_array( "select id,team_name from lib_marketing_team",'id','team_name');
$company_team_member_name_arr=return_library_array( "select id,team_member_name from  lib_mkt_team_member_info",'id','team_member_name');
$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library",'master_tble_id','image_location');
$commission_for_shipment_schedule_arr=return_library_array( "select job_no,commission from  wo_pre_cost_dtls",'job_no','commission');
$country_name_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
if ($action=="load_drop_down_buyer")
{
echo create_drop_down( "cbo_buyer_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name  order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );   	 
} 

if ($action=="load_drop_down_team_member")
{
echo create_drop_down( "cbo_team_member", 172, "select id,team_member_name 	 from lib_mkt_team_member_info  where team_id='$data' and status_active=1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-Select Team Member-", $selected, "" );   	 
}
if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array; var selected_name = new Array;
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
			}
		}
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		function js_set_value( str ) {
			if (str!="") str=str.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			$('#hide_job_id').val( id );
			$('#hide_job_no').val( name );
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
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');">						<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                   		<input type="hidden" name="hide_job_no" id="hide_job_no" value="" /> 
                    </th> 
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'cpa_short_fabric_booking_analysis_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$month_id=$data[5];
	//echo $month_id;
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and buyer_name=$data[1]";
	}
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";
	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no";
	//$year="year(insert_date)";
	if($db_type==0) $year_field="YEAR(insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year";
	else $year_field="";
	if($db_type==0)
		{
			$year_field_con=" and SUBSTRING_INDEX(insert_date, '-', 1)";
			if($year_id!=0) $year_cond=" $year_field_con=$year_id"; else $year_cond="";	
		}
	else if($db_type==2)
		{
			$year_field_con=" and to_char(insert_date,'YYYY')";
			if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";	
		}
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_field from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond  order by job_no";
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','',1) ;
   exit(); 
} // Job Search end

if($type=="report_generate")
{
	$data=explode("_",$data);
	if($data[0]==0) $company_name="%%"; else $company_name=$data[0];
	if($data[1]==0) $buyer_name="%%"; else $buyer_name=$data[1];
	if(trim($data[2])!="") $start_date=$data[2];
	if(trim($data[3])!="") $end_date=$data[3];
	 $job_id=str_replace("'",'',$data[9]);
	// $cbo_year_selection=str_replace("'",'',$data[8]);
	
	$cbo_order_status2=$data[4];
	if($data[4]==2) $cbo_order_status="%%"; else $cbo_order_status= "$data[4]";
	if(trim($data[5])=="0") $team_leader="%%"; else $team_leader="$data[5]";
	if(trim($data[6])=="0") $dealing_marchant="%%"; else $dealing_marchant="$data[6]";
	if(trim($data[8])!="") $pocond="and b.id in(".str_replace("'",'',$data[8]).")"; else  $pocond="";
	
	if(trim($job_id)!="") $job_cond="and a.job_no_prefix_num in(".$job_id.")"; else  $job_cond="";
	//echo $job_cond.'gggg';die;
	if($db_type==0)
	{
	$start_date=change_date_format($start_date,'yyyy-mm-dd','-');
	$end_date=change_date_format($end_date,'yyyy-mm-dd','-');
	}
	if($db_type==2)
	{
	$start_date=change_date_format($start_date,'yyyy-mm-dd','-',1);
	$end_date=change_date_format($end_date,'yyyy-mm-dd','-',1);
	}
	
	$cbo_category_by=$data[7];
	$cbo_year_selection=$data[8];
	
	if($cbo_category_by==1)
	{
		if ($start_date!="" && $end_date!="")
		{
			$date_cond="and b.pub_shipment_date between '$start_date' and  '$end_date'";
		}
		else	
		{
			$date_cond="";
		}
	}
	else
	{
		if ($start_date!="" && $end_date!="")
		{
			$date_cond=" and b.po_received_date between '$start_date' and  '$end_date'";
		}
		else	
		{
			$date_cond="";
		}
	}
	
	
function week_of_year($year,$week_start_day)
{
$week_array=array();
$week=0;
for($i=1;$i<=12; $i++)
{
	$month=str_pad($i, 2, '0', STR_PAD_LEFT);
	$year=$year;
	$first_date_of_year=$year."-01-01";
	$first_day_of_year=date('l', strtotime($first_date_of_year));
	if($i==1)
	{
	if(date('l', strtotime($first_day_of_year))==$week_start_day)
	{
		$week=0;
	}
	else
	{
		$week=1;
	}
	}
	$days_in_month = cal_days_in_month(0, $month, $year) ;
	
    foreach (range(1, $days_in_month) as $day) 
	{
		$test_date = $year."-".$month."-" . str_pad($day, 2, '0', STR_PAD_LEFT);
		global $db_type;
		if($db_type==2)
		{
		$test_date=change_date_format($test_date,'dd-mm-yyyy','-',1);
		}
		
		if(date('l', strtotime($test_date))==$week_start_day)
		{
		  $week++;
		}
		$week_day=date('l', strtotime($test_date));
		$week_array[$test_date]=$week;
		
		
		/*$con = connect();//the connection have to be called out of function
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$id=return_next_id( "id", "week_of_year", 1 );
		$field_array="id, year, month, week, week_start_day, week_date,week_day";
		$data_array="(".$id.",".$year.",".$month.",".$week.",'".$week_start_day."','".$test_date."','".$week_day."')";
		$rID=sql_insert("week_of_year",$field_array,$data_array,0);
		if($db_type==0)
		{
			if($rID){
				mysql_query("COMMIT");  
			}
			else{
				mysql_query("ROLLBACK"); 
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID){
				oci_commit($con); 
			}
			else{
				oci_rollback($con); 
			}
		}*/
		
    }
}
return $week_array ;
}
$weekarr=week_of_year($cbo_year_selection,"Sunday");

$week_for_header=array();
$sql_week_header=sql_select("select week_date,week from week_of_year where week_date between '$start_date' and  '$end_date'");
foreach ($sql_week_header as $row_week_header)
{
	$week_for_header[$row_week_header[csf("week")]]=$row_week_header[csf("week")];
}
//echo "select week_date,week, min(week_date) as week_start_day,Max(week_date) as week_end_day from week_of_year where year=$cbo_year_selection group by week";
$week_start_day=array();
$week_end_day=array();
//echo $year_selection.'ddd';die;
 $sql_week_start_end_date=sql_select("select week, min(week_date) as week_start_day, Max(week_date) as week_end_day from week_of_year where year=$cbo_year_selection group by week");
foreach ($sql_week_start_end_date as $row_week_week_start_end_date)
{
	$week_start_day[$row_week_week_start_end_date[csf("week")]][week_start_day]=$row_week_week_start_end_date[csf("week_start_day")];
	$week_end_day[$row_week_week_start_end_date[csf("week")]][week_end_day]=$row_week_week_start_end_date[csf("week_end_day")];
}
$from_date=$week_start_day[min(array_keys($week_for_header))][week_start_day];
$to_date=$week_end_day[max(array_keys($week_for_header))][week_end_day];


if($cbo_category_by==1)
	{
		if ($from_date!="" && $to_date!="")
		{
			$date_cond="and c.country_ship_date between '$from_date' and  '$to_date'";
			$date_cond_target_basic="and b.date between '$from_date' and  '$to_date'";
		}
		else	
		{
			$date_cond="";
			$date_cond_target_basic="";
		}
	}
	else
	{
		if ($start_date!="" && $end_date!="")
		{
			$date_cond=" and c.country_ship_date between '$from_date' and  '$to_date'";
			$date_cond_target_basic="and b.date between '$from_date' and  '$to_date'";
		}
		else	
		{
			$date_cond="";
			$date_cond_target_basic="";
		}
	}
	
	



$week_order_qty=array();
$buyer_array=array();
$buyer_week_order_qty=array();

$exfactory_data_array=array();
$exfactory_data=sql_select("select po_break_down_id,country_id,MAX(ex_factory_date) as ex_factory_date,
sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty
 from pro_ex_factory_mst  where status_active=1 and is_deleted=0 group by po_break_down_id,country_id");
foreach($exfactory_data as $exfatory_row)
{
	$exfactory_data_array[$exfatory_row[csf('po_break_down_id')]][$exfatory_row[csf('country_id')]][ex_factory_qnty]=$exfatory_row[csf('ex_factory_qnty')];
}

$data_arr_cut=sql_select( "select po_break_down_id, country_id, sum(production_quantity) as production_quantity from pro_garments_production_mst where  production_type ='1' and status_active=1 and is_deleted=0 group by po_break_down_id, country_id");
foreach($data_arr_cut as $row_cut)
{
$cut_qty_arr[$row_cut[csf('po_break_down_id')]][$row_cut[csf('country_id')]][cutting_qnty]=$row_cut[csf('production_quantity')];
}

$sewing_qnty=sql_select("SELECT po_break_down_id,country_id,sum(production_quantity) AS production_quantity  from pro_garments_production_mst where production_type ='5' and is_deleted=0 and status_active=1 group by po_break_down_id,country_id ");
foreach($sewing_qnty as $row_sew)
{
$sew_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('country_id')]][sewing_qnty]=$row_sew[csf('production_quantity')];
}

if($db_type==0)
{
$data_array=sql_select("select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.style_description, a.job_quantity, a.product_category, a.job_no, a.location_name, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.product_code, b.id, b.is_confirmed, b.po_number, sum(c.order_quantity/a.total_set_qnty) as po_quantity, sum(c.order_quantity) as po_quantity_pcs, b.pub_shipment_date, b.po_received_date, DATEDIFF(c.country_ship_date, '$date') date_diff_1, DATEDIFF(c.country_ship_date, '$date') date_diff_2, b.unit_price, sum(c.order_total) as po_total_price, b.details_remarks, c.shiping_status,c.country_ship_date,c.country_id,  b.t_year, b.t_month  from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id   and a.company_name like '$company_name' and a.buyer_name like '$buyer_name' and a.team_leader like '$team_leader'  and a.dealing_marchant like '$dealing_marchant' $date_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $job_cond group by a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.style_description, a.job_quantity, a.product_category, a.job_no, a.location_name, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.product_code, b.id, b.is_confirmed, b.po_number,b.pub_shipment_date, b.po_received_date,b.unit_price,b.details_remarks,b.t_year, b.t_month,c.country_id,c.country_ship_date,c.shiping_status order by c.country_ship_date,a.job_no_prefix_num,b.id");
}
if($db_type==2)
{
$date=date('d-m-Y');
$data_array=sql_select("select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.style_description, a.job_quantity, a.product_category, a.job_no, a.location_name, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.product_code, b.id, b.is_confirmed, b.po_number, sum(c.order_quantity/a.total_set_qnty) as po_quantity, sum(c.order_quantity) as po_quantity_pcs, b.pub_shipment_date, b.po_received_date, (c.country_ship_date - to_date('$date','dd-mm-yyyy')) date_diff_1, (c.country_ship_date - to_date('$date','dd-mm-yyyy')) date_diff_2, b.unit_price, sum(c.order_total) as po_total_price, b.details_remarks, c.shiping_status,c.country_ship_date,c.country_id,  b.t_year, b.t_month  from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id   and a.company_name like '$company_name' and a.buyer_name like '$buyer_name' and a.team_leader like '$team_leader'  and a.dealing_marchant like '$dealing_marchant' $date_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $job_cond group by a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.style_description, a.job_quantity, a.product_category, a.job_no, a.location_name, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.product_code, b.id, b.is_confirmed, b.po_number,b.pub_shipment_date, b.po_received_date,b.unit_price,b.details_remarks,b.t_year, b.t_month,c.country_id,c.country_ship_date,c.shiping_status order by c.country_ship_date,a.job_no_prefix_num,b.id");
}

foreach ($data_array as $row)
{
	$week=$weekarr[$row[csf("country_ship_date")]];
	if($db_type==2){
		$week=$weekarr[change_date_format($row[csf("country_ship_date")],'dd-mm-yyyy','-',1)];	
	}
	if( date('l', strtotime($row[csf("country_ship_date")]))=='Sunday' && $week_pad==1){
		$week=$week+1;
	}
	
	$week_order_qty[$week][po_quantity]+=$row[csf("po_quantity_pcs")];
	$week_order_qty[$week][po_total_price]+=$row[csf("po_total_price")];
	$week_order_qty[$week][ship_quantity]+=$exfactory_data_array[$row[csf('id')]][$row[csf('country_id')]][ex_factory_qnty];
	$week_order_qty[$week][cutting_quantity]+=$cut_qty_arr[$row[csf('id')]][$row[csf('country_id')]][cutting_qnty];
	$week_order_qty[$week][sewing_quantity]+=$sew_qty_arr[$row[csf('id')]][$row[csf('country_id')]][sewing_qnty];
	
	$buyer_array[$row[csf("buyer_name")]]=$row[csf("buyer_name")];
	$buyer_week_order_qty[$row[csf("buyer_name")]][$week][po_quantity]+=$row[csf("po_quantity_pcs")];
	$buyer_week_order_qty[$row[csf("buyer_name")]][$week][po_total_price]+=$row[csf("po_total_price")];
	$buyer_week_order_qty[$row[csf("buyer_name")]][$week][ship_quantity]+=$exfactory_data_array[$row[csf('id')]][$row[csf('country_id')]][ex_factory_qnty];
	$buyer_week_order_qty[$row[csf("buyer_name")]][$week][cutting_quantity]+=$cut_qty_arr[$row[csf('id')]][$row[csf('country_id')]][cutting_qnty];
	$buyer_week_order_qty[$row[csf("buyer_name")]][$week][sewing_quantity]+=$sew_qty_arr[$row[csf('id')]][$row[csf('country_id')]][sewing_qnty];
}
ob_start();
$tb_width= count($week_for_header)*100+(145+75)."px";
?>
<div style="width:3000px" align="left">
		<fieldset style="width:100%; text-align:left">
    <table cellspacing="0" width="<? echo $tb_width; ?>"  border="1" rules="all" class="rpt_table" >
        <thead align="center">
            <tr>
            <th width="145" align="center">Particulars</th>
            <?
            foreach($week_for_header as $week_key => $week_value)
            {
            ?>
            <th width="100"align="center">
            Week-
            <? 
            echo $week_key."<br/>".change_date_format($week_start_day[$week_key][week_start_day],"dd-mm-yyyy","-")." To ".change_date_format($week_end_day[$week_key][week_end_day],"dd-mm-yyyy","-");
            ?>
            </th>
            <?
            }
            ?>
            <th width="75" align="center">Total Qty</th>
            </tr>
           
        </thead>
        <tbody>
            
            <tr>
            <td width="145" align="center">Order Qty</td>
            <?
			$tot_or_qty=0;
			$tot_ord_value=0;
            foreach($week_for_header as $week_key => $week_value)
            {
            ?>
            <td width="100"  align="right"><? echo number_format($week_order_qty[$week_key][po_quantity],0); $tot_or_qty+=$week_order_qty[$week_key][po_quantity];?></td>
            
            <?
            }
            ?>
            <td width="75" align="right"><? echo number_format($tot_or_qty,0); ?></td>
            </tr>
            <tr>
            <td width="145" align="center">Ship Qty</td>
            <?
			$tot_ship_qty=0;
			$tot_ship_value=0;
            foreach($week_for_header as $week_key => $week_value)
            {
            ?>
            <td width="100"  align="right"><? echo number_format($week_order_qty[$week_key][ship_quantity],0); $tot_ship_qty+=$week_order_qty[$week_key][ship_quantity];?></td>
           
            
            <?
            }
            ?>
            <td width="75" align="right"><? echo number_format($tot_ship_qty,0); ?></td>
            
            </tr>
            
            <tr>
            <td width="145" align="center">Ship Balance</td>
            <?
			$tot_ship_balance_qty=0;
			$tot_ship_balance_value=0;
            foreach($week_for_header as $week_key => $week_value)
            {
            ?>
            <td width="100"  align="right">
            <? 
            $ship_balance_qty=$week_order_qty[$week_key][po_quantity]-$week_order_qty[$week_key][ship_quantity];
            echo number_format($ship_balance_qty,0);
			$tot_ship_balance_qty+=$ship_balance_qty;
            ?>
            </td>
           
            
            <?
            }
            ?>
            <td width="75" align="right" ><? echo number_format($tot_ship_balance_qty,0); ?></td>
            </tr>
            
            <tr>
            <td width="145" align="center">Ship Balance %</td>
            <?
            foreach($week_for_header as $week_key => $week_value)
            {
            ?>
            <td width="100"  align="right">
            <? 
            $ship_balance_qty_per=(($week_order_qty[$week_key][po_quantity]-$week_order_qty[$week_key][ship_quantity])/$week_order_qty[$week_key][po_quantity])*100;
            echo number_format($ship_balance_qty_per,2);
            ?>
            </td>
            <?
            }
            ?>
            <td width="75" align="right">
            <?
			 $tot_ship_balance_qty_per=(($tot_or_qty-$tot_ship_qty)/$tot_or_qty)*100;
            echo number_format($tot_ship_balance_qty_per,2);
			?>
            </td>
            </tr>
            
            
          
            <tr>
            <td width="145" align="center">Cutting Qty</td>
            <?
			$tot_cut_qty=0;
            foreach($week_for_header as $week_key => $week_value)
            {
            ?>
            <td width="100"  align="right"><? echo number_format($week_order_qty[$week_key][cutting_quantity],0); $tot_cut_qty+=$week_order_qty[$week_key][cutting_quantity];?></td>
           
            
            <?
            }
            ?>
            <td width="75" align="right"><? echo number_format($tot_cut_qty,0); ?></td>
            
            </tr>
            
            <tr>
            <td width="145" align="center">Cutting Balance</td>
            <?
			$tot_cut_balance_qty=0;
            foreach($week_for_header as $week_key => $week_value)
            {
            ?>
            <td width="100"  align="right">
            <? 
            $cut_balance_qty=$week_order_qty[$week_key][po_quantity]-$week_order_qty[$week_key][cutting_quantity];
            echo number_format($cut_balance_qty,0);
			$tot_cut_balance_qty+=$cut_balance_qty;
            ?>
            </td>
           
            
            <?
            }
            ?>
            <td width="75" align="right" ><? echo number_format($tot_cut_balance_qty,0); ?></td>
            </tr>
            
            <tr>
            <td width="145" align="center">Cutting Balance %</td>
            <?
            foreach($week_for_header as $week_key => $week_value)
            {
            ?>
            <td width="100"  align="right">
            <? 
            $cut_balance_qty_per=(($week_order_qty[$week_key][po_quantity]-$week_order_qty[$week_key][cutting_quantity])/$week_order_qty[$week_key][po_quantity])*100;
            echo number_format($cut_balance_qty_per,2);
            ?>
            </td>
            
            
            <?
            }
            ?>
            <td width="75" align="right">
			<?
			$tot_cut_balance_qty_per=(($tot_or_qty-$tot_cut_qty)/$tot_or_qty)*100;
            echo number_format($tot_cut_balance_qty_per,2);
			?>
            </td>
            </tr>
            
             <tr>
            <td width="145" align="center">Sewing Qty</td>
            <?
			$tot_sew_qty=0;
            foreach($week_for_header as $week_key => $week_value)
            {
            ?>
            <td width="100"  align="right"><? echo number_format($week_order_qty[$week_key][sewing_quantity],0); $tot_sew_qty+=$week_order_qty[$week_key][sewing_quantity];?></td>
           
            
            <?
            }
            ?>
            <td width="75" align="right"><? echo number_format($tot_sew_qty,0); ?></td>
            
            </tr>
            
            <tr>
            <td width="145" align="center">Sewing Balance</td>
            <?
			$tot_sew_balance_qty=0;
            foreach($week_for_header as $week_key => $week_value)
            {
            ?>
            <td width="100"  align="right">
            <? 
            $sew_balance_qty=$week_order_qty[$week_key][po_quantity]-$week_order_qty[$week_key][sewing_quantity];
            echo number_format($sew_balance_qty,0);
			$tot_sew_balance_qty+=$sew_balance_qty;
            ?>
            </td>
           
            
            <?
            }
            ?>
            <td width="75" align="right" ><? echo number_format($tot_sew_balance_qty,0); ?></td>
            </tr>
            
            <tr>
            <td width="145" align="center">Sewing Balance %</td>
            <?
            foreach($week_for_header as $week_key => $week_value)
            {
            ?>
            <td width="100"  align="right">
            <? 
            $sew_balance_qty_per=(($week_order_qty[$week_key][po_quantity]-$week_order_qty[$week_key][sewing_quantity])/$week_order_qty[$week_key][po_quantity])*100;
            echo number_format($sew_balance_qty_per,2);
            ?>
            </td>
            
            
            <?
            }
            ?>
            <td width="75" align="right">
			<? 
			
			$tot_sew_balance_qty_per=(($tot_or_qty-$tot_sew_qty)/$tot_or_qty)*100;
            echo number_format($tot_sew_balance_qty_per,2);
			?>
            </td>
            </tr>
        </tbody>
       <tfoot>
       </tfoot>
    </table>
  
    <br/>
    
    <table cellspacing="0" width="<? echo $tb_width; ?>"  border="1" rules="all" class="rpt_table" >
        <thead align="center">
            <tr>
            <th width="75"  align="center">Buyer Name</th>
            <th width="70" align="center">Purticulars</th>
            <?
            foreach($week_for_header as $week_key => $week_value)
            {
            ?>
            <th width="100"  align="center">
            Week-
            <? 
            echo $week_key."<br/>".change_date_format($week_start_day[$week_key][week_start_day],"dd-mm-yyyy","-")." To ".change_date_format($week_end_day[$week_key][week_end_day],"dd-mm-yyyy","-");
            ?>
            </th>
            <?
            }
            ?>
            <th width="75" align="center">Total Qty</th>
            </tr>
        </thead>
        <tbody>
        <?
		$bu=0;
		foreach ($buyer_array as $buyer_key => $buyer_value)
		{
			if ($bu%2==0)  
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";
		?>
        
            <tr bgcolor="<? echo $bgcolor ?>">
            <td width="75" rowspan="10" align="center"><? echo $buyer_short_name_arr[$buyer_value];?></td>
            <td width="70" align="center">Order Qty</td>
            <?
			$tot_buy_or_qty=0;
            foreach($week_for_header as $week_key => $week_value)
            {
            ?>
            <td width="100"  align="right"><? echo number_format($buyer_week_order_qty[$buyer_value][$week_key][po_quantity],0); $tot_buy_or_qty+=$buyer_week_order_qty[$buyer_value][$week_key][po_quantity];?>&nbsp;</td>
            
            <?
            }
            ?>
            <td width="75" align="right"><? echo number_format($tot_buy_or_qty,0);?></td>
            </tr>
            <tr bgcolor="<? echo $bgcolor ?>">
            <td width="70" align="center">Ship Qty</td>
            <?
			$tot_buy_ship_qty=0;
            foreach($week_for_header as $week_key => $week_value)
            {
            ?>
            <td width="100"  align="right"><? echo number_format($buyer_week_order_qty[$buyer_value][$week_key][ship_quantity],0); $tot_buy_ship_qty+=$buyer_week_order_qty[$buyer_value][$week_key][ship_quantity];?>&nbsp;</td>
           
            <?
            }
            ?>
            <td width="75" align="right"><? echo number_format($tot_buy_ship_qty,0);?></td>
            </tr>
            
            <tr bgcolor="<? echo $bgcolor ?>">
            <td width="70" align="center">Ship Balance </td>
            <?
			$tot_buy_ship_ba_qty=0;
            foreach($week_for_header as $week_key => $week_value)
            {
            ?>
            <td width="100"  align="right"><? echo number_format($buyer_week_order_qty[$buyer_value][$week_key][po_quantity]-$buyer_week_order_qty[$buyer_value][$week_key][ship_quantity],0); $tot_buy_ship_ba_qty+=$buyer_week_order_qty[$buyer_value][$week_key][po_quantity]-$buyer_week_order_qty[$buyer_value][$week_key][ship_quantity];?>&nbsp;</td>
            <?
            }
            ?>
            <td width="75" align="right"><? echo number_format($tot_buy_ship_ba_qty,0);?></td>
            </tr>
             <tr bgcolor="<? echo $bgcolor ?>">
            <td width="70" align="center">Ship Balance %</td>
            <?
            foreach($week_for_header as $week_key => $week_value)
            {
            ?>
            <td width="100"  align="right"><? echo number_format((($buyer_week_order_qty[$buyer_value][$week_key][po_quantity]-$buyer_week_order_qty[$buyer_value][$week_key][ship_quantity])/$buyer_week_order_qty[$buyer_value][$week_key][po_quantity])*100,2);?>&nbsp;</td>
            <?
            }
            ?>
            <td width="75" align="right">
			<? 
			$tot_buy_ship_balance_qty_per=(($tot_buy_or_qty-$tot_buy_ship_qty)/$tot_buy_or_qty)*100;
            echo number_format($tot_buy_ship_balance_qty_per,2);
			?>
            </td>
            </tr>
            
            
            
             <tr bgcolor="<? echo $bgcolor ?>">
            <td width="70" align="center">Cutting Qty</td>
            <?
			$tot_buy_cut_qty=0;
            foreach($week_for_header as $week_key => $week_value)
            {
            ?>
            <td width="100"  align="right"><? echo number_format($buyer_week_order_qty[$buyer_value][$week_key][cutting_quantity],0); $tot_buy_cut_qty+=$buyer_week_order_qty[$buyer_value][$week_key][cutting_quantity];?>&nbsp;</td>
           
            <?
            }
            ?>
            <td width="75" align="right"><? echo number_format($tot_buy_cut_qty,0);?></td>
            </tr>
            
            <tr bgcolor="<? echo $bgcolor ?>">
            <td width="70" align="center">Cutting Balance </td>
            <?
			$tot_buy_cut_ba_qty=0;
            foreach($week_for_header as $week_key => $week_value)
            {
            ?>
            <td width="100"  align="right"><? echo number_format($buyer_week_order_qty[$buyer_value][$week_key][po_quantity]-$buyer_week_order_qty[$buyer_value][$week_key][cutting_quantity],0); $tot_buy_cut_ba_qty+=$buyer_week_order_qty[$buyer_value][$week_key][po_quantity]-$buyer_week_order_qty[$buyer_value][$week_key][cutting_quantity];?>&nbsp;</td>
            <?
            }
            ?>
            <td width="75" align="right"><? echo number_format($tot_buy_cut_ba_qty,0);?></td>
            </tr>
             <tr bgcolor="<? echo $bgcolor ?>">
            <td width="70" align="center">Cutting Balance %</td>
            <?
            foreach($week_for_header as $week_key => $week_value)
            {
            ?>
            <td width="100"  align="right"><? echo number_format((($buyer_week_order_qty[$buyer_value][$week_key][po_quantity]-$buyer_week_order_qty[$buyer_value][$week_key][cutting_quantity])/$buyer_week_order_qty[$buyer_value][$week_key][po_quantity])*100,2);?>&nbsp;</td>
            <?
            }
            ?>
            <td width="75" align="right">
			<? 
			$tot_buy_cut_balance_qty_per=(($tot_buy_or_qty-$tot_buy_cut_qty)/$tot_buy_or_qty)*100;
            echo number_format($tot_buy_cut_balance_qty_per,2);
			?>
            </td>
            </tr>
            
            <tr bgcolor="<? echo $bgcolor ?>">
            <td width="70" align="center">Sewing Qty</td>
            <?
			$tot_buy_sew_qty=0;
            foreach($week_for_header as $week_key => $week_value)
            {
            ?>
            <td width="100"  align="right"><? echo number_format($buyer_week_order_qty[$buyer_value][$week_key][sewing_quantity],0); $tot_buy_sew_qty+=$buyer_week_order_qty[$buyer_value][$week_key][sewing_quantity];?>&nbsp;</td>
           
            <?
            }
            ?>
            <td width="75" align="right"><? echo number_format($tot_buy_sew_qty,0);?></td>
            </tr>
            
            <tr bgcolor="<? echo $bgcolor ?>">
            <td width="70" align="center">Sewing Balance </td>
            <?
			$tot_buy_sew_ba_qty=0;
            foreach($week_for_header as $week_key => $week_value)
            {
            ?>
            <td width="100"  align="right"><? echo number_format($buyer_week_order_qty[$buyer_value][$week_key][po_quantity]-$buyer_week_order_qty[$buyer_value][$week_key][sewing_quantity],0); $tot_buy_sew_ba_qty+=$buyer_week_order_qty[$buyer_value][$week_key][po_quantity]-$buyer_week_order_qty[$buyer_value][$week_key][sewing_quantity];?>&nbsp;</td>
            <?
            }
            ?>
            <td width="75" align="right"><? echo number_format($tot_buy_sew_ba_qty,0);?></td>
            </tr>
             <tr bgcolor="<? echo $bgcolor ?>">
            <td width="70" align="center">Sewing Balance %</td>
            <?
            foreach($week_for_header as $week_key => $week_value)
            {
            ?>
            <td width="100"  align="right"><? echo number_format((($buyer_week_order_qty[$buyer_value][$week_key][po_quantity]-$buyer_week_order_qty[$buyer_value][$week_key][sewing_quantity])/$buyer_week_order_qty[$buyer_value][$week_key][po_quantity])*100,2);?>&nbsp;</td>
            <?
            }
            ?>
            <td width="75" align="right">
			<? 
			$tot_buy_sew_balance_qty_per=(($tot_buy_or_qty-$tot_buy_sew_qty)/$tot_buy_or_qty)*100;
            echo number_format($tot_buy_sew_balance_qty_per,2);
			?>
            </td>
            </tr>
            
            <?
			$bu++;
		}
			?>
        </tbody>
        <tfoot>
        </tfoot>
    </table>
   <br/>
  <table>
<tr>
<td bgcolor="orange" height="15" width="30"></td>
<td>Maximum 10 Days Remaing To Ship</td>
<td bgcolor="green" height="15" width="30">&nbsp;</td>
<td>On Time Shipment</td>
<td bgcolor="#2A9FFF" height="15" width="30"></td>
<td>Delay shipment</td>
<td bgcolor="red" height="15" width="30"></td>
<td>Shipment Date Over & Pending</td>


</tr>
</table>

<h3 align="left" id="accordion_h4" class="accordion_h" onClick="accordion_menu( this.id,'content_report_panel', '')"> -Report Panel </h3>
            <div id="content_report_panel"> 
                <table width="3330" id="table_header_1" border="1" class="rpt_table" rules="all">
                    <thead>
                        <tr>
                            <th width="50">SL</th>
                            <th width="65" >Company</th>
                            <th width="60">Job No</th>
                            <th  width="50">Buyer</th>
                            <th  width="150">Order No</th>
                            <th  width="100">Pord. Dept Code</th>
                            <th width="30">Img</th>
                            <th width="150">Item</th>
                            <th width="90">Style Ref</th>
                            <th width="150">Style Des</th>
                            <th width="100">Country</th>
                            <th width="80">Ship Date</th>
                            
                            <th width="90">Order Qnty</th>
                            <th width="30">Uom</th>
                            <th width="90">Order Qnty(Pcs)</th>
                            <th  width="50">Per Unit Price</th>
                            <th width="100">Order Value</th>
                            <th width="100">Commission</th>
                            <th width="100">Net Order Value</th>
                            <th width="90">Ex-Fac Qnty (Pcs) </th>
                            <th  width="90">Ex-factory Bal. (Pcs)</th>
                            <th  width="90">Ex-factory Over (Pcs)</th>
                            <th width="120">Ex-factory Bal. Value</th>
                            <th width="120">Ex-factory Over. Value</th>
                            <th width="60">Order Status</th>
                            <th width="70">Prod. Catg</th>
                            <th width="80">PO Rec. Date</th>
                            <th  width="50">Days in Hand</th>
                            <th width="100" >Shipping Status</th>
                            <th width="150"> Team Member</th>
                            <th width="150">Team Name</th>
                            <th width="30">Id</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                </table>
                <div style=" max-height:400px; overflow-y:scroll; width:3330px"  align="left" id="scroll_body">
                    <table width="3310" border="1" class="rpt_table" rules="all" id="table-body">
                    <?
                    $i=1;
                    $order_qnty_pcs_tot=0;
                    $order_qntytot=0;
                    $oreder_value_tot=0;
                    $total_ex_factory_qnty=0;
                    $total_short_access_qnty=0;
                    $total_short_access_value=0;
                    $yarn_req_for_po_total=0;
                    foreach ($data_array as $row)
                    { 
						if ($i%2==0)  
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";	
						
						if($row[csf('is_confirmed')]==2)
						{
							$color_font="#F00";
						}
						else
						{
							$color_font="#000";
						}
						$ex_factory_date=$exfactory_data_array[$row[csf('id')]][$row[csf('country_id')]][ex_factory_date];//It has no array assign
						$date_diff_3=datediff( "d", $ex_factory_date , $row[csf('country_ship_date')]);
						$date_diff_4=datediff( "d", $ex_factory_date , $row[csf('country_ship_date')]);

						
						$cons=0;
						$costing_per_pcs=0;
						$data_array_yarn_cons=sql_select("select yarn_cons_qnty from  wo_pre_cost_sum_dtls where  job_no='".$row[csf('job_no')]."'");
						$data_array_costing_per=sql_select("select costing_per from  wo_pre_cost_mst where  job_no='".$row[csf('job_no')]."'");
						list($costing_per)=$data_array_costing_per;
						if($costing_per[csf('costing_per')]==1)
						{
						  $costing_per_pcs=1*12;	
						}
						else if($costing_per[csf('costing_per')]==2)
						{
						 $costing_per_pcs=1*1;	
						}
						else if($costing_per[csf('costing_per')]==3)
						{
						 $costing_per_pcs=2*12;	
						}
						else if($costing_per[csf('costing_per')]==4)
						{
						 $costing_per_pcs=3*12;	
						}
						else if($costing_per[csf('costing_per')]==5)
						{
						 $costing_per_pcs=4*12;	
						}
						
						$yarn_req_for_po=0;
						foreach($data_array_yarn_cons as $row_yarn_cons)
						{
							$cons=$row_yarn_cons[csf('yarn_cons_qnty')];
							$yarn_req_for_po=($row_yarn_cons[csf('yarn_cons_qnty')]/ $costing_per_pcs)*$row[csf('po_quantity')];
						}
						
						//--Calculation Yarn Required-------
						//--Color Determination-------------
						//==================================
						$shipment_performance=0;
						if($row[csf('shiping_status')]==1 && $row[csf('date_diff_1')]>10 )
						{
						$color="";	
						$number_of_order['yet']+=1;
						$shipment_performance=0;
						}
						if($row[csf('shiping_status')]==1 && ($row[csf('date_diff_1')]<=10 && $row[csf('date_diff_1')]>=0))
						{
						$color="orange";
						$number_of_order['yet']+=1;
						$shipment_performance=0;
						}
						if($row[csf('shiping_status')]==1 &&  $row[csf('date_diff_1')]<0)
						{
						$color="red";	
						$number_of_order['yet']+=1;
						$shipment_performance=0;
						}
						//=====================================
						if($row[csf('shiping_status')]==2 && $row[csf('date_diff_1')]>10 )
						{
						$color="";	
						}
						if($row[csf('shiping_status')]==2 && ($row[csf('date_diff_1')]<=10 && $row[csf('date_diff_1')]>=0))
						{
						$color="orange";	
						}
						if($row[csf('shiping_status')]==2 &&  $row[csf('date_diff_1')]<0)
						{
						$color="red";	
						}
						if($row[csf('shiping_status')]==2 &&  $row[csf('date_diff_2')]>=0)
						{
						$number_of_order['ontime']+=1;
						$shipment_performance=1;	
						}
						if($row[csf('shiping_status')]==2 &&  $row[csf('date_diff_2')]<0)
						{
						$number_of_order['after']+=1;
						$shipment_performance=2;	
						}
						//========================================
						if($row[csf('shiping_status')]==3 && $date_diff_3 >=0 )
						{
						$color="green";	
						}
						if($row[csf('shiping_status')]==3 &&  $date_diff_3<0)
						{
						$color="#2A9FFF";	
						}
						if($row[csf('shiping_status')]==3 && $date_diff_4>=0 )
						{
						$number_of_order['ontime']+=1;
						$shipment_performance=1;
						}
						if($row[csf('shiping_status')]==3 && $date_diff_4<0)
						{
						$number_of_order['after']+=1;
						$shipment_performance=2;	
						}
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle" height="25" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" >
								<td width="50" align="center" bgcolor="<? echo $color; ?>"> <? echo $i; ?> </td>
								<td width="65" align="center"><? echo $company_short_name_arr[$row[csf('company_name')]];?></td>
                                <td width="60" align="center"><? echo $row[csf('job_no_prefix_num')];?></td>
								<td  width="50" align="center"><? echo $buyer_short_name_arr[$row[csf('buyer_name')]];?></td>
                                <td  width="150" align="center"><font style="color:<? echo $color_font; ?>"><? echo $row[csf('po_number')];?></font></td>
                                 <td  width="100" align="center"><font style="color:<? echo $color_font; ?>"><? echo $row[csf('product_code')];?></font></td>
                                <td width="30" onClick="openmypage_image('requires/capacity_and_order_booking_status_controller.php?action=show_image&job_no=<? echo $row[csf("job_no")] ?>','Image View')"><img  src='../../../<? echo $imge_arr[$row[csf('job_no')]]; ?>' height='25' width='30' /></td>
                                <td width="150" align="center">
								<?
								$gmts_item_name="";
								$gmts_item_id=explode(',',$row[csf('gmts_item_id')]);
								for($j=0; $j<count($gmts_item_id); $j++)
								{
								$gmts_item_name.= $garments_item[$gmts_item_id[$j]].",";
								}
								?>
                                <p> <? echo rtrim($gmts_item_name,","); ?> </p>
								</td>
                                <td width="90" align="center"><p><? echo $row[csf('style_ref_no')];?></p></td>
                                <td width="150" align="center"><p><? echo $row[csf('style_description')];?></p></td>
                                <td width="100" align="center"><p><? echo $country_name_arr[$row[csf('country_id')]];?></p></td>
                                <td width="80" align="center"><? echo change_date_format($row[csf('country_ship_date')],'dd-mm-yyyy','-');?></td>
								<td width="90" align="right">
								<? 
								echo number_format( $row[csf('po_quantity')],0);
								$order_qntytot=$order_qntytot+$row[csf('po_quantity')];
								$gorder_qntytot=$gorder_qntytot+$row[csf('po_quantity')];
								?>
								</td>
								<td width="30" align="center"><? echo $unit_of_measurement[$row[csf('order_uom')]];?></td>
								<td width="90" align="right">
								<? 
								echo number_format(($row[csf('po_quantity')]*$row[csf('total_set_qnty')]),0);  
								$order_qnty_pcs_tot=$order_qnty_pcs_tot+($row[csf('po_quantity')]*$row[csf('total_set_qnty')]);
								$gorder_qnty_pcs_tot=$gorder_qnty_pcs_tot+($row[csf('po_quantity')]*$row[csf('total_set_qnty')]);
								?>
                                </td>
								
								<td  width="50" align="right"><? echo number_format($row[csf('unit_price')],2);?></td>
								<td width="100" align="right">
								<? 
								echo number_format($row[csf('po_total_price')],2);
								$oreder_value_tot=$oreder_value_tot+$row[csf('po_total_price')];
								$goreder_value_tot=$goreder_value_tot+$row[csf('po_total_price')];
								?>
                                </td>
								<td width="100"  align="right"><? $commission=($row[csf('po_quantity')]/$costing_per_pcs)*$commission_for_shipment_schedule_arr[$row[csf('job_no')]]; $commission_tot+=$commission; echo number_format($commission,2); ?></td>
                                <td width="100" align="right"><? $net_order_value=$row[csf('po_total_price')]-$commission;$net_order_value_tot+=$net_order_value; echo number_format ($net_order_value,2); ?></td>
								<td width="90" align="right">
								<? 
								$ex_factory_qnty=$exfactory_data_array[$row[csf("id")]][$row[csf("country_id")]][ex_factory_qnty]; 
								
								?>
                                <a href="##" onClick="generate_ex_factory_popup('ex_factory_popup','<? echo $row[csf('country_id')];?>','<? echo $row[csf("id")]; ?>','550px')"><? echo  number_format($ex_factory_qnty,0); ?></a>
                                <?
								//echo  number_format( $ex_factory_qnty,0); 
								$total_ex_factory_qnty=$total_ex_factory_qnty+$ex_factory_qnty ;
								$gtotal_ex_factory_qnty=$gtotal_ex_factory_qnty+$ex_factory_qnty ;;
								if ($shipment_performance==0)
								{
								$po_qnty['yet']+=($row[csf('po_quantity')]*$row[csf('total_set_qnty')]);
								$po_value['yet']+=100;
								}
								else if ($shipment_performance==1)
								{
								$po_qnty['ontime']+=$ex_factory_qnty;
								$po_value['ontime']+=((100*$ex_factory_qnty)/($row[csf('po_quantity')]*$row[csf('total_set_qnty')]));
								$po_qnty['yet']+=(($row[csf('po_quantity')]*$row[csf('total_set_qnty')])-$ex_factory_qnty);
								}
								else if ($shipment_performance==2)
								{
								$po_qnty['after']+=$ex_factory_qnty;
								$po_value['after']+=((100*$ex_factory_qnty)/($row[csf('po_quantity')]*$row[csf('total_set_qnty')]));
								$po_qnty['yet']+=(($row[csf('po_quantity')]*$row[csf('total_set_qnty')])-$ex_factory_qnty);
								}
								?> 
								</td>
								<td  width="90" align="right">
								<? 
								$short_access_qnty=(($row[csf('po_quantity')]*$row[csf('total_set_qnty')])-$ex_factory_qnty); 
								if($short_access_qnty>=0){
								echo number_format($short_access_qnty,0);
								$total_short_access_qnty=$total_short_access_qnty+$short_access_qnty;
								$gtotal_short_access_qnty=$gtotal_short_access_qnty+$short_access_qnty;
								}
								?>
                                </td>
                                <td  width="90" align="right">
								<? 
								//$short_access_qnty=(($row['po_quantity']*$row['total_set_qnty'])-$ex_factory_qnty); 
								if($short_access_qnty<0){
								echo number_format(ltrim($short_access_qnty,'-'),0);
								$total_over_access_qnty=$total_over_access_qnty+$short_access_qnty;
								//$gtotal_short_access_qnty=$gtotal_short_access_qnty+$short_access_qnty;
								}
								?>
                                </td>
								<td width="120" align="right">
								<? 
								if($short_access_qnty>=0){
								$short_access_value=$short_access_qnty*$row[csf('unit_price')];
								echo  number_format($short_access_value,2);
								$total_short_access_value=$total_short_access_value+$short_access_value;
								$gtotal_short_access_value=$gtotal_short_access_value+$short_access_value;
								}
								?>
                                </td>
                                <td width="120" align="right">
								<? 
								if($short_access_qnty<0){
								$short_over_value=$short_access_qnty*$row[csf('unit_price')];
								echo  number_format(ltrim($short_over_value,'-'),2);
								$total_over_access_value=$total_over_access_value+$short_over_value;
								//$gtotal_short_access_value=$gtotal_short_access_value+$short_access_value;
								}
								?>
                                </td>
                                <td width="60" align="center"><? echo  $order_status[$row[csf('is_confirmed')]];?></td>
								<td width="70" align="center"><? echo $product_category[$row[csf('product_category')]];?></td>
                                <td width="80" align="center"><? echo change_date_format($row[csf('po_received_date')],'dd-mm-yyyy','-');?></td>
								<td  width="50" align="center" bgcolor="<? echo $color; ?>"> 
								<?
								if($row[csf('shiping_status')]==1 || $row[csf('shiping_status')]==2)
								{
								echo $row[csf('date_diff_1')];
								}
								if($row[csf('shiping_status')]==3)
								{
								echo $date_diff_3;
								}
								?>
								</td>
								<td width="100" align="center"><? echo $shipment_status[$row[csf('shiping_status')]]; ?></td>
								<td width="150" align="center"><? echo $company_team_member_name_arr[$row[csf('dealing_marchant')]];?></td>
								<td width="150" align="center"><? echo $company_team_name_arr[$row[csf('team_leader')]];?></td>
								<td width="30"><? echo $row[csf('id')]; ?></td>
								<td><? echo $row[csf('details_remarks')]; ?></td>
							</tr>
                    <?
                    $i++;
					}
                    ?>
                    </table>
                </div>
                <table width="3330" id="report_table_footer" border="1" class="rpt_table" rules="all">
                    <tfoot>
                        <tr>
                            <th width="50"></th>
                            <th width="65" ></th>
                            <th width="60"></th>
                            <th  width="50"></th>
                            <th  width="150"></th>
                            <th  width="100"></th>
                            <th width="30"></th>
                            <th width="150"></th>
                            <th width="90"></th>
                            <th width="150"></th>
                            <th width="100"></th>
                            <th width="80"></th>
                           <th width="90" id="total_order_qnty"><? echo number_format($order_qntytot,0); ?></th>
                            <th width="30"></th>
                            
                            <th width="90" id="total_order_qnty_pcs"><? echo number_format($order_qnty_pcs_tot,0); ?></th>
                           
                             <th  width="50"></th>
                             <th width="100" id="value_total_order_value"><? echo number_format($oreder_value_tot,2); ?></th>
                            <th width="100" id="value_total_commission"><? echo number_format($commission_tot,2); ?></th>
                            <th width="100" id="value_total_net_order_value"><? echo number_format($net_order_value_tot,2); ?></th>
                            <th width="90" id="value_total_ex_factory_qnty"> <? echo number_format($total_ex_factory_qnty,0); ?></th>
                            <th  width="90" id="total_short_access_qnty"><? echo number_format($total_short_access_qnty,0); ?></th>
                             <th  width="90" id="total_over_access_qnty"><? echo number_format(ltrim($total_over_access_qnty,'-'),0); ?></th>
                            <th width="120" id="value_total_short_access_value"><? echo number_format($total_short_access_value,2); ?></th>
                            <th width="120" id="value_total_over_access_value"><? echo number_format(ltrim($total_over_access_value,'-'),2); ?></th>
                            <th width="60"></th>
                            <th width="70"></th>
                            <th width="80"></th>
                            <th  width="50"></th>
                            <th width="100" ></th>
                            <th width="150"> </th>
                            <th width="150"></th>
                            <th width="30"></th>
                            <th></th>
                        </tr>
                       
                    </tfoot>
                </table>
                <div id="shipment_performance">
                    <fieldset>
                        <table width="600" border="1" cellpadding="0" cellspacing="1" class="rpt_table" rules="all" >
                        <thead>
                        <tr>
                        <th colspan="4"> <font size="4">Shipment Performance</font></th>
                        </tr>
                        <tr>
                        <th>Particulars</th><th>No of PO</th><th>PO Qnty</th><th> %</th>
                        </tr>
                        </thead>
                        <tr bgcolor="#E9F3FF">
                        <td>On Time Shipment</td><td><? echo $number_of_order['ontime']; ?></td><td align="right"><? echo number_format($po_qnty['ontime'],0); ?></td><td align="right"><? echo number_format(((100*$po_qnty['ontime'])/$order_qnty_pcs_tot),2); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        <td> Delivery After Shipment Date</td><td><? echo $number_of_order['after']; ?></td><td align="right"><? echo number_format($po_qnty['after'],0); ?></td><td align="right"><? echo number_format(((100*$po_qnty['after'])/$order_qnty_pcs_tot),2); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        <td>Yet To Shipment </td><td><? echo $number_of_order['yet']; ?></td><td align="right"><? echo number_format($po_qnty['yet'],0); ?></td><td align="right"><? echo number_format(((100*$po_qnty['yet'])/$order_qnty_pcs_tot),2); ?></td>
                        </tr>
                        
                        <tr bgcolor="#E9F3FF">
                        <td> </td><td></td><td align="right"><? echo number_format($po_qnty['yet']+$po_qnty['ontime']+$po_qnty['after'],0); ?></td><td align="right"><? echo number_format(((100*$po_qnty['yet'])/$order_qnty_pcs_tot)+((100*$po_qnty['after'])/$order_qnty_pcs_tot)+((100*$po_qnty['ontime'])/$order_qnty_pcs_tot),2); ?></td>
                        </tr>
                        </table>
                    </fieldset>
                </div>
            </div>
            </fieldset>
     </div>
        
<?

	$html = ob_get_contents();
	foreach (glob(""."*.xls") as $filename) 
	{			
	   @unlink($filename);
	}
	$name="weekcapabooking".".xls";	
	$create_new_excel = fopen(''.$name, 'w');	
	$is_created = fwrite($create_new_excel,$html);
		
}

if($action=="show_image")
{
	echo load_html_head_contents("Set Entry","../../../../", 1, 1, $unicode);
    extract($_REQUEST);
	//echo "select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1";
	$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1");
	
	?>
    <table>
    <tr>
    <?
    foreach ($data_array as $row)
	{ 
	?>
    <td><img src='../../../../<? echo $row[csf('image_location')]; ?>' height='250' width='300' /></td>
    <?
	}
	?>
    </tr>
    </table>
    
    <?
}

//Ex-Factory Delv. and Return
if($action=="ex_factory_popup")
{
 	echo load_html_head_contents("Ex-Factory Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	//echo $id;//$job_no;
	?>
	<div style="width:100%" align="center">
		<fieldset style="width:500px"> 
        <div class="form_caption" align="center"><strong>Ex-Factory Details</strong></div><br />
            <div style="width:100%"> 
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="35">SL</th>
                        <th width="90">Ex-fac. Date</th>
                        <th width="120">System /Challan no</th>
                        <th width="100">Ex-Fact. Del.Qty.</th>
                        <th width="">Ex-Fact.Return Qty.</th>
                       
                     </tr>   
                </thead> 	 	
            </table>  
        </div>
        <div style="width:100%; max-height:400px;">
            <table cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
                <?
                $i=1;
              
				$exfac_sql=("select b.challan_no,a.sys_number,b.ex_factory_date, 
				CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_qnty,
				CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_return_qnty 
				from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b  where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.po_break_down_id in($id)  and b.country_id in($country_id) ");
                $sql_dtls=sql_select($exfac_sql);
                
                foreach($sql_dtls as $row_real)
                { 
                    if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";                               
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="35"><? echo $i; ?></td> 
                        <td width="90"><? echo change_date_format($row_real[csf("ex_factory_date")]); ?></td>
                        <td width="120"><? echo $row_real[csf("sys_number")]; ?></td>
                        <td width="100" align="right"><? echo $row_real[csf("ex_factory_qnty")]; ?></td>
                         <td width="" align="right"><? echo $row_real[csf("ex_factory_return_qnty")]; ?></td>
                    </tr>
                    <? 
                    $rec_qnty+=$row_real[csf("ex_factory_qnty")];
					 $rec_return_qnty+=$row_real[csf("ex_factory_return_qnty")];
                    $i++;
                }
                ?>
                <tfoot>
                <tr>
                    <th colspan="3">Total</th>
                    <th><? echo number_format($rec_qnty,2); ?></th>
                    <th><? echo number_format($rec_return_qnty,2); ?></th>
                </tr>
                <tr>
                 <th colspan="3">Total Balance</th>
                 <th colspan="2" align="right"><? echo number_format($rec_qnty-$rec_return_qnty,2); ?></th>
                </tr>
                </tfoot>
            </table>
        </div> 
		</fieldset>
	</div>    
	<?
    exit();	
}
?>