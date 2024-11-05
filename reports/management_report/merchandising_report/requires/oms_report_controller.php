<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.fabrics.php');
require_once('../../../../includes/class4/class.others.php');
require_once('../../../../includes/class4/class.commisions.php');


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
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "load_drop_down( 'requires/oms_report_controller', this.value, 'load_drop_down_season', 'season_td'); " );     	 
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'oms_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','') ;
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
	if($db_type==0)
	{
		if ($data[2]=="") $job_no_cond=""; else $job_no_cond="  and FIND_IN_SET(b.job_no_prefix_num,'$data[2]')";
	}
	else if($db_type==2)
	{
		if ($data[2]=="") $job_no_cond=""; else $job_no_cond="  and ',' || b.job_no_prefix_num || ',' LIKE '%$data[2]%' ";
	}
	
	$sql="select a.id, a.po_number, b.job_no_prefix_num, b.job_no, b.buyer_name, b.style_ref_no from wo_po_details_master b, wo_po_break_down a  where b.job_no=a.job_no_mst and b.company_name=$data[0] and b.is_deleted=0 $buyer_name $job_no_cond ORDER BY b.job_no";
	//echo $sql;
	$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
	$arr=array(1=>$buyer);
	
	echo  create_list_view("list_view", "Job No,Buyer,Style Ref.,Order No", "110,110,150,180","610","350",0, $sql, "js_set_value", "id,po_number", "", 1, "0,buyer_name,0,0,0", $arr , "job_no_prefix_num,buyer_name,style_ref_no,po_number", "oms_report_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0','') ;
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
	
	echo  create_list_view("list_view", "Job No,Buyer,Style Ref.,Order No,Actual Po", "110,110,150,130,100","610","350",0, $sql, "js_set_value", "id,acc_po_no", "", 1, "0,buyer_name,0,0,0,0", $arr , "job_no_prefix_num,buyer_name,style_ref_no,po_number,acc_po_no", "oms_report_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0','') ;
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
	$season=str_replace("'","",$cbo_season_id);
	
	$cbo_style_owner=str_replace("'","",$cbo_style_owner);
	$cbo_team_name=str_replace("'","",$cbo_team_name);
	$cbo_deal_marchant=str_replace("'","",$cbo_deal_marchant);
	 	//dealing_marchant team_leader style_owner
	$cbo_search_date=str_replace("'","",$cbo_search_date);
	
	if($cbo_style_owner==0) $style_owner_cond=""; else $style_owner_cond=" and a.style_owner='$cbo_style_owner' ";
	if($cbo_team_name==0) $team_leader_cond=""; else $team_leader_cond=" and a.team_leader='$cbo_team_name' ";
	if($cbo_deal_marchant==0) $dealing_marchant_cond=""; else $dealing_marchant_cond=" and a.dealing_marchant='$cbo_deal_marchant' ";
	
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

	else if(str_replace("'","",$cbo_search_date)==4)// Actual Po Date
	{
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
				$date_cond4=" and d.acc_ship_date between '".$start_date."' and '".$end_date."'";
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
				$date_cond4=" and d.acc_ship_date between '".$start_date."' and '".$end_date."'";
			}
			$date_max_profit=" and applying_period_to_date between '$start_date' and '$end_date'";
		} 
	}
	else{
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
		
	$job_no=str_replace("'","",$txt_job_no);
	//echo $job_no;die;
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
	//if($season=="") $season_cond=""; else $season_cond=" and a.season in('".implode("','",explode(",",$season))."')";
	if($season==0) $season_cond=""; else $season_cond=" and a.season_matrix in($season)";
	
	//if($txt_internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping=txt_internal_ref";
	
	$order_no=str_replace("'","",$txt_order_id);
	$order_num=str_replace("'","",$txt_order_no);
	if(str_replace("'","",$txt_order_id)!="" && str_replace("'","",$txt_order_id)!=0) $order_id_cond_trans=" and b.id in ($order_no)";
	else if ($order_num=="") $order_no_cond=""; else $order_no_cond=" and  b.po_number in ('$order_num') ";
	
	
	//$cbo_team_name=str_replace("'","",$cbo_team_name);
	//$cbo_team_member=str_replace("'","",$cbo_team_member);
	
	//if($cbo_team_name==0) $team_name_cond=""; else $team_name_cond=" and a.team_leader='$cbo_team_name'";
	//if($cbo_team_member==0) $team_member_cond=""; else $team_member_cond=" and a.dealing_marchant='$cbo_team_member'";
	
	$conv_sql="select a.id as item_id,a.conversion_factor from lib_item_group a,product_details_master b where a.id=b.item_group_id and b.entry_form=20 and b.item_category_id=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
	$sql_conv_result=sql_select($conv_sql);
	foreach($sql_conv_result as $row)
	{
		$conversion_arr[$row[csf("item_id")]]['conver_rate']=$row[csf("conversion_factor")];
	}
	unset($sql_conv_result);
		$body_id="1,10,11,14,15,16,17,20,22,36,69,95,100,110,111,125,128,129,131,132,135,143,149,152,164,167,171,177,190,191,198,199,201,208,219,217,218,225,227,232,233,234,235,236,238,240,246,247,252,253,258,274,276,277,278,283,284,285,301,305,306,308,309,322,325,330,350,364,365,368,371,373,390,392,393,394,417,421,428,429,432,435,437,439,440,445,448,450,451,452,453,454,456,457,462,463";		
	if($report_type==1)
	{ 
		if($template==1)
		{
			$revised_date_arr=array();$exfactory_data_array=array();$pre_cost_data_arr=array();
			$fabric_booking_data_arr=array();$fabric_des_data_arr=array();$pre_cost_cm_margin_arr=array();		
			$export_lc_data_array=array();$sewing_data_array=array();$lab_dip_data_arr=array();
			
			$sql_update_history=sql_select("Select po_id,max(po_received_date) as po_received_date,max(shipment_date) as shipment_date,min(shipment_date) as orig_ship_date from wo_po_update_log group by po_id order by po_id");
			
			foreach($sql_update_history as $row)
			{
				$revised_date_arr[$row[csf('po_id')]]['recv_date']=$row[csf('po_received_date')];
				$revised_date_arr[$row[csf('po_id')]]['lship_date']=$row[csf('shipment_date')];
				$revised_date_arr[$row[csf('po_id')]]['forig_date']=$row[csf('orig_ship_date')];
			}
			if($db_type==2)
			{
				$lab_grp="listagg(CAST(a.lapdip_no as VARCHAR(4000)),',') within group (order by a.lapdip_no) as lab_id";
				$count_grp="listagg(CAST(b.count_id as VARCHAR(4000)),',') within group (order by b.count_id) as count_id";
				$grop_fab="listagg(CAST(b.gsm_weight as VARCHAR(4000)),',') within group (order by b.gsm_weight) as gsm_weight,listagg(CAST(b.lib_yarn_count_deter_id as VARCHAR(4000)),',') within group (order by b.lib_yarn_count_deter_id) as deter_id";
			}
			else
			{
				$lab_grp="group_concat(a.lapdip_no) as lab_id";
				$count_grp="group_concat(b.count_id) as count_id";
				$grop_fab="group_concat(b.gsm_weight) as gsm_weight,group_concat(b.lib_yarn_count_deter_id) as deter_id";
			}
			//1,10,11,14,15,16,17,20,69,95,100,125,128,129,131,132,135,143,149,152,164,167,171,191,198,201,208,219
			
			//print_r($pre_cost_data_arr);
			
			
			
			$sql_yarn=sql_select("Select a.id,a.construction,a.gsm_weight,b.count_id,b.copmposition_id from lib_yarn_count_determina_mst a,lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active =1 and a.is_deleted=0 group by a.id,a.construction,a.gsm_weight,b.count_id ");
			
			foreach($sql_yarn as $row)
			{
				//$fab_comosition=$fab_composition_arr[$row[csf("id")]]['desc'];
				$fabric_des_data_arr[$row[csf('id')]]['des']=$row[csf('construction')];
				$fabric_des_data_arr[$row[csf('id')]]['composition']=$composition[$row[csf('copmposition_id')]].'100%';
				$fabric_des_data_arr[$row[csf('id')]]['gsm']=$row[csf('gsm_weight')];
				$fabric_des_data_arr[$row[csf('id')]]['count']=$row[csf('count_id')];
			}
			unset($sql_yarn);
			
			$sql_lab=sql_select("Select a.po_break_down_id as po_id,$lab_grp from wo_po_lapdip_approval_info a where a.status_active =1 and a.is_deleted=0 group by a.po_break_down_id");
			
			foreach($sql_lab as $row)
			{
				$lab_dip_data_arr[$row[csf('po_id')]]['ldip_id']=$row[csf('lab_id')];
				
			}
			$sql_sewing=sql_select("Select a.serving_company,a.production_source,a.po_break_down_id as po_id from pro_garments_production_mst a where a.company_id='$company_name' and a.production_type=4 and a.status_active =1 and a.is_deleted=0");
			
			foreach($sql_sewing as $row)
			{
				if($row[csf('production_source')]==1) $sewing_com=$company_library[$row[csf('serving_company')]]; else if($row[csf('production_source')]==3) $sewing_com=$supplier_library[$row[csf('serving_company')]];
				//$sewing_data_array[$row[csf('po_id')]]['des']=$row[csf('production_source')];
				$sewing_data_array[$row[csf('po_id')]]['sewing_com']=$sewing_com;
			}
			unset($sql_sewing);
			/*$exfactory_data=sql_select("select po_break_down_id as po_id,MAX(ex_factory_date) as ex_fact_date,
			sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as ex_fact_qty,
			sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as ex_fact_ret_qty,
			sum(CASE WHEN entry_form!=85 THEN total_carton_qnty ELSE 0 END) as tot_carton_qty,
			sum(CASE WHEN entry_form=85 THEN total_carton_qnty ELSE 0 END) as tot_ret_carton_qty
			 from pro_ex_factory_mst  where 1=1 and status_active=1 and is_deleted=0 group by po_break_down_id");
			
			foreach($exfactory_data as $row)
			{
				$exfactory_data_array[$row[csf('po_id')]][ex_fact_qty]=$row[csf('ex_fact_qty')]-$row[csf('ex_fact_ret_qty')];
				$exfactory_data_array[$row[csf('po_id')]][carton_qty]=$row[csf('tot_carton_qty')]-$row[csf('tot_ret_carton_qty')];
				$exfactory_data_array[$row[csf('po_id')]][ex_factory_date]=$row[csf('ex_fact_date')];
			}*/
			
			//current_invoice_value
			/*$sql_inv=sql_select("select b.po_breakdown_id as po_id,max(a.invoice_date) as invoice_date,
			sum(CASE WHEN a.shipping_mode=1 THEN b.current_invoice_qnty ELSE 0 END) as sea_qnty,
			sum(CASE WHEN a.shipping_mode=2 THEN b.current_invoice_qnty ELSE 0 END) as air_qnty,
			sum(CASE WHEN a.shipping_mode=1 THEN b.current_invoice_value ELSE 0 END) as sea_val,
			sum(CASE WHEN a.shipping_mode=2 THEN b.current_invoice_value ELSE 0 END) as air_val
			 from  com_export_invoice_ship_mst a,com_export_invoice_ship_dtls b where a.id=b.mst_id  and a.shipping_mode in(1,2)  and a.status_active ='1' and a.is_deleted ='0' group by b.po_breakdown_id");
			
			$export_invoice_arr=array();
			foreach($sql_inv as $row)
			{
				
				$export_invoice_arr[$row[csf('po_id')]][1]['sea']+=$row[csf('sea_qnty')];
				$export_invoice_arr[$row[csf('po_id')]][2]['air']+=$row[csf('air_qnty')];
				$export_invoice_arr[$row[csf('po_id')]][1]['seaval']+=$row[csf('sea_val')];
				$export_invoice_arr[$row[csf('po_id')]][2]['airval']+=$row[csf('air_val')];
				$export_invoice_arr[$row[csf('po_id')]]['date']=$row[csf('invoice_date')];
			}*/
			
			
		  		$sql_result="select a.company_name as company_id,a.job_no_prefix_num as job_prefix,a.team_leader,a.qlty_label,a.dealing_marchant,a.product_dept,a.season_buyer_wise as season_matrix, b.insert_date, a.job_no, a.buyer_name,a.client_id, a.style_ref_no,a.style_owner,b.is_confirmed,b.shiping_status,b.matrix_type, a.set_smv, a.avg_unit_price, 
				a.order_uom,a.gmts_item_id, a.total_set_qnty as ratio,a.job_quantity,a.total_price,b.id as po_id, b.po_number, b.pub_shipment_date, b.po_received_date,b.shipment_date,b.up_charge as up_charge,
				b.po_quantity as po_quantity, b.po_total_price as po_total_price, b.unit_price, b.grouping, b.file_no
				from wo_po_details_master a, wo_po_break_down b 
				 where a.job_no=b.job_no_mst and a.company_name='$company_name' and a.status_active=1 and
				a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_quantity>0 $date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond 
				$order_status_cond  $internal_ref_cond $file_no_cond $season_cond $style_owner_cond $team_leader_cond $dealing_marchant_cond ";
				
				$result_data=sql_select($sql_result);
				$tot_rows_po=count($result_data); 
 
				$all_po_id="";
				foreach($result_data as $row)
				{
					if($all_po_id=="") $all_po_id=$row[csf("po_id")]; else $all_po_id.=",".$row[csf("po_id")];
					
					$po_arr[$row[csf("po_id")]]['shipment_date']=$row[csf("shipment_date")]; 
				}
					$all_po=array_unique(explode(",",$all_po_id));
					$po_arr_cond=array_chunk($all_po,1000, true);
					$po_cond_for_in="";$po_cond_for_in2="";$po_cond_for_in3="";$po_cond_for_in4="";$po_cond_for_in5="";$po_cond_for_in6="";$po_cond_for_in7="";
					/*$pi=0;
					foreach($po_arr_cond as $key=>$value)
					{
					   if($pi==0)
					   {
						$po_cond_for_in=" and c.po_break_down_id  in(".implode(",",$value).")"; 
						$po_cond_for_in2=" and d.po_break_down_id  in(".implode(",",$value).")"; 
						$po_cond_for_in3=" and b.id  in(".implode(",",$value).")"; 
						$po_cond_for_in4=" and b.po_breakdown_id  in(".implode(",",$value).")"; 
						$po_cond_for_in5=" and po_break_down_id  in(".implode(",",$value).")"; 
					   }
					   else //po_break_down_id
					   {
						$po_cond_for_in.=" or c.po_break_down_id  in(".implode(",",$value).")";
						$po_cond_for_in2.=" or d.po_break_down_id  in(".implode(",",$value).")";
						$po_cond_for_in3=" or b.id  in(".implode(",",$value).")"; 
						$po_cond_for_in4=" or b.po_breakdown_id  in(".implode(",",$value).")"; 
						$po_cond_for_in5=" or po_break_down_id  in(".implode(",",$value).")"; 
					   }
					   $pi++;
					}*/	
					
					$poIds=chop($all_po_id,','); //$po_cond_for_in=""; $order_cond1=""; $order_cond2=""; $precost_po_cond="";
					$po_ids=count(array_unique(explode(",",$all_po_id)));
						if($db_type==2 && $po_ids>1000)
						{
							$po_cond_for_in=" and (";
							$po_cond_for_in2=" and (";
							$po_cond_for_in3=" and (";
							$po_cond_for_in4=" and (";
							$po_cond_for_in5=" and (";
							$po_cond_for_in6=" and (";
							$po_cond_for_in7=" and (";
							$poIdsArr=array_chunk(explode(",",$poIds),999);
							foreach($poIdsArr as $ids)
							{
								$ids=implode(",",$ids);
								//$poIds_cond.=" po_break_down_id in($ids) or ";
								$po_cond_for_in.=" c.po_break_down_id in($ids) or"; 
								$po_cond_for_in2.=" d.po_break_down_id in($ids) or"; 
								$po_cond_for_in3.=" b.id in($ids) or"; 
								$po_cond_for_in4.=" b.po_breakdown_id  in($ids) or"; 
								$po_cond_for_in5.=" po_break_down_id in($ids) or";
								$po_cond_for_in6.=" b.wo_po_break_down_id in($ids) or";
								$po_cond_for_in7.=" b.po_break_down_id in($ids) or"; 
								
							}
							$po_cond_for_in=chop($po_cond_for_in,'or ');
							$po_cond_for_in.=")";
							$po_cond_for_in2=chop($po_cond_for_in2,'or ');
							$po_cond_for_in2.=")";
							$po_cond_for_in3=chop($po_cond_for_in3,'or ');
							$po_cond_for_in3.=")";
							$po_cond_for_in4=chop($po_cond_for_in4,'or ');
							$po_cond_for_in4.=")";
							$po_cond_for_in5=chop($po_cond_for_in5,'or ');
							$po_cond_for_in5.=")";
							$po_cond_for_in6=chop($po_cond_for_in6,'or ');
							$po_cond_for_in6.=")";
							$po_cond_for_in7=chop($po_cond_for_in7,'or ');
							$po_cond_for_in7.=")";
							
						}
						else
						{
							$po_cond_for_in=" and c.po_break_down_id in($poIds)";
							$po_cond_for_in2=" and d.po_break_down_id  in($poIds)";
							//$po_cond_for_in3=" and d.po_breakdown_id in($poIds)";
							$po_cond_for_in3=" and b.id in($poIds)";
							$po_cond_for_in4=" and b.po_breakdown_id  in($poIds)";
							$po_cond_for_in5=" and po_break_down_id in($poIds)";
							$po_cond_for_in6=" and b.wo_po_break_down_id in($poIds)";
							$po_cond_for_in7=" and b.po_break_down_id in($poIds)";
							
						}
						
						$sql_lc=sql_select("select a.export_lc_no,b.wo_po_break_down_id as po_id from  com_export_lc a,com_export_lc_order_info b where a.id=b.com_export_lc_id and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 $po_cond_for_in6");
			
						foreach($sql_lc as $row)
						{
							$export_lc_data_array[$row[csf('po_id')]][lc_no].=$row[csf('export_lc_no')].',';
						}
						unset($sql_lc);
						$sql_sc=sql_select("select a.contract_no,b.wo_po_break_down_id as po_id from  com_sales_contract a,com_sales_contract_order_info b where a.id=b.com_sales_contract_id and b.status_active =1 and b.is_deleted=0 $po_cond_for_in6");
						$export_sc_data_array=array();
						foreach($sql_sc as $row)
						{
							$export_sc_data_array[$row[csf('po_id')]][sc_no].=$row[csf('contract_no')].',';
						}
						unset($sql_sc);
			
						$sql_book=sql_select("SELECT  e.uom, b.entry_form,b.booking_type,b.item_category,b.is_approved,b.fabric_source,b.is_short,b.supplier_id,c.job_no,b.pay_mode,b.booking_no,c.fin_fab_qnty,c.grey_fab_qnty,c.po_break_down_id as po_id from wo_po_details_master a,wo_booking_mst b,wo_booking_dtls c, wo_pre_cost_fabric_cost_dtls e where  a.job_no=c.job_no and c.booking_no=b.booking_no  and  e.id=c.pre_cost_fabric_cost_dtls_id and  a.job_no=e.job_no  and b.booking_type=1 and b.is_short in(1,2) and a.company_name='$company_name' and c.status_active=1 and c.is_deleted=0  $buyer_id_cond $job_no_cond $po_cond_for_in");
			//and c.status_active=1 and c.is_deleted=0
						foreach($sql_book as $row)
						{
							
							$fabric_booking_data_arr[$row[csf('po_id')]]['booking_no'].=$row[csf('booking_no')].',';
							
							$fabric_booking_data_arr_uom_wise[$row[csf('po_id')]][$row[csf('uom')]]['fin_qnty']+=$row[csf('grey_fab_qnty')];

							$fabric_booking_data_arr[$row[csf('po_id')]]['pay_mode'].=$row[csf('pay_mode')].',';
							$fabric_booking_data_arr[$row[csf('po_id')]]['fin_qnty']+=$row[csf('grey_fab_qnty')];
							
							if($row[csf('pay_mode')]==5 || $row[csf('pay_mode')]==3)
							{
								$supplier_comp=$company_library[$row[csf('supplier_id')]];
							}
							else
							{
								$supplier_comp=$supplier_library[$row[csf('supplier_id')]];
							}
							//echo $supplier_comp.'<br>';
							$fabric_booking_data_arr[$row[csf('po_id')]]['supplier_id'].=$supplier_comp.',';
							
							$fabric_booking_data2_arr[$row[csf('booking_no')]]['item_category']=$row[csf('item_category')];
							$fabric_booking_data2_arr[$row[csf('booking_no')]]['fabric_source']=$row[csf('fabric_source')];
							$fabric_booking_data2_arr[$row[csf('booking_no')]]['is_short']=$row[csf('is_short')];
							$fabric_booking_data2_arr[$row[csf('booking_no')]]['entry_form']=$row[csf('entry_form')];
							$fabric_booking_data2_arr[$row[csf('booking_no')]]['booking_type']=$row[csf('booking_type')];
							$fabric_booking_data2_arr[$row[csf('booking_no')]]['is_approved']=$row[csf('is_approved')];
						}
						unset($sql_book);
						$sql_invo=sql_select("select b.po_breakdown_id as po_id,a.country_id,max(a.invoice_date) as invoice_date,
						sum(CASE WHEN a.shipping_mode=1 THEN b.current_invoice_qnty ELSE 0 END) as sea_qnty,
						sum(CASE WHEN a.shipping_mode=2 THEN b.current_invoice_qnty ELSE 0 END) as air_qnty,
						sum(CASE WHEN a.shipping_mode=1 THEN b.current_invoice_value ELSE 0 END) as sea_val,
						sum(CASE WHEN a.shipping_mode=2 THEN b.current_invoice_value ELSE 0 END) as air_val
						 from  com_export_invoice_ship_mst a,com_export_invoice_ship_dtls b where a.id=b.mst_id  and a.shipping_mode in(1,2)  and a.status_active ='1' and a.is_deleted ='0' $po_cond_for_in4  group by b.po_breakdown_id,a.country_id");
						 	foreach($sql_invo as $row)
							{
								$invoice_data_array[$row[csf('po_id')]][1]['sea_qnty']+=$row[csf('sea_qnty')];
								$invoice_data_array[$row[csf('po_id')]][2]['air_qnty']+=$row[csf('air_qnty')];
							}
					
			
						$sql_inv_exf="select b.po_break_down_id as po_id,max(b.ex_factory_date) as factory_date,
						sum(CASE WHEN b.shiping_mode=1 and  b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as sea_qnty,
						sum(CASE WHEN b.shiping_mode=1 and  b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as ret_sea_qnty,
						sum(CASE WHEN b.shiping_mode=2 and  b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as air_qnty,
						sum(CASE WHEN b.shiping_mode=2 and  b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as ret_air_qnty,
						sum(CASE WHEN b.entry_form!=85 THEN b.total_carton_qnty ELSE 0 END) as tot_carton_qty,
						sum(CASE WHEN b.entry_form=85 THEN b.total_carton_qnty ELSE 0 END) as tot_ret_carton_qty
						from  pro_ex_factory_mst b where  b.shiping_mode in(1,2)  and b.status_active=1 and b.is_deleted =0 $po_cond_for_in7 group by b.po_break_down_id";
						$sql_exf_res=sql_select($sql_inv_exf);
						$export_invoice_arr=array();$exfactory_data_array=array();
						foreach($sql_exf_res as $row)
						{
							$export_invoice_arr[$row[csf('po_id')]][1]['sea']+=$row[csf('sea_qnty')]-$row[csf('ret_sea_qnty')];
							$export_invoice_arr[$row[csf('po_id')]][2]['air']+=$row[csf('air_qnty')]-$row[csf('ret_air_qnty')];
							$exfactory_data_array[$row[csf('po_id')]]['carton_qty']+=$row[csf('tot_carton_qty')]-$row[csf('tot_ret_carton_qty')];
							$export_invoice_arr[$row[csf('po_id')]]['date']=$row[csf('factory_date')];
						}
						unset($sql_exf_res);
			//and b.body_part_id in(1,10,11,14,15,16,17,20,69,95,100,125,128,129,131,132,135,143,149,152,164,167,171,191,198,201,208,219,217,227,232,233,234,235,236,238,246,252,253,258,274,276,283,284,301,322,330,350,364,371,373,392,393,394) 
			
						$sql_pre=sql_select("Select c.po_break_down_id as po_id,b.job_no,$grop_fab,d.margin_dzn as margin_dzn,b.composition,b.construction as descrp, d.cm_cost from wo_po_details_master a,wo_pre_cost_fabric_cost_dtls b,wo_pre_cos_fab_co_avg_con_dtls c,wo_pre_cost_dtls d where a.job_no=b.job_no and c.pre_cost_fabric_cost_dtls_id=b.id and c.job_no=a.job_no and d.job_no=a.job_no  and d.job_no=b.job_no  and  c.cons>0 and a.company_name='$company_name' and b.body_part_id in($body_id) $po_cond_for_in $buyer_id_cond $job_no_cond group by c.po_break_down_id,b.job_no,d.margin_dzn, d.cm_cost,b.composition,b.construction");
						//and b.body_part_type in(1,20)
				
						foreach($sql_pre as $row)
						{
							$pre_cost_data_arr[$row[csf('po_id')]]['deter_id']=$row[csf('deter_id')];
							$pre_cost_data_arr[$row[csf('po_id')]]['gsm']=$row[csf('gsm_weight')];
							$pre_cost_data_arr[$row[csf('po_id')]]['composition'].=$row[csf('composition')].',';
							$pre_cost_data_arr[$row[csf('po_id')]]['fab_des'].=$row[csf('descrp')].',';//	$pre_cost_data_arr[$row[csf('po_id')]]['fab_des'].=$row[csf('descrp')].',';
							$pre_cost_cm_margin_arr[$row[csf('job_no')]]['margin']=$row[csf('margin_dzn')];
							$pre_cost_cm_margin_arr[$row[csf('job_no')]]['cm']=$row[csf('cm_cost')];
						}
						unset($sql_pre);
				
				$sql_fab_book= "select b.id as po_id,(d.amount) as amount,c.item_category,c.booking_type,c.entry_form,d.grey_fab_qnty,d.fin_fab_qnty,d.wo_qnty,d.exchange_rate as exchange_rate
						 from wo_po_details_master a, wo_po_break_down b, wo_booking_mst c,wo_booking_dtls d  where a.job_no=b.job_no_mst and a.company_name='$company_name'  and b.id=d.po_break_down_id and d.booking_no=c.booking_no  and  c.status_active=1 and c.short_booking_type not in(2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.amount>0  $date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond 
				$order_status_cond  $internal_ref_cond $file_no_cond $season_cond $style_owner_cond $team_leader_cond $dealing_marchant_cond $po_cond_for_in3";
				$book_result=sql_select($sql_fab_book);
				$po_wo_cost_arr=array();
				foreach($book_result as $row)
				{
					$booking_type=$row[csf('booking_type')];
					$item_category=$row[csf('item_category')];
					if($booking_type==1 || $booking_type==4)
					{
						if($row[csf('entry_form')]!=108) //Fabric Booking
						{
							  $fab_qnty=$row[csf('grey_fab_qnty')];
							 if($fab_qnty==0 || $fab_qnty=='')
							 {
								$avg_rate=0; $amount=0; $fab_qnty=0;
							 }
							 
							$avg_rate=number_format($row[csf('amount')]/$fab_qnty,6,'.','');
							//echo $avg_rate.'='.$fab_qnty.'<br>';
							if($avg_rate=='inf') $avg_rate=0;else $avg_rate=$avg_rate;
							
							$amount=$fab_qnty*$avg_rate;
						}
						else
						{
							$fab_qnty=$row[csf('fin_fab_qnty')];
							 if($fab_qnty==0 || $fab_qnty=='')
							 {
								$avg_rate=0; $amount=0; $fab_qnty=0;
							 }
							$avg_rate=number_format($row[csf('amount')]/$fab_qnty,6,'.','');
							if($avg_rate=='inf') $avg_rate=0;else $avg_rate=$avg_rate;
							$amount=$fab_qnty*$avg_rate;
						}
					}
					else if(($booking_type==2 || $booking_type==5) && $item_category==4) //Trims Booking
					{
						 if($row[csf('wo_qnty')]==0 || $row[csf('wo_qnty')]=='')
							 {
								$avg_rate=0; $amount=0; $fab_qnty=0;
							 }
						$avg_rate=number_format($row[csf('amount')]/$row[csf('wo_qnty')],6,'.','');
						$rate=($avg_rate/$row[csf('exchange_rate')]);
						$amount=number_format($row[csf('wo_qnty')]*$rate,6,'.','');// number_format($avg_rate,'.','');
						
					}
					else if($booking_type==6 && $item_category==25) //Emblish
					{
						$avg_rate=number_format($row[csf('amount')]/$row[csf('wo_qnty')],6,'.','');
						//$rate=($avg_rate/$row[csf('exchange_rate')]);
						$amount=number_format($row[csf('wo_qnty')]*$avg_rate,6,'.','');// number_format($avg_rate,'.','');
					
						// $embl_tot_amt+=$amount;
					}
					else
					{
						$amount=$row[csf('amount')];
					}
					//echo $amount.'<br>';
					$po_wo_cost_arr[$row[csf('po_id')]]['amount']+=$amount;
				}
					//echo 'Fab='.$fab_tot_amt.'trim='.$trim_tot_amt.'Embl='.$embl_tot_amt;
				unset($book_result);
				//print_r($po_wo_cost_arr);
				$sql_lab_book= "select b.id as po_id,sum(e.wo_value) as amount
						 from wo_po_details_master a, wo_po_break_down b, wo_labtest_mst c,wo_labtest_dtls d,wo_labtest_order_dtls e  where a.job_no=b.job_no_mst and a.company_name='$company_name'  and a.job_no=d.job_no and c.id=d.mst_id and e.dtls_id=d.id and e.order_id=b.id and c.status_active=1 and  c.is_deleted=0 $date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond 
				$order_status_cond  $internal_ref_cond $file_no_cond $season_cond $style_owner_cond $team_leader_cond $dealing_marchant_cond $po_cond_for_in3 group by b.id";
				$lab_result=sql_select($sql_lab_book);
				$lab_po_data_cost_arr=array();
				foreach($lab_result as $row)
				{
					/*$po_num=array_unique(explode(",",$rows[csf('po_id')]));
					foreach($po_num as $po)
					{
						$lab_po_data_arr[$po]['lab']+=$rows[csf('amount')];
					}*/
					$lab_po_data_cost_arr[$row[csf('po_id')]]['lab']+=$row[csf('amount')];
				}
				unset($lab_result);
				//print_r($lab_po_data_arr);
				
							
				$sql_mrr_recv= "select a.recv_number,b.id as trans_id,f.issue_qnty,f.issue_trans_id,b.order_rate,b.order_ile,e.product_name_details,e.item_group_id
						 from  inv_receive_master a, inv_transaction b,product_details_master e,inv_mrr_wise_issue_details f  where a.id=b.mst_id and e.id=b.prod_id  and f.recv_trans_id=b.id and b.transaction_type=1 and a.entry_form=20 and a.company_id=$company_name  and b.status_active=1 and  b.is_deleted=0  ";
				$mrr_recv_result=sql_select($sql_mrr_recv);
				$mrr_recv_arr=array();
				foreach($mrr_recv_result as $row)
				{
					$mrr_recv_arr[$row[csf('trans_id')]]['rate']=$row[csf('order_rate')]+$row[csf('order_ile')];
				}
				
				$sql_gen_acc= "select b.id as po_id,f.recv_trans_id,e.item_group_id,
				sum(d.cons_amount) as amount,sum(cons_quantity) as cons_quantity,sum(f.issue_qnty) as issue_qnty
				 from wo_po_details_master a, wo_po_break_down b, inv_issue_master c,inv_transaction d,product_details_master e,inv_mrr_wise_issue_details f  where a.company_name='$company_name' and  f.issue_trans_id=d.id and e.id=f.prod_id  and a.job_no=b.job_no_mst  and b.id=d.order_id and c.id=d.mst_id and d.transaction_type in(2) and e.id=d.prod_id and  d.status_active=1 and  d.item_category=4 and c.entry_form=21  and f.entry_form=21 and c.is_deleted=0 and d.cons_quantity>0 $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond 
				$order_status_cond  $internal_ref_cond $file_no_cond $season_cond $style_owner_cond $team_leader_cond $dealing_marchant_cond $po_cond_for_in3 group by b.id,f.recv_trans_id,e.item_group_id ";
				$gen_dzn_result=sql_select($sql_gen_acc);$gen_acces_data_cost_arr=array();
				$trans_rec_ids='';
				foreach($gen_dzn_result as $row)
				{
					$converrate=$conversion_arr[$row[csf("item_group_id")]]['conver_rate'];
					 $orderrate=($mrr_recv_arr[$row[csf('recv_trans_id')]]['rate']/$converrate);
					 $gen_amount=$row[csf('issue_qnty')]*$orderrate;
					//$sum_total_accesso_amount+=number_format($gen_amount,6,'.','');
					$gen_acces_data_cost_arr[$row[csf('po_id')]]['amt']+=$gen_amount;
					
					//if($trans_rec_ids=='') $trans_rec_ids=$row[csf('recv_trans_id')];else $trans_rec_ids.=",".$row[csf('recv_trans_id')];
				}
				//print_r($gen_acces_data_cost_arr2);
				//echo $trans_rec_ids;
				/*$all_transID=array_unique(explode(",",$trans_rec_ids));
					
					$trans_arr_cond=array_chunk($all_transID,1000, true);
					$trans_arr_cond_in="";
					$t=0;
					foreach($trans_arr_cond as $key=>$value)
					{
					   if($t==0)
					   {
						$trans_arr_cond_in=" and f.recv_trans_id  in(".implode(",",$value).")"; 
						
					   }
					   else 
					   {
						$trans_arr_cond_in.=" or f.recv_trans_id  in(".implode(",",$value).")";
						
					   }
					   $t++;
					}	//*/
				$print_report_format_ids_partial = return_field_value("format_id","lib_report_template","template_name=".$company_name." and module_id=2 and report_id=35 and is_deleted=0 and status_active=1");
				$format_ids_partial=explode(",",$print_report_format_ids_partial);
				
				$print_report_format_ids_short = return_field_value("format_id","lib_report_template","template_name=".$company_name." and module_id=2 and report_id=2 and is_deleted=0 and status_active=1");
				$print_report_ids_short=explode(",",$print_report_format_ids_short);
				$print_report_format_ids2 = return_field_value("format_id","lib_report_template","template_name=".$company_name." and module_id=2 and report_id=1 and is_deleted=0 and status_active=1");
				$format_ids=explode(",",$print_report_format_ids2);
				/*$print_sam_report_format=return_field_value("format_id","lib_report_template","template_name =".$company_name." and module_id=2 and report_id=3 and is_deleted=0 and status_active=1");
				$format_sam_ids=explode(",",$print_sam_report_format);*/
				
				//echo $print_report_format_ids_partial.'='.$print_report_format_ids_short.'='.$print_report_format_ids2.'='.$print_sam_report_format;
				
				
			ob_start(); 
		?>
			<div style="width:4930px">
			<fieldset style="width:100%;">	
				<table width="4930">
					<tr class="form_caption">
						<td colspan="54" align="center">OMS Report(PO Wise)</td>
					</tr>
					<tr class="form_caption">
						<td colspan="54" align="center"><? echo $company_library[$company_name]; ?></td>
					</tr>
				</table>
				<table id="table_header_1" class="rpt_table" width="4930" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<th width="30">SL</th>
						<th width="40">Ship Year</th>
						<th width="90"><div style="word-wrap:break-word:90px;">Ord. Month</div></th>
						<th width="80"><div style="word-wrap:break-word:80px;">TeamLeader</div></th>
						<th width="80"><div style="word-wrap:break-word:80px;">Deal Merchant.</div></th>
                        <th width="80"><div style="word-wrap:break-word:80px;">Style Owner</div></th>
						<th width="100">Prod. Dept.</th>
                        <th width="50">Job No</th>
						<th width="130"><div style="word-wrap:break-word:130px;">Style Ref.</div></th>
						<th width="100"><div style="word-wrap:break-word:100px;">Order No</div></th>
						<th width="80">Order Rcv. Date</th>
						
						<th width="100">Buyer Name</th>
						<th width="100">Buyer Client</th>
						
						<th width="80">Season</th>
						<th width="130">GMT Item</th>
						
						
						<th width="150">Fabric Const.</th>
                        <th width="200">Fabric Composition</th>
						<th width="150">Fab.Booking No</th>
                        <th width="120">Supplier</th>
						<th width="60">GSM</th>
						<th width="70">Order Qty</th>
						<th width="50">Order UOM</th>
						<th width="80">Order Qty / Pcs</th>
						<th width="40">Set Ratio</th>
                       
						<th width="80">Short / Excess Qty.</th>
                        <th width="80">Air Qty</th>
                        <th width="80">Sea Qty</th>
                        <th width="90" title="Air+Sea Qty">Total Qty</th>
						
						<th width="80">Ori. ShipDate</th>
                        <th width="80">Invoice Qty</th>
						<th width="80">Last Revised ShipDate</th>
						<th width="80">Last Ex-Fact Date</th>
						<th width="60">Unit Price (FOB)</th>
						<th width="110">SC/LC No</th>
                        <th width="70">Commission / DZN</th>
                        <th width="100">Raw Material Cost/Dzn</th>
                        <th width="70">Overhead Cost/Dzn</th>
						<th width="70">CM US/DZN</th>
                        <th width="70">Profit Except Overhead</th>
						
						 <th width="60">EARLY / Delay Days</th>
						<th width="60">Early / Delay Status</th>
						<th width="100">Total FOB Price</th>
						<th width="100">Ship Value</th>
						<th width="100">Short / Excess Val.</th>
                        <th width="80">Raw material Cost</th>
						<th width="90">Total CM</th>
                        <th width="90">Total Profit Except Overhead</th>
						<th width="70">Qlty. Level</th>
						<th width="70">No Of Ctn. </th>
						<th width="80">UpCharge</th>
						<th width="100">LabDip No.</th>
						
						<th width="80">Sew. Company</th>
						<th width="80">Ship Month</th>
						<th width="80">Booking Qty(Kg)</th>
						<th width="100">Booking Qty(Yds)</th>
						<th width="100">Booking Qty(Mtr)</th>
						<th width="">SMV</th>
					   
						
					</thead>
				</table>
				<div style="width:4950px; max-height:400px; overflow-y:scroll" id="scroll_body">
					<table class="rpt_table" width="4930" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
					<?
					
					$i=1; $total_po_qty=0; $total_ex_fact_qty=0;$total_tot_fob_price=0;$total_tot_ex_fact_val=0;		
					$total_cm_margin_val=0;	$total_qlty_qty=0;	$total_up_charge=0;	 $total_fab_finish_req=0; $total_raw_materials_cost=0; 
				  //  $tot_rows=count($nameArray);
					 $condition= new condition();
					 $condition->company_name("=$cbo_company_name");
					 if(str_replace("'","",$cbo_buyer_name)>0){
						  $condition->buyer_name("=$cbo_buyer_name");
					 }
					 if(str_replace("'","",$txt_job_no) !=''){
						  $condition->job_no_prefix_num("=$txt_job_no");
					 }
					 if(str_replace("'","",$cbo_order_status) >0){
						  $condition->is_confirmed("=$cbo_order_status");
					 }
					 if(str_replace("'","",$cbo_order_status)==0){
						  $condition->is_confirmed("in(1,2)");
					 }
					if(str_replace("'","",$cbo_search_date) ==1 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!=''){
						$condition->pub_shipment_date(" between '$start_date' and '$end_date'");
					 }
					  if(str_replace("'","",$cbo_search_date) ==2 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
					 {
						 $condition->po_received_date(" between '$start_date' and '$end_date'");
						
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
					/* if(str_replace("'","",$txt_season)!='')
					 {
						$condition->season("=$txt_season"); 
					 };*/
					$condition->init();
					//$yarn= new yarn($condition);
					
					//$fabric= new fabric($condition);
					//$fabric_costing_arr=$fabric->getQtyArray_by_order_knitAndwoven_greyAndfinish();
					//print_r($fabric_costing_arr);
					$other= new other($condition);
					//echo $other->getQuery(); die;
					$other_costing_arr=$other->getAmountArray_by_order();
					$commission= new commision($condition);
					$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
				
					$total_tot_air=0;$total_tot_sea=0;$total_profit_except_val=0;$total_invoice_qty=0;$total_raw_materials_cost=0;
					foreach($result_data as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						//$fabric_des_data_arr[$row[csf('id')]]['composition']
						$fab_deter_id=array_unique(explode(",",$pre_cost_data_arr[$row[csf('po_id')]]['deter_id']));
						$fab_Desc=$pre_cost_data_arr[$row[csf('po_id')]]['fab_des'];
						
						$fabDesc=rtrim($fab_Desc,',');
						//$composition=implode(",",array_unique(explode(",",$composition)));
						
						$composition=rtrim($pre_cost_data_arr[$row[csf('po_id')]]['composition'],',');
						$composition=implode(",",array_unique(explode(",",$composition)));
						
						$fab_des=implode(",",array_unique(explode(",",$fabDesc)));
						$fab_gsm=implode(",",array_unique(explode(",",$pre_cost_data_arr[$row[csf('po_id')]]['gsm']))); 
						$yarn_counts="";
						 foreach($fab_deter_id as $yid)
						 {
							if($yarn_counts=="") $yarn_counts=$yarn_count_library[$fabric_des_data_arr[$yid]['count']];else $yarn_counts.=",".$yarn_count_library[$fabric_des_data_arr[$yid]['count']];
							 //$fab_count_id=$fabric_des_data_arr[$fab_deter_id]['count']; 
						 }
						
						 $ship_qty=($export_invoice_arr[$row[csf('po_id')]][2]['air']+$export_invoice_arr[$row[csf('po_id')]][1]['sea'])/$row[csf('ratio')];//$exfactory_data_array[$row[csf('po_id')]][ex_fact_qty]/$row[csf('ratio')];
						$seaval=$export_invoice_arr[$row[csf('po_id')]][1]['seaval'];
						$airval=$export_invoice_arr[$row[csf('po_id')]][2]['airval'];
						 $po_cm_cost=$other_costing_arr[$row[csf('po_id')]]['cm_cost'];
						// echo $po_cm_cost;
						 $invoice_date=$export_invoice_arr[$row[csf('po_id')]]['date'];
						 $short_excess_qty=$ship_qty-$row[csf('po_quantity')];
						 
						 $invo_sea_qnty=$invoice_data_array[$row[csf('po_id')]][1]['sea_qnty'];
						 $invo_air_qnty=$invoice_data_array[$row[csf('po_id')]][2]['air_qnty'];
						 $tot_invoice_qty=($invo_sea_qnty+$invo_air_qnty)/$row[csf('ratio')];
						
						 $ex_fact_carton_qty=$exfactory_data_array[$row[csf('po_id')]]['carton_qty'];
						 $lab_dip_no=explode(",",$lab_dip_data_arr[$row[csf('po_id')]]['ldip_id']);
						 $lab_dip='';
						 foreach($lab_dip_no as $lap_id )
						 {
							if($lab_dip=='') $lab_dip=$lap_id;else $lab_dip.=", ".$lap_id;
						 }
						
								
						$all_wo_booking_cost=$po_wo_cost_arr[$row[csf('po_id')]]['amount'];
						$lab_po_cost=$lab_po_data_cost_arr[$row[csf('po_id')]]['lab'];
						
						$conver_rate=$conversion_arr[$row[csf("item_group_id")]]['conver_rate'];
						if($conver_rate=='') $conver_rate=0;else $conver_rate=$conver_rate;
						$mrr_no=$mrr_recv_arr[$row[csf('recv_trans_id')]]['mrr_no'];
						$order_rate=($mrr_recv_arr[$row[csf('recv_trans_id')]]['rate']/$conver_rate);
						
						$gen_accss_cost=$gen_acces_data_cost_arr[$row[csf('po_id')]]['amt'];
						/*$fab_production_knit=$fabric_costing_arr['knit']['finish'][$row[csf('po_id')]];
						$fab_production_woven=$fabric_costing_arr['woven']['finish'][$row[csf('po_id')]];*/
							$fab_finish_req=$fabric_booking_data_arr[$row[csf('po_id')]]['fin_qnty'];//$fab_production_knit+$fab_production_woven;
							$date_diff=datediff( "d", $invoice_date , $row[csf('pub_shipment_date')]);	
							//$matrix_type=$row[csf('matrix_type')];
							//if($matrix_type==1) $avg_unit=$row[csf("unit_price")];else $avg_unit=$row[csf("avg_unit_price")];
							$po_total_price=$row[csf('po_total_price')];//a.job_quantity,a.total_price
							$po_quantity=$row[csf('po_quantity')];
							$job_total_price=$row[csf('total_price')];
							$job_quantity=$row[csf('job_quantity')];
							$avg_unit=$po_total_price/$po_quantity;
							$ship_val=$ship_qty*$avg_unit;//$seaval+$airval;
							$tot_fob_val=$po_quantity*$avg_unit;
							
							$foreign_comm=$commission_costing_arr[$row[csf('po_id')]][1];
							$local_comm=$commission_costing_arr[$row[csf('po_id')]][2];
							$tot_commisssion=($foreign_comm+$local_comm);
							
							$tot_commisssion_dzn=($tot_commisssion/$po_quantity)*12;
							//echo $tot_commisssion.'='.$po_quantity.',';
							$cm_us_dzn=($job_total_price/$job_quantity)*12;
							$tot_fob_val_dzn=$tot_fob_val/12;
							$cm_cost=0;
							$cm_cost=$pre_cost_cm_margin_arr[$row[csf('job_no')]]['cm'];
							//echo $cm_cost;
							//echo $all_wo_booking_cost.'='.$lab_po_cost.'='.$gen_accss_cost;
							$tot_raw_materials_cost=$all_wo_booking_cost+$lab_po_cost+$gen_accss_cost;
							$tot_raw_material=($tot_raw_materials_cost/$row[csf('po_quantity')])*12;
							$cm_margin_dzn=($cm_us_dzn-($tot_raw_material+$cm_cost+$tot_commisssion_dzn));//$pre_cost_cm_margin_arr[$row[csf('job_no')]]['margin'];
							//	echo $tot_fob_val.'='.$tot_raw_materials_cost.'='.$po_cm_cost.',';
						 	$cm_margin_val=($cm_margin_dzn/12)*$row[csf('po_quantity')];

						 	$fab_finish_kg=$fabric_booking_data_arr_uom_wise[$row[csf('po_id')]][12]['fin_qnty'];
						 	$fab_finish_yds=$fabric_booking_data_arr_uom_wise[$row[csf('po_id')]][27]['fin_qnty'];
						 	$fab_finish_mtr=$fabric_booking_data_arr_uom_wise[$row[csf('po_id')]][23]['fin_qnty'];

						//$fab_des_row=count(array_unique(explode(",",$fab_des)));
						 	$fab_des_button_kg="<a href='##' onClick=\"generate_order_report('".$row[csf('po_id')]."','".$row[csf('company_id')]."','".$row[csf('job_no')]."','".$row[csf('buyer_name')]."','".$row[csf('style_ref_no')]."','".$row[csf('pub_shipment_date')]."','".$row[csf('country_id')]."','show_po_fabric_dtls','9__12__".$row[csf('actual_po_id')]."')\"> ".number_format($fab_finish_kg,2)." </a>";

						 	$fab_des_button_yds="<a href='##' onClick=\"generate_order_report('".$row[csf('po_id')]."','".$row[csf('company_id')]."','".$row[csf('job_no')]."','".$row[csf('buyer_name')]."','".$row[csf('style_ref_no')]."','".$row[csf('pub_shipment_date')]."','".$row[csf('country_id')]."','show_po_fabric_dtls','9__27__".$row[csf('actual_po_id')]."')\"> ".number_format($fab_finish_yds,2)." </a>";

						 	$fab_des_button_mtr="<a href='##' onClick=\"generate_order_report('".$row[csf('po_id')]."','".$row[csf('company_id')]."','".$row[csf('job_no')]."','".$row[csf('buyer_name')]."','".$row[csf('style_ref_no')]."','".$row[csf('pub_shipment_date')]."','".$row[csf('country_id')]."','show_po_fabric_dtls','9__23__".$row[csf('actual_po_id')]."')\"> ".number_format($fab_finish_mtr,2)." </a>";

                            
							 $fab_des_button="<a href='##' onClick=\"generate_order_report('".$row[csf('po_id')]."','".$row[csf('company_id')]."','".$row[csf('job_no')]."','".$row[csf('buyer_name')]."','".$row[csf('style_ref_no')]."','".$row[csf('pub_shipment_date')]."','','show_po_fabric_dtls','9')\"> ".$fab_des." </a>";
							
							  $fab_des_button2="<a href='##' onClick=\"generate_order_report('".$row[csf('po_id')]."','".$row[csf('company_id')]."','".$row[csf('job_no')]."','".$row[csf('buyer_name')]."','".$row[csf('style_ref_no')]."','".$row[csf('pub_shipment_date')]."','','show_po_fabric_dtls',9)\"> ".number_format($fab_finish_req,2)." </a>";
							
							$tot_ship_qty_button="<a href='##' onClick=\"generate_order_report('".$row[csf('po_id')]."','".$row[csf('company_id')]."','".$row[csf('job_no')]."','".$row[csf('buyer_name')]."','".$row[csf('style_ref_no')]."','".$row[csf('pub_shipment_date')]."','','show_po_ship_qty_dtls','10')\"> ".number_format($ship_qty,0)." </a>";
							    $invoice_qty_button="<a href='##' onClick=\"generate_order_report('".$row[csf('po_id')]."','".$row[csf('company_id')]."','".$row[csf('job_no')]."','".$row[csf('buyer_name')]."','".$row[csf('style_ref_no')]."','".$row[csf('pub_shipment_date')]."','','show_invo_ship_qty_dtls','10')\"> ".number_format($tot_invoice_qty,0)." </a>";
							
							 $profit_except_cm=0;//$row[csf('pub_shipment_date')]
							
							$profit_except_cm=($cm_us_dzn-($tot_raw_material+$tot_commisssion_dzn));
							$export_lc=rtrim($export_lc_data_array[$row[csf('po_id')]][lc_no],',');
							$export_lc=implode(",",array_unique(explode(",",$export_lc)));
							
							$export_sc=rtrim($export_sc_data_array[$row[csf('po_id')]][sc_no],',');
							$export_sc=implode(",",array_unique(explode(",",$export_sc)));
							
							if(!$export_lc) $export_lc=$export_sc;
							//$export_sc=$export_sc_data_array[$row[csf('po_id')]][sc_no];
							$tot_profit_except_val=($profit_except_cm/12)*$row[csf('po_quantity')];
							//echo $export_lc.'='.$export_sc.', ';
							/*if($export_lc!='' &&  $export_sc!='')
							{
								$export_no=$export_lc;
							}
							else if($export_lc=='' && $export_sc!='')
							{
								$export_no=$export_sc;
							}
							else if($export_lc=='' && $export_sc!='')
							{
								$export_no=$export_sc;
							}
							else if($export_lc=='' && $export_sc=='')
							{
								$export_no='';
							}
							else $export_no='';*/
							$booking_nos=rtrim($fabric_booking_data_arr[$row[csf('po_id')]]['booking_no'],',');
							$booking_numbers=array_unique(explode(",",$booking_nos));
							$button_setting='';
							foreach($booking_numbers as $bookno)
							{
								$item_category=$fabric_booking_data2_arr[$bookno]['item_category'];
								$booking_type=$fabric_booking_data2_arr[$bookno]['booking_type'];
								$fabric_source=$fabric_booking_data2_arr[$bookno]['fabric_source'];
								$is_short=$fabric_booking_data2_arr[$bookno]['is_short'];
								$entry_form=$fabric_booking_data2_arr[$bookno]['entry_form'];
								$is_approved=$fabric_booking_data2_arr[$bookno]['is_approved'];
								
								if($booking_type==1 && $entry_form==108 && $is_short==2){
									$row_id=$format_ids_partial[0];
								}
								else if($booking_type==1 && $entry_form==118 && $is_short==2){
									$row_id=$format_ids[0];
								}
								else
								{
									$row_id=$print_report_ids_short[0];
								}
								//echo $booking_type.'='. $is_short.'='. $entry_form;
								if($row_id==1)
									{ 
									$button_setting.="<a href='#' onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report_gr','".$i."')\"> ".$bookno." <a/>".',';
									}
									else if($row_id==2)
									{ 
									 
									 $button_setting.="<a href='#' onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report','".$i."')\">  ".$bookno. "<a/>".',';
									}
								 	else if($row_id==3)
									{ 
									 
									  $button_setting.="<a href='#' onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report3','".$i."')\"> ".$bookno." <a/>".',';
									}
									else if($row_id==4)
									{ 
									 $button_setting.="<a href='#' onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report1','".$i."')\">" .$bookno. "<a/>".',';
									}
								   	else if($row_id==5)
									{ 
									 $button_setting.="<a href='#' onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report2','".$i."')\"> ".$bookno." <a/>".',';
									}
									else if($row_id==6)
									{ 
									 $button_setting.="<a href='#' onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report4','".$i."')\"> ".$bookno." <a/>".',';
									}
								   	else if($row_id==7)
									{ 
									 $button_setting.="<a href='#' onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report5','".$i."')\"> ".$bookno." <a/>".',';
								   	}
									else if($row_id==8)
									{ 
									 $button_setting.="<a href='#' onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report','".$i."')\"> ".$bookno." <a/>".',';
								   	}
									else if($row_id==9)
									{ 
									 $button_setting.="<a href='#' onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report3','".$i."')\"> ".$bookno." <a/>".',';
								   	}
									
								   	else if($row_id==45) 
									{ 
									 $button_setting.="<a href='#' onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report_urmi','".$i."')\"> ".$bookno." <a/>".',';
									}
								   	else  if($row_id==53)
									{ 
									 $button_setting.="<a href='#'  onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report_jk','".$i."')\"> ".$bookno." <a/>".',';
									}
								   	else if($row_id==93)
									{ 
									 $button_setting.="<a href='#'  onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report_libas','".$i."')\"> ".$bookno." <a/>".',';
									}
								   else if($row_id==73)
									{ 
									 $button_setting.="<a href='#'  onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report_mf','".$i."')\"> ".$bookno." <a/>".',';
									}
								    else if($row_id==85)
									{ 
									 $button_setting.="<a href='#'  onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','print_booking_3','".$i."')\"> ".$bookno."<a/>".',';
									 }
									else if($row_id==84)
									{ 
									 $button_setting.="<a href='#'  onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report_urmi_per_job','".$i."')\"> ".$bookno."<a/>".',';
									 }
									else if($row_id==143)
									{ 
									 $button_setting.="<a href='#'  onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report_urmi','".$i."')\"> ".$bookno."<a/>".',';
									 }
							}
							
							
								
								
						
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
							<td width="30"><? echo $i; ?></td>
							<td width="40" title="Pub Ship Date Year"><? echo date("Y",strtotime($row[csf('pub_shipment_date')])); ?></td>
							<td width="90"  title="Pub Ship Date Month" align="center"><p><? echo date("F",strtotime($row[csf('pub_shipment_date')])); ?></p></td>
							<td width="80" ><div style="word-break:break-all"><? echo $team_leader_library
	[$row[csf('team_leader')]]; ?></div></td>
							<td width="80"><div style="word-break:break-all"><? echo $dealing_merchant_array[$row[csf('dealing_marchant')]]; ?></div></td>
                            <td width="80"><div style="word-break:break-all"><? echo $company_library[$row[csf('style_owner')]]; ?></div></td>
							<td width="100"><div style="word-wrap:break-word:100px;"><? echo $product_dept[$row[csf('product_dept')]]; ?></div></td>
							<td width="50"><div style="word-wrap:break-word:100px;"><? echo $row[csf('job_prefix')]; ?></div></td>
                            <td width="130"><div style="word-break:break-all"><? echo $row[csf('style_ref_no')]; ?></div></td>
							<td width="100" title="<? echo $row[csf('job_no')];?>"> <div style="word-break:break-all"> <? echo $row[csf('po_number')]; ?>   </div>
							<td width="80" align="center" > <? echo change_date_format($row[csf('po_received_date')]);// echo number_format($job_qty,2); ?>
							</td>
							
							<td width="100" align="left"><div style="word-break:break-all"><? echo $buyer_library[$row[csf('buyer_name')]];?> </div></td>
							<td width="100" align="left"><div style="word-break:break-all"><? echo $buyer_library[$row[csf('client_id')]];?> </div></td>
							
							<td width="80" align="center">
                            <div style="word-wrap:break-word:80px;">
							 <? echo $season_name_library[$row[csf('season_matrix')]];?>
                             </div>
							</td>
							<td width="130" align="left"><div style="word-break:break-all">
							<?
							$gmts_item='';$body_item=''; $gsm_item=''; $gmts_item_id=array_unique(explode(",",$row[csf('gmts_item_id')]));
							foreach($gmts_item_id as $item_id)
							{
								if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
								if($body_item=="") $body_item=$body_part[$pre_cost_data_arr[$row[csf('job_no')]][$item_id]['body']]; else $body_item.=",".$body_part[$pre_cost_data_arr[$row[csf('job_no')]][$item_id]['body']];
								if($gsm_item=="") $gsm_item=$pre_cost_data_arr[$row[csf('job_no')]][$item_id]['gsm']; else $gsm_item.=",".$pre_cost_data_arr[$row[csf('job_no')]][$item_id]['gsm'];
							}
									echo $gmts_item; 
									$order_qty_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
									?>
                                    </div>
							</td>
							
							<td width="150"  align="center"><div style="word-break:break-all">
							<? echo  $fab_des_button; ?>
                            
                            </div></td>
                            <td width="200" title="Composition"  align="center"><div style="word-break:break-all">
							<? echo  $composition;
							
							$pay_mode=rtrim($fabric_booking_data_arr[$row[csf('po_id')]]['pay_mode'],',');
							$supplier_ids=rtrim($fabric_booking_data_arr[$row[csf('po_id')]]['supplier_id'],',');
							$suppliers_comp=implode(", ",array_unique(explode(",",$supplier_ids)));
							
							$booking_no=implode(",",array_unique(explode(",",$booking_nos)));
							
							
							$lrevised_date=explode(" ",$revised_date_arr[$row[csf('po_id')]]['lship_date']);
							$forig_date=explode(" ",$revised_date_arr[$row[csf('po_id')]]['forig_date']);
							if($forig_date[0]!='')
							{
								$origin_ship_date=$forig_date[0];	
							}
							else
							{
								$origin_ship_date=$row[csf('shipment_date')];
							}
							if($lrevised_date[0]!='')
							{
								$last_ship_date=$row[csf('shipment_date')];//$lrevised_date[0];	
							}
							else
							{
								$last_ship_date='';
							}
							
							
							 ?>
                            
                            </div></td>
							<td width="150" ><div style="word-break:break-all"><? echo rtrim($button_setting,','); ?></div></td>
                            <td width="120" ><div style="word-break:break-all"><? echo $suppliers_comp; ?></div></td>
							<td width="60" align="center"><div style="word-break:break-all"><? echo $fab_gsm; ?></div></td>
							<td width="70" align="right"><div style="word-break:break-all"><? echo $row[csf('po_quantity')]; ?></div></td>
							<td width="50" align="center"><div style="word-break:break-all"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></div></td>
							<td width="80" align="right"> <p> <? echo $order_qty_pcs; ?>   </p>
							<td width="40" align="right" > <? echo $row[csf('ratio')]; ?>  </td>
                            
							<td width="80" align="right" title="Po Qty-Ex-Fact Qty"> <? echo  number_format($short_excess_qty,2); ?></td>
                            <td width="80" align="right"> <? echo  number_format($export_invoice_arr[$row[csf('po_id')]][2]['air']/$row[csf('ratio')],0); ?></td>
                            <td width="80" align="right"> <? echo  number_format($export_invoice_arr[$row[csf('po_id')]][1]['sea']/$row[csf('ratio')],0); ?></td>
                            <td width="90" title="Air+Sea Qty" align="right"> <? echo  $tot_ship_qty_button;//number_format($ship_qty,0); ?></td>
							<td width="80"   align="center" title="First Ship Date"><? echo change_date_format($origin_ship_date); ?></td>
                            <td width="80" align="right">  <? echo $invoice_qty_button;//number_format($tot_invoice_qty,0); ?> </td>
							<td width="80" align="center" title="Last Revised Date"><div style="word-break:break-all"><? 
							
							echo change_date_format($last_ship_date);
							//echo $revised_date_arr[$row[csf('po_id')]]['ship_date']; ?> </div></td>
							<td width="80" align="center">  <? echo change_date_format($invoice_date); ?> </td>
							<td width="60" title="FOB Value/PO Qty" align="right"><? echo number_format($avg_unit,2); ?></td>
							<td width="110"  align="left"><div style="word-break:break-all"><? echo $export_lc; ?> &nbsp;</div></td>
                            <td width="70" align="right" title="Total Commission(<? echo number_format($tot_commisssion,2);?>)/PO Qty*12"><p> <? echo  number_format($tot_commisssion_dzn,2); ?></p></td>
                            <td width="100" align="right" title="<? echo 'Total '.number_format($tot_raw_materials_cost,2);?>"><div style="word-break:break-all"><? echo number_format($tot_raw_material,2); ?></div></td>
                            
                            <td width="70" align="right" title="<? echo 'Total '.number_format($po_cm_cost,2); ?>" ><div style="word-break:break-all"><? echo number_format($cm_cost,2); ?></div></td>
							<td width="70" align="right" title="FOB Value-(Raw Material Cost(<? echo number_format($tot_raw_materials_cost,2)?>)+ Overhead cost (<? echo number_format($cm_cost,2);?>+  commission(<? echo number_format($tot_commisssion,2);?>))/ PO Qty X 12"><div style="word-break:break-all"><? echo number_format($cm_margin_dzn,2); ?></div></td>
                            <td width="70" align="right" title="{FOB value-(Raw material cost + Commission)}/PO Qty X 12"><div style="word-break:break-all"><? echo number_format($profit_except_cm,2); ?></div></td>
                            
                            
							<td width="60" title="Invoice Date-Ori. Ship Date"  align="center"><div style="word-break:break-all"><? echo number_format($date_diff);?></div></td>
							<td width="60"  align="center"><div style="word-break:break-all"><? 
							if($date_diff<0)
							{
								$early_status='Delay'; 
							}
							if($date_diff>0)
							{
								$early_status='Early';
							}
							if($date_diff==0)
							{
								$early_status='Ontime';
							}
							if($date_diff=='')
							{
								$early_status='';
							}
							echo $early_status; 
							 ?></div></td>
							<td width="100" align="right" title="PO Qty*Unit Price"><div style="word-break:break-all"><? $tot_fob_price=$row[csf('po_quantity')]*$avg_unit; echo number_format($tot_fob_price,4);?></div></td>
							<td width="100" align="right" title="Ship Qty*Unit Price"> <p> <? $tot_ex_fact_val=$ship_val;echo number_format($ship_val,2); ?>   </p>
							<td width="100" title="Ship Val-FOB Value" align="right"><? $tot_short_excess_val=$ship_val-$tot_fob_price; echo number_format($tot_short_excess_val,2); ?> </td>
							<td width="80" title="Raw material=All Booking+Lab+General Acceesories Cost" align="right"><?  echo number_format($tot_raw_materials_cost,2); ?> </td>
                            <td width="90" title="CM US/12*PO Qty" align="right" ><p><? echo number_format($cm_margin_val,2); ?></p></td>
                             <td width="90" title="Profit Except OH/12*PO Qty" align="right" ><p><?  echo number_format($tot_profit_except_val,2); ?></p></td>
							<td width="70" align="center"> <div style="word-wrap:break-word:70px;"><? echo $quality_label[$row[csf('qlty_label')]]; ?></div></td>
							
							
							<td width="70" align="right"> <p><? echo number_format($ex_fact_carton_qty); ?> </p> </td>
							<td width="80" align="right" ><p> <? echo number_format($row[csf('up_charge')],2); ?>  </p></td>
							<td width="100" align="left"><div style="word-wrap:break-word:100px;"><? echo $lab_dip; ?></div></td>
							<td width="80" align="right"><div style="word-wrap:break-word:100px;"><? echo $sewing_data_array[$row[csf('po_id')]]['sewing_com']; ?> </div></td>
							<td width="80" title="Invoice Date"   align="center"><?
							$ex_fact_date=$invoice_date;
							 if($ex_fact_date!='')
							 {
							 echo date("F",strtotime($ex_fact_date));
							 }
							 else echo '';
							 
							 ?> </td>
							<td width="80" align="right" title="Main Booking Qty"><? echo $fab_des_button_kg; ?> </td>
							<td width="100" align="right" title="Main Booking Qty"><? echo $fab_des_button_yds; ?> </td>
							<td width="100" align="right" title="Main Booking Qty"><? echo $fab_des_button_mtr; ?> </td>
						  
							<td width=""  align="right"><? echo number_format($row[csf('set_smv')],2); ?></td>
						   
							
						</tr>
					<?
					$total_po_qty+=$order_qty_pcs;
					$total_ex_fact_qty+=$ex_fact_qty;
					$total_invoice_qty+=$tot_invoice_qty;
					$total_tot_fob_price+=$tot_fob_price;
					$total_tot_air+=$export_invoice_arr[$row[csf('po_id')]][2]['air']/$row[csf('ratio')];
					$total_tot_sea+=$export_invoice_arr[$row[csf('po_id')]][1]['sea']/$row[csf('ratio')];
					$total_cm_margin_val+=$cm_margin_val;
					$total_profit_except_val+=$tot_profit_except_val;
					
					$total_up_charge+=$row[csf('up_charge')];
					$total_fab_finish_req+=$fab_finish_req;
					$total_raw_materials_cost+=$tot_raw_materials_cost;
					$i++;
					}
					?>
					</table>
                    </div>
					<table class="rpt_table" width="4930" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
						<tfoot>
							<th width="30">&nbsp;</th>
							<th width="40">&nbsp;</th>
							<th width="90">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="80">&nbsp;</th>
                            <th width="80">&nbsp;</th>
                            <th width="100">&nbsp;</th>
							<th width="50">&nbsp;</th>
							<th width="130">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="80">&nbsp;</th>
							
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							
							<th width="80">&nbsp;</th>
							<th width="130">&nbsp;</th>
                          
							
							<th width="150">&nbsp;</th>
                            <th width="200">&nbsp;</th>
							<th width="150">&nbsp;</th>
                            <th width="120">&nbsp;</th>
							<th width="60">Total</th>
							<th width="70"><? //echo number_format($total_po_qty); ?></th>
							<th width="50">&nbsp;</th>
							<th width="80"  id="total_po_qty"><? echo number_format($total_po_qty); ?></th>
							<th width="40">&nbsp;</th>
                            
							<th width="80">&nbsp;<? //echo number_format($total_tot_ex_fact_val); ?></th>
                            <th width="80" id="total_ship_qty_air"><? echo number_format($total_tot_air,0); ?></th>
                            <th width="80" id="total_ship_qty_sea"><? echo number_format($total_tot_sea,0); ?></th>
                            <th width="90" id="total_ship_qty"><? echo number_format($total_tot_sea+$total_tot_air,0); ?></th>
                            
							<th width="80">&nbsp;</th>
                          <th width="80" id="total_invoice_qty"><? echo number_format($total_invoice_qty,0); ?></th>
							<th width="80">&nbsp;<? //echo number_format($total_tot_ex_fact_val); ?></th>
							<th width="80">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="110">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="100">&nbsp;</th>
							<th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="100" id="value_total_fob_price"><? echo number_format($total_tot_fob_price,2);//echo $total_cm_margin_val; ?></th>
							<th width="100" id="value_total_ex_fact_val"><? echo number_format($total_tot_ex_fact_val,2); ?></th>
							<th width="100">&nbsp;</th>
							<th width="80" id="value_total_raw_materials_cost"><? echo number_format($total_raw_materials_cost,2); ?></th>
                            <th width="90" id="value_total_cm_margin_val"><? echo number_format($total_cm_margin_val,2); ?></th>
                            <th width="90" id="value_total_profit_except_val"><? echo number_format($total_profit_except_val,2); ?></th>
							<th width="70">&nbsp;</th>
							
							
							<th width="70">&nbsp;</th>
							<th width="80" id="value_total_up_charge"><? echo number_format($total_up_charge,2); ?></th>
							<th width="100">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="80" id="value_total_fab_finish_req1"><? echo number_format($total_fab_finish_req,2); ?></th>
							<th width="100" id="value_total_fab_finish_req2"><? echo number_format($total_fab_finish_req,2); ?></th>
							<th width="100" id="value_total_fab_finish_req3"><? echo number_format($total_fab_finish_req,2); ?></th>
							<th>&nbsp;</th>
						</tfoot>
					</table>
				
				</fieldset>
			</div>
	<?
		}
	}
	//PO Wise End
	else if($report_type==2) //Job Wise
	{
		if($template==1)
		{
			$revised_date_arr=array();$exfactory_data_array=array();$pre_cost_data_arr=array();
			$fabric_booking_data_arr=array();$fabric_des_data_arr=array();$pre_cost_cm_margin_arr=array();		
			$export_lc_data_array=array();$sewing_data_array=array();$lab_dip_data_arr=array();
			
			$sql_update_history=sql_select("Select po_id,max(po_received_date) as po_received_date,max(shipment_date) as shipment_date from wo_po_update_log group by po_id order by po_id");
			
			foreach($sql_update_history as $row)
			{
				$revised_date_arr[$row[csf('po_id')]]['recv_date']=$row[csf('po_received_date')];
				$revised_date_arr[$row[csf('po_id')]]['ship_date']=$row[csf('shipment_date')];
			}
			if($db_type==2)
			{
				$lab_grp="listagg(CAST(a.id as VARCHAR(4000)),',') within group (order by a.id) as lab_id";
				$count_grp="listagg(CAST(b.count_id as VARCHAR(4000)),',') within group (order by b.count_id) as count_id";
				$grop_fab="listagg(CAST(b.gsm_weight as VARCHAR(4000)),',') within group (order by b.gsm_weight) as gsm_weight,listagg(CAST(b.construction as VARCHAR(4000)),',') within group (order by b.construction) as descrp,listagg(CAST(b.lib_yarn_count_deter_id as VARCHAR(4000)),',') within group (order by b.lib_yarn_count_deter_id) as deter_id";
			}
			else
			{
				$lab_grp="group_concat(a.id) as lab_id";
				$count_grp="group_concat(b.count_id) as count_id";
				$grop_fab="group_concat(b.gsm_weight) as gsm_weight,group_concat(b.construction) as descrp,group_concat(b.lib_yarn_count_deter_id) as deter_id";
			}
			
			
			$sql_pre=sql_select("Select b.job_no,$grop_fab,d.margin_dzn as margin_dzn from wo_po_details_master a,wo_pre_cost_fabric_cost_dtls b,wo_pre_cos_fab_co_avg_con_dtls c,wo_pre_cost_dtls d where a.job_no=b.job_no and c.pre_cost_fabric_cost_dtls_id=b.id and c.job_no=a.job_no and d.job_no=a.job_no  and d.job_no=b.job_no  and  c.cons>0 and a.company_name='$company_name' and b.body_part_id in($body_id) $buyer_id_cond $job_no_cond $year_cond group by b.job_no,d.margin_dzn");
			
			foreach($sql_pre as $row)
			{
				$pre_cost_data_arr[$row[csf('job_no')]]['deter_id']=$row[csf('deter_id')];
				$pre_cost_data_arr[$row[csf('job_no')]]['gsm']=$row[csf('gsm_weight')];
				$pre_cost_data_arr[$row[csf('job_no')]]['des']=$row[csf('descrp')];
				$pre_cost_cm_margin_arr[$row[csf('job_no')]]['cm']=$row[csf('margin_dzn')];
			}
			$sql_book=sql_select("Select b.job_no,b.booking_no,c.po_break_down_id as po_id from wo_po_details_master a,wo_booking_mst b,wo_booking_dtls c where a.job_no=b.job_no and a.job_no=b.job_no and c.booking_no=b.booking_no  and b.booking_type=1 and b.is_short=2 and a.company_name='$company_name' $buyer_id_cond $job_no_cond");
			
			foreach($sql_book as $row)
			{
				$fabric_booking_data_arr[$row[csf('po_id')]]['booking_no']=$row[csf('booking_no')];
			}
			
			
			
			$sql_yarn=sql_select("Select a.id,a.construction,a.gsm_weight, $count_grp from lib_yarn_count_determina_mst a,lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active =1 and a.is_deleted=0 group by a.id,a.construction,a.gsm_weight");
			
			foreach($sql_yarn as $row)
			{
				$fabric_des_data_arr[$row[csf('id')]]['des']=$row[csf('construction')];
				$fabric_des_data_arr[$row[csf('id')]]['gsm']=$row[csf('gsm_weight')];
				$fabric_des_data_arr[$row[csf('id')]]['count']=$row[csf('count_id')];
			}
			
			/*$sql_yarn=sql_select("Select a.id,a.construction,a.gsm_weight from lib_yarn_count_determina_mst a where a.status_active =1 and a.is_deleted=0");
			
			foreach($sql_yarn as $row)
			{
				$fabric_des_data_arr[$row[csf('id')]]['des']=$row[csf('construction')];
				$fabric_des_data_arr[$row[csf('id')]]['gsm']=$row[csf('gsm_weight')];
			}*/
			if($db_type==2)
			{
				$lab_grp="listagg(CAST(a.id as VARCHAR(4000)),',') within group (order by a.id) as lab_id";
			}
			else
			{
				$lab_grp="group_concat(a.id) as lab_id";
			}
			
			$sql_lab=sql_select("Select a.po_break_down_id as po_id,$lab_grp from wo_po_lapdip_approval_info a where a.status_active =1 and a.is_deleted=0 group by a.po_break_down_id");
			
			foreach($sql_lab as $row)
			{
				$lab_dip_data_arr[$row[csf('po_id')]]['ldip_id']=$row[csf('lab_id')];
				
			}
			$sql_sewing=sql_select("Select a.serving_company,a.production_source,a.po_break_down_id as po_id from pro_garments_production_mst a where a.company_id='$company_name' and a.production_type=4 and a.status_active =1 and a.is_deleted=0");
			
			foreach($sql_sewing as $row)
			{
				if($row[csf('production_source')]==1) $sewing_com=$company_library[$row[csf('serving_company')]]; else if($row[csf('production_source')]==3) $sewing_com=$supplier_library[$row[csf('serving_company')]];
				
				//$sewing_data_array[$row[csf('po_id')]]['des']=$row[csf('production_source')];
				$sewing_data_array[$row[csf('po_id')]]['sewing_com']=$sewing_com;
			}
			
			$exfactory_data=sql_select("select po_break_down_id as po_id,MAX(ex_factory_date) as ex_fact_date,
			sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as ex_fact_qty,
			sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as ex_fact_ret_qty,
			sum(CASE WHEN entry_form!=85 THEN total_carton_qnty ELSE 0 END) as tot_carton_qty,
			sum(CASE WHEN entry_form=85 THEN total_carton_qnty ELSE 0 END) as tot_ret_carton_qty
			 from pro_ex_factory_mst  where 1=1 and status_active=1 and is_deleted=0 group by po_break_down_id");
			
			foreach($exfactory_data as $row)
			{
				$exfactory_data_array[$row[csf('po_id')]][ex_fact_qty]+=$row[csf('ex_fact_qty')]-$row[csf('ex_fact_ret_qty')];
				$exfactory_data_array[$row[csf('po_id')]][carton_qty]+=$row[csf('tot_carton_qty')]-$row[csf('tot_ret_carton_qty')];
				$exfactory_data_array[$row[csf('po_id')]][ex_factory_date]=$row[csf('ex_fact_date')];
			}
			$sql_lc=sql_select("select a.export_lc_no,b.wo_po_break_down_id as po_id from  com_export_lc a,com_export_lc_order_info b where a.id=b.com_export_lc_id and a.status_active ='1' and a.is_deleted ='0'");
			
			foreach($sql_lc as $row)
			{
				
				$export_lc_data_array[$row[csf('po_id')]][lc_no]=$row[csf('export_lc_no')];
			}
			
			if($db_type==0) $group_concat="group_concat( distinct b.id) AS po_number,group_concat( distinct b.id) AS po_id,group_concat( distinct b.pub_shipment_date) AS pub_shipment_date,group_concat( distinct b.po_received_date) AS po_received_date,group_concat( distinct b.shipment_date) AS shipment_date,group_concat( distinct b.grouping) AS grouping,group_concat( distinct b.file_no) AS file_no,group_concat( distinct b.shiping_status) AS shiping_status";
			else if($db_type==2)  $group_concat="listagg(cast(b.id as varchar2(4000)),',') within group (order by b.id) AS po_id,listagg(cast(b.po_number as varchar2(4000)),',') within group (order by b.po_number) AS po_number,listagg(cast(b.pub_shipment_date as varchar2(4000)),',') within group (order by b.pub_shipment_date) AS pub_shipment_date,listagg(cast(b.po_received_date as varchar2(4000)),',') within group (order by b.po_received_date) AS po_received_date,listagg(cast(b.shipment_date as varchar2(4000)),',') within group (order by b.shipment_date) AS shipment_date,listagg(cast(b.grouping as varchar2(4000)),',') within group (order by b.grouping) AS grouping,listagg(cast(b.file_no as varchar2(4000)),',') within group (order by b.file_no) AS file_no,listagg(cast(b.shiping_status as varchar2(4000)),',') within group (order by b.shiping_status) AS shiping_status";
			//a.style_owner,b.is_confirmed,b.shiping_status b.unit_price
		 $sql_result="select a.job_no_prefix_num as job_prfix,$group_concat,a.team_leader,a.style_owner,a.qlty_label,a.dealing_marchant,a.ship_mode,a.product_dept,a.season_matrix, a.job_no, a.buyer_name, a.style_ref_no, a.set_smv, a.avg_unit_price as unit_price, 
				a.order_uom,a.gmts_item_id, a.total_set_qnty as ratio,sum(b.up_charge) as up_charge,
				sum(b.po_quantity) as po_quantity
				from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$company_name' and a.status_active=1 and
				a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond 
				$order_status_cond  $internal_ref_cond $file_no_cond $season_cond $style_owner_cond $team_leader_cond $dealing_marchant_cond group by a.job_no_prefix_num,a.team_leader,a.style_owner,a.qlty_label,a.dealing_marchant,a.ship_mode,a.product_dept,a.season_matrix, a.job_no, a.buyer_name, a.style_ref_no, a.set_smv, a.avg_unit_price, a.dealing_marchant,a.order_uom,a.gmts_item_id, a.total_set_qnty ";
				
				$result_data=sql_select($sql_result); 
				$tot_rows_po=count($result_data); 
		
			ob_start(); 
		?>
			<div style="width:4030px">
			<fieldset style="width:100%;">	
				<table width="4030">
					<tr class="form_caption">
						<td colspan="47" align="center">OMS Report</td>
					</tr>
					<tr class="form_caption">
						<td colspan="47" align="center"><? echo $company_library[$company_name]; ?></td>
					</tr>
				</table>
				<table id="table_header_1" class="rpt_table" width="4030" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<th width="40">SL</th>
						<th width="50">Ship Year</th>
						<th width="90">Order Month</th>
						<th width="110"><div style="word-break:break-all">Team Leader</div></th>
						<th width="110">Deal Merchandising</th>
                        <th width="120"><div style="word-wrap:break-word:100px;">Style Owner</div></th>
						<th width="100">Prod. Dept.</th>
                        <th width="100">Job No</th>
						<th width="100"><div style="word-break:break-all">Style Ref.</div></th>
						<th width="100"><div style="word-break:break-all">Order No</div></th>
						<th width="80">Order Rcvd Date</th>
						<th width="80">Revised Rcvd.</th>
						<th width="100">Buyer Name</th>
						<th width="80">Count</th> 
						<th width="80">Season</th>
						<th width="120">GMT Item</th>
						<th width="50">Ship Mode</th>
						
						<th width="150">Fabric Desc</th>
						<th width="100">Fab. Booking No</th>
						<th width="100">GSM</th>
						<th width="100">Order Qty</th>
						<th width="50">Order UOM</th>
						<th width="80">Order Qty / Pcs</th>
						<th width="60">Set Ratio</th>
						<th width="80">Ex-Fact QTY</th>
						<th width="80">Short / Excess Qty</th>
						
						<th width="80">Ori. Ship Date</th>
						<th width="80">Revised Ship Date</th>
						<th width="80">Ex-Factory Date</th>
						<th width="60">Unit Price (FOB)</th>
						<th width="100">Export LC No</th>
						<th width="70">CM US</th>
						
						 <th width="60">EARLY / Delay Days</th>
						<th width="60">Early / Delay Status</th>
						<th width="100">Total FOB Price</th>
						<th width="100">Ex-Factory Val</th>
						<th width="100">Short / Excess Val</th>
						<th width="80">Total CM</th>
						
						<th width="70">Qty Level</th>
						<th width="80">Ship Status</th>
					
						<th width="70">No Of Ctn</th>
						<th width="80">Up Charge</th>
						<th width="80">Lab Dip No.</th>
						
						 <th width="100">Sew. Company</th>
						<th width="80">Ex-Fact Month</th>
						<th width="80">Fabric Qty</th>
						<th width="">SMV</th>
					   
						
					</thead>
				</table>

				<div style="width:4030px; max-height:400px; overflow-y:scroll" id="scroll_body">
					<table class="rpt_table" width="4010" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
					<?
					$i=1; $total_po_qty=0; $total_ex_fact_qty=0;$total_tot_fob_price=0;$total_tot_ex_fact_val=0;		
					$total_cm_margin_val=0;	$total_qlty_qty=0;	$total_up_charge=0;	 $total_fab_finish_req=0; 
				  //  $tot_rows=count($nameArray);
			 $condition= new condition();
			 $condition->company_name("=$cbo_company_name");
			 if(str_replace("'","",$cbo_buyer_name)>0){
				  $condition->buyer_name("=$cbo_buyer_name");
			 }
			 if(str_replace("'","",$txt_job_no) !=''){
				  $condition->job_no_prefix_num("=$txt_job_no");
			 }
			 if(str_replace("'","",$cbo_order_status) >0){
				  $condition->is_confirmed("=$cbo_order_status");
			 }
			 if(str_replace("'","",$cbo_order_status)==0){
				  $condition->is_confirmed("in(1,2)");
			 }
			 if(str_replace("'","",$cbo_search_date) ==1 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!=''){
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
				/*if($db_type==0)
				{
				 $condition->insert_date(" between '".$start_date."' and '".$end_date." 23:59:59'");
				}
				else
				{
					$condition->insert_date(" between '".$start_date."' and '".$end_date." 11:59:59 PM'");
				}*/
				
				
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
			
			 $condition->init();
			//$yarn= new yarn($condition);
			//echo $yarn->getQuery(); die;
			$fabric= new fabric($condition);
			$fabric_costing_arr=$fabric->getQtyArray_by_order_knitAndwoven_greyAndfinish();
			//print_r($fabric_costing_arr);
					
					foreach($result_data as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						$poids=array_unique(explode(",",$row[csf('po_id')]));
						$ex_fact_qty=0;$ex_fact_carton_qty=0; $fab_production_knit=0;$fab_production_woven=0;
						$lab_dip_no='';$fab_deterid='';$lab_dip_no=''; $ex_fact_date=''; $lc_nos='';
						foreach($poids as $pid)
						{
						  $ex_fact_qty+=$exfactory_data_array[$pid][ex_fact_qty]/$row[csf('ratio')];
						  $ex_fact_carton_qty+=$exfactory_data_array[$pid][carton_qty];	
						  $fab_production_knit+=$fabric_costing_arr['knit']['finish'][$pid];
						  $fab_production_woven+=$fabric_costing_arr['woven']['finish'][$pid];
						 //  $lab_dip_no.=", ".$lab_dip_data_arr[$pid]['ldip_id'];	
						 if($lab_dip_no=='') $lab_dip_no=$lab_dip_data_arr[$pid]['ldip_id'];else $lab_dip_no.=", ".$lab_dip_data_arr[$pid]['ldip_id'];
						   if($fab_deterid=='') $fab_deterid=$pre_cost_data_arr[$pid]['deter_id'];else $fab_deterid.=", ".$pre_cost_data_arr[$pid]['deter_id'];
						     if($ex_fact_date=='') $ex_fact_date=change_date_format($exfactory_data_array[$pid][ex_factory_date]);else $ex_fact_date.=", ".change_date_format($exfactory_data_array[$pid][ex_factory_date]);
						
						  if($lc_nos=='') $lc_nos= $export_lc_data_array[$pid][lc_no];else $lc_nos.=", ".$export_lc_data_array[$pid][lc_no];
						
						}
						// $fab_deter_id=$pre_cost_data_arr[$row[csf('po_id')]]['deter_id'];
						$fab_deter_id=array_unique(explode(",",$pre_cost_data_arr[$row[csf('job_no')]]['deter_id']));
						 $fab_des=implode(",",array_unique(explode(",",$pre_cost_data_arr[$row[csf('job_no')]]['des'])));
						$fab_gsm=implode(",",array_unique(explode(",",$pre_cost_data_arr[$row[csf('job_no')]]['gsm']))); 
						$yarn_counts="";
						 foreach($fab_deter_id as $yid)
						 {
							if($yarn_counts=="") $yarn_counts=$yarn_count_library[$fabric_des_data_arr[$yid]['count']];else $yarn_counts.=",".$yarn_count_library[$fabric_des_data_arr[$yid]['count']];
							
						 }
						
						 
						 $fab_deterid=array_unique(explode(",",$fab_deterid));
						 //$fab_des=''; $fab_gsm='';
						 //print_r($fab_deterid);
						foreach($fab_deterid as $fab_did)
						{
						// if($fab_des=='') $fab_des=$fabric_des_data_arr[$fab_did]['des'];else $fab_des.=",".$fabric_des_data_arr[$fab_did]['des'];
						 // if($fab_gsm=='') $fab_gsm=$fabric_des_data_arr[$fab_did]['gsm'];else $fab_gsm.=", ".$fabric_des_data_arr[$fab_did]['gsm'];
						 // $fab_count_id=array_unique(explode(",",$fabric_des_data_arr[$fab_did]['count']));
							//$yarn_counts='';
							 foreach($fab_count_id as $y_id)
							 {
								//if($yarn_counts=="") $yarn_counts=$yarn_count_library[$y_id];else $yarn_counts.=",".$yarn_count_library[$y_id];
							 }
						}
						
						 $short_excess_qty=$ex_fact_qty-$row[csf('po_quantity')];
						 $cm_margin_dzn=$pre_cost_cm_margin_arr[$row[csf('job_no')]]['cm'];
						 $cm_margin_val=($cm_margin_dzn/12)*$row[csf('po_quantity')];
						
						 $fab_finish_req=$fab_production_knit+$fab_production_woven;
						 $po_received_date= array_unique(explode(",",$row[csf('po_received_date')])); 			
						 $shipment_date= array_unique(explode(",",$row[csf('shipment_date')])); 
						// echo $row[csf('pub_shipment_date')];
						 $pub_shipment_date= array_unique(explode(",",$row[csf('pub_shipment_date')]));
						  $job_year_con="";  $job_mon_con="";
						 foreach($pub_shipment_date as $pub_shipdate)
						 {
							  if($job_year_con=='') $job_year_con=date("Y",strtotime($pub_shipdate));else $job_year_con.=", ".date("Y",strtotime($pub_shipdate)); 
							  if($job_mon_con=='') $job_mon_con=date("F",strtotime($pub_shipdate));else $job_mon_con.=", ".date("F",strtotime($pub_shipdate)); 
						 }
						 
						 $shipment_date_con="";	$po_received_date_con='';
						 foreach($po_received_date as $po_date)
						 {
							  if($po_received_date_con=='') $po_received_date_con=change_date_format($po_date);else $po_received_date_con.=", ".change_date_format($po_date); 
						 }
						 foreach($shipment_date as $po_shipdate)
						 {
							  if($shipment_date_con=='') $shipment_date_con=change_date_format($po_shipdate);else $shipment_date_con.=", ".change_date_format($po_shipdate); 
						 }
						 
						  $shipment_status_id= array_unique(explode(",",$row[csf('shiping_status')])); 
						  $ship_status_con='';
						 foreach($shipment_status_id as $status_id)
						 {
							if($ship_status_con=='') $ship_status_con=$shipment_status[$status_id];else $ship_status_con.=", ".$shipment_status[$status_id]; 
						 }
						// echo $order_status_con.'ds';
						 $ex_fact_dat= array_unique(explode(",",$ex_fact_date)); 
						 $exf_mon_con='';
						 foreach($ex_fact_dat as $exf_date)
						 {
							  if($exf_mon_con=='') $exf_mon_con=date("F",strtotime($exf_date));else $exf_mon_con.=", ".date("F",strtotime($exf_date)); 
						 }
						 
						$date_diff=datediff( "d", $exfactory_data_array[$row[csf('po_id')]][ex_factory_date] , $row[csf('pub_shipment_date')]);	
							
						$po_number=implode(",",array_unique(explode(",",$row[csf('po_number')])));
						$po_number_row=count(array_unique(explode(",",$row[csf('po_number')])));
						if($po_number_row>1)
						{
							
							 $po_button="<input type='button' value='View' onClick=\"generate_order_report('".$row[csf('po_id')]."','".$row[csf('company_id')]."','".$row[csf('job_no')]."','".$row[csf('buyer_name')]."','".$row[csf('style_ref_no')]."','show_po_listview_report','1')\" style='width:50px;' name='print_po' id='print_po' class='formbutton' />";
						}
						else
						{
							$po_button=$po_number;	
						}
						
						$orgi_shipment_date=implode(",",array_unique(explode(",",$shipment_date_con)));
						$orgi_shipment_date_row=count(array_unique(explode(",",$shipment_date_con)));
						if($orgi_shipment_date_row>1)
						{
							
							 $orgi_po_button="<input type='button' value='View' onClick=\"generate_order_report('".$row[csf('po_id')]."','".$row[csf('company_id')]."','".$row[csf('job_no')]."','".$row[csf('buyer_name')]."','".$row[csf('style_ref_no')]."','show_po_listview_report','2')\" style='width:50px;' name='print_po' id='print_po' class='formbutton' />";
						}
						else
						{
							$orgi_po_button=$orgi_shipment_date;	
						}
						$ex_fact_date=implode(",",array_unique(explode(",",$ex_fact_date)));
						$ex_fact_date_row=count(array_unique(explode(",",$ex_fact_date)));
						if($ex_fact_date_row>1)
						{
							
							 $exf_po_button="<input type='button' value='View' onClick=\"generate_order_report('".$row[csf('po_id')]."','".$row[csf('company_id')]."','".$row[csf('job_no')]."','".$row[csf('buyer_name')]."','".$row[csf('style_ref_no')]."','show_exf_po_listview_report','3')\" style='width:50px;' name='print_po' id='print_po' class='formbutton' />";
						}
						else
						{
							$exf_po_button=$ex_fact_date;	
						}
						$lab_dip_id=implode(",",array_unique(explode(",",$lab_dip_no)));
						$lab_dip_id_row=count(array_unique(explode(",",$lab_dip_no)));
						if($lab_dip_id_row>1)
						{
							
							 $lab_button="<input type='button' value='View' onClick=\"generate_order_report('".$row[csf('po_id')]."','".$row[csf('company_id')]."','".$row[csf('job_no')]."','".$row[csf('buyer_name')]."','".$row[csf('style_ref_no')]."','show_labdip_listview_report','4')\" style='width:50px;' name='print_po' id='print_po' class='formbutton' />";
						}
						else
						{
							$lab_button=$lab_dip_id;	
						}
						//echo rtrim($fab_des,',');
						// $fab_des=substr($fab_des,0,-1);
						//$fab_desc=implode(",",array_unique(explode(",",$fab_des)));
						 //count($fab_desd);
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
							<td width="40"><? echo $i; ?></td>
							<td width="50"><? echo $job_year_con; ?></td>
							<td width="90"  align="center"><p><? echo $job_mon_con; ?></p></td>
							<td width="110" ><div style="word-break:break-all"><? echo $team_leader_library
	[$row[csf('team_leader')]]; ?></div></td>
							<td width="110"><div style="word-break:break-all"><? echo $dealing_merchant_array[$row[csf('dealing_marchant')]]; ?></div></td>
                            <td width="120"><div style="word-wrap:break-word:120px;"><? echo $company_library[$row[csf('style_owner')]]; ?></div></td>
							<td width="100"><div style="word-break:break-all"><? echo $product_dept[$row[csf('product_dept')]]; ?></div></td>
							<td width="100"><div style="word-break:break-all"><? echo $row[csf('job_prfix')]; ?></div></td>
                            <td width="100"><div style="word-break:break-all"><? echo $row[csf('style_ref_no')]; ?></div></td>
							<td width="100" align="center" title="<? echo $row[csf('job_no')];?>"> <p> <? echo $po_button; ?>   </p>
							<td width="80" align="center" ><div style="word-break:break-all"> <? echo $po_received_date_con;// echo number_format($job_qty,2); ?>
                            </div>
							</td>
							<td width="80" align="center" ><div style="word-break:break-all"><? 
							$revised_date_recv=explode(" ",$revised_date_arr[$row[csf('po_id')]]['recv_date']);
							echo change_date_format($revised_date_recv[0]);
							//echo $revised_date_arr[$row[csf('po_id')]]['recv_date']; ?></div></td>
							<td width="100" align="left"><div style="word-break:break-all"><? echo $buyer_library[$row[csf('buyer_name')]];?> </div></td>
							<td width="80"   align="center" ><div style="word-break:break-all">
							<? echo $yarn_counts;?>
                            </div>
							</td>
							<td width="80" align="center"><div style="word-break:break-all">
							 <? echo $season_name_library[$row[csf('season_matrix')]];?>
                             </div>
							</td>
							<td width="120" align="left">
                            <div style="word-break:break-all">
							<?
							$gmts_item='';$body_item=''; $gsm_item=''; $gmts_item_id=array_unique(explode(",",$row[csf('gmts_item_id')]));
							foreach($gmts_item_id as $item_id)
							{
								if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
								if($body_item=="") $body_item=$body_part[$pre_cost_data_arr[$row[csf('job_no')]][$item_id]['body']]; else $body_item.=",".$body_part[$pre_cost_data_arr[$row[csf('job_no')]][$item_id]['body']];
								if($gsm_item=="") $gsm_item=$pre_cost_data_arr[$row[csf('job_no')]][$item_id]['gsm']; else $gsm_item.=",".$pre_cost_data_arr[$row[csf('job_no')]][$item_id]['gsm'];
								
							}
									echo $gmts_item; 
									$order_qty_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
									?>
                                    </div>
							</td>
							<td width="50" align="center"><div style="word-break:break-all"><? echo  $shipment_mode[$row[csf('ship_mode')]]; ?></div></td>
							<td width="150"  align="center"><div style="word-break:break-all"><? echo  $fab_des; ?></div></td>
							<td width="100" ><div style="word-break:break-all"><? echo $fabric_booking_data_arr[$row[csf('po_id')]]['booking_no']; ?></div></td>
							<td width="100" align="center"><div style="word-break:break-all"><? echo  $fab_gsm;//implode(",",array_unique(explode(",",$fab_gsm))); ?></div></td>
							<td width="100" align="right"><div style="word-break:break-all"><? echo $row[csf('po_quantity')]; ?></div></td>
							<td width="50" align="center"><div style="word-break:break-all"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></div></td>
							<td width="80" align="right"> <p> <? echo $order_qty_pcs; ?>   </p>
							<td width="60" align="right" > <? echo $row[csf('ratio')]; ?>  </td>
							<td width="80" align="right" ><p><? echo  $ex_fact_qty; ?></p></td>
							<td width="80" align="right"> <? echo  $short_excess_qty; ?></td>
							<td width="80"   align="center"><? echo $orgi_po_button; ?></td>
							<td width="80" align="center"><div style="word-break:break-all"><? 
							$revised_shipdate=explode(" ",$revised_date_arr[$row[csf('po_id')]]['ship_date']);
							echo change_date_format($revised_shipdate[0]);
							//echo $revised_date_arr[$row[csf('po_id')]]['ship_date']; ?> </div></td>
							<td width="80" align="center"> <div style="word-break:break-all"> <? echo $exf_po_button; ?> </div></td>
							<td width="60" align="right"><? echo number_format($row[csf('unit_price')],4); ?></td>
							<td width="100" align="center"><div style="word-break:break-all"><? $lcnos=implode(",",array_unique(explode(",",$lc_nos)));echo rtrim($lcnos,','); ?></div></td>
							<td width="70" align="right"><div style="word-break:break-all"><? echo $cm_margin_dzn; ?></div></td>
							<td width="60" align="center" title="Ex-Factory Date-Pub Shipment Date"><div style="word-break:break-all"><? echo number_format($date_diff);?></div></td>
							<td width="60" align="center"><div style="word-break:break-all"><? 
							if($date_diff>0)
							{
								$early_status='Delay'; 
							}
							if($date_diff<0)
							{
								$early_status='Early';
							}
							if($date_diff==0)
							{
								$early_status='Ontime';
							}
							if($date_diff=='')
							{
								$early_status='';
							}
							echo $early_status; 
							 ?></div></td>
							<td width="100" align="right" title="PO Qty*Unit Price"><div style="word-break:break-all"><? $tot_fob_price=$row[csf('po_quantity')]*$row[csf('unit_price')]; echo number_format($tot_fob_price,4);?></div></td>
							<td width="100" align="right" title="Ex-Factory Qty*Unit Price"> <p> <? $tot_ex_fact_val=$ex_fact_qty*$row[csf('unit_price')];echo number_format($tot_ex_fact_val,2); ?>   </p>
							<td width="100" title="Short Excess Qty*Unit Price" align="right"><? $tot_short_excess_val=$short_excess_qty*$row[csf('unit_price')]; echo number_format($tot_short_excess_val,2); ?> </td>
							<td width="80" title="CM US/12*PO Qty" align="right" ><p><? echo number_format($cm_margin_val,2); ?></p></td>
							<td width="70" align="center"> <? echo $quality_label[$row[csf('qlty_label')]]; ?></td>
							<td width="80"  align="center"><? echo $ship_status_con; ?></td>
							
							<td width="70" align="right"><p> <? echo number_format($ex_fact_carton_qty); ?>  </p></td>
							<td width="80" align="right" > <? echo number_format($row[csf('up_charge')],2); ?>  </td>
							<td width="80" align="center" ><p><? echo $lab_button; ?></p></td>
							<td width="100" align="right"><p><? echo $sewing_data_array[$row[csf('po_id')]]['sewing_com']; ?> </p></td>
							<td width="80"   align="center"><?
							//$ex_fact_date=$exfactory_data_array[$row[csf('po_id')]][ex_factory_date];
							 if($exf_mon_con!='')
							 {
							 echo $exf_mon_con;
							 }
							 else echo '';
							 
							 ?> </td>
							<td width="80" align="right"><p><? echo number_format($fab_finish_req,2); ?></p> </td>
						  
							<td width=""  align="right"><? echo number_format($row[csf('set_smv')],2); ?></td>
						   
							
						</tr>
					<?
					$total_po_qty+=$row[csf('po_quantity')];
					$total_ex_fact_qty+=$ex_fact_qty;
					$total_tot_fob_price+=$tot_fob_price;
					$total_tot_ex_fact_val+=$tot_ex_fact_val;
					$total_cm_margin_val+=$cm_margin_val;
					
					$total_up_charge+=$row[csf('up_charge')];
					$total_fab_finish_req+=$fab_finish_req;
					$i++;
					}
					?>
					</table>
					<table class="rpt_table" width="4010" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
						<tfoot>
							<th width="40"></th>
							<th width="50"></th>
							<th width="90"></th>
							<th width="110"></th>
							<th width="110"></th>
                            <th width="120"></th>
                            <th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="80" id=""> </th>
							<th width="80"></th>
							<th width="100" id=""></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="120"> </th>
							<th width="50"> </th>
							<th width="150"></th>
							<th width="100"></th>
							<th width="100">Total</th>
							<th width="100" id="value_total_order_qty"><? echo number_format($total_po_qty); ?></th>
							<th width="50"></th>
							<th width="80"></th>
							<th width="60"></th>
							<th width="80" align="right" id="value_total_ex_fact_qty"> <? echo number_format($total_ex_fact_qty); ?></th>
							<th width="80"><? //echo number_format($total_tot_ex_fact_val); ?></th>
							<th width="80"></th>
							<th width="80"><? //echo number_format($total_tot_ex_fact_val); ?></th>
							<th width="80"></th>
							<th width="60"> </th>
							<th width="100"> </th>
							<th width="70"> </th>
							<th width="60"></th>
							<th width="60"></th>
							<th width="100" id="value_total_fob_price"><? echo number_format($total_tot_fob_price,4);//echo $total_cm_margin_val; ?></th>
							<th width="100" id="value_total_ex_fact_val"><? echo number_format($total_tot_ex_fact_val,2); ?></th>
							<th width="100"></th>
							<th width="80" id="value_total_cm_margin_val"><? echo number_format($total_cm_margin_val,2); ?></th>
							<th width="70"></th>
							<th width="80"><? //echo $total_up_charge; ?></th>
							
							<th width="70"></th>
							<th width="80" id="value_total_up_charge"><? echo number_format($total_up_charge,2); ?></th>
							<th width="80"></th>
							<th width="100"></th>
							<th width="80"></th>
							<th width="80" id="value_total_fab_finish_req"><? echo number_format($total_fab_finish_req,2); ?></th>
							<th width=""></th>
						
						</tfoot>
					</table>
				</div>
				</fieldset>
			</div>
	<?
		}
	}
	else if($report_type==4)
	{
			// actual po wise 
			if($template==1)
			{
				$revised_date_arr=array();$exfactory_data_array=array();$pre_cost_data_arr=array();
				$fabric_booking_data_arr=array();$fabric_des_data_arr=array();$pre_cost_cm_margin_arr=array();		
				$export_lc_data_array=array();$sewing_data_array=array();$lab_dip_data_arr=array();		
				
				$sql_update_history=sql_select("SELECT c.po_break_down_id as po_id,c.country_ship_date,min(c.country_ship_date_prev) as orig_ship_date,max(c.country_ship_date) as shipment_date from wo_po_details_master a, wo_po_break_down b ,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and c.po_break_down_id=b.id and a.job_no=c.job_no_mst and a.company_name='$company_name' and a.status_active=1 and c.order_quantity>0 and
					a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $c_date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond 
					$order_status_cond  $internal_ref_cond $file_no_cond $season_cond $style_owner_cond $team_leader_cond $dealing_marchant_cond group by c.po_break_down_id,c.country_ship_date order by c.po_break_down_id");
				
				foreach($sql_update_history as $row)
				{
					$revised_date_arr[$row[csf('po_id')]][$row[csf('country_ship_date')]]['recv_date']=$row[csf('po_received_date')];
					$revised_date_arr[$row[csf('po_id')]][$row[csf('country_ship_date')]]['lship_date']=$row[csf('shipment_date')];
					$revised_date_arr[$row[csf('po_id')]][$row[csf('country_ship_date')]]['forig_date']=$row[csf('orig_ship_date')];
					
				}
				unset($sql_update_history);
				if($db_type==2)
				{
					$lab_grp="listagg(CAST(b.lapdip_no as VARCHAR(4000)),',') within group (order by b.lapdip_no) as lab_id";
					$count_grp="listagg(CAST(b.count_id as VARCHAR(4000)),',') within group (order by b.count_id) as count_id";
					$grop_fab="listagg(CAST(b.gsm_weight as VARCHAR(4000)),',') within group (order by b.gsm_weight) as gsm_weight,listagg(CAST(b.construction as VARCHAR(4000)),',') within group (order by b.construction) as descrp,listagg(CAST(b.lib_yarn_count_deter_id as VARCHAR(4000)),',') within group (order by b.lib_yarn_count_deter_id) as deter_id";
				
					$grop_country="listagg(CAST(c.country_id as VARCHAR(4000)),',') within group (order by c.country_id) as country_id";
				}
				else
				{
					$lab_grp="group_concat(b.lapdip_no) as lab_id";
					$count_grp="group_concat(b.count_id) as count_id";
					$grop_fab="group_concat(b.gsm_weight) as gsm_weight,group_concat(b.construction) as descrp,group_concat(b.lib_yarn_count_deter_id) as deter_id";
					$grop_country="group_concat(c.country_id) as country_id";
				}
				
				$sql_yarn=sql_select("SELECT a.id,a.construction,a.gsm_weight,b.count_id from lib_yarn_count_determina_mst a,lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active =1 and a.is_deleted=0 group by a.id,a.construction,a.gsm_weight,b.count_id ");
				
				foreach($sql_yarn as $row)
				{
					$fabric_des_data_arr[$row[csf('id')]]['des']=$row[csf('construction')];
					$fabric_des_data_arr[$row[csf('id')]]['gsm']=$row[csf('gsm_weight')];
					$fabric_des_data_arr[$row[csf('id')]]['count']=$row[csf('count_id')];
				}
				unset($sql_yarn);
				
				 

				$sql_result="SELECT d.acc_ship_date, d.id as actual_po_id ,d.acc_po_no as po_number, a.company_name as company_id, a.job_no_prefix_num as job_prefix, a.team_leader, a.qlty_label, a.dealing_marchant, a.product_dept, a.season_buyer_wise as season_matrix, b.insert_date, a.job_no, a.buyer_name, a.client_id, a.style_ref_no, a.style_owner, b.is_confirmed, b.shiping_status, b.matrix_type, a.set_smv, a.avg_unit_price, 
				a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, a.job_quantity, a.total_price, b.id as po_id, b.po_number as initial_po, b.pub_shipment_date, b.po_received_date, b.shipment_date, (b.up_charge) as up_charge,
				sum(distinct b.po_quantity) as po_quantity, sum(b.po_total_price) as po_total_price, b.grouping, b.file_no, sum(c.order_quantity/a.total_set_qnty) as  order_quantity, sum(c.order_quantity) as order_quantity_pcs, c.country_ship_date, sum(c.order_total) as order_total, $grop_country
				from wo_po_details_master a, wo_po_break_down b ,wo_po_color_size_breakdown c,wo_po_acc_po_info d
				where a.job_no=b.job_no_mst and c.po_break_down_id=b.id and a.job_no=c.job_no_mst and c.po_break_down_id=d.po_break_down_id and d.status_active=1 and a.company_name='$company_name' and a.status_active=1 and c.order_quantity>0 and
				a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_cond4 $c_date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond 
				$order_status_cond  $internal_ref_cond $file_no_cond $season_cond $style_owner_cond $team_leader_cond $dealing_marchant_cond group by d.acc_ship_date, d.id,d.acc_po_no,  a.company_name, a.job_no_prefix_num, a.team_leader, a.qlty_label, a.dealing_marchant, a.product_dept, a.season_buyer_wise , b.insert_date, a.job_no, a.buyer_name, a.client_id,a.style_ref_no, a.style_owner, b.is_confirmed, b.shiping_status, b.matrix_type, a.set_smv, a.avg_unit_price, 
				a.order_uom, a.gmts_item_id, a.total_set_qnty, a.job_quantity, a.total_price, b.id, b.po_number, b.pub_shipment_date,b.up_charge, b.po_received_date, b.shipment_date, b.grouping, b.file_no, c.country_ship_date order by b.id";
				//echo $sql_result;die;

				$extra_cond="";
				$actual_po_val=trim(str_replace("'", "", $txt_actual_po));
				if($actual_po_val)
					$extra_cond.=" and c.acc_po_no like '%$actual_po_val%' ";
				$actual_po_id=str_replace("'", "", $txt_actual_po_id);

				//if($actual_po_id)
					//$extra_cond.=" and c.id='$actual_po_id' ";

				$actual_po_sql=" SELECT b.id as po_id ,c.id,c.acc_po_no,c.acc_po_qty as qty
				from wo_po_details_master a, wo_po_break_down b ,wo_po_acc_po_info c
				where a.job_no=b.job_no_mst and c.po_break_down_id=b.id   and a.company_name='$company_name' and a.status_active=1   and
				a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond 
				$order_status_cond  $internal_ref_cond $file_no_cond $season_cond $style_owner_cond $team_leader_cond $dealing_marchant_cond $extra_cond
				";
				//$c_date_cond will be actual po ship date
				$po_and_actual_po_wise=array();
				$po_wise_arr=array();

				foreach(sql_select($actual_po_sql) as $val)
				{
					$po_and_actual_po_wise[$val[csf("po_id")]][$val[csf("id")]]["name"]=$val[csf("acc_po_no")];
					$po_and_actual_po_wise[$val[csf("po_id")]][$val[csf("id")]]["qty"]+=$val[csf("qty")];				 
					$po_wise_arr[$val[csf("po_id")]]+=$val[csf("qty")];
				}
				//echo "<pre>";print_r($po_wise_arr);die;

				$result_data=sql_select($sql_result); 
				$country_arr=array();$all_po_id='';
				foreach($result_data as $row)
				{
					$country_arr[$row[csf('job_no')]]['po_qty']+=$row[csf('job_quantity')];
					$country_shipdate_arr[$row[csf('po_id')]][$row[csf('country_ship_date')]]['po_qty']+=$row[csf('order_quantity')];
					$po_country_arr[$row[csf('po_id')]]['po_qty']+=$row[csf('po_quantity')];
					if($all_po_id=="") $all_po_id=$row[csf("po_id")]; else $all_po_id.=",".$row[csf("po_id")];
				}
				$tot_rows_po=count($result_data); 
				$all_po=array_unique(explode(",",$all_po_id));
				$po_arr_cond=array_chunk($all_po,1000, true);
				$po_cond_for_in="";$po_cond_for_in2="";$po_cond_for_in3="";$po_cond_for_in4="";$po_cond_for_in5="";$po_cond_for_in6="";$po_cond_for_in7="";
				$poIds=chop($all_po_id,',');
				$po_ids=count(array_unique(explode(",",$all_po_id)));
				if($db_type==2 && $po_ids>1000)
				{
					$po_cond_for_in=" and (";
					$po_cond_for_in2=" and (";
					$po_cond_for_in3=" and (";
					$po_cond_for_in4=" and (";
					$po_cond_for_in5=" and (";
					$po_cond_for_in6=" and (";
					$po_cond_for_in7=" and (";

					$poIdsArr=array_chunk(explode(",",$poIds),999);
					foreach($poIdsArr as $ids)
					{
						$ids=implode(",",$ids);
	 					$po_cond_for_in.=" c.po_break_down_id in($ids) or"; 
						$po_cond_for_in2.=" d.po_break_down_id in($ids) or"; 
						$po_cond_for_in3.=" b.id in($ids) or"; 
						$po_cond_for_in4.=" b.po_breakdown_id in($ids) or"; 
						$po_cond_for_in5.=" po_break_down_id in($ids) or"; 
						$po_cond_for_in6.=" b.po_break_down_id in($ids) or"; 
						$po_cond_for_in7.=" b.wo_po_break_down_id in($ids) or"; 
					}
					$po_cond_for_in=chop($po_cond_for_in,'or ');
					$po_cond_for_in.=")";
					$po_cond_for_in2=chop($po_cond_for_in2,'or ');
					$po_cond_for_in2.=")";
					$po_cond_for_in3=chop($po_cond_for_in3,'or ');
					$po_cond_for_in3.=")";
					$po_cond_for_in4=chop($po_cond_for_in4,'or ');
					$po_cond_for_in4.=")";
					$po_cond_for_in5=chop($po_cond_for_in5,'or ');
					$po_cond_for_in5.=")";
					$po_cond_for_in6=chop($po_cond_for_in6,'or ');
					$po_cond_for_in6.=")";
					$po_cond_for_in7=chop($po_cond_for_in7,'or ');
					$po_cond_for_in7.=")";
				}
				else
				{
					$po_cond_for_in=" and c.po_break_down_id in($poIds)";
					$po_cond_for_in2=" and d.po_break_down_id  in($poIds)";
	 				$po_cond_for_in3=" and b.id in($poIds)";
					$po_cond_for_in4=" and b.po_breakdown_id  in($poIds)";
					$po_cond_for_in5=" and po_break_down_id in($poIds)";
					$po_cond_for_in6=" and b.po_break_down_id in($poIds)";
					$po_cond_for_in7=" and b.wo_po_break_down_id in($poIds)";
				}
							
				$sql_invo_exp="SELECT b.po_breakdown_id as po_id,a.country_id,max(a.invoice_date) as invoice_date,
				sum(CASE WHEN a.shipping_mode=1 THEN b.current_invoice_qnty ELSE 0 END) as sea_qnty,
				sum(CASE WHEN a.shipping_mode=2 THEN b.current_invoice_qnty ELSE 0 END) as air_qnty,
				sum(CASE WHEN a.shipping_mode=1 THEN b.current_invoice_value ELSE 0 END) as sea_val,
				sum(CASE WHEN a.shipping_mode=2 THEN b.current_invoice_value ELSE 0 END) as air_val
				from  com_export_invoice_ship_mst a,com_export_invoice_ship_dtls b where a.id=b.mst_id  and a.shipping_mode in(1,2)  and a.status_active ='1' and a.is_deleted ='0' $po_cond_for_in4  group by b.po_breakdown_id,a.country_id";
				$sql_inv_exp_res=sql_select($sql_invo_exp);
				foreach($sql_inv_exp_res as $row)
				{
					$invoice_data_array[$row[csf('po_id')]][$row[csf('country_id')]][1]['sea_qnty']+=$row[csf('sea_qnty')];
					$invoice_data_array[$row[csf('po_id')]][$row[csf('country_id')]][2]['air_qnty']+=$row[csf('air_qnty')];
				}
				
				$sql_yarn = "SELECT a.sequence_no,a.id,c.yarn_count,a.construction,b.copmposition_id  from  lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b,lib_yarn_count c where a.id=b.mst_id and b.count_id=c.id  order by a.id, a.sequence_no";
				$data_arr_ycount=sql_select($sql_yarn);
				foreach($data_arr_ycount as $row)
				{
					$composition1=$composition[$row[csf("copmposition_id")]];
					$fab_desc=$composition1.'100%';
					$fab_composition_arr[$row[csf("id")]]['desc']=$fab_desc;

				}
				 
				$sql_pre=sql_select("SELECT c.po_break_down_id as po_id,b.job_no,b.gsm_weight,b.construction as descrp,b.composition,b.lib_yarn_count_deter_id as deter_id,d.margin_dzn as margin_dzn, d.cm_cost from wo_po_details_master a,wo_pre_cost_fabric_cost_dtls b,wo_pre_cos_fab_co_avg_con_dtls c,wo_pre_cost_dtls d where a.job_no=b.job_no and c.pre_cost_fabric_cost_dtls_id=b.id and c.job_no=a.job_no and d.job_no=a.job_no  and d.job_no=b.job_no  and  c.cons>0 and a.company_name='$company_name' and b.body_part_id in($body_id) $buyer_id_cond $job_no_cond  $po_cond_for_in  group by c.po_break_down_id,b.job_no,d.margin_dzn, d.cm_cost,b.gsm_weight,b.construction,b.lib_yarn_count_deter_id,b.composition");
				
				foreach($sql_pre as $row)
				{
					$fab_comosition=$fab_composition_arr[$row[csf("deter_id")]]['desc'];
					$pre_cost_data_arr[$row[csf('po_id')]]['deter_id'].=$row[csf('deter_id')].',';
					$pre_cost_data_arr[$row[csf('po_id')]]['gsm'].=$row[csf('gsm_weight')].',';
					$pre_cost_data_arr[$row[csf('po_id')]]['fab_des'].=$row[csf('descrp')].',';
					$pre_cost_data_arr[$row[csf('po_id')]]['fab_composiiton'].=$row[csf('composition')].',';
					$pre_cost_cm_margin_arr[$row[csf('job_no')]]['margin']=$row[csf('margin_dzn')];
					$pre_cost_cm_margin_arr[$row[csf('job_no')]]['cm']=$row[csf('cm_cost')];
				}
				unset($sql_pre);
						
				$sql_book=sql_select("SELECT e.uom, b.entry_form,b.booking_type,b.item_category,b.is_approved,b.fabric_source,b.is_short,b.supplier_id,b.booking_no,b.pay_mode,c.fin_fab_qnty,c.grey_fab_qnty,c.po_break_down_id as po_id from wo_po_details_master a,wo_booking_mst b,wo_booking_dtls c,wo_pre_cost_fabric_cost_dtls e where a.job_no=c.job_no   and  e.id=c.pre_cost_fabric_cost_dtls_id and  a.job_no=e.job_no and c.booking_no=b.booking_no  and b.booking_type=1 and b.is_short in(1,2) and a.company_name='$company_name' and c.status_active=1 and c.is_deleted=0  $buyer_id_cond $job_no_cond $po_cond_for_in");


				foreach($sql_book as $row)
				{
					$fabric_booking_data_arr[$row[csf('po_id')]]['fin_qnty']+=$row[csf('grey_fab_qnty')];
					$fabric_booking_data_arr_uom_wise[$row[csf('po_id')]][$row[csf('uom')]]['fin_qnty']+=$row[csf('grey_fab_qnty')];
					$fabric_booking_data_arr[$row[csf('po_id')]]['booking_no'].=$row[csf('booking_no')].',';


					if($row[csf('pay_mode')]==5 || $row[csf('pay_mode')]==3)
					{
						$supplier_company=$company_library[$row[csf('supplier_id')]];
					}
					else
					{
						$supplier_company=$supplier_library[$row[csf('supplier_id')]];
					}
					$fabric_booking_data_arr[$row[csf('po_id')]]['supplier_id'].=$supplier_company.',';

					$fabric_booking_data2_arr[$row[csf('booking_no')]]['item_category']=$row[csf('item_category')];
					$fabric_booking_data2_arr[$row[csf('booking_no')]]['fabric_source']=$row[csf('fabric_source')];
					$fabric_booking_data2_arr[$row[csf('booking_no')]]['is_short']=$row[csf('is_short')];
					$fabric_booking_data2_arr[$row[csf('booking_no')]]['entry_form']=$row[csf('entry_form')];
					$fabric_booking_data2_arr[$row[csf('booking_no')]]['booking_type']=$row[csf('booking_type')];
					$fabric_booking_data2_arr[$row[csf('booking_no')]]['is_approved']=$row[csf('is_approved')];

				}
				unset($sql_book);
				$sql_lab=sql_select("Select b.po_break_down_id as po_id,$lab_grp from wo_po_lapdip_approval_info b where b.status_active =1 and b.is_deleted=0 $po_cond_for_in6 group by b.po_break_down_id");
				
				foreach($sql_lab as $row)
				{
					$lab_dip_data_arr[$row[csf('po_id')]]['ldip_id']=$row[csf('lab_id')];
				}
				unset($sql_lab);
				$sql_sewing=sql_select("Select b.serving_company,b.production_source,b.po_break_down_id as po_id,b.country_id from pro_garments_production_mst b where b.company_id='$company_name' and b.production_type=4 and b.status_active =1 and b.is_deleted=0 $po_cond_for_in6");
				foreach($sql_sewing as $row)
				{
					if($row[csf('production_source')]==1) $sewing_com=$company_library[$row[csf('serving_company')]]; else if($row[csf('production_source')]==3) $sewing_com=$supplier_library[$row[csf('serving_company')]];
					$sewing_data_array[$row[csf('po_id')]][$row[csf('country_id')]]['sewing_com'].=$sewing_com.',';
				}
				unset($sql_sewing);
				
				$sql_lc=sql_select("select a.export_lc_no,b.wo_po_break_down_id as po_id from  com_export_lc a,com_export_lc_order_info b where a.id=b.com_export_lc_id and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 $po_cond_for_in7" );
				$export_lc_data_array=array();
				foreach($sql_lc as $row)
				{
					$export_lc_data_array[$row[csf('po_id')]][lc_no].=$row[csf('export_lc_no')].',';
				}
				unset($sql_lc);
				$sql_sc=sql_select("select a.contract_no,b.wo_po_break_down_id as po_id from  com_sales_contract a,com_sales_contract_order_info b where a.id=b.com_sales_contract_id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 $po_cond_for_in7");
				$export_sc_data_array=array();
				foreach($sql_sc as $row)
				{
					$export_sc_data_array[$row[csf('po_id')]][sc_no].=$row[csf('contract_no')].',';
				}
				unset($sql_sc);
				$sql_invo="SELECT b.actual_po, b.po_break_down_id as po_id,b.country_id,max(b.ex_factory_date) as factory_date,
				sum(CASE WHEN b.shiping_mode=1 THEN b.ex_factory_qnty ELSE 0 END) as sea_qnty,
				sum(CASE WHEN b.shiping_mode=2 THEN b.ex_factory_qnty ELSE 0 END) as air_qnty,
				sum(CASE WHEN b.entry_form!=85 THEN b.total_carton_qnty ELSE 0 END) as total_carton_qnty,
				sum(CASE WHEN  b.entry_form=85 THEN b.total_carton_qnty ELSE 0 END) as total_carton_qnty_ret
				from  pro_ex_factory_mst b where  b.shiping_mode in(1,2)  and b.status_active=1 and b.is_deleted =0  $po_cond_for_in6 group by b.actual_po, b.po_break_down_id,b.country_id order by factory_date";

				$export_invoice_arr=array();
				$sql_inv_res=sql_select($sql_invo);
				foreach($sql_inv_res as $row)
				{
					$export_invoice_arr[$row[csf('po_id')]][$row[csf('actual_po')]][$row[csf('country_id')]][1]['sea']+=$row[csf('sea_qnty')];
					$export_invoice_arr[$row[csf('po_id')]][$row[csf('actual_po')]][$row[csf('country_id')]][2]['air']+=$row[csf('air_qnty')];
					$exfactory_data_array[$row[csf('po_id')]][$row[csf('actual_po')]][$row[csf('country_id')]]["carton_qty"]+=$row[csf('total_carton_qnty')]-$row[csf('total_carton_qnty_ret')];
					$export_invoice_date_arr[$row[csf('po_id')]][$row[csf('actual_po')]]['date']=$row[csf('factory_date')];
				}
				unset($sql_inv_res);
				$sql_fab_book= "SELECT d.booking_no,b.id as po_id,(d.amount) as amount,c.booking_type,c.entry_form,d.grey_fab_qnty,d.fin_fab_qnty,d.wo_qnty,d.exchange_rate as exchange_rate
				from wo_po_details_master a, wo_po_break_down b, wo_booking_mst c,wo_booking_dtls d  where a.job_no=b.job_no_mst and a.company_name='$company_name'  and b.id=d.po_break_down_id and d.booking_no=c.booking_no  and b.status_active=1 and    b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and d.status_active=1  and c.short_booking_type not in(2,3) and d.is_deleted=0  and d.amount>0   $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond 
				$order_status_cond  $internal_ref_cond $file_no_cond $season_cond $style_owner_cond $team_leader_cond $dealing_marchant_cond $po_cond_for_in3";
				$book_result=sql_select($sql_fab_book);
				$po_wo_cost_arr=array();
				foreach($book_result as $row)
				{
					$booking_type=$row[csf('booking_type')];
					if($booking_type==1 || $booking_type==4)
					{
							if($row[csf('entry_form')]!=108) //Fabric Booking
							{
								$fab_qnty=$row[csf('grey_fab_qnty')];
								if($fab_qnty==0 || $fab_qnty=='')
								{
									$avg_rate=0; $amount=0; $fab_qnty=0;
								}
								$avg_rate=number_format($row[csf('amount')]/$fab_qnty,6,'.','');
								if($avg_rate=='inf') $avg_rate=0;else $avg_rate=$avg_rate;
								$amount=$fab_qnty*$avg_rate;
							}
							else
							{
								$fab_qnty=$row[csf('fin_fab_qnty')];
								if($fab_qnty==0 || $fab_qnty=='')
								{
									$avg_rate=0; $amount=0;  $fab_qnty=0;
								}

								$avg_rate=$row[csf('amount')]/$fab_qnty;
								if($avg_rate=='inf') $avg_rate=0;else $avg_rate=$avg_rate;
								$amount=$fab_qnty*$avg_rate;
							}
						}
						else if($booking_type==2 || $booking_type==5) //Trims Booking
						{
							if($row[csf('wo_qnty')]==0 || $row[csf('wo_qnty')]=='')
							{
								$avg_rate=0; $amount=0; 
							}
							$avg_rate=$row[csf('amount')]/$row[csf('wo_qnty')];
							if($avg_rate=='inf') $avg_rate=0;else $avg_rate=$avg_rate;
							$rate=($avg_rate/$row[csf('exchange_rate')]);
							$amount=number_format($row[csf('wo_qnty')]*$rate,6,'.','');// number_format($avg_rate,'.','');
							
						}
						else
						{
							$amount=$row[csf('amount')];
						}
						$po_wo_cost_arr[$row[csf('po_id')]]['amount']+=$amount;
					}
					unset($book_result);

					$sql_lab_book= "SELECT b.id as po_id,sum(e.wo_value) as amount
					from wo_po_details_master a, wo_po_break_down b, wo_labtest_mst c,wo_labtest_dtls d,wo_labtest_order_dtls e  where a.job_no=b.job_no_mst and a.company_name='$company_name'  and a.job_no=d.job_no and c.id=d.mst_id and e.dtls_id=d.id and e.order_id=b.id and c.status_active=1 and  c.is_deleted=0 $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond 
					$order_status_cond $po_cond_for_in3 $internal_ref_cond $file_no_cond $season_cond $style_owner_cond $team_leader_cond $dealing_marchant_cond  group by b.id";//die;
					$lab_result=sql_select($sql_lab_book);
					$lab_po_data_cost_arr=array();
					foreach($lab_result as $row)
					{
						$lab_po_data_cost_arr[$row[csf('po_id')]]['lab']+=$row[csf('amount')];
					}
					unset($lab_result);
					$sql_mrr_recv= "select a.recv_number,b.id as trans_id,f.issue_qnty,f.issue_trans_id,b.order_rate,b.order_ile,e.product_name_details,e.item_group_id
					from  inv_receive_master a, inv_transaction b,product_details_master e,inv_mrr_wise_issue_details f  where a.id=b.mst_id and e.id=b.prod_id  and f.recv_trans_id=b.id and b.transaction_type=1 and a.entry_form=20 and a.company_id=$company_name  and b.status_active=1 and  b.is_deleted=0  ";
					$mrr_recv_result=sql_select($sql_mrr_recv);
					$mrr_recv_arr=array();
					foreach($mrr_recv_result as $row)
					{
						$mrr_recv_arr[$row[csf('trans_id')]]['rate']=$row[csf('order_rate')]+$row[csf('order_ile')];
					}
					unset($mrr_recv_result);
					
					$sql_gen_acc= "select b.id as po_id,f.recv_trans_id,e.item_group_id,
					sum(d.cons_amount) as amount,sum(d.cons_quantity) as cons_quantity,sum(f.issue_qnty) as issue_qnty
					from wo_po_details_master a, wo_po_break_down b, inv_issue_master c,inv_transaction d,product_details_master e,inv_mrr_wise_issue_details f   where a.company_name='$company_name' and a.job_no=b.job_no_mst  and b.id=d.order_id and c.id=d.mst_id and  f.issue_trans_id=d.id and e.id=f.prod_id  and d.transaction_type in(2) and e.id=d.prod_id and  d.status_active=1 and  d.item_category=4 and c.entry_form=21 and f.entry_form=21  and c.is_deleted=0 and d.cons_quantity>0 $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond 
					$order_status_cond  $internal_ref_cond $file_no_cond $season_cond $style_owner_cond $team_leader_cond $dealing_marchant_cond $po_cond_for_in3 group by b.id,f.recv_trans_id,e.item_group_id ";
					$gen_dzn_result=sql_select($sql_gen_acc);$gen_acces_data_cost_arr=array();
					foreach($gen_dzn_result as $row)
					{
						$converrate=$conversion_arr[$row[csf("item_group_id")]]['conver_rate'];
						$orderrate=($mrr_recv_arr[$row[csf('recv_trans_id')]]['rate']/$converrate);
						$gen_amount=$row[csf('issue_qnty')]*$orderrate;
						$gen_acces_data_cost_arr[$row[csf('po_id')]]['amt']+=$gen_amount;
					}
					unset($gen_dzn_result);
					
					
					$print_report_format_ids_partial = return_field_value("format_id","lib_report_template","template_name=".$company_name." and module_id=2 and report_id=35 and is_deleted=0 and status_active=1");
					$format_ids_partial=explode(",",$print_report_format_ids_partial);
					
					
					$print_report_format_ids_short = return_field_value("format_id","lib_report_template","template_name=".$company_name." and module_id=2 and report_id=2 and is_deleted=0 and status_active=1");
					$print_report_ids_short=explode(",",$print_report_format_ids_short);
					
					
					$print_report_format_ids2 = return_field_value("format_id","lib_report_template","template_name=".$company_name." and module_id=2 and report_id=1 and is_deleted=0 and status_active=1");
					$format_ids=explode(",",$print_report_format_ids2);
					
					ob_start(); 
				
					?>
				<div style="width:5320px">
				<fieldset style="width:100%;">	
						<table width="5320">
							<tr class="form_caption">
								<td colspan="59" align="center">OMS Report(Country Ship Date Wise)</td>
							</tr>
							<tr class="form_caption">
								<td colspan="59" align="center"><? echo $company_library[$company_name]; ?></td>
							</tr>
						</table>
						<table id="table_header_1" class="rpt_table" width="5320" cellpadding="0" cellspacing="0" border="1" rules="all">
							<thead>
								<th width="30">SL</th>
								<th width="40">Ship Year</th>
								<th width="90"><div style="word-wrap:break-word:90px;">Ord. Month</div></th>
								<th width="80"><div style="word-wrap:break-word:80px;">Team Leader</div></th>
								<th width="80"><div style="word-wrap:break-word:80px;">Deal Merchant. </div></th>
		                        <th width="80"><div style="word-wrap:break-word:80px;">Style Owner</div></th>
								<th width="100">Prod. Dept.</th>
		                        <th width="50">Job No</th>
								<th width="130"><div style="word-wrap:break-word:130px;">Style Ref.</div></th>
								<th width="100"><div style="word-wrap:break-word:100px;" title="Actual Po">Order No</div></th>
								<th width="100"><div style="word-wrap:break-word:100px;">Initial Po</div></th>
								
		                        <th width="80">Country</th>
								
								<th width="100">Buyer Name</th>
								<th width="100">Client</th>
								
								<th width="80">Season</th>
								<th width="130">GMT Item</th>
								
								<th width="150">Fabric Const.</th>
		                        <th width="200">Fabric Composition</th>
								<th width="150">Fab.Booking No</th>
		                        <th width="100">Supplier</th>
								<th width="50">GSM</th>
								<th width="70">Order Qty</th>
								<th width="50">Order UOM</th>
								<th width="80">Order Qty / Pcs</th>
								<th width="40">Set Ratio</th>
		                       
								<th width="80">Short / Excess Qty.</th>
		                        <th width="80">Actual PO Ship Date</th>
		                        <th width="80">Air Qty</th>
		                        <th width="80">Sea Qty</th>
		                        <th width="90" title="Air+Sea Qty">Total Qty</th>
								
								<th width="80">Ori. ShipDate</th>
		                         
								<th width="80">Last Revised ShipDate</th>
								<th width="80">Last Ex-Fact Date</th>
								<th width="60">Unit Price (FOB)</th>
								<th width="110">SC/LC No</th>
		                        <th width="70">Commi/DZN</th>
		                        <th width="100">Raw Material Cost/Dzn</th>
		                        
		                        <th width="70">Overhead Cost Dzn</th>
								<th width="70">CM US/DZN</th>
		                        <th width="70">Profit Except Overhead</th>
								<th width="70">Profit/Dzn based on ship</th>
								
								 <th width="60">EARLY / Delay Days</th>
								<th width="60">Early / Delay Status</th>
								<th width="70"> <div style="word-wrap:break-word:70px;">TTL Commi. </div></th>
								<th width="100">Total FOB Price</th>
								<th width="100">Ship Value</th>
								<th width="100">Short / Excess Val.</th>
		                        <th width="80">Raw material Cost</th>
								<th width="90">Total CM</th>
		                        <th width="90">Total Profit Except Overhead</th>
								<th width="90"><div style="word-wrap:break-word:100px;">TTL Profit basedOn ship</div></th>
								
								<th width="70">Qlty. Level</th>
								
								
								<th width="70">No Of Ctn. </th>
								<th width="80">UpCharge</th>
								<th width="100">LabDip No.</th>
								
								<th width="80"> <div style="word-wrap:break-word:80px;">Sew Company</div></th>
								<th width="80">Ship Month</th>
								<th width="80">Booking Qty(Kg)</th>
								<th width="100">Booking Qty(Yds)</th>
								<th width="100">Booking Qty(Mtr)</th>
								<th width="">SMV</th>
								<th width="70">Sales Minute</th>
							   
								
							</thead>
						</table>
					<div style="width:5340px; max-height:400px; overflow-y:scroll" id="scroll_body">
						<table class="rpt_table" width="5320" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
						<?
						$i=1; $total_po_qty=0; $total_ex_fact_qty=0;$total_tot_fob_price=0;$total_tot_ex_fact_val=0;		
						$total_cm_margin_val=0;	$total_qlty_qty=0;	$total_up_charge=0;	 $total_fab_finish_req=0;  $total_invoice_qty=0; 
	 					$condition= new condition();
						 $condition->company_name("=$cbo_company_name");
						 if(str_replace("'","",$cbo_buyer_name)>0){
							  $condition->buyer_name("=$cbo_buyer_name");
						 }
						 if(str_replace("'","",$txt_job_no) !=''){
							  $condition->job_no_prefix_num("=$txt_job_no");
						 }
						 if(str_replace("'","",$cbo_order_status) >0){
							  $condition->is_confirmed("=$cbo_order_status");
						 }
						 if(str_replace("'","",$cbo_order_status)==0){
							  $condition->is_confirmed("in(1,2)");
						 }
						if(str_replace("'","",$cbo_search_date) ==1 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!=''){
						 }
						  if(str_replace("'","",$cbo_search_date) ==2 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
						 {
							 $condition->po_received_date(" between '$start_date' and '$end_date'");
							
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
						$condition->init();
						
								$other= new other($condition);
						$other_costing_arr=$other->getAmountArray_by_order();
						$commission= new commision($condition);
						$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
						$total_tot_air=0;$total_tot_sea=$total_sales_minute=$total_tot_profit_base_on_ship=$total_ttl_commission_cost=0;$check_shipmon=array();$total_up_profit_except_oh=0;
						foreach($result_data as $row)
						{
							$actual_po_qty=$po_and_actual_po_wise[$row[csf("po_id")]][$row[csf("actual_po_id")]]["qty"];
							$total_actual=$po_wise_arr[$row[csf("po_id")]];
							$order_qty_pcs=$actual_po_qty;
							//$row[csf('order_quantity')]=$actual_po_qty;
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$deter_id=rtrim($pre_cost_data_arr[$row[csf('po_id')]]['deter_id'],',');
							$fabdes=rtrim($pre_cost_data_arr[$row[csf('po_id')]]['fab_des'],',');
							$fab_composiiton=rtrim($pre_cost_data_arr[$row[csf('po_id')]]['fab_composiiton'],',');
							$fab_composiiton=implode(",",array_unique(explode(",",$fab_composiiton)));
							$gsm=rtrim($pre_cost_data_arr[$row[csf('po_id')]]['gsm'],',');
							$fab_deter_id=array_unique(explode(",",$deter_id));
							$fab_des=implode(",",array_unique(explode(",",$fabdes)));
							$fab_gsm=implode(",",array_unique(explode(",",$gsm))); 
							//$actual_po_qty=$po_and_actual_po_wise[$row[csf("po_id")]][$row[csf("actual_po_id")]]["qty"];
							
							$country_ids=array_unique(explode(",",$row[csf('country_id')]));
							$country_name="";$invoice_date='';$sewing_com="";$ship_mon="";$ex_fact_carton_qty=0;$po_cm_cost=0;$invoice_date='';
							$sea_qty=0;$air_qty=0;$po_cm_cost=0;$invo_sea_qty=0;$invo_air_qty=0;
							 foreach($country_ids as $cid)
							 {  
								if($country_name=="") $country_name=$country_name_library[$cid];else $country_name.=",".$country_name_library[$cid];
								$ex_fact_carton_qty+=$exfactory_data_array[$row[csf('po_id')]][$row[csf('actual_po_id')]][$cid][carton_qty];
								$air_qty+=($export_invoice_arr[$row[csf('po_id')]][$row[csf('actual_po_id')]][$cid][2]['air']);
							
							
								$sea_qty+=($export_invoice_arr[$row[csf('po_id')]][$row[csf('actual_po_id')]][$cid][1]['sea']);
								$invo_sea_qty+=$invoice_data_array[$row[csf('po_id')]][$cid][1]['sea_qnty'];
								$invo_air_qty+=$invoice_data_array[$row[csf('po_id')]][$cid][2]['air_qnty'];
								
								if($sewing_com=="") $sewing_com=$sewing_data_array[$row[csf('po_id')]][$cid]['sewing_com'];else $sewing_com.=",".$sewing_data_array[$row[csf('po_id')]][$cid]['sewing_com'];
							 }
							$invoice_date=$export_invoice_date_arr[$row[csf('po_id')]][$row[csf('actual_po_id')]]['date'];
							$all_invoice_date=$invoice_date;
							$all_invoicedate=array_unique(explode(",",$all_invoice_date));
							
							$tot_invoice_qty=($invo_sea_qty+$invo_air_qty)/$row[csf('ratio')];
							$ship_qty_air_sea=($sea_qty+$air_qty);
							$po_cm_cost=$other_costing_arr[$row[csf('po_id')]]['cm_cost'];
							$yarn_counts="";
							 foreach($fab_deter_id as $yid)
							 {
								if($yarn_counts=="") $yarn_counts=$yarn_count_library[$fabric_des_data_arr[$yid]['count']];else $yarn_counts.=",".$yarn_count_library[$fabric_des_data_arr[$yid]['count']];
							 }
							 $ship_qty=$ship_qty_air_sea;
							 $short_excess_qty=$ship_qty_air_sea-$actual_po_qty;
							 $lab_dip_no=explode(",",$lab_dip_data_arr[$row[csf('po_id')]]['ldip_id']);
							 $lab_dip='';
							 foreach($lab_dip_no as $lap_id )
							 {
								if($lab_dip=='') $lab_dip=$lap_id;else $lab_dip.=", ".$lap_id;
							 }
								
								$date_diff='';
								foreach($all_invoicedate as $sdate)
								{
									
									if($date_diff=='') $date_diff=datediff( "d", $sdate , $row[csf('acc_ship_date')]);else $date_diff.=",".datediff( "d", $sdate , $row[csf('acc_ship_date')]);
								}
								
								$po_total_price=$row[csf('order_total')];
								$po_quantity=$row[csf('order_quantity')];
								$job_quantity=$row[csf('job_quantity')];
								$job_total_price=$row[csf('total_price')];
								$tot_po_qty=$country_arr[$row[csf('job_no')]]['po_qty'];
								$po_qty=$po_country_arr[$row[csf('po_id')]]['po_qty'];
								$country_ship_qty=$country_shipdate_arr[$row[csf('po_id')]][$row[csf('country_ship_date')]]['po_qty'] ;
								$avg_unit=($po_total_price/$po_quantity)/$row[csf('ratio')];
								$ship_val=($ship_qty*$avg_unit);
								$tot_fob_price= $actual_po_qty*$avg_unit;
								$wo_booking_cost=$po_wo_cost_arr[$row[csf('po_id')]]['amount'];
								if($wo_booking_cost=='') $wo_booking_cost=0;else $wo_booking_cost=$wo_booking_cost;
								$lab_cost=$lab_po_data_cost_arr[$row[csf('po_id')]]['lab'];
								if($lab_cost=='') $lab_cost=0;else $lab_cost=$lab_cost;
								$tot_lab_raw=((($lab_cost/$row[csf('po_quantity')])*$row[csf('order_quantity')]*$actual_po_qty)/$total_actual);
								$gen_access_cost=$gen_acces_data_cost_arr[$row[csf('po_id')]]['amt'];
								if($gen_access_cost=='') $gen_access_cost=0;else $gen_access_cost=$gen_access_cost;
								$tot_gen_access_raw=((($gen_access_cost/$row[csf('po_quantity')])*$row[csf('order_quantity')]*$actual_po_qty)/$total_actual);
								
								$fab_finish=$fabric_booking_data_arr[$row[csf('po_id')]]['fin_qnty'];

								$fab_finish_kg=$fabric_booking_data_arr_uom_wise[$row[csf('po_id')]][12]['fin_qnty'];
								$fab_finish_yds=$fabric_booking_data_arr_uom_wise[$row[csf('po_id')]][27]['fin_qnty'];
								$fab_finish_mtr=$fabric_booking_data_arr_uom_wise[$row[csf('po_id')]][23]['fin_qnty'];

								if($fab_finish=='') $fab_finish=0;else $fab_finish=$fab_finish;

								$fab_finish_req_kg=( $fab_finish_kg/$row[csf('po_quantity')])*$row[csf('order_quantity')];
								$fab_finish_req_kg=(($fab_finish_req_kg*$actual_po_qty)/$total_actual);

								$fab_finish_req_yds=( $fab_finish_yds/$row[csf('po_quantity')])*$row[csf('order_quantity')];
								$fab_finish_req_yds=(($fab_finish_req_yds*$actual_po_qty)/$total_actual);

								$fab_finish_req_mtr=( $fab_finish_mtr/$row[csf('po_quantity')])*$row[csf('order_quantity')];
								$fab_finish_req_mtr=(($fab_finish_req_mtr*$actual_po_qty)/$total_actual);

								 
								
								$foreign_comm=$commission_costing_arr[$row[csf('po_id')]][1];
								$local_comm=$commission_costing_arr[$row[csf('po_id')]][2];
								$tot_commisssion=($foreign_comm+$local_comm);
								$tot_commisssion_raw=($tot_commisssion/$row[csf('po_quantity')])*$actual_po_qty;
								$cm_cost=0;
								$cm_cost=$pre_cost_cm_margin_arr[$row[csf('job_no')]]['cm'];
								////$tot_commisssion_dzn=((($tot_commisssion_raw/$po_quantity)*$actual_po_qty)/$total_actual)*12;
								$tot_commisssion_dzn=( $tot_commisssion_raw/$actual_po_qty)*12;
								$cm_us_dzn=(($job_total_price/$job_quantity)/$row[csf('ratio')])*12;
								$tot_booking_raw=((($wo_booking_cost/$row[csf('po_quantity')])*$row[csf('order_quantity')]*$actual_po_qty)/$total_actual);
								$tot_raw_materials_cost=0;
								$actual_po_cal=($row[csf('order_quantity')]*$actual_po_qty)/$total_actual;
								$tot_raw_materials_cost=$tot_booking_raw+$tot_lab_raw+$tot_gen_access_raw;
								$tot_raw_material= ($tot_raw_materials_cost/$actual_po_qty)*12;
								
								$cm_margin_dzn=$cm_us_dzn-($tot_raw_material+$cm_cost+$tot_commisssion_dzn);
								$cm_margin_val=($cm_margin_dzn/12)*$actual_po_qty ;
								 $fab_des_button="<a href='##' onClick=\"generate_order_report('".$row[csf('po_id')]."','".$row[csf('company_id')]."','".$row[csf('job_no')]."','".$row[csf('buyer_name')]."','".$row[csf('style_ref_no')]."','".$row[csf('country_ship_date')]."','".$row[csf('country_id')]."','show_po_fabric_dtls','5')\"> ".$fab_des." </a>";
								
								 $fab_des_button2="<a href='##' onClick=\"generate_order_report('".$row[csf('po_id')]."','".$row[csf('company_id')]."','".$row[csf('job_no')]."','".$row[csf('buyer_name')]."','".$row[csf('style_ref_no')]."','".$row[csf('country_ship_date')]."','".$row[csf('country_id')]."','show_po_fabric_dtls','5')\"> ".number_format($fab_finish_req,2)." </a>";

								 $fab_des_button_kg="<a href='##' onClick=\"generate_order_report('".$row[csf('po_id')]."','".$row[csf('company_id')]."','".$row[csf('job_no')]."','".$row[csf('buyer_name')]."','".$row[csf('style_ref_no')]."','".$row[csf('country_ship_date')]."','".$row[csf('country_id')]."','show_po_fabric_dtls','5__12__".$row[csf('actual_po_id')]."')\"> ".number_format($fab_finish_req_kg,2)." </a>";

								 $fab_des_button_yds="<a href='##' onClick=\"generate_order_report('".$row[csf('po_id')]."','".$row[csf('company_id')]."','".$row[csf('job_no')]."','".$row[csf('buyer_name')]."','".$row[csf('style_ref_no')]."','".$row[csf('country_ship_date')]."','".$row[csf('country_id')]."','show_po_fabric_dtls','5__27__".$row[csf('actual_po_id')]."')\"> ".number_format($fab_finish_req_yds,2)." </a>";

								 $fab_des_button_mtr="<a href='##' onClick=\"generate_order_report('".$row[csf('po_id')]."','".$row[csf('company_id')]."','".$row[csf('job_no')]."','".$row[csf('buyer_name')]."','".$row[csf('style_ref_no')]."','".$row[csf('country_ship_date')]."','".$row[csf('country_id')]."','show_po_fabric_dtls','5__23__".$row[csf('actual_po_id')]."')\"> ".number_format($fab_finish_req_mtr,2)." </a>";
								  $ship_date_button="<a href='##' onClick=\"generate_order_report('".$row[csf('po_id')]."','".$row[csf('company_id')]."','".$row[csf('job_no')]."','".$row[csf('buyer_name')]."','".$row[csf('style_ref_no')]."','".$row[csf('country_ship_date')]."','".$row[csf('country_id')]."','show_po_ship_date_dtls','6')\"> ".change_date_format($row[csf('acc_ship_date')])." </a>";
								   $tot_ship_qty_button="<a href='##' onClick=\"generate_order_report('".$row[csf('po_id')]."','".$row[csf('company_id')]."','".$row[csf('job_no')]."','".$row[csf('buyer_name')]."','".$row[csf('style_ref_no')]."','".$row[csf('country_ship_date')]."','".$row[csf('country_id')]."','show_po_ship_qty_dtls','7__".$row[csf('actual_po_id')]."')\"> ".number_format($ship_qty,0)." </a>";
								    $invoice_qty_button="<a href='##' onClick=\"generate_order_report('".$row[csf('po_id')]."','".$row[csf('company_id')]."','".$row[csf('job_no')]."','".$row[csf('buyer_name')]."','".$row[csf('style_ref_no')]."','".$row[csf('country_ship_date')]."','".$row[csf('country_id')]."','show_invo_ship_qty_dtls','8')\"> ".number_format($tot_invoice_qty,0)." </a>";
								 $profit_except_cm=0;
								$profit_except_cm=($cm_us_dzn-($tot_raw_material+$tot_commisssion_dzn));
								 $total_profit_except_oh=($profit_except_cm/12)*$actual_po_qty;
								
								$booking_nos=rtrim($fabric_booking_data_arr[$row[csf('po_id')]]['booking_no'],',');
								$booking_numbers=array_unique(explode(",",$booking_nos));
								$button_setting='';
								foreach($booking_numbers as $bookno)
								{
									$item_category=$fabric_booking_data2_arr[$bookno]['item_category'];
									$booking_type=$fabric_booking_data2_arr[$bookno]['booking_type'];
									$fabric_source=$fabric_booking_data2_arr[$bookno]['fabric_source'];
									$is_short=$fabric_booking_data2_arr[$bookno]['is_short'];
									$entry_form=$fabric_booking_data2_arr[$bookno]['entry_form'];
									$is_approved=$fabric_booking_data2_arr[$bookno]['is_approved'];
									
									if($booking_type==1 && $entry_form==108 && $is_short==2){
										$row_id=$format_ids_partial[0];
									}
									else if($booking_type==1 && $entry_form==118 && $is_short==2){
										$row_id=$format_ids[0];
									}
									else
									{
										$row_id=$print_report_ids_short[0];
									}
									if($row_id==1)
										{ 
										$button_setting.="<a href='#' onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report_gr','".$i."')\"> ".$bookno." <a/>".',';
										}
										else if($row_id==2)
										{ 
										 
										 $button_setting.="<a href='#' onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report','".$i."')\">  ".$bookno. "<a/>".',';
										}
									 	else if($row_id==3)
										{ 
										 
										  $button_setting.="<a href='#' onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report3','".$i."')\"> ".$bookno." <a/>".',';
										}
										else if($row_id==4)
										{ 
										 $button_setting.="<a href='#' onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report1','".$i."')\">" .$bookno. "<a/>".',';
										}
									   	else if($row_id==5)
										{ 
										 $button_setting.="<a href='#' onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report2','".$i."')\"> ".$bookno." <a/>".',';
										}
										else if($row_id==6)
										{ 
										 $button_setting.="<a href='#' onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report4','".$i."')\"> ".$bookno." <a/>".',';
										}
									   	else if($row_id==7)
										{ 
										 $button_setting.="<a href='#' onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report5','".$i."')\"> ".$bookno." <a/>".',';
									   	}
										else if($row_id==8)
										{ 
										 $button_setting.="<a href='#' onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report','".$i."')\"> ".$bookno." <a/>".',';
									   	}
										else if($row_id==9)
										{ 
										 $button_setting.="<a href='#' onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report3','".$i."')\"> ".$bookno." <a/>".',';
									   	}
										
									   	else if($row_id==45) 
										{ 
										 $button_setting.="<a href='#' onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report_urmi','".$i."')\"> ".$bookno." <a/>".',';
										}
									   	else  if($row_id==53)
										{ 
										 $button_setting.="<a href='#'  onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report_jk','".$i."')\"> ".$bookno." <a/>".',';
										}
									   	else if($row_id==93)
										{ 
										 $button_setting.="<a href='#'  onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report_libas','".$i."')\"> ".$bookno." <a/>".',';
										}
									   else if($row_id==73)
										{ 
										 $button_setting.="<a href='#'  onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report_mf','".$i."')\"> ".$bookno." <a/>".',';
										}
									    else if($row_id==85)
										{ 
										 $button_setting.="<a href='#'  onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','print_booking_3','".$i."')\"> ".$bookno."<a/>".',';
										 }
										 else if($row_id==84)
										{ 
										 $button_setting.="<a href='#'  onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report_urmi_per_job','".$i."')\"> ".$bookno."<a/>".',';
										 }
										else if($row_id==143)
										{ 
										 $button_setting.="<a href='#'  onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report_urmi','".$i."')\"> ".$bookno."<a/>".',';
										 }
								}
								
							
						?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
							<td width="30"><? echo $i; ?></td>
							<td width="40" title="Pub Ship Date Year"><? echo date("Y",strtotime($row[csf('pub_shipment_date')])); ?></td>
							<td width="90"  title="Pub Ship Date Month" align="center"><p><? echo date("F",strtotime($row[csf('pub_shipment_date')])); ?></p></td>
							<td width="80" ><div style="word-break:break-all"><? echo $team_leader_library[$row[csf('team_leader')]]; ?></div></td>
							<td width="80"><div style="word-break:break-all"><? echo $dealing_merchant_array[$row[csf('dealing_marchant')]]; ?></div></td>
							<td width="80"><div style="word-break:break-all"><? echo $company_library[$row[csf('style_owner')]]; ?></div></td>
							<td width="100"><div style="word-wrap:break-word:100px;"><? echo $product_dept[$row[csf('product_dept')]]; ?></div></td>
							<td width="50"><div style="word-wrap:break-word:50px;"><? echo $row[csf('job_prefix')]; ?></div></td>
							<td width="130"><div style="word-break:break-all"><? echo $row[csf('style_ref_no')]; ?></div></td>
							<td width="100" title="<? echo $row[csf('job_no')];?>"> <div style="word-break:break-all"> <? echo $row[csf('po_number')]; ?>  </div>	</td>
							<td width="100" title="<? echo $row[csf('job_no')];?>"> <div style="word-break:break-all"> <? echo $row[csf('initial_po')]; ?>  </div>	</td>
							<td width="80" align="center" > <div style="word-break:break-all"><? echo $country_name; ?> </div>
							</td>

							<td width="100" align="left"><div style="word-break:break-all"><?  $buyer_name_str=$buyer_library[$row[csf('buyer_name')]]; echo $buyer_name_str; ?> </div></td>
							<td width="100" align="left"><div style="word-break:break-all"><? $client_name_str=$buyer_library[$row[csf('client_id')]]; echo $client_name_str; ?> </div></td>
							<td width="80" align="center">
								<div style="word-wrap:break-word:80px;">
									<? echo $season_name_library[$row[csf('season_matrix')]];?>
								</div>
							</td>
							<td width="130" align="left">
								<div style="word-break:break-all">
									<?
									$gmts_item='';$body_item=''; $gsm_item=''; $gmts_item_id=array_unique(explode(",",$row[csf('gmts_item_id')]));
									foreach($gmts_item_id as $item_id)
									{
										if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
										if($body_item=="") $body_item=$body_part[$pre_cost_data_arr[$row[csf('job_no')]][$item_id]['body']]; else $body_item.=",".$body_part[$pre_cost_data_arr[$row[csf('job_no')]][$item_id]['body']];
										if($gsm_item=="") $gsm_item=$pre_cost_data_arr[$row[csf('job_no')]][$item_id]['gsm']; else $gsm_item.=",".$pre_cost_data_arr[$row[csf('job_no')]][$item_id]['gsm'];
									}
									echo $gmts_item; 
									//$order_qty_pcs=$row[csf('order_quantity_pcs')];
									?>
								</div>
							</td>
							<td width="150"  align="center">
								<div style="word-break:break-all">
									<? echo  $fab_des_button;
									$booking_no=rtrim($fabric_booking_data_arr[$row[csf('po_id')]]['booking_no'],',');
									$booking_no=implode(',',array_unique(explode(',',$booking_no)));

									$supplier_ids=rtrim($fabric_booking_data_arr[$row[csf('po_id')]]['supplier_id'],',');
									$supplier_company=implode(',',array_unique(explode(",",$supplier_ids)));

									$export_lc=rtrim($export_lc_data_array[$row[csf('po_id')]][lc_no],',');
									$export_lc=implode(',',array_unique(explode(',',$export_lc)));
									$export_sc=rtrim($export_sc_data_array[$row[csf('po_id')]][sc_no],',');
									$export_sc=implode(',',array_unique(explode(',',$export_sc)));
									$profit_except_cm=($cm_us_dzn-($tot_raw_material+$tot_commisssion_dzn));					

									if(!$export_lc) $export_lc=$export_sc;



									$lrevised_date=$revised_date_arr[$row[csf('po_id')]][$row[csf('country_ship_date')]]['lship_date'];
									$forig_date=$revised_date_arr[$row[csf('po_id')]][$row[csf('country_ship_date')]]['forig_date'];
									if($forig_date!='')
									{
										$origin_ship_date=$row[csf('country_ship_date')];	
									}
									else
									{
										$origin_ship_date='';
									}
									if($lrevised_date!='')
									{
									$last_ship_date=$row[csf('shipment_date')];//$lrevised_date[0];	
								}
								else
								{
									$last_ship_date='';
								}
								//$order_qty_pcs=$actual_po_qty;
								$row[csf('order_quantity')]=$actual_po_qty;
								?>

							</div>
						</td>
						
						<td width="200" ><div style="word-break:break-all"><? echo $fab_composiiton; ?></div></td>
						<td width="150" ><div style="word-break:break-all"><? echo rtrim($button_setting,','); ?></div></td>
						<td width="100" ><div style="word-break:break-all"><? echo $supplier_company; ?></div></td>
						<td width="50" align="center"><div style="word-break:break-all"><? echo $fab_gsm; ?></div></td>
						<td width="70" align="right"><div style="word-break:break-all"><? echo number_format($row[csf('po_quantity')],0); ?></div></td>
						<td width="50" align="center"><div style="word-break:break-all"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></div></td>
						<td width="80" align="right"> <p> <? echo number_format($order_qty_pcs,0); ?>   </p>
							<td width="40" align="right" > <? echo $row[csf('ratio')]; ?>  </td>

							<td width="80" align="right" title="Ex Fact qty-Actual Po Qty"> <? echo  number_format($short_excess_qty,0); ?></td>
							<td width="80" align="right"> <? echo  $ship_date_button;//change_date_format($row[csf('country_ship_date')]); ?></td>
							<td width="80" align="right"> <? echo  number_format($air_qty ,0); ?></td>
							<td width="80" align="right"> <? echo  number_format($sea_qty ,0); ?></td>
							<td width="90" title="Air+Sea Qty" align="right"><? echo  $tot_ship_qty_button;//number_format($ship_qty,0); ?></td>
							<td width="80"   align="center" title="First Origin Date"> <? echo change_date_format($last_ship_date); ?></td>
							 
							<td width="80" align="center" title="Last Revised Date"><div style="word-break:break-all"><? 
								//$revised_date=explode(" ",$revised_date_arr[$row[csf('po_id')]]['ship_date']);
								echo change_date_format($origin_ship_date);
								$ttl_commission_cost=($row[csf('po_quantity')]*$tot_commisssion_dzn)/12;
								$ttl_profit_base_on_ship=($ship_val+$row[csf('up_charge')])-($ttl_commission_cost+$tot_raw_materials_cost);
								//echo $revised_date_arr[$row[csf('po_id')]]['ship_date']; ?> </div></td>
								<td width="80" align="center"><? echo change_date_format($all_invoice_date); ?> </td>
								<td width="60" title="FOB Value/Set Ratio" align="right"><? echo number_format($avg_unit,2); ?></td>
								<td width="110"  align="left"><div style="word-break:break-all"><? echo $export_lc; ?> &nbsp;</div></td>
								<td width="70" align="right" title="Total Commission=Tot Comision(<? echo number_format($tot_commisssion_raw,2);?>)/Actual Po Qty*12"> <? echo  number_format($tot_commisssion_dzn,2); ?></td>
								<td width="100" align="right" title="<? echo 'Total Raw Material='.number_format($tot_raw_materials_cost,2); ?>"> <? echo  number_format($tot_raw_material,2); ?></td>
								<td width="70" align="right" title="<? echo 'Total OH='.number_format($po_cm_cost,2);?>"><div style="word-break:break-all"><? echo number_format($cm_cost,2); ?></div></td>
								<td width="70" align="right" title="FOB Value-(Raw Material Cost(<? echo number_format($tot_raw_materials_cost,2)?>)+Overhead cost(<? echo number_format($cm_cost,2);?>+ commission(<? echo number_format($tot_commisssion_raw,2);?>))/Actual Po Qty X 12"><div style="word-break:break-all"><? echo number_format($cm_margin_dzn,2); ?></div></td>
								<td width="70" align="right" title="{FOB value-(Raw material cost + commission)}/Actual Po Qty X 12"><div style="word-break:break-all"><? echo number_format($profit_except_cm,2); ?></div></td>
								 <td width="70" align="right" title="TTL Profit based on ship/12">
								 <div style="word-break:break-all"><? $tot_profit_base_on_ship=$ttl_profit_base_on_ship/12;
								 		echo number_format($tot_profit_base_on_ship,2); ?></div>
								 </td>

							<td width="60" title="Invoice Date-Ori. Ship Date"  align="center"><div style="word-break:break-all"><? echo number_format($date_diff);?></div></td>
								<td width="60"  align="center"><div style="word-break:break-all"><? 
									if($date_diff<0)
									{
										$early_status='Delay'; 
									}
									if($date_diff>0)
									{
										$early_status='Early';
									}
									if($date_diff==0)
									{
										$early_status='Ontime';
									}
									if($date_diff=='')
									{
										$early_status='';
									}
									echo $early_status; 

									$sewingCompany=implode(',',array_unique(explode(',',$sewing_com)));
									?></div></td>
									<td width="70" align="right" title="PO Qty*Commission Dzn/12"><div style="word-break:break-all"><?  echo number_format($ttl_commission_cost,2);?></div></td>
									<td width="100" align="right" title="Actual Po Qty*Unit Price"><div style="word-break:break-all"><?  echo number_format($tot_fob_price ,2);?> </div></td>
									<td width="100" align="right" title="Air+sea Qty*Unit Price"> <p> <? $tot_ex_fact_val=$ship_val;echo number_format($ship_val,2); ?>   </p>
										<td width="100" title="Ship Val-FOB Value" align="right"><? $tot_short_excess_val=$ship_val-$tot_fob_price; echo number_format($tot_short_excess_val,2); ?> </td>
										<td width="80" title="Raw material Cost" align="right" ><p><? echo number_format($tot_raw_materials_cost,2); ?></p></td>
										<td width="90" title="CM US/12*Actual Qty" align="right" ><p><? echo number_format($cm_margin_val,2); ?></p></td>
										<td width="90" title="Profit Except OH/12*Actual Po Qty" align="right" ><p><? echo number_format($total_profit_except_oh,2); ?></p></td>
										<td width="90" title="Ship Value+Up Charge-TTL Commission-Tot Raw Material" align="right" ><p><? echo number_format($ttl_profit_base_on_ship,2); ?></p></td>
										<td width="70" align="center"> <div style="word-wrap:break-word:70px;"><? echo $quality_label[$row[csf('qlty_label')]]; ?></div></td>
										<td width="70" align="right"> <p><? echo number_format($ex_fact_carton_qty); ?> </p> </td>
										<td width="80" align="right" ><p> <? echo number_format($row[csf('up_charge')],2); ?>  </p></td>
										<td width="100" align="left"><div style="word-wrap:break-word:100px;"><? echo $lab_dip; ?></div></td>
										<td width="80" align="right"><div style="word-wrap:break-word:100px;"><? echo $sewingCompany; ?> </div></td>
										<td width="80" title="Ex fact Date"   align="center"><?

											$invoice_datess=$all_invoice_date;
											if($invoice_datess)
											{
												$dd= date("F",strtotime($invoice_datess));
												echo $dd;
											}

											?> </td>
											<td width="80" align="right" title="Booking Qty(<? echo number_format($fab_finish,2); ?>)/PO Qty(<? echo number_format($po_quantity,0); ?>)*Country Qty(<? echo number_format($country_ship_qty,2); ?>)"><? echo $fab_des_button_kg; ?> </td>
											<td width="100" align="right" title="Booking Qty(<? echo number_format($fab_finish,2); ?>)/PO Qty(<? echo number_format($po_quantity,0); ?>)*Country Qty(<? echo number_format($country_ship_qty,2); ?>)"><? echo $fab_des_button_yds; ?> </td>
											<td width="100" align="right" title="Booking Qty(<? echo number_format($fab_finish,2); ?>)/PO Qty(<? echo number_format($po_quantity,0); ?>)*Country Qty(<? echo number_format($country_ship_qty,2); ?>)"><? echo $fab_des_button_mtr; ?> </td>
											<td width=""  align="right"><? echo number_format($row[csf('set_smv')],2); ?></td>
											<td width="70"  align="right" title="SMV*Tot Ship Qty"><? $sales_minute=$row[csf('set_smv')]*$ship_qty; echo number_format($sales_minute,2); ?></td>

										</tr>
						<?
						$total_po_qty+=$row[csf('order_quantity')];
						$total_ex_fact_qty+=$ex_fact_qty;
						$total_invoice_qty+=$tot_invoice_qty;
						$total_tot_fob_price+=$tot_fob_price;
						$total_tot_air+=$air_qty/$row[csf('ratio')];
						$total_tot_sea+=$sea_qty/$row[csf('ratio')];
						$total_cm_margin_val+=$cm_margin_val;
						$total_up_profit_except_oh+=$total_profit_except_oh;
						$total_up_charge+=$row[csf('up_charge')];
						$total_fab_finish_req+=$fab_finish_req;
						$total_raw_materials_cost+=$tot_raw_materials_cost;
						
						$total_ttl_commission_cost+=$ttl_commission_cost;
						$total_tot_profit_base_on_ship+=$ttl_profit_base_on_ship;
						$total_sales_minute+=$sales_minute;
					
						$i++;
						}
						?>
						</table>
	                    </div>
						<table class="rpt_table" width="5320" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
							<tfoot>
								<th width="30">&nbsp;</th>
								<th width="40">&nbsp;</th>
								<th width="90">&nbsp;</th>
								<th width="80">&nbsp;</th>
								<th width="80">&nbsp;</th>
	                            <th width="80">&nbsp;</th>
	                            <th width="100">&nbsp;</th>
								<th width="50">&nbsp;</th>
								<th width="130">&nbsp;</th>
								<th width="100">&nbsp;</th>
								<th width="100">&nbsp;</th>
								
	                            <th width="80">&nbsp;</th>
							
								<th width="100">&nbsp;</th>
								<th width="100">&nbsp;</th>
							
								<th width="80">&nbsp;</th>
								<th width="130">&nbsp;</th>
								
								<th width="150">&nbsp;</th>
	                            
								<th width="200">&nbsp;</th>
	                            <th width="150">&nbsp;</th>
	                            <th width="100">&nbsp;</th>
								<th width="50">Total</th>
								<th width="70">&nbsp;</th>
								<th width="50">&nbsp;</th>
								<th width="80"  id="total_po_qty"><? echo number_format($total_po_qty,0); ?></th>
								<th width="40">&nbsp;</th>
	                            
								<th width="80">&nbsp;<? //echo number_format($total_tot_ex_fact_val); ?></th>
	                            <th width="80">&nbsp;<? //echo number_format($total_tot_ex_fact_val); ?></th>
	                            <th width="80" id="total_ship_qty_air"><? echo number_format($total_tot_air,0); ?></th>
	                            <th width="80" id="total_ship_qty_sea"><? echo number_format($total_tot_sea,0); ?></th>
	                            <th width="90" id="total_ship_qty"><? echo number_format($total_tot_sea+$total_tot_air,0); ?></th>
	                           
	                            
								<th width="80">&nbsp;</th>
	                             
								<th width="80">&nbsp;<? //echo number_format($total_tot_ex_fact_val); ?></th>
								<th width="80">&nbsp;</th>
								<th width="60">&nbsp;</th>
								<th width="110">&nbsp;</th>
	                            <th width="70">&nbsp;</th>
	                            <th width="100">&nbsp;</th>
								<th width="70">&nbsp;</th>
	                            <th width="70">&nbsp;</th>
								 <th width="70">&nbsp;</th>
	                            <th width="70">&nbsp;</th>
								
								<th width="60">&nbsp;</th>
								<th width="60">&nbsp;</th>
								<th width="70"><? echo number_format($total_ttl_commission_cost,2);//echo $total_cm_margin_val; ?></th>
								<th width="100" id="value_total_fob_price"><? echo number_format($total_tot_fob_price,2);//echo $total_cm_margin_val; ?></th>
								<th width="100" id="value_total_ex_fact_val"><? echo number_format($total_tot_ex_fact_val,2); ?></th>
								<th width="100">&nbsp;</th>
	                            <th width="80" id="value_total_raw_materials_cost"><? echo number_format($total_raw_materials_cost,2); ?></th>
								<th width="90" id="value_total_cm_margin_val"><? echo number_format($total_cm_margin_val,2); ?></th>
	                            <th width="90" id="value_total_profit_except_val"><? echo number_format($total_up_profit_except_oh,2); ?></th>
								<th width="90"><? echo number_format($total_tot_profit_base_on_ship,2); ?></th>
								<th width="70">&nbsp;</th>
								
								
								<th width="70">&nbsp;</th>
								<th width="80" id="value_total_up_charge"><? echo number_format($total_up_charge,2); ?></th>
								<th width="100">&nbsp;</th>
								<th width="80">&nbsp;</th>
								<th width="80">&nbsp;</th>
								<th width="80" id="value_total_fab_finish_req1"><? echo number_format($total_fab_finish_req,2); ?></th>
								<th width="100" id="value_total_fab_finish_req2"><? echo number_format($total_fab_finish_req,2); ?></th>
								<th width="100" id="value_total_fab_finish_req3"><? echo number_format($total_fab_finish_req,2); ?></th>
								<th>&nbsp;</th>
								<th width="70"><? echo number_format($total_sales_minute,2); ?></th>
							</tfoot>
						</table>
					
			</fieldset>
				</div>
			<?
			}
	}
	else if($report_type==3) //Country Ship Date Wise
	{

		if($template==1)
		{
			$revised_date_arr=array();$exfactory_data_array=array();$pre_cost_data_arr=array();
			$fabric_booking_data_arr=array();$fabric_des_data_arr=array();$pre_cost_cm_margin_arr=array();		
			$export_lc_data_array=array();$sewing_data_array=array();$lab_dip_data_arr=array();
			
			/*$sql_update_history=sql_select("Select po_id,max(po_received_date) as po_received_date,max(shipment_date) as shipment_date,min(shipment_date) as orig_ship_date from wo_po_update_log group by po_id order by po_id");
			
			foreach($sql_update_history as $row)
			{
				$revised_date_arr[$row[csf('po_id')]]['recv_date']=$row[csf('po_received_date')];
				$revised_date_arr[$row[csf('po_id')]]['lship_date']=$row[csf('shipment_date')];
				$revised_date_arr[$row[csf('po_id')]]['forig_date']=$row[csf('orig_ship_date')];
				
			}*/
			$sql_update_history=sql_select("Select c.po_break_down_id as po_id,c.country_ship_date,min(c.country_ship_date_prev) as orig_ship_date,max(c.country_ship_date) as shipment_date from wo_po_details_master a, wo_po_break_down b ,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and c.po_break_down_id=b.id and a.job_no=c.job_no_mst and a.company_name='$company_name' and a.status_active=1 and c.order_quantity>0 and
				a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $c_date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond 
				$order_status_cond  $internal_ref_cond $file_no_cond $season_cond $style_owner_cond $team_leader_cond $dealing_marchant_cond group by c.po_break_down_id,c.country_ship_date order by c.po_break_down_id");
			
			foreach($sql_update_history as $row)
			{
				$revised_date_arr[$row[csf('po_id')]][$row[csf('country_ship_date')]]['recv_date']=$row[csf('po_received_date')];
				$revised_date_arr[$row[csf('po_id')]][$row[csf('country_ship_date')]]['lship_date']=$row[csf('shipment_date')];
				$revised_date_arr[$row[csf('po_id')]][$row[csf('country_ship_date')]]['forig_date']=$row[csf('orig_ship_date')];
				
			}
			unset($sql_update_history);
			if($db_type==2)
			{
				$lab_grp="listagg(CAST(b.lapdip_no as VARCHAR(4000)),',') within group (order by b.lapdip_no) as lab_id";
				$count_grp="listagg(CAST(b.count_id as VARCHAR(4000)),',') within group (order by b.count_id) as count_id";
				$grop_fab="listagg(CAST(b.gsm_weight as VARCHAR(4000)),',') within group (order by b.gsm_weight) as gsm_weight,listagg(CAST(b.construction as VARCHAR(4000)),',') within group (order by b.construction) as descrp,listagg(CAST(b.lib_yarn_count_deter_id as VARCHAR(4000)),',') within group (order by b.lib_yarn_count_deter_id) as deter_id";
			
				$grop_country="listagg(CAST(c.country_id as VARCHAR(4000)),',') within group (order by c.country_id) as country_id";
			}
			else
			{
				$lab_grp="group_concat(b.lapdip_no) as lab_id";
				$count_grp="group_concat(b.count_id) as count_id";
				$grop_fab="group_concat(b.gsm_weight) as gsm_weight,group_concat(b.construction) as descrp,group_concat(b.lib_yarn_count_deter_id) as deter_id";
				$grop_country="group_concat(c.country_id) as country_id";
			}
			
			//1,10,11,14,15,16,17,20,69,95,100,125,128,129,131,132,135,143,149,152,164,167,171,191,198,201,208,219
			
			//print_r($fabric_booking_data_arr);
			$sql_yarn=sql_select("Select a.id,a.construction,a.gsm_weight,b.count_id from lib_yarn_count_determina_mst a,lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active =1 and a.is_deleted=0 group by a.id,a.construction,a.gsm_weight,b.count_id ");
			
			foreach($sql_yarn as $row)
			{
				$fabric_des_data_arr[$row[csf('id')]]['des']=$row[csf('construction')];
				$fabric_des_data_arr[$row[csf('id')]]['gsm']=$row[csf('gsm_weight')];
				$fabric_des_data_arr[$row[csf('id')]]['count']=$row[csf('count_id')];
			}
			unset($sql_yarn);
			
			/*$exfactory_data=sql_select("select po_break_down_id as po_id,MAX(ex_factory_date) as ex_fact_date,
			sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as ex_fact_qty,
			sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as ex_fact_ret_qty,
			sum(CASE WHEN entry_form!=85 THEN total_carton_qnty ELSE 0 END) as tot_carton_qty,
			sum(CASE WHEN entry_form=85 THEN total_carton_qnty ELSE 0 END) as tot_ret_carton_qty
			 from pro_ex_factory_mst  where 1=1 and status_active=1 and is_deleted=0 group by po_break_down_id");
			
			foreach($exfactory_data as $row)
			{
				$exfactory_data_array[$row[csf('po_id')]][ex_fact_qty]=$row[csf('ex_fact_qty')]-$row[csf('ex_fact_ret_qty')];
				$exfactory_data_array[$row[csf('po_id')]][carton_qty]=$row[csf('tot_carton_qty')]-$row[csf('tot_ret_carton_qty')];
				$exfactory_data_array[$row[csf('po_id')]][ex_factory_date]=$row[csf('ex_fact_date')];
			}*/
			
				   $sql_result="select a.company_name as company_id, a.job_no_prefix_num as job_prefix, a.team_leader, a.qlty_label, a.dealing_marchant, a.product_dept, a.season_buyer_wise as season_matrix, b.insert_date, a.job_no, a.buyer_name, a.client_id, a.style_ref_no, a.style_owner, b.is_confirmed, b.shiping_status, b.matrix_type, a.set_smv, a.avg_unit_price, 
				a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, a.job_quantity, a.total_price, b.id as po_id, b.po_number, b.pub_shipment_date, b.po_received_date, b.shipment_date, (b.up_charge) as up_charge,
				sum(distinct b.po_quantity) as po_quantity, sum(b.po_total_price) as po_total_price, b.grouping, b.file_no, sum(c.order_quantity/a.total_set_qnty) as  order_quantity, sum(c.order_quantity) as order_quantity_pcs, c.country_ship_date, sum(c.order_total) as order_total, $grop_country
				from wo_po_details_master a, wo_po_break_down b ,wo_po_color_size_breakdown c
				 where a.job_no=b.job_no_mst and c.po_break_down_id=b.id and a.job_no=c.job_no_mst and a.company_name='$company_name' and a.status_active=1 and c.order_quantity>0 and
				a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $c_date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond 
				$order_status_cond  $internal_ref_cond $file_no_cond $season_cond $style_owner_cond $team_leader_cond $dealing_marchant_cond group by  a.company_name, a.job_no_prefix_num, a.team_leader, a.qlty_label, a.dealing_marchant, a.product_dept, a.season_buyer_wise, b.insert_date, a.job_no, a.buyer_name, a.client_id,a.style_ref_no, a.style_owner, b.is_confirmed, b.shiping_status, b.matrix_type, a.set_smv, a.avg_unit_price, 
				a.order_uom, a.gmts_item_id, a.total_set_qnty, a.job_quantity, a.total_price, b.id, b.po_number, b.pub_shipment_date,b.up_charge, b.po_received_date, b.shipment_date, b.grouping, b.file_no, c.country_ship_date order by b.id";
				//die;
				$result_data=sql_select($sql_result); 
				$country_arr=array();$all_po_id='';
				foreach($result_data as $row)
				{
					$country_arr[$row[csf('job_no')]]['po_qty']+=$row[csf('job_quantity')];
					$country_shipdate_arr[$row[csf('po_id')]][$row[csf('country_ship_date')]]['po_qty']+=$row[csf('order_quantity')];
					$po_country_arr[$row[csf('po_id')]]['po_qty']+=$row[csf('po_quantity')];
					if($all_po_id=="") $all_po_id=$row[csf("po_id")]; else $all_po_id.=",".$row[csf("po_id")];
				}
					$tot_rows_po=count($result_data); 
					$all_po=array_unique(explode(",",$all_po_id));
					$po_arr_cond=array_chunk($all_po,1000, true);
					$po_cond_for_in="";$po_cond_for_in2="";$po_cond_for_in3="";$po_cond_for_in4="";$po_cond_for_in5="";$po_cond_for_in6="";$po_cond_for_in7="";
					
					$poIds=chop($all_po_id,','); //$po_cond_for_in=""; $order_cond1=""; $order_cond2=""; $precost_po_cond="";
					$po_ids=count(array_unique(explode(",",$all_po_id)));
						if($db_type==2 && $po_ids>1000)
						{
							$po_cond_for_in=" and (";
							$po_cond_for_in2=" and (";
							$po_cond_for_in3=" and (";
							$po_cond_for_in4=" and (";
							$po_cond_for_in5=" and (";
							$po_cond_for_in6=" and (";
							$po_cond_for_in7=" and (";
							
							$poIdsArr=array_chunk(explode(",",$poIds),999);
							foreach($poIdsArr as $ids)
							{
								$ids=implode(",",$ids);
								//$poIds_cond.=" po_break_down_id in($ids) or ";
								$po_cond_for_in.=" c.po_break_down_id in($ids) or"; 
								$po_cond_for_in2.=" d.po_break_down_id in($ids) or"; 
								$po_cond_for_in3.=" b.id in($ids) or"; 
								$po_cond_for_in4.=" b.po_breakdown_id in($ids) or"; 
								$po_cond_for_in5.=" po_break_down_id in($ids) or"; 
								$po_cond_for_in6.=" b.po_break_down_id in($ids) or"; 
								$po_cond_for_in7.=" b.wo_po_break_down_id in($ids) or"; 
							}
							$po_cond_for_in=chop($po_cond_for_in,'or ');
							$po_cond_for_in.=")";
							$po_cond_for_in2=chop($po_cond_for_in2,'or ');
							$po_cond_for_in2.=")";
							$po_cond_for_in3=chop($po_cond_for_in3,'or ');
							$po_cond_for_in3.=")";
							$po_cond_for_in4=chop($po_cond_for_in4,'or ');
							$po_cond_for_in4.=")";
							$po_cond_for_in5=chop($po_cond_for_in5,'or ');
							$po_cond_for_in5.=")";
							$po_cond_for_in6=chop($po_cond_for_in6,'or ');
							$po_cond_for_in6.=")";
							$po_cond_for_in7=chop($po_cond_for_in7,'or ');
							$po_cond_for_in7.=")";
						}
						else
						{
							$po_cond_for_in=" and c.po_break_down_id in($poIds)";
							$po_cond_for_in2=" and d.po_break_down_id  in($poIds)";
							//$po_cond_for_in3=" and d.po_breakdown_id in($poIds)";
							$po_cond_for_in3=" and b.id in($poIds)";
							$po_cond_for_in4=" and b.po_breakdown_id  in($poIds)";
							$po_cond_for_in5=" and po_break_down_id in($poIds)";
							$po_cond_for_in6=" and b.po_break_down_id in($poIds)";
							$po_cond_for_in7=" and b.wo_po_break_down_id in($poIds)";
						}
						
						//current_invoice_value
					$sql_invo_exp="select b.po_breakdown_id as po_id,a.country_id,max(a.invoice_date) as invoice_date,
					sum(CASE WHEN a.shipping_mode=1 THEN b.current_invoice_qnty ELSE 0 END) as sea_qnty,
					sum(CASE WHEN a.shipping_mode=2 THEN b.current_invoice_qnty ELSE 0 END) as air_qnty,
					sum(CASE WHEN a.shipping_mode=1 THEN b.current_invoice_value ELSE 0 END) as sea_val,
					sum(CASE WHEN a.shipping_mode=2 THEN b.current_invoice_value ELSE 0 END) as air_val
					 from  com_export_invoice_ship_mst a,com_export_invoice_ship_dtls b where a.id=b.mst_id  and a.shipping_mode in(1,2)  and a.status_active ='1' and a.is_deleted ='0' $po_cond_for_in4  group by b.po_breakdown_id,a.country_id";
					 $sql_inv_exp_res=sql_select($sql_invo_exp);
					 foreach($sql_inv_exp_res as $row)
					{
						$invoice_data_array[$row[csf('po_id')]][$row[csf('country_id')]][1]['sea_qnty']+=$row[csf('sea_qnty')];
						$invoice_data_array[$row[csf('po_id')]][$row[csf('country_id')]][2]['air_qnty']+=$row[csf('air_qnty')];
						//$invoice_data_array[$row[csf('po_id')]][ex_factory_date]=$row[csf('ex_fact_date')];
					}
			
					$sql_yarn = "select a.sequence_no,a.id,c.yarn_count,a.construction,b.copmposition_id  from  lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b,lib_yarn_count c where a.id=b.mst_id and b.count_id=c.id  order by a.id, a.sequence_no";
					$data_arr_ycount=sql_select($sql_yarn);
					foreach($data_arr_ycount as $row)
					{
							$composition1=$composition[$row[csf("copmposition_id")]];
							$fab_desc=$composition1.'100%';
							$fab_composition_arr[$row[csf("id")]]['desc']=$fab_desc;
							
					}
			// and b.body_part_id in(1,10,11,14,15,16,17,20,69,95,100,125,128,129,131,132,135,143,149,152,164,167,171,191,198,201,208,219,217,227,232,233,234,235,236,238,246,252,253,258,274,276,283,284,301,322,330,350,364,371,373,392,393,394)
					 $sql_pre=sql_select("Select c.po_break_down_id as po_id,b.job_no,b.gsm_weight,b.construction as descrp,b.composition,b.lib_yarn_count_deter_id as deter_id,d.margin_dzn as margin_dzn, d.cm_cost from wo_po_details_master a,wo_pre_cost_fabric_cost_dtls b,wo_pre_cos_fab_co_avg_con_dtls c,wo_pre_cost_dtls d where a.job_no=b.job_no and c.pre_cost_fabric_cost_dtls_id=b.id and c.job_no=a.job_no and d.job_no=a.job_no  and d.job_no=b.job_no  and  c.cons>0 and a.company_name='$company_name' and b.body_part_id in($body_id) $buyer_id_cond $job_no_cond  $po_cond_for_in  group by c.po_break_down_id,b.job_no,d.margin_dzn, d.cm_cost,b.gsm_weight,b.construction,b.lib_yarn_count_deter_id,b.composition");
			
					foreach($sql_pre as $row)
					{
						$fab_comosition=$fab_composition_arr[$row[csf("deter_id")]]['desc'];
						$pre_cost_data_arr[$row[csf('po_id')]]['deter_id'].=$row[csf('deter_id')].',';
						$pre_cost_data_arr[$row[csf('po_id')]]['gsm'].=$row[csf('gsm_weight')].',';
						$pre_cost_data_arr[$row[csf('po_id')]]['fab_des'].=$row[csf('descrp')].',';
						$pre_cost_data_arr[$row[csf('po_id')]]['fab_composiiton'].=$row[csf('composition')].',';
						$pre_cost_cm_margin_arr[$row[csf('job_no')]]['margin']=$row[csf('margin_dzn')];
						$pre_cost_cm_margin_arr[$row[csf('job_no')]]['cm']=$row[csf('cm_cost')];
					}
					unset($sql_pre);
					
					//print_r($pre_cost_data_arr);//wo_po_color_size_breakdown
				$sql_book=sql_select("SELECT e.uom,  b.entry_form,b.booking_type,b.item_category,b.is_approved,b.fabric_source,b.is_short,b.supplier_id,b.booking_no,b.pay_mode,c.fin_fab_qnty,c.grey_fab_qnty,c.po_break_down_id as po_id from wo_po_details_master a,wo_booking_mst b,wo_booking_dtls c,wo_pre_cost_fabric_cost_dtls e where a.job_no=c.job_no   and  e.id=c.pre_cost_fabric_cost_dtls_id and  a.job_no=e.job_no and c.booking_no=b.booking_no  and b.booking_type=1 and b.is_short in(1,2) and a.company_name='$company_name' and c.status_active=1 and c.is_deleted=0  $buyer_id_cond $job_no_cond $po_cond_for_in");
					
					/*$sql_book=sql_select("Select b.booking_no,c.fin_fab_qnty,c.po_break_down_id as po_id,e.country_ship_date from wo_po_details_master a,wo_booking_mst b,wo_booking_dtls c,wo_po_color_size_breakdown e where a.job_no=c.job_no and  e.id=c.color_size_table_id and e.po_break_down_id=c.po_break_down_id and  a.job_no=e.job_no_mst and c.booking_no=b.booking_no  and b.booking_type=1 and b.is_short=2 and a.company_name='$company_name' and c.status_active=1 and c.is_deleted=0  $buyer_id_cond $job_no_cond");*/
					//and c.status_active=1 and c.is_deleted=0
					foreach($sql_book as $row)
					{
						//$fabric_booking_data_arr[$row[csf('po_id')]][$row[csf('country_ship_date')]]['booking_no'].=$row[csf('booking_no')].',';
						$fabric_booking_data_arr[$row[csf('po_id')]]['fin_qnty']+=$row[csf('grey_fab_qnty')];
						$fabric_booking_data_arr[$row[csf('po_id')]]['booking_no'].=$row[csf('booking_no')].',';
						$fabric_booking_data_arr_uom_wise[$row[csf('po_id')]][$row[csf('uom')]]['fin_qnty']+=$row[csf('grey_fab_qnty')];
						
						if($row[csf('pay_mode')]==5 || $row[csf('pay_mode')]==3)
						{
							$supplier_company=$company_library[$row[csf('supplier_id')]];
						}
						else
						{
							$supplier_company=$supplier_library[$row[csf('supplier_id')]];
						}
						$fabric_booking_data_arr[$row[csf('po_id')]]['supplier_id'].=$supplier_company.',';
						
						$fabric_booking_data2_arr[$row[csf('booking_no')]]['item_category']=$row[csf('item_category')];
						$fabric_booking_data2_arr[$row[csf('booking_no')]]['fabric_source']=$row[csf('fabric_source')];
						$fabric_booking_data2_arr[$row[csf('booking_no')]]['is_short']=$row[csf('is_short')];
						$fabric_booking_data2_arr[$row[csf('booking_no')]]['entry_form']=$row[csf('entry_form')];
						$fabric_booking_data2_arr[$row[csf('booking_no')]]['booking_type']=$row[csf('booking_type')];
						$fabric_booking_data2_arr[$row[csf('booking_no')]]['is_approved']=$row[csf('is_approved')];
						
					}
					unset($sql_book);
					$sql_lab=sql_select("Select b.po_break_down_id as po_id,$lab_grp from wo_po_lapdip_approval_info b where b.status_active =1 and b.is_deleted=0 $po_cond_for_in6 group by b.po_break_down_id");
			
					foreach($sql_lab as $row)
					{
						$lab_dip_data_arr[$row[csf('po_id')]]['ldip_id']=$row[csf('lab_id')];
					}
					unset($sql_lab);
					$sql_sewing=sql_select("Select b.serving_company,b.production_source,b.po_break_down_id as po_id,b.country_id from pro_garments_production_mst b where b.company_id='$company_name' and b.production_type=4 and b.status_active =1 and b.is_deleted=0 $po_cond_for_in6");
					foreach($sql_sewing as $row)
					{
						if($row[csf('production_source')]==1) $sewing_com=$company_library[$row[csf('serving_company')]]; else if($row[csf('production_source')]==3) $sewing_com=$supplier_library[$row[csf('serving_company')]];
						$sewing_data_array[$row[csf('po_id')]][$row[csf('country_id')]]['sewing_com'].=$sewing_com.',';
					}
					unset($sql_sewing);
			
					$sql_lc=sql_select("select a.export_lc_no,b.wo_po_break_down_id as po_id from  com_export_lc a,com_export_lc_order_info b where a.id=b.com_export_lc_id and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 $po_cond_for_in7" );
					$export_lc_data_array=array();
					foreach($sql_lc as $row)
					{
						$export_lc_data_array[$row[csf('po_id')]][lc_no].=$row[csf('export_lc_no')].',';
					}
						unset($sql_lc);
					$sql_sc=sql_select("select a.contract_no,b.wo_po_break_down_id as po_id from  com_sales_contract a,com_sales_contract_order_info b where a.id=b.com_sales_contract_id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 $po_cond_for_in7");
					$export_sc_data_array=array();
					foreach($sql_sc as $row)
					{
						$export_sc_data_array[$row[csf('po_id')]][sc_no].=$row[csf('contract_no')].',';
					}
					unset($sql_sc);
					$sql_invo="select b.po_break_down_id as po_id,b.country_id,max(b.ex_factory_date) as factory_date,
					sum(CASE WHEN b.shiping_mode=1 THEN b.ex_factory_qnty ELSE 0 END) as sea_qnty,
					sum(CASE WHEN b.shiping_mode=2 THEN b.ex_factory_qnty ELSE 0 END) as air_qnty,
					sum(CASE WHEN b.entry_form!=85 THEN b.total_carton_qnty ELSE 0 END) as total_carton_qnty,
					sum(CASE WHEN  b.entry_form=85 THEN b.total_carton_qnty ELSE 0 END) as total_carton_qnty_ret
					from  pro_ex_factory_mst b where  b.shiping_mode in(1,2)  and b.status_active=1 and b.is_deleted =0  $po_cond_for_in6 group by b.po_break_down_id,b.country_id order by factory_date";
					
					$export_invoice_arr=array();
					$sql_inv_res=sql_select($sql_invo);
					foreach($sql_inv_res as $row)
					{
						$export_invoice_arr[$row[csf('po_id')]][$row[csf('country_id')]][1]['sea']+=$row[csf('sea_qnty')];
						$export_invoice_arr[$row[csf('po_id')]][$row[csf('country_id')]][2]['air']+=$row[csf('air_qnty')];
						$exfactory_data_array[$row[csf('po_id')]][$row[csf('country_id')]][carton_qty]+=$row[csf('total_carton_qnty')]-$row[csf('total_carton_qnty_ret')];
						$export_invoice_date_arr[$row[csf('po_id')]]['date']=$row[csf('factory_date')];
					}
					unset($sql_inv_res);
					//print_r($export_invoice_arr);
				 $sql_fab_book= "select d.booking_no,b.id as po_id,(d.amount) as amount,c.booking_type,c.entry_form,d.grey_fab_qnty,d.fin_fab_qnty,d.wo_qnty,d.exchange_rate as exchange_rate
				from wo_po_details_master a, wo_po_break_down b, wo_booking_mst c,wo_booking_dtls d  where a.job_no=b.job_no_mst and a.company_name='$company_name'  and b.id=d.po_break_down_id and d.booking_no=c.booking_no  and b.status_active=1 and 
   b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and d.status_active=1  and c.short_booking_type not in(2,3) and d.is_deleted=0  and d.amount>0   $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond 
				$order_status_cond  $internal_ref_cond $file_no_cond $season_cond $style_owner_cond $team_leader_cond $dealing_marchant_cond $po_cond_for_in3";
				$book_result=sql_select($sql_fab_book);
				$po_wo_cost_arr=array();
				foreach($book_result as $row)
				{
					$booking_type=$row[csf('booking_type')];
					if($booking_type==1 || $booking_type==4)
					{
						if($row[csf('entry_form')]!=108) //Fabric Booking
						{
							 $fab_qnty=$row[csf('grey_fab_qnty')];
							 if($fab_qnty==0 || $fab_qnty=='')
							 {
								$avg_rate=0; $amount=0; $fab_qnty=0;
							 }
							 $avg_rate=number_format($row[csf('amount')]/$fab_qnty,6,'.','');
							if($avg_rate=='inf') $avg_rate=0;else $avg_rate=$avg_rate;
							$amount=$fab_qnty*$avg_rate;
						}
						else
						{
							$fab_qnty=$row[csf('fin_fab_qnty')];
							if($fab_qnty==0 || $fab_qnty=='')
							 {
								$avg_rate=0; $amount=0;  $fab_qnty=0;
							 }
							 
							$avg_rate=$row[csf('amount')]/$fab_qnty;
							if($avg_rate=='inf') $avg_rate=0;else $avg_rate=$avg_rate;
							$amount=$fab_qnty*$avg_rate;
						}
					}
					else if($booking_type==2 || $booking_type==5) //Trims Booking
					{
						if($row[csf('wo_qnty')]==0 || $row[csf('wo_qnty')]=='')
							 {
								$avg_rate=0; $amount=0; 
							 }
						$avg_rate=$row[csf('amount')]/$row[csf('wo_qnty')];
						if($avg_rate=='inf') $avg_rate=0;else $avg_rate=$avg_rate;
						$rate=($avg_rate/$row[csf('exchange_rate')]);
						$amount=number_format($row[csf('wo_qnty')]*$rate,6,'.','');// number_format($avg_rate,'.','');
						
					}
					else
					{
						$amount=$row[csf('amount')];
					}
					$po_wo_cost_arr[$row[csf('po_id')]]['amount']+=$amount;
					//$po_wo_cost_arr[$row[csf('po_id')]]['booking_no'].=$row[csf('booking_no')].',';
				}
				unset($book_result);
				//die;
				//print_r($po_wo_cost_arr);//$po_cond_for_in3
				  $sql_lab_book= "select b.id as po_id,sum(e.wo_value) as amount
				from wo_po_details_master a, wo_po_break_down b, wo_labtest_mst c,wo_labtest_dtls d,wo_labtest_order_dtls e  where a.job_no=b.job_no_mst and a.company_name='$company_name'  and a.job_no=d.job_no and c.id=d.mst_id and e.dtls_id=d.id and e.order_id=b.id and c.status_active=1 and  c.is_deleted=0 $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond 
				$order_status_cond $po_cond_for_in3 $internal_ref_cond $file_no_cond $season_cond $style_owner_cond $team_leader_cond $dealing_marchant_cond  group by b.id";//die;
				$lab_result=sql_select($sql_lab_book);
				$lab_po_data_cost_arr=array();
				foreach($lab_result as $row)
				{
					$lab_po_data_cost_arr[$row[csf('po_id')]]['lab']+=$row[csf('amount')];
				}
					unset($lab_result);
				//print_r($lab_po_data_arr);//$trans_arr_cond_in 
				$sql_mrr_recv= "select a.recv_number,b.id as trans_id,f.issue_qnty,f.issue_trans_id,b.order_rate,b.order_ile,e.product_name_details,e.item_group_id
						 from  inv_receive_master a, inv_transaction b,product_details_master e,inv_mrr_wise_issue_details f  where a.id=b.mst_id and e.id=b.prod_id  and f.recv_trans_id=b.id and b.transaction_type=1 and a.entry_form=20 and a.company_id=$company_name  and b.status_active=1 and  b.is_deleted=0  ";
				$mrr_recv_result=sql_select($sql_mrr_recv);
				$mrr_recv_arr=array();
				foreach($mrr_recv_result as $row)
				{
					$mrr_recv_arr[$row[csf('trans_id')]]['rate']=$row[csf('order_rate')]+$row[csf('order_ile')];
				}
				unset($mrr_recv_result);
				
				 $sql_gen_acc= "select b.id as po_id,f.recv_trans_id,e.item_group_id,
				sum(d.cons_amount) as amount,sum(d.cons_quantity) as cons_quantity,sum(f.issue_qnty) as issue_qnty
				 from wo_po_details_master a, wo_po_break_down b, inv_issue_master c,inv_transaction d,product_details_master e,inv_mrr_wise_issue_details f   where a.company_name='$company_name' and a.job_no=b.job_no_mst  and b.id=d.order_id and c.id=d.mst_id and  f.issue_trans_id=d.id and e.id=f.prod_id  and d.transaction_type in(2) and e.id=d.prod_id and  d.status_active=1 and  d.item_category=4 and c.entry_form=21 and f.entry_form=21  and c.is_deleted=0 and d.cons_quantity>0 $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond 
				$order_status_cond  $internal_ref_cond $file_no_cond $season_cond $style_owner_cond $team_leader_cond $dealing_marchant_cond $po_cond_for_in3 group by b.id,f.recv_trans_id,e.item_group_id ";
				$gen_dzn_result=sql_select($sql_gen_acc);$gen_acces_data_cost_arr=array();
				foreach($gen_dzn_result as $row)
				{
					$converrate=$conversion_arr[$row[csf("item_group_id")]]['conver_rate'];
					 $orderrate=($mrr_recv_arr[$row[csf('recv_trans_id')]]['rate']/$converrate);
					 $gen_amount=$row[csf('issue_qnty')]*$orderrate;
					$gen_acces_data_cost_arr[$row[csf('po_id')]]['amt']+=$gen_amount;
				}
				unset($gen_dzn_result);
				
				
				$print_report_format_ids_partial = return_field_value("format_id","lib_report_template","template_name=".$company_name." and module_id=2 and report_id=35 and is_deleted=0 and status_active=1");
				$format_ids_partial=explode(",",$print_report_format_ids_partial);
				
				
				$print_report_format_ids_short = return_field_value("format_id","lib_report_template","template_name=".$company_name." and module_id=2 and report_id=2 and is_deleted=0 and status_active=1");
				$print_report_ids_short=explode(",",$print_report_format_ids_short);
				
				
				$print_report_format_ids2 = return_field_value("format_id","lib_report_template","template_name=".$company_name." and module_id=2 and report_id=1 and is_deleted=0 and status_active=1");
				$format_ids=explode(",",$print_report_format_ids2);
				
			ob_start(); 
			
		?>
			<div style="width:5300px">
			<fieldset style="width:100%;">	
				<table width="5300">
					<tr class="form_caption">
						<td colspan="59" align="center">OMS Report(Country Ship Date Wise)</td>
					</tr>
					<tr class="form_caption">
						<td colspan="59" align="center"><? echo $company_library[$company_name]; ?></td>
					</tr>
				</table>
				<table id="table_header_1" class="rpt_table" width="5300" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<th width="30">SL</th>
						<th width="40">Job Year</th>
						<th width="90"><div style="word-wrap:break-word:90px;">Ord. Month</div></th>
						<th width="80"><div style="word-wrap:break-word:80px;">Team Leader</div></th>
						<th width="80"><div style="word-wrap:break-word:80px;">Deal Merchant. </div></th>
                        <th width="80"><div style="word-wrap:break-word:80px;">Style Owner</div></th>
						<th width="100">Prod. Dept.</th>
                        <th width="50">Job No</th>
						<th width="130"><div style="word-wrap:break-word:130px;">Style Ref.</div></th>
						<th width="100"><div style="word-wrap:break-word:100px;">Order No</div></th>
						
                        <th width="80">Country</th>
						
						<th width="100">Buyer Name</th>
						<th width="100">Client</th>
						
						<th width="80">Season</th>
						<th width="130">GMT Item</th>
						
						<th width="150">Fabric Const.</th>
                        <th width="200">Fabric Composition</th>
						<th width="150">Fab.Booking No</th>
                        <th width="100">Supplier</th>
						<th width="50">GSM</th>
						<th width="70">Order Qty</th>
						<th width="50">Order UOM</th>
						<th width="80">Order Qty / Pcs</th>
						<th width="40">Set Ratio</th>
                       
						<th width="80">Short / Excess Qty.</th>
                        <th width="80">Country Date</th>
                        <th width="80">Air Qty</th>
                        <th width="80">Sea Qty</th>
                        <th width="90" title="Air+Sea Qty">Total Qty</th>
						
						<th width="80">Ori. ShipDate</th>
                        <th width="80">Invoice Qty</th>
						<th width="80">Last Revised ShipDate</th>
						<th width="80">Last Ex-Fact Date</th>
						<th width="60">Unit Price (FOB)</th>
						<th width="110">SC/LC No</th>
                        <th width="70">Commi/ DZN</th>
                        <th width="100">RawMaterial Cost/Dzn</th>
                        
                        <th width="70">Overhead Cost Dzn</th>
						<th width="70">CM US/DZN</th>
                        <th width="70">Profit Except Overhead</th>
						<th width="70">Profit/Dzn based on ship</th>
						<th width="60">EARLY / Delay Days</th>
						
						<th width="60">Early / Delay Status</th>
						<th width="70"><div style="word-wrap:break-word:70px;">TTL  Commission</div></th>
						<th width="100">Total FOB Price</th>
						<th width="100">Ship Value</th>
						<th width="100">Short / Excess Val.</th>
                        <th width="80">Raw material Cost</th>
						<th width="90">Total CM</th>
                        <th width="90">Total Profit Except Overhead</th>
						<th width="90">TTL Profit based on ship</th>
						<th width="70">Qlty. Level</th>
						<th width="70">No Of Ctn. </th>
						<th width="80">UpCharge</th>
						<th width="100">LabDip No</th>
						
						 <th width="80">Sew. Company</th>
						<th width="80">Ship Month</th>
						<th width="80">Booking Qty(Kg)</th>
						<th width="100">Booking Qty(Yds)</th>
						<th width="100">Booking Qty(Mtr)</th>
						<th width="">SMV</th>
						<th width="70">Sales Minute</th>
					   
						
					</thead>
				</table>
				<div style="width:5320px; max-height:400px; overflow-y:scroll" id="scroll_body">
					<table class="rpt_table" width="5300" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
					<?
					$i=1; $total_po_qty=0; $total_ex_fact_qty=0;$total_tot_fob_price=0;$total_tot_ex_fact_val=0;		
					$total_cm_margin_val=0;	$total_qlty_qty=0;	$total_up_charge=0;	 $total_fab_finish_req=0;  $total_invoice_qty=0; 
				  //  $tot_rows=count($nameArray);
					$condition= new condition();
					 $condition->company_name("=$cbo_company_name");
					 if(str_replace("'","",$cbo_buyer_name)>0){
						  $condition->buyer_name("=$cbo_buyer_name");
					 }
					 if(str_replace("'","",$txt_job_no) !=''){
						  $condition->job_no_prefix_num("=$txt_job_no");
					 }
					 if(str_replace("'","",$cbo_order_status) >0){
						  $condition->is_confirmed("=$cbo_order_status");
					 }
					 if(str_replace("'","",$cbo_order_status)==0){
						  $condition->is_confirmed("in(1,2)");
					 }
					if(str_replace("'","",$cbo_search_date) ==1 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!=''){
						 // $condition->country_ship_date(" between '$start_date' and '$end_date'");
					 }
					  if(str_replace("'","",$cbo_search_date) ==2 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
					 {
						 $condition->po_received_date(" between '$start_date' and '$end_date'");
						
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
					/* if(str_replace("'","",$txt_season)!='')
					 {
						$condition->season("=$txt_season"); 
					 }*/
							$condition->init();
					//$yarn= new yarn($condition);
					//echo $yarn->getQuery(); die;
					//$fabric= new fabric($condition);
					//$fabric_costing_arr=$fabric->getQtyArray_by_order_knitAndwoven_greyAndfinish();
					//print_r($fabric_costing_arr);
							$other= new other($condition);
					//echo $other->getQuery(); die;
					$other_costing_arr=$other->getAmountArray_by_order();
					//print_r($other_costing_arr);
					$commission= new commision($condition);
					$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
					$total_tot_air=0;$total_tot_sea=$total_sales_minute=$total_tot_profit_base_on_ship=$total_ttl_commission_cost=0;$check_shipmon=array();$total_up_profit_except_oh=0;
					foreach($result_data as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$deter_id=rtrim($pre_cost_data_arr[$row[csf('po_id')]]['deter_id'],',');
						$fabdes=rtrim($pre_cost_data_arr[$row[csf('po_id')]]['fab_des'],',');
						$fab_composiiton=rtrim($pre_cost_data_arr[$row[csf('po_id')]]['fab_composiiton'],',');
						$fab_composiiton=implode(",",array_unique(explode(",",$fab_composiiton)));
						$gsm=rtrim($pre_cost_data_arr[$row[csf('po_id')]]['gsm'],',');
						$fab_deter_id=array_unique(explode(",",$deter_id));
						$fab_des=implode(",",array_unique(explode(",",$fabdes)));
						$fab_gsm=implode(",",array_unique(explode(",",$gsm))); 
						//echo $row[csf('country_id')];
					
						$country_ids=array_unique(explode(",",$row[csf('country_id')]));
						$country_name="";$invoice_date='';$sewing_com="";$ship_mon="";$ex_fact_carton_qty=0;$po_cm_cost=0;$invoice_date='';
						$sea_qty=0;$air_qty=0;$po_cm_cost=0;$invo_sea_qty=0;$invo_air_qty=0;
						 foreach($country_ids as $cid)
						 {
							//echo $cid;
							if($country_name=="") $country_name=$country_name_library[$cid];else $country_name.=",".$country_name_library[$cid];
							$ex_fact_carton_qty+=$exfactory_data_array[$row[csf('po_id')]][$cid][carton_qty];
							$air_qty+=$export_invoice_arr[$row[csf('po_id')]][$cid][2]['air'];
						
							//echo $row[csf('po_id')].',';
						
							$sea_qty+=$export_invoice_arr[$row[csf('po_id')]][$cid][1]['sea'];
							$invo_sea_qty+=$invoice_data_array[$row[csf('po_id')]][$cid][1]['sea_qnty'];
							$invo_air_qty+=$invoice_data_array[$row[csf('po_id')]][$cid][2]['air_qnty'];
							
							if($sewing_com=="") $sewing_com=$sewing_data_array[$row[csf('po_id')]][$cid]['sewing_com'];else $sewing_com.=",".$sewing_data_array[$row[csf('po_id')]][$cid]['sewing_com'];
						 }
						// if($invoice_date=='')  else $invoice_date.=",".$export_invoice_date_arr[$row[csf('po_id')]][$cid]['date'];
						$invoice_date=$export_invoice_date_arr[$row[csf('po_id')]]['date'];
						//echo $mon_ship[$row[csf('po_id')]][$row[csf('country_ship_date')]];
						$all_invoice_date=$invoice_date;
						$all_invoicedate=array_unique(explode(",",$all_invoice_date));
						
						// echo $invoice_date;
						$tot_invoice_qty=($invo_sea_qty+$invo_air_qty)/$row[csf('ratio')];
						$ship_qty_air_sea=($sea_qty+$air_qty)/$row[csf('ratio')];
						//echo $po_cm_cost;
						$po_cm_cost=$other_costing_arr[$row[csf('po_id')]]['cm_cost'];
						$yarn_counts="";
						 foreach($fab_deter_id as $yid)
						 {
							if($yarn_counts=="") $yarn_counts=$yarn_count_library[$fabric_des_data_arr[$yid]['count']];else $yarn_counts.=",".$yarn_count_library[$fabric_des_data_arr[$yid]['count']];
							 //$fab_count_id=$fabric_des_data_arr[$fab_deter_id]['count']; 
						 }
						 $ship_qty=$ship_qty_air_sea;//$sea_qty+$air_qty;//$exfactory_data_array[$row[csf('po_id')]][ex_fact_qty]/$row[csf('ratio')];
						 $short_excess_qty=$ship_qty_air_sea-$row[csf('order_quantity')];
						 $lab_dip_no=explode(",",$lab_dip_data_arr[$row[csf('po_id')]]['ldip_id']);
						 $lab_dip='';
						 foreach($lab_dip_no as $lap_id )
						 {
							if($lab_dip=='') $lab_dip=$lap_id;else $lab_dip.=", ".$lap_id;
						 }
							
							$date_diff='';//$shim_mon='';
							foreach($all_invoicedate as $sdate)
							{
								//echo  $sdate.'ff';
								if($date_diff=='') $date_diff=datediff( "d", $sdate , $row[csf('pub_shipment_date')]);else $date_diff.=",".datediff( "d", $sdate , $row[csf('pub_shipment_date')]);
							}
							//if($row[csf('order_quantity')]=='NAN') $row[csf('order_quantity')]=0;else $row[csf('order_quantity')]=$row[csf('order_quantity')];
							//if($row[csf('order_total')]=='NAN') $row[csf('order_total')]=0;else $row[csf('order_total')]=$row[csf('order_total')];
							$po_total_price=$row[csf('order_total')];
							$po_quantity=$row[csf('order_quantity')];
							$job_quantity=$row[csf('job_quantity')];
							//$po_quantity=$row[csf('po_quantity')];
							$job_total_price=$row[csf('total_price')];
							$tot_po_qty=$country_arr[$row[csf('job_no')]]['po_qty'];
							$po_qty=$po_country_arr[$row[csf('po_id')]]['po_qty'];//$po_country_arr[$row[csf('po_id')]]['po_qty']
							$country_ship_qty=$country_shipdate_arr[$row[csf('po_id')]][$row[csf('country_ship_date')]]['po_qty'];
							$avg_unit=$po_total_price/$po_quantity;
							$ship_val=$ship_qty*$avg_unit;//$seaval+$airval;
							$tot_fob_price=$row[csf('order_quantity')]*$avg_unit;
							$wo_booking_cost=$po_wo_cost_arr[$row[csf('po_id')]]['amount'];
							if($wo_booking_cost=='') $wo_booking_cost=0;else $wo_booking_cost=$wo_booking_cost;
							$lab_cost=$lab_po_data_cost_arr[$row[csf('po_id')]]['lab'];
							if($lab_cost=='') $lab_cost=0;else $lab_cost=$lab_cost;
							//echo $row[csf('order_quantity')].'ff';
							$tot_lab_raw=($lab_cost/$row[csf('po_quantity')])*$row[csf('order_quantity')];
							$gen_access_cost=$gen_acces_data_cost_arr[$row[csf('po_id')]]['amt'];
							if($gen_access_cost=='') $gen_access_cost=0;else $gen_access_cost=$gen_access_cost;
							$tot_gen_access_raw=($gen_access_cost/$row[csf('po_quantity')])*$row[csf('order_quantity')];
							
							$fab_finish=$fabric_booking_data_arr[$row[csf('po_id')]]['fin_qnty'];
							$fab_finish_kg=$fabric_booking_data_arr_uom_wise[$row[csf('po_id')]][12]['fin_qnty'];
							$fab_finish_yds=$fabric_booking_data_arr_uom_wise[$row[csf('po_id')]][27]['fin_qnty'];
							$fab_finish_mtr=$fabric_booking_data_arr_uom_wise[$row[csf('po_id')]][23]['fin_qnty'];

							if($fab_finish=='') $fab_finish=0;else $fab_finish=$fab_finish;
							//echo $fab_finish.'='.'**'.$po_qty.'**'.$country_ship_qty.'<br>';
							$fab_finish_req=($fab_finish/$row[csf('po_quantity')])*$country_ship_qty;
							
							$foreign_comm=$commission_costing_arr[$row[csf('po_id')]][1];
							$local_comm=$commission_costing_arr[$row[csf('po_id')]][2];
							$tot_commisssion=($foreign_comm+$local_comm);
							$tot_commisssion_raw=($tot_commisssion/$row[csf('po_quantity')])*$row[csf('order_quantity')];
							$cm_cost=0;
							$cm_cost=$pre_cost_cm_margin_arr[$row[csf('job_no')]]['cm'];
							//echo $tot_commisssion.'='.$po_quantity.',';
							$tot_commisssion_dzn=($tot_commisssion_raw/$po_quantity)*12;
							$cm_us_dzn=($job_total_price/$job_quantity)*12;
							//echo $wo_booking_cost.'='.$tot_lab_raw.'='.$tot_gen_access_raw.'<br>';
							$tot_booking_raw=($wo_booking_cost/$row[csf('po_quantity')])*$row[csf('order_quantity')];
							$tot_raw_materials_cost=0;
							$tot_raw_materials_cost=$tot_booking_raw+$tot_lab_raw+$tot_gen_access_raw;
								//echo $tot_booking_raw.'='.$tot_lab_raw.'='.$tot_gen_access_raw.'<br>';
							$tot_raw_material=($tot_raw_materials_cost/$po_quantity)*12;
						
							$cm_margin_dzn=$cm_us_dzn-($tot_raw_material+$cm_cost+$tot_commisssion_dzn);
							$cm_margin_val=($cm_margin_dzn/12)*$row[csf('order_quantity')];
							 $fab_des_button="<a href='##' onClick=\"generate_order_report('".$row[csf('po_id')]."','".$row[csf('company_id')]."','".$row[csf('job_no')]."','".$row[csf('buyer_name')]."','".$row[csf('style_ref_no')]."','".$row[csf('country_ship_date')]."','".$row[csf('country_id')]."','show_po_fabric_dtls','5')\"> ".$fab_des." </a>";

							 $fab_des_button_kg="<a href='##' onClick=\"generate_order_report('".$row[csf('po_id')]."','".$row[csf('company_id')]."','".$row[csf('job_no')]."','".$row[csf('buyer_name')]."','".$row[csf('style_ref_no')]."','".$row[csf('country_ship_date')]."','".$row[csf('country_id')]."','show_po_fabric_dtls','5__12__".$row[csf('actual_po_id')]."')\"> ".number_format($fab_finish_kg,2)." </a>";

							 $fab_des_button_yds="<a href='##' onClick=\"generate_order_report('".$row[csf('po_id')]."','".$row[csf('company_id')]."','".$row[csf('job_no')]."','".$row[csf('buyer_name')]."','".$row[csf('style_ref_no')]."','".$row[csf('country_ship_date')]."','".$row[csf('country_id')]."','show_po_fabric_dtls','5__27__".$row[csf('actual_po_id')]."')\"> ".number_format($fab_finish_yds,2)." </a>";

							 $fab_des_button_mtr="<a href='##' onClick=\"generate_order_report('".$row[csf('po_id')]."','".$row[csf('company_id')]."','".$row[csf('job_no')]."','".$row[csf('buyer_name')]."','".$row[csf('style_ref_no')]."','".$row[csf('country_ship_date')]."','".$row[csf('country_id')]."','show_po_fabric_dtls','5__23__".$row[csf('actual_po_id')]."')\"> ".number_format($fab_finish_mtr,2)." </a>";

							
							 $fab_des_button2="<a href='##' onClick=\"generate_order_report('".$row[csf('po_id')]."','".$row[csf('company_id')]."','".$row[csf('job_no')]."','".$row[csf('buyer_name')]."','".$row[csf('style_ref_no')]."','".$row[csf('country_ship_date')]."','".$row[csf('country_id')]."','show_po_fabric_dtls','5')\"> ".number_format($fab_finish_req,2)." </a>";
							  $ship_date_button="<a href='##' onClick=\"generate_order_report('".$row[csf('po_id')]."','".$row[csf('company_id')]."','".$row[csf('job_no')]."','".$row[csf('buyer_name')]."','".$row[csf('style_ref_no')]."','".$row[csf('country_ship_date')]."','".$row[csf('country_id')]."','show_po_ship_date_dtls','6')\"> ".change_date_format($row[csf('country_ship_date')])." </a>";
							   $tot_ship_qty_button="<a href='##' onClick=\"generate_order_report('".$row[csf('po_id')]."','".$row[csf('company_id')]."','".$row[csf('job_no')]."','".$row[csf('buyer_name')]."','".$row[csf('style_ref_no')]."','".$row[csf('country_ship_date')]."','".$row[csf('country_id')]."','show_po_ship_qty_dtls','7')\"> ".number_format($ship_qty,0)." </a>";
							    $invoice_qty_button="<a href='##' onClick=\"generate_order_report('".$row[csf('po_id')]."','".$row[csf('company_id')]."','".$row[csf('job_no')]."','".$row[csf('buyer_name')]."','".$row[csf('style_ref_no')]."','".$row[csf('country_ship_date')]."','".$row[csf('country_id')]."','show_invo_ship_qty_dtls','8')\"> ".number_format($tot_invoice_qty,0)." </a>";
							 $profit_except_cm=0;//$total_profit_except_oh=0;//$row[csf('country_id')]
							
							//$profit_except_cm=$cm_margin_dzn+$cm_cost;
							//echo $cm_us_dzn.'='.$tot_raw_material.'+'.$tot_commisssion_dzn;
							$profit_except_cm=($cm_us_dzn-($tot_raw_material+$tot_commisssion_dzn));
							//echo $profit_except_cm.'d';
							 $total_profit_except_oh=($profit_except_cm/12)*$row[csf('order_quantity')];
							//if($total_profit_except_oh==NAN) echo "aziz"; //$total_profit_except_oh=0;else $total_profit_except_oh=$total_profit_except_oh;
							
							$booking_nos=rtrim($fabric_booking_data_arr[$row[csf('po_id')]]['booking_no'],',');
							$booking_numbers=array_unique(explode(",",$booking_nos));
							$button_setting='';
							foreach($booking_numbers as $bookno)
							{
								//echo $bookno.'M';
								$item_category=$fabric_booking_data2_arr[$bookno]['item_category'];
								$booking_type=$fabric_booking_data2_arr[$bookno]['booking_type'];
								$fabric_source=$fabric_booking_data2_arr[$bookno]['fabric_source'];
								$is_short=$fabric_booking_data2_arr[$bookno]['is_short'];
								$entry_form=$fabric_booking_data2_arr[$bookno]['entry_form'];
								$is_approved=$fabric_booking_data2_arr[$bookno]['is_approved'];
								
								if($booking_type==1 && $entry_form==108 && $is_short==2){
									$row_id=$format_ids_partial[0];
								}
								else if($booking_type==1 && $entry_form==118 && $is_short==2){
									$row_id=$format_ids[0];
								}
								else
								{
									$row_id=$print_report_ids_short[0];
								}
								//echo $row_id.'='. $is_short.'='. $entry_form.'<br>';
								if($row_id==1)
									{ 
									$button_setting.="<a href='#' onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report_gr','".$i."')\"> ".$bookno." <a/>".',';
									}
									else if($row_id==2)
									{ 
									 
									 $button_setting.="<a href='#' onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report','".$i."')\">  ".$bookno. "<a/>".',';
									}
								 	else if($row_id==3)
									{ 
									 
									  $button_setting.="<a href='#' onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report3','".$i."')\"> ".$bookno." <a/>".',';
									}
									else if($row_id==4)
									{ 
									 $button_setting.="<a href='#' onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report1','".$i."')\">" .$bookno. "<a/>".',';
									}
								   	else if($row_id==5)
									{ 
									 $button_setting.="<a href='#' onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report2','".$i."')\"> ".$bookno." <a/>".',';
									}
									else if($row_id==6)
									{ 
									 $button_setting.="<a href='#' onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report4','".$i."')\"> ".$bookno." <a/>".',';
									}
								   	else if($row_id==7)
									{ 
									 $button_setting.="<a href='#' onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report5','".$i."')\"> ".$bookno." <a/>".',';
								   	}
									else if($row_id==8)
									{ 
									 $button_setting.="<a href='#' onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report','".$i."')\"> ".$bookno." <a/>".',';
								   	}
									else if($row_id==9)
									{ 
									 $button_setting.="<a href='#' onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report3','".$i."')\"> ".$bookno." <a/>".',';
								   	}
									
								   	else if($row_id==45) 
									{ 
									 $button_setting.="<a href='#' onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report_urmi','".$i."')\"> ".$bookno." <a/>".',';
									}
								   	else  if($row_id==53)
									{ 
									 $button_setting.="<a href='#'  onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report_jk','".$i."')\"> ".$bookno." <a/>".',';
									}
								   	else if($row_id==93)
									{ 
									 $button_setting.="<a href='#'  onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report_libas','".$i."')\"> ".$bookno." <a/>".',';
									}
								   else if($row_id==73)
									{ 
									 $button_setting.="<a href='#'  onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report_mf','".$i."')\"> ".$bookno." <a/>".',';
									}
								    else if($row_id==85)
									{ 
									 $button_setting.="<a href='#'  onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','print_booking_3','".$i."')\"> ".$bookno."<a/>".',';
									 }
									 else if($row_id==84)
									{ 
									 $button_setting.="<a href='#'  onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report_urmi_per_job','".$i."')\"> ".$bookno."<a/>".',';
									 }
									else if($row_id==143)
									{ 
									 $button_setting.="<a href='#'  onClick=\"generate_worder_report('".$bookno."','".$row[csf('company_id')]."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$row[csf('job_no')]."','".$is_approved."','".$row_id."','show_fabric_booking_report_urmi','".$i."')\"> ".$bookno."<a/>".',';
									 }
							}
							
						
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
							<td width="30"><? echo $i; ?></td>
							<td width="40" title="Pub Ship Date Year"><? echo date("Y",strtotime($row[csf('pub_shipment_date')])); ?></td>
							<td width="90"  title="Pub Ship Date Month" align="center"><p><? echo date("F",strtotime($row[csf('pub_shipment_date')])); ?></p></td>
							<td width="80" ><div style="word-break:break-all"><? echo $team_leader_library
	[$row[csf('team_leader')]]; ?></div></td>
							<td width="80"><div style="word-break:break-all"><? echo $dealing_merchant_array[$row[csf('dealing_marchant')]]; ?></div></td>
                            <td width="80"><div style="word-break:break-all"><? echo $company_library[$row[csf('style_owner')]]; ?></div></td>
							<td width="100"><div style="word-wrap:break-word:100px;"><? echo $product_dept[$row[csf('product_dept')]]; ?></div></td>
							<td width="50"><div style="word-wrap:break-word:50px;"><? echo $row[csf('job_prefix')]; ?></div></td>
                            <td width="130"><div style="word-break:break-all"><? echo $row[csf('style_ref_no')]; ?></div></td>
							<td width="100" title="<? echo $row[csf('job_no')];?>"> <div style="word-break:break-all"> <? echo $row[csf('po_number')]; ?>  </div>	</td>
                            <td width="80" align="center" > <div style="word-break:break-all"><? echo $country_name; ?> </div>
							</td>
							
							<td width="100" align="left"><div style="word-break:break-all"><?  $buyer_name_str=$buyer_library[$row[csf('buyer_name')]]; echo $buyer_name_str; ?> </div></td>
							<td width="100" align="left"><div style="word-break:break-all"><? $client_name_str=$buyer_library[$row[csf('client_id')]]; echo $client_name_str; ?> </div></td>
							<td width="80" align="center">
                            <div style="word-wrap:break-word:80px;">
							 <? echo $season_name_library[$row[csf('season_matrix')]];?>
                             </div>
							</td>
							<td width="130" align="left"><div style="word-break:break-all">
							<?
							$gmts_item='';$body_item=''; $gsm_item=''; $gmts_item_id=array_unique(explode(",",$row[csf('gmts_item_id')]));
							foreach($gmts_item_id as $item_id)
							{
								if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
								if($body_item=="") $body_item=$body_part[$pre_cost_data_arr[$row[csf('job_no')]][$item_id]['body']]; else $body_item.=",".$body_part[$pre_cost_data_arr[$row[csf('job_no')]][$item_id]['body']];
								if($gsm_item=="") $gsm_item=$pre_cost_data_arr[$row[csf('job_no')]][$item_id]['gsm']; else $gsm_item.=",".$pre_cost_data_arr[$row[csf('job_no')]][$item_id]['gsm'];
							}
									echo $gmts_item; 
									$order_qty_pcs=$row[csf('order_quantity_pcs')];///$row[csf('ratio')];
									?>
                                    </div>
							</td>
							<td width="150"  align="center"><div style="word-break:break-all">
							<? echo  $fab_des_button;
							$booking_no=rtrim($fabric_booking_data_arr[$row[csf('po_id')]]['booking_no'],',');
							$booking_no=implode(',',array_unique(explode(',',$booking_no)));
							
							$supplier_ids=rtrim($fabric_booking_data_arr[$row[csf('po_id')]]['supplier_id'],',');
							$supplier_company=implode(',',array_unique(explode(",",$supplier_ids)));
							
							$export_lc=rtrim($export_lc_data_array[$row[csf('po_id')]][lc_no],',');
							$export_lc=implode(',',array_unique(explode(',',$export_lc)));
							//$export_sc_data_array[$row[csf('po_id')]][sc_no]
							$export_sc=rtrim($export_sc_data_array[$row[csf('po_id')]][sc_no],',');
							$export_sc=implode(',',array_unique(explode(',',$export_sc)));
							//echo $row[csf('po_id')].'='.$export_sc;
							$profit_except_cm=($cm_us_dzn-($tot_raw_material+$tot_commisssion_dzn));
							
							//$export_lc=rtrim($export_lc_data_array[$row[csf('po_id')]][lc_no],',');
							//$export_lc=implode(",",array_unique(explode(",",$export_lc)));
							
							//$export_sc=rtrim($export_sc_data_array[$row[csf('po_id')]][sc_no],',');
							//$export_sc=implode(",",array_unique(explode(",",$export_sc)));
							
							if(!$export_lc) $export_lc=$export_sc;
							
							
							
							$lrevised_date=$revised_date_arr[$row[csf('po_id')]][$row[csf('country_ship_date')]]['lship_date'];
							$forig_date=$revised_date_arr[$row[csf('po_id')]][$row[csf('country_ship_date')]]['forig_date'];
							if($forig_date!='')
							{
								$origin_ship_date=$row[csf('country_ship_date')];	
							}
							else
							{
								$origin_ship_date='';
							}
							if($lrevised_date!='')
							{
								$last_ship_date=$row[csf('shipment_date')];//$lrevised_date[0];	
							}
							else
							{
								$last_ship_date='';
							}
							 ?>
                            
                            </div></td>
							<td width="200" ><div style="word-break:break-all"><? echo $fab_composiiton; ?></div></td>
                            <td width="150" ><div style="word-break:break-all"><? echo rtrim($button_setting,','); ?></div></td>
                            <td width="100" ><div style="word-break:break-all"><? echo $supplier_company; ?></div></td>
							<td width="50" align="center"><div style="word-break:break-all"><? echo $fab_gsm; ?></div></td>
							<td width="70" align="right"><div style="word-break:break-all"><? echo number_format($row[csf('order_quantity')],0); ?></div></td>
							<td width="50" align="center"><div style="word-break:break-all"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></div></td>
							<td width="80" align="right"> <p> <? echo number_format($order_qty_pcs,0); ?>   </p>
							<td width="40" align="right" > <? echo $row[csf('ratio')]; ?>  </td>
                            
							<td width="80" align="right" title="Ex Fact qty-Country Qty"> <? echo  number_format($short_excess_qty,2); ?></td>
                            <td width="80" align="right"> <? echo  $ship_date_button;//change_date_format($row[csf('country_ship_date')]); ?></td>
                            <td width="80" align="right"> <? echo  number_format($air_qty/$row[csf('ratio')],0); ?></td>
                            <td width="80" align="right"> <? echo  number_format($sea_qty/$row[csf('ratio')],0); ?></td>
                            <td width="90" title="Air+Sea Qty" align="right"><? echo  $tot_ship_qty_button;//number_format($ship_qty,0); ?></td>
							<td width="80"   align="center" title="First Origin Date"> <? echo change_date_format($last_ship_date); ?></td>
                            <td width="80" title="Invoice Qty" align="right"><? echo  $invoice_qty_button;//number_format($ship_qty,0); ?></td>
							<td width="80" align="center" title="Last Revised Date"><div style="word-break:break-all"><? 
							//$revised_date=explode(" ",$revised_date_arr[$row[csf('po_id')]]['ship_date']);
							echo change_date_format($origin_ship_date);
							$ttl_commission_cost=($row[csf('order_quantity')]*$tot_commisssion_dzn)/12;
							$ttl_profit_base_on_ship=($ship_val+$row[csf('up_charge')])-($ttl_commission_cost+$tot_raw_materials_cost);
							
							//echo $revised_date_arr[$row[csf('po_id')]]['ship_date']; ?> </div></td>
							<td width="80" align="center"><? echo change_date_format($all_invoice_date); ?> </td>
							<td width="60" title="FOB Value/PO Qty" align="right"><? echo number_format($avg_unit,2); ?></td>
							<td width="110"  align="left"><div style="word-break:break-all"><? echo $export_lc; ?> &nbsp;</div></td>
                             <td width="70" align="right" title="Total Commission=Tot Comision(<? echo number_format($tot_commisssion_raw,2);?>)/Country Qty*12"> <? echo  number_format($tot_commisssion_dzn,2); ?></td>
                             <td width="100" align="right" title="<? echo 'Total Raw Material='.number_format($tot_raw_materials_cost,2); ?>"> <? echo  number_format($tot_raw_material,2); ?></td>
                            <td width="70" align="right" title="<? echo 'Total OH='.number_format($po_cm_cost,2);?>"><div style="word-break:break-all"><? echo number_format($cm_cost,2); ?></div></td>
							<td width="70" align="right" title="FOB Value-(Raw Material Cost(<? echo number_format($tot_raw_materials_cost,2)?>)+Overhead cost(<? echo number_format($cm_cost,2);?>+ commission(<? echo number_format($tot_commisssion_raw,2);?>))/Country Qty X 12"><div style="word-break:break-all"><? echo number_format($cm_margin_dzn,2); ?></div></td>
                            <td width="70" align="right" title="{FOB value-(Raw material cost + commission)}/Country Qty X 12"><div style="word-break:break-all"><? echo number_format($profit_except_cm,2); ?></div></td>
							 <td width="70" align="right" title="TTL Profit based on ship/12">
							 <div style="word-break:break-all"><? $tot_profit_base_on_ship=$ttl_profit_base_on_ship/12;echo number_format($tot_profit_base_on_ship,2); ?></div></td>
                            
							<td width="60" title="Invoice Date-Ori. Ship Date"  align="center"><div style="word-break:break-all"><? echo number_format($date_diff);?></div></td>
							<td width="60"  align="center"><div style="word-break:break-all"><? 
							if($date_diff<0)
							{
								$early_status='Delay'; 
							}
							if($date_diff>0)
							{
								$early_status='Early';
							}
							if($date_diff==0)
							{
								$early_status='Ontime';
							}
							if($date_diff=='')
							{
								$early_status='';
							}
							echo $early_status; 
							//$sewingCompany=rtrim($sewing_data_array[$row[csf('po_id')]][$row[csf('country_id')]]['sewing_com'],',');
							$sewingCompany=implode(',',array_unique(explode(',',$sewing_com)));
							 ?></div></td>
							<td width="70" align="right" title="PO Qty*Commission Dzn/12"><div style="word-break:break-all"><?  echo number_format($ttl_commission_cost,2);?></div></td>
							<td width="100" align="right" title="Country Qty*Unit Price"><div style="word-break:break-all"><?  echo number_format($tot_fob_price,2);?></div></td>
							<td width="100" align="right" title="Air+sea Qty*Unit Price"> <p> <? $tot_ex_fact_val=$ship_val;echo number_format($ship_val,2); ?>   </p>
							<td width="100" title="Ship Val-FOB Value" align="right"><? $tot_short_excess_val=$ship_val-$tot_fob_price; echo number_format($tot_short_excess_val,2); ?> </td>
							<td width="80" title="Raw material Cost" align="right" ><p><? echo number_format($tot_raw_materials_cost,2); ?></p></td>
                            <td width="90" title="CM US/12*Country Qty" align="right" ><p><? echo number_format($cm_margin_val,2); ?></p></td>
                            <td width="90" title="Profit Except OH/12*Country Qty" align="right" ><p><? echo number_format($total_profit_except_oh,2); ?></p></td>
							<td width="90" title="Ship Value+Up Charge-TTL Commission-Tot Raw Material" align="right" ><p><? echo number_format($ttl_profit_base_on_ship,2); ?></p></td>
							<td width="70" align="center"> <div style="word-wrap:break-word:70px;"><? echo $quality_label[$row[csf('qlty_label')]]; ?></div></td>
							<td width="70" align="right"> <p><? echo number_format($ex_fact_carton_qty); ?> </p> </td>
							<td width="80" align="right" ><p> <? echo number_format($row[csf('up_charge')],2); ?>  </p></td>
							<td width="100" align="left"><div style="word-wrap:break-word:100px;"><? echo $lab_dip; ?></div></td>
							<td width="80" align="right"><div style="word-wrap:break-word:100px;"><? echo $sewingCompany; ?> </div></td>
							<td width="80" title="Ex_fact Date"   align="center"><?
						
							 $invoice_datess=$all_invoice_date;
							 if($invoice_datess){
							 $dd= date("F",strtotime($invoice_datess));
							 echo $dd;
							 }
							//echo $dd;
							 ?> </td>
							<td width="80" align="right" title="Booking Qty(<? echo number_format($fab_finish,2); ?>)/PO Qty(<? echo number_format($po_quantity,0); ?>)*Country Qty(<? echo number_format($country_ship_qty,2); ?>)"><? echo $fab_des_button_kg;//number_format($fab_finish_req,2); ?> </td>

							<td width="100" align="right" title="Booking Qty(<? echo number_format($fab_finish,2); ?>)/PO Qty(<? echo number_format($po_quantity,0); ?>)*Country Qty(<? echo number_format($country_ship_qty,2); ?>)"><? echo $fab_des_button_yds;//number_format($fab_finish_req,2); ?> </td>

							<td width="100" align="right" title="Booking Qty(<? echo number_format($fab_finish,2); ?>)/PO Qty(<? echo number_format($po_quantity,0); ?>)*Country Qty(<? echo number_format($country_ship_qty,2); ?>)"><? echo $fab_des_button_mtr;//number_format($fab_finish_req,2); ?> </td>
							<td width=""  align="right"><? echo number_format($row[csf('set_smv')],2); ?></td>
							<td width="70"  align="right" title="SMV*Tot Ship Qty"><? $sales_minute=$row[csf('set_smv')]*$ship_qty; echo number_format($sales_minute,2); ?></td>
						</tr>
					<?
					$total_po_qty+=$row[csf('order_quantity')];
					$total_ex_fact_qty+=$ex_fact_qty;
					$total_invoice_qty+=$tot_invoice_qty;
					$total_tot_fob_price+=$tot_fob_price;
					$total_tot_air+=$air_qty/$row[csf('ratio')];
					$total_tot_sea+=$sea_qty/$row[csf('ratio')];
					$total_cm_margin_val+=$cm_margin_val;
					$total_up_profit_except_oh+=$total_profit_except_oh;
					$total_up_charge+=$row[csf('up_charge')];
					$total_fab_finish_req+=$fab_finish_req;
					$total_raw_materials_cost+=$tot_raw_materials_cost;
					$total_ttl_commission_cost+=$ttl_commission_cost;
					$total_tot_profit_base_on_ship+=$ttl_profit_base_on_ship;
					$total_sales_minute+=$sales_minute;
					$i++;
					}
					?>
					</table>
                    </div>
					<table class="rpt_table" width="5300" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
						<tfoot>
							<th width="30">&nbsp;</th>
							<th width="40">&nbsp;</th>
							<th width="90">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="80">&nbsp;</th>
                            <th width="80">&nbsp;</th>
                            <th width="100">&nbsp;</th>
							<th width="50">&nbsp;</th>
							<th width="130">&nbsp;</th>
							<th width="100">&nbsp;</th>
							
                            <th width="80">&nbsp;</th>
						
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
						
							<th width="80">&nbsp;</th>
							<th width="130">&nbsp;</th>
							
							<th width="150">&nbsp;</th>
                            
							<th width="200">&nbsp;</th>
                            <th width="150">&nbsp;</th>
                            <th width="100">&nbsp;</th>
							<th width="50">Total</th>
							<th width="70">&nbsp;</th>
							<th width="50">&nbsp;</th>
							<th width="80"  id="total_po_qty"><? echo number_format($total_po_qty,0); ?></th>
							<th width="40">&nbsp;</th>
                            
							<th width="80">&nbsp;<? //echo number_format($total_tot_ex_fact_val); ?></th>
                            <th width="80">&nbsp;<? //echo number_format($total_tot_ex_fact_val); ?></th>
                            <th width="80" id="total_ship_qty_air"><? echo number_format($total_tot_air,0); ?></th>
                            <th width="80" id="total_ship_qty_sea"><? echo number_format($total_tot_sea,0); ?></th>
                            <th width="90" id="total_ship_qty"><? echo number_format($total_tot_sea+$total_tot_air,0); ?></th>
                           
                            
							<th width="80">&nbsp;</th>
                            <th width="80" id="total_invoice_qty"><? echo number_format($total_invoice_qty,0); ?></th>
							<th width="80">&nbsp;<? //echo number_format($total_tot_ex_fact_val); ?></th>
							<th width="80">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="110">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="100">&nbsp;</th>
							<th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
							<th width="70">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="70"><? echo number_format($total_ttl_commission_cost,2);//echo $total_cm_margin_val; ?></th>
							<th width="100" id="value_total_fob_price"><? echo number_format($total_tot_fob_price,2);//echo $total_cm_margin_val; ?></th>
							<th width="100" id="value_total_ex_fact_val"><? echo number_format($total_tot_ex_fact_val,2); ?></th>
							<th width="100">&nbsp;</th>
                            <th width="80" id="value_total_raw_materials_cost"><? echo number_format($total_raw_materials_cost,2); ?></th>
							<th width="90" id="value_total_cm_margin_val"><? echo number_format($total_cm_margin_val,2); ?></th>
                            <th width="90" id="value_total_profit_except_val"><? echo number_format($total_up_profit_except_oh,2); ?></th>
							<th width="90"><? echo number_format($total_tot_profit_base_on_ship,2); ?></th>
							<th width="70">&nbsp;</th>
							
							
							<th width="70">&nbsp;</th>
							<th width="80" id="value_total_up_charge"><? echo number_format($total_up_charge,2); ?></th>
							<th width="100">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="80" id="value_total_fab_finish_req1"><? echo number_format($total_fab_finish_req,2); ?></th>
							<th width="100" id="value_total_fab_finish_req2"><? echo number_format($total_fab_finish_req,2); ?></th>
							<th width="100" id="value_total_fab_finish_req3"><? echo number_format($total_fab_finish_req,2); ?></th>
							<th>&nbsp;</th>
							<th width="70"><? echo number_format($total_sales_minute,2); ?></th>
						</tfoot>
					</table>
				
				</fieldset>
			</div>
	<?
		}
	} // Country End
	
	/*$html = ob_get_contents();
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
   echo "$html****$filename****$report_type"; */
	echo "$total_data****$filename****$report_type";
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
                	<td colspan="5" align="center"><strong> Lhhhabdip Details </strong></td>
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