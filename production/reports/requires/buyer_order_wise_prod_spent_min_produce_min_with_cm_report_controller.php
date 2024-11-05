<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.commisions.php');
require_once('../../../includes/class4/class.trims.php');
require_once('../../../includes/class4/class.fabrics.php');
require_once('../../../includes/class4/class.yarns.php');
require_once('../../../includes/class4/class.conversions.php');
require_once('../../../includes/class4/class.others.php');
require_once('../../../includes/class4/class.emblishments.php');
require_once('../../../includes/class4/class.commercials.php');
require_once('../../../includes/class4/class.washes.php');


$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
	exit();
}
if ($action=="buyer_location_multi_select")
	{
		echo "set_multiselect('cbo_location_id','0','0','','0');\n";
		echo "set_multiselect('cbo_buyer_name','0','0','','0');\n";
		exit();
	}
if ($action=="load_drop_down_location")
{
	//echo "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name";die;
	echo create_drop_down( "cbo_location_id", 100, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "","" );     	 
	exit();
}

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
//--------------------------------------------------------------------------------------------------------------------

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $job_no;
	
	if($search_type==1 || $search_type==2 || $search_type==3) $search_cond=$job_no;
	//else if($search_type==2) $search_cond=$job_no;
	
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
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref",3=>"Order No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
					?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" value="<? //echo $search_cond;?>" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>'+'**'+'<? echo $search_type; ?>'+'**'+'<? echo $job_no; ?>'+'**'+'<? echo $po_no; ?>', 'create_job_no_search_list_view', 'search_div', 'buyer_order_wise_prod_spent_min_produce_min_with_cm_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
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
	$search_type=$data[6];
	$po_no=$data[8];
	$job_no=$data[7];
	//echo $month_id;
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	if($data[1]==0)
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
		$buyer_id_cond=" and a.buyer_name=$data[1]";
	}
	
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";
	if($search_by==2) $search_field="a.style_ref_no";
	 else if($search_by==1) $search_field="a.job_no";
	 else $search_field="b.po_number";
	//if($job_no!='') $job_no_cond="and a.job_no_prefix_num=$job_no"; else $job_no_cond="";
	//$year="year(insert_date)";
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";
	
	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(a.insert_date)=$year_id"; else $year_cond="";	
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(a.insert_date,'YYYY')";
		if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";	
	}
	if($search_type==1)
	{
		$search_filed="id,job_no_prefix_num";
	}
	else if($search_type==2)
	{
		$search_filed="po_id,po_number";
	}
	else
	{
		$search_filed="id,style_ref_no";
	}
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	$sql= "select a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,b.id as po_id,b.po_number, $year_field from wo_po_details_master a,wo_po_break_down b where   b.job_no_mst=a.job_no and a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond  order by job_no";
	
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No,Po No", "120,130,80,60,100","700","240",0, $sql , "js_set_value", "$search_filed", "", 1, "company_name,buyer_name,0,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no,po_number", "",'','0,0,0,0,0,0','') ;
	exit(); 
} // Job Search end



$tmplte=explode("**",$data);

if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;

