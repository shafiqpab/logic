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
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "");     	 
	exit();
}

$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
$yarn_count_library=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
$team_member_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
$team_leader_arr=return_library_array( "select b.id as id ,a.team_leader_name as team_leader_name from lib_marketing_team a, lib_mkt_team_member_info b where a.id=b.team_id and a.is_deleted=0 and b.is_deleted=0",'id','team_leader_name');
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'woven_order_wise_budget_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	
	echo  create_list_view("list_view", "Job No,Buyer,Style Ref.,Order No", "110,110,150,180","610","350",0, $sql, "js_set_value", "id,po_number", "", 1, "0,buyer_name,0,0,0", $arr , "job_no_prefix_num,buyer_name,style_ref_no,po_number", "order_wise_budget_report_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0','') ;
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
	//echo 'joy'.$total_months; die;
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
		
	$job_no=str_replace("'","",$txt_job_no);
	//echo $job_no;die;
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
	//if($season=="") $season_cond=""; else $season_cond=" and a.season in('".implode("','",explode(",",$season))."')";
	if($txt_season_id=="") $season_cond=""; else $season_cond=" and a.season_buyer_wise in(".$txt_season_id.")";
	//echo $season_cond;
	
	//if($txt_internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping=txt_internal_ref";
	
	$order_no=str_replace("'","",$txt_order_id);
	$order_num=str_replace("'","",$txt_order_no);
	if(str_replace("'","",$txt_order_id)!="" && str_replace("'","",$txt_order_id)!=0) $order_id_cond_trans=" and b.id in ($order_no)";
	else if ($order_num=="") $order_no_cond=""; else $order_no_cond=" and  b.po_number in ('$order_num') ";
	
	
	if($report_type==1) //Summary
	{
		if($template==1)
		{
			$style1="#E9F3FF"; 
			$style="#FFFFFF";
			
			$asking_profit_arr=array(); 		
			
			if($db_type==2)
			{
				if(str_replace("'","",$cbo_search_date)==1)
				{
					$date_type="to_char(b.pub_shipment_date,'YYYY-MM-DD')";
				}
				else if(str_replace("'","",$cbo_search_date)==2)
				{
					$date_type="to_char(b.po_received_date,'YYYY-MM-DD')";
				}
				else if(str_replace("'","",$cbo_search_date)==3)
				{
					$date_type="to_char(b.insert_date,'YYYY-MM-DD')";
				}
			}
			else if ($db_type==0)
			{
				if(str_replace("'","",$cbo_search_date)==1)
				{
					$date_type="b.pub_shipment_date";
				}
				else if(str_replace("'","",$cbo_search_date)==2)
				{
					$date_type="b.po_received_date";
				}
				else if(str_replace("'","",$cbo_search_date)==3)
				{
					$date_type="year(b.insert_date,'YYYY-MM-DD')";
				}
			}
			
			$buyer_data=array(); $buyer_data_arr=array();
			$job_sql="select a.job_no, a.buyer_name, a.avg_unit_price, a.total_set_qnty as ratio";
			$k=1;
			foreach($month_array as $val)
			{
				$job_sql.=",sum(CASE WHEN $date_type like '$val-%%' THEN b.plan_cut END) AS plan_cut$k
				,sum(CASE WHEN $date_type like '$val-%%' THEN b.po_quantity END) AS po_quantity$k
				,sum(CASE WHEN $date_type like '$val-%%' THEN b.unit_price END) AS unit_price$k
				,sum(CASE WHEN $date_type like '$val-%%' THEN b.po_total_price END) AS po_total_price$k
				";
				$k++;
			}
			
			$job_sql.=", b.id as po_id from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c where a.job_no=b.job_no_mst  and a.job_no=c.job_no  and b.job_no_mst=c.job_no  and a.company_name='$company_name' and c.entry_from=158 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.garments_nature=3 $date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond $order_status_cond $internal_ref_cond $file_no_cond $season_cond group by a.job_no, a.buyer_name, a.avg_unit_price, a.total_set_qnty, b.id, b.insert_date, b.is_confirmed";
			 $qty=0; $amt=0; $plan_cut_qnty=0;
			$result=sql_select($job_sql); 
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
				  $condition->country_ship_date(" between '$start_date' and '$end_date'");
			 }
			 if(str_replace("'","",$cbo_search_date) ==2 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
			 {
				 $condition->po_received_date(" between '$start_date' and '$end_date'");
			 }
			
			 if(str_replace("'","",$cbo_search_date)==3 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
			 {				

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
			 }
			 $condition->init();
			$yarn= new yarn($condition);
     
			$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
		
			$conversion= new conversion($condition);
			$conversion_costing_arr=$conversion->getAmountArray_by_order();
			$trims= new trims($condition);

			$trims_costing_arr=$trims->getAmountArray_by_order();
			$fabric= new fabric($condition);
			$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
			$emblishment= new emblishment($condition);
			$emblishment_costing_arr=$emblishment->getAmountArray_by_order();
			$wash= new wash($condition);
			$emblishment_costing_arr_name=$wash->getAmountArray_by_orderAndEmbname();
			$commercial= new commercial($condition);
			$commercial_costing_arr=$commercial->getAmountArray_by_order();
			$commission= new commision($condition);
			$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
			$other= new other($condition);
			$other_costing_arr=$other->getAmountArray_by_order();
			foreach($result as $row)
			{
				$dzn_qnty=0;
				$costing_per_id=$costing_per_arr[$row[csf('job_no')]];//$fabriccostArray[$row[csf('job_no')]]['costing_per_id'];
				if($costing_per_id==1) $dzn_qnty=12;
				else if($costing_per_id==3) $dzn_qnty=12*2;
				else if($costing_per_id==4) $dzn_qnty=12*3;
				else if($costing_per_id==5) $dzn_qnty=12*4;
				else $dzn_qnty=1;
				$dzn_qnty_yarn=$dzn_qnty*$row[csf('ratio')];
				
				$k=1;
				foreach($month_array as $val)
				{
					$plan_cut_qnty=$row[csf('plan_cut'.$k)]*$row[csf('ratio')];
					$po_quantity=$row[csf('po_quantity'.$k)]*$row[csf('ratio')];
					$order_value=$row[csf('po_total_price'.$k)];//$row[csf('po_quantity'.$k)]*$row[csf('avg_unit_price')];
					
					
					$fab_purchase_knit=array_sum($fabric_costing_arr['knit']['grey'][$row[csf('po_id')]]);
					$fab_purchase_woven=array_sum($fabric_costing_arr['woven']['grey'][$row[csf('po_id')]]);
					$fab_purchase=$fab_purchase_knit+$fab_purchase_woven;
					$yarn_costing=$yarn_costing_arr[$row[csf('po_id')]];
					$trim_cost= $trims_costing_arr[$row[csf('po_id')]];//$fabriccostArray[$row[csf('job_no')]]['trims_cost']/$dzn_qnty*$po_quantity;
					$wash_cost=$emblishment_costing_arr_name[$row[csf('po_id')]][3];//$fab_emb[$row[csf('job_no')]]['wash']/$dzn_qnty*$po_quantity;
					$embl_cost=$emblishment_costing_arr[$row[csf('po_id')]]+$wash_cost;//($fabriccostArray[$row[csf('job_no')]]['embel_cost']/$dzn_qnty*$po_quantity)+$wash_cost;
					$commercial_cost=$commercial_costing_arr[$row[csf('po_id')]];//($fabriccostArray[$row[csf('job_no')]]['comm_cost']/$dzn_qnty)*$po_quantity;
					
					
				
					$foreign=$commission_costing_arr[$row[csf('po_id')]][1];
                    $local=$commission_costing_arr[$row[csf('po_id')]][2];
					$commission_cost=$foreign+$local;
					
					$testing_cost=$other_costing_arr[$row[csf('po_id')]]['lab_test'];//$fabriccostArray[$row[csf('job_no')]]['lab_test']/$dzn_qnty*$po_quantity;
					$freight_cost=$other_costing_arr[$row[csf('po_id')]]['freight'];//$fabriccostArray[$row[csf('job_no')]]['freight']/$dzn_qnty*$po_quantity;
					
					$inspection_cost=$other_costing_arr[$row[csf('po_id')]]['inspection'];//$fabriccostArray[$row[csf('job_no')]]['inspection']/$dzn_qnty*$po_quantity;
					$certificate_cost=$other_costing_arr[$row[csf('po_id')]]['certificate_pre_cost'];//$fabriccostArray[$row[csf('job_no')]]['certificate_pre_cost']/$dzn_qnty*$po_quantity;
					$common_oh=$other_costing_arr[$row[csf('po_id')]]['common_oh'];//$fabriccostArray[$row[csf('job_no')]]['common_oh']/$dzn_qnty*$po_quantity;
					$currier_cost=$other_costing_arr[$row[csf('po_id')]]['currier_pre_cost'];//$fabriccostArray[$row[csf('job_no')]]['currier_pre_cost']/$dzn_qnty*$po_quantity;
					
                    $cm_cost=$other_costing_arr[$row[csf('po_id')]]['cm_cost'];//$fabriccostArray[$row[csf('job_no')]]['c_cost']/$dzn_qnty*$po_quantity;
                    $cm_cost_dzn=$fabriccostArray[$row[csf('job_no')]]['c_cost'];
					
					$kniting_cost=$knit_arr[$row[csf('job_no')]]['knit']+$knit_arr[$row[csf('job_no')]]['weaving']+$knit_arr[$row[csf('job_no')]]['collar_cuff']+$knit_arr[$row[csf('job_no')]]['feeder_stripe'];
					$knit_cost=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][1])+array_sum($conversion_costing_arr_process[$row[csf('po_id')]][2])+array_sum($conversion_costing_arr_process[$row[csf('po_id')]][3]);//($kniting_cost/$dzn_qnty)*$plan_cut_qnty;
					$fabric_dyeing_cost=($knit_arr[$row[csf('job_no')]]['fabric_dyeing']/$dzn_qnty)*$plan_cut_qnty;
					$yarn_dyed_cost=($knit_arr[$row[csf('job_no')]]['yarn_dyeing']/$dzn_qnty)*$plan_cut_qnty;
					$heat_setting_cost=($knit_arr[$row[csf('job_no')]]['heat']/$dzn_qnty)*$plan_cut_qnty;
					$fabric_finish=($knit_arr[$row[csf('job_no')]]['fabric_finish']/$dzn_qnty)*$plan_cut_qnty;
					$all_over_cost=($knit_arr[$row[csf('job_no')]]['all_over']/$dzn_qnty)*$plan_cut_qnty;
				
					$lab_test_cost=$other_costing_arr[$row[csf('po_id')]]['lab_test'];//$fabriccostArray[$row[csf('job_no')]]['lab_test']/$dzn_qnty*$order_qty_pcs;
					$other_direct_expenses=$freight_cost+$inspection_cost+$certificate_cost+$currier_cost+$lab_test_cost+$wash_cost;
					
					$conversion_cost=$conversion_costing_arr[$row[csf('po_id')]];//$knit_cost+$fabric_dyeing_cost+$yarn_dyed_cost+$heat_setting_cost+$fabric_finish+$wash_cost+$all_over_cost;
					$cost_of_material_service=$yarn_costing+$conversion_cost+$trim_cost+$embl_cost+$other_direct_expenses;
					
					$net_fob_value=$order_value-$commission_cost;
					$contribution_value=$net_fob_value-$cost_of_material_service;
					$gross_profit=$contribution_value-$cm_cost;
					$operating_expense=$other_costing_arr[$row[csf('po_id')]]['common_oh'];//$fabriccostArray[$row[csf('job_no')]]['common_oh']/$dzn_qnty*$po_quantity;

					$operating_profit=$gross_profit-($commercial_cost+$operating_expense);
					
					$depreciation_amortization=$other_costing_arr[$row[csf('po_id')]]['depr_amor_pre_cost'];//$fabriccostArray[$row[csf('job_no')]]['depr_amor_cost']/$dzn_qnty*$po_quantity;
					$interest_expense=$net_fob_value*$financial_para[csf('interest_expense')]/100;
					$income_tax=$net_fob_value*$financial_para[csf('income_tax')]/100;
					$net_profit=$operating_profit-($depreciation_amortization+$interest_expense+$income_tax);
					
					$buyer_data_arr[$row[csf('buyer_name')]][$val]['yarn_cost']+=$yarn_costing;
					$buyer_data_arr[$row[csf('buyer_name')]][$val]['conversion_cost']+=$conversion_cost;//$conver_costing;
					$buyer_data_arr[$row[csf('buyer_name')]][$val]['fab_purchase']+=$fab_purchase;
					$buyer_data_arr[$row[csf('buyer_name')]][$val]['trim_cost']+=$trim_cost;
					$buyer_data_arr[$row[csf('buyer_name')]][$val]['embl_amount']+=$embl_cost;
					$buyer_data_arr[$row[csf('buyer_name')]][$val]['commercial_cost']+=$commercial_cost;
					$buyer_data_arr[$row[csf('buyer_name')]][$val]['commission_cost']+=$commission_cost;
					$buyer_data_arr[$row[csf('buyer_name')]][$val]['testing_cost']+=$testing_cost;
					$buyer_data_arr[$row[csf('buyer_name')]][$val]['freight_cost']+=$freight_cost;
					$buyer_data_arr[$row[csf('buyer_name')]][$val]['inspection_cost']+=$inspection_cost;
					$buyer_data_arr[$row[csf('buyer_name')]][$val]['certificate_cost']+=$certificate_cost;
					$buyer_data_arr[$row[csf('buyer_name')]][$val]['common_oh']+=$common_oh;
					$buyer_data_arr[$row[csf('buyer_name')]][$val]['currier_cost']+=$currier_cost;
					$buyer_data_arr[$row[csf('buyer_name')]][$val]['cm_cost']+=$cm_cost;
					$buyer_data_arr[$row[csf('buyer_name')]][$val]['order_value']+=$order_value;
					$buyer_data_arr[$row[csf('buyer_name')]][$val]['profit_loss']+=$net_profit;
					$k++;				
				}
				$buyer_data[$row[csf('buyer_name')]]=$row[csf('buyer_name')];
			}
			$table_width=200+(1750*($total_months+1));
			$col_span=2+(17*$total_months);
			ob_start();
			?>
            <br/>  
            <div> 
            <fieldset>
                <table width="<? echo $table_width; ?>">
                    <tr class="form_caption">
                        <td colspan="<? echo $col_span; ?>" align="center"><strong>Order Wise Budget Report Summary</strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="<? echo $col_span; ?>" align="center"><strong><? echo $company_library[$company_name]; ?></strong></td>
                    </tr>
                </table>
                <table class="rpt_table" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr>
                            <th width="30" rowspan="3">SL</th>
                            <th width="150" rowspan="3">Buyer</th>
                             <?
                                foreach($month_array as $month_id)
                                {
                                    $month_name=date("F",strtotime($month_id)).", ".date("Y",strtotime($month_id));
                                    ?>
                                    <th colspan="17">Month OF <? echo $month_name; ?></th>
                                    <?
                                }		 
                            ?>
                        </tr>
                        <tr>
                            <?
                            foreach($month_array as $month_id)
                            {
                                ?>
                                <th colspan="3">Fabric Cost</th>
                                <th width="100" rowspan="2">Trim Cost</th>
                                <th width="100" rowspan="2">Embellishment Cost</th>
                                <th width="100" rowspan="2">Commercial Cost</th>
                                <th width="100" rowspan="2">Commision Cost</th>
                                <th width="100" rowspan="2">Testing Cost</th>
                                <th width="100" rowspan="2">Freight Cost</th>
                                <th width="100" rowspan="2">Inspection Cost</th>
                                <th width="100" rowspan="2">Certificate Cost</th>
                                <th width="100" rowspan="2">Commn OH</th>
                                <th width="100" rowspan="2">Courier OH</th>
                                <th width="100" rowspan="2">CM Cost</th>
                                <th width="100" rowspan="2">Total Cost</th>
                                <th width="100" rowspan="2">Total Order value</th>
                                <th rowspan="2">Profit/Loss</th>
                            <?
                            }		 
                            ?>
                        </tr>
                        <tr>
                            <?
                            foreach($month_array as $month_id)
                            {
                                ?>
                                <th width="100">Yarn Cost</th>
                                <th width="100">Convirsion Cost</th>
                                <th width="100">Fab. Purch. Cost</th>
                            <?
                            }		 
                            ?>
                        </tr>
                    </thead>
                </table>
                <div style="width:<? echo 20+$table_width; ?>px; max-height:250px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
                <? 
                    $i=1; 
                    foreach($buyer_data as $buyer_id )
                    {
                        if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
        
                        ?>
                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="150"><p><? echo $buyer_library[$buyer_id] ?></p></td>
                            <?
                            foreach($month_array as $val)
                            {
                                //echo $val;
                                $row_yarn_costing=$buyer_data_arr[$buyer_id][$val]['yarn_cost'];
                                $row_conver_costing=$buyer_data_arr[$buyer_id][$val]['conversion_cost'];
                                $row_fab_purchase=$buyer_data_arr[$buyer_id][$val]['fab_purchase'];
                                $row_trim_amount=$buyer_data_arr[$buyer_id][$val]['trim_cost'];
                                $row_embl_amount=$buyer_data_arr[$buyer_id][$val]['embl_amount'];
                                $row_commercial_cost=$buyer_data_arr[$buyer_id][$val]['commercial_cost'];
                                $row_commission_cost=$buyer_data_arr[$buyer_id][$val]['commission_cost'];
                                $row_testing_cost=$buyer_data_arr[$buyer_id][$val]['testing_cost'];
                                $row_freight_cost=$buyer_data_arr[$buyer_id][$val]['freight_cost'];
                                $row_inspection_cost=$buyer_data_arr[$buyer_id][$val]['inspection_cost'];
                                $row_certificate_cost=$buyer_data_arr[$buyer_id][$val]['certificate_cost'];
                                $row_common_oh=$buyer_data_arr[$buyer_id][$val]['common_oh'];
                                $row_currier_cost=$buyer_data_arr[$buyer_id][$val]['currier_cost'];
                                $row_cm_cost=$buyer_data_arr[$buyer_id][$val]['cm_cost'];
                                $row_total_order_value=$buyer_data_arr[$buyer_id][$val]['order_value'];	
                                $tot_cost=$row_yarn_costing+$row_conver_costing+$row_fab_purchase+$row_trim_amount+$row_embl_amount+$row_commercial_cost+$row_commission_cost+$row_testing_cost+$row_freight_cost+$row_inspection_cost+$row_certificate_cost+$row_common_oh+$row_currier_cost+$row_cm_cost;	
								//echo 'yarn_costing='.$row_yarn_costing.'=conver_costing='.$row_conver_costing.'=fab_purchase='.$row_fab_purchase.'=trim_amount='.$row_trim_amount.'=embl_amount='.$row_embl_amount.'=commercial_cost='.$row_commercial_cost.'=testing_cost='.$row_testing_cost.'=freight_cost='.$row_freight_cost.'=inspection_cost='.$row_inspection_cost.'=certificate_cost='.$row_certificate_cost.'=common_oh='.$row_common_oh.'=currier_cost='.$row_currier_cost.'=cm_cost='.$row_cm_cost.'<br>';
                                $profit_loss=$row_total_order_value-$tot_cost;	
                                ?>
                                <td width="100" align="right"><p><? echo number_format($row_yarn_costing,2); ?></p></td>
                                <td width="100" align="right"><p><? echo number_format($row_conver_costing,2); ?></p></td>
                                <td width="100" align="right"><p><? echo number_format($row_fab_purchase,2); ?></p></td>
                                <td width="100" align="right"><p><? echo number_format($row_trim_amount,2); ?></p></td>
                                <td width="100" align="right"><p><? echo number_format($row_embl_amount,2); ?></p></td>
                                <td width="100" align="right"><p><? echo number_format($row_commercial_cost,2); ?></p></td>
                                <td width="100" align="right"><p><? echo number_format($row_commission_cost,2); ?></p></td>
                                
                                <td width="100" align="right"><p><? echo number_format($row_testing_cost,2); ?></p></td>
                                <td width="100" align="right"><p><? echo number_format($row_freight_cost,2); ?></p></td>
                                <td width="100" align="right"><p><? echo number_format($row_inspection_cost,2); ?></p></td>
                                <td width="100" align="right"><p><? echo number_format($row_certificate_cost,2); ?></p></td>
                                <td width="100" align="right"><p><? echo number_format($row_common_oh,2); ?></p></td>
                                <td width="100" align="right"><p><? echo number_format($row_currier_cost,2); ?></p></td>
                                <td width="100" align="right"><p><? echo number_format($row_cm_cost,2); ?></p></td>
                                <td width="100" align="right"><p><? echo number_format($tot_cost,2); ?></p></td>
                                <td width="100" align="right"><p><? echo number_format($row_total_order_value,2); ?></p></td>
                                <td align="right"><p><? echo number_format($profit_loss,2); ?></p></td>
                                <?
                                $total_yarn_costing[$val]+=$row_yarn_costing;
                                $total_conver_costing[$val]+=$row_conver_costing;
                                $total_fab_purchase[$val]+=$row_fab_purchase;
                                $total_trim_amount[$val]+=$row_trim_amount;
                                $total_embl_amount[$val]+=$row_embl_amount;
                                $total_commercial_cost[$val]+=$row_commercial_cost;
                                $total_commission_cost[$val]+=$row_commission_cost;
                                $total_testing_cost[$val]+=$row_testing_cost;
                                $total_freight_cost[$val]+=$row_freight_cost;
                                $total_inspection_cost[$val]+=$row_inspection_cost;
                                $total_certificate_cost[$val]+=$row_certificate_cost;
                                $total_common_oh[$val]+=$row_common_oh;
                                $total_currier_cost[$val]+=$row_currier_cost;
                                $total_cm_cost[$val]+=$row_cm_cost;
                                $total_cost[$val]+=$tot_cost;
                                $total_ord_value[$val]+=$row_total_order_value;
                                $total_profit_losse[$val]+=$profit_loss;
                            }		 
                            ?>
                        </tr> 
                        <?
                        //echo $total_fab_cost_amount;
                        $i++;
                    }
                    ?>
                </table>
                </div>
                <table class="tbl_bottom" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <tr>
                        <td align="right" width="30">&nbsp;</td>
                        <td align="right" width="150">Total :</td>
                        <?
                        foreach($month_array as $val)
                        {
                        ?>
                            <td align="right" width="100"><p><? echo number_format($total_yarn_costing[$val],2); ?></p></td>
                            <td align="right" width="100"><p><? echo number_format($total_conver_costing[$val],2); ?></p></td>
                            <td align="right" width="100"><p><? echo number_format($total_fab_purchase[$val],2); ?></p></td>
                            <td align="right" width="100"><p><? echo number_format($total_trim_amount[$val],2); ?></p></td>
                            <td align="right" width="100"><p><? echo number_format($total_embl_amount[$val],2); ?></p></td>
                            <td align="right" width="100"><p><? echo number_format($total_commercial_cost[$val],2); ?></p></td>
                            <td align="right" width="100"><p><? echo number_format($total_commission_cost[$val],2); ?></p></td>
                            <td align="right" width="100"><p><? echo number_format($total_testing_cost[$val],2); ?></p></td>
                            <td align="right" width="100"><p><? echo number_format($total_freight_cost[$val],2); ?></p></td>
                            <td align="right" width="100"><p><? echo number_format($total_inspection_cost[$val],2); ?></p></td>
                            <td align="right" width="100"><p><? echo number_format($total_certificate_cost[$val],2); ?></p></td>
                            <td align="right" width="100"><p><? echo number_format($total_common_oh[$val],2); ?></p></td>
                            <td align="right" width="100"><p><? echo number_format($total_currier_cost[$val],2); ?></p></td>
                            <td align="right" width="100"><p><? echo number_format($total_cm_cost[$val],2); ?></p></td>
                            <td align="right" width="100"><p><? echo number_format($total_cost[$val],2); ?></p></td>
                            <td align="right" width="100"><p><? echo number_format($total_ord_value[$val],2); ?></p></td>  
                            <td align="right"><p><? echo number_format($total_profit_losse[$val],2); ?></p></td>
                        <?
                        }
                        ?>
                    </tr>
                </table>
            </fieldset>
        </div>
        <?
		}
	}
	else if($report_type==2)//Budget
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
		
			$sql_budget="select a.job_no_prefix_num, b.insert_date, a.order_uom,a.job_no, a.buyer_name, a.style_ref_no, b.is_confirmed, a.agent_name, a.avg_unit_price, 
			a.dealing_marchant, a.set_smv, a.gmts_item_id, a.total_set_qnty as ratio, b.plan_cut, b.id as po_id, b.po_number, b.pub_shipment_date, b.po_received_date,
			b.po_quantity, b.po_total_price, b.unit_price, b.grouping, b.file_no,c.costing_date
			from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and c.entry_from in (158,425) and a.company_name='$company_name' and a.status_active=1 and
			a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond 
		    $order_status_cond $season_cond $internal_ref_cond $file_no_cond";

		   // echo $sql_budget.'****10'; die;
			
			$result_sql_budget=sql_select($sql_budget); 
			$tot_rows_budget=count($result_sql_budget); 
			
			ob_start();
			
			?>
            <script>
			 /*var order_amt=document.getElementById('total_order_amount2').innerHTML.replace(/,/g ,'');
			document.getElementById('yarn_cost').innerHTML=document.getElementById('total_yarn_cost2').innerHTML;
			document.getElementById('yarn_cost_per').innerHTML=document.getElementById('total_yarn_cost_per').innerHTML + ' %';
			document.getElementById('purchase_cost').innerHTML=document.getElementById('total_purchase_cost').innerHTML;
			document.getElementById('purchase_cost_per').innerHTML=number_format_common((document.getElementById('total_purchase_cost').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			document.getElementById('knit_cost').innerHTML=document.getElementById('total_knitting_cost').innerHTML;
			document.getElementById('knit_cost_per').innerHTML=number_format_common((document.getElementById('total_knitting_cost').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			document.getElementById('ydyeing_cost').innerHTML=document.getElementById('total_yarn_dyeing_cost').innerHTML;
			document.getElementById('ydyeing_cost_per').innerHTML=number_format_common((document.getElementById('total_yarn_dyeing_cost').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			document.getElementById('aop_cost').innerHTML=document.getElementById('all_over_print_cost').innerHTML;
			document.getElementById('aop_cost_per').innerHTML=number_format_common((document.getElementById('all_over_print_cost').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			
			var dyefin_val=(parseFloat(document.getElementById('total_fabric_dyeing_cost4').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('total_finishing_cost').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('total_heat_setting_cost').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('total_washing_cost').innerHTML.replace(',','')));
			document.getElementById('dyefin_cost').innerHTML=number_format_common(dyefin_val,2);
			document.getElementById('dyefin_cost_per').innerHTML=number_format_common((dyefin_val/order_amt)*100,2) + ' %';
			document.getElementById('trim_cost').innerHTML=document.getElementById('total_trim_cost').innerHTML;
			document.getElementById('trim_cost_per').innerHTML=number_format_common((document.getElementById('total_trim_cost').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			var embelishment_val=(parseFloat(document.getElementById('total_print_amount').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('total_embroidery_amount').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('total_special_amount').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('total_wash_cost').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('total_other_amount').innerHTML.replace(',','')));
			document.getElementById('embelishment_cost').innerHTML=number_format_common(embelishment_val,2);
			document.getElementById('embelishment_cost_per').innerHTML=number_format_common((embelishment_val/order_amt)*100,2) + ' %';
			document.getElementById('commercial_cost').innerHTML=document.getElementById('total_commercial_cost').innerHTML;
			document.getElementById('commercial_cost_per').innerHTML=number_format_common((document.getElementById('total_commercial_cost').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			var comission_val=(parseFloat(document.getElementById('total_foreign_amount').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('total_local_amount').innerHTML.replace(',','')));
			document.getElementById('commission_cost').innerHTML=number_format_common(comission_val,2);
			document.getElementById('commission_cost_per').innerHTML=number_format_common((comission_val/order_amt)*100,2) + ' %';
			document.getElementById('testing_cost').innerHTML=document.getElementById('total_test_cost_amount').innerHTML;
			document.getElementById('testing_cost_per').innerHTML=number_format_common((document.getElementById('total_test_cost_amount').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			document.getElementById('freight_cost').innerHTML=document.getElementById('total_freight_amount').innerHTML;
			document.getElementById('freight_cost_per').innerHTML=number_format_common((document.getElementById('total_freight_amount').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			document.getElementById('inspection_cost').innerHTML=document.getElementById('total_inspection_amount').innerHTML;
			document.getElementById('inspection_cost_per').innerHTML=number_format_common((document.getElementById('total_inspection_amount').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			document.getElementById('certificate_cost').innerHTML=document.getElementById('total_certificate_amount').innerHTML;
			document.getElementById('certificate_cost_percent').innerHTML=number_format_common((document.getElementById('total_certificate_amount').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			
			document.getElementById('commn_cost').innerHTML=document.getElementById('total_common_oh_amount').innerHTML;
			document.getElementById('commn_cost_per').innerHTML=number_format_common((document.getElementById('total_common_oh_amount').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			
			document.getElementById('courier_cost').innerHTML=document.getElementById('total_currier_amount').innerHTML;
			document.getElementById('courier_cost_per').innerHTML=number_format_common((document.getElementById('total_currier_amount').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			document.getElementById('cm_cost').innerHTML=document.getElementById('total_cm_amount').innerHTML;
			document.getElementById('cm_cost_per').innerHTML=number_format_common((document.getElementById('total_cm_amount').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			
			document.getElementById('cost_cost').innerHTML=document.getElementById('total_tot_cost').innerHTML;
			document.getElementById('cost_cost_per').innerHTML=number_format_common((document.getElementById('total_tot_cost').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			
			document.getElementById('order_id').innerHTML=number_format_common(order_amt,2);
			document.getElementById('order_percent').innerHTML=number_format_common((order_amt/order_amt)*100,2);
			
			document.getElementById('fab_profit_id').innerHTML=document.getElementById('total_fabric_profit').innerHTML;
			document.getElementById('profit_fab_percentage').innerHTML=number_format_common((document.getElementById('total_fabric_profit').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			document.getElementById('expected_id').innerHTML=document.getElementById('total_expected_profit').innerHTML;
			document.getElementById('profit_expt_fab_percentage').innerHTML=number_format_common((document.getElementById('total_expected_profit').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			document.getElementById('expt_p_variance_id').innerHTML=document.getElementById('total_expected_variance').innerHTML;
			document.getElementById('expt_p_percent').innerHTML=number_format_common((document.getElementById('total_expected_variance').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			
			var matr_ser_cost=(parseFloat(document.getElementById('yarn_cost').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('purchase_cost').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('knit_cost').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('ydyeing_cost').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('aop_cost').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('dyefin_cost').innerHTML.replace(',','')));
			document.getElementById('tot_matr_ser_cost').innerHTML=number_format_common(matr_ser_cost,2);
			document.getElementById('tot_matr_ser_per').innerHTML=number_format_common((matr_ser_cost/order_amt)*100,2) + ' %'; */
			//var aa=order_amt;
			//alert(aa);
			
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
            <div style="width:4570px;">
            <div style="width:900px;" align="left">
                <table width="900" cellpadding="0" cellspacing="2" border="0">
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
			 if(str_replace("'","",$txt_season)!='')
			 {
				//$condition->season("=$txt_season"); 
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
		 	$trims= new trims($condition);
			$trims_costing_arr=$trims->getAmountArray_by_order();
			
			$fabric= new fabric($condition);
			$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
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
			$knit_cost_arr=array(1,2,3,4);
			$fabric_dyeingCost_arr=array(25,31,26,32,60,61,62,63,72,80,81,84,85,86,87,38,39,74,78,79,101,133,137,138,139,146,147,149);
			$aop_cost_arr=array(35,36,37,40);
			$fab_finish_cost_arr=array(33,34,38,63,65,66,67,68,69,70,71,72,73,75,76,77,88,82,89,90,91,92,93,94,128,129,135,136,141,143,150,151,155,156,157,145,82,89,132,144);
			$washing_cost_arr=array(140,142,148,64);
	
	
			foreach($result_sql_budget as $row )
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
				if(str_replace("'","",$cbo_search_date)==1)
				{
					$ship_po_recv_date=change_date_format($row[csf('pub_shipment_date')]);
				}
				else if(str_replace("'","",$cbo_search_date)==2)
				{
					$ship_po_recv_date=change_date_format($row[csf('po_received_date')]);
				}
				else if(str_replace("'","",$cbo_search_date)==3)
				{
					$insert_date=explode(" ",$row[csf('insert_date')]);
					$ship_po_recv_date=change_date_format($insert_date[0]);
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
				$commercial_cost=$commercial_costing_arr[$row[csf('po_id')]];//($fabriccostArray[$row[csf('job_no')]]['comm_cost']/$dzn_qnty)*$order_qty_pcs;
						
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
						
						//$knit_cost=($kniting_cost/$dzn_qnty)*$plan_cut_qnty;
						
						$yarn_dyeing_cost=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][30]);
						if(is_infinite($yarn_dyeing_cost) || is_nan($yarn_dyeing_cost)){$yarn_dyeing_cost=0;}

						$yarn_dyeing_cost_dzn=($yarn_dyeing_cost/$plan_cut_qnty)*$dzn_qnty;
						if(is_infinite($yarn_dyeing_cost_dzn) || is_nan($yarn_dyeing_cost_dzn)){$yarn_dyeing_cost_dzn=0;}
						$fabric_dyeing_cost_dzn=($fabric_dyeing_cost/$plan_cut_qnty)*$dzn_qnty;
						if(is_infinite($fabric_dyeing_cost_dzn) || is_nan($fabric_dyeing_cost_dzn)){$fabric_dyeing_cost_dzn=0;}
						$heat_setting_cost=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][33]);
						if(is_infinite($heat_setting_cost) || is_nan($heat_setting_cost)){$heat_setting_cost=0;}
						$trim_amount= $trims_costing_arr[$row[csf('po_id')]];
						$print_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][1];
						$embroidery_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][2];
						$special_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][4];
						$wash_cost=$emblishment_costing_arr_name_wash[$row[csf('po_id')]][3];
						$other_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][5];
						$foreign=$commission_costing_arr[$row[csf('po_id')]][1];
						$local=$commission_costing_arr[$row[csf('po_id')]][2];
						$test_cost=$other_costing_arr[$row[csf('po_id')]]['lab_test'];
						$freight_cost= $other_costing_arr[$row[csf('po_id')]]['freight'];
						$inspection=$other_costing_arr[$row[csf('po_id')]]['inspection'];
						$certificate_cost=$other_costing_arr[$row[csf('po_id')]]['certificate_pre_cost'];
						$common_oh=$other_costing_arr[$row[csf('po_id')]]['common_oh'];
						$currier_cost=$other_costing_arr[$row[csf('po_id')]]['currier_pre_cost'];
						
						$cm_cost=$other_costing_arr[$row[csf('po_id')]]['cm_cost'];
						$cm_cost_dzn=($cm_cost/$order_qty_pcs)*$dzn_qnty;
						if(is_infinite($cm_cost_dzn) || is_nan($cm_cost_dzn)){$cm_cost_dzn=0;}

						if(is_infinite($cm_cost_dzn) || is_nan($cm_cost_dzn)){$cm_cost_dzn=0;}
						$total_cost=$yarn_costing+$fab_purchase+$knit_cost+$washing_cost+$all_over_cost+$yarn_dyeing_cost+$fabric_dyeing_cost+$heat_setting_cost+$fabric_finish+$trim_amount+$test_cost+$print_amount+$embroidery_amount+$special_amount+$other_amount+$wash_cost+$commercial_cost+$foreign+$local+$freight_cost+$inspection+$certificate_cost+$common_oh+$currier_cost+$cm_cost;
						
						$total_profit=$order_value-$total_cost;
						$total_profit_percentage2=$total_profit/$order_value*100; 
						$expected_profit=$asking_profit_arr[$row[csf('company_name')]]['asking_profit']*$order_value/100;
						if(is_infinite($expected_profit) || is_nan($expected_profit)){$expected_profit=0;}
						$expect_variance=$total_profit-$expected_profit;
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
					$expected_profit=$asking_profit*$order_value/100;
						$expected_profit_per=$asking_profit;
						$expect_variance=$total_profit-$expected_profit_per;
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
				$total_common_oh+=$common_oh;
				$total_currier_cost+=$currier_cost;
				$total_fabric_profit+=$total_profit;
				$total_expected_profit+=$expected_profit;
				$total_expt_profit_percentage+=$total_profit_percentage;
				$total_expected_variance+=$expect_variance;
				$total_profit_fab_percentage_up+=$total_profit_percentage2;
			}
				?>
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
                                    <td width="120" align="right" id="yarn_cost"><? echo number_format($total_yarn_cost,2);?></td>
                                    <td width="80" align="right" id="yarn_cost_per"><? echo number_format($yarn_cost_percent=($total_yarn_cost/$total_order_amount)*100,4);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>2</td>
                                    <td>Fabric Purchase</td>
                                    <td align="right" id="purchase_cost"><? echo number_format($total_purchase_cost,2);?></td>
                                    <td align="right" id="purchase_cost_per"><? echo number_format(($total_purchase_cost/$total_order_amount)*100,4);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>3</td>
                                    <td>Knitting Cost</td>
				
                                    <td align="right" id="knit_cost"><? echo number_format($total_knitting_cost,2);?></td>
                                    <td align="right" id="knit_cost_per"><? echo number_format(($total_knitting_cost/$total_order_amount)*100,4);//$total_fabric_dyeing_cost?></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>4</td>
                                    <td>Yarn Dyeing Cost</td>
                                    <td align="right" id="ydyeing_cost"><? echo number_format($total_yarn_dyeing_cost,2);?></td>
                                    <td align="right" id="ydyeing_cost_per"><? echo number_format(($total_yarn_dyeing_cost/$total_order_amount)*100,4);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>5</td>
                                    <td>AOP Cost</td>
                                    <td align="right" id="aop_cost"><? echo number_format($all_over_print_cost,2);?></td>
                                    <td align="right" id="aop_cost_per"><? echo number_format(($all_over_print_cost/$total_order_amount)*100,4);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>6</td>
                                    <td>Dyeing & Finishing Cost</td>
                                    <td align="right" id="dyefin_cost"><? echo number_format($total_finishing_cost,2);?></td>
                                    <td align="right" id="dyefin_cost_per"><? echo number_format(($total_finishing_cost/$total_order_amount)*100,4);?></td>
                                </tr>
                                <tr bgcolor="#CCCCCC">
                                    <td colspan="2"><strong>Total Material & Service Cost</strong></td>
                                    <?
                                    $tot_materials_service_cost=$total_yarn_cost+$total_purchase_cost+$total_knitting_cost+$all_over_print_cost+$total_finishing_cost;
									?>
                                    <td align="right" id="tot_matr_ser_cost"><? echo number_format($tot_materials_service_cost,2);?></td>
                                    <td align="right" id="tot_matr_ser_per"><? echo number_format(($tot_materials_service_cost/$total_order_amount)*100,4);?></td>
                                </tr>
                                <tr bgcolor="<?  echo $style1; ?>">
                                    <td>7</td>
                                    <td>Trims Cost</td> 
				
                                    <td align="right" id="trim_cost"><? echo number_format($total_trim_cost,2);?></td>
                                    <td align="right" id="trim_cost_per"><? echo number_format(($total_trim_cost/$total_order_amount)*100,4);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>8</td>
                                    <td>Print/ Emb. /Wash Cost/SP Or Other</td>
                                    <td align="right" id="embelishment_cost"><? echo number_format($total_embelishment_cost,2);?></td>
                                    <td align="right" id="embelishment_cost_per"><? echo number_format(($total_embelishment_cost/$total_order_amount)*100,4);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>9</td>
                                    <td>Commercial Cost</td>
                                    <td align="right" id="commercial_cost"><? echo number_format($total_commercial_cost,2);?></td>
                                    <td align="right" id="commercial_cost_per"><? echo number_format(($total_commercial_cost/$total_order_amount)*100,4);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>10</td>
                                    <td>Commision Cost</td>
                                    <td align="right" id="commission_cost"><? echo number_format($total_commssion,2);?></td>
                                    <td align="right" id="commission_cost_per"><? echo number_format(($total_commssion/$total_order_amount)*100,4);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>11</td>
                                    <td>Testing Cost</td>
                                    <td align="right" id="testing_cost"><? echo number_format($total_testing_cost,2);?></td>
                                    <td align="right" id="testing_cost_per"><? echo number_format(($total_testing_cost/$total_order_amount)*100,4);?></td>
                                </tr>
                                    <tr bgcolor="<? echo $style1; ?>">
                                    <td>12</td>
                                    <td>Freight Cost</td>
                                    <td align="right" id="freight_cost"><? echo number_format($total_freight_cost,2);?></td>
                                    <td align="right" id="freight_cost_per"><? echo number_format(($total_freight_cost/$total_order_amount)*100,4);?></td>
                                
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td width="20">13</td>
                                    <td width="100">Inspection Cost</td>
                                    <td align="right" id="inspection_cost"><? echo number_format($total_inspection,2);?></td>
                                    <td align="right" id="inspection_cost_per"><? echo number_format(($total_inspection/$total_order_amount)*100,4);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>14</td>
                                    <td>Certificate Cost</td>
                                    <td align="right" id="certificate_cost"><? echo number_format($total_certificate_cost,2);?></td>
                                    <td align="right" id="certificate_cost_percent"><? echo number_format(($total_certificate_cost/$total_order_amount)*100,4);?></td>
                                </tr>
                                    <tr bgcolor="<? echo $style; ?>">
                                    <td>15</td>
                                    <td>Operating Exp.</td>
                                    <td align="right" id="commn_cost"><? echo number_format($total_common_oh,2);?></td>
                                    <td align="right" id="commn_cost_per"><? echo number_format(($total_common_oh/$total_order_amount)*100,4);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>16</td>
                                    <td>Courier Cost</td>
                                    <td align="right" id="courier_cost"><? echo number_format($total_currier_cost,2);?></td>
                                    <td align="right" id="courier_cost_per"><? echo number_format(($total_currier_cost/$total_order_amount)*100,4);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>17</td>
                                    <td>CM Cost</td>
                                    <td align="right" id="cm_cost"><? echo number_format($total_cm_cost,2);?></td>
                                    <td align="right" id="cm_cost_per"><? echo number_format(($total_cm_cost/$total_order_amount)*100,4);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>18</td>
                                    <td>Total Cost</td>
                                    <td align="right" id="cost_cost"><? echo number_format($total_tot_cost,2);?></td>
                                    <td align="right" id="cost_cost_per"><? echo number_format(($total_tot_cost/$total_order_amount)*100,4);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>19</td>
                                    <td>Total Order Value</td>
                                    <td align="right" id="order_id"><? echo number_format($total_order_amount,2);?></td>
                                    <td align="right" id="order_percent"><? echo number_format(($total_order_amount/$total_order_amount)*100,4);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>20</td>
                                    <td>Profit/Loss </td>
                                    <td align="right" id="fab_profit_id"><? echo number_format($total_fabric_profit,2);?></td>
                                    <td align="right" id="profit_fab_percentage"><? echo number_format(($total_fabric_profit/$total_order_amount)*100,4);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>21</td>
                                    <td>Expected Profit <div id="expt_percent"></div></td>
                                    <td align="right" id="expected_id"><? echo number_format($total_expected_profit,2);?></td>
                                    <td align="right" id="profit_expt_fab_percentage"><? echo number_format(($total_expected_profit/$total_order_amount)*100,4);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>22</td>
                                    <td>Expt.Profit Variance </td>
                                    <td align="right" id="expt_p_variance_id"><? echo number_format($total_expected_variance,2);?></td>
                                    <td align="right" id="expt_p_percent"><? echo number_format(($total_expected_variance/$total_order_amount)*100,4);?></td>
                                </tr>
                            </table>
                        </td>
                        <!--<td colspan="5" style="min-height:900px; max-height:100%" align="center" valign="top">
                            <div id="chartdiv" style="width:580px; height:900px;" align="center"></div>
                        </td>-->
                    </tr>
                </table>
            </div>
            <br/> 
            <?
			//ob_start();
			?>
			<div>
            <h3 align="left" id="accordion_h2" style="width:5025px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel2', '')"> -Search Panel</h3>
        	<fieldset style="width:100%;" id="content_search_panel2">	
            <table width="5025">
                    <tr class="form_caption">
                        <td colspan="53" align="center"><strong>Order Wise Budget Report</strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="53" align="center"><strong><? echo $company_library[$company_name]; ?></strong></td>
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
            <table  class="rpt_table" width="5025" cellpadding="0" cellspacing="0" border="1" rules="all">
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
                        <th width="110" rowspan="2">Dealing</th>
                        <th width="70" rowspan="2"><? echo $caption; ?></th>
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
            <div style="width:5045px; max-height:400px; overflow-y:scroll" id="scroll_body">
            <table class="rpt_table" width="5025" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
			<? 
			//$approve_arr=return_library_array( "select job_no, approved from wo_pre_cost_mst", "job_no", "approved");
            $i=1; $total_order_qty=0;  $total_yarn_dyeing_cost=0; $total_yarn_cost=0; $total_order_value=0;$total_purchase_cost=0; $grand_tot_trims_cost=0; $total_fabric_dyeing_cost=0; $total_knitting_cost=0; $total_heat_setting_cost=0;$total_finishing_cost=0; $total_washing_cost=0; $fabric_dyeing_cost_dzn=0; $other_cost=0;
			$all_over_print_cost=0;$total_trim_cost=0;$total_commercial_cost=0;$total_yarn_cons=0;
		
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
			 if(str_replace("'","",$txt_season)!='')
			 {
				//$condition->season("=$txt_season"); 
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
		 	$trims= new trims($condition);
			$trims_costing_arr=$trims->getAmountArray_by_order();
			
			$fabric= new fabric($condition);
			$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
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
			$knit_cost_arr=array(1,2,3,4);
			$fabric_dyeingCost_arr=array(25,31,26,32,60,61,62,63,72,80,81,84,85,86,87,38,39,74,78,79,101,133,137,138,139,146,147,149);
			$aop_cost_arr=array(35,36,37,40);
			$fab_finish_cost_arr=array(33,34,38,63,65,66,67,68,69,70,71,72,73,75,76,77,88,82,89,90,91,92,93,94,128,129,135,136,141,143,150,151,155,156,157,145,82,89,132,144);
			$washing_cost_arr=array(140,142,148,64);
	
			 
			$print_report_format=return_field_value("format_id","lib_report_template","template_name ='$company_name' and module_id=2 and report_id=122 and is_deleted=0 and status_active=1");			
			$format_ids=explode(",",$print_report_format);
			//echo $print_report_format;
		
		//================================report setting=========================================================
			if($format_ids[0]==51)  $r_type="preCostRpt2";
			if($format_ids[0]==52)  $r_type="bomRpt";
			if($format_ids[0]==63)  $r_type="bomRpt2";
			if($format_ids[0]==142) $r_type="preCostRptBpkW";
			if($format_ids[0]==156)  $r_type="accessories_details";
			if($format_ids[0]==157)  $r_type="accessories_details2";
			if($format_ids[0]==158)  $r_type="preCostRptWoven";
			if($format_ids[0]==159) $r_type="bomRptWoven";
			if($format_ids[0]==170) $r_type="preCostRpt3";
			if($format_ids[0]==171) $r_type="preCostRpt4";
			if($format_ids[0]==192) $r_type="checkListRpt";
			if($format_ids[0]==211) $r_type="mo_sheet";
			if($format_ids[0]==307) $r_type="basic_cost";
			if($format_ids[0]==311) $r_type="bom_epm_woven1";
			if($format_ids[0]==313) $r_type="mkt_source_cost";
			if($format_ids[0]==381) $r_type="mo_sheet_2";
			if($format_ids[0]==260) $r_type="bomRptWoven_2";
			if($format_ids[0]==761) $r_type="bom_pcs_woven";
			if($format_ids[0]==403) $r_type="mo_sheet_3";
			if($format_ids[0]==770) $r_type="bom_pcs_woven2";
			if($format_ids[0]==473) $r_type="slgCostRpt";




			foreach($result_sql_budget as $row )
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
				if(str_replace("'","",$cbo_search_date)==1)
				{
					$ship_po_recv_date=change_date_format($row[csf('pub_shipment_date')]);
				}
				else if(str_replace("'","",$cbo_search_date)==2)
				{
					$ship_po_recv_date=change_date_format($row[csf('po_received_date')]);
				}
				else if(str_replace("'","",$cbo_search_date)==3)
				{
					$insert_date=explode(" ",$row[csf('insert_date')]);
					$ship_po_recv_date=change_date_format($insert_date[0]);
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
                     <td width="70"><p><a href="##" onClick="precost_job_report('','<? echo $row[csf('job_no')]; ?>','<?  echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $cost_date; ?>','<? echo $r_type; ?>');"><? echo $row[csf('job_no_prefix_num')]; ?></a></p></td>
                     <td width="100"><p><a href="##" onClick="precost_bom_pop('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')]; ?>','<?  echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $cost_date; ?>','<? echo $r_type; ?>');"><? echo $row[csf('po_number')]; ?></a></p></td>
                     <td width="100"><p><? echo $is_approve; ?></p></td>
                     <td width="80"><p><? echo $row[csf('file_no')]; ?></p></td>
                     <td width="80"><p><? echo $row[csf('grouping')]; ?></p></td>
                     <td width="100"><p><? echo  $order_status[$row[csf('is_confirmed')]]; ?></p></td>
                     <td width="110"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                     <td width="110"><div style="width:110px; word-wrap:break-word;"><? $gmts_item=''; $gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
						foreach($gmts_item_id as $item_id)
						{
							if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=", ".$garments_item[$item_id];
						}
						echo $gmts_item; ?></div></td>
                     <td width="110"><p><? echo $team_member_arr[$row[csf('dealing_marchant')]]; ?></p></td>
                     <td width="70"><p><? echo '&nbsp;'.$ship_po_recv_date; ?></p></td>
                     <td width="90" align="right"><p><? echo number_format($order_qty_pcs,2); ?></p></td>
                     <td width="90" align="right"><p><? echo number_format($row[csf('avg_unit_price')],4); ?></p></td>
                     <td width="100" align="right"><p><? echo number_format($row[csf('po_total_price')],2); ?></p></td>
                     <td width="100" align="right"><p><? echo number_format($row[csf('set_smv')],2); ?></p></td>
                     <? 
						$commercial_cost=$commercial_costing_arr[$row[csf('po_id')]];//($fabriccostArray[$row[csf('job_no')]]['comm_cost']/$dzn_qnty)*$order_qty_pcs;
						
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
						
						//$knit_cost=($kniting_cost/$dzn_qnty)*$plan_cut_qnty;
						
						$yarn_dyeing_cost=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][30]);
						if(is_infinite($yarn_dyeing_cost) || is_nan($yarn_dyeing_cost)){$yarn_dyeing_cost=0;}

						$yarn_dyeing_cost_dzn=($yarn_dyeing_cost/$plan_cut_qnty)*$dzn_qnty;
						if(is_infinite($yarn_dyeing_cost_dzn) || is_nan($yarn_dyeing_cost_dzn)){$yarn_dyeing_cost_dzn=0;}
						$fabric_dyeing_cost_dzn=($fabric_dyeing_cost/$plan_cut_qnty)*$dzn_qnty;
						if(is_infinite($fabric_dyeing_cost_dzn) || is_nan($fabric_dyeing_cost_dzn)){$fabric_dyeing_cost_dzn=0;}
						$heat_setting_cost=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][33]);
						if(is_infinite($heat_setting_cost) || is_nan($heat_setting_cost)){$heat_setting_cost=0;}
						$trim_amount= $trims_costing_arr[$row[csf('po_id')]];
						$print_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][1];
						$embroidery_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][2];
						$special_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][4];
						$wash_cost=$emblishment_costing_arr_name_wash[$row[csf('po_id')]][3];
						$other_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][5];
						$foreign=$commission_costing_arr[$row[csf('po_id')]][1];
						$local=$commission_costing_arr[$row[csf('po_id')]][2];
						$test_cost=$other_costing_arr[$row[csf('po_id')]]['lab_test'];
						$freight_cost= $other_costing_arr[$row[csf('po_id')]]['freight'];
						$inspection=$other_costing_arr[$row[csf('po_id')]]['inspection'];
						$certificate_cost=$other_costing_arr[$row[csf('po_id')]]['certificate_pre_cost'];
						$common_oh=$other_costing_arr[$row[csf('po_id')]]['common_oh'];
						$currier_cost=$other_costing_arr[$row[csf('po_id')]]['currier_pre_cost'];
						
						$cm_cost=$other_costing_arr[$row[csf('po_id')]]['cm_cost'];
						$cm_cost_dzn=($cm_cost/$order_qty_pcs)*$dzn_qnty;
						if(is_infinite($cm_cost_dzn) || is_nan($cm_cost_dzn)){$cm_cost_dzn=0;}

						if(is_infinite($cm_cost_dzn) || is_nan($cm_cost_dzn)){$cm_cost_dzn=0;}
						$total_cost=$yarn_costing+$fab_purchase+$knit_cost+$washing_cost+$all_over_cost+$yarn_dyeing_cost+$fabric_dyeing_cost+$heat_setting_cost+$fabric_finish+$trim_amount+$test_cost+$print_amount+$embroidery_amount+$special_amount+$other_amount+$wash_cost+$commercial_cost+$foreign+$local+$freight_cost+$inspection+$certificate_cost+$common_oh+$currier_cost+$cm_cost;
						
						$total_profit=$order_value-$total_cost;
						$total_profit_percentage2=$total_profit/$order_value*100; 
						$expected_profit=$asking_profit_arr[$row[csf('company_name')]]['asking_profit']*$order_value/100;
						if(is_infinite($expected_profit) || is_nan($expected_profit)){$expected_profit=0;}
						$expect_variance=$total_profit-$expected_profit;
						
						if($fabric_dyeing_cost<=0 && $yarn_dyeing_cost<=0) $color_fab="red"; else $color_fab="";	
						if($yarn_costing<=0) $color_yarn="red"; else $color_yarn="";	
						if($knit_cost<=0) $color_knit="red"; else $color_knit="";	
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
                     <td width="110" align="right"><? echo number_format($yarn_dyeing_cost ,2); ?></td>
                     <td width="120" align="right"><? echo number_format($fabric_dyeing_cost_dzn,2); ?></td>
                     <td width="100" align="right" bgcolor="<? echo $color_fab; ?>"><a href="##" onClick="generate_precost_fab_dyeing_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $fab_source_id; ?>','fab_dyeing_detail')"><? echo number_format($fabric_dyeing_cost,2); ?></a></td>
                     <td width="90" align="right"><? echo number_format($heat_setting_cost,2); ?></td>
                     <td width="100" align="right" ><a href="##" onClick="generate_precost_fab_finishing_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','fab_finishing_detail')"><? echo number_format($fabric_finish,2); ?></a></td>
                     <td width="90" align="right"><a href="##" onClick="generate_precost_fab_finishing_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','fab_washing_detail')"><? echo number_format($washing_cost,2); ?></a></td>
                     <td width="90" align="right"><a href="##" onClick="generate_precost_fab_all_over_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','fab_all_over_detail')"><? echo number_format($all_over_cost,2); ?></a></td>
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
                     <td width="120" align="right"><? echo number_format($cm_cost_dzn,2);?></td>
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
				$total_common_oh+=$common_oh;
				$total_currier_cost+=$currier_cost;
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
            <table class="tbl_bottom" width="5025" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
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
                    <td width="70">Total</td>
                    <td width="90" align="right" id="total_order_qnty"><? echo number_format($total_order_qty,2); ?></td>
                    <td width="90">&nbsp;</td>
                    <td width="100" align="right" id="total_order_amount2"><? echo number_format($total_order_amount,2); ?></td>
                    <td width="100">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="80" align="right" id="total_yarn_cost2"><? echo number_format($total_yarn_cost,2); ?></td>
                    <td width="80" align="right" id="total_yarn_cost_per"><? echo number_format($total_yarn_cost_percentage,2); ?></td>
                    <td width="100" align="right" id="total_purchase_cost"><? echo number_format($total_purchase_cost,2); ?></td>
                    <td width="80">&nbsp;</td>
                    <td width="80" align="right" id="total_knitting_cost"><? echo number_format($total_knitting_cost,2); ?></td>
                    <td width="100">&nbsp;</td>
                    <td width="110" align="right" id="total_yarn_dyeing_cost"><? echo number_format($total_yarn_dyeing_cost,2); ?></td>
                    <td width="120">&nbsp;</td>
                    <td width="100" align="right" id="total_fabric_dyeing_cost4"><? echo number_format($total_fabric_dyeing_cost,2); ?></td>
                    <td width="90" align="right" id="total_heat_setting_cost"><? echo number_format($total_heat_setting_cost,2); ?></td>		
                    <td width="100" align="right" id="total_finishing_cost"><? echo number_format($total_finishing_cost,2); ?></td>
                    <td width="90" align="right" id="total_washing_cost"><? echo number_format($total_washing_cost,2); ?></td>
                    <td width="90" align="right" id="all_over_print_cost"><? echo number_format($all_over_print_cost,2); ?></td>
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
                    <td width="100" align="right" id="total_expected_profit"><? echo number_format($total_expected_profit,2);?></td>
                    <td width="100" align="right" id="">&nbsp;<? //echo number_format($total_expected_profit,2);?></td>
                    <td width="80"align="right" id="total_expected_variance"><? echo number_format($total_expected_variance,2);?></td>
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
            <a id="displayText" href="javascript:toggle();">Show Yarn Summary</a>
            
            <div style="width:600px; display:none" id="yarn_summary" >
                <div id="data_panel2" align="center" style="width:500px">
                     <input type="button" value="Print Preview" class="formbutton" style="width:100px" name="print" id="print" onClick="new_window1(1)" />
                 </div>
                <table width="500">
                    <tr class="form_caption">
                        <td colspan="6" align="center"><strong>Yarn Cost Summary </strong></td>
                    </tr>
                </table>
                <table class="rpt_table" width="500" cellpadding="0" cellspacing="0" border="1" rules="all" style="display:none">
                    <thead>
                        <th width="30">SL</th>
                        <th width="80">Yarn Count</th>
                        <th width="120">Type</th>
                        <th width="120">Req. Qnty</th>
                        <th width="80">Avg. rate</th>
                        <th>Amount</th>
                    </thead>
					<?
					//not use it
                    $s=1;// $tot_yarn_req_qnty=0; $tot_yarn_req_amnt=0;
                    foreach($yarn_desc_array as $key=>$value)
                    {
						if($s%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$yarn_desc=explode("**",$key);
						
						$tot_yarn_req_qnty+=$yarn_desc_array[$key]['qnty']; 
						$tot_yarn_req_amnt+=$yarn_desc_array[$key]['amnt'];
						?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr3_<? echo $s; ?>','<? echo $bgcolor; ?>')" id="tr3_<? echo $s;?>">
                            <td><? echo $s; ?></td>
                            <td align="center"><? echo $yarn_desc[0]; ?></td>
                            <td><? echo $yarn_desc[1]; ?></td>
                            <td align="right"><? echo number_format($yarn_desc_array[$key]['qnty'],2); ?></td>
                            <td align="right"><? echo number_format($yarn_desc_array[$key]['amnt']/$yarn_desc_array[$key]['qnty'],2); ?></td>
                            <td align="right"><? echo number_format($yarn_desc_array[$key]['amnt'],2); ?></td>
						</tr>
						<?	
						$s++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="3" align="right">Total</th>
                        <th align="right"><? echo number_format($tot_yarn_req_qnty,2); ?></th>
                        <th align="right"><? echo number_format($tot_yarn_req_amnt/$tot_yarn_req_qnty,2); ?></th>
                        <th align="right"><? echo number_format($tot_yarn_req_amnt,2); ?></th>
                    </tfoot>
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
			  </div>
            </div>
            <?
		}
	}
	else if($report_type==3) //Country Ship Date
	{
		if($template==1)
		{
			
			$style1="#E9F3FF"; 
			$style="#FFFFFF";
			//echo 'Aziz';die;
			  $asking_profit_arr=array();
			
			$asking_profit=sql_select("select id,company_id,asking_profit,max_profit from lib_standard_cm_entry where status_active=1 and is_deleted=0 $date_max_profit");
			foreach($asking_profit as $ask_row )
			{
				$asking_profit_arr[$ask_row[csf('company_id')]]['asking_profit']=$ask_row[csf('asking_profit')];
				$asking_profit_arr[$ask_row[csf('company_id')]]['max_profit']=$ask_row[csf('max_profit')];
			} //var_dump($asking_profit_arr);
			
			$financial_para=array();
			$sql_std_para=sql_select("select interest_expense,income_tax,cost_per_minute,applying_period_date as from_period_date from lib_standard_cm_entry where company_id=$company_name and status_active=1 and is_deleted=0  order by id desc");	
			foreach($sql_std_para as $row)
			{
				$period_date=date("m-Y", strtotime($row[csf('from_period_date')]));
				$financial_para[$period_date]['interest_expense']=$row[csf('interest_expense')];
				$financial_para[$period_date]['income_tax']=$row[csf('income_tax')];
				//$financial_para[csf('cost_per_minute')]=$sql_std_row[csf('cost_per_minute')];
			}
			//print_r($financial_para);
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
						text.innerHTML = "Show Summary & Yarn Cost";
					}
					else 
					{
						ele.style.display = "block";
						text.innerHTML = "Hide Summary & Yarn Cost";
					}
				} 
			</script>
			<div style="width:5585px;">
			<div style="width:900px;" align="left">
           </div>
            <h3 align="left" id="accordion_h2" style="width:5578px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel2', '')"> -Search Panel</h3>
            <fieldset style="width:100%;" id="content_search_panel2">	
                <table width="5575">
                    <tr class="form_caption">
                        <td colspan="52" align="center"><strong>Order Wise Budget Report 2</strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="52" align="center"><strong><? echo $company_library[$company_name]; ?></strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td align="left"><strong>Details Report </strong></td>
                    </tr>
                </table>
                <? //$asking_profit_head=$asking_profit_arr[$company_name]['asking_profit']; 
				if(str_replace("'","",$cbo_search_date)==1) 
				{
					$caption_head="Ship. Date";
					$qty_type_cap="Country Order Qty";
				}
				else if(str_replace("'","",$cbo_search_date)==2) 
				{
					$caption_head="PO Recv. Date";
					$qty_type_cap="Order Qty";
				}
				else 
				{
					$caption_head="PO Insert Date";
					$qty_type_cap="Order Qty";
				}
				?>
                <table class="rpt_table" width="5565" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr>
                            <th width="40" rowspan="2">SL</th>
                            <th width="70" rowspan="2">Buyer</th>
                            <th width="70" rowspan="2">Job No</th>
                            <th width="100" rowspan="2">Order No</th>
                            <th width="80" rowspan="2">File No</th>
                            <th width="80" rowspan="2">Internal Ref: No</th>
                            <th width="100" rowspan="2">Order Status</th>
                            <th width="110" rowspan="2">Style</th>
                            <th width="110" rowspan="2">Item Name</th>
                            <th width="110" rowspan="2">Dealing</th>
                            <th width="70" rowspan="2"><? echo $caption_head; ?></th>
                            <th width="90" rowspan="2">Plan Cut Qty</th>
                            <th width="90" rowspan="2">Order Qty</th>
                            <th width="70" rowspan="2">UOM</th>
                            <th width="90" rowspan="2">Avg Unit Price</th>
                            <th width="100" rowspan="2">Gross FOB Value</th>
                            <th colspan="3">Commission</th>
                            <th width="100" rowspan="2">Net FOB Value</th>
                            <th width="100" rowspan="2">Less:Cost of Materials & Services</th>
                            <th colspan="3">Yarn Cost</th>
                            <th width="80" rowspan="2" >Fabric Purchase</th>
                            <th width="100" rowspan="2">Conversion Cost </th>
                            <th colspan="9">Conversion Cost(Dyeing & Finishing)</th>
                            <th width="100" rowspan="2">Trims Cost Per DZN</th>
                            <th width="100" rowspan="2">TT Trims Cost</th>
                            <th width="80" rowspan="2">TT Embel. Cost</th>
                            <th colspan="3">Embel. Cost</th>
                            <th width="120" rowspan="2">Other Direct Expenses</th>
                            <th width="120" rowspan="2">Contribution/Value Additions</th>
                            <th width="100" rowspan="2">Contribution/Value Additions%</th>
                            <th width="120" rowspan="2">CM Cost</th>
                            <th width="100" rowspan="2">Gross Profit/Loss</th>
                            <th width="100" rowspan="2">Gross Profit%</th>
                            <th width="100" rowspan="2">Commercial Cost</th>
                            <th width="100" rowspan="2">Operating Expenses</th>
                            <th width="100" rowspan="2">Operating Profit/Loss</th>
                            <th width="100" rowspan="2">Operating Profit/Loss %</th>
                            <th width="100" rowspan="2">Depreciation & Amortization</th>
                            <th width="100" rowspan="2">Interest</th>
                            <th width="100" rowspan="2">Income Tax(<? echo $financial_para['income_tax'].'%' ?>)</th>
                            <th width="100" rowspan="2">Net Profit</th>
                            <th width="" rowspan="2">Net Profit %</th>
                            
                        </tr>
                        <tr>
                            <th width="80">Foreign</th>
                            <th width="80">Local</th>
                            <th width="60">%</th>
                            <th width="120">Yarn Cost Per DZN</th>
                            <th width="120">Total Yarn Cost</th>
                            <th width="120">Yarn Cost%</th>
                            <th width="120">Knitting Cost Per DZN</th>
                            <th width="120">TT Knitting Cost</th>
                            <th width="120">Dyeing Cost Per DZN</th>
                            <th width="120">TT Dyeing Cost</th>
                            <th width="120">Yarn Dyed Cost</th>
                            <th width="120">AOP Cost</th>
                            <th width="120">Heat Setting</th>
                            <th width="120">Finishing Cost</th>
                            <th width="120">Washing Cost</th>
                            <th width="80">Printing</th>
                            <th width="85">Embroidery</th>
                            <th width="80" title="Special Works+Others">Special Works</th>
                        </tr>
                    </thead>
                </table>
                <div style="width:5585px; max-height:400px; overflow-y:scroll" id="scroll_body">
             	<table class="rpt_table" width="5565" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
                <? 
				if(str_replace("'","",$cbo_search_date)==1)
				{
					$contry_ship_qty_arr=array();
				}
				
                $i=1; 
				$total_order_qty=0; $total_foreign_cost=0; $total_yarn_dyeing_cost=0; $total_fabric_dyeing_cost_dzn=0; $total_yarn_cost=0; $grand_total_gross_fob_value=0;$total_purchase_cost=0; $total_tot_trim_cost=0; $total_trim_cost_per_dzn=0; $total_fabric_dyeing_cost=0; $total_knitting_cost=0; $total_heat_setting_cost=0; $total_finishing_cost=0; $total_washing_cost=0; $fabric_dyeing_cost_dzn=0; $total_cm_cost=0; $grand_total_gross_fob_plancut_value=0; $total_local_cost=0; $total_net_fob_value=0; $total_contribution_value=0; $total_gross_profit=0; $total_operating_profit=0; $grand_total_net_profit=0; $total_yarn_amount_per_dzn=0; $total_fabric_finish=0; $total_washing_cost=0; $total_embell_cost=0; $total_yarn_dyed_cost=0; $total_all_over_cost=0; $total_trim_cost=0; $total_commercial_cost=0; $grand_total_net_profit=0; $total_interet=0; $total_depreciation_amortization=0; $total_commercial_cost=0; $total_other_direct_expenses=0; $total_print_cost=0; $total_embroidery_cost=0; $total_wash_cost=0; $total_special_cost=0; $total_cost_of_material_service=0; $cost_of_material_service=0;$total_purchase_cost=0;
				if(str_replace("'","",$cbo_search_date)==1)
				{
					if($db_type==0) $all_country_id="group_concat(c.country_id)";
					else if($db_type==2) $all_country_id="LISTAGG(c.country_id, ',') WITHIN GROUP (ORDER BY c.country_id)";
					
					if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="") $country_date_cond="and c.country_ship_date between '$start_date' and '$end_date'"; else $country_date_cond="";
					
					$sql="select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.insert_date, a.order_uom, b.is_confirmed, a.agent_name, a.avg_unit_price, a.dealing_marchant, a.gmts_item_id, a.total_set_qnty as ratio,b.id as po_id, b.po_number, b.unit_price, b.grouping, b.file_no, c.country_ship_date as pub_shipment_date, $all_country_id as country_id, sum(c.plan_cut_qnty) as plan_cut, sum(c.order_quantity) as po_quantity, sum(c.order_total) as po_total_price
					
					
					from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,wo_pre_cost_mst d where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.job_no=d.job_no and b.job_no_mst=d.job_no and c.job_no_mst=d.job_no  and d.entry_from=158  and a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $country_date_cond $buyer_id_cond  $year_cond $job_no_cond $order_id_cond_trans $order_no_cond $order_status_cond $season_cond $internal_ref_cond $file_no_cond group by a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.insert_date, a.order_uom, b.is_confirmed, a.agent_name, a.avg_unit_price, a.dealing_marchant, a.gmts_item_id, a.total_set_qnty, b.id, b.po_number, b.unit_price, b.grouping, b.file_no, c.country_ship_date";
				}
				else
				{
					$sql="select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.insert_date, a.order_uom, b.is_confirmed, a.agent_name, a.avg_unit_price, a.dealing_marchant, a.gmts_item_id, a.total_set_qnty as ratio, b.plan_cut,b.id as po_id, b.po_number, b.pub_shipment_date,b.po_received_date, b.po_quantity, b.unit_price, b.grouping, b.file_no, b.po_total_price from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst d where a.job_no=b.job_no_mst and b.job_no_mst=d.job_no  and a.job_no=d.job_no and d.entry_from=158  and a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond $buyer_id_cond  $year_cond $job_no_cond $order_id_cond_trans $order_no_cond $order_status_cond $season_cond $internal_ref_cond $file_no_cond";
				}


			$result=sql_select($sql);
			$tot_rows=count($result);

		
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
				  $condition->country_ship_date(" between '$start_date' and '$end_date'");
			 }
			 if(str_replace("'","",$cbo_search_date) ==2 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
			 {
				 $condition->po_received_date(" between '$start_date' and '$end_date'");
				 //and b.po_received_date between '$start_date' and '$end_date' 
				// echo 'FFGG';
			 }
			
			 if(str_replace("'","",$cbo_search_date)==3 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
			 {
				
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
			
			
			if(str_replace("'","",$cbo_search_date)==1)
			{
				$yarn= new yarn($condition);
				$yarn_costing_arr=$yarn->getOrderAndCountryWiseYarnAmountArray();
				$yarn= new yarn($condition);
				$yarn_requerd_arr=$yarn->getOrderAndCountryWiseYarnQtyArray();
				
				$yarn= new yarn($condition);
				$yarn_des_data=$yarn->getOrdeCountryshipdaterCountCompositionPercentAndTypeWiseYarnQtyAndAmountArray();
				$fabric= new fabric($condition);
				$fabric_costing_arr=$fabric->getAmountArray_by_OrderAndCountry_knitAndwoven_greyAndfinish();
				$conversion= new conversion($condition);
				$conversion_costing_arr=$conversion->getAmountArray_by_orderAndCountry();
				$conversion= new conversion($condition);
				$conversion_costing_arr_process=$conversion->getAmountArray_by_orderCountryAndProcess();
				$emblishment= new emblishment($condition);
				$emblishment_costing_arr=$emblishment->getAmountArray_by_orderAndCountry();
				$emblishment= new emblishment($condition);
				$emblishment_costing_arr_name=$emblishment->getAmountArray_by_orderCountryAndEmbname();
				$wash= new wash($condition);
				$wash_costing_arr=$wash->getAmountArray_by_orderAndCountry();
				$wash= new wash($condition);
				$wash_costing_arr_name=$wash->getAmountArray_by_orderCountryAndEmbname();
				$commission= new commision($condition);
				$commission_costing_arr=$commission->getAmountArray_by_orderCountryAndItemid();
				$commercial= new commercial($condition);
				$commercial_costing_arr=$commercial->getAmountArray_by_orderAndCountry();
				$other= new other($condition);
				$other_costing_arr=$other->getAmountArray_by_orderAndCountry();
				$trims= new trims($condition);
				$trims_costing_arr=$trims->getAmountArray_by_orderAndCountry();
			}
			else
			{
				$yarn= new yarn($condition);
				$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
				$yarn= new yarn($condition);
				$yarn_requerd_arr=$yarn->getOrderWiseYarnQtyArray();
				$yarn= new yarn($condition);
				$yarn_des_data=$yarn->getCountCompositionAndTypeWiseYarnQtyAndAmountArray();

				$fabric= new fabric($condition);
				$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
				$conversion= new conversion($condition);
				$conversion_costing_arr=$conversion->getAmountArray_by_order();
				$conversion= new conversion($condition);
				$conversion_costing_arr_process=$conversion->getAmountArray_by_orderAndProcess();
				
				$emblishment_costing_arr=$emblishment->getAmountArray_by_order();
				$emblishment_costing_arr_name=$emblishment->getAmountArray_by_orderAndEmbname();
				$wash= new wash($condition);
				$wash_costing_arr=$wash->getAmountArray_by_order();
				$wash= new wash($condition);
				$wash_costing_arr_name=$wash->getAmountArray_by_orderAndEmbname();
				//$wash->unsetDataArray();
				$commission= new commision($condition);
				$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
				//$commission->unsetDataArray();
				$commercial= new commercial($condition);
				$commercial_costing_arr=$commercial->getAmountArray_by_order();
				//$commercial->unsetDataArray();
				$other= new other($condition);
				$other_costing_arr=$other->getAmountArray_by_order();
				//$other->unsetDataArray();
				$trims= new trims($condition);
				$trims_costing_arr=$trims->getAmountArray_by_order();
				//$trims->unsetDataArray();
			}
			
				$knit_cost_arr=array(1,2,3,4);
				$fabric_dyeingCost_arr=array(25,31,26,32,60,61,62,63,72,80,81,84,85,86,87,38,39,74,78,79,101,133,137,138,139,146,147,149);
				$aop_cost_arr=array(35,36,37,40);
				$fab_finish_cost_arr=array(33,34,38,63,65,66,67,68,69,70,71,72,73,75,76,77,88,82,89,90,91,92,93,94,128,129,135,136,141,143,150,151,155,156,157,145,82,89,132,144);
				$washing_cost_arr=array(140,142,148,64);
			
				foreach($result as $row )
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					$dzn_qnty=0;
					$costing_per_id=$costing_per_arr[$row[csf('job_no')]];
					if($costing_per_id==1) $dzn_qnty=12;
					else if($costing_per_id==3) $dzn_qnty=12*2;
					else if($costing_per_id==4) $dzn_qnty=12*3;
					else if($costing_per_id==5) $dzn_qnty=12*4;
					else $dzn_qnty=1;
						
					$dzn_qnty_yarn=$dzn_qnty*$row[csf('ratio')];
					$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
					
					
					$gross_fob_value=$row[csf('po_total_price')];
					$gross_fob_plancut_value=$plan_cut_qnty*$row[csf('unit_price')];
					if(str_replace("'","",$cbo_search_date)==1)
					{
						$order_qty_pcs=$row[csf('po_quantity')];	
						$plan_cut_qnty=$row[csf('plan_cut')];
					}
					else
					{
						$order_qty_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
						$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];	
					}
					
					$costing_date=$costing_per_date_arr[$row[csf('job_no')]];
					if(str_replace("'","",$cbo_search_date)==1)
					{
						 
						$fab_purchase_detail="country_fab_purchase_detail";
						$country_id_all=array_unique(explode(",",$row[csf('country_id')]));
						$foreign=0; $local=0; $yarn_costing=0; $conversion_cost=0; $trim_cost_per_dzn=0; $trim_cost=0; $embell_cost=0; $freight_cost=0; $inspection=0; $certificate_cost=0; $currier_cost=0; $lab_test_cost=0; $wash_cost=0; $knit_cost=0;$yarn_cons=0;
						$fabric_dyeing_cost=0; $yarn_dyed_cost=0; $all_over_cost=0;  $yarn_req_data=0;
						$fabric_finish=0; $washing_cost=0; 
						$print_cost=0; $embroidery_cost=0; $special_cost_pre=0; $other_cost=0; $cm_cost=0; $commercial_cost=0; $operating_expense=0; $depreciation_amortization=0;
						$heat_setting_cost=0;
						$yarn_descrip_data=$yarn_des_data[$row[csf('po_id')]][$row[csf('pub_shipment_date')]];
						//echo $cbo_search_date.'fsdd';
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
						foreach($country_id_all as $id_country)
						{
							$foreign+=$commission_costing_arr[$row[csf('po_id')]][$id_country][1];
							$local+=$commission_costing_arr[$row[csf('po_id')]][$id_country][2];
							$yarn_costing+=$yarn_costing_arr[$row[csf('po_id')]][$id_country];
							$yarn_req_data+=$yarn_requerd_arr[$row[csf('po_id')]][$id_country];
							$yarn_cons+=($yarn_requerd_arr[$row[csf('po_id')]][$id_country]/$plan_cut_qnty)*12;
							
							$fab_purchase_knit=array_sum($fabric_costing_arr['knit']['grey'][$row[csf('po_id')]][$id_country]);
						    $fab_purchase_woven=array_sum($fabric_costing_arr['woven']['grey'][$row[csf('po_id')]][$id_country]);
						    $fab_purchase=$fab_purchase_knit+$fab_purchase_woven;
							
							$conversion_cost+=array_sum($conversion_costing_arr[$row[csf('po_id')]][$id_country]);
							$trim_cost+=$trims_costing_arr[$row[csf('po_id')]][$id_country];
							$trim_cost_per_dzn+=($trim_cost/$order_qty_pcs)*12;//$trims_costing_arr[$row[csf('po_id')]][$id_country]['trims_cost'];
							
							$embell_cost+=$emblishment_costing_arr[$row[csf('po_id')]][$id_country];
							
							$freight_cost+= $other_costing_arr[$row[csf('po_id')]][$id_country]['freight'];
							$inspection+=$other_costing_arr[$row[csf('po_id')]][$id_country]['inspection'];
							$certificate_cost+=$other_costing_arr[$row[csf('po_id')]][$id_country]['certificate_pre_cost'];
							$currier_cost+=$other_costing_arr[$row[csf('po_id')]][$id_country]['currier_pre_cost'];
							$lab_test_cost+=$other_costing_arr[$row[csf('po_id')]][$id_country]['lab_test'];
							
							$wash_cost+=$wash_costing_arr_name[$row[csf('po_id')]][$id_country][3];
							$knit_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$id_country][1])+array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$id_country][2])+array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$id_country][3]);
							foreach($fabric_dyeingCost_arr as $dye_id)
							{
								$fabric_dyeing_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$id_country][$dye_id]);
							}
							
							$yarn_dyed_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$id_country][30]);
							foreach ($aop_cost_arr as $aop_id)
							{
								$all_over_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$id_country][$aop_id]);
							}
							
							$heat_setting_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$id_country][33]); 
							foreach($fab_finish_cost_arr as $fab_fin_id)
							{
								$fabric_finish+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$id_country][$fab_fin_id]);
							}
							foreach($washing_cost_arr as $washing_id)
							{
								$washing_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$id_country][$washing_id]);
							}
							
							$print_cost+=$emblishment_costing_arr_name[$row[csf('po_id')]][$id_country][1];
							$embroidery_cost+=$emblishment_costing_arr_name[$row[csf('po_id')]][$id_country][2];
							$special_cost_pre+=$emblishment_costing_arr_name[$row[csf('po_id')]][$id_country][4];
							$other_cost+=$emblishment_costing_arr_name[$row[csf('po_id')]][$id_country][5];
							
							$cm_cost+=$other_costing_arr[$row[csf('po_id')]][$id_country]['cm_cost'];
							$commercial_cost+=$commercial_costing_arr[$row[csf('po_id')]][$id_country];
							$operating_expense+=$other_costing_arr[$row[csf('po_id')]][$id_country]['common_oh'];
							$depreciation_amortization+=$other_costing_arr[$row[csf('po_id')]][$id_country]['depr_amor_pre_cost'];
						}
					}
					else
					{
						$fab_purchase_detail="fab_purchase_detail";
						$foreign=$commission_costing_arr[$row[csf('po_id')]][1];
						$local=$commission_costing_arr[$row[csf('po_id')]][2];
						$yarn_costing=$yarn_costing_arr[$row[csf('po_id')]];
						$yarn_cons=($yarn_requerd_arr[$row[csf('po_id')]]/$plan_cut_qnty)*12;
						$fab_purchase_knit=array_sum($fabric_costing_arr['knit']['grey'][$row[csf('po_id')]]);
						$fab_purchase_woven=array_sum($fabric_costing_arr['woven']['grey'][$row[csf('po_id')]]);
						$fab_purchase=$fab_purchase_knit+$fab_purchase_woven;
						
						$conversion_cost=array_sum($conversion_costing_arr[$row[csf('po_id')]]);
						
						$trim_cost=$trims_costing_arr[$row[csf('po_id')]];
						$trim_cost_per_dzn=($trim_cost/$order_qty_pcs)*12;//$fabriccostArray[$row[csf('job_no')]]['trims_cost'];
						
						$embell_cost=$emblishment_costing_arr[$row[csf('po_id')]];
						
						$freight_cost= $other_costing_arr[$row[csf('po_id')]]['freight'];
						$inspection=$other_costing_arr[$row[csf('po_id')]]['inspection'];
						$certificate_cost=$other_costing_arr[$row[csf('po_id')]]['certificate_pre_cost'];
						$currier_cost=$other_costing_arr[$row[csf('po_id')]]['currier_pre_cost'];
						$lab_test_cost=$other_costing_arr[$row[csf('po_id')]]['lab_test'];
						
						$wash_cost=$wash_costing_arr_name[$row[csf('po_id')]][3];
						$knit_cost=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][1])+array_sum($conversion_costing_arr_process[$row[csf('po_id')]][2])+array_sum($conversion_costing_arr_process[$row[csf('po_id')]][3]);
						$fabric_dyeing_cost=0;
						foreach($fabric_dyeingCost_arr as $dye_id)
						{
							$fabric_dyeing_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$dye_id]);
						}
						
						$yarn_dyed_cost=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][30]);
						$all_over_cost=0;
						foreach ($aop_cost_arr as $aop_id)
						{
							$all_over_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$id_country][$aop_id]);
						}
						
						$heat_setting_cost=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][33]);
						$fabric_finish=0;
						foreach ($fab_finish_cost_arr as $fab_fin_id)
						{
							$fabric_finish+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$id_country][$fab_fin_id]);
						}
						$washing_cost=0;
						foreach($washing_cost_arr as $washing_id)
						{
							$washing_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$id_country][$washing_id]);
						}
						
						$print_cost=$emblishment_costing_arr_name[$row[csf('po_id')]][1];
						$embroidery_cost=$emblishment_costing_arr_name[$row[csf('po_id')]][2];
						$special_cost_pre=$emblishment_costing_arr_name[$row[csf('po_id')]][4];
						$other_cost=$emblishment_costing_arr_name[$row[csf('po_id')]][5];
						
						$cm_cost=$other_costing_arr[$row[csf('po_id')]]['cm_cost'];
						$commercial_cost=$commercial_costing_arr[$row[csf('po_id')]];
						$operating_expense=$other_costing_arr[$row[csf('po_id')]]['common_oh'];
						$depreciation_amortization=$other_costing_arr[$row[csf('po_id')]]['depr_amor_pre_cost'];
					}
					if(str_replace("'","",$cbo_search_date)==1)
					{
						$ship_po_recv_date=$row[csf('pub_shipment_date')];
					}
					else if(str_replace("'","",$cbo_search_date)==2)
					{
						$ship_po_recv_date=$row[csf('po_received_date')];
					}
					else if(str_replace("'","",$cbo_search_date)==3)
					{
						$insert_date=explode(" ",$row[csf('insert_date')]);
						$ship_po_recv_date=$insert_date[0];
					}
					
					$commision_cost=$foreign+$local;
					$commission_percent=($commision_cost/$gross_fob_value)*100;
					$net_fob_value=$gross_fob_value-$commision_cost;
					$yarn_cost_percent=($yarn_costing/$gross_fob_value)*100;
					//echo $dzn_qnty;
					$other_direct_expenses=$freight_cost+$inspection+$certificate_cost+$currier_cost+$lab_test_cost+$wash_cost;
					//echo $freight_cost."+".$inspection."+".$certificate_cost."+".$currier_cost."+".$lab_test_cost."+".$wash_cost;
					$cost_of_material_service=$yarn_costing+$conversion_cost+$trim_cost+$embell_cost+$other_direct_expenses+$fab_purchase;
					$knit_cost_dzn=($knit_cost/$plan_cut_qnty)*12;
					
					$fabric_dyeing_cost_dzn=($fabric_dyeing_cost/$plan_cut_qnty)*12;
					$yarn_dyeing_cost_dzn=($yarn_dyed_cost/$plan_cut_qnty)*12;
					
					$special_cost=$special_cost_pre+$other_cost;
					$contribution_value=$net_fob_value-$cost_of_material_service;
					$contribution_value_percent=$contribution_value/$gross_fob_value*100;
					$gross_profit=$contribution_value-$cm_cost;
					$gross_profit_percentage=($gross_profit/$gross_fob_value)*100;
					$operating_profit=$gross_profit-($commercial_cost+$operating_expense);
                    $operating_profit_percent=($operating_profit/$gross_fob_value)*100;
					
					$interest_expense=$net_fob_value*$financial_para[$costing_date]['interest_expense']/100;
					$income_tax=$net_fob_value*$financial_para[$costing_date]['income_tax']/100;
					//echo $financial_para[$costing_date]['interest_expense'].'=='.$financial_para[$costing_date]['income_tax'];
					$net_profit=$operating_profit-($depreciation_amortization+$interest_expense+$income_tax);
                    $net_profit_percent=$net_profit/$gross_fob_value*100;
					
					?>
					<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="70"><p><? echo $buyer_library[$row[csf('buyer_name')]] ?></p></td>
                        <td width="70"><p><? echo $row[csf('job_no_prefix_num')];  ?></p></td>
                        <td width="100" style="word-break:break-all"><p><a href="#" onClick="precost_bom_pop('<?  echo $row[csf('po_id')]; ?>','<?  echo $row[csf('job_no')]; ?>','<?  echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>');"><? echo $row[csf('po_number')]; ?></a></p></td>
                        <td width="80"><p><? echo  $row[csf('file_no')]; ?></p></td>
                        <td width="80"><p><? echo  $row[csf('grouping')]; ?></p></td>
                        <td width="100"><p><? echo  $order_status[$row[csf('is_confirmed')]]; ?></p></td>
                        <td width="110" style="word-break:break-all"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                        <td width="110" style="word-break:break-all"><p><? $gmts_item=''; $gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
							foreach($gmts_item_id as $item_id)
							{
								if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
							}
							echo $gmts_item; ?> </p></td>
                        <td width="110"><p><? echo $team_member_arr[$row[csf('dealing_marchant')]]; ?></p></td>
                        <? if(str_replace("'","",$cbo_search_date)==1){ ?>
                        <td width="70"><a href="##" onClick="country_order_dtls('<? echo $row[csf('po_id')]; ?>','<? echo '&nbsp;'.$ship_po_recv_date; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('job_no')]; ?>','country_order_dtls_popup')"><p><? echo '&nbsp;'.change_date_format($ship_po_recv_date); ?></p></a></td>
                        <? } else { ?>
                        <td width="70"><p><? echo '&nbsp;'.$ship_po_recv_date; ?></p></td>
                        <? } ?>
                        <td width="90" align="right"><p><? echo number_format($plan_cut_qnty,2); ?></p></td>
                        <td width="90" align="right"><p><? echo number_format($order_qty_pcs,2); ?></p></td>
                        <td width="70" align="right"><p><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></p></td>
                        <td width="90" align="right"><p><? echo number_format($row[csf('unit_price')],4); ?></p></td>
                        <td width="100" align="right"><p><? echo number_format($gross_fob_value,2); ?></p></td>
                        <td width="80" align="right"><? echo number_format($foreign,2); ?></td>
                        <td width="80" align="right"><? echo number_format($local,2); ?></td>
                        <td width="60" align="right"><? echo number_format($commission_percent,4); ?></td>
                        <td width="100" align="right"><? echo number_format($net_fob_value,2); ?></td>
                        <td width="100" align="right"><? echo number_format($cost_of_material_service,2); ?></td>
                        <td width="120" align="right"><? $yarn_amount=($yarn_costing/$plan_cut_qnty)*12; echo number_format($yarn_amount,2); ?></td>
                        <td width="120" align="right" title="Yarn Cost Like BOM"><? echo number_format($yarn_costing,2); ?></td>
                        <td width="120" align="right"><? echo number_format($yarn_cost_percent,2); ?></td>
                        <td width="80" align="right"><a href="##" onClick="generate_precost_fab_purchase_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $fab_source_id; ?>','<? echo $fab_purchase_detail;?>')"><? echo number_format($fab_purchase,2); ?></a></td>
                        <td width="100" align="right"><? echo number_format($conversion_cost,2); ?> </td>
                        <td width="120" align="right"><? echo number_format($knit_cost_dzn,4); ?></td>
                        <td width="120" align="right"><? echo number_format($knit_cost,2); ?></td>
                        <td width="120" align="right"><? echo number_format($fabric_dyeing_cost_dzn,4); ?></td>
                        <td width="120" align="right"><? echo number_format($fabric_dyeing_cost,2); ?> </td>
                        <td width="120" align="right"><? echo number_format($yarn_dyed_cost,2); ?></td>
                        <td width="120" align="right"><? echo number_format($all_over_cost,2); ?></td>
                        <td width="120" align="right"><? echo number_format($heat_setting_cost,2); ?></td>
                        <td width="120" align="right"><? echo number_format($fabric_finish,2);?> </td>
                        <td width="120" align="right"><? echo number_format($washing_cost,2) ?></td>
                        <td width="100" align="right"><? echo number_format($trim_cost_per_dzn,2); ?> </td>
                        <td width="100" align="right"><a href="##" onClick="country_order_dtls_trim('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('country_id')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('job_no')]; ?>','country_trims_dtls_popup')"><? echo number_format($trim_cost,2); ?></a><? //echo number_format($trim_cost,2); ?></td>
                        <td width="80" align="right"><? echo number_format($embell_cost,2); ?></td>
                        <td width="80" align="right"><? echo number_format($print_cost,2); ?></td>
                        <td width="80" align="right"><? echo number_format($embroidery_cost,2); ?> </td>
                        <td width="80" align="right"><? echo number_format($special_cost,2); ?></td>
                        <td width="120" align="right"><? echo number_format($other_direct_expenses,2); ?></td>
                        <td width="120" align="right"><? echo number_format($contribution_value,2); ?> </td>
                        <td width="100" align="right"><? echo number_format($contribution_value_percent,2); ?></td>
                        <td width="120" align="right" title="<? echo "CM Cost"; ?>"><? echo number_format($cm_cost,2);?> </td>
                        <td width="100" align="right" title="<? echo "Gross Profit"; ?>" ><? echo number_format($gross_profit,2); ?></td>
                        <td width="100" align="right" title="<? echo "Gross Profit %"; ?>"><? echo number_format($gross_profit_percentage,2); ?> </td>
                        <td width="100" align="right"><? echo number_format($commercial_cost,2); ?></td>
                        <td width="100" align="right" title="<? echo "Operating Cost"; ?>"><? echo  number_format($operating_expense,2); ?> </td>
                        <td width="100" align="right"><? echo number_format($operating_profit,2); ?></td>
                        <td width="100" align="right"><? echo number_format($operating_profit_percent,2); ?> </td>
                        <td width="100" align="right"><? echo number_format($depreciation_amortization,2); ?> </td>
                        <td width="100" align="right" title="Net Fob Value*Interest Expense(<? echo $financial_para[$costing_date]['interest_expense'] ?>)/100"><? echo number_format($interest_expense,2); ?></td>
                        <td width="100"  align="right" title="Net Fob Value*Income Tax(<? echo $financial_para[$costing_date]['income_tax'] ?>)/100"><? echo number_format($income_tax,2); ?> </td>
                        <td width="100" align="right" title="<? echo  number_format($net_profit,2); ?>"> <? echo number_format($net_profit,2); ?></td>
                        <td width="" align="right"><? echo number_format($net_profit_percent,2); ?></td>
                        
					</tr> 
					<?
					$total_country_ship_qty+=$plan_cut_qnty;
					$total_order_qty+=$order_qty_pcs;
					$total_gross_fob_val+=$gross_fob_value;
					$total_gross_fob_plancut_value+=$gross_fob_plancut_value;
					$total_foreign_cost+=$foreign;
					$total_local_cost+=$local;
					$total_net_fob_value+=$net_fob_value;
					$total_cost_of_material_service+=$cost_of_material_service;
					$total_yarn_amount_per_dzn+=$yarn_amount;
					$total_yarn_dyeing_cost+=$yarn_costing;
					$total_yarn_dyed_cost+=$yarn_dyed_cost;
					$total_conver_cost+=$conversion_cost;
					$total_knit_cost_dzn+=$knit_cost_dzn;
					$total_knit_cost+=$knit_cost;
					$total_all_over_cost+=$all_over_cost;
					$total_heat_setting_cost+=$heat_setting_cost;
					$total_trim_cost_per_dzn+=$trim_cost_per_dzn;
					$total_trim_cost+=$trim_cost;
					$total_contribution_value+=$contribution_value;
					$total_gross_profit+=$gross_profit;
					$total_operating_profit+=$operating_profit;
					$grand_total_net_profit+=$net_profit;
					$total_fabric_dyeing_cost+=$fabric_dyeing_cost;
					$total_embell_cost+=$embell_cost;
					
					$total_purchase_cost+=$fab_purchase;
					//echo $total_fab_cost_amount;
					//$total_all_over_print_cost+=$all_over_cost;
					$total_fabric_dyeing_cost_dzn+=$fabric_dyeing_cost_dzn;
					$total_fabric_finish+=$fabric_finish;
					$total_washing_cost+=$washing_cost;
					$total_print_cost+=$print_cost;
					$total_embroidery_cost+=$embroidery_cost;
					$total_special_cost+=$special_cost;
					$total_wash_cost+=$wash_cost;
					$total_other_direct_expenses+=$other_direct_expenses;
					$total_cm_cost+=$cm_cost;
					$total_commercial_cost+=$commercial_cost;
					$total_operating_exp+=$operating_expense;
					$total_incomeTax+=$income_tax;
					$total_netProfit+=$net_profit;
					$total_depreciation_amortization+=$depreciation_amortization;
					$total_interet+=$interest_expense;
					$i++;
				}
               ?>
                </table>
                </div>
                <table class="tbl_bottom" width="5565" cellpadding="0" cellspacing="0" border="1" rules="all">
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
                        <td width="70"><strong>Total</strong></td>
                        <td width="90" align="right" id="value_country_ship_qty"><? echo number_format($total_country_ship_qty,2); ?></td>
                        <td width="90" align="right" id="value_order_qty"><? echo number_format($total_order_qty,2); ?></td>
                        <td width="70">&nbsp;</td>
                        <td width="90">&nbsp;</td>
                        <td width="100" align="right" id="value_gross_fob_val"><? echo number_format($total_gross_fob_val,2); ?></td>
                        <td width="80" align="right" id="value_foreign_cost"><? echo number_format($total_foreign_cost,2); ?></td>
                        <td width="80" align="right" id="value_local_cost"><? echo number_format($total_local_cost,2); ?></td>
                        <td width="60" align="right">&nbsp;</td>
                        <td width="100" align="right" id="value_net_fob_value"><? echo number_format($total_net_fob_value,2); ?></td>
                        <td width="100" align="right" id="value_cost_of_material_service"><? echo number_format($total_cost_of_material_service,2); ?></td>
                        <td width="120" align="right"><? echo number_format($tot_yarn_req,2); ?></td>
                        <td width="120" align="right" id="value_yarn_dyeing_cost"><? echo number_format($total_yarn_dyeing_cost,2); ?></td>
                        <td width="120" align="right">&nbsp;</td>
                        <td width="80" align="right" id="value_purchase_cost"><? echo number_format($total_purchase_cost,2); ?></td>
                        <td width="100"  align="right" id="value_td_conver_cost"><? echo number_format($total_conver_cost,2); ?></td>
                        <td width="120" align="right">&nbsp;</td>
                        <td width="120" align="right" id="value_knit_cost"><? echo number_format($total_knit_cost,2); ?></td>		
                        <td width="120" align="right">&nbsp;</td>
                        <td width="120" align="right" id="value_fabric_dyeing_cost"><? echo number_format($total_fabric_dyeing_cost,2); ?></td>
                        <td width="120" align="right" id="value_yarn_dyed_cost"><? echo number_format($total_yarn_dyed_cost,2); ?></td>
                        <td width="120" align="right" id="value_all_over_cost"><? echo number_format($total_all_over_cost,2); ?></td>
                        <td width="120" align="right" id="value_heat_setting_cost"><? echo number_format($total_heat_setting_cost,2); ?></td>
                        <td width="120" align="right" id="value_fabric_finish"><? echo number_format($total_fabric_finish,2); ?></td>
                        <td width="120" align="right" id="value_washing_cost"><? echo number_format($total_washing_cost,2); ?></td>
                        <td width="100" align="right">&nbsp;</td>
                        <td width="100" align="right" id="value_trim_cost"><? echo number_format($total_trim_cost,2); ?></td>
                        <td width="80" align="right" id="value_embell_cost"><? echo number_format($total_embell_cost,2); ?></td>
                        <td width="80" align="right" id="value_print_cost"><? echo number_format($total_print_cost,2); ?></td>
                        <td width="80" align="right" id="value_embroidery_cost"><? echo number_format($total_embroidery_cost,2); ?></td>
                        <td width="80" align="right" id="value_special_cost"><? echo number_format($total_special_cost,2); ?></td>
                        <td width="120" align="right" id="value_other_direct_expenses"><? echo number_format($total_other_direct_expenses,2); ?></td>
                        <td width="120" align="right" id="value_contribution_value"><? echo number_format($total_contribution_value,2); ?></td>
                        <td width="100" align="right">&nbsp;</td>
                        <td width="120" align="right" id="value_cm_cost"><? echo number_format($total_cm_cost,2); ?></td>
                        <td width="100" align="right" id="value_gross_profit"><?  echo number_format($total_gross_profit,2);?></td>
                        <td width="100" align="right">&nbsp;</td>
                        <td width="100" align="right" id="value_commercial_cost"><? echo number_format($total_commercial_cost,2); ?></td>
                        <td width="100" align="right" id="value_operating_exp"><? echo number_format($total_operating_exp,2);?></td>
                        <td width="100" align="right" id="value_operating_profit"><? echo number_format($total_operating_profit,2);?></td>
                        <td width="100" align="right">&nbsp;</td>
                        <td width="100" align="right" id="value_depreciation_amortization"><? echo number_format($total_depreciation_amortization,2);?></td>
                        <td width="100" align="right" id="value_interet"><? echo number_format($total_interet,2); ?></td>
                        <td width="100" align="right" id="value_incomeTax"><? echo number_format($total_incomeTax,2); ?></td>
                        <td width="100" align="right" id="value_netProfit"><? echo number_format($total_netProfit,2);?></td>
                        <td width="" align="right">&nbsp;</td>
                        
                    </tr>
                </table>
               <input type="hidden" id="total_yarn" name="total_yarn" value="<? echo number_format($total_yarn_dyeing_cost,2);?>">
          		
            
            <table><tr><? //Graph here ?> </tr></table>
            <table>
                <tr><td height="15"></td></tr>
            </table>
            <table>
                <tr><td height="15"></td></tr>
            </table>
            <a id="displayText" href="javascript:toggle();">Show Summary & Yarn Cost</a>
            <div style="width:930px; display:none" id="yarn_summary" >
                <div id="data_panel2" align="center" style="width:930px">
                    <input type="button" value="Print Preview Summary & Yarn Cost" class="formbutton" style="width:250px" name="print" id="print" onClick="new_window1(1)" />
                </div>
                <table width="930" cellpadding="0" cellspacing="2" border="0">
                    <tr>
                        <td width="320" align="left" valign="top">
                            <table width="320" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="2">
                                <thead align="center">
                                    <tr><th colspan="4">Order Wise Budget Cost Summary</th></tr>
                                    <tr><th width="20">SL</th><th width="100">Particulars</th><th width="120">Amount</th><th>Percentage</th></tr>
                                </thead>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>1</td><td><strong>Gross FOB Value</strong></td>
                                    <td align="right"><? echo number_format($total_gross_fob_val,2); ?></td>
                                    <td align="right"><? echo number_format($total_gross_fob_val/$total_gross_fob_val*100,2); ?></td>
                                </tr>
                                <tr bgcolor="<?  echo $style; ?>">
                                    <td>2</td><td>Less Commission</td>
                                    <td align="right"><? $tot_commision_sum=$total_foreign_cost+$total_local_cost; echo number_format($tot_commision_sum,2); ?></td>
                                    <td align="right"><? echo number_format($tot_commision_sum/$total_gross_fob_val*100,2); ?></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>3</td><td><strong>Net FOB Value</strong></td>
                                    <td align="right"><? echo number_format($total_net_fob_value,2); ?></td>
                                    <td align="right"><? echo number_format($total_net_fob_value/$total_gross_fob_val*100,2); ?></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>4</td><td><strong>Less Cost of Material & Service</strong></td>
                                    <td align="right"> <? echo number_format($total_cost_of_material_service,2); ?></td>
                                    <td align="right"> <? echo number_format($total_cost_of_material_service/$total_gross_fob_val*100,2);?> </td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>5</td><td>Yarn Cost</td>
                                    <td align="right"><? echo number_format($total_yarn_dyeing_cost,2); ?></td>
                                    <td align="right"><? echo number_format($total_yarn_dyeing_cost/$total_gross_fob_val*100,2);?> </td>
                                </tr>
                                 <tr bgcolor="<? echo $style; ?>">
                                    <td>6</td><td>Purchase</td>
                                    <td align="right"><? echo number_format($total_purchase_cost,2); ?></td>
                                    <td align="right"><? echo number_format($total_purchase_cost/$total_gross_fob_val*100,2);?> </td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>7</td><td >Knitting Cost</td>
                                    <td align="right"><? echo number_format($total_knit_cost,2); ?></td>
                                    <td align="right"><? echo number_format($total_knit_cost/$total_gross_fob_val*100,2);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>8</td><td >Dyeing Cost</td>
                                    <td align="right"><? echo number_format($total_fabric_dyeing_cost,2); ?></td>
                                    <td align="right"><? echo number_format($total_fabric_dyeing_cost/$total_gross_fob_val*100,2);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>9</td><td >Yarn Dyed Cost</td>
                                    <td align="right"><? echo number_format($total_yarn_dyed_cost,2); ?></td>
                                    <td align="right"><? echo number_format($total_yarn_dyed_cost/$total_gross_fob_val*100,2);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>10</td><td >AOP Cost</td>
                                    <td align="right"><? echo number_format($total_all_over_cost,2); ?></td>
                                    <td align="right"><? echo number_format($total_all_over_cost/$total_gross_fob_val*100,2);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>11</td><td >Heat Setting Cost</td>
                                    <td align="right"><? echo number_format($total_heat_setting_cost,2); ?></td>
                                    <td align="right"><? echo number_format($total_heat_setting_cost/$total_gross_fob_val*100,2);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>12</td><td >Finishing Cost</td>
                                    <td align="right"><? echo number_format($total_fabric_finish,2); ?></td>
                                    <td align="right"><? echo number_format($total_fabric_finish/$total_gross_fob_val*100,2);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>13</td><td >Washing Cost</td>
                                    <td align="right"><? echo number_format($total_washing_cost,2); ?></td>
                                    <td align="right"><? echo number_format($total_washing_cost/$total_gross_fob_val*100,2);?></td>
                                </tr>
                                <tr bgcolor="#CCCCCC">
                                    <td>14</td><td><strong>Conversion Cost</strong></td>
                                    <td align="right"><strong><? echo number_format($total_conver_cost,2); ?></strong></td>
                                    <td align="right"><strong><? echo number_format($total_conver_cost/$total_gross_fob_val*100,2);?></strong></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>15</td><td>Trims Cost</td>
                                    <td align="right"><? echo number_format($total_trim_cost,2); ?></td>
                                    <td align="right"><? echo number_format($total_trim_cost/$total_gross_fob_val*100,2); ?> </td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>16</td><td>Embelishment Cost</td>
                                    <td align="right"><? echo number_format($total_embell_cost,2); ?></td>
                                    <td align="right"><? echo number_format($total_embell_cost/$total_gross_fob_val*100,2);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>17</td><td>Other Direct Expenses</td>
                                    <td align="right"><? echo number_format($total_other_direct_expenses,2); ?></td>
                                    <td align="right"><? echo number_format($total_other_direct_expenses/$total_gross_fob_val*100,2);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>18</td><td><strong>Contribution/Value Additions</strong></td>
                                    <td align="right"><? echo number_format($total_contribution_value,2); ?> </td>
                                    <td align="right"><? echo number_format($total_contribution_value/$total_gross_fob_val*100,2); ?></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>19</td><td>Less CM Cost</td>
                                    <td align="right"><? echo number_format($total_cm_cost,2); ?></td>
                                    <td align="right"><? echo number_format($total_cm_cost/$total_gross_fob_val*100,2); ?></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>20</td><td><strong>Gross Profit/Loss</strong></td>
                                    <td align="right"><? echo number_format($total_gross_profit,2); ?></td>
                                    <td align="right"><? echo number_format($total_gross_profit/$total_gross_fob_val*100,2); ?></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>21</td><td>Less Commercial Cost</td>
                                    <td align="right" id="cost_id"><? echo number_format($total_commercial_cost,2); ?></td>
                                    <td align="right"><? echo number_format($total_commercial_cost/$total_gross_fob_val*100,2); ?></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>22</td><td>Less Operating Expenses</td>
                                    <td align="right"><? echo number_format($total_operating_exp,2); ?></td>
                                    <td align="right"><? echo number_format($total_operating_exp/$total_gross_fob_val*100,2); ?></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>23</td><td><strong>Operatin Profit/Loss</strong></td>
                                    <td align="right" ><? echo number_format($total_operating_profit,2); ?></td>
                                    <td align="right"><? echo number_format($total_operating_profit/$total_gross_fob_val*100,2); ?></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>24</td><td>Less Depereciation & Amortization</td>
                                    <td align="right"><? echo number_format($total_depreciation_amortization,2); ?></td>
                                    <td align="right"><? echo number_format($total_depreciation_amortization/$total_gross_fob_val*100,2); ?></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>25</td><td>Less Interest</td>
                                    <td align="right"><? echo number_format($total_interet,2); ?></td>
                                    <td align="right"> <? echo number_format($total_interet/$total_gross_fob_val*100,2);?></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>26</td><td>Less Income Tax</td>
                                    <td align="right"><? echo number_format($total_incomeTax,2); ?></td>
                                    <td align="right"><? echo number_format($total_incomeTax/$total_gross_fob_val*100,2); ?></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>27</td><td><strong>Net Profit</strong></td>
                                    <td align="right"><? echo number_format($grand_total_net_profit,2); ?></td>
                                    <td align="right"><? echo number_format($grand_total_net_profit/$total_gross_fob_val*100,2); ?></td>
                                </tr>
                            </table>
                        </td>
                        <td width="20">&nbsp;</td>
                        <td valign="top" width="590">
                            <table class="rpt_table" width="590" cellpadding="0" cellspacing="0" border="1" rules="all">
                                <thead>
                                	<tr>
                                    	<th colspan="7">Yarn Cost Summary </th>
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
							//	print_r($yarn_description_data_arr);
                                $s=1; $tot_yarn_req_qnty=0; $tot_yarn_req_amnt=0;
								//$yarn_description_data_arr[$count_id][$copm_one_id][$percent_one][$type_id]['qty']=$qnty;
								//$yarn_description_data_arr[$count_id][$copm_one_id][$percent_one][$type_id]['amount']=$amount;
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
                        </td>
                        <!--<td colspan="5" style="min-height:800px; max-height:100%" align="center" valign="top"></td>-->
                    </tr>  
                </table>
            </div>
            </fieldset>
            </div>
           <?
		}
	}
	else if($report_type==4) //Pre cost Vs Price Quotation
	{
		if($template==1)
		{
			ob_start();
			$style1="#E9F3FF"; 
			$style="#FFFFFF";
			 	
			$cbo_ready_to=str_replace("'","",$cbo_ready_to);
			if($cbo_ready_to==2 || $cbo_ready_to=='' || $cbo_ready_to==0) $cbo_ready_to_cond="and c.ready_to_approved in(2,0)";
			else if($cbo_ready_to==1) $cbo_ready_to_cond="and c.ready_to_approved in(1)";
			else $cbo_ready_to_cond="";

			//if($cbo_ready_to==0 || $cbo_ready_to=='') $cbo_ready_to_cond="";else $cbo_ready_to_cond="and c.ready_to_approved in($cbo_ready_to)";
			$fab_precost_arr=array(); $p_fab_precost_arr=array(); $commission_array=array(); $price_commission_array=array(); $knit_arr=array(); $pq_knit_arr=array(); $fabriccostArray=array(); $fab_emb=array(); $price_fab_emb=array(); $fabric_data_Array=array(); $price_fabric_data_Array=array();$price_costing_perArray=array(); $asking_profit_arr=array(); $yarncostArray=array(); $yarn_desc_array=array(); $costing_date_arr=array();
			
			/*$yarncostDataArray=sql_select("select job_no, count_id, type_id, sum(cons_qnty) as cons_qnty, sum(amount) as amount from wo_pre_cost_fab_yarn_cost_dtls where status_active=1 and is_deleted=0 group by job_no, count_id, type_id");
			foreach($yarncostDataArray as $yarnRow)
			{
				$yarncostArray[$yarnRow[csf('job_no')]].=$yarnRow[csf('count_id')]."**".$yarnRow[csf('type_id')]."**".$yarnRow[csf('cons_qnty')]."**".$yarnRow[csf('amount')].",";
			}*/
			
			$asking_profit=sql_select("select id, company_id, applying_period_date, applying_period_to_date, asking_profit, max_profit from lib_standard_cm_entry where status_active=1 and is_deleted=0 and company_id=$company_name");//$date_max_profit and company_id=3 and id=16
			//echo "select id, company_id, applying_period_date, applying_period_to_date, asking_profit, max_profit from lib_standard_cm_entry where status_active=1 and is_deleted=0 and company_id=$company_name";
			foreach($asking_profit as $ask_row )
			{
				$applying_period_date=change_date_format($ask_row[csf('applying_period_date')],'','',1);
				$applying_period_to_date=change_date_format($ask_row[csf('applying_period_to_date')],'','',1);
				$diff=datediff('d',$applying_period_date,$applying_period_to_date);
				for($j=0;$j<$diff;$j++)
				{
					$date_all=add_date(str_replace("'","",$applying_period_date),$j);
				    $newdate =change_date_format($date_all,'','',1);
					$asking_profit_arr[$newdate]['asking_profit']=$ask_row[csf('asking_profit')];
					$asking_profit_arr[$newDate]['max_profit']=$ask_row[csf('max_profit')];
				}
			}
			$costing_date_sql=sql_select("select job_no, costing_date,ready_to_approved from wo_pre_cost_mst where status_active=1 and is_deleted=0 ");
			foreach($costing_date_sql as $row )
			{
				$cost_date=change_date_format($row[csf('costing_date')],'','',1);
				$costing_date_arr[$row[csf('job_no')]]['ask']=$asking_profit_arr[$cost_date]['asking_profit'];
				$costing_date_arr[$row[csf('job_no')]]['max']=$asking_profit_arr[$cost_date]['max_profit'];
				$job_approve_arr[$row[csf('job_no')]]['ready_to_approved']=$row[csf('ready_to_approved')];
			}

			$pri_fab_arr=sql_select("select a.quotation_id,b.fabric_source, a.rate, (a.requirment) as requirment, (a.pcs) as pcs from wo_pri_quo_fab_co_avg_con_dtls a,wo_pri_quo_fabric_cost_dtls  b where a.wo_pri_quo_fab_co_dtls_id=b.id and a.quotation_id=b.quotation_id  and b.status_active=1 and b.is_deleted=0 and b.fabric_source=2");
			foreach($pri_fab_arr as $p_row_pre)
			{
				$p_fab_precost_arr[$p_row_pre[csf('quotation_id')]].=$p_row_pre[csf('requirment')]."**".$p_row_pre[csf('pcs')]."**".$p_row_pre[csf('rate')]."**".$p_row_pre[csf('fabric_source')].",";	
			}
			$fabricDataArray=sql_select("select a.job_no, a.fab_nature_id, a.color_type_id, a.fabric_source, a.rate, b.yarn_cons_qnty, b.yarn_amount from wo_pre_cost_fabric_cost_dtls a, wo_pre_cost_sum_dtls b where a.job_no=b.job_no and a.fabric_source!=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			foreach($fabricDataArray as $fabricRow)
			{
				$fabric_data_Array[$fabricRow[csf('job_no')]].=$fabricRow[csf('fab_nature_id')]."**".$fabricRow[csf('fabric_source')]."**".$fabricRow[csf('rate')]."**".$fabricRow[csf('yarn_cons_qnty')]."**".$fabricRow[csf('yarn_amount')]."**".$fabricRow[csf('color_type_id')].",";
			} //Pre cost end
			unset($fabricDataArray);
			//var_dump($fabric_data_Array);
			$price_costDataArray=sql_select("select  id,costing_per,sew_smv  from wo_price_quotation where status_active=1 and is_deleted=0  ");
			foreach($price_costDataArray as $pri_fabRow)
			{
				$price_costing_perArray[$pri_fabRow[csf('id')]]['costing_per']=$pri_fabRow[csf('costing_per')];
				$price_costing_perArray[$pri_fabRow[csf('id')]]['smv']=$pri_fabRow[csf('sew_smv')];
			}
			//var_dump($price_costing_perArray);
			$sql_yarn_price=sql_select("select  rate, amount,cons_qnty,quotation_id from wo_pri_quo_fab_yarn_cost_dtls a where status_active=1");
			foreach($sql_yarn_price as $fabricRow)
			{
				$price_yarn_data_Array[$fabricRow[csf('quotation_id')]]['amount']+=$fabricRow[csf('amount')];
				$price_yarn_data_Array[$fabricRow[csf('quotation_id')]]['cons_qnty']+=$fabricRow[csf('cons_qnty')];
			}
			
			$price_fabricDataArray=sql_select("select a.quotation_id, a.fab_nature_id, a.color_type_id, a.fabric_source, a.rate, b.yarn_cons_qnty, b.yarn_amount from wo_pri_quo_fabric_cost_dtls a, wo_pri_quo_sum_dtls b where a.quotation_id=b.quotation_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			foreach($price_fabricDataArray as $price_fabricRow)
			{
				$yarn_amount=$price_yarn_data_Array[$price_fabricRow[csf('quotation_id')]]['amount'];
				$yarn_cons_qnty=$price_yarn_data_Array[$price_fabricRow[csf('quotation_id')]]['cons_qnty'];
				
				$price_fabric_data_Array[$price_fabricRow[csf('quotation_id')]].=$price_fabricRow[csf('fab_nature_id')]."**".$price_fabricRow[csf('fabric_source')]."**".$price_fabricRow[csf('rate')]."**".$yarn_cons_qnty."**".$yarn_amount."**".$price_fabricRow[csf('color_type_id')].",";
			} 
			$price_data_array_emb=("select  quotation_id,
			sum(CASE WHEN emb_name=1 THEN amount END) AS print_amount,
			sum(CASE WHEN emb_name=2 THEN amount END) AS embroidery_amount,
			sum(CASE WHEN emb_name=3 THEN amount END) AS wash_amount,
			sum(CASE WHEN emb_name=4 THEN amount END) AS special_amount,
			sum(CASE WHEN emb_name=5 THEN amount END) AS other_amount
			from  wo_pri_quo_embe_cost_dtls where  status_active=1 and is_deleted=0 group by quotation_id");
			$sql_embl_array=sql_select($price_data_array_emb);
			foreach($sql_embl_array as $p_row_emb)
			{
				$price_fab_emb[$p_row_emb[csf('quotation_id')]]['print']=$p_row_emb[csf('print_amount')];
				$price_fab_emb[$p_row_emb[csf('quotation_id')]]['embroidery']=$p_row_emb[csf('embroidery_amount')];
				$price_fab_emb[$p_row_emb[csf('quotation_id')]]['special']=$p_row_emb[csf('special_amount')];
				$price_fab_emb[$p_row_emb[csf('quotation_id')]]['other']=$p_row_emb[csf('other_amount')];
				$price_fab_emb[$p_row_emb[csf('quotation_id')]]['wash']=$p_row_emb[csf('wash_amount')];
			} 
			//var_dump($price_fab_emb);
			
			$fabriccostDataArray=sql_select("select job_no, costing_per_id, trims_cost, embel_cost, cm_cost, commission, common_oh, lab_test, inspection, freight, comm_cost, certificate_pre_cost, currier_pre_cost from wo_pre_cost_dtls where status_active=1 and is_deleted=0");
			foreach($fabriccostDataArray as $fabRow)
			{
				$fabriccostArray[$fabRow[csf('job_no')]]['costing_per_id']=$fabRow[csf('costing_per_id')];
				$fabriccostArray[$fabRow[csf('job_no')]]['trims_cost']=$fabRow[csf('trims_cost')];
				$fabriccostArray[$fabRow[csf('job_no')]]['embel_cost']=$fabRow[csf('embel_cost')];
				$fabriccostArray[$fabRow[csf('job_no')]]['cm_cost']=$fabRow[csf('cm_cost')];
				$fabriccostArray[$fabRow[csf('job_no')]]['commission']=$fabRow[csf('commission')];
				$fabriccostArray[$fabRow[csf('job_no')]]['common_oh']=$fabRow[csf('common_oh')];
				$fabriccostArray[$fabRow[csf('job_no')]]['lab_test']=$fabRow[csf('lab_test')];
				$fabriccostArray[$fabRow[csf('job_no')]]['inspection']=$fabRow[csf('inspection')];
				$fabriccostArray[$fabRow[csf('job_no')]]['freight']=$fabRow[csf('freight')];
				$fabriccostArray[$fabRow[csf('job_no')]]['comm_cost']=$fabRow[csf('comm_cost')];
				$fabriccostArray[$fabRow[csf('job_no')]]['certificate_pre_cost']=$fabRow[csf('certificate_pre_cost')];
				$fabriccostArray[$fabRow[csf('job_no')]]['currier_pre_cost']=$fabRow[csf('currier_pre_cost')];
				$fabriccostArray[$fabRow[csf('job_no')]]['c_cost']=$fabRow[csf('cm_cost')];
			} //pre cost trim emb, commission and others end
			//var_dump($fabriccostArray);
			$price_fabriccostArray=array();
			$p_fabriccostDataArray=sql_select("select quotation_id, costing_per_id, trims_cost, embel_cost, cm_cost, commission, common_oh, lab_test, inspection, freight, comm_cost, certificate_pre_cost, currier_pre_cost from wo_price_quotation_costing_mst where status_active=1 and is_deleted=0");
			foreach($p_fabriccostDataArray as $p_fabRow)
			{
				$price_fabriccostArray[$p_fabRow[csf('quotation_id')]]['costing_per_id']=$p_fabRow[csf('costing_per_id')];
				$price_fabriccostArray[$p_fabRow[csf('quotation_id')]]['trims_cost']=$p_fabRow[csf('trims_cost')];
				$price_fabriccostArray[$p_fabRow[csf('quotation_id')]]['embel_cost']=$p_fabRow[csf('embel_cost')];
				$price_fabriccostArray[$p_fabRow[csf('quotation_id')]]['cm_cost']=$p_fabRow[csf('cm_cost')];
				$price_fabriccostArray[$p_fabRow[csf('quotation_id')]]['commission']=$p_fabRow[csf('commission')];
				$price_fabriccostArray[$p_fabRow[csf('quotation_id')]]['common_oh']=$p_fabRow[csf('common_oh')];
				$price_fabriccostArray[$p_fabRow[csf('quotation_id')]]['lab_test']=$p_fabRow[csf('lab_test')];
				$price_fabriccostArray[$p_fabRow[csf('quotation_id')]]['inspection']=$p_fabRow[csf('inspection')];
				$price_fabriccostArray[$p_fabRow[csf('quotation_id')]]['freight']=$p_fabRow[csf('freight')];
				$price_fabriccostArray[$p_fabRow[csf('quotation_id')]]['comm_cost']=$p_fabRow[csf('comm_cost')];
				$price_fabriccostArray[$p_fabRow[csf('quotation_id')]]['certificate_pre_cost']=$p_fabRow[csf('certificate_pre_cost')];
				$price_fabriccostArray[$p_fabRow[csf('quotation_id')]]['currier_pre_cost']=$p_fabRow[csf('currier_pre_cost')];
				$price_fabriccostArray[$p_fabRow[csf('quotation_id')]]['c_cost']=$p_fabRow[csf('cm_cost')];
			}
			
			$pq_knit_data=sql_select("select quotation_id,
			sum(CASE WHEN cons_type=1 THEN amount END) AS knit_charge,
			sum(CASE WHEN cons_type=2 THEN amount END) AS weaving_charge,
			sum(CASE WHEN cons_type=3 THEN amount END) AS knit_charge_collar_cuff,
			sum(CASE WHEN cons_type=4 THEN amount END) AS knit_charge_feeder_stripe,
			sum(CASE WHEN cons_type in(140,142,148,64) THEN amount END) AS washing_cost,
			sum(CASE WHEN cons_type in(35,36,37,40) THEN amount END) AS all_over_cost,
			sum(CASE WHEN cons_type=30 THEN amount END) AS yarn_dyeing_cost,
			sum(CASE WHEN cons_type=33 THEN amount END) AS heat_setting_cost,
			sum(CASE WHEN cons_type in(25,31,26,32,60,61,62,63,72,80,81,84,85,86,87,38,39,74,78,79,101,133,137,138,139,146,147,149) THEN amount END) AS fabric_dyeing_cost,
			sum(CASE WHEN cons_type in(33,34,38,63,65,66,67,68,69,70,71,72,73,75,76,77,88,82,89,90,91,92,93,94,128,129,135,136,141,143,150,151,155,156,157,145,82,89,132,144) THEN amount END) AS fabric_finish_cost
			from wo_pri_quo_fab_conv_cost_dtls where  status_active=1  and is_deleted=0 group by quotation_id");
			foreach($pq_knit_data as $p_row_knit)
			{
				$pq_knit_arr[$p_row_knit[csf('quotation_id')]]['knit']=$p_row_knit[csf('knit_charge')];
				$pq_knit_arr[$p_row_knit[csf('quotation_id')]]['weaving']=$p_row_knit[csf('weaving_charge')];
				$pq_knit_arr[$p_row_knit[csf('quotation_id')]]['collar_cuff']=$p_row_knit[csf('knit_charge_collar_cuff')];
				$pq_knit_arr[$p_row_knit[csf('quotation_id')]]['feeder_stripe']=$p_row_knit[csf('knit_charge_feeder_stripe')];
				$pq_knit_arr[$p_row_knit[csf('quotation_id')]]['washing']=$p_row_knit[csf('washing_cost')];
				$pq_knit_arr[$p_row_knit[csf('quotation_id')]]['all_over']=$p_row_knit[csf('all_over_cost')];
				$pq_knit_arr[$p_row_knit[csf('quotation_id')]]['fabric_dyeing']=$p_row_knit[csf('fabric_dyeing_cost')];
				$pq_knit_arr[$p_row_knit[csf('quotation_id')]]['yarn_dyeing']=$p_row_knit[csf('yarn_dyeing_cost')];	
				$pq_knit_arr[$p_row_knit[csf('quotation_id')]]['heat']=$p_row_knit[csf('heat_setting_cost')];
				$pq_knit_arr[$p_row_knit[csf('quotation_id')]]['fabric_finish']=$p_row_knit[csf('fabric_finish_cost')];	
			} 
			
						
			$p_data_array=sql_select("select quotation_id,
			sum(CASE WHEN particulars_id=1 THEN commission_amount END) AS foreign_comm,
			sum(CASE WHEN particulars_id=2 THEN commission_amount END) AS local_comm
			from  wo_pri_quo_commiss_cost_dtls where status_active=1 and  is_deleted=0 group by quotation_id");// quotation_id='$data'
			foreach($p_data_array as $p_row_fl )
			{
				$price_commission_array[$p_row_fl[csf('quotation_id')]]['foreign']=$p_row_fl[csf('foreign_comm')];
				$price_commission_array[$p_row_fl[csf('quotation_id')]]['local']=$p_row_fl[csf('local_comm')];
			}
		
			?>
			
		<script>
		
				var order_value=document.getElementById('p_total_order_value').innerHTML.replace(/,/g,'');
				document.getElementById('yarn_qut_cost').innerHTML=document.getElementById('p_total_yarn_costing').innerHTML;
				document.getElementById('yarn_qut_per').innerHTML=number_format_common((document.getElementById('p_total_yarn_costing').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('yarn_bud_cost').innerHTML=document.getElementById('total_yarn_costing').innerHTML;
				document.getElementById('yarn_bud_per').innerHTML=number_format_common((document.getElementById('total_yarn_costing').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('yarn_ver_cost').innerHTML=document.getElementById('v_total_yarn_costing').innerHTML;
				document.getElementById('yarn_ver_per').innerHTML=number_format_common((document.getElementById('v_total_yarn_costing').innerHTML.split(',').join('')/document.getElementById('p_total_yarn_costing').innerHTML.split(',').join(''))*100,2);
				
				document.getElementById('fab_qut_cost').innerHTML=document.getElementById('p_total_fab_purchase').innerHTML;
				document.getElementById('fab_qut_per').innerHTML=number_format_common((document.getElementById('p_total_fab_purchase').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('fab_bud_cost').innerHTML=document.getElementById('total_fab_purchase').innerHTML;
				document.getElementById('fab_bud_per').innerHTML=number_format_common((document.getElementById('total_fab_purchase').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('fab_ver_cost').innerHTML=document.getElementById('v_total_fab_purchase').innerHTML;
				document.getElementById('fab_ver_per').innerHTML=number_format_common((document.getElementById('v_total_fab_purchase').innerHTML.split(',').join('')/document.getElementById('p_total_fab_purchase').innerHTML.split(',').join(''))*100,2);
				
				document.getElementById('knit_qut_cost').innerHTML=document.getElementById('p_total_knit_cost').innerHTML;
				document.getElementById('knit_qut_per').innerHTML=number_format_common((document.getElementById('p_total_knit_cost').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('knit_bud_cost').innerHTML=document.getElementById('total_knit_cost').innerHTML;
				document.getElementById('knit_bud_per').innerHTML=number_format_common((document.getElementById('total_knit_cost').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('knit_ver_cost').innerHTML=document.getElementById('v_total_knit_cost').innerHTML;
				document.getElementById('knit_ver_per').innerHTML=number_format_common((document.getElementById('v_total_knit_cost').innerHTML.split(',').join('')/document.getElementById('p_total_knit_cost').innerHTML.split(',').join(''))*100,2);
				
				document.getElementById('ydye_qut_cost').innerHTML=document.getElementById('p_total_yarn_dyeing_cost').innerHTML;
				document.getElementById('ydye_qut_per').innerHTML=number_format_common((document.getElementById('p_total_yarn_dyeing_cost').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('ydye_bud_cost').innerHTML=document.getElementById('total_yarn_dyeing_cost').innerHTML;
				document.getElementById('ydye_bud_per').innerHTML=number_format_common((document.getElementById('total_yarn_dyeing_cost').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('ydye_ver_cost').innerHTML=document.getElementById('v_total_yarn_dyeing_cost').innerHTML;
				document.getElementById('ydye_ver_per').innerHTML=number_format_common((document.getElementById('v_total_yarn_dyeing_cost').innerHTML.split(',').join('')/document.getElementById('p_total_yarn_dyeing_cost').innerHTML.split(',').join(''))*100,2);
				
				document.getElementById('aop_qut_cost').innerHTML=document.getElementById('p_aop_td').innerHTML;
				document.getElementById('aop_qut_per').innerHTML=number_format_common((document.getElementById('p_aop_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('aop_bud_cost').innerHTML=document.getElementById('aop_td').innerHTML;
				document.getElementById('aop_bud_per').innerHTML=number_format_common((document.getElementById('aop_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('aop_ver_cost').innerHTML=document.getElementById('v_aop_td').innerHTML;
				document.getElementById('aop_ver_per').innerHTML=number_format_common((document.getElementById('v_aop_td').innerHTML.split(',').join('')/document.getElementById('p_aop_td').innerHTML.split(',').join(''))*100,2);
				
				var p_dyefin_val=(parseFloat(document.getElementById('p_fabric_dyeing_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('p_fabric_finishing_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('p_heat_setting_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('p_washing_td').innerHTML.split(',').join('')));
				var dyefin_val=(parseFloat(document.getElementById('fabric_dyeing_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('fabric_finishing_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('heat_setting_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('washing_td').innerHTML.split(',').join('')));
				var v_dyefin_val=(parseFloat(document.getElementById('v_fabric_dyeing_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('v_fabric_finishing_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('v_heat_setting_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('v_washing_td').innerHTML.split(',').join('')));
				document.getElementById('dyeFin_qut_cost').innerHTML=number_format_common(p_dyefin_val,2);
				document.getElementById('dyeFin_qut_per').innerHTML=number_format_common((p_dyefin_val/order_value)*100,2);
				document.getElementById('dyeFin_bud_cost').innerHTML=number_format_common(dyefin_val,2);
				document.getElementById('dyeFin_bud_per').innerHTML=number_format_common((dyefin_val/order_value)*100,2);
				document.getElementById('dyeFin_ver_cost').innerHTML=number_format_common(v_dyefin_val,2);
				document.getElementById('dyeFin_ver_per').innerHTML=number_format_common((v_dyefin_val/p_dyefin_val)*100,2);
				//var p_matService_val=document.getElementById('p_total_yarn_costing').innerHTML.split(',').join('');
				var p_matService_val=(parseFloat(document.getElementById('yarn_qut_cost').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('fab_qut_cost').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('knit_qut_cost').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('ydye_qut_cost').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('aop_qut_cost').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('dyeFin_qut_cost').innerHTML.split(',').join('')));
				var matService_val=(parseFloat(document.getElementById('yarn_bud_cost').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('fab_bud_cost').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('knit_bud_cost').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('ydye_bud_cost').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('aop_bud_cost').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('dyeFin_bud_cost').innerHTML.split(',').join('')));
				
				var v_matService_val=(parseFloat(document.getElementById('yarn_ver_cost').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('fab_ver_cost').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('knit_ver_cost').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('ydye_ver_cost').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('aop_ver_cost').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('dyeFin_ver_cost').innerHTML.split(',').join('')));
				document.getElementById('matService_qut_cost').innerHTML=number_format_common(p_matService_val,2);
				document.getElementById('matService_qut_per').innerHTML=number_format_common((p_matService_val/order_value)*100,2);
				document.getElementById('matService_bud_cost').innerHTML=number_format_common(matService_val,2);
				document.getElementById('matService_bud_per').innerHTML=number_format_common((matService_val/order_value)*100,2);
				document.getElementById('matService_ver_cost').innerHTML=number_format_common(v_matService_val,2);
				document.getElementById('matService_ver_per').innerHTML=number_format_common((v_matService_val/p_matService_val)*100,2)
				
				document.getElementById('trim_qut_cost').innerHTML=document.getElementById('p_trim_amount_td').innerHTML;
				document.getElementById('trim_qut_per').innerHTML=number_format_common((document.getElementById('p_trim_amount_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('trim_bud_cost').innerHTML=document.getElementById('trim_amount_td').innerHTML;
				document.getElementById('trim_bud_per').innerHTML=number_format_common((document.getElementById('trim_amount_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('trim_ver_cost').innerHTML=document.getElementById('v_trim_amount_td').innerHTML;
				document.getElementById('trim_ver_per').innerHTML=number_format_common((document.getElementById('v_trim_amount_td').innerHTML.split(',').join('')/document.getElementById('p_trim_amount_td').innerHTML.split(',').join(''))*100,2);
				
				var p_embel_val=(parseFloat(document.getElementById('p_print_amount_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('p_embroidery_amount_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('p_wash_amt_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('p_other_amount_td').innerHTML.split(',').join('')));
				var embel_val=(parseFloat(document.getElementById('print_amount_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('embroidery_amount_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('wash_amt_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('other_amount_td').innerHTML.split(',').join('')));
				
				
				var v_embel_val=(parseFloat(document.getElementById('v_print_amount_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('v_embroidery_amount_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('v_wash_amt_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('v_other_amount_td').innerHTML.split(',').join('')));
				
				
				document.getElementById('embl_qut_cost').innerHTML=number_format_common(p_embel_val,2);
				document.getElementById('embl_qut_per').innerHTML=number_format_common((p_embel_val/order_value)*100,2);
				document.getElementById('embl_bud_cost').innerHTML=number_format_common(embel_val,2);
				document.getElementById('embl_bud_per').innerHTML=number_format_common((embel_val/order_value)*100,2);
				document.getElementById('embl_ver_cost').innerHTML=number_format_common(v_embel_val,2);
				document.getElementById('embl_ver_per').innerHTML=number_format_common((v_embel_val/p_embel_val)*100,2)
				
				document.getElementById('commercial_qut_cost').innerHTML=document.getElementById('p_commercial_cost_td').innerHTML;
				document.getElementById('commercial_qut_per').innerHTML=number_format_common((document.getElementById('p_commercial_cost_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('commercial_bud_cost').innerHTML=document.getElementById('commercial_cost_td').innerHTML;
				document.getElementById('commercial_bud_per').innerHTML=number_format_common((document.getElementById('commercial_cost_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('commercial_ver_cost').innerHTML=document.getElementById('v_commercial_cost_td').innerHTML;
				document.getElementById('commercial_ver_per').innerHTML=number_format_common((document.getElementById('v_commercial_cost_td').innerHTML.split(',').join('')/document.getElementById('p_commercial_cost_td').innerHTML.split(',').join(''))*100,2);
				var p_commision_val=(parseFloat(document.getElementById('p_foreign_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('p_local_td').innerHTML.split(',').join('')));
				var commision_val=(parseFloat(document.getElementById('foreign_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('local_td').innerHTML.split(',').join('')));
				var v_commision_val=(parseFloat(document.getElementById('v_foreign_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('v_local_td').innerHTML.split(',').join('')));
				
				document.getElementById('commision_qut_cost').innerHTML=number_format_common(p_commision_val,2);
				document.getElementById('commision_qut_per').innerHTML=number_format_common((p_commision_val/order_value)*100,2);
				document.getElementById('commision_bud_cost').innerHTML=number_format_common(commision_val,2);
				document.getElementById('commision_bud_per').innerHTML=number_format_common((commision_val/order_value)*100,2);
				document.getElementById('commision_ver_cost').innerHTML=number_format_common(v_commision_val,2);
				document.getElementById('commision_ver_per').innerHTML=number_format_common((v_commision_val/p_commision_val)*100,2);
				
				document.getElementById('testing_qut_cost').innerHTML=document.getElementById('p_test_cost_td').innerHTML;
				document.getElementById('testing_qut_per').innerHTML=number_format_common((document.getElementById('p_test_cost_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('testing_bud_cost').innerHTML=document.getElementById('test_cost_td').innerHTML;
				document.getElementById('testing_bud_per').innerHTML=number_format_common((document.getElementById('test_cost_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('testing_ver_cost').innerHTML=document.getElementById('v_test_cost_td').innerHTML;
				document.getElementById('testing_ver_per').innerHTML=number_format_common((document.getElementById('v_test_cost_td').innerHTML.split(',').join('')/document.getElementById('p_test_cost_td').innerHTML.split(',').join(''))*100,2);
				
				document.getElementById('freight_qut_cost').innerHTML=document.getElementById('p_freight_cost_td').innerHTML;
				document.getElementById('freight_qut_per').innerHTML=number_format_common((document.getElementById('p_freight_cost_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('freight_bud_cost').innerHTML=document.getElementById('freight_cost_td').innerHTML;
				document.getElementById('freight_bud_per').innerHTML=number_format_common((document.getElementById('freight_cost_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('freight_ver_cost').innerHTML=document.getElementById('v_freight_cost_td').innerHTML;
				document.getElementById('freight_ver_per').innerHTML=number_format_common((document.getElementById('v_freight_cost_td').innerHTML.split(',').join('')/document.getElementById('p_freight_cost_td').innerHTML.split(',').join(''))*100,2);
				document.getElementById('insp_qut_cost').innerHTML=document.getElementById('p_inspection_cost_td').innerHTML;
				document.getElementById('insp_qut_per').innerHTML=number_format_common((document.getElementById('p_inspection_cost_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('insp_bud_cost').innerHTML=document.getElementById('inspection_cost_td').innerHTML;
				document.getElementById('insp_bud_per').innerHTML=number_format_common((document.getElementById('inspection_cost_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('insp_ver_cost').innerHTML=document.getElementById('v_inspection_cost_td').innerHTML;
				document.getElementById('insp_ver_per').innerHTML=number_format_common((document.getElementById('v_inspection_cost_td').innerHTML.split(',').join('')/document.getElementById('p_inspection_cost_td').innerHTML.split(',').join(''))*100,2);
				document.getElementById('certi_qut_cost').innerHTML=document.getElementById('p_certificate_cost_td').innerHTML;
				document.getElementById('certi_qut_per').innerHTML=number_format_common((document.getElementById('p_certificate_cost_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('certi_bud_cost').innerHTML=document.getElementById('certificate_cost_td').innerHTML;
				document.getElementById('certi_bud_per').innerHTML=number_format_common((document.getElementById('certificate_cost_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('certi_ver_cost').innerHTML=document.getElementById('v_certificate_cost_td').innerHTML;
				document.getElementById('certi_ver_per').innerHTML=number_format_common((document.getElementById('v_certificate_cost_td').innerHTML.split(',').join('')/document.getElementById('p_certificate_cost_td').innerHTML.split(',').join(''))*100,2);
				document.getElementById('operating_qut_cost').innerHTML=document.getElementById('p_operating_exp_td').innerHTML;
				document.getElementById('operating_qut_per').innerHTML=number_format_common((document.getElementById('p_operating_exp_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('operating_bud_cost').innerHTML=document.getElementById('operating_exp_td').innerHTML;
				document.getElementById('operating_bud_per').innerHTML=number_format_common((document.getElementById('operating_exp_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('operating_ver_cost').innerHTML=document.getElementById('v_operating_exp_td').innerHTML;
				document.getElementById('operating_ver_per').innerHTML=number_format_common((document.getElementById('v_operating_exp_td').innerHTML.split(',').join('')/document.getElementById('p_operating_exp_td').innerHTML.split(',').join(''))*100,2);
				document.getElementById('courier_qut_cost').innerHTML=document.getElementById('p_currier_cost_td').innerHTML;
				document.getElementById('courier_qut_per').innerHTML=number_format_common((document.getElementById('p_currier_cost_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('courier_bud_cost').innerHTML=document.getElementById('currier_cost_td').innerHTML;
				document.getElementById('courier_bud_per').innerHTML=number_format_common((document.getElementById('currier_cost_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('courier_ver_cost').innerHTML=document.getElementById('v_currier_cost_td').innerHTML;
				document.getElementById('courier_ver_per').innerHTML=number_format_common((document.getElementById('v_currier_cost_td').innerHTML.split(',').join('')/document.getElementById('p_currier_cost_td').innerHTML.split(',').join(''))*100,2);
				document.getElementById('cm_qut_cost').innerHTML=document.getElementById('p_cm_cost_td').innerHTML;
				document.getElementById('cm_qut_per').innerHTML=number_format_common((document.getElementById('p_cm_cost_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('cm_bud_cost').innerHTML=document.getElementById('cm_cost_td').innerHTML;
				document.getElementById('cm_bud_per').innerHTML=number_format_common((document.getElementById('cm_cost_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('cm_ver_cost').innerHTML=document.getElementById('v_cm_cost_td').innerHTML;
				document.getElementById('cm_ver_per').innerHTML=number_format_common((document.getElementById('v_cm_cost_td').innerHTML.split(',').join('')/document.getElementById('p_cm_cost_td').innerHTML.split(',').join(''))*100,2);
				
				document.getElementById('cost_qut_cost').innerHTML=document.getElementById('p_tot_cost_td').innerHTML;
				document.getElementById('cost_qut_per').innerHTML=number_format_common((document.getElementById('p_tot_cost_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('cost_bud_cost').innerHTML=document.getElementById('tot_cost_td').innerHTML;
				document.getElementById('cost_bud_per').innerHTML=number_format_common((document.getElementById('tot_cost_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('cost_ver_cost').innerHTML=document.getElementById('v_tot_cost_td').innerHTML;
				document.getElementById('cost_ver_per').innerHTML=number_format_common((document.getElementById('v_tot_cost_td').innerHTML.split(',').join('')/document.getElementById('p_tot_cost_td').innerHTML.split(',').join(''))*100,2);
				
				document.getElementById('ordVal_qut_cost').innerHTML=document.getElementById('p_total_order_value').innerHTML;
				document.getElementById('ordVal_qut_per').innerHTML=number_format_common((document.getElementById('p_total_order_value').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('ordVal_bud_cost').innerHTML=document.getElementById('total_order_amount2').innerHTML;
				document.getElementById('ordVal_bud_per').innerHTML=number_format_common((document.getElementById('total_order_amount2').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('ordVal_ver_cost').innerHTML=document.getElementById('total_order_amount2').innerHTML;
				document.getElementById('ordVal_ver_per').innerHTML=number_format_common((document.getElementById('total_order_amount2').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('proLoss_qut_cost').innerHTML=document.getElementById('p_profitt_td').innerHTML;
				document.getElementById('proLoss_qut_per').innerHTML=number_format_common((document.getElementById('p_profitt_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('proLoss_bud_cost').innerHTML=document.getElementById('profitt_td').innerHTML;
				document.getElementById('proLoss_bud_per').innerHTML=number_format_common((document.getElementById('profitt_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('proLoss_ver_cost').innerHTML=document.getElementById('v_profitt_td').innerHTML;
				document.getElementById('proLoss_ver_per').innerHTML=number_format_common((document.getElementById('v_profitt_td').innerHTML.split(',').join('')/document.getElementById('p_profitt_td').innerHTML.split(',').join(''))*100,2);
				
				document.getElementById('expPro_qut_cost').innerHTML=number_format_common(document.getElementById('p_expProfitt_td').innerHTML,2);
				document.getElementById('expPro_qut_per').innerHTML=number_format_common((document.getElementById('p_expProfitt_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('expPro_bud_cost').innerHTML=number_format_common(document.getElementById('expProfitt_td').innerHTML,2);
				document.getElementById('expPro_bud_per').innerHTML=number_format_common((document.getElementById('expProfitt_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('expPro_ver_cost').innerHTML=number_format_common(document.getElementById('v_expProfitt_td').innerHTML,2);
				document.getElementById('expPro_ver_per').innerHTML=number_format_common((document.getElementById('v_expProfitt_td').innerHTML.split(',').join('')/document.getElementById('p_expProfitt_td').innerHTML.split(',').join(''))*100,2);
				
				document.getElementById('expProv_qut_cost').innerHTML=document.getElementById('p_expect_variance_td').innerHTML;
				document.getElementById('expProv_qut_per').innerHTML=number_format_common((document.getElementById('p_expect_variance_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('expProv_bud_cost').innerHTML=document.getElementById('expect_variance_td').innerHTML;
				document.getElementById('expProv_bud_per').innerHTML=number_format_common((document.getElementById('expect_variance_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('exp_prof_div').text=number_format_common((document.getElementById('p_expProfitt_td').innerHTML.split(',').join('')/order_value)*100,2);
				//document.getElementById('exp_prof_div').text=
				$('#exp_prof_div').text(number_format_common((document.getElementById('p_expProfitt_td').innerHTML.split(',').join('')/order_value)*100)); 
				 
				
                function toggle() 
                {
                    var ele = document.getElementById("yarn_summary");
                    
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
            
            
            
            <?
            	ob_start();
			?>
            <div style="width:4870px;">
            <div style="width:900px;" align="left">
                <table width="900" cellpadding="0" cellspacing="2" border="0">
                    <tr>
                        <td width="650" align="left">
                            <table width="600" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="2">
                                <thead align="center">
                                    <tr><th colspan="8">Price Quotation Vs Budget Variance Summary</th></tr>
                                    <tr>
                                        <th width="20">SL</th>
                                        <th width="140">Particulars</th>
                                        <th width="110">Mkt. Cost</th>
                                        <th width="80">% On Order Value</th>
                                        <th width="110">Budgeted Cost</th>
                                        <th width="80">% On Order Value</th>
                                        <th width="100">Variance</th>
                                        <th>% On Mkt. Cost</th>
                                    </tr>
                                </thead>
                                <tr bgcolor="<?  echo $style1; ?>">
                                    <td>1</td><td>Yarn Cost</td>
                                    <td align="right" id="yarn_qut_cost"></td>
                                    <td align="right" id="yarn_qut_per"></td>
                                    <td align="right" id="yarn_bud_cost"></td>
                                    <td align="right" id="yarn_bud_per"></td>
                                    <td align="right" id="yarn_ver_cost"></td>
                                    <td align="right" id="yarn_ver_per"></td>
                                </tr>
                                <tr bgcolor="<?  echo $style; ?>">
                                    <td>2</td><td>Fabric Purchase</td>
                                    <td align="right" id="fab_qut_cost"></td>
                                    <td align="right" id="fab_qut_per"></td>
                                    <td align="right" id="fab_bud_cost"></td>
                                    <td align="right" id="fab_bud_per"></td>
                                    <td align="right" id="fab_ver_cost"></td>
                                    <td align="right" id="fab_ver_per"></td>
                                </tr>
                                <tr bgcolor="<?  echo $style1; ?>">
                                    <td>3</td><td>Knitting Cost</td>
                                    <td align="right" id="knit_qut_cost"></td>
                                    <td align="right" id="knit_qut_per"></td>
                                    <td align="right" id="knit_bud_cost"></td>
                                    <td align="right" id="knit_bud_per"></td>
                                    <td align="right" id="knit_ver_cost"></td>
                                    <td align="right" id="knit_ver_per"></td>
                                </tr>
                                <tr bgcolor="<?  echo $style; ?>">
                                    <td>4</td><td>Yarn Dyeing Cost</td>
                                    <td align="right" id="ydye_qut_cost"></td>
                                    <td align="right" id="ydye_qut_per"></td>
                                    <td align="right" id="ydye_bud_cost"></td>
                                    <td align="right" id="ydye_bud_per"></td>
                                    <td align="right" id="ydye_ver_cost"></td>
                                    <td align="right" id="ydye_ver_per"></td>
                                </tr>
                                <tr bgcolor="<?  echo $style1; ?>">
                                    <td>5</td><td>AOP Cost</td>
                                    <td align="right" id="aop_qut_cost"></td>
                                    <td align="right" id="aop_qut_per"></td>
                                    <td align="right" id="aop_bud_cost"></td>
                                    <td align="right" id="aop_bud_per"></td>
                                    <td align="right" id="aop_ver_cost"></td>
                                    <td align="right" id="aop_ver_per"></td>
                                </tr>
                                <tr bgcolor="<?  echo $style; ?>">
                                    <td>6</td><td>Dyeing & Finishing Cost</td>
                                    <td align="right" id="dyeFin_qut_cost"></td>
                                    <td align="right" id="dyeFin_qut_per"></td>
                                    <td align="right" id="dyeFin_bud_cost"></td>
                                    <td align="right" id="dyeFin_bud_per"></td>
                                    <td align="right" id="dyeFin_ver_cost"></td>
                                    <td align="right" id="dyeFin_ver_per"></td>
                                </tr>
                                <tr bgcolor="#CCCCCC">
                                    <td colspan="2"><strong>Total Material & Service Cost</strong></td>
                                    <td align="right" id="matService_qut_cost"></td>
                                    <td align="right" id="matService_qut_per"></td>
                                    <td align="right" id="matService_bud_cost"></td>
                                    <td align="right" id="matService_bud_per"></td>
                                    <td align="right" id="matService_ver_cost"></td>
                                    <td align="right" id="matService_ver_per"></td>
                                </tr>
                                <tr bgcolor="<?  echo $style1; ?>">
                                    <td>7</td><td>Trims Cost</td>
                                    <td align="right" id="trim_qut_cost"></td>
                                    <td align="right" id="trim_qut_per"></td>
                                    <td align="right" id="trim_bud_cost"></td>
                                    <td align="right" id="trim_bud_per"></td>
                                    <td align="right" id="trim_ver_cost"></td>
                                    <td align="right" id="trim_ver_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>8</td><td>Print/ Emb./ Wash Cost</td>
                                    <td align="right" id="embl_qut_cost"></td>
                                    <td align="right" id="embl_qut_per"></td>
                                    <td align="right" id="embl_bud_cost"></td>
                                    <td align="right" id="embl_bud_per"></td>
                                    <td align="right" id="embl_ver_cost"></td>
                                    <td align="right" id="embl_ver_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>9</td><td>Commercial Cost</td>
                                    <td align="right" id="commercial_qut_cost"></td>
                                    <td align="right" id="commercial_qut_per"></td>
                                    <td align="right" id="commercial_bud_cost"></td>
                                    <td align="right" id="commercial_bud_per"></td>
                                    <td align="right" id="commercial_ver_cost"></td>
                                    <td align="right" id="commercial_ver_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>10</td><td>Commision Cost</td>
                                    <td align="right" id="commision_qut_cost"></td>
                                    <td align="right" id="commision_qut_per"></td>
                                    <td align="right" id="commision_bud_cost"></td>
                                    <td align="right" id="commision_bud_per"></td>
                                    <td align="right" id="commision_ver_cost"></td>
                                    <td align="right" id="commision_ver_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>11</td><td>Testing Cost</td>
                                    <td align="right" id="testing_qut_cost"></td>
                                    <td align="right" id="testing_qut_per"></td>
                                    <td align="right" id="testing_bud_cost"></td>
                                    <td align="right" id="testing_bud_per"></td>
                                    <td align="right" id="testing_ver_cost"></td>
                                    <td align="right" id="testing_ver_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>12</td><td>Freight Cost</td>
                                    <td align="right" id="freight_qut_cost"></td>
                                    <td align="right" id="freight_qut_per"></td>
                                    <td align="right" id="freight_bud_cost"></td>
                                    <td align="right" id="freight_bud_per"></td>
                                    <td align="right" id="freight_ver_cost"></td>
                                    <td align="right" id="freight_ver_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>13</td><td>Inspection Cost</td>
                                    <td align="right" id="insp_qut_cost"></td>
                                    <td align="right" id="insp_qut_per"></td>
                                    <td align="right" id="insp_bud_cost"></td>
                                    <td align="right" id="insp_bud_per"></td>
                                    <td align="right" id="insp_ver_cost"></td>
                                    <td align="right" id="insp_ver_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>14</td><td>Certificate Cost</td>
                                    <td align="right" id="certi_qut_cost"></td>
                                    <td align="right" id="certi_qut_per"></td>
                                    <td align="right" id="certi_bud_cost"></td>
                                    <td align="right" id="certi_bud_per"></td>
                                    <td align="right" id="certi_ver_cost"></td>
                                    <td align="right" id="certi_ver_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>15</td><td>Operating Exp.</td>
                                    <td align="right" id="operating_qut_cost"></td>
                                    <td align="right" id="operating_qut_per"></td>
                                    <td align="right" id="operating_bud_cost"></td>
                                    <td align="right" id="operating_bud_per"></td>
                                    <td align="right" id="operating_ver_cost"></td>
                                    <td align="right" id="operating_ver_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>16</td><td>Courier Cost</td>
                                    <td align="right" id="courier_qut_cost"></td>
                                    <td align="right" id="courier_qut_per"></td>
                                    <td align="right" id="courier_bud_cost"></td>
                                    <td align="right" id="courier_bud_per"></td>
                                    <td align="right" id="courier_ver_cost"></td>
                                    <td align="right" id="courier_ver_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>17</td><td>CM Cost</td>
                                    <td align="right" id="cm_qut_cost"></td>
                                    <td align="right" id="cm_qut_per"></td>
                                    <td align="right" id="cm_bud_cost"></td>
                                    <td align="right" id="cm_bud_per"></td>
                                    <td align="right" id="cm_ver_cost"></td>
                                    <td align="right" id="cm_ver_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>18</td><td>Total Cost</td>
                                    <td align="right" id="cost_qut_cost"></td>
                                    <td align="right" id="cost_qut_per"></td>
                                    <td align="right" id="cost_bud_cost"></td>
                                    <td align="right" id="cost_bud_per"></td>
                                    <td align="right" id="cost_ver_cost"></td>
                                    <td align="right" id="cost_ver_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>19</td><td>Total Order Value</td>
                                    <td align="right" id="ordVal_qut_cost"></td>
                                    <td align="right" id="ordVal_qut_per"></td>
                                    <td align="right" id="ordVal_bud_cost"></td>
                                    <td align="right" id="ordVal_bud_per"></td>
                                    <td align="right" id="ordVal_ver_cost"></td>
                                    <td align="right" id="ordVal_ver_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>20</td><td>Profit/Loss </td>
                                    <td align="right" id="proLoss_qut_cost"></td>
                                    <td align="right" id="proLoss_qut_per"></td>
                                    <td align="right" id="proLoss_bud_cost"></td>
                                    <td align="right" id="proLoss_bud_per"></td>
                                    <td align="right" id="proLoss_ver_cost"></td>
                                    <td align="right" id="proLoss_ver_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>21</td><td>Expected Profit <div id="exp_prof_div"></div>%</td>
                                    <td align="right" id="expPro_qut_cost"></td>
                                    <td align="right" id="expPro_qut_per"></td>
                                    <td align="right" id="expPro_bud_cost"></td>
                                    <td align="right" id="expPro_bud_per"></td>
                                    <td align="right" id="expPro_ver_cost"></td>
                                    <td align="right" id="expPro_ver_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>22</td><td>Expt.Profit Variance </td>
                                    <td align="right" id="expProv_qut_cost"></td>
                                    <td align="right" id="expProv_qut_per"></td>
                                    <td align="right" id="expProv_bud_cost"></td>
                                    <td align="right" id="expProv_bud_per"></td>
                                    <td align="right" id="expProv_ver_cost"></td>
                                    <td align="right" id="expProv_ver_per"></td>
                                </tr>
                            </table>
                        </td>
                        <td colspan="5" style="min-height:800px; max-height:100%" align="center" valign="top"></td>
                    </tr>
                </table>
            </div>
            <br/>
            <h3 align="left" id="accordion_h2" style="width:5310px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel2', '')"> -Search Panel</h3>
        	<fieldset style="width:100%;" id="content_search_panel2">	
            <table width="5310">
                <tr class="form_caption">
                    <td align="left">Decimal rounded to 2 digit.</td>
                    <td colspan="50" align="center"><strong>Order Wise Budget Report</strong></td>
                </tr>
                <tr class="form_caption">
                    <td colspan="50" align="center"><strong><? echo $company_library[$company_name]; ?></strong></td>
                </tr>
                <tr class="form_caption">
                        <td align="left"><strong>Details Report </strong></td>
                </tr>
            </table>
            <? $asking_profit_head=$asking_profit_arr[$company_name]['asking_profit']; 
				if(str_replace("'","",$cbo_search_date)==1) $head_cap="Ship. Date";
				else if(str_replace("'","",$cbo_search_date)==2) $head_cap="PO Recv. Date";
				else $head_cap="PO Insert Date";
			?>
            <table id="table_header_1" class="rpt_table" width="5310" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr>
                        <th width="40" rowspan="2">SL</th>
                        <th width="70" rowspan="2">Buyer</th>
                        <th width="70" rowspan="2">Job No</th>
                        <th width="100" rowspan="2">Order No</th>
                        <th width="100" rowspan="2">Ready To Approve
                        	
                             <select name="cbo_ready_to" id="cbo_ready_to" class="combo_boxes flt" style="width:95px" onChange="fn_report_generated2(4);">
                                    <option value='0'> All </option>
									<?
                                    foreach ($yes_no as $key => $value) {
                                        ?>
                                        <option value=<?
                                        echo $key;
                                        if ($key == $cbo_ready_to) {
                                            ?> selected <?php } ?>><? echo "$value" ?> </option>
                                                <?
                                            }
                                            ?>
                                </select> 
                        </th>
                        <th width="80" rowspan="2">File No</th>
                        <th width="80" rowspan="2">Internal Ref: No</th>
                        <th width="100" rowspan="2">Order Status</th>
                        <th width="110" rowspan="2">Style</th>
                        <th width="110" rowspan="2">Item Name</th>
                        <th width="110" rowspan="2">Dealing</th>
                        <th width="70" rowspan="2"><? echo $head_cap; ?></th>
                        <th width="90" rowspan="2">Order Qty</th>
                        <th width="90" rowspan="2">Avg Unit Price</th>
                        <th width="100" rowspan="2">Order Value</th>
                        <th width="100" rowspan="2">Particulars</th>
                        <th colspan="14">Fabric Cost</th>
                        <th width="100" rowspan="2">Trim Cost</th>
                        <th colspan="5">Embell. Cost</th>
                        <th width="120" rowspan="2">Commercial Cost</th>
                        <th colspan="2">Commission</th>
                        <th width="100" rowspan="2">Testing Cost</th>
                        <th width="100" rowspan="2">Freight Cost</th>
                        <th width="120" rowspan="2">Inspection Cost</th>
                        <th width="100" rowspan="2">Certificate Cost</th>
                        <th width="100" rowspan="2">Commn OH</th>
                        <th width="100" rowspan="2">Courier Cost</th>
                        <th width="120" rowspan="2">CM/DZN</th>
                        <th width="100" rowspan="2">CM Cost</th>
                        <th width="100" rowspan="2">Total Cost</th>
                        <th width="100" rowspan="2">Profit/Loss</th>
                        <th width="100" rowspan="2">Profit/Loss %</th>
                        <th width="100" rowspan="2">Expected Profit</th>
                        <th width="100" rowspan="2">Expected Profit %</th>
                        <th width="100" rowspan="2">Expt. Profit Variance</th>
                        <th width="100" rowspan="2">SMV</th>
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
            <div style="width:5350px; max-height:400px; overflow-y:scroll" id="scroll_body">
            <table class="rpt_table" width="5310" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
            <? 
			$i=1; $total_order_qty=0;  $total_yarn_dyeing_cost=0; $total_yarn_cost=0; $total_order_value=0;$total_purchase_cost=0; $grand_tot_trims_cost=0; $total_fabric_dyeing_cost=0; $total_knitting_cost=0; $total_heat_setting_cost=0;$total_finishing_cost=0; $total_washing_cost=0; $fabric_dyeing_cost_dzn=0; $other_cost=0; $all_over_print_cost=0; $total_trim_cost=0; $total_commercial_cost=0;$p_total_yarn_cons=0;$total_yarn_cons=0;
			$total_smv_price=0;$total_smv_pre=0;$total_smv_vari=0;
			if($cbo_ready_to==1 || $cbo_ready_to==2)
			{
				$sql="select a.job_no_prefix_num,a.set_smv, b.insert_date, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.is_confirmed, a.quotation_id, a.agent_name, a.avg_unit_price, a.dealing_marchant, a.gmts_item_id, a.total_set_qnty as ratio, b.plan_cut, b.id as po_id, b.po_number, b.pub_shipment_date, b.po_received_date, b.po_quantity, b.unit_price, b.grouping, b.file_no, b.po_total_price from  wo_po_break_down b, wo_po_details_master a , wo_pre_cost_mst c   where a.job_no=b.job_no_mst and  a.job_no=c.job_no and c.job_no=b.job_no_mst and c.entry_from=158  and a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 $cbo_ready_to_cond $date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond $season_cond $internal_ref_cond $file_no_cond  order by b.pub_shipment_date, b.id";
			}
			else
			{
				$sql="select a.job_no_prefix_num,a.set_smv, b.insert_date, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.is_confirmed, a.quotation_id, a.agent_name, a.avg_unit_price, a.dealing_marchant, a.gmts_item_id, a.total_set_qnty as ratio, b.plan_cut, b.id as po_id, b.po_number, b.pub_shipment_date, b.po_received_date, b.po_quantity, b.unit_price, b.grouping, b.file_no, b.po_total_price from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c where a.job_no=b.job_no_mst and  a.job_no=c.job_no and c.job_no=b.job_no_mst and c.entry_from=158  and a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond $season_cond $internal_ref_cond $file_no_cond order by b.pub_shipment_date, b.id";
				 
			}
			 //echo  "10**".$sql; die;
			
			//echo $sql;$order_status_cond
			$result=sql_select($sql);
			$tot_rows=count($result);
			
           
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
				  $condition->country_ship_date(" between '$start_date' and '$end_date'");
			 }
			 if(str_replace("'","",$cbo_search_date) ==2 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
			 {
				 $condition->po_received_date(" between '$start_date' and '$end_date'");
				 //and b.po_received_date between '$start_date' and '$end_date' 
				// echo 'FFGG';
			 }
			
			 if(str_replace("'","",$cbo_search_date)==3 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
			 {
				
				
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
			$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
			$yarn= new yarn($condition);
			$yarn_req_qty_arr=$yarn->getOrderWiseYarnQtyArray();
			
			//print_r($yarn_req_qty_arr);
			$trims= new trims($condition);
			$trims_costing_arr=$trims->getAmountArray_by_order();

			$fabric= new fabric($condition);
			$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
			$conversion= new conversion($condition);
			$conversion_costing_arr_process=$conversion->getAmountArray_by_orderAndProcess();
			
			$emblishment= new emblishment($condition);
			$emblishment_costing_arr_name=$emblishment->getAmountArray_by_orderAndEmbname();
			$commission= new commision($condition);
			$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
			$commercial= new commercial($condition);
			$commercial_costing_arr=$commercial->getAmountArray_by_order();
			$other= new other($condition);
			$other_costing_arr=$other->getAmountArray_by_order();
			$wash= new wash($condition);
			$emblishment_costing_arr_name_wash=$wash->getAmountArray_by_orderAndEmbname();
			
			$knit_cost_arr=array(1,2,3,4);
			$fabric_dyeingCost_arr=array(25,31,26,32,60,61,62,63,72,80,81,84,85,86,87,38,39,74,78,79,101,133,137,138,139,146,147,149);
			$aop_cost_arr=array(35,36,37,40);
			$fab_finish_cost_arr=array(33,34,38,63,65,66,67,68,69,70,71,72,73,75,76,77,88,82,89,90,91,92,93,94,128,129,135,136,141,143,150,151,155,156,157,145,82,89,132,144);
			$washing_cost_arr=array(140,142,148,64);
			
			//echo $yarn->getQuery(); die;
			
			foreach($result as $row )
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				$order_value=$row[csf('po_total_price')];
				$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
				$plancut_value=$plan_cut_qnty*$row[csf('unit_price')];
				$order_qty_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
				if(str_replace("'","",$cbo_search_date)==1)
				{
					$ship_po_recv_date=change_date_format($row[csf('pub_shipment_date')]);
				}
				else if(str_replace("'","",$cbo_search_date)==2)
				{
					$ship_po_recv_date=change_date_format($row[csf('po_received_date')]);
				}
				else if(str_replace("'","",$cbo_search_date)==3)
				{
					$insert_date=explode(" ",$row[csf('insert_date')]);
					$ship_po_recv_date=change_date_format($insert_date[0]);
				}
				
				$dzn_qnty_p=0;
				$price_costing_per_id=$price_costing_perArray[$row[csf('quotation_id')]]['costing_per'];
				$smv_price=$price_costing_perArray[$row[csf('quotation_id')]]['smv'];
				if($price_costing_per_id==1) $dzn_qnty_p=12;
				else if($price_costing_per_id==3) $dzn_qnty_p=12*2;
				else if($price_costing_per_id==4) $dzn_qnty_p=12*3;
				else if($price_costing_per_id==5) $dzn_qnty_p=12*4;
				else $dzn_qnty_p=1;
				$dzn_qnty_p=$dzn_qnty_p*$row[csf('ratio')];
				
				$p_commercial_cost_dzn=$price_fabriccostArray[$row[csf('quotation_id')]]['comm_cost'];
				$p_commercial_cost=($p_commercial_cost_dzn/$dzn_qnty_p)*$order_qty_pcs;
				if(is_infinite($p_commercial_cost) || is_nan($p_commercial_cost)){$p_commercial_cost=0;}
				$price_fabricData=array_filter(explode(",",substr($price_fabric_data_Array[$row[csf('quotation_id')]],0,-1)));
				$p_fab_precost_Data=explode(",",substr($p_fab_precost_arr[$row[csf('quotation_id')]],0,-1));
				//echo $row[csf('quotation_id')];
				$quote_fab_source_id=""; $quote_color_type_id=""; $p_avg_rate=0; $p_yarn_costing=0; $p_fab_purchase=0;
				foreach($price_fabricData as $p_fabricRow)
				{
					$p_fabricRow=explode("**",$p_fabricRow);
					$p_fab_nature_id=$p_fabricRow[0];	
					$p_fab_source_id=$p_fabricRow[1];
					$p_fab_rate=$p_fabricRow[2];
					$p_yarn_qty=$p_fabricRow[3];
					$p_yarn_amount=$p_fabricRow[4];
					$p_color_type=$p_fabricRow[5];
					if($quote_fab_source_id=="") $quote_fab_source_id=$p_fab_source_id; else $quote_fab_source_id.=','.$p_fab_source_id;
					if($quote_color_type_id=="") $quote_color_type_id=$p_color_type; else $quote_color_type_id.=','.$p_color_type;
					if($p_fab_source_id==2)
					{
						foreach($p_fab_precost_Data as $p_fab_row)
						{
							
						}
					}
					else if($p_fab_source_id==1 || $p_fab_source_id==3)
					{
						$p_avg_rate=$p_yarn_amount/$p_yarn_qty;
						if(is_infinite($p_avg_rate) || is_nan($p_avg_rate)){$p_avg_rate=0;}
						//$p_yarn_costing=$p_yarn_amount/$dzn_qnty_p*$plan_cut_qnty;	
					}
				}
					foreach($p_fab_precost_Data as $p_fab_row)
						{
							$p_fab_dataRow=explode("**",$p_fab_row);
							$p_fab_requirment=$p_fab_dataRow[0];
							$p_fab_pcs=$p_fab_dataRow[1];
							$p_fab_rate=$p_fab_dataRow[2];
							$p_fab_sourceId=$p_fab_dataRow[3];
							
							$p_fab_purchase_qty=$p_fab_requirment/$dzn_qnty_p*$order_qty_pcs; 
						
							$p_fab_purchase+=$p_fab_purchase_qty*$p_fab_rate; 
							
								//echo $p_fab_row.'='.$p_fab_requirment.'*'.$dzn_qnty_p.'*'.$order_qty_pcs.'<br>';
						}
				
				$p_yarn_costing=($p_yarn_amount/$dzn_qnty_p)*$order_qty_pcs;
				if(is_infinite($p_yarn_costing) || is_nan($p_yarn_costing)){$p_yarn_costing=0;}
				$p_yarn_cons=($p_yarn_qty/$plan_cut_qnty)*$dzn_qnty_p;
				if(is_infinite($p_yarn_cons) || is_nan($p_yarn_cons)){$p_yarn_cons=0;}
				$p_yarn_cost_percent=($p_yarn_costing/$order_value)*100;
				if(is_infinite($p_yarn_cost_percent) || is_nan($p_yarn_cost_percent)){$p_yarn_cost_percent=0;}
				$p_kniting_cost=$pq_knit_arr[$row[csf('quotation_id')]]['knit']+$pq_knit_arr[$row[csf('quotation_id')]]['weaving']+$pq_knit_arr[$row[csf('quotation_id')]]['collar_cuff']+$pq_knit_arr[$row[csf('quotation_id')]]['feeder_stripe'];
				$p_knit_cost_dzn=$p_kniting_cost; 
				$p_knit_cost=($p_kniting_cost/$dzn_qnty_p)*$order_qty_pcs;
				if(is_infinite($p_knit_cost) || is_nan($p_knit_cost)){$p_knit_cost=0;}
				$p_yarn_dyeing_cost_dzn=$pq_knit_arr[$row[csf('quotation_id')]]['yarn_dyeing'];
				$p_yarn_dyeing_cost=($p_yarn_dyeing_cost_dzn/$dzn_qnty_p)*$plan_cut_qnty;
				if(is_infinite($p_yarn_dyeing_cost) || is_nan($p_yarn_dyeing_cost)){$p_yarn_dyeing_cost=0;}
				$p_fabric_dyeing_cost_dzn=$pq_knit_arr[$row[csf('quotation_id')]]['fabric_dyeing'];
				//echo $p_fabric_dyeing_cost_dzn.'='.$dzn_qnty_p.'*'.$order_qty_pcs;
				$p_fabric_dyeing_cost=($p_fabric_dyeing_cost_dzn/$dzn_qnty_p)*$order_qty_pcs;
				if(is_infinite($p_fabric_dyeing_cost) || is_nan($p_fabric_dyeing_cost)){$p_fabric_dyeing_cost=0;}
				$p_heat_setting_cost=($pq_knit_arr[$row[csf('quotation_id')]]['heat']/$dzn_qnty_p)*$order_qty_pcs;
				if(is_infinite($p_heat_setting_cost) || is_nan($p_heat_setting_cost)){$p_heat_setting_cost=0;}
				$p_fabric_finish=($pq_knit_arr[$row[csf('quotation_id')]]['fabric_finish']/$dzn_qnty_p)*$order_qty_pcs;
				if(is_infinite($p_fabric_finish) || is_nan($p_fabric_finish)){$p_fabric_finish=0;}
				$p_washing_cost=($pq_knit_arr[$row[csf('quotation_id')]]['washing']/$dzn_qnty_p)*$order_qty_pcs;
				if(is_infinite($p_washing_cost) || is_nan($p_washing_cost)){$p_washing_cost=0;}
				$p_all_over_cost=($pq_knit_arr[$row[csf('quotation_id')]]['all_over']/$dzn_qnty_p)*$order_qty_pcs;
				if(is_infinite($p_all_over_cost) || is_nan($p_all_over_cost)){$p_all_over_cost=0;}
				
				$ex_quote_fab_source_id=implode(',',array_unique(explode(",",$quote_fab_source_id)));
				$ex_quote_color_type_id=array_unique(explode(",",$quote_color_type_id));
				/*$fab_source_array=array(2,3,4);
				if(array_intersect($fab_source_array,$ex_quote_fab_source_id))
				{
					
				}*/$color_yarn=""; $color_pur=""; $color_knit_q="";
				if($ex_quote_fab_source_id=='1,2' || $ex_quote_fab_source_id=='2,1') 
				{
					if($p_yarn_costing<=0) $color_yarn="";
					if($p_fab_purchase<=0) $color_pur="red"; else $color_pur="";
					if($p_yarn_costing<=0) $color_knit_q="red"; else $color_knit_q="";
				}
				else if($ex_quote_fab_source_id==1 && $ex_quote_fab_source_id==2 ) 
				{
					if($p_yarn_costing<=0) $color_yarn="red"; else $color_yarn="";
					if($p_fab_purchase<=0) $color_pur="red"; else $color_pur="";
					if($p_knit_cost<=0) $color_knit_q="red"; else $color_knit_q="";	
				}
				else if($ex_quote_fab_source_id==1) 
				{
					if($p_yarn_costing<=0) $color_yarn="red"; else $color_yarn="";
					if($p_fab_purchase<=0) $color_pur="";
					if($p_knit_cost<=0) $color_knit_q="red"; else $color_knit_q="";	
				}
				else if($ex_quote_fab_source_id==2) 
				{
					if($p_yarn_costing<=0) $color_yarn="";
					if($p_fab_purchase<=0) $color_pur="red"; else $color_pur="";
					if($p_knit_cost<=0) $color_knit_q="red"; else $color_knit_q="";	
				}
				else 
				{
					if($p_yarn_costing<=0) $color_yarn="red"; else $color_yarn="";
					if($p_fab_purchase<=0) $color_pur="red"; else $color_pur="";
					if($p_knit_cost<=0) $color_knit_q="red"; else $color_knit_q="";
				}
				
				//echo $p_yarn_costing.'=='.$color_yarn;
				$color_fab_dy="";
				foreach($ex_quote_color_type_id as $color_type_id)
				{
					if($color_type_id==2 || $color_type_id==3  || $color_type_id==4  || $color_type_id==6)
					{
						if($p_fabric_dyeing_cost<=0) if ($color_fab_dy=="") $color_fab_dy=""; else $color_fab_dy.=','."";
					}
					else
					{
						if($p_fabric_dyeing_cost<=0)  if ($color_fab_dy=="") $color_fab_dy="red"; else $color_fab_dy.=','."red"; else $color_fab_dy="";
					}
				}
				$ex_quote_color_type=implode(',',array_unique(explode(",",$color_fab_dy)));
				
				if($p_fabric_finish<=0) $color_finish="red"; else $color_finish="";	
				if($p_commercial_cost<=0) $color_com="red"; else $color_com="";	
				$p_trim_amount= $price_fabriccostArray[$row[csf('quotation_id')]]['trims_cost']/$dzn_qnty_p*$order_qty_pcs;
				if(is_infinite($p_trim_amount) || is_nan($p_trim_amount)){$p_trim_amount=0;}
				$p_print_amount=($price_fab_emb[$row[csf('quotation_id')]]['print']/$dzn_qnty_p)*$order_qty_pcs;
				if(is_infinite($p_print_amount) || is_nan($p_print_amount)){$p_print_amount=0;}
				$p_embroidery_amount=($price_fab_emb[$row[csf('quotation_id')]]['embroidery']/$dzn_qnty_p)*$order_qty_pcs;
				if(is_infinite($p_embroidery_amount) || is_nan($p_embroidery_amount)){$p_embroidery_amount=0;}
				$p_special_amount=($price_fab_emb[$row[csf('quotation_id')]]['special']/$dzn_qnty_p)*$order_qty_pcs;
				if(is_infinite($p_special_amount) || is_nan($p_special_amount)){$p_special_amount=0;}
				$p_wash_amt=($price_fab_emb[$row[csf('quotation_id')]]['wash']/$dzn_qnty_p)*$order_qty_pcs;
				if(is_infinite($p_wash_amt) || is_nan($p_wash_amt)){$p_wash_amt=0;}
				$p_other_amount=($price_fab_emb[$row[csf('quotation_id')]]['other']/$dzn_qnty_p)*$order_qty_pcs;
				if(is_infinite($p_other_amount) || is_nan($p_other_amount)){$p_other_amount=0;}
				$p_foreign=$price_commission_array[$row[csf('quotation_id')]]['foreign']/$dzn_qnty_p*$order_qty_pcs;
				if(is_infinite($p_foreign) || is_nan($p_foreign)){$p_foreign=0;}
				$p_local=$price_commission_array[$row[csf('quotation_id')]]['local']/$dzn_qnty_p*$order_qty_pcs;
				if(is_infinite($p_local) || is_nan($p_local)){$p_local=0;}
				$p_test_cost=$price_fabriccostArray[$row[csf('quotation_id')]]['lab_test']/$dzn_qnty_p*$order_qty_pcs;
				if(is_infinite($p_test_cost) || is_nan($p_test_cost)){$p_test_cost=0;}
				$p_freight_cost= $price_fabriccostArray[$row[csf('quotation_id')]]['freight']/$dzn_qnty_p*$order_qty_pcs;
				if(is_infinite($p_freight_cost) || is_nan($p_freight_cost)){$p_freight_cost=0;}
				$p_inspection=$price_fabriccostArray[$row[csf('quotation_id')]]['inspection']/$dzn_qnty_p*$order_qty_pcs;
				if(is_infinite($p_inspection) || is_nan($p_inspection)){$p_inspection=0;}
				$p_certificate_cost=$price_fabriccostArray[$row[csf('quotation_id')]]['certificate_pre_cost']/$dzn_qnty_p*$order_qty_pcs;
				if(is_infinite($p_certificate_cost) || is_nan($p_certificate_cost)){$p_certificate_cost=0;}
				$p_common_oh=$price_fabriccostArray[$row[csf('quotation_id')]]['common_oh']/$dzn_qnty_p*$order_qty_pcs;
				if(is_infinite($p_common_oh) || is_nan($p_common_oh)){$p_common_oh=0;}
				$p_currier_cost=$price_fabriccostArray[$row[csf('quotation_id')]]['currier_pre_cost']/$dzn_qnty_p*$order_qty_pcs;
				if(is_infinite($p_currier_cost) || is_nan($p_currier_cost)){$p_currier_cost=0;}
				$p_cm_cost_dzn=$price_fabriccostArray[$row[csf('quotation_id')]]['c_cost'];
				$p_cm_cost=$p_cm_cost_dzn/$dzn_qnty_p*$order_qty_pcs;
				if(is_infinite($p_cm_cost) || is_nan($p_cm_cost)){$p_cm_cost=0;}
				
				$total_p_cost=$p_yarn_costing+$p_fab_purchase+$p_knit_cost+$p_washing_cost+$p_all_over_cost+$p_yarn_dyeing_cost+$p_fabric_dyeing_cost+$p_heat_setting_cost+$p_fabric_finish+$p_trim_amount+$p_test_cost+$p_print_amount+$p_embroidery_amount+$p_special_amount+$p_other_amount+$p_wash_amt+$p_commercial_cost+$p_foreign+$p_local+$p_freight_cost+$p_inspection+$p_certificate_cost+$p_common_oh+$p_currier_cost+$p_cm_cost;
			 $total_p_cost=number_format($total_p_cost,2,".", "");
				
				if($p_trim_amount<=0) $color_trim="red"; else $color_trim="";	
				if($p_cm_cost<=0) $color="red"; else $color="";
				
				$max_profit=$asking_profit_arr[$row[csf('company_name')]]['max_profit'];
				$company_asking=$costing_date_arr[$row[csf('job_no')]]['ask'];
				
				$total_p_profit=$order_value-$total_p_cost;
				$total_p_profit_percentage2=$total_p_profit/$order_value*100; 
				if(is_infinite($total_p_profit_percentage2) || is_nan($total_p_profit_percentage2)){$total_p_profit_percentage2=0;}

				if($total_p_profit_percentage2<=0 ) $color_pl="red";
				else if($total_p_profit_percentage2>$max_profit) $color_pl="yellow";	
				else if($total_p_profit_percentage2<=$max_profit) $color_pl="green";	
				else $color_pl="";	
				$p_expected_profit=$company_asking*$order_value/100;
				if(is_infinite($p_expected_profit) || is_nan($p_expected_profit)){$p_expected_profit=0;}
				$tot_expect_variance=$total_p_profit-$p_expected_profit; 
				$ready_to_approved=$job_approve_arr[$row[csf('job_no')]]['ready_to_approved'];
				if($ready_to_approved==0 || $ready_to_approved==2) $ready_to_approved=2;else $ready_to_approved=$ready_to_approved;
				?>
                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                    <td width="40" rowspan="4"><? echo $i; ?></td>
                    <td width="70" rowspan="4"><p><? echo $buyer_library[$row[csf('buyer_name')]] ?></p></td>
                    <td width="70" rowspan="4"><p><? echo $row[csf('job_no_prefix_num')];  ?></p></td>
                    <td width="100" rowspan="4"><p><? echo $row[csf('po_number')]; ?></p></td>
                    <td width="100" rowspan="4"><p><? echo $yes_no[$ready_to_approved]; ?></p></td>
                    <td width="80" rowspan="4"><p><? echo $row[csf('file_no')]; ?></p></td>
                    <td width="80" rowspan="4"><p><? echo $row[csf('grouping')]; ?></p></td>
                    <td width="100" rowspan="4"><p><? echo  $order_status[$row[csf('is_confirmed')]]; ?></p></td>
                    <td width="110" rowspan="4"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                    <td width="110" rowspan="4"><p><? $gmts_item=''; $gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
						foreach($gmts_item_id as $item_id)
						{
							if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=", ".$garments_item[$item_id];
						}
						echo $gmts_item; ?></p></td>
                    <td rowspan="4" width="110"><p><? echo $team_member_arr[$row[csf('dealing_marchant')]]; ?></p></td>
                    <td rowspan="4" width="70"><p><? echo '&nbsp;'.$ship_po_recv_date; ?></p></td>
                    <td rowspan="4" width="90" align="right"><p><? echo number_format($row[csf('po_quantity')],2); ?></p></td>
                    <td rowspan="4" width="90" align="right"><p><? echo number_format($row[csf('unit_price')],4); ?></p></td>
                    <td rowspan="4" width="100" align="right"><p><? echo number_format($order_value,2); ?></p></td>
                    <td width="100">Price Quotation</td>
                    <td width="100" align="right"><a href="##" onClick="generate_pricecost_yarnavg_popup('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('quotation_id')]; ?>','pricost_yarnavg_detail')"><? echo number_format($p_avg_rate,2); ?></a></td>
                    <td width="80" align="right" bgcolor="<? echo $color_yarn; ?>" title="<? echo $p_yarn_costing; ?>"><? echo number_format($p_yarn_costing,2); ?></td>
                    <td width="80" align="right" title="<? echo $p_yarn_cost_percent.'%'; ?>"><? echo number_format($p_yarn_cost_percent,2).'%'; ?></td>
                    <td width="100" align="right" bgcolor="<? echo $color_pur; ?>"><a href="##" onClick="generate_pricecost_purchase_popup('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $p_fab_source_id; ?>','<? echo $row[csf('quotation_id')]; ?>','fab_price_purchase_detail')"><? echo number_format($p_fab_purchase,2); ?></a></td>
                    <td width="80" title="<? echo $p_knit_cost_dzn; ?>" align="right"><? echo number_format($p_knit_cost_dzn,2); ?></td>
                    <td width="80" align="right" bgcolor="<? echo $color_knit_q; ?>"><a href="##" onClick="generate_pri_cost_knit_popup('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $cons_process; ?>','<? echo $row[csf('quotation_id')]; ?>','pricost_knit_detail')"><? echo number_format($p_knit_cost,2); ?></a></td>
                    <td width="100" align="right"><? echo number_format($p_yarn_dyeing_cost_dzn ,2); ?></td>
                    <td width="110" align="right"><? echo number_format($p_yarn_dyeing_cost ,2); ?></td>
                    <td width="120" align="right"><? echo number_format($p_fabric_dyeing_cost_dzn,2); ?></td>
                    <td width="100" align="right" bgcolor="<? echo $ex_quote_color_type; ?>"><a href="##" onClick="generate_pricost_fab_dyeing_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $fab_source_id; ?>','<? echo $row[csf('quotation_id')];?>','fab_price_dyeing_detail')"><? echo number_format($p_fabric_dyeing_cost,2); ?></a></td>
                    <td width="90" align="right"><? echo number_format($p_heat_setting_cost,2); ?></td>
                    <td width="100" align="right"><a href="##" onClick="generate_pricost_fab_finishing_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('quotation_id')]; ?>','fab_price_finishing_detail')"><? echo number_format($p_fabric_finish,2); ?></a></td>
                    <td width="90" align="right"><a href="##" onClick="generate_pricost_fab_finishing_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('quotation_id')]; ?>','fab_price_washing_detail')"><? echo number_format($p_washing_cost,2); ?></a></td>
                    <td  width="90" align="right"><a href="##" onClick="generate_pricost_fab_all_over_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('quotation_id')]; ?>','fab_price_all_over_detail')"><? echo number_format($p_all_over_cost,2); ?></a></td>
                    <td width="100" align="right" bgcolor="<? echo $color_trim; ?>"><a href="##" onClick="generate_pricost_trim_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('quotation_id')]; ?>','trim_cost_price_detail')"><? echo number_format($p_trim_amount,2); ?></a></td>
                    <td width="80" align="right"><a href="##" onClick="generate_pricost_embell_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('quotation_id')]; ?>','price_print_cost_detail')"><? echo number_format($p_print_amount,2); ?></a></td>
                    <td width="85" align="right"><a href="##" onClick="generate_pricost_embell_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('quotation_id')]; ?>','price_embroidery_cost_detail')"><? echo number_format($p_embroidery_amount,2); ?></a></td>
                    <td width="80" align="right"><? echo number_format($p_special_amount,2); ?></td>
                    <td width="80" align="right"><a href="##" onClick="generate_pricost_embell_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('quotation_id')]; ?>','price_wash_cost_detail')"><? echo number_format($p_wash_amt,2); ?></a></td>
                    <td width="80" align="right"><? echo number_format($p_other_amount,2); ?></td>
                    <td width="120" align="right" bgcolor="<? echo $color_com; ?>"><? echo number_format($p_commercial_cost,2); ?></td>
                    <td width="120" align="right"><? echo number_format($p_foreign,2) ?></td>
                    <td width="120" align="right"><? echo number_format($p_local,2) ?></td>
                    <td width="100" align="right"><? echo number_format($p_test_cost,2);?></td>
                    <td width="100" align="right"><? echo number_format($p_freight_cost,2); ?></td>
                    <td width="120" align="right"><? echo number_format($p_inspection,2);?></td>
                    <td width="100" align="right"><? echo number_format($p_certificate_cost,2); ?></td>
                    <td width="100" align="right"><? echo number_format($p_common_oh,2); ?></td>
                    <td width="100" align="right"><? echo number_format($p_currier_cost,2);?></td>
                    <td width="120" align="right"><? echo number_format($p_cm_cost_dzn,2);?></td>
                    <td width="100" align="right" bgcolor="<? echo $color; ?>"><? echo number_format($p_cm_cost,2);?></td>
                    <td width="100" align="right"><? echo number_format($total_p_cost,2); ?></td>
                    <td width="100" align="right" bgcolor="<? echo $color_pl; ?>"><? echo number_format($total_p_profit,2); ?></td>
                    <td width="100" align="right"><? echo number_format($total_p_profit_percentage2,2).'%'; ?></td>
                    <td width="100" align="right"><? echo number_format($p_expected_profit,2); ?></td>
                    <td width="100" align="right"><? echo number_format($company_asking,2); ?></td>
                    <td align="right" width="100"><? echo number_format($tot_expect_variance,2)?></td>
                    <td align="right" width="100"><? $total_smv_price+=$smv_price;echo $smv_price; ?></td>
                     <td align="right" width=""><? echo number_format($p_yarn_cons,2); ?></td>
                </tr>
                	<?
                    $dzn_qnty=0;
                    $costing_per_id=$fabriccostArray[$row[csf('job_no')]]['costing_per_id'];
                    if($costing_per_id==1) $dzn_qnty=12;
                    else if($costing_per_id==3) $dzn_qnty=12*2;
                    else if($costing_per_id==4) $dzn_qnty=12*3;
                    else if($costing_per_id==5) $dzn_qnty=12*4;
                    else $dzn_qnty=1;
					$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
					
                    $commercial_cost_dzn=$fabriccostArray[$row[csf('job_no')]]['comm_cost'];
                    $commercial_cost=($commercial_cost_dzn/$dzn_qnty)*$order_qty_pcs;
					if(is_infinite($commercial_cost) || is_nan($commercial_cost)){$commercial_cost=0;}
                    $fabricData=explode(",",substr($fabric_data_Array[$row[csf('job_no')]],0,-1));
                    $fab_precost_Data=explode(",",substr($fab_precost_arr[$row[csf('job_no')]][$row[csf('po_id')]],0,-1));
					$pre_fab_source_id=""; $pre_color_type_id=""; $yarn_costing=0; $avg_rate=0; $fab_purchase=0; $yarn_cost_percent=0;
                    foreach($fabricData as $fabricRow)
                    {
						$fabricRow=explode("**",$fabricRow);
						$fab_source_id=$fabricRow[1];
						if($pre_fab_source_id=="") $pre_fab_source_id=$fab_source_id; else $pre_fab_source_id.=','.$fab_source_id;
						
                    }
					
					$fab_purchase_knit=array_sum($fabric_costing_arr['knit']['grey'][$row[csf('po_id')]]);
					if(is_infinite($fab_purchase_knit) || is_nan($fab_purchase_knit)){$fab_purchase_knit=0;}
					$fab_purchase_woven=array_sum($fabric_costing_arr['woven']['grey'][$row[csf('po_id')]]);
					if(is_infinite($fab_purchase_woven) || is_nan($fab_purchase_woven)){$fab_purchase_woven=0;}
					$fab_purchase=$fab_purchase_knit+$fab_purchase_woven;

					$yarn_costing=$yarn_costing_arr[$row[csf('po_id')]];
					$yarn_cons=($yarn_req_qty_arr[$row[csf('po_id')]]/$plan_cut_qnty)*$dzn_qnty;
					if(is_infinite($yarn_cons) || is_nan($yarn_cons)){$yarn_cons=0;}
					$yarn_cost_percent=($yarn_costing/$order_value)*100;
					if(is_infinite($yarn_cost_percent) || is_nan($yarn_cost_percent)){$yarn_cost_percent=0;}
					$avg_rate=$yarn_costing/$yarn_req_qty_arr[$row[csf('po_id')]];
					if(is_infinite($avg_rate) || is_nan($avg_rate)){$avg_rate=0;}
					$knit_cost=0;
						foreach($knit_cost_arr as $process_id)
						{
							$knit_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$process_id]);	
						}
					
					$knit_cost_dzn=($knit_cost/$plan_cut_qnty)*12;
					if(is_infinite($knit_cost_dzn) || is_nan($knit_cost_dzn)){$knit_cost_dzn=0;}
					$fabric_dyeing_cost=0;
					foreach($fabric_dyeingCost_arr as $fab_process_id)
					{
						$fabric_dyeing_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$fab_process_id]);	
					}
					$fabric_dyeing_cost_dzn=($fabric_dyeing_cost/$plan_cut_qnty)*12;
					if(is_infinite($fabric_dyeing_cost_dzn) || is_nan($fabric_dyeing_cost_dzn)){$fabric_dyeing_cost_dzn=0;}
						
				
					$yarn_dyeing_cost=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][30]);
					$yarn_dyeing_cost_dzn=($yarn_dyeing_cost/$plan_cut_qnty)*12;
					if(is_infinite($yarn_dyeing_cost_dzn) || is_nan($yarn_dyeing_cost_dzn)){$yarn_dyeing_cost_dzn=0;}
					
					$fabric_finish=0;
					foreach($fab_finish_cost_arr as $fin_process_id)
					{
						$fabric_finish+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$fin_process_id]);	
					}
					
					$all_over_cost=0;
					foreach($aop_cost_arr as $aop_process_id)
					{
						$all_over_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$aop_process_id]);	
					}
					
					$washing_cost=0;
					foreach($washing_cost_arr as $w_process_id)
					{
						$washing_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$w_process_id]);	
					}
						

					$heat_setting_cost=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][33]);
                    
					$ex_pre_fab_source_id=implode(',',array_unique(explode(",",$pre_fab_source_id)));
					$ex_pre_color_type_id=array_unique(explode(",",$pre_color_type_id));
					$color_yarn_p=""; $color_purchase=""; $color_knit="";
					if($ex_pre_fab_source_id=='1,2'  || $ex_pre_fab_source_id=='2,1')
					{
						if($yarn_costing<=0) $color_yarn_p="red"; else $color_yarn_p="";
						if($fab_purchase<=0) $color_purchase="red"; else $color_purchase="";
						if($knit_cost<=0) $color_knit="red"; else $color_knit="";
					}
					else if($ex_pre_fab_source_id==1 && $ex_pre_fab_source_id==2 ) 
					{
						if($yarn_costing<=0) $color_yarn_p="red"; else $color_yarn_p="";
						if($fab_purchase<=0) $color_purchase="red"; else $color_purchase="";
						if($knit_cost<=0) $color_knit="red"; else $color_knit="";
					}
					else if($ex_pre_fab_source_id==1) 
					{
						if($yarn_costing<=0) $color_yarn_p="";
						if($fab_purchase<=0) $color_purchase="";
						if($knit_cost<=0) $color_knit="red"; else $color_knit="";
					}
					else if($ex_pre_fab_source_id==2) 
					{
						if($yarn_costing<=0) $color_yarn_p="red"; else $color_yarn_p="";
						if($fab_purchase<=0) $color_purchase="red"; else $color_purchase="";
						if($knit_cost<=0) $color_knit="red"; else $color_knit="";
					}
					else 
					{
						if($yarn_costing<=0) $color_yarn_p="red"; else $color_yarn_p="";
						if($fab_purchase<=0) $color_purchase="red"; else $color_purchase="";
						if($knit_cost<=0) $color_knit="red"; else $color_knit="";
					}
					//echo $ex_pre_fab_source_id;
					
					$color_fab_d="";
					foreach($ex_pre_color_type_id as $colorType_id)
					{
						if($colorType_id==2 || $colorType_id==3  || $colorType_id==4  || $colorType_id==6)
						{
							if($fabric_dyeing_cost<=0) if ($color_fab_d=="") $color_fab_d=""; else $color_fab_d.=','."";
						}
						else
						{
							if($fabric_dyeing_cost<=0)  if ($color_fab_d=="") $color_fab_d="red"; else $color_fab_d.=','."red"; else $color_fab_d="";
						}
					}
					
					$ex_pre_color_type=implode(',',array_unique(explode(",",$color_fab_d)));

                    if($fabric_finish<=0) $color_finish="red"; else $color_finish="";	
                    if($commercial_cost<=0) $color_com="red"; else $color_com="";	
					//$trim_qty_arr[$row[csf('po_id')]];//
					$trim_amount= $trims_costing_arr[$row[csf('po_id')]];
					if(is_infinite($trim_amount) || is_nan($trim_amount)){$trim_amount=0;}

					$print_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][1];
					if(is_infinite($print_amount) || is_nan($print_amount)){$print_amount=0;}
					$embroidery_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][2];
					if(is_infinite($embroidery_amount) || is_nan($embroidery_amount)){$embroidery_amount=0;}
					$special_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][4];
					if(is_infinite($special_amount) || is_nan($special_amount)){$special_amount=0;}
					$wash_amount=$emblishment_costing_arr_name_wash[$row[csf('po_id')]][3];
					if(is_infinite($wash_amount) || is_nan($wash_amount)){$wash_amount=0;}
					$other_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][5];
					if(is_infinite($other_amount) || is_nan($other_amount)){$other_amount=0;}
					$foreign=$commission_costing_arr[$row[csf('po_id')]][1];
					if(is_infinite($foreign) || is_nan($foreign)){$foreign=0;}
					$local=$commission_costing_arr[$row[csf('po_id')]][2];
					if(is_infinite($local) || is_nan($local)){$local=0;}
					$test_cost=$other_costing_arr[$row[csf('po_id')]]['lab_test'];
					if(is_infinite($test_cost) || is_nan($test_cost)){$test_cost=0;}
					$freight_cost=$other_costing_arr[$row[csf('po_id')]]['freight'];
					if(is_infinite($freight_cost) || is_nan($freight_cost)){$freight_cost=0;}
					$inspection=$other_costing_arr[$row[csf('po_id')]]['inspection'];
					if(is_infinite($inspection) || is_nan($inspection)){$inspection=0;}
					$certificate_cost=$other_costing_arr[$row[csf('po_id')]]['certificate_pre_cost'];
					if(is_infinite($certificate_cost) || is_nan($certificate_cost)){$certificate_cost=0;}
					$common_oh=$other_costing_arr[$row[csf('po_id')]]['common_oh'];
					if(is_infinite($common_oh) || is_nan($common_oh)){$common_oh=0;}
					$currier_cost=$other_costing_arr[$row[csf('po_id')]]['currier_pre_cost'];
					if(is_infinite($currier_cost) || is_nan($currier_cost)){$currier_cost=0;}
					
					$cm_cost=$other_costing_arr[$row[csf('po_id')]]['cm_cost'];
					if(is_infinite($cm_cost) || is_nan($cm_cost)){$cm_cost=0;}
					$cm_cost_dzn=($cm_cost/$order_qty_pcs)*12;
					if(is_infinite($cm_cost_dzn) || is_nan($cm_cost_dzn)){$cm_cost_dzn=0;}
					
					//$yarn_dyeing_cost_dzn=($yarn_dyeing_cost/$plan_cut_qnty)*12;
                  
                    
                    $total_cost=$yarn_costing+$fab_purchase+$knit_cost+$washing_cost+$all_over_cost+$yarn_dyeing_cost+$fabric_dyeing_cost+$heat_setting_cost+$fabric_finish+$trim_amount+$test_cost+$print_amount+$embroidery_amount+$special_amount+$wash_amount+$other_amount+$commercial_cost+$foreign+$local+$freight_cost+$inspection+$certificate_cost+$common_oh+$currier_cost+$cm_cost;
                    
                    if($trim_amount<=0) $color_trim="red"; else $color_trim="";	
                    if($cm_cost<=0) $color="red"; else $color="";	
					
                    $total_profit=$order_value-$total_cost;
                    $total_profit_percentage=$total_profit/$order_value*100; 
                    if($total_profit_percentage<=0 ) $color_pl="red";
                    else if($total_profit_percentage>$max_profit) $color_pl="yellow";	
                    else if($total_profit_percentage<=$max_profit) $color_pl="green";	
                    else $color_pl="";	
                    
					$expected_profit=$company_asking*$order_value/100;
					$expect_variance=$total_profit-$expected_profit;
                   ?>
                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trp_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trp_<? echo $i; ?>">
                    <td width="100">Pre Cost</td>
                    <td width="100" align="right"><a href="##" onClick="generate_pre_cost_report('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','precost_yarn_detail')"><? echo  number_format($avg_rate,2); ?></a></td>
                    <td width="80" align="right" bgcolor="<? echo $color_yarn_p; ?>"><? echo number_format($yarn_costing,2); ?></td>
                    <td width="80" align="right"><? echo number_format($yarn_cost_percent,2).'%'; ?></td>
                    <td width="100" align="right" bgcolor="<? echo $color_purchase; ?>"><a href="##" onClick="generate_precost_fab_purchase_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $fab_source_id; ?>','fab_purchase_detail')"><? echo number_format($fab_purchase,2); ?></a></td>
                    <td width="80" title="<? echo $knit_cost_dzn; ?>" align="right"><? echo number_format($knit_cost_dzn,2); ?></td>
                    <td width="80" align="right" bgcolor="<? echo $color_knit; ?>"><a href="##" onClick="generate_pre_cost_knit_popup('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $cons_process; ?>','precost_knit_detail')"><? echo number_format($knit_cost,2); ?></a></td>
                    <td width="100" align="right"><? echo number_format($yarn_dyeing_cost_dzn ,2); ?></td>
                    <td width="110" align="right" bgcolor="<? //echo $color_fab_yd; ?>"><? echo number_format($yarn_dyeing_cost ,2); ?></td>
                    <td width="120" align="right"><? echo number_format($fabric_dyeing_cost_dzn,2); ?></td>
                    <td width="100" align="right" bgcolor="<? echo $ex_pre_color_type; ?>"><a href="##" onClick="generate_precost_fab_dyeing_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $fab_source_id; ?>','fab_dyeing_detail')"><? echo number_format($fabric_dyeing_cost,2); ?></a></td>
                    <td width="90" align="right"><? echo number_format($heat_setting_cost,2); ?></td>
                    <td width="100" align="right" ><a href="##" onClick="generate_precost_fab_finishing_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','fab_finishing_detail')"><? echo number_format($fabric_finish,2); ?></a> </td>
                    <td  width="90" align="right"><a href="##" onClick="generate_precost_fab_finishing_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','fab_washing_detail')"><? echo number_format($washing_cost,2); ?></a></td>
                    <td  width="90" align="right"><a href="##" onClick="generate_precost_fab_all_over_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','fab_all_over_detail')"><? echo number_format($all_over_cost,2); ?></a></td>
                    <td width="100" align="right" bgcolor="<? echo $color_trim; ?>"><a href="##" onClick="generate_precost_trim_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','trim_cost_detail')"><? echo number_format($trim_amount,2); ?></a><? //echo  number_format($trim_amount,2);?></td>
                    <td width="80" align="right"><a href="##" onClick="generate_precost_embell_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','print_cost_detail')"><? echo number_format($print_amount,2); ?></a></td>
                    <td width="85" align="right"><a href="##" onClick="generate_precost_embell_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','embroidery_cost_detail')"><? echo number_format($embroidery_amount,2); ?></a></td>
                    <td width="80" align="right"><? echo number_format($special_amount,2); ?></td>
                    <td width="80" align="right"><a href="##" onClick="generate_precost_embell_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','wash_cost_detail')"><? echo number_format($wash_amount,2); ?></a></td>
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
                    <td width="120" align="right"  title="<? echo 'CM Dzn=CM Cost/PO Qty Pcs*12';?>" ><? echo number_format($cm_cost_dzn,2);?></td>
                    <td width="100" align="right" bgcolor="<? echo $color; ?>"><? echo number_format($cm_cost,2);?></td>
                    <td width="100" align="right"><? echo number_format($total_cost,2); ?></td>
                    <td width="100" align="right" bgcolor="<? echo $color_pl; ?>"><? echo number_format($total_profit,2); ?></td>
                    <td width="100" align="right"><? echo number_format($total_profit_percentage,2).'%'; ?></td>
                    <td width="100" align="right"><? echo number_format($expected_profit,2); ?></td>
                    <td width="100" align="right"><? echo number_format($company_asking,2); ?></td>
                    <td align="right"  width="100"><? echo number_format($expect_variance,2)?></td>
                    <td width="100" align="right"><? $total_smv_pre+=$row[csf('set_smv')];echo number_format($row[csf('set_smv')],2); ?></td>
                    <td align="right" width=""><? echo number_format($yarn_cons,2); ?></td>
                </tr> 
                <?
                $avg_rate_variance=$p_avg_rate-$avg_rate;
				$yarn_variance=$p_yarn_costing-$yarn_costing;
				$yarn_cons_variance=$p_yarn_cons-$yarn_cons;
				$yarn_cost_percent_variance=$p_yarn_cost_percent-$yarn_cost_percent;
                $fab_purchase_variance=$p_fab_purchase-$fab_purchase;
                $knit_cost_dzn_variance=$p_knit_cost_dzn-$knit_cost_dzn;
                $knit_cost_variance=$p_knit_cost-$knit_cost;
                $yarn_dyeing_cost_dzn_vairance=$p_yarn_dyeing_cost_dzn-$yarn_dyeing_cost_dzn ;
                $yarn_dyeing_cost_variance=$p_yarn_dyeing_cost-$yarn_dyeing_cost;
                $fabric_dyeing_cost_dzn_variance=$p_fabric_dyeing_cost_dzn-$fabric_dyeing_cost_dzn;
                $fabric_dyeing_cost_variance=$p_fabric_dyeing_cost-$fabric_dyeing_cost;
                $heat_setting_cost_variance=$p_heat_setting_cost-$heat_setting_cost;
                $fabric_finish_variance_cost=$p_fabric_finish-$fabric_finish;
                $washing_cost_variance=$p_washing_cost-$washing_cost;
                $all_over_variance=$p_all_over_cost-$all_over_cost;
				
				if($trim_amount<=0) $color_trim="red"; else $color_trim="";	
				if($cm_cost<=0) $color="red"; else $color="";	
				
				$trim_cost_variance=$p_trim_amount-$trim_amount;
				$print_varinace=$p_print_amount-$print_amount;
				$embrodery_variance=$p_embroidery_amount-$embroidery_amount;
				$special_variance=$p_special_amount-$special_amount;
				$wash_amount_variance=$p_wash_amt-$wash_amount;
				$other_amount_variance=$p_other_amount-$other_amount;
				$commercial_variance=$p_commercial_cost-$commercial_cost;
				$foreign_variance=$p_foreign-$foreign;
				$local_variance=$p_local-$local;
				$test_variance=$p_test_cost-$test_cost;
				$feight_variance=$p_freight_cost-$freight_cost;
				$inspection_variance=$p_inspection-$inspection;
				$certificate_variance=$p_certificate_cost-$certificate_cost;
				$common_variance=$p_common_oh-$common_oh;
				$currier_variance=$p_currier_cost-$currier_cost;
				$cm_dzn_variance=$p_cm_cost_dzn-$cm_cost_dzn;
				$cm_variance=$p_cm_cost-$cm_cost;
				$total_cost_varaince=$total_p_cost-$total_cost;
				
				if($fabric_dyeing_cost_variance<=0) $color_fab_v="red"; else $color_fab_v="";
				if($yarn_dyeing_cost_variance<=0) $color_fab_dy_v="red"; else $color_fab_dy_v="";	
				if($yarn_variance<=0) $color_yarn_v="red"; else $color_yarn_v="";	
				if($knit_cost_variance<=0) $color_knit_v="red"; else $color_knit_v="";	
				if($fabric_finish<=0) $color_finish="red"; else $color_finish="";
				if($commercial_variance<=0) $color_com_v="red"; else $color_com_v="";	
				if($trim_cost_variance<=0) $color_trim_v="red"; else $color_trim_v="";
				if($cm_variance<=0) $color_cm_v="red"; else $color_cm_v="";
		
				$total_profit=$order_value-$total_cost;
				$total_profit_percentage=$total_profit/$order_value*100; 
				if($total_profit_percentage<=0 ) $color_pl="red";
				else if($total_profit_percentage>$max_profit) $color_pl="yellow";	
				else if($total_profit_percentage<=$max_profit) $color_pl="green";	
				else $color_pl="";	
				
				$tot_profit_variance=$total_profit-$total_p_profit;
				$tot_profit_percient_varaince=$total_profit_percentage-$total_p_profit_percentage2;
				$tot_expected_profit=$expected_profit-$p_expected_profit;
				$tot_expected_varaince_data_vairance=$expect_variance-$tot_expect_variance;
				
				//$max_profit=$asking_profit_arr[$row[csf('company_name')]]['max_profit'];
				//echo $max_profit;
				//$company_asking=$asking_profit_arr[$row[csf('company_name')]]['asking_profit'];
				?>
                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trv_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trv_<? echo $i; ?>">
                    <td width="100">Variance</td>
                    <td width="100" align="right"><? echo number_format($avg_rate_variance,2); ?></td>
                    <td width="80" align="right" bgcolor="<? //echo $color_yarn_v; ?>"><? echo number_format($yarn_variance,2); ?></td>
                    <td width="80" align="right"><? echo number_format($yarn_cost_percent_variance,2).'%'; ?></td>
                    <td width="100" align="right"><? echo number_format($fab_purchase_variance,2); ?></td>
                    <td width="80" align="right"><? echo number_format($knit_cost_dzn_variance,2); ?></td>
                    <td width="80" align="right" bgcolor="<? //echo $color_knit_v; ?>"><? echo number_format($knit_cost_variance,2); ?></td>
                    <td width="100" align="right"><? echo number_format($yarn_dyeing_cost_dzn_vairance ,2); ?></td>
                    <td width="110" align="right" bgcolor="<? //echo $color_fab_dy_v; ?>"><? echo number_format($yarn_dyeing_cost_variance ,2); ?></td>
                    <td width="120" align="right"><? echo number_format($fabric_dyeing_dzn_variance,2); ?></td>
                    <td width="100" align="right" bgcolor="<? //echo $color_fab_v; ?>"><? echo number_format($fabric_dyeing_cost_variance,2); ?></td>
                    <td width="90" align="right"><? echo number_format($heat_setting_cost_variance,2); ?></td>
                    <td width="100" align="right"><? echo number_format($fabric_finish_variance_cost,2); ?> </td>
                    <td width="90" align="right"><? echo number_format($washing_cost_variance,2); ?></td>
                    <td width="90" align="right"><? echo number_format($all_over_variance,2); ?></td>
                    <td width="100" align="right" bgcolor="<? //echo $color_trim_v; ?>"><? echo number_format($trim_cost_variance,2); ?></td>
                    <td width="80" align="right"><? echo number_format($print_varinace,2); ?></td>
                    <td width="85" align="right"><? echo number_format($embrodery_variance,2); ?></td>
                    <td width="80" align="right"><? echo number_format($special_variance,2); ?></td>
                    <td width="80" align="right"><? echo number_format($wash_amount_variance,2); ?></td>
                    <td width="80" align="right"><? echo number_format($other_amount_variance,2); ?></td>
                    <td width="120" align="right" bgcolor="<? //echo $color_com_v; ?>"><? echo number_format($commercial_variance,2); ?></td>
                    <td width="120" align="right"><? echo number_format($foreign_variance,2) ?></td>
                    <td width="120" align="right"><? echo number_format($local_variance,2) ?></td>
                    <td width="100" align="right"><? echo number_format($test_variance,2);?></td>
                    <td width="100" align="right"><? echo number_format($feight_variance,2); ?></td>
                    <td width="120" align="right"><? echo number_format($inspection_variance,2);?></td>
                    <td width="100" align="right"><? echo number_format($certificate_variance,2); ?></td>
                    <td width="100" align="right"><? echo number_format($common_variance,2); ?></td>
                    <td width="100" align="right"><? echo number_format($currier_variance,2);?></td>
                    <td width="120" align="right"><? echo number_format($cm_dzn_variance,2);?></td>
                    <td width="100" align="right" bgcolor="<? //echo $color_cm_v; ?>"><? echo number_format($cm_variance,2);?></td>
                    <td width="100" align="right"><? echo number_format($total_cost_varaince,2); ?></td>
                    <td width="100" align="right" bgcolor="<? //echo $color_pl; ?>"><? echo number_format($tot_profit_variance,2); ?></td>
                    <td width="100" align="right"><p><? echo number_format($tot_profit_percient_varaince,2).'%'; ?></p></td>
                    <td width="100" align="right"><? //echo number_format($p_expected_profit,2); ?></td>
                    <td width="100" align="right"><? //echo number_format($company_asking,2); ?></td>
                    <td align="right"  width="100"><? echo number_format($tot_expected_varaince_data_vairance,2); ?></td>
                    <td align="right"  width="100"><? $total_smv_vari+=$row[csf('set_smv')]-$smv_price;echo number_format($row[csf('set_smv')]-$smv_price,2); ?></td>
                     <td align="right"  width=""><? echo number_format($yarn_cons_variance,2); ?></td>
                </tr>
                <?
                    $percent_avg_rate=$avg_rate_variance/$p_avg_rate*100;
					if(is_infinite($percent_avg_rate) || is_nan($percent_avg_rate)){$percent_avg_rate=0;}
					$percent_yarn_cons=$yarn_cons_variance/$p_yarn_cons*100;
					if(is_infinite($percent_yarn_cons) || is_nan($percent_yarn_cons)){$percent_yarn_cons=0;}
					$percent_yarn_costing=$yarn_variance/$p_yarn_costing*100;
					if(is_infinite($percent_yarn_costing) || is_nan($percent_yarn_costing)){$percent_yarn_costing=0;}
					$percent_yarn_cost_percent=$yarn_cost_variance_percent/$p_yarn_cost_percent*100;
					if(is_infinite($percent_yarn_cost_percent) || is_nan($percent_yarn_cost_percent)){$percent_yarn_cost_percent=0;}
					$percent_fab_purchase=$fab_purchase_variance/$p_fab_purchase*100;
					if(is_infinite($percent_fab_purchase) || is_nan($percent_fab_purchase)){$percent_fab_purchase=0;}
					$percent_knit_cost_dzn=$knit_cost_dzn_variance/$p_knit_cost_dzn*100;
					if(is_infinite($percent_knit_cost_dzn) || is_nan($percent_knit_cost_dzn)){$percent_knit_cost_dzn=0;}
					$percent_knit_cost=$knit_cost_variance/$p_knit_cost*100;
					if(is_infinite($percent_knit_cost) || is_nan($percent_knit_cost)){$percent_knit_cost=0;}
					$percent_yarn_dyeing_cost_dzn=$p_yarn_dyeing_cost_dzn/$yarn_dyeing_cost_dzn*100;
					if(is_infinite($percent_yarn_dyeing_cost_dzn) || is_nan($percent_yarn_dyeing_cost_dzn)){$percent_yarn_dyeing_cost_dzn=0;}
					$percent_yarn_dyeing_cost=$p_yarn_dyeing_cost/$yarn_dyeing_cost*100;
					if(is_infinite($percent_yarn_dyeing_cost) || is_nan($percent_yarn_dyeing_cost)){$percent_yarn_dyeing_cost=0;}
					$percent_fabric_dyeing_dzn=$fabric_dyeing_dzn_variance/$p_fabric_dyeing_cost_dzn*100;
					if(is_infinite($percent_fabric_dyeing_dzn) || is_nan($percent_fabric_dyeing_dzn)){$percent_fabric_dyeing_dzn=0;}
					$percent_fabric_dyeing=$fabric_dyeing_cost_variance/$p_fabric_dyeing_cost*100;
					if(is_infinite($percent_fabric_dyeing) || is_nan($percent_fabric_dyeing)){$percent_fabric_dyeing=0;}
					$percent_heat_setting_cost=$heat_setting_cost_variance/$p_heat_setting_cost*100;
					if(is_infinite($percent_heat_setting_cost) || is_nan($percent_heat_setting_cost)){$percent_heat_setting_cost=0;}
					$percent_fabric_finish=$fabric_finish_variance_cost/$p_fabric_finish*100;
					if(is_infinite($percent_fabric_finish) || is_nan($percent_fabric_finish)){$percent_fabric_finish=0;}
					$percent_wash_cost=$wash_cost_variance/$p_washing_cost*100;
					if(is_infinite($percent_wash_cost) || is_nan($percent_wash_cost)){$percent_wash_cost=0;}
					$percent_all_over=$all_over_variance/$p_all_over_cost*100;
					if(is_infinite($percent_all_over) || is_nan($percent_all_over)){$percent_all_over=0;}
					
                    /*if($fabric_dyeing_cost<=0 && $yarn_dyeing_cost<=0) $color_fab="red"; else $color_fab="";	
                    if($yarn_costing<=0) $color_yarn="red"; else $color_yarn="";	
                    if($kniting_cost<=0) $color_knit="red"; else $color_knit="";	
                    if($fabric_finish<=0) $color_finish="red"; else $color_finish="";	
                    if($commercial_cost<=0) $color_com="red"; else $color_com="";
                    */
                   // $max_profit=$asking_profit_arr[$row[csf('company_name')]]['max_profit'];
                    //echo $max_profit;
                    //$company_asking=$asking_profit_arr[$row[csf('company_name')]]['asking_profit'];
                    /*if($trim_cost_variance<=0) $color_trim="red"; else $color_trim="";	
                    if($cm_cost<=0) $color="red"; else $color="";*/
					
					$percent_trim_cost=$trim_cost_variance/$p_trim_amount*100;
					if(is_infinite($percent_trim_cost) || is_nan($percent_trim_cost)){$percent_trim_cost=0;}
					$percent_print=$print_varinace/$p_print_amount*100;
					if(is_infinite($percent_print) || is_nan($percent_print)){$percent_print=0;}
					$percent_embroidery_amount=$embrodery_variance/$p_embroidery_amount*100;
					if(is_infinite($percent_embroidery_amount) || is_nan($percent_embroidery_amount)){$percent_embroidery_amount=0;}
					$percent_special=$special_variance/$p_special_amount*100;
					if(is_infinite($percent_special) || is_nan($percent_special)){$percent_special=0;}
					$percent_wash_amount=$wash_amount_variance/$p_wash_amount*100;
					if(is_infinite($percent_wash_amount) || is_nan($percent_wash_amount)){$percent_wash_amount=0;}
					$percent_other_amount=$other_amount_variance/$p_other_amount*100;
					if(is_infinite($percent_other_amount) || is_nan($percent_other_amount)){$percent_other_amount=0;}
					$percent_commercial=$commercial_variance/$p_commercial_cost*100;
					if(is_infinite($percent_commercial) || is_nan($percent_commercial)){$percent_commercial=0;}
					$percent_foreign=$foreign_variance/$p_foreign*100;
					if(is_infinite($percent_foreign) || is_nan($percent_foreign)){$percent_foreign=0;}
					$percent_local=$local_variance/$p_local*100;
					if(is_infinite($percent_local) || is_nan($percent_local)){$percent_local=0;}
					$percent_test=$test_variance/$p_test_cost*100;
					if(is_infinite($percent_test) || is_nan($percent_test)){$percent_test=0;}
					$percent_feight=$feight_variance/$p_freight_cost*100;
					if(is_infinite($percent_feight) || is_nan($percent_feight)){$percent_feight=0;}
					$percent_inspection=$inspection_variance/$p_inspection*100;
					if(is_infinite($percent_inspection) || is_nan($percent_inspection)){$percent_inspection=0;}
					$percent_certificate=$certificate_variance/$p_certificate_cost*100;
					if(is_infinite($percent_certificate) || is_nan($percent_certificate)){$percent_certificate=0;}
					$percent_common_oh=$common_variance/$p_common_oh*100;
					if(is_infinite($percent_common_oh) || is_nan($percent_common_oh)){$percent_common_oh=0;}
					$percent_currier=$currier_variance/$p_currier_cost*100;
					if(is_infinite($percent_currier) || is_nan($percent_currier)){$percent_currier=0;}
					$percent_cm_dzn=$cm_dzn_variance/$p_cm_cost_dzn*100;
					if(is_infinite($percent_cm_dzn) || is_nan($percent_cm_dzn)){$percent_cm_dzn=0;}
					$percent_cm=$cm_variance/$p_cm_cost*100;
					if(is_infinite($percent_cm) || is_nan($percent_cm)){$percent_cm=0;}
					$percent_total_cost=$total_cost_varaince/$total_p_cost*100;
					if(is_infinite($percent_total_cost) || is_nan($percent_total_cost)){$percent_total_cost=0;}
					$percent_tot_profit=$tot_profit_variance/$total_p_profit*100;
					if(is_infinite($percent_tot_profit) || is_nan($percent_tot_profit)){$percent_tot_profit=0;}
					
					$percent_tot_profit_percient_varaince=$tot_profit_percient_varaince/$total_p_profit_percentage2*100;
					if(is_infinite($percent_tot_profit_percient_varaince) || is_nan($percent_tot_profit_percient_varaince)){$percent_tot_profit_percient_varaince=0;}
                    $total_cost_vari_percent_amount=$total_p_cost/$total_cost*100;
					if(is_infinite($total_cost_vari_percent_amount) || is_nan($total_cost_vari_percent_amount)){$total_cost_vari_percent_amount=0;}
                    $total_profit_vari_percnt_amount=$total_order_value-$total_cost_vari_percent_amount;
                    $total_profit_percentage2=$total_profit/$total_order_value*100;
					if(is_infinite($total_profit_percentage2) || is_nan($total_profit_percentage2)){$total_profit_percentage2=0;} 
                    if($total_profit_percentage2<=0 ) $color_pl="red";
                    else if($total_profit_percentage2>$max_profit) $color_pl="yellow";	
                    else if($total_profit_percentage2<=$max_profit) $color_pl="green";	
                    else $color_pl="";	
                ?>
                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trvp_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trvp_<? echo $i; ?>">
                    <td width="100">Variance %</td>
                    <td width="100" align="right"><? echo number_format($percent_avg_rate,2).'%'; ?></td>
                    <td width="80" align="right" bgcolor="<? //echo $color_yarn; ?>"><? echo number_format($percent_yarn_costing,2); ?></td>
                    <td width="80" align="right"><? echo number_format($percent_yarn_cost_percent,2).'%'; ?></td>
                    <td width="100" align="right"><? echo number_format($percent_fab_purchase,2).'%'; ?></td>
                    <td width="80" align="right"><? echo number_format($percent_knit_cost_dzn,2).'%'; ?></td>
                    <td width="80" align="right" bgcolor="<? //echo $color_knit; ?>"><? echo number_format($percent_knit_cost,2).'%'; ?></td>
                    <td width="100" align="right"><? echo number_format($percent_yarn_dyeing_cost_dzn,2).'%'; ?></td>
                    <td width="110" align="right"><? echo number_format($percent_yarn_dyeing_cost,2).'%'; ?></td>
                    <td width="120" align="right"><? echo number_format($percent_fabric_dyeing_dzn,2).'%'; ?></td>
                    <td width="100" align="right" bgcolor="<? //echo $color_fab; ?>"><? echo number_format($percent_fabric_dyeing,2).'%'; ?></td>
                    <td width="90" align="right"><? echo number_format($percent_heat_setting_cost,2).'%'; ?></td>
                    <td width="100" align="right"><? echo number_format($percent_fabric_finish,2).'%'; ?> </td>
                    <td width="90" align="right"><? echo number_format($percent_wash_cost,2).'%'; ?></td>
                    <td width="90" align="right"><? echo number_format($percent_all_over,2).'%'; ?></td>
                    <td width="100" align="right"><? echo number_format($percent_trim_cost,2).'%'; ?></td>
                    <td width="80" align="right"><? echo number_format($percent_print,2).'%'; ?></td>
                    <td width="85" align="right"><? echo number_format($percent_embroidery_amount,2).'%'; ?></td>
                    <td width="80" align="right"><? echo number_format($percent_special,2).'%'; ?></td>
                    <td width="80" align="right"><? echo number_format($percent_wash_amount,2).'%'; ?></td>
                    <td width="80" align="right"><? echo number_format($percent_other_amount,2).'%'; ?></td>
                    <td width="120" align="right"><? echo number_format($percent_commercial,2).'%'; ?></td>
                    <td width="120" align="right"><? echo number_format($percent_foreign,2).'%'; ?></td>
                    <td width="120" align="right"><? echo number_format($percent_local,2).'%'; ?></td>
                    <td width="100" align="right"><? echo number_format($percent_test,2).'%';?></td>
                    <td width="100" align="right"><? echo number_format($percent_feight,2).'%'; ?></td>
                    <td width="120" align="right"><? echo number_format($percent_inspection,2).'%';?></td>
                    <td width="100" align="right"><? echo number_format($percent_certificate,2).'%'; ?></td>
                    <td width="100" align="right"><? echo number_format($percent_common_oh,2).'%'; ?></td>
                    <td width="100" align="right"><? echo number_format($percent_currier,2).'%';?></td>
                    <td width="120" align="right"><? echo number_format($percent_cm_dzn,2).'%';?></td>
                    <td width="100" align="right"><? echo number_format($percent_cm,2).'%';?></td>
                    <td width="100" align="right"><? echo number_format($percent_total_cost,2).'%'; ?></td>
                    
                    <td width="100" align="right"><? echo number_format($percent_tot_profit,2).'%'; ?></td>
                    <td width="100" align="right"><? echo number_format($percent_tot_profit_percient_varaince,2).'%'; ?></td>
                    <td width="100" align="right"><? //echo number_format($p_expected_profit,2); ?></td>
                    <td width="100" align="right"><? //echo number_format($company_asking,2); ?></td>
                    <td align="right" width="100"><? $expect_variance=$total_profit-$expected_profit; echo '-'; ?></td>
                     <td align="right" width="100"><? 
					 	$smv_price=number_format($smv_price,'.','',4);
					  echo (($row[csf('set_smv')]-$smv_price)/$smv_price*100); ?></td>
                     <td align="right"  width=""><? echo number_format($percent_yarn_cons,2); ?></td>
                </tr>
				<?
                $total_order_amount+=$order_value;
                $total_plancut_amount+=$plancut_value;
				
				//price_total
				$p_total_order_qty+=$order_qty_pcs;
				$p_total_order_value+=$order_value;
				$p_total_yarn_costing+=$p_yarn_costing;
				$p_total_yarn_cons+=$p_yarn_cons;
				$p_total_fab_purchase+=$p_fab_purchase;
				$p_total_knit_cost+=$p_knit_cost;
				$p_total_yarn_dyeing_cost+=$p_yarn_dyeing_cost;
				$p_total_fabric_dyeing_cost+=$p_fabric_dyeing_cost;
				$p_total_heat_setting_cost+=$p_heat_setting_cost;
				$p_total_fabric_finish+=$p_fabric_finish;
				$p_total_washing_cost+=$p_washing_cost;
				$p_total_all_over_cost+=$p_all_over_cost;
				$p_total_trim_amount+=$p_trim_amount;
				$p_total_print_amount+=$p_print_amount;
				$p_total_embroidery_amount+=$p_embroidery_amount;
				$p_total_special_amount+=$p_special_amount;
				$p_total_wash_amount+=$p_wash_amount;
				$p_total_other_amount+=$p_other_amount;
				$p_total_commercial_cost+=$p_commercial_cost;
				$p_total_foreign+=$p_foreign;
				$p_total_local+=$p_local;
				$p_total_test_cost+=$p_test_cost;
				$p_total_freight_cost+=$p_freight_cost;
				$p_total_wash_amt+=$p_wash_amt;
				$p_total_inspection+=$p_inspection;
				$p_total_certificate_cost+=$p_certificate_cost;
				$p_total_common_oh+=$p_common_oh;
				$p_total_currier_cost+=$p_currier_cost;
				$p_total_cm_cost+=$p_cm_cost;
				$p_total_p_cost+=$total_p_cost;
				$p_total_p_profit+=$total_p_profit;
				$p_total_profit_percentage2+=$total_p_profit_percentage2;
				$p_total_expected_profit+=$p_expected_profit;
				$p_total_expect_variance+=$tot_expect_variance;
				//pre_cost
				
				//pre_cost
				$total_order_qty+=$order_qty_pcs;
				$total_order_value+=$order_value;
				$total_yarn_costing+=$yarn_costing;
				$total_yarn_cons+=$yarn_cons;
				$total_fab_purchase+=$fab_purchase;
				$total_knit_cost+=$knit_cost;
				$total_yarn_dyeing_cost+=$yarn_dyeing_cost;
				$total_fabric_dyeing_cost+=$fabric_dyeing_cost;
				$total_heat_setting_cost+=$heat_setting_cost;
				$total_fabric_finish+=$fabric_finish;
				$total_washing_cost+=$washing_cost;
				$total_all_over_cost+=$all_over_cost;
				$total_trim_amount+=$trim_amount;
				$total_print_amount+=$print_amount;
				$total_embroidery_amount+=$embroidery_amount;
				$total_special_amount+=$special_amount;
				$total_wash_amt+=$wash_amount;
				$total_other_amount+=$other_amount;
				$total_commercial_cost+=$commercial_cost;
				$total_foreign+=$foreign;
				$total_local+=$local;
				$total_test_cost+=$test_cost;
				$total_freight_cost+=$freight_cost;
				$total_wash_amount+=$wash_amount;
				$total_inspection+=$inspection;
				$total_certificate_cost+=$certificate_cost;
				$total_common_oh+=$common_oh;
				$total_currier_cost+=$currier_cost;
				$total_cm_cost+=$cm_cost;
				$total_pre_cost+=$total_cost;
				$total_pre_profit+=$total_profit;
				$total_pre_profit_percentage2+=$total_profit_percentage2;
				$total_pre_expected_profit+=$expected_profit;
				$total_pre_expect_variance+=$expect_variance;
				//variance_total
				$v_total_order_qty+=$order_qty_pcs;
				$v_total_order_value+=$order_value;
				$v_total_yarn_costing+=$yarn_variance;
				$v_total_yarn_cons+=$yarn_cons_variance;
				$v_total_fab_purchase+=$fab_purchase_variance;
				$v_total_knit_cost+=$knit_cost_variance;
				$v_total_yarn_dyeing_cost+=$yarn_dyeing_cost_variance;
				$v_total_fabric_dyeing_cost+=$fabric_dyeing_cost_variance;
				$v_total_heat_setting_cost+=$heat_setting_cost_variance;
				$v_total_fabric_finish+=$fabric_finish_variance_cost;
				$v_total_washing_cost+=$washing_cost_variance;
				$v_total_all_over_cost+=$all_over_variance;
				$v_total_trim_amount+=$trim_cost_variance;
				$v_total_print_amount+=$print_varinace;
				$v_total_embroidery_amount+=$embrodery_variance;
				$v_total_special_amount+=$special_variance;
				$v_total_wash_amount+=$wash_amount_variance;
				$v_total_other_amount+=$other_amount_variance;
				$v_total_commercial_cost+=$commercial_variance;
				$v_total_foreign+=$foreign_variance;
				$v_total_local+=$local_variance;
				$v_total_test_cost+=$test_variance;
				$v_total_freight_cost+=$feight_variance;
				$v_total_wash_amount+=$p_wash_amount;
				$v_total_inspection+=$inspection_variance;
				$v_total_certificate_cost+=$certificate_variance;
				$v_total_common_oh+=$common_variance;
				$v_total_currier_cost+=$currier_variance;
				$v_total_cm_cost+=$cm_variance;
				$v_total_p_cost+=$total_cost_varaince;
				$v_total_p_profit+=$tot_profit_variance;
				$v_total_profit_percentage2+=$tot_profit_percient_varaince;
				$v_total_expected_profit+=$tot_expected_profit;
				$v_total_expect_variance+=$tot_expected_varaince_data_vairance;
				//pre_cost
				$i++;
			}
			$p_yarn_cost_percentage=$p_total_yarn_costing/$p_total_order_value*100;
			if(is_infinite($p_yarn_cost_percentage) || is_nan($p_yarn_cost_percentage)){$p_yarn_cost_percentage=0;}
			?>
            </table>
            </div>
            <table class="tbl_bottom" width="5310" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
                <tr>
                    <td width="40"></td>
                    <td width="70"></td>
                    <td width="70"></td>
                    <td width="100"></td>
                     <td width="100"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="100"></td>
                    <td width="110"></td>
                    <td width="110"></td>
                    <td width="110"></td>
                    <td width="70">Price Quote</td>
                    <td width="90" align="right" id="total_order_qnty"><? echo number_format($p_total_order_qty,2); ?></td>
                    <td width="90"></td>
                    <td width="100" align="right" id="p_total_order_value"><? echo number_format($p_total_order_value,2); ?></td>
                    <td width="100"></td>
                    <td width="100"></td>
                    <td width="80" align="right" id="p_total_yarn_costing"><? echo number_format($p_total_yarn_costing,2); ?></td>
                    <td width="80" align="right" ><? //echo number_format($p_yarn_cost_percentage,2); ?></td>
                    <td width="100" align="right" id="p_total_fab_purchase"><? echo number_format($p_total_fab_purchase,2); ?></td>
                    <td width="80"></td>
                    <td width="80" align="right" id="p_total_knit_cost"><? echo number_format($p_total_knit_cost,2); ?></td>
                    <td width="100"></td>
                    <td width="110" align="right" id="p_total_yarn_dyeing_cost"><? echo number_format($p_total_yarn_dyeing_cost,2); ?></td>
                    <td width="120"></td>
                    <td width="100" align="right" id="p_fabric_dyeing_td"><? echo number_format($p_total_fabric_dyeing_cost,2); ?></td>
                    <td width="90" align="right" id="p_heat_setting_td"><? echo number_format($p_total_heat_setting_cost,2); ?></td>		
                    <td width="100" align="right" id="p_fabric_finishing_td"><? echo number_format($p_total_fabric_finish,2); ?></td>
                    <td width="90" align="right" id="p_washing_td"><? echo number_format($p_total_washing_cost,2); ?></td>
                    <td width="90" align="right" id="p_aop_td"><? echo number_format($p_total_all_over_cost,2); ?></td>
                    <td width="100" align="right" id="p_trim_amount_td"><? echo number_format($p_total_trim_amount,2); ?></td>
                    <td width="80" align="right" id="p_print_amount_td"><? echo number_format($p_total_print_amount,2); ?></td>
                    <td width="85" align="right" id="p_embroidery_amount_td"><? echo number_format($p_total_embroidery_amount,2); ?></td>
                    <td width="80" align="right" id="p_special_amount_td"><? echo number_format($p_total_special_amount,2); ?></td>
                    <td width="80" align="right" id="p_wash_amt_td"><? echo number_format($p_total_wash_amt,2); ?></td>
                    <td width="80" align="right" id="p_other_amount_td"><? echo number_format($p_total_other_amount,2); ?></td>
                    <td width="120" align="right" id="p_commercial_cost_td"><? echo number_format($p_total_commercial_cost,2); ?></td>
                    <td width="120" align="right" id="p_foreign_td"><? echo number_format($p_total_foreign,2); ?></td>
                    <td width="120" align="right" id="p_local_td"><? echo number_format($p_total_local,2); ?></td>
                    <td width="100" align="right" id="p_test_cost_td"><? echo number_format($p_total_test_cost,2); ?></td>
                    <td width="100" align="right" id="p_freight_cost_td"><? echo number_format($p_total_freight_cost,2); ?></td>
                    <td width="120" align="right" id="p_inspection_cost_td"><? echo number_format($p_total_inspection,2); ?></td>
                    <td width="100" align="right" id="p_certificate_cost_td"><? echo number_format($p_total_certificate_cost,2); ?></td>
                    <td width="100" align="right" id="p_operating_exp_td"><? echo number_format($p_total_common_oh,2); ?></td>
                    <td width="100" align="right" id="p_currier_cost_td"><? echo number_format($p_total_currier_cost,2); ?></td>
                    <td width="120"></td>
                    <td width="100" align="right" id="p_cm_cost_td"><? echo number_format($p_total_cm_cost,2); ?></td>
                    <td width="100" align="right" id="p_tot_cost_td"><? echo number_format($p_total_p_cost,2); ?></td>
                    <td width="100" align="right" id="p_profitt_td"><? echo number_format($p_total_p_profit,2);?></td>
                    <td width="100" align="right"><? echo number_format($p_total_profit_percentage2,2);?></td>
                    <td width="100" align="right" id="p_expProfitt_td"><? echo number_format($p_total_expected_profit,2);?></td>
                    <td width="100" align="right"><? //echo number_format($p_total_expected_profit,2);?></td>
                    <td align="right" width="100" id="p_expect_variance_td"><? echo number_format($p_total_expect_variance,2);?></td>
                    <td align="right" width="100" id="p_expect_variance_td2"><? echo number_format($total_smv_price,2);?></td>
                     <td align="right" width="" id=""><? echo number_format($p_total_yarn_cons,2);?></td>
                </tr>
                <tr>
                    <td width="40"></td>
                    <td width="70"></td>
                    <td width="70"></td>
                    <td width="100"></td>
                    <td width="100"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="100"></td>
                    <td width="110"></td>
                    <td width="110"></td>
                    <td width="110"></td>
                    <td width="70">Pre Cost</td>
                    <td width="90" align="right" id="total_order_qnty"><? echo number_format($total_order_qty,2); ?></td>
                    <td width="90"></td>
                    <td width="100" align="right" id="total_order_amount2"><? echo number_format($total_order_value,2); ?></td>
                    <td width="100"></td>
                    <td width="100"></td>
                    <td width="80" align="right" id="total_yarn_costing"><? echo number_format($total_yarn_costing,2); ?></td>
                    <td width="80" align="right" ><? //echo number_format($total_yarn_cost_percentage,2); ?></td>
                    <td width="100" align="right" id="total_fab_purchase"><? echo number_format($total_fab_purchase,2); ?></td>
                    <td width="80"></td>
                    <td width="80" align="right" id="total_knit_cost"><? echo number_format($total_knit_cost,2); ?></td>
                    <td width="100"></td>
                    <td width="110" align="right" id="total_yarn_dyeing_cost"><? echo number_format($total_yarn_dyeing_cost,2); ?></td>
                    <td width="120"><? ?></td>
                    <td width="100" align="right" id="fabric_dyeing_td"><? echo number_format($total_fabric_dyeing_cost,2); ?></td>
                    <td width="90" align="right" id="heat_setting_td"><? echo number_format($total_heat_setting_cost,2); ?></td>		
                    <td width="100" align="right" id="fabric_finishing_td"><? echo number_format($total_fabric_finish,2); ?></td>
                    <td width="90" align="right" id="washing_td"><? echo number_format($total_washing_cost,2); ?></td>
                    <td width="90" align="right" id="aop_td"><? echo number_format($total_all_over_cost,2); ?></td>
                    <td width="100" align="right" id="trim_amount_td"><? echo number_format($total_trim_amount,2); ?></td>
                    <td width="80" align="right" id="print_amount_td"><? echo number_format($total_print_amount,2); ?></td>
                    <td width="85" align="right" id="embroidery_amount_td"><? echo number_format($total_embroidery_amount,2); ?></td>
                    <td width="80" align="right" id="special_amount_td"> <? echo number_format($total_special_amount,2); ?></td>
                    <td width="80" align="right" id="wash_amt_td"> <? echo number_format($total_wash_amt,2); ?></td>
                    <td width="80" align="right" id="other_amount_td"><? echo number_format($total_other_amount,2); ?></td>
                    <td width="120" align="right" id="commercial_cost_td"><? echo number_format($total_commercial_cost,2); ?></td>
                    <td width="120" align="right" id="foreign_td"><? echo number_format($total_foreign,2); ?></td>
                    <td width="120" align="right" id="local_td"><? echo number_format($total_local,2); ?></td>
                    <td width="100" align="right" id="test_cost_td"><? echo number_format($total_test_cost,2); ?></td>
                    <td width="100" align="right" id="freight_cost_td"><? echo number_format($total_freight_cost,2); ?></td>
                    <td width="120" align="right" id="inspection_cost_td"><? echo number_format($total_inspection,2); ?></td>
                    <td width="100" align="right" id="certificate_cost_td"><? echo number_format($total_certificate_cost,2); ?></td>
                    <td width="100" align="right" id="operating_exp_td"><? echo number_format($total_common_oh,2); ?></td>
                    <td width="100" align="right" id="currier_cost_td"><? echo number_format($total_currier_cost,2); ?></td>
                    <td width="120"></td>
                    <td width="100" align="right" id="cm_cost_td"><? echo number_format($total_cm_cost,2); ?></td>
                    <td width="100" align="right" id="tot_cost_td"><? echo number_format($total_pre_cost,2); ?></td>
                    <td width="100" align="right" id="profitt_td"><? echo number_format($total_pre_profit,2);?></td>
                    <td width="100" align="right"><?  echo number_format($total_pre_profit_percentage2,2);?></td>
                    <td width="100" align="right" id="expProfitt_td"><? echo number_format($total_pre_expected_profit,2);?></td>
                    <td width="100" align="right"><? //echo number_format($total_pre_expected_profit,2);?></td>
                    <td align="right" width="100"  id="expect_variance_td"><? echo number_format($total_pre_expect_variance,2);?></td>
                    <td align="right" width="100"  id="expect_variance_td2"><? echo number_format($total_smv_pre,2);?></td>
                     <td align="right" width=""  id=""><? echo number_format($total_yarn_cons,2);?></td>
                </tr>
                <tr>
                    <td width="40"></td>
                    <td width="70"></td>
                    <td width="70"></td>
                    <td width="100"></td>
                    <td width="100"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="100"></td>
                    <td width="110"></td>
                    <td width="110"></td>
                    <td width="110"></td>
                    <td width="70">Variance</td>
                    <td width="90" align="right"><? //echo number_format($v_total_order_qty,2); ?></td>
                    <td width="90"></td>
                    <td width="100" align="right"><? //echo number_format($v_total_order_value,2); ?></td>
                    <td width="100"></td>
                    <td width="100"></td>
                    <td width="80" align="right" id="v_total_yarn_costing"><? echo number_format($v_total_yarn_costing,2); ?></td>
                    <td width="80" align="right"><? //echo number_format($v_total_yarn_cost_percentage,2); ?></td>
                    <td width="100" align="right" id="v_total_fab_purchase"><? echo number_format($v_total_fab_purchase,2); ?></td>
                    <td width="80"></td>
                    <td width="80" align="right" id="v_total_knit_cost"><? echo number_format($v_total_knit_cost,2); ?></td>
                    <td width="100"></td>
                    <td width="110" align="right" id="v_total_yarn_dyeing_cost"><? echo number_format($v_total_yarn_dyeing_cost,2); ?></td>
                    <td width="120"><? ?></td>
                    <td width="100" align="right" id="v_fabric_dyeing_td"><? echo number_format($v_total_fabric_dyeing_cost,2); ?></td>
                    <td width="90" align="right" id="v_heat_setting_td"><? echo number_format($v_total_heat_setting_cost,2); ?></td>		
                    <td width="100" align="right" id="v_fabric_finishing_td"><? echo number_format($v_total_fabric_finish,2); ?></td>
                    <td width="90" align="right" id="v_washing_td"><? echo number_format($v_total_washing_cost,2); ?></td>
                    <td width="90" align="right" id="v_aop_td"><? echo number_format($v_total_all_over_cost,2); ?></td>
                    <td width="100" align="right" id="v_trim_amount_td"><? echo number_format($v_total_trim_amount,2); ?></td>
                    <td width="80" align="right" id="v_print_amount_td"><? echo number_format($v_total_print_amount,2); ?></td>
                    <td width="85" align="right" id="v_embroidery_amount_td"><? echo number_format($v_total_embroidery_amount,2); ?></td>
                    <td width="80" align="right" id="v_special_amount_td"><? echo number_format($v_total_special_amount,2); ?></td>
                    <td width="80" align="right" id="v_wash_amt_td"><? echo number_format($v_total_wash_amount,2); ?></td>
                    <td width="80" align="right" id="v_other_amount_td"><? echo number_format($v_total_other_amount,2); ?></td>
                    <td width="120" align="right" id="v_commercial_cost_td"><? echo number_format($v_total_commercial_cost,2); ?></td>
                    <td width="120" align="right" id="v_foreign_td"><? echo number_format($v_total_foreign,2); ?></td>
                    <td width="120" align="right" id="v_local_td"><? echo number_format($v_total_local,2); ?></td>
                    <td width="100" align="right" id="v_test_cost_td"><? echo number_format($v_total_test_cost,2); ?></td>
                    <td width="100" align="right" id="v_freight_cost_td"><? echo number_format($v_total_freight_cost,2); ?></td>
                    <td width="120" align="right" id="v_inspection_cost_td"><? echo number_format($v_total_inspection,2); ?></td>
                    <td width="100" align="right" id="v_certificate_cost_td"><? echo number_format($v_total_certificate_cost,2); ?></td>
                    <td width="100" align="right" id="v_operating_exp_td"><? echo number_format($v_total_common_oh,2); ?></td>
                    <td width="100" align="right" id="v_currier_cost_td"><? echo number_format($v_total_currier_cost,2); ?></td>
                    <td width="120"></td>
                    <td width="100" align="right" id="v_cm_cost_td"><? echo number_format($v_total_cm_cost,2); ?></td>
                    <td width="100" align="right" id="v_tot_cost_td"><? echo number_format($v_total_p_cost,2); ?></td>
                    <td width="100" align="right" id="v_profitt_td"><? echo number_format($v_total_p_profit,2);?></td>
                    <td width="100" align="right"><?  //echo number_format($v_total_pre_profit_percentage2,2);?></td>
                    <td width="100" align="right" id="v_expProfitt_td"><? //echo number_format($v_total_pre_expected_profit,2);?></td>
                    <td width="100" align="right"><? //echo number_format($v_total_pre_expected_profit,2);?></td>
                    <td align="right" width="100"  id="v_expect_variance_td"><? echo number_format($v_total_expect_variance,2);?></td>
                    <td width="100"  align="right"><? echo number_format($total_smv_vari,2);?></td>
                     <td width=""  align="right"><? echo number_format($v_total_yarn_cons,2);?></td>
                </tr>
            </table>
            </fieldset>
            </div>
           <?
		}
	}
	else if($report_type==9) //Pre cost Vs Price Quotation
	{
		if($template==1)
		{
			ob_start();
			$style1="#E9F3FF"; 
			$style="#FFFFFF";
			 	
			$cbo_ready_to=str_replace("'","",$cbo_ready_to);
			if($cbo_ready_to==2 || $cbo_ready_to=='' || $cbo_ready_to==0) $cbo_ready_to_cond="and c.ready_to_approved in(2,0)";
			else if($cbo_ready_to==1) $cbo_ready_to_cond="and c.ready_to_approved in(1)";
			else $cbo_ready_to_cond="";

			//if($cbo_ready_to==0 || $cbo_ready_to=='') $cbo_ready_to_cond="";else $cbo_ready_to_cond="and c.ready_to_approved in($cbo_ready_to)";
			$fab_precost_arr=array(); $p_fab_precost_arr=array(); $commission_array=array(); $price_commission_array=array(); $knit_arr=array(); $pq_knit_arr=array(); $fabriccostArray=array(); $fab_emb=array(); $price_fab_emb=array(); $fabric_data_Array=array(); $price_fabric_data_Array=array();$price_costing_perArray=array(); $asking_profit_arr=array(); $yarncostArray=array(); $yarn_desc_array=array(); $costing_date_arr=array();
			
			/*$yarncostDataArray=sql_select("select job_no, count_id, type_id, sum(cons_qnty) as cons_qnty, sum(amount) as amount from wo_pre_cost_fab_yarn_cost_dtls where status_active=1 and is_deleted=0 group by job_no, count_id, type_id");
			foreach($yarncostDataArray as $yarnRow)
			{
				$yarncostArray[$yarnRow[csf('job_no')]].=$yarnRow[csf('count_id')]."**".$yarnRow[csf('type_id')]."**".$yarnRow[csf('cons_qnty')]."**".$yarnRow[csf('amount')].",";
			}*/
			
			$asking_profit=sql_select("select id, company_id, applying_period_date, applying_period_to_date, asking_profit, max_profit from lib_standard_cm_entry where status_active=1 and is_deleted=0 and company_id=$company_name");//$date_max_profit and company_id=3 and id=16
			//echo "select id, company_id, applying_period_date, applying_period_to_date, asking_profit, max_profit from lib_standard_cm_entry where status_active=1 and is_deleted=0 and company_id=$company_name";
			foreach($asking_profit as $ask_row )
			{
				$applying_period_date=change_date_format($ask_row[csf('applying_period_date')],'','',1);
				$applying_period_to_date=change_date_format($ask_row[csf('applying_period_to_date')],'','',1);
				$diff=datediff('d',$applying_period_date,$applying_period_to_date);
				for($j=0;$j<$diff;$j++)
				{
					$date_all=add_date(str_replace("'","",$applying_period_date),$j);
				    $newdate =change_date_format($date_all,'','',1);
					$asking_profit_arr[$newdate]['asking_profit']=$ask_row[csf('asking_profit')];
					$asking_profit_arr[$newDate]['max_profit']=$ask_row[csf('max_profit')];
				}
			}
			$costing_date_sql=sql_select("select job_no, costing_date,ready_to_approved from wo_pre_cost_mst where status_active=1 and is_deleted=0 ");
			foreach($costing_date_sql as $row )
			{
				$cost_date=change_date_format($row[csf('costing_date')],'','',1);
				$costing_date_arr[$row[csf('job_no')]]['ask']=$asking_profit_arr[$cost_date]['asking_profit'];
				$costing_date_arr[$row[csf('job_no')]]['max']=$asking_profit_arr[$cost_date]['max_profit'];
				$job_approve_arr[$row[csf('job_no')]]['ready_to_approved']=$row[csf('ready_to_approved')];
			}

			$pri_fab_arr=sql_select("select a.quotation_id,b.fabric_source, a.rate, (a.requirment) as requirment, (a.pcs) as pcs from wo_pri_quo_fab_co_avg_con_dtls a,wo_pri_quo_fabric_cost_dtls  b where a.wo_pri_quo_fab_co_dtls_id=b.id and a.quotation_id=b.quotation_id  and b.status_active=1 and b.is_deleted=0 and b.fabric_source=2");
			foreach($pri_fab_arr as $p_row_pre)
			{
				$p_fab_precost_arr[$p_row_pre[csf('quotation_id')]].=$p_row_pre[csf('requirment')]."**".$p_row_pre[csf('pcs')]."**".$p_row_pre[csf('rate')]."**".$p_row_pre[csf('fabric_source')].",";	
			}
			$fabricDataArray=sql_select("select a.job_no, a.fab_nature_id, a.color_type_id, a.fabric_source, a.rate, b.yarn_cons_qnty, b.yarn_amount from wo_pre_cost_fabric_cost_dtls a, wo_pre_cost_sum_dtls b where a.job_no=b.job_no and a.fabric_source!=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			foreach($fabricDataArray as $fabricRow)
			{
				$fabric_data_Array[$fabricRow[csf('job_no')]].=$fabricRow[csf('fab_nature_id')]."**".$fabricRow[csf('fabric_source')]."**".$fabricRow[csf('rate')]."**".$fabricRow[csf('yarn_cons_qnty')]."**".$fabricRow[csf('yarn_amount')]."**".$fabricRow[csf('color_type_id')].",";
			} //Pre cost end
			unset($fabricDataArray);
			//var_dump($fabric_data_Array);
			$price_costDataArray=sql_select("select  id,costing_per,sew_smv  from wo_price_quotation where status_active=1 and is_deleted=0  ");
			foreach($price_costDataArray as $pri_fabRow)
			{
				$price_costing_perArray[$pri_fabRow[csf('id')]]['costing_per']=$pri_fabRow[csf('costing_per')];
				$price_costing_perArray[$pri_fabRow[csf('id')]]['smv']=$pri_fabRow[csf('sew_smv')];
			}
			//var_dump($price_costing_perArray);
			$sql_yarn_price=sql_select("select  rate, amount,cons_qnty,quotation_id from wo_pri_quo_fab_yarn_cost_dtls a where status_active=1");
			foreach($sql_yarn_price as $fabricRow)
			{
				$price_yarn_data_Array[$fabricRow[csf('quotation_id')]]['amount']+=$fabricRow[csf('amount')];
				$price_yarn_data_Array[$fabricRow[csf('quotation_id')]]['cons_qnty']+=$fabricRow[csf('cons_qnty')];
			}
			
			$price_fabricDataArray=sql_select("select a.quotation_id, a.fab_nature_id, a.color_type_id, a.fabric_source, a.rate, b.yarn_cons_qnty, b.yarn_amount from wo_pri_quo_fabric_cost_dtls a, wo_pri_quo_sum_dtls b where a.quotation_id=b.quotation_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			foreach($price_fabricDataArray as $price_fabricRow)
			{
				$yarn_amount=$price_yarn_data_Array[$price_fabricRow[csf('quotation_id')]]['amount'];
				$yarn_cons_qnty=$price_yarn_data_Array[$price_fabricRow[csf('quotation_id')]]['cons_qnty'];
				
				$price_fabric_data_Array[$price_fabricRow[csf('quotation_id')]].=$price_fabricRow[csf('fab_nature_id')]."**".$price_fabricRow[csf('fabric_source')]."**".$price_fabricRow[csf('rate')]."**".$yarn_cons_qnty."**".$yarn_amount."**".$price_fabricRow[csf('color_type_id')].",";
			} 
			$price_data_array_emb=("select  quotation_id,
			sum(CASE WHEN emb_name=1 THEN amount END) AS print_amount,
			sum(CASE WHEN emb_name=2 THEN amount END) AS embroidery_amount,
			sum(CASE WHEN emb_name=3 THEN amount END) AS wash_amount,
			sum(CASE WHEN emb_name=4 THEN amount END) AS special_amount,
			sum(CASE WHEN emb_name=5 THEN amount END) AS other_amount
			from  wo_pri_quo_embe_cost_dtls where  status_active=1 and is_deleted=0 group by quotation_id");
			$sql_embl_array=sql_select($price_data_array_emb);
			foreach($sql_embl_array as $p_row_emb)
			{
				$price_fab_emb[$p_row_emb[csf('quotation_id')]]['print']=$p_row_emb[csf('print_amount')];
				$price_fab_emb[$p_row_emb[csf('quotation_id')]]['embroidery']=$p_row_emb[csf('embroidery_amount')];
				$price_fab_emb[$p_row_emb[csf('quotation_id')]]['special']=$p_row_emb[csf('special_amount')];
				$price_fab_emb[$p_row_emb[csf('quotation_id')]]['other']=$p_row_emb[csf('other_amount')];
				$price_fab_emb[$p_row_emb[csf('quotation_id')]]['wash']=$p_row_emb[csf('wash_amount')];
			} 
			//var_dump($price_fab_emb);
			
			$fabriccostDataArray=sql_select("select job_no, costing_per_id, trims_cost, embel_cost, cm_cost, commission, common_oh, lab_test, inspection, freight, comm_cost, certificate_pre_cost, currier_pre_cost from wo_pre_cost_dtls where status_active=1 and is_deleted=0");
			foreach($fabriccostDataArray as $fabRow)
			{
				$fabriccostArray[$fabRow[csf('job_no')]]['costing_per_id']=$fabRow[csf('costing_per_id')];
				$fabriccostArray[$fabRow[csf('job_no')]]['trims_cost']=$fabRow[csf('trims_cost')];
				$fabriccostArray[$fabRow[csf('job_no')]]['embel_cost']=$fabRow[csf('embel_cost')];
				$fabriccostArray[$fabRow[csf('job_no')]]['cm_cost']=$fabRow[csf('cm_cost')];
				$fabriccostArray[$fabRow[csf('job_no')]]['commission']=$fabRow[csf('commission')];
				$fabriccostArray[$fabRow[csf('job_no')]]['common_oh']=$fabRow[csf('common_oh')];
				$fabriccostArray[$fabRow[csf('job_no')]]['lab_test']=$fabRow[csf('lab_test')];
				$fabriccostArray[$fabRow[csf('job_no')]]['inspection']=$fabRow[csf('inspection')];
				$fabriccostArray[$fabRow[csf('job_no')]]['freight']=$fabRow[csf('freight')];
				$fabriccostArray[$fabRow[csf('job_no')]]['comm_cost']=$fabRow[csf('comm_cost')];
				$fabriccostArray[$fabRow[csf('job_no')]]['certificate_pre_cost']=$fabRow[csf('certificate_pre_cost')];
				$fabriccostArray[$fabRow[csf('job_no')]]['currier_pre_cost']=$fabRow[csf('currier_pre_cost')];
				$fabriccostArray[$fabRow[csf('job_no')]]['c_cost']=$fabRow[csf('cm_cost')];
			} //pre cost trim emb, commission and others end
			//var_dump($fabriccostArray);
			$price_fabriccostArray=array();
			$p_fabriccostDataArray=sql_select("select quotation_id, costing_per_id, trims_cost, embel_cost, cm_cost, commission, common_oh, lab_test, inspection, freight, comm_cost, certificate_pre_cost, currier_pre_cost from wo_price_quotation_costing_mst where status_active=1 and is_deleted=0");
			foreach($p_fabriccostDataArray as $p_fabRow)
			{
				$price_fabriccostArray[$p_fabRow[csf('quotation_id')]]['costing_per_id']=$p_fabRow[csf('costing_per_id')];
				$price_fabriccostArray[$p_fabRow[csf('quotation_id')]]['trims_cost']=$p_fabRow[csf('trims_cost')];
				$price_fabriccostArray[$p_fabRow[csf('quotation_id')]]['embel_cost']=$p_fabRow[csf('embel_cost')];
				$price_fabriccostArray[$p_fabRow[csf('quotation_id')]]['cm_cost']=$p_fabRow[csf('cm_cost')];
				$price_fabriccostArray[$p_fabRow[csf('quotation_id')]]['commission']=$p_fabRow[csf('commission')];
				$price_fabriccostArray[$p_fabRow[csf('quotation_id')]]['common_oh']=$p_fabRow[csf('common_oh')];
				$price_fabriccostArray[$p_fabRow[csf('quotation_id')]]['lab_test']=$p_fabRow[csf('lab_test')];
				$price_fabriccostArray[$p_fabRow[csf('quotation_id')]]['inspection']=$p_fabRow[csf('inspection')];
				$price_fabriccostArray[$p_fabRow[csf('quotation_id')]]['freight']=$p_fabRow[csf('freight')];
				$price_fabriccostArray[$p_fabRow[csf('quotation_id')]]['comm_cost']=$p_fabRow[csf('comm_cost')];
				$price_fabriccostArray[$p_fabRow[csf('quotation_id')]]['certificate_pre_cost']=$p_fabRow[csf('certificate_pre_cost')];
				$price_fabriccostArray[$p_fabRow[csf('quotation_id')]]['currier_pre_cost']=$p_fabRow[csf('currier_pre_cost')];
				$price_fabriccostArray[$p_fabRow[csf('quotation_id')]]['c_cost']=$p_fabRow[csf('cm_cost')];
			}
			
			$pq_knit_data=sql_select("select quotation_id,
			sum(CASE WHEN cons_type=1 THEN amount END) AS knit_charge,
			sum(CASE WHEN cons_type=2 THEN amount END) AS weaving_charge,
			sum(CASE WHEN cons_type=3 THEN amount END) AS knit_charge_collar_cuff,
			sum(CASE WHEN cons_type=4 THEN amount END) AS knit_charge_feeder_stripe,
			sum(CASE WHEN cons_type in(140,142,148,64) THEN amount END) AS washing_cost,
			sum(CASE WHEN cons_type in(35,36,37,40) THEN amount END) AS all_over_cost,
			sum(CASE WHEN cons_type=30 THEN amount END) AS yarn_dyeing_cost,
			sum(CASE WHEN cons_type=33 THEN amount END) AS heat_setting_cost,
			sum(CASE WHEN cons_type in(25,31,26,32,60,61,62,63,72,80,81,84,85,86,87,38,39,74,78,79,101,133,137,138,139,146,147,149) THEN amount END) AS fabric_dyeing_cost,
			sum(CASE WHEN cons_type in(33,34,38,63,65,66,67,68,69,70,71,72,73,75,76,77,88,82,89,90,91,92,93,94,128,129,135,136,141,143,150,151,155,156,157,145,82,89,132,144) THEN amount END) AS fabric_finish_cost
			from wo_pri_quo_fab_conv_cost_dtls where  status_active=1  and is_deleted=0 group by quotation_id");
			foreach($pq_knit_data as $p_row_knit)
			{
				$pq_knit_arr[$p_row_knit[csf('quotation_id')]]['knit']=$p_row_knit[csf('knit_charge')];
				$pq_knit_arr[$p_row_knit[csf('quotation_id')]]['weaving']=$p_row_knit[csf('weaving_charge')];
				$pq_knit_arr[$p_row_knit[csf('quotation_id')]]['collar_cuff']=$p_row_knit[csf('knit_charge_collar_cuff')];
				$pq_knit_arr[$p_row_knit[csf('quotation_id')]]['feeder_stripe']=$p_row_knit[csf('knit_charge_feeder_stripe')];
				$pq_knit_arr[$p_row_knit[csf('quotation_id')]]['washing']=$p_row_knit[csf('washing_cost')];
				$pq_knit_arr[$p_row_knit[csf('quotation_id')]]['all_over']=$p_row_knit[csf('all_over_cost')];
				$pq_knit_arr[$p_row_knit[csf('quotation_id')]]['fabric_dyeing']=$p_row_knit[csf('fabric_dyeing_cost')];
				$pq_knit_arr[$p_row_knit[csf('quotation_id')]]['yarn_dyeing']=$p_row_knit[csf('yarn_dyeing_cost')];	
				$pq_knit_arr[$p_row_knit[csf('quotation_id')]]['heat']=$p_row_knit[csf('heat_setting_cost')];
				$pq_knit_arr[$p_row_knit[csf('quotation_id')]]['fabric_finish']=$p_row_knit[csf('fabric_finish_cost')];	
			} 
			
						
			$p_data_array=sql_select("select quotation_id,
			sum(CASE WHEN particulars_id=1 THEN commission_amount END) AS foreign_comm,
			sum(CASE WHEN particulars_id=2 THEN commission_amount END) AS local_comm
			from  wo_pri_quo_commiss_cost_dtls where status_active=1 and  is_deleted=0 group by quotation_id");// quotation_id='$data'
			foreach($p_data_array as $p_row_fl )
			{
				$price_commission_array[$p_row_fl[csf('quotation_id')]]['foreign']=$p_row_fl[csf('foreign_comm')];
				$price_commission_array[$p_row_fl[csf('quotation_id')]]['local']=$p_row_fl[csf('local_comm')];
			}
		
			?>
			
		<script>
		
				var order_value=document.getElementById('p_total_order_value').innerHTML.replace(/,/g,'');


				
				document.getElementById('fab_qut_cost').innerHTML=document.getElementById('p_total_fab_purchase').innerHTML;
				document.getElementById('fab_qut_per').innerHTML=number_format_common((document.getElementById('p_total_fab_purchase').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('fab_bud_cost').innerHTML=document.getElementById('total_fab_purchase').innerHTML;
				document.getElementById('fab_bud_per').innerHTML=number_format_common((document.getElementById('total_fab_purchase').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('fab_ver_cost').innerHTML=document.getElementById('v_total_fab_purchase').innerHTML;
				document.getElementById('fab_ver_per').innerHTML=number_format_common((document.getElementById('v_total_fab_purchase').innerHTML.split(',').join('')/document.getElementById('p_total_fab_purchase').innerHTML.split(',').join(''))*100,2);
				
				
			
				
				document.getElementById('trim_qut_cost').innerHTML=document.getElementById('p_trim_amount_td').innerHTML;
				document.getElementById('trim_qut_per').innerHTML=number_format_common((document.getElementById('p_trim_amount_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('trim_bud_cost').innerHTML=document.getElementById('trim_amount_td').innerHTML;
				document.getElementById('trim_bud_per').innerHTML=number_format_common((document.getElementById('trim_amount_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('trim_ver_cost').innerHTML=document.getElementById('v_trim_amount_td').innerHTML;
				document.getElementById('trim_ver_per').innerHTML=number_format_common((document.getElementById('v_trim_amount_td').innerHTML.split(',').join('')/document.getElementById('p_trim_amount_td').innerHTML.split(',').join(''))*100,2);


				
				var p_embel_val=(parseFloat(document.getElementById('p_print_amount_td').innerHTML.split(',').join(''))) ;
				var embel_val=(parseFloat(document.getElementById('print_amount_td').innerHTML.split(',').join(''))) ;
				
				
				var v_embel_val=(parseFloat(document.getElementById('v_print_amount_td').innerHTML.split(',').join(''))) ;
				
				
				document.getElementById('print_qut_cost').innerHTML=number_format_common(p_embel_val,2);
				document.getElementById('print_qut_per').innerHTML=number_format_common((p_embel_val/order_value)*100,2);
				document.getElementById('print_bud_cost').innerHTML=number_format_common(embel_val,2);
				document.getElementById('print_bud_per').innerHTML=number_format_common((embel_val/order_value)*100,2);
				document.getElementById('print_ver_cost').innerHTML=number_format_common(v_embel_val,2);
				document.getElementById('print_ver_per').innerHTML=number_format_common((v_embel_val/p_embel_val)*100,2)



				 p_embel_val= (parseFloat(document.getElementById('p_embroidery_amount_td').innerHTML.split(',').join(''))) ;
				 embel_val= (parseFloat(document.getElementById('embroidery_amount_td').innerHTML.split(',').join(''))) ;
				
				
				 v_embel_val= (parseFloat(document.getElementById('v_embroidery_amount_td').innerHTML.split(',').join(''))) ;
				
				
				document.getElementById('embl_qut_cost').innerHTML=number_format_common(p_embel_val,2);
				document.getElementById('embl_qut_per').innerHTML=number_format_common((p_embel_val/order_value)*100,2);
				document.getElementById('embl_bud_cost').innerHTML=number_format_common(embel_val,2);
				document.getElementById('embl_bud_per').innerHTML=number_format_common((embel_val/order_value)*100,2);
				document.getElementById('embl_ver_cost').innerHTML=number_format_common(v_embel_val,2);
				document.getElementById('embl_ver_per').innerHTML=number_format_common((v_embel_val/p_embel_val)*100,2)





				 p_embel_val= (parseFloat(document.getElementById('p_wash_amt_td').innerHTML.split(',').join(''))) ;
				 embel_val=(parseFloat(document.getElementById('wash_amt_td').innerHTML.split(',').join(''))) ;
				
				
				 v_embel_val= (parseFloat(document.getElementById('v_wash_amt_td').innerHTML.split(',').join(''))) ;
				
				
				document.getElementById('wash_qut_cost').innerHTML=number_format_common(p_embel_val,2);
				document.getElementById('wash_qut_per').innerHTML=number_format_common((p_embel_val/order_value)*100,2);
				document.getElementById('wash_bud_cost').innerHTML=number_format_common(embel_val,2);
				document.getElementById('wash_bud_per').innerHTML=number_format_common((embel_val/order_value)*100,2);
				document.getElementById('wash_ver_cost').innerHTML=number_format_common(v_embel_val,2);
				document.getElementById('wash_ver_per').innerHTML=number_format_common((v_embel_val/p_embel_val)*100,2)




				var p_commision_val=(parseFloat(document.getElementById('p_foreign_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('p_local_td').innerHTML.split(',').join('')));
				var commision_val=(parseFloat(document.getElementById('foreign_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('local_td').innerHTML.split(',').join('')));
				var v_commision_val=(parseFloat(document.getElementById('v_foreign_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('v_local_td').innerHTML.split(',').join('')));
				
				document.getElementById('commision_qut_cost').innerHTML=number_format_common(p_commision_val,2);
				document.getElementById('commision_qut_per').innerHTML=number_format_common((p_commision_val/order_value)*100,2);
				document.getElementById('commision_bud_cost').innerHTML=number_format_common(commision_val,2);
				document.getElementById('commision_bud_per').innerHTML=number_format_common((commision_val/order_value)*100,2);
				document.getElementById('commision_ver_cost').innerHTML=number_format_common(v_commision_val,2);
				document.getElementById('commision_ver_per').innerHTML=number_format_common((v_commision_val/p_commision_val)*100,2);
				
				
			
				document.getElementById('cm_qut_cost').innerHTML=document.getElementById('p_cm_cost_td').innerHTML;
				document.getElementById('cm_qut_per').innerHTML=number_format_common((document.getElementById('p_cm_cost_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('cm_bud_cost').innerHTML=document.getElementById('cm_cost_td').innerHTML;
				document.getElementById('cm_bud_per').innerHTML=number_format_common((document.getElementById('cm_cost_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('cm_ver_cost').innerHTML=document.getElementById('v_cm_cost_td').innerHTML;
				document.getElementById('cm_ver_per').innerHTML=number_format_common((document.getElementById('v_cm_cost_td').innerHTML.split(',').join('')/document.getElementById('p_cm_cost_td').innerHTML.split(',').join(''))*100,2);
				
				document.getElementById('cost_qut_cost').innerHTML=document.getElementById('p_tot_cost_td').innerHTML;
				document.getElementById('cost_qut_per').innerHTML=number_format_common((document.getElementById('p_tot_cost_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('cost_bud_cost').innerHTML=document.getElementById('tot_cost_td').innerHTML;
				document.getElementById('cost_bud_per').innerHTML=number_format_common((document.getElementById('tot_cost_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('cost_ver_cost').innerHTML=document.getElementById('v_tot_cost_td').innerHTML;
				document.getElementById('cost_ver_per').innerHTML=number_format_common((document.getElementById('v_tot_cost_td').innerHTML.split(',').join('')/document.getElementById('p_tot_cost_td').innerHTML.split(',').join(''))*100,2);
				
				document.getElementById('ordVal_qut_cost').innerHTML=document.getElementById('p_total_order_value').innerHTML;
				document.getElementById('ordVal_qut_per').innerHTML=number_format_common((document.getElementById('p_total_order_value').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('ordVal_bud_cost').innerHTML=document.getElementById('total_order_amount2').innerHTML;
				document.getElementById('ordVal_bud_per').innerHTML=number_format_common((document.getElementById('total_order_amount2').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('ordVal_ver_cost').innerHTML=document.getElementById('total_order_amount2').innerHTML;
				document.getElementById('ordVal_ver_per').innerHTML=number_format_common((document.getElementById('total_order_amount2').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('proLoss_qut_cost').innerHTML=document.getElementById('p_profitt_td').innerHTML;
				document.getElementById('proLoss_qut_per').innerHTML=number_format_common((document.getElementById('p_profitt_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('proLoss_bud_cost').innerHTML=document.getElementById('profitt_td').innerHTML;
				document.getElementById('proLoss_bud_per').innerHTML=number_format_common((document.getElementById('profitt_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('proLoss_ver_cost').innerHTML=document.getElementById('v_profitt_td').innerHTML;
				document.getElementById('proLoss_ver_per').innerHTML=number_format_common((document.getElementById('v_profitt_td').innerHTML.split(',').join('')/document.getElementById('p_profitt_td').innerHTML.split(',').join(''))*100,2);
				
				document.getElementById('expPro_qut_cost').innerHTML=number_format_common(document.getElementById('p_expProfitt_td').innerHTML,2);
				document.getElementById('expPro_qut_per').innerHTML=number_format_common((document.getElementById('p_expProfitt_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('expPro_bud_cost').innerHTML=number_format_common(document.getElementById('expProfitt_td').innerHTML,2);
				document.getElementById('expPro_bud_per').innerHTML=number_format_common((document.getElementById('expProfitt_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('expPro_ver_cost').innerHTML=number_format_common(document.getElementById('v_expProfitt_td').innerHTML,2);
				document.getElementById('expPro_ver_per').innerHTML=number_format_common((document.getElementById('v_expProfitt_td').innerHTML.split(',').join('')/document.getElementById('p_expProfitt_td').innerHTML.split(',').join(''))*100,2);
				
				document.getElementById('expProv_qut_cost').innerHTML=document.getElementById('p_expect_variance_td').innerHTML;
				document.getElementById('expProv_qut_per').innerHTML=number_format_common((document.getElementById('p_expect_variance_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('expProv_bud_cost').innerHTML=document.getElementById('expect_variance_td').innerHTML;
				document.getElementById('expProv_bud_per').innerHTML=number_format_common((document.getElementById('expect_variance_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('exp_prof_div').text=number_format_common((document.getElementById('p_expProfitt_td').innerHTML.split(',').join('')/order_value)*100,2);
				//document.getElementById('exp_prof_div').text=
				$('#exp_prof_div').text(number_format_common((document.getElementById('p_expProfitt_td').innerHTML.split(',').join('')/order_value)*100)); 
				 
				
                function toggle() 
                {
                    var ele = document.getElementById("yarn_summary");
                    
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
            
            
            
            <?
            	ob_start();
			?>
            <div style="width:3390px;">
            <div style="width:900px;" align="left">
                <table width="900" cellpadding="0" cellspacing="2" border="0">
                    <tr>
                        <td width="650" align="left">
                            <table width="600" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="2">
                                <thead align="center">
                                    <tr><th colspan="8">Price Quotation Vs Budget Variance Summary</th></tr>
                                    <tr>
                                        <th width="20">SL</th>
                                        <th width="140">Particulars</th>
                                        <th width="110">Mkt. Cost</th>
                                        <th width="80">% On Order Value</th>
                                        <th width="110">Budgeted Cost</th>
                                        <th width="80">% On Order Value</th>
                                        <th width="100">Variance</th>
                                        <th>% On Mkt. Cost</th>
                                    </tr>
                                </thead>
                               
                                <tr bgcolor="<?  echo $style; ?>">
                                    <td>1</td><td>Fabric Purchase</td>
                                    <td align="right" id="fab_qut_cost"></td>
                                    <td align="right" id="fab_qut_per"></td>
                                    <td align="right" id="fab_bud_cost"></td>
                                    <td align="right" id="fab_bud_per"></td>
                                    <td align="right" id="fab_ver_cost"></td>
                                    <td align="right" id="fab_ver_per"></td>
                                </tr>
                               
                               
                                <tr bgcolor="<?  echo $style1; ?>">
                                    <td>2</td><td>Trims Cost</td>
                                    <td align="right" id="trim_qut_cost"></td>
                                    <td align="right" id="trim_qut_per"></td>
                                    <td align="right" id="trim_bud_cost"></td>
                                    <td align="right" id="trim_bud_per"></td>
                                    <td align="right" id="trim_ver_cost"></td>
                                    <td align="right" id="trim_ver_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>3</td><td>Print Cost</td>
                                    <td align="right" id="print_qut_cost"></td>
                                    <td align="right" id="print_qut_per"></td>
                                    <td align="right" id="print_bud_cost"></td>
                                    <td align="right" id="print_bud_per"></td>
                                    <td align="right" id="print_ver_cost"></td>
                                    <td align="right" id="print_ver_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>4</td><td>Emb. Cost</td>
                                    <td align="right" id="embl_qut_cost"></td>
                                    <td align="right" id="embl_qut_per"></td>
                                    <td align="right" id="embl_bud_cost"></td>
                                    <td align="right" id="embl_bud_per"></td>
                                    <td align="right" id="embl_ver_cost"></td>
                                    <td align="right" id="embl_ver_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>5</td><td> Wash Cost</td>
                                    <td align="right" id="wash_qut_cost"></td>
                                    <td align="right" id="wash_qut_per"></td>
                                    <td align="right" id="wash_bud_cost"></td>
                                    <td align="right" id="wash_bud_per"></td>
                                    <td align="right" id="wash_ver_cost"></td>
                                    <td align="right" id="wash_ver_per"></td>
                                </tr>

                                
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>6</td><td>Commision Cost</td>
                                    <td align="right" id="commision_qut_cost"></td>
                                    <td align="right" id="commision_qut_per"></td>
                                    <td align="right" id="commision_bud_cost"></td>
                                    <td align="right" id="commision_bud_per"></td>
                                    <td align="right" id="commision_ver_cost"></td>
                                    <td align="right" id="commision_ver_per"></td>
                                </tr>
                               
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>7</td><td>CM Cost</td>
                                    <td align="right" id="cm_qut_cost"></td>
                                    <td align="right" id="cm_qut_per"></td>
                                    <td align="right" id="cm_bud_cost"></td>
                                    <td align="right" id="cm_bud_per"></td>
                                    <td align="right" id="cm_ver_cost"></td>
                                    <td align="right" id="cm_ver_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>8</td><td>Total Cost</td>
                                    <td align="right" id="cost_qut_cost"></td>
                                    <td align="right" id="cost_qut_per"></td>
                                    <td align="right" id="cost_bud_cost"></td>
                                    <td align="right" id="cost_bud_per"></td>
                                    <td align="right" id="cost_ver_cost"></td>
                                    <td align="right" id="cost_ver_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>9</td><td>Total Order Value</td>
                                    <td align="right" id="ordVal_qut_cost"></td>
                                    <td align="right" id="ordVal_qut_per"></td>
                                    <td align="right" id="ordVal_bud_cost"></td>
                                    <td align="right" id="ordVal_bud_per"></td>
                                    <td align="right" id="ordVal_ver_cost"></td>
                                    <td align="right" id="ordVal_ver_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>10</td><td>Profit/Loss </td>
                                    <td align="right" id="proLoss_qut_cost"></td>
                                    <td align="right" id="proLoss_qut_per"></td>
                                    <td align="right" id="proLoss_bud_cost"></td>
                                    <td align="right" id="proLoss_bud_per"></td>
                                    <td align="right" id="proLoss_ver_cost"></td>
                                    <td align="right" id="proLoss_ver_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>11</td><td>Expected Profit <div style="display: inline;" id="exp_prof_div"></div>%</td>
                                    <td align="right" id="expPro_qut_cost"></td>
                                    <td align="right" id="expPro_qut_per"></td>
                                    <td align="right" id="expPro_bud_cost"></td>
                                    <td align="right" id="expPro_bud_per"></td>
                                    <td align="right" id="expPro_ver_cost"></td>
                                    <td align="right" id="expPro_ver_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>12</td><td>Expt.Profit Variance </td>
                                    <td align="right" id="expProv_qut_cost"></td>
                                    <td align="right" id="expProv_qut_per"></td>
                                    <td align="right" id="expProv_bud_cost"></td>
                                    <td align="right" id="expProv_bud_per"></td>
                                    <td align="right" id="expProv_ver_cost"></td>
                                    <td align="right" id="expProv_ver_per"></td>
                                </tr>
                            </table>
                        </td>
                        <td colspan="5" style="min-height:800px; max-height:100%" align="center" valign="top"></td>
                    </tr>
                </table>
            </div>
            <br/>
            <h3 align="left" id="accordion_h2" style="width:3390px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel2', '')"> -Search Panel</h3>
        	<fieldset style="width:100%;" id="content_search_panel2">	
            <table width="3490">
                <tr class="form_caption">
                    <td align="left">Decimal rounded to 2 digit.</td>
                    <td colspan="50" align="center"><strong>Order Wise Budget Report</strong></td>
                </tr>
                <tr class="form_caption">
                    <td colspan="50" align="center"><strong><? echo $company_library[$company_name]; ?></strong></td>
                </tr>
                <tr class="form_caption">
                        <td align="left"><strong>Details Report </strong></td>
                </tr>
            </table>
            <? $asking_profit_head=$asking_profit_arr[$company_name]['asking_profit']; 
				if(str_replace("'","",$cbo_search_date)==1) $head_cap="Ship. Date";
				else if(str_replace("'","",$cbo_search_date)==2) $head_cap="PO Recv. Date";
				else $head_cap="PO Insert Date";
			?>
            <table id="table_header_1" class="rpt_table" width="3390" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr>
                        <th width="40" rowspan="2">SL</th>
                        <th width="70" rowspan="2">Buyer</th>
                        <th width="70" rowspan="2">Job No</th>
                        <th width="100" rowspan="2">Order No</th>
                        <th width="100" rowspan="2">Ready To Approve
                        	
                             <select name="cbo_ready_to" id="cbo_ready_to" class="combo_boxes flt" style="width:95px" onChange="fn_report_generated2(4);">
                                    <option value='0'> All </option>
									<?
                                    foreach ($yes_no as $key => $value) {
                                        ?>
                                        <option value=<?
                                        echo $key;
                                        if ($key == $cbo_ready_to) {
                                            ?> selected <?php } ?>><? echo "$value" ?> </option>
                                                <?
                                            }
                                            ?>
                                </select> 
                        </th>
                        <th width="80" rowspan="2">File No</th>
                        <th width="80" rowspan="2">Master / Int.  Ref: No</th>
                        <th width="100" rowspan="2">Order Status</th>
                        <th width="110" rowspan="2">Style</th>
                        <th width="110" rowspan="2">Item Name</th>

                        <th width="110" rowspan="2">Team Leader</th>

                        <th width="110" rowspan="2">Dealing</th>
                        <th width="70" rowspan="2"><? echo $head_cap; ?></th>
                        <th width="90" rowspan="2">Order Qty</th>
                        <th width="90" rowspan="2">Avg Unit Price</th>
                        <th width="100" rowspan="2">Order Value</th>
                        <th width="100" rowspan="2">Particulars</th>
                        <th width="100" rowspan="2">Fabric Cost</th>
                        <th width="100" rowspan="2">Trim Cost</th>
                        <th colspan="3">Embell. Cost</th>
                         <th width="80" rowspan="2">Wash Cost</th>
                        <th width="120" rowspan="2">Commercial Cost</th>
                        <th colspan="2">Commission</th>

                     <!--    <th width="100" rowspan="2">Testing Cost</th>
                        <th width="100" rowspan="2">Freight Cost</th>
                        <th width="120" rowspan="2">Inspection Cost</th>
                        <th width="100" rowspan="2">Certificate Cost</th>
                        <th width="100" rowspan="2">Commn OH</th>
                        <th width="100" rowspan="2">Courier Cost</th> -->

                        <th width="120" rowspan="2">CM/PCS</th>
                        <th width="100" rowspan="2">CM Cost</th>
                        <th width="100" rowspan="2">Total Cost</th>
                        <th width="100" rowspan="2">Profit/Loss</th>
                        <th width="100" rowspan="2">Profit/Loss %</th>
                        <th width="100" rowspan="2">Expected Profit</th>
                        <th width="100" rowspan="2">Expected Profit %</th>
                        <th width="100" rowspan="2">Expt. Profit Variance</th>
                        <th  rowspan="2">SMV</th>
                        
                    </tr>
                    <tr>
                        <th width="80">Printing</th>
                        <th width="85">Embroidery</th>
                        <th width="80">Special Works</th>
                        <th width="120">Foreign</th>
                        <th width="120">Local</th>
                    </tr>
                </thead>
            </table>
            <div style="width:3430px; max-height:400px; overflow-y:scroll" id="scroll_body">
            <table class="rpt_table" width="3390" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
            <? 
			$i=1; $total_order_qty=0;  $total_yarn_dyeing_cost=0; $total_yarn_cost=0; $total_order_value=0;$total_purchase_cost=0; $grand_tot_trims_cost=0; $total_fabric_dyeing_cost=0; $total_knitting_cost=0; $total_heat_setting_cost=0;$total_finishing_cost=0; $total_washing_cost=0; $fabric_dyeing_cost_dzn=0; $other_cost=0; $all_over_print_cost=0; $total_trim_cost=0; $total_commercial_cost=0;$p_total_yarn_cons=0;$total_yarn_cons=0;
			$total_smv_price=0;$total_smv_pre=0;$total_smv_vari=0;
			if($cbo_ready_to==1 || $cbo_ready_to==2)
			{
				$sql="select a.job_no_prefix_num,a.set_smv, b.insert_date, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.is_confirmed, a.quotation_id, a.agent_name, a.avg_unit_price, a.dealing_marchant, a.gmts_item_id, a.total_set_qnty as ratio, b.plan_cut, b.id as po_id, b.po_number, b.pub_shipment_date, b.po_received_date, b.po_quantity, b.unit_price, b.grouping, b.file_no, b.po_total_price from  wo_po_break_down b, wo_po_details_master a , wo_pre_cost_mst c   where a.job_no=b.job_no_mst and  a.job_no=c.job_no and c.job_no=b.job_no_mst and c.entry_from=158  and a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 $cbo_ready_to_cond $date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond $season_cond $internal_ref_cond $file_no_cond  order by b.pub_shipment_date, b.id";
			}
			else
			{
				$sql="select a.job_no_prefix_num,a.set_smv, b.insert_date, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.is_confirmed, a.quotation_id, a.agent_name, a.avg_unit_price, a.dealing_marchant, a.gmts_item_id, a.total_set_qnty as ratio, b.plan_cut, b.id as po_id, b.po_number, b.pub_shipment_date, b.po_received_date, b.po_quantity, b.unit_price, b.grouping, b.file_no, b.po_total_price from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c where a.job_no=b.job_no_mst and  a.job_no=c.job_no and c.job_no=b.job_no_mst and c.entry_from=158  and a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond $season_cond $internal_ref_cond $file_no_cond order by b.pub_shipment_date, b.id";
				 
			}
			 //echo  "10**".$sql; die;
			
			//echo $sql;$order_status_cond
			$result=sql_select($sql);
			$tot_rows=count($result);
			
           
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
				  $condition->country_ship_date(" between '$start_date' and '$end_date'");
			 }
			 if(str_replace("'","",$cbo_search_date) ==2 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
			 {
				 $condition->po_received_date(" between '$start_date' and '$end_date'");
				 //and b.po_received_date between '$start_date' and '$end_date' 
				// echo 'FFGG';
			 }
			
			 if(str_replace("'","",$cbo_search_date)==3 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
			 {
				
				
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
			$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
			$yarn= new yarn($condition);
			$yarn_req_qty_arr=$yarn->getOrderWiseYarnQtyArray();
			
			//print_r($yarn_req_qty_arr);
			$trims= new trims($condition);
			$trims_costing_arr=$trims->getAmountArray_by_order();

			$fabric= new fabric($condition);
			$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
			$conversion= new conversion($condition);
			$conversion_costing_arr_process=$conversion->getAmountArray_by_orderAndProcess();
			
			$emblishment= new emblishment($condition);
			$emblishment_costing_arr_name=$emblishment->getAmountArray_by_orderAndEmbname();
			$commission= new commision($condition);
			$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
			$commercial= new commercial($condition);
			$commercial_costing_arr=$commercial->getAmountArray_by_order();
			$other= new other($condition);
			$other_costing_arr=$other->getAmountArray_by_order();
			$wash= new wash($condition);
			$emblishment_costing_arr_name_wash=$wash->getAmountArray_by_orderAndEmbname();
			
			$knit_cost_arr=array(1,2,3,4);
			$fabric_dyeingCost_arr=array(25,31,26,32,60,61,62,63,72,80,81,84,85,86,87,38,39,74,78,79,101,133,137,138,139,146,147,149);
			$aop_cost_arr=array(35,36,37,40);
			$fab_finish_cost_arr=array(33,34,38,63,65,66,67,68,69,70,71,72,73,75,76,77,88,82,89,90,91,92,93,94,128,129,135,136,141,143,150,151,155,156,157,145,82,89,132,144);
			$washing_cost_arr=array(140,142,148,64);
			
			//echo $yarn->getQuery(); die;
			
			foreach($result as $row )
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				$order_value=$row[csf('po_total_price')];
				$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
				$plancut_value=$plan_cut_qnty*$row[csf('unit_price')];
				$order_qty_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
				if(str_replace("'","",$cbo_search_date)==1)
				{
					$ship_po_recv_date=change_date_format($row[csf('pub_shipment_date')]);
				}
				else if(str_replace("'","",$cbo_search_date)==2)
				{
					$ship_po_recv_date=change_date_format($row[csf('po_received_date')]);
				}
				else if(str_replace("'","",$cbo_search_date)==3)
				{
					$insert_date=explode(" ",$row[csf('insert_date')]);
					$ship_po_recv_date=change_date_format($insert_date[0]);
				}
				
				$dzn_qnty_p=0;
				$price_costing_per_id=$price_costing_perArray[$row[csf('quotation_id')]]['costing_per'];
				$smv_price=$price_costing_perArray[$row[csf('quotation_id')]]['smv'];

				if($price_costing_per_id==1) $dzn_qnty_p=12;
				else if($price_costing_per_id==3) $dzn_qnty_p=12*2;
				else if($price_costing_per_id==4) $dzn_qnty_p=12*3;
				else if($price_costing_per_id==5) $dzn_qnty_p=12*4;
				else $dzn_qnty_p=1;


				// if($price_costing_per_id==1) $dzn_qnty_p=1;
				// else if($price_costing_per_id==3) $dzn_qnty_p=2;
				// else if($price_costing_per_id==4) $dzn_qnty_p=3;
				// else if($price_costing_per_id==5) $dzn_qnty_p=4;
				// else $dzn_qnty_p=1/12;

				$dzn_qnty_p=$dzn_qnty_p*$row[csf('ratio')];
				
				$p_commercial_cost_dzn=$price_fabriccostArray[$row[csf('quotation_id')]]['comm_cost'];
				$p_commercial_cost=($p_commercial_cost_dzn/$dzn_qnty_p)*$order_qty_pcs;
				if(is_infinite($p_commercial_cost) || is_nan($p_commercial_cost)){$p_commercial_cost=0;}
				$price_fabricData=array_filter(explode(",",substr($price_fabric_data_Array[$row[csf('quotation_id')]],0,-1)));
				$p_fab_precost_Data=explode(",",substr($p_fab_precost_arr[$row[csf('quotation_id')]],0,-1));
				//echo $row[csf('quotation_id')];
				$quote_fab_source_id=""; $quote_color_type_id=""; $p_avg_rate=0; $p_yarn_costing=0; $p_fab_purchase=0;
				foreach($price_fabricData as $p_fabricRow)
				{
					$p_fabricRow=explode("**",$p_fabricRow);
					$p_fab_nature_id=$p_fabricRow[0];	
					$p_fab_source_id=$p_fabricRow[1];
					$p_fab_rate=$p_fabricRow[2];
					$p_yarn_qty=$p_fabricRow[3];
					$p_yarn_amount=$p_fabricRow[4];
					$p_color_type=$p_fabricRow[5];
					if($quote_fab_source_id=="") $quote_fab_source_id=$p_fab_source_id; else $quote_fab_source_id.=','.$p_fab_source_id;
					if($quote_color_type_id=="") $quote_color_type_id=$p_color_type; else $quote_color_type_id.=','.$p_color_type;
					if($p_fab_source_id==2)
					{
						foreach($p_fab_precost_Data as $p_fab_row)
						{
							
						}
					}
					else if($p_fab_source_id==1 || $p_fab_source_id==3)
					{
						$p_avg_rate=$p_yarn_amount/$p_yarn_qty;
						if(is_infinite($p_avg_rate) || is_nan($p_avg_rate)){$p_avg_rate=0;}
						//$p_yarn_costing=$p_yarn_amount/$dzn_qnty_p*$plan_cut_qnty;	
					}
				}
					foreach($p_fab_precost_Data as $p_fab_row)
						{
							$p_fab_dataRow=explode("**",$p_fab_row);
							$p_fab_requirment=$p_fab_dataRow[0];
							$p_fab_pcs=$p_fab_dataRow[1];
							$p_fab_rate=$p_fab_dataRow[2];
							$p_fab_sourceId=$p_fab_dataRow[3];
							
							$p_fab_purchase_qty=$p_fab_requirment/$dzn_qnty_p*$order_qty_pcs; 
						
							$p_fab_purchase+=$p_fab_purchase_qty*$p_fab_rate; 
							
								//echo $p_fab_row.'='.$p_fab_requirment.'*'.$dzn_qnty_p.'*'.$order_qty_pcs.'<br>';
						}
				
				$p_yarn_costing=($p_yarn_amount/$dzn_qnty_p)*$order_qty_pcs;
				if(is_infinite($p_yarn_costing) || is_nan($p_yarn_costing)){$p_yarn_costing=0;}
				$p_yarn_cons=($p_yarn_qty/$plan_cut_qnty)*$dzn_qnty_p;
				if(is_infinite($p_yarn_cons) || is_nan($p_yarn_cons)){$p_yarn_cons=0;}
				$p_yarn_cost_percent=($p_yarn_costing/$order_value)*100;
				if(is_infinite($p_yarn_cost_percent) || is_nan($p_yarn_cost_percent)){$p_yarn_cost_percent=0;}
				$p_kniting_cost=$pq_knit_arr[$row[csf('quotation_id')]]['knit']+$pq_knit_arr[$row[csf('quotation_id')]]['weaving']+$pq_knit_arr[$row[csf('quotation_id')]]['collar_cuff']+$pq_knit_arr[$row[csf('quotation_id')]]['feeder_stripe'];
				$p_knit_cost_dzn=$p_kniting_cost; 
				$p_knit_cost=($p_kniting_cost/$dzn_qnty_p)*$order_qty_pcs;
				if(is_infinite($p_knit_cost) || is_nan($p_knit_cost)){$p_knit_cost=0;}
				$p_yarn_dyeing_cost_dzn=$pq_knit_arr[$row[csf('quotation_id')]]['yarn_dyeing'];
				$p_yarn_dyeing_cost=($p_yarn_dyeing_cost_dzn/$dzn_qnty_p)*$plan_cut_qnty;
				if(is_infinite($p_yarn_dyeing_cost) || is_nan($p_yarn_dyeing_cost)){$p_yarn_dyeing_cost=0;}
				$p_fabric_dyeing_cost_dzn=$pq_knit_arr[$row[csf('quotation_id')]]['fabric_dyeing'];
				//echo $p_fabric_dyeing_cost_dzn.'='.$dzn_qnty_p.'*'.$order_qty_pcs;
				$p_fabric_dyeing_cost=($p_fabric_dyeing_cost_dzn/$dzn_qnty_p)*$order_qty_pcs;
				if(is_infinite($p_fabric_dyeing_cost) || is_nan($p_fabric_dyeing_cost)){$p_fabric_dyeing_cost=0;}
				$p_heat_setting_cost=($pq_knit_arr[$row[csf('quotation_id')]]['heat']/$dzn_qnty_p)*$order_qty_pcs;
				if(is_infinite($p_heat_setting_cost) || is_nan($p_heat_setting_cost)){$p_heat_setting_cost=0;}
				$p_fabric_finish=($pq_knit_arr[$row[csf('quotation_id')]]['fabric_finish']/$dzn_qnty_p)*$order_qty_pcs;
				if(is_infinite($p_fabric_finish) || is_nan($p_fabric_finish)){$p_fabric_finish=0;}
				$p_washing_cost=($pq_knit_arr[$row[csf('quotation_id')]]['washing']/$dzn_qnty_p)*$order_qty_pcs;
				if(is_infinite($p_washing_cost) || is_nan($p_washing_cost)){$p_washing_cost=0;}
				$p_all_over_cost=($pq_knit_arr[$row[csf('quotation_id')]]['all_over']/$dzn_qnty_p)*$order_qty_pcs;
				if(is_infinite($p_all_over_cost) || is_nan($p_all_over_cost)){$p_all_over_cost=0;}
				
				$ex_quote_fab_source_id=implode(',',array_unique(explode(",",$quote_fab_source_id)));
				$ex_quote_color_type_id=array_unique(explode(",",$quote_color_type_id));
				/*$fab_source_array=array(2,3,4);
				if(array_intersect($fab_source_array,$ex_quote_fab_source_id))
				{
					
				}*/$color_yarn=""; $color_pur=""; $color_knit_q="";
				if($ex_quote_fab_source_id=='1,2' || $ex_quote_fab_source_id=='2,1') 
				{
					if($p_yarn_costing<=0) $color_yarn="";
					if($p_fab_purchase<=0) $color_pur="red"; else $color_pur="";
					if($p_yarn_costing<=0) $color_knit_q="red"; else $color_knit_q="";
				}
				else if($ex_quote_fab_source_id==1 && $ex_quote_fab_source_id==2 ) 
				{
					if($p_yarn_costing<=0) $color_yarn="red"; else $color_yarn="";
					if($p_fab_purchase<=0) $color_pur="red"; else $color_pur="";
					if($p_knit_cost<=0) $color_knit_q="red"; else $color_knit_q="";	
				}
				else if($ex_quote_fab_source_id==1) 
				{
					if($p_yarn_costing<=0) $color_yarn="red"; else $color_yarn="";
					if($p_fab_purchase<=0) $color_pur="";
					if($p_knit_cost<=0) $color_knit_q="red"; else $color_knit_q="";	
				}
				else if($ex_quote_fab_source_id==2) 
				{
					if($p_yarn_costing<=0) $color_yarn="";
					if($p_fab_purchase<=0) $color_pur="red"; else $color_pur="";
					if($p_knit_cost<=0) $color_knit_q="red"; else $color_knit_q="";	
				}
				else 
				{
					if($p_yarn_costing<=0) $color_yarn="red"; else $color_yarn="";
					if($p_fab_purchase<=0) $color_pur="red"; else $color_pur="";
					if($p_knit_cost<=0) $color_knit_q="red"; else $color_knit_q="";
				}
				
				//echo $p_yarn_costing.'=='.$color_yarn;
				$color_fab_dy="";
				foreach($ex_quote_color_type_id as $color_type_id)
				{
					if($color_type_id==2 || $color_type_id==3  || $color_type_id==4  || $color_type_id==6)
					{
						if($p_fabric_dyeing_cost<=0) if ($color_fab_dy=="") $color_fab_dy=""; else $color_fab_dy.=','."";
					}
					else
					{
						if($p_fabric_dyeing_cost<=0)  if ($color_fab_dy=="") $color_fab_dy="red"; else $color_fab_dy.=','."red"; else $color_fab_dy="";
					}
				}
				$ex_quote_color_type=implode(',',array_unique(explode(",",$color_fab_dy)));
				
				if($p_fabric_finish<=0) $color_finish="red"; else $color_finish="";	
				if($p_commercial_cost<=0) $color_com="red"; else $color_com="";	
				$p_trim_amount= $price_fabriccostArray[$row[csf('quotation_id')]]['trims_cost']/$dzn_qnty_p*$order_qty_pcs;
				if(is_infinite($p_trim_amount) || is_nan($p_trim_amount)){$p_trim_amount=0;}
				$p_print_amount=($price_fab_emb[$row[csf('quotation_id')]]['print']/$dzn_qnty_p)*$order_qty_pcs;
				if(is_infinite($p_print_amount) || is_nan($p_print_amount)){$p_print_amount=0;}
				$p_embroidery_amount=($price_fab_emb[$row[csf('quotation_id')]]['embroidery']/$dzn_qnty_p)*$order_qty_pcs;
				if(is_infinite($p_embroidery_amount) || is_nan($p_embroidery_amount)){$p_embroidery_amount=0;}
				$p_special_amount=($price_fab_emb[$row[csf('quotation_id')]]['special']/$dzn_qnty_p)*$order_qty_pcs;
				if(is_infinite($p_special_amount) || is_nan($p_special_amount)){$p_special_amount=0;}
				$p_wash_amt=($price_fab_emb[$row[csf('quotation_id')]]['wash']/$dzn_qnty_p)*$order_qty_pcs;
				if(is_infinite($p_wash_amt) || is_nan($p_wash_amt)){$p_wash_amt=0;}
				$p_other_amount=($price_fab_emb[$row[csf('quotation_id')]]['other']/$dzn_qnty_p)*$order_qty_pcs;
				if(is_infinite($p_other_amount) || is_nan($p_other_amount)){$p_other_amount=0;}
				$p_foreign=$price_commission_array[$row[csf('quotation_id')]]['foreign']/$dzn_qnty_p*$order_qty_pcs;
				if(is_infinite($p_foreign) || is_nan($p_foreign)){$p_foreign=0;}
				$p_local=$price_commission_array[$row[csf('quotation_id')]]['local']/$dzn_qnty_p*$order_qty_pcs;
				if(is_infinite($p_local) || is_nan($p_local)){$p_local=0;}
				$p_test_cost=$price_fabriccostArray[$row[csf('quotation_id')]]['lab_test']/$dzn_qnty_p*$order_qty_pcs;
				if(is_infinite($p_test_cost) || is_nan($p_test_cost)){$p_test_cost=0;}
				$p_freight_cost= $price_fabriccostArray[$row[csf('quotation_id')]]['freight']/$dzn_qnty_p*$order_qty_pcs;
				if(is_infinite($p_freight_cost) || is_nan($p_freight_cost)){$p_freight_cost=0;}
				$p_inspection=$price_fabriccostArray[$row[csf('quotation_id')]]['inspection']/$dzn_qnty_p*$order_qty_pcs;
				if(is_infinite($p_inspection) || is_nan($p_inspection)){$p_inspection=0;}
				$p_certificate_cost=$price_fabriccostArray[$row[csf('quotation_id')]]['certificate_pre_cost']/$dzn_qnty_p*$order_qty_pcs;
				if(is_infinite($p_certificate_cost) || is_nan($p_certificate_cost)){$p_certificate_cost=0;}
				$p_common_oh=$price_fabriccostArray[$row[csf('quotation_id')]]['common_oh']/$dzn_qnty_p*$order_qty_pcs;
				if(is_infinite($p_common_oh) || is_nan($p_common_oh)){$p_common_oh=0;}
				$p_currier_cost=$price_fabriccostArray[$row[csf('quotation_id')]]['currier_pre_cost']/$dzn_qnty_p*$order_qty_pcs;
				if(is_infinite($p_currier_cost) || is_nan($p_currier_cost)){$p_currier_cost=0;}
				$p_cm_cost_dzn=$price_fabriccostArray[$row[csf('quotation_id')]]['c_cost'];
				$p_cm_cost=$p_cm_cost_dzn/$dzn_qnty_p*$order_qty_pcs;
				if(is_infinite($p_cm_cost) || is_nan($p_cm_cost)){$p_cm_cost=0;}
				
				$total_p_cost=$p_fab_purchase  +  $p_trim_amount  +  $p_print_amount  +  $p_embroidery_amount  +  $p_special_amount  +  $p_wash_amt  +  $p_commercial_cost  +  $p_foreign  +  $p_local +  $p_cm_cost;
			 $total_p_cost=number_format($total_p_cost,2,".", "");
				
				if($p_trim_amount<=0) $color_trim="red"; else $color_trim="";	
				if($p_cm_cost<=0) $color="red"; else $color="";
				
				$max_profit=$asking_profit_arr[$row[csf('company_name')]]['max_profit'];
				$company_asking=$costing_date_arr[$row[csf('job_no')]]['ask'];
				
				$total_p_profit=$order_value-$total_p_cost;
				$total_p_profit_percentage2=($total_p_profit/$order_value)*100; 
				if(is_infinite($total_p_profit_percentage2) || is_nan($total_p_profit_percentage2)){$total_p_profit_percentage2=0;}

				if($total_p_profit_percentage2<=0 ) $color_pl="red";
				else if($total_p_profit_percentage2>$max_profit) $color_pl="yellow";	
				else if($total_p_profit_percentage2<=$max_profit) $color_pl="green";	
				else $color_pl="";	
				$p_expected_profit=$company_asking*$order_value/100;
				if(is_infinite($p_expected_profit) || is_nan($p_expected_profit)){$p_expected_profit=0;}
				$tot_expect_variance=$total_p_profit-$p_expected_profit; 
				$ready_to_approved=$job_approve_arr[$row[csf('job_no')]]['ready_to_approved'];
				if($ready_to_approved==0 || $ready_to_approved==2) $ready_to_approved=2;else $ready_to_approved=$ready_to_approved;
				?>
                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                    <td width="40" rowspan="4"><? echo $i; ?></td>
                    <td width="70" rowspan="4"><p><? echo $buyer_library[$row[csf('buyer_name')]] ?></p></td>
                    <td width="70" rowspan="4"><p><? echo $row[csf('job_no_prefix_num')];  ?></p></td>
                    <td width="100" rowspan="4"><p><? echo $row[csf('po_number')]; ?></p></td>
                    <td width="100" rowspan="4"><p><? echo $yes_no[$ready_to_approved]; ?></p></td>
                    <td width="80" rowspan="4"><p><? echo $row[csf('file_no')]; ?></p></td>
                    <td width="80" rowspan="4"><p><? echo $row[csf('grouping')]; ?></p></td>
                    <td width="100" rowspan="4"><p><? echo  $order_status[$row[csf('is_confirmed')]]; ?></p></td>
                    <td width="110" rowspan="4"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                    <td width="110" rowspan="4"><p><? $gmts_item=''; $gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
						foreach($gmts_item_id as $item_id)
						{
							if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=", ".$garments_item[$item_id];
						}
						echo $gmts_item; ?></p></td>

                    <td rowspan="4" width="110"><p><? echo $team_leader_arr[$row[csf('dealing_marchant')]]; ?></p></td>
                    <td rowspan="4" width="110"><p><? echo $team_member_arr[$row[csf('dealing_marchant')]]; ?></p></td>

                    <td rowspan="4" width="70"><p><? echo '&nbsp;'.$ship_po_recv_date; ?></p></td>
                    <td rowspan="4" width="90" align="right"><p><? echo number_format($row[csf('po_quantity')],2); ?></p></td>
                    <td rowspan="4" width="90" align="right"><p><? echo number_format($row[csf('unit_price')],4); ?></p></td>
                    <td rowspan="4" width="100" align="right"><p><? echo number_format($order_value,2); ?></p></td>
                    <td width="100">Price Quotation</td>
                   
                    
                    <td width="100" align="right" bgcolor="<? echo $color_pur; ?>"><a href="##" onClick="generate_pricecost_purchase_popup('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $p_fab_source_id; ?>','<? echo $row[csf('quotation_id')]; ?>','fab_price_purchase_detail')"><? echo number_format($p_fab_purchase,2); ?></a></td>

                   

                    <td width="100" align="right" bgcolor="<? echo $color_trim; ?>"><a href="##" onClick="generate_pricost_trim_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('quotation_id')]; ?>','trim_cost_price_detail')"><? echo number_format($p_trim_amount,2); ?></a></td>

                    <td width="80" align="right"><a href="##" onClick="generate_pricost_embell_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('quotation_id')]; ?>','price_print_cost_detail')"><? echo number_format($p_print_amount,2); ?></a></td>

                    <td width="85" align="right"><a href="##" onClick="generate_pricost_embell_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('quotation_id')]; ?>','price_embroidery_cost_detail')"><? echo number_format($p_embroidery_amount,2); ?></a></td>
                    <td width="80" align="right"><? echo number_format($p_special_amount,2); ?></td>
                    <td width="80" align="right"><a href="##" onClick="generate_pricost_embell_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('quotation_id')]; ?>','price_wash_cost_detail')"><? echo number_format($p_wash_amt,2); ?></a></td>


                      <? /* ?>
                    <td width="80" align="right"><? echo number_format($p_other_amount,2); ?></td>
                      <? */ ?>

                    <td width="120" align="right" bgcolor="<? echo $color_com; ?>"><? echo number_format($p_commercial_cost,2); ?></td>
                    <td width="120" align="right"><? echo number_format($p_foreign,2) ?></td>
                    <td width="120" align="right"><? echo number_format($p_local,2) ?></td>

                  

                    <td width="120" align="right"><? echo number_format($p_cm_cost_dzn,2);?></td>
                    <td width="100" align="right" bgcolor="<? echo $color; ?>"><? echo number_format($p_cm_cost,2);?></td>
                    <td width="100" align="right"><? echo number_format($total_p_cost,2); ?></td>
                    <td width="100" align="right" bgcolor="<? echo $color_pl; ?>"><? echo number_format($total_p_profit,2); ?></td>
                    <td width="100" align="right"><? echo number_format($total_p_profit_percentage2,2).'%'; ?></td>
                    <td width="100" align="right"><? echo number_format($p_expected_profit,2); ?></td>
                    <td width="100" align="right"><? echo number_format($company_asking,2); ?></td>
                    <td align="right" width="100"><? echo number_format($tot_expect_variance,2)?></td>
                    <td align="right" ><? $total_smv_price+=$smv_price;echo $smv_price; ?></td>

                   
                </tr>
                	<?
                    $dzn_qnty=0;
                    $costing_per_id=$fabriccostArray[$row[csf('job_no')]]['costing_per_id'];
                    if($costing_per_id==1) $dzn_qnty=12;
                    else if($costing_per_id==3) $dzn_qnty=12*2;
                    else if($costing_per_id==4) $dzn_qnty=12*3;
                    else if($costing_per_id==5) $dzn_qnty=12*4;
                    else $dzn_qnty=1;
					$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
					
                    $commercial_cost_dzn=$fabriccostArray[$row[csf('job_no')]]['comm_cost'];
                    $commercial_cost=($commercial_cost_dzn/$dzn_qnty)*$order_qty_pcs;
					if(is_infinite($commercial_cost) || is_nan($commercial_cost)){$commercial_cost=0;}
                    $fabricData=explode(",",substr($fabric_data_Array[$row[csf('job_no')]],0,-1));
                    $fab_precost_Data=explode(",",substr($fab_precost_arr[$row[csf('job_no')]][$row[csf('po_id')]],0,-1));
					$pre_fab_source_id=""; $pre_color_type_id=""; $yarn_costing=0; $avg_rate=0; $fab_purchase=0; $yarn_cost_percent=0;
                    foreach($fabricData as $fabricRow)
                    {
						$fabricRow=explode("**",$fabricRow);
						$fab_source_id=$fabricRow[1];
						if($pre_fab_source_id=="") $pre_fab_source_id=$fab_source_id; else $pre_fab_source_id.=','.$fab_source_id;
						
                    }
					
					$fab_purchase_knit=array_sum($fabric_costing_arr['knit']['grey'][$row[csf('po_id')]]);
					if(is_infinite($fab_purchase_knit) || is_nan($fab_purchase_knit)){$fab_purchase_knit=0;}
					$fab_purchase_woven=array_sum($fabric_costing_arr['woven']['grey'][$row[csf('po_id')]]);
					if(is_infinite($fab_purchase_woven) || is_nan($fab_purchase_woven)){$fab_purchase_woven=0;}
					$fab_purchase=$fab_purchase_knit+$fab_purchase_woven;

					$yarn_costing=$yarn_costing_arr[$row[csf('po_id')]];
					$yarn_cons=($yarn_req_qty_arr[$row[csf('po_id')]]/$plan_cut_qnty)*$dzn_qnty;
					if(is_infinite($yarn_cons) || is_nan($yarn_cons)){$yarn_cons=0;}
					$yarn_cost_percent=($yarn_costing/$order_value)*100;
					if(is_infinite($yarn_cost_percent) || is_nan($yarn_cost_percent)){$yarn_cost_percent=0;}
					$avg_rate=$yarn_costing/$yarn_req_qty_arr[$row[csf('po_id')]];
					if(is_infinite($avg_rate) || is_nan($avg_rate)){$avg_rate=0;}
					$knit_cost=0;
						foreach($knit_cost_arr as $process_id)
						{
							$knit_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$process_id]);	
						}
					
					$knit_cost_dzn=($knit_cost/$plan_cut_qnty)*12;
					if(is_infinite($knit_cost_dzn) || is_nan($knit_cost_dzn)){$knit_cost_dzn=0;}
					$fabric_dyeing_cost=0;
					foreach($fabric_dyeingCost_arr as $fab_process_id)
					{
						$fabric_dyeing_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$fab_process_id]);	
					}
					$fabric_dyeing_cost_dzn=($fabric_dyeing_cost/$plan_cut_qnty)*12;
					if(is_infinite($fabric_dyeing_cost_dzn) || is_nan($fabric_dyeing_cost_dzn)){$fabric_dyeing_cost_dzn=0;}
						
				
					$yarn_dyeing_cost=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][30]);
					$yarn_dyeing_cost_dzn=($yarn_dyeing_cost/$plan_cut_qnty)*12;
					if(is_infinite($yarn_dyeing_cost_dzn) || is_nan($yarn_dyeing_cost_dzn)){$yarn_dyeing_cost_dzn=0;}
					
					$fabric_finish=0;
					foreach($fab_finish_cost_arr as $fin_process_id)
					{
						$fabric_finish+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$fin_process_id]);	
					}
					
					$all_over_cost=0;
					foreach($aop_cost_arr as $aop_process_id)
					{
						$all_over_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$aop_process_id]);	
					}
					
					$washing_cost=0;
					foreach($washing_cost_arr as $w_process_id)
					{
						$washing_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$w_process_id]);	
					}
						

					$heat_setting_cost=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][33]);
                    
					$ex_pre_fab_source_id=implode(',',array_unique(explode(",",$pre_fab_source_id)));
					$ex_pre_color_type_id=array_unique(explode(",",$pre_color_type_id));
					$color_yarn_p=""; $color_purchase=""; $color_knit="";
					if($ex_pre_fab_source_id=='1,2'  || $ex_pre_fab_source_id=='2,1')
					{
						if($yarn_costing<=0) $color_yarn_p="red"; else $color_yarn_p="";
						if($fab_purchase<=0) $color_purchase="red"; else $color_purchase="";
						if($knit_cost<=0) $color_knit="red"; else $color_knit="";
					}
					else if($ex_pre_fab_source_id==1 && $ex_pre_fab_source_id==2 ) 
					{
						if($yarn_costing<=0) $color_yarn_p="red"; else $color_yarn_p="";
						if($fab_purchase<=0) $color_purchase="red"; else $color_purchase="";
						if($knit_cost<=0) $color_knit="red"; else $color_knit="";
					}
					else if($ex_pre_fab_source_id==1) 
					{
						if($yarn_costing<=0) $color_yarn_p="";
						if($fab_purchase<=0) $color_purchase="";
						if($knit_cost<=0) $color_knit="red"; else $color_knit="";
					}
					else if($ex_pre_fab_source_id==2) 
					{
						if($yarn_costing<=0) $color_yarn_p="red"; else $color_yarn_p="";
						if($fab_purchase<=0) $color_purchase="red"; else $color_purchase="";
						if($knit_cost<=0) $color_knit="red"; else $color_knit="";
					}
					else 
					{
						if($yarn_costing<=0) $color_yarn_p="red"; else $color_yarn_p="";
						if($fab_purchase<=0) $color_purchase="red"; else $color_purchase="";
						if($knit_cost<=0) $color_knit="red"; else $color_knit="";
					}
					//echo $ex_pre_fab_source_id;
					
					$color_fab_d="";
					foreach($ex_pre_color_type_id as $colorType_id)
					{
						if($colorType_id==2 || $colorType_id==3  || $colorType_id==4  || $colorType_id==6)
						{
							if($fabric_dyeing_cost<=0) if ($color_fab_d=="") $color_fab_d=""; else $color_fab_d.=','."";
						}
						else
						{
							if($fabric_dyeing_cost<=0)  if ($color_fab_d=="") $color_fab_d="red"; else $color_fab_d.=','."red"; else $color_fab_d="";
						}
					}
					
					$ex_pre_color_type=implode(',',array_unique(explode(",",$color_fab_d)));

                    if($fabric_finish<=0) $color_finish="red"; else $color_finish="";	
                    if($commercial_cost<=0) $color_com="red"; else $color_com="";	
					//$trim_qty_arr[$row[csf('po_id')]];//
					$trim_amount= $trims_costing_arr[$row[csf('po_id')]];
					if(is_infinite($trim_amount) || is_nan($trim_amount)){$trim_amount=0;}

					$print_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][1];
					if(is_infinite($print_amount) || is_nan($print_amount)){$print_amount=0;}
					$embroidery_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][2];
					if(is_infinite($embroidery_amount) || is_nan($embroidery_amount)){$embroidery_amount=0;}
					$special_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][4];
					if(is_infinite($special_amount) || is_nan($special_amount)){$special_amount=0;}
					$wash_amount=$emblishment_costing_arr_name_wash[$row[csf('po_id')]][3];
					if(is_infinite($wash_amount) || is_nan($wash_amount)){$wash_amount=0;}
					$other_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][5];
					if(is_infinite($other_amount) || is_nan($other_amount)){$other_amount=0;}
					$foreign=$commission_costing_arr[$row[csf('po_id')]][1];
					if(is_infinite($foreign) || is_nan($foreign)){$foreign=0;}
					$local=$commission_costing_arr[$row[csf('po_id')]][2];
					if(is_infinite($local) || is_nan($local)){$local=0;}
					$test_cost=$other_costing_arr[$row[csf('po_id')]]['lab_test'];
					if(is_infinite($test_cost) || is_nan($test_cost)){$test_cost=0;}
					$freight_cost=$other_costing_arr[$row[csf('po_id')]]['freight'];
					if(is_infinite($freight_cost) || is_nan($freight_cost)){$freight_cost=0;}
					$inspection=$other_costing_arr[$row[csf('po_id')]]['inspection'];
					if(is_infinite($inspection) || is_nan($inspection)){$inspection=0;}
					$certificate_cost=$other_costing_arr[$row[csf('po_id')]]['certificate_pre_cost'];
					if(is_infinite($certificate_cost) || is_nan($certificate_cost)){$certificate_cost=0;}
					$common_oh=$other_costing_arr[$row[csf('po_id')]]['common_oh'];
					if(is_infinite($common_oh) || is_nan($common_oh)){$common_oh=0;}
					$currier_cost=$other_costing_arr[$row[csf('po_id')]]['currier_pre_cost'];
					if(is_infinite($currier_cost) || is_nan($currier_cost)){$currier_cost=0;}
					
					$cm_cost=$other_costing_arr[$row[csf('po_id')]]['cm_cost'];
					if(is_infinite($cm_cost) || is_nan($cm_cost)){$cm_cost=0;}
					$cm_cost_dzn=($cm_cost/$order_qty_pcs);
					if(is_infinite($cm_cost_dzn) || is_nan($cm_cost_dzn)){$cm_cost_dzn=0;}
					
					//$yarn_dyeing_cost_dzn=($yarn_dyeing_cost/$plan_cut_qnty)*12;
                  
                    
                    $total_cost=   $fab_purchase  +  $trim_amount  +  $print_amount  +  $embroidery_amount  +  $special_amount  +  $wash_amount  +  $commercial_cost  +  $foreign  +  $local  +  $cm_cost;
                    
                    if($trim_amount<=0) $color_trim="red"; else $color_trim="";	
                    if($cm_cost<=0) $color="red"; else $color="";	
					
                    $total_profit=$order_value-$total_cost;
                    $total_profit_percentage=$total_profit/$order_value*100; 
                    if($total_profit_percentage<=0 ) $color_pl="red";
                    else if($total_profit_percentage>$max_profit) $color_pl="yellow";	
                    else if($total_profit_percentage<=$max_profit) $color_pl="green";	
                    else $color_pl="";	
                    
					$expected_profit=$company_asking*$order_value/100;
					$expect_variance=$total_profit-$expected_profit;
                   ?>
                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trp_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trp_<? echo $i; ?>">
                    <td width="100">Pre Cost</td>

                 

                    <td width="100" align="right" bgcolor="<? echo $color_purchase; ?>"><a href="##" onClick="generate_precost_fab_purchase_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $fab_source_id; ?>','fab_purchase_detail')"><? echo number_format($fab_purchase,2); ?></a></td>


                   

                    <td width="100" align="right" bgcolor="<? echo $color_trim; ?>"><a href="##" onClick="generate_precost_trim_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','trim_cost_detail')"><? echo number_format($trim_amount,2); ?></a><? //echo  number_format($trim_amount,2);?></td>
                    <td width="80" align="right"><a href="##" onClick="generate_precost_embell_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','print_cost_detail')"><? echo number_format($print_amount,2); ?></a></td>
                    <td width="85" align="right"><a href="##" onClick="generate_precost_embell_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','embroidery_cost_detail')"><? echo number_format($embroidery_amount,2); ?></a></td>
                    <td width="80" align="right"><? echo number_format($special_amount,2); ?></td>
                    <td width="80" align="right"><a href="##" onClick="generate_precost_embell_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','wash_cost_detail')"><? echo number_format($wash_amount,2); ?></a></td>

                      <? /* ?>
                    <td width="80" align="right"><? echo number_format($other_amount,2); ?></td>
                      <? */ ?>

                    <td width="120" align="right" bgcolor="<? echo $color_com; ?>"><? echo number_format($commercial_cost,2); ?></td>
                    <td width="120" align="right"><? echo number_format($foreign,2) ?></td>
                    <td width="120" align="right"><? echo number_format($local,2) ?></td>

                  

                    <td width="120" align="right"  title="<? echo 'CM Dzn=CM Cost/PO Qty Pcs';?>" ><? echo number_format($cm_cost_dzn,2);?></td>
                    <td width="100" align="right" bgcolor="<? echo $color; ?>"><? echo number_format($cm_cost,2);?></td>
                    <td width="100" align="right"><? echo number_format($total_cost,2); ?></td>
                    <td width="100" align="right" bgcolor="<? echo $color_pl; ?>" title="Order value - Total cost"><? echo number_format($total_profit,2); ?></td>
                    <td width="100" align="right" title="Total profit/Order value*100"><? echo number_format($total_profit_percentage,2).'%'; ?></td>
                    <td width="100" align="right" title="Company asking*Order value/100"><? echo number_format($expected_profit,2); ?></td>
                    <td width="100" align="right"><? echo number_format($company_asking,2); ?></td>
                    <td align="right"  width="100" title="Total profit-Expected profit"><? echo number_format($expect_variance,2)?></td>
                    <td  align="right"><? $total_smv_pre+=$row[csf('set_smv')];echo number_format($row[csf('set_smv')],2); ?></td>

                   
                </tr> 
                <?
                $avg_rate_variance=$p_avg_rate-$avg_rate;
				$yarn_variance=$p_yarn_costing-$yarn_costing;
				$yarn_cons_variance=$p_yarn_cons-$yarn_cons;
				$yarn_cost_percent_variance=$p_yarn_cost_percent-$yarn_cost_percent;
                $fab_purchase_variance=$p_fab_purchase-$fab_purchase;
                $knit_cost_dzn_variance=$p_knit_cost_dzn-$knit_cost_dzn;
                $knit_cost_variance=$p_knit_cost-$knit_cost;
                $yarn_dyeing_cost_dzn_vairance=$p_yarn_dyeing_cost_dzn-$yarn_dyeing_cost_dzn ;
                $yarn_dyeing_cost_variance=$p_yarn_dyeing_cost-$yarn_dyeing_cost;
                $fabric_dyeing_cost_dzn_variance=$p_fabric_dyeing_cost_dzn-$fabric_dyeing_cost_dzn;
                $fabric_dyeing_cost_variance=$p_fabric_dyeing_cost-$fabric_dyeing_cost;
                $heat_setting_cost_variance=$p_heat_setting_cost-$heat_setting_cost;
                $fabric_finish_variance_cost=$p_fabric_finish-$fabric_finish;
                $washing_cost_variance=$p_washing_cost-$washing_cost;
                $all_over_variance=$p_all_over_cost-$all_over_cost;
				
				if($trim_amount<=0) $color_trim="red"; else $color_trim="";	
				if($cm_cost<=0) $color="red"; else $color="";	
				
				$trim_cost_variance=$p_trim_amount-$trim_amount;
				$print_varinace=$p_print_amount-$print_amount;
				$embrodery_variance=$p_embroidery_amount-$embroidery_amount;
				$special_variance=$p_special_amount-$special_amount;
				$wash_amount_variance=$p_wash_amt-$wash_amount;
				$other_amount_variance=$p_other_amount-$other_amount;
				$commercial_variance=$p_commercial_cost-$commercial_cost;
				$foreign_variance=$p_foreign-$foreign;
				$local_variance=$p_local-$local;
				$test_variance=$p_test_cost-$test_cost;
				$feight_variance=$p_freight_cost-$freight_cost;
				$inspection_variance=$p_inspection-$inspection;
				$certificate_variance=$p_certificate_cost-$certificate_cost;
				$common_variance=$p_common_oh-$common_oh;
				$currier_variance=$p_currier_cost-$currier_cost;
				$cm_dzn_variance=$p_cm_cost_dzn-$cm_cost_dzn;
				$cm_variance=$p_cm_cost-$cm_cost;
				$total_cost_varaince=$total_p_cost-$total_cost;
				
				if($fabric_dyeing_cost_variance<=0) $color_fab_v="red"; else $color_fab_v="";
				if($yarn_dyeing_cost_variance<=0) $color_fab_dy_v="red"; else $color_fab_dy_v="";	
				if($yarn_variance<=0) $color_yarn_v="red"; else $color_yarn_v="";	
				if($knit_cost_variance<=0) $color_knit_v="red"; else $color_knit_v="";	
				if($fabric_finish<=0) $color_finish="red"; else $color_finish="";
				if($commercial_variance<=0) $color_com_v="red"; else $color_com_v="";	
				if($trim_cost_variance<=0) $color_trim_v="red"; else $color_trim_v="";
				if($cm_variance<=0) $color_cm_v="red"; else $color_cm_v="";
		
				$total_profit=$order_value-$total_cost;
				$total_profit_percentage=$total_profit/$order_value*100; 
				if($total_profit_percentage<=0 ) $color_pl="red";
				else if($total_profit_percentage>$max_profit) $color_pl="yellow";	
				else if($total_profit_percentage<=$max_profit) $color_pl="green";	
				else $color_pl="";	
				
				$tot_profit_variance=$total_profit-$total_p_profit;
				$tot_profit_percient_varaince=$total_profit_percentage-$total_p_profit_percentage2;
				$tot_expected_profit=$expected_profit-$p_expected_profit;
				$tot_expected_varaince_data_vairance=$expect_variance-$tot_expect_variance;
				
				
				?>
                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trv_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trv_<? echo $i; ?>">
                    <td width="100" >Variance</td>
                    <td width="100" align="right"><? echo number_format($fab_purchase_variance,2); ?></td>
                    <td width="100" align="right" bgcolor="<? //echo $color_trim_v; ?>"><? echo number_format($trim_cost_variance,2); ?></td>
                    <td width="80" align="right"><? echo number_format($print_varinace,2); ?></td>
                    <td width="85" align="right"><? echo number_format($embrodery_variance,2); ?></td>
                    <td width="80" align="right"><? echo number_format($special_variance,2); ?></td>
                    <td width="80" align="right"><? echo number_format($wash_amount_variance,2); ?></td>
                    <td width="120" align="right" bgcolor="<? //echo $color_com_v; ?>"><? echo number_format($commercial_variance,2); ?></td>
                    <td width="120" align="right"><? echo number_format($foreign_variance,2) ?></td>
                    <td width="120" align="right"><? echo number_format($local_variance,2) ?></td>
                    <td width="120" align="right"><? echo number_format($cm_dzn_variance,2);?></td>
                    <td width="100" align="right" bgcolor="<? //echo $color_cm_v; ?>"><? echo number_format($cm_variance,2);?></td>
                    <td width="100" align="right"><? echo number_format($total_cost_varaince,2); ?></td>
                    <td width="100" align="right" bgcolor="<? //echo $color_pl; ?>"><? echo number_format($tot_profit_variance,2); ?></td>
                    <td width="100" align="right"><p><? echo number_format($tot_profit_percient_varaince,2).'%'; ?></p></td>
                    <td width="100" align="right"><? //echo number_format($p_expected_profit,2); ?></td>
                    <td width="100" align="right"><? //echo number_format($company_asking,2); ?></td>
                    <td align="right"  width="100"><? echo number_format($tot_expected_varaince_data_vairance,2); ?></td>
                    <td align="right"  ><? $total_smv_vari+=$row[csf('set_smv')]-$smv_price;echo number_format($row[csf('set_smv')]-$smv_price,2); ?></td>
                </tr>
                <?
                    $percent_avg_rate=$avg_rate_variance/$p_avg_rate*100;
					if(is_infinite($percent_avg_rate) || is_nan($percent_avg_rate)){$percent_avg_rate=0;}
					$percent_yarn_cons=$yarn_cons_variance/$p_yarn_cons*100;
					if(is_infinite($percent_yarn_cons) || is_nan($percent_yarn_cons)){$percent_yarn_cons=0;}
					$percent_yarn_costing=$yarn_variance/$p_yarn_costing*100;
					if(is_infinite($percent_yarn_costing) || is_nan($percent_yarn_costing)){$percent_yarn_costing=0;}
					$percent_yarn_cost_percent=$yarn_cost_variance_percent/$p_yarn_cost_percent*100;
					if(is_infinite($percent_yarn_cost_percent) || is_nan($percent_yarn_cost_percent)){$percent_yarn_cost_percent=0;}
					$percent_fab_purchase=$fab_purchase_variance/$p_fab_purchase*100;
					if(is_infinite($percent_fab_purchase) || is_nan($percent_fab_purchase)){$percent_fab_purchase=0;}
					$percent_knit_cost_dzn=$knit_cost_dzn_variance/$p_knit_cost_dzn*100;
					if(is_infinite($percent_knit_cost_dzn) || is_nan($percent_knit_cost_dzn)){$percent_knit_cost_dzn=0;}
					$percent_knit_cost=$knit_cost_variance/$p_knit_cost*100;
					if(is_infinite($percent_knit_cost) || is_nan($percent_knit_cost)){$percent_knit_cost=0;}
					$percent_yarn_dyeing_cost_dzn=$p_yarn_dyeing_cost_dzn/$yarn_dyeing_cost_dzn*100;
					if(is_infinite($percent_yarn_dyeing_cost_dzn) || is_nan($percent_yarn_dyeing_cost_dzn)){$percent_yarn_dyeing_cost_dzn=0;}
					$percent_yarn_dyeing_cost=$p_yarn_dyeing_cost/$yarn_dyeing_cost*100;
					if(is_infinite($percent_yarn_dyeing_cost) || is_nan($percent_yarn_dyeing_cost)){$percent_yarn_dyeing_cost=0;}
					$percent_fabric_dyeing_dzn=$fabric_dyeing_dzn_variance/$p_fabric_dyeing_cost_dzn*100;
					if(is_infinite($percent_fabric_dyeing_dzn) || is_nan($percent_fabric_dyeing_dzn)){$percent_fabric_dyeing_dzn=0;}
					$percent_fabric_dyeing=$fabric_dyeing_cost_variance/$p_fabric_dyeing_cost*100;
					if(is_infinite($percent_fabric_dyeing) || is_nan($percent_fabric_dyeing)){$percent_fabric_dyeing=0;}
					$percent_heat_setting_cost=$heat_setting_cost_variance/$p_heat_setting_cost*100;
					if(is_infinite($percent_heat_setting_cost) || is_nan($percent_heat_setting_cost)){$percent_heat_setting_cost=0;}
					$percent_fabric_finish=$fabric_finish_variance_cost/$p_fabric_finish*100;
					if(is_infinite($percent_fabric_finish) || is_nan($percent_fabric_finish)){$percent_fabric_finish=0;}
					$percent_wash_cost=$wash_cost_variance/$p_washing_cost*100;
					if(is_infinite($percent_wash_cost) || is_nan($percent_wash_cost)){$percent_wash_cost=0;}
					$percent_all_over=$all_over_variance/$p_all_over_cost*100;
					if(is_infinite($percent_all_over) || is_nan($percent_all_over)){$percent_all_over=0;}
					
					$percent_trim_cost=$trim_cost_variance/$p_trim_amount*100;
					if(is_infinite($percent_trim_cost) || is_nan($percent_trim_cost)){$percent_trim_cost=0;}
					$percent_print=$print_varinace/$p_print_amount*100;
					if(is_infinite($percent_print) || is_nan($percent_print)){$percent_print=0;}
					$percent_embroidery_amount=$embrodery_variance/$p_embroidery_amount*100;
					if(is_infinite($percent_embroidery_amount) || is_nan($percent_embroidery_amount)){$percent_embroidery_amount=0;}
					$percent_special=$special_variance/$p_special_amount*100;
					if(is_infinite($percent_special) || is_nan($percent_special)){$percent_special=0;}
					$percent_wash_amount=$wash_amount_variance/$p_wash_amount*100;
					if(is_infinite($percent_wash_amount) || is_nan($percent_wash_amount)){$percent_wash_amount=0;}
					$percent_other_amount=$other_amount_variance/$p_other_amount*100;
					if(is_infinite($percent_other_amount) || is_nan($percent_other_amount)){$percent_other_amount=0;}
					$percent_commercial=$commercial_variance/$p_commercial_cost*100;
					if(is_infinite($percent_commercial) || is_nan($percent_commercial)){$percent_commercial=0;}
					$percent_foreign=$foreign_variance/$p_foreign*100;
					if(is_infinite($percent_foreign) || is_nan($percent_foreign)){$percent_foreign=0;}
					$percent_local=$local_variance/$p_local*100;
					if(is_infinite($percent_local) || is_nan($percent_local)){$percent_local=0;}
					$percent_test=$test_variance/$p_test_cost*100;
					if(is_infinite($percent_test) || is_nan($percent_test)){$percent_test=0;}
					$percent_feight=$feight_variance/$p_freight_cost*100;
					if(is_infinite($percent_feight) || is_nan($percent_feight)){$percent_feight=0;}
					$percent_inspection=$inspection_variance/$p_inspection*100;
					if(is_infinite($percent_inspection) || is_nan($percent_inspection)){$percent_inspection=0;}
					$percent_certificate=$certificate_variance/$p_certificate_cost*100;
					if(is_infinite($percent_certificate) || is_nan($percent_certificate)){$percent_certificate=0;}
					$percent_common_oh=$common_variance/$p_common_oh*100;
					if(is_infinite($percent_common_oh) || is_nan($percent_common_oh)){$percent_common_oh=0;}
					$percent_currier=$currier_variance/$p_currier_cost*100;
					if(is_infinite($percent_currier) || is_nan($percent_currier)){$percent_currier=0;}
					$percent_cm_dzn=$cm_dzn_variance/$p_cm_cost_dzn*100;
					if(is_infinite($percent_cm_dzn) || is_nan($percent_cm_dzn)){$percent_cm_dzn=0;}
					$percent_cm=$cm_variance/$p_cm_cost*100;
					if(is_infinite($percent_cm) || is_nan($percent_cm)){$percent_cm=0;}
					$percent_total_cost=$total_cost_varaince/$total_p_cost*100;
					if(is_infinite($percent_total_cost) || is_nan($percent_total_cost)){$percent_total_cost=0;}
					$percent_tot_profit=$tot_profit_variance/$total_p_profit*100;
					if(is_infinite($percent_tot_profit) || is_nan($percent_tot_profit)){$percent_tot_profit=0;}
					
					$percent_tot_profit_percient_varaince=$tot_profit_percient_varaince/$total_p_profit_percentage2*100;
					if(is_infinite($percent_tot_profit_percient_varaince) || is_nan($percent_tot_profit_percient_varaince)){$percent_tot_profit_percient_varaince=0;}
                    $total_cost_vari_percent_amount=$total_p_cost/$total_cost*100;
					if(is_infinite($total_cost_vari_percent_amount) || is_nan($total_cost_vari_percent_amount)){$total_cost_vari_percent_amount=0;}
                    $total_profit_vari_percnt_amount=$total_order_value-$total_cost_vari_percent_amount;
                    $total_profit_percentage2=$total_profit/$total_order_value*100;
					if(is_infinite($total_profit_percentage2) || is_nan($total_profit_percentage2)){$total_profit_percentage2=0;} 
                    if($total_profit_percentage2<=0 ) $color_pl="red";
                    else if($total_profit_percentage2>$max_profit) $color_pl="yellow";	
                    else if($total_profit_percentage2<=$max_profit) $color_pl="green";	
                    else $color_pl="";	
                ?>
                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trvp_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trvp_<? echo $i; ?>">
                    <td width="100">Variance %</td>
                    <td width="100" align="right"><? echo number_format($percent_fab_purchase,2).'%'; ?></td>
                    <td width="100" align="right"><? echo number_format($percent_trim_cost,2).'%'; ?></td>
                    <td width="80" align="right"><? echo number_format($percent_print,2).'%'; ?></td>
                    <td width="85" align="right"><? echo number_format($percent_embroidery_amount,2).'%'; ?></td>
                    <td width="80" align="right"><? echo number_format($percent_special,2).'%'; ?></td>
                    <td width="80" align="right"><? echo number_format($percent_wash_amount,2).'%'; ?></td>

                    <td width="120" align="right"><? echo number_format($percent_commercial,2).'%'; ?></td>
                    <td width="120" align="right"><? echo number_format($percent_foreign,2).'%'; ?></td>
                    <td width="120" align="right"><? echo number_format($percent_local,2).'%'; ?></td>

                    <td width="120" align="right"><? echo number_format($percent_cm_dzn,2).'%';?></td>
                    <td width="100" align="right"><? echo number_format($percent_cm,2).'%';?></td>
                    <td width="100" align="right"><? echo number_format($percent_total_cost,2).'%'; ?></td>
                    
                    <td width="100" align="right"><? echo number_format($percent_tot_profit,2).'%'; ?></td>
                    <td width="100" align="right"><? echo number_format($percent_tot_profit_percient_varaince,2).'%'; ?></td>
                    <td width="100" align="right"><? //echo number_format($p_expected_profit,2); ?></td>
                    <td width="100" align="right"><? //echo number_format($company_asking,2); ?></td>
                    <td align="right" width="100"><? $expect_variance=$total_profit-$expected_profit; echo '-'; ?></td>
                     <td align="right" ><? 
					 	$smv_price=number_format($smv_price,'.','',4);
					  echo (($row[csf('set_smv')]-$smv_price)/$smv_price*100); ?></td>

                </tr>
				<?
                $total_order_amount+=$order_value;
                $total_plancut_amount+=$plancut_value;
				
				//price_total
				$p_total_order_qty+=$order_qty_pcs;
				$p_total_order_value+=$order_value;
				$p_total_yarn_costing+=$p_yarn_costing;
				$p_total_yarn_cons+=$p_yarn_cons;
				$p_total_fab_purchase+=$p_fab_purchase;
				$p_total_knit_cost+=$p_knit_cost;
				$p_total_yarn_dyeing_cost+=$p_yarn_dyeing_cost;
				$p_total_fabric_dyeing_cost+=$p_fabric_dyeing_cost;
				$p_total_heat_setting_cost+=$p_heat_setting_cost;
				$p_total_fabric_finish+=$p_fabric_finish;
				$p_total_washing_cost+=$p_washing_cost;
				$p_total_all_over_cost+=$p_all_over_cost;
				$p_total_trim_amount+=$p_trim_amount;
				$p_total_print_amount+=$p_print_amount;
				$p_total_embroidery_amount+=$p_embroidery_amount;
				$p_total_special_amount+=$p_special_amount;
				$p_total_wash_amount+=$p_wash_amount;
				$p_total_other_amount+=$p_other_amount;
				$p_total_commercial_cost+=$p_commercial_cost;
				$p_total_foreign+=$p_foreign;
				$p_total_local+=$p_local;
				$p_total_test_cost+=$p_test_cost;
				$p_total_freight_cost+=$p_freight_cost;
				$p_total_wash_amt+=$p_wash_amt;
				$p_total_inspection+=$p_inspection;
				$p_total_certificate_cost+=$p_certificate_cost;
				$p_total_common_oh+=$p_common_oh;
				$p_total_currier_cost+=$p_currier_cost;
				$p_total_cm_cost+=$p_cm_cost;
				$p_total_p_cost+=$total_p_cost;
				$p_total_p_profit+=$total_p_profit;
				$p_total_profit_percentage2+=$total_p_profit_percentage2;
				$p_total_expected_profit+=$p_expected_profit;
				$p_total_expect_variance+=$tot_expect_variance;
				//pre_cost
				
				//pre_cost
				$total_order_qty+=$order_qty_pcs;
				$total_order_value+=$order_value;
				$total_yarn_costing+=$yarn_costing;
				$total_yarn_cons+=$yarn_cons;
				$total_fab_purchase+=$fab_purchase;
				$total_knit_cost+=$knit_cost;
				$total_yarn_dyeing_cost+=$yarn_dyeing_cost;
				$total_fabric_dyeing_cost+=$fabric_dyeing_cost;
				$total_heat_setting_cost+=$heat_setting_cost;
				$total_fabric_finish+=$fabric_finish;
				$total_washing_cost+=$washing_cost;
				$total_all_over_cost+=$all_over_cost;
				$total_trim_amount+=$trim_amount;
				$total_print_amount+=$print_amount;
				$total_embroidery_amount+=$embroidery_amount;
				$total_special_amount+=$special_amount;
				$total_wash_amt+=$wash_amount;
				$total_other_amount+=$other_amount;
				$total_commercial_cost+=$commercial_cost;
				$total_foreign+=$foreign;
				$total_local+=$local;
				$total_test_cost+=$test_cost;
				$total_freight_cost+=$freight_cost;
				$total_wash_amount+=$wash_amount;
				$total_inspection+=$inspection;
				$total_certificate_cost+=$certificate_cost;
				$total_common_oh+=$common_oh;
				$total_currier_cost+=$currier_cost;
				$total_cm_cost+=$cm_cost;
				$total_pre_cost+=$total_cost;
				$total_pre_profit+=$total_profit;
				$total_pre_profit_percentage2+=$total_profit_percentage2;
				$total_pre_expected_profit+=$expected_profit;
				$total_pre_expect_variance+=$expect_variance;
				//variance_total
				$v_total_order_qty+=$order_qty_pcs;
				$v_total_order_value+=$order_value;
				$v_total_yarn_costing+=$yarn_variance;
				$v_total_yarn_cons+=$yarn_cons_variance;
				$v_total_fab_purchase+=$fab_purchase_variance;
				$v_total_knit_cost+=$knit_cost_variance;
				$v_total_yarn_dyeing_cost+=$yarn_dyeing_cost_variance;
				$v_total_fabric_dyeing_cost+=$fabric_dyeing_cost_variance;
				$v_total_heat_setting_cost+=$heat_setting_cost_variance;
				$v_total_fabric_finish+=$fabric_finish_variance_cost;
				$v_total_washing_cost+=$washing_cost_variance;
				$v_total_all_over_cost+=$all_over_variance;
				$v_total_trim_amount+=$trim_cost_variance;
				$v_total_print_amount+=$print_varinace;
				$v_total_embroidery_amount+=$embrodery_variance;
				$v_total_special_amount+=$special_variance;
				$v_total_wash_amount+=$wash_amount_variance;
				$v_total_other_amount+=$other_amount_variance;
				$v_total_commercial_cost+=$commercial_variance;
				$v_total_foreign+=$foreign_variance;
				$v_total_local+=$local_variance;
				$v_total_test_cost+=$test_variance;
				$v_total_freight_cost+=$feight_variance;
				$v_total_wash_amount+=$p_wash_amount;
				$v_total_inspection+=$inspection_variance;
				$v_total_certificate_cost+=$certificate_variance;
				$v_total_common_oh+=$common_variance;
				$v_total_currier_cost+=$currier_variance;
				$v_total_cm_cost+=$cm_variance;
				$v_total_p_cost+=$total_cost_varaince;
				$v_total_p_profit+=$tot_profit_variance;
				$v_total_profit_percentage2+=$tot_profit_percient_varaince;
				$v_total_expected_profit+=$tot_expected_profit;
				$v_total_expect_variance+=$tot_expected_varaince_data_vairance;
				//pre_cost
				$i++;
			}
			$p_yarn_cost_percentage=$p_total_yarn_costing/$p_total_order_value*100;
			if(is_infinite($p_yarn_cost_percentage) || is_nan($p_yarn_cost_percentage)){$p_yarn_cost_percentage=0;}
			?>
            </table>
            </div>
            <table class="tbl_bottom" width="3390" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
                <tr>
                    <td width="40"></td>
                    <td width="70"></td>
                    <td width="70"></td>
                    <td width="100"></td>
                     <td width="100"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="100"></td>
                    <td width="110"></td>
                    <td width="110"></td>
                    <td width="110"></td>
                    <td width="110"></td>
                    <td width="70">Price Quote</td>
                    <td width="90" align="right" id="total_order_qnty"><? echo number_format($p_total_order_qty,2); ?></td>
                    <td width="90"></td>
                    <td width="100" align="right" id="p_total_order_value"><? echo number_format($p_total_order_value,2); ?></td>
                    <td width="100"></td>
                    <td width="100" align="right" id="p_total_fab_purchase"><? echo number_format($p_total_fab_purchase,2); ?></td>
                    <td width="100" align="right" id="p_trim_amount_td"><? echo number_format($p_total_trim_amount,2); ?></td>
                    <td width="80" align="right" id="p_print_amount_td"><? echo number_format($p_total_print_amount,2); ?></td>
                    <td width="85" align="right" id="p_embroidery_amount_td"><? echo number_format($p_total_embroidery_amount,2); ?></td>
                    <td width="80" align="right" id="p_special_amount_td"><? echo number_format($p_total_special_amount,2); ?></td>
                    <td width="80" align="right" id="p_wash_amt_td"><? echo number_format($p_total_wash_amt,2); ?></td>

                    <td width="120" align="right" id="p_commercial_cost_td"><? echo number_format($p_total_commercial_cost,2); ?></td>
                    <td width="120" align="right" id="p_foreign_td"><? echo number_format($p_total_foreign,2); ?></td>
                    <td width="120" align="right" id="p_local_td"><? echo number_format($p_total_local,2); ?></td>

                    <td width="120"></td>
                    <td width="100" align="right" id="p_cm_cost_td"><? echo number_format($p_total_cm_cost,2); ?></td>
                    <td width="100" align="right" id="p_tot_cost_td"><? echo number_format($p_total_p_cost,2); ?></td>
                    <td width="100" align="right" id="p_profitt_td"><? echo number_format($p_total_p_profit,2);?></td>
                    <td width="100" align="right"><? echo number_format($p_total_profit_percentage2,2);?></td>
                    <td width="100" align="right" id="p_expProfitt_td"><? echo number_format($p_total_expected_profit,2);?></td>
                    <td width="100" align="right"><? //echo number_format($p_total_expected_profit,2);?></td>
                    <td align="right" width="100" id="p_expect_variance_td"><? echo number_format($p_total_expect_variance,2);?></td>
                    <td align="right"  id="p_expect_variance_td2"><? echo number_format($total_smv_price,2);?></td>
                </tr>
                <tr>
                    <td width="40"></td>
                    <td width="70"></td>
                    <td width="70"></td>
                    <td width="100"></td>
                    <td width="100"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="100"></td>
                    <td width="110"></td>
                    <td width="110"></td>
                    <td width="110"></td>
                    <td width="110"></td>
                    <td width="70">Pre Cost</td>
                    <td width="90" align="right" id="total_order_qnty"><? echo number_format($total_order_qty,2); ?></td>
                    <td width="90"></td>
                    <td width="100" align="right" id="total_order_amount2"><? echo number_format($total_order_value,2); ?></td>
                    <td width="100"></td>
                    <td width="100" align="right" id="total_fab_purchase"><? echo number_format($total_fab_purchase,2); ?></td>

                    <td width="100" align="right" id="trim_amount_td"><? echo number_format($total_trim_amount,2); ?></td>
                    <td width="80" align="right" id="print_amount_td"><? echo number_format($total_print_amount,2); ?></td>
                    <td width="85" align="right" id="embroidery_amount_td"><? echo number_format($total_embroidery_amount,2); ?></td>
                    <td width="80" align="right" id="special_amount_td"> <? echo number_format($total_special_amount,2); ?></td>
                    <td width="80" align="right" id="wash_amt_td"> <? echo number_format($total_wash_amt,2); ?></td>

                    <td width="120" align="right" id="commercial_cost_td"><? echo number_format($total_commercial_cost,2); ?></td>
                    <td width="120" align="right" id="foreign_td"><? echo number_format($total_foreign,2); ?></td>
                    <td width="120" align="right" id="local_td"><? echo number_format($total_local,2); ?></td>
                    <td width="120"></td>
                    <td width="100" align="right" id="cm_cost_td"><? echo number_format($total_cm_cost,2); ?></td>
                    <td width="100" align="right" id="tot_cost_td"><? echo number_format($total_pre_cost,2); ?></td>
                    <td width="100" align="right" id="profitt_td"><? echo number_format($total_pre_profit,2);?></td>
                    <td width="100" align="right"><?  echo number_format($total_pre_profit_percentage2,2);?></td>
                    <td width="100" align="right" id="expProfitt_td"><? echo number_format($total_pre_expected_profit,2);?></td>
                    <td width="100" align="right"><? //echo number_format($total_pre_expected_profit,2);?></td>
                    <td align="right" width="100"  id="expect_variance_td"><? echo number_format($total_pre_expect_variance,2);?></td>
                    <td align="right"   id="expect_variance_td2"><? echo number_format($total_smv_pre,2);?></td>
                </tr>
                <tr>
                    <td width="40"></td>
                    <td width="70"></td>
                    <td width="70"></td>
                    <td width="100"></td>
                    <td width="100"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="100"></td>
                    <td width="110"></td>
                    <td width="110"></td>
                    <td width="110"></td>
                    <td width="110"></td>
                    <td width="70">Variance</td>
                    <td width="90" align="right"><? //echo number_format($v_total_order_qty,2); ?></td>
                    <td width="90"></td>
                    <td width="100" align="right"><? //echo number_format($v_total_order_value,2); ?></td>
                    <td width="100"></td>
                    <td width="100" align="right" id="v_total_fab_purchase"><? echo number_format($v_total_fab_purchase,2); ?></td>
                    <td width="100" align="right" id="v_trim_amount_td"><? echo number_format($v_total_trim_amount,2); ?></td>
                    <td width="80" align="right" id="v_print_amount_td"><? echo number_format($v_total_print_amount,2); ?></td>
                    <td width="85" align="right" id="v_embroidery_amount_td"><? echo number_format($v_total_embroidery_amount,2); ?></td>
                    <td width="80" align="right" id="v_special_amount_td"><? echo number_format($v_total_special_amount,2); ?></td>
                    <td width="80" align="right" id="v_wash_amt_td"><? echo number_format($v_total_wash_amount,2); ?></td>
                    <td width="120" align="right" id="v_commercial_cost_td"><? echo number_format($v_total_commercial_cost,2); ?></td>
                    <td width="120" align="right" id="v_foreign_td"><? echo number_format($v_total_foreign,2); ?></td>
                    <td width="120" align="right" id="v_local_td"><? echo number_format($v_total_local,2); ?></td>
                    <td width="120"></td>
                    <td width="100" align="right" id="v_cm_cost_td"><? echo number_format($v_total_cm_cost,2); ?></td>
                    <td width="100" align="right" id="v_tot_cost_td"><? echo number_format($v_total_p_cost,2); ?></td>
                    <td width="100" align="right" id="v_profitt_td"><? echo number_format($v_total_p_profit,2);?></td>
                    <td width="100" align="right"><?  //echo number_format($v_total_pre_profit_percentage2,2);?></td>
                    <td width="100" align="right" id="v_expProfitt_td"><? //echo number_format($v_total_pre_expected_profit,2);?></td>
                    <td width="100" align="right"><? //echo number_format($v_total_pre_expected_profit,2);?></td>
                    <td align="right" width="100"  id="v_expect_variance_td"><? echo number_format($v_total_expect_variance,2);?></td>
                    <td   align="right"><? echo number_format($total_smv_vari,2);?></td>

                      <? /* ?>
                     <td width=""  align="right"><? echo number_format($v_total_yarn_cons,2);?></td>
					 <? */ ?>

                </tr>
            </table>
            </fieldset>
            </div>
           <?
		}
	}
	else if($report_type==15) //ShipOut not use
	{
		if($template==1)
		{
			
			$style1="#E9F3FF"; 
			$style="#FFFFFF";
				
			$fab_precost_arr=array();$commission_array=array();$knit_arr=array(); $fabriccostArray=array(); $fab_emb=array();$fabric_data_Array=array();$asking_profit_arr=array(); $yarncostArray=array(); $convirsioncostArray=array(); $yarn_desc_array=array(); $actual_shipout_arr=array();
			
			$yarncostDataArray=sql_select("select job_no, sum(cons_qnty) as cons_qnty, sum(amount) as amount from wo_pre_cost_fab_yarn_cost_dtls where status_active=1 and is_deleted=0 group by job_no");
			foreach($yarncostDataArray as $yarnRow)
			{
			   $yarncostArray[$yarnRow[csf('job_no')]].=$yarnRow[csf('cons_qnty')]."**".$yarnRow[csf('amount')].",";
			}
			
			$convirsioncostDataArray=sql_select("select job_no, sum(req_qnty) as req_qnty, sum(amount) as amount from wo_pre_cost_fab_conv_cost_dtls where status_active=1 and is_deleted=0 group by job_no");
			foreach($convirsioncostDataArray as $conRow)
			{
			   $convirsioncostArray[$conRow[csf('job_no')]].=$conRow[csf('req_qnty')]."**".$conRow[csf('amount')].",";
			}
			//var_dump($convirsioncostArray);die;
			
			$asking_profit=sql_select("select id,company_id,asking_profit,max_profit from lib_standard_cm_entry where status_active=1 and is_deleted=0 $date_max_profit");
			foreach($asking_profit as $ask_row )
			{
				$asking_profit_arr[$ask_row[csf('company_id')]]['asking_profit']=$ask_row[csf('asking_profit')];
				$asking_profit_arr[$ask_row[csf('company_id')]]['max_profit']=$ask_row[csf('max_profit')];
			} //var_dump($asking_profit_arr);
			
			$fab_arr=sql_select("select a.job_no,a.pre_cost_fabric_cost_dtls_id, a.po_break_down_id, sum(a.requirment) as requirment ,sum(a.pcs) as pcs from wo_pre_cos_fab_co_avg_con_dtls a,wo_pre_cost_fabric_cost_dtls b where a.pre_cost_fabric_cost_dtls_id=b.id and a.job_no=b.job_no  and b.status_active=1 and b.is_deleted=0 group by a.po_break_down_id,a.pre_cost_fabric_cost_dtls_id,a.job_no");
			foreach($fab_arr as $row_pre)
			{
				$fab_precost_arr[$row_pre[csf('job_no')]][$row_pre[csf('po_break_down_id')]].=$row_pre[csf('requirment')]."**".$row_pre[csf('pcs')].",";	
			}
			
			$fabricDataArray=sql_select("select a.job_no, a.fab_nature_id, a.fabric_source, a.rate, b.yarn_cons_qnty, b.yarn_amount, b.conv_req_qnty, b.conv_amount from wo_pre_cost_fabric_cost_dtls a, wo_pre_cost_sum_dtls b where a.job_no=b.job_no and a.fabric_source!=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			foreach($fabricDataArray as $fabricRow)
			{
				$fabric_data_Array[$fabricRow[csf('job_no')]].=$fabricRow[csf('fab_nature_id')]."**".$fabricRow[csf('fabric_source')]."**".$fabricRow[csf('rate')]."**".$fabricRow[csf('yarn_cons_qnty')]."**".$fabricRow[csf('yarn_amount')]."**".$fabricRow[csf('conv_req_qnty')]."**".$fabricRow[csf('conv_amount')].",";
			}//Pre cost end
		//var_dump($fabric_data_Array); die;
			$data_array_emb=("select  job_no,
			sum(CASE WHEN emb_name=1 THEN amount END) AS print_amount,
			sum(CASE WHEN emb_name=2 THEN amount END) AS embroidery_amount,
			sum(CASE WHEN emb_name=3 THEN amount END) AS wash_amount,
			sum(CASE WHEN emb_name=4 THEN amount END) AS special_amount,
			sum(CASE WHEN emb_name=5 THEN amount END) AS other_amount
			from  wo_pre_cost_embe_cost_dtls where  status_active=1 and  is_deleted=0  group by job_no");
			$embl_array=sql_select($data_array_emb);
			foreach($embl_array as $row_emb)
			{
				$fab_emb[$row_emb[csf('job_no')]]['print']=$row_emb[csf('print_amount')];
				$fab_emb[$row_emb[csf('job_no')]]['embroidery']=$row_emb[csf('embroidery_amount')];
				$fab_emb[$row_emb[csf('job_no')]]['special']=$row_emb[csf('special_amount')];
				$fab_emb[$row_emb[csf('job_no')]]['other']=$row_emb[csf('other_amount')];
				$fab_emb[$row_emb[csf('job_no')]]['wash']=$row_emb[csf('wash_amount')];
			}
			//Trim cost from BOM calculation
			$trim_qty_arr_data=array();$trim_po_country_arr=array();$trim_poqty_country_arr=array();
			//if($db_type==2) $county_group="LISTAGG(CAST(country_id AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY country_id) as country_id,LISTAGG(CAST(po_break_down_id AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY po_break_down_id) as po_break_down_id";
			//else if($db_type==0) $county_group="group_concat(country_id) as country_id,group_concat(po_break_down_id) as po_break_down_id";
			//and job_no='FAL-15-00570'
			 $dtls_data=sql_select("select po_break_down_id,country_id, wo_pre_cost_trim_cost_dtls_id as wo_pre_dtls_id from wo_pre_cost_trim_co_cons_dtls where  cons !=0  ");
			foreach($dtls_data as $row)
			{
				 $trim_po_country_arr[$row[csf('wo_pre_dtls_id')]].=$row[csf('po_break_down_id')].'**'.$row[csf('country_id')].',';
			}
			$sql_poqty=sql_select("select b.id,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty");
			foreach($sql_poqty as $data_po)
			{
				$trim_poqty_country_arr[$data_po[csf('id')]]=$data_po[csf('order_quantity_set')];
			}
			
			$sql_trim = "select id, job_no, trim_group,description,brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,status_active
			from wo_pre_cost_trim_cost_dtls where status_active=1 ";//where job_no=".$$all_job_id."
			//echo $all_job_id;die;
		
			$costing_per_arr=return_library_array( "select job_no,costing_per from wo_pre_cost_mst ", "job_no", "costing_per");
			
			$data_array_trim=sql_select($sql_trim);
		
            foreach( $data_array_trim as $row_trim )
            { 
			   $trim_data=explode(",",$trim_po_country_arr[$row_trim[csf('id')]]);
			   foreach($trim_data as $val)
			   { 
			   	 $exp_data=explode("**",$val);
				 $po_id=$exp_data[0];
				 $country_id=$exp_data[1];
				$po_qty=$trim_poqty_country_arr[$po_id];
			
				$trim_qty_arr_data[$po_id]+=$row_trim[csf('amount')];
			//	$trim_qty_arr_data[$po_id]+=$row_trim[csf('amount')];
					 
			   }
			
			} //Trims End
			// Converstion cost like Pre cost
			 
			
					
					
					$gmtsitem_ratio_array=array();
					$gmtsitem_ratio_sql=sql_select("select b.job_no,b.gmts_item_id,b.set_item_ratio from wo_po_details_mas_set_details b,wo_po_details_master a where a.job_no=b.job_no ");// where job_no ='FAL-14-01157'
					foreach($gmtsitem_ratio_sql as $gmtsitem_ratio_sql_row)
					{
					$gmtsitem_ratio_array[$gmtsitem_ratio_sql_row[csf('job_no')]][$gmtsitem_ratio_sql_row[csf('gmts_item_id')]]=$gmtsitem_ratio_sql_row[csf('set_item_ratio')];	
					}
					$costing_per_arr=return_library_array( "select job_no,costing_per from wo_pre_cost_mst ", "job_no", "costing_per");
					$conv_data=array();
					$sql_conv="select a.job_no,b.id,c.item_number_id,c.country_id,c.color_number_id,c.size_number_id,c.order_quantity ,c.plan_cut_qnty ,d.id as pre_cost_dtls_id,d.fab_nature_id,e.cons,f.req_qnty,f.charge_unit,f.amount,f.color_break_down   from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_conv_cost_dtls f where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and a.job_no=f.job_no and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_description  and e.cons !=0   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $date_cond $buyer_id_cond  $year_cond $job_no_cond $order_id_cond_trans $order_no_cond $order_status_cond $season_cond 
					UNION ALL
					select a.job_no,b.id,c.item_number_id,c.country_id,c.color_number_id,c.size_number_id,c.order_quantity ,c.plan_cut_qnty ,d.id as pre_cost_dtls_id,d.fab_nature_id,e.cons,f.req_qnty,f.charge_unit,f.amount,f.color_break_down   from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_conv_cost_dtls f where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and a.job_no=f.job_no and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and f.fabric_description=0  and e.cons !=0  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $date_cond $buyer_id_cond  $year_cond $job_no_cond $order_id_cond_trans $order_no_cond $order_status_cond $season_cond ";
					$data_arr_conv=sql_select($sql_conv);
					foreach($data_arr_conv as $conv_row)
					{
						$costing_per_qty=0;
						$costing_per=$costing_per_arr[$conv_row[csf('job_no')]];
						if($costing_per==1)
						{
						$costing_per_qty=12	;
						}
						if($costing_per==2)
						{
						$costing_per_qty=1;	
						}
						if($costing_per==3)
						{
						$costing_per_qty=24	;
						}
						if($costing_per==4)
						{
						$costing_per_qty=36	;
						}
						if($costing_per==5)
						{
						$costing_per_qty=48	;
						}
						
						$set_item_ratio=$gmtsitem_ratio_array[$conv_row[csf('job_no')]][$conv_row[csf('item_number_id')]];
						$convcolorrate=array();
						if($conv_row[csf('color_break_down')] !="")
						{
							$arr_1=explode("__",$conv_row[csf('color_break_down')]);
							for($ci=0;$ci<count($arr_1);$ci++)
							{
							$arr_2=explode("_",$arr_1[$ci]);
							$convcolorrate[$arr_2[0]]=$arr_2[1];
								
							}
						}
						//print_r($convcolorrate);
						//echo "<br/>";
						$convrate=0;
						$convqnty =def_number_format(($conv_row[csf("plan_cut_qnty")]/($costing_per_qty*$set_item_ratio))*$conv_row[csf("req_qnty")],5,"");
						
						$convrate=$conv_row[csf('charge_unit')];
						$convamount=def_number_format($convqnty*$convrate,5,"");
						$conv_data[$conv_row[csf('id')]]['conv_amount']+=$convamount;
					
					}// Conversion end
					
			$fabriccostDataArray=sql_select("select job_no, costing_per_id, trims_cost, embel_cost, cm_cost, commission, common_oh, lab_test, inspection, freight, comm_cost, certificate_pre_cost, currier_pre_cost, depr_amor_pre_cost from wo_pre_cost_dtls where status_active=1 and is_deleted=0  ");
			foreach($fabriccostDataArray as $fabRow)
			{
				$fabriccostArray[$fabRow[csf('job_no')]]['costing_per_id']=$fabRow[csf('costing_per_id')];
				$fabriccostArray[$fabRow[csf('job_no')]]['trims_cost']=$fabRow[csf('trims_cost')];
				$fabriccostArray[$fabRow[csf('job_no')]]['embel_cost']=$fabRow[csf('embel_cost')];
				$fabriccostArray[$fabRow[csf('job_no')]]['cm_cost']=$fabRow[csf('cm_cost')];
				$fabriccostArray[$fabRow[csf('job_no')]]['commission']=$fabRow[csf('commission')];
				$fabriccostArray[$fabRow[csf('job_no')]]['common_oh']=$fabRow[csf('common_oh')];
				$fabriccostArray[$fabRow[csf('job_no')]]['lab_test']=$fabRow[csf('lab_test')];
				$fabriccostArray[$fabRow[csf('job_no')]]['inspection']=$fabRow[csf('inspection')];
				$fabriccostArray[$fabRow[csf('job_no')]]['freight']=$fabRow[csf('freight')];
				$fabriccostArray[$fabRow[csf('job_no')]]['comm_cost']=$fabRow[csf('comm_cost')];
				$fabriccostArray[$fabRow[csf('job_no')]]['certificate_pre_cost']=$fabRow[csf('certificate_pre_cost')];
				$fabriccostArray[$fabRow[csf('job_no')]]['currier_pre_cost']=$fabRow[csf('currier_pre_cost')];
				$fabriccostArray[$fabRow[csf('job_no')]]['c_cost']=$fabRow[csf('cm_cost')];
				$fabriccostArray[$fabRow[csf('job_no')]]['depr_amor_cost']=$fabRow[csf('depr_amor_pre_cost')];
			} 
			
			$knit_data=sql_select("select job_no,
			sum(CASE WHEN cons_process=1 THEN amount END) AS knit_charge,
			sum(CASE WHEN cons_process=2 THEN amount END) AS weaving_charge,
			sum(CASE WHEN cons_process=3 THEN amount END) AS knit_charge_collar_cuff,
			sum(CASE WHEN cons_process=4 THEN amount END) AS knit_charge_feeder_stripe,
			sum(CASE WHEN cons_process in(64,82,89) THEN amount END) AS washing_cost,
			sum(CASE WHEN cons_process in(35,36,37) THEN amount END) AS all_over_cost,
			sum(CASE WHEN cons_process=30 THEN amount END) AS yarn_dyeing_cost,
			sum(CASE WHEN cons_process=33 THEN amount END) AS heat_setting_cost,
			sum(CASE WHEN cons_process in(25,31,32,60,61,62,63,72,80,81,84,85,86,87,38,74,78,79) THEN amount END) AS fabric_dyeing_cost,
			sum(CASE WHEN cons_process in(34,65,66,67,68,69,70,71,73,75,76,77,88,90,91,92,93,100,125,127,128,129) THEN amount END) AS fabric_finish_cost
			from wo_pre_cost_fab_conv_cost_dtls where  status_active=1 and is_deleted=0 group by job_no");
			foreach($knit_data as $row_knit)
			{
				$knit_arr[$row_knit[csf('job_no')]]['knit']=$row_knit[csf('knit_charge')];
				$knit_arr[$row_knit[csf('job_no')]]['weaving']=$row_knit[csf('weaving_charge')];
				$knit_arr[$row_knit[csf('job_no')]]['collar_cuff']=$row_knit[csf('knit_charge_collar_cuff')];
				$knit_arr[$row_knit[csf('job_no')]]['feeder_stripe']=$row_knit[csf('knit_charge_feeder_stripe')];
				$knit_arr[$row_knit[csf('job_no')]]['washing']=$row_knit[csf('washing_cost')];
				$knit_arr[$row_knit[csf('job_no')]]['all_over']=$row_knit[csf('all_over_cost')];
				$knit_arr[$row_knit[csf('job_no')]]['fabric_dyeing']=$row_knit[csf('fabric_dyeing_cost')];
				$knit_arr[$row_knit[csf('job_no')]]['yarn_dyeing']=$row_knit[csf('yarn_dyeing_cost')];	
				$knit_arr[$row_knit[csf('job_no')]]['heat']=$row_knit[csf('heat_setting_cost')];
				$knit_arr[$row_knit[csf('job_no')]]['fabric_finish']=$row_knit[csf('fabric_finish_cost')];	
			}
			
			$data_array=sql_select("select  job_no,
			sum(CASE WHEN particulars_id=1 THEN commission_amount END) AS foreign_comm,
			sum(CASE WHEN particulars_id=2 THEN commission_amount END) AS local_comm
			from  wo_pre_cost_commiss_cost_dtls where status_active=1 and is_deleted=0 group by job_no");// quotation_id='$data'
			foreach($data_array as $row_fl )
			{
				$commission_array[$row_fl[csf('job_no')]]['foreign']=$row_fl[csf('foreign_comm')];
				$commission_array[$row_fl[csf('job_no')]]['local']=$row_fl[csf('local_comm')];
			}
			$act_ahip_arr=sql_select("select po_break_down_id,
			sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as shipout_qty,
			sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as ret_shipout_qty
	 from pro_ex_factory_mst  where  status_active=1 and is_deleted=0 group by po_break_down_id");
			//$act_ahip_arr=sql_select("select po_break_down_id, sum(ex_factory_qnty) as shipout_qty from  pro_ex_factory_mst where status_active=1 and is_deleted=0 group by po_break_down_id");// quotation_id='$data'
			foreach($act_ahip_arr as $row)
			{
				$actual_shipout_arr[$row[csf('po_break_down_id')]]['shipout_qty']=$row[csf('shipout_qty')]-$row[csf('ret_shipout_qty')];
			}
			
			$financial_para=array();
			$sql_std_para=sql_select("select interest_expense,income_tax,cost_per_minute from lib_standard_cm_entry where company_id=$company_name and status_active=1 and	is_deleted=0 order by id");	
			foreach($sql_std_para as $sql_std_row)
			{
				$financial_para[csf('interest_expense')]=$sql_std_row[csf('interest_expense')];
				$financial_para[csf('income_tax')]=$sql_std_row[csf('income_tax')];
				$financial_para[csf('cost_per_minute')]=$sql_std_row[csf('cost_per_minute')];
			}
		ob_start();
		?>
		<br/>   
		<fieldset>	
        <table width="2700">
            <tr class="form_caption">
                <td colspan="27" align="center"><strong>Order Wise Budget On Shipout Report</strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="27" align="center"><strong><? echo $company_library[$company_name]; ?></strong></td>
            </tr>
        </table>
        <table class="rpt_table" width="2860" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr>
                    <th width="35">SL</th>
                    <th width="120">Buyer</th>
                    <th width="100">Job</th>
                    <th width="110">Style Ref.</th>
                    <th width="100">Order No</th>
                    <th width="100">Order Qty(Pcs)</th>
                    <th width="80">File No</th>
                    <th width="80">Internal Ref: No</th>
                    <th width="100">Actual Shipment Qty(Pcs)</th>
                    <th width="110">Particulars</th>
                    <th width="100">Gross FOB Value</th>
                    <th width="100">Less Commission</th>
                    <th width="100">Net FOB Value</th>
                    <th width="100">Less Cost Of Material & Service</th>
                    <th width="100">Yarn Cost</th>
                    <th width="100">Conversion Cost</th>
                    <th width="100">Trims Cost</th>
                    <th width="100">Embellishment Cost</th>
                    <th width="100">Other Direct Exp.</th>
                    <th width="100">Contribution /Value Additions</th>
                    <th width="100">Less CM Cost</th>
                    <th width="100">Gross Profit/Loss Cost</th>
                    <th width="100">Less Commercial Cost</th>
                    <th width="100">Less Operating Exp.</th>
                    <th width="100">Operation Profit/Loss</th>
                    <th width="100">Less Deprecation & Amortization</th>
                    <th width="100">Less Interest</th>
                    <th width="100">Less Income Tex</th>
                    <th>Net Profit</th>
                </tr>
            </thead>
        </table>
        <div style="width:2880px; max-height:250px; overflow-y:scroll" id="scroll_body">
        <table class="rpt_table" width="2860" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
        <? 
        	$i=1;
			$sql="select a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, a.avg_unit_price, a.total_set_qnty as ratio, b.id as po_id, b.po_number, b.po_quantity, b.plan_cut, b.unit_price, b.shiping_status, b.grouping, b.file_no, b.po_total_price from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond $buyer_id_cond  $year_cond $job_no_cond $order_id_cond_trans $order_no_cond $order_status_cond $season_cond $internal_ref_cond order by b.id ";// and b.shiping_status=3
        	//echo $sql;//die;
        	$result=sql_select($sql);
        	//$tot_rows=count($result);
        	foreach($result as $row)
        	{
        		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				$dzn_qnty=0;
				$costing_per_id=$fabriccostArray[$row[csf('job_no')]]['costing_per_id'];
				if($costing_per_id==1) $dzn_qnty=12;
				else if($costing_per_id==3) $dzn_qnty=12*2;
				else if($costing_per_id==4) $dzn_qnty=12*3;
				else if($costing_per_id==5) $dzn_qnty=12*4;
				else $dzn_qnty=1;
				
				$dzn_qnty_yarn=$dzn_qnty*$row[csf('ratio')];
				$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
				$order_qty_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
				$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
				
				$order_value=$row[csf('po_total_price')];//$row[csf('po_quantity')]*$row[csf('avg_unit_price')];
				$gross_fob_value=$row[csf('po_total_price')];//$row[csf('po_quantity')]*$row[csf('avg_unit_price')];
				$actual_shipout_qty=$actual_shipout_arr[$row[csf('po_id')]]['shipout_qty'];
				$actual_shipout_val=$actual_shipout_qty*$row[csf('unit_price')];
				
				$foreign=($commission_array[$row[csf('job_no')]]['foreign']/$dzn_qnty)*$order_qty_pcs;
				$local=($commission_array[$row[csf('job_no')]]['local']/$dzn_qnty)*$order_qty_pcs;
				$commission_cost=$foreign+$local;
				$net_fob_value=$gross_fob_value-$commission_cost;
				
				$kniting_cost=$knit_arr[$row[csf('job_no')]]['knit']+$knit_arr[$row[csf('job_no')]]['weaving']+$knit_arr[$row[csf('job_no')]]['collar_cuff']+$knit_arr[$row[csf('job_no')]]['feeder_stripe'];
				$tot_knit_cost=($kniting_cost/$dzn_qnty)*$plan_cut_qnty;
				$fabric_dyeing_cost=($knit_arr[$row[csf('job_no')]]['fabric_dyeing']/$dzn_qnty)*$plan_cut_qnty;
				$yarn_dyed_cost=($knit_arr[$row[csf('job_no')]]['yarn_dyeing']/$dzn_qnty)*$plan_cut_qnty;
				$heat_setting_cost=($knit_arr[$row[csf('job_no')]]['heat']/$dzn_qnty)*$plan_cut_qnty;
				$fabric_finish=($knit_arr[$row[csf('job_no')]]['fabric_finish']/$dzn_qnty)*$plan_cut_qnty;
				$washing_cost=($knit_arr[$row[csf('job_no')]]['washing']/$dzn_qnty)*$plan_cut_qnty;
				$all_over_cost=($knit_arr[$row[csf('job_no')]]['all_over']/$dzn_qnty)*$plan_cut_qnty;
				
				
				
				$fabricData=explode(",",substr($fabric_data_Array[$row[csf('job_no')]],0,-1));
				$fab_precost_Data=explode(",",substr($fab_precost_arr[$row[csf('job_no')]][$row[csf('po_id')]],0,-1));
				//$yarn_costing=0;
					//$actual_yarn_costing=0;
				foreach($fabricData as $fabricRow)
				{
					$fabricRow=explode("**",$fabricRow);
					$fab_nature_id=$fabricRow[0];	
					$fab_source_id=$fabricRow[1];
					$fab_rate=$fabricRow[2];
					$yarn_qty=$fabricRow[3];
					$yarn_amount=$fabricRow[4];
					
					if($fab_source_id==2)
					{
						foreach($fab_precost_Data as $fab_row)
						{
							$fab_dataRow=explode("**",$fab_row);
							$fab_requirment=$fab_dataRow[0];
							$fab_pcs=$fab_dataRow[1];
							$fab_purchase_qty=$fab_requirment/$fab_pcs*$plan_cut_qnty; 
						//echo $fab_purchase_qty;
						$fab_purchase=$fab_purchase_qty*$fab_rate; 
							
						}
					}
					else if($fab_source_id==1 || $fab_source_id==3)
					{
						$avg_rate=$yarn_amount/$yarn_qty;
						//echo $yarn_amount.'/='.$dzn_qnty.'*'.$plan_cut_qnty;
						$yarn_costing=$yarn_amount/$dzn_qnty*$plan_cut_qnty;
						 $actual_yarn_costing=($yarn_amount/$dzn_qnty)*$plan_cut_qnty;//*$actual_shipout_qty;			
					}
					//echo $plan_cut_qnty;
				 
				}
				//echo $yarn_amount.'=='.$dzn_qnty.'=='.$actual_shipout_qty.'aziz';
				//$yarn_cost_percent=($yarn_costing/$total_gross_fob_plancut_value)*100;
				//$trim_qty_arr[$row[csf('po_id')]];//
				//echo $trim_qty_arr_data[$row[csf('po_id')]];
				$tot_trim_cost=($fabriccostArray[$row[csf('job_no')]]['trims_cost']/$dzn_qnty)*$order_qty_pcs;
				$print_cost=($fab_emb[$row[csf('job_no')]]['print']/$dzn_qnty)*$order_qty_pcs;
				$embroidery_cost=($fab_emb[$row[csf('job_no')]]['embroidery']/$dzn_qnty)*$order_qty_pcs;
				$special_cost=($fab_emb[$row[csf('job_no')]]['special']/$dzn_qnty)*$order_qty_pcs;
				$tot_embell_cost=$print_cost+$embroidery_cost+$special_cost;
				
				$freight_cost= $fabriccostArray[$row[csf('job_no')]]['freight']/$dzn_qnty*$order_qty_pcs;
				$inspection=$fabriccostArray[$row[csf('job_no')]]['inspection']/$dzn_qnty*$order_qty_pcs;
				$certificate_cost=$fabriccostArray[$row[csf('job_no')]]['certificate_pre_cost']/$dzn_qnty*$order_qty_pcs;
				
				$wash_cost=($fab_emb[$row[csf('job_no')]]['wash']/$dzn_qnty)*$order_qty_pcs;
				$currier_cost=$fabriccostArray[$row[csf('job_no')]]['currier_pre_cost']/$dzn_qnty*$order_qty_pcs;
				$lab_test_cost=$fabriccostArray[$row[csf('job_no')]]['lab_test']/$dzn_qnty*$order_qty_pcs;
				$other_direct_expenses=$freight_cost+$inspection+$certificate_cost+$currier_cost+$lab_test_cost+$wash_cost;
				
				$tot_conversion_cost=$conv_data[$row[csf('po_id')]]['conv_amount'];//$tot_knit_cost+$fabric_dyeing_cost+$yarn_dyed_cost+$heat_setting_cost+$fabric_finish+$washing_cost+$all_over_cost;
				$cost_of_material_service=$yarn_costing+$tot_conversion_cost+$tot_trim_cost+$tot_embell_cost+$other_direct_expenses;
				
				$contribution_value=$net_fob_value-$cost_of_material_service;
				$cm_cost=$fabriccostArray[$row[csf('job_no')]]['c_cost']/$dzn_qnty*$order_qty_pcs;
				$gross_profit=$contribution_value-$cm_cost;
				$commercial_cost=$fabriccostArray[$row[csf('job_no')]]['comm_cost'];
				$tot_commercial_cost=($commercial_cost/$dzn_qnty)*$order_qty_pcs;
				$operating_expense=$fabriccostArray[$row[csf('job_no')]]['common_oh']/$dzn_qnty*$order_qty_pcs;
				$operating_profit=$gross_profit-($tot_commercial_cost+$operating_expense);
				$depreciation_amortization=$fabriccostArray[$row[csf('job_no')]]['depr_amor_cost']/$dzn_qnty*$order_qty_pcs;
				$interest_expense=$net_fob_value*$financial_para[csf('interest_expense')]/100;
				$income_tax=$net_fob_value*$financial_para[csf('income_tax')]/100;
				$net_profit=$operating_profit-($depreciation_amortization+$interest_expense+$income_tax);
				//=========================Actual_shipout========
				$actual_foreign=($commission_array[$row[csf('job_no')]]['foreign']/$dzn_qnty)*$actual_shipout_qty;
				$actual_local=($commission_array[$row[csf('job_no')]]['local']/$dzn_qnty)*$actual_shipout_qty;
				$actual_commission_cost=$actual_foreign+$actual_local;
				$actual_net_fob_value=$actual_shipout_val-$actual_commission_cost;
				
				$actual_knit_cost=($kniting_cost/$dzn_qnty)*$plan_cut_qnty;
				$actual_fabric_dyeing_cost=($knit_arr[$row[csf('job_no')]]['fabric_dyeing']/$dzn_qnty)*$plan_cut_qnty;
				$actual_yarn_dyed_cost=($knit_arr[$row[csf('job_no')]]['yarn_dyeing']/$dzn_qnty)*$plan_cut_qnty;
				$actual_heat_setting_cost=($knit_arr[$row[csf('job_no')]]['heat']/$dzn_qnty)*$plan_cut_qnty;
				$actual_fabric_finish=($knit_arr[$row[csf('job_no')]]['fabric_finish']/$dzn_qnty)*$plan_cut_qnty;
				$actual_washing_cost=($knit_arr[$row[csf('job_no')]]['washing']/$dzn_qnty)*$plan_cut_qnty;
				$actual_all_over_cost=($knit_arr[$row[csf('job_no')]]['all_over']/$dzn_qnty)*$plan_cut_qnty;
				
				//$yarn_cost_percent=($yarn_costing/$total_gross_fob_plancut_value)*100;
				$actual_trim_cost=($fabriccostArray[$row[csf('job_no')]]['trims_cost']/$dzn_qnty)*$actual_shipout_qty;
				$actual_print_cost=($fab_emb[$row[csf('job_no')]]['print']/$dzn_qnty)*$actual_shipout_qty;
				$actual_embroidery_cost=($fab_emb[$row[csf('job_no')]]['embroidery']/$dzn_qnty)*$actual_shipout_qty;
				$actual_special_cost=($fab_emb[$row[csf('job_no')]]['special']/$dzn_qnty)*$actual_shipout_qty;
				$actual_embell_cost=$actual_print_cost+$actual_embroidery_cost+$actual_special_cost;
				
				$actual_freight_cost= $fabriccostArray[$row[csf('job_no')]]['freight']/$dzn_qnty*$actual_shipout_qty;
				$actual_inspection=$fabriccostArray[$row[csf('job_no')]]['inspection']/$dzn_qnty*$actual_shipout_qty;
				$actual_certificate_cost=$fabriccostArray[$row[csf('job_no')]]['certificate_pre_cost']/$dzn_qnty*$actual_shipout_qty;
				
				$actual_wash_cost=($fab_emb[$row[csf('job_no')]]['wash']/$dzn_qnty)*$actual_shipout_qty;
				$actual_currier_cost=$fabriccostArray[$row[csf('job_no')]]['currier_pre_cost']/$dzn_qnty*$actual_shipout_qty;
				$actual_lab_test_cost=$fabriccostArray[$row[csf('job_no')]]['lab_test']/$dzn_qnty*$actual_shipout_qty;
				$actual_other_direct_expenses=$actual_freight_cost+$actual_inspection+$actual_certificate_cost+$actual_currier_cost+$actual_lab_test_cost+$actual_wash_cost;
				
				$actual_conversion_cost=$conv_data[$row[csf('po_id')]]['conv_amount'];
				//$actual_knit_cost+$actual_fabric_dyeing_cost+$actual_yarn_dyed_cost+$actual_heat_setting_cost+$actual_fabric_finish+$actual_washing_cost+$actual_all_over_cost;
				$actual_cost_of_material_service=$actual_yarn_costing+$actual_conversion_cost+$actual_trim_cost+$actual_embell_cost+$actual_other_direct_expenses;
				
				$actual_contribution_value=$actual_net_fob_value-$actual_cost_of_material_service;
				$actual_cm_cost=$fabriccostArray[$row[csf('job_no')]]['c_cost']/$dzn_qnty*$actual_shipout_qty;
				$actual_gross_profit=$actual_contribution_value-$actual_cm_cost;
				$actual_commercial_cost=$fabriccostArray[$row[csf('job_no')]]['comm_cost'];
				$actual_tot_commercial_cost=($commercial_cost/$dzn_qnty)*$actual_shipout_qty;
				$actual_operating_expense=$fabriccostArray[$row[csf('job_no')]]['common_oh']/$dzn_qnty*$actual_shipout_qty;
				$actual_operating_profit=$actual_gross_profit-($actual_tot_commercial_cost+$actual_operating_expense);
				$actual_depreciation_amortization=$fabriccostArray[$row[csf('job_no')]]['depr_amor_cost']/$dzn_qnty*$actual_shipout_qty;
				$actual_interest_expense=$actual_net_fob_value*$financial_para[csf('interest_expense')]/100;
				$actual_income_tax=$actual_net_fob_value*$financial_para[csf('income_tax')]/100;
				$actual_net_profit=$actual_operating_profit-($actual_depreciation_amortization+$actual_interest_expense+$actual_income_tax);
				$tot_order_value+=$order_value; $tot_commission_cost+=$commission_cost;
				?>
                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                    <td width="35" rowspan="4"><? echo $i; ?></td>
                    <td width="120" rowspan="4"><p><? echo $buyer_library[$row[csf('buyer_name')]] ?></p></td>
                    <td width="100" rowspan="4"><p><? echo $row[csf('job_no')]; ?></p></td>
                    <td width="110" rowspan="4"><p><? echo  $row[csf('style_ref_no')]; ?></p></td>
                    <td width="100" rowspan="4"><p><? echo $row[csf('po_number')]; ?></p></td>
                    <td width="100" rowspan="4" align="right"><p><? echo number_format($row[csf('po_quantity')],2); ?></p></td>
                    <td width="80" rowspan="4"><p><? echo $row[csf('file_no')]; ?></p></td>
                    <td width="80" rowspan="4"><p><? echo $row[csf('grouping')]; ?></p></td>
                    <td width="100" rowspan="4" align="right"><p><? echo number_format($actual_shipout_qty,2); ?></p></td>
                    <td width="110"><p><strong>Budget Value</strong></p></td>
                    <td width="100" align="right"><p><? echo number_format($order_value,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($commission_cost,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($net_fob_value,2); ?></p></td>
                    <td width="100" align="right" title="Yarn Costing+Conversion Cost+Trim Cost+Embell Cost+Other Direct Expenses"><p><? echo number_format($cost_of_material_service,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($yarn_costing,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($tot_conversion_cost,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($tot_trim_cost,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($tot_embell_cost,2); ?></p></td>
                    <td width="100" align="right" title="Freight Cost + Inspection + Certificate Cost+Currier Cost+Lab Test Cost+Wash Cost"><p><? echo number_format($other_direct_expenses,2); ?></p></td>
                    <td width="100" align="right" title=" Net Fob Value-Actual Cost Of Material Service"><p><? echo number_format($contribution_value,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($cm_cost,2); ?></p></td>
                    <td width="100" align="right" title=" Contribution Value-Actual Cm Cost"><p><? echo number_format($gross_profit,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($tot_commercial_cost,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($operating_expense,2); ?></p></td>
                    <td width="100" align="right" title="Gross Profit-(Actual Tot Commercial Cost+Actual Operating Expense)"><p><? echo number_format($operating_profit,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($depreciation_amortization,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($interest_expense,2); ?></p></td>
                    <td width="100" align="right" title="Net_fob_value*Financial_para/100"><p><? echo number_format($income_tax,2); ?></p></td>
                    <td align="right" title=" operating_profit-(Actual_depreciation_amortization+Actual_interest_expense+Actual_income_tax)"><p><? echo number_format($net_profit,2); ?></p></td>
                </tr>
                <tr>
                	<td width="90"><p><strong>Budget Achivement</strong></p></td>
                    <td width="100" align="right"><p><? echo number_format($actual_shipout_val,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($actual_commission_cost,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($actual_net_fob_value,2); ?></p></td>
                    <td width="100" align="right" title="Actual Yarn Costing+Conversion Cost + Trim Cost + Embell Cost + Other Direct Expenses"><p><? echo number_format($actual_cost_of_material_service,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($actual_yarn_costing,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($actual_conversion_cost,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($actual_trim_cost,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($actual_embell_cost,2); ?></p></td>
                    <td width="100" align="right" title="Freight Cost + Inspection + Certificate Cost+Currier Cost+Lab Test Cost+Wash Cost"><p><? echo number_format($actual_other_direct_expenses,2); ?></p></td>
                    <td width="100" align="right" title="Actual Net Fob Value-Actual Cost Of Material Service"><p><? echo number_format($actual_contribution_value,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($actual_cm_cost,2); ?></p></td>
                    <td width="100" align="right" title="Actual Contribution Value-Actual Cm Cost"><p><? echo number_format($actual_gross_profit,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($actual_tot_commercial_cost,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($actual_operating_expense,2); ?></p></td>
                    <td width="100" align="right" title="Actual Gross Profit-(Actual Tot Commercial Cost+Actual Operating Expense)"><p><? echo number_format($actual_operating_profit,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($actual_depreciation_amortization,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($actual_interest_expense,2); ?></p></td>
                    <td width="100" align="right" title="Actual Net_fob_value*Financial_para/100"><p><? echo number_format($actual_income_tax,2); ?></p></td>
                    <td align="right" title=" Actual_operating_profit-(Actual_depreciation_amortization+Actual_interest_expense+Actual_income_tax)"><p><? echo number_format($actual_net_profit,2); ?></p></td>
                </tr>
                <?
					$pro_order_value=$actual_shipout_val-$order_value;
					$pro_commission_cost=$commission_cost-$actual_commission_cost;
					$pro_net_fob_value=$actual_net_fob_value-$net_fob_value;
					$pro_cost_of_material_service=$cost_of_material_service-$actual_cost_of_material_service;
					$pro_yarn_costing=$yarn_costing-$actual_yarn_costing;
					$pro_conversion_cost=$tot_conversion_cost-$actual_conversion_cost;
					$pro_trim_cost=$tot_trim_cost-$actual_trim_cost;
					$pro_embell_cost=$tot_embell_cost-$actual_embell_cost;
					$pro_other_direct_expenses=$other_direct_expenses-$actual_other_direct_expenses;
					$pro_contribution_value=$contribution_value-$actual_contribution_value;
					$pro_cm_cost=$cm_cost-$actual_cm_cost;
					$pro_gross_profit=$gross_profit-$actual_gross_profit;
					$pro_commercial_cost=$tot_commercial_cost-$actual_tot_commercial_cost;
					$pro_operating_expense=$operating_expense-$actual_operating_expense;
					$pro_operating_profit=$operating_profit-$actual_operating_profit;
					$pro_depreciation_amortization=$depreciation_amortization-$actual_depreciation_amortization;
					$pro_interest_expense=$interest_expense-$actual_interest_expense;
					$pro_income_tax=$income_tax-$actual_income_tax;
					$pro_net_profit=$net_profit-$actual_net_profit;
				?>
                <tr>
                	<td width="90"><p><strong>Variance</strong></p></td>
                    <td width="100" align="right" title="Net Profit-Actual_Net_profit"><p><? echo number_format($pro_order_value,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($pro_commission_cost,2); ?></p></td>
                    <td width="100" align="right" title="Actual_net_fob_value-Net_fob_value"><p><? echo number_format($pro_net_fob_value,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($pro_cost_of_material_service,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($pro_yarn_costing,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($pro_conversion_cost,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($pro_trim_cost,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($pro_embell_cost,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($pro_other_direct_expenses,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($pro_contribution_value,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($pro_cm_cost,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($pro_gross_profit,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($pro_commercial_cost,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($pro_operating_expense,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($pro_operating_profit,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($pro_depreciation_amortization,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($pro_interest_expense,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($pro_income_tax,2); ?></p></td>
                    <td align="right"><p><? echo number_format($pro_net_profit,2); ?></p></td>
                </tr>
                <?
					$per_order_value=($pro_order_value/$order_value)*100;
					$per_commission_cost=($pro_commission_cost/$commission_cost)*100;
					$per_net_fob_value=($pro_net_fob_value/$net_fob_value)*100;
					$per_cost_of_material_service=($pro_cost_of_material_service/$cost_of_material_service)*100;
					$per_yarn_costing=($pro_yarn_costing/$yarn_costing)*100;
					$per_conversion_cost=($pro_conversion_cost/$tot_conversion_cost)*100;
					$per_trim_cost=($pro_trim_cost/$tot_trim_cost)*100;
					$per_embell_cost=($pro_embell_cost/$tot_embell_cost)*100;
					$per_other_direct_expenses=($pro_other_direct_expenses/$other_direct_expenses)*100;
					$per_contribution_value=($pro_contribution_value/$contribution_value)*100;
					$per_cm_cost=($pro_cm_cost/$cm_cost)*100;
					$per_gross_profit=($pro_gross_profit/$gross_profit)*100;
					$per_commercial_cost=($pro_commercial_cost/$tot_commercial_cost)*100;
					$per_operating_expense=($pro_operating_expense/$operating_expense)*100;
					$per_operating_profit=($pro_operating_profit/$operating_profit)*100;
					$per_depreciation_amortization=($pro_depreciation_amortization/$depreciation_amortization)*100;
					$per_interest_expense=($pro_interest_expense/$interest_expense)*100;
					$per_income_tax=($pro_income_tax/$income_tax)*100;
					$per_net_profit=($pro_net_profit/$net_profit)*100;
				?>
                 <tr>
                	<td width="90"><p><strong>Variance %</strong></p></td>
                    <td width="100" align="right"><p><? echo number_format($per_order_value,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($per_commission_cost,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($per_net_fob_value,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($per_cost_of_material_service,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($per_yarn_costing,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($per_conversion_cost,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($per_trim_cost,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($per_embell_cost,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($per_other_direct_expenses,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($per_contribution_value,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($per_cm_cost,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($per_gross_profit,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($per_commercial_cost,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($per_operating_expense,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($per_operating_profit,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($per_depreciation_amortization,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($per_interest_expense,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($per_income_tax,2); ?></p></td>
                    <td align="right"><p><? echo number_format($per_net_profit,2); ?></p></td>
                </tr>
				<?
				//$total_net_fob_value+=$net_fob_value;
                $i++;
            }
			//echo $tot_order_value;
			?>
            </table>
            <table class="tbl_bottom" width="2860" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
                <tr>
               <td width="35"></td>
                    <td width="120"></td>
                    <td width="100"></td>
                    <td width="110"></td>
                    <td width="100"></td>
                    <td width="100"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="100"></td>
                    <td width="110"></td>
                    <td width="100"><? echo number_format($tot_order_value,2);?></td>
                    <td width="100"><? echo number_format($tot_commission_cost,2);?></td>
                    <td width="100"><? //echo number_format($tot_commission_cost,2);?></td>
                    <td width="100"></td>
                    <td width="100"></td>
                    <td width="100"></td>
                    <td width="100"></td>
                    <td width="100"></td>
                    <td width="100"></td>
                    <td width="100"></td>
                    <td width="100"></td>
                    <td width="100"></td>
                    <td width="100"></td>
                    <td width="100"></td>
                    <td width="100"></td>
                    <td width="100"></td>
                    <td width="100"></td>
                    <td width="100"></td>
                    <td></td>
                
                </tr>
            </table>
        </div>
        </fieldset>
        </div>
        <?
		}
	}
	
	else if($report_type==7)//Budget 2
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
			//var_dump($asking_profit_arr);die;
			$costing_date_sql=sql_select("select job_no, costing_date from wo_pre_cost_mst where status_active=1 and is_deleted=0 ");
			foreach($costing_date_sql as $row )
			{
				$cost_date=change_date_format($row[csf('costing_date')],'','',1);
				//echo $cost_date=change_date_format($row[csf('costing_date')]);
				$costing_date_arr[$row[csf('job_no')]]['ask']=$asking_profit_arr[$cost_date]['asking_profit'];
				$costing_date_arr[$row[csf('job_no')]]['max']=$asking_profit_arr[$cost_date]['max_profit'];
			}
		
			$sql_budget="select a.job_no_prefix_num, b.insert_date, a.order_uom,a.job_no, a.buyer_name, a.style_ref_no, b.is_confirmed, a.agent_name, a.avg_unit_price, 
			a.dealing_marchant, a.gmts_item_id, a.total_set_qnty as ratio, b.plan_cut, b.id as po_id, b.po_number, b.pub_shipment_date, b.po_received_date,
			b.po_quantity, b.po_total_price, b.unit_price, b.grouping, b.file_no
			from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c  where a.job_no=b.job_no_mst and a.job_no=c.job_no and c.job_no=b.job_no_mst and c.entry_from=158 and a.company_name='$company_name' and a.status_active=1 and
			a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond 
		    $order_status_cond $season_cond $internal_ref_cond $file_no_cond";
			
			$result_sql_budget=sql_select($sql_budget); 
			$tot_rows_budget=count($result_sql_budget); 
			
			ob_start();
			
			?>
            <script>
			 var order_amt=document.getElementById('total_order_amount2').innerHTML.replace(/,/g ,'');
			document.getElementById('yarn_cost').innerHTML=document.getElementById('total_yarn_cost2').innerHTML;
			document.getElementById('yarn_cost_per').innerHTML=document.getElementById('total_yarn_cost_per').innerHTML + ' %';
			document.getElementById('purchase_cost').innerHTML=document.getElementById('total_purchase_cost').innerHTML;
			document.getElementById('purchase_cost_per').innerHTML=number_format_common((document.getElementById('total_purchase_cost').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			document.getElementById('knit_cost').innerHTML=document.getElementById('total_knitting_cost').innerHTML;
			document.getElementById('knit_cost_per').innerHTML=number_format_common((document.getElementById('total_knitting_cost').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			document.getElementById('ydyeing_cost').innerHTML=document.getElementById('total_yarn_dyeing_cost').innerHTML;
			document.getElementById('ydyeing_cost_per').innerHTML=number_format_common((document.getElementById('total_yarn_dyeing_cost').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			document.getElementById('aop_cost').innerHTML=document.getElementById('all_over_print_cost').innerHTML;
			document.getElementById('aop_cost_per').innerHTML=number_format_common((document.getElementById('all_over_print_cost').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			
			var dyefin_val=(parseFloat(document.getElementById('total_fabric_dyeing_cost4').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('total_finishing_cost').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('total_heat_setting_cost').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('total_washing_cost').innerHTML.replace(',','')));
			document.getElementById('dyefin_cost').innerHTML=number_format_common(dyefin_val,2);
			document.getElementById('dyefin_cost_per').innerHTML=number_format_common((dyefin_val/order_amt)*100,2) + ' %';
			document.getElementById('trim_cost').innerHTML=document.getElementById('total_trim_cost').innerHTML;
			document.getElementById('trim_cost_per').innerHTML=number_format_common((document.getElementById('total_trim_cost').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			var embelishment_val=(parseFloat(document.getElementById('total_print_amount').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('total_embroidery_amount').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('total_special_amount').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('total_wash_cost').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('total_other_amount').innerHTML.replace(',','')));
			document.getElementById('embelishment_cost').innerHTML=number_format_common(embelishment_val,2);
			document.getElementById('embelishment_cost_per').innerHTML=number_format_common((embelishment_val/order_amt)*100,2) + ' %';
			document.getElementById('commercial_cost').innerHTML=document.getElementById('total_commercial_cost').innerHTML;
			document.getElementById('commercial_cost_per').innerHTML=number_format_common((document.getElementById('total_commercial_cost').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			var comission_val=(parseFloat(document.getElementById('total_foreign_amount').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('total_local_amount').innerHTML.replace(',','')));
			document.getElementById('commission_cost').innerHTML=number_format_common(comission_val,2);
			document.getElementById('commission_cost_per').innerHTML=number_format_common((comission_val/order_amt)*100,2) + ' %';
			document.getElementById('testing_cost').innerHTML=document.getElementById('total_test_cost_amount').innerHTML;
			document.getElementById('testing_cost_per').innerHTML=number_format_common((document.getElementById('total_test_cost_amount').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			document.getElementById('freight_cost').innerHTML=document.getElementById('total_freight_amount').innerHTML;
			document.getElementById('freight_cost_per').innerHTML=number_format_common((document.getElementById('total_freight_amount').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			document.getElementById('inspection_cost').innerHTML=document.getElementById('total_inspection_amount').innerHTML;
			document.getElementById('inspection_cost_per').innerHTML=number_format_common((document.getElementById('total_inspection_amount').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			document.getElementById('certificate_cost').innerHTML=document.getElementById('total_certificate_amount').innerHTML;
			document.getElementById('certificate_cost_percent').innerHTML=number_format_common((document.getElementById('total_certificate_amount').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			
			document.getElementById('commn_cost').innerHTML=document.getElementById('total_common_oh_amount').innerHTML;
			document.getElementById('commn_cost_per').innerHTML=number_format_common((document.getElementById('total_common_oh_amount').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			
			document.getElementById('courier_cost').innerHTML=document.getElementById('total_currier_amount').innerHTML;
			document.getElementById('courier_cost_per').innerHTML=number_format_common((document.getElementById('total_currier_amount').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			document.getElementById('cm_cost').innerHTML=document.getElementById('total_cm_amount').innerHTML;
			document.getElementById('cm_cost_per').innerHTML=number_format_common((document.getElementById('total_cm_amount').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			
			document.getElementById('cost_cost').innerHTML=document.getElementById('total_tot_cost').innerHTML;
			document.getElementById('cost_cost_per').innerHTML=number_format_common((document.getElementById('total_tot_cost').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			
			document.getElementById('order_id').innerHTML=number_format_common(order_amt,2);
			document.getElementById('order_percent').innerHTML=number_format_common((order_amt/order_amt)*100,2);
			
			document.getElementById('fab_profit_id').innerHTML=document.getElementById('total_fabric_profit').innerHTML;
			document.getElementById('profit_fab_percentage').innerHTML=number_format_common((document.getElementById('total_fabric_profit').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			document.getElementById('expected_id').innerHTML=document.getElementById('total_expected_profit').innerHTML;
			document.getElementById('profit_expt_fab_percentage').innerHTML=number_format_common((document.getElementById('total_expected_profit').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			document.getElementById('expt_p_variance_id').innerHTML=document.getElementById('total_expected_variance').innerHTML;
			document.getElementById('expt_p_percent').innerHTML=number_format_common((document.getElementById('total_expected_variance').innerHTML.replace(',','')/order_amt)*100,2) + ' %';
			
			var matr_ser_cost=(parseFloat(document.getElementById('yarn_cost').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('purchase_cost').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('knit_cost').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('ydyeing_cost').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('aop_cost').innerHTML.replace(',',''))) + (parseFloat(document.getElementById('dyefin_cost').innerHTML.replace(',','')));
			document.getElementById('tot_matr_ser_cost').innerHTML=number_format_common(matr_ser_cost,2);
			document.getElementById('tot_matr_ser_per').innerHTML=number_format_common((matr_ser_cost/order_amt)*100,2) + ' %'; 
			//var aa=order_amt;
			//alert(aa);
			
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
            <div style="width:4570px;">
            <div style="width:900px;" align="left">
                <table width="900" cellpadding="0" cellspacing="2" border="0">
                    <tr>
                        <td width="350" align="left">
                            <table width="350" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="2">
                                <thead align="center">
                                	<tr>
                                    	<th colspan="4">Order Wise Budget Cost Summary ss</th>
                                    </tr>
                                	<tr>
                                        <th>SL</th><th>Particulars</th><th>Amount</th><th>%</th>
                                    </tr>
                                </thead>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td width="20">1</td>
                                    <td width="130">Yarn Cost</td>
                                    <td width="120" align="right" id="yarn_cost"></td>
                                    <td width="80" align="right" id="yarn_cost_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>2</td>
                                    <td>Fabric Purchase</td>
                                    <td align="right" id="purchase_cost"></td>
                                    <td align="right" id="purchase_cost_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>3</td>
                                    <td>Knitting Cost</td>
                                    <td align="right" id="knit_cost"></td>
                                    <td align="right" id="knit_cost_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>4</td>
                                    <td>Yarn Dyeing Cost</td>
                                    <td align="right" id="ydyeing_cost"></td>
                                    <td align="right" id="ydyeing_cost_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>5</td>
                                    <td>AOP Cost</td>
                                    <td align="right" id="aop_cost"></td>
                                    <td align="right" id="aop_cost_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>6</td>
                                    <td>Dyeing & Finishing Cost</td>
                                    <td align="right" id="dyefin_cost"></td>
                                    <td align="right" id="dyefin_cost_per"></td>
                                </tr>
                                <tr bgcolor="#CCCCCC">
                                    <td colspan="2"><strong>Total Material & Service Cost</strong></td>
                                    <td align="right" id="tot_matr_ser_cost"></td>
                                    <td align="right" id="tot_matr_ser_per"></td>
                                </tr>
                                <tr bgcolor="<?  echo $style1; ?>">
                                    <td>7</td>
                                    <td>Trims Cost</td>
                                    <td align="right" id="trim_cost"></td>
                                    <td align="right" id="trim_cost_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>8</td>
                                    <td>Print/ Emb. /Wash Cost</td>
                                    <td align="right" id="embelishment_cost"></td>
                                    <td align="right" id="embelishment_cost_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>9</td>
                                    <td>Commercial Cost</td>
                                    <td align="right" id="commercial_cost"></td>
                                    <td align="right" id="commercial_cost_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>10</td>
                                    <td>Commision Cost</td>
                                    <td align="right" id="commission_cost"></td>
                                    <td align="right" id="commission_cost_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>11</td>
                                    <td>Testing Cost</td>
                                    <td align="right" id="testing_cost"></td>
                                    <td align="right" id="testing_cost_per"></td>
                                </tr>
                                    <tr bgcolor="<? echo $style1; ?>">
                                    <td>12</td>
                                    <td>Freight Cost</td>
                                    <td align="right" id="freight_cost"></td>
                                    <td align="right" id="freight_cost_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td width="20">13</td>
                                    <td width="100">Inspection Cost</td>
                                    <td align="right" id="inspection_cost"></td>
                                    <td align="right" id="inspection_cost_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>14</td>
                                    <td>Certificate Cost</td>
                                    <td align="right" id="certificate_cost"></td>
                                    <td align="right" id="certificate_cost_percent"></td>
                                </tr>
                                    <tr bgcolor="<? echo $style; ?>">
                                    <td>15</td>
                                    <td>Operating Exp.</td>
                                    <td align="right" id="commn_cost"></td>
                                    <td align="right" id="commn_cost_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>16</td>
                                    <td>Courier Cost</td>
                                    <td align="right" id="courier_cost"></td>
                                    <td align="right" id="courier_cost_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>17</td>
                                    <td>CM Cost</td>
                                    <td align="right" id="cm_cost"></td>
                                    <td align="right" id="cm_cost_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>18</td>
                                    <td>Total Cost</td>
                                    <td align="right" id="cost_cost"></td>
                                    <td align="right" id="cost_cost_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>19</td>
                                    <td>Total Order Value</td>
                                    <td align="right" id="order_id"></td>
                                    <td align="right" id="order_percent"></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>20</td>
                                    <td>Profit/Loss </td>
                                    <td align="right" id="fab_profit_id"></td>
                                    <td align="right" id="profit_fab_percentage"></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>21</td>
                                    <td>Expected Profit <div id="expt_percent"></div></td>
                                    <td align="right" id="expected_id"></td>
                                    <td align="right" id="profit_expt_fab_percentage"></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>22</td>
                                    <td>Expt.Profit Variance </td>
                                    <td align="right" id="expt_p_variance_id"></td>
                                    <td align="right" id="expt_p_percent"></td>
                                </tr>
                            </table>
                        </td>
                        <!--<td colspan="5" style="min-height:900px; max-height:100%" align="center" valign="top">
                            <div id="chartdiv" style="width:580px; height:900px;" align="center"></div>
                        </td>-->
                    </tr>
                </table>
            </div>
            <br/> 
            <?
			//ob_start();
			?>
            <h3 align="left" id="accordion_h2" style="width:5200px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel2', '')"> -Search Panel</h3>
        	<fieldset style="width:100%;" id="content_search_panel2">	
            <table width="5175">
                    <tr class="form_caption">
                        <td colspan="55" align="center"><strong>Order Wise Budget Report</strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="55" align="center"><strong><? echo $company_library[$company_name]; ?></strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td align="left" colspan="2"><strong>Details Report </strong></td>
                    </tr>
            </table>
               <? $asking_profit_head=$asking_profit_arr[$company_name]['asking_profit']; 
			   
			   		if(str_replace("'","",$cbo_search_date)==1) $caption="Ship. Date";
					else if(str_replace("'","",$cbo_search_date)==2) $caption="PO Recv. Date";
					else $caption="PO Insert Date";
			   ?>
            <table id="table_header_1" class="rpt_table" width="5175" cellpadding="0" cellspacing="0" border="1" rules="all">
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
                        <th width="110" rowspan="2">Dealing</th>
                        <th width="70" rowspan="2"><? echo $caption; ?></th>
                        <th width="90" rowspan="2">Order Qty</th>
                        <th width="50" rowspan="2">UOM</th>
                        <th width="100" rowspan="2">Order Qnty(Pcs)</th>
                        
                        <th width="90" rowspan="2">Avg Unit Price</th>
                        <th width="100" rowspan="2">Order Value</th>
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
                        <th width="120" rowspan="2">CM/DZN</th>
                        <th width="100" rowspan="2">Total CM Cost</th>
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
            <div style="width:5200px; max-height:400px; overflow-y:scroll" id="scroll_body">
            <table class="rpt_table" width="5175" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
			<? 
			//$approve_arr=return_library_array( "select job_no, approved from wo_pre_cost_mst", "job_no", "approved");
            $i=1; $total_order_qty=0;  $total_yarn_dyeing_cost=0; $total_yarn_cost=0; $total_order_value=0;$total_purchase_cost=0; $grand_tot_trims_cost=0; $total_fabric_dyeing_cost=0; $total_knitting_cost=0; $total_heat_setting_cost=0;$total_finishing_cost=0; $total_washing_cost=0; $fabric_dyeing_cost_dzn=0; $other_cost=0;
			$all_over_print_cost=0;$total_trim_cost=0;$total_commercial_cost=0;$total_yarn_cons=0;
		
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
			 if(str_replace("'","",$txt_season)!='')
			 {
				//$condition->season("=$txt_season"); 
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
		//	echo $conversion->getQuery(); die;
			$conversion_costing_arr=$conversion->getAmountArray_by_order();
			
			$conversion= new conversion($condition);
			$conversion_costing_arr_process=$conversion->getAmountArray_by_orderAndProcess();
			//echo $conversion->getQuery(); die;
		 //  print_r($conversion_costing_arr_process);
		 	$trims= new trims($condition);
			$trims_costing_arr=$trims->getAmountArray_by_order();
			
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
			//var_dump($other_costing_arr);die;
			
		/*	$knit_cost_arr=array(1,2,3,4);$fabric_dyeingCost_arr=array(25,31,32,60,61,62,63,72,80,81,84,85,86,87,38,74,78,79);
			$fab_finish_cost_arr=array(34,65,66,67,68,69,70,71,73,75,76,77,88,90,91,92,93,100,125,127,128,129);
			$washing_cost_arr=array(64,82,89);$aop_cost_arr=array(35,36,37);*/
			$knit_cost_arr=array(1,2,3,4);
			$fabric_dyeingCost_arr=array(25,31,26,32,60,61,62,63,72,80,81,84,85,86,87,38,39,74,78,79,101,133,137,138,139,146,147,149);
			$aop_cost_arr=array(35,36,37,40);
			$fab_finish_cost_arr=array(33,34,38,63,65,66,67,68,69,70,71,72,73,75,76,77,88,82,89,90,91,92,93,94,128,129,135,136,141,143,150,151,155,156,157,145,82,89,132,144);
			$washing_cost_arr=array(140,142,148,64);
			foreach($result_sql_budget as $row )
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
				if(str_replace("'","",$cbo_search_date)==1)
				{
					$ship_po_recv_date=change_date_format($row[csf('pub_shipment_date')]);
				}
				else if(str_replace("'","",$cbo_search_date)==2)
				{
					$ship_po_recv_date=change_date_format($row[csf('po_received_date')]);
				}
				else if(str_replace("'","",$cbo_search_date)==3)
				{
					$insert_date=explode(" ",$row[csf('insert_date')]);
					$ship_po_recv_date=change_date_format($insert_date[0]);
				}
				$approve_status=$approve_arr[$row[csf('job_no')]];
				if($approve_status==1) $is_approve="Approved"; else $is_approve="No";
				$dzn_qnty=0;
				$costing_per_id=$costing_per_arr[$row[csf('job_no')]];//$fabriccostArray[$row[csf('job_no')]]['costing_per_id'];
				if($costing_per_id==1) $dzn_qnty=12;
				else if($costing_per_id==3) $dzn_qnty=12*2;
				else if($costing_per_id==4) $dzn_qnty=12*3;
				else if($costing_per_id==5) $dzn_qnty=12*4;
				//else $dzn_qnty=1;
				else $dzn_qnty=12; // this changed by subbir for ffl i am not clear for this business
				
				
				
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
                     <td width="70"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
                     <td width="100"><p><a href="#" onClick="precost_bom_pop('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')]; ?>','<?  echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>');"><? echo $row[csf('po_number')]; ?></a></p></td>
                     <td width="100"><p><? echo $is_approve; ?></p></td>
                     <td width="80"><p><? echo $row[csf('file_no')]; ?></p></td>
                     <td width="80"><p><? echo $row[csf('grouping')]; ?></p></td>
                     <td width="100"><p><? echo  $order_status[$row[csf('is_confirmed')]]; ?></p></td>
                     <td width="110"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                     <td width="110"><div style="width:110px; word-wrap:break-word;"><? $gmts_item=''; $gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
						foreach($gmts_item_id as $item_id)
						{
							if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=", ".$garments_item[$item_id];
						}
						echo $gmts_item; ?></div></td>
                     <td width="110"><p><? echo $team_member_arr[$row[csf('dealing_marchant')]]; ?></p></td>
                     <td width="70"><p><? echo '&nbsp;'.$ship_po_recv_date; ?></p></td>
                     <td width="90" align="right"><p><? echo number_format($row[csf('po_quantity')],2); ?></p></td>
                     <td width="50" align="right"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
                     <td width="100" align="right"><p><? echo number_format($order_qty_pcs,2); ?></p></td>
                     <td width="90" align="right"><p><? echo number_format($row[csf('avg_unit_price')],4); ?></p></td>
                     <td width="100" align="right"><p><? echo number_format($row[csf('po_total_price')],2); ?></p></td>
                     <? 
						$commercial_cost=$commercial_costing_arr[$row[csf('po_id')]];//($fabriccostArray[$row[csf('job_no')]]['comm_cost']/$dzn_qnty)*$order_qty_pcs;
						
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
						
						// $row[csf('order_uom')]
						//$kniting_cost=$knit_arr[$row[csf('job_no')]]['knit']+$knit_arr[$row[csf('job_no')]]['weaving']+$knit_arr[$row[csf('job_no')]]['collar_cuff']+$knit_arr[$row[csf('job_no')]]['feeder_stripe'];
						
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
							$all_over_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$aop_process_id]);//[$row[csf('order_uom')]];	
						}
						
						//$knit_cost=($kniting_cost/$dzn_qnty)*$plan_cut_qnty;
						
						$yarn_dyeing_cost=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][30]);
						if(is_infinite($yarn_dyeing_cost) || is_nan($yarn_dyeing_cost)){$yarn_dyeing_cost=0;}
						//[$row[csf('order_uom')]];//($yarn_dyeing_cost_dzn/$dzn_qnty)*$plan_cut_qnty;
						$yarn_dyeing_cost_dzn=($yarn_dyeing_cost/$plan_cut_qnty)*$dzn_qnty;
						if(is_infinite($yarn_dyeing_cost_dzn) || is_nan($yarn_dyeing_cost_dzn)){$yarn_dyeing_cost_dzn=0;}
						//$knit_arr[$row[csf('job_no')]]['yarn_dyeing'];
						$fabric_dyeing_cost_dzn=($fabric_dyeing_cost/$plan_cut_qnty)*$dzn_qnty;
						if(is_infinite($fabric_dyeing_cost_dzn) || is_nan($fabric_dyeing_cost_dzn)){$fabric_dyeing_cost_dzn=0;}
						//$fabric_dyeing_cost_dzn=$knit_arr[$row[csf('job_no')]]['fabric_dyeing'];
						//$fabric_dyeing_cost=($fabric_dyeing_cost_dzn/$dzn_qnty)*$plan_cut_qnty;
						$heat_setting_cost=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][33]);
						if(is_infinite($heat_setting_cost) || is_nan($heat_setting_cost)){$heat_setting_cost=0;}
						//[$row[csf('order_uom')]];//($knit_arr[$row[csf('job_no')]]['heat']/$dzn_qnty)*$plan_cut_qnty;
						//$fabric_finish=($knit_arr[$row[csf('job_no')]]['fabric_finish']/$dzn_qnty)*$plan_cut_qnty;
						//$washing_cost=($knit_arr[$row[csf('job_no')]]['washing']/$dzn_qnty)*$plan_cut_qnty;
						//$all_over_cost=($knit_arr[$row[csf('job_no')]]['all_over']/$dzn_qnty)*$plan_cut_qnty;
						//echo $row[csf('po_id')];
						$trim_amount= $trims_costing_arr[$row[csf('po_id')]];
						if(is_infinite($trim_amount) || is_nan($trim_amount)){$trim_amount=0;}

						$print_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][1];
						if(is_infinite($print_amount) || is_nan($print_amount)){$print_amount=0;}
						$embroidery_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][2];
						if(is_infinite($embroidery_amount) || is_nan($embroidery_amount)){$embroidery_amount=0;}
						$special_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][4];
						if(is_infinite($special_amount) || is_nan($special_amount)){$special_amount=0;}
						$wash_cost=$emblishment_costing_arr_name_wash[$row[csf('po_id')]][3];
						if(is_infinite($wash_cost) || is_nan($wash_cost)){$wash_cost=0;}
						$other_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][5];
						if(is_infinite($other_amount) || is_nan($other_amount)){$other_amount=0;}
						$foreign=$commission_costing_arr[$row[csf('po_id')]][1];
						if(is_infinite($foreign) || is_nan($foreign)){$foreign=0;}
						$local=$commission_costing_arr[$row[csf('po_id')]][2];
						if(is_infinite($local) || is_nan($local)){$local=0;}
						$test_cost=$other_costing_arr[$row[csf('po_id')]]['lab_test'];
						if(is_infinite($test_cost) || is_nan($test_cost)){$test_cost=0;}
						$freight_cost= $other_costing_arr[$row[csf('po_id')]]['freight'];
						if(is_infinite($freight_cost) || is_nan($freight_cost)){$freight_cost=0;}
						$inspection=$other_costing_arr[$row[csf('po_id')]]['inspection'];
						if(is_infinite($inspection) || is_nan($inspection)){$inspection=0;}
						$certificate_cost=$other_costing_arr[$row[csf('po_id')]]['certificate_pre_cost'];
						if(is_infinite($certificate_cost) || is_nan($certificate_cost)){$certificate_cost=0;}
						$common_oh=$other_costing_arr[$row[csf('po_id')]]['common_oh'];
						if(is_infinite($common_oh) || is_nan($common_oh)){$common_oh=0;}
						$currier_cost=$other_costing_arr[$row[csf('po_id')]]['currier_pre_cost'];
						if(is_infinite($currier_cost) || is_nan($currier_cost)){$currier_cost=0;}

						
						$cm_cost=$other_costing_arr[$row[csf('po_id')]]['cm_cost'];
						$cm_cost_dzn=($cm_cost/$order_qty_pcs)*$dzn_qnty;
						if(is_infinite($cm_cost_dzn) || is_nan($cm_cost_dzn)){$cm_cost_dzn=0;}
						//$cm_cost_dzn='('.$cm_cost.'/'.$order_qty_pcs.')*'.$dzn_qnty;
						
						
						//$fabriccostArray[$row[csf('job_no')]]['c_cost'];
						//$fabriccostArray[$row[csf('job_no')]]['c_cost']/$dzn_qnty*$order_qty_pcs;
						$total_cost=$yarn_costing+$fab_purchase+$knit_cost+$washing_cost+$all_over_cost+$yarn_dyeing_cost+$fabric_dyeing_cost+$heat_setting_cost+$fabric_finish+$trim_amount+$test_cost+$print_amount+$embroidery_amount+$special_amount+$other_amount+$wash_cost+$commercial_cost+$foreign+$local+$freight_cost+$inspection+$certificate_cost+$common_oh+$currier_cost+$cm_cost;
						
						$total_profit=$order_value-$total_cost;
						$total_profit_percentage2=$total_profit/$order_value*100; 
						$expected_profit=$asking_profit_arr[$row[csf('company_name')]]['asking_profit']*$order_value/100;
						if(is_infinite($expected_profit) || is_nan($expected_profit)){$expected_profit=0;}
						$expect_variance=$total_profit-$expected_profit;
						
						if($fabric_dyeing_cost<=0 && $yarn_dyeing_cost<=0) $color_fab="red"; else $color_fab="";	
						if($yarn_costing<=0) $color_yarn="red"; else $color_yarn="";	
						if($knit_cost<=0) $color_knit="red"; else $color_knit="";	
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
                     <td width="110" align="right"><? echo number_format($yarn_dyeing_cost ,2); ?></td>
                     <td width="120" align="right"><? echo number_format($fabric_dyeing_cost_dzn,2); ?></td>
                     <td width="100" align="right" bgcolor="<? echo $color_fab; ?>"><a href="##" onClick="generate_precost_fab_dyeing_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $fab_source_id; ?>','fab_dyeing_detail')"><? echo number_format($fabric_dyeing_cost,2); ?></a></td>
                     <td width="90" align="right"><? echo number_format($heat_setting_cost,2); ?></td>
                     <td width="100" align="right" ><a href="##" onClick="generate_precost_fab_finishing_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','fab_finishing_detail')"><? echo number_format($fabric_finish,2); ?></a></td>
                     <td width="90" align="right"><a href="##" onClick="generate_precost_fab_finishing_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','fab_washing_detail')"><? echo number_format($washing_cost,2); ?></a></td>
                     <td width="90" align="right"><a href="##" onClick="generate_precost_fab_all_over_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','fab_all_over_detail')"><? echo number_format($all_over_cost,2); ?></a></td>
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
                     <td width="120" align="right"><? echo number_format($cm_cost_dzn,2);?></td>
                     <td width="100" align="right" bgcolor="<? echo $color; ?>"><? echo number_format($cm_cost,2);?></td>
                     <td width="100" align="right"><? echo number_format($total_cost,2); ?></td>
                    <?
						if($total_profit_percentage2<=0 ) $color_pl="red";
						else if($total_profit_percentage2>$max_profit) $color_pl="yellow";	
						else if($total_profit_percentage2<=$max_profit) $color_pl="green";	
						else $color_pl="";	
						$expected_profit=$costing_date_arr[$row[csf('job_no')]]['ask']*$order_value/100;
						$expected_profit_per=$costing_date_arr[$row[csf('job_no')]]['ask'];
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
				$total_common_oh+=$common_oh;
				$total_currier_cost+=$currier_cost;
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
            <table class="tbl_bottom" width="5175" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
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
                    <td width="70">Total</td>
                    <td width="90" align="right" id="total_order_qnty"></td>
                    <td width="50">&nbsp;</td>
                    <td width="100" align="right"><? echo number_format($total_order_qty,2); ?></td>
                    <td width="90">&nbsp;</td>
                    <td width="100" align="right" id="total_order_amount2"><? echo number_format($total_order_amount,2); ?></td>
                    <td width="100">&nbsp;</td>
                    <td width="80" align="right" id="total_yarn_cost2"><? echo number_format($total_yarn_cost,2); ?></td>
                    <td width="80" align="right" id="total_yarn_cost_per"><? echo number_format($total_yarn_cost_percentage,2); ?></td>
                    <td width="100" align="right" id="total_purchase_cost"><? echo number_format($total_purchase_cost,2); ?></td>
                    <td width="80">&nbsp;</td>
                    <td width="80" align="right" id="total_knitting_cost"><? echo number_format($total_knitting_cost,2); ?></td>
                    <td width="100">&nbsp;</td>
                    <td width="110" align="right" id="total_yarn_dyeing_cost"><? echo number_format($total_yarn_dyeing_cost,2); ?></td>
                    <td width="120">&nbsp;</td>
                    <td width="100" align="right" id="total_fabric_dyeing_cost4"><? echo number_format($total_fabric_dyeing_cost,2); ?></td>
                    <td width="90" align="right" id="total_heat_setting_cost"><? echo number_format($total_heat_setting_cost,2); ?></td>		
                    <td width="100" align="right" id="total_finishing_cost"><? echo number_format($total_finishing_cost,2); ?></td>
                    <td width="90" align="right" id="total_washing_cost"><? echo number_format($total_washing_cost,2); ?></td>
                    <td width="90" align="right" id="all_over_print_cost"><? echo number_format($all_over_print_cost,2); ?></td>
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
                    <td width="100" align="right" id="total_expected_profit"><? echo number_format($total_expected_profit,2);?></td>
                    <td width="100" align="right" id="">&nbsp;<? //echo number_format($total_expected_profit,2);?></td>
                    <td width="80"align="right" id="total_expected_variance"><? echo number_format($total_expected_variance,2);?></td>
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
            <a id="displayText" href="javascript:toggle();">Show Yarn Summary</a>
            
            <div style="width:600px; display:none" id="yarn_summary" >
                <div id="data_panel2" align="center" style="width:500px">
                     <input type="button" value="Print Preview" class="formbutton" style="width:100px" name="print" id="print" onClick="new_window1(1)" />
                 </div>
                <table width="500">
                    <tr class="form_caption">
                        <td colspan="6" align="center"><strong>Yarn Cost Summary </strong></td>
                    </tr>
                </table>
                <table class="rpt_table" width="500" cellpadding="0" cellspacing="0" border="1" rules="all" style="display:none">
                    <thead>
                        <th width="30">SL</th>
                        <th width="80">Yarn Count</th>
                        <th width="120">Type</th>
                        <th width="120">Req. Qnty</th>
                        <th width="80">Avg. rate</th>
                        <th>Amount</th>
                    </thead>
					<?
					//not use it
                    $s=1;// $tot_yarn_req_qnty=0; $tot_yarn_req_amnt=0;
                    foreach($yarn_desc_array as $key=>$value)
                    {
						if($s%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$yarn_desc=explode("**",$key);
						
						$tot_yarn_req_qnty+=$yarn_desc_array[$key]['qnty']; 
						$tot_yarn_req_amnt+=$yarn_desc_array[$key]['amnt'];
						?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr3_<? echo $s; ?>','<? echo $bgcolor; ?>')" id="tr3_<? echo $s;?>">
                            <td><? echo $s; ?></td>
                            <td align="center"><? echo $yarn_desc[0]; ?></td>
                            <td><? echo $yarn_desc[1]; ?></td>
                            <td align="right"><? echo number_format($yarn_desc_array[$key]['qnty'],2); ?></td>
                            <td align="right"><? echo number_format($yarn_desc_array[$key]['amnt']/$yarn_desc_array[$key]['qnty'],2); ?></td>
                            <td align="right"><? echo number_format($yarn_desc_array[$key]['amnt'],2); ?></td>
						</tr>
						<?	
						$s++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="3" align="right">Total</th>
                        <th align="right"><? echo number_format($tot_yarn_req_qnty,2); ?></th>
                        <th align="right"><? echo number_format($tot_yarn_req_amnt/$tot_yarn_req_qnty,2); ?></th>
                        <th align="right"><? echo number_format($tot_yarn_req_amnt,2); ?></th>
                    </tfoot>
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
            </div>
            <?
		}
	}
	else if($report_type==8) //Spot Cost Vs Budget
	{
		if($template==1)
		{
			ob_start();
			$style1="#E9F3FF"; 
			$style="#FFFFFF";
			 	
			$cbo_ready_to=str_replace("'","",$cbo_ready_to);
			if($cbo_ready_to==2 || $cbo_ready_to=='' || $cbo_ready_to==0) $cbo_ready_to_cond="and c.ready_to_approved in(2,0)";
			else if($cbo_ready_to==1) $cbo_ready_to_cond="and c.ready_to_approved in(1)";
			else $cbo_ready_to_cond="";

			//if($cbo_ready_to==0 || $cbo_ready_to=='') $cbo_ready_to_cond="";else $cbo_ready_to_cond="and c.ready_to_approved in($cbo_ready_to)";
			$fab_precost_arr=array(); $p_fab_precost_arr=array(); $commission_array=array(); $price_commission_array=array(); $knit_arr=array(); $pq_knit_arr=array(); $fabriccostArray=array(); $fab_emb=array(); $price_fab_emb=array(); $fabric_data_Array=array(); $price_fabric_data_Array=array();$price_costing_perArray=array(); $asking_profit_arr=array(); $yarncostArray=array(); $yarn_desc_array=array(); $costing_date_arr=array();
			
			/*$yarncostDataArray=sql_select("select job_no, count_id, type_id, sum(cons_qnty) as cons_qnty, sum(amount) as amount from wo_pre_cost_fab_yarn_cost_dtls where status_active=1 and is_deleted=0 group by job_no, count_id, type_id");
			foreach($yarncostDataArray as $yarnRow)
			{
				$yarncostArray[$yarnRow[csf('job_no')]].=$yarnRow[csf('count_id')]."**".$yarnRow[csf('type_id')]."**".$yarnRow[csf('cons_qnty')]."**".$yarnRow[csf('amount')].",";
			}*/
			
			$asking_profit=sql_select("select id, company_id, applying_period_date, applying_period_to_date, asking_profit, max_profit from lib_standard_cm_entry where status_active=1 and is_deleted=0 and company_id=$company_name");//$date_max_profit and company_id=3 and id=16
			//echo "select id, company_id, applying_period_date, applying_period_to_date, asking_profit, max_profit from lib_standard_cm_entry where status_active=1 and is_deleted=0 and company_id=$company_name";
			foreach($asking_profit as $ask_row )
			{
				$applying_period_date=change_date_format($ask_row[csf('applying_period_date')],'','',1);
				$applying_period_to_date=change_date_format($ask_row[csf('applying_period_to_date')],'','',1);
				$diff=datediff('d',$applying_period_date,$applying_period_to_date);
				for($j=0;$j<$diff;$j++)
				{
					$date_all=add_date(str_replace("'","",$applying_period_date),$j);
				    $newdate =change_date_format($date_all,'','',1);
					$asking_profit_arr[$newdate]['asking_profit']=$ask_row[csf('asking_profit')];
					$asking_profit_arr[$newDate]['max_profit']=$ask_row[csf('max_profit')];
				}
			}
			$costing_date_sql=sql_select("select job_no, costing_date,ready_to_approved from wo_pre_cost_mst where status_active=1 and is_deleted=0 ");
			foreach($costing_date_sql as $row )
			{
				$cost_date=change_date_format($row[csf('costing_date')],'','',1);
				$costing_date_arr[$row[csf('job_no')]]['ask']=$asking_profit_arr[$cost_date]['asking_profit'];
				$costing_date_arr[$row[csf('job_no')]]['max']=$asking_profit_arr[$cost_date]['max_profit'];
				$job_approve_arr[$row[csf('job_no')]]['ready_to_approved']=$row[csf('ready_to_approved')];
			}
			
			
			$sql_cons_rate="select id, mst_id, item_id, type, particular_type_id, consumption, ex_percent, tot_cons, unit, is_calculation, rate, rate_data, value from qc_cons_rate_dtls where status_active=1 and is_deleted=0 order by id asc";
			$sql_result_cons_rate=sql_select($sql_cons_rate); $yarn_dtls_arr=array(); $other_cost_arr=array();
			foreach ($sql_result_cons_rate as $rowConsRate)
			{
				if($rowConsRate[csf("type")]==1)
				{
					if($rowConsRate[csf("rate_data")]!="") $edata=explode("~~",$rowConsRate[csf("rate_data")]); else $edata="";
					$rate=$edata[3]+$edata[7]+$edata[11];
					$yarn_dtls_arr[$rowConsRate[csf("mst_id")]]['yarnkg']+=$rowConsRate[csf("tot_cons")];
					$yarn_dtls_arr[$rowConsRate[csf("mst_id")]]['yarnamt']+=$rowConsRate[csf("tot_cons")]*$rate;
					$yarn_dtls_arr[$rowConsRate[csf("mst_id")]]['knitamt']+=$rowConsRate[csf("tot_cons")]*$edata[14];
					$yarn_dtls_arr[$rowConsRate[csf("mst_id")]]['dyeamt']+=$rowConsRate[csf("tot_cons")]*$edata[15];
					$yarn_dtls_arr[$rowConsRate[csf("mst_id")]]['aopamt']+=$rowConsRate[csf("tot_cons")]*$edata[16];
					$yarn_dtls_arr[$rowConsRate[csf("mst_id")]]['finamt']+=$rowConsRate[csf("tot_cons")]*$edata[17];
					$yarn_dtls_arr[$rowConsRate[csf("mst_id")]]['otheramt']+=$rowConsRate[csf("tot_cons")]*$edata[18];
				}
				if($rowConsRate[csf("type")]==2 && $rowConsRate[csf("particular_type_id")]==3)
					$yarn_dtls_arr[$rowConsRate[csf("mst_id")]]['washamt']+=$rowConsRate[csf("value")];
				if($rowConsRate[csf("type")]==2 && $rowConsRate[csf("particular_type_id")]==1)
					$yarn_dtls_arr[$rowConsRate[csf("mst_id")]]['printamt']+=$rowConsRate[csf("value")];
				if($rowConsRate[csf("type")]==2 && $rowConsRate[csf("particular_type_id")]==2)
					$yarn_dtls_arr[$rowConsRate[csf("mst_id")]]['embamt']+=$rowConsRate[csf("value")];
				if($rowConsRate[csf("type")]==2 && $rowConsRate[csf("particular_type_id")]==4)
					$yarn_dtls_arr[$rowConsRate[csf("mst_id")]]['spcamt']+=$rowConsRate[csf("value")];
				if($rowConsRate[csf("type")]==2 && $rowConsRate[csf("particular_type_id")]==99)
					$yarn_dtls_arr[$rowConsRate[csf("mst_id")]]['othamt']+=$rowConsRate[csf("value")];
				if($rowConsRate[csf("type")]==3)
					$yarn_dtls_arr[$rowConsRate[csf("mst_id")]]['trimsamt']+=$rowConsRate[csf("value")];
				
			}
			$sql_item_summ="select mst_id, item_id, fabric_cost, sp_operation_cost, accessories_cost, smv, efficiency, cm_cost, frieght_cost, lab_test_cost, miscellaneous_cost, other_cost, commission_cost, fob_pcs from qc_item_cost_summary where status_active=1 and is_deleted=0";
            $sql_result_item_summ=sql_select($sql_item_summ);
            foreach($sql_result_item_summ as $rowItemSumm)
            {
				$yarn_dtls_arr[$rowItemSumm[csf("mst_id")]]['commisamt']+=$rowItemSumm[csf("commission_cost")];
				$yarn_dtls_arr[$rowItemSumm[csf("mst_id")]]['testamt']+=$rowItemSumm[csf("lab_test_cost")];
				$yarn_dtls_arr[$rowItemSumm[csf("mst_id")]]['frightamt']+=$rowItemSumm[csf("frieght_cost")];
				$yarn_dtls_arr[$rowItemSumm[csf("mst_id")]]['cmamt']+=$rowItemSumm[csf("cm_cost")];
				$yarn_dtls_arr[$rowItemSumm[csf("mst_id")]]['smvamt']+=$rowItemSumm[csf("smv")];
			}
			//print_r($yarn_dtls_arr); die;
			

			$pri_fab_arr=sql_select("select a.quotation_id,b.fabric_source, a.rate, (a.requirment) as requirment, (a.pcs) as pcs from wo_pri_quo_fab_co_avg_con_dtls a,wo_pri_quo_fabric_cost_dtls  b where a.wo_pri_quo_fab_co_dtls_id=b.id and a.quotation_id=b.quotation_id  and b.status_active=1 and b.is_deleted=0 and b.fabric_source=2");
			foreach($pri_fab_arr as $p_row_pre)
			{
				$p_fab_precost_arr[$p_row_pre[csf('quotation_id')]].=$p_row_pre[csf('requirment')]."**".$p_row_pre[csf('pcs')]."**".$p_row_pre[csf('rate')]."**".$p_row_pre[csf('fabric_source')].",";	
			}
			$fabricDataArray=sql_select("select a.job_no, a.fab_nature_id, a.color_type_id, a.fabric_source, a.rate, b.yarn_cons_qnty, b.yarn_amount from wo_pre_cost_fabric_cost_dtls a, wo_pre_cost_sum_dtls b where a.job_no=b.job_no and a.fabric_source!=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			foreach($fabricDataArray as $fabricRow)
			{
				$fabric_data_Array[$fabricRow[csf('job_no')]].=$fabricRow[csf('fab_nature_id')]."**".$fabricRow[csf('fabric_source')]."**".$fabricRow[csf('rate')]."**".$fabricRow[csf('yarn_cons_qnty')]."**".$fabricRow[csf('yarn_amount')]."**".$fabricRow[csf('color_type_id')].",";
			} //Pre cost end
			unset($fabricDataArray);
			//var_dump($fabric_data_Array);
			$price_costDataArray=sql_select("select  id,costing_per,sew_smv  from wo_price_quotation where status_active=1 and is_deleted=0  ");
			foreach($price_costDataArray as $pri_fabRow)
			{
				$price_costing_perArray[$pri_fabRow[csf('id')]]['costing_per']=$pri_fabRow[csf('costing_per')];
				$price_costing_perArray[$pri_fabRow[csf('id')]]['smv']=$pri_fabRow[csf('sew_smv')];
			}
			//var_dump($price_costing_perArray);
			$sql_yarn_price=sql_select("select  rate, amount,cons_qnty,quotation_id from wo_pri_quo_fab_yarn_cost_dtls a where status_active=1");
			foreach($sql_yarn_price as $fabricRow)
			{
				$price_yarn_data_Array[$fabricRow[csf('quotation_id')]]['amount']+=$fabricRow[csf('amount')];
				$price_yarn_data_Array[$fabricRow[csf('quotation_id')]]['cons_qnty']+=$fabricRow[csf('cons_qnty')];
			}
			
			$price_fabricDataArray=sql_select("select a.quotation_id, a.fab_nature_id, a.color_type_id, a.fabric_source, a.rate, b.yarn_cons_qnty, b.yarn_amount from wo_pri_quo_fabric_cost_dtls a, wo_pri_quo_sum_dtls b where a.quotation_id=b.quotation_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			foreach($price_fabricDataArray as $price_fabricRow)
			{
				$yarn_amount=$price_yarn_data_Array[$price_fabricRow[csf('quotation_id')]]['amount'];
				$yarn_cons_qnty=$price_yarn_data_Array[$price_fabricRow[csf('quotation_id')]]['cons_qnty'];
				
				$price_fabric_data_Array[$price_fabricRow[csf('quotation_id')]].=$price_fabricRow[csf('fab_nature_id')]."**".$price_fabricRow[csf('fabric_source')]."**".$price_fabricRow[csf('rate')]."**".$yarn_cons_qnty."**".$yarn_amount."**".$price_fabricRow[csf('color_type_id')].",";
			} 
			$price_data_array_emb=("select  quotation_id,
			sum(CASE WHEN emb_name=1 THEN amount END) AS print_amount,
			sum(CASE WHEN emb_name=2 THEN amount END) AS embroidery_amount,
			sum(CASE WHEN emb_name=3 THEN amount END) AS wash_amount,
			sum(CASE WHEN emb_name=4 THEN amount END) AS special_amount,
			sum(CASE WHEN emb_name=5 THEN amount END) AS other_amount
			from  wo_pri_quo_embe_cost_dtls where  status_active=1 and is_deleted=0 group by quotation_id");
			$sql_embl_array=sql_select($price_data_array_emb);
			foreach($sql_embl_array as $p_row_emb)
			{
				$price_fab_emb[$p_row_emb[csf('quotation_id')]]['print']=$p_row_emb[csf('print_amount')];
				$price_fab_emb[$p_row_emb[csf('quotation_id')]]['embroidery']=$p_row_emb[csf('embroidery_amount')];
				$price_fab_emb[$p_row_emb[csf('quotation_id')]]['special']=$p_row_emb[csf('special_amount')];
				$price_fab_emb[$p_row_emb[csf('quotation_id')]]['other']=$p_row_emb[csf('other_amount')];
				$price_fab_emb[$p_row_emb[csf('quotation_id')]]['wash']=$p_row_emb[csf('wash_amount')];
			} 
			//var_dump($price_fab_emb);
			
			$fabriccostDataArray=sql_select("select job_no, costing_per_id, trims_cost, embel_cost, cm_cost, commission, common_oh, lab_test, inspection, freight, comm_cost, certificate_pre_cost, currier_pre_cost from wo_pre_cost_dtls where status_active=1 and is_deleted=0");
			foreach($fabriccostDataArray as $fabRow)
			{
				$fabriccostArray[$fabRow[csf('job_no')]]['costing_per_id']=$fabRow[csf('costing_per_id')];
				$fabriccostArray[$fabRow[csf('job_no')]]['trims_cost']=$fabRow[csf('trims_cost')];
				$fabriccostArray[$fabRow[csf('job_no')]]['embel_cost']=$fabRow[csf('embel_cost')];
				$fabriccostArray[$fabRow[csf('job_no')]]['cm_cost']=$fabRow[csf('cm_cost')];
				$fabriccostArray[$fabRow[csf('job_no')]]['commission']=$fabRow[csf('commission')];
				$fabriccostArray[$fabRow[csf('job_no')]]['common_oh']=$fabRow[csf('common_oh')];
				$fabriccostArray[$fabRow[csf('job_no')]]['lab_test']=$fabRow[csf('lab_test')];
				$fabriccostArray[$fabRow[csf('job_no')]]['inspection']=$fabRow[csf('inspection')];
				$fabriccostArray[$fabRow[csf('job_no')]]['freight']=$fabRow[csf('freight')];
				$fabriccostArray[$fabRow[csf('job_no')]]['comm_cost']=$fabRow[csf('comm_cost')];
				$fabriccostArray[$fabRow[csf('job_no')]]['certificate_pre_cost']=$fabRow[csf('certificate_pre_cost')];
				$fabriccostArray[$fabRow[csf('job_no')]]['currier_pre_cost']=$fabRow[csf('currier_pre_cost')];
				$fabriccostArray[$fabRow[csf('job_no')]]['c_cost']=$fabRow[csf('cm_cost')];
			} //pre cost trim emb, commission and others end
			//var_dump($fabriccostArray);
			$price_fabriccostArray=array();
			$p_fabriccostDataArray=sql_select("select quotation_id, costing_per_id, trims_cost, embel_cost, cm_cost, commission, common_oh, lab_test, inspection, freight, comm_cost, certificate_pre_cost, currier_pre_cost from wo_price_quotation_costing_mst where status_active=1 and is_deleted=0");
			foreach($p_fabriccostDataArray as $p_fabRow)
			{
				$price_fabriccostArray[$p_fabRow[csf('quotation_id')]]['costing_per_id']=$p_fabRow[csf('costing_per_id')];
				$price_fabriccostArray[$p_fabRow[csf('quotation_id')]]['trims_cost']=$p_fabRow[csf('trims_cost')];
				$price_fabriccostArray[$p_fabRow[csf('quotation_id')]]['embel_cost']=$p_fabRow[csf('embel_cost')];
				$price_fabriccostArray[$p_fabRow[csf('quotation_id')]]['cm_cost']=$p_fabRow[csf('cm_cost')];
				$price_fabriccostArray[$p_fabRow[csf('quotation_id')]]['commission']=$p_fabRow[csf('commission')];
				$price_fabriccostArray[$p_fabRow[csf('quotation_id')]]['common_oh']=$p_fabRow[csf('common_oh')];
				$price_fabriccostArray[$p_fabRow[csf('quotation_id')]]['lab_test']=$p_fabRow[csf('lab_test')];
				$price_fabriccostArray[$p_fabRow[csf('quotation_id')]]['inspection']=$p_fabRow[csf('inspection')];
				$price_fabriccostArray[$p_fabRow[csf('quotation_id')]]['freight']=$p_fabRow[csf('freight')];
				$price_fabriccostArray[$p_fabRow[csf('quotation_id')]]['comm_cost']=$p_fabRow[csf('comm_cost')];
				$price_fabriccostArray[$p_fabRow[csf('quotation_id')]]['certificate_pre_cost']=$p_fabRow[csf('certificate_pre_cost')];
				$price_fabriccostArray[$p_fabRow[csf('quotation_id')]]['currier_pre_cost']=$p_fabRow[csf('currier_pre_cost')];
				$price_fabriccostArray[$p_fabRow[csf('quotation_id')]]['c_cost']=$p_fabRow[csf('cm_cost')];
			}
			
			$pq_knit_data=sql_select("select quotation_id,
			sum(CASE WHEN cons_type=1 THEN amount END) AS knit_charge,
			sum(CASE WHEN cons_type=2 THEN amount END) AS weaving_charge,
			sum(CASE WHEN cons_type=3 THEN amount END) AS knit_charge_collar_cuff,
			sum(CASE WHEN cons_type=4 THEN amount END) AS knit_charge_feeder_stripe,
			sum(CASE WHEN cons_type in(140,142,148,64) THEN amount END) AS washing_cost,
			sum(CASE WHEN cons_type in(35,36,37,40) THEN amount END) AS all_over_cost,
			sum(CASE WHEN cons_type=30 THEN amount END) AS yarn_dyeing_cost,
			sum(CASE WHEN cons_type=33 THEN amount END) AS heat_setting_cost,
			sum(CASE WHEN cons_type in(25,31,26,32,60,61,62,63,72,80,81,84,85,86,87,38,39,74,78,79,101,133,137,138,139,146,147,149) THEN amount END) AS fabric_dyeing_cost,
			sum(CASE WHEN cons_type in(33,34,38,63,65,66,67,68,69,70,71,72,73,75,76,77,88,82,89,90,91,92,93,94,128,129,135,136,141,143,150,151,155,156,157,145,82,89,132,144) THEN amount END) AS fabric_finish_cost
			from wo_pri_quo_fab_conv_cost_dtls where  status_active=1  and is_deleted=0 group by quotation_id");
			foreach($pq_knit_data as $p_row_knit)
			{
				$pq_knit_arr[$p_row_knit[csf('quotation_id')]]['knit']=$p_row_knit[csf('knit_charge')];
				$pq_knit_arr[$p_row_knit[csf('quotation_id')]]['weaving']=$p_row_knit[csf('weaving_charge')];
				$pq_knit_arr[$p_row_knit[csf('quotation_id')]]['collar_cuff']=$p_row_knit[csf('knit_charge_collar_cuff')];
				$pq_knit_arr[$p_row_knit[csf('quotation_id')]]['feeder_stripe']=$p_row_knit[csf('knit_charge_feeder_stripe')];
				$pq_knit_arr[$p_row_knit[csf('quotation_id')]]['washing']=$p_row_knit[csf('washing_cost')];
				$pq_knit_arr[$p_row_knit[csf('quotation_id')]]['all_over']=$p_row_knit[csf('all_over_cost')];
				$pq_knit_arr[$p_row_knit[csf('quotation_id')]]['fabric_dyeing']=$p_row_knit[csf('fabric_dyeing_cost')];
				$pq_knit_arr[$p_row_knit[csf('quotation_id')]]['yarn_dyeing']=$p_row_knit[csf('yarn_dyeing_cost')];	
				$pq_knit_arr[$p_row_knit[csf('quotation_id')]]['heat']=$p_row_knit[csf('heat_setting_cost')];
				$pq_knit_arr[$p_row_knit[csf('quotation_id')]]['fabric_finish']=$p_row_knit[csf('fabric_finish_cost')];	
			} 
			
						
			$p_data_array=sql_select("select quotation_id,
			sum(CASE WHEN particulars_id=1 THEN commission_amount END) AS foreign_comm,
			sum(CASE WHEN particulars_id=2 THEN commission_amount END) AS local_comm
			from  wo_pri_quo_commiss_cost_dtls where status_active=1 and  is_deleted=0 group by quotation_id");// quotation_id='$data'
			foreach($p_data_array as $p_row_fl )
			{
				$price_commission_array[$p_row_fl[csf('quotation_id')]]['foreign']=$p_row_fl[csf('foreign_comm')];
				$price_commission_array[$p_row_fl[csf('quotation_id')]]['local']=$p_row_fl[csf('local_comm')];
			}
		
			?>
			
		<script>
				var order_value=document.getElementById('p_total_order_value').innerHTML.replace(/,/g,'');
				document.getElementById('yarn_qut_cost').innerHTML=document.getElementById('p_total_yarn_costing').innerHTML;
				document.getElementById('yarn_qut_per').innerHTML=number_format_common((document.getElementById('p_total_yarn_costing').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('yarn_bud_cost').innerHTML=document.getElementById('total_yarn_costing').innerHTML;
				document.getElementById('yarn_bud_per').innerHTML=number_format_common((document.getElementById('total_yarn_costing').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('yarn_ver_cost').innerHTML=document.getElementById('v_total_yarn_costing').innerHTML;
				document.getElementById('yarn_ver_per').innerHTML=number_format_common((document.getElementById('v_total_yarn_costing').innerHTML.split(',').join('')/document.getElementById('p_total_yarn_costing').innerHTML.split(',').join(''))*100,2);
				
				document.getElementById('fab_qut_cost').innerHTML=document.getElementById('p_total_fab_purchase').innerHTML;
				document.getElementById('fab_qut_per').innerHTML=number_format_common((document.getElementById('p_total_fab_purchase').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('fab_bud_cost').innerHTML=document.getElementById('total_fab_purchase').innerHTML;
				document.getElementById('fab_bud_per').innerHTML=number_format_common((document.getElementById('total_fab_purchase').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('fab_ver_cost').innerHTML=document.getElementById('v_total_fab_purchase').innerHTML;
				document.getElementById('fab_ver_per').innerHTML=number_format_common((document.getElementById('v_total_fab_purchase').innerHTML.split(',').join('')/document.getElementById('p_total_fab_purchase').innerHTML.split(',').join(''))*100,2);
				
				document.getElementById('knit_qut_cost').innerHTML=document.getElementById('p_total_knit_cost').innerHTML;
				document.getElementById('knit_qut_per').innerHTML=number_format_common((document.getElementById('p_total_knit_cost').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('knit_bud_cost').innerHTML=document.getElementById('total_knit_cost').innerHTML;
				document.getElementById('knit_bud_per').innerHTML=number_format_common((document.getElementById('total_knit_cost').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('knit_ver_cost').innerHTML=document.getElementById('v_total_knit_cost').innerHTML;
				document.getElementById('knit_ver_per').innerHTML=number_format_common((document.getElementById('v_total_knit_cost').innerHTML.split(',').join('')/document.getElementById('p_total_knit_cost').innerHTML.split(',').join(''))*100,2);
				
				document.getElementById('ydye_qut_cost').innerHTML=document.getElementById('p_total_yarn_dyeing_cost').innerHTML;
				document.getElementById('ydye_qut_per').innerHTML=number_format_common((document.getElementById('p_total_yarn_dyeing_cost').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('ydye_bud_cost').innerHTML=document.getElementById('total_yarn_dyeing_cost').innerHTML;
				document.getElementById('ydye_bud_per').innerHTML=number_format_common((document.getElementById('total_yarn_dyeing_cost').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('ydye_ver_cost').innerHTML=document.getElementById('v_total_yarn_dyeing_cost').innerHTML;
				document.getElementById('ydye_ver_per').innerHTML=number_format_common((document.getElementById('v_total_yarn_dyeing_cost').innerHTML.split(',').join('')/document.getElementById('p_total_yarn_dyeing_cost').innerHTML.split(',').join(''))*100,2);
				
				document.getElementById('aop_qut_cost').innerHTML=document.getElementById('p_aop_td').innerHTML;
				document.getElementById('aop_qut_per').innerHTML=number_format_common((document.getElementById('p_aop_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('aop_bud_cost').innerHTML=document.getElementById('aop_td').innerHTML;
				document.getElementById('aop_bud_per').innerHTML=number_format_common((document.getElementById('aop_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('aop_ver_cost').innerHTML=document.getElementById('v_aop_td').innerHTML;
				document.getElementById('aop_ver_per').innerHTML=number_format_common((document.getElementById('v_aop_td').innerHTML.split(',').join('')/document.getElementById('p_aop_td').innerHTML.split(',').join(''))*100,2);
				
				var p_dyefin_val=(parseFloat(document.getElementById('p_fabric_dyeing_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('p_fabric_finishing_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('p_heat_setting_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('p_washing_td').innerHTML.split(',').join('')));
				var dyefin_val=(parseFloat(document.getElementById('fabric_dyeing_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('fabric_finishing_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('heat_setting_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('washing_td').innerHTML.split(',').join('')));
				var v_dyefin_val=(parseFloat(document.getElementById('v_fabric_dyeing_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('v_fabric_finishing_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('v_heat_setting_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('v_washing_td').innerHTML.split(',').join('')));
				document.getElementById('dyeFin_qut_cost').innerHTML=number_format_common(p_dyefin_val,2);
				document.getElementById('dyeFin_qut_per').innerHTML=number_format_common((p_dyefin_val/order_value)*100,2);
				document.getElementById('dyeFin_bud_cost').innerHTML=number_format_common(dyefin_val,2);
				document.getElementById('dyeFin_bud_per').innerHTML=number_format_common((dyefin_val/order_value)*100,2);
				document.getElementById('dyeFin_ver_cost').innerHTML=number_format_common(v_dyefin_val,2);
				document.getElementById('dyeFin_ver_per').innerHTML=number_format_common((v_dyefin_val/p_dyefin_val)*100,2);
				//var p_matService_val=document.getElementById('p_total_yarn_costing').innerHTML.split(',').join('');
				var p_matService_val=(parseFloat(document.getElementById('yarn_qut_cost').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('fab_qut_cost').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('knit_qut_cost').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('ydye_qut_cost').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('aop_qut_cost').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('dyeFin_qut_cost').innerHTML.split(',').join('')));
				var matService_val=(parseFloat(document.getElementById('yarn_bud_cost').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('fab_bud_cost').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('knit_bud_cost').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('ydye_bud_cost').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('aop_bud_cost').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('dyeFin_bud_cost').innerHTML.split(',').join('')));
				
				var v_matService_val=(parseFloat(document.getElementById('yarn_ver_cost').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('fab_ver_cost').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('knit_ver_cost').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('ydye_ver_cost').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('aop_ver_cost').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('dyeFin_ver_cost').innerHTML.split(',').join('')));
				document.getElementById('matService_qut_cost').innerHTML=number_format_common(p_matService_val,2);
				document.getElementById('matService_qut_per').innerHTML=number_format_common((p_matService_val/order_value)*100,2);
				document.getElementById('matService_bud_cost').innerHTML=number_format_common(matService_val,2);
				document.getElementById('matService_bud_per').innerHTML=number_format_common((matService_val/order_value)*100,2);
				document.getElementById('matService_ver_cost').innerHTML=number_format_common(v_matService_val,2);
				document.getElementById('matService_ver_per').innerHTML=number_format_common((v_matService_val/p_matService_val)*100,2)
				
				document.getElementById('trim_qut_cost').innerHTML=document.getElementById('p_trim_amount_td').innerHTML;
				document.getElementById('trim_qut_per').innerHTML=number_format_common((document.getElementById('p_trim_amount_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('trim_bud_cost').innerHTML=document.getElementById('trim_amount_td').innerHTML;
				document.getElementById('trim_bud_per').innerHTML=number_format_common((document.getElementById('trim_amount_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('trim_ver_cost').innerHTML=document.getElementById('v_trim_amount_td').innerHTML;
				document.getElementById('trim_ver_per').innerHTML=number_format_common((document.getElementById('v_trim_amount_td').innerHTML.split(',').join('')/document.getElementById('p_trim_amount_td').innerHTML.split(',').join(''))*100,2);
				
				var p_embel_val=(parseFloat(document.getElementById('p_print_amount_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('p_embroidery_amount_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('p_wash_amt_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('p_other_amount_td').innerHTML.split(',').join('')));
				var embel_val=(parseFloat(document.getElementById('print_amount_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('embroidery_amount_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('wash_amt_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('other_amount_td').innerHTML.split(',').join('')));
				
				
				var v_embel_val=(parseFloat(document.getElementById('v_print_amount_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('v_embroidery_amount_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('v_wash_amt_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('v_other_amount_td').innerHTML.split(',').join('')));
				
				
				document.getElementById('embl_qut_cost').innerHTML=number_format_common(p_embel_val,2);
				document.getElementById('embl_qut_per').innerHTML=number_format_common((p_embel_val/order_value)*100,2);
				document.getElementById('embl_bud_cost').innerHTML=number_format_common(embel_val,2);
				document.getElementById('embl_bud_per').innerHTML=number_format_common((embel_val/order_value)*100,2);
				document.getElementById('embl_ver_cost').innerHTML=number_format_common(v_embel_val,2);
				document.getElementById('embl_ver_per').innerHTML=number_format_common((v_embel_val/p_embel_val)*100,2)
				
				document.getElementById('commercial_qut_cost').innerHTML=document.getElementById('p_commercial_cost_td').innerHTML;
				document.getElementById('commercial_qut_per').innerHTML=number_format_common((document.getElementById('p_commercial_cost_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('commercial_bud_cost').innerHTML=document.getElementById('commercial_cost_td').innerHTML;
				document.getElementById('commercial_bud_per').innerHTML=number_format_common((document.getElementById('commercial_cost_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('commercial_ver_cost').innerHTML=document.getElementById('v_commercial_cost_td').innerHTML;
				document.getElementById('commercial_ver_per').innerHTML=number_format_common((document.getElementById('v_commercial_cost_td').innerHTML.split(',').join('')/document.getElementById('p_commercial_cost_td').innerHTML.split(',').join(''))*100,2);
				var p_commision_val=(parseFloat(document.getElementById('p_foreign_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('p_local_td').innerHTML.split(',').join('')));
				var commision_val=(parseFloat(document.getElementById('foreign_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('local_td').innerHTML.split(',').join('')));
				var v_commision_val=(parseFloat(document.getElementById('v_foreign_td').innerHTML.split(',').join(''))) + (parseFloat(document.getElementById('v_local_td').innerHTML.split(',').join('')));
				
				document.getElementById('commision_qut_cost').innerHTML=number_format_common(p_commision_val,2);
				document.getElementById('commision_qut_per').innerHTML=number_format_common((p_commision_val/order_value)*100,2);
				document.getElementById('commision_bud_cost').innerHTML=number_format_common(commision_val,2);
				document.getElementById('commision_bud_per').innerHTML=number_format_common((commision_val/order_value)*100,2);
				document.getElementById('commision_ver_cost').innerHTML=number_format_common(v_commision_val,2);
				document.getElementById('commision_ver_per').innerHTML=number_format_common((v_commision_val/p_commision_val)*100,2);
				
				document.getElementById('testing_qut_cost').innerHTML=document.getElementById('p_test_cost_td').innerHTML;
				document.getElementById('testing_qut_per').innerHTML=number_format_common((document.getElementById('p_test_cost_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('testing_bud_cost').innerHTML=document.getElementById('test_cost_td').innerHTML;
				document.getElementById('testing_bud_per').innerHTML=number_format_common((document.getElementById('test_cost_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('testing_ver_cost').innerHTML=document.getElementById('v_test_cost_td').innerHTML;
				document.getElementById('testing_ver_per').innerHTML=number_format_common((document.getElementById('v_test_cost_td').innerHTML.split(',').join('')/document.getElementById('p_test_cost_td').innerHTML.split(',').join(''))*100,2);
				
				document.getElementById('freight_qut_cost').innerHTML=document.getElementById('p_freight_cost_td').innerHTML;
				document.getElementById('freight_qut_per').innerHTML=number_format_common((document.getElementById('p_freight_cost_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('freight_bud_cost').innerHTML=document.getElementById('freight_cost_td').innerHTML;
				document.getElementById('freight_bud_per').innerHTML=number_format_common((document.getElementById('freight_cost_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('freight_ver_cost').innerHTML=document.getElementById('v_freight_cost_td').innerHTML;
				document.getElementById('freight_ver_per').innerHTML=number_format_common((document.getElementById('v_freight_cost_td').innerHTML.split(',').join('')/document.getElementById('p_freight_cost_td').innerHTML.split(',').join(''))*100,2);
				document.getElementById('insp_qut_cost').innerHTML=document.getElementById('p_inspection_cost_td').innerHTML;
				document.getElementById('insp_qut_per').innerHTML=number_format_common((document.getElementById('p_inspection_cost_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('insp_bud_cost').innerHTML=document.getElementById('inspection_cost_td').innerHTML;
				document.getElementById('insp_bud_per').innerHTML=number_format_common((document.getElementById('inspection_cost_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('insp_ver_cost').innerHTML=document.getElementById('v_inspection_cost_td').innerHTML;
				document.getElementById('insp_ver_per').innerHTML=number_format_common((document.getElementById('v_inspection_cost_td').innerHTML.split(',').join('')/document.getElementById('p_inspection_cost_td').innerHTML.split(',').join(''))*100,2);
				document.getElementById('certi_qut_cost').innerHTML=document.getElementById('p_certificate_cost_td').innerHTML;
				document.getElementById('certi_qut_per').innerHTML=number_format_common((document.getElementById('p_certificate_cost_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('certi_bud_cost').innerHTML=document.getElementById('certificate_cost_td').innerHTML;
				document.getElementById('certi_bud_per').innerHTML=number_format_common((document.getElementById('certificate_cost_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('certi_ver_cost').innerHTML=document.getElementById('v_certificate_cost_td').innerHTML;
				document.getElementById('certi_ver_per').innerHTML=number_format_common((document.getElementById('v_certificate_cost_td').innerHTML.split(',').join('')/document.getElementById('p_certificate_cost_td').innerHTML.split(',').join(''))*100,2);
				document.getElementById('operating_qut_cost').innerHTML=document.getElementById('p_operating_exp_td').innerHTML;
				document.getElementById('operating_qut_per').innerHTML=number_format_common((document.getElementById('p_operating_exp_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('operating_bud_cost').innerHTML=document.getElementById('operating_exp_td').innerHTML;
				document.getElementById('operating_bud_per').innerHTML=number_format_common((document.getElementById('operating_exp_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('operating_ver_cost').innerHTML=document.getElementById('v_operating_exp_td').innerHTML;
				document.getElementById('operating_ver_per').innerHTML=number_format_common((document.getElementById('v_operating_exp_td').innerHTML.split(',').join('')/document.getElementById('p_operating_exp_td').innerHTML.split(',').join(''))*100,2);
				document.getElementById('courier_qut_cost').innerHTML=document.getElementById('p_currier_cost_td').innerHTML;
				document.getElementById('courier_qut_per').innerHTML=number_format_common((document.getElementById('p_currier_cost_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('courier_bud_cost').innerHTML=document.getElementById('currier_cost_td').innerHTML;
				document.getElementById('courier_bud_per').innerHTML=number_format_common((document.getElementById('currier_cost_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('courier_ver_cost').innerHTML=document.getElementById('v_currier_cost_td').innerHTML;
				document.getElementById('courier_ver_per').innerHTML=number_format_common((document.getElementById('v_currier_cost_td').innerHTML.split(',').join('')/document.getElementById('p_currier_cost_td').innerHTML.split(',').join(''))*100,2);
				document.getElementById('cm_qut_cost').innerHTML=document.getElementById('p_cm_cost_td').innerHTML;
				document.getElementById('cm_qut_per').innerHTML=number_format_common((document.getElementById('p_cm_cost_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('cm_bud_cost').innerHTML=document.getElementById('cm_cost_td').innerHTML;
				document.getElementById('cm_bud_per').innerHTML=number_format_common((document.getElementById('cm_cost_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('cm_ver_cost').innerHTML=document.getElementById('v_cm_cost_td').innerHTML;
				document.getElementById('cm_ver_per').innerHTML=number_format_common((document.getElementById('v_cm_cost_td').innerHTML.split(',').join('')/document.getElementById('p_cm_cost_td').innerHTML.split(',').join(''))*100,2);
				
				document.getElementById('cost_qut_cost').innerHTML=document.getElementById('p_tot_cost_td').innerHTML;
				document.getElementById('cost_qut_per').innerHTML=number_format_common((document.getElementById('p_tot_cost_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('cost_bud_cost').innerHTML=document.getElementById('tot_cost_td').innerHTML;
				document.getElementById('cost_bud_per').innerHTML=number_format_common((document.getElementById('tot_cost_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('cost_ver_cost').innerHTML=document.getElementById('v_tot_cost_td').innerHTML;
				document.getElementById('cost_ver_per').innerHTML=number_format_common((document.getElementById('v_tot_cost_td').innerHTML.split(',').join('')/document.getElementById('p_tot_cost_td').innerHTML.split(',').join(''))*100,2);
				
				document.getElementById('ordVal_qut_cost').innerHTML=document.getElementById('p_total_order_value').innerHTML;
				document.getElementById('ordVal_qut_per').innerHTML=number_format_common((document.getElementById('p_total_order_value').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('ordVal_bud_cost').innerHTML=document.getElementById('total_order_amount2').innerHTML;
				document.getElementById('ordVal_bud_per').innerHTML=number_format_common((document.getElementById('total_order_amount2').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('ordVal_ver_cost').innerHTML=document.getElementById('total_order_amount2').innerHTML;
				document.getElementById('ordVal_ver_per').innerHTML=number_format_common((document.getElementById('total_order_amount2').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('proLoss_qut_cost').innerHTML=document.getElementById('p_profitt_td').innerHTML;
				document.getElementById('proLoss_qut_per').innerHTML=number_format_common((document.getElementById('p_profitt_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('proLoss_bud_cost').innerHTML=document.getElementById('profitt_td').innerHTML;
				document.getElementById('proLoss_bud_per').innerHTML=number_format_common((document.getElementById('profitt_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('proLoss_ver_cost').innerHTML=document.getElementById('v_profitt_td').innerHTML;
				document.getElementById('proLoss_ver_per').innerHTML=number_format_common((document.getElementById('v_profitt_td').innerHTML.split(',').join('')/document.getElementById('p_profitt_td').innerHTML.split(',').join(''))*100,2);
				
				document.getElementById('expPro_qut_cost').innerHTML=number_format_common(document.getElementById('p_expProfitt_td').innerHTML,2);
				document.getElementById('expPro_qut_per').innerHTML=number_format_common((document.getElementById('p_expProfitt_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('expPro_bud_cost').innerHTML=number_format_common(document.getElementById('expProfitt_td').innerHTML,2);
				document.getElementById('expPro_bud_per').innerHTML=number_format_common((document.getElementById('expProfitt_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('expPro_ver_cost').innerHTML=number_format_common(document.getElementById('v_expProfitt_td').innerHTML,2);
				document.getElementById('expPro_ver_per').innerHTML=number_format_common((document.getElementById('v_expProfitt_td').innerHTML.split(',').join('')/document.getElementById('p_expProfitt_td').innerHTML.split(',').join(''))*100,2);
				
				document.getElementById('expProv_qut_cost').innerHTML=document.getElementById('p_expect_variance_td').innerHTML;
				document.getElementById('expProv_qut_per').innerHTML=number_format_common((document.getElementById('p_expect_variance_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('expProv_bud_cost').innerHTML=document.getElementById('expect_variance_td').innerHTML;
				document.getElementById('expProv_bud_per').innerHTML=number_format_common((document.getElementById('expect_variance_td').innerHTML.split(',').join('')/order_value)*100,2);
				document.getElementById('exp_prof_div').text=number_format_common((document.getElementById('p_expProfitt_td').innerHTML.split(',').join('')/order_value)*100,2);
				//document.getElementById('exp_prof_div').text=
				$('#exp_prof_div').text(number_format_common((document.getElementById('p_expProfitt_td').innerHTML.split(',').join('')/order_value)*100)); 
				 
				
                function toggle() 
                {
                    var ele = document.getElementById("yarn_summary");
                    
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
            
            
            
            <?
            	ob_start();
			?>
            <div style="width:4870px;">
            <div style="width:900px;" align="left">
                <table width="900" cellpadding="0" cellspacing="2" border="0">
                    <tr>
                        <td width="650" align="left">
                            <table width="600" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="2">
                                <thead align="center">
                                    <tr><th colspan="8">Spot Costing Vs Budget Variance Summary</th></tr>
                                    <tr>
                                        <th width="20">SL</th>
                                        <th width="140">Particulars</th>
                                        <th width="110">Mkt. Cost</th>
                                        <th width="80">% On Order Value</th>
                                        <th width="110">Budgeted Cost</th>
                                        <th width="80">% On Order Value</th>
                                        <th width="100">Variance</th>
                                        <th>% On Mkt. Cost</th>
                                    </tr>
                                </thead>
                                <tr bgcolor="<?  echo $style1; ?>">
                                    <td>1</td><td>Yarn Cost</td>
                                    <td align="right" id="yarn_qut_cost"></td>
                                    <td align="right" id="yarn_qut_per"></td>
                                    <td align="right" id="yarn_bud_cost"></td>
                                    <td align="right" id="yarn_bud_per"></td>
                                    <td align="right" id="yarn_ver_cost"></td>
                                    <td align="right" id="yarn_ver_per"></td>
                                </tr>
                                <tr bgcolor="<?  echo $style; ?>">
                                    <td>2</td><td>Fabric Purchase</td>
                                    <td align="right" id="fab_qut_cost"></td>
                                    <td align="right" id="fab_qut_per"></td>
                                    <td align="right" id="fab_bud_cost"></td>
                                    <td align="right" id="fab_bud_per"></td>
                                    <td align="right" id="fab_ver_cost"></td>
                                    <td align="right" id="fab_ver_per"></td>
                                </tr>
                                <tr bgcolor="<?  echo $style1; ?>">
                                    <td>3</td><td>Knitting Cost</td>
                                    <td align="right" id="knit_qut_cost"></td>
                                    <td align="right" id="knit_qut_per"></td>
                                    <td align="right" id="knit_bud_cost"></td>
                                    <td align="right" id="knit_bud_per"></td>
                                    <td align="right" id="knit_ver_cost"></td>
                                    <td align="right" id="knit_ver_per"></td>
                                </tr>
                                <tr bgcolor="<?  echo $style; ?>">
                                    <td>4</td><td>Yarn Dyeing Cost</td>
                                    <td align="right" id="ydye_qut_cost"></td>
                                    <td align="right" id="ydye_qut_per"></td>
                                    <td align="right" id="ydye_bud_cost"></td>
                                    <td align="right" id="ydye_bud_per"></td>
                                    <td align="right" id="ydye_ver_cost"></td>
                                    <td align="right" id="ydye_ver_per"></td>
                                </tr>
                                <tr bgcolor="<?  echo $style1; ?>">
                                    <td>5</td><td>AOP Cost</td>
                                    <td align="right" id="aop_qut_cost"></td>
                                    <td align="right" id="aop_qut_per"></td>
                                    <td align="right" id="aop_bud_cost"></td>
                                    <td align="right" id="aop_bud_per"></td>
                                    <td align="right" id="aop_ver_cost"></td>
                                    <td align="right" id="aop_ver_per"></td>
                                </tr>
                                <tr bgcolor="<?  echo $style; ?>">
                                    <td>6</td><td>Dyeing & Finishing Cost</td>
                                    <td align="right" id="dyeFin_qut_cost"></td>
                                    <td align="right" id="dyeFin_qut_per"></td>
                                    <td align="right" id="dyeFin_bud_cost"></td>
                                    <td align="right" id="dyeFin_bud_per"></td>
                                    <td align="right" id="dyeFin_ver_cost"></td>
                                    <td align="right" id="dyeFin_ver_per"></td>
                                </tr>
                                <tr bgcolor="#CCCCCC">
                                    <td colspan="2"><strong>Total Material & Service Cost</strong></td>
                                    <td align="right" id="matService_qut_cost"></td>
                                    <td align="right" id="matService_qut_per"></td>
                                    <td align="right" id="matService_bud_cost"></td>
                                    <td align="right" id="matService_bud_per"></td>
                                    <td align="right" id="matService_ver_cost"></td>
                                    <td align="right" id="matService_ver_per"></td>
                                </tr>
                                <tr bgcolor="<?  echo $style1; ?>">
                                    <td>7</td><td>Trims Cost</td>
                                    <td align="right" id="trim_qut_cost"></td>
                                    <td align="right" id="trim_qut_per"></td>
                                    <td align="right" id="trim_bud_cost"></td>
                                    <td align="right" id="trim_bud_per"></td>
                                    <td align="right" id="trim_ver_cost"></td>
                                    <td align="right" id="trim_ver_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>8</td><td>Print/ Emb./ Wash Cost</td>
                                    <td align="right" id="embl_qut_cost"></td>
                                    <td align="right" id="embl_qut_per"></td>
                                    <td align="right" id="embl_bud_cost"></td>
                                    <td align="right" id="embl_bud_per"></td>
                                    <td align="right" id="embl_ver_cost"></td>
                                    <td align="right" id="embl_ver_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>9</td><td>Commercial Cost</td>
                                    <td align="right" id="commercial_qut_cost"></td>
                                    <td align="right" id="commercial_qut_per"></td>
                                    <td align="right" id="commercial_bud_cost"></td>
                                    <td align="right" id="commercial_bud_per"></td>
                                    <td align="right" id="commercial_ver_cost"></td>
                                    <td align="right" id="commercial_ver_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>10</td><td>Commision Cost</td>
                                    <td align="right" id="commision_qut_cost"></td>
                                    <td align="right" id="commision_qut_per"></td>
                                    <td align="right" id="commision_bud_cost"></td>
                                    <td align="right" id="commision_bud_per"></td>
                                    <td align="right" id="commision_ver_cost"></td>
                                    <td align="right" id="commision_ver_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>11</td><td>Testing Cost</td>
                                    <td align="right" id="testing_qut_cost"></td>
                                    <td align="right" id="testing_qut_per"></td>
                                    <td align="right" id="testing_bud_cost"></td>
                                    <td align="right" id="testing_bud_per"></td>
                                    <td align="right" id="testing_ver_cost"></td>
                                    <td align="right" id="testing_ver_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>12</td><td>Freight Cost</td>
                                    <td align="right" id="freight_qut_cost"></td>
                                    <td align="right" id="freight_qut_per"></td>
                                    <td align="right" id="freight_bud_cost"></td>
                                    <td align="right" id="freight_bud_per"></td>
                                    <td align="right" id="freight_ver_cost"></td>
                                    <td align="right" id="freight_ver_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>13</td><td>Inspection Cost</td>
                                    <td align="right" id="insp_qut_cost"></td>
                                    <td align="right" id="insp_qut_per"></td>
                                    <td align="right" id="insp_bud_cost"></td>
                                    <td align="right" id="insp_bud_per"></td>
                                    <td align="right" id="insp_ver_cost"></td>
                                    <td align="right" id="insp_ver_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>14</td><td>Certificate Cost</td>
                                    <td align="right" id="certi_qut_cost"></td>
                                    <td align="right" id="certi_qut_per"></td>
                                    <td align="right" id="certi_bud_cost"></td>
                                    <td align="right" id="certi_bud_per"></td>
                                    <td align="right" id="certi_ver_cost"></td>
                                    <td align="right" id="certi_ver_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>15</td><td>Operating Exp.</td>
                                    <td align="right" id="operating_qut_cost"></td>
                                    <td align="right" id="operating_qut_per"></td>
                                    <td align="right" id="operating_bud_cost"></td>
                                    <td align="right" id="operating_bud_per"></td>
                                    <td align="right" id="operating_ver_cost"></td>
                                    <td align="right" id="operating_ver_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>16</td><td>Courier Cost</td>
                                    <td align="right" id="courier_qut_cost"></td>
                                    <td align="right" id="courier_qut_per"></td>
                                    <td align="right" id="courier_bud_cost"></td>
                                    <td align="right" id="courier_bud_per"></td>
                                    <td align="right" id="courier_ver_cost"></td>
                                    <td align="right" id="courier_ver_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>17</td><td>CM Cost</td>
                                    <td align="right" id="cm_qut_cost"></td>
                                    <td align="right" id="cm_qut_per"></td>
                                    <td align="right" id="cm_bud_cost"></td>
                                    <td align="right" id="cm_bud_per"></td>
                                    <td align="right" id="cm_ver_cost"></td>
                                    <td align="right" id="cm_ver_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>18</td><td>Total Cost</td>
                                    <td align="right" id="cost_qut_cost"></td>
                                    <td align="right" id="cost_qut_per"></td>
                                    <td align="right" id="cost_bud_cost"></td>
                                    <td align="right" id="cost_bud_per"></td>
                                    <td align="right" id="cost_ver_cost"></td>
                                    <td align="right" id="cost_ver_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>19</td><td>Total Order Value</td>
                                    <td align="right" id="ordVal_qut_cost"></td>
                                    <td align="right" id="ordVal_qut_per"></td>
                                    <td align="right" id="ordVal_bud_cost"></td>
                                    <td align="right" id="ordVal_bud_per"></td>
                                    <td align="right" id="ordVal_ver_cost"></td>
                                    <td align="right" id="ordVal_ver_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>20</td><td>Profit/Loss </td>
                                    <td align="right" id="proLoss_qut_cost"></td>
                                    <td align="right" id="proLoss_qut_per"></td>
                                    <td align="right" id="proLoss_bud_cost"></td>
                                    <td align="right" id="proLoss_bud_per"></td>
                                    <td align="right" id="proLoss_ver_cost"></td>
                                    <td align="right" id="proLoss_ver_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style1; ?>">
                                    <td>21</td><td>Expected Profit <div id="exp_prof_div"></div>%</td>
                                    <td align="right" id="expPro_qut_cost"></td>
                                    <td align="right" id="expPro_qut_per"></td>
                                    <td align="right" id="expPro_bud_cost"></td>
                                    <td align="right" id="expPro_bud_per"></td>
                                    <td align="right" id="expPro_ver_cost"></td>
                                    <td align="right" id="expPro_ver_per"></td>
                                </tr>
                                <tr bgcolor="<? echo $style; ?>">
                                    <td>22</td><td>Expt.Profit Variance </td>
                                    <td align="right" id="expProv_qut_cost"></td>
                                    <td align="right" id="expProv_qut_per"></td>
                                    <td align="right" id="expProv_bud_cost"></td>
                                    <td align="right" id="expProv_bud_per"></td>
                                    <td align="right" id="expProv_ver_cost"></td>
                                    <td align="right" id="expProv_ver_per"></td>
                                </tr>
                            </table>
                        </td>
                        <td colspan="5" style="min-height:800px; max-height:100%" align="center" valign="top"></td>
                    </tr>
                </table>
            </div>
            <br/>
            <h3 align="left" id="accordion_h2" style="width:5310px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel2', '')"> -Search Panel</h3>
        	<fieldset style="width:100%;" id="content_search_panel2">	
            <table width="5310">
                <tr class="form_caption">
                    <td align="left">Decimal rounded to 2 digit.</td>
                    <td colspan="50" align="center"><strong>Order Wise Budget Report</strong></td>
                </tr>
                <tr class="form_caption">
                    <td colspan="50" align="center"><strong><? echo $company_library[$company_name]; ?></strong></td>
                </tr>
                <tr class="form_caption">
                        <td align="left"><strong>Details Report </strong></td>
                </tr>
            </table>
            <? $asking_profit_head=$asking_profit_arr[$company_name]['asking_profit']; 
				if(str_replace("'","",$cbo_search_date)==1) $head_cap="Ship. Date";
				else if(str_replace("'","",$cbo_search_date)==2) $head_cap="PO Recv. Date";
				else $head_cap="PO Insert Date";
			?>
            <table id="table_header_1" class="rpt_table" width="5310" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr>
                        <th width="40" rowspan="2">SL</th>
                        <th width="70" rowspan="2">Buyer</th>
                        <th width="70" rowspan="2">Job No</th>
                        <th width="100" rowspan="2">Order No</th>
                        <th width="100" rowspan="2">Ready To Approve
                        	
                             <select name="cbo_ready_to" id="cbo_ready_to" class="combo_boxes flt" style="width:95px" onChange="fn_report_generated2(4);">
                                    <option value='0'> All </option>
									<?
                                    foreach ($yes_no as $key => $value) {
                                        ?>
                                        <option value=<?
                                        echo $key;
                                        if ($key == $cbo_ready_to) {
                                            ?> selected <?php } ?>><? echo "$value" ?> </option>
                                                <?
                                            }
                                            ?>
                                </select> 
                        </th>
                        <th width="80" rowspan="2">File No</th>
                        <th width="80" rowspan="2">Internal Ref: No</th>
                        <th width="100" rowspan="2">Order Status</th>
                        <th width="110" rowspan="2">Style</th>
                        <th width="110" rowspan="2">Item Name</th>
                        <th width="110" rowspan="2">Dealing</th>
                        <th width="70" rowspan="2"><? echo $head_cap; ?></th>
                        <th width="90" rowspan="2">Order Qty</th>
                        <th width="90" rowspan="2">Avg Unit Price</th>
                        <th width="100" rowspan="2">Order Value</th>
                        <th width="100" rowspan="2">Particulars</th>
                        <th colspan="14">Fabric Cost</th>
                        <th width="100" rowspan="2">Trim Cost</th>
                        <th colspan="5">Embell. Cost</th>
                        <th width="120" rowspan="2">Commercial Cost</th>
                        <th colspan="2">Commission</th>
                        <th width="100" rowspan="2">Testing Cost</th>
                        <th width="100" rowspan="2">Freight Cost</th>
                        <th width="120" rowspan="2">Inspection Cost</th>
                        <th width="100" rowspan="2">Certificate Cost</th>
                        <th width="100" rowspan="2">Commn OH</th>
                        <th width="100" rowspan="2">Courier Cost</th>
                        <th width="120" rowspan="2">CM/DZN</th>
                        <th width="100" rowspan="2">CM Cost</th>
                        <th width="100" rowspan="2">Total Cost</th>
                        <th width="100" rowspan="2">Profit/Loss</th>
                        <th width="100" rowspan="2">Profit/Loss %</th>
                        <th width="100" rowspan="2">Expected Profit</th>
                        <th width="100" rowspan="2">Expected Profit %</th>
                        <th width="100" rowspan="2">Expt. Profit Variance</th>
                        <th width="100" rowspan="2">SMV</th>
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
            <div style="width:5350px; max-height:400px; overflow-y:scroll" id="scroll_body">
            <table class="rpt_table" width="5310" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
            <? 
			$i=1; $total_order_qty=0;  $total_yarn_dyeing_cost=0; $total_yarn_cost=0; $total_order_value=0;$total_purchase_cost=0; $grand_tot_trims_cost=0; $total_fabric_dyeing_cost=0; $total_knitting_cost=0; $total_heat_setting_cost=0;$total_finishing_cost=0; $total_washing_cost=0; $fabric_dyeing_cost_dzn=0; $other_cost=0; $all_over_print_cost=0; $total_trim_cost=0; $total_commercial_cost=0;$p_total_yarn_cons=0;$total_yarn_cons=0;
			$total_smv_price=0;$total_smv_pre=0;$total_smv_vari=0;
			if($cbo_ready_to==1 || $cbo_ready_to==2)
			{
				$sql="select a.job_no_prefix_num,a.set_smv, b.insert_date, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.is_confirmed, a.quotation_id, a.agent_name, a.avg_unit_price, a.dealing_marchant, a.gmts_item_id, a.total_set_qnty as ratio, b.plan_cut, b.id as po_id, b.po_number, b.pub_shipment_date, b.po_received_date, b.po_quantity, b.unit_price, b.grouping, b.file_no, b.po_total_price from  wo_po_break_down b, wo_po_details_master a , wo_pre_cost_mst c   where a.job_no=b.job_no_mst and  a.job_no=c.job_no and c.job_no=b.job_no_mst and c.entry_from=158  and a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 $cbo_ready_to_cond $date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond $season_cond $internal_ref_cond $file_no_cond  order by b.pub_shipment_date, b.id";
			}
			else
			{
				$sql="select a.job_no_prefix_num,a.set_smv, b.insert_date, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.is_confirmed, a.quotation_id, a.agent_name, a.avg_unit_price, a.dealing_marchant, a.gmts_item_id, a.total_set_qnty as ratio, b.plan_cut, b.id as po_id, b.po_number, b.pub_shipment_date, b.po_received_date, b.po_quantity, b.unit_price, b.grouping, b.file_no, b.po_total_price from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and c.job_no=b.job_no_mst and c.entry_from=158  and a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond $season_cond $internal_ref_cond $file_no_cond order by b.pub_shipment_date, b.id";
				 
			}
			 //echo  "10**".$sql; die;
			
			//echo $sql;$order_status_cond
			$result=sql_select($sql);
			$tot_rows=count($result);
			
           
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
				  $condition->country_ship_date(" between '$start_date' and '$end_date'");
			 }
			 if(str_replace("'","",$cbo_search_date) ==2 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
			 {
				 $condition->po_received_date(" between '$start_date' and '$end_date'");
				 //and b.po_received_date between '$start_date' and '$end_date' 
				// echo 'FFGG';
			 }
			
			 if(str_replace("'","",$cbo_search_date)==3 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
			 {
				
				
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
			$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
			$yarn= new yarn($condition);
			$yarn_req_qty_arr=$yarn->getOrderWiseYarnQtyArray();
			
			//print_r($yarn_req_qty_arr);
			$trims= new trims($condition);
			$trims_costing_arr=$trims->getAmountArray_by_order();

			$fabric= new fabric($condition);
			$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
			$conversion= new conversion($condition);
			$conversion_costing_arr_process=$conversion->getAmountArray_by_orderAndProcess();
			
			$emblishment= new emblishment($condition);
			$emblishment_costing_arr_name=$emblishment->getAmountArray_by_orderAndEmbname();
			$commission= new commision($condition);
			$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
			$commercial= new commercial($condition);
			$commercial_costing_arr=$commercial->getAmountArray_by_order();
			$other= new other($condition);
			$other_costing_arr=$other->getAmountArray_by_order();
			$wash= new wash($condition);
			$emblishment_costing_arr_name_wash=$wash->getAmountArray_by_orderAndEmbname();
			
			$knit_cost_arr=array(1,2,3,4);
			$fabric_dyeingCost_arr=array(25,31,26,32,60,61,62,63,72,80,81,84,85,86,87,38,39,74,78,79,101,133,137,138,139,146,147,149);
			$aop_cost_arr=array(35,36,37,40);
			$fab_finish_cost_arr=array(33,34,38,63,65,66,67,68,69,70,71,72,73,75,76,77,88,82,89,90,91,92,93,94,128,129,135,136,141,143,150,151,155,156,157,145,82,89,132,144);
			$washing_cost_arr=array(140,142,148,64);
			
			//echo $yarn->getQuery(); die;
			
			foreach($result as $row )
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				$order_value=$row[csf('po_total_price')];
				$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
				$plancut_value=$plan_cut_qnty*$row[csf('unit_price')];
				$order_qty_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
				if(str_replace("'","",$cbo_search_date)==1)
				{
					$ship_po_recv_date=change_date_format($row[csf('pub_shipment_date')]);
				}
				else if(str_replace("'","",$cbo_search_date)==2)
				{
					$ship_po_recv_date=change_date_format($row[csf('po_received_date')]);
				}
				else if(str_replace("'","",$cbo_search_date)==3)
				{
					$insert_date=explode(" ",$row[csf('insert_date')]);
					$ship_po_recv_date=change_date_format($insert_date[0]);
				}
				
				$dzn_qnty_p=12;
				$smv_price=$yarn_dtls_arr[$row[csf('quotation_id')]]['smvamt'];
				$dzn_qnty_p=$dzn_qnty_p*$row[csf('ratio')];
				
				//$p_commercial_cost_dzn=$price_fabriccostArray[$row[csf('quotation_id')]]['comm_cost'];
				//$p_commercial_cost=($p_commercial_cost_dzn/$dzn_qnty_p)*$order_qty_pcs;
				//if(is_infinite($p_commercial_cost) || is_nan($p_commercial_cost)){$p_commercial_cost=0;}
				//$price_fabricData=array_filter(explode(",",substr($price_fabric_data_Array[$row[csf('quotation_id')]],0,-1)));
				//$p_fab_precost_Data=explode(",",substr($p_fab_precost_arr[$row[csf('quotation_id')]],0,-1));
				//echo $row[csf('quotation_id')];
				$quote_fab_source_id=""; $quote_color_type_id=""; $p_avg_rate=0; $p_yarn_costing=0; $p_fab_purchase=0;
				/*foreach($price_fabricData as $p_fabricRow)
				{
					$p_fabricRow=explode("**",$p_fabricRow);
					$p_fab_nature_id=$p_fabricRow[0];	
					$p_fab_source_id=$p_fabricRow[1];
					$p_fab_rate=$p_fabricRow[2];
					$p_yarn_qty=$p_fabricRow[3];
					$p_yarn_amount=$p_fabricRow[4];
					$p_color_type=$p_fabricRow[5];
					if($quote_fab_source_id=="") $quote_fab_source_id=$p_fab_source_id; else $quote_fab_source_id.=','.$p_fab_source_id;
					if($quote_color_type_id=="") $quote_color_type_id=$p_color_type; else $quote_color_type_id.=','.$p_color_type;
					if($p_fab_source_id==2)
					{
						foreach($p_fab_precost_Data as $p_fab_row)
						{
							
						}
					}
					else if($p_fab_source_id==1 || $p_fab_source_id==3)
					{
						$p_avg_rate=$p_yarn_amount/$p_yarn_qty;
						if(is_infinite($p_avg_rate) || is_nan($p_avg_rate)){$p_avg_rate=0;}
						//$p_yarn_costing=$p_yarn_amount/$dzn_qnty_p*$plan_cut_qnty;	
					}
				}*/
				$p_yarn_amount=$yarn_dtls_arr[$row[csf('quotation_id')]]['yarnamt'];
				$p_yarn_costing=($p_yarn_amount/$dzn_qnty_p)*$plan_cut_qnty;
				$p_yarn_qty=($yarn_dtls_arr[$row[csf('quotation_id')]]['yarnkg']/$dzn_qnty_p)*$plan_cut_qnty;
				$p_avg_rate=$p_yarn_costing/$p_yarn_qty;
				//echo $p_yarn_amount.'-'.$p_yarn_qty.'-'.$p_avg_rate.'<br>';
					/*foreach($p_fab_precost_Data as $p_fab_row)
					{
						$p_fab_dataRow=explode("**",$p_fab_row);
						$p_fab_requirment=$p_fab_dataRow[0];
						$p_fab_pcs=$p_fab_dataRow[1];
						$p_fab_rate=$p_fab_dataRow[2];
						$p_fab_sourceId=$p_fab_dataRow[3];
						
						$p_fab_purchase_qty=$p_fab_requirment/$dzn_qnty_p*$order_qty_pcs; 
					
						$p_fab_purchase+=$p_fab_purchase_qty*$p_fab_rate; 
						
							//echo $p_fab_row.'='.$p_fab_requirment.'*'.$dzn_qnty_p.'*'.$order_qty_pcs.'<br>';
					}*/
				
				
				//echo $p_yarn_amount.'-'.$dzn_qnty_p.'-'.$order_qty_pcs.'<br>';
				if(is_infinite($p_yarn_costing) || is_nan($p_yarn_costing)){$p_yarn_costing=0;}
				$p_yarn_cons=($p_yarn_qty/$plan_cut_qnty)*$dzn_qnty_p;
				if(is_infinite($p_yarn_cons) || is_nan($p_yarn_cons)){$p_yarn_cons=0;}
				$p_yarn_cost_percent=($p_yarn_costing/$order_value)*100;
				if(is_infinite($p_yarn_cost_percent) || is_nan($p_yarn_cost_percent)){$p_yarn_cost_percent=0;}
				$p_kniting_cost=$yarn_dtls_arr[$row[csf('quotation_id')]]['knitamt'];
				$p_knit_cost_dzn=$p_kniting_cost; 
				$p_knit_cost=($p_kniting_cost/$dzn_qnty_p)*$plan_cut_qnty;
				if(is_infinite($p_knit_cost) || is_nan($p_knit_cost)){$p_knit_cost=0;}
				//$p_yarn_dyeing_cost_dzn=0;$yarn_dtls_arr[$row[csf('quotation_id')]]['dyeamt'];
				//$p_yarn_dyeing_cost=($p_yarn_dyeing_cost_dzn/$dzn_qnty_p)*$plan_cut_qnty;
				//if(is_infinite($p_yarn_dyeing_cost) || is_nan($p_yarn_dyeing_cost)){$p_yarn_dyeing_cost=0;}
				$p_fabric_dyeing_cost_dzn=$yarn_dtls_arr[$row[csf('quotation_id')]]['dyeamt'];
				//echo $p_fabric_dyeing_cost_dzn.'='.$dzn_qnty_p.'*'.$order_qty_pcs;
				$p_fabric_dyeing_cost=($p_fabric_dyeing_cost_dzn/$dzn_qnty_p)*$plan_cut_qnty;
				if(is_infinite($p_fabric_dyeing_cost) || is_nan($p_fabric_dyeing_cost)){$p_fabric_dyeing_cost=0;}
				//$p_heat_setting_cost=($pq_knit_arr[$row[csf('quotation_id')]]['heat']/$dzn_qnty_p)*$order_qty_pcs;
				//if(is_infinite($p_heat_setting_cost) || is_nan($p_heat_setting_cost)){$p_heat_setting_cost=0;}
				$p_fabric_finish=($yarn_dtls_arr[$row[csf('quotation_id')]]['finamt']/$dzn_qnty_p)*$plan_cut_qnty;
				if(is_infinite($p_fabric_finish) || is_nan($p_fabric_finish)){$p_fabric_finish=0;}
				$p_washing_cost=($yarn_dtls_arr[$row[csf('quotation_id')]]['washamt']/$dzn_qnty_p)*$order_qty_pcs;
				if(is_infinite($p_washing_cost) || is_nan($p_washing_cost)){$p_washing_cost=0;}
				$p_all_over_cost=($yarn_dtls_arr[$row[csf('quotation_id')]]['aopamt']/$dzn_qnty_p)*$plan_cut_qnty;
				if(is_infinite($p_all_over_cost) || is_nan($p_all_over_cost)){$p_all_over_cost=0;}
				
				//$ex_quote_fab_source_id=implode(',',array_unique(explode(",",$quote_fab_source_id)));
				//$ex_quote_color_type_id=array_unique(explode(",",$quote_color_type_id));
				/*$fab_source_array=array(2,3,4);
				if(array_intersect($fab_source_array,$ex_quote_fab_source_id))
				{
					
				}*/$color_yarn=""; $color_pur=""; $color_knit_q="";
				if($ex_quote_fab_source_id=='1,2' || $ex_quote_fab_source_id=='2,1') 
				{
					if($p_yarn_costing<=0) $color_yarn="";
					//if($p_fab_purchase<=0) $color_pur="red"; else $color_pur="";
					if($p_yarn_costing<=0) $color_knit_q="red"; else $color_knit_q="";
				}
				else if($ex_quote_fab_source_id==1 && $ex_quote_fab_source_id==2 ) 
				{
					if($p_yarn_costing<=0) $color_yarn="red"; else $color_yarn="";
					//if($p_fab_purchase<=0) $color_pur="red"; else $color_pur="";
					if($p_knit_cost<=0) $color_knit_q="red"; else $color_knit_q="";	
				}
				else if($ex_quote_fab_source_id==1) 
				{
					if($p_yarn_costing<=0) $color_yarn="red"; else $color_yarn="";
					//if($p_fab_purchase<=0) $color_pur="";
					if($p_knit_cost<=0) $color_knit_q="red"; else $color_knit_q="";	
				}
				else if($ex_quote_fab_source_id==2) 
				{
					if($p_yarn_costing<=0) $color_yarn="";
					//if($p_fab_purchase<=0) $color_pur="red"; else $color_pur="";
					if($p_knit_cost<=0) $color_knit_q="red"; else $color_knit_q="";	
				}
				else 
				{
					if($p_yarn_costing<=0) $color_yarn="red"; else $color_yarn="";
					//if($p_fab_purchase<=0) $color_pur="red"; else $color_pur="";
					if($p_knit_cost<=0) $color_knit_q="red"; else $color_knit_q="";
				}
				
				//echo $p_yarn_costing.'=='.$color_yarn;
				$color_fab_dy="";
				foreach($ex_quote_color_type_id as $color_type_id)
				{
					if($color_type_id==2 || $color_type_id==3  || $color_type_id==4  || $color_type_id==6)
					{
						if($p_fabric_dyeing_cost<=0) if ($color_fab_dy=="") $color_fab_dy=""; else $color_fab_dy.=','."";
					}
					else
					{
						if($p_fabric_dyeing_cost<=0)  if ($color_fab_dy=="") $color_fab_dy="red"; else $color_fab_dy.=','."red"; else $color_fab_dy="";
					}
				}
				$ex_quote_color_type=implode(',',array_unique(explode(",",$color_fab_dy)));
				
				if($p_fabric_finish<=0) $color_finish="red"; else $color_finish="";	
				//if($p_commercial_cost<=0) $color_com="red"; else $color_com="";	
				$p_trim_amount= $yarn_dtls_arr[$row[csf('quotation_id')]]['trimsamt']/$dzn_qnty_p*$order_qty_pcs;
				if(is_infinite($p_trim_amount) || is_nan($p_trim_amount)){$p_trim_amount=0;}
				$p_print_amount=($yarn_dtls_arr[$row[csf('quotation_id')]]['printamt']/$dzn_qnty_p)*$order_qty_pcs;
				if(is_infinite($p_print_amount) || is_nan($p_print_amount)){$p_print_amount=0;}
				$p_embroidery_amount=($yarn_dtls_arr[$row[csf('quotation_id')]]['embamt']/$dzn_qnty_p)*$order_qty_pcs;
				if(is_infinite($p_embroidery_amount) || is_nan($p_embroidery_amount)){$p_embroidery_amount=0;}
				$p_special_amount=($yarn_dtls_arr[$row[csf('quotation_id')]]['spcamt']/$dzn_qnty_p)*$order_qty_pcs;
				if(is_infinite($p_special_amount) || is_nan($p_special_amount)){$p_special_amount=0;}
				$p_wash_amt=($yarn_dtls_arr[$row[csf('quotation_id')]]['washamt']/$dzn_qnty_p)*$order_qty_pcs;
				if(is_infinite($p_wash_amt) || is_nan($p_wash_amt)){$p_wash_amt=0;}
				$p_other_amount=($yarn_dtls_arr[$row[csf('quotation_id')]]['othamt']/$dzn_qnty_p)*$order_qty_pcs;
				if(is_infinite($p_other_amount) || is_nan($p_other_amount)){$p_other_amount=0;}
				$p_foreign=$yarn_dtls_arr[$row[csf('quotation_id')]]['commisamt']/$dzn_qnty_p*$order_qty_pcs;
				if(is_infinite($p_foreign) || is_nan($p_foreign)){$p_foreign=0;}
				//$p_local=$price_commission_array[$row[csf('quotation_id')]]['local']/$dzn_qnty_p*$order_qty_pcs;
				//if(is_infinite($p_local) || is_nan($p_local)){$p_local=0;}
				$p_test_cost=$yarn_dtls_arr[$row[csf('quotation_id')]]['testamt']/$dzn_qnty_p*$order_qty_pcs;
				if(is_infinite($p_test_cost) || is_nan($p_test_cost)){$p_test_cost=0;}
				$p_freight_cost= $yarn_dtls_arr[$row[csf('quotation_id')]]['frightamt']/$dzn_qnty_p*$order_qty_pcs;
				if(is_infinite($p_freight_cost) || is_nan($p_freight_cost)){$p_freight_cost=0;}
				//$p_inspection=$price_fabriccostArray[$row[csf('quotation_id')]]['inspection']/$dzn_qnty_p*$order_qty_pcs;
				//if(is_infinite($p_inspection) || is_nan($p_inspection)){$p_inspection=0;}
				//$p_certificate_cost=$price_fabriccostArray[$row[csf('quotation_id')]]['certificate_pre_cost']/$dzn_qnty_p*$order_qty_pcs;
				//if(is_infinite($p_certificate_cost) || is_nan($p_certificate_cost)){$p_certificate_cost=0;}
				//$p_common_oh=$price_fabriccostArray[$row[csf('quotation_id')]]['common_oh']/$dzn_qnty_p*$order_qty_pcs;
				//if(is_infinite($p_common_oh) || is_nan($p_common_oh)){$p_common_oh=0;}
				//$p_currier_cost=$price_fabriccostArray[$row[csf('quotation_id')]]['currier_pre_cost']/$dzn_qnty_p*$order_qty_pcs;
				//if(is_infinite($p_currier_cost) || is_nan($p_currier_cost)){$p_currier_cost=0;}
				$p_cm_cost_dzn=$yarn_dtls_arr[$row[csf('quotation_id')]]['cmamt'];
				$p_cm_cost=$p_cm_cost_dzn/$dzn_qnty_p*$order_qty_pcs;
				if(is_infinite($p_cm_cost) || is_nan($p_cm_cost)){$p_cm_cost=0;}
				
				//$total_p_cost=$p_yarn_costing+$p_fab_purchase+$p_knit_cost+$p_washing_cost+$p_all_over_cost+$p_yarn_dyeing_cost+$p_fabric_dyeing_cost+$p_heat_setting_cost+$p_fabric_finish+$p_trim_amount+$p_test_cost+$p_print_amount+$p_embroidery_amount+$p_special_amount+$p_other_amount+$p_wash_amt+$p_commercial_cost+$p_foreign+$p_local+$p_freight_cost+$p_inspection+$p_certificate_cost+$p_common_oh+$p_currier_cost+$p_cm_cost;
				$total_p_cost=$p_yarn_costing+$p_knit_cost+$p_washing_cost+$p_all_over_cost+$p_fabric_dyeing_cost+$p_fabric_finish+$p_trim_amount+$p_test_cost+$p_print_amount+$p_embroidery_amount+$p_special_amount+$p_other_amount+$p_wash_amt+$p_foreign+$p_freight_cost+$p_cm_cost;
			 	$total_p_cost=number_format($total_p_cost,2,".", "");
				
				if($p_trim_amount<=0) $color_trim="red"; else $color_trim="";	
				if($p_cm_cost<=0) $color="red"; else $color="";
				
				$max_profit=$asking_profit_arr[$row[csf('company_name')]]['max_profit'];
				$company_asking=$costing_date_arr[$row[csf('job_no')]]['ask'];
				
				$total_p_profit=$order_value-$total_p_cost;
				$total_p_profit_percentage2=$total_p_profit/$order_value*100; 
				if(is_infinite($total_p_profit_percentage2) || is_nan($total_p_profit_percentage2)){$total_p_profit_percentage2=0;}

				if($total_p_profit_percentage2<=0 ) $color_pl="red";
				else if($total_p_profit_percentage2>$max_profit) $color_pl="yellow";	
				else if($total_p_profit_percentage2<=$max_profit) $color_pl="green";	
				else $color_pl="";	
				$p_expected_profit=$company_asking*$order_value/100;
				if(is_infinite($p_expected_profit) || is_nan($p_expected_profit)){$p_expected_profit=0;}
				$tot_expect_variance=$total_p_profit-$p_expected_profit; 
				$ready_to_approved=$job_approve_arr[$row[csf('job_no')]]['ready_to_approved'];
				if($ready_to_approved==0 || $ready_to_approved==2) $ready_to_approved=2;else $ready_to_approved=$ready_to_approved;
				?>
                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                    <td width="40" rowspan="4"><? echo $i; ?></td>
                    <td width="70" rowspan="4"><p><? echo $buyer_library[$row[csf('buyer_name')]] ?></p></td>
                    <td width="70" rowspan="4"><p><? echo $row[csf('job_no_prefix_num')];  ?></p></td>
                    <td width="100" rowspan="4"><p><? echo $row[csf('po_number')]; ?></p></td>
                    <td width="100" rowspan="4"><p><? echo $yes_no[$ready_to_approved]; ?></p></td>
                    <td width="80" rowspan="4"><p><? echo $row[csf('file_no')]; ?></p></td>
                    <td width="80" rowspan="4"><p><? echo $row[csf('grouping')]; ?></p></td>
                    <td width="100" rowspan="4"><p><? echo  $order_status[$row[csf('is_confirmed')]]; ?></p></td>
                    <td width="110" rowspan="4"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                    <td width="110" rowspan="4"><p><? $gmts_item=''; $gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
						foreach($gmts_item_id as $item_id)
						{
							if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=", ".$garments_item[$item_id];
						}
						echo $gmts_item; ?></p></td>
                    <td rowspan="4" width="110"><p><? echo $team_member_arr[$row[csf('dealing_marchant')]]; ?></p></td>
                    <td rowspan="4" width="70"><p><? echo $ship_po_recv_date; ?></p></td>
                    <td rowspan="4" width="90" align="right"><p><? echo number_format($row[csf('po_quantity')],2); ?></p></td>
                    <td rowspan="4" width="90" align="right"><p><? echo number_format($row[csf('unit_price')],4); ?></p></td>
                    <td rowspan="4" width="100" align="right"><p><? echo number_format($order_value,2); ?></p></td>
                    <td width="100">Spot Cost</td>
                    <td width="100" align="right"><? echo number_format($p_avg_rate,2); ?></td>
                    <td width="80" align="right" bgcolor="<? echo $color_yarn; ?>" title="<? echo $p_yarn_costing; ?>"><? echo number_format($p_yarn_costing,2); ?></td>
                    <td width="80" align="right" title="<? echo $p_yarn_cost_percent.'%'; ?>"><? echo number_format($p_yarn_cost_percent,2).'%'; ?></td>
                    <td width="100" align="right" bgcolor="<? echo $color_pur; ?>"><? //echo number_format($p_fab_purchase,2); ?></td>
                    <td width="80" title="<? echo $p_knit_cost_dzn; ?>" align="right"><? echo number_format($p_knit_cost_dzn,2); ?></td>
                    <td width="80" align="right" bgcolor="<? echo $color_knit_q; ?>"><? echo number_format($p_knit_cost,2); ?></td>
                    <td width="100" align="right"><? //echo number_format($p_yarn_dyeing_cost_dzn ,2); ?></td>
                    <td width="110" align="right"><? //echo number_format($p_yarn_dyeing_cost ,2); ?></td>
                    <td width="120" align="right"><? echo number_format($p_fabric_dyeing_cost_dzn,2); ?></td>
                    <td width="100" align="right" bgcolor="<? echo $ex_quote_color_type; ?>"><? echo number_format($p_fabric_dyeing_cost,2); ?></td>
                    <td width="90" align="right"><? //echo number_format($p_heat_setting_cost,2); ?></td>
                    <td width="100" align="right"><? echo number_format($p_fabric_finish,2); ?></td>
                    <td width="90" align="right"><? echo number_format($p_washing_cost,2); ?></td>
                    <td  width="90" align="right"><? echo number_format($p_all_over_cost,2); ?></td>
                    <td width="100" align="right" bgcolor="<? echo $color_trim; ?>"><? echo number_format($p_trim_amount,2); ?></td>
                    <td width="80" align="right"><? echo number_format($p_print_amount,2); ?></td>
                    <td width="85" align="right"><? echo number_format($p_embroidery_amount,2); ?></td>
                    <td width="80" align="right"><? echo number_format($p_special_amount,2); ?></td>
                    <td width="80" align="right"><? echo number_format($p_wash_amt,2); ?></td>
                    <td width="80" align="right"><? echo number_format($p_other_amount,2); ?></td>
                    <td width="120" align="right" bgcolor="<? echo $color_com; ?>"><? //echo number_format($p_commercial_cost,2); ?></td>
                    <td width="120" align="right"><? echo number_format($p_foreign,2) ?></td>
                    <td width="120" align="right"><? //echo number_format($p_local,2) ?></td>
                    <td width="100" align="right"><? echo number_format($p_test_cost,2);?></td>
                    <td width="100" align="right"><? echo number_format($p_freight_cost,2); ?></td>
                    <td width="120" align="right"><? //echo number_format($p_inspection,2);?></td>
                    <td width="100" align="right"><? //echo number_format($p_certificate_cost,2); ?></td>
                    <td width="100" align="right"><? //echo number_format($p_common_oh,2); ?></td>
                    <td width="100" align="right"><? //echo number_format($p_currier_cost,2);?></td>
                    <td width="120" align="right"><? echo number_format($p_cm_cost_dzn,2);?></td>
                    <td width="100" align="right" bgcolor="<? echo $color; ?>"><? echo number_format($p_cm_cost,2);?></td>
                    <td width="100" align="right"><? echo number_format($total_p_cost,2); ?></td>
                    <td width="100" align="right" bgcolor="<? echo $color_pl; ?>"><? echo number_format($total_p_profit,2); ?></td>
                    <td width="100" align="right"><? echo number_format($total_p_profit_percentage2,2).'%'; ?></td>
                    <td width="100" align="right"><? echo number_format($p_expected_profit,2); ?></td>
                    <td width="100" align="right"><? echo number_format($company_asking,2); ?></td>
                    <td align="right" width="100"><? echo number_format($tot_expect_variance,2)?></td>
                    <td align="right" width="100"><? $total_smv_price+=$smv_price;echo $smv_price; ?></td>
                     <td align="right" width=""><? //echo number_format($p_yarn_cons,2); ?></td>
                </tr>
                	<?
                    $dzn_qnty=0;
                    $costing_per_id=$fabriccostArray[$row[csf('job_no')]]['costing_per_id'];
                    if($costing_per_id==1) $dzn_qnty=12;
                    else if($costing_per_id==3) $dzn_qnty=12*2;
                    else if($costing_per_id==4) $dzn_qnty=12*3;
                    else if($costing_per_id==5) $dzn_qnty=12*4;
                    else $dzn_qnty=1;
					$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
					
                    $commercial_cost_dzn=$fabriccostArray[$row[csf('job_no')]]['comm_cost'];
                    $commercial_cost=($commercial_cost_dzn/$dzn_qnty)*$order_qty_pcs;
					if(is_infinite($commercial_cost) || is_nan($commercial_cost)){$commercial_cost=0;}
                    $fabricData=explode(",",substr($fabric_data_Array[$row[csf('job_no')]],0,-1));
                    $fab_precost_Data=explode(",",substr($fab_precost_arr[$row[csf('job_no')]][$row[csf('po_id')]],0,-1));
					$pre_fab_source_id=""; $pre_color_type_id=""; $yarn_costing=0; $avg_rate=0; $fab_purchase=0; $yarn_cost_percent=0;
                    foreach($fabricData as $fabricRow)
                    {
						$fabricRow=explode("**",$fabricRow);
						$fab_source_id=$fabricRow[1];
						if($pre_fab_source_id=="") $pre_fab_source_id=$fab_source_id; else $pre_fab_source_id.=','.$fab_source_id;
						
                    }
					
					$fab_purchase_knit=array_sum($fabric_costing_arr['knit']['grey'][$row[csf('po_id')]]);
					if(is_infinite($fab_purchase_knit) || is_nan($fab_purchase_knit)){$fab_purchase_knit=0;}
					$fab_purchase_woven=array_sum($fabric_costing_arr['woven']['grey'][$row[csf('po_id')]]);
					if(is_infinite($fab_purchase_woven) || is_nan($fab_purchase_woven)){$fab_purchase_woven=0;}
					$fab_purchase=$fab_purchase_knit+$fab_purchase_woven;

					$yarn_costing=$yarn_costing_arr[$row[csf('po_id')]];
					$yarn_cons=($yarn_req_qty_arr[$row[csf('po_id')]]/$plan_cut_qnty)*$dzn_qnty;
					if(is_infinite($yarn_cons) || is_nan($yarn_cons)){$yarn_cons=0;}
					$yarn_cost_percent=($yarn_costing/$order_value)*100;
					if(is_infinite($yarn_cost_percent) || is_nan($yarn_cost_percent)){$yarn_cost_percent=0;}
					$avg_rate=$yarn_costing/$yarn_req_qty_arr[$row[csf('po_id')]];
					if(is_infinite($avg_rate) || is_nan($avg_rate)){$avg_rate=0;}
					$knit_cost=0;
						foreach($knit_cost_arr as $process_id)
						{
							$knit_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$process_id]);	
						}
					
					$knit_cost_dzn=($knit_cost/$plan_cut_qnty)*12;
					if(is_infinite($knit_cost_dzn) || is_nan($knit_cost_dzn)){$knit_cost_dzn=0;}
					$fabric_dyeing_cost=0;
					foreach($fabric_dyeingCost_arr as $fab_process_id)
					{
						$fabric_dyeing_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$fab_process_id]);	
					}
					$fabric_dyeing_cost_dzn=($fabric_dyeing_cost/$plan_cut_qnty)*12;
					if(is_infinite($fabric_dyeing_cost_dzn) || is_nan($fabric_dyeing_cost_dzn)){$fabric_dyeing_cost_dzn=0;}
						
				
					$yarn_dyeing_cost=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][30]);
					$yarn_dyeing_cost_dzn=($yarn_dyeing_cost/$plan_cut_qnty)*12;
					if(is_infinite($yarn_dyeing_cost_dzn) || is_nan($yarn_dyeing_cost_dzn)){$yarn_dyeing_cost_dzn=0;}
					
					$fabric_finish=0;
					foreach($fab_finish_cost_arr as $fin_process_id)
					{
						$fabric_finish+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$fin_process_id]);	
					}
					
					$all_over_cost=0;
					foreach($aop_cost_arr as $aop_process_id)
					{
						$all_over_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$aop_process_id]);	
					}
					
					$washing_cost=0;
					foreach($washing_cost_arr as $w_process_id)
					{
						$washing_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$w_process_id]);	
					}
						

					$heat_setting_cost=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][33]);
                    
					$ex_pre_fab_source_id=implode(',',array_unique(explode(",",$pre_fab_source_id)));
					$ex_pre_color_type_id=array_unique(explode(",",$pre_color_type_id));
					$color_yarn_p=""; $color_purchase=""; $color_knit="";
					if($ex_pre_fab_source_id=='1,2'  || $ex_pre_fab_source_id=='2,1')
					{
						if($yarn_costing<=0) $color_yarn_p="red"; else $color_yarn_p="";
						if($fab_purchase<=0) $color_purchase="red"; else $color_purchase="";
						if($knit_cost<=0) $color_knit="red"; else $color_knit="";
					}
					else if($ex_pre_fab_source_id==1 && $ex_pre_fab_source_id==2 ) 
					{
						if($yarn_costing<=0) $color_yarn_p="red"; else $color_yarn_p="";
						if($fab_purchase<=0) $color_purchase="red"; else $color_purchase="";
						if($knit_cost<=0) $color_knit="red"; else $color_knit="";
					}
					else if($ex_pre_fab_source_id==1) 
					{
						if($yarn_costing<=0) $color_yarn_p="";
						if($fab_purchase<=0) $color_purchase="";
						if($knit_cost<=0) $color_knit="red"; else $color_knit="";
					}
					else if($ex_pre_fab_source_id==2) 
					{
						if($yarn_costing<=0) $color_yarn_p="red"; else $color_yarn_p="";
						if($fab_purchase<=0) $color_purchase="red"; else $color_purchase="";
						if($knit_cost<=0) $color_knit="red"; else $color_knit="";
					}
					else 
					{
						if($yarn_costing<=0) $color_yarn_p="red"; else $color_yarn_p="";
						if($fab_purchase<=0) $color_purchase="red"; else $color_purchase="";
						if($knit_cost<=0) $color_knit="red"; else $color_knit="";
					}
					//echo $ex_pre_fab_source_id;
					
					$color_fab_d="";
					foreach($ex_pre_color_type_id as $colorType_id)
					{
						if($colorType_id==2 || $colorType_id==3  || $colorType_id==4  || $colorType_id==6)
						{
							if($fabric_dyeing_cost<=0) if ($color_fab_d=="") $color_fab_d=""; else $color_fab_d.=','."";
						}
						else
						{
							if($fabric_dyeing_cost<=0)  if ($color_fab_d=="") $color_fab_d="red"; else $color_fab_d.=','."red"; else $color_fab_d="";
						}
					}
					
					$ex_pre_color_type=implode(',',array_unique(explode(",",$color_fab_d)));

                    if($fabric_finish<=0) $color_finish="red"; else $color_finish="";	
                    if($commercial_cost<=0) $color_com="red"; else $color_com="";	
					//$trim_qty_arr[$row[csf('po_id')]];//
					$trim_amount= $trims_costing_arr[$row[csf('po_id')]];
					if(is_infinite($trim_amount) || is_nan($trim_amount)){$trim_amount=0;}

					$print_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][1];
					if(is_infinite($print_amount) || is_nan($print_amount)){$print_amount=0;}
					$embroidery_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][2];
					if(is_infinite($embroidery_amount) || is_nan($embroidery_amount)){$embroidery_amount=0;}
					$special_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][4];
					if(is_infinite($special_amount) || is_nan($special_amount)){$special_amount=0;}
					$wash_amount=$emblishment_costing_arr_name_wash[$row[csf('po_id')]][3];
					if(is_infinite($wash_amount) || is_nan($wash_amount)){$wash_amount=0;}
					$other_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][5];
					if(is_infinite($other_amount) || is_nan($other_amount)){$other_amount=0;}
					$foreign=$commission_costing_arr[$row[csf('po_id')]][1];
					if(is_infinite($foreign) || is_nan($foreign)){$foreign=0;}
					$local=$commission_costing_arr[$row[csf('po_id')]][2];
					if(is_infinite($local) || is_nan($local)){$local=0;}
					$test_cost=$other_costing_arr[$row[csf('po_id')]]['lab_test'];
					if(is_infinite($test_cost) || is_nan($test_cost)){$test_cost=0;}
					$freight_cost=$other_costing_arr[$row[csf('po_id')]]['freight'];
					if(is_infinite($freight_cost) || is_nan($freight_cost)){$freight_cost=0;}
					$inspection=$other_costing_arr[$row[csf('po_id')]]['inspection'];
					if(is_infinite($inspection) || is_nan($inspection)){$inspection=0;}
					$certificate_cost=$other_costing_arr[$row[csf('po_id')]]['certificate_pre_cost'];
					if(is_infinite($certificate_cost) || is_nan($certificate_cost)){$certificate_cost=0;}
					$common_oh=$other_costing_arr[$row[csf('po_id')]]['common_oh'];
					if(is_infinite($common_oh) || is_nan($common_oh)){$common_oh=0;}
					$currier_cost=$other_costing_arr[$row[csf('po_id')]]['currier_pre_cost'];
					if(is_infinite($currier_cost) || is_nan($currier_cost)){$currier_cost=0;}
					
					$cm_cost=$other_costing_arr[$row[csf('po_id')]]['cm_cost'];
					if(is_infinite($cm_cost) || is_nan($cm_cost)){$cm_cost=0;}
					$cm_cost_dzn=($cm_cost/$order_qty_pcs)*12;
					if(is_infinite($cm_cost_dzn) || is_nan($cm_cost_dzn)){$cm_cost_dzn=0;}
					
					//$yarn_dyeing_cost_dzn=($yarn_dyeing_cost/$plan_cut_qnty)*12;
                  
                    
                    $total_cost=$yarn_costing+$fab_purchase+$knit_cost+$washing_cost+$all_over_cost+$yarn_dyeing_cost+$fabric_dyeing_cost+$heat_setting_cost+$fabric_finish+$trim_amount+$test_cost+$print_amount+$embroidery_amount+$special_amount+$wash_amount+$other_amount+$commercial_cost+$foreign+$local+$freight_cost+$inspection+$certificate_cost+$common_oh+$currier_cost+$cm_cost;
                    
                    if($trim_amount<=0) $color_trim="red"; else $color_trim="";	
                    if($cm_cost<=0) $color="red"; else $color="";	
					
                    $total_profit=$order_value-$total_cost;
                    $total_profit_percentage=$total_profit/$order_value*100; 
                    if($total_profit_percentage<=0 ) $color_pl="red";
                    else if($total_profit_percentage>$max_profit) $color_pl="yellow";	
                    else if($total_profit_percentage<=$max_profit) $color_pl="green";	
                    else $color_pl="";	
                    
					$expected_profit=$company_asking*$order_value/100;
					$expect_variance=$total_profit-$expected_profit;
                   ?>
                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trp_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trp_<? echo $i; ?>">
                    <td width="100">Pre Cost</td>
                    <td width="100" align="right"><a href="##" onClick="generate_pre_cost_report('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','precost_yarn_detail')"><? echo  number_format($avg_rate,2); ?></a></td>
                    <td width="80" align="right" bgcolor="<? echo $color_yarn_p; ?>"><? echo number_format($yarn_costing,2); ?></td>
                    <td width="80" align="right"><? echo number_format($yarn_cost_percent,2).'%'; ?></td>
                    <td width="100" align="right" bgcolor="<? echo $color_purchase; ?>"><a href="##" onClick="generate_precost_fab_purchase_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $fab_source_id; ?>','fab_purchase_detail')"><? echo number_format($fab_purchase,2); ?></a></td>
                    <td width="80" title="<? echo $knit_cost_dzn; ?>" align="right"><? echo number_format($knit_cost_dzn,2); ?></td>
                    <td width="80" align="right" bgcolor="<? echo $color_knit; ?>"><a href="##" onClick="generate_pre_cost_knit_popup('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $cons_process; ?>','precost_knit_detail')"><? echo number_format($knit_cost,2); ?></a></td>
                    <td width="100" align="right"><? echo number_format($yarn_dyeing_cost_dzn ,2); ?></td>
                    <td width="110" align="right" bgcolor="<? //echo $color_fab_yd; ?>"><? echo number_format($yarn_dyeing_cost ,2); ?></td>
                    <td width="120" align="right"><? echo number_format($fabric_dyeing_cost_dzn,2); ?></td>
                    <td width="100" align="right" bgcolor="<? echo $ex_pre_color_type; ?>"><a href="##" onClick="generate_precost_fab_dyeing_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $fab_source_id; ?>','fab_dyeing_detail')"><? echo number_format($fabric_dyeing_cost,2); ?></a></td>
                    <td width="90" align="right"><? echo number_format($heat_setting_cost,2); ?></td>
                    <td width="100" align="right" ><a href="##" onClick="generate_precost_fab_finishing_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','fab_finishing_detail')"><? echo number_format($fabric_finish,2); ?></a> </td>
                    <td  width="90" align="right"><a href="##" onClick="generate_precost_fab_finishing_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','fab_washing_detail')"><? echo number_format($washing_cost,2); ?></a></td>
                    <td  width="90" align="right"><a href="##" onClick="generate_precost_fab_all_over_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','fab_all_over_detail')"><? echo number_format($all_over_cost,2); ?></a></td>
                    <td width="100" align="right" bgcolor="<? echo $color_trim; ?>"><a href="##" onClick="generate_precost_trim_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','trim_cost_detail')"><? echo number_format($trim_amount,2); ?></a><? //echo  number_format($trim_amount,2);?></td>
                    <td width="80" align="right"><a href="##" onClick="generate_precost_embell_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','print_cost_detail')"><? echo number_format($print_amount,2); ?></a></td>
                    <td width="85" align="right"><a href="##" onClick="generate_precost_embell_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','embroidery_cost_detail')"><? echo number_format($embroidery_amount,2); ?></a></td>
                    <td width="80" align="right"><? echo number_format($special_amount,2); ?></td>
                    <td width="80" align="right"><a href="##" onClick="generate_precost_embell_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','wash_cost_detail')"><? echo number_format($wash_amount,2); ?></a></td>
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
                    <td width="120" align="right"  title="<? echo 'CM Dzn=CM Cost/PO Qty Pcs*12';?>" ><? echo number_format($cm_cost_dzn,2);?></td>
                    <td width="100" align="right" bgcolor="<? echo $color; ?>"><? echo number_format($cm_cost,2);?></td>
                    <td width="100" align="right"><? echo number_format($total_cost,2); ?></td>
                    <td width="100" align="right" bgcolor="<? echo $color_pl; ?>"><? echo number_format($total_profit,2); ?></td>
                    <td width="100" align="right"><? echo number_format($total_profit_percentage,2).'%'; ?></td>
                    <td width="100" align="right"><? echo number_format($expected_profit,2); ?></td>
                    <td width="100" align="right"><? echo number_format($company_asking,2); ?></td>
                    <td align="right"  width="100"><? echo number_format($expect_variance,2)?></td>
                    <td width="100" align="right"><? $total_smv_pre+=$row[csf('set_smv')];echo number_format($row[csf('set_smv')],2); ?></td>
                    <td align="right" width=""><? echo number_format($yarn_cons,2); ?></td>
                </tr> 
                <?
                $avg_rate_variance=$p_avg_rate-$avg_rate;
				$yarn_variance=$p_yarn_costing-$yarn_costing;
				$yarn_cons_variance=$p_yarn_cons-$yarn_cons;
				$yarn_cost_percent_variance=$p_yarn_cost_percent-$yarn_cost_percent;
                $fab_purchase_variance=$p_fab_purchase-$fab_purchase;
                $knit_cost_dzn_variance=$p_knit_cost_dzn-$knit_cost_dzn;
                $knit_cost_variance=$p_knit_cost-$knit_cost;
                $yarn_dyeing_cost_dzn_vairance=$p_yarn_dyeing_cost_dzn-$yarn_dyeing_cost_dzn ;
                $yarn_dyeing_cost_variance=$p_yarn_dyeing_cost-$yarn_dyeing_cost;
                $fabric_dyeing_cost_dzn_variance=$p_fabric_dyeing_cost_dzn-$fabric_dyeing_cost_dzn;
                $fabric_dyeing_cost_variance=$p_fabric_dyeing_cost-$fabric_dyeing_cost;
                $heat_setting_cost_variance=$p_heat_setting_cost-$heat_setting_cost;
                $fabric_finish_variance_cost=$p_fabric_finish-$fabric_finish;
                $washing_cost_variance=$p_washing_cost-$washing_cost;
                $all_over_variance=$p_all_over_cost-$all_over_cost;
				
				if($trim_amount<=0) $color_trim="red"; else $color_trim="";	
				if($cm_cost<=0) $color="red"; else $color="";	
				
				$trim_cost_variance=$p_trim_amount-$trim_amount;
				$print_varinace=$p_print_amount-$print_amount;
				$embrodery_variance=$p_embroidery_amount-$embroidery_amount;
				$special_variance=$p_special_amount-$special_amount;
				$wash_amount_variance=$p_wash_amt-$wash_amount;
				$other_amount_variance=$p_other_amount-$other_amount;
				$commercial_variance=$p_commercial_cost-$commercial_cost;
				$foreign_variance=$p_foreign-$foreign;
				$local_variance=$p_local-$local;
				$test_variance=$p_test_cost-$test_cost;
				$feight_variance=$p_freight_cost-$freight_cost;
				$inspection_variance=$p_inspection-$inspection;
				$certificate_variance=$p_certificate_cost-$certificate_cost;
				$common_variance=$p_common_oh-$common_oh;
				$currier_variance=$p_currier_cost-$currier_cost;
				$cm_dzn_variance=$p_cm_cost_dzn-$cm_cost_dzn;
				$cm_variance=$p_cm_cost-$cm_cost;
				$total_cost_varaince=$total_p_cost-$total_cost;
				
				if($fabric_dyeing_cost_variance<=0) $color_fab_v="red"; else $color_fab_v="";
				if($yarn_dyeing_cost_variance<=0) $color_fab_dy_v="red"; else $color_fab_dy_v="";	
				if($yarn_variance<=0) $color_yarn_v="red"; else $color_yarn_v="";	
				if($knit_cost_variance<=0) $color_knit_v="red"; else $color_knit_v="";	
				if($fabric_finish<=0) $color_finish="red"; else $color_finish="";
				if($commercial_variance<=0) $color_com_v="red"; else $color_com_v="";	
				if($trim_cost_variance<=0) $color_trim_v="red"; else $color_trim_v="";
				if($cm_variance<=0) $color_cm_v="red"; else $color_cm_v="";
		
				$total_profit=$order_value-$total_cost;
				$total_profit_percentage=$total_profit/$order_value*100; 
				if($total_profit_percentage<=0 ) $color_pl="red";
				else if($total_profit_percentage>$max_profit) $color_pl="yellow";	
				else if($total_profit_percentage<=$max_profit) $color_pl="green";	
				else $color_pl="";	
				
				$tot_profit_variance=$total_profit-$total_p_profit;
				$tot_profit_percient_varaince=$total_profit_percentage-$total_p_profit_percentage2;
				$tot_expected_profit=$expected_profit-$p_expected_profit;
				$tot_expected_varaince_data_vairance=$expect_variance-$tot_expect_variance;
				
				//$max_profit=$asking_profit_arr[$row[csf('company_name')]]['max_profit'];
				//echo $max_profit;
				//$company_asking=$asking_profit_arr[$row[csf('company_name')]]['asking_profit'];
				?>
                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trv_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trv_<? echo $i; ?>">
                    <td width="100">Variance</td>
                    <td width="100" align="right"><? echo number_format($avg_rate_variance,2); ?></td>
                    <td width="80" align="right" bgcolor="<? //echo $color_yarn_v; ?>"><? echo number_format($yarn_variance,2); ?></td>
                    <td width="80" align="right"><? echo number_format($yarn_cost_percent_variance,2).'%'; ?></td>
                    <td width="100" align="right"><? echo number_format($fab_purchase_variance,2); ?></td>
                    <td width="80" align="right"><? echo number_format($knit_cost_dzn_variance,2); ?></td>
                    <td width="80" align="right" bgcolor="<? //echo $color_knit_v; ?>"><? echo number_format($knit_cost_variance,2); ?></td>
                    <td width="100" align="right"><? echo number_format($yarn_dyeing_cost_dzn_vairance ,2); ?></td>
                    <td width="110" align="right" bgcolor="<? //echo $color_fab_dy_v; ?>"><? echo number_format($yarn_dyeing_cost_variance ,2); ?></td>
                    <td width="120" align="right"><? echo number_format($fabric_dyeing_dzn_variance,2); ?></td>
                    <td width="100" align="right" bgcolor="<? //echo $color_fab_v; ?>"><? echo number_format($fabric_dyeing_cost_variance,2); ?></td>
                    <td width="90" align="right"><? echo number_format($heat_setting_cost_variance,2); ?></td>
                    <td width="100" align="right"><? echo number_format($fabric_finish_variance_cost,2); ?> </td>
                    <td width="90" align="right"><? echo number_format($washing_cost_variance,2); ?></td>
                    <td width="90" align="right"><? echo number_format($all_over_variance,2); ?></td>
                    <td width="100" align="right" bgcolor="<? //echo $color_trim_v; ?>"><? echo number_format($trim_cost_variance,2); ?></td>
                    <td width="80" align="right"><? echo number_format($print_varinace,2); ?></td>
                    <td width="85" align="right"><? echo number_format($embrodery_variance,2); ?></td>
                    <td width="80" align="right"><? echo number_format($special_variance,2); ?></td>
                    <td width="80" align="right"><? echo number_format($wash_amount_variance,2); ?></td>
                    <td width="80" align="right"><? echo number_format($other_amount_variance,2); ?></td>
                    <td width="120" align="right" bgcolor="<? //echo $color_com_v; ?>"><? echo number_format($commercial_variance,2); ?></td>
                    <td width="120" align="right"><? echo number_format($foreign_variance,2) ?></td>
                    <td width="120" align="right"><? echo number_format($local_variance,2) ?></td>
                    <td width="100" align="right"><? echo number_format($test_variance,2);?></td>
                    <td width="100" align="right"><? echo number_format($feight_variance,2); ?></td>
                    <td width="120" align="right"><? echo number_format($inspection_variance,2);?></td>
                    <td width="100" align="right"><? echo number_format($certificate_variance,2); ?></td>
                    <td width="100" align="right"><? echo number_format($common_variance,2); ?></td>
                    <td width="100" align="right"><? echo number_format($currier_variance,2);?></td>
                    <td width="120" align="right"><? echo number_format($cm_dzn_variance,2);?></td>
                    <td width="100" align="right" bgcolor="<? //echo $color_cm_v; ?>"><? echo number_format($cm_variance,2);?></td>
                    <td width="100" align="right"><? echo number_format($total_cost_varaince,2); ?></td>
                    <td width="100" align="right" bgcolor="<? //echo $color_pl; ?>"><? echo number_format($tot_profit_variance,2); ?></td>
                    <td width="100" align="right"><p><? echo number_format($tot_profit_percient_varaince,2).'%'; ?></p></td>
                    <td width="100" align="right"><? //echo number_format($p_expected_profit,2); ?></td>
                    <td width="100" align="right"><? //echo number_format($company_asking,2); ?></td>
                    <td align="right"  width="100"><? echo number_format($tot_expected_varaince_data_vairance,2); ?></td>
                    <td align="right"  width="100"><? $total_smv_vari+=$row[csf('set_smv')]-$smv_price;echo number_format($row[csf('set_smv')]-$smv_price,2); ?></td>
                     <td align="right"  width=""><? echo number_format($yarn_cons_variance,2); ?></td>
                </tr>
                <?
                    $percent_avg_rate=$avg_rate_variance/$p_avg_rate*100;
					if(is_infinite($percent_avg_rate) || is_nan($percent_avg_rate)){$percent_avg_rate=0;}
					$percent_yarn_cons=$yarn_cons_variance/$p_yarn_cons*100;
					if(is_infinite($percent_yarn_cons) || is_nan($percent_yarn_cons)){$percent_yarn_cons=0;}
					$percent_yarn_costing=$yarn_variance/$p_yarn_costing*100;
					if(is_infinite($percent_yarn_costing) || is_nan($percent_yarn_costing)){$percent_yarn_costing=0;}
					$percent_yarn_cost_percent=$yarn_cost_variance_percent/$p_yarn_cost_percent*100;
					if(is_infinite($percent_yarn_cost_percent) || is_nan($percent_yarn_cost_percent)){$percent_yarn_cost_percent=0;}
					$percent_fab_purchase=$fab_purchase_variance/$p_fab_purchase*100;
					if(is_infinite($percent_fab_purchase) || is_nan($percent_fab_purchase)){$percent_fab_purchase=0;}
					$percent_knit_cost_dzn=$knit_cost_dzn_variance/$p_knit_cost_dzn*100;
					if(is_infinite($percent_knit_cost_dzn) || is_nan($percent_knit_cost_dzn)){$percent_knit_cost_dzn=0;}
					$percent_knit_cost=$knit_cost_variance/$p_knit_cost*100;
					if(is_infinite($percent_knit_cost) || is_nan($percent_knit_cost)){$percent_knit_cost=0;}
					$percent_yarn_dyeing_cost_dzn=$p_yarn_dyeing_cost_dzn/$yarn_dyeing_cost_dzn*100;
					if(is_infinite($percent_yarn_dyeing_cost_dzn) || is_nan($percent_yarn_dyeing_cost_dzn)){$percent_yarn_dyeing_cost_dzn=0;}
					$percent_yarn_dyeing_cost=$p_yarn_dyeing_cost/$yarn_dyeing_cost*100;
					if(is_infinite($percent_yarn_dyeing_cost) || is_nan($percent_yarn_dyeing_cost)){$percent_yarn_dyeing_cost=0;}
					$percent_fabric_dyeing_dzn=$fabric_dyeing_dzn_variance/$p_fabric_dyeing_cost_dzn*100;
					if(is_infinite($percent_fabric_dyeing_dzn) || is_nan($percent_fabric_dyeing_dzn)){$percent_fabric_dyeing_dzn=0;}
					$percent_fabric_dyeing=$fabric_dyeing_cost_variance/$p_fabric_dyeing_cost*100;
					if(is_infinite($percent_fabric_dyeing) || is_nan($percent_fabric_dyeing)){$percent_fabric_dyeing=0;}
					$percent_heat_setting_cost=$heat_setting_cost_variance/$p_heat_setting_cost*100;
					if(is_infinite($percent_heat_setting_cost) || is_nan($percent_heat_setting_cost)){$percent_heat_setting_cost=0;}
					$percent_fabric_finish=$fabric_finish_variance_cost/$p_fabric_finish*100;
					if(is_infinite($percent_fabric_finish) || is_nan($percent_fabric_finish)){$percent_fabric_finish=0;}
					$percent_wash_cost=$wash_cost_variance/$p_washing_cost*100;
					if(is_infinite($percent_wash_cost) || is_nan($percent_wash_cost)){$percent_wash_cost=0;}
					$percent_all_over=$all_over_variance/$p_all_over_cost*100;
					if(is_infinite($percent_all_over) || is_nan($percent_all_over)){$percent_all_over=0;}
					
                    /*if($fabric_dyeing_cost<=0 && $yarn_dyeing_cost<=0) $color_fab="red"; else $color_fab="";	
                    if($yarn_costing<=0) $color_yarn="red"; else $color_yarn="";	
                    if($kniting_cost<=0) $color_knit="red"; else $color_knit="";	
                    if($fabric_finish<=0) $color_finish="red"; else $color_finish="";	
                    if($commercial_cost<=0) $color_com="red"; else $color_com="";
                    */
                   // $max_profit=$asking_profit_arr[$row[csf('company_name')]]['max_profit'];
                    //echo $max_profit;
                    //$company_asking=$asking_profit_arr[$row[csf('company_name')]]['asking_profit'];
                    /*if($trim_cost_variance<=0) $color_trim="red"; else $color_trim="";	
                    if($cm_cost<=0) $color="red"; else $color="";*/
					
					$percent_trim_cost=$trim_cost_variance/$p_trim_amount*100;
					if(is_infinite($percent_trim_cost) || is_nan($percent_trim_cost)){$percent_trim_cost=0;}
					$percent_print=$print_varinace/$p_print_amount*100;
					if(is_infinite($percent_print) || is_nan($percent_print)){$percent_print=0;}
					$percent_embroidery_amount=$embrodery_variance/$p_embroidery_amount*100;
					if(is_infinite($percent_embroidery_amount) || is_nan($percent_embroidery_amount)){$percent_embroidery_amount=0;}
					$percent_special=$special_variance/$p_special_amount*100;
					if(is_infinite($percent_special) || is_nan($percent_special)){$percent_special=0;}
					$percent_wash_amount=$wash_amount_variance/$p_wash_amount*100;
					if(is_infinite($percent_wash_amount) || is_nan($percent_wash_amount)){$percent_wash_amount=0;}
					$percent_other_amount=$other_amount_variance/$p_other_amount*100;
					if(is_infinite($percent_other_amount) || is_nan($percent_other_amount)){$percent_other_amount=0;}
					$percent_commercial=$commercial_variance/$p_commercial_cost*100;
					if(is_infinite($percent_commercial) || is_nan($percent_commercial)){$percent_commercial=0;}
					$percent_foreign=$foreign_variance/$p_foreign*100;
					if(is_infinite($percent_foreign) || is_nan($percent_foreign)){$percent_foreign=0;}
					$percent_local=$local_variance/$p_local*100;
					if(is_infinite($percent_local) || is_nan($percent_local)){$percent_local=0;}
					$percent_test=$test_variance/$p_test_cost*100;
					if(is_infinite($percent_test) || is_nan($percent_test)){$percent_test=0;}
					$percent_feight=$feight_variance/$p_freight_cost*100;
					if(is_infinite($percent_feight) || is_nan($percent_feight)){$percent_feight=0;}
					$percent_inspection=$inspection_variance/$p_inspection*100;
					if(is_infinite($percent_inspection) || is_nan($percent_inspection)){$percent_inspection=0;}
					$percent_certificate=$certificate_variance/$p_certificate_cost*100;
					if(is_infinite($percent_certificate) || is_nan($percent_certificate)){$percent_certificate=0;}
					$percent_common_oh=$common_variance/$p_common_oh*100;
					if(is_infinite($percent_common_oh) || is_nan($percent_common_oh)){$percent_common_oh=0;}
					$percent_currier=$currier_variance/$p_currier_cost*100;
					if(is_infinite($percent_currier) || is_nan($percent_currier)){$percent_currier=0;}
					$percent_cm_dzn=$cm_dzn_variance/$p_cm_cost_dzn*100;
					if(is_infinite($percent_cm_dzn) || is_nan($percent_cm_dzn)){$percent_cm_dzn=0;}
					$percent_cm=$cm_variance/$p_cm_cost*100;
					if(is_infinite($percent_cm) || is_nan($percent_cm)){$percent_cm=0;}
					$percent_total_cost=$total_cost_varaince/$total_p_cost*100;
					if(is_infinite($percent_total_cost) || is_nan($percent_total_cost)){$percent_total_cost=0;}
					$percent_tot_profit=$tot_profit_variance/$total_p_profit*100;
					if(is_infinite($percent_tot_profit) || is_nan($percent_tot_profit)){$percent_tot_profit=0;}
					
					$percent_tot_profit_percient_varaince=$tot_profit_percient_varaince/$total_p_profit_percentage2*100;
					if(is_infinite($percent_tot_profit_percient_varaince) || is_nan($percent_tot_profit_percient_varaince)){$percent_tot_profit_percient_varaince=0;}
                    $total_cost_vari_percent_amount=$total_p_cost/$total_cost*100;
					if(is_infinite($total_cost_vari_percent_amount) || is_nan($total_cost_vari_percent_amount)){$total_cost_vari_percent_amount=0;}
                    $total_profit_vari_percnt_amount=$total_order_value-$total_cost_vari_percent_amount;
                    $total_profit_percentage2=$total_profit/$total_order_value*100;
					if(is_infinite($total_profit_percentage2) || is_nan($total_profit_percentage2)){$total_profit_percentage2=0;} 
                    if($total_profit_percentage2<=0 ) $color_pl="red";
                    else if($total_profit_percentage2>$max_profit) $color_pl="yellow";	
                    else if($total_profit_percentage2<=$max_profit) $color_pl="green";	
                    else $color_pl="";	
                ?>
                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trvp_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trvp_<? echo $i; ?>">
                    <td width="100">Variance %</td>
                    <td width="100" align="right"><? echo number_format($percent_avg_rate,2).'%'; ?></td>
                    <td width="80" align="right" bgcolor="<? //echo $color_yarn; ?>"><? echo number_format($percent_yarn_costing,2); ?></td>
                    <td width="80" align="right"><? echo number_format($percent_yarn_cost_percent,2).'%'; ?></td>
                    <td width="100" align="right"><? echo number_format($percent_fab_purchase,2).'%'; ?></td>
                    <td width="80" align="right"><? echo number_format($percent_knit_cost_dzn,2).'%'; ?></td>
                    <td width="80" align="right" bgcolor="<? //echo $color_knit; ?>"><? echo number_format($percent_knit_cost,2).'%'; ?></td>
                    <td width="100" align="right"><? echo number_format($percent_yarn_dyeing_cost_dzn,2).'%'; ?></td>
                    <td width="110" align="right"><? echo number_format($percent_yarn_dyeing_cost,2).'%'; ?></td>
                    <td width="120" align="right"><? echo number_format($percent_fabric_dyeing_dzn,2).'%'; ?></td>
                    <td width="100" align="right" bgcolor="<? //echo $color_fab; ?>"><? echo number_format($percent_fabric_dyeing,2).'%'; ?></td>
                    <td width="90" align="right"><? echo number_format($percent_heat_setting_cost,2).'%'; ?></td>
                    <td width="100" align="right"><? echo number_format($percent_fabric_finish,2).'%'; ?> </td>
                    <td width="90" align="right"><? echo number_format($percent_wash_cost,2).'%'; ?></td>
                    <td width="90" align="right"><? echo number_format($percent_all_over,2).'%'; ?></td>
                    <td width="100" align="right"><? echo number_format($percent_trim_cost,2).'%'; ?></td>
                    <td width="80" align="right"><? echo number_format($percent_print,2).'%'; ?></td>
                    <td width="85" align="right"><? echo number_format($percent_embroidery_amount,2).'%'; ?></td>
                    <td width="80" align="right"><? echo number_format($percent_special,2).'%'; ?></td>
                    <td width="80" align="right"><? echo number_format($percent_wash_amount,2).'%'; ?></td>
                    <td width="80" align="right"><? echo number_format($percent_other_amount,2).'%'; ?></td>
                    <td width="120" align="right"><? echo number_format($percent_commercial,2).'%'; ?></td>
                    <td width="120" align="right"><? echo number_format($percent_foreign,2).'%'; ?></td>
                    <td width="120" align="right"><? echo number_format($percent_local,2).'%'; ?></td>
                    <td width="100" align="right"><? echo number_format($percent_test,2).'%';?></td>
                    <td width="100" align="right"><? echo number_format($percent_feight,2).'%'; ?></td>
                    <td width="120" align="right"><? echo number_format($percent_inspection,2).'%';?></td>
                    <td width="100" align="right"><? echo number_format($percent_certificate,2).'%'; ?></td>
                    <td width="100" align="right"><? echo number_format($percent_common_oh,2).'%'; ?></td>
                    <td width="100" align="right"><? echo number_format($percent_currier,2).'%';?></td>
                    <td width="120" align="right"><? echo number_format($percent_cm_dzn,2).'%';?></td>
                    <td width="100" align="right"><? echo number_format($percent_cm,2).'%';?></td>
                    <td width="100" align="right"><? echo number_format($percent_total_cost,2).'%'; ?></td>
                    
                    <td width="100" align="right"><? echo number_format($percent_tot_profit,2).'%'; ?></td>
                    <td width="100" align="right"><? echo number_format($percent_tot_profit_percient_varaince,2).'%'; ?></td>
                    <td width="100" align="right"><? //echo number_format($p_expected_profit,2); ?></td>
                    <td width="100" align="right"><? //echo number_format($company_asking,2); ?></td>
                    <td align="right" width="100"><? $expect_variance=$total_profit-$expected_profit; echo '-'; ?></td>
                     <td align="right" width="100"><? 
					 	$smv_price=number_format($smv_price,'.','',4);
					  echo (($row[csf('set_smv')]-$smv_price)/$smv_price*100); ?></td>
                     <td align="right"  width=""><? echo number_format($percent_yarn_cons,2); ?></td>
                </tr>
				<?
                $total_order_amount+=$order_value;
                $total_plancut_amount+=$plancut_value;
				
				//price_total
				$p_total_order_qty+=$order_qty_pcs;
				$p_total_order_value+=$order_value;
				$p_total_yarn_costing+=$p_yarn_costing;
				$p_total_yarn_cons+=$p_yarn_cons;
				$p_total_fab_purchase+=$p_fab_purchase;
				$p_total_knit_cost+=$p_knit_cost;
				$p_total_yarn_dyeing_cost+=$p_yarn_dyeing_cost;
				$p_total_fabric_dyeing_cost+=$p_fabric_dyeing_cost;
				$p_total_heat_setting_cost+=$p_heat_setting_cost;
				$p_total_fabric_finish+=$p_fabric_finish;
				$p_total_washing_cost+=$p_washing_cost;
				$p_total_all_over_cost+=$p_all_over_cost;
				$p_total_trim_amount+=$p_trim_amount;
				$p_total_print_amount+=$p_print_amount;
				$p_total_embroidery_amount+=$p_embroidery_amount;
				$p_total_special_amount+=$p_special_amount;
				$p_total_wash_amount+=$p_wash_amount;
				$p_total_other_amount+=$p_other_amount;
				$p_total_commercial_cost+=$p_commercial_cost;
				$p_total_foreign+=$p_foreign;
				$p_total_local+=$p_local;
				$p_total_test_cost+=$p_test_cost;
				$p_total_freight_cost+=$p_freight_cost;
				$p_total_wash_amt+=$p_wash_amt;
				$p_total_inspection+=$p_inspection;
				$p_total_certificate_cost+=$p_certificate_cost;
				$p_total_common_oh+=$p_common_oh;
				$p_total_currier_cost+=$p_currier_cost;
				$p_total_cm_cost+=$p_cm_cost;
				$p_total_p_cost+=$total_p_cost;
				$p_total_p_profit+=$total_p_profit;
				$p_total_profit_percentage2+=$total_p_profit_percentage2;
				$p_total_expected_profit+=$p_expected_profit;
				$p_total_expect_variance+=$tot_expect_variance;
				//pre_cost
				
				//pre_cost
				$total_order_qty+=$order_qty_pcs;
				$total_order_value+=$order_value;
				$total_yarn_costing+=$yarn_costing;
				$total_yarn_cons+=$yarn_cons;
				$total_fab_purchase+=$fab_purchase;
				$total_knit_cost+=$knit_cost;
				$total_yarn_dyeing_cost+=$yarn_dyeing_cost;
				$total_fabric_dyeing_cost+=$fabric_dyeing_cost;
				$total_heat_setting_cost+=$heat_setting_cost;
				$total_fabric_finish+=$fabric_finish;
				$total_washing_cost+=$washing_cost;
				$total_all_over_cost+=$all_over_cost;
				$total_trim_amount+=$trim_amount;
				$total_print_amount+=$print_amount;
				$total_embroidery_amount+=$embroidery_amount;
				$total_special_amount+=$special_amount;
				$total_wash_amt+=$wash_amount;
				$total_other_amount+=$other_amount;
				$total_commercial_cost+=$commercial_cost;
				$total_foreign+=$foreign;
				$total_local+=$local;
				$total_test_cost+=$test_cost;
				$total_freight_cost+=$freight_cost;
				$total_wash_amount+=$wash_amount;
				$total_inspection+=$inspection;
				$total_certificate_cost+=$certificate_cost;
				$total_common_oh+=$common_oh;
				$total_currier_cost+=$currier_cost;
				$total_cm_cost+=$cm_cost;
				$total_pre_cost+=$total_cost;
				$total_pre_profit+=$total_profit;
				$total_pre_profit_percentage2+=$total_profit_percentage2;
				$total_pre_expected_profit+=$expected_profit;
				$total_pre_expect_variance+=$expect_variance;
				//variance_total
				$v_total_order_qty+=$order_qty_pcs;
				$v_total_order_value+=$order_value;
				$v_total_yarn_costing+=$yarn_variance;
				$v_total_yarn_cons+=$yarn_cons_variance;
				$v_total_fab_purchase+=$fab_purchase_variance;
				$v_total_knit_cost+=$knit_cost_variance;
				$v_total_yarn_dyeing_cost+=$yarn_dyeing_cost_variance;
				$v_total_fabric_dyeing_cost+=$fabric_dyeing_cost_variance;
				$v_total_heat_setting_cost+=$heat_setting_cost_variance;
				$v_total_fabric_finish+=$fabric_finish_variance_cost;
				$v_total_washing_cost+=$washing_cost_variance;
				$v_total_all_over_cost+=$all_over_variance;
				$v_total_trim_amount+=$trim_cost_variance;
				$v_total_print_amount+=$print_varinace;
				$v_total_embroidery_amount+=$embrodery_variance;
				$v_total_special_amount+=$special_variance;
				$v_total_wash_amount+=$wash_amount_variance;
				$v_total_other_amount+=$other_amount_variance;
				$v_total_commercial_cost+=$commercial_variance;
				$v_total_foreign+=$foreign_variance;
				$v_total_local+=$local_variance;
				$v_total_test_cost+=$test_variance;
				$v_total_freight_cost+=$feight_variance;
				$v_total_wash_amount+=$p_wash_amount;
				$v_total_inspection+=$inspection_variance;
				$v_total_certificate_cost+=$certificate_variance;
				$v_total_common_oh+=$common_variance;
				$v_total_currier_cost+=$currier_variance;
				$v_total_cm_cost+=$cm_variance;
				$v_total_p_cost+=$total_cost_varaince;
				$v_total_p_profit+=$tot_profit_variance;
				$v_total_profit_percentage2+=$tot_profit_percient_varaince;
				$v_total_expected_profit+=$tot_expected_profit;
				$v_total_expect_variance+=$tot_expected_varaince_data_vairance;
				//pre_cost
				$i++;
			}
			$p_yarn_cost_percentage=$p_total_yarn_costing/$p_total_order_value*100;
			if(is_infinite($p_yarn_cost_percentage) || is_nan($p_yarn_cost_percentage)){$p_yarn_cost_percentage=0;}
			?>
            </table>
            </div>
            <table class="tbl_bottom" width="5310" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
                <tr>
                    <td width="40"></td>
                    <td width="70"></td>
                    <td width="70"></td>
                    <td width="100"></td>
                     <td width="100"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="100"></td>
                    <td width="110"></td>
                    <td width="110"></td>
                    <td width="110"></td>
                    <td width="70">Spot Cost</td>
                    <td width="90" align="right" id="total_order_qnty"><? echo number_format($p_total_order_qty,2); ?></td>
                    <td width="90"></td>
                    <td width="100" align="right" id="p_total_order_value"><? echo number_format($p_total_order_value,2); ?></td>
                    <td width="100"></td>
                    <td width="100"></td>
                    <td width="80" align="right" id="p_total_yarn_costing"><? echo number_format($p_total_yarn_costing,2); ?></td>
                    <td width="80" align="right" ><? //echo number_format($p_yarn_cost_percentage,2); ?></td>
                    <td width="100" align="right" id="p_total_fab_purchase"><? echo number_format($p_total_fab_purchase,2); ?></td>
                    <td width="80"></td>
                    <td width="80" align="right" id="p_total_knit_cost"><? echo number_format($p_total_knit_cost,2); ?></td>
                    <td width="100"></td>
                    <td width="110" align="right" id="p_total_yarn_dyeing_cost"><? echo number_format($p_total_yarn_dyeing_cost,2); ?></td>
                    <td width="120"></td>
                    <td width="100" align="right" id="p_fabric_dyeing_td"><? echo number_format($p_total_fabric_dyeing_cost,2); ?></td>
                    <td width="90" align="right" id="p_heat_setting_td"><? echo number_format($p_total_heat_setting_cost,2); ?></td>		
                    <td width="100" align="right" id="p_fabric_finishing_td"><? echo number_format($p_total_fabric_finish,2); ?></td>
                    <td width="90" align="right" id="p_washing_td"><? echo number_format($p_total_washing_cost,2); ?></td>
                    <td width="90" align="right" id="p_aop_td"><? echo number_format($p_total_all_over_cost,2); ?></td>
                    <td width="100" align="right" id="p_trim_amount_td"><? echo number_format($p_total_trim_amount,2); ?></td>
                    <td width="80" align="right" id="p_print_amount_td"><? echo number_format($p_total_print_amount,2); ?></td>
                    <td width="85" align="right" id="p_embroidery_amount_td"><? echo number_format($p_total_embroidery_amount,2); ?></td>
                    <td width="80" align="right" id="p_special_amount_td"><? echo number_format($p_total_special_amount,2); ?></td>
                    <td width="80" align="right" id="p_wash_amt_td"><? echo number_format($p_total_wash_amt,2); ?></td>
                    <td width="80" align="right" id="p_other_amount_td"><? echo number_format($p_total_other_amount,2); ?></td>
                    <td width="120" align="right" id="p_commercial_cost_td"><? echo number_format($p_total_commercial_cost,2); ?></td>
                    <td width="120" align="right" id="p_foreign_td"><? echo number_format($p_total_foreign,2); ?></td>
                    <td width="120" align="right" id="p_local_td"><? echo number_format($p_total_local,2); ?></td>
                    <td width="100" align="right" id="p_test_cost_td"><? echo number_format($p_total_test_cost,2); ?></td>
                    <td width="100" align="right" id="p_freight_cost_td"><? echo number_format($p_total_freight_cost,2); ?></td>
                    <td width="120" align="right" id="p_inspection_cost_td"><? echo number_format($p_total_inspection,2); ?></td>
                    <td width="100" align="right" id="p_certificate_cost_td"><? echo number_format($p_total_certificate_cost,2); ?></td>
                    <td width="100" align="right" id="p_operating_exp_td"><? echo number_format($p_total_common_oh,2); ?></td>
                    <td width="100" align="right" id="p_currier_cost_td"><? echo number_format($p_total_currier_cost,2); ?></td>
                    <td width="120"></td>
                    <td width="100" align="right" id="p_cm_cost_td"><? echo number_format($p_total_cm_cost,2); ?></td>
                    <td width="100" align="right" id="p_tot_cost_td"><? echo number_format($p_total_p_cost,2); ?></td>
                    <td width="100" align="right" id="p_profitt_td"><? echo number_format($p_total_p_profit,2);?></td>
                    <td width="100" align="right"><? echo number_format($p_total_profit_percentage2,2);?></td>
                    <td width="100" align="right" id="p_expProfitt_td"><? echo number_format($p_total_expected_profit,2);?></td>
                    <td width="100" align="right"><? //echo number_format($p_total_expected_profit,2);?></td>
                    <td align="right" width="100" id="p_expect_variance_td"><? echo number_format($p_total_expect_variance,2);?></td>
                    <td align="right" width="100" id="p_expect_variance_td2"><? echo number_format($total_smv_price,2);?></td>
                     <td align="right" width="" id=""><? echo number_format($p_total_yarn_cons,2);?></td>
                </tr>
                <tr>
                    <td width="40"></td>
                    <td width="70"></td>
                    <td width="70"></td>
                    <td width="100"></td>
                    <td width="100"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="100"></td>
                    <td width="110"></td>
                    <td width="110"></td>
                    <td width="110"></td>
                    <td width="70">Pre Cost</td>
                    <td width="90" align="right" id="total_order_qnty"><? echo number_format($total_order_qty,2); ?></td>
                    <td width="90"></td>
                    <td width="100" align="right" id="total_order_amount2"><? echo number_format($total_order_value,2); ?></td>
                    <td width="100"></td>
                    <td width="100"></td>
                    <td width="80" align="right" id="total_yarn_costing"><? echo number_format($total_yarn_costing,2); ?></td>
                    <td width="80" align="right" ><? //echo number_format($total_yarn_cost_percentage,2); ?></td>
                    <td width="100" align="right" id="total_fab_purchase"><? echo number_format($total_fab_purchase,2); ?></td>
                    <td width="80"></td>
                    <td width="80" align="right" id="total_knit_cost"><? echo number_format($total_knit_cost,2); ?></td>
                    <td width="100"></td>
                    <td width="110" align="right" id="total_yarn_dyeing_cost"><? echo number_format($total_yarn_dyeing_cost,2); ?></td>
                    <td width="120"><? ?></td>
                    <td width="100" align="right" id="fabric_dyeing_td"><? echo number_format($total_fabric_dyeing_cost,2); ?></td>
                    <td width="90" align="right" id="heat_setting_td"><? echo number_format($total_heat_setting_cost,2); ?></td>		
                    <td width="100" align="right" id="fabric_finishing_td"><? echo number_format($total_fabric_finish,2); ?></td>
                    <td width="90" align="right" id="washing_td"><? echo number_format($total_washing_cost,2); ?></td>
                    <td width="90" align="right" id="aop_td"><? echo number_format($total_all_over_cost,2); ?></td>
                    <td width="100" align="right" id="trim_amount_td"><? echo number_format($total_trim_amount,2); ?></td>
                    <td width="80" align="right" id="print_amount_td"><? echo number_format($total_print_amount,2); ?></td>
                    <td width="85" align="right" id="embroidery_amount_td"><? echo number_format($total_embroidery_amount,2); ?></td>
                    <td width="80" align="right" id="special_amount_td"> <? echo number_format($total_special_amount,2); ?></td>
                    <td width="80" align="right" id="wash_amt_td"> <? echo number_format($total_wash_amt,2); ?></td>
                    <td width="80" align="right" id="other_amount_td"><? echo number_format($total_other_amount,2); ?></td>
                    <td width="120" align="right" id="commercial_cost_td"><? echo number_format($total_commercial_cost,2); ?></td>
                    <td width="120" align="right" id="foreign_td"><? echo number_format($total_foreign,2); ?></td>
                    <td width="120" align="right" id="local_td"><? echo number_format($total_local,2); ?></td>
                    <td width="100" align="right" id="test_cost_td"><? echo number_format($total_test_cost,2); ?></td>
                    <td width="100" align="right" id="freight_cost_td"><? echo number_format($total_freight_cost,2); ?></td>
                    <td width="120" align="right" id="inspection_cost_td"><? echo number_format($total_inspection,2); ?></td>
                    <td width="100" align="right" id="certificate_cost_td"><? echo number_format($total_certificate_cost,2); ?></td>

                    <td width="100" align="right" id="operating_exp_td"><? echo number_format($total_common_oh,2); ?></td>
                    <td width="100" align="right" id="currier_cost_td"><? echo number_format($total_currier_cost,2); ?></td>
                    <td width="120"></td>
                    <td width="100" align="right" id="cm_cost_td"><? echo number_format($total_cm_cost,2); ?></td>
                    <td width="100" align="right" id="tot_cost_td"><? echo number_format($total_pre_cost,2); ?></td>
                    <td width="100" align="right" id="profitt_td"><? echo number_format($total_pre_profit,2);?></td>
                    <td width="100" align="right"><?  echo number_format($total_pre_profit_percentage2,2);?></td>
                    <td width="100" align="right" id="expProfitt_td"><? echo number_format($total_pre_expected_profit,2);?></td>
                    <td width="100" align="right"><? //echo number_format($total_pre_expected_profit,2);?></td>
                    <td align="right" width="100"  id="expect_variance_td"><? echo number_format($total_pre_expect_variance,2);?></td>
                    <td align="right" width="100"  id="expect_variance_td2"><? echo number_format($total_smv_pre,2);?></td>
                     <td align="right" width=""  id=""><? echo number_format($total_yarn_cons,2);?></td>
                </tr>
                <tr>
                    <td width="40"></td>
                    <td width="70"></td>
                    <td width="70"></td>
                    <td width="100"></td>
                    <td width="100"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="100"></td>
                    <td width="110"></td>
                    <td width="110"></td>
                    <td width="110"></td>
                    <td width="70">Variance</td>
                    <td width="90" align="right"><? //echo number_format($v_total_order_qty,2); ?></td>
                    <td width="90"></td>
                    <td width="100" align="right"><? //echo number_format($v_total_order_value,2); ?></td>
                    <td width="100"></td>
                    <td width="100"></td>
                    <td width="80" align="right" id="v_total_yarn_costing"><? echo number_format($v_total_yarn_costing,2); ?></td>
                    <td width="80" align="right"><? //echo number_format($v_total_yarn_cost_percentage,2); ?></td>
                    <td width="100" align="right" id="v_total_fab_purchase"><? echo number_format($v_total_fab_purchase,2); ?></td>
                    <td width="80"></td>
                    <td width="80" align="right" id="v_total_knit_cost"><? echo number_format($v_total_knit_cost,2); ?></td>
                    <td width="100"></td>
                    <td width="110" align="right" id="v_total_yarn_dyeing_cost"><? echo number_format($v_total_yarn_dyeing_cost,2); ?></td>
                    <td width="120"><? ?></td>
                    <td width="100" align="right" id="v_fabric_dyeing_td"><? echo number_format($v_total_fabric_dyeing_cost,2); ?></td>
                    <td width="90" align="right" id="v_heat_setting_td"><? echo number_format($v_total_heat_setting_cost,2); ?></td>		
                    <td width="100" align="right" id="v_fabric_finishing_td"><? echo number_format($v_total_fabric_finish,2); ?></td>
                    <td width="90" align="right" id="v_washing_td"><? echo number_format($v_total_washing_cost,2); ?></td>
                    <td width="90" align="right" id="v_aop_td"><? echo number_format($v_total_all_over_cost,2); ?></td>
                    <td width="100" align="right" id="v_trim_amount_td"><? echo number_format($v_total_trim_amount,2); ?></td>
                    <td width="80" align="right" id="v_print_amount_td"><? echo number_format($v_total_print_amount,2); ?></td>
                    <td width="85" align="right" id="v_embroidery_amount_td"><? echo number_format($v_total_embroidery_amount,2); ?></td>
                    <td width="80" align="right" id="v_special_amount_td"><? echo number_format($v_total_special_amount,2); ?></td>
                    <td width="80" align="right" id="v_wash_amt_td"><? echo number_format($v_total_wash_amount,2); ?></td>
                    <td width="80" align="right" id="v_other_amount_td"><? echo number_format($v_total_other_amount,2); ?></td>
                    <td width="120" align="right" id="v_commercial_cost_td"><? echo number_format($v_total_commercial_cost,2); ?></td>
                    <td width="120" align="right" id="v_foreign_td"><? echo number_format($v_total_foreign,2); ?></td>
                    <td width="120" align="right" id="v_local_td"><? echo number_format($v_total_local,2); ?></td>
                    <td width="100" align="right" id="v_test_cost_td"><? echo number_format($v_total_test_cost,2); ?></td>
                    <td width="100" align="right" id="v_freight_cost_td"><? echo number_format($v_total_freight_cost,2); ?></td>
                    <td width="120" align="right" id="v_inspection_cost_td"><? echo number_format($v_total_inspection,2); ?></td>
                    <td width="100" align="right" id="v_certificate_cost_td"><? echo number_format($v_total_certificate_cost,2); ?></td>
                    <td width="100" align="right" id="v_operating_exp_td"><? echo number_format($v_total_common_oh,2); ?></td>
                    <td width="100" align="right" id="v_currier_cost_td"><? echo number_format($v_total_currier_cost,2); ?></td>
                    <td width="120"></td>
                    <td width="100" align="right" id="v_cm_cost_td"><? echo number_format($v_total_cm_cost,2); ?></td>
                    <td width="100" align="right" id="v_tot_cost_td"><? echo number_format($v_total_p_cost,2); ?></td>
                    <td width="100" align="right" id="v_profitt_td"><? echo number_format($v_total_p_profit,2);?></td>
                    <td width="100" align="right"><?  //echo number_format($v_total_pre_profit_percentage2,2);?></td>
                    <td width="100" align="right" id="v_expProfitt_td"><? //echo number_format($v_total_pre_expected_profit,2);?></td>
                    <td width="100" align="right"><? //echo number_format($v_total_pre_expected_profit,2);?></td>
                    <td align="right" width="100"  id="v_expect_variance_td"><? echo number_format($v_total_expect_variance,2);?></td>
                    <td width="100"  align="right"><? echo number_format($total_smv_vari,2);?></td>
                     <td width=""  align="right"><? echo number_format($v_total_yarn_cons,2);?></td>
                </tr>
            </table>
            </fieldset>
            </div>
           <?
		}
	}
	
	
	if($report_type==1 || $report_type==5 || $report_type==4 || $report_type==8 || $report_type==9)
	{
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
	}
	else
	{
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
	}
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
                $data_array=("select  a.body_part_id, a.fab_nature_id,a.fabric_source, a.fabric_description,  a.fabric_source, a.rate, a.amount,a.avg_finish_cons,b.cons,sum(b.requirment) as avg_cons   from wo_pri_quo_fabric_cost_dtls a,wo_pri_quo_fab_co_avg_con_dtls b where b.wo_pri_quo_fab_co_dtls_id=a.id and a.quotation_id='$quotation_id' and a.quotation_id=b.quotation_id  and a.fabric_source=2 and a.status_active=1 and  a.is_deleted=0 group by a.id,a.quotation_id, a.item_number_id, a.body_part_id, a.fab_nature_id,a.fabric_source, a.fabric_description, a.fabric_source, a.rate, a.amount,a.avg_finish_cons,b.cons");
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
                $data_array=("select cons_process, avg_req_qnty, req_qnty as req_qnty, charge_unit as charge_unit, amount as amount from wo_pre_cost_fab_conv_cost_dtls where job_no='$job_no' and cons_process in(1,2,3,4) and status_active=1 and is_deleted=0");
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
				//$fabric_dyeingCost_arr=array(25,26,31,32,60,61,62,63,72,80,81,84,85,86,87,38,74,78,79);
				$fabric_dyeingCost_arr=array(25,31,26,32,60,61,62,63,72,80,81,84,85,86,87,38,39,74,78,79,101,133,137,138,139,146,147,149);
                $i=1;
               $data_array=("select  a.cons_process, avg(a.avg_req_qnty) as avg_req_qnty, sum(a.req_qnty) as req_qnty, avg(a.charge_unit) as charge_unit,
 sum(a.amount) as amount,b.id as po_id from wo_pre_cost_fab_conv_cost_dtls a,wo_po_break_down b where a.job_no='$job_no' and b.id in($po_id) and a.job_no=b.job_no_mst and a.cons_process in(25,31,26,32,60,61,62,63,72,80,81,84,85,86,87,38,39,74,78,79,101,133,137,138,139,146,147,149) and a.status_active=1 and a.is_deleted=0 group by  b.id,a.cons_process");
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
			   $data_array=("select cons_process,req_qnty as req_qnty,charge_unit as charge_unit,amount as amount from wo_pre_cost_fab_conv_cost_dtls where job_no='$job_no' and cons_process in(33,34,38,63,65,66,67,68,69,70,71,72,73,75,76,77,88,82,89,90,91,92,93,94,128,129,135,136,141,143,150,151,155,156,157,145,82,89,132,144) and status_active=1 and is_deleted=0 group by id,cons_process,req_qnty,charge_unit,amount ");
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
				$washing_cost_arr=array(140,142,148,64);
                $i=1;
                 $data_array=("select  a.cons_process, avg(a.avg_req_qnty) as avg_req_qnty, sum(a.req_qnty) as req_qnty, avg(a.charge_unit) as charge_unit,
 sum(a.amount) as amount,b.id as po_id from wo_pre_cost_fab_conv_cost_dtls a,wo_po_break_down b where a.job_no='$job_no' and b.id in($po_id) and a.job_no=b.job_no_mst and a.cons_process in(140,142,148,64) and a.status_active=1 and a.is_deleted=0 group by  b.id,a.cons_process");
                $sql_result=sql_select($data_array);
                foreach($sql_result as $row)
                {
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					//echo $dzn_qnty;
					$cons_qty=($row[csf('req_qnty')]/$dzn_qnty)*$order_qty;
					$tot_amount=$row[csf('amount')];
					$total_amount=($tot_amount/$dzn_qnty)*$order_qty;
					$tot_cons_amount=$cons_qty*$order_qty;
					$washing_cost=$washing_qty=0;
					foreach($washing_cost_arr as $w_process_id)
						{
							$washing_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$w_process_id]);	
							$washing_qty+=array_sum($conversion_qty_arr_process[$row[csf('po_id')]][$w_process_id]);
						}
						
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
                $data_array=("select cons_process, avg_req_qnty, req_qnty as req_qnty, charge_unit as charge_unit, amount as amount from wo_pre_cost_fab_conv_cost_dtls where job_no='$job_no' and cons_process in(35,36,37,40) and status_active=1 and is_deleted=0");
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
            $trimsArray=("select  a.trim_group, a.cons_dzn_gmts,a.ex_cons,a.cons_uom, a.amount, a.rate 
            from wo_pri_quo_trim_cost_dtls a where a.quotation_id='$quotation_id' and a.status_active=1 and  a.is_deleted=0 order by a.id");
            //echo $trimsArray;
            $sql_result=sql_select($trimsArray);
            foreach($sql_result as $row)
            {
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				
				$tot_amount=$row[csf('cons_dzn_gmts')]+$row[csf('ex_cons')];
				//echo $order_qty.'--'.$dzn_qnty.'--'.$tot_amount.'<br>';
				$total_reg=($order_qty/$dzn_qnty)*$tot_amount;
				$tot_cons_amount=$row[csf('rate')]*$total_reg;
				?>
				<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    <td width="30"><p><? echo $i; ?></p></td>
                    <td width="70" align="center"><p><? echo $item_library[$row[csf('trim_group')]]; ?></p></td>
                    <td width="80" align="right"><p><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></p></td>
                    <td width="70" align="right"><p><? echo $tot_amount; ?></p></td>
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
	$dzn_qnty=1;
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
	//echo $ratio_qty."__".$dzn_qnty;
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
	$dzn_qnty=1;
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
?>