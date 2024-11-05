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
$buyer_library=return_library_array( "select id, buyer_name from lib_buyer where is_deleted=0 order by sequence_no", "id", "buyer_name"  );
$buyer_short_name_library=return_library_array( "select id, buyer_name from lib_buyer where is_deleted=0 order by sequence_no", "id", "buyer_name"  );
$season_name_library=return_library_array( "select id, season_name from  lib_buyer_season", "id", "season_name"  );

//$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
$dealing_merchant_array = return_library_array("select id, team_member_name from lib_mkt_team_member_info","id","team_member_name");
$team_leader_library=return_library_array( "select id, team_leader_name from lib_marketing_team", "id", "team_leader_name");
//$costing_library=return_library_array( "select job_no, costing_date from wo_pre_cost_mst", "job_no", "costing_date"  );
$supplier_library=return_library_array( "select id,short_name from lib_supplier", "id","short_name");
$country_name_library=return_library_array( "select id,country_name from lib_country", "id","country_name");

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "load_drop_down( 'requires/fabric_wise_fab_req_report_controller', this.value, 'load_drop_down_season', 'season_td'); " );     	 
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'fabric_wise_fab_req_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	
	echo  create_list_view("list_view", "Job No,Buyer,Style Ref.,Order No", "110,110,150,180","610","350",0, $sql, "js_set_value", "id,po_number", "", 1, "0,buyer_name,0,0,0", $arr , "job_no_prefix_num,buyer_name,style_ref_no,po_number", "fabric_wise_fab_req_report_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0','') ;
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
	
	echo  create_list_view("list_view", "Job No,Buyer,Style Ref.,Order No,Actual Po", "110,110,150,130,100","610","350",0, $sql, "js_set_value", "id,acc_po_no", "", 1, "0,buyer_name,0,0,0,0", $arr , "job_no_prefix_num,buyer_name,style_ref_no,po_number,acc_po_no", "fabric_wise_fab_req_report_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0','') ;
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
	$yarn_determin_library=return_library_array( "select id,genetic_name from lib_yarn_count_determina_mst where genetic_name is not null ", "id","genetic_name");

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
	
	

	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
//	echo $date_from;die;
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
			//$date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
			$c_date_cond=" and c.country_ship_date between '$start_date' and '$end_date'";
			//$date_max_profit=" and applying_period_to_date between '$start_date' and '$end_date'";
		}
	}

//echo $c_date_cond;die;
	
		
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
	//echo "AAAAAAA";
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
	
	
	
  $sql_po="SELECT a.id,a.job_no, a.buyer_name,a.total_set_qnty as ratio,a.avg_unit_price,a.style_ref_no,b.id as po_id,b.pub_shipment_date,a.order_uom,b.po_number,b.po_quantity,b.is_confirmed,c.item_number_id,c.country_id,c.item_number_id as item_id,c.plan_cut_qnty,c.country_ship_date,c.color_number_id,c.size_number_id,c.order_quantity,c.plan_cut_qnty,d.gsm_weight,d.composition,d.body_part_id,d.fabric_description,d.construction,d.lib_yarn_count_deter_id as deter_id,d.width_dia_type, d.id as fabric_id, e.dia_width, f.sequence_no  from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e, lib_buyer f where  1=1 and a.id=b.job_id and a.buyer_name=f.id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id  and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and  c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes    and e.cons !=0   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1  and e.is_deleted=0 and e.status_active=1  $company_name_cond $job_no_cond $year_cond  $c_date_cond $order_id_cond_trans  $order_no_cond $internal_ref_cond $file_no_cond $buyer_id_cond $order_status_cond order by f.sequence_no, b.id,c.country_ship_date"; 
	//echo $sql_po; die;
	$sql_po_data=sql_select($sql_po);
	$signle_part_arr=array(266,398,399,400);
	$double_part_arr=array(267,401,473);
	$without_process_arr=array(1,35,30);
	
	foreach($sql_po_data as $row)
	{
	$c_ship_date=$row[csf('country_ship_date')];
	$body_id=$row[csf('body_part_id')];
	$deter_id=$row[csf('deter_id')];$gsm=$row[csf('gsm_weight')];
	$dia_type=$row[csf('width_dia_type')];$dia=$row[csf('dia_width')];
	$process_part='';
	 foreach($conversion_cost_head_array as $process_id=>$val)
	 {
		if(in_array($process_id,$double_part_arr))
		{
		$process_part="Double part";
		}
		else if(in_array($process_id,$signle_part_arr))
		{
		$process_part="Signle part";
		}
		else //Other Process
		{
			if(!in_array($process_id,$without_process_arr))
			{
				$process_part="Others part";
			}
		}
	 }
	 
	
	$fab_wise_req_arr[$row[csf('po_id')]][$row[csf('item_id')]][$c_ship_date][$body_id][$deter_id][$dia_type][$dia][$gsm]['po_number']=$row[csf('po_number')];
	$fab_wise_req_arr[$row[csf('po_id')]][$row[csf('item_id')]][$c_ship_date][$body_id][$deter_id][$dia_type][$dia][$gsm]['order_uom']=$row[csf('order_uom')];
	$fab_wise_req_arr[$row[csf('po_id')]][$row[csf('item_id')]][$c_ship_date][$body_id][$deter_id][$dia_type][$dia][$gsm]['country_id']=$row[csf('country_id')];
	$fab_wise_req_arr[$row[csf('po_id')]][$row[csf('item_id')]][$c_ship_date][$body_id][$deter_id][$dia_type][$dia][$gsm]['po_quantity']=$row[csf('po_quantity')];
	$fab_wise_req_arr[$row[csf('po_id')]][$row[csf('item_id')]][$c_ship_date][$body_id][$deter_id][$dia_type][$dia][$gsm]['po_quantity_pcs']=$row[csf('po_quantity')]*$row[csf('ratio')];
	$fab_wise_req_arr[$row[csf('po_id')]][$row[csf('item_id')]][$c_ship_date][$body_id][$deter_id][$dia_type][$dia][$gsm]['po_qty_pcs']+=$row[csf('order_quantity')];

	$fab_wise_req_arr[$row[csf('po_id')]][$row[csf('item_id')]][$c_ship_date][$body_id][$deter_id][$dia_type][$dia][$gsm]['plan_cut']+=$row[csf('plan_cut_qnty')];
	$fab_wise_req_arr[$row[csf('po_id')]][$row[csf('item_id')]][$c_ship_date][$body_id][$deter_id][$dia_type][$dia][$gsm]['buyer_name']=$row[csf('buyer_name')];
	$fab_wise_req_arr[$row[csf('po_id')]][$row[csf('item_id')]][$c_ship_date][$body_id][$deter_id][$dia_type][$dia][$gsm]['style_ref']=$row[csf('style_ref_no')];
	$fab_wise_req_arr[$row[csf('po_id')]][$row[csf('item_id')]][$c_ship_date][$body_id][$deter_id][$dia_type][$dia][$gsm]['construction']=$row[csf('construction')];
	$fab_wise_req_arr[$row[csf('po_id')]][$row[csf('item_id')]][$c_ship_date][$body_id][$deter_id][$dia_type][$dia][$gsm]['job_no']=$row[csf('job_no')];
	$fab_wise_req_arr[$row[csf('po_id')]][$row[csf('item_id')]][$c_ship_date][$body_id][$deter_id][$dia_type][$dia][$gsm]['is_confirmed']=$row[csf('is_confirmed')];
	$fab_wise_req_arr[$row[csf('po_id')]][$row[csf('item_id')]][$c_ship_date][$body_id][$deter_id][$dia_type][$dia][$gsm]['desc']=$row[csf('fabric_description')];
	$fab_wise_req_arr[$row[csf('po_id')]][$row[csf('item_id')]][$c_ship_date][$body_id][$deter_id][$dia_type][$dia][$gsm]['construction']=$row[csf('construction')];
	$fab_wise_req_arr[$row[csf('po_id')]][$row[csf('item_id')]][$c_ship_date][$body_id][$deter_id][$dia_type][$dia][$gsm]['composition']=$row[csf('composition')];
	$fab_wise_req_arr[$row[csf('po_id')]][$row[csf('item_id')]][$c_ship_date][$body_id][$deter_id][$dia_type][$dia][$gsm]['fabric_id']=$row[csf('fabric_id')];
	$fab_wise_req_arr[$row[csf('po_id')]][$row[csf('item_id')]][$c_ship_date][$body_id][$deter_id][$dia_type][$dia][$gsm]['process_part']=$process_part;
	
	//$plan_cut_qty_arr[$row[csf('po_id')]][$row[csf('item_id')]][$c_ship_date]+=$row[csf('plan_cut_qnty')];
	$buyer_po_arr[$row[csf('po_id')]]=$row[csf('buyer_name')];
	$fabric_id_arr[$row[csf('fabric_id')]] = $row[csf('fabric_id')];
	//$buyer_po_qty_arr[$row[csf('buyer_name')]]+=$row[csf('plan_cut_qnty')];
	
	
	}
	//$fabric_id_string=implode(",", $fabric_id_arr);
	if(count($fabric_id_arr)>0)
	{
	   $fabric_id=array_chunk($fabric_id_arr,999, true);
	   $fabric_cond_in="";
	   $ji=0;
	   foreach($fabric_id as $key=> $value)
	   {
		   if($ji==0)
		   {
				$fabric_cond_in=" fabric_description in(".implode(",",$value).")"; 
				
		   }
		   else
		   {
				$fabric_cond_in.=" or fabric_description in(".implode(",",$value).")";
		   }
		   $ji++;
	   }
	}

	$dyeing_fin_process_status= array(158=>"Others",266=>"Single Part",267=>"Double Part");

	$conversions_arr=sql_select("SELECT fabric_description, cons_process from wo_pre_cost_fab_conv_cost_dtls where status_active=1 and is_deleted=0  and $fabric_cond_in");
	//echo "SELECT fabric_description, cons_process from wo_pre_cost_fab_conv_cost_dtls where status_active=1 and is_deleted=0 and cons_process in (158,266,267) and $fabric_cond_in"; die;//and cons_process in (158,266,267)

	foreach ($conversions_arr as $row) {
		$conversions_process[$row[csf('fabric_description')]]=$row[csf('cons_process')];
	}
	
	$sql_po_Plan="select a.id,a.job_no, a.buyer_name,a.total_set_qnty as ratio,a.avg_unit_price,a.style_ref_no,b.id as po_id,b.pub_shipment_date,b.po_quantity,b.is_confirmed,c.item_number_id,c.country_id,c.item_number_id as item_id,c.plan_cut_qnty,c.country_ship_date,c.color_number_id,c.size_number_id,c.order_quantity,c.plan_cut_qnty  from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where  1=1 and a.id=b.job_id and a.id=c.job_id   and b.id=c.po_break_down_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  $company_name_cond $job_no_cond $order_status_cond $year_cond  $c_date_cond $order_id_cond_trans $order_status_cond $order_no_cond $internal_ref_cond $file_no_cond $buyer_id_cond order by b.id,c.country_ship_date"; 
