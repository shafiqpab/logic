<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
//--------------------------------------------------------------------------------------------------------------------
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
$yarn_count_library=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count"  );
$costing_library=return_library_array( "select job_no, costing_date from wo_pre_cost_mst", "job_no", "costing_date"  );
$team_member_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
$order_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number"  );

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 160, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'order_wise_budget_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
					$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
					$arr=array(1=>$buyer);
					
					echo  create_list_view("list_view", "Job No,Buyer,Style Ref.,Order No", "110,110,150,180","610","350",0, $sql, "js_set_value", "id,po_number", "", 1, "0,buyer_name,0,0,0", $arr , "job_no_prefix_num,buyer_name,style_ref_no,po_number", "order_wise_budget_report_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0','') ;
					disconnect($con);
					exit(); 
	}					// Order Search End
					$tmplte=explode("**",$data);
					if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
					if ($template=="") $template=1;
if($action=="report_generate")
{ 
					$process = array( &$_POST );
					extract(check_magic_quote_gpc( $process )); 
					$report_type=str_replace("'","",$reporttype);
					//echo $report_type;
					//echo $cbo_search_date;die;
					$company_name=str_replace("'","",$cbo_company_name);
					$season=str_replace("'","",$txt_season);
					
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
					
					$start_date=str_replace("'","",$txt_date_from);
					$end_date=str_replace("'","",$txt_date_to);
					
					$start_month=date("Y-m",strtotime($start_date));
					$end_month=date("Y-m",strtotime($end_date));
					$end_date2=date("Y-m-d",strtotime($end_date));
					
					if($db_type==2)
					{
						$start_date=change_date_format($start_date,'yyyy-mm-dd','-',1);
						$end_date2=change_date_format($end_date2,'yyyy-mm-dd','-',1);
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
					if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
					if($season=="") $season_cond=""; else $season_cond=" and a.season in('".implode("','",explode(",",$season))."')";
					$order_no=str_replace("'","",$txt_order_id);
					$order_num=str_replace("'","",$txt_order_no);
					if(str_replace("'","",$txt_order_id)!="" && str_replace("'","",$txt_order_id)!=0) $order_id_cond_trans=" and b.id in ($order_no)";
					else if ($order_num=="") $order_no_cond=""; else $order_no_cond=" and  b.po_number in ('$order_num') ";
			if($report_type==1)
			{
					if($template==1)
					{
					ob_start();
					$style1="#E9F3FF"; 
					$style="#FFFFFF";
 
 					$fab_precost_arr=array();$commission_array=array();$knit_arr=array(); $fabriccostArray=array(); $fab_emb=array();$fabric_data_Array=array();$asking_profit_arr=array(); $yarncostArray=array(); $yarn_desc_array=array();
					
					$yarncostDataArray=sql_select("select job_no, count_id, type_id, sum(cons_qnty) as cons_qnty, sum(amount) as amount from wo_pre_cost_fab_yarn_cost_dtls where status_active=1 and is_deleted=0 group by job_no, count_id, type_id");
					foreach($yarncostDataArray as $yarnRow)
					{
					   $yarncostArray[$yarnRow[csf('job_no')]].=$yarnRow[csf('count_id')]."**".$yarnRow[csf('type_id')]."**".$yarnRow[csf('cons_qnty')]."**".$yarnRow[csf('amount')].",";
					}
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
					$fabricDataArray=sql_select("select a.job_no, a.fab_nature_id, a.fabric_source, a.rate, b.yarn_cons_qnty, b.yarn_amount from wo_pre_cost_fabric_cost_dtls a, wo_pre_cost_sum_dtls b where a.job_no=b.job_no and a.fabric_source!=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
					foreach($fabricDataArray as $fabricRow)
					{
					$fabric_data_Array[$fabricRow[csf('job_no')]].=$fabricRow[csf('fab_nature_id')]."**".$fabricRow[csf('fabric_source')]."**".$fabricRow[csf('rate')]."**".$fabricRow[csf('yarn_cons_qnty')]."**".$fabricRow[csf('yarn_amount')].",";
					}//Pre cost end
					
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
					 $fabriccostDataArray=sql_select("select job_no, costing_per_id, trims_cost, embel_cost, cm_cost, commission, common_oh, lab_test, inspection, freight, comm_cost,certificate_pre_cost,currier_pre_cost from wo_pre_cost_dtls where status_active=1 and is_deleted=0  ");
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
					?>
				<script>
                    var total_fab_cost=document.getElementById('total_fab_cost').value;
                    var total_fab_percent=document.getElementById('total_fab_percent').value;
                    document.getElementById('fab_cost').innerHTML=total_fab_cost;
                    document.getElementById('fab_percent').innerHTML=total_fab_percent;
                    
                    var total_trim_cost=document.getElementById('total_trim_cost').value;
                    var total_trim_percent=document.getElementById('total_trim_percent').value;
                    document.getElementById('trim_cost_id').innerHTML=total_trim_cost;
                    document.getElementById('trim_percent').innerHTML=total_trim_percent;
                    
                    var total_embelishment_cost=document.getElementById('total_embelishment_cost').value;
                    var total_embelishment_percent=document.getElementById('total_embelishment_percent').value;
                    document.getElementById('embelishment_id').innerHTML=total_embelishment_cost;
                    document.getElementById('embelishment_percent').innerHTML=total_embelishment_percent;
                    
                    var total_commercial_cost=document.getElementById('total_commercial_cost').value;
                    var total_commercial_percent=document.getElementById('total_commercial_percent').value;
                    document.getElementById('commercial_id').innerHTML=total_commercial_cost;
                    document.getElementById('commercial_percent').innerHTML=total_commercial_percent;
                    
                    var total_commssion_cost=document.getElementById('total_commssion_cost').value;
                    var total_commssion_percent=document.getElementById('total_commssion_percent').value;
                    document.getElementById('commission_id').innerHTML=total_commssion_cost;
                    document.getElementById('commission_percent').innerHTML=total_commssion_percent;
                    
                    var total_testing_cost=document.getElementById('total_testing_cost').value;
                    var total_testing_cost_percent=document.getElementById('total_testing_cost_percent').value;
                    document.getElementById('testing_id').innerHTML=total_testing_cost;
                    document.getElementById('testing_percent').innerHTML=total_testing_cost_percent;
                    
                    var total_freight_cost=document.getElementById('total_freight_cost').value;
                    var total_freight_cost_percent=document.getElementById('total_freight_cost_percent').value;
                    document.getElementById('freight_id').innerHTML=total_freight_cost;
                    document.getElementById('freight_percent').innerHTML=total_freight_cost_percent;
                    var total_cost_up=document.getElementById('total_cost_up2').value;
                 
                    document.getElementById('cost_id').innerHTML=total_cost_up;
                   
                    var total_cm_cost=document.getElementById('total_cm_cost').value;
                    var total_cm_percent=document.getElementById('total_cm_percent').value;
                    document.getElementById('cm_id').innerHTML=total_cm_cost;
                    document.getElementById('cm_percent').innerHTML=total_cm_percent;
                    var total_order_amount=document.getElementById('total_order_amount').value;
                    var total_order_amount_percent=document.getElementById('total_order_amount_percent').value;
                    document.getElementById('order_id').innerHTML=total_order_amount;
                    document.getElementById('order_percent').innerHTML=total_order_amount_percent;
                    var total_inspection=document.getElementById('total_inspection').value;
                    var total_inspection_percent=document.getElementById('total_inspection_percent').value;
                    document.getElementById('inspection_id').innerHTML=total_inspection;
                    document.getElementById('inspection_percent').innerHTML=total_inspection_percent;
                    var total_certificate_cost=document.getElementById('total_certificate_cost').value;
                    var total_certificate_percent=document.getElementById('total_certificate_percent').value;
                    document.getElementById('certificate_id').innerHTML=total_certificate_cost;
                    document.getElementById('certificate_percent').innerHTML=total_certificate_percent;
                    var total_common_oh=document.getElementById('total_common_oh').value;
                    var total_common_oh_percent=document.getElementById('total_common_oh_percent').value;
                    document.getElementById('commn_id').innerHTML=total_common_oh;
                    document.getElementById('commn_percent').innerHTML=total_common_oh_percent;
                    
                    var total_common_oh=document.getElementById('total_common_oh').value;
                    var total_common_oh_percent=document.getElementById('total_common_oh_percent').value;
                    document.getElementById('commn_id').innerHTML=total_common_oh;
                    document.getElementById('commn_percent').innerHTML=total_common_oh_percent;
                    var total_currier_cost=document.getElementById('total_currier_cost').value;
                    var total_currier_cost_percent=document.getElementById('total_currier_cost_percent').value;
                    document.getElementById('courier_id').innerHTML=total_currier_cost;
                    document.getElementById('courier_percent').innerHTML=total_currier_cost_percent;
					
					var total_fab_profit_id=document.getElementById('total_fab_profit_id').value;
                    var total_expected_profit_id=document.getElementById('total_expected_profit_id').value;
                    document.getElementById('fab_profit_id').innerHTML=total_fab_profit_id;
                    document.getElementById('expected_id').innerHTML=total_expected_profit_id;
					
					var total_expt_profit_variance=document.getElementById('total_expt_profit_variance_id').value;
                  
                    document.getElementById('expt_p_variance_id').innerHTML=total_expt_profit_variance;
                  
				    var total_cost_percent=document.getElementById('total_cost_percent').value;
				    document.getElementById('cost_percent').innerHTML=total_cost_percent;
					var total_profit_fab_percentage=document.getElementById('total_profit_fab_percentage_id').value;
				    document.getElementById('profit_fab_percentage').innerHTML=total_profit_fab_percentage;
					var total_expt_profit_percentage=document.getElementById('total_expt_profit_percentage_id').value;
				    document.getElementById('profit_expt_fab_percentage').innerHTML=total_expt_profit_percentage;
					var total_expt_profit_percentage=document.getElementById('total_expt_profit_variance_percentage_id').value;
				    document.getElementById('expt_p_percent').innerHTML=total_expt_profit_percentage;
					var expected_profit_percent=document.getElementById('expected_profit_percent').value;
				    document.getElementById('expt_percent').innerHTML=expected_profit_percent;
					
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
                	<td width="600" align="left">
                    	<table width="320" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="2">
                        <caption><strong>Order Wise Budget Cost Summary</strong></caption>
                        <thead align="center">
                        <th>SL</th><th>Particulars</th><th>Amount</th><th>Percentage</th>
                        </thead>
                        <tr bgcolor="<? echo $style1; ?>">
                        <td width="20">1</td>
                        <td width="100">Fabric Cost</td><td width="120" align="right" id="fab_cost"></td>
                        <td width="80" align="right" id="fab_percent"></td>
                        </tr>
                        <tr bgcolor="<?  echo $style; ?>">
                        <td width="20">2</td>
                        <td width="100">Trims Cost</td><td align="right" id="trim_cost_id"></td>
                        <td align="right" id="trim_percent"> </td>
                        </tr>
                        <tr bgcolor="<? echo $style1; ?>">
                        <td width="20">3</td>
                        <td width="100">Embellish Cost</td><td align="right" id="embelishment_id"></td>
                        <td align="right" id="embelishment_percent"> </td>
                        </tr>
                        <tr bgcolor="<? echo $style; ?>">
                        <td width="20">4</td>
                        <td width="100">Commercial Cost</td><td align="right" id="commercial_id"></td>
                        <td align="right" id="commercial_percent"> </td>
                        </tr>
                        <tr bgcolor="<? echo $style1; ?>">
                        <td width="20">5</td>
                        <td width="100">Commision Cost</td><td align="right" id="commission_id"></td>
                        <td align="right" id="commission_percent"> </td>
                        </tr>
                         <tr bgcolor="<? echo $style; ?>">
                        <td width="20">6</td>
                        <td width="100">Testing Cost</td><td align="right" id="testing_id"></td>
                        <td align="right" id="testing_percent"> </td>
                        </tr>
                        <tr bgcolor="<? echo $style1; ?>">
                        <td width="20">7</td>
                        <td width="100">Freight Cost</td><td align="right" id="freight_id"></td>
                        <td align="right" id="freight_percent"> </td>
                        </tr>
                        
                        <tr bgcolor="<? echo $style; ?>">
                        <td width="20">8</td>
                        <td width="100">Inspection Cost</td><td align="right" id="inspection_id"></td>
                        <td align="right" id="inspection_percent"> </td>
                        </tr>
                        <tr bgcolor="<? echo $style1; ?>">
                        <td width="20">9</td>
                        <td width="100">Certificate Cost</td><td align="right" id="certificate_id"></td>
                        <td align="right" id="certificate_percent"> </td>
                        </tr>
                        <tr bgcolor="<? echo $style; ?>">
                        <td width="20">10</td>
                        <td width="100">Commn OH</td><td align="right" id="commn_id"></td>
                        <td align="right" id="commn_percent"> </td>
                        </tr>
                        <tr bgcolor="<? echo $style1; ?>">
                        <td width="20">11</td>
                        <td width="100">Courier Cost</td><td align="right" id="courier_id"></td>
                        <td align="right" id="courier_percent"> </td>
                        </tr>
                        <tr bgcolor="<? echo $style; ?>">
                        <td width="20">12</td>
                        <td width="100">CM Cost</td><td align="right" id="cm_id"></td>
                        <td align="right" id="cm_percent"> </td>
                        </tr>
                        <tr bgcolor="<? echo $style1; ?>">
                        <td width="20">13</td>
                        <td width="100">Total Cost</td><td align="right" id="cost_id"></td>
                        <td align="right" id="cost_percent"> </td>
                        </tr>
                        <tr bgcolor="<? echo $style; ?>">
                        <td width="20">14</td>
                        <td width="100">Total Order Value</td><td align="right" id="order_id"></td>
                        <td align="right" id="order_percent"> </td>
                        </tr>
                        <tr bgcolor="<? echo $style1; ?>">
                        <td width="20">15</td>
                        <td width="100">Profit/Loss </td><td align="right" id="fab_profit_id"></td>
                        <td align="right" id="profit_fab_percentage"> </td>
                        </tr>
                        <tr bgcolor="<? echo $style; ?>">
                        <td width="20">16</td>
                        <td width="100">Expected Profit <div id="expt_percent"></div> </td><td align="right" id="expected_id"></td>
                        <td align="right" id="profit_expt_fab_percentage"> </td>
                        </tr>
                        <tr bgcolor="<? echo $style1; ?>">
                        <td width="20">17</td>
                        <td width="100">Expt.Profit Variance </td><td align="right" id="expt_p_variance_id"></td>
                        <td align="right" id="expt_p_percent"> </td>
                        </tr>
                       
                        </table>
                    </td>
                    <td colspan="5" style="min-height:800px; max-height:100%" align="center" valign="top">
                  <div id="chartdiv" style="width:700px; height:900px;" align="center"></div>
                   </td>
                  </tr>
           </table>
           </div>
           <br/>   
         <h3 align="left" id="accordion_h2" style="width:4670px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel2', '')"> -Search Panel</h3>
        <fieldset style="width:100%;" id="content_search_panel2">	
            <table width="4670">
                    <tr class="form_caption">
                        <td colspan="47" align="center"><strong>Order Wise Budget Report</strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="47" align="center"><strong><? echo $company_library[$company_name]; ?></strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td align="left"><strong>Details Report </strong></td>
                    </tr>
            </table>
               <? $asking_profit_head=$asking_profit_arr[$company_name]['asking_profit']; ?>
            <table id="table_header_1" class="rpt_table" width="4650" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                <tr>
                    <th width="40" rowspan="2">SL</th>
                   	<th width="70" rowspan="2">Buyer</th>
                    <th width="70" rowspan="2">Job No</th>
                    <th width="100" rowspan="2">Order No</th>
                    <th width="100" rowspan="2">Order Status</th>
                    <th width="110" rowspan="2">Style</th>
                    <th width="110" rowspan="2">Item Name</th>
                    <th width="110" rowspan="2">Dealing</th>
                    <?
                   if(str_replace("'","",$cbo_search_date)==1)
					{ ?>
						<th width="70" rowspan="2">Ship. Date</th>
					<? }
					else  if(str_replace("'","",$cbo_search_date)==2)
					{ ?>
						<th width="70" rowspan="2">PO Recv. Date</th>
					<? }
					else
					{
					 ?>
						<th width="70" rowspan="2">PO Insert Date</th>
					<? 	
					}
					?>
                    <th width="90" rowspan="2">Order Qty</th>
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
                    <th width="100" rowspan="2">Commn OH</th>
                    <th width="100" rowspan="2">Courier Cost</th>
                    <th width="120" rowspan="2">CM/DZN</th>
                    <th width="100" rowspan="2">CM Cost</th>
                    <th width="100" rowspan="2">Total Cost</th>
                    <th width="100" rowspan="2">Profit/Loss</th>
                    <th width="100" rowspan="2">Profit/Loss %</th>
                    <th width="100" rowspan="2">Expected Profit(<? echo $asking_profit_head.'%' ?>)</th>
                    <th width="" rowspan="2">Expt.Profit Variance</th>
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
            <div style="width:4670px; max-height:400px; overflow-y:scroll" id="scroll_body">
             <table class="rpt_table" width="4650" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
                <? 
                $i=1; $total_order_qty=0;  $total_yarn_dyeing_cost=0; $total_yarn_cost=0; $total_order_value=0;$total_purchase_cost=0; $grand_tot_trims_cost=0; $total_fabric_dyeing_cost=0; $total_knitting_cost=0; $total_heat_setting_cost=0;$total_finishing_cost=0; $total_washing_cost=0; $fabric_dyeing_cost_dzn=0; $other_cost=0;
$all_over_print_cost=0;$total_trim_cost=0;$total_commercial_cost=0;
                
                $sql="select a.job_no_prefix_num,b.insert_date, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,b.is_confirmed,a.agent_name,a.avg_unit_price,a.dealing_marchant, a.gmts_item_id, a.total_set_qnty as ratio,b.plan_cut,b.id as po_id, b.po_number, b.pub_shipment_date,b.po_received_date, b.po_quantity, b.unit_price from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond $buyer_id_cond  $year_cond $job_no_cond $order_id_cond_trans $order_no_cond $order_status_cond $season_cond group by b.id,a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,b.is_confirmed,a.style_ref_no,a.agent_name,a.avg_unit_price,a.dealing_marchant,a.total_set_qnty,b.plan_cut, a.gmts_item_id, b.po_number, b.pub_shipment_date,b.po_received_date, b.po_quantity, b.unit_price,b.insert_date order by  b.pub_shipment_date,b.id ";
				//echo $sql;
				$result=sql_select($sql);
				 $tot_rows=count($result);
				 foreach($result as $row )
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
					 $dzn_qnty=0;
					$costing_per_id=$fabriccostArray[$row[csf('job_no')]]['costing_per_id'];
					if($costing_per_id==1)
					{
						$dzn_qnty=12;
					}
					else if($costing_per_id==3)
					{
						$dzn_qnty=12*2;
					}
					else if($costing_per_id==4)
					{
						$dzn_qnty=12*3;
					}
					else if($costing_per_id==5)
					{
						$dzn_qnty=12*4;
					}
					else
					{
						$dzn_qnty=1;
					}
					$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
					$order_qty_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
					$dzn_qnty_yarn=$dzn_qnty*$row[csf('ratio')];
					
					$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
					$total_order_value=$row[csf('po_quantity')]*$row[csf('avg_unit_price')];
					$total_plancut_value=$plan_cut_qnty*$row[csf('avg_unit_price')];
				?>
				 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                
                     <td width="40"><? echo $i; ?></td>
                     <td  width="70" title="<? echo $buyer_library[$row[csf('buyer_name')]] ?>"><p><? echo $buyer_library[$row[csf('buyer_name')]] ?></p></td>
                     <td  width="70" title="<? echo $row[csf('job_no_prefix_num')];  ?>"><p><? echo $row[csf('job_no_prefix_num')];  ?></p></td>
                     <td  width="100" title="<? echo $row[csf('po_number')]; ?>"><p><a href="#" onClick="precost_bom_pop('<?  echo $row[csf('po_id')]; ?>','<?  echo $row[csf('job_no')]; ?>','<?  echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>');"><? echo $row[csf('po_number')]; ?></a></p></td>
                     <td  width="100" title="<? echo $order_status[$row[csf('is_confirmed')]]; ?>"><p><? echo  $order_status[$row[csf('is_confirmed')]]; ?></p></td>
                     <td  width="110" title="<? echo $row[csf('style_ref_no')]; ?>"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                     <td  width="110"><p>
					<? $gmts_item='';
                    $gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
                    foreach($gmts_item_id as $item_id)
                    {
                        if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
                    }
                    echo $gmts_item;
					?>
                     </p></td>
                     <td  width="110" title="<? echo $team_member_arr[$row[csf('dealing_marchant')]]; ?>"><p><? echo $team_member_arr[$row[csf('dealing_marchant')]]; ?></p></td>
                     <td  width="70" title="<? echo $ship_po_recv_date; ?>"><p><? echo $ship_po_recv_date; ?></p></td>
                     <td  width="90" align="right" title="<? echo number_format($row[csf('po_quantity')],2); ?>"><p><? echo number_format($row[csf('po_quantity')],2); ?></p></td>
                     <td  width="90" align="right" title="<? echo number_format($row[csf('avg_unit_price')],2); ?>"><p><? echo number_format($row[csf('avg_unit_price')],2); ?></p></td>
                     <td width="100" align="right" title="<? echo number_format($total_order_value,2); ?>" ><p><? 
					
					 $total_order_amount+=$total_order_value;
					 $total_plancut_amount+=$total_plancut_value;
					 echo number_format($total_order_value,2); ?></p></td>
                     <?
                       
						$commercial_cost=$fabriccostArray[$row[csf('job_no')]]['comm_cost'];
						$tot_commercial_cost=($commercial_cost/$dzn_qnty)*$order_qty_pcs;
						$fabricData=explode(",",substr($fabric_data_Array[$row[csf('job_no')]],0,-1));
						$fab_precost_Data=explode(",",substr($fab_precost_arr[$row[csf('job_no')]][$row[csf('po_id')]],0,-1));
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
							$yarn_costing=$yarn_amount/$dzn_qnty*$plan_cut_qnty;		
							}
						 
						}
						$kniting_cost=$knit_arr[$row[csf('job_no')]]['knit']+$knit_arr[$row[csf('job_no')]]['weaving']+$knit_arr[$row[csf('job_no')]]['collar_cuff']+$knit_arr[$row[csf('job_no')]]['feeder_stripe'];
						$tot_knit_cost=($kniting_cost/$dzn_qnty)*$plan_cut_qnty;
						$knit_cost_dzn=$kniting_cost; 
						$washing_cost=($knit_arr[$row[csf('job_no')]]['washing']/$dzn_qnty)*$plan_cut_qnty;
						$all_over_cost=($knit_arr[$row[csf('job_no')]]['all_over']/$dzn_qnty)*$plan_cut_qnty;
						$yarn_dyeing_cost=($knit_arr[$row[csf('job_no')]]['yarn_dyeing']/$dzn_qnty)*$plan_cut_qnty;
						$yarn_dyeing_cost_dzn=$knit_arr[$row[csf('job_no')]]['yarn_dyeing'];
						$fabric_dyeing_cost=($knit_arr[$row[csf('job_no')]]['fabric_dyeing']/$dzn_qnty)*$plan_cut_qnty;
						$fabric_dyeing_cost_dzn=$knit_arr[$row[csf('job_no')]]['fabric_dyeing'];
						$heat_setting_cost=($knit_arr[$row[csf('job_no')]]['heat']/$dzn_qnty)*$plan_cut_qnty;
						$fabric_finish=($knit_arr[$row[csf('job_no')]]['fabric_finish']/$dzn_qnty)*$plan_cut_qnty;
						
						if($fabric_dyeing_cost<=0 && $yarn_dyeing_cost<=0)
						{
						$color_fab="red";
						}
						
						else
						{
						$color_fab="";	
						}
						if($yarn_costing<=0)
						{
						$color_yarn="red";
						}
						else
						{
						$color_yarn="";	
						}
						if($kniting_cost<=0)
						{
						$color_knit="red";
						}
						else
						{
						$color_knit="";	
						}
						if($fabric_finish<=0)
						{
						$color_finish="red";
						}
						else
						{
						$color_finish="";	
						}
						if($commercial_cost<=0)
						{
						$color_com="red";
						}
						else
						{
						$color_com="";	
						}
						
						$yarn_cost_percent=($yarn_costing/$total_order_value)*100;
						$total_yarn_cost_percent+=$yarn_cost_percent;
					 ?>
                     <td width="100" align="right"><a href="##" onClick="generate_pre_cost_report('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','precost_yarn_detail')"><? echo number_format($avg_rate,2); ?></a></td>
                     <td width="80" align="right" title="<? echo $yarn_costing; ?>" bgcolor="<? echo $color_yarn; ?>"><? echo number_format($yarn_costing,2); ?></td>
                     <td width="80" align="right" title="<? echo $yarn_cost_percent; ?>"><? echo number_format($yarn_cost_percent,2); ?></td>
                     <td width="100" align="right"><a href="##" onClick="generate_precost_fab_purchase_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $fab_source_id; ?>','fab_purchase_detail')"><? echo number_format($fab_purchase,2); ?></a></td>
                     <td width="80" title="<? echo $knit_cost_dzn; ?>" align="right"><? echo number_format($knit_cost_dzn,2); ?></td>
                     <td width="80" align="right" title="<? echo $tot_knit_cost; ?>"  bgcolor="<? echo $color_knit; ?>"><?
					 ?>
                     <a href="##" onClick="generate_pre_cost_knit_popup('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $cons_process; //$row[csf('style_ref_no')]; ?>','precost_knit_detail')"><? 
					 echo number_format($tot_knit_cost,2);
					  ?></a></td>
                     <td  width="100" align="right" title="<? echo number_format($yarn_dyeing_cost_dzn ,2); ?>" ><? echo number_format($yarn_dyeing_cost_dzn ,2); ?></td>
                     <td  width="110" align="right" title="<? echo number_format($yarn_dyeing_cost ,2); ?>" ><? echo number_format($yarn_dyeing_cost ,2); ?></td>
                     <td  width="120" align="right"  title="<? echo number_format($fabric_dyeing_cost_dzn ,2); ?>" ><? echo number_format($fabric_dyeing_cost_dzn,2); 
					 $total_fabrics_cost_summary+=($row[csf('po_quantity')]/$dzn_qnty)*$fabric_dyeing_cost_dzn;
					  ?></td>
                     <td  width="100" align="right" title="<? echo number_format($fabric_dyeing_cost ,2); ?>" bgcolor="<? echo $color_fab; ?>"><a href="##" onClick="generate_precost_fab_dyeing_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $fab_source_id; ?>','fab_dyeing_detail')"><? echo number_format($fabric_dyeing_cost,2); ?></a></td>
                     <td  width="90" align="right"><? echo number_format($heat_setting_cost,2); ?></td>
                     <td  width="100" align="right" ><a href="##" onClick="generate_precost_fab_finishing_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','fab_finishing_detail')"><? echo number_format($fabric_finish,2); ?></a> </td>
                     <td  width="90" align="right"><a href="##" onClick="generate_precost_fab_finishing_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','fab_washing_detail')"><? echo number_format($washing_cost,2); ?></a></td>
                     <td  width="90" align="right"><a href="##" onClick="generate_precost_fab_all_over_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','fab_all_over_detail')"><? echo number_format($all_over_cost,2); ?></a></td>
				<?
					$tot_trim_amount= $fabriccostArray[$row[csf('job_no')]]['trims_cost']/$dzn_qnty*$order_qty_pcs;
                    $tot_test_cost=$fabriccostArray[$row[csf('job_no')]]['lab_test']/$dzn_qnty*$order_qty_pcs;
                    $print_amount=($fab_emb[$row[csf('job_no')]]['print']/$dzn_qnty)*$order_qty_pcs;
                    $embroidery_amount=($fab_emb[$row[csf('job_no')]]['embroidery']/$dzn_qnty)*$order_qty_pcs;
                    $special_amount=($fab_emb[$row[csf('job_no')]]['special']/$dzn_qnty)*$order_qty_pcs;
					$wash_cost=($fab_emb[$row[csf('job_no')]]['wash']/$dzn_qnty)*$order_qty_pcs;
                    $other_amount=($fab_emb[$row[csf('job_no')]]['other']/$dzn_qnty)*$order_qty_pcs;
                    $foreign=$commission_array[$row[csf('job_no')]]['foreign']/$dzn_qnty*$order_qty_pcs;
                    $local=$commission_array[$row[csf('job_no')]]['local']/$dzn_qnty*$order_qty_pcs;
                    $freight_cost= $fabriccostArray[$row[csf('job_no')]]['freight']/$dzn_qnty*$order_qty_pcs;
                    $inspection=$fabriccostArray[$row[csf('job_no')]]['inspection']/$dzn_qnty*$order_qty_pcs;
                    $certificate_cost=$fabriccostArray[$row[csf('job_no')]]['certificate_pre_cost']/$dzn_qnty*$order_qty_pcs;
                    
                    $common_oh=$fabriccostArray[$row[csf('job_no')]]['common_oh']/$dzn_qnty*$order_qty_pcs;
                    $currier_cost=$fabriccostArray[$row[csf('job_no')]]['currier_pre_cost']/$dzn_qnty*$order_qty_pcs;
                    //echo $currier_cost;
                    $cm_cost=$fabriccostArray[$row[csf('job_no')]]['c_cost']/$dzn_qnty*$order_qty_pcs;
                    $cm_cost_dzn=$fabriccostArray[$row[csf('job_no')]]['c_cost'];
                    $total_cost=$yarn_costing+$fab_purchase+$tot_knit_cost+$washing_cost+$all_over_cost+$yarn_dyeing_cost+$fabric_dyeing_cost+$heat_setting_cost+$fabric_finish+$tot_trim_amount+$tot_test_cost+$print_amount+$embroidery_amount+$special_amount+$other_amount+$wash_cost+$tot_commercial_cost+$foreign+$local+$freight_cost+$inspection+$certificate_cost+$common_oh+$currier_cost+$cm_cost;
					//echo  $total_cost;
					$total_print_amount+=$print_amount;
					$total_embroidery_amount+=$embroidery_amount;
					$total_special_amount+=$special_amount;
					$total_other_amount+=$other_amount;
					$total_wash_cost+=$wash_cost;
					
					$total_foreign_amount+=$foreign;
					$total_local_amount+=$local;
					$total_test_cost_amount+=$tot_test_cost;
					$total_freight_amount+=$freight_cost;
					$total_inspection_amount+=$inspection;
					$total_certificate_amount+=$certificate_cost;
					
					$total_common_oh_amount+=$common_oh;
					$total_currier_amount+=$currier_cost;
					$total_cm_amount+=$cm_cost;
					$max_profit=$asking_profit_arr[$row[csf('company_name')]]['max_profit'];
					//echo $max_profit;
					$company_asking=$asking_profit_arr[$row[csf('company_name')]]['asking_profit'];
					
					if($tot_trim_amount<=0)
						{
						$color_trim="red";
						}
						else
						{
						$color_trim="";	
						}
						
					if($cm_cost<=0)
						{
						$color="red";
						}
						else
						{
						$color="";	
						}
						$yarnData=explode(",",substr($yarncostArray[$row[csf('job_no')]],0,-1));
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
						}
				//
					?>
                     <td width="100" align="right" bgcolor="<? echo $color_trim; ?>"><a href="##" onClick="generate_precost_trim_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','trim_cost_detail')"><? echo number_format($tot_trim_amount,2); ?></a></td>
                     <td width="80" align="right"><a href="##" onClick="generate_precost_embell_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','print_cost_detail')"><? echo number_format($print_amount,2); ?></a></td>
                     <td width="85" align="right"><a href="##" onClick="generate_precost_embell_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','embroidery_cost_detail')"><? echo number_format($embroidery_amount,2); ?></a></td>
                     <td width="80" align="right"><? echo number_format($special_amount,2); ?></td>
                     <td width="80" align="right"><a href="##" onClick="generate_precost_embell_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','wash_cost_detail')"><? echo number_format($wash_cost,2); ?></a></td>
                     <td width="80" align="right"><? echo number_format($other_amount,2); ?></td>
                     <td width="120" align="right" bgcolor="<? echo $color_com; ?>"><? echo number_format($tot_commercial_cost,2); ?></td>
                     <td width="120" align="right"><? echo number_format($foreign,2) ?></td>
                     <td width="120" align="right"><? echo number_format($local,2) ?></td>
                     <td width="100" align="right"><? echo number_format($tot_test_cost,2);?></td>
                     <td width="100" align="right"><? echo number_format($freight_cost,2); ?></td>
                     <td width="120" align="right"><? echo number_format($inspection,2);?></td>
                     <td width="100" align="right"><? echo number_format($certificate_cost,2); ?></td>
                     <td width="100" align="right"><? echo number_format($common_oh,2); ?></td>
                     <td width="100" align="right"><? echo number_format($currier_cost,2);?></td>
                     <td width="120" align="right"><? echo number_format($cm_cost_dzn,2);?></td>
                     <td width="100" align="right" bgcolor="<? echo $color; ?>"><? echo number_format($cm_cost,2);?></td>
                     <td width="100" align="right"><? echo number_format($total_cost,2); ?></td>
                    <?
						$total_profit=$total_order_value-$total_cost;
						$total_profit_percentage2=$total_profit/$total_order_value*100; 
						if($total_profit_percentage2<=0 )
						{
							$color_pl="red";
						}
						else if($total_profit_percentage2>$max_profit)
						{
							$color_pl="yellow";	
						}
						else if($total_profit_percentage2<=$max_profit)
						{
							$color_pl="green";	
						}
						else
						{
							$color_pl="";	
						}
					?>
                     <td width="100" align="right" bgcolor="<? echo $color_pl; ?>"><? echo number_format($total_profit,2); ?></td>
                     <td width="100" align="right"><? echo number_format($total_profit_percentage2,2); ?></td>
                     <td width="100" align="right"><?  $expected_profit=$asking_profit_arr[$row[csf('company_name')]]['asking_profit']*$total_order_value/100; echo number_format($expected_profit,2) //$total_profit=$total_cost-$total_order_value; //echo number_format($total_profit,2); ?></td>
                     <td width="" align="right"><? $expect_variance=$total_profit-$expected_profit; echo number_format($expect_variance,2)?></td>
                  </tr> 
                <?
				$total_order_qty+=$row[csf('po_quantity')];
				$total_plan_cut_qty+=$plan_cut_qnty;
				$total_yarn_dyeing_cost+=$yarn_dyeing_cost;
				$total_yarn_cost+=$yarn_costing;
				$total_purchase_cost+=$fab_purchase;
				$total_knitting_cost+=$tot_knit_cost;
				$total_fabric_dyeing_cost+=$fabric_dyeing_cost;
				$total_heat_setting_cost+=$heat_setting_cost;
				$total_finishing_cost+=$fabric_finish;
				$total_washing_cost+=$washing_cost;
				$all_over_print_cost+=$all_over_cost;
				$total_trim_cost+=$tot_trim_amount;
				$total_commercial_cost+=$tot_commercial_cost;
				$total_fab_cost_amount=$total_yarn_cost+$total_purchase_cost+$total_knitting_cost+$total_yarn_dyeing_cost+$total_fabric_dyeing_cost+$total_heat_setting_cost+$total_finishing_cost+$total_washing_cost+$all_over_print_cost;
				
				//echo $total_purchase_cost;
				//$total_fab_cost_amount2+=$total_fab_cost_amount;
				$total_embelishment_cost+=$print_amount+$embroidery_amount+$special_amount+$other_amount+$wash_cost;
				$total_commssion+=$foreign+$local;
				$total_testing_cost+=$tot_test_cost;
				$total_freight_cost+=$freight_cost;
				$total_cm_cost+=$cm_cost;
				$total_cost_up+=$total_cost;
				$total_inspection+=$inspection;
				$total_certificate_cost+=$certificate_cost;
				$total_common_oh+=$common_oh;
				$total_currier_cost+=$currier_cost;
				$total_fab_profit+=$total_profit;
				$total_expected_profit+=$expected_profit;
				$total_expt_profit_percentage+=$total_profit_percentage;
				$total_expect_variance+=$expect_variance;
				$total_profit_fab_percentage_up+=$total_profit_percentage2;
				//echo $total_fab_cost_amount;
				$i++;
				}
               ?>
                </table>
                <table class="rpt_table" width="4650" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <tfoot>
                     <th width="40"></th>
                     <th width="70"></th>
                     <th width="70"></th>
                     <th width="100"></th>
                     <th width="100"></th>
                     <th width="110"></th>
                     <th width="110"></th>
                     <th width="110"></th>
                     <th width="70"></th>
                     <th width="90" align="right" id="total_order_qnty"><? echo number_format($total_order_qty,2); ?></th>
                     <th width="90"></th>
                     <th width="100" align="right" id="total_order_amount2"><? echo number_format($total_order_amount,2); ?></th>
                     <th width="100"></th>
                     <th width="80" align="right" id="total_yarn_cost2"><? echo number_format($total_yarn_cost,2); ?></th>
                     <th width="80" align="right" ><?  $total_yarn_cost_percentage=$total_yarn_cost/$total_order_amount*100;  echo number_format($total_yarn_cost_percentage,2); ?></th>
                     <th width="100" align="right"><? echo number_format($total_purchase_cost,2); ?></th>
                     <th width="80"></th>
                     <th width="80" align="right"><? echo number_format($total_knitting_cost,2); ?></th>
                     <th width="100"></th>
                     <th width="110" align="right"><? echo number_format($total_yarn_dyeing_cost,2); ?></th>
                     <th width="120"><? ?></th>
                     <th width="100" align="right" id="total_fabric_dyeing_cost4"><? echo number_format($total_fabric_dyeing_cost,2); ?></th>
                     <th width="90" align="right"><? echo number_format($total_heat_setting_cost,2); ?></th>		
                     <th width="100" align="right"><? echo number_format($total_finishing_cost,2); ?></th>
                     <th width="90" align="right"><? echo number_format($total_washing_cost,2); ?></th>
                     <th width="90" align="right"><? echo number_format($all_over_print_cost,2); ?></th>
                     <th width="100" align="right"><strong><? echo number_format($total_trim_cost,2); ?></strong></th>
                     <th width="80" align="right"><strong><? echo number_format($total_print_amount,2); ?></strong></th>
                     <th width="85" align="right"><strong><? echo number_format($total_embroidery_amount,2); ?></strong></th>
                     <th width="80" align="right"><strong> <? echo number_format($total_special_amount,2); ?></strong></th>
                     <th width="80" align="right"><strong><? echo number_format($total_wash_cost,2); ?></strong></th>
                     <th width="80" align="right"><strong><? echo number_format($total_other_amount,2); ?></strong></th>
                     <th width="120" align="right"><strong><? echo number_format($total_commercial_cost,2); ?></strong></th>
                     <th width="120" align="right"><strong><? echo number_format($total_foreign_amount,2); ?></strong></th>
                     <th width="120" align="right"><strong><? echo number_format($total_local_amount,2); ?></strong></th>
                     <th width="100" align="right"><strong><? echo number_format($total_test_cost_amount,2); ?></strong></th>
                     <th width="100" align="right"><strong><? echo number_format($total_freight_amount,2); ?></strong></th>
                     <th width="120" align="right"><strong><? echo number_format($total_inspection_amount,2); ?></strong></th>
                     <th width="100" align="right"><strong><? echo number_format($total_certificate_amount,2); ?></strong></th>
                     <th width="100" align="right"><strong><? echo number_format($total_common_oh_amount,2); ?></strong></th>
                     <th width="100" align="right"><strong><? echo number_format($total_currier_amount,2); ?></strong></th>
                     <th width="120"></th>
                     <th width="100" align="right"><strong><? echo number_format($total_cm_amount,2); ?></strong></th>
                     <th width="100" id="total_cost_up" align="right"><strong><? echo number_format($total_cost_up,2); ?></strong></th>
                     <th width="100" align="right"><strong><? echo number_format($total_fab_profit,2);?></strong></th>
                     <th width="100" align="right"><strong><? $total_profit_fab_percentage=$total_fab_profit/$total_order_amount*100; echo number_format($total_profit_fab_percentage,2);?></strong></th>
                     <th width="100"  align="right"><strong><? echo number_format($total_expected_profit,2);?></strong></th>
                     <th width=""  align="right"><strong><? echo number_format($total_expect_variance,2);?></strong></th>
                  </tfoot>
                </table>
                <?
                $fab_percent=($total_fab_cost_amount*100)/$total_order_amount;
				$fab_percent=$fab_percent;
				$trim_percent=($total_trim_cost*100)/$total_order_amount;
				$trim_percent=$trim_percent;
				
				$embelishment_percent=($total_embelishment_cost*100)/$total_order_amount;
				$embelishment_percent=$embelishment_percent;
				$total_commercial_percent=($total_commercial_cost*100)/$total_order_amount;
				$total_commercial_percent=$total_commercial_percent;
				
				$total_commssion_percent=(($total_commssion*100)/$total_order_amount);
				$total_testing_cost_percent=(($total_testing_cost*100)/$total_order_amount);
				$total_freight_cost_percent=(($total_freight_cost*100)/$total_order_amount);
				$total_cost_percent=(($total_cost_up*100)/$total_order_amount);
				$total_cm_percent=(($total_cm_cost*100)/$total_order_amount);
				$total_order_amount_percent=(($total_order_amount*100)/$total_order_amount);
				
				$total_inspection_percent=(($total_inspection*100)/$total_order_amount);
				$total_certificate_percent=(($total_certificate_cost*100)/$total_order_amount);
				$total_common_oh_percent=(($total_common_oh*100)/$total_order_amount);
				$total_currier_cost_percent=(($total_currier_cost*100)/$total_order_amount);
				$all_tot_cost_percentage=$fab_percent+$trim_percent+$embelishment_percent+$total_commercial_percent+$total_commssion_percent+$total_testing_cost_percent+$total_freight_cost_percent+$total_cm_percent+$total_inspection_percent+$total_common_oh_percent+$total_currier_cost_percent;
				
				$total_expected_profit_percent=(($total_expected_profit*100)/$total_order_amount);
				$total_expected_profit_variance_percent=(($total_expect_variance*100)/$total_order_amount);
				
				?>
                  <input type="hidden" id="total_fab_cost" value="<? echo number_format($total_fab_cost_amount,2); ?>">
                  <input type="hidden" id="total_fab_percent" value="<? echo number_format($fab_percent,2)."%"; ?>">
                  <input type="hidden" id="total_trim_cost" value="<? echo number_format($total_trim_cost,2); ?>">
                  <input type="hidden" id="total_trim_percent" value="<? echo number_format($trim_percent,2)."%"; ?>">
                  <input type="hidden" id="total_embelishment_cost" value="<? echo number_format($total_embelishment_cost,2); ?>">
                  <input type="hidden" id="total_embelishment_percent" value="<? echo number_format($embelishment_percent,2)."%"; ?>">
                  <input type="hidden" id="total_commercial_cost" value="<? echo number_format($total_commercial_cost,2); ?>">
                  <input type="hidden" id="total_commercial_percent" value="<? echo number_format($total_commercial_percent,2)."%"; ?>">
                  <input type="hidden" id="total_commssion_cost" value="<? echo number_format($total_commssion,2); ?>">
                  <input type="hidden" id="total_commssion_percent" value="<? echo number_format($total_commssion_percent,2)."%"; ?>">
                  <input type="hidden" id="total_testing_cost" value="<? echo number_format($total_testing_cost,2); ?>">
                  <input type="hidden" id="total_testing_cost_percent" value="<? echo number_format($total_testing_cost_percent,2)."%"; ?>">
                  <input type="hidden" id="total_freight_cost" value="<? echo number_format($total_freight_cost,2); ?>">
                  <input type="hidden" id="total_freight_cost_percent" value="<? echo number_format($total_freight_cost_percent,2)."%"; ?>">
                  <input type="hidden" id="total_cost_up2" value="<? echo number_format($total_cost_up,2); ?>">
                  <input type="hidden" id="total_cost_percent" value="<? echo number_format($all_tot_cost_percentage,2)."%"; ?>">
                  <input type="hidden" id="total_cm_cost" value="<? echo number_format($total_cm_cost,2); ?>">
                  <input type="hidden" id="total_cm_percent" value="<? echo number_format($total_cm_percent,2)."%"; ?>">
                  <input type="hidden" id="total_order_amount" value="<? echo number_format($total_order_amount,2); ?>">
                  <input type="hidden" id="total_order_amount_percent" value="<? echo number_format($total_order_amount_percent,2)."%"; ?>">
                  <input type="hidden" id="total_inspection" value="<? echo number_format($total_inspection,2); ?>">
                  <input type="hidden" id="total_inspection_percent" value="<? echo number_format($total_inspection_percent,2)."%"; ?>">
                  <input type="hidden" id="total_certificate_cost" value="<? echo number_format($total_certificate_cost,2); ?>">
                  <input type="hidden" id="total_certificate_percent" value="<? echo number_format($total_certificate_percent,2)."%"; ?>">
                  <input type="hidden" id="total_common_oh" value="<? echo number_format($total_common_oh,2); ?>">
                  <input type="hidden" id="total_common_oh_percent" value="<? echo number_format($total_common_oh_percent,2)."%"; ?>">
                 
                  <input type="hidden" id="total_currier_cost" value="<? echo number_format($total_currier_cost,2); ?>">
                  <input type="hidden" id="total_currier_cost_percent" value="<? echo number_format($total_currier_cost_percent,2)."%"; ?>">
                  
                  <input type="hidden" id="total_fab_profit_id" value="<? echo number_format($total_fab_profit,2); ?>">
                  <input type="hidden" id="total_expected_profit_id" value="<? echo number_format($total_expected_profit,2); ?>">
                  <input type="hidden" id="total_expt_profit_variance_id" value="<? echo number_format($total_expect_variance,2); ?>">
                  
                   <input type="hidden" id="total_profit_fab_percentage_id" value="<? echo number_format($total_profit_fab_percentage,2)."%"; ?>">
                   <input type="hidden" id="total_expt_profit_percentage_id" value="<? echo number_format($total_expected_profit_percent,2)."%"; ?>">
                   <input type="hidden" id="total_expt_profit_variance_percentage_id" value="<? echo number_format($total_expected_profit_variance_percent,2)."%"; ?>">
                   <input type="hidden" id="expected_profit_percent" value="<? echo '('.$company_asking.'%'.')'; ?>">
                  
            </div>
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
					//echo $total_fabric_profit_up;
					$chart_data_qnty="Fabric Cost;".$total_fab_cost."\nTrimCost;".$total_trim_cost."\nEmbelishment Cost;".$total_embelishment_cost."\nCommercial Cost;".$total_commercial_cost."\nCommission Cost;".$total_commssion."\nTesting Cost;".$total_testing_cost."\nFreightCost;".$total_freight_cost."\nCM Cost;".$total_cm_cost."\nInspection Cost;".$total_inspection."\nCertificate Cost;".$total_certificate_cost."\nCommn OH Cost;".$total_common_oh."\nCurrier Cost;".$total_currier_cost."\n Profit/Loss;".$total_fabric_profit_up."\n";
					 
					?>
                    <input type="hidden" id="graph_data" value="<? echo substr($chart_data_qnty,0,-1); ?>"/>
                </tr>
            </table>
             <table>
                <tr><td height="15"></td></tr>
            </table>
           <a id="displayText" href="javascript:toggle();">Show Yarn Summary</a>
            <div style="width:600px; display:none" id="yarn_summary" >
            <div id="data_panel2" align="center" style="width:500px">
                 <input type="button" value="Print Preview" class="formbutton" style="width:100px" name="print" id="print" onClick="new_window(1)" />
            </div>
            <table width="500">
                    <tr class="form_caption">
                        <td colspan="6" align="center"><strong>Yarn Cost Summary </strong></td>
                    </tr>
            </table>
            <table class="rpt_table" width="500" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <th width="30">SL</th>
                    <th width="80">Yarn Count</th>
                    <th width="120">Type</th>
                    <th width="120">Req. Qnty</th>
                    <th width="80">Avg. rate</th>
                    <th>Amount</th>
                </thead>
                <?
                $s=1; $tot_yarn_req_qnty=0; $tot_yarn_req_amnt=0;
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
        	</div>
		</fieldset>
	</div>
<?
			}
	} //Budget end
	else if($report_type==2)
	{
					if($template==1)
					{
					ob_start();
					$style1="#E9F3FF"; 
					$style="#FFFFFF";
 
 					$fab_precost_arr=array();$p_fab_precost_arr=array();$commission_array=array();$price_commission_array=array();$knit_arr=array();$pq_knit_arr=array(); $fabriccostArray=array(); $price_fabriccostArray=array(); $fab_emb=array();$price_fab_emb=array();$fabric_data_Array=array(); $price_fabric_data_Array=array();$price_costing_perArray=array();$asking_profit_arr=array(); $yarncostArray=array(); $yarn_desc_array=array();
						
					$yarncostDataArray=sql_select("select job_no, count_id, type_id, sum(cons_qnty) as cons_qnty, sum(amount) as amount from wo_pre_cost_fab_yarn_cost_dtls where status_active=1 and is_deleted=0 group by job_no, count_id, type_id");
					foreach($yarncostDataArray as $yarnRow)
					{
					   $yarncostArray[$yarnRow[csf('job_no')]].=$yarnRow[csf('count_id')]."**".$yarnRow[csf('type_id')]."**".$yarnRow[csf('cons_qnty')]."**".$yarnRow[csf('amount')].",";
					}
					$asking_profit=sql_select("select id,company_id,asking_profit,max_profit from lib_standard_cm_entry where status_active=1 and is_deleted=0  $date_max_profit");
					foreach($asking_profit as $ask_row )
					{
					$asking_profit_arr[$ask_row[csf('company_id')]]['asking_profit']=$ask_row[csf('asking_profit')];
					$asking_profit_arr[$ask_row[csf('company_id')]]['max_profit']=$ask_row[csf('max_profit')];
					} //var_dump($asking_profit_arr);
					$fab_arr=sql_select("select a.job_no,a.pre_cost_fabric_cost_dtls_id, a.po_break_down_id, sum(a.requirment) as requirment ,sum(a.pcs) as pcs from wo_pre_cos_fab_co_avg_con_dtls a,wo_pre_cost_fabric_cost_dtls b where a.pre_cost_fabric_cost_dtls_id=b.id and a.job_no=b.job_no  and b.status_active=1 and b.is_deleted=0 group by a.po_break_down_id,a.pre_cost_fabric_cost_dtls_id,a.job_no");
					foreach($fab_arr as $row_pre)
					{
					$fab_precost_arr[$row_pre[csf('job_no')]][$row_pre[csf('po_break_down_id')]].=$row_pre[csf('requirment')]."**".$row_pre[csf('pcs')].",";	
					}//pre cost purchase cost end
					$pri_fab_arr=sql_select("select a.quotation_id,a.wo_pri_quo_fab_co_dtls_id, sum(a.requirment) as requirment ,sum(a.pcs) as pcs from wo_pri_quo_fab_co_avg_con_dtls a,wo_pri_quo_fabric_cost_dtls  b where a.wo_pri_quo_fab_co_dtls_id=b.id and a.quotation_id=b.quotation_id  and b.status_active=1 and b.is_deleted=0 group by a.quotation_id,a.wo_pri_quo_fab_co_dtls_id");
					foreach($pri_fab_arr as $p_row_pre)
					{
					$p_fab_precost_arr[$p_row_pre[csf('quotation_id')]].=$p_row_pre[csf('requirment')]."**".$p_row_pre[csf('pcs')].",";	
					}
					
					$fabricDataArray=sql_select("select a.job_no, a.fab_nature_id, a.fabric_source, a.rate, b.yarn_cons_qnty, b.yarn_amount from wo_pre_cost_fabric_cost_dtls a, wo_pre_cost_sum_dtls b where a.job_no=b.job_no and a.fabric_source!=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
					foreach($fabricDataArray as $fabricRow)
					{
					$fabric_data_Array[$fabricRow[csf('job_no')]].=$fabricRow[csf('fab_nature_id')]."**".$fabricRow[csf('fabric_source')]."**".$fabricRow[csf('rate')]."**".$fabricRow[csf('yarn_cons_qnty')]."**".$fabricRow[csf('yarn_amount')].",";
					} //Pre cost end
					 $price_costDataArray=sql_select("select  id,costing_per  from wo_price_quotation where status_active=1 and is_deleted=0  ");
					foreach($price_costDataArray as $pri_fabRow)
					{
					 $price_costing_perArray[$pri_fabRow[csf('id')]]['costing_per']=$pri_fabRow[csf('costing_per')];
					
					}
				
					$price_fabricDataArray=sql_select("select a.quotation_id, a.fab_nature_id, a.fabric_source, a.rate, b.yarn_cons_qnty, b.yarn_amount from wo_pri_quo_fabric_cost_dtls a, wo_pri_quo_sum_dtls b where a.quotation_id=b.quotation_id and a.fabric_source!=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
					foreach($price_fabricDataArray as $price_fabricRow)
					{
					$price_fabric_data_Array[$price_fabricRow[csf('quotation_id')]].=$price_fabricRow[csf('fab_nature_id')]."**".$price_fabricRow[csf('fabric_source')]."**".$price_fabricRow[csf('rate')]."**".$price_fabricRow[csf('yarn_cons_qnty')]."**".$price_fabricRow[csf('yarn_amount')].",";
					} //var_dump($price_fabric_data_Array);
					
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
					 }  //Pre Cost embel end
					  $price_data_array_emb=("select  quotation_id,
					 sum(CASE WHEN emb_name=1 THEN amount END) AS print_amount,
					 sum(CASE WHEN emb_name=2 THEN amount END) AS embroidery_amount,
					 sum(CASE WHEN emb_name=3 THEN amount END) AS wash_amount,
					 sum(CASE WHEN emb_name=4 THEN amount END) AS special_amount,
					 sum(CASE WHEN emb_name=5 THEN amount END) AS other_amount
					 from  wo_pri_quo_embe_cost_dtls where  status_active=1  and is_deleted=0 group by quotation_id");
					 $sql_embl_array=sql_select($price_data_array_emb);
					foreach($sql_embl_array as $p_row_emb)
					 {
					 $price_fab_emb[$p_row_emb[csf('quotation_id')]]['print']=$p_row_emb[csf('print_amount')];
					 $price_fab_emb[$p_row_emb[csf('quotation_id')]]['embroidery']=$p_row_emb[csf('embroidery_amount')];
					 $price_fab_emb[$p_row_emb[csf('quotation_id')]]['special']=$p_row_emb[csf('special_amount')];
					 $price_fab_emb[$p_row_emb[csf('quotation_id')]]['other']=$p_row_emb[csf('other_amount')];
					 $price_fab_emb[$p_row_emb[csf('quotation_id')]]['wash']=$p_row_emb[csf('wash_amount')];
					 } //var_dump($price_fab_emb);
					 $fabriccostDataArray=sql_select("select job_no, costing_per_id, trims_cost, embel_cost, cm_cost, commission, common_oh, lab_test, inspection, freight, comm_cost,certificate_pre_cost,currier_pre_cost from wo_pre_cost_dtls where status_active=1 and is_deleted=0  ");
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
					
					$p_fabriccostDataArray=sql_select("select quotation_id, costing_per_id, trims_cost, embel_cost, cm_cost, commission, common_oh, lab_test, inspection, freight, comm_cost,certificate_pre_cost,currier_pre_cost from wo_price_quotation_costing_mst where status_active=1 and is_deleted=0  ");
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
					} //Pre cost knit charge end
					$pq_knit_data=sql_select("select quotation_id,
					  sum(CASE WHEN cons_type=1 THEN amount END) AS knit_charge,
					  sum(CASE WHEN cons_type=2 THEN amount END) AS weaving_charge,
					  sum(CASE WHEN cons_type=3 THEN amount END) AS knit_charge_collar_cuff,
					  sum(CASE WHEN cons_type=4 THEN amount END) AS knit_charge_feeder_stripe,
					  sum(CASE WHEN cons_type in(64,82,89) THEN amount END) AS washing_cost,
					  sum(CASE WHEN cons_type in(35,36,37) THEN amount END) AS all_over_cost,
					  sum(CASE WHEN cons_type=30 THEN amount END) AS yarn_dyeing_cost,
					  sum(CASE WHEN cons_type=33 THEN amount END) AS heat_setting_cost,
					  sum(CASE WHEN cons_type in(25,31,32,60,61,62,63,72,80,81,84,85,86,87,38,74,78,79) THEN amount END) AS fabric_dyeing_cost,
					  sum(CASE WHEN cons_type in(34,65,66,67,68,69,70,71,73,75,76,77,88,90,91,92,93,100,125,127,128,129) THEN amount END) AS fabric_finish_cost
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
					$data_array=sql_select("select  job_no,
					 sum(CASE WHEN particulars_id=1 THEN commission_amount END) AS foreign_comm,
					 sum(CASE WHEN particulars_id=2 THEN commission_amount END) AS local_comm
					 from  wo_pre_cost_commiss_cost_dtls where status_active=1 and is_deleted=0 group by job_no");// quotation_id='$data'
					 foreach($data_array as $row_fl )
					{
						$commission_array[$row_fl[csf('job_no')]]['foreign']=$row_fl[csf('foreign_comm')];
						$commission_array[$row_fl[csf('job_no')]]['local']=$row_fl[csf('local_comm')];
					} //Pre Cost Commission end
					$p_data_array=sql_select("select  quotation_id,
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
		var total_fab_cost=document.getElementById('total_fab_cost').value;
		var total_fab_percent=document.getElementById('total_fab_percent').value;
		document.getElementById('fab_cost').innerHTML=total_fab_cost;
		document.getElementById('fab_percent').innerHTML=total_fab_percent;
		
		var total_trim_cost=document.getElementById('total_trim_cost').value;
		var total_trim_percent=document.getElementById('total_trim_percent').value;
		document.getElementById('trim_cost_id').innerHTML=total_trim_cost;
		document.getElementById('trim_percent').innerHTML=total_trim_percent;
		
		var total_embelishment_cost=document.getElementById('total_embelishment_cost').value;
		var total_embelishment_percent=document.getElementById('total_embelishment_percent').value;
		document.getElementById('embelishment_id').innerHTML=total_embelishment_cost;
		document.getElementById('embelishment_percent').innerHTML=total_embelishment_percent;
		
		var total_commercial_cost=document.getElementById('total_commercial_cost').value;
		var total_commercial_percent=document.getElementById('total_commercial_percent').value;
		document.getElementById('commercial_id').innerHTML=total_commercial_cost;
		document.getElementById('commercial_percent').innerHTML=total_commercial_percent;
		
		var total_commssion_cost=document.getElementById('total_commssion_cost').value;
		var total_commssion_percent=document.getElementById('total_commssion_percent').value;
		document.getElementById('commission_id').innerHTML=total_commssion_cost;
		document.getElementById('commission_percent').innerHTML=total_commssion_percent;
		
		var total_testing_cost=document.getElementById('total_testing_cost').value;
		var total_testing_cost_percent=document.getElementById('total_testing_cost_percent').value;
		document.getElementById('testing_id').innerHTML=total_testing_cost;
		document.getElementById('testing_percent').innerHTML=total_testing_cost_percent;
		var total_freight_cost=document.getElementById('total_freight_cost').value;
		var total_freight_cost_percent=document.getElementById('total_freight_cost_percent').value;
		document.getElementById('freight_id').innerHTML=total_freight_cost;
		document.getElementById('freight_percent').innerHTML=total_freight_cost_percent;
		var total_cost_up=document.getElementById('total_cost_up2').value;
		
		document.getElementById('cost_id').innerHTML=total_cost_up;
		
		var total_cm_cost=document.getElementById('total_cm_cost').value;
		var total_cm_percent=document.getElementById('total_cm_percent').value;
		document.getElementById('cm_id').innerHTML=total_cm_cost;
		document.getElementById('cm_percent').innerHTML=total_cm_percent;
		var total_order_amount=document.getElementById('total_order_amount').value;
		var total_order_amount_percent=document.getElementById('total_order_amount_percent').value;
		document.getElementById('order_id').innerHTML=total_order_amount;
		document.getElementById('order_percent').innerHTML=total_order_amount_percent;
		var total_inspection=document.getElementById('total_inspection').value;
		var total_inspection_percent=document.getElementById('total_inspection_percent').value;
		document.getElementById('inspection_id').innerHTML=total_inspection;
		document.getElementById('inspection_percent').innerHTML=total_inspection_percent;
		var total_certificate_cost=document.getElementById('total_certificate_cost').value;
		var total_certificate_percent=document.getElementById('total_certificate_percent').value;
		document.getElementById('certificate_id').innerHTML=total_certificate_cost;
		document.getElementById('certificate_percent').innerHTML=total_certificate_percent;
		var total_common_oh=document.getElementById('total_common_oh').value;
		var total_common_oh_percent=document.getElementById('total_common_oh_percent').value;
		document.getElementById('commn_id').innerHTML=total_common_oh;
		document.getElementById('commn_percent').innerHTML=total_common_oh_percent;
		
		var total_common_oh=document.getElementById('total_common_oh').value;
		var total_common_oh_percent=document.getElementById('total_common_oh_percent').value;
		document.getElementById('commn_id').innerHTML=total_common_oh;
		document.getElementById('commn_percent').innerHTML=total_common_oh_percent;
		var total_currier_cost=document.getElementById('total_currier_cost').value;
		var total_currier_cost_percent=document.getElementById('total_currier_cost_percent').value;
		document.getElementById('courier_id').innerHTML=total_currier_cost;
		document.getElementById('courier_percent').innerHTML=total_currier_cost_percent;
		
		var total_fab_profit_id=document.getElementById('total_fab_profit_id').value;
		var total_expected_profit_id=document.getElementById('total_expected_profit_id').value;
		document.getElementById('fab_profit_id').innerHTML=total_fab_profit_id;
		document.getElementById('expected_id').innerHTML=total_expected_profit_id;
		var total_expt_profit_variance=document.getElementById('total_expt_profit_variance_id').value;
		
		document.getElementById('expt_p_variance_id').innerHTML=total_expt_profit_variance;
		var total_cost_percent=document.getElementById('total_cost_percent').value;
		document.getElementById('cost_percent').innerHTML=total_cost_percent;
		var total_profit_fab_percentage=document.getElementById('total_profit_fab_percentage_id').value;
		document.getElementById('profit_fab_percentage').innerHTML=total_profit_fab_percentage;
		var total_expt_profit_percentage=document.getElementById('total_expt_profit_percentage_id').value;
		document.getElementById('profit_expt_fab_percentage').innerHTML=total_expt_profit_percentage;
		var total_expt_profit_percentage=document.getElementById('total_expt_profit_variance_percentage_id').value;
		document.getElementById('expt_p_percent').innerHTML=total_expt_profit_percentage;
		var expected_profit_percent=document.getElementById('expected_profit_percent').value;
		document.getElementById('expt_percent').innerHTML=expected_profit_percent;
		var total_p_fab_cost=document.getElementById('total_p_fab_cost').value;
		var total_p_fab_percent=document.getElementById('total_p_fab_percent').value;
		document.getElementById('p_fab_cost').innerHTML=total_p_fab_cost;
		document.getElementById('p_fab_percent').innerHTML=total_p_fab_percent;
		
		var total_p_trim_cost=document.getElementById('total_p_trim_cost').value;
		var total_p_trim_percent=document.getElementById('total_p_trim_percent').value;
		document.getElementById('p_trim_cost_id').innerHTML=total_p_trim_cost;
		document.getElementById('p_trim_percent').innerHTML=total_p_trim_percent;
		
		var total_p_embelishment_cost=document.getElementById('total_p_embelishment_cost').value;
		var total_p_embelishment_percent=document.getElementById('total_p_embelishment_percent').value;
		document.getElementById('p_embelishment_id').innerHTML=total_p_embelishment_cost;
		document.getElementById('p_embelishment_percent').innerHTML=total_p_embelishment_percent;
		
		var total_p_commercial_cost=document.getElementById('total_p_commercial_cost').value;
		var total_p_commercial_percent=document.getElementById('total_p_commercial_percent').value;
		document.getElementById('p_commercial_id').innerHTML=total_p_commercial_cost;
		document.getElementById('p_commercial_percent').innerHTML=total_p_commercial_percent;
		
		var total_p_commssion_cost=document.getElementById('total_p_commssion_cost').value;
		var total_p_commssion_percent=document.getElementById('total_p_commssion_percent').value;
		document.getElementById('p_commission_id').innerHTML=total_p_commssion_cost;
		document.getElementById('p_commission_percent').innerHTML=total_p_commssion_percent;
		
		var total_p_testing_cost=document.getElementById('total_p_testing_cost').value;
		var total_p_testing_cost_percent=document.getElementById('total_p_testing_cost_percent').value;
		document.getElementById('p_testing_id').innerHTML=total_p_testing_cost;
		document.getElementById('p_testing_percent').innerHTML=total_p_testing_cost_percent;
		
		var total_p_freight_cost=document.getElementById('total_p_freight_cost').value;
		var total_p_freight_cost_percent=document.getElementById('total_p_freight_cost_percent').value;
		document.getElementById('p_freight_id').innerHTML=total_p_freight_cost;
		document.getElementById('p_freight_percent').innerHTML=total_p_freight_cost_percent;
		var total_p_cost_up=document.getElementById('total_p_cost_up2').value;
		document.getElementById('p_cost_id').innerHTML=total_p_cost_up;
		
		var total_p_cm_cost=document.getElementById('total_p_cm_cost').value;
		var total_p_cm_percent=document.getElementById('total_p_cm_percent').value;
		document.getElementById('p_cm_id').innerHTML=total_p_cm_cost;
		document.getElementById('p_cm_percent').innerHTML=total_p_cm_percent;
		var total_p_order_amount=document.getElementById('total_p_order_amount').value;
		var total_p_order_amount_percent=document.getElementById('total_p_order_amount_percent').value;
		document.getElementById('p_order_id').innerHTML=total_p_order_amount;
		document.getElementById('p_order_percent').innerHTML=total_p_order_amount_percent;
		var total_p_inspection=document.getElementById('total_p_inspection').value;
		var total_p_inspection_percent=document.getElementById('total_p_inspection_percent').value;
		document.getElementById('p_inspection_id').innerHTML=total_p_inspection;
		document.getElementById('p_inspection_percent').innerHTML=total_p_inspection_percent;
		var total_p_certificate_cost=document.getElementById('total_p_certificate_cost').value;
		var total_p_certificate_percent=document.getElementById('total_p_certificate_percent').value;
		document.getElementById('p_certificate_id').innerHTML=total_p_certificate_cost;
		document.getElementById('p_certificate_percent').innerHTML=total_p_certificate_percent;
		var total_p_common_oh=document.getElementById('total_p_common_oh').value;
		var total_p_common_oh_percent=document.getElementById('total_p_common_oh_percent').value;
		document.getElementById('p_commn_id').innerHTML=total_p_common_oh;
		document.getElementById('p_commn_percent').innerHTML=total_p_common_oh_percent;
		
		var total_p_common_oh=document.getElementById('total_p_common_oh').value;
		var total_common_p_oh_percent=document.getElementById('total_p_common_oh_percent').value;
		document.getElementById('p_commn_id').innerHTML=total_p_common_oh;
		document.getElementById('p_commn_percent').innerHTML=total_p_common_oh_percent;
		var total_p_currier_cost=document.getElementById('total_p_currier_cost').value;
		var total_p_currier_cost_percent=document.getElementById('total_p_currier_cost_percent').value;
		document.getElementById('p_courier_id').innerHTML=total_p_currier_cost;
		document.getElementById('p_courier_percent').innerHTML=total_p_currier_cost_percent;
		
		var total_p_fab_profit_id=document.getElementById('total_p_fab_profit_id').value;
		var total_p_expected_profit_id=document.getElementById('total_p_expected_profit_id').value;
		document.getElementById('p_fab_profit_id').innerHTML=total_p_fab_profit_id;
		document.getElementById('p_expected_id').innerHTML=total_p_expected_profit_id;
		var total_p_expt_profit_variance=document.getElementById('total_p_expt_profit_variance_id').value;
		document.getElementById('p_expt_variance_id').innerHTML=total_p_expt_profit_variance;
		var total_p_cost_percent=document.getElementById('total_p_cost_percent').value;
		document.getElementById('p_cost_percent').innerHTML=total_p_cost_percent;
		var total_p_profit_fab_percentage=document.getElementById('total_p_profit_fab_percentage_id').value;
		document.getElementById('p_profit_fab_percentage').innerHTML=total_p_profit_fab_percentage;
		var total_p_expt_profit_percentage=document.getElementById('total_p_expt_profit_percentage_id').value;
		document.getElementById('p_profit_expt_fab_percentage').innerHTML=total_p_expt_profit_percentage;
		var total_p_expt_profit_percentage=document.getElementById('total_p_expt_profit_variance_percentage_id').value;
		document.getElementById('p_expt_percent').innerHTML=total_p_expt_profit_percentage;
		var p_expected_profit_percent=document.getElementById('p_expected_profit_percent').value;
		document.getElementById('p_expt_percent').innerHTML=p_expected_profit_percent;
		
		var total_v_fab_cost=document.getElementById('total_v_fab_cost').value;
		var total_v_fab_percent=document.getElementById('total_v_fab_percent').value;
		document.getElementById('v_fab_cost').innerHTML=total_v_fab_cost;
		document.getElementById('v_fab_percent').innerHTML=total_v_fab_percent;
		
		var total_v_trim_cost=document.getElementById('total_v_trim_cost').value;
		var total_v_trim_percent=document.getElementById('total_v_trim_percent').value;
		document.getElementById('v_trim_cost_id').innerHTML=total_v_trim_cost;
		document.getElementById('v_trim_percent').innerHTML=total_v_trim_percent;
		var total_v_embelishment_cost=document.getElementById('total_v_embelishment_cost').value;
		var total_v_embelishment_percent=document.getElementById('total_v_embelishment_percent').value;
		document.getElementById('v_embelishment_id').innerHTML=total_v_embelishment_cost;
		document.getElementById('v_embelishment_percent').innerHTML=total_v_embelishment_percent;
		
		var total_v_commercial_cost=document.getElementById('total_v_commercial_cost').value;
		var total_v_commercial_percent=document.getElementById('total_v_commercial_percent').value;
		document.getElementById('v_commercial_id').innerHTML=total_v_commercial_cost;
		document.getElementById('v_commercial_percent').innerHTML=total_v_commercial_percent;
		
		var total_v_commssion_cost=document.getElementById('total_v_commssion_cost').value;
		var total_v_commssion_percent=document.getElementById('total_v_commssion_percent').value;
		document.getElementById('v_commission_id').innerHTML=total_v_commssion_cost;
		document.getElementById('v_commission_percent').innerHTML=total_v_commssion_percent;
		
		var total_v_testing_cost=document.getElementById('total_v_testing_cost').value;
		var total_v_testing_cost_percent=document.getElementById('total_v_testing_cost_percent').value;
		document.getElementById('v_testing_id').innerHTML=total_v_testing_cost;
		document.getElementById('v_testing_percent').innerHTML=total_v_testing_cost_percent;
		
		var total_v_freight_cost=document.getElementById('total_v_freight_cost').value;
		var total_v_freight_cost_percent=document.getElementById('total_v_freight_cost_percent').value;
		document.getElementById('v_freight_id').innerHTML=total_v_freight_cost;
		document.getElementById('v_freight_percent').innerHTML=total_v_freight_cost_percent;
		var total_v_cost_up=document.getElementById('total_v_cost_up2').value;
		
		document.getElementById('v_cost_id').innerHTML=total_v_cost_up;
		
		
		var total_v_cm_cost=document.getElementById('total_v_cm_cost').value;
		var total_v_cm_percent=document.getElementById('total_v_cm_percent').value;
		document.getElementById('v_cm_id').innerHTML=total_v_cm_cost;
		document.getElementById('v_cm_percent').innerHTML=total_v_cm_percent;
		var total_v_order_amount=document.getElementById('total_v_order_amount').value;
		var total_v_order_amount_percent=document.getElementById('total_v_order_amount_percent').value;
		document.getElementById('v_order_id').innerHTML=total_v_order_amount;
		
		//error start
		
		document.getElementById('v_order_percent').innerHTML=total_v_order_amount_percent;
		var total_v_inspection=document.getElementById('total_v_inspection').value;
		var total_v_inspection_percent=document.getElementById('total_v_inspection_percent').value;
		document.getElementById('v_inspection_id').innerHTML=total_v_inspection;
		document.getElementById('v_inspection_percent').innerHTML=total_v_inspection_percent;
		var total_v_certificate_cost=document.getElementById('total_v_certificate_cost').value;
		var total_v_certificate_percent=document.getElementById('total_v_certificate_percent').value;
		document.getElementById('v_certificate_id').innerHTML=total_v_certificate_cost;
		document.getElementById('v_certificate_percent').innerHTML=total_v_certificate_percent;
		var total_v_common_oh=document.getElementById('total_v_common_oh').value;
		var total_v_common_oh_percent=document.getElementById('total_v_common_oh_percent').value;
		document.getElementById('v_commn_id').innerHTML=total_v_common_oh;
		document.getElementById('v_commn_percent').innerHTML=total_v_common_oh_percent;
		
		var total_v_common_oh=document.getElementById('total_v_common_oh').value;
		var total_common_v_oh_percent=document.getElementById('total_v_common_oh_percent').value;
		document.getElementById('v_commn_id').innerHTML=total_v_common_oh;
		document.getElementById('v_commn_percent').innerHTML=total_v_common_oh_percent;
		var total_v_currier_cost=document.getElementById('total_v_currier_cost').value;
		var total_v_currier_cost_percent=document.getElementById('total_v_currier_cost_percent').value;
		document.getElementById('v_courier_id').innerHTML=total_v_currier_cost;
		document.getElementById('v_courier_percent').innerHTML=total_v_currier_cost_percent;
		
		var total_v_fab_profit_id=document.getElementById('total_v_fab_profit_id').value;
		var total_v_expected_profit_id=document.getElementById('total_v_expected_profit_id').value;
		document.getElementById('v_fab_profit_id').innerHTML=total_v_fab_profit_id;
		document.getElementById('v_expected_id').innerHTML=total_v_expected_profit_id;
		var total_v_expt_profit_variance=document.getElementById('total_v_expt_profit_variance_id').value;
		document.getElementById('v_expt_variance_id').innerHTML=total_v_expt_profit_variance;
		var total_v_cost_percent=document.getElementById('total_v_cost_percent').value;
		document.getElementById('v_cost_percent').innerHTML=total_v_cost_percent;
		var total_v_profit_fab_percentage=document.getElementById('total_v_profit_fab_percentage_id').value;
		document.getElementById('v_profit_fab_percentage').innerHTML=total_v_profit_fab_percentage;
		var total_v_expt_profit_percentage=document.getElementById('total_v_expt_profit_percentage_id').value;
		document.getElementById('v_profit_expt_fab_percentage').innerHTML=total_v_expt_profit_percentage;
		var total_v_expt_profit_percentage=document.getElementById('total_v_expt_profit_variance_percentage_id').value;
		document.getElementById('v_expt_percent').innerHTML=total_v_expt_profit_percentage;
		var v_expected_profit_percent=document.getElementById('v_expected_profit_percent').value;
		document.getElementById('v_expt_percent').innerHTML=v_expected_profit_percent;
		
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
        <div style="width:4570px;">
        <div style="width:900px;" align="left">
        	<table width="900" cellpadding="0" cellspacing="2" border="0">
                <tr>
                	<td width="650" align="left">
                    	<table width="550" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="2">
                        <caption><strong>Price Quotation Vs Budget Variance</strong></caption>
                        <thead align="center">
                        <th>SL</th><th>Particulars</th><th>Quoted Amount</th><th>% On Order Value</th><th>Budget Amount</th><th>% On Order Value</th><th>Variance</th><th>% On Order Value</th>
                        </thead>
                        <tr bgcolor="<? echo $style1; ?>">
                        <td width="20">1</td>
                        <td width="100">Fabric Cost</td>
                        <td width="120" align="right" id="p_fab_cost"></td>
                        <td width="80" align="right" id="p_fab_percent"></td>
                        <td width="120" align="right" id="fab_cost"></td>
                        <td width="80" align="right" id="fab_percent"></td>
                        <td width="100" align="right" id="v_fab_cost"></td>
                        <td width="80" align="right" id="v_fab_percent"></td>
                        </tr>
                        <tr bgcolor="<?  echo $style; ?>">
                        <td width="20">2</td>
                        <td width="100">Trims Cost</td>
                        <td align="right" id="p_trim_cost_id"></td>
                        <td align="right" id="p_trim_percent"> </td>
                        <td align="right" id="trim_cost_id"></td>
                        <td align="right" id="trim_percent"> </td>
                        <td align="right" id="v_trim_cost_id"></td>
                        <td align="right" id="v_trim_percent"> </td>
                        </tr>
                        <tr bgcolor="<? echo $style1; ?>">
                        <td width="20">3</td>
                        <td width="100">Embellish Cost</td>
                        <td align="right" id="p_embelishment_id"></td>
                        <td align="right" id="p_embelishment_percent"> </td>
                        <td align="right" id="embelishment_id"></td>
                        <td align="right" id="embelishment_percent"> </td>
                        <td align="right" id="v_embelishment_id"></td>
                        <td align="right" id="v_embelishment_percent"> </td>
                        </tr>
                        <tr bgcolor="<? echo $style; ?>">
                        <td width="20">4</td>
                        <td width="100">Commercial Cost</td>
                        <td align="right" id="p_commercial_id"></td>
                        <td align="right" id="p_commercial_percent"> </td>
                        <td align="right" id="commercial_id"></td>
                        <td align="right" id="commercial_percent"> </td>
                        <td align="right" id="v_commercial_id"></td>
                        <td align="right" id="v_commercial_percent"> </td>
                        </tr>
                        <tr bgcolor="<? echo $style1; ?>">
                        <td width="20">5</td>
                        <td width="100">Commision Cost</td>
                        <td align="right" id="p_commission_id"></td>
                        <td align="right" id="p_commission_percent"> </td>
                        <td align="right" id="commission_id"></td>
                        <td align="right" id="commission_percent"> </td>
                         
                         <td align="right" id="v_commission_id"></td>
                        <td align="right" id="v_commission_percent"> </td>
                        </tr>
                         <tr bgcolor="<? echo $style; ?>">
                        <td width="20">6</td>
                        <td width="100">Testing Cost</td>
                        <td align="right" id="p_testing_id"></td>
                        <td align="right" id="p_testing_percent"> </td>
                        <td align="right" id="testing_id"></td>
                        <td align="right" id="testing_percent"> </td>
                   
                        <td align="right" id="v_testing_id"></td>
                        <td align="right" id="v_testing_percent"> </td>
                        </tr>
                        <tr bgcolor="<? echo $style1; ?>">
                        <td width="20">7</td>
                        <td width="100">Freight Cost</td>
                        <td align="right" id="p_freight_id"></td>
                        <td align="right" id="p_freight_percent"> </td>
                        <td align="right" id="freight_id"></td>
                        <td align="right" id="freight_percent"> </td>
                        <td align="right" id="v_freight_id"></td>
                        <td align="right" id="v_freight_percent"> </td>
                        </tr>
                        <tr bgcolor="<? echo $style; ?>">
                        <td width="20">8</td>
                        <td width="100">Inspection Cost</td>
                        <td align="right" id="p_inspection_id"></td>
                        <td align="right" id="p_inspection_percent"> </td>
                        <td align="right" id="inspection_id"></td>
                        <td align="right" id="inspection_percent"> </td>
                        <td align="right" id="v_inspection_id"></td>
                        <td align="right" id="v_inspection_percent"> </td>
                        </tr>
                        <tr bgcolor="<? echo $style1; ?>">
                        <td width="20">9</td>
                        <td width="100">Certificate Cost</td>
                        <td align="right" id="p_certificate_id"></td>
                        <td align="right" id="p_certificate_percent"> </td>
                        <td align="right" id="certificate_id"></td>
                        <td align="right" id="certificate_percent"> </td>
                        <td align="right" id="v_certificate_id"></td>
                        <td align="right" id="v_certificate_percent"> </td>
                        </tr>
                        <tr bgcolor="<? echo $style; ?>">
                        <td width="20">10</td>
                        <td width="100">Commn OH</td>
                        <td align="right" id="p_commn_id"></td>
                        <td align="right" id="p_commn_percent"> </td>
                        <td align="right" id="commn_id"></td>
                        <td align="right" id="commn_percent"> </td>
                        <td align="right" id="v_commn_id"></td>
                        <td align="right" id="v_commn_percent"> </td>
                        </tr>
                        <tr bgcolor="<? echo $style1; ?>">
                        <td width="20">11</td>
                        <td width="100">Courier Cost</td>
                        <td align="right" id="p_courier_id"></td>
                        <td align="right" id="p_courier_percent"> </td>
                        <td align="right" id="courier_id"></td>
                        <td align="right" id="courier_percent"> </td>
                        <td align="right" id="v_courier_id"></td>
                        <td align="right" id="v_courier_percent"> </td>
                        </tr>
                        <tr bgcolor="<? echo $style; ?>">
                        <td width="20">12</td>
                        <td width="100">CM Cost</td>
                        <td align="right" id="p_cm_id"></td>
                        <td align="right" id="p_cm_percent"> </td>
                        <td align="right" id="cm_id"></td>
                        <td align="right" id="cm_percent"> </td>
                        <td align="right" id="v_cm_id"></td>
                        <td align="right" id="v_cm_percent"> </td>
                        </tr>
                        <tr bgcolor="<? echo $style1; ?>">
                        <td width="20">13</td>
                        <td width="100">Total Cost</td><td align="right" id="p_cost_id"></td>
                        <td align="right" id="p_cost_percent"> </td>
                        <td align="right" id="cost_id"></td>
                        <td align="right" id="cost_percent"> </td>
                        <td align="right" id="v_cost_id"></td>
                        <td align="right" id="v_cost_percent"> </td>
                        </tr>
                        <tr bgcolor="<? echo $style; ?>">
                        <td width="20">14</td>
                        <td width="100">Total Order Value</td><td align="right" id="p_order_id"></td>
                        <td align="right" id="p_order_percent"> </td>
                        <td align="right" id="order_id"></td>
                        <td align="right" id="order_percent"> </td>
                        <td align="right" id="v_order_id"></td>
                        <td align="right" id="v_order_percent"> </td>
                        </tr>
                        <tr bgcolor="<? echo $style1; ?>">
                        <td width="20">15</td>
                        <td width="100">Profit/Loss </td>
                        <td align="right" id="p_fab_profit_id"></td>
                        <td align="right" id="p_profit_fab_percentage"> </td>
                        <td align="right" id="fab_profit_id"></td>
                        <td align="right" id="profit_fab_percentage"> </td>
                        <td align="right" id="v_fab_profit_id"></td>
                        <td align="right" id="v_profit_fab_percentage"> </td>
                        </tr>
                        <tr bgcolor="<? echo $style; ?>">
                        <td width="20">16</td>
                        <td width="100">Expected Profit <div id="expt_percent"></div> </td>
                        <td align="right" id="p_expected_id"></td>
                        <td align="right" id="p_profit_expt_fab_percentage"> </td>
                        <td align="right" id="expected_id"></td>
                        <td align="right" id="profit_expt_fab_percentage"> </td>
                        <td align="right" id="v_expected_id"></td>
                        <td align="right" id="v_profit_expt_fab_percentage"> </td>
                        </tr>
                        <tr bgcolor="<? echo $style1; ?>">
                        <td width="20">17</td>
                        <td width="100">Expt.Profit Variance </td>
                        <td align="right" id="p_expt_variance_id"></td>
                        <td align="right" id="p_expt_percent"> </td>
                        <td align="right" id="expt_p_variance_id"></td>
                        <td align="right" id="expt_p_percent"> </td>
                        <td align="right" id="v_expt_variance_id"></td>
                        <td align="right" id="v_expt_percent"> </td>
                        </tr>
                        </table>
                    </td>
                    <td colspan="5" style="min-height:800px; max-height:100%" align="center" valign="top">
                   </td>
                  </tr>
           </table>
           </div>
           <br/>   
         <h3 align="left" id="accordion_h2" style="width:4790px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel2', '')"> -Search Panel</h3>
        <fieldset style="width:100%;" id="content_search_panel2">	
            <table width="4790">
                    <tr class="form_caption">
                     <td  align="left">Decimal rounded to 2 digit.</td>
                      <td colspan="47" align="center"><strong>Order Wise Budget Report</strong></td>
                    </tr>
                    <tr class="form_caption">
                    <td colspan="47" align="center"><strong><? echo $company_library[$company_name]; ?></strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td align="left"><strong>Details Report </strong></td>
                    </tr>
            </table>
            <? $asking_profit_head=$asking_profit_arr[$company_name]['asking_profit']; ?>
            <table id="table_header_1" class="rpt_table" width="4750" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                <tr>
                    <th width="40" rowspan="2">SL</th>
                   	<th width="70" rowspan="2">Buyer</th>
                    <th width="70" rowspan="2">Job No</th>
                    <th width="100" rowspan="2">Order No</th>
                    <th width="100" rowspan="2">Order Status</th>
                    <th width="110" rowspan="2">Style</th>
                    <th width="110" rowspan="2">Item Name</th>
                    <th width="110" rowspan="2">Dealing</th>
                    <?
                     if(str_replace("'","",$cbo_search_date)==1)
					{ ?>
						<th width="70" rowspan="2">Ship. Date</th>
					<? }
					else   if(str_replace("'","",$cbo_search_date)==2)
					{ ?>
						<th width="70" rowspan="2">PO Recv. Date</th>
					<? }
					else 
					{ ?>
					<th width="70" rowspan="2">PO Insert Date</th>	
					<? }
					?>
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
                    <th width="100" rowspan="2">Expected Profit(<? echo $asking_profit_head.'%';?>)</th>
                    <th width="" rowspan="2">Expt.Profit Variance</th>
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
            <div style="width:4790px; max-height:400px; overflow-y:scroll" id="scroll_body">
             <table class="rpt_table" width="4750" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
                <? 
                $i=1; $total_order_qty=0;  $total_yarn_dyeing_cost=0; $total_yarn_cost=0; $total_order_value=0;$total_purchase_cost=0; $grand_tot_trims_cost=0; $total_fabric_dyeing_cost=0; $total_knitting_cost=0; $total_heat_setting_cost=0;$total_finishing_cost=0; $total_washing_cost=0; $fabric_dyeing_cost_dzn=0; $other_cost=0;
$all_over_print_cost=0;$total_trim_cost=0;$total_commercial_cost=0;
                $sql="select a.job_no_prefix_num, b.insert_date,a.job_no,a.company_name,a.buyer_name,a.style_ref_no,b.is_confirmed,a.quotation_id,a.agent_name,a.avg_unit_price,a.dealing_marchant, a.gmts_item_id,a.total_set_qnty as ratio,b.plan_cut,b.id as po_id, b.po_number, b.pub_shipment_date,b.po_received_date, b.po_quantity, b.unit_price from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond $buyer_id_cond  $year_cond $job_no_cond $order_id_cond_trans $order_no_cond $order_status_cond $season_cond group by b.id,a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,b.is_confirmed,a.style_ref_no,a.agent_name,a.avg_unit_price,a.dealing_marchant, a.gmts_item_id, b.po_number, b.pub_shipment_date,b.po_received_date, b.po_quantity, b.unit_price,a.quotation_id,a.total_set_qnty,b.plan_cut, b.insert_date order by  b.pub_shipment_date,b.id ";
				//echo $sql;
				$result=sql_select($sql);
				 $tot_rows=count($result);
				 foreach($result as $row )
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$total_order_value=$row[csf('po_quantity')]*$row[csf('avg_unit_price')];
					$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
					$total_plancut_value=$plan_cut_qnty*$row[csf('avg_unit_price')];
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
				?>
				 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                     <td width="40" rowspan="4"><? echo $i; ?></td>
                     <td  width="70" rowspan="4" title="<? echo $buyer_library[$row[csf('buyer_name')]] ?>"><p><? echo $buyer_library[$row[csf('buyer_name')]] ?></p></td>
                     <td  width="70" rowspan="4" title="<? echo $row[csf('job_no_prefix_num')];  ?>"><p><? echo $row[csf('job_no_prefix_num')];  ?></p></td>
                     <td  width="100" rowspan="4" title="<? echo $row[csf('po_number')]; ?>"><p><? echo $row[csf('po_number')]; ?></p></td>
                     <td  width="100" rowspan="4" title="<? echo $order_status[$row[csf('is_confirmed')]]; ?>"><p><? echo  $order_status[$row[csf('is_confirmed')]]; ?></p></td>
                     <td  width="110" rowspan="4" title="<? echo $row[csf('style_ref_no')]; ?>"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                     <td  width="110" rowspan="4" title="<?   echo $gmts_item; ?>"><p><? echo $row[csf('gmts_item_id')]; ?>
					<? $gmts_item='';
                    $gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
                    foreach($gmts_item_id as $item_id)
                    {
                        if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
                    }
                    echo $gmts_item;
					?>
                     </p></td>
                     <td rowspan="4"  width="110" title="<? echo $team_member_arr[$row[csf('dealing_marchant')]]; ?>"><p><? echo $team_member_arr[$row[csf('dealing_marchant')]]; ?></p></td>
                     <td rowspan="4" width="70" title="<? echo $ship_po_recv_date; ?>"><p><? echo $ship_po_recv_date; ?></p></td>
                     <td rowspan="4"  width="90" align="right" title="<? echo number_format($row[csf('po_quantity')],2); ?>"><p><? echo number_format($row[csf('po_quantity')],2); ?></p></td>
                     <td rowspan="4" width="90" align="right" title="<? echo number_format($row[csf('avg_unit_price')],2); ?>"><p><? echo number_format($row[csf('avg_unit_price')],2); ?></p></td>
                     <td rowspan="4" width="100" align="right" title="<? echo number_format($total_order_value,2); ?>" ><p><? 
					 $total_order_amount+=$total_order_value;
					 $total_plancut_amount+=$total_plancut_value;
					 echo number_format($total_order_value,2); ?></p>
                     </td>
                     <td width="100">
                  	 Price Quotation
                     </td>
                     <?
                        $dzn_qnty=0;
						$price_costing_per_id=$price_costing_perArray[$row[csf('quotation_id')]]['costing_per'];
                        if($price_costing_per_id==1)
                        {
                            $dzn_qnty=12;
                        }
                        else if($price_costing_per_id==3)
                        {
                            $dzn_qnty=12*2;
                        }
                        else if($price_costing_per_id==4)
                        {
                            $dzn_qnty=12*3;
                        }
                        else if($price_costing_per_id==5)
                        {
                            $dzn_qnty=12*4;
                        }
                        else
                        {
                            $dzn_qnty=1;
                        } $dzn_qnty=$dzn_qnty*$row[csf('ratio')];
						$p_commercial_cost=$price_fabriccostArray[$row[csf('quotation_id')]]['comm_cost'];
						$tot_p_commercial_cost=($p_commercial_cost/$dzn_qnty)*$order_qty_pcs;
						$price_fabric_data_Array[$price_fabricRow[csf('quotation_id')]];
						$price_fabricData=explode(",",substr($price_fabric_data_Array[$row[csf('quotation_id')]],0,-1));
						$p_fab_precost_Data=explode(",",substr($p_fab_precost_arr[$row[csf('quotation_id')]],0,-1));
						foreach($price_fabricData as $p_fabricRow)
						{
						$p_fabricRow=explode("**",$p_fabricRow);
						$p_fab_nature_id=$p_fabricRow[0];	
						$p_fab_source_id=$p_fabricRow[1];
						$p_fab_rate=$p_fabricRow[2];
						$p_yarn_qty=$p_fabricRow[3];
						$p_yarn_amount=$p_fabricRow[4];
						if($p_fab_source_id==2)
							{
							foreach($p_fab_precost_Data as $p_fab_row)
							{
							$p_fab_dataRow=explode("**",$p_fab_row);
							$p_fab_requirment=$p_fab_dataRow[0];
							$p_fab_pcs=$p_fab_dataRow[1];
							$p_fab_purchase_qty=$p_fab_requirment/$p_fab_pcs*$plan_cut_qnty; 
							$p_fab_purchase=$p_fab_purchase_qty*$p_fab_rate; 
							}
							}
						else if($p_fab_source_id==1 || $p_fab_source_id==3)
							{
							$p_avg_rate=$p_yarn_amount/$p_yarn_qty;
							$p_yarn_costing=$p_yarn_amount/$dzn_qnty*$plan_cut_qnty;	
							}
						}
						$p_kniting_cost=$pq_knit_arr[$row[csf('quotation_id')]]['knit']+$pq_knit_arr[$row[csf('quotation_id')]]['weaving']+$pq_knit_arr[$row[csf('quotation_id')]]['collar_cuff']+$pq_knit_arr[$row[csf('quotation_id')]]['feeder_stripe'];
						$tot_p_knit_cost=($p_kniting_cost/$dzn_qnty)*$plan_cut_qnty;
						$p_knit_cost_dzn=$p_kniting_cost; 
						$p_washing_cost=($pq_knit_arr[$row[csf('quotation_id')]]['washing']/$dzn_qnty)*$plan_cut_qnty;
						$p_all_over_cost=($pq_knit_arr[$row[csf('quotation_id')]]['all_over']/$dzn_qnty)*$plan_cut_qnty;
						$p_yarn_dyeing_cost=($pq_knit_arr[$row[csf('quotation_id')]]['yarn_dyeing']/$dzn_qnty)*$plan_cut_qnty;
						$p_yarn_dyeing_cost_dzn=$pq_knit_arr[$row[csf('quotation_id')]]['yarn_dyeing'];
						$p_fabric_dyeing_cost=($pq_knit_arr[$row[csf('quotation_id')]]['fabric_dyeing']/$dzn_qnty)*$plan_cut_qnty;
						$p_fabric_dyeing_cost_dzn=$pq_knit_arr[$row[csf('quotation_id')]]['fabric_dyeing'];
						$p_heat_setting_cost=($pq_knit_arr[$row[csf('quotation_id')]]['heat']/$dzn_qnty)*$plan_cut_qnty;
						$p_fabric_finish=($pq_knit_arr[$row[csf('quotation_id')]]['fabric_finish']/$dzn_qnty)*$plan_cut_qnty;
						
						if($p_fabric_dyeing_cost<=0 && $p_yarn_dyeing_cost<=0)
						{
						$color_fab="red";
						}
						else
						{
						$color_fab="";	
						}
						if($p_yarn_costing<=0)
						{
						$color_yarn="red";
						}
						else
						{
						$color_yarn="";	
						}
						if($tot_p_knit_cost<=0)
						{
						$color_knit="red";
						}
						else
						{
						$color_knit="";	
						}
						if($p_fabric_finish<=0)
						{
						$color_finish="red";
						}
						else
						{
						$color_finish="";	
						}
						if($tot_p_commercial_cost<=0)
						{
						$color_com="red";
						}
						else
						{
						$color_com="";	
						}
						$p_yarn_cost_percent=($p_yarn_costing/$total_order_value)*100;
						$total_yarn_cost_percent+=$p_yarn_cost_percent;
						$tot_p_yarn_cost_percent+=$p_yarn_cost_percent;
						$tot_yarn_cost_price+=$p_yarn_costing;
					 ?>
                     <td width="100" align="right"><a href="##" onClick="generate_pricecost_yarnavg_popup('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('quotation_id')]; ?>','pricost_yarnavg_detail')"><? echo number_format($p_avg_rate,2); ?></a></td>
                     <td width="80" align="right" title="<? echo $p_yarn_costing; ?>" bgcolor="<? echo $color_yarn; ?>"><? echo number_format($p_yarn_costing,2); ?></td>
                     <td width="80" align="right" title="<? echo $p_yarn_cost_percent.'%'; ?>"><? echo number_format($p_yarn_cost_percent,2).'%'; ?></td>
                     <td width="100" align="right"><a href="##" onClick="generate_pricecost_purchase_popup('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $p_fab_source_id; ?>','<? echo $row[csf('quotation_id')]; ?>','fab_price_purchase_detail')"><? echo number_format($p_fab_purchase,2); ?></a></td>
                     <td width="80" title="<? echo $p_knit_cost_dzn; ?>" align="right"><? echo number_format($p_knit_cost_dzn,2); ?></td>
                     <td width="80" align="right" title="<? echo $tot_knit_cost; ?>"  bgcolor="<? echo $color_knit; ?>"><?
					 ?>
                     <a href="##" onClick="generate_pri_cost_knit_popup('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $cons_process; ?>','<? echo $row[csf('quotation_id')]; ?>','pricost_knit_detail')"><? 
					 echo number_format($tot_p_knit_cost,2);
					  ?></a></td>
                     <td  width="100" align="right" title="<? echo number_format($p_yarn_dyeing_cost_dzn ,2); ?>" ><? echo number_format($p_yarn_dyeing_cost_dzn ,2); ?></td>
                     <td  width="110" align="right" title="<? echo number_format($p_yarn_dyeing_cost ,2); ?>" ><? echo number_format($p_yarn_dyeing_cost ,2); ?></td>
                     <td  width="120" align="right"  title="<? echo number_format($p_fabric_dyeing_cost_dzn ,2); ?>" ><? echo number_format($p_fabric_dyeing_cost_dzn,2); 
					 $total_fabrics_cost_summary+=($row[csf('po_quantity')]/$dzn_qnty)*$p_fabric_dyeing_cost_dzn;
					  ?></td>
                     <td  width="100" align="right" title="<? echo number_format($p_fabric_dyeing_cost ,2); ?>" bgcolor="<? echo $color_fab; ?>"><a href="##" onClick="generate_pricost_fab_dyeing_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $fab_source_id; ?>','<? echo $row[csf('quotation_id')];?>','fab_price_dyeing_detail')"><? echo number_format($p_fabric_dyeing_cost,2); ?></a></td>
                     <td  width="90" align="right"><? echo number_format($p_heat_setting_cost,2); ?></td>
                     <td  width="100" align="right"><a href="##" onClick="generate_pricost_fab_finishing_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('quotation_id')]; ?>','fab_price_finishing_detail')"><? echo number_format($p_fabric_finish,2); ?></a> </td>
                     <td  width="90" align="right"><a href="##" onClick="generate_pricost_fab_finishing_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('quotation_id')]; ?>','fab_price_washing_detail')"><? echo number_format($p_washing_cost,2); ?></a></td>
                     <td  width="90" align="right"><a href="##" onClick="generate_pricost_fab_all_over_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('quotation_id')]; ?>','fab_price_all_over_detail')"><? echo number_format($p_all_over_cost,2); ?></a></td>
				<?
					$tot_p_trim_amount= $price_fabriccostArray[$row[csf('quotation_id')]]['trims_cost']/$dzn_qnty*$order_qty_pcs;
                    $tot_p_test_cost=$price_fabriccostArray[$row[csf('quotation_id')]]['lab_test']/$dzn_qnty*$total_order_value;
                 
				    $p_print_amount=($price_fab_emb[$row[csf('quotation_id')]]['print']/$dzn_qnty)*$order_qty_pcs;
                    $p_embroidery_amount=($price_fab_emb[$row[csf('quotation_id')]]['embroidery']/$dzn_qnty)*$order_qty_pcs;
                    $p_special_amount=($price_fab_emb[$row[csf('quotation_id')]]['special']/$dzn_qnty)*$order_qty_pcs;
                    $p_other_amount=($price_fab_emb[$row[csf('quotation_id')]]['other']/$dzn_qnty)*$order_qty_pcs;
					$p_wash_amount=($price_fab_emb[$row[csf('quotation_id')]]['wash']/$dzn_qnty)*$order_qty_pcs;
					
                    $p_foreign=$price_commission_array[$row[csf('quotation_id')]]['foreign']/$dzn_qnty*$order_qty_pcs;
                    $p_local=$price_commission_array[$row[csf('quotation_id')]]['local']/$dzn_qnty*$order_qty_pcs;
					
                    $p_freight_cost= $price_fabriccostArray[$row[csf('quotation_id')]]['freight']/$dzn_qnty*$order_qty_pcs;
                    $p_inspection=$price_fabriccostArray[$row[csf('quotation_id')]]['inspection']/$dzn_qnty*$order_qty_pcs;
                    $p_certificate_cost=$price_fabriccostArray[$row[csf('quotation_id')]]['certificate_pre_cost']/$dzn_qnty*$order_qty_pcs;
                    
                    $p_common_oh=$price_fabriccostArray[$row[csf('quotation_id')]]['common_oh']/$dzn_qnty*$order_qty_pcs;
                    $p_currier_cost=$price_fabriccostArray[$row[csf('quotation_id')]]['currier_pre_cost']/$dzn_qnty*$order_qty_pcs;
                    //echo $currier_cost;
                    $p_cm_cost=$price_fabriccostArray[$row[csf('quotation_id')]]['c_cost']/$dzn_qnty*$order_qty_pcs;
                    $p_cm_cost_dzn=$price_fabriccostArray[$row[csf('quotation_id')]]['c_cost'];
                    $total_p_cost=$p_yarn_costing+$p_fab_purchase+$tot_p_knit_cost+$p_washing_cost+$p_all_over_cost+$p_yarn_dyeing_cost+$p_fabric_dyeing_cost+$p_heat_setting_cost+$p_fabric_finish+$tot_p_trim_amount+$tot_p_test_cost+$p_print_amount+$p_embroidery_amount+$p_special_amount+$p_other_amount+$p_wash_amount+$tot_p_commercial_cost+$p_foreign+$p_local+$p_freight_cost+$p_inspection+$p_certificate_cost+$p_common_oh+$p_currier_cost+$p_cm_cost;
					//echo  $total_cost;
					$total_p_print_amount+=$p_print_amount;
					$total_p_embroidery_amount+=$p_embroidery_amount;
					$total_p_special_amount+=$p_special_amount;
					$total_p_other_amount+=$p_other_amount;
					$total_p_wash_amount+=$p_wash_amount;
					
					$total_p_foreign_amount+=$p_foreign;
					$total_p_local_amount+=$p_local;
					$total_p_test_cost_amount+=$tot_p_test_cost;
					$total_p_freight_amount+=$p_freight_cost;
					$total_p_inspection_amount+=$p_inspection;
					$total_p_certificate_amount+=$p_certificate_cost;
					
					$total_p_common_oh_amount+=$p_common_oh;
					$total_p_currier_amount+=$p_currier_cost;
					$total_p_cm_amount+=$p_cm_cost;
					$max_profit=$asking_profit_arr[$row[csf('company_name')]]['max_profit'];
					//echo $max_profit;
					$company_asking=$asking_profit_arr[$row[csf('company_name')]]['asking_profit'];
					
					if($tot_p_trim_amount<=0)
						{
						$color_trim="red";
						}
						else
						{
						$color_trim="";	
						}
						
					if($p_cm_cost<=0)
						{
						$color="red";
						}
						else
						{
						$color="";	
						}
					?>
                     <td width="100" align="right" bgcolor="<? echo $color_trim; ?>"><a href="##" onClick="generate_pricost_trim_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('quotation_id')]; ?>','trim_cost_price_detail')"><? echo number_format($tot_p_trim_amount,2); ?></a></td>
                     <td width="80" align="right"><a href="##" onClick="generate_pricost_embell_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('quotation_id')]; ?>','price_print_cost_detail')"><? echo number_format($p_print_amount,2); ?></a></td>
                     <td width="85" align="right"><a href="##" onClick="generate_pricost_embell_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('quotation_id')]; ?>','price_embroidery_cost_detail')"><? echo number_format($p_embroidery_amount,2); ?></a></td>
                     <td width="80" align="right"><? echo number_format($p_special_amount,2); ?></td>
                     <td width="80" align="right"><a href="##" onClick="generate_pricost_embell_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('quotation_id')]; ?>','price_wash_cost_detail')"><? echo number_format($p_wash_amount,2); ?></a></td>
                     <td width="80" align="right"><? echo number_format($p_other_amount,2); ?></td>
                     <td width="120" align="right" bgcolor="<? echo $color_com; ?>"><? echo number_format($tot_p_commercial_cost,2); ?></td>
                     <td width="120" align="right"><? echo number_format($p_foreign,2) ?></td>
                     <td width="120" align="right"><? echo number_format($p_local,2) ?></td>
                     <td width="100" align="right"><? echo number_format($tot_p_test_cost,2);?></td>
                     <td width="100" align="right"><? echo number_format($p_freight_cost,2); ?></td>
                     <td width="120" align="right"><? echo number_format($p_inspection,2);?></td>
                     <td width="100" align="right"><? echo number_format($p_certificate_cost,2); ?></td>
                     <td width="100" align="right"><? echo number_format($p_common_oh,2); ?></td>
                     <td width="100" align="right"><? echo number_format($p_currier_cost,2);?></td>
                     <td width="120" align="right"><? echo number_format($p_cm_cost_dzn,2);?></td>
                     <td width="100" align="right" bgcolor="<? echo $color; ?>"><? echo number_format($p_cm_cost,2);?></td>
                     <td width="100" align="right"><? echo number_format($total_p_cost,2); ?></td>
                    <?
						$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
						$total_p_profit=$total_order_value-$total_p_cost;
						$total_p_profit_percentage2=$total_p_profit/$total_order_value*100; 
						if($total_p_profit_percentage2<=0 )
						{
							$color_pl="red";
						}
						else if($total_p_profit_percentage2>$max_profit)
						{
							$color_pl="yellow";	
						}
						else if($total_p_profit_percentage2<=$max_profit)
						{
							$color_pl="green";	
						}
						else
						{
							$color_pl="";	
						}
						$p_expected_profit=$asking_profit_arr[$row[csf('company_name')]]['asking_profit']*$total_order_value/100;
					?>
                     <td width="100" align="right" bgcolor="<? echo $color_pl; ?>"><? echo number_format($total_p_profit,2); ?></td>
                     <td width="100" align="right"><? echo number_format($total_p_profit_percentage2,2).'%'; ?></td>
                     <td width="100" align="right"><?   echo number_format($p_expected_profit,2); ?></td>
                     <td width="" align="right"><? $tot_expect_variance=$total_p_profit-$p_expected_profit; echo number_format($tot_expect_variance,2)?></td>
                  </tr> 
                   <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                     <td width="100">
                     Pre Cost
                     </td>
                     <?
                        $dzn_qnty=0;
						$costing_per_id=$fabriccostArray[$row[csf('job_no')]]['costing_per_id'];
                        if($costing_per_id==1)
                        {
                            $dzn_qnty=12;
                        }
                        else if($costing_per_id==3)
                        {
                            $dzn_qnty=12*2;
                        }
                        else if($costing_per_id==4)
                        {
                            $dzn_qnty=12*3;
                        }
                        else if($costing_per_id==5)
                        {
                            $dzn_qnty=12*4;
                        }
                        else
                        {
                            $dzn_qnty=1;
                        } $dzn_qnty=$dzn_qnty*$row[csf('ratio')];
						$dzn_qnty_yarn=$dzn_qnty*$row[csf('ratio')];
						$commercial_cost=$fabriccostArray[$row[csf('job_no')]]['comm_cost'];
						$tot_commercial_cost=($commercial_cost/$dzn_qnty)*$order_qty_pcs;
						$fabricData=explode(",",substr($fabric_data_Array[$row[csf('job_no')]],0,-1));
						$fab_precost_Data=explode(",",substr($fab_precost_arr[$row[csf('job_no')]][$row[csf('po_id')]],0,-1));
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
							$fab_requirment=number_format($fab_requirment,4);
							$fab_purchase_qty=$fab_requirment/$fab_pcs*$plan_cut_qnty; 
							$fab_purchase=$fab_purchase_qty*$fab_rate; 
							}
							}
						else if($fab_source_id==1 || $fab_source_id==3)
							{
							$avg_rate=$yarn_amount/$yarn_qty;
							$yarn_costing=$yarn_amount/$dzn_qnty*$plan_cut_qnty;		
							}
						}
						$kniting_cost=$knit_arr[$row[csf('job_no')]]['knit']+$knit_arr[$row[csf('job_no')]]['weaving']+$knit_arr[$row[csf('job_no')]]['collar_cuff']+$knit_arr[$row[csf('job_no')]]['feeder_stripe'];
						$tot_knit_cost=($kniting_cost/$dzn_qnty)*$plan_cut_qnty;
						$knit_cost_dzn=$kniting_cost; 
						$washing_cost=($knit_arr[$row[csf('job_no')]]['washing']/$dzn_qnty)*$plan_cut_qnty;
						$all_over_cost=($knit_arr[$row[csf('job_no')]]['all_over']/$dzn_qnty)*$plan_cut_qnty;
						$yarn_dyeing_cost=($knit_arr[$row[csf('job_no')]]['yarn_dyeing']/$dzn_qnty)*$plan_cut_qnty;
						$yarn_dyeing_cost_dzn=$knit_arr[$row[csf('job_no')]]['yarn_dyeing'];
						$fabric_dyeing_cost=($knit_arr[$row[csf('job_no')]]['fabric_dyeing']/$dzn_qnty)*$plan_cut_qnty;
						$fabric_dyeing_cost_dzn=$knit_arr[$row[csf('job_no')]]['fabric_dyeing'];
						$heat_setting_cost=($knit_arr[$row[csf('job_no')]]['heat']/$dzn_qnty)*$plan_cut_qnty;
						$fabric_finish=($knit_arr[$row[csf('job_no')]]['fabric_finish']/$dzn_qnty)*$plan_cut_qnty;
						
						if($fabric_dyeing_cost<=0 && $yarn_dyeing_cost<=0)
						{
						$color_fab="red";
						}
						else
						{
						$color_fab="";	
						}
						if($yarn_costing<=0)
						{
						$color_yarn="red";
						}
						else
						{
						$color_yarn="";	
						}
						if($kniting_cost<=0)
						{
						$color_knit="red";
						}
						else
						{
						$color_knit="";	
						}
						if($fabric_finish<=0)
						{
						$color_finish="red";
						}
						else
						{
						$color_finish="";	
						}
						if($commercial_cost<=0)
						{
						$color_com="red";
						}
						else
						{
						$color_com="";	
						}
						
						$yarn_cost_percent=($yarn_costing/$total_order_value)*100;
						$total_yarn_cost_percent+=$yarn_cost_percent;
					 ?>
                     <td width="100" align="right"><a href="##" onClick="generate_pre_cost_report('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','precost_yarn_detail')"><? echo  number_format($avg_rate,2); ?></a></td>
                     <td width="80" align="right" title="<? echo $yarn_costing; ?>" bgcolor="<? echo $color_yarn; ?>"><? echo number_format($yarn_costing,2); ?></td>
                     <td width="80" align="right" title="<? echo $yarn_cost_percent; ?>"><? echo number_format($yarn_cost_percent,2).'%'; ?></td>
                     <td width="100" align="right"><a href="##" onClick="generate_precost_fab_purchase_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $fab_source_id; ?>','fab_purchase_detail')"><? echo number_format($fab_purchase,2); ?></a></td>
                     <td width="80" title="<? echo $knit_cost_dzn; ?>" align="right"><? echo number_format($knit_cost_dzn,2); ?></td>
                     <td width="80" align="right" title="<? echo $tot_knit_cost; ?>"  bgcolor="<? echo $color_knit; ?>"><?
					 ?>
                     <a href="##" onClick="generate_pre_cost_knit_popup('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $cons_process; //$row[csf('style_ref_no')]; ?>','precost_knit_detail')"><? 
					 echo number_format($tot_knit_cost,2);
					  ?></a></td>
                     <td  width="100" align="right" title="<? echo number_format($yarn_dyeing_cost_dzn ,2); ?>" ><? echo number_format($yarn_dyeing_cost_dzn ,2); ?></td>
                     <td  width="110" align="right" title="<? echo number_format($yarn_dyeing_cost ,2); ?>" ><? echo number_format($yarn_dyeing_cost ,2); ?></td>
                     <td  width="120" align="right"  title="<? echo number_format($fabric_dyeing_cost_dzn ,2); ?>" ><? echo number_format($fabric_dyeing_cost_dzn,2); 
					 $total_fabrics_cost_summary+=($row[csf('po_quantity')]/$dzn_qnty)*$fabric_dyeing_cost_dzn;
					  ?></td>
                     <td  width="100" align="right" title="<? echo number_format($fabric_dyeing_cost ,2); ?>" bgcolor="<? echo $color_fab; ?>"><a href="##" onClick="generate_precost_fab_dyeing_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $fab_source_id; ?>','fab_dyeing_detail')"><? echo number_format($fabric_dyeing_cost,2); ?></a></td>
                     <td  width="90" align="right"><? echo number_format($heat_setting_cost,2); ?></td>
                     <td  width="100" align="right" ><a href="##" onClick="generate_precost_fab_finishing_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','fab_finishing_detail')"><? echo number_format($fabric_finish,2); ?></a> </td>
                     <td  width="90" align="right"><a href="##" onClick="generate_precost_fab_finishing_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','fab_washing_detail')"><? echo number_format($washing_cost,2); ?></a></td>
                     <td  width="90" align="right"><a href="##" onClick="generate_precost_fab_all_over_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','fab_all_over_detail')"><? echo number_format($all_over_cost,2); ?></a></td>
				<?
					$tot_trim_amount= $fabriccostArray[$row[csf('job_no')]]['trims_cost']/$dzn_qnty*$order_qty_pcs;
                    $tot_test_cost=$fabriccostArray[$row[csf('job_no')]]['lab_test']/$dzn_qnty*$order_qty_pcs;
                    $print_amount=($fab_emb[$row[csf('job_no')]]['print']/$dzn_qnty)*$order_qty_pcs;
                    $embroidery_amount=($fab_emb[$row[csf('job_no')]]['embroidery']/$dzn_qnty)*$order_qty_pcs;
                    $special_amount=($fab_emb[$row[csf('job_no')]]['special']/$dzn_qnty)*$order_qty_pcs;
					$wash_amount=($fab_emb[$row[csf('job_no')]]['wash']/$dzn_qnty)*$order_qty_pcs;
                    $other_amount=($fab_emb[$row[csf('job_no')]]['other']/$dzn_qnty)*$order_qty_pcs;
                    $foreign=$commission_array[$row[csf('job_no')]]['foreign']/$dzn_qnty*$order_qty_pcs;
                    $local=$commission_array[$row[csf('job_no')]]['local']/$dzn_qnty*$order_qty_pcs;
                    $freight_cost= $fabriccostArray[$row[csf('job_no')]]['freight']/$dzn_qnty*$order_qty_pcs;
                    $inspection=$fabriccostArray[$row[csf('job_no')]]['inspection']/$dzn_qnty*$order_qty_pcs;
                    $certificate_cost=$fabriccostArray[$row[csf('job_no')]]['certificate_pre_cost']/$dzn_qnty*$order_qty_pcs;
                    
                    $common_oh=$fabriccostArray[$row[csf('job_no')]]['common_oh']/$dzn_qnty*$order_qty_pcs;
                    $currier_cost=$fabriccostArray[$row[csf('job_no')]]['currier_pre_cost']/$dzn_qnty*$order_qty_pcs;
                  
                    $cm_cost=$fabriccostArray[$row[csf('job_no')]]['c_cost']/$dzn_qnty*$order_qty_pcs;
                    $cm_cost_dzn=$fabriccostArray[$row[csf('job_no')]]['c_cost'];
                    $total_cost=$yarn_costing+$fab_purchase+$tot_knit_cost+$washing_cost+$all_over_cost+$yarn_dyeing_cost+$fabric_dyeing_cost+$heat_setting_cost+$fabric_finish+$tot_trim_amount+$tot_test_cost+$print_amount+$embroidery_amount+$special_amount+$wash_amount+$other_amount+$tot_commercial_cost+$foreign+$local+$freight_cost+$inspection+$certificate_cost+$common_oh+$currier_cost+$cm_cost;
					
					$total_print_amount+=$print_amount;
					$total_embroidery_amount+=$embroidery_amount;
					$total_special_amount+=$special_amount;
					$total_wash_amount+=$wash_amount;
					$total_other_amount+=$other_amount;
					
					$total_foreign_amount+=$foreign;
					$total_local_amount+=$local;
					$total_test_cost_amount+=$tot_test_cost;
					$total_freight_amount+=$freight_cost;
					$total_inspection_amount+=$inspection;
					$total_certificate_amount+=$certificate_cost;
					
					$total_common_oh_amount+=$common_oh;
					$total_currier_amount+=$currier_cost;
					$total_cm_amount+=$cm_cost;
					$max_profit=$asking_profit_arr[$row[csf('company_name')]]['max_profit'];
					$company_asking=$asking_profit_arr[$row[csf('company_name')]]['asking_profit'];
					if($tot_trim_amount<=0)
						{
						$color_trim="red";
						}
						else
						{
						$color_trim="";	
						}
						
					if($cm_cost<=0)
						{
						$color="red";
						}
						else
						{
						$color="";	
						}
						$yarnData=explode(",",substr($yarncostArray[$row[csf('job_no')]],0,-1));
						
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
						}
				
					?>
                     <td width="100" align="right" bgcolor="<? echo $color_trim; ?>"><a href="##" onClick="generate_precost_trim_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','trim_cost_detail')"><? echo number_format($tot_trim_amount,2); ?></a></td>
                     <td width="80" align="right"><a href="##" onClick="generate_precost_embell_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','print_cost_detail')"><? echo number_format($print_amount,2); ?></a> </td>
                     <td width="85" align="right"><a href="##" onClick="generate_precost_embell_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','embroidery_cost_detail')"><? echo number_format($embroidery_amount,2); ?></a></td>
                     <td width="80" align="right"><? echo number_format($special_amount,2); ?></td>
                     <td width="80" align="right"><a href="##" onClick="generate_precost_embell_cost_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','wash_cost_detail')"><? echo number_format($wash_amount,2); ?></a></td>
                     <td width="80" align="right"><? echo number_format($other_amount,2); ?></td>
                     <td width="120" align="right" bgcolor="<? echo $color_com; ?>"><? echo number_format($tot_commercial_cost,2); ?></td>
                     <td width="120" align="right"><? echo number_format($foreign,2) ?></td>
                     <td width="120" align="right"><? echo number_format($local,2) ?></td>
                     <td width="100" align="right"><? echo number_format($tot_test_cost,2);?></td>
                     <td width="100" align="right"><? echo number_format($freight_cost,2); ?></td>
                     <td width="120" align="right"><? echo number_format($inspection,2);?></td>
                     <td width="100" align="right"><? echo number_format($certificate_cost,2); ?></td>
                     <td width="100" align="right"><? echo number_format($common_oh,2); ?></td>
                     <td width="100" align="right"><? echo number_format($currier_cost,2);?></td>
                     <td width="120" align="right"><? echo number_format($cm_cost_dzn,2);?></td>
                     <td width="100" align="right" bgcolor="<? echo $color; ?>"><? echo number_format($cm_cost,2);?></td>
                     <td width="100" align="right"><? echo number_format($total_cost,2); ?></td>
                    <?
						$total_profit=$total_order_value-$total_cost;
						$total_profit_percentage2=$total_profit/$total_order_value*100; 
						if($total_profit_percentage2<=0 )
						{
							$color_pl="red";
						}
						else if($total_profit_percentage2>$max_profit)
						{
							$color_pl="yellow";	
						}
						else if($total_profit_percentage2<=$max_profit)
						{
							$color_pl="green";	
						}
						else
						{
							$color_pl="";	
						}
					?>
                     <td width="100" align="right" bgcolor="<? echo $color_pl; ?>"><? echo number_format($total_profit,2); ?></td>
                     <td width="100" align="right"><? echo number_format($total_profit_percentage2,2).'%'; ?></td>
                     <td width="100" align="right"><?  $expected_profit=$asking_profit_arr[$row[csf('company_name')]]['asking_profit']*$total_order_value/100; echo number_format($expected_profit,2) //$total_profit=$total_cost-$total_order_value; //echo number_format($total_profit,2); ?></td>
                     <td width="" align="right"><? $expect_variance=$total_profit-$expected_profit; echo number_format($expect_variance,2)?></td>
                  </tr> 
                   <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                     <td width="100">
                     Variance
                     </td>
                     <?
						$total_avg_rate_percent_variance=$p_avg_rate-$avg_rate;
						$tot_fab_purchase_variance=$p_fab_purchase-$fab_purchase;
						$tot_knit_cost_dzn_variance=$p_knit_cost_dzn-$knit_cost_dzn;
						$tot_knit_cost_variance=$tot_p_knit_cost-$tot_knit_cost;
						$tot_yarn_dyeing_cost_dzn_vairance=$p_yarn_dyeing_cost_dzn-$yarn_dyeing_cost_dzn ;
						$tot_yarn_dyeing_cost_variance=$p_yarn_dyeing_cost-$yarn_dyeing_cost;
						$tot_fabric_dyeing_variance=$p_fabric_dyeing_cost_dzn-$fabric_dyeing_cost_dzn;
						$tot_fabric_dyeing_cost_variance=$p_fabric_dyeing_cost-$fabric_dyeing_cost;
						$tot_heat_variance_cost=$p_heat_setting_cost-$heat_setting_cost;
						$tot_fabric_finish_variance_cost=$p_fabric_finish-$fabric_finish;
						$tot_wash_cost_variance=$p_washing_cost-$washing_cost;
						$tot_all_over_variance=$p_all_over_cost-$all_over_cost;
					 ?>
                     <td width="100" align="right"><? echo number_format($total_avg_rate_percent_variance,2); ?></td>
                     <td width="80" align="right" title="<? echo $tot_yarn_variance; ?>" bgcolor="<? echo $color_yarn; ?>"><? $tot_yarn_variance=$p_yarn_costing-$yarn_costing; echo number_format($tot_yarn_variance,2); ?></td>
                     <td width="80" align="right" title="<? echo $tot_yarn_cost_percent; ?>"><? $tot_yarn_cost_percent=$p_yarn_cost_percent-$yarn_cost_percent;echo number_format($tot_yarn_cost_percent,2).'%'; ?></td>
                     <td width="100" align="right"><? echo number_format($tot_fab_purchase_variance,2); ?></td>
                     <td width="80" title="<? echo $knit_cost_dzn; ?>" align="right"><? echo number_format($tot_knit_cost_dzn_variance,2); ?></td>
                     <td width="80" align="right" title="<? echo $tot_knit_cost; ?>"  bgcolor="<? echo $color_knit; ?>">
                     <? 
					 echo number_format($tot_p_knit_cost-$tot_knit_cost,2);
					  ?></td>
                     <td  width="100" align="right" title="<? echo number_format($tot_yarn_dyeing_cost_dzn_vairance ,2); ?>" ><? echo number_format($tot_yarn_dyeing_cost_dzn_vairance ,2); ?></td>
                     <td  width="110" align="right" title="<? echo number_format($tot_yarn_dyeing_cost_variance ,2); ?>" ><? echo number_format($tot_yarn_dyeing_cost_variance ,2); ?></td>
                     <td  width="120" align="right"  title="<? echo number_format($tot_fabric_dyeing_variance ,2); ?>" ><? echo number_format($tot_fabric_dyeing_variance,2); 
					 $total_fabrics_cost_summary+=($row[csf('po_quantity')]/$dzn_qnty)*$fabric_dyeing_cost_dzn;
					  ?></td>
                     <td  width="100" align="right" title="<? //echo number_format($fabric_dyeing_cost ,2); ?>" bgcolor="<? echo $color_fab; ?>"><? echo number_format($tot_fabric_dyeing_cost_variance,2); ?></td>
                     <td  width="90" align="right"><? echo number_format($tot_heat_variance_cost,2); ?></td>
                     <td  width="100" align="right"><? echo number_format($tot_fabric_finish_variance_cost,2); ?> </td>
                     <td  width="90" align="right"><? echo number_format($p_washing_cost-$washing_cost,2); ?></td>
                     <td  width="90" align="right"><? echo number_format($p_all_over_cost-$all_over_cost,2); ?></td>
				<?
					$max_profit=$asking_profit_arr[$row[csf('company_name')]]['max_profit'];
					//echo $max_profit;
					$company_asking=$asking_profit_arr[$row[csf('company_name')]]['asking_profit'];
					if($tot_trim_amount<=0)
						{
						$color_trim="red";
						}
						else
						{
						$color_trim="";	
						}
						
					if($cm_cost<=0)
						{
						$color="red";
						}
						else
						{
						$color="";	
						}
						
						$tot_trim_cost_variance=$tot_p_trim_amount-$tot_trim_amount;
						$tot_print_varinace=$p_print_amount-$print_amount;
						$tot_embrodery_variance=$p_embroidery_amount-$embroidery_amount;
						$tot_special_variance=$p_special_amount-$special_amount;
						$tot_other_amount_variance=$p_other_amount-$other_amount;
						$tot_wash_amount_variance=$p_wash_amount-$wash_amount;
						$tot_commercial_variance=$tot_p_commercial_cost-$tot_commercial_cost;
						$tot_local_variance=$p_local-$local;
						$tot_test_variance=$tot_p_test_cost-$tot_test_cost;
						$tot_feight_variance=$p_freight_cost-$freight_cost;
						$tot_inspection_variance=$p_inspection-$inspection;
						$tot_certificate_variance=$p_certificate_cost-$certificate_cost;
						$tot_common_variance=$p_common_oh-$common_oh;
						$tot_currier_variance=$p_currier_cost-$currier_cost;
						$tot_cm_dzn_variance=$p_cm_cost_dzn-$cm_cost_dzn;
						$tot_cm_variance=$p_cm_cost-$cm_cost;
						$tot_total_cost_varaince=$total_p_cost-$total_cost;
				
					?>
                     <td width="100" align="right"><? echo number_format($tot_p_trim_amount-$tot_trim_amount,2); ?></td>
                     <td width="80" align="right"><? echo number_format($tot_print_varinace,2); ?></td>
                     <td width="85" align="right"><? echo number_format($tot_embrodery_variance,2); ?></td>
                     <td width="80" align="right"><? echo number_format($tot_special_variance,2); ?></td>
                     <td width="80" align="right"><? echo number_format($tot_wash_amount_variance,2); ?></td>
                     <td width="80" align="right"><? echo number_format($p_other_amount-$other_amount,2); ?></td>
                     <td width="120" align="right"><? echo number_format($tot_p_commercial_cost-$tot_commercial_cost,2); ?></td>
                     <td width="120" align="right"><? echo number_format($p_foreign-$foreign,2) ?></td>
                     <td width="120" align="right"><? echo number_format($tot_local_variance,2) ?></td>
                     <td width="100" align="right"><? echo number_format($tot_test_variance,2);?></td>
                     <td width="100" align="right"><? echo number_format($p_freight_cost-$freight_cost,2); ?></td>
                     <td width="120" align="right"><? echo number_format($p_inspection-$inspection,2);?></td>
                     <td width="100" align="right"><? echo number_format($tot_certificate_variance,2); ?></td>
                     <td width="100" align="right"><? echo number_format($tot_common_variance,2); ?></td>
                     <td width="100" align="right"><? echo number_format($p_currier_cost-$currier_cost,2);?></td>
                     <td width="120" align="right"><? echo number_format($tot_cm_dzn_variance,2);?></td>
                     <td width="100" align="right"><? echo number_format($p_cm_cost-$cm_cost,2);?></td>
                     <td width="100" align="right"><? echo number_format($total_p_cost-$total_cost,2); ?></td>
                    <?
						$total_profit=$total_order_value-$total_cost;
						$total_profit_percentage2=$total_profit/$total_order_value*100; 
						if($total_profit_percentage2<=0 )
						{
							$color_pl="red";
						}
						else if($total_profit_percentage2>$max_profit)
						{
							$color_pl="yellow";	
						}
						else if($total_profit_percentage2<=$max_profit)
						{
							$color_pl="green";	
						}
						else
						{
							$color_pl="";	
						}
						$tot_profit_variance=$total_profit-$total_p_profit;
						$tot_profit_percient_varaince=$total_profit_percentage2-$total_p_profit_percentage2;
						$tot_expected_profit=$expected_profit-$p_expected_profit;
						$tot_expected_varaince_data_vairance=$expect_variance-$tot_expect_variance;
					?>
                     <td width="100" align="right" bgcolor="<? echo $color_pl; ?>"><? echo number_format($total_profit-$total_p_profit,2); ?></td>
                     <td width="100" align="right"><? echo number_format($total_profit_percentage2-$total_p_profit_percentage2,2).'%'; ?></td>
                     <td width="100" align="right"><?   echo '-'; //$tot_expected_profit ?></td>
                     <td width="" align="right"><? echo '-';// echo number_format($tot_expected_varaince_data_vairance,2)?></td>
                  </tr>
                  <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                     <td width="100">
                     Variance %
                     </td>
                     <?
                        $dzn_qnty=0;
						$costing_per_id=$fabriccostArray[$row[csf('job_no')]]['costing_per_id'];
                        if($costing_per_id==1)
                        {
                            $dzn_qnty=12;
                        }
                        else if($costing_per_id==3)
                        {
                            $dzn_qnty=12*2;
                        }
                        else if($costing_per_id==4)
                        {
                            $dzn_qnty=12*3;
                        }
                        else if($costing_per_id==5)
                        {
                            $dzn_qnty=12*4;
                        }
                        else
                        {
                            $dzn_qnty=1;
                        }  $dzn_qnty=$dzn_qnty*$row[csf('ratio')];
						$commercial_cost=$fabriccostArray[$row[csf('job_no')]]['comm_cost'];
						$tot_commercial_cost=($commercial_cost/$dzn_qnty)*$order_qty_pcs;
						$fabricData=explode(",",substr($fabric_data_Array[$row[csf('job_no')]],0,-1));
						$fab_precost_Data=explode(",",substr($fab_precost_arr[$row[csf('job_no')]][$row[csf('po_id')]],0,-1));
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
							$fab_purchase=$fab_purchase_qty*$fab_rate; 
							}
							}
						else if($fab_source_id==1 || $fab_source_id==3)
							{
							$avg_rate=$yarn_amount/$yarn_qty;
							$yarn_costing=$yarn_amount/$dzn_qnty*$plan_cut_qnty;		
							}
						}
						$kniting_cost=$knit_arr[$row[csf('job_no')]]['knit']+$knit_arr[$row[csf('job_no')]]['weaving']+$knit_arr[$row[csf('job_no')]]['collar_cuff']+$knit_arr[$row[csf('job_no')]]['feeder_stripe'];
						$tot_knit_cost=($kniting_cost/$dzn_qnty)*$plan_cut_qnty;
						$knit_cost_dzn=$kniting_cost; 
						$washing_cost=($knit_arr[$row[csf('job_no')]]['washing']/$dzn_qnty)*$plan_cut_qnty;
						$all_over_cost=($knit_arr[$row[csf('job_no')]]['all_over']/$dzn_qnty)*$plan_cut_qnty;
						$yarn_dyeing_cost=($knit_arr[$row[csf('job_no')]]['yarn_dyeing']/$dzn_qnty)*$plan_cut_qnty;
						$yarn_dyeing_cost_dzn=$knit_arr[$row[csf('job_no')]]['yarn_dyeing'];
						$fabric_dyeing_cost=($knit_arr[$row[csf('job_no')]]['fabric_dyeing']/$dzn_qnty)*$plan_cut_qnty;
						$fabric_dyeing_cost_dzn=$knit_arr[$row[csf('job_no')]]['fabric_dyeing'];
						$heat_setting_cost=($knit_arr[$row[csf('job_no')]]['heat']/$dzn_qnty)*$plan_cut_qnty;
						$fabric_finish=($knit_arr[$row[csf('job_no')]]['fabric_finish']/$dzn_qnty)*$plan_cut_qnty;
						if($fabric_dyeing_cost<=0 && $yarn_dyeing_cost<=0)
						{
						$color_fab="red";
						}
						else
						{
						$color_fab="";	
						}
						if($yarn_costing<=0)
						{
						$color_yarn="red";
						}
						else
						{
						$color_yarn="";	
						}
						if($kniting_cost<=0)
						{
						$color_knit="red";
						}
						else
						{
						$color_knit="";	
						}
						if($fabric_finish<=0)
						{
						$color_finish="red";
						}
						else
						{
						$color_finish="";	
						}
						if($commercial_cost<=0)
						{
						$color_com="red";
						}
						else
						{
						$color_com="";	
						}
						$tot_yarn_cost_variance=$p_yarn_costing/$yarn_costing*100;
						$yarn_cost_variance_percent=($tot_yarn_cost_variance/$total_order_value)*100;
						$total_yarn_cost_variance_percent+=($tot_yarn_variance/$p_yarn_costing)*100;
					 ?>
                     <td width="100" align="right"><? echo number_format($total_avg_rate_percent_variance/$p_avg_rate*100,2).'%'; ?></td>
                     <td width="80" align="right" title="<? echo number_format($tot_yarn_variance/$p_yarn_costing*100,2).'%'; ?>" bgcolor="<? echo $color_yarn; ?>"><? echo number_format($tot_yarn_variance/$p_yarn_costing*100,2); ?></td>
                     <td width="80" align="right" title="<? echo $yarn_cost_variance_percent/$p_yarn_cost_percent*100; ?>"><? echo number_format($tot_yarn_cost_percent/$p_yarn_cost_percent*100,2).'%'; ?></td>
                     <td width="100" align="right"><? echo number_format($tot_fab_purchase_variance/$p_fab_purchase*100,2).'%'; ?></td>
                     <td width="80" title="<? echo $tot_knit_cost_dzn_variance/$p_knit_cost_dzn*100; ?>" align="right"><? echo number_format($tot_knit_cost_dzn_variance/$p_knit_cost_dzn*100,2).'%'; ?></td>
                     <td width="80" align="right" title="<? echo $tot_knit_cost; ?>"  bgcolor="<? echo $color_knit; ?>">
                    <? 
					 echo number_format($tot_knit_cost_variance/$tot_p_knit_cost*100,2).'%';
					  ?></td>
                     <td  width="100" align="right" title="<? echo number_format($p_yarn_dyeing_cost_dzn /$yarn_dyeing_cost_dzn*100 ,2); ?>" ><? echo number_format($tot_yarn_dyeing_cost_dzn_vairance/$p_yarn_dyeing_cost_dzn*100 ,2).'%'; ?></td>
                     <td  width="110" align="right" title="<? echo number_format($p_yarn_dyeing_cost/$yarn_dyeing_cost*100 ,2); ?>" ><? echo number_format($tot_yarn_dyeing_cost_variance/$p_yarn_dyeing_cost*100 ,2).'%'; ?></td>
                     <td  width="120" align="right"  title="<? echo number_format($tot_fabric_dyeing_variance/$p_fabric_dyeing_cost_dzn*100 ,2); ?>" ><? echo number_format($tot_fabric_dyeing_variance/$p_fabric_dyeing_cost_dzn*100,2).'%'; 
					 $total_fabrics_cost_summary+=($row[csf('po_quantity')]/$dzn_qnty)*$fabric_dyeing_cost_dzn;
					  ?></td>
                     <td  width="100" align="right" title="<? echo number_format($tot_fabric_dyeing_cost_variance/$p_fabric_dyeing_cost*100,2); ?>" bgcolor="<? echo $color_fab; ?>"><? echo number_format($tot_fabric_dyeing_cost_variance/$p_fabric_dyeing_cost*100,2).'%'; ?></td>
                     <td  width="90" align="right"><? echo number_format($tot_heat_variance_cost/$p_heat_setting_cost*100,2).'%'; ?></td>
                     <td  width="100" align="right" ><? echo number_format($tot_fabric_finish_variance_cost/$p_fabric_finish*100,2).'%'; ?> </td>
                     <td  width="90" align="right"><? echo number_format($tot_wash_cost_variance/$p_washing_cost*100,2).'%'; ?></td>
                     <td  width="90" align="right"><? echo number_format($tot_all_over_variance/$p_all_over_cost*100,2).'%'; ?></td>
				<?
					$total_cm_amount+=$cm_cost;
					$max_profit=$asking_profit_arr[$row[csf('company_name')]]['max_profit'];
					//echo $max_profit;
					$company_asking=$asking_profit_arr[$row[csf('company_name')]]['asking_profit'];
					if($tot_trim_cost_variance<=0)
						{
						$color_trim="red";
						}
						else
						{
						$color_trim="";	
						}
					if($cm_cost<=0)
						{
						$color="red";
						}
						else
						{
						$color="";	
						}
					$tot_foreign_variance=$p_foreign/$foreign*100;
					?>
                     <td width="100" align="right"><? echo number_format($tot_trim_cost_variance/$tot_p_trim_amount*100,2).'%'; ?></td>
                     <td width="80" align="right"><? echo number_format($tot_print_varinace/$p_print_amount*100,2).'%'; ?></td>
                     <td width="85" align="right"><? echo number_format($tot_embrodery_variance/$p_embroidery_amount*100,2).'%'; ?></td>
                     <td width="80" align="right"><? echo number_format($tot_special_variance/$p_special_amount*100,2).'%'; ?></td>
                     <td width="80" align="right"><? echo number_format($tot_wash_amount_variance/$p_wash_amount*100,2).'%'; ?></td>
                     <td width="80" align="right"><? echo number_format($tot_other_amount_variance/$p_other_amount*100,2).'%'; ?></td>
                     <td width="120" align="right"><? echo number_format($tot_commercial_variance/$tot_p_commercial_cost*100,2).'%'; ?></td>
                     <td width="120" align="right"><? echo number_format($tot_foreign_variance/$p_foreign*100,2).'%'; ?></td>
                     <td width="120" align="right"><? echo number_format($tot_local_variance/$p_local*100,2).'%'; ?></td>
                     <td width="100" align="right"><? echo number_format($tot_test_variance/$tot_p_test_cost*100,2).'%';?></td>
                     <td width="100" align="right"><? echo number_format($tot_feight_variance/$p_freight_cost*100,2).'%'; ?></td>
                     <td width="120" align="right"><? echo number_format($tot_inspection_variance/$p_inspection*100,2).'%';?></td>
                     <td width="100" align="right"><? echo number_format($tot_certificate_variance/$p_certificate_cost*100,2).'%'; ?></td>
                     <td width="100" align="right"><? echo number_format($tot_common_variance/$p_common_oh*100,2).'%'; ?></td>
                     <td width="100" align="right"><? echo number_format($tot_currier_variance/$p_currier_cost*100,2).'%';?></td>
                     <td width="120" align="right"><? echo number_format($tot_cm_dzn_variance/$p_cm_cost_dzn*100,2).'%';?></td>
                     <td width="100" align="right"><? echo number_format($tot_cm_variance/$p_cm_cost*100,2).'%';?></td>
                     <td width="100" align="right"><? echo number_format($tot_total_cost_varaince/$total_p_cost*100,2).'%'; ?></td>
                    <?
						$total_cost_vari_percent_amount=$total_p_cost/$total_cost*100;
						$total_profit_vari_percnt_amount=$total_order_value-$total_cost_vari_percent_amount;
						$total_profit_percentage2=$total_profit/$total_order_value*100; 
						if($total_profit_percentage2<=0 )
						{
							$color_pl="red";
						}
						else if($total_profit_percentage2>$max_profit)
						{
							$color_pl="yellow";	
						}
						else if($total_profit_percentage2<=$max_profit)
						{
							$color_pl="green";	
						}
						else
						{
							$color_pl="";	
						}
					?>
                     <td width="100" align="right"><? echo number_format($tot_profit_variance/$total_p_profit*100,2).'%'; ?></td>
                     <td width="100" align="right"><? echo number_format($tot_profit_percient_varaince/$total_p_profit_percentage2*100,2).'%'; ?></td>
                     <td width="100" align="right">
					 <?  
					 $expected_profit=$asking_profit_arr[$row[csf('company_name')]]['asking_profit']*$total_order_value/100; 
					echo '-';// echo number_format($tot_expected_profit_varaince/$expected_profit*100,2).'%'; 
					 ?>
                     </td>
                     <td width="" align="right"><? $expect_variance=$total_profit-$expected_profit; echo '-'; //number_format($tot_expected_varaince_data_vairance /$tot_expect_variance*100,2).'%'; ?></td>
                  </tr>
                <?
				$total_order_qty+=$row[csf('po_quantity')];
				$total_yarn_dyeing_cost+=$yarn_dyeing_cost;
				$total_yarn_cost+=$yarn_costing;
				$total_purchase_cost+=$fab_purchase;
				$total_knitting_cost+=$tot_knit_cost;
				$total_fabric_dyeing_cost+=$fabric_dyeing_cost;
				$total_heat_setting_cost+=$heat_setting_cost;
				$total_finishing_cost+=$fabric_finish;
				$total_washing_cost+=$washing_cost;
				$all_over_print_cost+=$all_over_cost;
				$total_trim_cost+=$tot_trim_amount;
				$total_commercial_cost+=$tot_commercial_cost;
				$total_fab_cost_amount=$total_yarn_cost+$total_purchase_cost+$total_knitting_cost+$total_yarn_dyeing_cost+$total_fabric_dyeing_cost+$total_heat_setting_cost+$total_finishing_cost+$total_washing_cost+$all_over_print_cost;
				$total_fab_cost_amount2+=$total_fab_cost_amount;
				$total_embelishment_cost+=$print_amount+$embroidery_amount+$special_amount+$other_amount;
				$total_commssion+=$foreign+$local;
				$total_testing_cost+=$tot_test_cost;
				$total_freight_cost+=$freight_cost;
				$total_cm_cost+=$cm_cost;
				$total_cost_up+=$total_cost;
				
				$total_inspection+=$inspection;
				$total_certificate_cost+=$certificate_cost;
				$total_common_oh+=$common_oh;
				$total_currier_cost+=$currier_cost;
				$total_fab_profit+=$total_profit;
				$total_expected_profit+=$expected_profit;
				$total_expt_profit_percentage+=$total_profit_percentage;
				$total_expect_variance+=$expect_variance;
				$total_profit_fab_percentage_up+=$total_profit_percentage2;
				//Pre Cost end;
				
				$total_p_yarn_dyeing_cost+=$p_yarn_dyeing_cost;
				$total_p_yarn_cost+=$p_yarn_costing;
				$total_p_purchase_cost+=$p_fab_purchase;
				$total_p_knitting_cost+=$tot_p_knit_cost;
				$total_p_fabric_dyeing_cost+=$p_fabric_dyeing_cost;
				$total_p_heat_setting_cost+=$p_heat_setting_cost;
				$total_p_finishing_cost+=$p_fabric_finish;
				$total_p_washing_cost+=$p_washing_cost;
				$p_all_over_print_cost+=$p_all_over_cost;
				$total_p_trim_cost+=$tot_p_trim_amount;
				$total_p_commercial_cost+=$tot_p_commercial_cost;
				
				$total_p_fab_cost_amount=$total_p_yarn_cost+$total_p_purchase_cost+$total_p_knitting_cost+$total_p_yarn_dyeing_cost+$total_p_fabric_dyeing_cost+$total_p_heat_setting_cost+$total_p_finishing_cost+$total_p_washing_cost+$p_all_over_print_cost;
				
				$total_fab_cost_amount2+=$total_fab_cost_amount;
				$total_p_embelishment_cost+=$p_print_amount+$p_embroidery_amount+$p_special_amount+$p_other_amount;
				$total_p_commssion+=$p_foreign+$p_local;
				$total_p_testing_cost+=$tot_p_test_cost;
				$total_p_freight_cost+=$p_freight_cost;
				$total_p_cm_cost+=$p_cm_cost;
				$total_p_cost_up+=$total_p_cost;
				
				$total_p_inspection+=$p_inspection;
				$total_p_certificate_cost+=$p_certificate_cost;
				$total_p_common_oh+=$p_common_oh;
				$total_p_currier_cost+=$p_currier_cost;
				$total_p_fab_profit+=$total_p_profit;
				$total_p_expected_profit+=$p_expected_profit;
				$total_p_expt_profit_percentage+=$total_p_profit_percentage;
				$total_p_expect_variance+=$p_expected_profit;
				
				$total_p_profit_fab_percentage_up+=$total_p_profit_percentage2;
				//Varaince Start here;
				$total_yarn_pp_variance+=$tot_yarn_variance;
				$total_yarncost_percent+=$tot_yarn_cost_percent;
				$total_fab_purchase_variance+=$p_fab_purchase-$fab_purchase;
				$total_knit_variance+=$tot_p_knit_cost-$tot_knit_cost;
				$total_yarn_dyeing_variance+=$p_yarn_dyeing_cost-$yarn_dyeing_cost ;
				$total_fab_dyeing_cost_variance+=$p_fabric_dyeing_cost-$fabric_dyeing_cost;
				$total_heatsetting_variance+=$p_heat_setting_cost-$heat_setting_cost;
				$total_fab_finish_varince+=$p_fabric_finish-$fabric_finish;
				$total_wash_cost_variance+=$p_washing_cost-$washing_cost;
				$total_all_over_variance+=$p_all_over_cost-$all_over_cost;
				$total_trim_variance+=$tot_p_trim_amount-$tot_trim_amount;
				$total_print_variance+=$p_print_amount-$print_amount;
				$total_embrod_variance+=$p_embroidery_amount-$embroidery_amount;
				$total_special_variance+=$p_special_amount-$special_amount;
				$total_wash_variance+=$p_wash_amount-$wash_amount;
				$total_other_variance+=$p_other_amount-$other_amount;
				$total_local_variance+=$p_local-$local;
				
				$total_commcercial_variance+=$tot_p_commercial_cost-$tot_commercial_cost;
				
				$total_foreign_variance+=$p_foreign-$foreign;
				$total_test_variance+=$tot_p_test_cost-$tot_test_cost;
				$total_freigt_variance+=$p_freight_cost-$freight_cost;
				$total_inspection_variance+=$p_inspection-$inspection;
				$total_certificate_variance+=$p_certificate_cost-$certificate_cost;
				$total_common_oh_variance+=$p_common_oh-$common_oh;
				$total_currier_variance+=$p_currier_cost-$currier_cost;
				$total_cm_variance_dzn+=$p_cm_cost_dzn-$cm_cost_dzn;
				$total_cm_cost_variance+=$p_cm_cost-$cm_cost;
				$total_cost_variance+=$total_order_amount-($total_p_cost-$total_cost);
				$total_profit_variance+=$total_profit-$total_p_profit;
				//echo $total_cost_variance;
				$total_p_profit_fab_percentage=$total_profit_variance/$total_p_fab_profit*100;
				$total_profit_variance_percent_variance=$total_profit_variance/$total_p_fab_profit*100;
				$total_expected_profit_variance+=$total_expected_profit-$total_p_expect_variance;
				$total_expected_profit_vari_varianace+=$tot_expected_varaince_data_vairance;
				 //Variance % start
				$total_v_fab_cost_varaince=$total_yarn_pp_variance+$total_fab_purchase_variance+$total_knit_variance+$total_yarn_dyeing_variance+
				$total_fab_dyeing_cost_variance+$total_heatsetting_variance+$total_fab_finish_varince+$total_wash_cost_variance+$total_all_over_variance;
				$total_v_embellish_cost_variance=$total_print_variance+$total_embrod_variance+$total_special_variance+$total_other_variance;
				$total_v_commission_variance=$total_foreign_variance+$total_local_variance;
				//$total_yarn_cost_variance_percent+=$p_yarn_costing/$yarn_costing*100;
				$total_purchase_variance_percent+=$tot_fab_purchase_variance/$p_fab_purchase*100;
				$total_knit_cost_variance_percent+=$tot_knit_cost_dzn_variance/$p_knit_cost_dzn*100;
				$total_yarn_dyeing_cost_variance_percent+=$tot_yarn_dyeing_cost_dzn_vairance/$p_yarn_dyeing_cost_dzn*100;
				
				$total_fab_variance_percent+=$tot_fabric_dyeing_variance/$p_fabric_dyeing_cost_dzn*100;
				$total_heat_variance_percent+=$tot_heat_variance_cost/$p_heat_setting_cost*100;
				$total_fab_finish_variance_percent+=$tot_fabric_finish_variance_cost/$p_fabric_finish*100;
				$total_wash_cost_variance_percent+=$tot_wash_cost_variance/$p_washing_cost*100;
				$total_all_over_variance_percent+=$tot_all_over_variance/$p_all_over_cost*100;
				$total_trim_variance_percent+=$tot_trim_cost_variance/$tot_p_trim_amount*100;
				$total_print_amount_variance_percent+=$tot_print_varinace/$p_print_amount*100;
				$total_embrodery_variance_percent+=$tot_embrodery_variance/$p_embroidery_amount*100;
				$total_special_variance_percent+=$tot_special_variance/$p_special_amount*100;
				$total_wash_variance_percent+=$tot_wash_amount_variance/$p_wash_amount*100;
				$total_other_variance_percent+=$tot_other_amount_variance/$p_other_amount*100;
				$total_commcercial_variance_percent+=$tot_commercial_variance/$tot_p_commercial_cost*100;
				$total_foreign_variance_percent+=$tot_foreign_variance/$p_foreign*100;
