<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
//ini_set('memory_limit','3072M');
require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.yarns.php');
require_once('../../../../includes/class4/class.conversions.php');
require_once('../../../../includes/class4/class.emblishments.php');
require_once('../../../../includes/class4/class.commisions.php');
require_once('../../../../includes/class4/class.commercials.php');
require_once('../../../../includes/class4/class.others.php');
require_once('../../../../includes/class4/class.trims.php');
require_once('../../../../includes/class4/class.washes.php');
require_once('../../../../includes/class4/class.fabrics.php');


$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
//--------------------------------------------------------------------------------------------------------------------
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "");     	 
	exit();
}

$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
$yarn_count_library=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
$team_member_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
$team_leader_arr=return_library_array( "select id, team_leader_name from lib_marketing_team",'id','team_leader_name');
//$order_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");
$approve_arr=return_library_array( "select job_no, approved from wo_pre_cost_mst", "job_no", "approved");

if($action=="print_button_variable_setting")
{
	$print_report_format=0;
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=11 and report_id=64 and is_deleted=0 and status_active=1");
	echo "document.getElementById('report_ids').value = '".$print_report_format."';\n";
	echo "print_report_button_setting('".$print_report_format."');\n";
	exit();	
}
if($action=="search_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Season No Info", "../../../../", 1, 1,'','','');
	?>
	<script>
		$(document).ready(function(e) {
            setFilterGrid('tbl_list_search',-1);
        });
		
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
			
			$('#hide_season_id').val( id );
			$('#hide_season').val( name );
		}
    </script>
    </head>
    <body>
        <div align="center">
            <form name="styleRef_form" id="styleRef_form">
                <fieldset style="width:350px;">
                    <input type="hidden" name="hide_season" id="hide_season" value="" />
                     <input type="hidden" name="hide_season_id" id="hide_season_id" value="" />
                    <?
					$buyer_name_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
                        if($buyerID==0)
                        {
                            if ($_SESSION['logic_erp']["data_level_secured"]==1)
                            {
                                if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
                            }
                            else $buyer_id_cond="";
                        }
                        else $buyer_id_cond=" and buyer_id=$buyerID";
                        
                     
							
							$arr=array (1=>$buyer_name_arr);
						
						  $sql="select id,season_name,buyer_id from lib_buyer_season where status_active=1 and is_deleted=0 $buyer_id_cond order by season_name";
                        //echo $sql;	
                        echo create_list_view("tbl_list_search", "season_name,Buyer", "100,200","300","280",0, $sql , "js_set_value", "id,season_name", "", 1, "0,buyer_id", $arr , "season_name,buyer_id", "","",'0,0','',1) ;
                        ?>
                </fieldset>
            </form>
        </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}
if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
    <script>
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
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
				//selected_no.push( str );				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				//selected_no.splice( i, 1 ); 
			}
			var id = ''; var name = ''; var job = ''; var num='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				//num += selected_no[i] + ','; 
			}
			id 		= id.substr( 0, id.length - 1 );
			name 	= name.substr( 0, name.length - 1 ); 
			//num 	= num.substr( 0, num.length - 1 );
			//alert(name);
			$('#hide_job_id').val( id );
			$('#hide_job_no').val( name ); 
			//$('#hide_job_no').val( num );
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="100">Please Enter Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:80px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $txt_style_ref_no; ?>'+'**'+'<? echo $txt_style_ref_id; ?>', 'create_job_no_search_list_view', 'search_div', 'order_wise_budget_sweater_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
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

//style search------------------------------//
if($action=="create_job_no_search_list_view")
{
	extract($_REQUEST);
	//echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	list($company,$buyer,$search_type,$search_value,$cbo_year,$txt_style_ref_no,$txt_style_ref_id)=explode('**',$data);

	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	$cbo_year=str_replace("'","",$cbo_year);
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0)
		{
			$year_cond=" and YEAR(a.insert_date)=$cbo_year";
		}
		else
		{
			$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";	
		}
	}
	if($db_type==0) if($cbo_year!=0) $job_cond=" and year(a.insert_date)='$cbo_year'";
	else if($cbo_year!=0) $job_cond=" and to_char(a.insert_date,'YYYY')='$cbo_year'";
	
	if($search_type==1 && $search_value!=''){
		$search_con=" and a.job_no_prefix_num='$search_value'";	
	}
	else if($search_type==2 && $search_value!=''){
		$search_con=" and a.style_ref_no='$search_value'";	
	}

	
	if($db_type==0) $year_field="YEAR(a.insert_date) as job_year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as job_year";
	else $year_field="";
	
	if($buyer!=0) $buyer_cond="and a.buyer_name=$buyer"; else $buyer_cond="";
	$sql = "select a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,$year_field from wo_po_details_master a where a.company_name=$company $buyer_cond $year_cond $job_cond $search_con and is_deleted=0 order by job_no_prefix_num"; 
	//echo $sql;
	echo create_list_view("list_view", "Style Ref No,Job No,Year","160,90,100","400","200",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "0", $arr, "style_ref_no,job_no_prefix_num,job_year", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='hide_job_id' />";
	//echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='hide_job_no' />";
	?>
    <script language="javascript" type="text/javascript">
	var style_no='<? echo $txt_style_ref_no;?>';
	var style_id='<? echo $txt_style_ref_id;?>';
	//var style_des='<? echo $txt_style_ref;?>';
	//alert(style_id);
	if(style_no!="")
	{
		style_no_arr=style_no.split(",");
		style_id_arr=style_id.split(",");
		style_des_arr=style_des.split(",");
		var str_ref="";
		for(var k=0;k<style_no_arr.length; k++)
		{
			str_ref=style_no_arr[k]+'_'+style_id_arr[k];
			js_set_value(str_ref);
		}
	}
	</script>
    <?
	exit();
}