$sql_po_plan_data=sql_select($sql_po_Plan);
	foreach($sql_po_plan_data as $row)
	{
		$c_ship_date=$row[csf('country_ship_date')];
		$buyer_po_qty_arr[$row[csf('buyer_name')]]+=$row[csf('plan_cut_qnty')];
		$po_wise_req_arr[$row[csf('po_id')]][$row[csf('item_id')]][$c_ship_date]['plan_cut']+=$row[csf('plan_cut_qnty')];
		$po_wise_req_arr[$row[csf('po_id')]][$row[csf('item_id')]][$c_ship_date]['po_qty_pcs']+=$row[csf('order_quantity')];
	}
	unset($sql_po_plan_data);
	//print_r($buyer_po_qty_arr);
	 $condition= new condition();
	 $condition->company_name("=$cbo_company_name");
	 if(str_replace("'","",$cbo_buyer_name)>0){
		  $condition->buyer_name("=$cbo_buyer_name");
	 }
	 if(str_replace("'","",$txt_job_no) !=''){
		  $condition->job_no_prefix_num("=$txt_job_no");
	 }
	 if(str_replace("'","",$cbo_order_status) >0){
		 // $condition->is_confirmed("=$cbo_order_status");
	 }
	 if(str_replace("'","",$cbo_order_status)==0){
		//  $condition->is_confirmed("in(1,2)");
	 }
	 if(str_replace("'","",$cbo_search_date) ==1 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!=''){
		  $condition->country_ship_date(" between '$start_date' and '$end_date'");
	 }
	
	 if(str_replace("'","",$txt_order_no)!='')
	 {
		$condition->po_number("=$txt_order_no"); 
	 }
	
	 $condition->init();
	$fabric= new fabric($condition);
	$fabric_qty_arr=$fabric->getQtyArray_by_OrderItemidCountrydateBodypartDeterminIdDiatypeDiaAndGsm_knitAndwoven_greyAndfinish();
	$conversions = new conversion($condition);
	$conversions_qty_arr = $conversions->getQtyArray_by_OrderItemidCountrydateBodypartDeterminIdDiatypeDiaGsmAndProcess_knitAndwoven_greyAndfinish();
	/*echo '<pre>';
	print_r($conversions_qty_arr); die;*/
	
			$process_partArr=array();
	foreach($fab_wise_req_arr as $po_id=>$po_data)
	{
	 $po_row_span=0;
	  foreach($po_data as $item_id=>$item_data)
	   {  $po_item_row_span=0;
		   foreach($item_data as $c_date=>$cdate_data)
		   {
			   $po_item_cdate_row_span=0;
			
			foreach($cdate_data as $body_id=>$body_data)
			{
			foreach($body_data as $deterid=>$deter_data)
			{
			foreach($deter_data as $dia_type_id=>$dtype_data)
			{
			foreach($dtype_data as $dia_id=>$dia_data)
			 {
			 foreach($dia_data as $gsm_id=>$row)
			 {
				//echo $deterid.', ';
				 						
				$fab_qty_knit=array_sum($fabric_qty_arr['knit']['grey'][$po_id][$item_id][$c_date][$body_id][$deterid][$dia_type_id][$dia_id][$gsm_id]);
				//$fab_qty_woven=array_sum($fabric_qty_arr['woven']['grey'][$po_id][$item_id][$c_date][$body_id][$deterid][$dia_type_id][$dia_id][$gsm_id]);
				$grey_fab_req_qty=$fab_qty_knit+$fab_qty_woven;
						  
				$fab_qty_knit_fin=array_sum($fabric_qty_arr['knit']['finish'][$po_id][$item_id][$c_date][$body_id][$deterid][$dia_type_id][$dia_id][$gsm_id]);
				//$fab_qty_woven_fin=array_sum($fabric_qty_arr['woven']['finish'][$po_id][$item_id][$c_date][$body_id][$deterid][$dia_type_id][$dia_id][$gsm_id]);
				$fin_fab_req_qty=$fab_qty_knit_fin+$fab_qty_woven_fin;
				$con_fab_knit_fin_others=0;$con_fab_knit_fin_double=0;$con_fab_knit_fin_single=0;
				$process_part='';
				 foreach($conversion_cost_head_array as $process_id=>$val)
				 {
				 	if(in_array($process_id,$double_part_arr))
					{
						$con_fin_double=array_sum($conversions_qty_arr['knit']['finish'][$po_id][$item_id][$c_date][$body_id][$deterid][$dia_type_id][$dia_id][$gsm_id][$process_id]);						if($con_fin_double>0)
						{
						$con_fab_knit_fin_double+=$con_fin_double;	
						}
					//	$process_part="Double part";
				//	$process_partArr[$po_id][$item_id][$c_date][$body_id][$deterid][$dia_type_id][$dia_id][$gsm_id]=$process_id;
					}
					else if(in_array($process_id,$signle_part_arr))
					{
						$con_fin_single=array_sum($conversions_qty_arr['knit']['finish'][$po_id][$item_id][$c_date][$body_id][$deterid][$dia_type_id][$dia_id][$gsm_id][$process_id]);						if($con_fin_single>0)
						{
						$con_fab_knit_fin_single+=$con_fin_single;	
						}
					//	$process_part="Signle part";
				//	$process_partArr[$po_id][$item_id][$c_date][$body_id][$deterid][$dia_type_id][$dia_id][$gsm_id]=$process_id;
					}
					else //Other Process
					{
						if(!in_array($process_id,$without_process_arr))
						{
						 $con_in_others=array_sum($conversions_qty_arr['knit']['finish'][$po_id][$item_id][$c_date][$body_id][$deterid][$dia_type_id][$dia_id][$gsm_id][$process_id]);						  if($con_in_others>0)
						  {
							$con_fab_knit_fin_others+=$con_in_others;	
						  }
						//$process_part="Others part";
						//$process_partArr[$po_id][$item_id][$c_date][$body_id][$deterid][$dia_type_id][$dia_id][$gsm_id]=$process_id;
						}
					}
				 }
				
				//$con_fab_woven_fin_others=array_sum($conversions_qty_arr['woven']['finish'][$po_id][$item_id][$c_date][$body_id][$deterid][$dia_type_id][$dia_id][$gsm_id][158]);
				$con_fin_fab_req_qty_others=$con_fab_knit_fin_others;

				//$con_fab_knit_fin_single=array_sum($conversions_qty_arr['knit']['finish'][$po_id][$item_id][$c_date][$body_id][$deterid][$dia_type_id][$dia_id][$gsm_id][266]);
				//$con_fab_woven_fin_single=array_sum($conversions_qty_arr['woven']['finish'][$po_id][$item_id][$c_date][$body_id][$deterid][$dia_type_id][$dia_id][$gsm_id][266]);
				$con_fin_fab_req_qty_single=$con_fab_knit_fin_single;

			//	$con_fab_knit_fin_double=array_sum($conversions_qty_arr['knit']['finish'][$po_id][$item_id][$c_date][$body_id][$deterid][$dia_type_id][$dia_id][$gsm_id][267]);
				//$con_fab_woven_fin_double=array_sum($conversions_qty_arr['woven']['finish'][$po_id][$item_id][$c_date][$body_id][$deterid][$dia_type_id][$dia_id][$gsm_id][267]);
				$con_fin_fab_req_qty_double=$con_fab_knit_fin_double+$con_fab_woven_fin_double;
				
				
				if($con_fin_fab_req_qty_others>0)
				{
					$process_partArr[$po_id][$item_id][$c_date][$body_id][$deterid][$dia_type_id][$dia_id][$gsm_id]='Others Part';
				}
				
				if($con_fin_fab_req_qty_single>0)
				{
					$process_partArr[$po_id][$item_id][$c_date][$body_id][$deterid][$dia_type_id][$dia_id][$gsm_id]='Single Part';
				}
				
				if($con_fin_fab_req_qty_double>0)
				{
					$process_partArr[$po_id][$item_id][$c_date][$body_id][$deterid][$dia_type_id][$dia_id][$gsm_id]='Double Part';
				}

				

				//echo $fin_fab_req_qty.',DDDDDDDDD';
				$buyer_wise_req_arr[$row[('buyer_name')]][$row[('construction')]]['construction']=$row[('construction')];
				//$buyer_wise_req_arr[$row[('buyer_name')]][$row[('construction')]]['plan_cut']+=$plan_cut_aty;
				$buyer_wise_req_arr[$row[('buyer_name')]][$row[('construction')]]['grey_fab_req_qty']+=$grey_fab_req_qty;
				$buyer_wise_req_arr[$row[('buyer_name')]][$row[('construction')]]['fin_fab_req_qty']+=$fin_fab_req_qty;
				
				
				
				
				$construction_wise_req_arr[$row[('construction')]]=$row[('construction')];
				if($yarn_determin_library[$deterid]!="")
				{
				$genetic_wise_req_arr[$yarn_determin_library[$deterid]]=$yarn_determin_library[$deterid];
				$buyer_wise_genetic_req_arr[$row[('buyer_name')]][$yarn_determin_library[$deterid]]['grey_fab_req_qty']+=$grey_fab_req_qty;
				$buyer_wise_genetic_req_arr[$row[('buyer_name')]][$yarn_determin_library[$deterid]]['fin_fab_req_qty']+=$fin_fab_req_qty;
				}
				//$buyer_wise_arr[$row[('buyer_name')]]['plan_cut']+=$plan_cut_aty;
				$buyer_wise_arr[$row[('buyer_name')]]['grey_fab_req_qty']+=$grey_fab_req_qty;
				$buyer_wise_arr[$row[('buyer_name')]]['fin_fab_req_qty']+=$fin_fab_req_qty;

				$buyer_wise_arr[$row[('buyer_name')]]['single_part']+=$con_fin_fab_req_qty_single;
				$buyer_wise_arr[$row[('buyer_name')]]['double_part']+=$con_fin_fab_req_qty_double;
				$buyer_wise_arr[$row[('buyer_name')]]['others_finishing']+=$con_fin_fab_req_qty_others;
	
				$po_row_span++; $po_item_row_span++; $po_item_cdate_row_span++; 
			 }
			 $po_row_spanArr[$po_id]=$po_row_span;
			 $po_item_row_spanArr[$po_id][$item_id]=$po_item_row_span;
			 $po_item_cdate_row_spanArr[$po_id][$item_id][$c_date]=$po_item_cdate_row_span;
			 }
			}
			}
			}
		   }
		 }
	   }

