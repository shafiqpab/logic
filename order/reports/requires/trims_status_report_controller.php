<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.trims.php');
$user_id = $_SESSION['logic_erp']["user_id"];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$fabric_nature = $_SESSION['fabric_nature'];
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');

//--------------------------------------------------------------------------------------------------------------------
if($action=="print_button_variable_setting")
{
	$print_report_format_arr = return_library_array("select format_id,format_id from lib_report_template where template_name =$data and module_id=2 and report_id=75 and is_deleted=0 and status_active=1","format_id","format_id");
	echo "print_report_button_setting('".implode(',',$print_report_format_arr)."');\n";
	exit();
}

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );
	exit();
}

if ($action=="style_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
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
 <div align="center"><? echo create_drop_down( "cbo_string_search_type", 140, $string_search_type,'', 1, "-- Searching Type --" ); ?></div>
     <?
	
	//$type_id=str_replace("'","",$type_id);
	//echo $data[2].'d,';
	
	if($data[2]==1)
	{
		if ($data[0]==0) $company_name=""; else $company_name=" and a.company_name='$data[0]'";
		if ($data[1]==0) $buyer_name=""; else $buyer_name=" and a.buyer_name='$data[1]'";
		
		$yearCond="";
		if($data[3]==0) $yearCond="";
		else
		{
			if($db_type==0) $yearCond=" and YEAR(a.insert_date)='$data[3]'"; 
			else if($db_type==2) $yearCond=" and to_char(a.insert_date,'YYYY')='$data[3]'";
		}
		if($db_type==0) $year_field=",YEAR(a.insert_date) as year"; 
		else if($db_type==2) $year_field=",to_char(a.insert_date,'YYYY') as year";
		else $year_field="";

		$sql ="select b.id, b.po_number, a.job_no_prefix_num as job_prefix $year_field from wo_po_details_master a, wo_po_break_down b  where a.job_no=b.job_no_mst $company_name $buyer_name $yearCond order by id desc"; 
		//echo $sql;
		echo create_list_view("list_view", "PO No,Job No,Year","200,100,100","450","350",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "po_number,job_prefix,year", "","setFilterGrid('list_view',-1)","0","",1) ;
	}
	else
	{
		if ($data[0]==0) $company_name=""; else $company_name="company_name='$data[0]'";
		if ($data[1]==0) $buyer_name=""; else $buyer_name=" and buyer_name='$data[1]'";
		
		$yearCond="";
		if($data[3]==0) $yearCond="";
		else
		{
			if($db_type==0) $yearCond="and YEAR(insert_date)='$data[3]'"; 
			else if($db_type==2) $yearCond="and to_char(insert_date,'YYYY')='$data[3]'";
		}
		if($db_type==0) $year_field=",YEAR(insert_date) as year"; 
		else if($db_type==2) $year_field=",to_char(insert_date,'YYYY') as year";
		else $year_field="";
		
		
		
		$sql ="select id, style_ref_no, job_no_prefix_num as job_prefix $year_field from wo_po_details_master where $company_name $buyer_name $yearCond order by id desc"; 
		echo create_list_view("list_view", "Style Ref. No.,Job No,Year","200,100,100","450","350",0, $sql , "js_set_value", "id,job_prefix", "", 1, "0", $arr, "style_ref_no,job_prefix,year", "","setFilterGrid('list_view',-1)","0","",1) ;
	}
		
	exit();	 
}

$tmplte=explode("**",$data);
if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;


