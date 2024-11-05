<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.fabrics.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//--------------------------------------------------------------------------------------------------------------------
$company_library=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
$deal_merchant_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
$team_leader_arr=return_library_array( "select id, team_leader_name from lib_marketing_team",'id','team_leader_name');
$lib_season_name_arr=return_library_array( "select id,season_name from lib_buyer_season", "id", "season_name"  );
$lib_supplier_arr=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name"  );
$lib_color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );



if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );   	 
	exit();
}
if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $cbo_search_type;
	?>
	<script>
	var search_type='<? echo $cbo_search_type;?>';
	//alert(search_type);
		function js_set_value(str)
		{
			var splitData = str.split("_");
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
													
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );//cbo_search_type
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $search_common; ?>'+'**'+'<? echo $cbo_search_type; ?>', 'create_job_no_search_list_view', 'search_div', 'partial_fabric_booking_analysis_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	$search_common=$data[4];
	$cbo_search_type=$data[5];
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
	if($search_by==2) $search_field="a.style_ref_no"; else $search_field="a.job_no";
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
	if($cbo_search_type==1)//Style
	{
		$selete_data="id,style_ref_no";	
	}
	else if($cbo_search_type==1) //Job
	{
		$selete_data="id,job_no_prefix_num";	
	}
	else if($cbo_search_type==3)
	{
		$selete_data="po_id,po_number";	
	}
	else if($cbo_search_type==4) //File
	{
		$selete_data="po_id,file_no";	
	}
	else if($cbo_search_type==5) //Ref. no
	{
		$selete_data="po_id,grouping";	
	}
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	if($cbo_search_type!=6)
	{
	$sql= "select b.id as po_id,a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, $year_field,b.po_number,b.file_no,b.grouping from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond  order by a.job_no";
	
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref.,PO No,File No,Ref. No", "120,100,80,60,100,100,60,80","780","240",0, $sql , "js_set_value", "$selete_data", "", 1, "company_name,buyer_name,0,0,0,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no,po_number,file_no,grouping", "",'','0,0,0,0,0,0','') ;
	}
	else
	{
		$sql= "select c.id as book_id,c.booking_no_prefix_num as booking_prefix,c.booking_date, a.job_no, a.company_name, a.buyer_name, $year_field from wo_po_details_master a,wo_po_break_down b,wo_booking_mst c,wo_booking_dtls d where a.job_no=b.job_no_mst and c.booking_no=d.booking_no and b.id=d.po_break_down_id and d.job_no=a.job_no and a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id  and c.company_id=$company_id and c.entry_form=108 and c.booking_type in(1) and c.is_short in(2) and $search_field like '$search_string' $buyer_id_cond $year_cond group by  c.id,c.booking_no_prefix_num,c.booking_date,a.job_no,a.company_name, a.buyer_name,a.insert_date order by a.job_no";
	
	echo create_list_view("tbl_list_search", "Company,Buyer,Job No,Year,Booking No.,Booking Date", "150,120,120,60,100,100","780","240",0, $sql , "js_set_value", "book_id,booking_prefix", "", 1, "company_name,buyer_name,0,0,0,0", $arr , "company_name,buyer_name,job_no,year,booking_prefix,booking_date", "",'','0,0,0,0,0,3','') ;
	}
	exit(); 
} // Job Search end


$tmplte=explode("**",$data);
if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;