if ($action=="order_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data); 
	?>	
	<script>
		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#order_no_id").val(splitData[0]); 
			$("#order_no_val").val(splitData[1]); 
			parent.emailwindow.hide();
		}
	</script>
	<input type="hidden" id="order_no_id" />
	<input type="hidden" id="order_no_val" />
	<?
	if ($data[1]==0) $buyer_name=""; else $buyer_name=" and b.buyer_name=$data[1]";
	if ($data[2]=="") $order_no=""; else $order_no=" and a.po_number=$data[2]";
	$txt_job_id=str_replace("'","",$data[3]);
	//echo $data[3].'DDD';
	if ($data[3]=="") $job_no_cond=""; else $job_no_cond="  and b.id in($txt_job_id)";
	
	$sql="select a.id, a.po_number, b.job_no_prefix_num, b.job_no, b.buyer_name, b.style_ref_no from wo_po_details_master b, wo_po_break_down a  where b.job_no=a.job_no_mst and b.company_name=$data[0] and b.is_deleted=0 $buyer_name $job_no_cond ORDER BY b.job_no";
	//echo $sql;
	$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
	$arr=array(1=>$buyer);
	
	echo  create_list_view("list_view", "Job No,Buyer,Style Ref.,Order No", "110,110,150,180","610","350",0, $sql, "js_set_value", "id,po_number", "", 1, "0,buyer_name,0,0,0", $arr , "job_no_prefix_num,buyer_name,style_ref_no,po_number", "order_wise_budget_sweater_report_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0','') ;
	disconnect($con);
	exit(); 
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$report_type=str_replace("'","",$reporttype);
	$company_name=str_replace("'","",$cbo_company_name);
	$season=str_replace("'","",$txt_season);
	$txt_season_id=str_replace("'","",$txt_season_id);
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
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}

	$cbo_year=str_replace("'","",$cbo_year);
	if($db_type==0)
	{
		if(trim($cbo_year)!=0) $year_cond=" and YEAR(a.insert_date)=$cbo_year"; else $year_cond="";
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(a.insert_date,'YYYY')";
		if(trim($cbo_year)!=0) $year_cond=" $year_field_con=$cbo_year"; else $year_cond="";
	}
	
	$order_status_id=str_replace("'","",$cbo_order_status);
	$order_status_cond='';
	if($order_status_id==0)
	{
		$order_status_cond=" and b.is_confirmed in(1,2)";
	}
	else if($order_status_id!=0)
	{
		$order_status_cond=" and b.is_confirmed=$order_status_id";	
	}

	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	
	$job_no=str_replace("'","",$txt_job_no);
	//echo $job_no;die;
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
	if($txt_season_id=="") $season_cond=""; else $season_cond=" and a.season_buyer_wise in(".$txt_season_id.")";
	//if($txt_internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping=txt_internal_ref";
	
	$order_no=str_replace("'","",$txt_order_id);
	$order_num=str_replace("'","",$txt_order_no);
	if(str_replace("'","",$txt_order_id)!="" && str_replace("'","",$txt_order_id)!=0) $order_id_cond_trans=" and b.id in ($order_no)";
	else if ($order_num=="") $order_no_cond=""; else $order_no_cond=" and  b.po_number in ('$order_num') ";
	
	
	$date_cond='';
	if($report_type==1 || $report_type==2)
	{
		if(str_replace("'","",$cbo_search_date)==1)
		{
			if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
			{
				if($db_type==0)
				{
					$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
					$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
				}
				else if($db_type==2)
				{
					$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
					$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
				}
				$date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
				$date_max_profit=" and applying_period_to_date between '$start_date' and '$end_date'";
			}
		}
		else if(str_replace("'","",$cbo_search_date)==2)
		{
			if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
			{
				if($db_type==0)
				{
					$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
					$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
				}
				else if($db_type==2)
				{
					$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
					$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
				}
				$date_cond=" and b.po_received_date between '$start_date' and '$end_date'";
				$date_max_profit=" and applying_period_to_date between '$start_date' and '$end_date'";
			}//applying_period_date,applying_period_to_date
		}
		else if(str_replace("'","",$cbo_search_date)==3)// PO Insert Date
		{
			if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
			{
				if($db_type==0)
				{
					$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
					$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
					$date_cond=" and b.insert_date between '".$start_date."' and '".$end_date." 23:59:59'";
				}
				else if($db_type==2)
				{
					$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
					$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
					$date_cond=" and b.insert_date between '".$start_date."' and '".$end_date." 11:59:59 PM'";
				}
				$date_max_profit=" and applying_period_to_date between '$start_date' and '$end_date'";
			}//applying_period_date,applying_period_to_date
		}
		$hader_caption="Order Wise Budget On Shipout Report";
		$header_ord_qty="Order Qty(Pcs)";
		$header_act_ship_qty="Actual Shipment Qty(Pcs)";
	}
	$asking_profit_arr=array();$costing_date_arr=array();
	$asking_profit=sql_select("select id, company_id, applying_period_date, applying_period_to_date, asking_profit, max_profit from lib_standard_cm_entry where status_active=1 and is_deleted=0 and company_id=$company_name");//$date_max_profit
	foreach($asking_profit as $ask_row )
	{
		$applying_period_date=change_date_format($ask_row[csf('applying_period_date')],'','',1);
		$applying_period_to_date=change_date_format($ask_row[csf('applying_period_to_date')],'','',1);
		$diff=datediff('d',$applying_period_date,$applying_period_to_date);
		for($j=0;$j<$diff;$j++)
		{
			//$newDate=add_date(str_replace("'","",$applying_period_date),$j);
			$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);
			
			$asking_profit_arr[$newdate]['asking_profit']=$ask_row[csf('asking_profit')];
			//$asking_profit_arr[$newDate]['max_profit']=$ask_row[csf('max_profit')];
		}
	}
	//var_dump($asking_profit_arr);die;
	/*$costing_date_sql=sql_select("select c.job_no, c.costing_date from wo_pre_cost_mst c where c.status_active=1 and c.is_deleted=0 and c.company_id=$company_name");
	foreach($costing_date_sql as $row )
	{
		$cost_date=change_date_format($row[csf('costing_date')],'','',1);
		//echo $cost_date=change_date_format($row[csf('costing_date')]);
		$costing_date_arr[$row[csf('job_no')]]['ask']=$asking_profit_arr[$cost_date]['asking_profit'];
		$costing_date_arr[$row[csf('job_no')]]['max']=$asking_profit_arr[$cost_date]['max_profit'];
		$costing_date_arr[$row[csf('job_no')]]['costing_date']=$cost_date;
	}*/
	 $condition= new condition();
	 $condition->company_name("=$cbo_company_name");
	 if(str_replace("'","",$cbo_buyer_name)>0){
		  $condition->buyer_name("=$cbo_buyer_name");
	 }
	 if(str_replace("'","",$txt_job_no) !=''){
		  $condition->job_no_prefix_num("in($job_no)");
	 }
	 if(str_replace("'","",$cbo_order_status) >0){
		  $condition->is_confirmed("=$cbo_order_status");
	 }
	 if(str_replace("'","",$cbo_order_status)==0){
		  $condition->is_confirmed("in(1,2)");
	 }
	 if(str_replace("'","",$cbo_search_date) ==1 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!=''){
		  //$condition->country_ship_date(" between '$start_date' and '$end_date'");
		  $condition->pub_shipment_date(" between '$start_date' and '$end_date'");
	 }
	 if(str_replace("'","",$cbo_search_date) ==2 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
	 {
		 $condition->po_received_date(" between '$start_date' and '$end_date'");
		 //and b.po_received_date between '$start_date' and '$end_date' 
		// echo 'FFGG';
	 }
	
	 if(str_replace("'","",$cbo_search_date)==3 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
	 {
		 // echo $cbo_search_date.'aaaa';die;
		if($db_type==0)
		{
			$condition->insert_date(" between '".$start_date."' and '".$end_date." 23:59:59'");
		}
		else
		{
			$condition->insert_date(" between '".$start_date."' and '".$end_date." 11:59:59 PM'");
		}
		
		
	 }
	 if(str_replace("'","",$txt_file_no)!='')
	 {
		$condition->file_no("=$txt_file_no"); 
	 }
	 if(str_replace("'","",$txt_internal_ref)!='')
	 {
		$condition->grouping("=$txt_internal_ref"); 
	 }
	 if(str_replace("'","",$txt_order_no)!='')
	 {
		$condition->po_number("=$txt_order_no"); 
	 }
	 if(str_replace("'","",$txt_season)!='')
	 {
		//$condition->season("=$txt_season"); 
	 }
	 $condition->init();
	$yarn= new yarn($condition);
	//echo $yarn->getQuery(); die;
	
  
	$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
	//print_r($yarn_costing_arr);

	$yarn_req_qty_arr=$yarn->getOrderWiseYarnQtyArray();

	$yarn_des_data=$yarn->getOrderCountCompositionPercentAndTypeWiseYarnQtyAndAmountArray();
	$yarn_des_data_job=$yarn->getJobCountCompositionPercentAndTypeWiseYarnQtyAndAmountArray();
	//print_r($yarn_des_data);
	//echo $conversion->getQuery(); die;
	$trims= new trims($condition);
	$trims_costing_arr=$trims->getAmountArray_by_order();
	
	$fabric= new fabric($condition);
	$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
	//print_r($fabric_costing_arr);
	$emblishment= new emblishment($condition);
	$emblishment_costing_arr=$emblishment->getAmountArray_by_order();
	
	$emblishment= new emblishment($condition);
	$emblishment_costing_arr_name=$emblishment->getAmountArray_by_orderAndEmbname();
	$wash= new wash($condition);
	$emblishment_costing_arr_name_wash=$wash->getAmountArray_by_orderAndEmbname();
	$commercial= new commercial($condition);
	$commercial_costing_arr=$commercial->getAmountArray_by_order();
	 
	$commission= new commision($condition);
	$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
	$other= new other($condition);
	$other_costing_arr=$other->getAmountArray_by_order(); 
			
	if($report_type==1) //Order Wise-Show Button
	{
			
			$sql_budget="select a.job_no_prefix_num, b.insert_date, a.order_uom,a.job_no, a.buyer_name, a.style_ref_no, b.is_confirmed, a.agent_name, a.avg_unit_price, 
			a.dealing_marchant,a.team_leader, a.gmts_item_id, a.total_set_qnty as ratio, b.plan_cut, b.id as po_id, b.po_number, b.pub_shipment_date, b.po_received_date,
			b.po_quantity, b.po_total_price, b.unit_price, b.grouping, b.file_no,c.costing_date,c.costing_per
			from wo_po_details_master a, wo_po_break_down b LEFT JOIN wo_pre_cost_mst c on c.job_no=b.job_no_mst and c.entry_from=158 where a.job_no=b.job_no_mst and a.company_name='$company_name'  and a.status_active=1 and
			a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_quantity>0 $date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond 
		    $order_status_cond $season_cond $internal_ref_cond $file_no_cond";
			$result_sql_budget=sql_select($sql_budget); 
			$tot_rows_budget=count($result_sql_budget);
			$tot_po_amount=$tot_summ_yarn_cost=$tot_summ_purchase_cost=$tot_summ_trims_cost=$tot_summ_embl_cost=$tot_summ_commercial_cost=$tot_summ_commission_cost=$tot_summ_lab_test=$tot_summ_freight=$tot_summ_inspection=$tot_summ_certificate_pre_cost=$tot_summ_common_oh=$tot_summ_currier_pre_cost=$tot_summ_cm_cost=$tot_summ_expected_profit=$tot_summ_expected_profit_per=0;$all_po_id='';
			foreach($result_sql_budget as $row)
			{
				if($all_po_id=='') $all_po_id=$row[csf('po_id')];else $all_po_id.=",".$row[csf('po_id')];
				$po_wise_arr[$row[csf('po_id')]]['job_no']=$row[csf('job_no')];
				$po_wise_arr[$row[csf('po_id')]]['po_number']=$row[csf('po_number')];
				$po_wise_arr[$row[csf('po_id')]]['job_no_prefix']=$row[csf('job_no_prefix_num')];
				
				$po_wise_arr[$row[csf('po_id')]]['insert_date']=$row[csf('insert_date')];
				$po_wise_arr[$row[csf('po_id')]]['order_uom']=$row[csf('order_uom')];
				$po_wise_arr[$row[csf('po_id')]]['buyer_name']=$row[csf('buyer_name')];
				$po_wise_arr[$row[csf('po_id')]]['style_ref_no']=$row[csf('style_ref_no')];
				$po_wise_arr[$row[csf('po_id')]]['dealing_marchant']=$row[csf('dealing_marchant')];
				$po_wise_arr[$row[csf('po_id')]]['team_leader']=$row[csf('team_leader')];
				$po_wise_arr[$row[csf('po_id')]]['gmts_item_id']=$row[csf('gmts_item_id')];
				$po_wise_arr[$row[csf('po_id')]]['is_confirmed']=$row[csf('is_confirmed')];
				$po_wise_arr[$row[csf('po_id')]]['agent_name']=$row[csf('agent_name')];
				$po_wise_arr[$row[csf('po_id')]]['avg_unit_price']=$row[csf('avg_unit_price')];
				$po_wise_arr[$row[csf('po_id')]]['po_quantity']=$row[csf('po_quantity')];
				$po_wise_arr[$row[csf('po_id')]]['po_quantity_pcs']=$row[csf('po_quantity')]*$row[csf('ratio')];
				$po_wise_arr[$row[csf('po_id')]]['po_total_price']=$row[csf('po_total_price')];
				$po_wise_arr[$row[csf('po_id')]]['unit_price']=$row[csf('unit_price')];
				$po_wise_arr[$row[csf('po_id')]]['ratio']=$row[csf('ratio')];
				$po_wise_arr[$row[csf('po_id')]]['costing_per']=$row[csf('costing_per')];
				$po_wise_arr[$row[csf('po_id')]]['plan_cut']=$row[csf('plan_cut')];
				$po_wise_arr[$row[csf('po_id')]]['pub_shipment_date']=$row[csf('pub_shipment_date')];
				$po_wise_arr[$row[csf('po_id')]]['po_received_date']=$row[csf('po_received_date')];
				$po_wise_arr[$row[csf('po_id')]]['plan_cut']=$row[csf('plan_cut')];
				
				//$yarn_costing=$yarn_costing_arr[$row[csf('po_id')]];
				$po_wise_arr[$row[csf('po_id')]]['yarn_cost']=$yarn_costing_arr[$row[csf('po_id')]];
				$po_wise_arr[$row[csf('po_id')]]['yarn_req_qty']=$yarn_req_qty_arr[$row[csf('po_id')]];
				
				
				$po_wise_arr[$row[csf('po_id')]]['fab_purchase_cost']=array_sum($fabric_costing_arr['sweater']['grey'][$row[csf('po_id')]])+array_sum($fabric_costing_arr['knit']['grey'][$row[csf('po_id')]])+array_sum($fabric_costing_arr['woven']['grey'][$row[csf('po_id')]]);
				
				$po_wise_arr[$row[csf('po_id')]]['trim_cost']= $trims_costing_arr[$row[csf('po_id')]];
				$po_wise_arr[$row[csf('po_id')]]['print_amount']= $emblishment_costing_arr_name[$row[csf('po_id')]][1];
				$po_wise_arr[$row[csf('po_id')]]['embroidery_amount']= $emblishment_costing_arr_name[$row[csf('po_id')]][2];
				$po_wise_arr[$row[csf('po_id')]]['wash_amount']= $emblishment_costing_arr_name[$row[csf('po_id')]][3];
				$po_wise_arr[$row[csf('po_id')]]['special_amount']= $emblishment_costing_arr_name[$row[csf('po_id')]][4];
				$po_wise_arr[$row[csf('po_id')]]['other_amount']= $emblishment_costing_arr_name[$row[csf('po_id')]][5];
				
				$po_wise_arr[$row[csf('po_id')]]['foreign']= $commission_costing_arr[$row[csf('po_id')]][1];
				$po_wise_arr[$row[csf('po_id')]]['local']= $commission_costing_arr[$row[csf('po_id')]][2];
				$po_wise_arr[$row[csf('po_id')]]['lab_test']= $other_costing_arr[$row[csf('po_id')]]['lab_test'];
				$po_wise_arr[$row[csf('po_id')]]['freight']= $other_costing_arr[$row[csf('po_id')]]['freight'];
				$po_wise_arr[$row[csf('po_id')]]['inspection']= $other_costing_arr[$row[csf('po_id')]]['inspection'];
				$po_wise_arr[$row[csf('po_id')]]['certificate_pre_cost']= $other_costing_arr[$row[csf('po_id')]]['certificate_pre_cost'];
				$po_wise_arr[$row[csf('po_id')]]['common_oh']= $other_costing_arr[$row[csf('po_id')]]['common_oh'];
				$po_wise_arr[$row[csf('po_id')]]['currier_pre_cost']= $other_costing_arr[$row[csf('po_id')]]['currier_pre_cost'];
				$po_wise_arr[$row[csf('po_id')]]['cm_cost']= $other_costing_arr[$row[csf('po_id')]]['cm_cost'];
				$po_wise_arr[$row[csf('po_id')]]['commercial_cost']=$commercial_costing_arr[$row[csf('po_id')]];
				$po_wise_arr[$row[csf('po_id')]]['yarn_desc']=$yarn_des_data[$row[csf('po_id')]];
				// Summary Part
				$tot_po_amount+=$row[csf('po_quantity')]*$row[csf('unit_price')];
				//$tot_summ_yarn_cost+=$yarn_costing_arr[$row[csf('po_id')]];
				$tot_summ_purchase_cost+=array_sum($fabric_costing_arr['sweater']['grey'][$row[csf('po_id')]])+array_sum($fabric_costing_arr['knit']['grey'][$row[csf('po_id')]])+array_sum($fabric_costing_arr['woven']['grey'][$row[csf('po_id')]]);
				
				$tot_summ_trims_cost+=$trims_costing_arr[$row[csf('po_id')]];
				$tot_summ_embl_cost+=$emblishment_costing_arr_name[$row[csf('po_id')]][1]+$emblishment_costing_arr_name[$row[csf('po_id')]][2]+$emblishment_costing_arr_name[$row[csf('po_id')]][3]+$emblishment_costing_arr_name[$row[csf('po_id')]][4]+$emblishment_costing_arr_name[$row[csf('po_id')]][5];
				$tot_summ_commercial_cost+=$commercial_costing_arr[$row[csf('po_id')]];
				$tot_summ_commission_cost+=$commission_costing_arr[$row[csf('po_id')]][1]+$commission_costing_arr[$row[csf('po_id')]][2];
				
				$tot_summ_lab_test+=$other_costing_arr[$row[csf('po_id')]]['lab_test'];
				$tot_summ_freight+=$other_costing_arr[$row[csf('po_id')]]['freight'];
				$tot_summ_inspection+=$other_costing_arr[$row[csf('po_id')]]['inspection'];
				$tot_summ_certificate_pre_cost+=$other_costing_arr[$row[csf('po_id')]]['certificate_pre_cost'];
				$tot_summ_common_oh+=$other_costing_arr[$row[csf('po_id')]]['common_oh'];
				$tot_summ_currier_pre_cost+=$other_costing_arr[$row[csf('po_id')]]['currier_pre_cost'];	
				$tot_summ_cm_cost+=$other_costing_arr[$row[csf('po_id')]]['cm_cost'];	
				
				$costing_date=change_date_format($row[csf('costing_date')],'','',1);//$costing_date_arr[$row[csf('job_no')]]['costing_date'];//
				$tot_summ_expected_profit+=($asking_profit_arr[$costing_date]['asking_profit']*$tot_po_amount)/100;
				$tot_summ_expected_profit_per+=$asking_profit_arr[$costing_date]['asking_profit'];
				
						
			}
			$poIds=chop($all_po_id,','); $po_cond_for_in=""; 
			$po_ids=count(array_unique(explode(",",$all_po_id)));
				if($db_type==2 && $po_ids>1000)
				{
					$po_cond_for_in=" and (";
					$poIdsArr=array_chunk(explode(",",$poIds),999);
					foreach($poIdsArr as $ids)
					{
						$ids=implode(",",$ids);
						$po_cond_for_in.=" d.id in($ids) or"; 
					}
					$po_cond_for_in=chop($po_cond_for_in,'or ');
					$po_cond_for_in.=")";
				}
				else
				{
					$po_cond_for_in=" and d.id in($poIds)";
				}
    $sql_po="select a.job_no,d.id as po_id, c.item_number_id, c.color_number_id, c.order_quantity, c.plan_cut_qnty from wo_po_details_master a, wo_po_break_down d, wo_po_color_size_breakdown c where a.job_no=d.job_no_mst and a.job_no=c.job_no_mst and d.id=c.po_break_down_id  and a.is_deleted=0 and a.status_active=1 and d.is_deleted=0 and d.status_active=1 and c.is_deleted=0 and c.status_active=1 $po_cond_for_in order by d.id ASC";
	$sql_po_data=sql_select($sql_po); $gmts_item_color_qty_arr=array();
	foreach($sql_po_data as $row)
	{
		$gmts_item_color_qty_arr[$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
	}
	unset($sql_po_data);
	
	
	if($db_type==2)
	{
		$po_grp="listagg(CAST(d.id as VARCHAR(4000)),',') within group (order by d.id) as po_ids";
		
	}
	else
	{
		$po_grp="group_concat(d.id) as po_ids";
		
	}
  $data_sql="select a.id, a.fabric_cost_dtls_id, a.job_no, a.count_id, a.copm_one_id, a.percent_one, a.copm_two_id, a.percent_two, a.type_id, a.cons_ratio, a.cons_qnty, a.rate, a.amount, a.avg_cons_qnty, a.supplier_id, a.color, a.consdznlbs, a.rate_dzn, 
	b.item_number_id, b.body_part_id, b.fabric_description, b.color_type_id, b.uom, c.color_number_id, c.stripe_color, c.measurement ,d.id as po_id
	from wo_pre_cost_fab_yarn_cost_dtls a, wo_pre_cost_fabric_cost_dtls b, wo_pre_stripe_color c,wo_po_break_down d where a.fabric_cost_dtls_id=b.id and b.id=c.pre_cost_fabric_cost_dtls_id and a.color=c.stripe_color and d.job_no_mst=a.job_no and d.job_no_mst=b.job_no  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $po_cond_for_in group by a.id, a.fabric_cost_dtls_id, a.job_no, a.count_id, a.copm_one_id, a.percent_one, a.copm_two_id, a.percent_two, a.type_id, a.cons_ratio, a.cons_qnty, a.rate, a.amount, a.avg_cons_qnty, a.supplier_id, a.color, a.consdznlbs, a.rate_dzn, 
	b.item_number_id, b.body_part_id, b.fabric_description, b.color_type_id, b.uom, c.color_number_id, c.stripe_color, c.measurement,d.id";
	$data_arr_yarn=sql_select($data_sql);
	foreach($data_arr_yarn as $yarn_row)
	{
		$costing_per=$po_wise_arr[$yarn_row[csf('po_id')]]['costing_per'];
		 if($costing_per==1){
            $order_price_per_dzn=12;
            $costing_for=" DZN";
        }
        else if($costing_per==2){
            $order_price_per_dzn=1;
            $costing_for=" PCS";
        }
        else if($costing_per==3){
            $order_price_per_dzn=24;
            $costing_for=" 2 DZN";
        }
        else if($costing_per==4){
            $order_price_per_dzn=36;
            $costing_for=" 3 DZN";
        }
        else if($costing_per==5){
            $order_price_per_dzn=48;
            $costing_for=" 4 DZN";
        }
		$poQty=0; $yarn_req_kg=0; $yarn_req_lbs=0; $amount_req=0;
		$poQty=$gmts_item_color_qty_arr[$yarn_row[csf('po_id')]][$yarn_row[csf('item_number_id')]][$yarn_row[csf('color_number_id')]];
		//echo  $poQty.'='.$yarn_row[csf('measurement')];
		$yarn_req_kg=($yarn_row[csf('measurement')]/$order_price_per_dzn)*$poQty;
		$yarn_req_lbs=$yarn_req_kg*2.20462;
		$amount_req=$yarn_req_lbs*$yarn_row[csf('rate')];
		$yarn_po_wise_arr[$yarn_row[csf('po_id')]]['amount']+=$amount_req;
		$yarn_po_wise_arr[$yarn_row[csf('po_id')]]['req_qty']+=$yarn_req_lbs;
		$yarn_type_count_wise_arr[$yarn_row[csf('po_id')]][$yarn_row[csf('count_id')]][$yarn_row[csf('copm_one_id')]][$yarn_row[csf('percent_one')]][$yarn_row[csf('type_id')]]['req_qty']+=$yarn_req_lbs;
		$yarn_type_count_wise_arr[$yarn_row[csf('po_id')]][$yarn_row[csf('count_id')]][$yarn_row[csf('copm_one_id')]][$yarn_row[csf('percent_one')]][$yarn_row[csf('type_id')]]['amount']+=$amount_req;
		$tot_summ_yarn_cost+=$amount_req;
		//$summary_data[yarn_cost_job]+=$amount_req;
	}
			//echo $all_po_id.'DD';
			$total_summ_cost=$tot_summ_yarn_cost+$tot_summ_purchase_cost+$tot_summ_trims_cost+$tot_summ_embl_cost+$tot_summ_commercial_cost+$tot_summ_commission_cost+$tot_summ_lab_test+$tot_summ_freight+$tot_summ_inspection+$tot_summ_certificate_pre_cost+$tot_summ_common_oh+$tot_summ_currier_pre_cost+$tot_summ_cm_cost;
			$total_summ_profit=$tot_po_amount-$total_summ_cost;
			$tot_summ_expect_variance=$total_summ_profit-$tot_summ_expected_profit_per;	
				
			ob_start();
			?>
		<script>
			function toggle() 
				{
					var ele = document.getElementById("yarn_summary");
					//alert(ele);
					var text = document.getElementById("displayText");
					if(ele.style.display!= "none") 
					{
						ele.style.display = "none";
						text.innerHTML = "Show Yarn Summary";
					}
					else 
					{
						ele.style.display = "block";
						text.innerHTML = "Hide Yarn Summary";
					}
				} 
				
            </script>
			<div style="width:900px;" align="left">
                <table width="900" cellpadding="0" cellspacing="2" border="0">
                    <tr>
                        <td width="350" align="left">
                            <table width="350" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="2">
                                <thead align="center">
                                	<tr>
                                    	<th colspan="4">Order Wise Budget Cost Summary</th>
                                    </tr>
                                	<tr>
                                        <th>SL</th><th>Particulars</th><th>Amount</th><th>%</th>
                                    </tr>
                                </thead>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td width="20">1</td>
                                    <td width="130">Yarn Cost</td>
                                    <td width="120" align="right" id=""><? echo number_format($tot_summ_yarn_cost,2);?></td>
                                    <td width="80" align="right" id=""><? echo number_format(($tot_summ_yarn_cost/$tot_po_amount)*100,2);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>2</td>
                                    <td>Fabric Purchase</td>
                                    <td align="right" id=""><? echo number_format($tot_summ_purchase_cost,2);?></td>
                                    <td align="right" id=""><? echo number_format(($tot_summ_purchase_cost/$tot_po_amount)*100,2);?></td>
                                </tr>
                              
                                <tr bgcolor="#CCCCCC">
                                    <td colspan="2"><strong>Total Material & Service Cost</strong></td>
                                    <td align="right" id="tot_matr_ser_cost"><? echo number_format(($tot_summ_yarn_cost+$tot_summ_purchase_cost),2);?></td>
                                    <td align="right" id="tot_matr_ser_per"><? echo number_format((($tot_summ_yarn_cost+$tot_summ_purchase_cost)/$tot_po_amount)*100,2);?></td>
                                </tr>
                                <tr bgcolor="<?  echo $style1; ?>">
                                    <td>7</td>
                                    <td>Trims Cost</td>
                                    <td align="right" id=""><? echo number_format($tot_summ_trims_cost,2);?></td>
                                    <td align="right" id=""><? echo number_format(($tot_summ_trims_cost/$tot_po_amount)*100,2);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>8</td>
                                    <td>Print/ Emb. /Wash Cost</td>
                                    <td align="right" id="embelishment_cost"><? echo number_format($tot_summ_embl_cost,2);?></td>
                                    <td align="right" id="embelishment_cost_per"><? echo number_format(($tot_summ_embl_cost/$tot_po_amount)*100,2);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>9</td>
                                    <td>Commercial Cost</td>
                                    <td align="right" id="commercial_cost"><? echo number_format($tot_summ_commercial_cost,2);?></td>
                                    <td align="right" id="commercial_cost_per"><? echo number_format(($tot_summ_commercial_cost/$tot_po_amount)*100,2);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>10</td>
                                    <td>Commision Cost</td>
                                    <td align="right" id=""><? echo number_format($tot_summ_commission_cost,2);?></td>
                                    <td align="right" id=""><? echo number_format(($tot_summ_commission_cost/$tot_po_amount)*100,2);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>11</td>
                                    <td>Testing Cost</td>
                                    <td align="right" id="testing_cost"><? echo number_format($tot_summ_lab_test,2);?></td>
                                    <td align="right" id="testing_cost_per"><? echo number_format(($tot_summ_lab_test/$tot_po_amount)*100,2);?></td>
                                </tr>
                                    <tr bgcolor="<? echo $style1; ?>">
                                    <td>12</td>
                                    <td>Freight Cost</td>
                                    <td align="right" id="freight_cost"><? echo number_format($tot_summ_freight,2);?></td>
                                    <td align="right" id="freight_cost_per"><? echo number_format(($tot_summ_freight/$tot_po_amount)*100,2);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td width="20">13</td>
                                    <td width="100">Inspection Cost</td>
                                    <td align="right" id="inspection_cost"><? echo number_format($tot_summ_inspection,2);?></td>
                                    <td align="right" id="inspection_cost_per"><? echo number_format(($tot_summ_inspection/$tot_po_amount)*100,2);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>14</td>
                                    <td>Certificate Cost</td>
                                    <td align="right" id="certificate_cost"><? echo number_format($tot_summ_certificate_pre_cost,2);?></td>
                                    <td align="right" id="certificate_cost_percent"><? echo number_format(($tot_summ_certificate_pre_cost/$tot_po_amount)*100,2);?></td>
                                </tr>
                                    <tr bgcolor="<? echo $style; ?>">
                                    <td>15</td>
                                    <td>Operating Exp.</td>
                                    <td align="right" id="commn_cost"><? echo number_format($tot_summ_common_oh,2);?></td>
                                    <td align="right" id="commn_cost_per"><? echo number_format(($tot_summ_common_oh/$tot_po_amount)*100,2);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>16</td>
                                    <td>Courier Cost</td>
                                    <td align="right" id="courier_cost"><? echo number_format($tot_summ_currier_pre_cost,2);?></td>
                                    <td align="right" id="courier_cost_per"><? echo number_format(($tot_summ_currier_pre_cost/$tot_po_amount)*100,2);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>17</td>
                                    <td>CM Cost</td>
                                    <td align="right" id="cm_cost"><? echo number_format($tot_summ_cm_cost,2);?></td>
                                    <td align="right" id="cm_cost_per"><? echo number_format(($tot_summ_cm_cost/$tot_po_amount)*100,2);?></td>
                                </tr>
                                <tr bgcolor="#999999">
                                    <td>18</td>
                                    <td>Total Cost</td>
                                    <td align="right" id="cost_cost"><? echo number_format($total_summ_cost,2);?></td>
                                    <td align="right" id="cost_cost_per"><? echo number_format(($total_summ_cost/$tot_po_amount)*100,2);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>19</td>
                                    <td>Total Order Value</td>
                                    <td align="right" id="order_id"><? echo number_format($tot_po_amount,2);?></td>
                                    <td align="right" id="order_percent"><? echo number_format(($tot_po_amount/$tot_po_amount)*100,2);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>20</td>
                                    <td>Profit/Loss </td>
                                    <td align="right" id="fab_profit_id"><?  echo number_format($total_summ_profit,2);?></td>
                                    <td align="right" id="profit_fab_percentage"><? echo number_format(($total_summ_profit/$tot_po_amount)*100,2);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>21</td>
                                    <td>Expected Profit <div id="expt_percent"></div></td>
                                    <td align="right" id="expected_id"><? echo number_format($tot_summ_expected_profit,2);?></td>
                                    <td align="right" id="profit_expt_fab_percentage"><? echo number_format(($tot_summ_expected_profit/$tot_po_amount)*100,2);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>22</td>
                                    <td>Expt.Profit Variance </td>
                                    <td align="right" id="expt_p_variance_id"><? echo number_format($tot_summ_expect_variance,2);?></td>
                                    <td align="right" id="expt_p_percent"><? echo number_format(($tot_summ_expect_variance/$tot_po_amount)*100,2);?></td>
                                </tr>
                            </table>
                        </td>
                       
                    </tr>
                </table>
            </div>
			<br>
			<div>
			
			<h3 align="left" id="accordion_h2" style="width:3920px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel2', '')"> -</h3>
        	<fieldset style="width:100%;" id="content_search_panel2">	
            <table width="3970">
                    <tr class="form_caption">
                        <td colspan="42" align="center"><strong><? echo $report_title;?></strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="42" align="center"><strong><? echo $company_library[$company_name]; ?></strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td align="left"><strong>Details Report </strong></td>
                    </tr>
            </table>
               <? $asking_profit_head=$asking_profit_arr[$company_name]['asking_profit']; 
			   
			   		if(str_replace("'","",$cbo_search_date)==1) $caption="Ship. Date";
					else if(str_replace("'","",$cbo_search_date)==2) $caption="PO Recv. Date";
					else $caption="PO Insert Date";
			   ?>
            <table  class="rpt_table" width="3970" cellpadding="0" cellspacing="0" border="1" rules="all">
               <thead>
                    <tr>
                        <th width="40" rowspan="2">SL</th>
                        <th width="70" rowspan="2">Buyer</th>
                        <th width="70" rowspan="2">Job No</th>
                        <th width="100" rowspan="2">Order No</th>
                        <th width="100" rowspan="2">Approve Status</th>
                        <th width="80" rowspan="2">File No</th>
                        <th width="80" rowspan="2">Internal Ref:</th>
                        <th width="100" rowspan="2">Order Status</th>
                        <th width="110" rowspan="2">Style</th>
                        <th width="110" rowspan="2">Item Name</th>
                        <th width="110" rowspan="2">Team Leader</th>
						<th width="110" rowspan="2">Team Member</th>
                        <th width="70" rowspan="2"><? echo $caption; ?></th>
                        <th width="90" rowspan="2">Order Qty</th>
						<th width="50" rowspan="2">Uom</th>
                        <th width="90" rowspan="2">Avg Unit Price</th>
                        <th width="100" rowspan="2">Order Value</th>
                        <th colspan="4">Yarn Cost</th>
                        <th width="100" rowspan="2">Trim Cost</th>
                        <th colspan="5">Embell. Cost</th>
                        <th width="120" rowspan="2">Commercial Cost</th>
                        <th colspan="2">Commission</th>
                        <th width="100" rowspan="2">Testing Cost</th>
                        <th width="100" rowspan="2">Freight Cost</th>
                        <th width="120" rowspan="2">Inspection Cost</th>
                        <th width="100" rowspan="2">Certificate Cost</th>
                        <th width="100" rowspan="2">Operating Exp</th>
                        <th width="100" rowspan="2">Courier Cost</th>
                        <th width="120" rowspan="2">CM/DZN</th>
                        <th width="100" rowspan="2">Total CM Cost</th>
                        <th width="100" rowspan="2">Total Cost</th>
                        <th width="100" rowspan="2">Profit/Loss</th>
                        <th width="100" rowspan="2">Profit/Loss %</th>
                        
                        <th width="" rowspan="2">Yarn Cons</th>
                    </tr>
                    <tr>
                        <th width="100">Avg Yarn Rate</th>
                        <th width="80">Yarn Cost</th>
                        <th width="80">Yarn Cost %</th>
                        <th width="100">Fabric Purchase</th>
						
                       
                        <th width="80">Printing</th>
                        <th width="85">Embroidery</th>
                        <th width="80">Special Works</th>
                        <th width="80">Wash Cost</th>
                        <th width="80">GMT Dyeing</th>
                        <th width="120">Foreign</th>
                        <th width="120">Local</th>
                    </tr>
				</thead>
                
            </table>
            <div style="width:3990px; max-height:400px; overflow-y:scroll" id="scroll_body">
            <table class="rpt_table" width="3970" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
			<? 
			
            $i=1; $total_order_qty=0;  $total_yarn_dyeing_cost=0; $total_yarn_cost=0; $total_order_value=0;$total_purchase_cost=0; $grand_tot_trims_cost=0; $total_fabric_dyeing_cost=0; $total_knitting_cost=0; $total_heat_setting_cost=0;$total_finishing_cost=0; $total_washing_cost=0; $fabric_dyeing_cost_dzn=0; $other_cost=0;
$all_over_print_cost=0;$total_trim_cost=0;$total_commercial_cost=0;$total_yarn_cons=0;
		
			foreach($po_wise_arr as $po_id=>$row )
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
				if(str_replace("'","",$cbo_search_date)==1)
				{
					$ship_po_recv_date=change_date_format($row[('pub_shipment_date')]);
				}
				else if(str_replace("'","",$cbo_search_date)==2)
				{
					$ship_po_recv_date=change_date_format($row[('po_received_date')]);
				}
				else if(str_replace("'","",$cbo_search_date)==3)
				{
					$insert_date=explode(" ",$row[('insert_date')]);
					$ship_po_recv_date=change_date_format($insert_date[0]);
				}
				$approve_status=$approve_arr[$row[('job_no')]];
				if($approve_status==1) $is_approve="Approved"; else $is_approve="No";
				$dzn_qnty=0;
				$costing_per_id=$costing_per_arr[$row[('job_no')]];
				if($costing_per_id==1) $dzn_qnty=12;
				else if($costing_per_id==3) $dzn_qnty=12*2;
				else if($costing_per_id==4) $dzn_qnty=12*3;
				else if($costing_per_id==5) $dzn_qnty=12*4;
				else $dzn_qnty=1;
				
				$dzn_qnty=$dzn_qnty*$row[('ratio')];
				$order_qty_pcs=$row[('po_quantity')]*$row[('ratio')];
				$dzn_qnty_yarn=$dzn_qnty*$row[('ratio')];
				
				$plan_cut_qnty=$row[('plan_cut')]*$row[('ratio')];
				$order_value=$row[('po_total_price')];
				$plancut_value=$plan_cut_qnty*$row[('avg_unit_price')];
				
				//$total_order_amount+=$order_value; 
				$total_plancut_amount+=$plancut_value;
				$yarn_descrip_data=$yarn_des_data[$po_id];//$row[('yarn_desc')];
						$qnty=0; $amount=0;
						foreach($yarn_descrip_data as $count=>$count_value)
						{
							foreach($count_value as $Composition=>$composition_value)
							{
								foreach($composition_value as $percent=>$percent_value)
								{	
									foreach($percent_value as $type=>$qty_amt)
									{
										$count_id=$count;//$yarnRow[0];
										$copm_one_id=$Composition;//$yarnRow[1];
										$percent_one=$percent;//$yarnRow[2];
										$type_id=$type;//$yarnRow[5];
										$qnty=$qty_amt['qty'];
										$amount=$qty_amt['amount'];
										
										//$yarn_description_data_arr[$count_id][$copm_one_id][$percent_one][$type_id]['qty']+=$qnty;
										//$yarn_description_data_arr[$count_id][$copm_one_id][$percent_one][$type_id]['amount']+=$amount;
										$yarn_description_data_arr[$count_id][$copm_one_id][$percent_one][$type_id]['qty']+=$yarn_type_count_wise_arr[$po_id][$count_id][$copm_one_id][$percent_one][$type_id]['req_qty'];
										$yarn_description_data_arr[$count_id][$copm_one_id][$percent_one][$type_id]['amount']+=$yarn_type_count_wise_arr[$po_id][$count_id][$copm_one_id][$percent_one][$type_id]['amount'];
									}
								}
							}
						} 
				?>
				 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                     <td width="40"><? echo $i; ?></td>
                     <td width="70"><p><? echo $buyer_library[$row[('buyer_name')]]; ?></p></td>
                     <td width="70"><p><? echo $row[('job_no_prefix')]; ?></p></td>
                     <td width="100"><p><a href="#" onClick="precost_bom_pop('<? echo $row[('po_id')]; ?>','<? echo $row[('job_no')]; ?>','<?  echo $company_name; ?>','<? echo $row[('buyer_name')]; ?>');"><? echo $row[('po_number')]; ?></a></p></td>
                     <td width="100"><p><? echo $is_approve; ?></p></td>
                     <td width="80"><p><? echo $row[('file_no')]; ?></p></td>
                     <td width="80"><p><? echo $row[('grouping')]; ?></p></td>
                     <td width="100"><p><? echo  $order_status[$row[('is_confirmed')]]; ?></p></td>
                     <td width="110"><p><? echo $row[('style_ref_no')]; ?></p></td>
                     <td width="110"><div style="width:110px; word-wrap:break-word;"><? $gmts_item=''; $gmts_item_id=explode(",",$row[('gmts_item_id')]);
						foreach($gmts_item_id as $item_id)
						{
							if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=", ".$garments_item[$item_id];
						}
						echo $gmts_item; ?></div></td>
                     <td width="110"><p><? echo $team_leader_arr[$row[('team_leader')]]; ?></p></td>
					 <td width="110"><p><? echo $team_member_arr[$row[('dealing_marchant')]]; ?></p></td>
                     <td width="70"><p><? echo $ship_po_recv_date; ?></p></td>
                     <td width="90" align="right"><p><? echo number_format($order_qty_pcs,2); ?></p></td>
					  <td width="50" align="right"><p><? echo $unit_of_measurement[$row[('order_uom')]]; ?></p></td>
                     <td width="90" align="right"><p><? echo number_format($row[('avg_unit_price')],4); ?></p></td>
                     <td width="100" align="right"><p><? echo number_format($row[('po_total_price')],2); ?></p></td>
                     <? 
						$commercial_cost=$row[('commercial_cost')];
						//$yarn_po_wise_arr[$yarn_row[csf('po_id')]]['amount']+=$amount_req;
		//$yarn_po_wise_arr[$yarn_row[csf('po_id')]]['req_qty']+=$yarn_req_lbs;
						$yarn_costing=$yarn_po_wise_arr[$po_id]['amount'];//$row[('yarn_cost')];
						$yarn_req_qty=$yarn_po_wise_arr[$po_id]['req_qty'];
						$yarn_cost_percent=($yarn_costing/$order_value)*100;
						$avg_rate=$yarn_costing/$yarn_req_qty;
						
						//$fab_purchase_knit=array_sum($fabric_costing_arr['knit']['grey'][$po_id]);
						//$fab_purchase_woven=array_sum($fabric_costing_arr['woven']['grey'][$po_id]);
						//echo  $fab_purchase_knit.'='. $fab_purchase_woven.'<br>';
						$fab_purchase=$row[('fab_purchase_cost')];
						$yarn_cons=($yarn_req_qty/$plan_cut_qnty)*$dzn_qnty;
						// $row[csf('order_uom')]
						
						$trim_amount= $row[('trim_cost')];
						$print_amount=$row[('print_amount')];
						$embroidery_amount=$row[('embroidery_amount')];
						$special_amount=$row[('special_amount')];
						$wash_cost=$row[('wash_amount')];
						$other_amount=$row[('other_amount')];
						$foreign=$row[('foreign')];
						$local=$row[('local')];
						$test_cost=$row[('lab_test')];
						$freight_cost= $row[('freight')];
						$inspection=$row[('inspection')];
						$certificate_cost=$row[('certificate_pre_cost')];
						$common_oh=$row[('common_oh')];
						$currier_cost=$row[('currier_pre_cost')];
						$cm_cost=$row[('cm_cost')];
						$cm_cost_dzn=($cm_cost/$order_qty_pcs)*$dzn_qnty;
						
						$total_cost=$yarn_costing+$fab_purchase+$trim_amount+$test_cost+$print_amount+$embroidery_amount+$special_amount+$other_amount+$wash_cost+$commercial_cost+$foreign+$local+$freight_cost+$inspection+$certificate_cost+$common_oh+$currier_cost+$cm_cost;
						
						$total_profit=$order_value-$total_cost;
						$total_profit_percentage2=$total_profit/$order_value*100; 
						$expected_profit=$asking_profit_arr[$row[('company_name')]]['asking_profit']*$order_value/100;
						$expect_variance=$total_profit-$expected_profit;
						
						if($yarn_costing<=0) $color_yarn="red"; else $color_yarn="";	
						if($commercial_cost<=0) $color_com="red"; else $color_com="";	
					 ?>
                     <td width="100" align="right"><a href="##" onClick="generate_pre_cost_report('<? echo $po_id; ?>','<? echo $row[('job_no')];?>','<? echo $company_name; ?>','<? echo $row[('buyer_name')]; ?>','<? echo $row[('style_ref_no')]; ?>','precost_yarn_detail')"><? echo number_format($avg_rate,2); ?></a></td>
                     <td width="80" align="right" title="Req=<? echo $row[('yarn_req_qty')];?>" bgcolor="<? echo $color_yarn; ?>"><?  echo number_format($yarn_costing,2);   //$yarn_costing=$yarn->getOrderWiseYarnAmount($row[csf('po_id')]);?></td>
                     <td width="80" align="right"><? echo number_format($yarn_cost_percent,2); ?></td>
                     <td width="100" align="right"><a href="##" onClick="generate_precost_fab_purchase_detail('<? echo $po_id; ?>','<? echo $row[('job_no')];?>','<? echo $company_name; ?>','<? echo $row[('buyer_name')]; ?>','<? echo $fab_source_id; ?>','fab_purchase_detail')"><? echo number_format($fab_purchase,2); ?></a></td>
                    
				<?
					//echo  $total_cost;
					$total_print_amount+=$print_amount;
					$total_embroidery_amount+=$embroidery_amount;
					$total_special_amount+=$special_amount;
					$total_other_amount+=$other_amount;
					$total_wash_cost+=$wash_cost;
					
					$total_foreign_amount+=$foreign;
					$total_local_amount+=$local;
					$total_test_cost_amount+=$test_cost;
					$total_freight_amount+=$freight_cost;
					$total_inspection_amount+=$inspection;
					$total_certificate_amount+=$certificate_cost;
					
					$total_common_oh_amount+=$common_oh;
					$total_currier_amount+=$currier_cost;
					$total_cm_amount+=$cm_cost;
					$max_profit=$asking_profit_arr[$row[('company_name')]]['max_profit'];
					$company_asking=$asking_profit_arr[$row[('company_name')]]['asking_profit'];
					
					if($trim_amount<=0) $color_trim="red"; else $color_trim="";	
					if($cm_cost<=0) $color="red"; else $color="";
					?>
                     <td width="100" align="right" bgcolor="<? echo $color_trim; ?>"><a href="##" onClick="generate_precost_trim_cost_detail('<? echo $po_id; ?>','<? echo $row[('job_no')];?>','<? echo $company_name; ?>','<? echo $row[('buyer_name')]; ?>','<? echo $row[('style_ref_no')]; ?>','trim_cost_detail')"><? echo number_format($trim_amount,2); ?></a><? //echo number_format($trim_amount,2); ?></td>
                     <td width="80" align="right"><a href="##" onClick="generate_precost_embell_cost_detail('<? echo $po_id; ?>','<? echo $row[('job_no')];?>','<? echo $company_name; ?>','<? echo $row[('buyer_name')]; ?>','<? echo $row[('style_ref_no')]; ?>','embl_cost_detail',1)"><? echo number_format($print_amount,2); ?></a></td>
                     <td width="85" align="right"><a href="##" onClick="generate_precost_embell_cost_detail('<? echo $po_id; ?>','<? echo $row[('job_no')];?>','<? echo $company_name; ?>','<? echo $row[('buyer_name')]; ?>','<? echo $row[('style_ref_no')]; ?>','embl_cost_detail',2)"><? echo number_format($embroidery_amount,2); ?></a></td>
                     <td width="80" align="right"><? echo number_format($special_amount,2); ?></td>
                     <td width="80" align="right"><a href="##" onClick="generate_precost_embell_cost_detail('<? echo $po_id; ?>','<? echo $row[('job_no')];?>','<? echo $company_name; ?>','<? echo $row[('buyer_name')]; ?>','<? echo $row[('style_ref_no')]; ?>','embl_cost_detail',3)"><? echo number_format($wash_cost,2); ?></a></td>
                     <td width="80" align="right"><? echo number_format($other_amount,2); ?></td>
                     <td width="120" align="right" bgcolor="<? echo $color_com; ?>"><? echo number_format($commercial_cost,2); ?></td>
                     <td width="120" align="right"><? echo number_format($foreign,2) ?></td>
                     <td width="120" align="right"><? echo number_format($local,2) ?></td>
                     <td width="100" align="right"><? echo number_format($test_cost,2);?></td>
                     <td width="100" align="right"><? echo number_format($freight_cost,2); ?></td>
                     <td width="120" align="right"><? echo number_format($inspection,2);?></td>
                     <td width="100" align="right"><? echo number_format($certificate_cost,2); ?></td>
                     <td width="100" align="right"><? echo number_format($common_oh,2); ?></td>
                     <td width="100" align="right"><? echo number_format($currier_cost,2);?></td>
                     <td width="120" align="right"><? echo number_format($cm_cost_dzn,2);?></td>
                     <td width="100" align="right" bgcolor="<? echo $color; ?>"><? echo number_format($cm_cost,2);?></td>
                     <td width="100" align="right"><? echo number_format($total_cost,2); ?></td>
                    <?
						if($total_profit_percentage2<=0 ) $color_pl="red";
						else if($total_profit_percentage2>$max_profit) $color_pl="yellow";	
						else if($total_profit_percentage2<=$max_profit) $color_pl="green";	
						else $color_pl="";	
						
						
					?>
                     <td width="100" align="right" bgcolor="<? echo $color_pl; ?>"><? echo number_format($total_profit,2); ?></td>
                     <td width="100" align="right"><? echo number_format($total_profit_percentage2,2); ?></td>
                     
                     <td width="" align="right"><? echo number_format($yarn_cons,2);?></td>
                  </tr> 
                <?
				$total_order_qty+=$order_qty_pcs;
				$total_order_amount+=$order_value;
				$total_plan_cut_qty+=$plan_cut_qnty;
				$total_yarn_dyeing_cost+=$yarn_dyeing_cost;
				$total_yarn_cons+=$yarn_cons;
				$total_yarn_cost+=$yarn_costing;
				$total_purchase_cost+=$fab_purchase;
			
				$total_trim_cost+=$trim_amount;
				$total_commercial_cost+=$commercial_cost;
				$total_fab_cost_amount=$total_yarn_cost+$total_purchase_cost;
				
				//echo $total_cost.'<br>';
				//$total_fab_cost_amount2+=$total_fab_cost_amount;
				$total_embelishment_cost+=$print_amount+$embroidery_amount+$special_amount+$other_amount+$wash_cost;
				$total_commssion+=$foreign+$local;
				$total_testing_cost+=$test_cost;
				$total_freight_cost+=$freight_cost;
				$total_cm_cost+=$cm_cost;
				$total_tot_cost+=$total_cost;
				$total_inspection+=$inspection;
				$total_certificate_cost+=$certificate_cost;
				$total_common_oh+=$common_oh;
				$total_currier_cost+=$currier_cost;
				$total_fabric_profit+=$total_profit;
				
				$total_profit_fab_percentage_up+=$total_profit_percentage2;
				//echo $total_fab_cost_amount;
				$i++;
			}
			$total_profit_fab_percentage=$total_fab_profit/$total_order_amount*100;
			$total_yarn_cost_percentage=$total_yarn_cost/$total_order_amount*100;
			//$total_purchase_cost_percentage=$total_purchase_cost/$total_order_amount*100;
			?>
            	</table>
			</div>
			  <table class="tbl_bottom" width="3970" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
                <tr>
                    <td width="40">&nbsp;</td>
                    <td width="70">&nbsp;</td>
                    <td width="70">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="80">&nbsp;</td>
                    <td width="80">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="110">&nbsp;</td>
                    <td width="110">&nbsp;</td>
                    <td width="110">&nbsp;</td>
					<td width="110">&nbsp;</td>
                    <td width="70">Total</td>
                    <td width="90" align="right" id="total_order_qnty"><? echo number_format($total_order_qty,2); ?></td>
					<td width="50">&nbsp;</td>
                    <td width="90">&nbsp;</td>
                    <td width="100" align="right" id="total_order_amount2"><? echo number_format($total_order_amount,2); ?></td>
                    <td width="100">&nbsp;</td>
                    <td width="80" align="right" id="total_yarn_cost2"><? echo number_format($total_yarn_cost,2); ?></td>
                    <td width="80" align="right" id="total_yarn_cost_per"><? echo number_format($total_yarn_cost_percentage,2); ?></td>
                    <td width="100" align="right" id="total_purchase_cost"><? echo number_format($total_purchase_cost,2); ?></td>
                  
                    <td width="100" align="right" id="total_trim_cost"><? echo number_format($total_trim_cost,2); ?></td>
                    <td width="80" align="right" id="total_print_amount"><? echo number_format($total_print_amount,2); ?></td>
                    <td width="85" align="right" id="total_embroidery_amount"><? echo number_format($total_embroidery_amount,2); ?></td>
                    <td width="80" align="right" id="total_special_amount"><? echo number_format($total_special_amount,2); ?></td>
                    <td width="80" align="right" id="total_wash_cost"><? echo number_format($total_wash_cost,2); ?></td>
                    <td width="80" align="right" id="total_other_amount"><? echo number_format($total_other_amount,2); ?></td>
                    <td width="120" align="right" id="total_commercial_cost"><? echo number_format($total_commercial_cost,2); ?></td>
                    <td width="120" align="right" id="total_foreign_amount"><? echo number_format($total_foreign_amount,2); ?></td>
                    <td width="120" align="right" id="total_local_amount"><? echo number_format($total_local_amount,2); ?></td>
                    <td width="100" align="right" id="total_test_cost_amount"><? echo number_format($total_test_cost_amount,2); ?></td>
                    <td width="100" align="right" id="total_freight_amount"><? echo number_format($total_freight_amount,2); ?></td>
                    <td width="120" align="right" id="total_inspection_amount"><? echo number_format($total_inspection_amount,2); ?></td>
                    <td width="100" align="right" id="total_certificate_amount"><? echo number_format($total_certificate_amount,2); ?></td>
                    <td width="100" align="right" id="total_common_oh_amount"><? echo number_format($total_common_oh_amount,2); ?></td>
                    <td width="100" align="right" id="total_currier_amount"><? echo number_format($total_currier_amount,2); ?></td>
                    <td width="120">&nbsp;</td>
                    <td width="100" align="right" id="total_cm_amount"><? echo number_format($total_cm_amount,2); ?></td>
                    <td width="100" align="right" id="total_tot_cost"><? echo number_format($total_tot_cost,2); ?></td>
                    <td width="100" align="right" id="total_fabric_profit"><? echo number_format($total_fabric_profit,2);?></td>
                    <td width="100" align="right" id="total_profit_fab_percentage"><? echo number_format($total_profit_fab_percentage,2); ?></td>
                    
                    <td align="right" id="tot_yarn_cons"><? echo number_format($total_yarn_cons,2);?></td>
                </tr>
            </table>
            <table>
                <tr>
                	<?
					$total_fab_cost=number_format($total_fab_cost_amount,2,'.','');
					$total_trim_cost=number_format($total_trim_cost,2,'.','');
					$total_embelishment_cost=number_format($total_embelishment_cost,2,'.','');
					$total_commercial_cost=number_format($total_commercial_cost,2,'.','');
					$total_commssion=number_format($total_commssion,2,'.','');
					$total_testing_cost=number_format($total_testing_cost,2,'.','');
					$total_freight_cost=number_format($total_freight_cost,2,'.','');
					$total_cost_up=number_format($total_tot_cost,2,'.','');
					$total_cm_cost=number_format($total_cm_cost,2,'.','');
					$total_order_amount=number_format($total_order_amount,2,'.','');
					$total_inspection=number_format($total_inspection,2,'.','');
					$total_certificate_cost=number_format($total_certificate_cost,2,'.','');
					$total_common_oh=number_format($total_common_oh,2,'.','');
					$total_currier_cost=number_format($total_currier_cost,2,'.','');
					$total_fabric_profit_up=number_format($total_fab_profit,2,'.','');
					$total_expected_profit_up=number_format($total_expected_profit,2,'.','');
					
					$chart_data_qnty="Fabric Cost;".$total_fab_cost."\nTrimCost;".$total_trim_cost."\nEmbelishment Cost;".$total_embelishment_cost."\nCommercial Cost;".$total_commercial_cost."\nCommission Cost;".$total_commssion."\nTesting Cost;".$total_testing_cost."\nFreightCost;".$total_freight_cost."\nCM Cost;".$total_cm_cost."\nInspection Cost;".$total_inspection."\nCertificate Cost;".$total_certificate_cost."\nCommn OH Cost;".$total_common_oh."\nCurrier Cost;".$total_currier_cost."\n Profit/Loss;".$total_fabric_profit_up."\n";
					?>
                    <input type="hidden" id="graph_data" value="<? //echo substr($chart_data_qnty,0,-1); ?>"/>
                </tr>
            </table>
            <br>
            <a id="displayText" href="javascript:toggle();">Show Yarn Summary</a>
            <div style="width:600px; display:none" id="yarn_summary">
                <div id="data_panel2" align="center" style="width:500px">
                     <input type="button" value="Print Preview" class="formbutton" style="width:100px" name="print" id="print" onClick="new_window1(1)" />
                 </div>
                <table width="500">
                    <tr class="form_caption">
                        <td colspan="6" align="center"><strong>Yarn Cost Summary </strong></td>
                    </tr>
                </table>
                
                <br/> 
                <table class="rpt_table" width="590" cellpadding="0" cellspacing="0" border="1" rules="all">
                                <thead>
                                	<tr>
                                    	<th colspan="7">Yarn Cost Summary</th>
                                    </tr>
                                    <tr>
                                        <th width="30">SL</th>
                                        <th width="140">Composition</th>
                                        <th width="60">Yarn Count</th>
                                        <th width="80">Type</th>
                                        <th width="100">Req. Qty</th>
                                        <th width="70">Avg. Rate</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <?
                                $s=1; $tot_yarn_req_qnty=0; $tot_yarn_req_amnt=0;
                                foreach($yarn_description_data_arr as $count=>$count_value)
                                {
								foreach($count_value as $Composition=>$composition_value)
                                {
								foreach($composition_value as $percent=>$percent_value)
                                {
								foreach($percent_value as $type=>$type_value)
                                {
                                    if($s%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                    //$yarn_desc=explode("**",$key);
                                    
                                    $tot_yarn_req_qnty+=$type_value['qty']; 
                                    $tot_yarn_req_amnt+=$type_value['amount'];
                                    ?>
                                    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr3_<? echo $s; ?>','<? echo $bgcolor; ?>')" id="tr3_<? echo $s;?>">
                                        <td><? echo $s; ?></td>
                                        <td><div style="word-wrap:break-word; width:140px"><? echo $composition[$Composition]." ".$percent."%"; ?></div></td>
                                        <td><div style="word-wrap:break-word; width:60px"><? echo $yarn_count_library[$count]; ?></div></td>
                                        <td><div style="word-wrap:break-word; width:80px"><? echo $yarn_type[$type]; ?></div></td>
                                        <td align="right"><? echo number_format($type_value['qty'],2); ?></td>
                                        <td align="right"><? echo number_format($type_value['amount']/$type_value['qty'],2); ?></td>
                                        <td align="right"><? echo number_format($type_value['amount'],2); ?></td>
                                    </tr>
                                    <?	
                                    $s++;
								}
								}
								}
                                }
                                ?>
                                <tfoot>
                                    <th colspan="4" align="right">Total</th>
                                    <th align="right"><? echo number_format($tot_yarn_req_qnty,2); ?></th>
                                    <th align="right"><? echo number_format($tot_yarn_req_amnt/$tot_yarn_req_qnty,2); ?></th>
                                    <th align="right"><? echo number_format($tot_yarn_req_amnt,2); ?></th>
                                </tfoot>
                            </table>
					</div>
			</fieldset>
			<?
	
	} //1st button end
	else if($report_type==2) //Style Wise- Button
	{
			$wo_set="select a.job_no, d.printseq,d.finsmv_pcs,d.smv_set from wo_po_details_master a,wo_po_details_mas_set_details d where a.job_no=d.job_no and a.status_active=1 and
			a.is_deleted=0 $year_cond $job_no_cond $buyer_id_cond  $season_cond";
			$result_wo_set=sql_select($wo_set); 
			foreach($result_wo_set as $row)
			{
				$smv_job_arr[$row[csf('job_no')]]['link']+=$row[csf('printseq')];
				$smv_job_arr[$row[csf('job_no')]]['finsmv_pcs']+=$row[csf('finsmv_pcs')];
				$smv_job_arr[$row[csf('job_no')]]['knit']+=$row[csf('smv_set')];
				
			}
			$costing_per_arr=return_library_array( "select job_no,costing_per from wo_pre_cost_mst ", "job_no", "costing_per");
			
			$sql_budget="select a.job_no_prefix_num,a.set_break_down,a.order_uom,a.job_no, a.buyer_name, a.style_ref_no, b.is_confirmed, a.agent_name, a.avg_unit_price, 
			a.dealing_marchant,a.team_leader, a.gmts_item_id, a.total_set_qnty as ratio, b.plan_cut, b.id as po_id, b.po_number,b.insert_date, b.pub_shipment_date, b.po_received_date,
			b.po_quantity, b.po_total_price, b.unit_price, b.grouping, b.file_no, c.costing_date, c.remarks,c.costing_per
			from wo_po_details_master a, wo_po_break_down b LEFT JOIN wo_pre_cost_mst c on c.job_no=b.job_no_mst and c.entry_from=158 where a.job_no=b.job_no_mst and a.company_name='$company_name'  and a.status_active=1 and
			a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_quantity>0 $date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond 
		    $order_status_cond $season_cond $internal_ref_cond $file_no_cond";
			// echo $sql_budget;
			$result_sql_budget=sql_select($sql_budget); 
			$tot_rows_budget=count($result_sql_budget);
			$tot_po_amount=$tot_summ_yarn_cost=$tot_summ_purchase_cost=$tot_summ_trims_cost=$tot_summ_embl_cost=$tot_summ_commercial_cost=$tot_summ_commission_cost=$tot_summ_lab_test=$tot_summ_freight=$tot_summ_inspection=$tot_summ_certificate_pre_cost=$tot_summ_common_oh=$tot_summ_currier_pre_cost=$tot_summ_cm_cost=$tot_summ_expected_profit=$tot_summ_expected_profit_per=0;$all_po_id="";
			foreach($result_sql_budget as $row)
			{
				if($all_po_id=="") $all_po_id=$row[csf('po_id')];else $all_po_id.=",".$row[csf('po_id')];
				$job_wise_arr[$row[csf('job_no')]]['job_no']=$row[csf('job_no')];
				$job_wise_arr[$row[csf('job_no')]]['po_number'].=$row[csf('po_number')].',';
				$job_wise_arr[$row[csf('job_no')]]['po_id'].=$row[csf('po_id')].',';
				$job_wise_arr[$row[csf('job_no')]]['job_no_prefix']=$row[csf('job_no_prefix_num')];
				
				$job_wise_arr[$row[csf('job_no')]]['insert_date']=$row[csf('insert_date')];
				$job_wise_arr[$row[csf('job_no')]]['order_uom']=$row[csf('order_uom')];
				$job_wise_arr[$row[csf('job_no')]]['buyer_name']=$row[csf('buyer_name')];
				$job_wise_arr[$row[csf('job_no')]]['style_ref_no']=$row[csf('style_ref_no')];
				$job_wise_arr[$row[csf('job_no')]]['dealing_marchant']=$row[csf('dealing_marchant')];
				$job_wise_arr[$row[csf('job_no')]]['team_leader']=$row[csf('team_leader')];
				$job_wise_arr[$row[csf('job_no')]]['gmts_item_id']=$row[csf('gmts_item_id')];
				$job_wise_arr[$row[csf('job_no')]]['is_confirmed']=$row[csf('is_confirmed')];
				$job_wise_arr[$row[csf('job_no')]]['agent_name']=$row[csf('agent_name')];
				$job_wise_arr[$row[csf('job_no')]]['avg_unit_price']=$row[csf('avg_unit_price')];
				$job_wise_arr[$row[csf('job_no')]]['po_quantity']+=$row[csf('po_quantity')];
				$job_wise_arr[$row[csf('job_no')]]['po_quantity_pcs']+=$row[csf('po_quantity')]*$row[csf('ratio')];
				$job_wise_arr[$row[csf('job_no')]]['po_total_price']+=$row[csf('po_total_price')];
				$job_wise_arr[$row[csf('job_no')]]['unit_price']=$row[csf('unit_price')];
				$job_wise_arr[$row[csf('job_no')]]['ratio']=$row[csf('ratio')];
				$job_wise_arr[$row[csf('job_no')]]['costing_per']=$row[csf('costing_per')];
				$job_wise_arr[$row[csf('job_no')]]['plan_cut']+=$row[csf('plan_cut')];
				$job_wise_arr[$row[csf('job_no')]]['pub_shipment_date']=$row[csf('pub_shipment_date')];
				$job_wise_arr[$row[csf('job_no')]]['po_received_date']=$row[csf('po_received_date')];
				$job_wise_arr[$row[csf('job_no')]]['remarks']=$row[csf('remarks')];
				
				//$yarn_costing=$yarn_costing_arr[$row[csf('po_id')]];
				$job_wise_arr[$row[csf('job_no')]]['yarn_cost']+=$yarn_costing_arr[$row[csf('po_id')]];
				$job_wise_arr[$row[csf('job_no')]]['yarn_req_qty']=$yarn_req_qty_arr[$row[csf('po_id')]];
				
				
				$job_wise_arr[$row[csf('job_no')]]['fab_purchase_cost']=array_sum($fabric_costing_arr['sweater']['grey'][$row[csf('po_id')]])+array_sum($fabric_costing_arr['knit']['grey'][$row[csf('po_id')]])+array_sum($fabric_costing_arr['woven']['grey'][$row[csf('po_id')]]);
				
				$job_wise_arr[$row[csf('job_no')]]['trim_cost']+= $trims_costing_arr[$row[csf('po_id')]];
				$job_wise_arr[$row[csf('job_no')]]['print_amount']= $emblishment_costing_arr_name[$row[csf('po_id')]][1];
				$job_wise_arr[$row[csf('job_no')]]['embroidery_amount']= $emblishment_costing_arr_name[$row[csf('po_id')]][2];
				$job_wise_arr[$row[csf('job_no')]]['wash_amount']= $emblishment_costing_arr_name[$row[csf('po_id')]][3];
				$job_wise_arr[$row[csf('job_no')]]['special_amount']= $emblishment_costing_arr_name[$row[csf('po_id')]][4];
				$job_wise_arr[$row[csf('job_no')]]['other_amount']= $emblishment_costing_arr_name[$row[csf('po_id')]][5];
				
				$job_wise_arr[$row[csf('job_no')]]['foreign']= $commission_costing_arr[$row[csf('po_id')]][1];
				$job_wise_arr[$row[csf('job_no')]]['local']= $commission_costing_arr[$row[csf('po_id')]][2];
				$job_wise_arr[$row[csf('job_no')]]['lab_test']= $other_costing_arr[$row[csf('po_id')]]['lab_test'];
				$job_wise_arr[$row[csf('job_no')]]['freight']= $other_costing_arr[$row[csf('po_id')]]['freight'];
				$job_wise_arr[$row[csf('job_no')]]['inspection']= $other_costing_arr[$row[csf('po_id')]]['inspection'];
				$job_wise_arr[$row[csf('job_no')]]['certificate_pre_cost']= $other_costing_arr[$row[csf('po_id')]]['certificate_pre_cost'];
				$job_wise_arr[$row[csf('job_no')]]['common_oh']= $other_costing_arr[$row[csf('po_id')]]['common_oh'];
				$job_wise_arr[$row[csf('job_no')]]['currier_pre_cost']= $other_costing_arr[$row[csf('po_id')]]['currier_pre_cost'];
				$job_wise_arr[$row[csf('job_no')]]['cm_cost']= $other_costing_arr[$row[csf('po_id')]]['cm_cost'];
				$job_wise_arr[$row[csf('job_no')]]['commercial_cost']=$commercial_costing_arr[$row[csf('po_id')]];
				$job_wise_arr[$row[csf('job_no')]]['yarn_desc']=$yarn_des_data[$row[csf('po_id')]];
				// Summary Part
				$tot_po_amount+=$row[csf('po_quantity')]*$row[csf('unit_price')];
				//$tot_summ_yarn_cost+=$yarn_costing_arr[$row[csf('po_id')]];
				$tot_summ_purchase_cost+=array_sum($fabric_costing_arr['sweater']['grey'][$row[csf('po_id')]])+array_sum($fabric_costing_arr['knit']['grey'][$row[csf('po_id')]])+array_sum($fabric_costing_arr['woven']['grey'][$row[csf('po_id')]]);
				
				$tot_summ_trims_cost+=$trims_costing_arr[$row[csf('po_id')]];
				$tot_summ_embl_cost+=$emblishment_costing_arr_name[$row[csf('po_id')]][1]+$emblishment_costing_arr_name[$row[csf('po_id')]][2]+$emblishment_costing_arr_name[$row[csf('po_id')]][3]+$emblishment_costing_arr_name[$row[csf('po_id')]][4]+$emblishment_costing_arr_name[$row[csf('po_id')]][5];
				$tot_summ_commercial_cost+=$commercial_costing_arr[$row[csf('po_id')]];
				$tot_summ_commission_cost+=$commission_costing_arr[$row[csf('po_id')]][1]+$commission_costing_arr[$row[csf('po_id')]][2];
				
				$tot_summ_lab_test+=$other_costing_arr[$row[csf('po_id')]]['lab_test'];
				$tot_summ_freight+=$other_costing_arr[$row[csf('po_id')]]['freight'];
				$tot_summ_inspection+=$other_costing_arr[$row[csf('po_id')]]['inspection'];
				$tot_summ_certificate_pre_cost+=$other_costing_arr[$row[csf('po_id')]]['certificate_pre_cost'];
				$tot_summ_common_oh+=$other_costing_arr[$row[csf('po_id')]]['common_oh'];
				$tot_summ_currier_pre_cost+=$other_costing_arr[$row[csf('po_id')]]['currier_pre_cost'];	
				$tot_summ_cm_cost+=$other_costing_arr[$row[csf('po_id')]]['cm_cost'];	
				
				$costing_date=change_date_format($row[csf('costing_date')],'','',1);
				$tot_summ_expected_profit+=($asking_profit_arr[$costing_date]['asking_profit']*$tot_po_amount)/100;
				$tot_summ_expected_profit_per+=$asking_profit_arr[$costing_date]['asking_profit'];
				
						
			}
			//echo $tot_summ_purchase_cost.'DD';
			$total_summ_cost=$tot_summ_yarn_cost+$tot_summ_purchase_cost+$tot_summ_trims_cost+$tot_summ_embl_cost+$tot_summ_commercial_cost+$tot_summ_commission_cost+$tot_summ_lab_test+$tot_summ_freight+$tot_summ_inspection+$tot_summ_certificate_pre_cost+$tot_summ_common_oh+$tot_summ_currier_pre_cost+$tot_summ_cm_cost;
			$total_summ_profit=$tot_po_amount-$total_summ_cost;
			$tot_summ_expect_variance=$total_summ_profit-$tot_summ_expected_profit_per;	
			$poIds=chop($all_po_id,','); $po_cond_for_in=""; 
		$po_ids=count(array_unique(explode(",",$all_po_id)));
		if($db_type==2 && $po_ids>1000)
		{
			$po_cond_for_in=" and (";
			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$po_cond_for_in.=" d.id in($ids) or"; 
			}
			$po_cond_for_in=chop($po_cond_for_in,'or ');
			$po_cond_for_in.=")";
		}
		else
		{
			$po_cond_for_in=" and d.id in($poIds)";
		}
		
	$sql_po="select a.job_no,d.id as po_id, c.item_number_id, c.color_number_id, c.order_quantity, c.plan_cut_qnty from wo_po_details_master a, wo_po_break_down d, wo_po_color_size_breakdown c where a.job_no=d.job_no_mst and a.job_no=c.job_no_mst and d.id=c.po_break_down_id  and a.is_deleted=0 and a.status_active=1 and d.is_deleted=0 and d.status_active=1 and c.is_deleted=0 and c.status_active=1 $po_cond_for_in order by d.id ASC";
	$sql_po_data=sql_select($sql_po); $gmts_item_color_qty_arr=array();
	foreach($sql_po_data as $row)
	{
		$gmts_item_color_qty_arr[$row[csf('job_no')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
	}
	unset($sql_po_data);
	
	
	if($db_type==2)
	{
		$po_grp="listagg(CAST(d.id as VARCHAR(4000)),',') within group (order by d.id) as po_ids";
		
	}
	else
	{
		$po_grp="group_concat(d.id) as po_ids";
		
	}
 $data_sql="select a.id, a.fabric_cost_dtls_id, a.job_no, a.count_id, a.copm_one_id, a.percent_one, a.copm_two_id, a.percent_two, a.type_id, a.cons_ratio, a.cons_qnty, a.rate, a.amount, a.avg_cons_qnty, a.supplier_id, a.color, a.consdznlbs, a.rate_dzn, 
	b.item_number_id, b.body_part_id, b.fabric_description, b.color_type_id, b.uom, c.color_number_id, c.stripe_color, c.measurement ,$po_grp
	from wo_pre_cost_fab_yarn_cost_dtls a, wo_pre_cost_fabric_cost_dtls b, wo_pre_stripe_color c,wo_po_break_down d where a.fabric_cost_dtls_id=b.id and b.id=c.pre_cost_fabric_cost_dtls_id and a.color=c.stripe_color and d.job_no_mst=a.job_no and d.job_no_mst=b.job_no  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $po_cond_for_in group by a.id, a.fabric_cost_dtls_id, a.job_no, a.count_id, a.copm_one_id, a.percent_one, a.copm_two_id, a.percent_two, a.type_id, a.cons_ratio, a.cons_qnty, a.rate, a.amount, a.avg_cons_qnty, a.supplier_id, a.color, a.consdznlbs, a.rate_dzn, 
	b.item_number_id, b.body_part_id, b.fabric_description, b.color_type_id, b.uom, c.color_number_id, c.stripe_color, c.measurement";
	$data_arr_yarn=sql_select($data_sql);
	foreach($data_arr_yarn as $yarn_row)
	{
		$costing_per=$costing_per_arr[$yarn_row[csf('job_no')]];
		// echo $costing_per."===><br>";
		 if($costing_per==1){
            $order_price_per_dzn=12;
            $costing_for=" DZN";
        }
        else if($costing_per==2){
            $order_price_per_dzn=1;
            $costing_for=" PCS";
        }
        else if($costing_per==3){
            $order_price_per_dzn=24;
            $costing_for=" 2 DZN";
        }
        else if($costing_per==4){
            $order_price_per_dzn=36;
            $costing_for=" 3 DZN";
        }
        else if($costing_per==5){
            $order_price_per_dzn=48;
            $costing_for=" 4 DZN";
        }
		// $poQty=0; $yarn_req_kg=0; $yarn_req_lbs=0; $amount_req=0;
		$poQty=$gmts_item_color_qty_arr[$yarn_row[csf('job_no')]][$yarn_row[csf('item_number_id')]][$yarn_row[csf('color_number_id')]];
		// echo  $poQty.'='.$yarn_row[csf('measurement')]."=".$order_price_per_dzn."<br>";
		$yarn_req_kg=($yarn_row[csf('measurement')]/$order_price_per_dzn)*$poQty;
		$yarn_req_lbs=$yarn_req_kg*2.20462;
		$amount_req=$yarn_req_lbs*$yarn_row[csf('rate')];
		$yarn_job_wise_arr[$yarn_row[csf('job_no')]]['amount']+=$amount_req;
		$yarn_job_wise_arr[$yarn_row[csf('job_no')]]['req_qty']+=$yarn_req_lbs;
		$yarn_type_count_wise_arr[$yarn_row[csf('job_no')]][$yarn_row[csf('count_id')]][$yarn_row[csf('copm_one_id')]][$yarn_row[csf('percent_one')]][$yarn_row[csf('type_id')]]['req_qty']+=$yarn_req_lbs;
		$yarn_type_count_wise_arr[$yarn_row[csf('job_no')]][$yarn_row[csf('count_id')]][$yarn_row[csf('copm_one_id')]][$yarn_row[csf('percent_one')]][$yarn_row[csf('type_id')]]['amount']+=$amount_req;
		$tot_summ_yarn_cost+=$amount_req;
		//$summary_data[yarn_cost_job]+=$amount_req;
	}
	// print_r($yarn_job_wise_arr);
				
			ob_start();
			?>
		<script>
			function toggle() 
				{
					var ele = document.getElementById("yarn_summary");
					//alert(ele);
					var text = document.getElementById("displayText");
					if(ele.style.display!= "none") 
					{
						ele.style.display = "none";
						text.innerHTML = "Show Yarn Summary";
					}
					else 
					{
						ele.style.display = "block";
						text.innerHTML = "Hide Yarn Summary";
					}
				} 
				
            </script>
			<div style="width:900px;" align="left">
                <table width="900" cellpadding="0" cellspacing="2" border="0">
                    <tr>
                        <td width="350" align="left">
                            <table width="350" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="2">
                                <thead align="center">
                                	<tr>
                                    	<th colspan="4">Order Wise Budget Cost Summary</th>
                                    </tr>
                                	<tr>
                                        <th>SL</th><th>Particulars</th><th>Amount</th><th>%</th>
                                    </tr>
                                </thead>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td width="20">1</td>
                                    <td width="130">Yarn Cost</td>
                                    <td width="120" align="right" id=""><? echo number_format($tot_summ_yarn_cost,2);?></td>
                                    <td width="80" align="right" id=""><? echo number_format(($tot_summ_yarn_cost/$tot_po_amount)*100,2);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>2</td>
                                    <td>Fabric Purchase</td>
                                    <td align="right" id=""><? echo number_format($tot_summ_purchase_cost,2);?></td>
                                    <td align="right" id=""><? echo number_format(($tot_summ_purchase_cost/$tot_po_amount)*100,2);?></td>
                                </tr>
                              
                                <tr bgcolor="#CCCCCC">
                                    <td colspan="2"><strong>Total Material & Service Cost</strong></td>
                                    <td align="right" id="tot_matr_ser_cost"><? echo number_format(($tot_summ_yarn_cost+$tot_summ_purchase_cost),2);?></td>
                                    <td align="right" id="tot_matr_ser_per"><? echo number_format((($tot_summ_yarn_cost+$tot_summ_purchase_cost)/$tot_po_amount)*100,2);?></td>
                                </tr>
                                <tr bgcolor="<?  echo $style1; ?>">
                                    <td>7</td>
                                    <td>Trims Cost</td>
                                    <td align="right" id=""><? echo number_format($tot_summ_trims_cost,2);?></td>
                                    <td align="right" id=""><? echo number_format(($tot_summ_trims_cost/$tot_po_amount)*100,2);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>8</td>
                                    <td>Print/ Emb. /Wash Cost</td>
                                    <td align="right" id="embelishment_cost"><? echo number_format($tot_summ_embl_cost,2);?></td>
                                    <td align="right" id="embelishment_cost_per"><? echo number_format(($tot_summ_embl_cost/$tot_po_amount)*100,2);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>9</td>
                                    <td>Commercial Cost</td>
                                    <td align="right" id="commercial_cost"><? echo number_format($tot_summ_commercial_cost,2);?></td>
                                    <td align="right" id="commercial_cost_per"><? echo number_format(($tot_summ_commercial_cost/$tot_po_amount)*100,2);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>10</td>
                                    <td>Commision Cost</td>
                                    <td align="right" id=""><? echo number_format($tot_summ_commission_cost,2);?></td>
                                    <td align="right" id=""><? echo number_format(($tot_summ_commission_cost/$tot_po_amount)*100,2);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>11</td>
                                    <td>Testing Cost</td>
                                    <td align="right" id="testing_cost"><? echo number_format($tot_summ_lab_test,2);?></td>
                                    <td align="right" id="testing_cost_per"><? echo number_format(($tot_summ_lab_test/$tot_po_amount)*100,2);?></td>
                                </tr>
                                    <tr bgcolor="<? echo $style1; ?>">
                                    <td>12</td>
                                    <td>Freight Cost</td>
                                    <td align="right" id="freight_cost"><? echo number_format($tot_summ_freight,2);?></td>
                                    <td align="right" id="freight_cost_per"><? echo number_format(($tot_summ_freight/$tot_po_amount)*100,2);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td width="20">13</td>
                                    <td width="100">Inspection Cost</td>
                                    <td align="right" id="inspection_cost"><? echo number_format($tot_summ_inspection,2);?></td>
                                    <td align="right" id="inspection_cost_per"><? echo number_format(($tot_summ_inspection/$tot_po_amount)*100,2);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>14</td>
                                    <td>Certificate Cost</td>
                                    <td align="right" id="certificate_cost"><? echo number_format($tot_summ_certificate_pre_cost,2);?></td>
                                    <td align="right" id="certificate_cost_percent"><? echo number_format(($tot_summ_certificate_pre_cost/$tot_po_amount)*100,2);?></td>
                                </tr>
                                    <tr bgcolor="<? echo $style; ?>">
                                    <td>15</td>
                                    <td>Operating Exp.</td>
                                    <td align="right" id="commn_cost"><? echo number_format($tot_summ_common_oh,2);?></td>
                                    <td align="right" id="commn_cost_per"><? echo number_format(($tot_summ_common_oh/$tot_po_amount)*100,2);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>16</td>
                                    <td>Courier Cost</td>
                                    <td align="right" id="courier_cost"><? echo number_format($tot_summ_currier_pre_cost,2);?></td>
                                    <td align="right" id="courier_cost_per"><? echo number_format(($tot_summ_currier_pre_cost/$tot_po_amount)*100,2);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>17</td>
                                    <td>CM Cost</td>
                                    <td align="right" id="cm_cost"><? echo number_format($tot_summ_cm_cost,2);?></td>
                                    <td align="right" id="cm_cost_per"><? echo number_format(($tot_summ_cm_cost/$tot_po_amount)*100,2);?></td>
                                </tr>
                                <tr bgcolor="#999999">
                                    <td>18</td>
                                    <td>Total Cost</td>
                                    <td align="right" id="cost_cost"><? echo number_format($total_summ_cost,2);?></td>
                                    <td align="right" id="cost_cost_per"><? echo number_format(($total_summ_cost/$tot_po_amount)*100,2);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>19</td>
                                    <td>Total Order Value</td>
                                    <td align="right" id="order_id"><? echo number_format($tot_po_amount,2);?></td>
                                    <td align="right" id="order_percent"><? echo number_format(($tot_po_amount/$tot_po_amount)*100,2);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>20</td>
                                    <td>Profit/Loss </td>
                                    <td align="right" id="fab_profit_id"><?  echo number_format($total_summ_profit,2);?></td>
                                    <td align="right" id="profit_fab_percentage"><? echo number_format(($total_summ_profit/$tot_po_amount)*100,2);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>21</td>
                                    <td>Expected Profit <div id="expt_percent"></div></td>
                                    <td align="right" id="expected_id"><? echo number_format($tot_summ_expected_profit,2);?></td>
                                    <td align="right" id="profit_expt_fab_percentage"><? echo number_format(($tot_summ_expected_profit/$tot_po_amount)*100,2);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>22</td>
                                    <td>Expt.Profit Variance </td>
                                    <td align="right" id="expt_p_variance_id"><? echo number_format($tot_summ_expect_variance,2);?></td>
                                    <td align="right" id="expt_p_percent"><? echo number_format(($tot_summ_expect_variance/$tot_po_amount)*100,2);?></td>
                                </tr>
                            </table>
                        </td>
                       
                    </tr>
                </table>
            </div>
			<br>
			<div>
			
			<h3 align="left" id="accordion_h2" style="width:4290px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel2', '')"> -</h3>
        	<fieldset style="width:100%;" id="content_search_panel2">	
            <table width="4290">
                    <tr class="form_caption">
                        <td colspan="46" align="center"><strong><? echo $report_title;?></strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="46" align="center"><strong><? echo $company_library[$company_name]; ?></strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td align="left"><strong>Details Report </strong></td>
                    </tr>
            </table>
               <? $asking_profit_head=$asking_profit_arr[$company_name]['asking_profit']; 
			   
			   		if(str_replace("'","",$cbo_search_date)==1) $caption="Ship. Date";
					else if(str_replace("'","",$cbo_search_date)==2) $caption="PO Recv. Date";
					else $caption="PO Insert Date";
			   ?>
            <table  class="rpt_table" width="4290" cellpadding="0" cellspacing="0" border="1" rules="all">
               <thead>
                    <tr>
                        <th width="40" rowspan="2">SL</th>
                        <th width="70" rowspan="2">Buyer</th>
                        <th width="70" rowspan="2">Job No</th>
                      
                        <th width="100" rowspan="2">Approve Status</th>
                        <th width="80" rowspan="2">File No</th>
                        <th width="80" rowspan="2">Internal Ref:</th>
                        <th width="100" rowspan="2">Order Status</th>
                        <th width="110" rowspan="2">Style</th>
                        <th width="110" rowspan="2">Item Name</th>
                        <th width="110" rowspan="2">Team Leader</th>
						<th width="110" rowspan="2">Team Member</th>
                        <th width="70" rowspan="2"><? echo $caption; ?></th>
                        <th width="90" rowspan="2">Order Qty</th>
						<th width="50" rowspan="2">Uom</th>
                        <th width="90" rowspan="2">Avg Unit Price</th>
                        <th width="100" rowspan="2">Style Value</th>
                        <th colspan="4">Yarn Cost</th>
                        <th width="100" rowspan="2">Trim Cost</th>
                        <th colspan="5">Embell. Cost</th>
                        <th width="120" rowspan="2">Commercial Cost</th>
                        <th colspan="2">Commission</th>
                        <th width="100" rowspan="2">Testing Cost</th>
                        <th width="100" rowspan="2">Freight Cost</th>
                        <th width="120" rowspan="2">Inspection Cost</th>
                        <th width="100" rowspan="2">Certificate Cost</th>
                        <th width="100" rowspan="2">Operating Exp</th>
                        <th width="100" rowspan="2">Courier Cost</th>
                        <th width="120" rowspan="2">CM/DZN</th>
                        <th width="100" rowspan="2">Total CM Cost</th>
                        <th width="100" rowspan="2">Total Cost</th>
                        <th width="100" rowspan="2">Profit/Loss</th>
                        <th width="100" rowspan="2">Profit/Loss %</th>
						
                        <th width="80" rowspan="2">Yarn Cons</th>
						<th colspan="4">SMV</th>
                        <th width="100" rowspan="2">Remarks</th>
                    </tr>
                    <tr>
                        <th width="100">Avg Yarn Rate</th>
                        <th width="80">Yarn Cost</th>
                        <th width="80">Yarn Cost %</th>
                        <th width="100">Fabric Purchase</th>
						
                       
                        <th width="80">Printing</th>
                        <th width="85">Embroidery</th>
                        <th width="80">Special Works</th>
                        <th width="80">Wash Cost</th>
                        <th width="80">GMT Dyeing</th>
                        <th width="120">Foreign</th>
                        <th width="120">Local</th>
						
						<th width="80">Knitting</th>
                        <th width="80">Linking</th>
                        <th width="80">Finishing</th>
                        <th width="80">Total</th>
                    </tr>
				</thead>
                
            </table>
            <div style="width:4310px; max-height:400px; overflow-y:scroll" id="scroll_body">
            <table class="rpt_table" width="4290" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
			<? 
            $i=1; $total_order_qty=0;  $total_yarn_dyeing_cost=0; $total_yarn_cost=0; $total_order_value=0;$total_purchase_cost=0; $grand_tot_trims_cost=0; $total_fabric_dyeing_cost=0; $total_knitting_cost=0; $total_heat_setting_cost=0;$total_finishing_cost=0; $total_washing_cost=0; $fabric_dyeing_cost_dzn=0; $other_cost=0;
$all_over_print_cost=0;$total_trim_cost=0;$total_commercial_cost=0;$total_yarn_cons=$total_smv_knit=$total_smv_link=$total_smv_finsmv_pcs=$total_tot_smv=0;
		
			foreach($job_wise_arr as $job_id=>$row )
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
				$po_id=rtrim($row[('po_id')],',');
				$po_id=implode(",",array_unique(explode(",",$po_id)));
				$po_number=rtrim($row[('po_number')],',');
				$po_numbers=implode(",",array_unique(explode(",",$po_number)));
				
				if(str_replace("'","",$cbo_search_date)==1)
				{
					$ship_po_recv_date=change_date_format($row[('pub_shipment_date')]);
				}
				else if(str_replace("'","",$cbo_search_date)==2)
				{
					$ship_po_recv_date=change_date_format($row[('po_received_date')]);
				}
				else if(str_replace("'","",$cbo_search_date)==3)
				{
					$insert_date=explode(" ",$row[('insert_date')]);
					$ship_po_recv_date=change_date_format($insert_date[0]);
				}
				$approve_status=$approve_arr[$job_id];
				if($approve_status==1) $is_approve="Approved"; else $is_approve="No";
				$dzn_qnty=0;
				$costing_per_id=$costing_per_arr[$row[('job_no')]];
				if($costing_per_id==1) $dzn_qnty=12;
				else if($costing_per_id==3) $dzn_qnty=12*2;
				else if($costing_per_id==4) $dzn_qnty=12*3;
				else if($costing_per_id==5) $dzn_qnty=12*4;
				else $dzn_qnty=1;
				
				$dzn_qnty=$dzn_qnty*$row[('ratio')];
				$order_qty_pcs=$row[('po_quantity')]*$row[('ratio')];
				$dzn_qnty_yarn=$dzn_qnty*$row[('ratio')];
				
				$plan_cut_qnty=$row[('plan_cut')]*$row[('ratio')];
				$order_value=$row[('po_total_price')];
				$plancut_value=$plan_cut_qnty*$row[('avg_unit_price')];
				
				//$total_order_amount+=$order_value; 
				$total_plancut_amount+=$plancut_value;
				$yarn_descrip_data=$yarn_des_data_job[$job_id];//$row[('yarn_desc')];
						$qnty=0; $amount=0;
						foreach($yarn_descrip_data as $count=>$count_value)
						{
							foreach($count_value as $Composition=>$composition_value)
							{
								foreach($composition_value as $percent=>$percent_value)
								{	
									foreach($percent_value as $type=>$qty_amt)
									{
										$count_id=$count;//$yarnRow[0];
										$copm_one_id=$Composition;//$yarnRow[1];
										$percent_one=$percent;//$yarnRow[2];
										$type_id=$type;//$yarnRow[5];
										$qnty=$qty_amt['qty'];
										$amount=$qty_amt['amount'];
										
										$yarn_description_data_arr[$count_id][$copm_one_id][$percent_one][$type_id]['qty']+=$yarn_type_count_wise_arr[$job_id][$count_id][$copm_one_id][$percent_one][$type_id]['req_qty'];
										$yarn_description_data_arr[$count_id][$copm_one_id][$percent_one][$type_id]['amount']+=$yarn_type_count_wise_arr[$job_id][$count_id][$copm_one_id][$percent_one][$type_id]['amount'];
									}
								}
							}
						} 
				?>
				 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                     <td width="40"><? echo $i; ?></td>
                     <td width="70"><p><? echo $buyer_library[$row[('buyer_name')]]; ?></p></td>
                     <td width="70"><p><? echo $row[('job_no_prefix')]; ?></p></td>
                    
                     <td width="100"><p><? echo $is_approve; ?></p></td>
                     <td width="80"><p><? echo $row[('file_no')]; ?></p></td>
                     <td width="80"><p><? echo $row[('grouping')]; ?></p></td>
                     <td width="100"><p><? echo  $order_status[$row[('is_confirmed')]]; ?></p></td>
                     <td width="110"><p><? echo $row[('style_ref_no')]; ?></p></td>
                     <td width="110"><div style="width:110px; word-wrap:break-word;"><? $gmts_item=''; $gmts_item_id=explode(",",$row[('gmts_item_id')]);
						foreach($gmts_item_id as $item_id)
						{
							if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=", ".$garments_item[$item_id];
						}
						echo $gmts_item; ?></div></td>
                     <td width="110"><p><? echo $team_leader_arr[$row[('team_leader')]]; ?></p></td>
					 <td width="110"><p><? echo $team_member_arr[$row[('dealing_marchant')]]; ?></p></td>
                     <td width="70"><p><? echo $ship_po_recv_date; ?></p></td>
                     <td width="90" align="right"><p><? echo number_format($order_qty_pcs,2); ?></p></td>
					 <td width="50" align="right"><p><? echo $unit_of_measurement[$row[('order_uom')]]; ?></p></td>
                     <td width="90" align="right"><p><? echo number_format($row[('avg_unit_price')],4); ?></p></td>
                     <td width="100" align="right"><p><? echo number_format($row[('po_total_price')],2); ?></p></td>
                     <? 
						$commercial_cost=$row[('commercial_cost')];
						$yarn_costing=$yarn_job_wise_arr[$job_id]['amount'];
						$yarn_req_qty=$yarn_job_wise_arr[$job_id]['req_qty'];//$row[('yarn_cost')];
						$yarn_cost_percent=($yarn_costing/$order_value)*100;
						$avg_rate=$yarn_costing/$yarn_req_qty;
						
						//$fab_purchase_knit=array_sum($fabric_costing_arr['knit']['grey'][$po_id]);
						//$fab_purchase_woven=array_sum($fabric_costing_arr['woven']['grey'][$po_id]);
						//echo  $fab_purchase_knit.'='. $fab_purchase_woven.'<br>';
						$fab_purchase=$row[('fab_purchase_cost')];
						$yarn_cons=($yarn_req_qty/$plan_cut_qnty)*$dzn_qnty;
						// $row[csf('order_uom')]
						
						$trim_amount= $row[('trim_cost')];
						$print_amount=$row[('print_amount')];
						$embroidery_amount=$row[('embroidery_amount')];
						$special_amount=$row[('special_amount')];
						$wash_cost=$row[('wash_amount')];
						$other_amount=$row[('other_amount')];
						$foreign=$row[('foreign')];
						$local=$row[('local')];
						$test_cost=$row[('lab_test')];
						$freight_cost= $row[('freight')];
						$inspection=$row[('inspection')];
						$certificate_cost=$row[('certificate_pre_cost')];
						$common_oh=$row[('common_oh')];
						$currier_cost=$row[('currier_pre_cost')];
						$cm_cost=$row[('cm_cost')];
						$cm_cost_dzn=($cm_cost/$order_qty_pcs)*$dzn_qnty;
						
						$total_cost=$yarn_costing+$fab_purchase+$trim_amount+$test_cost+$print_amount+$embroidery_amount+$special_amount+$other_amount+$wash_cost+$commercial_cost+$foreign+$local+$freight_cost+$inspection+$certificate_cost+$common_oh+$currier_cost+$cm_cost;
						
						$total_profit=$order_value-$total_cost;
						$total_profit_percentage2=$total_profit/$order_value*100; 
						$expected_profit=$asking_profit_arr[$row[('company_name')]]['asking_profit']*$order_value/100;
						$expect_variance=$total_profit-$expected_profit;
						
						if($yarn_costing<=0) $color_yarn="red"; else $color_yarn="";	
						if($commercial_cost<=0) $color_com="red"; else $color_com="";	
					 ?>
                     <td width="100" align="right"><a href="##" onClick="generate_pre_cost_report('<? echo $po_id; ?>','<? echo $job_id;?>','<? echo $company_name; ?>','<? echo $row[('buyer_name')]; ?>','<? echo $row[('style_ref_no')]; ?>','precost_yarn_detail')"><? echo number_format($avg_rate,2); ?></a></td>
                     <td width="80" align="right" title="Req=<? echo $yarn_req_qty;?>" bgcolor="<? echo $color_yarn; ?>"><?  echo number_format($yarn_costing,2);   //$yarn_costing=$yarn->getOrderWiseYarnAmount($row[csf('po_id')]);?></td>
                     <td width="80" align="right" title="Yarn Costing/Order Value*100"><? echo number_format($yarn_cost_percent,2); ?></td>
                     <td width="100" align="right"><a href="##" onClick="generate_precost_fab_purchase_detail('<? echo $po_id; ?>','<? echo $row[('job_no')];?>','<? echo $company_name; ?>','<? echo $row[('buyer_name')]; ?>','<? echo $fab_source_id; ?>','fab_purchase_detail')"><? echo number_format($fab_purchase,2); ?></a></td>
                    
				<?
					//echo  $total_cost;
					$total_print_amount+=$print_amount;
					$total_embroidery_amount+=$embroidery_amount;
					$total_special_amount+=$special_amount;
					$total_other_amount+=$other_amount;
					$total_wash_cost+=$wash_cost;
					
					$total_foreign_amount+=$foreign;
					$total_local_amount+=$local;
					$total_test_cost_amount+=$test_cost;
					$total_freight_amount+=$freight_cost;
					$total_inspection_amount+=$inspection;
					$total_certificate_amount+=$certificate_cost;
					
					$total_common_oh_amount+=$common_oh;
					$total_currier_amount+=$currier_cost;
					$total_cm_amount+=$cm_cost;
					$max_profit=$asking_profit_arr[$row[('company_name')]]['max_profit'];
					$company_asking=$asking_profit_arr[$row[('company_name')]]['asking_profit'];
					
					if($trim_amount<=0) $color_trim="red"; else $color_trim="";	
					if($cm_cost<=0) $color="red"; else $color="";
					?>
                     <td width="100" align="right" bgcolor="<? echo $color_trim; ?>"><a href="##" onClick="generate_precost_trim_cost_detail('<? echo $po_id; ?>','<? echo $row[('job_no')];?>','<? echo $company_name; ?>','<? echo $row[('buyer_name')]; ?>','<? echo $row[('style_ref_no')]; ?>','trim_cost_detail')"><? echo number_format($trim_amount,2); ?></a><? //echo number_format($trim_amount,2); ?></td>
                     <td width="80" align="right"><a href="##" onClick="generate_precost_embell_cost_detail('<? echo $po_id; ?>','<? echo $row[('job_no')];?>','<? echo $company_name; ?>','<? echo $row[('buyer_name')]; ?>','<? echo $row[('style_ref_no')]; ?>','embl_cost_detail',1)"><? echo number_format($print_amount,2); ?></a></td>
                     <td width="85" align="right"><a href="##" onClick="generate_precost_embell_cost_detail('<? echo $po_id; ?>','<? echo $row[('job_no')];?>','<? echo $company_name; ?>','<? echo $row[('buyer_name')]; ?>','<? echo $row[('style_ref_no')]; ?>','embl_cost_detail',2)"><? echo number_format($embroidery_amount,2); ?></a></td>
                     <td width="80" align="right"><? echo number_format($special_amount,2); ?></td>
                     <td width="80" align="right"><a href="##" onClick="generate_precost_embell_cost_detail('<? echo $po_id; ?>','<? echo $row[('job_no')];?>','<? echo $company_name; ?>','<? echo $row[('buyer_name')]; ?>','<? echo $row[('style_ref_no')]; ?>','embl_cost_detail',3)"><? echo number_format($wash_cost,2); ?></a></td>
                     <td width="80" align="right"><? echo number_format($other_amount,2); ?></td>
                     <td width="120" align="right" bgcolor="<? echo $color_com; ?>"><? echo number_format($commercial_cost,2); ?></td>
                     <td width="120" align="right"><? echo number_format($foreign,2) ?></td>
                     <td width="120" align="right"><? echo number_format($local,2) ?></td>
                     <td width="100" align="right"><? echo number_format($test_cost,2);?></td>
                     <td width="100" align="right"><? echo number_format($freight_cost,2); ?></td>
                     <td width="120" align="right"><? echo number_format($inspection,2);?></td>
                     <td width="100" align="right"><? echo number_format($certificate_cost,2); ?></td>
                     <td width="100" align="right"><? echo number_format($common_oh,2); ?></td>
                     <td width="100" align="right"><? echo number_format($currier_cost,2);?></td>
                     <td width="120" align="right"><? echo number_format($cm_cost_dzn,2);?></td>
                     <td width="100" align="right" bgcolor="<? echo $color; ?>"><? echo number_format($cm_cost,2);?></td>
                     <td width="100" align="right"><? echo number_format($total_cost,2); ?></td>
                    <?
						if($total_profit_percentage2<=0 ) $color_pl="red";
						else if($total_profit_percentage2>$max_profit) $color_pl="yellow";	
						else if($total_profit_percentage2<=$max_profit) $color_pl="green";	
						else $color_pl="";	
						
						$smv_link=$smv_job_arr[$job_id]['link']*$order_qty_pcs;
						$smv_finsmv_pcs=$smv_job_arr[$job_id]['finsmv_pcs']*$order_qty_pcs;
						$smv_knit=$smv_job_arr[$job_id]['knit']*$order_qty_pcs;
						$tot_smv=$smv_link+$smv_finsmv_pcs+$smv_knit;
					?>
                     <td width="100" align="right" bgcolor="<? echo $color_pl; ?>"><? echo number_format($total_profit,2); ?></td>
                     <td width="100" align="right"><? echo number_format($total_profit_percentage2,2); ?></td>
                     <td width="" align="right"><? echo number_format($yarn_cons,2);?></td>
					 
					<td width="80" align="right" title="SMV*Style Qty Pcs"><? echo number_format($smv_knit,2);?></td>
					<td width="80" align="right"><? echo number_format($smv_link,2);?></td>
					<td width="80" align="right"><? echo number_format($smv_finsmv_pcs,2);?></td>
					<td width="80" align="right"><? echo number_format($tot_smv,2);?></td>
                    <td width="100" style="word-break:break-all"><? echo $row[('remarks')]; ?>&nbsp;</td>
                  </tr> 
                <?
				$total_order_qty+=$order_qty_pcs;
				$total_order_amount+=$order_value;
				$total_plan_cut_qty+=$plan_cut_qnty;
				$total_yarn_dyeing_cost+=$yarn_dyeing_cost;
				$total_yarn_cons+=$yarn_cons;
				$total_yarn_cost+=$yarn_costing;
				$total_purchase_cost+=$fab_purchase;
			
				$total_trim_cost+=$trim_amount;
				$total_commercial_cost+=$commercial_cost;
				$total_fab_cost_amount=$total_yarn_cost+$total_purchase_cost;
				
				//echo $total_cost.'<br>';
				//$total_fab_cost_amount2+=$total_fab_cost_amount;
				$total_embelishment_cost+=$print_amount+$embroidery_amount+$special_amount+$other_amount+$wash_cost;
				$total_commssion+=$foreign+$local;
				$total_testing_cost+=$test_cost;
				$total_freight_cost+=$freight_cost;
				$total_cm_cost+=$cm_cost;
				$total_tot_cost+=$total_cost;
				$total_inspection+=$inspection;
				$total_certificate_cost+=$certificate_cost;
				$total_common_oh+=$common_oh;
				$total_currier_cost+=$currier_cost;
				$total_fabric_profit+=$total_profit;
				
				$total_profit_fab_percentage_up+=$total_profit_percentage2;
				
				$total_smv_knit+=$smv_knit;
				$total_smv_link+=$smv_link;
				$total_smv_finsmv_pcs+=$smv_finsmv_pcs;
				$total_tot_smv+=$tot_smv;
				//echo $total_fab_cost_amount;
				$i++;
			}
			$total_profit_fab_percentage=$total_fab_profit/$total_order_amount*100;
			$total_yarn_cost_percentage=$total_yarn_cost/$total_order_amount*100;
			//$total_purchase_cost_percentage=$total_purchase_cost/$total_order_amount*100;
			?>
            	</table>
			</div>
			  <table class="tbl_bottom" width="4290" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
                <tr>
                    <td width="40">&nbsp;</td>
                    <td width="70">&nbsp;</td>
                    <td width="70">&nbsp;</td>
                    
                    <td width="100">&nbsp;</td>
                    <td width="80">&nbsp;</td>
                    <td width="80">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="110">&nbsp;</td>
                    <td width="110">&nbsp;</td>
                    <td width="110">&nbsp;</td>
					<td width="110">&nbsp;</td>
                    <td width="70">Total</td>
                    <td width="90" align="right" id="total_order_qnty"><? echo number_format($total_order_qty,2); ?></td>
                    <td width="50">&nbsp;</td>
					<td width="90">&nbsp;</td>
                    <td width="100" align="right" id="total_order_amount2"><? echo number_format($total_order_amount,2); ?></td>
                    <td width="100">&nbsp;</td>
                    <td width="80" align="right" id="total_yarn_cost2"><? echo number_format($total_yarn_cost,2); ?></td>
                    <td width="80" align="right" id="total_yarn_cost_per"><? echo number_format($total_yarn_cost_percentage,2); ?></td>
                    <td width="100" align="right" id="total_purchase_cost"><? echo number_format($total_purchase_cost,2); ?></td>
                  
                    <td width="100" align="right" id="total_trim_cost"><? echo number_format($total_trim_cost,2); ?></td>
                    <td width="80" align="right" id="total_print_amount"><? echo number_format($total_print_amount,2); ?></td>
                    <td width="85" align="right" id="total_embroidery_amount"><? echo number_format($total_embroidery_amount,2); ?></td>
                    <td width="80" align="right" id="total_special_amount"><? echo number_format($total_special_amount,2); ?></td>
                    <td width="80" align="right" id="total_wash_cost"><? echo number_format($total_wash_cost,2); ?></td>
                    <td width="80" align="right" id="total_other_amount"><? echo number_format($total_other_amount,2); ?></td>
                    <td width="120" align="right" id="total_commercial_cost"><? echo number_format($total_commercial_cost,2); ?></td>
                    <td width="120" align="right" id="total_foreign_amount"><? echo number_format($total_foreign_amount,2); ?></td>
                    <td width="120" align="right" id="total_local_amount"><? echo number_format($total_local_amount,2); ?></td>
                    <td width="100" align="right" id="total_test_cost_amount"><? echo number_format($total_test_cost_amount,2); ?></td>
                    <td width="100" align="right" id="total_freight_amount"><? echo number_format($total_freight_amount,2); ?></td>
                    <td width="120" align="right" id="total_inspection_amount"><? echo number_format($total_inspection_amount,2); ?></td>
                    <td width="100" align="right" id="total_certificate_amount"><? echo number_format($total_certificate_amount,2); ?></td>
                    <td width="100" align="right" id="total_common_oh_amount"><? echo number_format($total_common_oh_amount,2); ?></td>
                    <td width="100" align="right" id="total_currier_amount"><? echo number_format($total_currier_amount,2); ?></td>
                    <td width="120">&nbsp;</td>
                    <td width="100" align="right" id="total_cm_amount"><? echo number_format($total_cm_amount,2); ?></td>
                    <td width="100" align="right" id="total_tot_cost"><? echo number_format($total_tot_cost,2); ?></td>
                    <td width="100" align="right" id="total_fabric_profit"><? echo number_format($total_fabric_profit,2);?></td>
                    <td width="100" align="right" id="total_profit_fab_percentage"><? echo number_format($total_profit_fab_percentage,2); ?></td>
                    
                    <td align="right" width="" id="tot_yarn_cons"><? echo number_format($total_yarn_cons,2);?></td>
					
					<td align="right" width="80" id="tot_yarn_cons"><? echo number_format($total_smv_knit,2);?></td>
					<td align="right" width="80" id="tot_yarn_cons"><? echo number_format($total_smv_link,2);?></td>
					<td align="right" width="80" id="tot_yarn_cons"><? echo number_format($total_smv_finsmv_pcs,2);?></td>
					<td align="right" width="80" id="tot_yarn_cons"><? echo number_format($total_tot_smv,2);?></td>
					<td width="100">&nbsp;</td>
                </tr>
            </table>
            <table>
                <tr>
                	<?
					$total_fab_cost=number_format($total_fab_cost_amount,2,'.','');
					$total_trim_cost=number_format($total_trim_cost,2,'.','');
					$total_embelishment_cost=number_format($total_embelishment_cost,2,'.','');
					$total_commercial_cost=number_format($total_commercial_cost,2,'.','');
					$total_commssion=number_format($total_commssion,2,'.','');
					$total_testing_cost=number_format($total_testing_cost,2,'.','');
					$total_freight_cost=number_format($total_freight_cost,2,'.','');
					$total_cost_up=number_format($total_tot_cost,2,'.','');
					$total_cm_cost=number_format($total_cm_cost,2,'.','');
					$total_order_amount=number_format($total_order_amount,2,'.','');
					$total_inspection=number_format($total_inspection,2,'.','');
					$total_certificate_cost=number_format($total_certificate_cost,2,'.','');
					$total_common_oh=number_format($total_common_oh,2,'.','');
					$total_currier_cost=number_format($total_currier_cost,2,'.','');
					$total_fabric_profit_up=number_format($total_fab_profit,2,'.','');
					$total_expected_profit_up=number_format($total_expected_profit,2,'.','');
					
					$chart_data_qnty="Fabric Cost;".$total_fab_cost."\nTrimCost;".$total_trim_cost."\nEmbelishment Cost;".$total_embelishment_cost."\nCommercial Cost;".$total_commercial_cost."\nCommission Cost;".$total_commssion."\nTesting Cost;".$total_testing_cost."\nFreightCost;".$total_freight_cost."\nCM Cost;".$total_cm_cost."\nInspection Cost;".$total_inspection."\nCertificate Cost;".$total_certificate_cost."\nCommn OH Cost;".$total_common_oh."\nCurrier Cost;".$total_currier_cost."\n Profit/Loss;".$total_fabric_profit_up."\n";
					?>
                    <input type="hidden" id="graph_data" value="<? //echo substr($chart_data_qnty,0,-1); ?>"/>
                </tr>
            </table>
            <br>
            <a id="displayText" href="javascript:toggle();">Show Yarn Summary</a>
            <div style="width:600px; display:none" id="yarn_summary">
                <div id="data_panel2" align="center" style="width:500px">
                     <input type="button" value="Print Preview" class="formbutton" style="width:100px" name="print" id="print" onClick="new_window1(1)" />
                 </div>
                <table width="500">
                    <tr class="form_caption">
                        <td colspan="6" align="center"><strong>Yarn Cost Summary </strong></td>
                    </tr>
                </table>
                
                <br/> 
                <table class="rpt_table" width="590" cellpadding="0" cellspacing="0" border="1" rules="all">
                                <thead>
                                	<tr>
                                    	<th colspan="7">Yarn Cost Summary</th>
                                    </tr>

                                    <tr>
                                        <th width="30">SL</th>
                                        <th width="140">Composition</th>
                                        <th width="60">Yarn Count</th>
                                        <th width="80">Type</th>
                                        <th width="100">Req. Qty</th>
                                        <th width="70">Avg. Rate</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <?
                                $s=1; $tot_yarn_req_qnty=0; $tot_yarn_req_amnt=0;
                                foreach($yarn_description_data_arr as $count=>$count_value)
                                {
								foreach($count_value as $Composition=>$composition_value)
                                {
								foreach($composition_value as $percent=>$percent_value)
                                {
								foreach($percent_value as $type=>$type_value)
                                {
                                    if($s%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                    //$yarn_desc=explode("**",$key);
                                    
                                    $tot_yarn_req_qnty+=$type_value['qty']; 
                                    $tot_yarn_req_amnt+=$type_value['amount'];
                                    ?>
                                    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr3_<? echo $s; ?>','<? echo $bgcolor; ?>')" id="tr3_<? echo $s;?>">
                                        <td><? echo $s; ?></td>
                                        <td><div style="word-wrap:break-word; width:140px"><? echo $composition[$Composition]." ".$percent."%"; ?></div></td>
                                        <td><div style="word-wrap:break-word; width:60px"><? echo $yarn_count_library[$count]; ?></div></td>
                                        <td><div style="word-wrap:break-word; width:80px"><? echo $yarn_type[$type]; ?></div></td>
                                        <td align="right"><? echo number_format($type_value['qty'],2); ?></td>
                                        <td align="right"><? echo number_format($type_value['amount']/$type_value['qty'],2); ?></td>
                                        <td align="right"><? echo number_format($type_value['amount'],2); ?></td>
                                    </tr>
                                    <?	
                                    $s++;
								}
								}
								}
                                }
                                ?>
                                <tfoot>
                                    <th colspan="4" align="right">Total</th>
                                    <th align="right"><? echo number_format($tot_yarn_req_qnty,2); ?></th>
                                    <th align="right"><? echo number_format($tot_yarn_req_amnt/$tot_yarn_req_qnty,2); ?></th>
                                    <th align="right"><? echo number_format($tot_yarn_req_amnt,2); ?></th>
                                </tfoot>
                            </table>
					</div>
			</fieldset>
			<?
	
	} //2nd button end
	
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
	echo "$html****$filename****$report_type"; 
	exit();

}

if($action=="precost_yarn_detail")
{ 
	echo load_html_head_contents("Yarn Cost Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//$order_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");
	$style_ref_arr=return_library_array( "select job_no, style_ref_no from wo_po_details_master", "job_no", "style_ref_no");				
	$gmts_item_arr=return_library_array( "select job_no, gmts_item_id from wo_po_details_master", "job_no", "gmts_item_id");
	//echo "select sum(b.plan_cut*a.total_set_qnty) as po_quantity from wo_po_break_down b,wo_po_details_master a  where a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id='$po_id'";
	$order_qty=return_field_value("sum(b.plan_cut*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id in($po_id)","po_quantity");
	$ratio_qty=return_field_value("sum(a.total_set_qnty) as ratio", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id in($po_id)","ratio");
	//print($order_qty);die;
    $costing_per=return_field_value("costing_per as costing_per", "wo_pre_cost_mst", "job_no='$job_no'","costing_per");
	if($costing_per==1) $costing_per_dzn="1 Dzn";
	else if($costing_per==2) $costing_per_dzn="1 Pcs";
	else if($costing_per==3) $costing_per_dzn="2 Dzn";
	else if($costing_per==4) $costing_per_dzn="3 Dzn";
	else if($costing_per==5) $costing_per_dzn="4 Dzn";
						
	$fabriccostArray=sql_select("select costing_per_id, trims_cost, embel_cost, cm_cost, commission, common_oh, lab_test, inspection, freight, comm_cost from wo_pre_cost_dtls where job_no='".$job_no."' and status_active=1 and is_deleted=0");
                        
	$dzn_qnty=0;
	if($fabriccostArray[0][csf('costing_per_id')]==1) $dzn_qnty=12;
	else if($fabriccostArray[0][csf('costing_per_id')]==3) $dzn_qnty=12*2;
	else if($fabriccostArray[0][csf('costing_per_id')]==4) $dzn_qnty=12*3;
	else if($fabriccostArray[0][csf('costing_per_id')]==5) $dzn_qnty=12*4;
	else $dzn_qnty=1;
	$dzn_qnty=$dzn_qnty*$ratio_qty;
	$costing_per=$fabriccostArray[0][csf('costing_per_id')];
	//echo $costing_per.'DDDDDDDDDDD';
	$sql_po="select a.job_no,d.id as po_id, c.item_number_id, c.color_number_id, c.order_quantity, c.plan_cut_qnty,d.po_number from wo_po_details_master a, wo_po_break_down d, wo_po_color_size_breakdown c where a.job_no=d.job_no_mst and a.job_no=c.job_no_mst and d.id=c.po_break_down_id  and a.is_deleted=0 and a.status_active=1 and d.is_deleted=0 and d.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.id in($po_id) order by d.id ASC";
	$sql_po_data=sql_select($sql_po); $gmts_item_color_qty_arr=array();
	foreach($sql_po_data as $row)
	{
		$gmts_item_color_qty_arr[$row[csf('job_no')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
		$po_number.=$row[csf('po_number')].',';
	}
	unset($sql_po_data);
	
	if($db_type==2)
	{
		$po_grp="listagg(CAST(d.id as VARCHAR(4000)),',') within group (order by d.id) as po_ids";
		
	}
	else
	{
		$po_grp="group_concat(d.id) as po_ids";
		
	}
 $data_sql="select a.id, a.fabric_cost_dtls_id, a.job_no, a.count_id, a.copm_one_id, a.percent_one, a.copm_two_id, a.percent_two, a.type_id, a.cons_ratio, a.cons_qnty, a.rate, a.amount, a.avg_cons_qnty, a.supplier_id, a.color, a.consdznlbs, a.rate_dzn, 
	b.item_number_id, b.body_part_id, b.fabric_description, b.color_type_id, b.uom, c.color_number_id, c.stripe_color, c.measurement ,$po_grp
	from wo_pre_cost_fab_yarn_cost_dtls a, wo_pre_cost_fabric_cost_dtls b, wo_pre_stripe_color c,wo_po_break_down d where a.fabric_cost_dtls_id=b.id and b.id=c.pre_cost_fabric_cost_dtls_id and a.color=c.stripe_color and d.job_no_mst=a.job_no and d.job_no_mst=b.job_no  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.id in($po_id) group by a.id, a.fabric_cost_dtls_id, a.job_no, a.count_id, a.copm_one_id, a.percent_one, a.copm_two_id, a.percent_two, a.type_id, a.cons_ratio, a.cons_qnty, a.rate, a.amount, a.avg_cons_qnty, a.supplier_id, a.color, a.consdznlbs, a.rate_dzn, 
	b.item_number_id, b.body_part_id, b.fabric_description, b.color_type_id, b.uom, c.color_number_id, c.stripe_color, c.measurement";
	$data_arr_yarn=sql_select($data_sql);
	foreach($data_arr_yarn as $yarn_row)
	{
		//$costing_per=$job_wise_arr[$yarn_row[csf('job_no')]]['costing_per'];
		
		$poQty=0; $yarn_req_kg=0; $yarn_req_lbs=0; $amount_req=0;
		$poQty=$gmts_item_color_qty_arr[$yarn_row[csf('job_no')]][$yarn_row[csf('item_number_id')]][$yarn_row[csf('color_number_id')]];
		$yarn_req_kg=($yarn_row[csf('measurement')]/$order_price_per_dzn)*$poQty;
		$yarn_req_lbs=$yarn_req_kg*2.20462;
		$amount_req=$yarn_req_lbs*$yarn_row[csf('rate')];
		$yarn_job_wise_arr[$row[csf('job_no')]]['amount']+=$amount_req;
		$yarn_job_wise_arr[$row[csf('job_no')]]['req_qty']+=$yarn_req_lbs;
	}
	
	 $po_no=rtrim($po_number,',');
	  $po_nos=implode(",",array_unique(explode(",", $po_no)));
	?>
	<fieldset style="width:830px; margin-left:3px">
		<div id="scroll_body" align="center">
        <table  border="1" class="rpt_table" rules="all" width="830" cellpadding="0" cellspacing="0" align="center">
             <tr> 
                <td colspan="3" align="center"><strong>Yarn Cost Details</strong></td>
            </tr>
            <tr> 
                <td width="150"><strong>Job No.:</strong>&nbsp; <? echo $job_no; ?> </td><td  width="150"><strong>Order: </strong>&nbsp; <? echo $po_nos;  ?></td><td  width="150"><strong>Buyer:</strong> &nbsp; <? echo $buyer_library[$buyer_id]; ?></td>
            </tr>
        </table>
        <table border="1" class="rpt_table" rules="all" width="830" cellpadding="0" cellspacing="0" align="center">
            <thead>
                <th width="30">Sl</th>
                <th width="70">Count</th>
                <th width="80">Comp 1</th>
                <th width="50">%</th>
                <th width="80">Comp 2</th>
                <th width="50">%</th>
                <th width="80">Type</th>
                <th width="80">GMTS Qty</th>
                <th width="80">Cons Qnty/&nbsp; <? echo $costing_per_dzn; ?></th>
                <th width="80">Yarn Req. Qty(LBS)</th>
                <th width="70">Yarn Rate</th>
                <th width="80">Amount(LBS)</th>
            </thead>
            <tbody>
            <?
		
            $i=1;
         // $fabricArray="select a.id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id,  cons_qnty, rate, amount,status_active from wo_pre_cost_fab_yarn_cost_dtls where job_no='$job_no'";
           // $sql_result=sql_select($fabricArray);
            
            foreach($data_arr_yarn as $row)
            {
				if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($costing_per==1){
				$order_price_per_dzn=12;
				$costing_for=" DZN";
				}
				else if($costing_per==2){
				$order_price_per_dzn=1;
				$costing_for=" PCS";
				}
				else if($costing_per==3){
				$order_price_per_dzn=24;
				$costing_for=" 2 DZN";
				}
				else if($costing_per==4){
				$order_price_per_dzn=36;
				$costing_for=" 3 DZN";
				}
				else if($costing_per==5){
				$order_price_per_dzn=48;
				$costing_for=" 4 DZN";
				}
				$poQty=$gmts_item_color_qty_arr[$row[csf('job_no')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]];	
				//echo $poQty.'sdd';
				$yarn_req_kg=($row[csf('measurement')]/$order_price_per_dzn)*$poQty;
				$yarn_req_lbs=$yarn_req_kg*2.20462;
				//echo $yarn_req_kg.'DDD';
				$amount_req=$yarn_req_lbs*$row[csf('rate')];
				$req_qty=$yarn_req_lbs;
				//$total_amount=$req_qty*$row[csf('rate')];
				//$req_qty=$cost_per_qty*$order_qty;
				$tot_amount=$amount_req;
				//$total_amount=($tot_amount/$dzn_qnty)*$order_qty;
				//$tot_cons_amount=$cons_qty*$order_qty;
				?>
				<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    <td width="30"><p><? echo $i; ?></p></td>
                    <td width="70" align="center"><p><? echo $yarn_count_library[$row[csf('count_id')]]; ?></p></td>
                    <td width="80" align="center"><p><? echo $composition[$row[csf('copm_one_id')]]; ?></p></td>
                    <td width="50" align="center"><p><? echo number_format($row[csf('percent_one')],2); ?></p></td>
                    <td width="80" align="center"><p><? echo $composition[$row[csf('copm_two_id')]]; ?></p></td>
                    <td width="50" align="center"><p><? echo $row[csf('percent_two')]; ?></p></td>
                    <td width="80" align="center"><p><? echo $yarn_type[$row[csf('type_id')]]; ?></p></td>
                    <td width="80" align="right"><p><? echo number_format($order_qty,2); ?></p></td>
                    <td width="80" align="right"><p><? echo number_format($row[csf('cons_qnty')],4); ?></p></td>
                    <td width="80" align="right"><p><? echo number_format($req_qty,2); ?></p></td>
                    <td width="70"  align="right"><p><? echo number_format($row[csf('rate')],2); ?></p></td>
                    <td width="80"  align="right"><p><? echo number_format($amount_req,2); ?></p></td>
				</tr>
				<?
				$tot_qty+=$req_qty;
				$tot_amount_yarn+=$amount_req;
				$i++;
            }
            ?>
            </tbody>
            <tfoot>
                <tr class="tbl_bottom">
                    <td colspan="9" align="right">Total</td>
                    <td align="right"><? echo number_format($tot_qty,2); ?>&nbsp;</td>
                    <td>&nbsp; </td>
                    <td align="right"><? echo number_format($tot_amount_yarn,2); ?>&nbsp;</td>
                </tr>
                <tr class="tbl_bottom">
                    <td colspan="9" align="right">Avg.Yarn Rate</td>
                    <td  align="left"><? echo number_format($tot_amount_yarn/$tot_qty,2); ?></td>
                    <td colspan="2" align="left"> </td>
                </tr>
            </tfoot>
        </table>
        </div>
    </fieldset>
   <script>

	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
	}	
	
</script>
    <? 
	die;
		$sql_po="select a.job_no,d.id as po_id, c.item_number_id, c.color_number_id, c.order_quantity, c.plan_cut_qnty from wo_po_details_master a, wo_po_break_down d, wo_po_color_size_breakdown c where a.job_no=d.job_no_mst and a.job_no=c.job_no_mst and d.id=c.po_break_down_id  and a.is_deleted=0 and a.status_active=1 and d.is_deleted=0 and d.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.id in($po_id) order by d.id ASC";die;
	$sql_po_data=sql_select($sql_po); $gmts_item_color_qty_arr=array();
	foreach($sql_po_data as $row)
	{
		$gmts_item_color_qty_arr[$row[csf('job_no')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
	}
	unset($sql_po_data);
	
	if($db_type==2)
	{
		$po_grp="listagg(CAST(d.id as VARCHAR(4000)),',') within group (order by d.id) as po_ids";
		
	}
	else
	{
		$po_grp="group_concat(d.id) as po_ids";
		
	}
 $data_sql="select a.id, a.fabric_cost_dtls_id, a.job_no, a.count_id, a.copm_one_id, a.percent_one, a.copm_two_id, a.percent_two, a.type_id, a.cons_ratio, a.cons_qnty, a.rate, a.amount, a.avg_cons_qnty, a.supplier_id, a.color, a.consdznlbs, a.rate_dzn, 
	b.item_number_id, b.body_part_id, b.fabric_description, b.color_type_id, b.uom, c.color_number_id, c.stripe_color, c.measurement ,$po_grp
	from wo_pre_cost_fab_yarn_cost_dtls a, wo_pre_cost_fabric_cost_dtls b, wo_pre_stripe_color c,wo_po_break_down d where a.fabric_cost_dtls_id=b.id and b.id=c.pre_cost_fabric_cost_dtls_id and a.color=c.stripe_color and d.job_no_mst=a.job_no and d.job_no_mst=b.job_no  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.id in($po_id) group by a.id, a.fabric_cost_dtls_id, a.job_no, a.count_id, a.copm_one_id, a.percent_one, a.copm_two_id, a.percent_two, a.type_id, a.cons_ratio, a.cons_qnty, a.rate, a.amount, a.avg_cons_qnty, a.supplier_id, a.color, a.consdznlbs, a.rate_dzn, 
	b.item_number_id, b.body_part_id, b.fabric_description, b.color_type_id, b.uom, c.color_number_id, c.stripe_color, c.measurement";
	$data_arr_yarn=sql_select($data_sql);
	foreach($data_arr_yarn as $yarn_row)
	{
		//$costing_per=$job_wise_arr[$yarn_row[csf('job_no')]]['costing_per'];
		 if($costing_per==1){
            $order_price_per_dzn=12;
            $costing_for=" DZN";
        }
        else if($costing_per==2){
            $order_price_per_dzn=1;
            $costing_for=" PCS";
        }
        else if($costing_per==3){
            $order_price_per_dzn=24;
            $costing_for=" 2 DZN";
        }
        else if($costing_per==4){
            $order_price_per_dzn=36;
            $costing_for=" 3 DZN";
        }
        else if($costing_per==5){
            $order_price_per_dzn=48;
            $costing_for=" 4 DZN";
        }
		$poQty=0; $yarn_req_kg=0; $yarn_req_lbs=0; $amount_req=0;
		$poQty=$gmts_item_color_qty_arr[$yarn_row[csf('job_no')]][$yarn_row[csf('item_number_id')]][$yarn_row[csf('color_number_id')]];
		$yarn_req_kg=($yarn_row[csf('measurement')]/$order_price_per_dzn)*$poQty;
		$yarn_req_lbs=$yarn_req_kg*2.20462;
		$amount_req=$yarn_req_lbs*$yarn_row[csf('rate')];
		$yarn_job_wise_arr[$row[csf('job_no')]]['amount']+=$amount_req;
		$yarn_job_wise_arr[$row[csf('job_no')]]['req_qty']+=$yarn_req_lbs;
	}
	
	//$job_no="'".$job_no."'";
	//$yarn= new yarn($job_no,'job');
	//$condition= new condition();
	//$condition->job_no("='$job_no'");
	//$condition->init();
	//$yarn= new yarn($condition);
	//echo $yarn->getQuery(); die;
	//$popUpDataArray=$yarn->getOrderCountCompositionColorTypeAndConsumptionWiseYarnDataArray();
	//print_r($popUpDataArray);
		?>
		<div style="width:860px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:855px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0">
				<thead>
                	<tr>
                    	<th colspan="4">Buyer : <? echo $buyer_library[$buyer_id]; ?></th>
                        <th colspan="2">Job :<? echo $job_no; ?></th>
                        <th colspan="2">PO No. : <? echo $order_arr[$po_id]; ?></th>
                        <th>Po Qty.: <? echo $order_qty; ?></th>
                    </tr>
                	<tr>
						<th colspan="5">Gmts Item :<? 
						 $gmts_item=''; $gmts_item_id=explode(",",$gmts_item_arr[$job_no]);
						foreach($gmts_item_id as $item_id)
						{
							if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=", ".$garments_item[$item_id];
						}
						echo $gmts_item;
						
						//echo $gmts_item_arr[$job_no];//$gmts_item; ?></th>
                        <th colspan="4">Style : <? echo $style_ref_arr[$job_no]; ?></th>
                        
                    </tr>
                	<tr>
                        <th width="40">SL</th>
                        <th width="150">Yarn Description</th>
                        <th width="150">Size</th>
                        <th width="70">PO Qty</th>
                        <th width="70">Fab.Cons. / Dzn</th>
                        <th width="70">Yarn Cons. / Dzn</th>
                        <th width="100">Req. Qty.</th>
                        <th width="100">Rate (USD)</th>
                        <th>Amount (USD)</th>
                    </tr>
				</thead>
                <?
				 $tot_reqQty=0;
				 //$tot_rate+=$row[csf('rate')];
				 $tot_amount=0;
				//$yarn= new yarn($job_no,'job');
				//$sql="select job_no, count_id, copm_one_id,color, percent_one, copm_two_id, percent_two, type_id, sum(cons_qnty) as qty, sum(avg_cons_qnty) as qnty, sum(amount) as amnt, sum(rate) as rate, sum(rate*avg_cons_qnty) as amount from wo_pre_cost_fab_yarn_cost_dtls where status_active=1 and is_deleted=0 and job_no='$job_no' group by job_no, count_id, copm_one_id,color, percent_one, copm_two_id, percent_two, type_id";	
				//$result=sql_select($sql);
				$i=1;
				foreach($popUpDataArray[$po_id] as $count=>$countwisevalue)
				{
				foreach($countwisevalue as $compositionid=>$compositionwisevalue)
				{
				foreach($compositionwisevalue as $percentOne=>$percentOnewisevalue)
				{
				foreach($percentOnewisevalue as $color=>$colorwisevalue)
				{
				foreach($colorwisevalue as $type=>$typewisevalue)
				{
				foreach($typewisevalue as $consumption=>$consumptionwisevalue)
				{
					
					//print_r($consumptionwisevalue);
					//die;
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$compos="";
					$compos=$composition[$compositionid]." ".$percentOne." %";
					$description="";
					$description=$yarn_count_details[$count].' '.$compos.' '.$color_library[$color].' '.$yarn_type[$type];
					//echo $description;
					
					
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40" align="center"><? echo $i; ?></td>
                        <td width="150"><p><? echo $description; ?></p></td>
                       <td width="150">
                       <p>
					   <? 
					   ksort($consumptionwisevalue['gmtsSize']);
					   $sizeString='';
					    foreach ($consumptionwisevalue['gmtsSize'] as $sizeId){
							$sizeString.= $size_library[$sizeId].",";
						}
						echo  rtrim($sizeString,",");
					   ?>
                       </p></td>
                        <td width="70" align="right"><? echo number_format($consumptionwisevalue['planPutQnty'],4); ?></td>
                        <td width="70" align="right"><? echo number_format($consumptionwisevalue['fabCons'],4); ?></td>
                        <td width="70" align="right">
						<? 
						echo number_format($consumption,4); 
						echo "</br>";
						echo "(".number_format($consumptionwisevalue['yratio'],2)."%)";
						?>
                        
                        </td>
                        <td width="100" align="right"><? echo number_format($consumptionwisevalue['qty'],2); ?></td>
                        <td width="100" align="right"><? echo number_format($consumptionwisevalue['rate'],4); ?></td>
                        <td align="right"><? echo number_format($consumptionwisevalue['amount'],2); ?></td>
                    </tr>
                 <?
				 //$tot_consDzn+=$consumptionwisevalue['planPutQnty'];
				 //$tot_avgConsDzn+=$consumptionwisevalue['qty'];
				 $tot_reqQty+=$consumptionwisevalue['qty'];
				 //$tot_rate+=$row[csf('rate')];
				 $tot_amount+=$consumptionwisevalue['amount'];
				 $i++;
				}
				}
				}
				}
				}
				}
				?>
                <tr bgcolor="#CCCCCC">
                	<td>&nbsp;</td>
                    <td align="right"></td>
                    <td align="right"><strong>Total</strong></td>
                    <td align="right"><? //echo number_format($tot_consDzn,4); ?></td>
                    <td align="right"><? //echo number_format($tot_consDzn,4); ?></td>
                    <td align="right"><? //echo number_format($tot_avgConsDzn,4); ?></td>
                    <td align="right"><? echo number_format($tot_reqQty,2); ?></td>
                    <td align="right"><? //echo number_format($tot_rate,4); ?></td>
                    <td align="right"><? echo number_format($tot_amount,2); ?></td>
                </tr>
                <tr class="tbl_bottom">
                <td align="right" colspan="8"><strong>Avg Rate</strong></td>
                <td align="right"><strong><? echo number_format($tot_amount/$tot_reqQty,2); ?> </strong></td>
                </tr>
            </table>
        </div>
    </fieldset>
    
    <?
	exit();
}// pre cost Yarn  end
if($action=="fab_purchase_detail")
{
	echo load_html_head_contents("Purchase Cost Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $job_no;
	$order_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");
	$order_qty=return_field_value("sum(b.plan_cut*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id'  and b.id in($po_id)","po_quantity");
	$ratio_qty=return_field_value("sum(a.total_set_qnty) as ratio", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id in($po_id) ","ratio");
	//print($order_qty);die;
	$costing_per=return_field_value("costing_per as costing_per", "wo_pre_cost_mst", "job_no='$job_no'","costing_per");
	if($costing_per==1) $costing_per_dzn="1 Dzn";
	else if($costing_per==2) $costing_per_dzn="1 Pcs";
	else if($costing_per==3) $costing_per_dzn="2 Dzn";
	else if($costing_per==4) $costing_per_dzn="3 Dzn";
	else if($costing_per==5) $costing_per_dzn="4 Dzn";
	
	$fabriccostArray=sql_select("select costing_per_id, trims_cost, embel_cost, cm_cost, commission, common_oh, lab_test, inspection, freight, comm_cost from wo_pre_cost_dtls where job_no='".$job_no."' and status_active=1 and is_deleted=0");
	
	$dzn_qnty=0;
	if($fabriccostArray[0][csf('costing_per_id')]==1) $dzn_qnty=12;
	else if($fabriccostArray[0][csf('costing_per_id')]==3) $dzn_qnty=12*2;
	else if($fabriccostArray[0][csf('costing_per_id')]==4) $dzn_qnty=12*3;
	else if($fabriccostArray[0][csf('costing_per_id')]==5) $dzn_qnty=12*4;
	else $dzn_qnty=1;
	$dzn_qnty=$dzn_qnty*$ratio_qty;
	$costing_per=$fabriccostArray[0][csf('costing_per_id')];
	?>
	<fieldset style="width:830px; margin-left:3px">
        <div id="scroll_body" align="center">
            <table  border="1" class="rpt_table" rules="all" width="830" cellpadding="0" cellspacing="0" align="center">
                <tr> 
                	<td colspan="3" align="center"><strong>Fabric Purchase Cost Details</strong></td>
                </tr>
                <tr> 
                	<td width="150"><strong>Job No.:</strong>&nbsp; <? echo $job_no; ?> </td><td  width="150"><strong>Order: </strong>&nbsp; <? echo $order_arr[$po_id];  ?></td><td  width="150"><strong>Buyer:</strong> &nbsp; <? echo $buyer_library[$buyer_id]; ?></td>
                </tr>
            </table>
            <table border="1" class="rpt_table" rules="all" width="830" cellpadding="0" cellspacing="0" align="center">
                <thead>
                    <th width="30">Sl</th>
                    <th width="70">Body Part</th>
                    <th width="80">Fab. Nature</th>
                    <th width="100">Fab. Descrp.</th>
                    <th width="80">GMTS Qty.</th>
                    <th width="50">Source</th>
                    <th width="80">Cons Qty./<? echo $costing_per_dzn; ?></th>
                    <th width="80">Req. Qty.</th>
                    <th width="70">Rate</th>
                    <th width="80">Amount</th>
                </thead>
                <tbody>
                 <tr style="display:none">
                    <td colspan="10" bgcolor="#CCCCCC"><strong>Fabric Cost Details</strong> </td>
                 </tr>
                <?
			
				
				$condition= new condition();
				if(str_replace("'","",$job_no) !='')
				{
				  $condition->job_no("='$job_no'");
			 	}
				if(str_replace("'","",$po_id) !='')
				{
				  $condition->po_id("=$po_id");
			 	}
				 $condition->init();
				
				$fabric= new fabric($condition);
				// echo $fabric->getQuery(); die;
				$fabric_costing_qty_arr=$fabric->getQtyArray_by_orderAndFabriccostid_knitAndwoven_greyAndfinish();
				$fabric_costing_amount_arr=$fabric->getAmountArray_by_orderAndFabriccostid_knitAndwoven_greyAndfinish();
				//print_r($fabric_costing_qty_arr);
			
				//and a.fabric_source in(2) and a.fab_nature_id=2
                $i=1;
              $data_array=("select  a.id,a.body_part_id, a.fab_nature_id,a.fabric_source, a.fabric_description,  a.fabric_source, avg(a.rate) as rate, sum(a.amount) as amount,sum(a.avg_finish_cons) as avg_finish_cons,avg(b.cons) as cons,avg(b.requirment) as avg_cons,b.po_break_down_id as po_id   from wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b where b.pre_cost_fabric_cost_dtls_id=a.id and a.job_no='$job_no' and a.job_no=b.job_no and b.po_break_down_id in($po_id)   and a.status_active=1 and  a.is_deleted=0 and a.fabric_source=2 group by a.id, a.job_no, a.item_number_id, a.body_part_id, a.fab_nature_id,a.consumption_basis,a.fabric_source, a.fabric_description, a.fabric_source,b.po_break_down_id order by a.fab_nature_id");
                $sql_result=sql_select($data_array);
				//echo $fab_purchase_knit=array_sum($fabric_costing_arr['knit']['grey'][$po_id])+array_sum($fabric_costing_arr['woven']['grey'][$po_id]);
				$fab_purchase_woven=array_sum($fabric_costing_arr['woven']['grey'][$po_id]);
                foreach($sql_result as $row)
                {
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					$tot_avg=number_format($row[csf('avg_cons')],4);
					//echo $tot_avg;
					//$req_qty_avg=($tot_avg/$dzn_qnty)*$order_qty;
					//echo $req_qty_avg;
					//$req_qty_p=$req_qty_avg/$dzn_qnty*$order_qty;
					//$total_amount=$req_qty_avg*$row[csf('rate')];
						
					 $fab_purchase_knit_qty=array_sum($fabric_costing_qty_arr['sweater']['grey'][$row[csf('po_id')]][$row[csf('id')]])+array_sum($fabric_costing_qty_arr['knit']['grey'][$row[csf('po_id')]][$row[csf('id')]])+array_sum($fabric_costing_qty_arr['woven']['grey'][$row[csf('po_id')]][$row[csf('id')]]);
					 $fab_purchase_knit_amount=array_sum($fabric_costing_amount_arr['sweater']['grey'][$row[csf('po_id')]][$row[csf('id')]])+array_sum($fabric_costing_amount_arr['knit']['grey'][$row[csf('po_id')]][$row[csf('id')]]);
					 $fab_purchase_woven_amount=array_sum($fabric_costing_amount_arr['woven']['grey'][$row[csf('po_id')]][$row[csf('id')]]);
				//	$fab_purchase_woven=$fabric_costing_arr['woven']['grey'][$po_id];
					$fab_purchase_amount=$fab_purchase_knit_amount+$fab_purchase_woven_amount;
					?>
                   
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="30"><p><? echo $i; ?></p></td>
                        <td width="70" align="center"><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
                        <td width="80" align="center"><p><? echo $item_category[$row[csf('fab_nature_id')]]; ?></p></td>
                        <td width="100" align="center"><p><? echo $row[csf('fabric_description')]; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($order_qty,2); ?></p></td>
                        <td width="50" align="center"><p><? if($row[csf('fabric_source')]==2) echo "Purchase"; else echo ""; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format(($order_qty/$fab_purchase_knit_qty)*12,2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($fab_purchase_knit_qty,2); ?></p></td>
                        <td width="70" align="right"><p><? echo number_format($fab_purchase_knit_amount/$fab_purchase_knit_qty,2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($fab_purchase_amount,2); ?></p></td>
					</tr>
					<?
					$tot_qty_knit+=$fab_purchase_knit_qty;
					$tot_amount_knit+=$fab_purchase_amount;
					$i++;
                }
				
                ?>
                <tr bgcolor="#CCCCCC">
                 <td colspan="7" align="right" > <strong>Total</strong></td><td align="right"><strong><? echo number_format($tot_qty_knit,2);?></strong></td><td align="right">&nbsp; </td> <td align="right"> <strong><? echo number_format($tot_amount_knit,2);?></strong></td>
                </tr>
                <?
                die;
				?>
                <tr>
                    <td colspan="10" bgcolor="#CCCCCC"><strong>Woven Purchase</strong> </td>
                 </tr>
                <?
				
				
				$condition= new condition();
				if(str_replace("'","",$job_no) !='')
				{
				  $condition->job_no("='$job_no'");
			 	}
				if(str_replace("'","",$po_id) !='')
				{
				  $condition->po_id("in($po_id)");
			 	}
				 $condition->init();
				
				$fabric= new fabric($condition);
				// echo $fabric->getQuery(); die;
				$fabric_costing_qty_arr=$fabric->getQtyArray_by_orderAndFabriccostid_knitAndwoven_greyAndfinish();
				$fabric_costing_amount_arr=$fabric->getAmountArray_by_orderAndFabriccostid_knitAndwoven_greyAndfinish();
				//print_r($fabric_costing_amount_arr);
			
				
				//$fab_purchase_woven=$fabric_costing_arr['woven']['grey'][$po_id];
				//$fab_purchase=$fab_purchase_knit+$fab_purchase_woven;
                $i=1;//
                $data_array_woven=("select  a.id,a.body_part_id, a.fab_nature_id,a.fabric_source, a.fabric_description,  a.fabric_source, avg(a.rate) as rate, sum(a.amount) as amount,sum(a.avg_finish_cons) as avg_finish_cons,avg(b.cons) as cons,avg(b.requirment) as avg_cons   from wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b where b.pre_cost_fabric_cost_dtls_id=a.id and a.job_no='$job_no' and a.job_no=b.job_no and b.po_break_down_id in($po_id)   and a.status_active=1 and  a.is_deleted=0 and a.fabric_source=2 and a.fab_nature_id=3 group by a.id, a.job_no, a.item_number_id, a.body_part_id, a.fab_nature_id,a.consumption_basis,a.fabric_source, a.fabric_description, a.fabric_source");
                $sql_result_wvn=sql_select($data_array_woven);
                foreach($sql_result_wvn as $row)
                {
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					$tot_avg=number_format($row[csf('avg_cons')],4);
					//echo $tot_avg;
					//$req_qty_avg=($tot_avg/$dzn_qnty)*$order_qty;
					//echo $req_qty_avg;
					//$req_qty_p=$req_qty_avg/$dzn_qnty*$order_qty;
					//$total_amount=$req_qty_avg*$row[csf('rate')];
					$woven_qty=array_sum($fabric_costing_qty_arr['woven']['grey'][$po_id][$row[csf('id')]]);
					$woven_amount=array_sum($fabric_costing_amount_arr['woven']['grey'][$po_id][$row[csf('id')]]);
					$woven_amount_fin=array_sum($fabric_costing_amount_arr['woven']['finish'][$po_id][$row[csf('id')]]);
					?>
                   
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="30"><p><? echo $i; ?></p></td>
                        <td width="70" align="center"><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
                        <td width="80" align="center"><p><? echo $item_category[$row[csf('fab_nature_id')]]; ?></p></td>
                        <td width="100" align="center"><p><? echo $row[csf('fabric_description')]; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($order_qty,2); ?></p></td>
                        <td width="50" align="center"><p><? if($row[csf('fabric_source')]==2) echo "Purchase"; else echo ""; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($row[csf('avg_cons')],2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($woven_qty,2); ?></p></td>
                        <td width="70" align="right"><p><? echo number_format($woven_amount/$woven_qty,2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($woven_amount+$woven_amount_fin,2); ?></p></td>
					</tr>
					<?
					$tot_qty_woven+=$woven_qty;
					$tot_woven_amount+=$woven_amount;
					$i++;
                }
                ?>
                 <tr>
                 
                 <td colspan="7" align="right"> <strong>Total</strong></td><td align="right"><? echo number_format($tot_qty_woven,2);?> </td><td align="right">&nbsp; </td> <td align="right"> <? echo number_format($tot_woven_amount,2);?></td>
                
                </tr>
                </tbody>
                <tfoot>
                    <tr class="tbl_bottom">
                        <td colspan="7" align="right"><strong>Grand Total</strong></td>
                        <td align="right"><? echo number_format($tot_qty_woven+$tot_qty_knit,2); ?>&nbsp;</td>
                        <td>&nbsp; </td>
                        <td align="right"><? echo number_format($tot_amount_knit+$tot_woven_amount,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>
	</fieldset>
	<?
	exit();
}
if($action=="trim_cost_detail")
{
	echo load_html_head_contents("Trim Cost Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $job_no;die;
	$order_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");
			$po_qty_country_wise=array();
			$sql_po_qty=sql_select("select b.id,c.country_id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id     and a.status_active=1 and b.status_active=1 and c.status_active=1 and b.id in($po_id) group by b.id,a.total_set_qnty,c.country_id");
			foreach($sql_po_qty as $row)
			{
				$po_qty_country_wise[$row[csf('id')]][$row[csf('country_id')]]=$row[csf('order_quantity_set')];
				
			}
			$po_qty_po_wise=array();
			$sql_po_qty2=sql_select("select b.id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id  and a.status_active=1 and b.status_active=1 and c.status_active=1 and b.id in($po_id) group by b.id,a.total_set_qnty");
			foreach($sql_po_qty2 as $row)
			{
			//	$po_qty_country_wise[$row[csf('id')]][$row[csf('country_id')]]=$row[csf('order_quantity_set')];
				$po_qty_po_wise[$row[csf('id')]]=$row[csf('order_quantity_set')];
			}
	$order_qty=return_field_value("sum(b.po_quantity*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id in($po_id)","po_quantity");
	
	$ratio_qty=return_field_value("sum(a.total_set_qnty) as ratio", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id in($po_id)","ratio");
	//print($order_qty);die;
	$costing_per=return_field_value("costing_per as costing_per", "wo_pre_cost_mst", "job_no='$job_no'","costing_per");
	if($costing_per==1) $costing_per_dzn="1 Dzn";
	else if($costing_per==2) $costing_per_dzn="1 Pcs";
	else if($costing_per==3) $costing_per_dzn="2 Dzn";
	else if($costing_per==4) $costing_per_dzn="3 Dzn";
	else if($costing_per==5) $costing_per_dzn="4 Dzn";
	
	$fabriccostArray=sql_select("select costing_per_id, trims_cost, embel_cost, cm_cost, commission, common_oh, lab_test, inspection, freight, comm_cost from wo_pre_cost_dtls where job_no='".$job_no."' and status_active=1 and is_deleted=0");
	
	$dzn_qnty=0;
	if($fabriccostArray[0][csf('costing_per_id')]==1) $dzn_qnty=12;
	else if($fabriccostArray[0][csf('costing_per_id')]==3) $dzn_qnty=12*2;
	else if($fabriccostArray[0][csf('costing_per_id')]==4) $dzn_qnty=12*3;
	else if($fabriccostArray[0][csf('costing_per_id')]==5) $dzn_qnty=12*4;
	else $dzn_qnty=1;
	$dzn_qnty=$dzn_qnty*$ratio_qty;
	$costing_per=$fabriccostArray[0][csf('costing_per_id')];
	?>
	<fieldset style="width:770px; margin-left:3px">
        <div id="scroll_body" align="center">
            <table  border="1" class="rpt_table" rules="all" width="750" cellpadding="0" cellspacing="0" align="center">
                <tr> 
                	<td colspan="6" align="center"><strong> Trim Cost Details</strong></td>
                </tr>
                <tr> 
                	<td width="150"><strong>Job No.</strong>&nbsp; <? echo $job_no; ?> </td><td  width="150"><strong>Order: </strong>&nbsp; <? echo $order_arr[$po_id];  ?></td><td  width="150"><strong>Buyer:</strong> &nbsp; <? echo $buyer_library[$buyer_id]; ?></td>
                </tr>
            </table>
            <table border="1" class="rpt_table" rules="all" width="750" cellpadding="0" cellspacing="0" align="center">
                <thead>
                    <th width="30">Sl</th>
                    <th width="70">Item Name</th>
                    <th width="80">Description.</th>
                    <th width="80">Brand/Supplier Ref</th>
                    <th width="80">UOM</th>
                    <th width="70">Cons Per/<? echo $costing_per_dzn;?></th>
                    <th width="80">Req. Qty</th>
                    <th width="70">Rate Per Unit</th>
                    <th width="80">Amount</th>
                </thead>
                <tbody>
                <?
                $i=1;
               $trimsArray=("select  a.id as trims_id,b.po_break_down_id as po_id,b.country_id,a.trim_group,a.description, a.cons_dzn_gmts,a.cons_uom, a.brand_sup_ref,a.amount, a.rate 
                from wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b 
                where a.id=b.wo_pre_cost_trim_cost_dtls_id and a.job_no=b.job_no  and a.job_no='$job_no' and b.po_break_down_id in($po_id) and a.status_active=1 and  a.is_deleted=0 group by a.id,b.po_break_down_id,a.trim_group,a.description, b.country_id,a.cons_dzn_gmts,a.cons_uom, a.brand_sup_ref,a.amount, a.rate  ");
				$order_qty_tr=0;
                $sql_result=sql_select($trimsArray);
				$condition= new condition();
				
				if($po_id!='')
				{
					$condition->po_id("in($po_id)"); 
				}
				$condition->init();
				  
				$trims= new trims($condition);
				
				//echo $trims->getQuery(); die;
				$trims_costing_arr_qty=$trims->getQtyArray_by_orderAndPrecostdtlsid();
				$trims_costing_arr_amount=$trims->getAmountArray_by_orderAndPrecostdtlsid();
				//print_r($trims_costing_arr_amount);
                foreach($sql_result as $row)
                {
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					$tot_amount=$row[csf('cons_dzn_gmts')];
					$total_reg=$trims_costing_arr_qty[$row[csf('po_id')]][$row[csf('trims_id')]];//($order_qty/$dzn_qnty)*$tot_amount;
					$tot_cons_amount=$trims_costing_arr_amount[$row[csf('po_id')]][$row[csf('trims_id')]];//$row[csf('rate')]*$total_reg;
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="30"><p><? echo $i; ?></p></td>
                        <td width="70" align="center"><p><? echo $item_library[$row[csf('trim_group')]]; ?></p></td>
                        <td width="80" align="right"><p><? echo $row[csf('description')]; ?></p></td>
                        <td width="80" align="right"><p><? echo $row[csf('brand_sup_ref')]; ?></p></td>
                        <td width="80" align="right"><p><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></p></td>
                        <td width="70" align="right"><p><? echo $row[csf('cons_dzn_gmts')]; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($total_reg,2); ?></p></td>
                        <td width="70" align="right"><p><? echo number_format($tot_cons_amount/$total_reg,2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($tot_cons_amount,2); ?></p></td>
					</tr>
					<?
					$total_req_qty+=$total_reg;
					$total_cons_amountt+=$tot_cons_amount;
					$i++;
                }
                ?>
                </tbody>
                <tfoot>
                    <tr class="tbl_bottom">
                        <td colspan="6" align="right">Total</td>
                        <td align="right"><? echo  number_format($total_req_qty,2); ?>&nbsp;</td>
                         <td align="right"> </td>
                        
                        <td align="right"><? echo  number_format($total_cons_amountt,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>
	</fieldset>
	<?
	exit();
}// Pre cost Trim End
if($action=="embl_cost_detail")//budget
{
	echo load_html_head_contents("Trim Cost Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $job_no;die;
	$order_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");
	$order_qty=return_field_value("sum(b.plan_cut*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id in($po_id)","po_quantity");
	$ratio_qty=return_field_value("sum(a.total_set_qnty) as ratio", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id in($po_id)","ratio");
	//print($order_qty);die;
	$costing_per=return_field_value("costing_per as costing_per", "wo_pre_cost_mst", "job_no='$job_no'","costing_per");
	if($costing_per==1) $costing_per_dzn="1 Dzn";
	else if($costing_per==2) $costing_per_dzn="1 Pcs";
	else if($costing_per==3) $costing_per_dzn="2 Dzn";
	else if($costing_per==4) $costing_per_dzn="3 Dzn";
	else if($costing_per==5) $costing_per_dzn="4 Dzn";
	
	$fabriccostArray=sql_select("select costing_per_id, trims_cost, embel_cost, cm_cost, commission, common_oh, lab_test, inspection, freight, comm_cost from wo_pre_cost_dtls where job_no='".$job_no."' and status_active=1 and is_deleted=0");
	
	$dzn_qnty=0;
	if($fabriccostArray[0][csf('costing_per_id')]==1) $dzn_qnty=12;
	else if($fabriccostArray[0][csf('costing_per_id')]==3) $dzn_qnty=12*2;
	else if($fabriccostArray[0][csf('costing_per_id')]==4) $dzn_qnty=12*3;
	else if($fabriccostArray[0][csf('costing_per_id')]==5) $dzn_qnty=12*4;
	else $dzn_qnty=1;
	$dzn_qnty=$dzn_qnty*$ratio_qty;
	$costing_per=$fabriccostArray[0][csf('costing_per_id')];
	
	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
		}	
	</script>	
	<fieldset style="width:670px; margin-left:3px">
        <div style="width:670px;" align="center">
       	 <input  type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
        </div>
        <div id="report_div" align="center">
            <table  border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
                <tr> 
                	<td colspan="6" align="center"><strong> Print Cost Details</strong></td>
                </tr>
                <tr> 
                	<td width="150"><strong>Job No.</strong>&nbsp; <? echo $job_no; ?> </td><td  width="150"><strong>Order: </strong>&nbsp; <? echo $order_arr[$po_id];  ?></td><td  width="150"><strong>Buyer:</strong> &nbsp; <? echo $buyer_library[$buyer_id]; ?></td>
                </tr>
            </table>
            <table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
                <thead>
                    <th width="30">Sl</th>
                    <th width="70">Emb Name</th>
                    <th width="80">Emb Type</th>
                    <th width="70">Cons Per/<? echo $costing_per_dzn;?></th>
                    <th width="80">Req. Qty</th>
                    <th width="70">Rate Per Unit</th>
                    <th width="80">Amount</th>
                </thead>
                <tbody>
                <?
				$condition= new condition();
				 if(str_replace("'","",$job_no) !=''){
				  $condition->job_no("=('$job_no')");
			 	}
				 if(str_replace("'","",$po_id)!='')
				 {
					$condition->po_id("=$po_id"); 
				 }
				  $condition->init();
				//echo $job_no;die;
				$emblishment= new emblishment($condition);
				//echo $emblishment->getQuery(); die;
				
				$emblishment_costing_arr_name=$emblishment->getAmountArray_by_orderEmbnameAndEmbtype();
				$emblishment_qty_arr_name=$emblishment->getQtyArray_by_orderEmbnameAndEmbtype();
				//print_r($emblishment_costing_arr_name);
                $i=1;
                 $data_emb=("select  c.po_break_down_id as po_id, b.emb_name, b.emb_type, sum(b.cons_dzn_gmts) as cons_dzn_gmts, avg(b.rate) as rate, sum(b.amount) as print_amount  from  wo_pre_cost_embe_cost_dtls b, wo_pre_cos_emb_co_avg_con_dtls c where b.job_no='$job_no' and c.po_break_down_id in($po_id) and  b.job_no=c.job_no and b.emb_name=$embl_type and b.status_active=1 and  b.is_deleted=0  group by c.po_break_down_id,b.emb_name,b.emb_type");
                $sql_result=sql_select($data_emb);
				$total_emblish_cost=0;
                foreach($sql_result as $row)
                {
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					
					$tot_amount=$row[csf('cons_dzn_gmts')];
					//$total_reg=($order_qty/$dzn_qnty)*$tot_amount;
					$tot_cons_amount=$row[csf('rate')]*$total_reg;
					$emblish_cost=$emblishment_costing_arr_name[$row[csf('po_id')]][$row[csf('emb_name')]][$row[csf('emb_type')]];
					$total_reg=$emblishment_qty_arr_name[$row[csf('po_id')]][$row[csf('emb_name')]][$row[csf('emb_type')]];
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="30"><p><? echo $i; ?></p></td>
                        <td width="70" align="center"><p><? echo $emblishment_name_array[$row[csf('emb_name')]]; ?></p></td>
                        <td width="80" align="right"><p><? echo $emblishment_print_type[$row[csf('emb_type')]]; ?></p></td>
                        <td width="70" align="right"><p><? echo $row[csf('cons_dzn_gmts')]; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($total_reg,4); ?></p></td>
                        <td width="70" align="right"><p><? echo $row[csf('rate')]; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($emblish_cost,4); ?></p></td>
					</tr>
					<?
					$tot_req_qty+=$total_reg;
					$total_emblish_cost+=$emblish_cost;
					$i++;
                }
                ?>
                </tbody>
                <tfoot>
                    <tr class="tbl_bottom">
                        <td colspan="4" align="right">Total</td>
                        <td align="right"><? echo  number_format($tot_req_qty,2); ?>&nbsp;</td>
                         <td align="right"><? //echo  number_format($total_emblish_cost,2); ?>&nbsp;</td>
                         <td align="right"><? echo  number_format($total_emblish_cost,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>
	</fieldset>
	<?
	exit();
}// Price Print


?>