if($action=="report_generate") 
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$report_type=str_replace("'","",$reporttype);
	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	
	$cbo_year=str_replace("'","",$cbo_year);
	$txt_style_id=str_replace("'","",$txt_style_id);
	$style_ref_no=str_replace("'","",$txt_style_no);
	$cbo_product_cat=str_replace("'","",$cbo_product_cat);
	$cbo_location_id=str_replace("'","",$cbo_location_id); 
	//$report_type=str_replace("'","",$report_type);
	$report_title=str_replace("'","",$report_title);
	
	if($txt_style_id=="") $style_id_cond=""; else $style_id_cond=" and a.id in ($txt_style_id) ";
	if($style_ref_no=="") $style_no_cond=""; else $style_no_cond=" and a.style_ref_no='$style_ref_no' ";
	if($cbo_product_cat==0) $product_cat_cond=""; else $product_cat_cond=" and a.product_category=$cbo_product_cat ";
	if($cbo_location_id==0) $location_id_cond=""; else $location_id_cond=" and c.location_id in($cbo_location_id) ";
	
	
	
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
		$buyer_id_cond=" and a.buyer_name in($cbo_buyer_name)";//.str_replace("'","",$cbo_buyer_name)
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
	
	$date_cond='';	$ex_fact_date_cond='';$est_date_cond='';$reso_date_cond='';
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
			$date_cond=" and c.production_date  between '$start_date' and '$end_date'";
			$ex_fact_date_cond=" and c.ex_factory_date  between '$start_date' and '$end_date'";
			$est_date_cond=" and c.production_date  between '$start_date' and '$end_date'";
			$reso_date_cond=" and e.pr_date  between '$start_date' and '$end_date'";
		}
		ob_start();
		//echo $report_type.'dd';die;
	
				$financial_para=array();
				$sql_std_para=sql_select("select cost_per_minute,interest_expense,income_tax,applying_period_date as from_period_date,applying_period_to_date from lib_standard_cm_entry where company_id=$company_name and status_active=1 and is_deleted=0  order by id desc");	
				foreach($sql_std_para as $row)
				{
					$applying_period_date=change_date_format($row[csf('from_period_date')],'','',1);
					$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
					$diff=datediff('d',$applying_period_date,$applying_period_to_date);
					for($j=0;$j<$diff;$j++)
					{
						$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);
						$financial_para[$newdate]['cost_per_minute']=$row[csf('cost_per_minute')];
						$financial_para[$newdate]['interest_expense']=$row[csf('interest_expense')];
						$financial_para[$newdate]['income_tax']=$row[csf('income_tax')];
					}
				
				}
				
				$sql_precost=sql_select("select a.job_no,b.id,c.costing_date,c.costing_per,d.cm_cost,d.margin_pcs_set from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c,wo_pre_cost_dtls d where  a.job_no=b.job_no_mst and a.job_no=c.job_no and a.job_no=d.job_no  and c.job_no=d.job_no  and a.company_name=$company_name and c.status_active=1 and c.is_deleted=0 $buyer_id_cond $year_cond $style_no_cond  $style_id_cond  $product_cat_cond  order by a.id desc");	
				
				foreach($sql_precost as $row)
				{
					//$costing_date=date("d-m-Y", strtotime($row[csf('costing_date')]));
					$cost_date=change_date_format($row[csf('costing_date')],'','',1);
					$pre_cost_date_arr[$row[csf('job_no')]]['cost_per_minute']=$financial_para[$cost_date]['cost_per_minute'];
					$pre_cost_date_arr[$row[csf('job_no')]]['margin_pcs_set']=$row[csf('margin_pcs_set')];
					$pre_cost_date_arr[$row[csf('job_no')]]['costing_date']=$row[csf('costing_date')];
					$pre_cost_date_arr2[$row[csf('id')]]['cm_cost']=$row[csf('cm_cost')];
				
					$pre_cost_date_arr[$row[csf('job_no')]]['interest_expense']=$financial_para[$cost_date]['interest_expense'];
					$pre_cost_date_arr[$row[csf('job_no')]]['income_tax']=$financial_para[$cost_date]['income_tax'];
					$pre_cost_date_arr[$row[csf('job_no')]]['costing_per']=$row[csf('costing_per')];
				}
				//print_r($pre_cost_date_arr);
			 $sql_result="select a.company_name as company_id,a.job_quantity,a.total_price, a.job_no, a.buyer_name, a.style_ref_no, 
				a.gmts_item_id, a.total_set_qnty as ratio,b.id as po_id,b.unit_price, b.po_number, b.pub_shipment_date,c.produced_min  as produced_min,c.efficency_min,c.total_produced  as production_quantity,c.total_target
				from wo_po_details_master a, wo_po_break_down b,pro_resource_ava_min_dtls c
				 where a.job_no=b.job_no_mst and c.order_ids=b.id   and  a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $location_id_cond $buyer_id_cond $year_cond $style_no_cond  $style_id_cond  $product_cat_cond  $est_date_cond
				  order by b.id"; 
				$data_result=sql_select($sql_result);
				$prod_detail_arr=array();
				
				$i=$z=1;$all_full_job=""; $total_po_qty=$total_fab_req_qty=$total_po_qty_pcs=$total_produced_min=$total_effecincy=$total_cm_cost_earning=$total_fob_earning=$total_cm_cost=$total_profit_loss=$total_po_total_price=0;
				$all_po_id='';
				foreach($data_result as $row)
				{
					$po_wise_prod_data_arr[$row[csf('po_id')]]['pub_date']=$row[csf('pub_shipment_date')];
					$po_wise_prod_data_arr[$row[csf('po_id')]]['job_no']=$row[csf('job_no')];
					$po_wise_prod_data_arr[$row[csf('po_id')]]['buyer']=$row[csf('buyer_name')];
					$po_wise_prod_data_arr[$row[csf('po_id')]]['season_matrix']=$row[csf('season_matrix')];
					$po_wise_prod_data_arr[$row[csf('po_id')]]['gmts_item_id']=$row[csf('gmts_item_id')];
					$po_wise_prod_data_arr[$row[csf('po_id')]]['style_ref_no']=$row[csf('style_ref_no')];
					$po_wise_prod_data_arr[$row[csf('po_id')]]['po_no']=$row[csf('po_number')];
					$po_wise_prod_data_arr[$row[csf('po_id')]]['po_quantity']=$row[csf('po_quantity')]*$row[csf('ratio')];
					$po_wise_prod_data_arr[$row[csf('po_id')]]['unit_price']=$row[csf('unit_price')];
					$po_wise_prod_data_arr[$row[csf('po_id')]]['po_total_price']=$row[csf('po_total_price')];
					$po_wise_prod_data_arr[$row[csf('po_id')]]['production_quantity']+=$row[csf('production_quantity')];
					$po_wise_prod_data_arr[$row[csf('po_id')]]['produced_min']+=$row[csf('produced_min')];
					$po_wise_prod_data_arr[$row[csf('po_id')]]['efficency_min']+=$row[csf('efficency_min')];
					$po_wise_prod_data_arr[$row[csf('po_id')]]['total_target']=$row[csf('total_target')];
					$po_wise_prod_data_arr[$row[csf('po_id')]]['po_id']=$row[csf('po_id')];
					$job_wise_prod_data_arr[$row[csf('job_no')]]['row_span']+=1;
					
					$job_wise_prod_data_arr[$row[csf('job_no')]]['job_quantity']=$row[csf('job_quantity')];
					$job_wise_prod_data_arr[$row[csf('job_no')]]['total_price']=$row[csf('total_price')];
					
					$buyer_wise_prod_data_arr[$row[csf('buyer_name')]]['production_quantity']+=$row[csf('production_quantity')];
					$buyer_wise_prod_data_arr[$row[csf('buyer_name')]]['efficency_min']+=$row[csf('efficency_min')];
					
					if($all_po_id=='') $all_po_id=$row[csf('po_id')];else  $all_po_id.=",".$row[csf('po_id')];
					if($all_full_job=="") $all_full_job="'".$row[csf('job_no')]."'"; else $all_full_job.=","."'".$row[csf('job_no')]."'";
					
				}
					$all_job_no=array_unique(explode(",",$all_full_job));
					$all_jobs="";
					foreach($all_job_no as $jno)
					{
							if($all_jobs=="") $all_jobs=$jno; else $all_jobs.=",".$jno;
					}
			
					 $poIds=implode(",",array_unique(explode(",",$all_po_id)));
						$poIds=chop($poIds,','); $po_cond_for_in=""; $po_cond_for_in2=""; 
						$po_ids=count(array_unique(explode(",",$poIds)));
						if($db_type==2 && $po_ids>1000)
						{
							$po_cond_for_in=" and (";
							$po_cond_for_in2=" and (";
							$poIdsArr=array_chunk(explode(",",$poIds),999);
							foreach($poIdsArr as $ids)
							{
								$ids=implode(",",$ids);
								$po_cond_for_in.=" b.po_breakdown_id in($ids) or"; 
								$po_cond_for_in2.=" b.id in($ids) or"; 
							}
							$po_cond_for_in=chop($po_cond_for_in,'or ');
							$po_cond_for_in.=")";
							$po_cond_for_in2=chop($po_cond_for_in2,'or ');
							$po_cond_for_in2.=")";
						}
						else
						{
							$poIds=implode(",",array_unique(explode(",",$poIds)));
							$po_cond_for_in=" and b.po_breakdown_id in($poIds)";
							$po_cond_for_in2=" and b.id in($poIds)";
						}
				
				$all_job_cond="and b.job_no_mst in($all_jobs)";
				$sql_resouce="SELECT b.id as po_id,
				d.target_per_line as target_per_hour,c.working_hour
				FROM  wo_po_break_down b,prod_resource_dtls_mast c,prod_resource_color_size d,prod_resource_dtls e
         WHERE c.id=d.dtls_id  and d.po_id=b.id and e.mast_dtl_id=c.id  and b.is_deleted=0 and b.status_active=1 and d.is_deleted=0 and d.status_active=1 $all_job_cond $reso_date_cond order by b.id";
				$result_resource=sql_select($sql_resouce);
				foreach($result_resource as $row)
				{
					$res_prod_data_arr[$row[csf('po_id')]]['target']+=$row[csf('target_per_hour')]*$row[csf('working_hour')];
					//$res_prod_data_arr[$row[csf('po_id')]]['efficency_min']+=$row[csf('efficency_min')];
				}
				
				$exfactory_res="SELECT c.po_break_down_id as po_id,
				sum(CASE WHEN c.entry_form!=85 THEN c.ex_factory_qnty ELSE 0 END)-sum(CASE WHEN c.entry_form=85 THEN c.ex_factory_qnty ELSE 0 END) as ex_factory_qnty
				FROM wo_po_details_master a, wo_po_break_down b,pro_ex_factory_mst c
         WHERE a.job_no=b.job_no_mst  and  c.po_break_down_id=b.id   and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $ex_fact_date_cond $po_cond_for_in2   $buyer_id_cond $year_cond $style_no_cond  $style_id_cond  $product_cat_cond  group by c.po_break_down_id order by c.po_break_down_id";
				$result_exf=sql_select($exfactory_res);
				foreach($result_exf as $row)
				{
					$exfactory_qty_arr[$row[csf('po_id')]]['ex_factory_qnty']=$row[csf('ex_factory_qnty')];
				}
				
		?>
        <div style="width:1800px">
		 <?
		 if($report_type==2)
		 {
		 ?>
           <br/> <br/>
         	  <div style="width:450px; margin-left:0px">


				<span id="img_id" style="display: none;">        	 			
     	 			<? $image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$company_name'","image_location"); ?>
     	 			<img src="../../<? echo $image_location; ?>" height="70" width="200">
     	 			<br>
		         </span>


			   <fieldset  style="width:100%;"  >
		     <table class="rpt_table" width="450px" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
			 <caption> Buyer Order Wise Prod Spent Min Produce Min With CM Report Summary<br>
			 <? echo $company_arr[$company_name].'<br>'.$start_date.' To '.$end_date; ?>
			 </caption>
			 <thead>
				 <th width="30"> SL</th>
				 <th width="200">Buyer </th>
				 <th width="120">Production Qty </th>
				 <th width="">Spent Minute </th>
			 </thead>
			 </table>
			   <div style="width:470px; max-height:430px; overflow-y:scroll" id="scroll_body">
			   <table class="rpt_table" width="450" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
			   
			 <?
			 $k=1;$tot_prod_summaray_qty=$tot_prod_spent_min=0;
			 foreach( $buyer_wise_prod_data_arr as $key=>$row)
			 {
			 if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			 ?>
				  <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trsumm_<? echo $k; ?>','<? echo $bgcolor;?>')" id="trsumm_<? echo $k; ?>">
					<td width="30"> <? echo $k;?></td>
					<td width="200" ><? echo $buyer_arr[$key]; ?></td>
					<td width="120" align="right" style="word-break:break-all"><p><? echo $row['production_quantity']; ?></p></td>
					<td width="" align="right" style="word-break:break-all"><p><? echo $row['efficency_min']; ?></p></td>
				 </tr>
				 <?
				 $k++;
				  $tot_prod_summaray_qty+=$row['production_quantity'];
				  $tot_prod_spent_min+=$row['efficency_min'];
			 }
				 ?>
				
				 <tr style="background:#CCCCCC">
				 <th colspan="2"> Total </th>
				  <th align="right"><? echo number_format($tot_prod_summaray_qty,2); ?>  </th>
				  <th align="right"><? echo number_format($tot_prod_spent_min,2); ?>  </th>
				 </tr>
				
			 </table>
			 </div>
			 </fieldset>
			  <?
                      echo signature_table(136, $company_name, "450px");
			?>
			 </div>
                   <?
			}
			else
			{
                   ?>
			
			
			<fieldset style="width:100%;">	
        <table id="table_header_1" class="rpt_table" width="1780" cellpadding="0" cellspacing="0" border="1" rules="all">
        <caption><strong><? echo $report_title.'<br>'.$company_arr[$company_name].'<br>'.$start_date.' To '.$end_date;
		?> </strong> </caption>
                <thead>
                    <tr>
                        <th width="30" >SL</th>
                        <th width="120">Buyer</th>
                        <th width="120">Style Ref.</th>
                        <th width="120">Order No</th>
                        <th width="120">Job NO.</th>
                        <th width="90">Avg. SMV</th>
                        <th width="90">Target</th>
                        <th width="90">Production</th> 
                        <th width="90">Spent Minute</th>
                        <th width="90">Produced Min</th>
                        <th width="90">Effeciency</th>
                        <th width="90">Performance</th>
                        <th width="90">CM Earning</th>
                        <th width="90">FOB Earning</th>
                        <th width="90">CM Cost</th>
                        <th width="90">Profit/Loss</th>
                        <th width="90">Shipment Qty</th>
                        <th width="90">Shipment Value</th>
                        <th width="">Shipment CM Earning</th>
                    </tr>
                </thead>
            </table>
            
            <div style="width:1800px; max-height:400px; overflow-y:scroll" id="scroll_body">
			<table class="rpt_table" width="1780" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
            <?
				
				
				$total_prod_sew_out_qty=$total_avaiable_spent_min=$total_produced_min=$total_tot_knit_balance_qnty=$total_tot_fin_com_qty=$total_tot_dye_balance_qnty=$total_tot_cut_balance_qnty=$total_tot_embrod_balance_qnty=$total_tot_sew_in_qty=$total_shipment_value=0;
				
				 $condition= new condition();
				 $condition->company_name("=$company_name");
				 if(str_replace("'","",$cbo_buyer_name)>0){
					  $condition->buyer_name("=$cbo_buyer_name");
				 }
				 if($style_ref_no!=''){
					 $condition->style_ref_no("='$style_ref_no'");
				 }
				 if($db_type==0 || $db_type==2)
				 {
					 if(str_replace("'","",$all_jobs)!='')
					{
						$condition->job_no("in($all_jobs)");
					}
				}
				 
				  if( str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
				  {
					  //$condition->pub_shipment_date(" between '$start_date' and '$end_date'");
				  } //$all_po_id
			 	 $condition->init();
				$other= new other($condition);
				$yarn= new yarn($condition);
				$fabric= new fabric($condition);
				$conversion= new conversion($condition);
				$trim= new trims($condition);
				$emblishment= new emblishment($condition);
				$wash= new wash($condition);
				$commercial= new commercial($condition);
				$commission= new commision($condition);
				//echo $other->getQuery(); die;
				$fabric_costing_arr=$fabric->getAmountArray_by_job_knitAndwoven_greyAndfinish();
				$yarn_costing_arr=$yarn->getJobWiseYarnAmountArray();
				$trim_arr_amount=$trim->getAmountArray_by_job();
				$conversion_costing_arr=$conversion->getAmountArray_by_job();
				$emblishment_amount_arr=$emblishment->getAmountArray_by_job();
				$wash_amount_arr=$wash->getAmountArray_by_job();
				$commercial_amount_arr=$commercial->getAmountArray_by_job();
				$commission_amount_arr=$commission->getAmountArray_by_job();
				//print_r($emblishment_amount_arr);
				$other_costing_arr=$other->getAmountArray_by_job(); 
				$j=1;
				foreach($po_wise_prod_data_arr as $po_key=>$val)
				{
					
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$job_no=$val['job_no'];
							
							$costing_per=$pre_cost_date_arr[$job_no]['costing_per'];
							$job_quantity=$job_wise_prod_data_arr[$val['job_no']]['job_quantity'];
							//echo $costing_per;
							$total_job_value=$job_wise_prod_data_arr[$job_no]['total_price'];
							$fabric_cost_knit=array_sum($fabric_costing_arr['knit']['grey'][$job_no]);
							$fabric_cost_wv=array_sum($fabric_costing_arr['woven']['grey'][$job_no]);
							$fabric_cost=$fabric_cost_knit+$fabric_cost_wv;
							$yarn_cost=$yarn_costing_arr[$job_no];
							$trim_cost=$trim_arr_amount[$job_no];
							$conversion_cost=array_sum($conversion_costing_arr[$job_no]);
							$emblishment_cost=$emblishment_amount_arr[$job_no];
							$wash_cost=$wash_amount_arr[$job_no];
							$commercial_cost=$commercial_amount_arr[$job_no];
							$commission_cost=$commission_amount_arr[$job_no];
							
							//echo $emblishment_cost.'dd'.$wash_cost;;
							$lab_cost=$other_costing_arr[$job_no]['lab_test'];
							$inspection_cost=$other_costing_arr[$job_no]['inspection'];
							$currier_cost=$other_costing_arr[$job_no]['currier_pre_cost'];
							$certificate_cost=$other_costing_arr[$job_no]['certificate_pre_cost'];
							$common_oh_cost=$other_costing_arr[$job_no]['common_oh'];
							$freight_cost=$other_costing_arr[$job_no]['freight'];
							$po_cm_cost=$other_costing_arr[$val['job_no']]['cm_cost'];
							$design_cost=$other_costing_arr[$job_no]['design_cost'];
							$studio_cost=$other_costing_arr[$job_no]['studio_cost'];
							//echo $design_cost.'='.$studio_cost;
							$interest_expense=$pre_cost_date_arr[$job_no]['interest_expense']/100;
							$income_tax=$pre_cost_date_arr[$job_no]['income_tax']/100;
							$NetFOBValue_job=$total_job_value-$commission_cost;
							//$interest_expense_job=$NetFOBValue_job*$interest_expense;
                          	//$income_tax_job=$NetFOBValue_job*$income_tax;
							$depr_amor_pre_cost=$other_costing_arr[$job_no]['depr_amor_pre_cost'];
							$total_other_cost=$lab_cost+$inspection_cost+$currier_cost+$certificate_cost+$common_oh_cost+$freight_cost+$depr_amor_pre_cost+$design_cost+$studio_cost+$po_cm_cost;
							$total_cost=$fabric_cost+$yarn_cost+$trim_cost+$conversion_cost+$emblishment_cost+$wash_cost+$commercial_cost+$commission_cost+$total_other_cost+$interest_expense_job+$income_tax_job;
						//	echo $commission_cost.'='.$wash_cost;
							$net_job_profit_value=$total_job_value-$total_cost;
							$net_job_profit_pcs= $net_job_profit_value/$job_quantity;
							
							//echo $net_job_profit_value.'='.$total_job_value.'='.$total_cost;
							
							$pre_costing_date=$pre_cost_date_arr[$val['job_no']]['costing_date'];
							$cost_per_minute=$pre_cost_date_arr[$val['job_no']]['cost_per_minute'];
							$margin_pcs=$pre_cost_date_arr[$val['job_no']]['margin_pcs_set'];
							$prod_sew_out_qty=$gmt_prod_qty_arr[$po_key]['sew_out_qty'];
							$produced_min=$val['produced_min'];//$est_prod_qty_arr[$po_key]['produced_min'];
							$avaiable_spent_min=$val['efficency_min'];//$est_prod_qty_arr[$po_key]['efficency_min'];
							$prod_sew_out_qty=$val['production_quantity'];
							$shipment_qty=$exfactory_qty_arr[$po_key]['ex_factory_qnty'];
							//echo $pre_cm_cost.'=='.$po_cm_cost/$val['po_quantity'].'<br>';
							$cm_cost_earning_per_pcs=($po_cm_cost+$net_job_profit_value)/$job_quantity;
							$cm_cost_earning=$cm_cost_earning_per_pcs*$prod_sew_out_qty;
							$tot_target=$res_prod_data_arr[$po_key]['target'];//$val['total_target'];
							$shipment_cm_earning_value=$cm_cost_earning_per_pcs*$shipment_qty;
							if($db_type==0)
							{
								$conversion_date=change_date_format($pre_costing_date, "Y-m-d", "-",1);
							}
							else
							{
								$conversion_date=change_date_format($pre_costing_date, "d-M-y", "-",1);
							}
							$usd_id=2;
							$currency_rate=set_conversion_rate($usd_id,$conversion_date );
						
						//echo $currency_rate.'<br>';
						$row_span=$job_wise_prod_data_arr[$val['job_no']]['row_span'];
							
						?>
                       <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                        	<td width="30"   ><? echo $i; ?></td>
                           
							<td width="120" ><? echo $buyer_arr[$val['buyer']]; ?></td>
							<td width="120"style="word-break:break-all"><p><? echo $val['style_ref_no']; ?></p></td>
							
							<td width="120" style="word-break:break-all"><? echo $val['po_no']; ?></td>
							<td width="120" style="word-break:break-all"><? echo $val['job_no'];  ?></td>
							<td width="90" align="right" title="Produced Min/Production(Sewing Out)"><div style="word-wrap:break-all;"><? $avg_smv=$produced_min/$prod_sew_out_qty;;echo number_format($avg_smv,2); ?></div></td>
							
                            <td width="90"  align="right" title="From Prod. Resource<? echo $row_span;?>" ><div style="word-break:break-all"><? echo number_format($tot_target); ?></div></td>
							
							
							<td width="90" title="Sewing Out<? echo $prod_sew_out_qty; ?>" align="right"> <div style="word-break:break-all"> <? echo number_format($prod_sew_out_qty,2); ?>   </div>
							<td width="90" title="Available Min"  align="right" ><? echo $avaiable_spent_min; ?>
                            <td width="90"  align="right"><div style="word-break:break-all"><?  echo $produced_min;  ?></div></td>
                            <td  width="90" align="right"  title="Produced Min/ Available Min"><? $tot_effecincy=$produced_min/$avaiable_spent_min;echo number_format($tot_effecincy,2); ?>  </td>
                            <td  width="90" align="right" title="Producton/Target">	<?  $tot_performance=$prod_sew_out_qty/$tot_target;echo number_format($tot_performance,2); ?>  </td>
                              <td  width="90" title="CM (<? echo $po_cm_cost;?>)+Net FOB Value(<? echo number_format($net_job_profit_value,4);?>)/Job Qty(<? echo $job_quantity;?>)*Production" align="right"><?  echo number_format($cm_cost_earning,2); ?></td>
                            <td width="90"  title="Production(Sewing Out=<? echo $fob_earning=$val['unit_price']; ?>)*PO Unit price=<? echo $prod_sew_out_qty; ?>"  align="right"> <div style="word-break:break-all"><? $fob_earning=$val['unit_price']*$prod_sew_out_qty; echo number_format($fob_earning,2); ?> </div>	</td>
                            <td width="90"  title="Spent Min*CPM(<? echo $cost_per_minute?>)/Conversion Rate(<? echo $currency_rate;?>)(<? echo 'Cost Date '.$pre_costing_date;?>)" align="right"> <div style="word-break:break-all"><? $cm_cost=($avaiable_spent_min*$cost_per_minute)/$currency_rate; echo number_format($cm_cost,2); ?> </div>
                            </td>
							  <td width="90" title="CM Cost Earning-CM Cost" align="right"> <div style="word-break:break-all"><? $profit_loss=$cm_cost_earning-$cm_cost;echo number_format($profit_loss,2); ?> </div>
                            </td>
							  <td width="90"  title="Ex Fact Qty" align="right"> <div style="word-break:break-all"><? echo number_format($shipment_qty,2); ?> </div>
                            </td>
							 <td width="90"  align="right" title="<? echo 'Po Unit Price='. $val['unit_price'];?>"> <div style="word-break:break-all"><? $shipment_value=$shipment_qty*$val['unit_price'];
							 echo number_format($shipment_value,2); ?> </div> 
                            </td>
                          <td width=""  title="CM Earning Per Pcs(<? echo number_format($cm_cost_earning_per_pcs,4);?>)*Shipment Qty"  align="right"> <div style="word-break:break-all"><? echo number_format($shipment_cm_earning_value,2); ?> </div></td>
                        </tr>
                            <?
							$i++;
							
						
							$total_prod_sew_out_qty+=$prod_sew_out_qty;
							$total_avaiable_spent_min+=$avaiable_spent_min;
							$total_tot_sew_out_qty+=$tot_sew_out_qty;
							//$total_tot_wash_balance_qnty+=$tot_wash_balance_qnty;
							$total_produced_min+=$produced_min;
							$total_effecincy+=$tot_effecincy;
							$total_cm_cost_earning+=$cm_cost_earning;
							$total_fob_earning+=$fob_earning;
							$total_cm_cost+=$cm_cost;
							$total_profit_loss+=$profit_loss;
							$total_po_qty_pcs+=$shipment_qty;
							$total_po_total_price+=$shipment_value;
							$total_shipment_value+=$shipment_cm_earning_value;
							//$total_tot_ship_prod_qty+=$ship_prod_qty;
					
				}
							?>
            </table>
			 </div>
        <table class="rpt_table" width="1780" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tfoot>
                    <th width="30">&nbsp; </th>
                    <th width="120">&nbsp;</th>
                    <th width="120">&nbsp;  </th>
                    <th width="120">&nbsp;  </th>
                    <th  width="120">&nbsp; </th>
                    <th width="90">&nbsp; </th>
                    <th width="90">&nbsp; </th>
                    <th width="90" id="value_total_sew_out"><? echo number_format($total_prod_sew_out_qty,2); ?>  </th>
                    
                    <th width="90" id="value_total_spent_min"><? echo number_format($total_avaiable_spent_min,2); ?></th>
                    <th  width="90" id="value_total_produced_min"><? echo number_format($total_produced_min,2); ?> </th>
                    <th width="90">&nbsp; </th>
                    <th width="90">&nbsp; </th>
                    <th width="90" id="value_total_cm_cost_earning"><? echo number_format($total_cm_cost_earning,2); ?> </th>
                    <th width="90" id="value_total_fob_earning"><? echo number_format($total_fob_earning,2); ?> </th>
                    <th  width="90" id="value_total_cm_cost"><? echo number_format($total_cm_cost,2); ?> </th>
                    <th width="90" id="value_total_profit_loss"> <? echo number_format($total_profit_loss,2); ?> </th>
                    <th width="90" id="value_total_po_qty_pcs"> <? echo number_format($total_po_qty_pcs,2); ?> </th>
                    <th width="90" id="value_total_po_value"> <? echo number_format($total_po_total_price,2); ?> </th>
                    <th width=""  id="value_total_shipment_value"> <? echo number_format($total_shipment_value,2); ?> </th>
                   
             </tfoot>
          </table>
		 </fieldset>
		 <?
		 }
		 ?>
            </div>
            <?	
	
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
    echo "$html****$filename****$report_type"; 
	exit();	
}


