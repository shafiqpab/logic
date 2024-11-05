<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
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
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" value="<? echo $search_cond;?>" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>'+'**'+'<? echo $search_type; ?>'+'**'+'<? echo $job_no; ?>'+'**'+'<? echo $po_no; ?>', 'create_job_no_search_list_view', 'search_div', 'production_status_summary_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	if($search_by==2) $search_field="a.style_ref_no"; else $search_field="job_no";
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


$yarn_count_details=return_library_array( "select id,yarn_count from lib_yarn_count", "id", "yarn_count"  );
$season_details=return_library_array( "select id, season_name from  lib_buyer_season", "id", "season_name"  );
$color_details=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );


$tmplte=explode("**",$data);

if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;

if($action=="report_generate") 
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$report_type=str_replace("'","",$reporttype);
	$company_name=str_replace("'","",$cbo_company_name);
	
	$cbo_year=str_replace("'","",$cbo_year); 
	$job_no=str_replace("'","",$txt_job_no);
	$order_id=str_replace("'","",$txt_order_id);
	$order_num=str_replace("'","",$txt_order_no);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	
	$report_type=str_replace("'","",$report_type);
	if($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
	
	if($order_id!="" && $order_id!=0) $order_id_cond_trans=" and b.id in ($order_no)";
	else if ($order_num=="") $order_no_cond=""; else $order_no_cond=" and  b.po_number in ('$order_num') ";
	
	
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
	
	$date_cond='';	$c_date_cond='';
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
			//$c_date_cond=" and c.country_ship_date between '$start_date' and '$end_date'";
			//$date_max_profit=" and applying_period_to_date between '$start_date' and '$end_date'";
		}
	if($report_type==1)
	{
		
		
		
		?>
        <div style="width:3580px">
			<fieldset style="width:100%;">	
        <table id="table_header_1" class="rpt_table" width="3580" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                <tr>
                	<th colspan="12" >Order Details </th>
                    <th colspan="12" >Fabric Detail </th>
                    <th colspan="19" >Gmt Production </th>
                </tr>
                    <tr>
                        <th width="30" rowspan="2">SL</th>
                        <th width="100" rowspan="2">Buyer</th>
                        <th width="70" rowspan="2">Season</th>
                        <th width="130" rowspan="2">Item Name</th>
                        <th width="100" rowspan="2">Style/Article</th>
                        <th width="80" rowspan="2">Job NO.</th>
                        <th width="80" rowspan="2">Order No.</th>
                        <th width="80" rowspan="2">Garments TOD</th>
                      
                        <th colspan="4">Fabric TOD</th>
                        <th width="130" rowspan="2">Fabrication</th> 
                        <th width="50" rowspan="2">Count</th>
                        <th width="50" rowspan="2">GSM</th>
                         
                        <th colspan="3">Knitting Status</th>
                        
                        <th width="100" rowspan="2">Fab. Color</th>
                        <th colspan="3">Dyeing Finishing Status</th>
                         
                        <th width="80" rowspan="2">Gmts Color</th>
                        <th width="100" rowspan="2">Order Qnty(PCS)</th>
                        
                       
                        <th colspan="3">Cutting Status</th>
                        <th colspan="3">Print Status</th>
                        <th colspan="3">Embellishment Status</th>
                        <th colspan="2">Sewing Status</th>
                        <th colspan="3">Washing Status</th>
                        <th colspan="2">Finishing Status</th>
                      
                        <th width="80" rowspan="2">Shipment Qnty.</th> 
                        <th width="80" rowspan="2">Excess/Short Qnty</th>
                        <th width="" rowspan="2">Actual Ex-Factory Date</th>
                        
                    </tr>
                    <tr>
                        <th width="80" title="23*80=2080 dwn">Knitting Delivery Start</th>
                        <th width="80">Knitting Delivery End</th>
                        <th width="80">Dyeing Delivery Start</th>
                        <th width="80">Dyeing Delivery End</th>
                        
                        <th width="80">Required Qty(kg)</th>
                        <th width="80">Complete</th>
                        <th width="80">Balance</th>
                        
                        <th width="80">Required Qty(kg))</th>
                        <th width="80">Complete</th>
                        <th width="80">Balance</th>
                        
                         <th width="80">Required Qty(kg)</th>
                        <th width="80">Complete</th>
                        <th width="80">Balance</th>
                        
                        <th width="80">Required Qty(kg)</th>
                        <th width="80">Complete</th>
                        <th width="80">Balance</th>
                        
                        <th width="80">Required Qty(kg)</th>
                        <th width="80">Complete</th>
                        <th width="80">Balance</th>
                        
                        <th width="80">Input</th>
                        <th width="80">Output</th>
                        
                        <th width="80" title="Wash Status">Wash Rcv Qty (Pcs)</th>
                        <th width="80">Wash Delv.</th>
                        <th width="80">Balance</th>
                        
                         <th width="80" title="Fin Status">Complete</th>
                        <th width="80">Balance</th>
                       
                       
                    </tr>
                </thead>
            </table>
            
            <div style="width:3600px; max-height:400px; overflow-y:scroll" id="scroll_body">
			<table class="rpt_table" width="3580" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
            <?
             	
				$prod_data=sql_select("select a.febric_description_id as deter_id, b.po_breakdown_id as po_id,
				 (case when b.entry_form in(2,22) and b.trans_type=1 then b.quantity end) as kint_qty,
				 (case when b.entry_form in(7,60)  and b.trans_type=1 then b.quantity end) as fin_qty
				from pro_grey_prod_entry_dtls a, order_wise_pro_details b 
				where a.id=b.dtls_id and b.entry_form in(2,22,7,60) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");
				foreach($prod_data as $row)
				{
					$prod_qty_arr[$row[csf('po_id')]][$row[csf('deter_id')]]['kint_qty']+=$row[csf('kint_qty')];
					$prod_qty_arr[$row[csf('po_id')]][$row[csf('deter_id')]]['fin_qty']+=$row[csf('fin_qty')];
				}
				//color_size_break_down_id
				 $sql_gmtprod= sql_select("SELECT b.id as po_id,d.color_number_id as gmt_color,
					  (CASE WHEN c.production_type=1 THEN e.production_qnty ELSE 0 END)  as cut_qty,
					  (CASE WHEN c.production_type=3  and c.embel_name=1 THEN e.production_qnty ELSE 0 END)  as print_qty,
					  (CASE WHEN c.production_type=3  and c.embel_name=2 THEN e.production_qnty ELSE 0 END)  as embrod_qty,
					  (CASE WHEN c.production_type=3  and c.embel_name=3 THEN e.production_qnty ELSE 0 END)  as wash_qty,
					  (CASE WHEN c.production_type=4 THEN e.production_qnty ELSE 0 END)  as sew_in_qty,
					  (CASE WHEN c.production_type=5 THEN e.production_qnty ELSE 0 END)  as sew_out_qty,
					  (CASE WHEN c.production_type=11 THEN e.production_qnty ELSE 0 END)  as poly_qty,
					  (CASE WHEN c.production_type=8 THEN e.production_qnty ELSE 0 END)  as finish_qty
					   FROM wo_po_details_master a, wo_po_break_down b,pro_garments_production_mst c,pro_garments_production_dtls e,wo_po_color_size_breakdown d
         WHERE a.job_no=b.job_no_mst and c.po_break_down_id=d.po_break_down_id and c.po_break_down_id=b.id and 
         d.po_break_down_id=b.id and e. color_size_break_down_id=d.id and c.id=e.mst_id and c.production_type in(1,3,4,5,11,8) and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  $date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond  order by b.id,c.country_id ");
				foreach($sql_gmtprod as $row)
				{
					$gmt_prod_qty_arr[$row[csf('po_id')]][$row[csf('gmt_color')]]['cut_qty']+=$row[csf('cut_qty')];
					$gmt_prod_qty_arr[$row[csf('po_id')]][$row[csf('gmt_color')]]['sew_in_qty']+=$row[csf('sew_in_qty')];
					$gmt_prod_qty_arr[$row[csf('po_id')]][$row[csf('gmt_color')]]['sew_out_qty']+=$row[csf('sew_out_qty')];
					$gmt_prod_qty_arr[$row[csf('po_id')]][$row[csf('gmt_color')]]['finish_qty']+=$row[csf('finish_qty')];
					$gmt_prod_qty_arr[$row[csf('po_id')]][$row[csf('gmt_color')]]['print_qty']+=$row[csf('print_qty')];
					$gmt_prod_qty_arr[$row[csf('po_id')]][$row[csf('gmt_color')]]['embrod_qty']+=$row[csf('embrod_qty')];
					$gmt_prod_qty_arr[$row[csf('po_id')]][$row[csf('gmt_color')]]['wash_qty']+=$row[csf('wash_qty')];
					
				}
				
				$sql_tna = "select b.id as po_id,c.po_number_id,
				 (case when c.task_number=61 then c.task_start_date end) as dyeing_start_date,
				 (case when c.task_number=61 then c.task_finish_date end) as dyeing_finish_date,
				  (case when c.task_number=60 then c.task_start_date end) as knit_start_date,
				 (case when c.task_number=60 then c.task_finish_date end) as knit_finish_date
				  from wo_po_details_master a, wo_po_break_down b, tna_process_mst c  where a.job_no=b.job_no_mst and a.job_no=c.job_no and 
				  b.id=c.po_number_id and c.task_number in(60,61) 
				     $date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond order by b.id";
				$data_arr_tna=sql_select($sql_tna);
				foreach($data_arr_tna as $row)
				{
					$tna_tmp_arr[$row[csf("po_id")]]['dyeing_start_date']=$row[csf("dyeing_start_date")];
					$tna_tmp_arr[$row[csf("po_id")]]['dyeing_finish_date']=$row[csf("dyeing_finish_date")];
					$tna_tmp_arr[$row[csf("po_id")]]['knit_start_date']=$row[csf("knit_start_date")];
					$tna_tmp_arr[$row[csf("po_id")]]['knit_finish_date']=$row[csf("knit_finish_date")];
				}
				
					$sql_yarn = "select a.sequence_no,a.id,c.yarn_count,a.construction,b.copmposition_id  from  lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b,lib_yarn_count c where a.id=b.mst_id and b.count_id=c.id  order by a.id, a.sequence_no";
					$data_arr_ycount=sql_select($sql_yarn);
				foreach($data_arr_ycount as $row)
				{
						$composition1=$composition[$row[csf("copmposition_id")]];
						//$percentage1=$com_percentage1_details[$row[csf("copmposition_id")]];
						$fab_desc=$row[csf("construction")].','.$composition1.'100%';
						$precost_yarnCount_arr[$row[csf("id")]]['count']=$row[csf("yarn_count")];
						$precost_yarnCount_arr[$row[csf("id")]]['desc']=$fab_desc;
						
				}
					
				$sql_fabric = "select b.id as po_id,c.id, c.construction,c.composition,c.fabric_description,c.gsm_weight,c.lib_yarn_count_deter_id as count_deter_id,d.color_number_id   from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_fabric_cost_dtls c,wo_pre_cos_fab_co_avg_con_dtls d where a.job_no=b.job_no_mst and a.job_no=c.job_no and d.pre_cost_fabric_cost_dtls_id=c.id and d.po_break_down_id=b.id and d.job_no=a.job_no and d.job_no=c.job_no  $date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond order by c.id";
				$data_arr_fabric=sql_select($sql_fabric);
				foreach($data_arr_fabric as $fab_row)
				{
					
					$yarncount=$precost_yarnCount_arr[$fab_row[csf("count_deter_id")]]['count'];
					$precost_fab_arr[$fab_row[csf("id")]][$fab_row[csf("po_id")]]['gsm']=$fab_row[csf("gsm_weight")];
					$precost_fab_arr[$fab_row[csf("id")]][$fab_row[csf("po_id")]]['yarncount']=$yarncount;
					$precost_fab_arr[$fab_row[csf("id")]][$fab_row[csf("po_id")]]['count_deter_id']=$fab_row[csf("count_deter_id")];
					$precost_fab_arr[$fab_row[csf("id")]][$fab_row[csf("po_id")]]['desc']=$fab_row[csf("fabric_description")];
					
				}
				//print_r($precost_fab_arr);
				 $sql_result="select a.company_name as company_id,a.job_no_prefix_num as job_prefix,a.season_matrix,  a.job_no, a.buyer_name, a.style_ref_no, 
				a.gmts_item_id, a.total_set_qnty as ratio,b.id as po_id, b.po_number, b.pub_shipment_date,(b.po_quantity) as po_quantity, (b.po_total_price) as po_total_price,c.fabric_color_id as fab_color,c.gmts_color_id,(c.fin_fab_qnty) as fin_fab_qnty,c.grey_fab_qnty,c.pre_cost_fabric_cost_dtls_id as pre_cost_fab_dtls_id,d.color_number_id as gmt_color,d.order_quantity
				from wo_po_details_master a, wo_po_break_down b ,wo_booking_dtls c,wo_po_color_size_breakdown d
				 where a.job_no=b.job_no_mst and c.po_break_down_id=b.id and a.job_no=c.job_no and d.po_break_down_id=b.id and d.id=c.color_size_table_id and c.job_no=d.job_no_mst and d.job_no_mst=a.job_no and c.booking_type=1 and  a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond 
				  order by b.id"; 
				$data_result=sql_select($sql_result);
				$prod_detail_arr=array();
				
				$i=$z=1;$all_full_job=""; $total_po_qty=$total_fab_req_qty=$total_po_qty_pcs=0;
				foreach($data_result as $row)
				{
					$fab_id=$row[csf("pre_cost_fab_dtls_id")];
					$yarn_count=$precost_fab_arr[$fab_id][$fab_row[csf("po_id")]]['yarncount'];
					$gsm_desc=$precost_fab_arr[$fab_id][$row[csf('po_id')]]['gsm'];//$row[csf("pre_cost_fab_dtls_id")];
					$deter_ids=$precost_fab_arr[$fab_id][$row[csf("po_id")]]['count_deter_id'];
					
					
					$deter_id=$precost_fab_arr[$fab_id][$row[csf('po_id')]]['desc'];
					//echo $count_deter_id.'=';
					$prod_detail_arr[$row[csf('po_id')]][$row[csf('fab_color')]][$row[csf('gmt_color')]]['pub_date']=$row[csf('pub_shipment_date')];
					$prod_detail_arr[$row[csf('po_id')]][$row[csf('fab_color')]][$row[csf('gmt_color')]]['job_no']=$row[csf('job_no')];
					$prod_detail_arr[$row[csf('po_id')]][$row[csf('fab_color')]][$row[csf('gmt_color')]]['buyer_name']=$row[csf('buyer_name')];
					$prod_detail_arr[$row[csf('po_id')]][$row[csf('fab_color')]][$row[csf('gmt_color')]]['style_ref_no']=$row[csf('style_ref_no')];
					$prod_detail_arr[$row[csf('po_id')]][$row[csf('fab_color')]][$row[csf('gmt_color')]]['season_matrix']=$row[csf('season_matrix')];
					
					$prod_detail_arr[$row[csf('po_id')]][$row[csf('fab_color')]][$row[csf('gmt_color')]]['gmts_item_id']=$row[csf('gmts_item_id')];
					$prod_detail_arr[$row[csf('po_id')]][$row[csf('fab_color')]][$row[csf('gmt_color')]]['total_set_qnty']=$row[csf('total_set_qnty')];
					$prod_detail_arr[$row[csf('po_id')]][$row[csf('fab_color')]][$row[csf('gmt_color')]]['po_no']=$row[csf('po_number')];
					$prod_detail_arr[$row[csf('po_id')]][$row[csf('fab_color')]][$row[csf('gmt_color')]]['po_qty_pcs']+=($row[csf('order_quantity')]);
					
					$prod_detail_arr[$row[csf('po_id')]][$row[csf('fab_color')]][$row[csf('gmt_color')]]['gmts_color_id']=$row[csf('gmt_color')];
					$prod_detail_arr[$row[csf('po_id')]][$row[csf('fab_color')]][$row[csf('gmt_color')]]['fin_fab_qnty']+=$row[csf('fin_fab_qnty')];
					$prod_detail_arr[$row[csf('po_id')]][$row[csf('fab_color')]][$row[csf('gmt_color')]]['grey_fab_qnty']+=$row[csf('grey_fab_qnty')];
					$prod_detail_arr[$row[csf('po_id')]][$row[csf('fab_color')]][$row[csf('gmt_color')]]['pre_cost_fab_dtls_id']=$row[csf('pre_cost_fab_dtls_id')];
					$prod_detail_arr[$row[csf('po_id')]][$row[csf('fab_color')]][$row[csf('gmt_color')]]['gsm'].=$gsm_desc.',';
					$prod_detail_arr[$row[csf('po_id')]][$row[csf('fab_color')]][$row[csf('gmt_color')]]['count_deter_id']=$deter_ids;
					$prod_detail_arr[$row[csf('po_id')]][$row[csf('fab_color')]][$row[csf('gmt_color')]]['fab_desc']=$fab_dtls_desc;
					$prod_detail_arr[$row[csf('po_id')]][$row[csf('fab_color')]][$row[csf('gmt_color')]]['yarn_count']=$yarn_count;
					$prod_detail_arr[$row[csf('po_id')]][$row[csf('fab_color')]][$row[csf('gmt_color')]]['desc']=$deter_id;
					
					//if($all_full_job=="") $all_full_job="'".$row[csf('job_no')]."'"; else $all_full_job.=","."'".$row[csf('job_no')]."'";
					
				}
				
				$fab_fab_rowspan_arr=array();$fab_desc_rowspan_arr=array();$po_rowspan_arr=array();
				foreach($prod_detail_arr as $po_key=>$fab_data)
				{
					$po_rowspan=0;
					foreach($fab_data as $fab_color=>$fcolor_data)
					{
						//$desc_rowspan=0;
						//foreach($deter_data as $fab_color=>$fab_val)
						//{
							$fabcolor_rowspan=0;
							foreach($fcolor_data as $gmt_color=>$val)
							{
								$fabcolor_rowspan++;
								$desc_rowspan++;
								$po_rowspan++;
								
							}
							//$fab_color_rowspan_arr[$po_key][$fab_desc][$gmt_color]=$gmtcolor_rowspan;
							$fab_fab_rowspan_arr[$po_key][$fab_color]=$fabcolor_rowspan;
							//$fab_desc_rowspan_arr[$po_key]=$desc_rowspan;
							$po_rowspan_arr[$po_key]=$po_rowspan;
						//}
						
					}
				}
				//print_r($fab_fab_rowspan_arr);
				foreach($prod_detail_arr as $po_key=>$fab_data)
				{
					//$p=1;
					//foreach($fab_data as $deter_key=>$deter_data)
					//{
						$m=1;
						foreach($fab_data as $fab_color=>$fab_val)
						{
							$g=1;
							foreach($fab_val as $gmt_color=>$val)
							{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
							$ydeter_id=$val['count_deter_id'];
							$fab_desc=$val['desc'];//$deter_key;//$precost_yarnCount_arr[$deter_key]['desc'];//$val['fab_desc'];//$precost_fab_arr[$fab_key][$po_key]['desc'];
							$yarn_count=$val['yarn_count'];
							$fab_gsm=rtrim($val['gsm'],',');
							$fab_gsms=implode(",",array_unique(explode(",",$fab_gsm)));
							$knitting_com_qty=$prod_qty_arr[$po_key][$ydeter_id]['kint_qty'];
							$fin_com_qty=$prod_qty_arr[$po_key][$ydeter_id]['fin_qty'];;
							$grey_fab_qnty=$val['grey_fab_qnty'];
							$fin_fab_qnty=$val['fin_fab_qnty'];
							$cut_qnty=$gmt_prod_qty_arr[$po_key][$gmt_color]['cut_qty'];
							$finish_qty=$gmt_prod_qty_arr[$po_key][$gmt_color]['finish_qty'];
							$sew_in_qty=$gmt_prod_qty_arr[$po_key][$gmt_color]['sew_in_qty'];
							$sew_out_qty=$gmt_prod_qty_arr[$po_key][$gmt_color]['sew_out_qty'];
							
							$print_qty=$gmt_prod_qty_arr[$po_key][$gmt_color]['print_qty'];
							$embrod_qty=$gmt_prod_qty_arr[$po_key][$gmt_color]['embrod_qty'];
							$wash_qty=$gmt_prod_qty_arr[$po_key][$gmt_color]['wash_qty'];
							
							$dyeing_start_date=$tna_tmp_arr[$po_key]['dyeing_start_date'];
							$dyeing_finish_date=$tna_tmp_arr[$po_key]['dyeing_finish_date'];
							
							$knit_start_date=$tna_tmp_arr[$po_key]['knit_start_date'];
							$knit_finish_date=$tna_tmp_arr[$po_key]['knit_finish_date'];
							
							$gmt_color_rowspan=$fab_fab_rowspan_arr[$po_key][$fab_color];
							$fab_desc_rowspan2=$fab_desc_rowspan_arr[$po_key];
							$po_rowspan_rowspan=$po_rowspan_arr[$po_key];
							
						?>
            			<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
							<?
                         if($m==1)
							{
							?>
                            <td width="30"  rowspan="<? echo $po_rowspan_rowspan;?>" ><? echo $i; ?></td>
                           
							<td width="100" rowspan="<? echo $po_rowspan_rowspan;?>"><? echo $buyer_arr[$val['buyer_name']]; ?></td>
							<td width="70"   rowspan="<? echo $po_rowspan_rowspan;?>" align="center"><p><? echo $season_details[$val['season_matrix']]; ?></p></td>
							<td width="130" rowspan="<? echo $po_rowspan_rowspan;?>" ><div style="width:130px; word-wrap:break-word;"><? 
							$gmts_item=''; $gmts_item_id=explode(",",$val['gmts_item_id']);
							foreach($gmts_item_id as $item_id)
							{
								if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=", ".$garments_item[$item_id];
							}
							echo $gmts_item; 
							 ?></div></td>
							<td width="100" rowspan="<? echo $po_rowspan_rowspan;?>"><div style="word-break:break-all"><? echo $val['style_ref_no']; ?></div></td>
                            <td width="80" rowspan="<? echo $po_rowspan_rowspan;?>"><div style="word-break:break-all"><? echo  $val['job_no']; ?></div></td>
							<td width="80" rowspan="<? echo $po_rowspan_rowspan;?>" ><div style="word-wrap:break-word:100px;"><? echo $val['po_no']; ?></div></td>
							<td width="80" rowspan="<? echo $po_rowspan_rowspan;?>"><div style="word-wrap:break-word:100px;"><? echo change_date_format($val['pub_date']); ?></div></td>
                            
                            <td width="80" rowspan="<? echo $po_rowspan_rowspan;?>"><div style="word-break:break-all"><? echo change_date_format($knit_start_date); ?></div></td>
							<td width="80" rowspan="<? echo $po_rowspan_rowspan;?>"> <div style="word-break:break-all"> <? echo change_date_format($knit_finish_date); ?>   </div>
							<td width="80" rowspan="<? echo $po_rowspan_rowspan;?>" align="center" > <? echo change_date_format($dyeing_start_date); ?>
                            <td width="80" rowspan="<? echo $po_rowspan_rowspan;?>"><div style="word-break:break-all"><?  echo change_date_format($dyeing_finish_date);  ?></div></td>
                              <?
							
							}
							
                          if($g==1)
							{
							//?>
							<td width="130" rowspan="<? echo $gmt_color_rowspan;?>" title="<? echo $ydeter_id.' Fab'.$po_key; ?>"> 
                            <div style="word-break:break-all"> <? echo $fab_desc; ?>   </div> </td>
                            
							<td width="50" rowspan="<? echo $gmt_color_rowspan;?>" align="center" > <? echo $yarn_count; ?></td>
                            <td width="50" rowspan="<? echo $gmt_color_rowspan;?>" title=""> <div style="word-break:break-all"> <? echo $fab_gsms; ?>   </div>	</td>
                            
                            <td width="80" rowspan="<? echo $gmt_color_rowspan;?>" title="Fab Finish Qty" align="right"> <div style="word-break:break-all"> 
							<? echo number_format($grey_fab_qnty,2); ?>   </div>	</td>
                            <td width="80" rowspan="<? echo $gmt_color_rowspan;?>" title="" align="right"> <div style="word-break:break-all"> 
							<? $knit_balance_qnty=$grey_fab_qnty-$knitting_com_qty; echo number_format($knitting_com_qty,2);//$val['fin_fab_qnty']; ?>   </div>	</td>
                            <td width="80" rowspan="<? echo $gmt_color_rowspan;?>" title=""> <div style="word-break:break-all"> <?  ?>   gfg</div>	</td>
                            <?
							//}
							//?>
                             <td width="100" title="" rowspan="<? echo $gmt_color_rowspan;?>"> <div style="word-break:break-all"> <? echo $color_details[$fab_color]; ?> </div>	</td>
                             
                             <td width="80" title="Fab Finish Qty" rowspan="<? echo $gmt_color_rowspan;?>" align="right" > <div style="word-break:break-all"> <? echo number_format($fin_fab_qnty,2); ?> </div>	</td>
                            <td width="80" title="" align="right" rowspan="<? echo $gmt_color_rowspan;?>">  <div style="word-break:break-all"> <? $dye_balance_qnty=$fin_fab_qnty-$fin_com_qty;echo number_format($fin_com_qty,2); ?> </div></td>
                            <td width="80" title="" align="right" rowspan="<? echo $gmt_color_rowspan;?>"> <div style="word-break:break-all"> <? echo number_format($dye_balance_qnty,2); ?> </div>	</td>
                           <? //if($m==1)
								//{
                             ?>
                             <td width="80"  rowspan="<? echo $gmt_color_rowspan;?>"  title=""  align="right"> 
                            		 <div style="word-break:break-all"> <? echo $color_details[$gmt_color]; ?>   </div>	</td>
                            <td width="100" rowspan="<? echo $gmt_color_rowspan;?>"   title="<? echo $fab_color;?>"> <div style="word-break:break-all"> <? echo $val['po_qty_pcs'];//$color_details[$gmt_color]; ?>   </div>	</td>
                            
                            <td width="80"  rowspan="<? echo $gmt_color_rowspan;?>" title="Cutting 3"  align="right"> <div style="word-break:break-all"> <? echo $val['po_qty_pcs']; ?>   </div>	</td>
                            <td width="80" rowspan="<? echo $gmt_color_rowspan;?>" title=""  align="right"> <div style="word-break:break-all"> 
							<? $cut_balance_qnty=$val['po_qty_pcs']-$cut_qnty;echo number_format($cut_qnty,2); ?>   </div>	</td>
                            <td width="80" rowspan="<? echo $gmt_color_rowspan;?>" title=""> <div style="word-break:break-all">
                             <?  echo number_format($cut_balance_qnty,2); ?>   </div>	</td>
                            
                            <td width="80" rowspan="<? echo $gmt_color_rowspan;?>" title="Print 3"> <div style="word-break:break-all"> <? echo $val['po_qty_pcs']; ?>   </div>	</td>
                            <td width="80" rowspan="<? echo $gmt_color_rowspan;?>" title=""> <div style="word-break:break-all">
                             <? $print_balance_qnty=$val['po_qty_pcs']-$print_qty;echo number_format($print_qty,2); ?>   </div>	</td>
                            <td width="80" rowspan="<? echo $gmt_color_rowspan;?>" title=""> <div style="word-break:break-all">
                             <? echo number_format($print_balance_qnty,2); ?>   </div>	</td>
                            
                            <td width="80" rowspan="<? echo $gmt_color_rowspan;?>"  title="Emrodery 3"  align="right"> <div style="word-break:break-all"> <? echo $val['po_qty_pcs']; ?>   </div>	</td>
                            <td width="80" rowspan="<? echo $gmt_color_rowspan;?>"  title=""  align="right"> <div style="word-break:break-all">
                             <? $embrod_balance_qnty=$val['po_qty_pcs']-$embrod_qty;echo number_format($embrod_qty,2); ?>   </div>	</td>
                            <td width="80" rowspan="<? echo $gmt_color_rowspan;?>"  title=""  align="right"> <div style="word-break:break-all"> <? echo  number_format($embrod_balance_qnty,2); ?>   </div>	</td>
                            
                            <td width="80"  rowspan="<? echo $gmt_color_rowspan;?>" title="Sewing 3"> <div style="word-break:break-all"> <? echo $val['po_qty_pcs']; ?>   </div>	</td>
                            <td width="80"  rowspan="<? echo $gmt_color_rowspan;?>" title="Sew In"> <div style="word-break:break-all">
                             <? $sew_in_balance_qnty=$val['po_qty_pcs']-$sew_in_qty;echo number_format($sew_in_qty,2); ?>   </div>	</td>
                            <td width="80"  rowspan="<? echo $gmt_color_rowspan;?>" title=""> <div style="word-break:break-all"> <? echo number_format($sew_in_balance_qnty,2); ?>   </div>	</td>
                           
                            <td width="80"  rowspan="<? echo $gmt_color_rowspan;?>"title="Wash 3"  align="right"> <div style="word-break:break-all"> <? echo $val['po_qty_pcs']; ?>   </div>	</td>
                            <td width="80"  rowspan="<? echo $gmt_color_rowspan;?>" title=""  align="right"> <div style="word-break:break-all"> <? $sew_out_balance_qnty=$val['po_qty_pcs']-$sew_out_qty;echo number_format($sew_out_qty,2);  ?>   </div>	</td>
                            <td width="80"   rowspan="<? echo $gmt_color_rowspan;?>" title=""  align="right"> <div style="word-break:break-all"> <? echo number_format($sew_out_balance_qnty,2); ?>   </div>	</td>
                            
                             <td width="80" rowspan="<? echo $gmt_color_rowspan;?>"  title="Finish 2"> <div style="word-break:break-all"> <? echo $val['po_qty_pcs']; ?>   </div>	</td>
                            <td width="80" rowspan="<? echo $gmt_color_rowspan;?>" title=""  align="right"> <div style="word-break:break-all">
                             <? echo $wash_qty_balance_qnty=$val['po_qty_pcs']-$wash_qty;echo number_format($wash_qty_balance_qnty,2); ?>   </div>	</td>
                            
                            <td width="80" rowspan="<? echo $gmt_color_rowspan;?>" title=""  align="right"> <div style="word-break:break-all"> <? //echo $row[csf('po_number')]; ?>   </div>	</td>
                            <td width="" rowspan="<? echo $gmt_color_rowspan;?>"  title=""  align="right"> <div style="word-break:break-all"> <? //echo $row[csf('po_number')]; ?>   </div>	</td>
                            
                            <?
							}
							?>
                           
                            </tr>
                            <?
							$m++;
							//$p++;
							$g++;
							$i++;
							
							$total_fab_req_qty+=$val['fin_fab_qnty'];
							$total_po_qty_pcs+=$val['po_qty_pcs'];
								}
							//$z++;	
						//}
					}
					
				}
							?>
                            <tfoot>
                            <tr>
                            <th colspan="15"> Total </th>
                            <th><? echo number_format($total_fab_req_qty,2); ?>  </th>
                            <th>&nbsp; </th>
                            <th>&nbsp; </th>
                             <th><? //echo number_format($total_po_qty_pcs,2); ?>  </th>
                            <th><? echo number_format($total_po_qty_pcs,2); ?></th>
                            <th>&nbsp; </th>
                             <th colspan="23">&nbsp; </th>
                            
                            </tr>
                            </tfoot>
            </table>
            </div>
            </fieldset>
            </div>
            <?	
	}
	

}

?>