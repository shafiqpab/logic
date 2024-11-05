<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];

if($db_type==2) $select_date=" to_char(a.insert_date,'YYYY')";
else if ($db_type==0) $select_date=" year(a.insert_date)";

if($action=="print_button_variable_setting")
{
    $print_report_format_arr = return_library_array("select format_id,format_id from lib_report_template where template_name =$data and module_id=11 and report_id=190 and is_deleted=0 and status_active=1","format_id","format_id");
    echo "print_report_button_setting('".implode(',',$print_report_format_arr)."');\n";
    exit(); 
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0);
	exit();
}

if ($action=="report_button_setting")
{
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=11 and report_id=62 and is_deleted=0 and status_active=1");
	echo "print_report_button_setting('$print_report_format');\n"; 
}

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $txt_job_no;
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
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" value="<? echo $txt_job_no?>" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>'+'**'+'<? echo $type; ?>', 'create_job_no_search_list_view', 'search_div', 'style_production_summary_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div id="search_div"></div>
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
	$type_id=$data[6];
	//echo $type_id;
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and a.buyer_name=$data[1]";
	
	$search_by=$data[2];
	$search_string=trim($data[3]);
	$search_cond="";
	if($search_string!="")
	{
		if($search_by==2) $search_cond=" and a.style_ref_no='$data[3]'";
		elseif($search_by==1) $search_cond="and a.job_no_prefix_num=$data[3]";
	}
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
	//if($month_id!=0) $month_cond=" and month(insert_date)=$month_id"; else $month_cond="";
	if($type_id==1) $job_cond="id,job_no_prefix_num";
	else if($type_id==2) $job_cond="id,style_ref_no";
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	if($type_id==1 || $type_id==2 )
	{
		$sql= "select a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, $year_field from wo_po_details_master a where a.garments_nature=100 and a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id $search_cond $buyer_id_cond $year_cond  order by a.id DESC";
	
		echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "$job_cond", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','') ;
	}
	else
	{
		  $sql= "select a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name,a.style_ref_no, $year_field,b.id,b.po_number from wo_po_details_master a,wo_po_break_down b where  a.job_no=b.job_no_mst and a.garments_nature=100 and a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id $search_cond $buyer_id_cond $year_cond  order by a.id DESC";
		echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No,PO No", "120,130,80,60,100","700","240",0, $sql , "js_set_value", "id,po_number", "", 1, "company_name,buyer_name,0,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no,po_number", "",'','0,0,0,0,0,0','') ;
	}
	exit(); 
} // Job Search end

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
			
	$reporttype=str_replace("'","",$reporttype);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_wo_company_name=str_replace("'","",$cbo_wo_company_name);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$txt_order=str_replace("'","",$txt_order);
	$txt_order_id=str_replace("'","",$txt_order_id);
	$job_no=str_replace("'","",$txt_job_no);
	$job_no_id=str_replace("'","",$txt_job_no_id);
	$year_id=str_replace("'","",$cbo_year);
	$ship_status=str_replace("'","",$cbo_ship_status);

	if($type==1)
	{
		if($cbo_company_name==0) $company_name_cond=""; else $company_name_cond=" and a.company_name='$cbo_company_name' ";
		if($cbo_wo_company_name==0) $wo_company_name_cond=""; else $wo_company_name_cond=" and c.serving_company='$cbo_wo_company_name' ";
		
		if($cbo_buyer_name==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="")
				{
					$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
				}
				else  $buyer_id_cond="";
			}
			else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
		}
				
		if($db_type==0)
		{
			if($year_id!=0) $year_cond=" and year(a.insert_date)=$year_id"; else $year_cond="";	
		}
		else if($db_type==2)
		{
			$year_field_con=" and to_char(a.insert_date,'YYYY')";
			if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";	
		}
		
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
		}
		$job_style_cond="";
		if(trim(str_replace("'","",$txt_style_ref))!="") $job_style_cond=" and a.style_ref_no = '".trim(str_replace("'","",$txt_style_ref))."'";
				
		$order_cond="";
		if(trim(str_replace("'","",$txt_order))!="")
		{
			if(str_replace("'","",$txt_order_id)!="")
			{
				$order_cond=" and b.id in(".str_replace("'","",$txt_order_id).")";
			}
			else
			{
				$order_cond=" and b.po_number = '".trim(str_replace("'","",$txt_order))."'";
			}
		}
		$job_cond="";
		if(trim(str_replace("'","",$job_no))!="")
		{
			if(str_replace("'","",$job_no_id)!="")
			{
				$job_cond=" and a.id in(".str_replace("'","",$job_no_id).")";
			}
			else
			{
				$job_cond=" and a.job_no_prefix_num = '".trim(str_replace("'","",$job_no))."'";
			}
		}
		
		$ship_status_cond="";
		if($ship_status==1) $ship_status_cond="and b.shiping_status in (1,2)"; else if($ship_status==2) $ship_status_cond="and b.shiping_status in (3)";
		
		ob_start();
		
		$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
		$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
		$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where file_type=1 and form_name='knit_order_entry'",'master_tble_id','image_location');
		
		$sql="SELECT a.company_name, a.buyer_name, a.style_ref_no, a.gauge, a.job_no_prefix_num as job_prefix, a.job_no, a.order_uom, a.total_set_qnty as ratio, b.id as po_id, b.po_number, b.po_quantity, b.plan_cut, b.excess_cut, b.shiping_status, b.pub_shipment_date, c.production_type, c.production_quantity as production_quantity, c.re_production_qty,c.serving_company,c.production_source
		
		 from wo_po_details_master a, wo_po_break_down b, pro_garments_production_mst c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.garments_nature=100 and c.production_type in (1,3,4,5,7,8,11,67,111,112,114) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_name_cond $wo_company_name_cond $buyer_id_cond $date_cond $job_style_cond $order_cond $job_cond $year_cond $ship_status_cond order by a.id ASC";
		// echo $sql;
		$sql_result=sql_select($sql);	
		$po_arr=array(); 
		$production_arr=array(); 
		$po_arr_sub=array(); 
		$chk_arr=array(); 
		$production_arr_sub=array();
		$production_summary_arr=array();
		$production_plan_cut_summary_arr=array();
		$tot_rows=0; 
		$poIds='';
		foreach($sql_result as $row)
		{
			$tot_rows++;
			$poIds.=$row[csf("po_id")].",";
			if($row[csf("production_source")]==1)
			{
				$po_arr[$row[csf("po_id")]]=$row[csf("buyer_name")].'___'.$row[csf("style_ref_no")].'___'.$row[csf("gauge")].'___'.$row[csf("job_no_prefix_num")].'___'.$row[csf("job_no")].'___'.$row[csf("po_number")].'___'.$row[csf("po_quantity")].'___'.$row[csf("plan_cut")].'___'.$row[csf("excess_cut")].'___'.$row[csf("shiping_status")].'___'.$row[csf("pub_shipment_date")].'___'.$row[csf("company_name")].'___'.$row[csf("serving_company")];
				
				$production_arr[$row[csf("po_id")]][$row[csf("production_type")]]['g']+=$row[csf("production_quantity")];
				$production_arr[$row[csf("po_id")]][$row[csf("production_type")]]['r']+=$row[csf("re_production_qty")];
			}
			else
			{
				$po_arr_sub[$row[csf("po_id")]]=$row[csf("buyer_name")].'___'.$row[csf("style_ref_no")].'___'.$row[csf("gauge")].'___'.$row[csf("job_no_prefix_num")].'___'.$row[csf("job_no")].'___'.$row[csf("po_number")].'___'.$row[csf("po_quantity")].'___'.$row[csf("plan_cut")].'___'.$row[csf("excess_cut")].'___'.$row[csf("shiping_status")].'___'.$row[csf("pub_shipment_date")].'___'.$row[csf("company_name")].'___'.$row[csf("serving_company")];
				
				$production_arr_sub[$row[csf("po_id")]][$row[csf("production_type")]]['g']+=$row[csf("production_quantity")];
				$production_arr_sub[$row[csf("po_id")]][$row[csf("production_type")]]['r']+=$row[csf("re_production_qty")];
			}
			$production_summary_arr[$row[csf("production_source")]][$row[csf("production_type")]]['g'] += $row[csf("production_quantity")];
			$production_summary_arr[$row[csf("production_source")]][$row[csf("production_type")]]['r'] += $row[csf("re_production_qty")];
			if(!in_array($row[csf("po_id")], $chk_arr))
			{
				$plan_cut_qty += $row[csf("plan_cut")];
				$chk_arr[$row[csf("po_id")]] = $row[csf("po_id")];
			}
			
		}
		unset($sql_result);
		
		$poIds=chop($poIds,','); $poIds_cond="";
		if($db_type==2 && $tot_rows>1000)
		{
			$poIds_cond=" and (";
			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$poIds_cond.=" po_break_down_id in($ids) or ";
			}
			$poIds_cond=chop($poIds_cond,'or ');
			$poIds_cond.=")";
		}
		else
		{
			$poIds_cond=" and po_break_down_id in ($poIds)";
		}
		
		$inspection_arr=array();
		$inspection_arr_sub=array();
		$inspection_summary_arr=array();
		$sql_ins="SELECT po_break_down_id,source, inspection_qnty from pro_buyer_inspection where status_active=1 and is_deleted=0 $poIds_cond";
		$sql_ins_result=sql_select($sql_ins);
		foreach($sql_ins_result as $row)
		{
			if($row[csf("source")]==1)
			{
				$inspection_arr[$row[csf("po_break_down_id")]]['ins']+=$row[csf("inspection_qnty")];
			}
			else
			{
				$inspection_arr_sub[$row[csf("po_break_down_id")]]['ins']+=$row[csf("inspection_qnty")];
			}
			$inspection_summary_arr[$row[csf("source")]]+=$row[csf("inspection_qnty")];
		}
		unset($sql_ins_result);
		
		$ex_factory_arr=array();
		$ex_factory_arr_sub=array();
		$ex_factory_data=sql_select("SELECT po_break_down_id,production_source, MAX(ex_factory_date) AS ex_factory_date, sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) AS ex_factory_qnty from pro_ex_factory_mst where status_active=1 and is_deleted=0 $poIds_cond group by po_break_down_id");
		foreach($ex_factory_data as $exRow)
		{
			if($row[csf("source")]==1)
			{
				$ex_factory_arr[$exRow[csf('po_break_down_id')]]['date']=$exRow[csf('ex_factory_date')];
				$ex_factory_arr[$exRow[csf('po_break_down_id')]]['qty']=$exRow[csf('ex_factory_qnty')];
			}
			else
			{
				$ex_factory_arr_sub[$exRow[csf('po_break_down_id')]]['date']=$exRow[csf('ex_factory_date')];
				$ex_factory_arr_sub[$exRow[csf('po_break_down_id')]]['qty']=$exRow[csf('ex_factory_qnty')];
			}
		}
		unset($ex_factory_data);
		
		?>
		<div>
			<!-- ============================================ heading part =========================== -->
	        <div>
	            <table width="3700" cellspacing="0" >
	                <tr class="form_caption" style="border:none;">
	                    <td colspan="41" align="center" style="border:none;font-size:16px; font-weight:bold" ><?  echo $report_title; ?></td>
	                </tr>
	                <tr style="border:none;">
	                    <td colspan="41" align="center" style="border:none; font-size:14px;">
	                        Company Name : <? echo $company_library[$cbo_company_name]; ?>                                
	                    </td>
	                </tr>
	                <tr style="border:none;">
	                    <td colspan="41" align="center" style="border:none;font-size:12px; font-weight:bold">
	                        <?
	                            if(str_replace("'","",trim($txt_date_from))!="" && str_replace("'","",trim($txt_date_to))!="")
	                            {
	                                echo "From $fromDate To $toDate" ;
	                            }
	                        ?>
	                    </td>
	                </tr>
	            </table>
	        </div>
	        <!-- ============================================ summary part ============================== -->
	        <div style="width:3300px;padding:10px 0">
	            <table width="3300" cellspacing="0" border="1" class="rpt_table" rules="all">
	            	<caption style="font-size:18px;font-weight:bold;">Production Summary</caption>
	                <thead> 
	                	<th width="100">Particular</th>

	                    <th width="100">Knitting Complete</th>
	                    <th width="100">Knitting Balance</th>
	                    <th width="100">Knitting WIP</th>
	                    
	                    <th width="100">1st Inspection</th>
	                    <th width="100">1st Inspection Balance</th>
	                    <th width="100">1st Inspection WIP</th>
	                    
	                    <th width="100">Linking Complete</th>
	                    <th width="100">Linking Balance</th>
	                    <th width="100">Linking WIP</th>
	                    
	                    <th width="100">Triming Complete</th>
	                    <th width="100">Triming Balance</th>
	                    <th width="100">Triming WIP</th>
	                    
	                    <th width="100">Mending Complete</th>
	                    <th width="100">Mending Balance</th>
	                    <th width="100">Mending WIP</th>
	                    
	                    <th width="100">Wash Complete</th>
	                    <th width="100">Wash Balance</th>
	                    <th width="100">Wash WIP</th>
	                    
	                    <th width="100">Attachment Complete</th>
	                    <th width="100">Attach. Balance</th>
	                    <th width="100">Attcah. WIP</th>
	                    
	                    <th width="100">Sewing Complete</th>
	                    <th width="100">Sewing Balance</th>
	                    <th width="100">Sewing WIP</th>
	                    
	                    <th width="100">PQC Complete</th>
	                    <th width="100">PQC Balance</th>
	                    <th width="100">PQC WIP</th>
	                    
	                    <th width="100" >Iron Complete</th>
	                    <th width="100" >Iron Balance</th>
	                    <th width="100" >Re-Iron</th>
	                    
	                    <th width="100">Packing Complete</th>
	                    <th width="100">Packing Balance</th>
	                </thead>
	                <tbody>
	                	<?
	                	$summary_tot_knitting_com = 0;
						$summary_tot_knitting_bal = 0;
						$summary_tot_knitting_wip = 0;
						
						$summary_tot_inspection_qnty = 0;
						$summary_tot_inspe_bal = 0;
						$summary_tot_ins_wip = 0;
						
						$summary_tot_makeup_comp = 0;
						$summary_tot_makeup_bal = 0;
						$summary_tot_makeup_wip = 0;

						$summary_tot_triming_comp = 0;
						$summary_tot_triming_bal = 0;
						$summary_tot_triming_wip = 0;

						$summary_tot_mending_comp = 0;
						$summary_tot_mending_bal = 0;
						$summary_tot_mending_wip = 0;
						
						$summary_tot_wash_comp = 0;
						$summary_tot_wash_bal = 0;
						$summary_tot_wash_wip = 0;
						
						$summary_tot_attach_comm = 0;
						$summary_tot_attach_bal = 0;
						$summary_tot_attach_wip = 0;
						
						$summary_tot_sewing_comm = 0;
						$summary_tot_sewing_bal = 0;
						$summary_tot_sewing_wip = 0;
						
						$summary_tot_pqc_comm = 0;
						$summary_tot_pqc_bal = 0;
						$summary_tot_pqc_wip = 0;
						
						$summary_tot_iron_com = 0;
						$summary_tot_iron_bal = 0;
						$summary_tot_re_iron = 0;
						
						$tot_packing_comm = 0;
						$tot_packing_bal = 0;
						ksort($production_summary_arr);
	                	foreach ($production_summary_arr as $key => $val) 
	                	{
	                		$tot_knit = $val[1]['g'];
	                		$tot_insp = $inspection_summary_arr[$key];
	                		$tot_mok = $val[4]['g'];
	                		$tot_trim = $val[111]['g'];
	                		$tot_mend = $val[112]['g'];
	                		$tot_wash = $val[3]['g'];
	                		$tot_atta = $val[11]['g'];
	                		$tot_sew = $val[5]['g'];
	                		$tot_pqc = $val[114]['g'];
	                		$tot_iron = $val[7]['g']+$val[67]['g'];
	                		$tot_re_iron = $val[7]['r']+$val[67]['r'];
	                		$tot_fin = $val[8]['g'];

	                		$tot_knit_bal 	= $plan_cut_qty - $tot_knit;
	                		$tot_insp_bal 	= $plan_cut_qty - $tot_insp;
	                		$tot_mok_bal 	= $plan_cut_qty - $tot_mok;
	                		$tot_trim_bal 	= $plan_cut_qty - $tot_trim;
	                		$tot_mend_bal 	= $plan_cut_qty - $tot_mend;
	                		$tot_wash_bal 	= $plan_cut_qty - $tot_wash;
	                		$tot_atta_bal 	= $plan_cut_qty - $tot_atta;
	                		$tot_sew_bal 	= $plan_cut_qty - $tot_sew;
	                		$tot_pqc_bal 	= $plan_cut_qty - $tot_pqc;
	                		$tot_iron_bal 	= $plan_cut_qty - $tot_iron;
	                		$tot_fin_bal 	= $plan_cut_qty - $tot_fin;

	                		$tot_knit_wip 	= $plan_cut_qty - $tot_knit;
	                		$tot_insp_wip 	= $tot_knit - $tot_insp;
	                		$tot_mok_wip 	= $tot_insp - $tot_mok;
	                		$tot_trim_wip 	= $tot_mok - $tot_trim;
	                		$tot_mend_wip 	= $tot_trim - $tot_mend;
	                		$tot_wash_wip 	= $tot_mend - $tot_wash;
	                		$tot_atta_wip 	= $tot_wash - $tot_atta;
	                		$tot_sew_wip 	= $tot_atta - $tot_sew;
	                		$tot_pqc_wip 	= $tot_sew - $tot_pqc;
	                		$tot_iron_wip 	= $tot_pqc - $tot_iron;

	                		$prod_source = ($key==1) ? "Inhouse Production" : "Subcon Production";
		                	?>
		                    <tr>
		                        <td><? echo $prod_source; ?></td>
		                        <td align="right"><? echo number_format($tot_knit,0);?></td>
		                        <td align="right"><? echo number_format($tot_knit_bal,0);?></td>
		                        <td align="right"><? echo number_format($tot_knit_wip,0);?></td>

		                        <td align="right"><? echo number_format($tot_insp,0);?></td>
		                        <td align="right"><? echo number_format($tot_insp_bal,0);?></td>
		                        <td align="right"><? echo number_format($tot_insp_wip,0);?></td>

		                        <td align="right"><? echo number_format($tot_mok,0);?></td>
		                        <td align="right"><? echo number_format($tot_mok_bal,0);?></td>
		                        <td align="right"><? echo number_format($tot_mok_wip,0);?></td>

		                        <td align="right"><? echo number_format($tot_trim,0);?></td>
		                        <td align="right"><? echo number_format($tot_trim_bal,0);?></td>
		                        <td align="right"><? echo number_format($tot_trim_wip,0);?></td>

		                        <td align="right"><? echo number_format($tot_mend,0);?></td>
		                        <td align="right"><? echo number_format($tot_mend_bal,0);?></td>
		                        <td align="right"><? echo number_format($tot_mend_wip,0);?></td>

		                        <td align="right"><? echo number_format($tot_wash,0);?></td>
		                        <td align="right"><? echo number_format($tot_wash_bal,0);?></td>
		                        <td align="right"><? echo number_format($tot_wash_wip,0);?></td>

		                        <td align="right"><? echo number_format($tot_atta,0);?></td>
		                        <td align="right"><? echo number_format($tot_atta_bal,0);?></td>
		                        <td align="right"><? echo number_format($tot_atta_wip,0);?></td>

		                        <td align="right"><? echo number_format($tot_sew,0);?></td>
		                        <td align="right"><? echo number_format($tot_sew_bal,0);?></td>
		                        <td align="right"><? echo number_format($tot_sew_wip,0);?></td>

		                        <td align="right"><? echo number_format($tot_pqc,0);?></td>
		                        <td align="right"><? echo number_format($tot_pqc_bal,0);?></td>
		                        <td align="right"><? echo number_format($tot_pqc_wip,0);?></td>

		                        <td align="right"><? echo number_format($tot_iron,0);?></td>
		                        <td align="right"><? echo number_format($tot_iron_bal,0);?></td>
		                        <td align="right"><? echo number_format($tot_re_iron,0);?></td>

		                        <td align="right"><? echo number_format($tot_fin,0);?></td>
		                        <td align="right"><? echo number_format($tot_fin_bal,0);?></td>
		                    </tr>
		                    <?
		                    $summary_tot_knitting_com += $tot_knit;
							$summary_tot_kintting_bal += $tot_knit_bal;
							$summary_tot_knitting_wip += $tot_knit_wip;
							
							$summary_tot_inspection_qnty += $tot_insp;
							$summary_tot_inspe_bal += $tot_insp_bal;
							$summary_tot_ins_wip += $tot_insp_wip;
							
							$summary_tot_makeup_comp += $tot_mok;
							$summary_tot_makeup_bal += $tot_mok_bal;
							$summary_tot_makeup_wip += $tot_mok_wip;

							$summary_tot_triming_comp += $tot_trim;
							$summary_tot_triming_bal += $tot_trim_bal;
							$summary_tot_triming_wip += $tot_trim_wip;

							$summary_tot_mending_comp += $tot_mend;
							$summary_tot_mending_bal += $tot_mend_bal;
							$summary_tot_mending_wip += $tot_mend_wip;
							
							$summary_tot_wash_comp += $tot_wash;
							$summary_tot_wash_bal += $tot_wash_bal;
							$summary_tot_wash_wip += $tot_wash_wip;
							
							$summary_tot_attach_comm += $tot_atta;
							$summary_tot_attach_bal += $tot_atta_bal;
							$summary_tot_attach_wip += $tot_atta_wip;
							
							$summary_tot_sewing_comm += $tot_sew;
							$summary_tot_sewing_bal += $tot_sew_bal;
							$summary_tot_sewing_wip += $tot_sew_wip;
							
							$summary_tot_pqc_comm += $tot_pqc;
							$summary_tot_pqc_bal += $tot_pqc_bal;
							$summary_tot_pqc_wip += $tot_pqc_wip;
							
							$summary_tot_iron_com += $iron_com;
							$summary_tot_iron_bal += $iron_com_bal;
							$summary_tot_re_iron += $tot_re_iron;
							
							$summary_tot_packing_comm += $tot_fin;
							$summary_tot_packing_bal += $tot_fin_bal;
		                }
		                ?>
	                    
	                </tbody>
	                <tfoot>                    
	                    <tr>
	                        <th>Total Production</td>
	                        <th align="right"><? echo number_format($summary_tot_knitting_com,0);?></th>
	                        <th align="right"><? echo number_format($summary_tot_kintting_bal,0);?></th>
	                        <th align="right"><? echo number_format($summary_tot_knitting_wip,0);?></th>

	                        <th align="right"><? echo number_format($summary_tot_inspection_qnty,0);?></th>
	                        <th align="right"><? echo number_format($summary_tot_inspe_bal,0);?></th>
	                        <th align="right"><? echo number_format($summary_tot_ins_wip,0);?></th>

	                        <th align="right"><? echo number_format($summary_tot_makeup_comp,0);?></th>
	                        <th align="right"><? echo number_format($summary_tot_makeup_bal,0);?></th>
	                        <th align="right"><? echo number_format($summary_tot_makeup_wip,0);?></th>

	                        <th align="right"><? echo number_format($summary_tot_triming_comp,0);?></th>
	                        <th align="right"><? echo number_format($summary_tot_triming_bal,0);?></th>
	                        <th align="right"><? echo number_format($summary_tot_triming_wip,0);?></th>

	                        <th align="right"><? echo number_format($summary_tot_mending_comp,0);?></th>
	                        <th align="right"><? echo number_format($summary_tot_mending_bal,0);?></th>
	                        <th align="right"><? echo number_format($summary_tot_mending_wip,0);?></th>

	                        <th align="right"><? echo number_format($summary_tot_wash_comp,0);?></th>
	                        <th align="right"><? echo number_format($summary_tot_wash_bal,0);?></th>
	                        <th align="right"><? echo number_format($summary_tot_wash_wip,0);?></th>

	                        <th align="right"><? echo number_format($summary_tot_attach_comm,0);?></th>
	                        <th align="right"><? echo number_format($summary_tot_attach_bal,0);?></th>
	                        <th align="right"><? echo number_format($summary_tot_attach_wip,0);?></th>

	                        <th align="right"><? echo number_format($summary_tot_sewing_comm,0);?></th>
	                        <th align="right"><? echo number_format($summary_tot_sewing_bal,0);?></th>
	                        <th align="right"><? echo number_format($summary_tot_sewing_wip,0);?></th>

	                        <th align="right"><? echo number_format($summary_tot_pqc_comm,0);?></th>
	                        <th align="right"><? echo number_format($summary_tot_pqc_bal,0);?></th>
	                        <th align="right"><? echo number_format($summary_tot_pqc_wip,0);?></th>

	                        <th align="right"><? echo number_format($summary_tot_iron_com,0);?></th>
	                        <th align="right"><? echo number_format($summary_tot_iron_bal,0);?></th>
	                        <th align="right"><? echo number_format($summary_tot_re_iron,0);?></th>

	                        <th align="right"><? echo number_format($summary_tot_packing_comm,0);?></th>
	                        <th align="right"><? echo number_format($summary_tot_packing_bal,0);?></th>
	                    </tr>
	                </tfoot>
	            </table>
	        </div>
	        <!-- =========================================== inhouse data ====================================== -->
	        <div>
		        <table width="3700" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
		        	<caption style="font-size:18px;font-weight:bold;">In House Production</caption>
		            <thead>
		            	<tr>
		                	<th rowspan="2" width="30">Sl.</th> 
		                	<th colspan="10" style="background:#CCFF99">Order Details</th>
		                    <th colspan="34">Style Closing Summary</th>
		                    <th colspan="5"  style="background:#CCFF99">Shipment Status</th>
		                </tr>
		                <tr>
		                    <th width="100">Company</th>
		                    <th width="100">Working Company</th>
		                    <th width="100">Buyer</th>
		                    <th width="100">Style</th>
		                    <th width="80">GG</th>
		                    <th width="50">Photo</th>
		                    <th width="100">Order No</th>
		                    <th width="80">Order Qty</th>
		                    <th width="80">Plan Knit Qty</th>
		                    <th width="60">Ex. Knit %</th>
		                    
		                    <th width="70" style="background:#FFA">Knitting Complete</th>
		                    <th width="70" style="background:#FFA">Knitting Balance</th>
		                    <th width="70" style="background:#FFA">Knitting WIP</th>
		                    
		                    <th width="70">1st Inspection</th>
		                    <th width="70">1st Inspection Balance</th>
		                    <th width="70">1st Inspection WIP</th>
		                    
		                    <th width="70" style="background:#FFA">Linking Complete</th>
		                    <th width="70" style="background:#FFA">Linking Balance</th>
		                    <th width="70" style="background:#FFA">Linking WIP</th>
		                    
		                    <th width="70">Triming Complete</th>
		                    <th width="70">Triming Balance</th>
		                    <th width="70">Triming WIP</th>
		                    
		                    <th width="70" style="background:#FFA">Mending Complete</th>
		                    <th width="70" style="background:#FFA">Mending Balance</th>
		                    <th width="70" style="background:#FFA">Mending WIP</th>
		                    
		                    <th width="70">Wash Complete</th>
		                    <th width="70">Wash Balance</th>
		                    <th width="70">Wash WIP</th>
		                    
		                    <th width="70" style="background:#FFA">Attachment Complete</th>
		                    <th width="70" style="background:#FFA">Attach. Balance</th>
		                    <th width="70" style="background:#FFA">Attcah. WIP</th>
		                    
		                    <th width="70">Sewing Complete</th>
		                    <th width="70">Sewing Balance</th>
		                    <th width="70">Sewing WIP</th>
		                    
		                    <th width="70" style="background:#FFA">PQC Complete</th>
		                    <th width="70" style="background:#FFA">PQC Balance</th>
		                    <th width="70" style="background:#FFA">PQC WIP</th>
		                    
		                    <th width="70" >Iron Complete</th>
		                    <th width="70" >Iron Balance</th>
		                    <th width="70" >Re-Iron</th>
		                    
		                    <th width="70" style="background:#FFA">Packing Complete</th>
		                    <th width="70" style="background:#FFA">Packing Balance</th>
		                    <th width="70" style="background:#FFA">Packing Complete %</th>
		                    
		                    <th width="70">Status</th>
		                    <th width="70">Shipment Qty</th>
		                    
		                    <th width="80">Shipment Access/ Balance</th>
		                    <th width="70">Shipment Date</th>
		                    <th width="50">R. Days</th>
		                    
		                    <th>Shipment Status</th>
		                </tr>
		            </thead>
		        </table>
		        <div style="max-height:380px; overflow-y:scroll; width:3700px" id="scroll_body">
		            <table cellspacing="0" border="1" class="rpt_table" width="3680" id="table_body" rules="all">
		            <? $i=1;
		            $tot_knitting_com = 0;
					$tot_kintting_bal = 0;
					$tot_knitting_wip = 0;
					
					$tot_inspection_qnty = 0;
					$tot_inspe_bal = 0;
					$tot_ins_wip = 0;
					
					$tot_makeup_comp = 0;
					$tot_makeup_bal = 0;
					$tot_makeup_wip = 0;

					$tot_triming_comp = 0;
					$tot_triming_bal = 0;
					$tot_triming_wip = 0;

					$tot_mending_comp = 0;
					$tot_mending_bal = 0;
					$tot_mending_wip = 0;
					
					$tot_wash_comp = 0;
					$tot_wash_bal = 0;
					$tot_wash_wip = 0;
					
					$tot_attach_comm = 0;
					$tot_attach_bal = 0;
					$tot_attach_wip = 0;
					
					$tot_sewing_comm = 0;
					$tot_sewing_bal = 0;
					$tot_sewing_wip = 0;
					
					$tot_pqc_comm = 0;
					$tot_pqc_bal = 0;
					$tot_pqc_wip = 0;
					
					$tot_iron_com = 0;
					$tot_iron_bal = 0;
					$tot_re_iron = 0;
					
					$tot_packing_comm = 0;
					$tot_packing_bal = 0;
					$tot_shipment_com = 0;
					$tot_shipment_acc_bal = 0;
					foreach($po_arr as $poid=>$value)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$buyer_name=$style_ref_no=$gauge=$job_no_prefix_num=$job_no=$po_number=$shiping_status=""; $pubshipment_date="";
						$po_qty=$plan_cut=$excess_cut=0;
						$exdata=explode("___",$value);
						$buyer_name=$exdata[0];
						$style_ref_no=$exdata[1];
						$gauge=$exdata[2];
						$job_no_prefix_num=$exdata[3];
						$job_no=$exdata[4];
						$po_number=$exdata[5];
						
						$po_qty=$exdata[6];
						$plan_cut=$exdata[7];
						$excess_cut=$exdata[8];				
						$shiping_status=$exdata[9];
						$pubshipment_date=$exdata[10];
						$company_name=$exdata[11];
						$wo_company_name=$exdata[12];
						
						$knitting_com=$inspection_qnty=$makeup_comp=$wash_comp=$attach_comm=$sewing_comm=$iron_com=$re_iron=$packing_comm=$shipment_com=$triming_comp=$mending_comp=$pqc_comm=0;
						$knitting_com=$production_arr[$poid][1]['g'];
						$inspection_qnty=$inspection_arr[$poid]['ins'];
						$makeup_comp=$production_arr[$poid][4]['g'];
						$triming_comp=$production_arr[$poid][111]['g'];
						$mending_comp=$production_arr[$poid][112]['g'];
						$wash_comp=$production_arr[$poid][3]['g'];
						$attach_comm=$production_arr[$poid][11]['g'];
						$sewing_comm=$production_arr[$poid][5]['g'];
						$pqc_comm=$production_arr[$poid][114]['g'];
						$iron_com=$production_arr[$poid][7]['g']+$production_arr[$poid][67]['g'];
						$re_iron=$production_arr[$poid][7]['r']+$production_arr[$poid][67]['r'];
						$packing_comm=$production_arr[$poid][8]['g'];
						$shipment_com=$ex_factory_arr[$poid]['qty'];
						
						$kintting_bal=$inspe_bal=$makeup_bal=$wash_bal=$attach_bal=$sewing_bal=$iron_bal=$packing_bal=$shipment_acc_bal=$triming_bal=$mending_bal=$pqc_bal=0;
						$kintting_bal=$plan_cut-$knitting_com;
						$inspe_bal=$plan_cut-$inspection_qnty;
						$makeup_bal=$plan_cut-$makeup_comp;
						$triming_bal=$plan_cut-$triming_comp;
						$mending_bal=$plan_cut-$mending_comp;
						$wash_bal=$plan_cut-$wash_comp;
						$attach_bal=$plan_cut-$attach_comm;
						$sewing_bal=$plan_cut-$sewing_comm;
						$pqc_bal=$plan_cut-$pqc_comm;
						$iron_bal=$plan_cut-$iron_com;
						$packing_bal=$plan_cut-$packing_comm;
						$shipment_acc_bal=$po_qty-$shipment_com;
						
						$knitting_wip=$ins_wip=$makeup_wip=$wash_wip=$attach_wip=$sewing_wip=$packing_percent=$triming_wip=$mending_wip=$pqc_wip=0;
						$knitting_wip=$knitting_com-$inspection_qnty;
						$ins_wip=$inspection_qnty-$makeup_comp;
						$makeup_wip=$makeup_comp-$triming_comp;
						$triming_wip=$makeup_comp-$triming_comp;
						$mending_wip=$mending_comp-$wash_comp;
						$wash_wip=$wash_comp-$attach_comm;
						$attach_wip=$attach_comm-$sewing_comm;
						$sewing_wip=$sewing_comm-$pqc_comm;
						$pqc_wip=$pqc_comm-$iron_com;
						$packing_percent=($packing_comm/$po_qty)*100;
						
						$packing_status="Running"; $shpment_date='';
						
						if($packing_comm>=$plan_cut) $packing_status="Complete"; else if($packing_comm<$plan_cut) $packing_status="Running";
						
						$shpment_date=$ex_factory_arr[$poid]['date'];
						$reaming_days=0;
						$reaming_days=datediff("d",date("Y-m-d"),$pubshipment_date);
						if($shiping_status==3) $reaming_days=0;
						
						?>
		                 <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
		                    <td width="30"><? echo $i; ?></td>      
		                    <td width="100" style="word-break:break-all"><? echo $company_library[$company_name]; ?>&nbsp;</td>
		                    <td width="100" style="word-break:break-all"><? echo $company_library[$wo_company_name]; ?>&nbsp;</td> 
		                    <td width="100" style="word-break:break-all"><? echo $buyer_arr[$buyer_name]; ?>&nbsp;</td>
		                    <td width="100" style="word-break:break-all"><? echo $style_ref_no; ?>&nbsp;</td>
		                    <td width="80" style="word-break:break-all"><? echo $gauge_arr[$gauge]; ?>&nbsp;</td>
		                    
		                    <td width="50" onClick="openmypage_image('requires/style_production_summary_report_controller.php?action=show_image&job_no=<? echo $job_no; ?>','Image View')"><p><img  src='../../../<? echo $imge_arr[$job_no]; ?>' height='20' width='40' /></p></td>
		                    <td width="100" style="word-break:break-all"><? echo $po_number; ?>&nbsp;</td>
		                    <td width="80" align="right"><? echo $po_qty; ?></td>
		                    <td width="80" align="right"><? echo $plan_cut; ?></td>
		                    <td width="60" align="right"><? echo number_format($excess_cut,2); ?></td>
		                    
		                    <td width="70" align="right"><? echo $knitting_com; ?></td>
		                    <td width="70" align="right"><? echo $kintting_bal; ?></td>
		                    <td width="70" align="right"><? echo $knitting_wip; ?></td>
		                    
		                    <td width="70" align="right"><? echo $inspection_qnty; ?></td>
		                    <td width="70" align="right"><? echo $inspe_bal; ?></td>
		                    <td width="70" align="right"><? echo $ins_wip; ?></td>
		                    
		                    <td width="70" align="right"><? echo $makeup_comp; ?></td>
		                    <td width="70" align="right"><? echo $makeup_bal; ?></td>
		                    <td width="70" align="right"><? echo $makeup_wip; ?></td>
		                    
		                    <td width="70" align="right"><? echo $triming_comp; ?></td>
		                    <td width="70" align="right"><? echo $triming_bal; ?></td>
		                    <td width="70" align="right"><? echo $triming_wip; ?></td>
		                    
		                    <td width="70" align="right"><? echo $mending_comp; ?></td>
		                    <td width="70" align="right"><? echo $mending_bal; ?></td>
		                    <td width="70" align="right"><? echo $mending_wip; ?></td>
		                    
		                    <td width="70" align="right"><? echo $wash_comp; ?></td>
		                    <td width="70" align="right"><? echo $wash_bal; ?></td>
		                    <td width="70" align="right"><? echo $wash_wip; ?></td>
		                    
		                    <td width="70" align="right"><? echo $attach_comm; ?></td>
		                    <td width="70" align="right"><? echo $attach_bal; ?></td>
		                    <td width="70" align="right"><? echo $attach_wip; ?></td>
		                    
		                    <td width="70" align="right"><? echo $sewing_comm; ?></td>
		                    <td width="70" align="right"><? echo $sewing_bal; ?></td>
		                    <td width="70" align="right"><? echo $sewing_wip; ?></td>
		                    
		                    <td width="70" align="right"><? echo $pqc_comm; ?></td>
		                    <td width="70" align="right"><? echo $pqc_bal; ?></td>
		                    <td width="70" align="right"><? echo $pqc_wip; ?></td>
		                    
		                    <td width="70" align="right"><? echo $iron_com; ?></td>
		                    <td width="70" align="right"><? echo $iron_bal; ?></td>
		                    <td width="70" align="right"><? echo $re_iron; ?></td>
		                    
		                    <td width="70" align="right"><? echo $packing_comm; ?></td>
		                    <td width="70" align="right"><? echo $packing_bal; ?></td>
		                    <td width="70" align="right"><? echo number_format($packing_percent,2); ?></td>
		                    
		                    <td width="70" style="word-break:break-all"><? echo $packing_status; ?></td>
		                    <td width="70" align="right"><? echo $shipment_com; ?></td>
		                    
		                    <td width="80" align="right"><? echo $shipment_acc_bal; ?></td>
		                    <td width="70" style="font-size:11px"><? echo change_date_format($shpment_date); ?></td>
		                    <td width="50" align="right" title="<? echo "Pub Ship Date: ".change_date_format($pubshipment_date); ?>"><? echo $reaming_days; ?></td>
		                    <td style="word-break:break-all; font-size:11px"><? echo $shipment_status[$shiping_status]; ?></td>
		                </tr>
						<?
						$i++;
						$tot_knitting_com += $knitting_com;
						$tot_kintting_bal+=$kintting_bal;
						$tot_knitting_wip+=$knitting_wip;
						
						$tot_inspection_qnty+=$inspection_qnty;
						$tot_inspe_bal+=$inspe_bal;
						$tot_ins_wip+=$ins_wip;
						
						$tot_makeup_comp+=$makeup_comp;
						$tot_makeup_bal+=$makeup_bal;
						$tot_makeup_wip+=$makeup_wip;
						
						$tot_triming_comp+=$triming_comp;
						$tot_triming_bal+=$triming_bal;
						$tot_triming_wip+=$triming_wip;
						
						$tot_mending_comp+=$mending_comp;
						$tot_mending_bal+=$mending_bal;
						$tot_mending_wip+=$mending_wip;
						
						$tot_wash_comp+=$wash_comp;
						$tot_wash_bal+=$wash_bal;
						$tot_wash_wip+=$wash_wip;
						
						$tot_attach_comm+=$attach_comm;
						$tot_attach_bal+=$attach_bal;
						$tot_attach_wip+=$attach_wip;
						
						$tot_sewing_comm+=$sewing_comm;
						$tot_sewing_bal+=$sewing_bal;
						$tot_sewing_wip+=$sewing_wip;
						
						$tot_pqc_comm+=$pqc_comm;
						$tot_pqc_bal+=$pqc_bal;
						$tot_pqc_wip+=$pqc_wip;
						
						$tot_iron_com+=$iron_com;
						$tot_iron_bal+=$iron_bal;
						$tot_re_iron+=$re_iron;
						
						$tot_packing_comm+=$packing_comm;
						$tot_packing_bal+=$packing_bal;
						$tot_shipment_com+=$shipment_com;
						$tot_shipment_acc_bal+=$shipment_acc_bal;
					}
					?>
		            </table>
		        </div>
		        <table width="3700" cellspacing="0" border="1" class="tbl_bottom" rules="all">
		            <tr>
		            	<td width="30">&nbsp;</td> 
		                <td width="100">&nbsp;</td>
		                <td width="100">&nbsp;</td>
		                <td width="100">&nbsp;</td>
		                <td width="100">&nbsp;</td>
		                <td width="80">&nbsp;</td>
		                <td width="50">&nbsp;</td>
		                <td width="100">&nbsp;</td>
		                <td width="80">&nbsp;</td>
		                <td width="80">&nbsp;</td>
		                <td width="60">Total:</td>
		                
		                <td width="70" style="background:#FFA" id="td_knitting_com"><? echo $tot_knitting_com; ?></td>
		                <td width="70" style="background:#FFA" id="td_knitting_bal"><? echo $tot_kintting_bal; ?></td>
		                <td width="70" style="background:#FFA" id="td_knitting_wip"><? echo $tot_knitting_wip; ?></td>
		                
		                <td width="70" id="td_inspection_qnty"><? echo $tot_inspection_qnty; ?></td>
		                <td width="70" id="td_inspe_bal"><? echo $tot_inspe_bal; ?></td>
		                <td width="70" id="td_ins_wip"><? echo $tot_ins_wip; ?></td>
		                
		                <td width="70" style="background:#FFA" id="td_makeup_comp"><? echo $tot_makeup_comp; ?></td>
		                <td width="70" style="background:#FFA" id="td_makeup_bal"><? echo $tot_makeup_bal; ?></td>
		                <td width="70" style="background:#FFA" id="td_makeup_wip"><? echo $tot_makeup_wip; ?></td>
		                
		                <td width="70" id="td_triming_comp"><? echo $tot_triming_comp; ?></td>
		                <td width="70" id="td_triming_bal"><? echo $tot_triming_bal; ?></td>
		                <td width="70" id="td_triming_wip"><? echo $tot_triming_wip; ?></td>
		                
		                <td width="70" id="td_mending_comp"><? echo $tot_mending_comp; ?></td>
		                <td width="70" id="td_mending_bal"><? echo $tot_mending_bal; ?></td>
		                <td width="70" id="td_mending_wip"><? echo $tot_mending_wip; ?></td>
		                
		                <td width="70" id="td_wash_comp"><? echo $tot_wash_comp; ?></td>
		                <td width="70" id="td_wash_bal"><? echo $tot_wash_bal; ?></td>
		                <td width="70" id="td_wash_wip"><? echo $tot_wash_wip; ?></td>
		                
		                <td width="70" style="background:#FFA" id="td_attach_comm"><? echo $tot_attach_comm; ?></td>
		                <td width="70" style="background:#FFA" id="td_attach_bal"><? echo $tot_attach_bal; ?></td>
		                <td width="70" style="background:#FFA" id="td_attach_wip"><? echo $tot_attach_wip; ?></td>
		                
		                <td width="70" id="td_sewing_comm"><? echo $tot_sewing_comm; ?></td>
		                <td width="70" id="td_sewing_bal"><? echo $tot_sewing_bal; ?></td>
		                <td width="70" id="td_sewing_wip"><? echo $tot_sewing_wip; ?></td>
		                
		                <td width="70" id="td_pqc_comm"><? echo $tot_pqc_comm; ?></td>
		                <td width="70" id="td_pqc_bal"><? echo $tot_pqc_bal; ?></td>
		                <td width="70" id="td_pqc_wip"><? echo $tot_pqc_wip; ?></td>
		                
		                <td width="70" style="background:#FFA" id="td_iron_com"><? echo $tot_iron_com; ?></td>
		                <td width="70" style="background:#FFA" id="td_iron_bal"><? echo $tot_iron_bal; ?></td>
		                <td width="70" style="background:#FFA" id="td_re_iron"><? echo $tot_re_iron; ?></td>
		                
		                <td width="70" id="td_packing_comm"><? echo $tot_packing_comm; ?></td>
		                <td width="70" id="td_packing_bal"><? echo $tot_packing_bal; ?></td>
		                <td width="70">&nbsp;</td>
		                
		                <td width="70">&nbsp;</td>
		                <td width="70" id="td_shipment_com"><? echo $tot_shipment_com; ?></td>
		                
		                <td width="80" id="td_shipment_acc_bal"><? echo $tot_shipment_acc_bal; ?></td>
		                <td width="70">&nbsp;</td>
		                <td width="50">&nbsp;</td>
		                <td>&nbsp;</td>
		            </tr>
		        </table>
		    </div>
		    <!-- =========================================== subcon data ========================================= -->
	        <div>
		        <table width="3700" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
		        	<caption style="font-size:18px;font-weight:bold;">Subcontract Production</caption>
		            <thead>
		            	<tr>
		                	<th rowspan="2" width="30">Sl.</th> 
		                	<th colspan="10" style="background:#CCFF99">Order Details</th>
		                    <th colspan="34">Style Closing Summary</th>
		                    <th colspan="5"  style="background:#CCFF99">Shipment Status</th>
		                </tr>
		                <tr>
		                    <th width="100">Company</th>
		                    <th width="100">Working Company</th>
		                    <th width="100">Buyer</th>
		                    <th width="100">Style</th>
		                    <th width="80">GG</th>
		                    <th width="50">Photo</th>
		                    <th width="100">Order No</th>
		                    <th width="80">Order Qty</th>
		                    <th width="80">Plan Knit Qty</th>
		                    <th width="60">Ex. Knit %</th>
		                    
		                    <th width="70" style="background:#FFA">Knitting Complete</th>
		                    <th width="70" style="background:#FFA">Knitting Balance</th>
		                    <th width="70" style="background:#FFA">Knitting WIP</th>
		                    
		                    <th width="70">1st Inspection</th>
		                    <th width="70">1st Inspection Balance</th>
		                    <th width="70">1st Inspection WIP</th>
		                    
		                    <th width="70" style="background:#FFA">Linking Complete</th>
		                    <th width="70" style="background:#FFA">Linking Balance</th>
		                    <th width="70" style="background:#FFA">Linking WIP</th>
		                    
		                    <th width="70">Triming Complete</th>
		                    <th width="70">Triming Balance</th>
		                    <th width="70">Triming WIP</th>
		                    
		                    <th width="70" style="background:#FFA">Mending Complete</th>
		                    <th width="70" style="background:#FFA">Mending Balance</th>
		                    <th width="70" style="background:#FFA">Mending WIP</th>
		                    
		                    <th width="70">Wash Complete</th>
		                    <th width="70">Wash Balance</th>
		                    <th width="70">Wash WIP</th>
		                    
		                    <th width="70" style="background:#FFA">Attachment Complete</th>
		                    <th width="70" style="background:#FFA">Attach. Balance</th>
		                    <th width="70" style="background:#FFA">Attcah. WIP</th>
		                    
		                    <th width="70">Sewing Complete</th>
		                    <th width="70">Sewing Balance</th>
		                    <th width="70">Sewing WIP</th>
		                    
		                    <th width="70" style="background:#FFA">PQC Complete</th>
		                    <th width="70" style="background:#FFA">PQC Balance</th>
		                    <th width="70" style="background:#FFA">PQC WIP</th>
		                    
		                    <th width="70" >Iron Complete</th>
		                    <th width="70" >Iron Balance</th>
		                    <th width="70" >Re-Iron</th>
		                    
		                    <th width="70" style="background:#FFA">Packing Complete</th>
		                    <th width="70" style="background:#FFA">Packing Balance</th>
		                    <th width="70" style="background:#FFA">Packing Complete %</th>
		                    
		                    <th width="70">Status</th>
		                    <th width="70">Shipment Qty</th>
		                    
		                    <th width="80">Shipment Access/ Balance</th>
		                    <th width="70">Shipment Date</th>
		                    <th width="50">R. Days</th>
		                    
		                    <th>Shipment Status</th>
		                </tr>
		            </thead>
		        </table>
		        <div style="max-height:380px; overflow-y:scroll; width:3700px" id="scroll_body">
		            <table cellspacing="0" border="1" class="rpt_table" width="3680" id="table_body_sub" rules="all">
		            <? $i=1;
		            $tot_knitting_com = 0;
					$tot_kintting_bal = 0;
					$tot_knitting_wip = 0;
					
					$tot_inspection_qnty = 0;
					$tot_inspe_bal = 0;
					$tot_ins_wip = 0;
					
					$tot_makeup_comp = 0;
					$tot_makeup_bal = 0;
					$tot_makeup_wip = 0;

					$tot_triming_comp = 0;
					$tot_triming_bal = 0;
					$tot_triming_wip = 0;

					$tot_mending_comp = 0;
					$tot_mending_bal = 0;
					$tot_mending_wip = 0;
					
					$tot_wash_comp = 0;
					$tot_wash_bal = 0;
					$tot_wash_wip = 0;
					
					$tot_attach_comm = 0;
					$tot_attach_bal = 0;
					$tot_attach_wip = 0;
					
					$tot_sewing_comm = 0;
					$tot_sewing_bal = 0;
					$tot_sewing_wip = 0;
					
					$tot_pqc_comm = 0;
					$tot_pqc_bal = 0;
					$tot_pqc_wip = 0;
					
					$tot_iron_com = 0;
					$tot_iron_bal = 0;
					$tot_re_iron = 0;
					
					$tot_packing_comm = 0;
					$tot_packing_bal = 0;
					$tot_shipment_com = 0;
					$tot_shipment_acc_bal = 0;
					foreach($po_arr_sub as $poid=>$value)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$buyer_name=$style_ref_no=$gauge=$job_no_prefix_num=$job_no=$po_number=$shiping_status=""; $pubshipment_date="";
						$po_qty=$plan_cut=$excess_cut=0;
						$exdata=explode("___",$value);
						$buyer_name=$exdata[0];
						$style_ref_no=$exdata[1];
						$gauge=$exdata[2];
						$job_no_prefix_num=$exdata[3];
						$job_no=$exdata[4];
						$po_number=$exdata[5];
						
						$po_qty=$exdata[6];
						$plan_cut=$exdata[7];
						$excess_cut=$exdata[8];				
						$shiping_status=$exdata[9];
						$pubshipment_date=$exdata[10];
						$company_name=$exdata[11];
						$wo_company_name=$exdata[12];
						
						$knitting_com=$inspection_qnty=$makeup_comp=$wash_comp=$attach_comm=$sewing_comm=$iron_com=$re_iron=$packing_comm=$shipment_com=$triming_comp=$mending_comp=$pqc_comm=0;
						$knitting_com=$production_arr_sub[$poid][1]['g'];
						$inspection_qnty=$inspection_arr_sub[$poid]['ins'];
						$makeup_comp=$production_arr_sub[$poid][4]['g'];
						$triming_comp=$production_arr_sub[$poid][111]['g'];
						$mending_comp=$production_arr_sub[$poid][112]['g'];
						$wash_comp=$production_arr_sub[$poid][3]['g'];
						$attach_comm=$production_arr_sub[$poid][11]['g'];
						$sewing_comm=$production_arr_sub[$poid][5]['g'];
						$pqc_comm=$production_arr_sub[$poid][114]['g'];
						$iron_com=$production_arr_sub[$poid][7]['g'];
						$re_iron=$production_arr_sub[$poid][7]['r'];
						$packing_comm=$production_arr_sub[$poid][8]['g'];
						$shipment_com=$ex_factory_arr_sub[$poid]['qty'];
						
						$kintting_bal=$inspe_bal=$makeup_bal=$wash_bal=$attach_bal=$sewing_bal=$iron_bal=$packing_bal=$shipment_acc_bal=$triming_bal=$mending_bal=$pqc_bal=0;
						$kintting_bal=$plan_cut-$knitting_com;
						$inspe_bal=$plan_cut-$inspection_qnty;
						$makeup_bal=$plan_cut-$makeup_comp;
						$triming_bal=$plan_cut-$triming_comp;
						$mending_bal=$plan_cut-$mending_comp;
						$wash_bal=$plan_cut-$wash_comp;
						$attach_bal=$plan_cut-$attach_comm;
						$sewing_bal=$plan_cut-$sewing_comm;
						$pqc_bal=$plan_cut-$pqc_comm;
						$iron_bal=$plan_cut-$iron_com;
						$packing_bal=$plan_cut-$packing_comm;
						$shipment_acc_bal=$po_qty-$shipment_com;
						
						$knitting_wip=$ins_wip=$makeup_wip=$wash_wip=$attach_wip=$sewing_wip=$packing_percent=$triming_wip=$mending_wip=$pqc_wip=0;
						
						$knitting_wip=$knitting_com-$inspection_qnty;
						$ins_wip=$inspection_qnty-$makeup_comp;
						$makeup_wip=$makeup_comp-$triming_comp;
						$triming_wip=$makeup_comp-$triming_comp;
						$mending_wip=$mending_comp-$wash_comp;
						$wash_wip=$wash_comp-$attach_comm;
						$attach_wip=$attach_comm-$sewing_comm;
						$sewing_wip=$sewing_comm-$pqc_comm;
						$pqc_wip=$pqc_comm-$iron_com;
						$packing_percent=($packing_comm/$po_qty)*100;
						
						$packing_status="Running"; $shpment_date='';
						
						if($packing_comm>=$plan_cut) $packing_status="Complete"; else if($packing_comm<$plan_cut) $packing_status="Running";
						
						$shpment_date=$ex_factory_arr_sub[$poid]['date'];
						$reaming_days=0;
						$reaming_days=datediff("d",date("Y-m-d"),$pubshipment_date);
						if($shiping_status==3) $reaming_days=0;
						
						?>
		                 <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
		                    <td width="30"><? echo $i; ?></td>      
		                    <td width="100" style="word-break:break-all"><? echo $company_library[$company_name]; ?>&nbsp;</td>
		                    <td width="100" style="word-break:break-all"><? echo $company_library[$wo_company_name]; ?>&nbsp;</td> 
		                    <td width="100" style="word-break:break-all"><? echo $buyer_arr[$buyer_name]; ?>&nbsp;</td>
		                    <td width="100" style="word-break:break-all"><? echo $style_ref_no; ?>&nbsp;</td>
		                    <td width="80" style="word-break:break-all"><? echo $gauge; ?>&nbsp;</td>
		                    
		                    <td width="50" onClick="openmypage_image('requires/style_production_summary_report_controller.php?action=show_image&job_no=<? echo $job_no; ?>','Image View')"><p><img  src='../../../<? echo $imge_arr[$job_no]; ?>' height='20' width='40' /></p></td>
		                    <td width="100" style="word-break:break-all"><? echo $po_number; ?>&nbsp;</td>
		                    <td width="80" align="right"><? echo $po_qty; ?></td>
		                    <td width="80" align="right"><? echo $plan_cut; ?></td>
		                    <td width="60" align="right"><? echo number_format($excess_cut,2); ?></td>
		                    
		                    <td width="70" align="right"><? echo $knitting_com; ?></td>
		                    <td width="70" align="right"><? echo $kintting_bal; ?></td>
		                    <td width="70" align="right"><? echo $knitting_wip; ?></td>
		                    
		                    <td width="70" align="right"><? echo $inspection_qnty; ?></td>
		                    <td width="70" align="right"><? echo $inspe_bal; ?></td>
		                    <td width="70" align="right"><? echo $ins_wip; ?></td>
		                    
		                    <td width="70" align="right"><? echo $makeup_comp; ?></td>
		                    <td width="70" align="right"><? echo $makeup_bal; ?></td>
		                    <td width="70" align="right"><? echo $makeup_wip; ?></td>
		                    
		                    <td width="70" align="right"><? echo $triming_comp; ?></td>
		                    <td width="70" align="right"><? echo $triming_bal; ?></td>
		                    <td width="70" align="right"><? echo $triming_wip; ?></td>
		                    
		                    <td width="70" align="right"><? echo $mending_comp; ?></td>
		                    <td width="70" align="right"><? echo $mending_bal; ?></td>
		                    <td width="70" align="right"><? echo $mending_wip; ?></td>
		                    
		                    <td width="70" align="right"><? echo $wash_comp; ?></td>
		                    <td width="70" align="right"><? echo $wash_bal; ?></td>
		                    <td width="70" align="right"><? echo $wash_wip; ?></td>
		                    
		                    <td width="70" align="right"><? echo $attach_comm; ?></td>
		                    <td width="70" align="right"><? echo $attach_bal; ?></td>
		                    <td width="70" align="right"><? echo $attach_wip; ?></td>
		                    
		                    <td width="70" align="right"><? echo $sewing_comm; ?></td>
		                    <td width="70" align="right"><? echo $sewing_bal; ?></td>
		                    <td width="70" align="right"><? echo $sewing_wip; ?></td>
		                    
		                    <td width="70" align="right"><? echo $pqc_comm; ?></td>
		                    <td width="70" align="right"><? echo $pqc_bal; ?></td>
		                    <td width="70" align="right"><? echo $pqc_wip; ?></td>
		                    
		                    <td width="70" align="right"><? echo $iron_com; ?></td>
		                    <td width="70" align="right"><? echo $iron_bal; ?></td>
		                    <td width="70" align="right"><? echo $re_iron; ?></td>
		                    
		                    <td width="70" align="right"><? echo $packing_comm; ?></td>
		                    <td width="70" align="right"><? echo $packing_bal; ?></td>
		                    <td width="70" align="right"><? echo number_format($packing_percent,2); ?></td>
		                    
		                    <td width="70" style="word-break:break-all"><? echo $packing_status; ?></td>
		                    <td width="70" align="right"><? echo $shipment_com; ?></td>
		                    
		                    <td width="80" align="right"><? echo $shipment_acc_bal; ?></td>
		                    <td width="70" style="font-size:11px"><? echo change_date_format($shpment_date); ?></td>
		                    <td width="50" align="right" title="<? echo "Pub Ship Date: ".change_date_format($pubshipment_date); ?>"><? echo $reaming_days; ?></td>
		                    <td style="word-break:break-all; font-size:11px"><? echo $shipment_status[$shiping_status]; ?></td>
		                </tr>
						<?
						$i++;
						$tot_knitting_com+=$knitting_com;
						$tot_kintting_bal+=$kintting_bal;
						$tot_knitting_wip+=$knitting_wip;
						
						$tot_inspection_qnty+=$inspection_qnty;
						$tot_inspe_bal+=$inspe_bal;
						$tot_ins_wip+=$ins_wip;
						
						$tot_makeup_comp+=$makeup_comp;
						$tot_makeup_bal+=$makeup_bal;
						$tot_makeup_wip+=$makeup_wip;
						
						$tot_triming_comp+=$triming_comp;
						$tot_triming_bal+=$triming_bal;
						$tot_triming_wip+=$triming_wip;
						
						$tot_mending_comp+=$mending_comp;
						$tot_mending_bal+=$mending_bal;
						$tot_mending_wip+=$mending_wip;
						
						$tot_wash_comp+=$wash_comp;
						$tot_wash_bal+=$wash_bal;
						$tot_wash_wip+=$wash_wip;
						
						$tot_attach_comm+=$attach_comm;
						$tot_attach_bal+=$attach_bal;
						$tot_attach_wip+=$attach_wip;
						
						$tot_sewing_comm+=$sewing_comm;
						$tot_sewing_bal+=$sewing_bal;
						$tot_sewing_wip+=$sewing_wip;
						
						$tot_pqc_comm+=$pqc_comm;
						$tot_pqc_bal+=$pqc_bal;
						$tot_pqc_wip+=$pqc_wip;
						
						$tot_iron_com+=$iron_com;
						$tot_iron_bal+=$iron_bal;
						$tot_re_iron+=$re_iron;
						
						$tot_packing_comm+=$packing_comm;
						$tot_packing_bal+=$packing_bal;
						$tot_shipment_com+=$shipment_com;
						$tot_shipment_acc_bal+=$shipment_acc_bal;
					}
					?>
		            </table>
		        </div>
		        <table width="3700" cellspacing="0" border="1" class="tbl_bottom" rules="all">
		            <tr>
		            	<td width="30">&nbsp;</td> 
		                <td width="100">&nbsp;</td>
		                <td width="100">&nbsp;</td>
		                <td width="100">&nbsp;</td>
		                <td width="100">&nbsp;</td>
		                <td width="80">&nbsp;</td>
		                <td width="50">&nbsp;</td>
		                <td width="100">&nbsp;</td>
		                <td width="80">&nbsp;</td>
		                <td width="80">&nbsp;</td>
		                <td width="60">Total:</td>
		                
		                <td width="70" style="background:#FFA" id="td_knitting_com"><? echo $tot_knitting_com; ?></td>
		                <td width="70" style="background:#FFA" id="td_knitting_bal"><? echo $tot_kintting_bal; ?></td>
		                <td width="70" style="background:#FFA" id="td_knitting_wip"><? echo $tot_knitting_wip; ?></td>
		                
		                <td width="70" id="td_inspection_qnty"><? echo $tot_inspection_qnty; ?></td>
		                <td width="70" id="td_inspe_bal"><? echo $tot_inspe_bal; ?></td>
		                <td width="70" id="td_ins_wip"><? echo $tot_ins_wip; ?></td>
		                
		                <td width="70" style="background:#FFA" id="td_makeup_comp"><? echo $tot_makeup_comp; ?></td>
		                <td width="70" style="background:#FFA" id="td_makeup_bal"><? echo $tot_makeup_bal; ?></td>
		                <td width="70" style="background:#FFA" id="td_makeup_wip"><? echo $tot_makeup_wip; ?></td>
		                
		                <td width="70" id="td_wash_comp"><? echo $tot_triming_comp; ?></td>
		                <td width="70" id="td_wash_bal"><? echo $tot_triming_bal; ?></td>
		                <td width="70" id="td_wash_wip"><? echo $tot_triming_wip; ?></td>
		                
		                <td width="70" id="td_wash_comp"><? echo $tot_mending_comp; ?></td>
		                <td width="70" id="td_wash_bal"><? echo $tot_mending_bal; ?></td>
		                <td width="70" id="td_wash_wip"><? echo $tot_mending_wip; ?></td>
		                
		                <td width="70" id="td_wash_comp"><? echo $tot_wash_comp; ?></td>
		                <td width="70" id="td_wash_bal"><? echo $tot_wash_bal; ?></td>
		                <td width="70" id="td_wash_wip"><? echo $tot_wash_wip; ?></td>
		                
		                <td width="70" style="background:#FFA" id="td_attach_comm"><? echo $tot_attach_comm; ?></td>
		                <td width="70" style="background:#FFA" id="td_attach_bal"><? echo $tot_attach_bal; ?></td>
		                <td width="70" style="background:#FFA" id="td_attach_wip"><? echo $tot_attach_wip; ?></td>
		                
		                <td width="70" id="td_sewing_comm"><? echo $tot_sewing_comm; ?></td>
		                <td width="70" id="td_sewing_bal"><? echo $tot_sewing_bal; ?></td>
		                <td width="70" id="td_sewing_wip"><? echo $tot_sewing_wip; ?></td>
		                
		                <td width="70" id="td_sewing_comm"><? echo $tot_pqc_comm; ?></td>
		                <td width="70" id="td_sewing_bal"><? echo $tot_pqc_bal; ?></td>
		                <td width="70" id="td_sewing_wip"><? echo $tot_pqc_wip; ?></td>
		                
		                <td width="70" style="background:#FFA" id="td_iron_com"><? echo $tot_iron_com; ?></td>
		                <td width="70" style="background:#FFA" id="td_iron_bal"><? echo $tot_iron_bal; ?></td>
		                <td width="70" style="background:#FFA" id="td_re_iron"><? echo $tot_re_iron; ?></td>
		                
		                <td width="70" id="td_packing_comm"><? echo $tot_packing_comm; ?></td>
		                <td width="70" id="td_packing_bal"><? echo $tot_packing_bal; ?></td>
		                <td width="70">&nbsp;</td>
		                
		                <td width="70">&nbsp;</td>
		                <td width="70" id="td_shipment_com"><? echo $tot_shipment_com; ?></td>
		                
		                <td width="80" id="td_shipment_acc_bal"><? echo $tot_shipment_acc_bal; ?></td>
		                <td width="70">&nbsp;</td>
		                <td width="50">&nbsp;</td>
		                <td>&nbsp;</td>
		            </tr>
		        </table>
		    </div>
	    </div>
		<?
	}
	else if($type==2)
	{
		if($cbo_company_name==0) $company_name_cond=""; else $company_name_cond=" and a.company_name='$cbo_company_name' ";
		
		if($cbo_buyer_name==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="")
				{
					$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
				}
				else  $buyer_id_cond="";
			}
			else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
		}
				
		if($db_type==0)
		{
			if($year_id!=0) $year_cond=" and year(a.insert_date)=$year_id"; else $year_cond="";	
		}
		else if($db_type==2)
		{
			$year_field_con=" and to_char(a.insert_date,'YYYY')";
			if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";	
		}
		
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
		}
		$job_style_cond="";
		if(trim(str_replace("'","",$txt_style_ref))!="") $job_style_cond=" and a.style_ref_no = '".trim(str_replace("'","",$txt_style_ref))."'";
				
		$order_cond="";
		if(trim(str_replace("'","",$txt_order))!="")
		{
			if(str_replace("'","",$txt_order_id)!="")
			{
				$order_cond=" and b.id in(".str_replace("'","",$txt_order_id).")";
			}
			else
			{
				$order_cond=" and b.po_number = '".trim(str_replace("'","",$txt_order))."'";
			}
		}
		$job_cond="";
		if(trim(str_replace("'","",$job_no))!="")
		{
			if(str_replace("'","",$job_no_id)!="")
			{
				$job_cond=" and a.id in(".str_replace("'","",$job_no_id).")";
			}
			else
			{
				$job_cond=" and a.job_no_prefix_num = '".trim(str_replace("'","",$job_no))."'";
			}
		}
		
		$ship_status_cond="";
		if($ship_status==1) $ship_status_cond="and b.shiping_status in (1,2)"; else if($ship_status==2) $ship_status_cond="and b.shiping_status in (3)";
		
		ob_start();
		
		$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
		$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
		$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where file_type=1 and form_name='knit_order_entry'",'master_tble_id','image_location');
		
		$sql="select a.buyer_name, a.style_ref_no, a.gauge, a.job_no_prefix_num as job_prefix, a.job_no, a.order_uom, a.total_set_qnty as ratio, b.id as po_id, b.po_number, b.po_quantity, b.plan_cut, b.excess_cut, b.shiping_status, b.pub_shipment_date, c.production_type, c.production_quantity as production_quantity, c.re_production_qty,c.production_date
		
		 from wo_po_details_master a, wo_po_break_down b, pro_garments_production_mst c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.garments_nature=100 and c.production_type in (1,3,4,5,7,8,11,52,56,76,67) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_name_cond $buyer_id_cond $date_cond $job_style_cond $order_cond $job_cond $year_cond $ship_status_cond order by a.id ASC";
		//echo $sql;
		$sql_result=sql_select($sql);	
		$po_arr=array(); $production_arr=array(); $tot_rows=0; $poIds='';
		$style_arr=array();
		$production_today_arr=array();
		$pre_vious_date=date('d-M-y',strtotime(' -1 day'));
		$pre_vious_date=strtoupper($pre_vious_date);
		foreach($sql_result as $row)
		{
			$tot_rows++;
			$poIds.=$row[csf("po_id")].",";
			$style_arr[$row[csf("po_id")]]=$row[csf("style_ref_no")];
			$po_arr[$row[csf("po_id")]]=$row[csf("buyer_name")].'___'.$row[csf("style_ref_no")].'___'.$row[csf("gauge")].'___'.$row[csf("job_no_prefix_num")].'___'.$row[csf("job_no")].'___'.$row[csf("po_number")].'___'.$row[csf("po_quantity")].'___'.$row[csf("plan_cut")].'___'.$row[csf("excess_cut")].'___'.$row[csf("shiping_status")].'___'.$row[csf("pub_shipment_date")];

			$style_wise_data[$row[csf("style_ref_no")]]['buyer_name']=$row[csf("buyer_name")];
			$style_wise_data[$row[csf("style_ref_no")]]['gauge']=$row[csf("gauge")];
			$style_wise_data[$row[csf("style_ref_no")]]['job_no_prefix_num']=$row[csf("job_no_prefix_num")];
			$style_wise_data[$row[csf("style_ref_no")]]['job_no']=$row[csf("job_no")];
			$style_wise_data[$row[csf("style_ref_no")]]['po_number'].=$row[csf("po_number")]."***";
			$style_wise_data[$row[csf("style_ref_no")]]['shiping_status'].=$row[csf("shiping_status")]."***";
			$style_wise_data[$row[csf("style_ref_no")]]['pub_shipment_date'].=$row[csf("pub_shipment_date")]."***";
			$style_wise_data[$row[csf("style_ref_no")]]['po_quantity']+=$row[csf("po_quantity")];
			$style_wise_data[$row[csf("style_ref_no")]]['plan_cut']+=$row[csf("plan_cut")];
			$style_wise_data[$row[csf("style_ref_no")]]['excess_cut']+=$row[csf("excess_cut")];
			
			$production_arr[$row[csf("style_ref_no")]][$row[csf("production_type")]]['g']+=$row[csf("production_quantity")];
			$production_arr[$row[csf("style_ref_no")]][$row[csf("production_type")]]['r']+=$row[csf("re_production_qty")];
			if($row[csf("production_date")]==$pre_vious_date)
			{
				$production_today_arr[$row[csf("style_ref_no")]][$row[csf("production_type")]]['g']+=$row[csf("production_quantity")];
				$production_today_arr[$row[csf("style_ref_no")]][$row[csf("production_type")]]['r']+=$row[csf("production_quantity")];
			}
			
			
		}
		// echo "<pre>";
		// print_r($production_today_arr);
		// echo "</pre>";
		unset($sql_result);
		
		$poIds=chop($poIds,','); $poIds_cond="";
		if($db_type==2 && $tot_rows>1000)
		{
			$poIds_cond=" and (";
			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$poIds_cond.=" po_break_down_id in($ids) or ";
			}
			$poIds_cond=chop($poIds_cond,'or ');
			$poIds_cond.=")";
		}
		else
		{
			$poIds_cond=" and po_break_down_id in ($poIds)";
		}
		
		$inspection_arr=array();
		$sql_ins="select po_break_down_id, inspection_qnty,inspection_date from pro_buyer_inspection where status_active=1 and is_deleted=0 $poIds_cond";
		$sql_ins_result=sql_select($sql_ins);
		foreach($sql_ins_result as $row)
		{
			$inspection_arr[$style_arr[$row[csf("po_break_down_id")]]]['ins']+=$row[csf("inspection_qnty")];
			$inspection_arr[$style_arr[$row[csf("po_break_down_id")]]]['ins'][$row[csf("inspection_date")]]+=$row[csf("inspection_qnty")];
		}
		unset($sql_ins_result);
		$wash_arr=array();
		$w_po_cond=str_replace("po_break_down_id", "po_id", $poIds_cond);
		$sql_wash="select po_id, qcpass_qty,production_date from subcon_embel_production_dtls where status_active=1 and is_deleted=0 $w_po_cond ";
		$sql_wash_result=sql_select($sql_wash);
		foreach($sql_wash_result as $row)
		{

			$wash_arr[$style_arr[$row[csf("po_id")]]]['wash']+=$row[csf("qcpass_qty")];
			if($row[csf("production_date")]==$pre_vious_date)
			{
				$wash_arr[$style_arr[$row[csf("po_id")]]]['wash_today']+=$row[csf("qcpass_qty")];
			}
			
		}
		unset($sql_wash_result);
		
		$ex_factory_arr=array();
		$ex_factory_data=sql_select("select po_break_down_id, MAX(ex_factory_date) AS ex_factory_date, sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) AS ex_factory_qnty from pro_ex_factory_mst where status_active=1 and is_deleted=0 $poIds_cond group by po_break_down_id");

		foreach($ex_factory_data as $exRow)
		{

			$ex_factory_arr[$style_arr[$exRow[csf("po_break_down_id")]]]['date']=$exRow[csf('ex_factory_date')];
			$ex_factory_arr[$style_arr[$exRow[csf("po_break_down_id")]]]['qty']=$exRow[csf('ex_factory_qnty')];
		}
		// echo "<pre>";
		// print_r($ex_factory_arr);
		// echo "</pre>";
		unset($ex_factory_data);
		
		
		//echo $pre_vious_date;
		
		?>
		<div>
	        <div>
	            <table  cellspacing="0" >
	               
	                <tr style="border:none;">
	                	
	                    <td  align="left" style="border:none; font-size:14px;">
	                        <p >
	                        	<table>
	                        		<tr>
	                        			<td>
                                             <!-- <img  src="../../../<?// echo $image_location; ?>" height='60' width='60' /> -->
											<?
											 $image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$cbo_company_name'","image_location");
											?>
											 <img  src='../../../<? echo $image_location; ?>' height='20' width='40' />
									    </td>
	                        			<td style="font-size:16px; font-weight:bold">Company Name : <? echo $company_library[$cbo_company_name]; ?></td>
	                        		</tr>
	                        	</table>
	                        </p>
	                    	<p   style="border:none;font-size:16px; font-weight:bold" ><?  echo $report_title; ?></p>
	                        <p style="font-weight:bold;">Production Date : <? echo date('d-m-Y',strtotime(' -1 day')); ?></p>                                
	                        <p style="font-weight:bold;">Report  Date : <? echo date('d-m-Y'); ?></p>   
	                                                     
	                    </td>
	                </tr>
	                
	            </table>
	        </div>
	        <table width="3470" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
	            <thead>
	            	<tr>
	                	<th rowspan="2" width="30">Sl.</th> 
	                	<th colspan="8" style="background:#CCFF99">Order Details</th>
	                    <th colspan="32">Style Wise Production Summary</th>
	                    <th colspan="6"  style="background:#CCFF99">Shipment Status</th>
	                </tr>
	                <tr>
	                    <th width="100">Buyer</th>
	                    <th width="100">Style</th>
	                    <th width="100">job no</th>
	                    <th width="80">GG</th>
	                    <th width="50">Photo</th>
	                   
	                    <th width="80">Order Qty</th>
	                    <th width="80">Plan Knit Qty</th>
	                    <th width="60">Ex. Knit %</th>
	                    
	                    <th width="70" style="background:#FFA">Knitting Today</th>
	                    <th width="70" style="background:#FFA">Knitting Complete</th>
	                    <th width="70" style="background:#FFA">Knitting Balance</th>
	                    <th width="70" style="background:#FFA">Knitting WIP</th>
	                    
	                    <th width="70">1st Inspection Today</th>
	                    <th width="70">1st Inspection Complete</th>
	                    <th width="70">1st Inspection Balance</th>
	                    <th width="70">1st Inspection WIP</th>
	                    
	                    <th width="70" style="background:#FFA">Make-Up Today</th>
	                    <th width="70" style="background:#FFA">Make-Up Complete</th>
	                    <th width="70" style="background:#FFA">Make-up Balance</th>
	                    <th width="70" style="background:#FFA">Make-Up WIP</th>
	                    
	                    <th width="70">Wash Today</th>
	                    <th width="70">Wash Complete</th>
	                    <th width="70">Wash Balance</th>
	                    <th width="70">Wash WIP</th>
	                    
	                    <th width="70" style="background:#FFA">Attachment Today</th>
	                    <th width="70" style="background:#FFA">Attachment Complete</th>
	                    <th width="70" style="background:#FFA">Attach. Balance</th>
	                    <th width="70" style="background:#FFA">Attach. WIP</th>
	                    
	                    <th width="70">Sewing Today</th>
	                    <th width="70">Sewing Complete</th>
	                    <th width="70">Sewing Balance</th>
	                    <th width="70">Sewing WIP</th>
	                    
	                    <th width="70" style="background:#FFA">Iron Today</th>
	                    <th width="70" style="background:#FFA">Iron Complete</th>
	                    <th width="70" style="background:#FFA">Iron Balance</th>
	                    <th width="70" style="background:#FFA">Re-Iron</th>
	                    
	                    <th width="70">Packing Today</th>
	                    <th width="70">Packing Complete</th>
	                    <th width="70">Packing Balance</th>
	                    <th width="70">Packing Complete %</th>
	                    
	                    <th width="70">Status</th>
	                    <th width="70">Shipment Qty</th>
	                    
	                    <th width="80">Shipment Access/ Balance</th>
	                    <th width="70">Shipment Date</th>
	                    <th width="50">R. Days</th>
	                    
	                    <th >Shipment Status</th>
	                </tr>
	            </thead>
	        </table>
	        <div style="max-height:380px; overflow-y:scroll; width:3470px" id="scroll_body">
	            <table cellspacing="0" border="1" class="rpt_table" width="3450" id="table_body" rules="all">
	            <? $i=1;
	            $tot_knitting_com_today=$tot_inspection_qnty_today=0;
				foreach($style_wise_data as $style=>$value)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$buyer_name=$style_ref_no=$gauge=$job_no_prefix_num=$job_no=$po_number=$shiping_status=""; $pubshipment_date="";
					$po_qty=$plan_cut=$excess_cut=0;
					/*
					$exdata=explode("___",$value);
					$buyer_name=$exdata[0];
					$style_ref_no=$exdata[1];
					$gauge=$exdata[2];
					$job_no_prefix_num=$exdata[3];
					$job_no=$exdata[4];
					$po_number=$exdata[5];
					
					$po_qty=$exdata[6];
					$plan_cut=$exdata[7];
					$excess_cut=$exdata[8];
					
					$shiping_status=$exdata[9];
					$pubshipment_date=$exdata[10];
					*/


					$buyer_name=$value['buyer_name'];
					$style_ref_no=$style;
					$gauge=$value['gauge'];
					$job_no_prefix_num=$value['job_no_prefix_num'];
					$job_no=$value['job_no'];
					$po_number=implode(",", array_unique(array_filter(explode("***", $value['po_number']))));
					
					$po_qty=$value['po_quantity'];
					$plan_cut=$value['plan_cut'];
					$excess_cut=$value['excess_cut'];
					
					$shiping_status=implode(",", array_unique(array_filter(explode("***", $value['shiping_status']))));
					$pubshipment_date=implode(",", array_unique(array_filter(explode("***", $value['pub_shipment_date']))));
					
					$knitting_com=$inspection_qnty=$makeup_comp=$wash_comp=$attach_comm=$sewing_comm=$iron_com=$re_iron=$packing_comm=$shipment_com=$knitting_com_today=$inspection_qnty_today=$makeup_comp_today=0;

					$poid=$style;
					//$pre_vious_date="'".strtoupper($pre_vious_date)."'";
					//$pre_vious_date=trim($pre_vious_date);
					//echo $pre_vious_date;
					//$knitting_com=$production_arr[$poid][1]['g'];
					$knitting_com=$production_arr[$poid][76]['g'];
					$knitting_com_today=$production_today_arr[$poid][76]['g'];
					//echo $production_today_arr[$poid][76]['g'][$pre_vious_date];
					//$inspection_qnty=$inspection_arr[$poid]['ins'];
					$inspection_qnty=$production_arr[$poid][52]['g'];
					$inspection_qnty_today=$production_today_arr[$poid][52]['g'];
					//$makeup_comp=$production_arr[$poid][4]['g'];
					$makeup_comp=$production_arr[$poid][56]['g'];
					$makeup_comp_today=$production_today_arr[$poid][56]['g'];
					//$wash_comp=$production_arr[$poid][3]['g'];
					$wash_comp=$wash_arr[$poid]['wash'];
					$wash_comp_today=$wash_arr[$poid]['wash_today'];
					$attach_comm=$production_arr[$poid][11]['g'];
					$attach_comm_today=$production_today_arr[$poid][11]['g'];
					$sewing_comm=$production_arr[$poid][5]['g'];
					$sewing_comm_today=$production_today_arr[$poid][5]['g'];
					$iron_com=$production_arr[$poid][67]['g'];
					$iron_com_today=$production_today_arr[$poid][67]['g'];
					$re_iron=$production_arr[$poid][67]['r'];
					$re_iron_today=$production_today_arr[$poid][67]['r'];
					$packing_comm=$production_arr[$poid][8]['g'];
					$packing_comm_today=$production_today_arr[$poid][8]['g'];
					$shipment_com=$ex_factory_arr[$poid]['qty'];
					
					$kintting_bal=$inspe_bal=$makeup_bal=$wash_bal=$attach_bal=$sewing_bal=$iron_bal=$packing_bal=$shipment_acc_bal=0;
					$kintting_bal=$plan_cut-$knitting_com;
					$inspe_bal=$plan_cut-$inspection_qnty;
					$makeup_bal=$plan_cut-$makeup_comp;
					$wash_bal=$plan_cut-$wash_comp;
					$attach_bal=$plan_cut-$attach_comm;
					$sewing_bal=$plan_cut-$sewing_comm;
					$iron_bal=$plan_cut-$iron_com;
					$packing_bal=$plan_cut-$packing_comm;
					$shipment_acc_bal=$po_qty-$shipment_com;
					
					$knitting_wip=$ins_wip=$makeup_wip=$wash_wip=$attach_wip=$sewing_wip=$packing_percent=0;
					$knitting_wip=$knitting_com-$inspection_qnty;
					$ins_wip=$inspection_qnty-$makeup_comp;
					$makeup_wip=$makeup_comp-$wash_comp;
					$wash_wip=$wash_comp-$attach_comm;
					$attach_wip=$attach_comm-$sewing_comm;
					$sewing_wip=$sewing_comm-$iron_com;
					$packing_percent=($packing_comm/$po_qty)*100;
					
					$packing_status="Running"; $shpment_date='';
					
					if($packing_comm>=$plan_cut) $packing_status="Complete"; else if($packing_comm<$plan_cut) $packing_status="Running";
					
					$shpment_date=$ex_factory_arr[$poid]['date'];
					$reaming_days=0;
					$reaming_days=datediff("d",date("Y-m-d"),$pubshipment_date);
					if($shiping_status==3) $reaming_days=0;
					
					?>
	                 <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
	                    <td width="30"><? echo $i; ?></td>    
	                    <td width="100" style="word-break:break-all"><? echo $buyer_arr[$buyer_name]; ?>&nbsp;</td>
	                    <td width="100" style="word-break:break-all"><? echo $style_ref_no; ?>&nbsp;</td>
	                    <td width="100" style="word-break:break-all"><? echo $job_no; ?>&nbsp;</td>
	                    <td width="80" style="word-break:break-all"><? echo $gauge_arr[$gauge]; ?>&nbsp;</td>
	                    
	                    <td width="50" onClick="openmypage_image('requires/style_production_summary_report_controller.php?action=show_image&job_no=<? echo $job_no; ?>','Image View')"><img  src='../../../<? echo $imge_arr[$job_no]; ?>' height='20' width='40' /></td>
	                  
	                    <td width="80" align="right"><? echo $po_qty; ?></td>
	                    <td width="80" align="right"><? echo $plan_cut; ?></td>
	                    <td width="60" align="right"><? echo number_format($excess_cut,2); ?></td>
	                    
	                    <td width="70" align="right"><? echo $knitting_com_today; ?></td>
	                    <td width="70" align="right"><? echo $knitting_com; ?></td>
	                    <td width="70" align="right"><? echo $kintting_bal; ?></td>
	                    <td width="70" align="right"><? echo $knitting_wip; ?></td>
	                    
	                    <td width="70" align="right"><? echo $inspection_qnty_today; ?></td>
	                    <td width="70" align="right"><? echo $inspection_qnty; ?></td>
	                    <td width="70" align="right"><? echo $inspe_bal; ?></td>
	                    <td width="70" align="right"><? echo $ins_wip; ?></td>
	                    
	                    <td width="70" align="right"><? echo $makeup_comp_today; ?></td>
	                    <td width="70" align="right"><? echo $makeup_comp; ?></td>
	                    <td width="70" align="right"><? echo $makeup_bal; ?></td>
	                    <td width="70" align="right"><? echo $makeup_wip; ?></td>
	                    
	                    <td width="70" align="right"><? echo $wash_comp_today; ?></td>
	                    <td width="70" align="right"><? echo $wash_comp; ?></td>
	                    <td width="70" align="right"><? echo $wash_bal; ?></td>
	                    <td width="70" align="right"><? echo $wash_wip; ?></td>
	                    
	                    <td width="70" align="right"><? echo $attach_comm_today; ?></td>
	                    <td width="70" align="right"><? echo $attach_comm; ?></td>
	                    <td width="70" align="right"><? echo $attach_bal; ?></td>
	                    <td width="70" align="right"><? echo $attach_wip; ?></td>
	                    
	                    <td width="70" align="right"><? echo $sewing_comm_today; ?></td>
	                    <td width="70" align="right"><? echo $sewing_comm; ?></td>
	                    <td width="70" align="right"><? echo $sewing_bal; ?></td>
	                    <td width="70" align="right"><? echo $sewing_wip; ?></td>
	                    
	                    <td width="70" align="right"><? echo $iron_com_today; ?></td>
	                    <td width="70" align="right"><? echo $iron_com; ?></td>
	                    <td width="70" align="right"><? echo $iron_bal; ?></td>
	                    <td width="70" align="right"><? echo $re_iron; ?></td>
	                    
	                    <td width="70" align="right"><? echo $packing_comm_today; ?></td>
	                    <td width="70" align="right"><? echo $packing_comm; ?></td>
	                    <td width="70" align="right"><? echo $packing_bal; ?></td>
	                    <td width="70" align="right"><? echo number_format($packing_percent,2); ?></td>
	                    
	                    <td width="70" style="word-break:break-all"><? echo $packing_status; ?></td>
	                    <td width="70" align="right"><? echo $shipment_com; ?></td>
	                    
	                    <td width="80" align="right"><? echo $shipment_acc_bal; ?></td>
	                    <td width="70" style="font-size:11px"><? echo change_date_format($shpment_date); ?></td>
	                    <td width="50" align="right" title="<? echo "Pub Ship Date: ".change_date_format($pubshipment_date); ?>"><? echo $reaming_days; ?></td>
	                    <td style="word-break:break-all; font-size:11px" ><? echo $shipment_status[$shiping_status]; ?></td>
	                </tr>
					<?
					$i++;
					$tot_knitting_com+=$knitting_com;
					$tot_knitting_com_today+=$knitting_com_today;
					$tot_kintting_bal+=$kintting_bal;
					$tot_knitting_wip+=$knitting_wip;
					
					$tot_inspection_qnty+=$inspection_qnty;
					$tot_inspection_qnty_today+=$inspection_qnty_today;
					$tot_inspe_bal+=$inspe_bal;
					$tot_ins_wip+=$ins_wip;
					
					$tot_makeup_comp+=$makeup_comp;
					$tot_makeup_comp_today+=$makeup_comp_today;
					$tot_makeup_bal+=$makeup_bal;
					$tot_makeup_wip+=$makeup_wip;
					
					$tot_wash_comp+=$wash_comp;
					$tot_wash_comp_today+=$wash_comp_today;
					$tot_wash_bal+=$wash_bal;
					$tot_wash_wip+=$wash_wip;
					
					$tot_attach_comm+=$attach_comm;
					$tot_attach_comm_today+=$attach_comm_today;
					$tot_attach_bal+=$attach_bal;
					$tot_attach_wip+=$attach_wip;
					
					$tot_sewing_comm+=$sewing_comm;
					$tot_sewing_comm_today+=$sewing_comm_today;
					$tot_sewing_bal+=$sewing_bal;
					$tot_sewing_wip+=$sewing_wip;
					
					$tot_iron_com+=$iron_com;
					$tot_iron_com_today+=$iron_com_today;
					$tot_iron_bal+=$iron_bal;
					$tot_re_iron+=$re_iron;
					
					$tot_packing_comm+=$packing_comm;
					$tot_packing_comm_today+=$packing_comm_today;
					$tot_packing_bal+=$packing_bal;
					$tot_shipment_com+=$shipment_com;
					$tot_shipment_acc_bal+=$shipment_acc_bal;
				}
				?>
	            </table>
	        </div>
	        <table width="3470" cellspacing="0" border="1" class="tbl_bottom" rules="all">
	            <tr>
	            	<td width="30">&nbsp;</td> 
	                <td width="100">&nbsp;</td>
	                
	                <td width="80">&nbsp;</td>
	                <td width="100">&nbsp;</td>
	                <td width="50">&nbsp;</td>
	                <td width="100">&nbsp;</td>
	                <td width="80">&nbsp;</td>
	                <td width="80">&nbsp;</td>
	                <td width="60">Total:</td>
	                
	                <td width="70" style="background:#FFA" id="td_knitting_com_today"><? echo $tot_knitting_com_today; ?></td>
	                <td width="70" style="background:#FFA" id="td_knitting_com"><? echo $tot_knitting_com; ?></td>
	                <td width="70" style="background:#FFA" id="td_knitting_bal"><? echo $tot_kintting_bal; ?></td>
	                <td width="70" style="background:#FFA" id="td_knitting_wip"><? echo $tot_knitting_wip; ?></td>
	                
	                <td width="70" id="td_inspection_qnty_today"><? echo $tot_inspection_qnty_today; ?></td>
	                <td width="70" id="td_inspection_qnty"><? echo $tot_inspection_qnty; ?></td>
	                <td width="70" id="td_inspe_bal"><? echo $tot_inspe_bal; ?></td>
	                <td width="70" id="td_ins_wip"><? echo $tot_ins_wip; ?></td>
	                
	                <td width="70" style="background:#FFA" id="td_makeup_comp_today"><? echo $tot_makeup_comp_today; ?></td>
	                <td width="70" style="background:#FFA" id="td_makeup_comp"><? echo $tot_makeup_comp; ?></td>
	                <td width="70" style="background:#FFA" id="td_makeup_bal"><? echo $tot_makeup_bal; ?></td>
	                <td width="70" style="background:#FFA" id="td_makeup_wip"><? echo $tot_makeup_wip; ?></td>
	                
	                <td width="70" id="td_wash_comp_today"><? echo $tot_wash_comp_today; ?></td>
	                <td width="70" id="td_wash_comp"><? echo $tot_wash_comp; ?></td>
	                <td width="70" id="td_wash_bal"><? echo $tot_wash_bal; ?></td>
	                <td width="70" id="td_wash_wip"><? echo $tot_wash_wip; ?></td>
	                
	                <td width="70" style="background:#FFA" id="td_attach_comm_today"><? echo $tot_attach_comm_today; ?></td>
	                <td width="70" style="background:#FFA" id="td_attach_comm"><? echo $tot_attach_comm; ?></td>
	                <td width="70" style="background:#FFA" id="td_attach_bal"><? echo $tot_attach_bal; ?></td>
	                <td width="70" style="background:#FFA" id="td_attach_wip"><? echo $tot_attach_wip; ?></td>
	                
	                <td width="70" id="td_sewing_comm_today"><? echo $tot_sewing_comm_today; ?></td>
	                <td width="70" id="td_sewing_comm"><? echo $tot_sewing_comm; ?></td>
	                <td width="70" id="td_sewing_bal"><? echo $tot_sewing_bal; ?></td>
	                <td width="70" id="td_sewing_wip"><? echo $tot_sewing_wip; ?></td>
	                
	                <td width="70" style="background:#FFA" id="td_iron_com_today"><? echo $tot_iron_com_today; ?></td>
	                <td width="70" style="background:#FFA" id="td_iron_com"><? echo $tot_iron_com; ?></td>
	                <td width="70" style="background:#FFA" id="td_iron_bal"><? echo $tot_iron_bal; ?></td>
	                <td width="70" style="background:#FFA" id="td_re_iron"><? echo $tot_re_iron; ?></td>
	                
	                <td width="70" id="td_packing_comm"><? echo $tot_packing_comm; ?></td>
	                <td width="70" id="td_packing_comm_today"><? echo $tot_packing_comm_today; ?></td>
	                <td width="70" id="td_packing_bal"><? echo $tot_packing_bal; ?></td>
	                <td width="70">&nbsp;</td>
	                
	                <td width="70">&nbsp;</td>
	                <td width="70" id="td_shipment_com"><? echo $tot_shipment_com; ?></td>
	                
	                <td width="80" id="td_shipment_acc_bal"><? echo $tot_shipment_acc_bal; ?></td>
	                <td width="70">&nbsp;</td>
	                <td width="50">&nbsp;</td>
	                <td >&nbsp;</td>
	            </tr>
	        </table>
	        
	    </div>
		<?
	}else if($type==3)
	{
		if($cbo_company_name==0) $company_name_cond=""; else $company_name_cond=" and a.company_name='$cbo_company_name' ";
		
		if($cbo_buyer_name==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="")
				{
					$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
				}
				else  $buyer_id_cond="";
			}
			else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
		}
				
		if($db_type==0)
		{
			if($year_id!=0) $year_cond=" and year(a.insert_date)=$year_id"; else $year_cond="";	
		}
		else if($db_type==2)
		{
			$year_field_con=" and to_char(a.insert_date,'YYYY')";
			if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";	
		}
		
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
		}
		$job_style_cond="";
		if(trim(str_replace("'","",$txt_style_ref))!="") $job_style_cond=" and a.style_ref_no = '".trim(str_replace("'","",$txt_style_ref))."'";
				
		$order_cond="";
		if(trim(str_replace("'","",$txt_order))!="")
		{
			if(str_replace("'","",$txt_order_id)!="")
			{
				$order_cond=" and b.id in(".str_replace("'","",$txt_order_id).")";
			}
			else
			{
				$order_cond=" and b.po_number = '".trim(str_replace("'","",$txt_order))."'";
			}
		}
		$job_cond="";
		if(trim(str_replace("'","",$job_no))!="")
		{
			if(str_replace("'","",$job_no_id)!="")
			{
				$job_cond=" and a.id in(".str_replace("'","",$job_no_id).")";
			}
			else
			{
				$job_cond=" and a.job_no_prefix_num = '".trim(str_replace("'","",$job_no))."'";
			}
		}
		
		$ship_status_cond="";
		if($ship_status==1) $ship_status_cond="and b.shiping_status in (1,2)"; else if($ship_status==2) $ship_status_cond="and b.shiping_status in (3)";
		
		ob_start();
		
		$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
		$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
		$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where file_type=1 and form_name='knit_order_entry'",'master_tble_id','image_location');
		
		$sql="select a.buyer_name, a.style_ref_no, a.gauge, a.job_no_prefix_num as job_prefix, a.job_no, a.order_uom, a.total_set_qnty as ratio, b.id as po_id, b.po_number, b.po_quantity, b.plan_cut, b.excess_cut, b.shiping_status, b.pub_shipment_date, c.production_type, c.production_quantity as production_quantity, c.re_production_qty,c.production_date ,d.color_number_id	from wo_po_details_master a, wo_po_break_down b left join wo_po_color_size_breakdown d on b.id=d.po_break_down_id and d.status_active=1, pro_garments_production_mst c  where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.garments_nature=100 and c.production_type in (1,3,4,5,7,8,11,52,56,76,67) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_name_cond $buyer_id_cond $date_cond $job_style_cond $order_cond $job_cond $year_cond $ship_status_cond group by a.buyer_name, a.style_ref_no, a.gauge, a.job_no_prefix_num , a.job_no, a.order_uom, a.total_set_qnty , b.id , b.po_number, b.po_quantity, b.plan_cut, b.excess_cut, 
		b.shiping_status, b.pub_shipment_date, c.production_type, c.production_quantity , c.re_production_qty,c.production_date ,d.color_number_id order by a.job_no ASC ";
		 
		// echo $sql;
		$sql_result=sql_select($sql);	
		$po_arr=array(); $production_arr=array(); $tot_rows=0; $poIds='';
		$style_arr=array();
		$production_today_arr=array();
		$pre_vious_date=date('d-M-y',strtotime(' -1 day'));
		$pre_vious_date=strtoupper($pre_vious_date);
		// $current_date = date("Y-m-d",strtotime(add_date(date("Y-m-d",time()),0)));
		// $prev_date = date('Y-m-d', strtotime('-1 day', strtotime($current_date))); 
		// echo $pre_vious_date."=>";
		foreach($sql_result as $row)
		{
			$tot_rows++;
			$poIds.=$row[csf("po_id")].",";
			$style_arr[$row[csf("po_id")]]=$row[csf("style_ref_no")];
			$po_arr[$row[csf("po_id")]]=$row[csf("buyer_name")].'___'.$row[csf("style_ref_no")].'___'.$row[csf("gauge")].'___'.$row[csf("job_no_prefix_num")].'___'.$row[csf("job_no")].'___'.$row[csf("po_number")].'___'.$row[csf("po_quantity")].'___'.$row[csf("plan_cut")].'___'.$row[csf("excess_cut")].'___'.$row[csf("shiping_status")].'___'.$row[csf("pub_shipment_date")];

			$style_wise_data[$row[csf("style_ref_no")]][$row[csf("color_number_id")]]['buyer_name']=$row[csf("buyer_name")];
			$style_wise_data[$row[csf("style_ref_no")]][$row[csf("color_number_id")]]['gauge']=$row[csf("gauge")];
			$style_wise_data[$row[csf("style_ref_no")]][$row[csf("color_number_id")]]['job_no_prefix_num']=$row[csf("job_no_prefix_num")];
			$style_wise_data[$row[csf("style_ref_no")]][$row[csf("color_number_id")]]['job_no']=$row[csf("job_no")];
			
			$style_wise_data[$row[csf("style_ref_no")]][$row[csf("color_number_id")]]['po_number'].=$row[csf("po_number")]."***";
			$style_wise_data[$row[csf("style_ref_no")]][$row[csf("color_number_id")]]['shiping_status'].=$row[csf("shiping_status")]."***";
			$style_wise_data[$row[csf("style_ref_no")]][$row[csf("color_number_id")]]['pub_shipment_date'].=$row[csf("pub_shipment_date")]."***";
			$style_wise_data[$row[csf("style_ref_no")]][$row[csf("color_number_id")]]['po_quantity']+=$row[csf("po_quantity")];
			$style_wise_data[$row[csf("style_ref_no")]][$row[csf("color_number_id")]]['plan_cut']+=$row[csf("plan_cut")];
			$style_wise_data[$row[csf("style_ref_no")]][$row[csf("color_number_id")]]['po_id']+=$row[csf("po_id")];
			$style_wise_data[$row[csf("style_ref_no")]][$row[csf("color_number_id")]]['excess_cut']+=$row[csf("excess_cut")];
			
			$production_arr[$row[csf("style_ref_no")]][$row[csf("color_number_id")]][$row[csf("production_type")]]['g']+=$row[csf("production_quantity")];
			$production_arr[$row[csf("style_ref_no")]][$row[csf("color_number_id")]][$row[csf("production_type")]]['r']+=$row[csf("re_production_qty")];
			if($row[csf("production_date")]==$pre_vious_date)
			{
				$production_today_arr[$row[csf("style_ref_no")]][$row[csf("color_number_id")]][$row[csf("production_type")]]['g']+=$row[csf("production_quantity")];
				$production_today_arr[$row[csf("style_ref_no")]][$row[csf("color_number_id")]][$row[csf("production_type")]]['r']+=$row[csf("production_quantity")];
			}
			
			$job_arr[$row[csf("job_no")]]=$row[csf("job_no")];
			
			$color_arr[$row[csf("color_number_id")]]=$row[csf("color_number_id")];
		}
		//  echo "<pre>";
		//  print_r($style_wise_data);
		// echo "</pre>";
		unset($sql_result);
		


			//========================================================================================
			$color_data=sql_select(" select a.style_ref_no,b.color_number_id,b.job_no_mst,sum(b.order_quantity) as order_quantity,sum(b.plan_cut_qnty) as plan_cut_qnty  from wo_po_details_master a,wo_po_color_size_breakdown b
			where a.job_no=b.job_no_mst ".where_con_using_array($job_arr,1,'a.job_no')." ".where_con_using_array($color_arr,1,'b.color_number_id')." group by b.color_number_id,b.job_no_mst,a.style_ref_no order by b.job_no_mst ASC");

		
			foreach($color_data as $val){
				$style_wise_data[$val[csf("style_ref_no")]][$val[csf("color_number_id")]]['order_quantity']+=$val[csf("order_quantity")];
				$style_wise_data[$val[csf("style_ref_no")]][$val[csf("color_number_id")]]['plan_cut_qnty']+=$val[csf("plan_cut_qnty")];
			}




		$poIds=chop($poIds,','); $poIds_cond="";
		if($db_type==2 && $tot_rows>1000)
		{
			$poIds_cond=" and (";
			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$poIds_cond.=" po_break_down_id in($ids) or ";
			}
			$poIds_cond=chop($poIds_cond,'or ');
			$poIds_cond.=")";
		}
		else
		{
			$poIds_cond=" and po_break_down_id in ($poIds)";
		}
		
		$inspection_arr=array();
		$sql_ins="select po_break_down_id, inspection_qnty,inspection_date from pro_buyer_inspection where status_active=1 and is_deleted=0 $poIds_cond";
		$sql_ins_result=sql_select($sql_ins);
		foreach($sql_ins_result as $row)
		{
			$inspection_arr[$style_arr[$row[csf("po_break_down_id")]]]['ins']+=$row[csf("inspection_qnty")];
			$inspection_arr[$style_arr[$row[csf("po_break_down_id")]]]['ins'][$row[csf("inspection_date")]]+=$row[csf("inspection_qnty")];
		}
		unset($sql_ins_result);
		$wash_arr=array();
		$w_po_cond=str_replace("po_break_down_id", "po_id", $poIds_cond);
		$sql_wash="select po_id, qcpass_qty,production_date,color_id from subcon_embel_production_dtls where status_active=1 and is_deleted=0 $w_po_cond ";
		$sql_wash_result=sql_select($sql_wash);
		foreach($sql_wash_result as $row)
		{

			$wash_arr[$style_arr[$row[csf("po_id")]]][$row[csf("color_id")]]['wash']+=$row[csf("qcpass_qty")];
			if($row[csf("production_date")]==$pre_vious_date)
			{
				$wash_arr[$style_arr[$row[csf("po_id")]]][$row[csf("color_id")]]['wash_today']+=$row[csf("qcpass_qty")];
			}
			
		}
		unset($sql_wash_result);
		
		$ex_factory_arr=array();
		$ex_factory_data=sql_select("select po_break_down_id, MAX(ex_factory_date) AS ex_factory_date, sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) AS ex_factory_qnty from pro_ex_factory_mst where status_active=1 and is_deleted=0 $poIds_cond group by po_break_down_id");

		foreach($ex_factory_data as $exRow)
		{

			// $ex_factory_arr[$style_arr[$exRow[csf("po_break_down_id")]]]['date']=$exRow[csf('ex_factory_date')];
			$ex_factory_arr[$style_arr[$exRow[csf("po_break_down_id")]]]['qty']=$exRow[csf('ex_factory_qnty')];
		}
		// echo "<pre>";
		// print_r($ex_factory_arr);
		// echo "</pre>";
		unset($ex_factory_data);
		
		$color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );
		//echo $pre_vious_date;
		

		///=====================================================knitting_data==============================================================================================
		$knitting_data=sql_select("SELECT a.serving_company, a.floor_id, c.id as prdid, d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num,f.job_no, to_char(f.insert_date,'YYYY') as year, f.buyer_name, f.style_ref_no,d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no, c.bundle_no, c.barcode_no, c.production_qnty as production_qnty, e.po_number, c.barcode_no,a.production_date from pro_garments_production_mst a,pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where  a.id=c.mst_id and c.color_size_break_down_id=d.id	and d.po_break_down_id=e.id and e.job_no_mst=f.job_no  and a.production_type=76 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and e.status_active in(1,2,3) ".where_con_using_array($color_arr,1,'d.color_number_id')." ".where_con_using_array($job_arr,1,'f.job_no')."  and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc");

		foreach($knitting_data as $row){

			$knitting_data_arr[$row[csf("style_ref_no")]][$row[csf("color_number_id")]]['knit_complete']+=$row[csf('production_qnty')];

			if($row[csf("production_date")]==$pre_vious_date)
			{
				$knitting_data_arr[$row[csf("style_ref_no")]][$row[csf("color_number_id")]]['knit_today']+=$row[csf('production_qnty')];
			}
		
		}


		///=====================================================First Inspection==============================================================================================
		$first_inspection_data=sql_select("SELECT  a.gmt_item_id, c.bundle_qty, c.production_qnty, c.color_size_break_down_id, c.reject_qty, c.replace_qty,c.defect_qty, c.mst_id,c.is_rescan,a.color_id,e.style_ref_no,f.cutting_qc_date	from ppl_cut_lay_dtls a, ppl_cut_lay_bundle b, pro_garments_production_dtls c ,wo_po_break_down d,wo_po_details_master e,pro_gmts_cutting_qc_mst f  where d.job_no_mst=e.job_no  and  b.order_id=d.id  and c.production_type=52  and c.bundle_no=b.bundle_no and c.delivery_mst_id=f.id and a.id=b.dtls_id  and a.status_active=1 and a.is_deleted=0 ".where_con_using_array($color_arr,1,'a.color_id')."  ".where_con_using_array($job_arr,1,'e.job_no')." and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ");



		

		 

		foreach($first_inspection_data as $row){

			$first_inspection_data_arr[$row[csf("style_ref_no")]][$row[csf("color_id")]]['1st_inspection_complete']+=$row[csf('production_qnty')];
		
			if($row[csf("cutting_qc_date")]==$pre_vious_date)
			{
				$first_inspection_data_arr[$row[csf("style_ref_no")]][$row[csf("color_id")]]['1st_inspection_today']+=$row[csf('production_qnty')];
			}

		}


		// echo "<pre>";
		// print_r($first_inspection_data_arr);
		
		///=====================================================Linking Output==============================================================================================
		$makeup_data=sql_select("SELECT c.id as prdid, d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, to_char(f.insert_date,'YYYY') as year, f.buyer_name, f.style_ref_no, d.item_number_id, d.country_id, d.size_number_id,d.color_number_id, c.cut_no, c.bundle_no, c.barcode_no, c.production_qnty as production_qnty, c.reject_qty, c.alter_qty, c.spot_qty, c.replace_qty, c.is_rescan, e.po_number, c.barcode_no,a.production_date  from pro_garments_production_mst a,pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where  a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_no_mst=f.job_no and a.production_type=56 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and e.status_active in (1,2,3)	 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc ");


		foreach($makeup_data as $row){

			$makeup_data_arr[$row[csf("style_ref_no")]][$row[csf("color_number_id")]]['makeup_complete']+=$row[csf('production_qnty')];
			if($row[csf("production_date")]==$pre_vious_date)
			{
				$makeup_data_arr[$row[csf("style_ref_no")]][$row[csf("color_number_id")]]['makeup_today']+=$row[csf('production_qnty')];
			}
		
		}
		
		
		///=====================================================Wet Production [Sweater]=========================================================================================
		// $wash_data=sql_select("select a.id, a.batch_no, a.process_id, a.extention_no, a.operation_type, a.batch_weight, a.batch_date, a.color_id, sum(b.qty_pcs) as qty_pcs, b.po_id, sum(b.qty_gm) as qty_gm, c.job_no_mst, c.id as po_id, c.po_number, d.id as color_size_id, d.country_id, d.item_number_id, d.color_number_id, d.size_number_id, d.plan_cut_qnty,e.style_ref_no from pro_bundle_batch_mst a, pro_bundle_batch_dtls b, wo_po_break_down c, wo_po_color_size_breakdown d,wo_po_details_master e where a.id=b.mst_id and c.job_no_mst=e.job_no and b.po_id=c.id and c.id=d.po_break_down_id and c.job_no_mst=d.job_no_mst and d.id=b.colorsizeid  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($color_arr,1,'a.color_id')."  ".where_con_using_array($job_arr,1,'e.job_no')."	group by a.id, a.batch_no, a.process_id, a.extention_no, a.operation_type, a.batch_weight, a.batch_date, a.color_id, b.po_id, c.job_no_mst, c.id, c.po_number, d.id, d.country_id, d.item_number_id, d.color_number_id,d.size_number_id, d.plan_cut_qnty,e.style_ref_no");

		$wash_data=sql_select("SELECT a.id, a.prefix_no_num, a.sys_no, a.location_id, a.serv_company, a.recipe_id, a.job_no, b.po_id as order_id, a.floor_id, a.machine_id, a.product_date, b.production_date, b.qcpass_qty ,d.color_number_id,e.style_ref_no from subcon_embel_production_mst a, subcon_embel_production_dtls b ,wo_po_break_down c, wo_po_color_size_breakdown d,wo_po_details_master e where a.id=b.mst_id and a.entry_form=393 and a.status_active = 1 and c.job_no_mst=e.job_no and b.po_id=c.id  and c.id=d.po_break_down_id  and a.is_deleted = 0 and a.job_no='SSL-21-00381' group by a.id, a.prefix_no_num, a.sys_no, a.location_id, a.serv_company, a.recipe_id, a.job_no, b.po_id, a.floor_id, a.machine_id, a.product_date, b.production_date, b.qcpass_qty ,d.color_number_id,e.style_ref_no  order by a.id DESC");



		foreach($wash_data as $row){

			$wash_data_arr[$row[csf("style_ref_no")]][$row[csf("color_number_id")]]['wash_complete']+=$row[csf('qcpass_qty')];
			if($row[csf("production_date")]==$pre_vious_date)
			{
				$wash_data_arr[$row[csf("style_ref_no")]][$row[csf("color_number_id")]]['wash_today']+=$row[csf('qcpass_qty')];
			}
		
		}


		
			///=====================================================Attachment Complete=========================================================================================
			$attachment_data=sql_select("SELECT  a.id,a.company_id, a.po_break_down_id, a.item_number_id, a.production_date, sum(b.production_qnty) as production_qnty, a.production_type, a.reject_qnty, a.alter_qnty,	a.spot_qnty, a.wo_order_id,a.currency_id, a.exchange_rate, a.rate,e.color_number_id,d.style_ref_no from pro_garments_production_mst a,pro_garments_production_dtls b,wo_po_break_down c, wo_po_details_master d,wo_po_color_size_breakdown e  where a.id=b.mst_id  and e.id=b.color_size_break_down_id ".where_con_using_array($color_arr,1,'e.color_number_id')."  ".where_con_using_array($job_arr,1,'d.job_no')." and a.po_break_down_id=c.id and c.job_id=d.id and a.production_type='11' and b.production_type='11' and a.status_active=1 and a.is_deleted=0 	and b.status_active=1 and b.is_deleted=0 group by  a.id,a.company_id, a.po_break_down_id, a.item_number_id, a.production_date,  a.production_type, a.reject_qnty, a.alter_qnty,
			a.spot_qnty, a.wo_order_id,a.currency_id, a.exchange_rate, a.rate,e.color_number_id,d.style_ref_no  order by a.id");


			

			foreach($attachment_data as $arow){
	
				$attachment_data_arr[$arow[csf("style_ref_no")]][$arow[csf("color_number_id")]]['attachment_complete']+=$arow[csf('production_qnty')];

				if($arow[csf("production_date")]==$pre_vious_date){
					$attachment_data_arr[$arow[csf("style_ref_no")]][$arow[csf("color_number_id")]]['attachment_today']+=$arow[csf('production_qnty')];

				}
			
			}

		///=====================================================Sewing Complete=========================================================================================
		$sewing_data=sql_select("SELECT a.id,a.po_break_down_id,a.item_number_id,a.country_id, a.production_date, sum( b.production_qnty) as production_qnty, 
		a.alter_qnty, a.spot_qnty, a.reject_qnty,e.color_number_id,d.style_ref_no from pro_garments_production_mst a,pro_garments_production_dtls b 
		,wo_po_break_down c, wo_po_details_master d,wo_po_color_size_breakdown e where a.id=b.mst_id  and a.po_break_down_id=c.id and c.job_id=d.id and a.production_type='5' and a.status_active=1 and a.is_deleted=0 	and b.production_type='5' and b.status_active=1 and b.is_deleted=0  and e.id=b.color_size_break_down_id ".where_con_using_array($color_arr,1,'e.color_number_id')."  ".where_con_using_array($job_arr,1,'d.job_no')."	GROUP BY a.id,a.po_break_down_id,a.item_number_id,a.country_id, a.production_date,
		a.alter_qnty, a.spot_qnty, a.reject_qnty,e.color_number_id,d.style_ref_no ");




		foreach($sewing_data as $row){

			$sewing_data_arr[$row[csf("style_ref_no")]][$row[csf("color_number_id")]]['sewing_complete']+=$row[csf('production_qnty')];
			if($row[csf("production_date")]==$pre_vious_date){
				$sewing_data_arr[$row[csf("style_ref_no")]][$row[csf("color_number_id")]]['sewing_today']+=$row[csf('production_qnty')];

			}
		
		}


		///=====================================================Iron Complete=========================================================================================
		$iron_data=sql_select("select a.id, a.po_break_down_id, a.item_number_id, a.country_id, b.color_size_break_down_id, sum( b.production_qnty) as production_qnty, b.alter_qty, b.spot_qty, b.reject_qty,	b.re_production_qty ,e.color_number_id,d.style_ref_no,a.production_date   from  pro_garments_production_mst a, pro_garments_production_dtls b,wo_po_break_down c, wo_po_details_master d,wo_po_color_size_breakdown e  where a.id=b.mst_id  and a.po_break_down_id=c.id and c.job_id=d.id and e.id=b.color_size_break_down_id  and a.status_active=1 and a.is_deleted=0 and    b.status_active=1 and b.is_deleted=0 ".where_con_using_array($color_arr,1,'e.color_number_id')."  ".where_con_using_array($job_arr,1,'d.job_no')."  group by  a.id,  a.po_break_down_id, a.item_number_id, a.country_id, b.color_size_break_down_id, b.alter_qty, b.spot_qty,b.reject_qty, b.re_production_qty ,e.color_number_id,d.style_ref_no,a.production_date ");


		foreach($iron_data as $row){

			$iron_data_arr[$row[csf("style_ref_no")]][$row[csf("color_number_id")]]['iron_complete']+=$row[csf('production_qnty')];

			if($row[csf("production_date")]==$pre_vious_date){
				$iron_data_arr[$row[csf("style_ref_no")]][$row[csf("color_number_id")]]['iron_today']+=$row[csf('production_qnty')];

			}
		
		}

		///=====================================================Packing and Finishing========================================================================================
		$packing_finishing_data=sql_select("select a.id, a.po_break_down_id, a.item_number_id, a.country_id, b.color_size_break_down_id,sum( b.production_qnty) as production_qnty, b.alter_qty, b.spot_qty, b.reject_qty,
		b.re_production_qty ,e.color_number_id,d.style_ref_no,a.production_date  from  pro_garments_production_mst a, pro_garments_production_dtls b,wo_po_break_down c, wo_po_details_master d,wo_po_color_size_breakdown e   where  a.id=b.mst_id  and a.po_break_down_id=c.id and c.job_id=d.id and e.id=b.color_size_break_down_id  and a.status_active=1 and a.is_deleted=0 and    b.status_active=1 and b.is_deleted=0  and a.production_type='8' ".where_con_using_array($color_arr,1,'e.color_number_id')."  ".where_con_using_array($job_arr,1,'d.job_no')."  group by  a.id,  a.po_break_down_id, a.item_number_id, a.country_id, b.color_size_break_down_id,  b.alter_qty, b.spot_qty,	b.reject_qty, b.re_production_qty ,e.color_number_id,d.style_ref_no,a.production_date");


		foreach($packing_finishing_data as $row){

			$packing_finishing_data_arr[$row[csf("style_ref_no")]][$row[csf("color_number_id")]]['packing_complete']+=$row[csf('production_qnty')];
			if($row[csf("production_date")]==$pre_vious_date){
				$packing_finishing_data_arr[$row[csf("style_ref_no")]][$row[csf("color_number_id")]]['packing_today']+=$row[csf('production_qnty')];

			}
		
		}


		///=====================================================Shipment ========================================================================================
		$shipment_data=sql_select("SELECT a.id,a.po_break_down_id,a.item_number_id,MAX(ex_factory_date) AS factory_date,a.country_id,a.ex_factory_date,a.ex_factory_qnty,a.location,a.lc_sc_no,
		a.invoice_no,sum(b.production_qnty) as production_qnty,a.shiping_status ,e.color_number_id,e.color_number_id,d.style_ref_no 	from  pro_ex_factory_mst a, pro_ex_factory_dtls b,wo_po_break_down c, wo_po_details_master d,wo_po_color_size_breakdown e where a.id=b.mst_id and a.po_break_down_id=c.id and c.job_id=d.id   and e.id=b.color_size_break_down_id  and a.status_active=1 and a.is_deleted=0  ".where_con_using_array($color_arr,1,'e.color_number_id')."  ".where_con_using_array($job_arr,1,'d.job_no')." group by a.id,a.po_break_down_id,a.item_number_id,a.country_id,a.ex_factory_date,a.location,a.lc_sc_no,a.invoice_no,a.ex_factory_qnty,a.shiping_status ,e.color_number_id,e.color_number_id,d.style_ref_no  order by id ");


		foreach($shipment_data as $row){

			$shipment_data_arr[$row[csf("style_ref_no")]][$row[csf("color_number_id")]]['shipment_complete']+=$row[csf('production_qnty')];
			$ex_factory_arr[$row[csf("style_ref_no")]][$row[csf("color_number_id")]]['date']=$row[csf('factory_date')];
			if($row[csf("ex_factory_date")]==$pre_vious_date){
				$shipment_data_arr[$row[csf("style_ref_no")]][$row[csf("color_number_id")]]['shipment_today']+=$row[csf('production_qnty')];

			}
		
		}
		
		?>
		<div>
	        <div>
	            <table  cellspacing="0" >
	               
	                <tr style="border:none;">
	                	
	                    <td  align="left" style="border:none; font-size:14px;">
	                        <p >
	                        	<table>
	                        		<tr>
	                        			<td><img  src="../../../<? echo $imge_arr[$cbo_company_name]; ?>" height='60' width='60' /></td>
	                        			<td style="font-size:16px; font-weight:bold">Company Name : <? echo $company_library[$cbo_company_name]; ?></td>
	                        		</tr>
	                        	</table>
	                        </p>
	                    	<p   style="border:none;font-size:16px; font-weight:bold" ><?  echo $report_title; ?></p>
	                        <p>Production Date : <? echo date('d-m-Y',strtotime(' -1 day')); ?></p>                                
	                        <p>Report  Date : <? echo date('d-m-Y'); ?></p>   
	                                                     
	                    </td>
	                </tr>
	                
	            </table>
	        </div>
	        <table width="3520" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
	            <thead>
	            	<tr>
	                	<th rowspan="2" width="30">Sl.</th> 
	                	<th colspan="8" style="background:#CCFF99">Order Details</th>
	                    <th colspan="32">Style Closing Summary</th>
	                    <th colspan="6"  style="background:#CCFF99">Shipment Status</th>
	                </tr>
	                <tr>
	                    <th width="100">Buyer</th>
	                    <th width="100">Style</th>
						<th width="100">Job No</th>
	                    <th width="80">GG</th>
	                    <th width="100">Gmts. Color</th>
	                   
	                    <th width="80">Order Qty</th>
	                    <th width="80">Plan Knit Qty</th>
	                    <th width="60">Ex. Knit %</th>
	                    
	                    <th width="70" style="background:#FFA">Knitting Today</th>
	                    <th width="70" style="background:#FFA">Knitting Complete</th>
	                    <th width="70" style="background:#FFA">Knitting Balance</th>
	                    <th width="70" style="background:#FFA">Knitting WIP</th>
	                    
	                    <th width="70">1st Inspection Today</th>
	                    <th width="70">1st Inspection Complete</th>
	                    <th width="70">1st Inspection Balance</th>
	                    <th width="70">1st Inspection WIP</th>
	                    
	                    <th width="70" style="background:#FFA">Make-Up Today</th>
	                    <th width="70" style="background:#FFA">Make-Up Complete</th>
	                    <th width="70" style="background:#FFA">Make-up Balance</th>
	                    <th width="70" style="background:#FFA">Make-Up WIP</th>
	                    
	                    <th width="70">Wash Today</th>
	                    <th width="70">Wash Complete</th>
	                    <th width="70">Wash Balance</th>
	                    <th width="70">Wash WIP</th>
	                    
	                    <th width="70" style="background:#FFA">Attachment Today</th>
	                    <th width="70" style="background:#FFA">Attachment Complete</th>
	                    <th width="70" style="background:#FFA">Attach. Balance</th>
	                    <th width="70" style="background:#FFA">Attach. WIP</th>
	                    
	                    <th width="70">Sewing Today</th>
	                    <th width="70">Sewing Complete</th>
	                    <th width="70">Sewing Balance</th>
	                    <th width="70">Sewing WIP</th>
	                    
	                    <th width="70" style="background:#FFA">Iron Today</th>
	                    <th width="70" style="background:#FFA">Iron Complete</th>
	                    <th width="70" style="background:#FFA">Iron Balance</th>
	                    <th width="70" style="background:#FFA">Re-Iron</th>
	                    
	                    <th width="70">Packing Today</th>
	                    <th width="70">Packing Complete</th>
	                    <th width="70">Packing Balance</th>
	                    <th width="70">Packing Complete %</th>
	                    
	                    <th width="70">Status</th>
	                    <th width="70">Shipment Qty</th>
	                    
	                    <th width="80">Shipment Access/ Balance</th>
	                    <th width="70">Shipment Date</th>
	                    <th width="50">R. Days</th>
	                    
	                    <th>Shipment Status</th>
	                </tr>
	            </thead>
	        </table>
	        <div style="max-height:380px; overflow-y:scroll; width:3540px" id="scroll_body">
	            <table cellspacing="0" border="1" class="rpt_table" width="3500" id="table_body" rules="all">
	            <? $i=1;
	            $tot_knitting_com_today=$tot_inspection_qnty_today=0;
				foreach($style_wise_data as $style_Id=>$color_data)
				{
					foreach($color_data as $colorId=>$value)
					{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$buyer_name=$style_ref_no=$gauge=$job_no_prefix_num=$job_no=$po_number=$shiping_status=""; $pubshipment_date="";
					$po_qty=$plan_cut=$excess_cut=0;
					

					$buyer_name=$value['buyer_name'];
					$style_ref_no=$style_Id;
					$gauge=$value['gauge'];
					$job_no_prefix_num=$value['job_no_prefix_num'];
					$job_no=$value['job_no'];
					$po_number=implode(",", array_unique(array_filter(explode("***", $value['po_number']))));
					
					$po_qty=$value['po_quantity'];
					// $plan_cut=$value['plan_cut'];
					$plan_cut=$value['plan_cut_qnty'];
				
				
					$excess_cut=$value['excess_cut'];
					
					$shiping_status=implode(",", array_unique(array_filter(explode("***", $value['shiping_status']))));
					$pubshipment_date=implode(",", array_unique(array_filter(explode("***", $value['pub_shipment_date']))));
					
					$knitting_com=$inspection_qnty=$makeup_comp=$wash_comp=$attach_comm=$sewing_comm=$iron_com=$re_iron=$packing_comm=$shipment_com=$knitting_com_today=$inspection_qnty_today=$makeup_comp_today=0;

					$poid=$style_Id;
					
					$knitting_com=$knitting_data_arr[$style_Id][$colorId]['knit_complete'];
					$knitting_com_today=$knitting_data_arr[$style_Id][$colorId]['knit_today'];
			
				
					$inspection_qnty=$first_inspection_data_arr[$style_Id][$colorId]['1st_inspection_complete'];;
					$inspection_qnty_today= $first_inspection_data_arr[$style_Id][$colorId]['1st_inspection_today'];;
				
					$makeup_comp=$makeup_data_arr[$style_Id][$colorId]['makeup_complete'];
					$makeup_comp_today=$makeup_data_arr[$style_Id][$colorId]['makeup_today'];;
					
					$wash_comp=$wash_data_arr[$style_Id][$colorId]['wash_complete'];;
					$wash_comp_today=$wash_data_arr[$style_Id][$colorId]['wash_today'];;
				

					$attach_comm=$attachment_data_arr[$style_Id][$colorId]['attachment_complete'];
					$attach_comm_today=$attachment_data_arr[$style_Id][$colorId]['attachment_today'];

					$sewing_comm=$sewing_data_arr[$style_Id][$colorId]['sewing_complete'];;
					$sewing_comm_today= $sewing_data_arr[$style_Id][$colorId]['sewing_today'];;

					$iron_com=$iron_data_arr[$style_Id][$colorId]['iron_complete'];
					$iron_com_today= $iron_data_arr[$style_Id][$colorId]['iron_today'];

					$re_iron=$production_arr[$poid][$colorId][67]['r'];
					$re_iron_today= $production_today_arr[$poid][$colorId][67]['r'];

					$packing_comm=$packing_finishing_data_arr[$style_Id][$colorId]['packing_complete'];
					$packing_comm_today= $packing_finishing_data_arr[$style_Id][$colorId]['packing_today'];
				
					$shipment_com=$shipment_data_arr[$style_Id][$colorId]['shipment_complete'];
					
					$kintting_bal=$inspe_bal=$makeup_bal=$wash_bal=$attach_bal=$sewing_bal=$iron_bal=$packing_bal=$shipment_acc_bal=0;
					$kintting_bal=$plan_cut-$knitting_com;
					$inspe_bal=$plan_cut-$inspection_qnty;
					$makeup_bal=$plan_cut-$makeup_comp;
					$wash_bal=$plan_cut-$wash_comp;					
					$attach_bal=$plan_cut-$attach_comm;				
					$sewing_bal=$plan_cut-$sewing_comm;
					$iron_bal=$plan_cut-$iron_com;
					$packing_bal=$plan_cut-$packing_comm;
					$shipment_acc_bal=$po_qty-$shipment_com;
					
					$knitting_wip=$ins_wip=$makeup_wip=$wash_wip=$attach_wip=$sewing_wip=$packing_percent=0;
					$knitting_wip=$knitting_com-$inspection_qnty;
					$ins_wip=$inspection_qnty-$makeup_comp;
					$makeup_wip=$makeup_comp-$wash_comp;
					$wash_wip=$wash_comp-$attach_comm;
					$attach_wip=$attach_comm-$sewing_comm;
					$sewing_wip=$sewing_comm-$iron_com;
					$packing_percent=($packing_comm/$po_qty)*100;
					
					$packing_status="Running"; $shpment_date='';
					
					if($packing_comm>=$plan_cut) $packing_status="Complete"; else if($packing_comm<$plan_cut) $packing_status="Running";
					
					$shpment_date=$ex_factory_arr[$style_Id][$colorId]['date'];
					$reaming_days=0;
					$reaming_days=datediff("d",date("Y-m-d"),$pubshipment_date);
					if($shiping_status==3) $reaming_days=0;
					
			
					?>
	                 <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
	                    <td width="30"><? echo $i; ?></td>    
	                    <td width="100" style="word-break:break-all"><? echo $buyer_arr[$buyer_name]; ?>&nbsp;</td>
	                    <td width="100" style="word-break:break-all"><? echo $style_ref_no; ?>&nbsp;</td>
						<td width="100"><?=$job_no;?></td>
	                    <td width="80" style="word-break:break-all"><? echo $gauge_arr[$gauge]; ?>&nbsp;</td>
	                    
	                    <td width="100" title="color Id=<?=$colorId;?>"><?=$color_library[$colorId];?></td>
						
	                    <td width="80" align="right"><? echo $value['order_quantity']; // echo $po_qty;echo  ?></td>
	                    <td width="80" align="right"><? echo $value['plan_cut_qnty'];  //echo $plan_cut; ?></td>
	                    <td width="60" align="right"><? echo number_format($excess_cut,2); ?></td>
						
						


	                    <td width="70" align="right"><?=$knitting_data_arr[$style_Id][$colorId]['knit_today'];// echo $knitting_com_today; ?></td>
	                    <td width="70" align="right"><?=$knitting_data_arr[$style_Id][$colorId]['knit_complete'];// echo $knitting_com; ?></td>
	                    <td width="70" align="right"><? echo $kintting_bal; ?></td>
	                    <td width="70" align="right"><? echo $knitting_wip; ?></td>
	                    
	                    <td width="70" align="right"><?=$first_inspection_data_arr[$style_Id][$colorId]['1st_inspection_today'];// echo $inspection_qnty_today; ?></td>
	                    <td width="70" align="right"><?=$first_inspection_data_arr[$style_Id][$colorId]['1st_inspection_complete'];// echo $inspection_qnty; ?></td>
	                    <td width="70" align="right"><? echo $inspe_bal; ?></td>
	                    <td width="70" align="right"><? echo $ins_wip; ?></td>
	                    
	                    <td width="70" align="right"><?=$makeup_data_arr[$style_Id][$colorId]['makeup_today'];// echo $makeup_comp_today; ?></td>
	                    <td width="70" align="right"><?=$makeup_data_arr[$style_Id][$colorId]['makeup_complete'];// echo $makeup_comp; ?></td>
	                    <td width="70" align="right"><? echo $makeup_bal; ?></td>
	                    <td width="70" align="right"><? echo $makeup_wip; ?></td>
	                    
	                    <td width="70" align="right"><?=$wash_data_arr[$style_Id][$colorId]['wash_today']; // echo $wash_comp_today; ?></td>
	                    <td width="70" align="right"><?=$wash_data_arr[$style_Id][$colorId]['wash_complete']; //echo $wash_comp; ?></td>
	                    <td width="70" align="right"><? echo $wash_bal; ?></td>
	                    <td width="70" align="right"><? echo $wash_wip; ?></td>
	                    
	                    <td width="70" align="right"><?=$attachment_data_arr[$style_Id][$colorId]['attachment_today'];// echo $attach_comm_today; ?></td>
	                    <td width="70" align="right"><?=$attachment_data_arr[$style_Id][$colorId]['attachment_complete']; //echo $attach_comm; ?></td>
	                    <td width="70" align="right"><? echo $attach_bal; ?></td>
	                    <td width="70" align="right"><? echo $attach_wip; ?></td>
	                    
	                    <td width="70" align="right"><?=$sewing_data_arr[$style_Id][$colorId]['sewing_today'];// echo $sewing_comm_today; ?></td>
	                    <td width="70" align="right"><?=$sewing_data_arr[$style_Id][$colorId]['sewing_complete']; //echo $sewing_comm; ?></td>
	                    <td width="70" align="right"><? echo $sewing_bal; ?></td>
	                    <td width="70" align="right"><? echo $sewing_wip; ?></td>
	                    
	                    <td width="70" align="right"><?=$iron_data_arr[$style_Id][$colorId]['iron_today'];// echo $iron_com_today; ?></td>
	                    <td width="70" align="right"><?=$iron_data_arr[$style_Id][$colorId]['iron_complete'];// echo $iron_com; ?></td>
	                    <td width="70" align="right"><? echo $iron_bal; ?></td>
	                    <td width="70" align="right"><? echo $re_iron; ?></td>
	                    
	                    <td width="70" align="right"><?=$packing_finishing_data_arr[$style_Id][$colorId]['packing_today'];// echo $packing_comm_today; ?></td>
	                    <td width="70" align="right"><?=$packing_finishing_data_arr[$style_Id][$colorId]['packing_complete']; //echo $packing_comm; ?></td>
	                    <td width="70" align="right"><? echo $packing_bal; ?></td>
	                    <td width="70" align="right"><? echo number_format($packing_percent,2); ?></td>
	                    
	                    <td width="70" style="word-break:break-all"><? echo $packing_status; ?></td>
	                    <td width="70" align="right"><?=$shipment_data_arr[$style_Id][$colorId]['shipment_complete']; //echo $shipment_com; ?></td>
	                    
	                    <td width="80" align="right"><? echo $shipment_acc_bal; ?></td>
	                    <td width="70" style="font-size:11px"><? echo change_date_format($shpment_date); ?></td>
	                    <td width="50" align="right" title="<? echo "Pub Ship Date: ".change_date_format($pubshipment_date); ?>"><? echo $reaming_days; ?></td>
	                    <td><? echo $shipment_status[$shiping_status]; ?></td>
	                </tr>
					<?
					$i++;
					//style total
					$style_tot_knitting_com+=$knitting_data_arr[$style_Id][$colorId]['knit_complete'];
					$style_tot_knitting_com_today+=$knitting_data_arr[$style_Id][$colorId]['knit_today'];
					$style_tot_kintting_bal+=$kintting_bal;
					$style_tot_knitting_wip+=$knitting_wip;
					
					$style_tot_inspection_qnty+=$first_inspection_data_arr[$style_Id][$colorId]['1st_inspection_complete'];;
					$style_tot_inspection_qnty_today+=$first_inspection_data_arr[$style_Id][$colorId]['1st_inspection_today'];;
					$style_tot_inspe_bal+=$inspe_bal;
					$style_tot_ins_wip+=$ins_wip;
					
					$style_tot_makeup_comp+=$makeup_data_arr[$style_Id][$colorId]['makeup_complete'];
					$style_tot_makeup_comp_today+=$makeup_data_arr[$style_Id][$colorId]['makeup_today'];
					$style_tot_makeup_bal+=$makeup_bal;
					$style_tot_makeup_wip+=$makeup_wip;
					
					$style_tot_wash_comp+=$wash_data_arr[$style_Id][$colorId]['wash_complete'];
					$style_tot_wash_comp_today+=$wash_data_arr[$style_Id][$colorId]['wash_today'];
					$style_tot_wash_bal+=$wash_bal;
					$style_tot_wash_wip+=$wash_wip;
					
					$style_tot_attach_comm+=$attachment_data_arr[$style_Id][$colorId]['attachment_complete'];;
					$style_tot_attach_comm_today+=$attachment_data_arr[$style_Id][$colorId]['attachment_today'];;
					$style_tot_attach_bal+=$attach_bal;
					$style_tot_attach_wip+=$attach_wip;
					
					$style_tot_sewing_comm+=$sewing_data_arr[$style_Id][$colorId]['sewing_complete']; ;
					$style_tot_sewing_comm_today+=$sewing_data_arr[$style_Id][$colorId]['sewing_today']; ;
					$style_tot_sewing_bal+=$sewing_bal;
					$style_tot_sewing_wip+=$sewing_wip;
					
					$style_tot_iron_com+=$iron_data_arr[$style_Id][$colorId]['iron_complete'];;
					$style_tot_iron_com_today+=$iron_data_arr[$style_Id][$colorId]['iron_today'];;
					$style_tot_iron_bal+=$iron_bal;
					$style_tot_re_iron+=$re_iron;
					
					$style_tot_packing_comm+=$packing_finishing_data_arr[$style_Id][$colorId]['packing_complete'];;
					$style_tot_packing_comm_today+=$packing_finishing_data_arr[$style_Id][$colorId]['packing_today'];;
					$style_tot_packing_bal+=$packing_bal;

					$style_tot_shipment_com+=$shipment_data_arr[$style_Id][$colorId]['shipment_complete'];;
					$style_tot_shipment_acc_bal+=$shipment_acc_bal;



					//==============================grand total===================================
					$tot_knitting_com+=$knitting_com;
					$tot_knitting_com_today+=$knitting_com_today;
					$tot_kintting_bal+=$kintting_bal;
					$tot_knitting_wip+=$knitting_wip;
					
					$tot_inspection_qnty+=$inspection_qnty;
					$tot_inspection_qnty_today+=$inspection_qnty_today;
					$tot_inspe_bal+=$inspe_bal;
					$tot_ins_wip+=$ins_wip;
					
					$tot_makeup_comp+=$makeup_comp;
					$tot_makeup_comp_today+=$makeup_comp_today;
					$tot_makeup_bal+=$makeup_bal;
					$tot_makeup_wip+=$makeup_wip;
					
					$tot_wash_comp+=$wash_comp;
					$tot_wash_comp_today+=$wash_comp_today;
					$tot_wash_bal+=$wash_bal;
					$tot_wash_wip+=$wash_wip;
					
					$tot_attach_comm+=$attach_comm;
					$tot_attach_comm_today+=$attach_comm_today;
					$tot_attach_bal+=$attach_bal;
					$tot_attach_wip+=$attach_wip;
					
					$tot_sewing_comm+=$sewing_comm;
					$tot_sewing_comm_today+=$sewing_comm_today;
					$tot_sewing_bal+=$sewing_bal;
					$tot_sewing_wip+=$sewing_wip;
					
					$tot_iron_com+=$iron_com;
					$tot_iron_com_today+=$iron_com_today;
					$tot_iron_bal+=$iron_bal;
					$tot_re_iron+=$re_iron;
					
					$tot_packing_comm+=$packing_comm;
					$tot_packing_comm_today+=$packing_comm_today;
					$tot_packing_bal+=$packing_bal;
					$tot_shipment_com+=$shipment_com;
					$tot_shipment_acc_bal+=$shipment_acc_bal;
				}
				?>
				 
	            <tr style="background-color:skyblue">
	            	<td width="30">&nbsp;</td> 
	                <td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
	                <td width="100">&nbsp;</td>
	                <td width="80">&nbsp;</td>
	                <td width="100">&nbsp;</td>
	                <td width="80">&nbsp;</td>
	                <td width="80">&nbsp;</td>
	                <td width="60"><b>Total:</b></td>
	                
	                <td width="70" align="right"><b><? echo $style_tot_knitting_com_today; ?></b></td>
	                <td width="70" align="right"><b><? echo $style_tot_knitting_com; ?></b></td>
	                <td width="70" align="right"><b><? echo $style_tot_kintting_bal; ?></b></td>
	                <td width="70" align="right"><b><? echo $style_tot_knitting_wip; ?></b></td>
	                
	                <td width="70" align="right"><b><? echo $style_tot_inspection_qnty_today; ?></b></td>
	                <td width="70" align="right"><b><? echo $style_tot_inspection_qnty; ?></b></td>
	                <td width="70" align="right"><b><? echo $style_tot_inspe_bal; ?></b></td>
	                <td width="70" align="right"><b><? echo $style_tot_ins_wip; ?></b></td>
	                
	                <td width="70" align="right"><b><? echo $style_tot_makeup_comp_today; ?></b></td>
	                <td width="70" align="right"><b><? echo $style_tot_makeup_comp; ?></b></td>
	                <td width="70" align="right"><b><? echo $style_tot_makeup_bal; ?></b></td>
	                <td width="70" align="right"><b><? echo $style_tot_makeup_wip; ?></b></td>
	                
	                <td width="70" align="right"><b><? echo $style_tot_wash_comp_today; ?></b></td>
	                <td width="70" align="right"><b><? echo $style_tot_wash_comp; ?></b></td>
	                <td width="70" align="right"><b><? echo $style_tot_wash_bal; ?></b></td>
	                <td width="70" align="right"><b><? echo $style_tot_wash_wip; ?></b></td>
	                
	                <td width="70" align="right"><b><? echo $style_tot_attach_comm_today; ?></b></td>
	                <td width="70" align="right"><b><? echo $style_tot_attach_comm; ?></b></td>
	                <td width="70" align="right"><b><? echo $style_tot_attach_bal; ?></b></td>
	                <td width="70" align="right"><b><? echo $style_tot_attach_wip; ?></b></td>
	                
	                <td width="70" align="right"><b><? echo $style_tot_sewing_comm_today; ?></b></td>
	                <td width="70" align="right"><b><? echo $style_tot_sewing_comm; ?></b></td>
	                <td width="70" align="right"><b><? echo $style_tot_sewing_bal; ?></b></td>
	                <td width="70" align="right"><b><? echo $style_tot_sewing_wip; ?></b></td>
	                
	                <td width="70" align="right"><b><? echo $style_tot_iron_com_today; ?></b></td>
	                <td width="70" align="right"><b><? echo $style_tot_iron_com; ?></b></td>
	                <td width="70" align="right"><b><? echo $style_tot_iron_bal; ?></b></td>
	                <td width="70" align="right"><b><? echo $style_tot_re_iron; ?></b></td>
	                
	                <td width="70" align="right"><b><? echo $style_tot_packing_comm; ?></b></td>
	                <td width="70" align="right"><b><? echo $style_tot_packing_comm_today; ?></b></td>
	                <td width="70" align="right"><b><? echo $style_tot_packing_bal; ?></b></td>
	                <td width="70" align="right">&nbsp;</td>
	                
	                <td width="70" align="right">&nbsp;</td>
	                <td width="70" align="right"><b><? echo $style_tot_shipment_com; ?></b></td>
	                
	                <td width="80" align="right"><b><? echo $style_tot_shipment_acc_bal; ?></b></td>
	                <td width="70">&nbsp;</td>
	                <td width="50">&nbsp;</td>
	                <td >&nbsp;</td>
	            </tr>
	      <?
				
				    $style_tot_knitting_com=0;$style_tot_knitting_com_today=0;$style_tot_kintting_bal=0;$style_tot_knitting_wip=0;					
					$style_tot_inspection_qnty=0;$style_tot_inspection_qnty_today=0;$style_tot_inspe_bal=0;$style_tot_ins_wip=0;					
					$style_tot_makeup_comp=0;$style_tot_makeup_comp_today=0;$style_tot_makeup_bal=0;$style_tot_makeup_wip=0;					
					$style_tot_wash_comp=0;$style_tot_wash_comp_today=0;$style_tot_wash_bal=0;$style_tot_wash_wip=0;					
					$style_tot_attach_comm=0;$style_tot_attach_comm_today=0;$style_tot_attach_bal=0;$style_tot_attach_wip=0;					
					$style_tot_sewing_comm=0;$style_tot_sewing_comm_today=0;$style_tot_sewing_bal=0;$style_tot_sewing_wip=0;					
					$style_tot_iron_com=0;$style_tot_iron_com_today=0;$style_tot_iron_bal=0;$style_tot_re_iron=0;					
					$style_tot_packing_comm=0;$style_tot_packing_comm_today=0;$style_tot_packing_bal=0;$style_tot_shipment_com=0;$style_tot_shipment_acc_bal=0;
		    	}
				?>
	            </table>
	        </div>
	        <table width="3520" cellspacing="0" border="1" class="tbl_bottom" rules="all">
	            <tr>
	            	<td width="30">&nbsp;</td> 
	                <td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
	                <td width="100">&nbsp;</td>
	                <td width="80">&nbsp;</td>
	                <td width="100">&nbsp;</td>
	                <td width="80">&nbsp;</td>
	                <td width="80">&nbsp;</td>
	                <td width="60">Total:</td>
	                
	                <td width="70" style="background:#FFA" id="td_knitting_com_today"><? echo $tot_knitting_com_today; ?></td>
	                <td width="70" style="background:#FFA" id="td_knitting_com"><? echo $tot_knitting_com; ?></td>
	                <td width="70" style="background:#FFA" id="td_knitting_bal"><? echo $tot_kintting_bal; ?></td>
	                <td width="70" style="background:#FFA" id="td_knitting_wip"><? echo $tot_knitting_wip; ?></td>
	                
	                <td width="70" id="td_inspection_qnty_today"><? echo $tot_inspection_qnty_today; ?></td>
	                <td width="70" id="td_inspection_qnty"><? echo $tot_inspection_qnty; ?></td>
	                <td width="70" id="td_inspe_bal"><? echo $tot_inspe_bal; ?></td>
	                <td width="70" id="td_ins_wip"><? echo $tot_ins_wip; ?></td>
	                
	                <td width="70" style="background:#FFA" id="td_makeup_comp_today"><? echo $tot_makeup_comp_today; ?></td>
	                <td width="70" style="background:#FFA" id="td_makeup_comp"><? echo $tot_makeup_comp; ?></td>
	                <td width="70" style="background:#FFA" id="td_makeup_bal"><? echo $tot_makeup_bal; ?></td>
	                <td width="70" style="background:#FFA" id="td_makeup_wip"><? echo $tot_makeup_wip; ?></td>
	                
	                <td width="70" id="td_wash_comp_today"><? echo $tot_wash_comp_today; ?></td>
	                <td width="70" id="td_wash_comp"><? echo $tot_wash_comp; ?></td>
	                <td width="70" id="td_wash_bal"><? echo $tot_wash_bal; ?></td>
	                <td width="70" id="td_wash_wip"><? echo $tot_wash_wip; ?></td>
	                
	                <td width="70" style="background:#FFA" id="td_attach_comm_today"><? echo $tot_attach_comm_today; ?></td>
	                <td width="70" style="background:#FFA" id="td_attach_comm"><? echo $tot_attach_comm; ?></td>
	                <td width="70" style="background:#FFA" id="td_attach_bal"><? echo $tot_attach_bal; ?></td>
	                <td width="70" style="background:#FFA" id="td_attach_wip"><? echo $tot_attach_wip; ?></td>
	                
	                <td width="70" id="td_sewing_comm_today"><? echo $tot_sewing_comm_today; ?></td>
	                <td width="70" id="td_sewing_comm"><? echo $tot_sewing_comm; ?></td>
	                <td width="70" id="td_sewing_bal"><? echo $tot_sewing_bal; ?></td>
	                <td width="70" id="td_sewing_wip"><? echo $tot_sewing_wip; ?></td>
	                
	                <td width="70" style="background:#FFA" id="td_iron_com_today"><? echo $tot_iron_com_today; ?></td>
	                <td width="70" style="background:#FFA" id="td_iron_com"><? echo $tot_iron_com; ?></td>
	                <td width="70" style="background:#FFA" id="td_iron_bal"><? echo $tot_iron_bal; ?></td>
	                <td width="70" style="background:#FFA" id="td_re_iron"><? echo $tot_re_iron; ?></td>
	                
	                <td width="70" id="td_packing_comm"><? echo $tot_packing_comm; ?></td>
	                <td width="70" id="td_packing_comm_today"><? echo $tot_packing_comm_today; ?></td>
	                <td width="70" id="td_packing_bal"><? echo $tot_packing_bal; ?></td>
	                <td width="70">&nbsp;</td>
	                
	                <td width="70">&nbsp;</td>
	                <td width="70" id="td_shipment_com"><? echo $tot_shipment_com; ?></td>
	                
	                <td width="80" id="td_shipment_acc_bal"><? echo $tot_shipment_acc_bal; ?></td>
	                <td width="70">&nbsp;</td>
	                <td width="50">&nbsp;</td>
	                <td >&nbsp;</td>
	            </tr>
	        </table>
	        
	    </div>
		<?
	}
			
	
	$html = ob_get_contents();
	ob_clean();

	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc, $html);
	echo "$html****$filename"; 
	
    exit();
}


if($action=="show_image")
{
	echo load_html_head_contents("Set Entry","../../../../", 1, 1, $unicode);
    extract($_REQUEST);
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
	exit();
}

?>
