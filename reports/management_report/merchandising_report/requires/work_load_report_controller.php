<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.fabrics.php');
require_once('../../../../includes/class4/class.conversions.php');
require_once('../../../../includes/class4/class.emblishments.php');
require_once('../../../../includes/class4/class.washes.php');
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

$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
$dealing_merchant_array = return_library_array("select id, team_member_name from lib_mkt_team_member_info","id","team_member_name");
$team_leader_library=return_library_array( "select id, team_leader_name from lib_marketing_team", "id", "team_leader_name");
$costing_library=return_library_array( "select job_no, costing_date from wo_pre_cost_mst", "job_no", "costing_date"  );
$supplier_library=return_library_array( "select id,short_name from lib_supplier", "id","short_name");
$country_name_library=return_library_array( "select id,country_name from lib_country", "id","country_name");

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  group by buy.id, buy.buyer_name  order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "load_drop_down( 'requires/oms_report_controller', this.value, 'load_drop_down_season', 'season_td'); " );
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
	$company_name_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$yarn_count_library=return_library_array( "select id,yarn_count from lib_yarn_count", "id","yarn_count");
	$report_type=str_replace("'","",$reporttype);
	
	$company_name=str_replace("'","",$cbo_company_name);
	$file_no=trim(str_replace("'","",$txt_file_no));
	$internal_ref=trim(str_replace("'","",$txt_internal_ref));
	$season=str_replace("'","",$cbo_season_id);

	$cbo_style_owner=str_replace("'","",$cbo_style_owner);
	$cbo_team_name=str_replace("'","",$cbo_team_name);
	$cbo_deal_marchant=str_replace("'","",$cbo_deal_marchant);
	$cbo_search_date=str_replace("'","",$cbo_search_date);


	if($company_name==0) $comp_cond=""; else $comp_cond=" and a.company_name='$company_name' ";
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

	$job_no=str_replace("'","",$txt_job_no);
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
	if($season==0) $season_cond=""; else $season_cond=" and a.season_matrix in($season)";

	$order_no=str_replace("'","",$txt_order_id);
	$order_num=str_replace("'","",$txt_order_no);
	if(str_replace("'","",$txt_order_id)!="" && str_replace("'","",$txt_order_id)!=0){
		$order_id_cond_trans=" and b.id in ($order_no)";
	}
	else if ($order_num==""){$order_no_cond="";}
	else{
		$order_no_cond=" and  b.po_number in ('$order_num') ";
	}

	if($report_type==1)
	{
		if($template==1)
		{
			$sql=sql_select('select a.job_no AS "job_no",a.company_name AS "company_name",a.buyer_name AS "buyer_name",a.style_ref_no as "style_ref_no",a.total_set_qnty AS "total_set_qnty",b.id AS "id",b.po_number AS "po_number",b.pub_shipment_date AS "pub_shipment_date",b.po_received_date AS "po_received_date",b.grouping AS "grouping" , b.file_no AS "file_no",c.item_number_id AS "item_number_id",c.country_id AS "country_id",c.color_number_id AS "color_number_id",c.size_number_id AS "size_number_id",c.order_quantity AS "order_quantity",c.plan_cut_qnty AS "plan_cut_qnty",d.id AS "emblishment_id",d.emb_name AS "emb_name",d.emb_type AS "emb_type",d.cons_dzn_gmts AS "cons_dzn_gmts",d.rate AS "rate",d.amount AS "amount"   from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_embe_cost_dtls d  where 1=1 '. $comp_cond . $date_cond . $buyer_id_cond . $year_cond . $job_no_cond . $order_id_cond_trans . $order_no_cond . $order_status_cond . $internal_ref_cond . $file_no_cond . $season_cond . $style_owner_cond . $team_leader_cond . $dealing_marchant_cond .' and d.emb_name in(1,2,3,4,5) and cons_dzn_gmts>0 and  a.id=b.job_id and a.id=c.job_id and a.id=d.job_id  and b.id=c.po_break_down_id   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1');
			$data_arr=array();
			foreach($sql as $row){
			  $data_arr[$row['emb_name']][$row['emb_type']][$row['id']]['company_name']=$row['company_name'];
			  $data_arr[$row['emb_name']][$row['emb_type']][$row['id']]['buyer_name']=$row['buyer_name'];
			  $data_arr[$row['emb_name']][$row['emb_type']][$row['id']]['job_no']=$row['job_no'];
			  $data_arr[$row['emb_name']][$row['emb_type']][$row['id']]['style_ref_no']=$row['style_ref_no'];
			  $data_arr[$row['emb_name']][$row['emb_type']][$row['id']]['po_number']=$row['po_number'];
			  $data_arr[$row['emb_name']][$row['emb_type']][$row['id']]['pub_shipment_date']=$row['pub_shipment_date'];
			  $data_arr[$row['emb_name']][$row['emb_type']][$row['id']]['po_received_date']=$row['po_received_date'];
			  $data_arr[$row['emb_name']][$row['emb_type']][$row['id']]['grouping']=$row['grouping'];
			  $data_arr[$row['emb_name']][$row['emb_type']][$row['id']]['file_no']=$row['file_no'];
			  $data_arr[$row['emb_name']][$row['emb_type']][$row['id']]['plan_cut_qnty']+=$row['plan_cut_qnty'];

			  $data_arr[$row['emb_name']][$row['emb_type']][$row['id']]['gmts_item_id'][$row['item_number_id']]=$garments_item[$row['item_number_id']];
			  $poArrs[$row['id']]=$row['id'];
		 	}

				$i=1; $total_po_qty=0; $total_ex_fact_qty=0;$total_tot_fob_price=0;$total_tot_ex_fact_val=0;
					$total_cm_margin_val=0;	$total_qlty_qty=0;	$total_up_charge=0;	 $total_fab_finish_req=0;
					 $condition= new condition();
					 if(str_replace("'","",$cbo_company_name)>0){
					 $condition->company_name("=$cbo_company_name");
					 }
					 if(str_replace("'","",$cbo_buyer_name)>0){
						  $condition->buyer_name("=$cbo_buyer_name");
					 }
					//$txt_job_no= 'D n C-17-00143';
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

					$condition->init();
					$emblishment= new emblishment($condition);
					//echo $emblishment->getQuery();
					$emblishment_qty_arr=$emblishment->getQtyArray_by_orderEmbnameAndEmbtype();
					$wash= new wash($condition);
					$wash_qty_arr=$wash->getQtyArray_by_orderEmbnameAndEmbtype();




			$prod_sql= "SELECT c.po_break_down_id,c.embel_name,d.production_type,d.production_qnty,min(production_date) as production_date
			from
			pro_garments_production_mst c,pro_garments_production_dtls d
			where
			c.id=d.mst_id and c.production_type in(2,3) and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.company_id=$cbo_company_name and c.po_break_down_id in(".implode(',',$poArrs).")  group by c.po_break_down_id,c.embel_name,d.production_type,d.production_qnty";
			 //echo $prod_sql;
			$gmts_prod_arr=array();
			$res_gmtsData=sql_select($prod_sql);
			foreach($res_gmtsData as $Row)
			{
				$gmts_prod_arr[$Row[csf('po_break_down_id')]][$Row[csf('production_type')]][$Row[csf('embel_name')]]+=$Row[csf('production_qnty')];
				$gmts_prod_date_arr[$Row[csf('po_break_down_id')]][$Row[csf('production_type')]][$Row[csf('embel_name')]]=$Row[csf('production_date')];

			}
			unset($res_gmtsData);

			$emb_booking_info=sql_select("SELECT a.booking_no, a.booking_date, a.delivery_date, b.po_break_down_id, b.pre_cost_fabric_cost_dtls_id, b.wo_qnty, c.emb_name as emblishment_name from wo_booking_mst a join wo_booking_dtls b on a.id=b.booking_mst_id join wo_pre_cost_embe_cost_dtls c on c.id=b.pre_cost_fabric_cost_dtls_id  where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_type=6 and a.is_short=2 and b.po_break_down_id in(".implode(',',$poArrs).")");
			if(count($emb_booking_info)>0){
				foreach($emb_booking_info as $row){
					$booking_data_arr[$row[csf('po_break_down_id')]][$row[csf('emblishment_name')]]['booking_no'][$row[csf('booking_no')]]=$row[csf('booking_no')];
					$booking_data_arr[$row[csf('po_break_down_id')]][$row[csf('emblishment_name')]]['booking_date'][$row[csf('booking_no')]]=change_date_format($row[csf('booking_date')]);
					$booking_data_arr[$row[csf('po_break_down_id')]][$row[csf('emblishment_name')]]['delivery_date'][$row[csf('booking_no')]]=change_date_format($row[csf('delivery_date')]);
					$booking_data_arr[$row[csf('po_break_down_id')]][$row[csf('emblishment_name')]]['wo_qnty']+=$row[csf('wo_qnty')];
				}
			}

			/* echo "<pre>";
			print_r($booking_data_arr); die; */



			ob_start();
			?>
			<div style="width:1650px">
			<fieldset style="width:100%;">
				<table width="1640">
					<tr class="form_caption">
						<td colspan="17" align="center">Work Load of Printing/Embroidery/Gmts Dyeing/Gmts Washing(PO Wise)</td>
					</tr>
					<tr class="form_caption">
						<td colspan="17" align="center"><? echo $company_library[$company_name]; ?></td>
					</tr>
				</table>
                <?

				foreach($data_arr as $embName=>$embType){

				?>
				<table id="table_header_1" class="rpt_table" width="1640" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
                       <tr>
						<th colspan="24" style="font-weight:bold"><? echo $emblishment_name_array[$embName]; ?></th>
                        </tr>
                        <tr>
						<th width="30">SL</th>
                        <th width="100">Company</th>
						<th width="100">Buyer</th>
                        <th width="100">Job No</th>
						<th width="120">Style Ref.</th>
						<th width="100">Order No</th>
						<th width="130">GMT Item</th>
						<th width="80">Order Qty / Pcs</th>
						<th width="70">PO Receive Date</th>
						<th width="70">Ship Date</th>
                        <th width="70">Ship Month</th>
                        <th width="60">Lead Time</th>
                        <th width="80">Internal Ref</th>
                        <th width="80" title="">File No</th>
                        <th width="70">Req. Qty- Pcs</th>
                        <th width="70">WO Number</th>
                        <th width="70">WO Date</th>
						<th width="70">WO Delivery date</th>
                        <th width="70">WO Qty</th>                        
						<th width="70">Receive Qty</th>
                        <th width="70">Balance</th>
						<th width="70">Start Date</th>
						<th width=""><!--T.O.D--></th>
                        </tr>
					</thead>
				</table>
				<div style="width:1660px; max-height:400px; overflow-y:scroll" id="scroll_body">
					<table class="rpt_table" width="1640" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
					<?
					 $totreq_qty=0;

					foreach($embType as $embTypeId=>$poArr){

						?>
                        <tr>
						<td colspan="24" style="font-weight:bold">
						<?
						if($embName==1){
						echo $emblishment_print_type[$embTypeId];
						}
						if($embName==2){
						echo $emblishment_embroy_type[$embTypeId];
						}
						if($embName==3){
						echo $emblishment_wash_type[$embTypeId];
						}
						if($embName==4){
						echo $emblishment_spwork_type[$embTypeId];
						}
						if($embName==5){
						echo $emblishment_gmts_type[$embTypeId];
						}
						?>
                        </td>
                        </tr>
                        <?
					foreach($poArr as $poId=>$row){
						 $job_no= $row['job_no'];
						 $costing_per_qnty=0;
						 $costing_per_id=return_field_value( "costing_per", "wo_pre_cost_mst","job_no ='$job_no' ");
						 	if($costing_per_id==1)
							{
								$costing_per_qnty=12;
							}
							if($costing_per_id==2)
							{
								$costing_per_qnty=1;
							}
							if($costing_per_id==3)
							{
								$costing_per_qnty=24;
							}
							if($costing_per_id==4)
							{
								$costing_per_qnty=36;
							}
							if($costing_per_id==5)
							{
								$costing_per_qnty=48;
							}
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						    $date_diff=datediff( "d", $row['po_received_date'] , $row['pub_shipment_date']);
							$req_qty=0;
							$req_qty_per_costing =0;

							if($embName==3){
						    	$req_qty=round($wash_qty_arr[$poId][$embName][$embTypeId])*$costing_per_qnty;
							}
							else{
								$req_qty=round($emblishment_qty_arr[$poId][$embName][$embTypeId])*$costing_per_qnty;
							}
							if($req_qty !=0){

								$booking_no_str=implode(", ",$booking_data_arr[$poId][$embName]['booking_no']);
								$booking_date_str=implode(", ",$booking_data_arr[$poId][$embName]['booking_date']);
								$booking_ddate_str=implode(", ",$booking_data_arr[$poId][$embName]['delivery_date']);
								$booking_qty=$booking_data_arr[$poId][$embName]['wo_qnty'];
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
							<td width="30"><? echo $i; ?></td>
                            <td width="100"><div style="word-wrap:break-word:100px;"><? echo $company_library[$row['company_name']]; ?></div></td>
							<td width="100"><div style="word-wrap:break-word:100px;"><? echo $buyer_library[$row['buyer_name']]; ?></div></td>
                            <td width="100"><div style="word-wrap:break-word:100px;"><? echo $row['job_no']; ?></div></td>
                            <td width="120"><div style="word-break:break-all"><? echo $row['style_ref_no']; ?></div></td>
							<td width="100" title="<? echo $row[csf('job_no')];?>"> <div style="word-break:break-all"> <? echo $row['po_number']; ?>   </div>
							<td width="130" align="center" >
							<?
							echo implode(",",$row['gmts_item_id']);
						    ?>
							</td>
							<td width="80" align="right" >
							<? echo number_format($row['plan_cut_qnty'],0); ?>
                            </td>
							<td width="70" align="left"><div style="word-break:break-all"><? echo change_date_format($row['po_received_date']);?> </div></td>
							<td width="70"   align="center" ><div style="word-break:break-all">
							<?
							$date=date_create($row['pub_shipment_date']);
							echo date_format($date,"d-M-Y");

							?>
                            </div>
							</td>
                            <td width="70"   align="center" ><div style="word-break:break-all">
							<?
							echo date_format($date,"F");
							?>
                            </div>
							</td>
							<td width="60" align="center">
                            <div style="word-wrap:break-word:80px;"> <? echo $date_diff;?> </div>
							</td>
							<td width="80" align="left"><div style="word-break:break-all">
							<?	echo $row['grouping']; ?> </div>
							</td>
							<td width="80"  align="center"><div style="word-break:break-all">
							<? echo  $row['file_no']; ?> </div></td>
							<td width="70" align="right" ><div style="word-break:break-all"><? echo number_format($req_qty,0);?></div></td>
							<td width="70" align="left" ><? echo $booking_no_str;?></td>
							<td width="70" align="left" ><? echo $booking_date_str;?></td>
							<td width="70" align="left" ><? echo $booking_ddate_str;?></td>
							<td width="70" align="right" ><? echo number_format($booking_qty,2);?></td>
							<td width="70" align="right"><p><? echo number_format($gmts_prod_arr[$poId][3][$embName],2); ?></p></td>
							<td width="70" align="right"><p><? echo number_format($req_qty-$gmts_prod_arr[$poId][3][$embName],2);; ?></p></td>
							<td width="70" align="center"><p><? echo change_date_format($gmts_prod_date_arr[$poId][3][$embName]); ?></p></td>
							<td width="" align="center"> <p> <? //echo $order_qty_pcs; ?>   </p></td>

						</tr>
					<?
					$total_po_qty+=$row['plan_cut_qnty'];
					$totreq_qty+=$req_qty;
					//$totreq_qty+=$req_qty_per_costing;

					//$total_fab_finish_req+=$fab_finish_req;
					$i++;
					}
					}
				}
					?>
					<!-- </table> -->
                    </div>
					<!-- <table class="rpt_table" width="1640" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all"> -->
						<tfoot>
						<th width="30"></th>
                        <th width="100"></th>
						<th width="100"></th>
                        <th width="100"></th>
						<th width="120"></th>
						<th width="100"></th>
						<th width="130"></th>
						<th width="80"><? echo $total_po_qty;?></th>
						<th width="70"></th>
						<th width="70"></th>
                        <th width="70"></th>
                        <th width="60"></th>
                        <th width="80"></th>
                        <th width="80" title=""></th>
                        <th width="70"><? echo number_format($totreq_qty,2); ?></th>
						<th width="70"></th>
                        <th width="70"></th>
						<th width="70"></th>
						<th width="70"></th>
						<th width="70"></th>
						<th width="70"></th>
						<th width="70"></th>
						<th width=""></th>
						</tfoot>
					</table>
                    <?
				}
					?>

				</fieldset>
			</div>
			<?
		}
	}
	//PO Wise End
	else if($report_type==2){
		if($template==1)
		{
					
					$sql=sql_select('select a.job_no AS "job_no",a.company_name AS "company_name",a.buyer_name AS "buyer_name",a.style_ref_no as "style_ref_no",b.id AS "id",b.po_number AS "po_number",b.pub_shipment_date AS "pub_shipment_date",b.po_received_date AS "po_received_date",b.grouping AS "grouping" , b.file_no AS "file_no",c.item_number_id AS "item_number_id",c.country_id AS "country_id",c.color_number_id AS "color_number_id",c.size_number_id AS "size_number_id",c.order_quantity AS "order_quantity",c.plan_cut_qnty AS "plan_cut_qnty",d.id AS "pre_cost_dtls_id",d.body_part_id AS "body_part_id",d.construction AS "construction",d.composition AS "composition",d.gsm_weight AS "gsm_weight",d.width_dia_type AS "width_dia_type",d.fab_nature_id AS "fab_nature_id",d.color_size_sensitive AS "color_size_sensitive",d.color_type_id AS "color_type_id",e.dia_width AS "dia_width", e.cons AS "cons",e.requirment AS "requirment",f.id AS "convertion_id",f.fabric_description AS "fabric_description",f.cons_process AS "cons_process",f.req_qnty AS "req_qnty",f.process_loss AS "process_loss",f.avg_req_qnty AS "avg_req_qnty",f.charge_unit AS "charge_unit",f.amount "amount" ,f.color_break_down AS "color_break_down"   from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_conv_cost_dtls f  where 1=1 '. $comp_cond . $date_cond . $buyer_id_cond . $year_cond . $job_no_cond . $order_id_cond_trans . $order_no_cond . $order_status_cond . $internal_ref_cond . $file_no_cond . $season_cond . $style_owner_cond . $team_leader_cond . $dealing_marchant_cond.' and a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_description  and e.cons !=0 and f.cons_process in (30,35,129)   and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1');
					

					$data_arr=array();
				 foreach($sql as $row){
					  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['company_name']=$row['company_name'];
					  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['buyer_name']=$row['buyer_name'];
					  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['job_no']=$row['job_no'];
					  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['style_ref_no']=$row['style_ref_no'];
					  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['po_number']=$row['po_number'];
					  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['pub_shipment_date']=$row['pub_shipment_date'];
					  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['po_received_date']=$row['po_received_date'];
					  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['grouping']=$row['grouping'];
					  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['file_no']=$row['file_no'];
					  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['plan_cut_qnty']+=$row['plan_cut_qnty'];
					  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['gmts_item_id']=$garments_item[$row['item_number_id']];

					  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['body_part_id']=$body_part[$row['body_part_id']];
					  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['color_type_id']=$color_type[$row['color_type_id']];
					  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['construction']=$row['construction'];
					  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['composition']=$row['composition'];
					  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['gsm_weight']=$row['gsm_weight'];
					  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['width_dia_type']=$fabric_typee[$row['width_dia_type']];
					  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['fab_nature_id']=$row['fab_nature_id'];
					  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['convertion_id']=$row['convertion_id'];
					  $poArrs[$row['id']]=$row['id'];

				 }
				  unset($sql);
				 $aop_data_arr=array();
				 $aop_booking_info=sql_select("SELECT a.booking_no, a.booking_date, a.delivery_date, b.po_break_down_id, 
					b.pre_cost_fabric_cost_dtls_id, b.wo_qnty, b.emblishment_name  from wo_booking_mst a,  wo_booking_dtls b  
					 where a.id=b.booking_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and 
					a.booking_type=3 and b.po_break_down_id in(".implode(',',$poArrs).")");
					if(count($aop_booking_info)>0){
						foreach($aop_booking_info as $row){
							$aop_data_arr[$row[csf('po_break_down_id')]]['booking_no'][$row[csf('booking_no')]]=$row[csf('booking_no')];
							$aop_data_arr[$row[csf('po_break_down_id')]]['booking_date'][$row[csf('booking_no')]]=change_date_format($row[csf('booking_date')]);
							$aop_data_arr[$row[csf('po_break_down_id')]]['delivery_date'][$row[csf('booking_no')]]=change_date_format($row[csf('delivery_date')]);
							$aop_data_arr[$row[csf('po_break_down_id')]]['wo_qnty']+=$row[csf('wo_qnty')];
						}
					}
					 unset($aop_booking_info);
					/* echo '10**<pre>';
					print_r($aop_data_arr); die; */

				     $condition= new condition();
					 if(str_replace("'","",$cbo_company_name)>0){
					 $condition->company_name("=$cbo_company_name");
					 }
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
						  //$condition->pub_shipment_date(" between '$start_date' and '$end_date'");
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
					//echo __LINE__; die;
					//var_dump($condition); die;
					$conversion= new conversion($condition);
					

					//echo $conversion->getQuery(); die;

					//$conversion_qty_arr=$conversion->getQtyArray_by_orderFabricProcessAndDiaWidth();
					//$conversion_qty_arr=$conversion->getQtyArray_by_OrderFabricAndProcessDiaWidth();
					$conversion_qty_arr=$conversion->getQtyArray_by_OrderFabricProcessAndDiaWidth();
					 /* echo '<pre>';
					 print_r($conversion_qty_arr); die; */

					



			ob_start();

		?>
			<div style="width:2250px">
			<fieldset style="width:100%;">
				<table width="2240">
					<tr class="form_caption">
						<td colspan="25" align="center">AOP/Burn Out/Yarn Dyeing</td>
					</tr>
					<tr class="form_caption">
						<td colspan="25" align="center"><? echo $company_library[$company_name]; ?></td>
					</tr>
				</table>
                <?
				foreach($data_arr as $consProcessId=>$fabricDescriptionArr){
				?>
				<table id="table_header_1" class="rpt_table" width="2220" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
                        <tr>
						<th width="36" colspan="27"><? echo $conversion_cost_head_array[$consProcessId]; ?></th>
                        </tr>
                        <tr>
						<th width="30">SL</th>
                        <th width="100">Company</th>
						<th width="100">Buyer</th>
                        <th width="100">Job No</th>
						<th width="120"><div style="word-wrap:break-word:100px;">Style Ref.</div></th>
						<th width="100"><div style="word-wrap:break-word:100px;">Order No</div></th>
                        <th width="130" title="">Gmts Item</th>
						<th width="130">Body Part</th>
						<th width="80">Color Type</th>
						<th width="120">Fabric Desc.</th>
						<th width="50">Fabric Gsm</th>
                        <th width="50">Fabric Dia</th>
                        <th width="70">Width/ Dia Type</th>

                        <th width="70">Order Qty</th>
						<th width="70">PO Receive Date</th>
                        <th width="70">Ship Date</th>
                        <th width="70">Ship Month</th>
						<th width="70">Lead Time</th>
						<th width="70">Internal Ref</th>
                        <th width="70">File No</th>
						<th width="70">UOM</th>
						<th width="70">Req. Qty - Kg</th>
						<th width="70">WO Number</th>
                        <th width="70">WO Date</th>
						<th width="70">WO Delivery date</th>
                        <th width="70">WO Qty</th>
                        <th width="70">Receive Qty</th>
						<th width="70">Balance</th>
                        <th width="70">Start Date</th>
						<th width="70">T.O.D</th>
                        <th width="">Sourcing</th>
                        </tr>
					</thead>
				<!-- </table>
				<div style="width:2240px; max-height:400px; overflow-y:scroll" id="scroll_body">
					<table class="rpt_table" width="2220" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body"> -->
					<?

					$i=1;
					$total_req=0;
					
					foreach($fabricDescriptionArr as $fabricDescriptionId=>$poArr)
					{
						foreach($poArr as $poId=>$DiaArr)
					    {
							foreach($DiaArr as $dia=>$row)
					        {
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$date_diff=datediff( "d", $row['po_received_date'] , $row['pub_shipment_date']);
								$convertion_id=$row['convertion_id'];
								$req_qty=array_sum($conversion_qty_arr[$poId][$fabricDescriptionId][$consProcessId][$dia]);

								//print_r($conversion_qty_arr[$poId][$fabricDescriptionId][30]);
								$booking_no_str=implode(",",$aop_data_arr[$poId][$convertion_id]['booking_no']);
								$booking_date_str=implode(",",$aop_data_arr[$poId][$convertion_id]['booking_date']);
								$booking_ddate_str=implode(",",$aop_data_arr[$poId][$convertion_id]['delivery_date']);
								$booking_qty=$aop_data_arr[$poId][$convertion_id]['wo_qnty'];

					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
							<td width="30"><? echo $i; ?></td>
                            <td width="100"><div style="word-wrap:break-word:100px;"><? echo $company_library[$row['company_name']]; ?></div></td>
							<td width="100"><div style="word-wrap:break-word:100px;"><? echo $buyer_library[$row['buyer_name']]; ?></div></td>
                            <td width="100"><div style="word-wrap:break-word:100px;"><? echo $row['job_no']; ?></div></td>
                            <td width="120"><p style="word-break:break-all"><? echo $row['style_ref_no']; ?></p></td>
							<td width="100" > <p style="word-break:break-all"> <? echo $row['po_number']; ?></p></td>
                            <td width="130"  align="center"><? echo $row['gmts_item_id']; ?></td>
							<td width="130" align="center" ><p><? echo $row['body_part_id']; ?></p></td>
							<td width="80" align="center" ><div style="word-break:break-all"><? echo $row['color_type_id']; ?></div></td>
							<td width="120" align="left"><p style="word-break:break-all"><? echo $row['construction']." ".$row['composition'];?> </p></td>
							<td width="50"   align="center" ><div style="word-break:break-all">
							<? echo $row['gsm_weight']; //echo change_date_format($row['pub_shipment_date']);?> </div>
							</td>
							<td width="50" align="center">
                            <div style="word-wrap:break-word:80px;"> <? echo $dia; //echo $date_diff;?> </div>
							</td>
							<td width="70" align="center"><div style="word-break:break-all">
							<?	echo $row['width_dia_type'];  //echo $row['grouping']; ?> </div>
							</td>

							<td align="right" width="70"><? echo number_format($row['plan_cut_qnty'],0); //echo  $row['file_no']; ?> </div></td>
							<td width="70" align="right" ><div style="word-break:break-all"><? echo change_date_format($row['po_received_date']); ?></div></td>
							<td width="70" align="center"><div style="word-break:break-all">
							<?
							$date=date_create($row['pub_shipment_date']);
							echo date_format($date,"d-M-Y");
							?>
                            </div></td>
                            <td width="70" align="center"><div style="word-break:break-all">
							<?
							echo date_format($date,"F");
							?>
                            </div></td>
							<td width="70" align="center"><div style="word-break:break-all"><? echo $date_diff; ?></div></td>
							<td width="70" align="center"><div style="word-break:break-all"><? echo $row['grouping'] ?></div></td>
                            <td width="70" align="right" ><div style="word-break:break-all"><? echo  $row['file_no']; ?></div></td>
							<td width="70" align="center"><div style="word-break:break-all">
							<?
							if($row['fab_nature_id']==2){
								echo "Kg";
							}
							else{
								echo "Yds";
							}
							?></div></td>
							<td width="70" align="right"><div style="word-break:break-all"><? echo number_format($req_qty,2) ?></div></td>
							<td width="70" align="left" ><? echo $booking_no_str;?></td>
							<td width="70" align="left" ><? echo $booking_date_str;?></td>
							<td width="70" align="left" ><? echo $booking_ddate_str;?></td>
							<td width="70" align="right" ><? echo number_format($booking_qty,2);?></td>
							<td width="70" align="center"><div style="word-break:break-all"><?  ?></div></td>
                            <td width="70" align="right" ><div style="word-break:break-all"><? //echo number_format($req_qty,2); ?></div></td>
							<td width="70" align="center"><div style="word-break:break-all"><? //echo $fab_gsm; ?></div></td>
							<td width="70" align="right"><div style="word-break:break-all"><? //echo $row[csf('po_quantity')]; ?></div></td>
							<td width="70" align="center"><div style="word-break:break-all"><?  ?></div></td>
							<td width="" align="center"> <p> <? //echo $order_qty_pcs; ?>   </p></td>

						</tr>
					<?
					$total_po_qty+=$row['plan_cut_qnty'];
					$total_req+=$req_qty;

					//$total_fab_finish_req+=$fab_finish_req;
					$i++;
							}
						}
					}
					?>
					<!-- </table> -->
                    <!-- </div> -->
					<!-- <table class="rpt_table" width="2220" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all"> -->
						<tfoot>
						<th width="30"></th>
                        <th width="100"></th>
						<th width="100"></th>
                        <th width="100"></th>
						<th width="120"><div style="word-wrap:break-word:100px;"></div></th>
						<th width="100"><div style="word-wrap:break-word:100px;"></div></th>
                        <th width="130" title=""></th>
						<th width="130"></th>
						<th width="80"></th>
						<th width="120"></th>
						<th width="50"></th>
                        <th width="50"></th>
                        <th width="70"></th>
                        <th width="70"></th>
                        <th width="70"></th>
						<th width="70"></th>
                        <th width="70"> </th>
						<th width="70"> </th>
						<th width="70"> </th>
                        <th width="70"> </th>
						<th width="70"></th>
						<th width="70"><? echo number_format($total_req,2) ?></th>
                        <th width="70"></th>
						<th width="70"></th>
                        <th width="70"></th>
						<th width="70"></th>
						<th width="70"></th>
						<th width="70"></th>
						<th width="70"></th>
						<th width="70"></th>
                        <th width=""></th>
						</tfoot>
					</table>
				<?
		}
				?>
				</fieldset>
			</div>
	<?

		}
	} // Country End
	else if($report_type==3){
		if($template==1)
		{
		 $sql=sql_select('select a.job_no AS "job_no",a.company_name AS "company_name",a.buyer_name AS "buyer_name", a.style_ref_no as "style_ref_no",a.total_set_qnty AS "total_set_qnty",b.id AS "id",b.po_number AS "po_number",b.pub_shipment_date AS "pub_shipment_date",b.po_received_date AS "po_received_date",b.grouping AS "grouping" , b.file_no AS "file_no",c.item_number_id AS "item_number_id",c.country_id AS "country_id",c.color_number_id AS "color_number_id",c.size_number_id AS "size_number_id",c.order_quantity AS "order_quantity",c.plan_cut_qnty AS "plan_cut_qnty",d.id AS "emblishment_id",d.emb_name AS "emb_name",d.emb_type AS "emb_type",d.cons_dzn_gmts AS "cons_dzn_gmts",d.rate AS "rate",d.amount AS "amount"   from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_embe_cost_dtls d  where 1=1 '. $comp_cond . $date_cond . $buyer_id_cond . $year_cond . $job_no_cond . $order_id_cond_trans . $order_no_cond . $order_status_cond . $internal_ref_cond . $file_no_cond . $season_cond . $style_owner_cond . $team_leader_cond . $dealing_marchant_cond .' and d.emb_name in(1,2,3,4,5) and cons_dzn_gmts>0 and  a.id=b.job_id and a.id=c.job_id and a.id=d.job_id  and b.id=c.po_break_down_id   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1');
				 $data_arr=array();
				 foreach($sql as $row){
					  $data_arr[$row['emb_name']][$row['emb_type']][$row['id']]['company_name']=$row['company_name'];
					  $data_arr[$row['emb_name']][$row['emb_type']][$row['id']]['buyer_name']=$row['buyer_name'];
					  $data_arr[$row['emb_name']][$row['emb_type']][$row['id']]['job_no']=$row['job_no'];
					  $data_arr[$row['emb_name']][$row['emb_type']][$row['id']]['style_ref_no']=$row['style_ref_no'];
					  $data_arr[$row['emb_name']][$row['emb_type']][$row['id']]['po_number']=$row['po_number'];
					  $data_arr[$row['emb_name']][$row['emb_type']][$row['id']]['pub_shipment_date']=$row['pub_shipment_date'];
					  $data_arr[$row['emb_name']][$row['emb_type']][$row['id']]['po_received_date']=$row['po_received_date'];
					  $data_arr[$row['emb_name']][$row['emb_type']][$row['id']]['grouping']=$row['grouping'];
					  $data_arr[$row['emb_name']][$row['emb_type']][$row['id']]['file_no']=$row['file_no'];
					  $data_arr[$row['emb_name']][$row['emb_type']][$row['id']]['plan_cut_qnty']+=$row['plan_cut_qnty'];

					  $data_arr[$row['emb_name']][$row['emb_type']][$row['id']]['gmts_item_id'][$row['item_number_id']]=$garments_item[$row['item_number_id']];
				 }



				$i=1; $total_po_qty=0; $total_ex_fact_qty=0;$total_tot_fob_price=0;$total_tot_ex_fact_val=0;
					$total_cm_margin_val=0;	$total_qlty_qty=0;	$total_up_charge=0;	 $total_fab_finish_req=0;
					 $condition= new condition();
					 if(str_replace("'","",$cbo_company_name)>0){
					 $condition->company_name("=$cbo_company_name");
					 }
					 if(str_replace("'","",$cbo_buyer_name)>0){
						  $condition->buyer_name("=$cbo_buyer_name");
					 }
					//$txt_job_no= 'D n C-17-00143';
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

					$condition->init();
					$emblishment= new emblishment($condition);
					//echo $emblishment->getQuery();
					$emblishment_qty_arr=$emblishment->getQtyArray_by_orderEmbnameAndEmbtype();
					$wash= new wash($condition);
					$wash_qty_arr=$wash->getQtyArray_by_orderEmbnameAndEmbtype();





			ob_start();
		?>
			<div style="width:1550px">
			<fieldset style="width:100%;">
				<table width="1540">
					<tr class="form_caption">
						<td colspan="17" align="center">Work Load of Printing/Embroidery/Gmts Dyeing/Gmts Washing(PO Wise)</td>
					</tr>
					<tr class="form_caption">
						<td colspan="17" align="center"><? echo $company_library[$company_name]; ?></td>
					</tr>
				</table>

				<table id="table_header_1" class="rpt_table" width="1740" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>

                        <tr>
						<th width="30">SL</th>
                        <th width="100">Company</th>
						<th width="100">Buyer</th>
                        <th width="100">Job No</th>
						<th width="120">Style Ref.</th>
						<th width="100">Order No</th>
						<th width="130">GMT Item</th>
						<th width="80">Order Qty / Pcs</th>
						<th width="70">PO Receive Date</th>
						<th width="70">Ship Date</th>
                        <th width="70">Ship Month</th>
                        <th width="60">Lead Time</th>
                        <th width="80">Internal Ref</th>
                        <th width="80" title="">File No</th>
                        <th width="80">Emb Name</th>
                        <th width="80">Emb Type</th>
                        <th width="70">Req. Qty- Pcs</th>
						<th width="70">Receive Qty</th>
                        <th width="70">Balance</th>
						<th width="70">Start Date</th>
						<th width="">T.O.D</th>
                        </tr>
					</thead>
				</table>
				<div style="width:1760px; max-height:400px; overflow-y:scroll" id="scroll_body">
					<table class="rpt_table" width="1740" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
					<?
					$totreq_qty=0;
					foreach($data_arr as $embName=>$embType){
					foreach($embType as $embTypeId=>$poArr){
					foreach($poArr as $poId=>$row){
						$job_no= $row['job_no'];
						$costing_per_qnty=0;
						$costing_per_id=return_field_value( "costing_per", "wo_pre_cost_mst","job_no ='$job_no' ");
						if($costing_per_id==1)
						{
						$costing_per_qnty=12;
						}
						if($costing_per_id==2)
						{
						$costing_per_qnty=1;
						}
						if($costing_per_id==3)
						{
						$costing_per_qnty=24;
						}
						if($costing_per_id==4)
						{
						$costing_per_qnty=36;
						}
						if($costing_per_id==5)
						{
						$costing_per_qnty=48;
						}
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							//$po_quantity=$row[csf('po_quantity')];
						    $date_diff=datediff( "d", $row['po_received_date'] , $row['pub_shipment_date']);
							$req_qty=array();
							if($embName==3){
						    $req_qty=round($wash_qty_arr[$poId][$embName][$embTypeId])*$costing_per_qnty;
							}
							else{
								$req_qty=round($emblishment_qty_arr[$poId][$embName][$embTypeId])*$costing_per_qnty;
							}

					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
							<td width="30"><? echo $i; ?></td>
                            <td width="100"><div style="word-wrap:break-word:100px;"><? echo $company_library[$row['company_name']]; ?></div></td>
							<td width="100"><div style="word-wrap:break-word:100px;"><? echo $buyer_library[$row['buyer_name']]; ?></div></td>
                            <td width="100"><div style="word-wrap:break-word:100px;"><? echo $row['job_no']; ?></div></td>
                            <td width="120"><div style="word-break:break-all"><? echo $row['style_ref_no']; ?></div></td>
							<td width="100" title="<? echo $row[csf('job_no')];?>"> <div style="word-break:break-all"> <? echo $row['po_number']; ?>   </div>
							<td width="130" align="center" >
							<?
							echo implode(",",$row['gmts_item_id']);
						    ?>
							</td>
							<td width="80" align="right" >
							<? echo number_format($row['plan_cut_qnty'],0); ?>
                            </td>
							<td width="70" align="left"><div style="word-break:break-all"><? echo change_date_format($row['po_received_date']);?> </div></td>
							<td width="70"   align="center" ><div style="word-break:break-all">
							<?
							$date= date_create($row['pub_shipment_date']);
							echo date_format($date,"d-M-Y");
							?> </div>
							</td>
                            <td width="70"   align="center" ><div style="word-break:break-all">
							<?
							$date= date_create($row['pub_shipment_date']);
							echo date_format($date,"F");
							?> </div>
							</td>
							<td width="60" align="center">
                            <div style="word-wrap:break-word:80px;"> <? echo $date_diff;?> </div>
							</td>
							<td width="80" align="left"><div style="word-break:break-all">
							<?	echo $row['grouping']; ?> </div>
							</td>
							<td width="80"  align="center"><div style="word-break:break-all"><? echo  $row['file_no']; ?> </div></td>
                            <td width="80"  align="center"><div style="word-break:break-all"><? echo $emblishment_name_array[$embName];?> </div></td>
                            <td width="80"  align="center">
                            <div style="word-break:break-all">
							<?
							if($embName==1){
							echo $emblishment_print_type[$embTypeId];
							}
							if($embName==2){
							echo $emblishment_embroy_type[$embTypeId];
							}
							if($embName==3){
							echo $emblishment_wash_type[$embTypeId];
							}
							if($embName==4){
							echo $emblishment_spwork_type[$embTypeId];
							}
							if($embName==5){
							echo $emblishment_gmts_type[$embTypeId];
							}
							?>
                            </div>
                            </td>
							<td width="70" align="right" ><div style="word-break:break-all"><? echo number_format($req_qty,2); ?></div></td>
							<td width="70" align="center"><div style="word-break:break-all"><? //echo $fab_gsm; ?></div></td>
							<td width="70" align="right"><div style="word-break:break-all"><? //echo $row[csf('po_quantity')]; ?></div></td>
							<td width="70" align="center"><div style="word-break:break-all"><?  ?></div></td>
							<td width="" align="center"> <p> <? //echo $order_qty_pcs; ?>   </p></td>

						</tr>
					<?
					$total_po_qty+=$row['plan_cut_qnty'];
					$totreq_qty+=$req_qty;

					//$total_fab_finish_req+=$fab_finish_req;
					$i++;
					}
				}
				}
					?>
					</table>
                    </div>
					<table class="rpt_table" width="1740" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
						<tfoot>
						<th width="30"></th>
                        <th width="100"></th>
						<th width="100"></th>
                        <th width="100"></th>
						<th width="120"></th>
						<th width="100"></th>
						<th width="130"></th>
						<th width="80"><? echo $total_po_qty;?></th>
						<th width="70"></th>
						<th width="70"></th>
                        <th width="70"></th>
                        <th width="60"></th>
                        <th width="80"></th>
                        <th width="80" title=""></th>
                        <th width="80" title=""></th>
                        <th width="80" title=""></th>
                        <th width="70"><? echo number_format($totreq_qty,2); ?></th>
						<th width="70"></th>
                        <th width="70"></th>
						<th width="70"></th>
						<th width=""></th>
						</tfoot>
					</table>
                    <?

					?>

				</fieldset>
			</div>
	<?

		}
	}
	else if($report_type==4){
		if($template==1)
		{

					$sql=sql_select('select a.job_no AS "job_no",a.company_name AS "company_name",a.buyer_name AS "buyer_name",a.style_ref_no as "style_ref_no",b.id AS "id",b.po_number AS "po_number",b.pub_shipment_date AS "pub_shipment_date",b.po_received_date AS "po_received_date",b.grouping AS "grouping" , b.file_no AS "file_no",c.item_number_id AS "item_number_id",c.country_id AS "country_id",c.color_number_id AS "color_number_id",c.size_number_id AS "size_number_id",c.order_quantity AS "order_quantity",c.plan_cut_qnty AS "plan_cut_qnty",d.id AS "pre_cost_dtls_id",d.body_part_id AS "body_part_id",d.construction AS "construction",d.composition AS "composition",d.gsm_weight AS "gsm_weight",d.width_dia_type AS "width_dia_type",d.fab_nature_id AS "fab_nature_id",d.color_size_sensitive AS "color_size_sensitive",d.color_type_id AS "color_type_id",e.dia_width AS "dia_width", e.cons AS "cons",e.requirment AS "requirment",f.id AS "convertion_id",f.fabric_description AS "fabric_description",f.cons_process AS "cons_process",f.req_qnty AS "req_qnty",f.process_loss AS "process_loss",f.avg_req_qnty AS "avg_req_qnty",f.charge_unit AS "charge_unit",f.amount "amount" ,f.color_break_down AS "color_break_down"   from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_conv_cost_dtls f  where 1=1 '. $comp_cond . $date_cond . $buyer_id_cond . $year_cond . $job_no_cond . $order_id_cond_trans . $order_no_cond . $order_status_cond . $internal_ref_cond . $file_no_cond . $season_cond . $style_owner_cond . $team_leader_cond . $dealing_marchant_cond.' and a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and a.job_no=f.job_no and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_description  and e.cons !=0 and f.cons_process in (30,35,129)   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1');

					$data_arr=array();
				 foreach($sql as $row){
					  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['company_name']=$row['company_name'];
					  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['buyer_name']=$row['buyer_name'];
					  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['job_no']=$row['job_no'];
					  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['style_ref_no']=$row['style_ref_no'];
					  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['po_number']=$row['po_number'];
					  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['pub_shipment_date']=$row['pub_shipment_date'];
					  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['po_received_date']=$row['po_received_date'];
					  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['grouping']=$row['grouping'];
					  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['file_no']=$row['file_no'];
					  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['plan_cut_qnty']+=$row['plan_cut_qnty'];
					  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['gmts_item_id']=$garments_item[$row['item_number_id']];

					  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['body_part_id']=$body_part[$row['body_part_id']];
					  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['color_type_id']=$color_type[$row['color_type_id']];
					  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['construction']=$row['construction'];
					  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['composition']=$row['composition'];
					  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['gsm_weight']=$row['gsm_weight'];
					  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['width_dia_type']=$fabric_typee[$row['width_dia_type']];
					  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['fab_nature_id']=$row['fab_nature_id'];

				 }


				     $condition= new condition();
					 if(str_replace("'","",$cbo_company_name)>0){
					 $condition->company_name("=$cbo_company_name");
					 }
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
						  //$condition->pub_shipment_date(" between '$start_date' and '$end_date'");
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
					$conversion= new conversion($condition);
					$conversion_qty_arr=$conversion->getQtyArray_by_orderFabricProcessAndDiaWidth();
			ob_start();

		?>
			<div style="width:2350px">
			<fieldset style="width:100%;">
				<table width="2340">
					<tr class="form_caption">
						<td colspan="25" align="center">AOP/Burn Out/Yarn Dyeing</td>
					</tr>
					<tr class="form_caption">
						<td colspan="25" align="center"><? echo $company_library[$company_name]; ?></td>
					</tr>
				</table>
                <?

				?>
				<table id="table_header_1" class="rpt_table" width="2320" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>

                        <tr>
						<th width="30">SL</th>
                        <th width="100">Company</th>
						<th width="100">Buyer</th>
                        <th width="100">Job No</th>
						<th width="120"><div style="word-wrap:break-word:100px;">Style Ref.</div></th>
						<th width="100"><div style="word-wrap:break-word:100px;">Order No</div></th>
                        <th width="130" title="">Gmts Item</th>
						<th width="130">Body Part</th>
						<th width="80">Color Type</th>
						<th width="120">Fabric Desc.</th>
						<th width="50">Fabric Gsm</th>
                        <th width="50">Fabric Dia</th>
                        <th width="70">Width/ Dia Type</th>

                        <th width="70">Order Qty</th>
						<th width="70">PO Receive Date</th>
                        <th width="70">Ship Date</th>
                        <th width="70">Ship Month</th>
						<th width="70">Lead Time</th>
						<th width="70">Internal Ref</th>
                        <th width="70">File No</th>
						<th width="70">UOM</th>
                        <th width="70">Process</th>
						<th width="70">Req. Qty - Kg</th>
                        <th width="70">Receive Qty</th>
						<th width="70">Balance</th>
                        <th width="70">Start Date</th>
						<th width="70">T.O.D</th>
                        <th width="">Sourcing</th>
                        </tr>
					</thead>
				</table>
				<div style="width:2340px; max-height:400px; overflow-y:scroll" id="scroll_body">
					<table class="rpt_table" width="2320" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
					<?

					$i=1;
					$total_req=0;
					foreach($data_arr as $consProcessId=>$fabricDescriptionArr){
					foreach($fabricDescriptionArr as $fabricDescriptionId=>$poArr)
					{
						foreach($poArr as $poId=>$DiaArr)
					    {
							foreach($DiaArr as $dia=>$row)
					        {
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$date_diff=datediff( "d", $row['po_received_date'] , $row['pub_shipment_date']);
								$req_qty=$conversion_qty_arr[$poId][$fabricDescriptionId][$consProcessId][$dia];

								//print_r($conversion_qty_arr[$poId][$fabricDescriptionId][30]);

					?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
							<td width="30"><? echo $i; ?></td>
                            <td width="100"><div style="word-wrap:break-word:100px;"><? echo $company_library[$row['company_name']]; ?></div></td>
							<td width="100"><div style="word-wrap:break-word:100px;"><? echo $buyer_library[$row['buyer_name']]; ?></div></td>
                            <td width="100"><div style="word-wrap:break-word:100px;"><? echo $row['job_no']; ?></div></td>
                            <td width="120"><div style="word-break:break-all"><? echo $row['style_ref_no']; ?></div></td>
							<td width="100" > <div style="word-break:break-all"> <? echo $row['po_number']; ?>   </div></td>
                            <td width="130"  align="center"><? echo $row['gmts_item_id']; ?></td>
							<td width="130" align="center" >
							<? echo $row['body_part_id']; ?>
							</td>
							<td width="80" align="center" ><div style="word-break:break-all"><? echo $row['color_type_id']; ?></div></td>
							<td width="120" align="left"><div style="word-break:break-all"><? echo $row['construction']." ".$row['composition'];?> </div></td>
							<td width="50"   align="center" ><div style="word-break:break-all">
							<? echo $row['gsm_weight']; //echo change_date_format($row['pub_shipment_date']);?> </div>
							</td>
							<td width="50" align="center">
                            <div style="word-wrap:break-word:80px;"> <? echo $dia; //echo $date_diff;?> </div>
							</td>
							<td width="70" align="center"><div style="word-break:break-all">
							<?	echo $row['width_dia_type'];  //echo $row['grouping']; ?> </div>
							</td>

							<td align="right" width="70"><? echo number_format($row['plan_cut_qnty'],0); //echo  $row['file_no']; ?> </div></td>
							<td width="70" align="right" ><div style="word-break:break-all"><? echo change_date_format($row['po_received_date']); ?></div></td>
							<td width="70" align="center"><div style="word-break:break-all">
							<?
							$date= date_create($row['pub_shipment_date']);
							echo date_format($date,"d-M-Y");
							?>
                            </div>
                            </td>
                           <td width="70" align="center"><div style="word-break:break-all">
							<?
							echo date_format($date,"F");
							?>
                            </div>
                            </td>
							<td width="70" align="center"><div style="word-break:break-all"><? echo $date_diff; ?></div></td>
							<td width="70" align="center"><div style="word-break:break-all"><? echo $row['grouping'] ?></div></td>
                            <td width="70" align="right" ><div style="word-break:break-all"><? echo  $row['file_no']; ?></div></td>
							<td width="70" align="center"><div style="word-break:break-all">
							<?
							if($row['fab_nature_id']==2){
								echo "Kg";
							}
							else{
								echo "Yds";
							}
							?></div></td>
                            <td width="70" align=""><div style="word-break:break-all"><? echo $conversion_cost_head_array[$consProcessId];?></div></td>
							<td width="70" align="right"><div style="word-break:break-all"><? echo number_format($req_qty,2) ?></div></td>
							<td width="70" align="center"><div style="word-break:break-all"><?  ?></div></td>
                            <td width="70" align="right" ><div style="word-break:break-all"><? //echo number_format($req_qty,2); ?></div></td>
							<td width="70" align="center"><div style="word-break:break-all"><? //echo $fab_gsm; ?></div></td>
							<td width="70" align="right"><div style="word-break:break-all"><? //echo $row[csf('po_quantity')]; ?></div></td>
							<td width="70" align="center"><div style="word-break:break-all"><?  ?></div></td>
							<td width="" align="center"> <p> <? //echo $order_qty_pcs; ?>   </p></td>

						</tr>
					<?
					$total_po_qty+=$row[csf('po_quantity')];
					$total_req+=$req_qty;

					//$total_fab_finish_req+=$fab_finish_req;
					$i++;
							}
						}
					}
				}
					?>
					</table>
                    </div>
					<table class="rpt_table" width="2320" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
						<tfoot>
						<th width="30"></th>
                        <th width="100"></th>
						<th width="100"></th>
                        <th width="100"></th>
						<th width="120"><div style="word-wrap:break-word:100px;"></div></th>
						<th width="100"><div style="word-wrap:break-word:100px;"></div></th>
                        <th width="130" title=""></th>
						<th width="130"></th>
						<th width="80"></th>
						<th width="120"></th>
						<th width="50"></th>
                        <th width="50"></th>
                        <th width="70"></th>

                        <th width="70"></th>
						<th width="70"></th>
                        <th width="70"> </th>
						<th width="70"> </th>
                        <th width="70"> </th>
						<th width="70"> </th>
                        <th width="70"> </th>
						<th width="70"></th>
                        <th width="70"></th>
						<th width="70"><? echo number_format($total_req,2) ?></th>
                        <th width="70"></th>
						<th width="70"></th>
                        <th width="70"></th>
						<th width="70"></th>
                        <th width=""></th>
						</tfoot>
					</table>

				</fieldset>
			</div>
	<?


		}
	}
	else if($report_type==5){
		if($template==1)
		{

		$sql=sql_select('select a.id as job_id,a.job_no AS "job_no",a.company_name AS "company_name",a.buyer_name AS "buyer_name",a.style_ref_no as "style_ref_no",b.id AS "id",b.po_number AS "po_number",b.pub_shipment_date AS "pub_shipment_date",b.po_received_date AS "po_received_date",b.grouping AS "grouping" , b.file_no AS "file_no",c.item_number_id AS "item_number_id",c.country_id AS "country_id",c.color_number_id AS "color_number_id",c.size_number_id AS "size_number_id",c.order_quantity AS "order_quantity",c.plan_cut_qnty AS "plan_cut_qnty",d.id AS "pre_cost_dtls_id",d.body_part_id AS "body_part_id",d.construction AS "construction",d.composition AS "composition",d.gsm_weight AS "gsm_weight",d.width_dia_type AS "width_dia_type",d.fab_nature_id AS "fab_nature_id",d.color_size_sensitive AS "color_size_sensitive",d.color_type_id AS "color_type_id",e.dia_width AS "dia_width", e.cons AS "cons",e.requirment AS "requirment",f.id AS "convertion_id",f.fabric_description AS "fabric_description",f.cons_process AS "cons_process",f.req_qnty AS "req_qnty",f.process_loss AS "process_loss",f.avg_req_qnty AS "avg_req_qnty",f.charge_unit AS "charge_unit",f.amount "amount" ,f.color_break_down AS "color_break_down"   from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_conv_cost_dtls f  where a.is_deleted=0 '. $comp_cond . $date_cond . $buyer_id_cond . $year_cond . $job_no_cond . $order_id_cond_trans . $order_no_cond . $order_status_cond . $internal_ref_cond . $file_no_cond . $season_cond . $style_owner_cond . $team_leader_cond . $dealing_marchant_cond.' and a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_description   and a.status_active=1  and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 order by a.id');
					//and e.cons !=0 and f.cons_process in (30,35,129)

	$data_arr=array();
	 foreach($sql as $row){
		  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['company_name']=$row['company_name'];
		  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['buyer_name']=$row['buyer_name'];
		  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['job_no']=$row['job_no'];
		  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['style_ref_no']=$row['style_ref_no'];
		  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['po_number']=$row['po_number'];
		  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['pub_shipment_date']=$row['pub_shipment_date'];
		  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['po_received_date']=$row['po_received_date'];
		  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['grouping']=$row['grouping'];
		  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['file_no']=$row['file_no'];
		  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['plan_cut_qnty']+=$row['plan_cut_qnty'];
		  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['gmts_item_id']=$garments_item[$row['item_number_id']];

		  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['body_part_id']=$body_part[$row['body_part_id']];
		  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['color_type_id']=$color_type[$row['color_type_id']];
		  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['construction']=$row['construction'];
		  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['composition']=$row['composition'];
		  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['gsm_weight']=$row['gsm_weight'];
		  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['width_dia_type']=$fabric_typee[$row['width_dia_type']];
		  $data_arr[$row['cons_process']][$row['fabric_description']][$row['id']][$row['dia_width']]['fab_nature_id']=$row['fab_nature_id'];

	 }


	 $condition= new condition();
	 if(str_replace("'","",$cbo_company_name)>0){
	 $condition->company_name("=$cbo_company_name");
	 }
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

	$condition->init();
	$conversion= new conversion($condition);
	$conversion_qty_arr=$conversion->getQtyArray_by_OrderFabricProcessAndDiaWidth();


	$width=1800;
	ob_start();

		?>
			<div style="width:<?= $width+30;?>px">
			<fieldset style="width:100%;">
				<table width="<?= $width+20;?>">
					<tr class="form_caption">
						<td colspan="21" style="font-size:18px;" align="center"><? echo $company_name_library[$company_name]; ?></td>
					</tr>
                    <tr>
                        <th colspan="21"><b>Fabric Process Wise Finish Qty</b></th>
                    </tr>
				</table>
				<table id="table_header_1" class="rpt_table" width="<?= $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
                        <tr>
                            <th width="30">SL</th>
                            <th width="60">Company</th>
                            <th width="100">Buyer</th>
                            <th width="100">Job No</th>
                            <th width="120">Style Ref.</th>
                            <th width="100">Order No</th>
                            <th width="130" title="">Gmts Item</th>
                            <th width="130">Body Part</th>
                            <th width="80">Color Type</th>
                            <th width="80">Fabric Process</th>
                            <th width="120">Fabric Desc.</th>
                            <th width="80">Fabric Gsm</th>
                            <th width="80">Fabric Dia</th>
                            <th width="70">Width/ Dia Type</th>
                            <th width="70">Order Qty</th>
                            <th width="70">PO Receive Date</th>
                            <th width="70">Ship Date</th>
                            <th width="70">Ship Month</th>
                            <th width="70">Lead Time</th>
                            <th width="70">UOM</th>
                            <th>Req. Qty - Kg</th>
                        </tr>
					</thead>
				</table>
				<div style="width:<?= $width+20;?>px; max-height:400px; overflow-y:scroll" id="scroll_body">
					<table class="rpt_table" width="<?= $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
					
					<?
					$i=1; $total_req=0;
                    foreach($data_arr as $consProcessId=>$fabricDescriptionArr){

					
					
						foreach($fabricDescriptionArr as $fabricDescriptionId=>$poArr)
						{
							foreach($poArr as $poId=>$DiaArr)
							{
								foreach($DiaArr as $dia=>$row)
								{
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$date_diff=datediff( "d", $row['po_received_date'] , $row['pub_shipment_date']);
								$req_qty=array_sum($conversion_qty_arr[$poId][$fabricDescriptionId][$consProcessId][$dia]);
								if($req_qty>0){
							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
								<td width="30"><? echo $i; ?></td>
								<td width="60"><div style="word-wrap:break-word:58px;"><? echo $company_library[$row['company_name']]; ?></div></td>
								<td width="100"><div style="word-wrap:break-word:100px;"><? echo $buyer_library[$row['buyer_name']]; ?></div></td>
								<td width="100"><div style="word-wrap:break-word:100px;"><? echo $row['job_no']; ?></div></td>
								<td width="120"><p style="word-break:break-all"><? echo $row['style_ref_no']; ?></p></td>
								<td width="100" ><p style="word-break:break-all"> <? echo $row['po_number']; ?></p></td>
								<td width="130" align="center"><? echo $row['gmts_item_id']; ?></td>
								<td width="130" align="center"><p><? echo $row['body_part_id']; ?></p></td>
								<td width="80" align="center"><div style="word-break:break-all"><? echo $row['color_type_id']; ?></div></td>
                                <td width="80"><p><? echo $conversion_cost_head_array[$consProcessId]; ?></p></td>
								<td width="120" align="left"><p style="word-break:break-all"><? echo $row['construction']." ".$row['composition'];?> </p></td>
								<td width="80"   align="center" ><div style="word-break:break-all">
								<? echo $row['gsm_weight'];?> </div>
								</td>
								<td width="80" align="center"><div style="word-wrap:break-word:48px;"> <? echo $dia;?> </div></td>
								<td width="70" align="center"><div style="word-break:break-all"><?	echo $row['width_dia_type']; ?> </div></td>
	
								<td align="right" width="70"><? echo number_format($row['plan_cut_qnty'],0);?></td>
								<td width="70" align="right" ><div style="word-break:break-all"><? echo change_date_format($row['po_received_date']); ?></div></td>
								<td width="70" align="center"><div style="word-break:break-all">
								<?
									$date=date_create($row['pub_shipment_date']);
									echo date_format($date,"d-M-Y");
								?>
								</div></td>
								<td width="70" align="center"><div style="word-break:break-all">
								<?
								echo date_format($date,"F");
								?>
								</div></td>
								<td width="70" align="center"><div style="word-break:break-all"><? echo $date_diff; ?></div></td>
								<td width="70" align="center"><div style="word-break:break-all">
								<?
								if($row['fab_nature_id']==2){
									echo "Kg";
								}
								else{
									echo "Yds";
								}
								?></div></td>
								<td align="right"><div style="word-break:break-all"><? echo number_format($req_qty,2) ?></div></td>
	
							</tr>
						<?
								$total_po_qty+=$row['plan_cut_qnty'];
								$total_req+=$req_qty;
								$i++;
								}
								}
							}
						}
					}
					?>
					</table>
                    </div>
					<table class="rpt_table" width="<?= $width;?>" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
						<tfoot>
						<th width="30"></th>
                        <th width="60"></th>
						<th width="100"></th>
                        <th width="100"></th>
						<th width="120"></th>
						<th width="100"></th>
                        <th width="130"></th>
						<th width="130"></th>
						<th width="80"></th>
						<th width="80"></th>
						<th width="120"></th>
						<th width="80"></th>
                        <th width="80"></th>
                        <th width="70"></th>
                        <th width="70"></th>
                        <th width="70"></th>
						<th width="70"></th>
                        <th width="70"> </th>
						<th width="70"> </th>
						<th width="70"> </th>
						<th><? //echo number_format($total_req,2) ?></th>
						</tfoot>
					</table>

				</fieldset>
			</div>
	<?

		}
	}
	
	
	
	
	

	$html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
	//fopen("http://someurl/file.zip", 'r')
    echo "$html****$filename****$report_type";
	exit();
}


