<?
header('Content-type:text/html; charset=utf-8');
session_start();
//ini_set('memory_limit','3072M');
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
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
require_once('../../../../includes/class4/class.fabrics.php');
require_once('../../../../includes/class4/class.washes.php');


$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
//--------------------------------------------------------------------------------------------------------------------
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];


if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "");     	 
	exit();
}

$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
$yarn_count_library=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
$team_member_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
//$order_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");
$approve_arr=return_library_array( "select job_no, approved from wo_pre_cost_mst", "job_no", "approved");

if($action=="print_button_variable_setting")
{
	$print_report_format=0;
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=11 and report_id=18 and is_deleted=0 and status_active=1");
	echo "document.getElementById('report_ids').value = '".$print_report_format."';\n";
	echo "print_report_button_setting('".$print_report_format."');\n";
	exit();	
}

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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'style_order_wise_budget_details_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	
	$sql="select a.id, a.po_number, b.job_no_prefix_num, b.job_no, b.buyer_name, b.style_ref_no from wo_po_details_master b, wo_po_break_down a  where b.job_no=a.job_no_mst and b.company_name=$data[0] and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $buyer_name $job_no_cond ORDER BY b.job_no";
	//echo $sql;
	$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
	$arr=array(1=>$buyer);
	
	echo  create_list_view("list_view", "Job No,Buyer,Style Ref.,Order No", "110,110,150,180","610","350",0, $sql, "js_set_value", "id,po_number", "", 1, "0,buyer_name,0,0,0", $arr , "job_no_prefix_num,buyer_name,style_ref_no,po_number", "style_order_wise_budget_details_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0','') ;
	disconnect($con);
	exit(); 
}