if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_search_type=str_replace("'","",$cbo_search_type);
	$txt_search_common=trim(str_replace("'","",$txt_search_common));
	$cbo_year_selection=str_replace("'","",$cbo_year_selection);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$txt_job_po_id=str_replace("'","",$txt_job_po_id);
	$booking_no=str_replace("'","",$txt_booking_no);
	if ($booking_no=="") $booking_no_cond=""; else $booking_no_cond=" and a.booking_no_prefix_num='".trim($booking_no)."' ";
	$buyer_id_cond="";	$buyer_id_cond2="";
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
				$buyer_id_cond2=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else
			{
				$buyer_id_cond="";
				$buyer_id_cond2="";
			}
		}
		else
		{
			$buyer_id_cond="";
			$buyer_id_cond2="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
		$buyer_id_cond2=" and a.buyer_id=$cbo_buyer_name";
	}
	
	if($txt_search_common!="" || $txt_search_common !=0)
	{
		
		if($cbo_search_type==1)
		{
			$search_cond="and a.style_ref_no like '%".$txt_search_common."%'";
			if($txt_job_po_id!='')
			{
				$search_cond.=" and a.id=".$txt_job_po_id."";
			}
		}
		else if($cbo_search_type==2)
		{
			$search_cond="and a.job_no like '%".$txt_search_common."%'";
			if($txt_job_po_id!='')
			{
				$search_cond.=" and a.id=".$txt_job_po_id."";
			}
		}
		else if($cbo_search_type==3)
		{
			$search_cond="and b.po_number like '%".$txt_search_common."%'";
			if($txt_job_po_id!='')
			{
				$search_cond.=" and b.id=".$txt_job_po_id."";
			}
			
		}
		else if($cbo_search_type==4)
		{
			$search_cond="and b.file_no =".$txt_search_common."";
		}
		else if($cbo_search_type==5)
		{
			$search_cond="and b.grouping like '%".$txt_search_common."%'";
			
		}
		if($cbo_search_type==6) //Booking No
		{
			$search_booking_cond="and a.booking_no like '%".$txt_search_common."%'";
			if($txt_job_po_id!='')
			{
				$search_booking_cond.=" and a.id=".$txt_job_po_id."";
			}
		}
	}
	else
	{
		$search_cond="";
		$search_booking_cond="";	
	}
	
		$booking_date_cond='';
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			$start_date=(str_replace("'","",$txt_date_from));
			$end_date=(str_replace("'","",$txt_date_to));
			if($db_type==0)
			{
				$start_date= change_date_format($start_date,"yyyy-mm-dd");
				$end_date= change_date_format($end_date,"yyyy-mm-dd");
			}
			else
			{
				$start_date= change_date_format($start_date,"dd-mm-yyyy","-",1);
				$end_date= change_date_format($end_date,"dd-mm-yyyy","-",1);
			}
	
			$booking_date_cond="and a.booking_date between '$start_date' and '$end_date'";
		}
	
	
	
	if($template==1)
	{
		//$job_array=array();
	$job_wise_arr=array();
	
	$sql_data="select a.job_no,a.job_no_prefix_num,a.company_name,a.season_buyer_wise,a.season_matrix,a.buyer_name,a.style_ref_no,a.team_leader,a.dealing_marchant, a.gmts_item_id,a.job_quantity, b.id,b.is_confirmed,b.po_number,b.pub_shipment_date,b.shipment_date,b.po_quantity,b.unit_price,b.file_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1   and a.company_name=$company_name $buyer_id_cond  $search_cond";
	$res_data=sql_select($sql_data);
	foreach( $res_data as $row_data)
	{
	$po_id_arr[$row_data[csf('id')]]=$row_data[csf('id')];
	$po_wise_arr[$row_data[csf('id')]]['job']=$row_data[csf('job_no')];
	$job_wise_arr[$row_data[csf('job_no')]]['job']=$row_data[csf('buyer_name')];
	$job_wise_arr[$row_data[csf('job_no')]]['style']=$row_data[csf('style_ref_no')];
	$job_wise_arr[$row_data[csf('job_no')]]['season']=$row_data[csf('season_buyer_wise')];
	$job_wise_arr[$row_data[csf('job_no')]]['matrix_season']=$row_data[csf('season_matrix')];
	$job_wise_arr[$row_data[csf('job_no')]]['team_leader']=$team_leader_arr[$row_data[csf('team_leader')]];
	$job_wise_arr[$row_data[csf('job_no')]]['dealing_marchant']=$deal_merchant_arr[$row_data[csf('dealing_marchant')]];
	}
	
	if(count($po_id_arr)>0)
	{
	   $po_id=array_chunk($po_id_arr,999, true);
	   $po_cond_in="";
	   $ji=0;
	   foreach($po_id as $key=> $value)
	   {
		   if($ji==0)
		   {
				$po_cond_in=" and b.po_break_down_id in(".implode(",",$value).")"; 
				
		   }
		   else
		   {
				$po_cond_in.=" or b.po_break_down_id in(".implode(",",$value).")";
		   }
		   $ji++;
	   }
	   
	}// end if(count($po_id_arr)>0)
	
if($txt_search_common!="" && $cbo_search_type!=6)
{
	$po_cond_in=$po_cond_in;
	//echo $po_cond_in.'dd';
}
else { $po_cond_in="";}


		ob_start();
	?>
		<div style="width:2280px">
		<fieldset style="width:100%;">	
        
			<table width="2280">
				<tr class="form_caption">
					<td colspan="26" align="center"><? echo $report_title;?></td>
				</tr>
				<tr class="form_caption">
					<td colspan="26" align="center"><? echo $company_library[$company_name]; ?></td>
				</tr>
			</table>
			<table class="rpt_table" width="2280" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<th width="30">SL</th>
                    <th width="100">Buyer</th>
					<th width="100">Style</th>
                    <th width="70">Season</th>
					<th width="100">Job</th>
                    <th width="110">Booking No</th>
                    <th width="100">Supplier</th>
                    <th width="110">FSO No</th>
                    <th width="110">Garments Item</th>
					<th width="100">Body Part</th>
                    <th width="150">Fabric Description</th>
					<th width="80">Color Type</th>
					<th width="100">Fabric Color</th>
					<th width="60">UOM</th>
					<th width="80">Required Qty</th>
                    <th width="60">Rate</th>
					<th width="80">Adjust Qty</th>
                    <th width="80">Adjust Amount</th>
					<th width="80">Booking Qty.</th>
					<th width="80">Booking Amount</th>
					<th width="80">Booking Date</th>
                    <th width="80">Related Booking</th>
					<th width="80">Team Leader</th>
                    <th width="80">Dealing Merchant</th>
                    <th width="80">Adjusted</th>
                    <th width="">Remarks</th>
                    
				</thead>
			</table>
			<div style="width:2300px; max-height:400px; overflow-y:scroll" id="scroll_body">
			<table class="rpt_table" width="2280" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
	<?
	 
			$print_report_format_par=return_field_value("format_id"," lib_report_template","template_name =".$company_name."  and module_id=2 and report_id=35 and is_deleted=0 and status_active=1");
			///echo $print_report_format_par;
			$print_report_format_part=explode(",",$print_report_format_par);
			$print_report_format_id=$print_report_format_part[0];
			
			$sales_data=sql_select("select sales_booking_no,within_group,job_no as fso_no from  fabric_sales_order_mst  where status_active=1 and is_deleted=0 and sales_booking_no is not null");
			foreach($sales_data as $row)
			{
				$sales_no_arr[$row[csf('sales_booking_no')]]['fso_no']=$row[csf('fso_no')];
				$sales_no_arr[$row[csf('sales_booking_no')]]['within_group']=$row[csf('within_group')];
			}
			$fab_data="select a.entry_form,b.booking_no,c.id as fab_id,c.item_number_id,c.job_no as job_no,c.body_part_id,c.uom from  wo_booking_mst a,wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c  where a.booking_no=b.booking_no and a.company_id=$company_name and b.pre_cost_fabric_cost_dtls_id=c.id and b.job_no=c.job_no and a.booking_type in(1,4) and c.status_active=1 and c.is_deleted=0  and a.status_active=1 and a.is_deleted=0 $search_booking_cond $buyer_id_cond2 $booking_date_cond $po_cond_in group by a.entry_form,b.booking_no,c.id ,c.item_number_id,c.job_no ,c.body_part_id,c.uom";
			$result_fab_data=sql_select($fab_data);
			foreach($result_fab_data as $row)
			{
				$fabric_data_arr[$row[csf('fab_id')]]['item_id']=$row[csf('item_number_id')];
				$fabric_data_arr[$row[csf('fab_id')]]['body_part_id']=$row[csf('body_part_id')];
				$fabric_data_arr[$row[csf('fab_id')]]['uom']=$row[csf('uom')];
				if($row[csf('entry_form')]!=108)
				{
					$booking_related_data_arr[$row[csf('job_no')]]['booking_no'].=$row[csf('booking_no')].',';
				}
			}
		
			$sql_booking="Select a.id as booking_id,a.booking_no_prefix_num, a.booking_no,a.entry_form,a.item_category,a.pay_mode,a.remarks,a.fabric_source,a.is_approved, a.booking_date,a.supplier_id, a.buyer_id,b.pre_cost_fabric_cost_dtls_id as fab_cost_dtls_id,b.fabric_color_id,b.color_type,b.uom,b.gmts_color_id,b.construction,b.copmposition,b.dia_width,b.gsm_weight,b.pre_cost_remarks as pre_remark,b.remark, b.fin_fab_qnty as fin_fab_qnty, b.grey_fab_qnty as grey_fab_qnty, b.adjust_qty,b.wo_qnty as wo_qnty, b.amount as amount,b.rate, b.job_no,b.po_break_down_id as po_id 
			from wo_booking_mst a, wo_booking_dtls b  
			where a.booking_no=b.booking_no and a.company_id=$company_name and a.entry_form=108 and a.booking_type in(1) and a.is_short in(2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $search_booking_cond $buyer_id_cond2 $booking_date_cond $po_cond_in order by a.booking_no,b.job_no ";
						
				$booking_data=sql_select($sql_booking);
				$po_ids='';
				foreach($booking_data as $row)
				{
					
						if(($row[csf('gsm_weight')]!='' || $row[csf('gsm_weight')]!=0) && $row[csf('dia_width')]!='')
						{
						$gsm_dia=",".$row[csf('gsm_weight')].','.$row[csf('dia_width')];
						}
						else if($row[csf('gsm_weight')]!='' && $row[csf('dia_width')]=='')
						{
						$gsm_dia=",".$row[csf('gsm_weight')];
						}
					$fab_cost_dtls_id=$row[csf('fab_cost_dtls_id')];
					$fab_desc=$row[csf('construction')].','.$row[csf('copmposition')].$gsm_dia;
					$booking_data_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$fab_cost_dtls_id][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('pre_remark')]]['supplier_id']=$row[csf('supplier_id')];
					$booking_data_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$fab_cost_dtls_id][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('pre_remark')]]['uom']=$row[csf('uom')];
					$booking_data_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$fab_cost_dtls_id][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('pre_remark')]]['booking_date']=$row[csf('booking_date')];
					$booking_data_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$fab_cost_dtls_id][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('pre_remark')]]['construction']=$row[csf('construction')];
					$booking_data_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$fab_cost_dtls_id][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('pre_remark')]]['copmposition']=$row[csf('copmposition')];
					$booking_data_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$fab_cost_dtls_id][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('pre_remark')]]['gsm_weight']=$row[csf('gsm_weight')];
					$booking_data_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$fab_cost_dtls_id][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('pre_remark')]]['dia_width']=$row[csf('dia_width')];
					$booking_data_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$fab_cost_dtls_id][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('pre_remark')]]['gmts_color_id']=$row[csf('gmts_color_id')];
					$booking_data_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$fab_cost_dtls_id][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('pre_remark')]]['color_type']=$row[csf('color_type')];
					$booking_data_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$fab_cost_dtls_id][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('pre_remark')]]['item_category']=$row[csf('item_category')];
					$booking_data_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$fab_cost_dtls_id][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('pre_remark')]]['fab_desc']=$fab_desc;
					$booking_data_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$fab_cost_dtls_id][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('pre_remark')]]['fin_fab_qnty']+=$row[csf('fin_fab_qnty')];
					$booking_data_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$fab_cost_dtls_id][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('pre_remark')]]['grey_fab_qnty']+=$row[csf('grey_fab_qnty')];
					$booking_data_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$fab_cost_dtls_id][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('pre_remark')]]['amount']+=$row[csf('amount')];
					$booking_data_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$fab_cost_dtls_id][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('pre_remark')]]['adjust_qty']+=$row[csf('adjust_qty')];
					$booking_data_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$fab_cost_dtls_id][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('pre_remark')]]['adjust_amt']+=$row[csf('adjust_qty')]*$row[csf('rate')];
					$booking_data_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$fab_cost_dtls_id][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('pre_remark')]]['wo_qnty']+=$row[csf('wo_qnty')];
					
					$booking_data_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$fab_cost_dtls_id][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('pre_remark')]]['remark']=$row[csf('remark')];
					$booking_data_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$fab_cost_dtls_id][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('pre_remark')]]['fabric_source']=$row[csf('fabric_source')];
					$booking_data_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$fab_cost_dtls_id][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('pre_remark')]]['is_approved']=$row[csf('is_approved')];
					$booking_data_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$fab_cost_dtls_id][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('pre_remark')]]['booking_id']=$row[csf('booking_id')];
					$booking_data_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$fab_cost_dtls_id][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('pre_remark')]]['pay_mode']=$row[csf('pay_mode')];
						$booking_data_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$fab_cost_dtls_id][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('pre_remark')]]['remarks']=$row[csf('remarks')];
					$po_ids.=$row[csf('po_id')].',';
					$booking_data_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$fab_cost_dtls_id][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('pre_remark')]]['po_num'].=$row[csf('po_id')].',';
				}
				$po_ids=chop($po_ids,',');
				$po_idss=implode(",",array_unique(explode(",", $po_ids)));
				$condition= new condition();
				$condition->company_name("=$company_name");
				 if(str_replace("'","",$cbo_buyer_name)>0){
					  $condition->buyer_name("=$cbo_buyer_name");
				 }
				if($cbo_search_type==1)
				{
					//$search_cond="and a.job_no like '%".$txt_search_common."%'";
					  $condition->style_ref_no("like '%".$txt_search_common."%'");
				}
				else if($cbo_search_type==2)
				{
					//$search_cond="and b.po_number like '%".$txt_search_common."%'";
					 $condition->job_no("like '%".$txt_search_common."%'");
				}
				else if($cbo_search_type==3)
				{
					//$search_cond="and b.grouping like '%".$txt_search_common."%'";
					 $condition->po_number("like '%".$txt_search_common."%'");
				}
				else if($cbo_search_type==4)
				{
					//$search_cond="and b.file_no =".$txt_search_common."";
					 $condition->file_no("=".$txt_search_common."");
				}
				else if($cbo_search_type==5)
				{
					//$search_cond="and b.file_no =".$txt_search_common."";
					 $condition->grouping("=".$txt_search_common."");
				}
					
					 
				if(str_replace("'","",$po_idss) !=''){
				//$condition->po_id("in($po_idss)");
				}
				
				$condition->init();
				$fabric= new fabric($condition);
				// echo $fabric->getQuery(); die;
				$req_qty_arr=$fabric->getQtyArray_by_OrderFabriccostidGmtscolorDiaWidthAndRemarks_knitAndwoven_greyAndfinish();
				$req_amount_arr=$fabric->getAmountArray_by_OrderFabriccostidGmtscolorDiaWidthAndRemarks_knitAndwoven_greyAndfinish();
				//print_r($req_amount_arr);
			    $total_booking_amount=$total_grey_fin_qnty=$total_adjust_amt=$total_adjust_qty=$total_pre_req_qty=0;
				$i=1;
				foreach($booking_data_arr as $job_key=>$job_data)
				{
					foreach($job_data as $booking_key=>$booking_data)
					{
						foreach($booking_data as $fab_cost_id=>$fabric_data)
						{
							foreach($fabric_data as $dia_id=>$dia_data)
							{
								foreach($dia_data as $color_id=>$color_data)
								{
									foreach($color_data as $remark_id=>$val)
									{
				    					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";   
								
									$season=$job_wise_arr[$job_key]['season'];
									$matrix_season=$job_wise_arr[$job_key]['matrix_season'];
									$uom_id=$fabric_data_arr[$fab_cost_id]['uom'];
									if($season!=0 && $matrix_season==0)
									{
										$season_dynamic=$season;
									}
									else if($matrix_season!=0 && $season==0)
									{
										$season_dynamic=$matrix_season;
									}
									$gmts_color_id=$val['gmts_color_id'];
									$item_category=$val['item_category'];
									$fabric_source_id=$val['fabric_source'];
									$is_approved=$val['is_approved'];
									$booking_id=$val['booking_id'];
									$pay_mode=$val['pay_mode'];
									$remarks=$val['remarks'];
									$po_num=rtrim($val['po_num'],',');
								//	echo $po_num.'d';
									$po_num=array_unique(explode(",",$po_num));
									$pre_req_qty=$pre_req_amt=0;
									foreach($po_num as $pid)
									{
										if($item_category==2){
										$pre_req_qty+=$req_qty_arr['knit']['grey'][$pid][$fab_cost_id][$gmts_color_id][$dia_id][$remark_id][$uom_id];
										$pre_req_amt+=$req_amount_arr['knit']['grey'][$pid][$fab_cost_id][$gmts_color_id][$dia_id][$remark_id][$uom_id];
										//$pre_rate=$pre_req_amt/$pre_req_qty;
										}
										if($item_category==3){
										$pre_req_qty+=$req_qty_arr['woven']['grey'][$pid][$fab_cost_id][$gmts_color_id][$dia_id][$remark_id][$uom_id];
										$pre_req_amt+=$req_amount_arr['woven']['grey'][$pid][$fab_cost_id][$gmts_color_id][$dia_id][$remark_id][$uom_id];
										//$pre_rate=$pre_req_amt/$pre_req_qty;
										}
									}
									//$pre_rate=0;
									if($pre_req_amt) $pre_req_amt=$pre_req_amt;else $pre_req_amt=0;
									if($pre_req_qty) $pre_req_qty=$pre_req_qty;else $pre_req_qty=0;
									$pre_rate=$pre_req_amt/$pre_req_qty;
									if($pre_rate) $pre_rate=$pre_rate;else $pre_rate=0;
									//$job_key=$po_wise=_arr[$po_key]['job'];
									//echo $pre_req_amt.''.$pre_req_qty;
									
									$print_booking='';
									foreach($print_report_format_part as $row_id)
										{ 
												if($row_id==143 && $print_report_format_id==143) //partial 
												{ 												
												 $print_booking="<a href='#' onClick=\"generate_worder_report3('".$booking_key."','".$company_name."','".$po_key."','".$item_category."','".$fabric_source_id."','".$job_key."','".$is_approved."','".$row_id."','show_fabric_booking_report_urmi','".$i."')\"> ".$booking_key." <a/>";
												} 
												else if($row_id==84 && $print_report_format_id==84) //partial	
												{ 		
																					
												$print_booking="<a href='#' onClick=\"generate_worder_report3('".$booking_key."','".$company_name."','".$po_key."','".$item_category."','".$fabric_source_id."','".$job_key."','".$is_approved."','".$row_id."','show_fabric_booking_report_urmi_per_job','".$i."')\"> ".$booking_key." <a/>";
												}
												else if($row_id==85 && $print_report_format_id==85) //partial //	
												{ 
													
												 $print_booking="<a href='#' onClick=\"generate_worder_report3('".$booking_key."','".$company_name."','".$po_key."','".$item_category."','".$fabric_source_id."','".$job_key."','".$is_approved."','".$row_id."','print_booking_3','".$i."')\"> ".$booking_key." <a/>";
												 
												}
												else if($row_id==151 && $print_report_format_id==151) //partial 	
												{ 
												
												 $print_booking="<a href='#' onClick=\"generate_worder_report3('".$booking_key."','".$company_name."','".$po_key."','".$item_category."','".$fabric_source_id."','".$job_key."','".$is_approved."','".$row_id."','show_fabric_booking_report_advance_attire_ltd','".$i."')\"> ".$booking_key." <a/>";
												} 
												else if($row_id==160 && $print_report_format_id==160) //partial 	
												{ 
												
												 $print_booking="<a href='#' onClick=\"generate_worder_report3('".$booking_key."','".$company_name."','".$po_key."','".$item_category."','".$fabric_source_id."','".$job_key."','".$is_approved."','".$row_id."','print_booking_5','".$i."')\"> ".$booking_key." <a/>";
												} 
												else if($row_id==175 && $print_report_format_id==175) //partial 	
												{ 
												
												 $print_booking="<a href='#' onClick=\"generate_worder_report3('".$booking_key."','".$company_name."','".$po_key."','".$item_category."','".$fabric_source_id."','".$job_key."','".$is_approved."','".$row_id."','print_booking_6','".$i."')\"> ".$booking_key." <a/>";
												} 	
												else if($row_id==218 && $print_report_format_id==218) //partial 	
												{ 
												
												 $print_booking="<a href='#' onClick=\"generate_worder_report3('".$booking_key."','".$company_name."','".$po_key."','".$item_category."','".$fabric_source_id."','".$job_key."','".$is_approved."','".$row_id."','print_booking_7','".$i."')\"> ".$booking_key." <a/>";
												} 	
												else if($row_id==220 && $print_report_format_id==220) //partial 	
												{ 
												
												 $print_booking="<a href='#' onClick=\"generate_worder_report3('".$booking_key."','".$company_name."','".$po_key."','".$item_category."','".$fabric_source_id."','".$job_key."','".$is_approved."','".$row_id."','print_booking_northern_new','".$i."')\"> ".$booking_key." <a/>";
												} 	
												else if($row_id==235 && $print_report_format_id==235) //partial 	
												{ 
												
												 $print_booking="<a href='#' onClick=\"generate_worder_report3('".$booking_key."','".$company_name."','".$po_key."','".$item_category."','".$fabric_source_id."','".$job_key."','".$is_approved."','".$row_id."','print_booking_northern_9','".$i."')\"> ".$booking_key." <a/>";
												} 
												else if($row_id==274 && $print_report_format_id==274) //partial 	
												{ 
												
												 $print_booking="<a href='#' onClick=\"generate_worder_report3('".$booking_key."','".$company_name."','".$po_key."','".$item_category."','".$fabric_source_id."','".$job_key."','".$is_approved."','".$row_id."','print_booking_10','".$i."')\"> ".$booking_key." <a/>";
												} 
												else if($row_id==241 && $print_report_format_id==241) //partial 	
												{ 
												
												 $print_booking="<a href='#' onClick=\"generate_worder_report3('".$booking_key."','".$company_name."','".$po_key."','".$item_category."','".$fabric_source_id."','".$job_key."','".$is_approved."','".$row_id."','print_booking_11','".$i."')\"> ".$booking_key." <a/>";
												} 
												else if($row_id==269 && $print_report_format_id==269) //partial 	
												{ 
												
												 $print_booking="<a href='#' onClick=\"generate_worder_report3('".$booking_key."','".$company_name."','".$po_key."','".$item_category."','".$fabric_source_id."','".$job_key."','".$is_approved."','".$row_id."','print_booking_12','".$i."')\"> ".$booking_key." <a/>";
												} 
												else if($row_id==28 && $print_report_format_id==28) //partial	
												{ 		
																					
												$print_booking="<a href='#' onClick=\"generate_worder_report3('".$booking_key."','".$company_name."','".$po_key."','".$item_category."','".$fabric_source_id."','".$job_key."','".$is_approved."','".$row_id."','print_booking_13','".$i."')\"> ".$booking_key." <a/>";
												}
												else if($row_id==280 && $print_report_format_id==280) //partial 	
												{ 
												 $print_booking="<a href='#' onClick=\"generate_worder_report3('".$booking_key."','".$company_name."','".$po_key."','".$item_category."','".$fabric_source_id."','".$job_key."','".$is_approved."','".$row_id."','print_booking_14','".$i."')\"> ".$booking_key." <a/>";
												} 
												else if($row_id==304 && $print_report_format_id==304) //partial 	
												{ 
												
												 $print_booking="<a href='#' onClick=\"generate_worder_report3('".$booking_key."','".$company_name."','".$po_key."','".$item_category."','".$fabric_source_id."','".$job_key."','".$is_approved."','".$row_id."','print_booking_15','".$i."')\"> ".$booking_key." <a/>";
												} 
												
												else if($row_id==719 && $print_report_format_id==719) //partial 	
												{ 
												 $print_booking="<a href='#' onClick=\"generate_worder_report3('".$booking_key."','".$company_name."','".$po_key."','".$item_category."','".$fabric_source_id."','".$job_key."','".$is_approved."','".$row_id."','print_booking_16','".$i."')\"> ".$booking_key." <a/>";
												} 		
												
												else if($row_id==155 && $print_report_format_id==155) //partial 	
												{ 
												
												 $print_booking="<a href='#' onClick=\"generate_worder_report3('".$booking_key."','".$company_name."','".$po_key."','".$item_category."','".$fabric_source_id."','".$job_key."','".$is_approved."','".$row_id."','fabric_booking_report','".$i."')\"> ".$booking_key." <a/>";
												} 
												else if($row_id==723 && $print_report_format_id==723) //partial 	
												{ 
												
												 $print_booking="<a href='#' onClick=\"generate_worder_report3('".$booking_key."','".$company_name."','".$po_key."','".$item_category."','".$fabric_source_id."','".$job_key."','".$is_approved."','".$row_id."','print_booking_17','".$i."')\"> ".$booking_key." <a/>";
												} 
													
												
												
												
																																													
										}
										$form_caption="Fabric Sales Order Entry";
										$within_group=$sales_no_arr[$booking_key]['within_group'];
										//$data_row=$company_name.'*'.$booking_id.'*'.$booking_key.'*'.$job_key.'*'.$form_caption;
										 $print_sales_no="<a href='#' onClick=\"fabric_sales_order_print3('".$company_name."','".$booking_id."','".$booking_key."','".$job_key."','".$form_caption."','".$within_group."','".$row_id."','fabric_sales_order_print3','".$i."')\"> ".$sales_no_arr[$booking_key]['fso_no']." <a/>";
										 
										 if($pay_mode==3 || $pay_mode==5)
										 {
											$supplier_com=$company_library[$val['supplier_id']]; 
										 }
										 else
										 {
											 $supplier_com=$lib_supplier_arr[$val['supplier_id']]; 
										 }
										 
										 $related_booking=rtrim($booking_related_data_arr[$job_key]['booking_no'],',');
										 $related_booking_nos=implode(',',array_unique(explode(",",$related_booking)));
										// echo  $related_booking_nos.'D';
										 if($related_booking_nos!='')
										 {
										 	$related_booking="<a href='##' onClick=\"generate_related_booking('".$booking_key."','".$company_name."','".$job_key."','".$item_category."','".$related_booking_nos."','related_booking_popup')\"> Yes <a/>";	 
										}
										 else
										 {
											  $related_booking="No";	 
										 }
										  if($remarks!='')
										 {
										 	$booking_remarks="<a href='##' onClick=\"generate_related_booking('".$booking_key."','".$company_name."','".$job_key."','".$item_category."','".$related_booking_nos."','remark_booking_popup')\"> View <a/>";	 
										}
										 else
										 {
											  $booking_remarks="No";	 
										 }
										 //echo $remarks.'d';
									
													
				?>
                <tr style="font-size: 12px" align="center" bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
					<td width="30"><? echo $i; ?></td>
                    <td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $buyer_short_name_library[$job_wise_arr[$job_key]['job']];?></td>
					<td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $job_wise_arr[$job_key]['style']; ?></td> 
                    <td width="70" style="word-wrap:break-word; word-break: break-all;"><? echo $lib_season_name_arr[$season_dynamic]; ?></td>
					<td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $job_key; ?></td>
                    <td width="110" style="word-wrap:break-word; word-break: break-all;"><? echo $print_booking; ?></td>
                    <td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $supplier_com; ?></td>
                    <td width="110" style="word-wrap:break-word; word-break: break-all;"><? echo $print_sales_no; ?></td>
                    <td width="110" style="word-wrap:break-word; word-break: break-all;"><? echo $garments_item[$fabric_data_arr[$fab_cost_id]['item_id']]; ?></td>
					<td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $body_part[$fabric_data_arr[$fab_cost_id]['body_part_id']]; ?></td>
                    <td width="150" style="word-wrap:break-word; word-break: break-all;">
					<?					
						echo  $val['fab_desc'];  
					?>
                    </td>
					<td width="80" style="word-wrap:break-word; word-break: break-all;">
					<?
						echo $color_type[$val['color_type']];
					?>
                    </td>
					<td width="100" style="word-wrap:break-word; word-break: break-all;">
					<?
						echo $lib_color_arr[$color_id];
					?>
                    </td>
					<td width="60" style="word-wrap:break-word; word-break: break-all;">
					<?
						echo	$unit_of_measurement[$uom_id];
					?>
                    </td>
					<td width="80" title="From Budget" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<? echo number_format($pre_req_qty,2); ?>
                    </td>
                    <td width="60" title="From Budget" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<? echo number_format($pre_rate,2); ?>
                    </td>
					<td width="80" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<? echo number_format($val['adjust_qty'],2); ?>
                    </td>
                    <td width="80" style="word-wrap:break-word; word-break: break-all; text-align:right" >
					<? 
						echo number_format($val['adjust_amt'],2); 
					?>
                    </td>
					<td width="80" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<? echo number_format($val['fin_fab_qnty'],2); ?>
                    </td>
					<td width="80" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<? echo number_format($val['amount'],2);  ?>
                    </td>
                   
					<td width="80" style="word-wrap:break-word; word-break: break-all;">
                   <? echo change_date_format($val['booking_date']); ?>
                    </td>
                    <td width="80" style="word-wrap:break-word; word-break: break-all;">
                   <p><? echo $related_booking; ?> </p>
                    </td>
					<td width="80" style="word-wrap:break-word; word-break: break-all;">
					<? echo $job_wise_arr[$job_key]['team_leader']; ?>
                    </td>
					
                    <td width="80" style="word-wrap:break-word; word-break: break-all;">
					<? echo $job_wise_arr[$job_key]['dealing_marchant'] ?>
                    </td>
                     <td width="80" style="word-wrap:break-word; word-break: break-all;">
					<? if($val['adjust_qty']>0) echo 'Yes';else echo 'No'; ?>
                    </td>
                   
                    <td width="" style="word-wrap:break-word; word-break: break-all;" id="From Master part">
					 <? echo $booking_remarks; ?>
                    </td>
                    </tr>
                		<?				$total_pre_req_qty+=$pre_req_qty;
										$total_booking_amount+=$val['amount'];
										$total_grey_fin_qnty+=$val['fin_fab_qnty'];
										$total_adjust_qty+=$val['adjust_qty'];
										$total_adjust_amt+=$val['adjust_amt'];
										$i++;
									}
								}
							}
						}
					}
				}
				?>
                 
				</table>
				<table class="rpt_table" width="2280" cellpadding="0" cellspacing="0" border="1" rules="all">
					<tfoot>
					<th width="30"></th>
                    <th width="100"></th>
					<th width="100"></th>
                    <th width="70"></th>
					<th width="100"></th>
                   
                    <th width="110"></th>
                    <th width="100"></th>
                    <th width="110"></th>
                    <th width="110"></th>
					<th width="100"></th>
                    <th width="150"></th>
					<th width="80"></th>
					<th width="100"></th>
					<th width="60"></th>
					<th width="80" align="right" id="value_total_fab_req_qty"><? echo number_format($total_pre_req_qty,2); ?></th>
                    <th width="60"></th>
					<th width="80" align="right" id="value_total_adjust_qty"><? echo number_format($total_adjust_qty,2); ?></th>
                   
					<th width="80" align="right" id="value_total_adjust_amount"><? echo number_format($total_adjust_amt,2); ?></th>
                    <th width="80" align="right" id="value_total_grey_fin_qnty"><? echo number_format($total_grey_fin_qnty,2); ?></th>
					<th width="80" align="right" id="value_total_booking_amount"><? echo number_format($total_booking_amount,2); ?></th>
					<th width="80"></th>
                    <th width="80"></th>
					<th width="80"></th>
                    <th width="80"></th>
                    <th width="80"></th>
                    <th width=""></th>
					</tfoot>
				</table>
				</div>
			</fieldset>
		</div>
	<?
	}