//echo $total_foreign_variance_percent;
				$total_local_variance_percent+=$tot_local_variance/$p_local*100;
				$total_test_variance_percent+=$tot_test_variance/$tot_p_test_cost*100;
				$total_freight_variance_percent+=$tot_feight_variance/$p_freight_cost*100;
				$total_inspection_variance_percent+=$tot_inspection_variance/$p_inspection*100;
				$total_certificate_variance_percent+=$tot_certificate_variance/$p_certificate_cost*100;
				$total_common_variance_percent+=$tot_common_variance/$p_common_oh*100;
				$total_currier_variance_percent+=$tot_currier_variance/$p_currier_cost*100;
				$total_cm_cost_variance_percent+=$tot_cm_variance/$p_cm_cost*100;
				$total_cost_variance_percent+=$tot_total_cost_varaince/$total_p_cost*100;
				
				$total_profit_variance_percent+=$tot_profit_variance/$total_p_profit*100;
				$total_profit_fab_percentage_variance_percent+=$tot_profit_percient_varaince/$total_p_profit_percentage2*100;
				$tot_yarn_cost_vari_pernct+=$tot_yarn_cost_percent/$p_yarn_cost_percent*100;//$yarn_cost_variance_percent;
				$total_expected_profit_variance_percent_amount+=$tot_expected_profit_varaince/$p_expected_profit*100;
				$tot_expected_profit_variance+=$tot_expected_varaince_data_vairance /$tot_expect_variance*100;
				//echo $tot_expected_varaince/$p_expect_variance*100;
				$i++;
				}
               ?>
                </table>
                <table class="rpt_table" width="4750" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <tfoot>
                     <th width="40"></th>
                     <th width="70"></th>
                     <th width="70"></th>
                     <th width="100"></th>
                     <th width="100"></th>
                     <th width="110"></th>
                     <th width="110"></th>
                     <th width="110"></th>
                     <th width="70">Price Quote</th>
                     <th width="90" align="right" id="total_order_qnty"><? echo number_format($total_order_qty,2); ?></th>
                     <th width="90"></th>
                     <th width="100" align="right" id="total_order_amount2"><? echo number_format($total_order_amount,2); ?></th>
                     <th width="100"></th>
                     <th width="100"></th>
                     <th width="80" align="right" id="total_yarn_cost2"><? echo number_format($tot_yarn_cost_price,2); ?></th>
                     <th width="80" align="right" ><?  $total_p_yarn_cost_percentage=$tot_yarn_cost_price/$total_order_amount*100;  echo number_format($total_p_yarn_cost_percentage,2); ?></th>
                     <th width="100" align="right"><? echo number_format($total_p_purchase_cost,2); ?></th>
                     <th width="80"></th>
                     <th width="80" align="right"><? echo number_format($total_p_knitting_cost,2); ?></th>
                     <th width="100"></th>
                     <th width="110" align="right"><? echo number_format($total_p_yarn_dyeing_cost,2); ?></th>
                     <th width="120"><? ?></th>
                     <th width="100" align="right" id="total_fabric_dyeing_cost4"><? echo number_format($total_p_fabric_dyeing_cost,2); ?></th>
                     <th width="90" align="right"><? echo number_format($total_p_heat_setting_cost,2); ?></th>		
                     <th width="100" align="right"><? echo number_format($total_p_finishing_cost,2); ?></th>
                     <th width="90" align="right"><? echo number_format($total_p_washing_cost,2); ?></th>
                     <th width="90" align="right"><? echo number_format($p_all_over_print_cost,2); ?></th>
                     <th width="100" align="right"><strong><? echo number_format($total_p_trim_cost,2); ?></strong></th>
                     <th width="80" align="right"><strong><? echo number_format($total_p_print_amount,2); ?></strong></th>
                     <th width="85" align="right"><strong><? echo number_format($total_p_embroidery_amount,2); ?></strong></th>
                     <th width="80" align="right"><strong> <? echo number_format($total_p_special_amount,2); ?></strong></th>
                      <th width="80" align="right"><strong> <? echo number_format($total_p_wash_amount,2); ?></strong></th>
                     <th width="80" align="right"><strong><? echo number_format($total_p_other_amount,2); ?></strong></th>
                     <th width="120" align="right"><strong><? echo number_format($total_p_commercial_cost,2); ?></strong></th>
                     <th width="120" align="right"><strong><? echo number_format($total_p_foreign_amount,2); ?></strong></th>
                     <th width="120" align="right"><strong><? echo number_format($total_p_local_amount,2); ?></strong></th>
                     <th width="100" align="right"><strong><? echo number_format($total_p_test_cost_amount,2); ?></strong></th>
                     <th width="100" align="right"><strong><? echo number_format($total_p_freight_amount,2); ?></strong></th>
                     <th width="120" align="right"><strong><? echo number_format($total_p_inspection_amount,2); ?></strong></th>
                     <th width="100" align="right"><strong><? echo number_format($total_p_certificate_amount,2); ?></strong></th>
                     <th width="100" align="right"><strong><? echo number_format($total_p_common_oh_amount,2); ?></strong></th>
                     <th width="100" align="right"><strong><? echo number_format($total_p_currier_amount,2); ?></strong></th>
                     <th width="120"></th>
                     <th width="100" align="right"><strong><? echo number_format($total_p_cm_amount,2); ?></strong></th>
                     <th width="100" id="total_cost_up" align="right"><strong><? echo number_format($total_p_cost_up,2); ?></strong></th>
                     <th width="100" align="right"><strong><? echo number_format($total_p_fab_profit,2);?></strong></th>
                     <th width="100" align="right"><strong><?  echo number_format($total_p_profit_fab_percentage,2);?></strong></th>
                     <th width="100"  align="right"><strong><? echo number_format($total_p_expected_profit,2);?></strong></th>
                     <th width=""  align="right"><strong><? echo number_format($total_p_expect_variance,2);?></strong></th>
                  </tfoot>
                </table>
                 <table class="rpt_table" width="4750" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <tfoot>
                     <th width="40"></th>
                     <th width="70"></th>
                     <th width="70"></th>
                     <th width="100"></th>
                     <th width="100"></th>
                     <th width="110"></th>
                     <th width="110"></th>
                     <th width="110"></th>
                     <th width="70">Pre Cost</th>
                     <th width="90" align="right" id="total_order_qnty"><? echo number_format($total_order_qty,2); ?></th>
                     <th width="90"></th>
                     <th width="100" align="right" id="total_order_amount2"><? echo number_format($total_order_amount,2); ?></th>
                     <th width="100"></th>
                     <th width="100"></th>
                     <th width="80" align="right" id="total_yarn_cost2"><? echo number_format($total_yarn_cost,2); ?></th>
                     <th width="80" align="right" ><?  $total_yarn_cost_percentage=$total_yarn_cost/$total_order_amount*100;  echo number_format($total_yarn_cost_percentage,2); ?></th>
                     <th width="100" align="right"><? echo number_format($total_purchase_cost,2); ?></th>
                     <th width="80"></th>
                     <th width="80" align="right"><? echo number_format($total_knitting_cost,2); ?></th>
                     <th width="100"></th>
                     <th width="110" align="right"><? echo number_format($total_yarn_dyeing_cost,2); ?></th>
                     <th width="120"><? ?></th>
                     <th width="100" align="right" id="total_fabric_dyeing_cost4"><? echo number_format($total_fabric_dyeing_cost,2); ?></th>
                     <th width="90" align="right"><? echo number_format($total_heat_setting_cost,2); ?></th>		
                     <th width="100" align="right"><? echo number_format($total_finishing_cost,2); ?></th>
                     <th width="90" align="right"><? echo number_format($total_washing_cost,2); ?></th>
                     <th width="90" align="right"><? echo number_format($all_over_print_cost,2); ?></th>
                     <th width="100" align="right"><strong><? echo number_format($total_trim_cost,2); ?></strong></th>
                     <th width="80" align="right"><strong><? echo number_format($total_print_amount,2); ?></strong></th>
                     <th width="85" align="right"><strong><? echo number_format($total_embroidery_amount,2); ?></strong></th>
                     <th width="80" align="right"><strong> <? echo number_format($total_special_amount,2); ?></strong></th>
                     <th width="80" align="right"><strong><? echo number_format($total_wash_amount,2); ?></strong></th>
                     <th width="80" align="right"><strong><? echo number_format($total_other_amount,2); ?></strong></th>
                     <th width="120" align="right"><strong><? echo number_format($total_commercial_cost,2); ?></strong></th>
                     <th width="120" align="right"><strong><? echo number_format($total_foreign_amount,2); ?></strong></th>
                     <th width="120" align="right"><strong><? echo number_format($total_local_amount,2); ?></strong></th>
                     <th width="100" align="right"><strong><? echo number_format($total_test_cost_amount,2); ?></strong></th>
                     <th width="100" align="right"><strong><? echo number_format($total_freight_amount,2); ?></strong></th>
                     <th width="120" align="right"><strong><? echo number_format($total_inspection_amount,2); ?></strong></th>
                     <th width="100" align="right"><strong><? echo number_format($total_certificate_amount,2); ?></strong></th>
                     <th width="100" align="right"><strong><? echo number_format($total_common_oh_amount,2); ?></strong></th>
                     <th width="100" align="right"><strong><? echo number_format($total_currier_amount,2); ?></strong></th>
                     <th width="120"></th>
                     <th width="100" align="right"><strong><? echo number_format($total_cm_cost,2); ?></strong></th>
                     <th width="100" id="total_cost_up" align="right"><strong><? echo number_format($total_cost_up,2); ?></strong></th>
                     <th width="100" align="right"><strong><? echo number_format($total_fab_profit,2);?></strong></th>
                     <th width="100" align="right"><strong><? $total_profit_fab_percentage=$total_fab_profit/$total_order_amount*100; echo number_format($total_profit_fab_percentage,2);?></strong></th>
                     <th width="100"  align="right"><strong><? echo number_format($total_expected_profit,2);?></strong></th>
                     <th width=""  align="right"><strong><? echo number_format($total_expect_variance,2);?></strong></th>
                  </tfoot>
                </table>
                 <table class="rpt_table" width="4750" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <tfoot>
                     <th width="40"></th>
                     <th width="70"></th>
                     <th width="70"></th>
                     <th width="100"></th>
                     <th width="100"></th>
                     <th width="110"></th>
                     <th width="110"></th>
                     <th width="110"></th>
                     <th width="70"> Variance</th>
                     <th width="90" align="right" id="total_order_qnty"><? echo number_format($total_order_qty,2); ?></th>
                     <th width="90"></th>
                     <th width="100" align="right" id="total_order_amount2"><? echo number_format($total_order_amount,2); ?></th>
                     <th width="100"></th>
                     <th width="100"></th>
                     <th width="80" align="right" id="total_yarn_cost2"><? echo number_format($total_yarn_pp_variance,2); ?></th>
                     <th width="80" align="right" ><?  $total_p_yarn_cost_percentage=$total_p_yarn_cost/$total_order_amount*100;  echo number_format($total_yarn_pp_variance/$total_order_amount*100,2); ?></th>
                     <th width="100" align="right"><? echo number_format($total_fab_purchase_variance,2); ?></th>
                     <th width="80"></th>
                     <th width="80" align="right"><? echo number_format($total_knit_variance,2); ?></th>
                     <th width="100"></th>
                     <th width="110" align="right"><? echo number_format($total_yarn_dyeing_variance,2); ?></th>
                     <th width="120"><? ?></th>
                     <th width="100" align="right" id="total_fabric_dyeing_cost4"><? echo number_format($total_fab_dyeing_cost_variance,2); ?></th>
                     <th width="90" align="right"><? echo number_format($total_heatsetting_variance,2); ?></th>		
                     <th width="100" align="right"><? echo number_format($total_fab_finish_varince,2); ?></th>
                     <th width="90" align="right"><? echo number_format($total_wash_cost_variance,2); ?></th>
                     <th width="90" align="right"><? echo number_format($total_all_over_variance,2); ?></th>
                     <th width="100" align="right"><strong><? echo number_format($total_trim_variance,2); ?></strong></th>
                     <th width="80" align="right"><strong><? echo number_format($total_print_variance,2); ?></strong></th>
                     <th width="85" align="right"><strong><? echo number_format($total_embrod_variance,2); ?></strong></th>
                     <th width="80" align="right"><strong> <? echo number_format($total_special_variance,2); ?></strong></th>
                     <th width="80" align="right"><strong><? echo number_format($total_wash_variance,2); ?></strong></th>
                     <th width="80" align="right"><strong><? echo number_format($total_other_variance,2); ?></strong></th>
                     <th width="120" align="right"><strong><? echo number_format($total_commcercial_variance,2); ?></strong></th>
                     <th width="120" align="right"><strong><? echo number_format($total_foreign_variance,2); ?></strong></th>
                     <th width="120" align="right"><strong><? echo number_format($total_local_variance,2); ?></strong></th>
                     <th width="100" align="right"><strong><? echo number_format($total_test_variance,2); ?></strong></th>
                     <th width="100" align="right"><strong><? echo number_format($total_freigt_variance,2); ?></strong></th>
                     <th width="120" align="right"><strong><? echo number_format($total_inspection_variance,2); ?></strong></th>
                     <th width="100" align="right"><strong><? echo number_format($total_certificate_variance,2); ?></strong></th>
                     <th width="100" align="right"><strong><? echo number_format($total_common_oh_variance,2); ?></strong></th>
                     <th width="100" align="right"><strong><? echo number_format($total_currier_variance,2); ?></strong></th>
                     <th width="120"></th>
                     <th width="100" align="right"><strong><? echo number_format($total_cm_cost_variance,2); ?></strong></th>
                     <th width="100" id="total_cost_up" align="right"><strong><? echo number_format($total_cost_variance,2); ?></strong></th>
                     <th width="100" align="right"><strong><? echo number_format($total_profit_variance,2);?></strong></th>
                     <th width="100" align="right"><strong><? $total_profit_fab_percentage_variance=$total_profit_variance/$total_order_amount*100; echo number_format($total_profit_fab_percentage_variance,2);?></strong></th>
                     <th width="100"  align="right"><strong><? echo number_format($total_expected_profit_variance,2);?></strong></th>
                     <th width=""  align="right"><strong><? echo number_format($total_expected_profit_vari_varianace ,2);?></strong></th>
                  </tfoot>
                </table>
                 <table class="rpt_table" width="4750" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <tfoot>
                     <th width="40"></th>
                     <th width="70"></th>
                     <th width="70"></th>
                     <th width="100"></th>
                     <th width="100"></th>
                     <th width="110"></th>
                     <th width="110"></th>
                     <th width="110"></th>
                     <th width="70"> Variance %</th>
                     <th width="90" align="right" id="total_order_qnty"><? echo number_format($total_order_qty,2); ?></th>
                     <th width="90"></th>
                     <th width="100" align="right" id="total_order_amount2"><? echo number_format($total_order_amount,2); ?></th>
                     <th width="100"></th>
                     <th width="100"></th>
                     <th width="80" align="right" id="total_yarn_cost2"><? echo number_format($total_yarn_cost_variance_percent,2); ?></th>
                     <th width="80" align="right" ><?  echo number_format($tot_yarn_cost_vari_pernct,2); ?></th>
                     <th width="100" align="right"><? echo number_format($total_purchase_variance_percent,2); ?></th>
                     <th width="80"></th>
                     <th width="80" align="right"><? echo number_format($total_knit_cost_variance_percent,2); ?></th>
                     <th width="100"></th>
                     <th width="110" align="right"><? echo number_format($total_yarn_dyeing_cost_variance_percent,2); ?></th>
                     <th width="120"><? ?></th>
                     <th width="100" align="right" id="total_fabric_dyeing_cost4"><? echo number_format($total_fab_variance_percent,2); ?></th>
                     <th width="90" align="right"><? echo number_format($total_heat_variance_percent,2); ?></th>		
                     <th width="100" align="right"><? echo number_format($total_fab_finish_variance_percent,2); ?></th>
                     <th width="90" align="right"><? echo number_format($total_wash_cost_variance_percent,2); ?></th>
                     <th width="90" align="right"><? echo number_format($total_all_over_variance_percent,2); ?></th>
                     <th width="100" align="right"><strong><? echo number_format($total_trim_variance_percent,2); ?></strong></th>
                     <th width="80" align="right"><strong><? echo number_format($total_print_amount_variance_percent,2); ?></strong></th>
                     <th width="85" align="right"><strong><? echo number_format($total_embrodery_variance_percent,2); ?></strong></th>
                     <th width="80" align="right"><strong> <? echo number_format($total_special_variance_percent,2); ?></strong></th>
                     <th width="80" align="right"><strong><? echo number_format($total_wash_variance_percent,2); ?></strong></th>
                     <th width="80" align="right"><strong><? echo number_format($total_other_variance_percent,2); ?></strong></th>
                     <th width="120" align="right"><strong><? echo number_format($total_commcercial_variance_percent,2); ?></strong></th>
                     <th width="120" align="right"><strong><? echo number_format($total_foreign_variance_percent,2); ?></strong></th>
                     <th width="120" align="right"><strong><? echo number_format($total_local_variance_percent,2); ?></strong></th>
                     <th width="100" align="right"><strong><? echo number_format($total_test_variance_percent,2); ?></strong></th>
                     <th width="100" align="right"><strong><? echo number_format($total_freight_variance_percent,2); ?></strong></th>
                     <th width="120" align="right"><strong><? echo number_format($total_inspection_variance_percent,2); ?></strong></th>
                     <th width="100" align="right"><strong><? echo number_format($total_certificate_variance_percent,2); ?></strong></th>
                     <th width="100" align="right"><strong><? echo number_format($total_common_variance_percent,2); ?></strong></th>
                     <th width="100" align="right"><strong><? echo number_format($total_currier_variance_percent,2); ?></strong></th>
                     <th width="120"></th>
                     <th width="100" align="right"><strong><? echo number_format($total_cm_cost_variance_percent,2); ?></strong></th>
                     <th width="100" id="total_cost_up" align="right"><strong><? echo number_format($total_cost_variance_percent,2); ?></strong></th>
                     <th width="100" align="right"><strong><? echo number_format($total_profit_variance_percent,2);?></strong></th>
                     <th width="100" align="right"><strong><? $total_profit_fab_percentage_variance_percent=$total_profit_variance/$total_order_amount*100; echo number_format($total_profit_variance_percent,2);?></strong></th>
                     <th width="100"  align="right"><strong><? echo number_format($total_expected_profit_variance_percent_amount,2);?></strong></th>
                     <th width=""  align="right"><strong><? echo number_format($tot_expected_profit_variance,2);?></strong></th>
                  </tfoot>
                </table>
                <?
                $fab_percent=($total_fab_cost_amount*100)/$total_plancut_amount;
				$fab_percent=$fab_percent;
				$trim_percent=($total_trim_cost*100)/$total_order_amount;
				$trim_percent=$trim_percent;
				$embelishment_percent=($total_embelishment_cost*100)/$total_plancut_amount;
				$embelishment_percent=$embelishment_percent;
				$total_commercial_percent=($total_commercial_cost*100)/$total_order_amount;
				$total_commercial_percent=$total_commercial_percent;
				
				$total_commssion_percent=(($total_commssion*100)/$total_order_amount);
				$total_testing_cost_percent=(($total_testing_cost*100)/$total_order_amount);
				$total_freight_cost_percent=(($total_freight_cost*100)/$total_order_amount);
				$total_cost_percent=(($total_cost_up*100)/$total_order_amount);
				$total_cm_percent=(($total_cm_cost*100)/$total_order_amount);
				$total_order_amount_percent=(($total_order_amount*100)/$total_order_amount);
				
				$total_inspection_percent=(($total_inspection*100)/$total_order_amount);
				$total_certificate_percent=(($total_certificate_cost*100)/$total_order_amount);
				$total_common_oh_percent=(($total_common_oh*100)/$total_order_amount);
				$total_currier_cost_percent=(($total_currier_cost*100)/$total_order_amount);
				$all_tot_cost_percentage=$total_cost_up/$total_order_amount*100;
				$total_expected_profit_percent=(($total_expected_profit*100)/$total_order_amount);
				$total_expected_profit_variance_percent=(($total_expect_variance*100)/$total_order_amount);
				//Pre cost End
				$p_fab_percent=($total_p_fab_cost_amount*100)/$total_plancut_amount;
				$p_fab_percent=$p_fab_percent;
				$trim_p_percent=($total_p_trim_cost*100)/$total_order_amount;
				$trim_p_percent=$trim_p_percent;
				$p_embelishment_percent=($total_p_embelishment_cost*100)/$total_plancut_amount;
				$p_embelishment_percent=$p_embelishment_percent;
				$total_p_commercial_percent=($total_p_commercial_cost*100)/$total_order_amount;
				$total_p_commercial_percent=$total_p_commercial_percent;
				
				$total_p_commssion_percent=(($total_p_commssion*100)/$total_order_amount);
				$total_p_testing_cost_percent=(($total_p_testing_cost*100)/$total_order_amount);
				$total_p_freight_cost_percent=(($total_p_freight_cost*100)/$total_order_amount);
				$total_p_cost_percent=(($total_p_cost_up*100)/$total_order_amount);
				$total_p_cm_percent=(($total_p_cm_cost*100)/$total_order_amount);
				$total_p_order_amount_percent=(($total_order_amount*100)/$total_order_amount);
				
				$total_p_inspection_percent=(($total_p_inspection*100)/$total_order_amount);
				$total_p_certificate_percent=(($total_p_certificate_cost*100)/$total_order_amount);
				$total_p_common_oh_percent=(($total_p_common_oh*100)/$total_order_amount);
				$total_p_currier_cost_percent=(($total_p_currier_cost*100)/$total_order_amount);
				$p_all_tot_cost_percentage=$total_p_cost_up/$total_order_amount*100;
				$total_p_expected_profit_percent=(($total_p_expected_profit*100)/$total_order_amount);
				$total_p_expected_profit_variance_percent=(($total_p_expect_variance*100)/$total_order_amount);
				//Price Quotation End;
				//echo $total_v_fab_cost_varaince;
				$v_fab_percent=($total_v_fab_cost_varaince*100)/$total_plancut_amount;
				$v_trim_percent=($total_trim_variance*100)/$total_order_amount;
				$v_embelishment_percent=$total_v_embellish_cost_variance/$total_plancut_amount*100;
				$total_v_commercial_percent=$total_commcercial_variance/$total_order_amount*100;
				$total_v_commssion_percent=(($total_v_commission_variance*100)/$total_order_amount);
				$total_v_testing_cost_percent=(($total_test_variance*100)/$total_order_amount);
				$total_v_freight_cost_percent=(($total_freigt_variance*100)/$total_order_amount);
				$total_v_cost_percent=(($total_cost_variance*100)/$total_order_amount);
				$total_v_cm_percent=(($total_cm_cost_variance*100)/$total_order_amount);
				$total_v_order_amount_percent=(($total_order_amount*100)/$total_order_amount);
				$total_v_inspection_percent=(($total_inspection_variance*100)/$total_order_amount);
				$total_v_certificate_percent=(($total_certificate_variance*100)/$total_order_amount);
				$total_v_common_oh_percent=(($total_common_oh_variance*100)/$total_order_amount);
				$total_v_currier_cost_percent=(($total_currier_variance*100)/$total_order_amount);
				$v_all_tot_cost_percentage=$v_fab_percent+$v_trim_percent+$v_embelishment_percent+$total_v_commercial_percent+$total_v_commssion_percent+$total_v_testing_cost_percent+$total_v_freight_cost_percent+$total_v_cm_percent+$total_v_inspection_percent+$total_v_common_oh_percent+$total_v_currier_cost_percent;
			
				$total_v_expected_profit_percent=(($total_expected_profit_variance*100)/$total_order_amount);
				$total_v_expected_profit_variance_percent=(($tot_expected_profit_variance*100)/$total_p_expected_profit_variance_percent);
				//echo $total_expected_profit_variance;$tot_expected_profit_variance;
				
				?>
                  <input type="hidden" id="total_fab_cost" value="<? echo number_format($total_fab_cost_amount,2); ?>">
                  <input type="hidden" id="total_fab_percent" value="<? echo number_format($fab_percent,2)."%"; ?>">
                  <input type="hidden" id="total_trim_cost" value="<? echo number_format($total_trim_cost,2); ?>">
                  <input type="hidden" id="total_trim_percent" value="<? echo number_format($trim_percent,2)."%"; ?>">
                  <input type="hidden" id="total_embelishment_cost" value="<? echo number_format($total_embelishment_cost,2); ?>">
                  <input type="hidden" id="total_embelishment_percent" value="<? echo number_format($embelishment_percent,2)."%"; ?>">
                  <input type="hidden" id="total_commercial_cost" value="<? echo number_format($total_commercial_cost,2); ?>">
                  <input type="hidden" id="total_commercial_percent" value="<? echo number_format($total_commercial_percent,2)."%"; ?>">
                  <input type="hidden" id="total_commssion_cost" value="<? echo number_format($total_commssion,2); ?>">
                  <input type="hidden" id="total_commssion_percent" value="<? echo number_format($total_commssion_percent,2)."%"; ?>">
                  <input type="hidden" id="total_testing_cost" value="<? echo number_format($total_testing_cost,2); ?>">
                  <input type="hidden" id="total_testing_cost_percent" value="<? echo number_format($total_testing_cost_percent,2)."%"; ?>">
                  <input type="hidden" id="total_freight_cost" value="<? echo number_format($total_freight_cost,2); ?>">
                  <input type="hidden" id="total_freight_cost_percent" value="<? echo number_format($total_freight_cost_percent,2)."%"; ?>">
                  <input type="hidden" id="total_cost_up2" value="<? echo number_format($total_cost_up,2); ?>">
                  <input type="hidden" id="total_cost_percent" value="<? echo number_format($all_tot_cost_percentage,2)."%"; ?>">
                  <input type="hidden" id="total_cm_cost" value="<? echo number_format($total_cm_cost,2); ?>">
                  <input type="hidden" id="total_cm_percent" value="<? echo number_format($total_cm_percent,2)."%"; ?>">
                  <input type="hidden" id="total_order_amount" value="<? echo number_format($total_order_amount,2); ?>">
                  <input type="hidden" id="total_order_amount_percent" value="<? echo number_format($total_order_amount_percent,2)."%"; ?>">
                  <input type="hidden" id="total_inspection" value="<? echo number_format($total_inspection,2); ?>">
                  <input type="hidden" id="total_inspection_percent" value="<? echo number_format($total_inspection_percent,2)."%"; ?>">
                  <input type="hidden" id="total_certificate_cost" value="<? echo number_format($total_certificate_cost,2); ?>">
                  <input type="hidden" id="total_certificate_percent" value="<? echo number_format($total_certificate_percent,2)."%"; ?>">
                  <input type="hidden" id="total_common_oh" value="<? echo number_format($total_common_oh,2); ?>">
                  <input type="hidden" id="total_common_oh_percent" value="<? echo number_format($total_common_oh_percent,2)."%"; ?>">
                  <input type="hidden" id="total_currier_cost" value="<? echo number_format($total_currier_cost,2); ?>">
                  <input type="hidden" id="total_currier_cost_percent" value="<? echo number_format($total_currier_cost_percent,2)."%"; ?>">
                  <input type="hidden" id="total_fab_profit_id" value="<? echo number_format($total_fab_profit,2); ?>">
                  <input type="hidden" id="total_expected_profit_id" value="<? echo number_format($total_expected_profit,2); ?>">
                  <input type="hidden" id="total_expt_profit_variance_id" value="<? echo number_format($total_expect_variance,2); ?>">
                   <input type="hidden" id="total_profit_fab_percentage_id" value="<? echo number_format($total_profit_fab_percentage,2)."%"; ?>">
                   <input type="hidden" id="total_expt_profit_percentage_id" value="<? echo number_format($total_expected_profit_percent,2)."%"; ?>">
                   <input type="hidden" id="total_expt_profit_variance_percentage_id" value="<? echo number_format($total_expected_profit_variance_percent,2)."%"; ?>">
                   <input type="hidden" id="expected_profit_percent" value="<? echo '('.$company_asking.'%'.')'; ?>">
                   <? //Pre cost end here ?>

                  <input type="hidden" id="total_p_fab_cost" value="<? echo number_format($total_p_fab_cost_amount,2); ?>">
                  <input type="hidden" id="total_p_fab_percent" value="<? echo number_format($p_fab_percent,2)."%"; ?>">
                  <input type="hidden" id="total_p_trim_cost" value="<? echo number_format($total_p_trim_cost,2); ?>">
                  <input type="hidden" id="total_p_trim_percent" value="<? echo number_format($trim_p_percent,2)."%"; ?>">
                  <input type="hidden" id="total_p_embelishment_cost" value="<? echo number_format($total_p_embelishment_cost,2); ?>">
                  <input type="hidden" id="total_p_embelishment_percent" value="<? echo number_format($p_embelishment_percent,2)."%"; ?>">
                  <input type="hidden" id="total_p_commercial_cost" value="<? echo number_format($total_p_commercial_cost,2); ?>">
                  <input type="hidden" id="total_p_commercial_percent" value="<? echo number_format($total_p_commercial_percent,2)."%"; ?>">
                  <input type="hidden" id="total_p_commssion_cost" value="<? echo number_format($total_p_commssion,2); ?>">
                  <input type="hidden" id="total_p_commssion_percent" value="<? echo number_format($total_p_commssion_percent,2)."%"; ?>">
                  <input type="hidden" id="total_p_testing_cost" value="<? echo number_format($total_p_testing_cost,2); ?>">
                  <input type="hidden" id="total_p_testing_cost_percent" value="<? echo number_format($total_p_testing_cost_percent,2)."%"; ?>">
                  <input type="hidden" id="total_p_freight_cost" value="<? echo number_format($total_p_freight_cost,2); ?>">
                  <input type="hidden" id="total_p_freight_cost_percent" value="<? echo number_format($total_p_freight_cost_percent,2)."%"; ?>">
                  <input type="hidden" id="total_p_cost_up2" value="<? echo number_format($total_p_cost_up,2); ?>">
                  <input type="hidden" id="total_p_cost_percent" value="<? echo number_format($p_all_tot_cost_percentage,2)."%"; ?>">
                  <input type="hidden" id="total_p_cm_cost" value="<? echo number_format($total_p_cm_cost,2); ?>">
                  <input type="hidden" id="total_p_cm_percent" value="<? echo number_format($total_p_cm_percent,2)."%"; ?>">
                  <input type="hidden" id="total_p_order_amount" value="<? echo number_format($total_order_amount,2); ?>">
                  <input type="hidden" id="total_p_order_amount_percent" value="<? echo number_format($total_p_order_amount_percent,2)."%"; ?>">
                  <input type="hidden" id="total_p_inspection" value="<? echo number_format($total_p_inspection,2); ?>">
                  <input type="hidden" id="total_p_inspection_percent" value="<? echo number_format($total_p_inspection_percent,2)."%"; ?>">
                  <input type="hidden" id="total_p_certificate_cost" value="<? echo number_format($total_p_certificate_cost,2); ?>">
                  <input type="hidden" id="total_p_certificate_percent" value="<? echo number_format($total_p_certificate_percent,2)."%"; ?>">
                  <input type="hidden" id="total_p_common_oh" value="<? echo number_format($total_p_common_oh,2); ?>">
                  <input type="hidden" id="total_p_common_oh_percent" value="<? echo number_format($total_p_common_oh_percent,2)."%"; ?>">
                 
                  <input type="hidden" id="total_p_currier_cost" value="<? echo number_format($total_p_currier_cost,2); ?>">
                  <input type="hidden" id="total_p_currier_cost_percent" value="<? echo number_format($total_p_currier_cost_percent,2)."%"; ?>">
                  
                  <input type="hidden" id="total_p_fab_profit_id" value="<? echo number_format($total_p_fab_profit,2); ?>">
                  <input type="hidden" id="total_p_expected_profit_id" value="<? echo number_format($total_p_expected_profit,2); ?>">
                  <input type="hidden" id="total_p_expt_profit_variance_id" value="<? echo number_format($total_p_expect_variance,2); ?>">
                  
                   <input type="hidden" id="total_p_profit_fab_percentage_id" value="<? echo number_format($total_p_profit_fab_percentage,2)."%"; ?>">
                   <input type="hidden" id="total_p_expt_profit_percentage_id" value="<? echo number_format($total_p_expected_profit_percent,2)."%"; ?>">
                   <input type="hidden" id="total_p_expt_profit_variance_percentage_id" value="<? echo number_format($total_p_expected_profit_variance_percent,2)."%"; ?>">
                   <input type="hidden" id="p_expected_profit_percent" value="<? echo '('.$company_asking.'%'.')'; ?>">
                   <? 
				  // Price Quotation End
				   ?>
                  <input type="hidden" id="total_v_fab_cost" value="<? echo number_format($total_v_fab_cost_varaince,2); ?>">
                  <input type="hidden" id="total_v_fab_percent" value="<? echo number_format($v_fab_percent,2)."%"; ?>">
                  <input type="hidden" id="total_v_trim_cost" value="<? echo number_format($total_trim_variance,2); ?>">
                  <input type="hidden" id="total_v_trim_percent" value="<? echo number_format($v_trim_percent,2)."%"; ?>">
                  <input type="hidden" id="total_v_embelishment_cost" value="<? echo number_format($total_v_embellish_cost_variance,2); ?>">
                  <input type="hidden" id="total_v_embelishment_percent" value="<? echo number_format($v_embelishment_percent,2)."%"; ?>">
                  <input type="hidden" id="total_v_commercial_cost" value="<? echo number_format($total_commcercial_variance,2); ?>">
                  <input type="hidden" id="total_v_commercial_percent" value="<? echo number_format($total_v_commercial_percent,2)."%"; ?>">
                  <input type="hidden" id="total_v_commssion_cost" value="<? echo number_format($total_v_commission_variance,2); ?>">
                  <input type="hidden" id="total_v_commssion_percent" value="<? echo number_format($total_v_commssion_percent,2)."%"; ?>">
                  <input type="hidden" id="total_v_testing_cost" value="<? echo number_format($total_test_variance,2); ?>">
                  <input type="hidden" id="total_v_testing_cost_percent" value="<? echo number_format($total_v_testing_cost_percent,2)."%"; ?>">
                  <input type="hidden" id="total_v_freight_cost" value="<? echo number_format($total_freigt_variance,2); ?>">
                  <input type="hidden" id="total_v_freight_cost_percent" value="<? echo number_format($total_v_freight_cost_percent,2)."%"; ?>">
                  <input type="hidden" id="total_v_cost_up2" value="<? echo number_format($total_cost_variance,2); ?>">
                  <input type="hidden" id="total_v_cost_percent" value="<? echo number_format($v_all_tot_cost_percentage,2)."%"; ?>">
                  <input type="hidden" id="total_v_cm_cost" value="<? echo number_format($total_cm_cost_variance,2); ?>">
                  <input type="hidden" id="total_v_cm_percent" value="<? echo number_format($total_v_cm_percent,2)."%"; ?>">
                  <input type="hidden" id="total_v_order_amount" value="<? echo number_format($total_order_amount,2); ?>">
                  <input type="hidden" id="total_v_order_amount_percent" value="<? echo number_format($total_p_order_amount_percent,2)."%"; ?>">
                  <input type="hidden" id="total_v_inspection" value="<? echo number_format($total_inspection_variance,2); ?>">
                  <input type="hidden" id="total_v_inspection_percent" value="<? echo number_format($total_v_inspection_percent,2)."%"; ?>">
                  <input type="hidden" id="total_v_certificate_cost" value="<? echo number_format($total_certificate_variance,2); ?>">
                  <input type="hidden" id="total_v_certificate_percent" value="<? echo number_format($total_v_certificate_percent,2)."%"; ?>">
                  <input type="hidden" id="total_v_common_oh" value="<? echo number_format($total_common_oh_variance,2); ?>">
                  <input type="hidden" id="total_v_common_oh_percent" value="<? echo number_format($total_v_common_oh_percent,2)."%"; ?>">
                  <input type="hidden" id="total_v_currier_cost" value="<? echo number_format($total_currier_variance,2); ?>">
                  <input type="hidden" id="total_v_currier_cost_percent" value="<? echo number_format($total_v_currier_cost_percent,2)."%"; ?>">
                  <input type="hidden" id="total_v_fab_profit_id" value="<? echo number_format($total_profit_variance,2); ?>">
                  <input type="hidden" id="total_v_expected_profit_id" value="<? echo number_format($total_expected_profit_variance,2); ?>">
                  <input type="hidden" id="total_v_expt_profit_variance_id" value="<? echo number_format($total_expected_profit_vari_varianace,2); ?>">
                   <input type="hidden" id="total_v_profit_fab_percentage_id" value="<? echo number_format($tot_expected_profit_variance,2)."%"; ?>">
                   <input type="hidden" id="total_v_expt_profit_percentage_id" value="<? echo number_format($total_v_expected_profit_percent,2)."%"; ?>">
                   <input type="hidden" id="total_v_expt_profit_variance_percentage_id" value="<? echo number_format($total_v_expected_profit_variance_percent,2)."%"; ?>">
                   <input type="hidden" id="v_expected_profit_percent" value="<? echo '('.$company_asking.'%'.')'; ?>">
            </div>
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
					//echo $total_fabric_profit_up;
					$chart_data_qnty="Fabric Cost;".$total_fab_cost."\nTrimCost;".$total_trim_cost."\nEmbelishment Cost;".$total_embelishment_cost."\nCommercial Cost;".$total_commercial_cost."\nCommission Cost;".$total_commssion."\nTesting Cost;".$total_testing_cost."\nFreightCost;".$total_freight_cost."\nCM Cost;".$total_cm_cost."\nOrder Value;".$total_order_amount."\nInspection Cost;".$total_inspection."\nCertificate Cost;".$total_certificate_cost."\nCommn OH Cost;".$total_common_oh."\nCurrier Cost;".$total_currier_cost."\n Profit/Loss;".$total_fabric_profit_up."\n";
					 
					?>
                    <input type="hidden" id="graph_data" value="<? //echo substr($chart_data_qnty,0,-1); ?>"/>
                </tr>
            </table>
             <table>
                <tr><td height="15"></td></tr>
            </table>
           <a id="displayText" href="javascript:toggle();">Show Yarn Summary</a>
            <div style="width:600px; display:none" id="yarn_summary" >
            <div id="data_panel2" align="center" style="width:500px">
                 <input type="button" value="Print Preview" class="formbutton" style="width:100px" name="print" id="print" onClick="new_window(1)" />
            </div>
            <table width="500">
                    <tr class="form_caption">
                        <td colspan="6" align="center"><strong>Yarn Cost Summary As Per Pre-Cost </strong></td>
                    </tr>
            </table>
            <table class="rpt_table" width="500" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <th width="30">SL</th>
                    <th width="80">Yarn Count</th>
                    <th width="120">Type</th>
                    <th width="120">Req. Qnty</th>
                    <th width="80">Avg. rate</th>
                    <th>Amount</th>
                </thead>
                <?
                $s=1; $tot_yarn_req_qnty=0; $tot_yarn_req_amnt=0;
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
        	</div>
		</fieldset>
	</div>