//$tmplte=explode("**",$data);
//if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
//if ($template=="") $template=1;
$template=1;
if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$report_type=str_replace("'","",$reporttype);
	//echo $report_type;
	//echo $cbo_search_date;die;
	$company_name=str_replace("'","",$cbo_company_name);
	$file_no=trim(str_replace("'","",$txt_file_no));
	$internal_ref=trim(str_replace("'","",$txt_internal_ref));
	$season=str_replace("'","",$txt_season);
	$txt_season_id=str_replace("'","",$txt_season_id);
	if($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='$internal_ref' ";
	if($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='$file_no' ";
	//$costing_per_arr=return_library_array( "select job_no, costing_per from wo_pre_cost_mst",'job_no','costing_per');
	
		$pre_cost=sql_select("select job_no,costing_date, costing_per from wo_pre_cost_mst");
		foreach($pre_cost as $row)
		{
			$costing_date=date("m-Y", strtotime($row[csf('costing_date')]));
			$costing_per_arr[$row[csf('job_no')]]=$row[csf('costing_per')];
			$costing_per_date_arr[$row[csf('job_no')]]=$costing_date;
		}
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

	if(str_replace("'","",$cbo_team_name)>0){
		$team_cond=" and a.team_leader=$cbo_team_name";
	}
	if(str_replace("'","",$cbo_team_member)>0){
		$team_member_cond=" and a.dealing_marchant=$cbo_team_member";
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
	
	$start_month=date("Y-m",strtotime($date_from));
	$end_month=date("Y-m",strtotime($date_to));
	$date_to2=date("Y-m-d",strtotime($end_date));
	
	if($db_type==2)
	{
		$date_from=change_date_format($date_from,'yyyy-mm-dd','-',1);
		$date_to2=change_date_format($date_to2,'yyyy-mm-dd','-',1);
	}
	$total_months=datediff("m",$start_month,$end_month);

	//$last_month=date("Y-m", strtotime($end_month));
	$month_array=array();
	$st_month=$start_month;
	$month_array[]=$st_month;
	for($i=0; $i<$total_months;$i++)
	{
		$start_month=date("Y-m", strtotime("+1 Months", strtotime($start_month)));
		$month_array[]=$start_month;
	}

	$date_cond='';
	if(str_replace("'","",$cbo_search_date)==1)
	{
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
			$date_cond=" and c.ex_factory_date between '$start_date' and '$end_date'";
			$date_max_profit=" and applying_period_to_date between '$start_date' and '$end_date'";
		}
	}
	

	
		
	$job_no=str_replace("'","",$txt_job_no);
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
	if($txt_season_id=="") $season_cond=""; else $season_cond=" and a.season_buyer_wise in(".$txt_season_id.")";
	
	$order_no=str_replace("'","",$txt_order_id);
	$order_num=str_replace("'","",$txt_order_no);
	if(str_replace("'","",$txt_order_id)!="" && str_replace("'","",$txt_order_id)!=0) $order_id_cond_trans=" and b.id in ($order_no)";
	else if ($order_num=="") $order_no_cond=""; else $order_no_cond=" and  b.po_number in ('$order_num') ";
	
	
	
	if($report_type==2)//Budget
	{
		if($template==1)
		{
			
			$style1="#E9F3FF"; 
			$style="#FFFFFF";
			
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
			unset($asking_profit); 
		
			 $sql_budget="select a.id as job_id,a.job_no_prefix_num, b.insert_date, a.order_uom,a.job_no, a.buyer_name, a.style_ref_no, b.is_confirmed, a.agent_name, a.avg_unit_price, 
			a.dealing_marchant, a.gmts_item_id, a.total_set_qnty as ratio, b.plan_cut, b.id as po_id, b.po_number, b.pub_shipment_date, b.po_received_date, a.set_smv,
			b.po_quantity, b.po_total_price, b.unit_price, b.grouping, b.file_no,c.ex_factory_date
			from wo_po_details_master a, wo_po_break_down b,com_export_invoice_ship_mst c, com_export_invoice_ship_dtls d where a.job_no=b.job_no_mst and c.id=d.mst_id and d.po_breakdown_id=b.id and a.company_name='$company_name' and a.status_active=1 and
			a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond order by a.job_no";

		    //echo $sql_budget; die;
			
			$result_sql_budget=sql_select($sql_budget); 
			$tot_rows_budget=count($result_sql_budget);
			foreach($result_sql_budget as $row)
			{				 
				$po_idArr[$row[csf('po_id')]]=$row[csf('po_id')];
				$job_idArr[$row[csf('job_id')]]=$row[csf('job_id')];
			}
			$job_id_cond=where_con_using_array($job_idArr,0,'a.id');
			$job_id_cond2=where_con_using_array($job_idArr,0,'job_id');
			$job_id_cond3=where_con_using_array($job_idArr,0,'a.job_id');
			
		$sqlpo="select a.id as JOB_ID, a.job_no AS JOB_NO, b.id AS ID, c.item_number_id AS ITEM_NUMBER_ID, c.country_id AS COUNTRY_ID, c.color_number_id AS COLOR_NUMBER_ID, c.size_number_id AS SIZE_NUMBER_ID, c.order_quantity AS ORDER_QUANTITY, c.plan_cut_qnty AS PLAN_CUT_QNTY, c.country_ship_date AS COUNTRY_SHIP_DATE, c.article_number AS ARTICLE_NUMBER, d.costing_per_id AS COSTING_PER from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_dtls d where a.id=b.job_id and b.id=c.po_break_down_id  and a.id=d.job_id and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 $job_id_cond";
	//echo $sqlpo; die; //and a.job_no='$job_no'
	$sqlpoRes = sql_select($sqlpo);
	//print_r($sqlpoRes); die;
	$po_arr=array(); $poCountryArr=array(); $reqQtyAmtArr=array(); $costingPerArr=array(); $jobid="";
	foreach($sqlpoRes as $row)
	{
	$costingPerQty=0;
	if($row['COSTING_PER']==1) $costingPerQty=12;
	elseif($row['COSTING_PER']==2) $costingPerQty=1;	
	elseif($row['COSTING_PER']==3) $costingPerQty=24;
	elseif($row['COSTING_PER']==4) $costingPerQty=36;
	elseif($row['COSTING_PER']==5) $costingPerQty=48;
	else $costingPerQty=0;
	
	$costingPerArr[$row['JOB_ID']]=$costingPerQty;
	
	$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty']+=$row['ORDER_QUANTITY'];
	$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
	
	$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'].=$row['COUNTRY_ID'].',';
	
	$poCountryArr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty']+=$row['ORDER_QUANTITY'];
	$poCountryArr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
	
	$reqQtyAmtArr[$row['JOB_ID']][$row['ID']]['poqty']+=$row['ORDER_QUANTITY'];
	$reqQtyAmtArr[$row['JOB_ID']][$row['ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
	if($jobid=="") $jobid=$row['JOB_ID']; else $jobid.=','.$row['JOB_ID'];
	}
	unset($sqlpoRes);

	$gmtsitemRatioSql="select job_id AS JOB_ID, gmts_item_id AS GMTS_ITEM_ID, set_item_ratio AS SET_ITEM_RATIO from wo_po_details_mas_set_details where 1=1 $job_id_cond2";
	//echo $gmtsitemRatioSql; die;
	$gmtsitemRatioSqlRes = sql_select($gmtsitemRatioSql);
	$jobItemRatioArr=array();
	foreach($gmtsitemRatioSqlRes as $row)
	{
	$jobItemRatioArr[$row['JOB_ID']][$row['GMTS_ITEM_ID']]=$row['SET_ITEM_RATIO'];
	}
	unset($gmtsitemRatioSqlRes);
	//Trims Details
	$sqlTrim="select a.job_id AS JOB_ID, a.id AS TRIMID, a.trim_group AS TRIM_GROUP, a.description AS DESCRIPTION, a.cons_uom AS CONS_UOM, a.cons_dzn_gmts CONS_DZN_GMTS, a.rate AS RATEMST, a.amount AS AMOUNT, b.po_break_down_id as POID, b.item_number_id as ITEM_NUMBER_ID, b.color_number_id as COLOR_NUMBER_ID, b.size_number_id as SIZE_NUMBER_ID, b.cons AS CONS, b.tot_cons AS TOT_CONS, b.rate AS RATE, b.country_id AS COUNTRY_ID_TRIMS, b.color_size_table_id as COLOR_SIZE_ID
	from wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b
	where 1=1 and a.id=b.wo_pre_cost_trim_cost_dtls_id and b.cons>0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $job_id_cond3";
	//echo $sqlTrim; die;
	$sqlTrimRes = sql_select($sqlTrim);
	$tot_trim_amt=0;
	foreach($sqlTrimRes as $row)
	{
	$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$consTotQnty=$consAmt=$consTotAmt=0;
	
	$costingPer=$costingPerArr[$row['JOB_ID']];
	$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
	
	$poCountryId=array_filter(array_unique(explode(",",$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'])));
	//print_r($poCountryId);
	
	if($row['COUNTRY_ID_TRIMS']=="" || $row['COUNTRY_ID_TRIMS']==0)
	{
		$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
		$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
		
		$consQnty=($poQty/$itemRatio)*($row['CONS']/$costingPer);
		$consTotQnty=($poQty/$itemRatio)*($row['TOT_CONS']/$costingPer);
		
		$consAmt=$consQnty*$row['RATE'];
		$consTotAmt=$consTotQnty*$row['RATE'];
	}
	else
	{
		$countryIdArr=explode(",",$row['COUNTRY_ID_TRIMS']);
		$consQnty=$consTotQnty=$consAmt=$consTotAmt=0;
		foreach($poCountryId as $countryId)
		{
			if(in_array($countryId, $countryIdArr))
			{
				$poQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
				$planQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
				$consQty=$consTotQty=0;
				
				$consQty=($poQty/$itemRatio)*($row['CONS']/$costingPer);
				$consTotQty=($poQty/$itemRatio)*($row['TOT_CONS']/$costingPer);
				
				$consQnty+=$consQty;
				$consTotQnty+=$consTotQty;
				//echo $poQty.'-'.$itemRatio.'-'.$row['CONS'].'-'.$costingPer.'<br>';
				$consAmt+=$consQty*$row['RATE'];
				$consTotAmt+=$consTotQty*$row['RATE'];
			}
		}
	}
	
	//echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
	$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['trimqty']+=$consQnty;
	$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['trimtotqty']+=$consTotQnty;
	
	$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['trimamt']+=$consAmt;
	$tot_trim_amt+=$consAmt;
	$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['trimtotamt']+=$consTotAmt;
	}
	unset($sqlTrimRes); 
	//print_r($reqQtyAmtArr); die;
	//echo $tot_trim_amt.'DxD';

 

			$cbo_year=str_replace("'","",$cbo_year);
			if($db_type==0)
			{
				if($cbo_year!=0) $job_year_cond=" and year(a.insert_date)=$cbo_year"; else $job_year_cond="";	
			}
			else if($db_type==2)
			{
				$year_field_con=" and to_char(a.insert_date,'YYYY')";
				if($cbo_year!=0) $job_year_cond="$year_field_con=$cbo_year"; else $job_year_cond="";	
			}
			
			ob_start();
			
			$cTypeArr=array(2,3,4,6,32,33,44,47,48,63,71,74,76); 
			$cTypeArr2=array(5,7,45,47,48,49,54,55,56,57,58,59,60,63,67,69);
			$poIds=implode(",",$po_idArr);
			$fabDataArr=sql_select("select a.job_no,a.pre_cost_fabric_cost_dtls_id, a.po_break_down_id, b.color_type_id  from wo_pre_cos_fab_co_avg_con_dtls a,wo_pre_cost_fabric_cost_dtls b where a.pre_cost_fabric_cost_dtls_id=b.id and a.job_no=b.job_no  and b.status_active=1 and b.is_deleted=0 and a.po_break_down_id in ($poIds) and b.color_type_id in (2,3,4,6,32,33,44,47,48,63,71,74,76,5,7,45,47,48,49,54,55,56,57,58,59,60,63,67,69) group by a.job_no,a.pre_cost_fabric_cost_dtls_id, a.po_break_down_id, b.color_type_id");
			// echo "<pre>";
			// print_r($cTypeArr);
			foreach($fabDataArr as $row_pre)
			{
				$ctype=$row_pre[csf('color_type_id')];
			
				if($ctype==2 || $ctype==3 || $ctype==4 || $ctype==6 || $ctype==32 || $ctype==44 || $ctype==47 || $ctype==48 || $ctype==63 || $type==71 || $ctype==74 || $ctype==76){

					$fab_colorType_arr[$row_pre[csf('po_break_down_id')]][$row_pre[csf('color_type_id')]]=$row_pre[csf('color_type_id')];	
				}else{

					$fab_colorType_arr2[$row_pre[csf('po_break_down_id')]][$row_pre[csf('color_type_id')]]=$row_pre[csf('color_type_id')];	
				}
				
			}
			?>
            <div style="width:4570px;">
            <div style="width:900px;" align="left">
            <?
            $condition= new condition();
			 $condition->company_name("=$cbo_company_name");
			 if(str_replace("'","",$cbo_buyer_name)>0){
				  $condition->buyer_name("=$cbo_buyer_name");
			 }
			 if(str_replace("'","",$txt_job_no) !=''){
				  $condition->job_no_prefix_num("in($txt_job_no)");
			 }
			 if(str_replace("'","",$cbo_order_status) >0){
				  $condition->is_confirmed("=$cbo_order_status");
			 }
			 if(str_replace("'","",$cbo_order_status)==0){
				  $condition->is_confirmed("in(1,2)");
			 }
			 /* if(str_replace("'","",$cbo_search_date) ==1 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!=''){
				   $condition->ex_factory_date(" between '$start_date' and '$end_date'");
			 } */
			 if(str_replace("'","",$txt_order_no)!='')
			 {
				$condition->po_number("=$txt_order_no"); 
			 }
			 if(count($po_idArr)>0)
			 {
				$po_id=implode(",",$po_idArr);
				$condition->po_id_in("$po_id"); 
			 }
			 if(str_replace("'","",$job_year_cond)!='')
			 {
				$condition->job_year("$job_year_cond"); 
			 }
			 $condition->init();
			//$yarn= new yarn($condition);
			//echo $yarn->getQuery(); die;
			
           $yarn= new yarn($condition);
			$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
			$yarn= new yarn($condition);
			$yarn_req_qty_arr=$yarn->getOrderWiseYarnQtyArray();
			$yarn= new yarn($condition);
			$yarn_des_data=$yarn->getOrderCountCompositionPercentAndTypeWiseYarnQtyAndAmountArray();
		 	$conversion= new conversion($condition);
			$conversion_costing_arr=$conversion->getAmountArray_by_order();
			
			$conversion= new conversion($condition);
			$conversion_costing_arr_process=$conversion->getAmountArray_by_orderAndProcess();
			//print_r($conversion_costing_arr_process); die;

		 	//$trims= new trims($condition);
			//$trims_costing_arr=$trims->getAmountArray_by_order();
			
			$fabric= new fabric($condition);
			$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
			//	print_r($fabric_costing_arr);
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

			$knit_cost_arr=array(1,2,3,4,134,153);
			$fabric_dyeingCost_arr=array(25,26,31,32,34,38,39,60,61,62,63,74,75,76,78,79,80,81,83,84,85,86,87,88,89,90,92,129,137,138,139,141,144,145,146,147,149,158,162,163,165,169,170,171,172,173,174,176,203,232,234,238,266,267,280,281,282,283,284,285,286,287,427);
			$heat_setting_cost_arr=array(33,220,221);
			$fab_finish_cost_arr=array(65,66,67,68,69,70,71,72,73,77,91,93,94,100,125,127,128,136,143,150,151,155,156,157,159,160,161,164,166,167,168,183,184,185,186,187,189,190,191,194,195,196,197,198,199,200,201,202,204,205,206,207,208,210,211,212,219,225,226,227,228,229,230,240,242,249,250,251,253,254,255,256,257,258,259,260,261,262,263,264,265,277,278,279,288,290,291,292,293,294,406,407,398,404,399,400,401,402,405,243,403);
			//404,399,400,401,402,405,243,403
			$aop_cost_arr=array(35,36,37,40,213,214,215,216,217,218,222,223,235,236,237);			
			$washing_cost_arr=array(64,132,140,142,175,180,181,182,188,192,193,209,231,233,239,276);

			//162,163,138,221,25,26,77,290,257,165,169,276,161,71,132,237,214,38,210,251,174,265,145,4,79,216,235,223,292,70,260,261,233,249,184,250,73,66,185,129,281,75,211,212,200,40,137,89,180,239,136,181


			$summ_yarn_costing=0;$summ_order_value=$summ_commercial_cost=$summ_fab_purchase_cost=$summ_knit_cost=$summ_yarn_dyeing_cost=$summ_all_over_cost=$summ_heat_setting_cost=$summ_fabric_dyeing_cost=$summ_washing_cost=$summ_fabric_finish_cost=$summ_trim_amount=$tot_summ_emblish_cost=$tot_summ_commi_cost=$summ_test_cost=$summ_freight_cost=$summ_inspection_cost=$summ_certificate_cost=$summ_common_oh=$summ_currier_cost=$summ_cm_cost=$summ_expected_profit=$summ_expected_profit_per=$summ_expected_profit_per=$summ_wash_cost=$summ_expected_profit=0;
			$tot_depr_amor_pre_cost=$tot_deffdlc_cost=$tot_design_cost=$tot_studio_cost=$tot_interest_cost=$tot_incometax_cost=0;
			
			foreach($result_sql_budget as $row )
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
				if(str_replace("'","",$cbo_search_date)==1)
				{
					$ship_po_recv_date=change_date_format($row[csf('ex_factory_date')]);
				}
				$cost_date=change_date_format($row[csf('costing_date')],'','',1);
				$asking_profit=$asking_profit_arr[$cost_date]['asking_profit'];
				
				$approve_status=$approve_arr[$row[csf('job_no')]];
				if($approve_status==1) $is_approve="Approved"; else $is_approve="No";
				$dzn_qnty=0;
				$costing_per_id=$costing_per_arr[$row[csf('job_no')]];//$fabriccostArray[$row[csf('job_no')]]['costing_per_id'];
				if($costing_per_id==1) $dzn_qnty=12;
				else if($costing_per_id==3) $dzn_qnty=12*2;
				else if($costing_per_id==4) $dzn_qnty=12*3;
				else if($costing_per_id==5) $dzn_qnty=12*4;
				else $dzn_qnty=1;
				
				$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
				$order_qty_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
				$dzn_qnty_yarn=$dzn_qnty*$row[csf('ratio')];
				
				$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
				$summ_order_value+=$row[csf('po_total_price')];
				$plancut_value=$plan_cut_qnty*$row[csf('avg_unit_price')];
				$summ_commercial_cost+=$commercial_costing_arr[$row[csf('po_id')]];
				$summ_yarn_costing+=$yarn_costing_arr[$row[csf('po_id')]];
						
				$fab_purchase_knit=array_sum($fabric_costing_arr['knit']['grey'][$row[csf('po_id')]]);
				if(is_infinite($fab_purchase_knit) || is_nan($fab_purchase_knit)){$fab_purchase_knit=0;}
				$fab_purchase_woven=array_sum($fabric_costing_arr['woven']['grey'][$row[csf('po_id')]]);
				if(is_infinite($fab_purchase_woven) || is_nan($fab_purchase_woven)){$fab_purchase_woven=0;}
				//echo  $fab_purchase_knit.'='. $fab_purchase_woven.'<br>';
				$summ_fab_purchase_cost+=$fab_purchase_knit+$fab_purchase_woven;
				//$yarn_cons=($yarn_req_qty_arr[$row[csf('po_id')]]/$plan_cut_qnty)*$dzn_qnty;
				//if(is_infinite($yarn_cons) || is_nan($yarn_cons)){$yarn_cons=0;}
				
				$knit_cost=0;
				foreach($knit_cost_arr as $process_id)
				{
					$knit_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$process_id]);//[$row[csf('order_uom')]];	
				}
				
				$knit_cost_dzn=($knit_cost/$plan_cut_qnty)*12;
				if(is_infinite($knit_cost_dzn) || is_nan($knit_cost_dzn)){$knit_cost_dzn=0;}
				$fabric_dyeing_cost=0;
				foreach($fabric_dyeingCost_arr as $fab_process_id)
				{
					$fabric_dyeing_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$fab_process_id]);//[$row[csf('order_uom')]];	
				}
				$fabric_finish=0;
				foreach($fab_finish_cost_arr as $fin_process_id)
				{
					$fabric_finish+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$fin_process_id]);//[$row[csf('order_uom')]];	
				}
				$washing_cost=0;
				foreach($washing_cost_arr as $w_process_id)
				{
					$washing_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$w_process_id]);//[$row[csf('order_uom')]];	
				}
				$all_over_cost=0;
				foreach($aop_cost_arr as $aop_process_id)
				{
					$all_over_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$aop_process_id]);
				}
			
				
				$yarn_dyeing_cost=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][30]);
				if(is_infinite($yarn_dyeing_cost) || is_nan($yarn_dyeing_cost)){$yarn_dyeing_cost=0;}
				$heat_setting_cost=0;
				foreach ($heat_setting_cost_arr as $heatsetting_processid) {
					$heat_setting_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$heatsetting_processid]);
				}		

				if(is_infinite($heat_setting_cost) || is_nan($heat_setting_cost)){$heat_setting_cost=0;}
				$summ_heat_setting_cost+=$heat_setting_cost;
				$summ_yarn_dyeing_cost+=$yarn_dyeing_cost;
				$summ_knit_cost+=$knit_cost;
				$summ_all_over_cost+=$all_over_cost;
				$summ_fabric_dyeing_cost+=$fabric_dyeing_cost;
				//echo $washing_cost.'dd';
				$summ_washing_cost+=$washing_cost;
				//$summ_wash_cost+=$wash_cost;
				$summ_fabric_finish_cost+=$fabric_finish;
				//$summ_tot_cost_materials_service_cost+=
				//echo $row[csf('po_id')];
				$summ_trim_amount+= $reqQtyAmtArr[$row[csf('job_id')]][$row[csf('po_id')]]['trimamt'];//$trims_costing_arr[$row[csf('po_id')]];//$fabriccostArray[$row[csf('job_no')]]['trims_cost']/$dzn_qnty*$order_qty_pcs;
				$print_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][1];//($fab_emb[$row[csf('job_no')]]['print']/$dzn_qnty)*$order_qty_pcs;
				$embroidery_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][2];//($fab_emb[$row[csf('job_no')]]['embroidery']/$dzn_qnty)*$order_qty_pcs;
				$special_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][4];//($fab_emb[$row[csf('job_no')]]['special']/$dzn_qnty)*$order_qty_pcs;
				$wash_cost=$emblishment_costing_arr_name_wash[$row[csf('po_id')]][3];//($fab_emb[$row[csf('job_no')]]['wash']/$dzn_qnty)*$order_qty_pcs;
				$other_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][5];
				$tot_summ_emblish_cost+=$print_amount+$embroidery_amount+$special_amount+$wash_cost+$other_amount;
				//($fab_emb[$row[csf('job_no')]]['other']/$dzn_qnty)*$order_qty_pcs;
				$foreign=$commission_costing_arr[$row[csf('po_id')]][1];//$commission_array[$row[csf('job_no')]]['foreign']/$dzn_qnty*$order_qty_pcs;
				$local=$commission_costing_arr[$row[csf('po_id')]][2];//$commission_array[$row[csf('job_no')]]['local']/$dzn_qnty*$order_qty_pcs;
				$tot_summ_commi_cost+=$foreign+$local;
				$summ_test_cost+=$other_costing_arr[$row[csf('po_id')]]['lab_test'];//$fabriccostArray[$row[csf('job_no')]]['lab_test']/$dzn_qnty*$order_qty_pcs;
				$summ_freight_cost+= $other_costing_arr[$row[csf('po_id')]]['freight'];//$fabriccostArray[$row[csf('job_no')]]['freight']/$dzn_qnty*$order_qty_pcs;
				$summ_inspection_cost+=$other_costing_arr[$row[csf('po_id')]]['inspection'];//$fabriccostArray[$row[csf('job_no')]]['inspection']/$dzn_qnty*$order_qty_pcs;
				$summ_certificate_cost+=$other_costing_arr[$row[csf('po_id')]]['certificate_pre_cost'];//$fabriccostArray[$row[csf('job_no')]]['certificate_pre_cost']/$dzn_qnty*$order_qty_pcs;
				$summ_common_oh+=$other_costing_arr[$row[csf('po_id')]]['common_oh'];//$fabriccostArray[$row[csf('job_no')]]['common_oh']/$dzn_qnty*$order_qty_pcs;
				
				$summ_currier_cost+=$other_costing_arr[$row[csf('po_id')]]['currier_pre_cost'];//$fabriccostArray[$row[csf('job_no')]]['currier_pre_cost']/$dzn_qnty*$order_qty_pcs;
				//echo $currier_cost;
				
				$summ_cm_cost+=$other_costing_arr[$row[csf('po_id')]]['cm_cost'];
				
				$tot_depr_amor_pre_cost+=$other_costing_arr[$row[csf('po_id')]]['depr_amor_pre_cost'];
				$tot_deffdlc_cost+=$other_costing_arr[$row[csf('po_id')]]['deffdlc_cost'];
				$tot_design_cost+=$other_costing_arr[$row[csf('po_id')]]['design_cost'];
				$tot_studio_cost+=$other_costing_arr[$row[csf('po_id')]]['studio_cost'];
				$tot_interest_cost+=$other_costing_arr[$row[csf('po_id')]]['interest_cost'];
				$tot_incometax_cost+=$other_costing_arr[$row[csf('po_id')]]['incometax_cost'];
				$max_profit=$asking_profit_arr[$row[csf('company_name')]]['max_profit'];
				$company_asking=$asking_profit_arr[$row[csf('company_name')]]['asking_profit'];
				$summ_expected_profit+=$asking_profit*$row[csf('po_total_price')]/100;
				$summ_expected_profit_per+=$asking_profit;
				$summ_expect_variance+=$total_profit-$expected_profit;
			} 
			//echo $summ_expected_profit.'x';
			?>
            </div>
            <br/> 
            <?
			//ob_start();
			?>
			<div>
        	<fieldset style="width:100%;">	
            <table width="5350">
                    <tr class="form_caption">
                        <td colspan="59" align="center"><strong>Style and Order Wise Budget Details</strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="59" align="center"><strong><? echo $company_library[$company_name]; ?></strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td align="left"><strong>Details Report </strong></td>
                    </tr>
            </table>
               <? $asking_profit_head=$asking_profit_arr[$company_name]['asking_profit']; 
			   
			   		if(str_replace("'","",$cbo_search_date)==1) $caption="Invoice Exfactory Date";
			   ?>
            <table  class="rpt_table" width="5350" cellpadding="0" cellspacing="0" border="1" rules="all">
               <thead>
                    <tr>
                        <th width="40" rowspan="2">SL</th>
                        <th width="70" rowspan="2">Buyer</th>
                        <th width="70" rowspan="2">Job No</th>
                        <th width="100" rowspan="2">Order No</th>
                        <th width="100" rowspan="2">Approve Status</th>
                        <th width="110" rowspan="2">Style</th>
                        <th width="110" rowspan="2">Item Name</th>
                        <th width="70" rowspan="2">Invoice Exfactory Date</th>
                        <th width="90" rowspan="2">Order Qty</th>
                        <th width="90" rowspan="2">Avg Unit Price</th>
                        <th width="100" rowspan="2">Order Value</th>
                        <th width="100" rowspan="2">SMV</th>
                        <th colspan="14">Fabric Cost</th>
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
                        <th width="100" rowspan="2">Deffd. LC. Cost</th>
                        <th width="100" rowspan="2">Design Cost</th>
                        <th width="100" rowspan="2">Studio Cost</th>
                        <th width="100" rowspan="2">Interest Cost</th>
                        <th width="100" rowspan="2">Income Tax</th>
                        <th width="100" rowspan="2">Depc. & Amort. Tax</th>
                        <th width="120" rowspan="2">CM/DZN</th>
                        <th width="100" rowspan="2">CM Cost</th>
                        <th width="100" rowspan="2">Total Cost</th>
                        <th width="100" rowspan="2">Profit/Loss</th>
                        <th width="100" rowspan="2">Profit/Loss %</th>
                        <th width="100" rowspan="2">Expected Profit</th>
                        <th width="100" rowspan="2">Expected Profit %</th>
                        <th width="80" rowspan="2">Expt. Profit Variance</th>
                        <th width="" rowspan="2">Yarn Cons</th>
                    </tr>
                    <tr>
                        <th width="100">Avg Yarn Rate</th>
                        <th width="80">Yarn Cost</th>
                        <th width="80">Yarn Cost %</th>
                        <th width="100">Fabric Purchase</th>
                        <th width="80">Knit/ Weav Cost/Dzn</th>
                        <th width="80">Knitting/ Weav Cost</th>
                        <th width="100">Yarn Dye Cost/Dzn </th>
                        <th width="110">Yarn Dyeing Cost </th>
                        <th width="120">Fab.Dye Cost/Dzn</th>
                        <th width="100">Fabric Dyeing Cost</th>
                        <th width="90">Heat Setting</th>
                        <th width="100">Finishing Cost</th>
                        <th width="90">Washing Cost</th>
                        <th width="90">All Over Print</th>
                        <th width="80">Printing</th>
                        <th width="85">Embroidery</th>
                        <th width="80">Special Works</th>
                        <th width="80">Wash Cost</th>
                        <th width="80">Other</th>
                        <th width="120">Foreign</th>
                        <th width="120">Local</th>
                    </tr>
				</thead>
                
            </table>
            <div style="width:5370px; max-height:400px; overflow-y:scroll" id="scroll_body">
            <table class="rpt_table" width="5350" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
			<? 
			//$approve_arr=return_library_array( "select job_no, approved from wo_pre_cost_mst", "job_no", "approved");
            $i=1; $total_order_qty=0;  $total_yarn_dyeing_cost=0; $total_yarn_cost=0; $total_order_value=0;$total_purchase_cost=0; $grand_tot_trims_cost=0; $total_fabric_dyeing_cost=0; $total_knitting_cost=0; $total_heat_setting_cost=0;$total_finishing_cost=0; $total_washing_cost=0; $fabric_dyeing_cost_dzn=0; $other_cost=0;
			$all_over_print_cost=0;$total_trim_cost=0;$total_commercial_cost=0;$total_yarn_cons=0;
		
			$total_deffdlc_cost=$total_design_cost=$total_studio_cost=$total_interest_cost=$total_incometax_cost=$total_depr_amor_pre_cost=0;
			$print_report_format=return_field_value("format_id","lib_report_template","template_name ='$company_name' and module_id=2 and report_id=43 and is_deleted=0 and status_active=1");			
			$format_ids=explode(",",$print_report_format);
		
		//================================report setting=========================================================
			if($format_ids[0]==50) $r_type="preCostRpt";
			if($format_ids[0]==51)  $r_type="preCostRpt2";
			if($format_ids[0]==52)  $r_type="bomRpt";
			if($format_ids[0]==63)  $r_type="bomRpt2";
			if($format_ids[0]==156)  $r_type="accessories_details";
			if($format_ids[0]==157)  $r_type="accessories_details2";
			if($format_ids[0]==158)  $r_type="preCostRptWoven";
			if($format_ids[0]==159) $r_type="bomRptWoven";
			if($format_ids[0]==170) $r_type="preCostRpt3";
			if($format_ids[0]==171) $r_type="preCostRpt4";
			if($format_ids[0]==142) $r_type="preCostRptBpkW";
			if($format_ids[0]==192) $r_type="checkListRpt";
			if($format_ids[0]==197) $r_type="bomRpt3";
			if($format_ids[0]==211) $r_type="mo_sheet";
			if($format_ids[0]==221) $r_type="fabric_cost_detail";
			if($format_ids[0]==173) $r_type="preCostRpt5";
			if($format_ids[0]==238) $r_type="summary";
			if($format_ids[0]==215) $r_type="budget3_details";
			if($format_ids[0]==270) $r_type="preCostRpt6";
			if($format_ids[0]==581) $r_type="costsheet";
			if($format_ids[0]==730) $r_type="budgetsheet";

			foreach($result_sql_budget as $row )
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
				if(str_replace("'","",$cbo_search_date)==1)
				{
					$ship_po_recv_date=change_date_format($row[csf('ex_factory_date')]);
				}
				$cost_date=change_date_format($row[csf('costing_date')],'','',1);
				$asking_profit=$asking_profit_arr[$cost_date]['asking_profit'];
				
				$approve_status=$approve_arr[$row[csf('job_no')]];
				if($approve_status==1) $is_approve="Approved"; else $is_approve="No";
				$dzn_qnty=0;
				$costing_per_id=$costing_per_arr[$row[csf('job_no')]];//$fabriccostArray[$row[csf('job_no')]]['costing_per_id'];
				if($costing_per_id==1) $dzn_qnty=12;
				else if($costing_per_id==3) $dzn_qnty=12*2;
				else if($costing_per_id==4) $dzn_qnty=12*3;
				else if($costing_per_id==5) $dzn_qnty=12*4;
				else $dzn_qnty=1;
				
				$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
				$order_qty_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
				$dzn_qnty_yarn=$dzn_qnty*$row[csf('ratio')];
				
				$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
				$order_value=$row[csf('po_total_price')];//$row[csf('po_quantity')]*$row[csf('ratio')];
				$plancut_value=$plan_cut_qnty*$row[csf('avg_unit_price')];
				
				//$total_order_amount+=$order_value; 
				$total_plancut_amount+=$plancut_value;
				$yarn_descrip_data=$yarn_des_data[$row[csf('po_id')]];
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
										
										$yarn_description_data_arr[$count_id][$copm_one_id][$percent_one][$type_id]['qty']+=$qnty;
										$yarn_description_data_arr[$count_id][$copm_one_id][$percent_one][$type_id]['amount']+=$amount;
									}
								}
							}
						} 
				
				?>
				 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                     <td width="40"><? echo $i; ?></td>
                     <td width="70"><p><? echo $buyer_library[$row[csf('buyer_name')]]; ?></p></td>
                     <td width="70"><p><a href="##" onClick="precost_job_report('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')]; ?>','<?  echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $cost_date; ?>','<? echo $r_type; ?>');"><? echo $row[csf('job_no_prefix_num')]; ?></a></p></td>
                     <td width="100"><p><a href="##" onClick="precost_bom_pop('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')]; ?>','<?  echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $cost_date; ?>','<? echo $r_type; ?>');"><? echo $row[csf('po_number')]; ?></a></p></td>
                     <td width="100"><p><? echo $is_approve; ?></p></td>
                     <td width="110"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                     <td width="110"><div style="width:110px; word-wrap:break-word;"><? $gmts_item=''; $gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
						foreach($gmts_item_id as $item_id)
						{
							if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=", ".$garments_item[$item_id];
						}
						echo $gmts_item; ?></div></td>
                     <td width="70"><p><? echo $ship_po_recv_date; ?></p></td>
                     <td width="90" align="right"><p><? echo number_format($order_qty_pcs,2); ?></p></td>
                     <td width="90" align="right"><p><? echo number_format($row[csf('avg_unit_price')],4); ?></p></td>
                     <td width="100" align="right"><p><? echo number_format($row[csf('po_total_price')],2); ?></p></td>
                     <td width="100" align="right"><p><? echo number_format($row[csf('set_smv')],2); ?></p></td>
                     <? 
						$commercial_cost=$commercial_costing_arr[$row[csf('po_id')]];
						
						$yarn_costing=$yarn_costing_arr[$row[csf('po_id')]];
						$yarn_cost_percent=($yarn_costing/$order_value)*100;
						if(is_infinite($yarn_cost_percent) || is_nan($yarn_cost_percent)){$yarn_cost_percent=0;}
						$avg_rate=$yarn_costing/$yarn_req_qty_arr[$row[csf('po_id')]];
						if(is_infinite($avg_rate) || is_nan($avg_rate)){$avg_rate=0;}
						
						$fab_purchase_knit=array_sum($fabric_costing_arr['knit']['grey'][$row[csf('po_id')]]);
						if(is_infinite($fab_purchase_knit) || is_nan($fab_purchase_knit)){$fab_purchase_knit=0;}
						$fab_purchase_woven=array_sum($fabric_costing_arr['woven']['grey'][$row[csf('po_id')]]);
						if(is_infinite($fab_purchase_woven) || is_nan($fab_purchase_woven)){$fab_purchase_woven=0;}
						//echo  $fab_purchase_knit.'='. $fab_purchase_woven.'<br>';
						$fab_purchase=$fab_purchase_knit+$fab_purchase_woven;
						$yarn_cons=($yarn_req_qty_arr[$row[csf('po_id')]]/$plan_cut_qnty)*$dzn_qnty;
						if(is_infinite($yarn_cons) || is_nan($yarn_cons)){$yarn_cons=0;}
						
						$knit_cost=0;
						foreach($knit_cost_arr as $process_id)
						{
							$knit_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$process_id]);//[$row[csf('order_uom')]];	
						}
						$knit_cost_dzn=($knit_cost/$plan_cut_qnty)*12;
						if(is_infinite($knit_cost_dzn) || is_nan($knit_cost_dzn)){$knit_cost_dzn=0;}
						$fabric_dyeing_cost=0;
						foreach($fabric_dyeingCost_arr as $fab_process_id)
						{
							$fabric_dyeing_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$fab_process_id]);//[$row[csf('order_uom')]];	
						}
						$fabric_finish=0;
						foreach($fab_finish_cost_arr as $fin_process_id)
						{
							$fabric_finish+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$fin_process_id]);//[$row[csf('order_uom')]];	
						}
						$washing_cost=0;
						foreach($washing_cost_arr as $w_process_id)
						{
							$washing_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$w_process_id]);//[$row[csf('order_uom')]];	
						}
						$all_over_cost=0;
						foreach($aop_cost_arr as $aop_process_id)
						{
							$all_over_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$aop_process_id]);
							
							//[$row[csf('order_uom')]];	
						}
						
		
						$yarn_dyeing_cost=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][30]);
						if(is_infinite($yarn_dyeing_cost) || is_nan($yarn_dyeing_cost)){$yarn_dyeing_cost=0;}
						$yarn_dyeing_cost_dzn=($yarn_dyeing_cost/$plan_cut_qnty)*$dzn_qnty;
						if(is_infinite($yarn_dyeing_cost_dzn) || is_nan($yarn_dyeing_cost_dzn)){$yarn_dyeing_cost_dzn=0;}
						$fabric_dyeing_cost_dzn=($fabric_dyeing_cost/$plan_cut_qnty)*$dzn_qnty;
						if(is_infinite($fabric_dyeing_cost_dzn) || is_nan($fabric_dyeing_cost_dzn)){$fabric_dyeing_cost_dzn=0;}
						$heat_setting_cost=0;
						foreach ($heat_setting_cost_arr as $heatid) {
							$heat_setting_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$heatid]);
						}
						if(is_infinite($heat_setting_cost) || is_nan($heat_setting_cost)){$heat_setting_cost=0;}
						$trim_amount= $reqQtyAmtArr[$row[csf('job_id')]][$row[csf('po_id')]]['trimamt'];//$trims_costing_arr[$row[csf('po_id')]];
						$print_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][1];
						$embroidery_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][2];
						$special_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][4];
						$wash_cost=$emblishment_costing_arr_name_wash[$row[csf('po_id')]][3];
						$other_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][5];
						$foreign=$commission_costing_arr[$row[csf('po_id')]][1];
						$local=$commission_costing_arr[$row[csf('po_id')]][2];
						$test_cost=$other_costing_arr[$row[csf('po_id')]]['lab_test'];
						$freight_cost= $other_costing_arr[$row[csf('po_id')]]['freight'];//$fabriccostArray[$row[csf('job_no')]]['freight']/$dzn_qnty*$order_qty_pcs;
						$inspection=$other_costing_arr[$row[csf('po_id')]]['inspection'];//$fabriccostArray[$row[csf('job_no')]]['inspection']/$dzn_qnty*$order_qty_pcs;
						$certificate_cost=$other_costing_arr[$row[csf('po_id')]]['certificate_pre_cost'];//$fabriccostArray[$row[csf('job_no')]]['certificate_pre_cost']/$dzn_qnty*$order_qty_pcs;
						$common_oh=$other_costing_arr[$row[csf('po_id')]]['common_oh'];//$fabriccostArray[$row[csf('job_no')]]['common_oh']/$dzn_qnty*$order_qty_pcs;
						$currier_cost=$other_costing_arr[$row[csf('po_id')]]['currier_pre_cost'];//$fabriccostArray[$row[csf('job_no')]]['currier_pre_cost']/$dzn_qnty*$order_qty_pcs;
						//echo $currier_cost;
						$depr_amor_pre_cost=$other_costing_arr[$row[csf('po_id')]]['depr_amor_pre_cost'];
						$deffdlc_cost=$other_costing_arr[$row[csf('po_id')]]['deffdlc_cost'];
						$design_cost=$other_costing_arr[$row[csf('po_id')]]['design_cost'];
						$studio_cost=$other_costing_arr[$row[csf('po_id')]]['studio_cost'];
						$interest_cost=$other_costing_arr[$row[csf('po_id')]]['interest_cost'];
						$incometax_cost=$other_costing_arr[$row[csf('po_id')]]['incometax_cost'];
						
						
						$cm_cost=$other_costing_arr[$row[csf('po_id')]]['cm_cost'];
						$cm_cost_dzn=($cm_cost/$order_qty_pcs)*$dzn_qnty;
						if(is_infinite($cm_cost_dzn) || is_nan($cm_cost_dzn)){$cm_cost_dzn=0;}

						if(is_infinite($cm_cost_dzn) || is_nan($cm_cost_dzn)){$cm_cost_dzn=0;}
						//$fabriccostArray[$row[csf('job_no')]]['c_cost'];
						//$fabriccostArray[$row[csf('job_no')]]['c_cost']/$dzn_qnty*$order_qty_pcs;
						$total_cost=$yarn_costing+$fab_purchase+$knit_cost+$washing_cost+$all_over_cost+$yarn_dyeing_cost+$fabric_dyeing_cost+$heat_setting_cost+$fabric_finish+$trim_amount+$test_cost+$print_amount+$embroidery_amount+$special_amount+$other_amount+$wash_cost+$commercial_cost+$foreign+$local+$freight_cost+$inspection+$certificate_cost+$common_oh+$currier_cost+$cm_cost+$deffdlc_cost+$design_cost+$studio_cost+$interest_cost+$incometax_cost+$depr_amor_pre_cost;
						
						//echo $currier_cost.'='.$deffdlc_cost.',';
						$total_profit=$order_value-$total_cost;
						$total_profit_percentage2=$total_profit/$order_value*100; 
						$expected_profit=$asking_profit_arr[$row[csf('company_name')]]['asking_profit']*$order_value/100;
						if(is_infinite($expected_profit) || is_nan($expected_profit)){$expected_profit=0;}
						$expect_variance=$total_profit-$expected_profit;
						
						//  if($fabric_dyeing_cost<=0 && $yarn_dyeing_cost<=0) $color_fab_dyeing="red"; else $color_fab_dyeing="";	
						// if($yarn_costing<=0) $color_yarn="red"; else $color_yarn="";	
						if($fab_purchase<=0 && $yarn_costing <= 0){$color_yarn="red";} else{ $color_yarn=""; }
						if($fab_purchase<=0 && $knit_cost <=0){$color_knit="red";} else{ $color_knit="";}
						if($fab_purchase<=0 && $fabric_dyeing_cost<=0){$color_fab="red";} else{$color_fab="";}
						
						if($fab_purchase<=0 && $yarn_dyeing_cost <= 0 && count($fab_colorType_arr[$row[csf('po_id')]])>0){$color_yarn_dyeing="red";} else{ $color_yarn_dyeing=""; }
						if($fab_purchase<=0 && $all_over_cost <= 0 && count($fab_colorType_arr2[$row[csf('po_id')]])>0){$color_all_over="red";} else{ $color_all_over=""; }
						// if($knit_cost<=0) $color_knit="red"; else $color_knit="";	
						 if($fabric_finish<=0) $color_finish="red"; else $color_finish="";	
						if($commercial_cost<=0) $color_com="red"; else $color_com="";	
					 
					 ?>
                     <td width="100" align="right"><a href="##" onClick="generate_pre_cost_report('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','precost_yarn_detail')"><? echo number_format($avg_rate,2); ?></a></td>
                     <td width="80" align="right" bgcolor="<? echo $color_yarn; ?>"><?  echo number_format($yarn_costing,2);   //$yarn_costing=$yarn->getOrderWiseYarnAmount($row[csf('po_id')]);?></td>
                     <td width="80" align="right"><? echo number_format($yarn_cost_percent,2); ?></td>
                     <td width="100" align="right"><a href="##" onClick="generate_precost_fab_purchase_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $fab_source_id; ?>','fab_purchase_detail')"><? echo number_format($fab_purchase,2); ?></a></td>
                     <td width="80" align="right"><? echo number_format($knit_cost_dzn,4); ?></td>
                     <td width="80" align="right" bgcolor="<? echo $color_knit; ?>"><a href="##" onClick="generate_pre_cost_knit_popup('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $cons_process; ?>','precost_knit_detail')"><? echo number_format($knit_cost,2); ?></a></td>
                     <td width="100" align="right"><? echo number_format($yarn_dyeing_cost_dzn ,2); ?></td>
                     <td width="110" align="right" bgcolor="<? echo $color_yarn_dyeing; ?>"><? echo number_format($yarn_dyeing_cost ,2); ?></td>
                     <td width="120" align="right"><? echo number_format($fabric_dyeing_cost_dzn,2); ?></td>
                     <td width="100" align="right" bgcolor="<? echo $color_fab; ?>"><a href="##" onClick="generate_precost_fab_dyeing_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $fab_source_id; ?>','fab_dyeing_detail')"><? echo number_format($fabric_dyeing_cost,2); ?></a></td>
                     <td width="90" align="right"><? echo number_format($heat_setting_cost,2); ?></td>
                     <td width="100" align="right" ><a href="##" onClick="generate_precost_fab_finishing_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','fab_finishing_detail')"><? echo number_format($fabric_finish,2); ?></a></td>
                     <td width="90" align="right"><a href="##" onClick="generate_precost_fab_finishing_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','fab_washing_detail')"><? echo number_format($washing_cost,2); ?></a></td>
                     <td width="90" align="right" bgcolor="<? echo $color_all_over; ?>"><a href="##" onClick="generate_precost_fab_all_over_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','fab_all_over_detail')"><? echo number_format($all_over_cost,2); ?></a></td>
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
					$max_profit=$asking_profit_arr[$row[csf('company_name')]]['max_profit'];
					$company_asking=$asking_profit_arr[$row[csf('company_name')]]['asking_profit'];
					
					if($trim_amount<=0) $color_trim="red"; else $color_trim="";	
					if($cm_cost<=0) $color="red"; else $color="";
						
					/*$yarnData=explode(",",substr($yarncostArray[$row[csf('job_no')]],0,-1));
					//print_r($yarnData);
					foreach($yarnData as $yarnRow)
					{
						$yarnRow=explode("**",$yarnRow);
						$count_id=$yarnRow[0];
						$type_id=$yarnRow[1];
						$cons_qnty=$yarnRow[2];
						$amount=$yarnRow[3];
												
						$yarn_desc=$yarn_count_library[$count_id]."**".$yarn_type[$type_id];
						$req_qnty=($plan_cut_qnty/$dzn_qnty_yarn)*$cons_qnty;
						$req_amnt=($plan_cut_qnty/$dzn_qnty_yarn)*$amount;
						 
						$yarn_desc_array[$yarn_desc]['qnty']+=$req_qnty;
						$yarn_desc_array[$yarn_desc]['amnt']+=$req_amnt;
					}*/
					//var_dump($yarn_desc_array);
					?>
                     <td width="100" align="right" bgcolor="<? echo $color_trim; ?>"><a href="##" onClick="generate_precost_trim_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','trim_cost_detail')"><? echo number_format($trim_amount,2); ?></a><? //echo number_format($trim_amount,2); ?></td>
                     <td width="80" align="right"><a href="##" onClick="generate_precost_embell_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','print_cost_detail')"><? echo number_format($print_amount,2); ?></a></td>
                     <td width="85" align="right"><a href="##" onClick="generate_precost_embell_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','embroidery_cost_detail')"><? echo number_format($embroidery_amount,2); ?></a></td>
                     <td width="80" align="right"><? echo number_format($special_amount,2); ?></td>
                     <td width="80" align="right"><a href="##" onClick="generate_precost_embell_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','wash_cost_detail')"><? echo number_format($wash_cost,2); ?></a></td>
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
                     <td width="100" align="right"><? echo number_format($deffdlc_cost,2);?></td>
                     <td width="100" align="right"><? echo number_format($design_cost,2);?></td>
                     <td width="100" align="right"><? echo number_format($studio_cost,2);?></td>
                     <td width="100" align="right"><? echo number_format($interest_cost,2);?></td>
                     <td width="100" align="right"><? echo number_format($incometax_cost,2);?></td>
                     <td width="100" align="right"><? echo number_format($depr_amor_pre_cost,2);?></td>
                           
                     <td width="120" align="right" title="CM Cost/PoQty Pcs*Costing Per"><? echo number_format($cm_cost_dzn,2);?></td>
                     <td width="100" align="right" bgcolor="<? echo $color; ?>"><? echo number_format($cm_cost,2);?></td>
                     <td width="100" align="right"><? echo number_format($total_cost,2); ?></td>
                    <?
						if($total_profit_percentage2<=0 ) $color_pl="red";
						else if($total_profit_percentage2>$max_profit) $color_pl="yellow";	
						else if($total_profit_percentage2<=$max_profit) $color_pl="green";	
						else $color_pl="";	
						$expected_profit=$asking_profit*$order_value/100;
						$expected_profit_per=$asking_profit;
						$expect_variance=$total_profit-$expected_profit_per;
						
					?>
                     <td width="100" align="right" bgcolor="<? echo $color_pl; ?>"><? echo number_format($total_profit,2); ?></td>
                     <td width="100" align="right"><? echo number_format($total_profit_percentage2,2); ?></td>
                     <td width="100" align="right"><? echo number_format($expected_profit,2); ?></td>
                     <td width="100" align="right"><? echo number_format($expected_profit_per,2); ?></td>
                     <td width="80" align="right"><? echo number_format($expect_variance,2)?></td>
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
				$total_knitting_cost+=$knit_cost;
				$total_fabric_dyeing_cost+=$fabric_dyeing_cost;
				$total_heat_setting_cost+=$heat_setting_cost;
				$total_finishing_cost+=$fabric_finish;
				$total_washing_cost+=$washing_cost;
				$all_over_print_cost+=$all_over_cost;
				$total_trim_cost+=$trim_amount;
				$total_commercial_cost+=$commercial_cost;
				$total_fab_cost_amount=$total_yarn_cost+$total_purchase_cost+$total_knitting_cost+$total_yarn_dyeing_cost+$total_fabric_dyeing_cost+$total_heat_setting_cost+$total_finishing_cost+$total_washing_cost+$all_over_print_cost;
				
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
				
				$total_deffdlc_cost+=$deffdlc_cost;
				$total_design_cost+=$design_cost;
				$total_studio_cost+=$studio_cost;
				$total_interest_cost+=$interest_cost;
				$total_incometax_cost+=$incometax_cost;
				$total_depr_amor_pre_cost+=$depr_amor_pre_cost;
				
				$total_common_oh+=$common_oh;
				$total_currier_amount+=$currier_cost;
				$total_fabric_profit+=$total_profit;
				$total_expected_profit+=$expected_profit;
				$total_expt_profit_percentage+=$total_profit_percentage;
				$total_expected_variance+=$expect_variance;
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
            <table class="tbl_bottom" width="5350" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
                <tr>
                    <td width="40">&nbsp;</td>
                    <td width="70">&nbsp;</td>
                    <td width="70">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="110">&nbsp;</td>
                    <td width="110">&nbsp;</td>
                    <td width="70">Total</td>
                    <td width="90" align="right" id="value_total_order_qnty"><? echo number_format($total_order_qty,2); ?></td>
                    <td width="90">&nbsp;</td>
                    <td width="100" align="right" id="value_total_order_amount2"><? echo number_format($total_order_amount,2); ?></td>
                    <td width="100">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="80" align="right" id="value_total_yarn_cost2"><? echo number_format($total_yarn_cost,2); ?></td>
                    <td width="80" align="right" id="value_total_yarn_cost_per"><? echo number_format($total_yarn_cost_percentage,2); ?></td>
                    <td width="100" align="right" id="value_total_purchase_cost"><? echo number_format($total_purchase_cost,2); ?></td>
                    <td width="80">&nbsp;</td>
                    <td width="80" align="right" id="value_total_knitting_cost"><? echo number_format($total_knitting_cost,2); ?></td>
                    <td width="100">&nbsp;</td>
                    <td width="110" align="right" id="value_total_yarn_dyeing_cost"><? echo number_format($total_yarn_dyeing_cost,2); ?></td>
                    <td width="120">&nbsp;</td>
                    <td width="100" align="right" id="value_total_fabric_dyeing_cost4"><? echo number_format($total_fabric_dyeing_cost,2); ?></td>
                    <td width="90" align="right" id="value_total_heat_setting_cost"><? echo number_format($total_heat_setting_cost,2); ?></td>		
                    <td width="100" align="right" id="value_total_finishing_cost"><? echo number_format($total_finishing_cost,2); ?></td>
                    <td width="90" align="right" id="value_total_washing_cost"><? echo number_format($total_washing_cost,2); ?></td>
                    <td width="90" align="right" id="value_all_over_print_cost"><? echo number_format($all_over_print_cost,2); ?></td>
                    <td width="100" align="right" id="value_total_trim_cost"><? echo number_format($total_trim_cost,2); ?></td>
                    <td width="80" align="right" id="value_total_print_amount"><? echo number_format($total_print_amount,2); ?></td>
                    <td width="85" align="right" id="value_total_embroidery_amount"><? echo number_format($total_embroidery_amount,2); ?></td>
                    <td width="80" align="right" id="value_total_special_amount"><? echo number_format($total_special_amount,2); ?></td>
                    <td width="80" align="right" id="value_total_wash_cost"><? echo number_format($total_wash_cost,2); ?></td>
                    <td width="80" align="right" id="value_total_other_amount"><? echo number_format($total_other_amount,2); ?></td>
                    <td width="120" align="right" id="value_total_commercial_cost"><? echo number_format($total_commercial_cost,2); ?></td>
                    <td width="120" align="right" id="value_total_foreign_amount"><? echo number_format($total_foreign_amount,2); ?></td>
                    <td width="120" align="right" id="value_total_local_amount"><? echo number_format($total_local_amount,2); ?></td>
                    <td width="100" align="right" id="value_total_test_cost_amount"><? echo number_format($total_test_cost_amount,2); ?></td>
                    <td width="100" align="right" id="value_total_freight_amount"><? echo number_format($total_freight_amount,2); ?></td>
                    <td width="120" align="right" id="value_total_inspection_amount"><? echo number_format($total_inspection_amount,2); ?></td>
                    <td width="100" align="right" id="value_total_certificate_amount"><? echo number_format($total_certificate_amount,2); ?></td>
                    <td width="100" align="right" id="value_total_common_oh_amount"><? echo number_format($total_common_oh_amount,2); ?></td>
                    <td width="100" align="right" id="value_total_currier_amount"><? echo number_format($total_currier_amount,2); ?></td>
                    
                    <td width="100" align="right" id="value_total_deffd_amount"><? echo number_format($total_deffdlc_cost,2); ?></td>
                    <td width="100" align="right" id="value_total_design_amount"><? echo number_format($total_design_cost,2); ?></td>
                    <td width="100" align="right" id="value_total_studio_amount"><? echo number_format($total_studio_cost,2); ?></td>
                    <td width="100" align="right" id="value_total_interest_amount"><? echo number_format($total_interest_cost,2); ?></td>
                    <td width="100" align="right" id="value_total_income_amount"><? echo number_format($total_incometax_cost,2); ?></td>
                    <td width="100" align="right" id="value_total_depr_amount"><? echo number_format($total_depr_amor_pre_cost,2); ?></td>
                          
                    <td width="120">&nbsp;</td>
                    <td width="100" align="right" id="value_total_cm_amount"><? echo number_format($total_cm_cost,2); ?></td>
                    <td width="100" align="right" id="value_total_tot_cost"><? echo number_format($total_tot_cost,2); ?></td>
                    <td width="100" align="right" id="value_total_fabric_profit"><? echo number_format($total_fabric_profit,2);?></td>
                    <td width="100" align="right" id="value_total_profit_fab_percentage"><? echo number_format($total_profit_fab_percentage,2); ?></td>
                    <td width="100" align="right" id="value_total_expected_profit"><? echo number_format($total_expected_profit,2);?></td>
                    <td width="100" align="right" id="">&nbsp;<? //echo number_format($total_expected_profit,2);?></td>
                    <td width="80"align="right" id="value_total_expected_variance"><? echo number_format($total_expected_variance,2);?></td>
                    <td align="right" id="value_tot_yarn_cons"><? echo number_format($total_yarn_cons,2);?></td>
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
					$total_cost_up=number_format($total_cost_up,2,'.','');
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
                    <input type="hidden" id="graph_data" value="<? echo substr($chart_data_qnty,0,-1); ?>"/>
                </tr>
            </table>
            <br>
            <?
		}
	}


	
	
		//echo "$total_data****$filename****$report_type";
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
		//echo "$html****$filename****$report_type"; 
    exit();
}