if($action=="report_generate2") 
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$report_type=str_replace("'","",$reporttype);
	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	
	$cbo_year=str_replace("'","",$cbo_year);
	$txt_style_id=str_replace("'","",$txt_style_id);
	$style_ref_no=str_replace("'","",$txt_style_no);
	$cbo_product_cat=str_replace("'","",$cbo_product_cat);
	$cbo_location_id=str_replace("'","",$cbo_location_id); 
	//$report_type=str_replace("'","",$report_type);
	$report_title=str_replace("'","",$report_title);
	
	if($txt_style_id=="") 	$style_id_cond=""; 		else $style_id_cond		=" and a.id in ($txt_style_id) ";
	if($style_ref_no=="") 	$style_no_cond=""; 		else $style_no_cond		=" and a.style_ref_no='$style_ref_no' ";
	if($cbo_product_cat==0) $product_cat_cond=""; 	else $product_cat_cond	=" and a.product_category=$cbo_product_cat ";
	if($cbo_location_id==0) $location_id_cond=""; 	else $location_id_cond	=" and c.location_id in($cbo_location_id) ";
	
	
	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond =" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; 
			else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name in($cbo_buyer_name)";
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
	
	$date_cond='';	$ex_fact_date_cond='';$est_date_cond='';$reso_date_cond='';$pre_cost_date_cond='';
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		if($db_type==0)
		{
			$start_date	= change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
			$end_date	= change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$start_date	= change_date_format(str_replace("'","",$txt_date_from),"","",1);
			$end_date	= change_date_format(str_replace("'","",$txt_date_to),"","",1);
		}
		$date_cond			= " and c.production_date  between '$start_date' and '$end_date'";
		$ex_fact_date_cond	= " and c.ex_factory_date  between '$start_date' and '$end_date'";
		$est_date_cond		= " and c.production_date  between '$start_date' and '$end_date'";
		$reso_date_cond		= " and e.pr_date  between '$start_date' and '$end_date'";
		$pre_cost_date_cond	= " and c.costing_date  between '$start_date' and '$end_date'";
	}
	
	//echo $report_type.'dd';die;
	
	
	$financial_para=array();
	$sql_std_para=sql_select("select cost_per_minute,interest_expense,income_tax,applying_period_date as from_period_date,applying_period_to_date from lib_standard_cm_entry where company_id=$company_name and status_active=1 and is_deleted=0  order by id desc");	
	foreach($sql_std_para as $row)
	{
		$date_key=date("Y-m",strtotime($row[csf("from_period_date")]));
			
		$financial_para2[$date_key]['cost_per_minute']	=$row[csf('cost_per_minute')];
		$financial_para2[$date_key]['interest_expense']	=$row[csf('interest_expense')];
		$financial_para2[$date_key]['income_tax']		=$row[csf('income_tax')];
		/*$applying_period_date=change_date_format($row[csf('from_period_date')],'','',1);
		$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
		$diff=datediff('d',$applying_period_date,$applying_period_to_date);
		for($j=0;$j<$diff;$j++)
		{
			$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);
			$financial_para[$newdate]['cost_per_minute']=$row[csf('cost_per_minute')];
			$financial_para[$newdate]['interest_expense']=$row[csf('interest_expense')];
			$financial_para[$newdate]['income_tax']=$row[csf('income_tax')];
		}*/
	
	}
	unset($sql_std_para);
	//echo "<pre>";
	//print_r($financial_para2['2018-05']);die;
	
	$sql_precost=sql_select("select a.job_no, b.id,c.costing_date, c.costing_per, d.cm_cost, d.margin_pcs_set 
	from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c, wo_pre_cost_dtls d 
	where  a.job_no=b.job_no_mst and a.job_no=c.job_no and a.job_no=d.job_no  and c.job_no=d.job_no  and a.company_name=$company_name and c.status_active=1 and c.is_deleted=0 $buyer_id_cond $year_cond $style_no_cond  $style_id_cond  $product_cat_cond 
	order by a.id desc"); //$pre_cost_date_cond
	foreach($sql_precost as $row)
	{
		//$costing_date=date("d-m-Y", strtotime($row[csf('costing_date')]));
		//$cost_date = change_date_format($row[csf('costing_date')],'','',1);
		//$pre_cost_date_arr[$row[csf('job_no')]]['cost_per_minute']	=$financial_para[$cost_date]['cost_per_minute'];
		//$pre_cost_date_arr[$row[csf('job_no')]]['interest_expense']	=$financial_para[$cost_date]['interest_expense'];
		//$pre_cost_date_arr[$row[csf('job_no')]]['income_tax']		=$financial_para[$cost_date]['income_tax'];
		
		//$date_key=date("Y-m",strtotime($row[csf("costing_date")]));
		
		
		$pre_cost_date_arr[$row[csf('job_no')]]['margin_pcs_set']	=$row[csf('margin_pcs_set')];
		$pre_cost_date_arr[$row[csf('job_no')]]['costing_date']		=$row[csf('costing_date')];
		$pre_cost_date_arr[$row[csf('job_no')]]['costing_per']		=$row[csf('costing_per')];
		
		$pre_cost_date_arr2[$row[csf('id')]]['cm_cost']				=$row[csf('cm_cost')];
	}
	unset($sql_precost);
	//echo "<pre>";
	//print_r($pre_cost_date_arr3['FAL-18-00056']);die;
	
	$sql_result="select a.company_name as company_id, a.job_quantity, a.total_price, a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id, a.total_set_qnty as ratio, b.id as po_id, b.unit_price, b.po_number, b.pub_shipment_date, c.produced_min  as produced_min, c.efficency_min, c.total_produced  as production_quantity, c.total_target, c.production_date
	from wo_po_details_master a, wo_po_break_down b, pro_resource_ava_min_dtls c
	where a.job_no=b.job_no_mst and c.order_ids=b.id and a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $location_id_cond $buyer_id_cond $year_cond $style_no_cond $style_id_cond $product_cat_cond  $est_date_cond
	order by b.id"; 
	$data_result=sql_select($sql_result);
	$prod_detail_arr=array();
	
	$i=$z=1; 
	$all_full_job=""; 
	$total_po_qty = $total_fab_req_qty = $total_po_qty_pcs = $total_produced_min = $total_effecincy = $total_cm_cost_earning = $total_fob_earning = $total_cm_cost = $total_profit_loss = $total_po_total_price = 0;
	$all_po_id='';
	foreach($data_result as $row)
	{
		// $po_wise_prod_data_arr[$row[csf('po_id')]]['pub_date']	=$row[csf('pub_shipment_date')];
		$po_wise_prod_data_arr[$row[csf('po_id')]]['production_date']	=$row[csf('production_date')];
		$po_wise_prod_data_arr[$row[csf('po_id')]]['job_no']	=$row[csf('job_no')];
		$po_wise_prod_data_arr[$row[csf('po_id')]]['buyer']		=$row[csf('buyer_name')];
		$po_wise_prod_data_arr[$row[csf('po_id')]]['po_no']			=$row[csf('po_number')];
		$po_wise_prod_data_arr[$row[csf('po_id')]]['unit_price']	=$row[csf('unit_price')];
		$po_wise_prod_data_arr[$row[csf('po_id')]]['production_quantity']+=$row[csf('production_quantity')];
		$po_wise_prod_data_arr[$row[csf('po_id')]]['produced_min']	+=$row[csf('produced_min')];
		$po_wise_prod_data_arr[$row[csf('po_id')]]['efficency_min']	+=$row[csf('efficency_min')];
		
		
		// $po_wise_prod_data_arr[$row[csf('po_id')]]['season_matrix']	=$row[csf('season_matrix')];
		// $po_wise_prod_data_arr[$row[csf('po_id')]]['gmts_item_id']	=$row[csf('gmts_item_id')];
		// $po_wise_prod_data_arr[$row[csf('po_id')]]['style_ref_no']	=$row[csf('style_ref_no')];
		// $po_wise_prod_data_arr[$row[csf('po_id')]]['po_quantity']	=$row[csf('po_quantity')]*$row[csf('ratio')];
		// $po_wise_prod_data_arr[$row[csf('po_id')]]['po_total_price']=$row[csf('po_total_price')];
		// $po_wise_prod_data_arr[$row[csf('po_id')]]['total_target']	=$row[csf('total_target')];
		// $po_wise_prod_data_arr[$row[csf('po_id')]]['po_id']			=$row[csf('po_id')];
		
		$job_wise_prod_data_arr[$row[csf('job_no')]]['row_span']	+=1;
		
		$job_wise_prod_data_arr[$row[csf('job_no')]]['job_quantity']=$row[csf('job_quantity')];
		$job_wise_prod_data_arr[$row[csf('job_no')]]['total_price']	=$row[csf('total_price')];
		
		$buyer_wise_prod_data_arr[$row[csf('buyer_name')]]['production_quantity']	+=$row[csf('production_quantity')];
		$buyer_wise_prod_data_arr[$row[csf('buyer_name')]]['efficency_min']			+=$row[csf('efficency_min')];
		
		if($all_po_id=='') $all_po_id=$row[csf('po_id')];	
		else  $all_po_id.=",".$row[csf('po_id')];
		
		if($all_full_job=="") $all_full_job="'".$row[csf('job_no')]."'"; 	
		else $all_full_job.=","."'".$row[csf('job_no')]."'";
	}
	unset($data_result);
	
	
	
	$all_job_no=array_unique(explode(",",$all_full_job));
	$all_jobs="";
	foreach($all_job_no as $jno)
	{
		if($all_jobs=="") $all_jobs=$jno; else $all_jobs.=",".$jno;
	}

	$poIds=implode(",",array_unique(explode(",",$all_po_id)));
	$poIds=chop($poIds,','); $po_cond_for_in=""; $po_cond_for_in2=""; 
	$po_ids=count(array_unique(explode(",",$poIds)));
	if($db_type==2 && $po_ids>1000)
	{
		$po_cond_for_in		=" and (";
		$po_cond_for_in2	=" and (";
		$poIdsArr	=array_chunk(explode(",",$poIds),999);
		foreach($poIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$po_cond_for_in		.=" b.po_breakdown_id in($ids) or"; 
			$po_cond_for_in2	.=" b.id in($ids) or"; 
		}
		$po_cond_for_in	=chop($po_cond_for_in,'or ');
		$po_cond_for_in		.=")";
		$po_cond_for_in2	=chop($po_cond_for_in2,'or ');
		$po_cond_for_in2	.=")";
	}
	else
	{
		$poIds	=implode(",",array_unique(explode(",",$poIds)));
		$po_cond_for_in		=" and b.po_breakdown_id in($poIds)";
		$po_cond_for_in2	=" and b.id in($poIds)";
	}
	
	$all_job_cond	="and b.job_no_mst in($all_jobs)";
	
	$sql_resouce="SELECT b.id as po_id, d.target_per_line as target_per_hour, c.working_hour
	FROM  wo_po_break_down b, prod_resource_dtls_mast c, prod_resource_color_size d, prod_resource_dtls e
	WHERE c.id=d.dtls_id  and d.po_id=b.id and e.mast_dtl_id=c.id  and b.is_deleted=0 and b.status_active=1 and d.is_deleted=0 and d.status_active=1 $all_job_cond $reso_date_cond order by b.id";
	$result_resource=sql_select($sql_resouce);
	foreach($result_resource as $row)
	{
		$res_prod_data_arr[$row[csf('po_id')]]['target']	+=$row[csf('target_per_hour')]*$row[csf('working_hour')];
		//$res_prod_data_arr[$row[csf('po_id')]]['efficency_min']+=$row[csf('efficency_min')];
	}
	unset($result_resource);
	
	$exfactory_res="SELECT c.po_break_down_id as po_id,
	sum(CASE WHEN c.entry_form!=85 THEN c.ex_factory_qnty ELSE 0 END)-sum(CASE WHEN c.entry_form=85 THEN c.ex_factory_qnty ELSE 0 END) as ex_factory_qnty
	FROM wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c
	WHERE a.job_no=b.job_no_mst  and  c.po_break_down_id=b.id   and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $ex_fact_date_cond $po_cond_for_in2 $buyer_id_cond $year_cond $style_no_cond  $style_id_cond  $product_cat_cond  group by c.po_break_down_id order by c.po_break_down_id";
	$result_exf=sql_select($exfactory_res);
	foreach($result_exf as $row)
	{
		$exfactory_qty_arr[$row[csf('po_id')]]['ex_factory_qnty']=$row[csf('ex_factory_qnty')];
	}
	unset($result_exf);
	
	ob_start();
	?>
	<div style="width:1020px">
	<fieldset style="width:100%;">	
		<table id="table_header_1" class="rpt_table" width="1000" cellpadding="0" cellspacing="0" border="1" rules="all">
		<caption><strong><? echo $report_title.'<br>'.$company_arr[$company_name].'<br>'.$start_date.' To '.$end_date;
		?> </strong> </caption>
		<thead>
			<tr>
				<th width="30" >SL</th>
				<th width="120">Buyer</th>
				<th width="90">Avg. SMV</th>
				<th width="90">Effeciency</th>
				<th width="90">QC Pass Qty</th>
				<th width="90">Spent Minute</th>
				<th width="90">Produced Min</th>
				<th width="90">CM Earning</th>
				<th width="90">CM %</th>
				<th width="90">FOB on Sweing Qty</th>
				<th width="120">Cost Spend Minutes</th>
			</tr>
		</thead>
		</table>
		<div style="width:1020px; max-height:400px; overflow-y:scroll" id="scroll_body">
		<table class="rpt_table" width="1000" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
		<?
		$total_prod_sew_out_qty=$total_avaiable_spent_min=$total_produced_min=$total_tot_knit_balance_qnty=$total_tot_fin_com_qty=$total_tot_dye_balance_qnty=$total_tot_cut_balance_qnty=$total_tot_embrod_balance_qnty=$total_tot_sew_in_qty=$total_shipment_value=0;
		
		$condition= new condition();
		$condition->company_name("=$company_name");
		if(str_replace("'","",$cbo_buyer_name)>0){
			$condition->buyer_name("=$cbo_buyer_name");
		}
		if($style_ref_no!=''){
			$condition->style_ref_no("='$style_ref_no'");
		}
		if($db_type==0 || $db_type==2)
		{
			if(str_replace("'","",$all_jobs)!='')
			{
				$condition->job_no("in($all_jobs)");
			}
		}
		
		$condition->init();
		$other	= new other($condition);
		$yarn	= new yarn($condition);
		$fabric	= new fabric($condition);
		$conversion	= new conversion($condition);
		$trim	= new trims($condition);
		$emblishment= new emblishment($condition);
		$wash	= new wash($condition);
		$commercial	= new commercial($condition);
		$commission	= new commision($condition);
		//echo $other->getQuery(); die;
		$fabric_costing_arr		= $fabric->getAmountArray_by_job_knitAndwoven_greyAndfinish();
		$yarn_costing_arr		= $yarn->getJobWiseYarnAmountArray();
		$trim_arr_amount		= $trim->getAmountArray_by_job();
		$conversion_costing_arr	= $conversion->getAmountArray_by_job();
		$emblishment_amount_arr	= $emblishment->getAmountArray_by_job();
		$wash_amount_arr		= $wash->getAmountArray_by_job();
		$commercial_amount_arr	= $commercial->getAmountArray_by_job();
		$commission_amount_arr	= $commission->getAmountArray_by_job();
		//print_r($emblishment_amount_arr);
		$other_costing_arr		= $other->getAmountArray_by_job(); 
		$j=1;
		
		foreach($po_wise_prod_data_arr as $po_key=>$val)
		{
			$date_key=date("Y-m",strtotime($val["production_date"]));
			
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			
			$job_no=$val['job_no'];
			
			$job_quantity	 	= $job_wise_prod_data_arr[$job_no]['job_quantity'];
			$total_job_value 	= $job_wise_prod_data_arr[$job_no]['total_price'];
			
			$fabric_cost_knit 	= array_sum($fabric_costing_arr['knit']['grey'][$job_no]);
			$fabric_cost_wv		= array_sum($fabric_costing_arr['woven']['grey'][$job_no]);
			
			$fabric_cost		= $fabric_cost_knit+$fabric_cost_wv;
			$yarn_cost			= $yarn_costing_arr[$job_no];
			$trim_cost			= $trim_arr_amount[$job_no];
			$conversion_cost	= array_sum($conversion_costing_arr[$job_no]);
			$emblishment_cost	= $emblishment_amount_arr[$job_no];
			$wash_cost			= $wash_amount_arr[$job_no];
			$commercial_cost	= $commercial_amount_arr[$job_no];
			$commission_cost	= $commission_amount_arr[$job_no];
			
			$lab_cost			= $other_costing_arr[$job_no]['lab_test'];
			$inspection_cost	= $other_costing_arr[$job_no]['inspection'];
			$currier_cost		= $other_costing_arr[$job_no]['currier_pre_cost'];
			$certificate_cost 	= $other_costing_arr[$job_no]['certificate_pre_cost'];
			$common_oh_cost		= $other_costing_arr[$job_no]['common_oh'];
			$freight_cost		= $other_costing_arr[$job_no]['freight'];
			$po_cm_cost			= $other_costing_arr[$job_no]['cm_cost'];
			$design_cost		= $other_costing_arr[$job_no]['design_cost'];
			$studio_cost		= $other_costing_arr[$job_no]['studio_cost'];
			$depr_amor_pre_cost	= $other_costing_arr[$job_no]['depr_amor_pre_cost'];
			
			$interest_expense	= $financial_para2[$date_key]['interest_expense']/100;
			$income_tax			= $financial_para2[$date_key]['income_tax']/100;
			$NetFOBValue_job	= $total_job_value-$commission_cost;
			//$interest_expense_job=$NetFOBValue_job*$interest_expense;
			//$income_tax_job=$NetFOBValue_job*$income_tax;
			
			$total_other_cost	= $lab_cost+$inspection_cost+$currier_cost+$certificate_cost+$common_oh_cost+$freight_cost+$depr_amor_pre_cost+$design_cost+$studio_cost+$po_cm_cost;
			$total_cost			= $fabric_cost+$yarn_cost+$trim_cost+$conversion_cost+$emblishment_cost+$wash_cost+$commercial_cost+$commission_cost+$total_other_cost+$interest_expense_job+$income_tax_job;
			
			$net_job_profit_value = $total_job_value-$total_cost;
			
			$produced_min		= $val['produced_min'];
			
			//$pre_costing_date	= $pre_cost_date_arr[$val['job_no']]['costing_date'];
			$pre_costing_date	= $date_key."-01";
			
			$cost_per_minute	= $financial_para2[$date_key]['cost_per_minute'];
			
			$avaiable_spent_min = $val['efficency_min'];
			$prod_sew_out_qty	= $val['production_quantity'];
			
			$cm_cost_earning_per_pcs=($po_cm_cost+$net_job_profit_value)/$job_quantity;
			$cm_cost_earning	= $cm_cost_earning_per_pcs*$prod_sew_out_qty;
			/*if($db_type==0)
			{
				$conversion_date = change_date_format($pre_costing_date, "Y-m-d", "-",1);
			}
			else
			{
				$conversion_date = change_date_format($pre_costing_date, "d-M-y", "-",1);
			}*/
			
			
			
			if($avaiable_spent_min>0  || $produced_min !=0)
			{
				
				$usd_id=2;
				//$currency_rate	= set_conversion_rate($usd_id,$conversion_date );
				$currency_rate	= set_conversion_rate($usd_id,$pre_costing_date );
				
				$buyer_wise_arr[$val['buyer']]['avg_smv']+=($prod_sew_out_qty=="0")? $prod_sew_out_qty : $produced_min/$prod_sew_out_qty;
				
				$buyer_wise_arr[$val['buyer']]['tot_effecincy']+=$produced_min/$avaiable_spent_min;
				$buyer_wise_arr[$val['buyer']]['qc_pass_sew_out_qty'] += $prod_sew_out_qty;
				$buyer_wise_arr[$val['buyer']]['avaiable_spent_min'] += $avaiable_spent_min;
				$buyer_wise_arr[$val['buyer']]['produced_min'] += $produced_min;
				$buyer_wise_arr[$val['buyer']]['cm_cost_earning'] += $cm_cost_earning;
				$buyer_wise_arr[$val['buyer']]['price_per_qty'] += $val['unit_price']*$prod_sew_out_qty;
				$buyer_wise_arr[$val['buyer']]['fob_sew_qty']+=$val['unit_price']*$prod_sew_out_qty;
				$buyer_wise_arr[$val['buyer']]['cost_spend_minutes'] =($cost_per_minute)/$currency_rate;
			}
		}
		
		
		foreach($buyer_wise_arr as $buyer_id=>$buyer_data)
		{	
			?>
			<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
				<td width="30"><? echo $i; ?></td>
				<td width="120"><? echo $buyer_arr[$buyer_id]; ?></td>
				
				<td width="90" align="right" title="Produced Min/Production(Sewing Out)"><? 
					echo number_format($buyer_data['avg_smv'],2);
					?></td>
                <td  width="90" align="right"  title="Produced Min/ Available Min"><? 
					echo number_format($buyer_data['tot_effecincy'],2); 
					?></td>
				<td width="90" title="" align="right"><? 
				echo number_format($buyer_data['qc_pass_sew_out_qty'],2);
				?></td>
				<td width="90" title="Available Min"  align="right" ><? 
				echo $buyer_data['avaiable_spent_min']; 
				?></td>
				<td width="90"  align="right"><?  echo $buyer_data['produced_min'];  ?></td>
				<td  width="90" title="" align="right"><?  
				echo number_format($buyer_data['cm_cost_earning'],2); 
				?></td>
				<td  width="90" align="right"><?
				 ($buyer_data['price_per_qty']>0)? $cmPercent = ($buyer_data['cm_cost_earning'] / $buyer_data['price_per_qty'])*100 : $cmPercent = 0 ;
				echo number_format( $cmPercent,2);
				?></td>
				<td width="90"  title=""  align="right"><? 
				echo number_format($buyer_data['fob_sew_qty'],2); 
				?></td>
				<td width="120"  title="" align="right"><? 
				echo number_format($buyer_data['cost_spend_minutes'],4); 
				?></td>
			</tr>
			<?
			$i++;
			
			//$total_prod_sew_out_qty		+= $buyer_data['qc_pass_sew_out_qty'];
			//$total_avaiable_spent_min	+= $buyer_data['avaiable_spent_min'];
			//$total_produced_min			+= $buyer_data['produced_min'];
			//$total_cm_cost_earning		+= $buyer_data['cm_cost_earning'];
			//$total_fob_earning			+= $buyer_data['fob_sew_qty'];
		}
		?>
		</table>
		</div>
		<table class="rpt_table" width="1000" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
			<tfoot>
				<th width="30">&nbsp;</th>
				<th width="120">&nbsp;</th>
				<th width="90">&nbsp;</th>
				<th width="90">&nbsp;</th>
				<th width="90" align="right" id="value_total_prod_sew_out_qty"><? //echo number_format($total_prod_sew_out_qty,2); ?></th>
				<th width="90" align="right" id="value_total_avaiable_spent_min"><? //echo number_format($total_avaiable_spent_min,2); ?></th>
                <th width="90" align="right" id="value_total_produced_min"><? //echo number_format($total_produced_min,2); ?> </th>
				<th width="90" align="right" id="value_total_cm_cost_earning"><? //echo number_format($total_cm_cost_earning,2); ?> </th>
                <th width="90">&nbsp;</th>
				<th width="90" align="right" id="value_total_fob_earning"><? //echo number_format($total_fob_earning,2); ?> </th>
				<th width="120">&nbsp;</th>
			</tfoot>
		</table>
	</fieldset>
	<?
                      echo signature_table(136, $company_name, "450px");
			?>
	</div>
	<?	
	
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
    
	exit("$html****$filename****$report_type");	
}

?>