//===========================================================================================================================================================
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename****$tot_rows";
	exit();	
}

if($action=="related_booking_popup")//remark_booking_popup
{
	//echo load_html_head_contents("Related Booking Info", "../../../", 1, 1,'','','');
	echo load_html_head_contents("Related Booking Info","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
$booking_data="select a.entry_form,a.booking_type,a.is_short,a.booking_date,b.booking_no,sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.amount) as amount from wo_booking_mst a, wo_booking_dtls b  where a.booking_no=b.booking_no and b.job_no='$job_no' and a.booking_type in(1,4) and b.status_active=1 and b.is_deleted=0 group by  a.entry_form,a.booking_type,a.is_short,a.booking_date,b.booking_no";
$result=sql_select($booking_data);

	?>
   <script>
    var tableFilters = 
		 {
			  
			col_operation: {
			   id: ["value_booking_qty","value_booking_amt"],
			   col: [4,5],
			   operation: ["sum","sum"],
			   write_method: ["innerHTML","innerHTML"]
			},
				
		 }
   </script>
   <fieldset style="width:520px; margin-left:20px"> 
    <div id="report_div" align="center">
    <table class="rpt_table" width="490" cellpadding="0" cellspacing="0" border="1" rules="all">
    <caption> Related All Booking No</caption>
	<thead>
        <th width="30">SL</th> 
        <th width="120">Booking no</th>
         <th width="70">Booking Type</th>
         <th width="100">Booking Date</th>
         <th width="70">Qty</th>
         <th width="">Amount</th>
    </thead>
  </table>
   <div style="max-height:425px; overflow-y:scroll; width:490px;" id="scroll_body">
   <table width="470" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_body_popup" >
    <?
	$i=1;$total_booking_qty=$total_booking_amt=0;
	foreach($result as $row)
	{
		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";   
		if($row[csf('entry_form')]!=108)
		{
			$is_short=$row[csf('is_short')];
			$booking_type=$row[csf('booking_type')];
			if($booking_type==1 && $is_short==2)
			{
				$booking_types="Main";	
			}
			else if($booking_type==1 && $is_short==1)
			{
				$booking_types="Short";	
			}
			else if($booking_type==4 && $is_short==2)
			{
				$booking_types="Sample";	
			}
	?>
   <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
        <td  width="30"><? echo $i;?></td>
        <td  width="120"><? echo  $row[csf('booking_no')];?></td>
        <td  width="70"><? echo  $booking_types;?></td>
        <td  width="100"><? echo  change_date_format($row[csf('booking_date')]);?></td>
        <td  width="70" align="right"><? echo  number_format($row[csf('fin_fab_qnty')],2);?></td>
        <td align="right"  width="" ><? echo  number_format($row[csf('amount')],2);?></td>
   </tr>
    <?
		$i++;
		$total_booking_qty+=$row[csf('fin_fab_qnty')];
		$total_booking_amt+=$row[csf('amount')];
		}
	}
	?>
   
    </table>
    <table width="470" cellspacing="0" border="1" class="tbl_bottom" rules="all" id="body_bottom" >
         <tr>
         <td width="30"></td>
         <td width="120"></td>
         <td width="70"></td>
         <td width="100">Total</td>
         <td align="right"  width="70" id="value_booking_qty"> <? echo  $total_booking_qty;?></td>
         <td align="right"   width=""  id="value_booking_amt"> <? echo  $total_booking_amt;?></td>
        </tr>
    </table>
   </div>
   </div>
      <script>
			setFilterGrid("table_body_popup",-1,tableFilters);
	</script>
    </fieldset>    
    <?
	exit();
}

if($action=="remark_booking_popup")//
{
	echo load_html_head_contents(" Booking Remark Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
$booking_data="select a.remarks from wo_booking_mst a  where a.booking_no='$booking_no' and a.entry_form=108 and a.booking_type=1 and a.status_active=1 and a.is_deleted=0 group by a.remarks";
$result=sql_select($booking_data);
	?>
   <fieldset style="width:480px; margin-left:10px"> 
    <div id="report_div" align="center">
    <table class="rpt_table" width="470" cellpadding="0" cellspacing="0" border="1" rules="all">
    <caption> Booking Remark</caption>
	<thead>
        <th width="30">SL</th> 
        <th>Remark</th>
    </thead>
    <tbody id="table_body_popup">
    <?
	$i=1;
	foreach($result as $row)
	{
		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";   
		
	?>
   <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
        <td><? echo $i;?></td>
        <td><p><? echo  $row[csf('remarks')];?></p></td>
   </tr>
    <?
	$i++;
	}
	?>
    <tr>
    </tbody>
    </table>
    </div>
     <script>   setFilterGrid("table_body_popup",-1);</script>
    </fieldset>    
    
    <?
	exit();
}

	

if($action=="remarks_veiw")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$sql_lapdib_comments=sql_select("select color_name_id,lapdip_comments from wo_po_lapdip_approval_info where po_break_down_id in($po_id) and color_name_id=$color_id  and is_deleted=0 and status_active=1");//and  (send_to_factory_date='$send_to_factory_date' or recv_from_factory_date='$recv_from_factory_date' or submitted_to_buyer='$submitted_to_buyer' or approval_status_date='$approval_status_date') 
	
	$sql_sample_comments=sql_select("select a.color_number_id,b.sample_comments from  wo_po_color_size_breakdown a, wo_po_sample_approval_info b , lib_sample c where a.po_break_down_id=b.po_break_down_id and a.id=b.color_number_id  and b.sample_type_id=c.id	and b.po_break_down_id in($po_id) and c.sample_type=2 and a.color_number_id=$color_id    and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1");//and  (b.submitted_to_buyer='$pp_submitted_to_buyer' or b.approval_status_date='$pp_approval_status_date') 
	?>
    <table class="rpt_table" width="530" cellpadding="0" cellspacing="0" border="1" rules="all">
	<thead>
    <th width="30">SL</th> 
    <th>Comments</th>
    </thead>
    <tbody>
    <tr>
    <td colspan="3"><strong>Lapdib Comments</strong></td>
    </tr>
    
    <?
	$i=1;
	foreach($sql_lapdib_comments as $row_lapdib_comments)
	{
	?>
    <tr>
    <td><? echo $i;?></td>
    <td><? echo  $row_lapdib_comments[csf('lapdip_comments')];?></td>
    </tr>
    <?
	$i++;
	}
	?>
    <tr>
    <td colspan="3"><strong>Sample Comments</strong></td>
    </tr>
    
    <?
	$i=1;
	foreach($sql_sample_comments as $row_sample_comments)
	{
	?>
    <tr>
    <td><? echo $i;?></td>
    <td><? echo  $row_sample_comments[csf('sample_comments')];?></td>
    </tr>
    <?
	$i++;
	}
	?>
    </tbody>
    </table>
    <?
}

if($action=="booking_date_view")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$sql_booking=sql_select("select b.booking_no, b.booking_date from wo_pre_cost_fabric_cost_dtls a,wo_booking_mst b, wo_booking_dtls c where a.job_no=b.job_no and a.job_no=c.job_no and a.id=c.pre_cost_fabric_cost_dtls_id and b.booking_no=c.booking_no and b.booking_type=4 and b.item_category=2 and b.fabric_source=1  and c.po_break_down_id in($po_id) and c.fabric_color_id=$color_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.booking_no,b.booking_date");//and a.body_part_id in(1,14,15,16,17,20)
	?>
    <table class="rpt_table" width="310" cellpadding="0" cellspacing="0" border="1" rules="all">
	<thead>
    <th width="30">SL</th> 
    <th width="100">Booking No</th>
    <th>Booking Date</th>
    </thead>
    <tbody>
    <?
	$i=1;
	foreach($sql_booking as $row_booking)
	{
	?>
    <tr>
    <td><? echo $i;?></td>
    <td><? echo  $row_booking[csf('booking_no')];?></td>
    <td>
	<? 
	if($row_booking[csf('booking_date')] !="" && $row_booking[csf('booking_date')] !="0000-00-00" && $row_booking[csf('booking_date')] !="0")
	{
	echo  change_date_format($row_booking[csf('booking_date')],'dd-mm-yyyy','-');
	}
	?>
    </td>
    </tr>
    <?
	$i++;
	}
	?>
    </tbody>
    </table>
    <?
}