if($action=="precost_yarn_detail")
{ 
	echo load_html_head_contents("Yarn Cost Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$order_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");
	$style_ref_arr=return_library_array( "select job_no, style_ref_no from wo_po_details_master", "job_no", "style_ref_no");				
	$gmts_item_arr=return_library_array( "select job_no, gmts_item_id from wo_po_details_master", "job_no", "gmts_item_id");
	//echo "select sum(b.plan_cut*a.total_set_qnty) as po_quantity from wo_po_break_down b,wo_po_details_master a  where a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id='$po_id'";
	$order_qty=return_field_value("sum(b.plan_cut*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id='$po_id'","po_quantity");
	$ratio_qty=return_field_value("sum(a.total_set_qnty) as ratio", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id='$po_id'","ratio");
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
		<div id="scroll_body" align="center" style="display:none">
        <table  border="1" class="rpt_table" rules="all" width="830" cellpadding="0" cellspacing="0" align="center">
             <tr> 
                <td colspan="3" align="center"><strong>Yarn Cost Details</strong></td>
            </tr>
            <tr> 
                <td width="150"><strong>Job No.:</strong>&nbsp; <? echo $job_no; ?> </td><td  width="150"><strong>Order: </strong>&nbsp; <? echo $order_arr[$po_id];  ?></td><td  width="150"><strong>Buyer:</strong> &nbsp; <? echo $buyer_library[$buyer_id]; ?></td>
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
                <th width="80">Yarn Req. Qty</th>
                <th width="70">Yarn Rate</th>
                <th width="80">Amount</th>
            </thead>
            <tbody>
            <?
            $i=1;
            $fabricArray=("select id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id,  cons_qnty, rate, amount,status_active from wo_pre_cost_fab_yarn_cost_dtls where job_no='$job_no'");
            $sql_result=sql_select($fabricArray);
            
            foreach($sql_result as $row)
            {
				if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				
				$req_qty=($row[csf('cons_qnty')]/$dzn_qnty)*$order_qty;
				//$total_amount=$req_qty*$row[csf('rate')];
				//$req_qty=$cost_per_qty*$order_qty;
				$tot_amount=$row[csf('amount')];
				$total_amount=($tot_amount/$dzn_qnty)*$order_qty;
				$tot_cons_amount=$cons_qty*$order_qty;
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
                    <td width="80"  align="right"><p><? echo number_format($total_amount,2); ?></p></td>
				</tr>
				<?
				$tot_qty+=$req_qty;
				$tot_amount_yarn+=$total_amount;
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
	//$job_no="'".$job_no."'";
	//$yarn= new yarn($job_no,'job');
	$condition= new condition();
	$condition->job_no("='$job_no'");
	
	$condition->init();
	$yarn= new yarn($condition);
	//echo $yarn->getQuery(); die;
	$popUpDataArray=$yarn->getOrderCountCompositionColorTypeAndConsumptionWiseYarnDataArray();
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
}// pre cost Yarn AVG end

if($action=="pricost_yarnavg_detail")
{
	echo load_html_head_contents("Yarn Cost Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$order_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");
	$order_qty=return_field_value("sum(b.po_quantity*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id='$po_id'","po_quantity");
	$ratio_qty=return_field_value("sum(a.total_set_qnty) as ratio", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id='$po_id'","ratio");
	//print($order_qty);die;
	// $costing_per=return_field_value("costing_per as costing_per", "wo_pre_cost_mst", "job_no='$job_no'","costing_per");
	
	$price_costDataArray=sql_select("select  id,costing_per  from wo_price_quotation where status_active=1 and is_deleted=0 and id='$quotation_id' ");
	$price_costing_perArray=array();
	foreach($price_costDataArray as $pri_fabRow)
	{
		$price_costing_perArray[$pri_fabRow[csf('id')]]['costing_per']=$pri_fabRow[csf('costing_per')];
	}
	$costing_per_price=$price_costing_perArray[$quotation_id]['costing_per'];
	//echo $costing_per_price;
	if($costing_per_price==1)
	{
		$dzn_qnty=12;
		$costing_per_dzn="1 Dzn";
	}
	else if($costing_per_price==2)
	{
		$costing_per_dzn="1 Pcs";
		$dzn_qnty=12;
	}
	else if($costing_per_price==3)
	{
		$dzn_qnty=12*2;
		$costing_per_dzn="2 Dzn";
	}
	else if($costing_per_price==4)
	{
		$dzn_qnty=12*3;
		$costing_per_dzn="3 Dzn";
	}
	else if($costing_per_price==5)
	{
		$dzn_qnty=12*4;
		$costing_per_dzn="4 Dzn";
	}
	else
	{
		$dzn_qnty=1;
	} 
	$dzn_qnty=$dzn_qnty*$ratio_qty;
	$costing_per=$fabriccostArray[0][csf('costing_per_id')];
	?>
	<fieldset style="width:830px; margin-left:3px">
        <div id="scroll_body" align="center">
        <table  border="1" class="rpt_table" rules="all" width="830" cellpadding="0" cellspacing="0" align="center">
            <tr> 
            	<td colspan="3" align="center"><strong>Yarn Cost Details</strong></td>
            </tr>
            <tr> 
            	<td width="150"><strong>Job No.:</strong>&nbsp; <? echo $job_no; ?> </td><td  width="150"><strong>Order: </strong>&nbsp; <? echo $order_arr[$po_id];  ?></td><td  width="150"><strong>Buyer:</strong> &nbsp; <? echo $buyer_library[$buyer_id]; ?></td>
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
                <th width="80">Yarn Req. Qty</th>
                <th width="70">Yarn Rate</th>
                <th width="80">Amount</th>
            </thead>
            <tbody>
            <?
            $i=1;
            $fabricArray=("select id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id,  cons_qnty, rate, amount from wo_pri_quo_fab_yarn_cost_dtls where quotation_id='$quotation_id'");
            $sql_result=sql_select($fabricArray);
            
            foreach($sql_result as $row)
            {
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				
				$req_qty=($row[csf('cons_qnty')]/$dzn_qnty)*$order_qty;
				//$total_amount=$req_qty*$row[csf('rate')];
				//$req_qty=$cost_per_qty*$order_qty;
				$tot_amount=$row[csf('amount')];
				$total_amount=($tot_amount/$dzn_qnty)*$order_qty;
				$tot_cons_amount=$cons_qty*$order_qty;
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
                    <td width="80"  align="right"><p><? echo number_format($total_amount,2); ?></p></td>
				</tr>
				<?
				$tot_qty+=$req_qty;
				$tot_amount_yarn+=$total_amount;
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
                    <td colspan="10" align="right">Avg.Yarn Rate</td>
                    <td colspan="1" align="right"> <? echo number_format($tot_amount_yarn/$tot_qty,2); ?></td>
                    <td align="right"></td>
                </tr>
            </tfoot>
        </table>
        </div>
	</fieldset>
	<?
	exit();
}

if($action=="fab_purchase_detail")
{
	echo load_html_head_contents("Purchase Cost Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $job_no;
	$order_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");
	$order_qty=return_field_value("sum(b.plan_cut*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","po_quantity");
	$ratio_qty=return_field_value("sum(a.total_set_qnty) as ratio", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id='$po_id'","ratio");
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
              $data_array=("select  a.id,a.body_part_id, a.fab_nature_id,a.fabric_source, a.fabric_description,  a.fabric_source, avg(a.rate) as rate, sum(a.amount) as amount,sum(a.avg_finish_cons) as avg_finish_cons,avg(b.cons) as cons,avg(b.requirment) as avg_cons,b.po_break_down_id as po_id   from wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b where b.pre_cost_fabric_cost_dtls_id=a.id and a.job_no='$job_no' and a.job_no=b.job_no and b.po_break_down_id='$po_id'   and a.status_active=1 and  a.is_deleted=0 and a.fabric_source=2 group by a.id, a.job_no, a.item_number_id, a.body_part_id, a.fab_nature_id,a.consumption_basis,a.fabric_source, a.fabric_description, a.fabric_source,b.po_break_down_id order by a.fab_nature_id");
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
						
					 $fab_purchase_knit_qty=array_sum($fabric_costing_qty_arr['knit']['grey'][$row[csf('po_id')]][$row[csf('id')]])+array_sum($fabric_costing_qty_arr['woven']['grey'][$row[csf('po_id')]][$row[csf('id')]]);
					 $fab_purchase_knit_amount=array_sum($fabric_costing_amount_arr['knit']['grey'][$row[csf('po_id')]][$row[csf('id')]]);
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
                        <td width="70" align="right"><p><? echo number_format($fab_purchase_amount/$fab_purchase_knit_qty,4); ?></p></td>
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
                $data_array_woven=("select  a.id,a.body_part_id, a.fab_nature_id,a.fabric_source, a.fabric_description,  a.fabric_source, avg(a.rate) as rate, sum(a.amount) as amount,sum(a.avg_finish_cons) as avg_finish_cons,avg(b.cons) as cons,avg(b.requirment) as avg_cons   from wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b where b.pre_cost_fabric_cost_dtls_id=a.id and a.job_no='$job_no' and a.job_no=b.job_no and b.po_break_down_id='$po_id'   and a.status_active=1 and  a.is_deleted=0 and a.fabric_source=2 and a.fab_nature_id=3 group by a.id, a.job_no, a.item_number_id, a.body_part_id, a.fab_nature_id,a.consumption_basis,a.fabric_source, a.fabric_description, a.fabric_source");
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
if($action=="country_fab_purchase_detail")
{
	echo load_html_head_contents("Purchase Cost Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $job_no;die;
	$order_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");
	$order_qty=return_field_value("sum(b.plan_cut*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","po_quantity");
	$ratio_qty=return_field_value("sum(a.total_set_qnty) as ratio", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id='$po_id'","ratio");
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
				$fabric_costing_qty_arr=$fabric->getQtyArray_by_OrderCountryAndFabriccostid_knitAndwoven_greyAndfinish();
				$fabric= new fabric($condition);
				$fabric_costing_amount_arr=$fabric->getAmountArray_by_OrderCountryAndFabriccostid_knitAndwoven_greyAndfinish();
				//print_r($fabric_costing_amount_arr);
			
				//and a.fabric_source in(2) and a.fab_nature_id=2 item_number_id
                $i=1;
              $data_array=("select  a.id,a.body_part_id, a.fab_nature_id,a.fabric_source, a.fabric_description,  a.fabric_source, avg(a.rate) as rate, sum(a.amount) as amount,sum(a.avg_finish_cons) as avg_finish_cons,avg(b.cons) as cons,avg(b.requirment) as avg_cons,c.po_break_down_id as po_id ,c.country_id  from wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b,wo_po_color_size_breakdown c where c.po_break_down_id=b.po_break_down_id  and b.color_size_table_id=c.id and b.pre_cost_fabric_cost_dtls_id=a.id and a.job_no='$job_no' and a.job_no=b.job_no and b.po_break_down_id='$po_id' and a.status_active=1 and  a.is_deleted=0 group by a.id, a.job_no, a.item_number_id, a.body_part_id, a.fab_nature_id,a.consumption_basis,a.fabric_source, a.fabric_description, a.fabric_source,c.po_break_down_id,c.country_id  order by a.fab_nature_id");
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
					$fab_purchase_knit=array_sum($fabric_costing_amount_arr['knit']['grey'][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('id')]]);
					$fab_purchase_woven=array_sum($fabric_costing_amount_arr['woven']['grey'][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('id')]]);
					
					$fab_purchase_knit_qty=array_sum($fabric_costing_qty_arr['knit']['grey'][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('id')]]);
					$fab_purchase_woven_qty=array_sum($fabric_costing_qty_arr['woven']['grey'][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('id')]]);
							//echo $row[csf('country_id')].',';
					// $fab_purchase_knit=array_sum($fabric_costing_qty_arr['knit']['grey'][$row[csf('po_id')]][$row[csf('country_id')]])+array_sum($fabric_costing_qty_arr['woven']['grey'][$row[csf('po_id')]][$row[csf('country_id')]]);
					// $fab_purchase_knit_amount=array_sum($fabric_costing_amount_arr['knit']['grey'][$row[csf('po_id')]][$row[csf('country_id')]]);
					// $fab_purchase_woven_amount=array_sum($fabric_costing_amount_arr['woven']['grey'][$row[csf('po_id')]][$row[csf('country_id')]]);
				//	$fab_purchase_woven=$fabric_costing_arr['woven']['grey'][$po_id];
					$fab_purchase_amount=$fab_purchase_knit+$fab_purchase_woven;
					$fab_purchase_qty=$fab_purchase_knit_qty+$fab_purchase_knit_qty;
					?>
                   
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="30"><p><? echo $i; ?></p></td>
                        <td width="70" align="center"><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
                        <td width="80" align="center"><p><? echo $item_category[$row[csf('fab_nature_id')]]; ?></p></td>
                        <td width="100" align="center"><p><? echo $row[csf('fabric_description')]; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($order_qty,2); ?></p></td>
                        <td width="50" align="center"><p><? if($row[csf('fabric_source')]==2) echo "Purchase"; else echo ""; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format(($order_qty/$fab_purchase_qty)*12,2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($fab_purchase_qty,2); ?></p></td>
                        <td width="70" align="right"><p><? echo number_format($fab_purchase_amount/$fab_purchase_qty,4); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($fab_purchase_amount,2); ?></p></td>
					</tr>
					<?
					$tot_qty_knit+=$fab_purchase_qty;
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
                $data_array_woven=("select  a.id,a.body_part_id, a.fab_nature_id,a.fabric_source, a.fabric_description,  a.fabric_source, avg(a.rate) as rate, sum(a.amount) as amount,sum(a.avg_finish_cons) as avg_finish_cons,avg(b.cons) as cons,avg(b.requirment) as avg_cons   from wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b where b.pre_cost_fabric_cost_dtls_id=a.id and a.job_no='$job_no' and a.job_no=b.job_no and b.po_break_down_id='$po_id'   and a.status_active=1 and  a.is_deleted=0 and a.fabric_source=2 and a.fab_nature_id=3 group by a.id, a.job_no, a.item_number_id, a.body_part_id, a.fab_nature_id,a.consumption_basis,a.fabric_source, a.fabric_description, a.fabric_source");
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
if($action=="fab_price_purchase_detail")
{
	echo load_html_head_contents("Purchase Cost Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $job_no;
	$order_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");
	$order_qty=return_field_value("sum(b.po_quantity*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","po_quantity");
	$ratio_qty=return_field_value("sum(a.total_set_qnty) as ratio", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","ratio");
	//print($order_qty);die;
	$price_costDataArray=sql_select("select  id,costing_per  from wo_price_quotation where status_active=1 and is_deleted=0  ");
	$price_costing_perArray=array();
	foreach($price_costDataArray as $pri_fabRow)
	{
		$price_costing_perArray[$pri_fabRow[csf('id')]]['costing_per']=$pri_fabRow[csf('costing_per')];
	}
	$costing_per_price=$price_costing_perArray[$quotation_id]['costing_per'];
	//echo $costing_per_price;
	if($costing_per_price==1)
	{
		$dzn_qnty=12;
		$costing_per_dzn="1 Dzn";
	}
	else if($costing_per_price==2)
	{
		$dzn_qnty=1;
		$costing_per_dzn="1 Pcs";
	}
	else if($costing_per_price==3)
	{
		$dzn_qnty=12*2;
		$costing_per_dzn="2 Dzn";
	}
	else if($costing_per_price==4)
	{
		$dzn_qnty=12*3;
		$costing_per_dzn="3 Dzn";
	}
	else if($costing_per_price==5)
	{
		$dzn_qnty=12*4;
		$costing_per_dzn="4 Dzn";
	}
	else
	{
		$dzn_qnty=1;
	} 
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
                <?
                $i=1;
                $data_array=("select  a.body_part_id, a.fab_nature_id,a.fabric_source, a.fabric_description,  a.fabric_source, a.rate, a.amount,a.avg_finish_cons,b.cons,sum(b.requirment) as avg_cons   from wo_pri_quo_fabric_cost_dtls a,wo_pri_quo_fab_co_avg_con_dtls b where b.wo_pri_quo_fab_co_dtls_id=a.id and a.quotation_id='$quotation_id' and a.quotation_id=b.quotation_id   and a.status_active=1 and  a.is_deleted=0 group by a.id,a.quotation_id, a.item_number_id, a.body_part_id, a.fab_nature_id,a.fabric_source, a.fabric_description, a.fabric_source, a.rate, a.amount,a.avg_finish_cons,b.cons");//and a.fabric_source=2
                $sql_result=sql_select($data_array);
                foreach($sql_result as $row)
                {
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					$tot_avg=number_format($row[csf('avg_cons')],4);
					//echo $tot_avg;
					$req_qty_avg=($tot_avg/$dzn_qnty)*$order_qty;
					//echo $req_qty_avg.'='.$row[csf('rate')];
					$req_qty_p=$req_qty_avg/$dzn_qnty*$order_qty;
					$total_amount=$req_qty_avg*$row[csf('rate')];
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="30"><p><? echo $i; ?></p></td>
                        <td width="70" align="center"><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
                        <td width="80" align="center"><p><? echo $item_category[$row[csf('fab_nature_id')]]; ?></p></td>
                        <td width="100" align="center"><p><? echo $row[csf('fabric_description')]; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($order_qty,2); ?></p></td>
                        <td width="50" align="center"><p><? if($row[csf('fabric_source')]==2) echo "Purchase"; else echo ""; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($row[csf('avg_cons')],2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($req_qty_avg,2); ?></p></td>
                        <td width="70" align="right"><p><? echo number_format($row[csf('rate')],2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($total_amount,2); ?></p></td>
					</tr>
					<?
					$tot_qty+=$req_qty_avg;
					$tot_amount+=$total_amount;
					$i++;
                }
                ?>
                </tbody>
                <tfoot>
                    <tr class="tbl_bottom">
                        <td colspan="7" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty,2); ?>&nbsp;</td>
                        <td>&nbsp; </td>
                        <td align="right"><? echo number_format($tot_amount,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>
	</fieldset>
	<?
	exit();
}

if($action=="precost_knit_detail")
{
	echo load_html_head_contents("Knitting Cost Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $job_no;
	$order_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");
	$order_qty=return_field_value("sum(b.plan_cut*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","po_quantity");
	//print($order_qty);die;
	$ratio_qty=return_field_value("sum(a.total_set_qnty) as ratio", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","ratio");
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
	//echo  $dzn_qnty;
	$costing_per=$fabriccostArray[0][csf('costing_per_id')];
	?>
	<fieldset style="width:630px; margin-left:3px">
        <div id="scroll_body" align="center">
            <table  border="1" class="rpt_table" rules="all" width="600" cellpadding="0" cellspacing="0" align="center">
                <tr> 
                	<td colspan="3" align="center"><strong> Knitting Cost Details</strong></td>
                </tr>
                <tr> 
                	<td width="150"><strong>Job No.:</strong>&nbsp; <? echo $job_no; ?> </td><td  width="150"><strong>Order: </strong>&nbsp; <? echo $order_arr[$po_id];  ?></td><td  width="150"><strong>Buyer:</strong> &nbsp; <? echo $buyer_library[$buyer_id]; ?></td>
                </tr>
            </table>
            <table border="1" class="rpt_table" rules="all" width="600" cellpadding="0" cellspacing="0" align="center">
                <thead>
                    <th width="30">Sl</th>
                    <th width="70">Process</th>
                    <th width="80">GMTS Qty.</th>
                    <th width="100">Cons Qty./<? echo $costing_per_dzn; ?></th>
                    <th width="80">Req. Qty.</th>
                    <th width="50">Cost/Per Unit</th>
                    <th width="80">Amount</th>
                </thead>
                <tbody>
                <?
                $i=1;
                $data_array=("select cons_process, avg_req_qnty, req_qnty as req_qnty, charge_unit as charge_unit, amount as amount from wo_pre_cost_fab_conv_cost_dtls where job_no='$job_no' and cons_process in(1,2,3,4,134,153) and status_active=1 and is_deleted=0");
                $sql_result=sql_select($data_array);
                foreach($sql_result as $row)
                {
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					$cons_qty=($row[csf('req_qnty')]/$dzn_qnty)*$order_qty;
					$tot_cons_amount=$cons_qty*$order_qty;
					
					$tot_amount=$row[csf('amount')];
					$total_amount=($tot_amount/$dzn_qnty)*$order_qty;
					$tot_cons_amount=$cons_qty*$order_qty;
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="30"><p><? echo $i; ?></p></td>
                        <td width="70" align="center"><p><? echo $conversion_cost_head_array[$row[csf('cons_process')]]; ?></p></td>
                        <td width="80" align="center"><p><? echo number_format($order_qty,2); ?></p></td>
                        <td width="100" align="center"><p><? echo number_format($row[csf('avg_req_qnty')],4); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($cons_qty,2); ?></p></td>
                        <td width="50" align="right"><p><? echo number_format($row[csf('charge_unit')],2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($total_amount,2); ?></p></td>
					</tr>
					<?
					$tot_req_qty+=$cons_qty;
					$tot_amount_knit+=$total_amount; 
					$i++;
                }
                ?>
                </tbody>
                <tfoot>
                    <tr class="tbl_bottom">
                        <td colspan="4" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_req_qty,2); ?>&nbsp;</td>
                        <td>&nbsp; </td>
                        <td align="right"><? echo number_format($tot_amount_knit,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>
	</fieldset>
	<?
	exit();
}//Pre Cost Knit cost End

if($action=="pricost_knit_detail")
{
	echo load_html_head_contents("Knitting Cost Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $job_no;
	$order_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");
	$order_qty=return_field_value("sum(b.po_quantity*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","po_quantity");
	$ratio_qty=return_field_value("sum(a.total_set_qnty) as ratio", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","ratio");
	//print($order_qty);die;
	$price_costDataArray=sql_select("select  id,costing_per  from wo_price_quotation where status_active=1 and is_deleted=0  ");
	$price_costing_perArray=array();
	foreach($price_costDataArray as $pri_fabRow)
	{
		$price_costing_perArray[$pri_fabRow[csf('id')]]['costing_per']=$pri_fabRow[csf('costing_per')];
	}
	$costing_per_price=$price_costing_perArray[$quotation_id]['costing_per'];
	//echo $costing_per_price;
	if($costing_per_price==1)
	{
		$dzn_qnty=12;
		$costing_per_dzn="1 Dzn";
	}
	else if($costing_per_price==2)
	{
		$costing_per_dzn="1 Pcs";
	}
	else if($costing_per_price==3)
	{
		$dzn_qnty=12*2;
		$costing_per_dzn="2 Dzn";
	}
	else if($costing_per_price==4)
	{
		$dzn_qnty=12*3;
		$costing_per_dzn="3 Dzn";
	}
	else if($costing_per_price==5)
	{
		$dzn_qnty=12*4;
		$costing_per_dzn="4 Dzn";
	}
	else
	{
		$dzn_qnty=1;
	} 
	$dzn_qnty=$dzn_qnty*$ratio_qty;
	?>
	<fieldset style="width:630px; margin-left:3px">
        <div id="scroll_body" align="center">
            <table  border="1" class="rpt_table" rules="all" width="600" cellpadding="0" cellspacing="0" align="center">
                <tr> 
                	<td colspan="3" align="center"><strong> Knitting Cost Details</strong></td>
                </tr>
                <tr> 
                	<td width="150"><strong>Job No.:</strong>&nbsp; <? echo $job_no; ?> </td><td  width="150"><strong>Order: </strong>&nbsp; <? echo $order_arr[$po_id];  ?></td><td  width="150"><strong>Buyer:</strong> &nbsp; <? echo $buyer_library[$buyer_id]; ?></td>
                </tr>
            </table>
            <table border="1" class="rpt_table" rules="all" width="600" cellpadding="0" cellspacing="0" align="center">
                <thead>
                    <th width="30">Sl</th>
                    <th width="70">Process</th>
                    <th width="80">GMTS Qty.</th>
                    <th width="100">Cons Qty./<? echo $costing_per_dzn; ?></th>
                    <th width="80">Req. Qty.</th>
                    <th width="50">Cost/Per Unit</th>
                    <th width="80">Amount</th>
                </thead>
                <tbody>
                <?
                $i=1;
                $data_array=("select cons_type, req_qnty as req_qnty,charge_unit as charge_unit,amount as amount from wo_pri_quo_fab_conv_cost_dtls where quotation_id='$quotation_id' and cons_type in(1,2,3,4) and status_active=1 and is_deleted=0 group by id,cons_type,req_qnty,charge_unit,amount");
                $sql_result=sql_select($data_array);
                foreach($sql_result as $row)
                {
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					$cons_qty=($row[csf('req_qnty')]/$dzn_qnty)*$order_qty;
					$tot_cons_amount=$cons_qty*$order_qty;
					
					$tot_amount=$row[csf('amount')];
					$total_amount=($tot_amount/$dzn_qnty)*$order_qty;
					$tot_cons_amount=$cons_qty*$order_qty;
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="30"><p><? echo $i; ?></p></td>
                        <td width="70" align="center"><p><? echo $conversion_cost_head_array[$row[csf('cons_type')]]; ?></p></td>
                        <td width="80" align="center"><p><? echo number_format($order_qty,2); ?></p></td>
                        <td width="100" align="center"><p><? echo number_format($row[csf('req_qnty')],4); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($cons_qty,2); ?></p></td>
                        <td width="50" align="right"><p><? echo number_format($row[csf('charge_unit')],2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($total_amount,2); ?></p></td>
					</tr>
					<?
					$tot_req_qty+=$cons_qty;
					$tot_amount_knit+=$total_amount; 
					$i++;
                }
                ?>
                </tbody>
                <tfoot>
                    <tr class="tbl_bottom">
                        <td colspan="4" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_req_qty,2); ?>&nbsp;</td>
                        <td>&nbsp; </td>
                        <td align="right"><? echo number_format($tot_amount_knit,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>
	</fieldset>
	<?
	exit();
}

if($action=="fab_dyeing_detail")
{
	echo load_html_head_contents("Fabrics Dyeing Cost Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $job_no;die;
	$order_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");
	$order_qty=return_field_value("sum(b.plan_cut*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","po_quantity");
	$ratio_qty=return_field_value("sum(a.total_set_qnty) as ratio", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","ratio");
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
	$dzn_qnty= $dzn_qnty*$ratio_qty;
	$costing_per=$fabriccostArray[0][csf('costing_per_id')];
	?>
	<fieldset style="width:670px; margin-left:3px">
        <div id="scroll_body" align="center">
            <table  border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
                <tr> 
                	<td colspan="3" align="center"><strong> Fabric Dyeing Cost Details</strong></td>
                </tr>
                <tr> 
                	<td width="150"><strong>Job No.</strong>&nbsp; <? echo $job_no; ?> </td><td  width="150"><strong>Order: </strong>&nbsp; <? echo $order_arr[$po_id];  ?></td><td  width="150"><strong>Buyer:</strong> &nbsp; <? echo $buyer_library[$buyer_id]; ?></td>
                </tr>
            </table>
            <table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
                <thead>
                    <th width="30">Sl</th>
                    <th width="70">Process</th>
                    <th width="80">GMTS Qty.</th>
                    <th width="100">Cons Qty./<? echo $costing_per_dzn; ?></th>
                    <th width="80">Req.Qty.</th>
                    <th width="50">Cost/Per Unit</th>
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
				$conversion= new conversion($condition);
					//echo $conversion->getQuery(); die;
				$conversion_costing_arr_process=$conversion->getAmountArray_by_orderAndProcess();
				$conversion_qty_arr_process=$conversion->getQtyArray_by_orderAndProcess();
				//print_r($conversion_qty_arr_process);
				$fabric_dyeingCost_arr=array(25,26,31,32,34,38,39,60,61,62,63,64,74,75,76,77,78,79,80,81,83,84,85,86,87,88,89,90,92,129,137,138,139,141,144,145,146,147,149,158,162,163,165,169,170,171,172,173,174,176,203,232,234,238,266,267,280,281,282,283,284,285,286,287,427);
                $i=1;
               $data_array=("select  a.cons_process, avg(a.avg_req_qnty) as avg_req_qnty, sum(a.req_qnty) as req_qnty, avg(a.charge_unit) as charge_unit,
 sum(a.amount) as amount,b.id as po_id from wo_pre_cost_fab_conv_cost_dtls a,wo_po_break_down b where a.job_no='$job_no' and b.id in($po_id) and a.job_no=b.job_no_mst and a.cons_process in(25,26,31,32,34,38,39,60,61,62,63,64,74,75,76,77,78,79,80,81,83,84,85,86,87,88,89,90,92,129,137,138,139,141,144,145,146,147,149,158,162,163,165,169,170,171,172,173,174,176,203,232,234,238,266,267,280,281,282,283,284,285,286,287,427) and a.status_active=1 and a.is_deleted=0 group by  b.id,a.cons_process");
                $sql_result=sql_select($data_array);
                
                foreach($sql_result as $row)
                {
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					
					$cons_qty=($row[csf('req_qnty')]/$dzn_qnty)*$order_qty;
					$tot_cons_amount=$cons_qty*$order_qty;
					
					$tot_amount=$row[csf('amount')];
					$total_amount=($tot_amount/$dzn_qnty)*$order_qty;
					$cons_process=$row[csf('cons_process')];
					
					//$fabric_dyeing_cost=$fabric_req_qty=0;
						foreach($fabric_dyeingCost_arr as $fab_process_id)
						{
							//$fabric_dyeing_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$row[csf('cons_process')]]);//[$row[csf('order_uom')]];
							//$fabric_req_qty+=array_sum($conversion_qty_arr_process[$row[csf('po_id')]][$row[csf('cons_process')]]);	
						}
						//$fabric_dyeing_cost=array_sum($conversion_costing_arr_process[$po_id][$cons_process]);
						
							$fabric_dyeing_cost=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$row[csf('cons_process')]]);//[$row[csf('order_uom')]];
							$fabric_req_qty=array_sum($conversion_qty_arr_process[$row[csf('po_id')]][$row[csf('cons_process')]]);	
					//$tot_cons_amount=$cons_qty*$order_qty;
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="30"><p><? echo $i; ?></p></td>
						<td width="70" align="center"><p><? echo $conversion_cost_head_array[$row[csf('cons_process')]]; ?></p></td>
						<td width="80" align="right"><p><? echo number_format($order_qty,2); ?></p></td>
						<td width="100" align="right"><p><? echo number_format($row[csf('avg_req_qnty')],4); ?></p></td>
						<td width="80" align="right"><p><? echo number_format($fabric_req_qty,2); ?></p></td>
						<td width="50" align="right"><p><? echo number_format($fabric_dyeing_cost/$fabric_req_qty,2); ?></p></td>
						<td width="80" align="right"><p><? echo number_format($fabric_dyeing_cost,2); ?></p></td>
					</tr>
					<?
					$tot_req_qty+=$fabric_req_qty;
					$tot_amount_fab_dyeing+=$fabric_dyeing_cost;
					$i++;
                }
                ?>
                </tbody>
                <tfoot>
                    <tr class="tbl_bottom">
                        <td colspan="4" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_req_qty,2); ?>&nbsp;</td>
                        <td>&nbsp; </td>
                        <td align="right"><? echo number_format($tot_amount_fab_dyeing,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>
	</fieldset>
	<?
	exit();
}//Pre cost Fab dyeing cost details End

if($action=="fab_price_dyeing_detail")
{
	echo load_html_head_contents("Fabrics Dyeing Cost Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $job_no;die;
	$order_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");
	$order_qty=return_field_value("sum(b.po_quantity*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","po_quantity");
	$ratio_qty=return_field_value("sum(a.total_set_qnty) as ratio", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","ratio");
	//print($order_qty);die;
	$price_costDataArray=sql_select("select  id,costing_per  from wo_price_quotation where status_active=1 and is_deleted=0  ");
	$price_costing_perArray=array();
	foreach($price_costDataArray as $pri_fabRow)
	{
		$price_costing_perArray[$pri_fabRow[csf('id')]]['costing_per']=$pri_fabRow[csf('costing_per')];
	}
	$costing_per_price=$price_costing_perArray[$quotation_id]['costing_per'];
	//echo $costing_per_price;
	if($costing_per_price==1)
	{
		$dzn_qnty=12;
		$costing_per_dzn="1 Dzn";
	}
	else if($costing_per_price==2)
	{
		$costing_per_dzn="1 Pcs";
	}
	else if($costing_per_price==3)
	{
		$dzn_qnty=12*2;
		$costing_per_dzn="2 Dzn";
	}
	else if($costing_per_price==4)
	{
		$dzn_qnty=12*3;
		$costing_per_dzn="3 Dzn";
	}
	else if($costing_per_price==5)
	{
		$dzn_qnty=12*4;
		$costing_per_dzn="4 Dzn";
	}
	else
	{
		$dzn_qnty=1;
	} 
	$dzn_qnty= $dzn_qnty*$ratio_qty;
	?>
	<fieldset style="width:670px; margin-left:3px">
	<div id="scroll_body" align="center">
        <table  border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
            <tr> 
            <td colspan="3" align="center"><strong> Fabric Dyeing Cost Details</strong></td>
            </tr>
            <tr> 
            <td width="150"><strong>Job No.</strong>&nbsp; <? echo $job_no; ?> </td><td  width="150"><strong>Order: </strong>&nbsp; <? echo $order_arr[$po_id];  ?></td><td  width="150"><strong>Buyer:</strong> &nbsp; <? echo $buyer_library[$buyer_id]; ?></td>
            </tr>
        </table>
        <table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
            <thead>
                <th width="30">Sl</th>
                <th width="70">Process</th>
                <th width="80">GMTS Qty.</th>
                <th width="100">Cons Qty./<? echo $costing_per_dzn; ?></th>
                <th width="80">Req.Qty.</th>
                <th width="50">Cost/Per Unit</th>
                <th width="80">Amount</th>
            </thead>
            <tbody>
            <?
            $i=1;
            $data_array=("select cons_type, req_qnty as req_qnty, charge_unit as charge_unit, (amount) as amount from  wo_pri_quo_fab_conv_cost_dtls where quotation_id='$quotation_id' and cons_type in(25,31,26,32,60,61,62,63,72,80,81,84,85,86,87,38,39,74,78,79,101,133,137,138,139,146,147,149) and status_active=1 and is_deleted=0 
            ");
            $sql_result=sql_select($data_array);
            
            foreach($sql_result as $row)
            {
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				
				$cons_qty=($row[csf('req_qnty')]/$dzn_qnty)*$order_qty;
				$tot_cons_amount=$cons_qty*$order_qty;
				
				$tot_amount=$row[csf('amount')];
				$total_amount=($tot_amount/$dzn_qnty)*$order_qty;
				//$tot_cons_amount=$cons_qty*$order_qty;
				//echo $tot_amount.'*'.$dzn_qnty.'*'.$order_qty.', ';
				?>
				<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    <td width="30"><p><? echo $i; ?></p></td>
                    <td width="70" align="center"><p><? echo $conversion_cost_head_array[$row[csf('cons_type')]]; ?></p></td>
                    <td width="80" align="right"><p><? echo number_format($order_qty,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($row[csf('req_qnty')],4); ?></p></td>
                    <td width="80" align="right"><p><? echo number_format($cons_qty,2); ?></p></td>
                    <td width="50" align="right"><p><? echo number_format($row[csf('charge_unit')],2); ?></p></td>
                    <td width="80" align="right"><p><? echo number_format($total_amount,2); ?></p></td>
				</tr>
				<?
				$tot_req_qty+=$cons_qty;
				$tot_amount_fab_dyeing+=$total_amount;
				$i++;
            }
            ?>
            </tbody>
            <tfoot>
                <tr class="tbl_bottom">
                    <td colspan="4" align="right">Total</td>
                    <td align="right"><? echo number_format($tot_req_qty,2); ?>&nbsp;</td>
                    <td>&nbsp; </td>
                    <td align="right"><? echo number_format($tot_amount_fab_dyeing,2); ?>&nbsp;</td>
                </tr>
         </tfoot>
        </table>
	</div>
	</fieldset>
	<?
	exit();
}

if($action=="fab_finishing_detail")
{
	echo load_html_head_contents("Fabrics Finishing Cost Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $job_no;die;
	$order_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");
	$order_qty=return_field_value("sum(b.plan_cut*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","po_quantity");
	$ratio_qty=return_field_value("sum(a.total_set_qnty) as ratio", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","ratio");
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
	<fieldset style="width:670px; margin-left:3px">
        <div id="scroll_body" align="center">
            <table  border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
                <tr> 
                	<td colspan="3" align="center"><strong> Fabric Finishing Cost Details</strong></td>
                </tr>
                <tr> 
                	<td width="150"><strong>Job No.</strong>&nbsp; <? echo $job_no; ?> </td><td  width="150"><strong>Order: </strong>&nbsp; <? echo $order_arr[$po_id];  ?></td><td  width="150"><strong>Buyer:</strong> &nbsp; <? echo $buyer_library[$buyer_id]; ?></td>
                </tr>
            </table>
            <table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
                <thead>
                    <th width="30">Sl</th>
                    <th width="70">Process</th>
                    <th width="80">GMTS Qty.</th>
                    <th width="100">Cons Qty./<? echo $costing_per_dzn; ?></th>
                    <th width="80">Req.Qty.</th>
                    <th width="50">Cost/Per Unit</th>
                    <th width="80">Amount</th>
                </thead>
                <tbody>
                <?
                $i=1;
               // $data_array=("select cons_process,req_qnty as req_qnty,charge_unit as charge_unit,amount as amount from wo_pre_cost_fab_conv_cost_dtls where job_no='$job_no' and cons_process in(34,65,66,67,68,69,70,71,73,75,76,77,88,90,91,92,93,100,125,127,128,129) and status_active=1 and is_deleted=0 group by id,cons_process,req_qnty,charge_unit,amount ");
			   $data_array=("select cons_process,req_qnty as req_qnty,charge_unit as charge_unit,amount as amount from wo_pre_cost_fab_conv_cost_dtls where job_no='$job_no' and cons_process in(65,66,67,68,69,70,71,72,73,77,91,93,94,100,125,127,128,136,143,150,151,155,156,157,159,160,161,164,166,167,168,183,184,185,186,187,189,190,191,194,195,196,197,198,199,200,201,202,204,205,206,207,208,210,211,212,219,225,226,227,228,229,230,240,242,249,250,251,253,254,255,256,257,258,259,260,261,262,263,264,265,277,278,279,288,290,291,292,293,294,406,407) and status_active=1 and is_deleted=0 group by id,cons_process,req_qnty,charge_unit,amount ");
                $sql_result=sql_select($data_array);
                foreach($sql_result as $row)
                {
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					$cons_qty=($row[csf('req_qnty')]/$dzn_qnty)*$order_qty;
					$tot_cons_amount=$cons_qty*$row[csf('charge_unit')];
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="30"><p><? echo $i; ?></p></td>
                        <td width="70" align="center"><p><? echo $conversion_cost_head_array[$row[csf('cons_process')]]; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($order_qty,2); ?></p></td>
                        <td width="100" align="right"><p><? echo number_format($row[csf('req_qnty')],4); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($cons_qty,2); ?></p></td>
                        <td width="50" align="right"><p><? echo number_format($row[csf('charge_unit')],2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($tot_cons_amount,2); ?></p></td>
					</tr>
					<?
					$tot_req_qty+=$cons_qty;
					$tot_amount+=$tot_cons_amount;
					$i++;
                }
                ?>
                </tbody>
                <tfoot>
                    <tr class="tbl_bottom">
                        <td colspan="4" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_req_qty,2); ?>&nbsp;</td>
                        <td>&nbsp; </td>
                        <td align="right"><? echo number_format($tot_amount,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>
	</fieldset>
	<?
	exit();
} //Pre Cost Finish cost end

if($action=="fab_price_finishing_detail")
{
	echo load_html_head_contents("Fabrics Finishing Cost Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $job_no;die;
	$order_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");
	$order_qty=return_field_value("sum(b.po_quantity*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","po_quantity");
	$ratio_qty=return_field_value("sum(a.total_set_qnty) as ratio", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","ratio");
	$price_costDataArray=sql_select("select  id,costing_per  from wo_price_quotation where status_active=1 and is_deleted=0  ");
	$price_costing_perArray=array();
	foreach($price_costDataArray as $pri_fabRow)
	{
		$price_costing_perArray[$pri_fabRow[csf('id')]]['costing_per']=$pri_fabRow[csf('costing_per')];
	}
	$costing_per_price=$price_costing_perArray[$quotation_id]['costing_per'];
	//echo $costing_per_price;
	if($costing_per_price==1)
	{
		$dzn_qnty=12;
		$costing_per_dzn="1 Dzn";
	}
	else if($costing_per_price==2)
	{
		$costing_per_dzn="1 Pcs";
	}
	else if($costing_per_price==3)
	{
		$dzn_qnty=12*2;
		$costing_per_dzn="2 Dzn";
	}
	else if($costing_per_price==4)
	{
		$dzn_qnty=12*3;
		$costing_per_dzn="3 Dzn";
	}
	else if($costing_per_price==5)
	{
		$dzn_qnty=12*4;
		$costing_per_dzn="4 Dzn";
	}
	else
	{
		$dzn_qnty=1;
	}
	$dzn_qnty= $dzn_qnty*$ratio_qty;
	?>
	<fieldset style="width:670px; margin-left:3px">
        <div id="scroll_body" align="center">
            <table  border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
                <tr> 
                	<td colspan="3" align="center"><strong> Fabric Finishing Cost Details</strong></td>
                </tr>
                <tr> 
                	<td width="150"><strong>Job No.</strong>&nbsp; <? echo $job_no; ?> </td><td  width="150"><strong>Order: </strong>&nbsp; <? echo $order_arr[$po_id];  ?></td><td  width="150"><strong>Buyer:</strong> &nbsp; <? echo $buyer_library[$buyer_id]; ?></td>
                </tr>
            </table>
            <table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
                <thead>
                    <th width="30">Sl</th>
                    <th width="70">Process</th>
                    <th width="80">GMTS Qty.</th>
                    <th width="100">Cons Qty./<? echo $costing_per_dzn; ?></th>
                    <th width="80">Req.Qty.</th>
                    <th width="50">Cost/Per Unit</th>
                    <th width="80">Amount</th>
                </thead>
                <tbody>
                <?
                $i=1;
                $data_array=("select cons_type,req_qnty as req_qnty,charge_unit as charge_unit,amount as amount from wo_pri_quo_fab_conv_cost_dtls where quotation_id='$quotation_id' and cons_type in(33,34,38,63,65,66,67,68,69,70,71,72,73,75,76,77,88,82,89,90,91,92,93,94,128,129,135,136,141,143,150,151,155,156,157,145,82,89,132,144) and status_active=1 and is_deleted=0 group by id,cons_type,req_qnty,charge_unit,amount ");
                $sql_result=sql_select($data_array);
                foreach($sql_result as $row)
                {
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					$cons_qty=($row[csf('req_qnty')]/$dzn_qnty)*$order_qty;
					$tot_cons_amount=$cons_qty*$row[csf('charge_unit')];
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="30"><p><? echo $i; ?></p></td>
                        <td width="70" align="center"><p><? echo $conversion_cost_head_array[$row[csf('cons_type')]]; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($order_qty,2); ?></p></td>
                        <td width="100" align="right"><p><? echo number_format($row[csf('req_qnty')],4); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($cons_qty,2); ?></p></td>
                        <td width="50" align="right"><p><? echo number_format($row[csf('charge_unit')],2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($tot_cons_amount,2); ?></p></td>
					</tr>
					<?
					$tot_req_qty+=$cons_qty;
					$tot_amount+=$tot_cons_amount;
					$i++;
                }
                ?>
                </tbody>
                <tfoot>
                    <tr class="tbl_bottom">
                        <td colspan="4" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_req_qty,2); ?>&nbsp;</td>
                        <td>&nbsp; </td>
                        <td align="right"><? echo number_format($tot_amount,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>
	</fieldset>
	<?
	exit();
}

if($action=="fab_washing_detail")
{
	echo load_html_head_contents("Fabrics Finishing Cost Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $job_no;die;
	$order_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");
	$order_qty=return_field_value("sum(b.plan_cut*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","po_quantity");
	$ratio_qty=return_field_value("sum(a.total_set_qnty) as ratio", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","ratio");
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
	<fieldset style="width:670px; margin-left:3px">
        <div id="scroll_body" align="center">
            <table  border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
                <tr> 
                	<td colspan="3" align="center"><strong> Fabric Washing Cost Details</strong></td>
                </tr>
                <tr> 
                	<td width="150"><strong>Job No.</strong>&nbsp; <? echo $job_no; ?> </td><td  width="150"><strong>Order: </strong>&nbsp; <? echo $order_arr[$po_id];  ?></td><td  width="150"><strong>Buyer:</strong> &nbsp; <? echo $buyer_library[$buyer_id]; ?></td>
                </tr>
            </table>
            <table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
                <thead>
                    <th width="30">Sl</th>
                    <th width="70">Particulars</th>
                    <th width="80">Type.</th>
                    <th width="80">Gmts. Qnty./<? echo $costing_per_dzn; ?></th>
                    <th width="80">Req.Qty.</th>
                    <th width="50">Rate</th>
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
				
				$conversion= new conversion($condition);
				//echo $conversion->getQuery(); die;
				$conversion_costing_arr_process=$conversion->getAmountArray_by_orderAndProcess();
				$conversion_qty_arr_process=$conversion->getQtyArray_by_orderAndProcess();
				//print_r($emblishment_costing_arr_name_wash);
				//$washing_cost_arr=array(64,82,89);
				$washing_cost_arr=array(64,132,140,142,175,180,181,182,188,192,193,209,231,233,239,276);
                $i=1;
                 $data_array="select  a.cons_process, avg(a.avg_req_qnty) as avg_req_qnty, sum(a.req_qnty) as req_qnty, avg(a.charge_unit) as charge_unit,
 sum(a.amount) as amount,b.id as po_id from wo_pre_cost_fab_conv_cost_dtls a,wo_po_break_down b where a.job_no='$job_no' and b.id in($po_id) and a.job_no=b.job_no_mst and a.cons_process in(64,132,140,142,175,180,181,182,188,192,193,209,231,233,239,276) and a.status_active=1 and a.is_deleted=0 group by  b.id,a.cons_process";
                $sql_result=sql_select($data_array);
                /*echo '<pre>';
                print_r($conversion_qty_arr_process); die;*/
                foreach($sql_result as $row)
                {
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					//echo $dzn_qnty;
					$cons_qty=($row[csf('req_qnty')]/$dzn_qnty)*$order_qty;
					$tot_amount=$row[csf('amount')];
					$total_amount=($tot_amount/$dzn_qnty)*$order_qty;
					$tot_cons_amount=$cons_qty*$order_qty;
					$washing_cost=$washing_qty=0;
					/*foreach($washing_cost_arr as $w_process_id)
					{
						$washing_cost=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$w_process_id]);	
						$washing_qty=array_sum($conversion_qty_arr_process[$row[csf('po_id')]][$w_process_id]);
					}*/

					$washing_cost=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$row[csf('cons_process')]]);	
					$washing_qty=array_sum($conversion_qty_arr_process[$row[csf('po_id')]][$row[csf('cons_process')]]);
						
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="30"><p><? echo $i; ?></p></td>
                        <td width="70" align="center"><p><? echo $conversion_cost_head_array[$row[csf('cons_process')]]; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($order_qty,2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($row[csf('avg_req_qnty')],2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($washing_qty,2); ?></p></td>
                        <td width="50" align="right"><p><? echo number_format($washing_cost/$washing_qty,2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($washing_cost,2); ?></p></td>
					</tr>
					<?
					$tot_req_qty+=$washing_qty;
					$tot_wash_amount+=$washing_cost;
					$i++;
                }
                ?>
                </tbody>
                <tfoot>
                    <tr class="tbl_bottom">
                        <td colspan="4" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_req_qty,2); ?>&nbsp;</td>
                        <td>&nbsp; </td>
                        <td align="right"><? echo number_format($tot_wash_amount,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>
	</fieldset>
	<?
	exit();
} //Pre Cost Wash details end

if($action=="fab_price_washing_detail")
{
	echo load_html_head_contents("Fabrics Finishing Cost Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $job_no;die;
	$order_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");
	$order_qty=return_field_value("sum(b.po_quantity*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","po_quantity");
	$ratio_qty=return_field_value("sum(a.total_set_qnty) as ratio", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","ratio");
	$price_costDataArray=sql_select("select  id,costing_per  from wo_price_quotation where status_active=1 and is_deleted=0  ");
	$price_costing_perArray=array();
	foreach($price_costDataArray as $pri_fabRow)
	{
		$price_costing_perArray[$pri_fabRow[csf('id')]]['costing_per']=$pri_fabRow[csf('costing_per')];
	}
	$costing_per_price=$price_costing_perArray[$quotation_id]['costing_per'];
	//echo $costing_per_price;
	if($costing_per_price==1)
	{
		$dzn_qnty=12;
		$costing_per_dzn="1 Dzn";
	}
	else if($costing_per_price==2)
	{
		$costing_per_dzn="1 Pcs";
	}
	else if($costing_per_price==3)
	{
		$dzn_qnty=12*2;
		$costing_per_dzn="2 Dzn";
	}
	else if($costing_per_price==4)
	{
		$dzn_qnty=12*3;
		$costing_per_dzn="3 Dzn";
	}
	else if($costing_per_price==5)
	{
		$dzn_qnty=12*4;
		$costing_per_dzn="4 Dzn";
	}
	else
	{
		$dzn_qnty=1;
	} $dzn_qnty=$dzn_qnty*$ratio_qty;
	?>
	<fieldset style="width:670px; margin-left:3px">
	<div id="scroll_body" align="center">
        <table  border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
            <tr> 
            	<td colspan="3" align="center"><strong> Fabric Washing Cost Details</strong></td>
            </tr>
            <tr> 
            	<td width="150"><strong>Job No.</strong>&nbsp; <? echo $job_no; ?> </td><td  width="150"><strong>Order: </strong>&nbsp; <? echo $order_arr[$po_id];  ?></td><td  width="150"><strong>Buyer:</strong> &nbsp; <? echo $buyer_library[$buyer_id]; ?></td>
            </tr>
        </table>
        <table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
            <thead>
                <th width="30">Sl</th>
                <th width="70">Process</th>
                <th width="80">GMTS Qty.</th>
                <th width="80">Cons.Qty./<? echo $costing_per_dzn; ?></th>
                <th width="80">Req.Qty.</th>
                <th width="50">Cost/Per Unit</th>
                <th width="80">Amount</th>
            </thead>
            <tbody>
            <?
            $i=1;
            $data_array=("select cons_type, req_qnty as req_qnty, charge_unit as charge_unit,amount as amount from wo_pri_quo_fab_conv_cost_dtls where quotation_id='$quotation_id' and cons_type in(140,142,148,64) and status_active=1 and is_deleted=0 group by id,cons_type ,req_qnty ,charge_unit,amount");
            $sql_result=sql_select($data_array);
            foreach($sql_result as $row)
            {
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				//echo $dzn_qnty;
				$cons_qty=($row[csf('req_qnty')]/$dzn_qnty)*$order_qty;
				$tot_amount=$row[csf('amount')];
				$total_amount=($tot_amount/$dzn_qnty)*$order_qty;
				$tot_cons_amount=$cons_qty*$order_qty;
				?>
				<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    <td width="30"><p><? echo $i; ?></p></td>
                    <td width="70" align="center"><p><? echo $conversion_cost_head_array[$row[csf('cons_type')]]; ?></p></td>
                    <td width="80" align="right"><p><? echo number_format($order_qty,2); ?></p></td>
                    <td width="80" align="right"><p><? echo number_format($row[csf('req_qnty')],2); ?></p></td>
                    <td width="80" align="right"><p><? echo number_format($cons_qty,2); ?></p></td>
                    <td width="50" align="right"><p><? echo number_format($row[csf('charge_unit')],2); ?></p></td>
                    <td width="80" align="right"><p><? echo number_format($total_amount,2); ?></p></td>
				</tr>
				<?
				$tot_req_qty+=$cons_qty;
				$tot_wash_amount+=$total_amount;
				$i++;
            }
            ?>
            </tbody>
            <tfoot>
                <tr class="tbl_bottom">
                    <td colspan="4" align="right">Total</td>
                    <td align="right"><? echo number_format($tot_req_qty,2); ?>&nbsp;</td>
                    <td>&nbsp; </td>
                    <td align="right"><? echo number_format($tot_wash_amount,2); ?>&nbsp;</td>
                </tr>
            </tfoot>
        </table>
	</div>
	</fieldset>
	<?
	exit();
}

if($action=="fab_all_over_detail")
{
	echo load_html_head_contents("Fabrics All Over Cost Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $job_no;die;
	$order_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");
	$order_qty=return_field_value("sum(b.plan_cut*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","po_quantity");
	$ratio_qty=return_field_value("sum(a.total_set_qnty) as ratio", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","ratio");
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
	$condition= new condition();
	 if($po_id!='')
	 {
		$condition->po_id("=$po_id"); 
	 }
	$condition->init();
	$conversion= new conversion($condition);
	$conversion_costing_arr_process=$conversion->getAmountArray_by_orderAndProcess();
	$conversion_qty_arr_process=$conversion->getQtyArray_by_orderAndProcess();
	//print_r($conversion_costing_arr_process);
	?>
	<fieldset style="width:670px; margin-left:3px">
        <div id="scroll_body" align="center">
            <table  border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
                <tr> 
                	<td colspan="3" align="center"><strong> Fabric All Over Print Cost Details</strong></td>
                </tr>
                <tr> 
                	<td width="150"><strong>Job No.</strong>&nbsp; <? echo $job_no; ?> </td><td  width="150"><strong>Order: </strong>&nbsp; <? echo $order_arr[$po_id];  ?></td><td  width="150"><strong>Buyer:</strong> &nbsp; <? echo $buyer_library[$buyer_id]; ?></td>
                </tr>
            </table>
            <table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
                <thead>
                    <th width="30">Sl</th>
                    <th width="70">Process</th>
                    <th width="80">GMTS Qty.</th>
                    <th width="80">Cons. Qty./<? echo $costing_per_dzn; ?></th>
                    <th width="80">Req.Qty.</th>
                    <th width="50">Cost/Per Unit</th>
                    <th width="80">Amount</th>
                </thead>
                <tbody>
                <?
                $i=1;
                $data_array=("select cons_process, avg_req_qnty, req_qnty as req_qnty, charge_unit as charge_unit, amount as amount from wo_pre_cost_fab_conv_cost_dtls where job_no='$job_no' and cons_process in(35,36,37,40,213,214,215,216,217,218,222,223,235,236,237) and status_active=1 and is_deleted=0");
                $sql_result=sql_select($data_array);
                foreach($sql_result as $row)
                {
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					//$cons_qty=($row[csf('req_qnty')]/$dzn_qnty)*$order_qty;
					$tot_amount=$row[csf('amount')];
					//$total_amount=($tot_amount/$dzn_qnty)*$order_qty;
					$tot_cons_amount=$cons_qty*$order_qty;
					$total_amount=array_sum($conversion_costing_arr_process[$po_id][$row[csf('cons_process')]]);
					$cons_qty=array_sum($conversion_qty_arr_process[$po_id][$row[csf('cons_process')]]);
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="30"><p><? echo $i; ?></p></td>
                        <td width="70" align="center"><p><? echo $conversion_cost_head_array[$row[csf('cons_process')]]; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($order_qty,2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($row[csf('avg_req_qnty')],2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($cons_qty,2); ?></p></td>
                        <td width="50" align="right"><p><? echo number_format($row[csf('charge_unit')],2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($total_amount,2); ?></p></td>
					</tr>
					<?
					$tot_req_qty+=$cons_qty;
					$total_all_over_amount+=$total_amount;
					$i++;
                }
                ?>
                </tbody>
                <tfoot>
                    <tr class="tbl_bottom">
                        <td colspan="4" align="right">Total</td>
                        <td align="right"><? echo  number_format($tot_req_qty,2); ?>&nbsp;</td>
                        <td>&nbsp; </td>
                        <td align="right"><? echo number_format($total_all_over_amount,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>
	</fieldset>
	<?
	exit();
}// Pre cost all over end

if($action=="fab_price_all_over_detail")
{
	echo load_html_head_contents("Fabrics All Over Cost Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $job_no;die;
	$order_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");
	$order_qty=return_field_value("sum(b.po_quantity*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","po_quantity");
	$ratio_qty=return_field_value("sum(a.total_set_qnty) as ratio", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","ratio");
	$price_costDataArray=sql_select("select  id,costing_per  from wo_price_quotation where status_active=1 and is_deleted=0  ");
	$price_costing_perArray=array();
	foreach($price_costDataArray as $pri_fabRow)
	{
		$price_costing_perArray[$pri_fabRow[csf('id')]]['costing_per']=$pri_fabRow[csf('costing_per')];
	}
	$costing_per_price=$price_costing_perArray[$quotation_id]['costing_per'];
	//echo $costing_per_price;
	if($costing_per_price==1)
	{
		$dzn_qnty=12;
		$costing_per_dzn="1 Dzn";
	}
	else if($costing_per_price==2)
	{
		$costing_per_dzn="1 Pcs";
	}
	else if($costing_per_price==3)
	{
		$dzn_qnty=12*2;
		$costing_per_dzn="2 Dzn";
	}
	else if($costing_per_price==4)
	{
		$dzn_qnty=12*3;
		$costing_per_dzn="3 Dzn";
	}
	else if($costing_per_price==5)
	{
		$dzn_qnty=12*4;
		$costing_per_dzn="4 Dzn";
	}
	else
	{
		$dzn_qnty=1;
	}$dzn_qnty=$dzn_qnty*$ratio_qty;
	?>
	<fieldset style="width:670px; margin-left:3px">
        <div id="scroll_body" align="center">
            <table  border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
                <tr> 
                	<td colspan="3" align="center"><strong> Fabric All Over Print Cost Details</strong></td>
                </tr>
                <tr> 
                	<td width="150"><strong>Job No.</strong>&nbsp; <? echo $job_no; ?> </td><td  width="150"><strong>Order: </strong>&nbsp; <? echo $order_arr[$po_id];  ?></td><td  width="150"><strong>Buyer:</strong> &nbsp; <? echo $buyer_library[$buyer_id]; ?></td>
                </tr>
            </table>
            <table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
                <thead>
                    <th width="30">Sl</th>
                    <th width="70">Process</th>
                    <th width="80">GMTS Qty.</th>
                    <th width="80">Cons.Qty./<? echo $costing_per_dzn; ?></th>
                    <th width="80">Req.Qty.</th>
                    <th width="50">Cost/Per Unit</th>
                    <th width="80">Amount</th>
                </thead>
                <tbody>
                <?
                $i=1;
                $data_array=("select cons_type ,req_qnty as req_qnty,charge_unit as charge_unit,amount as amount from  wo_pri_quo_fab_conv_cost_dtls where quotation_id='$quotation_id' and cons_type  in(35,36,37) and status_active=1 and is_deleted=0 group by id,cons_type ,req_qnty,charge_unit,amount");
                $sql_result=sql_select($data_array);
                foreach($sql_result as $row)
                {
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					$cons_qty=($row[csf('req_qnty')]/$dzn_qnty)*$order_qty;
					$tot_amount=$row[csf('amount')];
					$total_amount=($tot_amount/$dzn_qnty)*$order_qty;
					$tot_cons_amount=$cons_qty*$order_qty;
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="30"><p><? echo $i; ?></p></td>
                        <td width="70" align="center"><p><? echo $conversion_cost_head_array[$row[csf('cons_type')]]; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($order_qty,2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($row[csf('req_qnty')],2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($cons_qty,2); ?></p></td>
                        <td width="50" align="right"><p><? echo number_format($row[csf('charge_unit')],2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($total_amount,2); ?></p></td>
					</tr>
					<?
					$tot_req_qty+=$cons_qty;
					$total_all_over_amount+=$total_amount;
					$i++;
                }
                ?>
                </tbody>
                <tfoot>
                    <tr class="tbl_bottom">
                        <td colspan="4" align="right">Total</td>
                        <td align="right"><? echo  number_format($tot_req_qty,2); ?>&nbsp;</td>
                        <td>&nbsp; </td>
                        <td align="right"><? echo number_format($total_all_over_amount,2); ?>&nbsp;</td>
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
			$sql_po_qty=sql_select("select b.id,c.country_id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id     and a.status_active=1 and b.status_active=1 and c.status_active=1  group by b.id,a.total_set_qnty,c.country_id");
			foreach($sql_po_qty as $row)
			{
				$po_qty_country_wise[$row[csf('id')]][$row[csf('country_id')]]=$row[csf('order_quantity_set')];
				
			}
			$po_qty_po_wise=array();
			$sql_po_qty2=sql_select("select b.id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id  and a.status_active=1 and b.status_active=1 and c.status_active=1  group by b.id,a.total_set_qnty");
			foreach($sql_po_qty2 as $row)
			{
			//	$po_qty_country_wise[$row[csf('id')]][$row[csf('country_id')]]=$row[csf('order_quantity_set')];
				$po_qty_po_wise[$row[csf('id')]]=$row[csf('order_quantity_set')];
			}
	$order_qty=return_field_value("sum(b.po_quantity*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","po_quantity");
	
	$ratio_qty=return_field_value("sum(a.total_set_qnty) as ratio", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","ratio");
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
                where a.id=b.wo_pre_cost_trim_cost_dtls_id and a.job_no=b.job_no  and a.job_no='$job_no' and b.po_break_down_id=$po_id and a.status_active=1 and  a.is_deleted=0 group by a.id,b.po_break_down_id,a.trim_group,a.description, b.country_id,a.cons_dzn_gmts,a.cons_uom, a.brand_sup_ref,a.amount, a.rate order by a.id");
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
					/*$country=explode(",",$row[csf('country_id')]);
					 //print_r( $country);
					 for($c=0;$c<=count( $country);$c++)
					 {
						 if($row[csf('country_id')]==0)
						 {
							 
						   $order_qty_tr=$po_qty_po_wise[$row[csf('po_break_down_id')]]; 
						 }
						 else
						 {
							// echo $country[$c].'add';
						 $order_qty_tr+=$po_qty_country_wise[$row[csf('po_break_down_id')]][$country[$c]]; 
						 }
					 }*/
				
 	
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

if($action=="trim_cost_price_detail")
{
	echo load_html_head_contents("Trim Cost Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $job_no;die;
	$order_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");
	$order_qty=return_field_value("sum(b.po_quantity*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","po_quantity");
	//echo $order_qty; die;
	$ratio_qty=return_field_value("sum(a.total_set_qnty) as ratio", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","ratio");
	$price_costDataArray=sql_select("select  id,costing_per  from wo_price_quotation where status_active=1 and is_deleted=0  ");
	$price_costing_perArray=array();
	foreach($price_costDataArray as $pri_fabRow)
	{
		$price_costing_perArray[$pri_fabRow[csf('id')]]['costing_per']=$pri_fabRow[csf('costing_per')];
	}
	$costing_per_price=$price_costing_perArray[$quotation_id]['costing_per'];
	//echo $costing_per_price; die;
	if($costing_per_price==1)
	{
		$dzn_qnty=12;
		$costing_per_dzn="1 Dzn";
	}
	else if($costing_per_price==2)
	{
		$dzn_qnty=1;
		$costing_per_dzn="1 Pcs";
	}
	else if($costing_per_price==3)
	{
		$dzn_qnty=12*2;
		$costing_per_dzn="2 Dzn";
	}
	else if($costing_per_price==4)
	{
		$dzn_qnty=12*3;
		$costing_per_dzn="3 Dzn";
	}
	else if($costing_per_price==5)
	{
		$dzn_qnty=12*4;
		$costing_per_dzn="4 Dzn";
	}
	else
	{
		$dzn_qnty=1;
	}
	$dzn_qnty=$dzn_qnty*$ratio_qty;
	?>
	<fieldset style="width:770px; margin-left:3px">
        <div id="scroll_body" align="center">
        <table  border="1" class="rpt_table" rules="all" width="750" cellpadding="0" cellspacing="0" align="center">
            <tr> 
            	<td colspan="6" align="center"><strong> Price Trim Cost Details</strong></td>
            </tr>
            <tr> 
            	<td width="150"><strong>Job No.</strong>&nbsp; <? echo $job_no; ?> </td><td  width="150"><strong>Order: </strong>&nbsp; <? echo $order_arr[$po_id];  ?></td><td  width="150"><strong>Buyer:</strong> &nbsp; <? echo $buyer_library[$buyer_id]; ?></td>
            </tr>
        </table>
        <table border="1" class="rpt_table" rules="all" width="750" cellpadding="0" cellspacing="0" align="center">
            <thead>
                <th width="30">Sl</th>
                <th width="70">Item Name</th>
                <th width="80">UOM</th>
                <th width="70">Cons Per/<? echo $costing_per_dzn;?></th>
                <th width="80">Req. Qty</th>
                <th width="70">Rate Per Unit</th>
                <th width="80">Amount</th>
            </thead>
            <tbody>
            <?
            $i=1;
            $trimsArray=("select  a.trim_group, a.cons_dzn_gmts,a.cons_uom, a.amount, a.rate 
            from wo_pri_quo_trim_cost_dtls a where a.quotation_id='$quotation_id' and a.status_active=1 and  a.is_deleted=0 order by a.id");
            $sql_result=sql_select($trimsArray);
            foreach($sql_result as $row)
            {
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				
				$tot_amount=$row[csf('cons_dzn_gmts')];
				//echo $order_qty.'--'.$dzn_qnty.'--'.$tot_amount.'<br>';
				$total_reg=($order_qty/$dzn_qnty)*$tot_amount;
				$tot_cons_amount=$row[csf('rate')]*$total_reg;
				?>
				<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    <td width="30"><p><? echo $i; ?></p></td>
                    <td width="70" align="center"><p><? echo $item_library[$row[csf('trim_group')]]; ?></p></td>
                    <td width="80" align="right"><p><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></p></td>
                    <td width="70" align="right"><p><? echo $row[csf('cons_dzn_gmts')]; ?></p></td>
                    <td width="80" align="right"><p><? echo number_format($total_reg,4); ?></p></td>
                    <td width="70" align="right"><p><? echo $row[csf('rate')]; ?></p></td>
                    <td width="80" align="right"><p><? echo number_format($tot_cons_amount,4); ?></p></td>
				</tr>
				<?
				$tot_req_qty+=$tot_cons_amount;
				$total_all_over_amount+=$total_amount;
				$i++;
            }
            ?>
            </tbody>
            <tfoot>
                <tr class="tbl_bottom">
                    <td colspan="6" align="right">Total</td>
                    <td align="right"><? echo  number_format($tot_req_qty,2); ?>&nbsp;</td>
                </tr>
            </tfoot>
        </table>
        </div>
	</fieldset>
	<?
	exit();
}

if($action=="print_cost_detail")//budget
{
	echo load_html_head_contents("Trim Cost Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $job_no;die;
	$order_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");
	$order_qty=return_field_value("sum(b.plan_cut*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id='$po_id'","po_quantity");
	$ratio_qty=return_field_value("sum(a.total_set_qnty) as ratio", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id='$po_id'","ratio");
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
                $data_emb=("select  c.po_break_down_id as po_id, b.emb_name, b.emb_type, sum(b.cons_dzn_gmts) as cons_dzn_gmts, avg(b.rate) as rate, sum(b.amount) as print_amount  from  wo_pre_cost_embe_cost_dtls b, wo_pre_cos_emb_co_avg_con_dtls c where b.job_no='$job_no' and c.po_break_down_id=$po_id and  b.job_no=c.job_no and b.emb_name=1 and b.status_active=1 and  b.is_deleted=0  group by c.po_break_down_id,b.emb_name,b.emb_type");
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
                        <td align="right"><? echo  number_format($total_emblish_cost,2); ?>&nbsp;</td>
                         <td align="right"><? //echo  number_format($total_emblish_cost,2); ?>&nbsp;</td>
                         <td align="right"><? echo  number_format($tot_req_qty,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>
	</fieldset>
	<?
	exit();
}// Price Print

if($action=="price_print_cost_detail")//budget
{
	echo load_html_head_contents("Trim Cost Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $quotation_id.'hhhhh';die;
	$order_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");
	$order_qty=return_field_value("sum(b.po_quantity*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","po_quantity");
	$ratio_qty=return_field_value("sum(a.total_set_qnty) as ratio", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","ratio");
	$price_costDataArray=sql_select("select  id,costing_per  from wo_price_quotation where status_active=1 and is_deleted=0  ");
	$price_costing_perArray=array();
	foreach($price_costDataArray as $pri_fabRow)
	{
		$price_costing_perArray[$pri_fabRow[csf('id')]]['costing_per']=$pri_fabRow[csf('costing_per')];
	}
	$costing_per_price=$price_costing_perArray[$quotation_id]['costing_per'];
	//echo $costing_per_price;
	if($costing_per_price==1)
	{
		$dzn_qnty=12;
		$costing_per_dzn="1 Dzn";
	}
	else if($costing_per_price==2)
	{
		$costing_per_dzn="1 Pcs";
			$dzn_qnty=1;
	}
	else if($costing_per_price==3)
	{
		$dzn_qnty=12*2;
		$costing_per_dzn="2 Dzn";
	}
	else if($costing_per_price==4)
	{
		$dzn_qnty=12*3;
		$costing_per_dzn="3 Dzn";
	}
	else if($costing_per_price==5)
	{
		$dzn_qnty=12*4;
		$costing_per_dzn="4 Dzn";
	}
	else
	{
		$dzn_qnty=1;
	}
	$dzn_qnty=$dzn_qnty*$ratio_qty;
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
                $i=1;
                $data_emb=("select quotation_id, emb_name,emb_type,cons_dzn_gmts,rate, amount  AS print_amount from  wo_pri_quo_embe_cost_dtls where quotation_id='$quotation_id' and emb_name=1 and status_active=1 and  is_deleted=0  group by quotation_id,emb_name,emb_type,cons_dzn_gmts,amount,rate");
                $sql_result=sql_select($data_emb);
                foreach($sql_result as $row)
                {
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					
					$tot_amount=$row[csf('cons_dzn_gmts')];
					$total_reg=($order_qty/$dzn_qnty)*$tot_amount;
					$tot_cons_amount=$row[csf('rate')]*$total_reg;
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="30"><p><? echo $i; ?></p></td>
                        <td width="70" align="center"><p><? echo $emblishment_name_array[$row[csf('emb_name')]]; ?></p></td>
                        <td width="80"><p><? echo $emblishment_print_type[$row[csf('emb_type')]]; ?></p></td>
                        <td width="70" align="right"><p><? echo $row[csf('cons_dzn_gmts')]; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($total_reg,4); ?></p></td>
                        <td width="70" align="right"><p><? echo $row[csf('rate')]; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($tot_cons_amount,4); ?></p></td>
					</tr>
					<?
					$tot_req_qty+=$tot_cons_amount;
					$total_all_over_amount+=$total_amount;
					$i++;
                }
                ?>
                </tbody>
                <tfoot>
                    <tr class="tbl_bottom">
                        <td colspan="6" align="right">Total</td>
                        <td align="right"><? echo  number_format($tot_req_qty,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>
	</fieldset>
	<?
	exit();
}// Price Embroidery

if($action=="price_embroidery_cost_detail")//budget
{
	echo load_html_head_contents("Trim Cost Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $quotation_id.'hhhhh';die;
	$order_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");
	$order_qty=return_field_value("sum(b.po_quantity*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","po_quantity");
	$ratio_qty=return_field_value("sum(a.total_set_qnty) as ratio", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","ratio");
	$price_costDataArray=sql_select("select  id,costing_per  from wo_price_quotation where status_active=1 and is_deleted=0  ");
	$price_costing_perArray=array();
	foreach($price_costDataArray as $pri_fabRow)
	{
		$price_costing_perArray[$pri_fabRow[csf('id')]]['costing_per']=$pri_fabRow[csf('costing_per')];
	}
	$costing_per_price=$price_costing_perArray[$quotation_id]['costing_per'];
	//echo $costing_per_price;
	if($costing_per_price==1)
	{
		$dzn_qnty=12;
		$costing_per_dzn="1 Dzn";
	}
	else if($costing_per_price==2)
	{
		$costing_per_dzn="1 Pcs";
	}
	else if($costing_per_price==3)
	{
		$dzn_qnty=12*2;
		$costing_per_dzn="2 Dzn";
	}
	else if($costing_per_price==4)
	{
		$dzn_qnty=12*3;
		$costing_per_dzn="3 Dzn";
	}
	else if($costing_per_price==5)
	{
		$dzn_qnty=12*4;
		$costing_per_dzn="4 Dzn";
	}
	else
	{
		$dzn_qnty=1;
	}
	$dzn_qnty=$dzn_qnty*$ratio_qty;
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
                $i=1;
                $data_emb=("select  quotation_id,emb_name,emb_type,cons_dzn_gmts,rate, amount  AS print_amount from  wo_pri_quo_embe_cost_dtls where quotation_id='$quotation_id' and emb_name=2 and status_active=1 and  is_deleted=0  group by quotation_id,emb_name,emb_type,cons_dzn_gmts,amount,rate");
                $sql_result=sql_select($data_emb);
                foreach($sql_result as $row)
                {
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					
					$tot_amount=$row[csf('cons_dzn_gmts')];
					$total_reg=($order_qty/$dzn_qnty)*$tot_amount;
					$tot_cons_amount=$row[csf('rate')]*$total_reg;
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="30"><p><? echo $i; ?></p></td>
                        <td width="70" align="center"><p><? echo $emblishment_name_array[$row[csf('emb_name')]]; ?></p></td>
                        <td width="80"><p><? echo $emblishment_print_type[$row[csf('emb_type')]]; ?></p></td>
                        <td width="70" align="right"><p><? echo $row[csf('cons_dzn_gmts')]; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($total_reg,4); ?></p></td>
                        <td width="70" align="right"><p><? echo $row[csf('rate')]; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($tot_cons_amount,4); ?></p></td>
					</tr>
					<?
					$tot_req_qty+=$tot_cons_amount;
					$total_all_over_amount+=$total_amount;
					$i++;
                }
                ?>
                </tbody>
                <tfoot>
                    <tr class="tbl_bottom">
                        <td colspan="6" align="right">Total</td>
                        <td align="right"><? echo  number_format($tot_req_qty,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>
	</fieldset>
	<?
	exit();
}

if($action=="price_wash_cost_detail")//budget
{
	echo load_html_head_contents("Trim Cost Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $quotation_id.'hhhhh';die;
	$order_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");
	$order_qty=return_field_value("sum(b.po_quantity*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id='$po_id'","po_quantity");
	$ratio_qty=return_field_value("sum(a.total_set_qnty) as ratio", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id='$po_id'","ratio");
	$price_costDataArray=sql_select("select  id,costing_per  from wo_price_quotation where status_active=1 and is_deleted=0  ");
	$price_costing_perArray=array();
	foreach($price_costDataArray as $pri_fabRow)
	{
		$price_costing_perArray[$pri_fabRow[csf('id')]]['costing_per']=$pri_fabRow[csf('costing_per')];
	}
	$costing_per_price=$price_costing_perArray[$quotation_id]['costing_per'];
	//echo $costing_per_price;
	if($costing_per_price==1)
	{
		$dzn_qnty=12;
		$costing_per_dzn="1 Dzn";
	}
	else if($costing_per_price==2)
	{
		$costing_per_dzn="1 Pcs";
	}
	else if($costing_per_price==3)
	{
		$dzn_qnty=12*2;
		$costing_per_dzn="2 Dzn";
	}
	else if($costing_per_price==4)
	{
		$dzn_qnty=12*3;
		$costing_per_dzn="3 Dzn";
	}
	else if($costing_per_price==5)
	{
		$dzn_qnty=12*4;
		$costing_per_dzn="4 Dzn";
	}
	else
	{
		$dzn_qnty=1;
	}$dzn_qnty=$dzn_qnty*$ratio_qty;
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
				$i=1;
				$data_emb=("select  quotation_id,emb_name,emb_type,cons_dzn_gmts,rate, amount  AS print_amount from  wo_pri_quo_embe_cost_dtls where quotation_id='$quotation_id' and emb_name=3 and status_active=1 and  is_deleted=0  group by quotation_id,emb_name,emb_type,cons_dzn_gmts,amount,rate");
				$sql_result=sql_select($data_emb);
				foreach($sql_result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					
					$tot_amount=$row[csf('cons_dzn_gmts')];
					$total_reg=($order_qty/$dzn_qnty)*$tot_amount;
					$tot_cons_amount=$row[csf('rate')]*$total_reg;
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="30"><p><? echo $i; ?></p></td>
						<td width="70" align="center"><p><? echo $emblishment_name_array[$row[csf('emb_name')]]; ?></p></td>
						<td width="80" align="right"><p><? echo $emblishment_print_type[$row[csf('emb_type')]]; ?></p></td>
						<td width="70" align="right"><p><? echo $row[csf('cons_dzn_gmts')]; ?></p></td>
						<td width="80" align="right"><p><? echo number_format($total_reg,4); ?></p></td>
						<td width="70" align="right"><p><? echo $row[csf('rate')]; ?></p></td>
						<td width="80" align="right"><p><? echo number_format($tot_cons_amount,4); ?></p></td>
					</tr>
					<?
					$tot_req_qty+=$tot_cons_amount;
					$total_all_over_amount+=$total_amount;
					$i++;
				}
				?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="6" align="right">Total</td>
						<td align="right"><? echo  number_format($tot_req_qty,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	exit();
}

if($action=="embroidery_cost_detail")//budget
{
	echo load_html_head_contents("Trim Cost Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $job_no;die;
	$order_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");
	$order_qty=return_field_value("sum(b.plan_cut*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id='$po_id'","po_quantity");
	$ratio_qty=return_field_value("sum(a.total_set_qnty) as ratio", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id='$po_id'","ratio");
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
                	<td colspan="6" align="center"><strong> Embroidery Cost Details</strong></td>
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
                $i=1;
                $data_emb=("select  job_no,emb_name,emb_type,cons_dzn_gmts,rate, amount  AS print_amount from  wo_pre_cost_embe_cost_dtls where job_no='$job_no' and emb_name=2 and status_active=1 and  is_deleted=0  group by job_no,emb_name,emb_type,cons_dzn_gmts,amount,rate");
                $sql_result=sql_select($data_emb);
                foreach($sql_result as $row)
                {
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					
					$tot_amount=$row[csf('cons_dzn_gmts')];
					$total_reg=($order_qty/$dzn_qnty)*$tot_amount;
					$tot_cons_amount=$row[csf('rate')]*$total_reg;
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="30"><p><? echo $i; ?></p></td>
                        <td width="70" align="center"><p><? echo $emblishment_name_array[$row[csf('emb_name')]]; ?></p></td>
                        <td width="80"><p><? echo $emblishment_embroy_type[$row[csf('emb_type')]]; ?></p></td>
                        <td width="70" align="right"><p><? echo $row[csf('cons_dzn_gmts')]; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($total_reg,4); ?></p></td>
                        <td width="70" align="right"><p><? echo $row[csf('rate')]; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($tot_cons_amount,4); ?></p></td>
					</tr>
					<?
					$tot_req_qty+=$tot_cons_amount;
					$total_all_over_amount+=$total_amount;
					$i++;
                }
                ?>
                </tbody>
                <tfoot>
                    <tr class="tbl_bottom">
                        <td colspan="6" align="right">Total</td>
                        <td align="right"><? echo  number_format($tot_req_qty,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>
	</fieldset>
	<?
	exit();
}//wash_cost_detail

if($action=="wash_cost_detail")//budget
{
	echo load_html_head_contents("Trim Cost Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $job_no;die;
	$order_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");
	$order_qty=return_field_value("sum(b.plan_cut*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id='$po_id'","po_quantity");
	$ratio_qty=return_field_value("sum(a.total_set_qnty) as ratio", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id='$po_id'","ratio");
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
                	<td colspan="6" align="center"><strong> Wash Cost Details</strong></td>
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
                $i=1;
                $data_emb=("select  job_no,emb_name,emb_type,cons_dzn_gmts,rate, amount  AS print_amount from  wo_pre_cost_embe_cost_dtls where job_no='$job_no' and emb_name=3 and status_active=1 and  is_deleted=0  group by job_no,emb_name,emb_type,cons_dzn_gmts,amount,rate");
                $sql_result=sql_select($data_emb);
                foreach($sql_result as $row)
                {
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					
					$tot_amount=$row[csf('cons_dzn_gmts')];
					$total_reg=($order_qty/$dzn_qnty)*$tot_amount;
					$tot_cons_amount=$row[csf('rate')]*$total_reg;
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="30"><p><? echo $i; ?></p></td>
                        <td width="70" align="center"><p><? echo $emblishment_name_array[$row[csf('emb_name')]]; ?></p></td>
                        <td width="80" align="right"><p><? echo $emblishment_wash_type[$row[csf('emb_type')]]; ?></p></td>
                        <td width="70" align="right"><p><? echo $row[csf('cons_dzn_gmts')]; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($total_reg,4); ?></p></td>
                        <td width="70" align="right"><p><? echo $row[csf('rate')]; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($tot_cons_amount,4); ?></p></td>
					</tr>
					<?
					$tot_req_qty+=$tot_cons_amount;
					$total_all_over_amount+=$total_amount;
					$i++;
                }
                ?>
                </tbody>
                <tfoot>
                    <tr class="tbl_bottom">
                        <td colspan="6" align="right">Total</td>
                        <td align="right"><? echo  number_format($tot_req_qty,2); ?>&nbsp;</td>
                	</tr>
                </tfoot>
            </table>
        </div>
	</fieldset>
	<?
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

if($action=="country_order_dtls_popup")
{
	echo load_html_head_contents("Country Order Dtls Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$order_arr=return_library_array( "select id, po_number from wo_po_break_down where id='$po_id'", "id", "po_number");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
		}	
	</script>	
	<fieldset style="width:670px; margin-left:3px">
        <div style="width:670px;" align="center">
        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
        </div>
        <div id="report_div" align="center">
            <table rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
                <tr> 
                	<td colspan="4" align="center"><strong> Country Wise Order Details </strong></td>
                </tr>
                <tr> 
                    <td width="130"><strong>Job No. :&nbsp; <? echo $job_no; ?>;</strong></td>
                    <td width="150"><strong> Order:&nbsp;<? echo $order_arr[$po_id]; ?>;</strong></td>
                    <td width="150"><strong> Buyer:&nbsp;<? echo $buyer_library[$buyer_id]; ?>;</strong></td>
                    <td><strong> Country Ship Date:&nbsp;<? echo change_date_format($country_date); ?></strong></td>
                </tr>
            </table>
            <table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
                <thead>
                    <th width="30">SL</th>
                    <th width="100">Country</th>
                    <th width="100">Cut Off</th>
                    <th width="90">Order Qty</th>
                    <th width="60">Avg Exc. %</th>
                    <th width="90">Plan Cut Qty.</th>
                    <th width="60">Avg Rate</th>
                    <th>Order Value</th>
                </thead>
                <tbody>
                <?
				$contry_sql="select country_id, cutup, sum(order_quantity) as po_qty, sum(plan_cut_qnty) as plan_cut_qty, sum(order_total) as order_value from wo_po_color_size_breakdown where country_ship_date='$country_date' and po_break_down_id='$po_id' and status_active=1 and is_deleted=0 group by country_id, cutup";
				$contry_sql_result=sql_select($contry_sql); $i=1;
				foreach($contry_sql_result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					
					$avg_ex_per=0; $avg_rate=0;
					$avg_ex_per=(($row[csf('plan_cut_qty')]-$row[csf('po_qty')])/$row[csf('po_qty')])*100;
					$avg_rate=($row[csf('order_value')]/$row[csf('po_qty')]);
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="30"><p><? echo $i; ?></p></td>
						<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $country_arr[$row[csf('country_id')]]; ?></div></td>
						<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $cut_up_array[$row[csf('cutup')]]; ?></div></td>
						<td width="90" align="right"><p><? echo number_format($row[csf('po_qty')]); ?></p></td>
						<td width="60" align="right"><p><? echo number_format($avg_ex_per,3).' %'; ?></p></td>
						<td width="90" align="right"><p><? echo number_format($row[csf('plan_cut_qty')]); ?></p></td>
						<td width="60" align="right"><p><? echo number_format($avg_rate,4); ?></p></td>
                        <td align="right"><p><? echo number_format($row[csf('order_value')],2); ?></p></td>
					</tr>
					<?
					$tot_po_qty+=$row[csf('po_qty')];
					$tot_plan_cut_qty+=$row[csf('plan_cut_qty')];
					$tot_order_value+=$row[csf('order_value')];
					$i++;
				}
				?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="3" align="right">Total</td>
						<td align="right"><? echo number_format($tot_po_qty,2); ?></td>
                        <td align="right">&nbsp;</td>
                        <td align="right"><? echo number_format($tot_plan_cut_qty,2); ?></td>
                        <td align="right">&nbsp;</td>
                        <td align="right"><? echo number_format($tot_order_value,2); ?></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	exit();
}

if($action=="country_trims_dtls_popup")
{
	echo load_html_head_contents("Country Trims Dtls Info", "../../../../", 1, 1,'','','');
	

	extract($_REQUEST);
	$order_arr=return_library_array( "select id, po_number from wo_po_break_down where id='$po_id'", "id", "po_number");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	$item_name_arr=return_library_array( "select id, item_name  from  lib_item_group",'id','item_name');
	$order_qty=return_field_value("sum(b.plan_cut*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no'   and b.id='$po_id'","po_quantity");
	$supplier_name_arr=return_library_array( "select id, supplier_name  from  lib_supplier",'id','supplier_name');

	
	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
		}	
	</script>	
	<fieldset style="width:800px; margin-left:3px">
        <div style="width:800px;" align="center">
        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
        </div>
        <div id="report_div" align="center">
            <table border="1" class="rpt_table" rules="all" width="790" cellpadding="0" cellspacing="0" align="center">
                <thead>
                    <th width="30">SL</th>
                    <th width="100">Trims Name</th>
                    <th width="130">Desciption</th>
                    <th width="50">Cons</th>
                    <th width="50">Exc. %</th>
                    <th width="50">Ex. Cons</th>
                    <th width="60">Req. Qty.</th>
                    <th width="60">Rate</th>
                    <th width="60">Amount</th>
                    <th width="100">Nominated Supplier</th>
                    <th>Apv. Req.</th>
                </thead>
                <tbody>
                <?
				//$ratio_qty=return_field_value("sum(a.total_set_qnty) as ratio", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no'   and b.id='$po_id'","ratio");
				$costing_per=return_field_value("costing_per as costing_per", "wo_pre_cost_mst", "job_no='$job_no'","costing_per");
				$dzn_qnty=0;
				if($costing_per==1) $dzn_qnty=12;
				else if($costing_per==3) $dzn_qnty=12*2;
				else if($costing_per==4) $dzn_qnty=12*3;
				else if($costing_per==5) $dzn_qnty=12*4;
				else $dzn_qnty=1;
				 $dzn_qnty=$dzn_qnty;
	
				$country=array_unique(explode(",",$country_id));
				$country_id='';
				foreach($country as $c_id)
				{
					if($country_id!='') $country_id.=",".$c_id;else $country_id=$c_id;
				}
				if($country_id!=0)  $country_cond="and c.country_id in($country_id)";else $country_cond="";
				
				 $contry_sql=("select  a.id as trims_id,b.po_break_down_id as po_id,c.country_id,avg(b.cons) as cons, avg(b.excess_per) as excess_per, sum(a.amount) as amount,avg(b.ex_cons) as ex_cons,avg( b.tot_cons) as tot_cons,a.trim_group,a.description, a.cons_dzn_gmts,a.cons_uom, a.brand_sup_ref, avg(a.rate) as rate,a.nominated_supp, a.apvl_req 
                from wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b,wo_po_color_size_breakdown c 
                where a.id=b.wo_pre_cost_trim_cost_dtls_id and a.job_no=b.job_no and c.job_no_mst=a.job_no  and a.id=b.wo_pre_cost_trim_cost_dtls_id and b.po_break_down_id=c.po_break_down_id  and a.job_no='$job_no' and b.po_break_down_id=$po_id and a.status_active=1 and  a.is_deleted=0  $country_cond  group by a.id,a.trim_group,a.description, a.cons_dzn_gmts,a.cons_uom, a.brand_sup_ref,a.nominated_supp, a.apvl_req,b.po_break_down_id,c.country_id ");
				//getQtyArray_by_orderCountryAndPrecostdtlsid()
				//getAmountArray_by_orderCountryAndPrecostdtlsid()
				$contry_sql_result=sql_select($contry_sql); 
				$i=1;
				
				$condition= new condition();
				
				if($po_id!='')
				{
					$condition->po_id("in($po_id)"); 
				}
				$condition->init();
				  
				$trims= new trims($condition);
				
				 //echo $trims->getQuery(); die;
				$trims_costing_arr_qty=$trims->getQtyArray_by_orderCountryAndPrecostdtlsid();
				$trims_costing_arr_amount=$trims->getAmountArray_by_orderCountryAndPrecostdtlsid();
				
				
				foreach($contry_sql_result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					$amount=$row[csf('amount')];
					$rate=$row[csf('rate')];
					
					$req_qty=$trims_costing_arr_qty[$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('trims_id')]];
					$req_amount=$trims_costing_arr_amount[$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('trims_id')]];
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="30"><p><? echo $i; ?></p></td>
						<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $item_name_arr[$row[csf('trim_group')]]; ?></div></td>
						<td width="130"><div style="word-wrap:break-word; width:100px"><? echo $row[csf('description')]; ?></div></td>
						<td width="50" align="right"><p><? echo $row[csf('cons')]; ?></p></td>
						<td width="50" align="right"><p><? echo $row[csf('excess_per')].' %'; ?></p></td>
						<td width="50" align="right"><p><? echo $row[csf('ex_cons')]; ?></p></td>
						<td width="60" align="right"><p><? echo number_format($req_qty,2); ?></p></td>
                        <td width="60" align="right"><p><? echo number_format($req_amount/$req_qty,2); ?></p></td>
                        <td width="60" align="right"><p><? echo number_format($req_amount,2);//number_format($row[csf('amount')],4); ?></p></td>
                        <td width="100" align="right"><p><? echo $supplier_name_arr[$row[csf('nominated_supp')]]; ?></p></td>
                        <td align="right"><p><? echo $yes_no[$row[csf('apvl_req')]]; ?></p></td>
					</tr>
					<?
					$tot_req_qty+=$req_qty;
					$tot_plan_cut_qty+=$row[csf('plan_cut_qty')];
					$tot_trim_value+=$req_amount;
					$i++;
				}
				?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="6" align="right">Total</td>
						<td align="right"><? echo number_format($tot_req_qty,2); ?></td>
                        <td align="right">&nbsp;</td>
                        <td align="right"><? echo number_format($tot_trim_value,2); ?></td>
                        <td align="right">&nbsp;</td>
                        <td align="right"><? //echo number_format($tot_order_value,2); ?></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	exit();
}

if($action=="img")
{
	echo load_html_head_contents("Image View", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	
 ?>
	<fieldset style="width:600px; margin-left:5px">
		<div style="width:100%; word-wrap:break-word" id="scroll_body">
             <table border="0" rules="all" width="100%" cellpadding="2" cellspacing="2">
             	<tr>
					<?
					$i=0;
                    $sql="select image_location from common_photo_library where master_tble_id='$job_no' and form_name='knit_order_entry' and file_type=1";
                    $result=sql_select($sql);
                    foreach($result as $row)
                    {
						$i++;
                    ?>
                    	<td align="center"><img width="300px" height="180px" src="../../../../<? echo $row[csf('image_location')];?>" /></td>
                    <?
						if($i%2==0) echo "</tr><tr>";
                    }
                    ?>
                </tr>
            </table>
        </div>	
	</fieldset>     
	<?
    exit();
}
?>