//print_r($process_partArr);
	//$pub_shipment_dates=implode(",",array_unique(explode(",",$pub_shipment_date)));
	
	$tot_cons=count($construction_wise_req_arr);
	$tot_cons_gen=count($genetic_wise_req_arr);

		ob_start(); 
		$th_summ_width=530+160*$tot_cons;
		$th_width=2110;
		$th_gen_summ_width=530+160*$tot_cons_gen;
	 ?>
	 <div  class="scroll_div_inner">
		<div style="width:<? echo $th_width;?>+px">
			<fieldset style="width:100%;">	
				<table width="<? echo $th_width;?>">
					<tr class="form_caption">
						<td colspan="47" align="center" style="font-size: 18px;font-weight: bold;"><? echo str_replace("'","",$report_title);?></td>
					</tr>
					<tr class="form_caption">
						<td colspan="47" align="center" style="font-size: 18px;font-weight: bold;"><? echo $company_library[$company_name].'<br>' ;
						if($start_date!="") echo $start_date.' To '.$end_date; ?></td>
					</tr>
				</table>
              
                <table id="table_header_1" class="rpt_table" width="<? echo $th_gen_summ_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
                <caption style="justify-content: left;text-align: left;font-size: 16px;font-weight: bold;"><b> Group Wise Summary</b> </caption>
					<thead>
                    	<tr>
                         <th colspan="7">Total</th>
                         <th>Single Part</th>
                         <th>Double Part</th>
                         <th>Others</th>
                         <?
                        foreach($genetic_wise_req_arr as $construct=>$val)
						{
						?>
                        <th colspan="<? echo count($val)*2;?>"><? echo $construct;?></th>
                        <?
						}
						?>
                        </tr>
						<th width="20">SL</th>
						<th width="110">Buyer</th>
					
						<th width="80"> Plan Cut Qty</th>
                        
                        <th width="80">Avg Grey Cons/Pcs</th>
                        <th width="80">Grey Fabric Req.</th>
						<th width="80">Avg Finish Cons/Pcs</th>
						<th width="80">Finish Fabric Req.</th>
						<th width="80">Finish Fabric Req. Qty</th>
						<th width="80">Finish Fabric Req. Qty</th>
						<th width="80">Finish Fabric Req. Qty</th>
                        
						<?
						
                        foreach($genetic_wise_req_arr as $construct=>$val)
						{
						?>
						
						<th width="80">Grey Fabric Req.</th>
						<th width="">Finish Fabric Req.</th>
                        <?
						} //buyer_wise_arr
						?>
					</thead>
                    <tbody>
                    	<?
						$b=1;$tot_const_plan_cut=$total_grey_fab_req_qty=$total_fin_fab_req_qty=0;
                        foreach($buyer_wise_arr as $buyer_id=>$bval)
						{
							if($b%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$plan_cut=$buyer_po_qty_arr[$buyer_id];
						?>
                       <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trbuyer_<? echo $b; ?>','<? echo $bgcolor;?>')" id="trbuyer_<? echo $b; ?>">
                        
                        <td width="20"><? echo $b; ?></td>
                        <td width="110" align="left"><div style="word-break:break-all"><? echo $buyer_library[$buyer_id];?> </div></td>
                        <td width="80" align="right"><div style="word-break:break-all"><? $plan_cut=$plan_cut;echo $plan_cut;?> </div></td>
                        <td width="80" align="right" title="Grey Req/PlanCut"><div style="word-break:break-all"><? echo number_format($bval['grey_fab_req_qty']/$plan_cut,2);?> </div></td>
                        <td width="80" align="right"><div style="word-break:break-all"><? $grey_fab_req_qty=$bval['grey_fab_req_qty'];echo number_format($grey_fab_req_qty,0);?> </div></td>
                        <td width="80" align="right" title="Fin Req/PlanCut"><div style="word-break:break-all"><? $fin_fab_req_qty=$bval['fin_fab_req_qty'];echo number_format($fin_fab_req_qty/$plan_cut,2);;?> </div></td>
                        
                        <td width="80" align="right"><div style="word-break:break-all"><? echo number_format($fin_fab_req_qty,0);?> </div></td>
                        <td width="80" align="right"><div style="word-break:break-all"><? $single_part=$bval['single_part'];echo number_format($single_part,2);?> </div></td>
                        <td width="80" align="right"><div style="word-break:break-all"><? $double_part=$bval['double_part'];echo number_format($double_part,2);?> </div></td>
                        <td width="80" align="right"><div style="word-break:break-all"><? $others_finishing=$bval['others_finishing']; echo number_format($others_finishing,2);?> </div></td>
                        <?
                        foreach($genetic_wise_req_arr as $construct=>$val)
						{
							$const_grey_fab_req_qty=$buyer_wise_genetic_req_arr[$buyer_id][$construct]['grey_fab_req_qty'];
							$const_fin_fab_req_qty=$buyer_wise_genetic_req_arr[$buyer_id][$construct]['fin_fab_req_qty'];
						$tot_const_grey_fab_req_qty_arr[$construct]+=$const_grey_fab_req_qty;
						$tot_const_fin_fab_req_qty_arr[$construct]+=$const_fin_fab_req_qty;
						?>
                         <td width="80" align="right"><div style="word-break:break-all"><? echo number_format($const_grey_fab_req_qty,2);?> </div></td>
                         <td width="" align="right"><div style="word-break:break-all"><? echo number_format($const_fin_fab_req_qty,2);?> </div></td>
                       <?
						}?>
                        
                        </tr>
                        <?
						
						$tot_const_plan_cut+=$plan_cut;
						$total_grey_fab_req_qty+=$grey_fab_req_qty;
						$total_fin_fab_req_qty+=$fin_fab_req_qty;
						$total_single_part_req_qty+=$single_part;
						$total_double_part_req_qty+=$double_part;
						$total_others_finishing_req_qty+=$others_finishing;
						//$tot_const_plan_cut+=$plan_cut;
						$b++;
						}
						?>
                        
                    </tbody>
                    <tfoot>
                    <tr>
                    <th colspan="2"> Total </th>
                    <th width="80" align="right"><? echo number_format($tot_const_plan_cut,0); ?></th>
                    <th width="80"><? echo number_format($total_grey_fab_req_qty/$tot_const_plan_cut,2); ?> </th>
                   <th width="80" align="right"><? echo number_format($total_grey_fab_req_qty,2); ?></th>
                    <th width="80"><? echo number_format($total_fin_fab_req_qty/$tot_const_plan_cut,2); ?></th>
                    <th width="80" align="right"><? echo number_format($total_fin_fab_req_qty,2); ?></th>
                    <th width="80" align="right"><? echo number_format($total_single_part_req_qty,2); ?></th>
                    <th width="80" align="right"><? echo number_format($total_double_part_req_qty,2); ?></th>
                    <th width="80" align="right"><? echo number_format($total_others_finishing_req_qty,2); ?></th>
                      <?
                        foreach($genetic_wise_req_arr as $construct=>$val)
						{
						?>
                          <th width="80" align="right"><? echo number_format($tot_const_grey_fab_req_qty_arr[$construct],2); ?></th>
                          <th width="" align="right"><? echo number_format($tot_const_fin_fab_req_qty_arr[$construct],2); ?></th>
                        <?
						}
						?>
                    </tr>
                    </tfoot>
				</table>
                <br>
                
                <table id="table_header_1" class="rpt_table" width="<? echo $th_summ_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
                	<caption style="justify-content: left;text-align: left;font-size: 16px;font-weight: bold;"><b>Construction Wise Summary</b> </caption>
					<thead>

                    	<tr>
                         <th colspan="7">Total</th>
                         <?
                        foreach($construction_wise_req_arr as $construct=>$val)
						{
						?>
                        <th colspan="<? echo count($val)*2;?>"><? echo $construct;?></th>
                        <?
						}
						?>
                        </tr>
						<th width="20">SL</th>
						<th width="110">Buyer</th>
					
						<th width="80"> Plan Cut Qty</th>
                        
                        <th width="80">Avg Grey Cons/Pcs</th>
                        <th width="80">Grey Fabric Req.</th>
						<th width="80">Avg Finish Cons/Pcs</th>
						<th width="80">Finish Fabric Req.</th>
                        
						<?
						
                        foreach($construction_wise_req_arr as $construct=>$val)
						{
						?>
						
						<th width="80">Grey Fabric Req.</th>
						<th width="">Finish Fabric Req.</th>
                        <?
						} //buyer_wise_arr
						?>
					</thead>
                    <tbody>
                    	<?
						$b=1;$tot_const_plan_cut=$total_grey_fab_req_qty=$total_fin_fab_req_qty=0;
                        foreach($buyer_wise_arr as $buyer_id=>$bval)
						{
							if($b%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$plan_cut=$buyer_po_qty_arr[$buyer_id];
						?>
                       <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trbuyer_<? echo $b; ?>','<? echo $bgcolor;?>')" id="trbuyer_<? echo $b; ?>">
                        
                        <td width="20"><? echo $b; ?></td>
                        <td width="110" align="left"><div style="word-break:break-all"><? echo $buyer_library[$buyer_id];?> </div></td>
                        <td width="80" align="right"><div style="word-break:break-all"><? $plan_cut=$plan_cut;echo $plan_cut;?> </div></td>
                        <td width="80" align="right" title="Grey Req/PlanCut"><div style="word-break:break-all"><? echo number_format($bval['grey_fab_req_qty']/$plan_cut,2);?> </div></td>
                        <td width="80" align="right"><div style="word-break:break-all"><? $grey_fab_req_qty=$bval['grey_fab_req_qty'];echo number_format($grey_fab_req_qty,0);?> </div></td>
                        <td width="80" align="right" title="Fin Req/PlanCut"><div style="word-break:break-all"><? $fin_fab_req_qty=$bval['fin_fab_req_qty'];echo number_format($fin_fab_req_qty/$plan_cut,2);;?> </div></td>
                        
                        <td width="80" align="right"><div style="word-break:break-all"><? echo number_format($fin_fab_req_qty,0);?> </div></td>
                        <?
                        foreach($construction_wise_req_arr as $construct=>$val)
						{
							$const_grey_fab_req_qty=$buyer_wise_req_arr[$buyer_id][$construct]['grey_fab_req_qty'];
							$const_fin_fab_req_qty=$buyer_wise_req_arr[$buyer_id][$construct]['fin_fab_req_qty'];
						$tot_const_grey_fab_req_qty_arr[$construct]+=$const_grey_fab_req_qty;
						$tot_const_fin_fab_req_qty_arr[$construct]+=$const_fin_fab_req_qty;
						?>
                         <td width="80" align="right"><div style="word-break:break-all"><? echo number_format($const_grey_fab_req_qty,2);?> </div></td>
                         <td width="" align="right"><div style="word-break:break-all"><? echo number_format($const_fin_fab_req_qty,2);?> </div></td>
                       <?
						}?>
                        
                        </tr>
                        <?
						
						$tot_const_plan_cut+=$plan_cut;
						$total_grey_fab_req_qty+=$grey_fab_req_qty;
						$total_fin_fab_req_qty+=$fin_fab_req_qty;
						//$tot_const_plan_cut+=$plan_cut;
						$b++;
						}
						?>
                        
                    </tbody>
                    <tfoot>
                    <tr>
                    <th colspan="2"> Total </th>
                    <th width="80" align="right"><? echo number_format($tot_const_plan_cut,0); ?></th>
                    <th width="80"> <? echo number_format($total_grey_fab_req_qty/$tot_const_plan_cut,2); ?> </th>
                   <th width="80" align="right"><? echo number_format($total_grey_fab_req_qty,2); ?></th>
                    <th width="80"><? echo number_format($total_fin_fab_req_qty/$tot_const_plan_cut,2); ?></th>
                    <th width="80" align="right"><? echo number_format($total_fin_fab_req_qty,2); ?></th>
                      <?
                        foreach($construction_wise_req_arr as $construct=>$val)
						{
						?>
                          <th width="80" align="right"><? echo number_format($tot_const_grey_fab_req_qty_arr[$construct],2); ?></th>
                          <th width="" align="right"><? echo number_format($tot_const_fin_fab_req_qty_arr[$construct],2); ?></th>
                        <?
						}
						?>
                    </tr>
                    </tfoot>
				</table>
                <br>
                <? //die;?>
				<table id="table_header_1" class="rpt_table" width="<? echo $th_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
					
					<caption style="justify-content: left;text-align: left;font-size: 16px;font-weight: bold;"><b>Details Report</b> </caption>
					<thead>
						<th width="20">SL</th>
						<th width="110">Buyer</th>
						<th width="100">Job No</th>
						<th width="100"><div style="word-break:break-all">Order No</div></th>
						<th width="100">Style</th>
                        <th width="100"><div style="word-wrap:break-word:100px;">Order Status</div></th>
						<th width="80">Total Order Qty</th>
                        <th width="50">UOM</th>
						<th width="80"><div style="word-break:break-all">Total Order Qty(pcs)</div></th>
						<th width="110"><div style="word-break:break-all">Item</div></th>
						<th width="80">Country Ship Date</th>
						<th width="80">Country Ship Qty (pcs)</th>
						<th width="80">Plan Cut Qty</th>
                        
						<th width="100">Body Part</th>
						<th width="100">Dyeing Finishing Process Status</th>
						<th width="100">Fabric Description</th>
						<th width="100">Construction</th>
						<th width="100">Composition</th>
						<!-- <th width="100">Dyeing Type</th> -->
						<th width="100">Dia Type</th>
						<th width="50">Dia</th>
						<th width="50">GSM</th>
						<th width="80">Grey Cons</th>
						<th width="80">Finish Cons</th>
						<th width="80">Grey Fabric Req.</th>
						<th width="">Finish Fabric Req.</th>
					</thead>
				</table>
			<div style="width:<? echo $th_width+20;?>px; max-height:400px; overflow-y:scroll" id="scroll_body">
			<table class="rpt_table" width="<? echo $th_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
			<?
			$i=1;$j=1; $total_po_qty=0; $total_ex_fact_qty=0;$total_tot_fob_price=0;$total_tot_ex_fact_val=0;		
			$total_cm_margin_val=0;	$total_qlty_qty=0;	$total_up_charge=0;	 $total_fab_finish_req=0; 
				  //  $tot_rows=count($nameArray);
			
			//print_r($fabric_qty_arr);
					$total_grey_fab_req_qty=$total_fab_finish_req=$total_grey_fab_cons_qty=$total_fin_fab_cons_qty=0;
					foreach($fab_wise_req_arr as $po_id=>$po_data)
					{
					  $p=1;
					  foreach($po_data as $item_id=>$item_data)
					   {
						   $item=1;
						   foreach($item_data as $c_date=>$cdate_data)
						   { $c=1;
						    foreach($cdate_data as $body_id=>$body_data)
						    {
							foreach($body_data as $deterid=>$deter_data)
						   	{
							foreach($deter_data as $dia_type_id=>$dtype_data)
						   	{
							foreach($dtype_data as $dia_id=>$dia_data)
						     {
							 foreach($dia_data as $gsm_id=>$row)
						     {
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						//$poids=array_unique(explode(",",$row[csf('po_id')]));
						 $short_excess_qty=$ex_fact_qty-$row[('po_quantity')];
						 $cm_margin_dzn=$pre_cost_cm_margin_arr[$row[('job_no')]]['cm'];
						 $cm_margin_val=($cm_margin_dzn/12)*$row[('po_quantity')];
						
						 $fab_finish_req=$fab_production_knit+$fab_production_woven;
						 $po_received_date= array_unique(explode(",",$row[('po_received_date')])); 			
						 $shipment_date= array_unique(explode(",",$row[('shipment_date')])); 
						 						
						 $fab_qty_knit=array_sum($fabric_qty_arr['knit']['grey'][$po_id][$item_id][$c_date][$body_id][$deterid][$dia_type_id][$dia_id][$gsm_id]);
						//  $fab_qty_woven=array_sum($fabric_qty_arr['woven']['grey'][$po_id][$item_id][$c_date][$body_id][$deterid][$dia_type_id][$dia_id][$gsm_id]);
						  $grey_fab_req_qty=$fab_qty_knit+$fab_qty_woven;
						  
						   $fab_qty_knit_fin=array_sum($fabric_qty_arr['knit']['finish'][$po_id][$item_id][$c_date][$body_id][$deterid][$dia_type_id][$dia_id][$gsm_id]);
						//  $fab_qty_woven_fin=array_sum($fabric_qty_arr['woven']['finish'][$po_id][$item_id][$c_date][$body_id][$deterid][$dia_type_id][$dia_id][$gsm_id]);
						  $fin_fab_req_qty=$fab_qty_knit_fin+$fab_qty_woven_fin;
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
							
                           <?
                           if($p==1)
						   {
						   ?>
                            <td width="20" rowspan="<? echo  $po_row_spanArr[$po_id];?>"><? echo $j; ?></td>
                            <td width="110" rowspan="<? echo  $po_row_spanArr[$po_id];?>" align="left"><div style="word-break:break-all"><? echo $buyer_library[$row[('buyer_name')]];?> </div></td>
							<td width="100" rowspan="<? echo  $po_row_spanArr[$po_id];?>"><div style="word-break:break-all"><? echo $row[('job_no')]; ?></div></td>
							<td width="100" rowspan="<? echo  $po_row_spanArr[$po_id];?>" align="center"><div style="word-break:break-all"><? echo $row[('po_number')]; ?></div></td>
							<td width="100" rowspan="<? echo  $po_row_spanArr[$po_id];?>"><div style="word-break:break-all"><? echo $row[('style_ref')]; ?></div></td>
							<td width="100" rowspan="<? echo  $po_row_spanArr[$po_id];?>"><div style="word-break:break-all"><? echo $order_status[$row[('is_confirmed')]]; ?></div></td>
                            <td width="80" rowspan="<? echo  $po_row_spanArr[$po_id];?>" align="right"><div style="word-wrap:break-word:120px;"><? echo $row[('po_quantity')]; ?></div></td>
							<td width="50" rowspan="<? echo  $po_row_spanArr[$po_id];?>"><div style="word-break:break-all"><? echo $unit_of_measurement[$row[('order_uom')]]; ?></div></td>
							<td width="80" rowspan="<? echo  $po_row_spanArr[$po_id];?>" align="right"><div style="word-break:break-all"><? echo $row[('po_quantity_pcs')]; ?></div></td>
                            <?
						   }
							
                           if($item==1)
						   {
						   ?>
                            <td width="110"  rowspan="<? echo  $po_item_row_spanArr[$po_id][$item_id];?>"><div style="word-break:break-all"><? echo $garments_item[$item_id]; ?></div></td>
                            <?
						   }
						   //po_item_cdate_row_spanArr[$po_id][$item_id][$c_date]
							if($c==1)
						   {
							   $po_qty_pcs=$po_wise_req_arr[$po_id][$item_id][$c_date]['po_qty_pcs'];
						   ?>
							<td width="80"  rowspan="<? echo  $po_item_cdate_row_spanArr[$po_id][$item_id][$c_date];?>" align="center" title="<? echo $row[('job_no')];?>"> <p> <? echo change_date_format($c_date); ?>   </p>
							<td width="80"  rowspan="<? echo  $po_item_cdate_row_spanArr[$po_id][$item_id][$c_date];?>" align="right" ><div style="word-break:break-all"> <? echo $po_qty_pcs;// echo number_format($job_qty,2); ?>
                            </div>
							</td>
							<td width="80" rowspan="<? echo  $po_item_cdate_row_spanArr[$po_id][$item_id][$c_date];?>" align="right" ><div style="word-break:break-all"><? 
							$plan_cut=$po_wise_req_arr[$po_id][$item_id][$c_date]['plan_cut'];
							echo $plan_cut; ?></div></td>
                            <?
						   }
							?>
                            
							
							<td width="100"   align="center" ><div style="word-break:break-all">
							<? echo $body_part[$body_id];?>
                            </div>
							</td>
							<td width="100"   align="center"><div style="word-break:break-all">
							<? echo $process_partArr[$po_id][$item_id][$c_date][$body_id][$deterid][$dia_type_id][$dia_id][$gsm_id];
							//$dyeing_fin_process_status[$conversions_process[$row['fabric_id']]]; ?>
                            </div>
							</td>
							<td width="100" align="center"><div style="word-break:break-all">
							 <? echo $row[('desc')];?>
                             </div>
							</td>
							<td width="100" align="left">
                            <div style="word-break:break-all">
							<?
							 
									echo $row[('construction')]; 
									//$order_qty_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
									?>
                                    </div>
							</td>
							<td width="100" align="center"><div style="word-break:break-all"><? echo  $row['composition']; ?></div></td>
							<!-- <td width="100"  align="center"><div style="word-break:break-all"><? //echo  $fabric_typee[$dia_type_id];; ?></div></td> -->
							<td width="100" ><div style="word-break:break-all"><? echo   $fabric_typee[$dia_type_id]; ?></div></td>
							
                            <td width="50" align="center"><div style="word-break:break-all"><? echo  $dia_id;//implode(",",array_unique(explode(",",$fab_gsm))); ?></div></td>
							<td width="50" align="center"><div style="word-break:break-all"><? echo $gsm_id; ?></div></td>
							<td width="80" align="right" title="Grey Req Qty/Plan Cut"><div style="word-break:break-all">
							<? $grey_fab_cons_qty=$grey_fab_req_qty/$plan_cut;echo number_format($grey_fab_cons_qty,2); ?></div></td>
							<td width="80" align="right" title="Fin Req Qty/Plan Cut"> <p> <? $fin_fab_cons_qty=$fin_fab_req_qty/$plan_cut; echo number_format($fin_fab_cons_qty,2);; ?>   </p>
							<td width="80" align="right" > <? echo number_format($grey_fab_req_qty,2); ?>  </td>
						
							<td width=""  align="right"><? echo number_format($fin_fab_req_qty,2); ?></td>
						   
							
						</tr>
					<?
					$total_po_qty+=$row[('po_quantity')];
					//$total_ex_fact_qty+=$ex_fact_qty;
					//$total_tot_fob_price+=$tot_fob_price;
					////$total_tot_ex_fact_val+=$tot_ex_fact_val;
				//	//$total_cm_margin_val+=$cm_margin_val;
					
					//$total_up_charge+=$row[csf('up_charge')];
					$total_plun_cut_qty+=$plan_cut;
					$total_grey_fab_req_qty+=$grey_fab_req_qty;
					$total_fab_finish_req+=$fin_fab_req_qty;
					$i++;$p++;$item++;$c++;
					   			}
							   }
							  }
							 }
							}
						   }
					   }
					   $j++;
					}
					?>
					</table>
					<table class="rpt_table" width="<? echo $th_width;?>" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
						<tfoot>
							<th width="20"></th>
							<th width="110"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
                            <th width="100"></th>
                            
                            <th width="80"></th>
							<th width="50"></th>
							<th width="80"></th>
                            
							<th width="110"></th>
							<th width="80" id=""> </th>
							<th width="80"></th>
							<th width="80" id=""></th>
                            
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"> </th>
							<th width="100"> </th>
							<th width="100"></th>
							<th width="100"></th>
                            
							<th width="50"></th>
							<th width="50" >Total</th>
							<th width="80"><? echo number_format($total_grey_fab_req_qty/$total_plun_cut_qty,2); ?></th>
							<th width="80"><? echo number_format($total_fab_finish_req/$total_plun_cut_qty,2); ?></th>
							<th width="80" id="grey_total_order_qty"><? echo number_format($total_grey_fab_req_qty,2); ?></th>
							
							<th width="" id="fin_total_order_qty"><? echo number_format($total_fab_finish_req,2); ?></th>
						
						</tfoot>
					</table>
				</div>
				</fieldset>
			</div>		 
	 
	
	</div>
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
if( $action=="report_generate_color_range" )
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	//echo $txt_style;die;
	$company_name_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$color_arr=return_library_array( "select id, color_name from lib_color  where status_active=1",'id','color_name');
	//print_r($color_arr);

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
	
	

	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
//	echo $date_from;die;
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
			//$date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
			$c_date_cond=" and c.country_ship_date between '$start_date' and '$end_date'";
			//$date_max_profit=" and applying_period_to_date between '$start_date' and '$end_date'";
		}
	}