if($action=="fin_receive_date_view")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

					
	$sql_fin_fab_receive_qty=sql_select("select  a.receive_date,a.booking_id,a.booking_no
					from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c 
					where a.id=b.mst_id  and a.entry_form=37 and  a.item_category=2  and b.id=c.dtls_id and b.trans_id=c.trans_id and c.trans_type=1 and c.po_breakdown_id in($po_id) and c.color_id=$color_id $booking_id   and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by  a.receive_date,a.booking_id,a.booking_no");
	?>
    <table class="rpt_table" width="310" cellpadding="0" cellspacing="0" border="1" rules="all">
	<thead>
    <th width="30">SL</th> 
    <th width="100">Booking No</th>
    <th>Receive Date</th>
    </thead>
    <tbody>
    <?
	$i=1;
	foreach($sql_fin_fab_receive_qty as $row_fin_fab_receive_qty)
	{
	?>
    <tr>
    <td><? echo $i;?></td>
    <td><? echo  $row_fin_fab_receive_qty[csf('booking_no')];?></td>
    <td>
	<? 
	if($row_fin_fab_receive_qty[csf('receive_date')] !="" && $row_fin_fab_receive_qty[csf('receive_date')] !="0000-00-00" && $row_fin_fab_receive_qty[csf('receive_date')] !="0")
	{
	echo  change_date_format($row_fin_fab_receive_qty[csf('receive_date')],'dd-mm-yyyy','-');
	}
	?>
    </td>
    </tr>
    <?
	$i++;
	}
	?>
    </tbody>
    </table>
    <?
}
?>