if($action=="show_po_fabric_dtls")
{
	echo load_html_head_contents("Po Order Dtls Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);

	$booking_no=return_field_value( "booking_no", "wo_booking_mst","booking_type=1 and is_short=2 and job_no='$job_no' ");

	$order_arr=return_library_array( "select id, po_number from wo_po_break_down where id='$po_id'", "id", "po_number");
	$color_name_arr=return_library_array( "select id, color_name from lib_color ", "id", "color_name");

	if($type_id==5)
	{
		$td_width=860;
		$row_span=10;
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
				//and a.body_part_id in(1,10,11,14,15,16,17,20,69,95,100,125,128,129,131,132,135,143,149,152,164,167,171,191,198,201,208,219)
				 $sql_book=("Select a.item_number_id as gmts_item_id,a.body_part_id,a.job_no,b.booking_no,c.fabric_color_id,c.color_type,a.uom,c.construction,c.copmposition,c.gsm_weight,c.dia_width,c.po_break_down_id as po_id,sum(c.fin_fab_qnty) as fin_fab_qnty from wo_pre_cost_fabric_cost_dtls a,wo_booking_mst b,wo_booking_dtls c where a.id=c.pre_cost_fabric_cost_dtls_id and a.job_no=c.job_no and c.booking_no=b.booking_no  and b.booking_type=1 and b.is_short=2 and b.company_id='$company_id'  and c.po_break_down_id in($po_id) and c.status_active=1 and c.is_deleted=0 group by a.item_number_id ,a.body_part_id,a.job_no,b.booking_no,c.fabric_color_id,c.color_type,a.uom,c.construction,c.copmposition,c.gsm_weight,c.dia_width,c.po_break_down_id");
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

						<td width="90" align="right"><p><? echo number_format($row[csf('fin_fab_qnty')],2); ?></p></td>

						<td width="" align="center"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>

					</tr>
					<?

					$total_fin_fab_qnty+=$row[csf('fin_fab_qnty')];
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
if($action=="show_po_ship_date_dtls")
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