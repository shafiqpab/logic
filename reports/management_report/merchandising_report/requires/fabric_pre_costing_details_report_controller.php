<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.fabrics.php');
require_once('../../../../includes/class4/class.yarns.php');
require_once('../../../../includes/class4/class.conversions.php');
require_once('../../../../includes/class4/class.trims.php');
require_once('../../../../includes/class4/class.emblishments.php');
require_once('../../../../includes/class4/class.washes.php');
require_once('../../../../includes/class4/class.commercials.php');
require_once('../../../../includes/class4/class.commisions.php');
require_once('../../../../includes/class4/class.others.php');


$_SESSION['page_permission']=$permission;
$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//--------------------------------------------------------------------------------------------------------------------
$company_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );
$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$buyer_short_name_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$season_name_library=return_library_array( "select id, season_name from  lib_buyer_season", "id", "season_name"  );

//$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
$dealing_merchant_array = return_library_array("select id, team_member_name from lib_mkt_team_member_info","id","team_member_name");
$team_leader_library=return_library_array( "select id, team_leader_name from lib_marketing_team", "id", "team_leader_name");
//$costing_library=return_library_array( "select job_no, costing_date from wo_pre_cost_mst", "job_no", "costing_date"  );
$supplier_library=return_library_array( "select id,short_name from lib_supplier", "id","short_name");
$country_name_library=return_library_array( "select id,country_name from lib_country", "id","country_name");

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "load_drop_down( 'requires/fabric_pre_costing_details_report_controller', this.value, 'load_drop_down_season', 'season_td'); " );     	 
	exit();
}
if ($action=="load_drop_down_season")
{
	echo create_drop_down( "cbo_season_id", 80, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "" );
	exit();
}
if ($action=="load_drop_down_deal_marchant")
{
	
	echo create_drop_down( "cbo_deal_marchant", 110, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
}
/*if ($action=="load_drop_down_team_member")
{
	echo create_drop_down( "cbo_team_member", 110, "select id,team_member_name 	 from lib_mkt_team_member_info  where team_id='$data' and status_active=1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-Select Team Member-", $selected, "" );   	 
}*/

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
	
		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_job_id").val(splitData[0]); 
			$("#hide_job_no").val(splitData[1]); 
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
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'fabric_pre_costing_details_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
		if($year_id!=0) $year_cond=" and year(insert_date)=$year_id"; else $year_cond="";	
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(insert_date,'YYYY')";
		if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";	
	}
	//if($month_id!=0) $month_cond=" and month(insert_date)=$month_id"; else $month_cond="";
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_field from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond  order by job_no";
	
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","500","240",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','') ;
	exit(); 
} // Job Search end

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
	$job_no=str_replace("'","",$txt_job_id);
	$cbo_year=str_replace("'","",$cbo_year);

	if($db_type==0)
	{
		if ($data[2]=="") $job_no_cond=""; else $job_no_cond="  and FIND_IN_SET(b.job_no_prefix_num,'$data[2]')";
		if(trim($data[3])!=0) $year_cond=" and YEAR(b.insert_date)=$data[3]"; else $year_cond="";
	}
	else if($db_type==2)
	{
		if ($data[2]=="") $job_no_cond=""; else $job_no_cond="  and ',' || b.job_no_prefix_num || ',' LIKE '%$data[2]%' ";
		$year_field_con=" and to_char(b.insert_date,'YYYY')";
		if(trim($data[3])!=0) $year_cond=" $year_field_con=$data[3]"; else $year_cond="";
	}
	
	
	$sql="select a.id, a.po_number, b.job_no_prefix_num, b.job_no, b.buyer_name, b.style_ref_no from wo_po_details_master b, wo_po_break_down a  where b.job_no=a.job_no_mst and b.company_name=$data[0] and b.is_deleted=0 $buyer_name $job_no_cond $year_cond ORDER BY b.job_no";
	//echo $sql;
	$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
	$arr=array(1=>$buyer);
	
	echo  create_list_view("list_view", "Job No,Buyer,Style Ref.,Order No", "110,110,150,180","610","350",0, $sql, "js_set_value", "id,po_number", "", 1, "0,buyer_name,0,0,0", $arr , "job_no_prefix_num,buyer_name,style_ref_no,po_number", "fabric_pre_costing_details_report_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0','') ;
	disconnect($con);
	exit(); 
}


if ($action=="actual_popup")
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
	if ($data[3]=="") $order_no=""; else $order_no=" and a.po_number='".$data[3]."'";
	$job_no=str_replace("'","",$txt_job_id);
	if($db_type==0)
	{
		if ($data[2]=="") $job_no_cond=""; else $job_no_cond="  and FIND_IN_SET(b.job_no_prefix_num,'$data[2]')";
	}
	else if($db_type==2)
	{
		if ($data[2]=="") $job_no_cond=""; else $job_no_cond="  and ',' || b.job_no_prefix_num || ',' LIKE '%$data[2]%' ";
	}
	
	 $sql="SELECT c.id,   a.po_number, b.job_no_prefix_num, b.job_no, b.buyer_name, b.style_ref_no,c.acc_po_no from wo_po_details_master b, wo_po_break_down a,wo_po_acc_po_info c   where b.job_no=a.job_no_mst  and a.id=c.po_break_down_id and c.status_active=1  and b.company_name=$data[0] and b.is_deleted=0 $buyer_name $job_no_cond $order_no ORDER BY b.job_no";
	//echo $sql;
	$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
	$arr=array(1=>$buyer);
	
	echo  create_list_view("list_view", "Job No,Buyer,Style Ref.,Order No,Actual Po", "110,110,150,130,100","610","350",0, $sql, "js_set_value", "id,acc_po_no", "", 1, "0,buyer_name,0,0,0,0", $arr , "job_no_prefix_num,buyer_name,style_ref_no,po_number,acc_po_no", "fabric_pre_costing_details_report_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0','') ;
	disconnect($con);
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
	if ($data[2]==0) $job_cond=""; else $job_cond=" and job_no_prefix_num='$data[2]'";
	
	$cbo_year=str_replace("'","",$cbo_year);
	if($db_type==0)
	{
		if(trim($data[3])!=0) $year_cond=" and YEAR(insert_date)=$data[3]"; else $year_cond="";
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(insert_date,'YYYY')";
		if(trim($data[3])!=0) $year_cond=" $year_field_con=$data[3]"; else $year_cond="";
	}
	
	if($db_type==0) $year_field="YEAR(insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year";
	else $year_field="";

	
	$sql ="select id,style_ref_no,job_no_prefix_num as job_prefix,$year_field from wo_po_details_master where $company_name $buyer_name $job_cond $year_cond"; 
	echo create_list_view("list_view", "Style Ref. No.,Job No,Year","200,100,100","450","310",0, $sql , "js_set_value", "id,style_ref_no", "", 1, "0", $arr, "style_ref_no,job_prefix,year", "","setFilterGrid('list_view',-1)","0","",1) ;	
	exit();	 
}

$tmplte=explode("**",$data);

if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;