//echo $c_date_cond;die;
	
		
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
	//echo "AAAAAAA";
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
	
	
	
  $sql_po="SELECT a.id,a.job_no, a.buyer_name,a.total_set_qnty as ratio,a.avg_unit_price,a.style_ref_no,b.id as po_id,b.pub_shipment_date,a.order_uom,b.po_number,b.po_quantity,b.is_confirmed,c.item_number_id,c.country_id,c.item_number_id as item_id,c.plan_cut_qnty,c.country_ship_date,c.color_number_id,c.size_number_id,c.order_quantity,c.plan_cut_qnty, d.convchargelibraryid as convchargelibraryid,e.color_id,e.color_range_id,d.gmts_color_id,d.conv_cost_dtls_id  from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,wo_pre_cos_conv_color_dtls d,lib_subcon_charge e where  1=1 and a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and d.convchargelibraryid=e.id  and b.id=c.po_break_down_id  and d.gmts_color_id=c.color_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1  and e.is_deleted=0 and e.status_active=1 and d.convchargelibraryid>0 $company_name_cond $job_no_cond $year_cond  $c_date_cond $order_id_cond_trans  $order_no_cond $internal_ref_cond $file_no_cond $buyer_id_cond $order_status_cond order by  b.id,c.country_ship_date"; 
	// echo $sql_po; die;
	$sql_po_data=sql_select($sql_po);
	
	foreach($sql_po_data as $row)
	{
	$color_range_id=$row[csf('color_range_id')];
	$color_id=$row[csf('color_id')];
	$convchargelibraryid=$row[csf('convchargelibraryid')];
	$color_str=$color_id.'_'.$color_range_id;
	$fab_color_range_arr[$convchargelibraryid]['lib_color_str']=$color_str;
	$fabric_color_range_arr[$color_id]['lib_color_str']=$color_str;
	$fab_color_range_arr[$convchargelibraryid]['po_id'].=$row[csf('po_id')].',';
	$fab_color_range_arr[$convchargelibraryid]['conv_cost_dtls_id'].=$row[csf('conv_cost_dtls_id')].',';
	$fab_color_range_arr[$convchargelibraryid]['gmts_color_id']=$row[csf('gmts_color_id')];
	//$plan_cut_qty_arr[$row[csf('po_id')]][$row[csf('item_id')]][$c_ship_date]+=$row[csf('plan_cut_qnty')];
	$buyer_po_arr[$row[csf('po_id')]][$row[csf('conv_cost_dtls_id')]][$row[csf('gmts_color_id')]].=$row[csf('convchargelibraryid')].',';
	$po_idarr[$row[csf('po_id')]] = $row[csf('po_id')];
	}
	//echo '<pre>';print_r($fabric_color_range_arr);die;
	unset($sql_po_data);
	$poIdCond=where_con_using_array($po_idarr,0,'b.id');
	 $condition= new condition();
	 $condition->company_name("=$cbo_company_name");
	 if(str_replace("'","",$cbo_buyer_name)>0){
		  $condition->buyer_name("=$cbo_buyer_name");
	 }
	 if(str_replace("'","",$txt_job_no) !=''){
		  $condition->job_no_prefix_num("=$txt_job_no");
	 }
	 if(str_replace("'","",$cbo_order_status) >0){
		 // $condition->is_confirmed("=$cbo_order_status");
	 }
	 if(str_replace("'","",$cbo_order_status)==0){
		//  $condition->is_confirmed("in(1,2)");
	 }
	 if(str_replace("'","",$cbo_search_date) ==1 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!=''){
		  $condition->country_ship_date(" between '$start_date' and '$end_date'");
	 }
	
	 if(str_replace("'","",$txt_order_no)!='')
	 {
		$condition->po_number("=$txt_order_no"); 
	 }
	
	 $condition->init();
	
	$conversions = new conversion($condition);
	//echo $conversions->getQuery();die;
	$conversions_qty_arr = $conversions->getQtyArray_by_ConversionidOrderColorAndUom();
	/*echo '<pre>';
	print_r($conversions_qty_arr); die;*/
	 $sql_po_conv="SELECT  b.id as poid,d.conv_cost_dtls_id,d.convchargelibraryid as convchargelibraryid,e.color_id,e.color_range_id,d.gmts_color_id  from  wo_po_break_down b,wo_pre_cos_conv_color_dtls d,lib_subcon_charge e where  1=1 and  b.job_id=d.job_id and d.convchargelibraryid=e.id and b.is_deleted=0 and b.status_active=1  and d.is_deleted=0 and d.status_active=1  and e.is_deleted=0 and e.status_active=1 and d.convchargelibraryid>0 $order_id_cond_trans  $order_no_cond $internal_ref_cond $file_no_cond $poIdCond $order_status_cond "; 
	  //echo $sql_po_conv;  die;
	$sql_po_data_conv=sql_select($sql_po_conv);
	$fab_color_rangeQty_arr=array();
	foreach($sql_po_data_conv as $row)
	{
		$conversions_qty=array_sum($conversions_qty_arr[$row[csf('conv_cost_dtls_id')]][$row[csf('poid')]][$row[csf('gmts_color_id')]]);
		//echo $conversions_qty.'<br>';
		$fab_color_rangeQty_arr[$row[csf('color_id')]]+= $conversions_qty;
	}
	unset($sql_po_data_conv);
	

		ob_start(); 
		$th_color_summ_width=350;
	 ?>
	 <div  class="scroll_div_inner">
		<div style="width:<? echo $th_color_summ_width+50;?>+px">
			<fieldset style="width:<? echo $th_color_summ_width+50;?>px;">	
				<table width="<? echo $th_color_summ_width+50;?>">
					<tr class="form_caption">
						<td colspan="3" align="center" style="font-size: 18px;font-weight: bold;"><? echo str_replace("'","",$report_title);?></td>
					</tr>
					<tr class="form_caption">
						<td colspan="3" align="center" style="font-size: 18px;font-weight: bold;"><? echo $company_library[$company_name].'<br>' ;
						if($start_date!="") echo $start_date.' To '.$end_date; ?></td>
					</tr>
				</table>
                <?
                if($report_type==3) //Color Range button
				{
					
				?>
                <table id="table_header_1" class="rpt_table" width="<? echo $th_color_summ_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
                <caption style="justify-content: left;text-align: left;font-size: 16px;font-weight: bold;"><b> Color Range Wise Summary</b> </caption>
					<thead>
						<th width="50">SL</th>
						<th width="200">Color Range</th>
						<th width=""> Qty</th>
					</thead>
                    <tbody>
                    	<?
						$b=1;$tot_const_plan_cut=$total_grey_fab_req_qty=$tot_fab_qty=0;
                        foreach($fabric_color_range_arr as $color_key=>$row)
						{
							if($b%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							//$row['lib_color_str'];
							$colorId_arr=explode("_",$row['lib_color_str']);//$buyer_po_qty_arr[$buyer_id];
							$color_id=$colorId_arr[0];
							$color_range_id=$colorId_arr[1];
						//echo $color_id.'d';
						?>
                       <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trbuyer_<? echo $b; ?>','<? echo $bgcolor;?>')" id="trbuyer_<? echo $b; ?>">
                        
                        <td width="50"><? echo $b; ?></td>
                        <td width="200" align="left" title="<?=$color_key;?>"><div style="word-break:break-all"><? echo $color_arr[$color_id];?> </div></td>
                        <td width="" align="right"><div style="word-break:break-all"><? $fab_qty=$fab_color_rangeQty_arr[$color_key];echo number_format($fab_qty,2);?> </div></td>
                        </tr>
                        <?
						
						$tot_fab_qty+=$fab_qty;
						 
						//$tot_const_plan_cut+=$plan_cut;
						$b++;
						}
						?>
                        
                    </tbody>
                    <tfoot>
                    <tr>
                    <th colspan="2" > Total </th>
                     <th width="80" align="right"><? echo number_format($tot_fab_qty,2); ?></th>
                    </tr>
                    </tfoot>
				</table>
                
				<?	
				}
				//if($report_type==3) die;
				?>
                
              
                
                <br>
                <? //die;?>
				
			
				</fieldset>
			</div>		 
	 
	
	</div>
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