if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_library=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name");
	$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name");
	$country_name_library=return_library_array( "select id, country_name from lib_country", "id", "country_name");
	$lib_supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
	$colorArr=return_library_array( "select id, color_name from lib_color", "id", "color_name");

	$company_name=str_replace("'","",$cbo_company_name);
	$serch_by=str_replace("'","",$cbo_search_by);
	$txt_style_id=str_replace("'","",$txt_style_id);
	$buyer_id_cond="";
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)

	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_job_no=trim($txt_job_no);
	$txt_style_id=trim($txt_style_id);
	if($txt_style_id!="")
	{
		if($txt_style_id!="" || $txt_style_id!=0) 
		{ //$jobcond="and a.id in($txt_style_id) ";
			$styleArr=array_unique(explode(",",$txt_style_id));
		 $jobcond=where_con_using_array($styleArr,0,'a.id');
		}
		else { $jobcond="";}
		
	}
	else
	{
		if($txt_job_no!="" || $txt_job_no!=0) $jobcond="and a.job_no_prefix_num in('".$txt_job_no."')"; else $jobcond="";
		if(str_replace("'","",$txt_style_ref)!="") $style_ref_cond=" and a.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'"; else $style_ref_cond="";
	}
	
	$cbo_year=str_replace("'","",$cbo_year); $year_cond="";
	if($db_type==0)
	{
		if(trim($cbo_year)!=0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
	}
	else if($db_type==2)
	{
		if(trim($cbo_year)!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
	}
	//echo $file_no_cond.'=='.$internal_ref_cond;die;

	//Precost v2 print button.........................................................
	$pre_cost2_print_button_arr=return_library_array( "select template_name,format_id from lib_report_template where module_id = 2 and report_id = 43 and is_deleted = 0 and status_active=1", "template_name", "format_id"  );
	list($first_print_button)=explode(',',$pre_cost2_print_button_arr[$company_name]);
	$print_button_action_arr=array(50=>'preCostRpt',51=>'preCostRpt2',52=>'bomRpt',63=>'bomRpt2',156=>'accessories_details',157=>'accessories_details2',158=>'preCostRptWoven',159=>'bomRptWoven',170=>'preCostRpt3',171=>'preCostRpt4',142=>'preCostRptBpkW',192=>'checkListRpt');
	$print_button_action = $print_button_action_arr[$first_print_button];

	if(str_replace("'","",$cbo_search_by)==1) //Order Wise
	{
		if($template==1)
		{
			ob_start();
			?>
			<div style="width:2650px">
			<table width="2650">
                <tr class="form_caption"><td colspan="29" align="center"><?=$report_title; ?></td></tr>
                <tr class="form_caption"><td colspan="29" align="center"><?=$company_library[$company_name]; ?></td></tr>
			</table>
			<table style="margin-left:1050px; margin-top:5px" id="table_notes">
				<tr align="center">
                    <td bgcolor="yellow" height="15" width="30"></td>
                    <td>WO Qty Fully or Partial Pending with Req Qty</td>
                    <td bgcolor="green" height="15" width="30">&nbsp;</td>
                    <td>WO Qty equal with Req Qty </td>
                    <td bgcolor="red" height="15" width="30"></td>
                    <td>WO Qty greater than Req Qty</td>
				</tr>
				<tr>
                    <td colspan="6" align="center">
                        (All WO Qty will calculate with Conversion Factor)
                    </td>
				</tr>
			</table>

			<table class="rpt_table" width="2650" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <th width="30">SL</th>
                    <th width="60">Buyer</th>
                    <th width="60">Client</th>
                    <th width="90">Job No</th>
                    <th width="100">Style Ref</th>
                    <th width="100">Order No</th>
                    <th width="80">Order Qty</th>
                    <th width="50">UOM</th>
                    <th width="80">Qty (Pcs)</th>
                    <th width="100">Trims Name</th>
                    <th width="140">Item Description</th>
                    <th width="50">Trims UOM</th>
                    <th width="70">Avg. Cons</th>
                    <th width="100">Req. Qty</th>
                    <th width="100">BOM Value (USD)</th>
                    <th width="100">GMTS Color</th>
                    <th width="100">Item Color</th>
                    <th width="80">Item Size</th>
                    <th width="80">WO Qty</th>
                    <th width="90">WO Value (USD)</th>
                    <th width="80">In-House Qty</th>
                    <th width="90">In-House Value [USD]</th>
                    <th width="90">In-House/Receive Blance</th>
                    <th width="80">Issue to Prod.</th>
                    <th width="90">Issue Value[USD]</th>
                    <th width="80">Left Over/ Balance (Qty)</th>
                    
                    <th width="90">Left Over Value (USD)</th>
                    <th width="140">Supplier</th>
                    <th width="120">PI No</th>
                    <th>LC NO</th>
                </thead>
			</table>
			<div style="width:2650px; max-height:400px; overflow-y:scroll" id="scroll_body">
			<table class="rpt_table" width="2630" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
			<?
			
			$conversion_factor_array=array();$item_arr=array();
			$conversion_factor=sql_select("select id,trim_uom,order_uom,conversion_factor from lib_item_group where status_active=1");
			foreach($conversion_factor as $row_f)
			{
				$conversion_factor_array[$row_f[csf('id')]]['con_factor']=$row_f[csf('conversion_factor')];
				$conversion_factor_array[$row_f[csf('id')]]['cons_uom']=$row_f[csf('trim_uom')];
				$item_arr[$row_f[csf('id')]]['order_uom']=$row_f[csf('order_uom')];
			}
			unset($conversion_factor);
			
			/*$app_sql=sql_select("select job_no_mst,accessories_type_id,approval_status from wo_po_trims_approval_info");
			$app_status_arr=array();
			foreach($app_sql as $row)
			{
				$app_status_arr[$row[csf("job_no_mst")]][$row[csf("accessories_type_id")]]=$row[csf("approval_status")];
			}
			unset($app_sql);*/

			/*$sql_po_qty_country_wise_arr=array();
			$po_job_arr=array();
			$sql_po_country_data=sql_select("select  b.id,b.job_no_mst,c.country_id, sum(c.order_quantity/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst  and b.id=c.po_break_down_id   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond group by   b.id,b.job_no_mst,c.country_id order by b.id,b.job_no_mst,c.country_id");
			foreach( $sql_po_country_data as $sql_po_country_row)
			{
				$sql_po_qty_country_wise_arr[$sql_po_country_row[csf('id')]][$sql_po_country_row[csf('country_id')]]=$sql_po_country_row[csf('order_quantity_set')];
				$po_job_arr[$sql_po_country_row[csf('id')]]=$sql_po_country_row[csf('job_no_mst')];
			}
			unset($sql_po_country_data);*/

			$po_data_arr=array();
			$po_id_string="";
			$today=date("Y-m-d");

			$txt_po_id=str_replace("'","",$txt_po_id);
			$txt_po_id=trim($txt_po_id);
			if($txt_po_id!="")
			{
				if($txt_po_id!="" || $txt_po_id!=0)
				{
				// $jobcond="and b.id in($txt_po_id) ";
				$po_idArr=array_unique(explode(",",$txt_po_id));
				 $jobcond=where_con_using_array($po_idArr,0,'b.id');
				}
				  else { $jobcond="";}
			}

			$sql_pos=("select a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.order_uom, a.client_id, b.id, b.po_number, (c.order_quantity) as order_quantity, (c.order_quantity/a.total_set_qnty) as order_quantity_set
			from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
			where
			a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.company_name=$company_name $buyer_id_cond ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')."  $jobcond $year_cond order by b.id ASC");//and a.job_no='UG-20-00182'
			//echo $sql_pos; die;
			$sql_po=sql_select($sql_pos);
			$po_arr=array(); $tot_rows=0;
			foreach($sql_po as $row)
			{
				$po_arr[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
				$po_arr[$row[csf('id')]]['client']=$row[csf('client_id')];
				$po_arr[$row[csf('id')]]['job_no']=$row[csf('job_no')];
				$po_arr[$row[csf('id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
				$po_arr[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')];
				$po_arr[$row[csf('id')]]['order_uom']=$row[csf('order_uom')];
				$po_arr[$row[csf('id')]]['po_number']=$row[csf('po_number')];
				$po_arr[$row[csf('id')]]['order_quantity']+=$row[csf('order_quantity')];
				$po_arr[$row[csf('id')]]['order_quantity_set']+=$row[csf('order_quantity_set')];
				$po_id_string.=$row[csf('id')].",";
				$po_idArr[$row[csf('id')]]=$row[csf('id')];
			}
			unset($sql_po);
			$expoid=array_filter(array_unique(explode(",",$po_id_string)));
			$tot_rows=count($expoid);
			$poIds=implode(",",$expoid);
			if($poIds=="")
			{
				echo "<strong style='color:red;font-size:30px'>No Data found</strong>";
				die;
			}

			//$order_cond=""; $order_cond1=""; $order_cond2=""; $precost_po_cond="";
			/*if($db_type==2 && $tot_rows>1000)
			{
				$order_cond=" and (";
				$order_cond1=" and (";
				$order_cond2=" and (";
				$precost_po_cond=" and (";
				$poIdsArr=array_chunk(explode(",",$poIds),999);
				foreach($poIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					//$poIds_cond.=" po_break_down_id in($ids) or ";
					$order_cond.=" b.po_break_down_id in($ids) or";
					$order_cond1.=" b.po_breakdown_id in($ids) or";
					$order_cond2.=" d.po_breakdown_id in($ids) or";
					$precost_po_cond.=" c.po_break_down_id in($ids) or";
				}
				$order_cond=chop($order_cond,'or ');
				$order_cond.=")";
				$order_cond1=chop($order_cond1,'or ');
				$order_cond1.=")";
				$order_cond2=chop($order_cond2,'or ');
				$order_cond2.=")";
				$precost_po_cond=chop($precost_po_cond,'or ');
				$precost_po_cond.=")";
			}
			else

			{
				$order_cond=" and b.po_break_down_id in($poIds)";
				$order_cond1=" and b.po_breakdown_id in($poIds)";
				$order_cond2=" and d.po_breakdown_id in($poIds)";
				$precost_po_cond=" and c.po_break_down_id in($poIds)";
			}*/

			$condition= new condition();
			
			$condition->company_name("=$cbo_company_name");
			if(str_replace("'","",$cbo_buyer_name)>0){
				 $condition->buyer_name("=$cbo_buyer_name");
			}
			if($poIds!="")
			{
				$condition->po_id_in("$poIds");
			}
			
			$condition->init();
			$trim= new trims($condition);
			//echo $trim->getQuery(); die;
			$trim_qty=$trim->getQtyArray_by_orderCountryAndPrecostdtlsid();
			//print_r($trim_qty);
			$trim= new trims($condition);
			$trim_amount=$trim->getAmountArray_by_orderCountryAndPrecostdtlsid();
			
			 $precost_po_cond=where_con_using_array($po_idArr,0,'c.po_break_down_id');
			 $order_cond=where_con_using_array($po_idArr,0,'b.po_break_down_id');
			 $order_cond1=where_con_using_array($po_idArr,0,'b.po_breakdown_id');
			 $order_cond2=where_con_using_array($po_idArr,0,'d.po_breakdown_id');
			
			$budget_arr=array();
			$sqlbom="select a.costing_per, a.costing_date as costing_date, b.id as trim_dtla_id, b.trim_group, b.description, b.cons_uom, b.cons_dzn_gmts, b.rate, b.amount, b.nominated_supp, c.cons, c.country_id, c.cons as cons_cal, c.po_break_down_id,c.color_number_id,c.item_color_number_id,c.item_size
			from wo_pre_cost_mst a, wo_pre_cost_trim_cost_dtls b, wo_pre_cost_trim_co_cons_dtls c
			where c.job_no=b.job_no and b.id=c.wo_pre_cost_trim_cost_dtls_id and a.job_no=b.job_no and c.cons>0
			and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1
			 $precost_po_cond
			order by b.id asc";
			//echo $sqlbom; die;
			$sql_pre_cost=sql_select($sqlbom);
			
			foreach($sql_pre_cost as $rowp)
			{
				$dzn_qnty=0;

				if($rowp[csf('costing_per')]==1) $dzn_qnty=12;
				else if($rowp[csf('costing_per')]==3) $dzn_qnty=12*2;
				else if($rowp[csf('costing_per')]==4) $dzn_qnty=12*3;
				else if($rowp[csf('costing_per')]==5) $dzn_qnty=12*4;
				else $dzn_qnty=1;
				
				$po_id=$rowp[csf('po_break_down_id')];
				$bomDtlsId=$rowp[csf('trim_dtla_id')];
				
				$po_qty=0;$req_qnty=0; $req_value=0;
				if($rowp[csf('country_id')]==0)
				{
					$po_qty=$po_arr[$po_id]['order_quantity'];
					$req_qnty+=$trim_qty[$po_id][$rowp[csf('country_id')]][$bomDtlsId];
				}
				else
				{
					$country_id= explode(",",$rowp[csf('country_id')]);
					for($cou=0;$cou<=count($country_id); $cou++)
					{
						$po_qty+=$sql_po_qty_country_wise_arr[$po_id][$country_id[$cou]];
						$req_qnty+=$trim_qty[$po_id][$country_id[$cou]][$bomDtlsId];
						//$req_value+=$trim_amount[$po_id][$rowp[csf('country_id')]][$bomDtlsId];
					}
				}
				$req_value=$rowp[csf('rate')]*$req_qnty;
				
				$po_data_arr[$po_id][$bomDtlsId]['trim_dtla_id']=$bomDtlsId;// for rowspan
				$po_data_arr[$po_id][$bomDtlsId]['trim_group']=$rowp[csf('trim_group')];
				$po_data_arr[$po_id][$bomDtlsId]['req_qnty']=$req_qnty;
				$po_data_arr[$po_id][$bomDtlsId]['req_value']=$req_value;
				$po_data_arr[$po_id][$bomDtlsId]['cons_uom']=$rowp[csf('cons_uom')];
				$po_data_arr[$po_id][$bomDtlsId]['trim_group_from']="BOM";
				$po_data_arr[$po_id][$bomDtlsId]['description']=$rowp[csf('description')];
				$po_data_arr[$po_id][$bomDtlsId]['country_id'].=$rowp[csf('country_id')].',';
				$po_data_arr[$po_id][$bomDtlsId]['avg_cons']=$rowp[csf('cons_dzn_gmts')];
				$po_data_arr[$po_id][$bomDtlsId]['color_number_id'].=$colorArr[$rowp[csf('color_number_id')]].',';
				$po_data_arr[$po_id][$bomDtlsId]['item_color_number_id'].=$colorArr[$rowp[csf('item_color_number_id')]].',';
				$po_data_arr[$po_id][$bomDtlsId]['item_size'].=$rowp[csf('item_size')].',';

				$budget_arr[$po_id]['costing_per']=$rowp[csf('costing_per')];
				$budget_arr[$po_id]['costing_date']=$rowp[csf('costing_date')];
			}
			/* echo '<pre>';
			print_r($po_data_arr);die; */
			unset($sql_pre_cost);

			if($db_type==2)
			{
				/*$sqlBookingWithoutBom="select min(a.booking_date) as booking_date, b.job_no, LISTAGG(CAST(a.booking_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as booking_no, LISTAGG(CAST(a.supplier_id || '**' || a.pay_mode AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as supplier_id,
				LISTAGG(CAST(b.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as booking_dtls_id,
				 b.po_break_down_id, b.trim_group, b.pre_cost_fabric_cost_dtls_id, sum(b.wo_qnty) as wo_qnty, sum(b.amount/b.exchange_rate) as amount, sum(b.rate) as rate
				from wo_booking_mst a, wo_booking_dtls b
				where a.item_category=4 and a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name $order_cond 
				group by b.po_break_down_id, b.trim_group, b.job_no, b.pre_cost_fabric_cost_dtls_id";*///and item_from_precost=2
				
				 $sqlBookingWithoutBom="select (a.booking_date) as booking_date, b.job_no,a.pay_mode, a.booking_no as booking_no, a.supplier_id as supplier_id,
				 b.id as booking_dtls_id,
				 b.po_break_down_id, b.trim_group, b.pre_cost_fabric_cost_dtls_id, (b.wo_qnty) as wo_qnty, (b.amount/b.exchange_rate) as amount, (b.rate) as rate
				from wo_booking_mst a, wo_booking_dtls b
				where a.item_category=4 and a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name $order_cond ";
			}
			else if($db_type==0)
			{
				$sqlBookingWithoutBom="select min(a.booking_date) as booking_date, b.job_no, group_concat(a.booking_no) as booking_no, group_concat(concat_ws('**',a.supplier_id,a.pay_mode)) as supplier_id, b.po_break_down_id, b.trim_group, b.pre_cost_fabric_cost_dtls_id, sum(b.wo_qnty) as wo_qnty, sum(b.amount/b.exchange_rate) as amount, sum(b.rate) as rate
				from wo_booking_mst a, wo_booking_dtls b
				where a.item_category=4 and a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name $order_cond group by b.po_break_down_id, b.trim_group, b.job_no, b.pre_cost_fabric_cost_dtls_id";//and item_from_precost=2
			}
			//echo $sqlBookingWithoutBom; die;
			$sqlBookingWithoutBomData=sql_select($sqlBookingWithoutBom);
			$booking_precost_id=array(); $bookingId="";
			foreach($sqlBookingWithoutBomData as $worow)
			{
				$conversion_factor_rate=$conversion_factor_array[$worow[csf('trim_group')]]['con_factor'];
				$cons_uom=$item_arr[$worow[csf('trim_group')]]['order_uom'];
			//	$booking_no=implode(",",array_unique(explode(",",$worow[csf('booking_no')])));
				//$supplier_id=implode(",",array_unique(explode(",",$worow[csf('supplier_id')])));
				$booking_no=$worow[csf('booking_no')];
				$supplier_id=$worow[csf('supplier_id')];
				$bookingId.=$worow[csf('booking_dtls_id')].",";
				
				$wo_qnty=$worow[csf('wo_qnty')];
				$amount=$worow[csf('amount')];
				$wo_date=$worow[csf('booking_date')];
				
			//	$bookingId.=$worow[csf('booking_dtls_id')].",";

				$trim_dtla_id=$worow[csf('pre_cost_fabric_cost_dtls_id')];
				/*$booking_id_arr=array_unique(explode(",",$worow[csf('booking_dtls_id')]));
				foreach($booking_id_arr as $book_id)
				{
					$booking_precost_id[$book_id]=$trim_dtla_id;
				}*/
				$booking_precost_id[$worow[csf('booking_dtls_id')]]=$trim_dtla_id;

				$po_data_arr[$worow[csf('po_break_down_id')]][$trim_dtla_id]['wo_qnty']+=$wo_qnty;
				$po_data_arr[$worow[csf('po_break_down_id')]][$trim_dtla_id]['amount']+=$amount;
				$po_data_arr[$worow[csf('po_break_down_id')]][$trim_dtla_id]['booking_no'].=$booking_no.',';
				$po_data_arr[$worow[csf('po_break_down_id')]][$trim_dtla_id]['supplier_id'].=$worow[csf('supplier_id')].'**'.$worow[csf('pay_mode')].',';
				//$po_data_arr[$worow[csf('po_break_down_id')]][$trim_dtla_id]['pay_mode'].=$worow[csf('pay_mode')].',';
				$po_data_arr[$worow[csf('po_break_down_id')]][$trim_dtla_id]['conversion_factor_rate']=$conversion_factor_rate;
			}
			unset($sqlBookingWithoutBomData);
			
			$exbookingid=array_filter(array_unique(explode(",",$bookingId)));
			$tot_rowsbook=count($exbookingid);
			$bookingIds=implode(",",$exbookingid);

			/*$bookingConsCond="";
			if($db_type==2 && $tot_rowsbook>1000)
			{
				$bookingConsCond=" and (";
				
				$bookingIdsArr=array_chunk(explode(",",$bookingIds),999);
				foreach($bookingIdsArr as $bids)
				{
					$bids=implode(",",$bids);
					$bookingConsCond.=" wo_trim_booking_dtls_id in($bids) or";
				}
				$bookingConsCond=chop($bookingConsCond,'or ');
				$bookingConsCond.=")";
			}
			else
			{
				$bookingConsCond=" and wo_trim_booking_dtls_id in($bookingIds)";
			}*/
			
			 $bookingConsCond=where_con_using_array($exbookingid,0,'wo_trim_booking_dtls_id');
			 $PIbookingConsCond=where_con_using_array($exbookingid,0,'b.work_order_dtls_id');
			
			 $sqlBookCons="select wo_trim_booking_dtls_id, job_no, po_break_down_id, color_number_id, gmts_sizes, item_color, item_size, cons, requirment, booking_no, description, rate, amount from wo_trim_book_con_dtls where status_active=1 and is_deleted=0 $bookingConsCond";
			//echo $sqlBookCons; die;
			$sqlBookConsData=sql_select($sqlBookCons); $bookingConsArr=array();
			foreach($sqlBookConsData as $crow)
			{
				if($crow[csf('color_number_id')]==0) $crow[csf('color_number_id')]="";
				if($crow[csf('item_color')]==0) $crow[csf('item_color')]="";
				if($crow[csf('item_size')]=='0') $crow[csf('item_size')]="";
				
				$bom_id=$booking_precost_id[$crow[csf('wo_trim_booking_dtls_id')]];
				$bookingConsArr[$crow[csf('po_break_down_id')]][$bom_id][$crow[csf('color_number_id')]][$crow[csf('item_color')]][$crow[csf('item_size')]]['qty']+=$crow[csf('cons')];
				$bookingConsArr[$crow[csf('po_break_down_id')]][$bom_id][$crow[csf('color_number_id')]][$crow[csf('item_color')]][$crow[csf('item_size')]]['amt']+=$crow[csf('amount')];
			}
			unset($sqlBookConsData);
			 
			//echo "<pre>";print_r($bookingConsArr);die;

			$sqlRec="select b.po_breakdown_id, a.item_description, a.item_group_id, c.receive_basis, a.booking_id, b.quantity as quantity, b.order_rate as rate, c.exchange_rate, (b.quantity*b.order_rate) as amount, d.color, d.item_color, d.item_size
			from inv_trims_entry_dtls a, order_wise_pro_details b, inv_receive_master c, product_details_master d
			where a.mst_id=c.id and a.trans_id=b.trans_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_cond1 order by a.item_group_id ";
			$sqlRecData=sql_select($sqlRec); $recDataArr=array();
			//echo $sqlRec; die;
			foreach($sqlRecData as $row)
			{
				if($row[csf('color')]==0) $row[csf('color')]="";
				if($row[csf('item_color')]==0) $row[csf('item_color')]="";
				if($row[csf('item_size')]=='0') $row[csf('item_size')]="";
				$recDataArr[$row[csf('po_breakdown_id')]][trim($row[csf('item_description')])][$row[csf('item_group_id')]][$row[csf('color')]][$row[csf('item_color')]][$row[csf('item_size')]]['inhouse_qnty']+=$row[csf('quantity')];
				$amount=0; $amount=($row[csf('quantity')]*$row[csf('rate')]);
				$recDataArr[$row[csf('po_breakdown_id')]][trim($row[csf('item_description')])][$row[csf('item_group_id')]][$row[csf('color')]][$row[csf('item_color')]][$row[csf('item_size')]]['inhouse_value']+=$amount;
				$recDataArr[$row[csf('po_breakdown_id')]][trim($row[csf('item_description')])][$row[csf('item_group_id')]][$row[csf('color')]][$row[csf('item_color')]][$row[csf('item_size')]]['basis_piwono'].=$row[csf('receive_basis')].'_'.$row[csf('booking_id')].',';
			}
			unset($sqlRecData);
			//echo "<pre>";print_r($recDataArr); die;

			$transfer_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id, c.item_description, c.color, c.item_color, c.item_size, sum((case when d.trans_type=5 then d.quantity else 0 end)-(case when d.trans_type=6 then d.quantity else 0 end)) as quantity ,
			sum(case when d.trans_type=5 then d.quantity else 0 end) as in_qty,
			sum(case when d.trans_type=6 then d.quantity else 0 end) as out_qty,
			sum(case when d.trans_type=5 then (d.quantity*d.order_rate) else 0 end) as in_amount,
			sum(case when d.trans_type=6 then (d.quantity*d.order_rate) else 0 end) as out_amount
			from product_details_master c,order_wise_pro_details d
			where d.prod_id=c.id and d.trans_type in(5,6) and d.entry_form=78 and d.status_active=1 and d.is_deleted=0 $order_cond2 group by d.po_breakdown_id, c.item_group_id, c.item_description, c.color, c.item_color, c.item_size");
			foreach($transfer_qty_data as $row)
			{
				$transfe_amount=0;
				if($row[csf('item_size')]=='0') $row[csf('item_size')]="";
				$transfe_amount=$row[csf('in_amount')]-$row[csf('out_amount')];
				$transfer_data_arr[$row[csf('po_breakdown_id')]][trim($row[csf('item_description')])][$row[csf('item_group_id')]][$row[csf('color')]][$row[csf('item_color')]][$row[csf('item_size')]]['in_qty']+=$row[csf('in_qty')];
				$transfer_data_arr[$row[csf('po_breakdown_id')]][trim($row[csf('item_description')])][$row[csf('item_group_id')]][$row[csf('color')]][$row[csf('item_color')]][$row[csf('item_size')]]['out_qty']+=$row[csf('out_qty')];
				$transfer_data_arr[$row[csf('po_breakdown_id')]][trim($row[csf('item_description')])][$row[csf('item_group_id')]][$row[csf('color')]][$row[csf('item_color')]][$row[csf('item_size')]]['in_amount']+=$row[csf('in_amount')];
				$transfer_data_arr[$row[csf('po_breakdown_id')]][trim($row[csf('item_description')])][$row[csf('item_group_id')]][$row[csf('color')]][$row[csf('item_color')]][$row[csf('item_size')]]['out_amount']+=$row[csf('out_amount')];
			}
			//echo "<pre>";print_r($transfer_data_arr); 
			unset($transfer_qty_data);

			$receive_rtn_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id, d.quantity as quantity, d.order_rate as rate, c.item_description, c.color, c.item_color, c.item_size
			from product_details_master c, order_wise_pro_details d
			where d.prod_id=c.id and d.trans_type=3 and d.entry_form=49 and d.status_active=1 and d.is_deleted=0 $order_cond2");
			foreach($receive_rtn_qty_data as $row)
			{
				$receive_rtn_amount=0;
				if($row[csf('item_size')]=='0') $row[csf('item_size')]="";
				//$conv_quantity=$row[csf('quantity')]/$conversion_factor_array[$row[csf('item_group_id')]]['con_factor'];
				$conv_quantity=$row[csf('quantity')];
				$receive_rtn_amount=$conv_quantity*$row[csf('rate')];
				$rcv_rtn_data_arr[$row[csf('po_breakdown_id')]][trim($row[csf('item_description')])][$row[csf('item_group_id')]][$row[csf('color')]][$row[csf('item_color')]][$row[csf('item_size')]]['rcv_rtn_qty']+=$conv_quantity;
				$rcv_rtn_data_arr[$row[csf('po_breakdown_id')]][trim($row[csf('item_description')])][$row[csf('item_group_id')]][$row[csf('color')]][$row[csf('item_color')]][$row[csf('item_size')]]['rcv_rtn_amt']+=$receive_rtn_amount;

			}

			$sql_wo_pi=sql_select("select a.pi_number, b.work_order_no, b.work_order_dtls_id from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=4 and a.status_active=1 and a.importer_id=$company_name and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.work_order_dtls_id>0 $PIbookingConsCond");
			//echo "select a.pi_number, b.work_order_no, b.work_order_dtls_id from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=4 and a.status_active=1 and a.importer_id=$company_name and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.work_order_dtls_id>0 $PIbookingConsCond";die;
			$pi_arr=array();
			foreach($sql_wo_pi as $rowPi)
			{
				if($tem_pi[$rowPi[csf('work_order_no')]]=="")
				{
					$tem_pi[$rowPi[csf('work_order_no')]]=$rowPi[csf('pi_number')];
					$pi_arr[$rowPi[csf('work_order_no')]].=$rowPi[csf('pi_number')].'**';
				}
			}
			unset($sql_wo_pi);
		
			
			/*$sqlLc="select a.export_lc_no, b.wo_po_break_down_id from com_export_lc a, com_export_lc_order_info b where a.id=b.com_export_lc_id and a.beneficiary_name=$company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			$sqlLcData=sql_select($sqlLc);
			$lcArr=array();
			foreach($sqlLcData as $rowLc)
			{
				$lcArr[$rowLc[csf('wo_po_break_down_id')]].=$rowLc[csf('export_lc_no')].'**';
			}
			unset($sqlLcData);*/
			
			 $sqlLc="select c.lc_number, b.work_order_no from com_pi_master_details a, com_pi_item_details b, com_btb_lc_master_details c, com_btb_lc_pi d where a.id=b.pi_id and c.id=d.com_btb_lc_master_details_id and a.id=d.pi_id and a.item_category_id=4 and a.status_active=1 and a.importer_id=$company_name and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.work_order_dtls_id>0 $PIbookingConsCond   group by c.lc_number, b.work_order_no";
			//echo $sqlLc;
			$sqlLcData=sql_select($sqlLc);
			$lcArr=array();
			foreach($sqlLcData as $rowLc)
			{
				if($tem_lc[$rowLc[csf('work_order_no')]]=="")
				{
					$tem_lc[$rowLc[csf('work_order_no')]]=$rowLc[csf('lc_number')];
					$lcArr[$rowLc[csf('work_order_no')]].=$rowLc[csf('lc_number')].'**';
				}
			}
			unset($sqlLcData); 

			/*$receive_rtn_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id, d.quantity as quantity, c.order_rate as rate
			from product_details_master c,order_wise_pro_details d
			where d.prod_id=c.id and d.trans_type=3 and d.entry_form=49 and d.status_active=1 and d.is_deleted=0 $order_cond2");
			foreach($receive_rtn_qty_data as $row)
			{
				$ord_uom_qty=0; $receive_rtn_amount=0;
				//$ord_uom_qty=$row[csf('quantity')]/$conversion_factor_array[$row[csf('item_group_id')]]['con_factor'];
				$ord_uom_qty=$row[csf('quantity')];
				$receive_rtn_amount=$ord_uom_qty*$row[csf('rate')];
				$po_data_arr[$row[csf('po_breakdown_id')]]['receive_rtn_qty'][$row[csf('item_group_id')]]+=$ord_uom_qty;
				$po_data_arr[$row[csf('po_breakdown_id')]]['receive_rtn_amount'][$row[csf('item_group_id')]]+=$receive_rtn_amount;
			}
			//echo "<pre>";print_r($style_data_arr);
			unset($receive_rtn_qty_data);
			*/
			/*$transfer_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id, sum((case when d.trans_type=5 then d.quantity else 0 end)-(case when d.trans_type=6 then d.quantity else 0 end)) as quantity ,
			sum(case when d.trans_type=5 then d.quantity else 0 end) as in_qty,
			sum(case when d.trans_type=6 then d.quantity else 0 end) as out_qty,
			sum(case when d.trans_type=5 then (d.quantity*d.order_rate) else 0 end) as in_amount,
			sum(case when d.trans_type=6 then (d.quantity*d.order_rate) else 0 end) as out_amount
			from product_details_master c,order_wise_pro_details d
			where d.prod_id=c.id and d.trans_type in(5,6) and d.entry_form=78 and d.status_active=1 and d.is_deleted=0 $order_cond2 group by d.po_breakdown_id, c.item_group_id");
			foreach($transfer_qty_data as $row)
			{
				$transfe_amount=0;
				$transfe_amount=$row[csf('in_amount')]-$row[csf('out_amount')];

				$po_data_arr[$row[csf('po_breakdown_id')]]['transfe_qty'][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				$po_data_arr[$row[csf('po_breakdown_id')]]['transfe_in'][$row[csf('item_group_id')]]+=$row[csf('in_qty')];
				$po_data_arr[$row[csf('po_breakdown_id')]]['transfe_out'][$row[csf('item_group_id')]]+=$row[csf('out_qty')];
				$po_data_arr[$row[csf('po_breakdown_id')]]['transfe_in_amount'][$row[csf('item_group_id')]]+=$row[csf('in_amount')];
				$po_data_arr[$row[csf('po_breakdown_id')]]['transfe_out_amount'][$row[csf('item_group_id')]]+=$row[csf('out_amount')];
				$po_data_arr[$row[csf('po_breakdown_id')]]['transfe_amount'][$row[csf('item_group_id')]]+=$transfe_amount;
			}
			unset($transfer_qty_data);*/

			$sqlIssue="select b.po_breakdown_id, p.item_group_id, (b.quantity) as quantity, (b.quantity*b.order_rate) as issue_amount, p.color, p.item_color, p.item_size, p.item_description
			from inv_trims_issue_dtls a, order_wise_pro_details b, inv_issue_master d, product_details_master p
			where a.mst_id=d.id and a.prod_id=p.id and a.trans_id=b.trans_id and b.trans_type=2 and d.entry_form=25 and b.entry_form=25 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_cond1 $trm_group_iss_cond ";
			$sqlIssueData=sql_select($sqlIssue); $issueDataArr=array();
			//echo $issue_qty_data; die;
			foreach($sqlIssueData as $row)
			{
				if($row[csf('color')]==0) $row[csf('color')]="";
				if($row[csf('item_color')]==0) $row[csf('item_color')]="";
				if($row[csf('item_size')]=='0') $row[csf('item_size')]="";
				$issueDataArr[$row[csf('po_breakdown_id')]][trim($row[csf('item_description')])][$row[csf('item_group_id')]][$row[csf('color')]][$row[csf('item_color')]][$row[csf('item_size')]]['issue_qty']+=$row[csf('quantity')];
				$issueDataArr[$row[csf('po_breakdown_id')]][trim($row[csf('item_description')])][$row[csf('item_group_id')]][$row[csf('color')]][$row[csf('item_color')]][$row[csf('item_size')]]['issue_amount']+=$row[csf('issue_amount')];
			}
			//echo "<pre>";print_r($issueDataArr); 
			unset($sqlIssueData);
			/*echo "select d.po_breakdown_id, c.item_group_id, c.color, c.item_color, c.item_size,c.item_description, d.quantity as quantity, (d.quantity*c.avg_rate_per_unit) as amount
				from product_details_master c, order_wise_pro_details d
				where d.prod_id=c.id and d.trans_type=4 and d.entry_form=73 and d.status_active=1 and d.is_deleted=0 $order_cond2";*/
			$issue_rtn_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id, c.color, c.item_color, c.item_size,c.item_description, d.quantity as quantity, (d.quantity*d.order_rate) as amount
				from product_details_master c, order_wise_pro_details d
				where d.prod_id=c.id and d.trans_type=4 and d.entry_form=73 and d.status_active=1 and d.is_deleted=0 $order_cond2");
			foreach($issue_rtn_qty_data as $row)
			{
				if($row[csf('color')]==0) $row[csf('color')]="";
				if($row[csf('item_color')]==0) $row[csf('item_color')]="";
				if($row[csf('item_size')]=='0') $row[csf('item_size')]="";
				$issueRtnDataArr[$row[csf('po_breakdown_id')]][trim($row[csf('item_description')])][$row[csf('item_group_id')]][$row[csf('color')]][$row[csf('item_color')]][$row[csf('item_size')]]['issue_rtn_qty']+=$row[csf('quantity')];
				$issueRtnDataArr[$row[csf('po_breakdown_id')]][trim($row[csf('item_description')])][$row[csf('item_group_id')]][$row[csf('color')]][$row[csf('item_color')]][$row[csf('item_size')]]['issue_rtn_amt']+=$row[csf('amount')];
			}

			unset($issue_rtn_qty_data);
 

			/*$issue_rtn_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id, d.quantity as quantity, (d.quantity*d.order_rate) as amount
			from product_details_master c, order_wise_pro_details d
			where d.prod_id=c.id and d.trans_type=4 and d.entry_form=73 and d.status_active=1 and d.is_deleted=0 $order_cond2");
			foreach($issue_rtn_qty_data as $row)
			{
				$po_data_arr[$row[csf('po_breakdown_id')]]['issue_rtn_qty'][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				$po_data_arr[$row[csf('po_breakdown_id')]]['issue_rtn_amount'][$row[csf('item_group_id')]]+=$row[csf('amount')];
			}
			unset($issue_rtn_qty_data);*/
			//	die;
			$rowspanpoArr=array(); $rowspanpoBomArr=array(); $rowspanpoColorArr=array(); $rowspanpoColorSizeArr=array();
			foreach($po_data_arr as $poid=>$podata)
			{
				$i=1;
				foreach($podata as $bomid=>$bomData)
				{
					$j=1;
					if(!empty($bookingConsArr[$poid][$bomid]))
					{
						foreach($bookingConsArr[$poid][$bomid] as $gmtscolorid=>$gmtscolorData)
						{
							$k=1;
							foreach($gmtscolorData as $itemcolorid=>$itemcolorData)
							{
								$n=1;
								foreach($itemcolorData as $size=>$sizeData)
								{
									$rowspanpoArr[$poid]=$i;
									$rowspanpoBomArr[$poid][$bomid]=$j;
									$rowspanpoColorArr[$poid][$bomid][$gmtscolorid]=$k;
									$rowspanpoColorSizeArr[$poid][$bomid][$gmtscolorid][$size]=$n;
									$i++;
									$j++;
									$k++;
									$n++;
								}
							}
						}
					}
					else
					{
						$rowspanpoArr[$poid]=$i;
						$rowspanpoBomArr[$poid][$bomid]=$j;
						$rowspanpoColorArr[$poid][$bomid]['']=$k;
						$rowspanpoColorSizeArr[$poid][$bomid]['']['']=$n;
						$i++;
						$j++;
						$k++;
						$n++;
					}
				}
			}
			
			/*print_r($rowspanpoArr).'<br>';
			print_r($rowspanpoColorArr).'<br>';
			print_r($rowspanpoColorArr).'<br>'; die;*/
			$inhouse_or_rec_blance=0;

			$total_pre_costing_value=0; $total_wo_value=0; $total_left_over_balanc=0; $total_issue_amount=0; $total_rec_bal_qnty=0;

			$summary_array=array();
			$q=1; $a=1;
			foreach($po_data_arr as $poid=>$podata)
			{
				$i=1; $rowspan=$rowspanpoArr[$poid];
				foreach($podata as $bomtrimid=>$bomData)
				{
					$j=1; $rowspanpobom=0; $rowspanpobom=$rowspanpoBomArr[$poid][$bomtrimid];
					if(!empty($bookingConsArr[$poid][$bomtrimid]))
					{
						foreach($bookingConsArr[$poid][$bomtrimid] as $gmtscolorid=>$gmtscolorData)
						{
							$k=1; $rowspanpoBomColor=0; $rowspanpoBomColor=$rowspanpoColorArr[$poid][$bomtrimid][$gmtscolorid]; //echo $rowspanposolor.'<br>';
							foreach($gmtscolorData as $itemcolorid=>$itemcolorData)
							{
								$n=1;
								foreach($itemcolorData as $size=>$sizeData)
								{
									if($q%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
                                    <tr bgcolor="<?=$bgcolor;?>" onclick="change_color('tr_<?=$q; ?>','<?=$bgcolor;?>')" id="tr_<?=$q; ?>">
										<?
                                        if($i==1)
                                        {
                                            ?>
                                            <td width="30" rowspan="<?=$rowspan; ?>" align="center" valign="middle"><?=$a; ?></td>
                                            <td width="60" rowspan="<?=$rowspan; ?>" style="word-break:break-all" valign="middle"><?=$buyer_short_name_library[$po_arr[$poid]['buyer']]; ?></td>
                                            <td width="60" rowspan="<?=$rowspan; ?>" style="word-break:break-all" valign="middle"><?=$buyer_short_name_library[$po_arr[$poid]['client']]; ?></td>
                                            <td width="90" rowspan="<?=$rowspan; ?>" style="word-break:break-all" valign="middle"><p><?=$po_arr[$poid]['job_no']; ?></p></td>
                                            
                                            <td width="100" rowspan="<?=$rowspan; ?>" style="word-break: break-all;" valign="middle"><p><?=$po_arr[$poid]['style_ref']; ?></p></td>
                                            <td width="100" rowspan="<?=$rowspan; ?>" style="word-break: break-all;" valign="middle"><a href='#report_details' onclick="generate_report('<?=$company_name; ?>','<?=$po_arr[$poid]['job_no']; ?>','<?=$po_arr[$poid]['buyer']; ?>','<?=$po_arr[$poid]['style_ref']; ?>','<?=change_date_format($budget_arr[$poid]['costing_date']); ?>','<?=$poid; ?>','<?=$budget_arr[$poid]['costing_per']; ?>','preCostRpt');"><?=$po_arr[$poid]['po_number']; ?></a></td>
                                            <td width="80" rowspan="<?=$rowspan; ?>" align="right" style="word-break: break-all;" valign="middle"><a href='#report_details' onclick="order_qty_popup('<?=$company_name; ?>','<?=$po_arr[$poid]['job_no']; ?>','<?=$poid; ?>','<?=$po_arr[$poid]['buyer']; ?>',<?=$txt_date_from; ?>,<?=$txt_date_to; ?> ,'order_qty_data');"><?=number_format($po_arr[$poid]['order_quantity_set'],0,'.',''); ?></a></td>
                    
                                            <td width="50" rowspan="<?=$rowspan; ?>" style="word-break: break-all;" align="center" valign="middle"><?=$unit_of_measurement[$po_arr[$poid]['order_uom']]; ?></td>
                                            <td width="80" rowspan="<?=$rowspan; ?>" style="word-break: break-all;" align="right" valign="middle"><?=number_format($po_arr[$poid]['order_quantity'],0,'.',''); ?></td>
                                            <?
											$gPoQty+=$po_arr[$poid]['order_quantity_set'];
											$gPcsPoQty+=$po_arr[$poid]['order_quantity'];
                                            $a++;
                                        }
                                        
                                        if($j==1)
                                        {
                                            ?>
                                            <td width="100" rowspan="<?=$rowspanpobom; ?>" title="<?="BOM-".$bomtrimid.'-'.$bomData['trim_group']; ?>" style="word-break: break-all;" valign="middle"><?=$item_library[$bomData['trim_group']]; ?></td>
                                            <td width="140" rowspan="<?=$rowspanpobom; ?>" style="word-break: break-all;" valign="middle"><p><?=$bomData['description']; ?></p></td>
                                            <td width="50" rowspan="<?=$rowspanpobom; ?>" style="word-break: break-all;" valign="middle"><?=$unit_of_measurement[$bomData['cons_uom']]; ?></td>
                                            <td width="70" rowspan="<?=$rowspanpobom; ?>" style="word-break: break-all;" align="right" valign="middle"><?=$bomData['avg_cons']; ?></td>
                                            <td width="100" rowspan="<?=$rowspanpobom; ?>" style="word-break: break-all;" align="right" valign="middle"><?=number_format($bomData['req_qnty'],2); ?></td>
                                            <td width="100" rowspan="<?=$rowspanpobom; ?>" style="word-break: break-all;" align="right" valign="middle" title="<?=$bomData['req_qnty'].'-'.$bomData['wo_qnty']; ?>"><?=number_format($bomData['req_value'],2); ?></td>
                                            <?
											$gReqQty+=$bomData['req_qnty'];
											$gReqVal+=$bomData['req_value'];
                                        }
										
										$bookingReq=$bomReqQty=0; $tdColor="";
										$bomReqQty=number_format($bomData['req_qnty'],2,'.','');
										$bookingReq=number_format($bomData['wo_qnty'],2,'.','');
										
										if($bomReqQty==$bookingReq) $tdColor="Green";
										else if($bomReqQty<$bookingReq) $tdColor="RED";
										else if($bomReqQty>$bookingReq) $tdColor="Yellow";
                                        
                                        if($k==1)
                                        {
                                            ?>
                                            <td width="100" rowspan="<?=$rowspanpoBomColor; ?>" style="word-break: break-all;" valign="middle"><?=$colorArr[$gmtscolorid]; ?></td>
                                            <td width="100" rowspan="<?=$rowspanpoBomColor; ?>" style="word-break: break-all;" valign="middle"><?=$colorArr[$itemcolorid]; ?></td>
                                            <?
                                        }
                                        $rcvQty=$netIssueQty=$netIssueAmt=$inHouseQty=$inHouseAmt=$netInHouseAmt=$issueQty=$issueAmt=$leftOverQty=$leftOverAmt=$transferINHouseQty=$rcv_rtn_qty=$issueRtnAmt=0;
										if($bomData['description']=="") $bomData['description']=0;
										$inHousekey=$poid.'_'.trim($bomData['description']).'_'.$bomData['trim_group'].'_'.$gmtscolorid.'_'.$itemcolorid.'_'.$size;
                                        $rcvQty=$recDataArr[$poid][trim($bomData['description'])][$bomData['trim_group']][$gmtscolorid][$itemcolorid][$size]['inhouse_qnty'];
                                        $netInHouseAmt=$recDataArr[$poid][trim($bomData['description'])][$bomData['trim_group']][$gmtscolorid][$itemcolorid][$size]['inhouse_value'];
                                        $netIssueQty=$issueDataArr[$poid][trim($bomData['description'])][$bomData['trim_group']][$gmtscolorid][$itemcolorid][$size]['issue_qty'];
                                        $netIssueAmt=$issueDataArr[$poid][trim($bomData['description'])][$bomData['trim_group']][$gmtscolorid][$itemcolorid][$size]['issue_amount'];


                                        $issueRtnQty=$issueRtnDataArr[$poid][trim($bomData['description'])][$bomData['trim_group']][$gmtscolorid][$itemcolorid][$size]['issue_rtn_qty'];
                                        $issueRtnAmt=$issueRtnDataArr[$poid][trim($bomData['description'])][$bomData['trim_group']][$gmtscolorid][$itemcolorid][$size]['issue_rtn_amt'];

                                        $transferINHouseQty=$transfer_data_arr[$poid][trim($bomData['description'])][$bomData['trim_group']][$gmtscolorid][$itemcolorid][$size]['in_qty'];
                                        $transferOutHouseQty=$transfer_data_arr[$poid][trim($bomData['description'])][$bomData['trim_group']][$gmtscolorid][$itemcolorid][$size]['out_qty'];

                                        $transferINHouseAmt=$transfer_data_arr[$poid][trim($bomData['description'])][$bomData['trim_group']][$gmtscolorid][$itemcolorid][$size]['in_amount'];
                                        $transferOutHouseAmt=$transfer_data_arr[$poid][trim($bomData['description'])][$bomData['trim_group']][$gmtscolorid][$itemcolorid][$size]['out_amount']; 

                                        $rcv_rtn_qty=$rcv_rtn_data_arr[$poid][trim($bomData['description'])][$bomData['trim_group']][$gmtscolorid][$itemcolorid][$size]['rcv_rtn_qty'];
                                        $rcv_rtn_amt=$rcv_rtn_data_arr[$poid][trim($bomData['description'])][$bomData['trim_group']][$gmtscolorid][$itemcolorid][$size]['rcv_rtn_amt'];
                                        //echo $inHouseQty.'=='.$transferINHouseQty.'=='.$transferOutHouseQty.'=='.$rcv_rtn_qty.'++';
                                        //echo $poid.'_'.trim($bomData['description']).'_'.$bomData['trim_group'].'_'.$gmtscolorid.'_'.$itemcolorid.'_'.$size.'++';
                                        $inHouseQty=($rcvQty+$transferINHouseQty)-$transferOutHouseQty-$rcv_rtn_qty;
                                        $inHouseAmt=($netInHouseAmt+$transferINHouseAmt)-$transferOutHouseAmt-$rcv_rtn_amt;
                                        $issueQty=$netIssueQty-$issueRtnQty;

                                        //echo $netIssueAmt.'=='.$issueRtnAmt.'++';
                                        $issueAmt=$netIssueAmt-$issueRtnAmt;
                                        
                                        $leftOverQty=$inHouseQty-$issueQty;
                                        $leftOverAmt=$inHouseAmt-$issueAmt;
										//echo $poid.'='.$bomData['trim_group'].'='.$gmtscolorid.'='.$itemcolorid.'='.$size.'<br>';
										$supplier_id=rtrim($bomData['supplier_id'],',');
                                        $supplier_name_string="";
                                        $supplier_id_arr=array_unique(explode(',',$supplier_id));
                                        foreach($supplier_id_arr as $supplier_id_arr_key=>$supplier_id_arr_value)
                                        {
                                            $ex_sup_data=explode("**",$supplier_id_arr_value);
                                            if($ex_sup_data[1]==3 || $ex_sup_data[1]==5) $suplier_name_arr=$company_library; else $suplier_name_arr=$lib_supplier_arr;
                                            $supplier_name_string.=$suplier_name_arr[$ex_sup_data[0]].",";
                                        }
                                        $booking_no=rtrim($bomData['booking_no'],',');
                                        $booking_no_arr=array_unique(explode(',',$booking_no)); 
                                        $main_booking_no_large_data=""; $piWoNo=''; $lcNo="";
                                        foreach($booking_no_arr as $booking_no1)
                                        {
                                            $piWoNo.=chop($pi_arr[$booking_no1],"**").",";
											$lcNo.=chop($lcArr[$booking_no1],"**").",";
                                        }
										$piWoNo=implode(",",array_filter(array_unique(explode(",",$piWoNo))));
										$lcNo=implode(",",array_filter(array_unique(explode(",",$lcNo))));
										
				
										
                                        ?>
                                        <td width="80" style="word-break: break-all;"><?=$size; ?></td>
                                        <td width="80" style="word-break: break-all;" align="right" title="<?='BOM Req.='.$bomReqQty.'; Booking Req='.$bookingReq; ?>" bgcolor="<?=$tdColor; ?>"><?=number_format($sizeData['qty'],2); ?></td>
                                        <td width="90" style="word-break: break-all;" align="right"><?=number_format($sizeData['amt'],2); ?></td>
                                        
                                        <td width="80" style="word-break: break-all;" align="right"><a href='#report_details' onclick="openmypage_inhouse('<? echo $inHousekey; ?>','booking_inhouse_info');"><? echo number_format($inHouseQty,2,'.',''); ?></a></td>
                                        <td width="90" style="word-break: break-all;" align="right"><?=number_format($inHouseAmt,2); ?></td>

                                        <?php $inhouse_or_rec_blance+=$sizeData['qty']-$inHouseQty; ?>

                                        <td width="90" style="word-break: break-all;" align="right"><?=number_format($sizeData['qty']-$inHouseQty,2); ?></td>
                                        
                                        <td width="80" style="word-break: break-all;" align="right"><a href='#report_details' onclick="openmypage_issue('<? echo $inHousekey; ?>','booking_issue_info');"><? echo number_format($issueQty,2,'.',''); ?></a></td>
                                        <td width="90" style="word-break: break-all;" align="right"><?=number_format($issueAmt,2); ?></td>
                                        <td width="80" style="word-break: break-all;" align="right"><?=number_format($leftOverQty,2); ?></td>
                                        
                                        <td width="90" style="word-break: break-all;" align="right"><?=number_format($leftOverAmt,2); ?></td>
                                        <td width="140" style="word-break: break-all;"><p><?=chop($supplier_name_string,","); ?></p></td>
                                        <td width="120" style="word-break: break-all;"><p><?=$piWoNo; ?></p></td>
                                        <td style="word-break: break-all;"><p><?=$lcNo; ?>&nbsp;</p></td>
                                    </tr>
                                    <?
									$gWoQty+=$sizeData['qty'];
									$gWoAmt+=$sizeData['amt'];
									$gInHouseQty+=$inHouseQty;
									$gInHouseAmt+=$inHouseAmt;
									$gIssueQty+=$issueQty;
									$gIssueAmt+=$issueAmt;
									$gLeftOverQty+=$leftOverQty;
									$gLftOverAmt+=$leftOverAmt;
									
									$i++;
									$j++;
									$k++;
									$n++;
									$q++;
								}
							}
						}
					}
					else
					{
						if($q%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$rowspanpoBomColor=1;
						?>
						<tr bgcolor="<?=$bgcolor;?>" onclick="change_color('tr_<?=$q; ?>','<?=$bgcolor;?>')" id="tr_<?=$q; ?>">
							<?
                            if($i==1)
                            {
                                ?>
                                <td width="30" rowspan="<?=$rowspan; ?>" align="center" valign="middle"><?=$a; ?></td>
                                <td width="60" rowspan="<?=$rowspan; ?>" style="word-break:break-all" valign="middle"><?=$buyer_short_name_library[$po_arr[$poid]['buyer']]; ?></td>
                                <td width="60" rowspan="<?=$rowspan; ?>" style="word-break:break-all" valign="middle"><?=$buyer_short_name_library[$po_arr[$poid]['client']]; ?></td>
                                <td width="90" rowspan="<?=$rowspan; ?>" style="word-break:break-all" valign="middle"><p><?=$po_arr[$poid]['job_no']; ?></p></td>
                                
                                <td width="100" rowspan="<?=$rowspan; ?>" style="word-break: break-all;" valign="middle"><p><?=$po_arr[$poid]['style_ref']; ?></p></td>
                                <td width="100" rowspan="<?=$rowspan; ?>" style="word-break: break-all;" valign="middle"><a href='#report_details' onclick="generate_report('<?=$company_name; ?>','<?=$po_arr[$poid]['job_no']; ?>','<?=$po_arr[$poid]['buyer']; ?>','<?=$po_arr[$poid]['style_ref']; ?>','<?=change_date_format($budget_arr[$poid]['costing_date']); ?>','<?=$poid; ?>','<?=$budget_arr[$poid]['costing_per']; ?>','preCostRpt');"><?=$po_arr[$poid]['po_number']; ?></a></td>
                                <td width="80" rowspan="<?=$rowspan; ?>" align="right" style="word-break: break-all;" valign="middle"><a href='#report_details' onclick="order_qty_popup('<?=$company_name; ?>','<?=$po_arr[$poid]['job_no']; ?>','<?=$poid; ?>','<?=$po_arr[$poid]['buyer']; ?>',<?=$txt_date_from; ?>,<?=$txt_date_to; ?> ,'order_qty_data');"><?=number_format($po_arr[$poid]['order_quantity_set'],0,'.',''); ?></a></td>
        
                                <td width="50" rowspan="<?=$rowspan; ?>" style="word-break: break-all;" align="center" valign="middle"><?=$unit_of_measurement[$po_arr[$poid]['order_uom']]; ?></td>
                                <td width="80" rowspan="<?=$rowspan; ?>" style="word-break: break-all;" align="right" valign="middle"><?=number_format($po_arr[$poid]['order_quantity'],0,'.',''); ?></td>
                                <?
								$gPoQty+=$po_arr[$poid]['order_quantity_set'];
								$gPcsPoQty+=$po_arr[$poid]['order_quantity'];
                                $a++;
                            }
                            if($j==1)
                            {
                                ?>
                                <td width="100" rowspan="<?=$rowspanpobom; ?>" title="<?="BOM-".$bomtrimid; ?>" style="word-break: break-all;" valign="middle"><?=$item_library[$bomData['trim_group']]; ?></td>
                                <td width="140" rowspan="<?=$rowspanpobom; ?>" style="word-break: break-all;" valign="middle"><?=$bomData['description']; ?></td>
                                <td width="50" rowspan="<?=$rowspanpobom; ?>" style="word-break: break-all;" valign="middle"><?=$unit_of_measurement[$bomData['cons_uom']]; ?></td>
                                <td width="70" rowspan="<?=$rowspanpobom; ?>" style="word-break: break-all;" align="right" valign="middle"><?=$bomData['avg_cons']; ?></td>
                                <td width="100" rowspan="<?=$rowspanpobom; ?>" style="word-break: break-all;" align="right" valign="middle"><?=number_format($bomData['req_qnty'],2); ?></td>
                                <td width="100" rowspan="<?=$rowspanpobom; ?>" style="word-break: break-all;" align="right" valign="middle" title="<?=$bomData['req_qnty'].'-'.$bomData['wo_qnty']; ?>"><?=number_format($bomData['req_value'],2); ?></td>
                                <?
								$gReqQty+=$bomData['req_qnty'];
								$gReqVal+=$bomData['req_value'];
                            }
							
							
							$bookingReq=$bomReqQty=0; $tdColor="";
							$bomReqQty=number_format($bomData['req_qnty'],2,'.','');
							$bookingReq=number_format($bomData['wo_qnty'],2,'.','');
							
							if($bomReqQty==$bookingReq) $tdColor="Green";
							else if($bomReqQty<$bookingReq) $tdColor="RED";
							else if($bomReqQty>$bookingReq) $tdColor="Yellow";
							
                            $inHouseQty=$inHouseAmt=$issueQty=$issueAmt=$leftOverQty=$leftOverAmt=0;
                            $inHouseQty=$recDataArr[$poid][trim($bomData['description'])][$bomData['trim_group']]['']['']['']['inhouse_qnty'];
                            $inHouseAmt=$recDataArr[$poid][trim($bomData['description'])][$bomData['trim_group']]['']['']['']['inhouse_value'];
                            $issueQty=$issueDataArr[$poid][trim($bomData['description'])][$bomData['trim_group']]['']['']['']['issue_qty'];
                            $issueAmt=$issueDataArr[$poid][trim($bomData['description'])][$bomData['trim_group']]['']['']['']['issue_amount'];
                            $leftOverQty=$inHouseQty-$issueQty;
                            $leftOverAmt=$inHouseAmt-$issueAmt;
                            $supplier_id=rtrim($bomData['supplier_id'],',');
                            $supplier_name_string="";
                            $supplier_id_arr=array_unique(explode(',',$supplier_id));
                            foreach($supplier_id_arr as $supplier_id_arr_key=>$supplier_id_arr_value)
                            {
                                $ex_sup_data=explode("**",$supplier_id_arr_value);
                                if($ex_sup_data[1]==3 || $ex_sup_data[1]==5) $suplier_name_arr=$company_library; else $suplier_name_arr=$lib_supplier_arr;
                                $supplier_name_string.=$suplier_name_arr[$ex_sup_data[0]].",";
                            }
                             $booking_no=rtrim($bomData['booking_no'],',');
                            $booking_no_arr=array_unique(explode(',',$booking_no));
                            $main_booking_no_large_data=""; $piWoNo=''; $lcNo="";
                            foreach($booking_no_arr as $booking_no1)
                            {
                                $piWoNo.=chop($pi_arr[$booking_no1],"**").",";
								$lcNo.=chop($lcArr[$booking_no1],"**").",";
                            }
							$piWoNo=implode(",",array_filter(array_unique(explode(",",$piWoNo))));
							$lcNo=implode(",",array_filter(array_unique(explode(",",$lcNo))));
							$color_number_id=implode(",",array_unique(explode(",",$bomData['color_number_id'])));
							$item_color_number_id=implode(",",array_unique(explode(",",$bomData['item_color_number_id'])));
							$item_size=implode(",",array_unique(explode(",",$bomData['item_size'])));
                            ?>
                            <td width="100" style="word-break: break-all;"><?=$color_number_id;?>&nbsp;</td>
                            <td width="100" style="word-break: break-all;"><?=$item_color_number_id?>&nbsp;</td>
                            <td width="80" style="word-break: break-all;"><?=$item_size?>&nbsp;</td>
                            
                            <td width="80" style="word-break: break-all;" align="right" title="<?='BOM Req.='.$bomReqQty.'; Booking Req='.$bookingReq; ?>" bgcolor="<?=$tdColor; ?>"><?=number_format($bomData['wo_qnty'],2); ?></td>
                            <td width="90" style="word-break: break-all;" align="right"><?=number_format($bomData['amount'],2); ?></td>
                                
                            <td width="80" style="word-break: break-all;" align="right"><?=number_format($inHouseQty,2); ?></td>
                            <td width="90" style="word-break: break-all;" align="right"><?=number_format($inHouseAmt,2); ?></td>

                             <?php $inhouse_or_rec_blance+=$bomData['wo_qnty']-$inHouseQty; ?>

                                        <td width="90" style="word-break: break-all;" align="right"><?=number_format($sizeData['qty']-$inHouseQty,2); ?></td>

                            <td width="80" style="word-break: break-all;" align="right"><?=number_format($issueQty,2); ?></td>
                            <td width="90" style="word-break: break-all;" align="right"><?=number_format($issueAmt,2); ?></td>
                            <td width="80" style="word-break: break-all;" align="right"><?=number_format($leftOverQty,2); ?></td>
                            
                            <td width="90" style="word-break: break-all;" align="right"><?=number_format($leftOverAmt,2); ?></td>
                            <td width="140" style="word-break: break-all;"><p><?=chop($supplier_name_string,","); ?></p></td>
                            <td width="120" style="word-break: break-all;"><p><?=$piWoNo; ?></p></td>
                            <td style="word-break: break-all;"><p><?=$lcNo; ?>&nbsp;</p></td>
                        </tr>
                        <?
						$gWoQty+=$bomData['wo_qnty'];
						$gWoAmt+=$bomData['amount'];
						$gInHouseQty+=$inHouseQty;
						$gInHouseAmt+=$inHouseAmt;
						$gIssueQty+=$issueQty;
						$gIssueAmt+=$issueAmt;
						$gLeftOverQty+=$leftOverQty;
						$gLftOverAmt+=$leftOverAmt;
									
						$i++;
						$j++;
						$k++;
						$n++;
						$q++;
					}
				}
			}
			unset($po_data_arr);
			unset($bookingConsArr);
			?>
            </table>
         </div>
        <table class="rpt_table" width="2650" cellpadding="0" cellspacing="0" border="1" rules="all">
            <tfoot>
                <th width="30">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th width="90">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="100">Total:</th>
                <th width="80" style="word-break: break-all;"><?=number_format($gPoQty,2); ?></td>
                <th width="50">&nbsp;</th>
                <th width="80" style="word-break: break-all;"><?=number_format($gPcsPoQty,2); ?></td>
                <th width="100">&nbsp;</th>
                <th width="140">&nbsp;</th>
                <th width="50">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="100" style="word-break: break-all;"><?=number_format($gReqQty,2); ?></td>
                <th width="100" style="word-break: break-all;"><?=number_format($gReqVal,2); ?></td>
                <th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80" style="word-break: break-all;"><?=number_format($gWoQty,2); ?></td>
                <th width="90" style="word-break: break-all;"><?=number_format($gWoAmt,2); ?></td>
                <th width="80" style="word-break: break-all;"><?=number_format($gInHouseQty,2); ?></td>
                <th width="90" style="word-break: break-all;"><?=number_format($gInHouseAmt,2); ?></td>
                <th width="90" style="word-break: break-all;"><?=number_format($inhouse_or_rec_blance,2); ?></td>
                <th width="80" style="word-break: break-all;"><?=number_format($gIssueQty,2); ?></td>
                <th width="90" style="word-break: break-all;"><?=number_format($gIssueAmt,2); ?></td>
                <th width="80" style="word-break: break-all;"><?=number_format($gLeftOverQty,2); ?></td>
                
                <th width="90" style="word-break: break-all;"><?=number_format($gLftOverAmt,2); ?></td>
                <th width="140">&nbsp;</th>
                <th width="120">&nbsp;</th>
                <th>&nbsp;</th>
            </tfoot>
        </table>
        <?
        }
	}
	else if(str_replace("'","",$cbo_search_by)==2) //Style Wise
	{
		if($template==1)
		{
			ob_start();
			?>
			<div style="width:2650px">
			<table width="2650">
                <tr class="form_caption"><td colspan="29" align="center"><?=$report_title; ?></td></tr>
                <tr class="form_caption"><td colspan="29" align="center"><?=$company_library[$company_name]; ?></td></tr>
			</table>
			<table style="margin-left:1050px; margin-top:5px" id="table_notes">
				<tr align="center">
                    <td bgcolor="yellow" height="15" width="30"></td>
                    <td>WO Qty Fully or Partial Pending with Req Qty</td>
                    <td bgcolor="green" height="15" width="30">&nbsp;</td>
                    <td>WO Qty equal with Req Qty </td>
                    <td bgcolor="red" height="15" width="30"></td>
                    <td>WO Qty greater than Req Qty</td>
				</tr>
				<tr>
                    <td colspan="6" align="center">
                        (All WO Qty will calculate with Conversion Factor)
                    </td>
				</tr>
			</table>

			<table class="rpt_table" width="2650" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <th width="30">SL</th>
                    <th width="60">Buyer</th>
                    <th width="60">Client</th>
                    <th width="90">Job No</th>
                    <th width="100">Style Ref</th>
                    <th width="100">Order No</th>
                    <th width="80">Order Qty</th>
                    <th width="50">UOM</th>
                    <th width="80">Qty (Pcs)</th>
                    <th width="100">Trims Name</th>
                    <th width="140">Item Description</th>
                    <th width="50">Trims UOM</th>
                    <th width="70">Avg. Cons</th>
                    <th width="100">Req. Qty</th>
                    <th width="100">BOM Value (USD)</th>
                    <th width="100">GMTS Color</th>
                    <th width="100">Item Color</th>
                    <th width="80">Item Size</th>
                    <th width="80">WO Qty</th>
                    <th width="90">WO Value (USD)</th>
                    <th width="80">In-House Qty</th>
                    <th width="90">In-House Value [USD]</th>
                    <th width="90">In-House/Receive Blance</th>
                    <th width="80">Issue to Prod.</th>
                    <th width="90">Issue Value[USD]</th>
                    <th width="80">Left Over/ Balance (Qty)</th>
                    
                    <th width="90">Left Over Value (USD)</th>
                    <th width="140">Supplier</th>
                    <th width="120">PI No</th>
                    <th>LC NO</th>
                </thead>
			</table>
			<div style="width:2650px; max-height:400px; overflow-y:scroll" id="scroll_body">
			<table class="rpt_table" width="2630" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
			<?
			
			$conversion_factor_array=array();$item_arr=array();
			$conversion_factor=sql_select("select id,trim_uom,order_uom,conversion_factor from lib_item_group where status_active=1");
			foreach($conversion_factor as $row_f)
			{
				$conversion_factor_array[$row_f[csf('id')]]['con_factor']=$row_f[csf('conversion_factor')];
				$conversion_factor_array[$row_f[csf('id')]]['cons_uom']=$row_f[csf('trim_uom')];
				$item_arr[$row_f[csf('id')]]['order_uom']=$row_f[csf('order_uom')];
			}
			unset($conversion_factor);

			/*$sql_po_qty_country_wise_arr=array();
			$po_job_arr=array();
			$sql_po_country_data=sql_select("select  b.id,b.job_no_mst,c.country_id, sum(c.order_quantity/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst  and b.id=c.po_break_down_id   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond group by   b.id,b.job_no_mst,c.country_id order by b.id,b.job_no_mst,c.country_id");
			foreach( $sql_po_country_data as $sql_po_country_row)
			{
				$sql_po_qty_country_wise_arr[$sql_po_country_row[csf('id')]][$sql_po_country_row[csf('country_id')]]=$sql_po_country_row[csf('order_quantity_set')];
				$po_job_arr[$sql_po_country_row[csf('id')]]=$sql_po_country_row[csf('job_no_mst')];
			}
			unset($sql_po_country_data);*/

			$po_data_arr=array();
			$po_id_string="";
			$today=date("Y-m-d");

			$sql_pos=("select a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.order_uom, a.client_id, b.id, b.po_number, (c.order_quantity) as order_quantity, (c.order_quantity/a.total_set_qnty) as order_quantity_set
			from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
			where
			a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.company_name=$company_name $buyer_id_cond ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $style_ref_cond $jobcond $year_cond order by b.id ASC");//and a.job_no='UG-20-00182'
			//echo $sql_pos; die;
			$sql_po=sql_select($sql_pos);
			$job_arr=array(); $tot_rows=0;
			foreach($sql_po as $row)
			{
				$job_arr[$row[csf('job_no')]]['buyer']=$row[csf('buyer_name')];
				$job_arr[$row[csf('job_no')]]['client']=$row[csf('client_id')];
				$job_arr[$row[csf('job_no')]]['poid'].=$row[csf('id')].',';
				$job_arr[$row[csf('job_no')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
				$job_arr[$row[csf('job_no')]]['style_ref']=$row[csf('style_ref_no')];
				$job_arr[$row[csf('job_no')]]['order_uom']=$row[csf('order_uom')];
				$job_arr[$row[csf('job_no')]]['po_number'].=$row[csf('po_number')].',';
				$job_arr[$row[csf('job_no')]]['order_quantity']+=$row[csf('order_quantity')];
				$job_arr[$row[csf('job_no')]]['order_quantity_set']+=$row[csf('order_quantity_set')];
				$po_id_string.=$row[csf('id')].",";
			}
			unset($sql_po);
			$expoid=array_filter(array_unique(explode(",",$po_id_string)));
			$tot_rows=count($expoid);
			$poIds=implode(",",$expoid);
			if($poIds=="")
			{
				echo "<strong style='color:red;font-size:30px'>No Data found</strong>";
				die;
			}

			$order_cond=""; $order_cond1=""; $order_cond2=""; $precost_po_cond="";
			/*if($db_type==2 && $tot_rows>1000)
			{
				$order_cond=" and (";
				$order_cond1=" and (";
				$order_cond2=" and (";
				$precost_po_cond=" and (";
				$poIdsArr=array_chunk(explode(",",$poIds),999);
				foreach($poIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					//$poIds_cond.=" po_break_down_id in($ids) or ";
					$order_cond.=" b.po_break_down_id in($ids) or";
					$order_cond1.=" b.po_breakdown_id in($ids) or";
					$order_cond2.=" d.po_breakdown_id in($ids) or";
					$precost_po_cond.=" c.po_break_down_id in($ids) or";
				}
				$order_cond=chop($order_cond,'or ');
				$order_cond.=")";
				$order_cond1=chop($order_cond1,'or ');
				$order_cond1.=")";
				$order_cond2=chop($order_cond2,'or ');
				$order_cond2.=")";
				$precost_po_cond=chop($precost_po_cond,'or ');
				$precost_po_cond.=")";
			}
			else
			{
				$order_cond=" and b.po_break_down_id in($poIds)";
				$order_cond1=" and b.po_breakdown_id in($poIds)";
				$order_cond2=" and d.po_breakdown_id in($poIds)";
				$precost_po_cond=" and c.po_break_down_id in($poIds)";
			}*/
			$order_cond=where_con_using_array($expoid,0,'b.po_break_down_id');
			$order_cond1=where_con_using_array($expoid,0,'b.po_breakdown_id');
			$order_cond2=where_con_using_array($expoid,0,'d.po_breakdown_id');
			$precost_po_cond=where_con_using_array($expoid,0,'c.po_break_down_id');
			 

			$condition= new condition();
			
			$condition->company_name("=$cbo_company_name");
			if(str_replace("'","",$cbo_buyer_name)>0){
				 $condition->buyer_name("=$cbo_buyer_name");
			}
			if($poIds!="")
			{
				$condition->po_id_in("$poIds");
			}
			
			$condition->init();
			$trim= new trims($condition);
			//echo $trim->getQuery(); die;
			$trim_qty=$trim->getQtyArray_by_orderCountryAndPrecostdtlsid();
			//print_r($trim_qty);
			$trim= new trims($condition);
			$trim_amount=$trim->getAmountArray_by_orderCountryAndPrecostdtlsid();
			
			$budget_arr=array();
			$sqlbom="select a.job_no, a.costing_per, a.costing_date as costing_date, b.id as trim_dtla_id, b.trim_group, b.description, b.cons_uom, b.cons_dzn_gmts, b.rate, b.amount, b.nominated_supp, c.cons, c.country_id, c.cons as cons_cal, c.po_break_down_id
			from wo_pre_cost_mst a, wo_pre_cost_trim_cost_dtls b, wo_pre_cost_trim_co_cons_dtls c
			where c.job_no=b.job_no and b.id=c.wo_pre_cost_trim_cost_dtls_id and a.job_no=b.job_no and c.cons>0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $precost_po_cond
			order by b.id asc";
			//echo $sqlbom; die;
			$sql_pre_cost=sql_select($sqlbom);
			
			foreach($sql_pre_cost as $rowp)
			{
				$dzn_qnty=0;

				if($rowp[csf('costing_per')]==1) $dzn_qnty=12;
				else if($rowp[csf('costing_per')]==3) $dzn_qnty=12*2;
				else if($rowp[csf('costing_per')]==4) $dzn_qnty=12*3;
				else if($rowp[csf('costing_per')]==5) $dzn_qnty=12*4;
				else $dzn_qnty=1;
				
				$po_id=$rowp[csf('po_break_down_id')];
				$job_no=$rowp[csf('job_no')];
				$bomDtlsId=$rowp[csf('trim_dtla_id')];
				
				$po_qty=0;$req_qnty=0; $req_value=0;
				$pocountrystr="";
				$pocountrystr=$po_id.$rowp[csf('country_id')].$bomDtlsId;
				if (!in_array($pocountrystr,$tmparr) )
				{
					if($rowp[csf('country_id')]==0)
					{
						$po_qty=$po_arr[$po_id]['order_quantity'];
						$req_qnty+=$trim_qty[$po_id][$rowp[csf('country_id')]][$bomDtlsId];
					}
					else
					{
						$country_id= explode(",",$rowp[csf('country_id')]);
						for($cou=0;$cou<=count($country_id); $cou++)
						{
							$po_qty+=$sql_po_qty_country_wise_arr[$po_id][$country_id[$cou]];
							$req_qnty+=$trim_qty[$po_id][$country_id[$cou]][$bomDtlsId];
							//$req_value+=$trim_amount[$po_id][$rowp[csf('country_id')]][$bomDtlsId];
						}
					}
                   $tmparr[]=$pocountrystr;
				}
				$req_value=$rowp[csf('rate')]*$req_qnty;
				
				$po_data_arr[$job_no][$bomDtlsId]['trim_dtla_id']=$bomDtlsId;// for rowspan
				$po_data_arr[$job_no][$bomDtlsId]['trim_group']=$rowp[csf('trim_group')];
				$po_data_arr[$job_no][$bomDtlsId]['req_qnty']+=$req_qnty;
				$po_data_arr[$job_no][$bomDtlsId]['req_value']+=$req_value;
				$po_data_arr[$job_no][$bomDtlsId]['cons_uom']=$rowp[csf('cons_uom')];
				$po_data_arr[$job_no][$bomDtlsId]['trim_group_from']="BOM";
				$po_data_arr[$job_no][$bomDtlsId]['description']=$rowp[csf('description')];
				$po_data_arr[$job_no][$bomDtlsId]['country_id'].=$rowp[csf('country_id')].',';
				$po_data_arr[$job_no][$bomDtlsId]['avg_cons']=$rowp[csf('cons_dzn_gmts')];
				
				$budget_arr[$job_no]['costing_per']=$rowp[csf('costing_per')];
				$budget_arr[$job_no]['costing_date']=$rowp[csf('costing_date')];
			}
			unset($sql_pre_cost);

			if($db_type==2)
			{
				$sqlBookingWithoutBom="select min(a.booking_date) as booking_date, b.job_no, LISTAGG(CAST(a.booking_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as booking_no, LISTAGG(CAST(a.supplier_id || '**' || a.pay_mode AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as supplier_id,
				LISTAGG(CAST(b.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as booking_dtls_id,
				 b.po_break_down_id, b.trim_group, b.pre_cost_fabric_cost_dtls_id, sum(b.wo_qnty) as wo_qnty, sum(b.amount/b.exchange_rate) as amount, sum(b.rate) as rate
				from wo_booking_mst a, wo_booking_dtls b
				where a.item_category=4 and a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name $order_cond 
				group by b.po_break_down_id, b.trim_group, b.job_no, b.pre_cost_fabric_cost_dtls_id";//and item_from_precost=2
			}
			else if($db_type==0)
			{
				$sqlBookingWithoutBom="select min(a.booking_date) as booking_date, b.job_no, group_concat(a.booking_no) as booking_no, group_concat(concat_ws('**',a.supplier_id,a.pay_mode)) as supplier_id, b.po_break_down_id, b.trim_group, b.pre_cost_fabric_cost_dtls_id, sum(b.wo_qnty) as wo_qnty, sum(b.amount/b.exchange_rate) as amount, sum(b.rate) as rate
				from wo_booking_mst a, wo_booking_dtls b
				where a.item_category=4 and a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name $order_cond group by b.po_break_down_id, b.trim_group, b.job_no, b.pre_cost_fabric_cost_dtls_id";//and item_from_precost=2
			}
			//echo $sqlBookingWithoutBom; die;
			$sqlBookingWithoutBomData=sql_select($sqlBookingWithoutBom);
			$booking_precost_id=array(); $bookingId="";
			foreach($sqlBookingWithoutBomData as $worow)
			{
				$conversion_factor_rate=$conversion_factor_array[$worow[csf('trim_group')]]['con_factor'];
				$cons_uom=$item_arr[$worow[csf('trim_group')]]['order_uom'];
				$booking_no=implode(",",array_unique(explode(",",$worow[csf('booking_no')])));
				$supplier_id=implode(",",array_unique(explode(",",$worow[csf('supplier_id')])));
				$wo_qnty=$worow[csf('wo_qnty')];
				$amount=$worow[csf('amount')];
				$wo_date=$worow[csf('booking_date')];
				
				$bookingId.=$worow[csf('booking_dtls_id')].",";

				$trim_dtla_id=$worow[csf('pre_cost_fabric_cost_dtls_id')];
				$booking_id_arr=array_unique(explode(",",$worow[csf('booking_dtls_id')]));
				foreach($booking_id_arr as $book_id)
				{
					$booking_precost_id[$book_id]=$trim_dtla_id;
				}

				$po_data_arr[$worow[csf('job_no')]][$trim_dtla_id]['wo_qnty']+=$wo_qnty;
				$po_data_arr[$worow[csf('job_no')]][$trim_dtla_id]['amount']+=$amount;
				$po_data_arr[$worow[csf('job_no')]][$trim_dtla_id]['booking_no']=$booking_no;
				$po_data_arr[$worow[csf('job_no')]][$trim_dtla_id]['supplier_id']=$worow[csf('supplier_id')];
				$po_data_arr[$worow[csf('job_no')]][$trim_dtla_id]['conversion_factor_rate']=$conversion_factor_rate;
			}
			unset($sqlBookingWithoutBomData);
			
			$exbookingid=array_filter(array_unique(explode(",",$bookingId)));
			$tot_rowsbook=count($exbookingid);
			$bookingIds=implode(",",$exbookingid);

			$bookingConsCond="";
			/*if($db_type==2 && $tot_rowsbook>1000)
			{
				$bookingConsCond=" and (";
				
				$bookingIdsArr=array_chunk(explode(",",$bookingIds),999);
				foreach($bookingIdsArr as $bids)
				{
					$bids=implode(",",$bids);
					$bookingConsCond.=" wo_trim_booking_dtls_id in($bids) or";
				}
				$bookingConsCond=chop($bookingConsCond,'or ');
				$bookingConsCond.=")";
			}
			else
			{
				$bookingConsCond=" and wo_trim_booking_dtls_id in($bookingIds)";
			}*/
			$bookingConsCond=where_con_using_array($exbookingid,0,'wo_trim_booking_dtls_id');
			$PibookingConsCond=where_con_using_array($exbookingid,0,'b.work_order_dtls_id');
			
			$sqlBookCons="select wo_trim_booking_dtls_id, job_no, po_break_down_id, color_number_id, gmts_sizes, item_color, item_size, cons, requirment, booking_no, description, rate, amount from wo_trim_book_con_dtls where status_active=1 and is_deleted=0 $bookingConsCond";
			//echo $sqlBookCons; die;
			$sqlBookConsData=sql_select($sqlBookCons); $bookingConsArr=array();
			foreach($sqlBookConsData as $crow)
			{
				if($crow[csf('color_number_id')]==0) $crow[csf('color_number_id')]="";
				if($crow[csf('item_color')]==0) $crow[csf('item_color')]="";
				if($crow[csf('item_size')]=='0') $crow[csf('item_size')]="";
				
				$bom_id=$booking_precost_id[$crow[csf('wo_trim_booking_dtls_id')]];
				$bookingConsArr[$crow[csf('job_no')]][$bom_id][$crow[csf('color_number_id')]][$crow[csf('item_color')]][$crow[csf('item_size')]]['qty']+=$crow[csf('cons')];
				$bookingConsArr[$crow[csf('job_no')]][$bom_id][$crow[csf('color_number_id')]][$crow[csf('item_color')]][$crow[csf('item_size')]]['amt']+=$crow[csf('amount')];
			}
			unset($sqlBookConsData);
			//echo "<pre>";print_r($bookingConsArr);die;

			$sqlRec="select b.po_breakdown_id, a.item_description, a.item_group_id, c.receive_basis, a.booking_id, b.quantity as quantity, b.order_rate as rate, c.exchange_rate, (b.quantity*b.order_rate) as amount, d.color, d.item_color, d.item_size
			from inv_trims_entry_dtls a, order_wise_pro_details b, inv_receive_master c, product_details_master d
			where a.mst_id=c.id and a.id=b.dtls_id and a.trans_id=b.trans_id and b.trans_id!=0 and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and c.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_cond1 order by a.item_group_id ";
			$sqlRecData=sql_select($sqlRec); $recDataArr=array();
			//echo $sqlRec; die;
			foreach($sqlRecData as $row)
			{
				if($row[csf('color')]==0) $row[csf('color')]="";
				if($row[csf('item_color')]==0) $row[csf('item_color')]="";
				if($row[csf('item_size')]=='0') $row[csf('item_size')]="";
				$recDataArr[$row[csf('po_breakdown_id')]][trim($row[csf('item_description')])][$row[csf('item_group_id')]][$row[csf('color')]][$row[csf('item_color')]][$row[csf('item_size')]]['inhouse_qnty']+=$row[csf('quantity')];
				$amount=0; $amount=($row[csf('quantity')]*$row[csf('rate')]);
				$recDataArr[$row[csf('po_breakdown_id')]][trim($row[csf('item_description')])][$row[csf('item_group_id')]][$row[csf('color')]][$row[csf('item_color')]][$row[csf('item_size')]]['inhouse_value']+=$amount;
				
				$recDataArr[$row[csf('po_breakdown_id')]][trim($row[csf('item_description')])][$row[csf('item_group_id')]][$row[csf('color')]][$row[csf('item_color')]][$row[csf('item_size')]]['basis_piwono'].=$row[csf('receive_basis')].'_'.$row[csf('booking_id')].',';
			}
			unset($sqlRecData); //echo $q;
			//echo "<pre>";
			//print_r($recDataArr['67305']['49']);//=189=23853=23853=  ['7706']['7706']

			$transfer_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id, c.item_description, c.color, c.item_color, c.item_size, sum((case when d.trans_type=5 then d.quantity else 0 end)-(case when d.trans_type=6 then d.quantity else 0 end)) as quantity ,
			sum(case when d.trans_type=5 then d.quantity else 0 end) as in_qty,
			sum(case when d.trans_type=6 then d.quantity else 0 end) as out_qty,
			sum(case when d.trans_type=5 then (d.quantity*d.order_rate) else 0 end) as in_amount,
			sum(case when d.trans_type=6 then (d.quantity*d.order_rate) else 0 end) as out_amount
			from product_details_master c,order_wise_pro_details d
			where d.prod_id=c.id and d.trans_type in(5,6) and d.entry_form=78 and d.status_active=1 and d.is_deleted=0 $order_cond2 group by d.po_breakdown_id, c.item_group_id, c.item_description, c.color, c.item_color, c.item_size");
			foreach($transfer_qty_data as $row)
			{
				$transfe_amount=0;
				if($row[csf('item_size')]=='0') $row[csf('item_size')]="";
				$transfe_amount=$row[csf('in_amount')]-$row[csf('out_amount')];
				$transfer_data_arr[$row[csf('po_breakdown_id')]][trim($row[csf('item_description')])][$row[csf('item_group_id')]][$row[csf('color')]][$row[csf('item_color')]][$row[csf('item_size')]]['in_qty']+=$row[csf('in_qty')];
				$transfer_data_arr[$row[csf('po_breakdown_id')]][trim($row[csf('item_description')])][$row[csf('item_group_id')]][$row[csf('color')]][$row[csf('item_color')]][$row[csf('item_size')]]['out_qty']+=$row[csf('out_qty')];
				$transfer_data_arr[$row[csf('po_breakdown_id')]][trim($row[csf('item_description')])][$row[csf('item_group_id')]][$row[csf('color')]][$row[csf('item_color')]][$row[csf('item_size')]]['in_amount']+=$row[csf('in_amount')];
				$transfer_data_arr[$row[csf('po_breakdown_id')]][trim($row[csf('item_description')])][$row[csf('item_group_id')]][$row[csf('color')]][$row[csf('item_color')]][$row[csf('item_size')]]['out_amount']+=$row[csf('out_amount')];
			}
			//echo "<pre>";print_r($transfer_data_arr); 
			unset($transfer_qty_data);

			$receive_rtn_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id, d.quantity as quantity, d.order_rate as rate, c.item_description, c.color, c.item_color, c.item_size
			from product_details_master c, order_wise_pro_details d
			where d.prod_id=c.id and d.trans_type=3 and d.entry_form=49 and d.status_active=1 and d.is_deleted=0 $order_cond2");
			foreach($receive_rtn_qty_data as $row)
			{
				$receive_rtn_amount=0;
				if($row[csf('color')]==0) $row[csf('color')]="";
				if($row[csf('item_color')]==0) $row[csf('item_color')]="";
				if($row[csf('item_size')]=='0') $row[csf('item_size')]="";
				//$conv_quantity=$row[csf('quantity')]/$conversion_factor_array[$row[csf('item_group_id')]]['con_factor'];
				$conv_quantity=$row[csf('quantity')];
				$receive_rtn_amount=$conv_quantity*$row[csf('rate')];
				$rcv_rtn_data_arr[$row[csf('po_breakdown_id')]][trim($row[csf('item_description')])][$row[csf('item_group_id')]][$row[csf('color')]][$row[csf('item_color')]][$row[csf('item_size')]]['rcv_rtn_qty']+=$conv_quantity;
				$rcv_rtn_data_arr[$row[csf('po_breakdown_id')]][trim($row[csf('item_description')])][$row[csf('item_group_id')]][$row[csf('color')]][$row[csf('item_color')]][$row[csf('item_size')]]['rcv_rtn_amt']+=$receive_rtn_amount;

			}
			//echo "<pre>";print_r($rcv_rtn_data_arr);

			$sql_wo_pi=sql_select("select a.pi_number, b.work_order_no, b.work_order_dtls_id from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=4 and a.status_active=1 and a.importer_id=$company_name and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.work_order_dtls_id>0 $PibookingConsCond");
			$pi_arr=array();
			foreach($sql_wo_pi as $rowPi)
			{
				if($tem_pi[$rowPi[csf('work_order_no')]]=="")
				{
					$tem_pi[$rowPi[csf('work_order_no')]]=$rowPi[csf('pi_number')];
					$pi_arr[$rowPi[csf('work_order_no')]].=$rowPi[csf('pi_number')].'**';
				}
			}
			unset($sql_wo_pi);
			
			
			/*$sqlLc="select a.export_lc_no, b.wo_po_break_down_id from com_export_lc a, com_export_lc_order_info b where a.id=b.com_export_lc_id and a.beneficiary_name=$company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			$sqlLcData=sql_select($sqlLc);
			$lcArr=array();
			foreach($sqlLcData as $rowLc)
			{
				$lcArr[$rowLc[csf('wo_po_break_down_id')]].=$rowLc[csf('export_lc_no')].'**';
			}
			unset($sqlLcData);*/
			
			$sqlLc="select c.lc_number, b.work_order_no from com_pi_master_details a, com_pi_item_details b, com_btb_lc_master_details c, com_btb_lc_pi d where a.id=b.pi_id and c.id=d.com_btb_lc_master_details_id and a.id=d.pi_id and a.item_category_id=4 and a.status_active=1 and a.importer_id=$company_name and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.work_order_dtls_id>0 $PibookingConsCond group by c.lc_number, b.work_order_no";
			//echo $sqlLc;
			$sqlLcData=sql_select($sqlLc);
			$lcArr=array();
			foreach($sqlLcData as $rowLc)
			{
				if($tem_lc[$rowLc[csf('work_order_no')]]=="")
				{
					$tem_lc[$rowLc[csf('work_order_no')]]=$rowLc[csf('lc_number')];
					$lcArr[$rowLc[csf('work_order_no')]].=$rowLc[csf('lc_number')].'**';
				}
			}
			unset($sqlLcData);
			

			/*$receive_rtn_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id, d.quantity as quantity, c.order_rate as rate
			from product_details_master c,order_wise_pro_details d
			where d.prod_id=c.id and d.trans_type=3 and d.entry_form=49 and d.status_active=1 and d.is_deleted=0 $order_cond2");
			foreach($receive_rtn_qty_data as $row)
			{
				$ord_uom_qty=0; $receive_rtn_amount=0;
				//$ord_uom_qty=$row[csf('quantity')]/$conversion_factor_array[$row[csf('item_group_id')]]['con_factor'];
				$ord_uom_qty=$row[csf('quantity')];
				$receive_rtn_amount=$ord_uom_qty*$row[csf('rate')];
				$po_data_arr[$row[csf('po_breakdown_id')]]['receive_rtn_qty'][$row[csf('item_group_id')]]+=$ord_uom_qty;
				$po_data_arr[$row[csf('po_breakdown_id')]]['receive_rtn_amount'][$row[csf('item_group_id')]]+=$receive_rtn_amount;
			}
			//echo "<pre>";print_r($style_data_arr);
			unset($receive_rtn_qty_data);
			*/
			/*$transfer_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id, sum((case when d.trans_type=5 then d.quantity else 0 end)-(case when d.trans_type=6 then d.quantity else 0 end)) as quantity ,
			sum(case when d.trans_type=5 then d.quantity else 0 end) as in_qty,
			sum(case when d.trans_type=6 then d.quantity else 0 end) as out_qty,
			sum(case when d.trans_type=5 then (d.quantity*d.order_rate) else 0 end) as in_amount,
			sum(case when d.trans_type=6 then (d.quantity*d.order_rate) else 0 end) as out_amount
			from product_details_master c,order_wise_pro_details d
			where d.prod_id=c.id and d.trans_type in(5,6) and d.entry_form=78 and d.status_active=1 and d.is_deleted=0 $order_cond2 group by d.po_breakdown_id, c.item_group_id");
			foreach($transfer_qty_data as $row)
			{
				$transfe_amount=0;
				$transfe_amount=$row[csf('in_amount')]-$row[csf('out_amount')];

				$po_data_arr[$row[csf('po_breakdown_id')]]['transfe_qty'][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				$po_data_arr[$row[csf('po_breakdown_id')]]['transfe_in'][$row[csf('item_group_id')]]+=$row[csf('in_qty')];
				$po_data_arr[$row[csf('po_breakdown_id')]]['transfe_out'][$row[csf('item_group_id')]]+=$row[csf('out_qty')];
				$po_data_arr[$row[csf('po_breakdown_id')]]['transfe_in_amount'][$row[csf('item_group_id')]]+=$row[csf('in_amount')];
				$po_data_arr[$row[csf('po_breakdown_id')]]['transfe_out_amount'][$row[csf('item_group_id')]]+=$row[csf('out_amount')];
				$po_data_arr[$row[csf('po_breakdown_id')]]['transfe_amount'][$row[csf('item_group_id')]]+=$transfe_amount;
			}
			unset($transfer_qty_data);*/

			$sqlIssue="select b.po_breakdown_id, p.item_group_id, (b.quantity) as quantity, (b.quantity*b.order_rate) as issue_amount, p.color, p.item_color, p.item_size, p.item_description
			from inv_trims_issue_dtls a, order_wise_pro_details b, inv_issue_master d, product_details_master p
			where a.mst_id=d.id and a.prod_id=p.id and a.trans_id=b.trans_id and b.trans_type=2 and d.entry_form=25 and b.entry_form=25 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_cond1 $trm_group_iss_cond ";
			$sqlIssueData=sql_select($sqlIssue); $issueDataArr=array();
			//echo $issue_qty_data; die;
			foreach($sqlIssueData as $row)
			{
				if($row[csf('color')]==0) $row[csf('color')]="";
				if($row[csf('item_color')]==0) $row[csf('item_color')]="";
				if($row[csf('item_size')]=='0') $row[csf('item_size')]="";
				$issueDataArr[$row[csf('po_breakdown_id')]][trim($row[csf('item_description')])][$row[csf('item_group_id')]][$row[csf('color')]][$row[csf('item_color')]][$row[csf('item_size')]]['issue_qty']+=$row[csf('quantity')];
				$issueDataArr[$row[csf('po_breakdown_id')]][trim($row[csf('item_description')])][$row[csf('item_group_id')]][$row[csf('color')]][$row[csf('item_color')]][$row[csf('item_size')]]['issue_amount']+=$row[csf('issue_amount')];
			}
			unset($sqlIssueData);
			
			$issue_rtn_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id, c.color, c.item_color, c.item_size,c.item_description, d.quantity as quantity, (d.quantity*d.order_rate) as amount
				from product_details_master c, order_wise_pro_details d
				where d.prod_id=c.id and d.trans_type=4 and d.entry_form=73 and d.status_active=1 and d.is_deleted=0 $order_cond2");
			foreach($issue_rtn_qty_data as $row)
			{
				if($row[csf('color')]==0) $row[csf('color')]="";
				if($row[csf('item_color')]==0) $row[csf('item_color')]="";
				if($row[csf('item_size')]=='0') $row[csf('item_size')]="";
				$issueRtnDataArr[$row[csf('po_breakdown_id')]][trim($row[csf('item_description')])][$row[csf('item_group_id')]][$row[csf('color')]][$row[csf('item_color')]][$row[csf('item_size')]]['issue_rtn_qty']+=$row[csf('quantity')];
				$issueRtnDataArr[$row[csf('po_breakdown_id')]][trim($row[csf('item_description')])][$row[csf('item_group_id')]][$row[csf('color')]][$row[csf('item_color')]][$row[csf('item_size')]]['issue_rtn_amt']+=$row[csf('amount')];
			}

			unset($issue_rtn_qty_data);
			/*echo '<pre>';
			print_r($issueRtnDataArr);*/


			/*$issue_rtn_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id, d.quantity as quantity, (d.quantity*d.order_rate) as amount
			from product_details_master c, order_wise_pro_details d
			where d.prod_id=c.id and d.trans_type=4 and d.entry_form=73 and d.status_active=1 and d.is_deleted=0 $order_cond2");
			foreach($issue_rtn_qty_data as $row)
			{
				$po_data_arr[$row[csf('po_breakdown_id')]]['issue_rtn_qty'][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				$po_data_arr[$row[csf('po_breakdown_id')]]['issue_rtn_amount'][$row[csf('item_group_id')]]+=$row[csf('amount')];
			}
			unset($issue_rtn_qty_data);*/
			$inhouse_or_rec_blance=0;
			
			$rowspanpoArr=array(); $rowspanpoBomArr=array(); $rowspanpoColorArr=array(); $rowspanpoColorSizeArr=array();
			foreach($po_data_arr as $job_no=>$jobdata)
			{
				$i=1;
				foreach($jobdata as $bomid=>$bomData)
				{
					$j=1;
					if(!empty($bookingConsArr[$job_no][$bomid]))
					{
						foreach($bookingConsArr[$job_no][$bomid] as $gmtscolorid=>$gmtscolorData)
						{
							$k=1;
							foreach($gmtscolorData as $itemcolorid=>$itemcolorData)
							{
								$n=1;
								foreach($itemcolorData as $size=>$sizeData)
								{
									$rowspanpoArr[$job_no]=$i;
									$rowspanpoBomArr[$job_no][$bomid]=$j;
									$rowspanpoColorArr[$job_no][$bomid][$gmtscolorid]=$k;
									$rowspanpoColorSizeArr[$job_no][$bomid][$gmtscolorid][$size]=$n;
									$i++;
									$j++;
									$k++;
									$n++;
								}
							}
						}
					}
					else
					{
						$rowspanpoArr[$job_no]=$i;
						$rowspanpoBomArr[$job_no][$bomid]=$j;
						$rowspanpoColorArr[$job_no][$bomid]['']=$k;
						$rowspanpoColorSizeArr[$job_no][$bomid]['']['']=$n;
						$i++;
						$j++;
						$k++;
						$n++;
					}
				}
			}
			
			/*print_r($rowspanpoArr).'<br>';
			print_r($rowspanpoColorArr).'<br>';
			print_r($rowspanpoColorArr).'<br>'; die;*/

			$total_pre_costing_value=0; $total_wo_value=0; $total_left_over_balanc=0; $total_issue_amount=0; $total_rec_bal_qnty=0;

			$summary_array=array();
			$q=1; $a=1;
			foreach($po_data_arr as $job_no=>$podata)
			{
				$i=1; $rowspan=$rowspanpoArr[$job_no];
				
				$poid=implode(",",array_filter(array_unique(explode(",",$job_arr[$job_no]['poid']))));
				$po_number=implode(",",array_filter(array_unique(explode(",",$job_arr[$job_no]['po_number']))));
				
				foreach($podata as $bomtrimid=>$bomData)
				{
					$j=1; $rowspanpobom=0; $rowspanpobom=$rowspanpoBomArr[$job_no][$bomtrimid];
					if(!empty($bookingConsArr[$job_no][$bomtrimid]))
					{
						foreach($bookingConsArr[$job_no][$bomtrimid] as $gmtscolorid=>$gmtscolorData)
						{
							$k=1; $rowspanpoBomColor=0; $rowspanpoBomColor=$rowspanpoColorArr[$job_no][$bomtrimid][$gmtscolorid]; //echo $rowspanposolor.'<br>';
							foreach($gmtscolorData as $itemcolorid=>$itemcolorData)
							{
								$n=1;
								foreach($itemcolorData as $size=>$sizeData)
								{
									if($q%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
                                    <tr bgcolor="<?=$bgcolor;?>" onclick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>">
										<?
                                        if($i==1)
                                        {
											
                                            ?>
                                            <td width="30" rowspan="<?=$rowspan; ?>" align="center" valign="middle"><?=$a; ?></td>
                                            <td width="60" rowspan="<?=$rowspan; ?>" style="word-break:break-all" valign="middle"><?=$buyer_short_name_library[$job_arr[$job_no]['buyer']]; ?></td>
                                            <td width="60" rowspan="<?=$rowspan; ?>" style="word-break:break-all" valign="middle"><?=$buyer_short_name_library[$job_arr[$job_no]['client']]; ?></td>
                                            <td width="90" rowspan="<?=$rowspan; ?>" style="word-break:break-all" valign="middle"><?=$job_no; ?></td>
                                            
                                            <td width="100" rowspan="<?=$rowspan; ?>" style="word-break: break-all;" valign="middle"><?=$job_arr[$job_no]['style_ref']; ?></td>
                                            <td width="100" rowspan="<?=$rowspan; ?>" style="word-break: break-all;" valign="middle"><a href='#report_details' onclick="generate_report('<?=$company_name; ?>','<?=$job_no; ?>','<?=$job_arr[$job_no]['buyer']; ?>','<?=$job_arr[$job_no]['style_ref']; ?>','<?=change_date_format($budget_arr[$job_no]['costing_date']); ?>','<?=$job_no; ?>','<?=$budget_arr[$job_no]['costing_per']; ?>','preCostRpt');"><?=$po_number; ?></a></td>
                                            <td width="80" rowspan="<?=$rowspan; ?>" align="right" style="word-break: break-all;" valign="middle"><a href='#report_details' onclick="order_qty_popup('<?=$company_name; ?>','<?=$job_no; ?>','<?=$poid; ?>','<?=$job_arr[$job_no]['buyer']; ?>','','' ,'order_qty_data');"><?=number_format($job_arr[$job_no]['order_quantity_set'],0,'.',''); ?></a></td>
                    
                                            <td width="50" rowspan="<?=$rowspan; ?>" style="word-break: break-all;" align="center" valign="middle"><?=$unit_of_measurement[$job_arr[$job_no]['order_uom']]; ?></td>
                                            <td width="80" rowspan="<?=$rowspan; ?>" style="word-break: break-all;" align="right" valign="middle"><?=number_format($job_arr[$job_no]['order_quantity'],0,'.',''); ?></td>
                                            <?
											$gPoQty+=$job_arr[$job_no]['order_quantity_set'];
											$gPcsPoQty+=$job_arr[$job_no]['order_quantity'];
                                            $a++;
                                        }
                                        
                                        if($j==1)
                                        {
                                            ?>
                                            <td width="100" rowspan="<?=$rowspanpobom; ?>" title="<?="BOM-".$bomtrimid; ?>" style="word-break: break-all;" valign="middle"><?=$item_library[$bomData['trim_group']]; ?></td>
                                            <td width="140" rowspan="<?=$rowspanpobom; ?>" style="word-break: break-all;" valign="middle"><?=$bomData['description']; ?></td>
                                            <td width="50" rowspan="<?=$rowspanpobom; ?>" style="word-break: break-all;" valign="middle"><?=$unit_of_measurement[$bomData['cons_uom']]; ?></td>
                                            <td width="70" rowspan="<?=$rowspanpobom; ?>" style="word-break: break-all;" align="right" valign="middle"><?=$bomData['avg_cons']; ?></td>
                                            <td width="100" rowspan="<?=$rowspanpobom; ?>" style="word-break: break-all;" align="right" valign="middle"><?=number_format($bomData['req_qnty'],2); ?></td>
                                            <td width="100" rowspan="<?=$rowspanpobom; ?>" style="word-break: break-all;" align="right" valign="middle"><?=number_format($bomData['req_value'],2); ?></td>
                                            <?
											$gReqQty+=$bomData['req_qnty'];
											$gReqVal+=$bomData['req_value'];
                                        }
										
										$bookingReq=$bomReqQty=0; $tdColor="";
										$bomReqQty=number_format($bomData['req_qnty'],2,'.','');
										$bookingReq=number_format($bomData['wo_qnty'],2,'.','');
										
										if($bomReqQty==$bookingReq) $tdColor="Green";
										else if($bomReqQty<$bookingReq) $tdColor="RED";
										else if($bomReqQty>$bookingReq) $tdColor="Yellow";
                                        
                                        if($k==1)
                                        {
                                            ?>
                                            <td width="100" rowspan="<?=$rowspanpoBomColor; ?>" style="word-break: break-all;" valign="middle"><?=$colorArr[$gmtscolorid]; ?></td>
                                            <td width="100" rowspan="<?=$rowspanpoBomColor; ?>" style="word-break: break-all;" valign="middle"><?=$colorArr[$itemcolorid]; ?></td>
                                            <?
                                        }
                                        //echo $poid.'==';
                                       	$rcvQty=$netIssueQty=$netIssueAmt=$inHouseQty=$inHouseAmt=$netInHouseAmt=$issueQty=$issueAmt=$leftOverQty=$leftOverAmt=$transferINHouseQty=$rcv_rtn_qty=$issueRtnAmt=$transferOutHouseQty=$transferINHouseAmt=$transferOutHouseAmt=$rcv_rtn_amt=$issueRtnQty=0;
										$expoid=explode(",",$poid);
										foreach($expoid as $pid)
										{
											//if($bomData['trim_group']==49)echo $recDataArr[$pid][$bomData['trim_group']][$gmtscolorid][$itemcolorid][$size]['inhouse_qnty'].'<br>';
											/*if($bomData['description']=="") $bomData['description']=0;
											$inHouseQty+=$recDataArr[$pid][trim($bomData['description'])][$bomData['trim_group']][$gmtscolorid][$itemcolorid][$size]['inhouse_qnty'];
											$inHouseAmt+=$recDataArr[$pid][trim($bomData['description'])][$bomData['trim_group']][$gmtscolorid][$itemcolorid][$size]['inhouse_value'];
											$issueQty+=$issueDataArr[$pid][trim($bomData['description'])][$bomData['trim_group']][$gmtscolorid][$itemcolorid][$size]['issue_qty'];
											$issueAmt+=$issueDataArr[$pid][trim($bomData['description'])][$bomData['trim_group']][$gmtscolorid][$itemcolorid][$size]['issue_amount'];*/
											
											if($bomData['description']=="") $bomData['description']=0;
											$inHousekey=$poid.'_'.trim($bomData['description']).'_'.$bomData['trim_group'].'_'.$gmtscolorid.'_'.$itemcolorid.'_'.$size;
	                                        $rcvQty+=$recDataArr[$pid][trim($bomData['description'])][$bomData['trim_group']][$gmtscolorid][$itemcolorid][$size]['inhouse_qnty'];
	                                        $netInHouseAmt+=$recDataArr[$pid][trim($bomData['description'])][$bomData['trim_group']][$gmtscolorid][$itemcolorid][$size]['inhouse_value'];
	                                        $netIssueQty+=$issueDataArr[$pid][trim($bomData['description'])][$bomData['trim_group']][$gmtscolorid][$itemcolorid][$size]['issue_qty'];
	                                        $netIssueAmt+=$issueDataArr[$pid][trim($bomData['description'])][$bomData['trim_group']][$gmtscolorid][$itemcolorid][$size]['issue_amount'];


	                                        $issueRtnQty+=$issueRtnDataArr[$pid][trim($bomData['description'])][$bomData['trim_group']][$gmtscolorid][$itemcolorid][$size]['issue_rtn_qty'];
	                                        $issueRtnAmt+=$issueRtnDataArr[$pid][trim($bomData['description'])][$bomData['trim_group']][$gmtscolorid][$itemcolorid][$size]['issue_rtn_amt'];

	                                        $transferINHouseQty+=$transfer_data_arr[$pid][trim($bomData['description'])][$bomData['trim_group']][$gmtscolorid][$itemcolorid][$size]['in_qty'];
	                                        $transferOutHouseQty+=$transfer_data_arr[$pid][trim($bomData['description'])][$bomData['trim_group']][$gmtscolorid][$itemcolorid][$size]['out_qty'];

	                                        $transferINHouseAmt+=$transfer_data_arr[$pid][trim($bomData['description'])][$bomData['trim_group']][$gmtscolorid][$itemcolorid][$size]['in_amount'];
	                                        $transferOutHouseAmt+=$transfer_data_arr[$pid][trim($bomData['description'])][$bomData['trim_group']][$gmtscolorid][$itemcolorid][$size]['out_amount']; 

	                                        $rcv_rtn_qty+=$rcv_rtn_data_arr[$pid][trim($bomData['description'])][$bomData['trim_group']][$gmtscolorid][$itemcolorid][$size]['rcv_rtn_qty'];
	                                        $rcv_rtn_amt+=$rcv_rtn_data_arr[$pid][trim($bomData['description'])][$bomData['trim_group']][$gmtscolorid][$itemcolorid][$size]['rcv_rtn_amt'];
	                                        //echo $inHouseQty.'=='.$transferINHouseQty.'=='.$transferOutHouseQty.'=='.$rcv_rtn_qty.'++';
	                                        //echo $rcv_rtn_qty.'_'.$poid.'_'.trim($bomData['description']).'_'.$bomData['trim_group'].'_'.$gmtscolorid.'_'.$itemcolorid.'_'.$size.'++';
	                                        
										}
										$inHouseQty=($rcvQty+$transferINHouseQty)-$transferOutHouseQty-$rcv_rtn_qty;
                                        $inHouseAmt=($netInHouseAmt+$transferINHouseAmt)-$transferOutHouseAmt-$rcv_rtn_amt;
                                        $issueQty=$netIssueQty-$issueRtnQty;

                                       // echo $inHouseQty.'=='.$rcvQty.'=='.$transferINHouseQty.'=='.$transferOutHouseQty.'=='.$rcv_rtn_qty.'++';
                                        //echo $netIssueQty.'=='.$issueRtnQty;
                                        $issueAmt=$netIssueAmt-$issueRtnAmt;
                                        $leftOverQty=$inHouseQty-$issueQty;
                                        $leftOverAmt=$inHouseAmt-$issueAmt;
										//echo $poid.'='.$bomData['trim_group'].'='.$gmtscolorid.'='.$itemcolorid.'='.$size.'<br>';
										
                                        $supplier_name_string="";
                                        $supplier_id_arr=array_unique(explode(',',$bomData['supplier_id']));
                                        foreach($supplier_id_arr as $supplier_id_arr_key=>$supplier_id_arr_value)
                                        {
                                            $ex_sup_data=explode("**",$supplier_id_arr_value);
                                            if($ex_sup_data[1]==3 || $ex_sup_data[1]==5) $suplier_name_arr=$company_library; else $suplier_name_arr=$lib_supplier_arr;
                                            $supplier_name_string.=$suplier_name_arr[$ex_sup_data[0]].",";
                                        }
                                        
                                        $booking_no_arr=array_unique(explode(',',$bomData['booking_no']));
                                        $main_booking_no_large_data=""; $piWoNo=''; $lcNo="";
                                        foreach($booking_no_arr as $booking_no1)
                                        {
                                            $piWoNo.=chop($pi_arr[$booking_no1],"**").",";
											$lcNo.=chop($lcArr[$booking_no1],"**").",";
                                        }
										$piWoNo=implode(",",array_filter(array_unique(explode(",",$piWoNo))));
										$lcNo=implode(",",array_filter(array_unique(explode(",",$lcNo))));
                                        ?>
                                        <td width="80" style="word-break: break-all;"><?=$size; ?></td>
                                        <td width="80" style="word-break: break-all;" align="right" bgcolor="<?=$tdColor; ?>"><?=number_format($sizeData['qty'],2); ?></td>
                                        <td width="90" style="word-break: break-all;" align="right"><?=number_format($sizeData['amt'],2); ?></td>
                                        <td width="80" style="word-break: break-all;" align="right"><a href='#report_details' onclick="openmypage_inhouse('<? echo $inHousekey; ?>','booking_inhouse_info');"><? echo number_format($inHouseQty,2,'.',''); ?></a></td>

                                        <td width="90" style="word-break: break-all;" align="right"><?=number_format($inHouseAmt,2); ?></td>
                                        <td width="90" style="word-break: break-all;" align="right"><?=number_format($sizeData['qty']-$inHouseQty,2); ?></td>
                                        <td width="80" style="word-break: break-all;" align="right"><a href='#report_details' onclick="openmypage_issue('<? echo $inHousekey; ?>','booking_issue_info');"><? echo number_format($issueQty,2,'.',''); ?></a></td>
                                        <td width="90" style="word-break: break-all;" align="right"><?=number_format($issueAmt,2); ?></td>
                                        <td width="80" style="word-break: break-all;" align="right"><?=number_format($leftOverQty,2); ?></td>
                                        
                                        <td width="90" style="word-break: break-all;" align="right"><?=number_format($leftOverAmt,2); ?></td>
                                        <td width="140" style="word-break: break-all;"><?=chop($supplier_name_string,","); ?></td>
                                        <td width="120" style="word-break: break-all;"><?=$piWoNo; ?></td>
                                        <td style="word-break: break-all;"><?=$lcNo; ?>&nbsp;</td>
                                    </tr>
                                    <?
									$gWoQty+=$sizeData['qty'];
									$gWoAmt+=$sizeData['amt'];
									$gInHouseQty+=$inHouseQty;
									$gInHouseAmt+=$inHouseAmt;
									$gIssueQty+=$issueQty;
									$gIssueAmt+=$issueAmt;
									$gLeftOverQty+=$leftOverQty;
									$gLftOverAmt+=$leftOverAmt;
									$inhouse_or_rec_blance+=$sizeData['qty']-$inHouseQty;
									
									$i++;
									$j++;
									$k++;
									$n++;
									$q++;
								}
							}
						}
					}
					else
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$rowspanpoBomColor=1;
						?>
						<tr bgcolor="<?=$bgcolor;?>" onclick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>">
							<?
                            if($i==1)
                            {
                                ?>
                                <td width="30" rowspan="<?=$rowspan; ?>" align="center" valign="middle"><?=$a; ?></td>
                                <td width="60" rowspan="<?=$rowspan; ?>" style="word-break:break-all" valign="middle"><?=$buyer_short_name_library[$job_arr[$job_no]['buyer']]; ?></td>
                                <td width="60" rowspan="<?=$rowspan; ?>" style="word-break:break-all" valign="middle"><?=$buyer_short_name_library[$job_arr[$job_no]['client']]; ?></td>
                                <td width="90" rowspan="<?=$rowspan; ?>" style="word-break:break-all" valign="middle"><?=$job_no; ?></td>
                                
                                <td width="100" rowspan="<?=$rowspan; ?>" style="word-break: break-all;" valign="middle"><?=$job_arr[$job_no]['style_ref']; ?></td>
                                <td width="100" rowspan="<?=$rowspan; ?>" style="word-break: break-all;" valign="middle"><a href='#report_details' onclick="generate_report('<?=$company_name; ?>','<?=$job_no; ?>','<?=$job_arr[$job_no]['buyer']; ?>','<?=$job_arr[$job_no]['style_ref']; ?>','<?=change_date_format($budget_arr[$job_no]['costing_date']); ?>','<?=$poid; ?>','<?=$budget_arr[$job_no]['costing_per']; ?>','preCostRpt');"><?=$po_number; ?></a></td>
                                <td width="80" rowspan="<?=$rowspan; ?>" align="right" style="word-break: break-all;" valign="middle"><a href='#report_details' onclick="order_qty_popup('<?=$company_name; ?>','<?=$job_no; ?>','<?=$poid; ?>','<?=$job_arr[$job_no]['buyer']; ?>','','','order_qty_data');"><?=number_format($job_arr[$job_no]['order_quantity_set'],0,'.',''); ?></a></td>
        
                                <td width="50" rowspan="<?=$rowspan; ?>" style="word-break: break-all;" align="center" valign="middle"><?=$unit_of_measurement[$job_arr[$job_no]['order_uom']]; ?></td>
                                <td width="80" rowspan="<?=$rowspan; ?>" style="word-break: break-all;" align="right" valign="middle"><?=number_format($job_arr[$job_no]['order_quantity'],0,'.',''); ?></td>
                                <?
								$gPoQty+=$job_arr[$job_no]['order_quantity_set'];
								$gPcsPoQty+=$job_arr[$job_no]['order_quantity'];
                                $a++;
                            }
                            if($j==1)
                            {
                                ?>
                                <td width="100" rowspan="<?=$rowspanpobom; ?>" title="<?="BOM-".$bomtrimid; ?>" style="word-break: break-all;" valign="middle"><?=$item_library[$bomData['trim_group']]; ?></td>
                                <td width="140" rowspan="<?=$rowspanpobom; ?>" style="word-break: break-all;" valign="middle"><?=$bomData['description']; ?></td>
                                <td width="50" rowspan="<?=$rowspanpobom; ?>" style="word-break: break-all;" valign="middle"><?=$unit_of_measurement[$bomData['cons_uom']]; ?></td>
                                <td width="70" rowspan="<?=$rowspanpobom; ?>" style="word-break: break-all;" align="right" valign="middle"><?=$bomData['avg_cons']; ?></td>
                                <td width="100" rowspan="<?=$rowspanpobom; ?>" style="word-break: break-all;" align="right" valign="middle"><?=number_format($bomData['req_qnty'],2); ?></td>
                                <td width="100" rowspan="<?=$rowspanpobom; ?>" style="word-break: break-all;" align="right" valign="middle"><?=number_format($bomData['req_value'],2); ?></td>
                                <?
								$gReqQty+=$bomData['req_qnty'];
								$gReqVal+=$bomData['req_value'];
                            }
							
							$bookingReq=$bomReqQty=0; $tdColor="";
							$bomReqQty=number_format($bomData['req_qnty'],2,'.','');
							$bookingReq=number_format($bomData['wo_qnty'],2,'.','');
							
							if($bomReqQty==$bookingReq) $tdColor="Green";
							else if($bomReqQty<$bookingReq) $tdColor="RED";
							else if($bomReqQty>$bookingReq) $tdColor="Yellow";
							
                            $inHouseQty=$inHouseAmt=$issueQty=$issueAmt=$leftOverQty=$leftOverAmt=0;
							$expoid=explode(",",$poid);
							foreach($expoid as $pid)
							{
								$inHouseQty+=$recDataArr[$poid][trim($bomData['description'])][$bomData['trim_group']]['']['']['']['inhouse_qnty'];
								$inHouseAmt+=$recDataArr[$poid][trim($bomData['description'])][$bomData['trim_group']]['']['']['']['inhouse_value'];
								$issueQty+=$issueDataArr[$poid][trim($bomData['description'])][$bomData['trim_group']]['']['']['']['issue_qty'];
								$issueAmt+=$issueDataArr[$poid][trim($bomData['description'])][$bomData['trim_group']]['']['']['']['issue_amount'];
							}
                            $leftOverQty=$inHouseQty-$issueQty;
                            $leftOverAmt=$inHouseAmt-$issueAmt;
                            
                            $supplier_name_string="";
                            $supplier_id_arr=array_unique(explode(',',$bomData['supplier_id']));
                            foreach($supplier_id_arr as $supplier_id_arr_key=>$supplier_id_arr_value)
                            {
                                $ex_sup_data=explode("**",$supplier_id_arr_value);
                                if($ex_sup_data[1]==3 || $ex_sup_data[1]==5) $suplier_name_arr=$company_library; else $suplier_name_arr=$lib_supplier_arr;
                                $supplier_name_string.=$suplier_name_arr[$ex_sup_data[0]].",";
                            }
                            $booking_no=implode(",",array_unique(explode(",",$worow[csf('booking_no')])));
							$booking_no=implode(",",array_unique(explode(",",$worow[csf('booking_no')])));
							$booking_no=implode(",",array_unique(explode(",",$worow[csf('booking_no')])));
                            $booking_no_arr=array_unique(explode(',',$bomData['booking_no']));
                            $main_booking_no_large_data=""; $piWoNo=''; $lcNo="";
                            foreach($booking_no_arr as $booking_no1)
                            {
                                $piWoNo.=chop($pi_arr[$booking_no1],"**").",";
								$lcNo.=chop($lcArr[$booking_no1],"**").",";
                            }
							$piWoNo=implode(",",array_filter(array_unique(explode(",",$piWoNo))));
							$lcNo=implode(",",array_filter(array_unique(explode(",",$lcNo))));
                            ?>
                            <td width="100" style="word-break: break-all;">&nbsp;</td>
                            <td width="100" style="word-break: break-all;">&nbsp;</td>
                            <td width="80" style="word-break: break-all;">&nbsp;</td>
                            
                            <td width="80" style="word-break: break-all;" align="right" bgcolor="<?=$tdColor; ?>"><?=number_format($bomData['wo_qnty'],2); ?></td>
                            <td width="90" style="word-break: break-all;" align="right"><?=number_format($bomData['amount'],2); ?></td>
                                
                            <td width="80" style="word-break: break-all;" align="right"><?=number_format($inHouseQty,2); ?></td>
                            <td width="90" style="word-break: break-all;" align="right"><?=number_format($inHouseAmt,2); ?></td>

                            <td width="90" style="word-break: break-all;" align="right"><?=number_format($bomData['wo_qnty']-$inHouseQty,2); ?></td>

                            <td width="80" style="word-break: break-all;" align="right"><?=number_format($issueQty,2); ?></td>
                            <td width="90" style="word-break: break-all;" align="right"><?=number_format($issueAmt,2); ?></td>
                            <td width="80" style="word-break: break-all;" align="right"><?=number_format($leftOverQty,2); ?></td>
                            
                            <td width="90" style="word-break: break-all;" align="right"><?=number_format($leftOverAmt,2); ?></td>
                            <td width="140" style="word-break: break-all;"><?=chop($supplier_name_string,","); ?></td>
                            <td width="120" style="word-break: break-all;"><?=$piWoNo; ?></td>
                            <td style="word-break: break-all;"><?=$lcNo; ?>&nbsp;</td>
                        </tr>
                        <?
						$gWoQty+=$bomData['wo_qnty'];
						$gWoAmt+=$bomData['amount'];
						$gInHouseQty+=$inHouseQty;
						$gInHouseAmt+=$inHouseAmt;
						$gIssueQty+=$issueQty;
						$gIssueAmt+=$issueAmt;
						$gLeftOverQty+=$leftOverQty;
						$gLftOverAmt+=$leftOverAmt;

						$inhouse_or_rec_blance+=$bomData['wo_qnty']-$inHouseQty;
									
						$i++;
						$j++;
						$k++;
						$n++;
						$q++;
					}
				}
			}
			unset($po_data_arr);
			unset($bookingConsArr);
			?>
            </table>
         </div>
        <table class="rpt_table" width="2650" cellpadding="0" cellspacing="0" border="1" rules="all">
            <tfoot>
                <th width="30">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th width="90">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="100">Total:</th>
                <th width="80" style="word-break: break-all;"><?=number_format($gPoQty,2); ?></td>
                <th width="50">&nbsp;</th>
                <th width="80" style="word-break: break-all;"><?=number_format($gPcsPoQty,2); ?></td>
                <th width="100">&nbsp;</th>
                <th width="140">&nbsp;</th>
                <th width="50">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="100" style="word-break: break-all;"><?=number_format($gReqQty,2); ?></td>
                <th width="100" style="word-break: break-all;"><?=number_format($gReqVal,2); ?></td>
                <th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80" style="word-break: break-all;"><?=number_format($gWoQty,2); ?></td>
                <th width="90" style="word-break: break-all;"><?=number_format($gWoAmt,2); ?></td>
                <th width="80" style="word-break: break-all;"><?=number_format($gInHouseQty,2); ?></td>
                <th width="90" style="word-break: break-all;"><?=number_format($gInHouseAmt,2); ?></td>
                <th width="90" style="word-break: break-all;"><?=number_format($inhouse_or_rec_blance,2); ?></td>
                <th width="80" style="word-break: break-all;"><?=number_format($gIssueQty,2); ?></td>
                <th width="90" style="word-break: break-all;"><?=number_format($gIssueAmt,2); ?></td>
                <th width="80" style="word-break: break-all;"><?=number_format($gLeftOverQty,2); ?></td>
                
                <th width="90" style="word-break: break-all;"><?=number_format($gLftOverAmt,2); ?></td>
                <th width="140">&nbsp;</th>
                <th width="120">&nbsp;</th>
                <th>&nbsp;</th>
            </tfoot>
        </table>
        <?
        }
	}
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename****$tot_rows****1";
	exit();
}

if($action=="booking_inhouse_info")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$all_info=explode('_', $info);
	$po_id=$all_info[0];
	$description=$all_info[1];
	$item_name=$all_info[2];
	$gmtscolorid=$all_info[3];
	$itemcolorid=$all_info[4];
	$size=$all_info[5];
	if(str_replace("'","",$description)!=""){
		$sql_cond.=" and a.item_description = '".str_replace("'","",$description)."'";
		$other_sql_cond.=" and c.item_description = '".str_replace("'","",$description)."'";
	}
	if(str_replace("'","",$gmtscolorid)!="")
	{
		$sql_cond.=" and d.color = '".str_replace("'","",$gmtscolorid)."'";
		$other_sql_cond.=" and c.color = '".str_replace("'","",$gmtscolorid)."'";
	} 
	if(str_replace("'","",$itemcolorid)!=""){
		$sql_cond.=" and d.item_color = '".str_replace("'","",$itemcolorid)."'";
		$other_sql_cond.=" and c.item_color = '".str_replace("'","",$itemcolorid)."'";
	} 
	if(str_replace("'","",$size)!="")
	{
		$sql_cond.=" and d.item_size = '".str_replace("'","",$size)."'";
		$other_sql_cond.=" and c.item_size = '".str_replace("'","",$size)."'";
	} 


	if(str_replace("'","",$description)!="") $sql_cond.=" and a.item_description = '".str_replace("'","",$description)."'";

	//$inHouseQty=$recDataArr[$poid][trim($bomData['description'])][$bomData['trim_group']][$gmtscolorid][$itemcolorid][$size]['inhouse_qnty'];
	?>
	<!--<div style="width:880px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
	<fieldset style="width:870px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="30">Sl</th>
                    <th width="60">Prod. ID</th>
                    <th width="100">Recv. ID</th>
                    <th width="100">Wo/Pi No</th>
                    <th width="100">Chalan No</th>
                    <th width="70">Recv. Date</th>
                    <th width="150">Item Description.</th>
                    <th width="80">Recv. Qty.</th>
                    <th>Reject Qty.</th>
				</thead>
                <tbody>
                <?
					$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
					$bookingNoArr=return_library_array( "select id, booking_no from  wo_booking_mst", "id", "booking_no");
					$piArr=return_library_array("select id, pi_number from com_pi_master_details", "id", "pi_number");
					$i=1;

					$item_arr=array();
					$conversion_factor=sql_select("select id,conversion_factor,order_uom from lib_item_group where status_active=1  ");
					foreach($conversion_factor as $row_f)
					{
						$item_arr[$row_f[csf('id')]]['order_uom']=$row_f[csf('conversion_factor')];
					}
					unset($conversion_factor);

					$receive_rtn_data=array();
					$receive_qty_data="select b.po_breakdown_id, a.item_description, a.item_group_id, c.receive_basis, a.booking_id,a.prod_id, c.recv_number, sum( b.quantity ) as quantity, b.order_rate as rate, c.exchange_rate, d.color, d.item_color, d.item_size, c.challan_no, c.receive_date, sum(reject_receive_qnty) as reject_receive_qnty
					from inv_trims_entry_dtls a, order_wise_pro_details b, inv_receive_master c, product_details_master d
					where a.mst_id=c.id and a.trans_id=b.trans_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_breakdown_id in($po_id) and d.item_group_id='$item_name' $sql_cond group by b.po_breakdown_id, a.item_description, a.item_group_id, c.receive_basis, a.booking_id, b.order_rate, c.exchange_rate, d.color, d.item_color, d.item_size, c.challan_no, c.receive_date,a.prod_id, c.recv_number order by a.item_group_id ";
					$dtlsArray=sql_select($receive_qty_data);

					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$qty=0;
						$qty=$row[csf('quantity')];

						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="60"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="100" align="center"><p><? $piwo_no='';
							if($row[csf('receive_basis')]==1)
							{
								$piwo_no=$piArr[$row[csf('booking_id')]];
							}
							else if($row[csf('receive_basis')]==2)
							{
								$piwo_no=$bookingNoArr[$row[csf('booking_id')]];
							}
							echo $piwo_no; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="70" align="center"><p><? echo  change_date_format($row[csf('receive_date')]); ?></p></td>
                            <td width="150" align="center"><p><? echo $row[csf('item_description')]; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($qty,2); ?></p></td>
                            <td align="right"><p><? echo number_format($row[csf('reject_receive_qnty')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty+=$qty;
						$tot_rej_qty+=$row[csf('reject_receive_qnty')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="6" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_qty,2); ?></td>
                         <td><? echo number_format($tot_rej_qty,2); ?></td>
                    </tr>
                </tfoot>
            </table>
            <?
				$transfer_qty_data=sql_select("select a.transfer_system_id, a.transfer_date, d.po_breakdown_id, c.item_group_id, d.trans_type, d.quantity as quantity, b.prod_id, c.item_description
				from  inv_item_transfer_mst a, inv_transaction b, product_details_master c, order_wise_pro_details d
				where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and a.transfer_criteria=4 and a.item_category=4 and b.item_category=4 and b.transaction_type in(5,6) and d.trans_type in(5,6) and d.entry_form=78 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.po_breakdown_id in($po_id) and c.item_group_id='$item_name' $other_sql_cond");
				/*echo "select a.transfer_system_id, a.transfer_date, d.po_breakdown_id, c.item_group_id, d.trans_type, d.quantity as quantity, b.prod_id, c.item_description
				from  inv_item_transfer_mst a, inv_transaction b, product_details_master c, order_wise_pro_details d
				where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and a.transfer_criteria=4 and a.item_category=4 and b.item_category=4 and b.transaction_type in(5,6) and d.trans_type in(5,6) and d.entry_form=78 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.po_breakdown_id in($po_id) and c.item_group_id='$item_name'";*/
			?>
            <table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="30">Sl</th>
                    <th width="80">Prod. ID</th>
                    <th width="100">Transfer. ID</th>
                    <th width="100">Transfer Type</th>
                    <th width="100">Transfer Date</th>
                    <th width="80">Item Description.</th>
                    <th width="160">Return Qty.</th>
				</thead>
                <tbody>
                <?
					/*echo "select a.transfer_system_id, a.transfer_date, d.po_breakdown_id, c.item_group_id, sum((case when b.transaction_type=5 and d.trans_type=5 then d.quantity else 0 end)-(case when b.transaction_type=6 and d.trans_type=6 then d.quantity else 0 end)) as quantity, b.prod_id, c.item_description
					from  inv_item_transfer_mst a, inv_transaction b, product_details_master c, order_wise_pro_details d
					where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and a.transfer_criteria=4 and a.item_category=4 and b.item_category=4 and b.transaction_type in(5,6) and d.trans_type in(5,6) and d.entry_form=78 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.po_breakdown_id in($po_id) and c.item_group_id='$item_name'
					group by a.transfer_system_id, a.transfer_date, d.po_breakdown_id, c.item_group_id, b.prod_id, c.item_description";die;*/


					foreach($transfer_qty_data as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";

						$qty=0;
						$qty=$row[csf('quantity')];

						if($row[csf('trans_type')]==5)
						{
							$trans_type="Transfer In";
							$trans_in_qnty+=$qty;

						}
						else
						{
							$trans_type="Transfer Out";
							$trans_out_qnty+=$qty;
						}

						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="80"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $trans_type; ?></p></td>
                            <td width="100" align="center"><p><? echo  change_date_format($row[csf('transfer_date')]); ?></p></td>
                            <td width="80" align="center"><p><? echo $row[csf('item_description')]; ?></p></td>
                            <td width="160" align="right"><p><? echo number_format($qty,2); ?></p></td>
                        </tr>
						<?
						$tot_trans_qty+=$qty;
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_trans_qty,2); ?></td>
                    </tr>
            </table>

            <table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="30">Sl</th>
                    <th width="80">Prod. ID</th>
                    <th width="100">Return. ID</th>
                    <th width="100">Chalan No</th>
                    <th width="100">Return Date</th>
                    <th width="80">Item Description.</th>
                    <th width="160">Return Qty.</th>
				</thead>
                <tbody>
                <?
					$receive_rtn_qty_data=sql_select("select a.issue_number, a.issue_date , d.po_breakdown_id, c.item_group_id, d.quantity as quantity, b.prod_id, c.item_description
					from inv_issue_master a, inv_transaction b, product_details_master c, order_wise_pro_details d
					where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and b.transaction_type=3 and d.trans_type=3 and a.entry_form=49 and d.entry_form=49 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and d.po_breakdown_id in($po_id) and c.item_group_id='$item_name' $other_sql_cond");


					foreach($receive_rtn_qty_data as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";

						$qty=0;
						$qty=$row[csf('quantity')];
						//$qty=$row[csf('quantity')]/$item_arr[$row[csf('item_group_id')]]['order_uom'];
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="80"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="100" align="center"><p><? echo  change_date_format($row[csf('issue_date')]); ?></p></td>
                            <td width="80" align="center"><p><? echo $row[csf('item_description')]; ?></p></td>
                            <td width="160" align="right"><p><? echo number_format($qty,2); ?></p></td>
                        </tr>
						<?
						$tot_rtn_qty+=$qty;
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_rtn_qty,2); ?></td>
                    </tr>
                    <tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Balance</td>
                        <td><? $balance_qnty=($tot_qty+$trans_in_qnty)-($tot_rtn_qty+$trans_out_qnty); echo number_format($balance_qnty,2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}

if($action=="booking_issue_info")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$all_info=explode('_', $info);
	$po_id=$all_info[0];
	$description=$all_info[1];
	$item_name=$all_info[2];
	$gmtscolorid=$all_info[3];
	$itemcolorid=$all_info[4];
	$size=$all_info[5];
	if(str_replace("'","",$description)!=""){
		$sql_cond.=" and p.item_description = '".str_replace("'","",$description)."'";
		$other_sql_cond.=" and c.item_description = '".str_replace("'","",$description)."'";
	}
	if(str_replace("'","",$gmtscolorid)!="")
	{
		$sql_cond.=" and p.color = '".str_replace("'","",$gmtscolorid)."'";
		$other_sql_cond.=" and c.color = '".str_replace("'","",$gmtscolorid)."'";
	} 
	if(str_replace("'","",$itemcolorid)!=""){
		$sql_cond.=" and p.item_color = '".str_replace("'","",$itemcolorid)."'";
		$other_sql_cond.=" and c.item_color = '".str_replace("'","",$itemcolorid)."'";
	} 
	if(str_replace("'","",$size)!="")
	{
		$sql_cond.=" and p.item_size = '".str_replace("'","",$size)."'";
		$other_sql_cond.=" and c.item_size = '".str_replace("'","",$size)."'";
	} 
	?>
<!--	<div style="width:880px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
-->	<fieldset style="width:870px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="30">Sl</th>
                    <th width="80">Prod. ID</th>
                    <th width="100">Issue. ID</th>
                     <th width="100">Chalan No</th>
                     <th width="100">Issue. Date</th>
                    <th width="80">Item Description.</th>
                    <th width="100">Issue. Qty.</th>
				</thead>
                <tbody>
                <?
					$conversion_factor_array=array();	$item_arr=array();
					$conversion_factor=sql_select("select id ,trim_uom,order_uom,conversion_factor from  lib_item_group  ");
					foreach($conversion_factor as $row_f)
					{
					 $conversion_factor_array[$row_f[csf('id')]]['con_factor']=$row_f[csf('conversion_factor')];
					 $conversion_factor_array[$row_f[csf('id')]]['cons_uom']=$row_f[csf('trim_uom')];
					 $item_arr[$row_f[csf('id')]]['order_uom']=$row_f[csf('order_uom')];
					}
					$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
					$i=1;
					//$wo_sql="select a.item_group_id,a.prod_id,b.recv_number,b.receive_date,a.item_description,sum(a.cons_qnty) as cons_qnty  from inv_receive_master b, inv_trims_entry_dtls a where b.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category=4 group by a.item_group_id,a.prod_id,b.recv_number,b.receive_date,a.item_description";

					$mrr_sql="select a.id, a.issue_number,a.challan_no,p.item_group_id,b.prod_id, a.issue_date,b.item_description,SUM(c.quantity) as quantity
					from  inv_issue_master a,inv_trims_issue_dtls b, order_wise_pro_details c,product_details_master p
					where a.id=b.mst_id  and a.entry_form=25 and p.id=b.prod_id and b.id=c.dtls_id and b.trans_id=c.trans_id and c.trans_type=2 and a.is_deleted=0 and a.status_active=1 and
					b.status_active=1 and b.is_deleted=0 and  c.po_breakdown_id in($po_id) and p.item_group_id='$item_name' $sql_cond group by c.po_breakdown_id,p.item_group_id,b.item_description,a.issue_number,a.id,a.issue_date,b.prod_id,a.challan_no ";

					$dtlsArray=sql_select($mrr_sql);

					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$conv_fact=$conversion_factor_array[$row[csf('item_group_id')]]['con_factor'];
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="80" align="center"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td width="100"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="100" align="center"><p><? echo  change_date_format($row[csf('issue_date')]); ?></p></td>
                            <td width="80" align="center"><p><? echo $row[csf('item_description')]; ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($row[csf('quantity')],2); //echo number_format($row[csf('quantity')]/$conv_fact,2); ?></p></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_qty,2); ?></td>
                    </tr>
                </tfoot>
            </table>

            <table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="30">Sl</th>
                    <th width="80">Prod. ID</th>
                    <th width="100">Return. ID</th>
                    <th width="100">Chalan No</th>
                    <th width="100">Return Date</th>
                    <th width="80">Item Description.</th>
                    <th width="160">Return Qty.</th>
				</thead>
                <tbody>
                <?
					$issue_rtn_qty_data=sql_select("select a.recv_number, a.receive_date , d.po_breakdown_id, c.item_group_id, d.quantity as quantity, b.prod_id, c.item_description
					from inv_receive_master a, inv_transaction b, product_details_master c, order_wise_pro_details d
					where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and b.transaction_type=4 and d.trans_type=4 and a.entry_form=73 and d.entry_form=73 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and d.po_breakdown_id in($po_id) and c.item_group_id='$item_name' $other_sql_cond");

					foreach($issue_rtn_qty_data as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="80"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="100" align="center"><p><? echo  change_date_format($row[csf('receive_date')]); ?></p></td>
                            <td width="80" align="center"><p><? echo $row[csf('item_description')]; ?></p></td>
                            <td width="160" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                        </tr>
						<?
						$tot_rtn_qty+=$row[csf('quantity')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_rtn_qty,2); ?></td>
                    </tr>
                    <tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Balance</td>
                        <td><? $balance_qnty=($tot_qty-$tot_rtn_qty); echo number_format($balance_qnty,2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}

if($action=="booking_info")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

 <script>
	function generate_trim_report(action,txt_booking_no,cbo_company_name,id_approved_id,cbo_isshort)
	{
			var show_comment='';
			var r=confirm("Press  \"Cancel\"  to hide  comments\nPress  \"OK\"  to Show comments");
			if (r==true) show_comment="1"; else show_comment="0";
			var report_title="";
			var fabric_nature = <? echo $fabric_nature ?>;
			if(cbo_isshort==1)
			{
				report_title="Short Trims Booking [Multiple Order]";
			}
			else
			{
				report_title="Multi Job Wise Trim Booking";
			}
			//var report_title='';
			var data="action="+action+'&report_title='+"'"+report_title+'&txt_booking_no='+"'"+txt_booking_no+"'"+'&cbo_company_name='+cbo_company_name+'&id_approved_id='+id_approved_id+'&report_type=1&link=1';
			//freeze_window(5);
			if(fabric_nature == 3)
			{
				if(cbo_isshort==1)
				{
					http.open("POST","../../woven_gmts/requires/short_trims_booking_multi_job_controllerurmi.php",true);
				}
				else
				{
					http.open("POST","../../woven_gmts/requires/trims_booking_multi_job_controllerurmi.php",true);
				}
			}
			else
			{
				if(cbo_isshort==1)
				{
					http.open("POST","../../woven_order/requires/short_trims_booking_multi_job_controllerurmi.php",true);
				}
				else
				{
					http.open("POST","../../woven_order/requires/trims_booking_multi_job_controllerurmi.php",true);
				}
			}
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_trim_report_reponse;

	}


	function generate_trim_report_reponse()
	{
		if(http.readyState == 4)
		{
			$('#data_panel').html( http.responseText );
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
			d.close();
		}
	}


 </script>
	<!--<div style="width:880px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
	<fieldset style="width:870px; margin-left:3px">
		<div id="scroll_body" align="center">
        <table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
        <tr>
        <td align="center" colspan="9"><strong>WO Summary</strong> </td>
         </tr>
        </table>
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="20">Sl</th>
                    <th width="100">Wo No</th>
                    <th width="60">Wo Type</th>
                    <th width="60">Wo Date</th>
                    <th width="100">Country</th>
                    <th width="200">Item Description</th>
                    <th width="80">Wo Qty</th>
                    <th width="60">UOM</th>
                    <th>Supplier</th>
				</thead>
                <tbody>
                <?
				$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
				$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );

				$conversion_factor_array=array();

				$conversion_factor=sql_select("select id ,conversion_factor from  lib_item_group ");
				foreach($conversion_factor as $row_f)
				{
					$conversion_factor_array[$row_f[csf('id')]]['con_factor']=$row_f[csf('conversion_factor')];
				}

				$i=1;
				$country_arr_data=array();
				$sql_data=sql_select("select c.country_id,c.po_break_down_id,c.job_no_mst from wo_po_color_size_breakdown c  where c.po_break_down_id in($po_id) and c.status_active=1 and c.is_deleted=0 group by c.country_id,c.po_break_down_id,c.job_no_mst  ");
				foreach($sql_data as $row_c)
				{
					$country_arr_data[$row_c[csf('po_break_down_id')]][$row_c[csf('job_no_mst')]]['country']=$row_c[csf('country_id')];
				}

				$item_description_arr=array();
				$wo_sql_trim=sql_select("select b.id,b.item_color,b.job_no, b.po_break_down_id, b.description,b.brand_supplier,b.item_size from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id=b.wo_trim_booking_dtls_id and a.pre_cost_fabric_cost_dtls_id=$trim_dtla_id and a.is_deleted=0 and a.status_active=1 and a.job_no=b.job_no  group by b.id,b.po_break_down_id,b.job_no,b.description,b.brand_supplier,b.item_size,b.item_color");
				foreach($wo_sql_trim as $row_trim)
				{
					$item_description_arr[$row_trim[csf('po_break_down_id')]][$row_trim[csf('job_no')]][$trim_dtla_id]['description']=$row_trim[csf('description')];
				}

				$boking_cond="";
				$booking_no= explode(',',$book_num);
				foreach($booking_no as $book_row)
				{
					if($boking_cond=="") $boking_cond="and a.booking_no in('$book_row'"; else  $boking_cond .=",'$book_row'";

				}
				if($boking_cond!="")$boking_cond.=")";
				$wo_sql="select a.is_short, a.is_approved as is_approved, a.booking_no, a.booking_date, a.pay_mode, a.supplier_id, b.job_no, b.country_id_string, b.po_break_down_id, sum(b.wo_qnty) as wo_qnty, b.uom from wo_booking_mst a, wo_booking_dtls b
				where a.item_category=4 and a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1
				and b.status_active=1 and b.is_deleted=0 and  b.job_no='$job_no' and b.trim_group=$item_name and b.po_break_down_id in($po_id) and b.pre_cost_fabric_cost_dtls_id=$trim_dtla_id $boking_cond group by a.is_short, a.is_approved, b.po_break_down_id, b.job_no, a.booking_no, a.booking_date, a.pay_mode, a.supplier_id, b.uom, b.country_id_string";
				$dtlsArray=sql_select($wo_sql);

				$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$cbo_company_name."' and module_id=2 and report_id in(5,6) and is_deleted=0 and status_active=1");

				$report= max(explode(',',$print_report_format));

				if($report==13){$reporAction="show_trim_booking_report";}
				elseif($report==14){$reporAction="show_trim_booking_report1";}
				elseif($report==15){$reporAction="show_trim_booking_report2";}
				elseif($report==16){$reporAction="show_trim_booking_report3";}

				foreach($dtlsArray as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$description=$item_description_arr[$row[csf('po_break_down_id')]][$row[csf('job_no')]][$trim_dtla_id]['description'];
					$conversion_factor_rate=$conversion_factor_array[$item_name]['con_factor'];
					$country_arr_data=explode(',',$row[csf('country_id_string')]);
					$country_name_data="";
					foreach($country_arr_data as $country_row)
					{
						if($country_name_data=="") $country_name_data=$country_name_library[$country_row]; else $country_name_data.=",".$country_name_library[$country_row];
					}
					$wo_type=''; $action_name="";
					if($fabric_nature == 3)
					{
						if($row[csf('is_short')]==1)
						{
							$wo_type="Short";
							$action_name="show_trim_booking_report";
						}
						else
						{
							$wo_type="Main";
							$action_name="show_trim_booking_report";
						}
					}
					else
					{
						if($row[csf('is_short')]==1)
						{
							$wo_type="Short";
							$action_name="show_trim_booking_report2";
						}
						else
						{
							$wo_type="Main";
							$action_name="show_trim_booking_report2";
						}
					}
					$supplier_name_str="";
					if($row[csf('pay_mode')]==3 || $row[csf('pay_mode')]==5) $supplier_name_str=$company_arr[$row[csf('supplier_id')]]; else $supplier_name_str=$supplier_arr[$row[csf('supplier_id')]];
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="20"><p><? echo $i; ?></p></td>
						<td width="100"><p><a href="#" onClick="generate_trim_report('<? echo $action_name; ?>','<? echo $row[csf('booking_no')]; ?>',<? echo $cbo_company_name; ?>,<? echo $row[csf('is_approved')]; ?>,<? echo $row[csf('is_short')]; ?>)"><? echo $row[csf('booking_no')]; ?></a></p></td>
						<td width="60"><p><? echo $wo_type; ?></p></td>
						<td width="60"><p><? echo change_date_format($row[csf('booking_date')]); ?></p></td>
						<td width="100"><p><? echo $country_name_data; ?></p></td>
						<td width="200"><p><?  echo $description; ?></p></td>
						<td width="80" align="right" title="<? echo 'conversion_factor='.$conversion_factor_rate; ?>"><p><? echo number_format($row[csf('wo_qnty')],2); ?></p></td>
						<td width="60" align="center" ><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
						<td><p><? echo $supplier_name_str; ?></p></td>
					</tr>
					<?
					$tot_qty+=$row[csf('wo_qnty')];
					$i++;
				}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                   		 <td colspan="6" align="right">Total</td>
                    	<td  align="right"><? echo number_format($tot_qty,2); ?></td>
                        <td align="right">&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div style="display:none" id="data_panel"></div>
    </fieldset>
    <script type="text/javascript" src="../../../js/jquery.js"></script>
    <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
    <?
	exit();
}
//disconnect($con);

if($action=="booking_inhouse_info")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<!--<div style="width:880px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
	<fieldset style="width:870px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="30">Sl</th>
                    <th width="60">Prod. ID</th>
                    <th width="100">Recv. ID</th>
                    <th width="100">Wo/Pi No</th>
                    <th width="100">Chalan No</th>
                    <th width="70">Recv. Date</th>
                    <th width="150">Item Description.</th>
                    <th width="80">Recv. Qty.</th>
                    <th>Reject Qty.</th>
				</thead>
                <tbody>
                <?
					$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
					$bookingNoArr=return_library_array( "select id, booking_no from  wo_booking_mst", "id", "booking_no");
					$piArr=return_library_array("select id, pi_number from com_pi_master_details", "id", "pi_number");
					$i=1;

					$item_arr=array();
					$conversion_factor=sql_select("select id,conversion_factor,order_uom from lib_item_group where status_active=1  ");
					foreach($conversion_factor as $row_f)
					{
						$item_arr[$row_f[csf('id')]]['order_uom']=$row_f[csf('conversion_factor')];
					}
					unset($conversion_factor);

					$receive_rtn_data=array();
					//echo "select a.issue_number, a.issue_date, e.id, d.po_breakdown_id, c.item_group_id, sum(d.quantity) as quantity from  inv_issue_master a,inv_transaction b, product_details_master c, order_wise_pro_details d,inv_receive_master e  where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and e.id=a.received_id   and b.transaction_type=3 and a.entry_form=49 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and d.po_breakdown_id in($po_id) and c.item_group_id='$item_name' group by a.issue_number,a.issue_date,e.id,d.po_breakdown_id, c.item_group_id order by c.item_group_id";die;



					$receive_qty_data="select a.id, c.po_breakdown_id, a.receive_basis, b.booking_id, b.item_group_id, b.prod_id as prod_id, a.challan_no, b.item_description, a.recv_number, a.receive_date, SUM(c.quantity) as quantity, sum(reject_receive_qnty) as reject_receive_qnty
					from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c , product_details_master d
					where a.id=b.mst_id  and a.entry_form=24 and  a.item_category=4  and b.id=c.dtls_id and b.prod_id=d.id and b.trans_id=c.trans_id and c.trans_type=1 and  c.po_breakdown_id in($po_id)  and b.item_group_id='$item_name' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by  c.po_breakdown_id, b.item_group_id, a.receive_basis, b.booking_id, b.prod_id, a.id, b.item_description, a.recv_number, a.challan_no, a.receive_date";

					$dtlsArray=sql_select($receive_qty_data);

					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$qty=0;
						$qty=$row[csf('quantity')];

						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="60"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="100" align="center"><p><? $piwo_no='';
							if($row[csf('receive_basis')]==1)
							{
								$piwo_no=$piArr[$row[csf('booking_id')]];
							}
							else if($row[csf('receive_basis')]==2)
							{
								$piwo_no=$bookingNoArr[$row[csf('booking_id')]];
							}
							echo $piwo_no; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="70" align="center"><p><? echo  change_date_format($row[csf('receive_date')]); ?></p></td>
                            <td width="150" align="center"><p><? echo $row[csf('item_description')]; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($qty,2); ?></p></td>
                            <td align="right"><p><? echo number_format($row[csf('reject_receive_qnty')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty+=$qty;
						$tot_rej_qty+=$row[csf('reject_receive_qnty')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="6" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_qty,2); ?></td>
                         <td><? echo number_format($tot_rej_qty,2); ?></td>
                    </tr>
                </tfoot>
            </table>
            <?
			$transfer_qty_data=sql_select("select a.transfer_system_id, a.transfer_date, d.po_breakdown_id, c.item_group_id, d.trans_type, d.quantity as quantity, b.prod_id, c.item_description
					from  inv_item_transfer_mst a, inv_transaction b, product_details_master c, order_wise_pro_details d
					where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and a.transfer_criteria=4 and a.item_category=4 and b.item_category=4 and b.transaction_type in(5,6) and d.trans_type in(5,6) and d.entry_form=78 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.po_breakdown_id in($po_id) and c.item_group_id='$item_name'");
			?>
            <table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="30">Sl</th>
                    <th width="80">Prod. ID</th>
                    <th width="100">Transfer. ID</th>
                    <th width="100">Transfer Type</th>
                    <th width="100">Transfer Date</th>
                    <th width="80">Item Description.</th>
                    <th width="160">Return Qty.</th>
				</thead>
                <tbody>
                <?
					/*echo "select a.transfer_system_id, a.transfer_date, d.po_breakdown_id, c.item_group_id, sum((case when b.transaction_type=5 and d.trans_type=5 then d.quantity else 0 end)-(case when b.transaction_type=6 and d.trans_type=6 then d.quantity else 0 end)) as quantity, b.prod_id, c.item_description
					from  inv_item_transfer_mst a, inv_transaction b, product_details_master c, order_wise_pro_details d
					where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and a.transfer_criteria=4 and a.item_category=4 and b.item_category=4 and b.transaction_type in(5,6) and d.trans_type in(5,6) and d.entry_form=78 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.po_breakdown_id in($po_id) and c.item_group_id='$item_name'
					group by a.transfer_system_id, a.transfer_date, d.po_breakdown_id, c.item_group_id, b.prod_id, c.item_description";die;*/


					foreach($transfer_qty_data as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";

						$qty=0;
						$qty=$row[csf('quantity')];

						if($row[csf('trans_type')]==5)
						{
							$trans_type="Transfer In";
							$trans_in_qnty+=$qty;

						}
						else
						{
							$trans_type="Transfer Out";
							$trans_out_qnty+=$qty;
						}

						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="80"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $trans_type; ?></p></td>
                            <td width="100" align="center"><p><? echo  change_date_format($row[csf('transfer_date')]); ?></p></td>
                            <td width="80" align="center"><p><? echo $row[csf('item_description')]; ?></p></td>
                            <td width="160" align="right"><p><? echo number_format($qty,2); ?></p></td>
                        </tr>
						<?
						$tot_trans_qty+=$qty;
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_trans_qty,2); ?></td>
                    </tr>
            </table>

            <table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="30">Sl</th>
                    <th width="80">Prod. ID</th>
                    <th width="100">Return. ID</th>
                    <th width="100">Chalan No</th>
                    <th width="100">Return Date</th>
                    <th width="80">Item Description.</th>
                    <th width="160">Return Qty.</th>
				</thead>
                <tbody>
                <?
					$receive_rtn_qty_data=sql_select("select a.issue_number, a.issue_date , d.po_breakdown_id, c.item_group_id, d.quantity as quantity, b.prod_id, c.item_description
					from inv_issue_master a, inv_transaction b, product_details_master c, order_wise_pro_details d
					where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and b.transaction_type=3 and d.trans_type=3 and a.entry_form=49 and d.entry_form=49 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and d.po_breakdown_id in($po_id) and c.item_group_id='$item_name'");


					foreach($receive_rtn_qty_data as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";

						$qty=0;
						$qty=$row[csf('quantity')];
						//$qty=$row[csf('quantity')]/$item_arr[$row[csf('item_group_id')]]['order_uom'];
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="80"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="100" align="center"><p><? echo  change_date_format($row[csf('issue_date')]); ?></p></td>
                            <td width="80" align="center"><p><? echo $row[csf('item_description')]; ?></p></td>
                            <td width="160" align="right"><p><? echo number_format($qty,2); ?></p></td>
                        </tr>
						<?
						$tot_rtn_qty+=$qty;
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_rtn_qty,2); ?></td>
                    </tr>
                    <tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Balance</td>
                        <td><? $balance_qnty=($tot_qty+$trans_in_qnty)-($tot_rtn_qty+$trans_out_qnty); echo number_format($balance_qnty,2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}

if($action=="booking_issue_info")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
<!--	<div style="width:880px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
-->	<fieldset style="width:870px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="30">Sl</th>
                    <th width="80">Prod. ID</th>
                    <th width="100">Issue. ID</th>
                     <th width="100">Chalan No</th>
                     <th width="100">Issue. Date</th>
                    <th width="80">Item Description.</th>
                    <th width="100">Issue. Qty.</th>
				</thead>
                <tbody>
                <?
					$conversion_factor_array=array();	$item_arr=array();
					$conversion_factor=sql_select("select id ,trim_uom,order_uom,conversion_factor from  lib_item_group  ");
					foreach($conversion_factor as $row_f)
					{
					 $conversion_factor_array[$row_f[csf('id')]]['con_factor']=$row_f[csf('conversion_factor')];
					 $conversion_factor_array[$row_f[csf('id')]]['cons_uom']=$row_f[csf('trim_uom')];
					 $item_arr[$row_f[csf('id')]]['order_uom']=$row_f[csf('order_uom')];
					}
					$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
					$i=1;
					//$wo_sql="select a.item_group_id,a.prod_id,b.recv_number,b.receive_date,a.item_description,sum(a.cons_qnty) as cons_qnty  from inv_receive_master b, inv_trims_entry_dtls a where b.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category=4 group by a.item_group_id,a.prod_id,b.recv_number,b.receive_date,a.item_description";

				 $mrr_sql=("select a.id, a.issue_number,a.challan_no,p.item_group_id,b.prod_id, a.issue_date,b.item_description,SUM(c.quantity) as quantity
					from  inv_issue_master a,inv_trims_issue_dtls b, order_wise_pro_details c,product_details_master p
					where a.id=b.mst_id  and a.entry_form=25 and p.id=b.prod_id and b.id=c.dtls_id and b.trans_id=c.trans_id and c.trans_type=2 and a.is_deleted=0 and a.status_active=1 and
					b.status_active=1 and b.is_deleted=0 and  c.po_breakdown_id in($po_id) and p.item_group_id='$item_name' group by c.po_breakdown_id,p.item_group_id,b.item_description,a.issue_number,a.id,a.issue_date,b.prod_id,a.challan_no ");

					$dtlsArray=sql_select($mrr_sql);

					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$conv_fact=$conversion_factor_array[$row[csf('item_group_id')]]['con_factor'];
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="80" align="center"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td width="100"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="100" align="center"><p><? echo  change_date_format($row[csf('issue_date')]); ?></p></td>
                            <td width="80" align="center"><p><? echo $row[csf('item_description')]; ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($row[csf('quantity')],2); //echo number_format($row[csf('quantity')]/$conv_fact,2); ?></p></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_qty,2); ?></td>
                    </tr>
                </tfoot>
            </table>

            <table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="30">Sl</th>
                    <th width="80">Prod. ID</th>
                    <th width="100">Return. ID</th>
                    <th width="100">Chalan No</th>
                    <th width="100">Return Date</th>
                    <th width="80">Item Description.</th>
                    <th width="160">Return Qty.</th>
				</thead>
                <tbody>
                <?
					$issue_rtn_qty_data=sql_select("select a.recv_number, a.receive_date , d.po_breakdown_id, c.item_group_id, d.quantity as quantity, b.prod_id, c.item_description
					from inv_receive_master a, inv_transaction b, product_details_master c, order_wise_pro_details d
					where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and b.transaction_type=4 and d.trans_type=4 and a.entry_form=73 and d.entry_form=73 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and d.po_breakdown_id in($po_id) and c.item_group_id='$item_name'");

					foreach($issue_rtn_qty_data as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="80"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="100" align="center"><p><? echo  change_date_format($row[csf('receive_date')]); ?></p></td>
                            <td width="80" align="center"><p><? echo $row[csf('item_description')]; ?></p></td>
                            <td width="160" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                        </tr>
						<?
						$tot_rtn_qty+=$row[csf('quantity')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_rtn_qty,2); ?></td>
                    </tr>
                    <tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Balance</td>
                        <td><? $balance_qnty=($tot_qty-$tot_rtn_qty); echo number_format($balance_qnty,2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}

if($action=="order_qty_data")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$country_name_library=return_library_array( "select id, country_name from lib_country", "id", "country_name"  );
	//echo $po_id; die;
	?>
<!--	<div style="width:780px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
-->	<fieldset style="width:770px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="750" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="30">Sl</th>
                    <th width="80">Buyer Name</th>
                    <th width="100">Order No</th>
                   <th width="100">Country</th>
                    <th width="80">Order Qty. (PCS)</th>

				</thead>
                <tbody>
                <?
					$date_cond='';
					if(str_replace("'","",$from_date)!="" && str_replace("'","",$to_date)!="")
					{
						$start_date=(str_replace("'","",$from_date));
						$end_date=(str_replace("'","",$to_date));
						$date_cond="and c.country_ship_date between '$start_date' and '$end_date'";
					}
					$i=1;
					$order_arr=return_library_array( "select id, po_number from wo_po_break_down where id in($po_id)", "id", "po_number"  );

				 	$gmt_item_id=return_field_value("item_number_id", "wo_po_color_size_breakdown", "po_break_down_id='$po_id'");
					$country_id=return_field_value("country_id", "wo_po_color_size_breakdown", "po_break_down_id='$po_id'");
					 //echo $gmt_item_id;
					$sql_po_qty=sql_select("select sum(c.order_quantity) as order_quantity,c.country_id  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id in($po_id) and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,c.country_id ");
					list($sql_po_qty_row)=$sql_po_qty;
					$po_qty=$sql_po_qty_row[csf('order_quantity')];
					//echo "select sum(c.order_quantity) as order_quantity,c.country_id  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id in($po_id) and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,c.country_id ";

					$sql=" select sum( c.order_quantity) as po_quantity, c.country_id, c.po_break_down_id from wo_po_color_size_breakdown c where c.po_break_down_id in($po_id) and c.status_active=1 and c.is_deleted=0 $date_cond group by c.country_id,c.po_break_down_id";
					//echo $sql;
					$dtlsArray=sql_select($sql);

					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="80" align="center"><p><? echo $buyer_short_name_library[$buyer]; ?></p></td>
                            <td width="100"><p><? echo $order_arr[$row[csf('po_break_down_id')]]; ?></p></td>
                             <td width="100" align="center"><p><? echo $country_name_library[$row[csf('country_id')]]; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row[csf('po_quantity')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('po_quantity')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="3" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_qty,2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}
//disconnect($con);

if($action=="order_req_qty_data")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$buyer_short_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name");
	$po_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");

	?>
	<!--<div style="width:680px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px" class="formbutton"/></div>-->
	<fieldset style="width:670px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="30">Sl</th>
                    <th width="80">Buyer Name</th>
                    <th width="100">Order No</th>
                    <th width="100">Item Description</th>
                    <th width="100">Country</th>
                    <th width="80">Req. Qty.</th>
					<th width="60">Uom</th>
                    <th>Req. Rate</th>
				</thead>
                <tbody>
                <?
				//echo $po_id;
				$condition= new condition();
				$condition->job_no("='$job_no'");

				$condition->po_id("in($po_id)");

				if(str_replace("'","",$start_date)!="" && str_replace("'","",$end_date)!="")
				{
					$condition->country_ship_date(" between '$start_date' and '$end_date'");
				}

				$condition->init();
				$trim= new trims($condition);
				$trim_qty=$trim->getQtyArray_by_orderCountryAndPrecostdtlsid();


				//print_r($trim_qty);
				//$trim= new trims($condition);
				//$trim_amount=$trim->getAmountArray_by_orderAndPrecostdtlsid();

				//$trim_qty=$trim->getQtyArray_by_jobAndPrecostdtlsid();
			//print_r($trim_qty);
				//$trim= new trims($condition);
			//$trim_amount=$trim->getAmountArray_by_orderAndPrecostdtlsid();
				//$trim_amount=$trim->getAmountArray_by_jobAndPrecostdtlsid();

				$country_id_str="";
				if($start_date=="" && $end_date=="") $date_cond=""; else $date_cond="and country_ship_date between '$start_date' and '$end_date'";
				$sql_color_size="select id, country_id from wo_po_color_size_breakdown where po_break_down_id in ($po_id) and job_no_mst='$job_no' and status_active=1 and is_deleted=0 $date_cond";
				$sql_color_size_res=sql_select($sql_color_size);
				foreach($sql_color_size_res as $row)
				{
					if($country_id_str=="") $country_id_str=$row[csf('id')]; else $country_id_str.=','.$row[csf('id')];
				}
				$excountry_id=array_filter(array_unique(explode(",",$country_id_str)));
				if($excountry_id!="") $country_idcond= "and c.color_size_table_id in ($excountry_id)"; else $country_idcond= "";

				$sql="select  b.id as trim_dtla_id, b.description,b.cons_uom, b.rate, b.amount,  c.cons, c.country_id, c.po_break_down_id, b.job_no
					from wo_pre_cost_mst a, wo_pre_cost_trim_cost_dtls b, wo_pre_cost_trim_co_cons_dtls c
					where c.job_no=b.job_no and b.id=c.wo_pre_cost_trim_cost_dtls_id and a.job_no=b.job_no and a.job_no='$job_no' and c.po_break_down_id in ($po_id) and b.id=$trim_dtla_id and c.cons>0
					group by  b.id, b.description, b.rate, b.amount,b.cons_uom,  c.cons, c.country_id, c.po_break_down_id, b.job_no order by b.trim_group";

				$dtlsArray=sql_select($sql);
				$pre_cost_data_arr=array();
				foreach($dtlsArray as $row)
				{
					$excountry_id=array_unique(explode(",",$row[csf('country_id')])); $req_qty=0;
					foreach($excountry_id as $country_id)
					{

						//$req_qty=$trim_qty[$row[csf('po_break_down_id')]][$country_id][$row[csf('trim_dtla_id')]];
						$pre_cost_data_arr[$row[csf('po_break_down_id')]][$country_id][$row[csf('trim_dtla_id')]]=$req_qty;
						$pre_cost_uom_arr[$row[csf('po_break_down_id')]][$country_id][$row[csf('trim_dtla_id')]]=$row[csf('cons_uom')];
					}
				}
				unset($dtlsArray);
				$i=1;
				foreach($pre_cost_data_arr as $po_id=>$po_data)
				{
					foreach($po_data as $country_id=>$country_data)
					{
						foreach($country_data as $description=>$req_qty)
						{
							//if(in_array($country_id,$excountry_id))
							//{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								//echo $po_id.'='.$country_id.'='.$description.', ';
								$trim_req_qty=$trim_qty[$po_id][$country_id][$description];
								$uom_id=$pre_cost_uom_arr[$po_id][$country_id][$description];

								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
									<td width="30"><p><? echo $i; ?></p></td>
									<td width="80" align="center"><p><? echo $buyer_short_arr[$buyer]; ?></p></td>
									<td width="100"><p><? echo $po_arr[$po_id]; ?></p></td>
									<td width="100"><p><? //echo $description;//$description; ?></p></td>
									<td width="100" align="center"><p><? echo $country_arr[$country_id]; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($trim_req_qty,2); ?></p></td>
									<td width="60" align="right"><p><? echo $unit_of_measurement[$uom_id]; ?></p></td>
									<td align="right"><p><? echo number_format($rate,4); ?></p></td>
								</tr>
								<?
								$tot_qty+=$trim_req_qty;
								$i++;
							//}
						}
					}
				}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td align="right">&nbsp;</td>
                    	<td align="right" colspan="4">Total</td>
                        <td align="right"><? echo number_format($tot_qty,2); ?></td>
                        <td>&nbsp;</td>
						<td>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}
disconnect($con);
?>