if( $action=="report_generate" )
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	//echo $txt_style;die;
	$company_name_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$yarn_count_library=return_library_array( "select id,yarn_count from lib_yarn_count", "id","yarn_count");

	$report_type=str_replace("'","",$reporttype);
	//echo $report_type;
	//echo $cbo_search_date;die;
	//cbo_style_owner*cbo_team_name*cbo_deal_marchant
	$company_name=str_replace("'","",$cbo_company_name);
	$file_no=trim(str_replace("'","",$txt_file_no));
	$internal_ref=trim(str_replace("'","",$txt_internal_ref));
	$txt_job_id=trim(str_replace("'","",$txt_job_id));
	
	$cbo_style_owner=str_replace("'","",$cbo_style_owner);
	$cbo_search_date=str_replace("'","",$cbo_search_date);
	if($company_name==0) $company_name_cond=""; else $company_name_cond=" and a.company_name=".$company_name."";
	
	if($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='$internal_ref' ";
	if($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='$file_no' ";
	
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
		if(trim($cbo_year)!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year"; else $year_cond="";
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
 	$date_cond='';	$c_date_cond='';
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
			$c_date_cond=" and c.country_ship_date between '$start_date' and '$end_date'";
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

	
		
	$job_no=str_replace("'","",$txt_job_no);
	//echo $job_no;die;
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
	$order_id=str_replace("'","",$txt_order_id);
	$order_num=str_replace("'","",$txt_order_no);
	$order_id_cond_trans="";$order_no_cond="";
	if($order_id!="" && $order_id!=0) 
	{ 
		$order_id_cond_trans=" and b.id in ($order_id)";
	}
	else if($order_id=="" && $order_num!="") 
	{
		 $order_no_cond=" and  b.po_number in ('$order_num') ";
	}
	//else if ($order_num=="") $order_no_cond=""; else $order_no_cond=" and  b.po_number in ('$order_num') ";
	
	
	//$cbo_team_name=str_replace("'","",$cbo_team_name);
	//$cbo_team_member=str_replace("'","",$cbo_team_member);
	
	//if($cbo_team_name==0) $team_name_cond=""; else $team_name_cond=" and a.team_leader='$cbo_team_name'";
	//if($cbo_team_member==0) $team_member_cond=""; else $team_member_cond=" and a.dealing_marchant='$cbo_team_member'";
	
				
	 $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	 $comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	 $color_library=return_library_array( "select id, color_name from lib_color ", "id", "color_name"  );
	 $size_library=return_library_array( "select id, size_name from lib_size", "id", "size_name"  );


	 $sql_gsm="select b.gsm_weight,b.job_no,b.body_part_id from wo_po_details_master a,wo_pre_cost_fabric_cost_dtls b where a.job_no=b.job_no and b.body_part_id in(1,20) $company_name_cond $job_no_cond $year_cond $buyer_id_cond";
	$sql_gsm_data=sql_select($sql_gsm);
	foreach($sql_gsm_data as $row)
	{
		if($body_part_id==1) $gsm_weight_top=$sql_po_row[csf('gsm_weight')];
		if($body_part_id==20) $gsm_weight_bottom=$sql_po_row[csf('gsm_weight')];
	}
	  //$gsm_weight_top=return_field_value("gsm_weight", "wo_pre_cost_fabric_cost_dtls", "job_no=$txt_job_no and body_part_id=1");
	// $gsm_weight_bottom=return_field_value("gsm_weight", "wo_pre_cost_fabric_cost_dtls", "job_no=$txt_job_no and body_part_id=20");
	 $po_qty=0;
	 $po_plun_cut_qty=0;
	 $total_set_qnty=0;
 $sql_po="select a.id,a.job_no, a.buyer_name,a.total_set_qnty,a.avg_unit_price,a.style_ref_no,b.id,b.pub_shipment_date,b.po_number,c.item_number_id,c.country_id,c.color_number_id,c.size_number_id,c.order_quantity , c.plan_cut_qnty  from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $company_name_cond $job_no_cond $year_cond  $date_cond $order_id_cond_trans $order_status_cond $order_no_cond $internal_ref_cond $file_no_cond $buyer_id_cond order by b.id"; 
	$sql_po_data=sql_select($sql_po);
	foreach($sql_po_data as $sql_po_row)
	{
		$po_qty+=$sql_po_row[csf('order_quantity')];
		$po_plun_cut_qty+=$sql_po_row[csf('plan_cut_qnty')];
		$total_set_qnty=$sql_po_row[csf('total_set_qnty')];
		$txt_job_no="'".$sql_po_row[csf('job_no')]."'";
		
		$job_in_orders .= $sql_po_row[csf('po_number')].",";
		$job_no_full .= $sql_po_row[csf('job_no')].",";
		$txt_order_no_id .= $sql_po_row[csf('id')].",";
		$item_number_ids .= $sql_po_row[csf('item_number_id')].",";
		$style_ref_no .= $sql_po_row[csf('style_ref_no')].",";
		$job_id .= $sql_po_row[csf('id')].",";
		$buyer_name_id= $sql_po_row[csf('buyer_name')];
		$avg_unit_price= $sql_po_row[csf('avg_unit_price')];
		$pub_shipment_date.= change_date_format($sql_po_row[csf('pub_shipment_date')]).',';
		$po_number_arr[$sql_po_row[csf('id')]]=$sql_po_row[csf('po_number')];
		$total_set_qnty=$sql_po_row[csf('total_set_qnty')];
	}
	$job_ids=rtrim($job_id,',');
	$pub_shipment_date=rtrim($pub_shipment_date,',');
	$job_in_orders=rtrim($job_in_orders,',');$txt_order_no_id=rtrim($txt_order_no_id,',');
	$pub_shipment_dates=implode(",",array_unique(explode(",",$pub_shipment_date)));
	$job_ids=implode(",",array_unique(explode(",",$job_ids)));
	$job_in_orders=implode(", ",array_unique(explode(",",$job_in_orders)));
	$txt_order_no_id=implode(",",array_unique(explode(",",$txt_order_no_id)));
 $sql = "select a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.gmts_item_id, a.order_uom, a.job_quantity, a.ship_mode, a.avg_unit_price, a.total_price, b.costing_per, b.budget_minute, b.exchange_rate, b.approved, b.sew_smv, b.sew_effi_percent, c.fab_knit_req_kg, c.fab_knit_fin_req_kg, c.fab_woven_req_yds, c.fab_woven_fin_req_yds, c.fab_yarn_req_kg from wo_po_details_master a, wo_pre_cost_mst b left join wo_pre_cost_sum_dtls c on  b.job_no=c.job_no where a.job_no=b.job_no and a.status_active=1 and a.id in($job_ids) $job_no_cond $company_name_cond $buyer_id_cond $year_cond  order by a.job_no";


	$data_array=sql_select($sql);
	//$buyer_name_id=$data_array[0][csf('buyer_name')];
	$job_no=$data_array[0][csf('job_no')];
	$tot_po_qty=$po_qty;
	$tot_po_value=$data_array[0][csf('total_price')];
	$exchange_rate=$data_array[0][csf('exchange_rate')];
	$tot_perDznValue=($tot_po_value/$tot_po_qty)*12;
		ob_start(); 
	 ?>
	 <div  class="scroll_div_inner">
		<div style="width:850px; font-size:20px; font-weight:bold" align="center"><? echo $comp[str_replace("'","",$cbo_company_name)]; ?></div>
   		<div style="width:850px; font-size:14px; font-weight:bold" align="center">Fabric Pre-Costing Details:</div>
		 <table class="rpt_table">
		 <tr>
		 <td>
			 <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px" rules="all">
                	<tr>
                    	<td width="80">Job Number</td>
                        <td width="80"><b><? $job_nos=rtrim($job_no_full,','); echo implode(",",array_unique(explode(',',$job_nos)));?></b></td>
                        <td width="90">Buyer</td>
                        <td width="100"><b><? echo $buyer_arr[$buyer_name_id]; ?></b></td>
                        <td width="80">Plan Cut Qnty</td>
                        <? 
							$item_number_idss=rtrim($item_number_ids,',');
							$gmts_item_name="";
							$gmts_item=array_unique(explode(',',$item_number_idss));
							for($g=0;$g<=count($gmts_item); $g++)
							{
								$gmts_item_name.= $garments_item[$gmts_item[$g]].",";
							}
						?>
                        <td width="100"><b><? echo $po_plun_cut_qty; ?></b></td>
                    </tr>
                    <tr>
                    	<td>Order Qty. </td>
                        <td><b><? echo $tot_po_qty." ". $unit_of_measurement[$data_array[0][csf('order_uom')]]; ?></b></td>
						<td>Style Ref. </td>
                        <td><b><? $style_ref_nos=rtrim($style_ref_no,',');echo implode(",",array_unique(explode(',',$style_ref_nos))); ?></b></td>
                        <td>Price Per Unit</td>
                        <td><b><?  echo $avg_unit_price; ?></b></td>
                    </tr>
                    <tr>
                    	<td>Order Numbers</td>
                        <td colspan="5"><? echo $job_in_orders; ?></td>
                    </tr>
                    <tr>
                    	<td>Garments Item</td>
                        <td colspan="3"><b><? echo $gmts_item_name;?></b></td>
                        <td>Shipment Date</td>
                        <td><b><? echo $pub_shipment_dates;?></b></td>
                    </tr>
                    <tr>
                    	<td>Budget Minute</td>
                        <td><b><? echo $data_array[0][csf('budget_minute')]; ?></b></td>
                        <td colspan="4"  style="font-size:18px;"><font color="#FF0000"><b><? if( $row[csf("approved")]==1 || $row[csf("approved")]==3){echo "This Job is Approved ";} else {echo "";} ?></b></font></td>
                    </tr>
                    <tr>
                    <td>Costing Date</td>
                        <td><b><? echo $costing_date; ?> </b></td>
                        <td>Ship Mode</td>
                        <td colspan="3"><b><? echo $shipment_mode[$data_array[0][csf('ship_mode')]]; ?></b></td>
                    </tr>
                </table>
				</td>
				<td width="350"  valign="middle">
				  <?
     	 // image show here  -------------------------------------------
		// echo "SELECT image_location FROM common_photo_library where master_tble_id in($txt_job_no) and form_name='knit_order_entry' and file_type=1";
              $nameArray_img =sql_select("SELECT image_location FROM common_photo_library where master_tble_id in($txt_job_no) and form_name='knit_order_entry' and file_type=1");?>
			  <div style="margin:15px 5px;float:left;width:350px" >
				<? foreach($nameArray_img AS $inf){ ?>
					<img  src='../../../<? echo $inf[csf("image_location")]; ?>' height='120' width='100' />
				<?  } ?>
			  </div>
				</td>
				</tr>
				 </table>
				<br/>
				<div id="div_size_color_matrix" style="float:left; max-width:1000;">
            
				<?
				$nameArray_size=sql_select( "select  size_number_id,min(id) as id,	min(size_order) as size_order from wo_po_color_size_breakdown where po_break_down_id in(".$txt_order_no_id.") and  job_no_mst=$txt_job_no and	is_deleted=0 and status_active=1 group by size_number_id order by size_order");
				//echo "select  size_number_id,min(id) as id,	min(size_order) as size_order from wo_po_color_size_breakdown where po_break_down_id in(".$txt_order_no_id.") and  job_no_mst=$txt_job_no and	is_deleted=0 and status_active=1 group by size_number_id order by size_order";
				?>
 				
 				<table  class="rpt_table"  border="1" align="left" cellpadding="0" width="750" cellspacing="0" rules="all" >
               	 <caption><b>Size and Color Breakdown</b></caption>
                    <tr>
                        <td style="border:1px solid black"><strong>Color/Size</strong></td>
                    <?  				
						foreach($nameArray_size  as $result_size)
                        {	     ?>
                        <td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
                    <?	}    ?>				
                        <td style="border:1px solid black; width:130px" align="center"><strong> Total Order Qty(Pcs)</strong></td>
                        <td style="border:1px solid black; width:80px" align="center"><strong> Excess %</strong></td>
                        <td style="border:1px solid black; width:130px" align="center"><strong> Total Plan Cut Qty(Pcs)</strong></td>
                    </tr>
                    <?
					$color_size_order_qnty_array=array();
					$color_size_qnty_array=array();
					$size_tatal=array();
					$size_tatal_order=array();
					for($c=0;$c<count($gmts_item); $c++)
				    {
					$item_size_tatal=array();
					$item_size_tatal_order=array();
					$item_grand_total=0;
					$item_grand_total_order=0;
					$nameArray_color=sql_select( "select  color_number_id,min(id) as id,min(color_order) as color_order from wo_po_color_size_breakdown where  item_number_id=$gmts_item[$c] and po_break_down_id in(".$txt_order_no_id.") and is_deleted=0 and status_active=1 group by color_number_id  order by color_order");
					//echo "select  color_number_id,min(id) as id,min(color_order) as color_order from wo_po_color_size_breakdown where  item_number_id=$gmts_item[$c] and po_break_down_id in(".$txt_order_no_id.") and is_deleted=0 and status_active=1 group by color_number_id  order by color_order";
					?>
                    <tr>
                    	<td style="border:1px solid black" colspan="<? echo count($nameArray_size)+3;?>"><strong><? echo $garments_item[$gmts_item[$c]];?></strong></td>
                    </tr>
                    <?
					foreach($nameArray_color as $result_color)
                    {						
                    ?>
                    <tr>
                        <td align="center" style="border:1px solid black"><? echo $color_library[$result_color[csf('color_number_id')]]; // echo $row_num_tr; ?></td>
                        <? 
						$color_total=0; $color_total_order=0;
						
						foreach($nameArray_size  as $result_size)
						{
						$nameArray_color_size_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as  order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$result_color[csf('color_number_id')]."  and item_number_id=$gmts_item[$c] and  status_active=1 and is_deleted =0");                          
						foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                        {
                        ?>
                            <td style="border:1px solid black; text-align:right">
							<? 
								if($result_color_size_qnty[csf('plan_cut_qnty')]!= "")
								{
									 echo fn_number_format($result_color_size_qnty[csf('order_quantity')],0);
									 $color_total += $result_color_size_qnty[csf('plan_cut_qnty')] ;
									 $color_total_order += $result_color_size_qnty[csf('order_quantity')] ;
									 $item_grand_total+=$result_color_size_qnty[csf('plan_cut_qnty')];
									 $item_grand_total_order+=$result_color_size_qnty[csf('order_quantity')];
								     $grand_total +=$result_color_size_qnty[csf('plan_cut_qnty')];
									 $grand_total_order +=$result_color_size_qnty[csf('order_quantity')];
									 
									 $color_size_qnty_array[$result_size[csf('size_number_id')]][$result_color[csf('color_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')];
									 $color_size_order_qnty_array[$result_size[csf('size_number_id')]][$result_color[csf('color_number_id')]]=$result_color_size_qnty[csf('order_quantity')];
									 if (array_key_exists($result_size[csf('size_number_id')], $size_tatal))
									 {
										$size_tatal[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('plan_cut_qnty')];
										$size_tatal_order[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('order_quantity')];
									 }
									 else
									 {
										$size_tatal[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')]; 
										$size_tatal_order[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('order_quantity')]; 
									 }
									 if (array_key_exists($result_size[csf('size_number_id')], $item_size_tatal))
									 {
										$item_size_tatal[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('plan_cut_qnty')];
										$item_size_tatal_order[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('order_quantity')];
									 }
									 else
									 {
										$item_size_tatal[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')]; 
										$item_size_tatal_order[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('order_quantity')]; 
									 }
								}
								else echo "0";
							 ?>
							</td>
                    <?   
						}
                        }
                        ?>
                          <td style="border:1px solid black; text-align:right"><?  echo fn_number_format(round($color_total_order),0); ?></td>
                          
                         <td style="border:1px solid black; text-align:right"><? $excexss_per=($color_total-$color_total_order)/$color_total_order*100; echo fn_number_format($excexss_per,2)." %"; ?>
                         </td>
                        <td style="border:1px solid black; text-align:right"><? echo fn_number_format(round($color_total),0); ?></td>
                    </tr>
                    <?
                    }
					?>
                        <td align="center" style="border:1px solid black"><strong>Sub Total</strong></td>
                        <?
						foreach($nameArray_size  as $result_size)
                        {
                        ?>
                        <td style="border:1px solid black;  text-align:right"><? echo $item_size_tatal_order[$result_size[csf('size_number_id')]];  ?></td>
                        <?
                        }
                        ?>
                        <td  style="border:1px solid black;  text-align:right"><?  echo fn_number_format(round($item_grand_total_order),0); ?></td>
                        <td  style="border:1px solid black;  text-align:right"><? $excess_item_gra_tot=($item_grand_total-$item_grand_total_order)/$item_grand_total_order*100; echo fn_number_format($excess_item_gra_tot,2)." %"; ?></td>
                        <td  style="border:1px solid black;  text-align:right"><?  echo fn_number_format(round($item_grand_total),0); ?></td>
                    </tr>
                    <?
					}
                    ?>
                     <tr>
                        <td style="border:1px solid black" align="center" colspan="<? echo count($nameArray_size)+3; ?>"><strong>&nbsp;</strong></td>
                        </tr>
                    <tr>
                    <tr>
                        <td align="center" style="border:1px solid black"><strong>Grand Total</strong></td>
                        <?
						foreach($nameArray_size  as $result_size)
                        {
                        ?>
                        <td style="border:1px solid black;  text-align:right"><? echo $size_tatal_order[$result_size[csf('size_number_id')]];  ?></td>
                        <?
                        }
                        ?>
                        <td  style="border:1px solid black;  text-align:right"><?  echo fn_number_format(round($grand_total_order),0); ?></td>
                        <td  style="border:1px solid black;  text-align:right"><? $excess_gra_tot= ($grand_total-$grand_total_order)/$grand_total_order*100; echo fn_number_format($excess_gra_tot,2)." %"; ?></td>
                        <td  style="border:1px solid black;  text-align:right"><?  echo fn_number_format(round($grand_total),0); ?></td>
                    </tr>
                </table>
               <!-- </fieldset>-->
                </div>
				<br/><br/>
				<div>
				<!--Fabric Production Start-->
	 <?
	 $costing_per=""; $costing_per_qnty=0;
	 $costing_per_id=return_field_value( "costing_per", "wo_pre_cost_mst","job_no in($txt_job_no)");
	 if($costing_per_id==1)
		{
			$costing_per="1 Dzn";
			$costing_per_qnty=12;
		}
		if($costing_per_id==2)
		{
			$costing_per="1 Pcs";
			$costing_per_qnty=1;
		}
		if($costing_per_id==3)
		{
			$costing_per="2 Dzn";
			$costing_per_qnty=24;
		}
		if($costing_per_id==4)
		{
			$costing_per="3 Dzn";
			$costing_per_qnty=36;
		}
		if($costing_per_id==5)
		{
			$costing_per="4 Dzn";
			$costing_per_qnty=48;
		}
	//$process_loss_method=return_field_value( "process_loss_method", "wo_pre_cost_fabric_cost_dtls","job_no=$txt_job_no");
	
	$nameArray_fab_uom = sql_select("select a.process_loss_method,a.uom,a.fabric_source FROM wo_pre_cost_fabric_cost_dtls a WHERE a.job_no in($txt_job_no)");
	foreach($nameArray_fab_uom as $row){
	if($row[csf('fabric_source')]==2)
	{
		$uom_wise_arr[$row[csf('uom')]]=$row[csf('uom')];
	}
		$process_loss_method=$row[csf('process_loss_method')];
	}
	//print_r($uom_wise_arr);
	
			
 $fabric_source=1;
 //if($fabric_source==1)
 //{

 //$costing_per=$cos_per_arr[str_replace("'","",$txt_job_no)];

 $nameArray_gmts_sizes = sql_select("select a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight, b.dia_width, c.size_number_id,c.size_order FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b  
	WHERE 
	a.job_no=b.job_no and
	a.id=b.pre_cost_fabric_cost_dtls_id and
	c.job_no_mst=a.job_no and 
	c.id=b.color_size_table_id and
	a.job_no =$txt_job_no and c.po_break_down_id in(".$txt_order_no_id.")  and a.fabric_source=1
	group by a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width,c.size_number_id,c.size_order order by c.size_order");
	
	$GmtsSizesArr=array();
	foreach($nameArray_gmts_sizes as $sizes_row){
		$GmtsSizesArr[$sizes_row[csf('body_part_id')]][$sizes_row[csf('color_type_id')]][$sizes_row[csf('construction')]][$sizes_row[csf('composition')]][$sizes_row[csf('gsm_weight')]][$sizes_row[csf('dia_width')]][$sizes_row[csf('size_number_id')]]=$size_library[$sizes_row[csf('size_number_id')]];
	}
	$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
	
	$nameArray_gmts_sizes = sql_select("select a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight, b.dia_width, c.count_id 
	FROM wo_pre_cost_fabric_cost_dtls a, 
	wo_pre_cost_fab_yarn_cost_dtls c, 
	wo_pre_cos_fab_co_avg_con_dtls b
	
	WHERE 
	a.job_no=b.job_no and
	a.id=b.pre_cost_fabric_cost_dtls_id and
	c.job_no=a.job_no and  a.fabric_source=1 and 
	a.id=c.fabric_cost_dtls_id and
	b.pre_cost_fabric_cost_dtls_id=c.fabric_cost_dtls_id and
	a.job_no =$txt_job_no
	group by a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width,c.count_id");

	$countArr=array();
	foreach($nameArray_gmts_sizes as $sizes_row){
		$countArr[$sizes_row[csf('body_part_id')]][$sizes_row[csf('color_type_id')]][$sizes_row[csf('construction')]][$sizes_row[csf('composition')]][$sizes_row[csf('gsm_weight')]][$sizes_row[csf('dia_width')]][$sizes_row[csf('count_id')]]=$yarn_count_arr[$sizes_row[csf('count_id')]];
	}
	
	$nameArray_fabric_description= sql_select("select min(a.id) as fabric_cost_dtls_id,a.item_number_id, max(a.lib_yarn_count_deter_id) as determin_id,a.body_part_id,a.uom,a.color_type_id,a.fabric_source, a.construction, a.composition, a.gsm_weight,min(a.width_dia_type) as width_dia_type, b.dia_width,avg(b.cons) as cons  , avg(b.process_loss_percent) as process_loss_percent , avg(b.requirment) as requirment,c.color_number_id FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b   
	WHERE a.job_no=b.job_no and
	a.id=b.pre_cost_fabric_cost_dtls_id and
	c.job_no_mst=a.job_no and a.fabric_source=1 and
	c.id=b.color_size_table_id and c.status_active=1 and c.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and
	a.job_no =$txt_job_no and c.po_break_down_id in(".$txt_order_no_id.") and b.cons>0 
	group by a.body_part_id,a.uom,a.item_number_id,a.color_type_id,a.fabric_source,a.construction,a.composition,a.gsm_weight,b.dia_width,c.color_number_id order by fabric_cost_dtls_id,a.body_part_id,b.dia_width");
	
	
	$condition= new condition();
		if(str_replace("'","",$txt_job_no) !=''){
			$condition->job_no("=$txt_job_no");
		}
		if(str_replace("'","",$txt_order_no_id) !=''){
			$condition->po_id("in($txt_order_no_id)");
		}
		$condition->init();
		$fabric= new fabric($condition);$yarn= new yarn($condition);
		//$fab_data_qty_arr=$fabric->getQtyArray_by_orderGmtscolorAndBodypart_knitAndwoven_greyAndfinish();
		$fab_data_qty_arr=$fabric->getQtyArray_by_orderFabriccostidAndGmtscolor_knitAndwoven_greyAndfinish();
		$fab_data_qty_body_arr=$fabric->getQtyArray_by_OrderFabriccostidGmtscolorAndDiaWidth_knitAndwoven_greyAndfinish();
		//print_r($fab_data_qty_arr);
		$fab_data_amt_arr=$fabric->getAmountArray_by_orderGmtscolorAndBodypart_knitAndwoven_greyAndfinish();
		$conversion= new conversion($condition);
		//echo $conversion->getQuery();die;
		$conversion_costing_arr_process=$conversion->getAmountArray_by_job();
		//print_r($fab_data_amt_arr);
	 ?>
     <table class="rpt_table" rules="all"  width="100%"  border="2" cellpadding="0" cellspacing="0" >
	 <caption> <label style="font-size:x-large"><b>Fabric Source Production </b></label></caption>
     <tr align="center">
     <td colspan="3" align="left"><b>Item Name</b></td>
        <? 
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('item_number_id')] == "")  echo "<td  colspan='2'>&nbsp</td>";	
			else echo "<td  colspan='2'>". $garments_item[$result_fabric_description[csf('item_number_id')]]."</td>";			
		}
		?>
       </tr>
     <tr align="center">
     <td colspan="3" align="left"><b>Body Part</b></td>
        <? 
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('body_part_id')] == "")  echo "<td  colspan='2'>&nbsp</td>";	
			else    echo "<td  colspan='2'>". $body_part[$result_fabric_description[csf('body_part_id')]]."</td>";			
		}
		?>
        <td  rowspan="9" width="50"><p>Total  Finish Fabric (<? echo $unit_of_measurement[$result_fabric_description[csf('uom')]];?>)</p></td> 
        <td  rowspan="9" width="50"><p>Total Grey Fabric (<? echo $unit_of_measurement[$result_fabric_description[csf('uom')]];?>)</p></td>
        <td  rowspan="9" width="50"><p>Process Loss % </p></td>
       </tr>
     <tr align="center"><td colspan="3" align="left"><b>Color Type</b></td>
        <? 
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('color_type_id')] == "")  echo "<td  colspan='2'>&nbsp</td>";	
			else  echo "<td  colspan='2'>". $color_type[$result_fabric_description[csf('color_type_id')]]."</td>";			
		}
		?>
       </tr>  
        <tr align="center"><td colspan="3" align="left"><b>Fabric Construction</b></td>
        <? 
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('construction')] == "")  echo "<td  colspan='2'>&nbsp</td>";	
			else  echo "<td  colspan='2'>". $result_fabric_description[csf('construction')]."</td>";			
		}
		?>
       </tr>       
        <tr align="center"><td   colspan="3" align="left"><b>Fabric Composition / Yarn Type</b></td>
        <? 
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('composition')] == "")   echo "<td colspan='2' >&nbsp</td>";
			else  echo "<td colspan='2' >".$result_fabric_description[csf('composition')]."</td>";			
		}
		?>
       </tr>
       <tr align="center"><td  colspan="3" align="left"><b>GSM</b></td>
        <? 
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('gsm_weight')] == "")   echo "<td colspan='2'>&nbsp</td>";
			else echo "<td colspan='2' align='center'>". $result_fabric_description[csf('gsm_weight')]."</td>";			
		}
		?>
       </tr>
       <tr align="center"><td   colspan="3" align="left"><b>Yarn Count</b></td>
        <? 
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
		$cArr=$countArr[$result_fabric_description[csf('body_part_id')]][$result_fabric_description[csf('color_type_id')]][$result_fabric_description[csf('construction')]][$result_fabric_description[csf('composition')]][$result_fabric_description[csf('gsm_weight')]][$result_fabric_description[csf('dia_width')]];
			$Gcount=implode(",",$cArr);
			if( $Gcount == "")   echo "<td colspan='2'>&nbsp</td>";
			else  echo "<td colspan='2' align='center'>".$Gcount."</td>";			
		}
		?>
       </tr>
       <tr align="center"><td   colspan="3" align="left"><b>Gmts Sizes</b></td>
        <? 
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			$sizeArr=$GmtsSizesArr[$result_fabric_description[csf('body_part_id')]][$result_fabric_description[csf('color_type_id')]][$result_fabric_description[csf('construction')]][$result_fabric_description[csf('composition')]][$result_fabric_description[csf('gsm_weight')]][$result_fabric_description[csf('dia_width')]];
			$Gsize=implode(",",$sizeArr);
			if( $Gsize == "")   echo "<td colspan='2'>&nbsp</td>";
			else  echo "<td colspan='2' align='center'>".$Gsize."</td>";			
		}
		?>
       </tr>
       <tr align="center"><td   colspan="3" align="left"><b>Dia/Width (Inch)</b></td>
        <? 
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='2'>&nbsp</td>";
			else  echo "<td colspan='2' align='center'>".$result_fabric_description[csf('dia_width')].",".$fabric_typee[$result_fabric_description[csf('width_dia_type')]]."</td>";			
		}
		?>
        
       </tr>
       <tr align="center"><td   colspan="3" align="left"><b>Consumption For <? echo $costing_per; ?></b></td>
        <? 
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('requirment')] == "")   echo "<td colspan='2'>&nbsp</td>";
			else  echo "<td colspan='2' align='center'>Fin: ".fn_number_format($result_fabric_description[csf('cons')],4).", Grey: ".fn_number_format($result_fabric_description[csf('requirment')],4)."</td>";			
		}
		?>
        
       </tr>
       <tr>
       <th  colspan="<? echo  count($nameArray_fabric_description)*2+3; ?>" align="left" style="height:30px">&nbsp;</th>
       </tr>
       <tr>
            <td  width="120" align="left"><b>Fabric Color</b></td>
            <td  width="120" align="left"><b>Body Color</b></td>
            <td  width="120" align="left"><b>Lab Dip No</b></td>
        <? 
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			  echo "<td width='50'><b>Finish</b></td><td width='50'><b>Grey</b></td>";			
		}
		?>
       
       </tr>
       <?
	      //fab_nature_id,fabric_source //and a.fab_nature_id=$cbo_fabric_natu and a.fabric_source =$cbo_fabric_source
		  $gmt_color_library=array();//fabric_cost_dtls_id
		  $gmt_color_data=sql_select("select a.body_part_id,b.gmts_color_id, b.contrast_color_id  FROM  wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_color_dtls b 
		  WHERE a.id=b.pre_cost_fabric_cost_dtls_id  and  a.job_no in($txt_job_no) and a.color_size_sensitive=3 ");
		 
		  foreach( $gmt_color_data as $gmt_color_row)
		  {
			$gmt_color_library[$gmt_color_row[csf("contrast_color_id")]][$gmt_color_row[csf("gmts_color_id")]]=$gmt_color_row[csf("gmts_color_id")];
			$gmt_color_library2[$gmt_color_row[csf("gmts_color_id")]].=$color_library[$gmt_color_row[csf("contrast_color_id")]].',';
			$body_gmt_color_library[$gmt_color_row[csf("body_part_id")]][$gmt_color_row[csf("gmts_color_id")]]=$gmt_color_row[csf("contrast_color_id")];
		  }
 //	        $grand_total_fin_fab_qnty=0;
			$grand_total_grey_fab_qnty=0;
			$grand_totalcons_per_finish=0;
			$grand_totalcons_per_grey=0;
										  //body_part_id
			$color_wise_wo_sql=sql_select("select gmts_color_id
										  FROM  wo_pre_cos_fab_co_color_dtls WHERE job_no =$txt_job_no  group by gmts_color_id");
										 
			  if(count($color_wise_wo_sql)<=0)
			  {
				   $color_wise_wo_sql=sql_select("select color_number_id as gmts_color_id
										  FROM  wo_po_color_size_breakdown WHERE job_no_mst =$txt_job_no and po_break_down_id in(".$txt_order_no_id.")  group by color_number_id");
			  }
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
		?> 
			<tr>
            <td  width="120" align="left">
			<?
			$body_gmt_color_library[$gmt_color_row[csf("body_part_id")]][$color_wise_wo_result[csf("gmts_color_id")]];
			$fabColor=rtrim($gmt_color_library2[$color_wise_wo_result[csf("gmts_color_id")]],',');
			//$fab_color=$gmt_color_library2[$color_wise_wo_result[csf("gmts_color_id")]];
			//$fabColor=rtrim($color_wise_wo_result[csf("gmts_color_id")],',');
			if($fabColor!="") echo $fabColor;// $color_library[$fab_color];
			else echo $color_library[$color_wise_wo_result[csf('gmts_color_id')]];
			?>
            </td>
            <td>
            <?
				$gmt_color_id=$color_wise_wo_result[csf('gmts_color_id')];//$gmt_color_library2[$color_wise_wo_result[csf('fabric_color_id')]];
				echo $color_library[$gmt_color_id];
			?>
            </td>
            <td  width="120" align="left">
			<? 
			$lapdip_no="";
			$lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst=$txt_job_no and approval_status=3 and color_name_id=".$color_wise_wo_result[csf('gmts_color_id')]."");
			if($lapdip_no=="") echo "&nbsp;"; echo $lapdip_no; 
			?>
            </td>
            <?
			$total_fin_fab_qnty=0;
			$total_grey_fab_qnty=0;//b.color_number_id='".$color_wise_wo_result[csf('fabric_color_id')]."' and
			
			foreach($nameArray_fabric_description as $result_fabric_description)
		    {
					
			?>
			<td width='50' align='right'>
			
			<? 
			$fabric_source_id=$result_fabric_description[csf('fabric_source')];
			//echo $color_wise_wo_result_qnty[csf('po_id')].'='.$gmt_color_id.'='.$result_fabric_description[csf('uom')];
			$expo_id=array_filter(array_unique(explode(",",$txt_order_no_id))); $fab_knit_fin_qty=0; $fab_knit_grey_qty=0;
			foreach($expo_id as $poids)
			{
				$fab_knit_fin_qty+=$fab_data_qty_body_arr['knit']['finish'][$poids][$result_fabric_description[csf('fabric_cost_dtls_id')]][$gmt_color_id][$result_fabric_description[csf('dia_width')]][$result_fabric_description[csf('uom')]]+$fab_data_qty_body_arr['woven']['finish'][$poids][$result_fabric_description[csf('fabric_cost_dtls_id')]][$gmt_color_id][$result_fabric_description[csf('dia_width')]][$result_fabric_description[csf('uom')]];
				
				$fab_knit_grey_qty+=$fab_data_qty_arr['knit']['grey'][$poids][$result_fabric_description[csf('fabric_cost_dtls_id')]][$gmt_color_id][$result_fabric_description[csf('uom')]]+$fab_data_qty_arr['woven']['grey'][$poids][$result_fabric_description[csf('fabric_cost_dtls_id')]][$gmt_color_id][$result_fabric_description[csf('uom')]];
				
			} 
			
			$tot_fab_fin_qty=$fab_knit_fin_qty;
			if($tot_fab_fin_qty!="")
			{
				echo fn_number_format($tot_fab_fin_qty,2);
				$fab_fin_qty_descrp_wise_arr[$result_fabric_description[csf('fabric_cost_dtls_id')]][$result_fabric_description[csf('item_number_id')]][$result_fabric_description[csf('body_part_id')]]+=$tot_fab_fin_qty;
				$total_fin_fab_qnty+=$tot_fab_fin_qty;
			}
			?>
            </td>
            <td width='50' align='right' > 
			
			<? 
			if($fab_knit_grey_qty!="")
			{
				if($process_loss_method==1)
				{
					$process_loss_percent=(($fab_knit_grey_qty-$tot_fab_fin_qty)/$tot_fab_fin_qty)*100;
				}
				
				if($process_loss_method==2)
				{
					$process_loss_percent=(($fab_knit_grey_qty-$tot_fab_fin_qty)/$fab_knit_grey_qty)*100;
				}
				echo fn_number_format($process_loss_percent,2)."%<hr>";
				echo fn_number_format($fab_knit_grey_qty,2); 
				$total_grey_fab_qnty+=$fab_knit_grey_qty;
				$fab_grey_qty_descrp_wise_arr[$result_fabric_description[csf('fabric_cost_dtls_id')]][$result_fabric_description[csf('item_number_id')]][$result_fabric_description[csf('body_part_id')]]+=$fab_knit_grey_qty;
				
				
			}
			?>
            </td>
            <?
			}
			?>
            <td align="right"><? echo fn_number_format($total_fin_fab_qnty,2); $grand_total_fin_fab_qnty+=$total_fin_fab_qnty;?></td>
            <td align="right"><? echo fn_number_format($total_grey_fab_qnty,2); $grand_total_grey_fab_qnty+=$total_grey_fab_qnty;?></td>
            
            <td align="right">
            <?
			if($process_loss_method==1)
			{
				$process_percent=(($total_grey_fab_qnty-$total_fin_fab_qnty)/$total_fin_fab_qnty)*100;

			}
			
			if($process_loss_method==2)
			{
				$process_percent=(($total_grey_fab_qnty-$total_fin_fab_qnty)/$total_grey_fab_qnty)*100;
			}
			echo fn_number_format($process_percent,2);
			
			?>
            </td>
            </tr>
         <?
		}
		?>
        <tr style=" font-weight:bold">
        <th  width="120" align="left">&nbsp;</th>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left"><strong>Total</strong></td>
        <?
			foreach($nameArray_fabric_description as $result_fabric_description)
		    {
				
				$ttl_fab_knit_fin_qty=$fab_fin_qty_descrp_wise_arr[$result_fabric_description[csf('fabric_cost_dtls_id')]][$result_fabric_description[csf('item_number_id')]][$result_fabric_description[csf('body_part_id')]];
				$ttl_fab_knit_grey_qty=$fab_grey_qty_descrp_wise_arr[$result_fabric_description[csf('fabric_cost_dtls_id')]][$result_fabric_description[csf('item_number_id')]][$result_fabric_description[csf('body_part_id')]];
				
			
			?>
			<td width='50' align='right'><?  echo fn_number_format($ttl_fab_knit_fin_qty,2) ;?></td><td width='50' align='right' > <? echo fn_number_format($ttl_fab_knit_grey_qty,2);?></td>
            <?
			}
			?>
            <td align="right"><? echo fn_number_format($grand_total_fin_fab_qnty,2);?></td>
            <td align="right"><? echo fn_number_format($grand_total_grey_fab_qnty,2);?></td>
            <td align="right">
            <?
            if($process_loss_method==1)// markup
			{
				$totalprocess_percent=(($grand_total_grey_fab_qnty-$grand_total_fin_fab_qnty)/$grand_total_fin_fab_qnty)*100;
			}
			
			if($process_loss_method==2) //margin
			{
				$totalprocess_percent=(($grand_total_grey_fab_qnty-$grand_total_fin_fab_qnty)/$grand_total_grey_fab_qnty)*100;
			}
			echo fn_number_format($totalprocess_percent,2);
			?>
            </td>
            </tr> 
            <tr style="font-weight:bold">
        <!--<td  width="120" align="left">&nbsp;</td>-->
        <th  width="120" align="left">&nbsp;</th>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left"><strong>Consumption For <? echo $costing_per; ?></strong></td>
        <?
			foreach($nameArray_fabric_description as $result_fabric_description)
		    {
				
				
			?>
			<td width='50' align='right'><?  //echo fn_number_format(($color_wise_wo_result_qnty['fin_fab_qnty']/$grand_total)*($total_set_qnty*$costing_per_qnty),4) ;?></td><td width='50' align='right' > <? //echo fn_number_format(($color_wise_wo_result_qnty['grey_fab_qnty']/$grand_total)*($total_set_qnty*$costing_per_qnty),4);?></td>
            <?
			}
			?>
            <td align="right">
			<? 
			//echo $tot_po_qty.'=='.$total_set_qnty;
			echo fn_number_format(($grand_total_fin_fab_qnty/$tot_po_qty)*($total_set_qnty*$costing_per_qnty),4);
			$grand_total_fin_fab_qnty_dzn=fn_number_format(($grand_total_fin_fab_qnty/$tot_po_qty)*($total_set_qnty*$costing_per_qnty),4);
			
			
			?>
            </td>
            <td align="right"><? echo fn_number_format(($grand_total_grey_fab_qnty/$tot_po_qty)*($total_set_qnty*$costing_per_qnty),4);$grand_total_grey_fab_qnty_dzn=fn_number_format(($grand_total_grey_fab_qnty/$tot_po_qty)*($total_set_qnty*$costing_per_qnty),4)?></td>
            <td align="right">
            <?
            if($process_loss_method==1)
			{
				$totalprocess_percent_dzn=(($grand_total_grey_fab_qnty_dzn-$grand_total_fin_fab_qnty_dzn)/$grand_total_fin_fab_qnty_dzn)*100;
			}
			
			if($process_loss_method==2)
			{
				$totalprocess_percent_dzn=(($grand_total_grey_fab_qnty_dzn-$grand_total_fin_fab_qnty_dzn)/$grand_total_grey_fab_qnty_dzn)*100;
			}
			echo fn_number_format($totalprocess_percent_dzn,2);
			?>
            </td>
            </tr> 
    </table>
			</div> <!--Production End-->
			<div>
				<?
					$order_plan_qty_arr=array();
			$color_wise_wo_sql_qnty=sql_select( "select color_number_id, size_number_id, sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  job_no_mst in($txt_job_no) and po_break_down_id in(".$txt_order_no_id.") and status_active=1 and is_deleted =0 group by color_number_id, size_number_id"); 
			foreach($color_wise_wo_sql_qnty as $row)
			{
				$order_plan_qty_arr[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['plan']=$row[csf('plan_cut_qnty')];
				$order_plan_qty_arr[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['order']=$row[csf('order_quantity')];
			}
			
			$collar_cuff_percent_arr=array();
			$collar_cuff_body_arr=array();
			$collar_cuff_color_arr=array();
			$collar_cuff_size_arr=array();
			$collar_cuff_item_size_arr=array();
			$color_size_sensitive_arr=array();
			
			$collar_cuff_sql="select a.id, a.color_size_sensitive, a.color_break_down, b.color_number_id, b.gmts_sizes, b.item_size, c.size_number_id,e.body_part_full_name, e.body_part_type 
			FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c, lib_body_part e 
			
			WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and a.job_no =$txt_job_no and c.po_break_down_id in(".$txt_order_no_id.") and a.body_part_id=e.id and a.fabric_source=1 and e.body_part_type in (40,50) and c.id=b.color_size_table_id and c.status_active=1 and c.is_deleted=0 order by  c.size_order";
			//echo $collar_cuff_sql;
			$collar_cuff_sql_res=sql_select($collar_cuff_sql);
			
			foreach($collar_cuff_sql_res as $collar_cuff_row)
			{
				$collar_cuff_percent_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('color_number_id')]][$collar_cuff_row[csf('gmts_sizes')]]=$collar_cuff_row[csf('colar_cuff_per')];
				$collar_cuff_body_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]]=$collar_cuff_row[csf('body_part_full_name')];
				$collar_cuff_size_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('size_number_id')]]=$collar_cuff_row[csf('size_number_id')];
				$collar_cuff_item_size_arr[$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('size_number_id')]][$collar_cuff_row[csf('item_size')]]=$collar_cuff_row[csf('item_size')];
				$color_size_sensitive_arr[$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('id')]][$collar_cuff_row[csf('color_number_id')]][$collar_cuff_row[csf('color_size_sensitive')]]=$collar_cuff_row[csf('color_break_down')];
				
			}
			//print_r($collar_cuff_percent_arr[40]) ;
			unset($collar_cuff_sql_res); $jk=0;
			foreach($collar_cuff_body_arr as $body_type=>$body_name)
			{
				foreach($body_name as $body_val)
				{
					$count_collar_cuff=count($collar_cuff_size_arr[$body_type][$body_val]);
					$pre_grand_tot_collar=0; $pre_grand_tot_collar_order_qty=0; 
					if($jk==0 || (($jk/2)==0)) {
					?>
                    <div style="max-height:1330px; overflow:auto; float:left; padding-top:5px; margin-left:5px; margin:5px; margin-bottom:5px; position:relative;"> <? } ?>
					<table width="625" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                        <tr>
                        	<td colspan="<? echo $count_collar_cuff+3; ?>" align="center"><b><? echo $body_val; ?> - Color Size Brakedown in Pcs.</b></td>
                        </tr>
                        <tr>
                            <td width="100">Size <? echo $jk; ?></td>
								<?  
                                foreach($collar_cuff_size_arr[$body_type][$body_val]  as $size_number_id)
                                {	     
									?>
									<td align="center" style="border:1px solid black"><strong><? echo $size_library[$size_number_id];?></strong></td>
									<?	
                                }    
                                ?>	
                            <td width="60" rowspan="2" align="center"><strong>Total</strong></td> 
                            <td rowspan="2" align="center"><strong>Extra %</strong></td>
                        </tr>
                        <tr>
                            <td style="font-size:12px"><? echo $body_val; ?> Size</td>
                            <?
                            foreach($collar_cuff_item_size_arr[$body_val]  as $size_number_id=>$size_number)
                            {	
								if(count($size_number)>0) 
								{
									 foreach($size_number  as $item_size=>$val) 
									 {   
										?>
										<td align="center" style="border:1px solid black"><strong><? echo $item_size;?></strong></td>
										<?
									 }
								}
								else 
								{
									?>
									<td align="center" style="border:1px solid black"><strong>&nbsp;</strong></td>
									<?
								}
                            }    
                            
                            $pre_size_total_arr=array();
                            foreach($color_size_sensitive_arr[$body_val] as $pre_cost_id=>$pre_cost_data)
                            {
								foreach($pre_cost_data as $color_number_id=>$color_number_data)
								{
									foreach($color_number_data as $color_size_sensitive=>$color_break_down)
									{
										$pre_color_total_collar=0;
										$pre_color_total_collar_order_qnty=0;
										$process_loss_method=$process_loss_method;
										$constrast_color_arr=array();
										if($color_size_sensitive==3)
										{
											$constrast_color=explode('__',$color_break_down);
											for($i=0;$i<count($constrast_color);$i++)
											{
												$constrast_color2=explode('_',$constrast_color[$i]);
												$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
											}
										}
										?> 
										<tr>
											<td>
												<?
                                                if( $color_size_sensitive==3)
                                                {
                                                    echo strtoupper ($constrast_color_arr[$color_number_id]) ;
                                                    $lab_dip_color_id=$lab_dip_color_arr[$pre_cost_id][$color_number_id];
                                                }
                                                else
                                                {
                                                    echo $color_library[$color_number_id];
                                                    $lab_dip_color_id=$color_number_id;
                                                }
                                                ?>
											</td>
											<?
											foreach($collar_cuff_size_arr[$body_type][$body_val] as $size_number_id)
											{
												?>
												<td align="center" style="border:1px solid black">
													<? $plan_cut=0; $collerqty=0; $collar_ex_per=0;
													if($body_type==50) $plan_cut=($order_plan_qty_arr[$color_number_id][$size_number_id]['plan'])*2;
													else $plan_cut=$order_plan_qty_arr[$color_number_id][$size_number_id]['plan'];
                                                    $ord_qty=$order_plan_qty_arr[$color_number_id][$size_number_id]['order'];
                                                    
                                                    $collar_ex_per=$collar_cuff_percent_arr[$body_type][$body_val][$color_number_id][$size_number_id];
                                                    // echo $collar_ex_per.'=';
												    if($body_type==50) { if($collar_ex_per==0 || $collar_ex_per=="") $collar_ex_per=$cuff_excess_percent; else $collar_ex_per=$collar_ex_per; }
                                                    else if($body_type==40) { if($collar_ex_per==0 || $collar_ex_per=="") $collar_ex_per=$colar_excess_percent; else $collar_ex_per=$collar_ex_per; }
                                                    $colar_excess_per=fn_number_format(($plan_cut*$collar_ex_per)/100,6,".",",");
                                                    $collerqty=($plan_cut+$colar_excess_per);
                                                    
                                                    //$collerqty=fn_number_format(($requirment/$costing_per_qnty)*$plan_cut,2,'.','');
                                                    
                                                    echo fn_number_format($collerqty); 
                                                    $pre_size_total_arr[$size_number_id]+=$collerqty;
                                                    $pre_color_total_collar+=$collerqty; 
                                                    $pre_color_total_collar_order_qnty+=$plan_cut; 
                                                    
                                                    //$pre_grand_tot_collar_order_qty+=$plan_cut; 
                                                    ?>
												</td>
												<?	
											}    
											?>	
											
											<td align="center"><? echo fn_number_format($pre_color_total_collar); ?></td>
											<td align="center"><? echo fn_number_format((($pre_color_total_collar-$pre_color_total_collar_order_qnty)/$pre_color_total_collar_order_qnty)*100,2); ?></td>
										</tr>
										<?
										$pre_grand_collar_ex_per+=$collar_ex_per;
										$pre_grand_tot_collar+=$pre_color_total_collar; 
										$pre_grand_tot_collar_order_qty+=$pre_color_total_collar_order_qnty; 
									}
								}
							}
							?>
                        </tr>
                        <tr>
                            <td>Size Total</td>
								<?
                                foreach($pre_size_total_arr  as $size_qty)
                                {
									?>
									<td style="border:1px solid black;  text-align:center"><? echo fn_number_format($size_qty); ?></td>
									<?
                                }
                                ?>
                            <td style="border:1px solid black; text-align:center"><? echo fn_number_format($pre_grand_tot_collar); ?></td>
                            <td align="center" style="border:1px solid black"><? echo fn_number_format((($pre_grand_tot_collar-$pre_grand_tot_collar_order_qty)/$pre_grand_tot_collar_order_qty)*100,2); ?></td>
                        </tr>
					</table>
                     <? if($jk==0 || (($jk/2)==0)) { ?>
                </div> <? } ?>
                <br/>
                <?
				$jk++;
            }
        }
				?>
				<br>
				<?
				
		$yarn_data_array=$yarn->getOrderCountCompositionPercentTypeColorAndRateWiseYarnQtyAndAmountArray();
		$cos_per_arr=$condition->getCostingPerArr();
		//print_r($yarn_data_array);
		$yarn_sql_array=sql_select("SELECT b.id as po_id, a.count_id, a.copm_one_id, a.percent_one,  a.color, a.type_id,  a.rate as rate from wo_pre_cost_fab_yarn_cost_dtls a, wo_po_break_down b where a.job_no=b.job_no_mst and a.job_no in($txt_job_no) and b.id in (".$txt_order_no_id.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.id, a.count_id, a.copm_one_id, a.percent_one,  a.color, a.type_id,  a.rate order by type_id");
		?>
		<table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
            <tr>
                <td width="49%" valign="top">
                    <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <tr align="center">
                    	<td colspan="5"><b>Yarn Required Summary (Pre Cost)</b></td>
                    </tr>
                    <tr align="center">
                        <td>Sl</td>
                        <td>PO</td>
                        <td>Yarn Description</td>
                        <?
                        if($show_yarn_rate==1)
                        {
                        ?>
                        <td>Rate</td>
                        <?
                        }
                        ?>
                        <td>Cons for <? echo $costing_per; ?> Gmts</td>
                        <td>Total Qty. </td>
                    </tr>
                    <?
					$i=0;
					$total_yarn=0;
					foreach($yarn_sql_array  as $row)
                    {
						
						$i++;
						$rowcons_qnty = $yarn_data_array[$row[csf("po_id")]][$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['qty'];
						$rowcons_Amt = $yarn_data_array[$row[csf("po_id")]][$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['amount'];
						$booking_percent=1;
						//echo $cos_per_arr[str_replace("'","",$txt_job_no)].'DDS-';
						$rate=$rowcons_Amt/$rowcons_qnty;
						//$rowcons_qnty =($rowcons_qnty/100)*$booking_percent;
						?>
						<tr align="center">
                            <td><? echo $i; ?></td>
                            <td><? echo $po_number_arr[$row[csf('po_id')]]; ?></td>
                            <td>
                            <?
                            $yarn_des=$yarn_count_arr[$row[csf('count_id')]]." ".$composition[$row[csf('copm_one_id')]]." ".$row[csf('percent_one')]."%  ";
							$yarn_des.=$color_library[$row[csf('color')]]." ";
							$yarn_des.=$yarn_type[$row[csf('type_id')]];
							
                            echo $yarn_des;
							
                            ?>
                            </td>
                            <?
                            if($show_yarn_rate==1)
                            {
                            ?>
                             <td><? echo fn_number_format($rate,4); ?></td>
                             <?
                            }
                             ?>
                            <td title="Req Qty/PO Qty*Costing Per"><?  echo fn_number_format(($rowcons_qnty/$tot_po_qty)*$costing_per_qnty,4); ?></td>
                           
                            <td align="right"><? echo fn_number_format($rowcons_qnty,2); $total_yarn+=$rowcons_qnty; ?></td>
						</tr>
						<?
					}
					?>
                    <tr align="center">
                        <td></td>
                        <td></td>
                        <td></td>
                        <?
                        if($show_yarn_rate==1)
                        {
                        ?>
                        <td></td>
                        <?
                        }
                        ?>
                        <td align="right">Total : </td>
                        <td align="right"><? echo fn_number_format($total_yarn,2); ?></td>
                    </tr>
                    </table>
                </td>
                <td width="2%">
                </td>
                <td width="49%" valign="top" align="center">
                
                </td>
            </tr>
        </table>
	</div>
	<br>
	<div>
	<?

	
	foreach($uom_wise_arr as $uom_id)
	{
	
	$nameArray_fabric_description= sql_select("select min(a.id) as fabric_cost_dtls_id,  a.lib_yarn_count_deter_id as determin_id,a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,a.uom,a.item_number_id,min(a.width_dia_type) as width_dia_type , b.dia_width, avg(b.cons) as cons  , avg(b.process_loss_percent) as process_loss_percent, avg(b.requirment) as requirment  FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b
	WHERE a.job_no=b.job_no and a.fabric_source=2 and a.uom=$uom_id and
	a.id=b.pre_cost_fabric_cost_dtls_id and
	c.job_no_mst=a.job_no and 
	c.id=b.color_size_table_id and
	
	a.job_no =$txt_job_no and 
	c.po_break_down_id in(".$txt_order_no_id.") and
	c.status_active=1 and 
	c.is_deleted=0 
	group by a.body_part_id,a.uom,a.lib_yarn_count_deter_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,a.item_number_id,b.dia_width order by fabric_cost_dtls_id,a.body_part_id,b.dia_width");
	//fabric_cost_dtls_id  fabric_cost_dtls_id
	 ?>
    
     <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
	 <caption> <label style="font-size:x-large"><b>Fabric Source Purchase  &nbsp;<? echo $unit_of_measurement[$uom_id];?></b></label></caption>
     <tr align="center">
     <th colspan="3" align="left">Item Name</th>
        <? 
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('item_number_id')] == "")  echo "<td  colspan='3'>&nbsp</td>";	
			else  echo "<td  colspan='3'>". $garments_item[$result_fabric_description[csf('item_number_id')]]."</td>";			
		}
		?>
        <td  rowspan="10" width="50"><p>Total Fabric (<? echo $unit_of_measurement[$uom_id];?>)</p></td> 
        <td  rowspan="10" width="50"><p>Avg Rate (<? echo $unit_of_measurement[$uom_id];?>)</p></td>
        <td  rowspan="10" width="50"><p>Amount </p></td>
       </tr>
     <tr align="center">
     <th colspan="3" align="left">Body Part</th>
        <? 
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('body_part_id')] == "")  echo "<td  colspan='3'>&nbsp</td>";	
			else  echo "<td  colspan='3'>". $body_part[$result_fabric_description[csf('body_part_id')]]."</td>";			
		}
		?>
       </tr>
     <tr align="center"><th colspan="3" align="left">Color Type</th>
        <? 
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('color_type_id')] == "")  echo "<td  colspan='3'>&nbsp</td>";	
			else echo "<td  colspan='3'>". $color_type[$result_fabric_description[csf('color_type_id')]]."</td>";			
		}
		?>
       </tr>  
        <tr align="center"><th colspan="3" align="left">Fabric Construction</th>
        <? 
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('construction')] == "")  echo "<td  colspan='3'>&nbsp</td>";	
			else  echo "<td  colspan='3'>". $result_fabric_description[csf('construction')]."</td>";			
		}
		?>
       </tr>       
        <tr align="center"><th   colspan="3" align="left">Fabric Composition</th>
        <? 
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('composition')] == "")   echo "<td colspan='3' >&nbsp</td>";
			else  echo "<td colspan='3' >".$result_fabric_description[csf('composition')]."</td>";			
		}
		?>
       
       </tr>
       <tr align="center"><th  colspan="3" align="left">GSM</th>
        <? 
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('gsm_weight')] == "")   echo "<td colspan='3'>&nbsp</td>";
			else echo "<td colspan='3' align='center'>". $result_fabric_description[csf('gsm_weight')]."</td>";			
		}
		?>
       
       </tr>
       <tr align="center"><th   colspan="3" align="left">Dia/Width (Inch)</th>
        <? 
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='3'>&nbsp</td>";
			else echo "<td colspan='3' align='center'>".$result_fabric_description[csf('dia_width')].",".$fabric_typee[$result_fabric_description[csf('width_dia_type')]]."</td>";			
		}
		?>
        
       </tr>
       <tr align="center"><th   colspan="3" align="left">Consumption For <? echo $costing_per; ?></th>
        <? 
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('requirment')] == "")   echo "<td colspan='3'>&nbsp</td>";
			else  echo "<td colspan='3' align='center'>Fin: ".fn_number_format($result_fabric_description[csf('cons')],2).", Grey: ".fn_number_format($result_fabric_description[csf('requirment')],2)."</td>";			
		}
		?>
        
       </tr>
       <tr>
       <th  colspan="<? echo  count($nameArray_fabric_description)*3+3; ?>" align="left" style="height:30px">&nbsp;</th>
       </tr>
       <tr>
            <!--<th  width="120" align="left">Gmts. Color</th>-->
            <th  width="120" align="left">Fabric Color</th>
            <th  width="120" align="left">Body Color</th>
            <th  width="120" align="left">Lab Dip No</th>
        <? 
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			  echo "<th width='50'>Fab. Qty</th><th width='50' >Rate</th><th width='50' >Amount</th>";			
		}
		?>
       
       </tr>
       <?
		  $gmt_color_library=array();
		  $gmt_color_data=sql_select("select b.gmts_color_id, b.contrast_color_id 
		  FROM   wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_color_dtls b 
		  WHERE a.id=b.pre_cost_fabric_cost_dtls_id  and a.fabric_source =2  and a.uom=$uom_id and  a.job_no =$txt_job_no and b.pre_cost_fabric_cost_dtls_id='".$result_fabric_description[csf('fabric_cost_dtls_id')]."' and a.color_size_sensitive=3");
		 
		  foreach( $gmt_color_data as $gmt_color_row)
		  {
			$gmt_color_library[$gmt_color_row[csf("contrast_color_id")]][$gmt_color_row[csf("gmts_color_id")]]=$color_library[$gmt_color_row[csf("gmts_color_id")]];
			$fab_color_library2[$gmt_color_row[csf("gmts_color_id")]]=$gmt_color_row[csf("contrast_color_id")];
		  }

	        $grand_total_fin_fab_qnty=0;
			$grand_total_amount=0;
			
		$color_wise_wo_sql=sql_select("select color_number_id as gmts_color_id FROM  wo_po_color_size_breakdown WHERE job_no_mst =$txt_job_no and po_break_down_id in(".$txt_order_no_id.")  group by color_number_id");
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
		?> 
			<tr>
            <td  width="120" align="left">
			<?
			
			$fab_color=$fab_color_library2[$color_wise_wo_result[csf("gmts_color_id")]];
			if($fab_color!=0) echo $color_library[$fab_color];
			else echo $color_library[$color_wise_wo_result[csf('gmts_color_id')]];
			

			?>
            </td>
            <td>
            <?
			$gmt_color_id=$color_wise_wo_result[csf('gmts_color_id')];
			
			///echo implode(",",$gmt_color_library[$color_wise_wo_result[csf('fabric_color_id')]]);
			echo $color_library[$gmt_color_id];
			?>
            </td>
            <td  width="120" align="left">
			<? 
			$lapdip_no="";
			$lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst=$txt_job_no and approval_status=3 and color_name_id=".$color_wise_wo_result[csf('fabric_color_id')]."");
			if($lapdip_no=="") echo "&nbsp;"; echo $lapdip_no; 
			?>
            </td>
            <?
			$total_fin_fab_qnty=0;
			$total_amount=0;
			
			foreach($nameArray_fabric_description as $result_fabric_description)
		    {
				if($db_type==0) //avg(b.cons) avg(b.requirment)
				{
				$color_wise_wo_sql_qnty=sql_select("select  c.po_break_down_id as po_id,c.color_number_id FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b
				WHERE a.job_no=b.job_no and
				a.id=b.pre_cost_fabric_cost_dtls_id and
				c.job_no_mst=a.job_no  
				c.id=b.color_size_table_id and
				
				a.job_no =$txt_job_no and
				c.po_break_down_id in(".$txt_order_no_id.") and
				a.item_number_id='".$result_fabric_description[csf('item_number_id')]."' and
				a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
				a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
				a.construction='".$result_fabric_description[csf('construction')]."' and 
				a.composition='".$result_fabric_description[csf('composition')]."' and 
				a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
				b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
				c.color_number_id=".$gmt_color_id." and
				a.uom=$uom_id and
				c.status_active=1 and 
				c.is_deleted=0 
				");
				}
				if($db_type==2)
				{
				
				$color_wise_wo_sql_qnty=sql_select("select   c.po_break_down_id as po_id,c.color_number_id FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b
				WHERE a.job_no=b.job_no and
				a.id=b.pre_cost_fabric_cost_dtls_id and
				c.job_no_mst=a.job_no and 
				c.id=b.color_size_table_id and
				a.job_no =$txt_job_no and
				c.po_break_down_id in(".$txt_order_no_id.") and
				a.item_number_id='".$result_fabric_description[csf('item_number_id')]."' and
				a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
				a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
				a.construction='".$result_fabric_description[csf('construction')]."' and 
				a.composition='".$result_fabric_description[csf('composition')]."' and 
				a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
				b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
				nvl(c.color_number_id,0)=nvl('".$gmt_color_id."',0) and
				 a.uom=$uom_id and
				c.status_active=1 and 
				c.is_deleted=0 ");
				
				}
				
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty;
				
				$fab_knit_grey_qty=$fab_data_qty_arr['knit']['grey'][$color_wise_wo_result_qnty[csf('po_id')]][$result_fabric_description[csf('fabric_cost_dtls_id')]][$gmt_color_id][$result_fabric_description[csf('uom')]]+$fab_data_qty_arr['woven']['grey'][$result_fabric_description[csf('fabric_cost_dtls_id')]][$gmt_color_id][$result_fabric_description[csf('uom')]];// 
				//echo $fab_knit_grey_qty.'='.$color_wise_wo_result_qnty[csf('po_id')].'='.$result_fabric_description[csf('fabric_cost_dtls_id')].'='.$gmt_color_id.'='.$result_fabric_description[csf('uom')].'A,';
				$fab_knit_grey_amount=$fab_data_amt_arr['knit']['grey'][$color_wise_wo_result_qnty[csf('po_id')]][$gmt_color_id][$result_fabric_description[csf('body_part_id')]][$result_fabric_description[csf('uom')]]+$fab_data_amt_arr['woven']['grey'][$color_wise_wo_result_qnty[csf('po_id')]][$gmt_color_id][$result_fabric_description[csf('body_part_id')]][$result_fabric_description[csf('uom')]];
			?>
			<td width='50' align='right'>
			<? 
			//echo $color_wise_wo_result_qnty[csf('po_id')].'='.$result_fabric_description[csf('body_part_id')].'='.$result_fabric_description[csf('uom')];
			if($fab_knit_grey_qty!="")
			{
			echo fn_number_format($fab_knit_grey_qty,2) ;
			$total_fin_fab_qnty+=$fab_knit_grey_qty;
			
			$fab_grey_qty_descrp_wise_arr[$result_fabric_description[csf('item_number_id')]][$result_fabric_description[csf('body_part_id')]]+=$fab_knit_grey_qty;
			$fab_grey_amt_descrp_wise_arr[$result_fabric_description[csf('item_number_id')]][$result_fabric_description[csf('body_part_id')]]+=$fab_knit_grey_amount;
			}
			?>
            </td>
            <td width='50' align='right' >
			<? 
			echo fn_number_format($fab_knit_grey_amount/$fab_knit_grey_qty,2); 
			?>
            </td>
            <td width='50' align='right' >
			<?
			$amount=$fab_knit_grey_amount;
			if($amount!="")
			{
			echo fn_number_format($amount,2); 
			$total_amount+=$amount;
			}
			?>
            </td>
            <?
			}
			?>
            <td align="right"><? echo fn_number_format($total_fin_fab_qnty,2); $grand_total_fin_fab_qnty+=$total_fin_fab_qnty;?></td>
            <td align="right"><? echo fn_number_format($total_amount/$total_fin_fab_qnty,2); $grand_total_amount+=$total_amount;?></td>
            <td align="right">
            <?
			echo fn_number_format($total_amount,2);
			
			?>
            </td>
            </tr>
         <?
		}
		?>
        <tr style=" font-weight:bold">
        <!--<td  width="120" align="left">&nbsp;</td>-->
        <th  width="120" align="left">&nbsp;</th>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left"><strong>Total</strong></td>
        <?
			foreach($nameArray_fabric_description as $result_fabric_description)
		    {
				$color_wise_wo_sql_qnty=sql_select("select  sum(b.cons) as fin_fab_qnty,sum(b.requirment) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b
												WHERE a.job_no=b.job_no and
												a.id=b.pre_cost_fabric_cost_dtls_id and
												c.job_no_mst=a.job_no and 
												c.id=b.color_size_table_id and
												
												a.job_no =$txt_job_no and 
												c.po_break_down_id in(".$txt_order_no_id.") and
												a.item_number_id='".$result_fabric_description[csf('item_number_id')]."' and
												a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
												a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
												a.construction='".$result_fabric_description[csf('construction')]."' and 
												a.composition='".$result_fabric_description[csf('composition')]."' and 
												a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
												b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
												a.uom=$uom_id and
												c.status_active=1 and 
												c.is_deleted=0 
												");
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty;
				
			$ttl_grey_purchase_qty=$fab_grey_qty_descrp_wise_arr[$result_fabric_description[csf('item_number_id')]][$result_fabric_description[csf('body_part_id')]];
			$ttl_grey_purchase_amount=$fab_grey_amt_descrp_wise_arr[$result_fabric_description[csf('item_number_id')]][$result_fabric_description[csf('body_part_id')]];
			
			?>
			<td width='50' align='right'><?  echo fn_number_format($ttl_grey_purchase_qty,2) ;?></td>
            <td width='50' align='right' > <? //echo fn_number_format($color_wise_wo_result_qnty['grey_fab_qnty'],2);?></td>
            <td width='50' align='right' ><? echo fn_number_format($ttl_grey_purchase_amount,2);?></td>
            <?
			}
			?>
            <td align="right"><? echo fn_number_format($grand_total_fin_fab_qnty,2);?></td>
            <td align="right"><? echo fn_number_format($grand_total_amount/$grand_total_fin_fab_qnty,2);?></td>
            <td align="right">
            <?
			echo fn_number_format($grand_total_amount,2);
			?>
            </td>
            </tr> 
            <tr style="font-weight:bold">
        <!--<td  width="120" align="left">&nbsp;</td>-->
        <th  width="120" align="left">&nbsp;</th>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left"><strong>Consumption For <? echo $costing_per; ?></strong></td>
        <?
			foreach($nameArray_fabric_description as $result_fabric_description)
		    {
				
				
			?>
			<td width='50' align='right'><?  //echo fn_number_format(($color_wise_wo_result_qnty['fin_fab_qnty']/$grand_total)*($total_set_qnty*$costing_per_qnty),4) ;?></td>
            <td width='50' align='right' > <? //echo fn_number_format(($color_wise_wo_result_qnty['grey_fab_qnty']/$grand_total)*($total_set_qnty*$costing_per_qnty),4);?></td>
            <td width='50' align='right' > <? //echo fn_number_format(($color_wise_wo_result_qnty['grey_fab_qnty']/$grand_total)*($total_set_qnty*$costing_per_qnty),4);?></td>
            <?
			}
			?>
            <td align="right">
			<? 
			$consumption_per_unit_fab=($grand_total_fin_fab_qnty/$tot_po_qty)*($costing_per_qnty);
			echo fn_number_format($consumption_per_unit_fab,4); 
			?>
            </td>
            <td align="right">
			<?
			$consumption_per_unit_amuont=($grand_total_amount/$tot_po_qty)*($costing_per_qnty);
			echo fn_number_format(($consumption_per_unit_amuont/$consumption_per_unit_fab),2);
			?>
            </td>
            <td align="right">
            <?
			echo fn_number_format($consumption_per_unit_amuont,2);
			?>
            </td>
            </tr> 
    </table>
		<br/>
    <?
	
		
	}
	?>
	<!--Fab. Source Purchase End-->
	<br/>
	<?
			
			$collar_cuff_percent_arr=array();
			$collar_cuff_body_arr=array();
			$collar_cuff_color_arr=array();
			$collar_cuff_size_arr=array();
			$collar_cuff_item_size_arr=array();
			$color_size_sensitive_arr=array();
			
			$fab_collar_cuff_data_array=$fabric->getQtyArray_by_orderGmtscolorAndGmtssize_knitAndwoven_greyAndfinish_purchase();
			
			$collar_cuff_sql="select a.id,a.uom, a.color_size_sensitive, a.color_break_down, b.color_number_id, b.gmts_sizes, b.item_size,c.po_break_down_id as po_id, c.size_number_id,e.body_part_full_name, e.body_part_type 
			FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c, lib_body_part e 
			
			WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and a.job_no =$txt_job_no and c.po_break_down_id in(".$txt_order_no_id.") and a.body_part_id=e.id and a.fabric_source=2 and e.body_part_type in (40,50) and c.id=b.color_size_table_id and c.status_active=1 and c.is_deleted=0 order by  c.size_order";
			//echo $collar_cuff_sql;
			$collar_cuff_sql_res=sql_select($collar_cuff_sql);
			
			foreach($collar_cuff_sql_res as $row)
			{
					$fab_knit_grey_size_qty=$fab_collar_cuff_data_array['knit']['grey'][$row[csf('po_id')]][$row[csf('color_number_id')]][$row[csf('gmts_sizes')]][$row[csf('uom')]];
					
				$collar_cuff_percent_arr[$row[csf('body_part_type')]][$row[csf('body_part_full_name')]][$row[csf('color_number_id')]][$row[csf('gmts_sizes')]]=$row[csf('colar_cuff_per')];
				$collar_cuff_body_arr[$row[csf('body_part_type')]][$row[csf('body_part_full_name')]]=$row[csf('body_part_full_name')];
				$collar_cuff_size_arr[$row[csf('body_part_type')]][$row[csf('body_part_full_name')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$collar_cuff_item_size_arr[$row[csf('body_part_full_name')]][$row[csf('size_number_id')]][$row[csf('item_size')]]=$row[csf('item_size')];
				$color_size_sensitive_arr[$row[csf('body_part_full_name')]][$row[csf('id')]][$row[csf('color_number_id')]][$row[csf('color_size_sensitive')]]=$row[csf('color_break_down')];
				
				$color_size_sensitive_qty_arr[$row[csf('body_part_type')]][$row[csf('body_part_full_name')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['qty']=$fab_knit_grey_size_qty;
				
			}
			//print_r($color_size_sensitive_qty_arr) ;
			
			
			//print_r($fab_collar_cuff_data_array);
			unset($collar_cuff_sql_res); $jk=0;
			foreach($collar_cuff_body_arr as $body_type=>$body_name)
			{
				foreach($body_name as $body_val)
				{
					$count_collar_cuff=count($collar_cuff_size_arr[$body_type][$body_val]);
					$pre_grand_tot_collar=0; $pre_grand_tot_collar_order_qty=0; 
					if($jk==0 || (($jk/2)==0)) {
					?>
                    <div style="max-height:1330px; overflow:auto; float:left; padding-top:5px; margin-left:5px; margin:5px; margin-bottom:5px; position:relative;"> <? } ?>
					<table width="625" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                        <tr>
                        	<td colspan="<? echo $count_collar_cuff+3; ?>" align="center"><b><? echo $body_val; ?> - Color Size Breakdown in Pcs.</b></td>
                        </tr>
                        <tr>
                            <td width="100">Size <? echo $jk; ?></td>
								<?  
                                foreach($collar_cuff_size_arr[$body_type][$body_val]  as $size_number_id)
                                {	     
									?>
									<td align="center" style="border:1px solid black"><strong><? echo $size_library[$size_number_id];?></strong></td>
									<?	
                                }    
                                ?>	
                            <td width="60" rowspan="2" align="center"><strong>Total</strong></td> 
                            <td rowspan="2" align="center"><strong>Extra %</strong></td>
                        </tr>
                        <tr>
                            <td style="font-size:12px"><? echo $body_val; ?> Size</td>
                            <?
                            foreach($collar_cuff_item_size_arr[$body_val]  as $size_number_id=>$size_number)
                            {	
								if(count($size_number)>0) 
								{
									 foreach($size_number  as $item_size=>$val) 
									 {   
										?>
										<td align="center" style="border:1px solid black"><strong><? echo $item_size;?></strong></td>
										<?
									 }

								}
								else 
								{
									?>
									<td align="center" style="border:1px solid black"><strong>&nbsp;</strong></td>
									<?
								}
                            }    
                            
                            $pre_size_total_arr=array();
                            foreach($color_size_sensitive_arr[$body_val] as $pre_cost_id=>$pre_cost_data)
                            {
								foreach($pre_cost_data as $color_number_id=>$color_number_data)
								{
									foreach($color_number_data as $color_size_sensitive=>$color_break_down)
									{
										$pre_color_total_collar=0;
										$pre_color_total_collar_order_qnty=0;
										$process_loss_method=$process_loss_method;
										$constrast_color_arr=array();
										if($color_size_sensitive==3)
										{
											$constrast_color=explode('__',$color_break_down);
											for($i=0;$i<count($constrast_color);$i++)
											{
												$constrast_color2=explode('_',$constrast_color[$i]);
												$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
											}
										}
										?> 
										<tr>
											<td>
												<?
                                                if( $color_size_sensitive==3)
                                                {
                                                    echo strtoupper ($constrast_color_arr[$color_number_id]) ;
                                                    $lab_dip_color_id=$lab_dip_color_arr[$pre_cost_id][$color_number_id];
                                                }
                                                else
                                                {
                                                    echo $color_library[$color_number_id];
                                                    $lab_dip_color_id=$color_number_id;
                                                }
                                                ?>
											</td>
											<?
											foreach($collar_cuff_size_arr[$body_type][$body_val] as $size_number_id)
											{
												?>
												<td align="center" style="border:1px solid black">
													<? $plan_cut=0; $collerqty=0; $collar_ex_per=0;
													$size_wise_qty=$color_size_sensitive_qty_arr[$body_type][$body_val][$color_number_id][$size_number_id]['qty'];
													
													if($body_type==50) $plan_cut=($size_wise_qty)*2;
													else $plan_cut=$size_wise_qty;
                                                   // $ord_qty=$order_plan_qty_arr[$color_number_id][$size_number_id]['order'];
                                                    
                                                    $collar_ex_per=$collar_cuff_percent_arr[$body_type][$body_val][$color_number_id][$size_number_id];
                                                    // echo $collar_ex_per.'=';
												    if($body_type==50) { if($collar_ex_per==0 || $collar_ex_per=="") $collar_ex_per=$cuff_excess_percent; else $collar_ex_per=$collar_ex_per; }
                                                    else if($body_type==40) { if($collar_ex_per==0 || $collar_ex_per=="") $collar_ex_per=$colar_excess_percent; else $collar_ex_per=$collar_ex_per; }
                                                    $colar_excess_per=fn_number_format(($plan_cut*$collar_ex_per)/100,6,".",",");
                                                    $collerqty=($plan_cut+$colar_excess_per);
                                                    
                                                    //$collerqty=fn_number_format(($requirment/$costing_per_qnty)*$plan_cut,2,'.','');
                                                    
                                                    echo fn_number_format($collerqty); 
                                                    $pre_size_total_arr[$size_number_id]+=$collerqty;
                                                    $pre_color_total_collar+=$collerqty; 
                                                    $pre_color_total_collar_order_qnty+=$plan_cut; 
                                                    
                                                    //$pre_grand_tot_collar_order_qty+=$plan_cut; 
                                                    ?>
												</td>
												<?	
											}    
											?>	
											
											<td align="center"><? echo fn_number_format($pre_color_total_collar); ?></td>
											<td align="center"><? echo fn_number_format((($pre_color_total_collar-$pre_color_total_collar_order_qnty)/$pre_color_total_collar_order_qnty)*100,2); ?></td>
										</tr>
										<?
										$pre_grand_collar_ex_per+=$collar_ex_per;
										$pre_grand_tot_collar+=$pre_color_total_collar; 
										$pre_grand_tot_collar_order_qty+=$pre_color_total_collar_order_qnty; 
									}
								}
							}
							?>
                        </tr>
                        <tr>
                            <td>Size Total</td>
								<?
                                foreach($pre_size_total_arr  as $size_qty)
                                {
									?>
									<td style="border:1px solid black;  text-align:center"><? echo fn_number_format($size_qty); ?></td>
									<?
                                }
                                ?>
                            <td style="border:1px solid black; text-align:center"><? echo fn_number_format($pre_grand_tot_collar); ?></td>
                            <td align="center" style="border:1px solid black"><? echo fn_number_format((($pre_grand_tot_collar-$pre_grand_tot_collar_order_qty)/$pre_grand_tot_collar_order_qty)*100,2); ?></td>
                        </tr>
					</table>
                     <? if($jk==0 || (($jk/2)==0)) { ?>
                </div> <? } ?>
                <br/>
                <?
				$jk++;
            }
        }
		
		//start	Conversion Cost to Fabric report here -------------------------------------------
		$sql_count = "select a.cons_process as cons_process from wo_pre_cost_fab_conv_cost_dtls a left join wo_pre_cost_fabric_cost_dtls b on a.job_no=b.job_no and a.fabric_description=b.id  and b.status_active=1 where a.job_no=".$txt_job_no."  and a.status_active=1 group by a.cons_process order by  a.cons_process";
		$tot_data_array=sql_select($sql_count);
		foreach( $tot_data_array as $row ){
				 $process_id=$row[csf("cons_process")];
				 $process_row+=count($row[csf("cons_process")]);
		 }
		 $sql = "select a.id, a.fabric_description as pre_cost_fabric_cost_dtls_id, a.job_no, a.cons_process, a.req_qnty, a.charge_unit, a.amount, a.color_break_down, a.status_active, b.body_part_id, b.uom, b.fab_nature_id, b.color_type_id, b.fabric_description, b.item_number_id from wo_pre_cost_fab_conv_cost_dtls a left join wo_pre_cost_fabric_cost_dtls b on a.job_no=b.job_no and a.fabric_description=b.id  and b.status_active=1 where a.job_no=".$txt_job_no." and a.status_active=1 order by a.cons_process";
		 $data_array=sql_select($sql);
 	?>
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:1030px;text-align:center;" rules="all">
				<tr><td colspan="10">
			<label  style="float:left;background:#CCCCCC; font-size:larger"><b>Conversion Cost to Fabric </b> </label>	</td>
                <tr style="font-weight:bold">
                    <td width="230">Particulars</td>
                    <td width="100">Process</td>
                    <td width="60">Cons/Dzn</td>
					<td width="80">TTL Required</td>
					<td width="60">Uom</td>
                    <td width="160" title="For Fabric Dyeing & Yarn Dyeing">Fabric Color & Charge /Unit</td>
                   
                    <td width="60">Rate</td>
                    <td width="60">Rate (TK)</td>
					<td width="70">Amount /Dzn</td>
                    <td width="70">TTL Amount</td>
					<td >% to Ord. Value</td>
                </tr>
            <?
			//$txt_order_no_id
			
			$conv_data_qty=$conversion->getQtyArray_by_conversionid();
			$conv_data_amt=$conversion->getAmountArray_by_conversionid();
	
            $total_conversion_cost=$total_convsion_qty_dzn=$total_conversion_cost_dzn=0;$total_conversion_cost_dzn=0;$total_conversion_cost_kg=0;
			$total_convsion_qty=0;
			$total_avg_convsion_qty=0;$grand_total_conv_qnty=0;$grand_total_avg_convsion_qty=$grand_total_conversion_cost_dzn=$grand_total_avg_convsion_qty_dzn=0;$grand_total_conversion_cost=0;
			$process_array_check=array();$k=1;
            foreach( $data_array as $row )
            { 
				$convsion_qty=$conv_data_qty[$row[csf('id')]][$row[csf('uom')]];
				$conversion_cost=$conv_data_amt[$row[csf('id')]][$row[csf('uom')]];
				
				if($row[csf("pre_cost_fabric_cost_dtls_id")] ==0) $item_descrition = "All Fabrics";
				else $item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("fabric_description")];
				
				$process_id=$row[csf("cons_process")];
				if (!in_array($process_id,$process_array_check) )
				{
					if($k!=1)
					{
						?>
					   <tr>
							<td>&nbsp;</td>
							<td><strong>Process Total </strong></td>
							<td align="right"><strong><? echo fn_number_format($total_convsion_qty_dzn,3); ?></strong></td>
							<td align="right"><strong><? echo fn_number_format($total_convsion_qty,2); ?></strong></td>
							<td align="right"><strong><? //echo fn_number_format($total_convsion_qty,4); ?></strong></td>
						    <td>&nbsp;</td>
							<td>&nbsp;</td>
                            <td>&nbsp;</td>
							<td align="right"><strong><? echo fn_number_format($total_conversion_cost_dzn,3); ?></strong></td>
							<td align="right"><strong><? echo fn_number_format($total_conversion_cost,2); ?></strong></td>
							<td align="right"><? echo fn_number_format(($total_conversion_cost_dzn/$tot_perDznValue)*100,3); ?></td>
						</tr>
						<?
					}
					?>
					<?
					unset($total_convsion_qty_dzn);unset($total_conversion_cost_dzn);
					unset($total_convsion_qty);
					unset($total_avg_convsion_qty);
					unset($total_conversion_cost);
					unset($total_convsion_qty_dzn);
					$process_array_check[]=$process_id; 
					$k++;    
				}
			?>	 
                <tr>
                    <td><? echo $item_descrition; ?></td>
                    <td><? echo $conversion_cost_head_array[$row[csf("cons_process")]]; ?></td>
                    <td align="right"><? echo fn_number_format($row[csf("req_qnty")],3); ?></td>
					<td align="right"><? echo fn_number_format($convsion_qty,2); ?></td>
					<td align="right"><? echo  $unit_of_measurement[$row[csf('uom')]]; ?></td>
                  	<td style="word-break:break-all">
						<? if ($row[csf("cons_process")]==30 || $row[csf("cons_process")]==31)
                        {
                            $ex_color_break_down=explode("__",$row[csf('color_break_down')]);
                            ?>
                                <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:155px;text-align:center;" rules="all">
                                <?
                                foreach($ex_color_break_down as $exstr)
                                {
                                    $ex_data=explode("_",$exstr);
                                    $unitcharge=$ex_data[1];
                                    $fabriccolorid=$ex_data[3];
                                    ?>
                                    <tr>
                                        <td width="95" style="word-break:break-all"><? echo $color_library[$fabriccolorid]; ?></td>
                                        <td><? echo fn_number_format($unitcharge,4); ?></td>
                                    </tr>
                                    <?
                                }
                                ?>
                                </table>
                            <?
                        }
                        else echo "&nbsp;"; ?>
                    </td>
                    <td align="right"><? echo fn_number_format($row[csf("charge_unit")],3); ?></td>
                    <td align="right"><? echo fn_number_format(($row[csf("charge_unit")]*$exchange_rate),3); ?></td>
					<td align="right"><? echo fn_number_format($row[csf('amount')],4); ?></td>
                    <td align="right"><? echo fn_number_format($conversion_cost,2); ?></td>
					<td align="right" title="<? echo $row[csf('amount')].'='.$tot_perDznValue;?>"><? echo fn_number_format(($row[csf('amount')]/$tot_perDznValue)*100,2); ?></td>
                </tr>
            <?
				$total_convsion_qty+=$convsion_qty;
				$total_convsion_qty_dzn+=$row[csf("req_qnty")];
				$total_avg_convsion_qty+=$row[csf("req_qnty")];
				$total_conversion_cost += $conversion_cost;
			//	$total_conversion_cost_dzn += $row[csf('amount')];
				$grand_total_conv_qnty+=$convsion_qty;
				$grand_total_avg_convsion_qty+=$convsion_qty;
				$grand_total_avg_convsion_qty_dzn+=$row[csf("req_qnty")];
				$grand_total_conversion_cost+= $conversion_cost;
				$grand_total_conversion_cost_dzn+= $row[csf('amount')];
				$total_conversion_cost_dzn+=$row[csf('amount')];
				$total_conversion_cost_kg=$grand_total_conversion_cost/$total_avg_yarn_qty;//$grand_total_avg_convsion_qty;
           	 }
            ?>
            <tr class="rpt_bottom" style="font-weight:bold">
                <td>&nbsp;</td>
                <td align="right">Process Total</td>
                <td align="right"><? echo fn_number_format($total_convsion_qty_dzn,3); ?></td>
				 <td align="right"><? echo fn_number_format($total_convsion_qty,2); ?></td>
				 <td align="right"><? //echo fn_number_format($total_convsion_qty,4); ?></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td> 
                <td>&nbsp;</td>
				<td align="right"><? echo fn_number_format($total_conversion_cost_dzn,4); ?></td>                   
                <td align="right"><? echo fn_number_format($total_conversion_cost,2); ?></td>
				<td align="right"><? echo fn_number_format(($total_conversion_cost_dzn/$tot_perDznValue)*100,2); ?></td>
            </tr>   
            <tr class="rpt_bottom" style="font-weight:bold">
                <td colspan="2" align="right">Conversion Total</td>
                <td align="right"><? echo fn_number_format($grand_total_avg_convsion_qty_dzn,3); ?></td>
				<td align="right"><? echo fn_number_format($grand_total_conv_qnty,2); ?></td>
				<td align="right"><? //echo fn_number_format($grand_total_conv_qnty,4); ?></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td> 
				 <td align="right"><? echo fn_number_format($grand_total_conversion_cost_dzn,4); ?></td>                
                <td align="right"><? echo fn_number_format($grand_total_conversion_cost,2); ?></td>
				<td align="right"  title="<? echo $grand_total_conversion_cost_dzn.'='.$tot_perDznValue;?>"><? echo fn_number_format(($grand_total_conversion_cost_dzn/$tot_perDznValue)*100,3); ?></td>
            </tr>     
            </table>
      </div>
      <?
	//End Conversion Cost to Fabric report here -------------------------------------------
	?>
	<br>
	</div>
	</div>
	<br/>
    <? //echo signature_table(109, $cbo_company_name, "970px");
	//exit();
	
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
	//echo "$total_data****$filename****$report_type";
	exit();	
}
if( $action=="report_generate2") //md mamun ahmed sagor/crm=14751/05-10-2022
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	//echo $txt_style;die;
	$company_name_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$yarn_count_library=return_library_array( "select id,yarn_count from lib_yarn_count", "id","yarn_count");

	$report_type=str_replace("'","",$reporttype);
	//echo $report_type;
	//echo $cbo_search_date;die;
	//cbo_style_owner*cbo_team_name*cbo_deal_marchant
	$company_name=str_replace("'","",$cbo_company_name);
	$file_no=trim(str_replace("'","",$txt_file_no));
	$internal_ref=trim(str_replace("'","",$txt_internal_ref));
	$txt_job_id=trim(str_replace("'","",$txt_job_id));
	
	$cbo_style_owner=str_replace("'","",$cbo_style_owner);
	$cbo_search_date=str_replace("'","",$cbo_search_date);
	if($company_name==0) $company_name_cond=""; else $company_name_cond=" and a.company_name=".$company_name."";
	
	if($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='$internal_ref' ";
	if($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='$file_no' ";
	
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
		if(trim($cbo_year)!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year"; else $year_cond="";
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
 	$date_cond='';	$c_date_cond='';
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
			$c_date_cond=" and c.country_ship_date between '$start_date' and '$end_date'";
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

	
		
	$job_no=str_replace("'","",$txt_job_no);
	//echo $job_no;die;
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
	$order_id=str_replace("'","",$txt_order_id);
	$order_num=str_replace("'","",$txt_order_no);
	$order_id_cond_trans="";$order_no_cond="";
	if($order_id!="" && $order_id!=0) 
	{ 
		$order_id_cond_trans=" and b.id in ($order_id)";
	}
	else if($order_id=="" && $order_num!="") 
	{
		 $order_no_cond=" and  b.po_number in ('$order_num') ";
	}
	//else if ($order_num=="") $order_no_cond=""; else $order_no_cond=" and  b.po_number in ('$order_num') ";
	
	
	//$cbo_team_name=str_replace("'","",$cbo_team_name);
	//$cbo_team_member=str_replace("'","",$cbo_team_member);
	
	//if($cbo_team_name==0) $team_name_cond=""; else $team_name_cond=" and a.team_leader='$cbo_team_name'";
	//if($cbo_team_member==0) $team_member_cond=""; else $team_member_cond=" and a.dealing_marchant='$cbo_team_member'";
	
				
	 $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	 $comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	 $color_library=return_library_array( "select id, color_name from lib_color ", "id", "color_name"  );
	 $size_library=return_library_array( "select id, size_name from lib_size", "id", "size_name"  );


	 $sql_gsm="select b.gsm_weight,b.job_no,b.body_part_id from wo_po_details_master a,wo_pre_cost_fabric_cost_dtls b where a.job_no=b.job_no and b.body_part_id in(1,20) $company_name_cond $job_no_cond $year_cond $buyer_id_cond";
	$sql_gsm_data=sql_select($sql_gsm);
	foreach($sql_gsm_data as $row)
	{
		if($body_part_id==1) $gsm_weight_top=$sql_po_row[csf('gsm_weight')];
		if($body_part_id==20) $gsm_weight_bottom=$sql_po_row[csf('gsm_weight')];
	}
	  //$gsm_weight_top=return_field_value("gsm_weight", "wo_pre_cost_fabric_cost_dtls", "job_no=$txt_job_no and body_part_id=1");
	// $gsm_weight_bottom=return_field_value("gsm_weight", "wo_pre_cost_fabric_cost_dtls", "job_no=$txt_job_no and body_part_id=20");
	 $po_qty=0;
	 $po_plun_cut_qty=0;
	 $total_set_qnty=0;
 $sql_po="select a.id,a.job_no, a.buyer_name,a.total_set_qnty,a.avg_unit_price,a.style_ref_no,b.id,b.pub_shipment_date,b.po_number,c.item_number_id,c.country_id,c.color_number_id,c.size_number_id,c.order_quantity , c.plan_cut_qnty  from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $company_name_cond $job_no_cond $year_cond  $date_cond $order_id_cond_trans $order_status_cond $order_no_cond $internal_ref_cond $file_no_cond $buyer_id_cond order by b.id"; 
	$sql_po_data=sql_select($sql_po);
	foreach($sql_po_data as $sql_po_row)
	{
		$po_qty+=$sql_po_row[csf('order_quantity')];
		$po_plun_cut_qty+=$sql_po_row[csf('plan_cut_qnty')];
		$job_color_wise_arr[$sql_po_row[csf('color_number_id')]]['plan_cut_qnty']+=$sql_po_row[csf('plan_cut_qnty')];
		$job_color_wise_arr[$sql_po_row[csf('color_number_id')]]['order_quantity']+=$sql_po_row[csf('order_quantity')];
		$total_set_qnty=$sql_po_row[csf('total_set_qnty')];
		$txt_job_no="'".$sql_po_row[csf('job_no')]."'";
		
		$job_in_orders .= $sql_po_row[csf('po_number')].",";
		$job_no_full .= $sql_po_row[csf('job_no')].",";
		$txt_order_no_id .= $sql_po_row[csf('id')].",";
		$item_number_ids .= $sql_po_row[csf('item_number_id')].",";
		$style_ref_no .= $sql_po_row[csf('style_ref_no')].",";
		$job_id .= $sql_po_row[csf('id')].",";
		$buyer_name_id= $sql_po_row[csf('buyer_name')];
		$avg_unit_price= $sql_po_row[csf('avg_unit_price')];
		$pub_shipment_date.= change_date_format($sql_po_row[csf('pub_shipment_date')]).',';
		$po_number_arr[$sql_po_row[csf('id')]]=$sql_po_row[csf('po_number')];
		$total_set_qnty=$sql_po_row[csf('total_set_qnty')];
	}
	$job_ids=rtrim($job_id,',');
	$pub_shipment_date=rtrim($pub_shipment_date,',');
	$job_in_orders=rtrim($job_in_orders,',');$txt_order_no_id=rtrim($txt_order_no_id,',');
	$pub_shipment_dates=implode(",",array_unique(explode(",",$pub_shipment_date)));
	$job_ids=implode(",",array_unique(explode(",",$job_ids)));
	$job_in_orders=implode(", ",array_unique(explode(",",$job_in_orders)));
	$txt_order_no_id=implode(",",array_unique(explode(",",$txt_order_no_id)));
 $sql = "select a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.gmts_item_id, a.order_uom, a.job_quantity, a.ship_mode, a.avg_unit_price, a.total_price, b.costing_per, b.budget_minute, b.exchange_rate, b.approved, b.sew_smv, b.sew_effi_percent, c.fab_knit_req_kg, c.fab_knit_fin_req_kg, c.fab_woven_req_yds, c.fab_woven_fin_req_yds, c.fab_yarn_req_kg from wo_po_details_master a, wo_pre_cost_mst b left join wo_pre_cost_sum_dtls c on  b.job_no=c.job_no where a.job_no=b.job_no and a.status_active=1 and a.id in($job_ids) $job_no_cond $company_name_cond $buyer_id_cond $year_cond  order by a.job_no";


	$data_array=sql_select($sql);
	//$buyer_name_id=$data_array[0][csf('buyer_name')];
	$job_no=$data_array[0][csf('job_no')];
	$tot_po_qty=$po_qty;
	$tot_po_value=$data_array[0][csf('total_price')];
	$exchange_rate=$data_array[0][csf('exchange_rate')];
	$tot_perDznValue=($tot_po_value/$tot_po_qty)*12;
		ob_start(); 
	 ?>
	 <div  class="scroll_div_inner">
		<div style="width:850px; font-size:20px; font-weight:bold" align="center"><? echo $comp[str_replace("'","",$cbo_company_name)]; ?></div>
   		<div style="width:850px; font-size:14px; font-weight:bold" align="center">Fabric Pre-Costing Details:</div>
		 <table class="rpt_table">
		 <tr>
		 <td>
			 <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px" rules="all">
                	<tr>
                    	<td width="80">Job Number</td>
                        <td width="80"><b><? $job_nos=rtrim($job_no_full,','); echo implode(",",array_unique(explode(',',$job_nos)));?></b></td>
                        <td width="90">Buyer</td>
                        <td width="100"><b><? echo $buyer_arr[$buyer_name_id]; ?></b></td>
                        <td width="80">Plan Cut Qnty</td>
                        <? 
							$item_number_idss=rtrim($item_number_ids,',');
							$gmts_item_name="";
							$gmts_item=array_unique(explode(',',$item_number_idss));
							for($g=0;$g<=count($gmts_item); $g++)
							{
								$gmts_item_name.= $garments_item[$gmts_item[$g]].",";
							}
						?>
                        <td width="100"><b><? echo $po_plun_cut_qty; ?></b></td>
                    </tr>
                    <tr>
                    	<td>Order Qty. </td>
                        <td><b><? echo $tot_po_qty." ". $unit_of_measurement[$data_array[0][csf('order_uom')]]; ?></b></td>
						<td>Style Ref. </td>
                        <td><b><? $style_ref_nos=rtrim($style_ref_no,',');echo implode(",",array_unique(explode(',',$style_ref_nos))); ?></b></td>
                        <td>Shipment Date</td>
                        <td><b><? echo $pub_shipment_dates;?></b></td>
                    </tr>
                    <tr>
                    	<td>Order Numbers</td>
                        <td colspan="5"><? echo $job_in_orders; ?></td>
                    </tr>
                    <tr>
                    	<td>Garments Item</td>
                        <td colspan="3"><b><? echo $gmts_item_name;?></b></td>
                        <td>Costing Date</td>
                        <td><b><? echo $costing_date; ?> </b></td>
                    </tr>
                    
                  
                </table>
				</td>
				<td width="350"  valign="middle">
				  <?
     	 // image show here  -------------------------------------------
		// echo "SELECT image_location FROM common_photo_library where master_tble_id in($txt_job_no) and form_name='knit_order_entry' and file_type=1";
              $nameArray_img =sql_select("SELECT image_location FROM common_photo_library where master_tble_id in($txt_job_no) and form_name='knit_order_entry' and file_type=1");?>
			  <div style="margin:15px 5px;float:left;width:350px" >
				<? foreach($nameArray_img AS $inf){ ?>
					<img  src='../../../<? echo $inf[csf("image_location")]; ?>' height='120' width='100' />
				<?  } ?>
			  </div>
				</td>
				</tr>
				 </table>
				<br/>
			
				<br/><br/>
				<div>
				<!--Fabric Production Start-->
	 <?
		$costing_per=""; $costing_per_qnty=0;
		$costing_per_id=return_field_value( "costing_per", "wo_pre_cost_mst","job_no in($txt_job_no)");
		if($costing_per_id==1)
			{
				$costing_per="1 Dzn";
				$costing_per_qnty=12;
			}
			if($costing_per_id==2)
			{
				$costing_per="1 Pcs";
				$costing_per_qnty=1;
			}
			if($costing_per_id==3)
			{
				$costing_per="2 Dzn";
				$costing_per_qnty=24;
			}
			if($costing_per_id==4)
			{
				$costing_per="3 Dzn";
				$costing_per_qnty=36;
			}
			if($costing_per_id==5)
			{
				$costing_per="4 Dzn";
				$costing_per_qnty=48;
			}
	//$process_loss_method=return_field_value( "process_loss_method", "wo_pre_cost_fabric_cost_dtls","job_no=$txt_job_no");
	
	$nameArray_fab_uom = sql_select("select a.process_loss_method,a.uom,a.fabric_source FROM wo_pre_cost_fabric_cost_dtls a WHERE a.job_no in($txt_job_no)");
	foreach($nameArray_fab_uom as $row){
	if($row[csf('fabric_source')]==2)
	{
		$uom_wise_arr[$row[csf('uom')]]=$row[csf('uom')];
	}
		$process_loss_method=$row[csf('process_loss_method')];
	}
	//print_r($uom_wise_arr);
	
			
 $fabric_source=1;
 //if($fabric_source==1)
 //{

 //$costing_per=$cos_per_arr[str_replace("'","",$txt_job_no)];

 $nameArray_gmts_sizes = sql_select("select a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight, b.dia_width, c.size_number_id,c.size_order FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b  
	WHERE 
	a.job_no=b.job_no and
	a.id=b.pre_cost_fabric_cost_dtls_id and
	c.job_no_mst=a.job_no and 
	c.id=b.color_size_table_id and
	a.job_no =$txt_job_no and c.po_break_down_id in(".$txt_order_no_id.")  and a.fabric_source=1
	group by a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width,c.size_number_id,c.size_order order by c.size_order");
	
	$GmtsSizesArr=array();
	foreach($nameArray_gmts_sizes as $sizes_row){
		$GmtsSizesArr[$sizes_row[csf('body_part_id')]][$sizes_row[csf('color_type_id')]][$sizes_row[csf('construction')]][$sizes_row[csf('composition')]][$sizes_row[csf('gsm_weight')]][$sizes_row[csf('dia_width')]][$sizes_row[csf('size_number_id')]]=$size_library[$sizes_row[csf('size_number_id')]];
	}
	$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
	
	$nameArray_gmts_sizes = sql_select("select  a.id,a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight, b.dia_width, c.count_id,d.yarn_count ,e.composition_name,b.color_number_id,c.cons_qnty,c.rate,c.amount
	from wo_pre_cost_fabric_cost_dtls a, 
	wo_pre_cost_fab_yarn_cost_dtls c left join lib_composition_array e on c.copm_one_id=e.id,lib_yarn_count d  , 
	wo_pre_cos_fab_co_avg_con_dtls b
	where 
	a.job_no=b.job_no and
	a.id=b.pre_cost_fabric_cost_dtls_id and
	c.job_no=a.job_no and  a.fabric_source=1 and 
	c.count_id=d.id and
	a.id=c.fabric_cost_dtls_id and
	b.pre_cost_fabric_cost_dtls_id=c.fabric_cost_dtls_id and
	a.job_no =$txt_job_no
	group by  a.id,a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width,c.count_id,d.yarn_count  ,e.composition_name,b.color_number_id,c.cons_qnty,c.rate,c.amount");

	$countArr=array();
	foreach($nameArray_gmts_sizes as $sizes_row){
		$countArr[$sizes_row[csf('body_part_id')]][$sizes_row[csf('color_type_id')]][$sizes_row[csf('construction')]][$sizes_row[csf('composition')]][$sizes_row[csf('gsm_weight')]][$sizes_row[csf('dia_width')]][$sizes_row[csf('count_id')]]=$yarn_count_arr[$sizes_row[csf('count_id')]];

		// $yarn_details_arr[$sizes_row[csf('id')]][$sizes_row[csf('body_part_id')]][$sizes_row[csf('color_type_id')]][$sizes_row[csf('construction')]][$sizes_row[csf('composition')]][$sizes_row[csf('gsm_weight')]][$sizes_row[csf('dia_width')]][$sizes_row[csf('count_id')]]=$yarn_count_arr[$sizes_row[csf('count_id')]];
		$yarn_details_arr[$sizes_row[csf('id')]][$sizes_row[csf('color_number_id')]]['yarn_details']=$sizes_row[csf('yarn_count')]." ".$sizes_row[csf('composition_name')];
		$yarn_details_arr[$sizes_row[csf('id')]][$sizes_row[csf('color_number_id')]]['rate']=$sizes_row[csf('rate')];
		$yarn_details_arr[$sizes_row[csf('id')]][$sizes_row[csf('color_number_id')]]['qnty']+=($sizes_row[csf('cons_qnty')]/$tot_po_qty)*$costing_per_qnty;
		$yarn_details_arr[$sizes_row[csf('id')]][$sizes_row[csf('color_number_id')]]['amount']+=(($sizes_row[csf('cons_qnty')]/$tot_po_qty)*$costing_per_qnty)*$sizes_row[csf('rate')];
			
	}
	
	// echo "<pre>";
	// print_r($yarn_details_arr);

	
	$fabric_description_sql="SELECT  min(a.id) as fabric_cost_dtls_id,a.item_number_id, max(a.lib_yarn_count_deter_id) as determin_id,a.body_part_id,a.uom,a.color_type_id,a.fabric_source, a.construction, a.composition, a.gsm_weight,min(a.width_dia_type) as width_dia_type, b.dia_width,avg(b.cons) as cons  , avg(b.process_loss_percent) as process_loss_percent , avg(b.requirment) as requirment,c.color_number_id,a.avg_cons,	avg(c.excess_cut_perc) excess_per
	FROM wo_pre_cost_fabric_cost_dtls a,
	 wo_po_color_size_breakdown c,
	 wo_pre_cos_fab_co_avg_con_dtls b   
	WHERE a.job_no=b.job_no and
	a.id=b.pre_cost_fabric_cost_dtls_id and
	c.job_no_mst=a.job_no and 
	a.fabric_source=1 and
	c.id=b.color_size_table_id and 
	c.status_active=1 and 
	c.is_deleted=0  and 
	b.status_active=1 and 
	b.is_deleted=0 and 
	a.status_active=1 and 
	a.is_deleted=0 and
	a.job_no =$txt_job_no and 
	c.po_break_down_id in(".$txt_order_no_id.") and 
	b.cons>0 
	group by a.body_part_id,a.uom,a.item_number_id,a.color_type_id,a.fabric_source,a.construction,a.composition,a.gsm_weight,b.dia_width,c.color_number_id,a.avg_cons order by 1";

	// echo $fabric_description_sql;
	$nameArray_fabric_description= sql_select($fabric_description_sql);

		$string="";
		foreach($nameArray_fabric_description as $row){


			// $string=$row[csf('fabric_cost_dtls_id')]."*".$sizes_row[csf('body_part_id')]."*".$sizes_row[csf('body_part_id')]
				$string=$row[csf('fabric_cost_dtls_id')]."*".$row[csf('color_number_id')];
				$main_data_arr[$string]['color_number_id']=$row[csf('color_number_id')];
				$main_data_arr[$string]['color_type_id']=$row[csf('color_type_id')];
				$main_data_arr[$string]['gsm_weight']=$row[csf('gsm_weight')];
				$main_data_arr[$string]['avg_cons']=$row[csf('avg_cons')];
				$main_data_arr[$string]['construction']=$row[csf('construction')];
				$main_data_arr[$string]['composition']=$row[csf('composition')];
				$main_data_arr[$string]['body_part_id']=$row[csf('body_part_id')];
				$main_data_arr[$string]['cons']=$row[csf('cons')];
				$main_data_arr[$string]['requirment']=$row[csf('requirment')];				
				$main_data_arr[$string]['process_loss_percent']=$row[csf('process_loss_percent')];
				$main_data_arr[$string]['excess_per']=$row[csf('excess_per')];
				$main_data_arr[$string]['width_dia_type']=$row[csf('width_dia_type')];
				
		}
		// echo "<pre>";
		// print_r($main_data_arr);
		?>
		<div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:2830px;text-align:center;" rules="all">
				
			<thead>
			     <tr style="font-weight:bold">
                    <th width="230"  rowspan="2">Fabric Contruction & Composition / Yarn Type</th>
                    <th width="100"  rowspan="2">Body Color</th>
                    <th width="100"  rowspan="2">Fabric Color</th>
					<th width="100"  rowspan="2">Fabric Type</th>
					<th width="100"  rowspan="2">GSM</th>
                    <th width="100"  rowspan="2">Dia/Width (Inch)</th>                   
                    <th width="100"  rowspan="2">Lab Dip No</th>
                    <th width="100"  rowspan="2">Order Qty (Pcs)</th>
					<th width="100"  rowspan="2">Excess %</th>
                    <th width="100"  rowspan="2">Plan Cut Qty(Pcs)</th>
					<th width="100"  rowspan="2">Consumption / Dzn Fin</th>
					<th width="100"  rowspan="2">Finish (Kg)</th>
                    <th width="100"  rowspan="2">Process Loss %</th>
					<th width="100"  rowspan="2">Grey (Kg)</th>
					<th width="100"  rowspan="2">Yarn Count and Description</th>

                    <th width="100"  colspan="4">Yarn</th>                   
                    <th width="100"  colspan="2">KNITTING</th>				
					<th width="100"  colspan="2">DYEING</th>
					<th width="100"  colspan="2">AOP</th>                  
					<th width="100"  colspan="3">TOTAL</th>                   
                </tr>
				<tr style="font-weight:bold">
                   
                    <th width="100">Consumption / Pcs</th>                   
                    <th width="100">Yarn Required</th>
                    <th width="100">Rate</th>
					<th width="100">Yarn Cost</th>
                    <th width="100">Rate/kg</th>
					<th width="100">Charge</th>

					<th width="100">Rate/kg</th>
					<th width="100">Charge</th>
                    <th width="100">Rate/kg</th>
					<th width="100">Charge</th>
					<th width="100">Cost</th>
                    <th width="100">Process Loss</th>
					<th width="100">Fin Fabrics Rate/Kg</th>
                </tr>
			</thead>
                <body>
					<?
					foreach($main_data_arr as $key=>$row){
						list($pre_cost_dtls_id,$color_id)=explode("*",$key);
						$lapdip_no="";
						$lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst=$txt_job_no and approval_status=3 and color_name_id=".$row['color_number_id']."");
						$yarn_details=$yarn_details_arr[$pre_cost_dtls_id][$color_id]['yarn_details'];
						$yarn_rate=$yarn_details_arr[$pre_cost_dtls_id][$color_id]['rate'];
						$yarn_qnty=$yarn_details_arr[$pre_cost_dtls_id][$color_id]['qnty'];
						$yarn_amount=$yarn_details_arr[$pre_cost_dtls_id][$color_id]['amount'];
						


						?>
				<tr>
			    	<td width="230" align="left"><?=$row['construction']." ".$row['composition'];?></td>
                    <td width="100"><?=$color_library[$color_id];?></td>
                    <td width="100">Fabric Color</td>

					<td width="100"><?=$color_type[$row['color_type_id']];?></td>
					<td width="100"><?=$row['gsm_weight'];?></td>
                    <td width="100"><?=$fabric_typee[$row['width_dia_type']];?></td>                   
                    <td width="100"><?=$lapdip_no;?></td>
                    <td width="100"><?=$job_color_wise_arr[$color_id]['order_quantity'];?></td>
					<td width="100">Excess %</td>
                    <td width="100"><?=$job_color_wise_arr[$color_id]['plan_cut_qnty'];?></td>
					<td width="100">Consumption / Dzn Fin</td>
					<td width="100"><?=$row['cons'];?></td>
                    <td width="100"><?=$row['process_loss_percent'];?></td>
					<td width="100"><?=$row['requirment'];?></td>
					<td width="100"><?=$yarn_details;?></td>
                    <td width="100"><?=number_format($yarn_qnty,5)?></td>   

             
                    <td width="100"><?=number_format($yarn_qnty,5)?></td>
                    <td width="100"><?=$yarn_rate;?></td>
					<td width="100"><?=number_format($yarn_amount,5);?></td>
                    <td width="100">Rate/kg</td>
					<td width="100">Charge</td>

					<td width="100">Rate/kg</td>
					<td width="100">Charge</td>
                    <td width="100">Rate/kg</td>
					<td width="100">Charge</td>
					<td width="100">Cost</td>
                    <td width="100">Process Loss</td>
					<td width="100">Fin Fabrics Rate/Kg</td>
                </tr>
				<?}?>
				</body>
				
     	</table>
 		</div>
				<?
	
	
	$condition= new condition();
		if(str_replace("'","",$txt_job_no) !=''){
			$condition->job_no("=$txt_job_no");
		}
		if(str_replace("'","",$txt_order_no_id) !=''){
			$condition->po_id("in($txt_order_no_id)");
		}
		$condition->init();
		$fabric= new fabric($condition);$yarn= new yarn($condition);
		//$fab_data_qty_arr=$fabric->getQtyArray_by_orderGmtscolorAndBodypart_knitAndwoven_greyAndfinish();
		$fab_data_qty_arr=$fabric->getQtyArray_by_orderFabriccostidAndGmtscolor_knitAndwoven_greyAndfinish();
		$fab_data_qty_body_arr=$fabric->getQtyArray_by_OrderFabriccostidGmtscolorAndDiaWidth_knitAndwoven_greyAndfinish();
		//print_r($fab_data_qty_arr);
		$fab_data_amt_arr=$fabric->getAmountArray_by_orderGmtscolorAndBodypart_knitAndwoven_greyAndfinish();
		$conversion= new conversion($condition);
		//echo $conversion->getQuery();die;
		$conversion_costing_arr_process=$conversion->getAmountArray_by_job();
		//print_r($fab_data_amt_arr);
	 ?>
   
		
			
				<br>
				<?
				
		$yarn_data_array=$yarn->getOrderCountCompositionPercentTypeColorAndRateWiseYarnQtyAndAmountArray();
		$cos_per_arr=$condition->getCostingPerArr();
		//print_r($yarn_data_array);
		$yarn_sql_array=sql_select("SELECT b.id as po_id, a.count_id, a.copm_one_id, a.percent_one,  a.color, a.type_id,  a.rate as rate from wo_pre_cost_fab_yarn_cost_dtls a, wo_po_break_down b where a.job_no=b.job_no_mst and a.job_no in($txt_job_no) and b.id in (".$txt_order_no_id.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.id, a.count_id, a.copm_one_id, a.percent_one,  a.color, a.type_id,  a.rate order by type_id");
		
		?>

	
      <?
	//End Conversion Cost to Fabric report here -------------------------------------------
	?>
	<br>
	</div>
	</div>
	<br/>
    <? //echo signature_table(109, $cbo_company_name, "970px");
	//exit();
	
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
	//echo "$total_data****$filename****$report_type";
	exit();	
}
if( $action=="report_generate3") //md mamun ahmed sagor/crm=3519/02-03-2022
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	//echo $txt_style;die;
	$company_name_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$yarn_count_library=return_library_array( "select id,yarn_count from lib_yarn_count", "id","yarn_count");

	$report_type=str_replace("'","",$reporttype);
	//echo $report_type;
	//echo $cbo_search_date;die;
	//cbo_style_owner*cbo_team_name*cbo_deal_marchant
	$company_name=str_replace("'","",$cbo_company_name);
	$file_no=trim(str_replace("'","",$txt_file_no));
	$internal_ref=trim(str_replace("'","",$txt_internal_ref));
	$txt_job_id=trim(str_replace("'","",$txt_job_id));
	
	$cbo_style_owner=str_replace("'","",$cbo_style_owner);
	$cbo_search_date=str_replace("'","",$cbo_search_date);
	if($company_name==0) $company_name_cond=""; else $company_name_cond=" and a.company_name=".$company_name."";
	
	if($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='$internal_ref' ";
	if($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='$file_no' ";
	
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
		if(trim($cbo_year)!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year"; else $year_cond="";
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
 	$date_cond='';	$c_date_cond='';
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
			$c_date_cond=" and c.country_ship_date between '$start_date' and '$end_date'";
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

	
		
	$job_no=str_replace("'","",$txt_job_no);
	//echo $job_no;die;
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
	$order_id=str_replace("'","",$txt_order_id);
	$order_num=str_replace("'","",$txt_order_no);
	$order_id_cond_trans="";$order_no_cond="";
	if($order_id!="" && $order_id!=0) 
	{ 
		$order_id_cond_trans=" and b.id in ($order_id)";
	}
	else if($order_id=="" && $order_num!="") 
	{
		 $order_no_cond=" and  b.po_number in ('$order_num') ";
	}
 
	
				
	 $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	 $comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	 $color_library=return_library_array( "select id, color_name from lib_color ", "id", "color_name"  );
	 $size_library=return_library_array( "select id, size_name from lib_size", "id", "size_name"  );


	 $sql_gsm="select b.gsm_weight,b.job_no,b.body_part_id from wo_po_details_master a,wo_pre_cost_fabric_cost_dtls b where a.job_no=b.job_no and b.body_part_id in(1,20) $company_name_cond $job_no_cond $year_cond $buyer_id_cond";
	$sql_gsm_data=sql_select($sql_gsm);
	foreach($sql_gsm_data as $row)
	{
		if($body_part_id==1) $gsm_weight_top=$sql_po_row[csf('gsm_weight')];
		if($body_part_id==20) $gsm_weight_bottom=$sql_po_row[csf('gsm_weight')];
	}
	  //$gsm_weight_top=return_field_value("gsm_weight", "wo_pre_cost_fabric_cost_dtls", "job_no=$txt_job_no and body_part_id=1");
	// $gsm_weight_bottom=return_field_value("gsm_weight", "wo_pre_cost_fabric_cost_dtls", "job_no=$txt_job_no and body_part_id=20");
	 $po_qty=0;
	 $po_plun_cut_qty=0;
	 $total_set_qnty=0;
 $sql_po="select a.id,a.job_no, a.buyer_name,a.total_set_qnty,a.avg_unit_price,a.style_ref_no,b.id po_id,b.pub_shipment_date,b.po_number,c.item_number_id,c.country_id,c.color_number_id,c.size_number_id,c.order_quantity , c.plan_cut_qnty  from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $company_name_cond $job_no_cond $year_cond  $date_cond $order_id_cond_trans $order_status_cond $order_no_cond $internal_ref_cond $file_no_cond $buyer_id_cond order by b.id"; 
	$sql_po_data=sql_select($sql_po);
	foreach($sql_po_data as $sql_po_row)
	{
		$po_qty+=$sql_po_row[csf('order_quantity')];
		$po_plun_cut_qty+=$sql_po_row[csf('plan_cut_qnty')];
		$job_color_wise_arr[$sql_po_row[csf('color_number_id')]]['plan_cut_qnty']+=$sql_po_row[csf('plan_cut_qnty')];
		$job_color_wise_arr[$sql_po_row[csf('color_number_id')]]['order_quantity']+=$sql_po_row[csf('order_quantity')];
		$total_set_qnty=$sql_po_row[csf('total_set_qnty')];
		$txt_job_no="'".$sql_po_row[csf('job_no')]."'";
		
		$job_in_orders .= $sql_po_row[csf('po_number')].",";
		$job_no_full .= $sql_po_row[csf('job_no')].",";
		$txt_order_no_id .= $sql_po_row[csf('po_id')].",";
		$item_number_ids .= $sql_po_row[csf('item_number_id')].",";
		$style_ref_no .= $sql_po_row[csf('style_ref_no')].",";
		$job_id .= $sql_po_row[csf('id')].",";
		$buyer_name_id= $sql_po_row[csf('buyer_name')];
		$avg_unit_price= $sql_po_row[csf('avg_unit_price')];
		
		$total_set_qnty=$sql_po_row[csf('total_set_qnty')];

		$job_wise_data_arr[$sql_po_row[csf('job_no')]]['style_ref_no']=$sql_po_row[csf('style_ref_no')];
		$job_wise_data_arr[$sql_po_row[csf('job_no')]]['buyer_name']=$buyer_arr[$sql_po_row[csf('buyer_name')]];
		$job_wise_data_arr[$sql_po_row[csf('job_no')]]['plan_cut_qnty']+=$sql_po_row[csf('plan_cut_qnty')];
		$job_wise_data_arr[$sql_po_row[csf('job_no')]]['order_quantity']+=$sql_po_row[csf('order_quantity')];
		$job_wise_data_arr[$sql_po_row[csf('job_no')]]['item_number_id'].=$garments_item[$sql_po_row[csf('item_number_id')]].",";
		$job_wise_data_arr[$sql_po_row[csf('job_no')]]['po_number'].= $sql_po_row[csf('po_number')].",";
		$job_wise_data_arr[$sql_po_row[csf('job_no')]]['pub_shipment_date'].= change_date_format($sql_po_row[csf('pub_shipment_date')]).',';
 
	}
	 
	$pub_shipment_date=rtrim($pub_shipment_date,',');
	$job_in_orders=rtrim($job_in_orders,',');$txt_order_no_id=rtrim($txt_order_no_id,',');
	$pub_shipment_dates=implode(",",array_unique(explode(",",$pub_shipment_date)));
	$job_ids=implode(",",array_unique(explode(",",rtrim($job_id,","))));
	$job_in_orders=implode(", ",array_unique(explode(",",$job_in_orders)));
	$txt_order_no_id=implode(",",array_unique(explode(",",$txt_order_no_id)));
 	$sql = "select a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.gmts_item_id, a.order_uom, a.job_quantity, a.ship_mode, a.avg_unit_price, a.total_price, b.costing_per, b.budget_minute, b.exchange_rate, b.approved, b.sew_smv, b.sew_effi_percent, c.fab_knit_req_kg, c.fab_knit_fin_req_kg, c.fab_woven_req_yds, c.fab_woven_fin_req_yds, c.fab_yarn_req_kg,b.costing_date from wo_po_details_master a, wo_pre_cost_mst b left join wo_pre_cost_sum_dtls c on  b.job_no=c.job_no where a.job_no=b.job_no and a.status_active=1 and a.id in($job_ids) $job_no_cond $company_name_cond $buyer_id_cond $year_cond  order by a.job_no";


	$data_array=sql_select($sql);
	//$buyer_name_id=$data_array[0][csf('buyer_name')];
	foreach($data_array as $row){
		$job_wise_data_arr[$row[csf('job_no')]]['costing_date']= change_date_format($row[csf('costing_date')]);
	}
	$job_no=$data_array[0][csf('job_no')];
	$tot_po_qty=$po_qty;
	$tot_po_value=$data_array[0][csf('total_price')];
	$exchange_rate=$data_array[0][csf('exchange_rate')];
	$tot_perDznValue=($tot_po_value/$tot_po_qty)*12;
		ob_start(); 
	 ?>
	 <div  class="scroll_div_inner">
		<div style="width:3650px; font-size:20px; font-weight:bold" align="center"><? echo $comp[str_replace("'","",$cbo_company_name)]; ?><br>Fabric Pre-Costing Details:</div>
   	 
				<!--Fabric Production Start-->
	 <?
		$costing_per=""; $costing_per_qnty=0;
		$costing_per_id=return_field_value( "costing_per", "wo_pre_cost_mst","job_no in($txt_job_no)");
		if($costing_per_id==1)
			{
				$costing_per="1 Dzn";
				$costing_per_qnty=12;
			}
			if($costing_per_id==2)
			{
				$costing_per="1 Pcs";
				$costing_per_qnty=1;
			}
			if($costing_per_id==3)
			{
				$costing_per="2 Dzn";
				$costing_per_qnty=24;
			}
			if($costing_per_id==4)
			{
				$costing_per="3 Dzn";
				$costing_per_qnty=36;
			}
			if($costing_per_id==5)
			{
				$costing_per="4 Dzn";
				$costing_per_qnty=48;
			}
			//$process_loss_method=return_field_value( "process_loss_method", "wo_pre_cost_fabric_cost_dtls","job_no=$txt_job_no");
			
			$nameArray_fab_uom = sql_select("select a.process_loss_method,a.uom,a.fabric_source FROM wo_pre_cost_fabric_cost_dtls a WHERE a.job_no in($txt_job_no)");
			foreach($nameArray_fab_uom as $row){
			if($row[csf('fabric_source')]==2)
			{
				$uom_wise_arr[$row[csf('uom')]]=$row[csf('uom')];
			}
				$process_loss_method=$row[csf('process_loss_method')];
			}
			//print_r($uom_wise_arr);
			
			
	$fabric_source=1;
	//if($fabric_source==1)
	//{

	//$costing_per=$cos_per_arr[str_replace("'","",$txt_job_no)];

	$nameArray_gmts_sizes = sql_select("select a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight, b.dia_width, c.size_number_id,c.size_order FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b  
		WHERE 
		a.job_no=b.job_no and
		a.id=b.pre_cost_fabric_cost_dtls_id and
		c.job_no_mst=a.job_no and 
		c.id=b.color_size_table_id and
		a.job_no =$txt_job_no and c.po_break_down_id in(".$txt_order_no_id.")  and a.fabric_source=1
		group by a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width,c.size_number_id,c.size_order order by c.size_order");
		
		$GmtsSizesArr=array();
		foreach($nameArray_gmts_sizes as $sizes_row){
			$GmtsSizesArr[$sizes_row[csf('body_part_id')]][$sizes_row[csf('color_type_id')]][$sizes_row[csf('construction')]][$sizes_row[csf('composition')]][$sizes_row[csf('gsm_weight')]][$sizes_row[csf('dia_width')]][$sizes_row[csf('size_number_id')]]=$size_library[$sizes_row[csf('size_number_id')]];
		}
		$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
		
		$nameArray_gmts_sizes = sql_select("select  a.id,a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight, b.dia_width, c.count_id,d.yarn_count ,e.composition_name,b.color_number_id,c.cons_qnty,c.rate,c.amount
		from wo_pre_cost_fabric_cost_dtls a, 
		wo_pre_cost_fab_yarn_cost_dtls c left join lib_composition_array e on c.copm_one_id=e.id,lib_yarn_count d  , 
		wo_pre_cos_fab_co_avg_con_dtls b
		where 
		a.job_no=b.job_no and
		a.id=b.pre_cost_fabric_cost_dtls_id and
		c.job_no=a.job_no and  a.fabric_source=1 and 
		c.count_id=d.id and
		a.id=c.fabric_cost_dtls_id and
		b.pre_cost_fabric_cost_dtls_id=c.fabric_cost_dtls_id and
		a.job_no =$txt_job_no
		group by  a.id,a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width,c.count_id,d.yarn_count  ,e.composition_name,b.color_number_id,c.cons_qnty,c.rate,c.amount");

		$countArr=array();
		foreach($nameArray_gmts_sizes as $sizes_row){
			$countArr[$sizes_row[csf('body_part_id')]][$sizes_row[csf('color_type_id')]][$sizes_row[csf('construction')]][$sizes_row[csf('composition')]][$sizes_row[csf('gsm_weight')]][$sizes_row[csf('dia_width')]][$sizes_row[csf('count_id')]]=$yarn_count_arr[$sizes_row[csf('count_id')]];

			// $yarn_details_arr[$sizes_row[csf('id')]][$sizes_row[csf('body_part_id')]][$sizes_row[csf('color_type_id')]][$sizes_row[csf('construction')]][$sizes_row[csf('composition')]][$sizes_row[csf('gsm_weight')]][$sizes_row[csf('dia_width')]][$sizes_row[csf('count_id')]]=$yarn_count_arr[$sizes_row[csf('count_id')]];
			$yarn_details_arr[$sizes_row[csf('id')]][$sizes_row[csf('color_number_id')]]['yarn_details']=$sizes_row[csf('yarn_count')]." ".$sizes_row[csf('composition_name')];
			$yarn_details_arr[$sizes_row[csf('id')]][$sizes_row[csf('color_number_id')]]['rate']=$sizes_row[csf('rate')];
			$yarn_details_arr[$sizes_row[csf('id')]][$sizes_row[csf('color_number_id')]]['qnty']+=($sizes_row[csf('cons_qnty')]/$tot_po_qty)*$costing_per_qnty;
			$yarn_details_arr[$sizes_row[csf('id')]][$sizes_row[csf('color_number_id')]]['amount']+=(($sizes_row[csf('cons_qnty')]/$tot_po_qty)*$costing_per_qnty)*$sizes_row[csf('rate')];
				
		}
		
	 
	
		$fabric_description_sql="SELECT a.job_no, min(a.id) as fabric_cost_dtls_id,a.item_number_id, max(a.lib_yarn_count_deter_id) as determin_id,a.body_part_id,a.uom,a.color_type_id,a.fabric_source, a.construction, a.composition, a.gsm_weight,min(a.width_dia_type) as width_dia_type, b.dia_width,avg(b.cons) as cons  , avg(b.process_loss_percent) as process_loss_percent , avg(b.requirment) as requirment,c.color_number_id,a.avg_cons,	avg(c.excess_cut_perc) excess_per
		FROM wo_pre_cost_fabric_cost_dtls a,
		wo_po_color_size_breakdown c,
		wo_pre_cos_fab_co_avg_con_dtls b   
		WHERE a.job_no=b.job_no and
		a.id=b.pre_cost_fabric_cost_dtls_id and
		c.job_no_mst=a.job_no and 
		a.fabric_source=1 and
		c.id=b.color_size_table_id and 
		c.status_active=1 and 
		c.is_deleted=0  and 
		b.status_active=1 and 
		b.is_deleted=0 and 
		a.status_active=1 and 
		a.is_deleted=0 and
		a.job_no =$txt_job_no and 
		c.po_break_down_id in(".$txt_order_no_id.") and 
		b.cons>0 
		group by a.job_no,a.body_part_id,a.uom,a.item_number_id,a.color_type_id,a.fabric_source,a.construction,a.composition,a.gsm_weight,b.dia_width,c.color_number_id,a.avg_cons order by 1";

		// echo $fabric_description_sql;
		$nameArray_fabric_description= sql_select($fabric_description_sql);

		$string="";
		foreach($nameArray_fabric_description as $row){
			

			// $string=$row[csf('fabric_cost_dtls_id')]."*".$sizes_row[csf('body_part_id')]."*".$sizes_row[csf('body_part_id')]
				$string=$row[csf('fabric_cost_dtls_id')]."*".$row[csf('color_number_id')];
				$main_data_arr[$row[csf('job_no')]][$string]['color_number_id']=$row[csf('color_number_id')];
				$main_data_arr[$row[csf('job_no')]][$string]['color_type_id']=$row[csf('color_type_id')];
				$main_data_arr[$row[csf('job_no')]][$string]['gsm_weight']=$row[csf('gsm_weight')];
				$main_data_arr[$row[csf('job_no')]][$string]['avg_cons']=$row[csf('avg_cons')];
				$main_data_arr[$row[csf('job_no')]][$string]['construction']=$row[csf('construction')];
				$main_data_arr[$row[csf('job_no')]][$string]['composition']=$row[csf('composition')];
				$main_data_arr[$row[csf('job_no')]][$string]['body_part_id']=$row[csf('body_part_id')];
				$main_data_arr[$row[csf('job_no')]][$string]['cons']=$row[csf('cons')];
				$main_data_arr[$row[csf('job_no')]][$string]['requirment']=$row[csf('requirment')];				
				$main_data_arr[$row[csf('job_no')]][$string]['process_loss_percent']=$row[csf('process_loss_percent')];
				$main_data_arr[$row[csf('job_no')]][$string]['excess_per']=$row[csf('excess_per')];
				$main_data_arr[$row[csf('job_no')]][$string]['width_dia_type']=$row[csf('width_dia_type')];
				$job_row[$row[csf('job_no')]]+=1;
				
		}
		// echo "<pre>";
		// print_r($main_data_arr);
		?>
		<div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:3630px;text-align:center;" rules="all">
				
			<thead>
			     <tr style="font-weight:bold">
					<th width="100"  rowspan="2">Job Number</th>
					<th width="100"  rowspan="2">Buyer Name</th>
					<th width="100"  rowspan="2">Order qnty</th>
					<th width="100"  rowspan="2">Plan Cut Qnty</th>
					<th width="100"  rowspan="2">Order Number</th>
					<th width="100"  rowspan="2">Garments Item</th>
					<th width="100"  rowspan="2">Costing Date</th>
					<th width="100"  rowspan="2">Shipment Date</th>
                    <th width="230"  rowspan="2">Fabric Contruction & Composition / Yarn Type</th>
                    <th width="100"  rowspan="2">Body Color</th>
                    <th width="100"  rowspan="2">Fabric Color</th>
					<th width="100"  rowspan="2">Fabric Type</th>
					<th width="100"  rowspan="2">GSM</th>
                    <th width="100"  rowspan="2">Dia/Width (Inch)</th>                   
                    <th width="100"  rowspan="2">Lab Dip No</th>
                    <th width="100"  rowspan="2">Order Qty (Pcs)</th>
					<th width="100"  rowspan="2">Excess %</th>
                    <th width="100"  rowspan="2">Plan Cut Qty(Pcs)</th>
					<th width="100"  rowspan="2">Consumption / Dzn Fin</th>
					<th width="100"  rowspan="2">Finish (Kg)</th>
                    <th width="100"  rowspan="2">Process Loss %</th>
					<th width="100"  rowspan="2">Grey (Kg)</th>
					<th width="100"  rowspan="2">Yarn Count and Description</th>

                    <th width="100"  colspan="4">Yarn</th>                   
                    <th width="100"  colspan="2">KNITTING</th>				
					<th width="100"  colspan="2">DYEING</th>
					<th width="100"  colspan="2">AOP</th>                  
					<th width="100"  colspan="3">TOTAL</th>                   
                </tr>
				<tr style="font-weight:bold">
                   
                    <th width="100">Consumption / Pcs</th>                   
                    <th width="100">Yarn Required</th>
                    <th width="100">Rate</th>
					<th width="100">Yarn Cost</th>
                    <th width="100">Rate/kg</th>
					<th width="100">Charge</th>

					<th width="100">Rate/kg</th>
					<th width="100">Charge</th>
                    <th width="100">Rate/kg</th>
					<th width="100">Charge</th>
					<th width="100">Cost</th>
                    <th width="100">Process Loss</th>
					<th width="100">Fin Fabrics Rate/Kg</th>
                </tr>
			</thead>
                <body>
					<?
					foreach($main_data_arr as $jobNo=>$job_data){
						$j=1;
						foreach($job_data as $key=>$row){
						list($pre_cost_dtls_id,$color_id)=explode("*",$key);
						$lapdip_no="";
						$lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst=$txt_job_no and approval_status=3 and color_name_id=".$row['color_number_id']."");
						$yarn_details=$yarn_details_arr[$pre_cost_dtls_id][$color_id]['yarn_details'];
						$yarn_rate=$yarn_details_arr[$pre_cost_dtls_id][$color_id]['rate'];
						$yarn_qnty=$yarn_details_arr[$pre_cost_dtls_id][$color_id]['qnty'];
						$yarn_amount=$yarn_details_arr[$pre_cost_dtls_id][$color_id]['amount'];
						
						$style_ref_no=$job_wise_data_arr[$jobNo]['style_ref_no'];
						$buyer_name=$job_wise_data_arr[$jobNo]['buyer_name'];
						$plan_cut_qnty=$job_wise_data_arr[$jobNo]['plan_cut_qnty'];
						$order_quantity=$job_wise_data_arr[$jobNo]['order_quantity'];
						$costing_date=$job_wise_data_arr[$jobNo]['costing_date'];
						
						$po_number=implode(",",array_unique(explode(",",rtrim($job_wise_data_arr[$jobNo]['po_number'],","))));
						$item_number_id=implode(",",array_unique(explode(",",rtrim($job_wise_data_arr[$jobNo]['item_number_id'],","))));
						$pub_shipment_date=implode(",",array_unique(explode(",",rtrim($job_wise_data_arr[$jobNo]['pub_shipment_date'],","))));
						// $item_number_id=implode(",",array_unique(explode(",",rtrim($job_wise_data_arr[$jobNo]['item_number_id'],","))));
						// $pub_shipment_date=implode(",",array_unique(explode(",",rtrim($job_wise_data_arr[$jobNo]['pub_shipment_date'],","))));
						
						
						

						?>
				<tr>
					<?php
					if($j==1){?>
					<td width="100" rowspan="<?=$job_row[$jobNo];?>"><?=$jobNo;?></td>
					<td width="100" rowspan="<?=$job_row[$jobNo];?>"><?=$buyer_name;?></td>
					<td width="100" rowspan="<?=$job_row[$jobNo];?>"><?=$order_quantity;?></td>
					<td width="100" rowspan="<?=$job_row[$jobNo];?>"><?=$plan_cut_qnty;?></td>
					<td width="100" rowspan="<?=$job_row[$jobNo];?>"><?=$po_number;?></td>
					<td width="100" rowspan="<?=$job_row[$jobNo];?>"><?=$item_number_id;?></td>
					<td width="100" rowspan="<?=$job_row[$jobNo];?>"><?=$costing_date;?></td>
					<td width="100" rowspan="<?=$job_row[$jobNo];?>"><?=$pub_shipment_date;?></td>
					<?$j++;}?>

			    	<td width="230" align="left"><?=$row['construction']." ".$row['composition'];?></td>
                    <td width="100"><?=$color_library[$color_id];?></td>
                    <td width="100">Fabric Color</td>

					<td width="100"><?=$color_type[$row['color_type_id']];?></td>
					<td width="100"><?=$row['gsm_weight'];?></td>
                    <td width="100"><?=$fabric_typee[$row['width_dia_type']];?></td>                   
                    <td width="100"><?=$lapdip_no;?></td>
                    <td width="100"><?=$job_color_wise_arr[$color_id]['order_quantity'];?></td>
					<td width="100">Excess %</td>
                    <td width="100"><?=$job_color_wise_arr[$color_id]['plan_cut_qnty'];?></td>
					<td width="100">Consumption / Dzn Fin</td>
					<td width="100"><?=$row['cons'];?></td>
                    <td width="100"><?=$row['process_loss_percent'];?></td>
					<td width="100"><?=$row['requirment'];?></td>
					<td width="100"><?=$yarn_details;?></td>
                    <td width="100"><?=number_format($yarn_qnty,5)?></td>   

             
                    <td width="100"><?=number_format($yarn_qnty,5)?></td>
                    <td width="100"><?=$yarn_rate;?></td>
					<td width="100"><?=number_format($yarn_amount,5);?></td>
                    <td width="100">Rate/kg</td>
					<td width="100">Charge</td>

					<td width="100">Rate/kg</td>
					<td width="100">Charge</td>
                    <td width="100">Rate/kg</td>
					<td width="100">Charge</td>
					<td width="100">Cost</td>
                    <td width="100">Process Loss</td>
					<td width="100">Fin Fabrics Rate/Kg</td>
                </tr>
				<?}
			 }?>
				</body>
				
     	</table>
 		</div>
				<?
	
	
	$condition= new condition();
		if(str_replace("'","",$txt_job_no) !=''){
			$condition->job_no("=$txt_job_no");
		}
		if(str_replace("'","",$txt_order_no_id) !=''){
			$condition->po_id("in($txt_order_no_id)");
		}
		$condition->init();
		$fabric= new fabric($condition);$yarn= new yarn($condition);
		//$fab_data_qty_arr=$fabric->getQtyArray_by_orderGmtscolorAndBodypart_knitAndwoven_greyAndfinish();
		$fab_data_qty_arr=$fabric->getQtyArray_by_orderFabriccostidAndGmtscolor_knitAndwoven_greyAndfinish();
		$fab_data_qty_body_arr=$fabric->getQtyArray_by_OrderFabriccostidGmtscolorAndDiaWidth_knitAndwoven_greyAndfinish();
		//print_r($fab_data_qty_arr);
		$fab_data_amt_arr=$fabric->getAmountArray_by_orderGmtscolorAndBodypart_knitAndwoven_greyAndfinish();
		$conversion= new conversion($condition);
		//echo $conversion->getQuery();die;
		$conversion_costing_arr_process=$conversion->getAmountArray_by_job();
		//print_r($fab_data_amt_arr);
	 ?>
   
		
			
				<br>
				<?
				
		$yarn_data_array=$yarn->getOrderCountCompositionPercentTypeColorAndRateWiseYarnQtyAndAmountArray();
		$cos_per_arr=$condition->getCostingPerArr();
		//print_r($yarn_data_array);
		$yarn_sql_array=sql_select("SELECT b.id as po_id, a.count_id, a.copm_one_id, a.percent_one,  a.color, a.type_id,  a.rate as rate from wo_pre_cost_fab_yarn_cost_dtls a, wo_po_break_down b where a.job_no=b.job_no_mst and a.job_no in($txt_job_no) and b.id in (".$txt_order_no_id.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.id, a.count_id, a.copm_one_id, a.percent_one,  a.color, a.type_id,  a.rate order by type_id");
		
		?>

	
      <?
	//End Conversion Cost to Fabric report here -------------------------------------------
	?>
	<br>
	</div>
	</div>
	<br/>
    <? //echo signature_table(109, $cbo_company_name, "970px");
	//exit();
	
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
	//echo "$total_data****$filename****$report_type";
	exit();	
}
if($action=="show_po_fabric_dtls") 
{
	echo load_html_head_contents("Po Order Dtls Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$booking_no=return_field_value( "booking_no", "wo_booking_mst","booking_type=1 and is_short in(1,2) and job_no='$job_no' ");
	//echo '34343sss';die;
	$order_arr=return_library_array( "select id, po_number from wo_po_break_down where id='$po_id'", "id", "po_number");
	$color_name_arr=return_library_array( "select id, color_name from lib_color ", "id", "color_name");
    $type_data=explode("__", $type_id);
    //print_r($type_data);
	$type_id=$type_data[0];
	$uom_type=$type_data[1];
	$actual_po_id=$type_data[2];
	$uom_cond="";
	if($uom_type)$uom_cond.=" and  a.uom =$uom_type";
	if($type_id==5 || $type_id==9)
	{
		$td_width=860;
		$row_span=10;	
	}

			$actual_po_sql=" SELECT b.id as po_id ,c.id,c.acc_po_no,c.acc_po_qty as qty
				from wo_po_details_master a, wo_po_break_down b ,wo_po_acc_po_info c
				where a.job_no=b.job_no_mst and c.po_break_down_id=b.id     and a.status_active=1   and
				a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and b.id ='$po_id' " ;
				//$c_date_cond will be actual po ship date
				$po_and_actual_po_wise=array();
				$po_wise_arr=array();

				foreach(sql_select($actual_po_sql) as $val)
				{
					$po_and_actual_po_wise[$val[csf("po_id")]][$val[csf("id")]]["name"]=$val[csf("acc_po_no")];
					$po_and_actual_po_wise[$val[csf("po_id")]][$val[csf("id")]]["qty"]+=$val[csf("qty")];				 
					$po_wise_arr[$val[csf("po_id")]]+=$val[csf("qty")];

				}


	
	?>
	<script>
		function print_window()
		{
			$("#table_body_popup tr:first").hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
			$("#table_body_popup tr:first").show();
		}	
	</script>	
	<fieldset style="width:<? echo $td_width?>px; margin-left:3px">
        <div style="width:<? echo $td_width?>px;" align="center">
        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
        </div>
        <div id="report_div" align="center">
            <table rules="all" width="<? echo $td_width?>" cellpadding="0" cellspacing="0" align="center" class="rpt_table" >
                <tr> 
                	<td colspan="5" align="center"><strong> Fabric Details </strong></td>
                </tr>
                <tr> 
                    <td width="130"><strong>Job No. :&nbsp; <? echo $job_no; ?></strong></td>
                    <td width="150"><strong> Style:&nbsp;<? echo $style; ?></strong></td>
                    <td width="150"><strong> Buyer:&nbsp;<? echo $buyer_library[$buyer_id]; ?></strong></td>
                    <td width="100"><strong> Booking No:&nbsp;<? echo $booking_no; ?></strong></td>
                </tr>
            </table>
            <table border="1" class="rpt_table" rules="all" width="<? echo $td_width?>" cellpadding="0" cellspacing="0" align="center" >
                <thead>
                    <th width="30">SL</th>
                    <th width="100">Po No</th>
                    <th width="100">Item Name</th>
                    <th width="100">Body Part</th>
                    <th width="60">Color Type</th> 
                    <th width="80">Fab. Color</th>
                    <th width="80">Constraction</th>
                    <th width="80">Composition</th>
                    <th width="50">GSM</th>
                    <th width="50">F. Dia</th>
                    <th width="90">Fab. Qty.</th>
                    <th width="">UOM</th>
                </thead>
                </table>
                 <table border="1" class="rpt_table" rules="all" width="<? echo $td_width?>" cellpadding="0" cellspacing="0" align="center" id="table_body_popup">
                <?
				if($type_id==5) // For country
					{
						  $sql_ship="select a.total_set_qnty as ratio,a.job_quantity,b.id as po_id,sum(distinct b.po_quantity) as po_quantity,b.po_number,sum(c.order_quantity) as order_quantity  from wo_po_details_master a,wo_po_break_down b, wo_po_color_size_breakdown c where  b.job_no_mst=a.job_no  and c.po_break_down_id=b.id and c.job_no_mst=a.job_no and  b.id in($po_id) and c.country_id in($country_id) group by a.total_set_qnty,a.job_quantity,b.id,b.po_number";	
						$ship_sql_result=sql_select($sql_ship);
						$po_quantity=0;$order_quantity=0;
						foreach($ship_sql_result as $row)
						{
							$order_quantity+=$row[csf('order_quantity')]/$row[csf('ratio')];
							$po_quantity+=$row[csf('po_quantity')];
						}
					}
		
				//$fab_finish_req=($fab_finish/$job_quantity)*$row[csf('order_quantity')];
				//and a.body_part_id in(1,10,11,14,15,16,17,20,69,95,100,125,128,129,131,132,135,143,149,152,164,167,171,191,198,201,208,219)
				$sql_book="SELECT a.item_number_id as gmts_item_id,a.body_part_id,a.job_no,b.booking_no,c.fabric_color_id,c.color_type,a.uom,c.construction,c.copmposition,c.gsm_weight,c.dia_width,c.po_break_down_id as po_id,sum(c.grey_fab_qnty) as grey_fab_qnty from wo_pre_cost_fabric_cost_dtls a,wo_booking_mst b,wo_booking_dtls c where a.id=c.pre_cost_fabric_cost_dtls_id and a.job_no=c.job_no and c.booking_no=b.booking_no  and b.booking_type=1 and b.is_short in(1,2) and b.company_id='$company_id'  and c.po_break_down_id in($po_id)  $uom_cond and c.status_active=1 and c.is_deleted=0 group by a.item_number_id ,a.body_part_id,a.job_no,b.booking_no,c.fabric_color_id,c.color_type,a.uom,c.construction,c.copmposition,c.gsm_weight,c.dia_width,c.po_break_down_id";
				//and c.status_active=1 and c.is_deleted=0
				$po_sql_result=sql_select($sql_book); $i=1;$total_fin_fab_qnty=0;
				foreach($po_sql_result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					
					$gmts_item_id=array_unique(explode(",",$row[csf('gmts_item_id')]));
					$gmts_item="";
					foreach($gmts_item_id as $item_id)
							{
								if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
								//if($body_item=="") $body_item=$body_part[$pre_cost_data_arr[$row[csf('job_no')]][$item_id]['body']]; else $body_item.=",".$body_part[$pre_cost_data_arr[$row[csf('job_no')]][$item_id]['body']];
								
							}
						if($type_id==5) // For country
						{
							$fab_finish=$row[csf('grey_fab_qnty')];
								//echo $fab_finish.'**'.$po_quantity.'**'.$order_quantity;
								//$fab_finish_req=($fab_finish/$row[csf('po_quantity')])*$country_ship_qty;
							 $fin_fab_qnty=($fab_finish/$po_quantity)*$order_quantity;
							 if($uom_type && $actual_po_id)
							 {
							 	 
							 	$actual_po_qty=$po_and_actual_po_wise[$po_id][$actual_po_id]["qty"];
								$total_actual=$po_wise_arr[$po_id];
								$fin_fab_qnty=($fab_finish*$actual_po_qty)/$total_actual;

							 }
						}
						else //For PO Wise
						{
							 $fin_fab_qnty=$row[csf('grey_fab_qnty')];
						}
							//echo $fab_finish.'/'.$job_quantity.'*'.$order_quantity.'<br>';
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="30"><p><? echo $i; ?></p></td>
						<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $order_arr[$row[csf('po_id')]]; ?></div></td>
						<td width="100"><div style="word-wrap:break-word; width:80px"><? echo $gmts_item; ?></div></td>
                        <td width="100"><div style="word-wrap:break-word; width:80px"><? echo $body_part[$row[csf('body_part_id')]]; ?></div></td>
                         
						<td width="60" align="center"><p><? echo $color_type[$row[csf('color_type')]]; ?></p></td>
                        <td width="80" align="center"><p><? echo $color_name_arr[$row[csf('fabric_color_id')]]; ?></p></td>
                        <td width="80" align="center"><p><? echo $row[csf('construction')]; ?></p></td>
						<td width="80" align="center"><p><? echo $row[csf('copmposition')]; ?></p></td>
                         <td width="50" align="center"><p><? echo $row[csf('gsm_weight')]; ?></p></td>
						<td width="50" align="center"><p><? echo $row[csf('dia_width')]; ?></p></td>
						<td width="90" align="right" title="Booking Qty(<? echo number_format($fin_fab_qnty,0); ?>)/PO Qty(<? echo number_format($po_quantity,0); ?>)*Country Qty(<? echo $order_quantity; ?>)"><p><? echo number_format($fin_fab_qnty,2); ?></p></td>
						<td width="" align="center"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
					</tr>
					<?
					
					$total_fin_fab_qnty+=$fin_fab_qnty;
					$i++;
				}
				?>
				
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="<? echo $row_span ?>" align="right">Total</td>
                        
						<td align="right"><? echo number_format($total_fin_fab_qnty,2); ?></td>
                        <td align="right"></td>
                        
					</tr>
				</tfoot>
			</table>
         <script>   setFilterGrid("table_body_popup",-1);</script>
		</div>
	</fieldset>
	<?
	exit();
} //Po wise button end
if($action=="show_po_ship_date_dtls") //show_po_ship_qty_dtls
{
	echo load_html_head_contents("Po Order Dtls Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$order_arr=return_library_array( "select id, po_number from wo_po_break_down where id='$po_id'", "id", "po_number");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	
	if($type_id==6)
	{
		$td_width=630;
			//echo $width;
		$row_span=5;	
	}

	?>
	<script>
		function print_window()
		{
			$("#table_body_popup tr:first").hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
			$("#table_body_popup tr:first").show();
		}	
	</script>	
	<fieldset style="width:<? echo $td_width?>px; margin-left:3px">
        <div style="width:<? echo $td_width?>px;" align="center">
        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
        </div>
        <div id="report_div" align="center">
            <table rules="all" width="<? echo $td_width?>" cellpadding="0" cellspacing="0" align="center">
                <tr> 
                	<td colspan="5" align="center"><strong> Order Country Shipdate Details </strong></td>
                </tr>
                <tr> 
                    <td width="150"><strong>Job No. :&nbsp; <? echo $job_no; ?></strong></td>
                    <td width="180"><strong> Style:&nbsp;<? echo $style; ?></strong></td>
                    <td width="150" colspan="2"><strong> Buyer:&nbsp;<? echo $buyer_library[$buyer_id]; ?></strong></td>
                   
                </tr>
            </table>
            <table border="1" class="rpt_table" rules="all" width="<? echo $td_width?>" cellpadding="0" cellspacing="0" align="center" >
                <thead>
                    <th width="30">SL</th>
                    <th width="100">Po No</th>
                    <th width="80">Country ShipDate</th>
                    <th width="80">Country</th>
                    <th width="60">Up Charge</th>
                    <th width="90">Order Qty</th>
                    <th width="90">Unit Price</th>
                    <th>Order Value</th>
                </thead>
                </table>
                 <table border="1" class="rpt_table" rules="all" width="<? echo $td_width?>" cellpadding="0" cellspacing="0" align="center" id="table_body_popup">
                <?
			$po_sql="select  b.po_number,c.country_id,b.shipment_date as orgi_shipment_date ,b.is_confirmed,c.country_ship_date,c.order_quantity as po_qty, b.unit_price as unit_price,b.up_charge as up_charge from wo_po_break_down b,wo_po_color_size_breakdown c  where   b.id=c.po_break_down_id and b.id in($po_id) and b.status_active=1 and b.is_deleted=0 and c.country_ship_date='$ship_date'";
				$po_sql_result=sql_select($po_sql); $i=1;
				foreach($po_sql_result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					
					
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="30"><p><? echo $i; ?></p></td>
						<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[csf('po_number')]; ?></div></td>
						<td width="80"><div style="word-wrap:break-word; width:80px"><? echo change_date_format($row[csf('country_ship_date')]); ?></div></td>
                        <td width="80"><div style="word-wrap:break-word; width:80px"><? echo $country_arr[$row[csf('country_id')]]; ?></div></td>
                         
						<td width="60" align="right"><p><? echo number_format($row[csf('up_charge')],2); ?></p></td>
                        <td width="90" align="right"><p><? echo number_format($row[csf('po_qty')]); ?></p></td>
						<td width="90" align="right"><p><? echo number_format($row[csf('unit_price')],2); ?></p></td>
						<td width="" align="right"><p><? echo number_format($row[csf('po_qty')]*$row[csf('unit_price')],2); ?></p></td>
                       
					</tr>
					<?
					$tot_po_qty+=$row[csf('po_qty')];
					//$tot_plan_cut_qty+=$row[csf('plan_cut_qty')];
					$tot_order_value+=$row[csf('po_qty')]*$row[csf('unit_price')];
					$i++;
				}
				?>
				
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="<? echo $row_span ?>" align="right">Total</td>
                        
						<td align="right"><? echo number_format($tot_po_qty,2); ?></td>
                        <td align="right">&nbsp;</td>
                       
                        <td align="right"><? echo number_format($tot_order_value,2); ?></td>
					</tr>
				</tfoot>
			</table>
         <script>   setFilterGrid("table_body_popup",-1);</script>
		</div>
	</fieldset>
	<?
	exit();
} //Po wise button end
if($action=="show_po_ship_qty_dtls") //show_invo_ship_qty_dtls
{
		echo load_html_head_contents("Po Order Dtls Info", "../../../../", 1, 1,'','','');
		extract($_REQUEST);
		
		
		$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
		$company_name_arr=return_library_array( "select id, company_name from  lib_company",'id','company_name');
		$type_data=explode("__", $type_id);
		$type_id=$type_data[0];
		$actual_po_id=$type_data[1];
		if($actual_po_id)
		{
			$actual_po_sql=" SELECT b.id as po_id ,c.id,c.acc_po_no,c.acc_po_qty as qty
			from wo_po_details_master a, wo_po_break_down b ,wo_po_acc_po_info c
			where a.job_no=b.job_no_mst and c.po_break_down_id=b.id     and a.status_active=1   and
			a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and b.id ='$po_id' " ;
			$po_and_actual_po_wise=array();
			$po_wise_arr=array();

			foreach(sql_select($actual_po_sql) as $val)
			{
				$po_and_actual_po_wise[$val[csf("po_id")]][$val[csf("id")]]["name"]=$val[csf("acc_po_no")];
				$po_and_actual_po_wise[$val[csf("po_id")]][$val[csf("id")]]["qty"]+=$val[csf("qty")];				 
				$po_wise_arr[$val[csf("po_id")]]+=$val[csf("qty")];

			}
			$actual_po_qty=$po_and_actual_po_wise[$po_id][$actual_po_id]["qty"];
			$total_actual=$po_wise_arr[$po_id];
		}

		if($type_id==7 || $type_id==10)
		{
			$td_width=500;
				//echo $width;
			$row_span=5;	
		}
		$sql_ship="select a.total_set_qnty as ratio,b.id as po_id,b.po_number from wo_po_details_master a,wo_po_break_down b where  b.job_no_mst=a.job_no and  b.id=$po_id ";					
		$ship_sql_result=sql_select($sql_ship);
		foreach($ship_sql_result as $row)
		{
			$po_number=$row[csf('po_number')];
			$ratio=$row[csf('ratio')];
		}
	?>
	<script>
		function print_window()
		{
			$("#table_body_popup tr:first").hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
			$("#table_body_popup tr:first").show();
		}	
	</script>	
	<fieldset style="width:<? echo $td_width?>px; margin-left:20px">
        <div style="width:<? echo $td_width?>px;" align="center">
        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
        </div>
        <div id="report_div" align="center">
            <table rules="all" width="<? echo $td_width?>" cellpadding="0" cellspacing="0" align="center">
                <tr> 
                	<td colspan="5" align="center"><strong> Ship Qty Details </strong></td>
                </tr>
                <tr style="display:none"> 
                    <td width="150"><strong>Job No. :&nbsp; <? echo $job_no; ?></strong></td>
                    <td width="180"><strong> Style:&nbsp;<? echo $style; ?></strong></td>
                    <td width="150" colspan="2"><strong> Buyer:&nbsp;<? echo $buyer_library[$buyer_id]; ?></strong></td>
                   
                </tr>
            </table>
            <table border="1" class="rpt_table" rules="all" width="<? echo $td_width?>" cellpadding="0" cellspacing="0" align="center" >
                <thead>
                    <th width="30">SL</th>
                    <th width="100">Challan Date</th>
                    <th width="80">Challan No</th>
                    <th width="150">Delivery Company</th>
                    <th width="60">Ship Mode</th>
                    <th width="">Challan Qty</th>
                </thead>
                </table>
                 <table border="1" class="rpt_table" rules="all" width="<? echo $td_width?>" cellpadding="0" cellspacing="0" align="center" id="table_body_popup">
                <?
				if($type_id==7)
				{
					$country_cond="and b.country_id in($country_id)";
				}
				else if($type_id==10)
				{
					$country_cond="";
				}
				if($actual_po_id) $conds=" and actual_po='$actual_po_id'";
			 $po_sql="SELECT a.delivery_company_id,b.country_id,
			 (CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_fact_qty,
			 (CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_fact_ret_qty,
			 b.ex_factory_date,b.challan_no,b.shiping_mode from  pro_ex_factory_delivery_mst a ,pro_ex_factory_mst b  where  a.id=b.delivery_mst_id  and b.po_break_down_id in($po_id) $conds and b.status_active=1 and b.is_deleted=0 $country_cond  order by a.id";
				$po_sql_result=sql_select($po_sql); $i=1;$tot_ship_qty=0;
				foreach($po_sql_result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					
					$ex_factory_qnty=($row[csf('ex_fact_qty')]-$row[csf('ex_fact_ret_qty')]) ;
					//if($actual_po_id) $ex_factory_qnty=($ex_factory_qnty*$actual_po_qty)/$total_actual;
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="30"><p><? echo $i; ?></p></td>
						<td width="100"><div style="word-wrap:break-word; width:100px"><? echo  change_date_format($row[csf('ex_factory_date')]); ?></div></td>
						<td width="80"><div style="word-wrap:break-word; width:80px"><? echo $row[csf('challan_no')]; ?></div></td>
                        <td width="150"><div style="word-wrap:break-word; width:150px"><? echo $company_name_arr[$row[csf('delivery_company_id')]]; ?></div></td>
                         
						<td width="60" align="right"><p><? echo $shipment_mode[$row[csf('shiping_mode')]]; ?></p></td>
                     
						<td width="" align="right"><p><? echo number_format($ex_factory_qnty,0); ?></p></td>
                       
					</tr>
					<?
					$tot_ship_qty+=$ex_factory_qnty;
					//$tot_plan_cut_qty+=$row[csf('plan_cut_qty')];
					$tot_order_value+=$row[csf('po_qty')]*$row[csf('unit_price')];
					$i++;
				}
				?>
				
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="<? echo $row_span ?>" align="right">Total</td>
                        
						<td align="right"><? echo number_format($tot_ship_qty,0); ?></td>
                       
					</tr>
				</tfoot>
			</table>
         <script>   setFilterGrid("table_body_popup",-1);</script>
		</div>
	</fieldset>
	<?
	exit();
}
if($action=="show_invo_ship_qty_dtls") //
{
		echo load_html_head_contents("Po Invoice Dtls Info", "../../../../", 1, 1,'','','');
		extract($_REQUEST);
		
		
		$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
		$company_name_arr=return_library_array( "select id, company_name from  lib_company",'id','company_name');
		
		if($type_id==8 || $type_id==10)
		{
			$td_width=430;
				//echo $width;
			$row_span=4;	
		}
		$sql_ship="select a.total_set_qnty as ratio,b.id as po_id,b.po_number from wo_po_details_master a,wo_po_break_down b where  b.job_no_mst=a.job_no and  b.id=$po_id ";					
		$ship_sql_result=sql_select($sql_ship);
		foreach($ship_sql_result as $row)
		{
			$po_number=$row[csf('po_number')];
			$ratio=$row[csf('ratio')];
		}
	?>
	<script>
		function print_window()
		{
			$("#table_body_popup tr:first").hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
			$("#table_body_popup tr:first").show();
		}	
	</script>	
	<fieldset style="width:<? echo $td_width?>px; margin-left:20px">
        <div style="width:<? echo $td_width?>px;" align="center">
        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
        </div>
        <div id="report_div" align="center">
            <table rules="all" width="<? echo $td_width?>" cellpadding="0" cellspacing="0" align="center">
                <tr> 
                	<td colspan="5" align="center"><strong> Invoice Qty Details </strong></td>
                </tr>
                
            </table>
            <table border="1" class="rpt_table" rules="all" width="<? echo $td_width?>" cellpadding="0" cellspacing="0" align="center" >
                <thead>
                    <th width="30">SL</th>
                    <th width="100">invoice_date</th>
                    <th width="120">invoice_no No</th>
                    <th width="60">Ship Mode</th>
                    <th width="">Ship Qty</th>
                </thead>
                </table>
                 <table border="1" class="rpt_table" rules="all" width="<? echo $td_width?>" cellpadding="0" cellspacing="0" align="center" id="table_body_popup">
                <?
				
				if($type_id==8)
				{
					$country_cond="and a.country_id in($country_id)";
				}
				else if($type_id==10)
				{
					$country_cond="";
				}
				$sql_inv="select a.invoice_date,a.invoice_no,a.shipping_mode,b.po_breakdown_id as po_id,(a.invoice_date) as invoice_date,
			(CASE WHEN a.shipping_mode=1 THEN b.current_invoice_qnty ELSE 0 END) as sea_qnty,
			(CASE WHEN a.shipping_mode=2 THEN b.current_invoice_qnty ELSE 0 END) as air_qnty
			 from  com_export_invoice_ship_mst a,com_export_invoice_ship_dtls b where a.id=b.mst_id and b.po_breakdown_id in($po_id)   and a.shipping_mode in(1,2)  and a.status_active ='1' and a.is_deleted ='0' $country_cond ";
				
			/* $po_sql="select a.delivery_company_id,b.country_id,
			 (CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_fact_qty,
			 (CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_fact_ret_qty,
			 b.ex_factory_date,b.challan_no,b.shiping_mode from  com_export_invoice_ship_mst a ,com_export_invoice_ship_mst b  where  a.id=b.delivery_mst_id  and b.po_break_down_id in($po_id) and b.country_id in($country_id) and b.status_active=1 and b.is_deleted=0  order by a.id";*/
				$po_sql_result=sql_select($sql_inv); $i=1;$total_ship_qty=0;
				foreach($po_sql_result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					
					$tot_ship_qnty=($row[csf('sea_qnty')]+$row[csf('air_qnty')])/$ratio;
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="30"><p><? echo $i; ?></p></td>
						<td width="100"><div style="word-wrap:break-word; width:100px"><? echo  change_date_format($row[csf('invoice_date')]); ?></div></td>
						<td width="120"><div style="word-wrap:break-word; width:120px"><? echo $row[csf('invoice_no')]; ?></div></td>
                        <td width="60" align="right"><p><? echo $shipment_mode[$row[csf('shipping_mode')]]; ?></p></td>
						<td width="" align="right"><p><? echo number_format($tot_ship_qnty,0); ?></p></td>
                       
					</tr>
					<?
					$total_ship_qty+=$tot_ship_qnty;
					$i++;
				}
				?>
				
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="<? echo $row_span ?>" align="right">Total</td>
                        
						<td align="right"><? echo number_format($total_ship_qty,0); ?></td>
                       
					</tr>
				</tfoot>
			</table>
         <script>   setFilterGrid("table_body_popup",-1);</script>
		</div>
	</fieldset>
	<?
	exit();
}
//Job Button Start
if($action=="show_po_listview_report")
{
	echo load_html_head_contents("Po Order Dtls Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$order_arr=return_library_array( "select id, po_number from wo_po_break_down where id='$po_id'", "id", "po_number");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	
	if($type_id==1)
	{
		$td_width=710;
		$row_span=5;	
	}
	else if($type_id==2)
	{
		$td_width=710+80;
		$row_span=5;	
	}
	?>
	<script>
		function print_window()
		{
			$("#table_body_popup tr:first").hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
			$("#table_body_popup tr:first").show();
		}	
	</script>	
	<fieldset style="width:<? echo $td_width?>px; margin-left:3px">
        <div style="width:<? echo $td_width?>px;" align="center">
        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
        </div>
        <div id="report_div" align="center">
            <table rules="all" width="<? echo $td_width?>" cellpadding="0" cellspacing="0" align="center">
                <tr> 
                	<td colspan="5" align="center"><strong> Order Details </strong></td>
                </tr>
                <tr> 
                    <td width="130"><strong>Job No. :&nbsp; <? echo $job_no; ?></strong></td>
                    <td width="150"><strong> Style:&nbsp;<? echo $style; ?></strong></td>
                    <td width="150" colspan="2"><strong> Buyer:&nbsp;<? echo $buyer_library[$buyer_id]; ?></strong></td>
                   
                </tr>
            </table>
            <table border="1" class="rpt_table" rules="all" width="<? echo $td_width?>" cellpadding="0" cellspacing="0" align="center" >
                <thead>
                    <th width="30">SL</th>
                    <th width="100">Po No</th>
                    <th width="80">Ship Date</th>
                    <th width="80">PO Recv. Date</th>
                    <th width="60">Up Charge</th>
                    <th width="90">Order Qty</th>
                    <th width="90">Unit Price</th>
                    <th>Order Value</th>
                </thead>
                </table>
                 <table border="1" class="rpt_table" rules="all" width="<? echo $td_width?>" cellpadding="0" cellspacing="0" align="center" id="table_body_popup">
                <?
				$po_sql="select  po_number,po_received_date,shipment_date as orgi_shipment_date 	,is_confirmed,pub_shipment_date,po_quantity as po_qty, unit_price as unit_price,up_charge as up_charge from wo_po_break_down where  id in($po_id) and status_active=1 and is_deleted=0";
				$po_sql_result=sql_select($po_sql); $i=1;
				foreach($po_sql_result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					
					
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="30"><p><? echo $i; ?></p></td>
						<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[csf('po_number')]; ?></div></td>
						<td width="80"><div style="word-wrap:break-word; width:80px"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></div></td>
                        <td width="80"><div style="word-wrap:break-word; width:80px"><? echo change_date_format($row[csf('po_received_date')]); ?></div></td>
                         
						<td width="60" align="right"><p><? echo number_format($row[csf('up_charge')],2); ?></p></td>
                        <td width="90" align="right"><p><? echo number_format($row[csf('po_qty')]); ?></p></td>
						<td width="90" align="right"><p><? echo number_format($row[csf('unit_price')],2); ?></p></td>
						<td width="" align="right"><p><? echo number_format($row[csf('po_qty')]*$row[csf('unit_price')],2); ?></p></td>
                       
					</tr>
					<?
					$tot_po_qty+=$row[csf('po_qty')];
					//$tot_plan_cut_qty+=$row[csf('plan_cut_qty')];
					$tot_order_value+=$row[csf('po_qty')]*$row[csf('unit_price')];
					$i++;
				}
				?>
				
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="<? echo $row_span ?>" align="right">Total</td>
                        
						<td align="right"><? echo number_format($tot_po_qty,2); ?></td>
                        <td align="right">&nbsp;</td>
                       
                        <td align="right"><? echo number_format($tot_order_value,2); ?></td>
					</tr>
				</tfoot>
			</table>
         <script>   setFilterGrid("table_body_popup",-1);</script>
		</div>
	</fieldset>
	<?
	exit();
} //Po wise button end

if($action=="show_exf_po_listview_report")
{
	echo load_html_head_contents("Ex-Factory Dtls Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$order_arr=return_library_array( "select id, po_number from wo_po_break_down where id in($po_id)", "id", "po_number");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	if($type_id==3)
	{
		$td_width=520;
			
	}
	?>
	<script>
		function print_window()
		{
			$("#table_body_popup tr:first").hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
			$("#table_body_popup tr:first").show();
		}	
	</script>	
	<fieldset style="width:<? echo $td_width?>px; margin-left:3px">
        <div style="width:<? echo $td_width?>px;" align="center">
        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
        </div>
        <div id="report_div" align="center">
            <table rules="all" width="<? echo $td_width?>" cellpadding="0" cellspacing="0" align="center">
                <tr> 
                	<td colspan="5" align="center"><strong> Ex-Factory Details </strong></td>
                </tr>
                <tr> 
                    <td width="130"><strong>Job No. :&nbsp; <? echo $job_no; ?></strong></td>
                    <td width="150"><strong> Style:&nbsp;<? echo $style; ?></strong></td>
                    <td width="150" colspan="2"><strong> Buyer:&nbsp;<? echo $buyer_library[$buyer_id]; ?></strong></td>
                   
                </tr>
            </table>
            <table border="1" class="rpt_table" rules="all" width="<? echo $td_width?>" cellpadding="0" cellspacing="0" align="center" >
                <thead>
                    <th width="30">SL</th>
                    <th width="100">Po No</th>
                    <th width="100">Challan No</th>
                    <th width="90">Ex-Factory Date</th>
                    <th width="90">Ex-Factory Qty</th>
                    <th width="">Total Carton Qty</th>
                   
                </thead>
                </table>
                 <table border="1" class="rpt_table" rules="all" width="<? echo $td_width?>" cellpadding="0" cellspacing="0" align="center" id="table_body_popup">
                <?
				$exfactory_data=sql_select("select po_break_down_id as po_id,challan_no,ex_factory_date as ex_fact_date,
			sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as ex_fact_qty,
			sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as ex_fact_ret_qty,
			sum(CASE WHEN entry_form!=85 THEN total_carton_qnty ELSE 0 END) as tot_carton_qty,
			sum(CASE WHEN entry_form=85 THEN total_carton_qnty ELSE 0 END) as tot_ret_carton_qty
			 from pro_ex_factory_mst  where 1=1 and status_active=1 and is_deleted=0 and po_break_down_id in($po_id) group by po_break_down_id,challan_no,ex_factory_date");
				
			$i=1;$tot_exf_qty=0;$tot_carton_qty=0;
				foreach($exfactory_data as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="30"><p><? echo $i; ?></p></td>
						<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $order_arr[$row[csf('po_id')]]; ?></div></td>
						<td width="100"><div style="word-wrap:break-word; width:80px"><? echo $row[csf('challan_no')]; ?></div></td>
                        <td width="90"><div style="word-wrap:break-word; width:80px"><? echo change_date_format($row[csf('ex_fact_date')]); ?></div></td>
                         <td width="90" align="right"><p><? echo number_format($row[csf('ex_fact_qty')]-$row[csf('ex_fact_ret_qty')],2); ?></p></td>
                        <td width="" align="right"><p><? echo number_format($row[csf('tot_carton_qty')]-$row[csf('tot_ret_carton_qty')],2); ?></p></td>
                       
					</tr>
					<?
					$tot_exf_qty+=$row[csf('ex_fact_qty')]-$row[csf('ex_fact_ret_qty')];
					//$tot_plan_cut_qty+=$row[csf('plan_cut_qty')];
					$tot_carton_qty+=$row[csf('tot_carton_qty')]-$row[csf('tot_ret_carton_qty')];
					$i++;
				}
				?>
				
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="4" align="right">Total</td>
                         <td align="right"><? echo number_format($tot_exf_qty,2); ?></td>
						<td align="right"><? echo number_format($tot_carton_qty,2); ?></td>
                        
					</tr>
				</tfoot>
			</table>
         <script>   setFilterGrid("table_body_popup",-1);</script>
		</div>
	</fieldset>
	<?
	exit();
}

if($action=="show_labdip_listview_report")//
{
	echo load_html_head_contents("LapDip Dtls Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$order_arr=return_library_array( "select id, po_number from wo_po_break_down where id in($po_id)", "id", "po_number");
	$color_name_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	if($type_id==4)
	{
		$td_width=500;
			
	}
	?>
	<script>
		function print_window()
		{
			$("#table_body_popup tr:first").hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
			$("#table_body_popup tr:first").show();
		}	
	</script>	
	<fieldset style="width:<? echo $td_width?>px; margin-left:3px">
        <div style="width:<? echo $td_width?>px;" align="center">
        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
        </div>
        <div id="report_div" align="center">
            <table rules="all" width="<? echo $td_width?>" cellpadding="0" cellspacing="0" align="center">
                <tr> 
                	<td colspan="5" align="center"><strong> Labdip Details </strong></td>
                </tr>
                <tr> 
                    <td width="130"><strong>Job No. :&nbsp; <? echo $job_no; ?></strong></td>
                    <td width="150"><strong> Style:&nbsp;<? echo $style; ?></strong></td>
                    <td width="150" colspan="2"><strong> Buyer:&nbsp;<? echo $buyer_library[$buyer_id]; ?></strong></td>
                   
                </tr>
            </table>
            <table border="1" class="rpt_table" rules="all" width="<? echo $td_width?>" cellpadding="0" cellspacing="0" align="center" >
                <thead>
                    <th width="30">SL</th>
                    <th width="100">Po No</th>
                    <th width="100">Labdip No</th>
                    <th width="90">Color</th>
                    <th width="90">Submit to Buyer Date</th>
                    <th width="">Action</th>
                   
                </thead>
                </table>
                 <table border="1" class="rpt_table" rules="all" width="<? echo $td_width?>" cellpadding="0" cellspacing="0" align="center" id="table_body_popup">
                <?
				$sql_lab=sql_select("Select a.id,a.lapdip_no,a.submitted_to_buyer,a.current_status,a.po_break_down_id as po_id,a.color_name_id from wo_po_lapdip_approval_info a where a.status_active =1 and a.is_deleted=0 and a.po_break_down_id in($po_id) group by a.id,a.lapdip_no,a.submitted_to_buyer,a.current_status,a.po_break_down_id,a.color_name_id ");
			 
				//$po_sql="select  po_number,po_received_date,shipment_date as orgi_shipment_date 	,is_confirmed,pub_shipment_date,po_quantity as po_qty, unit_price as unit_price,up_charge as up_charge from  pro_ex_factory_mst where  id in($po_id) and status_active=1 and is_deleted=0";
				//$po_sql_result=sql_select($po_sql); $i=1;
				$i=1;
				foreach($sql_lab as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="30"><p><? echo $i; ?></p></td>
						<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $order_arr[$row[csf('po_id')]]; ?></div></td>
						<td width="100"><div style="word-wrap:break-word; width:80px"><? echo $row[csf('lapdip_no')]; ?></div></td>
                        <td width="90"><div style="word-wrap:break-word; width:90px"><? echo $color_name_arr[$row[csf('color_name_id')]]; ?></div></td>
                         <td width="90" align="right"><p><? echo change_date_format($row[csf('submitted_to_buyer')]); ?></p></td>
                        <td width="" align="right"><p><? echo $approval_status[$row[csf('current_status')]]; ?></p></td>
                       
					</tr>
					<?
					
					$i++;
				}
				?>
				
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="4" align="right"></td>
                         <td align="right"><? //echo number_format($tot_exf_qty,2); ?></td>
						<td align="right"><? //echo number_format($tot_carton_qty,2); ?></td>
                        
					</tr>
				</tfoot>
			</table>
         <script>   setFilterGrid("table_body_popup",-1);</script>
		</div>
	</fieldset>
	<?
	exit();
}
?>