<?
			}
	}
	else if($report_type==3)
	{
	
		if($template==1)
		{
			ob_start();
			$style1="#E9F3FF"; 
			$style="#FFFFFF";
	
			$fab_precost_arr=array();$commission_array=array();$knit_arr=array(); $fabriccostArray=array(); $fab_emb=array();$fabric_data_Array=array();$asking_profit_arr=array(); $yarncostArray=array(); $convirsioncostArray=array(); $yarn_desc_array=array();
			
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
			
			$fabriccostDataArray=sql_select("select job_no, costing_per_id, trims_cost, embel_cost, cm_cost, commission, common_oh, lab_test, inspection, freight, comm_cost,certificate_pre_cost,currier_pre_cost from wo_pre_cost_dtls where status_active=1 and is_deleted=0  ");
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
			$buyer_data=array(); $buyer_data_arr=array();
			$job_sql="select a.job_no, a.buyer_name, a.avg_unit_price, a.total_set_qnty as ratio";
			$k=1;
			foreach($month_array as $val)
			{
				$job_sql.=",sum(CASE WHEN $date_type like '$val-%%' THEN b.plan_cut END) AS plan_cut$k
				,sum(CASE WHEN $date_type like '$val-%%' THEN b.po_quantity END) AS po_quantity$k
				,sum(CASE WHEN $date_type like '$val-%%' THEN b.unit_price END) AS unit_price$k
				";
				$k++;
			}
			$job_sql.=", b.id as po_id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond $order_status_cond $season_cond group by a.job_no, a.buyer_name, a.avg_unit_price, a.total_set_qnty, b.id";
			//echo $job_sql;
			$result=sql_select($job_sql);  $qty=0; $amt=0; $plan_cut_qnty=0;
			foreach($result as $row)
			{
				$dzn_qnty=0;
				$costing_per_id=$fabriccostArray[$row[csf('job_no')]]['costing_per_id'];
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
					$total_order_value=$row[csf('po_quantity'.$k)]*$row[csf('avg_unit_price')];
					$commercial_cost=$fabriccostArray[$row[csf('job_no')]]['comm_cost'];
					
					$tot_commercial_cost=($commercial_cost/$dzn_qnty)*$plan_cut_qnty;
					$fabricData=explode(",",substr($fabric_data_Array[$row[csf('job_no')]],0,-1));
					$fab_precost_Data=explode(",",substr($fab_precost_arr[$row[csf('job_no')]][$row[csf('po_id')]],0,-1));

					foreach($fabricData as $fabricRow)
					{
						$fabricRow=explode("**",$fabricRow);
						$fab_nature_id=$fabricRow[0];	
						$fab_source_id=$fabricRow[1];
						$fab_rate=$fabricRow[2];
						$yarn_qty=$fabricRow[3];
						$yarn_amount=$fabricRow[4];
						$conv_qty=$fabricRow[5];
						$conv_amount=$fabricRow[6];
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
							$yarn_costing+=$yarn_amount/$dzn_qnty*$plan_cut_qnty;
							$conver_costing+=$conv_amount/$dzn_qnty*$plan_cut_qnty;		
						}
					}
					$trim_amount= $fabriccostArray[$row[csf('job_no')]]['trims_cost']/$dzn_qnty*$po_quantity;
					$wash_cost=$fab_emb[$row[csf('job_no')]]['wash']/$dzn_qnty*$plan_cut_qnty;
					$embl_amount=($fabriccostArray[$row[csf('job_no')]]['embel_cost']/$dzn_qnty*$po_quantity)+$wash_cost;


					$commercial_cost_arr=$fabriccostArray[$row[csf('job_no')]]['comm_cost'];
					$commercial_cost=($commercial_cost/$dzn_qnty)*$plan_cut_qnty;
					
					$foreign=$commission_array[$row[csf('job_no')]]['foreign']/$dzn_qnty*$po_quantity;
                    $local=$commission_array[$row[csf('job_no')]]['local']/$dzn_qnty*$po_quantity;
					$commission_cost=$foreign+$local;
					
					$testing_cost=$fabriccostArray[$row[csf('job_no')]]['lab_test']/$dzn_qnty*$po_quantity;
					$freight_cost= $fabriccostArray[$row[csf('job_no')]]['freight']/$dzn_qnty*$po_quantity;
					
					$inspection_cost=$fabriccostArray[$row[csf('job_no')]]['inspection']/$dzn_qnty*$po_quantity;
					$certificate_cost=$fabriccostArray[$row[csf('job_no')]]['certificate_pre_cost']/$dzn_qnty*$po_quantity;
					$common_oh=$fabriccostArray[$row[csf('job_no')]]['common_oh']/$dzn_qnty*$po_quantity;
					$currier_cost=$fabriccostArray[$row[csf('job_no')]]['currier_pre_cost']/$dzn_qnty*$po_quantity;
					
                    $cm_cost=$fabriccostArray[$row[csf('job_no')]]['c_cost']/$dzn_qnty*$po_quantity;
                    $cm_cost_dzn=$fabriccostArray[$row[csf('job_no')]]['c_cost'];
					
					
					
					$foreign=($commission_array[$row[csf('job_no')]]['foreign']/$dzn_qnty)*$po_quantity;
					$local=($commission_array[$row[csf('job_no')]]['local']/$dzn_qnty)*$po_quantity;
					$commission_cost=$foreign+$local;
					$net_fob_value=$total_order_value-$commission_cost;
					
					$contribution_value=$net_fob_value-$cost_of_material_service;
					$cm_cost=$fabriccostArray[$row[csf('job_no')]]['c_cost']/$dzn_qnty*$po_quantity;
					$gross_profit=$contribution_value-$cm_cost;
					
					$kniting_cost=$knit_arr[$row[csf('job_no')]]['knit']+$knit_arr[$row[csf('job_no')]]['weaving']+$knit_arr[$row[csf('job_no')]]['collar_cuff']+$knit_arr[$row[csf('job_no')]]['feeder_stripe'];
					$tot_knit_cost=($kniting_cost/$dzn_qnty)*$plan_cut_qnty;
					$fabric_dyeing_cost=($knit_arr[$row[csf('job_no')]]['fabric_dyeing']/$dzn_qnty)*$plan_cut_qnty;
					$yarn_dyed_cost=($knit_arr[$row[csf('job_no')]]['yarn_dyeing']/$dzn_qnty)*$plan_cut_qnty;
					$heat_setting_cost=($knit_arr[$row[csf('job_no')]]['heat']/$dzn_qnty)*$plan_cut_qnty;
					$fabric_finish=($knit_arr[$row[csf('job_no')]]['fabric_finish']/$dzn_qnty)*$plan_cut_qnty;
					$all_over_cost=($knit_arr[$row[csf('job_no')]]['all_over']/$dzn_qnty)*$plan_cut_qnty;
				
					$currier_cost=$fabriccostArray[$row[csf('job_no')]]['currier_pre_cost']/$dzn_qnty*$order_qty_pcs;
					$lab_test_cost=$fabriccostArray[$row[csf('job_no')]]['lab_test']/$dzn_qnty*$order_qty_pcs;
					$other_direct_expenses=$freight_cost+$inspection+$certificate_cost+$currier_cost+$lab_test_cost+$wash_cost;
					
					$tot_conversion_cost=$tot_knit_cost+$fabric_dyeing_cost+$yarn_dyed_cost+$heat_setting_cost+$fabric_finish+$wash_cost+$all_over_cost;
					$cost_of_material_service=$yarn_costing+$tot_conversion_cost+$tot_trim_cost+$tot_embell_cost+$other_direct_expenses;
					
					$operating_expense=$fabriccostArray[$row[csf('job_no')]]['common_oh']/$dzn_qnty*$po_quantity;
					$operating_profit=$gross_profit-($commercial_cost+$operating_expense);
					$depreciation_amortization=$fabriccostArray[$row[csf('job_no')]]['depr_amor_cost']/$dzn_qnty*$po_quantity;
					$interest_expense=$net_fob_value*$financial_para[csf('interest_expense')]/100;
					$income_tax=$net_fob_value*$financial_para[csf('income_tax')]/100;
					$net_profit=$operating_profit-($depreciation_amortization+$interest_expense+$income_tax);
					
					$buyer_data_arr[$row[csf('buyer_name')]][$val]['yarn_cost']+=$yarn_costing;
					$buyer_data_arr[$row[csf('buyer_name')]][$val]['conversion_cost']+=$conver_costing;
					$buyer_data_arr[$row[csf('buyer_name')]][$val]['fab_purchase']+=$fab_purchase;
					$buyer_data_arr[$row[csf('buyer_name')]][$val]['trim_cost']+=$trim_amount;
					$buyer_data_arr[$row[csf('buyer_name')]][$val]['embl_amount']+=$embl_amount;
					$buyer_data_arr[$row[csf('buyer_name')]][$val]['commercial_cost']+=$commercial_cost;
					$buyer_data_arr[$row[csf('buyer_name')]][$val]['commission_cost']+=$commission_cost;
					$buyer_data_arr[$row[csf('buyer_name')]][$val]['testing_cost']+=$testing_cost;
					$buyer_data_arr[$row[csf('buyer_name')]][$val]['freight_cost']+=$freight_cost;
					$buyer_data_arr[$row[csf('buyer_name')]][$val]['inspection_cost']+=$inspection_cost;
					$buyer_data_arr[$row[csf('buyer_name')]][$val]['certificate_cost']+=$certificate_cost;
					$buyer_data_arr[$row[csf('buyer_name')]][$val]['common_oh']+=$common_oh;
					$buyer_data_arr[$row[csf('buyer_name')]][$val]['currier_cost']+=$currier_cost;
					$buyer_data_arr[$row[csf('buyer_name')]][$val]['cm_cost']+=$cm_cost;
					$buyer_data_arr[$row[csf('buyer_name')]][$val]['order_value']+=$total_order_value;
					$buyer_data_arr[$row[csf('buyer_name')]][$val]['profit_loss']+=$net_profit;
					$k++;				
				}
				$buyer_data[$row[csf('buyer_name')]]=$row[csf('buyer_name')];
			}
			//print_r($buyer_data_arr);
			
			
			//die;
			$table_width=200+(1570*($total_months+1));
			$col_span=2+(17*$total_months);
		?>
	<br/>   
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
                        //$month_name=date("F",strtotime($month_id)).", ".date("Y",strtotime($month_id));
                        ?>
                        <th colspan="3">Fabric Cost</th>
                        <th width="90" rowspan="2">Trim Cost</th>
                        <th width="90" rowspan="2">Embellishment Cost</th>
                        <th width="90" rowspan="2">Commercial Cost</th>
                        <th width="90" rowspan="2">Commision Cost</th>
                        <th width="90" rowspan="2">Testing Cost</th>
                        <th width="90" rowspan="2">Freight Cost</th>
                        <th width="90" rowspan="2">Inspection Cost</th>
                        <th width="90" rowspan="2">Certificate Cost</th>
                        <th width="90" rowspan="2">Commn OH</th>
                        <th width="90" rowspan="2">Courier OH</th>
                        <th width="90" rowspan="2">CM Cost</th>
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
                        //$month_name=date("F",strtotime($month_id)).", ".date("Y",strtotime($month_id));
                        ?>
                        <th width="90">Yarn Cost</th>
                        <th width="90">Convirsion Cost</th>
                        <th width="90">Fab. Purch. Cost</th>
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
						$tot_cost=$row_yarn_costing+$row_conver_costing+$row_fab_purchase+$row_trim_amount+$row_embl_amount+$row_commercial_cost+$row_testing_cost+$row_freight_cost+$row_inspection_cost+$row_certificate_cost+$row_common_oh+$row_currier_cost+$row_cm_cost;	
						$profit_loss=$buyer_data_arr[$buyer_id][$val]['profit_loss'];		
                        ?>
                        <td width="90" align="right"><p><? echo number_format($row_yarn_costing,2); ?></p></td>
                        <td width="90" align="right"><p><? echo number_format($row_conver_costing,2); ?></p></td>
                        <td width="90" align="right"><p><? echo number_format($row_fab_purchase,2); ?></p></td>
                        <td width="90" align="right"><p><? echo number_format($row_trim_amount,2); ?></p></td>
                        <td width="90" align="right"><p><? echo number_format($row_embl_amount,2); ?></p></td>
                        <td width="90" align="right"><p><? echo number_format($row_commercial_cost,2); ?></p></td>
                        <td width="90" align="right"><p><? echo number_format($row_commission_cost,2); ?></p></td>
                        
                        <td width="90" align="right"><p><? echo number_format($row_testing_cost,2); ?></p></td>
                        <td width="90" align="right"><p><? echo number_format($row_freight_cost,2); ?></p></td>
                        <td width="90" align="right"><p><? echo number_format($row_inspection_cost,2); ?></p></td>
                        <td width="90" align="right"><p><? echo number_format($row_certificate_cost,2); ?></p></td>
                        <td width="90" align="right"><p><? echo number_format($row_common_oh,2); ?></p></td>
                        <td width="90" align="right"><p><? echo number_format($row_currier_cost,2); ?></p></td>
                        <td width="90" align="right"><p><? echo number_format($row_cm_cost,2); ?></p></td>
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
        <table class="rpt_table" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all">
            <tr bgcolor="#CCCCCC" style="font-weight:bold">
            	<td align="right" width="30">&nbsp;</td>
                <td align="right" width="150">Total :</td>
                <?
				foreach($month_array as $val)
				{
				?>
                    <td align="right" width="90"><? echo number_format($total_yarn_costing[$val],2); ?></td>
                    <td align="right" width="90"><? echo number_format($total_conver_costing[$val],2); ?></td>
                    <td align="right" width="90"><? echo number_format($total_fab_purchase[$val],2); ?></td>
                    <td align="right" width="90"><? echo number_format($total_trim_amount[$val],2); ?></td>
                    <td align="right" width="90"><? echo number_format($total_embl_amount[$val],2); ?></td>
                    <td align="right" width="90"><? echo number_format($total_commercial_cost[$val],2); ?></td>
                    <td align="right" width="90"><? echo number_format($total_commission_cost[$val],2); ?></td>
                    <td align="right" width="90"><? echo number_format($total_testing_cost[$val],2); ?></td>
                    <td align="right" width="90"><? echo number_format($total_freight_cost[$val],2); ?></td>
                    <td align="right" width="90"><? echo number_format($total_inspection_cost[$val],2); ?></td>
                    <td align="right" width="90"><? echo number_format($total_certificate_cost[$val],2); ?></td>
                    <td align="right" width="90"><? echo number_format($total_common_oh[$val],2); ?></td>
                    <td align="right" width="90"><? echo number_format($total_currier_cost[$val],2); ?></td>
                    <td align="right" width="90"><? echo number_format($total_cm_cost[$val],2); ?></td>
                    <td align="right" width="100"><? echo number_format($total_cost[$val],2); ?></td>
                    <td align="right" width="100"><? echo number_format($total_ord_value[$val],2); ?></td>  
                    <td align="right"><? echo number_format($total_profit_losse[$val],2); ?></td>
                <?
				}
				?>
            </tr>
		</table>
	</div>
        </fieldset>
        </div>
        <?
        }
		
	}
	else if($report_type==4)
	{
		if($template==1)
		{
			ob_start();
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
			
			$fabriccostDataArray=sql_select("select job_no, costing_per_id, trims_cost, embel_cost, cm_cost, commission, common_oh, lab_test, inspection, freight, comm_cost,certificate_pre_cost,currier_pre_cost from wo_pre_cost_dtls where status_active=1 and is_deleted=0  ");
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
			
			$act_ahip_arr=sql_select("select po_break_down_id, sum(ex_factory_qnty) as shipout_qty from  pro_ex_factory_mst where status_active=1 and is_deleted=0 group by po_break_down_id");// quotation_id='$data'
			foreach($act_ahip_arr as $row)
			{
				$actual_shipout_arr[$row[csf('po_break_down_id')]]['shipout_qty']=$row[csf('shipout_qty')];
			}
			
		?>
	<br/>   
	<fieldset>	
        <table width="2700">
            <tr class="form_caption">
                <td colspan="27" align="center"><strong>Order Wise Budget On Shipout Report Mkt</strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="27" align="center"><strong><? echo $company_library[$company_name]; ?></strong></td>
            </tr>
        </table>
        <table class="rpt_table" width="2700" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr>
                    <th width="35">SL</th>
                    <th width="120">Buyer</th>
                    <th width="100">Job</th>
                    <th width="110">Style Ref.</th>
                    <th width="100">Order No</th>
                    <th width="100">Ready To Approve</th>
                    <th width="100">Order Qty(Pcs)</th>
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
        <div style="width:2720px; max-height:250px; overflow-y:scroll" id="scroll_body">
        <table class="rpt_table" width="2700" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
        <? 
        	$i=1; 
       		$sql="select a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, a.avg_unit_price, a.total_set_qnty as ratio, b.id as po_id, b.po_number, b.po_quantity, b.plan_cut, b.unit_price, b.shiping_status from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.shiping_status=3 and b.is_deleted=0 $date_cond $buyer_id_cond  $year_cond $job_no_cond $order_id_cond_trans $order_no_cond $order_status_cond $season_cond order by b.id ";
        	//echo $sql;die;
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
				
				$order_value=$row[csf('po_quantity')]*$row[csf('avg_unit_price')];
				$gross_fob_value=$row[csf('po_quantity')]*$row[csf('avg_unit_price')];
				$actual_shipout_qty=$actual_shipout_arr[$row[csf('po_id')]]['shipout_qty'];
				$actual_shipout_val=$actual_shipout_qty*$row[csf('avg_unit_price')];
				
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
						$actual_yarn_costing=$yarn_amount/$dzn_qnty*$plan_cut_qnty;			
					}
				 
				}
				//$yarn_cost_percent=($yarn_costing/$total_gross_fob_plancut_value)*100;
				$tot_trim_cost=($fabriccostArray[$row[csf('job_no')]]['trims_cost']/$dzn_qnty)*$order_qty_pcs;
				$print_cost=($fab_emb[$row[csf('job_no')]]['print']/$dzn_qnty)*$plan_cut_qnty;
				$embroidery_cost=($fab_emb[$row[csf('job_no')]]['embroidery']/$dzn_qnty)*$plan_cut_qnty;
				$special_cost=($fab_emb[$row[csf('job_no')]]['special']/$dzn_qnty)*$plan_cut_qnty;
				$tot_embell_cost=$print_cost+$embroidery_cost+$special_cost;
				
				$freight_cost= $fabriccostArray[$row[csf('job_no')]]['freight']/$dzn_qnty*$order_qty_pcs;
				$inspection=$fabriccostArray[$row[csf('job_no')]]['inspection']/$dzn_qnty*$order_qty_pcs;
				$certificate_cost=$fabriccostArray[$row[csf('job_no')]]['certificate_pre_cost']/$dzn_qnty*$order_qty_pcs;
				
				$wash_cost=($fab_emb[$row[csf('job_no')]]['wash']/$dzn_qnty)*$plan_cut_qnty;
				$currier_cost=$fabriccostArray[$row[csf('job_no')]]['currier_pre_cost']/$dzn_qnty*$order_qty_pcs;
				$lab_test_cost=$fabriccostArray[$row[csf('job_no')]]['lab_test']/$dzn_qnty*$order_qty_pcs;
				$other_direct_expenses=$freight_cost+$inspection+$certificate_cost+$currier_cost+$lab_test_cost+$wash_cost;
				
				$tot_conversion_cost=$tot_knit_cost+$fabric_dyeing_cost+$yarn_dyed_cost+$heat_setting_cost+$fabric_finish+$washing_cost+$all_over_cost;
				$cost_of_material_service=$yarn_costing+$tot_conversion_cost+$tot_trim_cost+$tot_embell_cost+$other_direct_expenses;
				
				$contribution_value=$net_fob_value-$cost_of_material_service;
				$cm_cost=$fabriccostArray[$row[csf('job_no')]]['c_cost']/$dzn_qnty*$order_qty_pcs;
				$gross_profit=$contribution_value-$cm_cost;
				$commercial_cost=$fabriccostArray[$row[csf('job_no')]]['comm_cost'];
				$tot_commercial_cost=($commercial_cost/$dzn_qnty)*$plan_cut_qnty;
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
				
				$actual_knit_cost=($kniting_cost/$dzn_qnty)*$actual_shipout_qty;
				$actual_fabric_dyeing_cost=($knit_arr[$row[csf('job_no')]]['fabric_dyeing']/$dzn_qnty)*$actual_shipout_qty;
				$actual_yarn_dyed_cost=($knit_arr[$row[csf('job_no')]]['yarn_dyeing']/$dzn_qnty)*$actual_shipout_qty;
				$actual_heat_setting_cost=($knit_arr[$row[csf('job_no')]]['heat']/$dzn_qnty)*$actual_shipout_qty;
				$actual_fabric_finish=($knit_arr[$row[csf('job_no')]]['fabric_finish']/$dzn_qnty)*$actual_shipout_qty;
				$actual_washing_cost=($knit_arr[$row[csf('job_no')]]['washing']/$dzn_qnty)*$actual_shipout_qty;
				$actual_all_over_cost=($knit_arr[$row[csf('job_no')]]['all_over']/$dzn_qnty)*$actual_shipout_qty;
				
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
				
				$actual_conversion_cost=$actual_knit_cost+$actual_fabric_dyeing_cost+$actual_yarn_dyed_cost+$actual_heat_setting_cost+$actual_fabric_finish+$actual_washing_cost+$actual_all_over_cost;
				$actual_cost_of_material_service=$actual_yarn_costing+$actual_conversion_cost+$actual_trim_cost+$actual_embell_cost+$actual_other_direct_expenses;
				
				$actual_contribution_value=$actual_net_fob_value-$actual_cost_of_material_service;
				$actual_cm_cost=$fabriccostArray[$row[csf('job_no')]]['c_cost']/$dzn_qnty*$actual_shipout_qty;
				$actual_gross_profit=$actual_contribution_value-$actual_cm_cost;
				$actual_commercial_cost=$fabriccostArray[$row[csf('job_no')]]['comm_cost'];
				$actual_tot_commercial_cost=($commercial_cost/$dzn_qnty)*$plan_cut_qnty;
				$actual_operating_expense=$fabriccostArray[$row[csf('job_no')]]['common_oh']/$dzn_qnty*$actual_shipout_qty;
				$actual_operating_profit=$actual_gross_profit-($actual_commercial_cost+$actual_operating_expense);
				$actual_depreciation_amortization=$fabriccostArray[$row[csf('job_no')]]['depr_amor_cost']/$dzn_qnty*$actual_shipout_qty;
				$actual_interest_expense=$actual_net_fob_value*$financial_para[csf('interest_expense')]/100;
				$actual_income_tax=$actual_net_fob_value*$financial_para[csf('income_tax')]/100;
				$actual_net_profit=$actual_operating_profit-($actual_depreciation_amortization+$actual_interest_expense+$actual_shipout_qty);
				
				
				
				?>
                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                    <td width="35" rowspan="4"><? echo $i; ?></td>
                    <td width="120" rowspan="4"><p><? echo $buyer_library[$row[csf('buyer_name')]] ?></p></td>
                    <td width="100" rowspan="4"><p><? echo $row[csf('job_no')]; ?></p></td>
                    <td width="110" rowspan="4"><p><? echo  $row[csf('style_ref_no')]; ?></p></td>
                    <td width="100" rowspan="4"><p><? echo $row[csf('po_number')]; ?></p></td>
                    
                    <td width="100" rowspan="4" align="right"><p><? echo number_format($row[csf('po_quantity')],2); ?></p></td>
                    <td width="100" rowspan="4" align="right"><p><? echo number_format($actual_shipout_qty,2); ?></p></td>
                    <td width="110"><p><strong>Budget Value</strong></p></td>
                    <td width="100" align="right"><p><? echo number_format($order_value,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($commission_cost,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($net_fob_value,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($cost_of_material_service,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($yarn_costing,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($tot_conversion_cost,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($tot_trim_cost,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($tot_embell_cost,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($other_direct_expenses,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($contribution_value,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($cm_cost,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($gross_profit,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($tot_commercial_cost,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($operating_expense,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($operating_profit,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($depreciation_amortization,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($interest_expense,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($income_tax,2); ?></p></td>
                    <td align="right"><p><? echo number_format($net_profit,2); ?></p></td>
                </tr>
                <tr>
                	<td width="90"><p><strong>Budget Achivement</strong></p></td>
                    <td width="100" align="right"><p><? echo number_format($actual_shipout_val,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($actual_commission_cost,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($actual_net_fob_value,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($actual_cost_of_material_service,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($actual_yarn_costing,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($actual_conversion_cost,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($actual_trim_cost,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($actual_embell_cost,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($actual_direct_expenses,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($actual_contribution_value,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($actual_cm_cost,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($actual_gross_profit,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($actual_tot_commercial_cost,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($actual_operating_expense,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($actual_operating_profit,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($actual_depreciation_amortization,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($actual_interest_expense,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($actual_income_tax,2); ?></p></td>
                    <td align="right"><p><? echo number_format($actual_net_profit,2); ?></p></td>
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
					$pro_other_direct_expenses=$other_direct_expenses-$actual_direct_expenses;
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
                    <td width="100" align="right"><p><? echo number_format($pro_order_value,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($pro_commission_cost,2); ?></p></td>
                    <td width="100" align="right"><p><? echo number_format($pro_net_fob_value,2); ?></p></td>
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
                //echo $total_fab_cost_amount;
                $i++;
            }
            ?>
		</table>
	</div>
        </fieldset>
        </div>
        <?
        }
	
	}

echo "$total_data****$filename";
	exit();
}
if($action=="precost_yarn_detail")
{
	echo load_html_head_contents("Yarn Cost Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$order_qty=return_field_value("sum(b.plan_cut*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id='$po_id'","po_quantity");
	$ratio_qty=return_field_value("sum(a.total_set_qnty) as ratio", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id='$po_id'","ratio");
	//print($order_qty);die;
    $costing_per=return_field_value("costing_per as costing_per", "wo_pre_cost_mst", "job_no='$job_no'","costing_per");
						if($costing_per==1)
						{
							$costing_per_dzn="1 Dzn";
						}
						else if($costing_per==2)
						{
							$costing_per_dzn="1 Pcs";
						}
						else if($costing_per==3)
						{
							$costing_per_dzn="2 Dzn";
						}
						else if($costing_per==4)
						{
							$costing_per_dzn="3 Dzn";
						}
						else if($costing_per==5)
						{
							$costing_per_dzn="4 Dzn";
						}
						
	$fabriccostArray=sql_select("select costing_per_id, trims_cost, embel_cost, cm_cost, commission, common_oh, lab_test, inspection, freight, comm_cost from wo_pre_cost_dtls where job_no='".$job_no."' and status_active=1 and is_deleted=0");
                        
                        $dzn_qnty=0;
                        if($fabriccostArray[0][csf('costing_per_id')]==1)
                        {
                            $dzn_qnty=12;
                        }
                        else if($fabriccostArray[0][csf('costing_per_id')]==3)
                        {
                            $dzn_qnty=12*2;
                        }
                        else if($fabriccostArray[0][csf('costing_per_id')]==4)
                        {
                            $dzn_qnty=12*3;
                        }
                        else if($fabriccostArray[0][csf('costing_per_id')]==5)
                        {
                            $dzn_qnty=12*4;
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
					$fabricArray=("select id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id,  cons_qnty, rate, amount,status_active from wo_pre_cost_fab_yarn_cost_dtls where job_no='$job_no'");
						$sql_result=sql_select($fabricArray);
					
					foreach($sql_result as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
							
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
    <?
	exit();
}// pre cost Yarn AVG end
if($action=="pricost_yarnavg_detail")
{
	echo load_html_head_contents("Yarn Cost Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$order_qty=return_field_value("sum(b.plan_cut*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id='$po_id'","po_quantity");
	$ratio_qty=return_field_value("sum(a.total_set_qnty) as ratio", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id='$po_id'","ratio");
	//print($order_qty);die;
   // $costing_per=return_field_value("costing_per as costing_per", "wo_pre_cost_mst", "job_no='$job_no'","costing_per");
	
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
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
							
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
	$order_qty=return_field_value("sum(b.plan_cut*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","po_quantity");
	$ratio_qty=return_field_value("sum(a.total_set_qnty) as ratio", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id='$po_id'","ratio");
	//print($order_qty);die;
    $costing_per=return_field_value("costing_per as costing_per", "wo_pre_cost_mst", "job_no='$job_no'","costing_per");
						if($costing_per==1)
						{
							$costing_per_dzn="1 Dzn";
						}
						else if($costing_per==2)
						{
							$costing_per_dzn="1 Pcs";
						}
						else if($costing_per==3)
						{
							$costing_per_dzn="2 Dzn";
						}
						else if($costing_per==4)
						{
							$costing_per_dzn="3 Dzn";
						}
						else if($costing_per==5)
						{
							$costing_per_dzn="4 Dzn";
						}
						
	$fabriccostArray=sql_select("select costing_per_id, trims_cost, embel_cost, cm_cost, commission, common_oh, lab_test, inspection, freight, comm_cost from wo_pre_cost_dtls where job_no='".$job_no."' and status_active=1 and is_deleted=0");
                        
                        $dzn_qnty=0;
                        if($fabriccostArray[0][csf('costing_per_id')]==1)
                        {
                            $dzn_qnty=12;
                        }
                        else if($fabriccostArray[0][csf('costing_per_id')]==3)
                        {
                            $dzn_qnty=12*2;
                        }
                        else if($fabriccostArray[0][csf('costing_per_id')]==4)
                        {
                            $dzn_qnty=12*3;
                        }
                        else if($fabriccostArray[0][csf('costing_per_id')]==5)
                        {
                            $dzn_qnty=12*4;
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
					$data_array=("select  a.body_part_id, a.fab_nature_id,a.fabric_source, a.fabric_description,  a.fabric_source, a.rate, a.amount,a.avg_finish_cons,b.cons,avg(b.requirment) as avg_cons   from wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b where b.pre_cost_fabric_cost_dtls_id=a.id and a.job_no='$job_no' and a.job_no=b.job_no and b.po_break_down_id='$po_id'  and a.fabric_source=2 and a.status_active=1 and  a.is_deleted=0 group by a.id, a.job_no, a.item_number_id, a.body_part_id, a.fab_nature_id,a.consumption_basis,a.fabric_source, a.fabric_description, a.fabric_source, a.rate, a.amount,a.avg_finish_cons,b.cons");
					$sql_result=sql_select($data_array);
					foreach($sql_result as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
							 $tot_avg=number_format($row[csf('avg_cons')],4);
							 //echo $tot_avg;
							$req_qty_avg=($tot_avg/$dzn_qnty)*$order_qty;
							//echo $req_qty_avg;
							$req_qty_p=$req_qty_avg/$dzn_qnty*$order_qty;
							$total_amount=$req_qty_avg*$row[csf('rate')];
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="70" align="center"><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
                            <td width="80" align="center"><p><? echo $item_category[$row[csf('fab_nature_id')]]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('fabric_description')]; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($order_qty,2); ?></p></td>
                            <td width="50" align="center"><p><?  
							if($row[csf('fabric_source')]==2) 
							{ 
							echo "Purchase";
							}
							else
							{
								echo "";
							}
							?>
                            </p></td>
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
} //Pre Cost Purchase End
if($action=="fab_price_purchase_detail")
{
	echo load_html_head_contents("Purchase Cost Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $job_no;
	$order_qty=return_field_value("sum(b.plan_cut*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","po_quantity");
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
                        } $dzn_qnty=$dzn_qnty*$ratio_qty;
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
					
					$data_array=("select  a.body_part_id, a.fab_nature_id,a.fabric_source, a.fabric_description,  a.fabric_source, a.rate, a.amount,a.avg_finish_cons,b.cons,avg(b.requirment) as avg_cons   from wo_pri_quo_fabric_cost_dtls a,wo_pri_quo_fab_co_avg_con_dtls b where b.wo_pri_quo_fab_co_dtls_id=a.id and a.quotation_id='$quotation_id' and a.quotation_id=b.quotation_id  and a.fabric_source=2 and a.status_active=1 and  a.is_deleted=0 group by a.id,a.quotation_id, a.item_number_id, a.body_part_id, a.fab_nature_id,a.fabric_source, a.fabric_description, a.fabric_source, a.rate, a.amount,a.avg_finish_cons,b.cons");
					$sql_result=sql_select($data_array);
					foreach($sql_result as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
							 $tot_avg=number_format($row[csf('avg_cons')],4);
							 //echo $tot_avg;
							$req_qty_avg=($tot_avg/$dzn_qnty)*$order_qty;
							//echo $req_qty_avg;
							$req_qty_p=$req_qty_avg/$dzn_qnty*$order_qty;
							$total_amount=$req_qty_avg*$row[csf('rate')];
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="70" align="center"><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
                            <td width="80" align="center"><p><? echo $item_category[$row[csf('fab_nature_id')]]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('fabric_description')]; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($order_qty,2); ?></p></td>
                            <td width="50" align="center"><p><?  
							if($row[csf('fabric_source')]==2) 
							{ 
							echo "Purchase";
							}
							else
							{
								echo "";
							}
							?>
                            </p></td>
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
	$order_qty=return_field_value("sum(b.plan_cut*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","po_quantity");
	//print($order_qty);die;
	$ratio_qty=return_field_value("sum(a.total_set_qnty) as ratio", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","ratio");
    $costing_per=return_field_value("costing_per as costing_per", "wo_pre_cost_mst", "job_no='$job_no'","costing_per");
						if($costing_per==1)
						{
							$costing_per_dzn="1 Dzn";
						}
						else if($costing_per==2)
						{
							$costing_per_dzn="1 Pcs";
						}
						else if($costing_per==3)
						{
							$costing_per_dzn="2 Dzn";
						}
						else if($costing_per==4)
						{
							$costing_per_dzn="3 Dzn";
						}
						else if($costing_per==5)
						{
							$costing_per_dzn="4 Dzn";
						}
						
	$fabriccostArray=sql_select("select costing_per_id, trims_cost, embel_cost, cm_cost, commission, common_oh, lab_test, inspection, freight, comm_cost from wo_pre_cost_dtls where job_no='".$job_no."' and status_active=1 and is_deleted=0");
                        
                        $dzn_qnty=0;
                        if($fabriccostArray[0][csf('costing_per_id')]==1)
                        {
                            $dzn_qnty=12;
                        }
                        else if($fabriccostArray[0][csf('costing_per_id')]==3)
                        {
                            $dzn_qnty=12*2;
                        }
                        else if($fabriccostArray[0][csf('costing_per_id')]==4)
                        {
                            $dzn_qnty=12*3;
                        }
                        else if($fabriccostArray[0][csf('costing_per_id')]==5)
                        {
                            $dzn_qnty=12*4;
                        }
                        else
                        {
                            $dzn_qnty=1;
                        }
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
			  $data_array=("select cons_process,req_qnty as req_qnty,charge_unit as charge_unit,amount as amount from wo_pre_cost_fab_conv_cost_dtls where job_no='$job_no' and cons_process in(1,2,3,4) and status_active=1 and is_deleted=0 group by id,cons_process,req_qnty,charge_unit,amount");
			  $sql_result=sql_select($data_array);
					foreach($sql_result as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
							$cons_qty=($row[csf('req_qnty')]/$dzn_qnty)*$row[csf('charge_unit')];
							$tot_cons_amount=$cons_qty*$order_qty;
							
							$tot_amount=$row[csf('amount')];
							$total_amount=($tot_amount/$dzn_qnty)*$order_qty;
							$tot_cons_amount=$cons_qty*$order_qty;
							
							
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="70" align="center"><p><? echo $conversion_cost_head_array[$row[csf('cons_process')]]; ?></p></td>
                            <td width="80" align="center"><p><? echo number_format($order_qty,2); ?></p></td>
                            <td width="100" align="center"><p><? echo number_format($cons_qty,4); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row[csf('req_qnty')],2); ?></p></td>
                            <td width="50" align="right"><p><? echo number_format($row[csf('charge_unit')],2); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($total_amount,2); ?></p></td>
                        </tr>
						<?
						$tot_req_qty+=$row[csf('req_qnty')];
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
	$order_qty=return_field_value("sum(b.plan_cut*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","po_quantity");
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
                        } $dzn_qnty=$dzn_qnty*$ratio_qty;
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
			  $data_array=("select cons_type,req_qnty as req_qnty,charge_unit as charge_unit,amount as amount from wo_pri_quo_fab_conv_cost_dtls where quotation_id='$quotation_id' and cons_type in(1,2,3,4) and status_active=1 and is_deleted=0 group by id,cons_type,req_qnty,charge_unit,amount");
			  $sql_result=sql_select($data_array);
					foreach($sql_result as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
							$cons_qty=($row[csf('req_qnty')]/$dzn_qnty)*$row[csf('charge_unit')];
							$tot_cons_amount=$cons_qty*$order_qty;
							
							$tot_amount=$row[csf('amount')];
							$total_amount=($tot_amount/$dzn_qnty)*$order_qty;
							$tot_cons_amount=$cons_qty*$order_qty;
							
							
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="70" align="center"><p><? echo $conversion_cost_head_array[$row[csf('cons_process')]]; ?></p></td>
                            <td width="80" align="center"><p><? echo number_format($order_qty,2); ?></p></td>
                            <td width="100" align="center"><p><? echo number_format($cons_qty,4); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row[csf('req_qnty')],2); ?></p></td>
                            <td width="50" align="right"><p><? echo number_format($row[csf('charge_unit')],2); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($total_amount,2); ?></p></td>
                        </tr>
						<?
						$tot_req_qty+=$row[csf('req_qnty')];
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
	$order_qty=return_field_value("sum(b.plan_cut*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","po_quantity");
	$ratio_qty=return_field_value("sum(a.total_set_qnty) as ratio", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","ratio");
	//print($order_qty);die;
    $costing_per=return_field_value("costing_per as costing_per", "wo_pre_cost_mst", "job_no='$job_no'","costing_per");
						if($costing_per==1)
						{
							$costing_per_dzn="1 Dzn";
						}
						else if($costing_per==2)
						{
							$costing_per_dzn="1 Pcs";
						}
						else if($costing_per==3)
						{
							$costing_per_dzn="2 Dzn";
						}
						else if($costing_per==4)
						{
							$costing_per_dzn="3 Dzn";
						}
						else if($costing_per==5)
						{
							$costing_per_dzn="4 Dzn";
						}
						
	$fabriccostArray=sql_select("select costing_per_id, trims_cost, embel_cost, cm_cost, commission, common_oh, lab_test, inspection, freight, comm_cost from wo_pre_cost_dtls where job_no='".$job_no."' and status_active=1 and is_deleted=0");
                        
                        $dzn_qnty=0;
                        if($fabriccostArray[0][csf('costing_per_id')]==1)
                        {
                            $dzn_qnty=12;
                        }
                        else if($fabriccostArray[0][csf('costing_per_id')]==3)
                        {
                            $dzn_qnty=12*2;
                        }
                        else if($fabriccostArray[0][csf('costing_per_id')]==4)
                        {
                            $dzn_qnty=12*3;
                        }
                        else if($fabriccostArray[0][csf('costing_per_id')]==5)
                        {
                            $dzn_qnty=12*4;
                        }
                        else
                        {
                            $dzn_qnty=1;
                        }$dzn_qnty= $dzn_qnty*$ratio_qty;
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
					
			 $i=1;
			  $data_array=("select cons_process,req_qnty as req_qnty,charge_unit as charge_unit,amount as amount from wo_pre_cost_fab_conv_cost_dtls where job_no='$job_no' and cons_process in(25,31,32,60,61,62,63,72,80,81,84,85,86,87,38,74,78,79) and status_active=1 and is_deleted=0 group by id,cons_process,req_qnty,charge_unit,amount
");
			$sql_result=sql_select($data_array);
					
					foreach($sql_result as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
							
							$cons_qty=($row[csf('req_qnty')]/$dzn_qnty)*$row[csf('charge_unit')];
							$tot_cons_amount=$cons_qty*$order_qty;
							
							$tot_amount=$row[csf('amount')];
							$total_amount=($tot_amount/$dzn_qnty)*$order_qty;
							//$tot_cons_amount=$cons_qty*$order_qty;
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="70" align="center"><p><? echo $conversion_cost_head_array[$row[csf('cons_process')]]; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($order_qty,2); ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($cons_qty,4); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row[csf('req_qnty')],2); ?></p></td>
                            <td width="50" align="right"><p><? echo number_format($row[csf('charge_unit')],2); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($total_amount,2); ?></p></td>
                        </tr>
						<?
						$tot_req_qty+=$row[csf('req_qnty')];
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
}//Pre cost Fab dyeing cost details End

if($action=="fab_price_dyeing_detail")
{
	echo load_html_head_contents("Fabrics Dyeing Cost Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $job_no;die;
	$order_qty=return_field_value("sum(b.plan_cut*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","po_quantity");
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
                        } $dzn_qnty= $dzn_qnty*$ratio_qty;
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
			  $data_array=("select cons_type,req_qnty as req_qnty,charge_unit as charge_unit,amount as amount from  wo_pri_quo_fab_conv_cost_dtls where quotation_id='$quotation_id' and cons_type in(25,31,32,60,61,62,63,72,80,81,84,85,86,87,38,74,78,79) and status_active=1 and is_deleted=0 group by id,cons_type,req_qnty,charge_unit,amount
");
			$sql_result=sql_select($data_array);
					
					foreach($sql_result as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
							
							$cons_qty=($row[csf('req_qnty')]/$dzn_qnty)*$row[csf('charge_unit')];
							$tot_cons_amount=$cons_qty*$order_qty;
							
							$tot_amount=$row[csf('amount')];
							$total_amount=($tot_amount/$dzn_qnty)*$order_qty;
							//$tot_cons_amount=$cons_qty*$order_qty;
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="70" align="center"><p><? echo $conversion_cost_head_array[$row[csf('cons_process')]]; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($order_qty,2); ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($cons_qty,4); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row[csf('req_qnty')],2); ?></p></td>
                            <td width="50" align="right"><p><? echo number_format($row[csf('charge_unit')],2); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($total_amount,2); ?></p></td>
                        </tr>
						<?
						$tot_req_qty+=$row[csf('req_qnty')];
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
	$order_qty=return_field_value("sum(b.plan_cut*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","po_quantity");
	$ratio_qty=return_field_value("sum(a.total_set_qnty) as ratio", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","ratio");
	//print($order_qty);die;
    $costing_per=return_field_value("costing_per as costing_per", "wo_pre_cost_mst", "job_no='$job_no'","costing_per");
						if($costing_per==1)
						{
							$costing_per_dzn="1 Dzn";
						}
						else if($costing_per==2)
						{
							$costing_per_dzn="1 Pcs";
						}
						else if($costing_per==3)
						{
							$costing_per_dzn="2 Dzn";
						}
						else if($costing_per==4)
						{
							$costing_per_dzn="3 Dzn";
						}
						else if($costing_per==5)
						{
							$costing_per_dzn="4 Dzn";
						}
						
	$fabriccostArray=sql_select("select costing_per_id, trims_cost, embel_cost, cm_cost, commission, common_oh, lab_test, inspection, freight, comm_cost from wo_pre_cost_dtls where job_no='".$job_no."' and status_active=1 and is_deleted=0");
+                        
                        $dzn_qnty=0;
                        if($fabriccostArray[0][csf('costing_per_id')]==1)
                        {
                            $dzn_qnty=12;
                        }
                        else if($fabriccostArray[0][csf('costing_per_id')]==3)
                        {
                            $dzn_qnty=12*2;
                        }
                        else if($fabriccostArray[0][csf('costing_per_id')]==4)
                        {
                            $dzn_qnty=12*3;
                        }
                        else if($fabriccostArray[0][csf('costing_per_id')]==5)
                        {
                            $dzn_qnty=12*4;
                        }
                        else
                        {
                            $dzn_qnty=1;
                        } $dzn_qnty=$dzn_qnty*$ratio_qty;
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
			 $data_array=("select cons_process,req_qnty as req_qnty,charge_unit as charge_unit,amount as amount from wo_pre_cost_fab_conv_cost_dtls where job_no='$job_no' and cons_process in(34,65,66,67,68,69,70,71,73,75,76,77,88,90,91,92,93,100,125,127,128,129) and status_active=1 and is_deleted=0 group by id,cons_process,req_qnty,charge_unit,amount ");
					$sql_result=sql_select($data_array);
					foreach($sql_result as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
							$cons_qty=($row[csf('req_qnty')]/$dzn_qnty)*$row[csf('charge_unit')];
							$tot_cons_amount=$cons_qty*$order_qty;
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="70" align="center"><p><? echo $conversion_cost_head_array[$row[csf('cons_process')]]; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($order_qty,2); ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($cons_qty,4); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row[csf('req_qnty')],2); ?></p></td>
                            <td width="50" align="right"><p><? echo number_format($row[csf('charge_unit')],2); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($tot_cons_amount,2); ?></p></td>
                        </tr>
						<?
						$tot_req_qty+=$row[csf('req_qnty')];
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
	$order_qty=return_field_value("sum(b.plan_cut*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","po_quantity");
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
                        }$dzn_qnty= $dzn_qnty*$ratio_qty;
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
			$data_array=("select cons_type,req_qnty as req_qnty,charge_unit as charge_unit,amount as amount from wo_pri_quo_fab_conv_cost_dtls where quotation_id='$quotation_id' and cons_type in(34,65,66,67,68,69,70,71,73,75,76,77,88,90,91,92,93,100,125,127,128,129) and status_active=1 and is_deleted=0 group by id,cons_type,req_qnty,charge_unit,amount ");
					$sql_result=sql_select($data_array);
					foreach($sql_result as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
							$cons_qty=($row[csf('req_qnty')]/$dzn_qnty)*$row[csf('charge_unit')];
							$tot_cons_amount=$cons_qty*$order_qty;
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="70" align="center"><p><? echo $conversion_cost_head_array[$row[csf('cons_process')]]; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($order_qty,2); ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($cons_qty,4); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row[csf('req_qnty')],2); ?></p></td>
                            <td width="50" align="right"><p><? echo number_format($row[csf('charge_unit')],2); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($tot_cons_amount,2); ?></p></td>
                        </tr>
						<?
						$tot_req_qty+=$row[csf('req_qnty')];
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
	$order_qty=return_field_value("sum(b.plan_cut*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","po_quantity");
	$ratio_qty=return_field_value("sum(a.total_set_qnty) as ratio", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","ratio");
	//print($order_qty);die;
    $costing_per=return_field_value("costing_per as costing_per", "wo_pre_cost_mst", "job_no='$job_no'","costing_per");
						if($costing_per==1)
						{
							$costing_per_dzn="1 Dzn";
						}
						else if($costing_per==2)
						{
							$costing_per_dzn="1 Pcs";
						}
						else if($costing_per==3)
						{
							$costing_per_dzn="2 Dzn";
						}
						else if($costing_per==4)
						{
							$costing_per_dzn="3 Dzn";
						}
						else if($costing_per==5)
						{
							$costing_per_dzn="4 Dzn";
						}
						
	$fabriccostArray=sql_select("select costing_per_id, trims_cost, embel_cost, cm_cost, commission, common_oh, lab_test, inspection, freight, comm_cost from wo_pre_cost_dtls where job_no='".$job_no."' and status_active=1 and is_deleted=0");
                        
                        $dzn_qnty=0;
                        if($fabriccostArray[0][csf('costing_per_id')]==1)
                        {
                            $dzn_qnty=12;
                        }
                        else if($fabriccostArray[0][csf('costing_per_id')]==3)
                        {
                            $dzn_qnty=12*2;
                        }
                        else if($fabriccostArray[0][csf('costing_per_id')]==4)
                        {
                            $dzn_qnty=12*3;
                        }
                        else if($fabriccostArray[0][csf('costing_per_id')]==5)
                        {
                            $dzn_qnty=12*4;
                        }
                        else
                        {
                            $dzn_qnty=1;
                        } $dzn_qnty=$dzn_qnty*$ratio_qty;
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
                    <th width="70">Process</th>
                    <th width="80">GMTS Qty.</th>
                   
                    <th width="80">Req.Qty./<? echo $costing_per_dzn; ?></th>
                    <th width="50">Cost/Per Unit</th>
                    <th width="80">Amount</th>
				</thead>
                <tbody>
                <?
					
			 $i=1;
			  $data_array=("select cons_process,req_qnty as req_qnty,charge_unit as charge_unit,amount as amount from wo_pre_cost_fab_conv_cost_dtls where job_no='$job_no' and cons_process in(64,82,89) and status_active=1 and is_deleted=0 group by id,cons_process,req_qnty ,charge_unit,amount");
					$sql_result=sql_select($data_array);
					foreach($sql_result as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
							//echo $dzn_qnty;
							$cons_qty=($row[csf('req_qnty')]/$dzn_qnty)*$row[csf('charge_unit')];
							$tot_amount=$row[csf('amount')];
							$total_amount=($tot_amount/$dzn_qnty)*$order_qty;
							$tot_cons_amount=$cons_qty*$order_qty;
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="70" align="center"><p><? echo $conversion_cost_head_array[$row[csf('cons_process')]]; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($order_qty,2); ?></p></td>
                          
                            <td width="80" align="right"><p><? echo number_format($row[csf('req_qnty')],2); ?></p></td>
                            <td width="50" align="right"><p><? echo number_format($row[csf('charge_unit')],2); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($total_amount,2); ?></p></td>
                        </tr>
						<?
						$tot_req_qty+=$row[csf('req_qnty')];
						$tot_wash_amount+=$total_amount;
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="3" align="right">Total</td>
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
	$order_qty=return_field_value("sum(b.plan_cut*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","po_quantity");
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
                   
                    <th width="80">Req.Qty./<? echo $costing_per_dzn; ?></th>
                    <th width="50">Cost/Per Unit</th>
                    <th width="80">Amount</th>
				</thead>
                <tbody>
                <?
					
			 $i=1;
			  $data_array=("select cons_type ,req_qnty as req_qnty,charge_unit as charge_unit,amount as amount from wo_pri_quo_fab_conv_cost_dtls where quotation_id='$quotation_id' and cons_type in(64,82,89) and status_active=1 and is_deleted=0 group by id,cons_type ,req_qnty ,charge_unit,amount");
					$sql_result=sql_select($data_array);
					foreach($sql_result as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
							//echo $dzn_qnty;
							$cons_qty=($row[csf('req_qnty')]/$dzn_qnty)*$row[csf('charge_unit')];
							$tot_amount=$row[csf('amount')];
							$total_amount=($tot_amount/$dzn_qnty)*$order_qty;
							$tot_cons_amount=$cons_qty*$order_qty;
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="70" align="center"><p><? echo $conversion_cost_head_array[$row[csf('cons_process')]]; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($order_qty,2); ?></p></td>
                          
                            <td width="80" align="right"><p><? echo number_format($row[csf('req_qnty')],2); ?></p></td>
                            <td width="50" align="right"><p><? echo number_format($row[csf('charge_unit')],2); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($total_amount,2); ?></p></td>
                        </tr>
						<?
						$tot_req_qty+=$row[csf('req_qnty')];
						$tot_wash_amount+=$total_amount;
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="3" align="right">Total</td>
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
	$order_qty=return_field_value("sum(b.plan_cut*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","po_quantity");
	$ratio_qty=return_field_value("sum(a.total_set_qnty) as ratio", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","ratio");
	//print($order_qty);die;
    $costing_per=return_field_value("costing_per as costing_per", "wo_pre_cost_mst", "job_no='$job_no'","costing_per");
	if($costing_per==1)
	{
		$costing_per_dzn="1 Dzn";
	}
	else if($costing_per==2)
	{
		$costing_per_dzn="1 Pcs";
	}
	else if($costing_per==3)
	{
		$costing_per_dzn="2 Dzn";
	}
	else if($costing_per==4)
	{
		$costing_per_dzn="3 Dzn";
	}
	else if($costing_per==5)
	{
		$costing_per_dzn="4 Dzn";
	}
						
	$fabriccostArray=sql_select("select costing_per_id, trims_cost, embel_cost, cm_cost, commission, common_oh, lab_test, inspection, freight, comm_cost from wo_pre_cost_dtls where job_no='".$job_no."' and status_active=1 and is_deleted=0");
                        
	$dzn_qnty=0;
	if($fabriccostArray[0][csf('costing_per_id')]==1)
	{
		$dzn_qnty=12;
	}
	else if($fabriccostArray[0][csf('costing_per_id')]==3)
	{
		$dzn_qnty=12*2;
	}
	else if($fabriccostArray[0][csf('costing_per_id')]==4)
	{
		$dzn_qnty=12*3;
	}
	else if($fabriccostArray[0][csf('costing_per_id')]==5)
	{
		$dzn_qnty=12*4;
	}
	else
	{
		$dzn_qnty=1;
	}$dzn_qnty=$dzn_qnty*$$ratio_qty;
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
                    <th width="80">Req.Qty./<? echo $costing_per_dzn; ?></th>
                    <th width="50">Cost/Per Unit</th>
                    <th width="80">Amount</th>
                </thead>
         <tbody>
                <?
			 $i=1;
			  $data_array=("select cons_process,req_qnty as req_qnty,charge_unit as charge_unit,amount as amount from wo_pre_cost_fab_conv_cost_dtls where job_no='$job_no' and cons_process in(35,36,37) and status_active=1 and is_deleted=0 group by id,cons_process,req_qnty,charge_unit,amount");
					$sql_result=sql_select($data_array);
					foreach($sql_result as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
							
							$tot_amount=$row[csf('amount')];
							$total_amount=($tot_amount/$dzn_qnty)*$order_qty;
							$tot_cons_amount=$cons_qty*$order_qty;
						?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="30"><p><? echo $i; ?></p></td>
                        <td width="70" align="center"><p><? echo $conversion_cost_head_array[$row[csf('cons_process')]]; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($order_qty,2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($row[csf('req_qnty')],2); ?></p></td>
                        <td width="50" align="right"><p><? echo number_format($row[csf('charge_unit')],2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($total_amount,2); ?></p></td>
                    </tr>
						<?
						$tot_req_qty+=$row[csf('req_qnty')];
						$total_all_over_amount+=$total_amount;
						$i++;
					}
				?>
           </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="3" align="right">Total</td>
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
// Pre cost all over end
if($action=="fab_price_all_over_detail")
{
	echo load_html_head_contents("Fabrics All Over Cost Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $job_no;die;
	$order_qty=return_field_value("sum(b.plan_cut*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","po_quantity");
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
                    <th width="80">Req.Qty./<? echo $costing_per_dzn; ?></th>
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
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
							
							$tot_amount=$row[csf('amount')];
							$total_amount=($tot_amount/$dzn_qnty)*$order_qty;
							$tot_cons_amount=$cons_qty*$order_qty;
						?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="30"><p><? echo $i; ?></p></td>
                        <td width="70" align="center"><p><? echo $conversion_cost_head_array[$row[csf('cons_process')]]; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($order_qty,2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($row[csf('req_qnty')],2); ?></p></td>
                        <td width="50" align="right"><p><? echo number_format($row[csf('charge_unit')],2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($total_amount,2); ?></p></td>
                    </tr>
						<?
						$tot_req_qty+=$row[csf('req_qnty')];
						$total_all_over_amount+=$total_amount;
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="3" align="right">Total</td>
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
	$order_qty=return_field_value("sum(b.po_quantity*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","po_quantity");
	$ratio_qty=return_field_value("sum(a.total_set_qnty) as ratio", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","ratio");
	//print($order_qty);die;
    $costing_per=return_field_value("costing_per as costing_per", "wo_pre_cost_mst", "job_no='$job_no'","costing_per");
	if($costing_per==1)
	{
		$costing_per_dzn="1 Dzn";
	}
	else if($costing_per==2)
	{
		$costing_per_dzn="1 Pcs";
	}
	else if($costing_per==3)
	{
		$costing_per_dzn="2 Dzn";
	}
	else if($costing_per==4)
	{
		$costing_per_dzn="3 Dzn";
	}
	else if($costing_per==5)
	{
		$costing_per_dzn="4 Dzn";
	}
	
	$fabriccostArray=sql_select("select costing_per_id, trims_cost, embel_cost, cm_cost, commission, common_oh, lab_test, inspection, freight, comm_cost from wo_pre_cost_dtls where job_no='".$job_no."' and status_active=1 and is_deleted=0");
                        
	$dzn_qnty=0;
	if($fabriccostArray[0][csf('costing_per_id')]==1)
	{
		$dzn_qnty=12;
	}
	else if($fabriccostArray[0][csf('costing_per_id')]==3)
	{
		$dzn_qnty=12*2;
	}
	else if($fabriccostArray[0][csf('costing_per_id')]==4)
	{
		$dzn_qnty=12*3;
	}
	else if($fabriccostArray[0][csf('costing_per_id')]==5)
	{
		$dzn_qnty=12*4;
	}
	else
	{
		$dzn_qnty=1;
	}$dzn_qnty=$dzn_qnty*$ratio_qty;
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
			$trimsArray=("select  b.po_break_down_id,a.trim_group,a.description, a.cons_dzn_gmts,a.cons_uom, a.brand_sup_ref,a.amount, a.rate 
	from wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b 
	where a.id=b.wo_pre_cost_trim_cost_dtls_id and a.job_no=b.job_no  and a.job_no='$job_no' and b.po_break_down_id=$po_id and a.status_active=1 and  a.is_deleted=0 group by   b.po_break_down_id,a.trim_group,a.description, a.cons_dzn_gmts,a.cons_uom, a.brand_sup_ref,a.amount, a.rate  ");
					$sql_result=sql_select($trimsArray);
					foreach($sql_result as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
							$tot_amount=$row[csf('cons_dzn_gmts')];
							$total_reg=($order_qty/$dzn_qnty)*$tot_amount;
							$tot_cons_amount=$row[csf('rate')]*$total_reg;
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="70" align="center"><p><? echo $item_library[$row[csf('trim_group')]]; ?></p></td>
                            <td width="80" align="right"><p><? echo $row[csf('description')]; ?></p></td>
                            <td width="80" align="right"><p><? echo $row[csf('brand_sup_ref')]; ?></p></td>
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
                    	<td colspan="8" align="right">Total</td>
                        <td align="right"><? echo  number_format($tot_req_qty,2); ?>&nbsp;</td>
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
	from wo_pri_quo_trim_cost_dtls a
	where   a.quotation_id='$quotation_id' and a.status_active=1 and  a.is_deleted=0 group by  a.trim_group,a.cons_dzn_gmts,a.cons_uom,a.amount, a.rate  ");
					$sql_result=sql_select($trimsArray);
					foreach($sql_result as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
							
							$tot_amount=$row[csf('cons_dzn_gmts')];
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
	$order_qty=return_field_value("sum(b.po_quantity*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id='$po_id'","po_quantity");
	$ratio_qty=return_field_value("sum(a.total_set_qnty) as ratio", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id='$po_id'","ratio");
	//print($order_qty);die;
    $costing_per=return_field_value("costing_per as costing_per", "wo_pre_cost_mst", "job_no='$job_no'","costing_per");
	if($costing_per==1)
	{
		$costing_per_dzn="1 Dzn";
	}
	else if($costing_per==2)
	{
		$costing_per_dzn="1 Pcs";
	}
	else if($costing_per==3)
	{
		$costing_per_dzn="2 Dzn";
	}
	else if($costing_per==4)
	{
		$costing_per_dzn="3 Dzn";
	}
	else if($costing_per==5)
	{
		$costing_per_dzn="4 Dzn";
	}
						
	$fabriccostArray=sql_select("select costing_per_id, trims_cost, embel_cost, cm_cost, commission, common_oh, lab_test, inspection, freight, comm_cost from wo_pre_cost_dtls where job_no='".$job_no."' and status_active=1 and is_deleted=0");
                        
	$dzn_qnty=0;
	if($fabriccostArray[0][csf('costing_per_id')]==1)
	{
		$dzn_qnty=12;
	}
	else if($fabriccostArray[0][csf('costing_per_id')]==3)
	{
		$dzn_qnty=12*2;
	}
	else if($fabriccostArray[0][csf('costing_per_id')]==4)
	{
		$dzn_qnty=12*3;
	}
	else if($fabriccostArray[0][csf('costing_per_id')]==5)
	{
		$dzn_qnty=12*4;
	}
	else
	{
		$dzn_qnty=1;
	}$dzn_qnty=$dzn_qnty*$ratio_qty;
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
             $i=1;
             $data_emb=("select  job_no,emb_name,emb_type,cons_dzn_gmts,rate, amount  AS print_amount from  wo_pre_cost_embe_cost_dtls where job_no='$job_no' and emb_name=1 and status_active=1 and  is_deleted=0  group by job_no,emb_name,emb_type,cons_dzn_gmts,amount,rate");
            $sql_result=sql_select($data_emb);
        foreach($sql_result as $row)
            {
                if ($i%2==0)  
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";	
                    
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
}// Price Print
if($action=="price_print_cost_detail")//budget
{
	echo load_html_head_contents("Trim Cost Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $quotation_id.'hhhhh';die;
	$order_qty=return_field_value("sum(b.plan_cut*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id=$po_id","po_quantity");
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
             $data_emb=("select  quotation_id,emb_name,emb_type,cons_dzn_gmts,rate, amount  AS print_amount from  wo_pri_quo_embe_cost_dtls where quotation_id='$quotation_id' and emb_name=1 and status_active=1 and  is_deleted=0  group by quotation_id,emb_name,emb_type,cons_dzn_gmts,amount,rate");
            $sql_result=sql_select($data_emb);
        foreach($sql_result as $row)
            {
                if ($i%2==0)  
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";	
                    
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
}// Price Embroidery

if($action=="price_embroidery_cost_detail")//budget
{
	echo load_html_head_contents("Trim Cost Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $quotation_id.'hhhhh';die;
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
                if ($i%2==0)  
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";	
                    
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
if($action=="price_wash_cost_detail")//budget
{
	echo load_html_head_contents("Trim Cost Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $quotation_id.'hhhhh';die;
	$order_qty=return_field_value("sum(b.plan_cut*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id='$po_id'","po_quantity");
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
                if ($i%2==0)  
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";	
                    
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
	$order_qty=return_field_value("sum(b.po_quantity*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id='$po_id'","po_quantity");
	$ratio_qty=return_field_value("sum(a.total_set_qnty) as ratio", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id='$po_id'","ratio");
	//print($order_qty);die;
    $costing_per=return_field_value("costing_per as costing_per", "wo_pre_cost_mst", "job_no='$job_no'","costing_per");
	if($costing_per==1)
	{
		$costing_per_dzn="1 Dzn";
	}
	else if($costing_per==2)
	{
		$costing_per_dzn="1 Pcs";
	}
	else if($costing_per==3)
	{
		$costing_per_dzn="2 Dzn";
	}
	else if($costing_per==4)
	{
		$costing_per_dzn="3 Dzn";
	}
	else if($costing_per==5)
	{
		$costing_per_dzn="4 Dzn";
	}
						
	$fabriccostArray=sql_select("select costing_per_id, trims_cost, embel_cost, cm_cost, commission, common_oh, lab_test, inspection, freight, comm_cost from wo_pre_cost_dtls where job_no='".$job_no."' and status_active=1 and is_deleted=0");
                        
	$dzn_qnty=0;
	if($fabriccostArray[0][csf('costing_per_id')]==1)
	{
		$dzn_qnty=12;
	}
	else if($fabriccostArray[0][csf('costing_per_id')]==3)
	{
		$dzn_qnty=12*2;
	}
	else if($fabriccostArray[0][csf('costing_per_id')]==4)
	{
		$dzn_qnty=12*3;
	}
	else if($fabriccostArray[0][csf('costing_per_id')]==5)
	{
		$dzn_qnty=12*4;
	}
	else
	{
		$dzn_qnty=1;
	}$dzn_qnty=$dzn_qnty*$ratio_qty;
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
                if ($i%2==0)  
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";	
                    
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
}//wash_cost_detail
if($action=="wash_cost_detail")//budget
{
	echo load_html_head_contents("Trim Cost Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $job_no;die;
	$order_qty=return_field_value("sum(b.po_quantity*a.total_set_qnty) as po_quantity", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id='$po_id'","po_quantity");
	$ratio_qty=return_field_value("sum(a.total_set_qnty) as ratio", "wo_po_break_down b,wo_po_details_master a ", "a.job_no=b.job_no_mst and b.job_no_mst='$job_no' and  a.company_name='$company_id' and b.id='$po_id'","ratio");
	//print($order_qty);die;
    $costing_per=return_field_value("costing_per as costing_per", "wo_pre_cost_mst", "job_no='$job_no'","costing_per");
	if($costing_per==1)
	{
		$costing_per_dzn="1 Dzn";
	}
	else if($costing_per==2)
	{
		$costing_per_dzn="1 Pcs";
	}
	else if($costing_per==3)
	{
		$costing_per_dzn="2 Dzn";
	}
	else if($costing_per==4)
	{
		$costing_per_dzn="3 Dzn";
	}
	else if($costing_per==5)
	{
		$costing_per_dzn="4 Dzn";
	}
						
	$fabriccostArray=sql_select("select costing_per_id, trims_cost, embel_cost, cm_cost, commission, common_oh, lab_test, inspection, freight, comm_cost from wo_pre_cost_dtls where job_no='".$job_no."' and status_active=1 and is_deleted=0");
                        
	$dzn_qnty=0;
	if($fabriccostArray[0][csf('costing_per_id')]==1)
	{
		$dzn_qnty=12;
	}
	else if($fabriccostArray[0][csf('costing_per_id')]==3)
	{
		$dzn_qnty=12*2;
	}
	else if($fabriccostArray[0][csf('costing_per_id')]==4)
	{
		$dzn_qnty=12*3;
	}
	else if($fabriccostArray[0][csf('costing_per_id')]==5)
	{
		$dzn_qnty=12*4;
	}
	else
	{
		$dzn_qnty=1;
	}$dzn_qnty=$dzn_qnty*$ratio_qty;
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
                if ($i%2==0)  
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";	
                    
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
if($action=="search_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Season No Info", "../../../../", 1, 1,'','','');
	
	?>
	<script>
		$(document).ready(function(e) {
            setFilterGrid('tbl_list_search',-1);
        });
		var selected_name = new Array;
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
			if( jQuery.inArray( str[1], selected_name ) == -1 ) {
				selected_name.push( str[1] );
			}
			else {
				for( var i = 0; i < selected_name.length; i++ ) {
					if( selected_name[i] == str[1] ) break;
				}
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_name.length; i++ ) {
				name += selected_name[i] + ',';
			}
			
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_season').val( name );
		}
    </script>
</head>
<body>
<div align="center">
	<form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:350px;">
        	<input type="hidden" name="hide_season" id="hide_season" value="" />
            <?
				if($buyerID==0)
				{
					if ($_SESSION['logic_erp']["data_level_secured"]==1)
					{
						if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
					}
					else $buyer_id_cond="";
				}
				else $buyer_id_cond=" and buyer_name=$buyerID";
				if($job_no!=0) $jobno=" and job_no_prefix_num in (".$job_no.")"; else $jobno="";
		if($db_type==0)
		    {
				$sql="select distinct(season) as season from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$companyID and season<>'' $buyer_id_cond  $jobno order by season";
		    }
		if($db_type==2)
		    {
				$sql="select distinct(season) as season from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$companyID and season is not null $jobno  $buyer_id_cond order by season";
		    }
			//echo $sql;	
				echo create_list_view("tbl_list_search", "Season", "200","300","280",0, $sql , "js_set_value", "season", "", 1, "0", $arr , "season", "","",'0','',1) ;
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
// Budget2 Start 
if($action=="report_generate_budget2")
{
$process = array( &$_POST );
extract(check_magic_quote_gpc( $process )); 
$report_type=str_replace("'","",$reporttype);
//echo $report_type;
//echo $cbo_search_date;die;
$company_name=str_replace("'","",$cbo_company_name);
$season=str_replace("'","",$txt_season);
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
 
	$date_cond=" and b.po_received_date between  '$start_date' and '$end_date'";
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
if($season=="") $season_cond=""; else $season_cond=" and a.season in('".implode("','",explode(",",$season))."')";
$order_no=str_replace("'","",$txt_order_id);
$order_num=str_replace("'","",$txt_order_no);
if(str_replace("'","",$txt_order_id)!="" && str_replace("'","",$txt_order_id)!=0) $order_id_cond_trans=" and b.id in ($order_no)";
else if ($order_num=="") $order_no_cond=""; else $order_no_cond=" and  b.po_number in ('$order_num') ";
if($template==1)
{
ob_start();
$style1="#E9F3FF"; 
$style="#FFFFFF";

$fab_precost_arr=array();$commission_array=array();$knit_arr=array(); $fabriccostArray=array(); $fab_emb=array();$fabric_data_Array=array();$asking_profit_arr=array(); $yarncostArray=array(); $yarn_desc_array=array();

$yarncostDataArray=sql_select("select job_no, count_id, type_id, sum(cons_qnty) as cons_qnty, sum(amount) as amount from wo_pre_cost_fab_yarn_cost_dtls where status_active=1 and is_deleted=0 group by job_no, count_id, type_id");
foreach($yarncostDataArray as $yarnRow)
{
   $yarncostArray[$yarnRow[csf('job_no')]].=$yarnRow[csf('count_id')]."**".$yarnRow[csf('type_id')]."**".$yarnRow[csf('cons_qnty')]."**".$yarnRow[csf('amount')].",";
}
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
$fabricDataArray=sql_select("select a.job_no, a.fab_nature_id, a.fabric_source, a.rate, b.yarn_cons_qnty, b.yarn_amount from wo_pre_cost_fabric_cost_dtls a, wo_pre_cost_sum_dtls b where a.job_no=b.job_no and a.fabric_source!=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");
foreach($fabricDataArray as $fabricRow)
{
	$fabric_data_Array[$fabricRow[csf('job_no')]].=$fabricRow[csf('fab_nature_id')]."**".$fabricRow[csf('fabric_source')]."**".$fabricRow[csf('rate')]."**".$fabricRow[csf('yarn_cons_qnty')]."**".$fabricRow[csf('yarn_amount')].",";
}//Pre cost end

//print_r($fabric_data_Array).'hhhhh';


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
 $fabriccostDataArray=sql_select("select job_no, costing_per_id, trims_cost, embel_cost, cm_cost,depr_amor_pre_cost, commission, common_oh, lab_test, inspection, freight, comm_cost,certificate_pre_cost,currier_pre_cost from wo_pre_cost_dtls where status_active=1 and is_deleted=0  ");
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
$financial_para=array();
$sql_std_para=sql_select("select interest_expense,income_tax,cost_per_minute from lib_standard_cm_entry where company_id=$company_name and status_active=1 and	is_deleted=0 order by id");	
foreach($sql_std_para as $sql_std_row)
	{
		$financial_para[csf('interest_expense')]=$sql_std_row[csf('interest_expense')];
		$financial_para[csf('income_tax')]=$sql_std_row[csf('income_tax')];
		$financial_para[csf('cost_per_minute')]=$sql_std_row[csf('cost_per_minute')];
	} 
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
		text.innerHTML = "Show Budget Summary";
	}
	else 
	{
		ele.style.display = "block";
		text.innerHTML = "Hide Budget Summary";
	}
} 
	// var total_yarn_cost=document.getElementById('total_yarn').value;
	// alert(total_yarn_cost);
	// document.getElementById('yarn_td').innerHTML=total_yarn_cost;
 </script>
        <div style="width:5185px;">
        <div style="width:900px;" align="left">
        <?
		
            //$sql_sumarry="select a.job_no,a.avg_unit_price, a.total_set_qnty as ratio,b.plan_cut,b.po_quantity, b.unit_price from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond $buyer_id_cond  $year_cond $job_no_cond $order_id_cond_trans $order_no_cond $order_status_cond $season_cond ";
			$sql_sumarry="select a.job_no, b.id as po_id,b.insert_date,a.avg_unit_price,b.plan_cut,a.total_set_qnty as ratio,b.po_quantity as po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond $buyer_id_cond  $year_cond $job_no_cond $order_id_cond_trans $order_no_cond $order_status_cond $season_cond group by  b.id,a.job_no,a.avg_unit_price,b.plan_cut,a.total_set_qnty,b.po_quantity,b.insert_date ";
			//echo $sql_sumarry;
			$result_data=sql_select($sql_sumarry);
			$total_trim_cost=0;//$yarn_costing_sum=0;
			$total_gross_fob_value=0;
			$tot_commercial_cost_sum=0;$total_cm_cost_sum=0;$operating_expense_sum=0;
			$operating_expense_sum=0;$total_interest_expense_sum=0;
			$total_income_tax_sum=0;
			$total_depreciation_amortization_sum=0;
			$total_net_profit=0;
			$total_gross_fob_plancut_value=0;
			$total_yarn=0;$total_embell_cost_sum=0;$total_total_trim_cost_sum=0;$total_conversion_cost_sum=0;
			$total_other_direct_expenses_sum=0;
			$cost_of_material_service_sum=0;
			foreach($result_data as $sm_row)
			{
			
			$po_plan_cut_qty_sum=$sm_row[csf('plan_cut')]*$sm_row[csf('ratio')];
			$order_qty_sum_pcs=$sm_row[csf('po_quantity')]*$sm_row[csf('ratio')];
			//echo $sm_row[csf('ratio')];
			$total_gross_fob_value+=$sm_row[csf('po_quantity')]*$sm_row[csf('avg_unit_price')];
			$total_gross_fob_plancut_value+=$po_plan_cut_qty_sum*$sm_row[csf('avg_unit_price')];
			$dzn_qnty_sum=0;
			$costing_per_id=$fabriccostArray[$sm_row[csf('job_no')]]['costing_per_id'];
			if($costing_per_id==1) { $dzn_qnty_sum=12;}
			else if($costing_per_id==3){ $dzn_qnty_sum=12*2;}
			else if($costing_per_id==4) { $dzn_qnty_sum=12*3;}
			else if($costing_per_id==5){  $dzn_qnty_sum=12*4; }
			else { $dzn_qnty_sum=1;}
			$dzn_qnty_yarn=$dzn_qnty_sum*$sm_row[csf('ratio')];
			$dzn_qnty_sum=$dzn_qnty_sum*$sm_row[csf('ratio')];
			$foreign_sum=($commission_array[$sm_row[csf('job_no')]]['foreign']/$dzn_qnty_sum)*$order_qty_sum_pcs;
			$local_sum=($commission_array[$sm_row[csf('job_no')]]['local']/$dzn_qnty_sum)*$order_qty_sum_pcs;
			$tot_commision_sum+=$foreign_sum+$local_sum;
			
			$net_fob_value_summary=$total_gross_fob_value-$tot_commision_sum;
			$fabricData=explode(",",substr($fabric_data_Array[$sm_row[csf('job_no')]],0,-1));
			$fab_precost_Data=explode(",",substr($fab_precost_arr[$sm_row[csf('job_no')]][$sm_row[csf('po_id')]],0,-1));
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
							$fab_purchase_qty=$fab_requirment/$fab_pcs*$po_plan_cut_qty_sum; 
						//echo $fab_purchase_qty;
							$fab_purchase=$fab_purchase_qty*$fab_rate; 
						}
					}
				else if($fab_source_id==1 || $fab_source_id==3)
					{
					//echo $yarn_amount.'='.$po_plan_cut_qty_sum.'='.$dzn_qnty_sum;
					
						$avg_rate=$yarn_amount/$yarn_qty;
						$yarn_costing_sum=$yarn_amount/$dzn_qnty_sum*$po_plan_cut_qty_sum;
						//echo $yarn_costing_sum.'<br>';	
						//if($yarn_costing_sum>0)
						//{
						//$yarn_costing_sum=$yarn_amount/$dzn_qnty_sum*$po_plan_cut_qty_sum;
					
						//}
							
						
					}
					//echo $yarn_costing_sum.'<br>';
				}
			$kniting_cost_sum=$knit_arr[$sm_row[csf('job_no')]]['knit']+$knit_arr[$sm_row[csf('job_no')]]['weaving']+$knit_arr[$sm_row[csf('job_no')]]['collar_cuff']+$knit_arr[$sm_row[csf('job_no')]]['feeder_stripe'];
			//echo $kniting_cost_sum.'='.$dzn_qnty_sum;
			//$convamount =def_number_format(($conv_row[csf("plan_cut_qnty")]/($costing_per_qty*$set_item_ratio))*$conv_row[csf("amount")],5,"");
			$tot_kniting_cost_sum=($kniting_cost_sum/$dzn_qnty_sum)*$po_plan_cut_qty_sum;
			$washing_cost_sum=($knit_arr[$sm_row[csf('job_no')]]['washing']/$dzn_qnty_sum)*$po_plan_cut_qty_sum;
			$all_over_cost_sum=($knit_arr[$sm_row[csf('job_no')]]['all_over']/$dzn_qnty_sum)*$po_plan_cut_qty_sum;
			$yarn_dyed_cost_sum=($knit_arr[$sm_row[csf('job_no')]]['yarn_dyeing']/$dzn_qnty_sum)*$po_plan_cut_qty_sum;
			$fabric_dyeing_cost_sum=($knit_arr[$sm_row[csf('job_no')]]['fabric_dyeing']/$dzn_qnty_sum)*$po_plan_cut_qty_sum;
			$heat_setting_cost_sum=($knit_arr[$sm_row[csf('job_no')]]['heat']/$dzn_qnty_sum)*$po_plan_cut_qty_sum;
			$fabric_finish_sum=($knit_arr[$sm_row[csf('job_no')]]['fabric_finish']/$dzn_qnty_sum)*$po_plan_cut_qty_sum;
			$fabric_dyeing_cost_sum=($knit_arr[$sm_row[csf('job_no')]]['fabric_dyeing']/$dzn_qnty_sum)*$po_plan_cut_qty_sum;
			
			$freight_cost_sum= $fabriccostArray[$sm_row[csf('job_no')]]['freight']/$dzn_qnty_sum*$order_qty_sum_pcs;
			$inspection_sum=$fabriccostArray[$sm_row[csf('job_no')]]['inspection']/$dzn_qnty_sum*$order_qty_sum_pcs;
			$certificate_cost_sum=$fabriccostArray[$sm_row[csf('job_no')]]['certificate_pre_cost']/$dzn_qnty_sum*$order_qty_sum_pcs;
			
			$operating_expense=$fabriccostArray[$sm_row[csf('job_no')]]['common_oh']/$dzn_qnty_sum*$order_qty_sum_pcs;
			$currier_cost_sum=$fabriccostArray[$sm_row[csf('job_no')]]['currier_pre_cost']/$dzn_qnty_sum*$order_qty_sum_pcs;
			$lab_test_cost_sum=$fabriccostArray[$sm_row[csf('job_no')]]['lab_test']/$dzn_qnty_sum*$order_qty_sum_pcs;
			$depreciation_amortization=$fabriccostArray[$fabRow[csf('job_no')]]['depr_amor_cost']/$dzn_qnty_sum*$order_qty_sum_pcs;
			$wash_cost=($fab_emb[$sm_row[csf('job_no')]]['wash']/$dzn_qnty_sum)*$order_qty_sum_pcs;
			//$other_direct_expenses=$freight_cost+$inspection+$certificate_cost+$currier_cost+$lab_test_cost;
			$other_direct_expenses_sum=$freight_cost_sum+$inspection_sum+$certificate_cost_sum+$currier_cost_sum+$lab_test_cost_sum+$wash_cost;
			$print_cost=($fab_emb[$sm_row[csf('job_no')]]['print']/$dzn_qnty_sum)*$order_qty_sum_pcs;
			$embroidery_cost=($fab_emb[$sm_row[csf('job_no')]]['embroidery']/$dzn_qnty_sum)*$order_qty_sum_pcs;
			$special_cost=($fab_emb[$sm_row[csf('job_no')]]['special']/$dzn_qnty_sum)*$order_qty_sum_pcs;
			
			$tot_embell_cost_sum=$print_cost+$embroidery_cost+$special_cost;
			$trim_cost_sum=$fabriccostArray[$sm_row[csf('job_no')]]['trims_cost']/$dzn_qnty_sum;
			$total_trim_cost_sum=$trim_cost_sum*$order_qty_sum_pcs;
			$tot_conversion_cost_sum=$tot_kniting_cost_sum+$fabric_dyeing_cost_sum+$yarn_dyed_cost_sum+$heat_setting_cost_sum+$fabric_finish_sum+$washing_cost_sum+$all_over_cost_sum;
			//$tot_conversion_cost=$tot_knit_cost+$fabric_dyeing_cost+$yarn_dyed_cost+$heat_setting_cost+$fabric_finish+$washing_cost+$all_over_cost;
			//$tot_knit_cost_sum=($kniting_cost_sum/$dzn_qnty_sum)*$po_plan_cut_qty_sum;
			//$cost_of_material_service=$yarn_costing+$tot_conversion_cost+$tot_trim_cost+$tot_embell_cost+$other_direct_expenses;
			
			$total_yarn+=$yarn_costing_sum;	
			$total_total_trim_cost_sum+=$total_trim_cost_sum;
			$total_conversion_cost_sum+=$tot_conversion_cost_sum;
			$total_embell_cost_sum+=$tot_embell_cost_sum;
			$total_other_direct_expenses_sum+=$other_direct_expenses_sum;
			$cost_of_material_service_sum+=$tot_conversion_cost_sum+$yarn_costing_sum+$total_trim_cost_sum+$tot_embell_cost_sum+$other_direct_expenses_sum;
			$contribution_value_sum=$net_fob_value_summary-$cost_of_material_service_sum;
			
			$cm_cost_sum=$fabriccostArray[$sm_row[csf('job_no')]]['c_cost']/$dzn_qnty_sum*$order_qty_sum_pcs;
			$commercial_cost_sum=$fabriccostArray[$sm_row[csf('job_no')]]['comm_cost'];
			$tot_commercial_cost_sum+=($commercial_cost_sum/$dzn_qnty_sum)*$order_qty_sum_pcs;
			$total_cm_cost_sum+=$cm_cost_sum;
			$operating_expense_sum+=$fabriccostArray[$sm_row[csf('job_no')]]['common_oh']/$dzn_qnty_sum*$order_qty_sum_pcs;
			$gross_profit_sum=$contribution_value_sum-$total_cm_cost_sum;
			//$gross_profit_percentage_sum=$gross_profit_sum/$total_gross_fob_value*100;
			$operating_profit_sum=$gross_profit_sum-($tot_commercial_cost_sum+$operating_expense_sum);
			//$operating_profit_percent=$operating_profit/$total_gross_fob_value*100;
			//echo $fabriccostArray[$sm_row[csf('job_no')]]['depr_amor_cost']/$dzn_qnty_sum*$order_qty_sum;
			$depreciation_amortization_sum=$fabriccostArray[$sm_row[csf('job_no')]]['depr_amor_cost']/$dzn_qnty_sum*$order_qty_sum_pcs;
			//echo $net_fob_value_summary*$financial_para[csf('interest_expense')]/100;
			$interest_expense_sum=$net_fob_value_summary*$financial_para[csf('interest_expense')]/100;
			$income_tax_sum=$net_fob_value_summary*$financial_para[csf('income_tax')]/100;
			$total_interest_expense_sum+=$interest_expense_sum;
			$total_income_tax_sum+=$interest_expense_sum;
			$total_depreciation_amortization_sum+=$depreciation_amortization_sum;
			$total_net_profit=$operating_profit_sum-($total_depreciation_amortization_sum+$interest_expense_sum+$income_tax_sum);
			//$net_profit_percent=$net_profit/$total_gross_fob_value*100;
			//$yarn_costing_sum=0;
						
		}
            ?> 
           
        	<table width="900" cellpadding="0" cellspacing="2" border="0">
                <tr>
                	<td width="600" align="left">
                    	<table width="320" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="2">
                        <caption><strong>Order Wise Budget Cost Summary</strong></caption>
                        <thead align="center">
                        <th>SL</th><th>Particulars</th><th>Amount</th><th>Percentage</th>
                        </thead>
                        <tr bgcolor="<? echo $style1; ?>">
                        <td width="20">1</td>
                        <td width="100"><strong>Gross FOB Value</strong></td><td width="120" align="right"><? echo number_format($total_gross_fob_value,2);?> </td>
                        <td width="80" align="right"><? echo number_format($total_gross_fob_value/$total_gross_fob_value*100,2);?></td>
                        </tr>
                        <tr bgcolor="<?  echo $style; ?>">
                        <td width="20">2</td>
                        <td width="100">Less Commission</td><td align="right"><? echo number_format($tot_commision_sum,2); ?>  </td>
                        <td align="right"><? echo number_format($tot_commision_sum/$total_gross_fob_value*100,2);?>  </td>
                        </tr>
                        <tr bgcolor="<? echo $style1; ?>">
                        <td width="20">3</td>
                        <td width="100"><strong>Net FOB Value</strong></td><td align="right"><? echo number_format($net_fob_value_summary,2); ?></td>
                        <td align="right"> <? echo number_format($net_fob_value_summary/$total_gross_fob_value*100,2);?> </td>
                        </tr>
                        <tr bgcolor="<? echo $style; ?>">
                        <td width="20">4</td>
                        <td width="100"><strong>Less Cost of Material & Service</strong>
</td><td align="right" > <? echo number_format($cost_of_material_service_sum,2); ?></td>
                        <td align="right" > <? echo number_format($cost_of_material_service_sum/$total_gross_fob_value*100,2);?> </td>
                        </tr>
                        <tr bgcolor="<? echo $style1; ?>">
                        <td width="20">5</td>
                        <td width="100">Yarn Cost</td><td align="right" id="yarn_td" > <? echo number_format($total_yarn,2); ?></td>
                        <td align="right"> <? echo number_format($total_yarn/$total_gross_fob_value*100,2);?> </td>
                        </tr>
                         <tr bgcolor="<? echo $style; ?>">
                        <td width="20">6</td>
                        <td width="100">Conversion Cost</td><td align="right"><? echo number_format($total_conversion_cost_sum,2); ?></td>
                        <td align="right"> <? echo number_format($total_conversion_cost_sum/$total_gross_fob_value*100,2);?></td>
                        </tr>
                        <tr bgcolor="<? echo $style1; ?>">
                        <td width="20">7</td>
                        <td width="100">Trims Cost</td><td align="right"><? echo number_format($total_total_trim_cost_sum,2); ?></td>
                        <td align="right"><? echo number_format($total_total_trim_cost_sum/$total_gross_fob_value*100,2);?> </td>
                        </tr>
                        
                        <tr bgcolor="<? echo $style; ?>">
                        <td width="20">8</td>
                        <td width="100">Embelishment Cost</td><td align="right" id="inspection_id"> <? echo number_format($total_embell_cost_sum,2); ?></td>
                        <td align="right"> <? echo number_format($total_embell_cost_sum/$total_gross_fob_value*100,2);?></td>
                        </tr>
                        <tr bgcolor="<? echo $style1; ?>">
                        <td width="20">9</td>
                        <td width="100">Other Direct Expenses</td><td align="right"><? echo number_format($total_other_direct_expenses_sum,2); ?></td>
                        <td align="right"> <? echo number_format($total_other_direct_expenses_sum/$total_gross_fob_value*100,2);?> </td>
                        </tr>
                        <tr bgcolor="<? echo $style; ?>">
                        <td width="20">10</td>
                        <td width="100"><strong>Contribution/Value Additions</strong></td><td align="right" ><? echo number_format($contribution_value_sum,2); ?> </td>
                        <td align="right"> <? echo number_format($contribution_value_sum/$total_gross_fob_value*100,2);?></td>
                        </tr>
                        <tr bgcolor="<? echo $style1; ?>">
                        <td width="20">11</td>
                        <td width="100">Less CM Cost</td><td align="right"><? echo number_format($total_cm_cost_sum,2); ?></td>
                        <td align="right"><? echo number_format($total_cm_cost_sum/$total_gross_fob_value*100,2);?> </td>
                        </tr>
                        <tr bgcolor="<? echo $style; ?>">
                        <td width="20">12</td>
                        <td width="100"><strong>Gross Profit/Loss</strong></td><td align="right"><? echo number_format($gross_profit_sum,2); ?></td>
                        <td align="right"><? echo number_format($gross_profit_sum/$total_gross_fob_value*100,2);?> </td>
                        </tr>
                        <tr bgcolor="<? echo $style1; ?>">
                        <td width="20">13</td>
                        <td width="100">Less Commercial Cost</td><td align="right" id="cost_id"><? echo number_format($tot_commercial_cost_sum,2); ?></td>
                        <td align="right" id="cost_percent"> <? echo number_format($tot_commercial_cost_sum/$total_gross_fob_value*100,2);?></td>
                        </tr>
                        <tr bgcolor="<? echo $style; ?>">
                        <td width="20">14</td>
                        <td width="100">Less Operating Expenses</td><td align="right"><? echo number_format($operating_expense_sum,2); ?></td>
                        <td align="right"><? echo number_format($operating_expense_sum/$total_gross_fob_value*100,2);?> </td>
                        </tr>
                        <tr bgcolor="<? echo $style1; ?>">
                        <td width="20">15</td>
                        <td width="100"><strong>Operatin Profit/Loss</strong></td><td align="right" ><? echo number_format($operating_profit_sum,2); ?></td>
                        <td align="right"><? echo number_format($operating_profit_sum/$total_gross_fob_value*100,2);?> </td>
                        </tr>
                        <tr bgcolor="<? echo $style; ?>">
                        <td width="20">16</td>
                        <td width="100">Less Depereciation & Amortization</td><td align="right"><? echo number_format($total_depreciation_amortization_sum,2); ?></td>
                        <td align="right"> <? echo number_format($total_depreciation_amortization_sum/$total_gross_fob_value*100,2);?></td>
                        </tr>
                        <tr bgcolor="<? echo $style1; ?>">
                        <td width="20">17</td>
                        <td width="100">Less Interest</td><td align="right"><? echo number_format($interest_expense_sum,2); ?></td>
                        <td align="right"> <? echo number_format($interest_expense_sum/$total_gross_fob_value*100,2);?></td>
                        </tr>
                        <tr bgcolor="<? echo $style; ?>">
                        <td width="20">18</td>
                        <td width="100">Less Income Tax</td><td align="right"><? echo number_format($income_tax_sum,2); ?></td>
                        <td align="right"> <? echo number_format($income_tax_sum/$total_gross_fob_value*100,2);?></td>
                        </tr>
                        <tr bgcolor="<? echo $style1; ?>">
                        <td width="20">19</td>
                        <td width="100"><strong>Net Profit</strong></td><td align="right"><? echo number_format($total_net_profit,2); ?></td>
                        <td align="right"> <? echo number_format($total_net_profit/$total_gross_fob_value*100,2);?></td>
                        </tr>
                        </table>
                    </td>
                    <td colspan="5" style="min-height:800px; max-height:100%" align="center" valign="top">
                   </td>
                  </tr>
           </table>
          
           </div>
           <br/>   
         <h3 align="left" id="accordion_h2" style="width:5288px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel2', '')"> -Search Panel</h3>
        <fieldset style="width:100%;" id="content_search_panel2">	
            <table width="5285">
                    <tr class="form_caption">
                        <td colspan="50" align="center"><strong>Order Wise Budget Report</strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="50" align="center"><strong><? echo $company_library[$company_name]; ?></strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td align="left"><strong>Details Report </strong></td>
                    </tr>
            </table>
               <? //$asking_profit_head=$asking_profit_arr[$company_name]['asking_profit']; ?>
            <table id="table_header_1" class="rpt_table" width="5235" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                <tr>
                    <th width="40" rowspan="2">SL</th>
                   	<th width="70" rowspan="2">Buyer</th>
                    <th width="70" rowspan="2">Job No</th>
                    <th width="100" rowspan="2">Order No</th>
                    <th width="100" rowspan="2">Order Status</th>
                    <th width="110" rowspan="2">Style</th>
                    <th width="110" rowspan="2">Item Name</th>
                    <th width="110" rowspan="2">Dealing</th>
                    <?
                   if(str_replace("'","",$cbo_search_date)==1)
					{ ?>
						<th width="70" rowspan="2">Ship. Date</th>
					<? }
					else if(str_replace("'","",$cbo_search_date)==2)
					{ ?>
						<th width="70" rowspan="2">PO Recv. Date</th>
					<? }
					else
					{
						?>
					<th width="70" rowspan="2">PO Insert Date</th>
					<? }
					?>
                    <th width="90" rowspan="2">Order Qty</th>
                    <th width="70" rowspan="2">UOM</th>
                    <th width="90" rowspan="2">Avg Unit Price</th>
                    <th width="100" rowspan="2">Gross FOB Value</th>
                    <th colspan="3">Commission</th>
                    <th width="100" rowspan="2">Net FOB Value</th>
                    <th width="100" rowspan="2">Less:Cost of Materials & Services</th>
 					<th colspan="3">Yarn Cost</th>
                    <th  width="100" rowspan="2">Conversion Cost</th>
                   	<th colspan="9">Conversion Cost(Dyeing & Finishing)</th>
                    <th  width="100" rowspan="2">Trims Cost Per DZN</th>
                    <th  width="100" rowspan="2">TT Trims Cost</th>
                    <th width="80" rowspan="2">TT Embel. Cost</th>
                    <th colspan="3">Embel. Cost</th>
                    <th width="120" rowspan="2">Other Direct Expenses</th>
                    <th width="100" rowspan="2">Contribution/Value Additions</th>
                    
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
                    <th width="100" rowspan="2">Income Tax(<? echo $financial_para[csf('income_tax')].'%' ?>)</th>
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
                    <th width="80">Special Works</th>
                   
                   </tr>
                </thead>
            </table>
            <div style="width:5255px; max-height:400px; overflow-y:scroll" id="scroll_body">
             <table class="rpt_table" width="5235" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
                <? 
                $i=1; $total_order_qty=0; $total_foreign_cost=0; $total_yarn_dyeing_cost=0;$total_fabric_dyeing_cost_dzn=0; $total_yarn_cost=0; $grand_total_gross_fob_value=0;$total_purchase_cost=0; $total_tot_trim_cost=0;$total_trim_cost_per_dzn=0; $total_fabric_dyeing_cost=0; $total_knitting_cost=0; $total_heat_setting_cost=0;$total_finishing_cost=0; $total_washing_cost=0; $fabric_dyeing_cost_dzn=0; $total_cm_cost=0;$grand_total_gross_fob_plancut_value=0;$total_local_cost=0;$total_net_fob_value=0;$total_contribution_value=0;$total_gross_profit=0;$total_operating_profit=0;$grand_total_net_profit=0;$total_yarn_amount_per_dzn=0;$total_fabric_finish=0;
$total_washing_cost=0;$total_embell_cost=0;$total_yarn_dyed_cost=0;
$total_all_over_cost=0;$total_trim_cost=0;$total_commercial_cost=0;$grand_total_net_profit=0;$total_interet=0;$total_depreciation_amortization=0;$total_commercial_cost=0;$total_other_direct_expenses=0;$total_print_cost=0;$total_embroidery_cost=0;$total_wash_cost=0;$total_special_cost=0;$total_cost_of_material_service=0;
                
                $sql="select a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,b.insert_date,a.order_uom,b.is_confirmed,a.agent_name,a.avg_unit_price,a.dealing_marchant, a.gmts_item_id, a.total_set_qnty as ratio,b.plan_cut,b.id as po_id, b.po_number, b.pub_shipment_date,b.po_received_date, b.po_quantity, b.unit_price from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond $buyer_id_cond  $year_cond $job_no_cond $order_id_cond_trans $order_no_cond $order_status_cond $season_cond group by b.id,a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,b.is_confirmed,a.style_ref_no,a.agent_name,a.avg_unit_price,a.dealing_marchant,a.total_set_qnty,b.plan_cut, a.gmts_item_id, b.po_number, b.pub_shipment_date,b.po_received_date, b.po_quantity, b.unit_price,a.order_uom,b.insert_date order  by  b.pub_shipment_date,b.id";
				//echo $sql;
				$result=sql_select($sql);
				 $tot_rows=count($result);
				 foreach($result as $row )
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
					$total_gross_fob_value=$row[csf('po_quantity')]*$row[csf('avg_unit_price')];
					$total_gross_fob_plancut_value=$plan_cut_qnty*$row[csf('avg_unit_price')];
					//echo $row[csf('po_quantity')].'=Aziz'.$row[csf('avg_unit_price')];
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
						//print_r( $insert_date);
						$ship_po_recv_date=change_date_format($insert_date[0]);
					}
					
					
					$dzn_qnty=0;
						$costing_per_id=$fabriccostArray[$row[csf('job_no')]]['costing_per_id'];
                        if($costing_per_id==1)
                        {
                            $dzn_qnty=12;
                        }
                        else if($costing_per_id==3)
                        {
                            $dzn_qnty=12*2;
                        }
                        else if($costing_per_id==4)
                        {
                            $dzn_qnty=12*3;
                        }
                        else if($costing_per_id==5)
                        {
                            $dzn_qnty=12*4;
                        }
                        else
                        {
                            $dzn_qnty=1;
                        }
						$dzn_qnty_yarn=$dzn_qnty*$row[csf('ratio')];
						$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
						$order_qty_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
				?>
				 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                     <td width="40"><? echo $i; ?></td>
                     <td  width="70" title="<? echo $buyer_library[$row[csf('buyer_name')]] ?>"><p><? echo $buyer_library[$row[csf('buyer_name')]] ?></p></td>
                     <td  width="70" title="<? echo $row[csf('job_no_prefix_num')];  ?>"><p><? echo $row[csf('job_no_prefix_num')];  ?></p></td>
                     <td  width="100" title="<? echo $row[csf('po_number')]; ?>"><p><a href="#" onClick="precost_bom_pop('<?  echo $row[csf('po_id')]; ?>','<?  echo $row[csf('job_no')]; ?>','<?  echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>');"><? echo $row[csf('po_number')]; ?></a></p></td>
                     <td  width="100" title="<? echo $order_status[$row[csf('is_confirmed')]]; ?>"><p><? echo  $order_status[$row[csf('is_confirmed')]]; ?></p></td>
                     <td  width="110" title="<? echo $row[csf('style_ref_no')]; ?>"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                     <td  width="110" title="<?   echo $gmts_item; ?>"><p>
					<? $gmts_item='';
                    $gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
                    foreach($gmts_item_id as $item_id)
                    {
                        if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
                    }
					//echo $item_id.'G';
                   echo $gmts_item;
					?>
                     </p></td>
                     <td  width="110" title="<? echo $team_member_arr[$row[csf('dealing_marchant')]]; ?>"><p><? echo $team_member_arr[$row[csf('dealing_marchant')]]; ?></p></td>
                     <td  width="70" title="<? echo $ship_po_recv_date; ?>"><p><? echo $ship_po_recv_date; ?></p></td>
                     <td  width="90" align="right" title="<? echo number_format($row[csf('po_quantity')],2); ?>"><p><? echo number_format($order_qty_pcs,2); ?></p></td>
                     <td  width="70" align="right" title="<? echo $unit_of_measurement[$row[csf('order_uom')]]; ?>"><p><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></p></td>
                     <td  width="90" align="right" title="<? echo number_format($row[csf('avg_unit_price')],2); ?>"><p><? echo number_format($row[csf('avg_unit_price')],2); ?></p></td>
                     <td width="100" align="right" title="<? echo number_format($total_gross_fob_value,2); ?>" ><p><? echo number_format($total_gross_fob_value,2); ?></p></td>
                     <?
                        
						$foreign=($commission_array[$row[csf('job_no')]]['foreign']/$dzn_qnty)*$order_qty_pcs;
						$local=($commission_array[$row[csf('job_no')]]['local']/$dzn_qnty)*$order_qty_pcs;
						$tot_commision=$foreign+$local;
						$commission_percent=$tot_commision/$total_gross_fob_value*100;
						$net_fob_value=$total_gross_fob_value-$tot_commision;
						$commercial_cost=$fabriccostArray[$row[csf('job_no')]]['comm_cost'];
						$tot_commercial_cost=($commercial_cost/$dzn_qnty)*$plan_cut_qnty;
						$fabricData=explode(",",substr($fabric_data_Array[$row[csf('job_no')]],0,-1));
						$fab_precost_Data=explode(",",substr($fab_precost_arr[$row[csf('job_no')]][$row[csf('po_id')]],0,-1));
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
							}
						 
						}
					$yarn_cost_percent=($yarn_costing/$total_gross_fob_value)*100;
					$total_yarn_cost_percent+=$yarn_cost_percent;
				// echo $tot_commision.'aziz='.$total_gross_fob_value;
				$kniting_cost=$knit_arr[$row[csf('job_no')]]['knit']+$knit_arr[$row[csf('job_no')]]['weaving']+$knit_arr[$row[csf('job_no')]]['collar_cuff']+$knit_arr[$row[csf('job_no')]]['feeder_stripe'];
					$tot_knit_cost=($kniting_cost/$dzn_qnty)*$plan_cut_qnty;
					$knit_cost_dzn=$kniting_cost;
					$washing_cost=($knit_arr[$row[csf('job_no')]]['washing']/$dzn_qnty)*$plan_cut_qnty;
					$all_over_cost=($knit_arr[$row[csf('job_no')]]['all_over']/$dzn_qnty)*$plan_cut_qnty;
					$yarn_dyed_cost=($knit_arr[$row[csf('job_no')]]['yarn_dyeing']/$dzn_qnty)*$plan_cut_qnty;
					$yarn_dyeing_cost_dzn=$knit_arr[$row[csf('job_no')]]['yarn_dyeing'];
					$fabric_dyeing_cost=($knit_arr[$row[csf('job_no')]]['fabric_dyeing']/$dzn_qnty)*$plan_cut_qnty;
					$fabric_dyeing_cost_dzn=$knit_arr[$row[csf('job_no')]]['fabric_dyeing'];
					$heat_setting_cost=($knit_arr[$row[csf('job_no')]]['heat']/$dzn_qnty)*$plan_cut_qnty;
					$fabric_finish=($knit_arr[$row[csf('job_no')]]['fabric_finish']/$dzn_qnty)*$plan_cut_qnty;
					$tot_trim_cost=($fabriccostArray[$row[csf('job_no')]]['trims_cost']/$dzn_qnty)*$order_qty_pcs;
					$trim_cost_per_dzn=$fabriccostArray[$row[csf('job_no')]]['trims_cost'];
						//echo $fab_emb[$row[csf('job_no')]]['print'].'='.$plan_cut_qnty;
					$print_cost=($fab_emb[$row[csf('job_no')]]['print']/$dzn_qnty)*$order_qty_pcs;
                    $embroidery_cost=($fab_emb[$row[csf('job_no')]]['embroidery']/$dzn_qnty)*$order_qty_pcs;
                    $special_cost=($fab_emb[$row[csf('job_no')]]['special']/$dzn_qnty)*$order_qty_pcs;
					$wash_cost=($fab_emb[$row[csf('job_no')]]['wash']/$dzn_qnty)*$order_qty_pcs;
					$tot_embell_cost=$print_cost+$embroidery_cost+$special_cost;
					$cm_cost=$fabriccostArray[$row[csf('job_no')]]['c_cost']/$dzn_qnty*$order_qty_pcs;
					$commercial_cost=$fabriccostArray[$row[csf('job_no')]]['comm_cost'];
					$tot_commercial_cost=($commercial_cost/$dzn_qnty)*$order_qty_pcs;
					
					$freight_cost= $fabriccostArray[$row[csf('job_no')]]['freight']/$dzn_qnty*$order_qty_pcs;
                    $inspection=$fabriccostArray[$row[csf('job_no')]]['inspection']/$dzn_qnty*$order_qty_pcs;
                    $certificate_cost=$fabriccostArray[$row[csf('job_no')]]['certificate_pre_cost']/$dzn_qnty*$order_qty_pcs;
                    
                    $operating_expense=$fabriccostArray[$row[csf('job_no')]]['common_oh']/$dzn_qnty*$order_qty_pcs;
                    $currier_cost=$fabriccostArray[$row[csf('job_no')]]['currier_pre_cost']/$dzn_qnty*$order_qty_pcs;
					$lab_test_cost=$fabriccostArray[$row[csf('job_no')]]['lab_test']/$dzn_qnty*$order_qty_pcs;
					$depreciation_amortization=$fabriccostArray[$row[csf('job_no')]]['depr_amor_cost']/$dzn_qnty*$order_qty_pcs;
					$other_direct_expenses=$freight_cost+$inspection+$certificate_cost+$currier_cost+$lab_test_cost+$wash_cost;
					$tot_conversion_cost=$tot_knit_cost+$fabric_dyeing_cost+$yarn_dyed_cost+$heat_setting_cost+$fabric_finish+$washing_cost+$all_over_cost;
					$cost_of_material_service=$yarn_costing+$tot_conversion_cost+$tot_trim_cost+$tot_embell_cost+$other_direct_expenses;
					$contribution_value=$net_fob_value-$cost_of_material_service;
					//echo $yarn_costing;
					$contribution_value_percent=$contribution_value/$total_gross_fob_value*100;
					$total_all_cost=$yarn_costing+$tot_conversion_cost+$tot_trim_cost+$tot_embell_cost+$other_direct_expenses+$contribution_value+$cm_cost;
					$gross_profit=$contribution_value-$cm_cost;
					$gross_profit_percentage=$gross_profit/$total_gross_fob_value*100;
					$operating_profit=$gross_profit-($tot_commercial_cost+$operating_expense);
					$operating_profit_percent=$operating_profit/$total_gross_fob_value*100;
					
					$interest_expense=$net_fob_value*$financial_para[csf('interest_expense')]/100;
					$income_tax=$net_fob_value*$financial_para[csf('income_tax')]/100;
					//$interest_expense_job=$NetFOBValue_job*$financial_para[csf('interest_expense')]/100;
					//$income_tax_job=$NetFOBValue_job*$financial_para[csf('income_tax')]/100;
					$net_profit=$operating_profit-($depreciation_amortization+$interest_expense+$income_tax);
					$net_profit_percent=$net_profit/$total_gross_fob_value*100;
					 ?>
                     <td width="80" align="right"><? echo number_format($foreign,2); ?></td>
                     <td width="80" align="right"><? echo number_format($local,2); ?></td>
                     <td width="60" align="right"><? echo number_format($commission_percent,4); ?></td>
                     <td width="100" align="right"><? echo number_format($net_fob_value,2); ?></td>
                     <td width="100" align="right"><? echo number_format($cost_of_material_service,2); ?></td>
                     <td width="120" align="right"><? echo number_format($yarn_amount,2); ?></td>
                     <td width="120" align="right"><? echo number_format($yarn_costing,2); ?></td>
                     <td width="120" align="right"><? echo number_format($yarn_cost_percent,2); ?></td>
                     <td width="100" title="<? echo "Conversion Cost"; ?>" align="right"><? echo number_format($tot_conversion_cost,2); ?> </td>
                     <td width="120" align="right"><? echo number_format($knit_cost_dzn,2); ?></td>
                     <td width="120" align="right"><? echo number_format($tot_knit_cost,2); ?></td>
                     <td width="120" align="right"><? echo number_format($fabric_dyeing_cost_dzn,2); ?></td>
                     <td width="120" title="" align="right"><? echo number_format($fabric_dyeing_cost,2); ?> </td>
                     <td width="120" align="right"><? echo number_format($yarn_dyed_cost,2); ?></td>
                     <td width="120" align="right"><? echo number_format($all_over_cost,2); ?></td>
                     <td width="120" align="right"><? echo number_format($heat_setting_cost,2); ?></td>
                     <td width="120" title=""  align="right"> <? echo number_format($fabric_finish,2);?> </td>
                     <td width="120" align="right"><? echo number_format($washing_cost,2) ?></td>
                     <td width="100"  align="right" title="<? echo "Trim Cost"; ?>"><? echo number_format($trim_cost_per_dzn,2); ?> </td>
                     <td width="100" align="right"><? echo number_format($tot_trim_cost,2); ?></td>
                    <td width="80"  align="right" title="<? echo "Emblishment Cost"; ?>"> <? echo number_format($tot_embell_cost,2); ?></td>
                    <td width="80" align="right"><? echo number_format($print_cost,2); ?></td>
                    <td width="80"  align="right" title="<? echo "Embroidery"; ?>"><?  echo number_format($embroidery_cost,2); ?> </td>
                    <td width="80" align="right"><? echo number_format($special_cost,2); ?></td>
                    <td width="120" align="right"><? echo number_format($other_direct_expenses,2); ?></td>
                    <td width="120"  align="right" title="<? echo "Contribution Value"; ?>"><? echo number_format($contribution_value,2); ?> </td>
                    <td width="100" align="right"><? echo number_format($contribution_value_percent,2); ?></td>
                    <td width="120"  align="right" title="<? echo "CM Cost"; ?>"><? echo number_format($cm_cost,2);?> </td>
                    <td width="100" align="right" title="<? echo "Gross Profit"; ?>" ><? echo number_format($gross_profit,2); ?></td>
                    <td width="100"  align="right" title="<? echo "Gross Profit %"; ?>"><? echo number_format($gross_profit_percentage,2); ?> </td>
                    <td width="100" align="right"><? echo number_format($tot_commercial_cost,2); ?></td>
                    <td width="100"   align="right" title="<? echo "Operating Cost"; ?>"><? echo  number_format($operating_expense,2); ?> </td>
                    <td width="100" align="right"><? echo number_format($operating_profit,2); ?></td>
                    <td width="100"  align="right" title="<?  echo number_format($operating_profit_percent,2); ?>"><? echo number_format($operating_profit_percent,2); ?> </td>
                    <td width="100"  align="right" title="<?  echo number_format($depreciation_amortization,2); ?>"><? echo number_format($depreciation_amortization,2); ?> </td>
                    <td width="100" align="right"><? echo number_format($interest_expense,2); ?></td>
                    <td width="100"   align="right" title="<? echo number_format($income_tax,2); ?>"><? echo number_format($income_tax,2); ?> </td>
                    <td width="100"  align="right" title="<? echo  number_format($net_profit,2); ?>"> <? echo number_format($net_profit,2); ?></td>
               		 <td width="" align="right"><?  echo number_format($net_profit_percent,2); ?></td>
                  </tr> 
                <?
				$total_order_qty+=$order_qty_pcs;
				$grand_total_gross_fob_value+=$total_gross_fob_value;
				$grand_total_gross_fob_plancut_value+=$total_gross_fob_plancut_value;
				$total_foreign_cost+=$foreign;
				$total_local_cost+=$local;
				$total_net_fob_value+=$net_fob_value;
				$total_cost_of_material_service+=$cost_of_material_service;
				$total_yarn_amount_per_dzn+=$yarn_amount;
				$total_yarn_dyeing_cost+=$yarn_costing;
				$total_yarn_dyed_cost+=$yarn_dyed_cost;
				$total_tot_conversion_cost+=$tot_conversion_cost;
				$total_knit_cost_dzn+=$knit_cost_dzn;
				$total_tot_knit_cost+=$tot_knit_cost;
				$total_all_over_cost+=$all_over_cost;
				$total_heat_setting_cost+=$heat_setting_cost;
				$total_trim_cost_per_dzn+=$trim_cost_per_dzn;
				$total_tot_trim_cost+=$tot_trim_cost;
				$total_contribution_value+=$contribution_value;
				$total_gross_profit+=$gross_profit;
				$total_operating_profit+=$operating_profit;
				$grand_total_net_profit+=$net_profit;
				$total_fabric_dyeing_cost+=$fabric_dyeing_cost;
				$total_embell_cost+=$tot_embell_cost;
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
				$total_commercial_cost+=$tot_commercial_cost;
				$total_operating_expense+=$operating_expense;
				$total_income_tax+=$income_tax;
				$total_depreciation_amortization+=$depreciation_amortization;
				$total_interet+=$interest_expense;
				$i++;
				}
               ?>
                </table>
                <table class="rpt_table" width="5235" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <tfoot>
                     <th width="40"></th>
                     <th width="70"></th>
                     <th width="70"></th>
                     <th width="100"></th>
                     <th width="100"></th>
                     <th width="110"></th>
                     <th width="110"></th>
                     <th width="110"></th>
                     <th width="70"></th>
                     <th width="90" align="right" ><? echo number_format($total_order_qty,2); ?></th>
                     <th width="70" align="right"><? //echo number_format($total_order_qty,2); ?></th>
                     <th width="90"></th>
                     <th width="100" align="right"><? echo number_format($grand_total_gross_fob_value,2); ?></th>
                     <th width="80"><? echo number_format($total_foreign_cost,2); ?></th>
                     <th width="80" align="right"><? echo number_format($total_local_cost,2); ?></th>
                     <th width="60" align="right"><? $total_commission=$total_foreign_cost+$total_local_cost; $total_commission_percentage=$total_commission/$grand_total_gross_fob_value*100; echo number_format($total_commission_percentage,2); ?></th>
                     <th width="100" align="right"><? echo number_format($total_net_fob_value,2); ?></th>
                     <th width="100" align="right"><? echo number_format($total_cost_of_material_service,2); ?></th>
                     <th width="120" align="right"><? echo number_format($total_yarn_amount_per_dzn,2); ?></th>
                     <th width="120" align="right"><? echo number_format($total_yarn_dyeing_cost,2); ?></th>
                     <th width="120" align="right"><? $total_yarn_cost_percentage=$total_yarn_dyeing_cost/$grand_total_gross_fob_value*100; echo number_format($total_yarn_cost_percentage,2); ?></th>
                     <th width="100"  align="right"><? echo number_format($total_tot_conversion_cost,2); ?></th>
                     <th width="120" align="right"><? echo number_format($total_knit_cost_dzn,2); ?></th>
                     <th width="120" align="right"><? echo number_format($total_tot_knit_cost,2); ?></th>		
                     <th width="120" align="right"><? echo number_format($total_fabric_dyeing_cost_dzn,2); ?></th>
                     <th width="120" align="right"><? echo number_format($total_fabric_dyeing_cost,2); ?></th>
                     <th width="120" align="right"><? echo number_format($total_yarn_dyed_cost,2); ?></th>
                     <th width="120" align="right"><strong><? echo number_format($total_all_over_cost,2); ?></strong></th>
                     <th width="120" align="right"><strong><? echo number_format($total_heat_setting_cost,2); ?></strong></th>
                     <th width="120" align="right"><strong><? echo number_format($total_fabric_finish,2); ?></strong></th>
                     <th width="120" align="right"><strong> <? echo number_format($total_washing_cost,2); ?></strong></th>
                     <th width="100" align="right"><strong><? echo number_format($total_trim_cost_per_dzn,2); ?></strong></th>
                     <th width="100" align="right"><strong><? echo number_format($total_tot_trim_cost,2); ?></strong></th>
                     <th width="80" align="right"><strong><? echo number_format($total_embell_cost,2); ?></strong></th>
                     <th width="80" align="right"><? echo number_format($total_print_cost,2); ?></th>
                     <th width="80" align="right"><? echo number_format($total_embroidery_cost,2); ?></th>
                     <th width="80" align="right"><? echo number_format($total_special_cost,2); ?></th>
                     <th width="120" align="right"><? echo number_format($total_other_direct_expenses,2); ?></th>
                     <th width="120" align="right"><? echo number_format($total_contribution_value,2); ?></th>
                     <th width="100" align="right"><? $total_total_contribution_value_percentage=$total_contribution_value/$grand_total_gross_fob_value*100; echo number_format($total_total_contribution_value_percentage,2); ?></th>
                     <th width="120" align="right"><? echo number_format($total_cm_cost,2); ?></th>
                     <th width="100"><?  echo number_format($total_gross_profit,2);?></th>
                     <th width="100" align="right"><? $total_gross_profit_percentage=$total_gross_profit/$grand_total_gross_fob_value*100; echo number_format($total_gross_profit_percentage,2); ?></th>
                     <th width="100" id="total_cost_up" align="right"><? echo number_format($total_commercial_cost,2); ?></th>
                     <th width="100" align="right"><? echo number_format($total_operating_expense,2);?></th>
                     <th width="100" align="right"><?  echo number_format($total_operating_profit,2);?></th>
                     <th width="100"  align="right"><? $total_operating_profit_percentage=$total_operating_profit/$grand_total_gross_fob_value*100; echo number_format($total_operating_profit_percentage,2); //echo number_format($total_expected_profit,2);?></th>
                    <th width="100"  align="right"><? echo number_format($total_depreciation_amortization,2);?></th>
                    <th width="100"  align="right"><? echo number_format($total_interet,2); ?></th>
                    <th width="100"  align="right"><? echo number_format($total_income_tax,2); ?></th>
                    <th width="100"  align="right"><? echo number_format($grand_total_net_profit,2);?></th>
                  	<th width=""  align="right"><? $total_net_profit_percentage=$grand_total_net_profit/$grand_total_gross_fob_value*100; echo number_format($total_net_profit_percentage,2); ?></th>
                  </tfoot>
                </table>
               <input type="hidden" id="total_yarn" name="total_yarn" value="<? echo number_format($total_yarn_dyeing_cost,2);?>">
          		
            </div>
            <table>
                <tr>
                	<?
					//Graph here
					 
					?>
                </tr>
            </table>
             <table>
                <tr><td height="15"></td></tr>
            </table>
             <table>
                <tr><td height="15"></td></tr>
            </table>
           <a id="displayText" href="javascript:toggle();">Show Budget Summary</a>
            <div style="width:600px; display:none" id="yarn_summary" >
            <div id="data_panel2" align="center" style="width:500px">
                 <input type="button" value="Print Preview" class="formbutton" style="width:100px" name="print" id="print" onClick="new_window(1)" />
            </div>
           <table width="900" cellpadding="0" cellspacing="2" border="0">
              <tr>
                	<td width="600" align="left">
                    	<table width="320" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="2">
                        <caption><strong>Order Wise Budget Cost Summary</strong></caption>
                        <thead align="center">
                        <th>SL</th><th>Particulars</th><th>Amount</th><th>Percentage</th>
                        </thead>
                        <tr bgcolor="<? echo $style1; ?>">
                        <td width="20">1</td>
                        <td width="100"><strong>Gross FOB Value</strong></td><td width="120" align="right"><? echo number_format($grand_total_gross_fob_value,2);?> </td>
                        <td width="80" align="right"><? echo number_format($grand_total_gross_fob_value/$grand_total_gross_fob_value*100,2);?></td>
                        </tr>
                        <tr bgcolor="<?  echo $style; ?>">
                        <td width="20">2</td>
                        <td width="100">Less Commission</td><td align="right"><? $tot_commision_sum=$total_foreign_cost+$total_local_cost;echo number_format($tot_commision_sum,2); ?>  </td>
                        <td align="right"><? echo number_format($tot_commision_sum/$grand_total_gross_fob_value*100,2);?>  </td>
                        </tr>
                        <tr bgcolor="<? echo $style1; ?>">
                        <td width="20">3</td>
                        <td width="100"><strong>Net FOB Value</strong></td><td align="right"><? echo number_format($total_net_fob_value,2); ?></td>
                        <td align="right"> <? echo number_format($net_fob_value_summary/$grand_total_gross_fob_value*100,2);?> </td>
                        </tr>
                        <tr bgcolor="<? echo $style; ?>">
                        <td width="20">4</td>
                        <td width="100"><strong>Less Cost of Material & Service</strong>
</td><td align="right" > <? echo number_format($total_cost_of_material_service,2); ?></td>
                        <td align="right" > <? echo number_format($total_cost_of_material_service/$grand_total_gross_fob_value*100,2);?> </td>
                        </tr>
                        <tr bgcolor="<? echo $style1; ?>">
                        <td width="20">5</td>
                        <td width="100">Yarn Cost</td><td align="right"> <? echo number_format($total_yarn_dyeing_cost,2); ?></td>
                        <td align="right"> <? echo number_format($total_yarn_dyeing_cost/$grand_total_gross_fob_value*100,2);?> </td>
                        </tr>
                         <tr bgcolor="<? echo $style; ?>">
                        <td width="20">6</td>
                        <td width="100">Conversion Cost</td><td align="right"><? echo number_format($total_tot_conversion_cost,2); ?></td>
                        <td align="right"> <? echo number_format($total_tot_conversion_cost/$grand_total_gross_fob_value*100,2);?></td>
                        </tr>
                        <tr bgcolor="<? echo $style1; ?>">
                        <td width="20">7</td>
                        <td width="100">Trims Cost</td><td align="right"><? echo number_format($total_tot_trim_cost,2); ?></td>
                        <td align="right"><? echo number_format($total_tot_trim_cost/$grand_total_gross_fob_value*100,2);?> </td>
                        </tr>
                        
                        <tr bgcolor="<? echo $style; ?>">
                        <td width="20">8</td>
                        <td width="100">Embelishment Cost</td><td align="right"> <? echo number_format($total_embell_cost,2); ?></td>
                        <td align="right"> <? echo number_format($total_embell_cost/$grand_total_gross_fob_value*100,2);?></td>
                        </tr>
                        <tr bgcolor="<? echo $style1; ?>">
                        <td width="20">9</td>
                        <td width="100">Other Direct Expenses</td><td align="right"><? echo number_format($total_other_direct_expenses,2); ?></td>
                        <td align="right"> <? echo number_format($total_other_direct_expenses/$grand_total_gross_fob_value*100,2);?> </td>
                        </tr>
                        <tr bgcolor="<? echo $style; ?>">
                        <td width="20">10</td>
                        <td width="100"><strong>Contribution/Value Additions</strong></td><td align="right" ><? echo number_format($total_contribution_value,2); ?> </td>
                        <td align="right"> <? echo number_format($total_contribution_value/$grand_total_gross_fob_value*100,2);?></td>
                        </tr>
                        <tr bgcolor="<? echo $style1; ?>">
                        <td width="20">11</td>
                        <td width="100">Less CM Cost</td><td align="right"><? echo number_format($total_cm_cost,2); ?></td>
                        <td align="right"><? echo number_format($total_cm_cost/$grand_total_gross_fob_value*100,2);?> </td>
                        </tr>
                        <tr bgcolor="<? echo $style; ?>">
                        <td width="20">12</td>
                        <td width="100"><strong>Gross Profit/Loss</strong></td><td align="right"><? echo number_format($total_gross_profit,2); ?></td>
                        <td align="right"><? echo number_format($total_gross_profit/$grand_total_gross_fob_value*100,2);?> </td>
                        </tr>
                        <tr bgcolor="<? echo $style1; ?>">
                        <td width="20">13</td>
                        <td width="100">Less Commercial Cost</td><td align="right" id="cost_id"><? echo number_format($total_commercial_cost,2); ?></td>
                        <td align="right"> <? echo number_format($total_commercial_cost/$grand_total_gross_fob_value*100,2);?></td>
                        </tr>
                        <tr bgcolor="<? echo $style; ?>">
                        <td width="20">14</td>
                        <td width="100">Less Operating Expenses</td><td align="right"><? echo number_format($total_operating_expense,2); ?></td>
                        <td align="right"><? echo number_format($total_operating_expense/$grand_total_gross_fob_value*100,2);?> </td>
                        </tr>
                        <tr bgcolor="<? echo $style1; ?>">
                        <td width="20">15</td>
                        <td width="100"><strong>Operatin Profit/Loss</strong></td><td align="right" ><? echo number_format($total_operating_profit,2); ?></td>
                        <td align="right"><? echo number_format($total_operating_profit/$grand_total_gross_fob_value*100,2);?> </td>
                        </tr>
                        <tr bgcolor="<? echo $style; ?>">
                        <td width="20">16</td>
                        <td width="100">Less Depereciation & Amortization</td><td align="right"><? echo number_format($total_depreciation_amortization,2); ?></td>
                        <td align="right"> <? echo number_format($total_depreciation_amortization/$grand_total_gross_fob_value*100,2);?></td>
                        </tr>
                        <tr bgcolor="<? echo $style1; ?>">
                        <td width="20">17</td>
                        <td width="100">Less Interest</td><td align="right"><? echo number_format($total_interet,2); ?></td>
                        <td align="right"> <? echo number_format($total_interet/$grand_total_gross_fob_value*100,2);?></td>
                        </tr>
                        <tr bgcolor="<? echo $style; ?>">
                        <td width="20">18</td>
                        <td width="100">Less Income Tax</td><td align="right"><? echo number_format($total_income_tax,2); ?></td>
                        <td align="right"> <? echo number_format($total_income_tax/$grand_total_gross_fob_value*100,2);?></td>
                        </tr>
                        <tr bgcolor="<? echo $style1; ?>">
                        <td width="20">19</td>
                        <td width="100"><strong>Net Profit</strong></td><td align="right"><? echo number_format($grand_total_net_profit,2); ?></td>
                        <td align="right"> <? echo number_format($grand_total_net_profit/$grand_total_gross_fob_value*100,2);?></td>
                        </tr>
                       
                        </table>
                    </td>
                    <td colspan="5" style="min-height:800px; max-height:100%" align="center" valign="top">
                   </td>
                  </tr>  
           </table>
            </div>
		</fieldset>
	</div>
    
<?
			}
	//Budget Two end
	

echo "$total_data****$filename";
	exit(); 	 
	}
?>
